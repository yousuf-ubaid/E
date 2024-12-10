<?php

class Payment_voucher_model extends ERP_Model
{

    function save_paymentvoucher_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->input->post('PVdate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->input->post('PVchequeDate');
        $paymentTypeID = $this->input->post('paymentType');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $enbleAuthorizeSignature = getPolicyValues('SGB', 'All');
        $accountPayeeOnly = 0;
        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        $voucherType = $this->input->post('pvtype');
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails') ?? ''));
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
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
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $chequeRegister = getPolicyValues('CRE', 'All');

        $data['PVbankCode'] = trim($this->input->post('PVbankCode') ?? '');
        $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['PVdate'] = trim($PVdate);

        $narration = ($this->input->post('narration'));
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
        $data['paymentType'] = $this->input->post('paymentType');
        $data['supplierBankMasterID'] = $this->input->post('supplierBankMasterID');
        if($PVcheqDate == null)
        {
            $data['PVchequeDate'] = null;
        }
        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['chequeRegisterDetailID'] = null;
            $data['PVchequeDate'] = null;
        } else {
            if ($this->input->post('paymentType') == 2 && (($voucherType == 'Supplier') || ($voucherType == 'SupplierAdvance') || ($voucherType == 'SupplierDebitNote') || ($voucherType == 'SupplierInvoice') || ($voucherType == 'SupplierItem') || ($voucherType == 'SupplierExpense') || ($voucherType == 'Direct') || ($voucherType == 'DirectItem') || ($voucherType == 'DirectExpense') || ($voucherType == 'Employee') || ($voucherType == 'EmployeeExpense') || ($voucherType == 'EmployeeItem') || ($voucherType == 'PurchaseRequest'))) {
                $data['PVchequeNo'] = null;
                $data['chequeRegisterDetailID'] = null;
                $data['PVchequeDate'] = null;
            } else {
                if($chequeRegister==1) {
                    $data['chequeRegisterDetailID'] = trim($this->input->post('chequeRegisterDetailID') ?? '');
                    $data['PVchequeNo'] = $this->getchequeDetails($this->input->post('chequeRegisterDetailID'));
                }else{
                    $data['PVchequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                    $data['chequeRegisterDetailID'] = null;
                }
                $data['PVchequeDate'] = trim($PVchequeDate);
            }
        }
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['pvType'] = trim($this->input->post('pvtype') ?? '');
        $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
        $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        // $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');
        // $data['transactionExchangeRate'] = 1;
        // $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal($data['transactionCurrency']);
        // $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        // $default_currency = currency_conversion($data['transactionCurrency'], $data['companyLocalCurrency']);
        // $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        // $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        // $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        // $reporting_currency = currency_conversion($data['transactionCurrency'], $data['companyReportingCurrency']);
        // $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        // $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

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
        $bank_currency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bank_currency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bank_currency['DecimalPlaces'];
        if ($data['pvType'] == 'Direct' || $data['pvType'] == 'DirectItem' || $data['pvType'] == 'DirectExpense') {
            $data['partyType'] = 'DIR';
            $data['partyName'] = trim($this->input->post('partyName') ?? '');
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        }elseif ($data['pvType'] == 'PurchaseRequest') {
            $data['partyType'] = 'PRQ';
            $data['partyName'] = trim($this->input->post('partyName') ?? '');
            $data['partyID'] = trim($this->input->post('partyID') ?? '');
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
            if(!empty($this->input->post('partyID'))){
                $supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
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
            }

        } elseif ($data['pvType'] == 'Employee' || $data['pvType'] == 'EmployeeExpense' || $data['pvType'] == 'EmployeeItem') {
            $emp_arr = $this->fetch_empyoyee($this->input->post('partyID'));
            $data['partyType'] = 'EMP';
            $data['partyID'] = trim($this->input->post('partyID') ?? '');
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename2'];
            $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
            $data['partyTelephone'] = $emp_arr['EpTelephone'];
            $data['partyFax'] = $emp_arr['EpFax'];
            $data['partyEmail'] = $emp_arr['EEmail'];
            $data['partyGLAutoID'] = '';
            $data['partyGLCode'] = '';
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'Supplier' || $data['pvType'] == 'SupplierAdvance' || $data['pvType'] == 'SupplierDebitNote' || $data['pvType'] == 'SupplierInvoice' || $data['pvType'] == 'SupplierItem' || $data['pvType'] == 'SupplierExpense') {
            $supplier_arr = $this->fetch_supplier_data($this->input->post('partyID'));
            $data['partyType'] = 'SUP';
            $data['partyID'] = $this->input->post('partyID');
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
        } elseif ($data['pvType'] == 'SC') {
            $sales_rep = $this->fetch_sales_rep_data($this->input->post('partyID'));
            $data['partyType'] = 'SC';
            $data['partyID'] = $this->input->post('partyID');
            $data['partyCode'] = $sales_rep['SalesPersonCode'];
            $data['partyName'] = $sales_rep['SalesPersonName'];
            $data['partyAddress'] = $sales_rep['SalesPersonAddress'];
            $data['partyTelephone'] = $sales_rep['contactNumber'];
            $data['partyEmail'] = $sales_rep['SalesPersonEmail'];
            $data['partyGLAutoID'] = $sales_rep['receivableAutoID'];
            $data['partyGLCode'] = $sales_rep['receivableSystemGLCode'];
            $data['partyCurrencyID'] = $sales_rep['salesPersonCurrencyID'];
            $data['partyCurrency'] = $sales_rep['salesPersonCurrency'];
            $data['partyExchangeRate'] = 0;
            $data['partyCurrencyDecimalPlaces'] = $sales_rep['salesPersonCurrencyDecimalPlaces'];
        }
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

                if($enbleAuthorizeSignature==1){
                    if($paymentTypeID==1){
                        $signature = $this->input->post('signature');

                        $this->save_signature_authority_pv($signature ,$this->input->post('PVbankCode'),$this->input->post('PayVoucherAutoId'));
                    }
                }
                $this->session->set_flashdata('s', 'Payment Voucher Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('PayVoucherAutoId'));
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
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
                    $this->update_cheque_detail($data['chequeRegisterDetailID'],$last_id);
                }
                if($enbleAuthorizeSignature==1){
                    if($paymentTypeID==1){
                        $signature = $this->input->post('signature');

                        $this->save_signature_authority_pv($signature ,$this->input->post('PVbankCode'),$last_id);
                    }
                }

                $this->session->set_flashdata('s', 'Payment Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

/**SMSD */
    function save_signature_authority_pv( $sgnIDs,$docID,$lastID){

        $companyID = current_companyID();

        $this->db->trans_start();

        if(count($sgnIDs)>0){

            $this->db->where('documentautoID', $lastID);
            $this->db->where('DocumentID', 'PV');
            $this->db->where('companyID', $companyID);
            $this->db->delete('srp_erp_document_signatures');

            foreach ($sgnIDs as $sgnID) {

                $data['DocumentID'] = 'PV';
                $data['signatureTypeID'] = 1;
                $data['documentautoID'] = $lastID;
                $data['signatureID'] = $sgnID;
                $data['PVbankCode'] = $docID;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
        
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID']      = $this->common_data['current_pc'];
                $data['createdUserID']    = $this->common_data['current_userID'];
                $data['createdUserName']  = $this->common_data['current_user'];
                $data['createdDateTime']  = $this->common_data['current_date'];
                $data['timestamp']        = $this->common_data['current_date'];
        
                $this->db->insert('srp_erp_document_signatures', $data);
           
            }

        }

        $this->db->trans_complete();
    
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
        
    }



    function save_payment_match_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $matchingDate = $this->input->post('matchDate');
        $matchDate = input_format_date($matchingDate, $date_format_policy);

        $supplier_arr = $this->fetch_supplier_data($this->input->post('supplierID'));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['documentID'] = 'PVM';
        $data['matchDate'] = trim($matchDate);
        $narration = ($this->input->post('Narration'));
        $data['Narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['refNo'] = trim($this->input->post('refNo') ?? '');
        $data['supplierID'] = $this->input->post('supplierID');
        $data['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
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
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrencyDecimalPlaces'] = $supplier_arr['supplierCurrencyDecimalPlaces'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('matchID') ?? '')) {
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $this->db->update('srp_erp_pvadvancematch', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Match Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Match Updated Successfully.');
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

            $this->db->insert('srp_erp_pvadvancematch', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Match   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Match Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id, 'currency' => $currency_code[0], 'decimal' => $data['transactionCurrencyDecimalPlaces']);
            }
        }
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_sales_rep_data($salesPersonID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_salespersonmaster');
        $this->db->where('salesPersonID', $salesPersonID);
        return $this->db->get()->row_array();
    }

    function fetch_empyoyee($id)
    {
        $this->db->select('Ename1,Ename2,Ename3,Ename4,ECode,EIdNo,EcAddress1,EcAddress2,EcAddress3,EpTelephone,EpFax,EEmail');
        $this->db->where('EIdNo', $id);
        $this->db->from('srp_employeesdetails');
        return $this->db->get()->row_array();
    }

    function fetch_supplier_inv($supplierID, $currencyID, $PVdate)
    {
        $PVdate = format_date($PVdate);
        /*$output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount FROM srp_erp_paysupplierinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0 AND `supplierID` = '{$supplierID}' AND `transactionCurrencyID` = '{$currencyID}' AND `bookingDate` <= '{$PVdate}'")->result_array();*/

        $output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,srp_erp_paysupplierinvoicemaster.bookingDate,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo, supplierInvoiceNo, 
                            ((((IFNULL(tax.taxPercentage, 0) / 100 ) * ( (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) ) + (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * (IFNULL(sid.transactionAmount, 0) + IFNULL(taxAmount, 0)) ) ) AS transactionAmount
                             FROM srp_erp_paysupplierinvoicemaster 
                             LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount, IFNULL( SUM( taxAmount ), 0 ) AS taxAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID 
                             LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID 
                             WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0  AND `transactionCurrencyID` = '{$currencyID}' AND `supplierID` = '{$supplierID}' AND `bookingDate` <= '{$PVdate}'"
                            )->result_array();

        //AND srp_erp_paysupplierinvoicemaster.paymentTotalAmount != srp_erp_paysupplierinvoicemaster.transactionAmount
        //echo $this->db->last_query();
        //AND `supplierID` = '{$supplierID}'
        return $output;
    }

    function fetch_customer_inv($supplierID, $currencyID, $PVdate)
    {
        $PVdate = format_date($PVdate);

        //check linked customer
        $this->db->select('customerID');
        $this->db->where('supplierAutoID', $supplierID);
        $this->db->from('srp_erp_suppliermaster');
        $supplier_master_details = $this->db->get()->row_array();

        $customerID = $supplier_master_details['customerID'];
 
     
        $output = $this->db->query("
            SELECT
                srp_erp_customerinvoicemaster.InvoiceAutoID,
                srp_erp_customerinvoicemaster.invoiceDate as bookingDate,
                invoiceCode as bookingInvCode,
                srp_erp_customerinvoicemaster.transactionAmount,
                referenceNo as RefNo,
                invoiceNarration as supplierInvoiceNo

            FROM
                srp_erp_customerinvoicemaster
            WHERE 
                confirmedYN = 1 
                AND approvedYN = 1 
                AND receiptInvoiceYN != 1
                AND customerID = '{$customerID}'
                AND transactionCurrencyID = '{$currencyID}' 
                AND invoiceDate <= '{$PVdate}' 
                
                    
        ")->result_array();
        
       
        return $output;
    }

    function fetch_debit_note($supplierID, $currencyID, $documentDate)
    {
        $tmpDate = format_date($documentDate);
        /*$output = $this->db->query("SELECT
                                    masterTbl.debitNoteMasterAutoID AS debitNoteMasterAutoID,
                                    masterTbl.debitNoteCode AS debitNoteCode,
                                    detailTbl.transactionAmount,
                                    masterTbl.docRefNo AS RefNo,
                                    SUM(pvDetail.transactionAmount) AS PVTransactionAmount
                                    FROM
                                        srp_erp_debitnotemaster masterTbl
                                    LEFT JOIN (
                                        SELECT
                                            SUM(transactionAmount) AS transactionAmount,
                                            debitNoteMasterAutoID
                                        FROM
                                            srp_erp_debitnotedetail
                                        WHERE
                                            (ISNULL(InvoiceAutoID) OR InvoiceAutoID = 0)
                                        GROUP BY
                                            debitNoteMasterAutoID
                                    ) detailTbl ON detailTbl.debitNoteMasterAutoID = masterTbl.debitNoteMasterAutoID
                                    LEFT JOIN srp_erp_paymentvoucherdetail AS pvDetail ON pvDetail.debitNoteAutoID = masterTbl.debitNoteMasterAutoID
                                    WHERE
                                        masterTbl.confirmedYN = 1
                                    AND masterTbl.approvedYN = 1
                                    AND masterTbl.transactionCurrencyID = '" . $currencyID . "'
                                    AND masterTbl.debitNoteDate <= '" . $tmpDate . "'
                                    AND masterTbl.supplierID = '" . $supplierID . "'
                                    GROUP BY
                                        masterTbl.debitNoteMasterAutoID")->result_array();*/


        $output = $this->db->query("SELECT * FROM(
                    SELECT
                        masterTbl.debitNoteMasterAutoID AS debitNoteMasterAutoID,
                        masterTbl.debitNoteCode AS debitNoteCode,
                        detailTbl.transactionAmount AS transactionAmount,
                        masterTbl.docRefNo AS RefNo,
                        masterTbl.debitNoteDate AS debitNoteDate,
                        SUM(pvDetail.transactionAmount) AS PVTransactionAmount,
                        SUM( allocation.allocation_amount ) AS allocationAmount,
                        'debitnote' AS type
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
                    LEFT JOIN srp_erp_ap_vendor_payments_master AS vpaymaster ON vpaymaster.id = allocation.master_id AND vpaymaster.confirmedYN = 0
                    WHERE
                        masterTbl.confirmedYN = 1
                    AND masterTbl.approvedYN = 1
                    AND masterTbl.transactionCurrencyID = '" . $currencyID . "'
                    AND masterTbl.debitNoteDate <= '" . $tmpDate . "'
                    AND masterTbl.supplierID = '" . $supplierID . "'
                    
                    GROUP BY
                        masterTbl.debitNoteMasterAutoID

                    UNION ALL

                    SELECT
                        masterTbl.stockReturnAutoID AS debitNoteMasterAutoID,
                        masterTbl.stockReturnCode AS debitNoteCode,
                        detailTbl.transactionAmount,
                        masterTbl.returnDate AS debitNoteDate,
                        masterTbl.referenceNo AS RefNo,
                        SUM(pvDetail.transactionAmount) AS PVTransactionAmount,
                        0 AS allocationAmount,
                        'SR' AS type
                       
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
                    AND masterTbl.returnDate <= '" . $tmpDate . "'
                    AND masterTbl.supplierID = '" . $supplierID . "'
                    GROUP BY
                        masterTbl.stockReturnAutoID) as result")->result_array();
       // echo $this->db->last_query();
        return $output;

    }

    function fetch_supplier_po($supplierID, $currencyID, $PVdate)
    {
        $date_format_policy = date_format_policy();
        $format_PVdate = input_format_date($PVdate, $date_format_policy);
        $output = $this->db->query("SELECT purchaseOrderID,purchaseOrderCode,transactionAmount,narration,referenceNumber,expectedDeliveryDate FROM srp_erp_purchaseordermaster WHERE confirmedYN = 1 AND approvedYN = 1 AND supplierID = $supplierID AND transactionCurrencyID = $currencyID AND documentDate <= '" . $format_PVdate . "'")->result_array();
        return $output;
    }

    function save_inv_base_items()
    {
        $this->db->trans_start();
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $settlementAmount =  $this->input->post('settlementAmount');
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $type = $this->input->post('type');

        

        if($type == 'CUS'){

            $cus_inv = join(',',$InvoiceAutoID);

            $master_recode = $this->db->query("
                SELECT
                    srp_erp_customerinvoicemaster.InvoiceAutoID,
                    srp_erp_customerinvoicemaster.invoiceDate as bookingDate,
                    invoiceCode as bookingInvCode,
                    srp_erp_customerinvoicemaster.transactionAmount,
                    srp_erp_customerinvoicemaster.transactionAmount as paymentTotalAmount,
                    0 as DebitNoteTotalAmount,
                    0 as advanceMatchedTotal,
                    referenceNo as RefNo,
                    invoiceNarration as supplierInvoiceNo,
                    customerReceivableAutoID as supplierliabilityAutoID,
                    customerReceivableSystemGLCode as supplierliabilitySystemGLCode,
                    customerReceivableGLAccount as supplierliabilityGLAccount,
                    customerReceivableDescription as supplierliabilityDescription,
                    customerReceivableType as supplierliabilityType,
                    '' as comments,
                    srp_erp_customerinvoicemaster.*,
                    customerCurrency as supplierCurrency,
                    customerCurrencyID as supplierCurrencyID,
                    customerCurrencyExchangeRate as supplierCurrencyExchangeRate

                FROM
                    srp_erp_customerinvoicemaster
                WHERE 
                    srp_erp_customerinvoicemaster.InvoiceAutoID IN ($cus_inv)
                        
                ")->result_array();

        }else{

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
            $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $master_recode = $this->db->get()->result_array();

        }
       

        $amount = $this->input->post('amount');
        $am_arr = []; $inv_arr = []; $re_arr = []; $customer_inv = [];

        foreach($this->input->post('InvoiceAutoID') as $key=>$row){
            $am_arr[$row] = $amount[$key];
        }

        for ($i = 0; $i < count($master_recode); $i++) {
            $invAutoID = $master_recode[$i]['InvoiceAutoID'];

            $due_amount = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['paymentTotalAmount'] + $master_recode[$i]['DebitNoteTotalAmount'] + $master_recode[$i]['advanceMatchedTotal']));
            
            if($type == 'CUS'){
                $data[$i]['detailInvoiceType'] = $type;
            }

            $data[$i]['payVoucherAutoId'] = $this->input->post('payVoucherAutoId');
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
            $data[$i]['due_amount'] = $due_amount;
            $data[$i]['balance_amount'] = ($data[$i]['due_amount'] - (float)$am_arr[$invAutoID]);
            $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
            $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
            $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
            $data[$i]['transactionAmount'] = (float)$am_arr[$invAutoID];
            $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
            $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
            $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
            $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
            $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
            $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
            $data[$i]['partyCurrencyID'] = $master_recode[$i]['supplierCurrencyID'];
            $data[$i]['partyCurrency'] = $master_recode[$i]['supplierCurrency'];
            $data[$i]['partyExchangeRate'] = $master_recode[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['supplierCurrencyExchangeRate']);
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

            if($type == 'CUS'){
                $customer_inv[] = $master_recode[$i]['InvoiceAutoID'];
            }else{
                $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID[$i];
                $grv_m[$i]['paymentTotalAmount'] = ($master_recode[$i]['paymentTotalAmount'] + $am_arr[$invAutoID]);
                $grv_m[$i]['paymentInvoiceYN'] = 0;
                if ($data[$i]['balance_amount'] <= 0) {
                    $grv_m[$i]['paymentInvoiceYN'] = 1;
                }
            }
        
        }

        $data_up_settlement['settlementTotal'] = $settlementAmount;
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->update('srp_erp_paymentvouchermaster', $data_up_settlement);

        if(!empty($customer_inv)){
            foreach($customer_inv as $customerInvoiceID){
                $this->db->where('invoiceAutoID',$customerInvoiceID)->where('companyID',$this->common_data['company_data']['company_id'])->update('srp_erp_customerinvoicemaster', array('receiptInvoiceYN' => 1));
            }
        }


        if (!empty($data)) {
            if(count($grv_m) > 0 ) {
                $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
            }
            
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
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

    function save_debitNote_base_items()
    {
        $this->db->trans_start();
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        /** Array */
        $debitNoteMasterIDs = $this->input->post('debitNoteMasterID');
        $amount = $this->input->post('amount');
        $types = $this->input->post('types');
        $transactionAmount = $this->input->post('transactionAmount');


        if (!empty($debitNoteMasterIDs)) {
            $i = 0;
            foreach ($debitNoteMasterIDs as $debitNoteMasterID) {
                if ($types[$i] == 'debitnote') {
                    $master_recode = $this->get_debitNote_master($debitNoteMasterID);
                    $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($debitNoteMasterID, $types[$i]); // use this value to get due amount
                    $due_amount = $transactionAmount[$i] - $alreadyPaidAmount;
                    $balance_amount = $due_amount - $amount[$i];


                    $data[$i]['debitNoteAutoID'] = $debitNoteMasterIDs[$i];
                    $data[$i]['InvoiceAutoID'] = null;
                    $data[$i]['type'] = $types[$i];
                    $data[$i]['payVoucherAutoId'] = $payVoucherAutoId;
                    $data[$i]['bookingInvCode'] = $master_recode['debitNoteCode'];
                    $data[$i]['referenceNo'] = $master_recode['docRefNo'];
                    $data[$i]['bookingDate'] = $master_recode['debitNoteDate'];
                    $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
                    $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
                    $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
                    $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
                    $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
                    $data[$i]['description'] = $master_recode['comments'];

                    $data[$i]['Invoice_amount'] = $transactionAmount[$i];
                    $data[$i]['due_amount'] = $due_amount;
                    $data[$i]['balance_amount'] = $balance_amount;

                    $data[$i]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $data[$i]['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $data[$i]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $data[$i]['transactionAmount'] = (float)$amount[$i];
                    $data[$i]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $data[$i]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                    $data[$i]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $data[$i]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                    $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                    $data[$i]['partyCurrencyID'] = $master_recode['supplierCurrencyID'];
                    $data[$i]['partyCurrency'] = $master_recode['supplierCurrency'];
                    $data[$i]['partyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
                    $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);

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
                } else {
                    $master_recode = $this->get_stockReturn_master($debitNoteMasterID);
                    $alreadyPaidAmount = $this->get_debitNote_paymentVoucher_transactionAmount($debitNoteMasterID, $types[$i]); // use this value to get due amount
                    $due_amount = $transactionAmount[$i] - $alreadyPaidAmount;
                    $balance_amount = $due_amount - $amount[$i];


                    $data[$i]['debitNoteAutoID'] = $debitNoteMasterIDs[$i];
                    $data[$i]['InvoiceAutoID'] = null;
                    $data[$i]['type'] = $types[$i];
                    $data[$i]['payVoucherAutoId'] = $payVoucherAutoId;
                    $data[$i]['bookingInvCode'] = $master_recode['stockReturnCode'];
                    $data[$i]['referenceNo'] = $master_recode['referenceNo'];
                    $data[$i]['bookingDate'] = $master_recode['returnDate'];
                    $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
                    $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
                    $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
                    $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
                    $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
                    $data[$i]['description'] = $master_recode['comment'];
                    $data[$i]['Invoice_amount'] = $transactionAmount[$i];
                    $data[$i]['due_amount'] = $due_amount;
                    $data[$i]['balance_amount'] = $balance_amount;
                    $data[$i]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $data[$i]['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $data[$i]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $data[$i]['transactionAmount'] = (float)$amount[$i];
                    $data[$i]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $data[$i]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                    $data[$i]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $data[$i]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                    $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                    $data[$i]['partyCurrencyID'] = $master_recode['supplierCurrencyID'];
                    $data[$i]['partyCurrency'] = $master_recode['supplierCurrency'];
                    $data[$i]['partyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
                    $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);
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
        }


        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($master_recode) . ' Item Details Saved Successfully . ');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function delete_payment_voucher()
    {
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $data = $this->db->get('srp_erp_paymentvoucherdetail')->result_array();

        $this->db->select('PVcode');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        $master = $this->db->get()->row_array();

        if ($data) {
            $this->session->set_flashdata('e', 'Please delete all the details records before deleting this document.');
            return true;
        } else {
            if ($master['PVcode'] == "0") {
                $dataD = array(
                    'status' => 0,
                    'documentMasterAutoID' => null,
                    'documentID' => null
                );
                $this->db->where('documentMasterAutoID', $this->input->post('payVoucherAutoId'));
                $this->db->where('documentID', 'PV');
                $this->db->update('srp_erp_chequeregisterdetails', $dataD);

                $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
                $results = $this->db->delete('srp_erp_paymentvouchermaster');
                $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
                $results = $this->db->delete('srp_erp_paymentvouchermaster');
                if ($results) {
                    $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
                    $this->db->delete('srp_erp_paymentvoucherdetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
                $this->db->update('srp_erp_paymentvouchermaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }
        /*$this->db->delete('srp_erp_paymentvouchermaster', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId') ?? '')));
        $this->db->delete('srp_erp_paymentvoucherdetail', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId') ?? '')));*/

    }

    function delete_payment_match()
    {
        /*$this->db->where('matchID', $this->input->post('matchID'));
        $data = $this->db->get('srp_erp_pvadvancematchdetails')->result_array();
        foreach ($data as $val_as) {
            $id = $val_as['InvoiceAutoID'];
            $amo = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}) WHERE InvoiceAutoID='{
        $id}'");
        }
        $this->db->delete('srp_erp_pvadvancematch', array('matchID' => trim($this->input->post('matchID') ?? '')));
        $this->db->delete('srp_erp_pvadvancematchdetails', array('matchID' => trim($this->input->post('matchID') ?? '')));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_pvadvancematchdetails');
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
            $this->db->update('srp_erp_pvadvancematch', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        }

    }

    function delete_pv_match_detail()
    {
        $this->db->select('InvoiceAutoID,transactionAmount');
        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $data = $this->db->get('srp_erp_pvadvancematchdetails')->row_array();
        $id = $data['InvoiceAutoID'];
        $amo = $data['transactionAmount'];
        $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal-{$amo}),paymentInvoiceYN =0 WHERE InvoiceAutoID=$id");

        $this->db->where('matchDetailID', $this->input->post('matchDetailID'));
        $results = $this->db->delete('srp_erp_pvadvancematchdetails');
        $this->session->set_flashdata('s', 'Payment Matching Detail Deleted Successfully');
        return true;
    }

    function delete_item_direct()
    {
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.isSubitemExist');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->where('srp_erp_paymentvoucherdetail.payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        $detail_arr = $this->db->get()->row_array();

        /** delete sub item in PV*/
        if ($detail_arr['isSubitemExist'] == 1) {
            $this->db->where('receivedDocumentID', 'PV');
            $this->db->where('receivedDocumentAutoID', $detail_arr['payVoucherAutoId']);
            $this->db->where('receivedDocumentDetailID', $detail_arr['payVoucherDetailAutoID']);
            $this->db->delete('srp_erp_itemmaster_subtemp');
        }
        /**end  delete sub item in PV*/

        if ($detail_arr['type'] == 'Invoice') {
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $detail_arr['InvoiceAutoID'];
            $number = $detail_arr['transactionAmount'];
            $status = 0;
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET paymentTotalAmount = (paymentTotalAmount -{$number}), paymentInvoiceYN = {$status} WHERE InvoiceAutoID= $match_id and companyID = $company_id");
            //echo $this->db->last_query();

            if($detail_arr['detailInvoiceType'] == 'CUS'){
          
                $this->db->where('invoiceAutoID',$detail_arr['InvoiceAutoID'])->where('companyID',$this->common_data['company_data']['company_id'])->update('srp_erp_customerinvoicemaster', array('receiptInvoiceYN' => 0));

            }

        }

        $this->db->delete('srp_erp_taxledger', array('documentID' => 'PV','documentMasterAutoID' => $detail_arr['payVoucherAutoId'],'documentDetailAutoID' => trim($this->input->post('payVoucherDetailAutoID') ?? '')));

        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        $results = $this->db->delete('srp_erp_paymentvoucherdetail');


        if ($results) {
            $this->session->set_flashdata('s', 'Payment Voucher Detail Deleted Successfully');
            return true;
        }
    }

    function save_sales_rep_payment()
    {
        // if (!empty($this->input->post('payVoucherDetailAutoID'))) {
        //     $this->db->select('itemDescription,itemSystemCode');
        //     $this->db->from('srp_erp_paymentvoucherdetail');
        //     $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        //     $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
        //     $this->db->where('payVoucherDetailAutoID != ', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        //     $order_detail = $this->db->get()->row_array();
        //     if (!empty($order_detail)) {
        //         return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
        //     }
        // }

        $this->db->select(' * ');
        $this->db->where('salesPersonID', trim($this->input->post('salesPersonID') ?? ''));
        $salesperson = $this->db->get('srp_erp_salespersonmaster')->row_array();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->trans_start();
        $data['type'] = 'SC';
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['salesPersonID'] = trim($this->input->post('salesPersonID') ?? '');
        // $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        // $data['itemDescription'] = $item_arr['itemDescription'];
        // $data['unitOfMeasure'] = trim($uom[0] ?? '');
        // $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        // $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        // $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        // $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        // $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        // $data['unittransactionAmount'] = trim($this->input->post('estimatedAmount') ?? '');
        $data['segmentID'] = $salesperson['segmentID'];
        $data['segmentCode'] = $salesperson['segmentCode'];
        $data['transactionCurrencyID'] = $salesperson['salesPersonCurrencyID'];
        $data['transactionCurrency'] = $salesperson['salesPersonCurrency'];
        $data['transactionExchangeRate'] = 1;//$salesperson['transactionExchangeRate'];
        $data['transactionAmount'] = trim($this->input->post('transactionAmount') ?? '');
        $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        // $data['comment'] = trim($this->input->post('comment') ?? '');
        // $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['wareHouseAutoID'] = $salesperson['wareHouseAutoID'];
        $data['wareHouseCode'] = $salesperson['wareHouseCode'];
        $data['wareHouseLocation'] = $salesperson['wareHouseLocation'];
        $data['wareHouseDescription'] = $salesperson['wareHouseDescription'];
        $data['GLAutoID'] = $salesperson['receivableAutoID'];
        $data['systemGLCode'] = $salesperson['receivableSystemGLCode'];
        $data['GLCode'] = $salesperson['receivableGLAccount'];
        $data['GLDescription'] = $salesperson['receivableDescription'];
        $data['GLType'] = $salesperson['receivableType'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID') ?? '')) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Updated Successfully . ');
            }
        } else {
            $this->db->delete('srp_erp_paymentvoucherdetail', array('payVoucherAutoId' => trim($this->input->post('payVoucherAutoId') ?? '')));
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Payment Voucher Detail : ' . $salesperson['SalesPersonName'] . ' Saved Successfully . ');
            }
        }
    }

    function save_pv_item_detail()
    {
        $projectExist = project_is_exist();
        $payVoucherDetailAutoID = trim($this->input->post('payVoucherDetailAutoID') ?? '');

        $this->db->select('mainCategory');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $serviceitm= $this->db->get()->row_array();
        if($serviceitm['mainCategory']=="Inventory") {
            if (!empty($payVoucherDetailAutoID)) {
                $this->db->select('itemDescription,itemSystemCode');
                $this->db->from('srp_erp_paymentvoucherdetail');
                $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
                $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
                $this->db->where('payVoucherDetailAutoID != ', $payVoucherDetailAutoID);
                $order_detail = $this->db->get()->row_array();
                // if (!empty($order_detail)) {
                //     return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
                // }
            }
        }
        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->trans_start();
        $wareHouse_location = explode(' | ', trim($this->input->post('wareHouse') ?? ''));
        $uom = explode(' | ', $this->input->post('uom'));
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        if ($projectExist == 1) {
            $projectID = trim($this->input->post('projectID') ?? '');
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
            $data['projectID'] = $projectID;
            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            $data['project_categoryID'] = $this->input->post('project_categoryID');
            $data['project_subCategoryID'] = $this->input->post('project_subCategoryID');
        }

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $batch_number1 = $this->input->post('batch_number');
            $arraydata1 = implode(',',$batch_number1);
            $data['batchNumber'] = $arraydata1;

        }

        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if( $advanceCostCapturing == 1){
            $data['activityCodeID'] = trim($this->input->post('activityCode') ?? '');
        }

        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['SUOMID'] = trim($this->input->post('SUOMIDhn') ?? '');
        $data['SUOMQty'] = trim($this->input->post('SUOMQty') ?? '');
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unittransactionAmount'] = trim($this->input->post('estimatedAmount') ?? '');
        $data['segmentID'] = $master_recode['segmentID'];
        $data['segmentCode'] = $master_recode['segmentCode'];
        $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
        $data['transactionCurrency'] = $master_recode['transactionCurrency'];
        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
        $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['type'] = 'Item';
        $data['wareHouseAutoID'] = trim($this->input->post('wareHouseAutoID') ?? '');
        $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
        $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
        $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
        $item_data = fetch_item_data($data['itemAutoID']);
        if ($item_data['mainCategory'] == 'Inventory') {
            $data['GLAutoID'] = $item_data['assteGLAutoID'];
            $data['systemGLCode'] = $item_data['assteSystemGLCode'];
            $data['GLCode'] = $item_data['assteGLCode'];
            $data['GLDescription'] = $item_data['assteDescription'];
            $data['GLType'] = $item_data['assteType'];
        } else if ($item_data['mainCategory'] == 'Fixed Assets') {
            $data['GLAutoID'] = $ACA_ID['GLAutoID'];
            $data['systemGLCode'] = $ACA['systemAccountCode'];
            $data['GLCode'] = $ACA['GLSecondaryCode'];
            $data['GLDescription'] = $ACA['GLDescription'];
            $data['GLType'] = $ACA['subCategory'];
        } else {
            $data['GLAutoID'] = $item_data['costGLAutoID'];
            $data['systemGLCode'] = $item_data['costSystemGLCode'];
            $data['GLCode'] = $item_data['costGLCode'];
            $data['GLDescription'] = $item_data['costDescription'];
            $data['GLType'] = $item_data['costType'];
        }
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if ($payVoucherDetailAutoID) {
            /*echo 'payVoucherDetailAutoID: '.$payVoucherDetailAutoID;
            exit;*/

            /** update sub item master */
            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['payVoucherDetailAutoID'] = $payVoucherDetailAutoID;


            $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['payVoucherAutoId'], $payVoucherDetailAutoID, 'PV', $data['itemSystemCode'], $subData);

            $this->db->where('payVoucherDetailAutoID', $payVoucherDetailAutoID);
            $this->db->update('srp_erp_paymentvoucherdetail', $data);

            $item_text = $this->input->post('item_text');
            $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',$this->input->post('payVoucherAutoId'),'PV','payVoucherAutoId');
            if(!empty($item_text)){   
                if($group_based_tax == 1){
                    tax_calculation_vat(null,null,$item_text,'payVoucherAutoId',trim($this->input->post('payVoucherAutoId') ?? ''),$data['transactionAmount'],'PV',$payVoucherDetailAutoID,0,1);
                } else {
                    $data['taxCalculationformulaID'] = null;
                    $data['taxAmount'] = 0;
                    fetchExistsDetailTBL('PV', trim($this->input->post('payVoucherAutoId') ?? ''),trim($payVoucherDetailAutoID),null,0,$data['transactionAmount']);
                }
            } else {
                $data['taxCalculationformulaID'] = null;
                $data['taxAmount'] = 0;
                $this->db->where('payVoucherDetailAutoID', $payVoucherDetailAutoID);
                $this->db->update('srp_erp_paymentvoucherdetail', $data);
                fetchExistsDetailTBL('PV', trim($this->input->post('payVoucherAutoId') ?? ''),trim($payVoucherDetailAutoID),null,0,$data['transactionAmount']);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully . ');

                //return array('status' => true, 'last_id' => $this->input->post('purchaseOrderDetailsID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();

            $this->db->select('itemAutoID');
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
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


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Saved Successfully . ');
            }
        }
    }

    function save_pv_item_detail_multiple()
    {
        $projectExist = project_is_exist();
        $payVoucherDetailAutoID = $this->input->post('payVoucherDetailAutoID');
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $comment = $this->input->post('comment');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMID = $this->input->post('SUOMIDhn');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',$payVoucherAutoId,'PV','payVoucherAutoId');
        $tax_type = $this->input->post('item_text');
        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,companyReportingCurrencyID,partyCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if( $advanceCostCapturing == 1){
            $activityCodeID = $this->input->post('activityCode');
        }

        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $itemAutoID);
            $serviceitm= $this->db->get()->row_array();

            if (!trim($this->input->post('payVoucherDetailAutoID') ?? '')) {
                if($serviceitm['mainCategory']=="Inventory") {
                    $this->db->select('itemDescription,itemSystemCode');
                    $this->db->from('srp_erp_paymentvoucherdetail');
                    $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
                    $this->db->where('itemAutoID', $itemAutoID);
                    $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                    $order_detail = $this->db->get()->row_array();
                    // if (!empty($order_detail)) {
                    //     return array('w', 'Payment Voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
                    // }
                }
                $wareHouse_location = explode(' | ', $wareHouse[$key]);
                $item_arr = fetch_item_data($itemAutoID);
                $uomEx = explode(' | ', $uom[$key]);

                $data['payVoucherAutoId'] = trim($payVoucherAutoId);
                $data['itemAutoID'] = $itemAutoID;
                $data['itemSystemCode'] = $item_arr['itemSystemCode'];
                $data['itemDescription'] = $item_arr['itemDescription'];

                if($advanceCostCapturing == 1){
                    $data['activityCodeID'] = trim($activityCodeID[$key]);
                }

                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID[$key]);
                    $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                    $data['project_categoryID'] = $project_categoryID[$key];
                    $data['project_subCategoryID'] = $project_subCategoryID[$key];
                }

                $itemBatchPolicy = getPolicyValues('IB', 'All');

                if($itemBatchPolicy==1){

                    $batch_number2 = $this->input->post("batch_number[{$key}]");
                    $arraydata2 = implode(",",$batch_number2);
                    $data['batchNumber'] = $arraydata2;
                }

                $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
                $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
                $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                $data['SUOMID'] = $SUOMID[$key] ?? '';
                $data['SUOMQty'] = $SUOMQty[$key] ?? '';
                $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['requestedQty'] = $quantityRequested[$key];
                $data['unittransactionAmount'] = $estimatedAmount[$key];
                $data['segmentID'] = $master_recode['segmentID'];
                $data['segmentCode'] = $master_recode['segmentCode'];
                $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty']);
                $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['partyCurrency'] = $master_recode['partyCurrency'];
                $data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
                $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
                $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
                $data['comment'] = $comment[$key];
                $data['remarks'] = '';
                $data['type'] = 'Item';
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];
                $data['wareHouseCode'] = trim($wareHouse_location[0] ?? '');
                $data['wareHouseLocation'] = trim($wareHouse_location[1] ?? '');
                $data['wareHouseDescription'] = trim($wareHouse_location[2] ?? '');
                $item_data = fetch_item_data($data['itemAutoID']);
                if ($item_data['mainCategory'] == 'Inventory') {
                    $data['GLAutoID'] = $item_data['assteGLAutoID'];
                    $data['systemGLCode'] = $item_data['assteSystemGLCode'];
                    $data['GLCode'] = $item_data['assteGLCode'];
                    $data['GLDescription'] = $item_data['assteDescription'];
                    $data['GLType'] = $item_data['assteType'];
                } else if ($item_data['mainCategory'] == 'Fixed Assets') {
                    $data['GLAutoID'] = $ACA_ID['GLAutoID'];
                    $data['systemGLCode'] = $ACA['systemAccountCode'];
                    $data['GLCode'] = $ACA['GLSecondaryCode'];
                    $data['GLDescription'] = $ACA['GLDescription'];
                    $data['GLType'] = $ACA['subCategory'];
                } else {
                    $data['GLAutoID'] = $item_data['costGLAutoID'];
                    $data['systemGLCode'] = $item_data['costSystemGLCode'];
                    $data['GLCode'] = $item_data['costGLCode'];
                    $data['GLDescription'] = $item_data['costDescription'];
                    $data['GLType'] = $item_data['costType'];
                }
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
                $this->db->insert('srp_erp_paymentvoucherdetail', $data);
                $last_id = $this->db->insert_id();

                if(!empty($tax_type[$key])){   
                    if($group_based_tax == 1){ 
                        $this->db->select('*');
                        $this->db->where('taxCalculationformulaID',$tax_type[$key]);
                        $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                        tax_calculation_vat(null,null,$tax_type[$key],'payVoucherAutoId',trim($this->input->post('payVoucherAutoId') ?? ''),$data['transactionAmount'],'PV',$last_id,0,1);
                    }
                }


                /** add sub item config*/
                if ($item_data['isSubitemExist'] == 1) {

                    $qty = 0;
                    if (!empty($itemAutoIDs)) {
                        $x = 0;
                        foreach ($itemAutoIDs as $key => $itemAutoIDTmp) {
                            if ($itemAutoIDTmp == $itemAutoID) {
                                $qty = $quantityRequested[$key];
                                $warehouseID = $wareHouseAutoID[$x];
                            }
                            $x++;
                        }
                    }

                    $subData['uom'] = $data['unitOfMeasure'];
                    $subData['uomID'] = $data['unitOfMeasureID'];
                    $subData['pv_detailID'] = $last_id;
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $payVoucherAutoId, $last_id, 'PV', $item_data['itemSystemCode'], $subData, $warehouseID);


                }

                /** End add sub item config*/

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
                        'itemSystemCode' => $data['itemSystemCode'],
                        'barCodeNo' => $item_data['barcode'],
                        'salesPrice' => $item_data['companyLocalSellingPrice'],
                        'ActiveYN' => $item_data['isActive'],
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
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Payment Voucher Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Details : Saved Successfully . ');
        }

    }

    function fetch_payment_voucher_template_data($payVoucherAutoId)
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,srp_erp_paymentvouchermaster.subInvoiceList as subInvoiceList,srp_erp_paymentvouchermaster.createdUserName as createdUserName, DATE_FORMAT(srp_erp_paymentvouchermaster.createdDateTime, \'' . $convertFormat . '\') AS createdDateTime, srp_erp_paymentvouchermaster.createdUserName as crName,srp_erp_segment.description as segDescription,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_suppliermaster.nameOnCheque as nameOnCheque,srp_erp_suppliermaster.supplierName as supname,srp_erp_suppliermaster.supplierSystemCode as supsyscode, srp_erp_suppliermaster.supplierAddress1 as supaddress1, srp_erp_suppliermaster.supplierTelephone as suptel,srp_erp_suppliermaster.supplierFax as supfax, 
        case pvType when \'Direct\' then partyName when \'DirectItem\' then partyName when \'DirectExpense\' then partyName when \'PurchaseRequest\' then partyName 
        when \'Employee\' then srp_employeesdetails.Ename2 when \'EmployeeExpense\' then srp_employeesdetails.Ename2 when \'EmployeeItem\' then srp_employeesdetails.Ename2 
        when \'Supplier\' then srp_erp_suppliermaster.supplierName when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierName when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierName when \'SupplierItem\' then srp_erp_suppliermaster.supplierName when \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName,
        case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " when \'Employee\' then CONCAT_WS(\', \',
       IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeExpense\' then CONCAT_WS(\', \',
       IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeItem\' then CONCAT_WS(\', \',
       IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))
        when \'Supplier\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierAddress1  
        when \'SupplierItem\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierExpense\' then srp_erp_suppliermaster.supplierAddress1 end as partyAddresss,
        case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " 
        when \'Employee\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeExpense\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeItem\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) 
        when \'Supplier\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierAdvance\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
        when \'SupplierInvoice\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierItem\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
        when \'SupplierExpense\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) end as parttelfax,
        CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),
        IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn
        ');

        $this->db->where('payVoucherAutoId', $payVoucherAutoId);


        $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID', 'Left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'Left');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paymentvouchermaster.segmentID', 'Left');        
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['master'] = $this->db->get()->row_array();


        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('srp_erp_paymentvoucherdetail.*,  srp_erp_activity_code_main.activity_code as activityCodeName, `srp_erp_itemmaster`.`partNo`,	CONCAT_WS(
                        \' - Part No : \',
                    IF
                        ( LENGTH( srp_erp_paymentvoucherdetail.`comment` ), `srp_erp_paymentvoucherdetail`.`comment`, NULL ),
                    IF
                        ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
                        ) AS Itemdescriptionpartno,'.$item_code_alias.'
	        ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paymentvoucherdetail.activityCodeID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $data['item_detail'] = $this->db->get()->result_array();

        $this->db->select('*, srp_erp_paymentvoucherdetail.description as desc, srp_erp_expenseclaimmaster.expenseClaimCode, srp_erp_expenseclaimmaster.expenseClaimMasterAutoID,segment.segmentCode as ecsegmentcode, srp_erp_activity_code_main.activity_code as activityCodeName');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('(type = "GL" OR type = "EC" OR type = "LS")');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paymentvoucherdetail.activityCodeID', 'left');
        $this->db->join('srp_erp_expenseclaimmaster', 'srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = srp_erp_paymentvoucherdetail.expenseClaimMasterAutoID', 'LEFT');
        $this->db->join('srp_erp_segment segment','segment.segmentID = srp_erp_paymentvoucherdetail.segmentID','left');
        $data['gl_detail'] = $this->db->get()->result_array();

        $this->db->select('*, srp_erp_paymentvoucherdetail.description as desc, srp_erp_expenseclaimmaster.expenseClaimCode, srp_erp_expenseclaimmaster.expenseClaimMasterAutoID,segment.segmentCode as ecsegmentcode');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'INGL');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_expenseclaimmaster', 'srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = srp_erp_paymentvoucherdetail.expenseClaimMasterAutoID', 'LEFT');
        $this->db->join('srp_erp_segment segment','segment.segmentID = srp_erp_paymentvoucherdetail.segmentID','left');
        $data['gl_detail_income'] = $this->db->get()->result_array();


        $this->db->select('*, srp_erp_paysupplierinvoicemaster.supplierInvoiceNo, srp_erp_paysupplierinvoicemaster.invoiceDate, srp_erp_paymentvoucherdetail.transactionAmount as transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->where('detailInvoiceType IS NULL',null);
        $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paymentvoucherdetail.invoiceAutoID', 'Left');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('*, srp_erp_paysupplierinvoicemaster.supplierInvoiceNo, srp_erp_paysupplierinvoicemaster.invoiceDate, srp_erp_paymentvoucherdetail.transactionAmount as transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->where('detailInvoiceType', 'CUS');
        $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paymentvoucherdetail.invoiceAutoID', 'Left');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['customer_invoice'] = $this->db->get()->result_array();


        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'SR');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['SR'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_purchaserequestmaster.purchaseRequestCode,'.$item_code_alias.' ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'PRQ');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_paymentvoucherdetail.prMasterID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $data['PRQ'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_paymentvoucherdetail.*, documentDate');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_purchaseordermaster','srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_paymentvoucherdetail.purchaseOrderID','LEFT');
        $data['advance'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'SC');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['sales_commission'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();

        if(isset($data['master']['subInvoiceList']) && $data['master']['subInvoiceList']){
            $sub_invoices = $data['master']['subInvoiceList'];
            $invoice_arr = explode(',',$sub_invoices);
            $master_id = null;
            $firest_invoice = $invoice_arr[0];

            //get payment according to that
            $this->db->where('paymentVoucherAutoID',$firest_invoice);
            $this->db->from('srp_erp_ap_vendor_payments');
            $payemnt_details = $this->db->get()->row_array();

            if($payemnt_details){
                $master_id  = $payemnt_details['master_id'];

                $this->db->where('master_id',$master_id);
                $this->db->from('srp_erp_ap_vendor_payments');
                $data['subInvoicePayments'] = $this->db->get()->result_array();
            }
            
        }

        $this->db->select('*');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->join('srp_erp_supplierbankmaster', 'srp_erp_supplierbankmaster.supplierBankMasterID = srp_erp_paymentvouchermaster.supplierBankMasterID', 'left');
        $this->db->where('srp_erp_paymentvouchermaster.payVoucherAutoId', $payVoucherAutoId);
        $data['supplierBankMaster'] = $this->db->get()->row_array();
        
        return $data;
    }

    function get_pv_vendor_allocation_data($payVoucherAutoId){

        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,srp_erp_paymentvouchermaster.subInvoiceList as subInvoiceList,srp_erp_paymentvouchermaster.createdUserName as createdUserName, DATE_FORMAT(srp_erp_paymentvouchermaster.createdDateTime, \'' . $convertFormat . '\') AS createdDateTime, srp_erp_paymentvouchermaster.createdUserName as crName,srp_erp_segment.description as segDescription,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_suppliermaster.nameOnCheque as nameOnCheque,srp_erp_suppliermaster.supplierName as supname,srp_erp_suppliermaster.supplierSystemCode as supsyscode, srp_erp_suppliermaster.supplierAddress1 as supaddress1,
         srp_erp_suppliermaster.supplierTelephone as suptel,srp_erp_suppliermaster.supplierFax as supfax,srp_erp_paymentvouchermaster.subInvoiceList, srp_erp_paymentvouchermaster.transactionCurrency as CurrencyDes,
            case pvType when \'Direct\' then partyName when \'DirectItem\' then partyName when \'DirectExpense\' then partyName when \'PurchaseRequest\' then partyName 
            when \'Employee\' then srp_employeesdetails.Ename2 when \'EmployeeExpense\' then srp_employeesdetails.Ename2 when \'EmployeeItem\' then srp_employeesdetails.Ename2 
            when \'Supplier\' then srp_erp_suppliermaster.supplierName when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierName when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierName when \'SupplierItem\' then srp_erp_suppliermaster.supplierName when \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName,
            case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " when \'Employee\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeExpense\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeItem\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))
            when \'Supplier\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierAddress1  
            when \'SupplierItem\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierExpense\' then srp_erp_suppliermaster.supplierAddress1 end as partyAddresss,
            case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " 
            when \'Employee\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeExpense\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeItem\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) 
            when \'Supplier\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierAdvance\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
            when \'SupplierInvoice\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierItem\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
            when \'SupplierExpense\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) end as parttelfax,
            CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),
            IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn
        ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID', 'Left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'Left');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paymentvouchermaster.segmentID', 'Left');        
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['master'] = $this->db->get()->row_array();


        if(isset($data['master']['subInvoiceList']) && $data['master']['subInvoiceList']){
            $sub_invoices = $data['master']['subInvoiceList'];
            $invoice_arr = explode(',',$sub_invoices);
            $master_id = null;
            $firest_invoice = $invoice_arr[0];

            //get payment according to that
            $this->db->where('paymentVoucherAutoID',$firest_invoice);
            $this->db->from('srp_erp_ap_vendor_payments');
            $payemnt_details = $this->db->get()->row_array();

            if($payemnt_details){
                $master_id  = $payemnt_details['master_id'];

                $this->db->where('master_id',$master_id);
                $this->db->from('srp_erp_ap_vendor_payments');
                $data['subInvoicePayments'] = $this->db->get()->result_array();
            }


            $payments = $this->db->query("
                SELECT payment.*
                FROM srp_erp_ap_vendor_payments as payment
                LEFT JOIN srp_erp_suppliermaster as master ON payment.vendor_code = master.supplierSystemCode
                WHERE payment.paymentVoucherAutoID IN ($sub_invoices)
            ")->result_array();
            
            $base_arr = array();
            foreach($payments as $vpayment){

                $base_arr[$vpayment['vendor_code']]['master'] = $vpayment;

                $base_arr[$vpayment['vendor_code']]['invoices'] = $this->db->query("SELECT srp_erp_ap_vendor_invoice_allocation.*,srp_erp_paysupplierinvoicemaster.RefNo,srp_erp_paysupplierinvoicemaster.supplierInvoiceNo FROM srp_erp_ap_vendor_invoice_allocation 
                LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_ap_vendor_invoice_allocation.doc_id = srp_erp_paysupplierinvoicemaster.bookingInvCode WHERE payment_id = '{$vpayment['id']}' ")->result_array();

            }
        
        }

        $data['invoices'] = $base_arr;
        
        return $data;


    }

    function get_pv_vendor_allocation_invoice_data($payVoucherAutoId){

     $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,srp_erp_paymentvouchermaster.subInvoiceList as subInvoiceList,srp_erp_paymentvouchermaster.createdUserName as createdUserName, DATE_FORMAT(srp_erp_paymentvouchermaster.createdDateTime, \'' . $convertFormat . '\') AS createdDateTime, srp_erp_paymentvouchermaster.createdUserName as crName,srp_erp_segment.description as segDescription,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_suppliermaster.nameOnCheque as nameOnCheque,srp_erp_suppliermaster.supplierName as supname,srp_erp_suppliermaster.supplierSystemCode as supsyscode, srp_erp_suppliermaster.supplierAddress1 as supaddress1,
         srp_erp_suppliermaster.supplierTelephone as suptel,srp_erp_suppliermaster.supplierFax as supfax,srp_erp_paymentvouchermaster.subInvoiceList, srp_erp_paymentvouchermaster.transactionCurrency as CurrencyDes,
            case pvType when \'Direct\' then partyName when \'DirectItem\' then partyName when \'DirectExpense\' then partyName when \'PurchaseRequest\' then partyName 
            when \'Employee\' then srp_employeesdetails.Ename2 when \'EmployeeExpense\' then srp_employeesdetails.Ename2 when \'EmployeeItem\' then srp_employeesdetails.Ename2 
            when \'Supplier\' then srp_erp_suppliermaster.supplierName when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierName when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierName when \'SupplierItem\' then srp_erp_suppliermaster.supplierName when \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName,
            case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " when \'Employee\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeExpense\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))when \'EmployeeItem\' then CONCAT_WS(\', \',
        IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
        IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL))
            when \'Supplier\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierAdvance\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierInvoice\' then srp_erp_suppliermaster.supplierAddress1  
            when \'SupplierItem\' then srp_erp_suppliermaster.supplierAddress1 when \'SupplierExpense\' then srp_erp_suppliermaster.supplierAddress1 end as partyAddresss,
            case pvType when \'Direct\' then " " when \'DirectItem\' then " " when \'DirectExpense\' then " " 
            when \'Employee\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeExpense\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'EmployeeItem\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) 
            when \'Supplier\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierAdvance\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
            when \'SupplierInvoice\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) when \'SupplierItem\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax)
            when \'SupplierExpense\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) end as parttelfax,
            CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),
            IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn
        ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID', 'Left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'Left');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paymentvouchermaster.segmentID', 'Left');        
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->order_by('srp_erp_suppliermaster.supplierName');
        $data['master'] = $this->db->get()->row_array();

        // print_r($payVoucherAutoId); exit;
        
        //get payment according to that
        $this->db->where('master_id',$payVoucherAutoId);
        $this->db->from('srp_erp_ap_vendor_payments');
        $this->db->order_by('srp_erp_ap_vendor_payments.vendor_name');
        $payments = $this->db->get()->result_array();

            
        $base_arr = array();
        foreach($payments as $vpayment){

           if($vpayment['id']){
                $base_arr[$vpayment['vendor_code']]['master'] = $vpayment;

                $base_arr[$vpayment['vendor_code']]['invoices'] = $this->db->query("
                    SELECT 
                    srp_erp_ap_vendor_invoice_allocation.*,
                    srp_erp_paysupplierinvoicemaster.bookingInvCode as doc_id,
                    (CASE
                        WHEN srp_erp_ap_vendor_invoice_allocation.invoiceType = 'SupplierInvoice' THEN srp_erp_paysupplierinvoicemaster.RefNo
                        WHEN srp_erp_ap_vendor_invoice_allocation.invoiceType = 'debitnote' THEN srp_erp_debitnotemaster.docRefNo
                    END) as RefNo,
                    (CASE
                        WHEN srp_erp_ap_vendor_invoice_allocation.invoiceType = 'SupplierInvoice' THEN srp_erp_paysupplierinvoicemaster.supplierInvoiceNo
                        WHEN srp_erp_ap_vendor_invoice_allocation.invoiceType = 'debitnote' THEN srp_erp_debitnotemaster.docRefNo
                    END) as supplierInvoiceNo
                    FROM srp_erp_ap_vendor_invoice_allocation 
                    LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_ap_vendor_invoice_allocation.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
                    LEFT JOIN srp_erp_debitnotemaster ON srp_erp_ap_vendor_invoice_allocation.InvoiceAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID
                    WHERE payment_id = '{$vpayment['id']}' AND  srp_erp_ap_vendor_invoice_allocation.status = 1
                ")->result_array();

          
            }
           
        }
        
    

        $data['invoices'] = $base_arr;
        // $data['master']['transactionCurrencyDecimalPlaces'] = 2;

    
        
        return $data;


    }

    function fetch_payment_voucher_match_template_data($matchID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate, DATE_FORMAT(confirmedDate,\'' . $convertFormat . ' %h:%i:%s\') AS confirmedDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_pvadvancematch');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('*,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate');
        $this->db->where('matchID', $matchID);
        $this->db->from('srp_erp_pvadvancematchdetails');
        $data['detail'] = $this->db->get()->result_array();
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

    function load_payment_voucher_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,CASE WHEN pvType = \'Supplier\'   THEN \'SupplierInvoice\'  
	    WHEN pvType = \'Direct\'  THEN \'DirectItem\'  WHEN pvType = \'Employee\'  THEN \'EmployeeItem\' ELSE pvType END as documenttype');
        $this->db->where('payVoucherAutoId', $this->input->post('PayVoucherAutoId'));
        return $this->db->get('srp_erp_paymentvouchermaster')->row_array();
    }

    function load_payment_voucher_Signatures(){

        $PayVoucherAutoId = $this->input->post('PayVoucherAutoId');
        $this->db->select('*');
        $this->db->where('documentautoID', $PayVoucherAutoId);
        $this->db->where('documentID','PV');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_document_signatures')->result_array();

    }

    function load_payment_match_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_pvadvancematch')->row_array();
    }

    function fetch_match_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_pvadvancematchdetails.PVdate,\'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(srp_erp_pvadvancematchdetails.bookingDate,\'' . $convertFormat . '\') AS bookingDate');
        $this->db->where('matchID', $this->input->post('matchID'));
        return $this->db->get('srp_erp_pvadvancematchdetails')->result_array();
    }

    //changes
    function fetch_pv_direct_details()
    {
        $payVoucherAutoId = trim($this->input->post('payVoucherAutoId') ?? '');
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['currency'] = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->select('srp_erp_taxcalculationformulamaster.Description as taxdescription, srp_erp_activity_code_main.activity_code as activityCodeName,srp_erp_paymentvoucherdetail.*,IFNULL(discountAmount, 0) as discountAmount, srp_erp_itemmaster.isSubitemExist,CONCAT_WS(
                    \' - Part No : \',
                IF
                    ( LENGTH( srp_erp_paymentvoucherdetail.`comment` ), `srp_erp_paymentvoucherdetail`.`comment`, NULL ),
                IF
                    ( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
                    ) AS Itemdescriptionpartno,srp_erp_purchaserequestmaster.purchaseRequestCode, srp_erp_expenseclaimmaster.expenseClaimCode, srp_erp_expenseclaimmaster.expenseClaimMasterAutoID, IFNULL( taxAmount, 0 ) AS taxAmount, 
                    srp_erp_purchaseordermaster.documentDate as PODate, '.$item_code_alias.' ');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paymentvoucherdetail.activityCodeID', 'left');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_paymentvoucherdetail.prMasterID', 'left');
        $this->db->join('srp_erp_purchaseordermaster','srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_paymentvoucherdetail.purchaseOrderID','LEFT');
        $this->db->join('srp_erp_expenseclaimmaster', 'srp_erp_expenseclaimmaster.expenseClaimMasterAutoID = srp_erp_paymentvoucherdetail.expenseClaimMasterAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster','srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paymentvoucherdetail.taxCalculationformulaID','left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_detail()
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
        $this->db->select('*,'.$item_code_alias.'');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID','left');
        $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
        return $this->db->get()->result_array();
    }

    function save_direct_pv_detail()
    {
        $projectExist = project_is_exist();
        $taxType = trim($this->input->post('text_type') ?? '');
        $gl_tax = trim($this->input->post('gl_text_type') ?? '');
        $type = trim($this->input->post('type') ?? '');
        if(!empty($gl_tax)) {
            $taxType = trim($this->input->post('gl_text_type') ?? '');
        }
        $this->db->trans_start();
        $this->db->select('transactionCurrency, transactionExchangeRate, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,transactionCurrencyID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $segment = explode('|', trim($this->input->post('segment_gl') ?? ''));
        $gl_code = explode('|', trim($this->input->post('gl_code_des') ?? ''));
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');  
        }
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        $discountPercentage = $this->input->post('discountPercentage');
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['GLAutoID'] = trim($this->input->post('gl_code') ?? '');
        if ($projectExist == 1) {
            $projectID = trim($this->input->post('projectID') ?? '');
            $projectCurrency = project_currency($projectID);
            $projectCurrencyExchangerate = currency_conversionID($this->input->post('transactionCurrencyID'), $projectCurrency);
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
        if($advanceCostCapturing == 1){
            $data['activityCodeID'] = trim($activityCodeID);
        }
        $data['transactionCurrency'] = $master_recode['transactionCurrency'];
        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $data['discountPercentage'] = trim($discountPercentage);
        $data['discountAmount'] = trim(($this->input->post('amount')*$discountPercentage)/100);
        $data['transactionAmount'] = trim($this->input->post('amount')-$data['discountAmount']);
        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['partyCurrency'] = $master_recode['partyCurrency'];
        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['type'] = ($type) ? $type : 'GL';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID') ?? '')) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);

            $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($this->input->post('payVoucherAutoId') ?? ''),'PV','payVoucherAutoId');
            if($group_based_tax == 1 && !empty($taxType)){
                tax_calculation_vat(null,null,$taxType,'payVoucherAutoId',trim($this->input->post('payVoucherAutoId') ?? ''), trim($this->input->post('amount') ?? ''),'PV',trim($this->input->post('payVoucherDetailAutoID') ?? ''),$data['discountAmount'],1);
            } else if($group_based_tax == 1 && empty($taxType)) {
                
                fetchExistsDetailTBL('PV', trim($this->input->post('payVoucherAutoId') ?? ''),trim($this->input->post('payVoucherDetailAutoID') ?? ''),null,0,$data['transactionAmount']);
                $data['taxCalculationformulaID'] = null;
                $data['taxAmount'] = 0;
                $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
                $this->db->update('srp_erp_paymentvoucherdetail', $data);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
            }
        }
    }

    function save_direct_pv_detail_multiple()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $isGroupByTax = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($this->input->post('payVoucherAutoId') ?? ''),'PV','payVoucherAutoId');

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectID = $this->input->post('projectID');
        $discountPercentage = $this->input->post('discountPercentage');
        $ProjectCategory = $this->input->post('project_categoryID');
        $ProjectSubcategory = $this->input->post('project_subCategoryID');
        $gl_text_type = $this->input->post('gl_text_type');
        $item_text = $this->input->post('item_text');
        $GL_Type = $this->input->post('GL_Type');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');  
        }
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        
        if(!empty($item_text)) {
            $gl_text_type = $item_text;
        }

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $ProjectCategory[$key];
                $data['project_subCategoryID'] = $ProjectSubcategory[$key];
            }
            $gl_code = fetch_gl_account_desc($gl_codes[$key]);
            $data['systemGLCode'] = trim($gl_code['systemAccountCode'] ?? '');
            $data['GLCode'] = trim($gl_code['GLSecondaryCode'] ?? '');
            $data['GLDescription'] = trim($gl_code['GLDescription'] ?? '');
            $data['GLType'] = trim($gl_code['subCategory'] ?? '');
            $data['GLAutoID'] = $gl_codes[$key];
            //            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            //            $data['GLCode'] = trim($gl_code[1] ?? '');
            //            $data['GLDescription'] = trim($gl_code[2] ?? '');
            //            $data['GLType'] = trim($gl_code[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = trim($activityCodeID[$key]);
            }
            $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
            $data['transactionCurrency'] = $master_recode['transactionCurrency'];
            $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
            $data['discountPercentage'] = trim($discountPercentage[$key]);
            $data['discountAmount'] = trim(($amount[$key]*$discountPercentage[$key])/100);
            $data['transactionAmount'] = trim($amount[$key]-$data['discountAmount']);
            $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
            $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
            $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
            $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
            $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
            $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
            $data['partyCurrency'] = $master_recode['partyCurrency'];
            $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
            $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
            $data['description'] = $descriptions[$key];
            $data['type'] = ($GL_Type) ? $GL_Type : 'GL';
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
        
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
        
            if($isGroupByTax == 1){ 
                if(!empty($gl_text_type[$key])){
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$gl_text_type[$key]);
                    $master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();
            
                    $dataTax['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
                    $dataTax['taxFormulaMasterID'] = $gl_text_type[$key];
                    $dataTax['taxDescription'] = $master['Description'];
                    $dataTax['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $dataTax['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $dataTax['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $dataTax['transactionCurrencyDecimalPlaces'] = $master_recode['transactionCurrencyDecimalPlaces'];
                    $dataTax['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $dataTax['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $dataTax['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $dataTax['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $dataTax['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $dataTax['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];

                    tax_calculation_vat('srp_erp_paymentvouchertaxdetails',$dataTax,$gl_text_type[$key],'payVoucherAutoId',trim($this->input->post('payVoucherAutoId') ?? ''),$amount[$key],'PV',$last_id,$data['discountAmount'],1);
                }             
            }        
        }

        // $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Detail :  Saved Successfully.');
        }

    }

    function save_pv_po_detail()
    {
        $this->db->trans_start();
        $po = explode('|', trim($this->input->post('po_code') ?? ''));
        $po_des = explode('|', trim($this->input->post('po_des') ?? ''));
        if (!empty($this->input->post('po_code')) && $this->input->post('po_des') != 'Select PO') {
            $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,supplierCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
            $this->db->where('purchaseOrderID', trim($po[0] ?? ''));
            $master = $this->db->get('srp_erp_purchaseordermaster')->row_array();
            $data['purchaseOrderID'] = trim($po[0] ?? '');
            $data['PODate'] = trim($po[1] ?? '');
            $data['POCode'] = trim($po_des[0] ?? '');
            $data['PODescription'] = trim($po_des[1] ?? '');
            $data['partyCurrencyID'] = $master['supplierCurrencyID'];
            $data['partyCurrency'] = $master['supplierCurrency'];
            $data['partyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        } else {
            $this->db->select('transactionCurrency, transactionExchangeRate, transactionCurrencyID,companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate,partyCurrencyID,companyReportingCurrencyID,segmentID,segmentCode');
            $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
            $master = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
            $data['purchaseOrderID'] = 0;
            $data['PODate'] = null;
            $data['POCode'] = null;
            $data['PODescription'] = trim($this->input->post('description') ?? '');
            $data['partyCurrencyID'] = $master['partyCurrencyID'];
            $data['partyCurrency'] = $master['partyCurrency'];
            $data['partyExchangeRate'] = $master['partyExchangeRate'];
        }
        $data['transactionAmount'] = trim($this->input->post('amount') ?? '');
        $data['segmentID'] = $master['segmentID'];
        $data['segmentCode'] = $master['segmentCode'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
        $data['transactionCurrency'] = $master['transactionCurrency'];
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['partyAmount'] = ($data['transactionAmount'] / $data['partyExchangeRate']);
        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['type'] = 'Advance';
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID') ?? '')) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Detail Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Detail Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('payVoucherDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Payment Voucher Detail  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Payment Voucher Detail Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function payment_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocation = $this->common_data['emplanglocationid'];
        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
        $this->db->from('srp_erp_paymentvoucherdetail');
        $results = $this->db->get()->result_array();
        $PayVoucherAutoId = $this->input->post('PayVoucherAutoId');
        $currentdate = current_date(false);
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $autoApprovalFromPost = $this->input->post('autoApproval');

        $mastertbl = $this->db->query("SELECT PVdate,PVchequeDate FROM `srp_erp_paymentvouchermaster` where companyID = $companyID And payVoucherAutoId = $PayVoucherAutoId ")->row_array();
        $mastertbldetail = $this->db->query("SELECT payVoucherAutoId,type  FROM `srp_erp_paymentvoucherdetail` where companyID = $companyID And type = 'Item' And payVoucherAutoId = $PayVoucherAutoId")->row_array();

        if (empty($results)) {
            return array('error' => '2', 'message' => 'There are no records to confirm this document!');
        } else {
            $pvid = $this->input->post('PayVoucherAutoId');

          /*   $paymentvoucherDetail = $this->db->query("select
                                        GROUP_CONCAT(itemAutoID) as itemAutoID
                                        from 
                                        srp_erp_paymentvoucherdetail
                                        where 
                                        companyID = $companyID 
                                        AND payVoucherAutoId = $pvid")->row("itemAutoID");
            */

                /*        if(!empty($paymentvoucherDetail)){ 
                            $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$paymentvoucherDetail");
                            if(!empty($wacTransactionAmountValidation)){ 
                                return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                                exit();
                            }
                            
                        }
            */
            $taxamnt = 0;
            $GL = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='GL' OR type = 'LS' GROUP BY payVoucherAutoId")->row_array();

            if (empty($GL)) {
                $GL = 0;
            } else {
                $GL = $GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Item' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Item)) {
                $Item = 0;
            } else {
                $Item = $Item['transactionAmount'];
            }
            $debitnote = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND (type='debitnote' OR type = 'SR') GROUP BY payVoucherAutoId")->row_array();
            if (empty($debitnote)) {
                $debitnote = 0;
            } else {
                $debitnote = $debitnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Advance' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Advance)) {
                $Advance = 0;
            } else {
                $Advance = $Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionAmount )), 4 )))))) AS transactionAmount FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvid AND type='Invoice' GROUP BY payVoucherAutoId")->row_array();
            if (empty($Invoice)) {
                $Invoice = 0;
            } else {
                $Invoice = $Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( taxPercentage )), 4 )))))) AS taxPercentage  FROM srp_erp_paymentvouchertaxdetails WHERE payVoucherAutoId = $pvid GROUP BY payVoucherAutoId")->row_array();
            if (empty($tax)) {
                $tax = 0;
            } else {
                $tax = $tax['taxPercentage'];
                $taxamnt = (($Item + $GL) / 100) * $tax;
            }
            $totalamnt = ($Item + $GL + $Invoice + $Advance + $taxamnt) - $debitnote;
           
            if ($totalamnt < 0) {
                return array('error' => '2', 'message' => 'Grand total should be greater than 0.');
            } else {
                $this->db->select('PayVoucherAutoId');
                $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
                $this->db->where('confirmedYN', 1);
                $this->db->from('srp_erp_paymentvouchermaster');
                $Confirmed = $this->db->get()->row_array();
                if (!empty($Confirmed) && $autoApprovalFromPost != 1) {
                    return array('error' => '2', 'message' => 'Document already confirmed');
                } else {
                    $PayVoucherAutoId = trim($this->input->post('PayVoucherAutoId') ?? '');

                    // start approval type check
                    $approvalType = getApprovalTypesONDocumentCode('PV', $companyID);
                   // $documentTotal = $totalamnt;

                    $poLocalAmount = payment_voucher_total_value($PayVoucherAutoId, 2, 0);
                    

                    $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_paymentvouchermaster where payVoucherAutoId = $PayVoucherAutoId AND companyID = {$companyID}")->row_array();

                    if($approvalType['approvalType'] == 2) {
                        $amountApprovable = amount_based_approval_setup('PV', $poLocalAmount);
                        
                        if($amountApprovable['type'] == 'e') {
                            $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PV Value');
                            return array('error' => '2', 'message' => 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PV Value');
                            exit;
                        }
                    }
                    if($approvalType['approvalType'] == 3) {
                        $segment_based_approval = segment_based_approval('PV', $segmentID['segmentID']);

                        if($segment_based_approval['type'] == 'e') {
                            $this->session->set_flashdata('w', 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this PV Value');
                            return array('error' => '2', 'message' => 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this PV Value');
                            exit;
                        }
                    }
                    if($approvalType['approvalType'] == 4) {
                        $amount_base_segment_based_approval = amount_base_segment_based_approval('PV', $poLocalAmount, $segmentID['segmentID']);

                        if($amount_base_segment_based_approval['type'] == 'e') {
                            $this->session->set_flashdata('w', 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this PV Value');
                            return array('error' => '2', 'message' => 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this PV Value');
                            exit;
                        }
                    }
                    
                    //end start approval typs
                    //$subItemNullCount = $this->db->query("SELECT count(srp_erp_itemmaster_subtemp.subItemAutoID) as countAll FROM srp_erp_paymentvoucherdetail LEFT JOIN srp_erp_itemmaster_subtemp ON srp_erp_itemmaster_subtemp.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemmaster_subtemp.itemAutoID WHERE payVoucherAutoId = '" . $PayVoucherAutoId . "'  AND ( srp_erp_paymentvoucherdetail.itemAutoID <> NULL OR srp_erp_paymentvoucherdetail.itemAutoID != ''  ) AND (srp_erp_itemmaster_subtemp.productReferenceNo = NULL OR srp_erp_itemmaster_subtemp.productReferenceNo = '') AND srp_erp_itemmaster.isSubitemExist=1 ")->row_array();
                    $subItemNullCount = $this->db->query("SELECT
                                        count(im.isSubitemExist) AS countAll
                                    FROM
                                        srp_erp_paymentvouchermaster masterTbl
                                    LEFT JOIN srp_erp_paymentvoucherdetail detailTbl ON masterTbl.payVoucherAutoId = detailTbl.payVoucherAutoId
                                    LEFT JOIN srp_erp_itemmaster im ON im.itemAutoID = detailTbl.itemAutoID
                                    LEFT JOIN srp_erp_itemmaster_subtemp itemMaster ON itemMaster.receivedDocumentDetailID = detailTbl.payVoucherDetailAutoID
                                    WHERE
                                        masterTbl.payVoucherAutoId = '" . $PayVoucherAutoId . "'
                                    AND im.isSubitemExist = 1
                                    AND (
                                        ISNULL(itemMaster.productReferenceNo )
                                        OR itemMaster.productReferenceNo = ''
                    )")->row_array();
                    $isProductReference_completed = isMandatory_completed_document($PayVoucherAutoId, 'PV');

                    if ($isProductReference_completed == 0) {
                        $this->db->select('documentID, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID,PVdate');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $master_dt = $this->db->get()->row_array();
                        $this->load->library('sequence');
                        if ($master_dt['PVcode'] == "0") {

                            // master_sequence_code
                            $master_codegeratorpv = $this->input->post('master_sequence_code');

                            if ($locationwisecodegenerate == 1) {
                                $this->db->select('locationID');
                                $this->db->where('EIdNo', $currentuser);
                                $this->db->where('Erp_companyID', $companyID);
                                $this->db->from('srp_employeesdetails');
                                $location = $this->db->get()->row_array();
                                if ((empty($location)) || ($location == '')) {
                                    return array('error' => '2', 'message' => 'Location is not assigned for current employee');
                                } else {
                                    if ($emplocation != '') {
                                        $codegeratorpv = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $emplocation, $master_dt['invYear'], $master_dt['invMonth']);
                                    } else {
                                        return array('error' => '2', 'message' => 'Location is not assigned for current employee');
                                    }
                                }

                            } else {
                                if($master_codegeratorpv){
                                    $codegeratorpv = $master_codegeratorpv;
                                }else{
                                    $codegeratorpv = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                                }  
                            }

                            $validate_code = validate_code_duplication($codegeratorpv, 'PVcode', $PayVoucherAutoId,'PayVoucherAutoId', 'srp_erp_paymentvouchermaster');
                            if(!empty($validate_code)) {
                                return array( 'error' => '1', 'message' => 'The document Code Already Exist.(' . $validate_code . ')' );
                            }
  
                            $pvCd = array(
                                'PVcode' => $codegeratorpv
                            );

                            $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
                            $this->db->update('srp_erp_paymentvouchermaster', $pvCd);
                        } else {
                            $validate_code = validate_code_duplication($master_dt['PVcode'], 'PVcode', $PayVoucherAutoId,'PayVoucherAutoId', 'srp_erp_paymentvouchermaster');
                            if(!empty($validate_code)) {
                                return array( 'error' => '1', 'message' => 'The document Code Already Exist.(' . $validate_code . ')' );
                            }
                        }
                        $this->load->library('approvals');
                        $this->db->select('documentID,PayVoucherAutoId, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID,PVdate,partyID');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $app_data = $this->db->get()->row_array();


                        $autoApproval = get_document_auto_approval('PV');

                       
                        $isWithoutBankingEntry = $this->input->post('isWithoutBankingEntry');
                        $isWithoutGeneralLedger = $this->input->post('isWithoutGeneralLedger');


                        if ($autoApproval == 0 || $autoApprovalFromPost == 1) {
                           
                            if($mastertbldetail['type'] != 'Item') {
                                if ($PostDatedChequeManagement == 1 && ($mastertbl['PVchequeDate'] != '' || !empty($mastertbl['PVchequeDate'])) && (empty($mastertbldetail['payVoucherAutoId']) || $mastertbldetail['payVoucherAutoId']==' ')) {
                                    
                                    
                                    if ($mastertbl['PVchequeDate'] > $mastertbl['PVdate']) {
                                        if ($currentdate >= $mastertbl['PVchequeDate']) {

                                            $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                        } else {
                                            return array('error' => '1', 'message' => 'This is a post dated cheque document. you cannot approve this document before the cheque date!');

                                        }
                                    } else {

                                        $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                    }

                                     //approve salary provision
                                     $prov_response = $this->add_pv_to_salary_provision($app_data);
                                   

                                }else
                                {

                                    //approve salary provision
                                    $prov_response = $this->add_pv_to_salary_provision($app_data);

                                    $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                                }
                            }
                            else {

                                $approvals_status = $this->approvals->auto_approve($app_data['PayVoucherAutoId'], 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 'PV', $app_data['PVcode'], $app_data['PVdate']);
                            }


                        } elseif ($autoApproval == 1) {
                            $approvals_status = $this->approvals->CreateApproval('PV', $app_data['PayVoucherAutoId'], $app_data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId', 0, $app_data['PVdate'], $segmentID['segmentID'],$poLocalAmount);
                        } else {
                            return array('error' => '2', 'message' => 'Approval levels are not set for this document');
                            exit;
                        }

                    

                        if ($approvals_status == 1) {
                            $autoApproval = get_document_auto_approval('PV');

                            $updatedBatchNumberArray=[];

                            if($itemBatchPolicy==1){

                                $this->db->select('*');
                                $this->db->where('payVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
                                $this->db->from('srp_erp_paymentvoucherdetail');
                                $invoice_results = $this->db->get()->result_array();

                                $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);

                            }

                            if ($autoApproval == 0 || $autoApprovalFromPost == 1) {
                                $result = $this->save_pv_approval(0, $app_data['PayVoucherAutoId'], 1, 'Auto Approved',$updatedBatchNumberArray,$isWithoutBankingEntry,$isWithoutGeneralLedger);
                                if ($result) {

                                    $this->db->trans_commit();
                                    return array('error' => '0', 'message' => 'Document confirmed successfully.', 'code' => $app_data['PVcode']);
                                }
                            } else {
                                $data = array(
                                    'confirmedYN' => 1,
                                    'confirmedDate' => $this->common_data['current_date'],
                                    'confirmedByEmpID' => $this->common_data['current_userID'],
                                    'confirmedByName' => $this->common_data['current_user']
                                );
                                $this->db->where('PayVoucherAutoId', trim($this->input->post('PayVoucherAutoId') ?? ''));
                                $this->db->update('srp_erp_paymentvouchermaster', $data);
                                
                                return array('error' => '0', 'message' => 'Document confirmed successfully.', 'code' => $app_data['PVcode']);
                            }


                        } else if ($approvals_status == 3) {
                            return array('error' => '2', 'message' => 'There are no users exist to perform approval for this document.');
                        } else {
                            return array('error' => '1', 'message' => 'oops, something went wrong!');
                        }
                    } else {
                        return array('error' => '1', 'message' => 'Please complete you sub item configuration, fill all the mandatory fields!');
                    }
                }
            }


        }
    }

    function add_pv_to_salary_provision($pvMasterData){

        if($pvMasterData){

            if(isset($pvMasterData['PayVoucherAutoId'])){
                $pvMasterID = $pvMasterData['PayVoucherAutoId'];
            }

            if(isset($pvMasterData['payVoucherAutoId'])){
                $pvMasterID = $pvMasterData['payVoucherAutoId'];
            }
            
            $empID = $pvMasterData['partyID'];

            $detail_record = $this->db->query("SELECT * FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId = $pvMasterID AND type = 'LS'")->result_array();

            foreach($detail_record as $payments){

                $data = array();

                $data['empID'] = $empID;
                $data['amount'] = '-'.$payments['transactionAmount'];
                $data['provisionDocID'] = $payments['payVoucherDetailAutoID'];
                $data['provisionDocType'] = 'PV';
                $data['localCurrency'] = $payments['transactionCurrency'];
                $data['localCurrencyID'] = $payments['transactionCurrencyID'];
                $data['companyID'] = current_companyID();

                $res = $this->db->insert('srp_erp_jv_provision_detail', $data);

            }

            return TRUE;

        }

    }


    function payment_match_confirmation()
    {
        $this->db->select('matchID');
        $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
        $this->db->from('srp_erp_pvadvancematchdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('matchID');
            $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_pvadvancematch');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $this->db->select('matchSystemCode');
                $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
                $this->db->from('srp_erp_pvadvancematch');
                $mas_dt = $this->db->get()->row_array();
                $validate_code = validate_code_duplication($mas_dt['matchSystemCode'], 'matchSystemCode', trim($this->input->post('matchID') ?? ''),'matchID', 'srp_erp_pvadvancematch');
                if(!empty($validate_code)) {
                    return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                }

                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                    'confirmedByName' => $this->common_data['current_user']
                );
                $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
                $this->db->update('srp_erp_pvadvancematch', $data);
                return array('s', 'Document confirmed Successfully');
            }
        }
    }

    function fetch_pv_advance_detail()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $matchid = $this->input->post('matchID');
        $this->db->select('supplierID,transactionCurrency,DATE_FORMAT(matchDate,"%Y-%m-%d") AS matchDate');
        $this->db->where('matchID', $matchid);
        $master_arr = $this->db->get('srp_erp_pvadvancematch')->row_array();
        $matchDate = $this->db->query("SELECT matchDate from srp_erp_pvadvancematch WHERE matchID = $matchid")->row('matchDate');

        $this->db->select('purchaseOrderID,POCode,PODescription,srp_erp_paymentvoucherdetail.transactionAmount ,PODate ,DATE_FORMAT(srp_erp_paymentvouchermaster.PVdate,\'' . $convertFormat . '\') AS PVdate ,srp_erp_paymentvouchermaster.PVcode,IFNULL(sum(srp_erp_pvadvancematchdetails.transactionAmount),0) AS paid,,srp_erp_paymentvoucherdetail.payVoucherDetailAutoID');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('partyID', $master_arr['supplierID']);
        $this->db->where('srp_erp_paymentvoucherdetail.transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('srp_erp_paymentvoucherdetail.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('type', 'Advance');
        $this->db->group_by("payVoucherDetailAutoID");
        $this->db->where('srp_erp_paymentvouchermaster.approvedYN', 1);
        $this->db->where('srp_erp_paymentvouchermaster.PVDate <=', $matchDate);
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId');
        $this->db->join('srp_erp_pvadvancematchdetails', 'srp_erp_pvadvancematchdetails.payVoucherDetailAutoID = srp_erp_paymentvoucherdetail.payVoucherDetailAutoID', 'Left');
        $data['payment'] = $this->db->get()->result_array();

        /*$this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,transactionAmount,paymentTotalAmount ,DebitNoteTotalAmount,advanceMatchedTotal');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('paymentInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->where('supplierID', $master_arr['supplierID']);
        $this->db->where('transactionCurrency', $master_arr['transactionCurrency']);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['invoice'] = $this->db->get()->result_array();*/
        $supplierID=$master_arr['supplierID'];
        $transactionCurrency=$master_arr['transactionCurrency'];
        $companyID=$this->common_data['company_data']['company_id'];
        //$data['invoice'] = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID, `bookingInvCode`, `bookingDate`, `transactionAmount`, `paymentTotalAmount`, `DebitNoteTotalAmount`, `advanceMatchedTotal` FROM `srp_erp_paysupplierinvoicemaster` LEFT JOIN ( SELECT IFNULL( ( ( ( sid.transactionAmount * ( 100 + IFNULL(tax.taxPercentage, 0) ) ) / 100 ) - (paymentTotalAmount + DebitNoteTotalAmount + advanceMatchedTotal) ), 0 ) AS amount, srp_erp_paysupplierinvoicemaster.invoiceAutoID FROM srp_erp_paysupplierinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID ) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID ) tot ON tot.invoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID WHERE `paymentInvoiceYN` = 0 AND `approvedYN` = 1 AND `supplierID` = $supplierID AND `transactionCurrency` = '$transactionCurrency' AND `companyID` = $companyID AND tot.amount > 0")->result_array();
        $data['invoice'] = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID, `bookingInvCode`, `bookingDate`, `transactionAmount`, `paymentTotalAmount`, `DebitNoteTotalAmount`, `advanceMatchedTotal` FROM `srp_erp_paysupplierinvoicemaster` LEFT JOIN ( SELECT IFNULL( ( ( ( ( ( IFNULL(tax.taxPercentage, 0) / 100 ) * ( IFNULL(sid.transactionAmount, 0) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL(sid.transactionAmount, 0) ) ) ) + IFNULL(sid.transactionAmount, 0) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL(sid.transactionAmount, 0) ) )  - (paymentTotalAmount + DebitNoteTotalAmount + advanceMatchedTotal) ), 0 ) AS amount, srp_erp_paysupplierinvoicemaster.invoiceAutoID FROM srp_erp_paysupplierinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID ) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID ) tot ON tot.invoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID WHERE `paymentInvoiceYN` = 0 AND `approvedYN` = 1 AND bookingDate <= '$matchDate' AND `supplierID` = $supplierID AND `transactionCurrency` = '$transactionCurrency' AND `companyID` = $companyID AND tot.amount > 0")->result_array();

        return $data;
    }

    function save_match_amount()
    {
        $this->db->trans_start();
        $payVoucherDetailAutoID = $this->input->post('payVoucherDetailAutoID');
        $invoice_id = $this->input->post('InvoiceAutoID');
        $amounts = $this->input->post('amounts');
        $matchID = $this->input->post('matchID');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        $this->db->where('matchID', $matchID);
        $master = $this->db->get('srp_erp_pvadvancematch')->row_array();

        $this->db->select('srp_erp_paymentvouchermaster.payVoucherAutoId,srp_erp_paymentvoucherdetail.transactionAmount,srp_erp_paymentvouchermaster.PVdate,srp_erp_paymentvouchermaster.PVcode,srp_erp_paymentvoucherdetail.payVoucherDetailAutoID');
        $this->db->group_by("payVoucherDetailAutoID");
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId');
        $this->db->where_in('payVoucherDetailAutoID', $payVoucherDetailAutoID);
        $detail_arr = $this->db->get()->result_array();

        for ($i = 0; $i < count($detail_arr); $i++) {
            $invoice_data = $this->fetch_invoice($invoice_id[$i]);
            $data[$i]['matchID'] = $matchID;
            $data[$i]['payVoucherAutoId'] = $detail_arr[$i]['payVoucherAutoId'];
            $data[$i]['payVoucherDetailAutoID'] = $detail_arr[$i]['payVoucherDetailAutoID'];
            $data[$i]['pvCode'] = $detail_arr[$i]['PVcode'];
            $data[$i]['PVdate'] = $detail_arr[$i]['PVdate'];
            $data[$i]['InvoiceAutoID'] = trim($invoice_data['InvoiceAutoID'] ?? '');
            $data[$i]['bookingInvCode'] = trim($invoice_data['bookingInvCode'] ?? '');
            $data[$i]['bookingDate'] = trim($invoice_data['bookingDate'] ?? '');
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['transactionExchangeRate'] = 1;
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['supplierAmount'] = ($data[$i]['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
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

            $id = $data[$i]['InvoiceAutoID'];
            $amo = $data[$i]['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET advanceMatchedTotal = (advanceMatchedTotal+{$amo}) WHERE InvoiceAutoID='{$id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_pvadvancematchdetails', $data);
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
        $this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,transactionAmount,paymentTotalAmount ,DebitNoteTotalAmount,advanceMatchedTotal');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', $id);
        return $this->db->get()->row_array();
    }

    function process_sub_invoice_list($invoiceList,$mastercode=null){

        $invoices = explode(',',$invoiceList);
        $num = 1;

        foreach($invoices as $payVoucherAutoId){
         
            $_POST['PayVoucherAutoId'] =  $payVoucherAutoId;
            $_POST['autoApproval'] =  1;
            $_POST['isWithoutBankingEntry'] =  1;
            $_POST['master_sequence_code'] = $mastercode.'/'.$num;
            $res = $this->Payment_voucher_model->payment_confirmation();

            $num++;
            $_POST['PayVoucherAutoId'] = '';
        }
    }

    function save_pv_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0,$updatedBatchNumberArray=[],$isWithoutBankLedger = null,$isWithoutGeneralLedger = null)
    {
      
        $batchNumberPolicy = getPolicyValues('IB', 'All');
        $this->db->trans_start();
        $this->load->library('approvals');
        $companyID = current_companyID();
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('payVoucherAutoId') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['payVoucherAutoId'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }


        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PV');
        }

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvouchermaster');
            $master = $this->db->get()->row_array();


            //Approve provision
            //approve salary provision
            if($master){
           
                $prov_response = $this->add_pv_to_salary_provision($master);
            }

            if(isset($master['bypassLedger']) && $master['bypassLedger'] == 1){
                $isWithoutGeneralLedger = 1;
            }

            //approve sub document lists
            if(isset($master['subInvoiceList']) && $master['subInvoiceList']){
                $res = $this->process_sub_invoice_list($master['subInvoiceList'],$master['PVcode']);
            }
           

            $this->db->select('*,0 as taxLedgerAmount');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvoucherdetail');
            $payment_detail = $this->db->get()->result_array();

            $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',$system_code,'PV','payVoucherAutoId');

            if($isGroupByTax == 1){
                $payment_detail = $this->db->query("SELECT
                                                        srp_erp_paymentvoucherdetail.*,
	                                                    IFNULL( taxAmount, 0 ) AS taxAmount,
	                                                    IFNULL( pviitemtaxamount.taxLedgerAmount, 0 ) AS taxLedgerAmount 
                                                        FROM
	                                                    `srp_erp_paymentvoucherdetail`
	                                                    LEFT JOIN (
	                                                    SELECT sum( amount ) AS taxLedgerAmount,
		                                                srp_erp_paymentvoucherdetail.itemAutoID 
	                                                    FROM
		                                                srp_erp_taxledger
		                                                LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
		                                                LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
		                                                LEFT JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
	                                                    WHERE
		                                                srp_erp_taxledger.documentID = 'PV' 
		                                                AND ( srp_erp_taxledger.isClaimable = 0 ) 
		                                                AND documentMasterAutoID = $system_code  
	                                                    GROUP BY
		                                                srp_erp_paymentvoucherdetail.itemAutoID) pviitemtaxamount ON pviitemtaxamount.ItemAutoID = srp_erp_paymentvoucherdetail.itemAutoID 
                                                        WHERE
	                                                    `payVoucherAutoId` = $system_code 
	                                                    AND `companyID` =  $companyID")->result_array();
            }



            for ($a = 0; $a < count($payment_detail); $a++) {
                if ($payment_detail[$a]['type'] == 'Item' || $payment_detail[$a]['type'] == 'PRQ') {
                    $item = fetch_item_data($payment_detail[$a]['itemAutoID']);

                    $this->db->select('GLAutoID');
                    $this->db->where('controlAccountType', 'ACA');
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                    $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                    $company_loc = ($payment_detail[$a]['transactionAmount'] / $master['companyLocalExchangeRate']);
                    if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] =='Service') {
                        $itemAutoID = $payment_detail[$a]['itemAutoID'];
                        $qty = ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']);
                        $wareHouseAutoID = $payment_detail[$a]['wareHouseAutoID'];
                        $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                        $item_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                       /*  $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                        $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $company_loc) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                        $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + ($payment_detail[$a]['transactionAmount'] / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']); */
                        $itemledgerCurrentStock = fetch_itemledger_currentstock($payment_detail[$a]['itemAutoID']);
                        $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($payment_detail[$a]['itemAutoID'], 'companyLocalExchangeRate');
                        $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($payment_detail[$a]['itemAutoID'],'companyReportingExchangeRate');
                      
                      
                        
                        $item_arr[$a]['currentStock'] = ($itemledgerCurrentStock + $qty);
                        $item_arr[$a]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + ($company_loc+ $payment_detail[$a]['taxLedgerAmount'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                        $item_arr[$a]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + (($payment_detail[$a]['transactionAmount'] + $payment_detail[$a]['taxLedgerAmount']) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                        
                        if (!empty($item_arr)) {
                            $this->db->where('itemAutoID', trim($payment_detail[$a]['itemAutoID']));
                            $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                        }
                        $itemledger_arr[$a]['documentID'] = $master['documentID'];
                        $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                        $itemledger_arr[$a]['documentAutoID'] = $master['payVoucherAutoId'];
                        $itemledger_arr[$a]['documentSystemCode'] = $master['PVcode'];
                        $itemledger_arr[$a]['documentDate'] = $master['PVdate'];
                        $itemledger_arr[$a]['referenceNumber'] = $master['referenceNo'];
                        $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                        $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                        $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                        $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                        $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                        $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                        $itemledger_arr[$a]['wareHouseAutoID'] = $payment_detail[$a]['wareHouseAutoID'];
                        $itemledger_arr[$a]['wareHouseCode'] = $payment_detail[$a]['wareHouseCode'];
                        $itemledger_arr[$a]['wareHouseLocation'] = $payment_detail[$a]['wareHouseLocation'];
                        $itemledger_arr[$a]['wareHouseDescription'] = $payment_detail[$a]['wareHouseDescription'];
                        $itemledger_arr[$a]['itemAutoID'] = $payment_detail[$a]['itemAutoID'];
                        $itemledger_arr[$a]['itemSystemCode'] = $payment_detail[$a]['itemSystemCode'];
                        $itemledger_arr[$a]['itemDescription'] = $payment_detail[$a]['itemDescription'];
                        $itemledger_arr[$a]['SUOMID'] = $payment_detail[$a]['SUOMID'];
                        $itemledger_arr[$a]['SUOMQty'] = $payment_detail[$a]['SUOMQty'];
                        $itemledger_arr[$a]['defaultUOMID'] = $payment_detail[$a]['defaultUOMID'];
                        $itemledger_arr[$a]['defaultUOM'] = $payment_detail[$a]['defaultUOM'];
                        $itemledger_arr[$a]['transactionUOM'] = $payment_detail[$a]['unitOfMeasure'];
                        $itemledger_arr[$a]['transactionUOMID'] = $payment_detail[$a]['unitOfMeasureID'];
                        $itemledger_arr[$a]['transactionQTY'] = $payment_detail[$a]['requestedQty'];
                        $itemledger_arr[$a]['convertionRate'] = $payment_detail[$a]['conversionRateUOM'];
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
                        $itemledger_arr[$a]['transactionAmount'] = ($payment_detail[$a]['transactionAmount']+$payment_detail[$a]['taxLedgerAmount']) ;
                        $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                        $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                        $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                        $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                        $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                        $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                        $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyLocalWacAmount'] = $item_arr[$a]['companyLocalWacAmount'];
                        $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                        $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                        $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                        $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                        $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                        $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                        $itemledger_arr[$a]['partyCurrencyID'] = $master['partyCurrencyID'];
                        $itemledger_arr[$a]['partyCurrency'] = $master['partyCurrency'];
                        $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['partyExchangeRate'];
                        $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['partyCurrencyDecimalPlaces'];
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
                        $assat_amount = ($payment_detail[$a]['transactionAmount'] / ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']));
                        for ($b = 0; $b < ($payment_detail[$a]['requestedQty'] / $payment_detail[$a]['conversionRateUOM']); $b++) {
                            $assat_data[$b]['documentID'] = 'FA';
                            $assat_data[$b]['docOriginSystemCode'] = $master['payVoucherAutoId'];
                            $assat_data[$b]['docOriginDetailID'] = $payment_detail[$a]['payVoucherDetailAutoID'];
                            $assat_data[$b]['docOrigin'] = 'PV';
                            $assat_data[$b]['dateAQ'] = $master['PVdate'];
                            $assat_data[$b]['grvAutoID'] = $master['payVoucherAutoId'];
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
                            $assat_data[$b]['companyLocalAmount'] = round($assat_amount/$master['companyLocalExchangeRate'], $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $assat_data[$b]['companyReportingAmount'] = round($assat_amount/$master['companyReportingExchangeRate'], $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                            $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $assat_data[$b]['supplierID'] = $master['partyID'];
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
                } elseif ($payment_detail[$a]['type'] == 'Advance') {
                    $this->load->library('sequence');
                    $advance_data = array();
                }

            }


            if (!empty($itemledger_arr)) {
                $itemledger_arr = array_values($itemledger_arr);

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
            $generalledger_arr = array();
            $double_entry = $this->Double_entry_model->fetch_double_entry_payment_voucher_data($system_code, 'PV');



            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentType'] = $double_entry['master_data']['pvType'];
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['PVdate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                $generalledger_arr[$i]['documentNarration'] = isset($double_entry['gl_detail'][$i]['gl_remarks']) ? $double_entry['gl_detail'][$i]['gl_remarks'] : $double_entry['master_data']['PVNarration'];;
                //$generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['gl_remarks'];
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
                if(isset($generalledger_arr[$i]['partyExchangeRate']) && $generalledger_arr[$i]['partyExchangeRate'] > 0){
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                }else{
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / 1), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
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
            $bankledger_arr['bankCurrencyAmount'] = ($bankledger_arr['transactionAmount'] / $bankledger_arr['bankCurrencyExchangeRate']);
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

            if($isWithoutBankLedger != 1){
                $this->db->insert('srp_erp_bankledger', $bankledger_arr);
            }
            
            if (!empty($generalledger_arr)) {
                if($isWithoutGeneralLedger != 1){
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }
                
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'PV');
                $this->db->where('documentMasterAutoID', $system_code);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['payVoucherAutoId'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['PVcode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentType'] = $double_entry['master_data']['pvType'];
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['PVdate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['PVdate']));
                    $generalledger_arr['documentNarration'] = isset($double_entry['gl_detail'][$i]['gl_remarks']) ? $double_entry['gl_detail'][$i]['gl_remarks'] : $double_entry['master_data']['PVNarration'];
                    //$generalledger_arr['documentNarration'] = $double_entry['gl_detail'][$i]['gl_remarks'];
                    $generalledger_arr['chequeNumber'] = $double_entry['master_data']['PVchequeNo'];
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
                    $generalledger_arr['partyType'] = $double_entry['master_data']['partyType'];
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['partyID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['partyCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['partyName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['partyCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['partyCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['partyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['partyCurrencyDecimalPlaces'];
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

                    if($isWithoutGeneralLedger != 1){
                        $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                    }
                    
                }
            }

            $this->session->set_flashdata('s', 'Payment Voucher Approval Successfully.');


            $maxLevel = $this->approvals->maxlevel('PV');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $this->input->post('payVoucherAutoId');
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
            foreach ($payment_detail as $value) {
                if ($value['itemAutoID']) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                if ($value['wareHouseAutoID']) {
                    array_push($wareHouseAutoIDDarry, $value['wareHouseAutoID']);
                }

            }
            if ($itemAutoIDarry && $wareHouseAutoIDDarry) {
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $wareHouseAutoIDDarry) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['PVdate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['payVoucherAutoId'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['PVcode'];
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

                foreach ($payment_detail as $itemid) {
                    if ($itemid['type'] == 'Item') {
                        $receivedQty = $itemid['requestedQty'];
                        $receivedQtyConverted = ($itemid['requestedQty'] / $itemid['conversionRateUOM']);
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
                                    $balanceQtyConverted = ($exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM']);
                                    $updatedQtyConverted = ($exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM']);
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;

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
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {

            $this->db->select('*');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvouchermaster');
            $master_pv_srm = $this->db->get()->row_array();

            $this->db->select('*,0 as taxLedgerAmount');
            $this->db->where('payVoucherAutoId', $system_code);
            $this->db->from('srp_erp_paymentvoucherdetail');
            $payment_detail_pv_srm = $this->db->get()->result_array();

            //if($master_pv_srm)
            $this->send_pv_srm_supplier($master_pv_srm,$payment_detail_pv_srm);


            $this->db->trans_commit();
            return true;

        }
    }

    function send_pv_srm_supplier($master,$sub){

        if($master){

            $this->db->select('*');
            $this->db->where('erpSupplierAutoID', $master['partyID']);
            $this->db->from('srp_erp_srm_suppliermaster');
            $srm_supplier = $this->db->get()->row_array();

            if($srm_supplier){

                $token = getLoginToken();
                            
                $token_array=json_decode($token);

                if($token_array){

                    if($token_array->success==true){

                       
                    
                        $res= srmCommonApiCall($master,$sub,$token_array->data->token,'/Api_ecommerce/save_supplier_payment_voucher');
                        //print_r($res);exit;

                        $res_array=json_decode($res);

                        if($res_array->status==true){
                       

                        }else{
                         
                        }
                    }
                }
            }

        }

    }

    function fetch_payment_voucher_detail()
    {
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.seconeryItemCode, srp_erp_activity_code_main.activity_code as activityCodeName, srp_erp_paymentvoucherdetail.activityCodeID as activityCodeID');
        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paymentvoucherdetail.activityCodeID', 'left');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID','Left');

        return $this->db->get()->row_array();
    }


    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_paymentvouchertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_paymentvouchertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $this->db->from('srp_erp_taxmaster');
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $inv_master = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
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
            $this->db->update('srp_erp_paymentvouchertaxdetails', $data);
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
            $this->db->insert('srp_erp_paymentvouchertaxdetails', $data);
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

    function fetch_sales_person($salesPersonID, $currency, $payVoucherAutoId)
    {
        $data = $this->db->query("SELECT srp_erp_salescommisionmaster.transactionCurrencyDecimalPlaces,sum(netCommision) as netCommision,IFNULL(sum(srp_erp_paymentvoucherdetail.transactionAmount),0) as transactionAmount,srp_erp_salescommisionmaster.salesCommisionCode,srp_erp_salescommisionmaster.referenceNo,(sum(netCommision)-IFNULL(sum(transactionAmount),0)) as balance,srp_erp_salescommisionmaster.salesCommisionID FROM srp_erp_salespersonmaster 
INNER JOIN srp_erp_salescommisionperson ON srp_erp_salespersonmaster.salesPersonID = srp_erp_salescommisionperson.salesPersonID 
INNER JOIN srp_erp_salescommisionmaster ON srp_erp_salescommisionmaster.salesCommisionID = srp_erp_salescommisionperson.salesCommisionID AND srp_erp_salescommisionmaster.approvedYN=1
LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,salesPersonID FROM srp_erp_paymentvoucherdetail GROUP BY srp_erp_paymentvoucherdetail.salesPersonID) as srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.salesPersonID = srp_erp_salespersonmaster.salesPersonID
WHERE srp_erp_salespersonmaster.salesPersonID = {$salesPersonID} AND srp_erp_salescommisionmaster.transactionCurrencyID = $currency  GROUP BY srp_erp_salescommisionmaster.salesCommisionID HAVING balance > 0")->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code, $itemCode, $data, $warehouseID)
    {


        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $pv_detailID = isset($data['pv_detailID']) && !empty($data['pv_detailID']) ? $data['pv_detailID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/PV/' . $pv_detailID . '/' . $i;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseID;
                $data_subItemMaster[$x]['uom'] = $uom;
                $data_subItemMaster[$x]['uomID'] = $uomID;
                $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data_subItemMaster);
        }
    }

    function edit_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code = 'PV', $itemCode = null, $data = array())
    {
        $this->db->select('isSubitemExist');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $r = $this->db->get()->row_array();
        $isSubitemExist = $r['isSubitemExist'];

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $payVoucherDetailAutoID = isset($data['payVoucherDetailAutoID']) && !empty($data['payVoucherDetailAutoID']) ? $data['payVoucherDetailAutoID'] : null;
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');

        $result = $this->getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID);
        //echo $this->db->last_query();

        /** delete existing set */
        $this->delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID, 'PV');

        if ($isSubitemExist == 1) {
            $count_subItemMaster = 0;
            if (!empty($result)) {
                $count_subItemMaster = count($result);
            }
            if ($count_subItemMaster != $qty || true) {


                /** Add new set */

                $data_subItemMaster = array();
                if ($qty > 0) {
                    $x = 0;
                    for ($i = 1; $i <= $qty; $i++) {
                        $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                        $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                        $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/PV/' . $payVoucherDetailAutoID . '/' . $i;
                        $data_subItemMaster[$x]['uom'] = $uom;
                        $data_subItemMaster[$x]['uomID'] = $uomID;
                        $data_subItemMaster[$x]['wareHouseAutoID'] = $wareHouseAutoID;
                        $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                        $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                        $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                        $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                        $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                        $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                        $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                        $x++;
                    }
                }


                if (!empty($data_subItemMaster)) {
                    /** bulk insert to item master sub */
                    $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);

                }
            } else if ($count_subItemMaster == 0) {
                $data_subItemMaster = array();
                if ($qty > 0) {
                    $x = 0;
                    for ($i = 1; $i <= $qty; $i++) {
                        $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                        $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                        $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/' . $i;
                        $data_subItemMaster[$x]['uom'] = $uom;
                        $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                        $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                        $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                        $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                        $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                        $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                        $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                        $x++;
                    }
                }


                if (!empty($data_subItemMaster)) {
                    /** bulk insert to item master sub */
                    $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);
                }
            }
        }


    }

    function getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID)
    {

        $this->db->select('*');
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $this->db->from('srp_erp_itemmaster_subtemp');
        $r = $this->db->get()->result_array();
        return $r;
    }

    function delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID, $documentID)
    {
        $this->db->where('receivedDocumentID', $documentID);
        //$this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('receivedDocumentAutoID', $masterID);
        $this->db->where('receivedDocumentDetailID', $detailID);
        $result = $this->db->delete('srp_erp_itemmaster_subtemp');
        return $result;


    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }

    function save_commission_base_items()
    {
        $this->db->trans_start();
        $InvoiceAutoID = array_values(array_diff($this->input->post('InvoiceAutoID'), array("null", "")));
        $exist = $this->db->query("SELECT * FROM srp_erp_paymentvoucherdetail WHERE payVoucherAutoId=" . $this->input->post('payVoucherAutoId') . " AND InvoiceAutoID IN(" . join(',', $InvoiceAutoID) . ")")->result_array();
        if (empty($exist)) {
            $amount = array_values(array_diff($this->input->post('amount'), array("null", "")));
            $due_amount = array_values(array_diff($this->input->post('due_amount'), array("null", "")));
            $this->db->select('srp_erp_salescommisionmaster.*,srp_erp_salespersonmaster.*,srp_erp_salescommisionperson.netCommision,srp_erp_salescommisionperson.salesPersonCurrencyExchangeRate');
            $this->db->from('srp_erp_salescommisionmaster');
            $this->db->join('srp_erp_salescommisionperson', 'srp_erp_salescommisionperson.salesCommisionID = srp_erp_salescommisionmaster.salesCommisionID', 'inner');
            $this->db->join('srp_erp_salespersonmaster', 'srp_erp_salescommisionperson.salesPersonID = srp_erp_salespersonmaster.salesPersonID', 'inner');
            $this->db->where_in('srp_erp_salescommisionmaster.salesCommisionID', $InvoiceAutoID);
            $this->db->where('srp_erp_salescommisionperson.salesPersonID', $this->input->post('salesPersonID'));
            $master_recode = $this->db->get()->result_array();

            for ($i = 0; $i < count($master_recode); $i++) {
                $data[$i]['payVoucherAutoId'] = $this->input->post('payVoucherAutoId');
                $data[$i]['salesCommissionID'] = $master_recode[$i]['salesCommisionID'];
                $data[$i]['salesPersonID'] = $master_recode[$i]['salesPersonID'];
                $data[$i]['type'] = 'SC';
                $data[$i]['bookingInvCode'] = $master_recode[$i]['salesCommisionCode'];
                $data[$i]['referenceNo'] = $master_recode[$i]['referenceNo'];
                $data[$i]['bookingDate'] = $master_recode[$i]['asOfDate'];
                $data[$i]['GLAutoID'] = $master_recode[$i]['receivableAutoID'];
                $data[$i]['systemGLCode'] = $master_recode[$i]['receivableSystemGLCode'];
                $data[$i]['GLCode'] = $master_recode[$i]['receivableGLAccount'];
                $data[$i]['GLDescription'] = $master_recode[$i]['receivableDescription'];
                $data[$i]['GLType'] = $master_recode[$i]['receivableType'];
                $data[$i]['description'] = null;
                $data[$i]['Invoice_amount'] = $master_recode[$i]['netCommision'];
                $data[$i]['due_amount'] = $due_amount[$i];
                $data[$i]['balance_amount'] = ($due_amount[$i] - (float)$amount[$i]);
                $data[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
                $data[$i]['transactionAmount'] = (float)$amount[$i];
                $data[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                $data[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                $data[$i]['partyCurrencyID'] = $master_recode[$i]['salesPersonCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode[$i]['salesPersonCurrency'];
                $data[$i]['partyExchangeRate'] = $master_recode[$i]['salesPersonCurrencyExchangeRate'];
                $data[$i]['partyAmount'] = ($data[$i]['transactionAmount'] / $master_recode[$i]['salesPersonCurrencyExchangeRate']);
                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['modifiedPCID'] = null;
                $data[$i]['modifiedUserID'] = null;
                $data[$i]['modifiedUserName'] = null;
                $data[$i]['modifiedDateTime'] = null;
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];
            }

            if (!empty($data)) {
                $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('status' => false, 'message' => 'Sales Commission : Details Save Failed ' . $this->db->_error_message(), 'type' => 'e');
                } else {
                    $this->db->trans_commit();
                    return array('status' => true, 'message' => 'Sales Commission : ' . count($master_recode) . ' Item Details Saved Successfully.', 'type' => 's');
                }
            } else {
                return array('status' => false);
            }
        } else {
            return array('status' => false, 'message' => 'Sales Commission : Item detail already pulled to this document', 'type' => 'e');
        }
    }

    function re_open_commisionpayment()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
        $this->db->update('srp_erp_paymentvouchermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_payment_voucher()
    {
        $master_id = trim($this->input->post('payVoucherAutoId') ?? '');
        $is_system_generated = $this->db->get_where('srp_erp_paymentvouchermaster', ['payVoucherAutoId'=>$master_id])->row('isSytemGenerated');
        if($is_system_generated == 1){
            $this->session->set_flashdata('e', 'This is System Generated Document,You Cannot Reopen this document');
            return false;
        }

        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('payVoucherAutoId', $master_id);
        $this->db->update('srp_erp_paymentvouchermaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_payment_match()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('matchID', trim($this->input->post('matchID') ?? ''));
        $this->db->update('srp_erp_pvadvancematch', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_payment_voucher_cheque_data($payVoucherAutoId)
    {

        $this->db->select('srp_erp_paymentvouchermaster.pvType as pvType');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $type = $this->db->get()->row_array();

        if ($type['pvType'] == 'Direct' || $type['pvType'] == 'DirectItem' || $type['pvType'] == 'DirectExpense' || $type['pvType'] == 'Employee' || $type['pvType'] == 'Employee' || $type['pvType'] == 'EmployeeExpense' || $type['pvType'] == 'EmployeeItem') {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,PVcode,PVNarration,transactionCurrencyDecimalPlaces,PVdate');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        } else {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_suppliermaster.nameOnCheque,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,PVcode,PVNarration,transactionCurrencyDecimalPlaces,PVdate');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        }


        $this->db->select('transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('sum(taxPercentage) as taxPercentage');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax'] = $this->db->get()->row_array();

        /*$this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoices'] = $this->db->get()->result_array();*/

        $data['invoices'] = $this->db->query("SELECT payVoucherDetailAutoID, payVoucherAutoId, inv_mas.invoiceDate,
	                            inv_det.bookingInvCode AS invoiceCode, supplierInvoiceNo, inv_det.transactionCurrencyDecimalPlaces, 
	                            SUM(inv_det.transactionAmount  + IFNULL(inv_det.taxAmount, 0)) AS transactionAmount, inv_det.comment, inv_mas.comments AS inv_com
	                            FROM srp_erp_paymentvoucherdetail AS inv_det
	                            JOIN srp_erp_paysupplierinvoicemaster AS inv_mas ON inv_det.InvoiceAutoID = inv_mas.InvoiceAutoID
	                            WHERE payVoucherAutoId = $payVoucherAutoId AND `type` = 'Invoice'
	                            GROUP BY inv_det.InvoiceAutoID")->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount,transactionCurrencyDecimalPlaces,GLCode,comment');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['GLs'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,comment as description,transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount,transactionCurrencyDecimalPlaces,itemSystemCode,comment');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['Items'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount,transactionCurrencyDecimalPlaces,comment');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['Advances'] = $this->db->get()->result_array();

        $this->db->select('payVoucherDetailAutoID,payVoucherAutoId,description,transactionAmount + IFNULL(taxAmount, 0) AS transactionAmount,transactionCurrencyDecimalPlaces,comment');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('authourizedSignatureLevel');
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_paymentvouchermaster.PVbankCode', 'left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['signature'] = $this->db->get()->row_array();

        return $data;
    }

    function load_Cheque_templates($payVoucherAutoId)
    {
        $this->db->select('bankGLAutoID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $glid = $this->db->get()->row_array();

        $this->db->select('srp_erp_chartofaccountchequetemplates.coaChequeTemplateID,srp_erp_chartofaccountchequetemplates.pageLink,srp_erp_systemchequetemplates.Description');
        $this->db->where('companyID', current_companyID());
        $this->db->where('GLAutoID', $glid['bankGLAutoID']);
        $this->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PV');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function get_po_amount()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $pocode = $this->input->post('pocode');
        $companyID = current_companyID();
        $purchaseOrderID=explode("|",$pocode);
        $poid=$purchaseOrderID[0];
        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('companyID', $companyID);
        $this->db->where('type', 'Advance');
        $this->db->where('purchaseOrderID', $poid);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $poadded = $this->db->get()->row_array();
        if (!empty($poadded)) {
            return array('w', 'PO already added');
            exit;
        }

        $sumTransactionAmount = $this->db->query("SELECT IFNULL(SUM(transactionAmount),0) AS totalTransactionAmount FROM srp_erp_paymentvoucherdetail WHERE purchaseOrderID = '" . $poid . "' AND companyID = $companyID AND type='Advance'")->row_array();
        $sumTransactionAmountPO = $this->db->query("SELECT 
                                                            SUM(( totalAmount - generalDiscountAmount ) + IFNULL( generalTaxAmount, 0 ) + IFNULL( taxAmount, 0 ) /* - IFNULL( amount, 0 ) */) AS totalTransactionAmount 
                                                        FROM
                                                            srp_erp_purchaseorderdetails
                                                        LEFT JOIN (
                                                            SELECT
                                                                SUM( amount ) AS amount,
                                                                documentDetailAutoID 
                                                            FROM
                                                                srp_erp_taxledger
                                                                JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                            WHERE
                                                                documentID = 'PO' 
                                                                AND rcmApplicableYN = 1 
                                                                AND taxCategory = 2 
                                                            GROUP BY
                                                                documentMasterAutoID,
                                                                documentID 
                                                        ) ledger ON ledger.documentDetailAutoID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID 
                                                        WHERE purchaseOrderID = {$poid} AND companyID = {$companyID}")->row_array();

        $previoussum = $sumTransactionAmount['totalTransactionAmount'];

        //$balanceamnt = $sumTransactionAmountPO['totalTransactionAmount'] - $previoussum;

        $poamount=ROUND($sumTransactionAmountPO['totalTransactionAmount'], 2);
        $advanceamount=$previoussum;

        $balanceamnt = $poamount - $advanceamount;
        return array('s', $poamount,$advanceamount,$balanceamnt);
    }

    function get_supplier_banks()
    {
        $companyID = current_companyID();
        $supplierAutoID = $this->input->post('supplierID');
        $this->db->select('supplierBankMasterID,bankName');
        $this->db->where('supplierAutoID', $supplierAutoID);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_supplierbankmaster');
        return $this->db->get()->result_array();
    }
/**SMSD */
    function fetch_signature_authority(){

        $GLAutoID = $this->input->post('gl_autoID');
        $companyID = current_companyID();
       
        $this->db->select('empID');
        $this->db->where('glAutoID', $GLAutoID);
        $this->db->where('deletedYN', 0);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_chartofaccount_signatures');
        $emp = $this->db->get()->result_array();

        $data = array(); 

        foreach($emp as $row){
            $this->db->select('EIdNo,ECode,Ename2');
            $this->db->where('EIdNo', $row['empID']);
            $this->db->where('Erp_companyID', $companyID);
            $this->db->from('srp_employeesdetails');
            $result = $this->db->get()->row_array();

            if (!empty($result)) {
                $data[$result['EIdNo']] = $result['ECode'] . ' | ' . $result['Ename2'];
            }
          
        }
       
        return $data;
    }

    function fetch_signature_authority_on_pv(){

        $GLAutoID = $this->input->post('gl_autoID');
       
        return fetch_signature_authority_on_gl_code($GLAutoID);
    }

    function fetch_payment_voucher_transfer_data($payVoucherAutoId)
    {
        $this->db->select('srp_erp_paymentvouchermaster.pvType as pvType,supplierBankMasterID,partyID');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchermaster');
        $type = $this->db->get()->row_array();
        if ($type['pvType'] == 'Direct' || $type['pvType'] == 'DirectItem' || $type['pvType'] == 'DirectExpense' || $type['pvType'] == 'Employee' || $type['pvType'] == 'Employee' || $type['pvType'] == 'EmployeeExpense' || $type['pvType'] == 'EmployeeItem') {
//            if ($type['pvType'] == 'Direct' || $type['pvType'] == 'Employee') {
            $this->db->select('*,srp_erp_chartofaccounts.bankName as bankName,srp_erp_chartofaccounts.bankAccountNumber as bankAccountNumber');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_chartofaccounts', 'srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        } else {
            $this->db->select('srp_erp_paymentvouchermaster.PVchequeDate as PVchequeDate,srp_erp_paymentvouchermaster.partyName,srp_erp_suppliermaster.nameOnCheque,srp_erp_paymentvouchermaster.transactionCurrency,srp_erp_paymentvouchermaster.pvType as pvType,srp_erp_paymentvouchermaster.accountPayeeOnly as accountPayeeOnly,srp_erp_paymentvouchermaster.*,srp_erp_chartofaccounts.bankName as bankName,srp_erp_chartofaccounts.bankAccountNumber as bankAccountNumber');
            $this->db->where('payVoucherAutoId', $payVoucherAutoId);
            $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID');
            $this->db->join('srp_erp_chartofaccounts', 'srp_erp_paymentvouchermaster.bankGLAutoID = srp_erp_chartofaccounts.GLAutoID');
            $this->db->from('srp_erp_paymentvouchermaster');
            $data['master'] = $this->db->get()->row_array();
        }


        $this->db->select('nameOnCheque');
        $this->db->where('supplierAutoID', $type['partyID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('accountNumber,swiftCode,IbanCode,bankName,accountName,bankAddress');
        $this->db->where('supplierBankMasterID', $type['supplierBankMasterID']);
        $this->db->from('srp_erp_supplierbankmaster');
        $data['bank'] = $this->db->get()->row_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();

        $this->db->select('transactionAmount');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('sum(taxPercentage) as taxPercentage');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax'] = $this->db->get()->row_array();

        return $data;
    }

    function update_paymentvoucher_collectiondetails()
    {
        $this->db->trans_start();
        $paymentvoucherid = trim($this->input->post('payVoucherAutoIdpv') ?? '');
        $status = trim($this->input->post('statuspv') ?? '');
        $date_format_policy = date_format_policy();
        $documentDate = $this->input->post('collectiondatepv');
        $formatted_documentDate = input_format_date($documentDate, $date_format_policy);
        $data['collectedStatus'] = $status;
        if ($status == 1) {
            $data['collectedByName'] = $this->input->post('colectedbyemp');
            $data['collectedDate'] = $formatted_documentDate;
            $data['collectionComments'] = $this->input->post('commentpv');
        } else if ($status == 2) {
            $data['collectedByName'] = null;
            $data['collectedDate'] = null;
            $data['collectionComments'] = $this->input->post('commentpvonhold');
        } else {
            $data['collectedByName'] = null;
            $data['collectedDate'] = null;
            $data['collectionComments'] = null;
        }
        $this->db->where('payVoucherAutoId', $paymentvoucherid);
        $this->db->update('srp_erp_paymentvouchermaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Collection detail status updated ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Collection detail status updated successfully.', $paymentvoucherid);
        }
    }

    function paymentvoucher_collectionheader()
    {
        $paymentvoucherAutoID = trim($this->input->post('autoID') ?? '');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("select *, DATE_FORMAT(collectedDate,'{$convertFormat}') AS collectedDate from srp_erp_paymentvouchermaster where companyID = $companyid AND  payVoucherAutoId = $paymentvoucherAutoID")->row_array();

        return $data;
    }


    function get_stockReturn_master($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_stockreturnmaster');
        $this->db->where('stockReturnAutoID', $id);
        $output = $this->db->get()->row_array();
        return $output;
    }


    function showBalanceAmount_matching()
    {
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        if(!empty($InvoiceAutoID)){
            $data = $this->db->query("SELECT
                                        paymentTotalAmount,
                                        DebitNoteTotalAmount,
                                        advanceMatchedTotal,
                                        (
                                            (
                                                (
                                                    (
                                                        IFNULL(tax.taxPercentage, 0) / 100
                                                    ) * (IFNULL(sid.transactionAmount, 0) - ((IFNULL(generalDiscountPercentage, 0) / 100) * IFNULL(sid.transactionAmount, 0)))
                                                ) + IFNULL(sid.transactionAmount, 0)
                                            ) - ((IFNULL(generalDiscountPercentage, 0) / 100) * IFNULL(sid.transactionAmount, 0))
                                        ) AS transactionAmount,
                                        srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces
                                    FROM
                                        srp_erp_paysupplierinvoicemaster
                                    LEFT JOIN (
                                        SELECT
                                            invoiceAutoID,
                                            (TRIM( ROUND( IFNULL( SUM( transactionAmount ), 0 ), 4 ) ) + 0 ) + (TRIM( ROUND( SUM(IFNULL(taxAmount, 0 )), 4 ) ) + 0 ) AS transactionAmount
                                        FROM
                                            srp_erp_paysupplierinvoicedetail
                                        GROUP BY
                                            invoiceAutoID
                                    ) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
                                    LEFT JOIN (
                                        SELECT
                                            invoiceAutoID,
                                            SUM(taxPercentage) AS taxPercentage
                                        FROM
                                            srp_erp_paysupplierinvoicetaxdetails
                                        GROUP BY
                                            invoiceAutoID
                                    ) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID
                                    WHERE
                                        srp_erp_paysupplierinvoicemaster.InvoiceAutoID=$InvoiceAutoID ")->row_array();
            //echo $this->db->last_query();exit;

            $amount = $data['transactionAmount'] - ($data['paymentTotalAmount'] + $data['DebitNoteTotalAmount'] + $data['advanceMatchedTotal']);

            if ($amount > 0) {
                $amount = $amount;
            } else {
                $data['transactionCurrencyDecimalPlaces']=2;
                $amount = 0;
            }
        }else{
            $data['transactionCurrencyDecimalPlaces']=2;
            $amount = 0;
        }


        return number_format($amount,$data['transactionCurrencyDecimalPlaces']);
    }

    function fetch_prq_code()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        $this->db->select('PVdate,transactionCurrencyID');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $result = $this->db->get()->row_array();

        $documentDate = $result['PVdate'];
        $transactionCurrencyID = $result['transactionCurrencyID'];
        $companyID = current_companyID();

        $data = $this->db->query("SELECT
	srp_erp_purchaserequestmaster.purchaseRequestID,
	srp_erp_purchaserequestmaster.purchaseRequestCode,
	srp_erp_purchaserequestmaster.documentDate,
	srp_erp_purchaserequestmaster.requestedByName,
 /*IFNULL(SUM(prqdetailpv.prQtypv),0) + IFNULL(SUM(prqdetailpo.prQtypo),0) AS prQty,
SUM(prqd.requestedQty) as requestedQty*/
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( SUM( prqdetailpv.prQtypv ), 0 ) + IFNULL( SUM( prqdetailpo.prQtypo ), 0 )), 2 )))))) AS prQty,
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( prqd.requestedQty )), 2 )))))) AS requestedQty
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
	 srp_erp_purchaserequestmaster.transactionCurrencyID = '$transactionCurrencyID'
AND srp_erp_purchaserequestmaster.companyID = '$companyID'
AND srp_erp_purchaserequestmaster.approvedYN = 1 AND srp_erp_purchaserequestmaster.closedYN != 1
GROUP BY
	prqd.purchaseRequestID")->result_array();

        return $data;

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
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }
       $purchaseRequestID= $this->input->post('purchaseRequestID');
        $data['detail'] = $this->db->query("SELECT
	prqd.*, /*IFNULL(prqdetailpv.prQtypv,0) + IFNULL(prqdetailpo.prQtypo,0) AS prQty*/
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((IFNULL( prqdetailpv.prQtypv, 0 ) + IFNULL( prqdetailpo.prQtypo, 0 )), 2 )))))) AS prQty, 
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((requestedQty - (
	    TRIM(TRAILING '.' FROM (
	        TRIM(TRAILING 0 FROM ((ROUND((IFNULL( prqdetailpv.prQtypv, 0 ) + IFNULL( prqdetailpo.prQtypo, 0 )), 2 )))))) )), 2 )))))) AS qtyFormated,$item_code_alias
	
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
	prqd.purchaseRequestID = '$purchaseRequestID'
