<?php

class Iou_model extends ERP_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function save_iou_voucher_header()
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $employee = explode('|', trim($this->input->post('employeeid') ?? ''));
        $ioumasterautoid = trim($this->input->post('voucherautoid') ?? '');
        $voucherCat_id = $this->input->post('voucherCategory');
        $narration = $this->input->post('narration');
        $documentDate = $this->input->post('voucherdate');
        $chequeDate = $this->input->post('PVchequeDate');
        $bankcode = trim($this->input->post('PVbankCode') ?? '');
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
//        $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
//        $FYBegin = input_format_date($year[0], $date_format_policy);
//        $FYEnd = input_format_date($year[1], $date_format_policy);
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $employeedet = explode('|', trim($this->input->post('empname') ?? ''));

        $accountPayeeOnly = 0;
        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }

        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);

            if ($financeyearperiodYN == 1) {
                $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                $FYBegin = input_format_date($financeyr[0], $date_format_policy);
                $FYEnd = input_format_date($financeyr[1], $date_format_policy);
            } else {
                $financeYearDetails = get_financial_year($format_documentDate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($format_documentDate);
                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                }
            }

        }
        $format_chequeDate = null;
        if (isset($chequeDate) && !empty($chequeDate)) {
            $format_chequeDate = input_format_date($chequeDate, $date_format_policy);
        }

        if ($employee[1] == 1) {
            $empdet = $this->db->query("Select * from srp_employeesdetails where EIdNo = $employee[0]")->row_array();
        } else if ($employee[1] == 2) {
            $empdet = $this->db->query("select users.currencyID as payCurrencyID,currency.CurrencyCode as payCurrency from srp_erp_iouusers users LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = users.currencyID where companyID = $companyID and userID = $employee[0]")->row_array();
        }

        $data['empID'] = $employee[0];
        $data['userType'] = $employee[1];
        $data['empName'] = $employeedet[1];
        $data['companyID'] = $companyID;
        $data['voucherDate'] = $format_documentDate;
        $data['voucherCategoryID'] = $voucherCat_id;
        $data['documentID'] = 'IOU';
        //$data['narration'] = $narration;
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = $FYBegin;
        $data['FYEnd'] = $FYEnd;
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $bank_detail = fetch_gl_account_desc($bankcode);
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['bankGLAutoID'] = $bank_detail['GLAutoID'];
        if ($bank_detail['isCash'] == 1) {
            $data['chequeNo'] = null;
            $data['chequeDate'] = null;
        } else {
            if ($this->input->post('paymentType') == 2) {
                $data['chequeNo'] = null;
                $data['chequeDate'] = null;
            } else {
                $data['chequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                $data['chequeDate'] = trim($format_chequeDate);
            }

        }
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
        $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
        $data['referenceNumber'] = trim($this->input->post('referenceno') ?? '');
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['paymentType'] = $this->input->post('paymentType');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = $currency_code[0];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['bankCurrencyID'] = $bank_detail['bankCurrencyID'];
        $data['bankCurrency'] = $bank_detail['bankCurrencyCode'];
        $bankCurrency = currency_conversionID($data['transactionCurrencyID'], $data['bankCurrencyID']);
        $data['bankCurrencyExchangeRate'] = $bankCurrency['conversion'];
        $data['bankCurrencyDecimalPlaces'] = $bankCurrency['DecimalPlaces'];
        //$data['bankCurrencyAmount'] = round($data['transactionAmount'] / $bankCurrency['conversion'], $bankCurrency['DecimalPlaces']);
        $data['partyCurrencyID'] = $empdet['payCurrencyID'];
        $data['partyCurrency'] = $empdet['payCurrency'];
        $partyexchange = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        $data['partyExchangeRate'] = $partyexchange['conversion'];
        $data['partyCurrencyDecimalPlaces'] = $partyexchange['DecimalPlaces'];
        //$data['partyCurrencyAmount'] = round($data['transactionAmount'] / $partyexchange['conversion'], $partyexchange['DecimalPlaces']);
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];


        if ($ioumasterautoid) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('voucherAutoID', $ioumasterautoid);
            $this->db->update('srp_erp_iouvouchers', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in IOU Voucher Update ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'IOU Voucher Updated successfully.', $ioumasterautoid);
            }
        } else {
            $this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['chequeNo']));

            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM srp_erp_iouvouchers WHERE companyID={$companyID}")->row_array();
            $data['serialNo'] = $serial['serialNo'];
            $data['iouCode'] = ($company_code . '/' . 'IOU' . str_pad($data['serialNo'], 6,
                    '0', STR_PAD_LEFT));

            $this->db->insert('srp_erp_iouvouchers', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error Occured' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', '' . $data['iouCode'] . ' IOU Voucher created successfully.', $last_id);

            }
        }

        
    }

    function save_iou_voucher_details()
    {
        $this->db->trans_start();

        $vouchermasterid = trim($this->input->post('IOUmasterid') ?? '');
        $description = $this->input->post('description');
        $amount = $this->input->post('amount');
        $companyid = current_companyID();

        $this->db->select('*');
        $this->db->where('voucherAutoID', $vouchermasterid);
        $master_record = $this->db->get('srp_erp_iouvouchers')->row_array();

        $glautoid = $this->db->query("select controlac.GLAutoID as GLAutoID from srp_erp_chartofaccounts charts INNER JOIN srp_erp_companycontrolaccounts controlac on charts.GLAutoID = controlac.GLAutoID where controlac.companyID = $companyid AND controlac.controlAccountType = 'IOU'")->row_array();

        if(!empty($glautoid['GLAutoID'])) {
            foreach ($description as $key => $val) {
                $data[$key]['voucherAutoID'] = $vouchermasterid;
                $data[$key]['description'] = $val;
                $data[$key]['transactionAmount'] = $amount[$key];
                $data[$key]['companyLocalAmount'] = ($data[$key]['transactionAmount'] / $master_record['companyLocalExchangeRate']);
                $data[$key]['companyReportingAmount'] = ($data[$key]['transactionAmount'] / $master_record['companyReportingExchangeRate']);
                $data[$key]['partyCurrencyAmount'] = ($data[$key]['transactionAmount'] / $master_record['partyExchangeRate']);
                $data[$key]['glAutoID'] = $glautoid['GLAutoID'];
                $data[$key]['companyID'] = $companyid;
                $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
                $data[$key]['createdPCID'] = $this->common_data['current_pc'];
                $data[$key]['createdUserID'] = $this->common_data['current_userID'];
                $data[$key]['createdDateTime'] = $this->common_data['current_date'];
                $data[$key]['createdUserName'] = $this->common_data['current_user'];
            }
            $this->db->insert_batch('srp_erp_iouvoucherdetails', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'IOU Voucher Detail :  Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'IOU Voucher Detail :  Saved Successfully.');
            }
        } else {
            return array('e', 'Please Assign IOU Control Account and Try Again!');
        }
    }

    function delete_iouVoucher_detail()
    {
        $voucherdetailid = trim($this->input->post('voucherDetailID') ?? '');
        $this->db->delete('srp_erp_iouvoucherdetails', array('voucherDetailID' => $voucherdetailid));
        return true;
    }

    function load_voucherHeader()
    {
        $convertFormat = convert_date_format_sql();
        $ioumasterid = $this->input->post('IOUmasterid');
        $companyid = current_companyID();

        $data = $this->db->query("SELECT *,DATE_FORMAT(voucherDate,'{$convertFormat}') AS voucherDate,segment.segmentCode,DATE_FORMAT(chequeDate,'{$convertFormat}') AS chequeDate from srp_erp_iouvouchers Vouchers LEFT JOIN srp_erp_segment segment on Vouchers.segmentID = segment.segmentID WHERE voucherAutoID  = $ioumasterid AND Vouchers.companyID = $companyid")->row_array();

        return $data;

    }

    function fetch_iou_voucher($ioumasterid)
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("select voucherAutoID,vouchers.empName as empnameiou,iouCode,vouchers.approvedYN,vouchers.approvedbyEmpName,segment.segmentCode,transactionCurrencyDecimalPlaces,DATE_FORMAT(voucherDate,'{$convertFormat}') AS voucherDate,IFNULL(DATE_FORMAT(chequeDate,'{$convertFormat}'),'-')  AS chequeDate,DATE_FORMAT(vouchers.approvedDate,'{$convertFormat}') AS approvedDate,employee.Ename2 as employeename,currency.CurrencyName as transactioncurrency,currency.CurrencyCode as CurrencyCode,IFNULL(vouchers.narration,'-')  as narration,chart.bankName as bankname,IFNULL(chart.bankAccountNumber,'-') as bankacount,IFNULL(chart.bankSwiftCode,'-') as bankSwiftCode,IFNULL(chequeNo,'-') as ChequeNo,CASE WHEN vouchers.paymentType = 0 THEN \"Cash\" WHEN vouchers.paymentType = 1 THEN \"Cheque\" WHEN vouchers.paymentType = 2 THEN \"Bank Transfer\" END as paymentmode from srp_erp_iouvouchers vouchers LEFT JOIN srp_employeesdetails employee on employee.EIdNo = vouchers.empID LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = vouchers.transactionCurrencyID LEFT JOIN srp_erp_chartofaccounts chart on chart.GLAutoID = vouchers.bankGLAutoID LEFT JOIN srp_erp_segment segment ON segment.segmentID = vouchers.segmentID  where  vouchers.companyID = $companyid AND vouchers.voucherAutoID = $ioumasterid")->row_array();

        $data['detail'] = $this->db->query("select * from srp_erp_iouvoucherdetails WHERE companyID = $companyid AND voucherAutoID = $ioumasterid")->result_array();

        return $data;
    }

    function fetch_signaturelevel_iou_voucher()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'IOU');
        $this->db->from('srp_erp_documentcodemaster');
        return $this->db->get()->row_array();

    }

    function iou_Voucher_confirmation()
    {
        $iouvoucherid = trim($this->input->post('IOUmasterid') ?? '');

        $this->db->select('voucherDetailID');
        $this->db->where('voucherAutoID', $iouvoucherid);
        $this->db->from('srp_erp_iouvoucherdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('voucherAutoID');
            $this->db->where('voucherAutoID', $iouvoucherid);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_iouvouchers');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('approvals');
                $this->db->select('voucherAutoID, documentID,iouCode,voucherDate');
                $this->db->where('voucherAutoID', $iouvoucherid);
                $this->db->from('srp_erp_iouvouchers');
                $app_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($app_data['iouCode'], 'iouCode', $iouvoucherid,'voucherAutoID', 'srp_erp_iouvouchers');
                if(!empty($validate_code)) {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return array(false, 'error');
                }
                $auto_approval = get_document_auto_approval('IOU');
             
                if($auto_approval == 0){
                    $approvals_status = $this->approvals->auto_approve($app_data['voucherAutoID'], 'srp_erp_iouvouchers', 'voucherAutoID', 'IOU', $app_data['iouCode'], $app_data['voucherDate']);
                }else{
                    $approvals_status = $this->approvals->CreateApproval('IOU', $app_data['voucherAutoID'], $app_data['iouCode'], 'IOU Voucher', 'srp_erp_iouvouchers', 'voucherAutoID');
                }

                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user']
                    );
                    $this->db->where('voucherAutoID', trim($this->input->post('IOUmasterid') ?? ''));
                    $this->db->update('srp_erp_iouvouchers', $data);

                    return array('error' => 0, 'message' => 'document successfully confirmed');
                } else {
                    return array('error' => 1, 'message' => 'Approval setting are not configured!, please contact your system team.');
                }
            }
        }
    }

    function fetch_iou_voucher_details()
    {
        $iouvoucherdetail = trim($this->input->post('voucherDetailID') ?? '');
        $companyid = $this->common_data['company_data']['company_id'];

        $this->db->select('*');
        $this->db->from('srp_erp_iouvoucherdetails');
        $this->db->where('voucherDetailID', $iouvoucherdetail);
        $this->db->where('companyID', $companyid);
        return $this->db->get()->row_array();
    }

    function update_iou_voucher_details()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $data['description'] = $this->input->post('description_edit');
        $data['transactionAmount'] = $this->input->post('amount_edit');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $this->db->set('timestamp', current_date(true));
        $this->db->where('voucherDetailID', $this->input->post('iouvoucherdetails_edit'));
        $this->db->where('companyID', $companyid);
        $this->db->update('srp_erp_iouvoucherdetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'IOU Voucher Detail Updated Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'IOU Voucher Detail Updated successfully');
        }

    }

    function save_iou_approval()
    {

        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('iouvoucherid') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        if($type == 1){
            $approvals_status = 1;
            $status = 1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'IOU');
        }
        
        $comapnyid = current_companyID();

        if ($approvals_status == 1) {
            $datas['approvedYN'] = $status;
            $datas['approvedbyEmpID'] = $this->common_data['current_userID'];
            $datas['approvedbyEmpName'] = $this->common_data['current_user'];
            $datas['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('voucherAutoID', $system_id);
            $this->db->update('srp_erp_iouvouchers', $datas);

            $date_format_policy = date_format_policy();

            $mastertblvoucher = $this->db->query("Select vouchers.*,currency.CurrencyCode,segment.segmentCode from srp_erp_iouvouchers vouchers LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = vouchers.transactionCurrencyID LEFT JOIN srp_erp_segment segment on segment.segmentID = vouchers.segmentID where voucherAutoID = $system_id AND vouchers.companyID = $comapnyid")->row_array();

            $detailtblvoucher = $this->db->query("select sum(transactionAmount) as transactionamount from srp_erp_iouvoucherdetails where companyID = $comapnyid AND voucherAutoID = $system_id")->row_array();

            $detailtbl = $this->db->query("select * from srp_erp_iouvoucherdetails where companyID = $comapnyid AND voucherAutoID = $system_id")->result_array();

            $accountPayeeOnly = 0;
            if (!empty($mastertblvoucher['accountPayeeOnly']) == 1) {
                $accountPayeeOnly = 1;
            }
            $PaymentVoucherdate = $mastertblvoucher['voucherDate'];
            $PVcheqDate = $mastertblvoucher['chequeDate'];
            $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
            $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
            $segmentID = $mastertblvoucher['segmentID'];
            $segmentcode = $mastertblvoucher['segmentCode'];
            $currency_code = $mastertblvoucher['CurrencyCode'];
            $FYBegin = $mastertblvoucher['FYBegin'];
            $FYEnd = $mastertblvoucher['FYEnd'];
            $data['PVbankCode'] = $mastertblvoucher['bankGLAutoID'];
            $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
            $data['documentID'] = 'PV';
            $data['companyFinanceYearID'] = $mastertblvoucher['companyFinanceYearID'];
            $data['iouVoucherID'] = trim($this->input->post('iouvoucherid') ?? '');
            $data['isSytemGenerated'] = 1;
            $data['companyFinanceYear'] = $mastertblvoucher['companyFinanceYear'];
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = $mastertblvoucher['companyFinancePeriodID'];
            $data['PVdate'] = trim($PVdate);
            $data['PVNarration'] = $mastertblvoucher['narration'] . '(' . $mastertblvoucher['iouCode'] . ')';
            $data['accountPayeeOnly'] = $accountPayeeOnly;
            $data['segmentID'] = $segmentID;
            $data['segmentCode'] = $segmentcode;
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
            $data['paymentType'] = $mastertblvoucher['paymentType'];
            if ($bank_detail['isCash'] == 1) {
                $data['PVchequeNo'] = null;
                $data['PVchequeDate'] = null;
            } else {
                if ($mastertblvoucher['paymentType'] == 2) {
                    $data['PVchequeNo'] = null;
                    $data['PVchequeDate'] = null;
                } else {
                    $data['PVchequeNo'] = $mastertblvoucher['chequeNo'];
                    $data['PVchequeDate'] = $mastertblvoucher['chequeDate'];
                }

            }
            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);

            if ($mastertblvoucher['userType'] == 2) {
                $data['pvType'] = 'Direct';
            } else {
                $data['pvType'] = 'Employee';
            }
            $data['bankTransferDetails'] = $mastertblvoucher['bankTransferDetails'];
            $data['referenceNo'] = $mastertblvoucher['iouCode'];
            $data['transactionCurrencyID'] = $mastertblvoucher['transactionCurrencyID'];
            $data['transactionCurrency'] = $currency_code;
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
            $emp_arr = $this->fetch_empyoyee($mastertblvoucher['empID']);
           /* $data['partyType'] = 'EMP';
            $data['partyID'] = $mastertblvoucher['empID'];
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename2'];
            $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
            $data['partyTelephone'] = $emp_arr['EpTelephone'];
            $data['partyFax'] = $emp_arr['EpFax'];
            $data['partyEmail'] = $emp_arr['EEmail'];
            $data['partyGLAutoID'] = '';
            $data['partyGLCode'] = '';
            $data['partyCurrencyID'] = $mastertblvoucher['partyCurrencyID'];
            $data['partyCurrency'] = $mastertblvoucher['partyCurrency'];
            $data['partyExchangeRate'] = $mastertblvoucher['partyExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $mastertblvoucher['partyCurrencyDecimalPlaces'];
            $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
            $data['partyExchangeRate'] = $partyCurrency['conversion'];
            $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];*/

            if ($data['pvType'] == 'Direct') {
                $data['partyType'] = 'DIR';
                $data['partyName'] = $mastertblvoucher['empName'];
                $data['partyCurrencyID'] = $mastertblvoucher['partyCurrencyID'];
                $data['partyCurrency'] = $mastertblvoucher['partyCurrency'];
                $data['partyExchangeRate'] = $mastertblvoucher['partyExchangeRate'];
                $data['partyCurrencyDecimalPlaces'] = $mastertblvoucher['partyCurrencyDecimalPlaces'];
            } elseif ($data['pvType'] == 'Employee') {
                $emp_arr = $this->fetch_empyoyee($mastertblvoucher['empID']);
                $data['partyType'] = 'EMP';
                $data['partyID'] = $mastertblvoucher['empID'];
                $data['partyCode'] = $emp_arr['ECode'];
                $data['partyName'] = $emp_arr['Ename2'];
                $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
                $data['partyTelephone'] = $emp_arr['EpTelephone'];
                $data['partyFax'] = $emp_arr['EpFax'];
                $data['partyEmail'] = $emp_arr['EEmail'];
                $data['partyGLAutoID'] = '';
                $data['partyGLCode'] = '';
                $data['partyCurrencyID'] = $mastertblvoucher['partyCurrencyID'];
                $data['partyCurrency'] = $mastertblvoucher['partyCurrency'];
                $data['partyExchangeRate'] = $mastertblvoucher['partyExchangeRate'];
                $data['partyCurrencyDecimalPlaces'] = $mastertblvoucher['partyCurrencyDecimalPlaces'];
            }
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

            $dataiomaster['paymentVoucherAutoID'] = $last_id;
            $dataiomaster['transactionAmount'] = $detailtblvoucher['transactionamount'];
            $dataiomaster['companyLocalAmount'] = ($detailtblvoucher['transactionamount'] / $mastertblvoucher['companyLocalExchangeRate']);
            $dataiomaster['companyReportingAmount'] = ($detailtblvoucher['transactionamount'] / $mastertblvoucher['companyReportingExchangeRate']);
            $dataiomaster['partyCurrencyAmount'] = ($detailtblvoucher['transactionamount'] / $mastertblvoucher['partyExchangeRate']);

            $dataiomaster['bankCurrencyAmount'] = ($mastertblvoucher['bankCurrencyExchangeRate'] == 0) ? '0' : ($detailtblvoucher['transactionamount'] / $mastertblvoucher['bankCurrencyExchangeRate']);
            $this->db->where('voucherAutoID', $system_id);
            $this->db->where('companyID', current_companyID());
            $this->db->update('srp_erp_iouvouchers', $dataiomaster);

            $this->db->where('GLAutoID', $data['bankGLAutoID']);
            $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));


            foreach ($detailtbl as $val) {
                $gldes = $this->db->query("select systemAccountCode,GLSecondaryCode,GLDescription,subCategory from srp_erp_chartofaccounts where companyID = $comapnyid And GLAutoID = {$val['glAutoID']}")->row_array();

                $mastertblvoucherdet = $this->db->query("Select vouchers.*,currency.CurrencyCode,segment.segmentCode from srp_erp_iouvouchers vouchers LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = vouchers.transactionCurrencyID LEFT JOIN srp_erp_segment segment on segment.segmentID = vouchers.segmentID where voucherAutoID = {$val['voucherAutoID']} AND vouchers.companyID = $comapnyid")->row_array();


                $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
                $this->db->where('payVoucherAutoId', $last_id);
                $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();
                $segment = $mastertblvoucherdet['segmentCode'];
                $datadetail['payVoucherAutoId'] = $last_id;
                $datadetail['GLAutoID'] = $val['glAutoID'];
                $datadetail['systemGLCode'] = $gldes['systemAccountCode'];
                $datadetail['GLCode'] = $gldes['GLSecondaryCode'];
                $datadetail['GLDescription'] = $gldes['GLDescription'];
                $datadetail['GLType'] = $gldes['subCategory'];
                $datadetail['segmentID'] = $mastertblvoucherdet['segmentID'];
                $datadetail['segmentCode'] = $mastertblvoucherdet['segmentCode'];
                $datadetail['transactionCurrencyID'] = $mastertblvoucherdet['transactionCurrencyID'];
                $datadetail['transactionCurrency'] = $mastertblvoucherdet['transactionCurrency'];
                $datadetail['transactionExchangeRate'] = $mastertblvoucherdet['transactionExchangeRate'];
                $datadetail['transactionAmount'] = $val['transactionAmount'];
                $datadetail['companyLocalCurrencyID'] = $mastertblvoucherdet['companyLocalCurrencyID'];
                $datadetail['companyLocalCurrency'] = $mastertblvoucherdet['companyLocalCurrency'];
                $datadetail['companyLocalExchangeRate'] = $mastertblvoucherdet['companyLocalExchangeRate'];
                $datadetail['companyLocalAmount'] = ($datadetail['transactionAmount'] / $mastertblvoucherdet['companyLocalExchangeRate']);
                $datadetail['companyReportingCurrencyID'] = $mastertblvoucherdet['companyReportingCurrencyID'];
                $datadetail['companyReportingCurrency'] = $mastertblvoucherdet['companyReportingCurrency'];
                $datadetail['companyReportingExchangeRate'] = $mastertblvoucherdet['companyReportingExchangeRate'];
                $datadetail['companyReportingAmount'] = ($datadetail['transactionAmount'] / $mastertblvoucherdet['companyReportingExchangeRate']);
                $datadetail['partyCurrency'] = $mastertblvoucherdet['partyCurrency'];
                $datadetail['partyExchangeRate'] = $mastertblvoucherdet['partyExchangeRate'];
                $datadetail['partyAmount'] = ($datadetail['transactionAmount'] / $mastertblvoucherdet['partyExchangeRate']);
                $datadetail['description'] = $val['description'];
                $datadetail['type'] = 'GL';
                $datadetail['companyCode'] = $this->common_data['company_data']['company_code'];
                $datadetail['companyID'] = $this->common_data['company_data']['company_id'];
                $datadetail['createdUserGroup'] = $this->common_data['user_group'];
                $datadetail['createdPCID'] = $this->common_data['current_pc'];
                $datadetail['createdUserID'] = $this->common_data['current_userID'];
                $datadetail['createdUserName'] = $this->common_data['current_user'];
                $datadetail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_paymentvoucherdetail', $datadetail);
                $this->session->set_flashdata('s', 'Donor Collection Approved Successfully.');
                $this->db->trans_complete();
            }
            $this->payment_voucher_confirmation_iou_approval($last_id);

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

    function fetch_empyoyee($id)
    {
        $this->db->select('Ename1,Ename2,Ename3,Ename4,ECode,EIdNo,EcAddress1,EcAddress2,EcAddress3,EpTelephone,EpFax,EEmail');
        $this->db->where('EIdNo', $id);
        $this->db->from('srp_employeesdetails');
        return $this->db->get()->row_array();
    }


    function delete_iou_voucher_delete()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_iouvoucherdetails');
        $this->db->where('voucherAutoID', trim($this->input->post('voucherAutoID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'Please delete all detail records before deleting this document.');
            return true;
        } else {
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('voucherAutoID', trim($this->input->post('voucherAutoID') ?? ''));
            $this->db->update('srp_erp_iouvouchers', $data);
            $this->session->set_flashdata('s', 'IOU Voucher Deleted Successfully.');
            return true;
        }
    }

    function reopern_iou_voucher()
    {

        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('voucherAutoID', trim($this->input->post('voucherAutoID') ?? ''));
        $this->db->update('srp_erp_iouvouchers', $data);
        return array('s', 'IOU Voucher Re Opened Successfully.');

    }

    function save_iou_catergory()
    {
        // ini_set('display_errors', 1);
        // error_reporting(E_ALL);
        
        $this->db->trans_start();

        $mandatoryInputfField = $this->input->post(trim('mandatoryInputfField'));
        $mandatoryInputfField_exist = $this->input->post('mandatoryInputfField_exist');
        $mandatoryInputfField_exist_ID = $this->input->post('mandatoryInputfField_exist_ID');

        $type = $this->input->post('type');
        $maxLimit = $this->input->post('maxValue');
        $validityperiod = $this->input->post('validity_dperiod');

        $gldes = explode('|', trim($this->input->post('gldes') ?? ''));
        $expenceclaimid = trim($this->input->post('expenseClaimCategoriesAutoID') ?? '');
        $description = $this->input->post('Description');
        $companyid = current_companyID();

        if($type == 2){
            $data['type'] = 2;
        }
        elseif ($type == 3) {
            $data['type'] = 3;
            $data['maxLimit'] = $maxLimit;
            $data['validityPeriod'] = $validityperiod;
            $data['isDeductable'] = $this->input->post('is_deductable');
        }

        if($type==2 && !empty($mandatoryInputfField)){
            $data['isRequiredFields'] = 1;
        }

        $data['claimcategoriesDescription'] = $this->input->post('Description');

        $data['glAutoID'] = $this->input->post('glcode');
        $data['glCode'] = $gldes[0];
        $data['glCodeDescription'] = $gldes[1];
        $data['fuelUsageYN'] = $this->input->post('isfueluage');


        if (($expenceclaimid)) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->set('timestamp', current_date(true));
            $this->db->where('expenseClaimCategoriesAutoID', $expenceclaimid);
            $this->db->update('srp_erp_expenseclaimcategories', $data);
    
            $this->db->trans_complete();

            $this->save_iou_fields($expenceclaimid, $mandatoryInputfField, $mandatoryInputfField_exist, $mandatoryInputfField_exist_ID);

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'IOU Category Update Failed ' /*. $this->db->_error_message()*/);
                    $this->db->trans_rollback();
                    return array('status' => false);

                } else {
                    $this->session->set_flashdata('s', 'IOU Category Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true);
                }

        } else {
            $isExist = $this->db->query("SELECT expenseClaimCategoriesAutoID FROM srp_erp_expenseclaimcategories WHERE claimcategoriesDescription = '$description'And companyID = $companyid ")->row('expenseClaimCategoriesAutoID');
            if (isset($isExist)) {
                return array('e', 'This Expense Category already Exists');
            } else {
                $data['companyID'] = current_companyID();
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['timestamp'] = current_date(true);
                $this->db->insert('srp_erp_expenseclaimcategories', $data);

                $last_id = $this->db->insert_id();

                $this->db->trans_complete();

                $this->save_iou_fields($last_id, $mandatoryInputfField, null, null);

                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'IOU Category  Saved Failed ' /*. $this->db->_error_message()*/);
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'IOU Category Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id, 'fields' => $mandatoryInputfField);
                }

                
            }
        }
    }


