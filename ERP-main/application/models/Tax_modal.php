<?php
// =============================================
// -  File Name : Tax_modal.php
// -  Project Name : MERP
// -  Module Name : Tax_modal
// -  Create date : 11 - September 2016
// -  Description : This file contains the add function for tax.

// - REVISION HISTORY
// -  =============================================

class Tax_modal extends ERP_Model
{

    function save_tax_header()
    {
        $this->db->trans_start();
        $supplier_arr = $this->fetch_authority_data(trim($this->input->post('supplierID') ?? ''));
        $liability = fetch_gl_account_desc(trim($this->input->post('supplierGLAutoID') ?? ''));


        if (($this->input->post('taxCategory')) == 2 && empty($this->input->post('taxMasterAutoID'))) {
            $companyID = current_companyID();
            $vatTypeExist = $this->db->query("SELECT 
                                              COUNT(taxMasterAutoID) as taxCount
                                              FROM
                                              `srp_erp_taxmaster`
                                              where 
                                              companyID =  $companyID
                                              AND taxCategory =2
                                              HAVING 
                                              taxCount >= 1")->row('taxCount');
            if ($vatTypeExist > 0) {
                $this->session->set_flashdata('e', 'Tax category type VAT already exists');
                return array('status' => false);
            }
        }

        $date_format_policy = date_format_policy();
        $expClaimDate = trim($this->input->post('effectiveFrom') ?? '');
        $isVat = trim($this->input->post('isVat') ?? '');
        $effectiveFrom = input_format_date($expClaimDate, $date_format_policy);
        $supplierCurrency = fetch_currency_code($supplier_arr['currencyID']);
        $supplierCurrencyDecimalPlaces = fetch_currency_desimal($supplierCurrency);
        $taxCategory = $this->input->post('taxCategory');
        $isClaimable = 0;
        if (!empty($this->input->post('isClaimable'))) {
            $isClaimable = 1;
        }

        $data['taxDescription'] = trim($this->input->post('taxDescription') ?? '');
        $data['taxShortCode'] = trim($this->input->post('taxShortCode') ?? '');

        $data['supplierAutoID'] = trim($this->input->post('supplierID') ?? '');
        $data['isActive'] = trim($this->input->post('isActive') ?? '');
        $data['effectiveFrom'] = $effectiveFrom;
        $data['taxCategory'] = $this->input->post('taxCategory');

        if ($taxCategory == 2) {

            $inputVatGL = fetch_gl_account_desc(trim($this->input->post('inputVatGLAccountAutoID') ?? ''));
            $data['inputVatGLAccountAutoID'] = $this->input->post('inputVatGLAccountAutoID');
            $data['inputVatGLAccount'] = $inputVatGL['masterAccount'];
            $data['taxType'] = 0;
            $inputVatTransferGL = fetch_gl_account_desc(trim($this->input->post('inputVatTransferGLAccountAutoID') ?? ''));
            $data['inputVatTransferGLAccountAutoID'] = $this->input->post('inputVatTransferGLAccountAutoID');
            $data['inputVatTransferGLAccount'] = $inputVatTransferGL['masterAccount'];


            $outputVatGL = fetch_gl_account_desc(trim($this->input->post('outputVatGLAccountAutoID') ?? ''));
            $data['outputVatGLAccountAutoID'] = $this->input->post('outputVatGLAccountAutoID');
            $data['outputVatGLAccount'] = $outputVatGL['masterAccount'];

            $outputVatTransferGL = fetch_gl_account_desc(trim($this->input->post('outputVatTransferGLAccountAutoID') ?? ''));
            $data['outputVatTransferGLAccountAutoID'] = $this->input->post('outputVatTransferGLAccountAutoID');
            $data['outputVatTransferGLAccount'] = $outputVatTransferGL['masterAccount'];

            $data['registrationNo'] = $this->input->post('taxRegistrationNo');
            $data['identificationNo'] = $this->input->post('taxIdentificationNo');
            $data['isVat'] = 1;

        } else {

            $data['isVat'] = $isVat;
            $data['taxType'] = trim($this->input->post('taxType') ?? '');
            
        }

        $data['taxReferenceNo'] = $this->input->post('taxReferenceNo');
        $data['isApplicableforTotal'] = trim($this->input->post('isApplicableforTotal') ?? '');
        $data['taxPercentage'] = trim($this->input->post('taxPercentage') ?? '');
        $data['supplierSystemCode'] = $supplier_arr['authoritySystemCode'];
        $data['supplierName'] = $supplier_arr['AuthorityName'];
        $data['supplierAddress'] = $supplier_arr['address'];
        $data['supplierTelephone'] = $supplier_arr['telephone'];
        $data['supplierFax'] = $supplier_arr['fax'];
        $data['supplierEmail'] = $supplier_arr['email'];
        $data['supplierGLAutoID'] = trim($this->input->post('supplierGLAutoID') ?? '');
        $data['supplierGLSystemGLCode'] = $liability['systemAccountCode'];
        $data['supplierGLAccount'] = $liability['masterAccount'];
        $data['supplierGLDescription'] = $liability['GLDescription'];
        $data['supplierGLType'] = $liability['subCategory'];
        $data['supplierCurrencyID'] = $supplier_arr['currencyID'];
        $data['supplierCurrency'] = $supplierCurrency;
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrencyDecimalPlaces;
        $data['isClaimable'] = $isClaimable;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxMasterAutoID') ?? '')) {


            if (($this->input->post('taxCategory')) == 2) {
                $companyID = current_companyID();
                $taxMasterAutoID = $this->input->post('taxMasterAutoID');
                $vatTypeExist = $this->db->query("SELECT
                                                  COUNT(taxMasterAutoID) as taxCount
                                                  FROM
                                                  `srp_erp_taxmaster`
                                                  where 
                                                  companyID =  $companyID
                                                  AND taxMasterAutoID!=  $taxMasterAutoID 
                                                  AND taxCategory =2
                                                  HAVING 
                                                  taxCount >= 1")->row('taxCount');
                if ($vatTypeExist > 0) {
                    $this->session->set_flashdata('e', 'Tax category type VAT already exists');
                    return array('status' => false);
                }
            }


            $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID') ?? ''));
            $this->db->update('srp_erp_taxmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('TaxAutoID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_taxmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax for : (' . $data['supplierSystemCode'] . ' ) ' . $data['supplierName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
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

    function laad_tax_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(effectiveFrom,\'' . $convertFormat . '\') AS effectiveFrom');
        $this->db->from('srp_erp_taxmaster');
        $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_tax()
    {
        $this->db->delete('srp_erp_taxmaster', array('taxMasterAutoID' => trim($this->input->post('taxMasterAutoID') ?? '')));
        $this->db->delete('srp_erp_taxapplicableitems', array('taxMasterAutoID' => trim($this->input->post('taxMasterAutoID') ?? '')));
        $this->session->set_flashdata('e', 'Tax Deleted : ' . $this->input->post('value') . ' Successfully');
        return true;
    }

    function save_tax_group_header()
    {
        $this->db->trans_start();
        $data['taxType'] = trim($this->input->post('taxDescription') ?? '');
        $data['taxType'] = trim($this->input->post('taxgroup') ?? '');
        $data['Description'] = trim($this->input->post('taxdescription') ?? '');

        if (trim($this->input->post('taxGroupID_Edit') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('taxGroupID', trim($this->input->post('taxGroupID_Edit') ?? ''));
            $this->db->update('srp_erp_taxgroup', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Group Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Group Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('TaxAutoID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_taxgroup', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Tax Group Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Tax Group Created successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function get_tax_group_edit()
    {
        $this->db->select('*');
        $this->db->where('taxGroupID', $this->input->post('id'));
        return $this->db->get('srp_erp_taxgroup')->row_array();
    }

    function changesupplierGLAutoID()
    {
        $this->db->select('taxPayableGLAutoID');
        $this->db->from('srp_erp_taxauthorithymaster');
        $this->db->where('taxAuthourityMasterID', trim($this->input->post('supplierID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_authority_data($taxAuthourityMasterID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_taxauthorithymaster');
        $this->db->where('taxAuthourityMasterID', $taxAuthourityMasterID);
        return $this->db->get()->row_array();
    }

    function get_tax_type($taxType)
    {
        $qry = "SELECT
  taxMasterAutoID,
	taxShortCode,
	taxType,
	IF(taxType = 1,'Sales tax','Purchase tax') as taxTyp
FROM
	srp_erp_taxmaster
WHERE
	companyID = " . current_companyID() . "
AND taxMasterAutoID IN (" . join(',', $taxType) . ") ORDER BY  taxType DESC";
        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function get_tax_details($taxType, $datefrom, $dateto)
    {
        $date_format_policy = date_format_policy();
        $fromdate = input_format_date($datefrom, $date_format_policy);
        $date_format_policy = date_format_policy();
        $todate = input_format_date($dateto, $date_format_policy);
        $local = '';
        $reporting = '';
        foreach ($taxType as $tex) {
            $local .= 'SUM(if(ledger.taxMasterAutoID = ' . $tex . ',ledger.companyLocalAmount,0)) as L_' . $tex . ',';
            $reporting .= 'SUM(if(ledger.taxMasterAutoID = ' . $tex . ',ledger.companyReportingAmount,0)) as R_' . $tex . ',';
        }

        $qry = "SELECT
   gl.*, sum(ifnull(cinvD.companyLocalAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyLocalExchangeRate,1)) AS cinvlocal,
    sum(ifnull(cinvD.companyReportingAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyReportingExchangeRate,1)) AS cinvReporting,
    sum(ifnull(rvd.companyLocalAmount,0)) AS rvlocal,
    sum(ifnull(rvd.companyReportingAmount,0)) AS rvreporting,
    sum(ifnull(bsid.companyLocalAmount,0)) AS bsilocal,
    sum(ifnull(bsid.companyReportingAmount,0)) AS bsireporting,
    sum(ifnull(pvd.companyLocalAmount,0)) AS pvlocal,
    sum(ifnull(pvd.companyReportingAmount,0)) AS pvReporting,
  ((sum(ifnull(cinvD.companyLocalAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyLocalExchangeRate,1)) )+
  (sum(ifnull(rvd.companyLocalAmount,0)))+
    (sum(ifnull(bsid.companyLocalAmount,0)))+
    (sum(ifnull(pvd.companyLocalAmount,0)))
    ) AS totalgrossofTaxLocal,
((sum(ifnull(cinvD.companyReportingAmount,0)) - sum(ifnull(cinvD.totalAfterTax,0) / ifnull(cinvm.companyReportingExchangeRate,1)))+
    (sum(ifnull(rvd.companyReportingAmount,0)))+
    (sum(ifnull(bsid.companyReportingAmount,0)))+
    (sum(ifnull(pvd.companyReportingAmount,0)))) as totalGrossofTaxReporting,
    srp_erp_taxmaster.taxType as taxType,
    case WHEN gl.documentCode='PV' THEN pvm.PVNarration
   WHEN gl.documentcode='CINV' THEN cinvm.invoiceNarration
   WHEN gl.documentcode='rv' THEN rv.RVNarration
else bsi.comments END as narration,
case when gl.documentCode='BSI' THEN bsiSupplier.supplierName
     when gl.documentCode='CINV' THEN cinvCustomer.customername
     when gl.documentCode='PV' AND pvmSupplier.supplierName is not null THEN pvmSupplier.supplierName
     when gl.documentcode='RV' AND rvCustomer.customerName is not null  THEN rvCustomer.customerName
     when gl.documentCode='PV' AND pvmSupplier.supplierName is null THEN pvm.partyName
     when gl.documentcode='RV' AND rvCustomer.customerName is null  THEN rv.customerName
Else null END as SupplierName
FROM
    (
        SELECT
            $local
            $reporting
            ledger.documentCode,
            ledger.documentMasterAutoID,
            ledger.documentSystemCode,
            ledger.documentDate,
            ledger.partyVatIdNo,ledger.taxMasterAutoID,
            ledger.companyLocalCurrencyDecimalPlaces,
      ledger.companyReportingCurrencyDecimalPlaces
        FROM
            srp_erp_generalledger ledger
        WHERE
            ledger.companyID = " . current_companyID() . "
        AND ledger.taxMasterAutoID IS NOT NULL
        AND ledger.documentcode IN ('CINV', 'RV', 'PV', 'BSI')
        AND (
            ledger.Documentdate BETWEEN '$fromdate'
            AND '$todate'
        )
        GROUP BY
            ledger.documentMasterAutoID,ledger.documentCode
    ) gl
        LEFT JOIN srp_erp_customerinvoicedetails cinvD ON cinvD.invoiceAutoID = gl.documentMasterAutoID
        AND gl.documentCode = 'CINV'
        LEFT JOIN srp_erp_customerinvoicemaster cinvm ON cinvm.invoiceAutoID = cinvD.invoiceAutoID
        LEFT JOIN (select * from srp_erp_customermaster where companyID=" . current_companyID() . ")cinvCustomer on cinvCustomer.customerAutoID=cinvm.customerID
        LEFT JOIN srp_erp_paymentvoucherdetail pvd ON pvd.payVoucherAutoId = gl.documentMasterAutoID
        AND gl.documentCode = 'PV'
        LEFT JOIN srp_erp_paymentvouchermaster pvm on pvd.payVoucherAutoId=pvm.payVoucherAutoId
        LEFT JOIN (select * from srp_erp_suppliermaster where companyID=" . current_companyID() . ") pvmSupplier on pvmSupplier.supplierAutoID=pvm.partyID
        LEFT JOIN srp_erp_paysupplierinvoicedetail bsid ON bsid.InvoiceAutoID = gl.documentMasterAutoID
        AND gl.documentCode = 'BSI'
        LEFT JOIN srp_erp_paysupplierinvoicemaster bsi on bsid.InvoiceAutoID=bsi.InvoiceAutoID
        LEFT JOIN (select * from srp_erp_suppliermaster where companyID=" . current_companyID() . ") bsiSupplier on bsiSupplier.supplierAutoID=bsi.supplierID
        LEFT JOIN srp_erp_customerreceiptdetail rvd ON rvd.receiptVoucherAutoId = gl.documentMasterAutoID
        AND gl.documentCode = 'RV'
        LEFT JOIN srp_erp_customerreceiptmaster rv on rvd.receiptVoucherAutoId=rv.receiptVoucherAutoId
        LEFT JOIN (select * from srp_erp_customermaster where companyID=" . current_companyID() . ")rvCustomer on rvCustomer.customerAutoID=rv.customerID
        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID=gl.taxMasterAutoID
        where gl.taxMasterAutoID IN (" . join(',', $taxType) . ")
        group by gl.documentCode,gl.documentMasterAutoID";
        $output = $this->db->query($qry)->result_array();

        // print_r($this->db->last_query()); exit;

        return $output;
    }

    function save_vat_main_category()
    {
        $data['taxMasterAutoID'] = trim($this->input->post('taxMasterAutoID') ?? '');
        $data['mainCategoryDescription'] = trim($this->input->post('mainCategoryDesc') ?? '');
        $data['isActive'] = trim($this->input->post('isActive') ?? '');

        if (trim($this->input->post('taxVatMainCategoriesAutoID_cat') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];

            $this->db->where('taxVatMainCategoriesAutoID', trim($this->input->post('taxVatMainCategoriesAutoID_cat') ?? ''));
            $this->db->update('srp_erp_tax_vat_main_categories', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Failed to Update VAT Main Category ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'VAT Main Category Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxVatMainCategoriesAutoID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_tax_vat_main_categories', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Failed to Add VAT Main Category ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'VAT Main Category Added Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_VAT_main_category()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_tax_vat_main_categories');
        $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID') ?? ''));
        return $this->db->get()->result_array();
    }

    function load_vat_main_category()
    {
        $this->db->select('*, IFNULL(isActive, 0) as isActive');
        $this->db->from('srp_erp_tax_vat_main_categories');
        $this->db->where('taxVatMainCategoriesAutoID', trim($this->input->post('taxVatMainCategoriesAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_vat_main_category()
    {
        $this->db->delete('srp_erp_tax_vat_main_categories', array('taxVatMainCategoriesAutoID' => trim($this->input->post('taxVatMainCategoriesAutoID') ?? '')));
        $this->db->delete('srp_erp_tax_vat_sub_categories', array('mainCategory' => trim($this->input->post('taxVatMainCategoriesAutoID') ?? '')));
        $this->session->set_flashdata('e', 'VAT Main Category Deleted Successfully');
        return true;
    }

    function load_main_category_dropdown()
    {
        $this->db->select('taxVatMainCategoriesAutoID, mainCategoryDescription');
        $this->db->from('srp_erp_tax_vat_main_categories');
        $this->db->where('taxMasterAutoID', trim($this->input->post('taxMasterAutoID') ?? ''));
        return $this->db->get()->result_array();
    }

    function save_vat_sub_category()
    {
        $data['taxMasterAutoID'] = trim($this->input->post('taxMasterAutoID') ?? '');
        $data['mainCategory'] = trim($this->input->post('mainCategoryVAT') ?? '');
        $data['subCategoryDescription'] = trim($this->input->post('subCategoryDesc') ?? '');
        $data['percentage'] = trim($this->input->post('subPercentage') ?? '');
        $data['applicableOn'] = trim($this->input->post('applicableOn') ?? '');
        $data['isActive'] = trim($this->input->post('isActive') ?? '');

        if (trim($this->input->post('taxVatSubCategoriesAutoID') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];

            $this->db->where('taxVatSubCategoriesAutoID', trim($this->input->post('taxVatSubCategoriesAutoID') ?? ''));
            $this->db->update('srp_erp_tax_vat_sub_categories', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Failed to Update VAT Sub Category ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'VAT Sub Category Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('taxVatSubCategoriesAutoID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_tax_vat_sub_categories', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Failed to Add VAT Sub Category ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'VAT Sub Category Added Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_VAT_sub_category()
    {
        $this->db->select('srp_erp_tax_vat_sub_categories.*, mainCategoryDescription');
        $this->db->from('srp_erp_tax_vat_sub_categories');
        $this->db->from('srp_erp_tax_vat_main_categories', 'srp_erp_tax_vat_main_categories.taxVatMainCategoriesAutoID = srp_erp_tax_vat_sub_categories.mainCategory', 'left');
        $this->db->where('srp_erp_tax_vat_sub_categories.taxMasterAutoID', trim($this->input->post('taxMasterAutoID') ?? ''));
        return $this->db->get()->result_array();
    }

    function load_vat_sub_category()
    {
        $this->db->select('*, IFNULL(isActive, 0) as isActive');
        $this->db->from('srp_erp_tax_vat_sub_categories');
        $this->db->where('taxVatSubCategoriesAutoID', trim($this->input->post('taxVatSubCategoriesAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function delete_vat_sub_category()
    {
        $this->db->delete('srp_erp_tax_vat_sub_categories', array('taxVatSubCategoriesAutoID' => trim($this->input->post('taxVatSubCategoriesAutoID') ?? '')));
        $this->session->set_flashdata('e', 'VAT Sub Category Deleted Successfully');
        return true;
    }

    function assign_item_VAT()
    {
        $selectedItemsSync = $this->input->post('selectedItemsSync');
        $data['taxVatSubCategoriesAutoID'] = trim($this->input->post('taxVatSubCategoriesAutoID') ?? '');

        $this->db->trans_start();
        foreach ($selectedItemsSync as $itemAutoID) {
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->update('srp_erp_itemmaster', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Failed to add items for VAT Sub Category ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'items Added for VAT Sub Category Successfully!');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function load_output_vat_summary_report()
    {
        $qry = array();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $datetoconvert .= " 23:59:59";
        $currency = $this->input->post('currency');
        $taxType = $this->input->post('taxType');
        $accountType = $this->input->post('accountType');
        $vatType = implode(',', $taxType);
        $documentType = $this->input->post('documentType');
        $customerID = $this->input->post('customerAutoID');
        $taxCategory = $this->input->post('taxCategory');
        $customerAutoID = implode(',', $customerID);

        if ($currency == 1) {
            $currencyDecimalPlace = 'companyLocalCurrencyDecimalPlaces';
            $currencyExchangeRate = 'companyLocalExchangeRate';
        } else {
            $currencyDecimalPlace = 'companyReportingCurrencyDecimalPlaces';
            $currencyExchangeRate = 'companyReportingExchangeRate';
        }

        $taxCategory = '('.implode(',', $taxCategory).')';

        $companyID = current_companyID();

        if (in_array('CINV', $documentType)) {
            $where_account = '';
            $negativeAmount = '';
            if(in_array('2', $accountType) && !in_array('1', $accountType)) {
                $where_account = "AND type = 'DO'";
                $negativeAmount = " * -1";
            }

            $qry[] = "SELECT
                        'Customer Invoice' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        invoiceCode AS documentCode,
                        invoiceDate AS documentDate,
                        customerID AS partyID,
                        srp_erp_customerinvoicemaster.customerName AS partyName,
                        approvedbyEmpName AS approvedEmp,                        
                        SUM(amount/$currencyExchangeRate) AS vatAmount,
                        srp_erp_customerinvoicemaster.transactionAmount/$currencyExchangeRate AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        srp_erp_customerinvoicemaster.invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = srp_erp_customerinvoicemaster.documentID
                        JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicedetails.invoiceDetailsAutoID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'CINV'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customerinvoicemaster.customerID = srp_erp_customermaster.customerAutoID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND approvedYN = 1
                        AND isVat = 1
                        $where_account
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('RET', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Sales Return' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        salesReturnCode AS documentCode,
                        returnDate AS documentDate,
                        customerID AS partyID,
                        srp_erp_salesreturnmaster.customerName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM( amount / $currencyExchangeRate ) * -1 AS vatAmount,
                        (salesAmount / $currencyExchangeRate) * -1 AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        srp_erp_salesreturnmaster.comment  AS invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_salesreturnmaster ON srp_erp_salesreturnmaster.salesReturnAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_salesreturnmaster.documentID
                        LEFT JOIN ( SELECT SUM( totalValue + taxAmount ) AS salesAmount, salesReturnDetailsID FROM srp_erp_salesreturndetails GROUP BY salesReturnAutoID ) detailTbl ON detailTbl.salesReturnDetailsID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'SLR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_customermaster ON srp_erp_salesreturnmaster.customerID = srp_erp_customermaster.customerAutoID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND isVat = 1
                        AND approvedYN = 1 
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('DO', $documentType) && in_array('2', $accountType)) {
            $qry[] = "SELECT
                        'Delivery Order' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        DOCode AS documentCode,
                        DODate AS documentDate,
                        customerID AS partyID,
                        srp_erp_deliveryorder.customerName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM(amount/$currencyExchangeRate) * -1 AS vatAmount,
                        deliveredTransactionAmount/$currencyExchangeRate AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        srp_erp_deliveryorder.narration AS invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = srp_erp_deliveryorder.documentID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_customermaster ON srp_erp_deliveryorder.customerID = srp_erp_customermaster.customerAutoID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory 
                        AND isVat = 1
                        AND approvedYN = 1
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('CN', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Credit Note' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        creditNoteCode AS documentCode,
                        creditNoteDate AS documentDate,
                        srp_erp_creditnotemaster.customerID AS partyID,
                        srp_erp_creditnotemaster.customerName AS partyName,
                        srp_erp_creditnotemaster.approvedbyEmpName AS approvedEmp,
                        SUM((amount/srp_erp_creditnotemaster.$currencyExchangeRate) * -1) AS vatAmount,
                        ((det.transactionAmount/srp_erp_creditnotemaster.$currencyExchangeRate) * -1) AS documentAmount,
                        srp_erp_creditnotemaster.$currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        srp_erp_creditnotedetail.invoiceSystemCode,
                        srp_erp_customerinvoicemaster.invoiceDate,
                        srp_erp_customerinvoicemaster.invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_creditnotemaster.documentID
                        LEFT JOIN (SELECT SUM(transactionAmount) as transactionAmount,creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det ON det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_creditnotemaster.customerID = srp_erp_customermaster.customerAutoID 
                        LEFT JOIN srp_erp_creditnotedetail ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_creditnotedetail.creditNoteMasterAutoID 
                        INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_creditnotedetail.invoiceAutoID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND isVat = 1
                        AND srp_erp_creditnotemaster.approvedYN = 1
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('RV', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Receipt Voucher' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        RVcode AS documentCode,
                        RVdate AS documentDate,
                        customerID AS partyID,
                        srp_erp_customerreceiptmaster.customerName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM(amount/$currencyExchangeRate) AS vatAmount,
                        transactionAmount/$currencyExchangeRate AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        srp_erp_customerreceiptmaster.RVNarration AS invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_customerreceiptmaster.documentID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customerreceiptmaster.customerID = srp_erp_customermaster.customerAutoID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND isVat = 1
                        AND approvedYN = 1
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('POS', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                            'General POS' AS documentType,
                            documentMasterAutoID,
                            srp_erp_taxledger.documentID,
                            documentSystemCode AS documentCode,
                            invoiceDate AS documentDate,
                            customerID AS partyID,
                            IFNULL( srp_erp_customermaster.customerName, 'Cash Sale' ) AS partyName,
                            null AS approvedEmp,
                            SUM( amount / $currencyExchangeRate ) AS vatAmount,
                            netTotal / $currencyExchangeRate AS documentAmount,
                            $currencyDecimalPlace AS decimalPlace,
                            srp_erp_tax_vat_type.Description AS vatType,
                            srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                            isClaimed,
                            srp_erp_customermaster.vatIdNo,
                            null AS invoiceSystemCode,
                            null AS invoiceDate,
                            null AS invoiceNarration
                        FROM
                            srp_erp_taxledger
                            INNER JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.invoiceID = srp_erp_taxledger.documentMasterAutoID 
                            AND srp_erp_taxledger.documentID = 'GPOS'
                            LEFT JOIN srp_erp_customermaster ON srp_erp_pos_invoice.customerID = srp_erp_customermaster.customerAutoID
                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                            LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        WHERE
                            srp_erp_taxledger.companyID = {$companyID}
                            AND taxCategory in  $taxCategory
                            AND isVat = 1
                            AND isVoid != 1
                        GROUP BY
                            srp_erp_taxledger.documentID,
                            srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('RPOS', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Restaurant POS' AS documentType,
                        IF(isCreditSales=1,srp_erp_pos_menusalesmaster.documentMasterAutoID,srp_erp_pos_menusalesmaster.menuSalesID) AS documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        IF(isCreditSales=1,documentSystemCode,CONCAT('POSR/',srp_erp_warehousemaster.wareHouseCode,'/',srp_erp_pos_shiftdetails.shiftID)) AS documentCode,
                        srp_erp_pos_menusalesmaster.createdDateTime AS documentDate,
                        customerID AS partyID,
                        IFNULL( srp_erp_pos_menusalesmaster.customerName, 'Cash Sale') AS partyName,
                        null AS approvedEmp,
                        amount / srp_erp_pos_menusalesmaster.$currencyExchangeRate AS vatAmount,
                        SUM(paidAmount / srp_erp_pos_menusalesmaster.$currencyExchangeRate) AS documentAmount,
                        srp_erp_pos_menusalesmaster.$currencyDecimalPlace AS decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxledger.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        null AS invoiceNarration 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = 'POSR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxledger.vatTypeID 
                        LEFT JOIN srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_taxledger.documentMasterAutoID
                        LEFT JOIN srp_erp_warehousemaster on srp_erp_warehousemaster.wareHouseAutoID=srp_erp_pos_shiftdetails.wareHouseID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalesmaster.customerID = srp_erp_customermaster.customerAutoID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND isVat = 1
                        AND NOT EXISTS (SELECT taxLedgerAutoID FROM srp_erp_taxledger WHERE documentID = 'POSR' AND srp_erp_taxledger.documentMasterAutoID = srp_erp_pos_menusalesmaster.menuSalesID)
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        //credit sale.
        if (in_array('RPOS', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Restaurant POS' AS documentType,
                        IF(isCreditSales=1,srp_erp_pos_menusalesmaster.documentMasterAutoID,srp_erp_pos_menusalesmaster.menuSalesID) AS documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        IF(isCreditSales=1,documentSystemCode,CONCAT('POSR/',srp_erp_warehousemaster.wareHouseCode,'/',srp_erp_pos_shiftdetails.shiftID)) AS documentCode,
                        srp_erp_pos_menusalesmaster.createdDateTime AS documentDate,
                        customerID AS partyID,
                        IFNULL( srp_erp_pos_menusalesmaster.customerName, 'Cash Sale') AS partyName,
                        null AS approvedEmp,
                        amount / srp_erp_pos_menusalesmaster.$currencyExchangeRate AS vatAmount,
                        SUM(paidAmount / srp_erp_pos_menusalesmaster.$currencyExchangeRate) AS documentAmount,
                        srp_erp_pos_menusalesmaster.$currencyDecimalPlace AS decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxledger.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_customermaster.vatIdNo,
                        null AS invoiceSystemCode,
                        null AS invoiceDate,
                        null AS invoiceNarration
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalesmaster.menuSalesID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = 'POSR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxledger.vatTypeID 
                        LEFT JOIN srp_erp_pos_shiftdetails on srp_erp_pos_shiftdetails.shiftID=srp_erp_taxledger.documentMasterAutoID
                        LEFT JOIN srp_erp_warehousemaster on srp_erp_warehousemaster.wareHouseAutoID=srp_erp_pos_shiftdetails.wareHouseID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_menusalesmaster.customerID = srp_erp_customermaster.customerAutoID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory in  $taxCategory
                        AND isVat = 1
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('RET', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                            'Sales Return' AS documentType,
                            documentMasterAutoID,
                            srp_erp_taxledger.documentID,
                            documentSystemCode AS documentCode,
                            salesReturnDate AS documentDate,
                            customerID AS partyID,
                            null AS partyName,
                            null AS approvedEmp,
                            SUM( amount / $currencyExchangeRate ) * -1 AS vatAmount,
                            refundAmount / $currencyExchangeRate * -1 AS documentAmount,
                            $currencyDecimalPlace AS decimalPlace,
                            srp_erp_tax_vat_type.Description AS vatType,
                            srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                            isClaimed,
                            srp_erp_customermaster.vatIdNo,
                            null AS invoiceSystemCode,
                            null AS invoiceDate,
                            null AS invoiceNarration
                        FROM
                            srp_erp_taxledger
                            INNER JOIN srp_erp_pos_salesreturn ON srp_erp_pos_salesreturn.salesReturnID = srp_erp_taxledger.documentMasterAutoID 
                            AND srp_erp_taxledger.documentID = 'RET'
                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                            LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                            LEFT JOIN srp_erp_customermaster ON srp_erp_pos_salesreturn.customerID = srp_erp_customermaster.customerAutoID 
                        WHERE
                            srp_erp_taxledger.companyID = {$companyID}
                            AND taxCategory in  $taxCategory
                            AND isVat = 1	
                        GROUP BY
                            srp_erp_taxledger.documentID,
                            srp_erp_taxledger.documentMasterAutoID";
        }

        $result = '';
        if(!empty($qry)) {
            $unionQry = implode(' UNION ALL ', $qry);
            $result = $this->db->query("SELECT * FROM 
                                            ($unionQry)tbl 
                                        WHERE
                                            (partyID IN ({$customerAutoID}) OR partyID IS NULL)
                                            AND DATE(documentDate) BETWEEN '{$datefromconvert}' AND '{$datetoconvert}'
                                            AND vatTypeID IN ({$vatType})")->result_array();
        }
       
        return $result;
    }

    function fetch_output_vat_summary_report_excel()
    {
        $detail = array();
        $currency = $this->input->post('currency');
        if ($currency == 1) {
            $detail['currency'] = $this->common_data['company_data']['company_default_currency'];
        } else {
            $detail['currency'] = $this->common_data['company_data']['company_reporting_currency'];
        }
        $detail['documents'] = implode(',', $this->input->post('documentType'));

        $result = $this->load_output_vat_summary_report();
        $detail['customer'] = implode(',', array_unique(array_column($result, 'partyName')));
        $detail['vatType'] = implode(',', array_unique(array_column($result, 'vatType')));
        $detail['data'] = array();
        if(!empty($result)) 
        {
            $a = 1;
            $totalAmount = $totalVat = 0;
            foreach ($result as $val) 
            {
                if($val['approvedEmp'] == 1) {
                    $claimed = 'YES';
                } else {
                    $claimed = 'NO';
                }
                if(empty($val['approvedEmp'])) { $val['approvedEmp'] = ''; }
                $detail['data'][] = array( 
                    'Num' => $a,
                    'DocumentCode' => $val['documentCode'],
                    'documentType' => $val['documentType'],
                    'DocumentDate' => $val['documentDate'],
                    'invoiceSystemCode' => $val['invoiceSystemCode'],
                    'invoiceDate' => $val['invoiceDate'],
                    'vatIdNo' => $val['vatIdNo'],
                    'Customer' => $val['partyName'],
                    'Description' => $val['invoiceNarration'],                    
                    'VATtype' => $val['vatType'],
                    'VATClaimed' => $claimed, 
                    'ApprovedBy' => $val['approvedEmp'],
                    'TotalAmount' => number_format($val['documentAmount'], $val['decimalPlace'], '.', ''),
                    'VATAmount' => number_format($val['vatAmount'], $val['decimalPlace'], '.', '')
                );
                $totalAmount += $val['documentAmount'];
                $totalVat += $val['vatAmount'];
                $decimalPlaces = $val['decimalPlace'];
                $a++;
            }
            $detail['data'][] = array( 
                'Num' => '',
                'DocumentCode' => 'Total',
                'documentType' => '',
                'DocumentDate' => '',
                'invoiceSystemCode' => '',
                'invoiceDate' => '',
                'vatIdNo' => '',
                'Customer' => '',
                'Description' => '', 
                'VATtype' => '',
                'VATClaimed' => '',
                'ApprovedBy' => '',
                'TotalAmount' => number_format($totalAmount, $decimalPlaces, '.', ''),
                'VATAmount' => number_format($totalVat, $decimalPlaces, '.', '')
            );

        }
        return $detail;
    }

    function load_input_vat_summary_report()
    {
        $qry = array();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $currency = $this->input->post('currency');
        $taxType = $this->input->post('taxType');
        $accountType = $this->input->post('accountType');
        $view = "AND srp_erp_taxledger.rcmApplicableYN = 0";
        if($this->input->post('viewRCMapplied')) {
            $view = "";
        }
        $vatType = implode(',', $taxType);
        $documentType = $this->input->post('documentType');
        $supplierID = $this->input->post('supplierAutoID');
        $supplierAutoID = implode(',', $supplierID);
        $taxCategory_h = array(1,2);

        if ($currency == 1) {
            $currencyDecimalPlace = 'companyLocalCurrencyDecimalPlaces';
            $currencyExchangeRate = 'companyLocalExchangeRate';
        } else {
            $currencyDecimalPlace = 'companyReportingCurrencyDecimalPlaces';
            $currencyExchangeRate = 'companyReportingExchangeRate';
        }
        $companyID = current_companyID();

        if (in_array('BSI', $documentType)) {
            $where_account = '';
            $negativeAmount = '';
            if(in_array('2', $accountType) && !in_array('1', $accountType)) {
                $where_account = "AND grvType = 'GRV Base'";
                $negativeAmount = " * -1";
            }

            $qry[] = "SELECT
                        'Supplier Invoice' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        bookingInvCode AS documentCode,
                        bookingDate AS documentDate,
                        srp_erp_paysupplierinvoicemaster.supplierID AS partyID,
                        srp_erp_paysupplierinvoicemaster.supplierName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM(amount/srp_erp_paysupplierinvoicemaster.{$currencyExchangeRate}) {$negativeAmount} AS vatAmount,
                        srp_erp_paysupplierinvoicemaster.transactionAmount/srp_erp_paysupplierinvoicemaster.{$currencyExchangeRate} AS documentAmount,
                        srp_erp_paysupplierinvoicemaster.{$currencyDecimalPlace} as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_suppliermaster.vatIdNo,
                        srp_erp_paysupplierinvoicemaster.supplierInvoiceNo AS bookingInvCode,
                        srp_erp_paysupplierinvoicemaster.invoiceDate, 
                        srp_erp_paysupplierinvoicemaster.comments
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_paysupplierinvoicemaster.documentID
                        JOIN srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'BSI'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_paysupplierinvoicemaster.supplierID = srp_erp_suppliermaster.supplierAutoID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND srp_erp_taxmaster.isVat = 1
                        AND approvedYN = 1
                        $where_account
                        $view
                    GROUP BY 
                        srp_erp_taxledger.documentID,
                        documentMasterAutoID";
        }

        if (in_array('PV', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Payment Voucher' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        PVcode AS documentCode,
                        PVdate AS documentDate,
                        srp_erp_paymentvouchermaster.partyID AS partyID,
                        partyName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM(amount/$currencyExchangeRate) AS vatAmount,
                        (((IFNULL( addondet.taxPercentage, 0 )/ 100)* IFNULL( tyepdet.transactionAmount, 0 ))+ IFNULL( det.transactionAmount, 0 )- IFNULL( debitnote.transactionAmount, 0 )- IFNULL( SR.transactionAmount, 0 ))/$currencyExchangeRate AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        null AS vatIdNo,
                        null AS bookingInvCode,
                        null AS invoiceDate,
                        srp_erp_paymentvouchermaster.PVNarration AS comments
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_paymentvouchermaster.documentID
                        LEFT JOIN ( SELECT SUM(transactionAmount + IFNULL( taxAmount, 0 )) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type != 'debitnote' AND srp_erp_paymentvoucherdetail.type != 'SR' GROUP BY payVoucherAutoId ) det ON det.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                        LEFT JOIN (SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = 'GL' OR srp_erp_paymentvoucherdetail.type = 'Item' OR srp_erp_paymentvoucherdetail.type = 'PRQ' GROUP BY payVoucherAutoId) tyepdet ON tyepdet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = 'SR' GROUP BY payVoucherAutoId ) SR ON SR.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                        LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, payVoucherAutoId FROM srp_erp_paymentvoucherdetail WHERE srp_erp_paymentvoucherdetail.type = 'debitnote' GROUP BY payVoucherAutoId ) debitnote ON debitnote.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, payVoucherAutoId FROM srp_erp_paymentvouchertaxdetails GROUP BY payVoucherAutoId ) addondet ON addondet.payVoucherAutoId = srp_erp_paymentvouchermaster.payVoucherAutoId
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND srp_erp_taxmaster.isVat = 1
                        AND approvedYN = 1
                    GROUP BY 
                        srp_erp_taxledger.documentID,
                        documentMasterAutoID";
        }

        if (in_array('DN', $documentType) && in_array('1', $accountType)) {
            $qry[] = "SELECT
                        'Debit Note' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        debitNoteCode AS documentCode,
                        debitNoteDate AS documentDate,
                        srp_erp_debitnotemaster.supplierID AS partyID,
                        srp_erp_debitnotemaster.supplierName AS partyName,
                        srp_erp_debitnotemaster.approvedbyEmpName AS approvedEmp,
                        ((SUM(amount /srp_erp_debitnotemaster.$currencyExchangeRate)) * -1) AS vatAmount,
                        ((det.transactionAmount /srp_erp_debitnotemaster.$currencyExchangeRate) * -1) AS documentAmount,
                        srp_erp_debitnotemaster.$currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_suppliermaster.vatIdNo,
                        srp_erp_debitnotedetail.bookingInvCode,
                        srp_erp_paysupplierinvoicemaster.invoiceDate,
                        srp_erp_paysupplierinvoicemaster.comments	
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_debitnotemaster ON srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_debitnotemaster.documentID
                        LEFT JOIN (SELECT (SUM( transactionAmount )+ SUM( if(InvoiceAutoID!= '',0,taxAmount))) as transactionAmount,debitNoteMasterAutoID FROM srp_erp_debitnotedetail GROUP BY debitNoteMasterAutoID) det ON det.debitNoteMasterAutoID = srp_erp_debitnotemaster.debitNoteMasterAutoID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_debitnotemaster.supplierID = srp_erp_suppliermaster.supplierAutoID
                        LEFT JOIN srp_erp_debitnotedetail ON srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_debitnotedetail.debitNoteMasterAutoID
                        INNER JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = srp_erp_debitnotedetail.invoiceAutoID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND srp_erp_taxmaster.isVat = 1 
                        AND srp_erp_debitnotemaster.approvedYN = 1
                    GROUP BY 
                        srp_erp_taxledger.documentID,
                        documentMasterAutoID";
        }

        if (in_array('GRV', $documentType) && in_array('2', $accountType)) {
            $qry[] = "SELECT
                        'Goods Received Voucher' AS documentType,
                        documentMasterAutoID,
                        'GRV' AS documentID,
                        grvPrimaryCode AS documentCode,
                        grvDate AS documentDate,
                        supplierID AS partyID,
                        srp_erp_grvmaster.supplierName AS partyName,
                        approvedbyEmpName AS approvedEmp,
                        SUM(amount/$currencyExchangeRate) AS vatAmount,
                        CASE
                            WHEN srp_erp_taxledger.documentID = 'GRV' THEN ((IFNULL(det.receivedTotalAmount, 0) + IFNULL(taxAmount, 0))/$currencyExchangeRate)
                            WHEN srp_erp_taxledger.documentID = 'GRV-ADD' THEN ((IFNULL(addondet.total_amount, 0) + IFNULL(addontaxAmount, 0))/$currencyExchangeRate) 
                        END AS documentAmount,
                        $currencyDecimalPlace as decimalPlace,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        isClaimed,
                        srp_erp_suppliermaster.vatIdNo,
                        null AS bookingInvCode,
                        null AS invoiceDate,
                        srp_erp_grvmaster.grvNarration AS comments 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND (srp_erp_taxledger.documentID = srp_erp_grvmaster.documentID  OR srp_erp_taxledger.documentID = 'GRV-ADD')
                        LEFT JOIN ( SELECT SUM( receivedTotalAmount ) AS receivedTotalAmount, SUM( taxAmount ) AS taxAmount, grvAutoID FROM srp_erp_grvdetails GROUP BY grvAutoID ) det ON det.grvAutoID = srp_erp_grvmaster.grvAutoID
                        LEFT JOIN ( SELECT SUM( total_amount ) AS total_amount, SUM( taxAmount / transactionExchangeRate ) AS addontaxAmount, grvAutoID FROM srp_erp_grv_addon GROUP BY grvAutoID ) addondet ON addondet.grvAutoID = srp_erp_grvmaster.grvAutoID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_grvmaster.supplierID = srp_erp_suppliermaster.supplierAutoID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND srp_erp_taxmaster.isVat = 1
                        AND approvedYN = 1
                        $view
                    GROUP BY 
                        srp_erp_taxledger.documentID,
                        documentMasterAutoID";
        }

        $result = '';
        if(!empty($qry)) {
            $unionQry = implode(' UNION ALL ', $qry);
            $result = $this->db->query("SELECT * FROM 
                                            ($unionQry)tbl 
                                        WHERE
                                            (partyID IN ({$supplierAutoID}) OR partyID IS NULL)
                                            AND documentDate BETWEEN '{$datefromconvert}' AND '{$datetoconvert}'
                                            AND vatTypeID IN ({$vatType})")->result_array();
        }
       
        return $result;
    }

    function fetch_input_vat_summary_report_excel()
    {
        $detail = array();
        $currency = $this->input->post('currency');
        if ($currency == 1) {
            $detail['currency'] = $this->common_data['company_data']['company_default_currency'];
        } else {
            $detail['currency'] = $this->common_data['company_data']['company_reporting_currency'];
        }
        $detail['documents'] = implode(',', $this->input->post('documentType'));

        $result = $this->load_input_vat_summary_report();
        $detail['suppliers'] = implode(',', array_unique(array_column($result, 'partyName')));
        $detail['vatType'] = implode(',', array_unique(array_column($result, 'vatType')));
        $detail['data'] = array();
        if(!empty($result)) 
        {
            $a = 1;
            $totalAmount = $totalVat = 0;
            foreach ($result as $val) 
            {
                if($val['approvedEmp'] == 1) {
                    $claimed = 'YES';
                } else {
                    $claimed = 'NO';
                }
                if(empty($val['approvedEmp'])) { $val['approvedEmp'] = ''; }
                $detail['data'][] = array( 
                    'Num' => $a,
                    'DocumentCode' => $val['documentCode'],
                    'documentType' => $val['documentType'],
                    'DocumentDate' => $val['documentDate'],
                    'bookingInvCode' => $val['bookingInvCode'],
                    'invoiceDate' => $val['invoiceDate'],
                    'vatIdNo' => $val['vatIdNo'],
                    'Supplier' => $val['partyName'],
                    'comments' => $val['comments'],
                    'VATtype' => $val['vatType'],
                    'VATClaimed' => $claimed, 
                    'ApprovedBy' => $val['approvedEmp'],
                    'TotalAmount' => number_format($val['documentAmount'], $val['decimalPlace'], '.', ''),
                    'VATAmount' => number_format($val['vatAmount'], $val['decimalPlace'], '.', '')
                );
                $totalAmount += $val['documentAmount'];
                $totalVat += $val['vatAmount'];
                $decimalPlaces = $val['decimalPlace'];
                $a++;
            }
            $detail['data'][] = array( 
                'Num' => '',
                'DocumentCode' => 'Total',
                'documentType' => '',
                'DocumentDate' => '',
                'bookingInvCode' => '',
                'invoiceDate' => '',
                'vatIdNo' => '',
                'Supplier' => '',
                'comments' => '',
                'VATtype' => '',
                'VATClaimed' => '',
                'ApprovedBy' => '',
                'TotalAmount' => number_format($totalAmount, $decimalPlaces, '.', ''),
                'VATAmount' => number_format($totalVat, $decimalPlaces, '.', '')
            );

        }
        return $detail;
    }

    function delete_tax_formula_master()
    {
        $taxCalculationformulaID = $this->input->post('taxCalculationformulaID');
        $this->db->delete('srp_erp_taxcalculationformuladetails', array('taxCalculationformulaID' => trim($taxCalculationformulaID)));
        $this->db->delete('srp_erp_taxcalculationformulamaster', array('taxCalculationformulaID' => trim($taxCalculationformulaID)));
        $this->session->set_flashdata('s', 'Tax Formula Deleted Successfully');
        return true;
    }
    
    function load_input_vat_return_filling_report()
    {
        $qry = array();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $taxType = $this->input->post('taxType');
        $vatType = implode(',', $taxType);
        $documentType = $this->input->post('documentType');
        $supplierID = $this->input->post('supplierAutoID');
        $supplierAutoID = implode(',', $supplierID);
        $companyID = current_companyID();

        if (in_array('BSI', $documentType)) {
            $qry[] = "SELECT
                        'Supplier Invoice' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        bookingInvCode AS documentCode,
                        if(costGLCode is null, GLCode, costGLCode) AS glCode,
                        if(costDescription is null, srp_erp_paysupplierinvoicedetail.GLDescription, costDescription) AS glDescription,
                        bookingDate AS documentDate,
                        srp_erp_paysupplierinvoicemaster.supplierID AS partyID,
                        srp_erp_paysupplierinvoicemaster.supplierName AS partyName,
                        supplierCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES')  AS vatregistered,
                        vatIdNo,
                        srp_erp_paysupplierinvoicemaster.transactionCurrency,
                        RefNo AS referenceNumber,
                        invoiceDate,
                        invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_paysupplierinvoicemaster.companyLocalExchangeRate,
                        ( srp_erp_paysupplierinvoicedetail.transactionAmount ) AS documentAmount,
                        srp_erp_paysupplierinvoicemaster.transactionCurrencyDecimalPlaces AS decimalPlace,
	                    IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_paysupplierinvoicemaster.documentID
                        JOIN srp_erp_paysupplierinvoicedetail ON srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'BSI'
                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paysupplierinvoicedetail.itemAutoID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.inputVatGLAccountAutoID 
                        LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        -- AND grvType != 'GRV Base'
                        AND srp_erp_paysupplierinvoicemaster.approvedYN = 1";
        }

        if (in_array('PV', $documentType)) {
            $qry[] = "SELECT
                            'Payment Voucher' AS documentType,
                            documentMasterAutoID,
                            srp_erp_taxledger.documentID,
                            PVcode AS documentCode,
                            if(costGLCode is null, GLCode, costGLCode) AS glCode,
                            if(costDescription is null, srp_erp_paymentvoucherdetail.GLDescription, costDescription) AS glDescription,
                            PVdate AS documentDate,
                            srp_erp_paymentvouchermaster.partyID AS partyID,
                            partyName AS partyName,
                            supplierCountry AS partyCountry,
                            IF(vatEligible = 1, 'NO', 'YES')  AS vatregistered,
                            vatIdNo,
                            srp_erp_paymentvouchermaster.transactionCurrency,
                            '' AS referenceNumber,
                            '' AS invoiceDate,
                            '' AS invoiceDueDate,
                            '' AS referenceDocNo,
                            '' AS referenceDocDate,
                            srp_erp_tax_vat_type.Description AS vatType,
                            srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                            amount AS vatAmount,
                            srp_erp_paymentvouchermaster.companyLocalExchangeRate,
                            srp_erp_paymentvoucherdetail.transactionAmount AS documentAmount,
                            srp_erp_paymentvouchermaster.transactionCurrencyDecimalPlaces AS decimalPlace,
	                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                            srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                            srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                            srp_erp_chartofaccounts.GLDescription AS vatGLDescription
                        FROM
                            srp_erp_taxledger
                            INNER JOIN srp_erp_paymentvouchermaster ON srp_erp_paymentvouchermaster.payVoucherAutoId = srp_erp_taxledger.documentMasterAutoID 
                            AND srp_erp_taxledger.documentID = srp_erp_paymentvouchermaster.documentID
                            JOIN srp_erp_paymentvoucherdetail ON srp_erp_paymentvoucherdetail.payVoucherDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
                            AND srp_erp_taxledger.documentID = 'PV'
                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                            LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                            LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_paymentvoucherdetail.itemAutoID
                            LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID
                            LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.inputVatGLAccountAutoID 
                            LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                        WHERE
                            srp_erp_taxledger.companyID = {$companyID} AND
                            taxCategory = 2 
                            AND srp_erp_paymentvouchermaster.approvedYN = 1";
        }

        if (in_array('DN', $documentType)) {
            $qry[] = "SELECT
                        'Debit Note' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        debitNoteCode AS documentCode,
                        GLCode AS glCode,
                        srp_erp_debitnotedetail.GLDescription AS glDescription,
                        debitNoteDate AS documentDate,
                        srp_erp_debitnotemaster.supplierID AS partyID,
                        srp_erp_debitnotemaster.supplierName AS partyName,
                        supplierCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_debitnotemaster.transactionCurrency,
                        '' AS referenceNumber,
                        invoiceDate,
                        invoiceDueDate,
                        srp_erp_paysupplierinvoicemaster.bookingInvCode AS referenceDocNo,
                        invoiceDate AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        (amount * -1) AS vatAmount,
                        srp_erp_debitnotemaster.companyLocalExchangeRate,
                        (srp_erp_debitnotedetail.transactionAmount * -1) AS documentAmount,
                        srp_erp_debitnotemaster.transactionCurrencyDecimalPlaces AS decimalPlace,
	                    IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_debitnotemaster ON srp_erp_debitnotemaster.debitNoteMasterAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_debitnotemaster.documentID
                        JOIN srp_erp_debitnotedetail ON srp_erp_debitnotedetail.debitNoteDetailsID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'DN'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.inputVatGLAccountAutoID
                        LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_debitnotedetail.InvoiceAutoID
	                    LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        AND srp_erp_debitnotemaster.approvedYN = 1";
        }

       $result = '';
        if(!empty($qry)) {
            $unionQry = implode(' UNION ALL ', $qry);
            $result = $this->db->query("SELECT * FROM 
                                            ($unionQry)tbl 
                                        WHERE
                                            (partyID IN ({$supplierAutoID}) OR partyID IS NULL OR partyID = 0)
                                            AND documentDate BETWEEN '{$datefromconvert}' AND '{$datetoconvert}'
                                            AND vatTypeID IN ({$vatType})")->result_array();
        }
        
        return $result;
    }

    function load_output_vat_return_filling_report()
    {
        $qry = array();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        // $datetoconvert .+ " 23:59:59";
        $taxType = $this->input->post('taxType');
        $vatType = implode(',', $taxType);
        $documentType = $this->input->post('documentType');
        $customerID = $this->input->post('customerAutoID');
        $customerAutoID = implode(',', $customerID);
        $companyID = current_companyID();

        if (in_array('CINV', $documentType)) {
            $qry[] = "SELECT
                        'Customer Invoice' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        invoiceCode AS documentCode,
                        revenueGLCode AS glCode,
                        revenueGLDescription AS glDescription,
                        invoiceDate AS documentDate,
                        customerID AS partyID,
                        srp_erp_customerinvoicemaster.customerName AS partyName,
                        customerCountry AS partyCountry,
                        IF( vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_customerinvoicemaster.transactionCurrency,
                        '' AS referenceNumber,
                        supplyDate AS invoiceDate,
                        invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_customerinvoicemaster.companyLocalExchangeRate,
                        ( srp_erp_customerinvoicedetails.transactionAmount - srp_erp_customerinvoicedetails.taxAmount ) AS documentAmount,
                        srp_erp_customerinvoicemaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_customerinvoicemaster.documentID
                        JOIN srp_erp_customerinvoicedetails ON srp_erp_customerinvoicedetails.invoiceDetailsAutoID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'CINV'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID 
	                    LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        AND srp_erp_customerinvoicemaster.approvedYN = 1 
                        -- AND type != 'DO'
                        ";
        }

        if (in_array('RET', $documentType)) {
            $qry[] = "SELECT
                        'Sales Return' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        salesReturnCode AS documentCode,
                        revenueGLCode AS glCode,
                        revenueGLDescription AS glDescription,
                        returnDate AS documentDate,
                        srp_erp_salesreturnmaster.customerID AS partyID,
                        srp_erp_salesreturnmaster.customerName AS partyName,
                        customerCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_salesreturnmaster.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        IF(srp_erp_salesreturndetails.DOAutoID IS NULL, invoiceCode, DOCode) AS referenceDocNo,
                        IF(srp_erp_salesreturndetails.DOAutoID IS NULL, invoiceDate, DODate) AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        (amount * -1) AS vatAmount,
                        srp_erp_salesreturnmaster.companyLocalExchangeRate,
                        (srp_erp_salesreturndetails.totalValue * -1) AS documentAmount,
                        srp_erp_salesreturnmaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_salesreturnmaster ON srp_erp_salesreturnmaster.salesReturnAutoID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = srp_erp_salesreturnmaster.documentID
                        LEFT JOIN srp_erp_salesreturndetails ON srp_erp_salesreturndetails.salesReturnDetailsID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'SLR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_salesreturnmaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID 
                        LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID
                        LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = srp_erp_salesreturndetails.DOAutoID
                        LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        AND srp_erp_salesreturnmaster.approvedYN = 1";
        }

        if (in_array('CN', $documentType)) {
            $qry[] = "SELECT
                        'Credit Note' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        creditNoteCode AS documentCode,
                        srp_erp_creditnotedetail.GLCode AS glCode,
                        srp_erp_creditnotedetail.GLDescription AS glDescription,
                        creditNoteDate AS documentDate,
                        srp_erp_creditnotemaster.customerID AS partyID,
                        srp_erp_creditnotemaster.customerName AS partyName,
                        customerCountry AS partyCountry,
                        IF( vatEligible = 1, 'NO', 'YES' ) AS vatregistered,
                        vatIdNo,
                        srp_erp_creditnotemaster.transactionCurrency,
                        docRefNo AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        invoiceCode AS referenceDocNo,
                        invoiceDate AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        (amount * -1) AS vatAmount,
                        srp_erp_creditnotemaster.companyLocalExchangeRate,
                        ((srp_erp_creditnotedetail.transactionAmount - taxAmount) * -1) AS documentAmount,
                        srp_erp_creditnotemaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_creditnotemaster ON srp_erp_creditnotemaster.creditNoteMasterAutoID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_creditnotemaster.documentID
                        LEFT JOIN srp_erp_creditnotedetail ON srp_erp_creditnotedetail.creditNoteDetailsID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'CN'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID
                        LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_creditnotedetail.invoiceAutoID 
                        LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        AND srp_erp_creditnotemaster.approvedYN = 1";
        }

        if (in_array('RV', $documentType)) {
            $qry[] = "SELECT
                        'Receipt Voucher' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        RVcode AS documentCode,
                        IF(type = 'Item', revenueGLCode, srp_erp_customerreceiptdetail.GLCode) AS glCode,
                        IF(type = 'Item', revenueGLDescription, srp_erp_customerreceiptdetail.GLDescription) AS glDescription,
                        RVdate AS documentDate,
                        customerID AS partyID,
                        srp_erp_customerreceiptmaster.customerName AS partyName,
                        customerCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_customerreceiptmaster.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_customerreceiptmaster.companyLocalExchangeRate,
                        (srp_erp_customerreceiptdetail.transactionAmount - srp_erp_customerreceiptdetail.taxAmount) AS documentAmount,
                        srp_erp_customerreceiptmaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_customerreceiptmaster ON srp_erp_customerreceiptmaster.receiptVoucherAutoId = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = srp_erp_customerreceiptmaster.documentID
                        LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptdetail.receiptVoucherDetailAutoID = srp_erp_taxledger.documentDetailAutoID 
                        AND srp_erp_taxledger.documentID = 'RV'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerreceiptmaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID 
                        LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2 
                        AND srp_erp_customerreceiptmaster.approvedYN = 1";
        }

        if (in_array('POS', $documentType)) {
            $qry[] = "SELECT
                        'General POS' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        documentSystemCode AS documentCode,
                        revenueGLCode AS glCode,
                        revenueGLDescription AS glDescription,
                        invoiceDate AS documentDate,
                        customerID AS partyID,
                        IFNULL(srp_erp_customermaster.customerName, 'Cash Sale') AS partyName,
                        customerCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_pos_invoice.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_pos_invoice.companyLocalExchangeRate,
                        srp_erp_pos_invoicedetail.transactionAmount AS documentAmount,
                        srp_erp_pos_invoice.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_invoice ON srp_erp_pos_invoice.invoiceID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = 'GPOS'
                        JOIN srp_erp_pos_invoicedetail ON srp_erp_pos_invoicedetail.invoiceDetailsID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'GPOS'
                        LEFT JOIN srp_erp_customermaster ON srp_erp_pos_invoice.customerID = srp_erp_customermaster.customerAutoID
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID 
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID
                        LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID 
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2
                        AND isVoid != 1";
        }

        if (in_array('RPOS', $documentType)) {
            $qry[] = "SELECT
                        'Restaurant POS' AS documentType,
                        IF( isCreditSales = 1, srp_erp_pos_menusalesmaster.documentMasterAutoID, srp_erp_pos_menusalesmaster.menuSalesID ) AS documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        IF( isCreditSales = 1, documentSystemCode, CONCAT( 'POSR/', srp_erp_warehousemaster.wareHouseCode, '/', srp_erp_pos_shiftdetails.shiftID ) ) AS documentCode,
                        '' AS glCode,
                        '' AS glDescription,
                        srp_erp_pos_menusalesmaster.createdDateTime AS documentDate,
                        customerID AS partyID,
                        IFNULL(srp_erp_pos_menusalesmaster.customerName, 'Cash Sale') AS partyName,
                        customerCountry AS partyCountry,
                        IF( vatEligible = 1, 'NO', 'YES' ) AS vatregistered,
                        vatIdNo,
                        srp_erp_pos_menusalesmaster.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                        SUM( paidAmount ) AS documentAmount,
                        srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IFNULL(srp_erp_taxledger.taxPercentage, 0) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalesmaster.shiftID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = 'POSR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxledger.vatTypeID
                        LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_shiftdetails.shiftID = srp_erp_taxledger.documentMasterAutoID
                        LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_shiftdetails.wareHouseID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_menusalesmaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID 
	                    LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory = 2
                        AND NOT EXISTS (SELECT taxLedgerAutoID FROM srp_erp_taxledger WHERE documentID = 'POSR' AND srp_erp_taxledger.documentMasterAutoID = srp_erp_pos_menusalesmaster.menuSalesID)
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        //credit sale.
        if (in_array('RPOS', $documentType)) {
            $qry[] = "SELECT
                        'Restaurant POS' AS documentType,
                        IF( isCreditSales = 1, srp_erp_pos_menusalesmaster.documentMasterAutoID, srp_erp_pos_menusalesmaster.menuSalesID ) AS documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        IF( isCreditSales = 1, documentSystemCode, CONCAT( 'POSR/', srp_erp_warehousemaster.wareHouseCode, '/', srp_erp_pos_shiftdetails.shiftID ) ) AS documentCode,
                        '' AS glCode,
                        '' AS glDescription,
                        srp_erp_pos_menusalesmaster.createdDateTime AS documentDate,
                        customerID AS partyID,
                        IFNULL(srp_erp_pos_menusalesmaster.customerName, 'Cash Sale') AS partyName,
                        customerCountry AS partyCountry,
                        IF( vatEligible = 1, 'NO', 'YES' ) AS vatregistered,
                        vatIdNo,
                        srp_erp_pos_menusalesmaster.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount AS vatAmount,
                        srp_erp_pos_menusalesmaster.companyLocalExchangeRate,
                        SUM( paidAmount ) AS documentAmount,
                        srp_erp_pos_menusalesmaster.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_menusalesmaster ON srp_erp_pos_menusalesmaster.menuSalesID = srp_erp_taxledger.documentMasterAutoID 
                        AND srp_erp_taxledger.documentID = 'POSR'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxledger.vatTypeID
                        LEFT JOIN srp_erp_pos_shiftdetails ON srp_erp_pos_shiftdetails.shiftID = srp_erp_taxledger.documentMasterAutoID
                        LEFT JOIN srp_erp_warehousemaster ON srp_erp_warehousemaster.wareHouseAutoID = srp_erp_pos_shiftdetails.wareHouseID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_menusalesmaster.customerID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID
	                    LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID}
                        AND taxCategory = 2 
                    GROUP BY
                        srp_erp_taxledger.documentID,
                        srp_erp_taxledger.documentMasterAutoID";
        }

        if (in_array('RET', $documentType)) {
            $qry[] = "SELECT
                        'Sales Return' AS documentType,
                        documentMasterAutoID,
                        srp_erp_taxledger.documentID,
                        documentSystemCode AS documentCode,
                        srp_erp_itemmaster.revanueGLCode AS glCode,
                        srp_erp_itemmaster.revanueDescription AS glDescription,
                        salesReturnDate AS documentDate,
                        IF(customerID = 0, null, customerID) AS partyID,
                        customerName AS partyName,
                        customerCountry AS partyCountry,
                        IF(vatEligible = 1, 'NO', 'YES') AS vatregistered,
                        vatIdNo,
                        srp_erp_pos_salesreturn.transactionCurrency,
                        '' AS referenceNumber,
                        '' AS invoiceDate,
                        '' AS invoiceDueDate,
                        '' AS referenceDocNo,
                        '' AS referenceDocDate,
                        srp_erp_tax_vat_type.Description AS vatType,
                        srp_erp_taxcalculationformulamaster.Description AS VATtypeDesription,
                        amount * - 1 AS vatAmount,
                        srp_erp_pos_salesreturn.companyLocalExchangeRate,
                        transactionAmount * - 1 AS documentAmount,
                        srp_erp_pos_salesreturn.transactionCurrencyDecimalPlaces AS decimalPlace,
                        IF(ismanuallychanged = 1, srp_erp_taxledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage ) AS taxPercentage,
                        srp_erp_chartofaccounts.GLSecondaryCode AS vatGLCode,
                        srp_erp_taxcalculationformulamaster.vatTypeID AS vatTypeID,
                        srp_erp_chartofaccounts.GLDescription AS vatGLDescription 
                    FROM
                        srp_erp_taxledger
                        INNER JOIN srp_erp_pos_salesreturn ON srp_erp_pos_salesreturn.salesReturnID = srp_erp_taxledger.documentMasterAutoID AND srp_erp_taxledger.documentID = 'RET'
                        join srp_erp_pos_salesreturndetails ON srp_erp_pos_salesreturndetails.salesReturnDetailID = srp_erp_taxledger.documentDetailAutoID AND srp_erp_taxledger.documentID = 'RET'
                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                        LEFT JOIN srp_erp_taxcalculationformulamaster ON srp_erp_taxcalculationformulamaster.taxCalculationformulaID = srp_erp_taxledger.taxFormulaMasterID
                        LEFT JOIN srp_erp_tax_vat_type ON srp_erp_tax_vat_type.vatTypeID = srp_erp_taxcalculationformulamaster.vatTypeID
                        LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_pos_salesreturn.customerID
                        LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_pos_salesreturndetails.itemAutoID
                        LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_taxmaster.outputVatGLAccountAutoID 
	                    LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                    WHERE
                        srp_erp_taxledger.companyID = {$companyID} AND
                        taxCategory = 2";
        }

        $result = '';
        if(!empty($qry)) {
            $unionQry = implode(' UNION ALL ', $qry);
            $result = $this->db->query("SELECT * FROM 
                                            ($unionQry)tbl 
                                        WHERE
                                            (partyID IN ({$customerAutoID}) OR partyID IS NULL OR partyID = 0)
                                            AND DATE(documentDate) BETWEEN '{$datefromconvert}' AND '{$datetoconvert}'
                                            AND (vatTypeID IN ({$vatType}) OR vatTypeID IS NULL)")->result_array();
        }
        
        return $result;
    }
}