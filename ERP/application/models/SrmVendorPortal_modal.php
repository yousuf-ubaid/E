<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class SrmVendorPortal_modal extends CI_Model
{
    function __contruct()
    {
        parent::__contruct();
        $this->load->helper('srm');
       // $this->load->library('s3');
        
    }
    function save_vendor_submit_rfq()
    {

        $supplierID = $this->input->post('supplierID');
        $quatationId = $this->input->post('quatationId');
        $companyID = $this->input->post('companyID');
       // $comments = $this->input->post('comments');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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
       

        // $valueaccept = $this->input->post('value');
        // $companyid = $this->input->post('companyID');
        // $quatationId = $this->input->post('quatationId');

        // $data['approvedYN'] = $valueaccept;
        // $data['approvalComment'] = $comments;
        // $databaseresult->where('companyID', $companyid);
        // $databaseresult->where('quotationAutoID', $quatationId);
        // $databaseresult->update('srp_erp_crm_quotation', $data);
        $inquiryDetailID = $this->input->post('inquiryDetailID');
        $comments = $this->input->post('comments');
        $expectedDeliveryDate = $this->input->post('expectedDeliveryDate');
        $unitcost = $this->input->post('unitcost');
        $Qty = $this->input->post('Qty');
        $discountamountcal = $this->input->post('discountamountcal');
        $discount = $this->input->post('discount');
        $discountamt = $this->input->post('discountamt');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $taxamt = $this->input->post('taxamt');
        $totalcost = $this->input->post('totalcost');
        $date_format_policy = 'Y-m-d';

        $subt = $this->input->post('subt');
        $dist = $this->input->post('dist');
        $tax = $this->input->post('tax');
        $gtot = $this->input->post('gtot');

        foreach($inquiryDetailID as $key =>$val){ 
            $FYEnd=null;
            if($expectedDeliveryDate[$key]!=null){
                $FYEnd = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
            }

            $data_detail['isSupplierSubmited'] = 1;
            $data_detail['SupplierNarration'] =  '';
            $data_detail['supplierTechnicalSpecification'] =  $comments[$key];
            $data_detail['supplierExpectedDeliveryDate'] =  $FYEnd;
            $data_detail['supplierPrice'] =  $unitcost[$key];
            $data_detail['supplierQty'] =  $Qty[$key];
            $data_detail['supplierDiscount'] =  $discountamt[$key];
            $data_detail['supplierTax'] =  $taxamt[$key];
            $data_detail['supplierTaxPercentage'] =  $item_taxPercentage[$key];
            $data_detail['supplierDiscountPercentage'] =  $discount[$key];
            $data_detail['lineSubTotal'] =  $totalcost[$key];

            $databaseresult->where('inquiryDetailID', $val);
            $databaseresult->where('companyID',  $companyID);
            $databaseresult->update('srp_erp_srm_orderinquirydetails', $data_detail);
            
        }


        $databaseresult->trans_complete();

        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred',false);
        } else {
           

            $data_Sub['companyID'] = $companyID;
            $data_Sub['supplierID'] = $supplierID;
            $data_Sub['inquiryMasterID'] = $quatationId;
            $data_Sub['subTotal'] = $subt;
            $data_Sub['discountPrice'] = $dist;
            $data_Sub['taxPrice'] = $tax;
            $data_Sub['grandTotal'] = $gtot;
            $data_Sub['isRfqSubmitted'] = 1;
            $databaseresult->insert('srp_erp_srm_vendor_submit_rfq', $data_Sub);
            $databaseresult->trans_commit();
            return array('s', 'RFQ Submit Successfully',true);
        }

    }

    function do_upload_aws_S3($description = true)
    {
        //$this->load->model('upload_modal');
      //  include('\application\libraries\S3.php');
        $supplierID = $this->input->post('supID');
        $inquiryMasterID = $this->input->post('inquiryMasterID');
        $companyID = $this->input->post('comID');
       // $comments = $this->input->post('comments');
       
        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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
            
            $databaseresult->trans_start();
            $databaseresult->select('companyID');
            $databaseresult->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $databaseresult->get('srp_erp_documentattachments')->result_array();

            $file_name = $this->input->post('documentID') . '_' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,5) . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if(empty($ext)) {
              //  echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                return array('s', 'No extension found for the selected attachment');
                exit();
            }

           // print_r($this->s3);exit;

            $databaseresult->select('company_code');
            $databaseresult->where("company_id",$companyID);
            $databaseresult->from("srp_erp_company");
            $company = $databaseresult->get()->row_array();

           /// $cc = current_companyCode();
            $folderPath = !empty($company['company_code']) ? $company['company_code'] . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['documentSubID'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $companyID;
            // $data['companyCode'] = $this->common_data['company_data']['company_code'];
            // $data['createdUserGroup'] = $this->common_data['user_group'];
            // $data['modifiedPCID'] = $this->common_data['current_pc'];
            // $data['modifiedUserID'] = $this->common_data['current_userID'];
            // $data['modifiedUserName'] = $this->common_data['current_user'];
            // $data['modifiedDateTime'] = $this->common_data['current_date'];
            // $data['createdPCID'] = $this->common_data['current_pc'];
            // $data['createdUserID'] = $this->common_data['current_userID'];
            // $data['createdUserName'] = $this->common_data['current_user'];
            // $data['createdDateTime'] = $this->common_data['current_date'];
            $databaseresult->insert('srp_erp_documentattachments', $data);
            $databaseresult->trans_complete();
            if ($databaseresult->trans_status() === FALSE) {
                $databaseresult->trans_rollback();
                return array('s', 'Error occurred');
            } else {
                $databaseresult->trans_commit();
                return array('s', 'Document Submit Successfully');
            }
        // }
    }

    function remove_vendor_submit_documents(){
        $id = $this->input->post('id');

        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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

        $databaseresult->where('attachmentID', $id);
        $result = $databaseresult->delete('srp_erp_documentattachments');
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Document Delete Successfully');
        }
    }

    function save_sales_quotation_customer_feedback_comment()
    {
        $value = $this->input->post('comment');
        $quatationId = $this->input->post('quatationId');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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


        $data['approvalComment'] = $value;
        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('quotationAutoID', $quatationId);
        $databaseresult->update('srp_erp_crm_quotation', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Comment Added Successfully');
        }
    }
    function save_sales_qutation_accepted_item()
    {
        $DetailID = $this->input->post('DetailID');
        $val = $this->input->post('val');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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


       if($val==1)
       {
           $data['selectedByCustomer'] = 1;
       } else
       {
           $data['selectedByCustomer'] = 0;
       }

        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('contractDetailsAutoID', $DetailID);
        $databaseresult->update('srp_erp_crm_quotationdetails', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Item Added Successfully');
        }
    }
    function save_sales_qutation_accepted_item_remarks()
    {
        $DetailID = $this->input->post('DetailID');
        $val = $this->input->post('val');
        $companyID = $this->input->post('companyID');

        $this->db->select('*');
        $this->db->where("company_id",$companyID);
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


        $data['remarks'] = $val;
        $databaseresult->where('companyID', $companyID);
        $databaseresult->where('contractDetailsAutoID', $DetailID);
        $databaseresult->update('srp_erp_crm_quotationdetails', $data);
        $databaseresult->trans_complete();
        if ($databaseresult->trans_status() === FALSE) {
            $databaseresult->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $databaseresult->trans_commit();
            return array('s', 'Sales Quotation Remarks Saved Successfully');
        }
    }



}