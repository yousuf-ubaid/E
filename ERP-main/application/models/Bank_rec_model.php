<?php

class Bank_rec_model extends ERP_Model
{

    function viewbankrec_detail()
    {
        $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
        $bankRecAutoID = trim($this->input->post('bankRecAutoID') ?? '');
        $master = $this->get_bank_rec_header();
        $startdate = $master['year'] . '-' . $master['month'] . '-01';
        $endDate = $master['year'] . '-' . $master['month'] . '-31';
        $master['bankRecAsOf'];
        /*     $sql="SELECT transactionType,documentDate,documentSystemCode,partyCode,partyName,chequeNo,chequeDate,bankCurrencyAmount,bankLedgerAutoID,bankCurrencyDecimalPlaces,clearedYN,memo FROM srp_erp_bankledger WHERE bankGLAutoID = {$GLAutoID} AND (clearedYN = 0 OR bankRecMonthID={$bankRecAutoID}) AND (documentDate between '{$startdate}' AND '{$endDate}') ";*/
        $date_format_policy = date_format_policy();
        $bnkRecAsOf = $master['bankRecAsOf'];
        $bankRecAsOf = input_format_date($bnkRecAsOf, $date_format_policy);
        $sql = "SELECT transactionType,documentDate,clearedDate,documentSystemCode,partyCode,partyName,chequeNo,chequeDate,bankCurrencyAmount,bankLedgerAutoID,bankCurrencyDecimalPlaces,clearedYN,memo FROM srp_erp_bankledger WHERE bankGLAutoID = {$GLAutoID} AND (clearedYN = 0 OR bankRecMonthID={$bankRecAutoID}) AND documentDate <= '{$bankRecAsOf}' order by documentDate asc";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    function get_opening_balance_bank_rec()
    {
        $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
        $data = $this->db->query("Select receipt,payment,receipt-payment as balance from(SELECT SUM( IF ( transactionType = 2, bankcurrencyAmount, 0 ) ) payment, SUM( IF ( transactionType = 1, bankcurrencyAmount, 0 ) ) AS receipt FROM srp_erp_bankrecmaster m LEFT JOIN srp_erp_bankledger d ON m.bankRecAutoID =d.bankRecMonthID WHERE confirmedYN = 1 AND d.bankGLAutoID ={$GLAutoID} and d.bankRecMonthID is not NULL AND clearedYN=1 )tt")->row_array();
        return $data['balance'];
    }

    function bank_rec_confirm()
    {
        $bankRecAutoID = $this->input->post('bankRecAutoID');
        $GLAutoID = $this->input->post('GLAutoID');
        $this->load->library('Approvals');
        $master = $this->get_bank_rec_header();

        $validate_code = validate_code_duplication($master['bankRecPrimaryCode'], 'bankRecPrimaryCode', $bankRecAutoID,'bankRecAutoID', 'srp_erp_bankrecmaster');
        if(!empty($validate_code)) {
            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
            return false;
        }

        $approvals_status = $this->approvals->CreateApproval('BRC', $bankRecAutoID, $master['bankRecPrimaryCode'], 'Bank Reconsiliation', 'srp_erp_bankrecmaster', 'bankRecAutoID');
        $openingBalance = $this->get_opening_balance_bank_rec();
        if (empty($openingBalance)) {
            $openingBalance = 0;
        }
        $closingquery = $this->db->query("SELECT SUM(IF(transactionType = 2, bankcurrencyAmount, 0)) payment, SUM(IF(transactionType = 1, bankcurrencyAmount, 0)) as receipt FROM srp_erp_bankrecmaster m LEFT JOIN srp_erp_bankledger d ON m.bankGLAutoID = d.bankGLAutoID WHERE confirmedYN = 0 AND bankRecAutoID={$bankRecAutoID} AND  clearedYN=1")->row_array();
        $closingBalance = $closingquery['receipt'] - $closingquery['payment']; /* 'confirmedDate'      => $this->common_data['current_date'], 'confirmedByEmpID'   => $this->common_data['current_userID'], 'confirmedByName'    => $this->common_data['current_user'],*/
        if ($approvals_status==1) {
            $data = array('confirmedYN' => 1, 'openingBalance' => $openingBalance, 'closingBalance' => $closingBalance);
            $this->db->where('bankRecAutoID', $bankRecAutoID);
            $this->db->update('srp_erp_bankrecmaster', $data);
            $this->session->set_flashdata('s', 'Approvals Created Successfully.');
            return true;
        } else if($approvals_status==3){
            $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return false;
        }
    }

    function get_bank_rec_header()
    {
        $convertFormat = convert_date_format_sql();
        $bankRecAutoID = trim($this->input->post('bankRecAutoID') ?? '');
        $data = $this->db->query("SELECT *,createdBy as createdUserName,DATE_FORMAT(createdDateTime,'$convertFormat ') AS createdDateTime, CASE WHEN confirmedYN = 2 || confirmedYN = 3 THEN \" - \" WHEN confirmedYN = 1 THEN CONCAT_WS(' on ',IF( LENGTH( confirmedByname ), confirmedByname, '-' ),IF( LENGTH( DATE_FORMAT(confirmedDate, '%d-%m-%Y %h:%i:%s' ) ), DATE_FORMAT(confirmedDate, '%d-%m-%Y %h:%i:%s' ), NULL ) ) ELSE \"-\" END confirmedYNn,DATE_FORMAT(bankRecAsOf,'$convertFormat') AS bankRecAsOf,DATE_FORMAT(approvedDate,'.$convertFormat. %h:%i:%s') AS approvedDate FROM srp_erp_bankrecmaster where bankRecAutoID={$bankRecAutoID} ")->row_array();
        return $data;
    }

    function getconfirmationdetails()
    {
        $bankRecAutoID = trim($this->input->post('bankRecAutoID') ?? '');
        $data = $this->db->query("SELECT transactionType,documentDate,documentSystemCode,partyCode,partyName,chequeNo,chequeDate,bankCurrencyAmount,bankLedgerAutoID,bankCurrencyDecimalPlaces,clearedYN FROM srp_erp_bankledger WHERE  bankRecMonthID={$bankRecAutoID} AND clearedYN = 1 ")->result_array();
        return $data;
    }

    function getunconfirmedDetails()
    {
      $companyID=current_companyID();
        $bankRecAutoID = trim($this->input->post('bankRecAutoID') ?? '');
        $GLAutoID = trim($this->input->post('GLAutoID') ?? '');
        $master = $this->get_bank_rec_header();

        $startdate = $master['year'] . '-' . $master['month'] . '-01';
        $endDate = $master['year'] . '-' . $master['month'] . '-31';
      $bnkRecAsOf = $master['bankRecAsOf'];
      $date_format_policy = date_format_policy();
      $bankRecAsOf = input_format_date($bnkRecAsOf, $date_format_policy);

 /*     $openingbalance = $this->db->query( "SELECT receipt, payment, receipt - payment AS balance FROM ( SELECT SUM( IF ( transactionType = 2, bankcurrencyAmount, 0 ) ) payment, SUM( IF ( transactionType = 1, bankcurrencyAmount, 0 ) ) AS receipt FROM srp_erp_bankrecmaster m LEFT JOIN srp_erp_bankledger d ON m.bankRecAutoID = d.bankRecMonthID WHERE m.companyID = {$companyID} AND documentDate <= '{$asOfDate}' AND d.bankGLAutoID = {$GLAutoID} AND ( d.bankRecMonthID IS  NULL OR bankRecMonthID IN ( SELECT bankRecAutoID FROM srp_erp_bankrecmaster WHERE bankGLAutoID = {$GLAutoID} AND bankRecAsOf <= '{$asOfDate}' ) ) ) tt;")->row_array();*/

        $data = $this->db->query("SELECT transactionType, documentDate, documentSystemCode, partyCode, partyName, chequeNo, chequeDate, bankCurrencyAmount, bankLedgerAutoID, bankCurrencyDecimalPlaces, clearedYN FROM srp_erp_bankledger WHERE bankGLAutoID = {$GLAutoID} AND documentDate <= '{$bankRecAsOf}' AND     (clearedYN = 0 OR bankRecMonthID != {$bankRecAutoID} AND bankRecMonthID NOT IN ( SELECT bankRecAutoID FROM `srp_erp_bankrecmaster` WHERE companyID = {$companyID} AND bankGLAutoID = {$GLAutoID} AND bankRecAsOf <= '{$bankRecAsOf}' )) ORDER BY documentDate ASC")->result_array();
/*        $data = $this->db->query("SELECT transactionType,documentDate,documentSystemCode,partyCode,partyName,chequeNo,chequeDate,bankCurrencyAmount,bankLedgerAutoID,bankCurrencyDecimalPlaces,clearedYN FROM srp_erp_bankledger WHERE clearedYN = 0 AND bankGLAutoID = {$GLAutoID} AND clearedYN = 0 AND (documentDate <= '{$bankRecAsOf}')  order by documentDate asc ")->result_array();*/
        return $data;
    }

    function getopeningbalancebyrectautoID()
    {
        $bankRecAutoID = trim($this->input->post('bankRecAutoID') ?? '');
        $data = $this->db->query("Select receipt,payment,openingBalance,receipt-payment as closingbalance from(SELECT m.openingBalance, SUM( IF (transactionType = 2, bankcurrencyAmount, 0 ) ) payment, SUM( IF (transactionType = 1, bankcurrencyAmount, 0 ) ) AS receipt FROM srp_erp_bankrecmaster m LEFT JOIN srp_erp_bankledger d ON m.bankRecAutoID =d.bankRecMonthID WHERE m.bankRecAutoID={$bankRecAutoID} and d.clearedYN=1 )tt;")->row_array();
        return $data['openingBalance'];
    }

    function save_bank_rec_header()
    {

        $date_format_policy = date_format_policy();
        $dateAsOf = $this->input->post('bankRecAsOf');
        $bankRecAsOf = input_format_date($dateAsOf, $date_format_policy);

        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $month = explode('-', trim($bankRecAsOf));
        $data['year'] = $month[0];
        $data['month'] = $month[1];
        $data['bankRecAsOf'] = $bankRecAsOf;
        $data['description'] = $this->input->post('description');
        $data['companyID'] = current_companyID();
        $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
        $data['createdBy'] = $this->common_data['current_user'];
        $data['bankRecPrimaryCode'] = $this->sequence->sequence_generator('BRC');
        $this->db->trans_start();
        $this->db->insert('srp_erp_bankrecmaster', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Saved Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function saveLoanMgtGlMapping()
    {
        $this->db->trans_start();
       // $data['principalAmount']=$this->input->post('principalAmount');
        $data['principalGlCode']=$this->input->post('principalGlCode');
      //  $data['interestAmount']=$this->input->post('interestAmount');
        $data['interestGlCode']=$this->input->post('interestGlCode');
      //  $data['liabilityAmount']=$this->input->post('liabilityAmount');
        $data['libilityGlCode']=$this->input->post('libilityGlCode');

        $this->db->where('bankFacilityID', $this->input->post('masterID'));
        $this->db->update('srp_erp_bankfacilityloan', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Saved Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function open_receipt_voucher_modal(){
        $bankFacilityID = trim($this->input->post('bankFacilityID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_bankfacilityloan');
        $this->db->where('bankFacilityID', $bankFacilityID);
        $data['master']=$this->db->get()->row_array();

        $this->db->select("GLAutoID");
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('isDefaultlBank', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['GL']=$this->db->get()->row_array();

        $RVdate = current_date();
        $currencyID = $data['master']['currencyID'];

        return $data;
    }

    function save_payment_voucher_LO_settlement(){
        $this->db->trans_start();
        $bankFacilityDetailID = trim($this->input->post('bankFacilityDetailID') ?? '');
        $val=$this->db->query("SELECT isSettlement,variableLibor,bankFacilityID, variableAmount, variableTotal, installmentDueDays, closingBalance, principalRepayment, bankFacilityDetailID, DATE_FORMAT(date, '%d-%m-%Y') AS date, referenceNo, principleAmount, interestAmount, systemDocumentReference from srp_erp_bankfacilityloandetail WHERE bankFacilityDetailID= {$bankFacilityDetailID} ")->row_array();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $val['date'];
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->input->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $accountPayeeOnly = 0;
        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }
        $voucherType = $this->input->post('pvtype');


            $this->db->select('*');
            $this->db->from('srp_erp_bankfacilityloan');
            $this->db->where('bankFacilityID', $val['bankFacilityID']);
            $loan_master=$this->db->get()->row_array();

            $this->db->select('transactionCurrency, customerExchangeRate, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
            $this->db->where('receiptVoucherAutoId', $loan_master['receiptVoucherID']);
            $receipt_master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();


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
     
            $chequeRegister = getPolicyValues('CRE', 'All');

            $this->db->select('*');
            $this->db->where('segmentID',$receipt_master['segmentID']);
            $segment = $this->db->get('srp_erp_segment')->row_array();

            $this->db->select('*');
            $this->db->where('currencyID',$loan_master['currencyID']);
            $currency_code = $this->db->get('srp_erp_currencymaster')->row_array();

            $data['PVbankCode'] = $loan_master['bankID'];
            $bank_detail = fetch_gl_account_desc($loan_master['bankID']);
            $data['documentID'] = 'PV';
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        
            $data['PVdate'] = $PVdate;

            $narration = $val['systemDocumentReference'];
            $data['PVNarration'] = str_replace('<br />', PHP_EOL, $val['systemDocumentReference'] ?? '');
            $data['accountPayeeOnly'] = $accountPayeeOnly;
            $data['segmentID'] = $segment['segmentID'];
            $data['segmentCode'] = $segment['segmentCode'];
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
            $data['paymentType'] = 2;
           // $data['supplierBankMasterID'] = $this->input->post('supplierBankMasterID');
            if($PVcheqDate == null)
            {
                $data['PVchequeDate'] = null;
            }
            if ($bank_detail['isCash'] == 1) {
                $data['PVchequeNo'] = null;
                $data['chequeRegisterDetailID'] = null;
                $data['PVchequeDate'] = null;
            } 
          
            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
            $data['pvType'] = 'DirectItem';
         
            $data['referenceNo'] = $loan_master['facilityCode'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
           

            $data['transactionCurrencyID'] = $loan_master['currencyID'];
            $data['transactionCurrency'] = $currency_code['CurrencyCode'];
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

          
            $data['partyType'] = 'DIR';
            $data['partyName'] = $bank_detail['bankName'];
            $data['partyCurrencyID'] = $data['companyLocalCurrencyID'];
            $data['partyCurrency'] = $data['companyLocalCurrency'];
            $data['partyExchangeRate'] = $data['transactionExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
           
            $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
            $data['partyExchangeRate'] = $partyCurrency['conversion'];
            $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];


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

            if($last_id){

                $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
                $this->db->where('payVoucherAutoId', $last_id);
                $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

                if($val['principalRepayment']!=0){
                    $data_p['payVoucherAutoId'] = $last_id;
                
                    $gl_code = fetch_gl_account_desc($loan_master['principalGlCode']);
                    $data_p['systemGLCode'] = trim($gl_code['systemAccountCode'] ?? '');
                    $data_p['GLCode'] = trim($gl_code['GLSecondaryCode'] ?? '');
                    $data_p['GLDescription'] = trim($gl_code['GLDescription'] ?? '');
                    $data_p['GLType'] = trim($gl_code['subCategory'] ?? '');
                    $data_p['GLAutoID'] = $loan_master['principalGlCode'];

                    $data_p['segmentID'] = $segment['segmentID'];
                    $data_p['segmentCode'] = $segment['segmentCode'];
                    $data_p['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $data_p['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $data_p['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $data_p['transactionAmount'] = $val['principalRepayment'];
                    $data_p['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $data_p['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $data_p['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $data_p['companyLocalAmount'] = ($data_p['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                    $data_p['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $data_p['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $data_p['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                    $data_p['companyReportingAmount'] = ($data_p['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                    $data_p['partyCurrency'] = $master_recode['partyCurrency'];
                    $data_p['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                    $data_p['partyAmount'] = ($data_p['transactionAmount'] / $master_recode['partyExchangeRate']);
                    $data_p['description'] = '';
                    $data_p['type'] = 'GL';
                    $data_p['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_p['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_p['modifiedUserName'] = $this->common_data['current_user'];
                    $data_p['modifiedDateTime'] = $this->common_data['current_date'];

                    $data_p['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_p['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_p['createdUserGroup'] = $this->common_data['user_group'];
                    $data_p['createdPCID'] = $this->common_data['current_pc'];
                    $data_p['createdUserID'] = $this->common_data['current_userID'];
                    $data_p['createdUserName'] = $this->common_data['current_user'];
                    $data_p['createdDateTime'] = $this->common_data['current_date'];
                
                    $this->db->insert('srp_erp_paymentvoucherdetail', $data_p);

                }

                if($val['interestAmount']!=0){
                    $data_interest['payVoucherAutoId'] = $last_id;
                
                    $gl_code1 = fetch_gl_account_desc($loan_master['interestGlCode']);
                    $data_interest['systemGLCode'] = trim($gl_code1['systemAccountCode'] ?? '');
                    $data_interest['GLCode'] = trim($gl_code1['GLSecondaryCode'] ?? '');
                    $data_interest['GLDescription'] = trim($gl_code1['GLDescription'] ?? '');
                    $data_interest['GLType'] = trim($gl_code1['subCategory'] ?? '');
                    $data_interest['GLAutoID'] = $loan_master['interestGlCode'];

                    $data_interest['segmentID'] = $segment['segmentID'];
                    $data_interest['segmentCode'] = $segment['segmentCode'];
                    $data_interest['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $data_interest['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $data_interest['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $data_interest['transactionAmount'] = $val['interestAmount'];
                    $data_interest['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $data_interest['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $data_interest['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $data_interest['companyLocalAmount'] = ($data_interest['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                    $data_interest['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $data_interest['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $data_interest['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                    $data_interest['companyReportingAmount'] = ($data_interest['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                    $data_interest['partyCurrency'] = $master_recode['partyCurrency'];
                    $data_interest['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                    $data_interest['partyAmount'] = ($data_interest['transactionAmount'] / $master_recode['partyExchangeRate']);
                    $data_interest['description'] = '';
                    $data_interest['type'] = 'GL';
                    $data_interest['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_interest['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_interest['modifiedUserName'] = $this->common_data['current_user'];
                    $data_interest['modifiedDateTime'] = $this->common_data['current_date'];

                    $data_interest['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_interest['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_interest['createdUserGroup'] = $this->common_data['user_group'];
                    $data_interest['createdPCID'] = $this->common_data['current_pc'];
                    $data_interest['createdUserID'] = $this->common_data['current_userID'];
                    $data_interest['createdUserName'] = $this->common_data['current_user'];
                    $data_interest['createdDateTime'] = $this->common_data['current_date'];
                
                    $this->db->insert('srp_erp_paymentvoucherdetail', $data_interest);

                }

                if($val['variableAmount']!=0){
                    $data_variable['payVoucherAutoId'] = $last_id;
                
                    $gl_code2 = fetch_gl_account_desc($loan_master['libilityGlCode']);
                    $data_variable['systemGLCode'] = trim($gl_code2['systemAccountCode'] ?? '');
                    $data_variable['GLCode'] = trim($gl_code2['GLSecondaryCode'] ?? '');
                    $data_variable['GLDescription'] = trim($gl_code2['GLDescription'] ?? '');
                    $data_variable['GLType'] = trim($gl_code2['subCategory'] ?? '');
                    $data_variable['GLAutoID'] = $loan_master['libilityGlCode'];

                    $data_variable['segmentID'] = $segment['segmentID'];
                    $data_variable['segmentCode'] = $segment['segmentCode'];
                    $data_variable['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                    $data_variable['transactionCurrency'] = $master_recode['transactionCurrency'];
                    $data_variable['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
                    $data_variable['transactionAmount'] = $val['variableAmount'];
                    $data_variable['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                    $data_variable['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                    $data_variable['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                    $data_variable['companyLocalAmount'] = ($data_variable['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                    $data_variable['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                    $data_variable['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                    $data_variable['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                    $data_variable['companyReportingAmount'] = ($data_variable['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                    $data_variable['partyCurrency'] = $master_recode['partyCurrency'];
                    $data_variable['partyExchangeRate'] = $master_recode['partyExchangeRate'];
                    $data_variable['partyAmount'] = ($data_variable['transactionAmount'] / $master_recode['partyExchangeRate']);
                    $data_variable['description'] = '';
                    $data_variable['type'] = 'GL';
                    $data_variable['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_variable['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_variable['modifiedUserName'] = $this->common_data['current_user'];
                    $data_variable['modifiedDateTime'] = $this->common_data['current_date'];

                    $data_variable['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_variable['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_variable['createdUserGroup'] = $this->common_data['user_group'];
                    $data_variable['createdPCID'] = $this->common_data['current_pc'];
                    $data_variable['createdUserID'] = $this->common_data['current_userID'];
                    $data_variable['createdUserName'] = $this->common_data['current_user'];
                    $data_variable['createdDateTime'] = $this->common_data['current_date'];
                
                    $this->db->insert('srp_erp_paymentvoucherdetail', $data_variable);

                }

            }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher   Saved Failed','');
        } else {
            $data_loan['paymentVoucherYN']=1;
            $data_loan['paymentVoucherID']=$last_id;
            $this->db->where('bankFacilityDetailID', $bankFacilityDetailID);
            $this->db->update('srp_erp_bankfacilityloandetail', $data_loan);

            $this->db->trans_commit();
            return array('s', 'Payment Voucher Saved Successfully.',$last_id);
        }
    }

    function save_receiptvoucher_from_LO_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        $adjusted_bank_exchange_rate = $this->input->post('bank_currency_exchange_rate');

        $bankFacilityID=$this->input->post('bankFacilityID');

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

        $segarray = explode('|', trim($this->input->post('segment') ?? ''));

        $this->db->select('*');
        $this->db->where('segmentID',$segarray[0]);
        $segment = $this->db->get('srp_erp_segment')->row_array();

        $this->db->select('*');
        $this->db->where('currencyID',$this->input->post('transactionCurrencyID'));
        $currency_code = $this->db->get('srp_erp_currencymaster')->row_array();

        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('bankID') ?? ''));
        $data['documentID'] = 'RV';
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
       
        $data['RVdate'] = trim($RVdate);
        $narration= ($this->input->post('RVNarration'));
        $data['RVNarration'] = str_replace('<br />', PHP_EOL, $narration ?? '');
        $data['segmentID'] = $segment['segmentID'];
        $data['segmentCode'] =  $segment['segmentCode'];
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['bankSystemAccountCode'] = $bank_detail['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $bank_detail['GLSecondaryCode'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $data['RVbank'] = $bank_detail['bankName'];
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        $data['RVbankBranch'] = $bank_detail['bankBranch'];
        $data['RVbankSwiftCode'] = $bank_detail['bankSwiftCode'];
        $data['RVbankAccount'] = $bank_detail['bankAccountNumber'];
        $data['RVbankType'] = $bank_detail['subCategory'];
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['RVchequeNo'] = trim($this->input->post('RVchequeNo') ?? '');
        if ($bank_detail['isCash'] == 0) {
            $data['paymentType'] = 2;
            $data['RVchequeDate'] = null;
        } else {
            $data['RVchequeDate'] = null;
        }
        $data['RvType'] = trim($this->input->post('vouchertype') ?? '');
        $data['referanceNo'] = trim_desc($this->input->post('referenceno'));
        $data['RVbankCode'] =  $bank_detail['GLAutoID'];

     
        $data['customerName'] = $bank_detail['bankName'];
        $data['customerID'] = '';
        $data['customerAddress'] = '';
        $data['customerTelephone'] = '';
        $data['customerFax'] = '';
        $data['customerEmail'] = '';
        $data['customerCurrency'] = $currency_code['CurrencyCode'];
        $data['customerCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['customerCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['customerCurrencyID']);
       
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = $currency_code['CurrencyCode'];
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
        
  
        $isGroupBasedTax = getPolicyValues('GBT', 'All');
        if($isGroupBasedTax && $isGroupBasedTax == 1) {
            $data['isGroupBasedTax'] = 1;
        }
            

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

            if($last_id){
                $this->db->trans_start();
                $this->db->select('transactionCurrency, customerExchangeRate, transactionExchangeRate, companyLocalCurrency,companyLocalCurrencyID, companyReportingCurrency, companyReportingCurrencyID, companyLocalExchangeRate,companyReportingExchangeRate,transactionCurrency,segmentID,segmentCode,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,transactionCurrencyID');
                $this->db->where('receiptVoucherAutoId', $last_id);
                $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();

                $projectExist = project_is_exist();

                $this->db->select('*');
                $this->db->from('srp_erp_bankfacilityloan');
                $this->db->where('bankFacilityID', $bankFacilityID);
                $invAmount=$this->db->get()->row_array();

                $this->db->select('*');
                $this->db->from('srp_erp_chartofaccounts');
                $this->db->where('GLAutoID', $invAmount['principalGlCode']);
                $gl=$this->db->get()->row_array();

                    $dataD['receiptVoucherAutoId'] = $last_id;

                    $dataD['GLAutoID'] = $invAmount['principalGlCode'];
                    $dataD['systemGLCode'] = $gl['systemAccountCode'];
                    $dataD['GLCode'] = $gl['GLSecondaryCode'];
                    $dataD['GLDescription'] = $gl['GLDescription'];
                    $dataD['GLType'] = $gl['subCategory'];
                   
                    $dataD['segmentID'] = $segment['segmentID'];
                    $dataD['segmentCode'] =  $segment['segmentCode'];
                    $dataD['transactionAmount'] = trim($invAmount['amount'] ?? '');
                    $dataD['companyLocalAmount'] = ($dataD['transactionAmount'] / $master['companyLocalExchangeRate']);
                    $dataD['companyReportingAmount'] = ($dataD['transactionAmount'] / $master['companyReportingExchangeRate']);

                    $dataD['customerAmount'] = 0;
                   
                    $dataD['type'] = 'GL';
                    $dataD['modifiedPCID'] = $this->common_data['current_pc'];
                    $dataD['modifiedUserID'] = $this->common_data['current_userID'];
                    $dataD['modifiedUserName'] = $this->common_data['current_user'];
                    $dataD['modifiedDateTime'] = $this->common_data['current_date'];

                    
                    $dataD['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataD['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataD['createdUserGroup'] = $this->common_data['user_group'];
                    $dataD['createdPCID'] = $this->common_data['current_pc'];
                    $dataD['createdUserID'] = $this->common_data['current_userID'];
                    $dataD['createdUserName'] = $this->common_data['current_user'];
                    $dataD['createdDateTime'] = $this->common_data['current_date'];

                    

                    $this->db->insert('srp_erp_customerreceiptdetail', $dataD);
                   // $last_id1 = $this->db->insert_id();

            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Receipt Voucher   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('e', 'Receipt Voucher   Saved Failed.','');
            } else {
                $data_loan['receiptVoucherYN']=1;
                $data_loan['receiptVoucherID']=$last_id;
                $this->db->where('bankFacilityID', $bankFacilityID);
                $this->db->update('srp_erp_bankfacilityloan', $data_loan);

                $this->session->set_flashdata('s', 'Receipt Voucher Saved Successfully.');
                $this->db->trans_commit();
                return array('s', 'Receipt Voucher Saved Successfully.',$last_id);
            }
        
    }

    function save_bank_rec_details($clearedYN, $bankRecAutoID)
    {
        $datas = array("bankRecMonthID" => NULL, "clearedBy" => NULL, "clearedAmount" => 0, "clearedDate" => NULL, "clearedYN" => 0, "clearedBy" => NULL);
        $this->db->update('srp_erp_bankledger', $datas, array('bankRecMonthID' => $bankRecAutoID));
        $current_user = $this->common_data['current_user'];
        $current_date = current_date();
        $data = array();
        if (!empty($clearedYN)) {
            $commaList = implode(', ', $clearedYN);
            $bankAmount = $this->db->query("select bankLedgerAutoID,bankCurrencyAmount from srp_erp_bankledger where bankLedgerAutoID IN ($commaList); ")->result_array();
            for ($i = 0; $i < count($clearedYN); $i++) {
                $key = array_search($clearedYN[$i], array_column($bankAmount, 'bankLedgerAutoID'));
                if (array_key_exists($key, $bankAmount)) {
                    $Amount = !empty($bankAmount[$key]["bankCurrencyAmount"]) ? $bankAmount[$key]["bankCurrencyAmount"] : 0;
                    array_push($data, array("bankLedgerAutoID" => $clearedYN[$i], "bankrecmonthID" => $bankRecAutoID, "clearedYN" => 1, "clearedDate" => $current_date, "clearedBy" => $current_user, "clearedAmount" => $Amount));
                }
            }
        }
        if (!empty($data)) {
            $update = $this->db->update_batch('srp_erp_bankledger', $data, 'bankLedgerAutoID');
        }
        if ($update) {
            $this->session->set_flashdata('s', 'Bank Reconciliation : Draft Successfully.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Bank Reconciliation : Updated failed .');
            return true;
        }
    }

    function getGLdetails($GLAutoID)
    {
        $data = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE GLAutoID={$GLAutoID}")->row_array();
        return $data;

    }


    function save_bank_rec_approval()
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('bankRecAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'BRC');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Bank Rec Approval Successfully.');
            return true;
        }
    }

    function xeditable_update($tableName, $pkColumn)
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');


        $table = $tableName;
        $data = array($column => $value);
        $this->db->where($pkColumn, $pk);
        $result = $this->db->update($table, $data);
        echo $this->db->last_query();
        return $result;
    }

    function get_glcode_currency($GLAutoID)
    {
        $data = $this->db->query("SELECT bankCurrencyID,CurrencyCode as bankCurrencyCode,bankCurrencyDecimalplaces FROM `srp_erp_chartofaccounts` LEFT JOIN  `srp_erp_currencymaster` on bankCurrencyID=currencyID WHERE  GLAutoID={$GLAutoID}")->row_array();
        return $data;
    }

    function getexchangerate($masterCurrencyID, $subCurrencyID)
    {
        $companyID = current_companyID();
        $data = $this->db->query("select masterCurrencyCode,subCurrencyCode,conversion from srp_erp_companycurrencyconversion where masterCurrencyID='{$subCurrencyID}' and subCurrencyID='{$masterCurrencyID}' AND companyID=$companyID")->row_array();
        return $data;
    }

    function bank_transfer_master($bankTransferAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("SELECT srp_erp_banktransfer.confirmedbyName, DATE_FORMAT( srp_erp_banktransfer.confirmedDate, '%d-%m-%Y %h:%i:%s'  ) as confirmedDate, 
                DATE_FORMAT(srp_erp_banktransfer.createdDateTime,'.$convertFormat.') AS createdDateTime, fromCurrency.bankCurrencyID as fromcurrencyID,transferedDate as transferedDatebank,chequeNo,chequeDate as chequeDat,
                toCurrency.bankCurrencyID as tocurrencyID, srp_erp_banktransfer.*,DATE_FORMAT(transferedDate,'$convertFormat') AS transferedDate ,
                DATE_FORMAT(srp_erp_banktransfer.approvedDate,'.$convertFormat. %h:%i:%s') AS approvedDate, fromCurrency.bankCurrencyCode as fromcurrency, 
                toCurrency.bankCurrencyCode as tocurrency, fromCurrency.GLDescription as bankfrom, toCurrency.GLDescription as bankto,
                fromCurrency.bankCurrencyDecimalPlaces AS fromDecimalPlaces, toCurrency.bankCurrencyDecimalPlaces AS toDecimalPlaces,
                fromCurrency.systemAccountCode as fromSystemAccountCode,fromCurrency.GLSecondaryCode as fromGLSecondaryCode, toCurrency.systemAccountCode as toCurrencySystemAccountCode,
                toCurrency.GLSecondaryCode as toCurrencyGLSecondaryCode, fromCurrency.GLDescription as fromGLDescription, fromCurrency.subCategory as fromSubCategory, 
                toCurrency.GLDescription as toGLDescription, toCurrency.subCategory as toSubCategory,srp_erp_currencymaster.CurrencyCode as CurrencyCode,
                DATE_FORMAT(chequeDate,'$convertFormat') AS chequeDate,
                CASE WHEN srp_erp_banktransfer.confirmedYN = 2 || srp_erp_banktransfer.confirmedYN = 3 THEN \" - \" 
                    WHEN srp_erp_banktransfer.confirmedYN = 1 
                        THEN CONCAT_WS(' on ',IF( LENGTH( srp_erp_banktransfer.confirmedbyName ), srp_erp_banktransfer.confirmedbyName, '-' ),IF(LENGTH( DATE_FORMAT( srp_erp_banktransfer.confirmedDate, '%d-%m-%Y %h:%i:%s'  ) ),DATE_FORMAT( srp_erp_banktransfer.confirmedDate, '%d-%m-%Y %h:%i:%s'  ),NULL ) )
                    ELSE \"-\"
                END confirmedYNn,	
    	        srp_erp_currencymaster.DecimalPlaces AS fromDecimalPlaces,CASE

	WHEN srp_erp_banktransfer.transferType = 1 THEN
	\"Bank Transfer \"
	WHEN srp_erp_banktransfer.transferType = 2 THEN
	\"Cheque\"

	END transferTypebankcheq FROM srp_erp_banktransfer LEFT JOIN srp_erp_chartofaccounts AS fromCurrency ON fromBankGLAutoID = fromCurrency.GLAutoID LEFT JOIN srp_erp_chartofaccounts AS toCurrency ON toBankGLAutoID = toCurrency.GLAutoID LEFT JOIN srp_erp_currencymaster  ON fromBankCurrencyID = srp_erp_currencymaster.currencyID WHERE bankTransferAutoID = {$bankTransferAutoID}")->row_array();
        return $data;
    }

    function bank_transfer_confirmation()
    {

        $bankTransferAutoID = $this->input->post('bankTransferAutoID');
        $this->load->library('Approvals');
        $master = $this->bank_transfer_master($bankTransferAutoID);

        $validate_code = validate_code_duplication($master['bankTransferCode'], 'bankTransferCode', $bankTransferAutoID,'bankTransferAutoID', 'srp_erp_banktransfer');
        if(!empty($validate_code)) {
            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
            return false;
        }

        $autoApproval= get_document_auto_approval('BT');
        if($autoApproval==0){
            $approvals_status = $this->approvals->auto_approve($bankTransferAutoID, 'srp_erp_banktransfer','bankTransferAutoID', 'BT',$master['bankTransferCode'],$master['transferedDatebank']);
        }elseif($autoApproval==1){
            $approvals_status = $this->approvals->CreateApproval('BT', $bankTransferAutoID, $master['bankTransferCode'], 'Bank Transfer', 'srp_erp_banktransfer', 'bankTransferAutoID',0,$master['transferedDatebank']);
        }else{
            $this->session->set_flashdata('e', 'Approval levels are not set for this document.');
            return false;
        }

        if ($approvals_status==1) {
            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user']
            );
            $this->db->where('bankTransferAutoID', $bankTransferAutoID);
            $this->db->update('srp_erp_banktransfer', $data);

            $autoApproval= get_document_auto_approval('BT');

            if($autoApproval==0) {
                $result = $this->confirm_bank_approval(0, $bankTransferAutoID, 1, 'Auto Approved');
                if($result){
                    $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                    return true;
                  /*  return array('error' => 0, 'message' => 'Document confirmed successfully ');*/
                }
            }else{
                $this->session->set_flashdata('s', 'Approvals Created Successfully.');
                return true;
                /*return array('error' => 0, 'message' => 'Document confirmed successfully ');*/
            }
        } else if($approvals_status==3){
            $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return false;
        }
    }

    function confirm_bank_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $companyID = current_companyID();

        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('bankTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else
        {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['bankTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;


        }
        if ($autoappLevel == 0) {

            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'BT');
        }

        if ($approvals_status == 1) {

            $master = $this->bank_transfer_master($system_code);
            $date_format_policy = date_format_policy();
            $transferedDate = $master['transferedDate'];
            $master['transferedDate'] = input_format_date($transferedDate, $date_format_policy);
            $data['exchange'] = 1 / $master['exchangeRate'];
            $data = array(array('companyID' => $master['companyID'],
                'companyCode' => $master['companyCode'],
                'documentDate' => $master['transferedDate'],
                'transactionType' => 2,
                'documentType' => 'BT',
                'chequeNo' => $master['chequeNo'],
                'chequeDate' => $master['chequeDat'],
                'transactionCurrencyID' => $master['fromBankCurrencyID'],
                'transactionCurrency' => $master['fromcurrency'],
                'transactionExchangeRate' => 1,
                'transactionAmount' => $master['transferedAmount'],
                'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'bankCurrencyID' => $master['fromBankCurrencyID'],
                'bankCurrency' => $master['fromcurrency'],
                'bankCurrencyExchangeRate' => 1,
                'bankCurrencyAmount' => $master['transferedAmount'],
                'bankCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'memo' => $master['narration'],
                'bankName' => $master['bankfrom'],
                'bankGLAutoID' => $master['fromBankGLAutoID'],
                'bankSystemAccountCode' => $master['fromSystemAccountCode'],
                'bankGLSecondaryCode' => $master['fromGLSecondaryCode'],
                'documentMasterAutoID' => $master['bankTransferAutoID'],
                'documentSystemCode' => $master['bankTransferCode'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdDateTime' => $this->common_data['current_date'],
                'createdUserName' => $this->common_data['current_user']),

                array('companyID' => $master['companyID'],
                    'companyCode' => $master['companyCode'],
                    'documentDate' => $master['transferedDate'],
                    'transactionType' => 1,
                    'documentType' => 'BT',
                    'chequeNo' => $master['chequeNo'],
                    'chequeDate' => $master['chequeDat'],
                    'transactionCurrencyID' => $master['fromBankCurrencyID'],
                    'transactionCurrency' => $master['fromcurrency'],
                    'transactionExchangeRate' => 1,
                    'transactionAmount' => $master['transferedAmount'],
                    'transactionCurrencyDecimalPlaces' => $master['toDecimalPlaces'],
                    'bankCurrencyID' => $master['toBankCurrencyID'],
                    'bankCurrency' => $master['tocurrency'],
                    'bankCurrencyExchangeRate' => $data['exchange'],
                    'bankCurrencyAmount' => $master['transferedAmount'] * $master['exchangeRate'],
                    'bankCurrencyDecimalPlaces' => $master['toDecimalPlaces'],
                    'memo' => $master['narration'],
                    'bankName' => $master['bankto'],
                    'bankGLAutoID' => $master['toBankGLAutoID'],
                    'bankSystemAccountCode' => $master['toCurrencySystemAccountCode'],
                    'bankGLSecondaryCode' => $master['toCurrencyGLSecondaryCode'],
                    'documentMasterAutoID' => $master['bankTransferAutoID'],
                    'documentSystemCode' => $master['bankTransferCode'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdDateTime' => $this->common_data['current_date'],
                    'createdUserName' => $this->common_data['current_user']));
            $transferedDate = format_date($master['transferedDate']);
            $orderdate = explode('-', $transferedDate);
            $month = $orderdate[1];
            $year = $orderdate[0];
            $localdecimal = fetch_currency_desimal($master['companyLocalCurrency']);
            $reportingdecimal = fetch_currency_desimal($master['companyReportingCurrency']);
            //echo '<pre>';print_r($master); echo '</pre>'; die();
            /*localexchange*/
            /*if ($master['fromCurrency'] == $master['companyLocalCurrencyID']) {
              $companyLocalExchangeRate = 1 / $master['exchangeRate'];
            } else {
                $default_currency = currency_conversionID($master['fromCurrency'], $master['companyLocalCurrencyID']);
             $companyLocalExchangeRate = $default_currency['conversion'];
            }*/
            if ($master['companyLocalCurrencyID'] == $master['tocurrencyID']) {
                $companyLocalExchangeRate = 1 / $master['exchangeRate'];
            } else {
                $companyLocalExchangeRate = $master['companyLocalExchangeRate'];
            }

            if ($master['companyReportingCurrencyID'] == $master['tocurrencyID']) {
                $companyReportingexchangeRate = 1 / $master['exchangeRate'];
            } else {
                $companyReportingexchangeRate = $master['companyReportingExchangeRate'];
            }

            /*reporting Exchange*/
            /*if ($master['fromCurrency'] == $master['companyReportingCurrencyID']) {
                $companyReportingexchangeRate = 1 / $master['exchangeRate'];
            } else {
                $report = currency_conversionID($master['fromcurrencyID'], $master['companyReportingCurrencyID']);
                $companyReportingexchangeRate = $report['conversion'];
            }*/

            $data2 = array(array('documentCode' => 'BT',
                'documentMasterAutoID' => $master['bankTransferAutoID'],
                'documentSystemCode' => $master['bankTransferCode'],
                'documentType' => 'BT',
                'documentDate' => $master['transferedDate'],
                'documentYear' => $year,
                'documentMonth' => $month,
                'documentNarration' => $master['narration'],
                'GLAutoID' => $master['fromBankGLAutoID'],
                'systemGLCode' => $master['fromSystemAccountCode'],
                'GLCode' => $master['fromGLSecondaryCode'],
                'GLDescription' => $master['fromGLDescription'],
                'GLType' => $master['fromSubCategory'],
                'amount_type' => 'cr',
                'transactionCurrencyID' => $master['fromBankCurrencyID'],
                'transactionCurrency' => $master['fromcurrency'],
                'transactionExchangeRate' => 1,
                'transactionAmount' => -1 * abs($master['transferedAmount']),
                'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                'companyLocalCurrencyID' => $master['companyLocalCurrencyID'],
                'companyLocalCurrency' => $master['companyLocalCurrency'],
                'companyLocalExchangeRate' => $companyLocalExchangeRate,
                'companyLocalAmount' => -1 * abs($master['transferedAmount'] / $companyLocalExchangeRate),
                'companyLocalCurrencyDecimalPlaces' => $localdecimal,
                'companyReportingCurrencyID' => $master['companyReportingCurrencyID'],
                'companyReportingCurrency' => $master['companyReportingCurrency'],
                'companyReportingExchangeRate' => $companyReportingexchangeRate,
                'companyReportingAmount' => -1 * abs($master['transferedAmount'] / $companyReportingexchangeRate),
                'companyReportingCurrencyDecimalPlaces' => $reportingdecimal,
                'confirmedByEmpID' => $master['confirmedByEmpID'],
                'confirmedByName' => $master['confirmedByName'],
                'confirmedDate' => $master['confirmedDate'],
                'approvedDate' => $this->common_data['current_date'],
                'approvedbyEmpID' => $this->common_data['current_userID'],
                'approvedbyEmpName' => $this->common_data['current_user'],
                'companyID' => $master['companyID'],
                'companyCode' => $master['companyCode'],
                'createdUserGroup' => $this->common_data['user_group'],
                'createdPCID' => $this->common_data['current_pc'],
                'createdUserID' => $this->common_data['current_userID'],
                'createdDateTime' => $this->common_data['current_date'],
                'createdUserName' => $this->common_data['current_user']),

                array('documentCode' => 'BT',
                    'documentMasterAutoID' => $master['bankTransferAutoID'],
                    'documentSystemCode' => $master['bankTransferCode'],
                    'documentType' => 'BT',
                    'documentDate' => $master['transferedDate'],
                    'documentYear' => $year,
                    'documentMonth' => $month,
                    'documentNarration' => $master['narration'],
                    'GLAutoID' => $master['toBankGLAutoID'],
                    'systemGLCode' => $master['toCurrencySystemAccountCode'],
                    'GLCode' => $master['toCurrencyGLSecondaryCode'],
                    'GLDescription' => $master['toGLDescription'],
                    'GLType' => $master['toSubCategory'],
                    'amount_type' => 'dr',
                    'transactionCurrencyID' => $master['fromBankCurrencyID'],
                    'transactionCurrency' => $master['fromcurrency'],
                    'transactionExchangeRate' => 1,
                    'transactionAmount' => $master['transferedAmount'],
                    'transactionCurrencyDecimalPlaces' => $master['fromDecimalPlaces'],
                    'companyLocalCurrencyID' => $master['companyLocalCurrencyID'],
                    'companyLocalCurrency' => $master['companyLocalCurrency'],
                    'companyLocalExchangeRate' => $companyLocalExchangeRate,
                    'companyLocalAmount' => $master['transferedAmount'] / $companyLocalExchangeRate,
                    'companyLocalCurrencyDecimalPlaces' => $localdecimal,
                    'companyReportingCurrencyID' => $master['companyReportingCurrencyID'],
                    'companyReportingCurrency' => $master['companyReportingCurrency'],
                    'companyReportingExchangeRate' => $companyReportingexchangeRate,
                    'companyReportingAmount' => $master['transferedAmount'] / $companyReportingexchangeRate,
                    'companyReportingCurrencyDecimalPlaces' => $reportingdecimal,
                    'confirmedByEmpID' => $master['confirmedByEmpID'],
                    'confirmedByName' => $master['confirmedByName'],
                    'confirmedDate' => $master['confirmedDate'],
                    'approvedDate' => $this->common_data['current_date'],
                    'approvedbyEmpID' => $this->common_data['current_userID'],
                    'approvedbyEmpName' => $this->common_data['current_user'],
                    'companyID' => $master['companyID'],
                    'companyCode' => $master['companyCode'],
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdDateTime' => $this->common_data['current_date'],
                    'createdUserName' => $this->common_data['current_user']));

            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'BT');

            $levelNo = $this->db->query("select max(levelNo) as levelNo from srp_erp_approvalusers WHERE Status=1 AND companyID={$companyID} AND documentID='BT'  ")->row_array();
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            
            if ($status == 1) {
                if($levelNo['levelNo']==$level_id){
                $this->db->insert_batch('srp_erp_bankledger', $data);
                $this->db->insert_batch('srp_erp_generalledger', $data2);
                }
            }
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Bank Transfer Approval Successfully.');
            return true;
        }
    }

    function get_bank_ledger_details($bankGLAutoID, $from, $to)
    {

        $filter = "";
        if ($from != 'false') {
            $filter .= ' AND documentDate >="' . $from . '"';

            $qry2 = "SELECT sum(IF(transactionType = 1, bankCurrencyAmount, 0)) as bankCurrencyAmount, sum(IF(transactionType = 2, bankCurrencyAmount*-1, 0)) as deduct, sum(IF(transactionType = 1, bankCurrencyAmount, 0))+ sum(IF(transactionType = 2, bankCurrencyAmount*-1, 0)) as total, documentDate, memo, chequeNo, documentSystemCode, transactionType, partyType, partyCode, partyName, bankCurrency, bankCurrencyDecimalPlaces, bankCurrencyAmount AS amount, FORMAT(bankCurrencyAmount, bankCurrencyDecimalPlaces) AS bankCurrencyAmount, IF(m.bankRecMonthID != '', clearedYN, 0) AS clearedYN, m.bankRecMonthID FROM srp_erp_bankledger m WHERE m.bankGLAutoID = {$bankGLAutoID} AND documentDate < '{$from}' group by bankGLAutoID ORDER BY documentDate ASC";
            $openingbalance = $this->db->query($qry2)->row_array();
        }
        if ($to != 'false') {
            $filter .= ' AND documentDate <="' . $to . '"';
        }
        $qry = "SELECT documentDate,memo,chequeNo, documentSystemCode, transactionType, partyType, partyCode, partyName, bankCurrency,bankCurrencyDecimalPlaces, bankCurrencyAmount as amount, FORMAT(bankCurrencyAmount, bankCurrencyDecimalPlaces) as bankCurrencyAmount, IF(m.bankRecMonthID !='',clearedYN,0) as clearedYN, m.bankRecMonthID FROM srp_erp_bankledger m WHERE m.bankGLAutoID = {$bankGLAutoID} {$filter} order by documentDate asc";
        $data = $this->db->query($qry)->result_array();


        return json_encode(array('data' => $data, 'openingbalance' => $openingbalance));


        /*return $data;*/
    }

    function bankrec_recieved_account()
    {
        $this->db->trans_begin();
        $bankGLAutoID = $this->input->post('bankGLAutoID');
        $date_format_policy = date_format_policy();
        $docutDate = $this->input->post('documentDate');
        $documentDate = input_format_date($docutDate, $date_format_policy);
        $bankAccountID = $this->input->post('bankAccountID');
        $type1 = $this->input->post('type');
        $refNo = $this->input->post('reference');
        $narration = $this->input->post('narration');
        $amount = $this->input->post('amount');
        $GLAutoID = $this->input->post('GLAutoID');

        $seg = explode('|', $this->input->post('segmentID'));
        $segmentID = $seg[0];
        $segmentCode = $seg[1];

        $documentType = '';
        if ($type1 == 1) {
            $documentType = 'RV';
        }
        if ($type1 == 2) {
            $documentType = 'PV';
        }
        $gl2 = $this->getGLdetails($bankAccountID);
        /*payment voucher*/
        $companyid=current_companyID();
        $FY = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod INNER JOIN srp_erp_companyfinanceyear ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID WHERE dateFrom <= '{$documentDate}' AND dateTo >= '{$documentDate}' AND srp_erp_companyfinanceperiod.companyID = $companyid AND srp_erp_companyfinanceperiod.isActive = 1 AND srp_erp_companyfinanceyear.isActive = 1")->row_array();
        if (empty($FY)) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Active finance period not found for selected document date.');
            echo json_encode(false);
            EXIT;
        }
        $gl = $this->getGLdetails($GLAutoID);
        $type = substr($documentType, 0, 3);

        $data['documentID'] = $documentType;
        $data['RVdate'] = $documentDate;
        $data['RVType'] = 'Direct';
        $data['referanceNo'] = $refNo;
        $data['companyFinanceYearID'] = $FY['companyFinanceYearID'];
        $data['FYBegin'] = $FY['beginingDate'];
        $data['FYEnd'] = $FY['endingDate'];
        $data['FYPeriodDateFrom'] = $FY['dateFrom'];
        $data['FYPeriodDateTo'] = $FY['dateTo'];
        $data['RVbankCode'] = $gl['bankShortCode'];
        $data['bankGLAutoID'] = $gl['GLAutoID'];
        $data['bankSystemAccountCode'] = $gl['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $gl['GLSecondaryCode'];
        $data['RVbank'] = $gl['bankName'];
        $data['RVbankBranch'] = $gl['bankBranch'];
        $data['RVbankSwiftCode'] = $gl['bankSwiftCode'];
        $data['RVbankAccount'] = $gl['bankAccountNumber'];
        $data['RVbankType'] = $gl['subCategory'];
        $data['RVNarration'] = $narration;
        $data['confirmedYN'] = 1;
        $data['confirmedByEmpID'] = $this->common_data['current_user'];
        $data['confirmedByName'] = $this->common_data['current_userID'];
        $data['confirmedDate'] = $this->common_data['current_date'];
        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $data['transactionCurrency'] = $gl['bankCurrencyCode'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionAmount'] = $amount;
        $data['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $default_currency = currency_conversion($data['transactionCurrency'], $data['companyLocalCurrency']);
        $reporting_currency = currency_conversion($data['transactionCurrency'], $data['companyReportingCurrency']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalAmount'] = $amount / $data['companyLocalExchangeRate'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];;
        $data['companyReportingAmount'] = $amount / $data['companyReportingExchangeRate'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['bankCurrency'] = $gl['bankCurrencyCode'];


        $data['bankCurrencyID'] = $gl['bankCurrencyID'];
        $data['bankCurrencyExchangeRate'] = 1;
        $data['bankCurrencyAmount'] = $amount;
        $data['bankCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['segmentCode'] = $segmentCode;
        $data['segmentID'] = $segmentID;
        $this->load->library('sequence');
        //$data['RVcode'] = $this->sequence->sequence_generator($documentType);
        $invYear= date("Y", strtotime($data['RVdate']));
        $invMonth= date("m", strtotime($data['RVdate']));
        $data['RVcode'] = $this->sequence->sequence_generator_fin($documentType, $data['companyFinanceYearID'], $invYear, $invMonth);

        $insert = $this->db->insert('srp_erp_customerreceiptmaster', $data);
        $lastinsert_id=$this->db->insert_id();
        if ($insert) {

            $docapprove['departmentID'] = $documentType;
            $docapprove['documentID'] = $documentType;
            $docapprove['documentSystemCode'] = $lastinsert_id;
            $docapprove['documentCode'] = $data['RVcode'];
            $docapprove['documentDate'] = $documentDate;
            $docapprove['approvalLevelID'] = 1;
            $docapprove['isReverseApplicableYN'] = 1;
            $docapprove['docConfirmedDate'] = $this->common_data['current_date'];
            $docapprove['docConfirmedByEmpID'] = $this->common_data['current_user'];
            $docapprove['table_name'] = 'srp_erp_customerreceiptmaster';
            $docapprove['table_unique_field_name'] = 'receiptVoucherAutoId';
            $docapprove['approvedEmpID'] = $this->common_data['current_userID'];
            $docapprove['approvedYN'] = 1;
            $docapprove['approvedDate'] = $this->common_data['current_date'];
            $docapprove['approvedComments'] = 'Created from bank rec';
            $docapprove['approvedPC'] = $this->common_data['current_pc'];
            $docapprove['companyID'] = $this->common_data['company_data']['company_id'];
            $docapprove['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_documentapproved', $docapprove);


            $detail['receiptVoucherAutoId'] = $lastinsert_id;
            $detail['type'] = "GL";
            $detail['referenceNo'] = $refNo;
            $detail['GLAutoID'] = $gl2['GLAutoID'];
            $detail['systemGLCode'] = $gl2['systemAccountCode'];
            $detail['GLCode'] = $gl2['GLSecondaryCode'];
            $detail['GLDescription'] = $gl2['GLDescription'];
            $detail['GLType'] = $gl2['subCategory'];
            $detail['description'] = $narration;
            $detail['transactionAmount'] = $data['transactionAmount'];
            $detail['companyLocalAmount'] = $data['companyLocalAmount'];
            $detail['companyReportingAmount'] = $data['companyReportingAmount'];
            $detail['companyID'] = $data['companyID'];
            $detail['companyCode'] = $data['companyCode'];
            $detail['createdUserGroup'] = $data['createdUserGroup'];
            $detail['createdPCID'] = $data['createdPCID'];
            $detail['createdUserID'] = $data['createdUserID'];
            $detail['createdDateTime'] = $data['createdDateTime'];
            $detail['createdUserName'] = $data['createdUserName'];
            /*added*/
            $detail['segmentCode'] = $segmentCode;
            $detail['segmentID'] = $segmentID;

            $insertDetail = $this->db->insert('srp_erp_customerreceiptdetail', $detail);
        }
        $bankledger['documentDate'] = $documentDate;
        $bankledger['transactionType'] = $type1;
        $bankledger['transactionCurrencyID'] = $gl['bankCurrencyID'];

        $bankledger['transactionCurrency'] = $gl['bankCurrencyCode'];
        $bankledger['transactionExchangeRate'] = 1;
        $bankledger['transactionAmount'] = $amount;
        $bankledger['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $bankledger['bankCurrency'] = $gl['bankCurrencyCode'];
        $bankledger['bankCurrencyID'] = $gl['bankCurrencyID'];
        $bankledger['bankCurrencyExchangeRate'] = 1;
        $bankledger['bankCurrencyAmount'] = $amount;
        $bankledger['bankCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $bankledger['memo'] = $narration;

        $bankledger['bankName'] = $gl['bankName'];
        $bankledger['bankGLAutoID'] = $gl['GLAutoID'];
        $bankledger['bankSystemAccountCode'] = $gl['systemAccountCode'];
        $bankledger['bankGLSecondaryCode'] = $gl['GLSecondaryCode'];
        $bankledger['documentMasterAutoID'] = $detail['receiptVoucherAutoId'];
        $bankledger['documentSystemCode'] = $data['RVcode'];
        $bankledger['documentType'] = $documentType;
        $bankledger['createdPCID'] = $this->common_data['current_pc'];
        $bankledger['companyID'] = $this->common_data['company_data']['company_id'];
        $bankledger['companyCode'] = $this->common_data['company_data']['company_code'];
        $bankledger['createdUserID'] = $this->common_data['current_userID'];
        $bankledger['createdDateTime'] = $this->common_data['current_date'];;
        $bankledger['createdUserName'] = $this->common_data['current_user'];
        $bankledger['segmentCode'] = $segmentCode;
        $bankledger['segmentID'] = $segmentID;
        $bankledgerinsert = $this->db->insert('srp_erp_bankledger', $bankledger);
        $docdate = explode('-', $documentDate);
        $generalLedger[0]['documentCode'] = $documentType;
        $generalLedger[0]['documentMasterAutoID'] = $detail['receiptVoucherAutoId'];
        $generalLedger[0]['documentSystemCode'] = $data['RVcode'];
        $generalLedger[0]['documentType'] = "Direct";
        $generalLedger[0]['documentDate'] = $documentDate;
        $generalLedger[0]['documentYear'] = $docdate[0];
        $generalLedger[0]['documentMonth'] = $docdate[1];
        $generalLedger[0]['documentNarration'] = $narration;
        $generalLedger[0]['GLAutoID'] = $gl['GLAutoID'];
        $generalLedger[0]['systemGLCode'] = $gl['systemAccountCode'];
        $generalLedger[0]['GLCode'] = $gl['GLSecondaryCode'];
        $generalLedger[0]['GLDescription'] = $gl['GLDescription'];
        $generalLedger[0]['GLType'] = $gl['subCategory'];
        $generalLedger[0]['amount_type'] = 'dr';
        $generalLedger[0]['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $generalLedger[0]['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
        $generalLedger[0]['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
        $generalLedger[0]['transactionCurrency'] = $gl['bankCurrencyCode'];
        $generalLedger[0]['transactionExchangeRate'] = 1;
        $generalLedger[0]['transactionAmount'] = $amount;
        $generalLedger[0]['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $generalLedger[0]['companyLocalCurrency'] = $data['companyLocalCurrency'];
        $generalLedger[0]['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
        $generalLedger[0]['companyLocalAmount'] = $data['companyLocalAmount'];
        $generalLedger[0]['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
        $generalLedger[0]['companyReportingCurrency'] = $data['companyReportingCurrency'];
        $generalLedger[0]['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
        $generalLedger[0]['companyReportingAmount'] = $data['companyReportingAmount'];
        $generalLedger[0]['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];

        $generalLedger[0]['confirmedByEmpID'] = $this->common_data['current_userID'];
        $generalLedger[0]['confirmedByName'] = $this->common_data['current_user'];
        $generalLedger[0]['confirmedDate'] = $this->common_data['current_date'];
        $generalLedger[0]['approvedDate'] = $this->common_data['current_date'];
        $generalLedger[0]['approvedbyEmpID'] = $this->common_data['current_userID'];
        $generalLedger[0]['approvedbyEmpName'] = $this->common_data['current_user'];
        $generalLedger[0]['companyID'] = $this->common_data['company_data']['company_id'];;
        $generalLedger[0]['companyCode'] = $this->common_data['company_data']['company_code'];;
        $generalLedger[0]['createdUserGroup'] = $this->common_data['user_group'];
        $generalLedger[0]['createdPCID'] = $this->common_data['current_pc'];
        $generalLedger[0]['createdUserID'] = $this->common_data['current_userID'];;
        $generalLedger[0]['createdDateTime'] = $this->common_data['current_date'];
        $generalLedger[0]['createdUserName'] = $this->common_data['current_user'];
        $generalLedger[0]['segmentCode'] = $segmentCode;
        $generalLedger[0]['segmentID'] = $segmentID;
        $generalLedger[1]['segmentCode'] = $segmentCode;
        $generalLedger[1]['segmentID'] = $segmentID;
        $generalLedger[1]['documentCode'] = $documentType;
        $generalLedger[1]['documentMasterAutoID'] = $detail['receiptVoucherAutoId'];
        $generalLedger[1]['documentSystemCode'] = $data['RVcode'];
        $generalLedger[1]['documentType'] = "Direct";
        $generalLedger[1]['documentDate'] = $documentDate;
        $generalLedger[1]['documentYear'] = $docdate[0];
        $generalLedger[1]['documentMonth'] = $docdate[1];
        $generalLedger[1]['documentNarration'] = $narration;
        $generalLedger[1]['GLAutoID'] = $gl2['GLAutoID'];
        $generalLedger[1]['systemGLCode'] = $gl2['systemAccountCode'];
        $generalLedger[1]['GLCode'] = $gl2['GLSecondaryCode'];
        $generalLedger[1]['GLDescription'] = $gl2['GLDescription'];
        $generalLedger[1]['GLType'] = $gl2['subCategory'];
        $generalLedger[1]['amount_type'] = 'cr';
        $generalLedger[1]['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $generalLedger[1]['transactionCurrency'] = $gl['bankCurrencyCode'];
        $generalLedger[1]['transactionExchangeRate'] = 1;
        $generalLedger[1]['transactionAmount'] = -1 * abs($amount);
        $generalLedger[1]['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $generalLedger[1]['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
        $generalLedger[1]['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
        $generalLedger[1]['companyLocalCurrency'] = $data['companyLocalCurrency'];
        $generalLedger[1]['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
        $generalLedger[1]['companyLocalAmount'] = -1 * abs($data['companyLocalAmount']);
        $generalLedger[1]['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
        $generalLedger[1]['companyReportingCurrency'] = $data['companyReportingCurrency'];
        $generalLedger[1]['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
        $generalLedger[1]['companyReportingAmount'] = -1 * abs($data['companyReportingAmount']);
        $generalLedger[1]['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
        $generalLedger[1]['confirmedByEmpID'] = $this->common_data['current_userID'];
        $generalLedger[1]['confirmedByName'] = $this->common_data['current_user'];
        $generalLedger[1]['confirmedDate'] = $this->common_data['current_date'];
        $generalLedger[1]['approvedDate'] = $this->common_data['current_date'];
        $generalLedger[1]['approvedbyEmpID'] = $this->common_data['current_userID'];
        $generalLedger[1]['approvedbyEmpName'] = $this->common_data['current_user'];
        $generalLedger[1]['companyID'] = $this->common_data['company_data']['company_id'];
        $generalLedger[1]['companyCode'] = $this->common_data['company_data']['company_code'];
        $generalLedger[1]['createdUserGroup'] = $this->common_data['user_group'];
        $generalLedger[1]['createdPCID'] = $this->common_data['current_pc'];
        $generalLedger[1]['createdUserID'] = $this->common_data['current_userID'];
        $generalLedger[1]['createdDateTime'] = $this->common_data['current_date'];
        $generalLedger[1]['createdUserName'] = $this->common_data['current_user'];


        $this->db->insert_batch('srp_erp_generalledger', $generalLedger);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
            echo json_encode(true);
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Records Inserted Successfully.');
            echo json_encode(true);
        }

    }

    function bankrec_payment_account()
    {
        $this->db->trans_begin();
        $bankGLAutoID = $this->input->post('bankGLAutoID');
        $date_format_policy = date_format_policy();
        $docutDate = $this->input->post('documentDate');
        $documentDate = input_format_date($docutDate, $date_format_policy);
        $bankAccountID = $this->input->post('bankAccountID');
        $type1 = $this->input->post('type');
        $refNo = $this->input->post('reference');
        $narration = $this->input->post('narration');
        $amount = $this->input->post('amount');
        $GLAutoID = $this->input->post('GLAutoID');
        $seg = explode('|', $this->input->post('segmentID'));
        $segmentID = $seg[0];
        $segmentCode = $seg[1];
        $documentType = '';
        if ($type1 == 1) {
            $documentType = 'RV';
        }
        if ($type1 == 2) {
            $documentType = 'PV';
        }
        /*payment voucher*/
        $companyid=current_companyID();
        $FY = $this->db->query("SELECT * FROM srp_erp_companyfinanceperiod INNER JOIN srp_erp_companyfinanceyear ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID WHERE dateFrom <= '{$documentDate}' AND dateTo >= '{$documentDate}' AND srp_erp_companyfinanceperiod.companyID = $companyid AND srp_erp_companyfinanceperiod.isActive = 1 AND srp_erp_companyfinanceyear.isActive = 1")->row_array();
        if (empty($FY)) {
            if (empty($FY)) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('e', 'Active finance period not found for selected document date.');
                echo json_encode(false);
                EXIT;
            }
        }
        $gl = $this->getGLdetails($GLAutoID);
        $gl2 = $this->getGLdetails($bankAccountID);
        $type = substr($documentType, 0, 3);

        $data['documentID'] = $documentType;
        $data['PVdate'] = $documentDate;
        $data['pvType'] = 'Direct';
        $data['referenceNo'] = $refNo;
        $data['companyFinanceYearID'] = $FY['companyFinanceYearID'];
        $data['FYBegin'] = $FY['beginingDate'];
        $data['FYEnd'] = $FY['endingDate'];
        $data['FYPeriodDateFrom'] = $FY['dateFrom'];
        $data['FYPeriodDateTo'] = $FY['dateTo'];
        $data['PVbankCode'] = $gl['bankShortCode'];
        $data['bankGLAutoID'] = $gl['GLAutoID'];
        $data['bankSystemAccountCode'] = $gl['systemAccountCode'];
        $data['bankGLSecondaryCode'] = $gl['GLSecondaryCode'];
        $data['PVbank'] = $gl['bankName'];
        $data['PVbankBranch'] = $gl['bankBranch'];
        $data['PVbankSwiftCode'] = $gl['bankSwiftCode'];
        $data['PVbankAccount'] = $gl['bankAccountNumber'];
        $data['PVbankType'] = $gl['subCategory'];
        $data['PVNarration'] = $narration;
        $data['confirmedYN'] = 1;
        $data['confirmedByEmpID'] = $this->common_data['current_user'];
        $data['confirmedByName'] = $this->common_data['current_userID'];
        $data['confirmedDate'] = $this->common_data['current_date'];
        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $data['transactionCurrency'] = $gl['bankCurrencyCode'];
        $data['transactionExchangeRate'] = 1;
        $data['transactionAmount'] = $amount;
        $data['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $default_currency = currency_conversion($data['transactionCurrency'], $data['companyLocalCurrency']);
        $reporting_currency = currency_conversion($data['transactionCurrency'], $data['companyReportingCurrency']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalAmount'] = $amount / $data['companyLocalExchangeRate'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];;
        $data['companyReportingAmount'] = $amount / $data['companyReportingExchangeRate'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['bankCurrencyID'] = $gl['bankCurrencyID'];
        $data['bankCurrency'] = $gl['bankCurrencyCode'];
        $data['bankCurrencyExchangeRate'] = 1;
        $data['bankCurrencyAmount'] = $amount;
        $data['bankCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['segmentCode'] = $segmentCode;
        $data['segmentID'] = $segmentID;
        //$data['PVcode'] = $this->sequence->sequence_generator($documentType);
        $this->load->library('sequence');
        $invYear= date("Y", strtotime($data['PVdate']));
        $invMonth= date("m", strtotime($data['PVdate']));
        $data['PVcode'] = $this->sequence->sequence_generator_fin($documentType, $FY['companyFinanceYearID'], $invYear, $invMonth);


        $insert = $this->db->insert('srp_erp_paymentvouchermaster', $data);
        $lastinsert_id=$this->db->insert_id();
        if ($insert) {

            $docapprove['departmentID'] = $documentType;
            $docapprove['documentID'] = $documentType;
            $docapprove['documentSystemCode'] = $lastinsert_id;
            $docapprove['documentCode'] = $data['PVcode'];
            $docapprove['documentDate'] = $documentDate;
            $docapprove['approvalLevelID'] = 1;
            $docapprove['isReverseApplicableYN'] = 1;
            $docapprove['docConfirmedDate'] = $this->common_data['current_date'];
            $docapprove['docConfirmedByEmpID'] = $this->common_data['current_user'];
            $docapprove['table_name'] = 'srp_erp_paymentvouchermaster';
            $docapprove['table_unique_field_name'] = 'payVoucherAutoId';
            $docapprove['approvedEmpID'] = $this->common_data['current_userID'];
            $docapprove['approvedYN'] = 1;
            $docapprove['approvedDate'] = $this->common_data['current_date'];
            $docapprove['approvedComments'] = 'Created from bank rec';
            $docapprove['approvedPC'] = $this->common_data['current_pc'];
            $docapprove['companyID'] = $this->common_data['company_data']['company_id'];
            $docapprove['companyCode'] = $this->common_data['company_data']['company_code'];

            $this->db->insert('srp_erp_documentapproved', $docapprove);


            $detail['payVoucherAutoId'] = $lastinsert_id;
            $detail['type'] = "GL";
            $detail['referenceNo'] = $refNo;
            $detail['bookingDate'] = $documentDate;
            $detail['Invoice_amount'] = $amount;
            $detail['GLAutoID'] = $gl2['GLAutoID'];
            $detail['systemGLCode'] = $gl2['systemAccountCode'];
            $detail['GLCode'] = $gl2['GLSecondaryCode'];
            $detail['GLDescription'] = $gl2['GLDescription'];
            $detail['GLType'] = $gl2['subCategory'];
            $detail['description'] = $narration;
            $detail['transactionCurrencyID'] = $data['transactionCurrencyID'];
            $detail['transactionCurrency'] = $data['transactionCurrency'];
            $detail['transactionExchangeRate'] = $data['transactionExchangeRate'];
            $detail['transactionAmount'] = $data['transactionAmount'];
            $detail['transactionCurrencyDecimalPlaces'] = $data['transactionCurrencyDecimalPlaces'];
            $detail['companyLocalCurrency'] = $data['companyLocalCurrency'];
            $detail['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
            $detail['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
            $detail['companyLocalAmount'] = $data['companyLocalAmount'];
            $detail['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
            $detail['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
            $detail['companyReportingCurrency'] = $data['companyReportingCurrency'];
            $detail['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
            $detail['companyReportingAmount'] = $data['companyReportingAmount'];
            $detail['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
            $detail['companyID'] = $data['companyID'];
            $detail['companyCode'] = $data['companyCode'];
            $detail['createdUserGroup'] = $data['createdUserGroup'];
            $detail['createdPCID'] = $data['createdPCID'];
            $detail['createdUserID'] = $data['createdUserID'];
            $detail['createdDateTime'] = $data['createdDateTime'];
            $detail['createdUserName'] = $data['createdUserName'];
            $detail['segmentCode'] = $segmentCode;
            $detail['segmentID'] = $segmentID;

            $insertDetail = $this->db->insert('srp_erp_paymentvoucherdetail', $detail);


        }
        $bankledger['documentDate'] = $documentDate;
        $bankledger['transactionType'] = $type1;
        $bankledger['transactionCurrency'] = $gl['bankCurrencyCode'];
        $bankledger['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $bankledger['transactionExchangeRate'] = 1;
        $bankledger['transactionAmount'] = $amount;
        $bankledger['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $bankledger['bankCurrency'] = $gl['bankCurrencyCode'];
        $bankledger['bankCurrencyExchangeRate'] = 1;
        $bankledger['bankCurrencyAmount'] = $amount;
        $bankledger['bankCurrencyID'] = $gl['bankCurrencyID'];
        $bankledger['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $bankledger['bankCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $bankledger['memo'] = $narration;
        $bankledger['bankName'] = $gl['bankName'];
        $bankledger['bankGLAutoID'] = $gl['GLAutoID'];
        $bankledger['bankSystemAccountCode'] = $gl['systemAccountCode'];
        $bankledger['bankGLSecondaryCode'] = $gl['GLSecondaryCode'];
        $bankledger['documentMasterAutoID'] = $detail['payVoucherAutoId'];
        $bankledger['documentSystemCode'] = $data['PVcode'];
        $bankledger['documentType'] = $documentType;
        $bankledger['createdPCID'] = $this->common_data['current_pc'];
        $bankledger['companyID'] = $this->common_data['company_data']['company_id'];
        $bankledger['companyCode'] = $this->common_data['company_data']['company_code'];
        $bankledger['createdUserID'] = $this->common_data['current_userID'];
        $bankledger['createdDateTime'] = $this->common_data['current_date'];;
        $bankledger['createdUserName'] = $this->common_data['current_user'];
        $bankledger['segmentCode'] = $segmentCode;
        $bankledger['segmentID'] = $segmentID;
        $bankledgerinsert = $this->db->insert('srp_erp_bankledger', $bankledger);

        $docdate = explode('-', $documentDate);

        $generalLedger[0]['documentCode'] = $documentType;
        $generalLedger[0]['documentMasterAutoID'] = $detail['payVoucherAutoId'];
        $generalLedger[0]['documentSystemCode'] = $data['PVcode'];
        $generalLedger[0]['documentType'] = "Direct";
        $generalLedger[0]['documentDate'] = $documentDate;
        $generalLedger[0]['documentYear'] = $docdate[0];
        $generalLedger[0]['documentMonth'] = $docdate[1];
        $generalLedger[0]['documentNarration'] = $narration;
        $generalLedger[0]['GLAutoID'] = $gl['GLAutoID'];
        $generalLedger[0]['systemGLCode'] = $gl['systemAccountCode'];
        $generalLedger[0]['GLCode'] = $gl['GLSecondaryCode'];
        $generalLedger[0]['GLDescription'] = $gl['GLDescription'];
        $generalLedger[0]['GLType'] = $gl['subCategory'];
        $generalLedger[0]['amount_type'] = 'cr';
        $generalLedger[0]['transactionCurrency'] = $gl['bankCurrencyCode'];
        $generalLedger[0]['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $generalLedger[0]['transactionExchangeRate'] = 1;
        $generalLedger[0]['transactionAmount'] = -1 * abs($amount);
        $generalLedger[0]['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $generalLedger[0]['companyLocalCurrency'] = $data['companyLocalCurrency'];
        $generalLedger[0]['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
        $generalLedger[0]['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
        $generalLedger[0]['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
        $generalLedger[0]['companyLocalAmount'] = -1 * abs($data['companyLocalAmount']);
        $generalLedger[0]['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
        $generalLedger[0]['companyReportingCurrency'] = $data['companyReportingCurrency'];
        $generalLedger[0]['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
        $generalLedger[0]['companyReportingAmount'] = -1 * abs($data['companyReportingAmount']);
        $generalLedger[0]['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];
        $generalLedger[0]['confirmedByEmpID'] = $this->common_data['current_userID'];
        $generalLedger[0]['confirmedByName'] = $this->common_data['current_user'];
        $generalLedger[0]['confirmedDate'] = $this->common_data['current_date'];
        $generalLedger[0]['approvedDate'] = $this->common_data['current_date'];
        $generalLedger[0]['approvedbyEmpID'] = $this->common_data['current_userID'];
        $generalLedger[0]['approvedbyEmpName'] = $this->common_data['current_user'];
        $generalLedger[0]['companyID'] = $this->common_data['company_data']['company_id'];;
        $generalLedger[0]['companyCode'] = $this->common_data['company_data']['company_code'];;
        $generalLedger[0]['createdUserGroup'] = $this->common_data['user_group'];
        $generalLedger[0]['createdPCID'] = $this->common_data['current_pc'];
        $generalLedger[0]['createdUserID'] = $this->common_data['current_userID'];;
        $generalLedger[0]['createdDateTime'] = $this->common_data['current_date'];
        $generalLedger[0]['createdUserName'] = $this->common_data['current_user'];
        $generalLedger[0]['segmentCode'] = $segmentCode;
        $generalLedger[0]['segmentID'] = $segmentID;
        $generalLedger[1]['segmentCode'] = $segmentCode;
        $generalLedger[1]['segmentID'] = $segmentID;

        $generalLedger[1]['documentCode'] = $documentType;
        $generalLedger[1]['documentMasterAutoID'] = $detail['payVoucherAutoId'];
        $generalLedger[1]['documentSystemCode'] = $data['PVcode'];
        $generalLedger[1]['documentType'] = "Direct";
        $generalLedger[1]['documentDate'] = $documentDate;
        $generalLedger[1]['documentYear'] = $docdate[0];
        $generalLedger[1]['documentMonth'] = $docdate[1];
        $generalLedger[1]['documentNarration'] = $narration;
        $generalLedger[1]['GLAutoID'] = $gl2['GLAutoID'];
        $generalLedger[1]['systemGLCode'] = $gl2['systemAccountCode'];
        $generalLedger[1]['GLCode'] = $gl2['GLSecondaryCode'];
        $generalLedger[1]['GLDescription'] = $gl2['GLDescription'];
        $generalLedger[1]['GLType'] = $gl2['subCategory'];
        $generalLedger[1]['amount_type'] = 'dr';
        /**/

        /**/
        $generalLedger[1]['transactionCurrencyID'] = $gl['bankCurrencyID'];
        $generalLedger[1]['transactionCurrency'] = $gl['bankCurrencyCode'];
        $generalLedger[1]['companyReportingCurrency'] = 1;
        $generalLedger[1]['companyLocalCurrency'] = $data['companyLocalCurrency'];//$this->common_data['company_data']['company_default_currency'];
        /*    $default_currency = currency_conversion($generalLedger[1]['companyLocalCurrency'],$generalLedger[1]['companyLocalCurrency']);
            $reporting_currency = currency_conversion($generalLedger[1]['companyLocalCurrency'],$generalLedger[1]['companyReportingCurrency']);*/
        $generalLedger[1]['transactionExchangeRate'] = 1;
        $generalLedger[1]['transactionAmount'] = $amount;
        $generalLedger[1]['transactionCurrencyDecimalPlaces'] = $gl['bankCurrencyDecimalPlaces'];
        $generalLedger[1]['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];//$default_currency['conversion'];
        $companyLocalAmount = $amount / $generalLedger[1]['companyLocalExchangeRate'];
        $generalLedger[1]['companyLocalAmount'] = $data['companyLocalAmount']; //$companyLocalAmount;
        $generalLedger[1]['companyLocalCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces'];//$default_currency['DecimalPlaces'];
        $generalLedger[1]['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
        $generalLedger[1]['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];
        $generalLedger[1]['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $companyReportingAmount = $amount / $generalLedger[1]['companyReportingExchangeRate'];
        $generalLedger[1]['companyReportingAmount'] = $data['companyReportingAmount'];//$companyReportingAmount;
        $generalLedger[1]['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingCurrencyDecimalPlaces']; //$reporting_currency['DecimalPlaces'];
        $generalLedger[1]['confirmedByEmpID'] = $this->common_data['current_userID'];
        $generalLedger[1]['confirmedByName'] = $this->common_data['current_user'];
        $generalLedger[1]['confirmedDate'] = $this->common_data['current_date'];
        $generalLedger[1]['approvedDate'] = $this->common_data['current_date'];
        $generalLedger[1]['approvedbyEmpID'] = $this->common_data['current_userID'];
        $generalLedger[1]['approvedbyEmpName'] = $this->common_data['current_user'];
        $generalLedger[1]['companyID'] = $this->common_data['company_data']['company_id'];;
        $generalLedger[1]['companyCode'] = $this->common_data['company_data']['company_code'];;
        $generalLedger[1]['createdUserGroup'] = $this->common_data['user_group'];
        $generalLedger[1]['createdPCID'] = $this->common_data['current_pc'];
        $generalLedger[1]['createdUserID'] = $this->common_data['current_userID'];;
        $generalLedger[1]['createdDateTime'] = $this->common_data['current_date'];
        $generalLedger[1]['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert_batch('srp_erp_generalledger', $generalLedger);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('e', 'Failed. please try again');
            echo json_encode(true);
        } else {

            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Records Inserted Successfully.');
            echo json_encode(true);
        }
    }

    function get_assignedcurrency_company($companyID)
    {
        /*   $output= $this->db->query("SELECT srp_erp_companycurrencyassign.* FROM srp_erp_companycurrencyassign LEFT JOIN srp_erp_currencymaster on srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID WHERE companyID = {$companyID}")->result_array();*/
        $output = $this->db->query("    SELECT srp_erp_companycurrencyassign.*, IF(company_reporting_currencyID = srp_erp_companycurrencyassign.currencyID, company_reporting_currencyID, 0) as company_reporting_currencyID FROM `srp_erp_company` LEFT JOIN srp_erp_companycurrencyassign ON srp_erp_company.company_id = srp_erp_companycurrencyassign.companyID LEFT JOIN srp_erp_currencymaster ON srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID WHERE company_id = {$companyID} ORDER BY company_reporting_currencyID DESC ")->result_array();
        return $output;

    }

    function detail_assignedcurrency_company($companyID, $mastercurrencyassignAutoID)
    {
        $output = $this->db->query("SELECT mastercurrencyassignAutoID,subcurrencyassignAutoID,currencyConversionAutoID,m.CurrencyName as baseCurrency,s.CurrencyName as subCurrency,conversion FROM srp_erp_companycurrencyconversion LEFT JOIN srp_erp_currencymaster  m on m.CurrencyID=masterCurrencyID LEFT JOIN srp_erp_currencymaster  s on s.CurrencyID=subCurrencyID WHERE companyID = {$companyID} AND mastercurrencyassignAutoID = {$mastercurrencyassignAutoID}")->result_array();
        return $output;

    }

    function delete_banktransfer_master()
    {
        $dataD = array(
            'status' => 0,
            'documentMasterAutoID' => null,
            'documentID' => null
        );
        $this->db->where('documentMasterAutoID', $this->input->post('bankTransferAutoID'));
        $this->db->where('documentID', 'BT');
        $this->db->update('srp_erp_chequeregisterdetails', $dataD);

        $this->db->delete('srp_erp_banktransfer', array('bankTransferAutoID' => trim($this->input->post('bankTransferAutoID') ?? '')));
        return true;
    }

    function delete_bankfacilityLoan()
    {
        $this->db->delete('srp_erp_bankfacilityloan', array('bankFacilityID' => trim($this->input->post('bankFacilityID') ?? '')));
        $this->db->delete('srp_erp_bankfacilityloandetail', array('bankFacilityID' => trim($this->input->post('bankFacilityID') ?? '')));
        $this->session->set_flashdata('s', 'Deleted Records Successfully.');

        return true;


    }

    function get_companyCountry()
    {

    }

    function get_desertAllowance_report()
    {

    }

    function get_jobBonus_report()
    {

    }

    function delete_bankrec()
    {
       $result= $this->db->delete('srp_erp_bankrecmaster', array('bankRecAutoID' => trim($this->input->post('bankRecAutoID') ?? '')));
        if($result){
            return array('s','Deleted Successfully');
        }else{
            return array('E','Deletion Failed');
        }
    }

    function getDecimalPlaces()
    {
        $bankFrom = $this->input->post('bankFrom');
        if($bankFrom ==''){
          $data['bankCurrencyDecimalPlaces']=0;
        }else{
          $data = $this->db->query("SELECT bankCurrencyDecimalPlaces FROM srp_erp_chartofaccounts  WHERE GLAutoID = {$bankFrom}")->row_array();
        }

        return $data;
    }

    function load_Cheque_templates($bankTransferAutoID)
    {
        $this->db->select('fromBankGLAutoID');
        $this->db->where('bankTransferAutoID', $bankTransferAutoID);
        $this->db->from('srp_erp_banktransfer');
        $glid = $this->db->get()->row_array();

        $this->db->select('srp_erp_chartofaccountchequetemplates.coaChequeTemplateID,srp_erp_chartofaccountchequetemplates.pageLink,srp_erp_systemchequetemplates.Description');
        $this->db->where('companyID', current_companyID());
        $this->db->where('GLAutoID', $glid['fromBankGLAutoID']);
        $this->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $data = $this->db->get()->result_array();
        return $data;
    }

    function bank_transfer_master_cheque($bankTransferAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query("SELECT fromCurrency.bankCurrencyID as fromcurrencyID, toCurrency.bankCurrencyID as tocurrencyID, srp_erp_banktransfer.*,DATE_FORMAT(transferedDate,'$convertFormat') AS transferedDate ,DATE_FORMAT(srp_erp_banktransfer.approvedDate,'.$convertFormat. %h:%i:%s') AS approvedDate, fromCurrency.bankCurrencyCode as fromcurrency, toCurrency.bankCurrencyCode as tocurrency, fromCurrency.GLDescription as bankfrom, toCurrency.GLDescription as bankto,fromCurrency.bankCurrencyDecimalPlaces AS fromDecimalPlaces, toCurrency.bankCurrencyDecimalPlaces AS toDecimalPlaces,fromCurrency.systemAccountCode as fromSystemAccountCode,fromCurrency.GLSecondaryCode as fromGLSecondaryCode, toCurrency.systemAccountCode as toCurrencySystemAccountCode,toCurrency.GLSecondaryCode as toCurrencyGLSecondaryCode, fromCurrency.GLDescription as fromGLDescription, fromCurrency.subCategory as fromSubCategory, toCurrency.GLDescription as toGLDescription, toCurrency.subCategory as toSubCategory,srp_erp_currencymaster.CurrencyCode as CurrencyCode,chequeDate FROM srp_erp_banktransfer LEFT JOIN srp_erp_chartofaccounts AS fromCurrency ON fromBankGLAutoID = fromCurrency.GLAutoID LEFT JOIN srp_erp_chartofaccounts AS toCurrency ON toBankGLAutoID = toCurrency.GLAutoID LEFT JOIN srp_erp_currencymaster  ON fromBankCurrencyID = srp_erp_currencymaster.currencyID WHERE bankTransferAutoID = {$bankTransferAutoID}")->row_array();
        return $data;
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'BT');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }

    function saveChequeRegister(){
        $companyID=current_companyID();
        $bankGLAutoID=$this->input->post('bankGLAutoID');
        $startChequeNo=$this->input->post('startChequeNo');
        $noofcheques=$this->input->post('noofcheques');
        $endChequeNo=$this->input->post('endChequeNo');
        $frstZeros=$this->input->post('frstZeros');
        $chequeRegisterID=$this->input->post('chequeRegisterID');
        $description = $this->input->post('description');
        $data['description'] = str_replace('<br />', PHP_EOL, $description);
        $data['startChequeNo'] = $this->input->post('startChequeNo');
        $data['noofcheques'] = $this->input->post('noofcheques');
        $data['endChequeNo'] = $this->input->post('endChequeNo');
        $data['bankGLAutoID'] = $this->input->post('bankGLAutoID');
        $data['companyID'] = $companyID;
        if($chequeRegisterID)
        {
            $chequeDetail = $this->db->query("SELECT MAX(chequeNo) AS chequeNo FROM `srp_erp_chequeregisterdetails` LEFT JOIN srp_erp_chequeregister ON srp_erp_chequeregister.chequeRegisterID=srp_erp_chequeregisterdetails.chequeRegisterID WHERE  srp_erp_chequeregisterdetails.chequeRegisterID !=$chequeRegisterID AND srp_erp_chequeregisterdetails.companyID=$companyID  AND bankGLAutoID=$bankGLAutoID AND chequeNo BETWEEN $startChequeNo AND $endChequeNo")->row_array();
            if(!empty($chequeDetail['chequeNo'])){
                return array('e','Cheque no already exist');
                exit();
            }
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('chequeRegisterID', $chequeRegisterID);
            $result = $this->db->update('srp_erp_chequeregister', $data);
        }else
        {
            $chequeDetail = $this->db->query("SELECT MAX(chequeNo) AS chequeNo FROM `srp_erp_chequeregisterdetails` LEFT JOIN srp_erp_chequeregister ON srp_erp_chequeregister.chequeRegisterID=srp_erp_chequeregisterdetails.chequeRegisterID WHERE srp_erp_chequeregisterdetails.companyID=$companyID  AND bankGLAutoID=$bankGLAutoID AND chequeNo BETWEEN $startChequeNo AND $endChequeNo")->row_array();

            if(!empty($chequeDetail['chequeNo'])){
                return array('e','Cheque no already exist');
                exit();
            }
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $result = $this->db->insert('srp_erp_chequeregister', $data);
            $chequeRegisterID=$this->db->insert_id();
        }
        if($result){
                if($chequeRegisterID)
                {
                    $this->db->delete('srp_erp_chequeregisterdetails', array('chequeRegisterID' =>$chequeRegisterID));
                }
                for ($x = 1; $x <= $noofcheques; $x++) {
                    $dataD['chequeRegisterID'] = $chequeRegisterID;
                    if($x==1){
                        $dataD['chequeNo'] = $startChequeNo;
                    }else{
                        $dataD['chequeNo'] = $frstZeros.$startChequeNo;
                    }

                    $dataD['status'] = 0;
                    $dataD['companyID'] = $companyID;

                    $dataD['createdUserGroup'] = $this->common_data['user_group'];
                    $dataD['createdPCID'] = $this->common_data['current_pc'];
                    $dataD['createdUserID'] = $this->common_data['current_userID'];
                    $dataD['createdDateTime'] = $this->common_data['current_date'];
                    $dataD['createdUserName'] = $this->common_data['current_user'];

                    $registerD = $this->db->insert('srp_erp_chequeregisterdetails', $dataD);
                    $startChequeNo++;
                }
                return array('s','Cheque Register Saved Successfully');
        }else{
            return array('e','Cheque Register Save failed');
        }
    }
    function cheque_register_detail_modal(){
                $chequeRegisterID=$this->input->post('chequeRegisterID');
                $this->db->select('srp_erp_chequeregisterdetails.*,srp_erp_paymentvouchermaster.PVcode as PVcode,srp_erp_chequeregister.description as chqDescription');
                $this->db->where('srp_erp_chequeregisterdetails.companyID', current_companyID());
                $this->db->where('srp_erp_chequeregisterdetails.chequeRegisterID', $chequeRegisterID);
                $this->db->join('srp_erp_paymentvouchermaster', 'srp_erp_chequeregisterdetails.documentMasterAutoID = srp_erp_paymentvouchermaster.payVoucherAutoId', 'left');
                $this->db->join('srp_erp_chequeregister', 'srp_erp_chequeregister.chequeRegisterID = srp_erp_chequeregisterdetails.chequeRegisterID', 'left');
                //$this->db->join('srp_erp_banktransfer', 'srp_erp_chequeregister.chequeRegisterID = srp_erp_banktransfer.bankTransferAutoID', 'left');
                $this->db->from('srp_erp_chequeregisterdetails');
                return $this->db->get()->result_array();    
    }

    function uodatechequeStatus(){
        $chequeRegisterDetailID=$this->input->post('chequeRegisterDetailID');
        $chkval=$this->input->post('chkval');
        $exsist = $this->db->query("SELECT documentMasterAutoID FROM srp_erp_chequeregisterdetails  WHERE chequeRegisterDetailID = $chequeRegisterDetailID AND documentID != NULL")->row_array();
        if(!empty($exsist)){
            return array('s','This cheque cannot be canceled');
        }else{
            $data = array(
                'status' => $chkval,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedDateTime' => $this->common_data['current_date'],
                'modifiedUserID' => $this->common_data['current_userID'],
                'modifiedUserName' => $this->common_data['current_user']
            );
            $this->db->where('chequeRegisterDetailID', $chequeRegisterDetailID);
            $result=$this->db->update('srp_erp_chequeregisterdetails', $data);
            if($result){
                return array('s','Updated Successfully ');
            }else{
                return array('e','Update failed');
            }
        }
    }

    function get_bank_reconciliation_report()
    {
        $companyID = current_companyID();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $GLAutoID = $this->input->post('GLAutoID');

        $date_format_policy = date_format_policy();
        $DateFrom = input_format_date($datefrom, $date_format_policy);
        $DateTo = input_format_date($dateto, $date_format_policy);
        $convertFormat = convert_date_format_sql();
        $qry = $this->db->query("SELECT	documentSystemCode, DATE_FORMAT(documentDate,' $convertFormat ') AS documentDate, partyCode, partyName, bankCurrency, bankCurrencyAmount, IFNULL(description, ' - ') AS description, bankCurrencyDecimalPlaces, clearedBy, DATE_FORMAT(clearedDate,' $convertFormat ') AS clearedDate, DATE_FORMAT(bankRecAsOf,' $convertFormat ') AS bankRecAsOf, clearedAmount , memo
                            FROM `srp_erp_bankledger` 
                            LEFT JOIN srp_erp_bankrecmaster ON srp_erp_bankrecmaster.bankRecAutoID = srp_erp_bankledger.bankRecMonthID
                            WHERE
                                srp_erp_bankledger.companyID = {$companyID}
                                AND srp_erp_bankledger.bankGLAutoID = {$GLAutoID}
                                AND clearedYN = 1 
                                AND bankRecAsOf BETWEEN '{$DateFrom}' AND '{$DateTo}' AND approvedYN = 1")->result_array();

        return $qry;
    }

    function update_cheque_detail($chequeRegisterDetailID, $documentMasterAutoID)
    {
        $dataD = array(
            'status' => 0,
            'documentMasterAutoID' => null,
            'documentID' => null
        );
        $this->db->where('documentMasterAutoID', $documentMasterAutoID);
        $this->db->where('documentID', 'BT');
        $this->db->update('srp_erp_chequeregisterdetails', $dataD);

        $data = array(
            'status' => 1,
            'documentMasterAutoID' => $documentMasterAutoID,
            'documentID' => 'BT',
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
        $this->db->where('documentID', 'BT');
        $this->db->update('srp_erp_chequeregisterdetails', $dataD);
    }

    function update_cleared_date($clearedDate, $bankRecAutoID, $GLAutoID){
        $data = array(
            'clearedDate' => $clearedDate
        );
        $this->db->or_where('bankRecMonthID', $bankRecAutoID);
        $this->db->or_where('clearedYN', 0);
        $this->db->where('bankGLAutoID', $GLAutoID);
        $result = $this->db->update('srp_erp_bankledger', $data);

            if($result){
                return array('s','Updated Successfully ');
            }else{
                return array('e','Update failed');
            }
    }

    function updateClearDatebyID($bankLedgerAutoID, $clearedDateFormat){
        $data = array(
            'clearedDate' => $clearedDateFormat
        );
        $this->db->where('bankLedgerAutoID', $bankLedgerAutoID);
        $result = $this->db->update('srp_erp_bankledger', $data);

            if($result){
                return array('s','Updated Successfully ');
            }else{
                return array('e','Update failed');
            }
    }
}