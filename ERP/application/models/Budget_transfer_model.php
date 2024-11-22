<?php

class Budget_transfer_model extends ERP_Model
{

    function save_budget_transfer_detail(){

        $this->db->select('*');
        $this->db->where('budgetTransferAutoID', $this->input->post('budgetTransferAutoID'));
        $master = $this->db->get('srp_erp_budgettransfer')->row_array();

        $default_currency = currency_conversionID($master['transactionCurrencyID'], $master['companyLocalCurrencyID']);
        $companyLocalExchangeRate = $default_currency['conversion'];

        $reporting_currency = currency_conversionID($master['transactionCurrencyID'], $master['companyReportingCurrencyID']);
        $companyReportingExchangeRate = $reporting_currency['conversion'];

        $togldec=fetch_gl_account_desc($this->input->post('toGLAutoID'));
        $fromgldec=fetch_gl_account_desc($this->input->post('FromGLAutoID'));

        $data['budgetTransferAutoID'] = $this->input->post('budgetTransferAutoID');
        $data['fromSegmentID'] = $this->input->post('fromSegmentID');
        $data['FromGLAutoID'] = $this->input->post('FromGLAutoID');
        $data['FromGLCodeDescription'] = $fromgldec['GLDescription'];
        $data['toGLCodeDescription'] = $togldec['GLDescription'];
        $data['toSegmentID'] = $this->input->post('toSegmentID');
        $data['toGLAutoID'] = $this->input->post('toGLAutoID');
        $data['transferAmount'] = $this->input->post('adjustmentAmount');
        $data['budgetAmount'] = $this->input->post('budgetAmount');
        $data['transferAmountLocal'] = $this->input->post('adjustmentAmount')/$master['companyLocalExchangeRate'];
        $data['transferAmountRpt'] = $this->input->post('adjustmentAmount')/$master['companyReportingExchangeRate'];
        $data['remarks'] = $master['comments'];
        $data['financeYearID'] = $master['financeYearID'];

        $result=$this->db->insert('srp_erp_budgettransferdetail', $data);
        $last_id = $this->db->insert_id();

        if($result){
            return array('s','Successfully Saved');
        }else{
            return array('e','Save Failed');
        }
    }

