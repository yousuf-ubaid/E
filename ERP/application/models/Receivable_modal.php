<?php
class Receivable_modal extends ERP_Model
{
    function fetch_credit_note_template_data($creditNoteMasterAutoID)
    {
        $convertFormat=convert_date_format_sql();
        $this->db->select('*,srp_erp_creditnotemaster.createdUserName AS createdUserName,DATE_FORMAT(srp_erp_creditnotemaster.createdDateTime,\''.$convertFormat.'\') AS createdDateTime,DATE_FORMAT(srp_erp_creditnotemaster.creditNoteDate,\''.$convertFormat.'\') AS creditNoteDate,DATE_FORMAT(srp_erp_creditnotemaster.approvedDate,\''.$convertFormat.' %h:%i:%s\') AS approvedDate,CASE WHEN srp_erp_creditnotemaster.confirmedYN = 2 || srp_erp_creditnotemaster.confirmedYN = 3   THEN " - " WHEN srp_erp_creditnotemaster.confirmedYN = 1 THEN 
                CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn, srp_erp_company.companyVatNumber, srp_erp_customermaster.vatIdNo');
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->from('srp_erp_creditnotemaster');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_creditnotemaster.companyID', 'Left');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID', 'Left');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('customerName,customerAddress1,customerTelephone,customerSystemCode,customerFax');
        $this->db->where('customerAutoID',$data['master']['customerID']);
        $this->db->from('srp_erp_customermaster');
        $data['customer'] = $this->db->get()->row_array();

        $companyID = current_companyID();
        $documentID = 'CN';
        $documentMasterAutoID = $creditNoteMasterAutoID;
        $data['tax'] = $this->db->query("SELECT
                                    srp_erp_taxmaster.taxShortCode,
                                    srp_erp_taxmaster.taxDescription,
	                                SUM(amount) as taxAmount,
	                                srp_erp_taxledger.taxPercentage,
                                    srp_erp_taxmaster.taxCategory
                                    FROM
	                                `srp_erp_taxledger`
	                                JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                                WHERE 
	                                srp_erp_taxledger.companyID = {$companyID} 
	                                AND documentID IN ('{$documentID}')
	                                AND documentMasterAutoID = {$documentMasterAutoID}
	                                GROUP BY 
	                                taxMasterID")->result_array();

        $this->db->select('IFNULL(taxLedgerDetails.amount,0) as amount, IFNULL(taxLedgerDetails.taxPercentage,0) as taxpercentageLedger, creditNoteMasterAutoID,creditNoteDetailsID,invoiceAutoID,GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,customerAmount,description,isFromInvoice,invoiceSystemCode, IFNULL(taxAmount, 0) as taxAmount');
        // $this->db->group_by("GLCode"); 
        // $this->db->group_by("segmentCode"); 
        $this->db->where('creditNoteMasterAutoID', $creditNoteMasterAutoID);
        $this->db->join('(SELECT
                              SUM(amount) as amount,
                              srp_erp_taxledger.taxPercentage,
                              documentDetailAutoID
	                          FROM
                              srp_erp_taxledger 
	                          LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                          WHERE documentID = "CN" 
	                          AND taxCategory = 2 
	                          GROUP BY documentID,documentDetailAutoID)taxLedgerDetails','taxLedgerDetails.documentDetailAutoID = srp_erp_creditnotedetail.creditNoteDetailsID','left');
        $this->db->from('srp_erp_creditnotedetail');
        $data['detail'] = $this->db->get()->result_array();
        
        return $data;
    }

    function fetch_customer_data($customerID){
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID); 
        return $this->db->get()->row_array();
    }

