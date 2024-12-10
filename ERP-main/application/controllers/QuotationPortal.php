<?php

class QuotationPortal extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('QuotationPortal_modal');
    }

    function crm_quotation_view()
    {


             $companyid = base64_decode($_GET['comp']);
             $quatationId = base64_decode($_GET['qut']);

            $this->db->select('*');
            $this->db->where("company_id",$companyid);
            $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
            $resultDb2 = $this->db->get("user")->row_array();
            $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
            $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
            $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
            $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
            $config['dbdriver'] = 'mysqli';
            $config['db_debug'] = (ENVIRONMENT !== 'production');
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = TRUE;
           $databaseresult = $this->load->database($config, True);


        $where_header = "qut.companyID = '{$companyid}' AND qut.quotationAutoID = '{$quatationId}'";
        $databaseresult->select('quotationCode,DATE_FORMAT(quotationDate,\'%d-%m-%Y\') AS quotationDate,referenceNo, org.Name as organizationName,org.billingAddress as orgAddress,org.telephoneNo,CurrencyCode,qut.quotationPersonEmail as email,DATE_FORMAT(quotationExpDate,\'%d-%m-%Y\') AS quotationExpDate,quotationNarration,DATE_FORMAT(qut.confirmedDate,\'%d-%m-%Y\') AS qutConfirmDate,qut.confirmedByName as qutConfirmedUser,qut.confirmedYN as confirmedYNqut,termsAndConditions,qut.quotationPersonNumber,qut.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesqut,DATEDIFF( quotationExpDate,DATE_FORMAT(NOW( ), \'%Y-%m-%d\' )) AS linkexpiary,qut.approvedYN as approvedYN1,qut.approvalComment,concat(contact.firstName," ",contact.lastName) as quotationPersonName');
        $databaseresult->from('srp_erp_crm_quotation qut');
        $databaseresult->join('srp_erp_crm_organizations org', 'org.organizationID = qut.customerID', 'LEFT');
        $databaseresult->join('srp_erp_currencymaster cm', 'qut.transactionCurrencyID = cm.currencyID');
        $databaseresult->join('srp_erp_crm_contactmaster contact', 'contact.contactID = qut.quotationPersonID', 'LEFT');
        $databaseresult->where($where_header);
        $data['master'] = $databaseresult->get()->row_array();

        $where_detail = "srp_erp_crm_quotationdetails.companyID = '{$companyid}' AND srp_erp_crm_quotationdetails.contractAutoID = '{$quatationId}'";
        $databaseresult->select('srp_erp_crm_quotationdetails.*,IFNULL(srp_erp_crm_products.productName,CONCAT(srp_erp_itemmaster.itemDescription,\' - \',srp_erp_itemmaster.itemSystemCode)) AS productName,DATE_FORMAT(expectedDeliveryDate,\'%d-%m-%Y\') AS expectedDeliveryDate');
        $databaseresult->from('srp_erp_crm_quotationdetails');
        $databaseresult->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
        $databaseresult->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_crm_quotationdetails.itemAutoID', 'LEFT');
        //$databaseresult->order_by('contractDetailsAutoID','ASC');
        $databaseresult->where($where_detail);
        $data['detail'] = $databaseresult->get()->result_array();
        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();
        $data['logo'] = mPDFImage;
        $data['quatationId']=$quatationId;
        $data['companyID']=$companyid;
        $data['type'] = $this->input->post('type');
        if($data['master']['linkexpiary']>=0)
        {
            $this->load->view('system/crm/crm_quotation_print_new_view_customer',$data);

        }else
        {
            $this->load->view('system/crm/crm_quotation_link_expire');
        }


    }
    function save_sales_quotation_customer_feedback()
    {
        echo json_encode($this->QuotationPortal_modal->save_sales_quotation_customer_feedback());

    }
    function save_sales_quotation_customer_feedback_comment()
    {
        echo json_encode($this->QuotationPortal_modal->save_sales_quotation_customer_feedback_comment());

    }
    function load_qut_view()
    {
        $companyid = $this->input->post('companyid');
        $quatationId =  $this->input->post('quatationid');
        $csrf_token =  $this->input->post('csrf_token');

        $this->db->select('*');
        $this->db->where("company_id",$companyid);
        $this->db->join('srp_erp_company', ' user.companyID = srp_erp_company.company_id', 'inner');
        $resultDb2 = $this->db->get("user")->row_array();
        $config['hostname'] = trim($this->encryption->decrypt($resultDb2["host"]));
        $config['username'] = trim($this->encryption->decrypt($resultDb2["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($resultDb2["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($resultDb2["db_name"]));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = (ENVIRONMENT !== 'production');
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';
        $config['cachedir'] = '';
        $config['swap_pre'] = '';
        $config['encrypt'] = FALSE;
        $config['compress'] = FALSE;
        $config['stricton'] = FALSE;
        $config['failover'] = array();
        $config['save_queries'] = TRUE;
        $databaseresult = $this->load->database($config, True);


        $where_header = "qut.companyID = '{$companyid}' AND qut.quotationAutoID = '{$quatationId}'";
        $databaseresult->select('quotationCode,DATE_FORMAT(quotationDate,\'%d-%m-%Y\') AS quotationDate,referenceNo, org.Name as organizationName,org.billingAddress as orgAddress,org.telephoneNo,CurrencyCode,qut.quotationPersonEmail as email,DATE_FORMAT(quotationExpDate,\'%d-%m-%Y\') AS quotationExpDate,quotationNarration,DATE_FORMAT(qut.confirmedDate,\'%d-%m-%Y\') AS qutConfirmDate,qut.confirmedByName as qutConfirmedUser,qut.confirmedYN as confirmedYNqut,termsAndConditions,qut.quotationPersonNumber,qut.transactionCurrencyDecimalPlaces as transactionCurrencyDecimalPlacesqut,DATEDIFF(quotationExpDate,NOW()) as linkexpiary,qut.approvedYN as approvedYN1,qut.approvalComment,concat(contact.firstName," ",contact.lastName) as quotationPersonName,qut.quotationNarration');
        $databaseresult->from('srp_erp_crm_quotation qut');
        $databaseresult->join('srp_erp_crm_organizations org', 'org.organizationID = qut.customerID', 'LEFT');
        $databaseresult->join('srp_erp_currencymaster cm', 'qut.transactionCurrencyID = cm.currencyID');
        $databaseresult->join('srp_erp_crm_contactmaster contact', 'contact.contactID = qut.quotationPersonID');
        $databaseresult->where($where_header);
        $data['master'] = $databaseresult->get()->row_array();

        $where_detail = "srp_erp_crm_quotationdetails.companyID = '{$companyid}' AND srp_erp_crm_quotationdetails.contractAutoID = '{$quatationId}'";
        $databaseresult->select('srp_erp_crm_quotationdetails.*,DATE_FORMAT(srp_erp_crm_quotationdetails.expectedDeliveryDate,\'%d-%m-%Y\') AS expectedDeliveryDateformated,IFNULL(srp_erp_crm_products.productName,CONCAT(srp_erp_itemmaster.itemDescription,\' - \',srp_erp_itemmaster.itemSystemCode)) AS productnamewithcomment, DATE_FORMAT(expectedDeliveryDate,\'%d-%m-%Y\') AS expectedDeliveryDate,uom.UnitShortCode as uomshortcode');
        $databaseresult->from('srp_erp_crm_quotationdetails');
        $databaseresult->join('srp_erp_crm_products', 'srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID', 'LEFT');
        $databaseresult->join('srp_erp_unit_of_measure uom', 'uom.UnitID = srp_erp_crm_quotationdetails.unitOfMeasureID', 'LEFT');
        $databaseresult->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_crm_quotationdetails.itemAutoID', 'LEFT');
        $databaseresult->order_by('contractDetailsAutoID','ASC');
        $databaseresult->where($where_detail);
        $data['detail'] = $databaseresult->get()->result_array();
        
        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();
        $data['logo'] = mPDFImage;
        $data['quatationId']=$quatationId;
        $data['companyID']=$companyid;
        $data['csrf_token']= $csrf_token;

        $data['type'] = $this->input->post('type');
        $this->load->view('system/crm/crm_quotation_print_new_view_customer_detail_tbl',$data);
    }
    function save_sales_qutation_accepted_item()
    {
        echo json_encode($this->QuotationPortal_modal->save_sales_qutation_accepted_item());
    }
    function save_sales_qutation_accepted_item_remarks()
    {
        echo json_encode($this->QuotationPortal_modal->save_sales_qutation_accepted_item_remarks());
    }






}