    function confirm_budget_transfer(){
        $this->db->select('*');
        $this->db->where('budgetTransferAutoID', trim($this->input->post('budgetTransferAutoID') ?? ''));
        $this->db->from('srp_erp_budgettransferdetail');
        $detail = $this->db->get()->row_array();

        if($detail){
            $system_code=$this->input->post('budgetTransferAutoID');
            $this->load->library('Approvals');
            $this->db->select('budgetTransferAutoID, documentSystemCode,documentDate');
            $this->db->where('budgetTransferAutoID', $system_code);
            $this->db->from('srp_erp_budgettransfer');
            $bdt_data = $this->db->get()->row_array();

            $validate_code = validate_code_duplication($bdt_data['documentSystemCode'], 'documentSystemCode', $system_code,'budgetTransferAutoID', 'srp_erp_budgettransfer');
            if(!empty($validate_code)) {
                $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                return array(false, 'error');
            }

            $autoApproval= get_document_auto_approval('BDT');
            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($bdt_data['budgetTransferAutoID'], 'srp_erp_budgettransfer','budgetTransferAutoID', 'BDT',$bdt_data['documentSystemCode'],$bdt_data['documentDate']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval('BDT', $bdt_data['budgetTransferAutoID'], $bdt_data['documentSystemCode'], 'Budget Transfer', 'srp_erp_budgettransfer', 'budgetTransferAutoID',0,$bdt_data['documentDate']);
            }else{
                return array('e','Approval levels are not set for this document.');
            }

            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user'],
            );
            $this->db->where('budgetTransferAutoID', trim($this->input->post('budgetTransferAutoID') ?? ''));
            $this->db->update('srp_erp_budgettransfer', $data);

            return array('s','Document Confirmed Successfully');
        }else{
            return array('e','No detail records found to confirm this document');
        }
    }

    function fetch_template_data($budgetTransferAutoID){
        $convertFormat = convert_date_format_sql();
        $this->db->select('budgetTransferAutoID,documentID,documentSystemCode, DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS createdDate,srp_erp_budgettransfer.comments as comments,confirmedYN,confirmedByEmpID,confirmedByName,approvedYN,approvedbyEmpName,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_erp_currencymaster.CurrencyCode as CurrencyCode,srp_erp_currencymaster.DecimalPlaces as DecimalPlaces,CONCAT(srp_erp_companyfinanceyear.beginingDate,\' - \',srp_erp_companyfinanceyear.endingDate) AS financeYear');
        $this->db->where('budgetTransferAutoID', $budgetTransferAutoID);
        $this->db->from('srp_erp_budgettransfer');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_budgettransfer.transactionCurrencyID');
        $this->db->join('srp_erp_companyfinanceyear ', 'srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID');
        $data['master'] = $this->db->get()->row_array();

        $companyID = $this->common_data['company_data']['company_id'];
        $data['detail'] = $this->db->query('SELECT
	`budgetTransferDetailAutoID`,
	`budgetTransferAutoID`,
	CONCAT(
		fsg.segmentCode,
		" - " ,
		fsg.description
	) AS fsegment,
	CONCAT(
		tsg.segmentCode,
		" - ",
		tsg.description
	) AS tsegment,
	CONCAT(
		fgl.systemAccountCode,
		" - ",
		fgl.GLDescription
	) AS fGLC,
	CONCAT(
		tgl.systemAccountCode,
		" - ",
		tgl.GLDescription
	) AS tGLC,
	`transferAmount`
FROM
	`srp_erp_budgettransferdetail`
JOIN `srp_erp_segment` `fsg` ON `fsg`.`segmentID` = `srp_erp_budgettransferdetail`.`fromSegmentID`
JOIN `srp_erp_segment` `tsg` ON `tsg`.`segmentID` = `srp_erp_budgettransferdetail`.`toSegmentID`
JOIN `srp_erp_chartofaccounts` `fgl` ON `fgl`.`GLAutoID` = `srp_erp_budgettransferdetail`.`FromGLAutoID`
JOIN `srp_erp_chartofaccounts` `tgl` ON `tgl`.`GLAutoID` = `srp_erp_budgettransferdetail`.`toGLAutoID`
WHERE
	`srp_erp_budgettransferdetail`.`budgetTransferAutoID` = '.$budgetTransferAutoID.' ')->result_array();

    return $data;
    }


    function save_budget_transfer_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->load->library('Approvals');

        if ($autoappLevel == 1) {
            $system_code = trim($this->input->post('budgetTransferAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['budgetTransferAutoID'] = $system_code;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        $company_id = $this->common_data['company_data']['company_id'];
        $transaction_tot = 0;
        $company_rpt_tot = 0;
        $supplier_cr_tot = 0;
        $company_loc_tot = 0;

        $maxLevel = $this->approvals->maxlevel('BDT');

        $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'BDT');
        }
        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('budgetTransferAutoID', $system_code);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_budgettransfer');
            $master = $this->db->get()->row_array();

            $companyFinanceYearID=$master['financeYearID'];
            $companyID=current_companyID();

            $this->db->select('*');
            $this->db->where('budgetTransferAutoID', $system_code);
            $this->db->from('srp_erp_budgettransferdetail');
            $bdtdetails = $this->db->get()->result_array();

            $periods = $this->db->query('SELECT
	companyFinancePeriodID,
	MONTH(dateFrom) as months,
	YEAR(dateFrom) as years
FROM
	`srp_erp_companyfinanceperiod`
WHERE companyFinanceYearID='.$companyFinanceYearID.'
AND companyID='.$companyID.'
AND isClosed!=1 ')->result_array();

            if(!empty($periods)){

                $monthcount=count($periods);
                $data['budgetType'] = 2;
                $data['documentSystemCode'] = $master['documentSystemCode'];
                $data['narration'] = $master['comments'];
                $data['documentDate'] = current_date(false);
                $data['companyID'] = current_companyID();
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['transactionCurrencyID'] = $master['transactionCurrencyID'];
                $transcurr=$this->get_currency_details($master['transactionCurrencyID']);
                $data['transactionCurrency'] = $transcurr['CurrencyCode'];
                $data['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $data['transactionCurrencyDecimalPlaces'] = $transcurr['DecimalPlaces'];
                $data['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                $localcurr=$this->get_currency_details($master['companyLocalCurrencyID']);
                $data['companyLocalCurrency'] = $localcurr['CurrencyCode'];
                $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data['companyLocalCurrencyDecimalPlaces'] = $localcurr['DecimalPlaces'];
                $data['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                $repocurr=$this->get_currency_details($master['companyReportingCurrencyID']);
                $data['companyReportingCurrency'] = $repocurr['CurrencyCode'];
                $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data['companyReportingCurrencyDecimalPlaces'] = $repocurr['DecimalPlaces'];
                $data['companyFinanceYearID'] = $master['financeYearID'];
                $financeyr=get_financial_from_to($master['financeYearID']);
                $data['companyFinanceYear'] = $financeyr['beginingDate'].'-'.$financeyr['endingDate'];
                $data['FYBegin'] = $financeyr['beginingDate'];
                $data['FYEnd'] = $financeyr['endingDate'];
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = current_userID();
                $data['confirmedByName'] = current_user();
                $data['confirmedDate'] = current_date();
                $data['approvedYN'] = 1;
                $data['approvedbyEmpID'] = current_userID();
                $data['approvedbyEmpName'] = current_user();
                $data['approvedDate'] = current_date();
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $result=$this->db->insert('srp_erp_budgetmaster', $data);
                $last_id = $this->db->insert_id();

                if($result){
                    foreach($bdtdetails as $transdetl){
                        foreach($periods as $value){
                            $dataD['budgetAutoID'] = $last_id;
                            $dataD['companyID'] = current_companyID();
                            $dataD['companyCode'] = $this->common_data['company_data']['company_code'];
                            $dataD['segmentID'] = $transdetl['fromSegmentID'];
                            $segment=$this->get_segment_details($transdetl['fromSegmentID']);
                            $dataD['segmentCode'] = $segment['segmentCode'];
                            $glcode=fetch_gl_account_desc($transdetl['FromGLAutoID']);
                            $dataD['accountCategoryID'] = $glcode['accountCategoryTypeID'];
                            $dataD['accountCategoryDesc'] = $glcode['CategoryTypeDescription'];
                            $dataD['masterGLAutoID'] = $glcode['masterAutoID'];
                            $dataD['masterAccount'] = $glcode['masterAccount'];
                            $dataD['GLAutoID'] = $transdetl['FromGLAutoID'];
                            $dataD['GLDescription'] = $glcode['GLDescription'];
                            $dataD['budgetMonth'] = $value['months'];
                            $dataD['budgetYear'] = $value['years'];
                            $dataD['companyFinancePeriodID'] = $value['companyFinancePeriodID'];
                            $fyperod=fetchFinancePeriod($value['companyFinancePeriodID']);
                            $dataD['FYPeriodDateFrom'] = $fyperod['dateFrom'];
                            $dataD['FYPeriodDateTo'] = $fyperod['dateTo'];
                            $dataD['transactionCurrencyID'] = $master['companyReportingCurrencyID'];
                            $transcurr=$this->get_currency_details($master['companyReportingCurrencyID']);
                            $dataD['transactionCurrency'] = $transcurr['CurrencyCode'];
                            $dataD['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $dataD['transactionCurrencyDecimalPlaces'] = $transcurr['DecimalPlaces'];
                            $dataD['transactionAmount'] = ($transdetl['transferAmount']/$monthcount);
                            $dataD['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $localcurr=$this->get_currency_details($master['companyLocalCurrencyID']);
                            $dataD['companyLocalCurrency'] = $localcurr['CurrencyCode'];
                            $dataD['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $dataD['companyLocalCurrencyDecimalPlaces'] = $localcurr['DecimalPlaces'];
                            $dataD['companyLocalAmount'] = ($transdetl['transferAmountLocal']/$monthcount);
                            $dataD['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $repocurr=$this->get_currency_details($master['companyReportingCurrencyID']);
                            $dataD['companyReportingCurrency'] = $repocurr['CurrencyCode'];
                            $dataD['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $dataD['companyReportingCurrencyDecimalPlaces'] = $repocurr['DecimalPlaces'];
                            $dataD['companyReportingAmount'] = ($transdetl['transferAmountRpt']/$monthcount);
                            $dataD['createdUserGroup'] = $this->common_data['user_group'];
                            $dataD['createdPCID'] = $this->common_data['current_pc'];
                            $dataD['createdUserID'] = $this->common_data['current_userID'];
                            $dataD['createdUserName'] = $this->common_data['current_user'];
                            $dataD['createdDateTime'] = $this->common_data['current_date'];

                            $resultdtlF=$this->db->insert('srp_erp_budgetdetail', $dataD);


                            $dataDT['budgetAutoID'] = $last_id;
                            $dataDT['companyID'] = current_companyID();
                            $dataDT['companyCode'] = $this->common_data['company_data']['company_code'];
                            $dataDT['segmentID'] = $transdetl['toSegmentID'];
                            $segment=$this->get_segment_details($transdetl['toSegmentID']);
                            $dataDT['segmentCode'] = $segment['segmentCode'];
                            $glcode=fetch_gl_account_desc($transdetl['toGLAutoID']);
                            $dataDT['accountCategoryID'] = $glcode['accountCategoryTypeID'];
                            $dataDT['accountCategoryDesc'] = $glcode['CategoryTypeDescription'];
                            $dataDT['masterGLAutoID'] = $glcode['masterAutoID'];
                            $dataDT['masterAccount'] = $glcode['masterAccount'];
                            $dataDT['GLAutoID'] = $transdetl['toGLAutoID'];
                            $dataDT['GLDescription'] = $glcode['GLDescription'];
                            $dataDT['budgetMonth'] = $value['months'];
                            $dataDT['budgetYear'] = $value['years'];
                            $dataDT['companyFinancePeriodID'] = $value['companyFinancePeriodID'];
                            $fyperod=fetchFinancePeriod($value['companyFinancePeriodID']);
                            $dataDT['FYPeriodDateFrom'] = $fyperod['dateFrom'];
                            $dataDT['FYPeriodDateTo'] = $fyperod['dateTo'];
                            $dataDT['transactionCurrencyID'] = $master['companyReportingCurrencyID'];
                            $transcurr=$this->get_currency_details($master['companyReportingCurrencyID']);
                            $dataDT['transactionCurrency'] = $transcurr['CurrencyCode'];
                            $dataDT['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $dataDT['transactionCurrencyDecimalPlaces'] = $transcurr['DecimalPlaces'];
                            $dataDT['transactionAmount'] = ($transdetl['transferAmount']/$monthcount)*-1;
                            $dataDT['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $localcurr=$this->get_currency_details($master['companyLocalCurrencyID']);
                            $dataDT['companyLocalCurrency'] = $localcurr['CurrencyCode'];
                            $dataDT['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $dataDT['companyLocalCurrencyDecimalPlaces'] = $localcurr['DecimalPlaces'];
                            $dataDT['companyLocalAmount'] = ($transdetl['transferAmountLocal']/$monthcount)*-1;
                            $dataDT['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $repocurr=$this->get_currency_details($master['companyReportingCurrencyID']);
                            $dataDT['companyReportingCurrency'] = $repocurr['CurrencyCode'];
                            $dataDT['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $dataDT['companyReportingCurrencyDecimalPlaces'] = $repocurr['DecimalPlaces'];
                            $dataDT['companyReportingAmount'] = ($transdetl['transferAmountRpt']/$monthcount)*-1;
                            $dataDT['createdUserGroup'] = $this->common_data['user_group'];
                            $dataDT['createdPCID'] = $this->common_data['current_pc'];
                            $dataDT['createdUserID'] = $this->common_data['current_userID'];
                            $dataDT['createdUserName'] = $this->common_data['current_user'];
                            $dataDT['createdDateTime'] = $this->common_data['current_date'];

                            $resultdtlF=$this->db->insert('srp_erp_budgetdetail', $dataDT);
                        }
                    }
                }

                if($result){
                    $this->session->set_flashdata('s', 'Approved successfully');
                    return true;
                }else{
                    $this->session->set_flashdata('s', 'Approval failed');
                    return false;
                }

            }else{
                $this->session->set_flashdata('w', 'All Finance periods are closed');
                return false;
            }
        }

    }

    function get_currency_details($code){
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_currencymaster');
        $this->db->WHERE('currencyID', $code);

        return $this->db->get()->row_array();
    }
    function get_segment_details($code){
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_segment');
        $this->db->WHERE('segmentID', $code);

        return $this->db->get()->row_array();
    }



}