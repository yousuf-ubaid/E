<?php

class Documentallapproval extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('employee');
    }

    function fetch_approvaldocuments()
    {

        $filter_doc = $this->input->post('Document');
        $companyID = current_companyID();
        $userID = current_userID();
        $where = '';
        $cinv_constrain_usergroup = '';

        if(!empty($filter_doc)){
            $filter_doc = explode(',', $filter_doc);
            $filter_doc2 = "'". implode("', '", $filter_doc)."'";
            $where = "AND documentID IN ({$filter_doc2})";
        }

        $amountBasedApproval = getPolicyValues('ABA', 'All');
        $usergroup_assign = getPolicyValues('UGSE','All');

        $document_codes = $this->db->query("SELECT documentID FROM srp_erp_documentapproved WHERE companyID = {$companyID}
                                     AND approvedYN = 0 {$where} GROUP BY documentID
                                     ORDER BY documentID ASC #limit 32")->result_array();

        $document_codes = array_column($document_codes, 'documentID');
        array_push($document_codes, 'EC');

        $query = '';

        if($usergroup_assign == 1){
            $cinv_constrain_usergroup = 'INNER JOIN srp_erp_segment_usergroups ON srp_erp_approvalusers.groupID = srp_erp_segment_usergroups.userGroupID AND srp_erp_customerinvoicemaster.segmentID = srp_erp_segment_usergroups.segmentID ';
        }

        if(in_array('BD', $document_codes)){ /*** Budget ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT budgetAutoID as DocumentAutoID, srp_erp_budgetmaster.documentID as DocumentID, srp_erp_budgetmaster.documentSystemCode AS DocumentCode,
                       CONCAT( 'Segment : ', srp_erp_segment.description, ' | Currency : ', transactionCurrency,' | Financial Year : ',companyFinanceYear,' | ',narration) AS Narration,
                       '-' AS suppliercustomer, '' AS currency, '' AS Amount, approvalLevelID AS LEVEL, srp_erp_budgetmaster.companyID AS companyID, '' AS transactionCurrencyDecimalPlaces,
                       confirmedByName, DATE_FORMAT( confirmedDate, '%b %D %Y' ) AS `date`, documentApprovedID, '' AS payrollYear, '' AS payrollMonth,'' AS `bankGLAutoID`,
	                   IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
                       FROM srp_erp_budgetmaster
                       LEFT JOIN srp_erp_segment ON srp_erp_budgetmaster.segmentID = srp_erp_segment.segmentID
                       AND srp_erp_budgetmaster.companyID = srp_erp_segment.companyID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_budgetmaster.budgetAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgetmaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_budgetmaster.currentLevelNo
                       LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_budgetmaster.segmentID
                       WHERE srp_erp_budgetmaster.companyID = '{$companyID}' AND srp_erp_budgetmaster.budgetType = 1 AND srp_erp_documentapproved.approvedYN = ''
                       AND srp_erp_documentapproved.documentID = 'BD' AND srp_erp_approvalusers.documentID = 'BD' AND srp_erp_approvalusers.employeeID = '{$userID}'";
        }

        if(in_array('BDT', $document_codes)){ /*** Budget Transfer ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT budgetTransferAutoID as DocumentAutoID, srp_erp_budgettransfer.documentID as DocumentID, srp_erp_budgettransfer.documentSystemCode AS DocumentCode,
	                   CONCAT('Created Date : ',DATE_FORMAT( srp_erp_budgettransfer.documentDate, '%d-%m-%Y' ),' | Financial Year : ',
	                   CONCAT( srp_erp_companyfinanceyear.beginingDate, ' - ', srp_erp_companyfinanceyear.endingDate ),' | ' ,srp_erp_budgettransfer.comments) AS Narration,
	                   '-' AS suppliercustomer, ' '  AS currency, ' '  AS Amount, approvalLevelID AS LEVEL, srp_erp_budgettransfer.companyID AS companyID,
	                   ' ' AS transactionCurrencyDecimalPlaces, srp_erp_budgettransfer.confirmedByName, DATE_FORMAT( srp_erp_budgettransfer.confirmedDate, '%b %D %Y' ) AS `date`, 
	                   documentApprovedID, '' AS payrollYear, '' AS payrollMonth, '' AS `bankGLAutoID`, IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
	                   FROM srp_erp_budgettransfer 
	                   JOIN srp_erp_companyfinanceyear ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID
	                   JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_budgettransfer.budgetTransferAutoID
	                   AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgettransfer.currentLevelNo
	                   JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_budgettransfer.currentLevelNo
	                   LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_budgettransfer.segmentID
	                   WHERE srp_erp_budgettransfer.companyID = '{$companyID}' AND srp_erp_documentapproved.approvedYN = '' AND srp_erp_documentapproved.documentID = 'BDT'
	                   AND srp_erp_approvalusers.documentID = 'BDT' AND srp_erp_approvalusers.employeeID = '{$userID}'";
        }

        if(in_array('BRC', $document_codes)){ /*** Bank Reconciliation ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT b.bankRecAutoID AS DocumentAutoID, b.documentID AS DocumentID, bankRecPrimaryCode as DocumentCode,
                       concat('As Of Date : ',DATE_FORMAT( bankRecAsOf, \"%d/%m/%y\" ),' | Month : ',concat( MONTH, \"/\", YEAR ),' | ',
                       b.description,' | Bank Name : ',bankName,' | GL Code : ',c.systemAccountCode,' | Account Number : ',	c.bankAccountNumber ) AS Narration,
                       \"-\" as suppliercustomer, \" \" as currency, \" \" as Amount, currentLevelNo AS LEVEL, b.companyID AS companyID,
                       \" \" as transactionCurrencyDecimalPlaces, b.confirmedByName, DATE_FORMAT( b.confirmedDate, \"%b %D %Y\" ) AS `date`,
                       documentApprovedID, \"\" AS payrollYear, \"\" AS payrollMonth, `bankGLAutoID`, IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
                       FROM srp_erp_bankrecmaster AS b
                       JOIN srp_erp_documentapproved AS d ON d.documentSystemCode = b.bankRecAutoID
                       AND d.approvalLevelID = b.currentLevelNo
                       LEFT JOIN srp_erp_chartofaccounts AS c ON c.GLAutoID = b.bankGLAutoID
                       JOIN srp_erp_approvalusers AS au ON au.levelNo = b.currentLevelNo
                       LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = b.segmentID
                       WHERE d.documentID = 'BRC' AND au.documentID = 'BRC' AND au.employeeID = '{$userID}' AND au.companyID = '{$companyID}'
                       AND b.companyID = '{$companyID}' AND d.approvedYN = ''";
        }

        if(in_array('BSI', $document_codes)){ /*** Supplier Invoice ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID AS DocumentAutoID, srp_erp_paysupplierinvoicemaster.documentID AS DocumentID,
                       bookingInvCode AS DocumentCode, 
                       comments AS Narration, 
                       srp_erp_suppliermaster.supplierName AS suppliercustomer, 
                       transactionCurrency AS currency,
                       ((((IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))))+IFNULL(det.transactionAmount,0))-((IFNULL(generalDiscountPercentage,0)/100)*IFNULL(det.transactionAmount,0))) AS Amount, 
                       approvalLevelID AS LEVEL,
                       srp_erp_paysupplierinvoicemaster.companyID AS companyID, 
                       srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces, 
                       srp_erp_paysupplierinvoicemaster.confirmedByName,
                       DATE_FORMAT( srp_erp_paysupplierinvoicemaster.confirmedDate, \"%b %D %Y\" ) AS `date`,
                       documentApprovedID, \"\" AS payrollYear, \"\" AS payrollMonth, '' as `bankGLAutoID`, IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
                       FROM srp_erp_paysupplierinvoicemaster
                       LEFT JOIN ( 
                            SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID 
                       ) det ON ( det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
                       LEFT JOIN ( 
                            SELECT SUM(taxPercentage) as taxPercentage, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID 
                       ) addondet ON ( addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
                       JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_paysupplierinvoicemaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_paysupplierinvoicemaster.currentLevelNo
                       LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_paysupplierinvoicemaster.segmentID
                       WHERE srp_erp_documentapproved.documentID = 'BSI' AND srp_erp_approvalusers.documentID = 'BSI' AND srp_erp_approvalusers.employeeID = '{$userID}'
                       AND srp_erp_documentapproved.approvedYN = '0' AND srp_erp_paysupplierinvoicemaster.companyID = '{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'";
        }

        if(in_array('BT', $document_codes)){ /*** Bank Transfer ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT bankTransferAutoID AS DocumentAutoID, srp_erp_banktransfer.documentID AS DocumentID, bankTransferCode AS DocumentCode,
                       narration AS Narration, \"-\" AS suppliercustomer, cuMas.CurrencyCode AS currency, round( transferedAmount, 2 ) AS Amount, approvalLevelID AS LEVEL,
                       srp_erp_banktransfer.companyID AS companyID, cuMas.DecimalPlaces AS transactionCurrencyDecimalPlaces, srp_erp_banktransfer.confirmedByName,
                       DATE_FORMAT( srp_erp_banktransfer.confirmedDate, \"%b %D %Y\" ) AS `date`, documentApprovedID, \"\" AS payrollYear, \"\" AS payrollMonth,
                       '' AS `bankGLAutoID`, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes                   
                       FROM srp_erp_banktransfer 
                       LEFT JOIN srp_erp_chartofaccounts a ON fromBankGLAutoID = a.GLAutoID
                       LEFT JOIN srp_erp_chartofaccounts b ON toBankGLAutoID = b.GLAutoID
                       LEFT JOIN srp_erp_currencymaster cuMas ON cuMas.currencyID = srp_erp_banktransfer.fromBankCurrencyID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_banktransfer.bankTransferAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_banktransfer.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_banktransfer.currentLevelNo
                       LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID =  srp_erp_banktransfer.segmentID
                       WHERE srp_erp_approvalusers.documentID = 'BT' AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.documentID = 'BT'                    
                       AND srp_erp_documentapproved.approvedYN = '0' AND srp_erp_banktransfer.companyID = '{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'";
        }

        if(in_array('CINV', $document_codes)){ /*** Customer Invoice ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_customerinvoicemaster.invoiceAutoID AS DocumentAutoID, srp_erp_customerinvoicemaster.documentID AS DocumentID,
	                   invoiceCode AS DocumentCode, invoiceNarration AS Narration, 
	                   srp_erp_customermaster.customerName AS suppliercustomer, transactionCurrency AS currency,
	                   (IFNULL(addondet.taxPercentage,0)/100)*(IFNULL(det.transactionAmount,0)-IFNULL(det.detailtaxamount,0)-(
	                        (IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0))+IFNULL(genexchargistax.transactionAmount,0)
	                        )+IFNULL(det.transactionAmount,0)-((IFNULL(gendiscount.discountPercentage,0)/100)*IFNULL(det.transactionAmount,0)
	                        )+IFNULL(genexcharg.transactionAmount,0) - IFNULL( retensionTransactionAmount, 0 ) - IFNULL(rebateAmount, 0) AS Amount,
	                    approvalLevelID AS LEVEL, srp_erp_customerinvoicemaster.companyID AS companyID,
	                   srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces, srp_erp_customerinvoicemaster.confirmedByName, 
	                   DATE_FORMAT( srp_erp_customerinvoicemaster.confirmedDate, \"%b %D %Y\" ) AS `date`, documentApprovedID, \"\" AS payrollYear, \"\" AS payrollMonth,
	                   '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
	                   FROM srp_erp_customerinvoicemaster
	                   LEFT JOIN ( 
	                      SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID 
	                   ) det ON ( det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID )
	                   LEFT JOIN ( 
	                        SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails GROUP BY InvoiceAutoID 
	                   ) addondet ON ( addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID )
	                   LEFT JOIN ( 
	                        SELECT SUM(discountPercentage) as discountPercentage ,invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID 
	                   ) gendiscount ON ( gendiscount.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID )
	                   LEFT JOIN ( 
	                        SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails where isTaxApplicable=1  GROUP BY invoiceAutoID 
	                   ) genexchargistax ON ( genexchargistax.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID )
	                   LEFT JOIN ( 
	                        SELECT SUM(transactionAmount) as transactionAmount ,invoiceAutoID FROM srp_erp_customerinvoiceextrachargedetails  GROUP BY invoiceAutoID
	                   ) genexcharg ON ( genexcharg.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID )
	                   LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo
	                   $cinv_constrain_usergroup
                       LEFT join srp_erp_segment segmentmaster on segmentmaster.segmentID  = srp_erp_customerinvoicemaster.segmentID
	                   WHERE srp_erp_documentapproved.documentID = 'CINV' AND srp_erp_approvalusers.documentID = 'CINV' AND srp_erp_documentapproved.companyID = '{$companyID}'
	                   AND srp_erp_approvalusers.companyID = '{$companyID}' AND srp_erp_customerinvoicemaster.companyID = '{$companyID}' 
	                   AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.approvedYN = '0'";
        }

        if(in_array('CN', $document_codes)){ /*** Credit Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_creditnotemaster.creditNoteMasterAutoID AS DocumentAutoID, srp_erp_creditnotemaster.documentID AS DocumentID,
                       creditNoteCode AS DocumentCode, IFNULL( comments, '-' ) AS Narration, srp_erp_customermaster.customerName AS suppliercustomer,
                       transactionCurrency AS currency, det.transactionAmount AS Amount, approvalLevelID AS LEVEL, srp_erp_creditnotemaster.companyID AS companyID,
                       srp_erp_creditnotemaster.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, srp_erp_creditnotemaster.confirmedByName,
                       DATE_FORMAT( srp_erp_creditnotemaster.confirmedDate, \"%b %D %Y\" ) AS `date`, documentApprovedID, \"\" AS payrollYear, \"\" AS payrollMonth,
                       '' AS bankGLAutoID, '' AS segmentcodedes
                       FROM srp_erp_creditnotemaster
                       LEFT JOIN ( 
                            SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID 
                       ) det ON ( det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID )
                       LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_creditnotemaster.creditNoteMasterAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_creditnotemaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_creditnotemaster.currentLevelNo
                       WHERE srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_creditnotemaster.companyID = '{$companyID}'
                       AND srp_erp_approvalusers.companyID = '{$companyID}' AND srp_erp_documentapproved.documentID = 'CN' AND srp_erp_approvalusers.documentID = 'CN'
                       AND srp_erp_documentapproved.approvedYN = '0'";
        }

        if(in_array('CNT', $document_codes) or in_array('QUT', $document_codes) or in_array('SO', $document_codes)){ /*** Contract / Quotation / Sales Order ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_contractmaster.contractAutoID AS DocumentAutoID,
                        `srp_erp_contractmaster`.`documentID` AS `DocumentID`,
                        `contractCode` AS DocumentCode,
                        `contractNarration` AS Narration,
                        `srp_erp_customermaster`.`customerName` AS `suppliercustomer`,
                        `transactionCurrency` AS currency,
                        `det`.`transactionAmount` AS `Amount`,
                        srp_erp_contractmaster.currentLevelNo AS LEVEL,
                        srp_erp_contractmaster.companyID AS companyID,
                        srp_erp_contractmaster.transactionCurrencyDecimalPlaces,
                        srp_erp_contractmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_contractmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_contractmaster`
                        LEFT JOIN ( SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount, contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID ) det ON ( `det`.`contractAutoID` = srp_erp_contractmaster.contractAutoID )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_contractmaster`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_contractmaster`.`contractAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_contractmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_contractmaster`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster ON segmentmaster.segmentID = srp_erp_contractmaster.segmentID 
                    WHERE
                        `srp_erp_documentapproved`.`documentID` IN ( 'CNT','QUT', 'SO' )
                        AND `srp_erp_approvalusers`.`documentID` IN ( 'CNT','QUT', 'SO' )
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_contractmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    GROUP BY `srp_erp_documentapproved`.`documentSystemCode`";
        }

        if(in_array('DC', $document_codes)){ /*** Donor Collections ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `collectionAutoId` as DocumentAutoID,
                        `documentCode` as DocumentID,
                        `documentSystemCode` as DocumentCode,
                        CONCAT(\"Donor Name : \",NAME,\" | \",narration)  AS Narration,
                        \"-\" as suppliercustomer,
                        transactionCurrency AS currency,
                        transactionAmount AS Amount,
                        approvalLevelID AS LEVEL,
                    
                      companyID AS companyID,
                        transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                         confirmedByName,
                        DATE_FORMAT(confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM
                        (
                    SELECT
                        documentApprovedID,
                        `srp_erp_ngo_donorcollectionmaster`.`approvedYN`,
                        `srp_erp_ngo_donorcollectionmaster`.`confirmedDate`,
                        `srp_erp_ngo_donorcollectionmaster`.`confirmedByName`,
                        `srp_erp_ngo_donorcollectionmaster`.`companyID`,
                        `srp_erp_ngo_donorcollectionmaster`.`transactionCurrencyDecimalPlaces`,
                        `srp_erp_ngo_donorcollectionmaster`.`narration`,
                        `approvalLevelID`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentCode`,
                        `confirmedYN`,
                        `srp_erp_ngo_donorcollectionmaster`.`collectionAutoId`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentSystemCode`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentDate`,
                        `referenceNo`,
                        `transactionCurrency`,
                        `donorsID`,
                        FORMAT( IFNULL( transactionAmount, 0 ), transactionCurrencyDecimalPlaces ) AS transactionAmount,
                    NAME
                    FROM
                        srp_erp_ngo_donorcollectionmaster
                        LEFT JOIN srp_erp_ngo_donors ON donorsID = contactID
                        LEFT JOIN ( SELECT sum( transactionAmount ) AS transactionAmount, collectionAutoId FROM srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId ) srp_erp_ngo_donorcollectiondetails ON srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId
                        LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_ngo_donorcollectionmaster.collectionAutoId
                        AND approvalLevelID = currentLevelNo
                        LEFT JOIN srp_erp_approvalusers ON levelNo = srp_erp_ngo_donorcollectionmaster.currentLevelNo
                    WHERE
                        isDeleted != 1
                        AND srp_erp_documentapproved.documentID = 'DC'
                        AND srp_erp_approvalusers.documentID = 'DC'
                        AND employeeID = '{$userID}'
                        AND srp_erp_ngo_donorcollectionmaster.approvedYN = 0
                        AND srp_erp_ngo_donorcollectionmaster.companyID = '{$companyID}'
                    ORDER BY
                        collectionAutoId DESC
                        ) t";
        }

        if(in_array('DN', $document_codes)){ /*** Debit Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_debitnotemaster.debitNoteMasterAutoID AS DocumentAutoID,
                        srp_erp_debitnotemaster.documentID AS DocumentID,
                        debitNoteCode AS DocumentCode,
                        IFNULL( comments, '-' ) AS Narration,
                        IFNULL( `srp_erp_suppliermaster`.`supplierName`, '-' ) AS suppliercustomer,
                        `transactionCurrency` AS currency,
                        `det`.`transactionAmount` AS Amount,
                        approvalLevelID AS LEVEL,
                        srp_erp_debitnotemaster.companyID AS companyID,
                        srp_erp_debitnotemaster.transactionCurrencyDecimalPlaces,
                        srp_erp_debitnotemaster.confirmedByName,
                        DATE_FORMAT( srp_erp_debitnotemaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM
                        `srp_erp_debitnotemaster`
                        LEFT JOIN ( SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount, debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID ) det ON ( `det`.`debitNoteMasterAutoID` = srp_erp_debitnotemaster.debitNoteMasterAutoID )
                        JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_debitnotemaster`.`supplierID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_debitnotemaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_debitnotemaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_debitnotemaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`documentID` = 'DN'
                        AND `srp_erp_approvalusers`.`documentID` = 'DN'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('DO', $document_codes)){ /*** Delivery Order ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                masterTbl.DOAutoID AS DocumentAutoID,
                masterTbl.documentID AS DocumentID,
                masterTbl.DOCode AS DocumentCode,
                narration AS Narration,
                customerName AS suppliercustomer,
                cur_mas.CurrencyCode AS currency,
                transactionAmount AS Amount,
                approvalLevelID AS LEVEL,
                masterTbl.companyID,
                transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                masterTbl.confirmedByName,
                DATE_FORMAT( masterTbl.confirmedDate, \" % b % D % Y\" ) AS date,
                documentApprovedID,
                \"\" AS `payrollYear`,
                '' AS `payrollMonth`,
                '' AS bankGLAutoID, segmentCode AS segmentcodedes
            FROM
                srp_erp_deliveryorder masterTbl
                JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.DOAutoID
                JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.transactionCurrencyID 
                AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo 
            WHERE
                srp_erp_documentapproved.documentID = 'DO' 
                AND srp_erp_approvalusers.documentID = 'DO' 
                AND srp_erp_documentapproved.companyID = '{$companyID}' 
                AND srp_erp_approvalusers.companyID = '{$companyID}' 
                AND srp_erp_approvalusers.employeeID = '{$userID}' 
                AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array('EC', $document_codes)) { /*** Expense Claim ***/
           $query .= ($query != '')? ' UNION ': '';

            $query_expense = "SELECT
                    EC.expenseClaimMasterAutoID AS DocumentAutoID,
                    EC.documentID AS DocumentID,
                    expenseClaimCode AS DocumentCode,
                    CONCAT( ' Description : ', EC.comments, ' | Claimed Date : ', DATE_FORMAT( expenseClaimDate, '%d-%m-%Y' ) ) AS Narration,
                    claimedByEmpName AS suppliercustomer,
                    det_expense.empCurrency AS currency,
                    IFNULL(det_expense.transactionAmount,0) AS Amount,
                    \"\" as Level,
                    EC.companyID AS companyID,
                    det_expense.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    EC.confirmedByName,
                    DATE_FORMAT( EC.confirmedDate, '%b %D %Y' ) AS date,
                    \"\" AS documentApprovedID,
                    \"\" AS payrollYear,
                    \"\" AS payrollMonth,
                    \"\" AS bankGLAutoID,
                    IFNULL( segmentmaster.segmentCode, '-' ) AS segmentcodedes 
                    
                FROM
                    `srp_erp_expenseclaimmaster` AS `EC`
                    JOIN `srp_employeesdetails` `empTB` ON `empTB`.`EIdNo` = `EC`.`claimedByEmpID`
                    LEFT JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `EC`.`expenseClaimMasterAutoID` 
                    LEFT JOIN ( SELECT SUM( empCurrencyAmount ) AS transactionAmount, expenseClaimMasterAutoID, empCurrency, transactionCurrencyDecimalPlaces FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID ) det_expense ON ( det_expense.expenseClaimMasterAutoID = EC.expenseClaimMasterAutoID )
                    AND `approve`.`approvalLevelID` = `EC`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `EC`.`currentLevelNo` 
                    LEFT JOIN srp_erp_segment segmentmaster ON segmentmaster.segmentID = EC.segmentID 
                WHERE
                    `approve`.`documentID` = 'EC' 
                    AND `ap`.`documentID` = 'EC' 
                    AND `approve`.`approvedYN` = '0' 
                    AND `EC`.`companyID` = '{$companyID}' 
                    AND `ap`.`companyID` = '{$companyID}' 
                    AND (
                    `ap`.`employeeID` = '{$userID}' 
                OR (
                ap.employeeID = - 1 
                AND EC.claimedByEmpID IN (
                SELECT
                emp_manager.empID 
                FROM
                srp_employeesdetails AS emp_detail
                JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID 
                AND `emp_manager`.`active` = 1 
                AND `emp_manager`.`companyID` = '{$companyID}' 
                AND emp_manager.managerID = '{$userID}' 
                ) 
                    ) 
                    OR (
                        ap.employeeID = - 2 
                        AND EC.claimedByEmpID IN (
                        SELECT
                            emp_detail.EIdNo 
                        FROM
                            srp_employeesdetails AS emp_detail
                            JOIN srp_empdepartments AS emp_dep ON emp_detail.EIdNo = emp_dep.EmpID
                            JOIN srp_departmentmaster AS srp_dep ON emp_dep.DepartmentMasterID = srp_dep.DepartmentMasterID 
                            AND `emp_dep`.`isactive` = 1 
                            AND `emp_dep`.Erp_companyID = '{$companyID}' 
                            AND srp_dep.hod_id = '{$userID}' 
                        ) 
                    ) 
                    OR (
                        ap.employeeID = - 3 
                        AND EC.claimedByEmpID IN (
                        SELECT
                            emp_detail.Eidno 
                        FROM
                            srp_employeesdetails AS emp_detail
                            JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID
                            JOIN ( SELECT * FROM srp_erp_employeemanagers ) AS top_manager ON top_manager.empID = emp_manager.managerID 
                        WHERE
                            emp_manager.active = 1 
                            AND `emp_manager`.`companyID` = '{$companyID}' 
                            AND top_manager.managerID = '{$userID}' 
                        ) 
                    ) 
                    ) 
                ";

            //ORDER BY `documentCode` DESC 

           $query .= $query_expense;

            
        }

        if(in_array('FA', $document_codes)){ /*** Fixed Asset ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `faID` AS DocumentAutoID,
                        srp_erp_fa_asset_master.documentID AS DocumentID,
                        `faCode` AS DocumentCode,
                        CONCAT( srp_erp_fa_asset_master.assetDescription, \" | Asset Depreciation Date : \", DATE_FORMAT( srp_erp_fa_asset_master.dateDEP, '%Y-%m-%d' ), \" | Asset Acquired Date : \", DATE_FORMAT( srp_erp_fa_asset_master.dateAQ, '%Y-%m-%d' ) ) AS Narration,
                        \"-\" AS suppliercustomer,
                        srp_erp_fa_asset_master.transactionCurrency AS currency,
                        srp_erp_fa_asset_master.transactionAmount AS Amount,
                        `approvalLevelID` AS LEVEL,
                        `srp_erp_fa_asset_master`.`companyID` AS companyID,
                        transactionCurrencyDecimalPlaces,
                        srp_erp_fa_asset_master.confirmedByName,
                        DATE_FORMAT( srp_erp_fa_asset_master.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_fa_asset_master`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_fa_asset_master`.`faID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_asset_master`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_asset_master`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_fa_asset_master.segmentID
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'FA'
                        AND `srp_erp_approvalusers`.`documentID` = 'FA'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_fa_asset_master`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'";
        }
       
        if(in_array('FAD', $document_codes)){ /*** Fixed Asset Depreciation ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `depMasterAutoID` AS DocumentAutoID,
                    srp_erp_fa_depmaster.documentID AS DocumentID,
                    `depCode` AS DocumentCode,
                    CONCAT ( IF ( depType = 1, 'Adhoc Depreciation', 'Monthly Depreciation' ), ' | Depreciation Date : ', DATE_FORMAT( srp_erp_fa_depmaster.depDate, '%Y-%m-%d' ) ) AS Narration,
                    \"-\" AS suppliercustomer,
                    srp_erp_fa_depmaster.transactionCurrency AS currency,
                    transactionAmount AS Amount,
                    `approvalLevelID` AS LEVEL,
                    `srp_erp_fa_depmaster`.`companyID` AS companyID,
                    transactionCurrencyDecimalPlaces,
                    srp_erp_fa_depmaster.confirmedByName,
                    DATE_FORMAT( srp_erp_fa_depmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`,
                    '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_fa_depmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_fa_depmaster`.`depMasterAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_depmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_depmaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_fa_depmaster.segmentID 
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'FAD'
                    AND `srp_erp_approvalusers`.`documentID` = 'FAD'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_fa_depmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array('FS', $document_codes)){ /*** Final Settlement ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `masterID` as DocumentAutoID,
                    fm.documentID as DocumentID,
                    `fm`.`documentCode` AS `DocumentCode`,
                    CONCAT('Emp Code : ',ECode,' | Emp Name: ',Ename2,' | ',narration) as Narration,
                    \"-\" as suppliercustomer,
                    \" \"  AS currency,
                    \" \"  AS Amount,
                    `approvalLevelID` AS LEVEL,
                    fm.companyID AS companyID,
                
                    \" \" AS transactionCurrencyDecimalPlaces,
                    fm.confirmedByName,
                    DATE_FORMAT( fm.confirmedDate, \"%b %D %Y\" ) AS `date`,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`,
                    '' AS bankGLAutoID, '' AS segmentcodedes
                FROM
                    `srp_erp_pay_finalsettlementmaster` AS `fm`
                    JOIN `srp_employeesdetails` `empTB` ON `empTB`.`EIdNo` = `fm`.`empID`
                    JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `fm`.`masterID`
                    AND `approve`.`approvalLevelID` = `fm`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `fm`.`currentLevelNo`
                WHERE
                    `approve`.`documentID` = 'FS'
                    AND `ap`.`documentID` = 'FS'
                    AND `ap`.`employeeID` =  '{$userID}'
                    AND `approve`.`approvedYN` = ''
                    AND `fm`.`companyID` = '{$companyID}'
                    AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array('FU', $document_codes)){ /*** Fuel Usage ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                            `fuelusageID` as DocumentAutoID,
                            DocumentID,
                                `documentCode` as DocumentCode,
                                narration AS Narration,
                            `supplierName` as suppliercustomer,
                        `transactionCurrency` as currency,
                        `transactionAmount` as Amount,
                        
                        `approvalLevelID`  AS LEVEL,
                        companyID,
                        transactionCurrencyDecimalPlaces,
                            confirmedByName,
                            DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
                            documentApprovedID,
                            \"\" AS `payrollYear`,
                            \"\" AS `payrollMonth`,
                            '' AS bankGLAutoID, segmentcodedes
                        FROM
                            (
                        SELECT
                            documentApprovedID,
                        
                            `fleet_fuelusagemaster`.`approvedYN`,
                            `fleet_fuelusagemaster`.`companyID`,
                            `fleet_fuelusagemaster`.`confirmedByName`,
                            `fleet_fuelusagemaster`.`confirmedDate`,
                            fleet_fuelusagemaster.transactionCurrencyDecimalPlaces,
                            `approvalLevelID`,
                            narration,
                            `fleet_fuelusagemaster`.`documentID`,
                            `confirmedYN`,
                            `fleet_fuelusagemaster`.`fuelusageID`,
                            `fleet_fuelusagemaster`.`supplierAutoID`,
                            `fleet_fuelusagemaster`.`documentCode`,
                            `fleet_fuelusagemaster`.`documentDate`,
                            `referenceNumber`,
                            `transactionCurrency`,
                            FORMAT( IFNULL( fleet_fuelusagedetails.transactionAmount, 0 ), transactionCurrencyDecimalPlaces ) AS transactionAmount,
                            supplierName, IFNULL(segmentmaster.segmentCode,'-') as segmentcodedes
                        FROM
                            fleet_fuelusagemaster
                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = fleet_fuelusagemaster.supplierAutoID
                            LEFT JOIN ( SELECT sum( fleet_fuelusagedetails.totalAmount ) AS transactionAmount, fuelusageID FROM fleet_fuelusagedetails GROUP BY fuelusageID ) fleet_fuelusagedetails ON fleet_fuelusagemaster.fuelusageID = fleet_fuelusagedetails.fuelusageID
                            LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = fleet_fuelusagemaster.fuelusageID
                            AND approvalLevelID = currentLevelNo
                            LEFT JOIN srp_erp_approvalusers ON levelNo = fleet_fuelusagemaster.currentLevelNo
                            LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = fleet_fuelusagemaster.segmentID
                        WHERE
                            isDeleted != 1
                            AND srp_erp_documentapproved.documentID = 'FU'
                            AND srp_erp_approvalusers.documentID = 'FU'
                            AND employeeID = '{$userID}'
                            AND fleet_fuelusagemaster.approvedYN = 0
                            AND fleet_fuelusagemaster.companyID = '{$companyID}'
                            ) t";
        }

        if(in_array('GRV', $document_codes)){ /*** Goods Receipt Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `srp_erp_grvmaster`.`grvAutoID` AS `DocumentAutoID`,
                        `srp_erp_grvmaster`.`DocumentID` AS `DocumentID`,
                        `grvPrimaryCode` AS DocumentCode,
                        `grvNarration` AS Narration,
                        `srp_erp_suppliermaster`.`supplierName` AS `suppliercustomer`,
                        `transactionCurrency` AS currency,
                        ( IFNULL( det.receivedTotalAmount, 0 ) + IFNULL( addondet.total_amount, 0 ) ) AS Amount,
                        srp_erp_grvmaster.currentLevelNo AS LEVEL,
                        srp_erp_grvmaster.companyID AS `companyID`,
                        srp_erp_grvmaster.transactionCurrencyDecimalPlaces,
                        srp_erp_grvmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_grvmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_grvmaster`
                        LEFT JOIN ( SELECT SUM(receivedTotalAmount + IFNULL(taxAmount, 0)) AS receivedTotalAmount, grvAutoID FROM srp_erp_grvdetails GROUP BY grvAutoID ) det ON ( `det`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
                        LEFT JOIN ( SELECT SUM(total_amount + IFNULL(taxAmount, 0)) AS total_amount, grvAutoID FROM srp_erp_grv_addon GROUP BY grvAutoID ) addondet ON ( `addondet`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
                        LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_grvmaster`.`supplierID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_grvmaster`.`grvAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_grvmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_grvmaster`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_grvmaster.segmentID
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'GRV'
                        AND `srp_erp_approvalusers`.`documentID` = 'GRV'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_grvmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array('HCINV', $document_codes)){ /*** Customer Invoice - HIRA ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_customerinvoicemaster_temp.invoiceAutoID AS DocumentAutoID,
                    srp_erp_customerinvoicemaster_temp.documentID AS DocumentID,
                    invoiceCode AS DocumentCode,
                    invoiceNarration AS Narration,
                    srp_erp_customermaster.customerName AS suppliercustomer,
                    transactionCurrency AS currency,
                    (
                    (
                    ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * ( ( IFNULL( det.transactionAmount, 0 ) - ( IFNULL( det.detailtaxamount, 0 ) ) ) )
                    ) + IFNULL( det.transactionAmount, 0 )
                    ) AS Amount,
                    approvalLevelID AS LEVEL,
                    srp_erp_customerinvoicemaster_temp.companyID AS companyID,
                    srp_erp_customerinvoicemaster_temp.transactionCurrencyDecimalPlaces,
                    srp_erp_customerinvoicemaster_temp.confirmedByName,
                    DATE_FORMAT( srp_erp_customerinvoicemaster_temp.confirmedDate, \" % b % D % Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`,
                    '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes	
                FROM
                    `srp_erp_customerinvoicemaster_temp`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster_temp.invoiceAutoID )
                    LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster_temp.InvoiceAutoID )
                    LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster_temp`.`customerID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_customerinvoicemaster_temp`.`invoiceAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerinvoicemaster_temp`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerinvoicemaster_temp`.`currentLevelNo`
                    LEFT join srp_erp_segment segmentmaster on segmentmaster.segmentID  = srp_erp_customerinvoicemaster_temp.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'HCINV'
                    AND `srp_erp_approvalusers`.`documentID` = 'HCINV'
                AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_customerinvoicemaster_temp`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('IOU', $document_codes)){ /*** IOU Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    masterTbl.voucherAutoID AS DocumentAutoID,
                    masterTbl.documentID AS DocumentID,
                    masterTbl.iouCode AS DocumentCode,
                    masterTbl.narration AS Narration,
                    masterTbl.empName AS suppliercustomer,
                    transactionCurrency AS currency,
                    det.transactionAmount AS Amount,
                    approvalLevelID AS LEVEL,
                    masterTbl.companyID AS companyID,
                    transactionCurrencyDecimalPlaces,
                    masterTbl.confirmedByName,
                    DATE_FORMAT( masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`,
                    '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_iouvouchers` `masterTbl`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, voucherAutoID FROM srp_erp_iouvoucherdetails detailTbl GROUP BY voucherAutoID ) det ON ( `masterTbl`.`voucherAutoID` = det.voucherAutoID )
                    LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`voucherAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = masterTbl.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'IOU'
                    AND `srp_erp_approvalusers`.`documentID` = 'IOU'
                    AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND masterTbl.companyID = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('IOUE', $document_codes)){ /*** IOU Expenses ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        masterTbl.bookingMasterID AS DocumentAutoID,
                        masterTbl.documentID AS DocumentID,
                        masterTbl.bookingCode AS DocumentCode,
                        masterTbl.`comments` AS Narration,
                        masterTbl.empName AS suppliercustomer,
                        transactionCurrency AS currency,
                        det.transactionAmount AS Amount,
                        approvalLevelID AS LEVEL,
                        masterTbl.companyID AS companyID,
                        masterTbl.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                        masterTbl.confirmedByName,
                        DATE_FORMAT( masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_ioubookingmaster` `masterTbl`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, bookingMasterID FROM srp_erp_ioubookingdetails detailTbl GROUP BY bookingMasterID ) det ON ( `masterTbl`.`bookingMasterID` = det.bookingMasterID )
                        LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`bookingMasterID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = masterTbl.segmentID
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'IOUE'
                        AND `srp_erp_approvalusers`.`documentID` = 'IOUE'
                        AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                        AND `masterTbl`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('JP', $document_codes)){ /*** Journey Plan ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    masterTbl.journeyPlanMasterID AS DocumentAutoID,
                    masterTbl.documentID AS DocumentID,
                    masterTbl.documentCode AS DocumentCode,
                    CONCAT('Driver :',driver.driverName,' | ','Departure : ',depart.placeName,' | Destination',arrive.placeName) as Narration,
                    '-' as suppliercustomer,
                    '' as currency,
                    '' as Amount,
                    `approvalLevelID`  AS LEVEL,
                    masterTbl.companyID,
                    ' ' as transactionCurrencyDecimalPlaces,
                    masterTbl.confirmedByName,
                    DATE_FORMAT(masterTbl.confirmedDate, '%b %D %Y' ) AS date,
                    documentApprovedID,
                    '' AS `payrollYear`,
                    '' AS `payrollMonth`,
                    '' AS bankGLAutoID, '' AS segmentcodedes
                FROM
                    `srp_erp_journeyplan_master` `masterTbl`
                    LEFT JOIN ( SELECT MAX( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) rout ON `rout`.`journeyPlanMasterID` = `masterTbl`.`journeyPlanMasterID`
                    LEFT JOIN ( SELECT MIN( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin ON `routmin`.`journeyPlanMasterID` = `masterTbl`.`journeyPlanMasterID`
                    LEFT JOIN `fleet_drivermaster` `driver` ON `driver`.`driverMasID` = `masterTbl`.`driverID`
                    LEFT JOIN `fleet_vehiclemaster` `vehicalemaster` ON `vehicalemaster`.`vehicleMasterID` = `masterTbl`.`vehicleID`
                    LEFT JOIN `srp_erp_journeyplan_routedetails` `arrive` ON `arrive`.`JP_RouteDetailsID` = `rout`.`JP_RouteDetailsID`
                    LEFT JOIN `srp_erp_journeyplan_routedetails` `depart` ON `depart`.`JP_RouteDetailsID` = `routmin`.`JP_RouteDetailsID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`journeyPlanMasterID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'JP'
                    AND `srp_erp_approvalusers`.`documentID` = 'JP'
                    AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = ''";
        }

        if(in_array('JV', $document_codes)){ /*** Journal Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `srp_erp_jvmaster`.`JVMasterAutoId` AS DocumentAutoID,
                        `srp_erp_jvmaster`.`documentID` AS DocumentID,
                        `JVcode` AS DocumentCode,
                        `JVNarration` AS Narration,
                        \"-\" AS suppliercustomer,
                        `transactionCurrency` AS currency,
                        IFNULL( debamt.debitAmount, 0 ) AS Amount,
                        `approvalLevelID` AS LEVEL,
                        srp_erp_jvmaster.companyID AS companyID,
                        transactionCurrencyDecimalPlaces,
                        srp_erp_jvmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_jvmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM
                        `srp_erp_jvmaster`
                        LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId ) debamt ON ( `debamt`.`JVMasterAutoId` = srp_erp_jvmaster.JVMasterAutoId )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_jvmaster`.`JVMasterAutoId`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_jvmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_jvmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_jvmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`documentID` = 'JV'
                        AND `srp_erp_approvalusers`.`documentID` = 'JV'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('LA', $document_codes)){ /*** Leave ***/

            $query .= ($query != '')? ' UNION ': '';

            $leaveApprovalWithGroup = getPolicyValues('LAG', 'All');
            if($leaveApprovalWithGroup==1){
                $query_leave_group_policy='AND LM.leaveGroupID = LAS.leaveGroupID';
            }else{
                $query_leave_group_policy='';
            }

            $query_leave = "SELECT
                        LM.leaveMasterID AS DocumentAutoID,
                        'LA' AS DocumentID,
                        LM.documentCode AS DocumentCode,
                        LM.comments AS Narration,
                        CONCAT(empTB.ECode, ' - ', empTB.Ename1) AS suppliercustomer,
                        \"\" AS currency,
                        \"\" AS Amount,
                        LM.currentLevelNo AS LEVEL,
                        LM.companyID AS companyID,
                        \"\" AS decimalplaces,
                        LM.confirmedByName,
                        DATE_FORMAT(LM.confirmedDate, '%b %D %Y') AS date,
                        \"\" AS documentApprovedID,
                        \"\" AS payrollYear,
                        \"\" AS payrollMonth,
                        \"\" AS bankGLAutoID,
                        \"\" AS segmentcodedes 
                    FROM
                        `srp_erp_leavemaster` AS `LM`
                        JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = LM.empID
                        JOIN srp_erp_leaveapprovalsetup AS LAS ON LM.currentLevelNo = LAS.approvalLevel AND  LAS.companyID='{$companyID}' 
                        {$query_leave_group_policy}
                    WHERE
                        `LM`.`approvedYN` = '0' 
                        AND `LM`.`confirmedYN` = '1' 
                        AND `LM`.`companyID` = '{$companyID}' 
                        AND (
                            (LAS.approvalType = 1 AND LM.empID IN (
                                SELECT emp_manager.empID 
                                FROM srp_employeesdetails AS emp_detail
                                JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID 
                                WHERE emp_manager.active = 1 
                                AND emp_manager.companyID = '{$companyID}' 
                                AND emp_manager.managerID = '{$userID}'
                            ))
                            OR
                            (LAS.approvalType = 5 AND LM.empID IN (
                                SELECT emp_detail.EIdNo 
                                FROM srp_employeesdetails AS emp_detail
                                JOIN srp_empdepartments AS emp_dep ON emp_detail.EIdNo = emp_dep.EmpID
                                JOIN srp_departmentmaster AS srp_dep ON emp_dep.DepartmentMasterID = srp_dep.DepartmentMasterID 
                                WHERE emp_dep.isactive = 1 
                                AND emp_dep.Erp_companyID = '{$companyID}' 
                                AND srp_dep.hod_id = '{$userID}'
                            ))
                            OR
                            (LAS.approvalType = 2 AND LM.empID IN (
                                SELECT emp_detail.EIdNo 
                                FROM srp_employeesdetails AS emp_detail
                                JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID
                                JOIN srp_erp_employeemanagers AS top_manager ON top_manager.empID = emp_manager.managerID
                                AND emp_manager.active=1 AND top_manager.active=1
                                WHERE emp_manager.active = 1 
                                AND emp_manager.companyID = '{$companyID}' 
                                AND top_manager.managerID = '{$userID}'
                            ))
                            OR
                            (LAS.approvalType = 4 AND {$userID} IN (
                                                SELECT
                                                    emp_detail.EIdNo 
                                                FROM
                                                    srp_employeesdetails AS emp_detail
                                                    JOIN srp_erp_leave_covering_employee AS coveringemployee ON emp_detail.EIdNo = coveringemployee.coveringID 
                                                WHERE
                                                    emp_detail.Erp_companyID = '{$companyID}' 
                                                    AND coveringemployee.leaveapplicationID IN (
                                                        SELECT
                                                            LM.leaveMasterID AS DocumentAutoID 
                                                        FROM
                                                            `srp_erp_leavemaster` AS `LM`
                                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = LM.empID
                                                            JOIN srp_erp_leaveapprovalsetup AS LAS ON LM.currentLevelNo = LAS.approvalLevel  AND LAS.companyID='{$companyID}'
                                                            {$query_leave_group_policy} 
                                                        WHERE
                                                            `LM`.`approvedYN` = '0' 
                                                            AND `LM`.`companyID` = '{$companyID}' 
                                                    ) 
                                            ))
                            OR
                            (LAS.approvalType = 3 AND {$userID} IN (
                                SELECT emp_detail.EIdNo 
                                FROM srp_employeesdetails AS emp_detail
                                JOIN srp_erp_leaveapprovalsetuphremployees AS hremployee ON emp_detail.EIdNo = hremployee.empID 
                                WHERE hremployee.companyID = '{$companyID}' AND hremployee.empID = '{$userID}'
                            ))
                        )";

            $query .= $query_leave;

        }

        if(in_array( 'LEC', $document_codes)) {/*** Leave Encashment ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID, masterTbl.documentCode AS DocumentCode,
                        narration as Narration, \"-\" as suppliercustomer, cur_mas.CurrencyCode as currency, 0 as Amount, approvalLevelID AS LEVEL,
                        masterTbl.companyID, trDPlace as transactionCurrencyDecimalPlaces, masterTbl.confirmedByName,
                        DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) AS date, documentApprovedID, \"\" AS `payrollYear`, 
                        masterTbl.document_type AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                        FROM srp_erp_pay_leaveencashment masterTbl	 
                        JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
                        JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
                        AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                        JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
                        WHERE srp_erp_documentapproved.documentID = 'LEC' AND srp_erp_approvalusers.documentID = 'LEC'
                        AND srp_erp_documentapproved.companyID ='{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'
                        AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array( 'LO', $document_codes)) {/*** Loan ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `e_loan`.`ID` AS `DocumentAutoID`,
                        `e_loan`.`documentID` AS `DocumentID`,
                            `loanCode` as DocumentCode,
                            IFNULL(loanDescription,'-') as Narration,
                    
                        CONCAT( IFNULL( Ename2, '' ) ) AS suppliercustomer,
                        \" \"  AS currency,
                        \" \"  AS Amount,
                        `approvalLevelID` AS LEVEL,
                        e_loan.companyID AS companyID,
                    
                        \" \" AS transactionCurrencyDecimalPlaces,
                        e_loan.confirmedByName,
                        DATE_FORMAT( e_loan.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`,
                        '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_pay_emploan` AS `e_loan`
                        JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `e_loan`.`ID`
                        AND `approve`.`approvalLevelID` = `e_loan`.`currentLevelNo`
                        JOIN `srp_employeesdetails` AS `emp` ON `emp`.`EIdNo` = `e_loan`.`empID`
                        JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `e_loan`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = e_loan.segmentID
                    WHERE
                        `approve`.`documentID` = 'LO'
                        AND `ap`.`documentID` = 'LO'
                        AND `ap`.`employeeID` =  '{$userID}'
                        AND `approve`.`approvedYN` = ''
                        AND `e_loan`.`companyID` = '{$companyID}'
                        AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'MI', $document_codes)) {/*** Material Issue ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                srp_erp_itemissuemaster.itemIssueAutoID AS DocumentAutoID,
                srp_erp_itemissuemaster.documentID AS DocumentID,
                itemIssueCode AS DocumentCode,
                srp_erp_itemissuemaster.`comment` AS Narration,
                IFNULL( srp_erp_itemissuemaster.employeeName, '-' ) AS suppliercustomer,
                companyLocalCurrency AS currency,
                det.totalValue AS Amount,
                currentLevelNo AS LEVEL,
                srp_erp_itemissuemaster.companyID AS companyID,
                companyLocalCurrencyDecimalPlaces AS decimalplaces,
                srp_erp_itemissuemaster.confirmedByName AS confirmname,
                DATE_FORMAT( srp_erp_itemissuemaster.confirmedDate, \"%b %D %Y\" ) AS date,
                documentApprovedID,
                \"\" AS `payrollYear`,
                \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
            FROM
                `srp_erp_itemissuemaster`
                LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID ) det ON ( `det`.`itemIssueAutoID` = srp_erp_itemissuemaster.itemIssueAutoID )
                JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_itemissuemaster`.`itemIssueAutoID`
                AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_itemissuemaster`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_itemissuemaster`.`currentLevelNo`
                LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_itemissuemaster.segmentID
            WHERE
                `srp_erp_documentapproved`.`documentID` = 'MI'
                AND `srp_erp_approvalusers`.`documentID` = 'MI'
                AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                AND `srp_erp_itemissuemaster`.`companyID` = '{$companyID}'
                AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'MR', $document_codes)) {/*** Material Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_materialrequest.mrAutoID AS DocumentAutoID,
                        srp_erp_materialrequest.DocumentID AS DocumentID,
                        MRCode AS DocumentCode,
                        IFNULL( `comment`, '-' ) AS Narration,
                        srp_erp_materialrequest.employeeName AS suppliercustomer,
                        \" \" AS currency,
                        \" \" AS Amount,
                        approvalLevelID AS LEVEL,
                        srp_erp_materialrequest.companyID AS companyID,
                        \" \" AS decimalplaces,
                        srp_erp_materialrequest.confirmedByName,
                        DATE_FORMAT( srp_erp_materialrequest.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_materialrequest`
                        LEFT JOIN ( SELECT SUM( qtyRequested ) AS qtyRequested, mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID ) det ON ( `det`.`mrAutoID` = srp_erp_materialrequest.mrAutoID )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_materialrequest`.`mrAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialrequest`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialrequest`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_materialrequest.segmentID
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'MR'
                        AND `srp_erp_approvalusers`.`documentID` = 'MR'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_materialrequest`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'MRN', $document_codes)) {/*** Material Receipt Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_materialreceiptmaster.mrnAutoID AS DocumentAutoID,
                    srp_erp_materialreceiptmaster.documentID AS DocumentID,
                    mrnCode AS DocumentCode,
                    IFNULL( `comment`, '-' ) AS Narration,
                    srp_erp_materialreceiptmaster.employeeName AS suppliercustomer,
                    \" \" AS currency,
                    \" \" AS Amount,
                    approvalLevelID AS LEVEL,
                    srp_erp_materialreceiptmaster.companyID AS companyID,
                    \" \" AS decimalplaces,
                    srp_erp_materialreceiptmaster.confirmedByName,
                    DATE_FORMAT( srp_erp_materialreceiptmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_materialreceiptmaster`
                    LEFT JOIN ( SELECT SUM( qtyReceived ) AS qtyReceived, mrnAutoID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID ) det ON ( `det`.`mrnAutoID` = srp_erp_materialreceiptmaster.mrnAutoID )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_materialreceiptmaster`.`mrnAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_materialreceiptmaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'MRN'
                    AND `srp_erp_approvalusers`.`documentID` = 'MRN'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_materialreceiptmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'PO', $document_codes)) {/*** Purchase Order ***/
            $query .= ($query != '')? ' UNION ': '';
          
            switch ($amountBasedApproval) {
                case 1:
                    $query .= "SELECT
                    srp_erp_purchaseordermaster.purchaseOrderID AS DocumentAutoID,
                    srp_erp_purchaseordermaster.DocumentID AS DocumentID,
                    purchaseOrderCode AS DocumentCode,
                    narration AS Narration,
                    srp_erp_suppliermaster.supplierName AS suppliercustomer,
                    srp_erp_purchaseordermaster.transactionCurrency AS currency,
                    ( det.transactionAmount - generalDiscountAmount ) AS Amount,
                    currentLevelNo AS LEVEL,
                    srp_erp_purchaseordermaster.companyID AS companyID,
                    srp_erp_purchaseordermaster.transactionCurrencyDecimalPlaces,
                    srp_erp_purchaseordermaster.confirmedByName,
                    DATE_FORMAT( srp_erp_purchaseordermaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_purchaseordermaster`
                    LEFT JOIN ( SELECT SUM(totalAmount + IFNULL(taxAmount, 0)) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON ( `det`.`purchaseOrderID` = srp_erp_purchaseordermaster.purchaseOrderID )
                    LEFT JOIN ( SELECT 
	                            srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, 
                                srp_erp_purchaseordermaster.companyLocalExchangeRate,
                                transactionCurrencyID, 
                                transactionCurrency,
                                (( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) ) /  companyLocalExchangeRate AS total_value 
                                FROM
	                            srp_erp_purchaseordermaster
	                                LEFT JOIN ( SELECT 
                                                SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, 
                                                purchaseOrderID 
                                                FROM 
                                                srp_erp_purchaseorderdetails 
                                                GROUP BY 
                                                purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
	                                LEFT JOIN ( SELECT
		                                        ifnull( SUM( amount ), 0 ) AS gentaxamount,
		                                        documentMasterAutoID 
	                                            FROM
		                                        srp_erp_taxledger 
	                                            WHERE
		                                        documentID = 'PO' 
		                                        AND documentDetailAutoID IS NULL 
		                                        AND companyID = $companyID
	                                            GROUP BY
		                                        documentMasterAutoID) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
	                                            )  approvalBased ON  approvalBased.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                    LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_purchaseordermaster`.`supplierID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_purchaseordermaster`.`purchaseOrderID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_purchaseordermaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'PO'
                    AND `srp_erp_approvalusers`.`documentID` = 'PO'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND srp_erp_approvalusers.fromAmount  <=approvalBased.total_value AND srp_erp_approvalusers.toAmount >= approvalBased.total_value
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_purchaseordermaster`.`companyID` = '{$companyID}'";
                break;
                default:
                $query .= "SELECT
                srp_erp_purchaseordermaster.purchaseOrderID AS DocumentAutoID,
                srp_erp_purchaseordermaster.DocumentID AS DocumentID,
                purchaseOrderCode AS DocumentCode,
                narration AS Narration,
                srp_erp_suppliermaster.supplierName AS suppliercustomer,
                transactionCurrency AS currency,
                ( det.transactionAmount - generalDiscountAmount ) AS Amount,
                currentLevelNo AS LEVEL,
                srp_erp_purchaseordermaster.companyID AS companyID,
                srp_erp_purchaseordermaster.transactionCurrencyDecimalPlaces,
                srp_erp_purchaseordermaster.confirmedByName,
                DATE_FORMAT( srp_erp_purchaseordermaster.confirmedDate, \"%b %D %Y\" ) AS date,
                documentApprovedID,
                \"\" AS `payrollYear`,
                \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
            FROM
                `srp_erp_purchaseordermaster`
                LEFT JOIN ( SELECT SUM(totalAmount + IFNULL(taxAmount, 0)) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON ( `det`.`purchaseOrderID` = srp_erp_purchaseordermaster.purchaseOrderID )
                LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_purchaseordermaster`.`supplierID`
                JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_purchaseordermaster`.`purchaseOrderID`
                AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_purchaseordermaster.segmentID
            WHERE
                `srp_erp_documentapproved`.`documentID` = 'PO'
                AND `srp_erp_approvalusers`.`documentID` = 'PO'
                AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                AND `srp_erp_documentapproved`.`approvedYN` = '0'
                AND `srp_erp_purchaseordermaster`.`companyID` = '{$companyID}'";
            }

            
        }

        if(in_array( 'PRQ', $document_codes)) {/*** Purchase Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_purchaserequestmaster.purchaseRequestID AS DocumentAutoID,
                        srp_erp_purchaserequestmaster.documentID AS DocumentID,
                        purchaseRequestCode AS DocumentCode,
                        narration AS Narration,
                        \"-\" AS suppliercustomer,
                        transactionCurrency AS currency,
                        det.transactionAmount AS Amount,
                        approvalLevelID AS LEVEL,
                        srp_erp_purchaserequestmaster.companyID AS companyID,
                        srp_erp_purchaserequestmaster.transactionCurrencyDecimalPlaces,
                        srp_erp_purchaserequestmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_purchaserequestmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        srp_erp_documentapproved.documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_purchaserequestmaster`
                        LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID ) det ON ( `det`.`purchaseRequestID` = srp_erp_purchaserequestmaster.purchaseRequestID )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_purchaserequestmaster`.`purchaseRequestID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID =srp_erp_purchaserequestmaster.segmentID 
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'PRQ'
                        AND `srp_erp_approvalusers`.`documentID` = 'PRQ'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND (
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        OR (
                        `srp_erp_approvalusers`.`employeeID` = - 1
                        AND srp_erp_purchaserequestmaster.requestedEmpID IN (
                    SELECT
                        empmanagers.empID
                    FROM
                        srp_employeesdetails empdetail
                        JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
                        AND empmanagers.active = 1
                    WHERE
                        empmanagers.companyID = '{$companyID}'
                        AND empmanagers.managerID = '{$userID}'
                        )
                        )
                        )
                        AND `srp_erp_purchaserequestmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";

                    
        }

        if(in_array( 'PV', $document_codes)) {/*** Payment Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_paymentvouchermaster.payVoucherAutoId AS DocumentAutoID,
                    srp_erp_paymentvouchermaster.documentID AS DocumentID,
                    PVcode AS DocumentCode,
                    IFNULL( PVNarration, '-' ) AS Narration,
                CASE
                    pvType
                    WHEN 'Direct' THEN
                    partyName
                    WHEN 'Employee' THEN
                    srp_employeesdetails.Ename2
                    WHEN 'Supplier' THEN
                    srp_erp_suppliermaster.supplierName
                    END AS suppliercustomer,
                    transactionCurrency AS currency,
                    (
                    ( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( debitnote.transactionAmount, 0 ) - IFNULL( SR.transactionAmount, 0 )
                    ) AS Amount,
                    approvalLevelID AS LEVEL,
                    srp_erp_paymentvouchermaster.companyID AS companyID,
                    transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces,
                    srp_erp_paymentvouchermaster.confirmedByName,
                    DATE_FORMAT( srp_erp_paymentvouchermaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_paymentvouchermaster`
                    LEFT JOIN (
                SELECT
                    SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                    payVoucherAutoId
                FROM
                    srp_erp_paymentvoucherdetail
                WHERE
                    srp_erp_paymentvoucherdetail.type != \"debitnote\"
                    AND srp_erp_paymentvoucherdetail.type != \"SR\"
                GROUP BY
                    payVoucherAutoId
                    ) det ON ( `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN (
                SELECT
                    SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount,
                    payVoucherAutoId
                FROM
                    srp_erp_paymentvoucherdetail
                WHERE
                    srp_erp_paymentvoucherdetail.type = \"GL\"
                    OR srp_erp_paymentvoucherdetail.type = \"Item\"
                GROUP BY
                    payVoucherAutoId
                    ) tyepdet ON ( `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"debitnote\" GROUP BY payVoucherAutoId ) debitnote ON ( `debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"SR\" GROUP BY payVoucherAutoId ) SR ON ( `SR`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM(transactionAmount) AS transactionAmount, SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON ( `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_paymentvouchermaster`.`partyID`
                    LEFT JOIN `srp_employeesdetails` ON `srp_employeesdetails`.`EIdNo` = `srp_erp_paymentvouchermaster`.`partyID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_paymentvouchermaster`.`PayVoucherAutoId`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_paymentvouchermaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'PV'
                    AND `srp_erp_approvalusers`.`documentID` = 'PV'
                    /*AND `pvType` <> 'SC'*/
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_paymentvouchermaster`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'RJV', $document_codes)) {/*** Recurring Journal Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `srp_erp_recurringjvmaster`.`RJVMasterAutoId` AS `DocumentAutoID`,
                    `srp_erp_recurringjvmaster`.`documentID` AS `DocumentID`,
                    `RJVcode` AS `DocumentCode`,
                    `RJVNarration` AS Narration,
                    \"-\" AS suppliercustomer,
                    `transactionCurrency` AS currency,
                    IFNULL( debamt.debitAmount, 0 ) AS Amount,
                    `approvalLevelID` AS LEVEL,
                    srp_erp_recurringjvmaster.companyID AS companyID,
                    transactionCurrencyDecimalPlaces,
                    srp_erp_recurringjvmaster.confirmedByName,
                    DATE_FORMAT( srp_erp_recurringjvmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                FROM
                    `srp_erp_recurringjvmaster`
                    LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY RJVMasterAutoId ) debamt ON ( `debamt`.`RJVMasterAutoId` = srp_erp_recurringjvmaster.RJVMasterAutoId )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_recurringjvmaster`.`RJVMasterAutoId`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_recurringjvmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_recurringjvmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_recurringjvmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_documentapproved`.`documentID` = 'RJV'
                    AND `srp_erp_approvalusers`.`documentID` = 'RJV'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'RV', $document_codes)) {/*** Receipt Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_customerreceiptmaster.receiptVoucherAutoId AS DocumentAutoID,
                        srp_erp_customerreceiptmaster.documentID AS DocumentID,
                        RVcode AS DocumentCode,
                        RVNarration AS Narration,
                    IF
                        ( customerID IS NULL OR customerID = 0, srp_erp_customerreceiptmaster.customerName, srp_erp_customermaster.customerName ) AS suppliercustome,
                        transactionCurrency AS currency,
                        (
                        ( ( IFNULL( addondet.taxPercentage, 0 ) / 100 ) * IFNULL( tyepdet.transactionAmount, 0 ) ) + IFNULL( det.transactionAmount, 0 ) - IFNULL( Creditnots.transactionAmount, 0 )
                        ) AS Amount,
                        approvalLevelID AS LEVEL,
                        srp_erp_customerreceiptmaster.companyID AS companyID,
                        transactionCurrencyDecimalPlaces,
                        srp_erp_customerreceiptmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_customerreceiptmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                    FROM
                        `srp_erp_customerreceiptmaster`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type != \"creditnote\" GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerreceiptmaster`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = \"creditnote\" GROUP BY receiptVoucherAutoId ) Creditnots ON ( `Creditnots`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                        LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
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
                        ) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_customerreceiptmaster.segmentID
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'RV'
                        AND `srp_erp_approvalusers`.`documentID` = 'RV'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_customerreceiptmaster`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SA', $document_codes)) {/*** Stock Adjustment ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    stockAdjustmentAutoID AS DocumentAutoID,
                    srp_erp_stockadjustmentmaster.documentID AS DocumentID,
                    stockAdjustmentCode AS DocumentCode,
                    IFNULL( srp_erp_stockadjustmentmaster.`comment`, '-' ) AS Narration,
                    \"-\" AS suppliercustomer,
                    \" \" AS currency,
                    \" \" AS Amount,
                    approvalLevelID AS LEVEL,
                    srp_erp_stockadjustmentmaster.companyID AS companyID,
                    \" \" AS transactionCurrencyDecimalPlaces,
                    srp_erp_stockadjustmentmaster.confirmedByName,
                    DATE_FORMAT( srp_erp_stockadjustmentmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes 
                FROM
                    `srp_erp_stockadjustmentmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockadjustmentmaster`.`stockAdjustmentAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_stockadjustmentmaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SA'
                    AND `srp_erp_approvalusers`.`documentID` = 'SA'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stockadjustmentmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SAR', $document_codes)) {/*** Salary Advance Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID, masterTbl.documentCode AS DocumentCode,
                    narration as Narration, \"-\" as suppliercustomer, cur_mas.CurrencyCode as currency, request_amount as Amount, approvalLevelID AS LEVEL,
                    masterTbl.companyID, trDPlace as transactionCurrencyDecimalPlaces, masterTbl.confirmedByName,
                    DATE_FORMAT(masterTbl.confirmedDate, \"%b %D %Y\" ) AS date, documentApprovedID,
                    \"\" AS `payrollYear`, \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM srp_erp_pay_salaryadvancerequest masterTbl	 
                    JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
                    JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
                    AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                    JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
                    WHERE srp_erp_documentapproved.documentID = 'SAR' AND srp_erp_approvalusers.documentID = 'SAR'
                    AND srp_erp_documentapproved.companyID ='{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'
                    AND (
                        srp_erp_approvalusers.employeeID = '{$userID}'
                        OR (
                            srp_erp_approvalusers.employeeID = - 1
                            AND masterTbl.empID IN (
                                SELECT empmanagers.empID
                                FROM srp_employeesdetails empdetail
                                JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
                                AND empmanagers.active = 1 WHERE empmanagers.companyID = '{$companyID}' AND empmanagers.managerID = '{$userID}'
                           )
                        )
                        OR (
                            srp_erp_approvalusers.employeeID = - 2 
                            AND masterTbl.empID IN (
                            SELECT
                            emp_detail.EIdNo 
                            FROM
                            srp_employeesdetails AS emp_detail
                            JOIN srp_empdepartments AS emp_dep ON emp_detail.EIdNo = emp_dep.EmpID
                            JOIN srp_departmentmaster AS srp_dep ON emp_dep.DepartmentMasterID = srp_dep.DepartmentMasterID 
                            AND `emp_dep`.`isactive` = 1 
                            AND `emp_dep`.Erp_companyID = '{$companyID}' 
                            AND srp_dep.hod_id = '{$userID}' 
                            ) 
                        )
                        OR (
                            srp_erp_approvalusers.employeeID = - 3 
                            AND masterTbl.empID IN (
                            SELECT
                            emp_detail.Eidno 
                            FROM
                            srp_employeesdetails AS emp_detail
                            JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID
                            JOIN ( SELECT * FROM srp_erp_employeemanagers ) AS top_manager ON top_manager.empID = emp_manager.managerID 
                            WHERE
                            emp_manager.active = 1 
                            AND `emp_manager`.`companyID` = '{$companyID}' 
                            AND top_manager.managerID = '{$userID}' 
                            ) 
                        )
                    )	
                    AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array( 'SC', $document_codes)) {/*** Sales Commision ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `srp_erp_salescommisionmaster`.`salesCommisionID` AS `DocumentAutoID`,
                    `srp_erp_salescommisionmaster`.`DocumentID` AS `DocumentID`,
                    `salesCommisionCode` AS DocumentCode,
                    `Description` AS Narration,
                    \"-\" AS suppliercustomer,
                    `transactionCurrency` AS currency,
                    `det2`.`transactionAmount` AS `Amount`,
                    srp_erp_salescommisionmaster.currentLevelNo AS LEVEL,
                    srp_erp_salescommisionmaster.companyID AS companyID,
                    srp_erp_salescommisionmaster.transactionCurrencyDecimalPlaces,
                    srp_erp_salescommisionmaster.confirmedByName,
                    DATE_FORMAT( srp_erp_salescommisionmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                FROM
                    `srp_erp_salescommisionmaster`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisiondetail GROUP BY salesCommisionID ) det ON ( `det`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
                    LEFT JOIN ( SELECT SUM( netCommision ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisionperson GROUP BY salesCommisionID ) det2 ON ( `det2`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_salescommisionmaster`.`salesCommisionID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salescommisionmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_salescommisionmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SC'
                    AND `srp_erp_approvalusers`.`documentID` = 'SC'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_salescommisionmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SCNT', $document_codes)) {/*** Stock Counting ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    stockCountingAutoID AS DocumentAutoID,
                    srp_erp_stockcountingmaster.documentID AS DocumentID,
                    stockCountingCode AS DocumentCode,
                    IFNULL( srp_erp_stockcountingmaster.`comment`, '-' ) AS Narration,
                    \"-\" AS suppliercustomer,
                    \" \" AS currency,
                    \" \" AS Amount,
                    approvalLevelID AS LEVEL,
                    srp_erp_stockcountingmaster.companyID AS companyID,
                    \" \" AS decimalplaces,
                    srp_erp_stockcountingmaster.confirmedByName AS confirmname,
                    DATE_FORMAT( srp_erp_stockcountingmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_stockcountingmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockcountingmaster`.`stockCountingAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockcountingmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockcountingmaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID = srp_erp_stockcountingmaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SCNT'
                    AND `srp_erp_approvalusers`.`documentID` = 'SCNT'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stockcountingmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SD', $document_codes)) {/*** Salary Declaration ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        salarydeclarationMasterID AS DocumentAutoID,
                        srp_erp_salarydeclarationmaster.documentID AS DocumentID,
                        srp_erp_salarydeclarationmaster.documentSystemCode AS DocumentCode,
                            CONCAT(\"Date : \",DATE_FORMAT(srp_erp_salarydeclarationmaster.documentDate, '%d-%m-%Y'),' | Currency : ',transactionCurrency, \" | \" ,Description) AS Narration,
                    
                            \"-\" AS suppliercustomer,
                        \" \"  AS currency,
                        \" \"  AS Amount,
                        approvalLevelID AS LEVEL,
                    
                        srp_erp_salarydeclarationmaster.companyID AS companyID,
                        srp_erp_salarydeclarationmaster.transactionCurrencyDecimalPlaces,
                        srp_erp_salarydeclarationmaster.confirmedByName,
                        DATE_FORMAT( srp_erp_salarydeclarationmaster.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM
                        `srp_erp_salarydeclarationmaster`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_salarydeclarationmaster`.`salarydeclarationMasterID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salarydeclarationmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_salarydeclarationmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'SD'
                        AND `srp_erp_approvalusers`.`documentID` = 'SD'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_salarydeclarationmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` ='{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = ''";
        }

        if(in_array( 'SLR', $document_codes)) {/*** Sales Return ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        masterTbl.salesReturnAutoID AS DocumentAutoID,
                        masterTbl.documentID AS DocumentID,
                        salesReturnCode AS DocumentCode,
                        `comment` AS Narration,
                        srp_erp_customermaster.customerName AS suppliercustomer,
                        `transactionCurrency` AS currency,
                        det.totalValue AS Amount,
                        currentLevelNo AS LEVEL,
                        masterTbl.companyID AS companyID,
                        masterTbl.transactionCurrencyDecimalPlaces,
                        masterTbl.confirmedByName,
                        DATE_FORMAT( masterTbl.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                    FROM
                        `srp_erp_salesreturnmaster` `masterTbl`
                        LEFT JOIN ( SELECT SUM(totalValue + IFNULL(taxAmount, 0) - IFNULL(rebateAmount, 0)) AS totalValue, salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY salesReturnAutoID ) det ON ( `det`.`salesReturnAutoID` = masterTbl.salesReturnAutoID )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `masterTbl`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`salesReturnAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'SLR'
                        AND `srp_erp_approvalusers`.`documentID` = 'SLR'
                        AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'SP', $document_codes)) {/*** Salary Process ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `payrollMasterID` AS DocumentAutoID,
                    t2.documentID AS DocumentID,
                    `t2`.`documentCode` AS `DocumentCode`,
                    IFNULL( `narration`, '-' ) AS Narration,
                    \"-\" AS suppliercustomer,
                    \" \" AS currency,
                    \"\" AS Amount,
                    `approvalLevelID` AS LEVEL,
                    `t2`.`companyID` AS companyID,
                    \" \" AS transactionCurrencyDecimalPlaces,
                    t2.confirmedByName,
                    DATE_FORMAT( t2.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    `payrollYear`,
                    `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                FROM
                    `srp_erp_payrollmaster` AS `t2`
                    JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
                    AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
                WHERE
                    `approve`.`documentID` = 'SP'
                    AND `ap`.`documentID` = 'SP'
                    AND `ap`.`employeeID` = '{$userID}'
                    AND `approve`.`approvedYN` = '0'
                    AND `t2`.`companyID` = '{$companyID}'
                    AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SPN', $document_codes)) {/*** Non Salary Process ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                `payrollMasterID` AS DocumentAutoID,
                t2.documentID AS DocumentID,
                `t2`.`documentCode` AS `DocumentCode`,
                `narration` AS Narration,
                \" \" AS suppliercustomer,
                \" \" AS currency,
                \" \" AS Amount,
                approvalLevelID AS LEVEL,
                t2.companyID AS companyID,
                \" \" AS transactionCurrencyDecimalPlaces,
                t2.confirmedByName,
                DATE_FORMAT( t2.confirmedDate, \"%b %D %Y\" ) AS date,
                documentApprovedID,
                \"\" AS `payrollYear`,
                \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
            FROM
                `srp_erp_non_payrollmaster` AS `t2`
                JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
                AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
            WHERE
                `approve`.`documentID` = 'SPN'
                AND `ap`.`documentID` = 'SPN'
                AND `ap`.`employeeID` = '{$userID}'
                AND `approve`.`approvedYN` = '0'
                AND `ap`.`companyID` = '{$companyID}'
                AND `t2`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SR', $document_codes)) {/*** Purchase Return ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                stockReturnAutoID AS DocumentAutoID,
                srp_erp_stockreturnmaster.documentID AS DocumentID,
                stockReturnCode AS DocumentCode,
                IFNULL( srp_erp_stockreturnmaster.`comment`, '-' ) AS Narration,
                \"-\" AS suppliercustomer,
                \" \" AS currency,
                \" \" AS Amount,
                currentLevelNo AS LEVEL,
                srp_erp_stockreturnmaster.companyID AS companyID,
                srp_erp_stockreturnmaster.transactionCurrencyDecimalPlaces AS decimalplaces,
                confirmedByName,
                DATE_FORMAT( confirmedDate, \"%b %D %Y\" ) AS date,
                documentApprovedID,
                \"\" AS `payrollYear`,
                \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
            FROM
                `srp_erp_stockreturnmaster`
                JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockreturnmaster`.`stockReturnAutoID`
                AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockreturnmaster`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockreturnmaster`.`currentLevelNo`
            WHERE
                `srp_erp_documentapproved`.`documentID` = 'SR'
                AND `srp_erp_approvalusers`.`documentID` = 'SR'
                AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                AND `srp_erp_documentapproved`.`approvedYN` = '0'
                AND `srp_erp_stockreturnmaster`.`companyID` = '{$companyID}'
                AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'ST', $document_codes)) {/*** Stock Transfer ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                    stockTransferAutoID AS DocumentAutoID,
                    srp_erp_stocktransfermaster.documentID AS DocumentID,
                    stockTransferCode AS DocumentCode,
                    `comment` AS Narration,
                    \"-\" AS suppliercustomer,
                    \" \" AS currency,
                    \" \" AS Amount,
                    srp_erp_stocktransfermaster.currentLevelNo AS LEVEL,
                    srp_erp_stocktransfermaster.companyID AS companyID,
                    \" \" AS transactionCurrencyDecimalPlaces,
                    srp_erp_stocktransfermaster.confirmedByName,
                    DATE_FORMAT( srp_erp_stocktransfermaster.confirmedDate, \"%b %D %Y\" ) AS date,
                    documentApprovedID,
                    \"\" AS `payrollYear`,
                    \"\" AS `payrollMonth`, '' AS bankGLAutoID, IFNULL(segmentmaster.segmentCode,'-') AS segmentcodedes
                FROM
                    `srp_erp_stocktransfermaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stocktransfermaster`.`stockTransferAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stocktransfermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stocktransfermaster`.`currentLevelNo`
                    LEFT JOIN srp_erp_segment segmentmaster on	segmentmaster.segmentID = srp_erp_stocktransfermaster.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'ST'
                    AND `srp_erp_approvalusers`.`documentID` = 'ST'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stocktransfermaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'VD', $document_codes)) {/*** Variable Declaration ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                        vpMasterID as DocumentAutoID,
                        decMas.documentID as DocumentID,
                        decMas.documentCode AS DocumentCode,
                        CONCAT(\"Currency : \",crMas.CurrencyCode,\" | \",description)  AS Narration,
                        \"-\" as suppliercustomer,
                        \" \" AS currency,
                        \" \" AS Amount,
                        approvalLevelID AS LEVEL,
                        decMas.companyID AS companyID,
                        \" \" AS transactionCurrencyDecimalPlaces,
                        emp.Ename2 as confirmedByName,
                        DATE_FORMAT( decMas.confirmedDate, \"%b %D %Y\" ) AS date,
                        documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes
                        FROM
                        `srp_erp_variablepaydeclarationmaster` AS `decMas`
                        JOIN `srp_erp_documentapproved` AS `appTB` ON `appTB`.`documentSystemCode` = `decMas`.`vpMasterID`
                        AND `appTB`.`approvalLevelID` = `decMas`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `decMas`.`currentLevelNo`
                        JOIN `srp_erp_currencymaster` `crMas` ON `decMas`.`trCurrencyID` = `crMas`.`currencyID`
                        LEFT JOIN srp_employeesdetails emp on emp.EIdNo  = decMas.confirmedByEmpID
                    WHERE
                        `appTB`.`documentID` = 'VD'
                        AND `srp_erp_approvalusers`.`documentID` = 'VD'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `decMas`.`companyID` =  '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` =  '{$companyID}'
                        AND `appTB`.`approvedYN` = ''";
        }

        if(in_array('ASP',$document_codes)){ /*** Supplier automated payments ***/

            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        vpayments.id AS DocumentAutoID,
                        ap.documentID AS DocumentID,
                        vpayments.doc_id AS DocumentCode,
                        vpayments.narration AS Narration,
                        \"-\" AS suppliercustomer,
                            \" \" AS currency,
                            \" \" AS Amount,
                            approve.approvalLevelID AS LEVEL,
                        vpayments.companyID AS companyID,
                        \" \" AS transactionCurrencyDecimalPlaces,
                        \" \" AS  confirmedByName,
                        DATE_FORMAT( vpayments.date, \"%b %D %Y\" ) AS DATE,
                        approve.documentApprovedID,
                        \"\" AS `payrollYear`,
                        \"\" AS `payrollMonth`, '' AS bankGLAutoID, '' AS segmentcodedes

                FROM `srp_erp_ap_vendor_payments_master` AS `vpayments`
                        JOIN `srp_erp_documentapproved` AS `approve`
                            ON `approve`.`documentSystemCode` = `vpayments`.`id`
                            AND `approve`.`approvalLevelID` = `vpayments`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` AS `ap`
                            ON `ap`.`levelNo` = `vpayments`.`currentLevelNo`
                WHERE
                    `approve`.`documentID` = 'ASP'
                    AND `ap`.`documentID` = 'ASP'
                    AND `ap`.`employeeID` = '{$userID}'
                    AND `approve`.`approvedYN` = '0'
                    AND `vpayments`.`companyID` = '{$companyID}'
                    AND `ap`.`companyID` = '{$companyID}'";
        }

        $this->datatables->select('t1.DocumentAutoID as DocumentAutoID, t1.DocumentID as DocumentID, t1.DocumentCode as DocumentCode,
	           t1.Narration as Narration, t1.segmentcodedes as segmentcodedes,
	           t1.suppliercustomer as suppliercustomer, t1.currency as currency, 
	           t1.Amount as Amount, t1.Level as Level, t1.companyID as companyID, t1.transactionCurrencyDecimalPlaces as decimalplaces, 
	           t1.confirmedByName as confirmname, t1.date as confirmdate, t1.documentApprovedID AS documentApprovedID, t1.payrollYear AS payrollYear,
		       t1.payrollMonth AS payrollMonth,t1.bankGLAutoID as bankGLAutoID');
        $this->datatables->from("({$query}) AS t1");

        if(!empty($filter_doc)){
            $this->datatables->where_in('t1.DocumentID', $filter_doc);
        }
        $this->datatables->group_by('t1.DocumentAutoID');
        $this->datatables->group_by('t1.documentID');

        $this->datatables->edit_column('total_value', '$1', 'document_approval_total_value(Amount,decimalplaces,currency)');
        $this->datatables->edit_column('docid', ' <center><div class="person-circle align-left" style="width: 40px; height: 40px; background-color:#696CFF; cursor: pointer; border-radius: 40px"><span style="font-size: 13px; color: white; vertical-align: middle;"><center>$1</center></span></div></center>', 'DocumentID');
        $this->datatables->edit_column('Level','Level $1', 'Level');
        $this->datatables->edit_column('Narration', '<b>$1</b> <br> <b> Confirm By | $2 | $3 </b>', 'Narration,confirmname,confirmdate');
        $this->datatables->add_column('edit', '$1', 'documentallapproval(DocumentAutoID,Level,0,documentApprovedID,DocumentID,1,DocumentCode,payrollMonth,bankGLAutoID)');
       
       
        echo $this->datatables->generate();
    }

    function total_document_count()
    {
        $companyID = current_companyID();
        $userID = current_userID();

        $document_codes = $this->db->query("SELECT * FROM(  
                                 SELECT documentID FROM srp_erp_documentapproved WHERE companyID = {$companyID}
                                 AND approvedYN = 0 GROUP BY documentID
                                 UNION 
                                 SELECT 'EC' AS documentID 
                              ) t1 ORDER BY documentID ASC #limit 29")->result_array();

        $document_codes = array_column($document_codes, 'documentID');

        $query = '';

        if(in_array('BD', $document_codes)){ /*** Budget ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT budgetAutoID as DocumentAutoID, srp_erp_budgetmaster.documentID as DocumentID
                       FROM srp_erp_budgetmaster
                       LEFT JOIN srp_erp_segment ON srp_erp_budgetmaster.segmentID = srp_erp_segment.segmentID
                       AND srp_erp_budgetmaster.companyID = srp_erp_segment.companyID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_budgetmaster.budgetAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgetmaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_budgetmaster.currentLevelNo
                       WHERE srp_erp_budgetmaster.companyID = '{$companyID}' AND srp_erp_budgetmaster.budgetType = 1 AND srp_erp_documentapproved.approvedYN = ''
                       AND srp_erp_documentapproved.documentID = 'BD' AND srp_erp_approvalusers.documentID = 'BD' AND srp_erp_approvalusers.employeeID = '{$userID}'";
        }

        if(in_array('BDT', $document_codes)){ /*** Budget Transfer ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT budgetTransferAutoID as DocumentAutoID, srp_erp_budgettransfer.documentID as DocumentID
	                   FROM srp_erp_budgettransfer 
	                   JOIN srp_erp_companyfinanceyear ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_budgettransfer.financeYearID
	                   JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_budgettransfer.budgetTransferAutoID
	                   AND srp_erp_documentapproved.approvalLevelID = srp_erp_budgettransfer.currentLevelNo
	                   JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_budgettransfer.currentLevelNo
	                   WHERE srp_erp_budgettransfer.companyID = '{$companyID}' AND srp_erp_documentapproved.approvedYN = '' AND srp_erp_documentapproved.documentID = 'BDT'
	                   AND srp_erp_approvalusers.documentID = 'BDT' AND srp_erp_approvalusers.employeeID = '{$userID}'";
        }

        if(in_array('BRC', $document_codes)){ /*** Bank Reconciliation ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT b.bankRecAutoID AS DocumentAutoID, b.documentID AS DocumentID
                       FROM srp_erp_bankrecmaster AS b
                       JOIN srp_erp_documentapproved AS d ON d.documentSystemCode = b.bankRecAutoID
                       AND d.approvalLevelID = b.currentLevelNo
                       LEFT JOIN srp_erp_chartofaccounts AS c ON c.GLAutoID = b.bankGLAutoID
                       JOIN srp_erp_approvalusers AS au ON au.levelNo = b.currentLevelNo
                       WHERE d.documentID = 'BRC' AND au.documentID = 'BRC' AND au.employeeID = '{$userID}' AND au.companyID = '{$companyID}'
                       AND b.companyID = '{$companyID}' AND d.approvedYN = ''";
        }

        if(in_array('BSI', $document_codes)){ /*** Supplier Invoice ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_paysupplierinvoicemaster.InvoiceAutoID AS DocumentAutoID, srp_erp_paysupplierinvoicemaster.documentID AS DocumentID
                       FROM srp_erp_paysupplierinvoicemaster
                       LEFT JOIN ( 
                            SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicedetail GROUP BY InvoiceAutoID 
                       ) det ON ( det.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
                       LEFT JOIN ( 
                            SELECT SUM( transactionAmount ) AS transactionAmount, InvoiceAutoID FROM srp_erp_paysupplierinvoicetaxdetails GROUP BY InvoiceAutoID 
                       ) addondet ON ( addondet.InvoiceAutoID = srp_erp_paysupplierinvoicemaster.InvoiceAutoID )
                       JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_paysupplierinvoicemaster.InvoiceAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_paysupplierinvoicemaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_paysupplierinvoicemaster.currentLevelNo
                       WHERE srp_erp_documentapproved.documentID = 'BSI' AND srp_erp_approvalusers.documentID = 'BSI' AND srp_erp_approvalusers.employeeID = '{$userID}'
                       AND srp_erp_documentapproved.approvedYN = '0' AND srp_erp_paysupplierinvoicemaster.companyID = '{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'";
        }

        if(in_array('BT', $document_codes)){ /*** Bank Transfer ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT bankTransferAutoID AS DocumentAutoID, srp_erp_banktransfer.documentID AS DocumentID
                       FROM srp_erp_banktransfer 
                       LEFT JOIN srp_erp_chartofaccounts a ON fromBankGLAutoID = a.GLAutoID
                       LEFT JOIN srp_erp_chartofaccounts b ON toBankGLAutoID = b.GLAutoID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_banktransfer.bankTransferAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_banktransfer.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_banktransfer.currentLevelNo
                       WHERE srp_erp_approvalusers.documentID = 'BT' AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.documentID = 'BT'
                       AND srp_erp_documentapproved.approvedYN = '0' AND srp_erp_banktransfer.companyID = '{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'";
        }

        if(in_array('CINV', $document_codes)){ /*** Customer Invoice ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_customerinvoicemaster.invoiceAutoID AS DocumentAutoID, srp_erp_customerinvoicemaster.documentID AS DocumentID
	                   FROM srp_erp_customerinvoicemaster
	                   LEFT JOIN ( 
	                      SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails GROUP BY invoiceAutoID 
	                   ) det ON ( det.invoiceAutoID = srp_erp_customerinvoicemaster.invoiceAutoID )
	                   LEFT JOIN ( 
	                        SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails GROUP BY InvoiceAutoID 
	                   ) addondet ON ( addondet.InvoiceAutoID = srp_erp_customerinvoicemaster.InvoiceAutoID )
	                   LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
	                   JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_customerinvoicemaster.invoiceAutoID
	                   AND srp_erp_documentapproved.approvalLevelID = srp_erp_customerinvoicemaster.currentLevelNo
	                   JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_customerinvoicemaster.currentLevelNo
	                   WHERE srp_erp_documentapproved.documentID = 'CINV' AND srp_erp_approvalusers.documentID = 'CINV' AND srp_erp_documentapproved.companyID = '{$companyID}'
	                   AND srp_erp_approvalusers.companyID = '{$companyID}' AND srp_erp_customerinvoicemaster.companyID = '{$companyID}' 
	                   AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.approvedYN = '0'";
        }

        if(in_array('CN', $document_codes)){ /*** Credit Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_creditnotemaster.creditNoteMasterAutoID AS DocumentAutoID, srp_erp_creditnotemaster.documentID AS DocumentID
                       FROM srp_erp_creditnotemaster
                       LEFT JOIN ( 
                            SELECT SUM( transactionAmount ) AS transactionAmount, creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID 
                       ) det ON ( det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID )
                       LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID
                       JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_creditnotemaster.creditNoteMasterAutoID
                       AND srp_erp_documentapproved.approvalLevelID = srp_erp_creditnotemaster.currentLevelNo
                       JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = srp_erp_creditnotemaster.currentLevelNo
                       WHERE srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_creditnotemaster.companyID = '{$companyID}'
                       AND srp_erp_approvalusers.companyID = '{$companyID}' AND srp_erp_documentapproved.documentID = 'CN' AND srp_erp_approvalusers.documentID = 'CN'
                       AND srp_erp_documentapproved.approvedYN = '0'";
        }

        if(in_array('CNT', $document_codes) or in_array('QUT', $document_codes) or in_array('SO', $document_codes)){ /*** Contract / Quotation / Sales Order ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_contractmaster.contractAutoID AS DocumentAutoID,
                        `srp_erp_contractmaster`.`documentID` AS `DocumentID`
                    FROM
                        `srp_erp_contractmaster`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID ) det ON ( `det`.`contractAutoID` = srp_erp_contractmaster.contractAutoID )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_contractmaster`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_contractmaster`.`contractAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_contractmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_contractmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` IN ( 'CNT','QUT', 'SO' )
                        AND `srp_erp_approvalusers`.`documentID` IN ( 'CNT','QUT', 'SO' )
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_contractmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    GROUP BY `srp_erp_documentapproved`.`documentSystemCode`";
        }

        if(in_array('DC', $document_codes)){ /*** Donor Collections ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `collectionAutoId` as DocumentAutoID,
                        `documentCode` as DocumentID
                    FROM
                        (
                    SELECT
                        documentApprovedID,
                        `srp_erp_ngo_donorcollectionmaster`.`approvedYN`,
                        `srp_erp_ngo_donorcollectionmaster`.`confirmedDate`,
                        `srp_erp_ngo_donorcollectionmaster`.`confirmedByName`,
                        `srp_erp_ngo_donorcollectionmaster`.`companyID`,
                        `srp_erp_ngo_donorcollectionmaster`.`transactionCurrencyDecimalPlaces`,
                        `srp_erp_ngo_donorcollectionmaster`.`narration`,
                        `approvalLevelID`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentCode`,
                        `confirmedYN`,
                        `srp_erp_ngo_donorcollectionmaster`.`collectionAutoId`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentSystemCode`,
                        `srp_erp_ngo_donorcollectionmaster`.`documentDate`,
                        `referenceNo`,
                        `transactionCurrency`,
                        `donorsID`,
                        FORMAT( IFNULL( transactionAmount, 0 ), transactionCurrencyDecimalPlaces ) AS transactionAmount,
                    NAME
                    FROM
                        srp_erp_ngo_donorcollectionmaster
                        LEFT JOIN srp_erp_ngo_donors ON donorsID = contactID
                        LEFT JOIN ( SELECT sum( transactionAmount ) AS transactionAmount, collectionAutoId FROM srp_erp_ngo_donorcollectiondetails GROUP BY collectionAutoId ) srp_erp_ngo_donorcollectiondetails ON srp_erp_ngo_donorcollectionmaster.collectionAutoId = srp_erp_ngo_donorcollectiondetails.collectionAutoId
                        LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = srp_erp_ngo_donorcollectionmaster.collectionAutoId
                        AND approvalLevelID = currentLevelNo
                        LEFT JOIN srp_erp_approvalusers ON levelNo = srp_erp_ngo_donorcollectionmaster.currentLevelNo
                    WHERE
                        isDeleted != 1
                        AND srp_erp_documentapproved.documentID = 'DC'
                        AND srp_erp_approvalusers.documentID = 'DC'
                        AND employeeID = '{$userID}'
                        AND srp_erp_ngo_donorcollectionmaster.approvedYN = 0
                        AND srp_erp_ngo_donorcollectionmaster.companyID = '{$companyID}'
                    ORDER BY
                        collectionAutoId DESC
                        ) t";
        }

        if(in_array('DN', $document_codes)){ /*** Debit Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_debitnotemaster.debitNoteMasterAutoID AS DocumentAutoID,
                        srp_erp_debitnotemaster.documentID AS DocumentID
                    FROM
                        `srp_erp_debitnotemaster`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID ) det ON ( `det`.`debitNoteMasterAutoID` = srp_erp_debitnotemaster.debitNoteMasterAutoID )
                        JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_debitnotemaster`.`supplierID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_debitnotemaster`.`debitNoteMasterAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_debitnotemaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_debitnotemaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_debitnotemaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`documentID` = 'DN'
                        AND `srp_erp_approvalusers`.`documentID` = 'DN'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('DO', $document_codes)){ /*** Delivery Order ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                masterTbl.DOAutoID AS DocumentAutoID,
                masterTbl.documentID AS DocumentID
            FROM
                srp_erp_deliveryorder masterTbl
                JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.DOAutoID
                JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.transactionCurrencyID 
                AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo 
            WHERE
                srp_erp_documentapproved.documentID = 'DO' 
                AND srp_erp_approvalusers.documentID = 'DO' 
                AND srp_erp_documentapproved.companyID = '{$companyID}' 
                AND srp_erp_approvalusers.companyID = '{$companyID}' 
                AND srp_erp_approvalusers.employeeID = '{$userID}' 
                AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array( 'EC', $document_codes)) { /*** Expense Claim ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT srp_erp_expenseclaimmaster.expenseClaimMasterAutoID AS DocumentAutoID, srp_erp_expenseclaimmaster.documentID AS DocumentID
                   FROM srp_erp_expenseclaimmaster 
                   LEFT JOIN ( SELECT SUM( empCurrencyAmount ) AS transactionAmount, expenseClaimMasterAutoID, empCurrency,transactionCurrencyDecimalPlaces FROM srp_erp_expenseclaimdetails GROUP BY expenseClaimMasterAutoID ) det ON ( det.expenseClaimMasterAutoID = srp_erp_expenseclaimmaster.expenseClaimMasterAutoID )
                   JOIN srp_erp_employeemanagers ON srp_erp_expenseclaimmaster.claimedByEmpID = srp_erp_employeemanagers.empID
                   WHERE srp_erp_expenseclaimmaster.companyID = '{$companyID}' AND srp_erp_expenseclaimmaster.confirmedYN = 1 AND srp_erp_expenseclaimmaster.approvedYN = '0'
                   AND srp_erp_employeemanagers.managerID = '{$userID}' AND srp_erp_employeemanagers.active = 1 ";
                   
        }
        
        if(in_array('FA', $document_codes)){ /*** Fixed Asset ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `faID` AS DocumentAutoID,
                        srp_erp_fa_asset_master.documentID AS DocumentID
                    FROM
                        `srp_erp_fa_asset_master`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_fa_asset_master`.`faID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_asset_master`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_asset_master`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'FA'
                        AND `srp_erp_approvalusers`.`documentID` = 'FA'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_fa_asset_master`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'";
        }

        if(in_array('FAD', $document_codes)){ /*** Fixed Asset Depreciation ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `depMasterAutoID` AS DocumentAutoID,
                    srp_erp_fa_depmaster.documentID AS DocumentID
                FROM
                    `srp_erp_fa_depmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_fa_depmaster`.`depMasterAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_fa_depmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_fa_depmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'FAD'
                    AND `srp_erp_approvalusers`.`documentID` = 'FAD'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_fa_depmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array('FS', $document_codes)){ /*** Final Settlement ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `masterID` as DocumentAutoID,
                    fm.documentID as DocumentID
                FROM
                    `srp_erp_pay_finalsettlementmaster` AS `fm`
                    JOIN `srp_employeesdetails` `empTB` ON `empTB`.`EIdNo` = `fm`.`empID`
                    JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `fm`.`masterID`
                    AND `approve`.`approvalLevelID` = `fm`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `fm`.`currentLevelNo`
                WHERE
                    `approve`.`documentID` = 'FS'
                    AND `ap`.`documentID` = 'FS'
                    AND `ap`.`employeeID` =  '{$userID}'
                    AND `approve`.`approvedYN` = ''
                    AND `fm`.`companyID` = '{$companyID}'
                    AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array('FU', $document_codes)){ /*** Fuel Usage ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                            `fuelusageID` as DocumentAutoID,
                            DocumentID
                        FROM
                            (
                        SELECT
                            documentApprovedID,
                        
                            `fleet_fuelusagemaster`.`approvedYN`,
                            `fleet_fuelusagemaster`.`companyID`,
                            `fleet_fuelusagemaster`.`confirmedByName`,
                            `fleet_fuelusagemaster`.`confirmedDate`,
                            fleet_fuelusagemaster.transactionCurrencyDecimalPlaces,
                            `approvalLevelID`,
                            narration,
                            `fleet_fuelusagemaster`.`documentID`,
                            `confirmedYN`,
                            `fleet_fuelusagemaster`.`fuelusageID`,
                            `fleet_fuelusagemaster`.`supplierAutoID`,
                            `fleet_fuelusagemaster`.`documentCode`,
                            `fleet_fuelusagemaster`.`documentDate`,
                            `referenceNumber`,
                            `transactionCurrency`,
                            FORMAT( IFNULL( fleet_fuelusagedetails.transactionAmount, 0 ), transactionCurrencyDecimalPlaces ) AS transactionAmount,
                            supplierName
                        FROM
                            fleet_fuelusagemaster
                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = fleet_fuelusagemaster.supplierAutoID
                            LEFT JOIN ( SELECT sum( fleet_fuelusagedetails.totalAmount ) AS transactionAmount, fuelusageID FROM fleet_fuelusagedetails GROUP BY fuelusageID ) fleet_fuelusagedetails ON fleet_fuelusagemaster.fuelusageID = fleet_fuelusagedetails.fuelusageID
                            LEFT JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = fleet_fuelusagemaster.fuelusageID
                            AND approvalLevelID = currentLevelNo
                            LEFT JOIN srp_erp_approvalusers ON levelNo = fleet_fuelusagemaster.currentLevelNo
                        WHERE
                            isDeleted != 1
                            AND srp_erp_documentapproved.documentID = 'FU'
                            AND srp_erp_approvalusers.documentID = 'FU'
                            AND employeeID = '{$userID}'
                            AND fleet_fuelusagemaster.approvedYN = 0
                            AND fleet_fuelusagemaster.companyID = '{$companyID}'
                            ) t";
        }

        if(in_array('GRV', $document_codes)){ /*** Goods Receipt Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `srp_erp_grvmaster`.`grvAutoID` AS `DocumentAutoID`,
                        `srp_erp_grvmaster`.`DocumentID` AS `DocumentID`
                    FROM
                        `srp_erp_grvmaster`
                        LEFT JOIN ( SELECT SUM( receivedTotalAmount ) AS receivedTotalAmount, grvAutoID FROM srp_erp_grvdetails GROUP BY grvAutoID ) det ON ( `det`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
                        LEFT JOIN ( SELECT SUM( total_amount ) AS total_amount, grvAutoID FROM srp_erp_grv_addon GROUP BY grvAutoID ) addondet ON ( `addondet`.`grvAutoID` = srp_erp_grvmaster.grvAutoID )
                        LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_grvmaster`.`supplierID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_grvmaster`.`grvAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_grvmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_grvmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'GRV'
                        AND `srp_erp_approvalusers`.`documentID` = 'GRV'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_grvmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array('HCINV', $document_codes)){ /*** Customer Invoice - HIRA ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_customerinvoicemaster_temp.invoiceAutoID AS DocumentAutoID,
                    srp_erp_customerinvoicemaster_temp.documentID AS DocumentID
                FROM
                    `srp_erp_customerinvoicemaster_temp`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster_temp.invoiceAutoID )
                    LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster_temp.InvoiceAutoID )
                    LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerinvoicemaster_temp`.`customerID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_customerinvoicemaster_temp`.`invoiceAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerinvoicemaster_temp`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerinvoicemaster_temp`.`currentLevelNo`
                    LEFT join srp_erp_segment segmentmaster on segmentmaster.segmentID  = srp_erp_customerinvoicemaster_temp.segmentID
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'HCINV'
                    AND `srp_erp_approvalusers`.`documentID` = 'HCINV'
                AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_customerinvoicemaster_temp`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('IOU', $document_codes)){ /*** IOU Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    masterTbl.voucherAutoID AS DocumentAutoID,
                    masterTbl.documentID AS DocumentID
                FROM
                    `srp_erp_iouvouchers` `masterTbl`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, voucherAutoID FROM srp_erp_iouvoucherdetails detailTbl GROUP BY voucherAutoID ) det ON ( `masterTbl`.`voucherAutoID` = det.voucherAutoID )
                    LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`voucherAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'IOU'
                    AND `srp_erp_approvalusers`.`documentID` = 'IOU'
                    AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND masterTbl.companyID = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('IOUE', $document_codes)){ /*** IOU Expenses ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        masterTbl.bookingMasterID AS DocumentAutoID,
                        masterTbl.documentID AS DocumentID
                    FROM
                        `srp_erp_ioubookingmaster` `masterTbl`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, bookingMasterID FROM srp_erp_ioubookingdetails detailTbl GROUP BY bookingMasterID ) det ON ( `masterTbl`.`bookingMasterID` = det.bookingMasterID )
                        LEFT JOIN `srp_employeesdetails` `employee` ON `employee`.`EIdNo` = `masterTbl`.`empID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`bookingMasterID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`                    
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'IOUE'
                        AND `srp_erp_approvalusers`.`documentID` = 'IOUE'
                        AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                        AND `masterTbl`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('JP', $document_codes)){ /*** Journey Plan ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    masterTbl.journeyPlanMasterID AS DocumentAutoID,
                    masterTbl.documentID AS DocumentID
                FROM
                    `srp_erp_journeyplan_master` `masterTbl`
                    LEFT JOIN ( SELECT MAX( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) rout ON `rout`.`journeyPlanMasterID` = `masterTbl`.`journeyPlanMasterID`
                    LEFT JOIN ( SELECT MIN( JP_RouteDetailsID ) AS JP_RouteDetailsID, journeyPlanMasterID FROM srp_erp_journeyplan_routedetails GROUP BY journeyPlanMasterID ) routmin ON `routmin`.`journeyPlanMasterID` = `masterTbl`.`journeyPlanMasterID`
                    LEFT JOIN `fleet_drivermaster` `driver` ON `driver`.`driverMasID` = `masterTbl`.`driverID`
                    LEFT JOIN `fleet_vehiclemaster` `vehicalemaster` ON `vehicalemaster`.`vehicleMasterID` = `masterTbl`.`vehicleID`
                    LEFT JOIN `srp_erp_journeyplan_routedetails` `arrive` ON `arrive`.`JP_RouteDetailsID` = `rout`.`JP_RouteDetailsID`
                    LEFT JOIN `srp_erp_journeyplan_routedetails` `depart` ON `depart`.`JP_RouteDetailsID` = `routmin`.`JP_RouteDetailsID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`journeyPlanMasterID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'JP'
                    AND `srp_erp_approvalusers`.`documentID` = 'JP'
                    AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = ''";
        }

        if(in_array('JV', $document_codes)){ /*** Journal Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `srp_erp_jvmaster`.`JVMasterAutoId` AS DocumentAutoID,
                        `srp_erp_jvmaster`.`documentID` AS DocumentID
                    FROM
                        `srp_erp_jvmaster`
                        LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, JVMasterAutoId FROM srp_erp_jvdetail GROUP BY JVMasterAutoId ) debamt ON ( `debamt`.`JVMasterAutoId` = srp_erp_jvmaster.JVMasterAutoId )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_jvmaster`.`JVMasterAutoId`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_jvmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_jvmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_jvmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`documentID` = 'JV'
                        AND `srp_erp_approvalusers`.`documentID` = 'JV'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array('LA', $document_codes)){ /*** Leave ***/

            $query .= ($query != '')? ' UNION ': '';

            $query_leave = "SELECT
                            LM.leaveMasterID AS DocumentAutoID,
                            'LA' AS DocumentID
                        FROM
                            `srp_erp_leavemaster` AS `LM`
                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = LM.empID
                            JOIN srp_erp_leaveapprovalsetup AS LAS ON LM.currentLevelNo = LAS.approvalLevel 
                            AND LM.leaveGroupID = LAS.leaveGroupID
                        WHERE
                            `LM`.`approvedYN` = '0' 
                            AND `LM`.`companyID` = '{$companyID}' 
                            AND (
                                (LAS.approvalType = 1 AND LM.empID IN (
                                    SELECT emp_manager.empID 
                                    FROM srp_employeesdetails AS emp_detail
                                    JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID 
                                    WHERE emp_manager.active = 1 
                                    AND emp_manager.companyID = '{$companyID}' 
                                    AND emp_manager.managerID = '{$userID}'
                                ))
                                OR
                                (LAS.approvalType = 4 AND LM.empID IN (
                                    SELECT emp_detail.EIdNo 
                                    FROM srp_employeesdetails AS emp_detail
                                    JOIN srp_empdepartments AS emp_dep ON emp_detail.EIdNo = emp_dep.EmpID
                                    JOIN srp_departmentmaster AS srp_dep ON emp_dep.DepartmentMasterID = srp_dep.DepartmentMasterID 
                                    WHERE emp_dep.isactive = 1 
                                    AND emp_dep.Erp_companyID = '{$companyID}' 
                                    AND srp_dep.hod_id = '{$userID}'
                                ))
                                OR
                                (LAS.approvalType = 2 AND LM.empID IN (
                                    SELECT emp_detail.Eidno 
                                    FROM srp_employeesdetails AS emp_detail
                                    JOIN srp_erp_employeemanagers AS emp_manager ON emp_detail.EIdNo = emp_manager.empID
                                    JOIN srp_erp_employeemanagers AS top_manager ON top_manager.empID = emp_manager.managerID 
                                    WHERE emp_manager.active = 1 
                                    AND emp_manager.companyID = '{$companyID}' 
                                    AND top_manager.managerID = '{$userID}'
                                ))
                                OR
                                (LAS.approvalType = 3 AND LM.empID IN (
                                    SELECT emp_detail.EIdNo 
                                    FROM srp_employeesdetails AS emp_detail
                                    JOIN srp_erp_leaveapprovalsetuphremployees AS hremployee ON emp_detail.EIdNo = hremployee.empID 
                                    WHERE hremployee.companyID = '{$companyID}' AND hremployee.empID = '{$userID}'
                                ))
                            )";

                $query .= $query_leave;
        }

        if(in_array( 'LEC', $document_codes)) {/*** Leave Encashment ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID
                        FROM srp_erp_pay_leaveencashment masterTbl	 
                        JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
                        JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
                        AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                        JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
                        WHERE srp_erp_documentapproved.documentID = 'LEC' AND srp_erp_approvalusers.documentID = 'LEC'
                        AND srp_erp_documentapproved.companyID ='{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'
                        AND srp_erp_approvalusers.employeeID = '{$userID}' AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array( 'LO', $document_codes)) {/*** Loan ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        `e_loan`.`ID` AS `DocumentAutoID`,
                        `e_loan`.`documentID` AS `DocumentID`
                    FROM
                        `srp_erp_pay_emploan` AS `e_loan`
                        JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `e_loan`.`ID`
                        AND `approve`.`approvalLevelID` = `e_loan`.`currentLevelNo`
                        JOIN `srp_employeesdetails` AS `emp` ON `emp`.`EIdNo` = `e_loan`.`empID`
                        JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `e_loan`.`currentLevelNo`
                    WHERE
                        `approve`.`documentID` = 'LO'
                        AND `ap`.`documentID` = 'LO'
                        AND `ap`.`employeeID` =  '{$userID}'
                        AND `approve`.`approvedYN` = ''
                        AND `e_loan`.`companyID` = '{$companyID}'
                        AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'MI', $document_codes)) {/*** Material Issue ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                srp_erp_itemissuemaster.itemIssueAutoID AS DocumentAutoID,
                srp_erp_itemissuemaster.documentID AS DocumentID
            FROM
                `srp_erp_itemissuemaster`
                LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID ) det ON ( `det`.`itemIssueAutoID` = srp_erp_itemissuemaster.itemIssueAutoID )
                JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_itemissuemaster`.`itemIssueAutoID`
                AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_itemissuemaster`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_itemissuemaster`.`currentLevelNo`
            WHERE
                `srp_erp_documentapproved`.`documentID` = 'MI'
                AND `srp_erp_approvalusers`.`documentID` = 'MI'
                AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                AND `srp_erp_itemissuemaster`.`companyID` = '{$companyID}'
                AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'MR', $document_codes)) {/*** Material Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_materialrequest.mrAutoID AS DocumentAutoID,
                        srp_erp_materialrequest.DocumentID AS DocumentID
                    FROM
                        `srp_erp_materialrequest`
                        LEFT JOIN ( SELECT SUM( qtyRequested ) AS qtyRequested, mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID ) det ON ( `det`.`mrAutoID` = srp_erp_materialrequest.mrAutoID )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_materialrequest`.`mrAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialrequest`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialrequest`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'MR'
                        AND `srp_erp_approvalusers`.`documentID` = 'MR'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_materialrequest`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'MRN', $document_codes)) {/*** Material Receipt Note ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_materialreceiptmaster.mrnAutoID AS DocumentAutoID,
                    srp_erp_materialreceiptmaster.documentID AS DocumentID
                FROM
                    `srp_erp_materialreceiptmaster`
                    LEFT JOIN ( SELECT SUM( qtyReceived ) AS qtyReceived, mrnAutoID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID ) det ON ( `det`.`mrnAutoID` = srp_erp_materialreceiptmaster.mrnAutoID )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_materialreceiptmaster`.`mrnAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_materialreceiptmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'MRN'
                    AND `srp_erp_approvalusers`.`documentID` = 'MRN'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_materialreceiptmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'PO', $document_codes)) {/*** Purchase Order ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_purchaseordermaster.purchaseOrderID AS DocumentAutoID,
                    srp_erp_purchaseordermaster.DocumentID AS DocumentID
                FROM
                    `srp_erp_purchaseordermaster`
                    LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON ( `det`.`purchaseOrderID` = srp_erp_purchaseordermaster.purchaseOrderID )
                    LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_purchaseordermaster`.`supplierID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_purchaseordermaster`.`purchaseOrderID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaseordermaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'PO'
                    AND `srp_erp_approvalusers`.`documentID` = 'PO'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_purchaseordermaster`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'PRQ', $document_codes)) {/*** Purchase Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_purchaserequestmaster.purchaseRequestID AS DocumentAutoID,
                        srp_erp_purchaserequestmaster.documentID AS DocumentID
                    FROM
                        `srp_erp_purchaserequestmaster`
                        LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID ) det ON ( `det`.`purchaseRequestID` = srp_erp_purchaserequestmaster.purchaseRequestID )
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_purchaserequestmaster`.`purchaseRequestID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_purchaserequestmaster`.`currentLevelNo`
                        LEFT JOIN srp_erp_segment segmentmaster on segmentmaster.segmentID =srp_erp_purchaserequestmaster.segmentID 
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'PRQ'
                        AND `srp_erp_approvalusers`.`documentID` = 'PRQ'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND (
                        `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        OR (
                        `srp_erp_approvalusers`.`employeeID` = - 1
                        AND srp_erp_purchaserequestmaster.requestedEmpID IN (
                    SELECT
                        empmanagers.empID
                    FROM
                        srp_employeesdetails empdetail
                        JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
                        AND empmanagers.active = 1
                    WHERE
                        empmanagers.companyID = '{$companyID}'
                        AND empmanagers.managerID = '{$userID}'
                        )
                        )
                        )
                        AND `srp_erp_purchaserequestmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'PV', $document_codes)) {/*** Payment Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    srp_erp_paymentvouchermaster.payVoucherAutoId AS DocumentAutoID,
                    srp_erp_paymentvouchermaster.documentID AS DocumentID
                FROM
                    `srp_erp_paymentvouchermaster`
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
                    ) det ON ( `det`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
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
                    ) tyepdet ON ( `tyepdet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"debitnote\" GROUP BY payVoucherAutoId ) debitnote ON ( `debitnote`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = \"SR\" GROUP BY payVoucherAutoId ) SR ON ( `SR`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON ( `addondet`.`payVoucherAutoId` = srp_erp_paymentvouchermaster.payVoucherAutoId )
                    LEFT JOIN `srp_erp_suppliermaster` ON `srp_erp_suppliermaster`.`supplierAutoID` = `srp_erp_paymentvouchermaster`.`partyID`
                    LEFT JOIN `srp_employeesdetails` ON `srp_employeesdetails`.`EIdNo` = `srp_erp_paymentvouchermaster`.`partyID`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_paymentvouchermaster`.`PayVoucherAutoId`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_paymentvouchermaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'PV'
                    AND `srp_erp_approvalusers`.`documentID` = 'PV'
                    /*AND `pvType` <> 'SC'*/
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_paymentvouchermaster`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'RJV', $document_codes)) {/*** Recurring Journal Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `srp_erp_recurringjvmaster`.`RJVMasterAutoId` AS `DocumentAutoID`,
                    `srp_erp_recurringjvmaster`.`documentID` AS `DocumentID`
                FROM
                    `srp_erp_recurringjvmaster`
                    LEFT JOIN ( SELECT SUM( debitAmount ) AS debitAmount, RJVMasterAutoId FROM srp_erp_recurringjvdetail GROUP BY RJVMasterAutoId ) debamt ON ( `debamt`.`RJVMasterAutoId` = srp_erp_recurringjvmaster.RJVMasterAutoId )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_recurringjvmaster`.`RJVMasterAutoId`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_recurringjvmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_recurringjvmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                    AND `srp_erp_recurringjvmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_documentapproved`.`documentID` = 'RJV'
                    AND `srp_erp_approvalusers`.`documentID` = 'RJV'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'RV', $document_codes)) {/*** Receipt Voucher ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        srp_erp_customerreceiptmaster.receiptVoucherAutoId AS DocumentAutoID,
                        srp_erp_customerreceiptmaster.documentID AS DocumentID
                    FROM
                        `srp_erp_customerreceiptmaster`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type != \"creditnote\" GROUP BY receiptVoucherAutoId ) det ON ( `det`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_customerreceiptmaster`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_customerreceiptmaster`.`receiptVoucherAutoId`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_customerreceiptmaster`.`currentLevelNo`
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, receiptVoucherAutoId FROM srp_erp_customerreceiptdetail WHERE srp_erp_customerreceiptdetail.type = \"creditnote\" GROUP BY receiptVoucherAutoId ) Creditnots ON ( `Creditnots`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                        LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, receiptVoucherAutoId FROM srp_erp_customerreceipttaxdetails GROUP BY receiptVoucherAutoId ) addondet ON ( `addondet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
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
                        ) tyepdet ON ( `tyepdet`.`receiptVoucherAutoId` = srp_erp_customerreceiptmaster.receiptVoucherAutoId )
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'RV'
                        AND `srp_erp_approvalusers`.`documentID` = 'RV'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_customerreceiptmaster`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SA', $document_codes)) {/*** Stock Adjustment ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    stockAdjustmentAutoID AS DocumentAutoID,
                    srp_erp_stockadjustmentmaster.documentID AS DocumentID
                FROM
                    `srp_erp_stockadjustmentmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockadjustmentmaster`.`stockAdjustmentAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockadjustmentmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SA'
                    AND `srp_erp_approvalusers`.`documentID` = 'SA'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stockadjustmentmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SAR', $document_codes)) {/*** Salary Advance Request ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT masterTbl.masterID AS DocumentAutoID, masterTbl.documentID AS DocumentID
                    FROM srp_erp_pay_salaryadvancerequest masterTbl	 
                    JOIN srp_erp_documentapproved ON srp_erp_documentapproved.documentSystemCode = masterTbl.masterID
                    JOIN srp_erp_currencymaster AS cur_mas ON cur_mas.currencyID = masterTbl.trCurrencyID
                    AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo
                    JOIN srp_erp_approvalusers ON srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo
                    WHERE srp_erp_documentapproved.documentID = 'SAR' AND srp_erp_approvalusers.documentID = 'SAR'
                    AND srp_erp_documentapproved.companyID ='{$companyID}' AND srp_erp_approvalusers.companyID = '{$companyID}'
                    AND (
                        srp_erp_approvalusers.employeeID = '{$userID}'
                        OR (
                            srp_erp_approvalusers.employeeID = - 1
                            AND masterTbl.empID IN (
                                SELECT empmanagers.empID
                                FROM srp_employeesdetails empdetail
                                JOIN srp_erp_employeemanagers empmanagers ON empdetail.EIdNo = empmanagers.empID
                                AND empmanagers.active = 1 WHERE empmanagers.companyID = '{$companyID}' AND empmanagers.managerID = '{$userID}'
                           )
                        )
                    )	
                    AND srp_erp_documentapproved.approvedYN = ''";
        }

        if(in_array( 'SC', $document_codes)) {/*** Sales Commision ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `srp_erp_salescommisionmaster`.`salesCommisionID` AS `DocumentAutoID`,
                    `srp_erp_salescommisionmaster`.`DocumentID` AS `DocumentID`
                FROM
                    `srp_erp_salescommisionmaster`
                    LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisiondetail GROUP BY salesCommisionID ) det ON ( `det`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
                    LEFT JOIN ( SELECT SUM( netCommision ) AS transactionAmount, salesCommisionID FROM srp_erp_salescommisionperson GROUP BY salesCommisionID ) det2 ON ( `det2`.`salesCommisionID` = srp_erp_salescommisionmaster.salesCommisionID )
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_salescommisionmaster`.`salesCommisionID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salescommisionmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_salescommisionmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SC'
                    AND `srp_erp_approvalusers`.`documentID` = 'SC'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_salescommisionmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SCNT', $document_codes)) {/*** Stock Counting ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    stockCountingAutoID AS DocumentAutoID,
                    srp_erp_stockcountingmaster.documentID AS DocumentID
                FROM
                    `srp_erp_stockcountingmaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockcountingmaster`.`stockCountingAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockcountingmaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockcountingmaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'SCNT'
                    AND `srp_erp_approvalusers`.`documentID` = 'SCNT'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stockcountingmaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SD', $document_codes)) {/*** Salary Declaration ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        salarydeclarationMasterID AS DocumentAutoID,
                        srp_erp_salarydeclarationmaster.documentID AS DocumentID
                    FROM
                        `srp_erp_salarydeclarationmaster`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_salarydeclarationmaster`.`salarydeclarationMasterID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_salarydeclarationmaster`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_salarydeclarationmaster`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'SD'
                        AND `srp_erp_approvalusers`.`documentID` = 'SD'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_salarydeclarationmaster`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` ='{$companyID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = ''";
        }

        if(in_array( 'SLR', $document_codes)) {/*** Sales Return ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                        masterTbl.salesReturnAutoID AS DocumentAutoID,
                        masterTbl.documentID AS DocumentID
                    FROM
                        `srp_erp_salesreturnmaster` `masterTbl`
                        LEFT JOIN ( SELECT SUM( totalValue + IFNULL(taxAmount, 0) - IFNULL(rebateAmount, 0)) AS totalValue, salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY salesReturnAutoID ) det ON ( `det`.`salesReturnAutoID` = masterTbl.salesReturnAutoID )
                        LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `masterTbl`.`customerID`
                        JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `masterTbl`.`salesReturnAutoID`
                        AND `srp_erp_documentapproved`.`approvalLevelID` = `masterTbl`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `masterTbl`.`currentLevelNo`
                    WHERE
                        `srp_erp_documentapproved`.`documentID` = 'SLR'
                        AND `srp_erp_approvalusers`.`documentID` = 'SLR'
                        AND `srp_erp_documentapproved`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `srp_erp_documentapproved`.`approvedYN` = '0'";
        }

        if(in_array( 'SP', $document_codes)) {/*** Salary Process ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                    `payrollMasterID` AS DocumentAutoID,
                    t2.documentID AS DocumentID
                FROM
                    `srp_erp_payrollmaster` AS `t2`
                    JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
                    AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
                WHERE
                    `approve`.`documentID` = 'SP'
                    AND `ap`.`documentID` = 'SP'
                    AND `ap`.`employeeID` = '{$userID}'
                    AND `approve`.`approvedYN` = '0'
                    AND `t2`.`companyID` = '{$companyID}'
                    AND `ap`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SPN', $document_codes)) {/*** Non Salary Process ***/
            $query .= ($query != '')? ' UNION ': '';

            $query .= "SELECT
                `payrollMasterID` AS DocumentAutoID,
                t2.documentID AS DocumentID
            FROM
                `srp_erp_non_payrollmaster` AS `t2`
                JOIN `srp_erp_documentapproved` AS `approve` ON `approve`.`documentSystemCode` = `t2`.`payrollMasterID`
                AND `approve`.`approvalLevelID` = `t2`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` AS `ap` ON `ap`.`levelNo` = `t2`.`currentLevelNo`
            WHERE
                `approve`.`documentID` = 'SPN'
                AND `ap`.`documentID` = 'SPN'
                AND `ap`.`employeeID` = '{$userID}'
                AND `approve`.`approvedYN` = '0'
                AND `ap`.`companyID` = '{$companyID}'
                AND `t2`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'SR', $document_codes)) {/*** Purchase Return ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                stockReturnAutoID AS DocumentAutoID,
                srp_erp_stockreturnmaster.documentID AS DocumentID
            FROM
                `srp_erp_stockreturnmaster`
                JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stockreturnmaster`.`stockReturnAutoID`
                AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stockreturnmaster`.`currentLevelNo`
                JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stockreturnmaster`.`currentLevelNo`
            WHERE
                `srp_erp_documentapproved`.`documentID` = 'SR'
                AND `srp_erp_approvalusers`.`documentID` = 'SR'
                AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                AND `srp_erp_documentapproved`.`approvedYN` = '0'
                AND `srp_erp_stockreturnmaster`.`companyID` = '{$companyID}'
                AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'ST', $document_codes)) {/*** Stock Transfer ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                    stockTransferAutoID AS DocumentAutoID,
                    srp_erp_stocktransfermaster.documentID AS DocumentID
                FROM
                    `srp_erp_stocktransfermaster`
                    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_stocktransfermaster`.`stockTransferAutoID`
                    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_stocktransfermaster`.`currentLevelNo`
                    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_stocktransfermaster`.`currentLevelNo`
                WHERE
                    `srp_erp_documentapproved`.`documentID` = 'ST'
                    AND `srp_erp_approvalusers`.`documentID` = 'ST'
                    AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                    AND `srp_erp_documentapproved`.`approvedYN` = '0'
                    AND `srp_erp_stocktransfermaster`.`companyID` = '{$companyID}'
                    AND `srp_erp_approvalusers`.`companyID` = '{$companyID}'";
        }

        if(in_array( 'VD', $document_codes)) {/*** Variable Declaration ***/
            $query .= ($query != '')? ' UNION ': '';
            $query .= "SELECT
                        vpMasterID as DocumentAutoID,
                        decMas.documentID as DocumentID
                        FROM
                        `srp_erp_variablepaydeclarationmaster` AS `decMas`
                        JOIN `srp_erp_documentapproved` AS `appTB` ON `appTB`.`documentSystemCode` = `decMas`.`vpMasterID`
                        AND `appTB`.`approvalLevelID` = `decMas`.`currentLevelNo`
                        JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `decMas`.`currentLevelNo`
                        JOIN `srp_erp_currencymaster` `crMas` ON `decMas`.`trCurrencyID` = `crMas`.`currencyID`
                        LEFT JOIN srp_employeesdetails emp on emp.EIdNo  = decMas.confirmedByEmpID
                    WHERE
                        `appTB`.`documentID` = 'VD'
                        AND `srp_erp_approvalusers`.`documentID` = 'VD'
                        AND `srp_erp_approvalusers`.`employeeID` = '{$userID}'
                        AND `decMas`.`companyID` =  '{$companyID}'
                        AND `srp_erp_approvalusers`.`companyID` =  '{$companyID}'
                        AND `appTB`.`approvedYN` = ''";
        }


        if($query == ''){
            die( json_encode(0) );
        }

        $tot_count = $this->db->query("SELECT COUNT(tbl1.DocumentAutoID) AS tot FROM(  
                                             {$query}
	                                       ) AS tbl1 ")->row('tot');

        echo json_encode($tot_count);
    }
 }