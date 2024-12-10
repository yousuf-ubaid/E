<?php

class SrmVendorPortal extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        //Srm_vendor_potal_model
        $this->load->model('SrmVendorPortal_modal');
    }

    function vendor_rfq_view()
    {
            $companyid = base64_decode($_GET['comp']);
            $inquiryID = base64_decode($_GET['qut']);
            $supplierId = base64_decode($_GET['sup']);

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

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_srm_vendor_submit_rfq');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('supplierID', $supplierId);
        $databaseresult->where('inquiryMasterID', $inquiryID);
        $databaseresult->where('isRfqSubmitted', 1);

        $is_submit =$databaseresult->get()->row_array();

        if($is_submit){
            $data['is_submit_rfq']=1;
        }else{
            $data['is_submit_rfq']=0;
        }

        $databaseresult->select('srp_erp_srm_orderinquirymaster.inquiryID,confirmedYN,rfqExpDate,confirmedByName,DATEDIFF(rfqExpDate,NOW()) as linkexpiary,DATE_FORMAT(confirmedDate,\'%d-%m-%Y\') as confirmedDate,DATE_FORMAT(createdDateTime,\'%d-%m-%Y\') as createdDateTime,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID');
        $databaseresult->from('srp_erp_srm_orderinquirymaster');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('inquiryID', $inquiryID);

        $data['master']  = $databaseresult->get()->row_array();

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_srm_suppliermaster');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('supplierAutoID', $supplierId);

        $data['supplier']  = $databaseresult->get()->row_array();

        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();


        $data['logo'] = mPDFImage;
        $data['quatationId']=$inquiryID;
        $data['companyID']=$companyid;
        $data['supplierID']=$supplierId;

        $data['type'] = $this->input->post('type');

        if($data['master']['linkexpiary']>=0)
        {
            $this->load->view('system/srm/supplier_portal/srm_rfq_submit_view',$data);

        }else
        {
            $this->load->view('system/srm/supplier_portal/srm_rfq_link_expire');
        }


    }
    function save_vendor_submit_rfq()
    {
        $inquiryDetailID =  $this->input->post('inquiryDetailID[]');
       
        foreach($inquiryDetailID as $key => $inquiry) {
           
            $this->form_validation->set_rules("unitcost[{$key}]", "unitcost", 'trim|required');
            $this->form_validation->set_rules("Qty[{$key}]", "Qty", 'trim|required');
            $this->form_validation->set_rules("expectedDeliveryDate[{$key}]", "expectedDeliveryDate", 'trim|required');
           
        }

        if ($this->form_validation->run() == FALSE) {
            
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->SrmVendorPortal_modal->save_vendor_submit_rfq());
        }
       

    }


    function save_sales_quotation_customer_feedback_comment()
    {
        echo json_encode($this->QuotationPortal_modal->save_sales_quotation_customer_feedback_comment());

    }

    function do_upload_aws_S3()
    {
        
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
        $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
       
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->SrmVendorPortal_modal->do_upload_aws_S3());
        }
       

    }

    function load_details_view()
    {
        $companyid = $this->input->post('companyid');
        $quatationId =  $this->input->post('quatationid');
        $csrf_token =  $this->input->post('csrf_token');
        $supplierID = $this->input->post('supplierID');
        $type = $this->input->post('type');

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

        $databaseresult->select('srp_erp_srm_orderinquirymaster.inquiryID,confirmedYN,confirmedByName,DATEDIFF(rfqExpDate,NOW()) as linkexpiary,DATE_FORMAT(confirmedDate,\'%d-%m-%Y\') as confirmedDate,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID');
        $databaseresult->from('srp_erp_srm_orderinquirymaster');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('inquiryID', $quatationId);

        $data['master']  = $databaseresult->get()->row_array();

        $databaseresult->select('srp_erp_srm_orderinquirydetails.inquiryDetailID,srp_erp_srm_orderinquirydetails.supplierDiscount,srp_erp_srm_orderinquirydetails.supplierTaxPercentage,srp_erp_srm_orderinquirydetails.supplierDiscountPercentage,srp_erp_srm_orderinquirydetails.lineSubTotal,srp_erp_srm_orderinquirydetails.supplierTechnicalSpecification,srp_erp_srm_orderinquirydetails.supplierTax,srp_erp_srm_orderinquirydetails.supplierExpectedDeliveryDate,srp_erp_srm_orderinquirydetails.supplierPrice,srp_erp_srm_orderinquirydetails.supplierQty,srp_erp_srm_orderinquirydetails.supplierID,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_suppliermaster.supplierEmail,srp_erp_srm_suppliermaster.supplierAddress1,srp_erp_srm_orderinquirydetails.itemAutoID,srp_erp_srm_orderinquirydetails.requestedQty,srp_erp_srm_orderinquirydetails.expectedDeliveryDate,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
        $databaseresult->from('srp_erp_srm_orderinquirydetails');
        $databaseresult->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $databaseresult->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
        $databaseresult->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');

        
        $databaseresult->where('inquiryMasterID', $quatationId);
        $databaseresult->where('supplierID', $supplierID);
        $databaseresult->where('isRfqCreated', 1);
        $data['detail'] = $databaseresult->get()->result_array();
        
        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();

        $data['logo'] = mPDFImage;
        $data['quatationId']=$quatationId;
        $data['companyID']=$companyid;
        $data['csrf_token']= $csrf_token;

        if($type==0){
            $data['type'] = $type;
            $this->load->view('system/srm/supplier_portal/srm_rfq_details_table',$data);

        }else{
            $databaseresult->select('*');
            $databaseresult->from('srp_erp_srm_vendor_submit_rfq');
            $databaseresult->where('companyID', $companyid);
            $databaseresult->where('supplierID', $supplierID);
            $databaseresult->where('inquiryMasterID', $quatationId);
            $databaseresult->where('isRfqSubmitted', 1);
    
            $data['is_submit'] =$databaseresult->get()->row_array();

            $this->load->view('system/srm/supplier_portal/srm_rfq_details_table_submit',$data);
        }

       
    }

    function load_vendor_attachment_table()
    {
        $companyid = $this->input->post('companyid');
        $quatationId =  $this->input->post('quatationid');
        $csrf_token =  $this->input->post('csrf_token');
        $supplierID = $this->input->post('supplierID');
        $type = $this->input->post('type');

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

        $this->load->library('s3');

        $documentSubID = $quatationId.'_'.$companyid.'_'.$supplierID;

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_documentattachments');
        $databaseresult->where('documentID', 'ORD-RVW');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('documentSubID', $documentSubID);
        $data['detail'] = $databaseresult->get()->result_array();

        if($data['detail']){
            foreach($data['detail'] as $key=> $val){
                $link = $this->s3->createPresignedRequest($val['myFileName'], '1 hour');
                $data['detail'][$key]['url']= $link;
            }
        }

        $databaseresult->select('*');
        $databaseresult->from('srp_erp_srm_vendor_submit_rfq');
        $databaseresult->where('companyID', $companyid);
        $databaseresult->where('supplierID', $supplierID);
        $databaseresult->where('inquiryMasterID', $quatationId);
        $databaseresult->where('isRfqSubmitted', 1);

        $is_submit =$databaseresult->get()->row_array();

        if($is_submit){
            $data['is_submit_rfq']=1;
        }else{
            $data['is_submit_rfq']=0;
        }
        
        $databaseresult->select('*');
        $databaseresult->where("company_id",$companyid);
        $databaseresult->from("srp_erp_company");
        $data['company'] = $databaseresult->get()->row_array();

        $data['logo'] = mPDFImage;
        $data['quatationId']=$quatationId;
        $data['companyID']=$companyid;
        $data['csrf_token']= $csrf_token;

        $data['type'] = $this->input->post('type');
        $this->load->view('system/srm/supplier_portal/srm_rfq_attachment_details_table',$data);
    }

    function remove_vendor_submit_documents(){
        echo json_encode($this->SrmVendorPortal_modal->remove_vendor_submit_documents());
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
