<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PurchaseRequest extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Purchase_request_modal');
        $this->load->helpers('purchase_request');
    }

    function fetch_purchase_request()
    {
        // date inter change according to company policy
        $jobno = '';
        $jobNumberMandatory = getPolicyValues('JNP', 'All');
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        if($jobNumberMandatory)
        {
            $jobno .='<b>Job No : </b> $6 ';
        }else
        {
            $jobno .=' ';
        }


        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
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
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }else if ($status == 5) {
                $status_filter = " AND (approvedYN = 5 AND closedYN = 1)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.confirmedByEmpID as confirmedByEmp,companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency ,createdUserID,srp_erp_purchaserequestmaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value, ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_purchaserequestmaster.isDeleted as isDeleted,employee.Ename2 as createdUserNamepurchasereq,jobNumber,versionNo as versionNo");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
        $this->datatables->join('srp_employeesdetails employee','employee.EIdNo = srp_erp_purchaserequestmaster.createdUserID','left');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->add_column('prq_detail', '<b>Requested by : </b> $2 <br> <b>Exp Delivery Date : </b> $3 <br><b>Narration : </b> $1<br><b>Created By : </b> $5 <br> '.$jobno.'', 'narration,requestedByName,expectedDeliveryDate,transactionCurrency,createdUserNamepurchasereq,jobNumber');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action(purchaseRequestID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('purchaseRequestCode', '$1', 'add_version_code(purchaseRequestCode,versionNo)');
        echo $this->datatables->generate();
    }

    function fetch_purchase_request_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $currentuserid = current_userID();

        $single_source_pr = getPolicyValues('SSPR', 'All');
        $company_doc_approval_type = getApprovalTypesONDocumentCode('PRQ',$companyID);
        
        $approvalBasedWhere='';
        $where_single_source ='';

        if($single_source_pr ==1){
           $where_single_source = " AND ((srp_erp_purchaserequestmaster.isSingleSourcePr= 1 AND srp_erp_approvalusers.criteriaID =1) or (srp_erp_purchaserequestmaster.isSingleSourcePr= 0 AND srp_erp_approvalusers.criteriaID =0))";
        }

        if($company_doc_approval_type['approvalType']==1){
            
        }else if($company_doc_approval_type['approvalType']==2){
           // $approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount';
           $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1))";
        }else if($company_doc_approval_type['approvalType']==3){
            $approvalBasedWhere = ' AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
        }else if($company_doc_approval_type['approvalType']==4){
            //$approvalBasedWhere = ' AND srp_erp_approvalusers.fromAmount  <= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.toAmount >= srp_erp_documentapproved.documentAmount AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID';
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.segmentID  = srp_erp_documentapproved.segmentID";
        }else if($company_doc_approval_type['approvalType']==5){
            $approvalBasedWhere = " AND ((srp_erp_approvalusers.toAmount != 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_approvalusers.toAmount) OR (srp_erp_approvalusers.toAmount = 0 AND srp_erp_documentapproved.documentAmount BETWEEN srp_erp_approvalusers.fromAmount AND srp_erp_documentapproved.documentAmount+1)) AND srp_erp_approvalusers.typeID  = srp_erp_documentapproved.categoryID";
        }

        $where = "(srp_erp_approvalusers.employeeID = ".$this->common_data['current_userID']." or (srp_erp_approvalusers.employeeID=-1 and srp_erp_purchaserequestmaster.requestedEmpID in (SELECT
    empmanagers.empID
FROM
    srp_employeesdetails empdetail
