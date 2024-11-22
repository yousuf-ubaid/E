<?php

class Srm_master extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('srm');
        $this->load->model('Srm_master_model');
        $this->load->library('s3');

    }

    function save_customer()
    {
        if (!$this->input->post('customerAutoID')) {
            $this->form_validation->set_rules('customerCurrency', 'customer Currency', 'trim|required');
        }
        $this->form_validation->set_rules('customercode', 'customer Code', 'trim|required');
        $this->form_validation->set_rules('customerName', 'customer Name', 'trim|required');
        $this->form_validation->set_rules('customercountry', 'customer country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 'e', 'message' => validation_errors()));
        } else {
            ;
            echo json_encode($this->Srm_master_model->save_customer());
        }
    }


    function save_supplier()
    {
        if (!$this->input->post('supplierAutoID')) {
            $this->form_validation->set_rules('supplierCurrency', 'supplier Currency', 'trim|required');
        }
        $this->form_validation->set_rules('suppliercode', 'supplier Code', 'trim|required');
        $this->form_validation->set_rules('supplierName', 'suplier Name', 'trim|required');
        $this->form_validation->set_rules('suppliercountry', 'supplier country', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 'e', 'message' => validation_errors()));
        } else {
            ;
            echo json_encode($this->Srm_master_model->save_supplier());
        }
    }

    function fetch_customer()
    {
        $customer_filter = '';
        $category_filter = '';
        $currency_filter = '';

        $companyid = $this->common_data['company_data']['company_id'];
        $where = "srp_erp_srm_customermaster.companyID = {$companyid} ";
        $this->datatables->select('customerAutoID,customerSystemCode,secondaryCode')
            ->where($where)
            ->from('srp_erp_customermaster');

        $this->datatables->add_column('customer_detail', '<b>Name : </b> $1 &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b>$5<br><b>Address : </b> $2 &nbsp;&nbsp;$3 &nbsp;&nbsp;$4.<br><b>customer Currency : </b>$6 &nbsp;&nbsp;&nbsp;<b> Email </b> $7  &nbsp;&nbsp;&nbsp;<b>Telephone</b> $8', 'customerName,customerAddress1, customerAddress2, customerCountry, secondaryCode, customerCurrency, customerEmail,customerTelephone');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '$1', 'editcustomer(customerAutoID)');
        $this->datatables->edit_column('amt', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(Amount,partyCurrencyDecimalPlaces),customerCurrency');

        echo $this->datatables->generate();
    }

    function fetch_customer_all()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $status = trim($this->input->post('status') ?? '');
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND CustomerName Like '%" . $text . "%'";
        }

        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND CustomerName Like '" . $sorting . "%'";
        }

        $filter_status = '';
        if (isset($status) && $status == 1) {
            $filter_status = " AND isActive = 1";
        } else if (isset($status) && $status == 0) {
            $filter_status = " AND isActive = 0";
        }

        $where = "companyID = " . $companyid . $search_string . $search_sorting . $filter_status;

        $this->db->select('*,CountryDes');
        $this->db->from('srp_erp_srm_customermaster');
        $this->db->join('srp_erp_countrymaster', 'srp_erp_countrymaster.countryID = srp_erp_srm_customermaster.customerCountry', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('customerAutoID', 'desc');
        $result = $this->db->get()->result_array();
        $data['output'] = $result;
        $this->load->view('system/srm/customer/ajax/load_customer_master', $data); //_style2
    }


    function fetch_supplier_all()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $status = trim($this->input->post('status') ?? '');
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filtervalue') ?? '');

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND supplierName Like '%" . $text . "%'";
        }

        $search_sorting = '';
        if (isset($sorting) && $sorting != '#') {
            $search_sorting = " AND supplierName Like '" . $sorting . "%'";
        }

        $filter_status = '';
        if (isset($status) && $status == 1) {
            $filter_status = " AND isActive = 1";
        } else if (isset($status) && $status == 0) {
            $filter_status = " AND isActive = 0";
        }

        $where = "companyID = " . $companyid . $search_string . $search_sorting . $filter_status;
        $this->db->select('*,CountryDes');
        $this->db->from('srp_erp_srm_suppliermaster');
        $this->db->join('srp_erp_countrymaster', 'srp_erp_countrymaster.countryID = srp_erp_srm_suppliermaster.supplierCountry', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('supplierAutoID', 'desc');
        $data['output'] = $this->db->get()->result_array();
        $this->load->view('system/srm/supplier/ajax/load_supplier_master', $data);

    }

    function fetch_supplier_view()
    {
        $supplierID = $this->input->post('supplierID');
        $this->db->select('*');
        $this->db->from('srp_erp_srm_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        $result = $this->db->get()->row_array();
        $data['output'] = $result;
        $this->load->view('system/srm/supplier/ajax/ajax_view_supplier_detiles', $data);
    }


    function fetch_itemcode_view()
    {
        $supplierID = $this->input->post('supplierID');
        $this->db->select('*');
        $this->db->from('srp_erp_srm_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        $result = $this->db->get()->row_array();
        $data['output'] = $result;
        $this->load->view('system/srm/item/ajax/ajax_load_item_master_style', $data);
    }


    function load_supplier_items_details()
    {
        echo json_encode($this->Srm_master_model->load_supplier_items_details());
    }

    function load_supplier_itemsmaster()
    {

        echo json_encode($this->Srm_master_model->load_supplier_itemsmaster());


        /*out put => json array */
//        echo json_encode($this->Srm_master_model->load_supplier_items_details());


    }

    function save_supplierItem()
    {
        echo json_encode($this->Srm_master_model->save_supplierItem());
    }

    function load_customer_header()
    {
        echo json_encode($this->Srm_master_model->load_customer_header());
    }

    function load_supplier_header()
    {
        echo json_encode($this->Srm_master_model->load_supplier_header());
    }

    function load_supplier_company_request_header()
    {
        echo json_encode($this->Srm_master_model->load_supplier_company_request_header());
    }


    function delete_supplier_item()
    {
        echo json_encode($this->Srm_master_model->delete_supplier_item());
    }

    function delete_supplier_srm()
    {
        echo json_encode($this->Srm_master_model->delete_supplier_srm());
    }

    function delete_supplier()
    {
        echo json_encode($this->Srm_master_model->delete_supplier());
    }

    function delete_customer()
    {
        echo json_encode($this->Srm_master_model->delete_customer());
    }

    function load_supplier_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        $this->db->select('*,CountryDes,DATE_FORMAT(srp_erp_srm_suppliermaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(srp_erp_srm_suppliermaster.modifiedDateTime,\'' . $convertFormat . '\') AS modifydate,srp_erp_srm_suppliermaster.createdUserName as createdUserName');
        $this->db->from('srp_erp_srm_suppliermaster');
        $this->db->where('supplierAutoID', $supplierAutoID);
        $this->db->join('srp_erp_countrymaster', 'srp_erp_countrymaster.countryID = srp_erp_srm_suppliermaster.supplierCountry', 'LEFT');
        $data['header'] = $this->db->get()->row_array();

        $this->load->view('system/srm/supplier/ajax/load_supplier_edit_view', $data);
    }

    function load_customer_editView()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $CustomerAutoID = trim($this->input->post('customerAutoID') ?? '');
        $this->db->select('*,CountryDes,DATE_FORMAT(srp_erp_srm_customermaster.createdDateTime,\'' . $convertFormat . '\') AS createdDate,DATE_FORMAT(srp_erp_srm_customermaster.modifiedDateTime,\'' . $convertFormat . '\') AS modifydate,srp_erp_srm_customermaster.createdUserName as createdUserName');
        $this->db->from('srp_erp_srm_customermaster');
        $this->db->where('CustomerAutoID', $CustomerAutoID);
        $this->db->join('srp_erp_countrymaster', 'srp_erp_countrymaster.countryID = srp_erp_srm_customermaster.customerCountry', 'LEFT');
        $data['header'] = $this->db->get()->row_array();

        $this->load->view('system/srm/customer/ajax/load_customer_edit_view', $data);
    }

    function load_supplier_all_notes()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 1 AND documentAutoID = " . $supplierAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_srm_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/srm/supplier/ajax/load_supplier_notes', $data);
    }

    function load_supplier_all_attachments()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 1  AND documentAutoID = " . $supplierAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_srm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/srm/supplier/ajax/load_all_supplier_attachements', $data);
    }


    function load_customer_order_detail_item_view()
    {
        $companyid = $this->common_data['company_data']['company_id'];

        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode');
        $this->db->from('srp_erp_srm_customerorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_customerorderdetails.itemAutoID', 'LEFT');
        $this->db->where('srp_erp_srm_customerorderdetails.companyID', $companyid);
        $this->db->where('srp_erp_srm_customerorderdetails.customerOrderID', $customerOrderID);
        $data['header'] = $this->db->get()->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_customer_item_order_table', $data);
    }

    function save_customer_order_header()
    {
        $this->form_validation->set_rules('customerID', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Inquiry Date', 'trim|required');
        $this->form_validation->set_rules('expiryDate', 'Expiry Date', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_customer_order_header());
        }
    }

    function add_supplier_notes()
    {
        $this->form_validation->set_rules('supplierAutoID', 'Supplier ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->add_supplier_notes());
        }
    }

    function load_customer_order_autoGeneratedID()
    {
        echo json_encode($this->Srm_master_model->load_customer_order_autoGeneratedID());
    }

    function save_customer_ordermaster_add()
    {
        echo json_encode($this->Srm_master_model->save_customer_ordermaster_add());
    }

    function save_customer_order_detail()
    {

        $searches = $this->input->post('search');

        foreach ($searches as $key => $search) {
            //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
            $this->form_validation->set_rules("itemAutoID[{$key}]", 'Item ID', 'trim|required');
            $this->form_validation->set_rules("UnitOfMeasureID[{$key}]", 'Unit Of Measure', 'trim|required');
            $this->form_validation->set_rules("quantityRequested[{$key}]", 'Quantity', 'trim|required|greater_than[0]');
            $this->form_validation->set_rules("estimatedAmount[{$key}]", 'Unit Cost', 'trim|required');
            $this->form_validation->set_rules("expectedDeliveryDate[{$key}]", 'Expected Delivery Date', 'trim');
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
            echo json_encode($this->Srm_master_model->save_customer_order_detail());
        }
    }

    function load_customer_order_master()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $customerID = trim($this->input->post('customerID') ?? '');
        $text = trim($this->input->post('searchOrder') ?? '');
        $statusID = trim($this->input->post('statusID') ?? '');
        $convertFormat = convert_date_format_sql();

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND customerOrderCode Like '%" . $text . "%'";
        }
        $filter_statusID = '';
        if (isset($statusID) && !empty($statusID)) {
            $filter_statusID = " AND status = {$statusID}";
        }
        $filter_customerID = '';
        if (isset($customerID) && !empty($customerID)) {
            $filter_customerID = " AND customerID = {$customerID}";
        }
        $where = "srp_erp_srm_customerordermaster.companyID = " . $companyid . $search_string . $filter_statusID . $filter_customerID;
        $this->db->select("srp_erp_srm_customerordermaster.customerOrderID,srp_erp_srm_customerordermaster.status,customerOrderCode,srp_erp_customermaster.customerName,srp_erp_srm_customerordermaster.contactPersonNumber,srp_erp_srm_customerordermaster.confirmedYN,srp_erp_srm_status.description as statusDescription,backgroundColor,fontColor,srp_erp_srm_customerordermaster.narration,CurrencyCode,DATE_FORMAT(expiryDate,'" . $convertFormat . "') AS expiryDate");
        $this->db->from('srp_erp_srm_customerordermaster');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_srm_status', 'srp_erp_srm_status.statusID = srp_erp_srm_customerordermaster.status', 'LEFT');
        $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.customerOrderID = srp_erp_srm_customerordermaster.customerOrderID', 'LEFT');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_customerordermaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('srp_erp_srm_customerordermaster.customerOrderID', 'DESC');
        $data['output'] = $this->db->get()->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_customer_order_management', $data);
    }

    function load_customer_order_inquiry_master()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $text = trim($this->input->post('searchInquiry') ?? '');
        $confirmedYN = trim($this->input->post('confirmedYN') ?? '');

        $filterconfirmedYN = '';
        if ($confirmedYN == 0) {
            $filterconfirmedYN = " AND srp_erp_srm_orderinquirymaster.confirmedYN = 0";
        } else if ($confirmedYN == 1) {
            $filterconfirmedYN = " AND srp_erp_srm_orderinquirymaster.confirmedYN = 1";
        }

        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND documentCode Like '%" . $text . "%'";
        }

        $where = "srp_erp_srm_orderinquirymaster.companyID = " . $companyid . $search_string . $filterconfirmedYN;
        $this->db->select('srp_erp_srm_orderinquirymaster.confirmedYN,srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.inquiryType,srp_erp_srm_orderinquirymaster.documentCode as orderCode, customerName,customerOrderCode,srp_erp_srm_orderinquirymaster.confirmedYN as inquiryConfirm,CurrencyCode,srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN as isOrderReviewConfirmYN');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_orderinquirymaster.customerID', 'LEFT');
        $this->db->join('srp_erp_srm_customerordermaster', 'srp_erp_srm_customerordermaster.customerOrderID = srp_erp_srm_orderinquirymaster.customerOrderID', 'LEFT');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('inquiryID', 'DESC');
        $data['output'] = $this->db->get()->result_array();






        $this->load->view('system/srm/customer-order/ajax/load_customer_order_inquiry_management', $data);
    }

    function load_customerOrder_header()
    {
        echo json_encode($this->Srm_master_model->load_customerOrder_header());
    }

    function load_customerInquiry_header()
    {
        echo json_encode($this->Srm_master_model->load_customerInquiry_header());
    }

    function delete_customer_order_master()
    {
        echo json_encode($this->Srm_master_model->delete_customer_order_master());
    }

    function delete_customer_inquiry_master()
    {
        echo json_encode($this->Srm_master_model->delete_customer_inquiry_master());
    }

    function load_customerbase_ordersID()
    {
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];
        $orderID = $this->db->query("Select * from (SELECT
		detailtb.itemAutoID,
    detailtb.`customerOrderID`,
    mastertb.`customerOrderCode`,
		IFNULL(item.isChecked,0) as checked
FROM
    `srp_erp_srm_customerordermaster` mastertb
		 LEFT JOIN srp_erp_srm_customerorderdetails detailtb on mastertb.customerOrderID=detailtb.customerOrderID
     LEFT join srp_erp_srm_inquiryitem item on detailtb.customerOrderID=item.orderMasterID and detailtb.itemAutoID=item.itemAutoID
WHERE
    mastertb.`customerID` = " . $this->input->post('customerID') . "
AND mastertb.`transactionCurrencyID` = " . $this->input->post('currency') . "
AND mastertb.`confirmedYN` = 1
AND mastertb.`companyID` = " . $companyID . "
GROUP BY detailtb.customerOrderID,detailtb.itemAutoID )tbl1  group by customerOrderID")->result_array();

        if (isset($orderID)) {
            foreach ($orderID as $row) {
                $data_arr[trim($row['customerOrderID'] ?? '')] = trim($row['customerOrderCode'] ?? '');
            }
        }
        echo form_dropdown('customer_orderID[]', $data_arr, '', 'class="form-control " id="customer_orderID" multiple="" ');
    }

    function load_customerOrder_BaseItem()
    {
        echo json_encode($this->Srm_master_model->load_customerOrder_BaseItem());
    }

