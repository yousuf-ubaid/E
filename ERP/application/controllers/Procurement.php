<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Procurement extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Procurement_modal');
        $this->load->helpers('procurement');
        $this->load->helpers('buyback_helper');
        $this->load->library('Pagination');
    }

    function fetch_purchase_order()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $isReceived = $this->input->post('isReceived');
        $segmentID = $this->input->post('segmentID');
        $supplier_filter = '';
        $segment_filter = '';
        $isReceived_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        if (!empty($segmentID)) {
            $segmentID = array($this->input->post('segmentID'));
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segment_filter = " AND segmentID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND (closedYN = 1)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        if ($isReceived != 'all') {
            if ($isReceived == 0) {
                $isReceived_filter = " AND (isReceived = 0 AND approvedYN = 5)";
            } else if ($isReceived == 1) {
                $isReceived_filter = " AND (isReceived = 1)";
            } else if ($isReceived == 2) {
                $isReceived_filter = " AND (isReceived = 2 )";
            } else if ($isReceived == 3) {
                $isReceived_filter = " AND (closedYN = 1)";
            }
        }
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }

        $where = "srp_erp_purchaseordermaster.companyID = " . $companyid . $supplier_filter . $segment_filter . $date . $status_filter . $searches . $isReceived_filter . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.isPortalPOSubmitted as isPortalPOSubmitted,srp_erp_purchaseordermaster.companyCode,srp_erp_purchaseordermaster.purchaseOrderCode as purchaseOrderCode,srp_erp_purchaseordermaster.versionNo as versionNo,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'$convertFormat') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster.createdUserID as createdUser,srp_erp_purchaseordermaster.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0),2) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN,srp_erp_purchaseordermaster.confirmedByEmpID as confirmedByEmp");
        $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID,IFNULL(SUM(discountAmount),0) as discountAmount  FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyid . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->datatables->add_column('po_detail', '$1', 'load_details(narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate,purchaseOrderID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID,0,isPortalPOSubmitted)');
        $this->datatables->add_column('edit', '$1', 'load_po_action(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp,isPortalPOSubmitted)');
        $this->datatables->add_column('isReceivedlbl', '$1', 'po_Recived(isReceived,closedYN,' . $isReceived . ')');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->add_column('po_code', '$1', 'add_version_code(purchaseOrderCode,versionNo)');
        echo $this->datatables->generate();
        
    }

    function fetch_purchase_order_version()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $masterid = $this->input->post('masterid');

        $companyid = $this->common_data['company_data']['company_id'];
     
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }

        $where = "srp_erp_purchaseordermaster_version.purchaseOrderID = {$masterid} AND srp_erp_purchaseordermaster_version.companyID = " . $companyid . $searches . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster_version.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster_version.versionAutoID as versionAutoID,srp_erp_purchaseordermaster_version.versionNo as versionNo,srp_erp_purchaseordermaster_version.companyCode,srp_erp_purchaseordermaster_version.purchaseOrderCode as purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'$convertFormat') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster_version.createdUserID as createdUser,srp_erp_purchaseordermaster_version.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0),2) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN,srp_erp_purchaseordermaster_version.confirmedByEmpID as confirmedByEmp");
        $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID,versionMasterID,IFNULL(SUM(discountAmount),0) as discountAmount  FROM srp_erp_purchaseorderdetails_version GROUP BY versionMasterID) det', '(det.versionMasterID = srp_erp_purchaseordermaster_version.versionAutoID)', 'left');
        $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyid . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster_version.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster_version');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster_version.supplierID', 'left');
        $this->datatables->add_column('po_detail', '$1', 'load_details(narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate,purchaseOrderID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('po_code', '$1', 'add_po_version_code(purchaseOrderCode,versionNo)');
        $this->datatables->add_column('edit', '$1', 'load_po_action_version(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp,versionAutoID)');
       
        echo $this->datatables->generate();
        
    }

    function fetch_purchase_order_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $searche = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$searche%' ESCAPE '!')  OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%'))";
        }
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->select("approvalType");
        $this->db->from('srp_erp_documentcodemaster');
        $this->db->where('companyID', $companyID);
        $this->db->where('documentID', 'PO');

        $company_doc_approval_type = $this->db->get()->row_array();
        
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $convertFormat = convert_date_format_sql();
        $currentuser = current_userID();
        
        $amountBasedApproval = getPolicyValues('ABA', 'All');
        
        $approvalBasedJoin = '';
        $approvalBasedWhere = '';

        if($company_doc_approval_type['approvalType']==1){

        }else if($company_doc_approval_type['approvalType']==2){
           
           $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1))";
        }else if($company_doc_approval_type['approvalType']==3){
            $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        }else if($company_doc_approval_type['approvalType']==4){
            
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID";
        }else if($company_doc_approval_type['approvalType']==5){
            
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.typeID  = srp_erp_documentapproved.categoryID";
        }

        if ($approvedYN == 0) {
            $where = "srp_erp_purchaseordermaster.companyID = " . $companyID . $searches . $approvalBasedWhere."";
        }else{
            $where = "srp_erp_purchaseordermaster.companyID = " . $companyID . $searches."";
        }
     
       
        
        if ($approvedYN == 0) {
            $this->datatables->select('srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,srp_erp_purchaseordermaster.purchaseOrderCode as purchaseOrderCode,srp_erp_purchaseordermaster.versionNo as versionNo,narration,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,srp_erp_purchaseordermaster.transactionCurrency as transactionCurrency,transactionCurrencyDecimalPlaces,(det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0), 2) as total_value_search,(det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as detTransactionAmount', false);
            $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
            $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyID . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
            $this->datatables->from('srp_erp_purchaseordermaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaseordermaster.purchaseOrderID AND srp_erp_documentapproved.approvalLevelID = srp_erp_purchaseordermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_purchaseordermaster.currentLevelNo','');
           
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PO');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'PO');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
            $this->datatables->where($where);
          
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PO",purchaseOrderID)');
            $this->datatables->add_column('edit', '$1', 'po_action_approval(purchaseOrderID,approvalLevelID,approvedYN,documentApprovedID,PO)');
            $this->datatables->add_column('po_code', '$1', 'add_version_code(purchaseOrderCode,versionNo)');
            echo $this->datatables->generate();
        } else {
            $this->datatables->select('srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,srp_erp_purchaseordermaster.purchaseOrderCode as purchaseOrderCode,srp_erp_purchaseordermaster.versionNo as versionNo,narration,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,srp_erp_purchaseordermaster.transactionCurrency as transactionCurrency,transactionCurrencyDecimalPlaces,(det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0), 2) as total_value_search,(det.transactionAmount-generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as detTransactionAmount', false);
            $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
            $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyID . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
            $this->datatables->from('srp_erp_purchaseordermaster');
            $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaseordermaster.purchaseOrderID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PO');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->where($where);
            $this->datatables->group_by('srp_erp_purchaseordermaster.purchaseOrderID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PO",purchaseOrderID)');
            $this->datatables->add_column('edit', '$1', 'po_action_approval(purchaseOrderID,approvalLevelID,approvedYN,documentApprovedID,PO)');
            $this->datatables->add_column('po_code', '$1', 'add_version_code(purchaseOrderCode,versionNo)');
            echo $this->datatables->generate();
        }
       
    }

    function fetch_umo_data()
    {
        $this->datatables->select("UnitID,UnitShortCode,UnitDes,modifiedUserName");
        $this->datatables->from('srp_erp_unit_of_measure');
        $this->datatables->where('companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '$1', 'load_uom_action(UnitID,UnitDes,UnitShortCode)');
        echo $this->datatables->generate();
    }

    function save_uom()
    {
        $this->form_validation->set_rules('UnitShortCode', 'Code', 'trim|required');
        $this->form_validation->set_rules('UnitDes', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_uom());
        }
    }

    function save_uom_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_uom_conversion());
        }
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->save_inv_tax_detail());
        }
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Procurement_modal->delete_tax_detail());
    }

    function change_conversion()
    {
        $this->form_validation->set_rules('masterUnitID', 'Master Unit ID', 'trim|required');
        $this->form_validation->set_rules('subUnitID', 'Sub Unit ID', 'trim|required');
        $this->form_validation->set_rules('conversion', 'Conversion', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->change_conversion());
        }
    }

    function fetch_convertion_detail_table()
    {
        echo json_encode($this->Procurement_modal->fetch_convertion_detail_table());
    }

    function save_purchase_order_header()
    {
        $purchaseOrderType = trim($this->input->post('purchaseOrderType') ?? '');

        $this->form_validation->set_rules('supplierPrimaryCode', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('expectedDeliveryDate', 'Delivery Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('POdate', 'PO Date ', 'trim|required|validate_date');

        if($purchaseOrderType == 'BCO'){
            $this->form_validation->set_rules('customer_order_id', 'Customer Order', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $format_expectedDeliveryDate = input_format_date($this->input->post('expectedDeliveryDate'), $date_format_policy);
            $format_POdate = input_format_date($this->input->post('POdate'), $date_format_policy);

            if ($format_expectedDeliveryDate >= $format_POdate) {
                echo json_encode($this->Procurement_modal->save_purchase_order_header());
            } else {
                $this->session->set_flashdata('e', 'Expected Delivery Date should be greater than PO Date');
                echo json_encode(FALSE);
            }

        }
    }

    function save_purchase_order_header_buyback()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('supplierPrimaryCode', 'Supplier', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('expectedDeliveryDate', 'Delivery Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('POdate', 'PO Date ', 'trim|required|validate_date');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $format_expectedDeliveryDate = input_format_date($this->input->post('expectedDeliveryDate'), $date_format_policy);
            $format_POdate = input_format_date($this->input->post('POdate'), $date_format_policy);
            if ($format_expectedDeliveryDate >= $format_POdate) {
                echo json_encode($this->Procurement_modal->save_purchase_order_header_buyback());
            } else {
                $this->session->set_flashdata('e', 'Expected Delivery Date should be greater than PO Date');
                echo json_encode(FALSE);
            }
        }
    }

    function save_purchase_order_approval()
    {
        $system_code = trim($this->input->post('purchaseOrderID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'PO', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('purchaseOrderID');
                $this->db->where('purchaseOrderID', trim($system_code));
                $this->db->where('approvedYN', 2);
                $this->db->from('srp_erp_purchaseordermaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Purchase Order Status', 'trim|required');
                    if ($this->input->post('po_status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Procurement_modal->save_purchase_order_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('purchaseOrderID');
            $this->db->where('purchaseOrderID', trim($system_code));
            $this->db->where('approvedYN', 2);
            $this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_purchaseordermaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'PO', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('po_status', 'Purchase Order Status', 'trim|required');
                    if ($this->input->post('po_status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Procurement_modal->save_purchase_order_approval());
                    }
                }
            }
        }

    }


    function save_purchase_order_close()
    {
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_purchase_order_close());
        }
    }

    function save_capitalize_detail_po()
    {
        $this->form_validation->set_rules('capitalizeDate', 'Capitalization date', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->save_capitalize_detail_po());
        }
    }

    function save_purchase_order_detail()
    {

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item ID', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required|greater_than[0]');
          //  $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Unit Cost', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Procurement_modal->save_purchase_order_detail());
        }
    }

    function update_purchase_order_detail()
    {
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $estimatedAmount = trim($this->input->post('estimatedAmount') ?? '');

        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required|greater_than[0]');
        //$this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required|greater_than[0]');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->update_purchase_order_detail());
        }
    }

    function load_purchase_order_header()
    {
        echo json_encode($this->Procurement_modal->load_purchase_order_header());
    }

    function update_commission_btb(){
        echo json_encode($this->Procurement_modal->update_commission_btb());
    }

    function fetch_supplier_currency()
    {
        echo json_encode($this->Procurement_modal->fetch_supplier_currency());
    }

    function fetch_supplier_currency_by_id()
    {
        echo json_encode($this->Procurement_modal->fetch_supplier_currency_by_id());
    }

    function fetch_customer_currency()
    {
        echo json_encode($this->Procurement_modal->fetch_customer_currency());
    }

    function fetch_itemrecode()
    {
        echo json_encode($this->Procurement_modal->fetch_itemrecode());
    }

    function fetch_itemrecode_po()
    {
        echo json_encode($this->Procurement_modal->fetch_itemrecode_po());
    }

    function fetch_po_detail_table()
    {
        echo json_encode($this->Procurement_modal->fetch_po_detail_table());
    }

    function delete_purchase_order_detail()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order_detail());
    }

    function delete_purchase_order()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order());
    }

    function fetch_purchase_order_detail()
    {
        echo json_encode($this->Procurement_modal->fetch_purchase_order_detail());
    }

    function save_po_tax_detail(){
        echo json_encode($this->Procurement_modal->save_po_tax_detail());
    }

    function purchase_order_confirmation()
    {
        echo json_encode($this->Procurement_modal->purchase_order_confirmation());
    }

    function save_asset_detail_pr()
    {
        echo json_encode($this->Procurement_modal->save_asset_detail_pr());
    }

    function load_purchase_order_conformation()
    {
        $purchaseOrderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseOrderID') ?? '');
        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
        $data['version_drop'] = load_po_version_drop_down($purchaseOrderID);
        $data['approval'] = $this->input->post('approval');
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID')!=''?existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID'):0);
        
        $printFooterYN = 1;
        $data['printFooterYN'] = $printFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PO', $purchaseOrderID);

        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', $purchaseOrderID);
        $data['type'] = $this->input->post('html');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
            $data['isPrint']=0;
            $data['isHtml'] = false; 
            $data['show_attachment_header'] = false;
        } else {
            $data['signature'] = '';
            $data['isPrint']=1;
            $data['isHtml'] = true;
            $data['show_attachment_header'] = true;
        }

        $this->db->select('*');
        $this->db->from('srp_erp_documentattachments');
        $this->db->where('documentID', 'PO');
        $this->db->where('documentDetailID !=', 0);
        $this->db->where('documentDetailID IS NOT NULL', null, false);
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentSystemCode', $purchaseOrderID);
        $result = $this->db->get()->result_array();
        $data['attachments'] = $result;

        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $printlink = print_template_pdf('PO', 'system/procurement/erp_purchase_order_print');

        $html = $this->load->view('system/procurement/erp_purchase_order_print', $data, true);

        if ($this->input->post('html')) {
            if ($printlink == 'system/procurement/erp_purchase_order_mubadrah') {
                echo $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
            } else {
                echo $html;
            }
        } else {
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function load_purchase_order_conformation_version()
    {
        $purchaseOrderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseOrderID') ?? '');
        $data['extra'] = $this->Procurement_modal->fetch_template_data_version($purchaseOrderID);

        $this->db->select('*');
        $this->db->where('versionAutoID', trim($purchaseOrderID));
       
        $this->db->from('srp_erp_purchaseordermaster_version');
        $now_version = $this->db->get()->row_array();

        $data['version_drop'] = load_po_version_drop_down($now_version['purchaseOrderID']);
        $data['approval'] = $this->input->post('approval');
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID')!=''?existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID'):0);

        $printFooterYN = 1;
        $data['printFooterYN'] = $printFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PO', $purchaseOrderID);

        $data['isRcmDocument'] =  isRcmApplicable('srp_erp_purchaseordermaster','purchaseOrderID', $purchaseOrderID);
        $data['type'] = $this->input->post('html');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
            $data['isPrint']=0;
        } else {
            $data['signature'] = '';
            $data['isPrint']=1;
        }
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }
        $printlink = print_template_pdf('PO', 'system/procurement/erp_purchase_order_print');

        $html = $this->load->view($printlink, $data, true);
   
        if ($this->input->post('html')) {
            if ($printlink == 'system/procurement/erp_purchase_order_mubadrah') {
                echo $this->load->view('system/procurement/erp_purchase_order_print', $data, true);
            } else {
                echo $html;
            }
        } else {

            //die(' skjd '.$printFooterYN);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function load_approvel()
    {
        $this->datatables->select('documentApprovedID,documentSystemCode,approvalLevelID,srp_employeesdetails.Ename1 as docConfirmedByEmpID,documentDate,CONCAT(srp_employeesdetails.Ename1,\' \',srp_employeesdetails.Ename2) as empname', false)
            ->where('srp_erp_documentapproved.documentSystemCode', $this->input->post('porderid'))
            ->where('srp_erp_documentapproved.documentID', "PO")
            ->where('srp_erp_documentapproved.companyCode', $this->common_data['company_data']['company_code'])
            ->from('srp_erp_documentapproved');
        //$this->datatables->join('srp_schoolmaster', 'srp_erp_documentapproved.companyCode = srp_schoolmaster.SchMasterID', 'left');
        $this->datatables->join('srp_employeesdetails', 'srp_erp_documentapproved.docConfirmedByEmpID = srp_employeesdetails.ECode', 'left');
        echo $this->datatables->generate();

    }

    function referback_procurement()
    {
        $purchaseOrderID = $this->input->post('purchaseOrderID');

        $this->load->library('approvals');

        $this->db->select('approvedYN,purchaseOrderCode');
        $this->db->where('purchaseOrderID', trim($purchaseOrderID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_purchaseordermaster');
        $approved_purchase_order = $this->db->get()->row_array();
        if (!empty($approved_purchase_order)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_purchase_order['purchaseOrderCode']));
        } else {
            $status = $this->approvals->approve_delete($purchaseOrderID, 'PO');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function delete_purchaseOrder_attachement()
    {
        echo json_encode($this->Procurement_modal->delete_purchaseOrder_attachement());
    }

    function re_open_procurement()
    {
        echo json_encode($this->Procurement_modal->re_open_procurement());
    }

    function load_project_segmentBase_old()
    {
        $data_arr = [];
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $ex_segment = explode(" | ", $segment);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();

        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {

                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID', $data_arr, '', 'class="form-control select2" id="projectID_' . $type . '" onchange="load_project_segmentBase_category(this,this.value)"');
    }

    function load_project_segmentBase_multiple_old()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $ex_segment = explode("|", $segment);
        $ex_segment = explode(" | ", $ex_segment[0]);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', 'class="form-control select2" id="projectID" onchange="load_project_segmentBase_category(this,this.value)"');
    }

    function load_project_segmentBase_multiple()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $post_doc = trim($this->input->post('post_doc') ?? '');
        $ex_segment = explode("|", $segment);
        $ex_segment = explode(" | ", $ex_segment[0]);
        /*$this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');*/
        $this->db->select('headerID as projectID, projectDescription as projectName');
        $this->db->from('srp_erp_boq_header');
        $this->db->where('companyID', $companyID);
        //$this->db->where('segmentID', $ex_segment[0]);
        $str = '';
        if($post_doc != 'MR'){
            $this->db->where('segementID', $ex_segment[0]);
            $str = 'onchange="load_project_segmentBase_category(this,this.value)"';
        }
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', 'class="form-control select2 projectID" id="projectID" '.$str);
    }

    function load_project_segmentBase()
    {
        $data_arr = [];
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $post_doc = trim($this->input->post('post_doc') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $ex_segment = explode("|", $segment);
        /*$this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');*/
        $this->db->select('headerID as projectID, projectDescription as projectName');
        $this->db->from('srp_erp_boq_header');
        $this->db->where('companyID', $companyID);
        $this->db->where('segementID', $ex_segment[0]);
        $result = $this->db->get()->result_array();

        $str = 'onchange="load_project_segmentBase_category(this,this.value)"';
        if( $post_doc == 'PRQ' || $post_doc == 'PO' ){
            $str = '';
        }
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {

                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        //echo form_dropdown('projectID', $data_arr, '', 'class="form-control select2" id="projectID_' . $type . '" onchange="load_project_segmentBase_category(this,this.value)"');
        echo form_dropdown('projectID', $data_arr, '', 'class="form-control select2 projectID" id="projectID_' . $type . '" '.$str);
    
    }

    /* function load_project_segmentBase_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');
        $this->db->select('categoryID, categoryCode, categoryDescription');
        $this->db->from('srp_erp_boq_category');
        $this->db->where('companyID', $companyID);
        $this->db->where('projectID', $projectID);
        $result = $this->db->get()->result_array();
        echo json_encode($result);
    } */

    function load_project_segmentBase_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');

        $result ='';
        if(!empty($projectID)){
            $result = $this->db->query("SELECT srp_erp_boq_category.categoryID,`categoryCode`,`categoryDescription` FROM `srp_erp_boq_details`
            LEFT JOIN srp_erp_boq_category on srp_erp_boq_details.categoryID = srp_erp_boq_category.categoryID WHERE
            `companyID` = $companyID AND `headerID` = $projectID AND (tenderType IS NULL OR tenderType = 0)
            GROUP BY categoryID")->result_array();
        }
        
        echo json_encode($result);
    }

   /* function fetch_project_sub_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $categoryID = trim($this->input->post('categoryID') ?? '');
        $this->db->select('subCategoryID, description');
        $this->db->from('srp_erp_boq_subcategory');
        $this->db->where('categoryID', $categoryID);
        $result = $this->db->get()->result_array();

        echo json_encode($result);
    } */
    function fetch_project_sub_category()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = $this->input->post('projectID');
        $categoryID = trim($this->input->post('categoryID') ?? '');

        $result ='';
        if(!empty($categoryID)){
            $result = $this->db->query("SELECT detailID as subCategoryID, itemDescription as description FROM srp_erp_boq_details
                                       WHERE categoryID = $categoryID AND headerID = $projectID AND (tenderType IS NULL OR tenderType = 0)")->result_array();
        }
        
        echo json_encode($result);
    }

    function load_project_segmentBase_multiple_noclass()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $detailID = trim($this->input->post('detailID') ?? '');
        $ex_segment = explode(" | ", $segment);
        $this->db->select('projectID, projectName');
        $this->db->from('srp_erp_projects');
        $this->db->where('companyID', $companyID);
        $this->db->where('segmentID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', ' id="projectID_' . $detailID . '" onchange="load_project_segmentBase_category(this,this.value)"');
    }

    function fetch_prq_code()
    {
        echo json_encode($this->Procurement_modal->fetch_prq_code());
    }

    function fetch_prq_detail_table()
    {
        echo json_encode($this->Procurement_modal->fetch_prq_detail_table());
    }

    function save_prq_base_items()
    {
        echo json_encode($this->Procurement_modal->save_prq_base_items());
    }

    function fetch_last_grn_amount()
    {
        echo json_encode($this->Procurement_modal->fetch_last_grn_amount());
    }

    function loademail()
    {
        echo json_encode($this->Procurement_modal->loademail());
    }

    function send_po_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->send_po_email());
        }

    }

    function save_inv_disc_detail()
    {
        $this->form_validation->set_rules('discpercentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->save_inv_disc_detail());
        }
    }

    function open_po_sending_model_max_portal()
    {
        

        $this->form_validation->set_rules('statuspo', 'Status', 'trim|required');
      
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Procurement_modal->open_po_sending_model_max_portal());
        }
    }

    function delete_purchase_order_discount()
    {
        echo json_encode($this->Procurement_modal->delete_purchase_order_discount());
    }

    function edit_discount()
    {
        echo json_encode($this->Procurement_modal->edit_discount());
    }

    function fetch_purchase_order_buyback()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $isReceived = $this->input->post('isReceived');
        $segmentID = $this->input->post('segmentID');
        $supplier_filter = '';
        $segment_filter = '';
        $isReceived_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        if (!empty($segmentID)) {
            $segmentID = array($this->input->post('segmentID'));
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segment_filter = " AND segmentID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        if ($isReceived != 'all') {
            if ($isReceived == 0) {
                $isReceived_filter = " AND (isReceived = 0 AND approvedYN = 5)";
            } else if ($isReceived == 1) {
                $isReceived_filter = " AND (isReceived = 1)";
            } else if ($isReceived == 2) {
                $isReceived_filter = " AND (isReceived = 2 )";
            } else {
                $isReceived_filter = " AND (closedYN = 1)";
            }
        }
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }


        $where = "srp_erp_purchaseordermaster.companyID = " . $companyid . $supplier_filter . $segment_filter . $date . $status_filter . $searches . $isReceived_filter . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster.createdUserID as createdUser,srp_erp_purchaseordermaster.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount-(generalDiscountPercentage/100)*det.transactionAmount)+IFNULL(gentax.gentaxamount,0) as total_value,(det.transactionAmount-(generalDiscountPercentage/100)*det.transactionAmount)+IFNULL(gentax.gentaxamount,0) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN,srp_erp_purchaseordermaster.confirmedByEmpID as confirmedByEmp");
        $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyid . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->datatables->add_column('po_detail', '<b>Supplier Name : </b> $2 <br> <b>PO Date : </b> $6 <br> <b>Exp Delivery Date : </b> $3  <b>&nbsp;&nbsp; Type : </b> $5<br><b>Narration : </b> $1', 'narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'load_po_action_buyback(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->add_column('isReceivedlbl', '$1', 'po_Recived(isReceived,closedYN,' . $isReceived . ')');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function load_purchase_order_conformation_buyback()
    {
        $purchaseOrderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseOrderID') ?? '');
        $data['extra'] = $this->Procurement_modal->fetch_template_data($purchaseOrderID);
        $data['approval'] = $this->input->post('approval');
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PO');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Procurement_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo'] = mPDFImage;
        if ($this->input->post('html')) {
            $data['logo'] = htmlImage;
        }

        $html = $this->load->view('system/procurement/erp_purchase_order_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], 0);
        }
    }

    function fetch_purchase_order_approval_buyback()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $searche = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$searche%' ESCAPE '!')  OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%'))";
        }
        $companyID = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_purchaseordermaster.companyID = " . $companyID . $searches . "";
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supplierName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,transactionCurrency,transactionCurrencyDecimalPlaces,(det.transactionAmount-generalDiscountAmount) as total_value,ROUND((det.transactionAmount-generalDiscountAmount),2) as total_value_search,(det.transactionAmount-generalDiscountAmount) as detTransactionAmount', false);
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaseordermaster.purchaseOrderID AND srp_erp_documentapproved.approvalLevelID = srp_erp_purchaseordermaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_purchaseordermaster.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'PO');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'PO');
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
        $this->datatables->where($where);
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('purchaseOrderCode', '$1', 'approval_change_modal(purchaseOrderCode,purchaseOrderID,documentApprovedID,approvalLevelID,approvedYN,PO,0)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'po_action_approval_buyback(purchaseOrderID,approvalLevelID,approvedYN,documentApprovedID,PO)');
        echo $this->datatables->generate();
    }

