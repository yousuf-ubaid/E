<?php
/*
-- =============================================
-- File Name : Finance_dashboard.php
-- Project Name : SME ERP
-- Module Name : Dashboard - Finance
-- Create date : 15 - October 2016
-- Description : This file contains all the generation of finance dashboard.

-- REVISION HISTORY
-- =============================================*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_dashboard extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Finance_dashboard_model");
    }

    Function loadDashboard()
    {
        $data = array();
        $result = $this->Finance_dashboard_model->getAssignedDashboard();
        $data["dashboardTab"] = $result["dashboard"];
        $this->load->view('system/system_dashboard', $data);
    }

    function load_template()
    {
        $templatepage = $this->input->post("pageName");
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/' . $templatepage, $data);
    }


    Function load_overall_performance()
    {
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        $company_type = $this->session->userdata("companyType");
        if($company_type==1) {
            $lastTwoYears = get_last_two_financial_year();
        }else
        {
            $lastTwoYears = get_last_two_financial_year_group();
        }
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["months"] = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'M');
        $data["months2"] = get_month_list_from_date($beginingDate, $endDate, "Y-m", "1 month", 'My');
        $data["totalRevenue"] = $this->Finance_dashboard_model->getTotalRevenue($beginingDate, $endDate);
        $data["netProfit"] = $this->Finance_dashboard_model->getNetProfit($beginingDate, $endDate);
        $data["overallPerformance"] = $this->Finance_dashboard_model->getOverallPerformance($beginingDate, $endDate, $data["months"]);
        $this->load->view('system/erp_ajax_load_overall_performance', $data);
    }

    function load_financial_position()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_ajax_load_financial_position', $data);
    }

    function load_appraisal_completion()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_appraisal_completion_widget', $data);
    }

    function load_appraisal_allocation()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_appraisal_allocation_widget', $data);
    }

    function load_appraisal_calendar()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/appraisal_task_calendar_widget', $data);
    }

    function load_kpi_indicator(){
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/kpi_indicator_widget', $data);
    }

    function load_employee_completion(){
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/my_emp_completion_widget', $data);
    }

    function load_overdue_payable_receivable()
    {
        $documentId = $this->input->post("documentID");
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["documentId"] = $documentId ?? null;
        $this->load->view('system/erp_ajax_load_overdue_payable_receivable', $data);
    }

    function load_fast_moving_item()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_ajax_load_fast_moving_item', $data);
    }

    function load_postdated_cheque()
    {
        $documentId = $this->input->post("documentID");
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["documentId"] = $documentId ?? null;
        $this->load->view('system/erp_ajax_load_postdated_cheque', $data);
    }

    Function load_performance_summary()
    {
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }
        $data["totalRevenue"] = $this->Finance_dashboard_model->getTotalRevenue($beginingDate, $endDate);
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["performanceSummary"] = $this->Finance_dashboard_model->getPerformanceSummary($beginingDate, $endDate);
        $this->load->view('system/erp_ajax_load_performance_summary', $data);
    }

    Function load_revenue_detail_analysis()
    {
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["totalRevenue"] = $this->Finance_dashboard_model->getTotalRevenue($beginingDate, $endDate);
        $data["revenueDetailAnalysis"] = $this->Finance_dashboard_model->getRevenueDetailAnalysis($beginingDate, $endDate);
        $this->load->view('system/erp_ajax_revenue_detail_analysis', $data);
    }

    function fetch_financialPosition()
    {
        //$this->datatables->set_database('db2');
        $this->datatables->select('CurrencyCode as bankCurrencyCode,IFNULL(srp_erp_bankledger.bankCurrencyDecimalPlaces, 2) AS currencyDecimalPlaces, srp_erp_chartofaccounts.GLDescription,(SUM(if(srp_erp_bankledger.transactionType = 1,srp_erp_bankledger.bankcurrencyAmount,0)) - SUM(if(srp_erp_bankledger.transactionType = 2,srp_erp_bankledger.bankcurrencyAmount,0))) as bookBalance,(SUM(if(srp_erp_bankledger.transactionType = 1 AND srp_erp_bankrecmaster.approvedYN = 1,srp_erp_bankledger.bankcurrencyAmount,0)) - SUM(if(srp_erp_bankledger.transactionType = 2 AND srp_erp_bankrecmaster.approvedYN = 1,srp_erp_bankledger.bankcurrencyAmount,0))) as bankBalance', false)
            ->from('srp_erp_chartofaccounts')
            ->join('srp_erp_bankledger', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_bankledger.bankGLAutoID AND srp_erp_bankledger.companyID = ' . $this->common_data['company_data']['company_id'], 'INNER')
            ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_bankledger.bankCurrencyID', 'LEFT')
            ->where('srp_erp_chartofaccounts.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_chartofaccounts.isBank', 1)
            ->group_by('srp_erp_chartofaccounts.GLAutoID')
            ->edit_column('bookBalance', '<div class="text-right"><b> $1 </b></div>', 'dashboard_format_number(bookBalance, currencyDecimalPlaces)')
            ->edit_column('bankBalance', '<div class="text-right"><b> $1 </b></div>', 'dashboard_format_number(bankBalance, currencyDecimalPlaces)');
        echo $this->datatables->generate();
    }

    function fetch_postdated_cheque_given()
    {
        $companyid = current_companyID();
        //$this->datatables->set_database('db2');
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        $duedays = $this->input->post("dueDays");

        if ($PostDatedChequeManagement == 1) {
            if ($this->input->post("dueDays") == 'all') {
                $this->datatables->select('t1.bankCurrencyAmount as bankCurrencyAmount,t1.dueDate as dueDate,t1.bankCurrency as bankCurrency,t1.dueDays as dueDays,t1.vendor as vendor,t1.remainIn as remainIn,chequeNo', false)
                    ->from('srp_erp_company as Company')
                    ->join("
            (SELECT
	srp_erp_bankledger.bankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	bankCurrency,
	DATEDIFF( chequeDate, CURDATE( ) ) AS dueDays,
	IFNULL( ( CONCAT( IFNULL( srp_erp_customermaster.customerName, NULL ), IFNULL( srp_erp_suppliermaster.supplierName, NULL ) ) ), partyName ) AS vendor,
	remainIn,
	srp_erp_bankledger.companyID,
	chequeNo
FROM
	`srp_erp_bankledger`
	LEFT JOIN `srp_erp_bankrecmaster` ON `srp_erp_bankrecmaster`.`bankrecAutoID` = `srp_erp_bankledger`.`bankRecMonthID`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_customermaster`.`customerSystemCode`
	LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_suppliermaster`.`supplierSystemCode`
WHERE
	`srp_erp_bankledger`.`transactionType` = 2
	AND `srp_erp_bankledger`.`companyID` = '{$companyid}'
	AND ( `srp_erp_bankrecmaster`.`approvedYN` = 0 OR `srp_erp_bankledger`.`bankRecMonthID` IS NULL )
	AND `srp_erp_bankledger`.`documentDate` < `srp_erp_bankledger`.`chequeDate` AND `srp_erp_bankledger`.`chequeDate` >= CURDATE( )
 UNION
 SELECT
	IFNULL(
	(
	( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( debitnote.transactionAmount, 0 ) - IFNULL( SR.transactionAmount, 0 )
	) / bankCurrencyExchangeRate,
	'-'
	) AS bankCurrencyAmount,
	PVchequeDate AS dueDate,
	currencymaster.CurrencyCode,
	DATEDIFF( PVchequeDate, CURRENT_DATE ( ) ) AS dueDays,
	partyName as vendor,
	\"0\" AS remainIn,
	paymentvoucher.companyID,
	PVchequeNo AS chequeNo
FROM
	srp_erp_paymentvouchermaster paymentvoucher
	LEFT JOIN srp_erp_currencymaster currencymaster on currencymaster.currencyID = paymentvoucher.bankCurrencyID
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON addondet.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	payVoucherAutoId
FROM
	srp_erp_paymentvoucherdetail
WHERE
	srp_erp_paymentvoucherdetail.type = \"GL\"
	OR srp_erp_paymentvoucherdetail.type = \"Item\"
GROUP BY
	payVoucherAutoId
	) tyepdet ON tyepdet.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	payVoucherAutoId
FROM
	srp_erp_paymentvoucherdetail
WHERE
	srp_erp_paymentvoucherdetail.type != \"debitnote\"
	AND srp_erp_paymentvoucherdetail.type != \"SR\"
GROUP BY
	payVoucherAutoId
	) det ON det.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"debitnote\" GROUP BY payVoucherAutoId ) debitnote ON debitnote.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"SR\" GROUP BY payVoucherAutoId ) SR ON SR.payVoucherAutoId = paymentvoucher.payVoucherAutoId
WHERE
	paymentvoucher.companyID = '{$companyid}'
	AND modeOfPayment = 2
	AND paymentType = 1
	AND confirmedYN = 1
	AND approvedYN != 1
	AND PVchequeNo <> ''
	AND PVchequeDate > PVdate
	UNION
	SELECT
	banktransfer.toBankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( chequeDate, CURRENT_DATE ( ) ) AS dueDays,
	\"-\" as vendor,
	\"0\" AS remainIn,
	banktransfer.companyID,
	chequeNo
FROM
	srp_erp_banktransfer banktransfer
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = banktransfer.toBankGLAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = banktransfer.toBankCurrencyID
WHERE
	banktransfer.companyID = '{$companyid}'
	AND transferType = 2
	AND banktransfer.confirmedYN = 1
	AND banktransfer.approvedYN != 1
	AND chequeNo <> ''
	AND chequeDate > DATE_FORMAT( transferedDate, '%Y-%m-%d' )



	) t1", 't1.companyID=Company.company_id')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();
            } else {
                $this->datatables->select('t1.bankCurrencyAmount as bankCurrencyAmount,t1.dueDate as dueDate,t1.bankCurrency as bankCurrency,t1.dueDays as dueDays,t1.vendor as vendor,t1.remainIn as remainIn,chequeNo', false)
                    ->from('srp_erp_company as Company')
                    ->join("
            (SELECT
	srp_erp_bankledger.bankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	bankCurrency,
	DATEDIFF( chequeDate, CURDATE( ) ) AS dueDays,
	IFNULL( ( CONCAT( IFNULL( srp_erp_customermaster.customerName, NULL ), IFNULL( srp_erp_suppliermaster.supplierName, NULL ) ) ), partyName ) AS vendor,
	remainIn,
	srp_erp_bankledger.companyID,
	chequeNo
FROM
	`srp_erp_bankledger`
	LEFT JOIN `srp_erp_bankrecmaster` ON `srp_erp_bankrecmaster`.`bankrecAutoID` = `srp_erp_bankledger`.`bankRecMonthID`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_customermaster`.`customerSystemCode`
	LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_suppliermaster`.`supplierSystemCode`
WHERE
	 `srp_erp_bankledger`.`companyID` = '{$companyid}'
	AND `srp_erp_bankledger`.`transactionType` = 2
	AND DATEDIFF( chequeDate, CURDATE( ) ) <= '{$duedays}'

	AND ( `srp_erp_bankrecmaster`.`approvedYN` = 0 OR `srp_erp_bankledger`.`bankRecMonthID` IS NULL )
	AND `srp_erp_bankledger`.`documentDate` < `srp_erp_bankledger`.`chequeDate` AND `srp_erp_bankledger`.`chequeDate` >= CURDATE( )
 UNION
 SELECT
	IFNULL(
	(
	( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( debitnote.transactionAmount, 0 ) - IFNULL( SR.transactionAmount, 0 )
	) / bankCurrencyExchangeRate,
	'-'
	) AS bankCurrencyAmount,
	PVchequeDate AS dueDate,
	currencymaster.CurrencyCode,
	DATEDIFF( PVchequeDate, CURRENT_DATE ( ) ) AS dueDays,
	partyName as vendor,
	\"0\" AS remainIn,
	paymentvoucher.companyID,
	PVchequeNo AS chequeNo
FROM
	srp_erp_paymentvouchermaster paymentvoucher
	LEFT JOIN srp_erp_currencymaster currencymaster on currencymaster.currencyID = paymentvoucher.bankCurrencyID
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON addondet.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	payVoucherAutoId
FROM
	srp_erp_paymentvoucherdetail
WHERE
	srp_erp_paymentvoucherdetail.type = \"GL\"
	OR srp_erp_paymentvoucherdetail.type = \"Item\"
GROUP BY
	payVoucherAutoId
	) tyepdet ON tyepdet.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	payVoucherAutoId
FROM
	srp_erp_paymentvoucherdetail
WHERE
	srp_erp_paymentvoucherdetail.type != \"debitnote\"
	AND srp_erp_paymentvoucherdetail.type != \"SR\"
GROUP BY
	payVoucherAutoId
	) det ON det.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"debitnote\" GROUP BY payVoucherAutoId ) debitnote ON debitnote.payVoucherAutoId = paymentvoucher.payVoucherAutoId
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"SR\" GROUP BY payVoucherAutoId ) SR ON SR.payVoucherAutoId = paymentvoucher.payVoucherAutoId
WHERE
	paymentvoucher.companyID = '{$companyid}'
	AND DATEDIFF( PVchequeDate, CURRENT_DATE ( ) ) <= '{$duedays}'
	AND modeOfPayment = 2
	AND paymentType = 1
	AND confirmedYN = 1
	AND approvedYN != 1
	AND PVchequeNo <> ''
	AND PVchequeDate > PVdate
	UNION
	SELECT
	banktransfer.toBankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( chequeDate, CURRENT_DATE ( ) ) AS dueDays,
	\"-\" as vendor,
	\"0\" AS remainIn,
	banktransfer.companyID,
	chequeNo
FROM
	srp_erp_banktransfer banktransfer
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = banktransfer.toBankGLAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = banktransfer.toBankCurrencyID
WHERE
	banktransfer.companyID = '{$companyid}'
	AND DATEDIFF( chequeDate, CURRENT_DATE ( ) ) <= '{$duedays}'
	AND transferType = 2
	AND banktransfer.confirmedYN = 1
	AND banktransfer.approvedYN != 1
	AND chequeNo <> ''
	AND chequeDate > DATE_FORMAT( transferedDate, '%Y-%m-%d' )
	) t1", 't1.companyID=Company.company_id')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();
            }


        } else {
            if ($this->input->post("dueDays") == 'all') {
                $this->datatables->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,IFNULL((CONCAT(IFNULL(srp_erp_customermaster.customerName,null),IFNULL(srp_erp_suppliermaster.supplierName,null))),partyName) as vendor,remainIn,chequeNo', false)
                    ->from('srp_erp_bankledger')
                    ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
                    ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
                    ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
                    ->where('srp_erp_bankledger.transactionType', 2)
                    ->where('srp_erp_bankledger.companyID', $this->common_data['company_data']['company_id'])
                    ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
                    ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
                    ->where('srp_erp_bankledger.chequeDate >= CURDATE()')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');


                echo $this->datatables->generate();
            } else {
                $this->datatables->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,IFNULL((CONCAT(IFNULL(srp_erp_customermaster.customerName,null),IFNULL(srp_erp_suppliermaster.supplierName,null))),partyName) as vendor,remainIn,chequeNo', false)
                    ->from('srp_erp_bankledger')
                    ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
                    ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
                    ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
                    ->where('srp_erp_bankledger.transactionType', 2)
                    ->where('srp_erp_bankledger.companyID', $this->common_data['company_data']['company_id'])
                    ->where('DATEDIFF(chequeDate,CURDATE()) <=', $this->input->post("dueDays"))
                    ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
                    ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
                    ->where('srp_erp_bankledger.chequeDate >= CURDATE()')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();

                //echo $this->datatables->last_query();
            }
        }


    }

    function fetch_postdated_cheque_received()
    {
        //$this->datatables->set_database('db2');
        $duedays = $this->input->post("dueDays");
        $companyid = current_companyID();
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque
        if ($PostDatedChequeManagement == 1) {
            if ($this->input->post("dueDays") == 'all') {
                $this->datatables->select('t1.bankCurrencyAmount as bankCurrencyAmount,t1.dueDate as dueDate,t1.bankCurrency as bankCurrency,t1.dueDays as dueDays,t1.vendor as vendor,t1.remainIn as remainIn,chequeNo', false)
                    ->from('srp_erp_company as Company')
                    ->join("
            (SELECT
	srp_erp_bankledger.bankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	bankCurrency,
	DATEDIFF( chequeDate, CURDATE( ) ) AS dueDays,
	IFNULL( ( CONCAT( IFNULL( srp_erp_customermaster.customerName, NULL ), IFNULL( srp_erp_suppliermaster.supplierName, NULL ) ) ), partyName ) AS vendor,
	remainIn,
	srp_erp_bankledger.companyID,
	chequeNo
FROM
	`srp_erp_bankledger`
	LEFT JOIN `srp_erp_bankrecmaster` ON `srp_erp_bankrecmaster`.`bankrecAutoID` = `srp_erp_bankledger`.`bankRecMonthID`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_customermaster`.`customerSystemCode`
	LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_suppliermaster`.`supplierSystemCode`
WHERE
	`srp_erp_bankledger`.`transactionType` = 1
	AND `srp_erp_bankledger`.`companyID` = '{$companyid}'
	AND ( `srp_erp_bankrecmaster`.`approvedYN` = 0 OR `srp_erp_bankledger`.`bankRecMonthID` IS NULL )
	AND `srp_erp_bankledger`.`documentDate` < `srp_erp_bankledger`.`chequeDate` AND `srp_erp_bankledger`.`chequeDate` >= CURDATE( )
UNION

SELECT
	(
	( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( Creditnots.transactionAmount, 0 ) / bankCurrencyExchangeRate
	) AS bankCurrencyAmount,
	RVchequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( RVchequeDate, CURRENT_DATE ( ) ) AS dueDays,
	customer.customerName AS vendor,
	\"0\" AS remainIn,
	receiptmastertbl.companyID,
	RVchequeNo AS chequeNo
FROM
	srp_erp_customerreceiptmaster receiptmastertbl
	LEFT JOIN srp_erp_customermaster customer on customer.customerAutoID = receiptmastertbl.customerID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = receiptmastertbl.bankCurrencyID
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type != \"creditnote\" GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	receiptVoucherAutoId
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.type = \"GL\"
	OR srp_erp_customerreceiptdetail.type = \"Item\"
GROUP BY
	receiptVoucherAutoId
	) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = \"creditnote\" GROUP BY receiptVoucherAutoId ) Creditnots ON ( `Creditnots`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `receiptmastertbl`.`customerID`
WHERE
	receiptmastertbl.companyID = '{$companyid}'
	AND receiptmastertbl.modeOfPayment = 2
	AND receiptmastertbl.confirmedYN = 1
	AND receiptmastertbl.approvedYN != 1
	AND RVchequeNo <> ''
	AND RVchequeDate > DATE_FORMAT( RVdate, '%Y-%m-%d' )
	UNION

	SELECT
	banktransfer.fromBankCurrentBalance AS bankCurrencyAmount,
	chequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( chequeDate, CURRENT_DATE ( ) ) AS dueDays,
	\"-\" AS vendor,
	\"0\" AS remainIn ,
	banktransfer.companyID,
	chequeNo
FROM
	srp_erp_banktransfer banktransfer
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = banktransfer.fromBankGLAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = banktransfer.fromBankCurrencyID
WHERE
	banktransfer.companyID = '{$companyid}'
	AND transferType = 2
	AND banktransfer.confirmedYN = 1
	AND banktransfer.approvedYN != 1
	AND chequeNo <> ''
	AND chequeDate > DATE_FORMAT( transferedDate, '%Y-%m-%d' )) t1 ", 't1.companyID=Company.company_id')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();

            } else {


                $this->datatables->select('t1.bankCurrencyAmount as bankCurrencyAmount,t1.dueDate as dueDate,t1.bankCurrency as bankCurrency,t1.dueDays as dueDays,t1.vendor as vendor,t1.remainIn as remainIn,chequeNo', false)
                    ->from('srp_erp_company as Company')
                    ->join("
            (SELECT
	srp_erp_bankledger.bankCurrencyAmount AS bankCurrencyAmount,
	chequeDate AS dueDate,
	bankCurrency,
	DATEDIFF( chequeDate, CURDATE( ) ) AS dueDays,
	IFNULL( ( CONCAT( IFNULL( srp_erp_customermaster.customerName, NULL ), IFNULL( srp_erp_suppliermaster.supplierName, NULL ) ) ), partyName ) AS vendor,
	remainIn,
	srp_erp_bankledger.companyID,
	chequeNo
FROM
	`srp_erp_bankledger`
	LEFT JOIN `srp_erp_bankrecmaster` ON `srp_erp_bankrecmaster`.`bankrecAutoID` = `srp_erp_bankledger`.`bankRecMonthID`
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_customermaster`.`customerSystemCode`
	LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_bankledger`.`partyCode` = `srp_erp_suppliermaster`.`supplierSystemCode`
WHERE
	`srp_erp_bankledger`.`transactionType` = 1
	AND `srp_erp_bankledger`.`companyID` = '{$companyid}'
	AND DATEDIFF( chequeDate, CURDATE( ) ) <= '{$duedays}'
	AND ( `srp_erp_bankrecmaster`.`approvedYN` = 0 OR `srp_erp_bankledger`.`bankRecMonthID` IS NULL )
	AND `srp_erp_bankledger`.`documentDate` < `srp_erp_bankledger`.`chequeDate` AND `srp_erp_bankledger`.`chequeDate` >= CURDATE( )
UNION

SELECT
	(
	( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( Creditnots.transactionAmount, 0 ) / bankCurrencyExchangeRate
	) AS bankCurrencyAmount,
	RVchequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( RVchequeDate, CURRENT_DATE ( ) ) AS dueDays,
	customer.customerName AS vendor,
	\"0\" AS remainIn,
	receiptmastertbl.companyID,
	RVchequeNo AS chequeNo
FROM
	srp_erp_customerreceiptmaster receiptmastertbl
	LEFT JOIN srp_erp_customermaster customer on customer.customerAutoID = receiptmastertbl.customerID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = receiptmastertbl.bankCurrencyID
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type != \"creditnote\" GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN (
SELECT
	SUM( transactionAmount ) AS transactionAmount,
	receiptVoucherAutoId
FROM
	srp_erp_customerreceiptdetail
WHERE
	srp_erp_customerreceiptdetail.type = \"GL\"
	OR srp_erp_customerreceiptdetail.type = \"Item\"
GROUP BY
	receiptVoucherAutoId
	) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = \"creditnote\" GROUP BY receiptVoucherAutoId ) Creditnots ON ( `Creditnots`.`receiptVoucherAutoId` = receiptmastertbl.receiptVoucherAutoId )
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `receiptmastertbl`.`customerID`
WHERE
	receiptmastertbl.companyID = '{$companyid}'
	 AND DATEDIFF( RVchequeDate, CURRENT_DATE ( ) ) <= '{$duedays}'
	AND receiptmastertbl.modeOfPayment = 2
	AND receiptmastertbl.confirmedYN = 1
	AND receiptmastertbl.approvedYN != 1
	AND RVchequeNo <> ''
	AND RVchequeDate > DATE_FORMAT( RVdate, '%Y-%m-%d' )
	UNION

	SELECT
	banktransfer.fromBankCurrentBalance AS bankCurrencyAmount,
	chequeDate AS dueDate,
	currencymaster.CurrencyCode AS bankCurrency,
	DATEDIFF( chequeDate, CURRENT_DATE ( ) ) AS dueDays,
	\"-\" AS vendor,
	\"0\" AS remainIn ,
	banktransfer.companyID,
	chequeNo
FROM
	srp_erp_banktransfer banktransfer
	LEFT JOIN srp_erp_chartofaccounts chart ON chart.GLAutoID = banktransfer.fromBankGLAutoID
	LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = banktransfer.fromBankCurrencyID
WHERE
	banktransfer.companyID = '{$companyid}'
	AND DATEDIFF( chequeDate, CURRENT_DATE ( ) )<= '{$duedays}'
	AND transferType = 2
	AND banktransfer.confirmedYN = 1
	AND banktransfer.approvedYN != 1
	AND chequeNo <> ''
	AND chequeDate > DATE_FORMAT( transferedDate, '%Y-%m-%d' )) t1 ", 't1.companyID=Company.company_id')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();

            }
        } else {
            if ($this->input->post("dueDays") == 'all') {

                $this->datatables->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,IFNULL((CONCAT(IFNULL(srp_erp_customermaster.customerName,null),IFNULL(srp_erp_suppliermaster.supplierName,null))),partyName) as vendor,remainIn,chequeNo', false)
                    ->from('srp_erp_bankledger')
                    ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
                    ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
                    ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
                    ->where('srp_erp_bankledger.transactionType', 1)
                    ->where('srp_erp_bankledger.companyID', $this->common_data['company_data']['company_id'])
                    ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
                    ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
                    ->where('srp_erp_bankledger.chequeDate >= CURDATE()')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();
            } else {
                $this->datatables->select('srp_erp_bankledger.bankCurrencyAmount as bankCurrencyAmount,chequeDate as dueDate,bankCurrency,DATEDIFF(chequeDate,CURDATE()) as dueDays,IFNULL((CONCAT(IFNULL(srp_erp_customermaster.customerName,null),IFNULL(srp_erp_suppliermaster.supplierName,null))),partyName) as vendor,remainIn,chequeNo', false)
                    ->from('srp_erp_bankledger')
                    ->join('srp_erp_bankrecmaster', 'srp_erp_bankrecmaster.bankrecAutoID = srp_erp_bankledger.bankRecMonthID', 'LEFT')
                    ->join('srp_erp_customermaster', 'srp_erp_bankledger.partyCode = srp_erp_customermaster.customerSystemCode', 'LEFT')
                    ->join('srp_erp_suppliermaster', 'srp_erp_bankledger.partyCode = srp_erp_suppliermaster.supplierSystemCode', 'LEFT')
                    ->where('srp_erp_bankledger.transactionType', 1)
                    ->where('srp_erp_bankledger.companyID', $this->common_data['company_data']['company_id'])
                    ->where('DATEDIFF(chequeDate,CURDATE()) <=', $this->input->post("dueDays"))
                    ->where('(srp_erp_bankrecmaster.approvedYN = 0 OR srp_erp_bankledger.bankRecMonthID IS NULL)')
                    ->where('srp_erp_bankledger.documentDate < srp_erp_bankledger.chequeDate')
                    ->where('srp_erp_bankledger.chequeDate >= CURDATE()')
                    ->edit_column('bankCurrencyAmount', '<div class="text-right"> <b>$1</b>:$2 </div>', 'bankCurrency,format_number(bankCurrencyAmount)')
                    ->edit_column('dueDays', '<div class="text-right"> $1 </div>', 'dashboard_color_duedays(dueDays)');
                echo $this->datatables->generate();
            }
        }


    }

    function fetch_overdue_payables()
    {
        $fields = "";
        $currency = "";
        $userDashboardID = $this->input->post("userDashboardID");
        if ($this->input->post("currency") == 'transactionAmount') {
            $currency = ',srp_erp_paysupplierinvoicemaster.transactionCurrency';
            $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrency as currency,';
            $fields .= 'srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces as decimalPlace,';
            $fields .= 'SUM(srp_erp_paysupplierinvoicemaster.transactionAmount) - (IFNULL(pvd.transactionAmount,0) + IFNULL(dnd.transactionAmount,0) + IFNULL(pva.transactionAmount,0)) as amount,';
        } else if ($this->input->post("currency") == 'companyReportingAmount') {
            $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrency as currency,';
            $fields .= 'srp_erp_paysupplierinvoicemaster.companyReportingCurrencyDecimalPlaces as decimalPlace,';
            $fields .= 'SUM(srp_erp_paysupplierinvoicemaster.companyReportingAmount) - (IFNULL(pvd.companyReportingAmount,0)+ IFNULL(dnd.companyReportingAmount,0) + IFNULL(pva.companyReportingAmount,0)) as amount,';
        }
        $this->datatables->select($fields . 'srp_erp_suppliermaster.supplierName as supplierName,supplierAutoID', false)
            ->from('srp_erp_paysupplierinvoicemaster')
            ->join('srp_erp_suppliermaster', 'srp_erp_paysupplierinvoicemaster.supplierID = srp_erp_suppliermaster.supplierAutoID', 'LEFT')
            ->join("(SELECT
		 IFNULL(SUM(srp_erp_paymentvoucherdetail.companyReportingAmount),0) as companyReportingAmount,IFNULL(SUM(srp_erp_paymentvoucherdetail.transactionAmount),0) as transactionAmount,srp_erp_paymentvoucherdetail.InvoiceAutoID,srp_erp_paymentvoucherdetail.payVoucherAutoID,partyID,
srp_erp_paymentvouchermaster.transactionCurrency
	FROM
		srp_erp_paymentvoucherdetail
		INNER JOIN `srp_erp_paymentvouchermaster` ON `srp_erp_paymentvouchermaster`.`payVoucherAutoID` = `srp_erp_paymentvoucherdetail`.`payVoucherAutoID` AND `srp_erp_paymentvouchermaster`.`approvedYN` = 1
	WHERE
		`srp_erp_paymentvoucherdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_paymentvouchermaster.PVDate <= '" . current_date() . "' AND srp_erp_paymentvoucherdetail.InvoiceAutoID IS NOT NULL  GROUP BY srp_erp_paymentvouchermaster.partyID,
srp_erp_paymentvouchermaster.transactionCurrency) pvd", 'pvd.partyID = srp_erp_paysupplierinvoicemaster.supplierID AND `pvd`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT IFNULL(SUM(srp_erp_debitnotedetail.transactionAmount),0) as transactionAmount,IFNULL(SUM(srp_erp_debitnotedetail.companyReportingAmount),0) as companyReportingAmount,
		 srp_erp_debitnotedetail.InvoiceAutoID,srp_erp_debitnotedetail.debitNoteMasterAutoID,supplierID,
srp_erp_debitnotemaster.transactionCurrency
	FROM
		srp_erp_debitnotedetail 
		INNER JOIN `srp_erp_debitnotemaster` ON `srp_erp_debitnotemaster`.`debitNoteMasterAutoID` = `srp_erp_debitnotedetail`.`debitNoteMasterAutoID` AND `srp_erp_debitnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_debitnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_debitnotemaster.debitNoteDate <= '" . current_date() . "' AND srp_erp_debitnotedetail.InvoiceAutoID IS NOT NULL GROUP BY srp_erp_debitnotemaster.supplierID,
srp_erp_debitnotemaster.transactionCurrency) dnd", 'dnd.supplierID = srp_erp_paysupplierinvoicemaster.supplierID AND `dnd`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT
            IFNULL(SUM(srp_erp_pvadvancematchdetails.companyReportingAmount),0) as companyReportingAmount,IFNULL(SUM(srp_erp_pvadvancematchdetails.transactionAmount),0) as transactionAmount,
		 srp_erp_pvadvancematchdetails.InvoiceAutoID,supplierID,
srp_erp_pvadvancematch.transactionCurrency
	FROM
	srp_erp_pvadvancematchdetails
		INNER JOIN `srp_erp_pvadvancematch` ON `srp_erp_pvadvancematch`.`matchID` = `srp_erp_pvadvancematchdetails`.`matchID` AND `srp_erp_pvadvancematch`.`confirmedYN` = 1
		WHERE `srp_erp_pvadvancematch`.`matchDate` <= '" . current_date() . "' AND srp_erp_pvadvancematchdetails.InvoiceAutoID IS NOT NULL GROUP BY srp_erp_pvadvancematch.supplierID,
srp_erp_pvadvancematch.transactionCurrency) pva", 'pva.supplierID = srp_erp_paysupplierinvoicemaster.supplierID AND `pva`.`transactionCurrency` = `srp_erp_paysupplierinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->where('srp_erp_paysupplierinvoicemaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('invoiceDueDate <=', current_date())
            ->where('srp_erp_paysupplierinvoicemaster.approvedYN ', 1)
            ->group_by('srp_erp_paysupplierinvoicemaster.supplierID' . $currency)
            ->edit_column('supplierName', '<div style=" cursor: pointer;" onclick="dashboardOverduePayables' . $userDashboardID . '($2,\'$3\')">$1</div>', 'supplierName,supplierAutoID,currency')
            ->edit_column('amount', '<div class="text-right" style=" cursor: pointer;" onclick="dashboardOverduePayables' . $userDashboardID . '($2,\'$3\')"><b>$1</b></div>', 'dashboard_format_number(amount,decimalPlace),supplierAutoID,currency');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    function fetch_overdue_receivable()
    {
        $fields = "";
        $currency = "";
        $userDashboardID = $this->input->post("userDashboardID");
        if ($this->input->post("currency") == 'transactionAmount') {
            $currency = ',srp_erp_customerinvoicemaster.transactionCurrency';
            $fields .= 'srp_erp_customerinvoicemaster.transactionCurrency as currency,';
            $fields .= 'srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces as decimalPlace,';
            $fields .= 'SUM(srp_erp_customerinvoicemaster.transactionAmount) - (IFNULL(pvd.transactionAmount,0)+IFNULL(cnd.transactionAmount,0) + IFNULL(ca.transactionAmount,0)) as amount,';
        } else if ($this->input->post("currency") == 'companyReportingAmount') {
            $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrency as currency,';
            $fields .= 'srp_erp_customerinvoicemaster.companyReportingCurrencyDecimalPlaces as decimalPlace,';
            $fields .= 'SUM(srp_erp_customerinvoicemaster.companyReportingAmount) - (IFNULL(pvd.companyReportingAmount,0)+IFNULL(cnd.companyReportingAmount,0)+IFNULL(ca.transactionAmount,0)) as amount,';
        }
        $this->datatables->select($fields . 'srp_erp_customermaster.customerName as customerName,customerAutoID', false)
            ->from('srp_erp_customerinvoicemaster')
            ->join('srp_erp_customermaster', 'srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID', 'LEFT')
            ->join("(SELECT SUM(srp_erp_customerreceiptdetail.transactionAmount) as transactionAmount,SUM(srp_erp_customerreceiptdetail.companyReportingAmount) as companyReportingAmount,
		 srp_erp_customerreceiptdetail.invoiceAutoID,srp_erp_customerreceiptdetail.receiptVoucherAutoID,
		 srp_erp_customerreceiptmaster.customerID,
srp_erp_customerreceiptmaster.transactionCurrency
	FROM
		srp_erp_customerreceiptdetail
		INNER JOIN `srp_erp_customerreceiptmaster` ON `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId` = `srp_erp_customerreceiptdetail`.`receiptVoucherAutoId` AND `srp_erp_customerreceiptmaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerreceiptdetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerreceiptmaster.RVDate <= '" . current_date(false) . "' AND srp_erp_customerreceiptdetail.invoiceAutoID IS NOT NULL  GROUP BY srp_erp_customerreceiptmaster.customerID,srp_erp_customerreceiptmaster.transactionCurrency) pvd", 'pvd.customerID = srp_erp_customerinvoicemaster.customerID AND `pvd`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT SUM(srp_erp_creditnotedetail.transactionAmount) as transactionAmount,SUM(srp_erp_creditnotedetail.companyReportingAmount) as companyReportingAmount,
		 invoiceAutoID,srp_erp_creditnotedetail.creditNoteMasterAutoID,srp_erp_creditnotemaster.customerID,srp_erp_creditnotemaster.transactionCurrency
	FROM
		srp_erp_creditnotedetail
		INNER JOIN `srp_erp_creditnotemaster` ON `srp_erp_creditnotemaster`.`creditNoteMasterAutoID` = `srp_erp_creditnotedetail`.`creditNoteMasterAutoID` AND `srp_erp_creditnotemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_creditnotedetail`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_creditnotemaster.creditNoteDate <= '" . current_date(false) . "' AND srp_erp_creditnotedetail.invoiceAutoID IS NOT NULL GROUP BY srp_erp_creditnotemaster.customerID,srp_erp_creditnotemaster.transactionCurrency) cnd", 'cnd.customerID = srp_erp_customerinvoicemaster.customerID AND `cnd`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->join("(SELECT SUM(srp_erp_rvadvancematchdetails.transactionAmount) as transactionAmount,SUM(srp_erp_rvadvancematchdetails.companyReportingAmount) as companyReportingAmount,
 srp_erp_rvadvancematchdetails.InvoiceAutoID,srp_erp_rvadvancematchdetails.receiptVoucherAutoID,srp_erp_rvadvancematch.customerID,srp_erp_rvadvancematch.transactionCurrency
	FROM srp_erp_rvadvancematchdetails 
	INNER JOIN `srp_erp_rvadvancematch` ON `srp_erp_rvadvancematchdetails`.`matchID` = `srp_erp_rvadvancematch`.`matchID` AND `srp_erp_rvadvancematch`.`confirmedYN` = 1
	WHERE `srp_erp_rvadvancematchdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_rvadvancematchdetails.invoiceAutoID IS NOT NULL GROUP BY srp_erp_rvadvancematch.customerID,srp_erp_rvadvancematch.transactionCurrency) ca", 'ca.customerID = srp_erp_customerinvoicemaster.customerID AND `ca`.`transactionCurrency` = `srp_erp_customerinvoicemaster`.`transactionCurrency`', 'LEFT')
            ->where('srp_erp_customerinvoicemaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('invoiceDueDate <=', current_date())
            ->where('srp_erp_customerinvoicemaster.approvedYN', 1)
            ->group_by('srp_erp_customerinvoicemaster.customerID' . $currency)
            ->edit_column('customerName', '<div style=" cursor: pointer;" onclick="dashboardOverdueReceivables' . $userDashboardID . '($2,\'$3\')">$1</div>', 'customerName,customerAutoID,currency')
            ->edit_column('amount', '<div class="text-right" style=" cursor: pointer;" onclick="dashboardOverdueReceivables' . $userDashboardID . '($2,\'$3\')"> <b>$1</b></div>', 'dashboard_format_number(amount,decimalPlace),customerAutoID,currency');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    function fetch_fast_moving_item()
    {
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }

        $this->datatables->select('SUM(((il.transactionQTY/convertionRate)*-1) * il.salesPrice/il.companyReportingExchangeRate) as companyReportingAmount,il.companyReportingCurrencyDecimalPlaces as companyReportingCurrencyDecimalPlaces,im.defaultUnitOfMeasure as UOM,im.itemDescription,SUM(il.transactionQTY/convertionRate)*-1 as transactionQTY,im.itemSystemCode,il.companyReportingCurrencyDecimalPlaces,im.currentStock as currentStock', false)
            ->from('srp_erp_itemledger il')
            ->join('srp_erp_itemmaster im', 'il.itemAutoID = im.itemAutoID', 'inner')
            ->where('il.documentDate BETWEEN "' . $beginingDate . '"
AND "' . $endDate . '" AND il.companyID = "' . $this->common_data['company_data']['company_id'] . '" AND il.documentCode IN ("CINV","RV","POS") AND im.mainCategory = "Inventory"')
            ->group_by('il.itemAutoID')
            ->edit_column('companyReportingAmount', '<div class="text-right"> <b>$1</b></div>', 'dashboard_format_number(companyReportingAmount,companyReportingCurrencyDecimalPlaces)')
            ->edit_column('currentStock', '<div class="text-right"> <b>$1</b></div>', 'dashboard_format_number(currentStock)');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    Function load_revenue_detail_analysis_by_glcode()
    {
        $company_type = $this->session->userdata("companyType");
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        if($company_type==1) {
            $lastTwoYears = get_last_two_financial_year();
        }else
        {
            $lastTwoYears = get_last_two_financial_year_group();
        }

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["totalRevenue"] = $this->Finance_dashboard_model->getTotalRevenue($beginingDate, $endDate);
        $data["revenueDetailAnalysisByGlcode"] = $this->Finance_dashboard_model->getRevenueDetailAnalysisByGLcode($beginingDate, $endDate);
        $this->load->view('system/erp_ajax_revenue_detail_analysis_by_glcode', $data);
    }

    function fetch_assigned_dashboard()
    {
        echo json_encode($this->Finance_dashboard_model->getAssignedDashboard());
    }

    function fetch_assigned_dashboard_widget()
    {
        echo json_encode($this->Finance_dashboard_model->getAssignedDashboardWidget());
    }

    /*Started Function*/
    function save_private_link()
    {
        $this->form_validation->set_rules('description', 'Link Name', 'trim|required');
        $this->form_validation->set_rules('hyperlink', 'Link', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model->save_private_link());
        }


    }

    function deletePrivateLink()
    {
        echo json_encode($this->Finance_dashboard_model->deletePrivateLink());
    }

    function save_public_link()
    {
        echo json_encode($this->Finance_dashboard_model->save_public_link());
    }

    function load_shortcut_links()
    {
        $data["shortcutlinks"] = $this->Finance_dashboard_model->getShortcutLinks();
        $this->load->view('system/erp_ajax_load_shortcut_links', $data);
    }

    Function load_Public_links()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["publiclinks"] = $this->Finance_dashboard_model->getPublicLinks();
        $data["publiclist"] = $this->Finance_dashboard_model->getPublicList();
        $this->load->view('system/erp_ajax_load_public_links', $data);
    }

    Function load_new_members()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["newmembers"] = $this->Finance_dashboard_model->getNewMembers();
        $data["newmembersRest"] = $this->Finance_dashboard_model->getAllNewMembers();
        $this->load->view('system/erp_ajax_load_new_members', $data);
    }

    function load_to_do_list()
    {
        $data["todolist"] = $this->Finance_dashboard_model->getToDoList();
        $data["todolistHistory"] = $this->Finance_dashboard_model->getToDoListHistory();
        $this->load->view('system/erp_ajax_load_to_do_list', $data);
    }

    function save_to_do_list()
    {
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('startDate', 'Date', 'trim|required');
        $this->form_validation->set_rules('priority', 'Priority', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model->save_to_do_list());
        }
    }

    function check_to_do_list()
    {
        echo json_encode($this->Finance_dashboard_model->check_to_do_list());
    }

    function deletetodoList()
    {
        echo json_encode($this->Finance_dashboard_model->deletetodoList());
    }

    Function load_to_do_list_History()
    {
        $data["todolistHistory"] = $this->Finance_dashboard_model->getToDoListHistory();
        $this->load->view('system/erp_ajax_load_to_do_list_History', $data);
    }

    Function load_to_do_list_view()
    {
        $data["todolist"] = $this->Finance_dashboard_model->getToDoList();
        $this->load->view('system/erp_ajax_load_to_do_list_view', $data);
    }

    /*End Function*/

    function load_sales_log()
    {
        $result = $this->Finance_dashboard_model->getTotalSalesLog();
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["currentYear"] = $result["currentYear"];
        $data["lastYear"] = $result["lastYear"];
        $data["DecimalPlaces"] = $result["DecimalPlaces"];
        $this->load->view('system/erp_ajax_load_sales_log', $data);
    }

    function fetch_sales_log()
    {
        $data = $this->Finance_dashboard_model->getTotalSalesLog();
        $beginingDate = "";
        $beginingDateLast = "";
        $endDate = "";
        $endDateLast = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
            $beginingDateLast = isset($lastTwoYears[$period + 1]["beginingDate"]) ? $lastTwoYears[$period + 1]["beginingDate"] : '';
            $endDateLast = isset($lastTwoYears[$period + 1]["endingDate"]) ? $lastTwoYears[$period + 1]["endingDate"] : '';
        }
        $currentYear = date("Y");
        $lastYear = date("Y", strtotime("-1 year"));
        $this->datatables->select(' customerName,currentYear  ,lastYear ,DecimalPlaces ', false)
            ->from(' (SELECT
IF
	( srp_erp_customermaster.customerName IS NULL OR srp_erp_customermaster.customerName = "", "Sundry", srp_erp_customermaster.customerName ) AS customerName,
	SUM(
	IF
	( documentDate >= "'.$beginingDate.'" AND documentDate <= "'.$endDate.'", companyReportingAmount, 0 ))*- 1 AS currentYear,
	SUM(
	IF
	( documentDate >= "'.$beginingDateLast.'" AND documentDate <= "'.$endDateLast.'", companyReportingAmount, 0 ))*- 1 AS lastYear,
	DecimalPlaces
	
FROM
	`srp_erp_generalledger`
	INNER JOIN `srp_erp_chartofaccounts` ON `srp_erp_generalledger`.`GLAutoID` = `srp_erp_chartofaccounts`.`GLAutoID` 
	AND `srp_erp_chartofaccounts`.`masterCategory` = "PL" 
	AND `srp_erp_chartofaccounts`.`companyID` = '.$this->common_data['company_data']['company_id'].'
	LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_generalledger`.`partyAutoID`
	LEFT JOIN `srp_erp_currencymaster` ON `srp_erp_currencymaster`.`currencyID` = `srp_erp_generalledger`.`companyReportingCurrencyID` 
WHERE
	`srp_erp_chartofaccounts`.`accountCategoryTypeID` = 11 
	AND `srp_erp_generalledger`.`companyID` = '.$this->common_data['company_data']['company_id'].'
	AND `srp_erp_generalledger`.`partyType` = "CUS" 

GROUP BY
	`srp_erp_generalledger`.`partyAutoID` 
	HAVING 
	( currentYear + lastYear ) != 0 
ORDER BY
	`customerName` ASC ) t1 ')
            
            ->edit_column('customerName', '<div class="text-blue"> <b>$1</b></div>', 'customerName')
            ->edit_column('currentYear', '<div class="text-right"> <b>$1</b></div>', 'dashboard_format_number(currentYear,DecimalPlaces)')
            ->edit_column('lastYear', '<div class="text-right"> <b>$1</b></div>', 'dashboard_format_number(lastYear,DecimalPlaces)')
            ->add_column('currentYearPercentage', '<div class="text-right"> <b>$1</b></div>', 'amount_percentage(' . $data["currentYear"] . ',currentYear)')
            ->add_column('lastYearPercentage', '<div class="text-right"> <b>$1</b></div>', 'amount_percentage(' . $data["lastYear"] . ',lastYear)');
            
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    Function load_new_members_list_view()
    {
        $data["newmembers"] = $this->Finance_dashboard_model->getNewMembers();
        $this->load->view('system/erp_ajax_load_new_members_list_view', $data);
    }

    Function load_remaining_members_list_view()
    {
        $pageId = $this->input->post("pageId");
        $data["newmembers"] = $this->Finance_dashboard_model->getRestNewMembers($pageId);
        $this->load->view('system/erp_ajax_load_new_members_list_view', $data);
    }

    function fetch_quotation()
    {
        $this->datatables->select('DecimalPlaces,srp_erp_customermaster.customerName as customerName,customerAutoID,(contractdetailtbl.transactionamtcontractdetail) as conractCompanyReportingAmount,(IFNULL( SUM( pvd.companyReportingAmount ), 0 ) + IFNULL( SUM( doDet.companyReportingAmount ), 0 )) as invoiceCompanyReportingAmount,((contractdetailtbl.transactionamtcontractdetail)- (IFNULL( SUM( pvd.companyReportingAmount ), 0 ) + IFNULL( SUM( doDet.companyReportingAmount ), 0 ))) as balanceAmount', false)
            ->from('srp_erp_contractmaster')
            ->join('srp_erp_customermaster', 'srp_erp_contractmaster.customerID = srp_erp_customermaster.customerAutoID', 'LEFT')
            ->join("(SELECT SUM(srp_erp_customerinvoicedetails.companyReportingAmount) as companyReportingAmount,srp_erp_customerinvoicedetails.contractAutoID
	FROM
		srp_erp_customerinvoicedetails
		INNER JOIN `srp_erp_customerinvoicemaster` ON `srp_erp_customerinvoicedetails`.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerinvoicedetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.invoiceType = 'Quotation'  GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID,srp_erp_customerinvoicedetails.contractAutoID) pvd", 'pvd.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join("(SELECT SUM( srp_erp_deliveryorderdetails.companyReportingAmount ) AS companyReportingAmount, srp_erp_deliveryorderdetails.contractAutoID 
	FROM srp_erp_deliveryorderdetails
	INNER JOIN `srp_erp_deliveryorder` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `srp_erp_deliveryorder`.`DOAutoID` AND `srp_erp_deliveryorder`.`approvedYN` = 1 
	WHERE `srp_erp_deliveryorderdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_deliveryorder.DOType = 'Quotation'  GROUP BY srp_erp_deliveryorderdetails.DOAutoID, srp_erp_deliveryorderdetails.contractAutoID) doDet", 'doDet.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_contractmaster.companyReportingCurrencyID', 'LEFT')
            ->join("(SELECT (SUM(srp_erp_contractdetails.transactionAmount))/companyReportingExchangeRate as transactionamtcontractdetail,srp_erp_contractdetails.contractAutoID from srp_erp_contractdetails
LEFT JOIN srp_erp_contractmaster contractmaster on srp_erp_contractdetails.contractAutoID = contractmaster.contractAutoID 

	where
contractmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
 AND `contractmaster`.`documentID` = 'QUT' 
	AND `contractmaster`.`approvedYN` = 1 


GROUP BY customerID) contractdetailtbl", 'contractdetailtbl.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->where('srp_erp_contractmaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_contractmaster.documentID', 'QUT')
            ->group_by('srp_erp_contractmaster.customerID')
            ->where('srp_erp_contractmaster.approvedYN', 1)
            ->edit_column('conractCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(conractCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('invoiceCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(invoiceCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('balanceAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(balanceAmount,DecimalPlaces)');
        echo $this->datatables->generate();
    }

    function fetch_contract()
    {
        $this->datatables->select('DecimalPlaces,srp_erp_customermaster.customerName as customerName,customerAutoID,(contractdetailtbl.transactionamtcontractdetail) as conractCompanyReportingAmount,(IFNULL(SUM(pvd.companyReportingAmount), 0) + IFNULL(SUM( doDet.companyReportingAmount ), 0)) as invoiceCompanyReportingAmount,((contractdetailtbl.transactionamtcontractdetail)-(IFNULL(SUM(pvd.companyReportingAmount),0) + IFNULL( SUM( doDet.companyReportingAmount ), 0 ))) as balanceAmount', false)
            ->from('srp_erp_contractmaster')
            ->join('srp_erp_customermaster', 'srp_erp_contractmaster.customerID = srp_erp_customermaster.customerAutoID', 'LEFT')
            ->join("(SELECT SUM(srp_erp_customerinvoicedetails.companyReportingAmount) as companyReportingAmount,srp_erp_customerinvoicedetails.contractAutoID
	FROM
		srp_erp_customerinvoicedetails
		INNER JOIN `srp_erp_customerinvoicemaster` ON `srp_erp_customerinvoicedetails`.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerinvoicedetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.invoiceType = 'Contract' GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID,srp_erp_customerinvoicedetails.contractAutoID) pvd", 'pvd.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join("(SELECT
		SUM( srp_erp_deliveryorderdetails.companyReportingAmount ) AS companyReportingAmount,
		srp_erp_deliveryorderdetails.contractAutoID 
	FROM
		srp_erp_deliveryorderdetails
		INNER JOIN `srp_erp_deliveryorder` ON `srp_erp_deliveryorder`.DOAutoID = srp_erp_deliveryorderdetails.DOAutoID
		AND srp_erp_deliveryorder.`approvedYN` = 1 
	WHERE
		`srp_erp_deliveryorderdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_deliveryorder.DOType = 'Contract' GROUP BY srp_erp_deliveryorderdetails.DOAutoID,srp_erp_deliveryorderdetails.contractAutoID) doDet", 'doDet.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_contractmaster.companyReportingCurrencyID', 'LEFT')
            ->where('srp_erp_contractmaster.companyID', $this->common_data['company_data']['company_id'])
            ->join("(SELECT (SUM(srp_erp_contractdetails.transactionAmount))/companyReportingExchangeRate as transactionamtcontractdetail,srp_erp_contractdetails.contractAutoID from srp_erp_contractdetails
LEFT JOIN srp_erp_contractmaster contractmaster on srp_erp_contractdetails.contractAutoID = contractmaster.contractAutoID 

	where
contractmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
 AND `contractmaster`.`documentID` = 'CNT' 
	AND `contractmaster`.`approvedYN` = 1 


GROUP BY customerID) contractdetailtbl", 'contractdetailtbl.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->where('srp_erp_contractmaster.documentID', 'CNT')
            ->where('srp_erp_contractmaster.approvedYN', 1)
            ->group_by('srp_erp_contractmaster.customerID')
            ->edit_column('conractCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(conractCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('invoiceCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(invoiceCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('balanceAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(balanceAmount,DecimalPlaces)');
        echo $this->datatables->generate();
    }

    function fetch_sales_order()
    {

        $this->datatables->select('DecimalPlaces,srp_erp_customermaster.customerName as customerName,customerAutoID,(contractdetailtbl.transactionamtcontractdetail) AS conractCompanyReportingAmount,(IFNULL(SUM(pvd.companyReportingAmount),0) + IFNULL( SUM( doDet.companyReportingAmount ), 0 )) as invoiceCompanyReportingAmount,((contractdetailtbl.transactionamtcontractdetail)-(IFNULL(SUM(pvd.companyReportingAmount),0) + IFNULL( SUM( doDet.companyReportingAmount ), 0 ))) as balanceAmount', false)
            ->from('srp_erp_contractmaster')
            ->join('srp_erp_customermaster', 'srp_erp_contractmaster.customerID = srp_erp_customermaster.customerAutoID', 'LEFT')
            ->join("(SELECT SUM(srp_erp_customerinvoicedetails.companyReportingAmount) as companyReportingAmount,srp_erp_customerinvoicedetails.contractAutoID
	FROM
		srp_erp_customerinvoicedetails
		INNER JOIN `srp_erp_customerinvoicemaster` ON `srp_erp_customerinvoicedetails`.`invoiceAutoID` = `srp_erp_customerinvoicemaster`.`invoiceAutoID` AND `srp_erp_customerinvoicemaster`.`approvedYN` = 1
	WHERE
		`srp_erp_customerinvoicedetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_customerinvoicemaster.invoiceType = 'Sales Order'  GROUP BY srp_erp_customerinvoicedetails.invoiceAutoID,srp_erp_customerinvoicedetails.contractAutoID) pvd", 'pvd.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join("(SELECT SUM( srp_erp_deliveryorderdetails.companyReportingAmount ) AS companyReportingAmount,srp_erp_deliveryorderdetails.contractAutoID 
	FROM srp_erp_deliveryorderdetails
		INNER JOIN `srp_erp_deliveryorder` ON `srp_erp_deliveryorderdetails`.`DOAutoID` = `srp_erp_deliveryorder`.`DOAutoID` AND `srp_erp_deliveryorder`.`approvedYN` = 1 
	WHERE `srp_erp_deliveryorderdetails`.`companyID` = " . $this->common_data['company_data']['company_id'] . " AND srp_erp_deliveryorder.DOType = 'Sales Order' GROUP BY srp_erp_deliveryorderdetails.DOAutoID, srp_erp_deliveryorderdetails.contractAutoID) doDet", 'doDet.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join("(SELECT (SUM(srp_erp_contractdetails.transactionAmount))/companyReportingExchangeRate as transactionamtcontractdetail,srp_erp_contractdetails.contractAutoID from srp_erp_contractdetails
LEFT JOIN srp_erp_contractmaster contractmaster on srp_erp_contractdetails.contractAutoID = contractmaster.contractAutoID 

	where
contractmaster.companyID = " . $this->common_data['company_data']['company_id'] . "
 AND `contractmaster`.`documentID` = 'SO' 
	AND `contractmaster`.`approvedYN` = 1 


GROUP BY customerID) contractdetailtbl", 'contractdetailtbl.contractAutoID = srp_erp_contractmaster.contractAutoID', 'LEFT')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_contractmaster.companyReportingCurrencyID', 'LEFT')
            ->where('srp_erp_contractmaster.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_contractmaster.documentID', 'SO')
            ->where('srp_erp_contractmaster.approvedYN', 1)
            ->group_by('srp_erp_contractmaster.customerID')
            ->edit_column('conractCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(conractCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('invoiceCompanyReportingAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(invoiceCompanyReportingAmount,DecimalPlaces)')
            ->edit_column('balanceAmount', '<div class="text-right" style=" cursor: pointer;" onclick=""> <b>$1</b></div>', 'dashboard_format_number(balanceAmount,DecimalPlaces)');
        echo $this->datatables->generate();
    }

    function load_customer_order_analysis()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_ajax_load_customer_order_analysis', $data);
    }

    Function load_head_count()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        //$data["headcount"] = $this->Finance_dashboard_model->get_head_count();
        $this->load->view('system/erp_ajax_load_head_count', $data);
    }

    Function load_head_count_view()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["financeyearid"] = $this->input->post("financeyearid");
        //$data["headcount"] = $this->Finance_dashboard_model->get_head_count();
        $this->load->view('system/load_head_count_view', $data);
    }

    Function load_Designation_head_count()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        //$data["headcount"] = $this->Finance_dashboard_model->get_Designation_head_count();
        $this->load->view('system/erp_ajax_load_Designation_head_count', $data);
    }

    Function load_payroll_cost()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["financeYear"] = $this->common_data["company_data"]["companyFinanceYearID"];
        $this->load->view('system/erp_ajax_load_payroll_cost', $data);
    }

    Function load_payroll_cost_view()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $financeYear = $this->input->post("financeyearid");
        $financeYear = (!empty($financeYear)) ? $financeYear : $this->common_data["company_data"]["companyFinanceYearID"];
        $financeData = $this->db->query("SELECT beginingDate, endingDate FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID={$financeYear}")->row_array();

        $data["months"] = get_month_list_from_date($financeData['beginingDate'], $financeData['endingDate'], "Y-m", "1 month", 'MY');

        $data["financeYear"] = $financeYear;
        $this->load->view('system/erp_ajax_load_payroll_cost_view', $data);
    }

    Function load_revenue_detail_analysis_by_segment()
    {
        $beginingDate = "";
        $endDate = "";
        $period = $this->input->post("period");
        $company_type = $this->session->userdata("companyType");
        if($company_type==1) {
            $lastTwoYears = get_last_two_financial_year();
        }else
        {
            $lastTwoYears = get_last_two_financial_year_group();
        }

        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
        }

        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["totalRevenue"] = $this->Finance_dashboard_model->getTotalRevenue($beginingDate, $endDate);
        $data["revenueDetailAnalysisBySegment"] = $this->Finance_dashboard_model->getRevenueDetailAnalysisBySegment($beginingDate, $endDate);
        $this->load->view('system/erp_ajax_revenue_detail_analysis_by_segment', $data);
    }

    function birthdayReminder()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp-birthday-reminder', $data);
    }

    function contractReminder(){
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp-contract-reminder', $data);
    }

    function birthdayReminder_view()
    {
        $days = $this->input->post('days');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $nextYear = date('Y') + 1;
        $toDay = date('Y-m-d');
        $companyID = current_companyID();

        $employeeList = $this->db->query("SELECT EIdNo, Ename2, desTB.DesDescription, segTB.description, EDOB,
                            DATEDIFF( IF(
                                (DATE_FORMAT(EDOB, '%m') < {$currentMonth}), DATE_FORMAT(EDOB, '{$nextYear}-%m-%d'), DATE_FORMAT(EDOB, '{$currentYear}-%m-%d')
                            ), '{$toDay}' ) AS diff
                            FROM srp_employeesdetails empTB
                            JOIN srp_designation desTB ON desTB.DesignationID = empTB.EmpDesignationId
                            JOIN srp_erp_segment segTB ON segTB.segmentID = empTB.segmentID
                            WHERE empTB.Erp_companyID = {$companyID} AND isDischarged = 0
                            HAVING diff BETWEEN 0 AND {$days}
                            ORDER BY diff")->result_array();

        $data['employeeList'] = $employeeList;
        $this->load->view('system/erp-birthday-reminder-view', $data);
    }

    function contractReminder_view(){

        $days = $this->input->post('days');

        if(empty($days)){
            $days = 7;
        }

        $currentMonth = date('m');
        $currentYear = date('Y');
        $nextYear = date('Y') + 1;
        $toDay = date('Y-m-d');
        $companyID = current_companyID();

        $employeeList = $this->db->query("SELECT contractm.customerName, contractm.referenceNo, contractm.contractCode, contractm.contractExpDate,contractm.documentID, contractm.contractAutoID,
                            DATEDIFF( IF(
                                (DATE_FORMAT(contractm.contractExpDate, '%m') < {$currentMonth}), DATE_FORMAT(contractm.contractExpDate, '{$nextYear}-%m-%d'), DATE_FORMAT(contractm.contractExpDate, '{$currentYear}-%m-%d')
                            ), '{$toDay}' ) AS diff
                            FROM srp_erp_contractmaster as contractm
                            WHERE contractm.companyID = {$companyID} AND contractm.approvedYN = 1 AND contractm.contractExpDate > {$toDay}
                            HAVING diff BETWEEN 0 AND {$days}
                            ORDER BY diff")->result_array();

        $data['employeeList'] = $employeeList;
        $this->load->view('system/erp-contract-reminder-view', $data);

    }

    function updatePBLink()
    {
        echo json_encode($this->Finance_dashboard_model->updatePBLink());
    }

    function updateLinkDescription()
    {
        echo json_encode($this->Finance_dashboard_model->updateLinkDescription());
    }


    function fetch_overdue_payables_frmgl()
    {
        $fields = "";
        $join = "";
        $userDashboardID = $this->input->post("userDashboardID");
        if ($this->input->post("currency") == 'transactionAmount') {
            $join = 'gdl.transactionCurrencyID';
            $fields = 'sum(transactionAmount)*-1 as amount,sum(transactionAmount)*-1 as srchamount,';


        } else if ($this->input->post("currency") == 'companyReportingAmount') {
            $join = 'gdl.companyReportingCurrencyID';
            $fields = 'sum(companyReportingAmount)*-1 as amount,sum(companyReportingAmount)*-1 as srchamount,';
        }
        $this->datatables->select($fields . 'sm.supplierName as supplierName,sm.supplierAutoID as supplierAutoID,cm.currencycode as currency,cm.DecimalPlaces as decimalPlace', false)
            ->from('srp_erp_generalledger gdl')
            ->join('srp_erp_suppliermaster sm', 'gdl.partyAutoID = sm.supplierAutoID', 'LEFT')
            ->join('srp_erp_currencymaster cm', $join . ' = cm.currencyID', 'LEFT')
            ->where('gdl.companyID', $this->common_data['company_data']['company_id'])
            ->where('gdl.subLedgerType', 2)
            ->where('gdl.documentDate <=', current_date())
            ->group_by('gdl.partyAutoID')
            ->group_by($join)
            ->edit_column('supplierName', '<div style=" cursor: pointer;" onclick="dashboardOverduePayables' . $userDashboardID . '($2,\'$3\')">$1</div>', 'supplierName,supplierAutoID,currency')
            ->edit_column('amount', '<div class="text-right" style=" cursor: pointer;" onclick="dashboardOverduePayables' . $userDashboardID . '($2,\'$3\')"><b>$1</b></div>', 'dashboard_format_number(amount,decimalPlace),supplierAutoID,currency');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    function fetch_overdue_receivable_frmgl()
    {
        $fields = "";
        $join = "";
        $userDashboardID = $this->input->post("userDashboardID");
        if ($this->input->post("currency") == 'transactionAmount') {
            $join = 'gdl.transactionCurrencyID';
            $fields = 'sum(transactionAmount) as amount,sum(transactionAmount) as srchamount,';


        } else if ($this->input->post("currency") == 'companyReportingAmount') {
            $join = 'gdl.companyReportingCurrencyID';
            $fields = 'sum(companyReportingAmount) as amount,sum(companyReportingAmount) as srchamount,';
        }
        $this->datatables->select($fields . 'sm.customerName as customerName,sm.customerAutoID as customerAutoID,cm.currencycode as currency,cm.DecimalPlaces as decimalPlace', false)
            ->from('srp_erp_generalledger gdl')
            ->join('srp_erp_customermaster sm', 'gdl.partyAutoID = sm.customerAutoID', 'LEFT')
            ->join('srp_erp_currencymaster cm', $join . ' = cm.currencyID', 'LEFT')
            ->where('gdl.companyID', $this->common_data['company_data']['company_id'])
            ->where('gdl.subLedgerType', 3)
            ->where('gdl.documentDate <=', current_date())
            ->group_by('gdl.partyAutoID')
            ->group_by($join)
            ->edit_column('customerName', '<div style=" cursor: pointer;" onclick="dashboardOverdueReceivables' . $userDashboardID . '($2,\'$3\')">$1</div>', 'customerName,customerAutoID,currency')
            ->edit_column('amount', '<div class="text-right" style=" cursor: pointer;" onclick="dashboardOverdueReceivables' . $userDashboardID . '($2,\'$3\')"> <b>$1</b></div>', 'dashboard_format_number(amount,decimalPlace),customerAutoID,currency');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }

    Function loadDashboard_groupmonitoring()
    {
        $data = array();
        $result = $this->Finance_dashboard_model->getAssignedDashboard();
        $data["dashboardTab"] = $result["dashboard"];
        $this->load->view('system/system_dashboard_groupmonitoring', $data);
    }

    Function load_group_reporting()
    {
        $date = $this->input->post('Year');
        $this->form_validation->set_rules('companyID[]', 'Company', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo ' 
            <br>
            <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
            </div>';

        } else if (empty($date)) {
            echo '
                <br>
             <div class="alert alert-warning" role="alert">
                Please select a year
            </div>';
        } else {
            $startdate = "01-01-$date";
            $enddate = "31-12-$date";
            $data["output"] = $this->Finance_dashboard_model->monthlyincomestatement();
            $data["output_chart"] = $this->Finance_dashboard_model->monthlyincomestatement_chart();
            $data["month"] = get_month_list_from_date(format_date($startdate), format_date($enddate), "Y-m", "1 month");
            $data["months"] = get_month_list_from_date($startdate, $enddate, "Y-m", "1 month", 'M');
            $data["months2"] = get_month_list_from_date($startdate, $enddate, "Y-m", "1 month", 'My');
            $data["balancesheet_rpt"] = $this->Finance_dashboard_model->balancesheet_rpt();
            $data["localization_pie"] = $this->Finance_dashboard_model->emp_localization();
            $data["localization_country"] = $this->Finance_dashboard_model->localaization_country();
            $data['companyidgroup'] = $this->input->post('companyID');
            $data['date'] = $this->input->post('Year');
            $data['test'] = 'Test';
            $this->load->view('system/erp_ajax_load_group_monitoring', $data);
        }

    }

    function sync_details_group_rpt()
    {
        echo json_encode($this->Finance_dashboard_model->sync_details_group_rpt());
    }

    function load_last_update()
    {
        echo json_encode($this->Finance_dashboard_model->load_last_update());
    }

    function groupmonitoringdashboard()
    {
        $date = $this->input->post('year');
        $startdate = "01-01-$date";
        $enddate = "31-12-$date";
        $data["outputdrilldown"] = $this->Finance_dashboard_model->groupmonitoringdashboard();
        $data["month"] = get_month_list_from_date(format_date($startdate), format_date($enddate), "Y-m", "1 month");
        $this->load->view('system/group_monitoring_drilldown', $data);
    }

    function groupmonitoringdashboard_balancesheet()
    {
        $data['date'] = $this->input->post('year');
        $data["outputdrilldown"] = $this->Finance_dashboard_model->groupmonitoringdashboardblancesheet();
        $this->load->view('system/group_monitoring_balancesheet_drilldown', $data);
    }

    function emplocalizationdrilldown()
    {
        $data["outputdrilldown"] = $this->Finance_dashboard_model->group_monitoring_emplocal();
        $this->load->view('system/group_monitoring_emplocalization', $data);
    }

    function load_MPR()
    {
        $data['userDashboardID'] = $this->input->post("userDashboardID");
        $period = $this->input->post("period");
        $companyType = $this->session->userdata("companyType");
        if ($companyType == 1) {

            $lastTwoYears = get_last_two_financial_year();

        } else {

            $lastTwoYears = get_last_two_financial_year_group();
        }
        if (empty($lastTwoYears)) {
            die('Selected Finance year not found');
        }
        $start_date = $lastTwoYears[$period]["beginingDate"];
        $end_date = $lastTwoYears[$period]["endingDate"];
        $company_id = current_companyID();
        if ($companyType == 1) {
            $template_data = $this->db->query("SELECT companyReportTemplateID, description FROM srp_erp_companyreporttemplate 
                                           WHERE templateType = 2 AND companyID = {$company_id}  AND companyType = 1 ")->result_array();
        } else {
            $template_data = $this->db->query("SELECT companyReportTemplateID, description FROM srp_erp_companyreporttemplate 
                                           WHERE templateType = 2 AND companyID = {$company_id}  AND companyType = 2 ")->result_array();
        }


        $template_arr = [];
        if (isset($template_data)) {
            foreach ($template_data as $row) {
                $template_arr[$row['companyReportTemplateID']] = $row['description'];
            }
        }else
        {
            $template_arr =  array('' => 'Select Financial Year');
        }
        if ($companyType == 1) {
            $period_data = $this->db->query("SELECT companyFinancePeriodID, dateFrom, dateTo FROM srp_erp_companyfinanceperiod AS fn_per
                            JOIN srp_erp_companyfinanceyear AS fn_yr ON fn_yr.companyFinanceYearID = fn_per.companyFinanceYearID
                            WHERE fn_per.dateFrom BETWEEN '{$start_date}' AND '{$end_date}' AND fn_yr.companyID = {$company_id}")->result_array();
        } else {
            $period_data = $this->db->query("SELECT groupFinancePeriodID, dateFrom, dateTo FROM srp_erp_groupfinanceperiod AS fn_per JOIN srp_erp_groupfinanceyear AS fn_yr ON fn_yr.groupFinanceYearID = fn_per.groupFinanceYearID WHERE fn_per.dateFrom BETWEEN '{$start_date}' AND '{$end_date}' AND fn_yr.groupID = {$company_id}")->result_array();
        }


        $period_arr = [];
        $current_month = 0;
        if (!empty($period_data)) {
            foreach ($period_data as $row) {
                if (date('Y-m-01') == date('Y-m-01', strtotime($row['dateFrom']))) {
                    if ($companyType == 1) {
                        $current_month = $row['companyFinancePeriodID'];
                    } else {
                        $current_month = $row['groupFinancePeriodID'];
                    }
                }
                $month = date('F - Y', strtotime($row['dateFrom']));
                if ($companyType == 1) {
                    $period_arr[$row['companyFinancePeriodID']] = $month;
                } else {
                    $period_arr[$row['groupFinancePeriodID']] = $month;
                }
            }
        }

        $data['template_arr'] = $template_arr;
        $data['period_arr'] = $period_arr;
        $data['current_month'] = $current_month;
        $this->load->view('system/mpr_widget', $data);
    }

    function load_mpr_view()
    {
        $period = $this->input->post('periods_mpr');
        $temMasterID = $this->input->post('mpr_template');
        $companyType = $this->session->userdata("companyType");
        $companyIDTemp = current_companyID();
        $comp = '';
        $uncateInex = 0;
        $fieldname = '';
        if ($companyType == 1) {
            $period_data = $this->db->query("SELECT companyFinancePeriodID, dateFrom, dateTo FROM srp_erp_companyfinanceperiod 
                                         WHERE companyFinancePeriodID = {$period}")->row_array();
        } else {
            $period_data = $this->db->query("SELECT groupFinancePeriodID, dateFrom, dateTo FROM srp_erp_groupfinanceperiod 
                                         WHERE groupFinancePeriodID = {$period}")->row_array();
        }

        $master_data = $this->db->query("SELECT templateType, companyType, companyID FROM srp_erp_companyreporttemplate WHERE companyReportTemplateID = {$temMasterID}")->row_array();
        $companyID = $master_data['companyID'];

        $gross_rows = $this->db->query("SELECT detID FROM srp_erp_companyreporttemplatedetails WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND is_gross_rev = 1")->row('detID');
        /* if (empty($gross_rows)) {
            die(json_encode(['e', 'Please configure the Gross column and try again']));
        }*/

        $confirmationTemplate = $this->db->query("SELECT companyReportTemplateID FROM `srp_erp_companyreporttemplate` where companyID = {$companyIDTemp} AND confirmedYN = 1 ")->row_array();

        $tempDetIDPLE = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` where companyID = {$companyIDTemp} AND companyReportTemplateID = {$temMasterID} AND defaultType = 2")->row_array();
        $tempDetIDPLI = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` where companyID = {$companyIDTemp} AND companyReportTemplateID = {$temMasterID} AND defaultType = 1")->row_array();

        if ($companyType != 1) {

            $uncategorizedincome = $this->db->query("SELECT GLAutoID as groupchartofaccountmasterID FROM `srp_erp_groupchartofaccounts` where groupID = {$companyIDTemp} AND accountDefaultType = 2 AND GLAutoID NOT IN (SELECT chartofAccountID from srp_erp_groupchartofaccountdetails)")->row_array();
            $uncategorizedexpenses = $this->db->query("SELECT GLAutoID as groupchartofaccountmasterID FROM `srp_erp_groupchartofaccounts` where groupID = {$companyIDTemp} AND accountDefaultType = 3 AND GLAutoID NOT IN (SELECT chartofAccountID from srp_erp_groupchartofaccountdetails)")->row_array();

            if(empty($uncategorizedincome)&&(empty($uncategorizedexpenses)))
            {
                $uncateInex = 1;
                $fieldname = 'Uncategorized Income Chart of Account not created for this group <br> Uncategorized Expenses Chart of Account not created for this group';
            }
            else if(empty($uncategorizedincome))
            {

                $uncateInex = 1;
                $fieldname = 'Uncategorized Income Chart of Account not created for this group';
            }
            else if(empty($uncategorizedexpenses))
            {

                $uncateInex = 1;
                $fieldname = 'Uncategorized Expenses Chart of Account not created for this group';
            }

            if(!empty($uncategorizedincome)&&(!empty($uncategorizedexpenses)))
            {

                $createdPCID = $this->common_data['current_pc'];
                $createdUserID = $this->common_data['current_userID'];
                $createdUserName = $this->common_data['current_user'];
                $createdDateTime = $this->common_data['current_date'];

                $this->db->query("INSERT INTO srp_erp_groupchartofaccountdetails (groupChartofAccountMasterID,chartofAccountID,companyID,companyGroupID,createdPCID,createdUserID,createdUserName,createdDateTime)
SELECT
	{$uncategorizedincome['groupchartofaccountmasterID']} AS groupChartofAccountMasterID,
	GLAutoID,
	companyID,
	{$companyIDTemp} AS companygroupID,
    '{$createdPCID}' as createdPCID,
	{$createdUserID} as createdUserID,
	'{$createdUserName}' as createdUserName,
	'{$createdDateTime}' as createdDateTime
	
FROM
	srp_erp_chartofaccounts 
WHERE
	companyID IN ( SELECT companyID FROM srp_erp_companygroupdetails WHERE companygroupID = {$companyIDTemp} ) 
	AND subCategory = 'PLI' 
	AND GLAutoID NOT IN ( SELECT chartofAccountID FROM `srp_erp_groupchartofaccountdetails`) ");


                $this->db->query("INSERT INTO srp_erp_groupchartofaccountdetails (groupChartofAccountMasterID,chartofAccountID,companyID,companyGroupID,createdPCID,createdUserID,createdUserName,createdDateTime)
SELECT
	{$uncategorizedexpenses['groupchartofaccountmasterID']} AS groupChartofAccountMasterID,
	GLAutoID,
	companyID,
	{$companyIDTemp} AS companygroupID,
    '{$createdPCID}' as createdPCID,
	{$createdUserID} as createdUserID,
	'{$createdUserName}' as createdUserName,
	'{$createdDateTime}' as createdDateTime
FROM
	srp_erp_chartofaccounts 
WHERE
	companyID IN ( SELECT companyID FROM srp_erp_companygroupdetails WHERE companygroupID = {$companyIDTemp} ) 
	AND subCategory = 'PLE' 
	AND GLAutoID NOT IN ( SELECT chartofAccountID FROM `srp_erp_groupchartofaccountdetails`) ");
            }

        }



        if (!empty($confirmationTemplate)) {
            if ($companyType == 1) {
                /*************Uncategorized Expense Start*************/
                $this->db->query("INSERT INTO srp_erp_companyreporttemplatelinks (templateMasterID,templateDetailID,sortOrder,glAutoID,companyID)
SELECT
$temMasterID as templateMasterID,
{$tempDetIDPLE['detID']} as templateDetailID,
 @n := @n +1 as sortOrder,GLAutoID  as glAutoID,
 $companyIDTemp as companyID
FROM
	`srp_erp_chartofaccounts` , (SELECT @n := 0) m
WHERE
	companyID = {$companyIDTemp} 
	AND subCategory = 'PLE' 
	AND masterAccountYN = 0 
	AND GLAutoID NOT IN ( SELECT glAutoID FROM `srp_erp_companyreporttemplatelinks` WHERE companyID = {$companyIDTemp} AND templateMasterID = {$temMasterID} AND glAutoID IS NOT NULL )");
                /**************Uncategorized Expense End*************/


                /*************Uncategorized Income Start*************/
                $this->db->query("INSERT INTO srp_erp_companyreporttemplatelinks (templateMasterID,templateDetailID,sortOrder,glAutoID,companyID)
SELECT
$temMasterID as templateMasterID,
{$tempDetIDPLI['detID']} as templateDetailID,
 @n := @n +1 as sortOrder,GLAutoID  as glAutoID,
 $companyIDTemp as companyID
FROM
	`srp_erp_chartofaccounts` , (SELECT @n := 0) m
WHERE
	companyID = {$companyIDTemp} 
	AND subCategory = 'PLI' 
	AND masterAccountYN = 0 
	AND GLAutoID NOT IN ( SELECT glAutoID FROM `srp_erp_companyreporttemplatelinks` WHERE companyID = {$companyIDTemp} AND templateMasterID = {$temMasterID} AND glAutoID IS NOT NULL )");
                /**************Uncategorized Income End*************/
            }else
            {
                /*************Uncategorized Expense Start Group*************/
                $this->db->query("INSERT INTO srp_erp_companyreporttemplatelinks (templateMasterID,templateDetailID,sortOrder,glAutoID,companyID)
SELECT
$temMasterID as templateMasterID,
{$tempDetIDPLE['detID']} as templateDetailID,
 @n := @n +1 as sortOrder,GLAutoID  as glAutoID,
 $companyIDTemp as companyID
FROM
	`srp_erp_groupchartofaccounts` , (SELECT @n := 0) m
WHERE
	groupID = {$companyIDTemp} 
	AND subCategory = 'PLE' 
	AND masterAccountYN = 0 
	AND GLAutoID NOT IN ( SELECT glAutoID FROM `srp_erp_companyreporttemplatelinks` WHERE companyID = {$companyIDTemp} AND templateMasterID = {$temMasterID} AND glAutoID IS NOT NULL )");
                /**************Uncategorized Expense End Group*************/


                /*************Uncategorized Income Start Group*************/
                $this->db->query("INSERT INTO srp_erp_companyreporttemplatelinks (templateMasterID,templateDetailID,sortOrder,glAutoID,companyID)
SELECT
$temMasterID as templateMasterID,
{$tempDetIDPLI['detID']} as templateDetailID,
 @n := @n +1 as sortOrder,GLAutoID  as glAutoID,
 $companyIDTemp as companyID
FROM
	`srp_erp_groupchartofaccounts` , (SELECT @n := 0) m
WHERE
	groupID = {$companyIDTemp} 
	AND subCategory = 'PLI' 
	AND masterAccountYN = 0 
	AND GLAutoID NOT IN ( SELECT glAutoID FROM `srp_erp_companyreporttemplatelinks` WHERE companyID = {$companyIDTemp} AND templateMasterID = {$temMasterID} AND glAutoID IS NOT NULL )");
                /**************Uncategorized Income End Group*************/

            }

        }

        $rpt_curr = $this->common_data['company_data']['company_reporting_currencyID'];
        $dPlace = fetch_currency_desimal_by_id($rpt_curr);

        $start_date = $period_data["dateFrom"];
        $end_date = $period_data["dateTo"];

        $file_name = 'MPR - ' . date('M - Y', strtotime($start_date)) . '.xls';

        $periods['last_month'] = [
            'start' => date('Y-m-d', strtotime("$start_date -1 month")),
            'end' => date('Y-m-t', strtotime("$start_date -1 month")),
            'title' => date('M-Y', strtotime("$start_date -1 month")),
        ];

        $periods['last_month_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'last_month'
        ];

        $periods['selected_month'] = [
            'start' => date('Y-m-d', strtotime($start_date)),
            'end' => date('Y-m-t', strtotime($end_date)),
            'title' => date('M-Y', strtotime($start_date)),
        ];

        $periods['selected_month_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'selected_month'
        ];

        $periods['selected_month_last_month_rev'] = [
            'title' => '%LM',
        ];

        $periods['AYTD'] = [
            'start' => date('Y-01-01', strtotime($start_date)),
            'end' => date('Y-m-t', strtotime($end_date)),
            'title' => 'AYTD',
        ];

        $periods['AYTD_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'AYTD',
        ];

        $periods['LYTD'] = [
            'start' => date('Y-01-01', strtotime("$start_date -1 year")),
            'end' => date('Y-m-t', strtotime($end_date . " -1 year")),
            'title' => 'LYTD',
        ];

        $periods['LYTD_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'LYTD'
        ];

        $periods['AYTD_LYTD'] = [
            'title' => 'AYTD&nbsp;–&nbsp;LYTD',
        ];

        $periods['Var'] = [
            'title' => '%-Var',
        ];

        $percentage_cols = ['last_month_rev', 'selected_month_rev', 'selected_month_last_month_rev', 'AYTD_rev', 'LYTD_rev', 'AYTD_LYTD', 'Var'];


        $this->db->select('detID, description, itemType, sortOrder')->from('srp_erp_companyreporttemplatedetails')
            ->where('companyReportTemplateID', $temMasterID)->where('masterID IS NULL')->where('companyID', $companyID)->order_by('sortOrder');
        $temp_data = $this->db->get()->result_array();

        $rpt_data = [];
        $sub_data = [];
        $gross_val = [];

        foreach ($temp_data as $row) {
            $templateID = $row['detID'];

            if ($row['itemType'] == 2) {
                $rpt_data[] = ['des' => $row['description'], 'type' => 'header'];

                $subData = $this->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID} ORDER BY sortOrder")->result_array();


                foreach ($subData as $sub_row) {
                    $detID = $sub_row['detID'];

                    if ($sub_row['itemType'] == 1) { /*Sub category*/
                        $amount_arr = [];
                        foreach ($periods as $period_key => $period_row) {
                            if (in_array($period_key, $percentage_cols)) {
                                continue;
                            }
                            $start = $period_row['start'];
                            $end = $period_row['end'];
                            if ($companyType == 1) {
                                $amount = $this->db->query("SELECT SUM(gend_tb.companyReportingAmount * -1) AS amount                                      
                                        FROM srp_erp_companyreporttemplatelinks AS temp_link
                                        JOIN srp_erp_generalledger AS gend_tb ON gend_tb.GLAutoID = temp_link.glAutoID
                                        WHERE templateDetailID = {$detID} AND documentDate BETWEEN '{$start}' AND '{$end}'")->row('amount');
                            } else {

                                $CompanyID = '';

                                if (!empty($this->input->post('companygroupfilter'))) {
                                    $comp = implode(',', $this->input->post('companygroupfilter'));
                                    $CompanyID = 'AND ch_grp.companyID IN (' . $comp . ')  ';
                                }

                                $amount = $this->db->query("SELECT SUM(gend_tb.companyReportingAmount * -1) AS amount                                      
                                        FROM (
	SELECT ch_grp.chartofAccountID  as glAutoID, templateDetailID                              
	FROM srp_erp_companyreporttemplatelinks AS temp_link
	JOIN srp_erp_groupchartofaccountdetails AS ch_grp ON ch_grp.groupChartofAccountMasterID = temp_link.glAutoID
	WHERE templateDetailID = {$detID} $CompanyID
) AS temp_link
                                        JOIN srp_erp_generalledger AS gend_tb ON gend_tb.GLAutoID = temp_link.glAutoID
                                        WHERE templateDetailID = {$detID} AND documentDate BETWEEN '{$start}' AND '{$end}'")->row('amount');
                            }

                            $amount_arr[$period_key] = round($amount, $dPlace);
                            $sub_data[$period_key][$detID] = round($amount, $dPlace);
                        }


                        $rpt_data[] = ['des' => $sub_row['description'], 'type' => 'subCategory', 'amount' => $amount_arr, 'row_autoID' => $detID];

                    }

                    if ($sub_row['itemType'] == 3) { /*Group*/

                        $group_glData = $this->db->query("SELECT link.subCategory FROM srp_erp_companyreporttemplatelinks link
                                    JOIN srp_erp_companyreporttemplatedetails det ON det.detID = link.subCategory
                                    WHERE templateDetailID = {$detID} ORDER BY link.sortOrder")->result_array();

                        $amount_arr = [];
                        if (!empty($group_glData)) {
                            foreach ($periods as $period_key => $period_row) {
                                if (in_array($period_key, $percentage_cols)) {
                                    continue;
                                }
                                foreach ($group_glData as $gp_row) {
                                    $id = $gp_row['subCategory'];
                                    $amount = (array_key_exists($id, $sub_data[$period_key])) ? $sub_data[$period_key][$id] : 0;

                                    $amount += (array_key_exists($period_key, $amount_arr)) ? $amount_arr[$period_key] : 0;
                                    $amount_arr[$period_key] = $amount;

                                    $sub_data[$period_key][$detID] = round($amount, $dPlace);

                                    if ($gross_rows == $detID) {
                                        $gross_val[$period_key] = $amount;
                                    }
                                }
                            }
                        } else {
                            foreach ($periods as $period_key => $period_row) {
                                $amount_arr[$period_key] = 0;
                            }
                        }

                        $rpt_data[] = ['des' => $sub_row['description'], 'amount' => $amount_arr, 'type' => 'groupTotal', 'row_autoID' => $detID];
                    }
                }
            } else {
                /*Group*/

                $group_glData = $this->db->query("SELECT link.subCategory FROM srp_erp_companyreporttemplatelinks link
                                    JOIN srp_erp_companyreporttemplatedetails det ON det.detID = link.subCategory
                                    WHERE templateDetailID = {$templateID} ORDER BY link.sortOrder")->result_array();

                $amount_arr = [];
                if (!empty($group_glData)) {
                    foreach ($periods as $period_key => $period_row) {
                        if (in_array($period_key, $percentage_cols)) {
                            continue;
                        }
                        foreach ($group_glData as $gp_row) {
                            $id = $gp_row['subCategory'];
                            $amount = (array_key_exists($id, $sub_data[$period_key])) ? $sub_data[$period_key][$id] : 0;

                            $amount += (array_key_exists($period_key, $amount_arr)) ? $amount_arr[$period_key] : 0;
                            $amount_arr[$period_key] = $amount;

                            if ($gross_rows == $templateID) {
                                $gross_val[$period_key] = $amount;
                            }
                            $sub_data[$period_key][$templateID] = round($amount, $dPlace);
                        }
                    }
                } else {
                    foreach ($periods as $period_key => $period_row) {
                        if (in_array($period_key, $percentage_cols)) {
                            continue;
                        }
                        $amount_arr[$period_key] = 0;

                        $sub_data[$period_key][$templateID] = round($amount, $dPlace);
                    }
                }

                $rpt_data[] = ['des' => $row['description'], 'amount' => $amount_arr, 'type' => 'groupTotal', 'row_autoID' => $templateID];
            }

        }

        $data['gross_rows'] = $gross_rows;
        $data['periods'] = $periods;
        $data['percentage_cols'] = $percentage_cols;
        $data['gross_val'] = $gross_val;
        $data['rpt_data'] = $rpt_data;
        $data['period'] = $period;
        $data['temMasterID'] = $temMasterID;
        $data['Company'] = $comp;
        $data['confirmation'] = $confirmationTemplate;
        $data['uncateInex'] = $uncateInex;
        $data['fieldname'] = $fieldname;


        $data['sub_data'] = $sub_data;
        $data['dPlace'] = $dPlace;

        $view = $this->load->view('system/mpr_widget_view', $data, true);

        echo json_encode(['s', 'view' => $view, 'file_name' => $file_name]);
    }

    function mpr_drilldown()
    {
        $companyID_drilldown = $_POST["ComapnyID"];

        $companyType = $this->session->userdata("companyType");
        $ID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('id') ?? '');
        $period = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('period') ?? '');
        $temMasterID = ($this->uri->segment(5)) ? $this->uri->segment(5) : trim($this->input->post('temMasterID') ?? '');
        $SubName = $this->db->query("SELECT description as drilldownsubdescription FROM srp_erp_companyreporttemplatedetails det WHERE detID  = {$ID} ")->row_array();
        $companyIDSess = current_companyID();

        $CompanyID_filter = '';
        $comp = '';
        if ($companyID_drilldown) {
            $CompanyID_filter = 'AND ch_grp.companyID IN (' . $companyID_drilldown . ')';
        }

        if ($companyType == 1) {
            $period_data = $this->db->query("SELECT companyFinancePeriodID, dateFrom, dateTo FROM srp_erp_companyfinanceperiod 
                                         WHERE companyFinancePeriodID = {$period}")->row_array();
        } else {
            $period_data = $this->db->query("SELECT groupFinancePeriodID, dateFrom, dateTo FROM srp_erp_groupfinanceperiod 
                                         WHERE groupFinancePeriodID = {$period}")->row_array();
        }

        $master_data = $this->db->query("SELECT templateType, companyType, companyID FROM srp_erp_companyreporttemplate WHERE companyReportTemplateID = {$temMasterID}")->row_array();
        $companyID = $master_data['companyID'];

        $gross_rows = $this->db->query("SELECT detID FROM srp_erp_companyreporttemplatedetails WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND is_gross_rev = 1")->row('detID');

        if (empty($gross_rows)) {
            die(json_encode(['e', 'Please configure the Gross column and try again']));
        }

        $rpt_curr = $this->common_data['company_data']['company_reporting_currencyID'];
        $dPlace = fetch_currency_desimal_by_id($rpt_curr);

        $start_date = $period_data["dateFrom"];
        $end_date = $period_data["dateTo"];

        $file_name = 'MPR - ' . date('M - Y', strtotime($start_date)) . '.xls';

        $periods['last_month'] = [
            'start' => date('Y-m-d', strtotime("$start_date -1 month")),
            'end' => date('Y-m-t', strtotime("$start_date -1 month")),
            'title' => date('M-Y', strtotime("$start_date -1 month")),
        ];

        $periods['last_month_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'last_month'
        ];

        $periods['selected_month'] = [
            'start' => date('Y-m-d', strtotime($start_date)),
            'end' => date('Y-m-t', strtotime($end_date)),
            'title' => date('M-Y', strtotime($start_date)),
        ];

        $periods['selected_month_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'selected_month'
        ];

        $periods['selected_month_last_month_rev'] = [
            'title' => '%LM',
        ];

        $periods['AYTD'] = [
            'start' => date('Y-01-01', strtotime($start_date)),
            'end' => date('Y-m-t', strtotime($end_date)),
            'title' => 'AYTD',
        ];

        $periods['AYTD_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'AYTD',
        ];

        $periods['LYTD'] = [
            'start' => date('Y-01-01', strtotime("$start_date -1 year")),
            'end' => date('Y-m-t', strtotime($end_date . " -1 year")),
            'title' => 'LYTD',
        ];

        $periods['LYTD_rev'] = [
            'title' => '%-Rev',
            'calculation' => 'LYTD'
        ];

        $percentage_cols = ['last_month_rev', 'selected_month_rev', 'selected_month_last_month_rev', 'AYTD_rev', 'LYTD_rev'];


        $rpt_data = [];
        $sub_data = [];
        if ($companyType == 1) {
            $drilldownGLdescription = $this->db->query("SELECT
chartofacc.GLDescription as description, 
	companyreportlink.glAutoID as glAutoID
FROM
	srp_erp_companyreporttemplatedetails companyreportdetail
	LEFT JOIN srp_erp_companyreporttemplatelinks companyreportlink on companyreportlink.templateDetailID = companyreportdetail.detID
	LEFT JOIN srp_erp_chartofaccounts chartofacc on chartofacc.GLAutoID = companyreportlink.glAutoID 
	where 
	companyreportlink.companyID = $companyIDSess
	AND companyreportdetail.detID = $ID
	GROUP BY
	companyreportlink.glAutoID")->result_array();


            foreach ($drilldownGLdescription as $val) {
                foreach ($periods as $period_key => $period_row) {
                    if (in_array($period_key, $percentage_cols)) {
                        continue;
                    }
                    $start = $period_row['start'];
                    $end = $period_row['end'];

                    $amount = $this->db->query("SELECT SUM(gend_tb.companyReportingAmount * -1) AS amount                                      
                                        FROM srp_erp_companyreporttemplatelinks AS temp_link
                                        JOIN srp_erp_generalledger AS gend_tb ON gend_tb.GLAutoID = temp_link.glAutoID
                                        WHERE temp_link.glAutoID = {$val['glAutoID']} AND documentDate BETWEEN '{$start}' AND '{$end}'")->row('amount');

                    $sub_data[$period_key][$val['glAutoID']] = round($amount, $dPlace);
                    $amount_arr[$period_key] = round($amount, $dPlace);
                }


                $rpt_data[] = ['description' => $val['description'], 'row_autoID' => $val['glAutoID'], 'glAutoID' => $val['glAutoID'], 'amount' => $amount_arr];
            }
        } else {
            $drilldownGLdescription = $this->db->query("SELECT
temp_link.glAutoID,
temp_link.GLDescription as description,
temp_link.templateDetailID as detID
	FROM
    (
        SELECT
	chartofacc.GLAutoID AS glAutoID,
	templateDetailID,
	temp_link.companyID,
	chartofacc.GLDescription
FROM
	srp_erp_companyreporttemplatelinks AS temp_link
	LEFT JOIN srp_erp_groupchartofaccounts chartofacc on chartofacc.GLAutoID = temp_link.glAutoID 
WHERE
	templateDetailID = $ID 
	) AS temp_link
	
	where 
	temp_link.companyID = $companyIDSess
	GROUP BY
	temp_link.glAutoID")->result_array();

            foreach ($drilldownGLdescription as $val) {
                foreach ($periods as $period_key => $period_row) {
                    if (in_array($period_key, $percentage_cols)) {
                        continue;
                    }
                    $start = $period_row['start'];
                    $end = $period_row['end'];
                    /*$amount = $this->db->query("SELECT SUM(gend_tb.companyReportingAmount * -1) AS amount
                                        FROM srp_erp_companyreporttemplatelinks AS temp_link
                                        JOIN srp_erp_generalledger AS gend_tb ON gend_tb.GLAutoID = temp_link.glAutoID
                                        WHERE temp_link.glAutoID = {$val['glAutoID']} AND documentDate BETWEEN '{$start}' AND '{$end}'")->row('amount');
                    */
                    $amount = $this->db->query("SELECT
	SUM( gend_tb.companyReportingAmount * - 1 ) AS amount,
	temp_link.glAutoID 
FROM
(
    SELECT
	coa.GLAutoID AS glAutoID,
	templateDetailID 
FROM
	srp_erp_companyreporttemplatelinks AS temp_link
	JOIN srp_erp_groupchartofaccountdetails AS ch_grp ON ch_grp.groupChartofAccountMasterID = temp_link.glAutoID 
	join srp_erp_chartofaccounts as coa on ch_grp.chartofAccountID=coa.GLAutoID
WHERE
	temp_link.glAutoID = {$val['glAutoID']}  $CompanyID_filter
	) AS temp_link
	left JOIN srp_erp_generalledger AS gend_tb ON gend_tb.GLAutoID = temp_link.glAutoID 
WHERE documentDate BETWEEN '{$start}'  AND  '{$end}'")->row('amount');

                    $sub_data[$period_key][$val['glAutoID']] = round($amount, $dPlace);
                    $amount_arr[$period_key] = round($amount, $dPlace);
                }


                $rpt_data[] = ['description' => $val['description'], 'row_autoID' => $val['glAutoID'], 'glAutoID' => $val['glAutoID'], 'amount' => $amount_arr];
            }

        }


        $data['gross_rows'] = $gross_rows;
        $data['periods'] = $periods;
        $data['period_id'] = $period;
        $data['temMasterID'] = $temMasterID;
        $data['percentage_cols'] = $percentage_cols;
        /*$data['gross_val'] = $gross_val;*/
        /*   $data['rpt_data'] = $rpt_data;*/
        $data['rpt_data_drilldown'] = $rpt_data;


        /*   echo '<pre>'; print_r($rpt_data); echo '</pre>';

            exit();*/
        $data['Company_ID'] = $companyID_drilldown;
        $data['sub_data'] = $sub_data;
        $data['dPlace'] = $dPlace;
        $data['SubName'] = $SubName;
        $data['title'] = 'MPR DrillDown';
        $data['extra'] = 'MPR';
        $data['main_content'] = 'system/mpr_widget_drilldown_view';
        $this->load->view('system/mpr_drilldown', $data);
    }

    function mpr_drilldown_docuemt()
    {
        $companyID_drilldown = $_POST["ComapnyID"];
        $comapny_ID_filter = '';
        if ($companyID_drilldown) {
            $comapny_ID_filter = 'AND srp_erp_generalledger.companyID IN (' . $companyID_drilldown . ')';
        }

        $companyID = current_companyID();
        $GLAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('glAutoID') ?? '');
        $startDateyear = ($this->uri->segment(4));
        $startDatemonth = ($this->uri->segment(5));
        $startDatedate = ($this->uri->segment(6));

        $EndDateyear = ($this->uri->segment(7));
        $EndDatemonth = ($this->uri->segment(8));
        $EndDatedate = ($this->uri->segment(9));


        $startDate = array($startDateyear, $startDatemonth, $startDatedate);
        $EndDate = array($EndDateyear, $EndDatemonth, $EndDatedate);
        $startdateFormatted = implode("-", $startDate);
        $EnddateFormatted = implode("-", $EndDate);


        $data['glDescMaster'] = $this->db->query("SELECT GLDescription FROM srp_erp_groupchartofaccounts WHERE GLAutoID  = {$GLAutoID}")->row_array();

        $companyType = $this->session->userdata("companyType");
        if ($companyType == 1) {

            $glautoidcompnaytype = $GLAutoID;
            $data['drilldowndata'] = $this->db->query("SELECT
a.documentSystemCode as doccode,
	a.documentCode,
	a.department,
	DATE_FORMAT( a.documentDate, '%d-%m-%Y' ) AS documentDate,
	a.documentDate AS documentDate2,
	a.documentDate AS documentDateSort,
	a.documentNarration,
	(a.companyReportingAmount * -1) as companyReportingAmount,
	a.companyReportingAmountDecimalPlaces,

	a.confirmedByName,
	IFNULL(a.approvedbyEmpName,'-') as approvedbyEmp,
	a.documentMasterAutoID,
	a.GLDescription,
	a.masterCategory,
	a.GLAutoID,
	a.documentMasterAutoID
FROM
	(
	(
SELECT
	srp_erp_generalledger.documentSystemCode,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.documentNarration,
	srp_erp_generalledger.companyReportingAmount,
	CR.DecimalPlaces AS companyReportingAmountDecimalPlaces,
	srp_erp_generalledger.documentCode,
	srp_erp_generalledger.documentMasterAutoID,
	srp_erp_chartofaccounts.GLDescription,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_generalledger.GLAutoID,
	srp_erp_segment.segmentCode as department,
	srp_erp_generalledger.confirmedByName,
	srp_erp_generalledger.approvedbyEmpName
FROM
	srp_erp_generalledger
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
	AND srp_erp_chartofaccounts.companyID = {$companyID}
	LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID 
	AND srp_erp_segment.companyID = {$companyID}
	LEFT JOIN ( SELECT * FROM srp_erp_customermaster WHERE companyID = {$companyID} GROUP BY customerAutoID ) cust ON srp_erp_generalledger.partyAutoID = cust.customerAutoID 
	AND srp_erp_generalledger.partyType = 'CUS'
	LEFT JOIN ( SELECT * FROM srp_erp_suppliermaster WHERE companyID = {$companyID} GROUP BY supplierAutoID ) supp ON srp_erp_generalledger.partyAutoID = supp.supplierAutoID 
	AND srp_erp_generalledger.partyType = 'SUP'
	LEFT JOIN ( SELECT document, documentID FROM srp_erp_documentcodemaster WHERE companyID = {$companyID} ) dc ON ( dc.documentID = srp_erp_generalledger.documentCode )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID ) 
WHERE
	srp_erp_generalledger.GLAutoID IN ($glautoidcompnaytype) 
	AND srp_erp_generalledger.documentDate BETWEEN '{$startdateFormatted}' 
	AND '{$EnddateFormatted}' 
	AND srp_erp_generalledger.companyID = {$companyID}  
ORDER BY
	srp_erp_generalledger.documentType,
	srp_erp_generalledger.documentDate ASC 
	) UNION ALL
	(
SELECT
	'-' AS documentSystemCode,
	'' AS documentDate,
	'CF Balance' AS documentNarration,
	SUM( companyReportingAmount ) AS companyReportingAmount,
	CR.DecimalPlaces AS companyReportingAmountDecimalPlaces,
	srp_erp_generalledger.documentCode,
	srp_erp_generalledger.documentMasterAutoID,
	srp_erp_chartofaccounts.GLDescription,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_generalledger.GLAutoID,
	srp_erp_segment.segmentCode as department,
	srp_erp_generalledger.confirmedByName,
	srp_erp_generalledger.approvedbyEmpName
FROM
	srp_erp_generalledger
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
	AND srp_erp_chartofaccounts.companyID = {$companyID} 
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID ) 
	LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID 
	AND srp_erp_segment.companyID = {$companyID}
WHERE
	srp_erp_generalledger.GLAutoID IN ($glautoidcompnaytype) 
	AND srp_erp_chartofaccounts.masterCategory = 'BS' 
	AND srp_erp_generalledger.documentDate < '{$startdateFormatted}' 
	AND srp_erp_generalledger.companyID = {$companyID} 
GROUP BY
	srp_erp_generalledger.GLAutoID 
	) 
	) AS a 
ORDER BY
	documentDate2 ASC")->result_array();
        } else {
            $glID = $this->db->query("SELECT GROUP_CONCAT(chartofAccountID) as glautoid FROM `srp_erp_groupchartofaccountdetails` where groupChartofAccountMasterID = {$GLAutoID}")->row_array();
            $glautoidcompnaytype = $glID['glautoid'];

            $data['drilldowndata'] = $this->db->query("SELECT
a.documentSystemCode as doccode,
	a.documentCode,
	a.department,
	DATE_FORMAT( a.documentDate, '%d-%m-%Y' ) AS documentDate,
	a.documentDate AS documentDate2,
	a.documentDate AS documentDateSort,
	a.documentNarration,
	(a.companyReportingAmount * -1) as companyReportingAmount,
	a.companyReportingAmountDecimalPlaces,

	a.confirmedByName,
	IFNULL(a.approvedbyEmpName,'-') as approvedbyEmp,
	a.documentMasterAutoID,
	a.GLDescription,
	a.masterCategory,
	a.GLAutoID,
	a.documentMasterAutoID
FROM
	(
	(
SELECT
	srp_erp_generalledger.documentSystemCode,
	srp_erp_generalledger.documentDate,
	srp_erp_generalledger.documentNarration,
	srp_erp_generalledger.companyReportingAmount,
	CR.DecimalPlaces AS companyReportingAmountDecimalPlaces,
	srp_erp_generalledger.documentCode,
	srp_erp_generalledger.documentMasterAutoID,
	srp_erp_chartofaccounts.GLDescription,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_generalledger.GLAutoID,
	srp_erp_segment.segmentCode as department,
	srp_erp_generalledger.confirmedByName,
	srp_erp_generalledger.approvedbyEmpName
FROM
	srp_erp_generalledger
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
	
	LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID 
	
	LEFT JOIN ( SELECT * FROM srp_erp_customermaster WHERE companyID = {$companyID} GROUP BY customerAutoID ) cust ON srp_erp_generalledger.partyAutoID = cust.customerAutoID 
	AND srp_erp_generalledger.partyType = 'CUS'
	LEFT JOIN ( SELECT * FROM srp_erp_suppliermaster WHERE companyID = {$companyID} GROUP BY supplierAutoID ) supp ON srp_erp_generalledger.partyAutoID = supp.supplierAutoID 
	AND srp_erp_generalledger.partyType = 'SUP'
	LEFT JOIN ( SELECT document, documentID FROM srp_erp_documentcodemaster WHERE companyID = {$companyID} ) dc ON ( dc.documentID = srp_erp_generalledger.documentCode )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID ) 
WHERE
	srp_erp_generalledger.GLAutoID IN ($glautoidcompnaytype) 
    $comapny_ID_filter
	AND srp_erp_generalledger.documentDate BETWEEN '{$startdateFormatted}' 
	AND '{$EnddateFormatted}' 

ORDER BY
	srp_erp_generalledger.documentType,
	srp_erp_generalledger.documentDate ASC 
	) UNION ALL
	(
SELECT
	'-' AS documentSystemCode,
	'' AS documentDate,
	'CF Balance' AS documentNarration,
	SUM( companyReportingAmount ) AS companyReportingAmount,
	CR.DecimalPlaces AS companyReportingAmountDecimalPlaces,
	srp_erp_generalledger.documentCode,
	srp_erp_generalledger.documentMasterAutoID,
	srp_erp_chartofaccounts.GLDescription,
	srp_erp_chartofaccounts.masterCategory,
	srp_erp_generalledger.GLAutoID,
	srp_erp_segment.segmentCode as department,
	srp_erp_generalledger.confirmedByName,
	srp_erp_generalledger.approvedbyEmpName
FROM
	srp_erp_generalledger
	INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID 
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CR ON ( CR.currencyID = srp_erp_generalledger.companyReportingCurrencyID )
	LEFT JOIN ( SELECT DecimalPlaces, currencyID FROM srp_erp_currencymaster ) CL ON ( CL.currencyID = srp_erp_generalledger.companyLocalCurrencyID ) 
	LEFT JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID 

WHERE
	srp_erp_generalledger.GLAutoID IN ($glautoidcompnaytype) 
	$comapny_ID_filter
	AND srp_erp_chartofaccounts.masterCategory = 'BS' 
	AND srp_erp_generalledger.documentDate < '{$startdateFormatted}' 

GROUP BY
	srp_erp_generalledger.GLAutoID 
	) 
	) AS a 
ORDER BY
	documentDate2 ASC")->result_array();

        }


        $data['title'] = 'MPR DrillDown';
        $data['main_content'] = 'system/mpr_widget_drilldown_view_document_wise';
        $data['extra'] = 'MPR';
        $this->load->view('system/mpr_drilldown_document_wise', $data);
    }



    function fetch_company_wise_segment()
    {
        $data_arr = array();
        $companyID = $this->input->post('companyID');
        $data_arr = array('' => 'Select Segment');
        if (!empty($companyID)) {
            $CompanySegment = "SELECT segmentCode,description,segmentID FROM srp_erp_segment WHERE status = 1 AND companyID = {$companyID}";
            $Companyseg = $this->db->query($CompanySegment)->result_array();

            if (!empty($Companyseg)) {

                foreach ($Companyseg as $row) {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }
        }
            echo form_dropdown('segmentID', $data_arr, 'Each', 'class="form-control select2" id="segmentID" ');


    }
    function fetch_company_wise_employee()
    {
        $data_arr = array();
        $companyID = $this->input->post('companyID');
        $data_arr = array('' => 'Select Employee');
        if (!empty($companyID)) {
            $CompanyEmployee = "SELECT EIdNo,Ename2 FROM srp_employeesdetails WHERE isDischarged != 1 AND isSystemAdmin !=1  AND Erp_companyID = {$companyID}";
            $Companyemp = $this->db->query($CompanyEmployee)->result_array();

            if (!empty($Companyemp)) {

                foreach ($Companyemp as $row) {
                    $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
                }
            }
        }
        echo form_dropdown('employeeID', $data_arr, 'Each', 'class="form-control select2" id="employeeID" ');


    }
    function save_add_action_tracker()
    {
        $this->form_validation->set_rules('selectedcomapnyID', 'Company', 'trim|required');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('adddescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('targetdate', 'Target Date	', 'trim|required');
        $this->form_validation->set_rules('employeeID', 'Employee', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model->save_action_tracker());
        }
    }
    function fetch_company_wise_action_tracker()
    {
        $data['Output'] = $this->Finance_dashboard_model->get_company_action_tracker();
        $this->load->view('system/action_tacker_view',$data);
    }
    function fetch_company_master_details()
    {
        $data['Output'] = $this->Finance_dashboard_model->get_company_action_tracker_view();
        $this->load->view('system/action_tacker_view_master',$data);
    }
    function fetch_company_action_tracker_view_model()
    {
        echo json_encode($this->Finance_dashboard_model->get_company_action_tracker_view_master());

    }

    function update_add_action_tracker()
    {
        $this->form_validation->set_rules('selectedcomapnyID_edit', 'Company', 'trim|required');
        $this->form_validation->set_rules('segmentID_edit', 'Segment', 'trim|required');
        $this->form_validation->set_rules('actiondescriptionedit', 'Description', 'trim|required');
        $this->form_validation->set_rules('employeeID_edit', 'Employee', 'trim|required');
        $this->form_validation->set_rules('actiontrackerdetailID', 'Detail ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model->update_action_tracker_detial());
        }
    }
    function update_close_status()
    {
        echo json_encode($this->Finance_dashboard_model->update_close_status());
    }
    function fetch_company_wise_segment_edit()
    {
        $data_arr = array();
        $companyID = $this->input->post('companyID');
        $data_arr = array('' => 'Select Segment');
        if (!empty($companyID)) {
            $CompanySegment = "SELECT segmentCode,description,segmentID FROM srp_erp_segment WHERE status = 1 AND companyID = {$companyID}";
            $Companyseg = $this->db->query($CompanySegment)->result_array();

            if (!empty($Companyseg)) {

                foreach ($Companyseg as $row) {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }
        }
        echo form_dropdown('segmentID_edit', $data_arr, 'Each', 'class="form-control select2" id="segmentID_edit" ');


    }
    function fetch_company_wise_employee_edit()
    {
        $data_arr = array();
        $companyID = $this->input->post('companyID');
        $data_arr = array('' => 'Select Employee');
        if (!empty($companyID)) {
            $CompanyEmployee = "SELECT EIdNo,Ename2 FROM srp_employeesdetails WHERE isDischarged != 1 AND isSystemAdmin !=1  AND Erp_companyID = {$companyID}";
            $Companyemp = $this->db->query($CompanyEmployee)->result_array();

            if (!empty($Companyemp)) {

                foreach ($Companyemp as $row) {
                    $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
                }
            }
        }
        echo form_dropdown('employeeID_edit', $data_arr, 'Each', 'class="form-control select2" id="employeeID_edit" ');


    }
    function assignedtask_myprofile_mpr()
    {
        $data['output'] = $this->Finance_dashboard_model->fetch_assigned_taskmyprofile();
        $this->load->view('system/hrm/load_assignedtask_mpr_emp',$data);
    }
    function update_mpr_task_status()
    {
        $status = $this->input->post('status');
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        $this->form_validation->set_rules('assignedID', 'Assigned Detail ID', 'trim|required');
        if ($status == 2)
        {
            $this->form_validation->set_rules('completiondate', 'Completion Date', 'trim|required');
            $this->form_validation->set_rules('comment', 'Comment', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model->update_mpr_task_status());
        }
    }
    function createdtask_myprofile_mpr()
    {
        $data['output'] = $this->Finance_dashboard_model->fetch_created_taskmyprofile();
        $this->load->view('system/hrm/load_created_task_mpr_emp',$data);
    }

    function edit_to_do_list()
    {
        echo json_encode($this->Finance_dashboard_model->edit_to_do_list());
    }
    function  update_to_do_list()
    {
        $this->form_validation->set_rules('edit_description', 'Description', 'trim|required');
        $this->form_validation->set_rules('edit_startDate', 'Date', 'trim|required');
        $this->form_validation->set_rules('edit_startTime', 'Time', 'trim|required');
        $this->form_validation->set_rules('edit_priority', 'Priority', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Finance_dashboard_model-> update_to_do_list());
        }
    }
    function toptencustomers()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp-topten-customers', $data);
    }

    function toptencustomer_view()
    {
        $companyID = current_companyID();
        $data['currentcyID'] = $this->input->post('currencyID');
        $exchangeamt = 1;
        $data['userDashboardID'] = $this->input->post('userDashboardID');
        if($data['currentcyID']  == 1)
        {

            $exchangeamt = 'companyLocalExchangeRate';
        }else
        {

            $exchangeamt = 'companyReportingExchangeRate';
        }

        $toptencustomerlist = $this->db->query("SELECT 	
customerAutoID,
customerSystemCode,
customerName,
SUM(transactionAmount) as transactionAmount
FROM (SELECT
	customermaster.customerSystemCode,
	customermaster.customerName,
	customermaster.customerAutoID,
    (invoicedetail.transactionAmount/$exchangeamt) as transactionAmount
FROM
	srp_erp_customerinvoicedetails invoicedetail
	LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID
	LEFT JOIN srp_erp_customermaster customermaster on customermaster.customerAutoID = invoicemaster.customerID
	WHERE
	invoicedetail.companyID = $companyID
    AND approvedYN  = 1 
    AND customerAutoID IS NOT NULL
	UNION ALL
	SELECT
	customermaster.customerSystemCode,
	customermaster.customerName,
    customermaster.customerAutoID,
    (receiptdetail.transactionAmount/receiptdetail.$exchangeamt) as transactionAmount
FROM
	srp_erp_customerreceiptdetail receiptdetail
	LEFT JOIN srp_erp_customerreceiptmaster receiptmaster on receiptdetail.receiptVoucherAutoId = receiptmaster.receiptVoucherAutoId
    LEFT JOIN srp_erp_customermaster customermaster on customermaster.customerAutoID = receiptmaster.customerID
	where
	receiptdetail.companyID = $companyID
	AND approvedYN  = 1 
    AND customerAutoID IS NOT NULL
	AND type IN ('GL','Item','DO'))t1 GROUP BY t1.customerAutoID ORDER BY transactionAmount DESC LIMIT 0,20")->result_array();

        $data['toptencustomerlist'] = $toptencustomerlist;
        $this->load->view('system/erp-topten-customers-view',$data);
    }
    function get_toptencustomerdd()
    {
        $customerAutoID = $this->input->post('customerAutoID');
        $data['currenyID'] = $this->input->post('currenyID');
        $convertFormat = convert_date_format_sql();

        $companyID = current_companyID();
        $exchangeamt = 1;
        if($data['currenyID'] == 1)
        {

            $exchangeamt = 'companyLocalExchangeRate';
        }else
        {

            $exchangeamt = 'companyReportingExchangeRate';
        }
        $data['customername'] = $this->input->post('customerName');
        $data['toptencustomerdd'] = $this->db->query("SELECT docmasterID,documentsystemcode,documentID,transactionAmount,DATE_FORMAT(DocumentDate, \"{$convertFormat}\") AS DocumentDate FROM (SELECT
invoicemaster.invoiceAutoID as docmasterID,
invoicemaster.invoiceCode as documentsystemcode,
documentID as documentID,
SUM((invoicedetail.transactionAmount/$exchangeamt)) as transactionAmount,
invoiceDate as DocumentDate
FROM
	srp_erp_customerinvoicedetails invoicedetail
	LEFT JOIN srp_erp_customerinvoicemaster invoicemaster on invoicemaster.invoiceAutoID = invoicedetail.invoiceAutoID 
	LEFT JOIN srp_erp_customermaster customermaster on customermaster.customerAutoID = invoicemaster.customerID
	WHERE
	invoicedetail.companyID = $companyID
	AND approvedYN  = 1 
	AND customerAutoID  = $customerAutoID
	GROUP BY 
	invoicemaster.invoiceAutoID
UNION ALL
	SELECT
	receiptmaster.receiptVoucherAutoId as docmasterID,
	receiptmaster.RVcode as documentsystemcode,
	documentID as documentID,
	SUM((receiptdetail.transactionAmount/receiptdetail.$exchangeamt)) as transactionAmount,
	receiptmaster.RVdate as DocumentDate
FROM
	srp_erp_customerreceiptdetail receiptdetail
	LEFT JOIN srp_erp_customerreceiptmaster receiptmaster on receiptdetail.receiptVoucherAutoId = receiptmaster.receiptVoucherAutoId
		LEFT JOIN srp_erp_customermaster customermaster on customermaster.customerAutoID = receiptmaster.customerID
	where
	receiptdetail.companyID = $companyID
	AND approvedYN  = 1 
	AND customerAutoID  = $customerAutoID
    AND type IN ('GL','Item','DO')
	GROUP BY 
	receiptmaster.receiptVoucherAutoId) totalcustomer WHERE transactionAmount > 0 ORDER BY transactionAmount DESC
	")->result_array();
        $this->load->view('system/erp-topten-customers-drilldown',$data);

    }
    function toptensuppliers()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp-topten-suppliers', $data);
    }
    function toptensupplier_view()
    {
        $companyID = current_companyID();
        $data['currentcyID'] = $this->input->post('currencyID');
        $data['userDashboardID'] = $this->input->post('userDashboardID');
        $exchangeamt = 1;
        if($data['currentcyID']  == 1)
        {

            $exchangeamt = 'companyLocalExchangeRate';
        }else
        {

            $exchangeamt = 'companyReportingExchangeRate';
        }

        $toptensupplierlist = $this->db->query("SELECT supplierAutoID,supplierSystemCode,suppliername,sum( transactionAmount) AS transactionAmount FROM (SELECT
 suppliermaster.supplierAutoID,
 suppliermaster.supplierSystemCode,
	suppliermaster.supplierName as suppliername,
	(transactionAmount/$exchangeamt) AS transactionAmount 
FROM
	srp_erp_purchaseorderdetails podetails
	LEFT JOIN srp_erp_purchaseordermaster pomaster on pomaster.purchaseOrderID = podetails.purchaseOrderID
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = pomaster.supplierID
	where 
	podetails.companyID = $companyID 
	AND approvedYN  = 1 
	AND supplierID IS NOT NULL
UNION ALL
SELECT
 suppliermaster.supplierAutoID,
 suppliermaster.supplierSystemCode,
	suppliermaster.supplierName as suppliername,
		(transactionAmount/$exchangeamt)  AS transactionAmount 
FROM
	srp_erp_grvdetails grvdetail
	LEFT JOIN srp_erp_grvmaster grvmasr on grvdetail.grvAutoID =grvmasr.grvAutoID 
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = grvmasr.supplierID
	WHERE 
	grvdetail.companyID = $companyID 
	AND approvedYN  = 1 
    AND supplierID IS NOT NULL
	AND grvType = 'Standard'
	UNION ALL
	SELECT
	 suppliermaster.supplierAutoID,
 suppliermaster.supplierSystemCode,
	suppliermaster.supplierName as suppliername,
	(supdetail.transactionAmount/supdetail.$exchangeamt) AS transactionAmount 
FROM
	srp_erp_paysupplierinvoicedetail supdetail
	LEFT JOIN srp_erp_paysupplierinvoicemaster supmaster on supmaster.InvoiceAutoID = supdetail.InvoiceAutoID
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = supmaster.supplierID
	where 
	supdetail.companyID = $companyID 
	AND approvedYN  = 1 
	AND supplierID IS NOT NULL
	AND type IN ('GL','Item')
UNION ALL
SELECT
	 suppliermaster.supplierAutoID,
 suppliermaster.supplierSystemCode,
	suppliermaster.supplierName as suppliername,
		(paymentvoucherdetail.transactionAmount/paymentvoucherdetail.$exchangeamt) AS transactionAmount 
FROM
	srp_erp_paymentvoucherdetail paymentvoucherdetail
	LEFT JOIN srp_erp_paymentvouchermaster paymentvouchermaster on paymentvouchermaster.payVoucherAutoId = paymentvoucherdetail.payVoucherAutoId
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = paymentvouchermaster.partyID
	where 
	paymentvoucherdetail.companyID = $companyID 
	AND approvedYN  = 1 
	AND paymentvouchermaster.partyID IS NOT NULL
	AND partyType != 'DIR'
	AND partyType != 'EMP'
	AND partyType != 'PRQ'
	AND paymentvoucherdetail.type IN ('GL','Item')	
	) toptensupp  GROUP BY toptensupp.supplierAutoID ORDER BY transactionAmount DESC LIMIT 0,20")->result_array();

        $data['toptensupplierlist'] = $toptensupplierlist;
        $this->load->view('system/erp-topten-supplier-view',$data);
    }
    function get_toptensupplierrdd()
    {
        $supplierAutoID = $this->input->post('supplierAutoID');
        $companyID = current_companyID();
        $data['suppliername'] = $this->input->post('suppliername');

        $data['currenyID'] = $this->input->post('currenyID');
        $exchangeamt = 1;
        if($data['currenyID'] == 1)
        {

            $exchangeamt = 'companyLocalExchangeRate';
        }else
        {

            $exchangeamt = 'companyReportingExchangeRate';
        }
        $convertFormat = convert_date_format_sql();

        $data['toptensupplierdd'] = $this->db->query("SELECT documentmasterID,documentcode,documentID,transactionAmount,   DATE_FORMAT(docdate, \"{$convertFormat}\") AS DocumentDate FROM (SELECT
pomaster.purchaseOrderID as documentmasterID,
	pomaster.purchaseOrderCode as documentcode,
	pomaster.documentID as documentID,
	sum( transactionAmount/$exchangeamt) AS transactionAmount,
	pomaster.documentDate as docdate
FROM
	srp_erp_purchaseorderdetails podetails
	LEFT JOIN srp_erp_purchaseordermaster pomaster on pomaster.purchaseOrderID = podetails.purchaseOrderID
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = pomaster.supplierID
	where 
	podetails.companyID = $companyID 
	AND approvedYN  = 1 
	AND supplierAutoID  = $supplierAutoID
	GROUP BY 
	pomaster.purchaseOrderID
UNION ALL
SELECT
grvmasr.grvAutoID as documentmasterID,
grvmasr.grvPrimaryCode as documentcode,
grvmasr.documentID as documentID,
sum( transactionAmount/$exchangeamt) AS transactionAmount,
grvmasr.grvDate as docdate
FROM
	srp_erp_grvdetails grvdetail
	LEFT JOIN srp_erp_grvmaster grvmasr on grvdetail.grvAutoID =grvmasr.grvAutoID 
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = grvmasr.supplierID
	WHERE 
	grvdetail.companyID = $companyID 
	AND approvedYN  = 1 
	AND supplierAutoID  = $supplierAutoID
	AND grvType = 'Standard'
	GROUP BY 
    grvdetail.grvAutoID
	UNION ALL
	SELECT
		supmaster.InvoiceAutoID as documentmasterID,
	supmaster.bookingInvCode as documentcode,
supmaster.documentID as documentID,
	sum( supdetail.transactionAmount/supdetail.$exchangeamt ) AS transactionAmount,
	supmaster.bookingDate as docdate
FROM
	srp_erp_paysupplierinvoicedetail supdetail
	LEFT JOIN srp_erp_paysupplierinvoicemaster supmaster on supmaster.InvoiceAutoID = supdetail.InvoiceAutoID
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = supmaster.supplierID
	where 
	supdetail.companyID = $companyID 
	AND approvedYN  = 1 
	AND supplierAutoID  = $supplierAutoID
	AND type IN ('GL','Item')
		GROUP BY 
supmaster.InvoiceAutoID
UNION ALL
SELECT
paymentvouchermaster.payVoucherAutoId as documentmasterID,
paymentvouchermaster.PVcode as documentcode,
paymentvouchermaster.documentID as documentID,
sum( paymentvoucherdetail.transactionAmount/paymentvoucherdetail.$exchangeamt) AS transactionAmount,
paymentvouchermaster.PVdate as docdate
FROM
	srp_erp_paymentvoucherdetail paymentvoucherdetail
	LEFT JOIN srp_erp_paymentvouchermaster paymentvouchermaster on paymentvouchermaster.payVoucherAutoId = paymentvoucherdetail.payVoucherAutoId
	LEFT JOIN srp_erp_suppliermaster suppliermaster on suppliermaster.supplierAutoID = paymentvouchermaster.partyID
	where 
	paymentvoucherdetail.companyID = $companyID 
	AND approvedYN  = 1 
	AND paymentvouchermaster.partyID  = $supplierAutoID
	AND partyType != 'DIR'
	AND partyType != 'EMP'
	AND partyType != 'PRQ'
	AND paymentvoucherdetail.type IN ('GL','Item')	
    GROUP BY 
paymentvouchermaster.payVoucherAutoId 
	) toptensupp HAVING transactionAmount > 0  ORDER BY transactionAmount DESC")->result_array();
        $this->load->view('system/erp-topten-supplier-drilldown',$data);

    }
/*Start Function */
	function load_minimum_stock()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $this->load->view('system/erp_ajax_load_min_stock', $data);
    }

    function fetch_minimum_stock(){

    	$companyID = current_companyID();

       	  $this->datatables->select('t1.itemAutoID as itemAutoID ,t1.itemSystemCode as itemSystemCode,t1.itemDescription as itemDescription,t1.minimumQty as minimumQty, t1.currentQty as currentQty', false)
            ->from('(SELECT
itemmaster.itemAutoID,
itemmaster.itemSystemCode,
itemmaster.itemDescription,
itemmaster.minimumQty,
ifnull(itemledger.Qty,0) as currentQty
FROM
srp_erp_itemmaster itemmaster
LEFT JOIN (
SELECT
itemAutoID,
sum(
transactionQTY / convertionRate
) AS Qty
FROM
srp_erp_itemledger
where companyID='.$companyID.'
GROUP BY
itemAutoID
) itemledger on itemmaster.itemAutoID=itemledger.itemAutoID
where companyID='.$companyID.'
having currentQty<minimumQty)t1')
            ->edit_column('currentQty', '<div class="text-right"> <b>$1</b></div>', 'dashboard_format_number(currentQty)');
        echo $this->datatables->generate();
        //echo $this->datatables->last_query();
    }
/*End Function*/

    /*** dashboard widgets added */
    function PO_localVSinternational()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $country = $this->common_data['company_data']['company_country'];
        $data["userDashboardID"] = $this->input->post("userDashboardID");

        $local = $this->db->query("SELECT
	COUNT( purchaseOrderID ) AS totalDocuments 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID} 
	/*AND approvedYN = 1 */
	AND supplierCountry = '{$country}' 
	AND (
		(closedYN = 0 AND approvedYN = 1)
		OR (
			closedYN = 1 AND approvedYN = 5
		AND purchaseOrderID IN ( SELECT purchaseOrderMastertID FROM srp_erp_grvdetails WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID UNION ALL SELECT purchaseOrderMastertID FROM srp_erp_paysupplierinvoicedetail WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID ) 
	))")->row_array();

        $international = $this->db->query("SELECT
	COUNT( purchaseOrderID ) AS totalDocuments 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID} 
	/*AND approvedYN = 1 */
	AND supplierCountry != '{$country}' 
	AND (
		(closedYN = 0 AND approvedYN = 1)
		OR (
			closedYN = 1 AND approvedYN = 5
		AND purchaseOrderID IN ( SELECT purchaseOrderMastertID FROM srp_erp_grvdetails WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID UNION ALL SELECT purchaseOrderMastertID FROM srp_erp_paysupplierinvoicedetail WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID ) 
	))")->row_array();

        $data['local'] = $local['totalDocuments'];
        $data['international'] = $international['totalDocuments'];
        $this->load->view('system/erp-PO_localVSinternational', $data);
    }

    function PO_localVSinternational_permonth()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["financeYear"] = $this->common_data["company_data"]["companyFinanceYearID"];
        $this->load->view('system/erp-PO_localVSinternational_permonth', $data);
    }

    function fetch_PO_permonth_view()
    {
        $financeyearid =  $this->input->post('financeyearid');
        $country = $this->common_data['company_data']['company_country'];
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["details"] = $this->Finance_dashboard_model->fetch_PO_localVSinternational_permonth($financeyearid, $country);

        $this->load->view('system/erp-PO_localVSinternational_permonth_view', $data);
    }

    function supplier_delivery_analysis()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["financeYear"] = $this->common_data["company_data"]["companyFinanceYearID"];
        $this->load->view('system/erp-supplier_delivery_analysis', $data);
    }

    function supplier_delivery_analysis_view()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["details"] = $this->Finance_dashboard_model->fetch_supplier_delivery_analysis();

        $this->load->view('system/erp-supplier_delivery_analysis_view', $data);
    }

    function supplier_delivery_analysis_drilldown()
    {
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["extra"] = $this->Finance_dashboard_model->fetch_supplier_delivery_analysis_drilldown();
        $this->load->view('system/erp-supplier_delivery_analysis_drilldown', $data);
    }

    function load_raw_materials_avg_purchase(){
        $data["userDashboardID"] = $this->input->post("userDashboardID");
        $data["extra"] = $this->Finance_dashboard_model->fetch_raw_materials_avg_purchase();
        $this->load->view('system/raw_material_avg_purchase.php', $data);
    }
}



