<?php

class Payable_modal extends ERP_Model
{
    function save_supplier_invoice_header()
    {
        $this->db->trans_start();
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $date_format_policy = date_format_policy();
        $bookDate = $this->input->post('bookingDate');
        $bookingDate = input_format_date($bookDate, $date_format_policy);
        $invoiceReceivedDate = $this->input->post('invoiceReceivedDate');
        $formatted_invoiceReceivedDate = input_format_date($invoiceReceivedDate, $date_format_policy);
        $supplirInvDuDate = $this->input->post('supplierInvoiceDueDate');
        $supplierInvoiceDueDate = input_format_date($supplirInvDuDate, $date_format_policy);
        $supplierid = $this->input->post('supplierID');
        $supplierinvoice = $this->input->post('supplier_invoice_no');
        $po_id = trim($this->input->post('priamryPo') ?? '');
        $retensionPercentage = trim($this->input->post('retensionPercentage') ?? '');
        $retensionGL = trim($this->input->post('retensionGL') ?? '');

        $rcmYN = trim($this->input->post('rcmYN') ?? '');

        $supplirinvoiceDate = $this->input->post('invoiceDate');
        $supplierinvoiceDate_new = input_format_date($supplirinvoiceDate, $date_format_policy);
        $invoiceautoid = $this->input->post('InvoiceAutoID');
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        if ($financeyearperiodYN == 1) {
            $year = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($year[0], $date_format_policy);
            $FYEnd = input_format_date($year[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($bookingDate);
            if (empty($financeYearDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
            } else {
                $FYBegin = $financeYearDetails['beginingDate'];
                $FYEnd = $financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails = get_financial_period_date_wise($bookingDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
            } else {
                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }

        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplierID') ?? ''));
        $data['invoiceType'] = trim($this->input->post('invoiceType') ?? '');
        $data['bookingDate'] = trim($bookingDate);
        $data['InvoiceReceivedDate'] = trim($formatted_invoiceReceivedDate);
        $data['invoiceDueDate'] = trim($supplierInvoiceDueDate);
        $data['invoiceDate'] = trim($supplierinvoiceDate_new);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');

        if($retensionEnabled == 1){
            $data['retensionPercentage'] = trim($retensionPercentage);
            $data['retensionGL'] = trim($retensionGL);
            $data['isRetensionYN'] = trim($this->input->post('isRetensionYN') ?? '');
            $data['retensionInvoiceID'] = trim($this->input->post('retensionInvoiceID') ?? '');
        }

        $data['documentID'] = 'BSI';
        $data['supplierID'] = trim($supplierid);
        if ($supplier_arr) {
            $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
            $data['supplierName'] = $supplier_arr['supplierName'];
            $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
            $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
            $data['supplierFax'] = $supplier_arr['supplierFax'];
            $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
            $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
            $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
            $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
            $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        }

        $data['supplierInvoiceNo'] = trim($supplierinvoice);
        $data['supplierInvoiceDate'] = trim($this->input->post('supplierInvoiceDueDate') ?? '');
        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');

        if($po_id){
            $data['purchaseOrderIDMaster'] = $po_id;

            //fetch po detail
            if($po_id){
                $po_details = $this->fetch_po_details($po_id);
                if($po_details){
                    $data['purchaseOrderDetails'] = $po_details['purchaseOrderCode'].'|'.$po_details['supplierCode'].'|'.$po_details['referenceNumber'];
                }
            }
           
        }
        
        
        $data['segmentID'] = trim($this->input->post('segment') ?? '');
        $data['RefNo'] = trim($this->input->post('referenceno') ?? '');
        $comments = ($this->input->post('comments'));
        $data['comments'] = str_replace('<br />', PHP_EOL, $comments);
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

        $data['rcmApplicableYN'] =$rcmYN;

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($this->input->post('InvoiceAutoID') ?? '')) {
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT
                      supplierInvoiceNo,InvoiceAutoID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
               InvoiceAutoID!='" . $invoiceautoid . "' AND supplierID = '" . $supplierid . "' AND  supplierInvoiceNo = '" . $supplierinvoice . "' ";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Number already exist for the selected supplier');
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true, 'last_id' => $this->input->post('InvoiceAutoID'));
                    }
                }
            } else {
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice : ' . $data['documentID'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Supplier Invoice : ' . $data['documentID'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('InvoiceAutoID'));
                }
            }
        } else {
            if (!empty($supplierinvoice) || $supplierinvoice != '') {
                $q = "SELECT
                    supplierInvoiceNo,supplierID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
                 supplierID = '" . $supplierid . "'  AND supplierInvoiceNo = '" . $supplierinvoice . "'";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    $this->session->set_flashdata('e', ' Supplier Invoice Number already exist for the selected supplier');
                    $this->db->trans_rollback();
                    return array('status' => false);
                }
            }

            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['bookingInvCode'] = 0;
            if ((trim($this->input->post('invoiceType') ?? '') == 'StandardItem') || (trim($this->input->post('invoiceType') ?? '') == 'Standard')) {
                $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All') == 1) ? 1 : 0);
            }