/*    function load_customer_inquiry_detail_items_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $orderID = join($this->input->post('orderID'), ",");

        $where = "srp_erp_srm_inquiryitem.companyID = '{$companyID}' AND srp_erp_srm_inquiryitem.orderMasterID IN ($orderID) ";

        $this->db->select('srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,srp_erp_srm_inquiryitem.orderMasterID,srp_erp_srm_customerordermaster.customerOrderCode');
        $this->db->from('srp_erp_srm_inquiryitem');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_inquiryitem.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_srm_customerordermaster', 'srp_erp_srm_customerordermaster.customerOrderID = srp_erp_srm_inquiryitem.orderMasterID', 'LEFT');
        $this->db->where($where);
        $data['header'] = $this->db->get()->result_array();
        $this->load->view('system/srm/customer-order/ajax/load_customerbase_inquiry_item_table', $data);
    }*/

    function load_customer_inquiry_detail_items_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $inquiryID = $this->input->post('inquiry_ID');
        $inquiryType = $this->input->post('inquiryType');
        if ($inquiryType=='Customer') {
            $orderID = join($this->input->post('orderID'), ",");
        }
        $this->db->select('*');
        $this->db->where('inquiryID', $inquiryID);
        $inqmaster = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();

        if($inquiryType=='Customer'){
            $where = "srp_erp_srm_customerorderdetails.companyID = '{$companyID}' AND srp_erp_srm_customerorderdetails.customerOrderID IN ($orderID)";

            $this->db->select('srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage as itemImage,srp_erp_itemmaster.itemSystemCode,srp_erp_srm_customerorderdetails.customerOrderID,srp_erp_srm_customerordermaster.customerOrderCode,srp_erp_srm_customerorderdetails.customerOrderID,srp_erp_currencymaster.CurrencyCode,transactionCurrencyDecimalPlaces,(requestedQty * unitAmount) as uamount,requestedQty');
            $this->db->from('srp_erp_srm_customerorderdetails');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_customerorderdetails.itemAutoID', 'LEFT');
            $this->db->join('srp_erp_srm_customerordermaster', 'srp_erp_srm_customerordermaster.customerOrderID = srp_erp_srm_customerorderdetails.customerOrderID', 'LEFT');
            $this->db->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = srp_erp_srm_customerordermaster.transactionCurrencyID','LEFT');
            $this->db->where($where);
            $data['header'] = $this->db->get()->result_array();

            foreach($data['header'] as $key => $val){
                $data['header'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['header'][$key]['itemImage'], '1 hour');
            }
        }elseif ($inquiryType=='PRQ'){

            $this->db->select('*');
            $this->db->where('documentID', 'PRQ');
            $this->db->where('companyID', $companyID);
            $this->db->where('activityMasterID',  $inqmaster['purchaseRequestID']);
            $this->db->where('empID',  $this->common_data['current_userID']);
            $this->db->where('userType',  0);
            $this->db->or_where('empID', null);
            $this->db->group_by('activityDetailID');
            $current_user_assign_items = $this->db->get('srp_erp_incharge_assign')->result_array();

            $currentUser_item =[0];

            if(count($current_user_assign_items)>0){
                foreach($current_user_assign_items as $val){
                    $currentUser_item[]=$val['activityDetailID'];
                }
            }

            $this->db->select('srp_erp_purchaserequestdetails.itemDescription,srp_erp_itemmaster.itemAutoID,srp_erp_purchaserequestdetails.purchaseRequestDetailsID,srp_erp_purchaserequestdetails.purchaseRequestID,srp_erp_purchaserequestdetails.itemSystemCode,srp_erp_purchaserequestdetails.totalAmount,srp_erp_purchaserequestdetails.itemAutoID,srp_erp_purchaserequestmaster.transactionCurrency,srp_erp_itemmaster.itemImage as itemImage,requestedQty as srmqty');
            $this->db->where('srp_erp_purchaserequestdetails.purchaseRequestID', $inqmaster['purchaseRequestID']);
            $this->db->where_in('srp_erp_purchaserequestdetails.purchaseRequestDetailsID', $currentUser_item);
            $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaserequestdetails.purchaseRequestID', 'LEFT');
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID','left');
            $prqdetail = $this->db->get('srp_erp_purchaserequestdetails')->result_array();
            $data['prqdetail']=$prqdetail;

            foreach($data['prqdetail'] as $key => $val){
                $data['prqdetail'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['prqdetail'][$key]['itemImage'], '1 hour');

                $this->db->select('*');
                $this->db->where('inquiryID', $inquiryID);
                $this->db->where('companyID', $companyID);
                $this->db->where('itemAutoID', $val['itemAutoID']);
                $inqmaster_line_doc = $this->db->get('srp_erp_srm_vendor_rfq_linewise_documents')->row_array();

                if($inqmaster_line_doc){
                    $data['prqdetail'][$key]['url_doc'] = $this->s3->createPresignedRequest($inqmaster_line_doc['url'], '1 hour');
                    $data['prqdetail'][$key]['line_doc_id'] = $inqmaster_line_doc['autoID'];
                }else{
                    $data['prqdetail'][$key]['url_doc'] = null;
                    $data['prqdetail'][$key]['line_doc_id'] = 0;
                }

            }

            $this->db->select('*');
            $this->db->where('documentID', "PRQ");
            $this->db->where('documentSystemCode', $inqmaster['purchaseRequestID']);
            $data['pr_document'] = $this->db->get('srp_erp_documentattachments')->result_array();

        }else{
            $data['header']='';
        }


        $data['inquiryheader']=$inqmaster;

        $data['inquiryID'] = $inquiryID;

        $this->load->view('system/srm/customer-order/ajax/load_customerbase_inquiry_item_table', $data);
    }

    function load_customer_inquiry_detail_sellars_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $inquiryMasterID = trim($this->input->post('inquiryID') ?? '');

        $where = "srp_erp_srm_inquiryitem.companyID = '{$companyID}' AND srp_erp_srm_inquiryitem.isChecked = 1 AND srp_erp_srm_inquiryitem.inquiryMasterID = '{$inquiryMasterID}'";

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.currentStock,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemAutoID as itemAutoID');
        $this->db->from('srp_erp_srm_inquiryitem');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_inquiryitem.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_srm_customerorderdetails', 'srp_erp_srm_customerorderdetails.itemAutoID = srp_erp_srm_inquiryitem.itemAutoID', 'LEFT');

        $this->db->where($where);
        $this->db->group_by('srp_erp_srm_inquiryitem.itemAutoID');
        $data['header'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where('srp_erp_srm_orderinquirymaster.inquiryID',$inquiryMasterID);
        $data['master_header'] = $this->db->get()->row_array();

        $data['suppliers'] = $this->db->query("SELECT requestedQty,expectedDeliveryDate,inquiryDetailID,supplierName,supplierSystemCode,srp_erp_srm_suppliermaster.supplierAutoID,srp_erp_srm_orderinquirydetails.isRfqCreated FROM srp_erp_srm_orderinquirydetails INNER JOIN srp_erp_srm_suppliermaster ON srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID WHERE inquiryMasterID = {$inquiryMasterID} GROUP BY srp_erp_srm_orderinquirydetails.supplierID")->result_array();

        $data['view'] =  $this->load->view('system/srm/customer-order/ajax/load_orderbase_inquiry_supplier_table', $data,true);

        $data['rfq_gen'] = $this->db->query("SELECT inquiryDetailID FROM `srp_erp_srm_orderinquirydetails` WHERE companyID = $companyID AND inquiryMasterID = $inquiryMasterID AND isRfqCreated = 1 ")->result_array();
       
        echo json_encode($data);

    }

    function save_order_inquiry()
    {
        $inquiryType=$this->input->post('inquiryType');
        $this->form_validation->set_rules('inquiryType', 'Inquiry Type', 'trim|required');
        $this->form_validation->set_rules('transactionCurrencyID', 'Currency', 'trim|required');
        $this->form_validation->set_rules('documentDate', 'Document Date', 'trim|required');

        if($inquiryType=='PRQ'){
            $this->form_validation->set_rules('purchaseRequestID', 'PRQ ID', 'trim|required');
        }elseif($inquiryType=='Customer'){
            $this->form_validation->set_rules('customerID', 'Customer Name', 'trim|required');
            $this->form_validation->set_rules('customer_orderID[]', 'Order ID', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_order_inquiry());
        }
    }

    function load_OrderID_BaseCurrency()
    {
        echo json_encode($this->Srm_master_model->load_OrderID_BaseCurrency());
    }

    function save_order_inquiry_itemDetail()
    {
        $this->form_validation->set_rules('inquiryID', 'InquiryID', 'trim|required');
        //$this->form_validation->set_rules('orderID', 'Order ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_order_inquiry_itemDetail());
        }
    }

    function ajax_update_orderInquiry_supplier()
    {
        $result = $this->Srm_master_model->xeditable_update('srp_erp_srm_orderinquirydetails', 'inquiryDetailID');
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'updated'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'updated Fail'));
        }
    }

    function order_inquiry_generate_supplier_rfq()
    {
       // $this->form_validation->set_rules('deliveryTerms', 'Delivery Terms', 'trim|required');
        $this->form_validation->set_rules('inquiryID', 'InquiryID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->order_inquiry_generate_supplier_rfq());
        }
    }

    function order_inquiry_generate_supplier_view_rfq()
    {
       // $this->form_validation->set_rules('deliveryTerms', 'Delivery Terms', 'trim|required');
        $this->form_validation->set_rules('inquiryID', 'InquiryID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->order_inquiry_generate_supplier_view_rfq());
        }
    }

    function add_url_expire_date()
    {
       // $this->form_validation->set_rules('deliveryTerms', 'Delivery Terms', 'trim|required');
        $this->form_validation->set_rules('expireDate', 'date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->add_url_expire_date());
        }
    }


    function submit_supplier_rfq()
    {
        echo json_encode($this->Srm_master_model->submit_supplier_rfq());
    }

    function load_orderbase_generated_rfq_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $inquiryMasterID = trim($this->input->post('inquiryID') ?? '');

        $where = "srp_erp_srm_orderinquirydetails.companyID = '{$companyID}' AND srp_erp_srm_orderinquirydetails.inquiryMasterID = '{$inquiryMasterID}' AND isRfqCreated = 1";

        $this->db->select('*,srp_erp_srm_suppliermaster.supplierSystemCode,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_orderinquirymaster.rfqExpDate,srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID', 'LEFT');
        $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderinquirydetails.inquiryMasterID', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['header'] = $this->db->get()->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_orderbase_generated_rfq_table', $data);
    }

    function load_orderbase_generated_rfq_view_with_po()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $inquiryMasterID = trim($this->input->post('inquiryID') ?? '');

        $where = "srp_erp_srm_orderinquirydetails.companyID = '{$companyID}' AND srp_erp_srm_orderinquirydetails.inquiryMasterID = '{$inquiryMasterID}' AND isRfqCreated = 1";

        $this->db->select('*,srp_erp_srm_suppliermaster.supplierSystemCode,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_orderinquirymaster.rfqExpDate,srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID', 'LEFT');
        $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderinquirydetails.inquiryMasterID', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['header'] = $this->db->get()->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_orderbase_generated_rfq_table_with_po', $data);
    }

    function supplier_rfq_print_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $where_header = "srp_erp_srm_orderinquirymaster.companyID = '{$companyID}' AND srp_erp_srm_orderinquirymaster.inquiryID = '{$inquiryMasterID}'";
        $this->db->select('documentCode,deliveryTerms,DATE_FORMAT(documentDate,"' . $convertFormat . '") AS inquiryDocumentDate');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where($where_header);
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        $data['supplier'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_company');
        $this->db->where('company_id', $companyID);
        $data['company'] = $this->db->get()->row_array();

        $where_detail = "srp_erp_srm_orderinquirydetails.companyID = '{$companyID}' AND srp_erp_srm_orderinquirydetails.inquiryMasterID = '{$inquiryMasterID}' AND srp_erp_srm_orderinquirydetails.isRfqCreated = 1 AND srp_erp_srm_orderinquirydetails.supplierID = '{$supplierID}'";
        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,UnitShortCode,DATE_FORMAT(srp_erp_srm_orderinquirydetails.expectedDeliveryDate,"' . $convertFormat . '") AS expectedDeliveryDate');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->where($where_detail);
        $data['detail'] = $this->db->get()->result_array();
        $html = $this->load->view('system/srm/customer-order/srm_order_inquiry_print', $data, true);
        if ($this->input->post('html')) {
            echo $html;
        } else {
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($html, 'A4', $data['extra']['master']['approvedYN']);
        }
    }

    function load_customer_BaseDetail()
    {
        echo json_encode($this->Srm_master_model->load_customer_BaseDetail());
    }

    function assignItems_supplier_orderInquiry()
    {
        echo json_encode($this->Srm_master_model->assignItems_supplier_orderInquiry());
    }

    
    function assignItems_supplier_view_orderInquiry()
    {
        echo json_encode($this->Srm_master_model->assignItems_supplier_view_orderInquiry());
    }

    function delete_customer_order_detail()
    {
        echo json_encode($this->Srm_master_model->delete_customer_order_detail());
    }

    function load_inquiry_reviewHeader()
    {
        echo json_encode($this->Srm_master_model->load_inquiry_reviewHeader());
    }

    function load_order_multiple_attachemts()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $documentAutoID = trim($this->input->post('customerOrderID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 3 AND documentAutoID = " . $documentAutoID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_srm_attachments');
        $this->db->where($where);
        $this->db->order_by('attachmentID', 'desc');
        $data['attachment'] = $this->db->get()->result_array();
        $this->load->view('system/srm/customer-order/ajax/load_order_multiple_attachement', $data);
    }

    function delete_srm_attachment()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments/SRM");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            echo json_encode(false);
        } else {
            $this->db->delete('srp_erp_srm_attachments', array('attachmentID' => trim($attachmentID)));
            echo json_encode(true);
        }
    }

    function attachement_upload()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        $this->form_validation->set_rules('documentAutoID', 'Document Auto ID', 'trim|required');

        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_srm_attachments')->result_array();
            $file_name = $this->input->post('document_name') . '_' . $this->input->post('documentID') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments/SRM');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if (!$this->upload->do_upload("document_file")) {
                echo json_encode(array('status' => 0, 'type' => 'w', 'message' => 'Upload failed ' . $this->upload->display_errors()));
            } else {
                $upload_data = $this->upload->data();
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentAutoID'] = trim($this->input->post('documentAutoID') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                $data['myFileName'] = $file_name . $upload_data["file_ext"];
                $data['fileType'] = trim($upload_data["file_ext"]);
                $data['fileSize'] = trim($upload_data["file_size"]);
                $data['timestamp'] = date('Y-m-d H:i:s');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_srm_attachments', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message()));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.'));
                }
            }
        }
    }

    function assignItem_supplier_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $itemAutoID = $this->input->post('itemAutoID');
        $text = trim($this->input->post('Search') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((supplierSystemCode Like '%" . $text . "%') OR (supplierName Like '%" . $text . "%'))";
        }

        $data['supplier'] = $this->db->query("SELECT * FROM srp_erp_srm_suppliermaster where companyID = {$companyID}  AND supplierAutoID NOT IN (SELECT supplierAutoID FROM srp_erp_srm_supplieritems WHERE itemAutoID = {$itemAutoID} AND companyID = {$companyID}) $search_string")->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_suppliers_forAssign_item', $data);
    }

    function assignItem_supplier_template_view()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $inquiryID = $this->input->post('inquiryID');
        $text = trim($this->input->post('Search') ?? '');
        $search_string = '';
        if (isset($text) && !empty($text)) 
        { 
            $search_string = "AND ((srp_erp_srm_suppliermaster.supplierSystemCode Like '%" . $text . "%') OR (srp_erp_srm_suppliermaster.supplierName Like '%" . $text . "%'))";
        }

        $this->db->select('itemAutoID');
        $this->db->from('srp_erp_srm_inquiryitem');
        $this->db->where('inquiryMasterID',$inquiryID);
        $this->db->group_by('srp_erp_srm_inquiryitem.itemAutoID');
        $itemIDs = $this->db->get()->result_array();
       // print_r($search_string);exit;
        $itemIDArray=[0];

        foreach($itemIDs as $val){
            $itemIDArray[]=$val['itemAutoID'];
        }
      
       // $data['supplier'] = $this->db->query("SELECT * FROM srp_erp_srm_suppliermaster where companyID = {$companyID}  AND supplierAutoID NOT IN (SELECT supplierAutoID FROM srp_erp_srm_supplieritems WHERE itemAutoID IN (" . join(',', $itemIDArray) . ") AND companyID = {$companyID}) $search_string")->result_array();
        //LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
        $data['supplier'] = $this->db->query("SELECT srp_erp_srm_suppliermaster.* FROM srp_erp_srm_suppliermaster LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_srm_suppliermaster.erpSupplierAutoID where srp_erp_srm_suppliermaster.companyID = {$companyID} AND srp_erp_suppliermaster.masterApprovedYN=1  AND srp_erp_srm_suppliermaster.supplierAutoID NOT IN (SELECT supplierAutoID FROM srp_erp_srm_supplieritems WHERE itemAutoID IN (" . join(',', $itemIDArray) . ") AND companyID = {$companyID}) $search_string")->result_array();
        $this->load->view('system/srm/customer-order/ajax/load_suppliers_forAssign_supplier_view', $data);
    }

    function load_OrderInquiry_editView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $this->db->select('srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.documentCode,DATE_FORMAT(srp_erp_srm_orderinquirymaster.documentDate,\'' . $convertFormat . '\') AS documentDate,srp_erp_srm_customermaster.CustomerName,CurrencyCode,narration,srp_erp_srm_orderinquirymaster.confirmedYN as confirmStatus');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_orderinquirymaster.customerID', 'LEFT');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        //$this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_task.completedBy', 'LEFT');
        //$this->db->join('srp_erp_crm_project', 'srp_erp_crm_project.projectID = srp_erp_crm_task.projectID', 'LEFT');
        $this->db->where('inquiryID', $inquiryID);
        $data['header'] = $this->db->get()->row_array();

        $where_rfq = "srp_erp_srm_orderinquirydetails.companyID = '{$companyID}' AND srp_erp_srm_orderinquirydetails.inquiryMasterID = '{$inquiryID}' AND isRfqCreated = 1";

        $this->db->select('*,supplierSystemCode,supplierName');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID', 'LEFT');
        $this->db->where($where_rfq);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['detailrfq'] = $this->db->get()->result_array();


        $this->load->view('system/srm/customer-order/ajax/load_order_inquiry_edit_view', $data);
    }

    function load_order_master_editView()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');
        $this->db->select('srp_erp_srm_customerordermaster.customerOrderID,srp_erp_srm_customerordermaster.customerOrderCode,DATE_FORMAT(srp_erp_srm_customerordermaster.documentDate,\'' . $convertFormat . '\') AS documentDate,DATE_FORMAT(srp_erp_srm_customerordermaster.expiryDate,\'' . $convertFormat . '\') AS expiryDate,srp_erp_srm_customermaster.CustomerName,CurrencyCode,narration,srp_erp_srm_customerordermaster.status as orderStatus,contactPersonNumber,CustomerAddress,referenceNumber,srp_erp_srm_status.description as statusDescription,backgroundColor,fontColor,
        srp_erp_customermaster.customerName as CustomerName,srp_erp_srm_customerordermaster.bidStartDate,srp_erp_srm_customerordermaster.bidEndDate,srp_erp_srm_customerordermaster.isBackToBack,srp_erp_suppliermaster.supplierName');
        $this->db->from('srp_erp_srm_customerordermaster');
        $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_srm_customerordermaster.supplierID', 'LEFT');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_customerordermaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->join('srp_erp_srm_status', 'srp_erp_srm_customerordermaster.status = srp_erp_srm_status.statusID', 'LEFT');
        $this->db->where('customerOrderID', $customerOrderID);
        $data['header'] = $this->db->get()->row_array();


        $where_rfq = "srp_erp_srm_orderinquirydetails.companyID = '{$companyID}' AND srp_erp_srm_orderinquirydetails.customerOrderID = '{$customerOrderID}' AND isRfqCreated = 1";

        $this->db->select('*,supplierSystemCode,supplierName');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_orderinquirydetails.supplierID = srp_erp_srm_suppliermaster.supplierAutoID', 'LEFT');
        $this->db->where($where_rfq);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['detailrfq'] = $this->db->get()->result_array();

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode');
        $this->db->from('srp_erp_srm_customerorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_customerorderdetails.itemAutoID', 'LEFT');
        $this->db->where('srp_erp_srm_customerorderdetails.companyID', $companyID);
        $this->db->where('srp_erp_srm_customerorderdetails.customerOrderID', $customerOrderID);
        $data['orderitem'] = $this->db->get()->result_array();

        $this->load->view('system/srm/customer-order/ajax/load_order_master_edit_view', $data);
    }

    function load_customer_order_all_notes()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');

        $where = "companyID = " . $companyid . " AND documentID = 3 AND documentAutoID = " . $customerOrderID . "";
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->from('srp_erp_srm_notes');
        $this->db->where($where);
        $this->db->order_by('notesID', 'desc');
        $data['notes'] = $this->db->get()->result_array();
        $this->load->view('system/srm/customer-order/ajax/load_customer_order_notes', $data);
    }

    function add_customer_order_notes()
    {
        $this->form_validation->set_rules('customerOrderID', 'Order ID', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->add_customer_order_notes());
        }
    }

    function fetch_vendor_attachments()
    {
        $primaryLanguage = getPrimaryLanguage();
        $this->lang->load('common', $primaryLanguage);
        $documentSubID =$this->input->post('inquiryMasterID').'_'.$this->common_data['company_data']['company_id'].'_'.$this->input->post('supplierID');
        $this->db->where('documentSubID',  $documentSubID);
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

    function send_rfq_email_suppliers()
    {
        $this->form_validation->set_rules('inquiryMasterID', 'Inquiry ID', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->send_rfq_email_suppliers());
        }
    }

    function get_rfq_supplier_link()
    {
        $this->form_validation->set_rules('inquiryMasterID', 'Inquiry ID', 'trim|required');
        $this->form_validation->set_rules('supplierID', 'Supplier ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->get_rfq_supplier_link());
        }
    }

    function order_review_detail_view()
    {
        $convertFormat = convert_date_format_sql();
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $orderreviewID = trim($this->input->post('orderreviewID') ?? '');
       // $template = trim($this->input->post('template') ?? '');

        if(!empty($orderreviewID)){
            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID !=', $orderreviewID);
            $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();


            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID ', $orderreviewID);
            $reviewdetailsup=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();

            $pulleditem=array();
            $suparr=array();
            if(!empty($reviewdetail)){
                foreach ($reviewdetail as $vl){
                    if(!empty($vl['itemAutoID'])){
                        array_push($pulleditem,$vl['itemAutoID']);
                    }
                }
            }

            if(!empty($reviewdetailsup)){
                foreach ($reviewdetailsup as $vlsup){
                    if(!empty($vlsup['itemAutoID'])){
                        array_push($suparr,$vlsup['supplierID'].'_'.$vlsup['itemAutoID']);
                    }
                }
            }
            $itmwhereIN ="";
            if(!empty($pulleditem)){
                $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
            }

        }else{
            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();

            $pulleditem=array();
            $suparr=array();
            if(!empty($reviewdetail)){
                foreach ($reviewdetail as $vl){
                    if(!empty($vl['itemAutoID'])){
                        array_push($pulleditem,$vl['itemAutoID']);
                        array_push($suparr,$vl['supplierID']);
                    }
                }
            }
            $itmwhereIN ="";
            if(!empty($pulleditem)){
                $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
            }
        }

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        // if(!empty($itmwhereIN)){
        //     $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
        // }
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
        $data['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        //for supplier view
        $this->db->select('srp_erp_srm_orderinquirymaster.*,srp_erp_currencymaster.CurrencyCode');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->where('srp_erp_srm_orderinquirymaster.inquiryID', $inquiryID);
        $data['inquiry_master'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_srm_orderinquirydetails.*,srp_erp_srm_suppliermaster.supplierName');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['supplier_Data'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.companyReportingSellingPrice,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        if(!empty($itmwhereIN)){
            $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
        }
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
        $data['item_supplier'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();


        $amountArr =[];
        $suplierArr =[];

        foreach($data['supplier_Data'] as $val) {

            $total_amount = $this->db->query("SELECT supplierQty,supplierPrice,supplierTax,supplierOtherCharge,supplierDiscount from srp_erp_srm_orderinquirydetails  where srp_erp_srm_orderinquirydetails.supplierID = " . $val['supplierID'] . " AND srp_erp_srm_orderinquirydetails.isSupplierSubmited =  1 AND srp_erp_srm_orderinquirydetails.inquiryMasterID =".$val['inquiryMasterID']."")->result_array();
            
            $lastAmount=0;
            $sum=0;
            $tax=0;
            $supplierDiscount=0;

            foreach($total_amount as $tot){
                $sum =$sum+ ($tot['supplierPrice']*$tot['supplierQty']);
                $tax =$tax+ ($tot['supplierTax']+$tot['supplierOtherCharge']);
                $supplierDiscount =$supplierDiscount+ $tot['supplierDiscount'];

            }  

            $lastAmount=($sum+$tax)-$supplierDiscount;

            $amountArr[]=$lastAmount;
            $suplierArr[]=$val['supplierID'];
        }

        $minkey =array_keys($amountArr, min($amountArr))[0];
        $minsupplierID =$suplierArr[$minkey];

        foreach($data['supplier_Data'] as $key=>$val) {
            if($val['supplierID']==$minsupplierID){
                $data['supplier_Data'][$key]['isMin']=1;
            }else{
                $data['supplier_Data'][$key]['isMin']=0;
            }
        }
        
        ///end supplier view

        // $this->db->select('*');
        // $this->db->where('inquiryMasterID', $inquiryID);
        // $this->db->where('isSupplierSubmited', 1);
        
        //$data['template']=$template;
        foreach($data['item'] as $key => $val){
            $data['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['item'][$key]['itemImage'], '1 hour');
        }
        $data['supplierIDarr'] = $suparr;

        $this->load->view('system/srm/customer-order/ajax/load_order_review_detail', $data);
    }

    function order_review_detail_view_approval()
    {
        $convertFormat = convert_date_format_sql();
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $orderreviewID = trim($this->input->post('orderreviewID') ?? '');
       // $template = trim($this->input->post('template') ?? '');

        if(!empty($orderreviewID)){
            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID !=', $orderreviewID);
            $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();


            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID ', $orderreviewID);
            $reviewdetailsup=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();

            $pulleditem=array();
            $suparr=array();
            if(!empty($reviewdetail)){
                foreach ($reviewdetail as $vl){
                    if(!empty($vl['itemAutoID'])){
                        array_push($pulleditem,$vl['itemAutoID']);
                    }
                }
            }

            if(!empty($reviewdetailsup)){
                foreach ($reviewdetailsup as $vlsup){
                    if(!empty($vlsup['itemAutoID'])){
                        array_push($suparr,$vlsup['supplierID'].'_'.$vlsup['itemAutoID']);
                    }
                }
            }
            $itmwhereIN ="";
            if(!empty($pulleditem)){
                $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
            }

        }else{
            $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
            $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
            $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
            $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();

            $pulleditem=array();
            $suparr=array();
            if(!empty($reviewdetail)){
                foreach ($reviewdetail as $vl){
                    if(!empty($vl['itemAutoID'])){
                        array_push($pulleditem,$vl['itemAutoID']);
                        array_push($suparr,$vl['supplierID']);
                    }
                }
            }
            $itmwhereIN ="";
            if(!empty($pulleditem)){
                $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
            }
        }

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        // if(!empty($itmwhereIN)){
        //     $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
        // }
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
        $data['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        //for supplier view
        $this->db->select('srp_erp_srm_orderinquirymaster.*,srp_erp_currencymaster.CurrencyCode');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->where('srp_erp_srm_orderinquirymaster.inquiryID', $inquiryID);
        $data['inquiry_master'] = $this->db->get()->row_array();
        $this->db->select('srp_erp_srm_orderinquirydetails.*,srp_erp_srm_suppliermaster.supplierName');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
        $data['supplier_Data'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.companyReportingSellingPrice,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        if(!empty($itmwhereIN)){
            $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
        }
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
        $data['item_supplier'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        ///end supplier view

        // $this->db->select('*');
        // $this->db->where('inquiryMasterID', $inquiryID);
        // $this->db->where('isSupplierSubmited', 1);
        
        //$data['template']=$template;
        foreach($data['item'] as $key => $val){
            $data['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['item'][$key]['itemImage'], '1 hour');
        }
        $data['supplierIDarr'] = $suparr;

        $this->load->view('system/srm/customer-order/ajax/load_order_review_detail_approval', $data);
    }



    function generate_order_review_supplier()
    {
        $this->form_validation->set_rules('inquiryID', 'InquiryID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->generate_order_review_supplier());
        }
    }

        //-- Self Service Order Review changes ---//
    
    
        function load_order_view()
        {
            $currentUserID = current_userID();
            $convertFormat = convert_date_format_sql();
            $inquiryID = trim($this->input->post('inquiryID') ?? '');
            $orderreviewID = trim($this->input->post('orderreviewID') ?? '');
           // $template = trim($this->input->post('template') ?? '');
    
            if(!empty($orderreviewID)){
                $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
                $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
                $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
                $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID !=', $orderreviewID);
                $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();
    
    
                $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
                $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
                $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
                $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID ', $orderreviewID);
                $reviewdetailsup=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();
    
                $pulleditem=array();
                $suparr=array();
                if(!empty($reviewdetail)){
                    foreach ($reviewdetail as $vl){
                        if(!empty($vl['itemAutoID'])){
                            array_push($pulleditem,$vl['itemAutoID']);
                        }
                    }
                }
    
                if(!empty($reviewdetailsup)){
                    foreach ($reviewdetailsup as $vlsup){
                        if(!empty($vlsup['itemAutoID'])){
                            array_push($suparr,$vlsup['supplierID'].'_'.$vlsup['itemAutoID']);
                        }
                    }
                }
                $itmwhereIN ="";
                if(!empty($pulleditem)){
                    $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
                }
    
    
            }else{
                $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
                $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
                $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
                $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();
    
                $pulleditem=array();
                $suparr=array();
                if(!empty($reviewdetail)){
                    foreach ($reviewdetail as $vl){
                        if(!empty($vl['itemAutoID'])){
                            array_push($pulleditem,$vl['itemAutoID']);
                            array_push($suparr,$vl['supplierID']);
                        }
                    }
                }
                $itmwhereIN ="";
                if(!empty($pulleditem)){
                    $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
                }
            }
    
            $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
            $this->db->where('inquiryMasterID', $inquiryID);
            $this->db->where('isSupplierSubmited', 1);
            if(!empty($itmwhereIN)){
                $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
            }
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
            $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
            $data['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();
    
            //for supplier view
    
            $this->db->select('srp_erp_srm_orderinquirymaster.*,srp_erp_currencymaster.CurrencyCode');
            $this->db->from('srp_erp_srm_orderinquirymaster');
            $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
            $this->db->where('srp_erp_srm_orderinquirymaster.inquiryID', $inquiryID);
            $data['inquiry_master'] = $this->db->get()->row_array();
            
            $this->db->select('srp_erp_srm_orderinquirydetails.*,srp_erp_srm_suppliermaster.supplierName');
            $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
            $this->db->where('inquiryMasterID', $inquiryID);
            $this->db->where('isSupplierSubmited', 1);
            $this->db->group_by('srp_erp_srm_orderinquirydetails.supplierID');
            $data['supplier_Data'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();
    
            $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.companyReportingSellingPrice,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
            $this->db->where('inquiryMasterID', $inquiryID);
            $this->db->where('isSupplierSubmited', 1);
            if(!empty($itmwhereIN)){
                $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
            }
            $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
            $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
            $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
            $data['item_supplier'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();
    
            ///end supplier view
            $this->db->select('e.empID, e.activityMasterID, e.activityDetailID, o.inquiryMasterID, (CASE WHEN e.empID = ' . $currentUserID . ' THEN 1 ELSE 0 END) AS isCurrentUser');
            $this->db->from('srp_erp_incharge_assign e');
            $this->db->join('srp_erp_srm_orderinquirydetails o', 'e.activityMasterID = o.customerOrderID', 'LEFT');
            $this->db->where('o.inquiryMasterID', $inquiryID);
            $this->db->where('e.empID', $currentUserID);
            $data['self_filter'] = $this->db->get()->result_array();
            
            
            
           




            // $this->db->select('*');
            // $this->db->where('inquiryMasterID', $inquiryID);
            // $this->db->where('isSupplierSubmited', 1);
            
            //$data['template']=$template;
            foreach($data['item'] as $key => $val){
                $data['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['item'][$key]['itemImage'], '1 hour');
            }
            $data['supplierIDarr'] = $suparr;
    
            $this->load->view('system/srm/customer-order/ajax/selfservice_load_order_view', $data);
        }
    
    



        //--end -- // 

    function save_request_refer()
    {
        $referType = $this->input->post('referType');
        $comment = $this->input->post('comment');
        
        $this->form_validation->set_rules("referType", 'referType', 'trim|required');
        $this->form_validation->set_rules("comment", 'comment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->save_request_refer());
        }
    }

    function save_request_refer_supplier_template()
    {
        $referType = $this->input->post('referTypeSupplier');
        $comment = $this->input->post('commentSupplier');
        
        $this->form_validation->set_rules("referTypeSupplier", 'referType', 'trim|required');
        $this->form_validation->set_rules("commentSupplier", 'comment', 'trim|required');

        $this->form_validation->set_rules("itemAutoID", 'Item', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->save_request_refer_supplier_template());
        }
    }

    function save_company_request_basic_info()
    {
        $seconeryItemCode = $this->input->post('seconeryItemCode');
        
        $this->form_validation->set_rules("seconeryItemCode", 'Secondary Code', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->save_company_request_basic_info());
        }
    }

    function company_request_confirm_info()
    {
        $existSupplierVal = $this->input->post('existSupplierVal');
        $addSupplierVal = $this->input->post('addSupplierVal');
        $vatEligible = $this->input->post('vatEligible');

        if(!$existSupplierVal && !$addSupplierVal){
            $this->form_validation->set_rules("existSupplierVal", 'Supplier', 'trim|required');
        }

        if($existSupplierVal==1){
            $this->form_validation->set_rules("sup", 'Supplier', 'trim|required');
            $this->form_validation->set_rules("system_comment", 'Comment', 'trim|required');
        }

        if($existSupplierVal==2 && $addSupplierVal==2){
            $this->form_validation->set_rules("sup", 'Supplier', 'trim|required');
        }
        if($addSupplierVal==1){
           // $nameOnCheque = $this->input->post('nameOnCheque');
            $this->form_validation->set_rules("seconeryItemCode", 'Secondary Code', 'trim|required');
            $this->form_validation->set_rules("nameOnCheque", 'Name On Cheque', 'trim|required');
            $this->form_validation->set_rules("partyCategoryID", 'Category', 'trim|required');
            $this->form_validation->set_rules("liabilityAccount", 'Liability Account', 'trim|required');
            $this->form_validation->set_rules("supplierCurrency", 'Supplier Currency', 'trim|required');
            $this->form_validation->set_rules("suppliertaxgroup", 'Supplier taxgroup', 'trim|required');
            $this->form_validation->set_rules("supplierCreditPeriod", 'Supplier CreditPeriod', 'trim|required');
            $this->form_validation->set_rules("supplierCreditLimit", 'Supplier CreditLimit', 'trim|required');
            $this->form_validation->set_rules("vatEligible", 'VatEligible', 'trim|required');
            $this->form_validation->set_rules("add_comment", 'Comment', 'trim|required');
            if($vatEligible==2){
                $this->form_validation->set_rules("vatPercentage", 'vat Percentage', 'trim|required');
            }
            
        }

       // print_r( $addSupplierVal);exit;
        
        $this->form_validation->set_rules("requestMasterID", 'select request', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->company_request_confirm_info());
        }
    }

    function save_company_request_financial_info()
    {

        $nameOnCheque = $this->input->post('nameOnCheque');
        
        $this->form_validation->set_rules("nameOnCheque", 'name On Cheque', 'trim|required');
        $this->form_validation->set_rules("partyCategoryID", 'Category', 'trim|required');
        $this->form_validation->set_rules("liabilityAccount", 'liability Account', 'trim|required');
        $this->form_validation->set_rules("supplierCurrency", 'supplier Currency', 'trim|required');
        $this->form_validation->set_rules("suppliertaxgroup", 'supplier taxgroup', 'trim|required');
        $this->form_validation->set_rules("supplierCreditPeriod", 'supplier CreditPeriod', 'trim|required');
        $this->form_validation->set_rules("supplierCreditLimit", 'supplier CreditLimit', 'trim|required');
        $this->form_validation->set_rules("vatEligible", 'vatEligible', 'trim|required');
      //  $this->form_validation->set_rules("vatIdNo", 'vat Id No', 'trim|required');
        $this->form_validation->set_rules("vatPercentage", 'vat Percentage', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->save_company_request_financial_info());
        }
        
        

    }

    function load_change_request_data(){
        $this->datatables->select('reqResID,referType,comment,createdDatetime,receivedDate,vendorComment,isVendorSubmited,unitPriceApproveYN,qtyApproveYN,dateApproveYN')
            ->where('inquiryDetailID', $this->input->post('inquiryDetailID'))
            ->where('inquiryMasterID', $this->input->post('inquiryMasterID'))
            ->where('companyID', $this->input->post('companyID'))
            ->where('supplierID', $this->input->post('supplierID'))
            ->from('srp_erp_srm_vendor_request_responces');
            
            //->edit_column('action', '<span class="pull-right" ><a onclick="editItemPartNumber($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="deleteItemPartNumber($1)"><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"  rel="tooltip"></span></a></span>', 'inquiryDetailID');
            //->edit_column('action', '<span class="pull-right" ><a onclick="editItemPartNumber($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>', 'inquiryDetailID');
           // $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
            $this->datatables->add_column('send', ' $1 ', 'srm_rfq_vendor_new_update_aprove(reqResID,isVendorSubmited,referType,unitPriceApproveYN,qtyApproveYN,dateApproveYN)');
            echo $this->datatables->generate();
    }

    function load_change_request_data_supplier_view(){
        $companyID = $this->common_data['company_data']['company_id'];

        $this->datatables->select('srp_erp_srm_vendor_request_responces.reqResID as reqResID,srp_erp_srm_vendor_request_responces.referType as referType,srp_erp_srm_vendor_request_responces.comment as comment,srp_erp_srm_vendor_request_responces.createdDatetime as createdDatetime,srp_erp_srm_vendor_request_responces.receivedDate as receivedDate,srp_erp_srm_vendor_request_responces.vendorComment as vendorComment,srp_erp_itemmaster.itemName as itemName,srp_erp_srm_vendor_request_responces.isVendorSubmited as isVendorSubmited,srp_erp_srm_vendor_request_responces.unitPriceApproveYN as unitPriceApproveYN,srp_erp_srm_vendor_request_responces.qtyApproveYN as qtyApproveYN,srp_erp_srm_vendor_request_responces.dateApproveYN as dateApproveYN');
        $this->datatables->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_vendor_request_responces.itemAutoID', 'LEFT')
            ->where('srp_erp_srm_vendor_request_responces.inquiryMasterID', $this->input->post('inquiryMasterID'))
            ->where('srp_erp_srm_vendor_request_responces.companyID', $companyID)
            ->where('srp_erp_srm_vendor_request_responces.supplierID', $this->input->post('supplierID'))
            ->from('srp_erp_srm_vendor_request_responces');
            
            //->edit_column('action', '<span class="pull-right" ><a onclick="editItemPartNumber($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="deleteItemPartNumber($1)"><span title="Delete" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"  rel="tooltip"></span></a></span>', 'inquiryDetailID');
            //->edit_column('action', '<span class="pull-right" ><a onclick="editItemPartNumber($1)"><span title="Edit" class="glyphicon glyphicon-pencil" style="color:blue;"  rel="tooltip"></span></a>', 'inquiryDetailID');
           // $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
            $this->datatables->add_column('send', ' $1 ', 'srm_rfq_vendor_new_update_aprove(reqResID,isVendorSubmited,referType,unitPriceApproveYN,qtyApproveYN,dateApproveYN)');
            echo $this->datatables->generate();
    }

    function supplier_image_upload()
    {
        $this->form_validation->set_rules('supplierAutoID', 'Supplier ID is missing', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->supplier_image_upload());
        }
    }

    function update_vendor_change_details()
    {

        echo json_encode($this->Srm_master_model->update_vendor_change_details());
    }

    
    function fetch_item_supplier_view()
    {
        echo json_encode($this->Srm_master_model->fetch_item_supplier_view());
    }

    function load_vendor_change_details(){
        echo json_encode($this->Srm_master_model->load_vendor_change_details());
    }

    function fetch_confirm_company_request_info(){
        echo json_encode($this->Srm_master_model->fetch_confirm_company_request_info());
    }


    function load_purchase_requestID()
    {
        $data_arr = array();
        $companyID = $this->common_data['company_data']['company_id'];

        $prqID = $this->db->query("SELECT purchaseRequestID,purchaseRequestCode,documentDate,narration FROM srp_erp_purchaserequestmaster WHERE approvedYN=1 AND companyID = $companyID and transactionCurrencyID = " . $this->input->post('currency') . "")->result_array();

        if (isset($prqID)) {
            foreach ($prqID as $row) {
                $data_arr[trim($row['purchaseRequestID'] ?? '')] = trim($row['purchaseRequestCode'] ?? '') .' | '. $row['documentDate'].' | '. $row['narration'];
            }
        }
        echo form_dropdown('purchaseRequestID', $data_arr, '', 'class="form-control select2" onchange="load_prq_view()" id="purchaseRequestID" ');
    }


    function getOrderReviewManagement_tableView()
    {
        $companyid = $this->common_data['company_data']['company_id'];


        $convertFormat = convert_date_format_sql();


        $where = "srp_erp_srm_orderreviewmaster.companyID = " . $companyid ;
        $this->db->select("srp_erp_srm_orderreviewmaster.*,srp_erp_srm_orderinquirymaster.documentCode");
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderreviewmaster.inquiryID = srp_erp_srm_orderinquirymaster.inquiryID', 'LEFT');
        $this->db->where($where);
        $this->db->order_by('orderreviewID', 'DESC');
        $data['output'] = $this->db->get()->result_array();

        $this->load->view('system/srm/order_review/ajax/load_customer_order_review_management', $data);
    }

    function create_order_review_header(){
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $narration = $this->input->post('narration');
        $referanceNumber = $this->input->post('referanceNumber');
        $customerName = $this->input->post('customerName');
        $orderReview=$this->Srm_master_model->load_inquiry_reviewHeader();

        $this->db->select("*");
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $this->db->where('inquiryID',$inquiryID);
        $ex_review = $this->db->get()->result_array();

        if(!empty($ex_review)){
            foreach($ex_review as $val){
                $this->Srm_master_model->remove_customer_order_review($val['orderreviewID']);
            }
        }

        if(!empty($orderReview) && !empty($inquiryID)){
            $data['inquiryID'] = $inquiryID;
            $data['customerName'] = $customerName;
            $data['documentID'] = 'ORD-RVW';
            $data['narration'] = $narration;
            $data['referenceNo'] = $referanceNumber;
            $data['companyID'] = current_companyID();
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $result=$this->db->insert('srp_erp_srm_orderreviewmaster', $data);
            $insert_id = $this->db->insert_id();
            if($result){
                echo json_encode(array('s', 'Order Review Saved Successfully',$insert_id));
            }else{
                echo json_encode(array('e', 'Order Review failed to save'));
            }
        }else{
            echo json_encode(array('w', 'Suppliers Not Submitted'));
        }
    }

    function delete_customer_order_review()
    {
        echo json_encode($this->Srm_master_model->delete_customer_order_review());
    }

    function reject_company_request()
    {
        echo json_encode($this->Srm_master_model->reject_company_request());
    }

    function vendor_company_request_document_approve()
    {
        echo json_encode($this->Srm_master_model->vendor_company_request_document_approve());
    }

    function vendor_company_request_document_reject()
    {
        $typeReq = trim($this->input->post('typeReq') ?? '');
       // print_r($typeReq);exit;
        if($typeReq==2){
            $this->form_validation->set_rules('comments', 'Comment', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                echo json_encode($this->Srm_master_model->vendor_company_request_document_reject());
            }
        }else{
            echo json_encode($this->Srm_master_model->vendor_company_request_document_reject());
        }

       
       
    }

    function vendor_company_request_document_reject_approve()
    {
        $typeReq = trim($this->input->post('typeReq_approve') ?? '');
       
        $this->form_validation->set_rules('comments_approve', 'Comment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->vendor_company_request_document_reject_approve());
        }
    }

    function vendor_company_request_reject()
    {

        $this->form_validation->set_rules('comments_rej', 'Comment', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->vendor_company_request_reject());
        }
       
    }

    

    function insert_review_detail(){
        echo json_encode($this->Srm_master_model->insert_review_detail());
    }

    function insert_review_detail_supplier_base(){
        echo json_encode($this->Srm_master_model->insert_review_detail_supplier_base());
    }

    function confirm_order_review(){
        echo json_encode($this->Srm_master_model->confirm_order_review());
    }


    function fetch_order_review_approval()
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
            $this->datatables->select('srp_erp_srm_orderreviewmaster.documentID as document,srp_erp_srm_orderreviewmaster.orderreviewID as orderreviewID,srp_erp_srm_orderreviewmaster.documentSystemCode as contractCode,srp_erp_srm_orderreviewmaster.narration as narration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_srm_orderreviewmaster.createdDateTime,\'' . $convertFormat . '\') AS contractDate ,srp_erp_srm_orderreviewmaster.customerName as customerName,srp_erp_srm_orderreviewmaster.referenceNo as referenceNo');

            $this->datatables->from('srp_erp_srm_orderreviewmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_srm_orderreviewmaster.orderreviewID AND srp_erp_documentapproved.approvalLevelID = srp_erp_srm_orderreviewmaster.currentLevelNo');
            $this->datatables->join('srp_erp_approvalusers', 'srp_erp_approvalusers.levelNo = srp_erp_srm_orderreviewmaster.currentLevelNo');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('ORD-RVW'));
            $this->datatables->where_in('srp_erp_approvalusers.documentID', array('ORD-RVW'));
            $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
            $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
            $this->datatables->where('srp_erp_srm_orderreviewmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_approvalusers.companyID', $companyID);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,orderreviewID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,orderreviewID)');
            $this->datatables->add_column('edit', '$1', 'orerew_action_approval(orderreviewID,approvalLevelID,approvedYN,documentApprovedID,document,0)');


            echo $this->datatables->generate();
        }else
        {
            $this->datatables->select('srp_erp_srm_orderreviewmaster.documentID as document,srp_erp_srm_orderreviewmaster.orderreviewID as orderreviewID,srp_erp_srm_orderreviewmaster.documentSystemCode as contractCode,srp_erp_srm_orderreviewmaster.narration as narration,confirmedYN,srp_erp_documentapproved.approvedYN as approvedYN,documentApprovedID,approvalLevelID,DATE_FORMAT(srp_erp_srm_orderreviewmaster.createdDateTime,\'' . $convertFormat . '\') AS contractDate ,srp_erp_srm_orderreviewmaster.customerName as customerName,srp_erp_srm_orderreviewmaster.referenceNo as referenceNo');

            $this->datatables->from('srp_erp_srm_orderreviewmaster');
            $this->datatables->join('srp_erp_documentapproved', 'srp_erp_documentapproved.documentSystemCode = srp_erp_srm_orderreviewmaster.orderreviewID');
            $this->datatables->where_in('srp_erp_documentapproved.documentID', array('ORD-RVW'));
            $this->datatables->where('srp_erp_srm_orderreviewmaster.companyID', $companyID);
            $this->datatables->where('srp_erp_documentapproved.approvedEmpID', $currentuserid);
            $this->datatables->group_by('srp_erp_documentapproved.documentSystemCode');
            $this->datatables->group_by('srp_erp_documentapproved.approvalLevelID');
            $this->datatables->add_column('contractCode', '$1', 'approval_change_modal(contractCode,orderreviewID,documentApprovedID,approvalLevelID,approvedYN,document)');
            $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
            $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN,document,orderreviewID)');
            $this->datatables->add_column('edit', '$1', 'orerew_action_approval(orderreviewID,approvalLevelID,approvedYN,documentApprovedID,document,0)');

            echo $this->datatables->generate();
        }

    }


    /*function load_ordereview_conformation()
    {
        $orderreviewID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('orderreviewID') ?? '');
        $data['extra'] = $this->Srm_master_model->fetch_ordrew_template_data($orderreviewID);
        $data['approval'] = $this->input->post('approval');

        $this->db->select('documentID,approvedYN');
        $this->db->where('orderreviewID', trim($orderreviewID));
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_srm_orderreviewmaster');
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


        $data['logo']=mPDFImage;
        if($this->input->post('html')){
            $data['logo']=htmlImage;
        }

        $html = $this->load->view('system/srm/order_review_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $pdfp = $this->load->view('system/srm/order_review_html_view', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($pdfp, 'A4',$data['extra']['master']['approvedYN']);
        }
    }*/



    function load_ordereview_conformation()
    {
        $orderreviewID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('orderreviewID') ?? '');

        $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderinquirymaster.inquiryType');
        $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'left');
        $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID', $orderreviewID);
        $reviewmastr=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();

        if($reviewmastr['inquiryType']=='PRQ'){
            $this->db->select('srp_erp_srm_orderreviewmaster.*,srp_erp_srm_orderinquirymaster.documentCode as inquirycode,srp_erp_srm_orderinquirymaster.inquiryType,srp_erp_segment.segmentCode,srp_erp_segment.description as segdescription');
            $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'left');
            $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_srm_orderinquirymaster.purchaseRequestID', 'LEFT');
            $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_purchaserequestmaster.segmentID', 'LEFT');
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID', $orderreviewID);
            $data['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
        }else{
            $this->db->select('srp_erp_srm_orderreviewmaster.*,srp_erp_srm_orderinquirymaster.documentCode as inquirycode');
            $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'left');
            $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID', $orderreviewID);
            $data['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
        }



        $inquiryID = $data['reviewmaster']['inquiryID'];

        $data['inquiryMasterID'] = $inquiryID;
        $data['reviewMasterID'] = $orderreviewID;


        $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
        $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
        $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
        $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID !=', $orderreviewID);
        $reviewdetail=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();


        $this->db->select('srp_erp_srm_orderreviewmaster.inquiryID,srp_erp_srm_orderreviewdetails.itemAutoID,srp_erp_srm_orderreviewdetails.supplierID as supplierID');
        $this->db->join('srp_erp_srm_orderreviewdetails', 'srp_erp_srm_orderreviewmaster.orderreviewID = srp_erp_srm_orderreviewdetails.orderreviewID', 'INNER');
        $this->db->where('srp_erp_srm_orderreviewmaster.inquiryID', $inquiryID);
        $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID ', $orderreviewID);
        $reviewdetailsup=$this->db->get('srp_erp_srm_orderreviewmaster')->result_array();

        $pulleditem=array();
        $suparr=array();
        $suparrid=array();
        if(!empty($reviewdetail)){
            foreach ($reviewdetail as $vl){
                if(!empty($vl['itemAutoID'])){
                    array_push($pulleditem,$vl['itemAutoID']);
                }
            }
        }

        if(!empty($reviewdetailsup)){
            foreach ($reviewdetailsup as $vlsup){
                if(!empty($vlsup['itemAutoID'])){
                    array_push($suparr,$vlsup['supplierID'].'_'.$vlsup['itemAutoID']);
                }
            }
        }
        $itmwhereIN ="";
        if(!empty($pulleditem)){
            $itmwhereIN = " " . join(" , ", $pulleditem) . " ";
        }

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isSupplierSubmited', 1);
        if(!empty($itmwhereIN)){
            $this->db->where_not_in('srp_erp_srm_orderinquirydetails.itemAutoID', $itmwhereIN);
        }
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');
        $data['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        foreach($data['item'] as $key => $val){
            $data['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$data['item'][$key]['itemImage'], '1 hour');
        }
        $data['supplierIDarr'] = $suparr;
        $data['logo']=mPDFImage;
        $data['typepdf']='pdf';
        if($this->input->post('html')){
            $data['typepdf']='html';
            $data['logo']=htmlImage;
        }



        $html = $this->load->view('system/srm/order_review_html_view', $data, true);

        if ($this->input->post('html')) {
            echo $html;
        } else {
            $pdfp = $this->load->view('system/srm/order_review_html_view', $data, true);
            $this->load->library('pdf');
            $pdf = $this->pdf->printed($pdfp, 'A4-L',1);
        }
    }



    function save_order_review_approval()
    {
        $system_code = trim($this->input->post('orderreviewID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = trim($this->input->post('code') ?? '');

        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('orderreviewID');
                $this->db->where('orderreviewID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_srm_orderreviewmaster');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    if ($this->input->post('status') == 2) {
                        $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
                    }else{
                        $this->form_validation->set_rules('shippingAddressID', 'Ship To', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Srm_master_model->save_order_review_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('orderreviewID');
            $this->db->where('orderreviewID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_srm_orderreviewmaster');
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
                    }else{
                        $this->form_validation->set_rules('shippingAddressID', 'Ship To', 'trim|required');
                    }
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Srm_master_model->save_order_review_approval());
                    }
                }
            }
        }
    }


    function fetch_sync_supplier()
    {
        $this->datatables->select('masterTbl.supplierAutoID as supplierAutoID, masterTbl.supplierSystemCode as supplierSystemCode, masterTbl.supplierName as supplierName, masterTbl.supplierTelephone as supplierTelephone, masterTbl.supplierAddress1 as supplierAddress1, masterTbl.supplierEmail as supplierEmail, masterTbl.supplierCountry as supplierCountry', false)
            ->from('srp_erp_suppliermaster as masterTbl')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT supplierAutoID,erpSupplierAutoID FROM srp_erp_srm_suppliermaster WHERE srp_erp_srm_suppliermaster.erpSupplierAutoID = masterTbl.supplierAutoID AND companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('countryDiv', '$1', 'countryDiv(supplierCountry)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'supplierAutoID');

        $result = $this->datatables->generate();
        echo $result;
    }


    function add_suppliers()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Srm_master_model->add_suppliers());
        }
    }


    function referback_order_review()
    {
        $orderreviewID = $this->input->post('orderreviewID');

        $this->load->library('approvals');

        $this->db->select('approvedYN,documentSystemCode');
        $this->db->where('orderreviewID', trim($orderreviewID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $approved_purchase_order = $this->db->get()->row_array();
        if (!empty($approved_purchase_order)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_purchase_order['documentSystemCode']));
        } else {
            $status = $this->approvals->approve_delete($orderreviewID, 'ORD-RVW');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }
    function fetch_inquiryheader()
    {
        $companyID = current_companyID();
        $inquiryHeaderID = trim($this->input->post('inquiryID') ?? '');
        $records = $this->db->query("SELECT inquiryItemID FROM `srp_erp_srm_inquiryitem` where companyID = $companyID AND inquiryMasterID = $inquiryHeaderID")->row('inquiryItemID');
        echo json_encode($records);
    }
    function update_unit_price_srm(){ 
        $value = $this->input->post('value');
        $detailID = $this->input->post('inquiryDetailID');
        $companyID = current_companyID();

        $oldval = $this->db->query("SELECT supplierPrice FROM `srp_erp_srm_orderinquirydetails` WHERE companyID = $companyID AND inquiryDetailID = $detailID")->row("supplierPrice");
           
        $data['supplierPrice'] = $value;

        if($oldval!=$value) {
            $data_audit['old_val'] = $oldval;
            $data_audit['new_val'] = $value;
            $data_audit['companyID'] = $companyID;
            $data_audit['userID'] = current_userID();
            $data_audit['timestamp'] = current_date();
            $data_audit['type'] = 2;
            $data_audit['inquiryDetailID'] = $detailID;
            $this->db->insert('srp_erp_srm_pricechangehistory', $data_audit);
        }
        $this->db->where('inquiryDetailID', $detailID);
        $this->db->update('srp_erp_srm_orderinquirydetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
             echo json_encode(array('s', 'Unit Price Updated Sucessfully'));
         } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'Unit Price Updated faild'));
        }
    }

    

    function fetch_inquiry_details_view(){ 
    
        echo json_encode($this->Srm_master_model->fetch_inquiry_details_view());
        
    }

    function fetch_inquiry_terms_supplier(){ 
    
        echo json_encode($this->Srm_master_model->fetch_inquiry_terms_supplier());
        
    }

    function update_qty_srm(){ 
        $value = $this->input->post('value');
        $detailID = $this->input->post('inquiryDetailID');
        $companyID = current_companyID();
        $data['supplierQty'] = $value;
         
        $oldval = $this->db->query("SELECT supplierQty FROM `srp_erp_srm_orderinquirydetails` WHERE companyID = $companyID AND inquiryDetailID = $detailID")->row("supplierQty");
        if($oldval!=$value) {
                   $data_audit['old_val'] = $oldval;
                   $data_audit['new_val'] = $value;
                   $data_audit['companyID'] = $companyID;
                   $data_audit['userID'] = current_userID();
                   $data_audit['timestamp'] = current_date();
                   $data_audit['type'] = 1;
                   $data_audit['inquiryDetailID'] = $detailID;
                   $this->db->insert('srp_erp_srm_pricechangehistory', $data_audit);
               }
        
        
        
        $this->db->where('inquiryDetailID', $detailID);
        $this->db->update('srp_erp_srm_orderinquirydetails', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Qty Updated Sucessfully'));
        } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'Qty Updated faild'));
        }
    }
    function fetch_change_history()
    { 
        $companyID = current_companyID();
        $type  = $this->input->post('type');
        $detail_id = $this->input->post('detail_id');
        $this->datatables->select('Id,old_val,new_val,Ename2 as name,srp_erp_srm_pricechangehistory.Timestamp as chamgedtime', false);
        $this->datatables->from('srp_erp_srm_pricechangehistory');
        $this->datatables->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_srm_pricechangehistory.userID ','left');
        $this->datatables->where('CompanyID', $companyID);
        $this->datatables->where('inquiryDetailID', $detail_id);
        $this->datatables->where('type', $type);
        echo $this->datatables->generate();
    }

    function fetch_purchase_request_srm()
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
        $this->datatables->select("srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.confirmedByEmpID as confirmedByEmp,companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency ,createdUserID,srp_erp_purchaserequestmaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value, ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_purchaserequestmaster.isDeleted as isDeleted,employee.Ename2 as createdUserNamepurchasereq,jobNumber");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
        $this->datatables->join('srp_employeesdetails employee','employee.EIdNo = srp_erp_purchaserequestmaster.createdUserID','left');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->add_column('prq_detail', '<b>Requested by : </b> $2 <br> <b>Exp Delivery Date : </b> $3 <br><b>Narration : </b> $1<br><b>Created By : </b> $5 <br> '.$jobno.'', 'narration,requestedByName,expectedDeliveryDate,transactionCurrency,createdUserNamepurchasereq,jobNumber');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        $this->datatables->where('srp_erp_purchaserequestmaster.approvedYN',1);
        $this->datatables->where('srp_erp_purchaserequestmaster.isRfqSelected',1);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action_srm(purchaseRequestID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_purchase_request_srm_pending()
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
        $this->datatables->select("srp_erp_purchaserequestmaster.purchaseRequestID as purchaseRequestID,srp_erp_purchaserequestmaster.confirmedByEmpID as confirmedByEmp,companyCode,purchaseRequestCode,narration,requestedByName,confirmedYN,approvedYN ,DATE_FORMAT(expectedDeliveryDate,'.$convertFormat.') AS expectedDeliveryDate,transactionCurrency ,createdUserID,srp_erp_purchaserequestmaster.transactionAmount,transactionCurrencyDecimalPlaces,det.transactionAmount as total_value, ROUND(det.transactionAmount, 2) as detTransactionAmount,srp_erp_purchaserequestmaster.isDeleted as isDeleted,employee.Ename2 as createdUserNamepurchasereq,jobNumber");
        $this->datatables->join('(SELECT SUM(totalAmount) as transactionAmount,purchaseRequestID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID) det', '(det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID)', 'left');
        $this->datatables->join('srp_employeesdetails employee','employee.EIdNo = srp_erp_purchaserequestmaster.createdUserID','left');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->add_column('prq_detail', '<b>Requested by : </b> $2 <br> <b>Exp Delivery Date : </b> $3 <br><b>Narration : </b> $1<br><b>Created By : </b> $5 <br> '.$jobno.'', 'narration,requestedByName,expectedDeliveryDate,transactionCurrency,createdUserNamepurchasereq,jobNumber');
        $this->datatables->edit_column('total_value', '<div class="pull-right"><b>$2 : </b> $1 </div>', 'format_number(total_value,transactionCurrencyDecimalPlaces),transactionCurrency');
        $this->datatables->where($where);
        $this->datatables->where('srp_erp_purchaserequestmaster.approvedYN',1);
        $this->datatables->where('srp_erp_purchaserequestmaster.isRfqSelected',0);
        //$this->datatables->or_where('createdUserID', $this->common_data['current_userID']);
        //$this->datatables->or_where('confirmedYN', 1);
        $this->datatables->add_column('confirmed', '$1', 'confirm_user_approval_drilldown(confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('approved', '$1', 'confirm_ap_user(approvedYN,confirmedYN,"PRQ",purchaseRequestID)');
        $this->datatables->add_column('edit', '$1', 'load_prq_action_srm(purchaseRequestID,confirmedYN,approvedYN,createdUserID,isDeleted,confirmedByEmp)');
        $this->datatables->edit_column('DT_RowClass', '$1', 'set_deleted_class(isDeleted)');
        echo $this->datatables->generate();
    }

    function fetch_company_request_vendor()
    {
        // date inter change according to company policy
        $companyid = $this->common_data['company_data']['company_id'];
        $where = "r.companyID = " . $companyid. "";
        $convertFormat = convert_date_format_sql();

        $this->datatables->select("r.companyReqID as companyReqID ,r.country as CountryDes,employee.isActive as isActive,employee.masterConfirmedYN as masterConfirmedYN,employee.masterApprovedYN as masterApprovedYN,r.systemSupplierID as systemSupplierID,r.confirmYN as confirmYN,r.providerName as providerName,r.contactPersonEmail as contactPersonEmail,r.pointContactphone as pointContactphone,r.address1 as address1,r.approveYN as approveYN");
       // $this->datatables->join('srp_erp_countrymaster c','c.countryID = r.country','left');
        $this->datatables->from('srp_erp_srm_vendor_company_requests r');
        $this->datatables->join('srp_erp_suppliermaster employee','employee.supplierAutoID = r.systemSupplierID','left');
        $this->datatables->add_column('profile', '$1', 'load_company_request_vendor_image(companyReqID,providerName,contactPersonEmail,address1)');
        $this->datatables->add_column('action', '$1', 'load_company_request_vendor_view_action(companyReqID,approveYN,confirmYN)');
        // $this->datatables->where($where);

        // $this->datatables->select("r.supplierSystemCode as supplierSystemCode,r.supplierCurrency as supplierCurrency,r.supplierAutoID as supplierAutoID,r.supplierName as supplierName,r.supplierEmail as supplierEmail,r.supplierImage as supplierImage,r.isActive as isActive,c.CountryDes as CountryDes");
        // $this->datatables->join('srp_erp_countrymaster c','c.countryID = r.supplierCountry','left');
        // $this->datatables->from('srp_erp_srm_suppliermaster_company_request r');
        // $this->datatables->add_column('profile', '$1', 'load_company_request_vendor_image(supplierAutoID,supplierName,supplierEmail,supplierImage)');
       // $this->datatables->add_column('action', '$1', 'load_company_request_vendor_view_action(supplierAutoID)');
        $this->datatables->add_column('status', '$1', 'load_company_request_status(approveYN,confirmYN)');
        $this->datatables->add_column('supplierApprovalStatus', '$1', 'approvalStatus(isActive,masterConfirmedYN,masterApprovedYN,"ASM","SUP",systemSupplierID)');
        $this->datatables->where($where);
       
        echo $this->datatables->generate();
    }

    function save_company_request_approve()
    {


        $this->form_validation->set_rules("sup", 'Supplier', 'trim|required');
        $this->form_validation->set_rules("requestMasterID", 'request', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $uniqMesg = array_unique($msg);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->Srm_master_model->save_company_request_approve());
        }
    }

    function fetch_order_review_srm($custom = true)
    {
        /*$CI =& get_instance();
        $CI->db->select("inquiryID,documentCode");
        $CI->db->from('srp_erp_srm_orderinquirydetails');
        $CI->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderinquirydetails.inquiryMasterID');
        $CI->db->where('isSupplierSubmited', 1);
        $CI->db->where('srp_erp_srm_orderinquirydetails.companyID', $CI->common_data['company_data']['company_id']);
        $inquiry = $CI->db->get()->result_array();
        $inquiry_arr = array('' => 'Select Inquiry');
        if (isset($inquiry)) {
            foreach ($inquiry as $row) {
                $inquiry_arr[trim($row['inquiryID'] ?? '')] = (trim($row['documentCode'] ?? ''));
            }
        }
        return $inquiry_arr;*/



        $companyID = current_companyID();

        $this->datatables->select('srp_erp_srm_orderinquirymaster.inquiryID as inquiryID,srp_erp_srm_orderinquirymaster.orderreviewID as orderreviewID,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.referenceNumber,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.isRfqSubmitted');
        $this->datatables->from('srp_erp_srm_orderinquirymaster');
   
        $this->datatables->where('srp_erp_srm_orderinquirymaster.companyID', $companyID);
        $this->datatables->where('srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN', 1);
        $this->datatables->add_column('action', '$1', 'load_order_review_action(inquiryID,orderreviewID)');
        $this->datatables->add_column('rfq', '$1', 'load_order_submit_count(inquiryID)');
        echo $this->datatables->generate();
    }

    function fetch_order_review_srm_pending($custom = true)
    {
        $companyID = current_companyID();

        $this->datatables->select('srp_erp_srm_orderinquirymaster.inquiryID as inquiryID,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.referenceNumber,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.isRfqSubmitted');
        $this->datatables->from('srp_erp_srm_orderinquirymaster');
        
        $this->datatables->where('srp_erp_srm_orderinquirymaster.companyID', $companyID);
        $this->datatables->where('srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN', 0);
        $this->datatables->add_column('action', '$1', 'load_order_review_action_pending(inquiryID)');
        $this->datatables->add_column('status', '$1', 'load_order_review_status_pending(inquiryID)');
        $this->datatables->add_column('rfq', '$1', 'load_order_submit_count(inquiryID)');
        echo $this->datatables->generate();
    }

    function fetch_selfservice_order_review_srm_pending($custom = true)
    {
        $companyID = current_companyID();

        $this->datatables->select('srp_erp_srm_orderinquirymaster.inquiryID as inquiryID,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.referenceNumber,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.isRfqSubmitted');
        $this->datatables->from('srp_erp_srm_orderinquirymaster');
        
        $this->datatables->where('srp_erp_srm_orderinquirymaster.companyID', $companyID);
        $this->datatables->where('srp_erp_srm_orderinquirymaster.isOrderReviewConfirmYN', 0);
        $this->datatables->add_column('action', '$1', 'load_selfservice_order_review_action_pending(inquiryID)');
        $this->datatables->add_column('status', '$1', 'load_order_review_status_pending(inquiryID)');
        $this->datatables->add_column('rfq', '$1', 'load_order_submit_count(inquiryID)');
        echo $this->datatables->generate();
    }



    function prqViewTable()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $transactionCurrencyID = trim($this->input->post('transactionCurrencyID') ?? '');

        $this->datatables->select('purchaseRequestID as purchaseRequestID,purchaseRequestCode as purchaseRequestCode,documentDate,narration as narration,expectedDeliveryDate,requestedByName,transactionAmount,transactionCurrency,transactionCurrencyDecimalPlaces');
        $this->datatables->from('srp_erp_purchaserequestmaster');
        $this->datatables->where('approvedYN', 1);
        $this->datatables->where('isRfqSelected', 0);
        $this->datatables->where('transactionCurrencyID', $transactionCurrencyID);
        $this->datatables->where('companyID', $companyID);
        $this->datatables->add_column('action', '$1', 'load_prq_action(purchaseRequestID,purchaseRequestCode,narration)');
        $this->datatables->edit_column('transactionAmount', '<div class="text-right"><b>$2 : </b> $1 </div>', 'format_number(transactionAmount,transactionCurrencyDecimalPlaces),transactionCurrency');
        echo $this->datatables->generate();       
    }

    function save_my_chat()
    {
        $this->form_validation->set_rules('chat_msg', 'Message', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_my_chat());
        }
    }

    
    function send_checking_api()
    {
       
            echo json_encode($this->Srm_master_model->send_checking_api());
        
    }

    function load_my_chat_view()
    {
        $chat_msg = trim($this->input->post('chat_msg') ?? '');
        $inquiryDetailID = trim($this->input->post('inquiryDetailID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $chatType = trim($this->input->post('chatType') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $documentID = trim($this->input->post('documentID') ?? '');

        $this->db->select('*');
        $this->db->where('inquiryDetailID', $inquiryDetailID);
        $this->db->where('inquiryMasterID',  $inquiryMasterID);
        $this->db->where('supplierID', $supplierID);
        $this->db->where('chatType', $chatType);
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('documentID', $documentID);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_srm_vendor_chat');
        $data['master'] = $this->db->get()->result_array();

       
        $this->load->view('system/srm/chat/chat_view_body', $data);

    }

    function load_my_chat_view_open()
    {
        $chat_msg = trim($this->input->post('chat_msg') ?? '');
       // $inquiryDetailID = trim($this->input->post('inquiryDetailID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $chatType = trim($this->input->post('chatType') ?? '');
       // $itemAutoID = trim($this->input->post('itemAutoID') ?? '');

        $this->db->select('*');
      //  $this->db->where('inquiryDetailID', $inquiryDetailID);
        $this->db->where('inquiryMasterID',  $inquiryMasterID);
        $this->db->where('supplierID', $supplierID);
        $this->db->where('chatType', $chatType);
      //  $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('companyID', current_companyID());
        $this->db->from('srp_erp_srm_vendor_chat');
        $data['master'] = $this->db->get()->result_array();
       
        $this->load->view('system/srm/chat/chat_view_body', $data);

    }

    function save_line_wise_refer_back()
    {
        $this->form_validation->set_rules('comment_technical', 'Technical Specification', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_line_wise_refer_back());
        }
    }

    function srm_rfq_document_upload_line_wise()
    {
      
       // $this->form_validation->set_rules('doc_master_id', 'Document Name', 'trim|required');
       
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        // if ($this->form_validation->run() == FALSE) {
        //     echo json_encode(array('e', validation_errors()));
        // } else {
            echo json_encode($this->Srm_master_model->srm_rfq_document_upload_line_wise());
       // }
       

    }

    function srm_rfq_document_delete_line_wise(){
        $doc_master_id = $this->input->post('id');

        $this->db->where('autoID', $doc_master_id);
        $results = $this->db->delete('srp_erp_srm_vendor_rfq_linewise_documents');

        $this->db->trans_complete();
        if ( $this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $det = array('s', 'Error occurred');
            echo json_encode($det);
        } else {
            $this->db->trans_commit();
            $dt = array('s', 'Document remove Successfully');

            echo json_encode($dt);
        }
    }

    function save_new_supplier_quick()
    {
        if (!$this->input->post('supplierAutoID')) {
            $this->form_validation->set_rules('supplierCurrency', 'supplier Currency', 'trim|required');
        }
        $this->form_validation->set_rules('suppliercode', 'Supplier Code', 'trim|required');
        $this->form_validation->set_rules('supplierName', 'supplier Name', 'trim|required');
        $this->form_validation->set_rules('suppliercountry', 'supplier country', 'trim|required');
        $this->form_validation->set_rules('nameOnCheque', 'Name On Cheque', 'trim|required');
        /*        $this->form_validation->set_rules('supplierTelephone', 'supplier Telephone', 'trim|required');
                $this->form_validation->set_rules('supplierEmail', 'supplier Email', 'trim|required');
                $this->form_validation->set_rules('supplierAddress1', 'Address 1', 'trim|required');
                $this->form_validation->set_rules('supplierAddress2', 'Address 2', 'trim|required');
                $this->form_validation->set_rules('supplierCreditLimit', 'Credit Limit', 'trim|required');
                $this->form_validation->set_rules('supplierCreditPeriod', 'Credit Period', 'trim|required|max_length[3]');*/
        $this->form_validation->set_rules('liabilityAccount', 'liabilityAccount', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            //$this->session->set_flashdata($msgtype = 'e', validation_errors());
            //echo json_encode(FALSE);
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->save_new_supplier_quick());
        }
    }

    function company_request_referback_level_approval(){
 
        $this->form_validation->set_rules('masterID', 'Company Request ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
      
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Srm_master_model->company_request_referback_level_approval());
        }
    }

    function load_customer_order_confirmation_view()
    {
        $orderID = ($this->uri->segment(3)) ? $this->uri->segment(3) : trim($this->input->post('customerOrderID') ?? '');
        
        $this->load->library('NumberToWords');

        $response_type = $this->input->post('html');
        // createdUserID
        // confirmedByEmpID
        $data['html'] = $response_type;
        $data['approval'] = $this->input->post('approval');

        //$data['extra'] = $this->Delivery_order_model->fetch_delivery_order_full_details($orderID);
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        //$customerOrderID = trim($this->input->post('customerOrderID') ?? '');
        $this->db->select('srp_erp_srm_customerordermaster.customerOrderID,srp_erp_srm_customerordermaster.customerOrderCode,DATE_FORMAT(srp_erp_srm_customerordermaster.documentDate,\'' . $convertFormat . '\') AS documentDate,DATE_FORMAT(srp_erp_srm_customerordermaster.expiryDate,\'' . $convertFormat . '\') AS expiryDate,srp_erp_srm_customermaster.CustomerName,CurrencyCode,narration,srp_erp_srm_customerordermaster.status as orderStatus,contactPersonNumber,CustomerAddress,referenceNumber,srp_erp_srm_status.description as statusDescription,backgroundColor,fontColor,
        srp_erp_customermaster.customerName as CustomerName,srp_erp_srm_customerordermaster.bidStartDate,srp_erp_srm_customerordermaster.bidEndDate,srp_erp_srm_customerordermaster.isBackToBack,srp_erp_suppliermaster.supplierName');
        $this->db->from('srp_erp_srm_customerordermaster');
        $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_customermaster', 'srp_erp_customermaster.CustomerAutoID = srp_erp_srm_customerordermaster.customerID', 'LEFT');
        $this->db->join('srp_erp_suppliermaster', 'srp_erp_suppliermaster.supplierAutoID = srp_erp_srm_customerordermaster.supplierID', 'LEFT');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_customerordermaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
        $this->db->join('srp_erp_srm_status', 'srp_erp_srm_customerordermaster.status = srp_erp_srm_status.statusID', 'LEFT');
        $this->db->where('customerOrderID', $orderID);
        $data['extra'] = $this->db->get()->row_array();
        //var_dump($data['extra']);
        //exit;

        $this->db->select('*,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode');
        $this->db->from('srp_erp_srm_customerorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_customerorderdetails.itemAutoID', 'LEFT');
        $this->db->where('srp_erp_srm_customerorderdetails.companyID', $companyID);
        $this->db->where('srp_erp_srm_customerorderdetails.customerOrderID', $orderID);
        $data['orderitem'] = $this->db->get()->result_array();
        //var_dump($data['orderitem']);
        //exit;

        $data['signature'] = (!$response_type)? fetch_signature_level('DO'): '';
        $data['logo'] = ($response_type)? htmlImage: mPDFImage;
        //$data['approver_details'] = approved_emp_details('DO', $orderID);

        

        $where = [ 'companyID' => current_companyID(), 'documentID' => 'DO' ];
        $printHeaderFooterYN = $this->db->get_where('srp_erp_documentcodemaster', $where)->row('printHeaderFooterYN'); /*Header*/
        

        $data['printHeaderFooterYN'] = $printHeaderFooterYN;
        //$doc_code = $data['extra']['master']['DOCode'];
        //$data['doc_code'] = str_replace('/','-',$doc_code);
        //$data['isGroupByTax'] = existTaxPolicyDocumentWise('srp_erp_deliveryorder',trim($orderID),'DO','DOAutoID');
        
        if ($response_type) {
            $html = $this->load->view('system/srm/customer-order/srm_customer_order_print_view', $data, true);
            echo $html;
        } else {
            $this->load->library('pdf');
            $print_link = print_template_pdf('CO','system/srm/customer-order/srm_customer_order_print');
            $paper_size = print_template_paper_size('CO','A4');

            $view = $this->load->view($print_link, $data, true);
            $this->pdf->printed($view, $paper_size, 1, $printHeaderFooterYN);
        }
    }

}