/** IOU Expense Category - add Expense Category Mandatory fields*/
/** for : srp_erp_document_custom_fields */
function save_iou_fields($expenceclaimid, $mandatoryInputfField, $mandatoryInputfField_exist, $mandatoryInputfField_exist_ID)
{
    $this->db->trans_start();
    $companyid = current_companyID();
    $documentDetailID = $expenceclaimid;

    if($mandatoryInputfField_exist_ID)
    {
        foreach($mandatoryInputfField_exist_ID as $i=>$v){
            $id = $v;

            $data['fieldName'] = $mandatoryInputfField_exist[$i];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('id', $id);
            $this->db->update('srp_erp_document_custom_fields', $data);
        }
    }


    if(!empty($mandatoryInputfField))
    {
        foreach($mandatoryInputfField as $field)
        {
            $data = array(
                'fieldName' => $field,
            );
            $data['fieldType'] = 1;
            $data['documentCode'] = 'IOU-E';
            $data['documentDetailID'] = $documentDetailID;

                $data['companyID'] = $companyid;
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['timestamp'] = current_date(true);

            $fname = $this->db->escape($data['fieldName']);
            $isExist = $this->db->query("SELECT id FROM srp_erp_document_custom_fields WHERE documentDetailID = $documentDetailID AND fieldName = $fname And fieldType = 1 And companyID = $companyid ")->row('id');
            
            if(!isset($isExist) && !empty($field)) {
                $this->db->insert('srp_erp_document_custom_fields', $data); 
            }
            else
            {
                $this->session->set_flashdata('e', 'IOU Expense Field Already Exists.');
            }  
        }
    }
    
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $this->session->set_flashdata('e', 'IOU Expense Fields  Saved Failed ');
        $this->db->trans_rollback();
        return array('status' => false);
    } else {
        $this->session->set_flashdata('s', 'IOU Expense Fields Saved Successfully.');
        $this->db->trans_commit();
        return array('status' => true);
    }


}