JOIN srp_erp_employeemanagers empmanagers on empdetail.EIdNo=empmanagers.empID and empmanagers.active=1
WHERE
  empmanagers.companyID=".$companyID." and
    empmanagers.managerID=".$this->common_data['current_userID'].")))".$approvalBasedWhere.$where_single_source."";

        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as total_value_search,det.transactionAmount as detTransactionAmount', false);
            $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
            $this->datatables->from('srp_erp_purchaserequestmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaserequestmaster.purchaseRequestID AND srp_erp_documentapproved.approvalLevelID = srp_erp_purchaserequestmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_purchaserequestmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'PRQ');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'PRQ');
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where($where);
            $this->datatables->where('srp_erp_purchaserequestmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', $approvedYN);
            $this->datatables->group_by('srp_erp_purchaserequestmaster.purchaseRequestID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('purchaseRequestCode', '$1', 'approval_change_modal(purchaseRequestCode,purchaseRequestID,documentApprovedID,approvalLevelID,approvedYN,PRQ,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PRQ",purchaseRequestID)');
            $this->datatables->add_column('edit', '$1', 'prq_action_approval(purchaseRequestID,approvalLevelID,approvedYN,documentApprovedID,PRQ)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as total_value_search,det.transactionAmount as detTransactionAmount', false);
            $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
            $this->datatables->from('srp_erp_purchaserequestmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_purchaserequestmaster.purchaseRequestID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'PRQ');
            $this->datatables->where('srp_erp_purchaserequestmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_purchaserequestmaster.purchaseRequestID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('purchaseRequestCode', '$1', 'approval_change_modal(purchaseRequestCode,purchaseRequestID,documentApprovedID,approvalLevelID,approvedYN,PRQ,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"PRQ",purchaseRequestID)');
            $this->datatables->add_column('edit', '$1', 'prq_action_approval(purchaseRequestID,approvalLevelID,approvedYN,documentApprovedID,PRQ)');
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

    function save_purchase_request_header()
    {
        //$projectExist = project_is_exist();
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('expectedDeliveryDate', 'Delivery Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('documentDate', 'PRQ Date ', 'trim|required|validate_date');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        /* if($projectExist == 1){
            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        } */

        $singleSourcePR = getPolicyValues('SSPR', 'All');
        $enableOperationM = getPolicyValues('EOM', 'All');
        $single_source_val = trim($this->input->post('single_source_val') ?? '');

        if($singleSourcePR==1 && $single_source_val==1){
            $this->form_validation->set_rules('single_narration', 'Single Source Comment ', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            $date_format_policy = date_format_policy();
            $format_expectedDeliveryDate = input_format_date($this->input->post('expectedDeliveryDate'), $date_format_policy);
            $format_POdate = input_format_date($this->input->post('documentDate'), $date_format_policy);

           if ($format_expectedDeliveryDate >= $format_POdate) {
                echo json_encode($this->Purchase_request_modal->save_purchase_request_header());
            } else {
                $this->session->set_flashdata('e', 'Expected Delivery Date should be greater than PRQ Date');
                echo json_encode(FALSE);
            }


        }
    }

    function save_purchase_request_approval()
    {
        $system_code = trim($this->input->post('purchaseRequestID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        if($status==1){
            $approvedYN=checkApproved($system_code,'PRQ',$level_id);
            if($approvedYN){
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('purchaseRequestID');
                $this->db->where('purchaseRequestID', trim($system_code));
                $this->db->where('approvedYN', 2);
                $this->db->from('srp_erp_purchaserequestmaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseRequestID', 'Purchase Request ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Purchase_request_modal->save_purchase_request_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('purchaseRequestID');
            $this->db->where('purchaseRequestID', trim($system_code));
            $this->db->where('approvedYN', 2);
            $this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_purchaserequestmaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'PRQ',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    $this->form_validation->set_rules('po_status', 'Status', 'trim|required');
                    if($this->input->post('po_status') ==2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('purchaseRequestID', 'Purchase Request ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Purchase_request_modal->save_purchase_request_approval());
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

    function save_purchase_request_detail()
    {

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $purchaseRequestID = $this->input->post('purchaseRequestID');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item ID', 'trim|required');
           /* $this->form_validation->set_rules("expectedDeliveryDateDetail[{$key}]", 'Expected Delivery Date', 'trim|required');*/
           $this->form_validation->set_rules("expectedDeliveryDateDetail[{$key}]", 'Expected Delivery Date', 'trim|required|callback_deliverydate_check['.$purchaseRequestID.']');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required|greater_than[0]');
            //$this->form_validation->set_rules("estimatedAmount[{$key}]", 'Unit Cost', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Purchase_request_modal->save_purchase_request_detail());
        }
    }

    function update_purchase_request_detail()
    {
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $estimatedAmount = trim($this->input->post('estimatedAmount') ?? '');
        $purchaseRequestID = $this->input->post('purchaseRequestID');

        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('comment', 'Comment', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Quantity Requested', 'trim|required|greater_than[0]');
        //$this->form_validation->set_rules('estimatedAmount', 'Estimated Amount', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('expectedDeliveryDateDetailEdit', 'Expected Delivery Date', 'trim|required|callback_deliverydate_check['.$purchaseRequestID.']');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Purchase_request_modal->update_purchase_request_detail());
        }
    }

    function load_purchase_request_header()
    {
        echo json_encode($this->Purchase_request_modal->load_purchase_request_header());
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
    function fetch_itemrecode_pqr()
    {
        echo json_encode($this->Purchase_request_modal->fetch_itemrecode_pqr());
    }

    function fetch_pqr_detail_table()
    {
        echo json_encode($this->Purchase_request_modal->fetch_pqr_detail_table());
    }

    function delete_purchase_request_detail()
    {
        echo json_encode($this->Purchase_request_modal->delete_purchase_request_detail());
    }

    function delete_purchase_request()
    {
        echo json_encode($this->Purchase_request_modal->delete_purchase_request());
    }

    function fetch_purchase_request_detail()
    {
        echo json_encode($this->Purchase_request_modal->fetch_purchase_request_detail());
    }

    function purchase_request_confirmation()
    {
        echo json_encode($this->Purchase_request_modal->purchase_request_confirmation());
    }

    function load_purchase_request_conformation()
    {
        $purchaseRequestID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseRequestID') ?? '');
        $json = $this->input->post('json');
        $versionhide = $this->input->post('versionhide');

        $data['extra'] = $this->Purchase_request_modal->fetch_template_data($purchaseRequestID);
        $data['approval'] = $this->input->post('approval');

        $data['version_drop'] = load_version_drop_down($purchaseRequestID,'PRQ');
        $data['versionhide'] =  $versionhide;
       
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PRQ', $purchaseRequestID);
        $data['html'] = $this->input->post('html');
       
        if (!$this->input->post('html')) {
            $data['signature']=$this->Purchase_request_modal->fetch_signaturelevel();
            $data['isPrint']=0;
            $data['isHtml'] = false; 
            $data['show_attachment_header'] = false;
        } else {
            $data['signature']='';
            $data['isPrint']=1;
            $data['isHtml'] = true;
            $data['show_attachment_header'] = true;
        }

        $this->db->select('*');
        $this->db->from('srp_erp_documentattachments');
        $this->db->where('documentID', 'PRQ');
        $this->db->where('documentDetailID !=', 0);
        $this->db->where('documentDetailID IS NOT NULL', null, false);
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentSystemCode', $purchaseRequestID);
        $result = $this->db->get()->result_array();
        $data['attachments'] = $result;

        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        if($json){
            return json_encode($data);
        }

        $html = $this->load->view('system/PurchaseRequest/erp_purchase_request_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $printlink =  print_template_pdf('PRQ', 'system/PurchaseRequest/erp_purchase_request_print');
            $html = $this->load->view($printlink, $data, true);
            $printSize = $this->uri->segment(4);
         
            if($printSize == 0 && ($printSize!='')){
            
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
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

    function referback_purchaserequest()
    {
        $purchaseRequestID = $this->input->post('purchaseRequestID');

        $this->db->select('approvedYN,purchaseRequestCode');
        $this->db->where('purchaseRequestID', trim($purchaseRequestID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_purchaserequestmaster');
        $approved_purchase_request = $this->db->get()->row_array();
        if (!empty($approved_purchase_request)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_purchase_request['purchaseRequestCode']));
        }else
         {
            $this->load->library('approvals');
            $this->load->library('CostAllocation');
            $status = $this->approvals->approve_delete($purchaseRequestID, 'PRQ');
            $costAllocation = $this->costallocation->deleteDocumentCostAllocation('PRQ', $purchaseRequestID);
            if ($status == 1 && true === $costAllocation) {
                $assignBuyersPolicy = getPolicyValues('ABFC', 'All');

                if($assignBuyersPolicy==1){

                    $this->Purchase_request_modal->delete_pr_buyers_detail($purchaseRequestID);
                }
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
        echo json_encode($this->Purchase_request_modal->re_open_procurement());
    }

    function load_project_segmentBase()
    {
        $data_arr = [];
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $ex_segment = explode(" | " , $segment);
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
        echo form_dropdown('projectID', $data_arr, '', 'class="form-control select2" id="projectID_'.$type.'"');
    }

    function load_project_segmentBase_multiple()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $ex_segment = explode(" | " , $segment);
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
        echo form_dropdown('projectID[]', $data_arr, '', 'class="form-control select2" id="projectID"');
    }

    function load_project_segmentBase_multiple_noclass()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $segment = trim($this->input->post('segment') ?? '');
        $ex_segment = explode(" | " , $segment);
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
        echo form_dropdown('projectID[]', $data_arr, '', ' id="projectID"');
    }

    function fetch_last_grn_amount(){
        echo json_encode($this->Purchase_request_modal->fetch_last_grn_amount());
    }
    function fetch_purchase_request_employee()
    {
        // date inter change according to company policy
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $jobno = '';
        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        $currentuserid = current_userID();
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( documentDate >= '" . $datefromconvert . " 00:00:00' AND documentDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $jobNumberMandatory = getPolicyValues('JNP', 'All');
        if($jobNumberMandatory)
        {
            $jobno .='<b>Job No : </b> $6 ';
        }else
        {
            $jobno .=' ';
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $supplier_filter . $date . $status_filter . " AND (requestedEmpID = '{$currentuserid}'|| createdUserID = '{$currentuserid}')";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.confirmedByEmpID as confirmedByEmp,companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency ,createdUserID,srp_erp_purchaserequestmaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_purchaserequestmaster.isDeleted as isDeleted,employee.Ename2 as createdUserNamepurchasereq,srp_erp_purchaserequestmaster.jobNumber as jobNumber");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
        $this->datatables->join('srp_employeesdetails employee','employee.EIdNo = srp_erp_purchaserequestmaster.createdUserID','left');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->add_column('prq_detail', '<b>Requested by: </b> $2 <br> <b>Exp Delivery Date : </b> $3 <b><br><b>Narration : </b> $1<br><b>Created By : </b> $5<br> '.$jobno.'', 'narration,requestedByName,expectedDeliveryDate,transactionCurrency,createdUserNamepurchasereq,jobNumber');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action_employee(purchaseRequestID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    public function deliverydate_check($expectedDeliveryDate,$purchaseRequestID)
    {
        $date_format_policy = date_format_policy();
        $podate = $this->db->query("SELECT documentDate FROM srp_erp_purchaserequestmaster WHERE purchaseRequestID = $purchaseRequestID ")->row_array();
        $format_POdate = input_format_date($podate['documentDate'], $date_format_policy);
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);

        if ($format_expectedDeliveryDate <  $format_POdate)
        {
            $this->form_validation->set_message('deliverydate_check', 'The {field} should be greater than PRQ Date');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function save_purchase_request_close()
    {
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('purchaseRequestID', 'Purchase Request ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Purchase_request_modal->save_purchase_request_close());
        }
    }
    function fetch_purchase_request_buyback()
    {
        // date inter change according to company policy
        $jobno = '';
        $jobNumberMandatory = getPolicyValues('JNP', 'All');
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        if($jobNumberMandatory)
        {
            $jobno .='<b>Job No : </b> $6 ';
        }else
        {
            $jobno .=' ';
        }


        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
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
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }

            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $supplier_filter . $date . $status_filter . "";
        $convertFormat = convert_date_format_sql();
        $this->datatables->select("srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.confirmedByEmpID as confirmedByEmp,companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency ,createdUserID,srp_erp_purchaserequestmaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value, ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_purchaserequestmaster.isDeleted as isDeleted,employee.Ename2 as createdUserNamepurchasereq,jobNumber");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
        $this->datatables->join('srp_employeesdetails employee','employee.EIdNo = srp_erp_purchaserequestmaster.createdUserID','left');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->add_column('prq_detail', '<b>Requested by : </b> $2 <br> <b>Exp Delivery Date : </b> $3 <br><b>Narration : </b> $1<br><b>Created By : </b> $5 <br> '.$jobno.'', 'narration,requestedByName,expectedDeliveryDate,transactionCurrency,createdUserNamepurchasereq,jobNumber');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action_buyback(purchaseRequestID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function validate_close_prq()
    {
        echo json_encode($this->Purchase_request_modal->validate_close_prq());
    }

    function assignItem_pr_buyer_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $purchaseRequestDetailsID = $this->input->post('purchaseRequestDetailsID');
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $text = trim($this->input->post('Search') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((srp_employeesdetails.Ename1 Like '%" . $text . "%') OR (srp_employeesdetails.ECode Like '%" . $text . "%'))";
        }

        $data['type']=$type;
        $data['purchaseRequestDetailsID']=$purchaseRequestDetailsID;
        $data['purchaseRequestID']=$purchaseRequestID;

        $data['emp'] = $this->db->query("SELECT srp_erp_incharge_assign.autoID,srp_erp_incharge_assign.empID,srp_erp_incharge_assign.assignMasterID,srp_employeesdetails.ECode,srp_employeesdetails.Ename1,srp_erp_incharge_assign.activityIsActive,srp_erp_incharge_assign.activityDetailID,srp_erp_incharge_assign.activityMasterID FROM `srp_erp_incharge_assign`
        LEFT JOIN srp_employeesdetails on srp_employeesdetails.EIdNo = srp_erp_incharge_assign.empID WHERE
        srp_erp_incharge_assign.documentID = 'PRQ'  AND srp_erp_incharge_assign.activityMasterID = {$purchaseRequestID} AND srp_erp_incharge_assign.activityDetailID ={$purchaseRequestDetailsID} AND srp_erp_incharge_assign.userType=0 AND srp_erp_incharge_assign.companyID = {$companyID} AND srp_erp_incharge_assign.empID is NOT NULL $search_string
        ")->result_array();

        $data['emp_with_category_access'] = $this->db->query("SELECT srp_erp_incharge_assign.autoID,srp_erp_incharge_assign.empID,srp_employeesdetails.ECode,srp_employeesdetails.Ename1,srp_erp_incharge_assign.activityIsActive FROM `srp_erp_incharge_assign`
        LEFT JOIN srp_employeesdetails on srp_employeesdetails.EIdNo = srp_erp_incharge_assign.empID WHERE
        srp_erp_incharge_assign.documentID = 'PRQ'  AND srp_erp_incharge_assign.activityMasterID = {$purchaseRequestID} AND srp_erp_incharge_assign.activityDetailID ={$purchaseRequestDetailsID} AND srp_erp_incharge_assign.userType=0 AND srp_erp_incharge_assign.assignMasterID IS NOT NULL AND srp_erp_incharge_assign.companyID = {$companyID} AND srp_erp_incharge_assign.empID is NOT NULL
        ")->result_array();

        $emp_arr=[];

        if(count($data['emp_with_category_access'])>0){
            foreach($data['emp_with_category_access'] as $val){
                $emp_arr[]=$val['empID'];
            }
        }
        

        if (in_array(current_userID(),$emp_arr, TRUE)){
            $data['buyer_access']=1;
        }else{
            $data['buyer_access']=0;
        }

        $this->load->view('system/PurchaseRequest/load_assign_buyers_pr_document', $data);
    }

    function add_buyers_to_document_item(){

        $this->form_validation->set_rules('buyers_for_cat[]', 'Buyers', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Purchase_request_modal->add_buyers_to_document_item());
        }
    }

    function assignItem_pr_buyer_view_document()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $purchaseRequestDetailsID = $this->input->post('purchaseRequestDetailsID');
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $text = trim($this->input->post('Search') ?? '');
        $type = trim($this->input->post('type') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((srp_employeesdetails.Ename1 Like '%" . $text . "%') OR (srp_employeesdetails.ECode Like '%" . $text . "%'))";
        }

        $data['type']=$type;

        $data['emp'] = $this->db->query("SELECT srp_erp_incharge_assign.autoID,srp_erp_incharge_assign.empID,srp_employeesdetails.ECode,srp_employeesdetails.Ename1,srp_erp_incharge_assign.activityIsActive FROM `srp_erp_incharge_assign`
        LEFT JOIN srp_employeesdetails on srp_employeesdetails.EIdNo = srp_erp_incharge_assign.empID WHERE
        srp_erp_incharge_assign.documentID = 'PRQ'  AND srp_erp_incharge_assign.activityMasterID = {$purchaseRequestID} AND srp_erp_incharge_assign.activityDetailID ={$purchaseRequestDetailsID} AND srp_erp_incharge_assign.userType=0 AND srp_erp_incharge_assign.companyID = {$companyID} AND srp_erp_incharge_assign.empID is NOT NULL $search_string
        ")->result_array();

        $emp_arr=[];

        if(count($data['emp'])>0){
            foreach($data['emp'] as $val){
                $emp_arr[]=$val['empID'];
            }
        }
        

        if (in_array(current_userID(),$emp_arr, TRUE)){
            $data['buyer_access']=1;
        }else{
            $data['buyer_access']=0;
        }

        $this->load->view('system/PurchaseRequest/load_assign_buyers_pr', $data);
    }

    function assign_buyers_pr_details()
    {
        $this->form_validation->set_rules('assignBuyersSync[]', 'Buyer Selection', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Purchase_request_modal->assign_buyers_pr_details());
        }
    }

    function remove_assign_buyers_pr()
    {
        echo json_encode($this->Purchase_request_modal->remove_assign_buyers_pr());
    }

    function remove_assign_buyers_pr_item()
    {
        echo json_encode($this->Purchase_request_modal->remove_assign_buyers_pr_item());
    }

    function remove_assign_items_line_wise()
    {
        $this->form_validation->set_rules('narration_cl', 'Comment', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Purchase_request_modal->remove_assign_items_line_wise());
        }
    }

    function save_srm_acknowledge()
    {
        $this->form_validation->set_rules('ac_comment', 'Comment', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Purchase_request_modal->save_srm_acknowledge());
        }
    }

    
    function add_technical_users_to_pr_item(){

        $this->form_validation->set_rules('technical_users[]', 'Users', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            echo json_encode($this->Purchase_request_modal->add_technical_users_to_pr_item());
        }
    }

    function load_technical_users_to_pr_item()
    {
       
        $convertFormat = convert_date_format_sql();
        $master_id = trim($this->input->post('master_id') ?? '');
        $details_id = trim($this->input->post('details_id') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuserid = current_userID();
       
        $this->datatables->select('srp_erp_incharge_assign.autoID as autoID,srp_employeesdetails.ECode,srp_employeesdetails.Ename1,srp_employeesdetails.UserName,srp_employeesdetails.EIdNo', false);
        $this->datatables->from('srp_erp_incharge_assign');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_incharge_assign.empID');
        
        $this->datatables->where('srp_erp_incharge_assign.activityMasterID', $master_id);
        $this->datatables->where('srp_erp_incharge_assign.activityDetailID',  $details_id );
        $this->datatables->where('srp_erp_incharge_assign.companyID', $companyID);
        $this->datatables->where('srp_erp_incharge_assign.userType', 1);
        $this->datatables->where('srp_erp_incharge_assign.documentID', 'PRQ');

        $this->datatables->add_column('edit', '$1', 'delete_assign_technical_users(autoID)');
        echo $this->datatables->generate();
       
    }

    function delete_assign_tech_users_pr()
    {
        echo json_encode($this->Purchase_request_modal->delete_assign_tech_users_pr());
    }

    function fetch_close_document_details()
    {
        echo json_encode($this->Purchase_request_modal->fetch_close_document_details());
    }

    function load_pr_conformation_on_srm()
    {
        $purchaseRequestID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('purchaseRequestID') ?? '');
        $data['extra'] = $this->Purchase_request_modal->fetch_template_data($purchaseRequestID);
        $data['approval'] = $this->input->post('approval');
       
        $printHeaderFooterYN = 1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();

        $printHeaderFooterYN = $result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('PRQ', $purchaseRequestID);
        $data['html'] = $this->input->post('html');
       
        if (!$this->input->post('html')) {
            $data['signature']=$this->Purchase_request_modal->fetch_signaturelevel();
            $data['isPrint']=0;
        } else {
            $data['signature']='';
            $data['isPrint']=1;
        }

        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/PurchaseRequest/erp_purchase_request_print_srm', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $printlink =  print_template_pdf('PRQ', 'system/PurchaseRequest/erp_purchase_request_print_srm');
            $html = $this->load->view($printlink, $data, true);
            $printSize = $this->uri->segment(4);
         
            if($printSize == 0 && ($printSize!='')){
            
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);
        }
    }

    function get_purchase_history(){
        
        $itemAutoID = $this->input->post('ItemAutoID');
        $documentDate = $this->input->post('documentDate');
        $data = array();
    
        $this->db->select('*');
        $this->db->from('srp_erp_itemledger');
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('documentDate <=', $documentDate);
        $query = $this->db->get();
        $data['itemdetail'] = $query->result_array(); 
        $html=$this->load->view('system/inventory/report/erp_purchase_history', $data,true);
        echo $html;
    }

    function fetch_PR_Attachments()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);

        $detailID=$this->input->post('deatilID');
        $purchaseRequestID=$this->input->post('PurchaseId');

        $this->db->where('documentSystemCode',$purchaseRequestID);
        $this->db->where('documentID', 'PRQ');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('documentDetailID', $detailID);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        $confirmedYN = $this->input->post('confirmedYN');
        $view_modal = $this->input->post('view_modal');
        $result = '';
        $x = 1;
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
                //$link = generate_encrypt_link_only($burl); // old attachment
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour'); // s3 attachment link
                if($view_modal == 1) {
                        $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; </td></tr>';
                } else {
                    if ($confirmedYN == 0 || $confirmedYN == 2 || $confirmedYN == 3) {
                        $result .= '<tr id="' . $val['attachmentID'] . '"><td>' . $x . '</td><td>' . $val['myFileName'] . '</td><td>' . $val['attachmentDescription'] . '</td><td class="text-center">' . $type . '</td><td class="text-center"><a target="_blank" href="' . $link . '" ><i class="fa fa-download" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="delete_attachments(' . $val['attachmentID'] . ',\'' . $val['myFileName'] . '\')"><span title="" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" data-original-title="Delete"></span></a></td></tr>';
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

    function add_quotation_version_po()
    {
        echo json_encode($this->Purchase_request_modal->add_quotation_version_po());
    }

    function load_purchase_request_version(){

        $id = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('id') ?? '');
        $pdf = ($this->uri->segment(5)) ? $this->uri->segment(5) : null;
        $printHeaderFooterYN = 1;
     
        $this->db->select('*');
        $this->db->from('srp_erp_document_version');
        $this->db->where('id', $id);
        $documentVersion = $this->db->get()->row_array();

        $data = array();

        $data = json_decode($documentVersion['viewjson'],true);
        
        $data['version_drop'] = load_version_drop_down($documentVersion['documentMasterID'],'PRQ');
        $data['version'] = true;
        $data['versionID'] = $id;
        $data['pdf'] = $pdf;
        $data['versionhide'] = false;

        if($pdf){
            $data['html'] = false;
        }else{
            $data['html'] = true;
        }
        

        if($pdf){
            $this->load->library('pdf');
            $printlink =  print_template_pdf('PRQ', 'system/PurchaseRequest/erp_purchase_request_print');
            $html = $this->load->view($printlink, $data, true);
            $printSizeText='A4';
            
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printHeaderFooterYN);

        }else{

            $html = $this->load->view('system/PurchaseRequest/erp_purchase_request_print', $data, true);
            echo $html;
           
        }
        
     

    }


}