<?php

class Quotation_contract extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Quotation_contract_model');
        $this->load->helper('quotation_contract');
        $this->load->helpers('string_helper');
        $this->load->library('s3');
    }

    function fetch_Quotation_contract()
    {
        $sSearch=$this->input->post('sSearch');
        $convertFormat = convert_date_format_sql();

        $date_format_policy = date_format_policy();
        $datefrom = $this->input->post('datefrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $dateto = $this->input->post('dateto');
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $contractView = $this->input->post('contractView');
        $isAdvance = $this->input->post('isAdvance');

        $page_type = 0;
        if($isAdvance){
            $page_type=1;
        }

        $companyid = $this->common_data['company_data']['company_id'];
        $customer = $this->input->post('customerCode');
        //$datefrom = $this->input->post('datefrom');
        //$dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $contractType = $this->input->post('contractType');
        $customer_filter = '';

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }

        $contractType_filter = '';
        if (!empty($contractType)) {
            $contractType = explode(',', $this->input->post('contractType'));
            $whereIN = "( '" . join("' , '", $contractType) . "' )";
            $contractType_filter = " AND contractType IN " . $whereIN;
        }

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 23:59:00')";
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
                $status_filter = " AND (approvedYN = 5)";
            }else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }

        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( contractCode Like '%$search%' ESCAPE '!') OR ( contractType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (contractNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (contractDate Like '%$sSearch%') OR (contractExpDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }

        $where = "srp_erp_contractmaster.companyID = " . $companyid . $customer_filter . $date . $contractType_filter . $status_filter . $searches."";
        $this->datatables->select('srp_erp_contractmaster.referenceNo as referenceNo,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.isBackToBack as isBackToBack,srp_erp_contractmaster.confirmedByEmpID as confirmedByEmp,contractCode,contractNarration,srp_erp_customermaster.customerName as customerMasterName,documentID, closedYN, transactionCurrencyDecimalPlaces ,transactionCurrency,confirmedYN,approvedYN, contractType, srp_erp_contractmaster.createdUserID as createdUser,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate ,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate,det.transactionAmount as total_value,ROUND(det.transactionAmount,2) as detTransactionAmount,srp_erp_contractmaster.isDeleted as isDeleted,srp_erp_contractmaster.isSystemGenerated as isSystemGenerated,srp_erp_contractmaster.documentStatus as documentStatus');
        $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->where($where);
        $this->datatables->where('srp_erp_contractmaster.isAdvance', $page_type);
        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->add_column('detail', '<b>Customer Name : </b> $2 <br> <b> Type  : </b> $5<br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Document Exp Date : </b> $4 <br> <b>Comments : </b> $1 <br> <b>Reference No : </b> $6  ', 'contractNarration,customerMasterName,contractDate,contractExpDate,contractType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,documentID,contractAutoID,"",documentStatus)');

        if($contractView == 'advance'){
            $this->datatables->add_column('edit', '$1', 'load_contract_action(contractAutoID,confirmedYN,approvedYN,createdUser,documentID,confirmedYN,isDeleted,confirmedByEmp,isSystemGenerated, closedYN, 1)');
        }else{
            $this->datatables->add_column('edit', '$1', 'load_contract_action(contractAutoID,confirmedYN,approvedYN,createdUser,documentID,confirmedYN,isDeleted,confirmedByEmp,isSystemGenerated, closedYN,0,isBackToBack)');
        }
        
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');

        echo $this->datatables->generate();
    }


    function save_quotation_contract_header()
    {
        $this->form_validation->set_rules('contractType', 'Contract Type', 'trim|required');
        $this->form_validation->set_rules('contractDate', 'Contract Date', 'trim|required');
        $this->form_validation->set_rules('contractExpDate', 'Contract Exp Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        $financearray = explode("|", $this->input->post('financeyear_period') ?? '');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_quotation_contract_header());
        }
    }

    function save_quotation_contract_header_job()
    {   
        $amendmentID = $this->input->post('amendmentID');

        if($amendmentID){
            $this->form_validation->set_rules('referenceNo', 'C', 'trim|required');
        }else{
            $this->form_validation->set_rules('contractType', 'Contract Type', 'trim|required');
            $this->form_validation->set_rules('contractDate', 'Contract Date', 'trim|required');
            $this->form_validation->set_rules('contractExpDate', 'Contract Exp Date', 'trim|required');
            $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
            $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
            $financearray = explode("|", $this->input->post('financeyear_period'));
        }

        $AdvanceCostCapture = getPolicyValues('ACC','All');

        if($AdvanceCostCapture==1){
            $this->form_validation->set_rules('activityCode', 'Activity Code', 'trim|required');
        }
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {

            if($amendmentID){
                echo json_encode($this->Quotation_contract_model->save_quotation_contract_header_job_amendment()); 
            }else{
                echo json_encode($this->Quotation_contract_model->save_quotation_contract_header_job());
            }
           
        }
    }

    function fetch_quotation_contract_approval()
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
            $this->datatables->select('contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_customermaster.customerName as customerName,det.transactionAmount as detTransactionAmount,srp_erp_contractmaster.referenceNo as referenceNo');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount,0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
            $this->datatables->from('srp_erp_contractmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_contractmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_contractmaster.currentLevelNo');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where_in('srp_erp_approvalusers.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
            $this->datatables->add_column('edit', '$1', 'con_action_approval(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');

            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_customermaster.customerName as customerName,det.transactionAmount as detTransactionAmount,srp_erp_contractmaster.referenceNo as referenceNo');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount,0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
            $this->datatables->from('srp_erp_contractmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
            $this->datatables->add_column('edit', '$1', 'con_action_approval(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');

            echo $this->datatables->generate();
        }

    }

    function load_contract_conformation()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf($documentid['documentID'],'system/quotation_contract/erp_contract_print');

            $this->load->view($printlink, $data);
        }
    }

    function load_cost_sheet()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['header'] = $this->Quotation_contract_model->fetch_contract_template_header($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        $data['charge_data'] = $this->Quotation_contract_model->get_extra_charges_records($contractAutoID);
        $data['master'] = get_contractmaster_details($contractAutoID);

        $data['total_data'] = $this->Quotation_contract_model->get_extra_charges_records($contractAutoID,2);
        $data['contractAutoID'] = $contractAutoID;

        $data['compayLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['compayLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];

        $data['salesPriceTotal'] = $this->db->select('SUM(salesPriceTotal + discountAmount) as total')->where('contractAutoID',$contractAutoID)->from('srp_erp_contractdetails')->get()->row_array();
        $data['salesPriceTotalAED'] = currency_conversionID($data['compayLocalCurrencyID'], $data['header']['transactionCurrencyID'],$data['salesPriceTotal']['total']);

       

        $this->db->select('*');
        $this->db->where('master.contractAutoID', trim($contractAutoID));
        $this->db->where('master.companyID', current_companyID());
        $this->db->join('srp_erp_srm_customerordermaster as order','master.customerOrderID = order.customerOrderID','left') ;
        $this->db->join('srp_erp_suppliermaster as sup_master','order.supplierID = sup_master.supplierAutoID','left');
        $this->db->from('srp_erp_contractmaster as master');
        $data['supplier_details'] = $this->db->get()->row_array();
        // $data['customerOrder']

         // echo '<pre>';
         $po_material_cost = $this->db->query("
            SELECT SUM(poUnitPrice) as poPrice,SUM(commissionValue) as commission
            FROM srp_erp_contractdetails
            WHERE contractAutoID = '{$contractAutoID}'
            GROUP BY contractAutoID
        ")->row_array();

        $data['po_material_cost'] = $po_material_cost['poPrice'];
        $data['po_material_commission'] = $po_material_cost['commission'];
        $data['po_material_cost_aed'] =  currency_conversionID($data['compayLocalCurrencyID'], $data['header']['transactionCurrencyID'],$po_material_cost['poPrice']);

            // echo '<pre>';
        $po_material_commision_p = $this->db->query("
            SELECT commissionPercentage
            FROM srp_erp_contractdetails
            WHERE contractAutoID = '{$contractAutoID}' AND commissionPercentage > 0  limit 1
        ")->row_array();

        $data['commissionPercentage'] = $po_material_commision_p['commissionPercentage'];


        $po_material_markup = $this->db->query("
            SELECT SUM(charge.markup_value) as markup,master.transactionCurrency,master.transactionCurrencyID
            FROM srp_erp_contractextracharges as charge
            LEFT JOIN srp_erp_contractmaster as master ON charge.contractAutoID = master.contractAutoID
            WHERE charge.contractAutoID = '{$contractAutoID}'
            GROUP BY charge.contractAutoID
        ")->row_array();

        $data['po_material_markup'] = $po_material_markup['markup'];
        $data['po_material_markup_aed'] = currency_conversionID($data['compayLocalCurrencyID'],$data['header']['transactionCurrencyID'],$po_material_markup['markup']);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf('CST','system/quotation_contract/erp_cost_sheet_print');

            $this->load->view($printlink, $data);
        }
    }

    function load_cost_distribution()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['header'] = $this->Quotation_contract_model->fetch_contract_template_header($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $data['itemDetails'] = $this->Quotation_contract_model->get_contract_details($contractAutoID);
        $data['discountExt'] = 0;

        foreach($data['itemDetails'] as $item){
            if($item['discountAmount'] > 0){
                $data['discountExt'] = 1;
            }
        }

        $po_material_commision_p = $this->db->query("
        SELECT commissionPercentage
        FROM srp_erp_contractdetails
        WHERE contractAutoID = '{$contractAutoID}' AND commissionPercentage > 0  limit 1
        ")->row_array();


        $data['markupPercentage'] = $data['header']['marginPercentage'];
        $data['commissionPercentage'] = $po_material_commision_p['commissionPercentage'];
       
        $data['master'] = get_contractmaster_details($contractAutoID);

        $data['compayLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['compayLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        
        $data['conversion']  = currency_conversionID($data['compayLocalCurrencyID'], $data['header']['transactionCurrencyID']);

        // echo '<pre>';
        $this->db->select('*');
        $this->db->where('master.contractAutoID', trim($contractAutoID));
        $this->db->where('master.companyID', current_companyID());
        $this->db->join('srp_erp_srm_customerordermaster as order','master.customerOrderID = order.customerOrderID','left') ;
        $this->db->join('srp_erp_suppliermaster as sup_master','order.supplierID = sup_master.supplierAutoID','left');
        $this->db->from('srp_erp_contractmaster as master');
        $data['supplier_details'] = $this->db->get()->row_array();
        // $data['customerOrder']

        $po_material_cost = $this->db->query("
            SELECT SUM(poUnitPrice) as poPrice,SUM(commissionValue) as commission
            FROM srp_erp_contractdetails
            WHERE contractAutoID = '{$contractAutoID}'
            GROUP BY contractAutoID
        ")->row_array();

        $data['po_material_cost'] = $po_material_cost['poPrice'];
        $data['po_material_commission'] = $po_material_cost['commission'];
        $data['po_material_cost_aed'] =  currency_conversionID($data['compayLocalCurrencyID'], $data['header']['transactionCurrencyID'],$po_material_cost['poPrice']);

        $po_material_markup = $this->db->query("
            SELECT SUM(charge.markup_value) as markup,master.transactionCurrency,master.transactionCurrencyID
            FROM srp_erp_contractextracharges as charge
            LEFT JOIN srp_erp_contractmaster as master ON charge.contractAutoID = master.contractAutoID
            WHERE charge.contractAutoID = '{$contractAutoID}'
            GROUP BY charge.contractAutoID
        ")->row_array();

        $data['po_material_markup'] = $po_material_markup['markup'];
        $data['po_material_markup_aed'] = currency_conversionID($data['compayLocalCurrencyID'],$data['header']['transactionCurrencyID'],$po_material_markup['markup']);
       
        $this->db->select('SUM(extraCostValue) as costValue');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('type',1);
        $extra = $this->db->from('srp_erp_contractextracharges')->get()->row_array();

        $this->db->select('SUM(markup_value) as markup');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $extraMarkup = $this->db->from('srp_erp_contractextracharges')->get()->row_array();

        $data['extra_markup'] =  $extra['costValue'] + $extraMarkup['markup'];

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        // echo '<pre>';
        // print_r($data['itemDetails']); exit;

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf('CST','system/quotation_contract/erp_cost_distribution_print');

            $this->load->view($printlink, $data);
        }
    }

    function load_contract_conformation_job()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view_job', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf($documentid['documentID'],'system/quotation_contract/erp_contract_print_job');

            $this->load->view($printlink, $data);
        }
    }

    function load_payment_advice()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf($documentid['documentID'],'system/quotation_contract/erp_contract_payment_advice_print');

            $this->load->view($printlink, $data);
        }
    }

    function load_payment_advice_new()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $PAAutoID = ($this->uri->segment(4)) ? $this->uri->segment(4) : trim($this->input->post('autoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data_new($contractAutoID,$PAAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {

            //$this->load->view('system/quotation_contract/erp_contract_print', $data);
            $printlink = print_template_pdf($documentid['documentID'],'system/quotation_contract/erp_contract_payment_advice_print');

            $this->load->view($printlink, $data);
        }
    }

    function save_quotation_contract_approval()
    {
        $system_code = trim($this->input->post('contractAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = trim($this->input->post('code') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('contractAutoID');
                $this->db->where('contractAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_contractmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            //$this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_contractmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval());
                    }
                }
            }
        }
    }

    function quotation_version()
    {
        echo json_encode($this->Quotation_contract_model->quotation_version());
    }

    function load_contract_header()
    {
        echo json_encode($this->Quotation_contract_model->load_contract_header());
    }

    function fetch_item_detail_table()
    {
        echo json_encode($this->Quotation_contract_model->fetch_item_detail_table());
    }

    function fetch_item_detail()
    {
        echo json_encode($this->Quotation_contract_model->fetch_item_detail());
    }

    function delete_item_detail()
    {
        echo json_encode($this->Quotation_contract_model->delete_item_detail());
    }

    function contract_confirmation()
    {
        echo json_encode($this->Quotation_contract_model->contract_confirmation());
    }

    function referback_Quotation_contract()
    {
        $contractAutoID = trim($this->input->post('contractAutoID') ?? '');
        $documentID = trim($this->input->post('code') ?? '');

        $this->db->select('approvedYN,contractCode');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_contractmaster');
        $approved = $this->db->get()->row_array();
        if (!empty($approved)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved['contractCode']));
        } else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($contractAutoID, $documentID);
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }
    }

    function delete_con_master()
    {
        echo json_encode($this->Quotation_contract_model->delete_con_master());
    }

    function document_drill_down_View_modal()
    {
        echo json_encode($this->Quotation_contract_model->document_drill_down_View_modal());
    }

    function delete_tax_detail()
    {
        echo json_encode($this->Quotation_contract_model->delete_tax_detail());
    }

    function save_inv_tax_detail()
    {
        $this->form_validation->set_rules('text_type', 'Tax Type', 'trim|required');
        // $this->form_validation->set_rules('percentage', 'Percentage', 'trim|required');
        $this->form_validation->set_rules('contractAutoID', 'Document ID', 'trim|required');
        //$this->form_validation->set_rules('amounts[]', 'Amounts', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'data' => validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->save_inv_tax_detail());
        }
    }
    
    function save_item_order_detail()
    {
        $searchs = $this->input->post('search');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searchs as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity Requested', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');

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
            echo json_encode($this->Quotation_contract_model->save_item_order_detail());
        }
    }

    function save_item_order_detail_job()
    {
        $searchs = $this->input->post('search');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();
        foreach ($searchs as $key => $search) {
            $this->form_validation->set_rules("search[{$key}]", 'Item Code', 'trim|required');
            $this->form_validation->set_rules("itemID[{$key}]", ' Item field ', 'trim|required|greater_than[0]');
            // $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity Requested', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("groupToCategory[{$key}]", 'Category Group', 'trim|required');
            
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
            echo json_encode($this->Quotation_contract_model->save_item_order_detail_job());
        }
    }


    function update_item_order_detail()
    {
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $estimatedAmount = trim($this->input->post('estimatedAmount') ?? '');
        $projectExist = project_is_exist();
        $projectID=$this->input->post('projectID');

        $cat_mandetory = Project_Subcategory_is_exist();

        $this->form_validation->set_rules("search", 'Item', 'trim|required');
        $this->form_validation->set_rules("UnitOfMeasureID", 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules("groupToCategory_edit", 'Category Group', 'trim|required');
        $this->form_validation->set_rules("quantityRequested", 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules("estimatedAmount", 'Sales Price', 'trim|required|greater_than[0]');
         $this->form_validation->set_rules("itemID", 'Item', 'trim|required|greater_than[0]');
        //$this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required|greater_than[0]');
        
        if ($projectExist == 1  && !empty($projectID)) {
            //$this->form_validation->set_rules("projectID", 'Project', 'trim|required');
            if($cat_mandetory == 1) {
                $this->form_validation->set_rules("project_categoryID", 'project Category', 'trim|required');
            }
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if (($quantityRequested == 0) || ($estimatedAmount == 0)) {
                echo json_encode(array('e', ' Qty, Sales Price cannot be 0.'));
            } else {
                echo json_encode($this->Quotation_contract_model->update_item_order_detail());
            }

        }
    }

    function update_item_order_detail_buyback()
    {
        $quantityRequested = trim($this->input->post('quantityRequested') ?? '');
        $estimatedAmount = trim($this->input->post('estimatedAmount') ?? '');

        $this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required');
        $this->form_validation->set_rules("UnitOfMeasureID", 'Unit Of Measure', 'trim|required');
        $this->form_validation->set_rules("quantityRequested", 'Quantity Requested', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules("estimatedAmount", 'Sales Price', 'trim|required|greater_than[0]');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if (($quantityRequested == 0) || ($estimatedAmount == 0)) {
                echo json_encode(array('e', ' Qty, Sales Price cannot be 0.'));
            } else {
                echo json_encode($this->Quotation_contract_model->update_item_order_detail_buyback());
            }

        }
    }

    function load_unitprice_exchangerate() //get localwac amount into exchange rate
    {
        echo json_encode($this->Quotation_contract_model->load_unitprice_exchangerate());
    }

    function delete_quotationContract_attachement()
    {
        echo json_encode($this->Quotation_contract_model->delete_quotationContract_attachement());
    }

    function re_open_contract()
    {
        echo json_encode($this->Quotation_contract_model->re_open_contract());
    }

    function loademail()
    {
        echo json_encode($this->Quotation_contract_model->loademail());
    }

    function send_quatation_email()
    {
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->send_quatation_email());
        }
    }

    function set_contract_extra_charge(){

        $this->form_validation->set_rules('extraChargeValue', 'Charge Value', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->set_contract_extra_charge());
        }
    }

    function fetch_documentID(){
        $documentSystemCode=$this->input->post('documentSystemCode');
        $this->db->select('documentID');
        $this->db->where('contractAutoID', trim($documentSystemCode));
        $this->db->from('srp_erp_contractmaster');
        $documentID = $this->db->get()->row_array();
        echo json_encode($documentID['documentID']);
    }
    function load_contract_conformation_buyback()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');
        $data['html'] = $this->input->post('html');

        $this->db->select('documentID');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_print_buyback', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $html = $this->load->view('system/quotation_contract/erp_contract_printView_buyback', $data, true);
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

    function fetch_Quotation_contract_buyback()
    {
        $sSearch=$this->input->post('sSearch');
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
        $contractType = $this->input->post('contractType');
        $category = $this->input->post('category');
        $customer_filter = '';
        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $contractType_filter = '';
        if (!empty($contractType)) {
            $contractType = explode(',', $this->input->post('contractType'));
            $whereIN = "( '" . join("' , '", $contractType) . "' )";
            $contractType_filter = " AND contractType IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 23:59:00')";
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
        $category_filter = '';
        if (!empty($category)) {
            $category = array($this->input->post('category'));
            $whereIN = "( " . join("' , '", $category) . " )";
            $category_filter = " AND partyCategoryID IN " . $whereIN;
        }
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( contractCode Like '%$search%' ESCAPE '!') OR ( contractType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (contractNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (contractDate Like '%$sSearch%') OR (contractExpDate Like '%$sSearch%')) ";
        }
        $where = "srp_erp_contractmaster.companyID = " . $companyid . $customer_filter . $date . $contractType_filter . $status_filter . $searches. $category_filter . "";
        $this->datatables->select('srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.confirmedByEmpID as confirmedByEmp,contractCode,contractNarration,srp_erp_customermaster.customerName as customerMasterName, closedYN,documentID, transactionCurrencyDecimalPlaces ,transactionCurrency,confirmedYN,approvedYN, contractType, srp_erp_contractmaster.createdUserID as createdUser,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate ,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate,det.transactionAmount as total_value,ROUND(det.transactionAmount,2) as detTransactionAmount,srp_erp_contractmaster.isDeleted as isDeleted, srp_erp_contractmaster.isSystemGenerated as isSystemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->where($where);

        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->add_column('detail', '<b>Customer Name : </b> $2 <br> <b> Type  : </b> $5<br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Document Exp Date : </b> $4 <br> <b>Comments : </b> $1  ', 'contractNarration,customerMasterName,contractDate,contractExpDate,contractType');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_contract_action_buyback(contractAutoID,confirmedYN,approvedYN,createdUser,documentID,confirmedYN,isDeleted,confirmedByEmp, isSystemGenerated, closedYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_quotation_contract_approval_buyback()
    {
        /*
        * rejected = 1
        * not rejected = 0
        * */
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $approvedYN = trim($this->input->post('approvedYN') ?? '');
        $this->datatables->select('contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_customermaster.customerName as customerName');
        $this->datatables->join('(SELECT SUM(transactionAmount) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_contractmaster.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_contractmaster.currentLevelNo');
        $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
        $this->datatables->where_in('srp_erp_approvalusers.documentID', array('QUT', 'CNT', 'SO'));
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
        $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
        $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
        $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
        $this->datatables->add_column('edit', '$1', 'con_action_approval_buyback(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
        $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        echo $this->datatables->generate();
    }

    function load_mail_history(){
        $this->datatables->select('autoID,srp_erp_documentemailhistory.documentID,documentAutoID,sentByEmpID,toEmailAddress,sentDateTime,srp_employeesdetails.Ename2 as ename,srp_erp_contractmaster.contractCode')
            ->where('srp_erp_documentemailhistory.companyID', $this->common_data['company_data']['company_id'])
            ->where_in('srp_erp_documentemailhistory.documentID', array('QUT', 'CNT', 'SO'))
            ->where('srp_erp_documentemailhistory.documentAutoID', $this->input->post('contractAutoID'))
            ->join('srp_employeesdetails','srp_erp_documentemailhistory.sentByEmpID = srp_employeesdetails.EIdNo','left')
            ->join('srp_erp_contractmaster','srp_erp_contractmaster.contractAutoID = srp_erp_documentemailhistory.documentAutoID','left')
            ->from('srp_erp_documentemailhistory');
        echo $this->datatables->generate();
    }


    function save_item_order_detail_buyback()
    {
        $searchs = $this->input->post('search');
        $quantityRequested = $this->input->post('quantityRequested');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $projectExist = project_is_exist();
        foreach ($searchs as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity Requested', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');

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
            echo json_encode($this->Quotation_contract_model->save_item_order_detail_buyback());
        }
    }

    function save_deliveryorder_from_quotation_contract_header()
    {
        $date_format_policy = date_format_policy();
        $DOdt = $this->input->post('DOdate');
        $DOdate = input_format_date($DOdt, $date_format_policy);
        $financeyearperiodYN = getPolicyValues('FPC', 'All');
        $type = $this->input->post('type');

        $this->form_validation->set_rules('type', 'Type', 'trim|required');
        $this->form_validation->set_rules('DOdate', 'Delivery Order Date', 'trim|required');
        $this->form_validation->set_rules('warehouseAutoIDtemp', 'WareHouse', 'trim|required');

        if($financeyearperiodYN==1) {
            $this->form_validation->set_rules('financeyear', 'Financial Year', 'trim|required');
            $this->form_validation->set_rules('financeyear_period', 'Financial Period', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e',validation_errors()));
        } else {
            if ($financeyearperiodYN == 1) {
                $financearray = $this->input->post('financeyear_period');
                $financePeriod = fetchFinancePeriod($financearray);
                if ($DOdate >= $financePeriod['dateFrom'] && $DOdate <= $financePeriod['dateTo']) {
                    if($type == 1){
                        echo json_encode($this->Quotation_contract_model->save_deliveryorder_from_quotation_contract_header());
                    }else if($type == 2){
                        echo json_encode($this->Quotation_contract_model->save_invoice_from_quotation_contract_header());
                    }else{
                        echo json_encode(array('e', 'Incorrect type !'));
                    }
                   // echo json_encode($this->Quotation_contract_model->save_deliveryorder_from_quotation_contract_header());
                } else {

                    if($type == 1){
                         echo json_encode(array('e', 'Delivery order date not between Financial period !'));
                    }else{
                         echo json_encode(array('e', 'Invoice date not between Financial period !'));
                    }
                    //echo json_encode(array('e', 'Delivery order/ Invoice date not between Financial period !'));
                }
            }else{
                if($type == 1){
                    echo json_encode($this->Quotation_contract_model->save_deliveryorder_from_quotation_contract_header());
                }else if($type == 2){
                    echo json_encode($this->Quotation_contract_model->save_invoice_from_quotation_contract_header());
                }else{
                    echo json_encode(array('e', 'Incorrect type !'));
                }
                //echo json_encode($this->Quotation_contract_model->save_deliveryorder_from_quotation_contract_header());
            }
        }
    }
    function open_delivery_order_modal(){
         echo json_encode($this->Quotation_contract_model->open_delivery_order_modal());
    }
    function check_item_balance_from_quotation_contract(){
        echo json_encode($this->Quotation_contract_model->check_item_balance_from_quotation_contract());
    }
    function save_quotation_contract_header_nh()
    {
        $this->form_validation->set_rules('contractType', 'Contract Type', 'trim|required');
        $this->form_validation->set_rules('contractDate', 'Contract Date', 'trim|required');
        $this->form_validation->set_rules('contractExpDate', 'Contract Exp Date', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Transaction Currency', 'trim|required');
        $this->form_validation->set_rules('customerID', 'Customer', 'trim|required');
        $this->form_validation->set_rules('location', 'Delivery Location', 'trim|required');
        $financearray = explode("|", $this->input->post('financeyear_period'));
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_quotation_contract_header_nh());
        }
    }
    function fetch_Quotation_contract_nh()
    {


        $sSearch=$this->input->post('sSearch');
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
        $contractType = $this->input->post('contractType');
        $customer_filter = '';

        if (!empty($customer)) {
            $customer = array($this->input->post('customerCode'));
            $whereIN = "( " . join("' , '", $customer) . " )";
            $customer_filter = " AND customerID IN " . $whereIN;
        }
        $contractType_filter = '';
        if (!empty($contractType)) {
            $contractType = explode(',', $this->input->post('contractType'));
            $whereIN = "( '" . join("' , '", $contractType) . "' )";
            $contractType_filter = " AND contractType IN " . $whereIN;
        }
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( contractDate >= '" . $datefromconvert . " 00:00:00' AND contractDate <= '" . $datetoconvert . " 23:59:00')";
        }
        $status_filter = "";
        if ($status != 'all') {
            if ($status == 1) {
                $status_filter = " AND ( confirmedYN = 0 AND approvedYN = 0)";
            } else if ($status == 2) {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 0)";
            }else if ($status == 4) {
                $status_filter = " AND ((confirmedYN = 2 AND approvedYN != 1) or (confirmedYN = 3 AND approvedYN != 1))";
            }else {
                $status_filter = " AND ( confirmedYN = 1 AND approvedYN = 1)";
            }
        }
        $searches='';
        if($sSearch){
            $search = str_replace("\\", "\\\\", $sSearch);
            //$this->datatables->or_like('contractCode',"$search");
            $searches = " AND (( contractCode Like '%$search%' ESCAPE '!') OR ( contractType Like '%$sSearch%' ESCAPE '!') OR ( det.transactionAmount Like '%$sSearch%') OR (contractNarration Like '%$sSearch%') OR (srp_erp_customermaster.customerName Like '%$sSearch%') OR (contractDate Like '%$sSearch%') OR (contractExpDate Like '%$sSearch%') OR (referenceNo Like '%$sSearch%')) ";
        }
        $where = "srp_erp_contractmaster.companyID = " . $companyid . $customer_filter . $date . $contractType_filter . $status_filter . $searches."";
        $this->datatables->select('srp_erp_contractmaster.referenceNo as referenceNo,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.confirmedByEmpID as confirmedByEmp,srp_erp_contractmaster.closedYN as closedYN,contractCode,contractNarration,srp_erp_customermaster.customerName as customerMasterName,,documentID, transactionCurrencyDecimalPlaces ,transactionCurrency,confirmedYN,approvedYN, contractType, srp_erp_contractmaster.createdUserID as createdUser,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate ,DATE_FORMAT(contractExpDate,\'' . $convertFormat . '\') AS contractExpDate,det.transactionAmount as total_value,ROUND(det.transactionAmount,2) as detTransactionAmount,srp_erp_contractmaster.isDeleted as isDeleted,srp_erp_contractmaster.isSystemGenerated as isSystemGenerated');
        $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
        $this->datatables->where($where);

        $this->datatables->from('srp_erp_contractmaster');
        $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
        $this->datatables->add_column('detail', '<b>Customer Name : </b> $2 <br> <b> Type  : </b> $5<br> <b>Document Date : </b> $3 <b style="text-indent: 1%;">&nbsp | &nbsp Document Exp Date : </b> $4 <br> <b>Comments : </b> $1   <br> <b>Reference Number : </b>  $6 ', 'contractNarration,customerMasterName,contractDate,contractExpDate,contractType,referenceNo');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,documentID,contractAutoID)');
        $this->datatables->add_column('edit', '$1', 'load_contract_action_nh(contractAutoID,confirmedYN,approvedYN,createdUser,documentID,confirmedYN,isDeleted,confirmedByEmp,isSystemGenerated, closedYN)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }
    function load_contract_conformation_nh()
    {
        $contractAutoID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('contractAutoID') ?? '');
        $data['extra'] = $this->Quotation_contract_model->fetch_contract_template_data($contractAutoID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('contractAutoID', trim($contractAutoID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_contractmaster');
        $documentid = $this->db->get()->row_array();

        $printHeaderFooterYN=1;
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['Approved'] = $documentid['approvedYN'];
        $this->db->select('printHeaderFooterYN');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', $documentid['documentID']);
        $this->db->from('srp_erp_documentcodemaster');
        $result = $this->db->get()->row_array();
        $printHeaderFooterYN =$result['printHeaderFooterYN'];
        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        $data['ALD_policyValue'] = getPolicyValues('ALD', 'All');
        $data['approver_details'] = approved_emp_details($documentid['documentID'], $contractAutoID);

        if (!$this->input->post('html')) {
            $data['signature'] = $this->Quotation_contract_model->fetch_signaturelevel();
        } else {
            $data['signature'] = '';
        }
        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view_nh', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->view('system/quotation_contract/erp_contract_print_nh', $data);
        }
    }
    function contract_confirmation_nh()
    {
        echo json_encode($this->Quotation_contract_model->contract_confirmation_nh());
    }
    function fetch_quotation_contract_detail()
    {
        echo json_encode($this->Quotation_contract_model->fetch_quotation_contract_detail());
    }
    function delete_qut_detail()
    {
        echo json_encode($this->Quotation_contract_model->delete_qut_detail());
    }
    function update_all_qut_items()
    {
        $projectExist = project_is_exist();
        $searches = $this->input->post('search');
        $itemAutoID = $this->input->post('itemAutoID');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity Requested', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Sales Price', 'trim|required|greater_than[0]');

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
            echo json_encode($this->Quotation_contract_model->update_qut_items());
        }
    }
    function save_quotation_contract_approval_nh()
    {
        $system_code = trim($this->input->post('contractAutoID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = trim($this->input->post('code') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('contractAutoID');
                $this->db->where('contractAutoID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_contractmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval_nh());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('contractAutoID');
            $this->db->where('contractAutoID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            //$this->db->where('confirmedYN !=', 1);
            $this->db->from('srp_erp_contractmaster');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Quotation_contract_model->save_quotation_contract_approval_nh());
                    }
                }
            }
        }
    }
    function fetch_quotation_contract_approval_nh()
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
            $this->datatables->select('referenceNo as referenceNo,contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_customermaster.customerName as customerName,det.transactionAmount as detTransactionAmount');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
            $this->datatables->from('srp_erp_contractmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID AND srp_erp_documentapproved.approvalLevelID = srp_erp_contractmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_contractmaster.currentLevelNo');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where_in('srp_erp_approvalusers.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
            $this->datatables->add_column('edit', '$1', 'con_action_approval_nh(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');

            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('referenceNo as referenceNo,contractType,srp_erp_contractmaster.documentID as document,srp_erp_contractmaster.contractAutoID as contractAutoID,srp_erp_contractmaster.companyCode,contractCode,contractNarration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(contractDate,\'' . $convertFormat . '\') AS contractDate,transactionCurrencyDecimalPlaces ,transactionCurrency,det.transactionAmount as total_value,ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_customermaster.customerName as customerName,det.transactionAmount as detTransactionAmount');
            $this->datatables->join('(SELECT SUM(transactionAmount + IFNULL(taxAmount, 0)) as transactionAmount,contractAutoID FROM srp_erp_contractdetails GROUP BY contractAutoID) det', '(det.contractAutoID = srp_erp_contractmaster.contractAutoID)', 'left');
            $this->datatables->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID', 'left');
            $this->datatables->from('srp_erp_contractmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_contractmaster.contractAutoID');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('QUT', 'CNT', 'SO'));
            $this->datatables->where('srp_erp_contractmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,contractAutoID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,contractAutoID)');
            $this->datatables->add_column('edit', '$1', 'con_action_approval_nh(contractAutoID,approvalLevelID,approvedYN,documentApprovedID,document,0)');
            $this->datatables->add_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');

            echo $this->datatables->generate();
        }

    }

    function close_contract()
    {
        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        $this->form_validation->set_rules('closedDate', 'Date', 'trim|required');
        $this->form_validation->set_rules('contractAutoID', 'contract ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->close_contract());
        }
    }
    function fetch_converted_waccost()
    {
         $itemautoID = $this->input->post('itemAutoID');
        $contactAutoID = $this->input->post('contractAutoID');
        $uomexrate = $this->input->post('uomexrate');
        $itemdetail = $this->db->query("SELECT companyLocalWacAmount FROM `srp_erp_itemmaster` WHERE  itemAutoID = $itemautoID")->row_array();

        $localwacAmount = trim($this->input->post('LocalWacAmount') ?? '');
        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $contactAutoID);
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round((($itemdetail['companyLocalWacAmount'] / $localCurrency['conversion'])/$uomexrate),$result['transactionCurrencyDecimalPlaces']);
        echo json_encode($unitprice);

        //return array('status' => true, 'amount' => $unitprice);
    }
    function load_unitprice_exchangerate_convertion()
    {
        $contactAutoID = $this->input->post('contractAutoID');
        $uomexrate = (($this->input->post('uomexrate')>0)? $this->input->post('uomexrate'):1);
        $companyLocalWacAmount=  $this->input->post('LocalWacAmount');
        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('contractAutoID', $contactAutoID);
        $result = $this->db->get('srp_erp_contractmaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round((($companyLocalWacAmount/ $localCurrency['conversion'])/$uomexrate),$result['transactionCurrencyDecimalPlaces']);
        echo json_encode($unitprice);
    }

    /* Function reference number */
    function  fetch_referenceNo(){

        $result=$this->Quotation_contract_model->fetch_referenceNo();
        //var_dump($result);
        if($result){
            echo json_encode(array('isExist' => 1, 'message' => 'Reference Number already exists'));
        } else {
            echo json_encode(array('isExist' => 0, 'message' => ' '));
        }

    }
    /* End  Function */

    function fetch_line_tax_and_vat()
    {
        echo json_encode($this->Quotation_contract_model->fetch_line_tax_and_vat());
    }

    function load_line_tax_amount()
    {
        echo json_encode($this->Quotation_contract_model->load_line_tax_amount());
    }

    function fetch_line_visibility_edit()
    {
        echo json_encode($this->Quotation_contract_model->fetch_line_visibility_edit());
    }

    /* 
        Datatable
        Function fetch crew for contract
     */
    function fetch_crew_list_contract(){
        $contactAutoID = $this->input->post('contractAutoID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_contractcrew.*, srp_erp_contractcrew.id, srp_erp_contractcrew.isPrimary as isPrimary');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('contractAutoID',$contactAutoID);
        $this->datatables->from('srp_erp_contractcrew');
        $this->datatables->add_column('action','$1','fetch_crew_action()');
        $this->datatables->add_column('isPrimary','$1','get_isprimary(isPrimary)');
        echo $this->datatables->generate();
    }

    /* Function fetch crew for contract */
    function fetch_assets_list_contract(){
        $contactAutoID = $this->input->post('contractAutoID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('id,contractAutoID,faID,faCode,assetName,assetRef,groupToName,groupToID');
        $this->datatables->where('companyID',$companyID);
        $this->datatables->where('contractAutoID',$contactAutoID);
        $this->datatables->from('srp_erp_contractassets');
        $this->datatables->add_column('action','$1','fetch_asset_action(id)');
        echo $this->datatables->generate();
    }

    function save_contract_job_crew(){

        $this->form_validation->set_rules('contractAutoID', 'contract ID', 'trim|required');
        $employee = $this->input->post('employee');

        foreach($employee as $key => $emp){
            $this->form_validation->set_rules("employee[{$key}]", 'Employee', 'trim|required');
            $this->form_validation->set_rules("crew_designation[{$key}]", 'Employee Designation', 'trim|required');
        }

      
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_contract_job_crew());
        }
    }

    function save_contract_group_to(){

        $this->form_validation->set_rules('groupName', 'Group Name', 'trim|required');
        $this->form_validation->set_rules('groupType', 'Group Type', 'trim|required');
        $this->form_validation->set_rules('contractAutoID', 'Contract', 'trim|required');

      
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_contract_group_to());
        }
    }

    function save_contract_group_to_category(){

        $this->form_validation->set_rules('groupName', 'Group Name', 'trim|required');
        //$this->form_validation->set_rules('groupType', 'Group Type', 'trim|required');
        $this->form_validation->set_rules('contractAutoID', 'Contract', 'trim|required');

      
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_contract_group_to_category());
        }
    }

    function edit_contract_job_crew(){
        $this->form_validation->set_rules('contractAutoID', 'contract ID', 'trim|required');
        $this->form_validation->set_rules('employee', 'Employee', 'trim|required');
        $this->form_validation->set_rules('crew_designation', 'Employee Desigantion', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->edit_contract_job_crew());
        }

    }

    function delete_crew_detail(){
        echo json_encode($this->Quotation_contract_model->delete_crew_detail());
    }

    function delete_checklist_detail(){
        echo json_encode($this->Quotation_contract_model->delete_checklist_detail());
    }

    function delete_asset_detail(){
        echo json_encode($this->Quotation_contract_model->delete_asset_detail());
    }

    function delete_contract_visibility(){
        echo json_encode($this->Quotation_contract_model->delete_contract_visibility());
    }

    function save_contract_assets(){
        $this->form_validation->set_rules('contractAutoID', 'contract ID', 'trim|required');
        $assets = $this->input->post('assets');

        foreach($assets as $key => $emp){
            $this->form_validation->set_rules("assets[{$key}]", 'Assets', 'trim|required');
            $this->form_validation->set_rules("assets_name[{$key}]", 'Assets Name', 'trim|required');
        }

      
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_contract_assets());
        }
    }

    function edit_contract_asset_crew(){

        $this->form_validation->set_rules('contractAutoID', 'Contract ID', 'trim|required');
        $this->form_validation->set_rules('assets', 'Assets', 'trim|required');
        $this->form_validation->set_rules('assets_name', 'Assets Name', 'trim|required');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->edit_contract_asset_crew());
        }
    }

    function get_emp_details(){
        echo json_encode($this->Quotation_contract_model->get_emp_details());
    }

    function fetch_payment_advice()
    {
        $contractAutoID = $this->input->post('contractAutoID');
        echo json_encode($this->Quotation_contract_model->fetch_contract_template_data($contractAutoID));
    }

    function fetch_payment_application_details()
    {
        $autoID = $this->input->post('autoID');
        echo json_encode($this->Quotation_contract_model->fetch_payment_application_details($autoID));
    }

    function checkConfirmedPA()
    {
        $contractAutoID = $this->input->post('contractAutoID');
        $this->db->select('confirmedYN');
        $this->db->where('contractAutoID', $contractAutoID);
        $result = $this->db->get('srp_erp_payment_application')->row_array();
        if(isset($result)){
            echo json_encode(true);
        } else{
            echo json_encode(false);
        }
        
    }
    
    function edit_payment_application_details()
    {
        $autoID = $this->input->post('autoID');
        echo json_encode($this->Quotation_contract_model->edit_payment_application_details($autoID));
    }

    function update_payment_application_item_details()
    {
        $PADetailsAutoID = $this->input->post('PADetailsAutoID');
        $currentQty = $this->input->post('currentQty');
        $unittransactionAmount = $this->input->post('unittransactionAmount');
        $prevQty = $this->input->post('prevQty');
        $PAcuQty = $this->input->post('PAcuQty');
        echo json_encode($this->Quotation_contract_model->update_payment_application_item_details($PADetailsAutoID,$currentQty,$unittransactionAmount,$prevQty,$PAcuQty));
    }

    function fetch_payment_applications_headers()
    {
        $contractAutoID = $this->input->post('contractAutoID');
        echo json_encode($this->Quotation_contract_model->fetch_payment_applications_headers($contractAutoID));
    }

    function confirm_payment_application(){
        $PAautoID = $this->input->post('autoID');
        echo json_encode($this->Quotation_contract_model->confirm_payment_application($PAautoID));
    }

    function save_payment_application_header_and_details(){
            $contractAutoID = $this->input->post('contractAutoID');
            $autoID = $this->input->post('autoID');

            $checkConfirmedPA = checkConfirmedPA($contractAutoID);
            if($checkConfirmedPA == true){
                echo json_encode("pending"); //  Pending confirmation
            }else{

                $checkDocumentQty = checkAllDocumentQtyIssued($contractAutoID,$autoID);
                if($checkDocumentQty == true){

                    $this->db->trans_start();
                    //$documentID = 'PA-00001';
                    $randNo =random_string('numeric', 5);
                    $documentID= 'PA/' . $randNo;
                    
                    $company_id = current_companyID();
                    $data['companyID'] = $company_id;
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['confirmedBy'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];           
                    $data['documentID'] = $documentID;
                    $data['confirmedYN'] = 0; // set default 0
                    $data['contractAutoID'] = $contractAutoID;
                    
                    $this->db->insert('srp_erp_payment_application', $data);
                    $last_id = $this->db->insert_id();
                    if($last_id){
                        $headerDetailsItems = $this->Quotation_contract_model->fetch_contract_template_data_new($contractAutoID, $last_id);    
                        
                        foreach ($headerDetailsItems['detailsNew'] as $det) {
                            $detailPA['PAAutoID'] = $last_id;
                            $detailPA['contractAutoID'] = $contractAutoID;
                            $detailPA['contractDetailsAutoID'] = $det['contractDetailsAutoID'];
                            $detailPA['itemAutoID'] = $det['itemAutoID'];
                            $detailPA['itemSystemCode'] = $det['itemSystemCode'];
                            $detailPA['itemDescription'] = $det['itemDescription'];
                            $detailPA['itemCategory'] = $det['itemCategory'];     
                            $detailPA['requestedQty'] = ($det['cumilativeQty'] - $det['totalPreviousQty']);         
                            $detailPA['unittransactionAmount'] = $det['unittransactionAmount'];                   
                            $detailPA['cuQty'] = $det['cumilativeQty'];    
                            $detailPA['currentQty'] = 0;   
                            $detailPA['PAcuQty'] = ($det['totalPreviousQty']+$detailPA['currentQty']);   
                            $detailPA['prevqty'] = $det['totalPreviousQty'];        
                            $detailPA['currentAmount'] = 0;      
                            $detailPA['cumilativeAmount'] = ($det['PAcuQty'] * $det['unittransactionAmount']); 
                            $detailPA['prevAmount'] = ($detailPA['prevqty'] * $detailPA['unittransactionAmount']); 
        
                            if($last_id) {
                                $this->db->insert('srp_erp_payment_application_details', $detailPA);
                                $last_de_detailID = $this->db->insert_id();
                            }
                            
                        }
                        
                    }        
                    
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->session->set_flashdata('e', 'Saved Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        echo json_encode(false);
                        //return array('status' => false);
                    } else {
                        $this->session->set_flashdata('s', 'Saved Successfully.');
                        $this->db->trans_commit();
                        
                        echo json_encode(true);
                                
                    }
                } else {
                    echo json_encode("qtyOver"); //  Over qty
                }
            }
    }

    function assignItem_checklist_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $contractAutoID = $this->input->post('contractAutoID');
        $text = trim($this->input->post('Search') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((name Like '%" . $text . "%') OR (	document_reference_code Like '%" . $text . "%'))";
        }

        $data['checklists'] = $this->db->query("SELECT * FROM srp_erp_op_checklist_master where companyID = {$companyID} AND status =1  AND id NOT IN (SELECT checklistID FROM srp_erp_op_module_contractchecklist WHERE contractAutoID = {$contractAutoID} AND companyID = {$companyID}) $search_string")->result_array();

        //$data['checklists'] = $this->db->query("SELECT * FROM srp_erp_checklistmaster where companyID = {$companyID}  AND isActive =1  $search_string")->result_array();

        $this->load->view('system/quotation_contract/erp_quotation_checklist_model', $data);
    }

    function assignCheckListForContract()
    {
        echo json_encode($this->Quotation_contract_model->assignCheckListForContract());
    }

    function fetch_check_list_contract(){
        $contactAutoID = $this->input->post('contractAutoID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_op_module_contractchecklist.contractChecklistAutoID as contractChecklistAutoID,srp_erp_op_module_contractchecklist.checklistAccessUser as checklistAccessUser,srp_erp_op_module_contractchecklist.checklistID as checklistID, srp_erp_op_module_calling_list.callingCode as callingCode ,srp_erp_op_module_calling_list.callingName as callingName,srp_erp_op_checklist_master.document_reference_code as documentID,srp_erp_op_checklist_master.name as checklistDescription');
        $this->datatables->from('srp_erp_op_module_contractchecklist');
        $this->datatables->join('srp_erp_op_module_calling_list', 'srp_erp_op_module_calling_list.callingCode = srp_erp_op_module_contractchecklist.callingCode', 'left');
        $this->datatables->join('srp_erp_op_checklist_master', ' srp_erp_op_checklist_master.id = srp_erp_op_module_contractchecklist.checklistID', 'left');
        $this->datatables->where('srp_erp_op_module_contractchecklist.companyID',$companyID);
        $this->datatables->where('srp_erp_op_module_contractchecklist.contractAutoID',$contactAutoID);
        $this->datatables->add_column('action','$1','fetch_checklist_contract_action(contractChecklistAutoID,checklistID)');
        $this->datatables->add_column('call','$1','make_contract_calling_dropDown(callingCode,contractChecklistAutoID)');
        $this->datatables->add_column('user','$1','make_contract_checklist_user_dropDown(checklistAccessUser,contractChecklistAutoID)');
        echo $this->datatables->generate();
    }

    function fetch_check_list_contract_job()
    {
        $contactAutoID = $this->input->post('contractAutoID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->db->select('srp_erp_op_module_contractchecklist.contractChecklistAutoID as contractChecklistAutoID,srp_erp_op_module_contractchecklist.checklistAccessUser as checklistAccessUser,srp_erp_op_module_contractchecklist.checklistID as checklistID, srp_erp_op_module_calling_list.callingCode as callingCode ,srp_erp_op_module_calling_list.callingName as callingName,srp_erp_op_checklist_master.document_reference_code as documentID,srp_erp_op_checklist_master.name as checklistDescription');
        $this->db->from('srp_erp_op_module_contractchecklist');
        $this->db->join('srp_erp_op_module_calling_list', 'srp_erp_op_module_calling_list.callingCode = srp_erp_op_module_contractchecklist.callingCode', 'left');
        $this->db->join('srp_erp_op_checklist_master', ' srp_erp_op_checklist_master.id = srp_erp_op_module_contractchecklist.checklistID', 'left');
        $this->db->where('srp_erp_op_module_contractchecklist.companyID',$companyID);
        $this->db->where('srp_erp_op_module_contractchecklist.contractAutoID',$contactAutoID);
        $this->db->order_by('srp_erp_op_module_contractchecklist.contractChecklistAutoID','desc');

        $data['details'] = $this->db->get()->result_array();

        $html = $this->load->view('system/quotation_contract/erp_contract_html_view_checklist_job', $data, true);

        echo $html;
    }

    public function selectCallingUpdate()
    {
        $this->form_validation->set_rules('masterID', 'contract', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->selectCallingUpdate());
        }
    }

    public function selectChecklistUserUpdate()
    {
        $this->form_validation->set_rules('masterID', 'Check List', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Quotation_contract_model->selectChecklistUserUpdate());
        }
    }

    function save_visibility()
    {
       
        $this->form_validation->set_rules('section', 'section', 'trim|required');
        $this->form_validation->set_rules('customerCode[]', 'User', 'trim|required');
        $this->form_validation->set_rules('actionAr[]', 'Action', 'trim|required');
      
       
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_visibility());
        }
    }

    function save_visibility_edit()
    {
       
        $this->form_validation->set_rules('section_edit', 'section', 'trim|required');
        $this->form_validation->set_rules('customerCode_edit[]', 'User', 'trim|required');
        $this->form_validation->set_rules('actionAr_edit[]', 'Action', 'trim|required');
      
       
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->save_visibility_edit());
        }
    }

    function fetch_contract_visibility_table(){
        $contactAutoID = $this->input->post('contractAutoID');
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('visibilityAutoID,visibilityuserIDs,actionCodes,sectionCode');
        $this->datatables->from('srp_erp_op_module_contractvisibility');
        //$this->datatables->join('srp_erp_op_module_calling_list', 'srp_erp_op_module_calling_list.callingCode = srp_erp_op_module_contractchecklist.callingCode', 'left');
        //$this->datatables->join('srp_erp_checklistmaster', ' srp_erp_checklistmaster.checklistID = srp_erp_op_module_contractchecklist.checklistID', 'left');
        $this->datatables->where('srp_erp_op_module_contractvisibility.companyID',$companyID);
        $this->datatables->where('srp_erp_op_module_contractvisibility.contractAutoID',$contactAutoID);
        $this->datatables->add_column('action','$1','fetch_visibility_contract_action(visibilityAutoID)');
        $this->datatables->add_column('actionCodes','$1','action_codes(actionCodes)');
        $this->datatables->add_column('name','$1','make_user_name_arr(visibilityuserIDs)');
        echo $this->datatables->generate();
    }

    function create_amendment_for_document(){
        $this->form_validation->set_rules('ammendmentType[]', 'Ammendment Type', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Quotation_contract_model->create_amendment_for_document());
        }
    }

    function close_amendment_for_document(){
        echo json_encode($this->Quotation_contract_model->close_amendment_for_document());
    }

    function fetch_amendment_details(){
        echo json_encode($this->Quotation_contract_model->fetch_amendment_details());
    }

    function get_customer_order_details(){
        echo json_encode($this->Quotation_contract_model->get_customer_order_details());
    }

    function fetch_extra_charge_tbl(){
        $contractAutoID = $this->input->post('contractAutoID');

        $data = array();
        $data['charge_data'] = $this->Quotation_contract_model->get_extra_charges_records($contractAutoID);
        $data['master'] = get_contractmaster_details($contractAutoID);

        $data['total_data'] = $this->Quotation_contract_model->get_extra_charges_records($contractAutoID,2);

        // echo '<pre>';
        // print_r($data); exit;

        $html = $this->load->view('system/quotation_contract/ajax/cost_allocation_sar', $data, true);

        echo $html;

    }

    function get_backtoback_item_view(){

        $contractAutoID = $this->input->post('contractAutoID');

        $data = array();
        $data['itemDetails'] = $this->Quotation_contract_model->get_contract_details($contractAutoID);
        $data['master'] = get_contractmaster_details($contractAutoID);

        $html = $this->load->view('system/quotation_contract/ajax/backtoback_item', $data, true);

        echo $html;

    }

    function update_contract_extra_charge(){
        echo json_encode($this->Quotation_contract_model->update_contract_extra_charge());
    }

    function delete_extra_charge_entry(){
        echo json_encode($this->Quotation_contract_model->delete_extra_charge_entry());
    }

    function update_contract_detail_value(){
        echo json_encode($this->Quotation_contract_model->update_contract_detail_value());
    }

    function update_ap_value(){
        echo json_encode($this->Quotation_contract_model->update_ap_value());
    }

    function update_rounding_value(){
        echo json_encode($this->Quotation_contract_model->update_rounding_value());
    }

    

    function Quotation_contract_header(){

        $contractAutoID = $this->input->post('autoID');

        echo json_encode(get_contractmaster_details($contractAutoID));

    }

    function update_approved_status(){
        echo json_encode($this->Quotation_contract_model->update_approved_status());
    }

    function get_purchase_order_details(){
        echo json_encode($this->Quotation_contract_model->get_purchase_order_details());
    }

}