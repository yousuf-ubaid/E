<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Receivable extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Receivable_modal');
        $this->load->helpers('receivable');
    }

    function fetch_credit_note()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( creditNoteDate >= '" . $datefromconvert . " 00:00:00' AND creditNoteDate <= '" . $datetoconvert . " 23:59:00')";
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
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( creditNoteCode Like '%$search%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (customerName Like '%$sSearch%') OR (creditNoteDate Like '%$sSearch%')) OR (docRefNo Like '%$sSearch%')";
        }

        $where = "srp_erp_creditnotemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        $this->datatables->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as creditNoteMasterAutoID,creditNoteCode,DATE_FORMAT(creditNoteDate,\'' . $convertFormat . '\') AS creditNoteDate,comments,customerName as customermastername,confirmedYN,approvedYN,srp_erp_creditnotemaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_creditnotemaster.confirmedByEmpID as confirmedByEmp, srp_erp_creditnotemaster.docRefNo AS docRefNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det','(det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID)','left');
        $this->datatables->where($where);
        // $this->datatables->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID');
        $this->datatables->from('srp_erp_creditnotemaster');
        $this->datatables->add_column('dn_detail', '<b>Customer Name : </b> $2 <br> <b>Date : </b> $3 <br> <b>Comments : </b> $1 <br> <b> Ref No : </b>$4', 'comments,customermastername,creditNoteDate,docRefNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CN",creditNoteMasterAutoID)');
        $this->datatables->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CN",creditNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_credit_note_action(creditNoteMasterAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function load_cn_conformation()
    {
        $companyID = current_companyID();
        $creditNoteMasterAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $data['extra'] = $this->Receivable_modal->fetch_credit_note_template_data($creditNoteMasterAutoID);
        $data['approval'] = $this->input->post('approval');
        $data['isGroupByTax'] =  existTaxPolicyDocumentWise('srp_erp_creditnotemaster',trim($creditNoteMasterAutoID),'CN', 'creditNoteMasterAutoID');

        $VatTax = $this->db->query("SELECT
                                            COUNT(taxCategory) as taxcat
                                        FROM
                                            `srp_erp_taxledger`
                                        LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
                                        WHERE
                                            documentID = 'CN'
                                            ANd srp_erp_taxledger.companyID = {$companyID}
                                            AND documentMasterAutoID = {$creditNoteMasterAutoID}")->row('taxcat');
        $data['is_tax_cn'] = 0;
        if($data['isGroupByTax']==1 && $VatTax > 0 ){
            $data['is_tax_cn'] = 1;
        }

        if (!$this->input->post('html')) {
           $data['signature']=$this->Receivable_modal->fetch_signaturelevel();
        } else {
            $data['signature']='';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }
        $html = $this->load->view('system/accounts_receivable/erp_credit_note_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $printSize = $this->uri->segment(4);
            $printSizeText='A4';
            if($printSize == 0 && ($printSize!='')){
               $printSizeText='A5-L';
            }
            $printlink = print_template_pdf('CN','system/accounts_receivable/erp_credit_note_print');
            $html = $this->load->view($printlink, $data, true);
            $printFooter = 1;
            if($printlink == "system/accounts_receivable/erp_credit_note_print_buyback") {
                $printFooter = 0;
            }
             $pdf = $this->pdf->printed($html, $printSizeText, $data['extra']['master']['approvedYN'], $printFooter);
        }
    }

    function fetch_credit_note_approval()
    {
        /*
         * rejected = 1
         * not rejected = 0
         * */
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentuserid = current_userID();
        $convertFormat = convert_date_format_sql();
        if($approvedYN == 0)
        {
            $this->datatables->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as creditNoteMasterAutoID,creditNoteCode,comments,customerID,customerCode,srp_erp_customermaster.customerName as customerName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(creditNoteDate,\'' . $convertFormat . '\') AS creditNoteDate,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount, srp_erp_creditnotemaster.docRefNo AS docRefNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det','(det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID)','left');
            $this->datatables->from('srp_erp_creditnotemaster');
            $this->datatables->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID','left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_creditnotemaster.creditNoteMasterAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_creditnotemaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_creditnotemaster.currentLevelNo');
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_creditnotemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CN');
            $this->datatables->where('srp_erp_approvalusers.documentID', 'CN');
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->add_column('details', '$1 <br><b>Ref No : </b> $2 ', 'comments,docRefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('creditNoteCode', '$1', 'approval_change_modal(creditNoteCode,creditNoteMasterAutoID,documentApprovedID,approvalLevelID,approvedYN,CN,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CN", creditNoteMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'cn_action_approval(creditNoteMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as creditNoteMasterAutoID,creditNoteCode,comments,customerID,customerCode,srp_erp_customermaster.customerName as customerName,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(creditNoteDate,\'' . $convertFormat . '\') AS creditNoteDate,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_creditnotemaster.docRefNo AS docRefNo');
            $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det','(det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID)','left');
            $this->datatables->from('srp_erp_creditnotemaster');
            $this->datatables->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID','left');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_creditnotemaster.creditNoteMasterAutoID');
            $this->datatables->where('srp_erp_creditnotemaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.documentID', 'CN');
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_creditnotemaster.creditNoteMasterAutoID');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('details', '$1 <br><b>Ref No : </b> $2 ', 'comments,docRefNo');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
            $this->datatables->add_column('creditNoteCode', '$1', 'approval_change_modal(creditNoteCode,creditNoteMasterAutoID,documentApprovedID,approvalLevelID,approvedYN,CN,0)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "CN", creditNoteMasterAutoID)');
            $this->datatables->add_column('edit', '$1', 'cn_action_approval(creditNoteMasterAutoID,approvalLevelID,approvedYN,documentApprovedID,0)');
            echo $this->datatables->generate();
        }

    }

    function save_creditnote_header()
    {
        $date_format_policy = date_format_policy();
        $cnDt = $this->input->post('cnDate');
        $cnDate = input_format_date($cnDt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $this->form_validation->set_rules('customer', 'customer', 'trim|required');
        $this->form_validation->set_rules('customer_currencyID', 'customer Currency', 'trim|required');
        //$this->form_validation->set_rules('exchangerate', 'Exchange Rate', 'trim|required');
        $this->form_validation->set_rules('cnDate', 'Date', 'trim|required|validate_date');
        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial period', 'trim|required');
        }
        /*$this->form_validation->set_rules('referenceno', 'Reference No', 'trim|required');*/
        //$this->form_validation->set_rules('comments', 'Comments', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            if($financeyearperiodYN==1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($cnDate >= $financePeriod['dateFrom'] && $cnDate <= $financePeriod['dateTo']) {
                    echo json_encode($this->Receivable_modal->save_creditnote_header());
                } else {
                    $this->session->set_flashdata('e', 'Credit Note Date not between Financial period !');
                    echo json_encode(FALSE);
                }
            }else{
                echo json_encode($this->Receivable_modal->save_creditnote_header());
            }

        }
    }

    function load_credit_note_header()
    {
        echo json_encode($this->Receivable_modal->load_credit_note_header());
    }

    function save_cn_approval()
    {
        $system_code    = trim($this->input->post('creditNoteMasterAutoID') ?? '');
        $level_id       = trim($this->input->post('Level') ?? '');
        $status         = trim($this->input->post('status') ?? '');

        if($status==1){
            $approvedYN=checkApproved($system_code,'CN',$level_id);
            if($approvedYN){
                $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                echo json_encode(FALSE);
            }else{
                $this->db->select('creditNoteMasterAutoID');
                $this->db->where('creditNoteMasterAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_creditnotemaster');
                $po_approved = $this->db->get()->row_array();
                if(!empty($po_approved)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                    echo json_encode(FALSE);
                }else{
                    if($this->input->post('status') ==2){
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Receivable_modal->save_cn_approval());
                    }
                }
            }
        }else if($status==2){
            $this->db->select('creditNoteMasterAutoID');
            $this->db->where('creditNoteMasterAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_creditnotemaster');
            $po_approved = $this->db->get()->row_array();
            if(!empty($po_approved)){
                $this->session->set_flashdata($msgtype = 'w', 'Document already rejected');
                echo json_encode(FALSE);
            }else{
                $rejectYN=checkApproved($system_code,'CN',$level_id);
                if(!empty($rejectYN)){
                    $this->session->set_flashdata($msgtype = 'w', 'Document already approved');
                    echo json_encode(FALSE);
                }else{
                    if($this->input->post('status') ==2){
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Receivable_modal->save_cn_approval());
                    }
                }
            }
        }
    }

    function fetch_cn_detail_table()
    {
        echo json_encode($this->Receivable_modal->fetch_cn_detail_table());
    }

    function save_cn_detail()
    {
        $this->form_validation->set_rules('gl_code', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('segment_gl', 'Segment', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Receivable_modal->save_cn_detail());
        }
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('InvoiceAutoID', 'InvoiceAutoID', 'trim|required');
        $this->form_validation->set_rules('tax_amount', 'Tax Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Receivable_modal->save_inv_tax_detail());
        }
    }

    function cn_confirmation()
    {
        echo json_encode($this->Receivable_modal->cn_confirmation());
    }

    function delete_cn_detail()
    {
        echo json_encode($this->Receivable_modal->delete_cn_detail());
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Receivable_modal->delete_tax_detail());
    }

    function fetch_custemer_data_invoice()
    {

        $data = $this->Receivable_modal->fetch_custemer_data_invoice();
        $per_page = 500;
        $data_pagination = $this->input->post('pageID');
        $page = (!empty($data_pagination)) ? (($data_pagination - 1) * $per_page) : 0;
        $data["per_page"] = $per_page;
        $data['PageStartNumber'] = ($page + 1);
        $data['view'] = $this->load->view('system/accounts_receivable/erp_credit_note_detail', $data, true);
        echo json_encode($data);
    }

    function save_credit_base_items()
    {
        //$projectExist = project_is_exist();
        $this->form_validation->set_rules('gl_code[]', 'GL Code', 'trim|required');
        $this->form_validation->set_rules('segment[]', 'Segment', 'trim|required');
        $this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        $this->form_validation->set_rules('invoiceAutoID[]', 'InvoiceAutoID', 'trim|required');
        /* if($projectExist == 1){
            $this->form_validation->set_rules("project[]", 'Project', 'trim|required');
        } */
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Receivable_modal->save_credit_base_items());
        }
    }

    function referback_credit_note()
    {
        $creditNoteMasterAutoID = $this->input->post('creditNoteMasterAutoID');

        $this->db->select('approvedYN,creditNoteCode');
        $this->db->where('creditNoteMasterAutoID', trim($creditNoteMasterAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_creditnotemaster');
        $approved_inventory_credit_note = $this->db->get()->row_array();
        if (!empty($approved_inventory_credit_note)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_inventory_credit_note['creditNoteCode']));
        }else
        {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($creditNoteMasterAutoID, 'CN');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function delete_creditNote_attachement()
    {
        echo json_encode($this->Receivable_modal->delete_creditNote_attachement());
    }

    function delete_creditNote_master()
    {
        echo json_encode($this->Receivable_modal->delete_creditNote_master());
    }

    function re_open_credit_note()
    {
        echo json_encode($this->Receivable_modal->re_open_credit_note());
    }

    function save_crditNote_detail_GLCode_multiple(){
        //$projectExist = project_is_exist();
        $gl_codes = $this->input->post('gl_code_array');
        $segment_gls = $this->input->post('segment_gl');
        $descriptions = $this->input->post('description');

        foreach ($gl_codes as $key => $gl_code) {
            $this->form_validation->set_rules("gl_code_array[{$key}]", 'GL Code', 'required|trim');
            $this->form_validation->set_rules("segment_gl[{$key}]", 'Segment', 'required|trim');
            $this->form_validation->set_rules("amount[{$key}]", 'Amount', 'trim|required');
            $this->form_validation->set_rules("description[{$key}]", 'Description', 'trim|required');
            /* if ($projectExist == 1) {
                $this->form_validation->set_rules("projectID[{$key}]", 'Project', 'trim|required');
            } */
        }

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Receivable_modal->save_crditNote_detail_GLCode_multiple());
        }
    }
    function fetch_customer_Dropdown_all()
    {
        $data_arr = array();
        $customerid = $this->input->post('customer');
        $creditNoteMasterAutoID = $this->input->post('DocID');
        $Documentid = $this->input->post('Documentid');
        $customeridcurrentdoc = all_customer_drop_isactive_inactive($creditNoteMasterAutoID,$Documentid);

        if($customerid)
        {
            $customer = $customerid;
        }else
        {
            $customer = '';
        }

        $companyID = $this->common_data['company_data']['company_id'];
        $customerqry = "SELECT customerAutoID,customerName,customerSystemCode,customerCountry FROM srp_erp_customermaster WHERE companyID = {$companyID} AND isActive = 1 AND deletedYN = 0";
        $customermMaster = $this->db->query($customerqry)->result_array();
        $data_arr = array('' => 'Select Customer');
        if (!empty($customermMaster)) {
            foreach ($customermMaster as $row) {
                $data_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }
        if ($creditNoteMasterAutoID != ' ' && !empty($customeridcurrentdoc)) {
            if ($customeridcurrentdoc['isActive'] == 0) {
                $data_arr[trim($customeridcurrentdoc['customerAutoID'] ?? '')] = (trim($customeridcurrentdoc['customerSystemCode'] ?? '') ? trim($customeridcurrentdoc['customerSystemCode'] ?? '') . ' | ' : '') . trim($customeridcurrentdoc['customerName'] ?? '') . (trim($customeridcurrentdoc['customerCountry'] ?? '') ? ' | ' . trim($customeridcurrentdoc['customerCountry'] ?? '') : '');
            }
        }
        echo form_dropdown('customer', $data_arr, $customer, 'class="form-control select2" id="customer" onchange="Load_customer_currency(this.value)"');
    }

    function Load_invoice_overdue_report()
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
            $this->form_validation->set_rules('customerAutoID[]', 'Customer', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['currency'] = $this->input->post('currency');
                $data['details'] = $this->Receivable_modal->fetch_details_invoice_overdue_report();
                $data['type'] = "html";

                echo $this->load->view('system/accounts_receivable/report/load_invoice_overdue_report', $data, true);
            }
        }
    }

    function Load_invoice_overdue_report_pdf()
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
            $this->form_validation->set_rules('customerAutoID[]', 'Customer', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['type'] = "pdf";
                $data['currency'] = $this->input->post('currency');
                $data['details'] = $this->Receivable_modal->fetch_details_invoice_overdue_report();

                $html = $this->load->view('system/accounts_receivable/report/load_invoice_overdue_report', $data, true);
                $this->load->library('pdf');
                $this->pdf->printed($html, 'A4', 1);
            }
        }
    }

    function Load_invoice_overdue_drilldown_report()
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
            $this->form_validation->set_rules('customerAutoID[]', 'Customer', 'required');
            $this->form_validation->set_rules('currency', 'Currency', 'required');
            if ($this->form_validation->run() == FALSE) {
                echo ' <div class="alert alert-warning" role="alert">' . validation_errors() . '</div>';
            } else {
                $data['currency'] = $this->input->post('currency');
                $data['details'] = $this->Receivable_modal->fetch_details_invoice_overdue_drilldown_report();
                $data['type'] = "html";

                echo $this->load->view('system/accounts_receivable/report/load_invoice_overdue_drilldown_report', $data, true);
            }
        }
    }

    function fetch_credit_note_buyback()
    {
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( creditNoteDate >= '" . $datefromconvert . " 00:00:00' AND creditNoteDate <= '" . $datetoconvert . " 23:59:00')";
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
        $sSearch=$this->input->post('sSearch');
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( creditNoteCode Like '%$search%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (comments Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (creditNoteDate Like '%$sSearch%')) OR (docRefNo Like '%$sSearch%')";
        }

        $where = "srp_erp_creditnotemaster.companyID = " . $companyid . $customer_filter . $date . $status_filter . $searches."";
        $this->datatables->select('srp_erp_creditnotemaster.creditNoteMasterAutoID as creditNoteMasterAutoID,documentID,creditNoteCode,DATE_FORMAT(creditNoteDate,\'' . $convertFormat . '\') AS creditNoteDate,comments,srp_erp_customermaster.customerName as customermastername,confirmedYN,approvedYN,srp_erp_creditnotemaster.createdUserID as createdUser,transactionCurrency,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value,det.transactionAmount as detTransactionAmount,isDeleted,srp_erp_creditnotemaster.confirmedByEmpID as confirmedByEmp, srp_erp_creditnotemaster.docRefNo AS docRefNo');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,creditNoteMasterAutoID FROM srp_erp_creditnotedetail GROUP BY creditNoteMasterAutoID) det','(det.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID)','left');
        $this->datatables->where($where);
        $this->datatables->join('srp_erp_customermaster','srp_erp_customermaster.customerAutoID = srp_erp_creditnotemaster.customerID');
        $this->datatables->from('srp_erp_creditnotemaster');
        $this->datatables->add_column('dn_detail', '<b>Customer Name : </b> $2 <br> <b>Date : </b> $3 <br> <b>Comments : </b> $1 <br> <b> Ref No : </b>$4', 'comments,customermastername,creditNoteDate,docRefNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"CN",creditNoteMasterAutoID)');
        $this->datatables->add_column('approve', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"CN",creditNoteMasterAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_credit_note_action_buyback(creditNoteMasterAutoID,confirmedYN,approvedYN,createdUser,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    
    function load_line_tax_amount()
    {
        echo json_encode($this->Receivable_modal->load_line_tax_amount());
    }
}