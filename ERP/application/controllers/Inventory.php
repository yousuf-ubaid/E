<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Inventory_modal');
        $this->load->helpers('inventory');
        $this->load->helpers('exceedmatch');
        $this->load->helper('configuration');
    }

    function fetch_material_issue()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        $fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $userGroupID=getUserGroupId();
    
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( issueDate >= '" . $datefromconvert . " 00:00:00' AND issueDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,companyCode,itemIssueCode,comment,employeeName,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,wareHouseDescription,DATE_FORMAT(issueDate,'.$convertFormat.') AS issueDate,issueType,det.totalValue as tot_value,isDeleted,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_itemissuemaster.confirmedByEmpID as confirmedByEmp, srp_erp_itemissuemaster.issueRefNo AS issueRefNo");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
        $this->datatables->where($where);
	    if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_itemissuemaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='".$userGroupID."') ";
            $this->datatables->where($where2);
        }
        $this->datatables->from('srp_erp_itemissuemaster');
        $this->datatables->add_column('MI_detail', '<b>Request By : </b> $2 <br> <b>Warehouse : </b> $1 <br> <b>Issue Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $4 <br> <b> Ref No : </b> $5', 'wareHouseDescription,employeeName,issueDate,issueType,issueRefNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MI",itemIssueAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MI",itemIssueAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_material_issue_action(itemIssueAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_batch_details_byId()
    {
        echo json_encode($this->Inventory_modal->fetch_batch_details_byId());
    }

    function fetch_existing_batch_details(){
        echo json_encode($this->Inventory_modal->fetch_existing_batch_details());
    }


    function fetch_material_issue_mc()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);


        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( issueDate >= '" . $datefromconvert . " 00:00:00' AND issueDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,companyCode,itemIssueCode,comment,employeeName,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,wareHouseDescription,DATE_FORMAT(issueDate,'.$convertFormat.') AS issueDate,issueType,det.totalValue as tot_value,isDeleted,companyLocalCurrencyDecimalPlaces,companyLocalCurrency");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_itemissuemaster');
        $this->datatables->add_column('MI_detail', '<b>Request By : </b> $2 <br> <b>Warehouse : </b> $1 <br> <b>Issue Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $4', 'wareHouseDescription,employeeName,issueDate,issueType');
        /*$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');*/
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MI",itemIssueAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MI",itemIssueAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_material_issue_action_mc(itemIssueAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_stock_transfer()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( tranferDate >= '" . $datefromconvert . " 00:00:00' AND tranferDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }
            else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $this->datatables->select("stockTransferAutoID,confirmedYN,tranferDate,approvedYN,createdUserID,receivedYN,stockTransferCode, form_wareHouseCode , form_wareHouseLocation , form_wareHouseDescription,to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,isDeleted,confirmedByEmpID, srp_erp_stocktransfermaster.referenceNo AS referenceNo");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stocktransfermaster');
        $this->datatables->add_column('st_detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6 <br> <b>Ref No : </b>$7', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription, referenceNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('received', '$1', 'confirm(receivedYN)');
        $this->datatables->add_column('edit', '$1', 'load_stock_transfer_action(stockTransferAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('tranferDate', '<span >$1 </span>', 'convert_date_format(tranferDate)');
        echo $this->datatables->generate();
    }

    function fetch_stock_adjustment_table()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
	$fshowallsegmentYN = getPolicyValues('UGSE', 'All');
	$userGroupID=getUserGroupId();    
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( stockAdjustmentDate >= '" . $datefromconvert . " 00:00:00' AND stockAdjustmentDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,stockAdjustmentCode,comment, stockAdjustmentDate ,wareHouseCode,wareHouseLocation,wareHouseDescription,isDeleted,confirmedByEmpID, srp_erp_stockadjustmentmaster.referenceNo AS referenceNo ");
        $this->datatables->where($where);
	if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_stockadjustmentmaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='".$userGroupID."') ";
            $this->datatables->where($where2);
	   }
        $this->datatables->from('srp_erp_stockadjustmentmaster');
        $this->datatables->add_column('st_detail', '$1 - $2 - $3 ', 'wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->datatables->add_column('details', '$1 <br> <b> Ref No : </b>$2', 'comment, referenceNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_adjustment_action(stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('stockAdjustmentDate', '<span >$1 </span>', 'convert_date_format(stockAdjustmentDate)');
        echo $this->datatables->generate();
    }

    function fetch_stock_return_table()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $supplier = $this->input->post('supplierPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $supplier_filter = '';
        if (!empty($supplier)) {
            $supplier = array($this->input->post('supplierPrimaryCode'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $supplier_filter = " AND supplierID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 23:59:00')";
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
        $this->datatables->select("stockReturnAutoID,confirmedYN,approvedYN,createdUserID,stockReturnCode,comment,returnDate,wareHouseCode,wareHouseLocation,transactionCurrency,wareHouseDescription,supplierName,isDeleted,confirmedByEmpID, srp_erp_stockreturnmaster.referenceNo AS referenceNo");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockreturnmaster');
        $this->datatables->add_column('sr_detail', '<b>From : </b> $1', 'wareHouseLocation');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SR",stockReturnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SR",stockReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_return_action(stockReturnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('returnDate', '<span >$1 </span>', 'convert_date_format(returnDate)');
        echo $this->datatables->generate();
    }

    function save_material_issue_header()
    {
        $date_format_policy = date_format_policy();
        $isuDt = $this->input->post('issueDate');
        $issueType = trim($this->input->post('issueType') ?? '');
        $issueDate = input_format_date($isuDt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('issueType', 'Issue Type', 'trim|required');
       
            $companyID = current_companyID();
            $warehouseAutoID = $this->input->post('location');
            $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$warehouseAutoID} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');
            if($mfqWarehouseAutoID)
            {
                $this->form_validation->set_rules('jobID', 'Job Number', 'trim|required');
                $this->form_validation->set_rules('jobNumber', 'Job Number', 'trim|required'); 
            }
     
        $this->form_validation->set_rules('issueDate', 'Issue Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('location', 'Warehouse Location', 'trim|required');

        if ($issueType == 'Material Request') {
            $this->form_validation->set_rules('requested_location', 'Requested Warehouse', 'trim|required');
        } else {
            $this->form_validation->set_rules('segment', 'Segment', 'trim|required');
            $this->form_validation->set_rules('employeeName', 'Employee', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($issueDate >= $financePeriod['dateFrom'] && $issueDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Inventory_modal->save_material_issue_header());
                } else {
                    $this->session->set_flashdata('e', 'Date Issued not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Inventory_modal->save_material_issue_header());
            }
        }
    }

    function save_stock_return_header()
    {
        //$this->form_validation->set_rules('issueType', 'Issue Type', 'trim|required');
        //$this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $date_format_policy = date_format_policy();
        $rtndt = $this->input->post('returnDate');
        $returnDate = input_format_date($rtndt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $this->form_validation->set_rules('supplierID', 'Supplier ID', 'trim|required');
        $this->form_validation->set_rules('returnDate', 'Return Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($returnDate >= $financePeriod['dateFrom'] && $returnDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Inventory_modal->save_stock_return_header());
                } else {
                    $this->session->set_flashdata('e', 'Purchase Return Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Inventory_modal->save_stock_return_header());
            }
        }
    }

    function load_stock_return_conformation()
    {
        $stockReturnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockReturnAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_return_data($stockReturnAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_purchasereturn();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/inventory/erp_stock_return_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_material_issue_conformation()
    {
        $itemIssueAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('itemIssueAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_data($itemIssueAutoID);
        $data['type'] = $this->input->post('html');
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_material_issue();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        if ($this->input->post('html')) {
            $html = $this->load->view('system/inventory/erp_material_issue_print', $data, true);
            echo $html;
        } else {
            $printlink = print_template_pdf('MI','system/inventory/erp_material_issue_print');
            $papersize = print_template_paper_size('MI','A4-L');
            $pdfp = $this->load->view($printlink, $data, true);

            $this->load->library('pdf');
            $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN']);
        }
    }

    function load_stock_transfer_conformation()
    {
        $stockTransferAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockTransferAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_transfer($stockTransferAutoID);
        $data['approval'] = $this->input->post('approval');
        $data['type'] = $this->input->post('html');
    
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_stock_transfer();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $printlink = print_template_pdf('ST','system/inventory/erp_stock_transfer_print');
      
        $html = $this->load->view($printlink, $data, true);


        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }
    function load_stock_transfer_conformation_buyback()
    {
        $stockTransferAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockTransferAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_transfer($stockTransferAutoID);
        $data['approval'] = $this->input->post('approval');

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_stock_transfer();
        } else {
            $data['signature'] = '';
        }
        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN,printFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'BSI');
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN']= $printHeaderFooterYN;
        
        $html = $this->load->view('system/inventory/erp_stock_transfer_print_buyback', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $html = $this->load->view('system/inventory/erp_stock_transfer_printView_buyback', $data, true);
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $printSizeText='A5-L';
            }else{
                $printSizeText='A4';
            }
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'],0);
        }
    }

    function load_stock_adjustment_conformation()
    {
        $stockAdjustmentAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_adjustment($stockAdjustmentAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_stock_adjustment();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/inventory/erp_stock_adjustment_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_material_issue_header()
    {
        echo json_encode($this->Inventory_modal->load_material_issue_header());
    }

    function laad_stock_return_header()
    {
        echo json_encode($this->Inventory_modal->load_stock_return_header());
    }

    function laad_stock_transfer_header()
    {
        echo json_encode($this->Inventory_modal->laad_stock_transfer_header());
    }

    function referback_stock_return()
    {
        $stockReturnAutoID = $this->input->post('stockReturnAutoID');

        $this->db->select('approvedYN,stockReturnCode');
        $this->db->where('stockReturnAutoID', trim($stockReturnAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stockreturnmaster');
        $approved_inventory_stock_return = $this->db->get()->row_array();
        if (!empty($approved_inventory_stock_return)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_stock_return['stockReturnCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockReturnAutoID, 'SR');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function laad_stock_adjustment_header()
    {
        echo json_encode($this->Inventory_modal->laad_stock_adjustment_header());
    }

    function fetch_stockTransfer_detail_table()
    {
        echo json_encode($this->Inventory_modal->fetch_stockTransfer_detail_table());
    }

    function fetch_stock_adjustment_detail()
    {
        echo json_encode($this->Inventory_modal->fetch_stock_adjustment_detail());
    }

    function fetch_item_for_grv()
    {
        echo json_encode($this->Inventory_modal->fetch_item_for_grv());
    }

    function fetch_inv_item()
    {
        echo json_encode($this->Inventory_modal->fetch_inv_item());
    }

    function fetch_inv_item_stock_adjustment()
    {
        echo json_encode($this->Inventory_modal->fetch_inv_item_stock_adjustment());
    }

    function delete_return_detail()
    {
        echo json_encode($this->Inventory_modal->delete_return_detail());
    }

    function save_stock_transfer_header()
    {
        $jobNumberMandatory = getPolicyValues('JNP', 'All');
        $date_format_policy = date_format_policy();
        $trfrDt = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        $this->form_validation->set_rules('transferType', 'Transfer Type', 'trim|required');
        $this->form_validation->set_rules('tranferDate', 'Transfer Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('form_location', 'Form location', 'trim|required');
        $this->form_validation->set_rules('to_location', 'To location', 'trim|required');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if($jobNumberMandatory == 1) {
            $companyID = current_companyID();
            $warehouseAutoID = $this->input->post('to_location');
            $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$warehouseAutoID} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');
            if($mfqWarehouseAutoID) {
                $this->form_validation->set_rules('jobID', 'Job', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
            $financearray = $this->input->post('financeyear_period');
            $financePeriod = fetchFinancePeriod($financearray);
            if ($tranferDate >= $financePeriod['dateFrom'] && $tranferDate <= $financePeriod['dateTo']) {
                if (trim($this->input->post('form_location') ?? '') != trim($this->input->post('to_location') ?? '')) {
                    echo json_encode($this->Inventory_modal->save_stock_transfer_header());
                } else {
                    $this->session->set_flashdata('e', 'From location and to location cannot be same !');
                    echo json_encode(FALSE);
                }

            } else {
                $this->session->set_flashdata('e', 'Transfer Date not between Financial period !');
                echo json_encode(FALSE);
            }
        }else{
                if (trim($this->input->post('form_location') ?? '') != trim($this->input->post('to_location') ?? '')) {
                    echo json_encode($this->Inventory_modal->save_stock_transfer_header());
                } else {
                    $this->session->set_flashdata('e', 'From location and to location cannot be same !');
                    echo json_encode(FALSE);
                }
            }
        }
    }

    function save_stock_adjustment_header()
    {
        $date_format_policy = date_format_policy();
        $stkAdntDte = $this->input->post('stockAdjustmentDate');
        $stockAdjustmentDate = input_format_date($stkAdntDte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        $this->form_validation->set_rules('stockAdjustmentDate', 'Adjustment Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('location', 'location', 'trim|required');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($stockAdjustmentDate >= $financePeriod['dateFrom'] && $stockAdjustmentDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Inventory_modal->save_stock_adjustment_header());
                } else {
                    $this->session->set_flashdata('e', 'Adjustment Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Inventory_modal->save_stock_adjustment_header());
            }
        }
    }

    function save_stock_transfer_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        $transferType = $this->input->post('transferType');
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('transfer_QTY', 'Transfer Quantity', 'trim|required|greater_than[0]');
        if($transferType == 'standard') {
            $this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        }

        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($advanceCostCapturing == 1){
            $this->form_validation->set_rules("activityCode", 'Activity Code', 'required|trim');
        }

        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $this->form_validation->set_rules("batch_number[]", 'Batch Number', 'trim|required');
        }
        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail());
        }
    }

    function save_stock_transfer_detail_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
            
            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }

            $advanceCostCapturing = getPolicyValues('ACC', 'All');
            if($advanceCostCapturing == 1){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
            }

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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_multiple());
        }

    }

    function save_stock_return_detail()
    {
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemSystemCode', 'item System Code', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasure', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('return_Qty', 'Return Quantity', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_stock_return_detail());
        }
    }

    function save_return_item_detail()
    {
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemSystemCode', 'item System Code', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('itemDescription', 'Item', 'trim|required');
        $this->form_validation->set_rules('UnitOfMeasure', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('return_Qty', 'Return Quantity', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_return_item_detail());
        }
    }

    function save_stock_adjustment_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStock', 'Current Stock', 'trim|required');
        $this->form_validation->set_rules('currentWac', 'Current Wac', 'trim|required');
        $this->form_validation->set_rules('adjustment_Stock', 'Adjustment Stock', 'trim|required');
        $this->form_validation->set_rules('adjustment_wac', 'Adjustment Wac', 'trim|required');
        $this->form_validation->set_rules('a_segment', 'Segment ', 'trim|required');

        if( $itemBatchPolicy==1){
            $this->form_validation->set_rules('batchNumber', 'Batch Number', 'trim|required');
            $this->form_validation->set_rules('expireDate', 'Batch Expire Date', 'trim|required');
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
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail());
        }
    }

    function save_stock_adjustment_detail_multiple()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');
        $itemBatchPolicy = getPolicyValues('IB', 'All');
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $type = $this->input->post('type');
        $batchNumber=$this->input->post('batchNumber');
        $expireDate=$this->input->post('expireDate');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("currentWareHouseStock[{$key}]", 'Current Stock', 'trim|required');
            $this->form_validation->set_rules("currentWac[{$key}]", 'Current Wac', 'trim|required');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batchNumber[{$key}]", 'Batch Number', 'trim|required');
                $this->form_validation->set_rules("expireDate[{$key}]", 'Batch Expire Date', 'trim|required');
            }
            
            if($type == 0)
            {
                $this->form_validation->set_rules("adjustment_Stock[{$key}]", 'Adjustment Stock', 'trim|required');
            }else
            {
                $this->form_validation->set_rules("adjustment_wac[{$key}]", 'Adjustment Wac', 'trim|required');
            }
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'project Category', 'trim|required');
                }
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail_multiple());
        }
    }

    function save_material_detail()
    {
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        if (!$this->input->post('materialIssueType') == 'Material Request') {
            $this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        }
        $itemBatchPolicy = getPolicyValues('IB', 'All');

        if($itemBatchPolicy==1){
            $this->form_validation->set_rules("batch_number[]", 'Batch Number', 'trim|required');
        }
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Issued Qty', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        if ($projectExist == 1 && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        
        $advanceCostCapturing = getPolicyValues('ACC', 'All');
        if($advanceCostCapturing == 1){
            $this->form_validation->set_rules("activityCode", 'Activity Code', 'required|trim');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_material_detail());
        }
    }

    function save_material_detail_multiple()
    {
        $projectID=$this->input->post('projectID');
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Issued Qty', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
            
            $advanceCostCapturing = getPolicyValues('ACC', 'All');
            if($advanceCostCapturing == 1){
                $this->form_validation->set_rules("activityCode[{$key}]", 'Activity Code', 'required|trim');
            }

            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
            }

            if ($projectExist == 1 && !empty($projectID[$key])) {
                //$this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
                if($cat_mandetory == 1) {
                    $this->form_validation->set_rules("project_categoryID[{$key}]", 'Project Category', 'trim|required');
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
            echo json_encode($this->Inventory_modal->save_material_detail_multiple());
        }
    }

    function save_grv_base_items()
    {
        $this->form_validation->set_rules('grvDetailsID[]', 'GRV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_grv_base_items());
        }
    }

    function fetch_stock_return_detail()
    {
        $data['master'] = $this->Inventory_modal->load_stock_return_header();
        $data['stockReturnAutoID'] = trim($this->input->post('stockReturnAutoID') ?? '');
        $data['supplierID'] = $data['master']['supplierID'];
        $this->load->view('system/inventory/stock_return_detail', $data);
    }

    function fetch_material_item_detail()
    {
        echo json_encode($this->Inventory_modal->fetch_material_item_detail());
    }

    function fetch_return_direct_details()
    {
        echo json_encode($this->Inventory_modal->fetch_return_direct_details());
    }

    function delete_material_item()
    {
        echo json_encode($this->Inventory_modal->delete_material_item());
    }

    function delete_adjustment_item()
    {
        echo json_encode($this->Inventory_modal->delete_adjustment_item());
    }

    function delete_material_issue_header()
    {
        echo json_encode($this->Inventory_modal->delete_material_issue_header());
    }

    function load_material_item_detail()
    {
        echo json_encode($this->Inventory_modal->load_material_item_detail());
    }

    function load_stock_transfer_item_detail()
    {
        echo json_encode($this->Inventory_modal->load_stock_transfer_item_detail());
    }

    function material_item_confirmation()
    {
        echo json_encode($this->Inventory_modal->material_item_confirmation());
    }

    function stock_transfer_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_transfer_confirmation());
    }

    function delete_stock_adjustment()
    {
        echo json_encode($this->Inventory_modal->delete_stock_adjustment());
    }

    function stock_return_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_return_confirmation());
    }

    function stock_adjustment_confirmation()
    {
        echo json_encode($this->Inventory_modal->stock_adjustment_confirmation());
    }

    function load_adjustment_item_detail()
    {
        echo json_encode($this->Inventory_modal->load_adjustment_item_detail());
    }

    function fetch_warehouse_item()
    {
        echo json_encode($this->Inventory_modal->fetch_warehouse_item());
    }
    function fetch_warehouse_item_new()
    {
        echo json_encode($this->Inventory_modal->fetch_warehouse_item_new());
    }

    function fetch_st_warehouse_item()
    {
        echo json_encode($this->Inventory_modal->fetch_st_warehouse_item());
    }
    function fetch_st_warehouse_item_new()
    {
        echo json_encode($this->Inventory_modal->fetch_st_warehouse_item_new());
    }
    function fetch_warehouse_item_adjustment()
    {
        echo json_encode($this->Inventory_modal->fetch_warehouse_item_adjustment());
    }

    function referback_materialissue()
    {
        $itemIssueAutoID = $this->input->post('itemIssueAutoID');

        $this->db->select('approvedYN,itemIssueCode');
        $this->db->where('itemIssueAutoID', trim($itemIssueAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_itemissuemaster');
        $approved_inventory_mi = $this->db->get()->row_array();
        if (!empty($approved_inventory_mi)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_mi['itemIssueCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($itemIssueAutoID, 'MI');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function delete_purchase_return()
    {
        echo json_encode($this->Inventory_modal->delete_purchase_return());
    }

    function fetch_material_issue_approval()
    {

        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
	  $fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $userGroupID=getUserGroupId();    
        if($approvedYN == 0)
        {

            $this->datatables->select('srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,itemIssueCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_itemissuemaster.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,det.totalValue as tot_value,ROUND(det.totalValue, 2) as tot_value_search,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_itemissuemaster.issueRefNo AS issueRefNo', false);
            $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
            $this->datatables->from('srp_erp_itemissuemaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_itemissuemaster.itemIssueAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_itemissuemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_itemissuemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'MI');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'MI');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_itemissuemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
		 if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_itemissuemaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='".$userGroupID."') ";
            $this->datatables->where($where2);
        }
            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
            $this->datatables->add_column('itemIssueCode', '$1', 'approval_change_modal(itemIssueCode,itemIssueAutoID,documentApprovedID,approvalLevelID,approvedYN,MI,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MI", itemIssueAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_issue_action_approval(itemIssueAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,itemIssueCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_itemissuemaster.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,det.totalValue as tot_value,ROUND(det.totalValue,2) as tot_value_search,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_itemissuemaster.issueRefNo AS issueRefNo', false);
            $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
            $this->datatables->from('srp_erp_itemissuemaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_itemissuemaster.itemIssueAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'MI');
            $this->datatables->where('srp_erp_itemissuemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
		 if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_itemissuemaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='".$userGroupID."') ";
            $this->datatables->where($where2);
        }
            $this->datatables->group_by('srp_erp_itemissuemaster.itemIssueAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
            $this->datatables->add_column('itemIssueCode', '$1', 'approval_change_modal(itemIssueCode,itemIssueAutoID,documentApprovedID,approvalLevelID,approvedYN,MI,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MI", itemIssueAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_issue_action_approval(itemIssueAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }


    function fetch_material_issue_approval_mc()
    {

        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,itemIssueCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_itemissuemaster.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,det.totalValue as tot_value,ROUND(det.totalValue, 2) as tot_value_search,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_itemissuemaster.issueRefNo AS issueRefNo', false);
            $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
            $this->datatables->from('srp_erp_itemissuemaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_itemissuemaster.itemIssueAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_itemissuemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_itemissuemaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'MI');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'MI');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_itemissuemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
            $this->datatables->add_column('itemIssueCode', '$1', 'approval_change_modal_buyback(itemIssueCode,itemIssueAutoID,documentApprovedID,approvalLevelID,approvedYN,MI,mc,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MI", itemIssueAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_issue_action_approval_mc(itemIssueAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_itemissuemaster.itemIssueAutoID as itemIssueAutoID,itemIssueCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_itemissuemaster.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(issueDate,\'' . $convertFormat . '\') AS issueDate,det.totalValue as tot_value, ROUND(det.totalValue, 2) as tot_value_search,companyLocalCurrencyDecimalPlaces,companyLocalCurrency, srp_erp_itemissuemaster.issueRefNo AS issueRefNo', false);
            $this->datatables->join('(SELECT SUM(totalValue) as totalValue,itemIssueAutoID FROM srp_erp_itemissuedetails GROUP BY itemIssueAutoID) det', '(det.itemIssueAutoID = srp_erp_itemissuemaster.itemIssueAutoID)', 'left');
            $this->datatables->from('srp_erp_itemissuemaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_itemissuemaster.itemIssueAutoID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'MI');
            $this->datatables->where('srp_erp_itemissuemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_itemissuemaster.itemIssueAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
            $this->datatables->add_column('itemIssueCode', '$1', 'approval_change_modal_buyback(itemIssueCode,itemIssueAutoID,documentApprovedID,approvalLevelID,approvedYN,MI,mc,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MI", itemIssueAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_issue_action_approval_mc(itemIssueAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }


    }


    function fetch_stock_adjustment_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuser = current_userID();
	$fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $userGroupID=getUserGroupId();   

        if($approvedYN == 0)
        {
            $this->datatables->select('stockAdjustmentAutoID,stockAdjustmentCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate, srp_erp_stockadjustmentmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockadjustmentmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockadjustmentmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockadjustmentmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SA');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SA');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stockadjustmentmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
		
        if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_stockadjustmentmaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        } 
            $this->datatables->add_column('stockAdjustmentCode', '$1', 'approval_change_modal(stockAdjustmentCode,stockAdjustmentAutoID,documentApprovedID,approvalLevelID,approvedYN,SA,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SA", stockAdjustmentAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_adjustment_action_approval(stockAdjustmentAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();

        }else
        {
            $this->datatables->select('stockAdjustmentAutoID,stockAdjustmentCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate, srp_erp_stockadjustmentmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockadjustmentmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID ');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'SA');
            $this->datatables->where('srp_erp_stockadjustmentmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
     if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_stockadjustmentmaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        } 
            $this->datatables->group_by('srp_erp_stockadjustmentmaster.stockAdjustmentAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');


            $this->datatables->add_column('stockAdjustmentCode', '$1', 'approval_change_modal(stockAdjustmentCode,stockAdjustmentAutoID,documentApprovedID,approvalLevelID,approvedYN,SA,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SA", stockAdjustmentAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_adjustment_action_approval(stockAdjustmentAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function fetch_stock_adjustment_approval_buyback()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('stockAdjustmentAutoID,stockAdjustmentCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate', false);
            $this->datatables->from('srp_erp_stockadjustmentmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockadjustmentmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockadjustmentmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SA');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SA');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stockadjustmentmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('stockAdjustmentCode', '$1', 'approval_change_modal_buyback(stockAdjustmentCode,stockAdjustmentAutoID,documentApprovedID,approvalLevelID,approvedYN,SA,buy,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SA", stockAdjustmentAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_adjustment_action_approval_buyback(stockAdjustmentAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $companyID = $this->common_data['company_data']['company_id'];
            $this->datatables->select('stockAdjustmentAutoID,stockAdjustmentCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockAdjustmentDate,\'' . $convertFormat . '\') AS stockAdjustmentDate', false);
            $this->datatables->from('srp_erp_stockadjustmentmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockadjustmentmaster.stockAdjustmentAutoID');


            $this->datatables->where('srp_erp_documentapproved.documentID', 'SA');
            $this->datatables->where('srp_erp_stockadjustmentmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('srp_erp_stockadjustmentmaster.stockAdjustmentAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('stockAdjustmentCode', '$1', 'approval_change_modal_buyback(stockAdjustmentCode,stockAdjustmentAutoID,documentApprovedID,approvalLevelID,approvedYN,SA,buy,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SA", stockAdjustmentAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_adjustment_action_approval_buyback(stockAdjustmentAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function fetch_stock_return_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('stockReturnAutoID,stockReturnCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') as returnDate, srp_erp_stockreturnmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockreturnmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockreturnmaster.stockReturnAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockreturnmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockreturnmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SR');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SR');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stockreturnmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('stockReturnCode', '$1', 'approval_change_modal(stockReturnCode,stockReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SR,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SR",stockReturnAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_return_action_approval(stockReturnAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('stockReturnAutoID,stockReturnCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(returnDate,\'' . $convertFormat . '\') as returnDate, srp_erp_stockreturnmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockreturnmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockreturnmaster.stockReturnAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SR');
            $this->datatables->where('srp_erp_stockreturnmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_stockreturnmaster.stockReturnAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('stockReturnCode', '$1', 'approval_change_modal(stockReturnCode,stockReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SR,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SR",stockReturnAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_return_action_approval(stockReturnAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }


    }

    function fetch_stock_transfer_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
	$fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $userGroupID=getUserGroupId();    
        if($approvedYN == 0)
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate, srp_erp_stocktransfermaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stocktransfermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster.stockTransferAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stocktransfermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stocktransfermaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'ST');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'ST');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stocktransfermaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
 if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_stocktransfermaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        } 
		

            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,ST,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
            //$this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ST", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate, srp_erp_stocktransfermaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stocktransfermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster.stockTransferAutoID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'ST');
            $this->datatables->where('srp_erp_stocktransfermaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $this->common_data['current_userID']);
		if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_stocktransfermaster.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        } 
            $this->datatables->group_by('srp_erp_stocktransfermaster.stockTransferAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');


            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,ST,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
            //$this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ST", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }



    function fetch_stock_transfer_approval_buyback()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuserid = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate', false);
            $this->datatables->from('srp_erp_stocktransfermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster.stockTransferAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stocktransfermaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stocktransfermaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'ST');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'ST');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stocktransfermaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal_buyback(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,ST,buy,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
            //$this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ST", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate', false);
            $this->datatables->from('srp_erp_stocktransfermaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster.stockTransferAutoID');

            $this->datatables->where('srp_erp_documentapproved.documentID', 'ST');
            $this->datatables->where('srp_erp_stocktransfermaster.companyID', $companyID);
            $this->datatables->group_by('srp_erp_stocktransfermaster.stockTransferAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');


            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal_buyback(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,ST,buy,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
            //$this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "ST", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_material_issue_approval()
    {
        $system_code = trim($this->input->post('itemIssueAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'MI', $level_id);
            if ($approvedYN) {
                // $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(array('w', 'Document already approved', 1));
            } else {
                $this->db->select('itemIssueAutoID');
                $this->db->where('itemIssueAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_itemissuemaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(array('w', 'Document already rejected', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('itemIssueAutoID', 'Material Issue ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_material_issue_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('itemIssueAutoID');
            $this->db->where('itemIssueAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_itemissuemaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'MI', $level_id);
                if (!empty($rejectYN)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('itemIssueAutoID', 'Material Issue ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_material_issue_approval());
                    }
                }
            }
        }
    }

    function save_stock_adjustment_approval()
    {
        $system_code = trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SA', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockAdjustmentAutoID');
                $this->db->where('stockAdjustmentAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stockadjustmentmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockAdjustmentAutoID', 'Stock Adjustment ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_adjustment_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockAdjustmentAutoID');
            $this->db->where('stockAdjustmentAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stockadjustmentmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SA', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockAdjustmentAutoID', 'Stock Adjustment ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_adjustment_approval());
                    }
                }
            }
        }
    }

    function save_stock_transfer_approval()
    {
        $system_code = trim($this->input->post('stockTransferAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'ST', $level_id);
            if ($approvedYN) {
                //$this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(array('w', 'Document already approved'), 1);
            } else {
                $this->db->select('stockTransferAutoID');
                $this->db->where('stockTransferAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stocktransfermaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(array('w', 'Document already rejected'), 1);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_transfer_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stocktransfermaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'ST', $level_id);
                if (!empty($rejectYN)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_transfer_approval());
                    }
                }
            }
        }
    }


    function save_stock_return_approval()
    {
        $system_code = trim($this->input->post('stockReturnAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SR', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockReturnAutoID');
                $this->db->where('stockReturnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stockreturnmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockReturnAutoID', 'Purchase Return ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_return_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockReturnAutoID');
            $this->db->where('stockReturnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stockreturnmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SR', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockReturnAutoID', 'Purchase Return ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_stock_return_approval());
                    }
                }
            }
        }
    }

    function delete_purchaseReturn_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_purchaseReturn_attachement());
    }

    function delete_material_Issue_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_material_Issue_attachement());
    }

    function delete_stockTransfer_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_stockTransfer_attachement());
    }

    function delete_stockAdjustment_attachement()
    {
        echo json_encode($this->Inventory_modal->delete_stockAdjustment_attachement());
    }

    function referback_stock_transfer()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');

        $this->db->select('approvedYN,stockTransferCode');
        $this->db->where('stockTransferAutoID', trim($stockTransferAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stocktransfermaster');
        $approved_inventory_grv_stock_transfer = $this->db->get()->row_array();
        if (!empty($approved_inventory_grv_stock_transfer)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_grv_stock_transfer['stockTransferCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockTransferAutoID, 'ST');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function referback_stock_adjustment()
    {
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');

        $this->db->select('approvedYN,stockAdjustmentCode');
        $this->db->where('stockAdjustmentAutoID', trim($stockAdjustmentAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stockadjustmentmaster');
        $approved_inventory_stock_adjustment = $this->db->get()->row_array();
        if (!empty($approved_inventory_stock_adjustment)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_stock_adjustment['stockAdjustmentCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockAdjustmentAutoID, 'SA');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function delete_stockTransfer_details()
    {
        echo json_encode($this->Inventory_modal->delete_stockTransfer_details());
    }

    function delete_stocktransfer_master()
    {
        echo json_encode($this->Inventory_modal->delete_stocktransfer_master());
    }

    /** Created on 16-05-2017 */
    function fetch_sales_return_table()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerPrimaryCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or(confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
        $this->datatables->select("salesReturnAutoID,confirmedYN,approvedYN,createdUserID,salesReturnCode,comment,returnDate,wareHouseCode,wareHouseLocation,transactionCurrency,wareHouseDescription,customerName,isDeleted,confirmedByEmpID,referenceNo");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_salesreturnmaster');
        $this->datatables->add_column('sr_detail', '<b>From : </b> $1', 'wareHouseLocation');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_sales_return_action(salesReturnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('returnDate', '<span >$1 </span>', 'convert_date_format(returnDate)');
        echo $this->datatables->generate();
    }

    function createNewSalesReturn()
    {
        $data['id'] = '';
        $this->load->view('system/inventory/erp_sales_return', $data);
    }

    function save_sales_return_header()
    {
        $date_format_policy = date_format_policy();
        $rtndt = $this->input->post('returnDate');
        $returnDate = input_format_date($rtndt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('customerID', 'Supplier ID', 'trim|required');
        $this->form_validation->set_rules('returnDate', 'Return Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($returnDate >= $financePeriod['dateFrom'] && $returnDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Inventory_modal->save_sales_return_header());
                } else {
                    $this->session->set_flashdata('e', 'Purchase Return Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Inventory_modal->save_sales_return_header());
            }
        }
    }

    function load_sales_return_conformation()
    {
        $salesReturnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesReturnAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_sales_return_data($salesReturnAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/inventory/erp_sales_return_print', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_sales_return_header()
    {
        echo json_encode($this->Inventory_modal->load_sales_return_header());
    }

    function fetch_sales_return_detail()
    {
        $data['master'] = $this->Inventory_modal->load_sales_return_header();
        $data['stockReturnAutoID'] = trim($this->input->post('salesReturnAutoID') ?? '');
        $data['customerID'] = $data['master']['customerID'];
        $this->load->view('system/inventory/sales_return_detail', $data);
    }

    function fetch_sales_return_details()
    {
        echo json_encode($this->Inventory_modal->fetch_sales_return_details());
    }

    function fetch_item_for_sales_return()
    {
        echo json_encode($this->Inventory_modal->fetch_item_for_sales_return());
    }

    function save_sales_return_detail_items()
    {
        $this->form_validation->set_rules('invoiceDetailsAutoID[]', 'CINV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['e', validation_errors()]);
        } else {
            echo json_encode($this->Inventory_modal->save_sales_return_detail_items());
        }
    }

    function delete_sales_return_detail()
    {
        echo json_encode($this->Inventory_modal->delete_sales_return_detail());
    }

    function sales_return_confirmation()
    {
        echo json_encode($this->Inventory_modal->sales_return_confirmation());
    }

    function delete_sales_return()
    {
        echo json_encode($this->Inventory_modal->delete_sales_return());
    }

    function fetch_sales_return_approval()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $currentuser = current_userID();
        if($approvedYN == 0)
        {
            $this->datatables->select('masterTbl.salesReturnAutoID as masterAutoID, salesReturnCode as documentCode, `comment` as narration,srp_erp_customermaster.customerName as customerName, confirmedYN, srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID, DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS documentDate, det.totalValue as total_value, ROUND(det.totalValue, 2) as total_value_search,transactionCurrencyDecimalPlaces, transactionCurrency,masterTbl.referenceNo  as referenceNo', false);
            $this->datatables->join('(SELECT SUM((totalValue + IFNULL(taxAmount, 0)) - IFNULL(rebateAmount, 0)) as totalValue,salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY salesReturnAutoID) det', '(det.salesReturnAutoID = masterTbl.salesReturnAutoID)', 'left');
            $this->datatables->from('srp_erp_salesreturnmaster masterTbl');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = masterTbl.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.salesReturnAutoID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SLR');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SLR');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,salesReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SLR,0)');
            $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SLR",masterAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval(masterAutoID,approvalLevelID,approvedYN,documentApprovedID,SLR)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('masterTbl.salesReturnAutoID as masterAutoID, salesReturnCode as documentCode, `comment` as narration,srp_erp_customermaster.customerName as customerName, confirmedYN, srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID, DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS documentDate, det.totalValue as total_value, ROUND(det.totalValue, 2) as total_value_search,transactionCurrencyDecimalPlaces, transactionCurrency,masterTbl.referenceNo  as referenceNo', false);
            $this->datatables->join('(SELECT SUM((totalValue + IFNULL(taxAmount, 0)) - IFNULL(rebateAmount, 0)) as totalValue,salesReturnAutoID FROM srp_erp_salesreturndetails detailTbl GROUP BY salesReturnAutoID) det', '(det.salesReturnAutoID = masterTbl.salesReturnAutoID)', 'left');
            $this->datatables->from('srp_erp_salesreturnmaster masterTbl');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = masterTbl.customerID', 'left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.salesReturnAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SLR');
            $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuser);
            $this->datatables->group_by('masterTbl.salesReturnAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,salesReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SLR,0)');
            $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SLR",masterAutoID)');
            $this->datatables->add_column('edit', '$1', 'inv_action_approval(masterAutoID,approvalLevelID,approvedYN,documentApprovedID,SLR)');
            echo $this->datatables->generate();
        }

    }

    function save_sales_return_approval()
    {
        $system_code = trim($this->input->post('salesReturnAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SLR', $level_id);
            if ($approvedYN) {
                echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
            } else {
                $this->db->select('salesReturnAutoID');
                $this->db->where('salesReturnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_salesreturnmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('salesReturnAutoID');
            $this->db->where('salesReturnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_salesreturnmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'SLR', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('status', 'Approval Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval());
                    }
                }
            }
        }
    }

    function save_sales_return_approval_buyback()
    {
        $system_code = trim($this->input->post('salesReturnAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SLR', $level_id);
            if ($approvedYN) {
                echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
            } else {
                $this->db->select('salesReturnAutoID');
                $this->db->where('salesReturnAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_salesreturnmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval_buyback());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('salesReturnAutoID');
            $this->db->where('salesReturnAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_salesreturnmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('error' => 1, 'message' => 'Document already rejected'));
            } else {
                $rejectYN = checkApproved($system_code, 'SLR', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('error' => 1, 'message' => 'Document already approved'));
                } else {
                    $this->form_validation->set_rules('status', 'Approval Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('salesReturnAutoID', 'Sales Return ID ', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('error' => 1, 'message' => validation_errors()));

                    } else {
                        echo json_encode($this->Inventory_modal->save_sales_return_approval_buyback());
                    }
                }
            }
        }
    }

    function referback_sales_return()
    {
        $salesReturnAutoID = $this->input->post('salesReturnAutoID');

        $this->db->select('approvedYN,salesReturnCode');
        $this->db->where('salesReturnAutoID', trim($salesReturnAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_salesreturnmaster');
        $approved_sales_return = $this->db->get()->row_array();
        if (!empty($approved_sales_return)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_sales_return['salesReturnCode']));
        } else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($salesReturnAutoID, 'SLR');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }

    }

    function re_open_inventory()
    {
        echo json_encode($this->Inventory_modal->re_open_inventory());
    }

    function re_open_stock_return()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_return());
    }

    function re_open_material_issue()
    {
        echo json_encode($this->Inventory_modal->re_open_material_issue());
    }

    function re_open_stock_transfer()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_transfer());
    }

    function re_open_stock_adjestment()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_adjestment());
    }

    function stockadjustmentAccountUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->stockadjustmentAccountUpdate());
        }


    }

    function stockAdjustment_load_gldropdown()
    {
        $companyID = current_companyID();
        $data['PLGLAutoID'] = $this->input->post('PLGLAutoID');
        $data['BLGLAutoID'] = $this->input->post('BLGLAutoID');
        $master = $this->db->query("select masterAccountYN from srp_erp_chartofaccounts WHERE GLAutoID={$data['BLGLAutoID']}")->row_array();
        $data['masterAccountYN'] = $master['masterAccountYN'];
        $costGL = $this->db->query("SELECT systemAccountCode, GLAutoID, GLDescription FROM srp_erp_chartofaccounts WHERE controllAccountYN=0 and isBank=0 and accountCategoryTypeID!=4 AND isActive = 1 AND masterAccountYN = 0 AND companyID = $companyID")->result_array();

        $data_arr = array('' => 'Select GL Code');
        if (isset($costGL)) {
            foreach ($costGL as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }

        }
        $data['costGL'] = $data_arr;

        echo $html = $this->load->view('system/inventory/stock_adjustment-account-change', $data, TRUE);
    }

    function materialAccountUpdate()
    {
        $this->form_validation->set_rules('PLGLAutoID', 'Cost GL Account', 'trim|required');
        if ($this->input->post('BLGLAutoID')) {
            $this->form_validation->set_rules('BLGLAutoID', 'Asset GL Account', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->materialAccountUpdate());
        }
    }


    function fetch_stockTransfer_all_detail_edit()
    {
        echo json_encode($this->Inventory_modal->fetch_stockTransfer_all_detail_edit());
    }


    function save_stock_transfer_detail_edit_all_multiple()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
           $itemBatchPolicy = getPolicyValues('IB', 'All');

           if($itemBatchPolicy==1){
               $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
           }
           /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_edit_all_multiple());
        }

    }

    function save_material_detail_multiple_edit()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Issued Qty', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
            $itemBatchPolicy = getPolicyValues('IB', 'All');

            if($itemBatchPolicy==1){
                $this->form_validation->set_rules("batch_number[{$key}]", 'Batch Number', 'trim|required');
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
            echo json_encode($this->Inventory_modal->save_material_detail_multiple_edit());
        }
    }


    function fetch_material_request()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);


        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( requestedDate >= '" . $datefromconvert . " 00:00:00' AND requestedDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ( approvedYN = 5 )";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("srp_erp_materialrequest.mrAutoID as mrAutoID,companyCode,MRCode,comment,employeeName,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,wareHouseDescription,DATE_FORMAT(requestedDate,'.$convertFormat.') AS requestedDate,itemType,det.totalValue as tot_value,isDeleted,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_materialrequest.confirmedByEmpID as confirmedByEmp, srp_erp_materialrequest.closedYN as closedYN, referenceNo AS referenceNo");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID) det', '(det.mrAutoID = srp_erp_materialrequest.mrAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_materialrequest');
        $this->datatables->add_column('MR_detail', '<b>Request By : </b> $2 <br> <b>Warehouse : </b> $1 <br> <b>Requested Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $4 <br> <b>Ref No : </b> $5', 'wareHouseDescription,employeeName,requestedDate,itemType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_material_request_action(mrAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp, closedYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    

    function fetch_material_request_employee()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        // $currentuserid = current_userID();
        // var_dump( $currentuserid);


        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( requestedDate >= '" . $datefromconvert . " 00:00:00' AND requestedDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ( approvedYN = 5 )";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("srp_erp_materialrequest.mrAutoID as mrAutoID,companyCode,MRCode,comment,employeeName,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,wareHouseDescription,DATE_FORMAT(requestedDate,'.$convertFormat.') AS requestedDate,itemType,det.totalValue as tot_value,isDeleted,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,srp_erp_materialrequest.confirmedByEmpID as confirmedByEmp, srp_erp_materialrequest.closedYN as closedYN, referenceNo AS referenceNo");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID) det', '(det.mrAutoID = srp_erp_materialrequest.mrAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('srp_erp_materialrequest.createdUserID', $this->common_data['current_userID']);
        $this->datatables->from('srp_erp_materialrequest');
        $this->datatables->add_column('MR_detail', '<b>Request By : </b> $2 <br> <b>Warehouse : </b> $1 <br> <b>Requested Date : </b> $3 <b>&nbsp;&nbsp; Type : </b> $4 <br> <b>Ref No : </b> $5', 'wareHouseDescription,employeeName,requestedDate,itemType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_material_request_employe_action(mrAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp, closedYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    


    function fetch_inventory_catalogue(){
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( requestedDate >= '" . $datefromconvert . " 00:00:00' AND requestedDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            } else if ($status == 5) {
                $status_filter = " AND ( approvedYN = 5 )";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("ic.mrAutoID as mrAutoID,companyCode,MRCode,supplierID,supplierName,fromDAte,toDate,comment,DATE_FORMAT(fromDate,'%Y-%m-%d') AS fromDate,DATE_FORMAT(toDate,'%Y-%m-%d') AS toDate,DATE_FORMAT(confirmedDate,'%Y-%m-%d') AS confirmedDate,confirmedYN,approvedYN,createdUserID,DATE_FORMAT(requestedDate,'.$convertFormat.') AS requestedDate,det.totalValue as tot_value,isDeleted,companyLocalCurrencyDecimalPlaces,companyLocalCurrency,ic.confirmedByEmpID as confirmedByEmp, ic.closedYN as closedYN, referenceNo AS referenceNo");
        $this->datatables->join('(SELECT SUM(totalValue) as totalValue,mrAutoID FROM srp_erp_inventorycataloguedetails GROUP BY mrAutoID) det', '(det.mrAutoID = ic.mrAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_inventorycataloguemaster as ic');
        $this->datatables->add_column('MR_detail', '<b>Supplier : </b> $1 <br><b>From Date : </b> $2 <br><b>To Date : </b> $3 <br>','supplierName,fromDate,toDate');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(tot_value,companyLocalCurrencyDecimalPlaces),companyLocalCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"MR",mrAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_inventory_catalogue_action(mrAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp, closedYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();

    }

    function save_material_request_header()
    {
        $this->form_validation->set_rules('itemType', 'Item Type', 'trim|required');
        //$this->form_validation->set_rules('segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('employeeName', 'Employee', 'trim|required');
        $this->form_validation->set_rules('requestedDate', 'Requested Date', 'trim|required|validate_date');
        //$this->form_validation->set_rules('narration', 'Narration', 'trim|required');
        $this->form_validation->set_rules('location', 'Warehouse Location', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_material_request_header());
        }
    }

    function save_inventory_catalogue_header()
    {
        $this->form_validation->set_rules('requestedDate', 'Requested Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_inventory_catalogue_header());
        }
    }


    function load_material_request_header()
    {
        echo json_encode($this->Inventory_modal->load_material_request_header());
    }

    function load_inventory_catalogue_header()
    {
        echo json_encode($this->Inventory_modal->load_inventory_catalogue_header());
    }


    function fetch_material_request_detail()
    {
        echo json_encode($this->Inventory_modal->fetch_material_request_detail());
    }

    function save_material_request_detail_multiple()
    {
        //$projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            //$this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Issued Qty', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required');
            /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
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
            echo json_encode($this->Inventory_modal->save_material_request_detail_multiple());
        }
    }

    function save_inventory_catalogue_detail_multiple(){

            //$projectExist = project_is_exist();
            $searches = $this->input->post('search');
            $itemAutoID = $this->input->post('itemAutoID');
            $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
            $salesPrice = $this->input->post('salesPrice');
    
            foreach ($searches as $key => $search) {
         
                $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
                $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
                $this->form_validation->set_rules("salesPrice[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');
    
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
                echo json_encode($this->Inventory_modal->save_inventory_catalogue_detail_multiple());
            }

    }

    function fetch_warehouse_item_material_request()
    {
        echo json_encode($this->Inventory_modal->fetch_warehouse_item_material_request());
    }

    function load_material_request_detail()
    {
        echo json_encode($this->Inventory_modal->load_material_request_detail());
    }

    function save_material_request_detail()
    {
        //$projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        //$this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('quantityRequested', 'Requested Qty', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required');
        /* if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        } */
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_material_request_detail());
        }
    }

    function load_material_request_conformation()
    {
        $mrAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('mrAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_data_MR($mrAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_material_request();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/inventory/erp_material_request_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function delete_material_request_item()
    {
        echo json_encode($this->Inventory_modal->delete_material_request_item());
    }

    function delete_material_request_header()
    {
        echo json_encode($this->Inventory_modal->delete_material_request_header());
    }


    function referback_materialrequest()
    {
        $mrAutoID = $this->input->post('mrAutoID');

        $this->db->select('approvedYN,MRCode');
        $this->db->where('mrAutoID', trim($mrAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_materialrequest');
        $approved_inventory__mr = $this->db->get()->row_array();
        if (!empty($approved_inventory__mr)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory__mr['MRCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($mrAutoID, 'MR');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function re_open_material_request()
    {
        echo json_encode($this->Inventory_modal->re_open_material_request());
    }

    function material_request_item_confirmation()
    {
        echo json_encode($this->Inventory_modal->material_request_item_confirmation());
    }


    function save_material_request_detail_multiple_edit()
    {
        //$projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            //$this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Issued Qty', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required');
            /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
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
            echo json_encode($this->Inventory_modal->save_material_request_detail_multiple_edit());
        }
    }


    function fetch_material_request_approval()
    {
        $convertFormat = convert_date_format_sql();
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuserid = current_userID();
	$fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $userGroupID=getUserGroupId();   
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_materialrequest.mrAutoID as mrAutoID,MRCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_materialrequest.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate,det.qtyRequested as tot_value, srp_erp_materialrequest.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(qtyRequested) as qtyRequested,mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID) det', '(det.mrAutoID = srp_erp_materialrequest.mrAutoID)', 'left');
            $this->datatables->from('srp_erp_materialrequest');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_materialrequest.mrAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_materialrequest.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_materialrequest.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'MR');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'MR');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_materialrequest.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_materialrequest.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        }

		
            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->add_column('MRCode', '$1', 'approval_change_modal(MRCode,mrAutoID,documentApprovedID,approvalLevelID,approvedYN,MR,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MR", mrAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_request_action_approval(mrAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_materialrequest.mrAutoID as mrAutoID,MRCode,wareHouseCode,wareHouseLocation,wareHouseDescription,srp_erp_materialrequest.employeeName as employeeName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,DATE_FORMAT(requestedDate,\'' . $convertFormat . '\') AS requestedDate,det.qtyRequested as tot_value, srp_erp_materialrequest.referenceNo AS referenceNo', false);
            $this->datatables->join('(SELECT SUM(qtyRequested) as qtyRequested,mrAutoID FROM srp_erp_materialrequestdetails GROUP BY mrAutoID) det', '(det.mrAutoID = srp_erp_materialrequest.mrAutoID)', 'left');
            $this->datatables->from('srp_erp_materialrequest');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_materialrequest.mrAutoID ');


            $this->datatables->where('srp_erp_documentapproved.documentID', 'MR');
            $this->datatables->where('srp_erp_materialrequest.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
		 if ($fshowallsegmentYN==1 || $fshowallsegmentYN=='On') {
            $where2 = "srp_erp_materialrequest.segmentID in (select segmentID from srp_erp_segment_usergroups where srp_erp_segment_usergroups.userGroupID='". $userGroupID."') ";
            $this->datatables->where($where2);
        }
            $this->datatables->group_by('srp_erp_materialrequest.mrAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            //$this->datatables->add_column('total_value', '<div class="pull-right"><b>Issued : </b> $1 </div>', 'tot_value');
            $this->datatables->add_column('MRCode', '$1', 'approval_change_modal(MRCode,mrAutoID,documentApprovedID,approvalLevelID,approvedYN,MR,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "MR", mrAutoID)');
            $this->datatables->add_column('edit', '$1', 'material_request_action_approval(mrAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');

            echo $this->datatables->generate();
        }


    }


    function save_material_request_approval()
    {
        $system_code = trim($this->input->post('mrAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'MR', $level_id);
            if ($approvedYN) {
                // $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(array('w', 'Document already approved', 1));
            } else {
                $this->db->select('mrAutoID');
                $this->db->where('mrAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_materialrequest');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(array('w', 'Document already rejected', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('mrAutoID', 'Material Request ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_material_request_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('mrAutoID');
            $this->db->where('mrAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_materialrequest');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                //$this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'MR', $level_id);
                if (!empty($rejectYN)) {
                    //$this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('mrAutoID', 'Material Request ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        //$this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_material_request_approval());
                    }
                }
            }
        }
    }

    function fetch_MR_code()
    {
        echo json_encode($this->Inventory_modal->fetch_MR_code());
    }

    function fetch_mr_detail_table()
    {
        echo json_encode($this->Inventory_modal->fetch_mr_detail_table());
    }

    function save_mr_base_items()
    {
        echo json_encode($this->Inventory_modal->save_mr_base_items());
    }
    function load_material_issue_conformation_mc()
    {
        $itemIssueAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('itemIssueAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_data($itemIssueAutoID);
        //$data['extra'] = $this->Inventory_modal->fetch_template_data_test($itemIssueAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_material_issue();
        } else {
            $data['signature'] = '';
        }
        /// $confirmation_view = template_confirmation(20,'system/inventory/erp_material_issue_print','system/inventory/erp_material_issue_print_confirmation_view_mc');
        if ($this->input->post('html')) {
            $html = $this->load->view('system/inventory/erp_material_issue_print_confirmation_view_mc', $data, true);
            echo $html;
        } else {
            $printlink = print_template_pdf('MI','system/inventory/erp_material_issue_print');
            $papersize = print_template_paper_size('MI','A4-L');
            $pdfp = $this->load->view($printlink, $data, true);

            $this->load->library('pdf');
            $pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN']);
            /*$pdf = $this->pdf->printed($pdfp, $papersize,$data['extra']['master']['approvedYN']);*/
        }
    }
    function fetch_stock_adjustment_table_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $location = $this->input->post('location');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $location_filter = '';
        if (!empty($location)) {
            $supplier = array($this->input->post('location'));
            $whereIN = "( " . join("' , '", $supplier) . " )";
            $location_filter = " AND wareHouseAutoID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( stockAdjustmentDate >= '" . $datefromconvert . " 00:00:00' AND stockAdjustmentDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,stockAdjustmentCode,comment,stockAdjustmentDate ,wareHouseCode,wareHouseLocation,wareHouseDescription,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockadjustmentmaster');
        $this->datatables->add_column('st_detail', '$1 - $2 - $3 ', 'wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SA",stockAdjustmentAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_adjustment_action_buyback(stockAdjustmentAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('stockAdjustmentDate', '<span >$1 </span>', 'convert_date_format(stockAdjustmentDate)');
        echo $this->datatables->generate();
    }
    function delete_stock_adjustment_buyback()
    {
        echo json_encode($this->Inventory_modal->delete_stock_adjustment_buyback());
    }

    function referback_stock_adjustment_buyback()
    {
        $stockAdjustmentAutoID = $this->input->post('stockAdjustmentAutoID');

        $this->db->select('approvedYN,stockAdjustmentCode');
        $this->db->where('stockAdjustmentAutoID', trim($stockAdjustmentAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stockadjustmentmaster');
        $approved_inventory_stock_adjustment = $this->db->get()->row_array();
        if (!empty($approved_inventory_stock_adjustment)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_stock_adjustment['stockAdjustmentCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockAdjustmentAutoID, 'SA');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }
    function re_open_stock_adjestment_buyback()
    {
        echo json_encode($this->Inventory_modal->re_open_stock_adjestment_buyback());
    }
    function save_stock_adjustment_detail_multiple_buyback()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $type = $this->input->post('type');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("currentWareHouseStock[{$key}]", 'Current Stock', 'trim|required');
            $this->form_validation->set_rules("currentWac[{$key}]", 'Current Wac', 'trim|required');

            if($type == 0)
            {
                $this->form_validation->set_rules("noOfItems[{$key}]", 'No Of Birds', 'trim|required');
                $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Weight', 'trim|required');
                $this->form_validation->set_rules("noOfUnits[{$key}]", 'Buckets', 'trim|required');
                $this->form_validation->set_rules("deduction[{$key}]", 'B Weight', 'trim|required');
                $this->form_validation->set_rules("adjustment_Stock[{$key}]", 'Adjustment Stock', 'trim|required');
            }else
            {
                $this->form_validation->set_rules("adjustment_wac[{$key}]", 'Adjustment Wac', 'trim|required');
            }
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail_multiple_buyback());
        }
    }

    function stock_adjustment_confirmation_buyback()
    {
        echo json_encode($this->Inventory_modal->stock_adjustment_confirmation_buyback());
    }
    function load_stock_adjustment_conformation_buyback()
    {
        $stockAdjustmentAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockAdjustmentAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_stock_adjustment($stockAdjustmentAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_stock_adjustment();
        } else {
            $data['signature'] = '';
        }
        $html = $this->load->view('system/inventory/erp_stock_adjustment_print_buyback', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $printSize = $this->uri->segment(4);
            if($printSize == 0){
                $defaultpapersize='A5-L';
            }else{
                $defaultpapersize='A4';
            }
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, $defaultpapersize, $data['extra']['master']['approvedYN']);
        }
    }
    function save_stock_adjustment_detail_buyback()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStock', 'Current Stock', 'trim|required');
        $this->form_validation->set_rules('currentWac', 'Current Wac', 'trim|required');
        $this->form_validation->set_rules('adjustment_Stock', 'Adjustment Stock', 'trim|required');
        $this->form_validation->set_rules('adjustment_wac', 'Adjustment Wac', 'trim|required');
        $this->form_validation->set_rules('a_segment', 'Segment ', 'trim|required');
        $this->form_validation->set_rules('noOfItems', 'No Of Birds ', 'trim|required');
        $this->form_validation->set_rules('grossQty', 'Gross Weight ', 'trim|required');
        $this->form_validation->set_rules('noOfUnits', 'Buckets ', 'trim|required');
        $this->form_validation->set_rules('deductionedit', 'B Weight ', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_adjustment_detail_buyback());
        }
    }
    function fetch_stock_transfer_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( tranferDate >= '" . $datefromconvert . " 00:00:00' AND tranferDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $this->datatables->select("stockTransferAutoID,confirmedYN,tranferDate,approvedYN,createdUserID,receivedYN,stockTransferCode, form_wareHouseCode , form_wareHouseLocation , form_wareHouseDescription,to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,isDeleted");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stocktransfermaster');
        $this->datatables->add_column('st_detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('received', '$1', 'confirm(receivedYN)');
        $this->datatables->add_column('edit', '$1', 'load_stock_transfer_action_buyback(stockTransferAutoID,confirmedYN,approvedYN,createdUserID,isDeleted)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('tranferDate', '<span >$1 </span>', 'convert_date_format(tranferDate)');
        echo $this->datatables->generate();
    }
    function save_stock_transfer_detail_multiple_buyback()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');

            $this->form_validation->set_rules("noOfItems[{$key}]", 'No Of Birds', 'trim|required');
            $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Weight', 'trim|required');
            $this->form_validation->set_rules("noOfUnits[{$key}]", 'Buckets', 'trim|required');
            $this->form_validation->set_rules("deduction[{$key}]", 'B Weight', 'trim|required');

            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_multiple_buyback());
        }

    }
    function save_stock_transfer_detail_buyback()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('transfer_QTY', 'Transfer Quantity', 'trim|required|greater_than[0]');

        $this->form_validation->set_rules("noOfItems", 'No Of Birds', 'trim|required');
        $this->form_validation->set_rules("grossQty", 'Gross Weight', 'trim|required');
        $this->form_validation->set_rules("noOfUnits", 'Buckets', 'trim|required');
        $this->form_validation->set_rules("deductionedit", 'B Weight', 'trim|required');

        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_buyback());
        }
    }
    function save_stock_transfer_detail_edit_all_multiple_buyback()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');

            $this->form_validation->set_rules("noOfItems[{$key}]", 'No Of Birds', 'trim|required');
            $this->form_validation->set_rules("grossQty[{$key}]", 'Gross Weight', 'trim|required');
            $this->form_validation->set_rules("noOfUnits[{$key}]", 'Bucket', 'trim|required');
            $this->form_validation->set_rules("deduction[{$key}]", 'B Weight', 'trim|required');
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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_edit_all_multiple_buyback());
        }

    }
    function fetch_sales_return_table_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerPrimaryCode');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerPrimaryCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( returnDate >= '" . $datefromconvert . " 00:00:00' AND returnDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            } else if ($status == 4) {
                $status_filter = " AND ( (confirmedYN = 2 AND approvedYN != 1) or(confirmedYN = 3 AND approvedYN != 1))";
            }else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $customer_filter . $date . $status_filter . "";
        $this->datatables->select("masterTbl.salesReturnAutoID as salesReturnAutoID,confirmedYN,approvedYN,createdUserID,salesReturnCode,comment,returnDate,wareHouseCode,wareHouseLocation,transactionCurrency,wareHouseDescription,customerName,isDeleted,confirmedByEmpID,det.totalValue as total_value, ROUND(det.totalValue, 2) as total_value_search,transactionCurrencyDecimalPlaces");
        $this->datatables->join('(SELECT (SUM((ROUND((salesPrice + ((IFNULL(taxAmount,0)))), mastertbl.transactionCurrencyDecimalPlaces) * return_Qty))) as totalValue,
            mastertbl.salesReturnAutoID 
            FROM
            srp_erp_salesreturnmaster mastertbl
            LEFT JOIN srp_erp_salesreturndetails detiltbl ON detiltbl.salesReturnAutoID = mastertbl.salesReturnAutoID
            LEFT JOIN srp_erp_customerinvoicemaster customerinvmaster ON customerinvmaster.invoiceAutoID = detiltbl.invoiceAutoID
            LEFT JOIN srp_erp_customerinvoicedetails customerinvdetail ON customerinvdetail.invoiceDetailsAutoID = detiltbl.invoiceDetailID AND customerinvdetail.invoiceAutoID = customerinvmaster.invoiceAutoID
            GROUP BY
		    mastertbl.salesReturnAutoID ) det', '(det.salesReturnAutoID = masterTbl.salesReturnAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_salesreturnmaster masterTbl');
        $this->datatables->add_column('sr_detail', '<b>From : </b> $1', 'wareHouseLocation');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SLR",salesReturnAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_sales_return_action_buyback(salesReturnAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('returnDate', '<span >$1 </span>', 'convert_date_format(returnDate)');
        echo $this->datatables->generate();
    }
    function fetch_sales_return_detail_buyback()
    {
        $data['master'] = $this->Inventory_modal->load_sales_return_header();
        $data['stockReturnAutoID'] = trim($this->input->post('salesReturnAutoID') ?? '');
        $data['customerID'] = $data['master']['customerID'];
        $this->load->view('system/inventory/sales_return_detail_buyback', $data);
    }
    function fetch_stock_return_detail_buyback()
    {
        echo json_encode($this->Inventory_modal->fetch_stock_return_detail_buyback());
    }
    function save_stock_return_detail_buyback()
    {
        $this->form_validation->set_rules('noOfItems', 'No Of Birds ', 'trim|required');
        $this->form_validation->set_rules('grossQty', 'Gross Weight ', 'trim|required');
        $this->form_validation->set_rules('noOfUnits', 'Buckets ', 'trim|required');
        $this->form_validation->set_rules('deductionedit', 'B Weight ', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_return_detail_buyback());
        }
    }
    function load_sales_return_conformation_buyback()
    {
        $salesReturnAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('salesReturnAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_sales_return_buyback_data($salesReturnAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
       
        if ($this->input->post('html')) {
            $html = $this->load->view('system/inventory/erp_sales_return_print_buyback', $data, true);
            echo $html;
        } else {
            $html = $this->load->view('system/inventory/erp_sales_return_printView_buyback', $data, true);
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
    function fetch_sales_return_approval_buyback()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */

        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $this->datatables->select('masterTbl.salesReturnAutoID as masterAutoID, salesReturnCode as documentCode, `comment` as narration,srp_erp_customermaster.customerName as customerName, confirmedYN, srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID, DATE_FORMAT(returnDate,\'' . $convertFormat . '\') AS documentDate, det.totalValue as total_value, ROUND(det.totalValue, 2) as total_value_search,transactionCurrencyDecimalPlaces, transactionCurrency', false);
        $this->datatables->join('(SELECT (SUM((ROUND((salesPrice + ((IFNULL(taxAmount,0)))), mastertbl.transactionCurrencyDecimalPlaces) * return_Qty))) as totalValue,
            mastertbl.salesReturnAutoID 
            FROM
            srp_erp_salesreturnmaster mastertbl
            LEFT JOIN srp_erp_salesreturndetails detiltbl ON detiltbl.salesReturnAutoID = mastertbl.salesReturnAutoID
            LEFT JOIN srp_erp_customerinvoicemaster customerinvmaster ON customerinvmaster.invoiceAutoID = detiltbl.invoiceAutoID
            LEFT JOIN srp_erp_customerinvoicedetails customerinvdetail ON customerinvdetail.invoiceDetailsAutoID = detiltbl.invoiceDetailID AND customerinvdetail.invoiceAutoID = customerinvmaster.invoiceAutoID
            GROUP BY
		    mastertbl.salesReturnAutoID ) det', '(det.salesReturnAutoID = masterTbl.salesReturnAutoID)', 'left');
        $this->datatables->from('srp_erp_salesreturnmaster masterTbl');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = masterTbl.customerID', 'left');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = masterTbl.salesReturnAutoID AND srp_erp_documentapproved.approvalLevelID = masterTbl.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = masterTbl.currentLevelNo');
        $this->datatables->where('srp_erp_documentapproved.documentID', 'SLR');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'SLR');
        $this->datatables->where('srp_erp_documentapproved.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('invoiceCode', '$1', 'approval_change_modal(invoiceCode,salesReturnAutoID,documentApprovedID,approvalLevelID,approvedYN,SLR,0)');
        $this->datatables->add_column('confirmed', "<div style='text-align: center'>Level $1</div>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,"SLR",masterAutoID)');
        $this->datatables->add_column('edit', '$1', 'inv_action_approval_buyback(masterAutoID,approvalLevelID,approvedYN,documentApprovedID,SLR)');
        echo $this->datatables->generate();
    }
    function save_sales_return_detail_items_buyback()
    {
        $this->form_validation->set_rules('invoiceDetailsAutoID[]', 'CINV', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->save_sales_return_detail_items_buyback());
        }
    }
    function fetch_item_for_sales_return_buyback()
    {
        echo json_encode($this->Inventory_modal->fetch_item_for_sales_return_buyback());
    }


    function fetch_stock_transfer_suom()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( tranferDate >= '" . $datefromconvert . " 00:00:00' AND tranferDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }
            else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $this->datatables->select("stockTransferAutoID,confirmedYN,DATE_FORMAT(tranferDate,'$convertFormat') AS tranferDate,approvedYN,createdUserID,receivedYN,stockTransferCode, form_wareHouseCode , form_wareHouseLocation , form_wareHouseDescription,to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,isDeleted,confirmedByEmpID");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stocktransfermaster');
        $this->datatables->add_column('st_detail', '<b>From : </b> $1 - $2 - $3 | <b> To : </b> $4 - $5 - $6', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, to_wareHouseCode ,to_wareHouseLocation ,to_wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"ST",stockTransferAutoID)');
        $this->datatables->add_column('received', '$1', 'confirm(receivedYN)');
        $this->datatables->add_column('edit', '$1', 'load_stock_transfer_action_suom(stockTransferAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function save_stock_transfer_detail_multiple_suom()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $transfer_QTY = $this->input->post('transfer_QTY');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item ', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            $this->form_validation->set_rules("transfer_QTY[{$key}]", 'Transfer Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("currentWareHouseStockQty[{$key}]", 'Current Stock', 'trim|required|greater_than[0]');
            //$this->form_validation->set_rules("SUOMIDhn[{$key}]", 'Secondary UOM', 'trim|required|greater_than[0]');
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
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail_multiple());
        }

    }


    function save_stock_transfer_detail_suom()
    {
        $projectExist = project_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('transfer_QTY', 'Transfer Quantity', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('a_segment', 'Segment', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStockQty', 'Current Stock', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('SUOMIDhn', 'Secondary UOM', 'trim|required');
        $this->form_validation->set_rules('SUOMQty', 'Secondary QTY', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Inventory_modal->save_stock_transfer_detail());
        }
    }


    function check_item_not_approved_in_document(){
        echo json_encode($this->Inventory_modal->check_item_not_approved_in_document());
    }
    function check_item_not_approved_in_document_new(){
        echo json_encode($this->Inventory_modal->check_item_not_approved_in_document_new());
    }
    function check_item_not_approved_document_wise(){
        echo json_encode($this->Inventory_modal->check_item_not_approved_document_wise());
    }

    function load_item_received_history()
    {
        //$this->form_validation->set_rules('rate', 'Rate', 'trim|required');
        //if ($this->form_validation->run() == FALSE) {
        //    $errors = validation_errors();
        //    echo '<div class="alert alert-danger">' . $errors . '</div>';
       // } else {
            $requestType = $this->uri->segment(3);
            $currency = $this->input->post('currency');
            $item = $this->input->post('items');
            $supplier = $this->input->post('supplier');
            $companyID = current_companyID();
            $column_filter = $this->input->post('columSelectionDrop');

        if (isset($item) && !empty($item)) {
            $tmpitems = join(",", $item);
            $items = $tmpitems;
        } else {
            $items = null;
        }
            $data = array();



            $data['type'] = ' ';
            $data['details'] = $this->Inventory_modal->load_item_received_history($currency,$items);
            $data['taxtype'] = $this->db->query('SELECT taxMasterAutoID,taxDescription,taxShortCode FROM `srp_erp_taxmaster` where companyID  = '.$companyID.' AND taxType = 2  ')->result_array();
            $data["columnSelectionDrop"] = $column_filter;
        if ($requestType == 'pdf') {
            $html = $this->load->view('system/inventory/report/load_item_received_history_report', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4');
        } else {
            $data['type'] = 'html';
            $data = $this->load->view('system/inventory/report/load_item_received_history_report', $data, true);
        }


            //print_r(data);
            echo json_encode($data);
        //}
        //$data = array();
        //$data['details'] = $this->Inventory_modal->load_item_received_history();
        //$data = $this->load->view('system/inventory/report/load_item_received_history_report', $data, true);
        //print_r(data);
       // echo json_encode($data);

    }

    function close_material_request()
    {
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('closedDate', 'Date', 'trim|required');
        $this->form_validation->set_rules('mrAutoID', 'Material Request ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->close_material_request());
        }
    }

    function load_foc_report()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $column_filter = $this->input->post('columSelectionDrop');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert"> Date To is  required </div>';
        } else {
            $this->form_validation->set_rules('items[]', 'Item', 'required');
            $this->form_validation->set_rules('documentID[]', 'documentID', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Inventory_modal->fetch_details_foc_report();
                $data['currency'] = $this->input->post('currency');
                $data['type'] = "html";
                $data["columnSelectionDrop"] = $column_filter;
                echo $this->load->view('system/inventory/report/load_free_of_cost', $data, true);
            }
        }
    }

    function load_foc_reportpdf()
    {
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $column_filter = $this->input->post('columSelectionDrop');

        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert"> Date To is  required </div>';
        } else {
            $this->form_validation->set_rules('items[]', 'Item', 'required');
            $this->form_validation->set_rules('documentID[]', 'documentID', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Inventory_modal->fetch_details_foc_report();
                $data['currency'] = $this->input->post('currency');
                $data['type'] = "pdf";
                $data["columnSelectionDrop"] = $column_filter;

                $html = $this->load->view('system/inventory/report/load_free_of_cost', $data, true);
                $this->load->library('pdf');
                $this->pdf->printed($html, 'A4', 1);

//                echo $this->load->view('system/inventory/report/load_free_of_cost', $data, true);
            }
        }
    }

    function update_stock_minus_qty()
    {
        $stockAdjustmentDetailsAutoID = $this->input->post('stockAdjustmentDetailsAutoID');
        foreach ($stockAdjustmentDetailsAutoID as $key => $search) {
            $this->form_validation->set_rules("stock[{$key}]", 'stock', 'trim|required');
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
            echo json_encode($this->Inventory_modal->update_stock_minus_qty());
        }
    }

    function load_movement_analysis_report()
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
            $this->form_validation->set_rules('item[]', 'Item', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details']=$this->Inventory_modal->movement_analysis_item_details();
                $data['datefrom'] = date_create($datefrom);
                $data['datefrom'] = date_format( $data['datefrom'],"d-M-Y");
                $data['dateto'] = date_create($dateto);
                $data['dateto'] = date_format($data['dateto'],"d-M-Y");
                $item_id = $this->input->post('item');
                $currency = $this->input->post('currency');
                $data['company_default_decimal'] = $this->common_data['company_data']['company_default_decimal'];
                $data['item_wac_amount'] =  $this->Inventory_modal->get_item_wac_amount($item_id,$currency);
                $data['purchase_movement'] = $this->Inventory_modal->load_purchase_movement_analysis_report();//Movement Inward
                $data['sales_movement'] = $this->Inventory_modal->load_sales_movement_analysis_report();//Movement Outward
                $data['transfers_movement'] = $this->Inventory_modal->load_transfers_movement_analysis_report();//Transfers Outward
                $data['currency'] = $this->Inventory_modal->get_currency();
                $data['type'] = "html";

                echo $this->load->view('system/inventory/report/load_movement_analysis_report', $data, true);
            }
        }
    }
    function load_movement_analysis_report_pdf()
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
            $this->form_validation->set_rules('item', 'Item', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details']=$this->Inventory_modal->movement_analysis_item_details();
                $data['datefrom'] = date_create($datefrom);
                $data['datefrom'] = date_format( $data['datefrom'],"d-M-Y");
                $data['dateto'] = date_create($dateto);
                $data['dateto'] = date_format($data['dateto'],"d-M-Y");
                $data['sales_movement'] = $this->Inventory_modal->load_sales_movement_analysis_report();
                $data['purchase_movement'] = $this->Inventory_modal->load_purchase_movement_analysis_report();
                $data['transfers_movement'] = $this->Inventory_modal->load_transfers_movement_analysis_report();
                $data['type'] = "pdf";
                $html = $this->load->view('system/inventory/report/load_movement_analysis_report', $data, true);
                $this->load->library('pdf');
                $this->pdf->printed($html, 'A4-L', 1);
            }
        }
    }

    function load_item_movement_report()
    {
        $datefrom = $this->input->post('datefrom');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $column_filter = $this->input->post('columSelectionDrop');
        if (empty($datefrom)) {
            echo ' <div class="alert alert-warning" role="alert">
              Date From is required
            </div>';
        } else if (empty($dateto)) {
            echo ' <div class="alert alert-warning" role="alert"> Date To is  required </div>';
        } else {
            $this->form_validation->set_rules('items[]', 'Item', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Inventory_modal->fetch_details_item_movement_report();
                $data['warehouse'] = $this->Inventory_modal->get_warehouse_details();
                $data['type'] = "html";
                $data["columnSelectionDrop"] = $column_filter;
                echo $this->load->view('system/inventory/report/load_item_movement_report', $data, true);
            }
        }
    }

    function load_item_movement_report_pdf()
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
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['details'] = $this->Inventory_modal->fetch_details_item_movement_report();
                $data['warehouse'] = $this->Inventory_modal->get_warehouse_details();
                $data['type'] = "pdf";

                $html = $this->load->view('system/inventory/report/load_item_movement_report', $data, true);
                $this->load->library('pdf');
                $this->pdf->printed($html, 'A4-L', 1);
            }
        }
    }

    function check_mfq_warehouse()
    {
        echo json_encode($this->Inventory_modal->check_mfq_warehouse());
    }

    function fetch_MR_code_ST()
    {
        echo json_encode($this->Inventory_modal->fetch_MR_code_ST());
    }

    function fetch_mr_detail_table_ST()
    {
        echo json_encode($this->Inventory_modal->fetch_mr_detail_table_ST());
    }

    function save_mr_base_ST_items()
    {
        echo json_encode($this->Inventory_modal->save_mr_base_ST_items());
    }

    function save_bulk_transfer_header()
    {
        $toWarehouse = $this->input->post('to_location');
        $date_format_policy = date_format_policy();
        $trfrDt = $this->input->post('tranferDate');
        $tranferDate = input_format_date($trfrDt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        //$this->form_validation->set_rules('transferType', 'Transfer Type', 'trim|required');
        $this->form_validation->set_rules('tranferDate', 'Transfer Date', 'trim|required|validate_date');
        $this->form_validation->set_rules('form_location', 'Form location', 'trim|required');
        $this->form_validation->set_rules('to_location[]', 'To location', 'trim|required');
        $this->form_validation->set_rules('receiptType', 'Receipt Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($tranferDate >= $financePeriod['dateFrom'] && $tranferDate <= $financePeriod['dateTo']) {
                    if(in_array(trim($this->input->post('form_location') ?? ''), $toWarehouse)) {
                        $this->session->set_flashdata('e', 'To location cannot have From Location!');
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Inventory_modal->save_bulk_transfer_header());
                    }
                } else {
                    $this->session->set_flashdata('e', 'Transfer Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                if(in_array(trim($this->input->post('form_location') ?? ''), $toWarehouse)) {
                    $this->session->set_flashdata('e', 'To location cannot have From Location!');
                    echo json_encode(FALSE);
                } else {
                    echo json_encode($this->Inventory_modal->save_bulk_transfer_header());
                }
            }
        }
    }

    function fetch_bulk_transfer()
    {
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( tranferDate >= '" . $datefromconvert . " 00:00:00' AND tranferDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }
            else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }
            else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $where = "companyID = " . $companyid . $date . $status_filter . "";
        $this->datatables->select("stockTransferAutoID,confirmedYN,tranferDate,approvedYN,createdUserID,receivedYN,stockTransferCode, form_wareHouseCode , form_wareHouseLocation , form_wareHouseDescription,to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,isDeleted,confirmedByEmpID, referenceNo AS referenceNo");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stocktransfermaster_bulk');
        $this->datatables->add_column('st_detail', '<b>From : </b> $1 - $2 - $3 <br> <b>Ref No : </b>$4', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription, referenceNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"STB",stockTransferAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"STB",stockTransferAutoID)');
        $this->datatables->add_column('received', '$1', 'confirm(receivedYN)');
        $this->datatables->add_column('edit', '$1', 'load_bulk_transfer_action(stockTransferAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('tranferDate', '<span >$1 </span>', 'convert_date_format(tranferDate)');
        echo $this->datatables->generate();
    }

    function load_bulk_transfer_header()
    {
        echo json_encode($this->Inventory_modal->load_bulk_transfer_header());
    }

    function fetch_bulkTransfer_details()
    {
        $data['extra'] = $this->Inventory_modal->fetch_bulkTransfer_details();
        $data['stockTransferAutoID'] = trim($this->input->post('stockTransferAutoID') ?? '');
        $this->load->view('system/inventory/bulk_transfer_detail', $data);
    }

    function fetch_sync_item() {
        $stockTransferAutoID = trim($this->input->post('stockTransferAutoID') ?? '');
        $warehouseAutoID = trim($this->input->post('warehouseAutoID') ?? '');

        $this->datatables->select('srp_erp_itemmaster.itemAutoID AS itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,IFNULL(currentStock.currentStock, 0) AS currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,srp_erp_itemcategory.description as SubCategoryDescription,CONCAT(IFNULL(currentStock.currentStock, 0),\'  \',defaultUnitOfMeasure) as CurrentStock', false);
        $this->datatables->from('srp_erp_itemmaster');
        $this->datatables->join('(SELECT IFNULL(SUM(transactionQTY/convertionRate), 0) as currentStock, itemAutoID FROM srp_erp_itemledger where  wareHouseAutoID= ' . $warehouseAutoID . ' AND companyID = ' . current_companyID() . ' GROUP BY itemAutoID)currentStock', 'currentStock.ItemAutoID = srp_erp_itemmaster.itemAutoID', 'LEFT');
        $this->datatables->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');
        if(!empty($stockTransferAutoID)) {
            $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_stocktransferdetails_bulk WHERE srp_erp_stocktransferdetails_bulk.itemAutoID = srp_erp_itemmaster.itemAutoID AND stockTransferAutoID =' . $stockTransferAutoID . ' )');
        }
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        if (!empty($this->input->post('subsubcategoryID'))) {
            $this->datatables->where('subSubCategoryID', $this->input->post('subsubcategoryID'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }

    function add_item_bulk_transfer()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->add_item_bulk_transfer());
        }
    }

    function delete_bulk_transfer_detail()
    {
        echo json_encode($this->Inventory_modal->delete_bulk_transfer_detail());
    }

    function update_bulk_transfer_qty()
    {
        $this->form_validation->set_rules('stockTransferAutoID', 'stockTransferAutoID', 'required');
        $this->form_validation->set_rules('itemAutoID', 'itemAutoID', 'required');
        $this->form_validation->set_rules('stockTransferDetailAutoID', 'stockTransferDetailAutoID', 'required');
        $this->form_validation->set_rules('transferQty', 'transferQty', 'required');
        $this->form_validation->set_rules('warehouseAutoID', 'warehouseAutoID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Inventory_modal->update_bulk_transfer_qty());
        }
    }

    function load_bulk_transfer_conformation()
    {
        $stockTransferAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockTransferAutoID') ?? '');
        $_POST['stockTransferAutoID'] = $stockTransferAutoID;
        $data['extra'] = $this->Inventory_modal->fetch_bulkTransfer_details();
        $data['stockTransferAutoID'] = $stockTransferAutoID;
        $data['master'] = $this->db->query("SELECT *, IFNULL(confirmedByName, ' - ') as confirmedYNn FROM srp_erp_stocktransfermaster_bulk WHERE stockTransferAutoID = {$stockTransferAutoID}")->row_array();
        $data['approval'] = $this->input->post('approval');
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details('STB', $stockTransferAutoID);
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signatureLevel_bulk_transfer();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $printlink = print_template_pdf('STB','system/inventory/erp_bulk_transfer_print');
        $html = $this->load->view($printlink, $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4-L', $data['master']['approvedYN']);
        }
    }

    function bulkTransfer_details()
    {
        $data = $this->Inventory_modal->fetch_bulkTransfer_details();
        if($data['details']) {
            echo '1';
        }
    }

    function bulk_transfer_confirmation()
    {
        echo json_encode($this->Inventory_modal->bulk_transfer_confirmation());
    }

    function referback_bulk_transfer()
    {
        $stockTransferAutoID = $this->input->post('stockTransferAutoID');

        $this->db->select('approvedYN,stockTransferCode');
        $this->db->where('stockTransferAutoID', trim($stockTransferAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stocktransfermaster_bulk');
        $approved_inventory_grv_stock_transfer = $this->db->get()->row_array();
        if (!empty($approved_inventory_grv_stock_transfer)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_grv_stock_transfer['stockTransferCode']));
        }else
        {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockTransferAutoID, 'STB');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function delete_bulk_transfer_master()
    {
        echo json_encode($this->Inventory_modal->delete_bulk_transfer_master());
    }

    function re_open_bulk_transfer()
    {
        echo json_encode($this->Inventory_modal->re_open_bulk_transfer());
    }

    function fetch_bulk_transfer_approval()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        if($approvedYN == 0)
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate, srp_erp_stocktransfermaster_bulk.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stocktransfermaster_bulk');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster_bulk.stockTransferAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stocktransfermaster_bulk.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stocktransfermaster_bulk.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'STB');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'STB');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stocktransfermaster_bulk.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);

            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,STB,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 ', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "STB", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'bulk_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('stockTransferAutoID,stockTransferCode,form_wareHouseCode ,form_wareHouseLocation , form_wareHouseDescription ,  to_wareHouseCode , to_wareHouseLocation,to_wareHouseDescription,confirmedYN ,srp_erp_documentapproved.approvedYN as approvedYN, approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(tranferDate,\'' . $convertFormat . '\') AS tranferDate, srp_erp_stocktransfermaster_bulk.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stocktransfermaster_bulk');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stocktransfermaster_bulk.stockTransferAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'STB');
            $this->datatables->where('srp_erp_stocktransfermaster_bulk.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $this->common_data['current_userID']);
            $this->datatables->group_by('srp_erp_stocktransfermaster_bulk.stockTransferAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');

            $this->datatables->add_column('stockTransferCode', '$1', 'approval_change_modal(stockTransferCode,stockTransferAutoID,documentApprovedID,approvalLevelID,approvedYN,STB,0)');
            $this->datatables->add_column('detail', '<b>From : </b> $1 - $2 - $3 ', 'form_wareHouseCode, form_wareHouseLocation, form_wareHouseDescription');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "STB", stockTransferAutoID)');
            $this->datatables->add_column('edit', '$1', 'bulk_transfer_action_approval(stockTransferAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }
    }

    function save_bulk_transfer_approval()
    {
        $system_code = trim($this->input->post('stockTransferAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'STB', $level_id);
            if ($approvedYN) {
                echo json_encode(array('w', 'Document already approved'), 1);
            } else {
                $this->db->select('stockTransferAutoID');
                $this->db->where('stockTransferAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stocktransfermaster_bulk');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    echo json_encode(array('w', 'Document already rejected'), 1);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_bulk_transfer_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockTransferAutoID');
            $this->db->where('stockTransferAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stocktransfermaster_bulk');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                echo json_encode(array('w', 'Document already rejected', 1));
            } else {
                $rejectYN = checkApproved($system_code, 'ST', $level_id);
                if (!empty($rejectYN)) {
                    echo json_encode(array('w', 'Document already approved', 1));
                } else {
                    $this->form_validation->set_rules('status', 'Supplier Invoice Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockTransferAutoID', 'Stock Transfer ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(array('e', validation_errors(), 1));
                    } else {
                        echo json_encode($this->Inventory_modal->save_bulk_transfer_approval());
                    }
                }
            }
        }
    }

    function delete_all_bulk_transfer_detail()
    {
        echo json_encode($this->Inventory_modal->delete_all_bulk_transfer_detail());
    }
    function check_item_not_approved_in_document_bywarehouse()
    { 
        echo json_encode($this->Inventory_modal->check_item_not_approved_in_document_bywarehouse());
    }
    function fetch_converted_price_qty_invoice()
    { 
        echo json_encode($this->Inventory_modal->fetch_converted_price_qty_invoice());
    }
    function fetch_converted_price_qty_invoice_new()
    {
        echo json_encode($this->Inventory_modal->fetch_converted_price_qty_invoice_new());
    }
    function update_quantity_sec_uom(){
        echo json_encode($this->Inventory_modal->update_quantity_sec_uom());
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
        echo form_dropdown('supplier[]', $supplier_arr, 'Each', 'class="form-control" multiple id="supplier" '); 
    }

    function fetch_statusbase_item()
    {
        $supplier_arr = array();
        $tab = $this->input->post("tab");
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
        $filter ='';
        if($tab == 1 || $tab ==2){
            $filter = "AND financeCategory = 1 AND masterApprovedYN = 1" ;
        }
        $items= $this->db->query("SELECT itemSystemCode,itemName,itemAutoID,seconeryItemCode
                                          FROM `srp_erp_itemmaster` WHERE  `companyID` = $companyID  $status_filter $filter")->result_array();
        if (isset($items)) {
            foreach ($items as $row) {
                $itemSecondaryCodePolicy = is_show_secondary_code_enabled();
                if ($itemSecondaryCodePolicy) {
                    $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');

                }else{
                    $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                }
            }
        }
        if($tab == 1){
            echo form_dropdown('item[]', $data_arr, '', 'multiple class="form-control select2" id="item" required');
        }elseif($tab==2){
            echo form_dropdown('itemSum[]', $data_arr, 'Each', 'multiple class="form-control select2" id="itemSum" required');
        }else{
            echo form_dropdown('items[]', $data_arr, 'Each', 'class="form-control" multiple id="items" ');             
        }
    }

    function calculate_average_purcahse_of_raw_material()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $orderby = $this->input->post('orderby');
        $current_date = current_date(false);
        $begining_date= date('Y-m', strtotime('-12 month', strtotime($current_date)));
        if($orderby == 1){
            $interval="1 day";
            $caption="DMY";
            $format = "Y-m-d";
        }else if($orderby == 2){
            $interval="1 week";
            $caption="DMY";
            $format = "Y-m-d";
        }else{
            $interval="1 month";
            $caption="MY";
            $format = "Y-m";
        }
        $xvalues = get_daterange_list_from_date($begining_date , $current_date , "$format", $interval, $caption);
        $data = $this->Inventory_modal->calculate_average_purcahse_of_raw_material($begining_date,$current_date,$xvalues);
        $array = array();
        foreach ($data['details'] as $details) {
            unset($details['documentDate']);
            foreach ($details as $key=>$detail) {
                $array[] = array($key, (double)$detail);
            }
        }
        if(!empty($array)){
            echo json_encode($array);
        }else{
            //echo json_encode(array(array(1167609600000,0.7537),array(1167955200000,0.7644),array(1168214400000, 0.769)));
            echo json_encode(array());
        }
    }

    function fetch_inventory_catalogue_details(){
        echo json_encode($this->Inventory_modal->fetch_inventory_catalogue_details());
    }

    function delete_inventory_catalogue_item(){
        echo json_encode($this->Inventory_modal->delete_inventory_catalogue_item());
    }

    function load_inventory_catalogue_conformation()
    {
        $mrAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('mrAutoID') ?? '');
        $data['extra'] = $this->Inventory_modal->fetch_template_data_MIC($mrAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature'] = $this->Inventory_modal->fetch_signaturelevel_material_request();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/inventory/erp_inventory_catalogue_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function inventory_catalogue_confirmation(){
        echo json_encode($this->Inventory_modal->inventory_catalogue_confirmation());
    }
}