AND srp_erp_purchaserequestmaster.approvedYN = 1
GROUP BY
	prqd.purchaseRequestDetailsID")->result_array();
        $companyID = current_companyID();
        $this->db->SELECT("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID,wareHouseLocation");
        $this->db->FROM('srp_erp_warehousemaster');
        $this->db->WHERE('companyID', $companyID);
        $data['ware_house'] = $this->db->get()->result_array();


        return $data;
    }

    function save_prq_base_items()
    {
        //$post = $this->input->post();

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaserequestdetails.*,sum(srp_erp_paymentvoucherdetail.prQty) AS prQty,srp_erp_purchaserequestmaster.purchaseRequestCode');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where_in('srp_erp_purchaserequestdetails.purchaseRequestDetailsID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaserequestdetails.purchaseRequestID');
        $this->db->join('srp_erp_paymentvoucherdetail', 'srp_erp_paymentvoucherdetail.prDetailID = srp_erp_purchaserequestdetails.purchaseRequestDetailsID', 'left');
        $this->db->group_by("purchaseRequestDetailsID");
        $query = $this->db->get()->result_array();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate,transactionCurrencyDecimalPlaces, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,companyReportingCurrencyDecimalPlaces,partyCurrencyID,partyExchangeRate,partyCurrency,partyExchangeRate,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $whrehouse = $this->input->post('whrehouse');

        $this->db->select('GLAutoID')->from('srp_erp_companycontrolaccounts')->where('controlAccountType', 'ACA');
        $ACA_ID = $this->db->where('companyID', current_companyID())->get()->row('GLAutoID');
        $ACA = fetch_gl_account_desc($ACA_ID);

        for ($i = 0; $i < count($query); $i++) {
            $this->db->select('prMasterID');
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->where('prMasterID', $query[$i]['purchaseRequestID']);
            $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $order_detail = $this->db->get()->result_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $serviceitm= $this->db->get()->row_array();

            if (!empty($order_detail) && $serviceitm['mainCategory']=="Inventory") {
                $this->session->set_flashdata('w', 'Purchase Request Details added already.');
            } else {
                $this->db->select('wareHouseCode,wareHouseDescription,wareHouseLocation');
                $this->db->from('srp_erp_warehousemaster');
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$i]);
                $wareHouse_detail = $this->db->get()->row_array();


                $data[$i]['prMasterID'] = $query[$i]['purchaseRequestID'];
                $data[$i]['type'] = 'PRQ';
                $data[$i]['prDetailID'] = $query[$i]['purchaseRequestDetailsID'];
                $data[$i]['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
                $data[$i]['prQty'] = $query[$i]['requestedQty'];
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['comment'] = $query[$i]['comment'];
                $data[$i]['remarks'] = $query[$i]['remarks'];
                $data[$i]['wareHouseAutoID'] = $wareHouseAutoID[$i];
                $data[$i]['wareHouseCode'] = $wareHouse_detail['wareHouseCode'];
                $data[$i]['wareHouseLocation'] = $wareHouse_detail['wareHouseLocation'];
                $data[$i]['wareHouseDescription'] = $wareHouse_detail['wareHouseDescription'];

                $item_data = fetch_item_data($query[$i]['itemAutoID']);
                if ($item_data['mainCategory'] == 'Inventory') {
                    $data[$i]['GLAutoID'] = $item_data['assteGLAutoID'];
                    $data[$i]['systemGLCode'] = $item_data['assteSystemGLCode'];
                    $data[$i]['GLCode'] = $item_data['assteGLCode'];
                    $data[$i]['GLDescription'] = $item_data['assteDescription'];
                    $data[$i]['GLType'] = $item_data['assteType'];
                }
                elseif ($item_data['mainCategory'] == 'Fixed Assets') {
                    $data[$i]['GLAutoID'] = $ACA_ID;
                    $data[$i]['systemGLCode'] = $ACA['systemAccountCode'];
                    $data[$i]['GLCode'] = $ACA['GLSecondaryCode'];
                    $data[$i]['GLDescription'] = $ACA['GLDescription'];
                    $data[$i]['GLType'] = $ACA['subCategory'];
                }
                else {
                    $data[$i]['GLAutoID'] = $item_data['costGLAutoID'];
                    $data[$i]['systemGLCode'] = $item_data['costSystemGLCode'];
                    $data[$i]['GLCode'] = $item_data['costGLCode'];
                    $data[$i]['GLDescription'] = $item_data['costDescription'];
                    $data[$i]['GLType'] = $item_data['costType'];
                }

                $data[$i]['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data[$i]['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data[$i]['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data[$i]['unittransactionAmount'] = $amount[$i];
                $data[$i]['transactionAmount'] = ($amount[$i]) * $qty[$i];
                $data[$i]['transactionCurrencyDecimalPlaces'] = $master_recode['transactionCurrencyDecimalPlaces'];

                $data[$i]['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                $data[$i]['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data[$i]['companyLocalAmount'] = $data[$i]['transactionAmount']/$master_recode['companyLocalExchangeRate'];
                $data[$i]['companyLocalCurrencyDecimalPlaces'] = $master_recode['companyLocalCurrencyDecimalPlaces'];
                $data[$i]['unitcompanyLocalAmount'] = $amount[$i]/$master_recode['companyLocalExchangeRate'];

                $data[$i]['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                $data[$i]['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data[$i]['companyReportingAmount'] = $data[$i]['transactionAmount']/$master_recode['companyReportingExchangeRate'];
                $data[$i]['companyReportingCurrencyDecimalPlaces'] = $master_recode['companyReportingCurrencyDecimalPlaces'];
                $data[$i]['unitcompanyReportingAmount'] = $amount[$i]/$master_recode['companyReportingExchangeRate'];

                $data[$i]['partyCurrencyID'] = $master_recode['partyCurrencyID'];
                $data[$i]['partyCurrency'] = $master_recode['partyCurrency'];
                $data[$i]['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                $data[$i]['partyCurrencyDecimalPlaces'] = $master_recode['partyCurrencyDecimalPlaces'];
                $data[$i]['partyAmount'] = $data[$i]['transactionAmount']/$master_recode['partyExchangeRate'];
                $data[$i]['unitpartyAmount'] = $amount[$i]/$master_recode['partyExchangeRate'];

                $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];

            }
        }

        if (!empty($data)) {
            //print_r($data);
            $this->db->insert_batch('srp_erp_paymentvoucherdetail', $data);
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

    function fetch_purchase_request_based_detail()
    {
        $this->db->select('*');
        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        $this->db->from('srp_erp_paymentvoucherdetail');
        return $this->db->get()->row_array();
    }

    function update_purchase_request_detail()
    {
        /*if (!empty($this->input->post('payVoucherDetailAutoID'))) {
            $this->db->select('payVoucherAutoId,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_paymentvoucherdetail');
            $this->db->where('type', 'PRQ');
            $this->db->where('payVoucherAutoId', trim($this->input->post('payVoucherAutoId') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('payVoucherDetailAutoID !=', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Payment voucher Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }*/

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate,transactionCurrencyDecimalPlaces, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,companyReportingCurrencyDecimalPlaces,partyCurrencyID,partyExchangeRate,partyCurrency,partyExchangeRate,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $uom = explode('|', $this->input->post('uom'));

        $this->db->select('wareHouseCode,wareHouseDescription,wareHouseLocation');
        $this->db->from('srp_erp_warehousemaster');
        $this->db->where('wareHouseAutoID', $this->input->post('wareHouseAutoID'));
        $wareHouse_detail = $this->db->get()->row_array();


        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unittransactionAmount'] = (trim($this->input->post('estimatedAmount') ?? ''));
        $data['transactionAmount'] = ($data['unittransactionAmount'] * trim($this->input->post('quantityRequested') ?? ''));
        $data['unitcompanyLocalAmount'] = $this->input->post('estimatedAmount')/$master_recode['companyLocalExchangeRate'];
        $data['companyLocalAmount'] =  $data['transactionAmount']/$master_recode['companyLocalExchangeRate'];
        $data['unitcompanyReportingAmount'] = $this->input->post('estimatedAmount')/$master_recode['companyReportingExchangeRate'];
        $data['companyReportingAmount'] =  $data['transactionAmount']/$master_recode['companyReportingExchangeRate'];
        $data['unitpartyAmount'] = $this->input->post('estimatedAmount')/$master_recode['partyExchangeRate'];
        $data['partyAmount'] =  $data['transactionAmount']/$master_recode['partyExchangeRate'];
        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['wareHouseAutoID'] = trim($this->input->post('wareHouseAutoID') ?? '');
        $data['wareHouseLocation'] = $wareHouse_detail['wareHouseLocation'];
        $data['wareHouseCode'] = $wareHouse_detail['wareHouseCode'];
        $data['wareHouseDescription'] = $wareHouse_detail['wareHouseDescription'];



        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('payVoucherDetailAutoID') ?? '')) {
            $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
            $this->db->update('srp_erp_paymentvoucherdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Payment Voucher Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');

            }
        }
    }

    function fetch_pv_direct_details_suom()
    {
        $payVoucherAutoId = trim($this->input->post('payVoucherAutoId') ?? '');
        
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,partyCurrencyDecimalPlaces');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['currency'] = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_itemmaster.isSubitemExist,CONCAT_WS(
	\' - Part No : \',
IF
	( LENGTH( srp_erp_paymentvoucherdetail.`comment` ), `srp_erp_paymentvoucherdetail`.`comment`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,srp_erp_purchaserequestmaster.purchaseRequestCode,srp_erp_unit_of_measure.UnitShortCode as secuom ');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_paymentvoucherdetail.prMasterID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paymentvoucherdetail.SUOMID', 'left');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $data['detail'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function fetch_payment_voucher_detail_suom()
    {
        $this->db->select('*,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec');
        $this->db->where('payVoucherDetailAutoID', trim($this->input->post('payVoucherDetailAutoID') ?? ''));
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paymentvoucherdetail.SUOMID', 'left');
        $this->db->from('srp_erp_paymentvoucherdetail');
        return $this->db->get()->row_array();
    }


    function fetch_payment_voucher_template_data_suom($payVoucherAutoId)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select(' *,srp_erp_paymentvouchermaster.createdUserName as crName,srp_erp_segment.description as segDescription,DATE_FORMAT(PVdate, \'' . $convertFormat . '\') AS PVdate,DATE_FORMAT(PVchequeDate,\'' . $convertFormat . '\') AS PVchequeDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_suppliermaster.nameOnCheque as nameOnCheque,srp_erp_suppliermaster.supplierName as supname,srp_erp_suppliermaster.supplierSystemCode as supsyscode, srp_erp_suppliermaster.supplierAddress1 as supaddress1, srp_erp_suppliermaster.supplierTelephone as suptel,srp_erp_suppliermaster.supplierFax as supfax, case pvType when \'Direct\' then partyName when \'PurchaseRequest\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName,

        case pvType when \'Direct\' then " " when \'Employee\' then CONCAT_WS(\', \',
       IF(LENGTH(srp_employeesdetails.EpAddress1),srp_employeesdetails.EpAddress1,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress2),srp_employeesdetails.EpAddress2,NULL),
       IF(LENGTH(srp_employeesdetails.EpAddress3),srp_employeesdetails.EpAddress3,NULL)


) when \'Supplier\' then srp_erp_suppliermaster.supplierAddress1  end as partyAddresss,

        case pvType when \'Direct\' then " " when \'Employee\' then CONCAT_WS(" / ", srp_employeesdetails.EpTelephone ,srp_employeesdetails.EcFax) when \'Supplier\' then CONCAT_WS(" / ", srp_erp_suppliermaster.supplierTelephone ,srp_erp_suppliermaster.supplierFax) end as parttelfax,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN
CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn

        ');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_paymentvouchermaster.partyID = srp_erp_suppliermaster.supplierAutoID', 'Left');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'Left');
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paymentvouchermaster.segmentID', 'Left');
        $this->db->from('srp_erp_paymentvouchermaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
        $this->db->select('srp_erp_paymentvoucherdetail.*,`srp_erp_itemmaster`.`partNo`,	CONCAT_WS(
	\' - Part No : \',
IF
	( LENGTH( srp_erp_paymentvoucherdetail.`comment` ), `srp_erp_paymentvoucherdetail`.`comment`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secuom
	');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Item');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paymentvoucherdetail.SUOMID', 'left');
        $data['item_detail'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'GL');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['gl_detail'] = $this->db->get()->result_array();
        
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Invoice');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['invoice'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'debitnote');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['debitnote'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'SR');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['SR'] = $this->db->get()->result_array();

        $this->db->select('srp_erp_paymentvoucherdetail.*,srp_erp_purchaserequestmaster.purchaseRequestCode');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'PRQ');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_paymentvoucherdetail.prMasterID', 'left');
        $data['PRQ'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'Advance');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['advance'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('type', 'SC');
        $this->db->from('srp_erp_paymentvoucherdetail');
        $data['sales_commission'] = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->from('srp_erp_paymentvouchertaxdetails');
        $data['tax_detail'] = $this->db->get()->result_array();
        return $data;
    }

    function check_cheque_used(){
        $PayVoucherAutoId = trim($this->input->post('PayVoucherAutoId') ?? '');
        $chequeRegisterDetailID = trim($this->input->post('chequeRegisterDetailID') ?? '');

        $this->db->select('*');
        $this->db->where('chequeRegisterDetailID', $chequeRegisterDetailID);
        $this->db->where('documentMasterAutoID !=', $PayVoucherAutoId);
        $this->db->where('status', 1);
        $this->db->from('srp_erp_chequeregisterdetails');
        $data = $this->db->get()->row_array();
        if(!empty($data)){
            return 1;
        }else{
            return 2;
        }
    }

    function getchequeDetails($chequeRegisterDetailID){
        $this->db->select('chequeNo');
        $this->db->where('chequeRegisterDetailID', $chequeRegisterDetailID);
        $this->db->from('srp_erp_chequeregisterdetails');
        $result= $this->db->get()->row_array();

        return $result['chequeNo'];
    }

    function update_cheque_detail($chequeRegisterDetailID,$documentMasterAutoID){
        $dataD = array(
            'status' => 0,
            'documentMasterAutoID' => null,
            'documentID' => null
        );
        $this->db->where('documentMasterAutoID', $documentMasterAutoID);
        $this->db->where('documentID', 'PV');
        $this->db->update('srp_erp_chequeregisterdetails', $dataD);

        $data = array(
            'status' => 1,
            'documentMasterAutoID' => $documentMasterAutoID,
            'documentID' => 'PV',
            'modifiedPCID' => current_pc(),
            'modifiedDateTime' => current_date(),
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_user()
        );
        $this->db->where('chequeRegisterDetailID', $chequeRegisterDetailID);
        $result=$this->db->update('srp_erp_chequeregisterdetails', $data);
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
    function totalamountreceipt($payVoucherAutoId)
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT
	IFNULL(SUM( transactionAmount ) ,0) AS totalamounttransaction 
FROM
	`srp_erp_paymentvoucherdetail`
	where 
	companyID ='{$companyID}' 
	AND payVoucherAutoId ='{$payVoucherAutoId}'
	GROUP BY
	payVoucherAutoId")->row_array();
        return $data;

    }

    function fetch_expense_claim_code()
    {
        $companyID = current_companyID();
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $this->db->select('PVdate, partyID');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('companyID', $companyID);
        $pv = $this->db->get()->row_array();

        $this->db->select('srp_erp_expenseclaimmaster.expenseClaimMasterAutoID AS expenseClaimMasterAutoID, expenseClaimCode, expenseClaimDate, comments');
        $this->db->from('srp_erp_expenseclaimmaster');
        $this->db->where('claimedByEmpID', $pv['partyID']);
        $this->db->where('companyID', $companyID);
        $this->db->where('expenseClaimDate <= "' . $pv['PVdate'] . '"');
        $this->db->where('addedToSalary', 0);
        $this->db->where('addedForPayment', 0);
        $this->db->where('approvedYN', 1);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }

    function fetch_expense_gl_code()
    {
        $companyID = current_companyID();
        
        $this->db->select('t1.expenseGLAutoID as expenseGLAutoID, t2.systemAccountCode as expenseglcode, t2.GLDescription as expenseGLDescription');
        $this->db->from('srp_erp_leave_salary_provision AS t1');
        $this->db->join('srp_erp_chartofaccounts AS t2', 't2.GLAutoID = t1.expenseGLAutoID');
        $this->db->where('t1.isProvision', 1);
        $this->db->where('t1.companyID', $companyID);
        $this->db->where('t2.isActive', 1);
        $this->db->where('t2.isBank', 0);
        $this->db->where('t2.masterAccountYN', 0);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }

    function fetch_provision_amount(){
        $companyID = current_companyID();

        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $this->db->select('PVdate, partyID');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('companyID', $companyID);
        $pv = $this->db->get()->row_array();

        $empID = $pv['partyID'];

        $provision_detail = $this->db->query("SELECT empID,SUM(amount) as amount, JVmasterID
            FROM `srp_erp_jv_provision_detail`
            WHERE empID = {$empID} and companyID = {$companyID} and gratuityID IS NULL and provisionDocType = 'JV' and isReversal != 1
            GROUP BY JVmasterID 
            ORDER BY JVmasterID DESC")->row_array();
        
        if($provision_detail){
            //deduct pv
            $pv_details = $this->db->query("SELECT SUM(amount) as amount
                FROM `srp_erp_jv_provision_detail`
                WHERE empID = {$empID} and companyID = {$companyID} and provisionDocType = 'PV' and isReversal != 1
                GROUP BY empID")->row_array();

            $provision_detail['amount'] = $provision_detail['amount'] + $pv_details['amount'];

            if($provision_detail['amount'] < 0){
                $provision_detail['amount'] = 0;
            }

            return $provision_detail;
        }
        
    }

    function fetch_expense_claim_details()
    {
        $companyID = current_companyID();
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $this->db->select('PVdate, partyID, companyLocalExchangeRate, transactionCurrencyDecimalPlaces, transactionCurrency');
        $this->db->from('srp_erp_paymentvouchermaster');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->where('companyID', $companyID);
        $pv = $this->db->get()->row_array();

        $this->db->select('srp_erp_expenseclaimmaster.expenseClaimMasterAutoID AS expenseClaimMasterAutoID, expenseClaimCode, expenseClaimDate, segmentID, segmentCode, comments, (det.amount /' . $pv['companyLocalExchangeRate'] . ') AS amount');
        $this->db->from('srp_erp_expenseclaimmaster');
        $this->db->join('(SELECT SUM(companyLocalAmount) AS amount, expenseClaimMasterAutoID FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID)det',
            'det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID', 'LEFT');
        $this->db->where('claimedByEmpID', $pv['partyID']);
        $this->db->where('companyID', $companyID);
        $this->db->where('srp_erp_expenseclaimmaster.expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
        $data = $this->db->get()->row_array();

        $data['transactionCurrencyDecimalPlaces'] = $pv['transactionCurrencyDecimalPlaces'];
        $data['transactionCurrency'] = $pv['transactionCurrency'];

        return $data;
    }

    function save_emp_expense_multiple()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $this->input->post('payVoucherAutoId'));
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $docTypeIDs = $this->input->post('docTypeID');
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $gl_codes = $this->input->post('gl_code');
        $expenseGLCode = $this->input->post('expenseGLCode');
        $gl_code_des = $this->input->post('gl_code_des');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $projectID = $this->input->post('projectID');
        $discountPercentage = $this->input->post('discountPercentage');
        $ProjectCategory = $this->input->post('project_categoryID');
        $ProjectSubcategory = $this->input->post('project_subCategoryID');
        $expenseType = $this->input->post('expenseType');

        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($advanceCostCapturing == 1){
            $activityCodeID = $this->input->post('activityCode');
        }

        foreach ($docTypeIDs as $key => $docTypeID) {
            $documentID = substr($docTypeIDs[$key], 0, 2);
            if($expenseType && isset($expenseType[$key])){
                $select_type = $expenseType[$key];

                if($select_type == 3){
                    $documentID = 'LS';
                }
            }

            if ($documentID == 'GL' || $documentID == 'LS') {
                $segment = explode('|', $segment_gls[$key]);
                $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
                $data['GLAutoID'] = $gl_codes[$key];
                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID[$key]);
                    $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                    $data['project_categoryID'] = $ProjectCategory[$key];
                    $data['project_subCategoryID'] = $ProjectSubcategory[$key];
                }
                $gl_code = fetch_gl_account_desc($gl_codes[$key]);
                $data['systemGLCode'] = trim($gl_code['systemAccountCode'] ?? '');
                $data['GLCode'] = trim($gl_code['GLSecondaryCode'] ?? '');
                $data['GLDescription'] = trim($gl_code['GLDescription'] ?? '');
                $data['GLType'] = trim($gl_code['subCategory'] ?? '');
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');
                if($advanceCostCapturing == 1){
                    $data['activityCodeID'] = $activityCodeID[$key];
                }
                $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                $data['discountPercentage'] = trim($discountPercentage[$key]);
                $data['discountAmount'] = trim(($amount[$key]*$discountPercentage[$key])/100);
                $data['transactionAmount'] = trim($amount[$key]-$data['discountAmount']);
                $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['partyCurrency'] = $master_recode['partyCurrency'];
                $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
                $data['description'] = $descriptions[$key];
                $data['type'] = $documentID;
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

                $this->db->insert('srp_erp_paymentvoucherdetail', $data);
            } else if($documentID == 'EC') {
                $this->db->select('srp_erp_expenseclaimdetails.*, glAutoID, glCode, glCodeDescription');
                $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID[$key]);
                $this->db->join('srp_erp_expenseclaimcategories', 'srp_erp_expenseclaimcategories.expenseClaimCategoriesAutoID = srp_erp_expenseclaimdetails.expenseClaimCategoriesAutoID', 'LEFT');
                $expenseDetails = $this->db->get('srp_erp_expenseclaimdetails')->result_array();
                $insert = false;
                if(!empty($expenseDetails)) {
                    foreach ($expenseDetails AS $det) {
//                        $segment = explode('|', $segment_gls[$key]);
                        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
                        if ($projectExist == 1) {
                            $projectCurrency = project_currency($projectID[$key]);
                            $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                            $data['projectID'] = $projectID[$key];
                            $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                            $data['project_categoryID'] = $ProjectCategory[$key];
                            $data['project_subCategoryID'] = $ProjectSubcategory[$key];
                        }
                        if($det['glAutoID']) {
                            $data['GLAutoID'] = $det['glAutoID'];
                            $gl_code = fetch_gl_account_desc($data['GLAutoID']);
                            $data['systemGLCode'] = trim($gl_code['systemAccountCode'] ?? '');
                            $data['GLCode'] = trim($gl_code['GLSecondaryCode'] ?? '');
                            $data['GLDescription'] = trim($gl_code['GLDescription'] ?? '');
                            $data['GLType'] = trim($gl_code['subCategory'] ?? '');
                        }
                        $data['expenseClaimMasterAutoID'] = trim($expenseClaimMasterAutoID[$key]);
                        $data['expenseClaimDetailsID'] = trim($det['expenseClaimDetailsID'] ?? '');
                        $data['segmentID'] = trim($det['segmentID'] ?? '');
                        $segmentCode = '';
                        if($det['segmentID']) {
                            $companyID = current_companyID();
                            $segmentCode = $this->db->query("SELECT segmentCode FROM srp_erp_segment WHERE companyID = {$companyID} AND segmentID = {$det['segmentID']}")->row('segmentCode');
                        }
                        $data['segmentCode'] = trim($segmentCode);
                        if($advanceCostCapturing == 1){
                            $data['activityCodeID'] = $activityCodeID[$key];
                        }
                        $data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                        $data['transactionCurrency'] = $master_recode['transactionCurrency'];
                        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                        $data['discountPercentage'] = null;
                        $data['discountAmount'] = null;
                        $data['transactionAmount'] = ($det['companyLocalAmount'] * $master_recode['companyLocalExchangeRate']);
                        $data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                        $data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                        $data['companyLocalAmount'] = $det['companyLocalAmount'];
                        $data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                        $data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                        $data['companyReportingAmount'] = $det['companyReportingAmount'];
                        $data['partyCurrency'] = $master_recode['partyCurrency'];
                        $data['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                        $data['partyAmount'] = ($data['transactionAmount'] / $master_recode['partyExchangeRate']);
                        $data['description'] = $descriptions[$key];
                        $data['type'] = 'EC';
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

                        $insert = $this->db->insert('srp_erp_paymentvoucherdetail', $data);
                    }
                    if($insert) {
                        $data = array('addedForPayment' => 1);
                        $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID[$key]);
                        $this->db->update('srp_erp_expenseclaimmaster', $data);
                    }
                }
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Detail :  Saved Successfully.');
        }
    }

    function delete_pv_expense_claim_detail()
    {
        $this->db->delete('srp_erp_paymentvoucherdetail', array('expenseClaimMasterAutoID' => trim($this->input->post('expenseClaimMasterAutoID') ?? '')));

        $data = array('addedForPayment' => 0);
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->update('srp_erp_expenseclaimmaster', $data);

        $this->session->set_flashdata('s', 'Deleted Records Successfully.');
        return true;
    }

    function send_pv_email()
    {
        $payVoucherAutoId = trim($this->input->post('payVoucherAutoId') ?? '');
        $invoiceemail = trim($this->input->post('email') ?? '');
        $attachmentID = $this->input->post('attachmentID');
        $documentid = 'PV';
        $attachmentID_join = '';
        if(!empty($attachmentID))
        {
            $attachmentID_join =   join(', ', $attachmentID);
        }

        if(empty($invoiceemail) || $invoiceemail =='')
        {
            return array('e', 'email address is required.');
            exit();
        }

        $ccEmail = trim($this->input->post('ccemail') ?? '');
        $this->db->select('srp_erp_paymentvouchermaster.*,supplierEmail, supplierName');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->db->from('srp_erp_paymentvouchermaster ');
        $results = $this->db->get()->row_array();

        if (!empty($results)) {
            if ($results['supplierEmail'] == '') {
                $data_master['supplierEmail'] = $invoiceemail;
                $this->db->where('supplierAutoID', $results['partyID']);
                $this->db->update('srp_erp_suppliermaster', $data_master);
            }
        }
        $this->db->select('supplierEmail,supplierName');
        $this->db->where('supplierAutoID', $results['partyID']);
        $this->db->from('srp_erp_suppliermaster ');
        $customerMaster = $this->db->get()->row_array();


        $data['extra'] = $this->fetch_payment_voucher_template_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PV');
        $this->db->from('srp_erp_documentcodemaster');
        $printfooterResult = $this->db->get()->row_array();

        $printHeaderFooterYN = $printfooterResult['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payment_voucher_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $data['emailView'] = 1; // to get the html view otherwise it will set two headers
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print', $data, true);

        $this->load->library('pdf');
        $path = UPLOAD_PATH.base_url().'/uploads/paymentVoucher/'. $payVoucherAutoId .$results["documentID"] . current_userID() . ".pdf";
        $this->pdf->save_pdf($html, 'A4', 1, $path);

        if (!empty($customerMaster)) {
            if ($customerMaster['supplierEmail'] != '') {
                $param = array();
                $param["empName"] = 'Supplier';
                $param["body"] = 'Payment has been released and voucher is attached.';
                $mailData = [
                    'approvalEmpID' => '',
                    'documentCode' => '',
                    'toEmail' => $invoiceemail ,
                    'ccEmail' => $ccEmail ,
                    'subject' => "Payment has been released",
                    'param' => $param
                ];
                send_customerinvoice_emailCc($mailData, 1,$path,$documentid, $attachmentID_join);
                return array('s', 'Email Send Successfully.',$invoiceemail,$payVoucherAutoId);
            } else {
                return array('e', 'Please enter an Email ID.');
            }
        }
    }

    function fetch_line_tax_and_vat()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($this->input->post('payVoucherAutoId') ?? ''),'PV','payVoucherAutoId');
        if($data['isGroupByTax'] == 1){ 
            $data['dropdown'] = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription', 2);
        }else { 
           $data = array();
        }
        return $data;
    }

    function generate_receipt_voucher(){

        $paymentVoucherAutoID = $this->input->post('id');

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $paymentVoucherAutoID);
        $this->db->from('srp_erp_paymentvouchermaster ');
        $paymentMaster = $this->db->get()->row_array();

        //srp_erp_suppliermaster
        $this->db->select('customerID');
        $this->db->where('supplierAutoID', $paymentMaster['partyID']);
        $this->db->from('srp_erp_suppliermaster ');
        $supplierdetails = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('payVoucherAutoId', $paymentVoucherAutoID);
        $this->db->where('detailInvoiceType', 'CUS');
        $this->db->from('srp_erp_paymentvoucherdetail ');
        $paymentDetails = $this->db->get()->result_array();

        //Receipt_voucher_model
        $this->load->model('Receipt_voucher_model');

        if($supplierdetails['customerID']){

            $this->db->select('*');
            $this->db->where('customerAutoID', $supplierdetails['customerID']);
            $this->db->from('srp_erp_customermaster ');
            $customerdetails = $this->db->get()->row_array();

            $_POST['vouchertype'] = "CustomerInvoices";
            $_POST['segment'] = $paymentMaster['segmentID'].'|'.$paymentMaster['segmentCode'];
            $_POST['RVdate'] = $paymentMaster['PVdate'];
            $_POST['referenceno'] = $paymentMaster['referenceNo'];
            $_POST['customerID'] =  $customerdetails['customerAutoID'];
            $_POST['customer_name'] = $customerdetails['customerName'];
            $_POST['transactionCurrencyID'] = $paymentMaster['transactionCurrencyID'];
            $_POST['RVbankCode'] = '';
            $_POST['financeyear'] = $paymentMaster['companyFinanceYearID'];
            $_POST['financeyear_period'] = $paymentMaster['companyFinancePeriodID'];
            $_POST['financeyear_period'] = $paymentMaster['companyFinancePeriodID'];
            $_POST['companyFinanceYear'] = $paymentMaster['companyFinanceYear'];
            $_POST['currency_code'] = $paymentMaster['transactionCurrency'].' | '.$paymentMaster['transactionCurrency'];
    
            $header = array('last_id' => 43); //$this->Receipt_voucher_model->save_receiptvoucher_header();

            if($header){
                $receiptAutoID = $header['last_id'];

                $invoiceAutoID = array();
                $amount = array();
  
                foreach($paymentDetails as $payment){

                    $invoiceAutoID[] = $payment['InvoiceAutoID'];
                    $amount[] = $payment['transactionAmount'];
            
                }

                $_POST['invoiceAutoID'] = $invoiceAutoID;
                $_POST['amount'] = $amount;
                $_POST['receiptVoucherAutoId'] = $receiptAutoID;

                $this->Receipt_voucher_model->save_inv_base_items();

            }
            return array('s', 'Receipt Voucher Created Successfully.');
            

        }else{
            return array('e', 'No customer linked to this Supplier.');

        }
       

    }
}