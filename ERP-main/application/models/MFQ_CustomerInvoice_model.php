<?php

class MFQ_CustomerInvoice_model extends ERP_Model
{
    function fetch_delivery_note()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $this->db->select('*,IFNULL(qty,0) as qty,IFNULL(unitPrice,0) as unitPrice');
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynote.jobID", "left");
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID", "left");
        $this->db->where('srp_erp_mfq_deliverynote.mfqCustomerAutoID', $this->input->post("mfqCustomerAutoID"));
        $this->db->where('srp_erp_mfq_deliverynote.mfqSegmentID', $this->input->post("mfqsegmentID"));
        $this->db->where('srp_erp_mfq_deliverynote.confirmedYN', 1);
        $this->db->where('NOT EXISTS (SELECT *
                   FROM srp_erp_mfq_customerinvoicemaster
                   WHERE srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID != ' . $invoiceAutoID . ')');
        $master = $this->db->get('srp_erp_mfq_deliverynote')->result_array();
        return $master;

    }

    function save_customer_invoice()
    {
        $last_id = "";
        $this->db->trans_start();
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $invoiceDate = input_format_date(trim($this->input->post('invoiceDate') ?? ''), $date_format_policy);
        $invoiceDueDate = input_format_date(trim($this->input->post('invoiceDueDate') ?? ''), $date_format_policy);
        $dueDate = input_format_date(trim($this->input->post('dueDate') ?? ''), $date_format_policy);
        $deliveryNoteID = $this->input->post('deliveryNoteID');

        if ($deliveryNoteID) {
            $delivernoteJobReference = $this->db->query("SELECT jobreferenceNo FROM srp_erp_mfq_deliverynote WHERE companyID = $companyID AND deliverNoteID = $deliveryNoteID ")->row('jobreferenceNo');
        }

        $invAutoID = $this->input->post('invoiceAutoID');
        $delivernoteID_previous = $this->db->query("SELECT deliveryNoteID FROM `srp_erp_mfq_customerinvoicemaster` where invoiceAutoID = {$invAutoID} ")->row('deliveryNoteID');
        $dnAutoID = $this->input->post('deliveryNoteID');

        if (!empty($delivernoteID_previous) && ($delivernoteID_previous != $dnAutoID)) {

            $this->db->delete('srp_erp_mfq_customerinvoicedetails', 'invoiceAutoID=' . $invAutoID . ' AND type=2');
        }
        $this->db->set('isGroupBasedTax', ((getPolicyValues('GBT', 'All')==1)?1:0));
        $this->db->set('mfqSegmentID', $this->input->post('mfqsegmentID'));
        if (!$this->input->post('invoiceAutoID')) {
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_customerinvoicemaster', 'invoiceAutoID', 'companyID');
            $codes = $this->sequence->sequence_generator('MCINV', $serialInfo['serialNo']);
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('jobreferenceNo', $delivernoteJobReference);
            $this->db->set('invoiceCode', $codes);
            $this->db->set('invoiceDate', $invoiceDate);
            $this->db->set('invoiceDueDate', $invoiceDueDate);
            $this->db->set('invoiceNarration', $this->input->post('invoiceNarration'));
            $this->db->set('deliveryNoteID', $this->input->post('deliveryNoteID'));

            $this->db->select("srp_erp_customermaster.customerCurrencyID,srp_erp_customermaster.customerCurrency");
            $this->db->from("srp_erp_mfq_customermaster");
            $this->db->join("srp_erp_customermaster", "srp_erp_mfq_customermaster.CustomerAutoID=srp_erp_customermaster.customerAutoID", "LEFT");
            $this->db->where("mfqCustomerAutoID", $this->input->post('mfqCustomerAutoID'));
            $custInfo = $this->db->get()->row_array();

            $this->db->set('customerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('customerCurrency', $custInfo["customerCurrency"]);

            $customer_currency = currency_conversionID($this->input->post('currencyID'), $custInfo['customerCurrencyID']);
            $this->db->set('customerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('customerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);

            $this->db->set('transactionCurrencyID', $this->input->post('currencyID'));
            $this->db->set('transactionCurrency', null);
            $this->db->set('transactionExchangeRate', 1);
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->input->post('currencyID')));

            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
            $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
            $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
            $reporting_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_reporting_currencyID']);
            $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
            $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_customerinvoicemaster');
            $last_id = $this->db->insert_id();

        } else {
            $last_id = $this->input->post('invoiceAutoID');
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('invoiceDate', $invoiceDate);
            $this->db->set('invoiceDueDate', $invoiceDueDate);
            $this->db->set('invoiceNarration', $this->input->post('invoiceNarration'));
            //$this->db->set('currencyID', $this->input->post('currencyID'));
            $this->db->set('deliveryNoteID', $this->input->post('deliveryNoteID'));

            $this->db->select("srp_erp_customermaster.customerCurrencyID,srp_erp_customermaster.customerCurrency");
            $this->db->from("srp_erp_mfq_customermaster");
            $this->db->join("srp_erp_customermaster", "srp_erp_mfq_customermaster.CustomerAutoID=srp_erp_customermaster.customerAutoID", "LEFT");
            $this->db->where("mfqCustomerAutoID", $this->input->post('mfqCustomerAutoID'));
            $custInfo = $this->db->get()->row_array();

            $this->db->set('customerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('customerCurrency', $custInfo["customerCurrency"]);

            $customer_currency = currency_conversionID($this->input->post('currencyID'), $custInfo['customerCurrencyID']);
            $this->db->set('customerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('customerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);

            $this->db->set('transactionCurrencyID', $this->input->post('currencyID'));
            $this->db->set('transactionCurrency', null);
            $this->db->set('transactionExchangeRate', 1);
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->input->post('currencyID')));

            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
            $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
            $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
            $reporting_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_reporting_currencyID']);
            $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
            $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('modifiedUserID', current_userID());
            $this->db->set('modifiedUserName', current_user());
            $this->db->set('modifiedDateTime', current_date(true));

            $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
            $result = $this->db->update('srp_erp_mfq_customerinvoicemaster');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Customer invoice failed ' . $this->db->_error_message());

        } else {
            if ($this->input->post('invoiceAutoID')) {
                $this->db->where('invoiceAutoID', $this->input->post('invoiceAutoID'));
                $this->db->delete('srp_erp_mfq_customerinvoicedetails');

                $this->db->where('documentID', 'MCINV');
                $this->db->where('documentMasterAutoID', $this->input->post('invoiceAutoID'));
                $this->db->delete('srp_erp_taxledger');
            }

            $invoiceDetailID = $this->input->post('invoiceDetailsAutoID');
            $gl_code = $this->input->post('revenueGLAutoID');
            if (!empty($gl_code)) {
                foreach ($gl_code as $key => $val) {
                    /*if (!empty($invoiceDetailID[$key])) {
                        if (!empty($gl_code[$key])) {
                            $this->db->set('invoiceAutoID', $last_id);
                            $this->db->set('revenueGLAutoID', $this->input->post('revenueGLAutoID')[$key]);
                            $this->db->set('segmentID', $this->input->post('segmentID')[$key]);
                            $this->db->set('requestedQty', $this->input->post('requestedQty')[$key]);
                            $this->db->set('unitRate', $this->input->post('amount')[$key]);
                            $this->db->set('type', 1);
                            $amount = $this->input->post('requestedQty')[$key] * $this->input->post('amount')[$key];
                            $transactionAmount = $amount;
                            $this->db->set('transactionAmount', round($transactionAmount, fetch_currency_desimal_by_id($this->input->post('currencyID'))));
                            $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
                            $companyLocalAmount = $transactionAmount / $default_currency['conversion'];
                            $this->db->set('companyLocalAmount', round($companyLocalAmount, $default_currency['DecimalPlaces']));
                            $reporting_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_reporting_currencyID']);
                            $companyReportingAmount = $transactionAmount / $reporting_currency['conversion'];
                            $this->db->set('companyReportingAmount', round($companyReportingAmount, $reporting_currency['DecimalPlaces']));

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('invoiceDetailsAutoID', $invoiceDetailID[$key]);
                            $this->db->update('srp_erp_mfq_customerinvoicedetails');
                            $last_detail_id = $invoiceDetailID[$key];
                        }
                    } else {*/
                        if (!empty($gl_code[$key])) {
                            $this->db->set('revenueGLAutoID', $this->input->post('revenueGLAutoID')[$key]);
                            $this->db->set('segmentID', $this->input->post('segmentID')[$key]);
                            $this->db->set('requestedQty', $this->input->post('requestedQty')[$key]);
                            $this->db->set('unitRate', $this->input->post('amount')[$key]);
                            $this->db->set('companyID', current_companyID());
                            $this->db->set('invoiceAutoID', $last_id);
                            $this->db->set('type', 1);

                            $amount = $this->input->post('requestedQty')[$key] * $this->input->post('amount')[$key];
                            $transactionAmount = $amount;
                            $this->db->set('transactionAmount', round($transactionAmount, fetch_currency_desimal_by_id($this->input->post('currencyID'))));

                            if ($this->input->post('gl_text')[$key]) {
                                $return = fetch_line_wise_itemTaxcalculation($this->input->post('gl_text')[$key], $transactionAmount, 0, 'MCINV', $last_id);
                                $transactionAmount = $transactionAmount + $return['amount'];
                            }

                            $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
                            $companyLocalAmount = $transactionAmount / $default_currency['conversion'];
                            $this->db->set('companyLocalAmount', round($companyLocalAmount, $default_currency['DecimalPlaces']));
                            $reporting_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_reporting_currencyID']);
                            $companyReportingAmount = $transactionAmount / $reporting_currency['conversion'];
                            $this->db->set('companyReportingAmount', round($companyReportingAmount, $reporting_currency['DecimalPlaces']));

                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('createdUserID', current_userID());
                            $this->db->set('createdUserName', current_user());
                            $this->db->set('createdDateTime', current_date(true));
                            $this->db->insert('srp_erp_mfq_customerinvoicedetails');
                            $last_detail_id = $this->db->insert_id();
                        }
                    /*}*/
                    if ($this->input->post('gl_text')[$key]) {
                        tax_calculation_vat(null,null,$this->input->post('gl_text')[$key],'invoiceAutoID', trim($last_id), $amount,'MCINV', $last_detail_id, 0,1);
                    }
                }
            }

            $itemAutoID = $this->input->post('itemAutoID');
            $itemInvoiceDetailsAutoID = $this->input->post('itemInvoiceDetailsAutoID');
            $expectedQty = $this->input->post('expectedQty');
            $unitRate = $this->input->post('unitRate');
            $tax_type = $this->input->post('tax_type');

            if(!empty($itemAutoID)){
                foreach ($itemAutoID as $key => $itm) {
                    $this->db->select("srp_erp_itemmaster.*,srp_erp_mfq_itemmaster.unbilledServicesGLAutoID");
                    $this->db->from("srp_erp_mfq_itemmaster");
                    $this->db->join("srp_erp_itemmaster", "srp_erp_itemmaster.itemAutoID=srp_erp_mfq_itemmaster.itemAutoID", "LEFT");
                    $this->db->where("mfqItemID", $itemAutoID[$key]);
                    $itemDet = $this->db->get()->row_array();
                    $this->db->set('revenueGLAutoID', $itemDet['revanueGLAutoID']);
                    $this->db->set('revenueSystemGLCode', $itemDet['revanueSystemGLCode']);
                    $this->db->set('revenueGLCode', $itemDet['revanueGLCode']);
                    $this->db->set('revenueGLDescription', $itemDet['revanueDescription']);
                    $this->db->set('revenueGLType', $itemDet['revanueType']);
                    $this->db->set('deliveryNoteDetID', $this->input->post('deliveryNoteDetailID')[$key]);
                    $this->db->set('itemAutoID', $itemAutoID[$key]);
                    $this->db->set('requestedQty', $expectedQty[$key]);
                    $this->db->set('unitRate', $unitRate[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('invoiceAutoID', $last_id);
                    $this->db->set('type', 2);
                    $this->db->set('uomID', $itemDet['defaultUnitOfMeasureID']);
                    $this->db->set('expenseGLAutoID', $itemDet['costGLAutoID']);
                    if ($itemDet["mainCategory"] == "Service") {
                        $this->db->set('assetGLAutoID', $itemDet['unbilledServicesGLAutoID']);
                        $this->db->select('*,IFNULL(qty,0) as qty,IFNULL(unitPrice,0) as unitPrice');
                        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynote.jobID", "left");
                        $this->db->where('srp_erp_mfq_deliverynote.deliverNoteID', $this->input->post('deliveryNoteID'));
                        $master = $this->db->get('srp_erp_mfq_deliverynote')->row_array();

                        $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('unitCost', $master["unitPrice"]);
                        $this->db->set('totalCost', ($master["unitPrice"] * $master["qty"]));

                        $this->db->set('companyLocalWacAmount', $master["unitPrice"] * $default_currency['conversion']);
                    } else {
                        $this->db->set('assetGLAutoID', $itemDet['assteGLAutoID']);
                        $this->db->set('companyLocalWacAmount', $itemDet['companyLocalWacAmount']);
                        $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('unitCost', $itemDet['companyLocalWacAmount'] * $default_currency['conversion']);
                        $this->db->set('totalCost', ($itemDet['companyLocalWacAmount'] * $default_currency['conversion'] * $expectedQty[$key]));
                    }

                    $amount = $expectedQty[$key] * $unitRate[$key];
                    $transactionAmount = $amount;
                    $this->db->set('transactionAmount', round($transactionAmount, fetch_currency_desimal_by_id($this->input->post('currencyID'))));

                    if ($this->input->post('gl_text')[$key]) {
                        $return = fetch_line_wise_itemTaxcalculation($tax_type[$key], $transactionAmount, 0, 'MCINV', $last_id);
                        $transactionAmount = $transactionAmount + $return['amount'];
                    }

                    $default_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_default_currencyID']);
                    $companyLocalAmount = $transactionAmount / $default_currency['conversion'];
                    $this->db->set('companyLocalAmount', round($companyLocalAmount, $default_currency['DecimalPlaces']));
                    $reporting_currency = currency_conversionID($this->input->post('currencyID'), $this->common_data['company_data']['company_reporting_currencyID']);
                    $companyReportingAmount = $transactionAmount / $reporting_currency['conversion'];
                    $this->db->set('companyReportingAmount', round($companyReportingAmount, $reporting_currency['DecimalPlaces']));

                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->insert('srp_erp_mfq_customerinvoicedetails');
                    $last_detail_id = $this->db->insert_id();

                    if ($tax_type[$key]) {
                        tax_calculation_vat(null,null,$tax_type[$key],'invoiceAutoID', trim($last_id), $amount,'MCINV', $last_detail_id, 0,1);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Invoice Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Invoice Saved Successfully.', $last_id);
            }
        }
    }

    function load_mfq_customerInvoice()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_mfq_customerinvoicemaster.*,deliveryNoteCode,DATE_FORMAT(srp_erp_mfq_customerinvoicemaster.invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,DATE_FORMAT(srp_erp_mfq_customerinvoicemaster.invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate, srp_erp_customerinvoicemaster.invoiceCode as erpInvoiceCode, srp_erp_mfq_customerinvoicemaster.isGroupBasedTax');
        $this->db->where('srp_erp_mfq_customerinvoicemaster.invoiceAutoID', $this->input->post("invoiceAutoID"));
        $this->db->join('srp_erp_mfq_deliverynote', 'srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID');
        $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_mfq_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicemaster.mfqInvoiceAutoID', 'left');
        $master = $this->db->get('srp_erp_mfq_customerinvoicemaster')->row_array();

        $master['po_numberEST'] = $this->db->query("SELECT srp_erp_mfq_estimatemaster.poNumber, documentCode FROM srp_erp_mfq_estimatemaster JOIN srp_erp_mfq_job ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID WHERE deliveryNoteID = {$master['deliveryNoteID']}")->result_array();

        $linked_subJobs = $this->db->query("SELECT documentCode FROM srp_erp_mfq_job JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID WHERE deliveryNoteID = {$master['deliveryNoteID']}")->result_array();
        $master['linkedSubJobs'] = join(', ', array_column($linked_subJobs, 'documentCode'));

        return $master;

    }

    function load_mfq_customerinvoicedetail()
    {
        $this->db->select('srp_erp_mfq_customerinvoicedetails.*,CONCAT(GLSecondaryCode," | ",GLDescription," | ",subCategory ) as GLDescription,CONCAT(srp_erp_mfq_itemmaster.itemSystemCode," - ",srp_erp_mfq_itemmaster.itemDescription) as itemDescription,srp_erp_mfq_itemmaster.defaultUnitOfMeasure, IFNULL(isGroupBasedTax, 0) AS isGroupBasedTax, IFNULL(taxAmount, 0) as taxAmount');
        $this->db->where('srp_erp_mfq_customerinvoicedetails.invoiceAutoID', $this->input->post("invoiceAutoID"));
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_customerinvoicedetails.revenueGLAutoID', 'left');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_customerinvoicedetails.itemAutoID', 'left');
        $this->db->join('srp_erp_mfq_customerinvoicemaster', 'srp_erp_mfq_customerinvoicemaster.invoiceAutoID = srp_erp_mfq_customerinvoicedetails.invoiceAutoID', 'left');
        $detail = $this->db->get('srp_erp_mfq_customerinvoicedetails')->result_array();
        return $detail;

    }

    function fetch_double_entry_mfq_customerInvoice($invoiceAutoID)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();

        $invoiceMaster = $this->db->query("SELECT cinm.invoiceAutoID,cinm.transactionCurrencyID,currency.CurrencyCode as transactionCurrency,cinm.transactionExchangeRate,cinm.transactionCurrencyDecimalPlaces,cinm.companyLocalCurrencyID,cinm.companyLocalCurrency,cinm.companyLocalExchangeRate,cinm.companyLocalCurrencyDecimalPlaces,cinm.companyReportingCurrencyID,companyReportingCurrency,cinm.companyReportingExchangeRate,cinm.companyReportingCurrencyDecimalPlaces,detail.detailAmount,cinm.invoiceCode,cinm.invoiceDate,cinm.companyFinanceYear,cinm.FYPeriodDateFrom,cinm.FYPeriodDateTo,cinm.mfqCustomerAutoID,cinm.documentID,cinm.invoiceNarration,cinm.confirmedByEmpID,cinm.confirmedDate,cinm.confirmedByName,cinm.approvedDate,cinm.approvedDate,cinm.approvedbyEmpID,cinm.approvedbyEmpName,cinm.companyID FROM srp_erp_mfq_customerinvoicemaster cinm LEFT JOIN (SELECT SUM(transactionAmount) AS detailAmount,invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails WHERE invoiceAutoID = $invoiceAutoID) detail ON cinm.invoiceAutoID = detail.invoiceAutoID INNER JOIN srp_erp_currencymaster currency ON cinm.transactionCurrencyID = currency.currencyID WHERE cinm.invoiceAutoID = $invoiceAutoID")->row_array();

        $this->db->select('cm.CustomerAutoID,ca.GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory,cm.CustomerSystemCode,cm.customerCurrencyID,cm.customerCurrency,cm.customerCurrencyDecimalPlaces,cm.CustomerName');
        $this->db->where('mfqCustomerAutoID', $invoiceMaster['mfqCustomerAutoID']);
        $this->db->from('srp_erp_mfq_customermaster mcm');
        $this->db->join('srp_erp_customermaster cm', 'mcm.CustomerAutoID = cm.customerAutoID', 'left');
        $this->db->join('srp_erp_chartofaccounts ca', 'ca.GLAutoID = cm.receivableAutoID', 'left');
        $customerMaster = $this->db->get()->row_array();

        $globalArray = array();
        /*creditGL*/
        if ($customerMaster) {
            $data_arr['auto_id'] = $invoiceAutoID;
            $data_arr['gl_auto_id'] = $customerMaster['GLAutoID'];
            $data_arr['gl_code'] = $customerMaster['systemAccountCode'];
            $data_arr['secondary'] = $customerMaster['GLSecondaryCode'];
            $data_arr['gl_desc'] = $customerMaster['GLDescription'];
            $data_arr['gl_type'] = $customerMaster['subCategory'];
            $data_arr['segment_id'] = NULL;
            $data_arr['segment'] = NULL;
            $data_arr['projectID'] = NULL;
            $data_arr['projectExchangeRate'] = NULL;
            $data_arr['isAddon'] = 0;
            $data_arr['subLedgerType'] = 0;
            $data_arr['subLedgerDesc'] = null;
            $data_arr['partyContractID'] = null;
            $data_arr['partyType'] = 'Customer';
            $data_arr['partyAutoID'] = $customerMaster['CustomerAutoID'];
            $data_arr['partySystemCode'] = $customerMaster['CustomerSystemCode'];
            $data_arr['partyName'] = $customerMaster['CustomerName'];
            $data_arr['partyCurrencyID'] = $customerMaster['customerCurrencyID'];
            $data_arr['partyCurrency'] = $customerMaster['customerCurrency'];
            $data_arr['companyLocalExchangeRate'] = $invoiceMaster['companyLocalExchangeRate'];
            $data_arr['companyReportingExchangeRate'] = $invoiceMaster['companyReportingExchangeRate'];
            $data_arr['transactionExchangeRate'] = 1;
            $data_arr['partyExchangeRate'] = 1;
            $data_arr['partyCurrencyAmount'] = 0;
            $data_arr['partyCurrencyDecimalPlaces'] = $customerMaster['customerCurrencyDecimalPlaces'];
            $data_arr['gl_dr'] = $invoiceMaster['detailAmount'];
            $data_arr['gl_cr'] = '';
            $data_arr['amount_type'] = 'dr';
            array_push($globalArray, $data_arr);

        }

        /*item revenue*/
        $creditGL = $this->db->query("SELECT cid.invoiceAutoID,sum(cid.transactionAmount) AS transactionTotal,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,ca.GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory FROM srp_erp_mfq_customerinvoicedetails cid INNER JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.revenueGLAutoID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_segment.mfqSegmentID = cid.segmentID LEFT JOIN srp_erp_segment ON  srp_erp_segment.segmentID = srp_erp_mfq_segment.segmentID WHERE cid.invoiceAutoID = $invoiceAutoID AND type=1 GROUP BY revenueGLAutoID")->result_array();
        if ($creditGL) {
            foreach ($creditGL as $credit) {
                $data_arr['auto_id'] = $invoiceAutoID;
                $data_arr['gl_auto_id'] = $credit['GLAutoID'];
                $data_arr['gl_code'] = $credit['systemAccountCode'];
                $data_arr['secondary'] = $credit['GLSecondaryCode'];
                $data_arr['gl_desc'] = $credit['GLDescription'];
                $data_arr['gl_type'] = $credit['subCategory'];
                $data_arr['segment_id'] = $credit['segmentID'];
                $data_arr['segment'] = $credit['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $customerMaster['CustomerAutoID'];
                $data_arr['partySystemCode'] = $customerMaster['CustomerSystemCode'];
                $data_arr['partyName'] = $customerMaster['CustomerName'];
                $data_arr['partyCurrencyID'] = $customerMaster['customerCurrencyID'];
                $data_arr['partyCurrency'] = $customerMaster['customerCurrency'];
                $data_arr['companyLocalExchangeRate'] = $invoiceMaster['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $invoiceMaster['companyReportingExchangeRate'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $customerMaster['customerCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $credit['transactionTotal'];
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }
        /*item revenue*/
        $creditGL = $this->db->query("SELECT cid.invoiceAutoID,sum(cid.transactionAmount) AS transactionTotal,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,ca.GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory FROM srp_erp_mfq_customerinvoicedetails cid INNER JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.revenueGLAutoID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_segment.mfqSegmentID = cid.segmentID LEFT JOIN srp_erp_segment ON  srp_erp_segment.segmentID = srp_erp_mfq_segment.segmentID WHERE cid.invoiceAutoID = $invoiceAutoID AND type=2 GROUP BY revenueGLAutoID")->result_array();
        if ($creditGL) {
            foreach ($creditGL as $credit) {
                $data_arr['auto_id'] = $invoiceAutoID;
                $data_arr['gl_auto_id'] = $credit['GLAutoID'];
                $data_arr['gl_code'] = $credit['systemAccountCode'];
                $data_arr['secondary'] = $credit['GLSecondaryCode'];
                $data_arr['gl_desc'] = $credit['GLDescription'];
                $data_arr['gl_type'] = $credit['subCategory'];
                $data_arr['segment_id'] = $credit['segmentID'];
                $data_arr['segment'] = $credit['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $customerMaster['CustomerAutoID'];
                $data_arr['partySystemCode'] = $customerMaster['CustomerSystemCode'];
                $data_arr['partyName'] = $customerMaster['CustomerName'];
                $data_arr['partyCurrencyID'] = $customerMaster['customerCurrencyID'];
                $data_arr['partyCurrency'] = $customerMaster['customerCurrency'];
                $data_arr['companyLocalExchangeRate'] = $invoiceMaster['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $invoiceMaster['companyReportingExchangeRate'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $customerMaster['customerCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $credit['transactionTotal'];
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }

        /*item expense*/
        $creditGL = $this->db->query("SELECT cid.invoiceAutoID,sum(cid.totalCost) AS transactionTotal,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,ca.GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory FROM srp_erp_mfq_customerinvoicedetails cid INNER JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.expenseGLAutoID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_segment.mfqSegmentID = cid.segmentID LEFT JOIN srp_erp_segment ON  srp_erp_segment.segmentID = srp_erp_mfq_segment.segmentID WHERE cid.invoiceAutoID = $invoiceAutoID AND type=2 GROUP BY expenseGLAutoID")->result_array();
        if ($creditGL) {
            foreach ($creditGL as $credit) {
                $data_arr['auto_id'] = $invoiceAutoID;
                $data_arr['gl_auto_id'] = $credit['GLAutoID'];
                $data_arr['gl_code'] = $credit['systemAccountCode'];
                $data_arr['secondary'] = $credit['GLSecondaryCode'];
                $data_arr['gl_desc'] = $credit['GLDescription'];
                $data_arr['gl_type'] = $credit['subCategory'];
                $data_arr['segment_id'] = $credit['segmentID'];
                $data_arr['segment'] = $credit['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $customerMaster['CustomerAutoID'];
                $data_arr['partySystemCode'] = $customerMaster['CustomerSystemCode'];
                $data_arr['partyName'] = $customerMaster['CustomerName'];
                $data_arr['partyCurrencyID'] = $customerMaster['customerCurrencyID'];
                $data_arr['partyCurrency'] = $customerMaster['customerCurrency'];
                $data_arr['companyLocalExchangeRate'] = $invoiceMaster['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $invoiceMaster['companyReportingExchangeRate'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $customerMaster['customerCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = $credit['transactionTotal'];;
                $data_arr['gl_cr'] = '';
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }

        /*item asset*/
        $creditGL = $this->db->query("SELECT cid.invoiceAutoID,sum(cid.totalCost) AS transactionTotal,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,ca.GLAutoID,ca.systemAccountCode,ca.GLSecondaryCode,ca.GLDescription,ca.subCategory FROM srp_erp_mfq_customerinvoicedetails cid INNER JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.assetGLAutoID LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_segment.mfqSegmentID = cid.segmentID LEFT JOIN srp_erp_segment ON  srp_erp_segment.segmentID = srp_erp_mfq_segment.segmentID WHERE cid.invoiceAutoID = $invoiceAutoID AND type=2 GROUP BY assetGLAutoID")->result_array();
        if ($creditGL) {
            foreach ($creditGL as $credit) {
                $data_arr['auto_id'] = $invoiceAutoID;
                $data_arr['gl_auto_id'] = $credit['GLAutoID'];
                $data_arr['gl_code'] = $credit['systemAccountCode'];
                $data_arr['secondary'] = $credit['GLSecondaryCode'];
                $data_arr['gl_desc'] = $credit['GLDescription'];
                $data_arr['gl_type'] = $credit['subCategory'];
                $data_arr['segment_id'] = $credit['segmentID'];
                $data_arr['segment'] = $credit['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $customerMaster['CustomerAutoID'];
                $data_arr['partySystemCode'] = $customerMaster['CustomerSystemCode'];
                $data_arr['partyName'] = $customerMaster['CustomerName'];
                $data_arr['partyCurrencyID'] = $customerMaster['customerCurrencyID'];
                $data_arr['partyCurrency'] = $customerMaster['customerCurrency'];
                $data_arr['companyLocalExchangeRate'] = $invoiceMaster['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $invoiceMaster['companyReportingExchangeRate'];
                $data_arr['transactionExchangeRate'] = 1;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyAmount'] = 0;
                $data_arr['partyCurrencyDecimalPlaces'] = $customerMaster['customerCurrencyDecimalPlaces'];
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $credit['transactionTotal'];
                $data_arr['amount_type'] = 'cr';
                array_push($globalArray, $data_arr);
            }
        }

        $gl_array['currency'] = $invoiceMaster['transactionCurrency'];
        $gl_array['decimal_places'] = $invoiceMaster['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'MCINV';
        $gl_array['name'] = 'Customer Invoice';
        $gl_array['primary_Code'] = $invoiceMaster['invoiceCode'];
        $gl_array['date'] = $invoiceMaster['invoiceDate'];
        $gl_array['finance_year'] = $invoiceMaster['companyFinanceYear'];
        $gl_array['finance_period'] = $invoiceMaster['FYPeriodDateFrom'] . ' - ' . $invoiceMaster['FYPeriodDateTo'];
        $gl_array['master_data'] = $invoiceMaster;
        $gl_array['gl_detail'] = $globalArray;

        return $gl_array;
    }

    function customer_invoice_confirmation()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_customerinvoicemaster');
        $row = $this->db->get()->row_array();
        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $this->db->select('*');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->from('srp_erp_mfq_customerinvoicemaster');
            $invoiceMaster = $this->db->get()->row_array();

//            $validate_code = validate_code_duplication($invoiceMaster['invoiceCode'], 'invoiceCode', $invoiceAutoID,'invoiceAutoID', 'srp_erp_mfq_customerinvoicemaster');
//            if(!empty($validate_code)) {
//                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
//            }


            $mfqcustomerIDLinked = $this->db->query("select mfqCustomerAutoID FROM srp_erp_mfq_customermaster WHERE companyID = $companyID AND mfqCustomerAutoID = {$invoiceMaster['mfqCustomerAutoID']} AND CustomerAutoID IS NULL")->row('mfqCustomerAutoID');
            if ($mfqcustomerIDLinked) {
                return array('w', 'Manufacturing Customer not linked with ERP Customer');
            }

            if($invoiceMaster['mfqSegmentID']!=''){
                $mfqsegmentIDLinked = $this->db->query("select segmentID from srp_erp_mfq_segment WHERE companyID = $companyID AND mfqSegmentID = {$invoiceMaster['mfqSegmentID']}")->row('segmentID');
                if (($mfqsegmentIDLinked == '') || empty($mfqsegmentIDLinked)) {
                    return array('e', 'Manufacturing segment not linked with ERP segment');
                }
            }else{
                return array('e', 'Segment id is null.');
            }

            $this->db->select("*");
            $this->db->from('srp_erp_companyfinanceperiod');
            $this->db->join('srp_erp_companyfinanceyear', "srp_erp_companyfinanceyear.companyFinanceYearID=srp_erp_companyfinanceperiod.companyFinanceYearID", "LEFT");
            $this->db->where('srp_erp_companyfinanceperiod.companyID', $this->common_data['company_data']['company_id']);
            $this->db->where("'{$invoiceMaster['invoiceDate']}' BETWEEN dateFrom AND dateTo");
            $this->db->where("srp_erp_companyfinanceperiod.isActive", 1);
            $financePeriod = $this->db->get()->row_array();

            if ($financePeriod) {
                $this->db->set('confirmedYN', 1);
                $this->db->set('confirmedByEmpID', current_userID());
                $this->db->set('confirmedByName', current_user());
                $this->db->set('confirmedDate', current_date(false));
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $result = $this->db->update('srp_erp_mfq_customerinvoicemaster');

                if ($result) {
                    /*$gearsDB = $this->load->database('gearserp', TRUE);

                    $gearserpCustomerInvoice = $gearsDB->query("SELECT max(serialNo) as serialNo FROM erp_custinvoicedirect WHERE companyID = 'HEMT'")->row_array();

                    $smeCustomerInvoice = $this->db->query("SELECT cinm.*,detail.detailAmount FROM srp_erp_mfq_customerinvoicemaster cinm LEFT JOIN (SELECT SUM(transactionAmount) AS detailAmount,invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails WHERE invoiceAutoID = {$invoiceAutoID}) detail ON cinm.invoiceAutoID = detail.invoiceAutoID WHERE cinm.invoiceAutoID = {$invoiceAutoID}")->row_array();

                    $smeCustomerInvoiceDetail = $this->db->query("SELECT cid.revenueGLAutoID as glCode,ca.GLDescription as glDescription,ca.masterCategory as glType,segment.segmentCode as mSegmentCode,cid.requestedQty as requestedQty,unitRate,transactionAmount,companyLocalAmount,companyReportingAmount FROM srp_erp_mfq_customerinvoicedetails cid LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.revenueGLAutoID LEFT JOIN srp_erp_mfq_segment segment ON segment.mfqSegmentID = cid.segmentID WHERE invoiceAutoID = {$invoiceAutoID}")->result_array();

                    $smeMFQCustomerMaster = $this->db->query("SELECT CustomerAutoID FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = {$smeCustomerInvoice['mfqCustomerAutoID']}")->row_array();

                    $gearserpCustomerDetail = $gearsDB->query("SELECT custGLaccount FROM customermaster WHERE customerCodeSystem = {$smeMFQCustomerMaster['CustomerAutoID']}")->row_array();

                    $gearserpFinanceYear = $gearsDB->query("SELECT companyfinanceperiod.companyFinanceYearID,bigginingDate,endingDate,dateFrom,dateTo,companyFinancePeriodID FROM companyfinanceperiod LEFT JOIN companyfinanceyear ON companyfinanceperiod.companyFinanceYearID = companyfinanceyear.companyFinanceYearID WHERE '{$smeCustomerInvoice['invoiceDate']}' BETWEEN dateFrom AND dateTo AND companyfinanceperiod.companyID='HEMT' AND departmentID = 'AR'")->row_array();

                    $newSerialNumber = $gearserpCustomerInvoice['serialNo'] + 1;
                    $systemCode = "HEMT" . '\\' . date("Y") . '\\' . "INV" . str_pad($newSerialNumber, 6, '0', STR_PAD_LEFT);

                    $data['companyID'] = 'HEMT';
                    $data['documentID'] = 'INV';
                    $data['serialNo'] = $newSerialNumber;
                    $data['bookingInvCode'] = $systemCode;
                    $data['bookingDate'] = $smeCustomerInvoice['invoiceDate'];
                    $data['comments'] = $smeCustomerInvoice['invoiceNarration'];
                    $data['invoiceDueDate'] = $smeCustomerInvoice['invoiceDueDate'];
                    $data['customerID'] = $smeMFQCustomerMaster['CustomerAutoID'];
                    $data['customerGLCode'] = $gearserpCustomerDetail['custGLaccount'];
                    $data['custTransactionCurrencyID'] = $smeCustomerInvoice['transactionCurrencyID'];
                    $data['custTransactionCurrencyER'] = 1;
                    $data['companyReportingCurrencyID'] = $smeCustomerInvoice['companyReportingCurrencyID'];
                    $data['companyReportingER'] = $smeCustomerInvoice['companyReportingExchangeRate'];
                    $data['localCurrencyID'] = $smeCustomerInvoice['transactionCurrencyID'];
                    $data['localCurrencyER'] = 1;
                    $data['bookingAmountTrans'] = $smeCustomerInvoice['detailAmount'];
                    $data['isPerforma'] = 0;
                    $data['bookingAmountLocal'] = ($smeCustomerInvoice['detailAmount'] / $data['localCurrencyER']);
                    $data['bookingAmountRpt'] = ($smeCustomerInvoice['detailAmount'] / $data['companyReportingER']);
                    $data['companyFinanceYearID'] = $gearserpFinanceYear["companyFinanceYearID"];
                    $data['FYBiggin'] = $gearserpFinanceYear["bigginingDate"];
                    $data['FYEnd'] = $gearserpFinanceYear["endingDate"];
                    $data['companyFinancePeriodID'] = $gearserpFinanceYear["companyFinancePeriodID"];
                    $data['FYPeriodDateFrom'] = $gearserpFinanceYear["dateFrom"];
                    $data['FYPeriodDateTo'] = $gearserpFinanceYear["dateTo"];

                    $gearsDB->insert('erp_custinvoicedirect', $data);
                    $smeCustomerInvoiceMaster_id = $gearsDB->insert_id();

                    if ($smeCustomerInvoiceMaster_id) {
                        if (!empty($smeCustomerInvoiceDetail)) {
                            foreach ($smeCustomerInvoiceDetail as $row) {
                                $data_detail['custInvoiceDirectID'] = $smeCustomerInvoiceMaster_id;
                                $data_detail['companyID'] = 'HEMT';
                                $data_detail['serviceLineCode'] = $row['mSegmentCode'];
                                $data_detail['customerID'] = $smeMFQCustomerMaster['CustomerAutoID'];
                                $data_detail['glCode'] = $row['glCode'];
                                $data_detail['glCodeDes'] = $row['glDescription'];
                                $data_detail['accountType'] = $row['glType'];
                                $data_detail['comments'] = $smeCustomerInvoice['invoiceNarration'];
                                $data_detail['invoiceAmountCurrency'] = $smeCustomerInvoice['transactionCurrencyID'];
                                $data_detail['invoiceAmountCurrencyER'] = $smeCustomerInvoice['transactionExchangeRate'];
                                $data_detail['invoiceQty'] = $row['requestedQty'];
                                $data_detail['unitCost'] = $row['unitRate'];
                                $data_detail['invoiceAmount'] = $row['transactionAmount'];
                                $data_detail['localCurrency'] = $smeCustomerInvoice['companyLocalCurrencyID'];
                                $data_detail['localCurrencyER'] = $smeCustomerInvoice['companyLocalExchangeRate'];
                                $data_detail['localAmount'] = $row['companyLocalAmount'];
                                $data_detail['comRptCurrency'] = $smeCustomerInvoice['companyReportingCurrencyID'];
                                $data_detail['comRptCurrencyER'] = $smeCustomerInvoice['companyReportingExchangeRate'];
                                $data_detail['comRptAmount'] = $row['companyReportingAmount'];
                                $gearsDB->insert('erp_custinvoicedirectdet', $data_detail);
                            }
                        }
                    }*/

                    $smeCustomerInvoice = $this->db->query("SELECT cinm.*,detail.detailAmount,segment.segmentID,segment.segmentCode FROM srp_erp_mfq_customerinvoicemaster cinm LEFT JOIN (SELECT SUM(transactionAmount) AS detailAmount,invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails WHERE invoiceAutoID = {$invoiceAutoID}) detail ON cinm.invoiceAutoID = detail.invoiceAutoID LEFT JOIN srp_erp_mfq_deliverynote ON srp_erp_mfq_deliverynote.deliverNoteID = cinm.deliveryNoteID LEFT JOIN srp_erp_mfq_segment mfqsegment on mfqsegment.mfqSegmentID = srp_erp_mfq_deliverynote.mfqSegmentID LEFT JOIN srp_erp_segment segment on segment.segmentID = mfqsegment.segmentID WHERE cinm.invoiceAutoID = {$invoiceAutoID}")->row_array();

                    $smeCustomerInvoiceDetail = $this->db->query("SELECT cid.deliveryNoteDetID as deliveryNoteDetID,cid.revenueGLAutoID as glCode,ca.GLDescription as glDescription,ca.subCategory as glType,segment.segmentCode as mSegmentCode,cid.requestedQty as requestedQty,
                            unitRate,cid.transactionAmount,cid.companyLocalAmount,cid.companyReportingAmount,ca.GLSecondaryCode,ca.systemAccountCode,itm.*,cid.type,cid.assetGLAutoID as asstglCode,
                            asst.GLDescription as asstglDescription,asst.GLSecondaryCode as asstGLSecondaryCode,asst.systemAccountCode as asstsystemAccountCode,asst.subCategory as asstglType, cid.invoiceDetailsAutoID AS invoiceDetailsAutoID,
                            itm.mfqItemID, deliveryNoteID, cid.taxAmount as taxAmount, cid.taxCalculationformulaID as taxCalculationformulaID
                            FROM srp_erp_mfq_customerinvoicedetails cid
                            JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID
                            LEFT JOIN srp_erp_chartofaccounts ca ON ca.GLAutoID = cid.revenueGLAutoID
                            LEFT JOIN srp_erp_chartofaccounts asst ON asst.GLAutoID = cid.assetGLAutoID
                            LEFT JOIN srp_erp_mfq_segment segment ON segment.mfqSegmentID = cid.segmentID
                            LEFT JOIN (
                                SELECT srp_erp_itemmaster.*,srp_erp_mfq_itemmaster.mfqItemID, unbilledServicesGLAutoID
                                FROM srp_erp_mfq_itemmaster
                                LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID =  srp_erp_mfq_itemmaster.itemAutoID
                            ) itm ON itm.mfqItemID = cid.itemAutoID
                            WHERE cid.invoiceAutoID = {$invoiceAutoID}")->result_array();

                    $smeMFQCustomerMaster = $this->db->query("SELECT CustomerAutoID FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = {$smeCustomerInvoice['mfqCustomerAutoID']}")->row_array();

                    $jobDetail = $this->db->query("SELECT srp_erp_mfq_segment.*,srp_erp_segment.segmentCode as segCode,srp_erp_warehousemaster.* FROM srp_erp_mfq_deliverynotedetail INNER JOIN srp_erp_mfq_job ON workProcessID = jobID LEFT JOIN srp_erp_mfq_segment ON  srp_erp_mfq_job.mfqSegmentID = srp_erp_mfq_segment.mfqSegmentID LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID LEFT JOIN srp_erp_mfq_warehousemaster ON  srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.wareHouseAutoID  WHERE deliveryNoteID = {$smeCustomerInvoice['deliveryNoteID']} Group BY jobID")->row_array();

                    $this->load->library('sequence');

                    $this->db->select('*');
                    $this->db->from('srp_erp_customermaster');
                    $customer_arr = $this->db->where('customerAutoID', $smeMFQCustomerMaster['CustomerAutoID'])->get()->row_array();

                    $data['companyID'] = $smeCustomerInvoice['companyID'];
                    $data['invoiceType'] = 'Direct';
                    $data['mfqInvoiceAutoID'] = $invoiceAutoID;
                    $data['documentID'] = 'CINV';
                    $data['isGroupBasedTax'] = $smeCustomerInvoice['isGroupBasedTax'];
                    $data['invoiceDate'] = $smeCustomerInvoice['invoiceDate'];
                    $data['acknowledgementDate'] = $smeCustomerInvoice['invoiceDate'];
                    $data['invoiceNarration'] = $smeCustomerInvoice['invoiceNarration'];
                    $data['invoiceDueDate'] = $smeCustomerInvoice['invoiceDueDate'];
                    $data['customerInvoiceDate'] = $smeCustomerInvoice['invoiceDate'];
                    $data['customerInvoiceDate'] = $smeCustomerInvoice['invoiceDate'];
                    $data['referenceNo'] = $smeCustomerInvoice['jobreferenceNo'];

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

                    $data['transactionCurrencyID'] = $smeCustomerInvoice['transactionCurrencyID'];
                    $data['transactionCurrency'] = fetch_currency_code($smeCustomerInvoice['transactionCurrencyID']);
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

                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];

                    $data['companyFinanceYearID'] = $financePeriod["companyFinanceYearID"];
                    $data['FYBegin'] = trim($financePeriod["beginingDate"]);
                    $data['FYEnd'] = trim($financePeriod["endingDate"]);
                    $data['companyFinanceYear'] = trim($financePeriod["beginingDate"]) . ' - ' . trim($financePeriod["endingDate"]);
                    $data['companyFinancePeriodID'] = $financePeriod['companyFinancePeriodID'];

                    $data['segmentID'] = trim($smeCustomerInvoice['segmentID'] ?? '');
                    $data['segmentCode'] = trim($smeCustomerInvoice['segmentCode'] ?? '');
                    $data['invoiceType'] = 'Manufacturing';

                    $date = strtotime($data['invoiceDate']);
                    $month = date("m", $date);
                    $year = date("Y", $date);

                    $data['invoiceCode'] = $this->sequence->sequence_generator_fin($data['documentID'], $data['companyFinanceYearID'], $year, $month);
                    $data['timestamp'] = current_date();

                    $this->db->insert('srp_erp_customerinvoicemaster', $data);
                    $smeCustomerInvoiceMaster_id = $this->db->insert_id();

                    $policyJEC = getPolicyValues('JEC', 'All');
                    if ($smeCustomerInvoiceMaster_id) {
                        if (!empty($smeCustomerInvoiceDetail)) {
                            foreach ($smeCustomerInvoiceDetail as $row) {
                                if ($row["type"] == 1) {
                                    $data_detail['invoiceAutoID'] = $smeCustomerInvoiceMaster_id;
                                    $data_detail['type'] = 'GL';
                                    $data_detail['revenueGLAutoID'] = $row['glCode'];
                                    $data_detail['mfqinvoiceDetailsAutoID'] = $row['invoiceDetailsAutoID'];
                                    $data_detail['revenueGLCode'] = $row['GLSecondaryCode'];
                                    $data_detail['revenueSystemGLCode'] = $row['systemAccountCode'];
                                    $data_detail['revenueGLDescription'] = $row['glDescription'];
                                    $data_detail['revenueGLType'] = $row['glType'];
                                    $data_detail['description'] = $smeCustomerInvoice['invoiceNarration'];

                                    $data_detail['transactionAmount'] = $row["transactionAmount"];
                                    $data_detail['companyLocalAmount'] = $row["companyLocalAmount"];
                                    $data_detail['companyReportingAmount'] = $row["companyReportingAmount"];
                                    $customerAmount = 0;
                                    if ($smeCustomerInvoice['customerCurrencyExchangeRate']) {
                                        $customerAmount = $data_detail['transactionAmount'] / $smeCustomerInvoice['customerCurrencyExchangeRate'];
                                    } else {
                                        $customerAmount = $data_detail['transactionAmount'];
                                    }

                                    $data_detail['customerAmount'] = $customerAmount;
                                    $data_detail['taxCalculationformulaID'] = $row['taxCalculationformulaID'];
                                    $data_detail['taxAmount'] = $row['taxAmount'];
                                    $data_detail['segmentID'] = $data['segmentID'];
                                    $data_detail['companyID'] = $smeCustomerInvoice['companyID'];
                                    $data_detail['requestedQty'] = $row['requestedQty'];
                                    $data_detail['segmentCode'] = trim($jobDetail['segCode'] ?? '');
                                    $data_detail['unittransactionAmount'] = $row['unitRate'];
                                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                                    $data_detail['timestamp'] = current_date();

                                    $this->db->insert('srp_erp_customerinvoicedetails', $data_detail);
                                    $smeCustomerInvoiceDetail_id = $this->db->insert_id();
                                } else {
                                    $data_item_detail['invoiceAutoID'] = $smeCustomerInvoiceMaster_id;
                                    $data_item_detail['type'] = 'Item';
                                    $data_item_detail['mfqinvoiceDetailsAutoID'] = $row['invoiceDetailsAutoID'];
                                    $data_item_detail['itemAutoID'] = $row['itemAutoID'];
                                    $data_item_detail['mfqItemAutoID'] = $row['mfqItemID'];
                                    $data_item_detail['itemSystemCode'] = $row['itemSystemCode'];
                                    $data_item_detail['itemDescription'] = $row['itemDescription'];
                                    $data_item_detail['itemCategory'] = $row['mainCategory'];

                                    if ($row['mainCategory'] == 'Service' && $row['mainCategory'] == 'Non Inventory') {
                                        if (!empty($row['unbilledServicesGLAutoID'])) {
                                            $glDetails = fetch_gl_account_desc($row['unbilledServicesGLAutoID']);

                                            $data_item_detail['expenseGLAutoID'] = $glDetails['GLAutoID'];
                                            $data_item_detail['expenseSystemGLCode'] = $glDetails['systemAccountCode'];
                                            $data_item_detail['expenseGLCode'] = $glDetails['GLSecondaryCode'];
                                            $data_item_detail['expenseGLDescription'] = $glDetails['GLDescription'];
                                            $data_item_detail['expenseGLType'] = $glDetails['subCategory'];
                                        }
                                    } else {
                                        $data_item_detail['expenseGLAutoID'] = $row['costGLAutoID'];
                                        $data_item_detail['expenseSystemGLCode'] = $row['costSystemGLCode'];
                                        $data_item_detail['expenseGLCode'] = $row['costGLCode'];
                                        $data_item_detail['expenseGLDescription'] = $row['costDescription'];
                                        $data_item_detail['expenseGLType'] = $row['costType'];
                                    }

                                    if ($policyJEC && $policyJEC == 1) {
                                        if ($row['mainCategory'] == "Inventory") {
                                            $itemcategory = 'Inventory';
                                        } else {
                                            $itemcategory = 'Service';
                                        }
                                        $gldetails = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                FROM srp_erp_mfq_postingconfiguration JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_postingconfiguration.value
                                                WHERE configurationCode = '$itemcategory' AND srp_erp_mfq_postingconfiguration.companyID = {$companyID}")->row_array();

                                        $data_item_detail['assetGLAutoID'] = $gldetails['GLAutoID'];
                                        $data_item_detail['assetGLCode'] = $gldetails['GLSecondaryCode'];
                                        $data_item_detail['assetSystemGLCode'] = $gldetails['systemAccountCode'];
                                        $data_item_detail['assetGLDescription'] = $gldetails['GLDescription'];
                                    } else {
                                        $data_item_detail['assetGLAutoID'] = $row['asstglCode'];
                                        $data_item_detail['assetGLCode'] = $row['asstGLSecondaryCode'];
                                        $data_item_detail['assetSystemGLCode'] = $row['asstsystemAccountCode'];
                                        $data_item_detail['assetGLDescription'] = $row['asstglDescription'];
                                    }

                                    $data_item_detail['revenueGLAutoID'] = $row['revanueGLAutoID'];
                                    $data_item_detail['revenueGLCode'] = $row['revanueGLCode'];
                                    $data_item_detail['revenueSystemGLCode'] = $row['revanueSystemGLCode'];
                                    $data_item_detail['revenueGLDescription'] = $row['revanueDescription'];
                                    $data_item_detail['revenueGLType'] = $row['revanueType'];
                                    $data_item_detail['description'] = $smeCustomerInvoice['invoiceNarration'];

                                    $this->db->select('jobID, (IFNULL(companyLocalAmount, 0) / qty) AS wacAmount');
                                    $this->db->from('srp_erp_mfq_deliverynotedetail');
                                    $this->db->join('srp_erp_mfq_job', 'srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID');
                                    $this->db->where('deliveryNoteDetailID', $row['deliveryNoteDetID']);
                                    $this->db->where('deliveryNoteID', $row['deliveryNoteID']);
                                    $this->db->where('srp_erp_mfq_job.mfqItemID', $row['mfqItemID']);
                                    $jobIDs = $this->db->get()->row_array();

                                    $data_item_detail['companyLocalWacAmount'] = $jobIDs['wacAmount'];
                                    $data_item_detail['requestedQty'] = $row['requestedQty'];
                                    $data_item_detail['defaultUOMID'] = $row['defaultUnitOfMeasureID'];
                                    $data_item_detail['defaultUOM'] = $row['defaultUnitOfMeasure'];
                                    $data_item_detail['unitOfMeasureID'] = $row['defaultUnitOfMeasureID'];
                                    $data_item_detail['unitOfMeasure'] = $row['defaultUnitOfMeasure'];
                                    $data_item_detail['conversionRateUOM'] = 1;

                                    $data_item_detail['transactionAmount'] = $row["transactionAmount"];
                                    $data_item_detail['companyLocalAmount'] = $row["companyLocalAmount"];
                                    $data_item_detail['companyReportingAmount'] = $row["companyReportingAmount"];
                                    $customerAmount = 0;
                                    if ($smeCustomerInvoice['customerCurrencyExchangeRate']) {
                                        $customerAmount = $data_item_detail['transactionAmount'] / $smeCustomerInvoice['customerCurrencyExchangeRate'];
                                    } else {
                                        $customerAmount = $data_item_detail['transactionAmount'];
                                    }

                                    $data_item_detail['customerAmount'] = $customerAmount;
                                    $data_item_detail['taxCalculationformulaID'] = $row['taxCalculationformulaID'];
                                    $data_item_detail['taxAmount'] = $row['taxAmount'];
                                    $data_item_detail['unittransactionAmount'] = $row['unitRate'];
                                    $data_item_detail['wareHouseAutoID'] = $jobDetail['wareHouseAutoID'];
                                    $data_item_detail['wareHouseCode'] = $jobDetail['wareHouseCode'];
                                    $data_item_detail['wareHouseDescription'] = $jobDetail['wareHouseDescription'];
                                    $data_item_detail['wareHouseLocation'] = $jobDetail['wareHouseLocation'];
                                    $data_item_detail['discountPercentage'] = 0;
                                    $data_item_detail['discountAmount'] = 0;
                                    $data_item_detail['segmentID'] = $data['segmentID'];
                                    $data_item_detail['companyID'] = $smeCustomerInvoice['companyID'];
                                    $data_item_detail['segmentCode'] = trim($jobDetail['segCode'] ?? '');
                                    $data_item_detail['createdUserGroup'] = $this->common_data['user_group'];
                                    $data_item_detail['createdPCID'] = $this->common_data['current_pc'];
                                    $data_item_detail['createdUserID'] = $this->common_data['current_userID'];
                                    $data_item_detail['createdUserName'] = $this->common_data['current_user'];
                                    $data_item_detail['createdDateTime'] = $this->common_data['current_date'];
                                    $data_item_detail['timestamp'] = current_date();
                                    $this->db->insert('srp_erp_customerinvoicedetails', $data_item_detail);
                                    $smeCustomerInvoiceDetail_id = $this->db->insert_id();
                                }

                                /** taxledger details Duplication
                                 */
                                if($smeCustomerInvoice['isGroupBasedTax'] == 1) {
                                    $ledgerEntry = $this->db->query("SELECT * FROM srp_erp_taxledger WHERE companyID = {$companyID} AND documentID  = 'MCINV' AND documentMasterAutoID = {$invoiceAutoID} AND documentDetailAutoID = {$row['invoiceDetailsAutoID']}")->result_array();
                                    if ($ledgerEntry) {
                                        foreach ($ledgerEntry as $value) {
                                            $dataleg['documentID'] = 'CINV';
                                            $dataleg['documentMasterAutoID'] = $smeCustomerInvoiceMaster_id;
                                            $dataleg['documentDetailAutoID'] = $smeCustomerInvoiceDetail_id;
                                            $dataleg['taxDetailAutoID'] = null;
                                            $dataleg['taxPercentage'] = $value['taxPercentage'];
                                            $dataleg['ismanuallychanged'] = $value['ismanuallychanged'];
                                            $dataleg['taxFormulaMasterID'] = $value['taxFormulaMasterID'];
                                            $dataleg['taxFormulaDetailID'] = $value['taxFormulaDetailID'];
                                            $dataleg['taxMasterID'] = $value['taxMasterID'];
                                            $dataleg['taxGlAutoID'] = $value['taxGlAutoID'];
                                            $dataleg['rcmApplicableYN'] = 0;
                                            $dataleg['outputVatTransferGL'] = $value['outputVatTransferGL'];
                                            $dataleg['outputVatGL'] = $value['outputVatGL'];
                                            $dataleg['amount'] = $value['amount'];
                                            $dataleg['isClaimable'] = $value['isClaimable'];
                                            $dataleg['formula'] = $value['formula'];
                                            $dataleg['countryID'] = $value['countryID'];
                                            $dataleg['partyVATEligibleYN'] = $value['partyVATEligibleYN'];
                                            $dataleg['partyID'] = $value['partyID'];
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
                                            $ledgerIDArr[] = $this->db->insert_id();
                                        }
                                    }
                                }
                                /** End of taxledger details Duplication*/
                            }
                        }

                        /** Duplicate Attachments */
                        $this->duplicate_attachments_to_erp_invoice($invoiceAutoID, $smeCustomerInvoiceMaster_id);
                    }

                    $double_entry = $this->fetch_double_entry_mfq_customerInvoice($invoiceAutoID);
                    for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                        $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
                        $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                        $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];
                        $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
                        $generalledger_arr[$i]['documentYear'] = date("Y", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['invoiceDate']));
                        $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['invoiceNarration'];
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
                        /*$generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];*/
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

//                    if (!empty($generalledger_arr)) {
                    //$this->db->insert_batch('srp_erp_mfq_generalledger', $generalledger_arr);
//                        $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
//                    }
                }
            } else {
                return array('w', 'Finance period not active for customer invoice');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Invoice Confirmed Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Invoice : Confirmed Successfully');
            }
        }
    }

    function delete_customerInvoiceDetail()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('invoiceDetailsAutoID');
        $this->db->from('srp_erp_mfq_customerinvoicedetails');
        $this->db->where('invoiceAutoID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $this->db->delete('srp_erp_taxledger', array('documentDetailAutoID' => $this->input->post('invoiceDetailsID'), 'documentID'=>'MCINV'));
        $result = $this->db->delete('srp_erp_mfq_customerinvoicedetails', array('invoiceDetailsAutoID' => $this->input->post('invoiceDetailsID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function fetch_chartofaccount()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $sql = 'SELECT GLAutoID,GLSecondaryCode,GLDescription,CONCAT(IFNULL(GLSecondaryCode,"")," | ",IFNULL(GLDescription,"")," | ",IFNULL(subCategory,"") ) AS `Match` FROM srp_erp_chartofaccounts  WHERE (GLSecondaryCode LIKE "' . $search_string . '" OR GLDescription LIKE "' . $search_string . '" OR subCategory LIKE "' . $search_string . '" OR systemAccountCode LIKE "' . $search_string . '") AND companyID = "' . $companyID . '" AND isActive="1" AND `controllAccountYN` =0 AND `masterAccountYN` =0 AND `isActive` = 1 AND `isBank` =0 LIMIT 20';
        $data = $this->db->query($sql)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'GLAutoID' => $val['GLAutoID'], 'GLSecondaryCode' => $val['GLSecondaryCode']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_deliveryNote_details()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $currencyID = $this->input->post('selectedCurrencyID');

        $flowserve = getPolicyValues('MANFL', 'All');

        if($flowserve == 'FlowServe'){
            $this->db->select('srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID AS deliveryNoteDetailID, CONCAT( itemSystemCode, " - ", itemDescription ) AS itemdescription, estimatedetail.transactionCurrencyID, srp_erp_mfq_job.mfqItemID AS itemAutoID, defaultUnitOfMeasure AS uom,srp_erp_mfq_job.description, documentCode AS jobno, IFNULL( deliveredQty, 0 ) AS qty,(srp_erp_mfq_job.transactionAmount / srp_erp_mfq_job.qty) AS unitPrice, IFNULL(taxFormulaID, 0) AS taxFormulaID');
        }else{
            $this->db->select('srp_erp_mfq_deliverynotedetail.deliveryNoteDetailID AS deliveryNoteDetailID, CONCAT( itemSystemCode, " - ", itemDescription ) AS itemdescription, estimatedetail.transactionCurrencyID, srp_erp_mfq_job.mfqItemID AS itemAutoID, defaultUnitOfMeasure AS uom,srp_erp_mfq_job.description, documentCode AS jobno, IFNULL( deliveredQty, 0 ) AS qty,((IFNULL( discountedPrice, 0 )) * (( 100 + IFNULL( totMargin, 0 ))/ 100 ) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 ) / expectedQty) AS unitPrice, IFNULL(taxFormulaID, 0) AS taxFormulaID');
        }

        $this->db->from('srp_erp_mfq_deliverynotedetail');
        $this->db->join('srp_erp_mfq_deliverynote', "srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID", "left");
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID", "left");
        $this->db->join('srp_erp_mfq_estimatedetail estimatedetail', "estimatedetail.estimateDetailID = srp_erp_mfq_job.estimateDetailID", "left");
        $this->db->join('srp_erp_mfq_estimatemaster estimatemaster', "estimatemaster.estimateMasterID = srp_erp_mfq_job.estimateMasterID", "left");
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID", "left");
        $this->db->join('(SELECT itemAutoID, taxFormulaID FROM srp_erp_itemtaxformula WHERE taxType = 1 GROUP BY itemAutoID)srp_erp_itemtaxformula', "srp_erp_itemtaxformula.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID", "left");
        $this->db->where('srp_erp_mfq_deliverynote.confirmedYN', 1);
        $this->db->where('srp_erp_mfq_deliverynotedetail.deliveryNoteID', $this->input->post("deliveryNoteID"));
        $this->db->where('NOT EXISTS (SELECT *
                   FROM srp_erp_mfq_customerinvoicemaster
                   WHERE srp_erp_mfq_deliverynote.deliverNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID AND srp_erp_mfq_customerinvoicemaster.invoiceAutoID != ' . $invoiceAutoID . ')');
        $data = $this->db->get()->result_array();

        foreach ($data as $key => $val) {
            $transactionCurrencyID = $val['transactionCurrencyID'];
            $currencychange = currency_conversionID($transactionCurrencyID, $currencyID);
            if ($val['unitPrice'] != 0 && !empty($val['unitPrice'])) {
                $data[$key]['unitPrice'] = ($data[$key]['unitPrice'] / $currencychange['conversion']);
            }
        }

        return $data;

    }

    function fetch_attachment_for_invoice()
    {
        $ciMasterID = $this->input->post('ciMasterID');
        $deliveryNoteID = $this->db->query("SELECT deliveryNoteID FROM `srp_erp_mfq_customerinvoicemaster` WHERE invoiceAutoID = {$ciMasterID}")->row('deliveryNoteID');
        $jobIDs = $this->db->query("SELECT workProcessID FROM srp_erp_mfq_job JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID WHERE srp_erp_mfq_deliverynotedetail.deliveryNoteID = {$deliveryNoteID} GROUP BY workProcessID")->result_array();
        $jobID = array_unique(array_column($jobIDs, 'workProcessID'));
        $selectedJobs = implode(",", $jobID);

        $estimateMasterIDs = $this->db->query("SELECT srp_erp_mfq_estimatemaster.estimateMasterID AS estimateMasterID FROM srp_erp_mfq_estimatemaster join srp_erp_mfq_job ON srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID WHERE workProcessID IN ({$selectedJobs}) GROUP BY srp_erp_mfq_estimatemaster.estimateMasterID")->result_array();
        $estimateMasterID = array_unique(array_column($estimateMasterIDs, 'estimateMasterID'));
        $selectedestimateMasterID = implode(",", $estimateMasterID);

        $inquiries = $this->db->query("SELECT ciMasterID FROM srp_erp_mfq_estimatedetail WHERE estimateMasterID IN ($selectedestimateMasterID) GROUP BY ciMasterID ")->result_array();
        $ciMasterID = array_unique(array_column($inquiries, 'ciMasterID'));
        $selectedciMasterID = implode(",", $ciMasterID);

        $attachment = $this->db->query(" SELECT srp_erp_documentattachments.attachmentID, srp_erp_documentattachments.documentID, 'Estimate' AS documentName,srp_erp_documentattachments.attachmentDescription, srp_erp_documentattachments.myFileName, srp_erp_documentattachments.fileSize, srp_erp_documentattachments.fileType,srp_erp_documentattachments.documentSystemCode, pulled.attachmentID AS pulledID
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'EST' AND srp_erp_documentattachments.documentSystemCode IN ($selectedestimateMasterID)
            UNION SELECT srp_erp_documentattachments.attachmentID, srp_erp_documentattachments.documentID, 'Customer Inquiry' AS documentName, srp_erp_documentattachments.attachmentDescription, srp_erp_documentattachments.myFileName, srp_erp_documentattachments.fileSize, srp_erp_documentattachments.fileType, srp_erp_documentattachments.documentSystemCode, pulled.attachmentID AS pulledID
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'CI' AND srp_erp_documentattachments.documentSystemCode IN ($selectedciMasterID)
            UNION SELECT srp_erp_documentattachments.attachmentID, srp_erp_documentattachments.documentID, 'Job' AS documentName, srp_erp_documentattachments.attachmentDescription, srp_erp_documentattachments.myFileName, srp_erp_documentattachments.fileSize, srp_erp_documentattachments.fileType, srp_erp_documentattachments.documentSystemCode, pulled.attachmentID AS pulledID
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'MFQ_JOB' AND srp_erp_documentattachments.documentSystemCode IN ($selectedJobs)
            UNION SELECT srp_erp_documentattachments.attachmentID, srp_erp_documentattachments.documentID, 'Delivery Note' AS documentName, srp_erp_documentattachments.attachmentDescription, srp_erp_documentattachments.myFileName, srp_erp_documentattachments.fileSize, srp_erp_documentattachments.fileType, srp_erp_documentattachments.documentSystemCode, pulled.attachmentID AS pulledID
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'DN' AND srp_erp_documentattachments.documentSystemCode IN ($deliveryNoteID)")->result_array();

        $this->load->library('s3');
        foreach ($attachment as $key => $att) {
            $attachment[$key]['link'] = $this->s3->createPresignedRequest($att['myFileName'], '1 hour');
        }
        return $attachment;
    }

    function save_attachment_for_invoice()
    {
        $companyID = current_companyID();
        $attachmentID = $this->input->post('attachmentID');
        $invoiceID = $this->input->post('invoiceID');

        $attachmentIDs = "attachmentID IN (" . implode(',', $attachmentID) . ")";
        $this->db->query("DELETE FROM srp_erp_documentattachments WHERE documentSystemCode = {$invoiceID} AND (documentID LIKE '%MCINV_%') AND documentSubID IS NOT NULL");

        $this->db->select('*');
        $this->db->from('srp_erp_documentattachments');
        $this->db->where($attachmentIDs);
        $attach = $this->db->get()->result_array();

        foreach ($attach AS $att) {
            $data['documentID'] = 'MCINV_' . $att['documentID'];
            $data['documentSystemCode'] = trim($invoiceID);
            $data['documentSubID'] = $att['documentSystemCode'];
            $data['attachmentDescription'] = $att['attachmentDescription'];
            $data['myFileName'] = $att['myFileName'];
            $data['fileType'] = $att['fileType'];
            $data['fileSize'] = $att['fileSize'];
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $companyID;
            $data['companyCode'] = current_companyCode();
            $data['createdUserGroup'] = current_user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = current_date();
            $this->db->insert('srp_erp_documentattachments', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return (array('status' => 0, 'type' => 'e', 'message' => 'Attachments Merge Failed! ' . $this->db->_error_message()));
        } else {
            $this->db->trans_commit();
            return (array('status' => 1, 'type' => 's', 'message' => 'Attachments Merged Successfully!'));
        }

    }

    function fetch_customer_invoice_attachment_print()
    {
        $this->db->where('documentSystemCode', $this->input->post('invoiceAutoID'));
        $this->db->where("documentID LIKE '%MCINV%'");
        $this->db->where('companyID', current_companyID());
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        $confirmedYN = $this->input->post('confirmedYN');
        $result = '';
        $x = 1;
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
                //$link = generate_encrypt_link_only($burl); // old attachment
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
                if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                    $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments_mfq(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\',\'' . $val['documentID'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
                } else {
                    $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                }
                $x++;
            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">No Attachment Found</td></tr>';
        }
        return ($result);
    }

    function delete_attachments_mcinv()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $documentID = $this->input->post('documentID');

        $result = 1;
        if ($documentID == 'MCINV') {
            /**AWS S3 delete object */
            $result = $this->s3->delete($myFileName);
            /** end of AWS s3 delete object */
        }
        if ($result) {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        } else {
            return false;
        }
    }

    function duplicate_attachments_to_erp_invoice($mfqInvoiceAutoID, $invoiceAutoID)
    {
        /** MCINV added attachment Duplication*/
        $companyID = current_companyID();
        $attachments = $this->db->query("SELECT * FROM srp_erp_documentattachments WHERE companyID = {$companyID} AND documentID  = 'MCINV' AND documentSystemCode = {$mfqInvoiceAutoID}")->result_array();

        if ($attachments) {
            foreach ($attachments as $att) {
                $att_array['documentID'] = 'CINV';
                $att_array['documentSubID'] = $mfqInvoiceAutoID;
                $att_array['documentSystemCode'] = $invoiceAutoID;
                $att_array['attachmentDescription'] = $att['attachmentDescription'];
                $att_array['myFileName'] = $att['myFileName'];
                $att_array['fileType'] = $att['fileType'];
                $att_array['fileSize'] = $att['fileSize'];
                $att_array['companyID'] = $att['companyID'];
                $att_array['companyCode'] = $att['companyCode'];
                $att_array['createdUserGroup'] = $att['createdUserGroup'];
                $att_array['createdPCID'] = $att['createdPCID'];
                $att_array['createdUserID'] = $att['createdUserID'];
                $att_array['createdDateTime'] = $att['createdDateTime'];
                $att_array['createdUserName'] = $att['createdUserName'];

                $this->db->insert('srp_erp_documentattachments', $att_array);
            }
        }
        /** End of MCINV added attachment Duplication*/

        /** Linked Document attachment Duplication*/
        $deliveryNoteID = $this->db->query("SELECT deliveryNoteID FROM `srp_erp_mfq_customerinvoicemaster` WHERE invoiceAutoID = {$mfqInvoiceAutoID}")->row('deliveryNoteID');
        $jobIDs = $this->db->query("SELECT workProcessID FROM srp_erp_mfq_job JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID WHERE srp_erp_mfq_deliverynotedetail.deliveryNoteID = {$deliveryNoteID} GROUP BY workProcessID")->result_array();
        $jobID = array_unique(array_column($jobIDs, 'workProcessID'));
        $selectedJobs = implode(",", $jobID);

        $estimateMasterIDs = $this->db->query("SELECT srp_erp_mfq_estimatemaster.estimateMasterID AS estimateMasterID FROM srp_erp_mfq_estimatemaster join srp_erp_mfq_job ON srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID WHERE workProcessID IN ({$selectedJobs}) GROUP BY srp_erp_mfq_estimatemaster.estimateMasterID")->result_array();
        $estimateMasterID = array_unique(array_column($estimateMasterIDs, 'estimateMasterID'));
        $selectedestimateMasterID = implode(",", $estimateMasterID);

        $inquiries = $this->db->query("SELECT ciMasterID FROM srp_erp_mfq_estimatedetail WHERE estimateMasterID IN ($selectedestimateMasterID) GROUP BY ciMasterID ")->result_array();
        $mfqInvoiceID = array_unique(array_column($inquiries, 'ciMasterID'));
        $selectedciMasterID = implode(",", $mfqInvoiceID);

        $pulledAttachment = $this->db->query(" SELECT srp_erp_documentattachments.*
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'EST' AND srp_erp_documentattachments.documentSystemCode IN ($selectedestimateMasterID)
            UNION SELECT srp_erp_documentattachments.*
                FROM srp_erp_documentattachments 
                LEFT JOIN srp_erp_documentattachments pulled ON pulled.documentSubID = srp_erp_documentattachments.documentSystemCode AND pulled.myFileName = srp_erp_documentattachments.myFileName
                WHERE srp_erp_documentattachments.documentID = 'CI' AND srp_erp_documentattachments.documentSystemCode IN ($selectedciMasterID)")->result_array();

        $this->db->query("DELETE FROM srp_erp_documentattachments WHERE documentSystemCode = {$mfqInvoiceAutoID} AND (documentID LIKE '%MCINV_%') AND documentSubID IS NOT NULL");
        if ($pulledAttachment) {
            $att_array = array();
            $data = array();
            foreach ($pulledAttachment as $att) {
                /** Link to ERP invoice*/
                $att_array['documentID'] = 'CINV';
                $att_array['documentSubID'] = $mfqInvoiceAutoID;
                $att_array['documentSystemCode'] = $invoiceAutoID;
                $att_array['attachmentDescription'] = $att['attachmentDescription'];
                $att_array['myFileName'] = $att['myFileName'];
                $att_array['fileType'] = $att['fileType'];
                $att_array['fileSize'] = $att['fileSize'];
                $att_array['companyID'] = $att['companyID'];
                $att_array['companyCode'] = $att['companyCode'];
                $att_array['createdUserGroup'] = $att['createdUserGroup'];
                $att_array['createdPCID'] = $att['createdPCID'];
                $att_array['createdUserID'] = $att['createdUserID'];
                $att_array['createdDateTime'] = $att['createdDateTime'];
                $att_array['createdUserName'] = $att['createdUserName'];
                $this->db->insert('srp_erp_documentattachments', $att_array);

                /** Link to MFQ invoice*/
                $data['documentID'] = 'MCINV_' . $att['documentID'];
                $data['documentSystemCode'] = trim($mfqInvoiceAutoID);
                $data['documentSubID'] = $att['documentSystemCode'];
                $data['attachmentDescription'] = $att['attachmentDescription'];
                $data['myFileName'] = $att['myFileName'];
                $data['fileType'] = $att['fileType'];
                $data['fileSize'] = $att['fileSize'];
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $att['companyID'];
                $data['companyCode'] = $att['companyCode'];
                $data['createdUserGroup'] = $att['createdUserGroup'];
                $data['createdPCID'] = $att['createdPCID'];
                $data['createdUserID'] = $att['createdUserID'];
                $data['createdDateTime'] = $att['createdDateTime'];
                $data['createdUserName'] = $att['createdUserName'];
                $this->db->insert('srp_erp_documentattachments', $data);
            }
        }
        /** End of Linked Document attachment Duplication*/
    }

    function fetch_customer_invoice_details()
    {
        $companyID = current_companyID();
        $data = array();
        $where = '';
        $convertFormat = convert_date_format_sql();
        $customercode = $this->input->post('customerCode');
        $segmentID = $this->input->post('SegmentID');
        $segmentallSelected = $this->input->post('segmentallSelected');
        if ($customercode) {
            $where .= " AND srp_erp_mfq_customerinvoicemaster.mfqCustomerAutoID IN (" . join(',', $customercode) . ")";
        }

        if ($segmentID && $segmentallSelected != 1) {
            $where .= " AND srp_erp_mfq_customerinvoicemaster.mfqSegmentID IN (" . join(',', $segmentID) . ")";
        }

        $result = $this->db->query("SELECT
	invoiceCode,
	DATE_FORMAT( invoiceDate, '{$convertFormat}' ) AS invoiceDate,
	DATE_FORMAT( invoiceDueDate, '{$convertFormat}' ) AS invoiceDueDate,
	invoiceNarration,
	srp_erp_mfq_customermaster.CustomerName AS customerName,
	srp_erp_mfq_customerinvoicemaster.approvedYN,
	srp_erp_mfq_customerinvoicemaster.confirmedYN AS confirmedYN,
	det.transactionAmount AS transactionAmount,
	transactionCurrencyDecimalPlaces,
	transactionCurrency,
	srp_erp_mfq_customerinvoicemaster.invoiceAutoID AS invoiceAutoID,
	srp_erp_currencymaster.CurrencyCode AS CurrencyCode,
	IFNULL( mfqsegment.segmentCode, '-' ) AS segmentcode 
FROM
	`srp_erp_mfq_customerinvoicemaster`
	LEFT JOIN `srp_erp_mfq_deliverynote` `deliverynote` ON `deliverynote`.`deliverNoteID` = `srp_erp_mfq_customerinvoicemaster`.`deliveryNoteID`
	LEFT JOIN `srp_erp_mfq_segment` `mfqsegment` ON `mfqsegment`.`mfqSegmentID` = `srp_erp_mfq_customerinvoicemaster`.`mfqSegmentID`
	LEFT JOIN `srp_erp_mfq_customermaster` ON `srp_erp_mfq_customermaster`.`mfqCustomerAutoID` = `srp_erp_mfq_customerinvoicemaster`.`mfqCustomerAutoID`
	LEFT JOIN `srp_erp_currencymaster` ON `srp_erp_currencymaster`.`currencyID` = `srp_erp_mfq_customerinvoicemaster`.`transactionCurrencyID`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, invoiceAutoID FROM srp_erp_mfq_customerinvoicedetails GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` = srp_erp_mfq_customerinvoicemaster.invoiceAutoID ) 
WHERE
	`srp_erp_mfq_customerinvoicemaster`.`companyID` = {$companyID} {$where}
ORDER BY
	`invoiceAutoID` DESC")->result_array();

        if ($result) {
            $a = 1;
            foreach ($result AS $val) {
                $det['recordNo'] = $a;
                $det['invoiceCode'] = $val['invoiceCode'];
                $det['invoiceDate'] = $val['invoiceDate'];
                $det['invoiceDueDate'] = $val['invoiceDueDate'];
                $det['customerName'] = $val['customerName'];
                $det['invoiceNarration'] = str_replace('<br>', '     ', $val['invoiceNarration']);
                $det['segmentcode'] = $val['segmentcode'];
                $det['CurrencyCode'] = $val['CurrencyCode'];
                $det['transactionAmount'] = number_format($val['transactionAmount'], $val['transactionCurrencyDecimalPlaces'], '.', '');
                if ($val['confirmedYN'] == 1) {
                    $det['confirmedYN'] = 'Confirmed';
                } else {
                    $det['confirmedYN'] = 'Not Confirmed';
                }
                $a++;
                array_push($data, $det);
            }
        }

        return $data;
    }

    function upload_attachment_for_invoice()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
        $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }
            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }
            /** end of s3 integration */

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $companyID;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_documentattachments', $data);


            $mfqInvoiceAutoID = trim($this->input->post('documentSystemCode') ?? '');
            $invoiceAutoID = $this->db->query("SELECT invoiceAutoID FROM srp_erp_customerinvoicemaster WHERE companyID = {$companyID} AND mfqInvoiceAutoID = {$mfqInvoiceAutoID}")->row('invoiceAutoID');
            if ($invoiceAutoID) {
                $data['documentID'] = 'CINV';
                $data['documentSystemCode'] = trim($invoiceAutoID);
                $data['documentSubID'] = trim($this->input->post('documentSystemCode') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                $data['fileType'] = trim($ext);
                $data['fileSize'] = trim($file["size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $companyID;
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_documentattachments', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload);
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload);
            }
        }
    }

    function referback_customer_invoice()
    {
        $mfqInvoiceAutoID = $this->input->post('mfqInvoiceAutoID', true);
        $erpInvoiceQuery = $this->db->query("select * from srp_erp_customerinvoicemaster where mfqInvoiceAutoID=$mfqInvoiceAutoID");
        if ($erpInvoiceQuery->num_rows() > 0) {
            $result = $erpInvoiceQuery->result_array();
            $invoiceIDs = implode(',', array_column($result, 'invoiceAutoID'));
            if (in_array(1, array_column($result, 'approvedYN'))) {
                return array('e', 'You cannot referback this document. ERP document has been approved.');
            /*}
            $erpInvIsApproved = $erpInvoiceQuery->row('approvedYN');
            $erpInvoiceAutoID = $erpInvoiceQuery->row('invoiceAutoID');
            if ($erpInvIsApproved == 1) {
                return array('e', 'You cannot referback this document. ERP document has been approved.');*/
            } else {
                $this->db->trans_start();

//                $this->db->where('invoiceAutoID IN (' . $invoiceIDs . ')');
//                $this->db->delete('srp_erp_customerinvoicedetails');//Removing record from srp_erp_customerinvoicedetails.
                $erpRecordUpdate = array(
                    "confirmedYN" => 0,
                    "confirmedByEmpID" => '',
                    "confirmedByName" => '',
                    "confirmedDate" => '',
                    "approvedYN" => 0,
                    "isDeleted" => 1,
                    "deletedEmpID" => $this->common_data['current_userID'],
                    "deletedDate" => $this->common_data['current_date']
                );
                $this->db->where('invoiceAutoID IN (' . $invoiceIDs . ')');
                $this->db->update('srp_erp_customerinvoicemaster', $erpRecordUpdate); //referback erp invoice record.
//                $documentApprovedDelete = array(
//                    "documentID" => "CINV",
//                    "documentsystemcode" => $erpInvoiceAutoID
//                );
//                $this->db->delete('srp_erp_documentapproved', $documentApprovedDelete);//Removing record created in srp_erp_documentapproved.
                $this->db->where('documentID', 'CINV');
                $this->db->where_in('documentsystemcode', $invoiceIDs);
                $this->db->delete('srp_erp_documentapproved');//Removing record created in srp_erp_documentapproved.

                $mfqRecordUpdate = array(
                    "confirmedYN" => 0,
                    "confirmedByEmpID" => '',
                    "confirmedByName" => '',
                    "confirmedDate" => ''
                );
                $this->db->where('invoiceAutoID', $mfqInvoiceAutoID);
                $this->db->update('srp_erp_mfq_customerinvoicemaster', $mfqRecordUpdate);//refer back mfq invoice record.
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', ' Error in refer back.');
                } else {
                    $this->db->trans_commit();
                    return array('s', ' Referred Back Successfully.');
                }
            }
        } else {
            $mfqRecordUpdate = array(
                "confirmedYN" => 0,
                "confirmedByEmpID" => '',
                "confirmedByName" => '',
                "confirmedDate" => ''
            );
            $this->db->where('invoiceAutoID', $mfqInvoiceAutoID);
            $status = $this->db->update('srp_erp_mfq_customerinvoicemaster', $mfqRecordUpdate);
            if ($status) {
                return array('s', ' Referred Back Successfully.');
            } else {
                return array('e', ' Error in refer back.');
            }
        }
    }

    function load_line_tax_amount()
    {
        $applicableAmnt=$this->input->post('applicableAmnt');
        $invoiceAutoID=$this->input->post('invoiceAutoID');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'MCINV',$invoiceAutoID);
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $amnt = 0;
        } else {
            $amnt = $return['amount'];
        }
        return $amnt;
    }
}