    function save_creditnote_header(){
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $cDate = $this->input->post('cnDate');
        $cnDate = input_format_date($cDate,$date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        //$period          = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $currency_code   = explode('|', trim($this->input->post('currency_code') ?? ''));
        if($financeyearperiodYN==1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        }else{
            $financeYearDetails=get_financial_year($cnDate);
            if(empty($financeYearDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                $FYBegin=$financeYearDetails['beginingDate'];
                $FYEnd=$financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails=get_financial_period_date_wise($cnDate);

            if(empty($financePeriodDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $customer_arr                               = $this->fetch_customer_data(trim($this->input->post('customer') ?? ''));
        $data['documentID']                         = 'CN';
        $data['companyFinanceYearID']               = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear']                 = trim($this->input->post('companyFinanceYear') ?? '');
        $data['creditNoteDate']                     = trim($cnDate);
        $data['companyFinancePeriodID']                     = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom']                   = trim($period[0] ?? '');
        $data['FYPeriodDateTo']                     = trim($period[1] ?? '');*/
        $data['customerID']                         = trim($this->input->post('customer') ?? '');
        $data['customerCode']                       = $customer_arr['customerSystemCode'];
        $data['customerName']                       = $customer_arr['customerName'];
        $data['customerAddress']                    = $customer_arr['customerAddress1'];
        $data['customerTelephone']                  = $customer_arr['customerTelephone'];
        $data['customerFax']                        = $customer_arr['customerFax'];
        $data['customerReceivableAutoID']           = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode']     = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount']        = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription']      = $customer_arr['receivableDescription'];
        $data['customerReceivableType']             = $customer_arr['receivableType'];
        $data['customerCurrencyID']                 = $customer_arr['customerCurrencyID'];
        $data['customerCurrency']                   = $customer_arr['customerCurrency'];
        $data['FYBegin']                            = trim($FYBegin);
        $data['FYEnd']                              = trim($FYEnd);
        $data['docRefNo']                           = trim($this->input->post('referenceno') ?? '');
        $comments                                   = ($this->input->post('comments'));
        $data['comments']                           = str_replace('<br />', PHP_EOL, $comments);
        $data['isGroupBasedTax']                    = getPolicyValues('GBT', 'All');
        //$data['comments']                           = trim($this->input->post('comments') ?? '');
        $data['transactionCurrencyID']                  = trim($this->input->post('customer_currencyID') ?? '');
        $data['transactionCurrency']                    = trim($currency_code[0] ?? '');
        $data['transactionExchangeRate']                = 1;
        $data['transactionCurrencyDecimalPlaces']       = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID']                 = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
        $default_currency      = currency_conversionID($data['transactionCurrencyID'],$data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate']               = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces']      = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID']             = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency    = currency_conversionID($data['transactionCurrencyID'],$data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate']           = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];
        $customer_currency    = currency_conversionID($data['transactionCurrencyID'],$data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate']           = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces']          = $customer_currency['DecimalPlaces'];
        $data['modifiedPCID']                       = $this->common_data['current_pc'];
        $data['modifiedUserID']                     = $this->common_data['current_userID'];
        $data['modifiedUserName']                   = $this->common_data['current_user'];
        $data['modifiedDateTime']                   = $this->common_data['current_date'];

        if (trim($this->input->post('creditNoteMasterAutoID') ?? '')) {
            $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
            $this->db->update('srp_erp_creditnotemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('creditNoteMasterAutoID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            //$data['creditNoteCode']      = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_creditnotemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_inv_tax_detail(){
        $this->db->select('*');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_customerinvoicetaxdetails')->row_array();
        if (!empty($tax_detail)) {
            $this->session->set_flashdata('w', 'Tax Detail added already ! ');
            return array('status' => true);
        }
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID,companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('invoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

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
            $this->db->update('srp_erp_customerinvoicetaxdetails', $data);
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
            $this->db->insert('srp_erp_customerinvoicetaxdetails', $data);
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

    function load_credit_note_header()
    {
        update_group_based_tax('srp_erp_creditnotemaster', 'creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'), null, null, 'CN');

        $convertFormat=convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(creditNoteDate,\''.$convertFormat.'\') AS creditNoteDate,DATE_FORMAT(FYPeriodDateFrom,"%Y-%m-%d") AS FYPeriodDateFrom,DATE_FORMAT(FYPeriodDateTo,"%Y-%m-%d") AS FYPeriodDateTo');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        return $this->db->get('srp_erp_creditnotemaster')->row_array();
    }

    function fetch_cn_detail_table(){
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,customerCurrency,customerCurrencyDecimalPlaces, isGroupBasedTax');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_creditnotemaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_creditnotedetail');trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_cn_detail(){
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_creditnotemaster')->row_array();
        $segment                                 = explode('|', trim($this->input->post('segment_gl') ?? ''));
        $gl_code                                 = explode(' | ', trim($this->input->post('gl_code_des') ?? ''));
        $data['creditNoteMasterAutoID']          = trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $data['GLAutoID']                        = trim($this->input->post('gl_code') ?? '');
        $data['systemGLCode']                    = trim($gl_code[0] ?? '');
        $data['GLCode']                          = trim($gl_code[1] ?? '');
        $data['GLDescription']                   = trim($gl_code[2] ?? '');
        $data['GLType']                          = trim($gl_code[3] ?? '');
        $data['segmentID']                       = trim($segment[0] ?? '');
        $data['segmentCode']                     = trim($segment[1] ?? '');
        $data['transactionAmount']               = trim($this->input->post('amount') ?? '');
        $data['companyLocalAmount']              = ($data['transactionAmount']/$master['companyLocalExchangeRate']);
        $data['companyReportingAmount']          = ($data['transactionAmount']/$master['companyReportingExchangeRate']);
        $data['customerAmount']                  = ($data['transactionAmount']/$master['customerCurrencyExchangeRate']);
        $data['description']                     = trim($this->input->post('description') ?? '');
        $data['modifiedPCID']                    = $this->common_data['current_pc'];
        $data['modifiedUserID']                  = $this->common_data['current_userID'];
        $data['modifiedUserName']                = $this->common_data['current_user'];
        $data['modifiedDateTime']                = $this->common_data['current_date'];

        if (trim($this->input->post('creditNoteDetailsID') ?? '')) {
            $this->db->where('creditNoteDetailsID', trim($this->input->post('creditNoteDetailsID') ?? ''));
            $this->db->update('srp_erp_creditnotedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Detail : ' . $data['GLDescription']. ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('creditNoteDetailsID'));
            }
        } else {
            $data['companyCode']        = $this->common_data['company_data']['company_code'];
            $data['companyID']          = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup']   = $this->common_data['user_group'];
            $data['createdPCID']        = $this->common_data['current_pc'];
            $data['createdUserID']      = $this->common_data['current_userID'];
            $data['createdUserName']    = $this->common_data['current_user'];
            $data['createdDateTime']    = $this->common_data['current_date'];
            $this->db->insert('srp_erp_creditnotedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Credit Note Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Credit Note Detail : ' . $data['GLDescription']. ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function cn_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $locationemployee = $this->common_data['emplanglocationid'];
        $this->db->select('creditNoteMasterAutoID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_creditnotedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else{
        $this->db->select('creditNoteMasterAutoID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_creditnotemaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Document already confirmed');
        } else {
            $system_code = trim($this->input->post('creditNoteMasterAutoID') ?? '');
            $this->db->select('documentID, creditNoteCode,DATE_FORMAT(creditNoteDate, "%Y") as invYear,DATE_FORMAT(creditNoteDate, "%m") as invMonth,companyFinanceYearID');
            $this->db->where('creditNoteMasterAutoID', $system_code);
            $this->db->from('srp_erp_creditnotemaster');
            $master_dt = $this->db->get()->row_array();
            $this->load->library('sequence');
            if($master_dt['creditNoteCode'] == "0") {
                if($locationwisecodegenerate == 1)
                {
                    $this->db->select('locationID');
                    $this->db->where('EIdNo', $currentuser);
                    $this->db->where('Erp_companyID', $companyID);
                    $this->db->from('srp_employeesdetails');
                    $location = $this->db->get()->row_array();
                    if ((empty($location)) || ($location =='')) {
                        return array('w', 'Location is not assigned for current employee');
                    }else {
                        if($locationemployee!='') {
                            $codegeratorcrn = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'],$locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                        }else {
                            return array('w', 'Location is not assigned for current employee');
                        }
                    }
                }else {
                    $codegeratorcrn = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                }

                $validate_code = validate_code_duplication($codegeratorcrn, 'creditNoteCode', $system_code,'creditNoteMasterAutoID', 'srp_erp_creditnotemaster');
                if(!empty($validate_code)) {
                    return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                }
                $pvCd = array(
                    'creditNoteCode' => $codegeratorcrn
                );
                $this->db->where('creditNoteMasterAutoID', $system_code);
                $this->db->update('srp_erp_creditnotemaster', $pvCd);
            } else {
                $validate_code = validate_code_duplication($master_dt['creditNoteCode'], 'creditNoteCode', $system_code,'creditNoteMasterAutoID', 'srp_erp_creditnotemaster');
                if(!empty($validate_code)) {
                    return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                }
            }

            $this->load->library('Approvals');
            $this->db->select('creditNoteMasterAutoID, creditNoteCode,creditNoteDate');
            $this->db->where('creditNoteMasterAutoID', $system_code);
            $this->db->from('srp_erp_creditnotemaster');
            $cn_data = $this->db->get()->row_array();

            $autoApproval= get_document_auto_approval('CN');
            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($cn_data['creditNoteMasterAutoID'], 'srp_erp_creditnotemaster','creditNoteMasterAutoID', 'CN',$cn_data['creditNoteCode'],$cn_data['creditNoteDate']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval('CN',$cn_data['creditNoteMasterAutoID'],$cn_data['creditNoteCode'],'Credit note','srp_erp_creditnotemaster','creditNoteMasterAutoID',0,$cn_data['creditNoteDate']);
            }else{
                return array('e', 'Approval levels are not set for this document');
            }

            if ($approvals_status==1) {
                $autoApproval= get_document_auto_approval('CN');
                if($autoApproval==0) {
                    $result = $this->save_cn_approval(0, $cn_data['creditNoteMasterAutoID'], 1, 'Auto Approved');
                    if($result){
                        $this->db->trans_commit();
                        return array('s', 'Document confirmed Successfully');
                    }
                }else{
                    $data = array(
                        'confirmedYN'        => 1,
                        'confirmedDate'      => $this->common_data['current_date'],
                        'confirmedByEmpID'   => $this->common_data['current_userID'],
                        'confirmedByName'    => $this->common_data['current_user']
                    );

                    $this->db->where('creditNoteMasterAutoID', $system_code);
                    $result =  $this->db->update('srp_erp_creditnotemaster', $data);
                    if($result) {
                        return array('s', 'Document confirmed Successfully');
                    }
                }
            }else if($approvals_status==3){
                return array('w', 'There are no users exist to perform approval for this document.');
            }else{
                return array('e', 'Document confirmation failed');
            }
        }
        }
    }

    function delete_tax_detail(){
        $this->db->delete('srp_erp_customerinvoicetaxdetails',array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function delete_cn_detail()
    {
        $creditNoteDetailsID = trim($this->input->post('creditNoteDetailsID') ?? '');
        $creditNoteMasterAutoID = $this->db->query("SELECT creditNoteMasterAutoID FROM srp_erp_creditnotedetail WHERE creditNoteDetailsID = $creditNoteDetailsID")->row('creditNoteMasterAutoID');
        $this->db->delete('srp_erp_taxledger', array('documentID' => 'CN','documentMasterAutoID' => $creditNoteMasterAutoID,'documentDetailAutoID' => trim($this->input->post('creditNoteDetailsID') ?? '')));
       
        $this->db->select('invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteDetailsID', trim($this->input->post('creditNoteDetailsID') ?? '')); 
        $detail_arr = $this->db->get()->row_array();
        $company_id = $this->common_data['company_data']['company_id'];
        $match_id   = $detail_arr['invoiceAutoID'];
        $number     = $detail_arr['transactionAmount'];
        $status     = 0;
        $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount -{$number}),receiptInvoiceYN = 0  WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        $this->db->delete('srp_erp_creditnotedetail', array('creditNoteDetailsID' => trim($this->input->post('creditNoteDetailsID') ?? '')));
        $this->session->set_flashdata('s', 'Credit Note Detail Deleted Successfully');
        return true;
    }

    function save_cn_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0){
        $this->db->trans_start();
        $this->load->library('Approvals');
        if($autoappLevel==1) {
            $system_code = trim($this->input->post('creditNoteMasterAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['creditNoteMasterAutoID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }
        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code,$level_id,$status,$comments,'CN');
        }

        if ($approvals_status==1) {
            $this->load->model('Double_entry_model');
            $double_entry  = $this->Double_entry_model->fetch_double_entry_credit_note_data($system_code,'CN');
            for ($i=0; $i < count($double_entry['gl_detail']); $i++) { 
                $generalledger_arr[$i]['documentMasterAutoID']                      = $double_entry['master_data']['creditNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode']                              = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode']                        = $double_entry['master_data']['creditNoteCode'];
                $generalledger_arr[$i]['documentDate']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentType']                              = '';
                $generalledger_arr[$i]['documentYear']                              = $double_entry['master_data']['creditNoteDate'];
                $generalledger_arr[$i]['documentMonth']                             = date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                $generalledger_arr[$i]['documentNarration']                         = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber']                              = '';
                $generalledger_arr[$i]['transactionCurrency']                       = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionCurrencyID']                     = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionExchangeRate']                   =$double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']          = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency']                      = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID']                    = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate']                  = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']         = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrency']                  = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingCurrencyID']                = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingExchangeRate']              = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']     = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID']                           = '';
                $generalledger_arr[$i]['partyType']                                 = 'CUS';
                $generalledger_arr[$i]['partyAutoID']                               = $double_entry['master_data']['customerID'];
                $generalledger_arr[$i]['partySystemCode']                           = $double_entry['master_data']['customerCode'];
                $generalledger_arr[$i]['partyName']                                 = $double_entry['master_data']['customerName'];
                $generalledger_arr[$i]['partyCurrencyID']                           = $double_entry['master_data']['customerCurrencyID'];
                $generalledger_arr[$i]['partyCurrency']                             = $double_entry['master_data']['customerCurrency'];
                $generalledger_arr[$i]['partyExchangeRate']                         = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces']                = $double_entry['master_data']['customerCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID']                          = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName']                           = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate']                             = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate']                              = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID']                           = $double_entry['master_data']['approvedbyEmpID'];
                $generalledger_arr[$i]['approvedbyEmpName']                         = $double_entry['master_data']['approvedbyEmpName'];
                $generalledger_arr[$i]['companyID']                                 = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode']                               = $double_entry['master_data']['companyCode'];
                $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                if ($double_entry['gl_detail'][$i]['amount_type']=='cr') {
                    $amount =($double_entry['gl_detail'][$i]['gl_cr']*-1);
                }
                $generalledger_arr[$i]['transactionAmount']                         = round($amount,$generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyLocalAmount']                        = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyLocalExchangeRate']),$generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['companyReportingAmount']                    = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['companyReportingExchangeRate']),$generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['partyCurrencyAmount']                       = round(($generalledger_arr[$i]['transactionAmount']/$generalledger_arr[$i]['partyExchangeRate']),$generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                $generalledger_arr[$i]['amount_type']                               = $double_entry['gl_detail'][$i]['amount_type'];
                $generalledger_arr[$i]['documentDetailAutoID']                      = $double_entry['gl_detail'][$i]['auto_id'];
                $generalledger_arr[$i]['GLAutoID']                                  = $double_entry['gl_detail'][$i]['gl_auto_id'];
                $generalledger_arr[$i]['systemGLCode']                              = $double_entry['gl_detail'][$i]['gl_code'];
                $generalledger_arr[$i]['GLCode']                                    = $double_entry['gl_detail'][$i]['secondary'];
                $generalledger_arr[$i]['GLDescription']                             = $double_entry['gl_detail'][$i]['gl_desc'];
                $generalledger_arr[$i]['GLType']                                    = $double_entry['gl_detail'][$i]['gl_type'];
                $generalledger_arr[$i]['segmentID']                                 = $double_entry['gl_detail'][$i]['segment_id'];
                $generalledger_arr[$i]['segmentCode']                               = $double_entry['gl_detail'][$i]['segment'];
                $generalledger_arr[$i]['projectID'] = isset($double_entry['gl_detail'][$i]['projectID']) ? $double_entry['gl_detail'][$i]['projectID'] : null;
                $generalledger_arr[$i]['projectExchangeRate'] = isset($double_entry['gl_detail'][$i]['projectExchangeRate']) ? $double_entry['gl_detail'][$i]['projectExchangeRate'] : null;
                $generalledger_arr[$i]['subLedgerType']                             = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc']                             = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon']                                   = $double_entry['gl_detail'][$i]['isAddon'];
                $generalledger_arr[$i]['createdUserGroup']                          = $this->common_data['user_group'];
                $generalledger_arr[$i]['createdPCID']                               = $this->common_data['current_pc'];
                $generalledger_arr[$i]['createdUserID']                             = $this->common_data['current_userID'];
                $generalledger_arr[$i]['createdDateTime']                           = $this->common_data['current_date'];
                $generalledger_arr[$i]['createdUserName']                           = $this->common_data['current_user'];
                $generalledger_arr[$i]['modifiedPCID']                              = $this->common_data['current_pc'];
                $generalledger_arr[$i]['modifiedUserID']                            = $this->common_data['current_userID'];
                $generalledger_arr[$i]['modifiedDateTime']                          = $this->common_data['current_date'];
                $generalledger_arr[$i]['modifiedUserName']                          = $this->common_data['current_user'];
            }

            if (!empty($generalledger_arr)) {
                $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentMasterAutoID',$system_code);
                $this->db->where('documentCode','CN');
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] !=0 or $totals['companyLocal_total'] !=0 or $totals['companyReporting_total'] !=0 or $totals['party_total'] !=0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    $generalledger_arr['documentMasterAutoID']= $double_entry['master_data']['creditNoteMasterAutoID'];
                    $generalledger_arr['documentCode']        = $double_entry['code'];
                    $generalledger_arr['documentSystemCode']  = $double_entry['master_data']['creditNoteCode'];
                    $generalledger_arr['documentDate']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentType']        = '';
                    $generalledger_arr['documentYear']        = $double_entry['master_data']['creditNoteDate'];
                    $generalledger_arr['documentMonth']=date("m",strtotime($double_entry['master_data']['creditNoteDate']));
                    $generalledger_arr['documentNarration']   = $double_entry['master_data']['docRefNo'];
                    $generalledger_arr['chequeNumber']        = '';
                    $generalledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr['transactionExchangeRate']=$double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr['companyLocalExchangeRate']=$double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr['companyReportingCurrency']=$double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr['partyContractID'] = '';
                    $generalledger_arr['partyType'] = 'CUS';
                    $generalledger_arr['partyAutoID']               = $double_entry['master_data']['customerID'];
                    $generalledger_arr['partySystemCode']           = $double_entry['master_data']['customerCode'];
                    $generalledger_arr['partyName']                 = $double_entry['master_data']['customerName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['customerCurrencyID'];
                    $generalledger_arr['partyCurrency']             = $double_entry['master_data']['customerCurrency'];
                    $generalledger_arr['partyExchangeRate']  = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces']=$double_entry['master_data']['customerCurrencyDecimalPlaces'];
                    $generalledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $generalledger_arr['companyCode'] = $double_entry['master_data']['companyCode'];
                    $generalledger_arr['transactionAmount'] = round(($totals['transaction_total']* -1), $generalledger_arr['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr['companyLocalAmount'] = round(($totals['companyLocal_total']* -1), $generalledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr['companyReportingAmount'] = round(($totals['companyReporting_total']* -1), $generalledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr['partyCurrencyAmount'] = round(($totals['party_total']* -1), $generalledger_arr['partyCurrencyDecimalPlaces']);
                    $generalledger_arr['amount_type'] = null;
                    $generalledger_arr['documentDetailAutoID'] = 0;
                    $generalledger_arr['GLAutoID'] = $ERGL_ID;
                    $generalledger_arr['systemGLCode'] = $ERGL['systemAccountCode'];
                    $generalledger_arr['GLCode'] = $ERGL['GLSecondaryCode'];
                    $generalledger_arr['GLDescription'] = $ERGL['GLDescription'];
                    $generalledger_arr['GLType'] = $ERGL['subCategory'];
                    $seg = explode('|',$this->common_data['company_data']['default_segment']);
                    $generalledger_arr['segmentID'] = $seg[0];
                    $generalledger_arr['segmentCode'] = $seg[1];
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

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];

            // $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
            // $this->db->update('srp_erp_creditnotemaster', $data);

            $this->session->set_flashdata('s', 'Credit Note Approval Successfully.');
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

    function fetch_custemer_data_invoice(){

        $this->load->library('Pagination');

        $this->db->select('creditNoteDate,creditNoteMasterAutoID,customerID,transactionCurrency, transactionCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_creditnotemaster');
        $data['master'] = $this->db->get()->row_array();

        /*$this->db->select('invoiceAutoID,invoiceCode,invoiceDate,receiptTotalAmount,transactionCurrency,transactionCurrencyID, creditNoteTotalAmount, transactionAmount');
        $this->db->where('invoiceDate <=', $data['master']['creditNoteDate']);
        $this->db->where('customerID', $data['master']['customerID']);
        $this->db->where('transactionCurrency', $data['master']['transactionCurrency']);
        $this->db->where('receiptInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_customerinvoicemaster');*/
        $output_count = $this->db->query("SELECT TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((balance_cr)), transactionCurrencyDecimalPlaces)))))) balance FROM (SELECT
                                             (((((((cid.transactionAmount-retensionTransactionAmount) - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) - IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) ) - (IFNULL(receiptTotalAmount,0)  + IFNULL(creditNoteTotalAmount,0) + IFNULL(advanceMatchedTotal,0)  +  IFNULL(returnsalesvalue,0)  ))AS balance_cr,
                                             transactionCurrencyDecimalPlaces
                                        
                                              FROM srp_erp_customerinvoicemaster 
                                              LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID 
                                              LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                              LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                              LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                              LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                              LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                                              WHERE confirmedYN = 1 
                                              AND approvedYN = 1 
                                              AND receiptInvoiceYN = 0 
                                              AND `customerID` = '{$data['master']['customerID']}' 
                                              AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' 
                                              AND invoiceDate <= '{$data['master']['creditNoteDate']}')t1 HAVING balance > 0")->result_array();




        $totalCount = count($output_count);
        $data_pagination = $this->input->post('pageID');
        $per_page = 500;
        $config = array();
        $config["base_url"] = "#employee-list";
        $config["total_rows"] = $totalCount;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $page = (!empty($data_pagination)) ? (($data_pagination - 1) * $per_page) : 0;
        $data["empCount"] = $totalCount;
        $data["pagination"] = $this->pagination->create_links_employee_master();
        $data["per_page"] = $per_page;
        //$output = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID,invoiceCode,receiptTotalAmount,advanceMatchedTotal,creditNoteTotalAmount,referenceNo ,( ( cid.transactionAmount - cid.totalAfterTax ) * ( IFNULL( tax.taxPercentage, 0 ) / 100 ) + IFNULL( cid.transactionAmount, 0 ) ) as transactionAmount,invoiceDate  FROM srp_erp_customerinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount,IFNULL(SUM(totalAfterTax ),0) as totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$data['master']['customerID']}' AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' AND invoiceDate <= '{$data['master']['creditNoteDate']}' ")->result_array();
        $output = $this->db->query("SELECT *,TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(((balance_cr)), transactionCurrencyDecimalPlaces)))))) balance FROM(SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, ((( ( ( (cid.transactionAmount-retensionTransactionAmount) - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) - IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) ) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue,
                    (((((((cid.transactionAmount-retensionTransactionAmount) - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL((cid.transactionAmount-retensionTransactionAmount), 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) - IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) ) - (IFNULL(receiptTotalAmount,0)  + IFNULL(creditNoteTotalAmount,0) + IFNULL(advanceMatchedTotal,0)  +  IFNULL(returnsalesvalue,0)  ))AS balance_cr,transactionCurrencyDecimalPlaces
                    FROM srp_erp_customerinvoicemaster 
                    LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID 
                    LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                    LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                    LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                    LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                    LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID 
                    WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$data['master']['customerID']}' 
                        AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' AND invoiceDate <= '{$data['master']['creditNoteDate']}')tbl1 HAVING balance > 0 LIMIT {$page},{$per_page}")->result_array();
        $data['detail'] = $output;
        return $data;      
    }

    function save_credit_base_items(){
        $projectExist = project_is_exist();
        $this->db->trans_start();
        $creditNoteMasterAutoID = trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $invoice_id     = $this->input->post('invoiceAutoID');
        $segments       = $this->input->post('segment');
        $gl_code_d      = $this->input->post('gl_code_dec');
        $amounts        = $this->input->post('amounts');
        $gl_codes       = $this->input->post('gl_code');
        $code           = $this->input->post('invoiceCode');
        $projectID      = $this->input->post('project');
        $project_subCategoryID      = $this->input->post('project_subCategoryID');
        $project_categoryID      = $this->input->post('project_categoryID');
        for($i=0; $i < count($invoice_id); $i++) {
            $this->db->select('(transactionAmount-retensionTransactionAmount) as transactionAmount, receiptTotalAmount, creditNoteTotalAmount, advanceMatchedTotal, companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate,transactionCurrencyID');
            $this->db->where('invoiceAutoID', $invoice_id[$i]);
            $master = $this->db->get('srp_erp_customerinvoicemaster')->row_array();

            $segment                                     = explode('|', $segments[$i]);
            $gl_code_des                                 = explode('|', $gl_code_d[$i]);
            $data[$i]['creditNoteMasterAutoID']          = $creditNoteMasterAutoID;
            $data[$i]['invoiceAutoID']                   = $invoice_id[$i];
            $data[$i]['invoiceSystemCode']               = $code[$i];
            $data[$i]['GLAutoID']                        = $gl_codes[$i];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$i]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data[$i]['projectID'] = $projectID[$i];
                $data[$i]['project_subCategoryID'] = $project_subCategoryID[$i];
                $data[$i]['project_categoryID'] = $project_categoryID[$i];
                $data[$i]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$i]['projectID']                       = $projectID[$i] ?? '';
            $data[$i]['systemGLCode']                    = trim($gl_code_des[0] ?? '');
            $data[$i]['GLCode']                          = trim($gl_code_des[1] ?? '');
            $data[$i]['GLDescription']                   = trim($gl_code_des[2] ?? '');
            $data[$i]['GLType']                          = trim($gl_code_des[3] ?? '');
            $data[$i]['segmentID']                       = trim($segment[0] ?? '');
            $data[$i]['segmentCode']                     = trim($segment[1] ?? '');
            $data[$i]['transactionAmount']               = $amounts[$i];
            $data[$i]['companyLocalAmount']              = ($data[$i]['transactionAmount']/$master['companyLocalExchangeRate']);
            $data[$i]['companyLocalExchangeRate']        = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingAmount']          = ($data[$i]['transactionAmount']/$master['companyReportingExchangeRate']);
            $data[$i]['companyReportingExchangeRate']    = $master['companyReportingExchangeRate'];
            $data[$i]['customerAmount']                  = ($data[$i]['transactionAmount']/$master['customerCurrencyExchangeRate']);
            $data[$i]['customerCurrencyExchangeRate']    = $master['customerCurrencyExchangeRate'];
            $data[$i]['description']                     = trim($this->input->post('description') ?? '');
            $data[$i]['modifiedPCID']                    = $this->common_data['current_pc'];
            $data[$i]['modifiedUserID']                  = $this->common_data['current_userID'];
            $data[$i]['modifiedUserName']                = $this->common_data['current_user'];
            $data[$i]['modifiedDateTime']                = $this->common_data['current_date']; 
            $data[$i]['companyID']                       = $this->common_data['company_data']['company_id'];
            $data[$i]['companyCode']                     = $this->common_data['company_data']['company_code'];
            $data[$i]['createdUserGroup']                = $this->common_data['user_group'];
            $data[$i]['createdPCID']                     = $this->common_data['current_pc'];
            $data[$i]['createdUserID']                   = $this->common_data['current_userID'];
            $data[$i]['createdUserName']                 = $this->common_data['current_user'];
            $data[$i]['createdDateTime']                 = $this->common_data['current_date'];

            $id          = $data[$i]['invoiceAutoID'];
            //    $amo         = $data[$i]['transactionAmount'];
            //    $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount+{$amo}) WHERE invoiceAutoID='{$id}'");

            $amo['creditNoteTotalAmount']         = $master['creditNoteTotalAmount'] + $data[$i]['transactionAmount'];
            $balanceAmount                        = $master['transactionAmount'] - ($master['creditNoteTotalAmount'] + $master['receiptTotalAmount'] + $master['advanceMatchedTotal'] + $data[$i]['transactionAmount']);
            if ($balanceAmount <= 0) {
                $amo['receiptInvoiceYN'] = 1;
            }

            $this->db->where('invoiceAutoID', $id);
            $this->db->update('srp_erp_customerinvoicemaster', $amo);

        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_creditnotedetail', $data); 


            /** Added (SME-2988)*/
            $details = $this->db->query("SELECT * FROM srp_erp_creditnotedetail WHERE creditNoteMasterAutoID = $creditNoteMasterAutoID")->result_array();

            $companyID = current_companyID();
            foreach ($details as $det) {
                $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                                FROM srp_erp_taxledger 
                                                WHERE documentID = 'CN' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['creditNoteDetailsID']}"
                                            )->row('taxledgerID');



                if($dataExist == 0 && !empty($det['invoiceAutoID'])) {
                    // echo 'as';
                    /*srp_erp_taxmaster.taxCategory = 2
                    AND*/
                    $ledgerDet = $this->db->query("SELECT
                                                            customerCountryID,
                                                            vatEligible,
                                                            customerID,
                                                            srp_erp_taxledger.*,CASE 
                                                                WHEN taxCategory = 2 THEN outputVatGLAccountAutoID
                                                                ELSE taxGlAutoID 
                                                            END AS outputVatGLAccountAutoID,
                                                            outputVatTransferGLAccountAutoID,
                                                            transactionAmount,
                                                            IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable
                                                        FROM
                                                            srp_erp_taxledger
                                                            JOIN (
                                                                SELECT
                                                                    SUM( srp_erp_customerinvoicedetails.transactionAmount ) AS transactionAmount,
                                                                    srp_erp_customerinvoicedetails.invoiceAutoID,
                                                                    customerID 
                                                                FROM
                                                                    srp_erp_customerinvoicedetails
                                                                    JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID 
                                                                GROUP BY
                                                                    invoiceAutoID 
                                                            ) mastertbl ON mastertbl.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = 'CINV'
                                                            LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = mastertbl.customerID
                                                            JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                                            JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                                                        WHERE
                                                             invoiceAutoID = {$det['invoiceAutoID']}")->result_array();

        
                    if(!empty($ledgerDet)) {
                        $taxAmount = 0;
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'CN';
                            $dataleg['documentMasterAutoID'] = $creditNoteMasterAutoID;
                            $dataleg['documentDetailAutoID'] = $det['creditNoteDetailsID'];
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = $val['taxPercentage'];
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $dataleg['amount'] = ($val['amount'] / $val['transactionAmount']) * $det['transactionAmount'];
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['outputVatGLAccountAutoID'];
                            $dataleg['transferGLAutoID'] = null;
                            $dataleg['isClaimable'] = $val['isClaimable'];
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
                            $ledgerEntry = $this->db->insert('srp_erp_taxledger', $dataleg);

                            $taxAmount += ($val['amount'] / $val['transactionAmount']) * $det['transactionAmount'];
                        }
                        $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                        $data_detailTBL['taxAmount'] = $taxAmount;
                        $this->db->where('creditNoteDetailsID', $det['creditNoteDetailsID']);
                        $this->db->update('srp_erp_creditnotedetail', $data_detailTBL);
                    }
                }
            }
            /** End (SME-2988)*/
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

    function delete_creditNote_attachement(){
        $attachmentID=$this->input->post('attachmentID');
        $myFileName=$this->input->post('myFileName');
        $url= base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH.$link))
        {
            return false;
        }
        else
        {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_creditNote_master(){
        /*$this->db->select('invoiceAutoID,transactionAmount');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach($detail_arr as $val_as){
            $match_id   = $val_as['invoiceAutoID'];
            $number     = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_customerinvoicemaster SET creditNoteTotalAmount = (creditNoteTotalAmount - {$number}) WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }
        $this->db->delete('srp_erp_creditnotemaster', array('creditNoteMasterAutoID' => trim($this->input->post('creditNoteMasterAutoID') ?? '')));
        $this->db->delete('srp_erp_creditnotedetail', array('creditNoteMasterAutoID' => trim($this->input->post('creditNoteMasterAutoID') ?? '')));
        return true;*/

        $masterID = trim($this->input->post('creditNoteMasterAutoID') ?? '');

        $this->db->select('*');
        $this->db->from('srp_erp_creditnotedetail');
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $datas= $this->db->get()->row_array();
        if($datas){
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        }else{
        //    $data = array(
        //        'isDeleted' => 1,
        //        'deletedEmpID' => current_userID(),
        //        'deletedDate' => current_date(),
        //    );
        //    $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        //    $this->db->update('srp_erp_creditnotemaster', $data);
        //    $this->session->set_flashdata('s', 'Deleted Successfully.');
        //    return true;
           
            $documentCode = $this->db->get_where('srp_erp_creditnotemaster', ['creditNoteMasterAutoID'=> $masterID])->row('creditNoteCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
                $this->db->update('srp_erp_creditnotemaster', $data);
            }
            else{
                $this->db->where('creditNoteMasterAutoID', $masterID)->delete('srp_erp_creditnotedetail');
                $this->db->where('creditNoteMasterAutoID', $masterID)->delete('srp_erp_creditnotemaster');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');

                return false;
            }
           
        }
    }

    function re_open_credit_note(){
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('creditNoteMasterAutoID', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
        $this->db->update('srp_erp_creditnotemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }
    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'CN');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();
    }
    function save_crditNote_detail_GLCode_multiple()
    {
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_creditnotemaster',trim($this->input->post('creditNoteMasterAutoID') ?? ''),'CN', 'creditNoteMasterAutoID');
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $this->db->select('*');
        $this->db->where('creditNoteMasterAutoID', $this->input->post('creditNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_creditnotemaster')->row_array();

        $gl_codes = $this->input->post('gl_code_array');
        $gl_code_des = $this->input->post('gl_code_des');
        $projectID = $this->input->post('projectID');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        $gl_texts = $this->input->post('gl_text');

        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data['creditNoteMasterAutoID'] = trim($this->input->post('creditNoteMasterAutoID') ?? '');
            $data['GLAutoID'] = $gl_codes[$key];
            if($projectExist == 1){
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'],$projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            $data['description'] = $descriptions[$key];
            $data['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $customerAmount = $data['transactionAmount'] / $master['customerCurrencyExchangeRate'];
            $data['customerAmount'] = round($customerAmount, $master['customerCurrencyDecimalPlaces']);
            $data['customerCurrencyExchangeRate'] = $master['customerCurrencyExchangeRate'];

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['isFromInvoice'] = 0;

            $this->db->insert('srp_erp_creditnotedetail', $data);
            $last_id = $this->db->insert_id();

            if($isGroupByTax == 1){ 
                if(!empty($gl_texts[$key])){
                    $this->db->select('*');
                    $this->db->where('taxCalculationformulaID',$gl_texts[$key]);
                    $tax_master = $this->db->get('srp_erp_taxcalculationformulamaster')->row_array();

                    $dataTax['receiptVoucherAutoId'] = trim($this->input->post('receiptVoucherAutoId') ?? '');
                    $dataTax['taxFormulaMasterID'] = $gl_texts[$key];
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

                    tax_calculation_vat(null,$dataTax,$gl_texts[$key],'creditNoteMasterAutoID',trim($this->input->post('creditNoteMasterAutoID') ?? ''),$amount[$key],'CN',$last_id, 0, 1);
                }             
            }


        }
        // $this->db->insert_batch('srp_erp_creditnotedetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Credit Note Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Credit Note Detail : Saved Successfully.');
        }
    }

    function fetch_details_invoice_overdue_report()
    {
       $companyID = current_companyID();
        $customer = $this->input->post("customerAutoID");
        if (!empty($customer)) { /*generate the query according to selected customer*/
            $customerOR = " srp_erp_customerinvoicemaster.customerID IN ( '" . join("' , '", $customer) . "' )";
        }

        $fields = "";
        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $retentionamount = "";
        $decimalPlc = "";
        $fieldrebate = "";
        $having = array();
        $currency = $this->input->post("currency");
        if (isset($currency)) {
                if ($currency == 'transactionAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as ' . $currency . 'currency,';
                    $fields .= 'TC.DecimalPlaces as ' . $currency . 'DecimalPlaces,';
                    $decimalPlc = 'TC.DecimalPlaces';
                    $retentionamount .= '(IFNULL(retensionTransactionAmount,0))';
                    $fieldrebate .= 'IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) ';

                } else if ($currency == 'companyReportingAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as ' . $currency . 'currency,';
                    $fields .= 'CL.DecimalPlaces as ' . $currency . 'DecimalPlaces,';
                    $decimalPlc = 'CL.DecimalPlaces';
                    $retentionamount .= '(IFNULL(retensionReportingAmount,0))';
                    $fieldrebate .= '(IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) / srp_erp_customerinvoicemaster.companyReportingExchangeRate)  ';

                } else if ($currency == 'companyLocalAmount') {
                    $fields .= 'srp_erp_customerinvoicemaster.companyLocalCurrency as ' . $currency . 'currency,';
                    $fields .= 'CR.DecimalPlaces as ' . $currency . 'DecimalPlaces,';
                    $decimalPlc = 'CR.DecimalPlaces';
                    $retentionamount .= '(IFNULL(retensionLocalAmount,0))';
                    $fieldrebate .= '(IFNULL(srp_erp_customerinvoicemaster.rebateAmount, 0) / srp_erp_customerinvoicemaster.companyLocalExchangeRate)  ';

                }

                $fields .= '(SUM(srp_erp_customerinvoicemaster.' . $currency . ' - '.$retentionamount.' - ' . $fieldrebate .')) as ' . $currency . ',';
                $fields .= '(IFNULL(SUM(pvd.' . $currency . '),0)+IFNULL(SUM(cnd.' . $currency . '),0)+IFNULL(SUM(ca.' . $currency . '),0)) as paid' . $currency . ',';
                $fields .= 'ROUND((SUM(srp_erp_customerinvoicemaster.' . $currency . ' - '.$retentionamount.' - ' . $fieldrebate .') - (IFNULL(SUM(pvd.' . $currency . '),0)+IFNULL(SUM(cnd.' . $currency . '),0)+IFNULL(SUM(ca.' . $currency . '),0))), ' . $decimalPlc . ') as balance' . $currency . ',';
                $fields4 .= 'IFNULL(SUM(srp_erp_customerreceiptdetail.' . $currency . '),0) as ' . $currency . ',';
                $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $currency . ') as ' . $currency . ',';
                $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $currency . ') as ' . $currency . ',';
                $having[] = 'balance' . $currency . '!= -0 AND ' . 'balance' . $currency . ' != 0';
        }

        $result = $this->db->query("SELECT $fields 
                    srp_erp_customerinvoicemaster.invoiceAutoID,
                    srp_erp_customerinvoicemaster.invoiceCode AS bookingInvCode,
                    srp_erp_customerinvoicemaster.invoiceDate AS bookingDate,
                    srp_erp_customerinvoicemaster.invoiceDueDate AS invoiceDueDate,
                    CONCAT( srp_erp_customerinvoicemaster.customerSystemCode, ' | ', srp_erp_customerinvoicemaster.customerName ) AS customer,
                    srp_erp_customerinvoicemaster.referenceNo AS referenceNo
                FROM `srp_erp_customerinvoicemaster`
            
                LEFT JOIN 
                (
                    SELECT $fields4 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID
                    FROM srp_erp_customerreceiptdetail
                    INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1
                    WHERE `srp_erp_customerreceiptdetail`.`companyID` = {$companyID} AND srp_erp_customerreceiptmaster.RVDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' GROUP BY srp_erp_customerreceiptdetail.invoiceAutoID
                ) pvd ON (pvd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`)
                LEFT JOIN
                (
                    SELECT $fields5 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID
                    FROM srp_erp_creditnotedetail
                    INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
                    WHERE `srp_erp_creditnotedetail`.`companyID` = {$companyID} AND srp_erp_creditnotemaster.creditNoteDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' GROUP BY srp_erp_creditnotedetail.invoiceAutoID
                ) cnd ON (cnd.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID`)
                LEFT JOIN( 
                    SELECT $fields6 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID
                    FROM srp_erp_rvadvancematchdetails 
                    INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
                    WHERE `srp_erp_rvadvancematchdetails`.`companyID` = {$companyID} GROUP BY srp_erp_rvadvancematchdetails.InvoiceAutoID
                ) ca ON (ca.`InvoiceAutoID` = `srp_erp_customerinvoicemaster`.`InvoiceAutoID`)
                LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CR ON (CR.currencyID = srp_erp_customerinvoicemaster.companyReportingCurrencyID)
                LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) CL ON (CL.currencyID = srp_erp_customerinvoicemaster.companyLocalCurrencyID) 
                LEFT JOIN (SELECT DecimalPlaces,currencyID FROM srp_erp_currencymaster) TC ON (TC.currencyID = srp_erp_customerinvoicemaster.transactionCurrencyID) 
                WHERE $customerOR 
                   AND `srp_erp_customerinvoicemaster`.`companyID` = " . $this->common_data['company_data']['company_id'] . " 
                   AND srp_erp_customerinvoicemaster.invoiceDueDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' 
                   AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1 
               GROUP BY `srp_erp_customerinvoicemaster`.`invoiceAutoID` 
               HAVING (" . join(' AND ', $having) . ")
        ")->result_array();
        //        echo $this->db->last_query();
        return $result;
    }

    function fetch_details_invoice_overdue_drilldown_report()
    {
        $companyID = current_companyID();

        $fields4 = "";
        $fields5 = "";
        $fields6 = "";
        $decimalPlc = "";
        $invoiceAutoID = $this->input->post("invoiceAutoID");
        $currency = $this->input->post("currency");
        if (isset($currency)) {
            if ($currency == 'transactionAmount') {
                $fields4 .= 'srp_erp_customerreceiptmaster.transactionCurrency as ' . $currency . 'currency,';
                $fields4 .= 'srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields5 .= 'srp_erp_creditnotemaster.transactionCurrency as ' . $currency . 'currency,';
                $fields5 .= 'srp_erp_creditnotemaster.transactionCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields6 .= 'srp_erp_rvadvancematch.transactionCurrency as ' . $currency . 'currency,';
                $fields6 .= 'srp_erp_rvadvancematch.transactionCurrencyDecimalPlaces as ' . $currency . 'decimal,';

            } else if ($currency == 'companyReportingAmount') {
                $fields4 .= 'srp_erp_customerreceiptmaster.companyReportingCurrency as ' . $currency . 'currency,';
                $fields4 .= 'srp_erp_customerreceiptmaster.companyReportingCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields5 .= 'srp_erp_creditnotemaster.companyReportingCurrency as ' . $currency . 'currency,';
                $fields5 .= 'srp_erp_creditnotemaster.companyReportingCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields6 .= 'srp_erp_rvadvancematch.companyReportingCurrency as ' . $currency . 'currency,';
                $fields6 .= 'srp_erp_rvadvancematch.companyReportingCurrencyDecimalPlaces as ' . $currency . 'decimal,';

            } else if ($currency == 'companyLocalAmount') {
                $fields4 .= 'srp_erp_customerreceiptmaster.companyLocalCurrency as ' . $currency . 'currency,';
                $fields4 .= 'srp_erp_customerreceiptmaster.companyLocalCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields5 .= 'srp_erp_creditnotemaster.companyLocalCurrency as ' . $currency . 'currency,';
                $fields5 .= 'srp_erp_creditnotemaster.companyLocalCurrencyDecimalPlaces as ' . $currency . 'decimal,';
                $fields6 .= 'srp_erp_rvadvancematch.companyLocalCurrency as ' . $currency . 'currency,';
                $fields6 .= 'srp_erp_rvadvancematch.companyLocalCurrencyDecimalPlaces as ' . $currency . 'decimal,';

            }
            $fields4 .= 'IFNULL(SUM(srp_erp_customerreceiptdetail.' . $currency . '),0) as ' . $currency . ',';
            $fields5 .= 'SUM(srp_erp_creditnotedetail.' . $currency . ') as ' . $currency . ',';
            $fields6 .= 'SUM(srp_erp_rvadvancematchdetails.' . $currency . ') as ' . $currency . ',';

            $result = $this->db->query("SELECT
                            $fields4
                            srp_erp_customerreceiptdetail.invoiceAutoID,
                            srp_erp_customerreceiptdetail.receiptVoucherAutoID AS documentAutoID,
                            'RV' AS documentID,
                            RVcode AS documentCode,
                            RVdate AS documentDate 
                        FROM
                            srp_erp_customerreceiptdetail
                            INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` 
                            AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1 
                        WHERE
                            `srp_erp_customerreceiptdetail`.`companyID` = {$companyID}
                            AND srp_erp_customerreceiptmaster.RVDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' 
                            AND invoiceAutoID = {$invoiceAutoID} 
                        GROUP BY
                            srp_erp_customerreceiptdetail.receiptVoucherAutoId UNION ALL
                        SELECT
                            $fields5
                            invoiceAutoID,
                            srp_erp_creditnotedetail.creditNoteMasterAutoID AS documentAutoID,
                            'CN' AS documentID,
                            creditNoteCode AS documentCode,
                            creditNoteDate AS documentDate 
                        FROM
                            srp_erp_creditnotedetail
                            INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` 
                            AND `srp_erp_creditnotemaster`.`approvedYN` = 1 
                        WHERE
                            `srp_erp_creditnotedetail`.`companyID` = {$companyID}
                            AND srp_erp_creditnotemaster.creditNoteDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' 
                            AND invoiceAutoID = {$invoiceAutoID} 
                        GROUP BY
                            srp_erp_creditnotedetail.creditNoteMasterAutoID UNION ALL
                        SELECT
                            $fields6
                            srp_erp_rvadvancematchdetails.InvoiceAutoID,
                            srp_erp_rvadvancematchdetails.matchID AS documentAutoID,
                            'RVM' AS documentID,
                            matchSystemCode AS documentCode,
                            matchDate AS documentDate 
                        FROM
                            srp_erp_rvadvancematchdetails
                            INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` 
                            AND `srp_erp_rvadvancematch`.`confirmedYN` = 1 
                        WHERE
                            `srp_erp_rvadvancematchdetails`.`companyID` = {$companyID} 
                            AND matchDate BETWEEN '" . format_date($this->input->post("datefrom")) . "' AND '" . format_date($this->input->post("dateto")) . "' 
                            AND InvoiceAutoID = {$invoiceAutoID} 
                        GROUP BY
                            srp_erp_rvadvancematchdetails.matchID")->result_array();
                        //        echo $this->db->last_query();
            return $result;
        }

    }

    function load_line_tax_amount()
    {
        $amnt=0;
        $applicableAmnt=$this->input->post('applicableAmnt');
        $taxCalculationformulaID=$this->input->post('taxtype');
        $disount = trim($this->input->post('discount') ?? '');
        $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_creditnotemaster',trim($this->input->post('creditNoteMasterAutoID') ?? ''),'CN', 'creditNoteMasterAutoID');
        if($isGroupByTax == 1){
            $return = fetch_line_wise_itemTaxcalculation($taxCalculationformulaID,$applicableAmnt,$disount, 'CN', trim($this->input->post('creditNoteMasterAutoID') ?? ''));
            if($return['error'] == 1) {
                $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
                $amnt = 0;
            } else {
                $amnt = $return['amount'];
            }
        }
        return $amnt;
    }
}