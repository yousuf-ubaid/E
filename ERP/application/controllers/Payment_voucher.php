<?php

class Payment_voucher extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helpers('payable');
        $this->load->model('Payment_voucher_model');
        $this->load->model('Receipt_reversale_model');
        $this->load->model('Payable_modal');
        $this->load->helpers('exceedmatch');
    }

    function fetch_payment_voucher()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $collectionstatus = $this->input->post('collectionstatus');
        $supplier_filter = '';
        $collection_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        if (!empty($collectionstatus)) {
            if ($collectionstatus == 1) {
                $collection_filter = " AND (collectedStatus = 1 AND approvedYN = 1)";
            } else if ($collectionstatus == 2) {
                $collection_filter = " AND (collectedStatus = 2 AND approvedYN = 1)";
            } else if ($collectionstatus == 3) {
                $collection_filter = " AND (collectedStatus = 0  AND approvedYN = 1)";
            }
        }


        $sSearch = $this->input->post('sSearch');
        $searches = '';
        /*if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( PVcode Like '%$search%' ESCAPE '!') OR ( pvType Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (PVNarration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (PVdate Like '%$sSearch%')) ";
        }*/


        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches . $collection_filter . "";
        $this->datatables->select('srp_erp_paymentvouchermaster.modeOfPayment as modeOfPayment, srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,collectedStatus,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,srp_erp_paymentvouchermaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces, srp_erp_paymentvouchermaster.subInvoiceList as subInvoiceList,cus_typedet.transactionAmount as customer_amount,
        (((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)-IFNULL(incomeDetail.income_amount,0) - IFNULL(customerInv.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.isDeleted as isDeleted,bankGLAutoID,case pvType when \'Direct\' OR \'DirectItem\' OR \'DirectExpense\' then partyName when \'Employee\' OR \'EmployeeExpense\' OR \'EmployeeItem\' then srp_employeesdetails.Ename2 when \'PurchaseRequest\' then partyName when \'Supplier\' OR \'SupplierAdvance\' OR \'SupplierDebitNote\' OR \'SupplierInvoice\' OR \'SupplierItem\' OR \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName,paymentType,srp_erp_paymentvouchermaster.confirmedByEmpID as confirmedByEmp,srp_erp_paymentvouchermaster.collectedStatus as collectedStatus,srp_erp_paymentvouchermaster.isSytemGenerated as isSytemGenerated, srp_erp_paymentvouchermaster.referenceNo AS referenceNo,PVchequeNo, CASE WHEN pvType = \'DirectItem\' THEN \'Direct Item Payment\' WHEN pvType = \'DirectExpense\' THEN \'Direct Expense\' WHEN pvType = \'SupplierInvoice\' OR pvType = \'SupplierAdvance\' THEN \'Supplier Invoice Payment\' WHEN pvType = \'SupplierItem\' THEN \'Supplier Item Payment\' WHEN pvType = \'SupplierExpense\' THEN \'Supplier Expense Payment\' WHEN pvType = \'EmployeeExpense\' THEN \'Employee Expense Payment\' WHEN pvType = \'EmployeeItem\' THEN \'Employee Item Payment\' WHEN pvType = \'Direct\' THEN \'Direct Item Payment\' WHEN pvType = \'PurchaseRequest\' THEN \'Purchase Request\' ELSE `pvType`  END AS pvType');
        $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" AND srp_erp_paymentvoucherdetail.type != "INGL" AND  srp_erp_paymentvoucherdetail.detailInvoiceType IS NULL GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.detailInvoiceType="CUS" GROUP BY payVoucherAutoId) customerInv', '(customerInv.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');

        //$this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(IFNULL( transactionAmount, 0 )) as income_amount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE type ="INGL" GROUP BY payVoucherAutoId) incomeDetail', '(incomeDetail.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.detailInvoiceType="CUS" GROUP BY payVoucherAutoId) cus_typedet', '(cus_typedet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');


        $this->datatables->where($where);
        $this->datatables->where('pvType <>', 'SC');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b><span style="word-wrap: break-word;width: 600px;display: block;"> $1 </span><b>Ref No : </b> $6 $7', 'PVNarration,partyName,PVdate,transactionCurrency,pvType,referenceNo,set_chequeNo(paymentType,PVchequeNo, modeOfPayment)');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId,null,collectedStatus, paymentType, modeOfPayment)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action(payVoucherAutoId,confirmedYN,approvedYN,createdUser,PV,isDeleted,bankGLAutoID,paymentType,pvType,confirmedByEmp,isSytemGenerated,subInvoiceList,customer_amount)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_payment_voucher_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $collectionstatus = $this->input->post('collectionstatus');
        $supplier_filter = '';
        $collection_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        if (!empty($collectionstatus)) {
            if ($collectionstatus == 1) {
                $collection_filter = " AND (collectedStatus = 1 AND approvedYN = 1)";
            } else if ($collectionstatus == 2) {
                $collection_filter = " AND (collectedStatus = 2 AND approvedYN = 1)";
            } else if ($collectionstatus == 3) {
                $collection_filter = " AND (collectedStatus = 0  AND approvedYN = 1)";
            }
        }

        $sSearch = $this->input->post('sSearch');
        $searches = '';

        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches . $collection_filter . "";
        $this->datatables->select('srp_erp_paymentvouchermaster.modeOfPayment as modeOfPayment, srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,collectedStatus,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,srp_erp_paymentvouchermaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces, (((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.isDeleted as isDeleted,bankGLAutoID,case pvType when \'Direct\' OR \'DirectItem\' OR \'DirectExpense\' then partyName when \'Employee\' OR \'EmployeeExpense\' OR \'EmployeeItem\' then srp_employeesdetails.Ename2 when \'PurchaseRequest\' then partyName when \'Supplier\' OR \'SupplierAdvance\' OR \'SupplierDebitNote\' OR \'SupplierInvoice\' OR \'SupplierItem\' OR \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName,paymentType,srp_erp_paymentvouchermaster.confirmedByEmpID as confirmedByEmp,srp_erp_paymentvouchermaster.collectedStatus as collectedStatus,srp_erp_paymentvouchermaster.isSytemGenerated as isSytemGenerated, srp_erp_paymentvouchermaster.referenceNo AS referenceNo,PVchequeNo, CASE WHEN pvType = \'DirectItem\' THEN \'Direct Item Payment\' WHEN pvType = \'DirectExpense\' THEN \'Direct Expense\' WHEN pvType = \'SupplierInvoice\' OR pvType = \'SupplierAdvance\' THEN \'Supplier Invoice Payment\' WHEN pvType = \'SupplierItem\' THEN \'Supplier Item Payment\' WHEN pvType = \'SupplierExpense\' THEN \'Supplier Expense Payment\' WHEN pvType = \'EmployeeExpense\' THEN \'Employee Expense Payment\' WHEN pvType = \'EmployeeItem\' THEN \'Employee Item Payment\' WHEN pvType = \'Direct\' THEN \'Direct Item Payment\' WHEN pvType = \'PurchaseRequest\' THEN \'Purchase Request\' ELSE `pvType`  END AS pvType');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        //$this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('pvType <>', 'SC');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b> $1 <br> <b>Ref No : </b> $6 $7', 'PVNarration,partyName,PVdate,transactionCurrency,pvType,referenceNo,set_chequeNo(paymentType,PVchequeNo, modeOfPayment)');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId,null,collectedStatus, paymentType, modeOfPayment)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action_buyback(payVoucherAutoId,confirmedYN,approvedYN,createdUser,PV,isDeleted,bankGLAutoID,paymentType,pvType,confirmedByEmp,isSytemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_commission_payment()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,createdUserID,partyName,transactionCurrency,transactionCurrencyDecimalPlaces,pvType,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search,isDeleted,paymentType,confirmedByEmpID,srp_erp_paymentvouchermaster.referenceNo as referenceNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('pvType', 'SC');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Sales person Name : </b> $2 <br><b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b> $1 <br> <b>Ref No : </b> $6 <br>', 'PVNarration,partyName,PVdate,transactionCurrency,pvType,referenceNo');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action(payVoucherAutoId,confirmedYN,approvedYN,createdUserID,"SC",isDeleted,bankGLAutoID,paymentType,pvType,confirmedByEmpID,0)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function fetch_payment_match()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( matchDate >= '" . $datefromconvert . " 00:00:00' AND matchDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2) or (confirmedYN = 3))";
            }
        }
        $where = "srp_erp_pvadvancematch.companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_pvadvancematch.matchID as matchID,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate ,matchSystemCode,refNo,Narration,srp_erp_suppliermaster.supplierName as supliermastername,transactionCurrency ,transactionCurrencyDecimalPlaces,confirmedYN,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_pvadvancematch.confirmedByEmpID as confirmedByEmp,srp_erp_pvadvancematch.createdUserID as createdUser, srp_erp_pvadvancematch.refNo AS refNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,matchID FROM srp_erp_pvadvancematchdetails GROUP BY matchID) det', '(det.matchID = srp_erp_pvadvancematch.matchID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_pvadvancematch');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_pvadvancematch.supplierID');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3  <b>  <br>  <b>Comments : </b> $1 <br>  <b>Ref No : </b> $5', 'Narration,supliermastername,matchDate,transactionCurrency,refNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_approval(confirmedYN)');
        $this->datatables->add_column('edit', '$1', 'load_pvm_action(matchID,confirmedYN,isDeleted,confirmedByEmp,createdUser)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function save_paymentvoucher_header()
    {
        $date_format_policy = date_format_policy();
        $Pdte = $this->input->post('PVdate');
        $PVdate = input_format_date($Pdte, $date_format_policy);

        $PVchqDte = $this->input->post('PVchequeDate');
        $voucherType = $this->input->post('pvtype');
        $PVbankCode = $this->input->post('PVbankCode');
        $PVchequeDate = input_format_date($PVchqDte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $enbleAuthorizeSignature = getPolicyValues('SGB', 'All');
        
        $PVbankCode = $this->input->post('PVbankCode');
        $paymentType = $this->input->post('paymentType');

        if($enbleAuthorizeSignature ==1){
            if($paymentType ==1){
                $signature = $this->input->post('signature');
                $signature_on_bank = fetch_signature_authority_on_gl_code($PVbankCode);
                if(count($signature_on_bank)>0){
                    if(count($signature)>0){
    
                    }else{
                        $this->form_validation->set_rules('signature', 'Signatures', 'trim|required');
                    }
                    
                }
            }
        }
        

        $this->form_validation->set_rules('vouchertype', 'Voucher Type', 'trim|required');
        if($voucherType!= null) {
            $this->form_validation->set_rules('pvtype', 'Payee Type', 'trim|required');
        }
        $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('PVdate', 'Payment Voucher Date', 'trim|required|validate_date');
        if ($voucherType == 'Direct') {
            /*$this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');
            $this->form_validation->set_rules('narration', 'Narration', 'trim|required');*/
        }

        //$this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('PVbankCode', 'Bank Code', 'trim|required');
        if ($financeyearperiodYN == 1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }

        if ($voucherType == 'Supplier' || $voucherType == 'SupplierAdvance' || $voucherType == 'SupplierDebitNote' || $voucherType == 'SupplierInvoice' || $voucherType == 'SupplierItem' || $voucherType == 'SupplierExpense') {
            $this->form_validation->set_rules('partyID', 'Supplier', 'trim|required');
        } elseif ($voucherType == 'Direct' || $voucherType == 'DirectItem' || $voucherType == 'DirectExpense' || $voucherType == 'PurchaseRequest') {
            $this->form_validation->set_rules('partyName', 'Payee Name', 'trim|required');
        } elseif ($voucherType == 'Employee' || $voucherType == 'EmployeeExpense' || $voucherType == 'EmployeeItem') {
            $this->form_validation->set_rules('partyID', 'Employee Name', 'trim|required');
        } elseif ($voucherType == 'SC') {
            $this->form_validation->set_rules('partyID', 'Sales Person', 'trim|required');
        }
        $bank_detail = fetch_gl_account_desc($this->input->post('PVbankCode'));

        if (is_array($bank_detail) && $bank_detail['isCash'] == 0) {
            //$this->form_validation->set_rules('PVchequeNo', 'Cheque Number', 'trim|required');
           /* if ($voucherType == 'Supplier') {
                $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
            }*/
            $this->form_validation->set_rules('paymentType', 'Payment Type', 'trim|required');
            if ($this->input->post('paymentType') == 2 && ($voucherType == 'Supplier' || $voucherType == 'SupplierAdvance' || $voucherType == 'SupplierDebitNote' || $voucherType == 'SupplierInvoice' || $voucherType == 'SupplierItem' || $voucherType == 'SupplierExpense')) {
                $this->form_validation->set_rules('supplierBankMasterID', 'Supplier Bank', 'trim|required');
            } else if(($this->input->post('paymentType') == 1) && (($voucherType == 'Supplier') || $voucherType == 'SupplierAdvance' || $voucherType == 'SupplierDebitNote' || $voucherType == 'SupplierInvoice' || $voucherType == 'SupplierItem' || $voucherType == 'SupplierExpense' || ($voucherType == 'Direct' || $voucherType == 'DirectItem' || $voucherType == 'DirectExpense') || ($voucherType == 'Employee') || ($voucherType == 'PurchaseRequest'))) {
                $this->form_validation->set_rules('PVchequeDate', 'Cheque Date', 'trim|required');
                $chequeRegister = getPolicyValues('CRE', 'All');

                if ($chequeRegister == 1)
                {
                    $this->form_validation->set_rules('chequeRegisterDetailID', 'Cheque Number', 'trim|required');
                }else
                {
                    $this->form_validation->set_rules('PVchequeNo', 'Cheque Number', 'trim|required');
                }
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($PVdate >= $financePeriod['dateFrom'] && $PVdate <= $financePeriod['dateTo']) {

                    if ($PVchequeDate < $PVdate && $bank_detail['isCash'] == 0 && $this->input->post('paymentType') == 1) {
                        $this->session->set_flashdata('e', 'Cheque Date Cannot be less than Payment Voucher Date  !');
                        echo json_encode(FALSE);

                    } else {
                        echo json_encode($this->Payment_voucher_model->save_paymentvoucher_header());
                    }

                } else {
                    $this->session->set_flashdata('e', 'Payment Voucher Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            } else {
                echo json_encode($this->Payment_voucher_model->save_paymentvoucher_header());
            }
        }
    }

    function save_payment_match_header()
    {
        // $this->form_validation->set_rules('PVdate', 'Payment Voucher Date', 'trim|required');
        // $this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Payment Currency', 'trim|required');
        // $this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $financeyearperiodYN = getPolicyValues('FPC', 'All');
            $where = '';
            if ($financeyearperiodYN == 1) {
                $where = " AND period.isActive = 1";
            }
            $Comp = current_companyID();
            $date_format_policy = date_format_policy();
            $format_RMdate = input_format_date($this->input->post('matchDate'), $date_format_policy);
            $financePeriod = $this->db->query("SELECT period.companyFinanceYearID as companyFinanceYearID 
            FROM srp_erp_companyfinanceperiod period WHERE period.companyID = $Comp AND '{$format_RMdate}' BETWEEN period.dateFrom AND period.dateTo {$where}")->row_array();

            if (!empty($financePeriod))
            {
                echo json_encode($this->Payment_voucher_model->save_payment_match_header());
            } else {
                if ($financeyearperiodYN == 1)
                {
                    $this->session->set_flashdata('e', 'Payment Matching Date is not between Active Financial period!');
                } else {
                    $this->session->set_flashdata('e', 'Payment Matching Date not between Financial period!');
                }
                echo json_encode(FALSE);
            }
        }
    }

    function save_direct_pv_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        //$this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_direct_pv_detail());
        }
    }

    function save_direct_pv_detail_multiple()
    {
        $gl_codes = $this->input->post('gl_code');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');
        $amount = $this->input->post('amount');
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            if($advanceCostCapturing == 1){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
            }
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }

            }
        }
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payment_voucher_model->save_direct_pv_detail_multiple());
        }
    }

    function load_payment_match_header()
    {
        echo json_encode($this->Payment_voucher_model->load_payment_match_header());
    }

    function delete_pv()
    {
        echo json_encode($this->Payment_voucher_model->delete_pv());
    }

    function delete_pv_match_detail()
    {
        echo json_encode($this->Payment_voucher_model->delete_pv_match_detail());
    }

    function fetch_pv_advance_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_pv_advance_detail());
    }

    function fetch_payment_voucher_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_payment_voucher_detail());
    }

    function delete_item_direct()
    {
        echo json_encode($this->Payment_voucher_model->delete_item_direct());
    }

    function fetch_pv_direct_details()
    {
        echo json_encode($this->Payment_voucher_model->fetch_pv_direct_details());
    }

    function load_payment_voucher_header()
    {
        echo json_encode($this->Payment_voucher_model->load_payment_voucher_header());
    }

    function load_payment_voucher_Signatures()
    {
        echo json_encode($this->Payment_voucher_model->load_payment_voucher_Signatures());
    }

    function fetch_match_detail()
    {
        echo json_encode($this->Payment_voucher_model->fetch_match_detail());
    }

    function load_pv_conformation()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $doc_type = $this->input->post('doc_type');

        //Receipt reversal doc, will generate a corresponding PV documentation.
        //Fetch
        if($doc_type == 'RRVR'){
            $receipt_reversal = $this->Receipt_reversale_model->fetch_receipt_reversal_master($payVoucherAutoId);
           
            if($receipt_reversal && isset($receipt_reversal['payVoucherAutoId'])){
                $payVoucherAutoId = $receipt_reversal['payVoucherAutoId'];
            }else{
                return false;
            }
        }

        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_template_data($payVoucherAutoId);

        $data['approval'] = $this->input->post('approval');
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($payVoucherAutoId),'PV','payVoucherAutoId');
        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PV', $payVoucherAutoId);

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
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

        $data['html'] = $this->input->post('html');

        // echo '<pre>';
        // print_r($data); exit;

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function load_sub_pv_allocation()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $doc_type = $this->input->post('doc_type');

        //Receipt reversal doc, will generate a corresponding PV documentation.
        //Fetch
        if($doc_type == 'RRVR'){
            $receipt_reversal = $this->Receipt_reversale_model->fetch_receipt_reversal_master($payVoucherAutoId);
           
            if($receipt_reversal && isset($receipt_reversal['payVoucherAutoId'])){
                $payVoucherAutoId = $receipt_reversal['payVoucherAutoId'];
            }else{
                return false;
            }
        }

        $data['extra'] = $this->Payment_voucher_model->get_pv_vendor_allocation_data($payVoucherAutoId);

        $data['approval'] = $this->input->post('approval');
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($payVoucherAutoId),'PV','payVoucherAutoId');
        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PV', $payVoucherAutoId);

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
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

        $data['html'] = $this->input->post('html');

        // echo '<pre>';
        // print_r($data); exit;

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_sub_allocation_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }


    function load_pv_conformation_buyback()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_template_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
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

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print_buyback', $data, true);
            $this->load->library('pdf');
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function load_pv_match_conformation()
    {
        $matchID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('matchID') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_match_template_data($matchID);
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_match_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            
          
            $pdf = $this->pdf->printed($html, $printSizeText);
        }
    }

    function fetch_detail()
    {
     
        update_group_based_tax('srp_erp_paymentvouchermaster','payVoucherAutoId',trim($this->input->post('PayVoucherAutoId') ?? ''),'srp_erp_paymentvouchertaxdetails','payVoucherAutoId','PV');
        $data['groupBasedTax']  = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($this->input->post('PayVoucherAutoId') ?? ''),'PV','payVoucherAutoId');
        $data['master'] = $this->Payment_voucher_model->load_payment_voucher_header();

        if ($data['master']['pvType'] == 'Supplier' || $data['master']['pvType'] == 'SupplierItem' || $data['master']['pvType'] == 'SupplierItem' || $data['master']['pvType'] == 'SupplierInvoice' || $data['master']['pvType'] == 'SupplierAdvance' || $data['master']['pvType'] == 'SupplierDebitNote') {
            $data['supplier_po'] = $this->Payment_voucher_model->fetch_supplier_po($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['supplier_inv'] = $this->Payment_voucher_model->fetch_supplier_inv($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['debit_note'] = $this->Payment_voucher_model->fetch_debit_note($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['customer_inv'] = $this->Payment_voucher_model->fetch_customer_inv($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
        }

        if ($data['master']['pvType'] == 'SC') {
            $data['sales_commission'] = $this->Payment_voucher_model->fetch_sales_person($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['payVoucherAutoId']);
        }
    

        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['totalamountreceipt'] = $this->Payment_voucher_model->totalamountreceipt($this->input->post('PayVoucherAutoId'));
        $data['pvType'] = $data['master']['pvType'];
        $data['partyID'] = $data['master']['partyID'];
        $data['gl_code_arr'] = dropdown_all_revenue_gl();
        $data['gl_code_arr_income'] = dropdown_all_revenue_gl();
        $data['segment_arr'] = fetch_segment();
        $data['tab'] = $this->input->post('tab');
        $data['detail'] = $this->Payment_voucher_model->fetch_detail();
        
        $this->load->view('system/payment_voucher/payment_voucher_detail', $data);
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_inv_tax_detail());
        }
    }

    function save_sales_rep_payment()
    {
        $this->form_validation->set_rules('salesPersonID', 'Sales Person', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('transactionAmount', 'Payment Amount', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_sales_rep_payment());
        }
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Payment_voucher_model->delete_tax_detail());
    }

    function fetch_detail_header_lock()
    {

        echo json_encode($this->Payment_voucher_model->fetch_detail());
    }

    function load_html()
    {
        //onchange="fetch_supplier_currency(this.value)"
        $pageid = $this->input->post('masterid');
        $select_value = trim($this->input->post('select_value') ?? '');
        $supplier_arr = all_supplier_drop(true,1);
        $createmasterrecords = getPolicyValues('CMR','All');
        $SupplierID = $this->input->post('SupplierID');
        $supid = '';
        if($SupplierID)
        {
            $supid = $SupplierID;
        }

        $data_arr = array(''=>'Select Supplier');
        if ((trim($this->input->post('value') ?? '') == 'Employee')||(trim($this->input->post('value') ?? '') == 'EmployeeItem')||(trim($this->input->post('value') ?? '') == 'EmployeeExpense')) {
            $emp_list = $this->employee_with_bank_data();
            $str = '<select name="partyID" class="form-control select2" id="partyID" onchange="update_bank_transferDet()">
                      <option value="" selected="selected">Select Employee</option>';
            $str .= $emp_list;
            $str .= '</select>';

            echo $str;
            //echo form_dropdown('partyID', all_employee_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } elseif (trim($this->input->post('value') ?? '') == 'Sales Rep') {
            echo form_dropdown('partyID', all_srp_erp_sales_person_drop(), $select_value, 'class="form-control select2" id="partyID"');
        } else {

              if($createmasterrecords==1){
                  if($pageid) {
                      $Documentid = 'PV';
                      $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pageid, $Documentid);
                      if ($supplieridcurrentdoc && $supplieridcurrentdoc['isActive'] == 0) {
                          $data_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

                      }
                  }
                  $companyID = $this->common_data['company_data']['company_id'];
                  $supplierqry = "SELECT supplierAutoID,supplierName,supplierSystemCode,supplierCountry FROM srp_erp_suppliermaster WHERE companyID = {$companyID} AND isActive = 1";
                  $supplierMaster = $this->db->query($supplierqry)->result_array();
                  if (!empty($supplierMaster)) {
                      foreach ($supplierMaster as $row) {
                          $data_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
                      }
                  }


                echo '<div class="input-group">
                        <div id="div_supplier_drop">';
                  echo form_dropdown('partyID', $data_arr, $supid, 'class="form-control select2" id="partyID" onchange="fetch_supplier_currency_by_id(this.value)"');
                  echo ' </div>
                        <span class="input-group-btn">
                        <button class="btn btn-default " type="button" title="Add Supplier" rel="tooltip" onclick="link_employee_model()" id="addcustomer"
                                style="height: 27px; padding: 2px 10px;"><i class="fa fa-plus"></i></button>
                        </span>
                    </div>';

              }else
              {
                  if($pageid)
                  {
                      $Documentid = 'PV';
                      $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($pageid,$Documentid);
                      if($supplieridcurrentdoc && $supplieridcurrentdoc['isActive'] == 0)
                      {
                          $supplier_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');

                      }

                      echo form_dropdown('partyID',$supplier_arr, $select_value, 'class="form-control select2" id="partyID" required onchange="fetch_supplier_currency_by_id(this.value)"');
                  }else
                  {
                      echo form_dropdown('partyID',$supplier_arr, $select_value, 'class="form-control select2" id="partyID" required onchange="fetch_supplier_currency_by_id(this.value)"');
                  }
              }




        }
    }

    function fetch_payment_voucher_approval()
    {
        /*                 * rejected = 1
                 * not rejected = 0
                 * */
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $approvedYN = $this->input->post('approvedYN');
        $currentuserid = current_userID();

        $company_doc_approval_type = getApprovalTypesONDocumentCode('PV',$companyID);
       
        $approvalBasedWhere='';

        // if($company_doc_approval_type['approvalType']==1){

        // }else if($company_doc_approval_type['approvalType']==2){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount';
        // }else if($company_doc_approval_type['approvalType']==3){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        // }else if($company_doc_approval_type['approvalType']==4){
        //     $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        // }

        if($company_doc_approval_type['approvalType']==1){

        }else if($company_doc_approval_type['approvalType']==2){
           // $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount';
           $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1))";
        }else if($company_doc_approval_type['approvalType']==3){
            $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        }else if($company_doc_approval_type['approvalType']==4){
            //$approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID";
        }

        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyID . $approvalBasedWhere."";
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)-IFNULL(income_section.transactionAmount,0) - IFNULL(customerInv.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,case pvType when \'Direct\' OR \'DirectItem\' OR \'DirectExpense\' then partyName when \'Employee\' OR \'EmployeeExpense\' OR \'EmployeeItem\' then srp_employeesdetails.Ename2 when \'Supplier\' OR \'SupplierAdvance\' OR \'SupplierInvoice\' OR \'SupplierItem\' OR \'SupplierExpense\' then srp_erp_suppliermaster.supplierName end as partyName', false);
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" AND srp_erp_paymentvoucherdetail.type!="INGL" AND srp_erp_paymentvoucherdetail.detailInvoiceType IS NULL GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="INGL" GROUP BY payVoucherAutoId) income_section', '(income_section.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,SUM(taxPercentage) as taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.detailInvoiceType="CUS" GROUP BY payVoucherAutoId) customerInv', '(customerInv.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');

            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'PV');
            $this->datatables->where('pvType <>', 'SC');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            //$this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->where( $where);
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,case pvType when \'Direct\' OR \'DirectItem\' OR \'DirectExpense\' then partyName when \'Employee\' OR \'EmployeeExpense\' OR \'EmployeeItem\' then srp_employeesdetails.Ename2 when \'Supplier\' OR \'SupplierAdvance\' OR \'SupplierInvoice\' OR \'SupplierItem\' OR \'SupplierExpense\' THEN srp_erp_suppliermaster.supplierName end as partyName', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,SUM(taxPercentage) as taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('pvType <>', 'SC');
           // $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->where( $where);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_paymentvouchermaster.payVoucherAutoId');
            $this->datatables->group_by('srp_erp_documentapproved.approvedEmpID');

            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function fetch_commission_payment_approval()
    {
        /*                 * rejected = 1
                 * not rejected = 0
                 * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,partyName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'PV');
            $this->datatables->where('pvType', 'SC');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,partyName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value,(IFNULL(addondet.transactionAmount,0)+IFNULL(det.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('pvType', 'SC');
            $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_paymentvouchermaster.payVoucherAutoId');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');

            echo $this->datatables->generate();
        }

    }


    function save_pv_item_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $this->form_validation->set_rules("batch_number[]", 'Batch Number', 'trim|required');
        }

        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($advanceCostCapturing == 1){
            $this->form_validation->set_rules("activityCode", 'Activity Code', 'required|trim');
        }

        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }

        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail());
        }
    }

    function save_pv_item_detail_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item 1', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');

            $advanceCostCapturing = getPolicyValues('ACC', 'All');
            if($advanceCostCapturing == 1){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }
            
            if ($projectExist == 1  && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }

            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail_multiple());
        }

    }

    function payment_confirmation()
    {
        echo json_encode($this->Payment_voucher_model->payment_confirmation());
    }

    function payment_match_confirmation()
    {
        echo json_encode($this->Payment_voucher_model->payment_match_confirmation());
    }

    function delete_payment_match()
    {
        echo json_encode($this->Payment_voucher_model->delete_payment_match());
    }

    function save_inv_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_inv_base_items());
    }

    function save_debitNote_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_debitNote_base_items());
    }

    function delete_payment_voucher()
    {
        echo json_encode($this->Payment_voucher_model->delete_payment_voucher());
    }


    function save_pv_approval()
    {
        $system_code = trim($this->input->post('payVoucherAutoId') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $companyid = current_companyID();
        $currentdate = current_date(false);
        $PostDatedChequeManagement = getPolicyValues('PDC', 'All'); // policy for post dated cheque

        $mastertbl = $this->db->query("SELECT PVdate,PVchequeDate FROM `srp_erp_paymentvouchermaster` where companyID = $companyid And payVoucherAutoId = $system_code ")->row_array();
        $mastertbldetail = $this->db->query("SELECT payVoucherAutoId  FROM `srp_erp_paymentvoucherdetail` where companyID = $companyid And type = 'Item' And payVoucherAutoId = $system_code")->row_array();

        if ($PostDatedChequeManagement == 1 && ($mastertbl['PVchequeDate'] != '' || !empty($mastertbl['PVchequeDate'])) && (empty($mastertbldetail['payVoucherAutoId']) || $mastertbldetail['payVoucherAutoId']==' ') && $status == 1) {

            if ($mastertbl['PVchequeDate'] > $mastertbl['PVdate']) {
                if ($currentdate >= $mastertbl['PVchequeDate']) {
                    if ($status == 1) {
                        $approvedYN = checkApproved($system_code, 'PV', $level_id);
                        if ($approvedYN) {
                            $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                            echo json_encode(FALSE);
                        } else {
                            $this->db->select('payVoucherAutoId');
                            $this->db->where('payVoucherAutoId', trim($system_code));
                            $this->db->where('confirmedYN', 2);
                            $this->db->from('srp_erp_paymentvouchermaster');
                            $po_approved = $this->db->get()->row_array();
                            if (!empty($po_approved)) {
                                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                                echo json_encode(FALSE);
                            } else {
                                $this->form_validation->set_rules('status', 'Status', 'trim|required');
                                if ($this->input->post('status') == 2) {
                                    $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                                }
                                $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                                $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                                if ($this->form_validation->run() == FALSE) {
                                    $this->session->set_flashdata($msgtype = 'e', validation_errors());
                                    echo json_encode(FALSE);
                                } else {
                                    echo json_encode($this->Payment_voucher_model->save_pv_approval());
                                }
                            }
                        }
                    } else if ($status == 2) {
                        $this->db->select('payVoucherAutoId');
                        $this->db->where('payVoucherAutoId', trim($system_code));
                        $this->db->where('confirmedYN', 2);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $po_approved = $this->db->get()->row_array();
                        if (!empty($po_approved)) {
                            $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                            echo json_encode(FALSE);
                        } else {
                            $rejectYN = checkApproved($system_code, 'PV', $level_id);
                            if (!empty($rejectYN)) {
                                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                                echo json_encode(FALSE);
                            } else {
                                $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                                if ($this->input->post('status') == 2) {
                                    $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                                }
                                $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                                $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                                if ($this->form_validation->run() == FALSE) {
                                    $this->session->set_flashdata($msgtype = 'e', validation_errors());
                                    echo json_encode(FALSE);
                                } else {
                                    echo json_encode($this->Payment_voucher_model->save_pv_approval());
                                }
                            }
                        }
                    }
                } else {
                    $this->session->set_flashdata('e', 'This is a post dated cheque document. you cannot approve this document before the cheque date.');
                    echo json_encode(FALSE);
                }


            }else
            {
                if ($status == 1) {
                    $approvedYN = checkApproved($system_code, 'PV', $level_id);
                    if ($approvedYN) {
                        $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                        echo json_encode(FALSE);
                    } else {
                        $this->db->select('payVoucherAutoId');
                        $this->db->where('payVoucherAutoId', trim($system_code));
                        $this->db->where('confirmedYN', 2);
                        $this->db->from('srp_erp_paymentvouchermaster');
                        $po_approved = $this->db->get()->row_array();
                        if (!empty($po_approved)) {
                            $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                            echo json_encode(FALSE);
                        } else {
                            $this->form_validation->set_rules('status', 'Status', 'trim|required');
                            if ($this->input->post('status') == 2) {
                                $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                            }
                            $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                            $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                            if ($this->form_validation->run() == FALSE) {
                                $this->session->set_flashdata($msgtype = 'e', validation_errors());
                                echo json_encode(FALSE);
                            } else {
                                echo json_encode($this->Payment_voucher_model->save_pv_approval());
                            }
                        }
                    }
                } else if ($status == 2) {
                    $this->db->select('payVoucherAutoId');
                    $this->db->where('payVoucherAutoId', trim($system_code));
                    $this->db->where('confirmedYN', 2);
                    $this->db->from('srp_erp_paymentvouchermaster');
                    $po_approved = $this->db->get()->row_array();
                    if (!empty($po_approved)) {
                        $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                        echo json_encode(FALSE);
                    } else {
                        $rejectYN = checkApproved($system_code, 'PV', $level_id);
                        if (!empty($rejectYN)) {
                            $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                            echo json_encode(FALSE);
                        } else {
                            $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                            if ($this->input->post('status') == 2) {
                                $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                            }
                            $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                            $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                            if ($this->form_validation->run() == FALSE) {
                                $this->session->set_flashdata($msgtype = 'e', validation_errors());
                                echo json_encode(FALSE);
                            } else {
                                echo json_encode($this->Payment_voucher_model->save_pv_approval());
                            }
                        }
                    }
                }
            }


        }




        else {

            if ($status == 1) {
                $approvedYN = checkApproved($system_code, 'PV', $level_id);
                if ($approvedYN) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->db->select('payVoucherAutoId');
                    $this->db->where('payVoucherAutoId', trim($system_code));
                    $this->db->where('confirmedYN', 2);
                    $this->db->from('srp_erp_paymentvouchermaster');
                    $po_approved = $this->db->get()->row_array();
                    if (!empty($po_approved)) {
                        $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                        echo json_encode(FALSE);
                    } else {
                        $this->form_validation->set_rules('status', 'Status', 'trim|required');
                        if ($this->input->post('status') == 2) {
                            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                        }
                        $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                        if ($this->form_validation->run() == FALSE) {
                            $this->session->set_flashdata($msgtype = 'e', validation_errors());
                            echo json_encode(FALSE);
                        } else {
                            echo json_encode($this->Payment_voucher_model->save_pv_approval());
                        }
                    }
                }
            } else if ($status == 2) {
                $this->db->select('payVoucherAutoId');
                $this->db->where('payVoucherAutoId', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_paymentvouchermaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $rejectYN = checkApproved($system_code, 'PV', $level_id);
                    if (!empty($rejectYN)) {
                        $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                        echo json_encode(FALSE);
                    } else {
                        $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                        if ($this->input->post('status') == 2) {
                            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                        }
                        $this->form_validation->set_rules('payVoucherAutoId', 'Payment Voucher ID ', 'trim|required');
                        $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                        if ($this->form_validation->run() == FALSE) {
                            $this->session->set_flashdata($msgtype = 'e', validation_errors());
                            echo json_encode(FALSE);
                        } else {
                            echo json_encode($this->Payment_voucher_model->save_pv_approval());
                        }
                    }
                }
            }
        }
    }

    function save_pv_po_detail()
    {
        /*$this->form_validation->set_rules('po_code', 'PO Code', 'trim|required');*/
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_po_detail());
        }
    }

    function referback_payment_voucher()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        $this->db->select('approvedYN,PVcode,isSytemGenerated,confirmedYN');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        /*$this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);*/
        $this->db->from('srp_erp_paymentvouchermaster');
        $doc_status = $this->db->get()->row_array();
        if ($doc_status['approvedYN'] == 1) {
            echo json_encode(array('e', 'The document already approved - ' . $doc_status['PVcode']));
        }
        else if ($doc_status['confirmedYN'] == 0) {
            echo json_encode(['e', 'This document not confirmed yet.Please refresh the page and try again.']);
        }
        else if ($doc_status['isSytemGenerated'] == 1) {
            echo json_encode(['e', 'This is System Generated Document,You Cannot Refer Back this document']);
        }
        else {
           /* echo json_encode(array('e', 'er', $doc_status));
            die();*/
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($payVoucherAutoId, 'PV');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function save_match_amount()
    {
        $this->form_validation->set_rules('matchID', 'Match ID', 'trim|required');
        $amounts = $this->input->post('amounts');
        foreach ($amounts as $key => $amount) {
            $this->form_validation->set_rules("amounts[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("InvoiceAutoID[{$key}]", 'Invoice', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'messsage' => validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_match_amount());
        }
    }

    function referback_payment_match()
    {
        $matchID = $this->input->post('matchID');

        $data['confirmedYN'] = 3;
        $data['confirmedByEmpID'] = NULL;
        $data['confirmedByName'] = NULL;
        $data['confirmedDate'] = NULL;
        $this->db->where('matchID', $matchID);
        $result = $this->db->update('srp_erp_pvadvancematch', $data);
        $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $matchID, 'documentID' => 'PVM', 'companyID' => $this->common_data['company_data']['company_id']));

        if ($result) {
            echo json_encode(array('s', ' Referred Back Successfully.', $result));
        } else {
            echo json_encode(array('e', ' Error in refer back.', $result));
        }
    }

    function save_commission_base_items()
    {
        echo json_encode($this->Payment_voucher_model->save_commission_base_items());
    }

    function re_open_commisionpayment()
    {
        echo json_encode($this->Payment_voucher_model->re_open_commisionpayment());
    }

    function re_open_payment_voucher()
    {
        echo json_encode($this->Payment_voucher_model->re_open_payment_voucher());
    }

    function re_open_payment_match()
    {
        echo json_encode($this->Payment_voucher_model->re_open_payment_match());
    }

    function cheque_print()
    {

        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $coaChequeTemplateID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('coaChequeTemplateID') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_cheque_data($payVoucherAutoId);

        $this->db->select('pageLink');
        $this->db->where('coaChequeTemplateID', $coaChequeTemplateID);
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $pagelink = $this->db->get()->row_array();

        $this->load->library('NumberToWords');
        $html = $this->load->view('system/payment_voucher/' . $pagelink['pageLink'] . '', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', '1', null, 0);

    }

    function cheque_print_rak()
    {

        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $coaChequeTemplateID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('coaChequeTemplateID') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_cheque_data($payVoucherAutoId);

        $this->db->select('pageLink');
        $this->db->where('coaChequeTemplateID', $coaChequeTemplateID);
        $this->db->from('srp_erp_chartofaccountchequetemplates');
        $pagelink = $this->db->get()->row_array();

        $this->load->library('NumberToWords');
        $html = $this->load->view('system/payment_voucher/' . $pagelink['pageLink'] . '', $data, true);
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', '1', null, 0);

    }

    function load_Cheque_templates()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');

        $data['extra'] = $this->Payment_voucher_model->load_Cheque_templates($payVoucherAutoId);
        $html = $this->load->view('system/payment_voucher/ajax-erp_load_Cheque_templates', $data, true);
        echo $html;
    }

    function get_po_amount()
    {
        echo json_encode($this->Payment_voucher_model->get_po_amount());
    }

    function get_supplier_banks()
    {
        echo json_encode($this->Payment_voucher_model->get_supplier_banks());
    }