            $this->db->insert('srp_erp_paysupplierinvoicemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice   Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }


        }
    }

    function laad_supplier_invoice_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,if(invoiceType=\'Standard\',\'StandardItem\',invoiceType) as typedrop,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate, DATE_FORMAT(InvoiceReceivedDate,\'' . $convertFormat . '\') AS InvoiceReceivedDate');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        return $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
    }

    function fetch_supplier_invoice_template_data($InvoiceAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        $companyID = current_companyID();
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        if ($secondaryCode == 1) {
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }


        $this->db->select('*,srp_erp_segment.description as segDescription,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN
            CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paysupplierinvoicemaster.segmentID', 'Left');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail,vatNumber');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        if($group_based_tax == 1){
            $this->db->select('InvoiceAutoID,InvoiceDetailAutoID,retensionPercentage,retensionValue,GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,srp_erp_paysupplierinvoicedetail.description,discountAmount,discountPercentage,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID, srp_erp_activity_code_main.activity_code as activityCodeName');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'GL');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paysupplierinvoicedetail.activityCodeID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $data['detail'] = $this->db->get()->result_array();

            $this->db->select('
            	IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,
	IFNULL(taxAmount,0) as taxAmountLedger,
	taxledger.taxDetailAutoID,
            
            srp_erp_paysupplierinvoicedetail.*,`srp_erp_itemmaster`.`partNo`,CONCAT_WS(
	\' - Part No : \',

IF
	( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,`srp_erp_itemmaster`.`partNo`,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['podetail'] = $this->db->get()->result_array();



            $this->db->select('
            	IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,
	            IFNULL(taxAmount,0) as taxAmountLedger,
	            taxledger.taxDetailAutoID,
                srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`description` ), `srp_erp_paysupplierinvoicedetail`.`description`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_unit_of_measure.UnitShortCode as secuom,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['Itemdetail'] = $this->db->get()->result_array();

        }else{
            $this->db->select('InvoiceAutoID,InvoiceDetailAutoID,GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,srp_erp_paysupplierinvoicedetail.description,discountAmount,discountPercentage, srp_erp_activity_code_main.activity_code as activityCodeName');
            // $this->db->group_by("GLCode");
            // $this->db->group_by("segmentCode");
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'GL');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paysupplierinvoicedetail.activityCodeID', 'left');
            $data['detail'] = $this->db->get()->result_array();



            $this->db->select('srp_erp_paysupplierinvoicedetail.*,`srp_erp_itemmaster`.`partNo`,CONCAT_WS(
	\' - Part No : \',
IF
	( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,`srp_erp_itemmaster`.`partNo`,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $data['podetail'] = $this->db->get()->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`description` ), `srp_erp_paysupplierinvoicedetail`.`description`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_unit_of_measure.UnitShortCode as secuom,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');

            $data['Itemdetail'] = $this->db->get()->result_array();
        }







        $this->db->select('grvAutoID,srp_erp_paysupplierinvoicedetail.segmentCode,srp_erp_paysupplierinvoicedetail.transactionAmount,srp_erp_paysupplierinvoicedetail.companyLocalAmount,srp_erp_paysupplierinvoicedetail.supplierAmount,grvPrimaryCode,grvDocRefNo,grvDate,description,master.retensionPercentage,
        srp_erp_paysupplierinvoicedetail.retensionPercentage,srp_erp_paysupplierinvoicedetail.retensionValue');
        $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->join('srp_erp_paysupplierinvoicemaster master','srp_erp_paysupplierinvoicedetail.InvoiceAutoID = master.InvoiceAutoID','left');
        $data['grv_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        return $data;
    }

    function fetch_selected_supplier_invoice_template_data($InvoiceAutoID)
    {
        //$InvoiceAutoID = 2835;
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        $companyID = current_companyID();
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        if ($secondaryCode == 1) {
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }


        $this->db->select('*,srp_erp_segment.description as segDescription,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,DATE_FORMAT(bookingDate,\'' . $convertFormat . '\') AS bookingDate,DATE_FORMAT(invoiceDueDate,\'' . $convertFormat . '\') AS invoiceDueDate,DATE_FORMAT(invoiceDate,\'' . $convertFormat . '\') AS invoiceDate,(DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN
            CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_paysupplierinvoicemaster.segmentID', 'Left');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail,vatNumber');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();

        if($group_based_tax == 1){
            $this->db->select('InvoiceAutoID,InvoiceDetailAutoID,GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,srp_erp_paysupplierinvoicedetail.description,discountAmount,discountPercentage,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'GL');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $data['detail'] = $this->db->get()->result_array();

            $this->db->select('
            	IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,
	IFNULL(taxAmount,0) as taxAmountLedger,
	taxledger.taxDetailAutoID,
            
            srp_erp_paysupplierinvoicedetail.*,`srp_erp_itemmaster`.`partNo`,CONCAT_WS(
	\' - Part No : \',

IF
	( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,`srp_erp_itemmaster`.`partNo`,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['podetail'] = $this->db->get()->result_array();



            $this->db->select('
            	IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,
	            IFNULL(taxAmount,0) as taxAmountLedger,
	            taxledger.taxDetailAutoID,
                srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`description` ), `srp_erp_paysupplierinvoicedetail`.`description`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_unit_of_measure.UnitShortCode as secuom,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['Itemdetail'] = $this->db->get()->result_array();

        }else{
            $this->db->select('InvoiceAutoID,InvoiceDetailAutoID,GLCode,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,srp_erp_paysupplierinvoicedetail.description,discountAmount,discountPercentage');
            // $this->db->group_by("GLCode");
            // $this->db->group_by("segmentCode");
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'GL');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $data['detail'] = $this->db->get()->result_array();



            $this->db->select('srp_erp_paysupplierinvoicedetail.*,`srp_erp_itemmaster`.`partNo`,CONCAT_WS(
	\' - Part No : \',
IF
	( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),
IF
	( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )
	) AS Itemdescriptionpartno,`srp_erp_itemmaster`.`partNo`,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'PO');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $data['podetail'] = $this->db->get()->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`description` ), `srp_erp_paysupplierinvoicedetail`.`description`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_itemmaster.partNo,srp_erp_unit_of_measure.UnitShortCode as secuom,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $InvoiceAutoID);
            $this->db->where('type', 'Item');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');

            $data['Itemdetail'] = $this->db->get()->result_array();
        }







        $this->db->select('grvAutoID,segmentCode,transactionAmount,companyLocalAmount,supplierAmount,grvPrimaryCode ,grvDocRefNo,grvDate,description');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $data['grv_detail'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $InvoiceAutoID);
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
        return $data;
    }

    function fetch_debit_note_template_data($debitNoteMasterAutoID)
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate,
                                srp_erp_debitnotemaster.createdUserName as createdUserName,
                                DATE_FORMAT(srp_erp_debitnotemaster.createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,
                                (DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\')) AS approvedDate,
                                CASE 
                                    WHEN srp_erp_debitnotemaster.confirmedYN = 2 || srp_erp_debitnotemaster.confirmedYN = 3 THEN " - " 
                                    WHEN srp_erp_debitnotemaster.confirmedYN = 1 
                                        THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'), 
                                            IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )), 
                                            DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) 
                                    ELSE "-" 
                                END confirmedYNn,
                                srp_erp_company.companyVatNumber');
        $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_debitnotemaster.companyID', 'Left');
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->from('srp_erp_debitnotemaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);

        $this->db->select('supplierSystemCode,supplierName,supplierAddress1,supplierTelephone,supplierFax,supplierEmail');
        $this->db->where('supplierAutoID', $data['master']['supplierID']);
        $this->db->from('srp_erp_suppliermaster');
        $data['supplier'] = $this->db->get()->row_array();


        $this->db->select('IFNULL(taxLedgerDetails.amount,0) as amount, IFNULL(taxLedgerDetails.taxPercentage,0) as taxpercentageLedger, GLCode,InvoiceAutoID,GLDescription,segmentCode,transactionAmount,companyLocalAmount,supplierAmount ,bookingInvCode,srp_erp_debitnotedetail.description, isFromInvoice, IFNULL(taxAmount, 0) AS taxAmount');
        $this->db->join('(SELECT
                              SUM(amount) as amount,
                              srp_erp_taxledger.taxPercentage,
                              documentDetailAutoID
	                          FROM
                              srp_erp_taxledger 
	                          LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                          WHERE documentID = "DN" 
	                          AND taxCategory = 2 
	                          GROUP BY documentID,documentDetailAutoID)taxLedgerDetails','taxLedgerDetails.documentDetailAutoID = srp_erp_debitnotedetail.debitNoteDetailsID','left');
        // $this->db->group_by("GLCode");
        // $this->db->group_by("segmentCode");
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->where('isFromInvoice', 1);
        $this->db->from('srp_erp_debitnotedetail');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('IFNULL(taxLedgerDetails.amount,0) as amount, IFNULL(taxLedgerDetails.taxPercentage,0) as taxpercentageLedger,srp_erp_debitnotedetail.*,IFNULL(taxAmount,0 ) as taxAmount,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS TaxDescription, srp_erp_activity_code_main.activity_code AS activityCodeName');
        $this->db->join('(SELECT
                              SUM(amount) as amount,
                              srp_erp_taxledger.taxPercentage,
                              documentDetailAutoID
	                          FROM
                              srp_erp_taxledger 
	                          LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                          WHERE documentID = "DN" 
	                          AND taxCategory = 2 
	                          GROUP BY documentID,documentDetailAutoID)taxLedgerDetails','taxLedgerDetails.documentDetailAutoID = srp_erp_debitnotedetail.debitNoteDetailsID','left');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_debitnotedetail.activityCodeID', 'left');
        $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        $this->db->where('isFromInvoice', 0);
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_debitnotedetail.taxCalculationformulaID', 'left');
        $data['detail_glCode'] = $this->db->get()->result_array();
        return $data;
    }


    function fetch_supplier_invoice_detail()
    {
        $data = array();
        $companyID = current_companyID();
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $secondaryCode = getPolicyValues('SSC', 'All');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($this->input->post('InvoiceAutoID') ?? ''), 'BSI', 'InvoiceAutoID');
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if ($secondaryCode == 1) {
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }


        if ($group_based_tax == 1) {
            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secunitcode,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->where('type', 'Item');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['ItemDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();


            $this->db->select('srp_erp_paysupplierinvoicedetail.*, srp_erp_activity_code_main.activity_code as activityCodeName, IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS DescriptionTax,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_paysupplierinvoicedetail.activityCodeID', 'left');
            $this->db->where('type', 'GL');
            $data['detail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS DescriptionTax,IFNULL(taxAmount,0) as taxAmountLedger,taxledger.taxDetailAutoID,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->where('type', 'PO');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('(SELECT
            documentDetailAutoID,
            taxDetailAutoID
            FROM
            `srp_erp_taxledger`
            where
            companyID = ' . $companyID . '
            AND documentID = \'BSI\'
            AND documentMasterAutoID  = ' . $InvoiceAutoID . '
            GROUP BY
            documentMasterAutoID,documentDetailAutoID)taxledger', ' taxledger.documentDetailAutoID = srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', 'left');

            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['poDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();




        } else {


            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,srp_erp_unit_of_measure.UnitShortCode as secunitcode,IFNULL( srp_erp_taxcalculationformulamaster.Description,\' - \') AS Description,IFNULL(taxAmount,0) as taxAmountLedger,' . $item_code_alias . ' ');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->where('type', 'Item');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
            $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');
            $data['ItemDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->where('type', 'GL');
            $data['detail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();

            $this->db->select('srp_erp_paysupplierinvoicedetail.*,CONCAT_WS(\' - Part No : \',IF ( LENGTH( srp_erp_paysupplierinvoicedetail.`itemDescription` ), `srp_erp_paysupplierinvoicedetail`.`itemDescription`, NULL ),IF( LENGTH( srp_erp_itemmaster.partNo ), `srp_erp_itemmaster`.`partNo`, NULL )) AS Itemdescriptionpartno,' . $item_code_alias . '');
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->where('type', 'PO');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
            $data['poDetail'] = $this->db->get('srp_erp_paysupplierinvoicedetail')->result_array();


        }




        $this->db->select('*');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $data['tax'] = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();

        return $data;
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function fetch_supplier_invoice_grv($segmentID, $bookingDate)
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $bookingDate = input_format_date($bookingDate, $date_format_policy);
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $currencyID = trim($this->input->post('currencyID') ?? '');
        return $this->db->query("SELECT DATE_FORMAT(srp_erp_grvmaster.grvDate,'$convertFormat') AS grvDate, srp_erp_match_supplierinvoice.isAddon,srp_erp_match_supplierinvoice.match_supplierinvoiceAutoID,srp_erp_grvmaster.grvAutoID,srp_erp_grvmaster.grvPrimaryCode,srp_erp_grvmaster.grvDocRefNo,srp_erp_match_supplierinvoice.bookingAmount,srp_erp_match_supplierinvoice.invoicedTotalAmount,srp_erp_match_supplierinvoice.isAddon,srp_erp_stockreturndetails.totalValue FROM srp_erp_grvmaster INNER JOIN srp_erp_match_supplierinvoice ON srp_erp_grvmaster.grvAutoID = srp_erp_match_supplierinvoice.grvAutoID LEFT JOIN srp_erp_stockreturndetails ON srp_erp_grvmaster.grvAutoID = srp_erp_stockreturndetails.grvAutoID WHERE srp_erp_grvmaster.confirmedYN = 1 AND srp_erp_grvmaster.approvedYN = 1 AND srp_erp_match_supplierinvoice.supplierInvoiceYN = 0 AND srp_erp_match_supplierinvoice.supplierID = '{$supplierID}' AND `bookingCurrencyID` = '{$currencyID}' AND `grvDate` <= '{$bookingDate}'")->result_array();
    }

    function save_bsi_detail()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $tax_type = $this->input->post('tax_type');
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($this->input->post('InvoiceAutoID') ?? ''), 'BSI', 'InvoiceAutoID');

        $advanceCostCapturing = getPolicyValues('ACC', 'All');

        $segment = explode('|', trim($this->input->post('segment_gl') ?? ''));
        $gl_code = explode(' | ', trim($this->input->post('gl_code_des') ?? ''));
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['GLAutoID'] = trim($this->input->post('gl_code') ?? '');
        $discountPercentage = $this->input->post('discountPercentage');
        if ($projectExist == 1) {
            $projectID = trim($this->input->post('projectID') ?? '');
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
        if($advanceCostCapturing == 1){
            $data['activityCodeID'] = trim($activityCodeID);
        }
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['discountPercentage'] = trim($discountPercentage);
        $data['discountAmount'] = trim(($this->input->post('amount') * $discountPercentage) / 100);
        $data['transactionAmount'] = round($this->input->post('amount') - $data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);
        $data['transactionExchangeRate'] = $master['transactionExchangeRate'];

        $companyLocalAmount = 0;
        if ($master['companyLocalExchangeRate']) {
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
        }

        $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
        $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $companyReportingAmount = 0;
        if ($master['companyReportingExchangeRate']) {
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
        }
        $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
        $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];

        $supplierAmount = 0;
        if ($master['supplierCurrencyExchangeRate']) {
            $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
        }

        $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
        $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('InvoiceDetailAutoID') ?? '')) {
            $this->db->where('InvoiceDetailAutoID', trim($this->input->post('InvoiceDetailAutoID') ?? ''));
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();

            if ($group_based_tax == 1) {
                if (!empty($tax_type)) {

                    tax_calculation_vat(null, null, $tax_type, 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), $this->input->post('amount'), 'BSI', trim($this->input->post('InvoiceDetailAutoID') ?? ''), $data['discountAmount'], 1, $isRcmDocument);
                } else {
                    fetchExistsDetailTBL('BSI', trim($this->input->post('InvoiceAutoID') ?? ''), trim($this->input->post('InvoiceDetailAutoID') ?? ''), NULL, 1, $data['transactionAmount']);
                }
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Detail : ' . $data['GLDescription'] . ' Saved Successfully.');

            }
        }
    }

    function save_bsi_detail_multiple()
    {
        $this->db->trans_start();
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $this->db->select('companyLocalExchangeRate,retensionPercentage,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionExchangeRate,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $gl_codes = $this->input->post('gl_code');
        $gl_code_des = $this->input->post('gl_code_des');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $projectExist = project_is_exist();
        $projectID = $this->input->post('projectID');
        $discountPercentage = $this->input->post('discountPercentage');
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $tax_type = $this->input->post('tax_type');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $this->input->post('InvoiceAutoID'), 'BSI', 'InvoiceAutoID');
        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
            $data['GLAutoID'] = $gl_codes[$key] ?? '';
            $data['projectID'] = $projectID[$key] ?? '';

            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];
            }

            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = trim($activityCodeID[$key]);
            }
            $data['description'] = $descriptions[$key];
            $data['discountPercentage'] = trim($discountPercentage[$key]);
            $data['discountAmount'] = trim(($amount[$key] * $discountPercentage[$key]) / 100);
            $data['transactionAmount'] = round($amount[$key] - $data['discountAmount'], $master['transactionCurrencyDecimalPlaces']);
            $data['transactionExchangeRate'] = $master['transactionExchangeRate'];

            $companyLocalAmount = 0;
            if ($master['companyLocalExchangeRate']) {
                $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            }

            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = 0;

            if ($master['companyReportingExchangeRate']) {
                $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            }

            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = 0;
            if ($master['supplierCurrencyExchangeRate']) {
                $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            }

            if($retensionEnabled == 1){

                $data['retensionPercentage'] = $master['retensionPercentage'];

                if( $master['retensionPercentage'] > 0){
                    $data['retensionValue'] = round(($data['transactionAmount'] * $master['retensionPercentage']) / 100,2);
                }

            }

            $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);

            $last_id = $this->db->insert_id();
            if (!empty($tax_type[$key])) {
                if ($group_based_tax == 1) {
                    tax_calculation_vat(null, null, $tax_type[$key], 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), $amount[$key], 'BSI', $last_id, $data['discountAmount'], 1,$isRcmDocument);
                }
            }

        }

        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Supplier Invoice Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();


            return array('s', 'Supplier Invoice Detail : Saved Successfully.');
        }

    }

    function supplier_invoice_confirmation()
    {
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $this->db->select('InvoiceAutoID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {
            $this->db->select('InvoiceAutoID');
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return array('status' => true);
            } else {
                $this->db->trans_start();
                $system_id = trim($this->input->post('InvoiceAutoID') ?? '');

                // start approval type check
                $approvalType = getApprovalTypesONDocumentCode('BSI', $companyID);
                $documentTotal = $this->fetch_total_value_on_bsi($system_id, $companyID);

                $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];
                

                $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_paysupplierinvoicemaster where InvoiceAutoID = $system_id AND companyID = {$companyID}")->row_array();

                if($approvalType['approvalType'] == 2) {
                    $amountApprovable = amount_based_approval_setup('BSI', $poLocalAmount);
                    
                    if($amountApprovable['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this BSI Value');
                        return array('status' => false);
                        exit;
                    }
                }
                if($approvalType['approvalType'] == 3) {
                    $segment_based_approval = segment_based_approval('BSI', $segmentID['segmentID']);

                    if($segment_based_approval['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this BSI Value');
                        return array('status' => false);
                        exit;
                    }
                }
                if($approvalType['approvalType'] == 4) {
                    $amount_base_segment_based_approval = amount_base_segment_based_approval('BSI', $poLocalAmount, $segmentID['segmentID']);

                    if($amount_base_segment_based_approval['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this BSI Value');
                        return array('status' => false);
                        exit;
                    }
                }
                
                //end start approval typs

                /*      $supplierInvoiceDetail = $this->db->query("select
                                             GROUP_CONCAT(itemAutoID) as itemAutoID
                                             from
                                             srp_erp_paysupplierinvoicedetail
                                             where
                                             companyID = $companyID
                                             AND InvoiceAutoID = $system_id")->row("itemAutoID");

                     if(!empty($supplierInvoiceDetail)){
                         $wacTransactionAmountValidation  = fetch_itemledger_transactionAmount_validation("$supplierInvoiceDetail");
                         if(!empty($wacTransactionAmountValidation)){
                             return array('error' => 4, 'message' => $wacTransactionAmountValidation);
                             exit();
                         }

                     } */
                $this->db->select('bookingInvCode,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_dt = $this->db->get()->row_array();

                $this->load->library('sequence');
                $lenth = strlen($master_dt['bookingInvCode']);
                if ($lenth == 1) {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                            return false;
                        } else {
                            if ($emplocationid != '') {
                                $location = $this->sequence->sequence_generator_location('BSI', $master_dt['companyFinanceYearID'], $emplocationid, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            }
                        }
                    } else {
                        $location = $this->sequence->sequence_generator_fin('BSI', $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }

                    $validate_code = validate_code_duplication($location, 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }

                    $invcod = array(
                        /* 'bookingInvCode' => $this->sequence->sequence_generator_fin('BSI',$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']),*/
                        'bookingInvCode' => $location,
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $invcod);
                } else {
                    $validate_code = validate_code_duplication($master_dt['bookingInvCode'], 'bookingInvCode', $system_id, 'InvoiceAutoID', 'srp_erp_paysupplierinvoicemaster');
                    if (!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('InvoiceAutoID, bookingInvCode,transactionCurrency,transactionExchangeRate,companyFinanceYearID,DATE_FORMAT(bookingDate, "%Y") as invYear,DATE_FORMAT(bookingDate, "%m") as invMonth,bookingDate,isRetensionYN');
                $this->db->where('InvoiceAutoID', $system_id);
                $this->db->from('srp_erp_paysupplierinvoicemaster');
                $master_data = $this->db->get()->row_array();

                if($master_data['isRetensionYN'] != 1){
                    $autoApproval = get_document_auto_approval('BSI');
                    if ($autoApproval == 0) {
                        $approvals_status = $this->approvals->auto_approve($master_data['InvoiceAutoID'], 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 'BSI', $master_data['bookingInvCode'], $master_data['bookingDate']);
                    } elseif ($autoApproval == 1) {
                        $approvals_status = $this->approvals->CreateApproval('BSI', $master_data['InvoiceAutoID'], $master_data['bookingInvCode'], 'Supplier Invoice', 'srp_erp_paysupplierinvoicemaster', 'InvoiceAutoID', 0, $master_data['bookingDate'],$segmentID['segmentID'],$poLocalAmount);
                    } else {
                        $this->session->set_flashdata('e', 'Approval levels are not set for this document');
                        return array('status' => false);
                    }
                } else {
                    $approvals_status = 0;
                }
               

                if ($approvals_status == 1) {
                    $transa_total_amount = 0;
                    $loca_total_amount = 0;
                    $rpt_total_amount = 0;
                    $supplier_total_amount = 0;
                    $retension_total_amount = 0;
                    $t_arr = array();
                    $tra_tax_total = 0;
                    $loca_tax_total = 0;
                    $rpt_tax_total = 0;
                    $sup_tax_total = 0;
                    $this->db->select('SUM(retensionValue) as retensionValue,sum(transactionAmount) as transactionAmount,sum(companyLocalAmount) as companyLocalAmount,sum(companyReportingAmount) as companyReportingAmount,sum(supplierAmount) as supplierAmount');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $data_arr = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

                    $transa_total_amount += $data_arr['transactionAmount'];
                    $loca_total_amount += $data_arr['companyLocalAmount'];
                    $rpt_total_amount += $data_arr['companyReportingAmount'];
                    $supplier_total_amount += $data_arr['supplierAmount'];
                    $retension_total_amount += $data_arr['retensionValue'];

                    $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,taxPercentage');
                    $this->db->where('InvoiceAutoID', $system_id);
                    $tax_arr = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->result_array();
                    for ($x = 0; $x < count($tax_arr); $x++) {
                        $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $transa_total_amount);
                        $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                        $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                        $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                        $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                        $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                        $tra_tax_total = $t_arr[$x]['transactionAmount'];
                        $sup_tax_total = $t_arr[$x]['supplierCurrencyAmount'];
                        $loca_tax_total = $t_arr[$x]['companyLocalAmount'];
                        $rpt_tax_total = $t_arr[$x]['companyReportingAmount'];
                    }

                    //update total retension values

                    /*updating transaction amount using the query used in the master data table*/
                    $companyID = current_companyID();
                    $r1 = "SELECT
                            srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
                                `srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
                                `srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
                                `srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
                                `srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
                                `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
                                `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
                                `srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
                                (srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0) as discountAmnt,
                                (
                                    (
                                        (
                                            IFNULL(addondet.taxPercentage, 0) / 100
                                        ) * (IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0)))
                                    ) + IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0))
                                ) AS total_value

                            FROM
                                `srp_erp_paysupplierinvoicemaster`
                            LEFT JOIN (
                                SELECT
                                    SUM(transactionAmount) AS transactionAmount,
                                    InvoiceAutoID
                                FROM
                                    srp_erp_paysupplierinvoicedetail
                                GROUP BY
                                    InvoiceAutoID
                            ) det ON (
                                `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                            )
                            LEFT JOIN (
                                SELECT
                                    SUM(taxPercentage) AS taxPercentage,
                                    InvoiceAutoID
                                FROM
                                    srp_erp_paysupplierinvoicetaxdetails
                                GROUP BY
                                    InvoiceAutoID
                            ) addondet ON (
                                `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                            )
                            WHERE
                                `companyID` = $companyID
                                AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $system_id ";
                    $totalValue = $this->db->query($r1)->row_array();
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                        'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'], $totalValue['companyLocalCurrencyDecimalPlaces'])),
                        'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'], $totalValue['companyReportingCurrencyDecimalPlaces'])),
                        'supplierCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['supplierCurrencyExchangeRate'], $totalValue['supplierCurrencyDecimalPlaces'])),
                        'transactionAmount' => (round($totalValue['total_value'], $totalValue['transactionCurrencyDecimalPlaces'])),
                        'generalDiscountAmount' => ($totalValue['discountAmnt']),
                        'totalRetension' => $retension_total_amount,
                    );
                    $this->db->where('InvoiceAutoID', $system_id);
                    $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                    if (!empty($t_arr)) {
                        $this->db->update_batch('srp_erp_paysupplierinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                    }
                } else if ($approvals_status == 3) {
                    $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
                    return array('status' => true);
                } else {
                    $this->session->set_flashdata('e', 'Confirmation failed.');
                    return array('status' => false);
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Supplier Invoice Confirmed failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $autoApproval = get_document_auto_approval('BSI');

                    

                    $updatedBatchNumberArray=[];

                    if($itemBatchPolicy==1){

                        $this->db->select('*');
                        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
                        $this->db->from('srp_erp_paysupplierinvoicedetail');
                        $invoice_results = $this->db->get()->result_array();

                        $updatedBatchNumberArray=update_item_batch_number_details($invoice_results);

                    }

                     if ($autoApproval == 0) {
                        $result = $this->save_supplier_invoice_approval(0, $master_data['InvoiceAutoID'], 1, 'Auto Approved',$updatedBatchNumberArray);
                        if ($result) {

                            $this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');
                            $this->db->trans_commit();
                            return array('status' => true);
                        }
                    } else {
                        $this->session->set_flashdata('s', 'Supplier Invoice Confirmed Successfully.');
                        $this->db->trans_commit();
                        return array('status' => true);
                    }
                }
            }
        }
    }

    function fetch_total_value_on_bsi($InvoiceAutoID, $companyID){
        $query_dt = "SELECT
        srp_erp_paysupplierinvoicemaster.InvoiceAutoID,
            `srp_erp_paysupplierinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
            `srp_erp_paysupplierinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
            `srp_erp_paysupplierinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
            `srp_erp_paysupplierinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
            `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyExchangeRate` AS `supplierCurrencyExchangeRate`,
            `srp_erp_paysupplierinvoicemaster`.`supplierCurrencyDecimalPlaces` AS `supplierCurrencyDecimalPlaces`,
            `srp_erp_paysupplierinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,
            (srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0) as discountAmnt,
            (
                (
                    (
                        IFNULL(addondet.taxPercentage, 0) / 100
                    ) * (IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0)))
                ) + IFNULL(det.transactionAmount, 0)-((srp_erp_paysupplierinvoicemaster.generalDiscountPercentage/100)*IFNULL(det.transactionAmount, 0))
            ) AS total_value
        
        FROM
            `srp_erp_paysupplierinvoicemaster`
        LEFT JOIN (
            SELECT
                SUM(transactionAmount) AS transactionAmount,
                InvoiceAutoID
            FROM
                srp_erp_paysupplierinvoicedetail
            GROUP BY
                InvoiceAutoID
        ) det ON (
            `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
        )
        LEFT JOIN (
            SELECT
                SUM(taxPercentage) AS taxPercentage,
                InvoiceAutoID
            FROM
                srp_erp_paysupplierinvoicetaxdetails
            GROUP BY
                InvoiceAutoID
        ) addondet ON (
            `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
        )
        WHERE
            `companyID` = $companyID
            AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $InvoiceAutoID ";
        
        $totalValue = $this->db->query($query_dt)->row_array();

        return  $totalValue;
    }

    function delete_bsi_detail()
    {
        $InvoiceAutoID = trim($this->input->post('InvoiceAutoID') ?? '');
        $this->db->select('match_supplierinvoiceAutoID,transactionAmount,purchaseOrderDetailsID');
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->where('InvoiceDetailAutoID', trim($this->input->post('InvoiceDetailAutoID') ?? ''));
        $detail_arr = $this->db->get()->row_array();
        $company_id = $this->common_data['company_data']['company_id'];
        $match_id = $detail_arr['match_supplierinvoiceAutoID'];
        $number = $detail_arr['transactionAmount'];
        $status = 0;
        $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount -{$number})  , supplierInvoiceYN = {$status}  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        $this->db->delete('srp_erp_paysupplierinvoicedetail', array('InvoiceDetailAutoID' => trim($this->input->post('InvoiceDetailAutoID') ?? '')));

        /* $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS receivedQty,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS bsireceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $detail_arr['purchaseOrderDetailsID']);
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
        $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->group_by("purchaseOrderDetailsID"); */
        $query = fetch_po_detail_status($detail_arr['purchaseOrderDetailsID']);


        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        if ($group_based_tax == 1) {
            fetchExistsDetailTBL('BSI', $InvoiceAutoID, $this->input->post('InvoiceDetailAutoID'));
        }


        if ($query['receivedQty'] > 0 || $query['bsireceivedQty'] > 0) {
            $po_data['GRVSelectedYN'] = 1;
        } else {
            $po_data['GRVSelectedYN'] = 0;
        }

        if ($query['requestedQty'] <= (floatval($query['receivedQty']) + floatval($query['bsireceivedQty']))) {
            $po_data['goodsRecievedYN'] = 1;
        } else {
            $po_data['goodsRecievedYN'] = 0;
        }
        $this->db->where('purchaseOrderDetailsID', $detail_arr['purchaseOrderDetailsID']);
        $this->db->update('srp_erp_purchaseorderdetails', $po_data);

        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_paysupplierinvoicetaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function fetch_bsi_detail()
    {
        $this->db->select('*,srp_erp_unit_of_measure.UnitShortCode as secuom,srp_erp_unit_of_measure.UnitDes as secuomdec,srp_erp_itemmaster.seconeryItemCode,IFNULL(taxAmount,0) as taxAmountledger,IFNULL(srp_erp_taxcalculationformulamaster.Description,\'-\') as taxdescription');
        $this->db->where('InvoiceDetailAutoID', $this->input->post('InvoiceDetailAutoID'));
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_paysupplierinvoicedetail.SUOMID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_paysupplierinvoicedetail.taxCalculationformulaID', 'left');

        return $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();
    }

    /*function save_grv_base_items()
    {
        $this->db->trans_start();
        $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
        $this->db->from('srp_erp_grvmaster');
        $this->db->where_in('srp_erp_grvmaster.grvAutoID', $this->input->post('grvAutoID'));
        $master_recode = $this->db->get()->result_array();
        $amount = $this->input->post('amounts');
        $match = $this->input->post('match');
        for ($i = 0; $i < count($master_recode); $i++) {
            $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate');
            $this->db->where('match_supplierinvoiceAutoID', $match[$i]);
            $this->db->from('srp_erp_match_supplierinvoice');
            $match_data = $this->db->get()->row_array();
            $data[$i]['InvoiceAutoID'] = $this->input->post('InvoiceAutoID');
            $data[$i]['grvAutoID'] = $master_recode[$i]['grvAutoID'];
            $data[$i]['grvType'] = 'GRV Base';
            $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
            $data[$i]['grvPrimaryCode'] = $master_recode[$i]['grvPrimaryCode'];
            $data[$i]['grvDocRefNo'] = $master_recode[$i]['grvDocRefNo'];
            $data[$i]['grvDate'] = $master_recode[$i]['grvDate'];
            $data[$i]['segmentID'] = $master_recode[$i]['segmentID'];
            $data[$i]['segmentCode'] = $master_recode[$i]['segmentCode'];
            $data[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
            $data[$i]['description'] = $master_recode[$i]['grvNarration'];
            $transactionAmount = $amount[$i] / $match_data['bookingCurrencyExchangeRate'];
            $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode[$i]['transactionCurrencyDecimalPlaces']);
            $data[$i]['transactionExchangeRate'] = $match_data['bookingCurrencyExchangeRate'];
            $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode[$i]['companyLocalCurrencyDecimalPlaces']);
            $data[$i]['companyLocalExchangeRate'] = $match_data['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode[$i]['companyReportingCurrencyDecimalPlaces']);
            $data[$i]['companyReportingExchangeRate'] = $match_data['companyReportingExchangeRate'];
            $supplierAmount = $data[$i]['transactionAmount'] / $match_data['supplierCurrencyExchangeRate'];
            $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode[$i]['supplierCurrencyDecimalPlaces']);
            $data[$i]['supplierCurrencyExchangeRate'] = $match_data['supplierCurrencyExchangeRate'];
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
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $data[$i]['match_supplierinvoiceAutoID'];
            $number = $transactionAmount;
            $status = 0;

            $this->db->select('invoicedTotalAmount, bookingAmount');
            $this->db->from('srp_erp_match_supplierinvoice');
            $this->db->where('match_supplierinvoiceAutoID', $match_id);
            $inv_data = $this->db->get()->row_array();
            if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                $status = 1;
            }

            $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Good Received note : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }*/


    function save_grv_base_items()
    {
        $this->db->trans_start();

        $amount = $this->input->post('amounts');
        $match = $this->input->post('match');
        $grvAutoID = $this->input->post('grvAutoID');
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $companyID = current_companyID();
        $retensionEnabled = getPolicyValues('RETO', 'All');

        $this->db->select('*');
        $this->db->where_in('InvoiceAutoID', $InvoiceAutoID);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $master = $this->db->get()->row_array();


        $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate');
        $this->db->where_in('match_supplierinvoiceAutoID', $match);
        $this->db->from('srp_erp_match_supplierinvoice');
        $match_data = $this->db->get()->result_array();

        for ($i = 0; $i < count($match_data); $i++) {
            $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
            $this->db->from('srp_erp_grvmaster');
            $this->db->where('srp_erp_grvmaster.grvAutoID', $grvAutoID[$i]);
            $master_recode = $this->db->get()->row_array();

            $data[$i]['InvoiceAutoID'] = $this->input->post('InvoiceAutoID');
            $data[$i]['grvAutoID'] = $master_recode['grvAutoID'];
            $data[$i]['grvType'] = 'GRV Base';
            $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
            $data[$i]['grvPrimaryCode'] = $master_recode['grvPrimaryCode'];
            $data[$i]['grvDocRefNo'] = $master_recode['grvDocRefNo'];
            $data[$i]['grvDate'] = $master_recode['grvDate'];
            $data[$i]['segmentID'] = $master_recode['segmentID'];
            $data[$i]['segmentCode'] = $master_recode['segmentCode'];
            $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
            $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
            $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
            $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
            $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
            $data[$i]['description'] = $master_recode['grvNarration'];
            $transactionAmount = $amount[$i] / $match_data[$i]['bookingCurrencyExchangeRate'];
            $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode['transactionCurrencyDecimalPlaces']);

            if($retensionEnabled == 1){
                $data[$i]['retensionPercentage'] = $master['retensionPercentage'];
                $data[$i]['retensionValue'] = round((($data[$i]['transactionAmount'] * $master['retensionPercentage']) / 100),$master_recode['transactionCurrencyDecimalPlaces']);
            }

            $data[$i]['transactionExchangeRate'] = $match_data[$i]['bookingCurrencyExchangeRate'];
            $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyLocalExchangeRate'];
            $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode['companyLocalCurrencyDecimalPlaces']);
            $data[$i]['companyLocalExchangeRate'] = $match_data[$i]['companyLocalExchangeRate'];
            $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyReportingExchangeRate'];
            $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode['companyReportingCurrencyDecimalPlaces']);
            $data[$i]['companyReportingExchangeRate'] = $match_data[$i]['companyReportingExchangeRate'];
            $supplierAmount = $data[$i]['transactionAmount'] / $match_data[$i]['supplierCurrencyExchangeRate'];
            $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode['supplierCurrencyDecimalPlaces']);
            $data[$i]['supplierCurrencyExchangeRate'] = $match_data[$i]['supplierCurrencyExchangeRate'];
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
            $company_id = $this->common_data['company_data']['company_id'];
            $match_id = $data[$i]['match_supplierinvoiceAutoID'];
            $number = $transactionAmount;
            $status = 0;

            $this->db->select('invoicedTotalAmount, bookingAmount');
            $this->db->from('srp_erp_match_supplierinvoice');
            $this->db->where('match_supplierinvoiceAutoID', $match_id);
            $inv_data = $this->db->get()->row_array();
            if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                $status = 1;
            }

            $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);

            $this->db->query("INSERT INTO srp_erp_taxledger (documentID, documentMasterAutoID, documentDetailAutoID, amount,taxGlAutoID,transferGLAutoID,taxMasterID,companyID,outputVatTransferGL,outputVatGL,isClaimable,rcmApplicableYN, taxFormulaMasterID, taxFormulaDetailID)
                                      SELECT 'BSI' AS documentID,
                                              InvoiceAutoID AS documentMasterAutoID, 
                                              InvoiceDetailAutoID AS documentDetailAutoID, 
                                              IFNULL(( TRIM( ROUND( (( taxledger.taxamount / matchsupplierinvoice.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0) ,0)  +
                                              IFNULL(( TRIM( ROUND( (( taxledgerAddon.taxamount / matchsupplierInvoiceAddon.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0 ) ,0) AS amount,
                                              taxledger.inputVatGLAccountAutoID AS taxGlAutoID,
                                              taxledger.taxGlAutoID AS transferGLAutoID,
                                              taxledger.taxMasterID AS taxMasterID,
                                              {$companyID} AS companyID,
                                              taxledger.outputVatTransferGL AS outputVatTransferGL,
                                              taxledger.outputVatGL AS outputVatGL,
                                              taxledger.isClaimable AS isClaimable,
                                              taxledger.rcmApplicableYN AS rcmApplicableYN,
                                              taxledger.taxFormulaMasterID,
                                              taxledger.taxFormulaDetailID
                                            FROM
	                                          `srp_erp_paysupplierinvoicedetail`
                                            LEFT JOIN ( SELECT match_supplierinvoiceAutoID,addonID,bookingAmount  FROM srp_erp_match_supplierinvoice WHERE isAddon = 0 ) matchsupplierinvoice ON srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID = matchsupplierinvoice.match_supplierinvoiceAutoID
                                            LEFT JOIN (SELECT match_supplierinvoiceAutoID,addonID,bookingAmount   FROM srp_erp_match_supplierinvoice WHERE isAddon = 1) matchsupplierInvoiceAddon ON matchsupplierInvoiceAddon.match_supplierinvoiceAutoID = srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID
                                            LEFT JOIN (
	                                          SELECT
	                                                 SUM( amount ) AS taxamount,
		                                             documentMasterAutoID,
		                                             taxMasterID,
		                                             documentDetailAutoID,
		                                             taxGlAutoID,
		                                             srp_erp_taxmaster.inputVatGLAccountAutoID,
		                                             outputVatTransferGL,
		                                             outputVatGL,
		                                             srp_erp_taxledger.isClaimable,
		                                             srp_erp_taxledger.rcmApplicableYN,
                                                     srp_erp_taxledger.taxFormulaMasterID,
                                                     srp_erp_taxledger.taxFormulaDetailID,
		                                             documentID
	                                                 FROM
		                                             srp_erp_taxledger
		                                             LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
	                                                 WHERE
		                                             documentID IN ( 'GRV' ) 
		                                             AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
		                                             AND taxCategory = 2 
	                                                 GROUP BY
		                                             documentMasterAutoID 
                                            ) taxledger ON taxledger.documentMasterAutoID = srp_erp_paysupplierinvoicedetail.grvAutoID
                                            LEFT JOIN (
                                                     SELECT
                                                     SUM( amount ) AS taxamount,
                                                     documentMasterAutoID,
                                                     taxMasterID,
                                                     taxGlAutoID,
                                                     srp_erp_taxmaster.inputVatGLAccountAutoID,
                                                     outputVatTransferGL,
                                                     outputVatGL,
                                                     srp_erp_taxledger.isClaimable,
                                                     srp_erp_taxledger.rcmApplicableYN,
                                                     srp_erp_taxledger.documentDetailAutoID,
                                                     documentID
                                                     FROM
                                                     srp_erp_taxledger
                                                     LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                     LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvDetailsID = srp_erp_taxledger.documentDetailAutoID
                                                     WHERE
                                                     documentID IN ( 'GRV-ADD' ) 
                                                     AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
                                                     AND taxCategory = 2 
                                                     GROUP BY
                                                     documentMasterAutoID,documentDetailAutoID) taxledgerAddon  ON taxledgerAddon.documentDetailAutoID = matchsupplierInvoiceAddon.addonID WHERE
	                                                 InvoiceAutoID = {$InvoiceAutoID} 
	                                                 AND grvType = 'GRV Base' 
	                                                 AND srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID NOT IN ( SELECT documentDetailAutoID AS documentDetailAutoID FROM `srp_erp_taxledger` WHERE `documentID` = 'BSI' GROUP BY documentDetailAutoID ) 
                                                     GROUP BY
	                                                 InvoiceDetailAutoID");

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {

                $this->session->set_flashdata('s', 'Good Received note : ' . count($master_recode) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();

                
                return array('status' => true);
            }
        } else {
            return array('status' => false);
        }
    }

    function delete_supplier_invoice()
    {
        /*$this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $results = $this->db->delete('srp_erp_paysupplierinvoicemaster');
        if ($results) {
            $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
            $this->db->delete('srp_erp_paysupplierinvoicedetail');
            $this->session->set_flashdata('s', 'Item Deleted Successfully');
            return true;
        }*/
        $this->db->select('*');
        $this->db->from('srp_erp_paysupplierinvoicedetail');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $datas = $this->db->get()->row_array();

        $this->db->select('bookingInvCode');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before delete this document.');
            return true;
        } else {
            if ($master['bookingInvCode'] == "0") {
                $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
                $results = $this->db->delete('srp_erp_paysupplierinvoicemaster');
                if ($results) {
                    $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
                    $this->db->delete('srp_erp_paysupplierinvoicedetail');
                    $this->session->set_flashdata('s', 'Deleted Successfully');
                    return true;
                }
            } else {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
                $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }

        }

    }

    function save_supplier_invoice_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0,$updatedBatchNumberArray=[])
    {
        $batchNumberPolicy = getPolicyValues('IB', 'All');
        $companyID = current_companyID();
        $this->db->trans_start();
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('InvoiceAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['InvoiceAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'BSI');
        }

        if ($approvals_status == 1) {
            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $master = $this->db->get()->row_array();

            $this->db->select('*,0 as taxLedgerAmount');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $item_detail = $this->db->get()->result_array();

            $isGroupByTax =  existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster',$system_id,'BSI','InvoiceAutoID');
            if($isGroupByTax == 1){
                $item_detail = $this->db->query("SELECT
                    srp_erp_paysupplierinvoicedetail.*,
                    IFNULL( taxAmount, 0 ) AS taxAmount,
                    IFNULL( bsiitemtaxamount.taxLedgerAmount, 0 ) AS taxLedgerAmount 
                FROM
                    `srp_erp_paysupplierinvoicedetail`
                    LEFT JOIN (
                    SELECT
                        sum( amount ) AS taxLedgerAmount,
                        srp_erp_paysupplierinvoicedetail.itemAutoID 
                    FROM
                        srp_erp_taxledger
                        LEFT JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_taxledger.companyID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
                    WHERE
                        srp_erp_taxledger.documentID = 'BSI' 
                        AND (srp_erp_taxledger.isClaimable = 0) 
                        AND documentMasterAutoID = $system_id
                    GROUP BY
                        srp_erp_paysupplierinvoicedetail.itemAutoID 
                    ) bsiitemtaxamount ON bsiitemtaxamount.ItemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID 
                WHERE
                    `InvoiceAutoID` = $system_id 
                    AND `companyID` = $companyID")->result_array();
            }






            $this->db->select('sum(transactionAmount) as transactionAmount');
            $this->db->where('InvoiceAutoID', $system_id);
            $totalsum = $this->db->get('srp_erp_paysupplierinvoicedetail')->row_array();

            $disciunt = ($master['generalDiscountPercentage'] / 100) * $totalsum['transactionAmount'];

            //echo 'totsum = '.$totalsum['transactionAmount'] .'<br>'.'discamount = '.$disciunt;
            if ($master['documentOrigin'] != 'CINV') {
                for ($a = 0; $a < count($item_detail); $a++) {
     
                    if ($item_detail[$a]['type'] == 'Item' || $item_detail[$a]['type'] == 'PO') {
                        $item = fetch_item_data($item_detail[$a]['itemAutoID']);
                        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
                        $this->db->select('GLAutoID');
                        $this->db->where('controlAccountType', 'ACA');
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                        if ($disciunt > 0) {
                            $amountaftrdisc = ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                        } else {
                            $amountaftrdisc = 0;
                        }
                        $company_loc = (($item_detail[$a]['transactionAmount'] - $amountaftrdisc) / $master['companyLocalExchangeRate']);
                        if ($item['mainCategory'] == 'Inventory' or $item['mainCategory'] == 'Non Inventory' or $item['mainCategory'] =='Service') {
                            $itemAutoID = $item_detail[$a]['itemAutoID'];
                            $qty = $item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID'];
                            $wareHouseAutoID = $item_detail[$a]['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock +{$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];

                            $itemledgerCurrentStock = fetch_itemledger_currentstock($item_detail[$a]['itemAutoID']);
                            $itemledgerTransactionAmountLocalWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'], 'companyLocalExchangeRate');
                            $itemledgerTransactionAmountReportingWac = fetch_itemledger_transactionAmount($item_detail[$a]['itemAutoID'], 'companyReportingExchangeRate');


                            // $item_arr[$a]['currentStock'] = ($item['currentStock'] + $qty);
                            //  $item_arr[$a]['companyLocalWacAmount'] = round(((($item['currentStock'] * $item['companyLocalWacAmount']) + $company_loc) / $item_arr[$a]['currentStock']), $master['companyLocalCurrencyDecimalPlaces']);
                            //  $item_arr[$a]['companyReportingWacAmount'] = round(((($item['currentStock'] * $item['companyReportingWacAmount']) + (($item_detail[$a]['transactionAmount']-$amountaftrdisc) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $master['companyReportingCurrencyDecimalPlaces']);

                            $item_arr[$a]['currentStock'] = ($itemledgerCurrentStock + $qty);

                            $item_arr[$a]['companyLocalWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountLocalWac) + ($company_loc + $item_detail[$a]['taxLedgerAmount'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($itemledgerCurrentStock * $itemledgerTransactionAmountReportingWac) + ((($item_detail[$a]['transactionAmount'] - $amountaftrdisc)+$item_detail[$a]['taxLedgerAmount']) / $master['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), wacDecimalPlaces);


                            if (!empty($item_arr)) {
                                $this->db->where('itemAutoID', trim($item_detail[$a]['itemAutoID']));
                                $this->db->update('srp_erp_itemmaster', $item_arr[$a]);
                            }

                            $itemledger_arr[$a]['documentID'] = $master['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $master['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $master['InvoiceAutoID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $master['bookingInvCode'];
                            $itemledger_arr[$a]['documentDate'] = $master['bookingDate'];
                            $itemledger_arr[$a]['referenceNumber'] = $master['RefNo'];
                            $itemledger_arr[$a]['companyFinanceYearID'] = $master['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $master['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $master['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $master['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $master['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $master['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $item_detail[$a]['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $item_detail[$a]['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $item_detail[$a]['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $item_detail[$a]['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $item_detail[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $item_detail[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $item_detail[$a]['itemDescription'];
                            $itemledger_arr[$a]['SUOMID'] = $item_detail[$a]['SUOMID'];
                            $itemledger_arr[$a]['SUOMQty'] = $item_detail[$a]['SUOMQty'];
                            $itemledger_arr[$a]['defaultUOMID'] = $item_detail[$a]['defaultUOMID'];
                            $itemledger_arr[$a]['defaultUOM'] = $item_detail[$a]['defaultUOM'];
                            $itemledger_arr[$a]['transactionUOM'] = $item_detail[$a]['unitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $item_detail[$a]['unitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $item_detail[$a]['requestedQty'];
                            $itemledger_arr[$a]['convertionRate'] = $item_detail[$a]['conversionRateUOMID'];
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
                            $amountaftrdisc = $item_detail[$a]['transactionAmount'] - ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                            if ($disciunt > 0) {
                                $itemledger_arr[$a]['transactionAmount'] = $amountaftrdisc+ $item_detail[$a]['taxLedgerAmount'];
                            } else {
                                $itemledger_arr[$a]['transactionAmount'] = $item_detail[$a]['transactionAmount']+ $item_detail[$a]['taxLedgerAmount'];
                            }
                            $itemledger_arr[$a]['transactionCurrencyID'] = $master['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $master['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $master['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $master['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $master['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = ($item_arr[$a]['companyLocalWacAmount']);
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = $item_arr[$a]['companyReportingWacAmount'];
                            $itemledger_arr[$a]['partyCurrencyID'] = $master['supplierCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $master['supplierCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
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
                            if ($disciunt > 0) {
                                $assetdiscamnt = ($item_detail[$a]['transactionAmount'] / $totalsum['transactionAmount']) * $disciunt;
                            } else {
                                $assetdiscamnt = 0;
                            }
                            $assat_amount = ((($item_detail[$a]['transactionAmount'] - $assetdiscamnt) / $item_detail[$a]['conversionRateUOMID'])/$item_detail[$a]['requestedQty']);
                            for ($b = 0; $b < ($item_detail[$a]['requestedQty'] / $item_detail[$a]['conversionRateUOMID']); $b++) {
                                $assat_data[$b]['documentID'] = 'FA';
                                $assat_data[$b]['docOriginSystemCode'] = $master['InvoiceAutoID'];
                                $assat_data[$b]['docOriginDetailID'] = $item_detail[$a]['InvoiceDetailAutoID'];
                                $assat_data[$b]['docOrigin'] = 'BSI';
                                $assat_data[$b]['dateAQ'] = $master['bookingDate'];
                                $assat_data[$b]['grvAutoID'] = $master['InvoiceAutoID'];
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
                                $assat_data[$b]['companyLocalAmount'] = round($assat_amount, $assat_data[$b]['transactionCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyLocalCurrencyDecimalPlaces'] = $master['companyLocalCurrencyDecimalPlaces'];
                                $assat_data[$b]['companyReportingCurrencyID'] = $master['companyReportingCurrencyID'];
                                $assat_data[$b]['companyReportingCurrency'] = $master['companyReportingCurrency'];
                                $assat_data[$b]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $assat_data[$b]['companyReportingAmount'] = round($assat_amount, $assat_data[$b]['companyLocalCurrencyDecimalPlaces']);
                                $assat_data[$b]['companyReportingDecimalPlaces'] = $master['companyReportingCurrencyDecimalPlaces'];
                                $assat_data[$b]['supplierID'] = $master['supplierID'];
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
                    }
                }

                if (!empty($itemledger_arr)) {
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
            }
                        
                        
            $this->load->model('Double_entry_model');
            if ($master['documentOrigin'] == 'CINV') {
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_insurance_data($system_id, 'BSI');
            } else {
                $double_entry = $this->Double_entry_model->fetch_double_entry_supplier_invoices_data($system_id, 'BSI');
            }


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
                $generalledger_arr[$i]['project_categoryID'] = isset($double_entry['gl_detail'][$i]['project_categoryID']) ? $double_entry['gl_detail'][$i]['project_categoryID'] : null;
                $generalledger_arr[$i]['project_subCategoryID'] = isset($double_entry['gl_detail'][$i]['project_subCategoryID']) ? $double_entry['gl_detail'][$i]['project_subCategoryID'] : null;
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


            $maxLevel = $this->approvals->maxlevel('BSI');
            $isFinalLevel = !empty($maxLevel) && $level_id == $maxLevel['levelNo'] ? true : false;
            if ($isFinalLevel) {
                $masterID = $this->input->post('InvoiceAutoID');
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
            foreach ($item_detail as $value) {
                if ($value['itemAutoID']) {
                    array_push($itemAutoIDarry, $value['itemAutoID']);
                }
                if ($value['wareHouseAutoID']) {
                    array_push($wareHouseAutoIDDarry, $value['wareHouseAutoID']);
                }
            }

            $company_id = current_companyID();
            $this->db->query("UPDATE srp_erp_purchaseordermaster prd
                        JOIN (
                            SELECT
                                purchaseOrderID AS pid,
                                (
                                    CASE
                                    WHEN balance = 0 THEN
                                        '2'
                                    WHEN balance = requestedtqy THEN
                                        '0'
                                    ELSE
                                        '1'
                                    END
                                ) AS sts
                            FROM
                                (
                                    SELECT
                            t2.purchaseOrderID,
                        sum(requestedtqy) as requestedtqy ,
                            sum(balance) AS balance
                        FROM
                            (
                        SELECT
                                    po.purchaseOrderDetailsID,
                                    purchaseOrderID,
                                    po.itemAutoID,
                                    ifnull((po.requestedQty),0) AS requestedtqy,
                                    (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0)) AS receivedqty,
                                IF (
                                    (
                                        (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                    ) < 0,
                                    0,
                                    (
                                        (po.requestedQty) - (ifnull(gd.receivedQty,0)+ifnull(grd.receivedQty,0))
                                    )
                                ) AS balance
                                FROM
                                    srp_erp_purchaseorderdetails po
                                LEFT JOIN (
                                    SELECT
                                        purchaseOrderMastertID,
                                        ifnull(sum(requestedQty),0) AS receivedQty,
                                        itemAutoID,
                                        purchaseOrderDetailsID
                                    FROM
                                        srp_erp_paysupplierinvoicedetail
                                left join srp_erp_paysupplierinvoicemaster sinm on srp_erp_paysupplierinvoicedetail.InvoiceAutoID=sinm.InvoiceAutoID
                                        where sinm.approvedYN=1
                                    GROUP BY
                                    srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                ) gd ON po.purchaseOrderDetailsID=gd.purchaseOrderDetailsID

                                        LEFT JOIN (
                                    SELECT
                                        purchaseOrderMastertID,
                                        ifnull(sum(receivedQty),0) AS receivedQty,
                                        itemAutoID,
                                        purchaseOrderDetailsID
                                    FROM
                                        srp_erp_grvdetails
                                left join srp_erp_grvmaster grvm on srp_erp_grvdetails.grvAutoID=grvm.grvAutoID
                                        where grvm.grvType='PO Base' and grvm.approvedYN=1
                                    GROUP BY
                                    srp_erp_grvdetails.purchaseOrderDetailsID
                                ) grd ON po.purchaseOrderDetailsID=grd.purchaseOrderDetailsID

                            ) t2 group by t2.purchaseOrderID
                                ) z
                        ) tt ON prd.purchaseOrderID = tt.pid
                        SET prd.isReceived = tt.sts
                        where  prd.companyID = $company_id AND prd.purchaseOrderID=tt.pid");


            if ($itemAutoIDarry && $wareHouseAutoIDDarry) {
                $companyID = current_companyID();
                $exceededitems_master = $this->db->query("SELECT  * FROM srp_erp_itemexceeded WHERE itemAutoID IN (" . join(',', $itemAutoIDarry) . ") AND companyID= $companyID AND warehouseAutoID IN (" . join(',', $wareHouseAutoIDDarry) . ") AND balanceQty>0  ORDER BY exceededItemAutoID ASC")->result_array();
                $exceededMatchID = 0;
                if (!empty($exceededitems_master)) {
                    $this->load->library('sequence');
                    $exceededmatch['documentID'] = "EIM";
                    $exceededmatch['documentDate'] = $master ['bookingDate'];
                    $exceededmatch['orginDocumentID'] = $master ['documentID'];
                    $exceededmatch['orginDocumentMasterID'] = $master ['InvoiceAutoID'];
                    $exceededmatch['orginDocumentSystemCode'] = $master ['bookingInvCode'];
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

                foreach ($item_detail as $itemid) {
                    if ($itemid['type'] == 'Item') {
                        $receivedQty = $itemid['requestedQty'];
                        $receivedQtyConverted = $itemid['requestedQty'] / $itemid['conversionRateUOMID'];
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
                                    $balanceQtyConverted = $exceededItemAutoID['balanceQty'] / $exceededItemAutoID['conversionRateUOM'];
                                    $updatedQtyConverted = $exceededItemAutoID['updatedQty'] / $exceededItemAutoID['conversionRateUOM'];
                                    if ($receivedQtyConverted > $balanceQtyConverted) {
                                        $qty = $receivedQty - $balanceQty;
                                        $qtyconverted = $receivedQtyConverted - $balanceQtyConverted;
                                        $receivedQty = $qty;
                                        $receivedQtyConverted = $qtyconverted;
                                        $exeed['balanceQty'] = 0;
                                        //$exeed['updatedQty'] = $updatedQty+$balanceQty;
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


            $this->db->select('sum(srp_erp_paysupplierinvoicedetail.transactionAmount + IFNULL(srp_erp_paysupplierinvoicedetail.taxAmount, 0)) AS transactionAmount ,srp_erp_paysupplierinvoicedetail.companyLocalExchangeRate ,srp_erp_paysupplierinvoicedetail.companyReportingExchangeRate, srp_erp_paysupplierinvoicedetail.supplierCurrencyExchangeRate');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceAutoID', $system_id);
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID');
            $transactionAmount = $this->db->get()->row_array();

            $company_loc = ($transactionAmount['transactionAmount'] / $transactionAmount['companyLocalExchangeRate']);
            $company_rpt = ($transactionAmount['transactionAmount'] / $transactionAmount['companyReportingExchangeRate']);
            $supplier_cr = ($transactionAmount['transactionAmount'] / $transactionAmount['supplierCurrencyExchangeRate']);

            // $data['approvedYN']             = $status;
            // $data['approvedbyEmpID']        = $this->common_data['current_userID'];
            // $data['approvedbyEmpName']      = $this->common_data['current_user'];
            // $data['approvedDate']           = $this->common_data['current_date'];
            // $data['companyLocalAmount']     = $company_loc;
            // $data['companyReportingAmount'] = $company_rpt;
            // $data['supplierCurrencyAmount'] = $supplier_cr;
            // $data['transactionAmount']      = $transactionAmount['transactionAmount'];

            // $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            // $this->db->update('srp_erp_paysupplierinvoicemaster', $data);

            $this->session->set_flashdata('s', 'Supplier Invoices Approved Successfully.');
        }

        // else{
        //     $this->session->set_flashdata('s', 'Supplier Invoices Approval : Level '.$level_id.' Successfully.');
        // }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {

            $this->db->select('*');
            $this->db->where('InvoiceAutoID', $system_id);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->from('srp_erp_paysupplierinvoicemaster');
            $inv_all= $this->db->get()->row_array();

            if($inv_all){
                if(isset($inv_all['isSrmGenerated']) && $inv_all['isSrmGenerated'] == 1){
                    $res_po = send_approve_po_srm_portal($inv_all['srmInvoiceAutoID'],3);  // 1-po ,2-grv ,3 -customer invoice
                }
            }

            $this->db->trans_commit();
            return true;
        }
    }

    function save_debitnote_header()
    {
        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $debitnoteDate = $this->input->post('dnDate');
        $dnDate = input_format_date($debitnoteDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        //$period = explode('|', trim($this->input->post('financeyear_period') ?? ''));
        $supplierdetails = explode('|', trim($this->input->post('SupplierDetails') ?? ''));
        if ($financeyearperiodYN == 1) {
            $financeyr = explode(' - ', trim($this->input->post('companyFinanceYear') ?? ''));
            $FYBegin = input_format_date($financeyr[0], $date_format_policy);
            $FYEnd = input_format_date($financeyr[1], $date_format_policy);
        } else {
            $financeYearDetails = get_financial_year($dnDate);
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
            $financePeriodDetails = get_financial_period_date_wise($dnDate);

            if (empty($financePeriodDetails)) {
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            } else {

                $_POST['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }
        }
        $supplier_arr = $this->fetch_supplier_data(trim($this->input->post('supplier') ?? ''));
        $data['documentID'] = 'DN';
        $data['debitNoteDate'] = trim($dnDate);
        $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
        $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
        /*$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        $data['FYPeriodDateTo'] = trim($period[1] ?? '');*/
        $data['supplierID'] = trim($this->input->post('supplier') ?? '');
        $data['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data['supplierName'] = $supplier_arr['supplierName'];
        $data['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data['supplierFax'] = $supplier_arr['supplierFax'];
        $data['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data['docRefNo'] = trim($this->input->post('referenceno') ?? '');
        $data['isGroupBasedTax'] =getPolicyValues('GBT', 'All');

        $comments = ($this->input->post('comments'));
        $data['comments'] = str_replace('<br />', PHP_EOL, $comments);
        //$data['comments'] = trim($this->input->post('comments') ?? '');
        // $data['transactionExchangeRate']            = trim($this->input->post('exchangerate') ?? '');
        // $data['transactionCurrency']                = trim($this->input->post('supplier_currency') ?? '');
        // $data['transactionCurrencyDecimalPlaces']   = fetch_currency_desimal($data['transactionCurrency']);
        // $data['companyLocalCurrency']               = $this->common_data['company_data']['company_default_currency'];
        // $default_currency      = currency_conversion($data['transactionCurrency'],$data['companyLocalCurrency']);
        // $data['companyLocalExchangeRate']           = $default_currency['conversion'];
        // $data['companyLocalCurrencyDecimalPlaces']  = $default_currency['DecimalPlaces'];

        // $data['companyReportingCurrency']           = $this->common_data['company_data']['company_reporting_currency'];
        // $reporting_currency    = currency_conversion($data['transactionCurrency'],$data['companyReportingCurrency']);
        // $data['companyReportingExchangeRate']       = $reporting_currency['conversion'];
        // $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        // $data['supplierCurrency']                   = $supplier_arr['supplierCurrency'];
        // $supplierCurrency      = currency_conversion($data['transactionCurrency'],$data['supplierCurrency']);
        // $data['supplierCurrencyExchangeRate']       = $supplierCurrency['conversion'];
        // $data['supplierCurrencyDecimalPlaces']      = $supplierCurrency['DecimalPlaces'];
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

        $data['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data['supplierCurrency'] = $supplier_arr['supplierCurrency'];
        $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('debitNoteMasterAutoID') ?? '')) {
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
            $this->db->update('srp_erp_debitnotemaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('debitNoteMasterAutoID'));
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
            //$data['debitNoteCode'] = $this->sequence->sequence_generator($data['documentID']);

            $this->db->insert('srp_erp_debitnotemaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_segment()
    {
        $this->db->select('segmentCode,description,segmentID');
        $this->db->from('srp_erp_segment');
        $this->db->where('srp_erp_segment.companyCode', $this->common_data['company_data']['company_code']);
        $data = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Segment');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['segmentID'] ?? '') . '|' . trim($row['segmentCode'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }

    function load_debit_note_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(debitNoteDate,\'' . $convertFormat . '\') AS debitNoteDate ,DATE_FORMAT(FYPeriodDateFrom,"%Y-%m-%d") AS FYPeriodDateFrom,DATE_FORMAT(FYPeriodDateTo,"%Y-%m-%d") AS FYPeriodDateTo');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        return $this->db->get('srp_erp_debitnotemaster')->row_array();
    }

    function delete_dn()
    {
        $masterID = trim($this->input->post('debitNoteMasterAutoID') ?? '');
        /*$this->db->select('debitNoteMasterAutoID,transactionAmount,invoiceAutoID');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $detail_arr = $this->db->get()->result_array();
        $company_id = $this->common_data['company_data']['company_id'];
        foreach ($detail_arr as $val_as) {
            $match_id = $val_as['invoiceAutoID'];
            $number = $val_as['transactionAmount'];
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount - {$number}) WHERE invoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        }
        $this->db->delete('srp_erp_debitnotemaster', array('debitNoteMasterAutoID' => trim($this->input->post('debitNoteMasterAutoID') ?? '')));
        $this->db->delete('srp_erp_debitnotedetail', array('debitNoteMasterAutoID' => trim($this->input->post('debitNoteMasterAutoID') ?? '')));

        return array('error' => 0, 'message' => 'Deleted Successfully.');*/

        $this->db->select('*');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            //$this->session->set_flashdata('e', 'Delete detail first.');
            return array('error' => 1, 'message' => 'please delete all detail records before delete this document.');
        } else {
            /*$data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
            $this->db->update('srp_erp_debitnotemaster', $data);
            return array('error' => 0, 'message' => 'Deleted Successfully.');*/

            /* Added */
            $documentCode = $this->db->get_where('srp_erp_debitnotemaster', ['debitNoteMasterAutoID' => $masterID])->row('debitNoteCode');
            $this->db->trans_start();

            $length = strlen($documentCode);
            if ($length > 1) {
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
                $this->db->update('srp_erp_debitnotemaster', $data);
            } else {
                $this->db->where('debitNoteMasterAutoID', $masterID)->delete('srp_erp_debitnotedetail');
                $this->db->where('debitNoteMasterAutoID', $masterID)->delete('srp_erp_debitnotemaster');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                return array('error' => 0, 'message' => 'Deleted Successfully.');
            } else {
                return array('error' => 1, 'message' => 'Error in delete process.');
            }
            /* End */
        }


    }

    function fetch_dn_detail_table()
    {
        update_group_based_tax('srp_erp_debitnotemaster','debitNoteMasterAutoID',trim($this->input->post('debitNoteMasterAutoID') ?? ''),null,null,'DN');

        $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_debitnotemaster',trim($this->input->post('debitNoteMasterAutoID') ?? ''),'DN','debitNoteMasterAutoID')!=''?existTaxPolicyDocumentWise('srp_erp_debitnotemaster',trim($this->input->post('debitNoteMasterAutoID') ?? ''),'DN','debitNoteMasterAutoID'):0);



        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,supplierCurrency,supplierCurrencyDecimalPlaces');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_debitnotemaster');
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('srp_erp_debitnotedetail.*,IFNULL(srp_erp_taxcalculationformulamaster.Description,\' - \') as TaxDescription, srp_erp_activity_code_main.activity_code as activityCodeName');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->join('srp_erp_activity_code_main', 'srp_erp_activity_code_main.id = srp_erp_debitnotedetail.activityCodeID', 'left');
        $this->db->join('srp_erp_taxcalculationformulamaster', 'srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_debitnotedetail.taxCalculationformulaID', 'left');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function save_bsi_tax_detail()
    {

        $isGroupBased = trim($this->input->post('isGroupBasedTax') ?? '');
        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.supplierGLAutoID', 'left');
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('taxDetailAutoID');
        $this->db->where('taxMasterAutoID', $master['taxMasterAutoID']);
        $this->db->where('invoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $this->db->where('companyID', current_companyID());
        $taxexsist = $this->db->get('srp_erp_paysupplierinvoicetaxdetails')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,companyLocalCurrency,companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces,companyReportingCurrency,companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,transactionCurrencyID,companyLocalCurrencyID,companyReportingCurrencyID');
        $this->db->where('InvoiceAutoID', $this->input->post('InvoiceAutoID'));
        $inv_master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        if (!empty($taxexsist)) {
            $this->session->set_flashdata('e', 'Tax type already exist');
            return array('status' => false);
            exit;
        }

        $supplier_arr = fetch_supplier_data($master['supplierAutoID']);
        $data['invoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
        $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
        $data['taxDescription'] = $master['taxDescription'];
        $data['taxShortCode'] = $master['taxShortCode'];
        $data['supplierAutoID'] = $master['supplierAutoID'];
        $data['supplierSystemCode'] = $master['supplierSystemCode'];
        $data['supplierName'] = $master['supplierName'];
        $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
        $data['supplierCurrency'] = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID'] = $master['GLAutoID'];
        $data['systemGLCode'] = $master['systemAccountCode'];
        $data['GLCode'] = $master['GLSecondaryCode'];
        $data['GLDescription'] = $master['GLDescription'];
        $data['GLType'] = $master['subCategory'];


        if ((!empty($this->input->post('afterdiscounttot'))) && (!empty($this->input->post('tax_amount')))) {
            $data['taxPercentage'] = ((($this->input->post('tax_amount')) / $this->input->post('afterdiscounttot')) * 100);
        } else {
            $data['taxPercentage'] = 0;
        }


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
            $this->db->update('srp_erp_paysupplierinvoicetaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_paysupplierinvoicetaxdetails', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Tax Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
        }
    }

    function save_dn_detail()
    {
        $this->db->trans_start();
        $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();
        $segment = explode('|', trim($this->input->post('segment_gl') ?? ''));
        $gl_code = explode('|', trim($this->input->post('gl_code_des') ?? ''));
        $data['debitNoteMasterAutoID'] = trim($this->input->post('debitNoteMasterAutoID') ?? '');
        $data['GLAutoID'] = trim($this->input->post('gl_code') ?? '');
        $data['systemGLCode'] = trim($gl_code[0] ?? '');
        $data['GLCode'] = trim($gl_code[1] ?? '');
        $data['GLDescription'] = trim($gl_code[2] ?? '');
        $data['GLType'] = trim($gl_code[3] ?? '');
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['transactionAmount'] = trim($this->input->post('amount') ?? '');
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master['companyLocalExchangeRate']);
        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master['companyReportingExchangeRate']);
        $data['supplierAmount'] = ($data['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('debitNoteDetailsID') ?? '')) {
            $this->db->where('debitNoteDetailsID', trim($this->input->post('debitNoteDetailsID') ?? ''));
            $this->db->update('srp_erp_debitnotedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Detail : ' . $data['GLDescription'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Detail : ' . $data['GLDescription'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('debitNoteDetailsID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_debitnotedetail', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Debit Note Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Debit Note Detail : ' . $data['GLDescription'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_dn_detail()
    {
        $this->db->select('*');
        $this->db->where('debitNoteDetailsID', $this->input->post('debitNoteDetailsID'));
        return $this->db->get('srp_erp_debitnotedetail')->row_array();
    }

    function delete_dn_detail()
    {
        $this->db->select('InvoiceAutoID,transactionAmount,debitNoteMasterAutoID');
        $this->db->from('srp_erp_debitnotedetail');
        $this->db->where('debitNoteDetailsID', trim($this->input->post('debitNoteDetailsID') ?? ''));
        $detail_arr = $this->db->get()->row_array();
        $debitNoteDetailsID = trim($this->input->post('debitNoteDetailsID') ?? '');
        $debitNoteMasterAutoID = $detail_arr['debitNoteMasterAutoID'];
        $isTaxGroupPolicyEnable = existTaxPolicyDocumentWise('srp_erp_debitnotemaster',$debitNoteMasterAutoID,'DN','debitNoteMasterAutoID');

        if($isTaxGroupPolicyEnable == 1){
            $this->db->delete('srp_erp_taxledger', array('documentID' => 'DN','documentMasterAutoID'=>$debitNoteMasterAutoID,'documentDetailAutoID'=>$debitNoteDetailsID));
        }


        $company_id = $this->common_data['company_data']['company_id'];
        $match_id = $detail_arr['InvoiceAutoID'];
        $number = $detail_arr['transactionAmount'];
        $status = 0;
        $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount -{$number}) WHERE InvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
        $this->db->delete('srp_erp_debitnotedetail', array('debitNoteDetailsID' => trim($this->input->post('debitNoteDetailsID') ?? '')));
        $this->session->set_flashdata('s', 'Debit Note Detail Deleted Successfully');
        return true;
    }

    function dn_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $companyID = current_companyID();
        $currentuser = current_userID();
        $emplocationid = $this->common_data['emplanglocationid'];
        $this->db->select('debitNoteMasterAutoID');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_debitnotedetail');
        $results = $this->db->get()->row_array();
        if (empty($results)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('debitNoteMasterAutoID');
            $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_debitnotemaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $system_id = trim($this->input->post('debitNoteMasterAutoID') ?? '');
                $this->db->select('documentID, debitNoteCode,DATE_FORMAT(debitNoteDate, "%Y") as invYear,DATE_FORMAT(debitNoteDate, "%m") as invMonth,companyFinanceYearID');
                $this->db->where('debitNoteMasterAutoID', $system_id);
                $this->db->from('srp_erp_debitnotemaster');
                $master_dt = $this->db->get()->row_array();
                $this->load->library('sequence');
                if ($master_dt['debitNoteCode'] == "0") {
                    if ($locationwisecodegenerate == 1) {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location == '')) {
                            return array('w', 'Location is not assigned for current employee');
                        } else {
                            if ($emplocationid != '') {
                                $pvCd = $this->sequence->sequence_generator_location($master_dt['documentID'], $master_dt['companyFinanceYearID'], $emplocationid, $master_dt['invYear'], $master_dt['invMonth']);
                            } else {
                                return array('w', 'Location is not assigned for current employee');
                            }
                        }
                    } else {
                        $pvCd = $this->sequence->sequence_generator_fin($master_dt['documentID'], $master_dt['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                    }
                    $validate_code = validate_code_duplication($pvCd, 'debitNoteCode', $system_id, 'debitNoteMasterAutoID', 'srp_erp_debitnotemaster');
                    if (!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }

                    $pvCd = array(
                        'debitNoteCode' => $pvCd
                    );
                    $this->db->where('debitNoteMasterAutoID', $system_id);
                    $this->db->update('srp_erp_debitnotemaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['debitNoteCode'], 'debitNoteCode', $system_id, 'debitNoteMasterAutoID', 'srp_erp_debitnotemaster');
                    if (!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }
                $this->load->library('Approvals');
                $this->db->select('debitNoteMasterAutoID, debitNoteCode,debitNoteDate');
                $this->db->where('debitNoteMasterAutoID', $system_id);
                $this->db->from('srp_erp_debitnotemaster');
                $grv_data = $this->db->get()->row_array();

                $autoApproval = get_document_auto_approval('DN');
                if ($autoApproval == 0) {
                    $approvals_status = $this->approvals->auto_approve($grv_data['debitNoteMasterAutoID'], 'srp_erp_debitnotemaster', 'debitNoteMasterAutoID', 'DN', $grv_data['debitNoteCode'], $grv_data['debitNoteDate']);
                } elseif ($autoApproval == 1) {
                    $approvals_status = $this->approvals->CreateApproval('DN', $grv_data['debitNoteMasterAutoID'], $grv_data['debitNoteCode'], 'Debit note', 'srp_erp_debitnotemaster', 'debitNoteMasterAutoID', 0, $grv_data['debitNoteDate']);
                } else {
                    return array('e', 'Approval levels are not set for this document');
                }

                if ($approvals_status == 1) {
                    $autoApproval = get_document_auto_approval('DN');
                    if ($autoApproval == 0) {
                        $result = $this->save_dn_approval(0, $system_id, 1, 'Auto Approved');
                        if ($result) {
                            return array('s', 'Document confirmed Successfully');
                        }
                    } else {
                        $data = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user']
                        );
                        $this->db->where('debitNoteMasterAutoID', $system_id);
                        $result = $this->db->update('srp_erp_debitnotemaster', $data);
                        if ($result) {
                            return array('s', 'Document confirmed Successfully');
                        }
                    }
                } else if ($approvals_status == 3) {
                    return array('w', 'There are no users exist to perform approval for this document.');
                } else {
                    return array('e', 'Document confirmation failed');
                }
            }
        }
    }

    function fetch_supplier_invoice()
    {
        $this->db->select('debitNoteDate,debitNoteMasterAutoID,supplierID,transactionCurrency, transactionCurrencyDecimalPlaces,transactionCurrencyID');
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $this->db->from('srp_erp_debitnotemaster');
        $data['master'] = $this->db->get()->row_array();

        /*$this->db->select('InvoiceAutoID,bookingInvCode,bookingDate,paymentTotalAmount,transactionCurrency,DebitNoteTotalAmount, transactionAmount, InvoiceAutoID');
        $this->db->where('bookingDate <=', $data['master']['debitNoteDate']);
        $this->db->where('supplierID', $data['master']['supplierID']);
        $this->db->where('transactionCurrency', $data['master']['transactionCurrency']);
        $this->db->where('paymentInvoiceYN', 0);
        $this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_paysupplierinvoicemaster');*/

        /*$output = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID,bookingInvCode,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,RefNo,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,bookingDate,transactionCurrency FROM srp_erp_paysupplierinvoicemaster LEFT JOIN (SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
 LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND paymentInvoiceYN = 0 AND `supplierID` = '{$data['master']['supplierID']}' AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}' AND `bookingDate` <= '{$data['master']['debitNoteDate']}'")->result_array();*/

        $output = $this->db->query("SELECT
                    srp_erp_paysupplierinvoicemaster.InvoiceAutoID,bookingInvCode,paymentTotalAmount,
                    DebitNoteTotalAmount,advanceMatchedTotal,RefNo,
                   --  TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM (ROUND(((((IFNULL(tax.taxPercentage, 0) / 100) * (IFNULL(sid.transactionAmount, 0) - ((IFNULL(generalDiscountPercentage, 0) / 100) * IFNULL(sid.transactionAmount, 0) ) ) ) + IFNULL(sid.transactionAmount, 0)) - ((IFNULL(generalDiscountPercentage, 0 ) / 100) * IFNULL(sid.transactionAmount, 0))), srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces))))) AS transactionAmount,
                   TRIM(
					ROUND(((((
										IFNULL( tax.taxPercentage, 0 ) / 100
										) * (
										IFNULL( sid.transactionAmount, 0 ) - (( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL( sid.transactionAmount, 0 ) )
										)
									) + IFNULL( sid.transactionAmount, 0 )) - ((
									IFNULL( generalDiscountPercentage, 0 ) / 100
									) * IFNULL( sid.transactionAmount, 0 ))),
						srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces)	)+0 AS transactionAmount,

                    bookingDate,transactionCurrency
                FROM srp_erp_paysupplierinvoicemaster
                LEFT JOIN (SELECT invoiceAutoID,(IFNULL(SUM( transactionAmount ),0) + IFNULL(SUM(taxAmount),0) ) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID
                LEFT JOIN (SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax ON tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID
                WHERE confirmedYN = 1
                    AND approvedYN = 1
                    AND paymentInvoiceYN = 0
                    AND `supplierID` = '{$data['master']['supplierID']}'
                    AND `transactionCurrencyID` = '{$data['master']['transactionCurrencyID']}'
                    AND `bookingDate` <= '{$data['master']['debitNoteDate']}'")->result_array();


        $data['detail'] = $output;
        return $data;
    }

    function save_debit_base_items()
    {
        $this->db->trans_start();
        $projectExist = project_is_exist();
        $debitNoteMasterAutoID = trim($this->input->post('debitNoteMasterAutoID') ?? '');
        // $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate');
        // $this->db->where('debitNoteMasterAutoID', $debitNoteMasterAutoID);
        // $master = $this->db->get('srp_erp_debitnotemaster')->row_array();
        $companyID = current_companyID();
        $invoice_id = $this->input->post('InvoiceAutoID');
        $segments = $this->input->post('segment');
        $gl_code_d = $this->input->post('gl_code_dec');
        $amounts = $this->input->post('amounts');
        $gl_codes = $this->input->post('gl_code');
        $code = $this->input->post('bookingInvCode');
        $project = $this->input->post('project');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $project_categoryID = $this->input->post('project_categoryID');
        for ($i = 0; $i < count($invoice_id); $i++) {
            $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate,transactionCurrencyID');
            $this->db->where('InvoiceAutoID', $invoice_id[$i]);
            $master = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();
            $segment = explode('|', $segments[$i]);
            $gl_code_des = explode('|', $gl_code_d[$i]);
            $data[$i]['debitNoteMasterAutoID'] = $debitNoteMasterAutoID;
            $data[$i]['InvoiceAutoID'] = $invoice_id[$i];
            $data[$i]['bookingInvCode'] = $code[$i];
            $data[$i]['projectID'] = $project[$i] ?? null;
            if ($projectExist == 1) {
                $projectCurrency = project_currency($project[$i]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data[$i]['projectID'] = $project[$i];
                $data[$i]['project_subCategoryID'] = $project_subCategoryID[$i];
                $data[$i]['project_categoryID'] = $project_categoryID[$i];
                $data[$i]['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
            }
            $data[$i]['GLAutoID'] = $gl_codes[$i];
            $data[$i]['systemGLCode'] = trim($gl_code_des[0] ?? '');
            $data[$i]['GLCode'] = trim($gl_code_des[1] ?? '');
            $data[$i]['GLDescription'] = trim($gl_code_des[2] ?? '');
            $data[$i]['GLType'] = trim($gl_code_des[3] ?? '');
            $data[$i]['segmentID'] = trim($segment[0] ?? '');
            $data[$i]['segmentCode'] = trim($segment[1] ?? '');
            $data[$i]['transactionAmount'] = $amounts[$i];
            $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master['companyLocalExchangeRate']);
            $data[$i]['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master['companyReportingExchangeRate']);
            $data[$i]['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $data[$i]['supplierAmount'] = ($data[$i]['transactionAmount'] / $master['supplierCurrencyExchangeRate']);
            $data[$i]['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data[$i]['description'] = trim($this->input->post('description') ?? '');
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
            $this->db->query("UPDATE srp_erp_paysupplierinvoicemaster SET DebitNoteTotalAmount = (DebitNoteTotalAmount+{$amo}) WHERE InvoiceAutoID='{$id}'");
        }

        if (!empty($data)) {
            $this->db->insert_batch('srp_erp_debitnotedetail', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();

            $details = $this->db->query("SELECT * FROM srp_erp_debitnotedetail WHERE debitNoteMasterAutoID = $debitNoteMasterAutoID")->result_array();

            $companyID = current_companyID();
            foreach ($details as $det) {
                $dataExist = $this->db->query("SELECT COUNT(taxLedgerAutoID) as taxledgerID 
                                                FROM srp_erp_taxledger 
                                                WHERE documentID = 'DN' AND companyID = {$companyID} AND documentDetailAutoID =  {$det['debitNoteDetailsID']}"
                )->row('taxledgerID');

                if($dataExist == 0 && !empty($det['InvoiceAutoID'])) {
                    // echo 'as';
                    $ledgerDet = $this->db->query("SELECT
                                                        IF(srp_erp_taxmaster.taxCategory = 2, (SELECT vatRegisterYN FROM `srp_erp_company` WHERE company_id = {$companyID}), srp_erp_taxmaster.isClaimable) AS isClaimable,
                                                        supplierCountryID,
                                                        supplierID,
                                                        vatEligible,
                                                        supplierLocationID,
                                                        srp_erp_location.locationType,
                                                        srp_erp_taxledger.*,
                                                        IF(taxCategory = 2 ,inputVatGLAccountAutoID,supplierGLAutoID) as glAutoID,
                                                        transactionAmount 
                                                    FROM
                                                        srp_erp_taxledger
                                                    JOIN (
                                                        SELECT
                                                            srp_erp_paysupplierinvoicedetail.InvoiceAutoID,
                                                            ( IFNULL( SUM( srp_erp_paysupplierinvoicedetail.transactionAmount ), 0 )+ IFNULL( SUM( taxAmount ), 0 ) ) AS transactionAmount,
                                                            supplierID 
                                                        FROM
                                                            srp_erp_paysupplierinvoicedetail
                                                            JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicedetail.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID 
                                                        GROUP BY
                                                            invoiceAutoID 
                                                    ) mastertbl ON mastertbl.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = 'BSI'
                                                    JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = mastertbl.supplierID
                                                	LEFT JOIN srp_erp_location ON srp_erp_location.locationID = srp_erp_suppliermaster.supplierLocationID
                                                    JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                                    JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID 
                                                    WHERE
                                                       invoiceAutoID = {$det['InvoiceAutoID']}")->result_array();

                    if(!empty($ledgerDet)) {
                        $taxAmount = 0;
                        foreach ($ledgerDet as $val) {
                            $dataleg['documentID'] = 'DN';
                            $dataleg['documentMasterAutoID'] = $debitNoteMasterAutoID;
                            $dataleg['documentDetailAutoID'] = $det['debitNoteDetailsID'];
                            $dataleg['taxDetailAutoID'] = null;
                            $dataleg['taxPercentage'] = 0;
                            $dataleg['ismanuallychanged'] = 0;
                            $dataleg['taxFormulaMasterID'] = $val['taxFormulaMasterID'];
                            $dataleg['taxFormulaDetailID'] = $val['taxFormulaDetailID'];
                            $dataleg['taxMasterID'] = $val['taxMasterID'];
                            $dataleg['amount'] = ($val['amount'] / $val['transactionAmount']) * $det['transactionAmount'];
                            $dataleg['formula'] = $val['formula'];
                            $dataleg['taxGlAutoID'] = $val['glAutoID'];
                            $dataleg['transferGLAutoID'] = null;
                            $dataleg['isClaimable'] = $val['isClaimable'];
                            $dataleg['countryID'] = $val['supplierCountryID'];
                            $dataleg['partyVATEligibleYN'] = $val['vatEligible'];
                            $dataleg['partyID'] = $val['supplierID'];
                            $dataleg['locationID'] = $val['supplierLocationID'];
                            $dataleg['locationType'] = $val['locationType'];
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
                        $this->db->where('debitNoteDetailsID', $det['debitNoteDetailsID']);
                        $this->db->update('srp_erp_debitnotedetail', $data_detailTBL);
                    }
                }
            }

            return true;
        }
    }

    function save_dn_approval($autoappLevel = 1, $system_idAP = 0, $statusAP = 0, $commentsAP = 0)
    {
        $this->load->library('Approvals');
        if ($autoappLevel == 1) {
            $system_id = trim($this->input->post('debitNoteMasterAutoID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        } else {
            $system_id = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['debitNoteMasterAutoID'] = $system_id;
            $_post['Level'] = $level_id;
            $_post['status'] = $status;
            $_post['comments'] = $comments;
        }
        if ($autoappLevel == 0) {
            $approvals_status = 1;
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'DN');
        }
        if ($approvals_status == 1) {
            $this->load->model('Double_entry_model');
            $double_entry = $this->Double_entry_model->fetch_double_entry_debit_note_data($system_id, 'DN');
            for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                $generalledger_arr[$i]['documentCode'] = $double_entry['code'];
                $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentType'] = '';
                $generalledger_arr[$i]['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['comments'];
                $generalledger_arr[$i]['chequeNumber'] = '';
                $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['gl_detail'][$i]['transactionExchangeRate'];
                $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['gl_detail'][$i]['companyLocalExchangeRate'];
                $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['gl_detail'][$i]['companyReportingExchangeRate'];
                $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                $generalledger_arr[$i]['partyContractID'] = '';
                $generalledger_arr[$i]['partyType'] = 'SUP';
                $generalledger_arr[$i]['partyAutoID'] = $double_entry['master_data']['supplierID'];
                $generalledger_arr[$i]['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                $generalledger_arr[$i]['partyName'] = $double_entry['master_data']['supplierName'];
                $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                $generalledger_arr[$i]['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                $generalledger_arr[$i]['partyExchangeRate'] = $double_entry['gl_detail'][$i]['partyExchangeRate'];
                $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
                // To get actual amount from debit note detail table
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
                $this->db->select('sum(transactionAmount) as transaction_total, sum(companyLocalAmount) as companyLocal_total, sum(companyReportingAmount) as companyReporting_total, sum(partyCurrencyAmount) as party_total');
                $this->db->where('documentCode', 'DN');
                $this->db->where('documentMasterAutoID', $system_id);
                $totals = $this->db->get('srp_erp_generalledger')->row_array();
                if ($totals['transaction_total'] != 0 or $totals['companyLocal_total'] != 0 or $totals['companyReporting_total'] != 0 or $totals['party_total'] != 0) {
                    $generalledger_arr = array();
                    $ERGL_ID = $this->common_data['controlaccounts']['ERGL'];
                    //echo 'xx<hr/>';
                    $ERGL = fetch_gl_account_desc($ERGL_ID);
                    //print_r($ERGL);
                    $generalledger_arr['documentMasterAutoID'] = $double_entry['master_data']['debitNoteMasterAutoID'];
                    $generalledger_arr['documentCode'] = $double_entry['code'];
                    $generalledger_arr['documentSystemCode'] = $double_entry['master_data']['debitNoteCode'];
                    $generalledger_arr['documentDate'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentType'] = '';
                    $generalledger_arr['documentYear'] = $double_entry['master_data']['debitNoteDate'];
                    $generalledger_arr['documentMonth'] = date("m", strtotime($double_entry['master_data']['debitNoteDate']));
                    $generalledger_arr['documentNarration'] = $double_entry['master_data']['comments'];
                    $generalledger_arr['chequeNumber'] = '';
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
                    $generalledger_arr['partyType'] = 'SUP';
                    $generalledger_arr['partyAutoID'] = $double_entry['master_data']['supplierID'];
                    $generalledger_arr['partySystemCode'] = $double_entry['master_data']['supplierCode'];
                    $generalledger_arr['partyName'] = $double_entry['master_data']['supplierName'];
                    $generalledger_arr['partyCurrencyID'] = $double_entry['master_data']['supplierCurrencyID'];
                    $generalledger_arr['partyCurrency'] = $double_entry['master_data']['supplierCurrency'];
                    $generalledger_arr['partyExchangeRate'] = $double_entry['master_data']['supplierCurrencyExchangeRate'];
                    $generalledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['supplierCurrencyDecimalPlaces'];
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
                    $seg = explode('|', $this->common_data['company_data']['default_segment']);
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
                    //print_r($generalledger_arr);
                    $this->db->insert('srp_erp_generalledger', $generalledger_arr);
                }
            }
            $this->session->set_flashdata('s', 'Debit Note Approval Successfully.');
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

    function delete_supplierInvoices_attachement()
    {

        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";

        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function delete_debitNote_attachement()
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

    function delete_paymentVoucher_attachement()
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


    function fetch_customer_currency_by_id()
    {
        $this->db->select('customerCurrencyID,customerCreditPeriod');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function save_debitNote_detail_GLCode_multiple()
    {

        $this->db->trans_start();
        $projectExist = project_is_exist();
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        $this->db->select('*');
        $this->db->where('debitNoteMasterAutoID', $this->input->post('debitNoteMasterAutoID'));
        $master = $this->db->get('srp_erp_debitnotemaster')->row_array();

        $gl_codes = $this->input->post('gl_code_array');
        $gl_code_des = $this->input->post('gl_code_des');
        $projectID = $this->input->post('projectID');
        $amount = $this->input->post('amount');
        $descriptions = $this->input->post('description');
        $segment_gls = $this->input->post('segment_gl');
        if($this->input->post('activityCode')){
            $activityCodeID = $this->input->post('activityCode');
        }
        $project_categoryID = $this->input->post('project_categoryID');
        $project_subCategoryID = $this->input->post('project_subCategoryID');
        $tax_type = $this->input->post('tax_type');
        $group_based_tax = 1;
        foreach ($gl_codes as $key => $gl_code) {
            $segment = explode('|', $segment_gls[$key]);
            $gl_code = explode('|', $gl_code_des[$key]);

            $data['debitNoteMasterAutoID'] = trim($this->input->post('debitNoteMasterAutoID') ?? '');
            $data['GLAutoID'] = $gl_codes[$key];
            if ($projectExist == 1) {
                $projectCurrency = project_currency($projectID[$key]);
                $projectCurrencyExchangerate = currency_conversionID($master['transactionCurrencyID'], $projectCurrency);
                $data['projectID'] = $projectID[$key];
                $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                $data['project_categoryID'] = $project_categoryID[$key];
                $data['project_subCategoryID'] = $project_subCategoryID[$key];


            }
            $data['systemGLCode'] = trim($gl_code[0] ?? '');
            $data['GLCode'] = trim($gl_code[1] ?? '');
            $data['GLDescription'] = trim($gl_code[2] ?? '');
            $data['GLType'] = trim($gl_code[3] ?? '');
            $data['segmentID'] = trim($segment[0] ?? '');
            $data['segmentCode'] = trim($segment[1] ?? '');
            if($advanceCostCapturing == 1){
                $data['activityCodeID'] = trim($activityCodeID[$key]); 
            }
            $data['description'] = $descriptions[$key];
            $data['transactionAmount'] = round($amount[$key], $master['transactionCurrencyDecimalPlaces']);
            $companyLocalAmount = $data['transactionAmount'] / $master['companyLocalExchangeRate'];
            $data['companyLocalAmount'] = round($companyLocalAmount, $master['companyLocalCurrencyDecimalPlaces']);
            $data['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
            $companyReportingAmount = $data['transactionAmount'] / $master['companyReportingExchangeRate'];
            $data['companyReportingAmount'] = round($companyReportingAmount, $master['companyReportingCurrencyDecimalPlaces']);
            $data['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
            $supplierAmount = $data['transactionAmount'] / $master['supplierCurrencyExchangeRate'];
            $data['supplierAmount'] = round($supplierAmount, $master['supplierCurrencyDecimalPlaces']);
            $data['supplierCurrencyExchangeRate'] = $master['supplierCurrencyExchangeRate'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['isFromInvoice'] = 0;
            $this->db->insert('srp_erp_debitnotedetail', $data);
            $last_id = $this->db->insert_id();
            if (!empty($tax_type[$key])) {
                if ($group_based_tax == 1) {
                    tax_calculation_vat(null, null, $tax_type[$key], 'debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''), $amount[$key], 'DN', $last_id, 0, 1);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            //$this->session->set_flashdata('e', 'Supplier Invoice Detail : Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Debit Note Detail : Saved Failed ');
        } else {
            //$this->session->set_flashdata('s', 'Supplier Invoice Detail : Saved Successfully.');
            $this->db->trans_commit();
            return array('s', 'Debit Note Detail : Saved Successfully.');
        }

    }


    function re_open_supplier_invoice()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function re_open_dn()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('debitNoteMasterAutoID', trim($this->input->post('debitNoteMasterAutoID') ?? ''));
        $this->db->update('srp_erp_debitnotemaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();

    }

    function fetch_signaturelevel_debit_note()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'DN');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();

    }


    function save_bsi_item_detail_multiple()
    {
        $projectExist = project_is_exist();

        $retensionEnabled = getPolicyValues('RETO', 'All');
        $InvoiceDetailAutoID = $this->input->post('InvoiceDetailAutoID');
        $InvoiceAutoID = $this->input->post('InvoiceAutoID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $wareHouse = $this->input->post('wareHouse');
        $uom = $this->input->post('uom');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $comment = $this->input->post('comment');
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectID = $this->input->post('projectID');
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $SUOMQty = $this->input->post('SUOMQty');
        $SUOMID = $this->input->post('SUOMIDhn');
        $ProjectCategory = $this->input->post('project_categoryID');
        $ProjectSubcategory = $this->input->post('project_subCategoryID');
        $tax_type = $this->input->post('tax_type');
       

        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', $InvoiceAutoID, 'BSI', 'InvoiceAutoID');
        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID',$InvoiceAutoID);
       
        $this->db->select('transactionCurrencyID,transactionCurrency,retensionPercentage, transactionExchangeRate, companyLocalCurrencyID, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,companyReportingCurrencyID,supplierCurrencyID,segmentCode,segmentID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master_recode = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->trans_start();
        foreach ($itemAutoIDs as $key => $itemAutoID) {
            if (!trim($this->input->post('InvoiceDetailAutoID') ?? '')) {
                $this->db->select('itemDescription,itemSystemCode');
                $this->db->from('srp_erp_paysupplierinvoicedetail');
                $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->where('wareHouseAutoID', $wareHouseAutoID[$key]);
                $order_detail = $this->db->get()->row_array();

                $this->db->select('mainCategory');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $itemAutoID);
                $serviceitm = $this->db->get()->row_array();
                if (!empty($order_detail) && $serviceitm['mainCategory'] == "Inventory") {
                    return array('w', 'Supplier Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
                }

                $wareHouse_location = explode(' | ', $wareHouse[$key]);
                $item_arr = fetch_item_data($itemAutoID);
                $uomEx = explode(' | ', $uom[$key]);

                $data['InvoiceAutoID'] = trim($InvoiceAutoID);
                $data['itemAutoID'] = $itemAutoID;
                $data['itemSystemCode'] = $item_arr['itemSystemCode'];
                $data['itemDescription'] = $item_arr['itemDescription'];
                if ($projectExist == 1) {
                    $projectCurrency = project_currency($projectID[$key]);
                    $projectCurrencyExchangerate = currency_conversionID($master_recode['transactionCurrencyID'], $projectCurrency);
                    $data['projectID'] = $projectID[$key];
                    $data['projectExchangeRate'] = $projectCurrencyExchangerate['conversion'];
                    $data['project_categoryID'] = $ProjectCategory[$key];
                    $data['project_subCategoryID'] = $ProjectSubcategory[$key];
                }
                $data['SUOMQty'] = $SUOMQty[$key];
                $data['SUOMID'] = $SUOMID[$key];
                $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
                $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
                $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                $data['conversionRateUOMID'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
               // $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                $data['requestedQty'] = $quantityRequested[$key];
                $data['unittransactionAmount'] = $estimatedAmount[$key];
                $data['segmentID'] = $master_recode['segmentID'];
                $data['segmentCode'] = $master_recode['segmentCode'];
                /*$data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
                $data['transactionCurrency'] = $master_recode['transactionCurrency'];
                $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];*/
                $data['transactionAmount'] = (($data['unittransactionAmount'] * $data['requestedQty']) - $discount_amount[$key]);
                //$data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
                //$data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
                $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
                //$data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data['companyLocalAmount'] = (($data['transactionAmount']) / $master_recode['companyLocalExchangeRate']);
                //$data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
                //$data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
                $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
                //$data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data['companyReportingAmount'] = (($data['transactionAmount']) / $master_recode['companyReportingExchangeRate']);
                //$data['partyCurrency'] = $master_recode['partyCurrency'];
                //$data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
                $data['supplierCurrencyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
                //$data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
                $data['supplierAmount'] = (($data['transactionAmount']) / $master_recode['supplierCurrencyExchangeRate']);
                $data['description'] = $comment[$key];

                if($retensionEnabled == 1){
                    $data['retensionPercentage'] = $master_recode['retensionPercentage'];
                    $data['retensionValue'] = round(($data['transactionAmount'] * $master_recode['retensionPercentage']) / 100 , 2);
                }

                //$data['remarks'] = '';
                $data['type'] = 'Item';
                $data['wareHouseAutoID'] = $wareHouseAutoID[$key];

                $itemBatchPolicy = getPolicyValues('IB', 'All');

                if($itemBatchPolicy==1){

                    $batch_number2 = $this->input->post("batch_number[{$key}]");
                    $arraydata2 = implode(",",$batch_number2);
                    $data['batchNumber'] = $arraydata2;
                }

                $data['discountPercentage'] = $discount[$key];
                $data['discountAmount'] = $discount_amount[$key];

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
                $this->db->insert('srp_erp_paysupplierinvoicedetail', $data);
                $last_id = $this->db->insert_id();


                if (!empty($tax_type[$key])) {
                    if ($group_based_tax == 1) {

                        tax_calculation_vat(null, null, $tax_type[$key], 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), ($data['unittransactionAmount'] * $data['requestedQty']), 'BSI', $last_id, $data['discountAmount'], 1,$isRcmDocument);
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
                    $this->add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $InvoiceAutoID, $last_id, 'BSI', $item_data['itemSystemCode'], $subData, $warehouseID);


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
            $this->session->set_flashdata('e', 'Supplier Invoice Detail : Save Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('e', 'Supplier Invoice Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Supplier Invoice Details : Saved Successfully . ');
        }

    }

    function add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code, $itemCode, $data, $warehouseID, $comp_arr = null)
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
                if ($comp_arr) {
                    $data_subItemMaster[$x]['companyID'] = $comp_arr['companyID'];
                    $data_subItemMaster[$x]['createdUserGroup'] = $comp_arr['createdUserGroup'];
                    $data_subItemMaster[$x]['createdPCID'] = $comp_arr['createdPCID'];
                    $data_subItemMaster[$x]['createdUserID'] = $comp_arr['createdUserID'];
                    $data_subItemMaster[$x]['createdDateTime'] = $comp_arr['createdDateTime'];
                } else {
                    $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                }
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data_subItemMaster);
        }
    }

    function save_bsi_item_detail()
    {
        $projectExist = project_is_exist();
        $discount = $this->input->post('discount');
        $discount_amount = $this->input->post('discount_amount');
        $tax_type = $this->input->post('tax_type');
        $group_based_tax = existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($this->input->post('InvoiceAutoID') ?? ''), 'BSI', 'InvoiceAutoID');

        $InvoiceDetailAutoID = trim($this->input->post('InvoiceDetailAutoID') ?? '');
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $itemAutoID=trim($this->input->post('InvoiceAutoID') ?? '');
        $isRcmDocument = isRcmApplicable('srp_erp_paysupplierinvoicemaster','InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        if (!empty($InvoiceDetailAutoID)) {
            $this->db->select('itemDescription,itemSystemCode');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('InvoiceDetailAutoID != ', $InvoiceDetailAutoID);
            $order_detail = $this->db->get()->row_array();

            $this->db->select('mainCategory');
            $this->db->from('srp_erp_itemmaster');
            $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
            $serviceitm = $this->db->get()->row_array();
            if (!empty($order_detail) && $serviceitm['mainCategory'] == "Inventory") {
                return array('w', 'Supplier Invoice Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists . ');
            }
        }

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,companyReportingCurrencyID,supplierCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master_recode = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $this->db->select('GLAutoID');
        $this->db->where('controlAccountType', 'ACA');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);

        $this->db->trans_start();
        $wareHouse_location = explode(' | ', trim($this->input->post('wareHouse') ?? ''));
        $uom = explode(' | ', $this->input->post('uom'));
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $data['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
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


        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['SUOMID'] = $this->input->post('SUOMIDhn');
        $data['SUOMQty'] = $this->input->post('SUOMQty');
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOMID'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unittransactionAmount'] = (trim($this->input->post('estimatedAmount') ?? ''));
        $data['segmentID'] = $master_recode['segmentID'];
        $data['segmentCode'] = $master_recode['segmentCode'];
        //$data['transactionCurrencyID'] = $master_recode['transactionCurrencyID'];
        //$data['transactionCurrency'] = $master_recode['transactionCurrency'];
        $data['transactionExchangeRate'] = $master_recode['transactionExchangeRate'];
        $data['transactionAmount'] = ($data['unittransactionAmount'] * $data['requestedQty'] - $discount_amount);
        //$data['companyLocalCurrencyID'] = $master_recode['companyLocalCurrencyID'];
        //$data['companyLocalCurrency'] = $master_recode['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        //$data['unitcompanyLocalAmount'] = ($data['unittransactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyLocalAmount'] = (($data['transactionAmount']) / $master_recode['companyLocalExchangeRate']);
        //$data['companyReportingCurrencyID'] = $master_recode['companyReportingCurrencyID'];
        //$data['companyReportingCurrency'] = $master_recode['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];
        //$data['unitcompanyReportingAmount'] = ($data['unittransactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['companyReportingAmount'] = (($data['transactionAmount']) / $master_recode['companyReportingExchangeRate']);
        //$data['partyCurrency'] = $master_recode['partyCurrency'];
        //$data['partyCurrencyID'] = $master_recode['partyCurrencyID'];
        $data['supplierCurrencyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
        // $data['unitpartyAmount'] = ($data['unittransactionAmount'] / $master_recode['partyExchangeRate']);
        $data['supplierAmount'] = (($data['transactionAmount']) / $master_recode['supplierCurrencyExchangeRate']);
        $data['description'] = trim($this->input->post('comment') ?? '');
        //$data['remarks'] = trim($this->input->post('remarks') ?? '');
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
        $data['discountPercentage'] = $discount;
        $data['discountAmount'] = $discount_amount;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if ($InvoiceDetailAutoID) {
            /*echo 'payVoucherDetailAutoID: '.$payVoucherDetailAutoID;
            exit;*/

            /** update sub item master */
            $subData['uom'] = $data['unitOfMeasure'];
            $subData['uomID'] = $data['unitOfMeasureID'];
            $subData['InvoiceDetailAutoID'] = $InvoiceDetailAutoID;

            if ($group_based_tax == 1) {

                if (!empty($tax_type)) {

                    tax_calculation_vat(null, null, $tax_type, 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), ($data['unittransactionAmount'] * $data['requestedQty']), 'BSI', $InvoiceDetailAutoID, $discount_amount, 1, $isRcmDocument);
                } else {
                    fetchExistsDetailTBL('BSI', trim($this->input->post('InvoiceAutoID') ?? ''), $InvoiceDetailAutoID, NULL, 1, $data['transactionAmount']);
                }
            }


            $this->edit_sub_itemMaster_tmpTbl($this->input->post('quantityRequested'), $item_data['itemAutoID'], $data['InvoiceAutoID'], $InvoiceDetailAutoID, 'BSI', $data['itemSystemCode'], $subData);

            $this->db->where('InvoiceDetailAutoID', $InvoiceDetailAutoID);
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Supplier Invoice Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Supplier Invoice Detail : ' . $data['itemSystemCode'] . ' Updated Successfully . ');

                //return array('status' => true, 'last_id' => $this->input->post('purchaseOrderDetailsID'));
            }
        }
    }

    function edit_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code = 'BSI', $itemCode = null, $data = array(), $comp_arr = null)
    {
        $this->db->select('isSubitemExist');
        $this->db->from('srp_erp_itemmaster');
        $this->db->where('itemAutoID', $itemAutoID);
        $r = $this->db->get()->row_array();
        $isSubitemExist = $r['isSubitemExist'];

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $payVoucherDetailAutoID = isset($data['InvoiceDetailAutoID']) && !empty($data['InvoiceDetailAutoID']) ? $data['InvoiceDetailAutoID'] : null;
        $wareHouseAutoID = $this->input->post('wareHouseAutoID');

        $result = $this->getQty_subItemMaster_tmpTbl($itemAutoID, $masterID, $detailID);
        //echo $this->db->last_query();

        /** delete existing set */
        $this->delete_sub_itemMaster_existing($itemAutoID, $masterID, $detailID, 'BSI');

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
                        $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/BSI/' . $payVoucherDetailAutoID . '/' . $i;
                        $data_subItemMaster[$x]['uom'] = $uom;
                        $data_subItemMaster[$x]['uomID'] = $uomID;
                        $data_subItemMaster[$x]['wareHouseAutoID'] = $wareHouseAutoID;
                        $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                        $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                        $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                        if ($comp_arr) {
                            $data_subItemMaster[$x]['companyID'] = $comp_arr['companyID'];
                            $data_subItemMaster[$x]['createdUserGroup'] = $comp_arr['createdUserGroup'];
                            $data_subItemMaster[$x]['createdPCID'] = $comp_arr['createdPCID'];
                            $data_subItemMaster[$x]['createdUserID'] = $comp_arr['createdUserID'];
                            $data_subItemMaster[$x]['createdDateTime'] = $comp_arr['createdDateTime'];
                        } else {
                            $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                            $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                            $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                            $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                        }
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
                        if ($comp_arr) {
                            $data_subItemMaster[$x]['companyID'] = $comp_arr['companyID'];
                            $data_subItemMaster[$x]['createdUserGroup'] = $comp_arr['createdUserGroup'];
                            $data_subItemMaster[$x]['createdPCID'] = $comp_arr['createdPCID'];
                            $data_subItemMaster[$x]['createdUserID'] = $comp_arr['createdUserID'];
                            $data_subItemMaster[$x]['createdDateTime'] = $comp_arr['createdDateTime'];
                        } else {
                            $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                            $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                            $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                            $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                        }
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

    function fetch_supplier_po($master)
    {
        $convertFormat = convert_date_format_sql();
        $supplierID = $master['supplierID'];
        $currencyID = $master['transactionCurrencyID'];
        $segmentID = $master['segmentID'];
        $date = format_date($master['bookingDate']);
        return $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID,srp_erp_purchaseordermaster.purchaseOrderCode,DATE_FORMAT(srp_erp_purchaseordermaster.documentDate, '$convertFormat') AS documentDate FROM srp_erp_purchaseorderdetails INNER JOIN srp_erp_purchaseordermaster ON srp_erp_purchaseorderdetails.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID LEFT JOIN (
SELECT
srp_erp_purchaseorderdetails.`purchaseOrderID`,
	 sum(`srp_erp_purchaseorderdetails`.requestedQty-(
		IFNULL(supdetail.supqty, 0) + IFNULL(grvdetail.grvqty, 0)
	) ) AS receivedQty

FROM
	`srp_erp_purchaseorderdetails`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(requestedQty) AS supqty
	FROM
		srp_erp_paysupplierinvoicedetail
	GROUP BY
		purchaseOrderMastertID,
		itemAutoID
) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(receivedQty) AS grvqty
	FROM
		srp_erp_grvdetails
	GROUP BY
		purchaseOrderMastertID,
		itemAutoID
) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
AND (
	`goodsRecievedYN` = 0
	OR `goodsRecievedYN` IS NULL
)
GROUP BY
	srp_erp_purchaseorderdetails.`purchaseOrderID`
) qtybal ON srp_erp_purchaseordermaster.purchaseOrderID = qtybal.purchaseOrderID WHERE (srp_erp_purchaseorderdetails.goodsRecievedYN = 0 OR goodsRecievedYN IS NULL) AND `supplierID` = '{$supplierID}' AND `documentDate` <= '{$date}' AND `segmentID` = '{$segmentID}' AND `transactionCurrencyID` = '{$currencyID}' AND `confirmedYN` = 1 AND `closedYN` = 0 AND `approvedYN` = 1 AND qtybal.receivedQty>0  GROUP BY srp_erp_purchaseordermaster.purchaseOrderCode")->result_array();
    }

    function fetch_po_detail_table()
    {

        $purchaseOrderID = $this->input->post('purchaseOrderID');
        $secondaryCode = getPolicyValues('SSC', 'All');
        $item_code = 'srp_erp_itemmaster.itemSystemCode';
        $item_code_alias = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if ($secondaryCode == 1) {
            $item_code = 'seconeryItemCode';
            $item_code_alias = 'srp_erp_itemmaster.seconeryItemCode as itemSystemCode';
        }


        $data['detail'] = $this->db->query("SELECT
	`srp_erp_purchaseorderdetails`.*, (
		IFNULL(supdetail.supqty, 0) + IFNULL(grvdetail.grvqty, 0)
	) AS receivedQty,
	CONCAT_WS(
		' - Part No : ',

	IF (
		LENGTH(
			srp_erp_purchaseorderdetails.`itemDescription`
		),
		`srp_erp_purchaseorderdetails`.`itemDescription`,
		NULL
	),

IF (
	LENGTH(srp_erp_itemmaster.partNo),
	`srp_erp_itemmaster`.`partNo`,
	NULL
)
	) AS Itemdescriptionpartno,
       IFNULL( srp_erp_taxcalculationformulamaster.Description,' - ') AS taxDescription,
    $item_code_alias
FROM
	`srp_erp_purchaseorderdetails`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(requestedQty) AS supqty
	FROM
		srp_erp_paysupplierinvoicedetail
	WHERE
		purchaseOrderMastertID = '$purchaseOrderID'
	GROUP BY
	purchaseOrderDetailsID
) supdetail ON `supdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN (
	SELECT
		purchaseOrderDetailsID,
		SUM(receivedQty) AS grvqty
	FROM
		srp_erp_grvdetails
	WHERE
		purchaseOrderMastertID = '$purchaseOrderID'
	GROUP BY
	purchaseOrderDetailsID
) grvdetail ON `grvdetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
LEFT JOIN `srp_erp_itemmaster` ON `srp_erp_itemmaster`.`itemAutoID` = `srp_erp_purchaseorderdetails`.`itemAutoID`
LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_purchaseorderdetails.taxCalculationformulaID
WHERE
	`purchaseOrderID` = '$purchaseOrderID'
AND (
	`goodsRecievedYN` = 0
	OR `goodsRecievedYN` IS NULL
)
GROUP BY
	`purchaseOrderDetailsID`")->result_array();

        $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_warehousemaster');
        $data['warehouse'] = $this->db->get()->result_array();

        $data['policy_po_cost_change'] = policy_allow_to_change_po_cost_in_grv();
        return $data;
    }


    function save_po_base_items()
    {
        //$post = $this->input->post();
        $companyID = current_companyID();
        $poAutoID = trim($this->input->post('InvoiceAutoID') ?? '');
        $taxCalculationFormulaID = ($this->input->post('taxCalculationFormulaID'));

        $this->db->trans_start();
        $items_arr = array();
        $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS receivedQty,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS grvreceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where_in('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $this->input->post('DetailsID'));
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
        $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
        $this->db->group_by("purchaseOrderDetailsID");
        $query = $this->db->get()->result_array();

        $purchaseOrderMasterID = array_column($query,'purchaseOrderID');

        $this->db->select('srp_erp_paysupplierinvoicemaster.wareHouseAutoID as wareHouseAutoID,srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->join('srp_erp_warehousemaster', 'srp_erp_warehousemaster.wareHouseAutoID = srp_erp_paysupplierinvoicemaster.wareHouseAutoID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,companyReportingCurrencyID,supplierCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master_recode = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();

        $qty = $this->input->post('qty');
        $amount = $this->input->post('amount');
        $location = $this->input->post('location');
        for ($i = 0; $i < count($query); $i++) {
            $this->db->select('srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription');
            $this->db->from('srp_erp_warehousemaster');
            $this->db->where('wareHouseAutoID', $location[$i]);
            $master = $this->db->get()->row_array();

            $this->db->select('purchaseOrderMastertID');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->where('purchaseOrderMastertID', $query[$i]['purchaseOrderID']);
            $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
            $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
            $this->db->where('wareHouseAutoID', $location[$i]);
            $order_detail = $this->db->get()->result_array();
            $item_data = fetch_item_data($query[$i]['itemAutoID']);
            if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                $this->db->select('itemAutoID');
                $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                $this->db->where('wareHouseAutoID', $location[$i]);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                if (empty($warehouseitems)) {
                    $item_id = array_search($query[$i]['itemSystemCode'], array_column($items_arr, 'itemSystemCode'));
                    if ((string)$item_id == '') {
                        $items_arr[$i]['wareHouseAutoID'] = $location[$i];
                        $items_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                        $items_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                        $items_arr[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                        $items_arr[$i]['barCodeNo'] = $item_data['barcode'];
                        $items_arr[$i]['salesPrice'] = $item_data['companyLocalSellingPrice'];
                        $items_arr[$i]['ActiveYN'] = $item_data['isActive'];
                        $items_arr[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                        $items_arr[$i]['itemDescription'] = $query[$i]['itemDescription'];
                        $items_arr[$i]['unitOfMeasureID'] = $query[$i]['defaultUOMID'];
                        $items_arr[$i]['unitOfMeasure'] = $query[$i]['defaultUOM'];
                        $items_arr[$i]['currentStock'] = 0;
                        $items_arr[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $items_arr[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    }
                }
            }

            if (!empty($order_detail) && $item_data['mainCategory'] == 'Inventory') {
                $this->session->set_flashdata('w', 'PO Details added already.');
            } else {
                $ACA_ID = $this->common_data['controlaccounts']['ACA'];
                $this->db->select('GLAutoID');
                $this->db->where('controlAccountType', 'ACA');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
                $item_data = fetch_item_data($query[$i]['itemAutoID']);
                $potaxamnt = ($query[$i]['taxAmount'] + $query[$i]['generalTaxAmount']) / $query[$i]['requestedQty'];
                $data[$i]['purchaseOrderMastertID'] = $query[$i]['purchaseOrderID'];
                $data[$i]['purchaseOrderCode'] = $query[$i]['purchaseOrderCode'];
                $data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                $data[$i]['InvoiceAutoID'] = trim($this->input->post('InvoiceAutoID') ?? '');
                $data[$i]['type'] = 'PO';
                $data[$i]['taxCalculationformulaID'] = $taxCalculationFormulaID[$i];
                $data[$i]['wareHouseAutoID'] = $location[$i];
                $data[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                $data[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                $data[$i]['conversionRateUOMID'] = $query[$i]['conversionRateUOM'];
                $data[$i]['orderedQty'] = $query[$i]['requestedQty'];
                $data[$i]['orderedAmount'] = $query[$i]['unitAmount'] - ($query[$i]['generalDiscountAmount'] / $query[$i]['requestedQty']) + $potaxamnt;
                $data[$i]['requestedQty'] = $qty[$i];
                $data[$i]['segmentID'] = $master_recode['segmentID'];
                $data[$i]['segmentCode'] = $master_recode['segmentCode'];
                $data[$i]['unittransactionAmount'] = $amount[$i];
                $data[$i]['transactionAmount'] = ($data[$i]['requestedQty'] * $data[$i]['unittransactionAmount']);
                $data[$i]['companyLocalAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
                $data[$i]['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];

                $data[$i]['companyReportingAmount'] = ($data[$i]['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
                $data[$i]['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];

                $data[$i]['supplierAmount'] = ($data[$i]['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);
                $data[$i]['supplierCurrencyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];

                //$data[$i]['financeCategory'] = $item_data['financeCategory'];
                //$data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                if ($item_data['mainCategory'] == 'Inventory') {
                    $data[$i]['GLAutoID'] = $item_data['assteGLAutoID'];
                    $data[$i]['systemGLCode'] = $item_data['assteSystemGLCode'];
                    $data[$i]['GLCode'] = $item_data['assteGLCode'];
                    $data[$i]['GLDescription'] = $item_data['assteDescription'];
                    $data[$i]['GLType'] = $item_data['assteType'];
                } elseif ($item_data['mainCategory'] == 'Fixed Assets') {
                    $this->db->select('srp_erp_chartofaccounts.*');
                    $this->db->from('srp_erp_companycontrolaccounts');
                    $this->db->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_companycontrolaccounts.GLAutoID');
                    $this->db->where('srp_erp_companycontrolaccounts.companyID', current_companyID());
                    $this->db->where('srp_erp_companycontrolaccounts.controlAccountType', 'ACA');
                    $ACA = $this->db->get()->row_array();
                    $data[$i]['GLAutoID'] = $ACA['GLAutoID'];
                    $data[$i]['systemGLCode'] = $ACA['systemAccountCode'];
                    $data[$i]['GLCode'] = $ACA['GLSecondaryCode'];
                    $data[$i]['GLDescription'] = $ACA['GLDescription'];
                    $data[$i]['GLType'] = $ACA['subCategory'];
                } else {
                    $data[$i]['GLAutoID'] = $item_data['costGLAutoID'];
                    $data[$i]['systemGLCode'] = $item_data['costSystemGLCode'];
                    $data[$i]['GLCode'] = $item_data['costGLCode'];
                    $data[$i]['GLDescription'] = $item_data['costDescription'];
                    $data[$i]['GLType'] = $item_data['costType'];
                }
                $data[$i]['description'] = $query[$i]['comment'];
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

                $po_data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                $po_data[$i]['GRVSelectedYN'] = 1;
                if ($query[$i]['requestedQty'] <= (floatval($qty[$i]) + floatval($query[$i]['receivedQty']) + floatval($query[$i]['grvreceivedQty']))) {
                    $po_data[$i]['goodsRecievedYN'] = 1;
                } else {
                    $po_data[$i]['goodsRecievedYN'] = 0;
                }
            }
        }
        if (!empty($items_arr)) {
            $items_arr = array_values($items_arr);
            $this->db->insert_batch('srp_erp_warehouseitems', $items_arr);
        }
        if (!empty($data)) {
            //print_r($data);
            $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);


            $poTax = $this->db->query("SELECT
	                                       srp_erp_purchaseorderdetails.taxCalculationformulaID,
	                                       InvoiceAutoID,
	                                       ((srp_erp_paysupplierinvoicedetail.requestedQty * unitAmount)+(srp_erp_paysupplierinvoicedetail.requestedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)	))  as totalAmount,
		                                   (srp_erp_paysupplierinvoicedetail.requestedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount,
	                                       InvoiceDetailAutoID
                                           FROM
                                           `srp_erp_paysupplierinvoicedetail` 
                                           LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                           WHERE
                                           srp_erp_paysupplierinvoicedetail.companyID = $companyID
                                           AND InvoiceAutoID = $poAutoID")->result_array();


            $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$purchaseOrderMasterID[0]);

            if (existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($this->input->post('InvoiceAutoID') ?? ''), 'BSI', 'InvoiceAutoID') == 1) {
                if (!empty($poTax)) {
                    foreach ($poTax as $val) {
                        if (($val['taxCalculationformulaID'] != 0) && ($val['taxCalculationformulaID'] != '')) {
                            tax_calculation_vat(null, null,$val['taxCalculationformulaID'], 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), $val['totalAmount'], 'BSI', $val['InvoiceDetailAutoID'], $val['discountAmount'], 1,$isRcmDocument);
                        }
                    }
                }
            }

            /** sub item add */
            $InvoiceAutoID = trim($this->input->post('InvoiceAutoID') ?? '');
            $output = $this->db->query("SELECT * FROM srp_erp_paysupplierinvoicedetail INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID AND isSubitemExist = 1 WHERE InvoiceAutoID = '" . $InvoiceAutoID . "'")->result_array();
            if (!empty($output)) {
                foreach ($output as $item) {
                    if ($item['isSubitemExist'] == 1) {
                        $qty = $item['receivedQty'];
                        $subData['uom'] = $data[0]['unitOfMeasure'];
                        $subData['uomID'] = $data[0]['unitOfMeasureID'];
                        $subData['grv_detailID'] = $item['grvDetailsID'];
                        $this->add_sub_itemMaster_tmpTbl($qty, $item['itemAutoID'], $InvoiceAutoID, $item['InvoiceDetailAutoID'], 'BSI', $item['itemSystemCode'], $subData);
                    }
                }
            }
            /** End sub item add */

            $this->db->update_batch('srp_erp_purchaseorderdetails', $po_data, 'purchaseOrderDetailsID');
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Supplier Invoice : Details Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Supplier Invoice : ' . count($query) . ' Item Details Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true);
            }
        } else {
            return array('status' => false, 'data' => 'PO Details added already.');
        }
    }

    function Update_PO_detail()
    {
        $InvoiceDetailAutoID = $this->input->post('InvoiceDetailAutoID');
        $purchaseOrderMasterID = $this->input->post('purchaseOrderMasterID');
        $InvoiceAutoID = trim($this->input->post('InvoiceAutoID') ?? '');
        $companyID = current_companyID();
        $this->db->trans_start();


        $this->db->select('srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription');
        $this->db->from('srp_erp_warehousemaster');
        $this->db->where('wareHouseAutoID', $this->input->post('wareHouseAutoID'));
        $master = $this->db->get()->row_array();

        $this->db->select('transactionCurrencyID,transactionCurrency, transactionExchangeRate, companyLocalCurrency, companyLocalCurrency,companyLocalExchangeRate, companyReportingCurrency ,companyReportingExchangeRate ,supplierCurrency,supplierCurrencyExchangeRate,companyReportingCurrencyID,supplierCurrencyID,segmentCode,segmentID,companyLocalCurrencyID');
        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $master_recode = $this->db->get('srp_erp_paysupplierinvoicemaster')->row_array();


        $data['requestedQty'] = trim($this->input->post('requestedQty') ?? '');
        $data['unittransactionAmount'] = trim($this->input->post('unittransactionAmount') ?? '');
        $data['transactionAmount'] = ($data['requestedQty'] * $data['unittransactionAmount']);
        $data['companyLocalAmount'] = ($data['transactionAmount'] / $master_recode['companyLocalExchangeRate']);
        $data['companyLocalExchangeRate'] = $master_recode['companyLocalExchangeRate'];
        $data['wareHouseAutoID'] = $this->input->post('wareHouseAutoID');
        $data['wareHouseLocation'] = $master['wareHouseLocation'];
        $data['wareHouseDescription'] = $master['wareHouseDescription'];

        $data['companyReportingAmount'] = ($data['transactionAmount'] / $master_recode['companyReportingExchangeRate']);
        $data['companyReportingExchangeRate'] = $master_recode['companyReportingExchangeRate'];

        $data['supplierAmount'] = ($data['transactionAmount'] / $master_recode['supplierCurrencyExchangeRate']);
        $data['supplierCurrencyExchangeRate'] = $master_recode['supplierCurrencyExchangeRate'];
        $data['description'] = trim($this->input->post('description') ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];


        if (trim($InvoiceDetailAutoID)) {
            $this->db->where('InvoiceDetailAutoID', trim($InvoiceDetailAutoID));
            $this->db->update('srp_erp_paysupplierinvoicedetail', $data);

            /** update sub item master */
            $this->db->select('srp_erp_paysupplierinvoicedetail.*,srp_erp_paysupplierinvoicemaster.wareHouseAutoID');
            $this->db->from('srp_erp_paysupplierinvoicedetail');
            $this->db->join('srp_erp_paysupplierinvoicemaster', 'srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID', 'left');
            $this->db->where('srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID', trim($this->input->post('InvoiceDetailAutoID') ?? ''));
            $detail = $this->db->get()->row_array();

            $item_data = fetch_item_data(trim($detail['itemAutoID'] ?? ''));

            $subData['uom'] = $detail['unitOfMeasure'];
            $subData['uomID'] = $detail['unitOfMeasureID'];
            $subData['InvoiceDetailAutoID'] = $InvoiceDetailAutoID;
            $subData['wareHouseAutoID'] = $this->input->post('wareHouseAutoID');


            $this->edit_sub_itemMaster_tmpTbl($this->input->post('requestedQty'), $item_data['itemAutoID'], $detail['InvoiceAutoID'], $InvoiceDetailAutoID, 'BSI', $detail['itemSystemCode'], $subData);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'BSI Detail  Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'BSI Detail  Updated Successfully.');


                /* $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS receivedQty,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS bsireceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode');
                $this->db->from('srp_erp_purchaseorderdetails');
                $this->db->where('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $detail['purchaseOrderDetailsID']);
                $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
                $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
                $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
                $this->db->group_by("purchaseOrderDetailsID");
                $query = $this->db->get()->row_array(); */


                $podetail = fetch_po_detail_status($detail['purchaseOrderDetailsID']);


                if ($podetail['receivedQty'] > 0 || $podetail['bsireceivedQty'] > 0) {
                    $po_data['GRVSelectedYN'] = 1;
                } else {
                    $po_data['GRVSelectedYN'] = 0;
                }

                if ($podetail['requestedQty'] <= (floatval($podetail['receivedQty']) + floatval($podetail['bsireceivedQty']))) {
                    $po_data['goodsRecievedYN'] = 1;
                } else {
                    $po_data['goodsRecievedYN'] = 0;
                }

                $this->db->where('purchaseOrderDetailsID', $detail['purchaseOrderDetailsID']);
                $this->db->update('srp_erp_purchaseorderdetails', $po_data);

                $this->db->trans_commit();

                $poTax = $this->db->query("SELECT
	                                       srp_erp_purchaseorderdetails.taxCalculationformulaID,
	                                       InvoiceAutoID,
	                                       ((srp_erp_paysupplierinvoicedetail.requestedQty * unitAmount)+(srp_erp_paysupplierinvoicedetail.requestedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)	))  as totalAmount,
		                                   (srp_erp_paysupplierinvoicedetail.requestedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount,
	                                       InvoiceDetailAutoID
                                           FROM
                                           `srp_erp_paysupplierinvoicedetail` 
                                           LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID
                                           WHERE
                                           srp_erp_paysupplierinvoicedetail.companyID = $companyID
                                           AND InvoiceAutoID = $InvoiceAutoID")->result_array();


                $isRcmDocument = isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID',$purchaseOrderMasterID);

                if (existTaxPolicyDocumentWise('srp_erp_paysupplierinvoicemaster', trim($this->input->post('InvoiceAutoID') ?? ''), 'BSI', 'InvoiceAutoID') == 1) {
                    if (!empty($poTax)) {
                        foreach ($poTax as $val) {
                            if (($val['taxCalculationformulaID'] != 0) && ($val['taxCalculationformulaID'] != '')) {
                                tax_calculation_vat(null, null,$val['taxCalculationformulaID'], 'InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''), $val['totalAmount'], 'BSI', $val['InvoiceDetailAutoID'], $val['discountAmount'], 1,$isRcmDocument);
                            }
                        }
                    }
                }









                return array('status' => true, 'last_id' => $this->input->post('grvDetailsID'));
            }
        }
    }

    function save_general_discount()
    {
        $discountPercentage = $this->input->post('discountPercentage');
        $discounttotal = $this->input->post('discounttotal');

        $discountAmount = ($discountPercentage / 100) * $discounttotal;
        if ($discountAmount > 0) {
            if ($discountAmount < $discounttotal) {
                $data['generalDiscountAmount'] = $discountAmount;
                $data['generalDiscountPercentage'] = $discountPercentage;

                $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
                $result = $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
                if ($result) {
                    return array('s', 'Discount Amount successfully added');
                }
            } else {
                return array('e', 'Discount amount canot be greater than Discount applicable amount');
            }
        } else {
            return array('e', 'Discount should be greater than zero');
        }
    }

    function delete_discount()
    {
        $data['generalDiscountAmount'] = 0;
        $data['generalDiscountPercentage'] = 0;

        $this->db->where('InvoiceAutoID', trim($this->input->post('InvoiceAutoID') ?? ''));
        $result = $this->db->update('srp_erp_paysupplierinvoicemaster', $data);
        if ($result) {
            return array('s', 'Discount deleted successfully');
        }
    }

    function fetch_customer_details_by_id()
    {
        $CustomerAutoID = trim($this->input->post('customerAutoID') ?? '');
        $this->db->select('customerAutoID as cusAuto,customerName,customerTelephone');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $CustomerAutoID);
        return $this->db->get()->row_array();

    }

    function fetch_customer_details_currency()
    {
        $this->db->select('customerCurrencyID,customerCreditPeriod');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        $data['currency'] = $this->db->get()->row_array();

        $this->db->select('customerAutoID as cusAuto,customerName,customerTelephone');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        $data['detail'] = $this->db->get()->row_array();
        return $data;
    }

    function open_payment_voucher_modal()
    {
        $invoiceAutoID = trim($this->input->post('invoiceAutoID') ?? '');
        $this->db->select('*');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', $invoiceAutoID);
        $data['master'] = $this->db->get()->row_array();

        $this->db->select("GLAutoID");
        $this->db->from('srp_erp_chartofaccounts');
        $this->db->where('isDefaultlBank', 1);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data['GL'] = $this->db->get()->row_array();

        $supplierID = $data['master']['supplierID'];
        $PVdate = current_date();
        $currencyID = $data['master']['transactionCurrencyID'];

        //$dataw = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, (( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrencyID` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' AND srp_erp_customerinvoicemaster.invoiceAutoID = $invoiceAutoID ")->row_array();
        $dataw = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID, bookingInvCode, paymentTotalAmount, advanceMatchedTotal, DebitNoteTotalAmount, RefNo, ( ( ( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * ( IFNULL( det.transactionAmount, 0 ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL( det.transactionAmount, 0 ) ) ) ) + IFNULL( det.transactionAmount, 0 ) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL( det.transactionAmount, 0 ) ) ) AS transactionAmount, bookingDate FROM srp_erp_paysupplierinvoicemaster LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID ) det ON ( `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID ) LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID ) LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_paysupplierinvoicemaster`.`supplierID` WHERE confirmedYN = 1 AND approvedYN = 1 AND `supplierID` = '$supplierID' AND `transactionCurrencyID` = '$currencyID' AND bookingDate <= '$PVdate' AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $invoiceAutoID ")->row_array();
        $data['balance'] = number_format($dataw['transactionAmount'] - ($dataw['paymentTotalAmount'] + $dataw['DebitNoteTotalAmount'] + $dataw['advanceMatchedTotal']), $data['master']['transactionCurrencyDecimalPlaces']);

        return $data;
    }


    function save_paymentvoucher_from_BSI_header()
    {
        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $date_format_policy = date_format_policy();
        $PaymentVoucherdate = $this->input->post('PVdate');
        $PVdate = input_format_date($PaymentVoucherdate, $date_format_policy);
        $PVcheqDate = $this->input->post('PVchequeDate');
        $PVchequeDate = input_format_date($PVcheqDate, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');


        $this->db->select('bookingDate');
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('InvoiceAutoID', $invoiceAutoID);
        $invdate = $this->db->get()->row_array();
        if ($PVdate >= $invdate['bookingDate']) {
            $accountPayeeOnly = 0;
            if (!empty($this->input->post('accountPayeeOnly'))) {
                $accountPayeeOnly = 1;
            }
            $voucherType = $this->input->post('vouchertype');
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
            $this->db->select("segmentCode");
            $this->db->from('srp_erp_segment');
            $this->db->where('segmentID', $this->input->post('segment'));
            $segment = $this->db->get()->row_array();
            $currency_code = fetch_currency_code($this->input->post('transactionCurrencyID'));
            $bank = explode('|', trim($this->input->post('bank') ?? ''));
            $chequeRegister = getPolicyValues('CRE', 'All');

            $data['PVbankCode'] = trim($this->input->post('PVbankCode') ?? '');
            $bank_detail = fetch_gl_account_desc($data['PVbankCode']);
            $data['documentID'] = 'PV';
            $data['companyFinanceYearID'] = trim($this->input->post('financeyear') ?? '');
            $data['companyFinanceYear'] = trim($this->input->post('companyFinanceYear') ?? '');
            $data['FYBegin'] = trim($FYBegin);
            $data['FYEnd'] = trim($FYEnd);
            $data['companyFinancePeriodID'] = trim($this->input->post('financeyear_period') ?? '');
            $data['PVdate'] = trim($PVdate);
            $data['PVNarration'] = trim_desc($this->input->post('narration'));
            $data['accountPayeeOnly'] = $accountPayeeOnly;
            $data['segmentID'] = $this->input->post('segment');
            $data['segmentCode'] = trim($segment['segmentCode'] ?? '');
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
            if ($PVcheqDate == null) {
                $data['PVchequeDate'] = null;
            }
            if ($bank_detail['isCash'] == 1) {
                $data['PVchequeNo'] = null;
                $data['chequeRegisterDetailID'] = null;
                $data['PVchequeDate'] = null;
            } else {
                if ($this->input->post('paymentType') == 2 && (($voucherType == 'Supplier') || ($voucherType == 'Direct') || ($voucherType == 'Employee') || ($voucherType == 'PurchaseRequest'))) {
                    $data['PVchequeNo'] = null;
                    $data['chequeRegisterDetailID'] = null;
                    $data['PVchequeDate'] = null;
                } else {
                    if ($chequeRegister == 1) {
                        $data['chequeRegisterDetailID'] = trim($this->input->post('chequeRegisterDetailID') ?? '');
                        $data['PVchequeNo'] = $this->getchequeDetails($this->input->post('chequeRegisterDetailID'));
                    } else {
                        $data['PVchequeNo'] = trim($this->input->post('PVchequeNo') ?? '');
                        $data['chequeRegisterDetailID'] = null;
                    }
                    $data['PVchequeDate'] = trim($PVchequeDate);
                }
            }
            $data['modeOfPayment'] = ($bank_detail['isCash'] == 1 ? 1 : 2);
            $data['pvType'] = trim($this->input->post('vouchertype') ?? '');
            $data['bankTransferDetails'] = trim($this->input->post('bankTransferDetails') ?? '');
            $data['referenceNo'] = trim_desc($this->input->post('referenceno'));
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
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
            $partyCurrency = currency_conversionID($data['transactionCurrencyID'], $data['partyCurrencyID']);
            $data['partyExchangeRate'] = $partyCurrency['conversion'];
            $data['partyCurrencyDecimalPlaces'] = $partyCurrency['DecimalPlaces'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $type = substr($data['pvType'], 0, 3);
            $data['PVcode'] = 0;
            $result = $this->db->insert('srp_erp_paymentvouchermaster', $data);
            $last_id = $this->db->insert_id();

            if ($result) {
                $supplierID = $this->input->post('partyID');
                $currencyID = $this->input->post('transactionCurrencyID');

                //$dataw = $this->db->query("SELECT srp_erp_customerinvoicemaster.invoiceAutoID, invoiceCode, receiptTotalAmount, advanceMatchedTotal, creditNoteTotalAmount, referenceNo, (( ( ( cid.transactionAmount - cid.totalAfterTax ) - ( ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) )+ IFNULL( genexchargistax.transactionAmount, 0 ) ) * ( IFNULL(tax.taxPercentage, 0) / 100 ) + IFNULL(cid.transactionAmount, 0) ) - ( ( IFNULL( gendiscount.discountPercentage, 0 ) / 100 ) * IFNULL(cid.transactionAmount, 0) ) + IFNULL( genexcharg.transactionAmount, 0 )) AS transactionAmount, invoiceDate, slr.returnsalesvalue as salesreturnvalue FROM srp_erp_customerinvoicemaster LEFT JOIN ( SELECT invoiceAutoID, IFNULL(SUM(transactionAmount), 0) AS transactionAmount, IFNULL(SUM(totalAfterTax), 0) AS totalAfterTax FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID ) cid ON srp_erp_customerinvoicemaster.invoiceAutoID = cid.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, SUM(taxPercentage) AS taxPercentage FROM srp_erp_customerinvoicetaxdetails GROUP BY invoiceAutoID ) tax ON tax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(discountPercentage) AS discountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) gendiscount ON gendiscount.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails WHERE isTaxApplicable = 1 GROUP BY invoiceAutoID ) genexchargistax ON genexchargistax.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails GROUP BY invoiceAutoID ) genexcharg ON genexcharg.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID LEFT JOIN ( SELECT invoiceAutoID, IFNULL( SUM(slaesdetail.totalValue), 0 ) AS returnsalesvalue FROM srp_erp_salesreturndetails slaesdetail GROUP BY invoiceAutoID ) slr ON slr.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID WHERE confirmedYN = 1 AND approvedYN = 1 AND receiptInvoiceYN = 0 AND `customerID` = '{$customerID}' AND `transactionCurrencyID` = '{$currencyID}' AND invoiceDate <= '{$RVdate}' AND srp_erp_customerinvoicemaster.invoiceAutoID = $invoiceAutoID ")->row_array();
                $dataw = $this->db->query("SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID, bookingInvCode, paymentTotalAmount, advanceMatchedTotal, DebitNoteTotalAmount, RefNo, ( ( ( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * ( IFNULL( det.transactionAmount, 0 ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL( det.transactionAmount, 0 ) ) ) ) + IFNULL( det.transactionAmount, 0 ) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL( det.transactionAmount, 0 ) ) ) AS transactionAmount, bookingDate FROM srp_erp_paysupplierinvoicemaster LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID ) det ON ( `det`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID ) LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_paysupplierinvoicemaster.InvoiceAutoID ) LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_paysupplierinvoicemaster`.`supplierID` WHERE confirmedYN = 1 AND approvedYN = 1 AND `supplierID` = '$supplierID' AND `transactionCurrencyID` = '$currencyID' AND bookingDate <= '$PVdate' AND srp_erp_paysupplierinvoicemaster.InvoiceAutoID = $invoiceAutoID ")->row_array();
                $balance = $dataw['transactionAmount'] - ($dataw['paymentTotalAmount'] + $dataw['DebitNoteTotalAmount'] + $dataw['advanceMatchedTotal']);
                if ($balance > 0) {
                    // update_warehouse_items();
                    // update_item_master();
                    $this->db->where('GLAutoID', $data['bankGLAutoID']);
                    $this->db->update('srp_erp_chartofaccounts', array('bankCheckNumber' => $data['PVchequeNo']));

                    if (!empty($data['chequeRegisterDetailID'])) {
                        $this->update_cheque_detail($data['chequeRegisterDetailID'], $last_id);
                    }

                    $InvoiceAutoID = $this->input->post('invoiceAutoID');
                    $settlementAmount = 0;
                    $payVoucherAutoId = $last_id;
                    //$this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,((sid.transactionAmount * (100+IFNULL(tax.taxPercentage,0))) / 100 ) as transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
                    $this->db->select('companyLocalExchangeRate,srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlaces,companyReportingExchangeRate,supplierCurrency, supplierCurrencyExchangeRate,srp_erp_paysupplierinvoicemaster.InvoiceAutoID, DebitNoteTotalAmount,supplierliabilityAutoID, supplierliabilitySystemGLCode, supplierliabilityGLAccount,companyReportingCurrency, supplierliabilityDescription , supplierliabilityType,transactionCurrencyID , companyLocalCurrencyID, transactionCurrency,transactionExchangeRate, companyLocalCurrency, bookingInvCode,RefNo,bookingDate,comments,( ( ( ( IFNULL(tax.taxPercentage, 0) / 100 ) * ( IFNULL(sid.transactionAmount, 0) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL(sid.transactionAmount, 0) ) ) ) + IFNULL(sid.transactionAmount, 0) ) - ( ( IFNULL( generalDiscountPercentage, 0 ) / 100 ) * IFNULL(sid.transactionAmount, 0) ) ) AS transactionAmount,paymentTotalAmount,DebitNoteTotalAmount,advanceMatchedTotal,companyReportingCurrencyID,supplierCurrencyID');
                    $this->db->from('srp_erp_paysupplierinvoicemaster');
                    $this->db->join('(SELECT invoiceAutoID,IFNULL(SUM( transactionAmount ),0) as transactionAmount FROM srp_erp_paysupplierinvoicedetail GROUP BY invoiceAutoID) sid', 'srp_erp_paysupplierinvoicemaster.invoiceAutoID = sid.invoiceAutoID', 'left');
                    $this->db->join('(SELECT invoiceAutoID,SUM(taxPercentage) as taxPercentage FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY invoiceAutoID) tax', 'tax.invoiceAutoID = srp_erp_paysupplierinvoicemaster.invoiceAutoID', 'left');
                    $this->db->where_in('srp_erp_paysupplierinvoicemaster.InvoiceAutoID', $this->input->post('invoiceAutoID'));
                    $master_recode = $this->db->get()->result_array();
                    $amount = $balance;
                    for ($i = 0; $i < count($master_recode); $i++) {
                        $due_amount = ($master_recode[$i]['transactionAmount'] - ($master_recode[$i]['paymentTotalAmount'] + $master_recode[$i]['DebitNoteTotalAmount'] + $master_recode[$i]['advanceMatchedTotal']));
                        $dataD[$i]['payVoucherAutoId'] = $last_id;
                        $dataD[$i]['InvoiceAutoID'] = $master_recode[$i]['InvoiceAutoID'];
                        $dataD[$i]['type'] = 'Invoice';
                        $dataD[$i]['bookingInvCode'] = $master_recode[$i]['bookingInvCode'];
                        $dataD[$i]['referenceNo'] = $master_recode[$i]['RefNo'];
                        $dataD[$i]['bookingDate'] = $master_recode[$i]['bookingDate'];
                        $dataD[$i]['GLAutoID'] = $master_recode[$i]['supplierliabilityAutoID'];
                        $dataD[$i]['systemGLCode'] = $master_recode[$i]['supplierliabilitySystemGLCode'];
                        $dataD[$i]['GLCode'] = $master_recode[$i]['supplierliabilityGLAccount'];
                        $dataD[$i]['GLDescription'] = $master_recode[$i]['supplierliabilityDescription'];
                        $dataD[$i]['GLType'] = $master_recode[$i]['supplierliabilityType'];
                        $dataD[$i]['description'] = $master_recode[$i]['comments'];
                        $dataD[$i]['Invoice_amount'] = $master_recode[$i]['transactionAmount'];
                        $dataD[$i]['due_amount'] = $due_amount;
                        $dataD[$i]['balance_amount'] = ($dataD[$i]['due_amount'] - round($amount, $master_recode[$i]['transactionCurrencyDecimalPlaces']));
                        $dataD[$i]['transactionCurrencyID'] = $master_recode[$i]['transactionCurrencyID'];
                        $dataD[$i]['transactionCurrency'] = $master_recode[$i]['transactionCurrency'];
                        $dataD[$i]['transactionExchangeRate'] = $master_recode[$i]['transactionExchangeRate'];
                        $dataD[$i]['transactionAmount'] = round($amount, $master_recode[$i]['transactionCurrencyDecimalPlaces']);
                        $dataD[$i]['companyLocalCurrencyID'] = $master_recode[$i]['companyLocalCurrencyID'];
                        $dataD[$i]['companyLocalCurrency'] = $master_recode[$i]['companyLocalCurrency'];
                        $dataD[$i]['companyLocalExchangeRate'] = $master_recode[$i]['companyLocalExchangeRate'];
                        $dataD[$i]['companyLocalAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['companyLocalExchangeRate']);
                        $dataD[$i]['companyReportingCurrencyID'] = $master_recode[$i]['companyReportingCurrencyID'];
                        $dataD[$i]['companyReportingCurrency'] = $master_recode[$i]['companyReportingCurrency'];
                        $dataD[$i]['companyReportingExchangeRate'] = $master_recode[$i]['companyReportingExchangeRate'];
                        $dataD[$i]['companyReportingAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['companyReportingExchangeRate']);
                        $dataD[$i]['partyCurrencyID'] = $master_recode[$i]['supplierCurrencyID'];
                        $dataD[$i]['partyCurrency'] = $master_recode[$i]['supplierCurrency'];
                        $dataD[$i]['partyExchangeRate'] = $master_recode[$i]['supplierCurrencyExchangeRate'];
                        $dataD[$i]['partyAmount'] = ($dataD[$i]['transactionAmount'] / $master_recode[$i]['supplierCurrencyExchangeRate']);
                        $dataD[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataD[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataD[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataD[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataD[$i]['modifiedUserName'] = $this->common_data['current_user'];
                        $dataD[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataD[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $dataD[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $dataD[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $dataD[$i]['createdUserName'] = $this->common_data['current_user'];
                        $dataD[$i]['createdDateTime'] = $this->common_data['current_date'];

                        $grv_m[$i]['InvoiceAutoID'] = $InvoiceAutoID;
                        $grv_m[$i]['paymentTotalAmount'] = ($master_recode[$i]['paymentTotalAmount'] + $amount);
                        $grv_m[$i]['paymentInvoiceYN'] = 0;
                        if ($dataD[$i]['balance_amount'] <= 0) {
                            $grv_m[$i]['paymentInvoiceYN'] = 1;
                        }
                    }
                    $data_up_settlement['settlementTotal'] = $settlementAmount;
                    $this->db->where('payVoucherAutoId', $payVoucherAutoId);
                    $this->db->update('srp_erp_paymentvouchermaster', $data_up_settlement);

                    if (!empty($dataD)) {
                        $this->db->update_batch('srp_erp_paysupplierinvoicemaster', $grv_m, 'InvoiceAutoID');
                        $this->db->insert_batch('srp_erp_paymentvoucherdetail', $dataD);
                        return array('s', 'Payment Voucher Saved Successfully.', $last_id);
                    } else {
                        $this->db->delete('srp_erp_paymentvouchermaster', array('payVoucherAutoId' => trim($last_id)));
                        return array('e', 'Payment voucher not Created');
                    }

                } else {
                    $this->db->delete('srp_erp_paymentvouchermaster', array('payVoucherAutoId' => trim($last_id)));
                    return array('e', 'Balance amount should be greater than zero');
                }
            } else {
                return array('e', 'PAyment Voucher   Saved Failed ');
            }
        } else {
            return array('e', 'Payment voucher date should be greater than or equal to invoice date');
        }
    }

    function getchequeDetails($chequeRegisterDetailID)
    {
        $this->db->select('chequeNo');
        $this->db->where('chequeRegisterDetailID', $chequeRegisterDetailID);
        $this->db->from('srp_erp_chequeregisterdetails');
        $result = $this->db->get()->row_array();

        return $result['chequeNo'];
    }

    function fetch_empyoyee($id)
    {
        $this->db->select('Ename1,Ename2,Ename3,Ename4,ECode,EIdNo,EcAddress1,EcAddress2,EcAddress3,EpTelephone,EpFax,EEmail');
        $this->db->where('EIdNo', $id);
        $this->db->from('srp_employeesdetails');
        return $this->db->get()->row_array();
    }

    function update_cheque_detail($chequeRegisterDetailID, $documentMasterAutoID)
    {
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
        $result = $this->db->update('srp_erp_chequeregisterdetails', $data);
    }


    /**
     * 
     * @supplierID integer
     * return <list> Purchase Order List
     * 
     */

     function fetch_supplier_po_list(){

        $supplierID = $this->input->post('supplierID');

        $this->db->select('po.purchaseOrderCode,po.purchaseOrderID,po.supplierName,po.referenceNumber');
        $this->db->where('supplierID', $supplierID);
        $this->db->where('approvedYN', 1);
        $this->db->from('srp_erp_purchaseordermaster as po`');
        return $this->db->get()->result_array();

     }

     /**
     * 
     * @purchaseOrderID integer
     * return details of purchase order
     * 
     */

     function fetch_po_details($po_id=null){

        $purchaseOrderID = ($po_id) ? $po_id : trim($this->input->post('purchaseOrderID') ?? '');

        $this->db->select('*');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster as po`');
        return $this->db->get()->row_array();

     }

    function getInvoiceAutoID($id)
    {
        $this->db->select('InvoiceAutoID');
        $this->db->where('bookingInvCode', $id);
        $this->db->from('srp_erp_paysupplierinvoicemaster');
        return $this->db->get()->row_array();
    }

    function getRangeInvoiceAutoID($InvoiceAutoIDFrom = null, $InvoiceAutoIDTo = null)
    {
        $rangeInvoiceAutoIDs = $this->db->query("SELECT InvoiceAutoID FROM srp_erp_paysupplierinvoicemaster
                                WHERE InvoiceAutoID BETWEEN $InvoiceAutoIDFrom AND $InvoiceAutoIDTo")->result_array();
        return $rangeInvoiceAutoIDs;
    }

    function create_retension_invoice(){

        $invoiceAutoID = $this->input->post('invoiceAutoID');
        $documentID = $this->input->post('documentID');
        $amountset = $this->input->post('amount');

        $this->db->from('srp_erp_paysupplierinvoicemaster');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $invoiceDetails = $this->db->get()->row_array();

        //get currency details
        $currencyDetails = $this->db->where('currencyID',$invoiceDetails['transactionCurrencyID'])->from('srp_erp_currencymaster')->get()->row_array();

        $_POST['invoiceType'] = 'StandardExpense';
        $_POST['bookingDate'] = $invoiceDetails['bookingDate'];
        $_POST['referenceno'] = $invoiceDetails['RefNo'];
        $_POST['supplierID'] = $invoiceDetails['supplierID'];
        $_POST['transactionCurrencyID'] = $invoiceDetails['transactionCurrencyID'];
        $_POST['invoiceDate'] = $invoiceDetails['invoiceDate'];
        $_POST['supplierInvoiceDueDate'] = date('d-m-Y',strtotime('+1 months',strtotime($invoiceDetails['invoiceDate'])));
        $_POST['financeyear'] = $invoiceDetails['companyFinanceYearID'];
        $_POST['financeyear_period'] = $invoiceDetails['companyFinancePeriodID'];
        $_POST['segment'] = $invoiceDetails['segmentID'];
        $_POST['isRetensionYN'] = 1;
        $_POST['retensionInvoiceID'] = $invoiceAutoID;
        $_POST['currency_code'] = $currencyDetails['CurrencyCode'].' | '.$currencyDetails['CurrencyName'];
        $_POST['companyFinanceYear'] = $invoiceDetails['companyFinanceYear'];
        $_POST['InvoiceAutoID'] = '';

        $header = $this->save_supplier_invoice_header();

        if($header){

            $last_id = $header['last_id'];

            $gl_code = array();
            $segment_gl = array();
            $amount = array();
            $Netamount = array();
            $gl_code_des = array();
            $description = array();

            $retensionGLdetails = $this->db->where('GLAutoID',$invoiceDetails['retensionGL'])->from('srp_erp_chartofaccounts')->get()->row_array();

            $gl_code[] = $invoiceDetails['retensionGL'];
            $segment_gl[] = $invoiceDetails['segmentID'].'|'.$invoiceDetails['segmentCode'];
            $amount[] = $amountset;
            $Netamount[] = $amount;
            $gl_code_des[] = $retensionGLdetails['systemAccountCode'].' | '.$retensionGLdetails['GLSecondaryCode'].' | '.$retensionGLdetails['GLDescription'].' | '.$retensionGLdetails['subCategory'];
            $description[] = '';

            $_POST['InvoiceAutoID'] = $last_id;
            $_POST['gl_code'] = $gl_code;
            $_POST['segment_gl'] = $segment_gl;
            $_POST['amount'] = $amount;
            $_POST['Netamount'] = $Netamount;
            $_POST['gl_code_des'] = $gl_code_des;
            $_POST['description'] = $description;
    
            $res = $this->save_bsi_detail_multiple();

        }

        $res = $this->supplier_invoice_confirmation();

        return TRUE;


    }


}
