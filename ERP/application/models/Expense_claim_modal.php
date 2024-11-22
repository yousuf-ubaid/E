<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Expense_claim_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_expense_claim_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expClaimDate = trim($this->input->post('expenseClaimDate') ?? '');
        $expenseClaimDate = input_format_date($expClaimDate, $date_format_policy);
        $segment = explode('|', trim($this->input->post('segmentID') ?? ''));
        $claimedByEmpID = explode('|', trim($this->input->post('claimedByEmpID') ?? ''));
        $data['claimedByEmpID'] = trim($claimedByEmpID[0] ?? '');
        $data['claimedByEmpName'] = trim($claimedByEmpID[1] ?? '');
        $data['documentID'] = 'EC';
        $data['comments'] = trim_desc($this->input->post('comments'));
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['expenseClaimDate'] = $expenseClaimDate;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('expenseClaimMasterAutoID') ?? '')) {
            $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
            $this->db->update('srp_erp_expenseclaimmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Expense Claim Updating  Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName']. ' Update Failed '.$this->db->_error_message(),'Purchase Order');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Expense Claim Updated Successfully.');
                $this->db->trans_commit();
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                return array('status' => true, 'last_id' => $this->input->post('expenseClaimMasterAutoID'),'segmentID' => $segment[0]);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['expenseClaimCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_expenseclaimmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Expense Claim Save Failed ' . $this->db->_error_message());
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Failed '.$this->db->_error_message(),'Purchase Order');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Expense Claim Saved Successfully.');
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id,'segmentID' => $segment[0]);
            }
        }
    }

    function load_expense_claim_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expenseClaimDate,\'' . $convertFormat . '\') AS expenseClaimDate');
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimmaster');
        return $this->db->get()->row_array();
    }

    function save_expense_claim_category()
    {
        $this->db->trans_start();
        $travel_request = $this->input->post('travel_request');
        $claimcategoriesDescription = $this->input->post('claimcategoriesDescription');
        $expenseClaimCategoriesAutoID = $this->input->post('expenseClaimCategoriesAutoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $glcd = explode('|', trim($this->input->post('GLCode') ?? ''));
        $data['claimcategoriesDescription'] = trim_desc($this->input->post('claimcategoriesDescription'));
        $data['glAutoID'] = trim_desc($this->input->post('glAutoID'));
        $data['glCode'] = trim_desc($glcd[0]);
        $data['glCodeDescription'] = trim_desc($glcd[1]);

        if($travel_request)
        {
            $this->db->select('companyID')
            ->where('companyID', $companyID)
            ->where('IsTravelRequestYN', 1)
            
            ->from('srp_erp_expenseclaimcategories');
            $query = $this->db->get();
            $check = $query->row();
            if($check && $check->companyID == $companyID){
                return array('e', 'Travel Request Already Exist');
            }
            else{
                if (trim($this->input->post('expenseClaimCategoriesAutoID') ?? '')) {
                    $descexist = $this->db->query("SELECT expenseClaimCategoriesAutoID FROM srp_erp_expenseclaimcategories WHERE claimcategoriesDescription='$claimcategoriesDescription' AND expenseClaimCategoriesAutoID !=$expenseClaimCategoriesAutoID AND companyID = $companyID; ")->row_array();
                } else {
                    $descexist = $this->db->query("SELECT expenseClaimCategoriesAutoID FROM srp_erp_expenseclaimcategories WHERE claimcategoriesDescription='$claimcategoriesDescription' AND companyID = $companyID; ")->row_array();
                }
                $data['IsTravelRequestYN'] = '1';
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                if (trim($this->input->post('expenseClaimCategoriesAutoID') ?? '')) {
                    if ($descexist) {
                        return array('e', 'Description Already Exist');
                    } else {
                        $this->db->where('expenseClaimCategoriesAutoID', trim($this->input->post('expenseClaimCategoriesAutoID') ?? ''));
                        $this->db->update('srp_erp_expenseclaimcategories', $data);
                    }
        
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        return array('e', 'Expense Claim Updating  Failed');
                    } else {
                        return array('s', 'Expense Claim Updated Successfully');
                    }
                } else {
                    $this->load->library('sequence');
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['IsTravelRequestYN'] = '1';
                    if ($descexist) {
                        return array('e', 'Description Already Exist');
                    } else {
                        $this->db->insert('srp_erp_expenseclaimcategories', $data);
                    }
                    $last_id = $this->db->insert_id();
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        return array('e', 'Expense Claim Category Save Failed');
                    } else {
                        return array('s', 'Expense Claim Category Saved Successfully');
                    }
                }
            }
        }
        else
        {
            if (trim($this->input->post('expenseClaimCategoriesAutoID') ?? '')) {
                $descexist = $this->db->query("SELECT expenseClaimCategoriesAutoID FROM srp_erp_expenseclaimcategories WHERE claimcategoriesDescription='$claimcategoriesDescription' AND expenseClaimCategoriesAutoID !=$expenseClaimCategoriesAutoID AND companyID = $companyID; ")->row_array();
            } else {
                $descexist = $this->db->query("SELECT expenseClaimCategoriesAutoID FROM srp_erp_expenseclaimcategories WHERE claimcategoriesDescription='$claimcategoriesDescription' AND companyID = $companyID; ")->row_array();
            }
            $data['IsTravelRequestYN'] = null;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            if (trim($this->input->post('expenseClaimCategoriesAutoID') ?? '')) {
                if ($descexist) {
                    return array('e', 'Description Already Exist');
                } else {
                    $this->db->where('expenseClaimCategoriesAutoID', trim($this->input->post('expenseClaimCategoriesAutoID') ?? ''));
                    $this->db->update('srp_erp_expenseclaimcategories', $data);
                }
    
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    return array('e', 'Expense Claim Updating  Failed');
                } else {
                    return array('s', 'Expense Claim Updated Successfully');
                }
            } else {
                $this->load->library('sequence');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                if ($descexist) {
                    return array('e', 'Description Already Exist');
                } else {
                    $this->db->insert('srp_erp_expenseclaimcategories', $data);
                }
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    return array('e', 'Expense Claim Category Save Failed');
                } else {
                    return array('s', 'Expense Claim Category Saved Successfully');
                }
            }
        }
    }

    function editClaimCategory()
    {
        $this->db->select('*');
        $this->db->where('expenseClaimCategoriesAutoID', trim($this->input->post('expenseClaimCategoriesAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimcategories');
        return $this->db->get()->row_array();
    }

    function save_expense_claim_detail()
    {
        $expenseClaimCategoriesAutoID = $this->input->post('expenseClaimCategoriesAutoID');
        $description = $this->input->post('description');
        $referenceNo = $this->input->post('referenceNo');
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $transactionAmount = $this->input->post('transactionAmount');
        $tCurrencyID = $this->input->post('tCurrencyID');
        $segmentID = $this->input->post('segmentIDDetail');
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');

        $this->db->select('claimedByEmpID');
        $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
        $this->db->from('srp_erp_expenseclaimmaster');
        $emp= $this->db->get()->row_array();

        $this->db->select('payCurrencyID,payCurrency');
        $this->db->where('EIdNo', $emp['claimedByEmpID']);
        $this->db->from('srp_employeesdetails');
        $empcurr= $this->db->get()->row_array();

        $this->db->trans_start();

        foreach ($expenseClaimCategoriesAutoID as $key => $expenseClaimCatAutoID) {

            $tCurrencyIDEx = explode('|', $tCurrencyID[$key]);

            $data['expenseClaimMasterAutoID'] = $expenseClaimMasterAutoID;
            $data['expenseClaimCategoriesAutoID'] = $expenseClaimCategoriesAutoID[$key];
            $data['description'] = $description[$key];
            $data['referenceNo'] = $referenceNo[$key];
            $data['segmentID'] = $segmentID[$key];
            $data['transactionCurrencyID'] = $transactionCurrencyID[$key];
            $data['transactionCurrency'] = trim($tCurrencyIDEx[0] ?? '');
            $data['transactionExchangeRate'] = 1;
            $data['transactionAmount'] = $transactionAmount[$key];
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($transactionCurrencyID[$key]);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($transactionCurrencyID[$key], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $LocalAmount = $transactionAmount[$key] / $default_currency['conversion'];
            $data['companyLocalAmount'] = $LocalAmount;
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($transactionCurrencyID[$key], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $ReportingAmount = $transactionAmount[$key] / $reporting_currency['conversion'];
            $data['companyReportingAmount'] = $ReportingAmount;
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

            $data['empCurrencyID'] = $empcurr['payCurrencyID'];
            $data['empCurrency'] = $empcurr['payCurrency'];
            $emp_currency = currency_conversionID($transactionCurrencyID[$key], $empcurr['payCurrencyID']);
            $data['empCurrencyExchangeRate'] = $emp_currency['conversion'];
            $empCurrencyAmount = $transactionAmount[$key] / $emp_currency['conversion'];
            $data['empCurrencyAmount'] = round($empCurrencyAmount, $emp_currency['DecimalPlaces']);
            $data['empCurrencyDecimalPlaces'] = $emp_currency['DecimalPlaces'];

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
            $this->db->insert('srp_erp_expenseclaimdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Expense Claim Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Expense Claim Details :  Saved Successfully.');
        }

    }

    function fetch_Ec_detail_table()
    {
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $companyID = $this->common_data['company_data']['company_id'];
        $data['detail'] = $this->db->query('SELECT
	expenseClaimDetailsID,
	expenseClaimMasterAutoID,
	claimcategoriesDescription,
	srp_erp_expenseclaimdetails.description,
	referenceNo,
	transactionCurrency,
	transactionAmount,
	transactionCurrencyDecimalPlaces,
	srp_erp_segment.segmentCode,
	srp_erp_segment.description as segdescription
FROM
	srp_erp_expenseclaimdetails
JOIN srp_erp_expenseclaimcategories ON srp_erp_expenseclaimdetails.expenseClaimCategoriesAutoID = srp_erp_expenseclaimcategories.expenseClaimCategoriesAutoID
JOIN srp_erp_segment ON srp_erp_expenseclaimdetails.segmentID = srp_erp_segment.segmentID
WHERE
	expenseClaimMasterAutoID = ' . $expenseClaimMasterAutoID . ' AND
	srp_erp_expenseclaimdetails.companyID =' . $companyID . ' ')->result_array();

        return $data;
    }

    function fetch_expense_claim_detail()
    {
        $this->db->select('*');
        $this->db->where('expenseClaimDetailsID', trim($this->input->post('expenseClaimDetailsID') ?? ''));
        $this->db->from('srp_erp_expenseclaimdetails');
        return $this->db->get()->row_array();
    }

    function update_expense_claim_detail()
    {

        $this->db->select('payCurrencyID,payCurrency');
        $this->db->where('EIdNo', current_userID());
        $this->db->from('srp_employeesdetails');
        $empcurr= $this->db->get()->row_array();

        $expenseClaimCategoriesAutoID = $this->input->post('expenseClaimCategoriesAutoIDEdit');
        $description = $this->input->post('descriptionEdit');
        $referenceNo = $this->input->post('referenceNoEdit');
        $transactionCurrencyID = $this->input->post('transactionCurrencyIDEdit');
        $transactionAmount = $this->input->post('transactionAmountEdit');
        $tCurrencyID = $this->input->post('tCurrencyID');
        $expenseClaimMasterAutoID = $this->input->post('expenseClaimMasterAutoID');
        $segmentID = $this->input->post('segmentIDDetailEdit');

        $this->db->trans_start();
        $tCurrencyIDEx = explode('|', $tCurrencyID);

        $data['expenseClaimMasterAutoID'] = $expenseClaimMasterAutoID;
        $data['expenseClaimCategoriesAutoID'] = $expenseClaimCategoriesAutoID;
        $data['description'] = $description;
        $data['referenceNo'] = $referenceNo;
        $data['segmentID'] = $segmentID;
        $data['transactionCurrencyID'] = $transactionCurrencyID;
        $data['transactionCurrency'] = trim($tCurrencyIDEx[0] ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionAmount'] = $transactionAmount;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($transactionCurrencyID);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($transactionCurrencyID, $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $LocalAmount = $transactionAmount / $default_currency['conversion'];
        $data['companyLocalAmount'] = $LocalAmount;
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($transactionCurrencyID, $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $ReportingAmount = $transactionAmount / $reporting_currency['conversion'];
        $data['companyReportingAmount'] = $ReportingAmount;
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        $data['empCurrencyID'] = $empcurr['payCurrencyID'];
        $data['empCurrency'] = $empcurr['payCurrency'];
        $emp_currency = currency_conversionID($transactionCurrencyID, $empcurr['payCurrencyID']);
        $data['empCurrencyExchangeRate'] = $emp_currency['conversion'];
        $empCurrencyAmount = $transactionAmount / $emp_currency['conversion'];
        $data['empCurrencyAmount'] = round($empCurrencyAmount, $emp_currency['DecimalPlaces']);
        $data['empCurrencyDecimalPlaces'] = $emp_currency['DecimalPlaces'];

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('expenseClaimDetailsID') ?? '')) {
            $this->db->where('expenseClaimDetailsID', trim($this->input->post('expenseClaimDetailsID') ?? ''));
            $this->db->update('srp_erp_expenseclaimdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Expense Claim Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Expense Claim Detail : Updated Successfully.');

            }
        }
    }

    function delete_expense_claim_detail()
    {
        $this->db->delete('srp_erp_expenseclaimdetails', array('expenseClaimDetailsID' => trim($this->input->post('expenseClaimDetailsID') ?? '')));
        return true;
    }

    function fetch_template_data($expenseClaimMasterAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('expenseClaimMasterAutoID,documentID,expenseClaimCode, DATE_FORMAT(expenseClaimDate,\'' . $convertFormat . '\') AS expenseClaimDate,claimedByEmpID,claimedByEmpName,comments,confirmedYN,confirmedByEmpID,confirmedByName,approvedYN,approvedByEmpName,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_employeesdetails.ECode as ECode,srp_erp_expenseclaimmaster.currentLevelNo ');
        $this->db->where('expenseClaimMasterAutoID', $expenseClaimMasterAutoID);
        $this->db->from('srp_erp_expenseclaimmaster');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_expenseclaimmaster.claimedByEmpID');
        $data['master'] = $this->db->get()->row_array();

        $companyID = $this->common_data['company_data']['company_id'];
        $data['detail'] = $this->db->query('SELECT
	expenseClaimDetailsID,
	expenseClaimMasterAutoID,
	claimcategoriesDescription,
	srp_erp_expenseclaimdetails.description,
	referenceNo,
	transactionCurrency,
	transactionAmount,
	transactionCurrencyDecimalPlaces,
	srp_erp_segment.segmentCode,
	srp_erp_segment.description as segdescription,
    srp_erp_expenseclaimdetails.selectedYN
FROM
	srp_erp_expenseclaimdetails
JOIN srp_erp_expenseclaimcategories ON srp_erp_expenseclaimdetails.expenseClaimCategoriesAutoID = srp_erp_expenseclaimcategories.expenseClaimCategoriesAutoID
JOIN srp_erp_segment ON srp_erp_expenseclaimdetails.segmentID = srp_erp_segment.segmentID
WHERE
	expenseClaimMasterAutoID = ' . $expenseClaimMasterAutoID . ' AND
	srp_erp_expenseclaimdetails.companyID =' . $companyID . '
	ORDER BY transactionCurrency ')->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $expenseClaimMasterAutoID);
        $this->db->where('documentID', 'EC');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function expense_claim_confirmation()
    {

        $this->db->trans_start(); 

        $expenseClaimMasterAutoID = trim($this->input->post('expenseClaimMasterAutoID') ?? '');
        //$this->load->library('approvals');
        $this->db->select("approvalType");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'EC');
        $approvalType = $this->db->get()->row_array();

        if($approvalType['approvalType']==5){
            $this->db->select('expenseClaimCategoriesAutoID');
            $this->db->where('expenseClaimMasterAutoID',$expenseClaimMasterAutoID);
            $this->db->where('companyID',current_companyID());
            $this->db->from('srp_erp_expenseclaimdetails');
            $expenseClaimCategoriesAutoID=$this->db->get()->result_array();

            if ($expenseClaimCategoriesAutoID) {
                $firstID = $expenseClaimCategoriesAutoID[0]['expenseClaimCategoriesAutoID'];
                foreach ($expenseClaimCategoriesAutoID as $detail) {
                    if ($detail['expenseClaimCategoriesAutoID'] != $firstID) {
                        return array('e', 'Can not have different categories');
                    }
                }
            }
        }


        $this->db->select('*');
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimmaster');
        $ec_data = $this->db->get()->row_array();

        $this->db->select('expenseClaimDetailsID');
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimdetails');
        $detail = $this->db->get()->row_array();

        /*if ($approvals_status == 1) {*/
        if($detail){
            $validate_code = validate_code_duplication($ec_data['expenseClaimCode'], 'expenseClaimCode', $expenseClaimMasterAutoID,'expenseClaimMasterAutoID', 'srp_erp_expenseclaimmaster');
            if(!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }
           
            // $data = array(
            //     'confirmedYN' => 1,
            //     'confirmedDate' => $this->common_data['current_date'],
            //     'confirmedByEmpID' => $this->common_data['current_userID'],
            //     'confirmedByName' => $this->common_data['current_user'],
            // );
            // $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
            // $this->db->update('srp_erp_expenseclaimmaster', $data);
            // $this->session->set_flashdata('s', 'Create Approval : ' . $ec_data['expenseClaimCode'] . ' Approvals Created Successfully ');

            $this->load->library('approvals');
            $isAutoApproval = get_document_auto_approval('EC');

            $documentName = 'Expense Claim Approval';
            $tableName = 'srp_erp_expenseclaimmaster';
            $documentCode = $ec_data['expenseClaimCode'];
            $createdDate = $ec_data['createdDateTime'];
            $masterID = $this->input->post('expenseClaimMasterAutoID');

            if ($isAutoApproval == 0) { // If auto approval

                $this->approvals->auto_approve($masterID, $tableName, 'expenseClaimMasterAutoID', 'EC', $documentCode, $createdDate);
    
                $this->db->trans_complete();
                if ($this->db->trans_status() === true) {
                    $this->db->trans_commit();
                    return ['s', 'Approved successfully'];
                } else {
                    $this->db->trans_rollback();
                    return ['e', 'Error in approval process'];
                }
            }

            // $this->db->select('id')
            //          ->from('srp_erp_documentcodes')
            //          ->where('specificUserYN', 1);
            // $query = $this->db->get();
            // $checkIsSpecial = $query->result_array();
            
            // if (!empty($checkIsSpecial)) {
                
            //     $this->db->select('approvalUserID')
            //             ->from('srp_erp_appoval_specific_users')
            //             ->where('empID', current_userID());

            // } else {
            //     $approvals_status = $this->approvals->CreateApproval('EC', $masterID, $documentCode, $documentName, $tableName, 'expenseClaimMasterAutoID', 0, $createdDate);
            // }

           
            $this->db->select('expenseClaimCategoriesAutoID')
            ->from('srp_erp_expenseclaimdetails')
            ->where('expenseClaimMasterAutoID', $masterID);
        
            $result = $this->db->get()->row_array();
            $categoryID = isset($result['expenseClaimCategoriesAutoID']) ? $result['expenseClaimCategoriesAutoID'] : null;

            $isSpecialUser=$this->db->select('id')
                                    ->from('srp_erp_appoval_specific_users')
                                    ->where('empID',$ec_data['claimedByEmpID'])
                                    ->get()
                                    ->row_array();
            
            if(!empty($isSpecialUser)){
                $expenseUpdate = ['specificUserYN' => 1]; 
                $this->db->where('expenseClaimMasterAutoID',$ec_data['expenseClaimMasterAutoID']);
                $this->db->update('srp_erp_expenseclaimmaster', $expenseUpdate);
            }

        
            $approvals_status = $this->approvals->CreateApproval('EC', $masterID, $documentCode, $documentName, $tableName, 'expenseClaimMasterAutoID', 0, $createdDate,null,null,0,null, $categoryID);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return ['e', 'Something went wrong!, In approval create process'];
            }

            if ($approvals_status == 3) {
                $this->db->trans_rollback();
                return ['w', 'There is no user exists to perform <b>Expese Claim approval</b> for this company.'];
            } elseif ($approvals_status == 1) {

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    return ['e', 'Something went wrong!, In approval create process'];
                }
                $this->db->trans_commit();
                return ['s', 'Approval created : ' . $documentCode];
            } else {
                $this->db->trans_rollback();
                return ['w', 'some thing went wrong', $approvals_status];
            }

            
            $firbase_status = null;

            if($firbase_status){
                 /*** Firebase Mobile Notification*/
                $this->db->select('managerID');
                $this->db->where('empID', trim($ec_data['claimedByEmpID'] ?? ''));
                $this->db->where('active', 1);
                $this->db->from('srp_erp_employeemanagers');
                $managerid = $this->db->get()->row_array();

                $token_android = firebaseToken($managerid["managerID"], 'android');
                $token_ios = firebaseToken($managerid["managerID"], 'apple');

                $firebaseBody = $ec_data['claimedByEmpName'] . " has applied for an expense claim.";

                $this->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->firebase_notification->sendFirebasePushNotification("New Expense Claim", $firebaseBody, $token_android, 2, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "android");
                }
                if(!empty($token_ios)) {
                    $this->firebase_notification->sendFirebasePushNotification("New Expense Claim", $firebaseBody, $token_ios, 2, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "apple");
                }
            }

            return array('s','Approvals Created Successfully');

        }else{
            //$this->session->set_flashdata('e', 'No records found to confirm this document');
            return array('e','No records found to confirm this document');
        }

        /* } else {
             return false;
         }*/
    }

    function save_expense_Claim_approval($autoappLevel = 1)
    {

        $this->load->library('Approvals');

        $expenseClaimMasterAutoID = trim($this->input->post('expenseClaimMasterAutoID') ?? '');
        $this->db->select('*');
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimmaster');
        $ec_data = $this->db->get()->row_array();

        $system_id = trim($this->input->post('expenseClaimMasterAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('ec_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        $companyID = current_companyID();

          // Fetch the selectedYN values from the form
          $expenseclaimListSync = $this->input->post('expenseclaimListSync');

    // Debugging: Print the submitted data
// error_log('Submitted selectedYN data: ' . print_r($selectedYN, true));

        if($autoappLevel == 0){
            $approvals_status = 1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'EC');
        }

        if ($approvals_status == 1) {
            $data = array(
                'approvedYN' => 1,
                'approvedDate' => $this->common_data['current_date'],
                'approvedByEmpID' => $this->common_data['current_userID'],
                'approvedByEmpName' => $this->common_data['current_user'],
                'approvalComments' => $this->input->post('comments'),
            );
            $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
            $this->db->update('srp_erp_expenseclaimmaster', $data);
        

// Debugging: Check if $selectedYN is set and is an array
if (!empty($expenseclaimListSync)) {
   

    foreach($expenseclaimListSync as $detailID => $value) {
        $selected = $value ? 1 : 0; // Checkbox checked = 1, unchecked = 0
        $this->db->where('expenseClaimDetailsID', $value);
        $this->db->update('srp_erp_expenseclaimdetails', array('selectedYN' => 0));

        // Debugging: Log the update process
        error_log('Updating expenseClaimDetailsID: ' . $detailID . ' with selectedYN: ' . $selected);
    }
} else {
    // Debugging: Log if $selectedYN is not set or not an array
    error_log('SelectedYN is not set or is not an array');
}

            // if (isset($selectedYN) && is_array($selectedYN)) {
            //     foreach ($selectedYN as $detailID => $value) {
            //         $selected = $value ? 1 : 0; // Checkbox checked = 1, unchecked = 0
            //         $this->db->where('expenseClaimDetailsID', $detailID);
            //         $this->db->update('srp_erp_expenseclaimdetails', array('selectedYN' => $selected));
            //     }
            // }
                    /*** Firebase Mobile Notification*/
            $token_android = firebaseToken($ec_data["claimedByEmpID"], 'android');
            $token_ios = firebaseToken($ec_data["claimedByEmpID"], 'apple');

            $this->load->library('firebase_notification');
            if(!empty($token_android)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approved", "Your expense claim has been approved", $token_android, 4, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "android");
            }
            if(!empty($token_ios)) {
                $this->firebase_notification->sendFirebasePushNotification("Expense Claim Approved", "Your expense claim has been approved", $token_ios, 4, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "apple");
            }

            $this->session->set_flashdata('s', ' Approved Successfully ');
            return true;
        } 
        
        if($status != 1) {
            $this->db->select('expenseClaimCode');
            $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
            $this->db->from('srp_erp_expenseclaimmaster');
            $documentCode = $this->db->get()->row_array();


            $datas = array(
                'confirmedYN' => 3,
                /*'confirmedDate' => null,
                'confirmedByEmpID' => null,
                'confirmedByName' => null,*/
            );
            $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
            $update = $this->db->update('srp_erp_expenseclaimmaster', $datas);
            if ($update) {
                $data = array(
                    'documentID' => "EC",
                    'systemID' => $this->input->post('expenseClaimMasterAutoID'),
                    'documentCode' => $documentCode['expenseClaimCode'],
                    'comment' => $this->input->post('comments'),
                    'rejectedLevel' => 1,
                    'rejectByEmpID' => $this->common_data['current_userID'],
                    'rejectByEmpName' => $this->common_data['current_user'],
                    'table_name' => "srp_erp_expenseclaimmaster",
                    'table_unique_field' => "expenseClaimMasterAutoID",
                    'companyID' => current_companyID(),
                    'companyCode' => current_companyCode(),
                    'createdUserGroup' => $this->common_data['user_group'],
                    'createdPCID' => $this->common_data['current_pc'],
                    'createdUserID' => $this->common_data['current_userID'],
                    'createdUserName' => $this->common_data['current_user'],
                    'createdDateTime' => $this->common_data['current_date'],
                );
                $this->db->insert('srp_erp_approvalreject', $data);

                /*** Firebase Mobile Notification*/
                $token_android = firebaseToken($ec_data["claimedByEmpID"], 'android');
                $token_ios = firebaseToken($ec_data["claimedByEmpID"], 'apple');

                $firebaseBody = "Your expense claim has referred back";

                $this->load->library('firebase_notification');
                if(!empty($token_android)) {
                    $this->firebase_notification->sendFirebasePushNotification("Expense Claim Referred Back", $firebaseBody, $token_android, 4, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "android");
                }
                if(!empty($token_ios)) {
                    $this->firebase_notification->sendFirebasePushNotification("Expense Claim Referred Back", $firebaseBody, $token_ios, 4, $ec_data['expenseClaimCode'], "EC", $expenseClaimMasterAutoID, "apple");
                }

                $this->session->set_flashdata('s', ' Rejected Successfully ');
                return true;
            }

        }
    }

    function fetch_approval_user_modal_ec(){
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $documentSystemCode = $this->input->post('documentSystemCode');

        $this->db->select('*');
        $this->db->from('srp_erp_expenseclaimmaster');
        $this->db->join('srp_employeesdetails as empTB','srp_erp_expenseclaimmaster.claimedByEmpID = empTB.EIdNo');
        $this->db->where('expenseClaimMasterAutoID', $documentSystemCode);
        $this->db->where_in('companyID', $companyID);
        $masterData = $this->db->get()->row_array();

        $specialUser=$this->db->select('approvalUserID')
                              ->from('srp_erp_appoval_specific_users')
                              ->where('empID',$masterData['claimedByEmpID'])
                              ->get()
                              ->result_array();

        $expenseClaimCategory=$this->db->select('expenseClaimCategoriesAutoID')
                              ->from('srp_erp_expenseclaimdetails')
                              ->where('expenseClaimMasterAutoID',$documentSystemCode)
                              ->group_by('expenseClaimMasterAutoID')
                              ->get()
                              ->result_array();

        if($masterData){
            $claimEmpID = $masterData['claimedByEmpID'];

            //fetch levels and users

            $this->db->select("approvalLevelID,approvedYN,approvedDate,approvedComments,documentCode,docConfirmedByEmpID,
                DATE_FORMAT(documentDate,\"" . $convertFormat . "\") AS documentDate, '' AS Ename2,
                DATE_FORMAT(docConfirmedDate,\"" . $convertFormat . "\") AS docConfirmedDate,
                DATE_FORMAT(approvedDate,\"" . $convertFormat . "\") AS approveDate,
                ap.employeeID");
            $this->db->from('srp_erp_documentapproved');
            $this->db->join("srp_erp_approvalusers AS ap", "ap.levelNo = srp_erp_documentapproved.approvalLevelID AND ap.documentID = 'EC' AND ap.companyID = '{$companyID}'");
            $this->db->where('srp_erp_documentapproved.documentID', 'EC');
            $this->db->where('documentSystemCode', $documentSystemCode);
            $this->db->where('srp_erp_documentapproved.companyID', $companyID);
            if (!empty($specialUser)) {
                $this->db->where_in('ap.approvalUserID', array_column($specialUser, 'approvalUserID'));
            }
            else{
                $this->db->where('ap.specificUser',0);  
            }
            
            if (!empty($expenseClaimCategory)) {
                $this->db->where_in('ap.typeID', array_column($expenseClaimCategory, 'expenseClaimCategoriesAutoID'));
            }
           
            $this->db->order_by('srp_erp_documentapproved.approvalLevelID');
            $approved = $this->db->get()->result_array();
            // echo $this->db->last_query();

            $managers = $this->db->query("SELECT * FROM (
                        SELECT repManager, repManagerName, currentLevelNo,HOD,HODName
                        FROM srp_erp_expenseclaimmaster AS empTB
                        JOIN srp_erp_documentapproved ON empTB.documentID = 'EC'
                        LEFT JOIN (
                            SELECT hod_id AS HOD,EmpID AS EmpNew,t3.Ename2 AS HODName

                            FROM srp_empdepartments  AS dpt
                            JOIN srp_departmentmaster AS departmentmaster  ON departmentmaster.DepartmentMasterID = dpt.DepartmentMasterID
                            JOIN srp_employeesdetails AS t3 ON departmentmaster.hod_id=t3.EIdNo AND t3.Erp_companyID ='$companyID'
                            WHERE dpt.isPrimary = 1
                            ) AS HodData ON empTB.claimedByEmpID = HodData.EmpNew

                        LEFT JOIN (
                            SELECT empID, managerID AS repManager, Ename2 AS repManagerName  FROM srp_erp_employeemanagers AS t1
                            JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo 
                            WHERE active = 1 
                            AND t1.companyID ='$companyID'
                        ) AS repoManagerTB ON empTB.claimedByEmpID = repoManagerTB.empID
                        WHERE empTB.companyID ='$companyID' AND expenseClaimMasterAutoID={$documentSystemCode}
                        ) AS empData

                        LEFT JOIN (
                            SELECT managerID AS topManager, Ename2 AS topManagerName, empID AS topEmpID
                            FROM srp_erp_employeemanagers AS t1
                            JOIN srp_employeesdetails AS t2 ON t1.managerID=t2.EIdNo AND t2.Erp_companyID = '$companyID'
                            WHERE t1.companyID ='$companyID' AND active = 1
                        ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID 
            
            ")->row_array();

            $data_arr = array();

            foreach($approved as $key => $approve_details){

                $employeeID = $approve_details['employeeID'];

                if($employeeID == -1){
                    $approved[$key]['Ename2'] =  $managers['repManagerName'];
                    $approved[$key]['levelUserID'] =  $managers['repManager'];
                }elseif($employeeID == -2){
                    $approved[$key]['Ename2'] =  $managers['HODName'];
                    $approved[$key]['levelUserID'] =  $managers['HOD'];
                }elseif($employeeID == -3){
                    $approved[$key]['Ename2'] =  $managers['topManagerName'];
                    $approved[$key]['levelUserID'] =  $managers['topManager'];
                }else{
                    $employee_details = fetch_employeeNo($employeeID);
                    $approved[$key]['Ename2'] =  $employee_details['Ename2'];
                    $approved[$key]['levelUserID'] =  $employee_details['EIdNo'];
                }


            }
            $data_arr['approved'] = $approved;
            $data_arr['expenseClaimCode'] = isset($data_arr['approved'][0]['documentCode']) ? $data_arr['approved'][0]['documentCode'] : '';
            $data_arr['expenseClaimDate'] = isset($data_arr['approved'][0]['documentDate']) ? $data_arr['approved'][0]['documentDate'] : '' ;
            $data_arr['confirmedDate'] = isset($data_arr['approved'][0]['docConfirmedDate']) ? $data_arr['approved'][0]['docConfirmedDate'] : '';

            $confirmedEmpID = isset($data_arr['approved'][0]['docConfirmedByEmpID']) ? $data_arr['approved'][0]['docConfirmedByEmpID'] : '';
            $emp = fetch_employeeNo($confirmedEmpID);
            //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
            $data_arr['confirmedByName'] = $emp['Ename2'];

            // if(!empty($data_arr['approved'])){
            //     $data_arr['document_code'] = $data_arr['approved'][0]['documentCode'];
            //     $data_arr['document_date'] = $data_arr['approved'][0]['documentDate'];
            //     $data_arr['confirmed_date'] = $data_arr['approved'][0]['docConfirmedDate'];
            //     $emp = fetch_employeeNo($data_arr['approved'][0]['docConfirmedByEmpID']);
            //     //$data_arr['conformed_by']   = $emp['ECode'].' - '.$emp['Ename2'];
            //     $data_arr['conformed_by'] = $emp['Ename2'];
            // }
          
        }

        return $data_arr;


    }

    function deleteClaimCategory(){
        $this->db->select('expenseClaimMasterAutoID');
        $this->db->where('expenseClaimCategoriesAutoID', trim($this->input->post('expenseClaimCategoriesAutoID') ?? ''));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_expenseclaimdetails');
        $categoryExsist = $this->db->get()->row_array();
        if($categoryExsist){
            return array('e','Category has been used');
        }else{
            $this->db->delete('srp_erp_expenseclaimcategories', array('expenseClaimCategoriesAutoID' => trim($this->input->post('expenseClaimCategoriesAutoID') ?? '')));
            return array('s','Deleted Successfully');
        }
    }

    function checkDetailexsist()
    {
        $this->db->select('expenseClaimDetailsID');
        $this->db->where('expenseClaimMasterAutoID', trim($this->input->post('expenseClaimMasterAutoID') ?? ''));
        $this->db->from('srp_erp_expenseclaimdetails');
        $result= $this->db->get()->result_array();
        if($result){
            return array('w','Delete detail to change employee');
        }else{
            return array('s','Employee changed successfully');
        }
    }

    function get_user_segemnt(){
        $this->db->select("segmentID");
        $this->db->from('srp_employeesdetails');
        $this->db->where('EIdNo', $this->input->post('empid'));
        $data = $this->db->get()->row_array();

        $this->db->select("segmentCode");
        $this->db->from('srp_erp_segment');
        $this->db->where('segmentID', $data['segmentID'] ?? '');
        $datas = $this->db->get()->row_array();
        $result=($data['segmentID'] ?? '').'|'.($datas['segmentCode'] ?? '');
        return $result;
    }

}
