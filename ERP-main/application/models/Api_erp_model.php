<?php

class Api_erp_model extends ERP_Model
{

    function get_default_segment($companyID)
    {
        $CI =& get_instance();
        $CI->db->select("default_segment_id");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $companyID);
        return $CI->db->get()->row('default_segment_id');
    }

    function get_currency_code($currencyID)
    {
        $CI =& get_instance();
        $CI->db->select("CurrencyCode");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('currencyID', $currencyID);
        return $CI->db->get()->row('CurrencyCode');

    }

    function get_segment_code($segmentID)
    {
        $CI =& get_instance();
        $CI->db->select("segmentCode");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('segmentID', $segmentID);
        return $CI->db->get()->row('segmentCode');

    }

    function payment_voucher_bank_ledger($double_entry)
    {
        $this->load->helper('payable');
        $amount = payment_voucher_total_value($double_entry['master_data']['payVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);
        $bankledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
        $bankledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
        $bankledger_arr['transactionType'] = 2;
        $bankledger_arr['bankName'] = $double_entry['master_data']['PVbank'];
        $bankledger_arr['bankGLAutoID'] = $double_entry['master_data']['bankGLAutoID'];
        $bankledger_arr['bankSystemAccountCode'] = $double_entry['master_data']['bankSystemAccountCode'];
        $bankledger_arr['bankGLSecondaryCode'] = $double_entry['master_data']['bankGLSecondaryCode'];
        $bankledger_arr['documentType'] = 'PV';
        $bankledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
        $bankledger_arr['modeofPayment'] = $double_entry['master_data']['modeOfPayment'];
        $bankledger_arr['chequeNo'] = $double_entry['master_data']['PVchequeNo'];
        $bankledger_arr['chequeDate'] = $double_entry['master_data']['PVchequeDate'];
        $bankledger_arr['memo'] = $double_entry['master_data']['PVNarration'];
        $bankledger_arr['partyType'] = $double_entry['master_data']['partyType'];
        $bankledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
        $bankledger_arr['partyCode'] = $double_entry['master_data']['partyCode'];
        $bankledger_arr['partyName'] = $double_entry['master_data']['partyName'];
        $bankledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
        $bankledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
        $bankledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
        $bankledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
        $bankledger_arr['transactionAmount'] = $amount;
        $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
        $bankledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
        $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
        $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
        $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
        $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
        $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
        $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
        $bankledger_arr['bankCurrencyAmount'] = $bankledger_arr['bankCurrencyExchangeRate'] == 0 ? 0 : ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
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

        return $this->db->insert('srp_erp_bankledger', $bankledger_arr);
    }

    function payment_voucher_general_ledger($double_entry)
    {
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['PVcode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['PVdate'];
            $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['pvType'];
            $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['PVdate'];
            $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
            $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['PVNarration'];
            $generalledger_arr[$i]['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
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

    }

    function customer_invoice_general_ledger($double_entry)
    {

        $generalledger_arr = [];
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['invoiceAutoID'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['invoiceCode'];//$generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['bookingInvCode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['invoiceDate'];
            $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['invoiceType'];
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
                $amount = (abs($double_entry['gl_detail'][$i]['gl_cr']) * -1);
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
        return $generalledger_arr;
    }

    function invoice_general_ledger($double_entry)
    {
        $generalledger_arr = [];
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
                $amount = (abs($double_entry['gl_detail'][$i]['gl_cr']) * -1);
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
        return $generalledger_arr;
    }

    function invoice_general_ledger_tmp($double_entry)
    {
        $generalledger_arr = [];
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


        return $generalledger_arr;
    }

    function receipt_double_entry($double_entry)
    {
        $this->load->helper('receivable');
        $system_id = $double_entry['master_data']['receiptVoucherAutoId'];
        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['RVcode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['RVdate'];
            $generalledger_arr[$i]['acknowledgementDate'] = $double_entry['master_data']['RVdate'];
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
            $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['partyCurrencyAmount'] = $generalledger_arr[$i]['partyExchangeRate'] > 0 ? round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']) : 0;
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
        $bankledger_arr['transactionAmount'] = $amount;
        $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
        $bankledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
        $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
        $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
        $bankledger_arr['partyCurrencyAmount'] = $bankledger_arr['partyCurrencyExchangeRate'] == 0 ? 0 : ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
        $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
        $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
        $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
        $bankledger_arr['bankCurrencyAmount'] = $bankledger_arr['bankCurrencyExchangeRate'] == 0 ? 0 : ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
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
                $generalledger_arr['acknowledgementDate'] = $double_entry['master_data']['RVdate'];
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
        $data['transactionAmount'] = $total;
        $this->db->where('receiptVoucherAutoId', $system_id);
        $this->db->update('srp_erp_customerreceiptmaster', $data);
    }

    function fetch_gl_account_desc($company_id, $id)
    {
        $CI =& get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID IN (' . $id . ')');
        $CI->db->where('companyID', $company_id);
        $result = $CI->db->get()->row();
        return $result;
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row();
    }

    function fetch_customer_data($erp_customer_id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $erp_customer_id);
//        $this->db->get()->row();
//        var_dump($this->db->last_query());exit;
        return $this->db->get()->row();
    }

    function get_finance_year($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_companyfinanceyear');
        $this->db->where('companyFinanceYearID', $id);
        return $this->db->get()->row();
    }

    function get_finance_period($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_companyfinanceperiod');
        $this->db->where('companyFinancePeriodID', $id);
        return $this->db->get()->row();
    }


    function reversing_approval_document($post, $company_id, $current_userID, $current_user, $company_info)
    {
        $document_id = $post->document_id;
        $document_code = $post->document_code;
        $comments = $post->comments;

        $approved_data = null; // document approved table data
        $document_status = $this->current_document_status($document_code, $document_id);
        if ($document_status['status'] == 'A') {
            return array('type' => 'error', 'status' => 500, 'message' => 'This document can not be reversed, bank reconciliation already done | Code : ' . $document_status['data'][0]['system_code']);
        }
        $this->db->trans_start();

        $this->db->select('itemAutoID,transactionQTY,convertionRate,wareHouseAutoID,companyLocalAmount,documentID');
        $this->db->from('srp_erp_itemledger');
        $this->db->where('documentAutoID', $document_id);
        $this->db->where('documentID', $document_code);
        $this->db->where('companyID', $company_id);
        $item_ledger_data = $this->db->get()->result_array();

        if (!empty($item_ledger_data)) {
            foreach ($item_ledger_data as $value) {
                $ledger_qty = ($value['transactionQTY'] / $value['convertionRate']);
                $this->reversing_wac_calculation($value['itemAutoID'], $ledger_qty, $value['companyLocalAmount'], $value['wareHouseAutoID'], 1, $company_info);
            }
        }

        $this->db->delete('srp_erp_itemledger', array('documentAutoID' => $document_id, 'documentID' => $document_code, 'companyID' => $company_id));
        $this->db->delete('srp_erp_generalledger', array('documentMasterAutoID' => $document_id, 'documentCode' => $document_code, 'companyID' => $company_id));
        $this->db->delete('srp_erp_property_documents', array('document_id' => $document_id, 'document_code' => $document_code, 'company_id' => $company_id));

        if ($document_code == 'RV') {
            $this->db->where('receiptVoucherAutoId', $document_id);
            $update_data = array(
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'confirmedByEmpID' => null,
                'approvedbyEmpID' => null,
                'confirmedByName' => null,
                'approvedbyEmpName' => null,
                'confirmedDate' => null,
                'approvedDate' => null,
                'currentLevelNo' => 1,
                'isDeleted' => 1,
                'deletedEmpID' => $current_userID,
                'deletedDate' => date('Y-m-d H:i:s'),
                'referanceNo' =>''
            );
            $this->db->update('srp_erp_customerreceiptmaster', $update_data);
            $this->db->delete('srp_erp_customerreceiptdetail', array('receiptVoucherAutoId' => $document_id));
        } else if ($document_code == 'PV') {
            $this->db->where('payVoucherAutoId', $document_id);
            $update_data = array(
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'confirmedByEmpID' => null,
                'approvedbyEmpID' => null,
                'confirmedByName' => null,
                'approvedbyEmpName' => null,
                'confirmedDate' => null,
                'approvedDate' => null,
                'currentLevelNo' => 1,
                'isDeleted' => 1,
                'deletedEmpID' => $current_userID,
                'deletedDate' => date('Y-m-d H:i:s'),
                'referenceNo' => ''
            );
            $this->db->update('srp_erp_paymentvouchermaster', $update_data);
            $this->db->delete('srp_erp_paymentvoucherdetail', array('payVoucherAutoId' => $document_id));
        } else if ($document_code == 'BSI') {
            $this->db->where('InvoiceAutoID', $document_id);
            $update_data = array(
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'confirmedByEmpID' => null,
                'approvedbyEmpID' => null,
                'confirmedByName' => null,
                'approvedbyEmpName' => null,
                'confirmedDate' => null,
                'approvedDate' => null,
                'currentLevelNo' => 1,
                'isDeleted' => 1,
                'deletedEmpID' => $current_userID,
                'deletedDate' => date('Y-m-d H:i:s'),
                'RefNo' => ''
            );
            $this->db->update('srp_erp_paysupplierinvoicemaster', $update_data);
            $this->db->delete('srp_erp_paysupplierinvoicedetail', array('InvoiceAutoID' => $document_id));
        } else if ($document_code == 'CINV') {
            $this->db->where('InvoiceAutoID', $document_id);
            $update_data = array(
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'confirmedByEmpID' => null,
                'approvedbyEmpID' => null,
                'confirmedByName' => null,
                'approvedbyEmpName' => null,
                'confirmedDate' => null,
                'approvedDate' => null,
                'currentLevelNo' => 1,
                'isDeleted' => 1,
                'deletedEmpID' => $current_userID,
                'deletedDate' => date('Y-m-d H:i:s'),
                'referenceNo'=>''
            );
            $this->db->update('srp_erp_customerinvoicemaster', $update_data);
            $this->db->delete('srp_erp_customerinvoicedetails', array('invoiceAutoID' => $document_id));
        } else if ($document_code == 'JV') {
            $this->db->where('JVMasterAutoId', $document_id);
            $update_data = array(
                'confirmedYN' => 0,
                'approvedYN' => 0,
                'confirmedByEmpID' => null,
                'approvedbyEmpID' => null,
                'confirmedByName' => null,
                'approvedbyEmpName' => null,
                'confirmedDate' => null,
                'approvedDate' => null,
                'currentLevelNo' => 1,
                'isDeleted' => 1,
                'deletedEmpID' => $current_userID,
                'deletedDate' => date('Y-m-d H:i:s'),
                'referenceNo'=>''
            );
            $this->db->update('srp_erp_jvmaster', $update_data);
            $this->db->delete('srp_erp_jvdetail', array('JVMasterAutoId' => $document_id));
        }


        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $document_id, 'documentID' => $document_code, 'companyID' => $company_id));
        $this->db->delete('srp_erp_bankledger', array('documentMasterAutoID' => $document_id, 'documentType' => $document_code, 'companyID' => $company_id));


        /*buyback batch close reversing*/
        $data_reversing['documentMasterAutoID'] = $document_id;
        $data_reversing['documentID'] = $document_code;
        $data_reversing['reversedDate'] = current_date();
        $data_reversing['reversedEmpID'] = $current_userID;
        $data_reversing['reversedEmployee'] = $current_user;
        $data_reversing['comments'] = $comments;
        $data_reversing['companyID'] = $company_id;
        $this->db->insert('srp_erp_documentapprovedreversing', $data_reversing);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('type' => 'error', 'status' => 500, 'message' => 'An error has occurred, please try again!');
        } else {
            $this->db->trans_commit();
            return array('type' => 'success', 'status' => 200, 'message' => 'document reversed successfully', 'data' => null);
        }


    }

    private function reversing_wac_calculation($itemAutoID, $defoult_qty, $total_value = 0, $wareHouseID = 0, $is_minimum = 0, $company_info)
    {
        $CI =& get_instance();
        $com_currency = $company_info->company_default_currency;
        $com_currDPlace = $company_info->company_default_decimal;
        $rep_currency = $company_info->company_reporting_currency;
        $rep_currDPlace = $company_info->company_reporting_decimal;

        $item_current_data = $CI->db->select('itemSystemCode,currentStock,defaultUnitOfMeasure,companyLocalWacAmount as current_wac')->from('srp_erp_itemmaster')->where('itemAutoID', $itemAutoID)->get()->row();

        if ($is_minimum == 1) {
            $defoult_qty *= -1;
            $total_value *= -1;
            $document_total = $total_value;// * $defoult_qty;
        } else {
            $document_total = $total_value; //* $defoult_qty;//$item_current_data->current_wac * $defoult_qty;
        }

        $newQty = $item_current_data->currentStock + $defoult_qty;
        $currentTot = $item_current_data->current_wac * $item_current_data->currentStock;
        $newTot = $currentTot + $document_total;
        $newWac = round(($newTot / $newQty), $com_currDPlace);
        $reportConversion = currency_conversion($com_currency, $rep_currency, $newWac);
        $reportConversionRate = $reportConversion['conversion'];
        $repWac = round(($newWac / $reportConversionRate), $rep_currDPlace);

        $data = array('currentStock' => $newQty, 'companyLocalWacAmount' => $newWac, 'companyReportingWacAmount' => $repWac);
        $where = array('itemAutoID' => $itemAutoID, 'companyID' => current_companyID());
        $CI->db->where($where)->update('srp_erp_itemmaster', $data);

        if (isset($wareHouseID)) {
            $CI->db->query("UPDATE srp_erp_warehouseitems SET currentStock=(currentStock+{$defoult_qty}) WHERE itemAutoID={$itemAutoID} AND wareHouseAutoID={$wareHouseID}");
        }
        return true;
    }

    private function current_document_status($document_code, $document_id)
    {
        $document = array();
        if ($document_code == 'PO') {
            $this->db->select('srp_erp_grvmaster.grvAutoID as auto_id, srp_erp_grvmaster.grvPrimaryCode as system_code');
            $this->db->group_by("srp_erp_grvmaster.grvAutoID");
            $this->db->from('srp_erp_grvdetails');
            $this->db->join('srp_erp_grvmaster', 'srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID');
            $this->db->where('purchaseOrderMastertID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id,PVcode as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('type', 'Advance');
            $this->db->where('purchaseOrderID', $document_id);
            $rev_data_arr += $this->db->get()->result_array();


        } elseif ($document_code == 'GRV') {
            $this->db->select('srp_erp_paysupplierinvoicemaster.InvoiceAutoID as auto_id,bookingInvCode as system_code');
            $this->db->group_by("srp_erp_paysupplierinvoicemaster.InvoiceAutoID");
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $this->db->where('grvAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_stockreturnmaster.stockReturnAutoID as auto_id,stockReturnCode as system_code');
            $this->db->group_by("srp_erp_stockreturnmaster.stockReturnAutoID");
            $this->db->from('srp_erp_stockreturndetails');
            $this->db->join('srp_erp_stockreturnmaster', 'srp_erp_stockreturnmaster.stockReturnAutoID = srp_erp_stockreturndetails.stockReturnAutoID');
            $this->db->where('type', 'GRV');
            $this->db->where('grvAutoID', $document_id);
            $rev_data_arr += $this->db->get()->result_array();

        } elseif ($document_code == 'BSI') {
            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id, srp_erp_paymentvouchermaster.PVcode as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('InvoiceAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
            $this->db->select('srp_erp_debitnotemaster.debitNoteMasterAutoID as auto_id, srp_erp_debitnotemaster.debitNoteCode as system_code');
            $this->db->group_by("srp_erp_debitnotemaster.debitNoteMasterAutoID");
            $this->db->from('srp_erp_debitnotedetail');
            $this->db->join('srp_erp_debitnotemaster', 'srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_debitnotedetail.debitNoteMasterAutoID');
            $this->db->where('InvoiceAutoID', $document_id);
            $rev_data_arr += $this->db->get()->result_array();
        } elseif ($document_code == 'PV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType', 'PV');
            $this->db->where('clearedYN', 1);
            $this->db->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankRecAutoID=srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
        } elseif ($document_code == 'BT') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType', 'BT');
            $this->db->where('clearedYN', 1);
            $this->db->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankRecAutoID=srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
        } elseif ($document_code == 'RV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType', 'RV');
            $this->db->where('clearedYN', 1);
            $this->db->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankRecAutoID =srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
        } elseif ($document_code == 'CINV') {
            $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId as auto_id, srp_erp_customerreceiptmaster.RVcode as system_code');
            $this->db->group_by("srp_erp_customerreceiptmaster.receiptVoucherAutoId");
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->join('srp_erp_customerreceiptmaster', 'srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId');
            $this->db->where('invoiceAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as auto_id, srp_erp_creditnotemaster.creditNoteCode as system_code');
            $this->db->group_by("srp_erp_creditnotemaster.creditNoteMasterAutoID");
            $this->db->from('srp_erp_creditnotedetail');
            $this->db->join('srp_erp_creditnotemaster', 'srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID');
            $this->db->where('invoiceAutoID', $document_id);
            $rev_data_arr += $this->db->get()->result_array();
        } else if ($document_code == 'PRQ') {
            $this->db->select('srp_erp_purchaseordermaster.purchaseOrderID as auto_id,IFNULL(purchaseOrderCode,0) as system_code');
            $this->db->group_by("srp_erp_purchaseordermaster.purchaseOrderID");
            $this->db->from('srp_erp_purchaseorderdetails');
            $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
            $this->db->where('prMasterID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();

            $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId as auto_id,IFNULL(srp_erp_paymentvouchermaster.PVcode,0)  as system_code');
            $this->db->group_by("srp_erp_paymentvouchermaster.payVoucherAutoId");
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_paymentvoucherdetail.payVoucherAutoId');
            $this->db->where('prMasterID', $document_id);
            $rev_data_arr += $this->db->get()->result_array();
        } elseif ($document_code == 'JV') {
            $this->db->select('bankLedgerAutoID as auto_id, srp_erp_bankrecmaster.bankRecPrimaryCode as system_code');
            $this->db->group_by("bankLedgerAutoID");
            $this->db->from('srp_erp_bankledger');
            $this->db->where('documentType', 'JV');
            $this->db->where('clearedYN', 1);
            $this->db->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankRecAutoID =srp_erp_bankledger.bankRecMonthID');
            $this->db->where('documentMasterAutoID', $document_id);
            $rev_data_arr = $this->db->get()->result_array();
        }

        if (empty($rev_data_arr)) {
            return array('status' => 'B');
        }
        return array('status' => 'A', 'data' => $rev_data_arr);
    }

    function sequence_generator($documentID, $company_id, $company_code, $user_id, $count = 0)
    {

        $CI = &get_instance();
        $code = '';
        $CI->db->select('documentID, prefix, serialNo,formatLength,format_1,format_2,format_3,format_4,format_5,format_6');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        $CI->db->where('companyID', $company_id);
        $data = $CI->db->get()->row_array();
        if (empty($data)) {
            $data_arr = [
                'documentID' => $documentID,
                'prefix' => $documentID,
                'companyID' => $company_id,
                'companyCode' => $company_code,
                'createdUserGroup' => 0,
                'createdUserID' => $user_id,
                'createdUserName' => 'API',
                'createdPCID' => 'API',
                'createdDateTime' => $CI->common_data['current_date'],
                'modifiedUserID' => $user_id,
                'modifiedUserName' => $CI->common_data['current_user'],
                'modifiedPCID' => $CI->common_data['current_pc'],
                'modifiedDateTime' => $CI->common_data['current_date'],
                'startSerialNo' => 1,
                'formatLength' => 6,
                'format_1' => 'prefix',
                'format_2' => NULL,
                'format_3' => NULL,
                'format_4' => NULL,
                'format_5' => NULL,
                'format_6' => NULL,
                'serialNo' => 1,
            ];

            $CI->db->insert('srp_erp_documentcodemaster', $data_arr);
            $data = $data_arr;
        } else {
            $CI->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo+1)  WHERE documentID='{$documentID}' AND companyID = '{$company_id}'");
            $data['serialNo'] = ($data['serialNo'] + 1);
        }
        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }

    function journal_entry_post_validation($post)
    {
        $JVType = $post->JVType;
        $referenceNo = $post->referenceNo;
        $JVdate = $post->JVdate;
        $transactionCurrencyID = $post->transactionCurrencyID;
        $JVNarration = $post->JVNarration;
        $currency_code = $post->currency_code;
        if ($JVType == "" || $referenceNo == "" || $JVdate == "" || $transactionCurrencyID == "" || $JVNarration == "" || $currency_code == "") {
            return array('status' => false, 'message' => 'Some fields are missing.');
        } else {
            return array('status' => true, 'message' => '');
        }
    }

    function save_journal_entry_header($post)
    {

        $res = $this->journal_entry_post_validation($post);
        if ($res['status'] == false) {
            return array('type' => 'error', 'status' => 500, 'message' => $res['message']);
            exit;
        }

        foreach ($post->detail as $det) {
            if(empty($det->erp_segment_id)) {
                return array('type' => 'error', 'status' => 500, 'message' => 'Journal Voucher Detail Segment is missing.');
                exit;
            }
        }

        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $Jdates = $post->JVdate;
        $JVdate = input_format_date($Jdates, $date_format_policy);
        $financeYearDetails = get_financial_year($JVdate);
        if (empty($financeYearDetails)) {
            return array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date');
            exit;
        } else {
            $FYBegin = $financeYearDetails['beginingDate'];
            $FYEnd = $financeYearDetails['endingDate'];
        }
        $financePeriodDetails = get_financial_period_date_wise($JVdate);
        if (empty($financePeriodDetails)) {
            return array('type' => 'error', 'status' => 500, 'message' => 'Finance period not found for the selected document date');
            exit;
        } else {
            $PeriodBegin = $financePeriodDetails['dateFrom'];
            $PeriodEnd = $financePeriodDetails['dateTo'];
        }

        $glIDs = array();
        foreach ($post->detail as $detail) {
            $glIDs[] = $detail->gl_code;
        }
        $gl_status = $this->check_gl_status($glIDs);
        if(!empty($gl_status)) {
            return array('type' => 'error', 'status' => 500, 'message' => 'The linked ERP Chart of Account has been made inactive, activate it and try again.','data' => $gl_status);
            exit();
        }

        if (isset($post->referenceNo)) {
            $RVchequeNo = $this->db->query("SELECT COUNT(JVMasterAutoId) as isexistcount FROM srp_erp_jvmaster WHERE
			                                companyID = {$this->common_data['company_data']['company_id']} AND referenceNo = '{$post->referenceNo}' AND isDeleted=0")->row('isexistcount');
            if ($RVchequeNo > 0) {
                return array('type' => 'error', 'status' => 500, 'message' => 'Reference No already exist.');
                exit();
            }
        }

        $companyFinanceYear = $FYBegin . ' - ' . $FYEnd;
        $currency_code = explode('|', trim($post->currency_code));
        $data['documentID'] = 'JV';
        $data['JVType'] = trim($post->JVType);
        $data['JVdate'] = trim($JVdate);
        $data['JVNarration'] = trim_desc($post->JVNarration);
        $data['referenceNo'] = trim($post->referenceNo);

        $data['companyFinanceYearID'] = trim($financeYearDetails['companyFinanceYearID'] ?? '');
        $data['companyFinanceYear'] = trim($companyFinanceYear);
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($financePeriodDetails['companyFinancePeriodID'] ?? '');
        $data['FYPeriodDateFrom'] = trim($PeriodBegin);
        $data['FYPeriodDateTo'] = trim($PeriodEnd);

        $data['transactionCurrencyID'] = trim($post->transactionCurrencyID);
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
        //$this->load->library('sequence');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['JVcode'] = 0;//$this->sequence_generator('JV',$data['companyID'],$data['companyCode'],$this->common_data['current_userID']);
        /**confirmed*/
        $data['isSystemGenerated'] = 1;
        $data['confirmedYN'] = 1;
        $data['confirmedByEmpID'] = $this->common_data['current_userID'];
        $data['confirmedByName'] = $this->common_data['current_user'];
        $data['confirmedDate'] = $this->common_data['current_date'];
        /** Approval */
        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_jvmaster', $data);
        $JVMasterAutoId = $this->db->insert_id();
        $details = $post->detail;
        $debit_total = 0;
        $credit_total = 0;
        foreach ($details as $detail) {
            $debit_total += $detail->debitAmount;
            $credit_total += $detail->creditAmount;
        }
        if ($debit_total == $credit_total) {
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
            $this->db->where('JVMasterAutoId', $JVMasterAutoId);
            $master = $this->db->get('srp_erp_jvmaster')->row_array();
            $detail_dataset = array();
            $details = $post->detail;
            foreach ($details as $detail) {
                $gl_codes = $detail->gl_code;
                $debitAmount = $detail->debitAmount;
                $creditAmount = $detail->creditAmount;
                $descriptions = $detail->description;
                $segment_gls = $detail->erp_segment_id;
//                $segment = explode('|', $segment_gls);
                $gldata = fetch_gl_account_desc($gl_codes);
//                if ($gldata['masterCategory'] == 'PL') {
                    $data2['segmentID'] = trim($segment_gls);
                    if(!empty($segment_gls)) {
                        $data2['segmentCode'] = $this->Api_erp_model->get_segment_code($segment_gls);
                    }
//                } else {
//                    $data2['segmentID'] = null;
//                    $data2['segmentCode'] = null;
//                }
                $gl_details = $this->db->query("SELECT
    GLAutoID,
    systemAccountCode,
    GLSecondaryCode,
    GLDescription,   
    subCategory
FROM
    `srp_erp_chartofaccounts`
WHERE
    GLAutoID=$gl_codes")->row_array();
                $data2['JVMasterAutoId'] = trim($JVMasterAutoId);
                $data2['GLAutoID'] = $gl_codes;
                $data2['systemGLCode'] = trim($gl_details['systemAccountCode'] ?? '');
                $data2['GLCode'] = trim($gl_details['GLSecondaryCode'] ?? '');
                $data2['GLDescription'] = trim($gl_details['GLDescription'] ?? '');
                $data2['GLType'] = trim($gl_details['subCategory'] ?? '');
                if ($creditAmount > 0) {
                    $data2['gl_type'] = 'Cr';
                } else {
                    $data2['gl_type'] = 'Dr';
                }
                if ($data2['gl_type'] == 'Cr') {
                    $data2['creditAmount'] = round($creditAmount, $master['transactionCurrencyDecimalPlaces']);
                    $creditCompanyLocalAmount = $data2['creditAmount'] / $master['companyLocalExchangeRate'];
                    $data2['creditCompanyLocalAmount'] = round($creditCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $creditCompanyReportingAmount = $data2['creditAmount'] / $master['companyReportingExchangeRate'];
                    $data2['creditCompanyReportingAmount'] = round($creditCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    $data2['debitAmount'] = 0;
                    $data2['debitCompanyLocalAmount'] = 0;
                    $data2['debitCompanyReportingAmount'] = 0;
                    if ($gldata['isBank'] == 1) {
                        $data2['isBank'] = 1;
                        $data2['bankCurrencyID'] = $gldata['bankCurrencyID'];
                        $data2['bankCurrency'] = $gldata['bankCurrencyCode'];
                        $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                        $data2['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                        $data2['bankCurrencyAmount'] = $data2['creditAmount'] / $bank_currency['conversion'];
                    } else {
                        $data2['isBank'] = 0;
                        $data2['bankCurrencyID'] = null;
                        $data2['bankCurrency'] = null;
                        $data2['bankCurrencyExchangeRate'] = null;
                        $data2['bankCurrencyAmount'] = null;
                    }
                } else {
                    $data2['debitAmount'] = round($debitAmount, $master['transactionCurrencyDecimalPlaces']);
                    $debitCompanyLocalAmount = $data2['debitAmount'] / $master['companyLocalExchangeRate'];
                    $data2['debitCompanyLocalAmount'] = round($debitCompanyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
                    $debitCompanyReportingAmount = $data2['debitAmount'] / $master['companyReportingExchangeRate'];
                    $data2['debitCompanyReportingAmount'] = round($debitCompanyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
                    //updating the value as 0
                    $data2['creditAmount'] = 0;
                    $data2['creditCompanyLocalAmount'] = 0;
                    $data2['creditCompanyReportingAmount'] = 0;
                    if ($gldata['isBank'] == 1) {
                        $data2['isBank'] = 1;
                        $data2['bankCurrencyID'] = $gldata['bankCurrencyID'];
                        $data2['bankCurrency'] = $gldata['bankCurrencyCode'];
                        $bank_currency = currency_conversionID($master['transactionCurrencyID'], $gldata['bankCurrencyID']);
                        $data2['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                        $data2['bankCurrencyAmount'] = $data2['debitAmount'] / $bank_currency['conversion'];
                    } else {
                        $data2['isBank'] = 0;
                        $data2['bankCurrencyID'] = null;
                        $data2['bankCurrency'] = null;
                        $data2['bankCurrencyExchangeRate'] = null;
                        $data2['bankCurrencyAmount'] = null;
                    }
                }
                $data2['description'] = $descriptions;
                $data2['type'] = 'GL';
                $data2['companyCode'] = $this->common_data['company_data']['company_code'];
                $data2['companyID'] = $this->common_data['company_data']['company_id'];
                array_push($detail_dataset, $data2);
            }
            $this->db->insert_batch('srp_erp_jvdetail', $detail_dataset);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_complete();
                $this->db->trans_rollback();
                return array('type' => 'error', 'status' => 500, 'message' => 'DB Error');
                exit;
            } else {
                $locationwisecodegenerate = getPolicyValues('LDG', 'All');
                $this->db->select('documentID, JVcode,DATE_FORMAT(JVdate, "%Y") as invYear,DATE_FORMAT(JVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('JVMasterAutoId', trim($JVMasterAutoId));
                $this->db->from('srp_erp_jvmaster');
                $master_dt = $this->db->get()->row_array();
                $companyID = current_companyID();
                $currentuser = current_userID();
                $locationemp = $this->common_data['emplanglocationid'];
                $this->db->select('*');
                $this->db->where('JVMasterAutoId', trim($JVMasterAutoId));
                $detl = $this->db->get('srp_erp_jvdetail')->row_array();
                if (empty($detl)) {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    return array('type' => 'error', 'status' => 500, 'message' => 'JV Detail can not be empty');
                    exit;
                }
                $this->load->library('sequence');
                if ($master_dt['JVcode'] == "0") {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            $this->db->trans_complete();
                            $this->db->trans_rollback();
                            return array('type' => 'error', 'status' => 500, 'message' => 'Location is not assigned for current employee');
                            exit;
                        } else {
                            if ($locationemp != '') {
                                $jvcd = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $locationemp, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                $this->db->trans_complete();
                                $this->db->trans_rollback();
                                return array('type' => 'error', 'status' => 500, 'message' => 'Location is not assigned for current employee');
                                exit;
                            }
                        }

                    } else {
                        $jvcd = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }
                    $jvcd = array(
                        'JVcode' => $jvcd
                    );
                    $this->db->where('JVMasterAutoId', trim($JVMasterAutoId));
                    $this->db->update('srp_erp_jvmaster', $jvcd);
                }
                $approvals_status = $this->approvals->CreateApprovalWitoutEmailnotification($data['documentID'], $JVMasterAutoId, $jvcd['JVcode'], 'Journal Voucher', 'srp_erp_jvmaster', 'JVMasterAutoId', 1, $data['JVdate']);
                if ($approvals_status == 1) {
                    $approval_levels = $this->approvals->maxlevel('JV');
                    $number_of_levels = $approval_levels['levelNo'];
                    for ($i = 1; $i <= $number_of_levels; $i++) {
                        $this->approvals->approve_without_sending_email($JVMasterAutoId, $i, 1, '', $data['documentID']);
                    }
                    $this->journal_voucher_general_ledger($JVMasterAutoId);
                    $this->db->trans_complete();
                    $this->db->trans_commit();
                    $jv_master_data = $this->db->query("select * from srp_erp_jvmaster where JVMasterAutoId={$JVMasterAutoId}");
                    return array('type' => 'success', 'status' => 200, 'message' => 'Successfully inserted the journal voucher', 'data' => $jv_master_data->row_array());
                    exit;
                } else {
                    $this->db->trans_complete();
                    $this->db->trans_rollback();
                    return array('type' => 'error', 'status' => 500, 'message' => 'Auto approval failed.');
                    exit;
                }
            }
        } else {
            $this->db->trans_complete();
            $this->db->trans_rollback();
            return array('type' => 'error', 'status' => 500, 'message' => 'DB Error');
            exit;
        }
    }

    function check_gl_status($glIDs) {
        $companyID = $this->common_data['company_data']['company_id'];
        $data = array();
        foreach ($glIDs AS $glID)
        {
            $det = $this->db->query("SELECT GLAutoID, systemAccountCode, GLDescription FROM srp_erp_chartofaccounts WHERE companyID = {$companyID} AND GLAutoID = {$glID} AND isActive = 0 AND deletedYN != 1")->row_array();
            if(!empty($det)) {
                $data[] = $det;
            }
        }
        return $data;
    }

    function journal_voucher_general_ledger($JVMasterAutoId)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->load->model('Double_entry_model');
        $double_entry = $this->Double_entry_model->fetch_double_entry_journal_entry_data($JVMasterAutoId, 'JV');
        if (!empty($double_entry)) {
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
                $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                //$generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
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

            $JVDetails = $this->db->query("SELECT srp_erp_jvdetail.*,srp_erp_chartofaccounts.bankCurrencyID,srp_erp_chartofaccounts.bankCurrencyCode,srp_erp_chartofaccounts.bankCurrencyDecimalPlaces,srp_erp_chartofaccounts.isBank,srp_erp_chartofaccounts.bankName FROM srp_erp_jvdetail LEFT JOIN srp_erp_chartofaccounts ON srp_erp_jvdetail.GLAutoID = srp_erp_chartofaccounts.GLAutoID WHERE JVMasterAutoId = {$JVMasterAutoId} AND srp_erp_jvdetail.companyID = {$companyID}")->result_array();
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
    }

    function save_customer($this_post,$companyID)
    {
        $customercode = $this_post->customercode;
        $customerAutoID = $this_post->customerAutoID;
        $customerTelephone = trim($this_post->customerTelephone);

        $this->db->trans_start();
        $isactive = 0;
        if (!empty($this_post->isActive)) {
            $isactive = 1;
        }
        $liability = fetch_gl_account_desc(trim($this_post->receivableAccount));
        $currency_id=$this_post->customerCurrency;
        $currency_code = $this->db->query("select * from srp_erp_currencymaster where currencyID=$currency_id")->row()->CurrencyCode;
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this_post->customercode);
        $data['masterID'] = trim($this_post->masterID);
        if(!empty($data['masterID'])){
            $data['levelNo'] = 1;
        }else{
            $data['levelNo'] = null;
            $data['masterID'] = null;
        }

        $data['rebateGLAutoID'] = trim($this_post->rebateGL);
        $data['rebatePercentage'] = trim($this_post->rebatePercentage);

        $data['customerName'] = trim($this_post->customerName);
        $data['customerCountry'] = trim($this_post->customercountry);
        $data['customerTelephone'] = trim($this_post->customerTelephone);
        $data['customerEmail'] = trim($this_post->customerEmail);
        $data['customerUrl'] = trim($this_post->customerUrl);
        $data['customerFax'] = trim($this_post->customerFax);
        $data['customerAddress1'] = trim($this_post->customerAddress1);
        $data['customerAddress2'] = trim($this_post->customerAddress2);

        $data['IdCardNumber'] = trim($this_post->IdCardNumber);
        $data['partyCategoryID'] = trim($this_post->partyCategoryID);
        $data['receivableAutoID'] = $liability['GLAutoID'];
        $data['receivableSystemGLCode'] = $liability['systemAccountCode'];
        $data['receivableGLAccount'] = $liability['GLSecondaryCode'];
        $data['receivableDescription'] = $liability['GLDescription'];
        $data['receivableType'] = $liability['subCategory'];
        $data['customerCreditPeriod'] = trim($this_post->customerCreditPeriod);
        $data['customerCreditLimit'] = trim($this_post->customerCreditLimit);
        $data['externalProductID'] = trim($this_post->externalProductID);
        $data['externalPrimaryKey'] = trim($this_post->externalPrimaryKey);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($customerAutoID)) {
            $this->db->where('customerAutoID', trim($this_post->customerAutoID));
            $this->db->update('srp_erp_customermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('type' => 'error', 'status' => 500, 'message' => 'Update Failed.');
            } else {
                $this->db->trans_commit();
                return array('type' => 'success', 'status' => 200, 'message' => 'Updated Successfully.');
            }
        } else {
            $this->load->library('sequence');
            $data['customerCurrencyID'] = trim($this_post->customerCurrency);
            $data['customerCurrency'] = $currency_code;
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal($data['customerCurrency']);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['customerSystemCode'] = $this->sequence->sequence_generator('CUS');
            $this->db->insert('srp_erp_customermaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('type' => 'error', 'status' => 500, 'message' => 'Save Failed.');
            } else {
                $this->db->trans_commit();
                return array('type' => 'success', 'status' => 200, 'message' => 'Customer Created Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function save_supplier_master($this_post,$companyID)
    {
        $this->db->trans_start();
        $bankDet = array();
        $isactive = 0;
        $deleteByEmpID = null;
        $deletedDatetime = null;
        if (!empty($this_post->isActive)) {
            $isactive = 1;
        }
        if (!empty($this_post->deletedYN) && $this_post->deletedYN == 1) {
            $isactive = 0;
            $deleteByEmpID = $this->common_data['current_userID'];
            $deletedDatetime = $this->common_data['current_date'];
        }
        $currency_id=$this_post->supplierCurrency;
        $currency_code = $this->db->query("select * from srp_erp_currencymaster where currencyID=$currency_id")->row()->CurrencyCode;
        $liability = fetch_gl_account_desc(trim($this_post->liabilityAccount));


            if($this_post->nameOnCheque=="" || $this_post->nameOnCheque == null){
                $nameOnCheque = $this_post->supplierName;
            }

        $data['deletedYN'] = $this_post->deletedYN;
        $data['deleteByEmpID'] = $deleteByEmpID;
        $data['deletedDatetime'] = $deletedDatetime;
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this_post->suppliercode);
        $data['supplierName'] = trim($this_post->supplierName);
        $data['supplierCountry'] = trim($this_post->suppliercountry);
        $data['supplierTelephone'] = trim($this_post->supplierTelephone);
        $data['supplierEmail'] = trim($this_post->supplierEmail);
        $data['supplierUrl'] = trim($this_post->supplierUrl);
        $data['supplierFax'] = trim($this_post->supplierFax);

        $data['supplierAddress1'] = trim($this_post->supplierAddress1);
        $data['supplierAddress2'] = trim($this_post->supplierAddress2);
        $data['partyCategoryID'] = trim($this_post->partyCategoryID);
        $data['nameOnCheque'] = trim($nameOnCheque);
        $data['liabilityAutoID'] = $liability['GLAutoID'];
        $data['liabilitySystemGLCode'] = $liability['systemAccountCode'];
        $data['liabilityGLAccount'] = $liability['GLSecondaryCode'];
        $data['liabilityDescription'] = $liability['GLDescription'];
        $data['liabilityType'] = $liability['subCategory'];
        $data['supplierCreditPeriod'] = trim($this_post->supplierCreditPeriod);
        $data['supplierCreditLimit'] = trim($this_post->supplierCreditLimit);
        $data['externalProductID'] = trim($this_post->externalProductID);
        $data['externalPrimaryKey'] = trim($this_post->externalPrimaryKey);
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['masterApprovedYN'] = '1';
        $data['masterConfirmedYN'] = '1';

        if (trim($this_post->supplierAutoID)) {
            $this->db->where('supplierAutoID', trim($this_post->supplierAutoID));
            $this->db->update('srp_erp_suppliermaster', $data);
            
            if($this_post->bank_details) {
                $bankDet = $this->supplierbank_detail_save($this_post->bank_details, $this_post->supplierAutoID);
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('type' => 'error', 'status' => 500, 'message' => 'Update Failed.');
            } else {
                $this->db->trans_commit();
                return array('type' => 'success', 'status' => 200, 'message' => 'Updated Successfully.', 'bank_details' => $bankDet);
            }
        } else {
            $this->load->library('sequence');
            $data['supplierCurrencyID'] = trim($this_post->supplierCurrency);
            $data['supplierCurrency'] = $currency_code;
            $data['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data['supplierCurrency']);
            $data['companyID'] = $companyID;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
            $this->db->insert('srp_erp_suppliermaster', $data);
            $last_id = $this->db->insert_id();

            if($this_post->bank_details) {
                $bankDet = $this->supplierbank_detail_save($this_post->bank_details, $last_id);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('type' => 'error', 'status' => 500, 'message' => 'Save Failed.');
            } else {
                $this->db->trans_commit();
                return array('type' => 'success', 'status' => 200, 'message' => 'Supplier Created Successfully.', 'last_id' => $last_id, 'bank_details' => $bankDet);
            }
        }
    }

    function supplierbank_detail_save($this_bank_detail, $supplierAutoID)
    {
        $bankDet = array();
        foreach ($this_bank_detail as $key => $detail) {
            $data['bankName'] = $detail->bank_name;
            $data['currencyID'] = $detail->bank_currency;
            $data['accountName'] = $detail->account_name;
            $data['accountNumber'] = $detail->account_number;
            $data['swiftCode'] = $detail->swift_code;
            $data['ibanCode'] = $detail->iban_code;
            $data['bankAddress'] = $detail->bank_address;
            $data['supplierAutoID'] = $supplierAutoID;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            if(empty($detail->supplierBankMasterID)) {
                $this->db->insert('srp_erp_supplierbankmaster', $data);
                $detail->supplierBankMasterID = $this->db->insert_id();
                $bankDet[] = array(
                    'bankID' => $detail->supplierBankMasterID,
                    'account_number' => $detail->account_number
                );
            }else{
                $this->db->where('supplierBankMasterID', $detail->supplierBankMasterID);
                $this->db->update('srp_erp_supplierbankmaster', $data);
            }
        }

        return $bankDet;
    }
}