/**SMSD */
/** IOU Expense - add Mandetory field values (that declared in IOU Expense Category) */
    function add_iouBooking_requiredFields()
    {
        $this->db->trans_start();

        $bookingMasterID = $this->input->post('bookingMasterID');
        $bookingDetailsID = $this->input->post('bookingDetailsID');
        $documentDetailId= $this->input->post('documentDetailID');
        $requiredFieldID = $this->input->post('requiredFieldID');
        $fieldvalue = $this->input->post(trim('fieldvalue'));
        $companyID = $this->common_data['company_data']['company_id'];
        
        if($requiredFieldID){
            foreach($requiredFieldID as $key => $val)
            {
                //$bookingDetailsID = $bookingDetailsID[$key];
                //$bookingMasterID = $bookingMasterID[$key];
                //$requiredFieldID = $requiredFieldID[$key];
                //$fieldvalue = $fieldvalue[$key];

                $isExist = $this->db->query("SELECT fieldAutoID FROM srp_erp_ioubookingdetails_fields WHERE bookingMasterID = {$bookingMasterID[$key]} AND bookingDetailsID = {$bookingDetailsID[$key]} AND requiredFieldID = $val AND companyID = $companyID")->row('fieldAutoID');
                // $isExist = $this->db->select('fieldvalue')
                //             ->from('srp_erp_ioubookingdetails_fields')
                //             ->where('bookingDetailsID', $bookingDetailsID[$key])
                //             ->where('bookingMasterID', $bookingMasterID[$key])
                //             //->where('fieldvalue', $fieldvalue[$key])
                //             ->where('requiredFieldID', $val)
                //             ->where('companyID', $companyID)
                //             ->get()->row('fieldvalue');
                //print_r($isExist);exit;
                if(!$isExist){

                    $data['fieldvalue'] = $fieldvalue[$key];
                    $data['requiredFieldID'] = $val;
                    $data['bookingDetailsID'] = $bookingDetailsID[$key];
                    $data['bookingMasterID'] = $bookingMasterID[$key];
            
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['companyID'] = current_companyID();
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['timestamp'] = current_date(true);
                    
                    $this->db->insert('srp_erp_ioubookingdetails_fields', $data);

                }else{

                    $data['fieldvalue'] = $fieldvalue[$key];
                    $data['requiredFieldID'] = $val;
                    $data['bookingDetailsID'] = $bookingDetailsID[$key];
                    $data['bookingMasterID'] = $bookingMasterID[$key];

                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];

                    $this->db->where('fieldAutoID',$isExist);
                    $this->db->update('srp_erp_ioubookingdetails_fields', $data);

                }
                
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Field values  Saved Failed ' /*. $this->db->_error_message()*/);
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Field values Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }   
        }
    }

    function load_iou_cat_header()
    {
        $comanyid = current_companyID();
        $ioucatid = trim($this->input->post('expenseClaimCategoriesAutoID') ?? '');

        $data['expenseclaimcategories'] = $this->db->query("Select * from srp_erp_expenseclaimcategories where companyID = $comanyid And expenseClaimCategoriesAutoID = $ioucatid ")->row_array();/**SMSD */

        $data['documentcustomfields'] = $this->db->query("Select * from srp_erp_document_custom_fields where companyID = $comanyid And documentDetailID = $ioucatid ")->result_array();/**SMSD */

        //$data['html'] = $this->load->view('system/iou/ajax/iou_expenseCategory_htmlView', $data,true);
       
        return $data;
    }

    function delete_ioucategory()
    {
        $ioucatid = trim($this->input->post('expenseClaimCategoriesAutoID') ?? '');
        $this->db->delete('srp_erp_expenseclaimcategories', array('expenseClaimCategoriesAutoID' => $ioucatid));
        return true;
    }
