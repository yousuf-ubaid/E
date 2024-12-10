<?php defined('BASEPATH') OR exit('No direct script access allowed');

class StockCounting extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Stock_counting_modal');
        $this->load->helpers('stockcounting');
        $this->load->helpers('exceedmatch');
    }

    function fetch_stock_counting_table()
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
            $date .= " AND ( stockCountingDate >= '" . $datefromconvert . " 00:00:00' AND stockCountingDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("stockCountingAutoID,confirmedYN,approvedYN,createdUserID,stockCountingCode,comment,stockCountingDate ,wareHouseCode,wareHouseLocation,wareHouseDescription,isDeleted,confirmedByEmpID, srp_erp_stockcountingmaster.referenceNo AS referenceNo");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockcountingmaster');
        $this->datatables->add_column('scnt_detail', '$1 - $2 - $3 ', 'wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->datatables->add_column('details', '$1 <br> <b> Ref No : </b>$2 ', 'comment,referenceNo');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SCNT",stockCountingAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SCNT",stockCountingAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_counting_action(stockCountingAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        $this->datatables->edit_column('stockCountingDate', '<span >$1 </span>', 'convert_date_format(stockCountingDate)');
        echo $this->datatables->generate();
    }

    function save_stock_counting_header()
    {
        $date_format_policy = date_format_policy();
        $stkAdntDte = $this->input->post('stockCountingDate');
        $stockAdjustmentDate = input_format_date($stkAdntDte, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }
        $this->form_validation->set_rules('stockCountingDate', 'Counting Date', 'trim|required|validate_date');
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
                    echo json_encode($this->Stock_counting_modal->save_stock_counting_header());
                } else {
                    $this->session->set_flashdata('e', 'Counting Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Stock_counting_modal->save_stock_counting_header());
            }
        }
    }

    function laad_stock_counting_header()
    {
        echo json_encode($this->Stock_counting_modal->laad_stock_counting_header());
    }

    function fetch_stock_counting_detail()
    {
        echo json_encode($this->Stock_counting_modal->fetch_stock_counting_detail());
    }

    function fetch_warehouse_item_adjustment()
    {
        echo json_encode($this->Stock_counting_modal->fetch_warehouse_item_adjustment());
    }

    function updateinvetorystatustype(){
        echo json_encode($this->Stock_counting_modal->updateinvetorystatustype());
    }

    
    public function get_stock_type_status() {
        $stockid = $this->input->post('stocktypeid');

        $this->db->select("id, status");
        $this->db->from('srp_erp_stockcount_type_status');
        $this->db->where('stocktypeid', $stockid);
        $this->db->where('isActive', 1);
        $data = $this->db->get()->result_array();
        // $data_arr = array('');
        if (!empty($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }
        }
        echo json_encode($data_arr); 
    }

    function save_stock_counting_detail_multiple()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searches as $key => $search) {
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("currentWareHouseStock[{$key}]", 'Current Stock', 'trim|required');
            $this->form_validation->set_rules("currentWac[{$key}]", 'Current Wac', 'trim|required');
            $this->form_validation->set_rules("adjustment_Stock[{$key}]", 'Adjustment Stock', 'trim|required');
            $this->form_validation->set_rules("adjustment_wac[{$key}]", 'Adjustment Wac', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
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
            echo json_encode($this->Stock_counting_modal->save_stock_counting_detail_multiple());
        }
    }

    function delete_counting_item()
    {
        echo json_encode($this->Stock_counting_modal->delete_counting_item());
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
            echo json_encode($this->Stock_counting_modal->stockadjustmentAccountUpdate());
        }
    }

    function load_counting_item_detail()
    {
        echo json_encode($this->Stock_counting_modal->load_counting_item_detail());
    }

    function save_stock_counting_detail()
    {
        $projectExist = project_is_exist();
        $cat_mandetory = Project_Subcategory_is_exist();
        $this->form_validation->set_rules('search', 'Item', 'trim|required');
        $this->form_validation->set_rules('itemAutoID', 'Item ID', 'trim|required');
        $this->form_validation->set_rules('unitOfMeasureID', 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules('currentWareHouseStock', 'Current Stock', 'trim|required');
        $this->form_validation->set_rules('currentWac', 'Current Wac', 'trim|required');
        $this->form_validation->set_rules('adjustment_Stock', 'Adjustment Stock', 'trim|required');
        $this->form_validation->set_rules('adjustment_wac', 'Adjustment Wac', 'trim|required');
        $this->form_validation->set_rules('a_segment', 'Segment ', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }


        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Stock_counting_modal->save_stock_counting_detail());
        }
    }

    function load_stock_counting_conformation()
    {
        $stockCountingAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockCountingAutoID') ?? '');
        $data['extra'] = $this->Stock_counting_modal->fetch_template_stock_counting($stockCountingAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature']=$this->Stock_counting_modal->fetch_signaturelevel();
        } else {
            $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        if($data['extra']['master']['approvedYN']==1)
        {
            $html = $this->load->view('system/inventory/erp_stock_counting_print_approval', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }
       else
       {
           $data['logo']=mPDFImage;
           if($this->input->post('html')){
               $data['logo']=htmlImage;
           }
           $html = $this->load->view('system/inventory/erp_stock_counting_print', $data, true);
           if ($this->input->post('html')) {
               echo $html;
           } else {
               $this->load->library('pdf');
               $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
           }
       }


    }

    function stock_counting_confirmation()
    {
        echo json_encode($this->Stock_counting_modal->stock_counting_confirmation());
    }

    function referback_stock_counting()
    {
        $stockCountingAutoID = $this->input->post('stockCountingAutoID');

        $this->db->select('approvedYN,stockCountingCode');
        $this->db->where('stockCountingAutoID', trim($stockCountingAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_stockcountingmaster');
        $approved_inventory_grv_stockcounting = $this->db->get()->row_array();
        if (!empty($approved_inventory_grv_stockcounting['approvedYN']==1)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_grv_stockcounting['stockCountingCode']));
        }else {
            $this->load->library('approvals');
            $status = $this->approvals->approve_delete($stockCountingAutoID, 'SCNT');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }

        }

    }

    function delete_stock_counting()
    {
        echo json_encode($this->Stock_counting_modal->delete_stock_counting());
    }

    function re_open_stock_counting()
    {
        echo json_encode($this->Stock_counting_modal->re_open_stock_counting());
    }

    function fetch_stock_counting_approval()
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

            $this->datatables->select('stockCountingAutoID,stockCountingCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockCountingDate,\'' . $convertFormat . '\') AS stockCountingDate, srp_erp_stockcountingmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockcountingmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockcountingmaster.stockCountingAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_stockcountingmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_stockcountingmaster.currentLevelNo');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SCNT');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'SCNT');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_stockcountingmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->add_column('stockCountingCode', '$1', 'approval_change_modal(stockCountingCode,stockCountingAutoID,documentApprovedID,approvalLevelID,approvedYN,SCNT,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SCNT", stockCountingAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_counting_action_approval_suom(stockCountingAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('stockCountingAutoID,stockCountingCode,wareHouseCode,wareHouseLocation,wareHouseDescription,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,approvalLevelID,documentApprovedID,confirmedByName,DATE_FORMAT(stockCountingDate,\'' . $convertFormat . '\') AS stockCountingDate, srp_erp_stockcountingmaster.referenceNo AS referenceNo', false);
            $this->datatables->from('srp_erp_stockcountingmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_stockcountingmaster.stockCountingAutoID');
            $this->datatables->where('srp_erp_documentapproved.documentID', 'SCNT');
            $this->datatables->where('srp_erp_stockcountingmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_stockcountingmaster.stockCountingAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('stockCountingCode', '$1', 'approval_change_modal(stockCountingCode,stockCountingAutoID,documentApprovedID,approvalLevelID,approvedYN,SCNT,0)');
            $this->datatables->add_column('detail', '$1 - $2 ( $3 )', 'wareHouseDescription,wareHouseLocation,wareHouseCode');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "SCNT", stockCountingAutoID)');
            $this->datatables->add_column('edit', '$1', 'stock_counting_action_approval_suom(stockCountingAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }


    function load_stock_counting_approval_conformation()
    {
        $stockCountingAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockCountingAutoID') ?? '');
        $data['extra'] = $this->Stock_counting_modal->fetch_template_stock_counting($stockCountingAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_counting_approval_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }


    function save_stock_counting_approval()
    {
        $system_code = trim($this->input->post('stockCountingAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, 'SCNT', $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('stockCountingAutoID');
                $this->db->where('stockCountingAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_stockcountingmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockCountingAutoID', 'Stock Counting ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Stock_counting_modal->save_stock_counting_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('stockCountingAutoID');
            $this->db->where('stockCountingAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_stockcountingmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, 'SCNT', $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('stockCountingAutoID', 'Stock Counting ID', 'trim|required');
                    $this->form_validation->set_rules('documentApprovedID', 'Document Approved ID', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Stock_counting_modal->save_stock_counting_approval());
                    }
                }
            }
        }
    }

    function updateCountingStockSingle(){
        echo json_encode($this->Stock_counting_modal->updateCountingStockSingle());
    }

    function load_subcat()
    {
        echo json_encode($this->Stock_counting_modal->load_subcat());
    }

    function load_subsubcat()
    {
        echo json_encode($this->Stock_counting_modal->load_subsubcat());
    }

    function delete_all_detail(){
        echo json_encode($this->Stock_counting_modal->delete_all_detail());
    }


    function print_stock_counting_filter()
    {
        $stockCountingAutoID = $this->input->post('printID');
        $subcategoryID = $this->input->post('subcategoryID');
        $subsubcategoryID = $this->input->post('subsubcategoryID');
        $data['extra'] = $this->Stock_counting_modal->fetch_template_stock_counting_print($stockCountingAutoID,$subcategoryID,$subsubcategoryID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_counting_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }
    function delete_stock_counting_up_items(){
        echo json_encode($this->Stock_counting_modal->delete_stock_counting_up_items());
    }
    function chk_delete_stock_counting_up_items(){
        echo json_encode($this->Stock_counting_modal->chk_delete_stock_counting_up_items());
    }

    function updateCountingWacSingle(){
        echo json_encode($this->Stock_counting_modal->updateCountingWacSingle());
    }

    function fetch_stock_counting_table_suom()
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
            $date .= " AND ( stockCountingDate >= '" . $datefromconvert . " 00:00:00' AND stockCountingDate <= '" . $datetoconvert . " 23:59:00')";
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
        $where = "companyID = " . $companyid . $location_filter . $date . $status_filter . "";
        $this->datatables->select("stockCountingAutoID,confirmedYN,approvedYN,createdUserID,stockCountingCode,comment,DATE_FORMAT(stockCountingDate,'$convertFormat') AS stockCountingDate ,wareHouseCode,wareHouseLocation,wareHouseDescription,isDeleted,confirmedByEmpID");
        $this->datatables->where($where);
        $this->datatables->from('srp_erp_stockcountingmaster');
        $this->datatables->add_column('scnt_detail', '$1 - $2 - $3 ', 'wareHouseCode, wareHouseLocation, wareHouseDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"SCNT",stockCountingAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"SCNT",stockCountingAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_stock_counting_action_suom(stockCountingAutoID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmpID)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }


    function updateCountingStockUomSingle(){
        echo json_encode($this->Stock_counting_modal->updateCountingStockUomSingle());
    }


    function save_stock_counting_detail_multiple_suom()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("currentWareHouseStock[{$key}]", 'Current Stock', 'trim|required');
            $this->form_validation->set_rules("currentWac[{$key}]", 'Current Wac', 'trim|required');
            $this->form_validation->set_rules("adjustment_Stock[{$key}]", 'Adjustment Stock', 'trim|required');
            $this->form_validation->set_rules("adjustment_wac[{$key}]", 'Adjustment Wac', 'trim|required');
            $this->form_validation->set_rules("a_segment[{$key}]", 'Segment', 'trim|required');
            //$this->form_validation->set_rules("SUOMIDhn[{$key}]", 'Secondary UOM', 'trim|required');
            if(!empty($this->input->post("SUOMIDhn[$key]"))){
                $this->form_validation->set_rules("SUOMQty[{$key}]", 'Secondary QTY', 'trim|required|greater_than[0]');
            }
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
            echo json_encode($this->Stock_counting_modal->save_stock_counting_detail_multiple());
        }
    }


    function save_stock_counting_detail_suom()
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

        $this->form_validation->set_rules('SUOMIDhn', 'Secondary UOM ', 'trim|required');
        $this->form_validation->set_rules('SUOMQty', 'Secondary QTY', 'trim|required');
        if ($projectExist == 1) {
            $this->form_validation->set_rules("projectID", 'Project', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Stock_counting_modal->save_stock_counting_detail());
        }
    }


    function load_stock_counting_conformation_suom()
    {
        $stockCountingAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockCountingAutoID') ?? '');
        $data['extra'] = $this->Stock_counting_modal->fetch_template_stock_counting($stockCountingAutoID);
        $data['approval'] = $this->input->post('approval');
        if (!$this->input->post('html')) {
            $data['signature']=$this->Stock_counting_modal->fetch_signaturelevel();
        } else {
            $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        if($data['extra']['master']['approvedYN']==1)
        {
            $html = $this->load->view('system/inventory/erp_stock_counting_print_approval_suom', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }
        else
        {
            $data['logo']=mPDFImage;
            if($this->input->post('html')){
                $data['logo']=htmlImage;
            }
            $html = $this->load->view('system/inventory/erp_stock_counting_print_suom', $data, true);
            if ($this->input->post('html')) {
                echo $html;
            } else {
                $this->load->library('pdf');
                $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
            }
        }
    }

    function load_stock_counting_approval_conformation_suom()
    {
        $stockCountingAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('stockCountingAutoID') ?? '');
        $data['extra'] = $this->Stock_counting_modal->fetch_template_stock_counting($stockCountingAutoID);
        $data['approval'] = $this->input->post('approval');
        $html = $this->load->view('system/inventory/erp_stock_counting_print_approval_suom', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function update_stock_minus_qty()
    {
        $stockCountingDetailsAutoID = $this->input->post('stockCountingDetailsAutoID');
        foreach ($stockCountingDetailsAutoID as $key => $search) {
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
            echo json_encode($this->Stock_counting_modal->update_stock_minus_qty());
        }
    }
    

}