/**SMSD */
    function fetch_signature_authority()
    {
        echo json_encode($this->Payment_voucher_model->fetch_signature_authority());
    }

    
    function fetch_signature_authority_on_pv()
    {
        echo json_encode($this->Payment_voucher_model->fetch_signature_authority_on_pv());
    }
/**SMSD */
    function save_signature_authority_pv()
    {
        echo json_encode($this->Payment_voucher_model->save_signature_authority_pv());
    }

    function load_pv_bank_transfer()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_transfer_data($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payment_voucher_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $this->load->library('NumberToWords');
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_transfer_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], null, 0);
        }
    }

    function load_supplier_invoice_conformation()
    {
        $InvoiceAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('InvoiceAutoID') ?? '');
        $data['extra'] = $this->Payable_modal->fetch_supplier_invoice_template_data($InvoiceAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Payable_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/accounts_payable/erp_supplier_invoice_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function paymentvoucher_collectionheader()
    {
        echo json_encode($this->Payment_voucher_model->paymentvoucher_collectionheader());
    }

    function update_paymentvoucher_collectiondetails()
    {

        $this->form_validation->set_rules('payVoucherAutoIdpv', 'Payment Voucher', 'trim|required');
        $status = trim($this->input->post('statuspv') ?? '');

        if ($status == 1) {
            $this->form_validation->set_rules('colectedbyemp', 'Collected Employee', 'trim|required');
            $this->form_validation->set_rules('collectiondatepv', 'Collected Date', 'trim|required');
            // $this->form_validation->set_rules('commentpv', 'Comment', 'trim|required');
        }/* else if ($status == 2)
        {
            $this->form_validation->set_rules('commentpvonhold', 'Comment', 'trim|required');
        }*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->update_paymentvoucher_collectiondetails());
        }
    }

    function showBalanceAmount_matching()
    {
        echo json_encode($this->Payment_voucher_model->showBalanceAmount_matching());
    }

    function fetch_prq_code(){
        echo json_encode($this->Payment_voucher_model->fetch_prq_code());
    }

    function fetch_prq_detail_table(){
        echo json_encode($this->Payment_voucher_model->fetch_prq_detail_table());
    }

    function save_prq_base_items(){
        echo json_encode($this->Payment_voucher_model->save_prq_base_items());
    }

    function fetch_purchase_request_based_detail(){
        echo json_encode($this->Payment_voucher_model->fetch_purchase_request_based_detail());
    }

    function update_purchase_request_detail()
    {
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('wareHouseAutoID', 'Warehouse', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Payment_voucher_model->update_purchase_request_detail());
        }
    }

    function fetch_payment_voucher_suom()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $collectionstatus = $this->input->post('collectionstatus');
        $supplier_filter = '';
        $collection_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND partyID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( PVdate >= '" . $datefromconvert . " 00:00:00' AND PVdate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        if (!empty($collectionstatus)) {
            if ($collectionstatus == 1) {
                $collection_filter = " AND (collectedStatus = 1 AND approvedYN = 1)";
            } else if ($collectionstatus == 2) {
                $collection_filter = " AND (collectedStatus = 2 AND approvedYN = 1)";
            } else if ($collectionstatus == 3) {
                $collection_filter = " AND (collectedStatus = 0  AND approvedYN = 1)";
            }
        }


        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( PVcode Like '%$search%' ESCAPE '!') OR ( pvType Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%') OR (det.transactionAmount Like '%$sSearch%') OR (PVNarration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (PVdate Like '%$sSearch%')) ";
        }


        $where = "srp_erp_paymentvouchermaster.companyID = " . $companyid . $supplier_filter . $date . $status_filter . $searches . $collection_filter . "";
        $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as payVoucherAutoId,collectedStatus,PVNarration,PVcode,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,confirmedYN,approvedYN,srp_erp_paymentvouchermaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,pvType,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+IFNULL(det.transactionAmount,0)-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,srp_erp_paymentvouchermaster.isDeleted as isDeleted,bankGLAutoID,case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'PurchaseRequest\' then partyName when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName,paymentType,srp_erp_paymentvouchermaster.confirmedByEmpID as confirmedByEmp,srp_erp_paymentvouchermaster.collectedStatus as collectedStatus,srp_erp_paymentvouchermaster.isSytemGenerated as isSytemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        //$this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->join('(SELECT SUM(taxPercentage) as taxPercentage ,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('pvType <>', 'SC');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->datatables->from('srp_erp_paymentvouchermaster');
        $this->datatables->add_column('pv_detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $5 <br> <b>Comments : </b> $1 ', 'PVNarration,partyName,PVdate,transactionCurrency,pvType');
        //$this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'payment_voucher_total_value(payVoucherAutoId,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PV",payVoucherAutoId)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PV",payVoucherAutoId,null,collectedStatus)');
        $this->datatables->add_column('edit', '$1', 'load_pv_action_som(payVoucherAutoId,confirmedYN,approvedYN,createdUser,PV,isDeleted,bankGLAutoID,paymentType,pvType,confirmedByEmp,isSytemGenerated)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function fetch_detail_suom()
    {
        $data['master'] = $this->Payment_voucher_model->load_payment_voucher_header();
        if ($data['master']['pvType'] == 'Supplier') {
            $data['supplier_po'] = $this->Payment_voucher_model->fetch_supplier_po($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['supplier_inv'] = $this->Payment_voucher_model->fetch_supplier_inv($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
            $data['debit_note'] = $this->Payment_voucher_model->fetch_debit_note($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['PVdate']);
        }
        if ($data['master']['pvType'] == 'SC') {
            $data['sales_commission'] = $this->Payment_voucher_model->fetch_sales_person($data['master']['partyID'], $data['master']['transactionCurrencyID'], $data['master']['payVoucherAutoId']);
        }

        $data['payVoucherAutoId'] = trim($this->input->post('payVoucherAutoId') ?? '');
        $data['pvType'] = $data['master']['pvType'];
        $data['partyID'] = $data['master']['partyID'];
        $data['gl_code_arr'] = dropdown_all_revenue_gl();
        $data['gl_code_arr_income'] = dropdown_all_revenue_gl();
        $data['segment_arr'] = fetch_segment();
        $data['tab'] = $this->input->post('tab');
        $data['detail'] = $this->Payment_voucher_model->fetch_detail();
        $this->load->view('system/payment_voucher/payment_voucher_detail_suom', $data);
    }

    function fetch_sec_uom_dtls(){
        $uomid=$this->input->post('secondaryUOMID');
        $this->db->SELECT("UnitID,UnitDes,UnitShortCode");
        $this->db->FROM('srp_erp_unit_of_measure');
        $this->db->WHERE('UnitID', $uomid);
        $units = $this->db->get()->row_array();

        echo json_encode($units);
    }

    function fetch_pv_direct_details_suom()
    {
        echo json_encode($this->Payment_voucher_model->fetch_pv_direct_details_suom());
    }

    function fetch_payment_voucher_detail_suom()
    {
        echo json_encode($this->Payment_voucher_model->fetch_payment_voucher_detail_suom());
    }


    function load_pv_conformation_suom()
    {
        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_template_data_suom($payVoucherAutoId);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PV', $payVoucherAutoId);
        
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
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

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_print_suom', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'],  $printHeaderFooterYN);
        }
    }


    function fetch_payment_voucher_approval_suom()
    {
        /*                 * rejected = 1
                 * not rejected = 0
                 * */
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $approvedYN = $this->input->post('approvedYN');
        $currentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+(IFNULL(det.transactionAmount,0) + IFNULL(det.taxAmount,0))-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+(IFNULL(det.transactionAmount,0) + IFNULL(det.taxAmount,0))-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName, srp_erp_paymentvouchermaster.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,SUM(taxAmount) as taxAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,SUM(taxPercentage) as taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId AND srp_erp_documentapproved.approvalLevelID = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_paymentvouchermaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'PV');
            $this->datatables->where('pvType <>', 'SC');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->add_column('details', '$1 <br><b>Ref No :</b>$2', 'PVNarration,referenceNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval_suom(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_paymentvouchermaster.payVoucherAutoId as PayVoucherAutoId,PVcode,PVNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(PVdate,\'' . $convertFormat . '\') AS PVdate,transactionCurrency,transactionCurrencyDecimalPlaces,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+(IFNULL(det.transactionAmount,0) + IFNULL(det.taxAmount,0))-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value,(((IFNULL(addondet.taxPercentage,0)/100)*IFNULL(tyepdet.transactionAmount,0))+(IFNULL(det.transactionAmount,0) + IFNULL(det.taxAmount,0))-IFNULL(debitnote.transactionAmount,0)-IFNULL(SR.transactionAmount,0)) as total_value_search,case pvType when \'Direct\' then partyName when \'Employee\' then srp_employeesdetails.Ename2 when \'Supplier\' then srp_erp_suppliermaster.supplierName end as partyName, srp_erp_paymentvouchermaster.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId,SUM(taxAmount) as taxAmount FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type!="debitnote" AND srp_erp_paymentvoucherdetail.type!="SR" GROUP BY payVoucherAutoId) det', '(det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="GL" OR srp_erp_paymentvoucherdetail.type="Item" OR srp_erp_paymentvoucherdetail.type="PRQ"  GROUP BY payVoucherAutoId) tyepdet', '(tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="debitnote" GROUP BY payVoucherAutoId) debitnote', '(debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type="SR" GROUP BY payVoucherAutoId) SR', '(SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount ,SUM(taxPercentage) as taxPercentage,payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails  GROUP BY payVoucherAutoId) addondet', '(addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId)', 'left');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_paymentvouchermaster.partyID', 'left');
            $this->datatables->from('srp_erp_paymentvouchermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_paymentvouchermaster.PayVoucherAutoId');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'PV');
            $this->datatables->where('pvType <>', 'SC');
            $this->datatables->where('srp_erp_paymentvouchermaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_paymentvouchermaster.payVoucherAutoId');
            $this->datatables->group_by('srp_erp_documentapproved.approvedEmpID');
            $this->datatables->add_column('details', '$1 <br><b>Ref No :</b>$2', 'PVNarration,referenceNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('PVcode', '$1', 'approval_change_modal(PVcode,PayVoucherAutoId,documentApprovedID,approvalLevelID,approvedYN,PV,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PV",PayVoucherAutoId)');
            $this->datatables->add_column('edit', '$1', 'pv_action_approval_suom(PayVoucherAutoId,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_pv_item_detail_multiple_suom()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMID = $this->input->post('SUOMIDhn');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item 1', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("wareHouseAutoID[{$key}]", 'Warehouse', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Amount', 'trim|required');
            //$this->form_validation->set_rules("SUOMIDhn[{$key}]", 'Secondary Unit Of Measure', 'trim|required');
            if(!empty($this->input->post("SUOMIDhn[$key]"))){
                $this->form_validation->set_rules("SUOMQty[{$key}]", 'Secondary QTY', 'trim|required|greater_than[0]');
            }
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail_multiple());
        }
    }

    function save_pv_item_detail_suom()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules("wareHouseAutoID", 'Warehouse', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required');
        $this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required');
        if(!empty($this->input->post("SUOMIDhn"))){
            $this->form_validation->set_rules('SUOMQty', 'Secondary Quantity', 'trim|required');
        }
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_pv_item_detail());
        }
    }

    function check_cheque_used(){
        echo json_encode($this->Payment_voucher_model->check_cheque_used());
    }

    function employee_with_bank_data(){
        $companyID = current_companyID();
        $data = $this->db->query("SELECT EIdNo, ECode, Ename2, bankAcc.*
                        FROM srp_employeesdetails AS empTB
                        LEFT JOIN (
                            SELECT empID, acc.bankID, accountNo, accountHolderName, bankName, bankSwiftCode 
                            FROM srp_erp_pay_bankmaster AS bnk 
                            JOIN (
                                SELECT employeeNo AS empID, bankID, accountNo, accountHolderName
                                FROM srp_erp_pay_salaryaccounts WHERE companyID = {$companyID} AND isActive = 1
                                GROUP BY employeeNo
                            ) AS acc ON acc.bankID=bnk.bankID
                        )  bankAcc ON bankAcc.empID = empTB.EIdNo
                        WHERE Erp_companyID = {$companyID} AND isPayrollEmployee = 1 AND isDischarged = 0")->result_array();
        $str = '';
        if (!empty($data)) {
            foreach ($data as $row) {
                $attr = 'data-beneficiary="'.$row['accountHolderName'].'" data-bank="'.$row['bankName'].'" data-acc="'.$row['accountNo'].'"  data-swift="'.$row['bankSwiftCode'].'"';
                $str .= '<option value="'.$row['EIdNo'].'" '.$attr.'> '.$row['ECode'].' | '.$row['Ename2'].' | '.$row['bankSwiftCode'].'</option>';
            }
        }

        return $str;
    }

    function fetch_expense_claim_code()
    {
        echo json_encode($this->Payment_voucher_model->fetch_expense_claim_code());
    }

    function fetch_expense_gl_code()
    {
        echo json_encode($this->Payment_voucher_model->fetch_expense_gl_code());
    }

    function fetch_provision_amount()
    {
        echo json_encode($this->Payment_voucher_model->fetch_provision_amount());
    }

    function fetch_expense_claim_details()
    {
        echo json_encode($this->Payment_voucher_model->fetch_expense_claim_details());
    }

    function save_emp_expense_multiple()
    {
        $docTypeIDs = $this->input->post('docTypeID');
        $projectExist = project_is_exist();
        $cat_mandetory = Project_Subcategory_is_exist();
        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        foreach ($docTypeIDs as $key => $docTypeID) {
            $documentID = substr($docTypeIDs[$key], 0, 2);
            if ($documentID == 'EC') {
                $this->form_validation->set_rules("expenseClaimMasterAutoID[{$key}]", 'Expense Claim Code', 'required|trim');
//                $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
                $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
                $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
                if ($projectExist == 1) {
                    $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                    if($cat_mandetory == 1) {
                        $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                    }
                }
                if($advanceCostCapturing == 1){
                    $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
                }

            } else if ($documentID == 'GL') {
                $this->form_validation->set_rules("gl_code[{$key}]", 'GL Code', 'required|trim');
                $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
                $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
                $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
                if ($projectExist == 1) {
                    $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                    if($cat_mandetory == 1) {
                        $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                    }
                }
                if($advanceCostCapturing == 1){
                    $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
                }
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->save_emp_expense_multiple());
        }
    }

    function delete_pv_expense_claim_detail()
    {
        echo json_encode($this->Payment_voucher_model->delete_pv_expense_claim_detail());
    }

    function fetch_payment_match_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( matchDate >= '" . $datefromconvert . " 00:00:00' AND matchDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1)";
            }else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2) or (confirmedYN = 3))";
            }
        }
        $where = "srp_erp_pvadvancematch.companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $this->datatables->select('srp_erp_pvadvancematch.matchID as matchID,DATE_FORMAT(matchDate,\'' . $convertFormat . '\') AS matchDate ,matchSystemCode,refNo,Narration,srp_erp_suppliermaster.supplierName as supliermastername,transactionCurrency ,transactionCurrencyDecimalPlaces,confirmedYN,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_pvadvancematch.confirmedByEmpID as confirmedByEmp,srp_erp_pvadvancematch.createdUserID as createdUser, srp_erp_pvadvancematch.refNo AS refNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,matchID FROM srp_erp_pvadvancematchdetails GROUP BY matchID) det', '(det.matchID = srp_erp_pvadvancematch.matchID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_pvadvancematch');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_pvadvancematch.supplierID');
        $this->datatables->add_column('detail', '<b>Supplier Name : </b> $2 <br> <b>Voucher Date : </b> $3  <b>  <br>  <b>Comments : </b> $1 <br>  <b>Ref No : </b> $5', 'Narration,supliermastername,matchDate,transactionCurrency,refNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_approval(confirmedYN)');
        $this->datatables->add_column('edit', '$1', 'load_pvm_action_buyback(matchID,confirmedYN,isDeleted,confirmedByEmp,createdUser)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function load_pv_match_conformation_buyback()
    {
        $matchID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('matchID') ?? '');
        $data['extra'] = $this->Payment_voucher_model->fetch_payment_voucher_match_template_data($matchID);
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_match_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            $pdf = $this->pdf->printed($html, $printSizeText);
        }
    }

    function load_email_pv()
    {
        $payVoucherAutoId = $this->input->post('payVoucherAutoId');
        $this->db->select('supplierEmail');
        $this->db->where('payVoucherAutoId', $payVoucherAutoId);
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID', 'left');
        $this->db->from('srp_erp_paymentvouchermaster ');
        $supplierEmail = $this->db->get()->row_array();

        $data['attachmentdescription'] = $this->db->query("SELECT attachmentID,myFileName as filename,attachmentDescription as description FROM `srp_erp_documentattachments` where 
	                                                    documentID = 'PV' AND documentSystemCode = $payVoucherAutoId")->result_array();

        $data['supplierEmail'] = $supplierEmail['supplierEmail'];
        $this->load->view('system/payment_voucher/erp_payment_voucher_mail_view.php', $data);
    }

    function load_mail_history(){
        $this->datatables->select('autoID,srp_erp_documentemailhistory.documentID,documentAutoID,sentByEmpID,toEmailAddress,sentDateTime,srp_employeesdetails.Ename2 as ename,srp_erp_paymentvouchermaster.PVcode')
            ->where('srp_erp_documentemailhistory.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_documentemailhistory.documentID', 'PV')
            ->where('srp_erp_documentemailhistory.documentAutoID', $this->input->post('payVoucherAutoId'))
            ->join('srp_employeesdetails','srp_erp_documentemailhistory.sentByEmpID = srp_employeesdetails.EIdNo','left')
            ->join('srp_erp_paymentvouchermaster','srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_documentemailhistory.documentAutoID','left')
            ->from('srp_erp_documentemailhistory');
        echo $this->datatables->generate();
    }

    function send_pv_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Payment_voucher_model->send_pv_email());
        }
    }
    function fetch_line_tax_and_vat_pv()
    {
        echo json_encode(fetch_line_wise_itemTaxFormulaID(trim($this->input->post('itemAutoID') ?? ''),'taxMasterAutoID','taxDescription',2));
    }
    function load_line_tax_amount_vat() { 
        $payVoucherAutoId = trim($this->input->post('payVoucherAutoId') ?? '');
        $applicableAmnt= trim($this->input->post('applicableAmnt') ?? '');
        $taxtype= trim($this->input->post('taxtype') ?? '');
        $itemAutoID= trim($this->input->post('itemAutoID') ?? '');
        $discount= trim($this->input->post('discount') ?? '');
        $return = fetch_line_wise_itemTaxcalculation($taxtype,$applicableAmnt,($discount!=''?$discount:0),'PV',$payVoucherAutoId,$this->input->post('payVoucherDetailAutoID'));
        if($return['error'] == 1) {
            $this->session->set_flashdata('e', 'Something went wrong. Please Check your Formula!');
            $amnt = 0;
        } else {
            $amnt = $return['amount'];
        }
        echo json_encode($amnt);
    }

    function fetch_line_tax_and_vat()
    {
        echo json_encode($this->Payment_voucher_model->fetch_line_tax_and_vat());
    }

    function fetch_sub_paymentvoucher(){

        // echo '<pre>';
        $invoices = $this->input->post('sub_invoices');

        $invoice_arr = explode(',',$invoices);
        $master_id = null;

        foreach($invoice_arr as $pvVoucherID){
            $this->db->where('paymentVoucherAutoID',$pvVoucherID);
            $payments = $this->db->from('srp_erp_ap_vendor_payments')->get()->row_array();

            if($payments){
                $master_id = $payments['master_id'];
            }
        }

        $data = array();
        $data['master_id'] = $master_id;

        $this->load->view('system/ap_automation/partials/ajax_vendor_wise_pv_approval', $data);
        

    }

    function fetch_allocated_payments(){

        // echo '<pre>';
        $payment_id = $this->input->post('payment_id');
        $master_id = null;

        $data = array();
        $data['payment_id'] = $payment_id;

        $this->load->view('system/ap_automation/partials/ajax_vendor_allocation_approval', $data);
        

    }


    function load_sub_invoice_allocation(){

        $payVoucherAutoId = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('payVoucherAutoId') ?? '');
        $doc_type = $this->input->post('doc_type');

        //Receipt reversal doc, will generate a corresponding PV documentation.
        //Fetch
        if($doc_type == 'RRVR'){
            $receipt_reversal = $this->Receipt_reversale_model->fetch_receipt_reversal_master($payVoucherAutoId);
           
            if($receipt_reversal && isset($receipt_reversal['payVoucherAutoId'])){
                $payVoucherAutoId = $receipt_reversal['payVoucherAutoId'];
            }else{
                return false;
            }
        }

        $data['extra'] = $this->Payment_voucher_model->get_pv_vendor_allocation_invoice_data($payVoucherAutoId);

        $data['approval'] = $this->input->post('approval');
        $data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_paymentvouchermaster',trim($payVoucherAutoId),'PV','payVoucherAutoId');
        $this->db->select('documentID');
        $this->db->where('payVoucherAutoId', trim($payVoucherAutoId));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_paymentvouchermaster');
        $documentid = $this->db->get()->row_array();

        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PV', $payVoucherAutoId);

        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $printFooterYN=1;
        $data['printFooterYN'] = $printFooterYN;

        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
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

        $data['html'] = 0;//$this->input->post('html');

        $html = $this->load->view('system/payment_voucher/erp_payment_voucher_sub_invoice_print', $data, true);
       
        $this->load->library('pdf');
        $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
    


    }
  
    function generate_receipt_voucher(){
        $payment = $this->input->post('id');
        echo json_encode($this->Payment_voucher_model->generate_receipt_voucher());
    }
}