/**SMSD */
    function delete_ioucategory_field(){
        $field_id = trim($this->input->post('id') ?? '');
        $this->db->delete('srp_erp_document_custom_fields', array('id' => $field_id));
        return true;
    }

    function save_iou_booking()
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $date_format_policy = date_format_policy();
        $employee = explode('|', trim($this->input->post('employeeid') ?? ''));
        $ioumasterbookingautoid = trim($this->input->post('bookingautoid') ?? '');
        $comment = $this->input->post('comment');
        $documentDate = $this->input->post('bookingdate');
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $employeedet = explode('|', trim($this->input->post('empname') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
//        $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
//        $FYBegin = input_format_date($year[0], $date_format_policy);
//        $FYEnd = input_format_date($year[1], $date_format_policy);
        $transactioncurrencyid = trim($this->input->post('transactionCurrencyID') ?? '');
        $voucherautoid = trim($this->input->post('iouvoucher') ?? '');
        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);

            if ($financeyearperiodYN == 1) {
                $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
                $FYBegin = input_format_date($year[0], $date_format_policy);
                $FYEnd = input_format_date($year[1], $date_format_policy);
            } else {
                $financeYearDetails = get_financial_year($format_documentDate);
                if (empty($financeYearDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {
                    $FYBegin = $financeYearDetails['beginingDate'];
                    $FYEnd = $financeYearDetails['endingDate'];
                    $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                    $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
                }
                $financePeriodDetails = get_financial_period_date_wise($format_documentDate);

                if (empty($financePeriodDetails)) {
                    return array('e', 'Finance period not found for the selected document date');
                    exit;
                } else {

                    $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
                }
            }

        }

        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = $FYBegin;
        $data['FYEnd'] = $FYEnd;
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        $data['companyCode'] = $company_code;

        $compfinaceStartend = $this->db->query("Select beginingDate,endingDate from srp_erp_companyfinanceyear where companyFinanceYearID = {$data['companyFinanceYearID'] }")->row_array();

        if ($employee[1] == 1) {
            $empdet = $this->db->query("Select * from srp_employeesdetails where EIdNo = $employee[0]")->row_array();
        } else if ($employee[1] == 2) {
            $empdet = $this->db->query("select users.currencyID as payCurrencyID,currency.CurrencyCode as payCurrency from srp_erp_iouusers users LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = users.currencyID where companyID = $companyID and userID = $employee[0]")->row_array();
        }
        $data['empID'] = $employee[0];
        $data['userType'] = $employee[1];
        $data['documentID'] = 'IOUE';
        $data['iouVoucherAutoID'] = $voucherautoid;
        $data['FYPeriodDateFrom'] = $compfinaceStartend['beginingDate'];
        $data['FYPeriodDateTo'] = $compfinaceStartend['endingDate'];
        $data['empName'] = $employeedet[1];
        $data['bookingDate'] = $format_documentDate;
        $data['comments'] = str_replace('<br />', PHP_EOL, $comment);
        $data['segmentID'] = $segment[0];
        $data['segmentCode'] = $segment[1];
        $data['transactionCurrencyID'] = $this->input->post('transactionCurrencyID');
        $data['transactionCurrency'] = $currency_code[0];
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        $data['empCurrencyID'] = $empdet['payCurrencyID'];
        $data['empCurrency'] = $empdet['payCurrency'];
        $data['pullFromFuelYN'] = $this->input->post('isfueluage');

        $partyexchange = currency_conversionID($data['transactionCurrencyID'], $data['empCurrencyID']);
        $data['empCurrencyExchangeRate'] = $partyexchange['conversion'];
        $data['empCurrencyDecimalPlaces'] = $partyexchange['DecimalPlaces'];

        if ($ioumasterbookingautoid) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];


            $voucheridexist = $this->db->query("select bookingMasterID,vouchers.iouCode from srp_erp_ioubookingmaster bookmaster Inner join srp_erp_iouvouchers vouchers on vouchers.voucherAutoID = bookmaster.iouVoucherAutoID where iouVoucherAutoID = $voucherautoid AND bookingMasterID!=$ioumasterbookingautoid AND bookmaster.approvedYN != 1 AND bookmaster.companyID = $companyID")->row_array();
            if (!empty($voucheridexist)) {
                return array('e', 'This ' . $voucheridexist['iouCode'] . ' IOU Voucher already pulled to an Un Approved Document');
            } else {
                $this->db->where('bookingMasterID', $ioumasterbookingautoid);
                $this->db->update('srp_erp_ioubookingmaster', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Error in IOU Expense  Update ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'IOU Expense  Updated successfully.', $ioumasterbookingautoid,$data['pullFromFuelYN']);
                }
            }

        } else {
            $voucheridexist = $this->db->query("select bookingMasterID,vouchers.iouCode from srp_erp_ioubookingmaster bookmaster Inner join srp_erp_iouvouchers vouchers on vouchers.voucherAutoID = bookmaster.iouVoucherAutoID where iouVoucherAutoID = $voucherautoid  AND bookmaster.approvedYN != 1 AND bookmaster.companyID = $companyID")->row_array();
            if (!empty($voucheridexist) || $voucheridexist != '') {
                return array('e', 'This ' . $voucheridexist['iouCode'] . ' IOU Voucher already pulled to an Un Approved Document');
            } else {
                $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM srp_erp_ioubookingmaster WHERE companyID={$companyID}")->row_array();
                $data['serialNo'] = $serial['serialNo'];
                $data['bookingCode'] = ($company_code . '/' . 'IOUE' . str_pad($data['serialNo'], 6,
                        '0', STR_PAD_LEFT));
                $data['companyID'] = $companyID;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];

                $this->db->insert('srp_erp_ioubookingmaster', $data);
                $last_id = $this->db->insert_id();


                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Error Occured' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', '' . $data['bookingCode'] . ' IOU Expense created successfully.', $last_id,$data['pullFromFuelYN']);

                }
            }


        }


    }

    function fetch_iou_booking_detail()
    {
        $comapnyid = current_companyID();

        $ioubookingmasterid = $this->input->post('IOUbookingmasterid');

        $ioubookingmaster = $this->db->query("select empID,transactionCurrencyID,userType from srp_erp_ioubookingmaster where bookingMasterID = $ioubookingmasterid  AND companyID = $comapnyid")->row_array();


        $data['detail'] = $this->db->query("SELECT vouchers.voucherAutoID,vouchers.iouCode,IFNULL(sum( ioudet.transactionAmount ),0)  AS transactionAmount,IFNULL(bookingdetail.transactionAmount,0)  as bookingamt,vouchers.narration FROM srp_erp_iouvouchers vouchers LEFT JOIN srp_erp_iouvoucherdetails ioudet ON ioudet.voucherAutoID = vouchers.voucherAutoID LEFT JOIN (select sum( bookingdet.transactionAmount ) AS transactionAmount,iouVoucherAutoID from srp_erp_ioubookingmaster booking LEFT JOIN srp_erp_ioubookingdetails bookingdet on bookingdet.bookingMasterID = booking.bookingMasterID where booking.empID = {$ioubookingmaster['empID']} AND booking.userType = {$ioubookingmaster['userType']} AND booking.transactionCurrencyID = {$ioubookingmaster['transactionCurrencyID']} AND booking.companyID = $comapnyid GROUP BY bookingdet.iouVoucherAutoID)bookingdetail on bookingdetail.iouVoucherAutoID = vouchers.voucherAutoID WHERE vouchers.confirmedYN = 1 AND vouchers.approvedYN = 1 AND vouchers.companyID = $comapnyid AND vouchers.transactionCurrencyID  = {$ioubookingmaster['transactionCurrencyID']}  AND vouchers.empID = {$ioubookingmaster['empID']} AND vouchers.userType = {$ioubookingmaster['userType']} GROUP BY ioudet.voucherAutoID")->result_array();

        $data['expence'] = $this->db->query("select expenseClaimCategoriesAutoID,claimcategoriesDescription from srp_erp_expenseclaimcategories where type = 2 AND companyID = $comapnyid")->result_array();

        $data['segment'] = $this->db->query("select segmentCode,description,segmentID from srp_erp_segment where status = 1 AND companyID = $comapnyid ")->result_array();

        return $data;

    }

    function save_ioubooking_amt()
    {
        $amount = $this->input->post('amounts');
        $bookingmasterid = $this->input->post('IOUbookingmasterid');
        // $voucherid = $this->input->post('voucherid');
        $expencecate = $this->input->post('category');
        $description = $this->input->post('description');
        $segment = $this->input->post('segment');
            $invoiceNumber = $this->input->post('invoiceNumber');/**SMSD */
            $supplierName = $this->input->post('supplierName');/**SMSD */
            $vatNumber = $this->input->post('vatNumber');/**SMSD */
            $supplierMobile = $this->input->post('supplierMobile');/**SMSD */
            $vat = $this->input->post('vat');/**SMSD */
            $netAmounts = $this->input->post('netAmounts');/**SMSD */
        $transactioncurrency = $this->input->post('transactioncurrencyid');
        $employee = explode('|', trim($this->input->post('employeeid') ?? ''));
        $fuelusageID = $this->input->post('fuelusge');
        $currency = $this->db->query("select * from srp_erp_currencymaster where currencyID = $transactioncurrency")->row_array();
        $companyID = current_companyID();
        if ($employee[1] == 1) {
            $empdet = $this->db->query("Select * from srp_employeesdetails where EIdNo = $employee[0]")->row_array();
        } else if ($employee[1] == 2) {
            $empdet = $this->db->query("select users.currencyID as payCurrencyID,currency.CurrencyCode as payCurrency from srp_erp_iouusers users LEFT JOIN srp_erp_currencymaster currency on currency.currencyID = users.currencyID where companyID = $companyID and userID = $employee[0]")->row_array();
        }
        foreach ($segment as $key => $val) {
            $expenceglauto = $this->db->query("select glAutoID,claimcategoriesDescription from srp_erp_expenseclaimcategories where  expenseClaimCategoriesAutoID = $expencecate[$key]")->row_array();
            $voucherid = $this->db->query("select iouVoucherAutoID from srp_erp_ioubookingmaster where  bookingMasterID = $bookingmasterid AND companyID = $companyID")->row_array();

            $data['bookingMasterID'] = $bookingmasterid;
            $data['expenseCategoryAutoID'] = $expencecate[$key];
            $data['categoryDescription'] = $expenceglauto['claimcategoriesDescription'];
            $data['iouVoucherAutoID'] = $voucherid['iouVoucherAutoID'];
            $data['description'] = $description[$key];
            $data['segmentID'] = $segment[$key];
            $data['transactionAmount'] = $amount[$key];
            $data['fuelusageID'] = $fuelusageID[$key];
            $data['transactionCurrencyID'] = $transactioncurrency;
            $data['transactionCurrency'] = $currency['CurrencyCode'];
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalAmount'] = ($data['transactionAmount'] / $data['companyLocalExchangeRate']);
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['empCurrencyID'] = $empdet['payCurrencyID'];
            $data['empCurrency'] = $empdet['payCurrency'];
            $data['glAutoID'] = $expenceglauto['glAutoID'];
            $partyexchange = currency_conversionID($data['transactionCurrencyID'], $data['empCurrencyID']);
            $data['empCurrencyExchangeRate'] = $partyexchange['conversion'];
            $data['empCurrencyAmount'] = ($data['transactionAmount'] / $data['empCurrencyExchangeRate']);
            $data['empCurrencyDecimalPlaces'] = $partyexchange['DecimalPlaces'];
            $data['companyID'] = current_companyID();
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['timestamp'] = current_date(true);
            $this->db->insert('srp_erp_ioubookingdetails', $data);
            $last_id = $this->db->insert_id();

            $this->add_requiredfieldIds_to_srp_erp_ioubookingdetails_fields($last_id,$bookingmasterid);/**SMSD */

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'IOU Expense Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'IOU Expense Detail Saved Successfully.');
        }
    }

    /**SMSD */
    function add_requiredfieldIds_to_srp_erp_ioubookingdetails_fields($bookingDetailID,$bookingmasterid){

        $companyId = current_companyID();

                $this->db->select('expenseCategoryAutoID');
                $this->db->where('bookingDetailsID', trim($bookingDetailID));
                $this->db->where('bookingMasterID', trim($bookingmasterid));
                $this->db->where('companyID', $companyId);
                $this->db->from('srp_erp_ioubookingdetails');
                $expenseCategoryAutoID = $this->db->get()->result_array();

                if($expenseCategoryAutoID){
                    foreach($expenseCategoryAutoID as $documentDetailID){
                        $this->db->select('*');
                        $this->db->where('documentDetailID', $documentDetailID['expenseCategoryAutoID']);
                        $this->db->where('documentCode', 'IOU-E');
                        $this->db->where('companyID', $companyId);
                        $this->db->from('srp_erp_document_custom_fields');
                        $requiredFieldIds = $this->db->get()->result_array();
                    }
                }

                if($requiredFieldIds){
                    foreach($requiredFieldIds as $row){
                        $data['requiredFieldID'] = $row['id'];
                        $data['companyID'] = $companyId;
                        $data['bookingDetailsID'] = $bookingDetailID;
                        $data['bookingMasterID'] = $bookingmasterid;
                    
                        $this->db->insert('srp_erp_ioubookingdetails_fields', $data);
                    
                    }

                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'failed to add requiredFieldID on srp_erp_ioubookingdetails_fields');
                    $this->db->trans_rollback();
                    return array('e', 'Error while deleting!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('s', 'successfully added requiredFieldID on srp_erp_ioubookingdetails_fields.');
                    return true;
                }
        

    }

    function load_voucher_booking_header()
    {
        $convertFormat = convert_date_format_sql();
        $ioubookingmasterid = $this->input->post('IOUbookingmasterid');
        $companyid = current_companyID();

        $data = $this->db->query("SELECT *,DATE_FORMAT(bookingDate,'{$convertFormat}') AS bookingDate from srp_erp_ioubookingmaster booking  WHERE bookingMasterID  = $ioubookingmasterid AND booking.companyID = $companyid")->row_array();

        return $data;

    }

    function delete_ioubooking_detail()
    {
        $ioubookingdetid = trim($this->input->post('bookingdetailid') ?? '');
        $this->db->delete('srp_erp_ioubookingdetails', array('bookingDetailsID' => $ioubookingdetid));
        return true;
    }

    function fetch_iou_booking_details()
    {
        $ioubookigdetailid = trim($this->input->post('bookingDetailsID') ?? '');
        $iouVoucherAutoID = trim($this->input->post('iouVoucherAutoID') ?? '');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT ioubookdet.*,vouchermaster.iouCode AS vouchermasterioucode,SUM(ioubookdet.transactionAmount) as ioutotaltransactionamt,vouchermaster.transactionAmount as ioutransactionAmount,segment.segmentCode as detailsegmentcode,ioubookdet.description as ioubookingdescription,ioubookdet.segmentID as detailssegmentid,ioubookdet.transactionAmount as ioutransactionamt FROM
srp_erp_ioubookingdetails ioubookdet LEFT join(SELECT sum(transactionAmount) as totaltransactionamt,bookingDetailsID from srp_erp_ioubookingdetails WHERE iouVoucherAutoID = $iouVoucherAutoID  AND companyid = $companyid) iouboookdet on  iouboookdet.bookingDetailsID = ioubookdet.bookingDetailsID LEFT JOIN srp_erp_iouvouchers vouchermaster ON ioubookdet.iouVoucherAutoID = vouchermaster.voucherAutoID LEFT JOIN srp_erp_segment segment on segment.segmentID = ioubookdet.segmentID  WHERE ioubookdet.bookingDetailsID = $ioubookigdetailid AND ioubookdet.companyID = $companyid AND ioubookdet.iouVoucherAutoID = $iouVoucherAutoID")->row_array();

        return $data;
    }

    function fetch_iou_booking($ioubookingid)
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $data['master'] = $this->db->query("SELECT  bookingCode,bookingMasterID,bookmaster.empName as employeename,approvedbyEmpName,confirmedYN,approvedYN,transactionCurrency as currencyid,DATE_FORMAT(bookingDate,'{$convertFormat}') AS bookingDate,bookmaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,DATE_FORMAT(approvedDate,'{$convertFormat}') AS approvedDate,comments,segmentCode FROM srp_erp_ioubookingmaster bookmaster left join srp_employeesdetails employee on employee.EIdNo = bookmaster.empID WHERE bookingMasterID = $ioubookingid AND companyID = $companyid")->row_array();

        $data['detail'] = $this->db->query("select *,bookingdet.description AS bookingdescription,bookingdet.categoryDescription,bookingdet.bookingDetailsID,bookingdet.bookingMasterID,bookingdet.expenseCategoryAutoID, segment.segmentCode,catergorie.isRequiredFields,bookingdet.expenseCategoryAutoID,vouchers.iouCode,bookingdet.transactionAmount AS bookingAmount,bookingdet.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlacesbooking 
FROM srp_erp_ioubookingdetails bookingdet LEFT JOIN srp_erp_iouvouchers vouchers ON vouchers.voucherAutoID = bookingdet.iouVoucherAutoID LEFT JOIN srp_erp_expenseclaimcategories catergorie ON catergorie.expenseClaimCategoriesAutoID = bookingdet.expenseCategoryAutoID LEFT JOIN srp_erp_segment segment ON segment.segmentID = bookingdet.segmentID WHERE
bookingdet.companyID = $companyid AND bookingMasterID = $ioubookingid")->result_array();

        return $data;
    }

    function fetch_double_entry_iou_bookingded($ioubookingmasterid, $code = null)

    {

        $gl_array = array();
        $inv_total = 0;
        $cr_total = 0;
        $party_total = 0;
        $companyLocal_total = 0;
        $companyReporting_total = 0;
        $tax_total = 0;
        $gl_array['gl_detail'] = array();
        $this->db->select('bookmaster.*,bookmaster.approvedByEmpID as approvedByEmpIDbook,bookmaster.approvedByEmpName as approvedByEmpNamebook,approvedYN,employee.Ename2 as employeename,bookmaster.empID as empID,employee.ECode as ECode,bookmaster.companyCode as companyCodebooking');
        $this->db->where('bookingMasterID', $ioubookingmasterid);
        $this->db->join('srp_employeesdetails employee', 'bookmaster.empID = employee.EIdNo', 'left');
        $master = $this->db->get('srp_erp_ioubookingmaster bookmaster')->row_array();


        $this->db->select('bookingMasterID,bookingDetailsID,srp_erp_ioubookingdetails.glAutoID as GLAutoID,chart.systemAccountCode as SystemGLCode,chart.GLSecondaryCode as GLCode,chart.GLDescription as GLDescription,chart.subCategory as GLType,srp_erp_ioubookingdetails.transactionAmount,srp_erp_ioubookingdetails.segmentID as segmentID,segment.segmentCode as segmentCode,companyLocalExchangeRate,companyReportingExchangeRate,empCurrencyExchangeRate as partyExchangeRate,srp_erp_ioubookingdetails.description as ioudescription');
        $this->db->where('bookingMasterID', $ioubookingmasterid);
        $this->db->join('srp_erp_chartofaccounts chart', 'chart.GLAutoID = srp_erp_ioubookingdetails.GLAutoID', 'left');
        $this->db->join('srp_erp_segment segment', 'segment.segmentID = srp_erp_ioubookingdetails.segmentID', 'left');
        $detail = $this->db->get('srp_erp_ioubookingdetails')->result_array();

        $companyid = current_companyID();

        $credittot = $this->db->query("SELECT bookingMasterID,srp_erp_companycontrolaccounts.GLAutoID as glautoidiou,srp_erp_ioubookingdetails.glAutoID AS GLAutoID,chart.systemAccountCode AS SystemGLCode,chart.GLSecondaryCode AS GLCode,chart.GLDescription AS GLDescription,chart.subCategory AS GLType,sum(srp_erp_ioubookingdetails.transactionAmount) as transactionamt,srp_erp_ioubookingdetails.segmentID AS segmentID,segment.segmentCode AS segmentCode,companyLocalExchangeRate,companyReportingExchangeRate,empCurrencyExchangeRate AS partyExchangeRate FROM srp_erp_ioubookingdetails
LEFT JOIN srp_erp_companycontrolaccounts on srp_erp_companycontrolaccounts.controlAccountType = \"IOU\" LEFT JOIN srp_erp_chartofaccounts chart on srp_erp_companycontrolaccounts.GLAutoID = chart.GLAutoID LEFT JOIN srp_erp_segment segment ON segment.segmentID = srp_erp_ioubookingdetails.segmentID WHERE bookingMasterID = $ioubookingmasterid AND srp_erp_companycontrolaccounts.companyID = $companyid")->row_array();


        $deetaildes = $this->db->query("SELECT description as ioudescription from srp_erp_ioubookingdetails where bookingMasterID = $ioubookingmasterid AND companyID = $companyid")->row_array();

        $m_arr = array();
        $p_arr = array();
        $e_m_arr = array();
        $e_p_arr = array();
        $debitNoteAmount = 0;


        for ($i = 0; $i < count($detail); $i++) {
            $assat_entry_arr['auto_id'] = $detail[$i]['bookingDetailsID'];
            $assat_entry_arr['gl_auto_id'] = $detail[$i]['GLAutoID'];
            $assat_entry_arr['gl_code'] = $detail[$i]['SystemGLCode'];
            $assat_entry_arr['secondary'] = $detail[$i]['GLCode'];
            $assat_entry_arr['gl_desc'] = $detail[$i]['GLDescription'];
            $assat_entry_arr['gl_type'] = $detail[$i]['GLType'];
            $assat_entry_arr['narrationioudetail'] = $detail[$i]['ioudescription'];
            $assat_entry_arr['segment_id'] = $detail[$i]['segmentID'];
            $assat_entry_arr['segment'] = $detail[$i]['segmentCode'];
            $assat_entry_arr['projectID'] = null;
            $assat_entry_arr['projectExchangeRate'] = null;
            $assat_entry_arr['isAddon'] = 0;
            $assat_entry_arr['taxMasterAutoID'] = null;
            $assat_entry_arr['partyVatIdNo'] = null;
            $assat_entry_arr['subLedgerType'] = 0;
            $assat_entry_arr['subLedgerDesc'] = null;
            $assat_entry_arr['partyContractID'] = null;
            $assat_entry_arr['partyType'] = 'Employee';
            $assat_entry_arr['partyAutoID'] = $master['empID'];
            $assat_entry_arr['partySystemCode'] = $master['ECode'];
            $assat_entry_arr['partyName'] = $master['employeename'];
            $assat_entry_arr['partyCurrencyID'] = $master['empCurrencyID'];
            $assat_entry_arr['partyCurrency'] = $master['empCurrency'];
            $assat_entry_arr['transactionExchangeRate'] = 1;
            $assat_entry_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $assat_entry_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $assat_entry_arr['partyExchangeRate'] = $master['empCurrencyExchangeRate'];
            $assat_entry_arr['partyCurrencyAmount'] = ($detail[$i]['transactionAmount'] / $master['empCurrencyExchangeRate']);
            $assat_entry_arr['partyCurrencyDecimalPlaces'] = $master['empCurrencyDecimalPlaces'];
            $assat_entry_arr['amount_type'] = 'dr';
            if ($detail[$i]['transactionAmount'] >= 0) {
                $assat_entry_arr['gl_dr'] = $detail[$i]['transactionAmount'];
                $assat_entry_arr['gl_cr'] = 0;
                array_push($e_p_arr, $assat_entry_arr);
            } else {
                $assat_entry_arr['gl_dr'] = 0;
                $assat_entry_arr['gl_cr'] = $detail[$i]['transactionAmount'];
                $assat_entry_arr['amount_type'] = 'cr';
                array_push($e_m_arr, $assat_entry_arr);
            }

        }


        $gl_array['gl_detail'] = $p_arr;

        foreach ($m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_p_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }
        foreach ($e_m_arr as $key => $value) {
            array_push($gl_array['gl_detail'], $value);
        }


        $data_arr['auto_id'] = 0;
        $data_arr['gl_auto_id'] = $credittot['glautoidiou'];
        $data_arr['gl_code'] = $credittot['SystemGLCode'];
        $data_arr['secondary'] = $credittot['GLCode'];
        $data_arr['gl_desc'] = $credittot['GLDescription'];
        $data_arr['gl_type'] = $credittot['GLType'];
        $data_arr['narrationioudetail'] =  $deetaildes['ioudescription'];
        $data_arr['segment_id'] = $credittot['segmentID'];
        $data_arr['segment'] = $credittot['segmentCode'];
        $data_arr['gl_dr'] = 0;
        $data_arr['gl_cr'] = $credittot['transactionamt'];
        $data_arr['amount_type'] = 'cr';
        $data_arr['isAddon'] = 0;
        $data_arr['taxMasterAutoID'] = null;
        $data_arr['partyVatIdNo'] = null;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = 'Employee';
        $data_arr['partyAutoID'] = $master['empID'];
        $data_arr['partySystemCode'] = $master['ECode'];
        $data_arr['partyName'] = $master['employeename'];
        $data_arr['partyCurrencyID'] = $master['empCurrencyID'];
        $data_arr['partyCurrency'] = $master['empCurrency'];
        $data_arr['transactionExchangeRate'] = 1;
        $data_arr['partyExchangeRate'] = $master['empCurrencyExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data_arr['partyCurrencyAmount'] = ($credittot['transactionamt'] / $master['empCurrencyExchangeRate']);
        $data_arr['partyCurrencyDecimalPlaces'] = $master['empCurrencyDecimalPlaces'];
        array_push($gl_array['gl_detail'], $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'IOUE';
        $gl_array['name'] = 'IOU Booking';
        $gl_array['approved_YN'] = $master['approvedYN'];
        $gl_array['primary_Code'] = $master['bookingCode'];
        $gl_array['date'] = $master['bookingDate'];
        $gl_array['finance_year'] = '-';
        $gl_array['finance_period'] = '-';
        $gl_array['master_data'] = $master;

        return $gl_array;
    }

    function ioubooking_confirmation()
    {
        $ioubookingmasterid = trim($this->input->post('IOUbookingmasterid') ?? '');

        $this->db->select('bookingDetailsID');
        $this->db->where('bookingMasterID', $ioubookingmasterid);
        $this->db->from('srp_erp_ioubookingdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $this->db->select('bookingMasterID');
            $this->db->where('bookingMasterID', $ioubookingmasterid);
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_ioubookingmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            } else {
                $this->load->library('approvals');
                $this->db->select('bookingMasterID, documentID,bookingCode,bookingDate');
                $this->db->where('bookingMasterID', $ioubookingmasterid);
                $this->db->from('srp_erp_ioubookingmaster');
                $app_data = $this->db->get()->row_array();

                $validate_code = validate_code_duplication($app_data['bookingCode'], 'bookingCode', $ioubookingmasterid,'bookingMasterID', 'srp_erp_ioubookingmaster');
                if(!empty($validate_code)) {
                    $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    return false;
                }

                $auto_approval = get_document_auto_approval('IOUE');
             
                if($auto_approval == 0){
                    $approvals_status = $this->approvals->auto_approve($app_data['bookingMasterID'], 'srp_erp_ioubookingmaster', 'bookingMasterID', 'IOUE', $app_data['bookingCode'], $app_data['bookingDate']);
                    
                    //
                    $_POST['ioubookingid'] = $app_data['bookingMasterID'];
                    $_POST['status'] = 1;
                    $_POST['comments'] = 'Auto approved by the system';
                    $res = $this->save_ioub_approval();
                
                }else{
                    $approvals_status = $this->approvals->CreateApproval('IOUE', $app_data['bookingMasterID'], $app_data['bookingCode'], 'IOU Booking', 'srp_erp_ioubookingmaster', 'bookingMasterID');
                }
                


                $details = $this->db->query("select bookmaster.*,bookdetail.amount from srp_erp_ioubookingmaster bookmaster LEFT JOIN (select sum(transactionAmount) as amount,bookingMasterID from srp_erp_ioubookingdetails where bookingMasterID  = $ioubookingmasterid) bookdetail on bookdetail.bookingMasterID = bookmaster.bookingMasterID where bookmaster.bookingMasterID = $ioubookingmasterid")->row_array();
                if ($approvals_status == 1) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'transactionAmount' => ($details['amount'] / $details['transactionExchangeRate']),
                        'companyLocalAmount' => ($details['amount'] / $details['companyLocalExchangeRate']),
                        'companyLocalAmount' => ($details['amount'] / $details['companyLocalExchangeRate']),
                        'empCurrencyAmount' => ($details['amount'] / $details['empCurrencyExchangeRate'])
                    );
                    $this->db->where('bookingMasterID', trim($this->input->post('IOUbookingmasterid') ?? ''));
                    $this->db->update('srp_erp_ioubookingmaster', $data);

                    return array('error' => 0, 'message' => 'document successfully confirmed');
                } else {
                    return array('error' => 1, 'message' => 'Approval setting are not configured!, please contact your system team.');
                }
            }
        }
    }

    function delete_iou_booking_delete()
    {
        $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
                'iouVoucherAutoID' => null,
            );
            $this->db->where('bookingMasterID', trim($this->input->post('bookingMasterID') ?? ''));
            $this->db->update('srp_erp_ioubookingmaster', $data);

            $this->db->delete('srp_erp_ioubookingdetails', array('bookingMasterID' => trim($this->input->post('bookingMasterID') ?? '')));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error while deleting!');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('s', 'IOU Booking Deleted Successfully.');
                return true;
            }
    }

    function reopen_iou_booking()
    {

        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('bookingMasterID', trim($this->input->post('bookingMasterID') ?? ''));
        $this->db->update('srp_erp_ioubookingmaster', $data);
        return array('s', 'IOU Booking Re Opened Successfully.');

    }

    function save_ioub_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_code = trim($this->input->post('ioubookingid') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $auto_approval = get_document_auto_approval('IOUE');

        if($auto_approval != 0){
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'IOUE');
        }else{
            $approvals_status = 1; // document not been created to approvals
        }
        

        if ($approvals_status == 1) {

            $double_entry = $this->fetch_double_entry_iou_bookingded($system_code, 'IOUE');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['bookingMasterID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['bookingCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentType'] = null;
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['bookingDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['bookingDate']));

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
                $generalledger_arr[$i]['partyType'] = 'EMP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['empID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['ECode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['employeename'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['empCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['empCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['master_data']['empCurrencyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['empCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedByEmpIDbook'];
                $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedByEmpNamebook'];
                $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                $generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCodebooking'];
                $amount = $double_entry['gl_detail'][$i]['gl_cr'];
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'dr') {
                    $amount = ($double_entry['gl_detail'][$i]['gl_dr']);
                }
                if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                    $generalledger_arr[$i]['transactionAmount'] = round($amount * -1, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($amount * -1 / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($amount * -1 / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);

                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($amount * -1 / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);


                } else {
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                }
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
                $generalledger_arr[$i]['projectID'] = null;
                $generalledger_arr[$i]['projectExchangeRate'] = null;
                $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];

                $generalledger_arr[$i]['documentNarration'] = $double_entry['gl_detail'][$i]['narrationioudetail'];

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

        }
        if (!empty($generalledger_arr)) {
            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
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

    function ioubooking_submit()
    {
        $ioubookingmasterid = trim($this->input->post('IOUbookingmasterid') ?? '');

        $this->db->select('bookingDetailsID');
        $this->db->where('bookingMasterID', $ioubookingmasterid);
        $this->db->from('srp_erp_ioubookingdetails');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to Submit this document!');
        } else {
            $this->db->select('bookingMasterID');
            $this->db->where('bookingMasterID', $ioubookingmasterid);
            $this->db->where('submittedYN', 1);
            $this->db->from('srp_erp_ioubookingmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('error' => 2, 'message' => 'This Document Already Submitted!');
            } else {

                $data = array(
                    'submittedYN' => 1,
                    'submittedDate' => $this->common_data['current_date'],
                    'submittedEmpID' => $this->common_data['current_userID'],
                );

                $this->db->where('bookingMasterID', trim($this->input->post('IOUbookingmasterid') ?? ''));
                $result = $this->db->update('srp_erp_ioubookingmaster', $data);

                if ($result) {
                    return array('error' => 0, 'message' => 'document successfully Submitted');
                }

            }
        }
    }

    function iou_referback_booking_emp()
    {
        $bookingMasterID = $this->input->post('bookingMasterID');

        $this->db->select('approvedYN,documentID,bookingCode');
        $this->db->where('bookingMasterID', trim($bookingMasterID));
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_ioubookingmaster');
        $approved_iou_booking = $this->db->get()->row_array();
        if (!empty($approved_iou_booking)) {
            return array('e', 'The document already Confirmed - ' . $approved_iou_booking['bookingCode']);
        } else {

            $data = array(
                'submittedYN' => '',
                'submittedDate' => '',
                'submittedEmpID' => '',
            );

            $this->db->where('bookingMasterID', $bookingMasterID);
            $result = $this->db->update('srp_erp_ioubookingmaster', $data);

            if ($result) {
                return array('s', 'Referred Back Successfully');
            }
        }


    }

    function fetch_iou_employee_currency()
    {
        $empid = trim($this->input->post('empid') ?? '');
        $data = $this->db->query("SELECT payCurrencyID FROM srp_employeesdetails  WHERE EIdNo = $empid")->row_array();

        return $data;
    }

    function save_iou_user()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $userid = $this->input->post('userid');
        $company_code = $this->common_data['company_data']['company_code'];
        $data['currencyID'] = $this->input->post('transactionCurrencyID');
        $data['PhoneNo'] = $this->input->post('phonenumber');
        $data['Address'] = $this->input->post('address');
        $data['isActive'] = $this->input->post('active');
        $data['userName'] = trim($this->input->post('employeeName') ?? '');


        if ($userid) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('userID', $userid);
            $this->db->update('srp_erp_iouusers', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'User Update ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'User Updated Successfully.');
            }
        } else {
            $data['companyID'] = $companyID;
            $serial = $this->db->query("select IF ( isnull(MAX(serialNo)), 1, (MAX(serialNo) + 1) ) AS serialNo FROM `srp_erp_iouusers` WHERE companyID={$companyID}")->row_array();
            $data['companyID'] = $companyID;
            $data['serialNo'] = $serial['serialNo'];
            $data['userCode'] = ($company_code . '/' . 'IOUEMP' . str_pad($data['serialNo'], 6, '0', STR_PAD_LEFT));
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_iouusers', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'User Insertion Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'User Inserted Successfully.', $last_id);
            }
        }


    }

    function load_iou_header()
    {
        $userid = trim($this->input->post('userid') ?? '');
        $this->db->select('*');
        $this->db->where('userID', $userid);
        $this->db->from('srp_erp_iouusers');
        return $this->db->get()->row_array();
    }

    function delete_iou_master()
    {
        $userid = $this->input->post('userID');
        $this->db->select('empID');
        $this->db->where('empID', $userid);
        $this->db->where('userType', 2);
        $this->db->from('srp_erp_ioubookingmaster');
        $ioubookmaster = $this->db->get()->row_array();

        $userid = $this->input->post('userID');
        $this->db->select('empID');
        $this->db->where('empID', $userid);
        $this->db->where('userType', 2);
        $this->db->from('srp_erp_iouvouchers');
        $iouvouchermaster = $this->db->get()->row_array();

        if (!empty($ioubookmaster) || !empty($iouvouchermaster)) {
            return array('e', 'This user already pulled to some transactions');
        }

        $this->db->delete('srp_erp_iouusers', array('userID' => trim($this->input->post('userID') ?? '')));
        return array('s', 'IOU User deleted successfully.');

    }

    function fetch_iou_iouvoucher_details()
    {
        $voucherid = trim($this->input->post('iouvoucherid') ?? '');
        $bookingmasterid = trim($this->input->post('IOUbookingmasterid') ?? '');
        $comapnyid = current_companyID();

        $data = $this->db->query("select vouchermaster.transactionAmount as totalvoucheramount,IFNULL(bookingdetails.totalamt,0) as matchedamt,vouchermaster.transactionCurrencyDecimalPlaces from  srp_erp_iouvouchers vouchermaster LEFT JOIN (SELECT sum(transactionAmount) as totalamt,iouVoucherAutoID,bookingMasterID from srp_erp_ioubookingdetails where iouVoucherAutoID = {$voucherid} AND bookingMasterID !={$bookingmasterid} AND companyID = {$comapnyid}) bookingdetails on bookingdetails.iouVoucherAutoID = vouchermaster.voucherAutoID where  voucherAutoID = {$voucherid} ")->row_array();

        return $data;

    }

    function generatevoucher()
    {
        $iouvoucherid = trim($this->input->post('voucherAutoID') ?? '');
        $companyid = current_companyID();
        $approved = $this->db->query("select approvedYN from srp_erp_ioubookingmaster where iouVoucherAutoID = $iouvoucherid AND companyID = $companyid AND approvedYN !=1")->row_array();
        if (!empty($approved)) {
            return array('error' => 0, 'message' => 'Cannot generate a voucher some expences are not approved');

        } else {
            return array('error' => 1, 'message' => 'success');
        }

    }

    function save_iou_voucher_receipt_voucher()
    {
        $iouvoucherid = trim($this->input->post('voucherid') ?? '');
        $companyid = current_companyID();

        $iouvoucherdetails = $this->db->query("SELECT Vouchers.*,DATE_FORMAT( voucherDate, '%d-%m-%Y' ) AS voucherDateformated,segment.segmentCode FROM srp_erp_iouvouchers Vouchers
        LEFT JOIN srp_erp_segment segment ON Vouchers.segmentID = segment.segmentID  WHERE voucherAutoID = $iouvoucherid AND Vouchers.companyID = $companyid")->row_array();

        $amount = trim($this->input->post('balanceamt') ?? '');

        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $RVdates = $this->input->post('RVdate');
        $RVdate = input_format_date($RVdates, $date_format_policy);
        $RVcheqDate = $this->input->post('RVchequeDate');
        $RVchequeDate = input_format_date($RVcheqDate, $date_format_policy);
        $bank = explode('|', trim($this->input->post('bank') ?? ''));
        $bank_detail = fetch_gl_account_desc(trim($this->input->post('RVbankCode') ?? ''));

        $data['documentID'] = 'RV';
        $data['isSystemGenerated'] = 1;
        $data['companyFinanceYearID'] = $iouvoucherdetails['companyFinanceYearID'];
        $data['companyFinanceYear'] = $iouvoucherdetails['companyFinanceYear'];
        $data['FYBegin'] = $iouvoucherdetails['FYBegin'];
        $data['FYEnd'] = $iouvoucherdetails['FYEnd'];
        $data['companyFinancePeriodID'] = $iouvoucherdetails['companyFinancePeriodID'];

        $data['RVdate'] = trim($RVdate);
        $data['RVNarration'] = 'Closing of IOU Voucher - ' . $iouvoucherdetails['iouCode'] . '';
        $data['segmentID'] = $iouvoucherdetails['segmentID'];
        $data['segmentCode'] = $iouvoucherdetails['segmentCode'];
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
        $data['RvType'] = 'Direct';
        $data['referanceNo'] = $iouvoucherdetails['referenceNumber'];
        $data['RVbankCode'] = trim($this->input->post('RVbankCode') ?? '');
        $data['customerName'] = $iouvoucherdetails['empName'];
        $data['customerAddress'] = '';
        $data['customerTelephone'] = '';
        $data['customerFax'] = '';
        $data['customerEmail'] = '';
        $data['customerCurrency'] = $iouvoucherdetails['partyCurrency'];
        $data['customerCurrencyID'] = $iouvoucherdetails['partyCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $iouvoucherdetails['partyCurrencyDecimalPlaces'];
        $data['transactionCurrencyID'] = $iouvoucherdetails['transactionCurrencyID'];
        $data['transactionCurrency'] = $iouvoucherdetails['transactionCurrency'];
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

        $datass = array(
            'balanceVoucherType' => 1,
            'closedYN' => 1,
            'closedByEmpID' => current_userID(),
            'closedDate' => current_date(),
            'balanceVoucherAutoID' => $last_id,
            'balanceVoucherAmount' => $this->input->post('balanceamt'),
        );
        $this->db->where('voucherAutoID', $iouvoucherid);
        $this->db->update('srp_erp_iouvouchers', $datass);


        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerExchangeRate,transactionCurrencyID,segmentID,segmentCode');
        $this->db->where('receiptVoucherAutoId', $last_id);
        $master = $this->db->get('srp_erp_customerreceiptmaster')->row_array();
        $glautoid = $this->db->query("SELECT charts.GLAutoID as GLAutoID,charts.systemAccountCode as systemGLCode,charts.GLSecondaryCode as GLCode,charts.GLDescription as GLDescription,charts.subCategory as GLType FROM srp_erp_chartofaccounts charts INNER JOIN srp_erp_companycontrolaccounts controlac ON charts.GLAutoID = controlac.GLAutoID WHERE
        controlac.companyID = $companyid AND controlac.controlAccountType = 'IOU'")->row_array();
        $datas['receiptVoucherAutoId'] = $last_id;
        $datas['GLAutoID'] = $glautoid['GLAutoID'];
        $datas['systemGLCode'] = $glautoid['systemGLCode'];
        $datas['GLCode'] = $glautoid['GLCode'];
        $datas['GLDescription'] = $glautoid['GLDescription'];
        $datas['GLType'] = $glautoid['GLType'];
        $datas['segmentID'] = $master['segmentID'];
        $datas['segmentCode'] = $master['segmentCode'];
        $datas['transactionAmount'] = trim($amount);
        $datas['companyLocalAmount'] = ($datas['transactionAmount'] / $master['companyLocalExchangeRate']);
        $datas['companyReportingAmount'] = ($datas['transactionAmount'] / $master['companyReportingExchangeRate']);
        $datas['customerAmount'] = 0;
        if ($master['customerExchangeRate']) {
            $datas['customerAmount'] = ($datas['transactionAmount'] / $master['customerExchangeRate']);
        }
        $datas['description'] = 'Closing of IOU Voucher - ' . $iouvoucherdetails['iouCode'] . '';
        $datas['type'] = 'GL';
        $datas['companyCode'] = $this->common_data['company_data']['company_code'];
        $datas['companyID'] = $this->common_data['company_data']['company_id'];
        $datas['createdUserGroup'] = $this->common_data['user_group'];
        $datas['createdPCID'] = $this->common_data['current_pc'];
        $datas['createdUserID'] = $this->common_data['current_userID'];
        $datas['createdUserName'] = $this->common_data['current_user'];
        $datas['createdDateTime'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_customerreceiptdetail', $datas);
        $this->receipt_confirmation($last_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Receipt Voucher   Saved Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Receipt Voucher Saved Successfully', $last_id);
        }
    }

    function receipt_confirmation($last_id)
    {
        $this->load->library('approvals');

        $this->db->select('receiptVoucherAutoId');
        $this->db->where('receiptVoucherAutoId', $last_id);
        $this->db->from('srp_erp_customerreceiptdetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('error' => 2, 'message' => 'There are no records to confirm this document!');
        } else {
            $rvid = $last_id;
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
                $this->db->select('documentID, RVcode,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', $last_id);
                $this->db->from('srp_erp_customerreceiptmaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                if ($master_dt['RVcode'] == "0") {
                    $rvcd = array(
                        'RVcode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                    );
                    $this->db->where('receiptVoucherAutoId', $last_id);
                    $this->db->update('srp_erp_customerreceiptmaster', $rvcd);
                }

                $this->db->select('documentID,receiptVoucherAutoId, RVcode,RVdate,DATE_FORMAT(RVdate, "%Y") as invYear,DATE_FORMAT(RVdate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('receiptVoucherAutoId', $last_id);
                $this->db->from('srp_erp_customerreceiptmaster');
                $app_data = $this->db->get()->row_array();

                $sql = "SELECT (srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,(srp_erp_warehouseitems.currentStock-(srp_erp_customerreceiptdetail.requestedQty / srp_erp_customerreceiptdetail.conversionRateUOM)) as stock ,srp_erp_warehouseitems.itemAutoID as itemAutoID ,srp_erp_customerreceiptdetail.wareHouseAutoID FROM srp_erp_customerreceiptdetail INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerreceiptdetail.itemAutoID AND srp_erp_customerreceiptdetail.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID where receiptVoucherAutoId = '{$last_id}' AND itemCategory != 'Service' Having stock < 0";
                $item_low_qty = $this->db->query($sql)->result_array();
                if (!empty($item_low_qty)) {
                    //$this->session->set_flashdata('w', 'Some Item quantities are not sufficient to confirm this transaction');
                    return array('error' => 1, 'message' => 'Some Item quantities are not sufficient to confirm this transaction', 'itemAutoID' => $item_low_qty);
                }
                
                $auto_approval = get_document_auto_approval('RV');
             
                if($auto_approval == 0){
                    $approvals_status = $this->approvals->auto_approve($app_data['receiptVoucherAutoId'], 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId', 'RV', $app_data['RVcode'], $app_data['RVdate']);
                    
                    // update for auto approval general ledger
                    $this->load->model('Receipt_voucher_model');
                    $this->load->helpers('receivable');

                    $res = $this->Receipt_voucher_model->save_rv_approval(0,$app_data['receiptVoucherAutoId'],1,'Auto Approved');
                
                
                } else {
                    $approvals_status = $this->approvals->CreateApproval('RV', $app_data['receiptVoucherAutoId'], $app_data['RVcode'], 'Receipt Voucher', 'srp_erp_customerreceiptmaster', 'receiptVoucherAutoId');
                }

                if ($approvals_status == 1) {

                    /** item Master Sub check */
                    $documentID = $last_id;
                    $validate = $this->validate_itemMasterSub($documentID);

                    /** end of item master sub */
                    if ($validate) {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('receiptVoucherAutoId', $last_id);
                        $this->db->update('srp_erp_customerreceiptmaster', $data);
                        //return array('status' => true, 'data' => 'Document Confirmed Successfully!');
                        return array('error' => 0, 'message' => 'Document Confirmed Successfully!');
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

    function save_iou_voucher_payment_voucher()
    {
        $voucherid = trim($this->input->post('voucheridpayment') ?? '');
        $balanceamount = trim($this->input->post('balanceamtpaymentvoucher') ?? '');
        $comapnyid = current_companyID();

        $iouvouchermaster = $this->db->query("SELECT vouchers.*,segment.segmentCode FROM srp_erp_iouvouchers vouchers LEFT JOIN srp_erp_segment segment on segment.segmentID = vouchers.segmentID WHERE voucherAutoID = $voucherid AND vouchers.companyID = $comapnyid")->row_array();

        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->input->post('PVdate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->input->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $accountPayeeOnly = 0;
        if (!empty($this->input->post('accountPayeeOnly'))) {
            $accountPayeeOnly = 1;
        }

        $data['PVbankCode'] = trim($this->input->post('PVbankCode') ?? '');
        $data['isSytemGenerated'] = 1;
        $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
        $data['documentID'] = 'PV';
        $data['companyFinanceYearID'] = $iouvouchermaster['companyFinanceYearID'];
        $data['companyFinanceYear'] = $iouvouchermaster['companyFinanceYear'];
        $data['FYBegin'] = $iouvouchermaster['FYBegin'];
        $data['FYEnd'] = $iouvouchermaster['FYEnd'];
        $data['companyFinancePeriodID'] = $iouvouchermaster['companyFinancePeriodID'];
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['PVdate'] = trim($PVdate);
        $data['PVNarration'] = 'Closing of IOU Voucher - ' . $iouvouchermaster['iouCode'] . '';
        $data['accountPayeeOnly'] = $accountPayeeOnly;
        $data['segmentID'] = $iouvouchermaster['segmentID'];
        $data['segmentCode'] = $iouvouchermaster['segmentCode'];
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
        if ($bank_detail['isCash'] == 1) {
            $data['PVchequeNo'] = null;
            $data['PVchequeDate'] = null;
        } else {
            if ($this->input->post('paymentType') == 2 && $this->input->post('vouchertype') == 'Supplier') {
                $data['PVchequeNo'] = null;
                $data['PVchequeDate'] = null;
            } else {
                $data['PVchequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                $data['PVchequeDate'] = trim($PVchequeDate);
            }
        }
        $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);

        if ($iouvouchermaster['userType'] == 2) {
            $data['pvType'] = 'Direct';
        } else {
            $data['pvType'] = 'Employee';
        }
        $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
        $data['referenceNo'] = $iouvouchermaster['referenceNumber'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $iouvouchermaster['transactionCurrencyID'];
        $data['transactionCurrency'] = $iouvouchermaster['transactionCurrency'];
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
        if ($data['pvType'] == 'Direct') {
            $data['partyType'] = 'DIR';
            $data['partyName'] = $iouvouchermaster['empName'];
            $data['partyCurrencyID'] = $iouvouchermaster['partyCurrencyID'];
            $data['partyCurrency'] = $iouvouchermaster['partyCurrency'];
            $data['partyExchangeRate'] = $iouvouchermaster['partyExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $iouvouchermaster['partyCurrencyDecimalPlaces'];
        } elseif ($data['pvType'] == 'Employee') {
            $emp_arr = $this->fetch_empyoyee($iouvouchermaster['empID']);
            $data['partyType'] = 'EMP';
            $data['partyID'] = $iouvouchermaster['empID'];
            $data['partyCode'] = $emp_arr['ECode'];
            $data['partyName'] = $emp_arr['Ename2'];
            $data['partyAddress'] = $emp_arr['EcAddress1'] . ' ' . $emp_arr['EcAddress2'] . ' ' . $emp_arr['EcAddress3'];
            $data['partyTelephone'] = $emp_arr['EpTelephone'];
            $data['partyFax'] = $emp_arr['EpFax'];
            $data['partyEmail'] = $emp_arr['EEmail'];
            $data['partyGLAutoID'] = '';
            $data['partyGLCode'] = '';
            $data['partyCurrencyID'] = $iouvouchermaster['partyCurrencyID'];
            $data['partyCurrency'] = $iouvouchermaster['partyCurrency'];
            $data['partyExchangeRate'] = $iouvouchermaster['partyExchangeRate'];
            $data['partyCurrencyDecimalPlaces'] = $iouvouchermaster['partyCurrencyDecimalPlaces'];
        }
        $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
        $data['partyExchangeRate'] = $partyCurrency['conversion'];
        $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
        $this->db->where('GLAutoID', $data['bankGLAutoID']);
        $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));
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

        $this->db->select('transactionCurrencyID,transactionCurrency,segmentID,segmentCode,transactionExchangeRate, companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrencyID,companyReportingCurrency ,companyReportingExchangeRate ,partyCurrency,partyExchangeRate');
        $this->db->where('payVoucherAutoId', $last_id);
        $master_recode = $this->db->get('srp_erp_paymentvouchermaster')->row_array();

        $glautoid = $this->db->query("SELECT charts.GLAutoID as GLAutoID,charts.systemAccountCode as systemGLCode,charts.GLSecondaryCode as GLCode,charts.GLDescription as GLDescription,charts.subCategory as GLType FROM srp_erp_chartofaccounts charts INNER JOIN srp_erp_companycontrolaccounts controlac ON charts.GLAutoID = controlac.GLAutoID WHERE
controlac.companyID = $comapnyid AND controlac.controlAccountType = 'IOU'")->row_array();


        $datass['payVoucherAutoId'] = $last_id;
        $datass['GLAutoID'] = $glautoid['GLAutoID'];
        $datass['systemGLCode'] = $glautoid['systemGLCode'];
        $datass['GLCode'] = $glautoid['GLCode'];
        $datass['GLDescription'] = $glautoid['GLDescription'];
        $datass['GLType'] = $glautoid['GLType'];
        $datass['segmentID'] = $master_recode['segmentID'];
        $datass['segmentCode'] = $master_recode['segmentCode'];
        $datass['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
        $datass['transactionCurrency'] = $master_recode['transactionCurrency'];
        $datass['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $datass['transactionAmount'] = $balanceamount * -1;
        $datass['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        $datass['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $datass['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $datass['companyLocalAmount'] = ($datass['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $datass['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        $datass['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $datass['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        $datass['companyReportingAmount'] = ($datass['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $datass['partyCurrency'] = $master_recode['partyCurrency'];
        $datass['partyExchangeRate'] = $master_recode['partyExchangeRate'];
        $datass['partyAmount'] = ($datass['transactionAmount'] / $master_recode['partyExchangeRate']);
        $datass['description'] = 'Closing of IOU Voucher - ' . $iouvouchermaster['iouCode'] . '';
        $datass['type'] = 'GL';
        $datass['modifiedPCID'] = $this->common_data['current_pc'];
        $datass['modifiedUserID'] = $this->common_data['current_userID'];
        $datass['modifiedUserName'] = $this->common_data['current_user'];
        $datass['modifiedDateTime'] = $this->common_data['current_date'];
        $datass['companyCode'] = $this->common_data['company_data']['company_code'];
        $datass['companyID'] = $this->common_data['company_data']['company_id'];
        $datass['createdUserGroup'] = $this->common_data['user_group'];
        $datass['createdPCID'] = $this->common_data['current_pc'];
        $datass['createdUserID'] = $this->common_data['current_userID'];
        $datass['createdUserName'] = $this->common_data['current_user'];
        $datass['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_paymentvoucherdetail', $datass);
        $datasupdate = array(
            'balanceVoucherType' => 2,
            'closedYN' => 1,
            'closedByEmpID' => current_userID(),
            'closedDate' => current_date(),
            'balanceVoucherAutoID' => $last_id,
            'balanceVoucherAmount' => $balanceamount * -1,
        );
        $this->db->where('voucherAutoID', $voucherid);
        $this->db->update('srp_erp_iouvouchers', $datasupdate);

        $this->payment_voucher_confirmation_iou_approval($last_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Payment Voucher Detail :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Payment Voucher Detail :  Saved Successfully.');
        }

    }

    function payment_voucher_confirmation_iou_approval($PayVoucherAutoIdmaster)//payment voucher confirmation when a iou Voucher approved
    {
        $this->db->select('payVoucherDetailAutoID');
        $this->db->where('payVoucherAutoId', $PayVoucherAutoIdmaster);
        $this->db->from('srp_erp_paymentvoucherdetail');
        $results = $this->db->get()->result_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $pvid = $PayVoucherAutoIdmaster;
            $taxamnt = 0;
            $GL = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='GL'
GROUP BY payVoucherAutoId")->row_array();

            if (empty($GL)) {
                $GL = 0;
            } else {
                $GL = $GL['transactionAmount'];
            }
            $Item = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Item'
GROUP BY payVoucherAutoId")->row_array();
            if (empty($Item)) {
                $Item = 0;
            } else {
                $Item = $Item['transactionAmount'];
            }
            $debitnote = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='debitnote'
GROUP BY payVoucherAutoId")->row_array();
            if (empty($debitnote)) {
                $debitnote = 0;
            } else {
                $debitnote = $debitnote['transactionAmount'];
            }
            $Advance = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Advance'
GROUP BY payVoucherAutoId")->row_array();
            if (empty($Advance)) {
                $Advance = 0;
            } else {
                $Advance = $Advance['transactionAmount'];
            }
            $Invoice = $this->db->query("SELECT
	SUM(transactionAmount) as transactionAmount
FROM
	srp_erp_paymentvoucherdetail
WHERE
	payVoucherAutoId = $pvid
	AND type='Invoice'
GROUP BY payVoucherAutoId")->row_array();
            if (empty($Invoice)) {
                $Invoice = 0;
            } else {
                $Invoice = $Invoice['transactionAmount'];
            }
            $tax = $this->db->query("SELECT
	SUM(taxPercentage) as taxPercentage
FROM
	srp_erp_paymentvouchertaxdetails
WHERE
	payVoucherAutoId = $pvid
GROUP BY payVoucherAutoId")->row_array();
            if (empty($tax)) {
                $tax = 0;
            } else {
                $tax = $tax['taxPercentage'];
                $taxamnt = (($Item + $GL) / 100) * $tax;
            }
            $totalamnt = ($Item + $GL + $Invoice + $Advance + $taxamnt) - $debitnote;
            if ($totalamnt < 0) {
                //    return array('w', 'Grand total should be greater than 0.');
            } else {
                $this->db->select('PayVoucherAutoId');
                $this->db->where('PayVoucherAutoId', $PayVoucherAutoIdmaster);
                $this->db->where('confirmedYN', 1);
                $this->db->from('srp_erp_paymentvouchermaster');
                $Confirmed = $this->db->get()->row_array();
                if (!empty($Confirmed)) {
                    //  return array('w', 'Document already confirmed');
                } else {


                    $PayVoucherAutoId = $PayVoucherAutoIdmaster;
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
                        $this->db->select('documentID, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $master_dt = $this->db->get()->row_array();
                        $this->load->library('sequence');
                        if ($master_dt['PVcode'] == "0") {
                            $pvCd = array(
                                'PVcode' => $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth'])
                            );
                            $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                            $this->db->update('srp_erp_paymentvouchermaster', $pvCd);
                        }
                        $this->load->library('approvals');
                        $this->db->select('documentID,PayVoucherAutoId, PVcode,DATE_FORMAT(PVdate, "%Y") as invYear,DATE_FORMAT(PVdate, "%m") as invMonth,companyFinanceYearID');
                        $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $app_data = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('PV', $app_data['PayVoucherAutoId'], $app_data['PVcode'], 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId');
                        if ($approvals_status == 1) {
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user']
                            );
                            $this->db->where('PayVoucherAutoId', $PayVoucherAutoId);
                            $this->db->update('srp_erp_paymentvouchermaster', $data);

                            ///  $this->save_pv_approval_iou_voucher($PayVoucherAutoId);
                            //  return array('s','Document confirmed successfully.');

                        } else if ($approvals_status == 3) {
                            //return array('w', 'There are no users exist to perform approval for this document.');
                        } else {
                            //  return array('e', 'oops, something went wrong!');
                        }
                    } else {
                        // return array('e', 'Please complete you sub item configuration, fill all the mandatory fields!');
                    }
                }
            }

        }
    }

    function close_iou_voucher()
    {

        $voucherid = trim($this->input->post('voucherid') ?? '');
        $comapnyid = current_companyID();

        $detailexist = $this->db->query("select bookingMasterID from srp_erp_ioubookingmaster where iouVoucherAutoID = $voucherid AND companyID = $comapnyid AND approvedYN != 1")->row_array();
        if (!empty($detailexist)) {
            return array('e', 'Voucher Already pulled to transactions.');
        } else {
            $datasupdate = array(
                'closedYN' => 1,
                'closedByEmpID' => current_userID(),
                'closedDate' => current_date(),
            );
            $this->db->where('voucherAutoID', $voucherid);
            $this->db->update('srp_erp_iouvouchers', $datasupdate);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Voucher Closed  Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Voucher Closed Successfully.');
            }
        }


    }
    function fetch_iou_closed_details()
    {
        $voucherid = $this->input->post('voucherid');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("select vouchers.*,employee.Ename2 as employeenameclosed,  DATE_FORMAT(voucherDate,\"" . $convertFormat . "\") AS voucherDate, DATE_FORMAT(closedDate,\"" . $convertFormat . "\") AS closedDate,CASE WHEN vouchers.balanceVoucherType = \"1\" THEN \"Receipt Voucher\" WHEN vouchers.balanceVoucherType = \"2\" THEN \"Payment Voucher\" ELSE \"-\" END	Vouchertype from srp_erp_iouvouchers vouchers LEFT JOIN srp_employeesdetails employee on employee.EIdNo = vouchers.closedByEmpID where  voucherAutoID = $voucherid And companyID = $companyid ")->row_array();
        return $data;
    }

    function fetch_emp_segment_id($EIdNo){

        $companyid = current_companyID();
        
        $data = $this->db->query("SELECT segment.segmentID,segment.segmentCode 
        FROM srp_employeesdetails
        LEFT JOIN srp_erp_segment as segment ON  srp_employeesdetails.segmentID = segment.segmentID
        WHERE EIdNo = {$EIdNo} and Erp_companyID = {$companyid} and isActive= 1 ")->row_array();
        
        return $data;

    }


}