//check budget controls if inventory
    function confirmation_Inventory_check()
    {
        echo json_encode($this->Procurement_modal->confirmation_Inventory_check());
    }

    function open_all_notes()
    {
        echo json_encode($this->Procurement_modal->open_all_notes());
    }

    function load_default_note()
    {
        echo json_encode($this->Procurement_modal->load_default_note());
    }

    function fetch_po_details_by_id()
    {
        echo json_encode($this->Procurement_modal->fetch_po_details_by_id());
    }

    function fetch_item_history_on_document()
    {
        $convertFormat = convert_date_format_sql();
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $documentCode = trim($this->input->post('documentCode') ?? '');

        if($documentCode =='PO'){
            $this->db->select('podet.unitAmount,podet.requestedQty,podet.discountAmount,srp_erp_purchaseordermaster.documentDate,srp_erp_purchaseordermaster.companyLocalCurrency,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_purchaseordermaster.purchaseOrderCode');
            $this->db->from('srp_erp_purchaseorderdetails podet');
            $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = podet.purchaseOrderID', 'LEFT');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = podet.itemAutoID', 'LEFT');
            $this->db->where('podet.itemAutoID', $itemAutoID);
            $this->db->where('srp_erp_purchaseordermaster.approvedYN', 1);
            //$this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
            $data['item_Data'] = $this->db->get()->result_array();

            $this->db->select('*');
            $this->db->from('srp_erp_itemmaster');
           
            $this->db->where('itemAutoID', $itemAutoID);
            $data['item_details'] = $this->db->get()->row_array();

            $this->load->view('system/procurement/erp_document_item_history', $data);
        }

        
    }

    function load_notes()
    {
        echo json_encode($this->Procurement_modal->load_notes());
    }

    function fetch_supplier_Dropdown_all()
    {
        $supplierID = $this->input->post('supplier');
        $purchseorderid = $this->input->post('DocID');
        $data_arr = array('' => 'Select Supplier');
        $Documentid = $this->input->post('Documentid');;
        $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($purchseorderid, $Documentid);


        if ($supplierID) {
            $supplier = $supplierID;
        } else {
            $supplier = '';
        }
        if ($purchseorderid != ' ') {
            if ($supplieridcurrentdoc['isActive'] == 0) {
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
        echo form_dropdown('supplierPrimaryCode', $data_arr, $supplier, 'class="form-control select2" id="supplierPrimaryCode" onchange="fetch_supplier_currency_by_id(this.value);"');
    }

    function fetch_supplier_Dropdown_all_grv()
    {
        $supplierID = $this->input->post('supplier');
        $purchseorderid = $this->input->post('DocID');
        $data_arr = array('' => 'Select Supplier');
        $Documentid = $this->input->post('Documentid');
        $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($purchseorderid, $Documentid);


        if ($supplierID) {
            $supplier = $supplierID;
        } else {
            $supplier = '';
        }
        if ($purchseorderid != ' ') {
            if ($supplieridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');
            }

        }

        $companyID = $this->common_data['company_data']['company_id'];
        $supplierqry = "SELECT supplierAutoID,supplierName,supplierSystemCode,supplierCountry FROM srp_erp_suppliermaster WHERE companyID = {$companyID} AND isActive = 1 AND masterApprovedYN = 1";
        $supplierMaster = $this->db->query($supplierqry)->result_array();
        if (!empty($supplierMaster)) {
            foreach ($supplierMaster as $row) {
                $data_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('supplierID', $data_arr, $supplier, 'class="form-control select2" id="supplierID" onchange="fetch_supplier_currency_by_id(this.value);"');
    }

    function fetch_supplier_Dropdown_all_dn()
    {
        $supplierID = $this->input->post('supplier');
        $purchseorderid = $this->input->post('DocID');
        $data_arr = array('' => 'Select Supplier');
        $Documentid = $this->input->post('Documentid');;
        $supplieridcurrentdoc = all_supplier_drop_isactive_inactive($purchseorderid, $Documentid);


        if ($supplierID) {
            $supplier = $supplierID;
        } else {
            $supplier = '';
        }
        if ($purchseorderid != ' ') {
            if ($supplieridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($supplieridcurrentdoc['supplierAutoID'] ?? '')] = (trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') ? trim($supplieridcurrentdoc['supplierSystemCode'] ?? '') . ' | ' : '') . trim($supplieridcurrentdoc['supplierName'] ?? '') . (trim($supplieridcurrentdoc['supplierCountry'] ?? '') ? ' | ' . trim($supplieridcurrentdoc['supplierCountry'] ?? '') : '');
            }

        }


        $companyID = $this->common_data['company_data']['company_id'];
        $supplierqry = "SELECT supplierAutoID,supplierName,supplierSystemCode,supplierCountry FROM srp_erp_suppliermaster WHERE companyID = {$companyID} AND isActive = 1 AND masterApprovedYN = 1";
        $supplierMaster = $this->db->query($supplierqry)->result_array();
        if (!empty($supplierMaster)) {
            foreach ($supplierMaster as $row) {
                $data_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('supplier', $data_arr, $supplier, 'class="form-control select2" id="supplier" onchange="fetch_supplier_currency_by_id(this.value);"');
    }

    function save_document_email_history()
    {
        echo json_encode($this->Procurement_modal->save_document_email_history());
    }

    function load_mail_history()
    {
        $this->datatables->select('autoID,srp_erp_documentemailhistory.documentID,documentAutoID,sentByEmpID,toEmailAddress,sentDateTime,srp_employeesdetails.Ename2 as ename,srp_erp_purchaseordermaster.purchaseOrderCode')
            ->where('srp_erp_documentemailhistory.companyID', $this->common_data['company_data']['company_id'])
            ->where('srp_erp_documentemailhistory.documentID', 'PO')
            ->where('srp_erp_documentemailhistory.documentAutoID', $this->input->post('purchaseOrderID'))
            ->join('srp_employeesdetails', 'srp_erp_documentemailhistory.sentByEmpID = srp_employeesdetails.EIdNo', 'left')
            ->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_documentemailhistory.documentAutoID', 'left')
            ->from('srp_erp_documentemailhistory');
        echo $this->datatables->generate();
    }

    function save_po_general_tax()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('tax_total', 'Tax Applicable Amount', 'trim|required');
        $this->form_validation->set_rules('purchaseOrderID', 'Purchase Order ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Procurement_modal->save_po_general_tax());
        }
    }

    function delete_tax_po()
    {
        echo json_encode($this->Procurement_modal->delete_tax_po());
    }

    function load_line_tax_amount()
    {
        echo json_encode($this->Procurement_modal->load_line_tax_amount());
    }

    function load_purchase_order_tracking()
    {
        $supplier = $this->input->post('supplierAutoID');
        $pocode = $this->input->post('poautoID');


            $data = array();
            $data['details'] = $this->Procurement_modal->load_purchase_order_tracking();
            $data = $this->load->view('system/procurement/report/load_erp_purchase_order_tracking_report', $data, true);
            echo json_encode($data);


    }

    function export_purchase_order_tracking_excel()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Purchase Order tracking');
        $this->load->database();
        $data = $this->Procurement_modal->fetch_purchase_order_tracking_excel();
        $header = ['#', 'PO Number', 'PO Date', 'Narration', 'Supplier Code', 'Supplier Name', 'Currency', 'Amount', 'GRV Code', 'GRV Date', 'Amount', 'Invoice Code', 'Invoice Date', 'Amount', 'Payment Type', 'Payment Code', 'Payment Date', 'Paid Amount'];
        $trackingDetails = $data;
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');

        $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->fromArray(['Purchase Order tracking'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->fromArray($header, null, 'A4');
        $this->excel->getActiveSheet()->fromArray($trackingDetails, null, 'A5');

        $filename = 'Purchase Order tracking.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function fetch_pocode()
    {
            $data_arr = array();
            $suppierID = ($this->input->post('suppierID'));
            $where = "";
            $companyID = current_companyID();
             if (!empty($suppierID)) {
            $filtersuppier = join(',', $suppierID);
            $where = "AND supplierID IN ($filtersuppier)";
               }

            $po = $this->db->query("SELECT `srp_erp_purchaseordermaster`.`purchaseOrderID`, `purchaseOrderCode` FROM `srp_erp_purchaseordermaster` 
                                              WHERE `companyID` = '{$companyID}' $where AND `approvedYN` = 1")->result_array();
             if (!empty($po)) {
                foreach ($po as $row) {
                    $data_arr[trim($row['purchaseOrderID'] ?? '')] = trim($row['purchaseOrderCode'] ?? '');
                }
            }
            echo form_dropdown('poautoID[]', $data_arr, '', 'class="form-control select2" id="poautoID"  onchange="startMasterSearch()" multiple="multiple" ');
    }

    function fetch_last_PO_price()
    {
        echo json_encode($this->Procurement_modal->fetch_last_PO_price());
    }

    function load_item_status_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert"> Date To is  required </div>';
        } else {
            $this->form_validation->set_rules('items[]', 'Item', 'required');
            $this->form_validation->set_rules('poautoID[]', 'PO code', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Procurement_modal->fetch_details_item_status_report();
                $data['type'] = "html";

                echo $this->load->view('system/procurement/report/load_item_status_report', $data, true);
            }
        }
    }

    function load_item_status_report_pdf()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert"> Date To is  required </div>';
        } else {
            $this->form_validation->set_rules('items[]', 'Item', 'required');
            $this->form_validation->set_rules('poautoID[]', 'PO code', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Procurement_modal->fetch_details_item_status_report();
                $data['type'] = "pdf";

                $html = $this->load->view('system/procurement/report/load_item_status_report', $data, true);
                $this->load->library('pdf');
                $this->pdf->printed($html, 'A4-L', 1);
            }
        }
    }
    function fetch_pr_to_grv()
    {
        $companyID = current_companyID();
        $ItemAutoId = $this->input->post('itemautoID');
        $item_autoID_filter = '';
        $documentID = $this->input->post('documentID');
        $documentcode = $this->input->post('doccode');
        $posegment = $this->input->post('posegment');

        $date_format_policy = date_format_policy();
        $prDateFrom = $this->input->post('prDateFrom');
        $prDateFromconvert = input_format_date($prDateFrom, $date_format_policy);
        $prDateTo = $this->input->post('prDateTo');
        $prDateToconvert = input_format_date($prDateTo, $date_format_policy);
        $documentcodefilter = '';
        $dateFilter = '';
        $documentcodefilter_pr = '';
        $documentcodefilter_po = '';
        $filter_po_segment = '';

        if($ItemAutoId)
        {
            $item_autoID_filter .= "AND purchasereqdetail.itemAutoID IN ('".join(',', $ItemAutoId)."')";
        }
        if(!empty($prDateFrom) && !empty($prDateTo))
        {
            $dateFilter .= " AND purchasereqmaster.documentDate BETWEEN '" . $prDateFromconvert . "' AND '" . $prDateToconvert . "'";
        }
        if($documentcode)
        {
                if($documentID == 1)
                {
                    $documentcodefilter_pr = " AND ((purchaseRequestCode Like '%" . $documentcode . "%')) ";
                }else {
                    $documentcodefilter_po = " AND ((purchaseordermaster.purchaseOrderCode Like '%" . $documentcode . "%')) ";
                }
        }

        if($posegment){ 
            $filter_po_segment .= "AND purchaseordermaster.segmentID IN ('".join(',', $posegment)."')";
        }

        $receiptStatusFilter =  $this->input->post('receiptStatusFilter');
        if($receiptStatusFilter=='partially_received'){
            $query_select_extend = ',purchaseOrderID,poApprovedDate';
            $join_select_extend = ',purchaseorderdetial.purchaseOrderID	as purchaseOrderID,purchaseordermaster.approvedDate as poApprovedDate';
            $where_query_extend = "and purchaseOrderID IS NOT NULL and purchaseOrderID!=0 and purchaseOrderID!='' and poApprovedDate IS NOT NULL and poApprovedDate !='' ";
            $third_query_where_extend = "and purchaseorderdetial.purchaseOrderID IS NOT NULL	
                                            and purchaseorderdetial.purchaseOrderID!=0 
                                            and purchaseorderdetial.purchaseOrderID!='' 	
                                            and purchaseordermaster.approvedDate IS NOT NULL
                                            and purchaseordermaster.approvedDate != ''
                                            and purchasereqmaster.purchaseRequestID IS NOT NULL	and purchasereqmaster.purchaseRequestID!=0
                                            and (purchaseorderdetial.requestedQty>grvdetail.qty) and (grvdetail.qty>0)";

        }else if($receiptStatusFilter=='fully_received'){
            $query_select_extend = ',purchaseOrderID,
            poApprovedDate,
            grvbsidoc';
            $join_select_extend = ',purchaseorderdetial.purchaseOrderID	as purchaseOrderID,
                                    purchaseordermaster.approvedDate as poApprovedDate,
                                    grvbsidoc';
            $where_query_extend = " and purchaseOrderID IS NOT NULL 
            -- and purchaseOrderID!=0 
            -- and purchaseOrderID!='' 
            -- and poApprovedDate IS NOT NULL 
            -- and poApprovedDate !='' 
             and grvbsidoc IS NOT NULL and grvbsidoc !=''
            ";
            $third_query_where_extend = "
                                             and purchaseorderdetial.purchaseOrderID IS NOT NULL	
                                            -- and purchaseorderdetial.purchaseOrderID!=0 
                                            -- and purchaseorderdetial.purchaseOrderID!='' 	
                                            -- and purchaseordermaster.approvedDate IS NOT NULL
                                            -- and purchaseordermaster.approvedDate != ''
                                             and grvbsidoc IS NOT NULL and grvbsidoc !=''
                                            and (purchaseorderdetial.requestedQty<=grvdetail.qty)";

        }else if($receiptStatusFilter=='not_received'){
            $query_select_extend = ',purchaseOrderID,poApprovedDate,grvbsidoc,qty';
            $join_select_extend = ',purchaseorderdetial.purchaseOrderID	as purchaseOrderID,
            purchaseordermaster.approvedDate as poApprovedDate,
            purchaseordermaster.approvedYN posApprovedYN,
            grvbsidoc,
            qty';//<- need to check this field.
            $where_query_extend = " and purchaseOrderID IS NOT NULL 
             and purchaseOrderID!=0 
             and purchaseOrderID!='' 
             and poApprovedDate IS NOT NULL 
              and poApprovedDate != ''
             --  AND posApprovedYN = 0 
             -- AND ( posApprovedYN IS NOT NULL OR posApprovedYN != '' )
             -- and grvbsidoc !=''
             and qty IS NULL";//<- need to check this field.

            $third_query_where_extend = "and purchaseorderdetial.purchaseOrderID IS NOT NULL	
                                            and purchaseorderdetial.purchaseOrderID!=0 
                                            and purchaseorderdetial.purchaseOrderID!='' 	
                                            and purchaseordermaster.approvedDate IS NOT NULL
                                             and purchaseordermaster.approvedDate != ''
                                            and purchasereqmaster.purchaseRequestID IS NOT NULL	and purchasereqmaster.purchaseRequestID!=0
                                            --  AND purchaseordermaster.approvedYN = 0 
	 -- AND ( purchaseordermaster.approvedYN IS NOT NULL OR purchaseordermaster.approvedYN != '' )
	-- and grvbsidoc !=''
	 and grvdetail.qty IS NULL";//<- this field is correct.


        }else{
            $query_select_extend = '';
            $join_select_extend = '';
            $where_query_extend = "";
            $third_query_where_extend = "";
        }

        $data['headercount'] = $this->db->query("select
                                                     purchasereqmaster.purchaseRequestID as purchaseRequestID,
                                                     purchasereqmaster.purchaseRequestCode,
                                                     purchasereqmaster.documentDate,
                                                     purchasereqmaster.narration,
                                                     purchasereqmaster.approvedYN
                                                     $query_select_extend
                                                     FROM
                                                     srp_erp_purchaserequestmaster purchasereqmaster
                                                     INNER JOIN (SELECT
	                                                             purchasereqmaster.purchaseRequestID AS purchaseRequestID
	                                                             $join_select_extend
                                                                 FROM
	                                                             srp_erp_purchaserequestdetails purchasereqdetail
	                                                             LEFT JOIN srp_erp_purchaserequestmaster purchasereqmaster ON purchasereqmaster.purchaseRequestID = purchasereqdetail.purchaseRequestID
	                                                             LEFT JOIN srp_erp_purchaseorderdetails purchaseorderdetial ON purchaseorderdetial.prDetailID = purchasereqdetail.purchaseRequestDetailsID
	                                                             LEFT JOIN srp_erp_purchaseordermaster purchaseordermaster ON purchaseordermaster.purchaseOrderID = purchaseorderdetial.purchaseOrderID
	                                                             LEFT JOIN srp_erp_suppliermaster suppmaster ON suppmaster.supplierAutoID = purchaseordermaster.supplierID
	                                                             LEFT JOIN (SELECT
		                                                                    CONCAT( 'GRV', grvdetails.grvDetailsID ) AS masterIDcode,
		                                                                    grvmaster.grvPrimaryCode AS systemcode,
		                                                                    grvdetails.purchaseOrderDetailsID AS purchasedetId,
		                                                                    grvdetails.grvDetailsID AS masterDetailID,
		                                                                    grvdetails.purchaseOrderMastertID AS purchaseOrderMastertID,
		                                                                    grvmaster.grvDate AS grvbsiDate,
		                                                                    grvdetails.requestedQty AS qty,
		                                                                    grvdetails.purchaseOrderDetailsID AS poorderID,
		                                                                    grvmaster.documentID AS grvbsidoc	
	                                                                        FROM
                                                                            srp_erp_purchaseorderdetails purchaseorderdetial
                                                                            LEFT JOIN srp_erp_grvdetails grvdetails ON grvdetails.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                                            LEFT JOIN srp_erp_grvmaster grvmaster ON grvmaster.grvAutoID = grvdetails.grvAutoID 
                                                                            GROUP BY
		                                                                    grvmaster.documentID,
		                                                                    grvmaster.grvAutoID UNION ALL
	                                                                        SELECT
		                                                                    CONCAT( 'BSI', supplierDetail.InvoiceDetailAutoID ) AS masterIDcode,
                                                                            supplierinvmaster.bookingInvCode AS systemcode,
                                                                            supplierDetail.purchaseOrderDetailsID AS purchasedetId,
                                                                            supplierDetail.InvoiceDetailAutoID AS masterDetailID,
                                                                            supplierDetail.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                                            supplierinvmaster.bookingDate AS grvbsiDate,
                                                                            supplierDetail.requestedQty AS qty,
                                                                            supplierDetail.purchaseOrderDetailsID AS poorderID,
                                                                            supplierinvmaster.documentID AS grvbsidoc	 
	                                                                        FROM
                                                                            srp_erp_purchaseorderdetails purchaseorderdetial
                                                                            LEFT JOIN srp_erp_paysupplierinvoicedetail supplierDetail ON supplierDetail.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                                            LEFT JOIN srp_erp_paysupplierinvoicemaster supplierinvmaster ON supplierinvmaster.InvoiceAutoID = supplierDetail.InvoiceAutoID 
	                                                                        GROUP BY
		                                                                    supplierinvmaster.documentID,
		                                                                    supplierinvmaster.InvoiceAutoID) grvdetail ON grvdetail.purchasedetId = purchaseorderdetial.purchaseOrderDetailsID 
                                                                            WHERE
                                                                            purchasereqmaster.companyID = $companyID 
                                                                            AND purchasereqmaster.approvedYN = 1 
                                                                            $item_autoID_filter
                                                                            $documentcodefilter_pr
                                                                            $documentcodefilter_po
                                                                            $filter_po_segment
                                                                            GROUP BY
		                                                                    purchasereqmaster.purchaseRequestID) prdetail ON prdetail.purchaseRequestID = purchasereqmaster.purchaseRequestID
                                                                            WHERE
                                                                            purchasereqmaster.companyID = $companyID 
                                                                            $documentcodefilter_pr
                                                                            $dateFilter
                                                                            AND purchasereqmaster.approvedYN = 1
                                                                            $where_query_extend")->result_array();
        $totalCount = count($data['headercount']);
        $data_pagination = $this->input->post('pageID');
        $per_page = 10;
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
        $thisPageStartNumber = ($page + 1);
        $data['thisPageStartNumber'] = $thisPageStartNumber;

        $data['purchaserequestmaster'] =   $this->db->query("select 
                                                                 purchasereqmaster.purchaseRequestID as purchaseRequestID,
                                                                 purchasereqmaster.purchaseRequestCode,
                                                                 purchasereqmaster.documentDate,
                                                                 purchasereqmaster.narration,
                                                                 purchasereqmaster.approvedYN
                                                                 $query_select_extend
                                                                 FROM
                                                                 srp_erp_purchaserequestmaster purchasereqmaster
                                                                 INNER JOIN (SELECT
                                                                             purchasereqmaster.purchaseRequestID AS purchaseRequestID
                                                                             $join_select_extend
                                                                             FROM
	                                                                         srp_erp_purchaserequestdetails purchasereqdetail
	                                                                         LEFT JOIN srp_erp_purchaserequestmaster purchasereqmaster ON purchasereqmaster.purchaseRequestID = purchasereqdetail.purchaseRequestID
	                                                                         LEFT JOIN srp_erp_purchaseorderdetails purchaseorderdetial ON purchaseorderdetial.prDetailID = purchasereqdetail.purchaseRequestDetailsID
	                                                                         LEFT JOIN srp_erp_purchaseordermaster purchaseordermaster ON purchaseordermaster.purchaseOrderID = purchaseorderdetial.purchaseOrderID
	                                                                         LEFT JOIN srp_erp_suppliermaster suppmaster ON suppmaster.supplierAutoID = purchaseordermaster.supplierID
	                                                                         LEFT JOIN (SELECT
                                                                                        CONCAT( 'GRV', grvdetails.grvDetailsID ) AS masterIDcode,
                                                                                        grvmaster.grvPrimaryCode AS systemcode,
                                                                                        grvdetails.purchaseOrderDetailsID AS purchasedetId,
                                                                                        grvdetails.grvDetailsID AS masterDetailID,
                                                                                        grvdetails.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                                                        grvmaster.grvDate AS grvbsiDate,
                                                                                        grvdetails.requestedQty AS qty,
                                                                                        grvdetails.purchaseOrderDetailsID AS poorderID,
                                                                                        grvmaster.documentID AS grvbsidoc	 
                                                                                        FROM
		                                                                                srp_erp_purchaseorderdetails purchaseorderdetial
		                                                                                LEFT JOIN srp_erp_grvdetails grvdetails ON grvdetails.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
		                                                                                LEFT JOIN srp_erp_grvmaster grvmaster ON grvmaster.grvAutoID = grvdetails.grvAutoID 
	                                                                                    GROUP BY
                                                                                        grvmaster.documentID,
                                                                                        grvmaster.grvAutoID UNION ALL
	                                                                                    SELECT
                                                                                        CONCAT( 'BSI', supplierDetail.InvoiceDetailAutoID ) AS masterIDcode,
                                                                                        supplierinvmaster.bookingInvCode AS systemcode,
                                                                                        supplierDetail.purchaseOrderDetailsID AS purchasedetId,
                                                                                        supplierDetail.InvoiceDetailAutoID AS masterDetailID,
                                                                                        supplierDetail.purchaseOrderMastertID AS purchaseOrderMastertID,
                                                                                        supplierinvmaster.bookingDate AS grvbsiDate,
                                                                                        supplierDetail.requestedQty AS qty,
                                                                                        supplierDetail.purchaseOrderDetailsID AS poorderID,
                                                                                        supplierinvmaster.documentID AS grvbsidoc	 
                                                                                        FROM
                                                                                        srp_erp_purchaseorderdetails purchaseorderdetial
                                                                                        LEFT JOIN srp_erp_paysupplierinvoicedetail supplierDetail ON supplierDetail.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
                                                                                        LEFT JOIN srp_erp_paysupplierinvoicemaster supplierinvmaster ON supplierinvmaster.InvoiceAutoID = supplierDetail.InvoiceAutoID 
                                                                                        GROUP BY
                                                                                        supplierinvmaster.documentID,
		                                                                                supplierinvmaster.InvoiceAutoID 
	                                                                                    ) grvdetail ON grvdetail.purchasedetId = purchaseorderdetial.purchaseOrderDetailsID 
                                                                                        WHERE
                                                                                        purchasereqmaster.companyID = $companyID 
	                                                                                    AND purchasereqmaster.approvedYN = 1 
                                                                                        $item_autoID_filter
                                                                                        $documentcodefilter_pr
                                                                                        $documentcodefilter_po
                                                                                        $filter_po_segment
                                                                                        GROUP BY
		                                                                                purchasereqmaster.purchaseRequestID) prdetail ON prdetail.purchaseRequestID = purchasereqmaster.purchaseRequestID
                                                                                        WHERE
                                                                                        purchasereqmaster.companyID = $companyID 
                                                                                        $documentcodefilter_pr $dateFilter
                                                                                         AND purchasereqmaster.approvedYN = 1 
                                                                                         $where_query_extend                                                                                        
                                                                                         LIMIT {$page},{$per_page}
                                                                                         ")->result_array();

        $results = $this->db->query("SELECT
	purchasereqmaster.purchaseRequestID AS purchaseRequestID,
	purchasereqmaster.purchaseRequestCode,
	purchasereqmaster.documentDate,
	purchasereqdetail.itemAutoID,
	purchasereqdetail.itemSystemCode,
	purchasereqdetail.itemDescription,
	purchasereqdetail.defaultUOM,
	purchasereqdetail.requestedQty,
	purchasereqdetail.purchaseRequestDetailsID,
	purchasereqmaster.narration,
	purchasereqmaster.approvedYN,
	purchaseordermaster.purchaseOrderCode,
	purchaseorderdetial.prDetailID,
	purchaseorderdetial.purchaseOrderID,
	purchaseorderdetial.purchaseOrderDetailsID,
	purchaseordermaster.documentDate AS podate,
	suppmaster.supplierSystemCode,
	suppmaster.supplierName,
	purchaseorderdetial.requestedQty AS reqpoqty,
	purchaseordermaster.transactionCurrency AS pocurrency,
    (purchaseorderdetial.totalAmount +  IFNULL(purchaseorderdetial.taxAmount ,0)) AS poamount,
	purchaseordermaster.transactionCurrencyDecimalPlaces AS podecimal,
	DATE_FORMAT( purchaseordermaster.ConfirmedDate, '%d/%m/%Y' ) AS poconfirmeddate,
	DATE_FORMAT( purchaseordermaster.approvedDate, '%d/%m/%Y' ) AS poapproveddate,
	purchaseordermaster.approvedYN AS poapprovedYN,
	grvdetail.systemcode,
	IFNULL(grvdetail.masterIDcode,'UN-1') as  masterIDcode,
	grvdetail.grvbsiDate,
	grvdetail.qty  as grvqty,
		 IFNULL(grvdetail.poorderID,'N/A') as  poorderID,
	grvdetail.grvbsidoc,
	grvdetail.grvbsimasterID,
	grvdetail.grvbsiapprovedYN,
    srp_erp_segment.segmentCode
FROM
	srp_erp_purchaserequestdetails purchasereqdetail
	LEFT JOIN srp_erp_purchaserequestmaster purchasereqmaster ON purchasereqmaster.purchaseRequestID = purchasereqdetail.purchaseRequestID
	LEFT JOIN srp_erp_purchaseorderdetails purchaseorderdetial ON purchaseorderdetial.prDetailID = purchasereqdetail.purchaseRequestDetailsID
	LEFT JOIN srp_erp_purchaseordermaster purchaseordermaster ON purchaseordermaster.purchaseOrderID = purchaseorderdetial.purchaseOrderID
	LEFT JOIN srp_erp_suppliermaster suppmaster ON suppmaster.supplierAutoID = purchaseordermaster.supplierID
	LEFT JOIN srp_erp_segment ON srp_erp_segment.segmentID = purchaseordermaster.segmentID
    LEFT JOIN (
	SELECT
		CONCAT( 'GRV', grvdetails.grvDetailsID ) AS masterIDcode,
		grvmaster.grvPrimaryCode AS systemcode,
		grvdetails.purchaseOrderDetailsID AS purchasedetId,
		grvdetails.grvDetailsID AS masterDetailID,
		grvdetails.purchaseOrderMastertID AS purchaseOrderMastertID,
		grvmaster.grvDate AS grvbsiDate,
		grvdetails.receivedQty AS qty,
		grvdetails.purchaseOrderDetailsID AS poorderID,
		grvmaster.documentID  as grvbsidoc,
		grvmaster.grvAutoID  as grvbsimasterID,
		grvmaster.approvedYN AS grvbsiapprovedYN
	FROM
		srp_erp_purchaseorderdetails purchaseorderdetial
		LEFT JOIN srp_erp_grvdetails grvdetails ON grvdetails.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
		LEFT JOIN srp_erp_grvmaster grvmaster ON grvmaster.grvAutoID = grvdetails.grvAutoID 
	GROUP BY
		grvmaster.documentID,
		grvmaster.grvAutoID,
		grvdetails.purchaseOrderDetailsID
		
		UNION ALL
	SELECT
		CONCAT( 'BSI', supplierDetail.InvoiceDetailAutoID ) AS masterIDcode,
		supplierinvmaster.bookingInvCode AS systemcode,
		supplierDetail.purchaseOrderDetailsID AS purchasedetId,
		supplierDetail.InvoiceDetailAutoID AS masterDetailID,
		supplierDetail.purchaseOrderMastertID AS purchaseOrderMastertID,
		supplierinvmaster.bookingDate AS grvbsiDate,
		supplierDetail.requestedQty AS qty,
		supplierDetail.purchaseOrderDetailsID AS poorderID,
		supplierinvmaster.documentID as grvbsidoc,
        supplierinvmaster.InvoiceAutoID  as grvbsimasterID,
		supplierinvmaster.approvedYN AS grvbsiapprovedYN
	FROM
		srp_erp_purchaseorderdetails purchaseorderdetial
		LEFT JOIN srp_erp_paysupplierinvoicedetail supplierDetail ON supplierDetail.purchaseOrderMastertID = purchaseorderdetial.purchaseOrderID
		LEFT JOIN srp_erp_paysupplierinvoicemaster supplierinvmaster ON supplierinvmaster.InvoiceAutoID = supplierDetail.InvoiceAutoID 

	GROUP BY
		supplierinvmaster.documentID ,
		supplierinvmaster.InvoiceAutoID,supplierDetail.purchaseOrderDetailsID
		
		
	) grvdetail ON grvdetail.purchasedetId = purchaseorderdetial.purchaseOrderDetailsID 
WHERE
	purchasereqmaster.companyID = $companyID 
	AND purchasereqmaster.approvedYN = 1 
	$item_autoID_filter
	$documentcodefilter_pr
    $documentcodefilter_po
    $filter_po_segment
	$third_query_where_extend")->result_array();


        $pourchaseorder = array();
        $itemmaster = array();

        $grvbsi = array();
        foreach ($results as $key => $val) {
            $grvbsi[$val['masterIDcode']] = $val;
        }
        $data['grvdetail'] = $grvbsi;

        foreach ($results as $key => $val) {
            $pourchaseorder[$val['purchaseOrderDetailsID']] = $val;
        }

        foreach ($results as $key => $val) {
            $itemmaster[$val['purchaseRequestDetailsID']] = $val;
        }
        $data['purchaseordermaster'] = $pourchaseorder;
        $data['itemdetail'] = $itemmaster;
        $data['grvdetail'] = $grvbsi;
        $dataCount = count($data['purchaserequestmaster']);
        $thisPageEndNumber = $page + $dataCount;


        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['view'] = $this->load->view('system/procurement/report/load_pr_to_grv',$data,true);
        echo json_encode($data);
    }

    function export_excel_pr_to_grv_report()
    {
        $filename = 'PR To GRV Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache

        $header = [ '#', 'PR Number', 'PR Date', 'PR Comment', 'PR Approved', 'Item Code', 'Item Description', 'Unit', 'PR Qty', 'PO Number', 'ETA',
            'Supplier Code', 'Supplier Name', 'PO Qty', 'Currency','Segment', 'PO Cost', 'Confirmed Date', 'PO Approved Status', 'Approved Date',
            'Receipt Doc Number', 'Receipt Date', 'Receipt Qty', 'Receipt Status'];

        $details = $this->Procurement_modal->fetch_pr_to_grv_details();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setTitle('PR To GRV Report');
        $activeWorksheet = $spreadsheet->getActiveSheet()->fromArray([current_companyName()]);

        $activeWorksheet->setCellValue('A1', current_companyName());
        $activeWorksheet->mergeCells('A1:J1');

        $activeWorksheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $activeWorksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $activeWorksheet->setCellValue('A2', 'PR To GRV Report');
        $activeWorksheet->mergeCells('A2:J2');

        $activeWorksheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $activeWorksheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $activeWorksheet->fromArray($header, null, 'A4');

        $activeWorksheet->getStyle('A4:X4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');

        $activeWorksheet->getStyle('A4:X4')->getFont()->setBold(true)->setSize(11)->setName('Calibri');

        $activeWorksheet->fromArray($details, null, 'A6');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    function fetch_line_tax_and_vat()
    {
        echo json_encode($this->Procurement_modal->fetch_line_tax_and_vat());
    }

    function load_project_segmentBase_multiple_dn()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $detailID = trim($this->input->post('detailID') ?? '');
        $ex_segment = explode("|", $segment);
        $this->db->select('headerID as projectID, projectDescription as projectName');
        $this->db->from('srp_erp_boq_header');
        $this->db->where('companyID', $companyID);
        $this->db->where('segementID', $ex_segment[0]);
        $result = $this->db->get()->result_array();
        $data_arr = array('' => 'Select Project');
        if (isset($result)) {
            foreach ($result as $row) {
                $data_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('projectID[]', $data_arr, '', ' class="projectID" id="projectID_' . $detailID . '" onchange="load_project_segmentBase_category(this,this.value)"');
    }
    function fetch_tax_drop_itemwise(){ 
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $data = fetch_line_wise_itemTaxFormulaID($itemAutoID,'taxMasterAutoID','taxDescription',2);
        echo json_encode($data);
    }
    function fetch_rcmDetails()
    {

        $supplierAutoID = trim($this->input->post('supplierID') ?? '');
        $data['isEligibleRCM'] = fetch_rcmDetails($supplierAutoID);
        echo json_encode($data);
    }

    function fetch_supplier()
    {
        $supplier_arr = array();
        $activeStatus = $this->input->post("activeStatus");
        $status_filter='';
        if (!empty($activeStatus)) {
            if($activeStatus==1){
                $status_filter = "AND isActive = 1 ";
            }elseif($activeStatus==2){
                $status_filter = "AND isActive = 0 ";
            }else{
                $status_filter = '';
            }
        }
        $companyID = current_companyID();
        
        $customer= $this->db->query("SELECT supplierAutoID,supplierName,supplierSystemCode,supplierCountry 
                                          FROM `srp_erp_suppliermaster` WHERE masterApprovedYN = 1 AND `companyID` = $companyID AND (deletedYN = 0 OR deletedYN IS NULL)  $status_filter ")->result_array();
        if (isset($customer)) {
            foreach ($customer as $row) {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('supplierAutoID[]', $supplier_arr, '', 'class="form-control" id="supplierAutoID" onchange="startMasterSearch()" multiple="" ');
    }
  
   function load_commision_report_details()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];

        $from_date = trim($this->input->post('from_date') ?? '');
        $format_from_date = null;
        if (isset($from_date) && !empty($from_date)) {
            $format_from_date = input_format_date($from_date, $date_format_policy);
        }
        $to_date = trim($this->input->post('to_date') ?? '');
        $format_to_date = null;
        if (isset($to_date) && !empty($to_date)) {
            $format_to_date = input_format_date($to_date, $date_format_policy);
        }
        $date = "";
        if (!empty($from_date) && !empty($to_date)) {
            $date .= " AND ( po.documentDate >= '" . $format_from_date . " 00:00:00' AND po.documentDate <= '" . $format_to_date . " 23:59:00')";
        }

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $filter_supplierID = '';
        if (isset($supplierID) && !empty($supplierID)) {
            $filter_supplierID = " AND po.supplierID = {$supplierID}";
        }

        //$text = trim($this->input->post('searchOrder') ?? '');
        // $search_string = '';
        // if (isset($text) && !empty($text)) {
        //     $search_string = " AND po.supplierName Like '%" . $text . "%'";
        // }

        //$statusID = trim($this->input->post('statusID') ?? '');
        // $filter_statusID = '';
        // if (isset($statusID) && !empty($statusID)) {
        //     $filter_statusID = " AND status = {$statusID}";
        // }

        $where = "pod.companyID = " . $companyID . $date . $filter_supplierID;
        
        $this->db->select("	supplier.supplierAutoID,
        supplier.supplierSystemCode,
        supplier.supplierName,
        po.purchaseOrderID,
        po.purchaseOrderCode,
        pod.purchaseOrderDetailsID,
        so.contractAutoID,
        so.contractCode,
        co.customerOrderID,
        co.customerOrderCode,
        cus.customerName,
        pod.commision_value,
        SUM( pod.commision_value ) AS commision_total");
        $this->db->from('srp_erp_purchaseorderdetails pod');
        $this->db->join('srp_erp_purchaseordermaster po', 'po.purchaseorderID = pod.purchaseorderID', 'LEFT');
        $this->db->join('srp_erp_contractmaster so', 'so.purchaseorderID = po.purchaseorderID', 'LEFT');
        $this->db->join('srp_erp_srm_customerordermaster co', 'so.customerOrderID = co.customerOrderID', 'LEFT');
        $this->db->join('srp_erp_customermaster cus', 'co.customerID = cus.customerAutoID', 'LEFT');
        $this->db->join('srp_erp_suppliermaster supplier', 'po.supplierID = supplier.supplierAutoID', 'LEFT');
        
        $this->db->where($where);
        //$this->db->where('po.purchaseOrderType','BCO');
        //$this->db->where('po.approvedYN', 1);
        //$this->db->where('co.isBackToBack', 1);
        //$this->db->where('so.approvedYN', 1);
        $this->db->group_by('pod.purchaseOrderID');
        $data['records'] = $this->db->get()->result_array();

        $organized_records = [];
        foreach ($data['records'] as $record) {
            $supplierID = $record['supplierAutoID'];
            $organized_records[$supplierID][] = $record;
        }
        $data['organized_records'] = $organized_records;


        $this->load->view('system/procurement/report/ajax/load_commision_report_tableView', $data);
    }
    
    function delete_commision_record()
    {
        $purchaseOrderDetailsID = trim($this->input->post('purchaseOrderDetailsID') ?? '');
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderDetailsID' => $purchaseOrderDetailsID));

        //$purchaseOrderID = trim($this->input->post('purchaseOrderID') ?? '');
        //$this->db->delete('srp_erp_purchaseordermaster', array('purchaseOrderID' => $purchaseOrderID));
        return true;

    }
  
    function save_po_item_delivery_date()
    {
        echo json_encode($this->Procurement_modal->save_po_item_delivery_date());

    }

    function fetch_purchase_order_logistic()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $isReceived = $this->input->post('isReceived');
        $segmentID = $this->input->post('segmentID');
        $supplier_filter = '';
        $segment_filter = '';
        $isReceived_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        if (!empty($segmentID)) {
            $segmentID = array($this->input->post('segmentID'));
            $whereIN = "( " . join("' , '", $segmentID) . " )";
            $segment_filter = " AND segmentID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND (closedYN = 1)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        if ($isReceived != 'all') {
            if ($isReceived == 0) {
                $isReceived_filter = " AND (isReceived = 0 AND approvedYN = 5)";
            } else if ($isReceived == 1) {
                $isReceived_filter = " AND (isReceived = 1)";
            } else if ($isReceived == 2) {
                $isReceived_filter = " AND (isReceived = 2 )";
            } else if ($isReceived == 3) {
                $isReceived_filter = " AND (closedYN = 1)";
            }
        }
        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = " AND (( purchaseOrderCode Like '%$search%' ESCAPE '!') OR ( purchaseOrderType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (narration Like '%$sSearch%') OR (srp_erp_suppliermaster.supplierName Like '%$sSearch%') OR (documentDate Like '%$sSearch%') OR (expectedDeliveryDate Like '%$sSearch%') OR (transactionCurrency Like '%$sSearch%')) ";
        }


        $where = "srp_erp_purchaseordermaster.companyID = " . $companyid . $supplier_filter . $segment_filter . $date . $status_filter . $searches . $isReceived_filter . " AND logisticYN = 1";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaseordermaster.purchaseOrderID as purchaseOrderID,srp_erp_purchaseordermaster.companyCode,purchaseOrderCode,narration,srp_erp_suppliermaster.supplierName as supliermastername,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'$convertFormat') AS expectedDeliveryDate,transactionCurrency,purchaseOrderType ,srp_erp_purchaseordermaster.createdUserID as createdUser,srp_erp_purchaseordermaster.transactionAmount,transactionCurrencyDecimalPlaces,(det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0) as total_value,ROUND((det.transactionAmount- generalDiscountAmount)+IFNULL(gentax.gentaxamount,0),2) as detTransactionAmount,isDeleted,DATE_FORMAT(documentDate,'$convertFormat') AS documentDate,documentDate AS documentDatepofilter,isReceived,closedYN,srp_erp_purchaseordermaster.confirmedByEmpID as confirmedByEmp");
        $this->datatables->join('(SELECT SUM(totalAmount)+ifnull(SUM(taxAmount),0) as transactionAmount,purchaseOrderID,IFNULL(SUM(discountAmount),0) as discountAmount  FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID) det', '(det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->join('(SELECT ifnull(SUM(amount),0) as gentaxamount,documentMasterAutoID FROM srp_erp_taxledger WHERE documentID = "PO" AND documentDetailAutoID is null AND companyID=' . $companyid . '  GROUP BY documentMasterAutoID) gentax', '(gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID)', 'left');
        $this->datatables->from('srp_erp_purchaseordermaster');
        $this->datatables->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID', 'left');
        $this->datatables->add_column('po_detail', '$1', 'load_details(narration,supliermastername,expectedDeliveryDate,transactionCurrency,purchaseOrderType,documentDate,purchaseOrderID)');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        $this->datatables->where('approvedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PO",purchaseOrderID)');
        $this->datatables->add_column('edit', '$1', 'load_po_action_logistic(purchaseOrderID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->add_column('isReceivedlbl', '$1', 'po_Recived(isReceived,closedYN,' . $isReceived . ')');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
        
    }
    function get_cost_allocation_extracharges(){

        $purchaseOrderID = $this->input->post('purchaseOrderID');

        $data = array();
        $data['charge_data'] = $this->Procurement_modal->get_extra_charges_records($purchaseOrderID);

        $data['master'] = get_purchase_order_master_record($purchaseOrderID);

        $data['total_data'] = $this->Procurement_modal->get_extra_charges_records($purchaseOrderID,2);

        $data['purchaseOrderID'] = $purchaseOrderID;

        $html = $this->load->view('system/procurement/ajax/cost_extracharge', $data, true);

        echo $html;
        
    }

    function set_contract_extra_charge()
    {
        echo json_encode($this->Procurement_modal->set_contract_extra_charge());

    }

    function update_contract_extra_charge(){
        echo json_encode($this->Procurement_modal->update_contract_extra_charge());
    }
  
    function add_quotation_version_po()
    {
        echo json_encode($this->Procurement_modal->add_quotation_version_po());
    }


    function export_excel_commision_report()
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Commission Report');
        $this->load->database();

        $header = ['#', 'Supplier Name', 'PO No', 'Customer Name', 'Order No', 'Sales Order No', 'Commission Amount'];

        $details = $this->Procurement_modal->fetch_commision_report_details();

        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
                'align' => 'center',
            )
        );
        $this->excel->getActiveSheet()->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');

        $this->excel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->mergeCells("A1:J1");
        $this->excel->getActiveSheet()->fromArray([current_companyName()], null, 'A1');
        $this->excel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($styleArray);
        $this->excel->getActiveSheet()->mergeCells("A2:J2");
        $this->excel->getActiveSheet()->fromArray(['Commission Report'], null, 'A2');
        $this->excel->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);

        $z = 4;
        foreach($details['organized_records'] as $supplierID => $records) {
            if($z == 4){
                $this->excel->getActiveSheet()->getStyle('A'.$z.':G'.$z)->getFont()->setBold(true)->setSize(11)->setName('Calibri');
                $this->excel->getStyle('A'.$z.':G'.$z)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $this->excel->getActiveSheet()->fromArray($header, null, 'A'.$z);
            }

            $x = 1;
            $total = 0;
            foreach($records as $record) {
            
                $commision_value = format_number($record['commision_value'], $record['transactionCurrencyDecimalPlaces']);

                $dataRow = array(
                    $x,
                    $record['supplierName'],
                    $record['purchaseOrderCode'],
                    $record['customerName'],
                    $record['customerOrderCode'],
                    $record['contractCode'],
                    $commision_value
                );

                $z = $z + 1;
                $this->excel->getActiveSheet()->fromArray($dataRow, null, 'A'.$z);
                $x++;

                $total += (float)$commision_value;
            }

            $next = $z + 1;
            $this->excel->getActiveSheet()->getStyle("F".$next.":G".$next)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCFF');
            $this->excel->getActiveSheet()->mergeCells("A".$next.":E".$next);
            $this->excel->getActiveSheet()->setCellValue("F".$next, "Total =");
            $this->excel->getActiveSheet()->setCellValue("G".$next, $total);

            $z = $next + 1;
        }

        $filename = 'Commision Report.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel;charset=utf-16'); //mime type
        header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $objWriter = new Xlsx($this->excel);
        $objWriter->save('php://output');
    }

    function get_contract_detail(){
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');

        $this->db->select('srp_erp_contractmaster.*,srp_erp_srm_customerordermaster.supplierID,srp_erp_srm_customerordermaster.narration');
        $this->db->where('contractAutoID',$contractAutoID);
        $master = $this->db->from('srp_erp_contractmaster')->join('srp_erp_srm_customerordermaster','srp_erp_contractmaster.customerOrderID = srp_erp_srm_customerordermaster.customerOrderID','left')->get()->row_array();

        echo json_encode($master);
    }

    /**
     * Get purchase order detail by Id
     * 
     * @return void
     */
    public function getDetailsByPo()
    {
        $poId = (int)\trim($this->input->post('poAutoId') ?? '');
        echo json_encode($this->Procurement_modal->getDetailsByPo($poId));
    }

    /**
     * Confirm addons
     * 
     * @return void
     */
    public function purchaseOrderAddonConfirmation()
    {
        $poId = (int)\trim($this->input->post('purchaseOrderID') ?? '');
        echo json_encode($this->Procurement_modal->purchaseOrderAddonConfirmation($poId));
    }

    /**
     * Get logistic po related grv addon
     *
     * @return void
     */
    public function getLogisticPoAddons()
    {
        $poId = (int)\trim($this->input->post('purchaseOrderID') ?? '');
        $currentPo = (int)\trim($this->input->post('currentPurchaseOrderID') ?? '');
        echo json_encode($this->Procurement_modal->getLogisticPoAddons($poId, $currentPo));
    }

    /**
     * Save logistic po grv addons
     *
     * @return void
     */
    public function saveLogisticPoAddon()
    {
        $this->form_validation->set_rules('purchaseOrderID', 'Id', 'trim|required');
        $this->form_validation->set_rules('addonDetailID[]', 'Addon detail', 'trim|required');
        $this->form_validation->set_rules('addonAmount[]', 'Addon amount', 'trim|required');
        $this->form_validation->set_rules('matchedAmount[]', 'Matching amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(false);
        } else {
            $poId = (int)\trim($this->input->post('purchaseOrderID') ?? '');
            $data = $this->input->post();

            if(in_array(0, $data['matchedAmount'])){
                $this->session->set_flashdata('e', 'Match amount cannot be zero');
                echo json_encode(false);
                exit;
            }

            echo json_encode($this->Procurement_modal->saveLogisticPoAddon($poId, $data));
        }

    }

    /**
     * Get logistic po grv addons by id
     *
     * @return void
     */
    public function getLogisticPoAddonByID()
    {
        $logisticId = (int)\trim($this->input->post('logisticId') ?? '');
        echo json_encode($this->Procurement_modal->getLogisticPoAddonByID($logisticId));
    }

    /**
     * Update logistic po grv addons by id
     *
     * @return void
     */
    public function updateLogisticPoAddon()
    {
        $this->form_validation->set_rules('poLogisticID', 'Id', 'trim|required');
        $this->form_validation->set_rules('matchedAmount', 'Matching amount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(false);
        } else {
            $logisticId = (int)\trim($this->input->post('poLogisticID') ?? '');

            $existingData = $this->Procurement_modal->getLogisticPoAddonByID($logisticId);

            if (true === empty($existingData)){
                $this->session->set_flashdata('w', 'Document not found');
                echo json_encode(false);
                exit;
            }

            $data = $this->input->post();
            $data['addonBalance'] = $existingData['addonBalance'];
            echo json_encode($this->Procurement_modal->updateLogisticPoAddon($data));
        }

    }

    /**
     * Delete logistic po grv addons by id
     *
     * @return void
     */
    public function deleteLogisticPoAddon(){
        $logisticId = (int)\trim($this->input->post('poLogisticID') ?? '');

        $existingData = $this->Procurement_modal->getLogisticPoAddonByID($logisticId);

        if (true === empty($existingData)){
            $this->session->set_flashdata('w', 'Document not found');
            echo json_encode(false);
            exit;
        }

        echo json_encode($this->Procurement_modal->deleteLogisticPoAddon($logisticId));

    }

    function fetch_PO_attachments()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
    
        $detailID = $this->input->post('deatilID'); 
        $PurchaseId = $this->input->post('PurchaseId');
    
        $this->db->select('prDetailID, prMasterID');
        $this->db->from('srp_erp_purchaseorderdetails');
        $this->db->where('purchaseOrderID', $PurchaseId);
        $this->db->where('purchaseOrderDetailsID', $detailID);
        $query = $this->db->get();
        $prq = $query->row_array();
    
        $this->db->from('srp_erp_documentattachments');
        $this->db->where('documentSystemCode', $PurchaseId);
        $this->db->where('documentID', 'PO');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('documentDetailID', $detailID);
        $query1 = $this->db->get_compiled_select();
    
        $union_query = $query1; 
    
        if ($prq) {
            $this->db->from('srp_erp_documentattachments');
            $this->db->where('documentSystemCode', $prq['prMasterID']);
            $this->db->where('documentID', 'PRQ');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('documentDetailID', $prq['prDetailID']);
            $query2 = $this->db->get_compiled_select();
    
            $union_query = $query1 . ' UNION ALL ' . $query2;
        }
    
        $query = $this->db->query($union_query);
        $data = $query->result_array();
    
        $confirmedYN = $this->input->post('confirmedYN');
        $view_modal = $this->input->post('view_modal');
        $result = '';
        $x = 1;
    
        $query2_ids = $prq ? array_column($this->db->query($query2)->result_array(), 'attachmentID') : [];
    
        if (!empty($data)) {
            foreach ($data as $val) {
                $burl = base_url("attachments") . '/' . $val['myFileName'];
                $type = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';
                if ($val['fileType'] == '.xlsx') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xls') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.xlsxm') {
                    $type = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.doc') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.docx') {
                    $type = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.ppt') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.pptx') {
                    $type = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.jpeg') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.gif') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.png') {
                    $type = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                } else if ($val['fileType'] == '.txt') {
                    $type = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                }
    
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');
                $is_query2 = in_array($val['attachmentID'], $query2_ids);
    
                if ($view_modal == 1) {
                    $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                } else {
                    if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                        $delete_button = $is_query2 ? '' : ' | <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a>';
                        $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; ' . $delete_button . '</td></tr>';
                    } else {
                        $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                    }
                }
                $x++;

            }
        } else {
            $result = '<tr class="danger"><td colspan="5" class="text-center">'.$this->lang->line('common_no_attachment_found').'</td></tr>';
        }
        echo json_encode($result);
    }

    


}