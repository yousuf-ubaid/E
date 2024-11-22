<?php

class Receipt_voucher_model extends ERP_Model
{

    function save_receiptvoucher_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        $adjusted_bank_exchange_rate = $this->input->post('bank_currency_exchange_rate');

        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
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
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['RVdate'] = trim($RVdate);
        $narration= ($this->input->post('RVNarration'));
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
        $data['RVchequeNo'] = trim($this->input->post('RVchequeNo') ?? '');
        if ($bank_detail['isCash'] == 0) {
            $paymentMode =$this->input->post('paymentMode');
            $data['paymentType'] = trim($this->input->post('paymentMode') ?? '');
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
        $data['RvType'] = trim($this->input->post('vouchertype') ?? '');
        $data['referanceNo'] = trim_desc($this->input->post('referenceno'));
        $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');

        if ($data['RvType'] == 'Direct' || $data['RvType'] == 'DirectItem' || $data['RvType'] == 'DirectIncome') {
            $data['customerName'] = trim($this->input->post('customer_name') ?? '');
            $data['customerID'] = '';
            $data['customerAddress'] = '';
            $data['customerTelephone'] = '';
            $data['customerFax'] = '';
            $data['customerEmail'] = '';
            $data['customerCurrency'] = trim($currency_code[0] ?? '');
            $data['customerCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['customerCurrencyID']);
        } else {
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
        }
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
        $data['customerExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);

        //if bank currency exchange rates are altered
        if($bank_currency['conversion'] != $adjusted_bank_exchange_rate){
            $data['bankCurrencyExchangeRate'] = $adjusted_bank_exchange_rate;
        }else{
            $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        }

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

    function save_receiptvoucher_header_suom()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
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
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        if ($data['salesPersonID']) {
            $code = explode(' | ', trim($this->input->post('salesPerson') ?? ''));
            $data['SalesPersonCode'] = trim($code[0] ?? '');
        }

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));
        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['RVdate'] = trim($RVdate);
        $data['RVNarration'] = trim_desc($this->input->post('RVNarration'));
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
        $data['RVchequeNo'] = trim($this->input->post('RVchequeNo') ?? '');
        if ($bank_detail['isCash'] == 0) {
            $data['RVchequeDate'] = trim($RVchequeDate);
        } else {
            $data['RVchequeDate'] = null;
        }
        $data['RvType'] = trim($this->input->post('vouchertype') ?? '');
        $data['referanceNo'] = trim_desc($this->input->post('referenceno'));
        $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');

        if ($data['RvType'] == 'Direct') {
            $data['customerName'] = trim($this->input->post('customer_name') ?? '');
            $data['customerAddress'] = '';
            $data['customerTelephone'] = '';
            $data['customerFax'] = '';
            $data['customerEmail'] = '';
            $data['customerCurrency'] = trim($currency_code[0] ?? '');
            $data['customerCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['customerCurrencyID']);
        } else {
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
        }
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
        $data['customerExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if (trim($this->input->post('receiptVoucherAutoId') ?? '')) {
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
            $this->db->update('srp_erp_customerreceiptmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Receipt Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('receiptVoucherAutoId'));
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
            $data['RVcode'] = 0;

            $this->db->insert('srp_erp_customerreceiptmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Receipt Voucher Saved Successfully.');
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

    function save_receipt_match_header()
    {
        $date_format_policy = date_format_policy();
        $matDate = $this->input->post('matchDate');
        $matchDate = input_format_date($matDate, $date_format_policy);

        $this->db->trans_start();
        $customer_arr = $this->fetch_customer_data(trim($this->input->post('customerID') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['documentID'] = 'RVM';
        $data['matchDate'] = trim($matchDate);
        $narration = ($this->input->post('Narration'));
        $data['Narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['refNo'] = trim($this->input->post('refNo') ?? '');  
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
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

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('matchID') ?? '')) {
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $this->db->update('srp_erp_rvadvancematch', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Matching Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Matching Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('matchID'), 'currency' => $currency_code[0], 'decimal' => $data['transactionCurrencyDecimalPlaces']);
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
            $data['matchSystemCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_rvadvancematch', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Matching Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Receipt Matching Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function load_receipt_match_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate');
        $this->db->from('srp_erp_rvadvancematch');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get()->row_array();
    }

    function fetch_match_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_rvadvancematchdetails')->result_array();
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_customerreceipttaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $this->db->from('srp_erp_taxmaster');
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $inv_master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
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
            $this->db->update('srp_erp_customerreceipttaxdetails', $data);
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
            $this->db->insert('srp_erp_customerreceipttaxdetails', $data);
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

    function fetch_rv_advance_detail()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $matchId = $this->input->post('matchID');
        $this->db->select('customerID,transactionCurrency,transactionCurrencyID,DATE_FORMAT(matchDate,"%Y-%m-%d") AS matchDate');
        $this->db->where('matchID', $matchId);
        $master_arr = $this->db->get('srp_erp_rvadvancematch')->row_array();      
        $matchDate = $this->db->query("SELECT matchDate from srp_erp_rvadvancematch WHERE matchID = $matchId")->row('matchDate');

        $this->db->select('srp_erp_customerreceiptdetail.transactionAmount ,DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate,\'' . $convertFormat . '\') AS RVdate , srp_erp_customerreceiptmaster.RVcode,ROUND(sum( IFNULL(srp_erp_rvadvancematchdetails.transactionAmount , 0) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces) AS paid,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID,srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces as decimalplaces, ROUND(`srp_erp_customerreceiptdetail`.`transactionAmount` - (ROUND(sum( IFNULL(srp_erp_rvadvancematchdetails.transactionAmount , 0) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces)), 2) as balance');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('srp_erp_customerreceiptdetail.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('customerID', $master_arr['customerID']);
        $this->db->where('srp_erp_customerreceiptmaster.transactionCurrencyID', $master_arr['transactionCurrencyID']);
        $this->db->where('type', 'Advance');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->where('srp_erp_customerreceiptmaster.approvedYN', 1);
        $this->db->where('srp_erp_customerreceiptmaster.RVdate <=', $matchDate);
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->join('srp_erp_rvadvancematchdetails', 'srp_erp_rvadvancematchdetails.receiptVoucherDetailAutoID = srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID', 'Left');
        $this->db->having("balance > 0");
        $data['receipt'] = $this->db->get()->result_array();

        $customerID=$master_arr['customerID'];
        $transactionCurrency=$master_arr['transactionCurrencyID'];
        $companyID=$this->common_data['company_data']['company_id'];

        $data['invoice'] = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, `invoiceCode`, `invoiceDate`, `transactionAmount`, `receiptTotalAmount`, `creditNoteTotalAmount`, `advanceMatchedTotal` FROM `srp_erp_customerinvoicemaster` LEFT JOIN(SELECT srp_erp_customerinvoicemaster.invoiceAutoID, ( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexchargistax.transactionAmount, 0 ) ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 ) AS amount FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID )tot ON tot.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE `receiptInvoiceYN` = 0 AND `approvedYN` = 1 AND `customerID` = $customerID AND invoiceDate <= '$matchDate' AND `transactionCurrencyID` = '$transactionCurrency' AND `companyID` = $companyID AND ROUND((transactionAmount - (receiptTotalAmount+ creditNoteTotalAmount + advanceMatchedTotal )), srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces) > 0")->result_array();
        return $data;
    }

    function save_match_amount()
    {
        $this->db->trans_start();
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $invoice_id = $this->input->post('invoiceAutoID');
        $amounts = $this->input->post('amounts');
        $matchID = $this->input->post('matchID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
        $this->db->where('matchID', $matchID);
        $master = $this->db->get('srp_erp_rvadvancematch')->row_array();

        $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId,srp_erp_customerreceiptdetail.transactionAmount,srp_erp_customerreceiptmaster.RVdate,srp_erp_customerreceiptmaster.RVcode,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->where_in('receiptVoucherDetailAutoID', $receiptVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->where_in('receiptVoucherDetailAutoID', $receiptVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        for ($i = 0; $i < count($detail_arr); $i++) {
            $invoice_data = $this->fetch_invoice($invoice_id[$i]);
            $data[$i]['matchID'] = $matchID;
            $data[$i]['receiptVoucherAutoId'] = $detail_arr[$i]['receiptVoucherAutoId'];
            $data[$i]['receiptVoucherDetailAutoID'] = $detail_arr[$i]['receiptVoucherDetailAutoID'];
            $data[$i]['RVcode'] = $detail_arr[$i]['RVcode'];
            $data[$i]['RVdate'] = $detail_arr[$i]['RVdate'];
            $data[$i]['invoiceAutoID'] = trim($invoice_data['invoiceAutoID'] ?? '');
            $data[$i]['invoiceCode'] = trim($invoice_data['invoiceCode'] ?? '');
            $data[$i]['invoiceDate'] = trim($invoice_data['invoiceDate'] ?? '');
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['transactionExchangeRate'] = 1;
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['customerCurrencyAmount'] = ($data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate']);
            $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
            $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];

            $id = $data[$i]['invoiceAutoID'];
//            $amo = $data[$i]['transactionAmount'];
//            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal+$amo) WHERE invoiceAutoID='$id'");
            $amo['advanceMatchedTotal']         = $invoice_data['advanceMatchedTotal'] + $data[$i]['transactionAmount'];
            $balanceAmount                        = $invoice_data['transactionAmount'] - ($invoice_data['creditNoteTotalAmount'] + $invoice_data['receiptTotalAmount'] + $invoice_data['advanceMatchedTotal'] + $data[$i]['transactionAmount']);
            if ($balanceAmount <= 0) {
                $amo['receiptInvoiceYN'] = 1;
            }
            $this->db->where('invoiceAutoID', $id);
            $this->db->update('srp_erp_customerinvoicemaster', $amo);
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_rvadvancematchdetails', $data);
        }

        $details = $this->db->query("SELECT * FROM srp_erp_rvadvancematchdetails WHERE matchID = {$matchID}")->result_array();
        $companyID = current_companyID();
        foreach ($details as $det) {
            $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                                FROM srp_erp_taxledger 
                                                WHERE documentID = 'RVM' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['matchDetailID']}"
            )->row('taxledgerID');

            $vatRegisterYN = $this->db->query("SELECT vatRegisterYN FROM srp_erp_company where company_id = $companyID ")->row('vatRegisterYN');
            if($dataExist == 0) {
                $ledgerDet = $this->db->query("SELECT
                                                    customerCountryID,
                                                    vatEligible,
                                                    customerID,
                                                    srp_erp_taxledger.*,
                                                    srp_erp_taxmaster.outputVatGLAccountAutoID as glAutoID,
                                                    srp_erp_taxmaster.outputVatTransferGLAccountAutoID,
                                                    mastertbl.transactionAmount
                                                FROM
                                                    srp_erp_taxledger
                                                JOIN srp_erp_customerreceiptdetail mastertbl ON mastertbl.receiptVoucherDetailAutoID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'RV'
                                                LEFT JOIN srp_erp_customerreceiptmaster ON mastertbl.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId AND srp_erp_taxledger.documentID = 'RV'
                                                LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerreceiptmaster.customerID
                                                JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                WHERE
                                                   receiptVoucherDetailAutoID = {$det['receiptVoucherDetailAutoID']} AND taxCategory = 2")->result_array();

                if(!empty($ledgerDet)) {
                    $taxAmount = 0;
                    foreach ($ledgerDet as $val) {
                        $dataleg['documentID'] = 'RVM';
                        $dataleg['documentMasterAutoID'] = $matchID;
                        $dataleg['documentDetailAutoID'] = $det['matchDetailID'];
                        $dataleg['taxDetailAutoID'] = null;
                        $dataleg['taxPercentage'] = 0;
                        $dataleg['ismanuallychanged'] = 0;
                        $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                        $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                        $dataleg['taxMasterID'] = $val['taxMasterID'];
                        $dataleg['amount'] = ($val['amount'] / $val['transactionAmount']) * $det['transactionAmount'];
                        $dataleg['formula'] = $val['formula'];
                        $dataleg['taxGlAutoID'] = $val['glAutoID'];
                        $dataleg['isClaimable'] = $vatRegisterYN;
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
                        $taxAmount += ($val['amount'] / $val['transactionAmount']) * $det['transactionAmount'];

                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $taxAmount;
                        $this->db->where('matchDetailID', $det['matchDetailID']);
                        $this->db->update('srp_erp_rvadvancematchdetails', $data_detailTBL);
                    }
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'messsage' => 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'messsage' => 'Records Inserted successfully');
        }
    }

    function fetch_invoice($id)
    {
        $this->db->select('invoiceAutoID,invoiceCode,invoiceDate,transactionAmount, receiptTotalAmount, creditNoteTotalAmount, advanceMatchedTotal');
        $this->db->from('srp_erp_customerinvoicemaster');
        $this->db->where('invoiceAutoID', $id);
        //,receiptTotalAmount ,creditNoteTotalAmount,advanceMatchedTotal
        return $this->db->get()->row_array();
    }

    function delete_rv_match()
    {
        /*$this->db->select('invoiceAutoID,transactionAmount');
        $this->db->where('matchID', $this->input->post('matchID'));
        $data = $this->db->get('srp_erp_rvadvancematchdetails')->result_array();
        for ($i = 0; $i < count($data); $i++) {
            $id = $data[$i]['invoiceAutoID'];
            $amo = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}) and receiptInvoiceYN = 0 WHERE invoiceAutoID='{$id}'");
        }

        $this->db->where('matchID', $this->input->post('matchID'));
        $results = $this->db->delete('srp_erp_rvadvancematch');
        $this->db->where('matchID', $this->input->post('matchID'));
        $results = $this->db->delete('srp_erp_rvadvancematchdetails');
        $this->session->set_flashdata('s', 'Receipt Matching Deleted Successfully');
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_rvadvancematchdetails');
        $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
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
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $this->db->update('srp_erp_rvadvancematch', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }
    }

    function delete_rv_match_detail()
    {
        $this->db->select('invoiceAutoID,transactionAmount');
        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $data = $this->db->get('srp_erp_rvadvancematchdetails')->row_array();
        $id = $data['invoiceAutoID'];
        $amo = $data['transactionAmount'];
        $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-$amo),receiptInvoiceYN = 0 WHERE invoiceAutoID=$id");

        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $results = $this->db->delete('srp_erp_rvadvancematchdetails');
        $this->session->set_flashdata('s', 'Receipt Matching Deleted Successfully');
        return true;
    }

    function fetch_receipt_voucher_match_template_data($matchID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(confirmedDate,\'' . $convertFormat . ' %h:%i:%s\') AS confirmedDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_rvadvancematch');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_rvadvancematchdetails');
        $data['detail'] = $this->db->get()->result_array();
        $data['VAT_exist'] = $this->db->query("SELECT
                                                    COUNT(taxLedgerAutoID) as ledgerDet 
                                                FROM
                                                    srp_erp_taxledger 
                                                WHERE
                                                    documentMasterAutoID = {$matchID}
                                                    AND documentID = 'RVM'")->row('ledgerDet');
        return $data;
    }

    function customer_inv($customerID, $currencyID, $RVdate)
    {
        $date_format_policy = date_format_policy();
        $RVdate = input_format_date($RVdate, $date_format_policy);
        $multiple_currencies_allowed = getPolicyValues('RVMC', 'All');

        //$RVdate = convert_date_format($RVdate);
        $currency_filter = '';
        if($multiple_currencies_allowed != 1){
            $currency_filter .= " AND `transactionCurrency` = '{$currencyID}'";
        }


        $data = $this->db->query("SELECT rebatematchedtbl.rebatematchedamount,srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, 

        ((( ( ( (cid.transactionAmount-retensionTransactionAmount) - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) - IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0)) AS transactionAmount, 

        invoiceDate, slr.returnsalesvalue as salesreturnvalue,IFNULL(rebatePercentage,0) as rebatePercentage, srp_erp_customerinvoicemaster.transactionCurrencyID,srp_erp_customerinvoicemaster.transactionCurrency,rebatematchedtbl.invoiceAmount
        FROM srp_erp_customerinvoicemaster 
            LEFT JOIN ( SELECT invoiceAutoID,IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID 
            LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
            LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
            LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
            LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
            LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
            LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM(rebateAmount),0) as rebatematchedamount,IFNULL(SUM(invoiceAmount),0) as invoiceAmount FROM srp_erp_customerreceiptdetail GROUP BY invoiceAutoID )  rebatematchedtbl on rebatematchedtbl.invoiceAutoID =  srp_erp_customerinvoicemaster.invoiceAutoID 
                WHERE confirmedYN = 1 AND approvedYN = 1 AND `customerID` = '{$customerID}' AND receiptInvoiceYN = 0  {$currency_filter} AND invoiceDate <= '{$RVdate}' ")->result_array();
            
            //AND `customerID` = '{$customerID}'
            //allowed multiple currency types
            // AND `transactionCurrency` = '{$currencyID}'

            //$data = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID,slr.returnsalesvalue as salesreturnvalue,invoiceCode,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,referenceNo ,( ( cid.transactionAmount - cid.totalAfterTax ) * ( IFNULL( tax.taxPercentage, 0 ) / 100 ) + IFNULL( cid.transactionAmount, 0 )) as transactionAmount  FROM srp_erp_customerinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue from srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr on slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID  WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrency` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' ")->result_array();
            //echo $this->db->last_query();
        return $data;
    }

    function get_payment_inv($customerID, $currencyID, $RVdate)
    {
        $date_format_policy = date_format_policy();
        $RVdate = input_format_date($RVdate, $date_format_policy);
        $multiple_currencies_allowed = getPolicyValues('RVMC', 'All');
        $companyID = current_companyID();

        //$RVdate = convert_date_format($RVdate);
        $currency_filter = '';
        if($multiple_currencies_allowed != 1){
            $currency_filter .= " AND `transactionCurrency` = '{$currencyID}'";
        }


        // $data = $this->db->query("SELECT *
        // FROM srp_erp_customerinvoicemaster 
        //     LEFT JOIN ( SELECT invoiceAutoID,IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID 
        //     LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
        //     LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
        //     LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
        //     LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
        //     LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
        //     LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM(rebateAmount),0) as rebatematchedamount,IFNULL(SUM(invoiceAmount),0) as invoiceAmount FROM srp_erp_customerreceiptdetail GROUP BY invoiceAutoID )  rebatematchedtbl on rebatematchedtbl.invoiceAutoID =  srp_erp_customerinvoicemaster.invoiceAutoID 
        //         WHERE confirmedYN = 1 AND approvedYN = 1  ")->result_array();

        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_paysupplierinvoicemaster.bookingDate,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo, supplierInvoiceNo, 
                        ((((IFNULL(tax.taxPercentage, 0) / 100 ) * ( (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) ) + (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) AS transactionAmount
                        FROM srp_erp_paysupplierinvoicemaster 
                        LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID 
                        LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID 
                        WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0  AND `transactionCurrencyID` = '{$currencyID}'  AND `bookingDate` <= '{$RVdate}' AND srp_erp_paysupplierinvoicemaster.paymentTotalAmount != srp_erp_paysupplierinvoicemaster.transactionAmount AND companyID = $companyID"
                    )->result_array();

        // AND paymentInvoiceYN = 0  AND `transactionCurrencyID` = '{$currencyID}' 
        //
      
        return $output;
    }


    function reconfig_using_transaction_curreny($invoices,$currency,$currencyID){

   
        foreach($invoices as $key => $value){
            
            //USD
            $total_amount = $value['transactionAmount'];
            $balanced_amount = $value['transactionAmount'] - ($value['invoiceAmount'] + $value['advanceMatchedTotal'] + $value['creditNoteTotalAmount']);
            
            $detail = fetch_currency_dec($value['transactionCurrency']);
            $conversion = currency_conversionID($value['transactionCurrencyID'],$currencyID);

            $total_amount_converted = currency_conversionID($currencyID,$value['transactionCurrencyID'],$total_amount);
            $balanced_amount_converted = currency_conversionID($currencyID,$value['transactionCurrencyID'],$balanced_amount);

            $invoices[$key]['balanced_amount_converted'] = $balanced_amount_converted['convertedAmount'];
            $invoices[$key]['total_amount_converted'] = $total_amount_converted['convertedAmount'];
          
         
        }
        
    
        return $invoices;

    }

    function reconfig_using_transaction_curreny_creditnote($invoices,$currency,$currencyID){

   
        foreach($invoices as $key => $value){
            
            //USD
            $total_amount = $value['transactionAmount'];
            $balanced_amount = $value['transactionAmount'] - ($value['invoiceAmountTotal']);
            
            $detail = fetch_currency_dec($value['transactionCurrency']);
            $conversion = currency_conversionID($value['transactionCurrencyID'],$currencyID);

            $total_amount_converted = currency_conversionID($currencyID,$value['transactionCurrencyID'],$total_amount);
            $balanced_amount_converted = currency_conversionID($currencyID,$value['transactionCurrencyID'],$balanced_amount);

            $invoices[$key]['balanced_amount_converted'] = $balanced_amount_converted['convertedAmount'];
            $invoices[$key]['total_amount_converted'] = $total_amount_converted['convertedAmount'];
          
         
        }
        
    
        return $invoices;

    }

    function save_inv_base_items()
    {
        $this->db->trans_start();
//        $rebate = getPolicyValues('CRP', 'All');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $receiptVoucherAutoID =  $this->input->post('receiptVoucherAutoId');
        $settlementAmount =  $this->input->post('settlementAmount');
        $type =  $this->input->post('type');
        $grv_m = array();

        $multiple_currency = getPolicyValues('RVMC', 'All');

        if($type == 'SUP'){

            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate as customerCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID as invoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID as customerReceivableAutoID, supplierliabilitySystemGLCode as customerReceivableSystemGLCode, supplierliabilityGLAccount as customerReceivableGLAccount,companyReportingCurrency, supplierliabilityDescription as customerReceivableDescription, supplierliabilityType as customerReceivableType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode as invoiceCode,RefNo as referenceNo,bookingDate as invoiceDate,comments as invoiceNarration,	(
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
                ) AS transactionAmount,paymentTotalAmount as receiptTotalAmount,0 as creditNoteTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID,segmentID,segmentCode,transactionCurrencyDecimalPlaces');
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid', 'srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID', 'left');
            $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID', 'left');
            $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $this->input->post('invoiceAutoID'));
            $master_recode = $this->db->get()->result_array();


        }else{

            $this->db->select('customerReceivableAutoID,slr.returnsalesvalue as returnsalesvalue,companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate ,srp_erp_customerinvoicemaster.invoiceAutoID,invoiceCode,referenceNo,invoiceDate,invoiceNarration,(( ( ( (cid.transactionAmount-retensionTransactionAmount - IFNULL(rebateAmount, 0)) - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount - IFNULL(rebateAmount, 0)), 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL((cid.transactionAmount-retensionTransactionAmount - IFNULL(rebateAmount, 0)), 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount - IFNULL(rebateAmount, 0)), 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,customerReceivableSystemGLCode,customerReceivableGLAccount,customerReceivableDescription,customerReceivableType,segmentID,segmentCode,transactionCurrencyDecimalPlaces,srp_erp_customerinvoicemaster.rebateGLAutoID,srp_erp_customerinvoicemaster.rebatePercentage,srp_erp_customerinvoicemaster.transactionCurrencyID,srp_erp_customerinvoicemaster.transactionCurrency,
            srp_erp_customerinvoicemaster.companyLocalAmount,srp_erp_customerinvoicemaster.companyReportingAmount,srp_erp_customerinvoicemaster.customerCurrencyAmount');
            $this->db->from('srp_erp_customerinvoicemaster');
            $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid', 'srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID', 'left');
    
            $this->db->join('(SELECT 
                invoiceAutoID,
                IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue
                from 
                srp_erp_salesreturndetails slaesdetail
                GROUP BY invoiceAutoID) slr', 'srp_erp_customerinvoicemaster.invoiceAutoID = slr.invoiceAutoID', 'left');
    
            $this->db->join('(SELECT
                SUM(discountPercentage) AS discountPercentage,
                    invoiceAutoID
                from
                srp_erp_customerinvoicediscountdetails
                GROUP BY invoiceAutoID) gendiscount', 'srp_erp_customerinvoicemaster.invoiceAutoID = gendiscount.invoiceAutoID', 'left');
    
    
            $this->db->join('(SELECT
                SUM(transactionAmount) AS transactionAmount,
                    invoiceAutoID
                from
                srp_erp_customerinvoiceextrachargedetails
                WHERE
                    isTaxApplicable = 1
                GROUP BY invoiceAutoID) genexchargistax', 'srp_erp_customerinvoicemaster.invoiceAutoID = genexchargistax.invoiceAutoID', 'left');
    
    
            $this->db->join('(SELECT
                SUM(transactionAmount) AS transactionAmount,
                    invoiceAutoID
                from
                srp_erp_customerinvoiceextrachargedetails
                GROUP BY invoiceAutoID) genexcharg', 'srp_erp_customerinvoicemaster.invoiceAutoID = genexcharg.invoiceAutoID', 'left');
    
    
    
            $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID', 'left');
            $this->db->where_in('srp_erp_customerinvoicemaster.invoiceAutoID', $this->input->post('invoiceAutoID'));
            $master_recode = $this->db->get()->result_array();

        }



        $amount = $this->input->post('amount');
        $INVamount = $this->input->post('INVamount');
        $rebetamount = $this->input->post('rebetamount');

        $am_arr = []; $inv_arr = []; $re_arr = [];
        foreach($invoiceAutoID as $key=>$row){
            $am_arr[$row] = $amount[$key];
            $inv_arr[$row] = $INVamount[$key] ?? 0;
            $re_arr[$row] = $rebetamount[$key] ?? 0;
        }

        for ($i = 0; $i < count($master_recode); $i++) {
            $invAutoID=$master_recode[$i]['invoiceAutoID'];


            $transactionAmount =  $am_arr[$invAutoID];
            $transactionExchangeRate = 1;
            $localAmount = $am_arr[$invAutoID];
            $localExchangeRate = 1;
            $reportingAmount = $am_arr[$invAutoID];
            $reportingExchangeRate = 1;
            $customerAmount = $am_arr[$invAutoID];
            $customerExchangeRate = 1;


            if($multiple_currency == 1 && $type != 'SUP'){
                $calculated_amounts = $this->calculate_different_currency_exchange_rates($master_recode[$i],$receiptVoucherAutoID,$am_arr[$invAutoID]);

                $transactionAmount = $calculated_amounts['transactionAmount'];
                $transactionExchangeRate = $calculated_amounts['transactionExchangeRate'];
                $localAmount = $calculated_amounts['localCurrencyAmount'];
                $localExchangeRate = $calculated_amounts['localCurrencyExchangeRate'];
                $reportingAmount = $calculated_amounts['reportingCurrencyAmount'];
                $reportingExchangeRate = $calculated_amounts['reportingCurrencyExchangeRate'];
                $customerAmount = $calculated_amounts['customerCurrencyAmount'];
                $customerExchangeRate = $calculated_amounts['customerCurrencyExchangeRate'];
                  
                $master_recode[$i]['companyLocalExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['localCurrencyID']) ? 1 : $localExchangeRate;
                $master_recode[$i]['companyReportingExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['reportingCurrencyID']) ? 1 : $reportingExchangeRate;
                $master_recode[$i]['customerCurrencyExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['customerCurrencyID']) ? 1 : $customerExchangeRate;
            }
           
            $data[$i]['invoiceAmount'] = $transactionAmount;
            $data[$i]['invoiceExchangeRate'] = $transactionExchangeRate;

            $data[$i]['receiptVoucherAutoId'] = $this->input->post('receiptVoucherAutoId');
            $data[$i]['invoiceAutoID'] = $master_recode[$i]['invoiceAutoID'];
            $data[$i]['type'] = 'Invoice';
            $data[$i]['invoiceCode'] = $master_recode[$i]['invoiceCode'];
            $data[$i]['referenceNo'] = $master_recode[$i]['referenceNo'];
            $data[$i]['invoiceDate'] = $master_recode[$i]['invoiceDate'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['customerReceivableAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['customerReceivableSystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['customerReceivableGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['customerReceivableDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['customerReceivableType'];
            $data[$i]['description'] = $master_recode[$i]['invoiceNarration'];
            $data[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'] * $transactionExchangeRate;
            $data[$i]['segmentID'] = $master_recode[$i]['segmentID'];
            $data[$i]['segmentCode'] = $master_recode[$i]['segmentCode'];
            $data[$i]['due_amount'] = (($master_recode[$i]['transactionAmount'] * $transactionExchangeRate) - ($master_recode[$i]['receiptTotalAmount'] + $master_recode[$i]['advanceMatchedTotal'] + $master_recode[$i]['creditNoteTotalAmount']));
            
            $due_balance = $data[$i]['due_amount'];
            $data[$i]['due_amount'] = $data[$i]['due_amount'] ;

            $data[$i]['rebateAmount'] = 0;

            $data[$i]['balance_amount'] = ($due_balance - round($am_arr[$invAutoID]+( $data[$i]['rebateAmount']), $master_recode[$i]['transactionCurrencyDecimalPlaces']));

            $data[$i]['transactionAmount'] = round($am_arr[$invAutoID]+( $data[$i]['rebateAmount']), $master_recode[$i]['transactionCurrencyDecimalPlaces']);

            if($multiple_currency == 1){
                $data[$i]['companyLocalAmount'] = $localAmount;
                $data[$i]['companyReportingAmount'] =  $reportingAmount;
                $data[$i]['customerAmount'] = $customerAmount;
            }else{
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                $data[$i]['customerAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['customerCurrencyExchangeRate']);
            }

            if($type == 'SUP'){
                $data[$i]['detailInvoiceType'] = 'SUP';
            }

            $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
            $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
            $data[$i]['customerCurrencyExchangeRate'] = $master_recode[$i]['customerCurrencyExchangeRate'];
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
            
            if($type != 'SUP'){
                $grv_m[$i]['invoiceAutoID'] = $invoiceAutoID[$i];
                $grv_m[$i]['receiptTotalAmount'] = ($master_recode[$i]['receiptTotalAmount'] + ($transactionAmount + ($data[$i]['rebateAmount'])));
                $grv_m[$i]['receiptInvoiceYN'] = 0;
                if ($data[$i]['balance_amount'] <= 0) {
                    $grv_m[$i]['receiptInvoiceYN'] = 1;
                }
            }
           
        }
        $data_up_settlement['settlementTotal'] =$settlementAmount;
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoID);
        $this->db->update('srp_erp_customerreceiptmaster', $data_up_settlement);

        if (!empty($data)) {

            if(count($grv_m) > 0 ) {
                $this->db->update_batch('srp_erp_customerinvoicemaster', $grv_m, 'invoiceAutoID');
            }
            
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', ' Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', ' Invoice : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function calculate_different_currency_exchange_rates($master_record,$receiptVoucherAutoID,$payed_amount,$type = 'inv',$alreadypayed= null){

       
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoID);
        $voucherDetail = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $total_amount = $master_record['transactionAmount'];

     
        if($type == 'inv'){
            $balanced_amount = $master_record['transactionAmount'] - ($master_record['receiptTotalAmount'] + $master_record['advanceMatchedTotal'] + $master_record['creditNoteTotalAmount']);
           
        }else{
            $balanced_amount = $master_record['transactionAmount'] - ($alreadypayed);
        }

        $total_amount_converted = currency_conversionID( $voucherDetail['transactionCurrencyID'],$master_record['transactionCurrencyID'],$total_amount);;
        $balanced_amount_converted = currency_conversionID($voucherDetail['transactionCurrencyID'],$master_record['transactionCurrencyID'],$balanced_amount);;

        $payed_percentage = $payed_amount / $total_amount_converted['convertedAmount'];

        

        $invoiced_payed = $payed_percentage * $total_amount;
        $company_amount = $payed_percentage * $master_record['companyLocalAmount'];
        $reporting_amount = $payed_percentage * $master_record['companyReportingAmount'];
        $customer_amount = $payed_percentage * $master_record['customerCurrencyAmount'];
        

        $invoice_currency_ex_rate = $payed_amount / $invoiced_payed;
        $local_currency_ex_rate = $payed_amount / $company_amount;
        $reporting_currency_ex_rate = $payed_amount / $reporting_amount;
        $customer_currency_ex_rate = $payed_amount / $customer_amount;


        //print_r($local_currency_ex_rate); exit;
        
        $base_arr = array('transactionCurrencyID' => $voucherDetail['transactionCurrencyID'],'transactionCurrency' => $voucherDetail['transactionCurrency'],'transactionAmount'=>$invoiced_payed,'transactionExchangeRate' => $invoice_currency_ex_rate,'localCurrencyID'=>$voucherDetail['companyLocalCurrencyID'],'localCurrencyAmount' => $company_amount,'localCurrencyExchangeRate'=> $local_currency_ex_rate 
        ,'reportingCurrencyID' => $voucherDetail['companyReportingCurrencyID'],'reportingCurrencyAmount' => $reporting_amount,'reportingCurrencyExchangeRate' => $reporting_currency_ex_rate,'customerCurrencyAmount' => $customer_amount,'customerCurrencyExchangeRate' => $customer_currency_ex_rate,'customerCurrency' => $voucherDetail['customerCurrency'], 'customerCurrencyID'=>$voucherDetail['customerCurrencyID']);

        return $base_arr;
    }

    function save_rv_item_detail()
    {
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV', 'receiptVoucherAutoId');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouseAutoIDs = array_filter($this->input->post('wareHouseAutoID'));
        $itemAutoIDJoin = join(',', $itemAutoIDs);
        $wareHouseAutoIDJoin = join(',', $wareHouseAutoIDs);
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $ProjectCategory = $this->input->post('project_categoryID');
        $ProjectSubcategory = $this->input->post('project_subCategoryID');

        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();

        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMID = $this->input->post('SUOMIDhn');
        $item_text = $this->input->post('item_text');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm = $this->db->get()->row_array();

            $wareHouse_location = explode('|', trim($wareHouse[$key]));
            $item_data = fetch_item_data(trim($itemAutoID));

            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $item_data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $wareHouseAutoID[$key],
                        'wareHouseLocation' => $wareHouse_location[1],
                        'wareHouseDescription' => $wareHouse_location[2],
                        'itemAutoID' => $item_data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $item_data['itemSystemCode'],
                        'itemDescription' => $item_data['itemDescription'],
                        'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                        'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );

                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }

            $uomDesc = explode('|', $uom[$key]);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $ProjectCategory[$key];
                $data['project_subCategoryID'] = $ProjectSubcategory[$key];
            }
            $data['SUOMID'] = $SUOMID[$key];
            $data['SUOMQty'] = $SUOMQty[$key];
            $data['unitOfMeasure'] = trim($uomDesc[0] ?? '');
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = trim($estimatedAmount[$key]);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested[$key]));
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['type'] = 'Item';
            if ($serviceitm['mainCategory'] != 'Service') {
                $data['wareHouseAutoID'] = trim($wareHouseAutoID[$key]);
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            } else {
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
            }

            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);

            $data['unitpartyAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                $batch_number2 = $this->input->post("batch_number[{$key}]");
                $arraydata2 = implode(",",$batch_number2);
                $data['batchNumber'] = $arraydata2;
            }

            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $item_data['revanueGLAutoID'];
            $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['GLCode'] = $item_data['revanueGLCode'];
            $data['GLDescription'] = $item_data['revanueDescription'];
            $data['GLType'] = $item_data['revanueType'];
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
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (!empty($item_text[$key])) {
                if($isGroupByTax == 1) {
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$item_text[$key]);
                    $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                    $dataTax['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
                    $dataTax['taxFormulaMasterID'] = $item_text[$key];
                    $dataTax['taxDescription'] = $tax_master['Description'];
                    $dataTax['transactionCurrencyID'] = $master['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $master['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $master['transactionExchangeRate'];
                    $dataTax['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $master['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $master['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                }
            }

            if (trim($this->input->post('receiptVoucherDetailAutoID') ?? '')) {

            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerreceiptdetail', $data);
                $last_id = $this->db->insert_id();

                if($isGroupByTax == 1 && !empty($item_text[$key])) {
                    tax_calculation_vat('srp_erp_customerreceipttaxdetails',$dataTax,$item_text[$key],'receiptVoucherAutoId',trim($this->input->post('receiptVoucherAutoId') ?? ''),$data['transactionAmount'],'RV',$last_id, 0,1);
                }

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', trim($itemAutoID));
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Inserted successfully');
        }

    }

    function save_rv_item_detail_suom()
    {
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouseAutoIDs = array_filter($this->input->post('wareHouseAutoID'));
        $itemAutoIDJoin = join(',', $itemAutoIDs);
        $wareHouseAutoIDJoin = join(',', $wareHouseAutoIDs);
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');

        if (!trim($this->input->post('receiptVoucherDetailAutoID') ?? '') && !empty($wareHouseAutoIDJoin)) {
            foreach ($itemAutoIDs as $key => $itemID) {
                $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerreceiptdetail');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                $this->db->where('itemAutoID', $itemID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();

                $this->db->select('mainCategory');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $itemID);
                $serviceitm= $this->db->get()->row_array();

                if (!empty($order_detail) && $serviceitm['mainCategory']=="Inventory") {
                    return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }
        }

        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();

        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $netAmount = $this->input->post('netAmount');
        $projectID = $this->input->post('projectID');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMID = $this->input->post('SUOMIDhn');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm = $this->db->get()->row_array();

            $wareHouse_location = explode('|', trim($wareHouse[$key]));
            $item_data = fetch_item_data(trim($itemAutoID));

            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $item_data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $wareHouseAutoID[$key],
                        'wareHouseLocation' => $wareHouse_location[1],
                        'wareHouseDescription' => $wareHouse_location[2],
                        'itemAutoID' => $item_data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $item_data['itemSystemCode'],
                        'itemDescription' => $item_data['itemDescription'],
                        'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                        'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );

                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }


            $uomDesc = explode('|', $uom[$key]);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['SUOMID'] = $SUOMID[$key];
            $data['SUOMQty'] = $SUOMQty[$key];
            $data['unitOfMeasure'] = trim($uomDesc[0] ?? '');
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = trim($estimatedAmount[$key]);
            $data['transactionAmount'] = trim($netAmount[$key]);
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['type'] = 'Item';
            if ($serviceitm['mainCategory'] != 'Service') {
                $data['wareHouseAutoID'] = trim($wareHouseAutoID[$key]);
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            } else {
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);

            $data['unitpartyAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $item_data['revanueGLAutoID'];
            $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['GLCode'] = $item_data['revanueGLCode'];
            $data['GLDescription'] = $item_data['revanueDescription'];
            $data['GLType'] = $item_data['revanueType'];
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
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('receiptVoucherDetailAutoID') ?? '')) {

            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerreceiptdetail', $data);
                $last_id = $this->db->insert_id();

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', trim($itemAutoID));
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();


            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Inserted successfully');
        }

    }

    function update_rv_item_detail()
    {
        $item_text = $this->input->post('item_text');
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV','receiptVoucherAutoId');
        $itemAutoID = $this->input->post('itemAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm = $this->db->get()->row_array();

//        if (!empty($this->input->post('receiptVoucherDetailAutoID')) && $serviceitm['mainCategory'] =="Inventory") {
//            $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
//            $this->db->from('srp_erp_customerreceiptdetail');
//            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
//            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
//            $this->db->where('wareHouseAutoID IN (' . $wareHouseAutoID . ')');
//            $this->db->where('receiptVoucherDetailAutoID !=', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
//            $order_detail = $this->db->get()->row_array();
//            if (!empty($order_detail)) {
//                return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
//            }
//        }


        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $comment = $this->input->post('comment');
        $projectID = $this->input->post('projectID');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        $wareHouse_location = explode('|', trim($wareHouse));
        $item_data = fetch_item_data(trim($itemAutoID));
        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $item_data['itemAutoID']);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $wareHouseAutoID,
                    'wareHouseLocation' => $wareHouse_location[1],
                    'wareHouseDescription' => $wareHouse_location[2],
                    'itemAutoID' => $item_data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $item_data['itemSystemCode'],
                    'itemDescription' => $item_data['itemDescription'],
                    'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                    'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );

                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }
        $uom = explode('|', $uom);
        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($UnitOfMeasureID);
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = trim($estimatedAmount);
        $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested));
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['type'] = 'Item';
        if ($serviceitm['mainCategory'] != 'Service') {
            $data['wareHouseAutoID'] = trim($wareHouseAutoID);
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        } else {
            $data['wareHouseAutoID'] = null;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);
        $data['customerAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);
        $data['unitpartyAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $batch_number1 = $this->input->post('batch_number');
            $arraydata1 = implode(',',$batch_number1);
            $data['batchNumber'] = $arraydata1;

        } 

        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['GLAutoID'] = $item_data['revanueGLAutoID'];
        $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['GLCode'] = $item_data['revanueGLCode'];
        $data['GLDescription'] = $item_data['revanueDescription'];
        $data['GLType'] = $item_data['revanueType'];
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
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
        $this->db->update('srp_erp_customerreceiptdetail', $data);

        if(empty($isGroupByTax == 1 && $item_text)) {
            fetchExistsDetailTBL('RV', trim($this->input->post('receiptVoucherAutoId') ?? ''), trim($this->input->post('receiptVoucherDetailAutoID') ?? ''),'srp_erp_customerreceipttaxdetails',1,$data['transactionAmount']);
                $data['taxCalculationformulaID'] = null;
                $data['taxAmount'] = 0;
                $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
                $this->db->update('srp_erp_customerreceiptdetail', $data);
        }else if($isGroupByTax == 1 && !empty($item_text)) {
            $receiptVoucherDetailAutoID = trim($this->input->post('receiptVoucherDetailAutoID') ?? '');
            $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $this->db->select('*');
            $this->db->where('taxCalculationformulaID',$item_text);
            $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

            $dataTax['receiptVoucherAutoId'] = trim($receiptVoucherAutoId);
            $dataTax['taxFormulaMasterID'] = $item_text;
            $dataTax['taxDescription'] = $tax_master['Description'];
            $dataTax['transactionCurrencyID'] = $master['transactionCurrencyID'];
            $dataTax['transactionCurrency'] = $master['transactionCurrency'];
            $dataTax['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
            $dataTax['companyLocalCurrency'] = $master['companyLocalCurrency'];
            $dataTax['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $dataTax['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
            $dataTax['companyReportingCurrency'] = $master['companyReportingCurrency'];
            $dataTax['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

            tax_calculation_vat(null,null,$item_text,'receiptVoucherAutoId',trim($receiptVoucherAutoId),$data['transactionAmount'],'RV',$receiptVoucherDetailAutoID,0,1);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
        }

    }

    function update_rv_item_detail_suom()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $serviceitm = $this->db->get()->row_array();

        if (!empty($this->input->post('receiptVoucherDetailAutoID')) && $serviceitm['mainCategory'] =="Inventory") {
            $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
            $this->db->where('itemAutoID IN (' . $itemAutoID . ')');
            $this->db->where('wareHouseAutoID IN (' . $wareHouseAutoID . ')');
            $this->db->where('receiptVoucherDetailAutoID !=', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }


        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedamountnondec');
        $netAmount = $this->input->post('netAmount');
        $comment = $this->input->post('comment');
        $projectID = $this->input->post('projectID');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        $wareHouse_location = explode('|', trim($wareHouse));
        $item_data = fetch_item_data(trim($itemAutoID));
        if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', $item_data['itemAutoID']);
            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

            if (empty($warehouseitems)) {
                $data_arr = array(
                    'wareHouseAutoID' => $wareHouseAutoID,
                    'wareHouseLocation' => $wareHouse_location[1],
                    'wareHouseDescription' => $wareHouse_location[2],
                    'itemAutoID' => $item_data['itemAutoID'],
                    'barCodeNo' => $item_data['barcode'],
                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                    'ActiveYN' => $item_data['isActive'],
                    'itemSystemCode' => $item_data['itemSystemCode'],
                    'itemDescription' => $item_data['itemDescription'],
                    'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                    'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                    'currentStock' => 0,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'companyCode' => $this->common_data['company_data']['company_code'],
                );

                $this->db->insert('srp_erp_warehouseitems', $data_arr);
            }
        }
        $uom = explode('|', $uom);
        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
        $data['itemAutoID'] = trim($itemAutoID);
        $data['itemSystemCode'] = $item_data['itemSystemCode'];
        $data['itemDescription'] = $item_data['itemDescription'];
        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
        }
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($UnitOfMeasureID);
        $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($quantityRequested);
        $data['unittransactionAmount'] = trim($estimatedAmount);
        $data['transactionAmount'] = trim($netAmount);
        $data['comment'] = trim($comment);
        $data['remarks'] = trim($remarks);
        $data['type'] = 'Item';
        if ($serviceitm['mainCategory'] != 'Service') {
            $data['wareHouseAutoID'] = trim($wareHouseAutoID);
            $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
            $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
            $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        } else {
            $data['wareHouseAutoID'] = null;
            $data['wareHouseCode'] = null;
            $data['wareHouseLocation'] = null;
            $data['wareHouseDescription'] = null;
        }
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);
        $data['customerAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);
        $data['unitpartyAmount'] = 0;
        if ($data['customerCurrencyExchangeRate']) {
            $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
        }

        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['GLAutoID'] = $item_data['revanueGLAutoID'];
        $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
        $data['GLCode'] = $item_data['revanueGLCode'];
        $data['GLDescription'] = $item_data['revanueDescription'];
        $data['GLType'] = $item_data['revanueType'];
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
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
        $this->db->update('srp_erp_customerreceiptdetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');
        }

    }

    function fetch_receipt_voucher_template_data($receiptVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('*,srp_erp_customerreceiptmaster.createdUserName as createdUserName, srp_erp_segment.description as segDescription, DATE_FORMAT(srp_erp_customerreceiptmaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime, DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,
                    DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,
                    if(customerID IS NULL OR customerID = 0,srp_erp_customerreceiptmaster.customerName,srp_erp_customermaster.customerName) as customerName,
                    if(customerID IS NULL OR customerID = 0,srp_erp_customerreceiptmaster.customerTelephone,srp_erp_customermaster.customerTelephone) as customertel,
                    srp_erp_customermaster.customerAddress1 as customeradd, srp_erp_customermaster.customerSystemCode as customersys,srp_erp_customermaster.customerFax as customerfax,CASE WHEN srp_erp_customerreceiptmaster.confirmedYN = 2 || srp_erp_customerreceiptmaster.confirmedYN = 3   THEN " - " WHEN srp_erp_customerreceiptmaster.confirmedYN = 1 THEN
            CONCAT_WS(\' on \',IF(LENGTH(srp_erp_customerreceiptmaster.confirmedByName),srp_erp_customerreceiptmaster.confirmedByName,\'-\'),IF(LENGTH(DATE_FORMAT(srp_erp_customerreceiptmaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( srp_erp_customerreceiptmaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn
            ');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_customerreceiptmaster.segmentID', 'Left');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID', 'Left');
        $this->db->from('srp_erp_customerreceiptmaster');

        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        /*     $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
             $this->db->where('customerAutoID', $data['master']['customerID']);
             $this->db->from('srp_erp_customermaster');
             $data['customer'] = $this->db->get()->row_array();*/


        $this->db->select('srp_erp_customerreceiptdetail.*,CONCAT_WS(
                    \' - Part No : \',
                IF
                    ( LENGTH( srp_erp_customerreceiptdetail.`itemDescription` ), `srp_erp_customerreceiptdetail`.`itemDescription`, NULL ),
                IF
                    ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
                    ) AS Itemdescriptionpartno ,srp_erp_unit_of_measure.UnitShortCode as secuom,'.$item_code_alias.' ');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerreceiptdetail.SUOMID', 'left');
        $data['item_detail'] = $this->db->get()->result_array();
     
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['gl_detail'] = $this->db->get()->result_array();
        
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'EXGL');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['expense_gl_detail'] = $this->db->get()->result_array();

        $this->db->select('*,ifnull(srp_erp_customerreceiptdetail.rebateAmount,0) as rebateAmounts');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->where('detailInvoiceType !=', 'SUP');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('*,ifnull(srp_erp_customerreceiptdetail.rebateAmount,0) as rebateAmounts');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->where('detailInvoiceType', 'SUP');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['sup_invoice'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_customerreceiptdetail.*, contractCode, srp_erp_contractmaster.documentID as contractDocID, srp_erp_contractmaster.contractAutoID');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_customerreceiptdetail.contractAutoID', 'LEFT');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['advance'] = $this->db->get()->result_array();

       /* $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'creditnote');
        $this->db->or_where('type', 'SLR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['creditnote'] = $this->db->get()->result_array();*/

        $data['creditnote'] =  $this->db->query("SELECT * FROM srp_erp_customerreceiptdetail WHERE `receiptVoucherAutoId` = '$receiptVoucherAutoId' AND (`type` = 'creditnote' OR `type` = 'SLR')")->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'PRVR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['prvr_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
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

    function load_receipt_voucher_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate,IF(RVType=\'Direct\',\'DirectItem\',IF(RVType=\'Invoices\',\'CustomerInvoices\',RVType)) as documenttype');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        return $this->db->get('srp_erp_customerreceiptmaster')->row_array();
    }

    function fetch_rv_details()
    {
        $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId') ?? '');
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.mainCategory,CONCAT_WS(
                        \' - Part No : \',
                    IF
                        ( LENGTH( srp_erp_customerreceiptdetail.`itemDescription` ), `srp_erp_customerreceiptdetail`.`itemDescription`, NULL ),
                    IF
                        ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
                        ) AS Itemdescriptionpartno, IFNULL(contractCode, " - ") AS contractCode, '.$item_code_alias.' ');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_contractmaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_customerreceiptdetail.contractAutoID', 'left');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();

        $companyID = current_companyID();
        $contract_documents = $this->db->query("SELECT DISTINCT
                                    (contractAutoID) AS contractAutoID, 
                                    contractCode 
                                FROM
                                    srp_erp_contractmaster
                                    JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.customerID = srp_erp_contractmaster.customerID AND srp_erp_customerreceiptmaster.transactionCurrencyID = srp_erp_contractmaster.transactionCurrencyID 
                                WHERE
                                    srp_erp_contractmaster.companyID = {$companyID} 
                                    AND receiptVoucherAutoId = {$receiptVoucherAutoId} 
                                    AND srp_erp_contractmaster.approvedYN = 1 
                                    AND NOT EXISTS (SELECT contractAutoID FROM srp_erp_customerinvoicedetails WHERE srp_erp_customerinvoicedetails.contractAutoID = srp_erp_contractmaster.contractAutoID ) 
                                    AND NOT EXISTS (SELECT contractAutoID FROM srp_erp_deliveryorderdetails WHERE srp_erp_deliveryorderdetails.contractAutoID = srp_erp_contractmaster.contractAutoID)
                                    ")->result_array();
        $data['contract_documents'] = array(''=>'Select Document');
        if (isset($data)) {
            foreach ($contract_documents as $row) {
                $data['contract_documents'][trim($row['contractAutoID'] ?? '')] = trim($row['contractCode'] ?? '');
            }
        }

        return $data;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_customerreceipttaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function save_direct_rv_detail()
    {
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV','receiptVoucherAutoId');
        $this->db->trans_start();
        $this->db->select('transactionCurrency, customerExchangeRate, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();
        $projectExist = project_is_exist();
        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $gl_auto_ids = $this->input->post('gl_code');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $GL_Type = $this->input->post('GL_Type');
        $projectID = $this->input->post('projectID');
        $discountPercentage = $this->input->post('discountPercentage');
        $project_categoryID =  $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $gl_text_type = $this->input->post('gl_text');

        foreach ($gl_auto_ids as $key => $gl_auto_id) {
            $segment = explode('|', trim($segment_gl[$key]));
            $gl_code = explode('|', trim($gl_code_des[$key]));

            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $data['GLAutoID'] = trim($gl_auto_id);
            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] =$project_categoryID[$key];
                $data['project_subCategoryID'] =$project_subCategoryID[$key];
            }
            $data['projectID'] = $projectID[$key] ?? '';
            $data['discountPercentage'] = trim($discountPercentage[$key]);
            $data['discountAmount'] = trim(($amount[$key]*$discountPercentage[$key])/100);
            $data['transactionAmount'] = trim($amount[$key]-$data['discountAmount']);
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($master['customerExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $master['customerExchangeRate']);
            }


            $data['description'] = trim($description[$key]);
            $data['type'] = ($GL_Type) ? $GL_Type : 'GL';
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

                    tax_calculation_vat('srp_erp_customerreceipttaxdetails',$dataTax,$gl_text_type[$key],'receiptVoucherAutoId',trim($this->input->post('receiptVoucherAutoId') ?? ''),$amount[$key],'RV',$last_id,$data['discountAmount'],1);
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

    function update_direct_rv_detail()
    {
        $gl_text_type = $this->input->post('gl_text_type');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster', trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV', 'receiptVoucherAutoId');

        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID');
        $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherAutoId'));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        $projectExist = project_is_exist();
        $segment_gl = $this->input->post('segment_gl');
        $gl_code_des = $this->input->post('gl_code_des');
        $gl_auto_id = $this->input->post('gl_code');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $discountPercentage = $this->input->post('discountPercentage');
        $type = $this->input->post('type');

        $segment = explode('|', trim($segment_gl));
        $gl_code = explode('|', trim($gl_code_des));

        $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
        $data['GLAutoID'] = trim($gl_auto_id);

        if ($projectExist == 1) {
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }
        $data['systemGLCode'] = trim($gl_code[0] ?? '');
        $data['GLCode'] = trim($gl_code[1] ?? '');
        $data['GLDescription'] = trim($gl_code[2] ?? '');
        $data['GLType'] = trim($gl_code[3] ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['discountPercentage'] = trim($discountPercentage);
        $data['discountAmount'] = trim(($amount*$discountPercentage)/100);
        $data['transactionAmount'] = trim($amount-$data['discountAmount']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['customerAmount'] = 0;
        if ($master['customerExchangeRate']) {
            $data['customerAmount'] = ($data['transactionAmount'] / $master['customerExchangeRate']);
        }
        //$data['customerAmount'] = ($data['transactionAmount'] / $master['customerExchangeRate']);
        $data['description'] = trim($description);
        $data['type'] = ($type) ? $type : 'GL';
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

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
        $this->db->update('srp_erp_customerreceiptdetail', $data);

        if(empty($isGroupByTax == 1 && $gl_text_type)) {
            fetchExistsDetailTBL('RV', trim($this->input->post('receiptVoucherAutoId') ?? ''), trim($this->input->post('receiptVoucherDetailAutoID') ?? ''),'srp_erp_customerreceipttaxdetails',1,$data['transactionAmount']);
                $data['taxCalculationformulaID'] = null;
                $data['taxAmount'] = 0;
                $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
                $this->db->update('srp_erp_customerreceiptdetail', $data);
        }else if($isGroupByTax == 1 && !empty($gl_text_type)) {
            $this->db->select('*');
            $this->db->where('taxCalculationformulaID',$gl_text_type);
            $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
    
            $this->db->trans_start();
            $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
            $inv_master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

            $dataTax['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $dataTax['taxFormulaMasterID'] = $gl_text_type;
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

            tax_calculation_vat('srp_erp_customerreceipttaxdetails',$dataTax,$gl_text_type,'receiptVoucherAutoId',trim($this->input->post('receiptVoucherAutoId') ?? ''),$amount,'RV',trim($this->input->post('receiptVoucherDetailAutoID') ?? ''),$data['discountAmount'],1);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher Detail : Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Detail : Updated Successfully.');
        }

    }

    function receipt_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $wacRecalculationEnableYN = getPolicyValues('WACR','All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $RVcode = 0;
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $mastertbl = $this->db->query("SELECT RVdate, RVchequeDate FROM `srp_erp_customerreceiptmaster` where companyID = $companyID And receiptVoucherAutoId = $receiptVoucherAutoId ")->row_array();
        $mastertbldetail = $this->db->query("SELECT receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail` WHERE companyID = $companyID AND type = 'Item' AND receiptVoucherAutoId = $receiptVoucherAutoId")->row_array();
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $currentdate = current_date(false);
        $this->load->library('Approvals');
        $this->db->select('receiptVoucherAutoId');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
            $rvid = $this->input->post('receiptVoucherAutoId');
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

            if ($totalamnt || $totalamnt == 0) {
                $this->db->select('documentID, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
                    $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                $this->db->update('srp_erp_customerreceiptmaster', $tamount);

                $this->db->select('documentID,receiptVoucherAutoId, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID,RVdate');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction', 'itemAutoID' => $item_low_qty);
                }

                $autoApproval = get_document_auto_approval('RV');
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
                    $documentID = trim($this->input->post('receiptVoucherAutoId') ?? '');
                    $validate = $this->validate_itemMasterSub($documentID);
                    /** end of item master sub */
                    if ($validate) {
                        $autoApproval = get_document_auto_approval('RV');

                        $updatedBatchNumberArray=[];

                        if($itemBatchPolicy==1){
                            $this->db->select('*');
                            $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                            $this->db->from('srp_erp_customerreceiptdetail');
                            $invoice_results = $this->db->get()->result_array();
                            $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);
                        }

                        if ($autoApproval == 0) {
                            $result = $this->save_rv_approval(0, $app_data['receiptVoucherAutoId'], 1, 'Auto Approved',$updatedBatchNumberArray);
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
                }
            }
        }
    }

    function receipt_confirmation_suom()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $mastertbl = $this->db->query("SELECT RVdate, RVchequeDate,transactionCurrencyDecimalPlaces FROM `srp_erp_customerreceiptmaster` where companyID = $companyID And receiptVoucherAutoId = $receiptVoucherAutoId ")->row_array();
        $mastertbldetail = $this->db->query("SELECT receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail` WHERE companyID = $companyID AND type = 'Item' AND receiptVoucherAutoId = $receiptVoucherAutoId")->row_array();
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $currentdate = current_date(false);
        $this->load->library('Approvals');
        $this->db->select('receiptVoucherAutoId');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptdetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $rvid = $this->input->post('receiptVoucherAutoId');
            $taxamnt = 0;
            $GL = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='GL'
GROUP BY receiptVoucherAutoId")->row_array();

            if (empty($GL)) {
                $GL = 0;
            } else {
                $GL = $GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT
SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Item'
GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Item)) {
                $Item = 0;
            } else {
                $Item = $Item['transactionAmount'];
            }
            $creditnote = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='creditnote'
GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($creditnote)) {
                $creditnote = 0;
            } else {
                $creditnote = $creditnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Advance'
GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Advance)) {
                $Advance = 0;
            } else {
                $Advance = $Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT
	SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.receiptVoucherAutoId = $rvid
	AND srp_erp_customerreceiptdetail.type='Invoice'
GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($Invoice)) {
                $Invoice = 0;
            } else {
                $Invoice = $Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT
	SUM(srp_erp_customerreceipttaxdetails.taxPercentage) as taxPercentage
FROM
	srp_erp_customerreceipttaxdetails
WHERE
	srp_erp_customerreceipttaxdetails.receiptVoucherAutoId = $rvid

GROUP BY receiptVoucherAutoId")->row_array();
            if (empty($tax)) {
                $tax = 0;
            } else {
                $tax = $tax['taxPercentage'];
                $taxamnt = (($Item + $GL) / 100) * $tax;
            }
            $totalamnt = ($Item + $GL + $Invoice + $Advance + $taxamnt) - $creditnote;

            if ($totalamnt < 0) {
                return array('error' => 1, 'message' => 'Grand total should be greater than 0');
            } else {
                $payAmount = $this->db->query("SELECT COALESCE(sum(amount), 0) AS payAmount
                FROM srp_erp_customerreceiptpayments 
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_customerreceiptpayments.bankGLAutoID
                WHERE srp_erp_customerreceiptpayments.companyID = {$companyID} AND receiptVoucherAutoId = {$receiptVoucherAutoId}")->row_array();

                if(round($totalamnt,$mastertbl['transactionCurrencyDecimalPlaces']) > $payAmount['payAmount']){
                    return array('error' => 2, 'message' => 'Payment Amount should be equal to Receipt Amount');
                } else {
                    $this->db->select('documentID, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                    $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
                        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
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
                    $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                    $this->db->update('srp_erp_customerreceiptmaster', $tamount);

                    $this->db->select('documentID,receiptVoucherAutoId, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID,RVdate');
                    $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                    $this->db->from('srp_erp_customerreceiptmaster');
                    $app_data = $this->db->get()->row_array();

                    $sql = "SELECT (srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock-(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM)) as stock ,srp_erp_warehouseitems.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID FROM srp_erp_customerreceiptdetail INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where receiptVoucherAutoId = '{$this->input->post('receiptVoucherAutoId')}' AND itemCategory != 'Service' Having stock < 0";
                    $item_low_qty = $this->db->query($sql)->result_array();
                    if (!empty($item_low_qty)) {
                        //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                        return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction', 'itemAutoID' => $item_low_qty);
                    }


                    $autoApproval = get_document_auto_approval('RV');

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
                        exit;
                    }
 

                    if ($approvals_status == 1) {

                        /** item Master Sub check */
                        $documentID = trim($this->input->post('receiptVoucherAutoId') ?? '');
                        $validate = $this->validate_itemMasterSub($documentID);

                        /** end of item master sub */
                        if ($validate) {
                            $autoApproval = get_document_auto_approval('RV');

                            if ($autoApproval == 0) {
                                $result = $this->save_rv_approval(0, $app_data['receiptVoucherAutoId'], 1, 'Auto Approved');
                                if ($result) {
                                    return array('error' => 0, 'message' => 'Document Confirmed Successfully!');
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
                                return array('error' => 0, 'message' => 'Document Confirmed Successfully!');
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
    }

    function Receipt_match_confirmation()
    {
        $this->db->select('matchID');
        $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
        $this->db->from('srp_erp_rvadvancematchdetails');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('matchSystemCode');
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $this->db->from('srp_erp_rvadvancematch');
            $mas_dt = $this->db->get()->row_array();
            $validate_code = validate_code_duplication($mas_dt['matchSystemCode'], 'matchSystemCode', trim($this->input->post('matchID') ?? ''),'matchID', 'srp_erp_rvadvancematch');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return array(false, 'error');
            }

            /** Added (Task=> SME-3020)*/
            $this->load->model('Double_entry_model');
            $generalledger_arr = array();
            $double_entry = $this->Double_entry_model->fetch_double_entry_receipt_match_data(trim($this->input->post('matchID') ?? ''), 'RVM');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['matchID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['matchSystemCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['matchDate'];
                $generalledger_arr[$i]['documentType'] = null;
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['matchDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['matchDate']));
                $generalledger_arr[$i]['documentNarration'] = isset($double_entry['gl_detail'][$i]['gl_remarks']) ? $double_entry['gl_detail'][$i]['gl_remarks'] : $double_entry['master_data']['Narration'];;
                $generalledger_arr[$i]['chequeNumber'] = null;
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
//                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
//                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
//                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
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
            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
            }
            /** End of Task=> SME-3020  */

            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user']
            );
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $confirmation = $this->db->update('srp_erp_rvadvancematch', $data);
            if ($confirmation) {
                return array('error' => 0, 'message' => 'Document Confirmed Successfully !');
            } else {
                return array('error' => 1, 'message' => 'Document Confirmation failed !');
            }
        }
    }

    function delete_item_direct()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
        $detail_arr = $this->db->get()->row_array();

        $multiple_currencies_allowed = getPolicyValues('RVMC', 'All');

        if ($detail_arr['type'] == 'Invoice') {
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $detail_arr['invoiceAutoID'];
            if($multiple_currencies_allowed == 1){
                $number = $detail_arr['invoiceAmount'];
            }else{
                $number = $detail_arr['transactionAmount'];
            }
            
            $status = 0;
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number})  , receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }


        /** update sub item master */

        $dataTmp['isSold'] = null;
        $dataTmp['soldDocumentAutoID'] = null;
        $dataTmp['soldDocumentDetailID'] = null;
        $dataTmp['soldDocumentID'] = null;
        $dataTmp['modifiedPCID'] = current_pc();
        $dataTmp['modifiedUserID'] = current_userID();
        $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

        $this->db->where('soldDocumentAutoID', $detail_arr['receiptVoucherAutoId']);
        $this->db->where('soldDocumentDetailID', $detail_arr['receiptVoucherDetailAutoID']);
        $this->db->where('soldDocumentID', 'RV');
        $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

        /** end update sub item master */

        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster', $detail_arr['receiptVoucherAutoId'],'RV', 'receiptVoucherAutoId');
        if($isGroupByTax == 1){ 
            $this->db->delete('srp_erp_taxledger', array('documentID' => 'RV','documentMasterAutoID' => $detail_arr['receiptVoucherAutoId'],'documentDetailAutoID' => trim($this->input->post('receiptVoucherDetailAutoID') ?? '')));
        }

        $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
        $results = $this->db->delete('srp_erp_customerreceiptdetail');


        if ($results) {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail Deleted Successfully');
            return true;
        }
    }

    function save_rv_advance_detail()
    {
        $amounts = $this->input->post('amount');
        $description = $this->input->post('description');
        $projectID = $this->input->post('projectID');
        $contractAutoID = $this->input->post('contractAutoID_advance');
        $item_tax = $this->input->post('item_text_advance');
        $this->db->trans_start();
        $this->db->select('customerID, transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode,customerreceivableAutoID,customerreceivableSystemGLCode,customerreceivableGLAccount,customerreceivableDescription,customerreceivableType');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

        foreach ($amounts as $key => $amount) {
            $data['contractAutoID'] = trim($contractAutoID[$key] ?? '');
            $data['transactionAmount'] = trim($amount);
            $data['segmentID'] = $master['segmentID'];
            $data['projectID'] = $projectID[$key] ?? null;
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $master['customerreceivableAutoID'];
            $data['SystemGLCode'] = $master['customerreceivableSystemGLCode'];
            $data['GLCode'] = $master['customerreceivableGLAccount'];
            $data['GLDescription'] = $master['customerreceivableDescription'];
            $data['GLType'] = $master['customerreceivableType'];
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data['customerAmount'] = ($data['transactionAmount'] / $master['customerExchangeRate']);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $data['comment'] = trim($description[$key] ?? '');
            $data['type'] = 'Advance';
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($this->input->post('receiptVoucherDetailAutoID') ?? '')) {

            } else {
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_customerreceiptdetail', $data);
                $last_id = $this->db->insert_id();

                $companyID = current_companyID();
                $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId') ?? '');
                $vatRegisterYN = $this->db->query("SELECT vatRegisterYN FROM srp_erp_company where company_id = $companyID ")->row('vatRegisterYN');
                if($contractAutoID && $contractAutoID[$key]) {
                    $CNT_VAT_Exist = $this->db->query("SELECT
                                                            customerCountryID,
                                                            vatEligible,
                                                            customerID,
                                                            srp_erp_taxledger.*, 
                                                            SUM(amount) as amount,
                                                            srp_erp_taxmaster.outputVatTransferGLAccountAutoID, 
                                                            outputVatGLAccountAutoID
                                                        FROM 
                                                            srp_erp_taxledger 
                                                        JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                                        JOIN srp_erp_contractmaster ON srp_erp_contractmaster.contractAutoID = srp_erp_taxledger.documentMasterAutoID
	                                                    JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID
                                                        WHERE 
                                                            srp_erp_taxledger.documentID = 'CNT' 
                                                            AND taxCategory = 2
                                                            AND srp_erp_taxledger.companyID = {$companyID} 
                                                            AND documentMasterAutoID =  {$contractAutoID[$key]}")->row_array();

                    if($CNT_VAT_Exist) {
                        $contractAmount = $this->db->query("SELECT
                                                                    SUM(transactionAmount + IFNULL(taxAmount, 0)) AS contractAmount
                                                                FROM
                                                                    srp_erp_contractdetails
                                                                WHERE
                                                                    contractAutoID = {$contractAutoID[$key]}
                                                                GROUP BY
                                                                    contractAutoID")->row('contractAmount');

                        $dataleg['documentID'] = 'RV';
                        $dataleg['documentMasterAutoID'] = $receiptVoucherAutoId;
                        $dataleg['documentDetailAutoID'] = $last_id;
                        $dataleg['taxDetailAutoID'] = null;
                        $dataleg['taxPercentage'] = 0;
                        $dataleg['ismanuallychanged'] = 0;
                        $dataleg['isAdvance'] = 1;
                        $dataleg['taxFormulaMasterID'] = null;
                        $dataleg['taxFormulaDetailID'] = null;
                        $dataleg['taxMasterID'] = $CNT_VAT_Exist['taxMasterID'];
                        $dataleg['amount'] = ($CNT_VAT_Exist['amount'] / $contractAmount) * trim($amount);
                        $dataleg['formula'] = null;
                        $dataleg['isClaimable'] = $vatRegisterYN;
                        $dataleg['rcmApplicableYN'] = 0;
                        $dataleg['taxGlAutoID'] = $CNT_VAT_Exist['taxGlAutoID'];
                        $dataleg['outputVatTransferGL'] = $CNT_VAT_Exist['outputVatTransferGLAccountAutoID'];
                        $dataleg['outputVatGL'] = $CNT_VAT_Exist['outputVatGLAccountAutoID'];
                        $dataleg['transferGLAutoID'] = $CNT_VAT_Exist['outputVatTransferGLAccountAutoID'];
                        $dataleg['countryID'] = $CNT_VAT_Exist['customerCountryID'];
                        $dataleg['partyVATEligibleYN'] = $CNT_VAT_Exist['vatEligible'];
                        $dataleg['partyID'] = $CNT_VAT_Exist['customerID'];
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

                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $dataleg['amount'];
                        $this->db->where('receiptVoucherDetailAutoID', $last_id);
                        $this->db->update('srp_erp_customerreceiptdetail', $data_detailTBL);
                    }

                } else if(($contractAutoID && !($contractAutoID[$key])) && $item_tax[$key]) {
                    $this->db->select("*, srp_erp_taxcalculationformuladetails.taxPercentage as taxPercentage");
                    $this->db->join("srp_erp_taxmaster","srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID","LEFT");
                    $this->db->where('taxCalculationformulaID', $item_tax[$key]);
                    $this->db->where('taxCategory', 2);
                    $formulaDtl = $this->db->get("srp_erp_taxcalculationformuladetails")->row_array();

                    $this->db->select("customerCountryID, vatEligible, customerAutoID");
                    $this->db->where('customerAutoID', $master['customerID']);
                    $vatDetails_cus = $this->db->get("srp_erp_customermaster")->row_array();

                    $dataleg['documentID'] = 'RV';
                    $dataleg['documentMasterAutoID'] = $receiptVoucherAutoId;
                    $dataleg['documentDetailAutoID'] = $last_id;
                    $dataleg['taxDetailAutoID'] = null;
                    $dataleg['taxPercentage'] = 0;
                    $dataleg['ismanuallychanged'] = 0;
                    $dataleg['isAdvance'] = 1;
                    $dataleg['taxFormulaMasterID'] = $formulaDtl['taxCalculationformulaID'];
                    $dataleg['taxFormulaDetailID'] = $formulaDtl['formulaDetailID'];
                    $dataleg['taxMasterID'] = $formulaDtl['taxMasterAutoID'];
                    $dataleg['amount'] = ($formulaDtl['taxPercentage'] / (100 + $formulaDtl['taxPercentage'])) * trim($amount);
                    $dataleg['formula'] = null;
                    $dataleg['rcmApplicableYN'] = 0;
                    $dataleg['isClaimable'] = $vatRegisterYN;
                    $dataleg['taxGlAutoID'] = $formulaDtl['outputVatTransferGLAccountAutoID'];
                    $dataleg['outputVatTransferGL'] = $formulaDtl['outputVatTransferGLAccountAutoID'];
                    $dataleg['outputVatGL'] = $formulaDtl['outputVatGLAccountAutoID'];
                    $dataleg['transferGLAutoID'] = $formulaDtl['outputVatTransferGLAccountAutoID'];
                    $dataleg['countryID'] = $vatDetails_cus['customerCountryID'];
                    $dataleg['partyVATEligibleYN'] = $vatDetails_cus['vatEligible'];
                    $dataleg['partyID'] = $vatDetails_cus['customerAutoID'];
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

                    $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                    $data_detailTBL['taxAmount'] = $dataleg['amount'];
                    $this->db->where('receiptVoucherDetailAutoID', $last_id);
                    $this->db->update('srp_erp_customerreceiptdetail', $data_detailTBL);

                }
            }
        }

        $last_id = 0;
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Receipt Voucher Detail  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Receipt Voucher Detail Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function save_rv_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0,$updatedBatchNumberArray=[])
    {
        $batchNumberPolicy = getPolicyValues('IB', 'All');
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

        /*$sql = "SELECT srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, srp_erp_warehouseitems.currentStock as availableStock,
                SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,
                (srp_erp_warehouseitems.currentStock- SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM)) as stock ,
                srp_erp_warehouseitems.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID 
                FROM srp_erp_customerreceiptdetail 
                INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID
                 JOIN srp_erp_itemmaster ON srp_erp_customerreceiptdetail.itemAutoID = srp_erp_itemmaster.itemAutoID 
                AND srp_erp_customerreceiptdetail.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID 
                where receiptVoucherAutoId = '{$system_id}' AND itemCategory != 'Service' AND  itemCategory != 'Non Inventory'  
                GROUP BY itemAutoID
                Having stock < 0";*/
        $companyID = current_companyID();
        /* $sql = "SELECT  srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription,
                    SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,
                    IFNULL(warehouse.currentStock , 0) as availableStock, IFNULL(warehouse.currentStock , 0) as currentStock,
                    (IFNULL(warehouse.currentStock, 0) - IFNULL( SUM(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM),0)) as stock ,
                    warehouse.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID 
                        FROM srp_erp_customerreceiptdetail 
                         JOIN srp_erp_itemmaster ON srp_erp_customerreceiptdetail.itemAutoID = srp_erp_itemmaster.itemAutoID
                        LEFT JOIN (SELECT
                                    SUM( transactionQTY / convertionRate ) AS currentStock,
                                    wareHouseAutoID,
                                    itemAutoID 
                                FROM
                                    srp_erp_itemledger 
                                WHERE
                                    companyID = {$companyID}
                                GROUP BY
                                    wareHouseAutoID,
                                    itemAutoID )warehouse ON warehouse.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = warehouse.wareHouseAutoID
                        where receiptVoucherAutoId = '{$system_id}' AND itemCategory != 'Service' AND 
                        itemCategory != 'Non Inventory'  
                        GROUP BY itemAutoID
                        Having stock < 0"; */
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
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] =='Service') {
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
                    //$this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                    if($batchNumberPolicy==1){

                        foreach($itemledger_arr  as $key1=>$ledger){
    
                            if(count($updatedBatchNumberArray)>0){
                                foreach($updatedBatchNumberArray as $bKey=>$batch){
                                    if($ledger['itemAutoID']==$batch['itemAutoID'] && $ledger['wareHouseAutoID']==$batch['wareHouseAutoID']){
                                        $itemledger_arr[]=[
                                            'documentID'=>$ledger['documentID'],
                                            'documentCode'=>$ledger['documentCode'],
                                            'documentAutoID'=>$ledger['documentAutoID'],
                                            'documentSystemCode'=>$ledger['documentSystemCode'],
                                            'documentDate'=>$ledger['documentDate'],
                                            'referenceNumber'=>$ledger['referenceNumber'],
                                            'companyFinanceYearID'=>$ledger['companyFinanceYearID'],
                                            'companyFinanceYear'=>$ledger['companyFinanceYear'],
                                            'FYBegin'=>$ledger['FYBegin'],
                                            'FYEnd'=>$ledger['FYEnd'],
                                            'FYPeriodDateFrom'=>$ledger['FYPeriodDateFrom'],
                                            'FYPeriodDateTo'=>$ledger['FYPeriodDateTo'],
                                            'wareHouseAutoID'=>$ledger['wareHouseAutoID'],
                                            'wareHouseCode'=>$ledger['wareHouseCode'],
                                            'wareHouseLocation'=>$ledger['wareHouseLocation'],
                                            'wareHouseDescription'=>$ledger['wareHouseDescription'],
                                            'itemAutoID'=>$ledger['itemAutoID'],
                                            'itemSystemCode'=>$ledger['itemSystemCode'],
                                            'itemDescription'=>$ledger['itemDescription'],
                                            'SUOMID'=>$ledger['SUOMID'],
                                            'SUOMQty'=>$ledger['SUOMQty'],
                                            'defaultUOMID'=>$ledger['defaultUOMID'],
                                            'defaultUOM'=>$ledger['defaultUOM'],
                                            'transactionUOM'=>$ledger['transactionUOM'],
                                            'transactionUOMID'=>$ledger['transactionUOMID'],
                                            'transactionQTY'=>$batch['qty'],
                                            'batchNumber'=>$batch['batchNumber'],
                                            'convertionRate'=>$ledger['convertionRate'],
                                            'currentStock'=>$ledger['currentStock'],
                                            'PLGLAutoID'=>$ledger['PLGLAutoID'],
                                            'PLSystemGLCode'=>$ledger['PLSystemGLCode'],
                                            'PLGLCode'=>$ledger['PLGLCode'],
                                            'PLDescription'=>$ledger['PLDescription'],
                                            'PLType'=>$ledger['PLType'],
                                            'BLGLAutoID'=>$ledger['BLGLAutoID'],
                                            'BLSystemGLCode'=>$ledger['BLSystemGLCode'],
                                            'BLGLCode'=>$ledger['BLGLCode'],
                                            'BLDescription'=>$ledger['BLDescription'],
                                            'BLType'=>$ledger['BLType'],
                                            'transactionAmount'=>$ledger['transactionAmount'],
                                            'transactionCurrencyID'=>$ledger['transactionCurrencyID'],
                                            'transactionCurrency'=>$ledger['transactionCurrency'],
                                            'transactionExchangeRate'=>$ledger['transactionExchangeRate'],
                                            'transactionCurrencyDecimalPlaces'=>$ledger['transactionCurrencyDecimalPlaces'],
                                            'companyLocalCurrencyID'=>$ledger['companyLocalCurrencyID'],
                                            'companyLocalCurrency'=>$ledger['companyLocalCurrency'],
                                            'companyLocalExchangeRate'=>$ledger['companyLocalExchangeRate'],
                                            'companyLocalCurrencyDecimalPlaces'=>$ledger['companyLocalCurrencyDecimalPlaces'],
                                            'companyLocalAmount'=>$ledger['companyLocalAmount'],
                                            'companyLocalWacAmount'=>$ledger['companyLocalWacAmount'],
                                            'companyReportingCurrencyID'=>$ledger['companyReportingCurrencyID'],
                                            'companyReportingCurrency'=>$ledger['companyReportingCurrency'],
                                            'companyReportingExchangeRate'=>$ledger['companyReportingExchangeRate'],
                                            'companyReportingCurrencyDecimalPlaces'=>$ledger['companyReportingCurrencyDecimalPlaces'],
                                            'companyReportingAmount'=>$ledger['companyReportingAmount'],
                                            'companyReportingWacAmount'=>$ledger['companyReportingWacAmount'],
                                            'partyCurrencyID'=>$ledger['partyCurrencyID'],
                                            'partyCurrency'=>$ledger['partyCurrency'],
                                            'partyCurrencyExchangeRate'=>$ledger['partyCurrencyExchangeRate'],
                                            'partyCurrencyDecimalPlaces'=>$ledger['partyCurrencyDecimalPlaces'],
                                            'partyCurrencyAmount'=>$ledger['partyCurrencyAmount'],
                                            'confirmedYN'=>$ledger['confirmedYN'],
                                            'confirmedByEmpID'=>$ledger['confirmedByEmpID'],
                                            'confirmedByName'=>$ledger['confirmedByName'],
                                            'confirmedDate'=>$ledger['confirmedDate'],
                                            'approvedYN'=>$ledger['approvedYN'],
                                            'approvedDate'=>$ledger['approvedDate'],
                                            'approvedbyEmpID'=>$ledger['approvedbyEmpID'],
                                            'approvedbyEmpName'=>$ledger['approvedbyEmpName'],
                                            'segmentID'=>$ledger['segmentID'],
                                            'segmentCode'=>$ledger['segmentCode'],
                                            'companyID'=>$ledger['companyID'],
                                            'companyCode'=>$ledger['companyCode'],
                                            'createdUserGroup'=>$ledger['createdUserGroup'],
                                            'createdPCID'=>$ledger['createdPCID'],
                                            'createdUserID'=>$ledger['createdUserID'],
                                            'createdDateTime'=>$ledger['createdDateTime'],
                                            'createdUserName'=>$ledger['createdUserName'],
                                            'modifiedPCID'=>$ledger['modifiedPCID'],
                                            'modifiedUserID'=>$ledger['modifiedUserID'],
                                            'modifiedDateTime'=>$ledger['modifiedDateTime'],
                                            'modifiedUserName'=>$ledger['modifiedUserName'],
    
                                           
                                        ];
                                    }
            
                                }
                            }
    
                            unset($itemledger_arr[$key1]);
    
                        }
    
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
        
                    }else{
                        $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                    }
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
                $bankledger_arr['transactionAmount'] = $amount;
                $bankledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                $bankledger_arr['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                $bankledger_arr['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                $bankledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $bankledger_arr['partyCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['partyCurrencyExchangeRate']);
                $bankledger_arr['bankCurrencyID'] = $double_entry['master_data']['bankCurrencyID'];
                $bankledger_arr['bankCurrency'] = $double_entry['master_data']['bankCurrency'];
                $bankledger_arr['bankCurrencyExchangeRate'] = $double_entry['master_data']['bankCurrencyExchangeRate'];
                $bankledger_arr['bankCurrencyAmount'] = $bankledger_arr['bankCurrencyExchangeRate'] != 0 ? ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']) : 0;
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

    function delete_receipt_voucher()
    {
        /*$this->db->select('type,invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId') ?? ''));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach ($detail_arr as $val_as) {
            if ($val_as['type'] == 'Invoice') {
                $match_id = $val_as['invoiceAutoID'];
                $number = $val_as['transactionAmount'];
                $status = 0;
                $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number})  , receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
            }
        }
        $this->db->delete('srp_erp_customerreceiptmaster', array('receiptVoucherAutoId' => trim($this->input->post('receiptVoucherId') ?? '')));
        $this->db->delete('srp_erp_customerreceiptdetail', array('receiptVoucherAutoId' => trim($this->input->post('receiptVoucherId') ?? '')));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId') ?? ''));
        $datas = $this->db->get()->row_array();

        $this->db->select('RVcode');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId') ?? ''));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            if ($master['RVcode'] == "0") {
                $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherId'));
                $results = $this->db->delete('srp_erp_customerreceiptmaster');
                if ($results) {
                    $this->db->where('receiptVoucherAutoId', $this->input->post('receiptVoucherId'));
                    $this->db->delete('srp_erp_customerreceiptdetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId') ?? ''));
                $this->db->update('srp_erp_customerreceiptmaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }
    }

    function delete_receipt_voucher_attachement()
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

    function fetch_income_all_detail()
    {
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.seconeryItemCode');
        $this->db->where('receiptVoucherDetailAutoID', $this->input->post('receiptVoucherDetailAutoID'));
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID','Left');
        return $this->db->get('srp_erp_customerreceiptdetail')->row_array();
    }

    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_customerreceiptmaster masterTbl
                    LEFT JOIN srp_erp_customerreceiptdetail detailTbl ON masterTbl.receiptVoucherAutoId = detailTbl.receiptVoucherAutoId
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = detailTbl.receiptVoucherDetailAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.receiptVoucherAutoId = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();
        //echo $this->db->last_query();

        $query2 = "SELECT
                        SUM(detailTbl.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerreceiptmaster masterTbl
                    LEFT JOIN srp_erp_customerreceiptdetail detailTbl ON masterTbl.receiptVoucherAutoId = detailTbl.receiptVoucherAutoId
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = detailTbl.itemAutoID
                    WHERE
                        masterTbl.receiptVoucherAutoId = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";


        $r2 = $this->db->query($query2)->row_array();

        /*print_r($r1);
        print_r($r2);
        exit;*/

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

    function re_open_receipt_voucher()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherId') ?? ''));
        $this->db->update('srp_erp_customerreceiptmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_receipt_match()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
        $this->db->update('srp_erp_rvadvancematch', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_itemrecode_po()
    {
        $isallow =$_GET['column'];  
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT 
                                        mainCategory,mainCategoryID,subcategoryID, seconeryItemCode,subSubCategoryID,revanueGLCode, itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,
                                        defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock, companyLocalWacAmount,companyLocalSellingPrice, isSubitemExist,srp_erp_itemmaster.secondaryUOMID as secondaryUOMID,
                                        CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match"
                                    FROM srp_erp_itemmaster 
                                    WHERE 
                                        (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '" OR barcode LIKE "' . $search_string . '" OR partNo LIKE "' . $search_string . '") 
                                        AND financeCategory != 3 
                                        AND companyID = "' . $companyID .'" 
                                        AND isActive = "1" 
                                        AND '.$isallow.' = "1"  
                                        AND masterApprovedYN = "1" ')->result_array(); //AND isActive = "1" AND '.$isallow.' = "1" 
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist'], 'revanueGLCode' => $val['revanueGLCode'], 'mainCategory' => $val['mainCategory'], 'secondaryUOMID' => $val['secondaryUOMID']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_rv_warehouse_item()
    {

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $itemAutoID = $this->input->post('itemAutoID');

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }


        if (!empty($data) && ($data['currentStock']>0)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $currentStock, 'WacAmount' => $data['companyLocalWacAmount'], 'mainCategory' => $data['mainCategory']);
        } else {
          
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse ");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse");
        }
    }


    function updateReceiptVoucher_edit_all_Item()
    {
        $itemAutoIDs = $this->input->post('itemAutoID');
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $projectExist = project_is_exist();

        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $comment = $this->input->post('comment');
        $remarks = $this->input->post('remarks');

        $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate, customerCurrency,customerExchangeRate,customerCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_customerreceiptmaster');
        $master = $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {
            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm = $this->db->get()->row_array();

           /* if (!trim($receiptVoucherDetailAutoID[$key])) {
                $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerreceiptdetail');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if($serviceitm['mainCategory']=="Inventory") {
                    if (!empty($order_detail)) {
                        return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }
            } else {
                $this->db->select('receiptVoucherAutoId,,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_customerreceiptdetail');
                $this->db->where('receiptVoucherAutoId', trim($this->input->post('receiptVoucherAutoId') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('receiptVoucherDetailAutoID !=', $receiptVoucherDetailAutoID[$key]);
                $order_detail = $this->db->get()->row_array();
                if($serviceitm['mainCategory']=="Inventory") {
                    if (!empty($order_detail)) {
                        return array('w', 'Receipt Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                    }
                }
            }*/

            $wareHouse_location = explode('|', trim($wareHouse[$key]));
            $item_data = fetch_item_data(trim($itemAutoID));

            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $item_data['itemAutoID']);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();

                if (empty($warehouseitems)) {
                    $data_arr = array(
                        'wareHouseAutoID' => $wareHouseAutoID[$key],
                        'wareHouseLocation' => $wareHouse_location[1],
                        'wareHouseDescription' => $wareHouse_location[2],
                        'itemAutoID' => $item_data['itemAutoID'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
                        'itemSystemCode' => $item_data['itemSystemCode'],
                        'itemDescription' => $item_data['itemDescription'],
                        'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                        'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                        'currentStock' => 0,
                        'companyID' => $this->common_data['company_data']['company_id'],
                        'companyCode' => $this->common_data['company_data']['company_code'],
                    );

                    $this->db->insert('srp_erp_warehouseitems', $data_arr);
                }
            }


            $uomDesc = explode('|', $uom[$key]);
            $data['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
            $data['itemAutoID'] = trim($itemAutoID);
            $data['itemSystemCode'] = $item_data['itemSystemCode'];
            $data['itemDescription'] = $item_data['itemDescription'];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['unitOfMeasure'] = trim($uomDesc[0] ?? '');
            $data['unitOfMeasureID'] = trim($UnitOfMeasureID[$key]);
            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = trim($quantityRequested[$key]);
            $data['unittransactionAmount'] = trim($estimatedAmount[$key]);
            $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($quantityRequested[$key]));
            $data['comment'] = trim($comment[$key]);
            $data['remarks'] = trim($remarks[$key]);
            $data['type'] = 'Item';
            if ($serviceitm['mainCategory'] != 'Service') {
                $data['wareHouseAutoID'] = trim($wareHouseAutoID[$key]);
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
            } else {
                $data['wareHouseAutoID'] = null;
                $data['wareHouseCode'] = null;
                $data['wareHouseLocation'] = null;
                $data['wareHouseDescription'] = null;
            }
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data['customerCurrencyExchangeRate'] = $master['customerExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $data['companyReportingExchangeRate']);

            $data['customerAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['customerAmount'] = ($data['transactionAmount'] / $data['customerCurrencyExchangeRate']);
            }


            $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $data['companyLocalExchangeRate']);
            $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $data['companyReportingExchangeRate']);

            $data['unitpartyAmount'] = 0;
            if ($data['customerCurrencyExchangeRate']) {
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $data['customerCurrencyExchangeRate']);
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){

                $batch_number2 = $this->input->post('batch_number['.$key.']');
                $arraydata2 = implode(',',$batch_number2);
                $data['batchNumber'] = $arraydata2;
                
            }


            $data['segmentID'] = $master['segmentID'];
            $data['segmentCode'] = $master['segmentCode'];
            $data['GLAutoID'] = $item_data['revanueGLAutoID'];
            $data['systemGLCode'] = $item_data['revanueSystemGLCode'];
            $data['GLCode'] = $item_data['revanueGLCode'];
            $data['GLDescription'] = $item_data['revanueDescription'];
            $data['GLType'] = $item_data['revanueType'];
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
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            if (trim($receiptVoucherDetailAutoID[$key])) {
                $this->db->where('receiptVoucherDetailAutoID', trim($receiptVoucherDetailAutoID[$key]));
                $this->db->update('srp_erp_customerreceiptdetail', $data);
                $this->db->trans_complete();
            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerreceiptdetail', $data);

                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', trim($itemAutoID));
                $this->db->where('wareHouseAutoID', $data['wareHouseAutoID']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Records Insertion error');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Inserted successfully');
        }
    }


    function fetch_rv_details_all()
    {
        $receiptVoucherAutoId = trim($this->input->post('receiptVoucherAutoId') ?? '');
       
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,srp_erp_itemmaster.itemSystemCode as itemSystemCode ');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Item');

        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'RV');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function fetch_credit_note($customerID, $currencyID, $documentDate)
    {
        $tmpDate = format_date($documentDate);
        $companyID = current_companyID();

        $multiple_currencies_allowed = getPolicyValues('RVMC', 'All');

        $currency_filter = '';
             if($multiple_currencies_allowed != 1){
                 $currency_filter .= " AND masterTbl.transactionCurrencyID = '$currencyID'";
             }
        /*$output = $this->db->query("SELECT
                    masterTbl.creditNoteMasterAutoID AS creditNoteMasterAutoID,
                    masterTbl.creditNoteCode AS creditNoteCode,
                    detailTbl.transactionAmount,
                    masterTbl.docRefNo AS RefNo,
                    SUM(crDetail.transactionAmount) AS RVTransactionAmount
                FROM
                    srp_erp_creditnotemaster masterTbl
                LEFT JOIN (
                    SELECT
                        SUM(transactionAmount) AS transactionAmount,
                        creditNoteMasterAutoID
                    FROM
                        srp_erp_creditnotedetail
                    WHERE
                        (
                            ISNULL(InvoiceAutoID)
                            OR InvoiceAutoID = 0
                        )
                    GROUP BY
                        creditNoteMasterAutoID
                ) detailTbl ON detailTbl.creditNoteMasterAutoID = masterTbl.creditNoteMasterAutoID
                LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.creditNoteMasterAutoID
                WHERE
                    masterTbl.confirmedYN = 1
                AND masterTbl.approvedYN = 1
                AND masterTbl.transactionCurrencyID = '$currencyID'
                AND masterTbl.creditNoteDate <= '$tmpDate'
                AND masterTbl.customerID = '$customerID'
                GROUP BY
                    masterTbl.creditNoteMasterAutoID")->result_array();*/


        $output = $this->db->query("SELECT
                        * 
                    FROM
                        (
                            SELECT
                                masterTbl.creditNoteMasterAutoID AS creditNoteMasterAutoID,
                                masterTbl.creditNoteCode AS creditNoteCode,
                                detailTbl.transactionAmount,
                                masterTbl.docRefNo AS RefNo,
                                masterTbl.creditNoteDate AS creditNoteDate,
                                SUM( crDetail.transactionAmount ) AS RVTransactionAmount,
                                'creditnote' AS type,
                                crDetail.invoiceAmount,
                                masterTbl.transactionCurrencyID,
                                masterTbl.transactionCurrency,
                                SUM(crDetail.invoiceAmount) AS invoiceAmountTotal
                            FROM
                                srp_erp_creditnotemaster masterTbl
                                LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM srp_erp_creditnotedetail WHERE ( ISNULL( InvoiceAutoID ) OR InvoiceAutoID = 0 ) GROUP BY creditNoteMasterAutoID ) detailTbl ON detailTbl.creditNoteMasterAutoID = masterTbl.creditNoteMasterAutoID
                                LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.creditNoteMasterAutoID  AND crDetail.type='creditnote'
                            WHERE
                                masterTbl.confirmedYN = 1 
                                AND masterTbl.approvedYN = 1 
                                {$currency_filter}
                                AND masterTbl.creditNoteDate <= '$tmpDate' 
                                AND masterTbl.customerID = '$customerID'
                                AND masterTbl.companyID = '$companyID'  
                            GROUP BY
                                masterTbl.creditNoteMasterAutoID 
                                
                            UNION ALL
                                
                            SELECT
                                masterTbl.salesReturnAutoID AS creditNoteMasterAutoID,
                                masterTbl.salesReturnCode AS creditNoteCode,
                                detailTbl.transactionAmount,
                                masterTbl.returnDate AS creditNoteDate,
                                masterTbl.referenceNo AS RefNo,
                                SUM( crDetail.transactionAmount ) AS RVTransactionAmount,
                                'SLR' AS type,
                                crDetail.invoiceAmount,
                                masterTbl.transactionCurrencyID,
                                masterTbl.transactionCurrency,
                                SUM(crDetail.invoiceAmount) AS invoiceAmountTotal
                            FROM
                                srp_erp_salesreturnmaster masterTbl
                                LEFT JOIN ( SELECT  (SUM( totalValue + IFNULL(taxAmount, 0)) - sum(IFNULL(rebateAmount, 0))) AS transactionAmount, salesReturnAutoID FROM srp_erp_salesreturndetails WHERE companyID = '$companyID' GROUP BY salesReturnAutoID ) detailTbl ON detailTbl.salesReturnAutoID = masterTbl.salesReturnAutoID
                                LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.salesReturnAutoID  AND crDetail.type='SLR'
                            WHERE
                                masterTbl.confirmedYN = 1 
                                AND masterTbl.approvedYN = 1 
                                {$currency_filter} 
                                AND masterTbl.returnDate <= '$tmpDate' 
                                AND masterTbl.customerID = '$customerID' 
                                AND masterTbl.companyID = '$companyID'
                            GROUP BY
                                masterTbl.salesReturnAutoID 
                    ) AS result"
        )->result_array();
        //echo $this->db->last_query();
        return $output;

    }

    function save_creditNote_base_items()
    {
        $this->db->trans_start();
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');

        /** Array */
        $creditNoteMasterAutoIDs = $this->input->post('creditNoteMasterAutoID');
        $amount = $this->input->post('amount');
        $transactionAmountTotal = $this->input->post('transactionAmount');
        $types = $this->input->post('types');

        $multiple_currency = getPolicyValues('RVMC', 'All');

        if (!empty($creditNoteMasterAutoIDs)) {
            $i = 0;
            foreach ($creditNoteMasterAutoIDs as $creditNoteMasterAutoID) {

                $transactionExchangeRate = 1;
                $localAmount = $amount[$i];
                $localExchangeRate = 1;
                $reportingAmount = $amount[$i];
                $reportingExchangeRate = 1;
                $customerAmount = $amount[$i];
                $customerExchangeRate = 1;
                $transactionAmount = 0;
    

                if($types[$i]=='creditnote'){
                    $master_recode = $this->get_creditNote_master($creditNoteMasterAutoID);
                    $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($creditNoteMasterAutoID,'creditnote'); // use this value to get due amount

                    $data[$i]['creditNoteAutoID'] = $creditNoteMasterAutoIDs[$i];
                    $data[$i]['invoiceAutoID'] = null;
                    $data[$i]['type'] = $types[$i];
                    $data[$i]['receiptVoucherAutoId'] = $receiptVoucherAutoId;
                    $data[$i]['invoiceCode'] = $master_recode['creditNoteCode'];
                    $data[$i]['referenceNo'] = $master_recode['docRefNo'];
                    $data[$i]['invoiceDate'] = $master_recode['creditNoteDate'];
                    $data[$i]['description'] = $master_recode['comments'];

                }else{
                    $master_recode = $this->get_salesReturn_master($creditNoteMasterAutoID);
                    $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($creditNoteMasterAutoID,'SLR'); // use this value to get due amount

                    $data[$i]['creditNoteAutoID'] = $creditNoteMasterAutoIDs[$i];
                    $data[$i]['invoiceAutoID'] = null;
                    $data[$i]['type'] = $types[$i];
                    $data[$i]['receiptVoucherAutoId'] = $receiptVoucherAutoId;
                    $data[$i]['invoiceCode'] = $master_recode['salesReturnCode'];
                    $data[$i]['referenceNo'] = $master_recode['referenceNo'];
                    $data[$i]['invoiceDate'] = $master_recode['returnDate'];
                    $data[$i]['description'] = $master_recode['comment'];
                }

                if($types[$i]=='creditnote'){
                    if($multiple_currency == 1){

                        $calculated_amounts = $this->calculate_different_currency_exchange_rates($master_recode,$receiptVoucherAutoId,$amount[$i],'CN',$alreadyPaidAmount);

                        $transactionAmount = $calculated_amounts['transactionAmount'];
                        $transactionExchangeRate = $calculated_amounts['transactionExchangeRate'];
                        $localAmount = $calculated_amounts['localCurrencyAmount'];
                        $localExchangeRate = $calculated_amounts['localCurrencyExchangeRate'];
                        $reportingAmount = $calculated_amounts['reportingCurrencyAmount'];
                        $reportingExchangeRate = $calculated_amounts['reportingCurrencyExchangeRate'];
                        $customerAmount = $calculated_amounts['customerCurrencyAmount'];
                        $customerExchangeRate = $calculated_amounts['customerCurrencyExchangeRate'];
                       
                        $master_recode['companyLocalExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['localCurrencyID']) ? 1 : $localExchangeRate;
                        $master_recode['companyReportingExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['reportingCurrencyID']) ? 1 : $reportingExchangeRate;
                        $master_recode['customerCurrencyExchangeRate'] =  ($calculated_amounts['transactionCurrencyID'] == $calculated_amounts['customerCurrencyID']) ? 1 : $customerExchangeRate;
                    }
                }
               
                if($multiple_currency == 1){
                    $due_amount = ($transactionAmountTotal[$i]) - $alreadyPaidAmount;
                    $balance_amount = $due_amount - $transactionAmount;
                }else{
                    $due_amount = $transactionAmountTotal[$i] - $alreadyPaidAmount;
                    $balance_amount = $due_amount - $amount[$i];
                }

                $data[$i]['GLAutoID'] = $master_recode['customerReceivableAutoID'];
                $data[$i]['systemGLCode'] = $master_recode['customerReceivableSystemGLCode'];
                $data[$i]['GLCode'] = $master_recode['customerReceivableGLAccount'];
                $data[$i]['GLDescription'] = $master_recode['customerReceivableDescription'];
                $data[$i]['GLType'] = $master_recode['customerReceivableType'];

                $data[$i]['Invoice_amount'] = $transactionAmountTotal[$i] * $transactionExchangeRate;
                $data[$i]['due_amount'] = $due_amount * $transactionExchangeRate;
                $data[$i]['balance_amount'] = $balance_amount * $transactionExchangeRate;

                $data[$i]['invoiceAmount'] = $transactionAmount;
                $data[$i]['invoiceExchangeRate'] = $transactionExchangeRate;

                $data[$i]['transactionAmount'] = round($amount[$i], $master_recode['transactionCurrencyDecimalPlaces']);
                $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);

                $data[$i]['customerCurrencyExchangeRate'] = $master_recode['customerCurrencyExchangeRate'];
                $data[$i]['customerAmount'] = ($data[$i]['transactionAmount'] / $master_recode['customerCurrencyExchangeRate']);

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
                $i++;
            }
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_customerreceiptdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Customer Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Records Saved Successfully. ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function get_creditNote_master($id)
    {
        $this->db->select('srp_erp_creditnotemaster.*,SUM(detail.transactionAmount) as transactionAmount,SUM(detail.companyLocalAmount) as companyLocalAmount,SUM(detail.companyReportingAmount) as companyReportingAmount,SUM(detail.customerAmount) as customerCurrencyAmount');
        $this->db->from('srp_erp_creditnotemaster');
        $this->db->join('srp_erp_creditnotedetail as detail','srp_erp_creditnotemaster.creditNoteMasterAutoID = detail.creditNoteMasterAutoID','left');
        $this->db->where('srp_erp_creditnotemaster.creditNoteMasterAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }

    function get_salesReturn_master($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_salesreturnmaster');
        $this->db->where('salesReturnAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }

    function get_debitNote_paymentVoucher_transactionAmount($creditNoteAutoID,$type)
    {
        $multiple_currency = getPolicyValues('RVMC', 'All');

        if($multiple_currency == 1){
            $sumTransactionAmount = $this->db->query("SELECT SUM(invoiceAmount) AS totalTransactionAmount FROM srp_erp_customerreceiptdetail WHERE creditNoteAutoID = '" . $creditNoteAutoID . "' AND type= '" . $type . "'  ")->row('totalTransactionAmount');
        }else{
            $sumTransactionAmount = $this->db->query("SELECT SUM(transactionAmount) AS totalTransactionAmount FROM srp_erp_customerreceiptdetail WHERE creditNoteAutoID = '" . $creditNoteAutoID . "' AND type= '" . $type . "'  ")->row('totalTransactionAmount');
        }
        return $sumTransactionAmount;
    }

    function showBalanceAmount_matching()
    {
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');

        if(!empty($InvoiceAutoID)){
            $data = $this->db->query("SELECT
	srp_erp_customerinvoicemaster.invoiceAutoID,
	invoiceCode,
	receiptTotalAmount,
	advanceMatchedTotal,
	creditNoteTotalAmount,
	referenceNo,
	((( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) - IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0)) AS transactionAmount,
	invoiceDate,
slr.returnsalesvalue as salesreturnvalue,
srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces
FROM
	srp_erp_customerinvoicemaster
LEFT JOIN (
	SELECT
		invoiceAutoID,
		IFNULL(SUM(transactionAmount), 0) AS transactionAmount,
		IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID
LEFT JOIN (
	SELECT
		invoiceAutoID,
		SUM(taxPercentage) AS taxPercentage
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		invoiceAutoID
) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
LEFT JOIN (
	SELECT
		SUM(discountPercentage) AS discountPercentage,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicediscountdetails
	GROUP BY
		invoiceAutoID
) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
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
) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoiceextrachargedetails
	GROUP BY
		invoiceAutoID
) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
LEFT JOIN (
	SELECT
		invoiceAutoID,
		IFNULL(
			SUM(slaesdetail.totalValue),
			0
		) AS returnsalesvalue
	FROM
		srp_erp_salesreturndetails slaesdetail
	GROUP BY
		invoiceAutoID
) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID
WHERE
	srp_erp_customerinvoicemaster.invoiceAutoID=$InvoiceAutoID ")->row_array();
            //echo $this->db->last_query();exit;

            $amount = $data['transactionAmount'] - ($data['receiptTotalAmount'] + $data['creditNoteTotalAmount'] + $data['advanceMatchedTotal']+ $data['salesreturnvalue']);

            if ($amount > 0) {
                $amount = $amount;
            } else {
                $amount = 0;
            }
        }else{
            $data['transactionCurrencyDecimalPlaces']=2;
            $amount = 0;
        }


        return number_format($amount,$data['transactionCurrencyDecimalPlaces']);
    }

    function fetch_rv_details_suom()
    {
        $receiptVoucherAutoId = trim($this->input->post( 'receiptVoucherAutoId'));
        $this->db->select('srp_erp_customerreceiptdetail.*,srp_erp_itemmaster.seconeryItemCode,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.mainCategory,CONCAT_WS(
	\' - \',
IF
	( LENGTH( srp_erp_customerreceiptdetail.`itemDescription` ), `srp_erp_customerreceiptdetail`.`itemDescription`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.`partNo` ), CONCAT(\' Part No : \',`srp_erp_itemmaster`.`partNo`), NULL ),
IF
	( LENGTH( srp_erp_itemmaster.seconeryItemCode ), `srp_erp_itemmaster`.`seconeryItemCode`, NULL )
	) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secuom ');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerreceiptdetail.SUOMID', 'left');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);

        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_income_all_detail_suom()
    {
        $this->db->select('*,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec');
        $this->db->where('receiptVoucherDetailAutoID', $this->input->post('receiptVoucherDetailAutoID'));
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerreceiptdetail.SUOMID', 'left');
        return $this->db->get('srp_erp_customerreceiptdetail')->row_array();
    }
    function totalamountreceipt($receiptVoucherAutoId)
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT
	sum(transactionAmount) as totalamounttransaction 
FROM
	`srp_erp_customerreceiptdetail`
	where
	companyID = '{$companyID}'
	AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GROUP BY
	receiptVoucherAutoId")->row_array();

        return $data;
    }

    function save_receiptVoucher_payment_details()
    {
        $date_format_policy = date_format_policy();
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $payAmounts = $this->input->post('payAmount');
        $payMemo = $this->input->post('payMemo');
        $payBankCode = $this->input->post('payBankCode');
        $paymentMode = $this->input->post('paymentMode');
        $payChequeNo = $this->input->post('payChequeNo');
        $payChequeDate = $this->input->post('payChequeDate');


        foreach ($payAmounts as $key => $payAmount){
            $data['receiptVoucherAutoId'] = $receiptVoucherAutoId;
            $data['bankGLAutoID'] = $payBankCode[$key];
            $data['chequeNo'] = $payChequeNo[$key];
            $data['paymentType'] = $paymentMode[$key];
          //  $bank_detail = fetch_gl_account_desc(trim($payBankCode[$key]));
            if ($paymentMode[$key] == 2) {
                $RVchequeDate = input_format_date(($payChequeDate[$key]), $date_format_policy);
                $data['chequeDate'] = $RVchequeDate;
            } else {
                $data['chequeDate'] = null;
            }
            $data['memo'] = $payMemo[$key];
            $data['amount'] = $payAmounts[$key];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_customerreceiptpayments', $data);

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Detail   Saved Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Detail Saved Successfully.');
        }
    }

    function update_receiptVoucher_payment_details()
    {
        $date_format_policy = date_format_policy();
        $receiptPaymentID = $this->input->post('receiptPaymentID');
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $payAmounts = $this->input->post('edit_payAmount');
        $payMemo = $this->input->post('edit_payMemo');
        $payBankCode = $this->input->post('edit_payBankCode');
        $paymentMode = $this->input->post('edit_paymentMode');
        $payChequeNo = $this->input->post('edit_payChequeNo');
        $payChequeDate = $this->input->post('edit_payChequeDate');

        $data['bankGLAutoID'] = $payBankCode;
        $data['paymentType'] = $paymentMode;
        if ($paymentMode == 2) {
            $RVchequeDate = input_format_date(($payChequeDate), $date_format_policy);
            $data['chequeDate'] = $RVchequeDate;
            $data['chequeNo'] = $payChequeNo;
        } else {
            $data['chequeDate'] = null;
            $data['chequeNo'] = '';
        }

      /*  $bank_detail = fetch_gl_account_desc(trim($payBankCode));
        if ($bank_detail['isCash'] == 0) {
            $RVchequeDate = input_format_date(($payChequeDate), $date_format_policy);
            $data['chequeDate'] = $RVchequeDate;
            $data['chequeNo'] = $payChequeNo;
        } else {
            $data['chequeDate'] = null;
            $data['chequeNo'] = '';
        }*/
        $data['memo'] = $payMemo;
        $data['amount'] = $payAmounts;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('receiptPaymentID', trim($receiptPaymentID));
        $this->db->update('srp_erp_customerreceiptpayments', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Payment Detail Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Payment Details Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function fetch_receipt_payment_details()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $data['details'] = $this->db->query("SELECT receiptPaymentID, 
 CASE srp_erp_customerreceiptpayments.paymentType WHEN 1 THEN 'Cash' WHEN 2 THEN 'Cheque' WHEN 3 THEN 'Bank Transfer' 
       WHEN 4 THEN 'Master Card' WHEN 5 THEN 'Visa Card' END AS paymentMode, 
            receiptVoucherAutoId, chequeNo, chequeDate, memo, amount, CONCAT( bankBranch, ' | ', bankSwiftCode, ' | ',bankAccountNumber, ' | ',subCategory) as bankDetails
                FROM srp_erp_customerreceiptpayments 
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_customerreceiptpayments.bankGLAutoID
                WHERE srp_erp_customerreceiptpayments.companyID = {$companyid} AND receiptVoucherAutoId = {$receiptVoucherAutoId}")->result_array();

        $data['currency'] = $this->db->query("SELECT customerCurrency FROM srp_erp_customerreceiptmaster WHERE companyID = $companyid AND receiptVoucherAutoId = {$receiptVoucherAutoId}")->row_array();
        return $data;
    }

    function delete_payment_details()
    {
        $this->db->where('companyID', trim($this->common_data['company_data']['company_id']));
        $this->db->where('receiptPaymentID', trim($this->input->post('receiptPaymentID') ?? ''));
        $results = $this->db->delete('srp_erp_customerreceiptpayments');

        if ($results) {
            $this->session->set_flashdata('s', 'Payment Detail Deleted Successfully');
            return true;
        }
    }
    function fetch_receiptVocher_payment_detail()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = $this->common_data['company_data']['company_id'];
        $receiptPaymentID = $this->input->post('receiptPaymentID');
        $receiptVoucherAutoId = $this->input->post('receiptVoucherAutoId');
        $data = $this->db->query("SELECT receiptPaymentID, srp_erp_customerreceiptpayments.paymentType, bankGLAutoID, chequeNo, DATE_FORMAT(chequeDate,' . $convertFormat . ') AS chequeDate, memo, amount, ((
			totalamounttransaction + IFNULL((( taxPercentage / 100 ) * totalamounttransaction ) ,0)+ amount 
		) - ( paidAmount )) AS payablemount  FROM srp_erp_customerreceiptpayments 
                     left join(
                            SELECT sum(transactionAmount) as totalamounttransaction, receiptVoucherAutoId FROM `srp_erp_customerreceiptdetail`
	WHERE companyID = '{$companyid}' AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GROUP BY receiptVoucherAutoId
                     )payableAmount ON payableAmount.receiptVoucherAutoId = srp_erp_customerreceiptpayments.receiptVoucherAutoId
                     left join(
                            SELECT sum(amount) as paidAmount, receiptVoucherAutoId FROM `srp_erp_customerreceiptpayments`
	WHERE companyID = '{$companyid}' AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GrOUP BY receiptVoucherAutoId
                     )paidAmount ON paidAmount.receiptVoucherAutoId = srp_erp_customerreceiptpayments.receiptVoucherAutoId
                     left join(
                         SELECT sum(taxPercentage) as taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails	
    where companyID = '{$companyid}' AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GROUP BY receiptVoucherAutoId
                     )taxAmount ON taxAmount.receiptVoucherAutoId = srp_erp_customerreceiptpayments.receiptVoucherAutoId
                     WHERE companyID = {$companyid} AND receiptPaymentID = {$receiptPaymentID}")->row_array();

        return $data;
    }

    function fetchTotalAmount_receipt($receiptVoucherAutoId)
    {
        $companyID = current_companyID();
        $dataReceipt = $this->db->query("SELECT sum(transactionAmount) as totalamounttransaction FROM `srp_erp_customerreceiptdetail`	where companyID = '{$companyID}' AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GROUP BY receiptVoucherAutoId")->row_array();
        $tax_detail = $this->db->query("SELECT sum(taxPercentage) as taxPercentage FROM srp_erp_customerreceipttaxdetails WHERE receiptVoucherAutoId = {$receiptVoucherAutoId} GROUP BY receiptVoucherAutoId")->row_array();
        $receiptamount = $dataReceipt['totalamounttransaction'] + (($tax_detail['taxPercentage'] / 100) * $dataReceipt['totalamounttransaction']);
        $dataPaid = $this->db->query("SELECT sum(amount) as paidAmount FROM `srp_erp_customerreceiptpayments`	where companyID = '{$companyID}' AND receiptVoucherAutoId = '{$receiptVoucherAutoId}' GROUP BY receiptVoucherAutoId")->row_array();
        $data['receiptAmount'] = $receiptamount;
        $data['paidAmount'] = $dataPaid['paidAmount'];
        return $data;
    }


    function save_rv_approval_suom($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->db->trans_start();
        $this->load->library('approvals');
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

        $sql = "SELECT
	(
		srp_erp_warehouseitems.currentStock - srp_erp_customerreceiptdetail.requestedQty
	) AS stockDiff,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_warehouseitems.currentStock as availableStock
FROM
	`srp_erp_customerreceiptdetail`
JOIN `srp_erp_warehouseitems` ON `srp_erp_customerreceiptdetail`.`itemAutoID` = `srp_erp_warehouseitems`.`itemAutoID`
AND `srp_erp_customerreceiptdetail`.`wareHouseAutoID` = `srp_erp_warehouseitems`.`wareHouseAutoID`
JOIN `srp_erp_itemmaster` ON `srp_erp_customerreceiptdetail`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`

WHERE
	`srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = '$system_id'
AND `srp_erp_warehouseitems`.`companyID` = " . current_companyID() . "
HAVING
	`stockDiff` < 0";
        $items_arr = $this->db->query($sql)->result_array();
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
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] == 'Service') {
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
                $double_entry = $this->Double_entry_model->fetch_double_entry_receipt_voucher_suom_data($system_id, 'RV');
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
                $amount = receipt_voucher_total_value($double_entry['master_data']['receiptVoucherAutoId'], $double_entry['master_data']['transactionCurrencyDecimalPlaces'], 0);

                for ($i = 0; $i < count($double_entry['gl_bank']); $i++) {
                    $bankledger_arr[$i]['bankGLSecondaryCode'] = $double_entry['gl_bank'][$i]['secondary'];
                    $bankledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['receiptVoucherAutoId'];
                    $bankledger_arr[$i]['documentDate'] = $double_entry['master_data']['RVdate'];
                    $bankledger_arr[$i]['transactionType'] = 1;
                    $bankledger_arr[$i]['bankName'] = $double_entry['gl_bank'][$i]['gl_desc'];
                    $bankledger_arr[$i]['bankGLAutoID'] = $double_entry['gl_bank'][$i]['gl_auto_id'];
                    $bankledger_arr[$i]['bankSystemAccountCode'] = $double_entry['gl_bank'][$i]['gl_code'];
                    $bankledger_arr[$i]['bankGLSecondaryCode'] = $double_entry['gl_bank'][$i]['secondary'];
                    $bankledger_arr[$i]['documentType'] = 'RV';
                    $bankledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['RVcode'];
                    $bankledger_arr[$i]['modeofpayment'] = $double_entry['master_data']['modeOfPayment'];
                    $bankledger_arr[$i]['chequeNo'] = $double_entry['gl_bank'][$i]['chequeNo'];
                    $bankledger_arr[$i]['chequeDate'] = $double_entry['gl_bank'][$i]['chequeDate'];
                    $bankledger_arr[$i]['memo'] = $double_entry['gl_bank'][$i]['memo'];
                    $bankledger_arr[$i]['partyType'] = 'CUS';
                    $bankledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['customerID'];
                    $bankledger_arr[$i]['partyCode'] = $double_entry['master_data']['customerSystemCode'];
                    $bankledger_arr[$i]['partyName'] = $double_entry['master_data']['customerName'];
                    $bankledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $bankledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $bankledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $bankledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $bankledger_arr[$i]['transactionAmount'] = $double_entry['gl_bank'][$i]['gl_dr'];
                    $bankledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                    $bankledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['customerCurrency'];
                    $bankledger_arr[$i]['partyCurrencyExchangeRate'] = $double_entry['master_data']['customerExchangeRate'];
                    $bankledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                    $bankledger_arr[$i]['partyCurrencyAmount'] = ($bankledger_arr[$i]['transactionAmount'] / $bankledger_arr[$i]['partyCurrencyExchangeRate']);
                    $bankledger_arr[$i]['bankCurrencyID'] = $double_entry['gl_bank'][$i]['bankCurrencyID'];
                    $bankledger_arr[$i]['bankCurrency'] = $double_entry['gl_bank'][$i]['bankCurrencyCode'];

                    if( $double_entry['gl_bank'][$i]['bankCurrencyID'] < 1){
                        $bank_currency = currency_conversionID($bankledger_arr[$i]['transactionCurrencyID'], $double_entry['gl_bank'][$i]['bankCurrencyID']);
                        $bankCurrencyExchangeRate['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
                        $bankledger_arr[$i]['bankCurrencyExchangeRate'] = $bankCurrencyExchangeRate['bankCurrencyExchangeRate'];
                        $bankledger_arr[$i]['bankCurrencyAmount'] = ($bankledger_arr[$i]['transactionAmount'] / $bankledger_arr[$i]['bankCurrencyExchangeRate']);
                    }

                    $bankledger_arr[$i]['bankCurrencyDecimalPlaces'] = $double_entry['gl_bank'][$i]['bankCurrencyDecimalPlaces'];
                    $bankledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $bankledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $bankledger_arr[$i]['segmentID'] = $double_entry['master_data']['segmentID'];
                    $bankledger_arr[$i]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                    $bankledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $bankledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $bankledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $bankledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $bankledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $bankledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $bankledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $bankledger_arr[$i] ['modifiedUserName'] = $this->common_data['current_user'];
                }
                $this->db->insert_batch('srp_erp_bankledger', $bankledger_arr);


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

    function delete_item_direct_suom()
    {
        $company_id = $this->common_data['company_data']['company_id'];
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $paymentDetails = $this->db->query("SELECT SUM(amount) as amount FROM srp_erp_customerreceiptdetail 
              LEFT JOIN srp_erp_customerreceiptpayments ON srp_erp_customerreceiptpayments.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
              WHERE srp_erp_customerreceiptdetail.companyID = {$company_id} AND srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID = {$receiptVoucherDetailAutoID} GROUP BY srp_erp_customerreceiptpayments.receiptVoucherAutoId")->row_array();

        if($paymentDetails['amount'] > 0){
            $this->session->set_flashdata('e', 'Delete Payment Details before deleting Receipt Voucher Details');
            return false;
        } else {
            $this->db->select('*');
            $this->db->from('srp_erp_customerreceiptdetail');
            $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
            $detail_arr = $this->db->get()->row_array();

            if ($detail_arr['type'] == 'Invoice') {
                $match_id = $detail_arr['invoiceAutoID'];
                $number = $detail_arr['transactionAmount'];
                $status = 0;
                $this->db->query("UPDATE srp_erp_customerinvoicemaster SET receiptTotalAmount = (receiptTotalAmount -{$number})  , receiptInvoiceYN = {$status}  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
            }

            /** update sub item master */

            $dataTmp['isSold'] = null;
            $dataTmp['soldDocumentAutoID'] = null;
            $dataTmp['soldDocumentDetailID'] = null;
            $dataTmp['soldDocumentID'] = null;
            $dataTmp['modifiedPCID'] = current_pc();
            $dataTmp['modifiedUserID'] = current_userID();
            $dataTmp['modifiedDatetime'] = format_date_mysql_datetime();

            $this->db->where('soldDocumentAutoID', $detail_arr['receiptVoucherAutoId']);
            $this->db->where('soldDocumentDetailID', $detail_arr['receiptVoucherDetailAutoID']);
            $this->db->where('soldDocumentID', 'RV');
            $this->db->update('srp_erp_itemmaster_sub', $dataTmp);

            /** end update sub item master */

            $this->db->where('receiptVoucherDetailAutoID', trim($this->input->post('receiptVoucherDetailAutoID') ?? ''));
            $results = $this->db->delete('srp_erp_customerreceiptdetail');

            if ($results) {
                $this->session->set_flashdata('s', 'Receipt Voucher Detail Deleted Successfully');
                return true;
            }
        }
    }

    function fetch_bankCard_details_suom()
    {
        $company_id = $this->common_data['company_data']['company_id'];
        $GLAutoID = $this->input->post('GLAutoID');
        $data =  '';
        if($GLAutoID){
            $data = $this->db->query("SELECT isCard, isCash FROM srp_erp_chartofaccounts WHERE companyID = {$company_id} AND isBank = 1 AND GLAutoID = {$GLAutoID}")->row_array();
        }
        return $data;
    }



    function fetch_receipt_voucher_template_data_suom($receiptVoucherAutoId)
    {
        $company_id = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,srp_erp_segment.description as segDescription,DATE_FORMAT(RVdate,\'' . $convertFormat . '\') AS RVdate,DATE_FORMAT(RVchequeDate,\'' . $convertFormat . '\') AS RVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,if(customerID IS NULL OR customerID = 0,srp_erp_customerreceiptmaster.customerName,srp_erp_customermaster.customerName) as customerName,srp_erp_customermaster.customerAddress1 as customeradd,srp_erp_customermaster.customerTelephone as customertel,srp_erp_customermaster.customerSystemCode as customersys,srp_erp_customermaster.customerFax as customerfax,CASE WHEN srp_erp_customerreceiptmaster.confirmedYN = 2 || srp_erp_customerreceiptmaster.confirmedYN = 3   THEN " - " WHEN srp_erp_customerreceiptmaster.confirmedYN = 1 THEN
CONCAT_WS(\' on \',IF(LENGTH(srp_erp_customerreceiptmaster.confirmedByName),srp_erp_customerreceiptmaster.confirmedByName,\'-\'),IF(LENGTH(DATE_FORMAT(srp_erp_customerreceiptmaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( srp_erp_customerreceiptmaster.confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn
');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_customerreceiptmaster.segmentID', 'Left');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID', 'Left');
        $this->db->from('srp_erp_customerreceiptmaster');

        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('srp_erp_customerreceiptdetail.*,CONCAT_WS(
		\' - \',
	IF
		( LENGTH( srp_erp_customerreceiptdetail.`itemDescription` ), `srp_erp_customerreceiptdetail`.`itemDescription`, NULL ),
	IF
		( LENGTH( srp_erp_itemmaster.seconeryItemCode ), `srp_erp_itemmaster`.`seconeryItemCode`, NULL ) ,
		IF
		( LENGTH( wareHouseLocation), `wareHouseLocation`, NULL ) 
		
	) AS Itemdescriptionpartno, ,srp_erp_unit_of_measure.UnitShortCode as secuom	');

        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerreceiptdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_customerreceiptdetail.SUOMID', 'left');
        $data['item_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['gl_detail'] = $this->db->get()->result_array();

        
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['invoice'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['advance'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'creditnote');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['creditnote'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'PRVR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['prvr_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();

        $data['payment_detail'] = $this->db->query("SELECT receiptPaymentID, 
 CASE srp_erp_customerreceiptpayments.paymentType WHEN 1 THEN 'Cash' WHEN 2 THEN 'Cheque' WHEN 3 THEN 'Bank Transfer' 
       WHEN 4 THEN 'Master Card' WHEN 5 THEN 'Visa Card' END AS paymentMode, 
            receiptVoucherAutoId, chequeNo, chequeDate, memo, amount, CONCAT( bankName, ' | ',bankAccountNumber) as bankDetails
                FROM srp_erp_customerreceiptpayments 
                LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_customerreceiptpayments.bankGLAutoID
                WHERE srp_erp_customerreceiptpayments.companyID = {$company_id} AND receiptVoucherAutoId = {$receiptVoucherAutoId}")->result_array();



        return $data;
    }


    function get_salesReturn_transactionAmount($creditNoteAutoID)
    {
        $sumTransactionAmount = $this->db->query("SELECT SUM(totalValue)AS totalTransactionAmount FROM srp_erp_salesreturndetails WHERE salesReturnAutoID = '" . $creditNoteAutoID . "'")->row('totalTransactionAmount');
        return $sumTransactionAmount;
    }

    function fetch_credit_note_buyback($customerID, $currencyID, $documentDate)
    {
        $tmpDate = format_date($documentDate);
        $companyID = current_companyID();

        $output = $this->db->query("SELECT
	* 
FROM
	(
SELECT
	masterTbl.creditNoteMasterAutoID AS creditNoteMasterAutoID,
	masterTbl.creditNoteCode AS creditNoteCode,
	detailTbl.transactionAmount,
	masterTbl.docRefNo AS RefNo,
	SUM( crDetail.transactionAmount ) AS RVTransactionAmount,
	'creditnote' AS type
FROM
	srp_erp_creditnotemaster masterTbl
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM srp_erp_creditnotedetail WHERE ( ISNULL( InvoiceAutoID ) OR InvoiceAutoID = 0 ) GROUP BY creditNoteMasterAutoID ) detailTbl ON detailTbl.creditNoteMasterAutoID = masterTbl.creditNoteMasterAutoID
	LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.creditNoteMasterAutoID  AND crDetail.type='creditnote'
WHERE
	masterTbl.confirmedYN = 1 
	AND masterTbl.approvedYN = 1 
	AND masterTbl.transactionCurrencyID = '$currencyID' 
	AND masterTbl.creditNoteDate <= '$tmpDate' 
	AND masterTbl.customerID = '$customerID'
	AND masterTbl.companyID = '$companyID'  
GROUP BY
	masterTbl.creditNoteMasterAutoID 
	
	UNION ALL
	
	SELECT
		masterTbl.salesReturnAutoID AS creditNoteMasterAutoID,
		masterTbl.salesReturnCode AS creditNoteCode,
		ROUND(total_value,masterTbl.transactionCurrencyDecimalPlaces) As transactionAmount,
		masterTbl.referenceNo AS RefNo,
		SUM( crDetail.transactionAmount ) AS RVTransactionAmount,
		'SLR' AS type 
	FROM
		srp_erp_salesreturnmaster masterTbl
	LEFT JOIN (
SELECT 
SUM(tot) AS total_value, salesReturnAutoID
FROM
(
SELECT
(salesPrice + IFNULL((totalAfterTax / requestedQty),0)) * return_Qty AS tot,
salesReturnAutoID
FROM
	`srp_erp_salesreturndetails`
	LEFT JOIN `srp_erp_customerinvoicemaster` AS `inv_mas` ON `srp_erp_salesreturndetails`.`invoiceAutoID` = `inv_mas`.`invoiceAutoID`
	LEFT JOIN `srp_erp_customerinvoicedetails` AS `inv_det` ON `inv_det`.`invoiceAutoID` = `inv_mas`.`invoiceAutoID` 
	AND `inv_det`.`InvoiceDetailsAutoID` = `srp_erp_salesreturndetails`.`invoiceDetailID`
	LEFT JOIN `srp_erp_deliveryorder` AS `ord_mas` ON `srp_erp_salesreturndetails`.`DOAutoID` = `ord_mas`.`DOAutoID` 
	)a
	GROUP BY salesReturnAutoID
) detailTbl ON detailTbl.salesReturnAutoID = masterTbl.salesReturnAutoID 
	LEFT JOIN srp_erp_customerreceiptdetail AS crDetail ON crDetail.creditNoteAutoID = masterTbl.salesReturnAutoID  AND crDetail.type='SLR'
WHERE
	masterTbl.confirmedYN = 1 
	AND masterTbl.approvedYN = 1 
	AND masterTbl.transactionCurrencyID = '$currencyID' 
	AND masterTbl.returnDate <= '$tmpDate' 
	AND masterTbl.customerID = '$customerID' 
	AND masterTbl.companyID = '$companyID'
GROUP BY
	masterTbl.salesReturnAutoID 
	
	) AS result")->result_array();
        //echo $this->db->last_query();
        return $output;

    }

    function fetch_receipt_voucher_transfer_data($receiptVoucherAutoId)
    {
        $this->db->select('*,srp_erp_chartofaccounts.bankName as bankName,srp_erp_chartofaccounts.bankAccountNumber as bankAccountNumber');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_customerreceiptmaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID');
        $this->db->from('srp_erp_customerreceiptmaster');
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('SUM(transactionAmount) as transactionAmount');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type !=', 'creditnote');
        $this->db->where('type !=', 'SLR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('SUM(transactionAmount) as transactionAmount');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'creditnote');
        $this->db->where('type', 'SLR');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['creditnote'] = $this->db->get()->result_array();

        $this->db->select('SUM(transactionAmount) as transactionAmount');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_customerreceiptdetail');
        $data['typeDet'] = $this->db->get()->result_array();

        $this->db->select('sum(taxPercentage) as taxPercentage');
        $this->db->where('receiptVoucherAutoId', $receiptVoucherAutoId);
        $this->db->from('srp_erp_customerreceipttaxdetails');
        $data['tax'] = $this->db->get()->row_array();

        return $data['total'] = $this->db->query("SELECT 
                            ((( IFNULL( addondet.taxPercentage, 0 )/ 100 )* IFNULL( tyepdet.transactionAmount, 0 )) + IFNULL( det.transactionAmount, 0 )- IFNULL( Creditnote.transactionAmount, 0 )) AS transactionAmount 
                    FROM
                        srp_erp_customerreceiptmaster
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE type != 'SLR' AND type != 'creditnote' GROUP BY receiptVoucherAutoId ) det ON det.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
                        LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON addondet.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE type = \"GL\" OR type = \"Item\" GROUP BY receiptVoucherAutoId ) tyepdet ON tyepdet.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE type = \"creditnote\" OR type = \"SLR\" GROUP BY receiptVoucherAutoId ) Creditnote ON Creditnote.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
                        WHERE srp_erp_customerreceiptmaster.receiptVoucherAutoId = {$receiptVoucherAutoId}")->row_array();
    }
    function fetch_rv_advance_detail_project()
    {
        $data = array();
        $project = $this->input->post('projectID');
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $convertFormat = convert_date_format_sql();
        $this->db->select('customerID,transactionCurrency,transactionCurrencyID,DATE_FORMAT(matchDate,"%Y-%m-%d") AS matchDate,transactionCurrencyDecimalPlaces');
        $this->db->where('matchID', $this->input->post('matchID'));
        $matchid=  $this->input->post('matchID');
        $master_arr = $this->db->get('srp_erp_rvadvancematch')->row_array();
        $comapnyId = current_companyID();

        /*$this->db->select('srp_erp_customerreceiptdetail.transactionAmount,DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate,\'' . $convertFormat . '\') AS RVdate , srp_erp_customerreceiptmaster.RVcode,ROUND(sum( IFNULL(srp_erp_rvadvancematchdetails.transactionAmount , 0) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces) AS paid,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID,srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces as decimalplaces, ROUND(`srp_erp_customerreceiptdetail`.`transactionAmount` - (ROUND(sum( IFNULL(srp_erp_rvadvancematchdetails.transactionAmount , 0) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces)), 2) as balance');
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->where('srp_erp_customerreceiptdetail.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('customerID', $master_arr['customerID']);
        $this->db->where('srp_erp_customerreceiptmaster.transactionCurrencyID', $master_arr['transactionCurrencyID']);
        $this->db->where('type', 'Advance');
        $this->db->where('projectID', $project );
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->where('srp_erp_customerreceiptmaster.approvedYN', 1);
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->join('srp_erp_rvadvancematchdetails', 'srp_erp_rvadvancematchdetails.receiptVoucherDetailAutoID = srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID', 'Left');*/
        /*$this->db->having("balance > 0");*/
        $data['receipt'] = $this->db->query("SELECT `srp_erp_customerreceiptdetail`.`transactionAmount`,DATE_FORMAT(srp_erp_customerreceiptmaster.RVdate, '$convertFormat') AS RVdate,
        `srp_erp_customerreceiptmaster`.`RVcode`,ROUND( sum( IFNULL( rvadvancematchdetailspaid.transactionAmount, 0 ) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces ) AS paid,
        `srp_erp_customerreceiptdetail`.`receiptVoucherDetailAutoID`,`srp_erp_customerreceiptmaster`.`transactionCurrencyDecimalPlaces` AS `decimalplaces`,
        ROUND(`srp_erp_customerreceiptdetail`.`transactionAmount` - ( ROUND( sum( IFNULL( rvadvancematchdetailstrans.transactionAmount, 0 ) ), srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces ) ),
        2) AS balanceamount
        FROM `srp_erp_customerreceiptmaster` JOIN `srp_erp_customerreceiptdetail` ON `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
        LEFT JOIN(SELECT  ( IFNULL( srp_erp_rvadvancematchdetails.transactionAmount, 0 ) ) as transactionAmount,receiptVoucherDetailAutoID from 
        srp_erp_rvadvancematchdetails where  matchID = $matchid )`rvadvancematchdetailspaid` ON `rvadvancematchdetailspaid`.`receiptVoucherDetailAutoID` = `srp_erp_customerreceiptdetail`.`receiptVoucherDetailAutoID` 
        LEFT JOIN(SELECT ( IFNULL( srp_erp_rvadvancematchdetails.transactionAmount, 0 ) ) as transactionAmount,receiptVoucherDetailAutoID from 
        srp_erp_rvadvancematchdetails where matchID != $matchid)`rvadvancematchdetailstrans` ON `rvadvancematchdetailstrans`.`receiptVoucherDetailAutoID` = `srp_erp_customerreceiptdetail`.`receiptVoucherDetailAutoID` 
        WHERE `srp_erp_customerreceiptdetail`.`companyID` = $comapnyId 
	    AND `customerID` = '{$master_arr['customerID']}'
	    AND `srp_erp_customerreceiptmaster`.`transactionCurrencyID` = '{$master_arr['transactionCurrencyID']}' 
	    AND `type` = 'Advance' 
	    AND `projectID` = $project 
	    AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
        GROUP BY
	    `receiptVoucherDetailAutoID`")->result_array();

        $customerID=$master_arr['customerID'];
        $transactionCurrency=$master_arr['transactionCurrencyID'];
        $companyID=$this->common_data['company_data']['company_id'];

        $data['invoice'] = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, `invoiceCode`, `invoiceDate`, `transactionAmount`, `receiptTotalAmount`, `creditNoteTotalAmount`, `advanceMatchedTotal` FROM `srp_erp_customerinvoicemaster` LEFT JOIN(SELECT srp_erp_customerinvoicemaster.invoiceAutoID, ( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexchargistax.transactionAmount, 0 ) ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 ) AS amount FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID )tot ON tot.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE `receiptInvoiceYN` = 0 AND `approvedYN` = 1 AND `customerID` = $customerID AND `transactionCurrencyID` = '$transactionCurrency' AND `companyID` = $companyID AND ROUND((transactionAmount - (receiptTotalAmount+ creditNoteTotalAmount + advanceMatchedTotal )), srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces) > 0")->result_array();

        $invoiceamount = $this->db->query("SELECT
    SUM(transactionAmount) as totalamount 
FROM
    `srp_erp_customerinvoicedetails` 
WHERE
    invoiceAutoID =  $invoiceAutoID
    	AND type = 'Project'
    ")->row('totalamount');
        $retentionpercentage = $this->db->query("select 
    retensionPercentage,advancePercentage
    from 
    srp_erp_boq_header
    where 
    projectID = $project")->row_array();

        $advanceamount = $this->db->query("SELECT sum(srp_erp_rvadvancematchdetails.transactionAmount) as advanceamount FROM `srp_erp_rvadvancematchdetails`
	                                    LEFT JOIN srp_erp_rvadvancematch advancematch on advancematch.matchID = srp_erp_rvadvancematchdetails.matchID
	                                    where  advancematch.matchinvoiceAutoID = $invoiceAutoID GROUP BY advancematch.matchinvoiceAutoID  ")->row('advanceamount');

        $data['totalamountretention'] = round((($invoiceamount)-(($invoiceamount)*($retentionpercentage['retensionPercentage']/100))),$master_arr['transactionCurrencyDecimalPlaces']) ;

        $data['total_advanceinvoiceamt'] = (($data['totalamountretention']*$retentionpercentage['advancePercentage'])/100);

        $data['paidamount'] = array_sum( array_column($data['receipt'], 'paid'));



        return $data;
    }
    function save_match_amount_project()
    {
        $this->db->trans_start();
        $receiptVoucherDetailAutoID = $this->input->post('receiptVoucherDetailAutoID');
        $invoice_id = $this->input->post('invoiceAutoID');
        $amounts = $this->input->post('amounts');
        $matchID = $this->input->post('matchID');
        $totalinvoiceamount = $this->input->post('totalinvoiceamount');
        $invoiceamount = $this->input->post('invoiceamount');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
        $this->db->where('matchID', $matchID);
        $master = $this->db->get('srp_erp_rvadvancematch')->row_array();

        $this->db->select('srp_erp_customerreceiptmaster.receiptVoucherAutoId,srp_erp_customerreceiptdetail.transactionAmount,srp_erp_customerreceiptmaster.RVdate,srp_erp_customerreceiptmaster.RVcode,srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID');
        $this->db->group_by("receiptVoucherDetailAutoID");
        $this->db->from('srp_erp_customerreceiptmaster');
        $this->db->join('srp_erp_customerreceiptdetail', 'srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId');
        $this->db->where_in('receiptVoucherDetailAutoID', $receiptVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        $advancepercentage = $this->db->query("SELECT header.advancePercentage FROM `srp_erp_customerinvoicemaster` 
        LEFT JOIN srp_erp_boq_header header on header.projectID = srp_erp_customerinvoicemaster.projectID where invoiceAutoID = $invoice_id")->row('advancePercentage');

            $invoiceamoutasapercentage = (($invoiceamount*$advancepercentage)/100);

         if($totalinvoiceamount > $invoiceamoutasapercentage)
         {
             return array('status' => 0, 'type' => 'e', 'messsage' => 'You can only invoice '.$invoiceamoutasapercentage);
             exit();
         }



        for ($i = 0; $i < count($detail_arr); $i++) {
            $this->db->delete('srp_erp_rvadvancematchdetails', array('matchID' =>$matchID,'receiptVoucherAutoId'=>$detail_arr[$i]['receiptVoucherAutoId'],'receiptVoucherDetailAutoID'=>$detail_arr[$i]['receiptVoucherDetailAutoID']));
            if($amounts[$i]>0)
            {
                $invoice_data = $this->fetch_invoice($invoice_id[$i]);
                $data[$i]['matchID'] = $matchID;
                $data[$i]['receiptVoucherAutoId'] = $detail_arr[$i]['receiptVoucherAutoId'];
                $data[$i]['receiptVoucherDetailAutoID'] = $detail_arr[$i]['receiptVoucherDetailAutoID'];
                $data[$i]['RVcode'] = $detail_arr[$i]['RVcode'];
                $data[$i]['RVdate'] = $detail_arr[$i]['RVdate'];
                $data[$i]['invoiceAutoID'] = trim($invoice_data['invoiceAutoID'] ?? '');
                $data[$i]['invoiceCode'] = trim($invoice_data['invoiceCode'] ?? '');
                $data[$i]['invoiceDate'] = trim($invoice_data['invoiceDate'] ?? '');
                $data[$i]['transactionAmount'] = $amounts[$i];
                $data[$i]['transactionExchangeRate'] = 1;
                $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data[$i]['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
                $data[$i]['customerCurrencyAmount'] = ($data[$i]['transactionAmount'] / $master['customerCurrencyExchangeRate']);
                $data[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                $data[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                $data[$i]['modifiedUserName'] = $this->common_data['current_user'];
                $data[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];

                $id = $data[$i]['invoiceAutoID'];
//            $amo = $data[$i]['transactionAmount'];
//            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal+$amo) WHERE invoiceAutoID='$id'");
                $amo['advanceMatchedTotal'] = $invoice_data['advanceMatchedTotal'] + $data[$i]['transactionAmount'];
                $balanceAmount = $invoice_data['transactionAmount'] - ($invoice_data['creditNoteTotalAmount'] + $invoice_data['receiptTotalAmount'] + $invoice_data['advanceMatchedTotal'] + $data[$i]['transactionAmount']);
                if ($balanceAmount <= 0) {
                    $amo['receiptInvoiceYN'] = 1;
                }
                $this->db->where('invoiceAutoID', $id);
                $this->db->update('srp_erp_customerinvoicemaster', $amo);
            }


        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_rvadvancematchdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => 0, 'type' => 'e', 'messsage' => 'Records Inserted error');
        } else {
            $this->db->trans_commit();
            return array('status' => 1, 'type' => 's', 'messsage' => 'Records Inserted successfully');
        }
    }
    function fetch_rv_warehouse_item_deduct_qty()
    {

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $documentAutoID = $this->input->post('documentAutoID');
        $documentID = $this->input->post('documentID');

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }

        $pulled_stock = $this->fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID, $documentID, $documentAutoID);
        
        if (!empty($data) && ($data['currentStock']>0)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $currentStock, 'WacAmount' => $data['companyLocalWacAmount'], 'mainCategory' => $data['mainCategory'],'pulledstock'=>($currentStock - $pulled_stock['Unapproved_stock']),'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {
          
            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse ");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse");
        }
    }
    function fetch_rv_warehouse_item_deduct_qty_new()
    {

        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $itemAutoID = $this->input->post('itemAutoID');
        $documentAutoID = $this->input->post('documentAutoID');
        $documentID = $this->input->post('documentID');
        $documentDetAutoID = $this->input->post('documentDetAutoID');

        $this->db->select('srp_erp_warehouseitems.currentStock,companyLocalWacAmount,wareHouseDescription,wareHouseLocation,srp_erp_itemmaster.mainCategory as mainCategory');
        $this->db->from('srp_erp_warehouseitems');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_warehouseitems.itemAutoID');
        $this->db->where('wareHouseAutoID', trim($this->input->post('wareHouseAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        $this->db->where('srp_erp_warehouseitems.companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get()->row_array();

        $stock = $this->db->query('SELECT SUM(transactionQTY/convertionRate) as currentStock FROM `srp_erp_itemledger` where wareHouseAutoID="' . $wareHouseAutoID . '" AND itemAutoID="' . $itemAutoID . '" ')->row_array();
        if (!empty($stock)) {
            $currentStock = $stock['currentStock'];
        } else {
            $currentStock = 0;
        }

        $pulled_stock = $this->fetch_pulled_document_qty_new($itemAutoID,$wareHouseAutoID, $documentID, $documentAutoID,$documentDetAutoID);

        if (!empty($data) && ($data['currentStock']>0)) {
            return array('error' => 0, 'message' => '', 'status' => true, 'currentStock' => $currentStock, 'WacAmount' => $data['companyLocalWacAmount'], 'mainCategory' => $data['mainCategory'],'pulledstock'=>($currentStock - $pulled_stock['Unapproved_stock']),'parkQty'=>($pulled_stock['Unapproved_stock']));
        } else {

            $this->session->set_flashdata('w', "Item doesn't exists in the selected warehouse ");
            return array('status' => false, 'error' => 2, 'message' => "Item doesn't exists in the selected warehouse");
        }
    }
    function fetch_pulled_document_qty($itemAutoID,$wareHouseAutoID, $documentID = null, $documentAutoID = null)
    { 
        $documentAutoID_filter_mi = '';
        if($documentID == 'MI') {
            $documentAutoID_filter_mi = ' AND srp_erp_itemissuemaster.itemIssueAutoID != ' . $documentAutoID . ' ';
        }
        $documentAutoID_filter_st = '';
        if($documentID == 'ST') {
            $documentAutoID_filter_st = ' AND srp_erp_stocktransfermaster.stockTransferAutoID != ' . $documentAutoID . ' ';
        }
        $documentAutoID_filter_do = '';
        if($documentID == 'DO') {
            $documentAutoID_filter_do = ' AND srp_erp_deliveryorder.DOAutoID != ' . $documentAutoID . ' ';
        }
        $comapnyID =current_companyID();
        $stock = $this->db->query("SELECT SUM(stock) as stock FROM (
        SELECT
        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stockadjustmentmaster
        LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
        WHERE
        companyID = $comapnyID 
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stockadjustmentmaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stockcountingmaster
        LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
        WHERE
        companyID = $comapnyID  
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stockcountingmaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_itemissuemaster
        LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
        WHERE
        srp_erp_itemissuemaster.companyID = $comapnyID 
        $documentAutoID_filter_mi
        AND itemAutoID = $itemAutoID
        AND srp_erp_itemissuemaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( requestedQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_customerreceiptmaster
        LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
        WHERE
        srp_erp_customerreceiptmaster.companyID = $comapnyID  
        AND itemAutoID = $itemAutoID 
        AND srp_erp_customerreceiptdetail.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( requestedQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_customerinvoicemaster
        LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
        WHERE
        srp_erp_customerinvoicemaster.companyID = $comapnyID 
        AND itemAutoID = $itemAutoID 
        AND srp_erp_customerinvoicedetails.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( deliveredQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_deliveryorder
        LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
        WHERE
        srp_erp_deliveryorder.companyID = $comapnyID 
        $documentAutoID_filter_do
        AND itemAutoID = $itemAutoID 
        AND srp_erp_deliveryorderdetails.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( transfer_QTY / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stocktransfermaster
        LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
        WHERE
        srp_erp_stocktransfermaster.companyID = $comapnyID 
        $documentAutoID_filter_st
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stocktransfermaster.from_wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        ) t1
        
        GROUP BY
        t1.ItemAutoID")->row_array();

        $data['Unapproved_stock'] = $stock['stock'] ?? 0;
        return $data;

    }
    function fetch_pulled_document_qty_new($itemAutoID,$wareHouseAutoID, $documentID = null, $documentAutoID = null,$documentDetAutoID=null)
    {
        //print_r($documentDetAutoID);exit;
        $documentAutoID_filter_mi = '';
        if($documentID == 'MI') {
            $documentAutoID_filter_mi = ' AND srp_erp_itemissuedetails.itemIssueDetailID != ' . $documentDetAutoID . ' ';
        }
        $documentAutoID_filter_st = '';
        if($documentID == 'ST') {
            //$documentAutoID_filter_st = ' AND srp_erp_stocktransfermaster.stockTransferAutoID != ' . $documentAutoID . ' ';
            $documentAutoID_filter_st = ' AND srp_erp_stocktransferdetails.stockTransferDetailsID != ' . $documentDetAutoID . ' ';

        }
        $documentAutoID_filter_do = '';
        if($documentID == 'DO') {
            //$documentAutoID_filter_do = ' AND srp_erp_deliveryorder.DOAutoID != ' . $documentAutoID . ' ';
            $documentAutoID_filter_do = ' AND srp_erp_deliveryorderdetails.DODetailsAutoID != ' . $documentDetAutoID . ' ';

        }
        $documentAutoID_filter_rv = '';
        if($documentID == 'RV') {
            $documentAutoID_filter_rv = ' AND srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID != ' . $documentDetAutoID . ' ';

        }
       $documentAutoID_filter_cinv='';
       if($documentID == 'CINV') {
           $documentAutoID_filter_cinv = ' AND srp_erp_customerinvoicedetails.invoiceDetailsAutoID != ' . $documentDetAutoID . ' ';
       }
        $comapnyID =current_companyID();
        $stock = $this->db->query("SELECT SUM(stock) as stock FROM (
        SELECT
        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stockadjustmentmaster
        LEFT JOIN srp_erp_stockadjustmentdetails ON srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = srp_erp_stockadjustmentdetails.stockAdjustmentAutoID 
        WHERE
        companyID = $comapnyID 
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stockadjustmentmaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        IFNULL(( adjustmentStock / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stockcountingmaster
        LEFT JOIN srp_erp_stockcountingdetails ON srp_erp_stockcountingmaster.stockCountingAutoID = srp_erp_stockcountingdetails.stockCountingAutoID 
        WHERE
        companyID = $comapnyID  
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stockcountingmaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        IFNULL(( qtyIssued / conversionRateUOM ), 0 ) AS stock,
        itemAutoID 
        FROM
        srp_erp_itemissuemaster
        LEFT JOIN srp_erp_itemissuedetails ON srp_erp_itemissuemaster.itemIssueAutoID = srp_erp_itemissuedetails.itemIssueAutoID 
        WHERE
        srp_erp_itemissuemaster.companyID = $comapnyID 
        $documentAutoID_filter_mi
        AND itemAutoID = $itemAutoID
        AND srp_erp_itemissuemaster.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( requestedQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_customerreceiptmaster
        LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_customerreceiptdetail.receiptVoucherAutoId 
        WHERE
        srp_erp_customerreceiptmaster.companyID = $comapnyID  
        $documentAutoID_filter_rv
        AND itemAutoID = $itemAutoID 
        AND srp_erp_customerreceiptdetail.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( requestedQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_customerinvoicemaster
        LEFT JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
        WHERE
        srp_erp_customerinvoicemaster.companyID = $comapnyID 
        $documentAutoID_filter_cinv
        AND itemAutoID = $itemAutoID 
        AND srp_erp_customerinvoicedetails.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( deliveredQty / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_deliveryorder
        LEFT JOIN srp_erp_deliveryorderdetails ON srp_erp_deliveryorder.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID 
        WHERE
        srp_erp_deliveryorder.companyID = $comapnyID 
        $documentAutoID_filter_do
        AND itemAutoID = $itemAutoID 
        AND srp_erp_deliveryorderdetails.wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1 
        AND itemCategory = 'Inventory'
        UNION ALL
        SELECT
        ( transfer_QTY / conversionRateUOM ) AS stock,
        itemAutoID 
        FROM
        srp_erp_stocktransfermaster
        LEFT JOIN srp_erp_stocktransferdetails ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID 
        WHERE
        srp_erp_stocktransfermaster.companyID = $comapnyID 
        $documentAutoID_filter_st
        AND itemAutoID = $itemAutoID 
        AND srp_erp_stocktransfermaster.from_wareHouseAutoID = $wareHouseAutoID
        AND approvedYN != 1
        AND itemCategory = 'Inventory'
        ) t1
        
        GROUP BY
        t1.ItemAutoID")->row_array();

        $data['Unapproved_stock'] = $stock['stock'];
        return $data;

    }

    function fetch_line_tax_and_vat()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $data['isGroupByTax'] =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV','receiptVoucherAutoId');
        if($data['isGroupByTax'] == 1){ 
            $data['dropdown'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 1);
        }
        return $data;
    }

    function load_line_tax_amount()
    {
        $amnt=0;
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($this->input->post('receiptVoucherAutoId') ?? ''),'RV', 'receiptVoucherAutoId');
        if($isGroupByTax == 1){
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'RV', trim($this->input->post('receiptVoucherAutoId') ?? ''));
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        return $amnt;
    }

    function load_line_tax_amount_advance()
    {
        $data['amnt'] = 0;
        $data['contract_validation'] = 0;
        $receiptVoucherAutoId=$this->input->post('receiptVoucherAutoId');
        $contractAutoID=$this->input->post('contractAutoID');
        $appliedAmount=$this->input->post('appliedAmount');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_customerreceiptmaster',trim($receiptVoucherAutoId),'RV', 'receiptVoucherAutoId');
        if($isGroupByTax == 1){
            if(!($contractAutoID)) {
                $this->db->select("IFNULL(ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage) as taxPercentagedetail");
                $this->db->join("(SELECT  
                                       taxFormulaDetailID,
                                       taxPercentage
                                       FROM
                                       `srp_erp_taxledger` 
                                       WHERE
                                           documentID = 'RV'
                                           AND documentMasterAutoID = $receiptVoucherAutoId
                                           AND documentDetailAutoID = null
                                   ) ledger", "ledger.taxFormulaDetailID = srp_erp_taxcalculationformuladetails.formulaDetailID", "LEFT");
                $this->db->join("srp_erp_taxmaster","srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID","LEFT");
                $this->db->where('taxCalculationformulaID', $taxCalculationformulaID);
                $this->db->where('taxCategory', 2);
                $formulaDtl = $this->db->get("srp_erp_taxcalculationformuladetails")->row_array();

                if(!empty($formulaDtl)) {
                    $data['amnt'] = ((($appliedAmount - $disount) / (100+$formulaDtl['taxPercentagedetail'])) * $formulaDtl['taxPercentagedetail']);
                }
            } else {
                $contractAmount = $this->db->query("SELECT
                                                                    (SUM(transactionAmount + IFNULL(taxAmount, 0)) - IFNULL(paidAmount, 0)) AS contractAmount
                                                                FROM
                                                                    srp_erp_contractdetails 
                                                                	LEFT JOIN (SELECT SUM(transactionAmount) as paidAmount, contractAutoID FROM srp_erp_customerreceiptdetail GROUP BY contractAutoID)paid ON paid.contractAutoID = srp_erp_contractdetails.contractAutoID
                                                                WHERE
                                                                    srp_erp_contractdetails.contractAutoID = {$contractAutoID} 
                                                                GROUP BY
                                                                    srp_erp_contractdetails.contractAutoID")->row('contractAmount');

                if($contractAmount < $appliedAmount) {
                    $data['contract_validation'] = 1;
                    return $data;
                }
                $this->db->select("SUM(srp_erp_taxledger.taxPercentage) as taxPercentagedetail, SUM(amount) AS amount");
                $this->db->join("srp_erp_taxmaster","srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID","LEFT");
                $this->db->where('documentMasterAutoID', $contractAutoID);
                $this->db->where('documentID', 'CNT');
                $this->db->where('srp_erp_taxmaster.taxCategory', 2);
                $formulaDtl = $this->db->get("srp_erp_taxledger")->row_array();

                if(!empty($formulaDtl)) {
                    $data['amnt'] = (($appliedAmount / $contractAmount) * $formulaDtl['amount']);
                }
            }

        }
        return $data;
    }

    function load_contract_balance_amount_advance()
    {
        $contractAutoID=$this->input->post('contractAutoID');
        $this->db->select("SUM( transactionAmount + IFNULL(taxAmount, 0) ) AS contract_amount , IFNULL(paidAmount, 0) As paidAmount");
        $this->db->join("(SELECT SUM(transactionAmount) as paidAmount, contractAutoID FROM srp_erp_customerreceiptdetail GROUP BY contractAutoID)paid","paid.contractAutoID = srp_erp_contractdetails.contractAutoID","LEFT");
        $this->db->where('srp_erp_contractdetails.contractAutoID', $contractAutoID);
        $this->db->group_by('srp_erp_contractdetails.contractAutoID');
        return $this->db->get("srp_erp_contractdetails")->row_array();
    }

    function load_bank_detail_exchange_rates()
    {
        $transaction_currency=$this->input->post('transaction_currency');
        $bank_gl=$this->input->post('bank_gl');

        $bank_detail = fetch_gl_account_desc(trim($bank_gl));

        if($bank_detail){

            $bank_currency_id = $bank_detail['bankCurrencyID'];
            $exchange_rates = currency_conversionID($transaction_currency, $bank_currency_id);
            $bank_detail['conversions'] = $exchange_rates;

        }
       
        return $bank_detail;

    }

    function check_rv_reprocessed_ledger($companyID){

        //update
        $this->db->select('*');
        $this->db->where('status',0);
        $this->db->where('doc_type','RV');
        $this->db->where('companyID',$companyID);
        $this->db->limit(5);
        $doc_processed = $this->db->get('srp_erp_document_reprocessed')->result_array();

        foreach($doc_processed as $doc){
            $document = $doc['doc_code'];
            $status = $doc['status'];
            $updatedBatchNumberArray = array();
            $data = array();
            $data_rv = array();
            $status = 1;

            $this->db->select('documentID,receiptVoucherAutoId, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID,RVdate,approvedYN');
            $this->db->where('RVcode', $document);
            $this->db->from('srp_erp_customerreceiptmaster');
            $app_data = $this->db->get()->row_array();

            if($app_data){

                $receiptVoucherAutoId = $app_data['receiptVoucherAutoId'];
                $approvedYN = $app_data['approvedYN'];

                if($approvedYN == 1){
                    $data_rv['confirmedYN'] = 1;
                    $des = $this->save_rv_approval(0, $app_data['receiptVoucherAutoId'], 1, 'Auto Approved',$updatedBatchNumberArray);
                }else{
                    $data_rv['confirmedYN'] = 1;

                    $des = $this->save_rv_approval(0, $app_data['receiptVoucherAutoId'], 1, 'Auto Approved',$updatedBatchNumberArray);
                    $status = 2;
                }

            }
            
            $data['status'] = $status;
            $data['date_added'] = $this->common_data['current_date'];;

            //update master
            $res = $this->db->where('receiptVoucherAutoId',$receiptVoucherAutoId)->update('srp_erp_customerreceiptmaster',$data_rv);

            //update processed
            $res = $this->db->where('id',$doc['id'])->update('srp_erp_document_reprocessed',$data);

        }

        return TRUE;

    }
}
