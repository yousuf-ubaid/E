<?php

class Srm_master_model extends ERP_Model
{


    function __construct()
    {
        parent::__construct();
        $this->load->helper('srm');
       // $this->load->model('Procurement_modal');
        $this->load->helpers('procurement');
        $this->load->helpers('buyback_helper');
        $this->load->library('s3');
        $this->load->library('email_manual');
    }

    function save_customer()
    {
        $this->db->trans_start();
        $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        $data['secondaryCode'] = trim($this->input->post('customercode') ?? '');
        $data['customerName'] = trim($this->input->post('customerName') ?? '');
        $data['customerCurrencyID'] = trim($this->input->post('customerCurrency') ?? '');
        $data['customerCurrency'] = trim($currency_code[0] ?? '');

        $data['customerCountry'] = trim($this->input->post('customercountry') ?? '');
        $data['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
        $data['customeremail'] = trim($this->input->post('customerEmail') ?? '');
        $data['customerFax'] = trim($this->input->post('customerFax') ?? '');
        $data['customerAddress1'] = trim($this->input->post('customerAddress1') ?? '');
        $data['customerAddress2'] = trim($this->input->post('customerAddress2') ?? '');
        $data['customerUrl'] = trim($this->input->post('customerUrl') ?? '');
        $data['isActive'] = trim($this->input->post('isActive') ?? '');


        if (trim($this->input->post('customerAutoID') ?? '')) {
            $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
            $this->db->update('srp_erp_srm_customermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                //$this->session->set_flashdata('e', 'customer : ' . $data['customerName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => 'e', 'message' => 'customer : ' . $data['customerName'] . ' Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('status' => 's', 'message' => 'Customer Updated Successfully');
            }

        } else {
            $data['companyID'] = current_companyID();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdPCID'] = current_pc();
//
            $data['companyCode'] = current_companyCode();
            $data['createdUserGroup'] = user_group();

            $data['CustomerSystemCode'] = $this->sequence->sequence_generator('SRM-CUS');


            //$data['createdUserGroup'] = user_group();
            $data['timestamp'] = format_date_mysql_datetime();

            $this->db->insert('srp_erp_srm_customermaster', $data);

//        echo  $this->db->last_query();
//        exit;
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 's', 'message' => 'Customer Addition Failed');
            } else {
                $this->db->trans_commit();
                return array('status' => 's', 'message' => 'Customer Add Successfully');
            }

        }


    }

    function save_supplier()
    {
        $this->db->trans_start();
        $liability = fetch_gl_account_desc(trim($this->input->post('receivableAccount') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $data['secondaryCode'] = trim($this->input->post('suppliercode') ?? '');
        $data['supplierName'] = trim($this->input->post('supplierName') ?? '');
        $data['supplierCurrencyID'] = trim($this->input->post('supplierCurrency') ?? '');
        $data['supplierCurrency'] = trim($currency_code[0] ?? '');

        $data['supplierCountry'] = trim($this->input->post('suppliercountry') ?? '');
        $data['supplierTelephone'] = trim($this->input->post('supplierTelephone') ?? '');
        $data['supplieremail'] = trim($this->input->post('supplierEmail') ?? '');
        $data['supplierFax'] = trim($this->input->post('supplierFax') ?? '');
        $data['supplierAddress1'] = trim($this->input->post('supplierAddress1') ?? '');
        $data['supplierAddress2'] = trim($this->input->post('supplierAddress2') ?? '');
        $data['supplierUrl'] = trim($this->input->post('supplierUrl') ?? '');
        $data['isActive'] = trim($this->input->post('isActive') ?? '');
        if (trim($this->input->post('supplierAutoID') ?? '')) {
            $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
            $this->db->update('srp_erp_srm_suppliermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('status' => 'e', 'message' => 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('status' => 's', 'message' => 'Supplier : ' . $data['supplierName'] . ' Saved  Successfully');
            }

        } else {
            $data['companyID'] = current_companyID();
            $data['createdUserID'] = current_userID();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = format_date_mysql_datetime();
            $data['createdPCID'] = current_pc();
            $data['createdUserGroup'] = current_user_group();
            $data['companyCode'] = current_companyCode();
            $data['supplierSystemCode'] = $this->sequence->sequence_generator('SRM-SUP');
            $data['createdUserGroup'] = user_group();
            $data['timestamp'] = format_date_mysql_datetime();

            $this->db->insert('srp_erp_srm_suppliermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 's', 'message' => 'Supplier Save Failed');
            } else {
                $this->db->trans_commit();
                return array('status' => 's', 'message' => 'Supplier Saved Successfully');
            }

        }


    }


    function save_supplierItem()
    {
        $supplierAutoID = trim($this->input->post('supplierAutoID') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $this->db->select('*');
        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->where('supplierAutoID', $supplierAutoID);
        $output = $this->db->get('srp_erp_srm_supplieritems')->row_array();

        if (empty($output)) {
            $this->db->trans_start();
            $data['supplierAutoID'] = $supplierAutoID;
            $data['itemAutoID'] = $itemAutoID;
            $data['companyID'] = current_companyID();
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['timestamp'] = format_date_mysql_datetime();
            $this->db->insert('srp_erp_srm_supplieritems', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->_error_message();
                return array('error' => 1, 'message' => 'Error: ' . $error);

            } else {
                $this->db->trans_commit();
                return array('error' => 0, 'message' => 'Record Added Successfully.', 'code' => $supplierAutoID);
            }

        } else {
            return array('error' => 1, 'message' => 'This Item is already added');
        }


    }

    function load_customer_header()
    {
        $this->db->select('*');
        $this->db->where('customerAutoID', $this->input->post('customerAutoID'));
        return $this->db->get('srp_erp_srm_customermaster')->row_array();
    }

    function load_supplier_header()
    {
        $this->db->select('*');
        $this->db->where('supplierAutoID', $this->input->post('supplierAutoID'));
        return $this->db->get('srp_erp_srm_suppliermaster')->row_array();
    }

    function load_supplier_company_request_header()
    {
        $this->db->select('*');
        $this->db->where('companyReqID', $this->input->post('companyReqID'));
        $data_master= $this->db->get('srp_erp_srm_vendor_company_requests')->row_array();
        
        if($data_master['accountType']==1){

            $this->db->select('sup.*');
            $this->db->from('srp_erp_srm_suppliermaster masterTbl');
            $this->db->join('srp_erp_suppliermaster sup', 'masterTbl.erpSupplierAutoID = sup.supplierAutoID ', 'left');
            $this->db->where('masterTbl.supplierAutoID', $data_master['erpSupplierID']);
            $data_master_supplier = $this->db->get()->row_array();

            $data['masterSupplier']=$data_master_supplier;
        }

        $this->db->select('*');
        $this->db->where('companyReqMasterID', $this->input->post('companyReqID'));
        $data_master_doc= $this->db->get('srp_erp_srm_vendor_company_request_documents')->result_array();

        foreach($data_master_doc as $key=>$val){
            if($val['url']){
                $link = $this->s3->createPresignedRequest($val['url'], '1 hour');
                $data_master_doc[$key]['url']= $link;
            }
        }

        $this->db->select('*');
        $this->db->where('companyReqMasterID', $this->input->post('companyReqID'));
        $data_master_doc_other= $this->db->get('srp_erp_vendor_other_document')->result_array();

        if(!empty($data_master_doc_other)){

            foreach($data_master_doc_other as $key1=>$val){
                if($val['url']){
                    $link = $this->s3->createPresignedRequest($val['url'], '1 hour');
                    $data_master_doc_other[$key1]['url']= $link;
                }
            }
        }

        $this->db->select('*');
        $this->db->where('companyReqMasterID', $this->input->post('companyReqID'));
        $data_master_subcategories= $this->db->get('srp_erp_vendor_subcategories')->result_array();

        $this->db->select('*');
        $this->db->where('requestID', $this->input->post('companyReqID'));
        $srp_erp_vendor_register_family= $this->db->get('srp_erp_vendor_register_family')->result_array();
        

        $data['masterData']=$data_master;
        $data['masterSub']=$data_master_doc;
        $data['masterOther']=$data_master_doc_other;
        $data['masterSubCategory']=$data_master_subcategories;
        $data['family']=$srp_erp_vendor_register_family;

        return $data;

    }

    function load_supplier_items_details()
    {
        /*get post value */
        $supplierID = $this->input->post('supplierID');
        /*modal function */
        /* query from database table => srp_erp_srm_supplieritems where  supplierAutoID , join with item master */
        $where = "masterTbl.supplierAutoID = $supplierID AND masterTbl.isDeleted = 0 AND (itemmaster.financeCategory = 1 OR itemmaster.financeCategory = 2)";
        $this->db->select('masterTbl.supplierItemID,masterTbl.isDeleted,category.description as catDescription,itemmaster.mainCategory,itemmaster.seconeryItemCode,itemmaster.itemDescription,itemmaster.isActive,itemmaster.itemSystemCode');
        $this->db->from('srp_erp_srm_supplieritems masterTbl');
        $this->db->join('srp_erp_itemmaster itemmaster', 'masterTbl.itemAutoID = itemmaster.itemAutoID ', 'left');
        $this->db->join('srp_erp_itemcategory category', 'itemmaster.subcategoryID = category.itemCategoryID ', 'left');
        $this->db->where($where);
        $result = $this->db->get()->result_array();
        return $result;

    }

    /*SELECT `itmmaster`.*, `category`.`catDescription` FROM `srp_erp_itemmaster` `itmmaster
    ` LEFT JOIN `srp_erp_fa_category` `category` ON `itmmaster`.`subcategoryID`= `category`.`faCatID` LIMIT 10*/

    function load_supplier_itemsmaster()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $keyword = $this->input->post('keyword');
        $supplierID = $this->input->post('supplierID');
        $where = "itemmaster.companyID = $companyid AND (itemmaster.financeCategory = 1 OR itemmaster.financeCategory = 2)";
        $this->db->select('itemmaster.*,`category`.`Description` as catDescription,,supplierItem.supplierItemID');
        $this->db->from('srp_erp_itemmaster itemmaster');
        $this->db->join('srp_erp_itemcategory category', 'itemmaster.subcategoryID= category.itemCategoryID', 'left');
        //$this->db->join('srp_erp_srm_supplieritems supplierItem', 'supplierItem.itemAutoID = itemmaster.itemAutoID AND  supplierItem.supplierAutoID=' . $supplierID, 'left');
        $this->db->join('(SELECT * FROM srp_erp_srm_supplieritems WHERE `supplierAutoID` = ' . $supplierID . '  ) AS supplierItem', '`supplierItem`.`itemAutoID` = `itemmaster`.`itemAutoID`', 'left');
        $this->db->where($where);
        if (isset($keyword) && !empty($keyword)) {
            $this->db->like('itemmaster.itemDescription', $keyword);
            $this->db->or_like('itemmaster.itemSystemCode', $keyword);
            $this->db->or_like('itemmaster.seconeryItemCode', $keyword);
        }
        $this->db->limit(10);

        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;

        /*out put => json array */
//        echo json_encode($this->Srm_master_model->load_supplier_items_details());


    }


    function delete_supplier_item()
    {
        $supplierItemID = $this->input->post('supplierItemID');
        $this->db->where('supplierItemID', $supplierItemID);
        $results = $this->db->delete('srp_erp_srm_supplieritems');
        if ($results) {
            return array('error' => 0, 'message' => 'Record Deleted Successfully ');

        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact the system team!');
        }
    }


    function delete_supplier()
    {
        $supplierAutoID = trim($this->input->post('supplierID') ?? '');

        $this->db->select('*');
        $this->db->where('supplierAutoID', $supplierAutoID);
        $output = $this->db->get('srp_erp_srm_suppliermaster')->row_array();
        if (empty($output)) {
            $this->db->where('supplierAutoID', $supplierAutoID);
            $results = $this->db->delete('srp_erp_srm_suppliermaster');
            if ($results) {
                return array('s', 'Record Deleted Successfully !');

            } else {
                return array('e', 'Error in Record deleting');
            }
        } else {
            return array('w', 'This supplier has item assigned, please remove all the items before deleting the supplier');

        }

    }

    function delete_customer()
    {
        $CustomerAutoID = trim($this->input->post('customerID') ?? '');
        $this->db->where('CustomerAutoID', $CustomerAutoID);
        $results = $this->db->delete('srp_erp_srm_customermaster');
        if ($results) {
            return array('s', 'Record Deleted Successfully');

        } else {
            return array('e', 'Error while deleting, please contact the system team!');
        }
    }

    function save_customer_order_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $this->load->library('sequence');

        $companyID = $this->common_data['company_data']['company_id'];
        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');
        $cus_order_code = $this->sequence->sequence_generator('SRM-ORD');

        $documentDate = trim($this->input->post('documentDate') ?? '');
        $expiryDate = trim($this->input->post('expiryDate') ?? '');
        $confirmedYN = trim($this->input->post('confirmedYN') ?? '');
        $bid_start_date = trim($this->input->post('bid_start_date') ?? '');
        $bid_end_date = trim($this->input->post('bid_end_date') ?? '');

        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);
        }
        $format_expiryDate = null;
        if (isset($expiryDate) && !empty($expiryDate)) {
            $format_expiryDate = input_format_date($expiryDate, $date_format_policy);
        }
        $format_bid_start_date = null;
        if (isset($bid_start_date) && !empty($bid_start_date)) {
            $format_bid_start_date = input_format_date($bid_start_date, $date_format_policy);
        }
        $format_bid_end_date = null;
        if (isset($bid_end_date) && !empty($bid_end_date)) {
            $format_bid_end_date = input_format_date($bid_end_date, $date_format_policy);
        }
        if (isset($confirmedYN) && $confirmedYN == 1) {
            $where = '';
            if($customerOrderID){
                $where = " AND customerOrderID != " . $customerOrderID;
            }
            $validate_code = $this->db->query("SELECT customerOrderCode AS Code FROM srp_erp_srm_customerordermaster WHERE companyID = {$companyID} AND customerOrderCode LIKE '%{$cus_order_code}%' {$where}")->row('Code');
            if(!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }
            $data["status"] = 1;
            $data["confirmedYN"] = 1;
            $data["confirmedDate"] = $this->common_data['current_date'];
            $data["confirmedByEmpID"] = $this->common_data['current_userID'];
            $data["confirmedByName"] = $this->common_data['current_user'];
        }
        $data["documentID"] = 3;
        $data["contactPersonName"] = $this->input->post('customer_name');
        $data["contactPersonNumber"] = $this->input->post('customerTelephone');
        $data["customerID"] = $this->input->post('customerID');
        $data["CustomerAddress"] = $this->input->post('CustomerAddress1');
        $data["documentDate"] = $format_documentDate;
        $data["expiryDate"] = $format_expiryDate;
        $data["narration"] = $this->input->post('narration');
        $data["referenceNumber"] = $this->input->post('ref_number');
        $data["paymentTerms"] = $this->input->post('payment_term');
        $data["isBackToBack"] = $this->input->post('enable_back');
        //$data["status"] = trim($this->input->post('statusID') ?? '');
        $data["bidStartDate"] = $format_bid_start_date;
        $data["bidEndDate"] = $format_bid_end_date;
        $data["customerReferenceNumber"] = $this->input->post('cus_ref_number');
        $data["supplierID"] = trim($this->input->post('supplierID') ?? '');
        $data["registeredBy"] = $this->input->post('registered_by');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        if ($customerOrderID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('customerOrderID', $customerOrderID);
            $this->db->update('srp_erp_srm_customerordermaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Order Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Order Updated Successfully.');
            }
        } else {
            $data["customerOrderCode"] = $cus_order_code;
            $data["status"] = 1;
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_srm_customerordermaster', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Order Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Order Added Successfully.', $last_id);

            }
        }
    }

    function add_supplier_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $SupplierAutoID = trim($this->input->post('supplierAutoID') ?? '');

        $data['documentAutoID'] = $SupplierAutoID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 1;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_srm_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Supplier Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', 'Supplier Note Saved Successfully.');

        }
    }

    function load_customer_order_autoGeneratedID()
    {
        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');

        $lastID = $this->db->query('SELECT customerOrderCode FROM srp_erp_srm_customerordermaster WHERE customerOrderID = ' . $customerOrderID . '')->row_array();
        $cus_order_code = $lastID['customerOrderCode'];
        return array($cus_order_code);
    }

    function save_customer_ordermaster_add()
    {

        $this->db->trans_start();
        $this->load->library('sequence');
        $companyID = $this->common_data['company_data']['company_id'];

        $cus_order_code = $this->sequence->sequence_generator('SRM-ORD');

        $data["customerOrderCode"] = $cus_order_code;
        $data["status"] = 1;
        $data['companyID'] = $companyID;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_srm_customerordermaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Customer Order Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Customer Order Created Successfully.', $last_id);

        }
    }

    function save_customer_order_detail()
    {
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];
        $customerOrderID = trim($this->input->post('customerOrderID_orderDetail') ?? '');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $expectedDeliveryDate = $this->input->post('expectedDeliveryDate');
        $comment = $this->input->post('comment');

        $this->db->trans_start();

        foreach ($itemAutoIDs as $key => $itemAutoID) {

            $format_expectedDeliveryDate = null;
            if (isset($expectedDeliveryDate[$key]) && !empty($expectedDeliveryDate[$key])) {
                $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
            }
            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);

            $this->db->select('itemAutoID');
            $this->db->from('srp_erp_srm_customerorderdetails');
            $this->db->where('customerOrderID', $customerOrderID);
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('companyID', $companyID);
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Ordered Item already exists.');
                exit();
            }

            $data['customerOrderID'] = $customerOrderID;
            $data['itemAutoID'] = $itemAutoID;
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['requestedQty'] = $quantityRequested[$key];
            $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
            $data['unitAmount'] = ($estimatedAmount[$key]);
            $data['totalAmount'] = ($data['unitAmount'] * $quantityRequested[$key]);
            $data['comment'] = $comment[$key];

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_srm_customerorderdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Customer Order Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Customer Order Details :  Saved Successfully.');
        }

    }

    function load_customerOrder_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate, DATE_FORMAT(bidStartDate,\'' . $convertFormat . '\') AS bidStartDate, DATE_FORMAT(bidEndDate,\'' . $convertFormat . '\') AS bidEndDate,DATE_FORMAT(expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('customerOrderID', $this->input->post('customerOrderID'));
        $data['header'] = $this->db->get('srp_erp_srm_customerordermaster')->row_array();
        return $data;
    }


    function load_customerInquiry_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,DATE_FORMAT(rfqExpDate,\'' . $convertFormat . '\') AS rfqExpDate');
        $this->db->where('inquiryID', $inquiryID);
        $data['header'] = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();

        $this->db->select('customerOrderID');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->group_by('customerOrderID');
        $orderID = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();
        $data['orders'] = array_values($orderID);

        $this->db->select('customerOrderID');
        $this->db->where('inquiryID', $inquiryID);
        $this->db->where('companyID', $companyID);
        $this->db->group_by('customerOrderID');
        $ordersID = $this->db->get('srp_erp_srm_inquiryorders')->result_array();
        $data['ordersdrp'] = array_values($ordersID);

        $this->db->select('itemAutoID');
        $this->db->where('inquiryMasterID', $inquiryID);
        $this->db->where('isChecked', 1);
        $data['orderItem'] = $this->db->get('srp_erp_srm_inquiryitem')->result_array();
        //$data['orderItem'] = array_values($orderItem);
        return $data;
    }

    function delete_customer_order_master()
    {
        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');
        $this->db->delete('srp_erp_srm_customerordermaster', array('customerOrderID' => $customerOrderID));
        $this->db->delete('srp_erp_srm_customerorderdetails', array('customerOrderID' => $customerOrderID));
        return true;
    }

    function delete_customer_inquiry_master()
    {
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $this->db->delete('srp_erp_srm_orderinquirymaster', array('inquiryID' => $inquiryID));
        $this->db->delete('srp_erp_srm_orderinquirydetails', array('inquiryMasterID' => $inquiryID));
        $this->db->delete('srp_erp_srm_inquiryitem', array('inquiryMasterID' => $inquiryID));
        return true;
    }

    function delete_customer_order_detail()
    {
        $customerOrderDetailsID = trim($this->input->post('customerOrderDetailsID') ?? '');
        $this->db->delete('srp_erp_srm_customerorderdetails', array('customerOrderDetailsID' => $customerOrderDetailsID));
        return true;
    }

    function load_customerOrder_BaseItem()
    {
        $this->db->select('srp_erp_srm_customerorderdetails.itemAutoID,srp_erp_itemmaster.itemDescription');
        $this->db->where('customerOrderID', $this->input->post('customerOrderID'));
        $this->db->where('srp_erp_srm_customerorderdetails.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_srm_customerorderdetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_srm_customerorderdetails.itemAutoID = srp_erp_itemmaster.itemAutoID', 'LEFT');
        return $subcat = $this->db->get()->result_array();
    }

    function save_order_inquiry()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $this->load->library('sequence');
        $documentCode = $this->sequence->sequence_generator('SRM-ORD-INQ');

        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $inquiryType = trim($this->input->post('inquiryType') ?? '');
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $purchaseRequestName = $this->input->post('purchaseRequestName');
        $customer_orderID = $this->input->post('customer_orderID');
        $templateType= $this->input->post('templateType');
        $documentDate = trim($this->input->post('documentDate') ?? '');
        $linkExpire = trim($this->input->post('linkExpire') ?? '');
        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);
        }

        if (isset($linkExpire) && !empty($linkExpire)) {
            $format_linkExpire = input_format_date($linkExpire, $date_format_policy);
        }



        $data["customerID"] = trim($this->input->post('customerID') ?? '');
        //$data["customerOrderID"] = trim($this->input->post('customer_orderID') ?? '');
        $data["inquiryType"] = $inquiryType;
        if($inquiryType=='PRQ'){
            $data["purchaseRequestID"] = $purchaseRequestID;
        }
        $data["transactionCurrencyID"] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data["documentDate"] = $format_documentDate;
        $data["rfqExpDate"] = $format_linkExpire;
        $data["narration"] = trim($this->input->post('narration') ?? '');;
        $data["documentCode"] = $documentCode;
        $data["documentID"] = 6;
        $data['templateType']=$templateType;
        $data['purchaseRequestName']=$purchaseRequestName;

        if ($inquiryID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('inquiryID', $inquiryID);
            $this->db->update('srp_erp_srm_orderinquirymaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Order Inquiry Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                if(!empty($customer_orderID)){
                    $this->db->delete('srp_erp_srm_inquiryorders', array('inquiryID' => $inquiryID,'companyID' =>$companyID));
                    foreach ($customer_orderID as $val){
                        $order["inquiryID"] = trim($inquiryID);
                        $order["customerOrderID"] = $val;
                        $order["companyID"] = $companyID;
                        $order['createdUserGroup'] = $this->common_data['user_group'];
                        $order['createdPCID'] = $this->common_data['current_pc'];
                        $order['createdUserID'] = $this->common_data['current_userID'];
                        $order['createdUserName'] = $this->common_data['current_user'];
                        $order['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_srm_inquiryorders', $order);
                    }
                }else{
                    $this->db->delete('srp_erp_srm_inquiryorders', array('inquiryID' => $inquiryID,'companyID' =>$companyID));
                }
                return array('s', 'Customer Order Inquiry Updated Successfully.',$inquiryID);
            }
        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_srm_orderinquirymaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Order Inquiry Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                if(!empty($customer_orderID)){
                    $this->db->delete('srp_erp_srm_inquiryorders', array('inquiryID' => $inquiryID,'companyID' =>$companyID));
                    foreach ($customer_orderID as $val){
                        $order["inquiryID"] = trim($last_id);
                        $order["customerOrderID"] = $val;
                        $order["companyID"] = $companyID;
                        $order['createdUserGroup'] = $this->common_data['user_group'];
                        $order['createdPCID'] = $this->common_data['current_pc'];
                        $order['createdUserID'] = $this->common_data['current_userID'];
                        $order['createdUserName'] = $this->common_data['current_user'];
                        $order['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_srm_inquiryorders', $order);
                    }
                }else{
                    $this->db->delete('srp_erp_srm_inquiryorders', array('inquiryID' => $inquiryID,'companyID' =>$companyID));
                }
                
                $data2['isRfqSelected'] = 1; //1-completed

                $this->db->where('purchaseRequestID', $purchaseRequestID);
                $this->db->update('srp_erp_purchaserequestmaster', $data2);

                return array('s', 'Customer Order Inquiry Added Successfully.', $last_id);
            }
        }
    }

    function load_OrderID_BaseCurrency()
    {
        $this->db->select('transactionCurrencyID');
        $this->db->where('customerOrderID', $this->input->post('customerOrderID'));
        return $this->db->get('srp_erp_srm_customerordermaster')->row_array();
    }

    function save_order_inquiry_itemDetail()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $inquiryType = $this->input->post('inquiryType');
        $itemAutoIDs = $this->input->post('selectedItemsSync');
        $linecomment = $this->input->post('linecomment');
        $pr_doc = $this->input->post('pr_doc');

        $resault = $this->db->delete('srp_erp_srm_inquiryitem', array('inquiryMasterID' => $inquiryID));
        $resault = $this->db->delete('srp_erp_srm_orderinquirydetails', array('inquiryMasterID' => $inquiryID));
       // $resault = $this->db->delete('srp_erp_srm_vendor_rfq_linewise_documents', array('inquiryID' => $inquiryID));

        if($inquiryType=='Customer'){
            if ($resault) {

                foreach ($itemAutoIDs as $key => $itemAutoID) {
                    $autoID = explode('_', $itemAutoID);
                    $suppliers = $this->db->query("SELECT supplierAutoID FROM srp_erp_srm_supplieritems WHERE companyID = " . $companyID . " AND itemAutoID = " . $autoID[0] . "")->result_array();

                    $orderItems = $this->db->query("SELECT itemAutoID FROM srp_erp_srm_customerorderdetails WHERE companyID = " . $companyID . " AND customerOrderID = " . $autoID[1] . "")->result_array();

                    $orderQty = $this->db->query("SELECT requestedQty,expectedDeliveryDate,defaultUOMID FROM srp_erp_srm_customerorderdetails WHERE customerOrderID = " . $autoID[1] . " AND itemAutoID = " . $autoID[0] . "")->row_array();

                    if (!empty($orderItems)) {
                        foreach ($orderItems as $itm) {
                            $data_item["itemAutoID"] = $itm['itemAutoID'];
                            $data_item['inquiryMasterID'] = $inquiryID;
                            $data_item['orderMasterID'] = $autoID[1];
                            $data_item['companyID'] = $companyID;
                            $data_item['createdUserGroup'] = $this->common_data['user_group'];
                            $data_item['createdPCID'] = $this->common_data['current_pc'];
                            $data_item['createdUserID'] = $this->common_data['current_userID'];
                            $data_item['createdUserName'] = $this->common_data['current_user'];
                            $data_item['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_srm_inquiryitem', $data_item);
                        }
                    }

                    if (!empty($suppliers)) {
                        foreach ($suppliers as $val) {
                            $data["inquiryMasterID"] = $inquiryID;
                            $data["itemAutoID"] = $autoID[0];
                            $data["supplierID"] = $val['supplierAutoID'];
                            $data["customerOrderID"] = $autoID[1];
                            $data["defaultUOMID"] = $orderQty['defaultUOMID'];
                            $data["requestedQty"] = $orderQty['requestedQty'];
                            $data["expectedDeliveryDate"] = $orderQty['expectedDeliveryDate'];
                            $data['companyID'] = $companyID;
                            $data['createdUserGroup'] = $this->common_data['user_group'];
                            $data['createdPCID'] = $this->common_data['current_pc'];
                            $data['createdUserID'] = $this->common_data['current_userID'];
                            $data['createdUserName'] = $this->common_data['current_user'];
                            $data['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_srm_orderinquirydetails', $data);
                        }
                    }

                    $data_inquiry['isChecked'] = 1;
                    $this->db->where('itemAutoID', $autoID[0]);
                    $this->db->where('inquiryMasterID', $inquiryID);
                    $this->db->where('orderMasterID', $autoID[1]);
                    $this->db->update('srp_erp_srm_inquiryitem', $data_inquiry);

                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Inquiry Detail Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return true;
                }
            }
            return false;
        }elseif ($inquiryType=='PRQ'){
            if ($resault) {
                foreach ($itemAutoIDs as $key => $itemAutoID) {
                    $autoID = explode('_', $itemAutoID);
                    $itmid = $autoID[0];
                    $purchaseRequestID = $autoID[1];
                    $purchaseRequestDetailsID = $autoID[2];
                    $suppliers = $this->db->query("SELECT supplierAutoID FROM srp_erp_srm_supplieritems WHERE companyID = " . $companyID . " AND itemAutoID = " . $itmid . "")->result_array();
                    $prqdetail = $this->db->query("SELECT * FROM srp_erp_purchaserequestdetails WHERE companyID = " . $companyID . " AND purchaseRequestDetailsID = " . $purchaseRequestDetailsID . "")->row_array();


                    $data_item["itemAutoID"] = $itmid;
                    $data_item['inquiryMasterID'] = $inquiryID;
                    $data_item['orderMasterID'] = $purchaseRequestID;
                    $data_item['purchaseRequestDetailsID'] = $purchaseRequestDetailsID;
                    $data_item['companyID'] = $companyID;
                    $data_item['createdUserGroup'] = $this->common_data['user_group'];
                    $data_item['createdPCID'] = $this->common_data['current_pc'];
                    $data_item['createdUserID'] = $this->common_data['current_userID'];
                    $data_item['createdUserName'] = $this->common_data['current_user'];
                    $data_item['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_srm_inquiryitem', $data_item);

                    $this->db->select('*');
                    $this->db->where('inquiryID', $inquiryID);
                    $this->db->where('companyID', $companyID);
                    $this->db->where('itemAutoID', $itmid);
                    $inqmaster_line_doc = $this->db->get('srp_erp_srm_vendor_rfq_linewise_documents')->row_array();

                    if (!empty($suppliers)) {
                        foreach ($suppliers as $val) {

                            $data["inquiryMasterID"] = $inquiryID;
                            $data["itemAutoID"] = $itmid;
                            $data["lineWiseComment"] = $linecomment[$key];
                            $data["pr_document"] = $pr_doc[$key];
                            if( $inqmaster_line_doc){
                                $data["lineWiseDoc"] = $inqmaster_line_doc['url'];
                            }else{
                                $data["lineWiseDoc"] = null;
                            }
                           
                            $data["supplierID"] = $val['supplierAutoID'];
                            $data["customerOrderID"] = $purchaseRequestID;
                            $data["customerOrderDetailID"] = $purchaseRequestDetailsID;
                            $data["defaultUOMID"] = $prqdetail['defaultUOMID'];
                            $data["requestedQty"] = $prqdetail['requestedQty'];
                            $data["expectedDeliveryDate"] = $prqdetail['expectedDeliveryDate'];
                            $data['companyID'] = $companyID;
                            $data['createdUserGroup'] = $this->common_data['user_group'];
                            $data['createdPCID'] = $this->common_data['current_pc'];
                            $data['createdUserID'] = $this->common_data['current_userID'];
                            $data['createdUserName'] = $this->common_data['current_user'];
                            $data['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_srm_orderinquirydetails', $data);
                        }
                    }

                    $data_inquiry['isChecked'] = 1;
                    $this->db->where('itemAutoID', $itmid);
                    $this->db->where('inquiryMasterID', $inquiryID);
                    $this->db->where('orderMasterID', $purchaseRequestID);
                    $this->db->where('purchaseRequestDetailsID', $purchaseRequestDetailsID);
                    $this->db->update('srp_erp_srm_inquiryitem', $data_inquiry);

                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Inquiry Detail Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return true;
                }
            }
            return false;
        }



    }

    function xeditable_update($tableName, $pkColumn)
    {
        $column = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        switch ($column) {
            case 'DOB_O':
            case 'dateAssumed_O':
            case 'endOfContract_O':
            case 'SLBSeniority_O':
            case 'WSISeniority_O':
            case 'passportExpireDate_O':
            case 'VisaexpireDate_O':
            case 'coverFrom_O':
                $value = format_date_mysql_datetime($value);
                break;
        }
        $table = $tableName;
        $data = array($column => $value);
        $this->db->where($pkColumn, $pk);
        $result = $this->db->update($table, $data);
        echo $this->db->last_query();
        return $result;
    }

    function update_vendor_submit_rfq($data,$doc,$master){
        $this->db->trans_start();
       // $date_format_policy = date_format_policy();
        $date_format_policy = 'Y-m-d';
        $this->db->select('*');
        $this->db->where('inquiryID', $master['inquiryID']);
        $data_inquiry_master = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();

        foreach($data as $key =>$val){
            $FYEnd=null;
            if($val['vendor_delivery_date']!=null){
                $FYEnd = input_format_date($val['vendor_delivery_date'], $date_format_policy);
            }


            $data_detail['isSupplierSubmited'] = 1;
           // $data_detail['SupplierNarration'] =  $val['vendor_description'];
            $data_detail['supplierTechnicalSpecification'] =  $val['vendor_specification'];
            $data_detail['supplierExpectedDeliveryDate'] =  $FYEnd;

            if($val['isSelected'] ==1){
                $data_detail['supplierPrice'] =  $val['vendor_unit_price'];
                $data_detail['supplierQty'] =  $val['vendor_qty'];
            }else{
                $item_a = $this->db->query("SELECT srp_erp_purchaserequestdetails.* from srp_erp_srm_orderinquirymaster JOIN srp_erp_purchaserequestdetails  ON srp_erp_purchaserequestdetails.purchaseRequestID = srp_erp_srm_orderinquirymaster.purchaseRequestID where srp_erp_srm_orderinquirymaster.inquiryID = " . $data_inquiry_master['inquiryID'] . " AND srp_erp_purchaserequestdetails.itemAutoID = " . $val['itemAutoID'] . " AND srp_erp_purchaserequestdetails.purchaseRequestID = " . $data_inquiry_master['purchaseRequestID'] . "")->row_array();
                $data_detail['supplierPrice'] =  $item_a['unitAmount'];
                $data_detail['supplierQty'] =  $item_a['requestedQty'];
            }
           
            $data_detail['supplierDiscount'] =  $val['vendor_discount'];
            $data_detail['supplierTax'] =  $val['vendor_tax'];
            $data_detail['isSelected'] =  $val['isSelected'];
            //$data_detail['supplierOtherCharge'] =  $val['vendor_other_charge'];

            $data_detail['supplierTaxPercentage'] =  $val['supplierTaxPercentage'];
            $data_detail['supplierDiscountPercentage'] =  $val['supplierDiscountPercentage'];

            $this->db->where('inquiryDetailID', $val['srmInquiryDetailID']);
            $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);

        }

        if(count($doc)>0){

            foreach($doc as $key =>$val){ 

                $data1['documentID'] = $val['documentID'];
                $data1['documentSystemCode'] = $val['documentSystemCode'];
                $data1['attachmentDescription'] = $val['attachmentDescription'];
                $data1['documentSubID'] = $val['documentSubID'];
                $data1['myFileName'] = $val['myFileName'];
                $data1['fileType'] = $val['fileType'];
                $data1['fileSize'] = $val['fileSize'];
                $data1['timestamp'] = $val['timestamp'];
                $data1['companyID'] = $val['companyID'];
              
                $this->db->insert('srp_erp_documentattachments', $data1);
    
            }

        }

        $data_Sub1['companyID'] = $master['companyID'];
        $data_Sub1['inquiryID'] = $master['inquiryID'];
        $data_Sub1['supplierID'] = $master['supplierID'];
        $data_Sub1['terms'] = $master['terms'];

        $this->db->insert('srp_erp_vendor_terms', $data_Sub1);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Inquiry Detail Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            
            return true;
        }
    }

    function update_vendor_change_details(){
        $this->db->trans_start();
        $id = $this->input->post('id');
        $column = $this->input->post('col');
        $type = $this->input->post('type'); // 1-approve 2- reject
        $value = $this->input->post('value');

        $this->db->select('*');
        $this->db->where('reqResID', $id);
        $output = $this->db->get('srp_erp_srm_vendor_request_responces')->row_array();

        if($type==1){

            $data_detail[$column] = $value;
            $this->db->where('inquiryDetailID', $output['inquiryDetailID']);
            $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);

            if($column=="supplierPrice"){
                $data_detail1['unitPriceApproveYN'] = 1;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }
            if($column=="supplierQty"){
                $data_detail1['qtyApproveYN'] = 1;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }
            if($column=="supplierExpectedDeliveryDate"){
                $data_detail1['dateApproveYN'] = 1;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }

           // $data_detail['isVendorSubmited'] = 1;
           $this->db->select('*');
           $this->db->where('reqResID', $id);
           $this->db->where('unitPriceApproveYN', 1);
           $this->db->where('qtyApproveYN', 1);
           $this->db->where('dateApproveYN', 1);
           $output_approved = $this->db->get('srp_erp_srm_vendor_request_responces')->row_array();

           if($output_approved){
                $data_detail_x['isVendorSubmited'] = 2;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail_x);
           }

            

            $master_n = [
                "reqResID"=>$id,
                "inquiryDetailID"=> $output['inquiryDetailID'],
                "inquiryMasterID"=>$output['inquiryMasterID'],
                "supplierID"=>$output['supplierID'],
                "type"=> $type,
                "field"=>$column,
                "value"=>$value,
            ];     

            $res=$this->sendVenderChangesApprovelStatusAPI($master_n);

        }else{
            if($column=="supplierPrice"){
                $data_detail1['unitPriceApproveYN'] = 2;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }
            if($column=="supplierQty"){
                $data_detail1['qtyApproveYN'] = 2;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }
            if($column=="supplierExpectedDeliveryDate"){
                $data_detail1['dateApproveYN'] = 2;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail1);
            }

            $this->db->select('*');
           $this->db->where('reqResID', $id);
           $this->db->where('unitPriceApproveYN', 2);
           $this->db->where('qtyApproveYN', 2);
           $this->db->where('dateApproveYN', 2);
           $output_approved = $this->db->get('srp_erp_srm_vendor_request_responces')->row_array();

           if($output_approved){
                $data_detail_r['isVendorSubmited'] = 2;
                $this->db->where('reqResID', $id);
                $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail_r);
           }

            $master_n = [
                "reqResID"=>$id,
                "inquiryDetailID"=> $output['inquiryDetailID'],
                "inquiryMasterID"=>$output['inquiryMasterID'],
                "supplierID"=>$output['supplierID'],
                "type"=> $type,
                "field"=>$column,
                "value"=>$value,
            ];

            $res=$this->sendVenderChangesApprovelStatusAPI($master_n); 
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Price Change Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            
            return array('s', 'Price Change :  Saved Successfully.');
        }
    }


    public function sendVenderChangesApprovelStatusAPI($master){
        $curl = curl_init();
        //http://localhost:8000/ http://vendorbe.rbdemo.live

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/api/vendor/update-rfq-change-request-status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodysendStatusChange($master),
        CURLOPT_HTTPHEADER => array(
            // "Authorization: Bearer $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodysendStatusChange($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    function update_supplier_rfq_change_request($val,$date){
       // $date_format_policy = date_format_policy();
        $date_format_policy = 'Y-m-d';
        
        $this->db->trans_start();
        $FYEnd=null;
        if($val['date']!=null){
            $FYEnd = input_format_date($val['date'], $date_format_policy);
        }

        $this->db->select('*');
        $this->db->from('srp_erp_srm_vendor_request_responces');
        $this->db->where('reqResID', $val['reqResID']);
        $data_master = $this->db->get()->row_array();

        if($data_master['referType']=='General'){
            $data_detail['isVendorSubmited'] = 2;
            $data_detail['vendorComment'] =  $val['comment'];
            $data_detail['receivedDate'] =  $date;
    
            $this->db->where('reqResID', $val['reqResID']);
            $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail);

        }else{
            $data_detail['isVendorSubmited'] = 1;
            $data_detail['vendorComment'] =  $val['comment'];
            $data_detail['vendorPrice'] =  $val['price'];
            $data_detail['vendorQty'] =  $val['qty'];
            $data_detail['vendorNewDeliveryDate'] =  $FYEnd;
            $data_detail['receivedDate'] =  $date;
    
            $this->db->where('reqResID', $val['reqResID']);
            $this->db->update('srp_erp_srm_vendor_request_responces', $data_detail);
        }

        

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Inquiry Detail Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            
            return true;
        }
    }

    function save_vendor_company_request_details($val,$val1,$cat,$other_data,$type){
        $date_format_policy = 'y-m-d';

        $dataMaster =$val;
        $dataSub =$val1;

        $other = $other_data['other'];
        $family = $other_data['family'];
        
        $this->db->trans_start();
        $licenseExpireDate=null;

        if($dataMaster['licenseExpireDate']!=null){
            $licenseExpireDate = input_format_date($dataMaster['licenseExpireDate'], $date_format_policy);
        }

        $vatExpire=null;

        if($dataMaster['vatExpire']!=null){
            $vatExpire = input_format_date($dataMaster['vatExpire'], $date_format_policy);
        }

        $data_detail['companyID'] = $dataMaster['companyID'];
        $data_detail['portalReqID'] = $dataMaster['id'];
        $data_detail['providerName'] = $dataMaster['providerName'];
        $data_detail['companyRegNo'] = $dataMaster['companyRegNo'];
        $data_detail['companyUrl'] = $dataMaster['companyUrl'];

        $data_detail['licenseExpireDate'] = $licenseExpireDate;

        $data_detail['yearofEstablishment'] = $dataMaster['yearofEstablishment'];
        $data_detail['natureofBusiness'] = $dataMaster['natureofBusiness'];
        $data_detail['groupCompany'] = $dataMaster['groupCompany'];
        $data_detail['sponsorName'] = $dataMaster['sponsorName'];
        $data_detail['numberofYearBusiness'] = $dataMaster['numberofYearBusiness'];
        $data_detail['numberofBranch'] = $dataMaster['numberofBranch'];
        $data_detail['vatNumber'] = $dataMaster['vatNumber'];

        $data_detail['vatExpire'] = $vatExpire;

        $data_detail['brands'] = $dataMaster['brands'];
        $data_detail['address1'] = $dataMaster['address1'];
        $data_detail['address2'] = $dataMaster['address2'];
        $data_detail['country'] = $dataMaster['country'];
        $data_detail['state'] = $dataMaster['state'];
        $data_detail['city'] = $dataMaster['city'];
        $data_detail['pincode'] = $dataMaster['pincode'];
        $data_detail['companyPhone'] = $dataMaster['companyPhone'];
        $data_detail['companyfax'] = $dataMaster['companyfax'];
        $data_detail['contactName'] = $dataMaster['contactName'];
        $data_detail['contactPersonEmail'] = $dataMaster['contactPersonEmail'];
        $data_detail['pointContactphone'] = $dataMaster['pointContactphone'];
        $data_detail['pointofContactRole'] = $dataMaster['pointofContactRole'];

        $data_detail['sponserCountry'] = $dataMaster['sponserCountry'];
        $data_detail['holocation'] = $dataMaster['holocation'];

        $data_detail['declaration'] = $dataMaster['declaration'];
        $data_detail['agree1'] = $dataMaster['agree1'];
        $data_detail['agree2'] = $dataMaster['agree2'];
        $data_detail['haveCetification'] = $dataMaster['haveCetification'];

        $data_detail['isPdo'] = $dataMaster['isPdo'];
        $data_detail['isdcrp'] = $dataMaster['isdcrp'];
        $data_detail['certification'] = $dataMaster['certification'];
        $data_detail['apiKey'] = $dataMaster['apiKey'];

        if($type==4){
            $this->db->select('*');
            $this->db->from('srp_erp_srm_vendor_company_requests');
            $this->db->where('portalReqID', $dataMaster['id']);
            $this->db->where('companyID', $dataMaster['companyID']);
            $data_master = $this->db->get()->row_array();

            if($data_master){
                $data_detail['approveYN'] = 4;

                $this->db->where('companyReqID', $data_master['companyReqID']);
                $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);
                //$last_id = $this->db->insert_id();

                $this->db->where('companyReqMasterID', $data_master['companyReqID']);
                $this->db->delete('srp_erp_srm_vendor_company_request_documents');

                $this->db->where('companyReqMasterID', $data_master['companyReqID']);
                $this->db->delete('srp_erp_vendor_subcategories');

                $this->db->where('companyReqMasterID', $data_master['companyReqID']);
                $this->db->delete('srp_erp_vendor_other_document');

                $this->db->where('requestID', $data_master['companyReqID']);
                $this->db->delete('srp_erp_vendor_register_family');

                $last_id = $data_master['companyReqID'];

                if($dataSub){
                    foreach($dataSub as $val2){
                        $data_detail_doc['documentName'] = $val2['documentName'];
                        $data_detail_doc['url'] = $val2['url'];
                        $data_detail_doc['companyID'] = $val2['companyID'];
                        $data_detail_doc['companyReqMasterID'] = $last_id;
                        $data_detail_doc['documentBackendID'] = $val2['id'];
    
                        $this->db->insert('srp_erp_srm_vendor_company_request_documents', $data_detail_doc);
                    }
                }
    
                if($cat){
                    foreach($cat as $val2){
                        $data_detail_doc_cat['description'] = $val2['description'];
                        $data_detail_doc_cat['category'] = $val2['category'];
                        $data_detail_doc_cat['status'] = $val2['status'];
                        $data_detail_doc_cat['companyReqMasterID'] = $last_id;
                        $data_detail_doc_cat['backendID'] = $val2['subID'];
    
                        $this->db->insert('srp_erp_vendor_subcategories', $data_detail_doc_cat);
                    }
                }
    
                if($other){
                    foreach($other as $val2){
                        $data_detail_doc1['name'] = $val2['name'];
                        $data_detail_doc1['url'] = $val2['url'];
                        $data_detail_doc1['status'] = $val2['status'];
                        $data_detail_doc1['companyReqMasterID'] = $last_id;
                        $data_detail_doc1['requestID'] = $val2['id'];
    
                        $this->db->insert('srp_erp_vendor_other_document', $data_detail_doc1);
                    }
                }
    
                if($family){
                    foreach($family as $val2){
                        $family_v['requestID'] = $last_id;
                        $family_v['relationship'] = $val2['relationship'];
                        $family_v['empID'] = $val2['empID'];
                        $family_v['companyID'] = $val2['companyID'];
                        $family_v['designationID'] = $val2['designationID'];
                        $family_v['designationName'] = $val2['designationName'];
                        $family_v['ename'] = $val2['ename'];
                        $family_v['status'] = 1;
                        //$family_v['eCode'] = $val2['eCode'];
    
                        $family_v['backendID'] = $val2['requestID'];
    
    
                        $this->db->insert('srp_erp_vendor_register_family', $family_v);
                    }
                }

            }

        }else{

           
            // $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            // $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
            // $data_detail['modifiedUserName'] = $this->common_data['current_user'];
            // $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
        
            $this->db->insert('srp_erp_srm_vendor_company_requests', $data_detail);
            $last_id = $this->db->insert_id();
        
            if($dataSub){
                foreach($dataSub as $val2){
                    $data_detail_doc['documentName'] = $val2['documentName'];
                    $data_detail_doc['url'] = $val2['url'];
                    $data_detail_doc['companyID'] = $val2['companyID'];
                    $data_detail_doc['companyReqMasterID'] = $last_id;
                    $data_detail_doc['documentBackendID'] = $val2['id'];

                    $this->db->insert('srp_erp_srm_vendor_company_request_documents', $data_detail_doc);
                }
            }

            if($cat){
                foreach($cat as $val2){
                    $data_detail_doc_cat['description'] = $val2['description'];
                    $data_detail_doc_cat['category'] = $val2['category'];
                    $data_detail_doc_cat['status'] = $val2['status'];
                    $data_detail_doc_cat['companyReqMasterID'] = $last_id;
                    $data_detail_doc_cat['backendID'] = $val2['subID'];

                    $this->db->insert('srp_erp_vendor_subcategories', $data_detail_doc_cat);
                }
            }

            if($other){
                foreach($other as $val2){
                    $data_detail_doc1['name'] = $val2['name'];
                    $data_detail_doc1['url'] = $val2['url'];
                    $data_detail_doc1['status'] = $val2['status'];
                    $data_detail_doc1['companyReqMasterID'] = $last_id;
                    $data_detail_doc1['requestID'] = $val2['id'];

                    $this->db->insert('srp_erp_vendor_other_document', $data_detail_doc1);
                }
            }

            if($family){
                foreach($family as $val2){
                    $family_v['requestID'] = $last_id;
                    $family_v['relationship'] = $val2['relationship'];
                    $family_v['empID'] = $val2['empID'];
                    $family_v['companyID'] = $val2['companyID'];
                    $family_v['designationID'] = $val2['designationID'];
                    $family_v['designationName'] = $val2['designationName'];
                    $family_v['ename'] = $val2['ename'];
                    $family_v['status'] = 1;
                    //$family_v['eCode'] = $val2['eCode'];

                    $family_v['backendID'] = $val2['requestID'];


                    $this->db->insert('srp_erp_vendor_register_family', $family_v);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return true;
        }
    }

    function save_vendor_company_request_reject_document($val,$date){
        $date_format_policy = date_format_policy();

        $dataMaster =$val;
        
        $this->db->trans_start();

        $this->db->select('*');
        $this->db->where('documentBackendID', $dataMaster['requestID']);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_request_documents')->row_array();

        $data_detail['reqDocMasterID'] = $data_master['reqDocID'];
        $data_detail['documentName'] = $data_master['documentName'];
        $data_detail['url'] = $data_master['url'];
        $data_detail['companyID'] = $data_master['companyID'];
        $data_detail['documentBackendID'] = $data_master['documentBackendID'];
        $data_detail['companyReqMasterID'] = $data_master['companyReqMasterID'];
       
        $this->db->insert('srp_erp_srm_company_request_document_backup', $data_detail);
        $last_id = $this->db->insert_id();

        $data_detail1['url'] = $dataMaster['url'];
        $data_detail1['approveYN'] = 0;

        $this->db->where('documentBackendID', $dataMaster['requestID']);
        $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail1);
      
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return true;
        }
    }

    function find_company_details_for_vendor_portal($val){
        $date_format_policy = date_format_policy();

        $dataMaster =$val;
        
        $this->db->trans_start();

        $this->db->select('*');
        $this->db->where('company_id', $dataMaster['companyID']);
        $data_master=$this->db->get('srp_erp_company')->row_array();

      
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return $data_master;
        }
    }

    function save_company_request_approve(){

        $this->db->trans_start();

        $requestMasterID = $this->input->post('requestMasterID');
        $supplier = trim($this->input->post('sup') ?? '');
        $comment = trim($this->input->post('comments') ?? '');

        $this->db->select('*');
        $this->db->where('companyReqID', $requestMasterID);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_requests')->row_array();

        $data_detail['approveYN'] = 1;
        $data_detail['approveComment'] = $comment;
        $data_detail['erpSupplierID'] = $supplier;

        $this->db->where('companyReqID', $requestMasterID);
        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);

        $master_n = [
            "reqMasterID"=> $data_master['portalReqID'],
            "vendorErpID"=> $supplier,
        
        ];

        // $res=$this->updateApproveSupplierDetails($master_n);

        // //print_r($res);exit;

        // $res_array=json_decode($res);

        // if($res_array->action_status==true){
        //     $data_detail1['submitErpIDYN'] = 1;

        //     $this->db->where('companyReqID', $requestMasterID);
        //     $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);

        // }

        $token = $this->getLoginToken();

        $token_array=json_decode($token);

        if($token_array){

            if($token_array->success==true){
            
                $res=$this->updateApproveSupplierDetails($master_n,$token_array->data->token);

                $res_array=json_decode($res);
                if($res_array->status==true){
                    $data_detail1['submitErpIDYN'] = 1;

            $this->db->where('companyReqID', $requestMasterID);
            $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }


    function vendor_company_request_document_approve(){

        $this->db->trans_start();

        $requestID = $this->input->post('requestID');
       // $comment = trim($this->input->post('comments') ?? '');

        $this->db->select('*');
        $this->db->where('reqDocID', $requestID);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_request_documents')->row_array();

        $data_detail['approveYN'] = 1;

        $this->db->where('reqDocID', $requestID);
        $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail);

        $master_n = [
            "documentID"=> $data_master['documentBackendID'],
            "type"=> 1,
            "comment"=>""
        
        ];

        $res=$this->updateApproveDocumentDetails($master_n);

        //print_r($res);exit;

        $res_array=json_decode($res);

        if($res_array->action_status==true){
            $data_detail1['submitApprovalYN'] = 1;

            $this->db->where('reqDocID', $requestID);
            $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail1);

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function vendor_company_request_document_reject(){

        $this->db->trans_start();
        $requestID = $this->input->post('requestDocID');
        $comment = trim($this->input->post('comments') ?? '');
        $typeReq = trim($this->input->post('typeReq') ?? '');
        $this->db->select('*');
        $this->db->where('reqDocID', $requestID);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_request_documents')->row_array();

        $this->db->select('*');
        $this->db->where('companyReqID', $data_master['companyReqMasterID']);
        $data_req=$this->db->get('srp_erp_srm_vendor_company_requests')->row_array();

        if($typeReq==1){
            $data_detail['approveYN'] = 1;
        }else{
            $data_detail['approveYN'] = 2;
        }

        $data_detail['comment'] = $comment;

        $this->db->where('reqDocID', $requestID);
        $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail);

        $master_n = [
            "documentID"=> $data_master['documentBackendID'],
            "type"=> $typeReq,
            "comment"=>$comment,
            "backendID"=>$requestID
        
        ];

        $token = $this->getLoginToken();

        $token_array=json_decode($token);

        if($token_array){

            if($token_array->success==true){
               
                $res=$this->updateApproveDocumentDetails($master_n,$token_array->data->token);

               $res_array=json_decode($res);
               //print_r($res);exit;
               if($res_array->status==true){
                $data_detail1['submitApprovalYN'] = 1;

                $this->db->where('reqDocID', $requestID);
                $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail1);

                if($typeReq==2){
                    //$this->send_supplier_company_request_status($data_req['contactPersonEmail'],1);
                }

               }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function vendor_company_request_document_reject_approve(){

        $this->db->trans_start();

        $requestID = $this->input->post('requestDocID_approve');
        $comment = trim($this->input->post('comments_approve') ?? '');
        $typeReq = trim($this->input->post('typeReq_approve') ?? '');
        
        $data_detail['isApprovalLevelReferBack'] = 1;

        $data_detail['comment'] = $comment;

        $this->db->where('reqDocID', $requestID);
        $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail);

        // $master_n = [
        //     "documentID"=> $data_master['documentBackendID'],
        //     "type"=> 2,
        //     "comment"=>$comment,
        //     "backendID"=>$requestID
        
        // ];

        // $token = $this->getLoginToken();

        // $token_array=json_decode($token);

        // //print_r($token_array->success);exit;

        // if($token_array){

        //     if($token_array->success==true){
               
        //         $res=$this->updateApproveDocumentDetails($master_n,$token_array->data->token);

        //        $res_array=json_decode($res);
        //        //print_r($res);exit;
        //         if($res_array->status==true){
        //             $data_detail1['submitApprovalYN'] = 1;

        //             $this->db->where('reqDocID', $requestID);
        //             $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail1);

        //         }
        //     }
        // }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function vendor_company_request_reject(){

        $this->db->trans_start();

        $requestID = $this->input->post('requestDocID_rej');
        $comment = trim($this->input->post('comments_rej') ?? '');
        $typeReq = trim($this->input->post('typeReq_rej') ?? '');
        

        $this->db->select('*');
        $this->db->where('companyReqID', $requestID);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_requests')->row_array();

        //0-pending / 1-approve /2-refer /3 reject

        if($typeReq==1){
            $data_detail['approveYN'] = 3;
        }else{
            $data_detail['approveYN'] = 2;
        }

        $data_detail['comment'] = $comment;

        $this->db->where('companyReqID', $requestID);
        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);

        $master_n = [
            "documentID"=> $data_master['portalReqID'],
            "type"=> $data_detail['approveYN'],
            "comment"=>$comment
        
        ];

        $token = $this->getLoginToken();

        $token_array=json_decode($token);

        //print_r($token_array->success);exit;

        if($token_array){

            if($token_array->success==true){
               
                $res=$this->updateRejectCompanyRequestDetails($master_n,$token_array->data->token);

               $res_array=json_decode($res);
              // print_r($res);exit;
               if($res_array->status==true){

                //$this->send_supplier_company_request_status($data_master['contactPersonEmail'],$data_detail['approveYN']);
                $data_detail1['submitRejectYN'] = 1;

                $this->db->where('companyReqID', $requestID);
                $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);

               }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    public function updateApproveDocumentDetails($master,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/save_document_status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodyupdateApproveDocumentDetails($master),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodyupdateApproveDocumentDetails($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    public function updateRejectCompanyRequestDetails($master,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/save_company_request_status',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodyupdateRejectCompanyRequestDetails($master),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodyupdateRejectCompanyRequestDetails($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    public function updateApproveSupplierDetails($master,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/send_company_request_confirmation',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodyupdateApproveSupplierDetails($master),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodyupdateApproveSupplierDetails($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    function load_vendor_change_details()
    {
        $this->db->select('*');
        $this->db->where('reqResID', $this->input->post('id'));
        return $this->db->get('srp_erp_srm_vendor_request_responces')->row_array();
    }

    function fetch_confirm_company_request_info()
    {
        $this->db->select('*');
        $this->db->where('companyReqID', $this->input->post('requestMasterID'));
        return $this->db->get('srp_erp_srm_vendor_company_requests')->row_array();
    }


    function save_request_refer()
    {
        $referType = $this->input->post('referType');
        $comment = $this->input->post('comment');

        $inquiryDetailID = $this->input->post('inquiryDetailID');
        $inquiryMasterID = $this->input->post('inquiryMasterID');
        $supplierID = $this->input->post('supplierID');
        $itemAutoID = $this->input->post('itemAutoID');

        //$companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $data_arr = array(
            'inquiryDetailID' =>  $inquiryDetailID,
            'inquiryMasterID' => $inquiryMasterID,
            'referType' => $referType,
            'comment' => $comment,
            'supplierID' => $supplierID,
            'itemAutoID' => $itemAutoID,
            'createdDatetime' => $this->common_data['current_date'],
            'isSubmit' => 0,
            'companyID' => $this->common_data['company_data']['company_id'],
        );

        $this->db->insert('srp_erp_srm_vendor_request_responces', $data_arr);

        $refer_id = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Refer : Save Failed ' . $this->db->_error_message());
        } else {

            $master_n = [
                "reqResID"=>$refer_id,
                "inquiryDetailID"=> $inquiryDetailID,
                "inquiryMasterID"=> $inquiryMasterID,
                "itemAutoID"=>$itemAutoID,
                "referType"=> $referType,
                "comment"=> $comment,
                "supplierID"=> $supplierID,
                // "referenceID"=>$vendor_ref,
                'companyID' => $this->common_data['company_data']['company_id'],
            
            ];

            $this->saveChangeRequestAPI($master_n);
            $this->send_rfq_change_request_message_email_suppliers($inquiryMasterID,$supplierID);

            $this->db->trans_commit();
            return array('error' => 0, 's', 'Refer Details : Saved and Email Send Successfully.');
        }


    }

    function save_request_refer_supplier_template()
    {
        $referType = $this->input->post('referTypeSupplier');
        $comment = $this->input->post('commentSupplier');

      //  $inquiryDetailID = $this->input->post('inquiryDetailID');
        $inquiryMasterID = $this->input->post('inquiryMasterID');
        $supplierID = $this->input->post('supplierID');
        $itemAutoID = $this->input->post('itemAutoID');

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->where('inquiryMasterID',$inquiryMasterID);
        $this->db->where('itemAutoID',$itemAutoID);
        $this->db->where('supplierID',$supplierID);
        $inq = $this->db->get()->row_array();

        $inquiryDetailID =$inq['inquiryDetailID'];

        //$companyLocalCurrencyID = $this->common_data['company_data']['company_default_currencyID'];

        $data_arr = array(
            'inquiryDetailID' =>  $inquiryDetailID,
            'inquiryMasterID' => $inquiryMasterID,
            'referType' => $referType,
            'comment' => $comment,
            'supplierID' => $supplierID,
            'itemAutoID' => $itemAutoID,
            'createdDatetime' => $this->common_data['current_date'],
            'isSubmit' => 0,
            'companyID' => $this->common_data['company_data']['company_id'],
        );

        $this->db->insert('srp_erp_srm_vendor_request_responces', $data_arr);

        $refer_id = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Refer : Save Failed ' . $this->db->_error_message());
        } else {

            $master_n = [
                "reqResID"=>$refer_id,
                "inquiryDetailID"=> $inquiryDetailID,
                "inquiryMasterID"=> $inquiryMasterID,
                "itemAutoID"=>$itemAutoID,
                "referType"=> $referType,
                "comment"=> $comment,
                "supplierID"=> $supplierID,
                // "referenceID"=>$vendor_ref,
                'companyID' => $this->common_data['company_data']['company_id'],
            
            ];

            $this->saveChangeRequestAPI($master_n);
            $this->send_rfq_change_request_message_email_suppliers($inquiryMasterID,$supplierID);

            $this->db->trans_commit();
            return array('error' => 0, 's', 'Refer Details : Saved and Email Send Successfully.');
        }


    }

    function save_company_request_basic_info()
    {

        $seconeryItemCode = $this->input->post('seconeryItemCode');
        $requestMasterID = $this->input->post('requestMasterID');

        $data_detail['secondaryCode'] = $seconeryItemCode;

        $this->db->where('companyReqID', $requestMasterID);
        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);



        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Detais : Save Failed ' . $this->db->_error_message());
        } else {

            $this->db->trans_commit();
            return array('error' => 0, 's', 'Details : Update Successfully.');
        }

    }

    
    function save_company_request_financial_info()
    {

        $nameOnCheque = $this->input->post('nameOnCheque');
        $partyCategoryID = $this->input->post('partyCategoryID');
        $liabilityAccount = $this->input->post('liabilityAccount');
        $supplierCurrency = $this->input->post('supplierCurrency');
        $suppliertaxgroup = $this->input->post('suppliertaxgroup');
        $supplierCreditPeriod = $this->input->post('supplierCreditPeriod');
        $supplierCreditLimit = $this->input->post('supplierCreditLimit');
        $vatEligible = $this->input->post('vatEligible');
        $vatPercentage = $this->input->post('vatPercentage');
        $requestMasterID = $this->input->post('requestMasterID');

        $data_detail['nameOfCheque'] = $nameOnCheque;
        $data_detail['category'] = $partyCategoryID;
        $data_detail['liabilityAccount'] = $liabilityAccount;
        $data_detail['currency'] = $supplierCurrency;
        $data_detail['taxGroup'] = $suppliertaxgroup;
        $data_detail['creditPeriod'] = $supplierCreditPeriod;
        $data_detail['creditLimit'] = $supplierCreditLimit;
        $data_detail['vatElifible'] = $vatEligible;
        $data_detail['vatPercentage'] = $vatPercentage;

        $this->db->where('companyReqID', $requestMasterID);
        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'e', 'Refer : Save Failed ' . $this->db->_error_message());
        } else {

            $this->db->trans_commit();
            return array('error' => 0, 's', 'Refer Details : Update Successfully.');
        }

    }

    function company_request_confirm_info()
    {

        $existSupplierVal = $this->input->post('existSupplierVal');
        $addSupplierVal = $this->input->post('addSupplierVal');
        $ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');

        $supplier = $this->input->post('sup');

        $companyid_supplier = current_companyID();
       
        $requestMasterID = $this->input->post('requestMasterID');

        $this->db->select('*');
        $this->db->where('companyReqID', $requestMasterID);
        $data_master=$this->db->get('srp_erp_srm_vendor_company_requests')->row_array();

        // $this->db->select("*");
        // $this->db->from('srp_erp_srm_suppliermaster');
        // $this->db->where('supplierAutoID', $supplier);
        // $supplier_data = $this->db->get()->row_array();
       // print_r($data_master);exit;
        $this->db->select("*");
        $this->db->from('srp_erp_countrymaster');
        $this->db->where('CountryDes', $data_master['country']);
        $cntry = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('companyReqID', $requestMasterID);
        $this->db->where('erpSupplierID !=', null);
        $this->db->from('srp_erp_srm_vendor_company_requests');
        $already_registerd1 = $this->db->get()->row_array();

        if($already_registerd1){
            return array('error' => 1, 'e', 'Supplier Already Exist');
        }else{

            if($existSupplierVal==1){
                    $systemComment = $this->input->post('system_comment');
                    $data_detail['approveYN'] = 1;
                    $data_detail['erpSupplierID'] = $supplier;
                    $data_detail['accountType'] = 1;
                    $data_detail['systemComment'] = $systemComment;
                
                    $this->db->where('companyReqID', $requestMasterID);
                    $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);


                    $data_sup['companyRequestMasterID']=$requestMasterID;
                    $data_sup['isSupplierAcc']=1;
                    $this->db->where('supplierAutoID', $supplier);
                    $this->db->update('srp_erp_srm_suppliermaster', $data_sup);

                    $this->db->select('*');
                    $this->db->where('supplierAutoID', $supplier );
                    $sup_check = $this->db->get('srp_erp_srm_suppliermaster')->row_array();

                    $this->db->select('*');
                    $this->db->where('supplierAutoID', $sup_check['erpSupplierAutoID'] );
                    $new_sup_master = $this->db->get('srp_erp_suppliermaster')->row_array();


                    $data_api['apiKey'] = $data_master['apiKey'];
                    $this->db->where('supplierAutoID', $sup_check['erpSupplierAutoID'] );
                    $this->db->update('srp_erp_suppliermaster', $data_api);

                    $master_n = [
                        "reqMasterID"=> $data_master['portalReqID'],
                        "vendorErpID"=> $supplier,
                        "vendorErpMasterID"=> $sup_check['erpSupplierAutoID'],
                        "supplierDetails"=>$new_sup_master,
                        "systemComment"=>$systemComment
                    
                    ];

                    $where1 = "itemmaster.companyID = $companyid_supplier AND (itemmaster.financeCategory = 1 OR itemmaster.financeCategory = 2)";
                     $this->db->select('itemmaster.*,`category`.`Description` as catDescription');
                     $this->db->from('srp_erp_itemmaster itemmaster');
                     $this->db->join('srp_erp_itemcategory category', 'itemmaster.subcategoryID= category.itemCategoryID', 'left');
                     //$this->db->join('srp_erp_srm_supplieritems supplierItem', 'supplierItem.itemAutoID = itemmaster.itemAutoID AND  supplierItem.supplierAutoID=' . $supplierID, 'left');
                     //$this->db->join('(SELECT * FROM srp_erp_srm_supplieritems WHERE `supplierAutoID` = ' . $last_id . '  ) AS supplierItem', '`supplierItem`.`itemAutoID` = `itemmaster`.`itemAutoID`', 'left');
                     $this->db->where($where1);
             
                     $result_sup_items_new = $this->db->get()->result_array();
 
                     if(!empty($result_sup_items_new)){
                         foreach($result_sup_items_new as $val){
 
                             $this->db->select('*');
                             $this->db->where('itemAutoID', $val['itemAutoID']);
                             $this->db->where('supplierAutoID', $supplier );
                             $output123 = $this->db->get('srp_erp_srm_supplieritems')->row_array();
 
                             if (empty($output123)) {
                                // $this->db->trans_start();
                                 $data_sup_tems1['supplierAutoID'] = $supplier;
                                 $data_sup_tems1['itemAutoID'] =$val['itemAutoID'];
                                 $data_sup_tems1['companyID'] = current_companyID();
                                 $data_sup_tems1['createdPCID'] = $this->common_data['current_pc'];
                                 $data_sup_tems1['createdUserID'] = $this->common_data['current_userID'];
                                 $data_sup_tems1['createdUserName'] = $this->common_data['current_user'];
                                 $data_sup_tems1['createdDateTime'] = $this->common_data['current_date'];
                                 $data_sup_tems1['createdUserGroup'] = $this->common_data['user_group'];
                                 $data_sup_tems1['timestamp'] = format_date_mysql_datetime();
                                 $this->db->insert('srp_erp_srm_supplieritems', $data_sup_tems1);
 
                             }
 
                         }
                     }
            
                    // $res=$this->updateApproveSupplierDetails($master_n);
            
                    // //print_r($res);exit;
            
                    // $res_array=json_decode($res);
            
                    // if($res_array->action_status==true){
                    //     $data_detail1['submitErpIDYN'] = 1;
            
                    //     $this->db->where('companyReqID', $requestMasterID);
                    //     $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);
                    //     $this->send_company_approve_email_supplier($supplier,1);
                    // }

                    $token = $this->getLoginToken();

                    $token_array=json_decode($token);

                    if($token_array){

                        if($token_array->success==true){
                        
                            $res=$this->updateApproveSupplierDetails($master_n,$token_array->data->token);

                            $res_array=json_decode($res);
                            if($res_array->status==true){
                                $data_detail1['submitErpIDYN'] = 1;
                
                                $this->db->where('companyReqID', $requestMasterID);
                                $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);
                               // $this->send_company_approve_email_supplier($supplier,1);
            
                            }
                        }
                    }
                

            }else{

                $nameOnCheque = $this->input->post('nameOnCheque');
                $partyCategoryID = $this->input->post('partyCategoryID');
                $liabilityAccount = $this->input->post('liabilityAccount');
                $supplierCurrency = $this->input->post('supplierCurrency');
                $suppliertaxgroup = $this->input->post('suppliertaxgroup');
                $supplierCreditPeriod = $this->input->post('supplierCreditPeriod');
                $supplierCreditLimit = $this->input->post('supplierCreditLimit');
                $vatEligible = $this->input->post('vatEligible');
                $vatPercentage = $this->input->post('vatPercentage');
                $requestMasterID = $this->input->post('requestMasterID');
                $add_comment = $this->input->post('add_comment');

                $seconeryItemCode = $this->input->post('seconeryItemCode');
        
                $data_detail['secondaryCode'] = $seconeryItemCode;
                $data_detail['nameOfCheque'] = $nameOnCheque;
                $data_detail['category'] = $partyCategoryID;
                $data_detail['liabilityAccount'] = $liabilityAccount;
                $data_detail['currency'] = $supplierCurrency;
                $data_detail['taxGroup'] = $suppliertaxgroup;
                $data_detail['creditPeriod'] = $supplierCreditPeriod;
                $data_detail['creditLimit'] = $supplierCreditLimit;
                $data_detail['vatElifible'] = $vatEligible;
                $data_detail['vatPercentage'] = $vatPercentage;
                $data_detail['systemComment'] = $add_comment;
        
                $this->db->where('companyReqID', $requestMasterID);
                $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);

                $this->db->select('*');
                $this->db->where('companyReqID', $requestMasterID);
                $data_master_new=$this->db->get('srp_erp_srm_vendor_company_requests')->row_array();

                $this->db->select('*');
                $this->db->where('currencyID', $data_master_new['currency']);
                $data_master_currency=$this->db->get('srp_erp_currencymaster')->row_array();

                $this->db->select('*');
                $this->db->where('supplierEmail', $data_master_new['contactPersonEmail'] );
                $this->db->where('companyID', $companyid_supplier );
                $exist_email = $this->db->get('srp_erp_suppliermaster')->row_array();

                if($exist_email){
                    return array('w', 'Supplier Email Alreay exist !. Please Send Refer Back Request');
                    exit();
                }

                // create supplier master

                  $suppliercode = trim($this->input->post('seconeryItemCode') ?? '');
                  $liability = fetch_gl_account_desc($liabilityAccount);
                  $data_sup_master['isActive'] =1;
                  $data_sup_master['secondaryCode'] = $data_master_new['secondaryCode'];
                  $data_sup_master['supplierName'] = $data_master_new['providerName'];
                  $data_sup_master['suppliercountryID'] = $cntry['countryID'];
                  $data_sup_master['supplierCountry'] = $data_master_new['country'];
                  $data_sup_master['apiKey'] = $data_master_new['apiKey'];

                   $data_sup_master['supplierEmail'] = $data_master_new['contactPersonEmail'];
                   $data_sup_master['supplierAddress1'] = $data_master_new['address1'];
                   $data_sup_master['supplierAddress2'] = $data_master_new['address2'];
                   $data_sup_master['suppliercountryID'] = $cntry['countryID'];
                   $data_sup_master['supplierUrl'] = $data_master_new['companyUrl'];
                   $data_sup_master['vatNumber'] = $data_master_new['vatNumber'];

                   $data_sup_master['taxGroupID'] = $suppliertaxgroup;
                   $data_sup_master['isSrmGenerated'] = 1;
                   $data_sup_master['masterConfirmedYN'] = 1;

                   if($ApprovalforSupplierMaster ==1){

                   }else{
                    $data_sup_master['masterApprovedYN'] = 1;
                   }
                   
                   $data_sup_master['supplierTelephone'] = $data_master_new['companyPhone'];
                   $data_sup_master['supplierFax'] = $data_master_new['companyfax'];

                  $data_sup_master['partyCategoryID'] = $partyCategoryID;
                  $data_sup_master['nameOnCheque'] = $nameOnCheque;
          
                  $data_sup_master['vatEligible'] = $vatEligible;
                  $data_sup_master['vatPercentage'] =$vatPercentage;
          
                  $data_sup_master['liabilityAutoID'] = $liability['GLAutoID'];
                  $data_sup_master['liabilitySystemGLCode'] = $liability['systemAccountCode'];
                  $data_sup_master['liabilityGLAccount'] = $liability['GLSecondaryCode'];
                  $data_sup_master['liabilityDescription'] = $liability['GLDescription'];
                  $data_sup_master['liabilityType'] = $liability['subCategory'];
          
                  $data_sup_master['supplierCreditPeriod'] = $supplierCreditPeriod;
                  $data_sup_master['supplierCreditLimit'] = $supplierCreditLimit;
                  $data_sup_master['modifiedPCID'] = $this->common_data['current_pc'];
                  $data_sup_master['modifiedUserID'] = $this->common_data['current_userID'];
                  $data_sup_master['modifiedUserName'] = $this->common_data['current_user'];
                  $data_sup_master['modifiedDateTime'] = $this->common_data['current_date'];

                  $data_sup_master['supplierCurrencyID'] = $supplierCurrency;
                  $data_sup_master['supplierCurrency'] = $data_master_currency['CurrencyCode'];
                  $data_sup_master['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data_sup_master['supplierCurrency']);
                  $data_sup_master['companyID'] = $this->common_data['company_data']['company_id'];
                  $data_sup_master['companyCode'] = $this->common_data['company_data']['company_code'];
                  $data_sup_master['createdUserGroup'] = $this->common_data['user_group'];
                  $data_sup_master['createdPCID'] = $this->common_data['current_pc'];
                  $data_sup_master['createdUserID'] = $this->common_data['current_userID'];
                  $data_sup_master['createdUserName'] = $this->common_data['current_user'];
                  $data_sup_master['createdDateTime'] = $this->common_data['current_date'];
                  $data_sup_master['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
                  $this->db->insert('srp_erp_suppliermaster', $data_sup_master);
                  $last_id_sup = $this->db->insert_id();

                  
                    if($ApprovalforSupplierMaster ==1){
                        $this->load->library('Approvals');
                        $autoApproval= get_document_auto_approval('SUP');
    
                        if($autoApproval==0){
                            $approvals_status = $this->approvals->auto_approve($last_id_sup, 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$data_sup_master['supplierSystemCode'],$this->common_data['current_date']);
                        }elseif($autoApproval==1){
                            $approvals_status = $this->approvals->CreateApproval('SUP', $last_id_sup, $data_sup_master['supplierSystemCode'], 'Supplier', 'srp_erp_suppliermaster', 'supplierAutoID',0,$this->common_data['current_date']);
                        }else{
                            return array('e', 'Approval levels are not set for this document');
                        }
                        if ($approvals_status==1) {
    
                        }else if($approvals_status==3){
                            return array('w', 'There are no users exist to perform approval for this Suplier.');
                        } else {
                            return array('e', 'confirmation failed');
                        }
                    }

               //////////////////////////////////////////////////////////////////////

                    $data['secondaryCode'] = $data_master_new['secondaryCode'];
                    $data['supplierName'] = $data_master_new['providerName'];
                    $data['supplierCurrencyID'] = $data_master_new['currency'];
                    $data['supplierCurrency'] = $data_master_currency['CurrencyCode'];
                    $data['erpSupplierAutoID']=$last_id_sup;
                    $data['supplierCountry'] = $cntry['countryID'];

                    $data['supplierTelephone'] = $data_master_new['companyPhone'];
                    $data['supplieremail'] = $data_master_new['contactPersonEmail'];
                    $data['supplierFax'] =$data_master_new['companyfax'];
                    $data['supplierAddress1'] = $data_master_new['address1'];
                    $data['supplierAddress2'] = $data_master_new['address2'];
                    $data['supplierUrl'] = $data_master_new['companyUrl'];
                    $data['isActive'] = 1;
                    $data['companyRequestMasterID']=$requestMasterID;
                    $data['isSupplierAcc']=1;

                    $data['companyID'] = current_companyID();
                    $data['createdUserID'] = current_userID();
                    $data['createdUserName'] = current_user();
                    $data['createdDateTime'] = format_date_mysql_datetime();
                    $data['createdPCID'] = current_pc();
                    $data['createdUserGroup'] = current_user_group();
                    $data['companyCode'] = current_companyCode();
                    $data['supplierSystemCode'] = $this->sequence->sequence_generator('SRM-SUP');
                    $data['createdUserGroup'] = user_group();
                    $data['timestamp'] = format_date_mysql_datetime();

                    // print_r($data);exit;

                    $this->db->insert('srp_erp_srm_suppliermaster', $data);
                    $last_id = $this->db->insert_id();

                     //assign item for new supplier

                     
                     //$supplierID = $last_id;
 
                    //  $where = "itemmaster.companyID = $companyid_supplier AND (itemmaster.financeCategory = 1 OR itemmaster.financeCategory = 2)";
                    //  $this->db->select('itemmaster.*,`category`.`Description` as catDescription');
                    //  $this->db->from('srp_erp_itemmaster itemmaster');
                    //  $this->db->join('srp_erp_itemcategory category', 'itemmaster.subcategoryID= category.itemCategoryID', 'left');
                    //  //$this->db->join('srp_erp_srm_supplieritems supplierItem', 'supplierItem.itemAutoID = itemmaster.itemAutoID AND  supplierItem.supplierAutoID=' . $supplierID, 'left');
                    //  //$this->db->join('(SELECT * FROM srp_erp_srm_supplieritems WHERE `supplierAutoID` = ' . $last_id . '  ) AS supplierItem', '`supplierItem`.`itemAutoID` = `itemmaster`.`itemAutoID`', 'left');
                    //  $this->db->where($where);
             
                    //  $result_sup_items = $this->db->get()->result_array();
 
                    //  if(!empty($result_sup_items)){
                    //      foreach($result_sup_items as $val){
 
                    //          $this->db->select('*');
                    //          $this->db->where('itemAutoID', $val['itemAutoID']);
                    //          $this->db->where('supplierAutoID', $last_id );
                    //          $output12 = $this->db->get('srp_erp_srm_supplieritems')->row_array();
 
                    //          if (empty($output12)) {
                    //             // $this->db->trans_start();
                    //              $data_sup_tems['supplierAutoID'] = $last_id;
                    //              $data_sup_tems['itemAutoID'] =$val['itemAutoID'];
                    //              $data_sup_tems['companyID'] = current_companyID();
                    //              $data_sup_tems['createdPCID'] = $this->common_data['current_pc'];
                    //              $data_sup_tems['createdUserID'] = $this->common_data['current_userID'];
                    //              $data_sup_tems['createdUserName'] = $this->common_data['current_user'];
                    //              $data_sup_tems['createdDateTime'] = $this->common_data['current_date'];
                    //              $data_sup_tems['createdUserGroup'] = $this->common_data['user_group'];
                    //              $data_sup_tems['timestamp'] = format_date_mysql_datetime();
                    //              $this->db->insert('srp_erp_srm_supplieritems', $data_sup_tems);
 
                    //          }
 
                    //      }
                    //  }
 
                     //end assign item for new supplier

                   // $data_detail2['approveYN'] = 1;
                    $data_detail2['confirmYN'] = 1;
                    $data_detail2['erpSupplierID'] = $last_id ;
                    $data_detail2['systemSupplierID'] =  $last_id_sup ;
                
                    $this->db->where('companyReqID', $requestMasterID);
                    $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail2);

                    $this->db->select('*');
                    $this->db->where('supplierAutoID', $last_id_sup );
                    $new_sup_master = $this->db->get('srp_erp_suppliermaster')->row_array();

                    $master_n = [
                        "reqMasterID"=> $data_master['portalReqID'],
                        "vendorErpID"=> $last_id,
                        "vendorErpMasterID"=> $last_id_sup,
                        "supplierDetails"=>$new_sup_master,
                        "systemComment"=>$add_comment
                    ];
            
                    // $res=$this->updateApproveSupplierDetails($master_n);
            
                    //print_r($master_n);exit;
            
                    // $res_array=json_decode($res);
            
                    // if($res_array->action_status==true){
                    //     $data_detail1['submitErpIDYN'] = 1;
            
                    //     $this->db->where('companyReqID', $requestMasterID);
                    //     $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);

                    //     $this->send_company_approve_email_supplier($last_id,1);
            
                    // }

                    // $token = $this->getLoginToken();

                    // $token_array=json_decode($token);

                    // if($token_array){

                    //     if($token_array->success==true){
                        
                    //         $res=$this->updateApproveSupplierDetails($master_n,$token_array->data->token);

                    //         //print_r($res);exit;

                    //         $res_array=json_decode($res);
                    //         if($res_array->status==true){
                    //             $data_detail1['submitErpIDYN'] = 1;
            
                    //             $this->db->where('companyReqID', $requestMasterID);
                    //             $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);

                    //             $this->send_company_approve_email_supplier($last_id,1);
            
                    //         }
                    //     }
                    // }

            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Refer : Save Failed ' . $this->db->_error_message());
        } else {
            
            $this->db->trans_commit();
            return array('s', 'Update Successfully.');
        }

    }

    function send_company_approve_email_supplier($supplierID,$type)
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('supplierName,supplierEmail,supplierAddress1');
        $this->db->where('supplierAutoID', $supplierID);
        $this->db->from('srp_erp_srm_suppliermaster');
        $masterRecordSupplier = $this->db->get()->row_array();
        
        $newurl = explode("/", $_SERVER['SCRIPT_NAME']);

        $link = $this->config->item('vendor_portal_url');
        $emailsubject="";
        $emailbody ="";
        if($type==1){
            //register confirmation
            $emailsubject = "Company Request Approval";

            $emailbody = "Your Company request is Confirmed.</p><br><br><a href='$link'>Click here to access the portal online.</a><br><br><p>For more detail contact the supply chain department.</p><br><p>Thank You</p></div>";
        }

        if($type == 2){
            $emailsubject = "Purchase Order";

            $emailbody = "Your Purchase order is Created.</p><br><br><a href='$link'>Click here to access the portal online.</a><br><br><p>For more detail contact the supply chain department.</p><br><p>Thank You</p></div>";
        }

       
                

        if (!empty($masterRecordSupplier)) {
            if (!empty($masterRecordSupplier['supplierEmail'])) {

                $x = 0;
                $params[$x]["companyID"] = current_companyID();
                $params[$x]["documentID"] = '';
                $params[$x]["documentSystemCode"] = '';
                $params[$x]["documentCode"] = '';
                $params[$x]["emailSubject"] = $emailsubject;
                $params[$x]["empEmail"] = $masterRecordSupplier['supplierEmail'];
                $params[$x]["empID"] = current_userID();
                $params[$x]["empName"] = $masterRecordSupplier['supplierName'];
                $params[$x]["emailBody"] = $emailbody ;
                $params[$x]["type"] = 'SrmCompanyRequest' ;

                if (!empty($params)) {
                    $this->email_manual->set_email_detail($params);

                   // echo json_encode(['s', 'Successfully Requested']);
                }
                
                // $config['charset'] = "utf-8";
                

                // $config['mailtype'] = "html";
                // $config['wordwrap'] = TRUE;
                // $config['protocol'] = 'smtp';
                // $config['smtp_host'] = $this->config->item('email_smtp_host');
                // $config['smtp_user'] = $this->config->item('email_smtp_username');
                // $config['smtp_pass'] = $this->config->item('email_smtp_password');
                // //$config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                // $config['smtp_crypto'] = 'tls';
                // $config['smtp_port'] = '587';
                // $config['SMTPOptions'] =  array (
                //     'ssl' => array(
                //         'verify_peer' => false,
                //         'verify_peer_name' => false,
                //         'allow_self_signed' => true
                //     )
                // );
                // $config['crlf'] = "\r\n";
                // $config['newline'] = "\r\n";
                // $this->load->library('email', $config);
                
                // $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                // $this->email->to($masterRecordSupplier['supplierEmail']);
                // $this->email->subject($emailsubject);
                // $this->email->message($emailbody);
                // $result = $this->email->send();
                // //$err1 = $this->email->print_debugger();
                // //echo $err1;
                // //var_dump($result);
                
                // // return array('s', 'RFQ Email Send Successfully');
            } else {
                // return array('e', 'No Email ID Found for supplier');
            }

        } else {
            //return array('e', 'No Supplier Records Found');
        }
        
    }

    function send_rfq_change_request_message_email_suppliers($inquiryMasterID,$supplierID)
    {

        // $supplierID = trim($this->input->post('supplierID') ?? '');
        // $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('supplierName,supplierEmail,supplierAddress1');
        $this->db->where('supplierAutoID', $supplierID);
        $this->db->from('srp_erp_srm_suppliermaster');
        $masterRecordSupplier = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('inquiryID', $inquiryMasterID);
        $this->db->where('isRfqSubmitted', 1);
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $masterRecordInquiry = $this->db->get()->row_array();
        
       
        if(!empty($masterRecordInquiry)){
            $newurl = explode("/", $_SERVER['SCRIPT_NAME']);
            //$link = "http://$_SERVER[HTTP_HOST]/$newurl[1]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID;
            //$link = "https://$_SERVER[HTTP_HOST]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID . '_' . $companyID;
    
            $link = $this->config->item('vendor_portal_url').'/common/vendor/' .$companyID  . '/' . $supplierID . '/' . $masterRecordInquiry['vendorUrlReferenceID'];
    
            $emailsubject = $masterRecordInquiry['narration'] . "-" . $masterRecordInquiry['documentCode'] . "- RFQ";

            $emailbody = "You have request</p><br><br><a href='$link'>Click here to access the portal online.</a><br><br><p>For more detail contact the supply chain department.</p><br><p>Thank You</p></div>";
                    
    
            if (!empty($masterRecordSupplier)) {
                if (!empty($masterRecordSupplier['supplierEmail'])) {
                    
                    //send_custom_email("milindahasaranga@gmail.com", "Milee", "INVOICE");
                    //exit;
    
                    //$this->load->library('email_manual');
                    $config['charset'] = "utf-8";
                    
    
                    $config['mailtype'] = "html";
                    $config['wordwrap'] = TRUE;
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = $this->config->item('email_smtp_host');
                    $config['smtp_user'] = $this->config->item('email_smtp_username');
                    $config['smtp_pass'] = $this->config->item('email_smtp_password');
                    //$config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                    $config['smtp_crypto'] = 'tls';
                    $config['smtp_port'] = '587';
                    $config['SMTPOptions'] =  array (
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    $config['crlf'] = "\r\n";
                    $config['newline'] = "\r\n";
                    $this->load->library('email', $config);
                   
                    $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                    $this->email->to($masterRecordSupplier['supplierEmail']);
                    $this->email->subject($emailsubject);
                    $this->email->message($emailbody);
                    $result = $this->email->send();
                    //$err1 = $this->email->print_debugger();
                    //echo $err1;
                    //var_dump($result);
                   
                   // return array('s', 'RFQ Email Send Successfully');
                } else {
                   // return array('e', 'No Email ID Found for supplier');
                }
    
            } else {
                //return array('e', 'No Supplier Records Found');
            }
        }else{
            //return array('e', 'RFQ Email Not Send');
        }
        
    }

    function send_supplier_company_request_status($email,$type)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $currentUser = $this->common_data['current_userID'];
       
        if($type){
    
            $emailsubject = "Company Request Status";

            $emailbody ="";

            if($type==3){
                $emailbody = "Your Company Request is Rejected<br><p>Thank You</p></div>";
            }
            
            if($type==2){
                $emailbody = "Your Company Request is Refer Back<br><p>Thank You</p></div>";
            }

            if($type==1){
                $emailbody = "Your company request document is Rejected.Please upload again<br><p>Thank You</p></div>";
            }

            if ($email) {

                $x = 0;
                $params[$x]["companyID"] = current_companyID();
                $params[$x]["documentID"] = '';
                $params[$x]["documentSystemCode"] = '';
                $params[$x]["documentCode"] = '';
                $params[$x]["emailSubject"] = $emailsubject;
                $params[$x]["empEmail"] = $email;
                $params[$x]["empID"] = current_userID();
                $params[$x]["empName"] = '';
                $params[$x]["emailBody"] = $emailbody ;
                $params[$x]["type"] = 'SrmCompanyRequest' ;

                if (!empty($params)) {
                    $this->email_manual->set_email_detail($params);

                   // echo json_encode(['s', 'Successfully Requested']);
                }
                
                // $config['charset'] = "utf-8";
                

                // $config['mailtype'] = "html";
                // $config['wordwrap'] = TRUE;
                // $config['protocol'] = 'smtp';
                // $config['smtp_host'] = $this->config->item('email_smtp_host');
                // $config['smtp_user'] = $this->config->item('email_smtp_username');
                // $config['smtp_pass'] = $this->config->item('email_smtp_password');
                // //$config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                // $config['smtp_crypto'] = 'tls';
                // $config['smtp_port'] = '587';
                // $config['SMTPOptions'] =  array (
                //     'ssl' => array(
                //         'verify_peer' => false,
                //         'verify_peer_name' => false,
                //         'allow_self_signed' => true
                //     )
                // );
                // $config['crlf'] = "\r\n";
                // $config['newline'] = "\r\n";
                // $this->load->library('email', $config);
                
                // $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                // $this->email->to($email);
                // $this->email->subject($emailsubject);
                // $this->email->message($emailbody);
                // $result = $this->email->send();
                
            } else {
                // return array('e', 'No Email ID Found for supplier');
            }
    
        }else{
            //return array('e', 'RFQ Email Not Send');
        }
        
    }


    public function saveChangeRequestAPI($master){
        $curl = curl_init();
        //http://localhost:8000/ http://vendorbe.rbdemo.live

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/api/vendor/create-rfq-change-request',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodyChangeRequest($master),
        CURLOPT_HTTPHEADER => array(
            // "Authorization: Bearer $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodyChangeRequest($master,$sub){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }



    function order_inquiry_generate_supplier_rfq()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $inquiryDetailIDs = $this->input->post('selectedSupplierSync');
        $orderID = $this->input->post('orderID');
        
       $rfq_gendoc = $this->db->query("SELECT
                          inquiryDetailID
                   
                          FROM
                         `srp_erp_srm_orderinquirydetails` 
                          WHERE
                          companyID = 13 
                          AND inquiryMasterID = 129
                          AND isRfqCreated = 1")->result_array();

        $rfq_gendoc = array_map(function ($value) {
            return $value['inquiryDetailID'];
        }, $rfq_gendoc);
        
        $unchecked_inquiry = array_diff($rfq_gendoc, $this->input->post('selectedSupplierSync'));


       
        

        if (!empty($inquiryDetailIDs)) {

            if(!empty($unchecked_inquiry)){ 
            
                foreach ($unchecked_inquiry as $key => $uncheckedinquiryDetailID) {

                    $data_detail['isRfqCreated'] = 0;
                    $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                    $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->where('inquiryDetailID', $uncheckedinquiryDetailID);
                    $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);
                }
            }



            foreach ($inquiryDetailIDs as $key => $inquiryDetailID) {

                $data_detail['isRfqCreated'] = 1;
                $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('inquiryDetailID', $inquiryDetailID);
                $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);
            }

            if (!empty($orderID)) {
                foreach ($orderID as $row) {
                    if (trim($this->input->post('confirmed') ?? '') == 1) {
                        $data_order_master['status'] = 4;
                        $this->db->where('customerOrderID', $row);
                        $this->db->update('srp_erp_srm_customerordermaster', $data_order_master);
                    }
                }
            }
            if (trim($this->input->post('confirmed') ?? '') == 1) {
                $data_master['confirmedYN'] = 1;
                $data_master['confirmedDate'] = $this->common_data['current_date'];
                $data_master['confirmedByEmpID'] = $this->common_data['current_userID'];
                $data_master['confirmedByName'] = $this->common_data['current_user'];
            }
            $data_master['deliveryTerms'] = $this->input->post('deliveryTerms');
            $this->db->where('inquiryID', $inquiryID);
            $this->db->update('srp_erp_srm_orderinquirymaster', $data_master);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'RFQ Generate Failed ' . $this->db->_error_message());
            } else {

                $this->db->select('srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.rfqExpDate,srp_erp_currencymaster.CurrencyCode,srp_erp_srm_orderinquirymaster.deliveryTerms,srp_erp_srm_orderinquirymaster.narration,srp_erp_srm_orderinquirymaster.createdDateTime,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID,srp_erp_company.company_name,srp_erp_company.company_code');
                $this->db->from('srp_erp_srm_orderinquirymaster');
                $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_srm_orderinquirymaster.companyID', 'LEFT');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
                $this->db->where('inquiryID', $inquiryID);

                $master_data = $this->db->get()->row_array();

                 $vendor_ref=substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),1,10);

                $master_n = [
                    "documentCode"=> $master_data['documentCode'],
                    "documentDate"=> $master_data['documentDate'],
                    "companyCode"=> $master_data['company_code'],
                    "inquiryID"=> $master_data['inquiryID'],
                    "companyID"=> $master_data['companyID'],
                    "company_name"=> $master_data['company_name'],
                    "referenceID"=>$vendor_ref,
                    "rfqExpDate"=> $master_data['rfqExpDate'],
                    "narration"=> $master_data['narration'],
                    "createdDateTime"=> $master_data['createdDateTime'],
                    "deliveryTerms"=> $master_data['deliveryTerms'],
                    "currencyCode"=>$master_data['CurrencyCode']
                
                ];

                $this->db->select('srp_erp_srm_orderinquirydetails.inquiryDetailID,srp_erp_srm_orderinquirydetails.supplierID,srp_erp_srm_orderinquirydetails.lineWiseComment,srp_erp_srm_orderinquirydetails.pr_document,srp_erp_srm_orderinquirydetails.lineWiseDoc,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_suppliermaster.supplierEmail,srp_erp_srm_suppliermaster.supplierAddress1,srp_erp_srm_orderinquirydetails.itemAutoID,srp_erp_srm_orderinquirydetails.requestedQty,srp_erp_srm_orderinquirydetails.expectedDeliveryDate,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
                $this->db->from('srp_erp_srm_orderinquirydetails');
                $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
                $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
                $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
                //$this->db->where($where_detail);
                $this->db->where('inquiryMasterID', $inquiryID);
                $this->db->where('isRfqCreated', 1);
                $data = $this->db->get()->result_array();

                $token = $this->getLoginToken();

                $token_array=json_decode($token);

                if($token_array){

                    if($token_array->success==true){
                       // $res=$this->sendRfqAPI($detail_array,$token_array->data->token);

                       $res=$this->saveRfqAPI($master_n,$data,$token_array->data->token);

                       $res_array=json_decode($res);
                       if($res_array->status==true){
                           $data_detail1['isRfqSubmitted'] = 1;
                           $data_detail1['vendorUrlReferenceID'] = $vendor_ref;
                           $this->db->where('inquiryID', $inquiryID);
                           $this->db->update('srp_erp_srm_orderinquirymaster', $data_detail1);
       
                       }
                    }
                }


                // // $res=$this->saveRfqAPI($master_n,$data);

                // // $res_array=json_decode($res);

                // // if($res_array->action_status==true){
                //     $data_detail1['isRfqSubmitted'] = 1;
                //     $data_detail1['vendorUrlReferenceID'] = $vendor_ref;
                //     $this->db->where('inquiryID', $inquiryID);
                //     $this->db->update('srp_erp_srm_orderinquirymaster', $data_detail1);

                // // }

                $this->db->trans_commit();
                return array('s', 'RFQ Generated Successfully');
            }
        } else {
            return array('e', 'Atleast one item must be selected ! ');
        }
    }

    function order_inquiry_generate_supplier_view_rfq()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $supplierIDs = $this->input->post('selectedSupplierSync');
        $orderID = $this->input->post('orderID');

        $inquiryDetailIDs=[];

        foreach($supplierIDs as $val){
            
            $this->db->select('inquiryDetailID');
            $this->db->from('srp_erp_srm_orderinquirydetails');
            $this->db->where('supplierID', $val);
            $this->db->where('inquiryMasterID', $inquiryID);
            $result_arr = $this->db->get()->result_array();

            foreach($result_arr as $tr){
                $inquiryDetailIDs[]= $tr['inquiryDetailID'];
            }

        }

      //  print_r($inquiryDetailIDs);exit;
        
       $rfq_gendoc = $this->db->query("SELECT
                          inquiryDetailID
                   
                          FROM
                         `srp_erp_srm_orderinquirydetails` 
                          WHERE
                          companyID = 13 
                          AND inquiryMasterID = 129
                          AND isRfqCreated = 1")->result_array();

        $rfq_gendoc = array_map(function ($value) {
            return $value['inquiryDetailID'];
        }, $rfq_gendoc);
        
        $unchecked_inquiry = array_diff($rfq_gendoc, $inquiryDetailIDs);

        if (!empty($inquiryDetailIDs)) {

            if(!empty($unchecked_inquiry)){ 
            
                foreach ($unchecked_inquiry as $key => $uncheckedinquiryDetailID) {

                    $data_detail['isRfqCreated'] = 0;
                    $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                    $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->where('inquiryDetailID', $uncheckedinquiryDetailID);
                    $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);
                }
            }



            foreach ($inquiryDetailIDs as $key => $inquiryDetailID) {

                $data_detail['isRfqCreated'] = 1;
                $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('inquiryDetailID', $inquiryDetailID);
                $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail);
            }

            if (!empty($orderID)) {
                foreach ($orderID as $row) {
                    if (trim($this->input->post('confirmed') ?? '') == 1) {
                        $data_order_master['status'] = 4;
                        $this->db->where('customerOrderID', $row);
                        $this->db->update('srp_erp_srm_customerordermaster', $data_order_master);
                    }
                }
            }
            if (trim($this->input->post('confirmed') ?? '') == 1) {
                $data_master['confirmedYN'] = 1;
                $data_master['confirmedDate'] = $this->common_data['current_date'];
                $data_master['confirmedByEmpID'] = $this->common_data['current_userID'];
                $data_master['confirmedByName'] = $this->common_data['current_user'];
            }
            $data_master['deliveryTerms'] = $this->input->post('deliveryTerms');
            $this->db->where('inquiryID', $inquiryID);
            $this->db->update('srp_erp_srm_orderinquirymaster', $data_master);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'RFQ Generate Failed ' . $this->db->_error_message());
            } else {

                $this->db->select('srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.narration,srp_erp_currencymaster.CurrencyCode,srp_erp_srm_orderinquirymaster.deliveryTerms,srp_erp_srm_orderinquirymaster.createdDateTime,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID,srp_erp_company.company_name,srp_erp_srm_orderinquirymaster.rfqExpDate');
                $this->db->from('srp_erp_srm_orderinquirymaster');
                $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_srm_orderinquirymaster.companyID', 'LEFT');
                $this->db->join('srp_erp_currencymaster', 'srp_erp_srm_orderinquirymaster.transactionCurrencyID = srp_erp_currencymaster.currencyID', 'LEFT');
                $this->db->where('inquiryID', $inquiryID);

                $master_data = $this->db->get()->row_array();

                $vendor_ref=substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),1,10);

                $master_n = [
                    "documentCode"=> $master_data['documentCode'],
                    "documentDate"=> $master_data['documentDate'],
                    "inquiryID"=> $master_data['inquiryID'],
                    "companyID"=> $master_data['companyID'],
                    "company_name"=> $master_data['company_name'],
                    "referenceID"=>$vendor_ref,
                    "rfqExpDate"=> $master_data['rfqExpDate'],
                    "narration"=> $master_data['narration'],
                    "createdDateTime"=> $master_data['createdDateTime'],
                    "deliveryTerms"=> $master_data['deliveryTerms'],
                    "currencyCode"=>$master_data['CurrencyCode']
                
                ];

                $this->db->select('srp_erp_srm_orderinquirydetails.inquiryDetailID,srp_erp_srm_orderinquirydetails.supplierID,srp_erp_srm_orderinquirydetails.lineWiseComment,srp_erp_srm_orderinquirydetails.pr_document,srp_erp_srm_orderinquirydetails.lineWiseDoc,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_suppliermaster.supplierEmail,srp_erp_srm_suppliermaster.supplierAddress1,srp_erp_srm_orderinquirydetails.itemAutoID,srp_erp_srm_orderinquirydetails.requestedQty,srp_erp_srm_orderinquirydetails.expectedDeliveryDate,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
                $this->db->from('srp_erp_srm_orderinquirydetails');
                $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
                $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
                $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');
                //$this->db->where($where_detail);
                $this->db->where('inquiryMasterID', $inquiryID);
                $this->db->where('isRfqCreated', 1);
                $data = $this->db->get()->result_array();

                $token = $this->getLoginToken();

                $token_array=json_decode($token);

               // print_r($token_array->data->token);exit;

                if($token_array){

                    if($token_array->success==true){
                       // $res=$this->sendRfqAPI($detail_array,$token_array->data->token);

                       $res=$this->saveRfqAPI($master_n,$data,$token_array->data->token);

                       $res_array=json_decode($res);
                      // print_r($res);exit;
                       if($res_array->status==true){
                           $data_detail1['isRfqSubmitted'] = 1;
                           $data_detail1['vendorUrlReferenceID'] = $vendor_ref;
                           $this->db->where('inquiryID', $inquiryID);
                           $this->db->update('srp_erp_srm_orderinquirymaster', $data_detail1);
       
                       }
                    }
                }

            //    $res=$this->saveRfqAPI($master_n,$data);

            //     // $res_array=json_decode($res);

            //     // if($res_array->action_status==true){
            //         $data_detail1['isRfqSubmitted'] = 1;
            //        // $data_detail1['vendorUrlReferenceID'] = $vendor_ref;
            //         $this->db->where('inquiryID', $inquiryID);
            //         $this->db->update('srp_erp_srm_orderinquirymaster', $data_detail1);

            //     // }

                $this->db->trans_commit();
                return array('s', 'RFQ Generated Successfully');
            }
        } else {
            return array('e', 'Atleast one item must be selected ! ');
        }
    }

    function fetch_item_supplier_view(){
        $this->db->trans_start();
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');

        $this->db->select('srp_erp_srm_orderinquirydetails.itemAutoID,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemImage,srp_erp_itemmaster.companyReportingSellingPrice,srp_erp_itemmaster.itemSystemCode');
        $this->db->from('srp_erp_srm_orderinquirydetails');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
        $this->db->group_by('srp_erp_srm_orderinquirydetails.itemAutoID');

        $this->db->where('supplierID', $supplierID);
        $this->db->where('inquiryMasterID', $inquiryMasterID);
        $result_arr = $this->db->get()->result_array();
        return $result_arr;

    }

    function add_url_expire_date()
    {
        $this->db->trans_start();

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $companyID = $this->input->post('companyID');
        $expireDate = $this->input->post('expireDate');

        $this->db->select('*');
        $this->db->from('srp_erp_srm_vendor_url_expire');
        $this->db->where('inquiryMasterID', $inquiryMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->where('supplierID', $supplierID);

        $master_data = $this->db->get()->row_array();

        if($master_data){

            $data_detail['expireDate'] = $expireDate;
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
            $data_detail['modifiedUserName'] = $this->common_data['current_user'];
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('inquiryMasterID', $inquiryMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->where('supplierID', $supplierID);
            $this->db->update('srp_erp_srm_vendor_url_expire', $data_detail);

        }else{

            $data_detail['inquiryMasterID'] =$inquiryMasterID;
            $data_detail['supplierID'] =  $supplierID;
            $data_detail['companyID'] = $companyID;
            $data_detail['expireDate'] = $expireDate;
            $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
            $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
            $data_detail['modifiedUserName'] = $this->common_data['current_user'];
            $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_srm_vendor_url_expire', $data_detail);

        }


        $master_n = [
            "inquiryMasterID"=> $inquiryMasterID,
            "supplierID"=> $supplierID,
            "companyID"=> $companyID,
            "expireDate"=> $expireDate,
        
        ];

        $res=$this->saveUrlExpireDetails($master_n);

        $res_array=json_decode($res);

        if($res_array->action_status==true){
            $data_detail1['submitYN'] = 1;

            $this->db->where('inquiryMasterID', $inquiryMasterID);
            $this->db->where('companyID', $companyID);
            $this->db->where('supplierID', $supplierID);
            $this->db->update('srp_erp_srm_vendor_url_expire', $data_detail1);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'URL Expire Generate Failed ' . $this->db->_error_message());
        } else {

            $this->db->trans_commit();
            return array('s', 'URL Expire Generated Successfully');
        }
    }

    function reject_company_request()
    {
        $this->db->trans_start();

        $requestID = trim($this->input->post('requestID') ?? '');

        $this->db->select('*');
        $this->db->from('srp_erp_srm_vendor_company_requests');
        $this->db->where('companyReqID', $requestID);
        $master_data = $this->db->get()->row_array();

        if( $master_data ){
            $data_detail['approveYN'] = 2;
            $this->db->where('companyReqID', $requestID);
            $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail);
    
    
            $master_n = [
                "requestID"=> $master_data['portalReqID'],
            ];
    
            $res=$this->saveCompanyRequestReject($master_n);
    
            $res_array=json_decode($res);
    
            if($res_array->action_status==true){
                $data_detail1['submitRejectYN'] = 1;
                $this->db->where('companyReqID', $requestID);
                $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail1);
    
            }

        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update Failed ' . $this->db->_error_message());
        } else {

            $this->db->trans_commit();
            return array('s', 'Update Successfully');
        }
    }

    public function saveCompanyRequestReject($master){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/api/vendor/update-company-request-reject',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodysaveCompanyRequestReject($master),
        CURLOPT_HTTPHEADER => array(
            // "Authorization: Bearer $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodysaveCompanyRequestReject($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    public function saveUrlExpireDetails($master){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/api/vendor/update-link-expire',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodyUrlExpireDetails($master),
        CURLOPT_HTTPHEADER => array(
            // "Authorization: Bearer $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodyUrlExpireDetails($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];

         return json_encode($jayParsedAry);
    }

    public function resend_fail_rfq_details(){
        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where('isRfqSubmitted', 1);
        $data_master = $this->db->get()->result_array();

        if(count($data_master)>0){
            foreach($data_master as $val){

                $this->db->select('srp_erp_srm_orderinquirymaster.inquiryID,srp_erp_srm_orderinquirymaster.documentCode,srp_erp_srm_orderinquirymaster.documentDate,srp_erp_srm_orderinquirymaster.companyID,srp_erp_company.company_name');
                $this->db->from('srp_erp_srm_orderinquirymaster');
                $this->db->join('srp_erp_company', 'srp_erp_company.company_id = srp_erp_srm_orderinquirymaster.companyID', 'LEFT');
                $this->db->where('inquiryID', $val['inquiryID']);

                $master_data = $this->db->get()->row_array();

                $vendor_ref=substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'),1,10);

                $master_n = [
                    "documentCode"=> $master_data['documentCode'],
                    "documentDate"=> $master_data['documentDate'],
                    "inquiryID"=> $master_data['inquiryID'],
                    "companyID"=> $master_data['companyID'],
                    "company_name"=> $master_data['company_name'],
                    "referenceID"=>$vendor_ref,
                
                ];

                $this->db->select('srp_erp_srm_orderinquirydetails.inquiryDetailID,srp_erp_srm_orderinquirydetails.supplierID,srp_erp_srm_orderinquirydetails.pr_document,srp_erp_srm_orderinquirydetails.lineWiseDoc,srp_erp_srm_suppliermaster.supplierName,srp_erp_srm_suppliermaster.supplierEmail,srp_erp_srm_suppliermaster.supplierAddress1,srp_erp_srm_orderinquirydetails.itemAutoID,srp_erp_srm_orderinquirydetails.requestedQty,srp_erp_srm_orderinquirydetails.expectedDeliveryDate,srp_erp_itemmaster.itemName,srp_erp_itemmaster.itemSystemCode,UnitShortCode');
                $this->db->from('srp_erp_srm_orderinquirydetails');
                $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderinquirydetails.itemAutoID', 'LEFT');
                $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderinquirydetails.supplierID', 'LEFT');
                $this->db->join('srp_erp_unit_of_measure', 'srp_erp_srm_orderinquirydetails.defaultUOMID = srp_erp_unit_of_measure.UnitID', 'LEFT');

                //$this->db->where($where_detail);
                $this->db->where('inquiryMasterID', $val['inquiryID']);
                $this->db->where('isRfqCreated', 1);
                $data = $this->db->get()->result_array();

                $token = $this->getLoginToken();

                $token_array=json_decode($token);

                if($token_array){

                    if($token_array->success==true){
                       // $res=$this->sendRfqAPI($detail_array,$token_array->data->token);

                       $res=$this->saveRfqAPI($master_n,$data,$token_array->data->token);

                       $res_array=json_decode($res);

                       print_r($res_array);exit;
       
                       if($res_array->action_status==true){
                           $data_detail1['isRfqSubmitted'] = 1;
                           $data_detail1['vendorUrlReferenceID'] = $vendor_ref;
                           $this->db->where('inquiryID', $val['inquiryID']);
                           $this->db->update('srp_erp_srm_orderinquirymaster', $data_detail1);
       
                       }
                    }
                }

               

            }

            return true;
        }else{
            return false;
        }
    }


    public function saveRfqAPI($master,$sub,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/save_supplier_rfq',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBody($master,$sub),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        //print_r($response);exit;
        return $response;
    }


    public function getBody($master,$sub){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master, 
                  "dataSub" => $sub, 
                  
               ] 
         ];

        // print_r(json_encode($jayParsedAry));exit;

         return json_encode($jayParsedAry);
    }

    public function getLoginToken(){
        $curl = curl_init();
       
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/'.'index.php/Api_spur/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getLoginBody(),
        CURLOPT_HTTPHEADER => array(
            // "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

      // print_r(json_decode($response)->data->token);exit;
        return $response;
    }


    public function getLoginBody(){

        $jayParsedAry = [
                  "username" => $this->config->item('vendor_portal_api_username'),
                  "password"=>$this->config->item('vendor_portal_api_password')
        ];

         return json_encode($jayParsedAry);
    }

    function load_customer_BaseDetail()
    {
        $this->db->select('CustomerAddress1,customerCurrencyID,customerTelephone');
        $this->db->where('CustomerAutoID', $this->input->post('customerID'));
        return $this->db->get('srp_erp_srm_customermaster')->row_array();
    }

    function fetch_inquiry_details_view()
    {
         $this->db->select('*');
        $this->db->where('inquiryDetailID', $this->input->post('inquiryDetailID'));
        $res= $this->db->get('srp_erp_srm_orderinquirydetails')->row_array();

        if($res){
            return array('s', $res);
        }else{
            return array('e', '');
        }
    }

    function fetch_inquiry_terms_supplier()
    {
        $this->db->select('*');
        $this->db->where('companyID', current_companyID());
        $this->db->where('inquiryID', $this->input->post('inquiryMasterID'));
        $this->db->where('supplierID', $this->input->post('supplierID'));
        $res= $this->db->get('srp_erp_vendor_terms')->row_array();

        if($res){
            return array('s', $res);
        }else{
            return array('e', '');
        }
       
    }

   

    function assignItems_supplier_orderInquiry()
    {
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $supplierIDs = $this->input->post('assignSupplierItemSync');

        if (!empty($supplierIDs)) {
            foreach ($supplierIDs as $key => $supplierID) {

                $data['supplierAutoID'] = $supplierID;
                $data['itemAutoID'] = $itemAutoID;
                $data['companyID'] = current_companyID();
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['timestamp'] = format_date_mysql_datetime();
                $this->db->insert('srp_erp_srm_supplieritems', $data);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Supplier Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Supplier Assigned Successfully');
        }
    }

    function assignItems_supplier_view_orderInquiry()
    {
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $supplierIDs = $this->input->post('assignSupplierItemSync');

        $this->db->select('itemAutoID');
        $this->db->from('srp_erp_srm_inquiryitem');
        $this->db->where('inquiryMasterID',$inquiryID);
        $this->db->group_by('srp_erp_srm_inquiryitem.itemAutoID');
        $itemIDs = $this->db->get()->result_array();

        if (!empty($supplierIDs)) {
            foreach ($supplierIDs as $key => $supplierID) {

                foreach($itemIDs as $val){
                    $data['supplierAutoID'] = $supplierID;
                    $data['itemAutoID'] = $val['itemAutoID'];
                    $data['companyID'] = current_companyID();
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['timestamp'] = format_date_mysql_datetime();
                    $this->db->insert('srp_erp_srm_supplieritems', $data);
                }
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Supplier Assigned Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Supplier Assigned Successfully');
        }
    }

    function delete_supplier_srm()
    {
        $inquiryDetailID = $this->input->post('inquiryDetailID');
        $this->db->where('inquiryDetailID', $inquiryDetailID);
        $results = $this->db->delete('srp_erp_srm_orderinquirydetails');
        if ($results) {
            return array('error' => 0, 'message' => 'Record Deleted Successfully ');

        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact the system team!');
        }
    }

    function add_customer_order_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $customerOrderID = trim($this->input->post('customerOrderID') ?? '');

        $data['documentAutoID'] = $customerOrderID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 3;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_srm_notes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Order Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', 'Order  Note Saved Successfully.');

        }
    }

    /*function send_rfq_email_suppliers()
    {

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('supplierName,supplierEmail,supplierAddress1');
        $this->db->where('supplierAutoID', $supplierID);
        $this->db->from('srp_erp_srm_suppliermaster');
        $masterRecordSupplier = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('inquiryID', $inquiryMasterID);
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $masterRecordInquiry = $this->db->get()->row_array();

        $newurl = explode("/", $_SERVER['SCRIPT_NAME']);
        //$link = "http://$_SERVER[HTTP_HOST]/$newurl[1]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID;
        $link = "https://$_SERVER[HTTP_HOST]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID;
        $emailsubject = $masterRecordInquiry['narration'] . "-" . $masterRecordInquiry['documentCode'] . "- RFQ";
        $emailbody = "<div style='width: 80%;margin: auto;background-color:#fbfbfb;padding: 2%;font-family: sans-serif;'><b>To : " . $masterRecordSupplier['supplierName'] . "</b> <br><p>" . $masterRecordSupplier['supplierAddress1'] . "</p><br><p> ( ".current_companyCode()." ) ".ucwords($this->common_data['company_data']['company_name'])." issues this Purchasing Document detailed below through the iSupplier
Portal. NO ACTION IS REQUIRED but you can view the document at your discretion.</p><br><br><a href='$link'>Click here to access the portal online.</a><br><br><p>For more detail contact the supply chain department.</p><br><p>Thank You</p></div>";

        if (!empty($masterRecordSupplier)) {
            if (!empty($masterRecordSupplier['supplierEmail'])) {
                $config['mailtype'] = "html";
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = 'smtp.sparkpostmail.com';
                $config['smtp_user'] = 'SMTP_Injection';
                $config['smtp_pass'] = '6d911d3e2ffe9faabc3af1e289eb067908deb1c5';
                $config['smtp_crypto'] = 'tls';
                $config['smtp_port'] = '587';
                $condig['crlf'] = "\r\n";
                $config['newline'] = "\r\n";
                $this->load->library('email', $config);
                $this->email->from('noreply@redberylit.com', 'SME-SRM');
                $this->email->to($masterRecordSupplier['supplierEmail']);
                $this->email->subject($emailsubject);
                $this->email->message($emailbody);
                $result = $this->email->send();

                if ($result) {
                    $data['isRfqEmailed'] = 1;
                    $this->db->where('supplierID', $supplierID);
                    $this->db->where('inquiryMasterID', $inquiryMasterID);
                    $update = $this->db->update('srp_erp_srm_orderinquirydetails', $data);
                }
                return array('s', 'RFQ Email Send Successfully');
            } else {
                return array('e', 'No Email ID Found for supplier');
            }

        } else {
            return array('e', 'No Supplier Records Found');
        }
    }*/



    function send_rfq_email_suppliers()
    {

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('supplierName,supplierEmail,supplierAddress1');
        $this->db->where('supplierAutoID', $supplierID);
        $this->db->from('srp_erp_srm_suppliermaster');
        $masterRecordSupplier = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('inquiryID', $inquiryMasterID);
        $this->db->where('isRfqSubmitted', 1);
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $masterRecordInquiry = $this->db->get()->row_array();
        
       
        if(!empty($masterRecordInquiry)){
            $newurl = explode("/", $_SERVER['SCRIPT_NAME']);
            //$link = "http://$_SERVER[HTTP_HOST]/$newurl[1]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID;
            //$link = "https://$_SERVER[HTTP_HOST]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID . '_' . $companyID;
    
           // $link = $this->config->item('vendor_portal_url').'/common/vendor/' .$companyID  . '/' . $supplierID . '/' . $masterRecordInquiry['vendorUrlReferenceID'];

           $company_token = getLoginTokenCurrentCompany();
           $token_array_company=json_decode($company_token);
           
           if($token_array_company){
               if($token_array_company->success==true){
                    $url = $companyID;
                    $companyidcomp = base64_encode(($url));
                    $qut = base64_encode(($inquiryMasterID));
                    $sup = base64_encode(($supplierID));
                    $token= base64_encode(($token_array_company->data->token));
                    $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                    $companyCode1 = base64_encode(($companyCode));

                    $link_base_url = "$protocol$_SERVER[HTTP_HOST]";
                    $link_base_url_decode = base64_encode(($link_base_url));

                    $this->db->select('*');
                    $this->db->where('company_id', $companyID);
                    $this->db->from('srp_erp_company');
                    $data_company = $this->db->get()->row_array();

                    $com_data['company_address1'] = $data_company['company_address1'];
                    $com_data['company_address2'] = $data_company['company_address2'];
                    $com_data['company_city'] = $data_company['company_city'];
                    $com_data['company_country'] = $data_company['company_country'];
                    $com_data['company_phone'] = $data_company['company_phone'];
                    $com_data['company_email'] = $data_company['company_email'];
                    $com_data['company_name'] = $data_company['company_name'];

                    $companyDetails = json_encode(($com_data));
                    $companyDetails = base64_encode(($companyDetails));
        
                    //$link2 = '/index.php/SrmVendorPortal/vendor_rfq_view?comp='.$companyidcomp.'&qut='.$qut.'&sup='.$sup.' ';
                    $link2 = '/index.php/SrmVendorPortal/vendor_rfq_view?comp='.$companyidcomp.'&qut='.$qut.'&sup='.$sup.'&num='.$token.'&crf='.$companyCode1.'&ul='.$link_base_url_decode.'&dcom='.$companyDetails.' ';
                    // $link = "$protocol$_SERVER[HTTP_HOST]".$link2;
                    $link = $this->config->item('vendor_portal_api_base_url').$link2;
            
                    $emailsubject = $masterRecordInquiry['narration'] . "-" . $masterRecordInquiry['documentCode'] . "- RFQ";
                    $emailbody = "<div style='width: 80%;margin: auto;background-color:#fbfbfb;padding: 2%;font-family: sans-serif;'><b>To : " . $masterRecordSupplier['supplierName'] . "</b> <br><p>" . $masterRecordSupplier['supplierAddress1'] . "</p><br><p> ( ".current_companyCode()." ) ".ucwords($this->common_data['company_data']['company_name'])." issues this Purchasing Document detailed below through the iSupplier
                    Portal. you can view the document at your discretion.</p><br><br><a href='$link'>Click here to access the portal online.</a><br><br><p>For more detail contact the supply chain department.</p><br><p>Thank You</p></div>";
                            
            
                    if (!empty($masterRecordSupplier)) {
                        if (!empty($masterRecordSupplier['supplierEmail'])) {
                            
                            //send_custom_email("milindahasaranga@gmail.com", "Milee", "INVOICE");
                            //exit;
            
                            //$this->load->library('email_manual');
                            $config['charset'] = "utf-8";
                            
            
                            $config['mailtype'] = "html";
                            $config['wordwrap'] = TRUE;
                            $config['protocol'] = 'smtp';
                            $config['smtp_host'] = $this->config->item('email_smtp_host');
                            $config['smtp_user'] = $this->config->item('email_smtp_username');
                            $config['smtp_pass'] = $this->config->item('email_smtp_password');
                            //$config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
                            $config['smtp_crypto'] = 'tls';
                            $config['smtp_port'] = '587';
                            $config['SMTPOptions'] =  array (
                                'ssl' => array(
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                    'allow_self_signed' => true
                                )
                            );
                            $config['crlf'] = "\r\n";
                            $config['newline'] = "\r\n";
                            $this->load->library('email', $config);
                        
                            $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME);
                        
            
                            $this->email->to($masterRecordSupplier['supplierEmail']);                  
                            $this->email->subject($emailsubject);
                            $this->email->message($emailbody);
                            $result = $this->email->send();
                            //$err1 = $this->email->print_debugger();
                            //echo $err1;
                            //var_dump($result);
                            if ($result) {
                                $data['isRfqEmailed'] = 1;
                                $this->db->where('supplierID', $supplierID);
                                $this->db->where('inquiryMasterID', $inquiryMasterID);
                                $update = $this->db->update('srp_erp_srm_orderinquirydetails', $data);
                            }
                            return array('s', 'RFQ Email Send Successfully');
                        } else {
                            return array('e', 'No Email ID Found for supplier');
                        }
                    }else{
                        return array('e', 'RFQ Email Not Send');
                    }
               }else{
                return array('e', 'RFQ Email Not Send');
               }
    
            } else {
                return array('e', 'No Supplier Records Found');
            }
        }else{
            return array('e', 'RFQ Email Not Send');
        }
        
    }


    function get_rfq_supplier_link()
    {

        $supplierID = trim($this->input->post('supplierID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where('inquiryID', $inquiryMasterID);
       // $this->db->where('isRfqSubmitted', 1);
        $data_master = $this->db->get()->row_array();

       // $length = 10;    
        //substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);
      
       // $link = "https://$_SERVER[HTTP_HOST]/supplierPortal/index.php?link=" . $inquiryMasterID . '_' . $supplierID . '_' . $companyID;

        $company_token = getLoginTokenCurrentCompany();
        $token_array_company=json_decode($company_token);
        
        if($token_array_company){
            if($token_array_company->success==true){

                $url = $companyID;
                $companyidcomp = base64_encode(($url));
                $qut = base64_encode(($inquiryMasterID));
                $sup = base64_encode(($supplierID));
                $token= base64_encode(($token_array_company->data->token));
                $companyCode1 = base64_encode(($companyCode));

                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                $link_base_url = "$protocol$_SERVER[HTTP_HOST]";
                $link_base_url_decode = base64_encode(($link_base_url));

                $this->db->select('*');
                $this->db->where('company_id', $companyID);
                $this->db->from('srp_erp_company');
                $data_company = $this->db->get()->row_array();

                $com_data['company_address1'] = $data_company['company_address1'];
                $com_data['company_address2'] = $data_company['company_address2'];
                $com_data['company_city'] = $data_company['company_city'];
                $com_data['company_country'] = $data_company['company_country'];
                $com_data['company_phone'] = $data_company['company_phone'];
                $com_data['company_email'] = $data_company['company_email'];
                $com_data['company_name'] = $data_company['company_name'];

                $companyDetails = json_encode(($com_data));
                $companyDetails = base64_encode(($companyDetails));
                //print_r($companyDetails1);exit;

                
                //$organizationemail = $this->db->query("SELECT srp_erp_crm_quotation.customerID,quotationPersonName,srp_erp_crm_quotation.quotationPersonEmail as orgemail  FROM `srp_erp_crm_quotation` LEFT JOIN srp_erp_crm_organizations on srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID WHERE srp_erp_crm_quotation.companyID = '{$companyID}' AND quotationAutoID = '{$quotationAutoID}'")->row_array();
                

                //$link = "'$_SERVER[HTTP_HOST]'/index.php/QuotationPortal/crm_quotation_view?comp=$companyidcomp&qut=$qut";
                $link2 = '/index.php/SrmVendorPortal/vendor_rfq_view?comp='.$companyidcomp.'&qut='.$qut.'&sup='.$sup.'&num='.$token.'&crf='.$companyCode1.'&ul='.$link_base_url_decode.'&dcom='.$companyDetails.' ';
                $link = $this->config->item('vendor_portal_api_base_url').$link2;
                    

                    
                    if (!empty($data_master)) {
                    // $link = $this->config->item('vendor_portal_url').'/common/vendor/' .$companyID  . '/' . $supplierID . '/' . $data_master['vendorUrlReferenceID'];          
                        //return $link;
                        return array('s', $link);
                    } else {
                        return array('e', 'Please contact our support team');
                    }

            }
        }else{
            return array('e', 'Please contact our support team');
        }

       
    }



    function load_inquiry_reviewHeader()
    {
        $convertFormat = convert_date_format_sql();
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $orderreviewID = $this->input->post('orderreviewID');


        if(!empty($orderreviewID)){
            $this->db->select('srp_erp_srm_orderinquirymaster.inquiryType');
            $this->db->where('orderreviewID', $orderreviewID);
            $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'LEFT');
            $typ = $this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            if($typ['inquiryType']=='PRQ'){
                $this->db->select('customerName,srp_erp_srm_orderreviewmaster.narration,referenceNo as referenceNumber,srp_erp_srm_orderinquirymaster.inquiryType,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,srp_erp_segment.description as segdescription');
                $this->db->where('orderreviewID', $orderreviewID);
                $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'LEFT');
                $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_srm_orderinquirymaster.purchaseRequestID', 'LEFT');
                $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_purchaserequestmaster.segmentID', 'LEFT');
                $data = $this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }else{
                $this->db->select('CustomerName,srp_erp_srm_orderinquirymaster.customerID as CustomerAutoID,srp_erp_srm_orderreviewmaster.narration,referenceNo as referenceNumber');
                $this->db->where('orderreviewID', $orderreviewID);
                $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'LEFT');
                $data = $this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }


            return $data;
        }else{
            $this->db->select('inquiryType');
            $this->db->where('inquiryID', $inquiryID);
            $typ = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();

            if($typ['inquiryType']=='PRQ'){
                $this->db->select('*,DATE_FORMAT(srp_erp_srm_orderinquirymaster.documentDate,\'' . $convertFormat . '\') AS documentDate,CustomerName,srp_erp_segment.segmentID,srp_erp_segment.segmentCode,srp_erp_segment.description as segdescription');
                $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_orderinquirymaster.customerID', 'LEFT');
                $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_srm_orderinquirymaster.purchaseRequestID', 'LEFT');
                $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_purchaserequestmaster.segmentID', 'LEFT');
                $this->db->where('inquiryID', $inquiryID);
                $data = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();
                return $data;
            }else{
                $this->db->select('*,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,CustomerName');
                $this->db->join('srp_erp_srm_customermaster', 'srp_erp_srm_customermaster.CustomerAutoID = srp_erp_srm_orderinquirymaster.customerID', 'LEFT');
                $this->db->where('inquiryID', $inquiryID);
                $data = $this->db->get('srp_erp_srm_orderinquirymaster')->row_array();
                return $data;
            }


        }


    }

    function generate_order_review_supplier()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];
        $inquiryID = trim($this->input->post('inquiryID') ?? '');
        $supplierIDs = $this->input->post('supplierReviewSync');

        if (!empty($supplierIDs)) {
            $data_master['isSelectedForPO'] = 0;
            $this->db->where('inquiryMasterID', $inquiryID);
            $update = $this->db->update('srp_erp_srm_orderinquirydetails', $data_master);
            if ($update) {
                foreach ($supplierIDs as $key => $supplierID) {
                    $autoID = explode('_', $supplierID);
                    $data['isSelectedForPO'] = 1;
                    $this->db->where('inquiryMasterID', $inquiryID);
                    $this->db->where('supplierID', $autoID[1]);
                    $this->db->where('itemAutoID', $autoID[0]);
                    $this->db->update('srp_erp_srm_orderinquirydetails', $data);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Order Review update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Order Review updated successfully ');
            }

        } else {
            return array('e', 'No Supplier Selected');
        }
    }

    function supplier_image_upload()
    {
        $this->db->trans_start();
        $output_dir = "uploads/srm/supplierimage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/srm", 007);
            mkdir("uploads/srm/supplierimage", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Supplier_' . trim($this->input->post('supplierAutoID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['supplierImage'] = $fileName;

        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        $this->db->update('srp_erp_srm_suppliermaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Image uploaded  Successfully.');
        }
    }


    function delete_customer_order_review()
    {
        $orderreviewID = trim($this->input->post('orderreviewID') ?? '');
        $this->db->delete('srp_erp_srm_orderreviewmaster', array('orderreviewID' => $orderreviewID));
        $this->db->delete('srp_erp_srm_orderreviewdetails', array('orderreviewID' => $orderreviewID));
        return true;
    }

    function insert_review_detail(){
        $valu=$this->input->post('valu');
        $actn=$this->input->post('actn');
        $orderreviewID=$this->input->post('orderreviewID');
        $autoID = explode('_', $valu);
        $itemAutoID=$autoID[0];
        $supplierID=$autoID[1];
        $inquiryDetailID=$autoID[2];
        $companyID=current_companyID();

        $this->db->select('*');
        $this->db->where('inquiryDetailID', $inquiryDetailID);
        $data = $this->db->get('srp_erp_srm_orderinquirydetails')->row_array();

        if($actn=='checked'){
           // $this->db->delete('srp_erp_srm_orderreviewdetails', 'itemAutoID=' . $itemAutoID . ' AND orderreviewID=' . $orderreviewID . ' AND companyID=' . $companyID);

            $dataD['orderreviewID'] = $orderreviewID;
            $dataD['supplierID'] = $supplierID;
            $dataD['itemAutoID'] = $itemAutoID;
            $dataD['inquiryDetailID'] = $inquiryDetailID;

            $dataD['companyID'] = $companyID;
            $dataD['createdUserGroup'] = $this->common_data['user_group'];
            $dataD['createdPCID'] = $this->common_data['current_pc'];
            $dataD['createdUserID'] = $this->common_data['current_userID'];
            $dataD['createdUserName'] = $this->common_data['current_user'];
            $dataD['createdDateTime'] = $this->common_data['current_date'];
            $result=$this->db->insert('srp_erp_srm_orderreviewdetails', $dataD);

            if($result){
                return array('s', 'Added successfully.');
            }else{
                return array('e', 'Adding detail failed.');
            }

        }else{
            $result=$this->db->delete('srp_erp_srm_orderreviewdetails', 'itemAutoID=' . $itemAutoID . ' AND supplierID=' . $supplierID . ' AND orderreviewID=' . $orderreviewID . ' AND companyID=' . $companyID);

            if($result){
                return array('s', 'Successfully removed.');
            }else{
                return array('e', 'Removing detail failed.');
            }
        }
    }

    function insert_review_detail_supplier_base(){
        $valu=$this->input->post('valu');
        $actn=$this->input->post('actn');
        $orderreviewID=$this->input->post('orderreviewID');
        $autoID = explode('_', $valu);
       // $itemAutoID=$autoID[0];
        $supplierID=$autoID[0];
        $inquiryMasterID=$autoID[1];
        $companyID=current_companyID();

        $this->db->select('*');
        $this->db->where('inquiryMasterID', $inquiryMasterID);
        $this->db->where('supplierID', $supplierID);
        $this->db->where('isSupplierSubmited', 1);
        $data = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

        if($actn=='checked'){

            foreach($data as $val){

                //$this->db->delete('srp_erp_srm_orderreviewdetails', 'itemAutoID=' . $val['itemAutoID'] . ' AND orderreviewID=' . $orderreviewID . ' AND companyID=' . $companyID);

                $dataD['orderreviewID'] = $orderreviewID;
                $dataD['supplierID'] = $supplierID;
                $dataD['itemAutoID'] = $val['itemAutoID'];
                $dataD['inquiryDetailID'] = $val['inquiryDetailID'];

                $dataD['companyID'] = $companyID;
                $dataD['createdUserGroup'] = $this->common_data['user_group'];
                $dataD['createdPCID'] = $this->common_data['current_pc'];
                $dataD['createdUserID'] = $this->common_data['current_userID'];
                $dataD['createdUserName'] = $this->common_data['current_user'];
                $dataD['createdDateTime'] = $this->common_data['current_date'];
                $result=$this->db->insert('srp_erp_srm_orderreviewdetails', $dataD);
            }

            // if($result){
                return array('s', 'Added successfully.');
            // }else{
            //     return array('e', 'Adding detail failed.');
            // }

        }else{
            foreach($data as $val){
                $result=$this->db->delete('srp_erp_srm_orderreviewdetails', 'itemAutoID=' . $val['itemAutoID'] . ' AND supplierID=' . $supplierID . ' AND orderreviewID=' . $orderreviewID . ' AND companyID=' . $companyID);
            }

            // if($result){
                return array('s', 'Successfully removed.');
            // }else{
            //     return array('e', 'Removing detail failed.');
            // }
        }
    }

    function confirm_order_review(){
        $this->db->select('orderreviewID');
        $this->db->where('orderreviewID', trim($this->input->post('orderreviewID') ?? ''));
        $this->db->from('srp_erp_srm_orderreviewdetails');
        $companyid = current_companyID();
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            return array('w', 'There are no records to confirm this document!');
        } else {
            $this->db->select('orderreviewID');
            $this->db->where('orderreviewID', trim($this->input->post('orderreviewID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_srm_orderreviewmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                return array('w', 'Document already confirmed');
            } else {
                $orderreviewID = trim($this->input->post('orderreviewID') ?? '');

                $this->load->library('Approvals');
                $this->db->select('documentID,currentLevelNo,documentSystemCode,orderreviewID,createdDateTime,inquiryID');
                $this->db->where('orderreviewID', trim($this->input->post('orderreviewID') ?? ''));
                $this->db->from('srp_erp_srm_orderreviewmaster');
                $c_data = $this->db->get()->row_array();

                $company_id=current_companyID();
                $contractDate=$c_data['createdDateTime'];

                $financeYearID = $this->db->query("SELECT companyFinanceYearID FROM srp_erp_companyfinanceperiod WHERE companyID = {$company_id} 
                                                   AND '{$contractDate}' BETWEEN dateFrom AND dateTo")->row('companyFinanceYearID');

                $contr_year = date('Y', strtotime($contractDate));
                $contr_month = date('m', strtotime($contractDate));

                $contract_code = $this->sequence->sequence_generator_fin($c_data['documentID'],$financeYearID,$contr_year,$contr_month);

                $datas['documentSystemCode'] = $contract_code;
                $this->db->where('orderreviewID', trim($this->input->post('orderreviewID') ?? ''));
                $this->db->update('srp_erp_srm_orderreviewmaster', $datas);

                $autoApproval= get_document_auto_approval($c_data['documentID']);

                //print_r($autoApproval);exit;
                $msg='';
                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($c_data['orderreviewID'], 'srp_erp_srm_orderreviewmaster','orderreviewID', $c_data['documentID'],$contract_code, $c_data['createdDateTime']);
                
                    $autoApprovalsData = $this->save_order_review_auto_with_po_generate(0,$c_data['orderreviewID']);
                    $msg = "PO Created Successfully.";
                }elseif($autoApproval==1){
                    $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['orderreviewID'], $contract_code, $c_data['documentID'], 'srp_erp_srm_orderreviewmaster', 'orderreviewID', 1, $c_data['createdDateTime']);
                    $msg = "Approvals Created Successfully.";
                }else{
                    return array('e', 'Approval levels are not set for this document');
                    exit;
                }

                if ($approvals_status) {
                    $data = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],
                    );
                    $this->db->where('orderreviewID', trim($this->input->post('orderreviewID') ?? ''));
                    $this->db->update('srp_erp_srm_orderreviewmaster', $data);

                    $orderinquirymaster['isOrderReviewConfirmYN']=1;
                    $orderinquirymaster['orderreviewID']=trim($this->input->post('orderreviewID') ?? '');

                    $this->db->where('inquiryID', $c_data['inquiryID']);
                    $this->db->update('srp_erp_srm_orderinquirymaster', $orderinquirymaster);

                    return array('s', $msg);
                } else {
                    /*return false;*/
                    return array('e', 'oops, something went wrong!.');
                }

            }
        }
    }


    function fetch_ordrew_template_data($orderreviewID)
    {
        $convertFormat = convert_date_format_sql();
        $currentdate = $this->common_data['current_date'];
        $companyid = current_companyID();
        $date_format_policy = date_format_policy();
        $convertFormat = convert_date_format_sql();
        $datefromconvert = input_format_date($currentdate, $date_format_policy);

        $this->db->select('srp_erp_srm_orderreviewmaster.*,DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS contractDate,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn');
        $this->db->where('orderreviewID', $orderreviewID);
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $data['master'] = $this->db->get()->row_array();


        $this->db->select('srp_erp_unit_of_measure.UnitShortCode as UnitShortCode,srp_erp_srm_suppliermaster.supplierName as supplierName,srp_erp_srm_orderinquirydetails.supplierQty as supplierQty,srp_erp_srm_orderinquirydetails.supplierPrice as supplierPrice,srp_erp_itemmaster.itemName as itemName,srp_erp_itemmaster.itemImage as itemImage,FORMAT(srp_erp_srm_orderinquirydetails.requestedQty,0) as requestedQtyformated, srp_erp_srm_orderinquirydetails.requestedQty AS requestedQtyNotFormated,DATE_FORMAT( srp_erp_srm_orderinquirydetails.expectedDeliveryDate, \'' . $convertFormat . ' %h:%i:%s\' ) as deliverydate');
        $this->db->where('orderreviewID', $orderreviewID);
        $this->db->join('srp_erp_srm_orderinquirydetails', 'srp_erp_srm_orderreviewdetails.inquiryDetailID = srp_erp_srm_orderinquirydetails.inquiryDetailID','left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_itemmaster.itemAutoID = srp_erp_srm_orderreviewdetails.itemAutoID','left');
        $this->db->join('srp_erp_srm_suppliermaster', 'srp_erp_srm_suppliermaster.supplierAutoID = srp_erp_srm_orderreviewdetails.supplierID','left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_srm_orderinquirydetails.defaultUOMID','left');
        $this->db->from('srp_erp_srm_orderreviewdetails');
        $data['detail'] = $this->db->get()->result_array();


        return $data;
    }



    function save_order_review_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {


        if($autoappLevel==1) {
            $system_code = trim($this->input->post('orderreviewID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
            $code = trim($this->input->post('code') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['orderreviewID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $this->db->where('orderreviewID', $system_code);
        $code = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where('inquiryID', $code['inquiryID']);
        $ordrinq = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderreviewdetails');
        $this->db->where('orderreviewID', $system_code);
        $this->db->group_by("supplierID");
        $supplierIDs = $this->db->get()->result_array();

        $supnames=array();
        foreach ($supplierIDs as $supvl){

            $this->db->select('*');
            $this->db->from('srp_erp_srm_suppliermaster');
            $this->db->where('supplierAutoID', $supvl['supplierID']);
            $supplierIDerp = $this->db->get()->row_array();

            if(empty($supplierIDerp['erpSupplierAutoID']) || $supplierIDerp['erpSupplierAutoID']==null || $supplierIDerp['erpSupplierAutoID']==''){
                array_push($supnames,$supplierIDerp['supplierName']);
            }
        }

        if(!empty($supnames)){
            $jnsup=join(" , ",$supnames);
            $this->session->set_flashdata('e', 'Following suppliers are not linked. '.$jnsup.'');
            return false;
            exit;
        }

        $this->db->trans_start();
        $this->load->library('Approvals');

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $code['documentID']);
        }

        if($approvals_status == 3){
            $inquiryMasterID_now = trim($this->input->post('inquiryMasterID') ?? '');

            $datanow['isOrderReviewConfirmYN'] = 0;
                        
            $this->db->where('inquiryID', $inquiryMasterID_now);
            $this->db->update('srp_erp_srm_orderinquirymaster', $datanow);
        }

        if ($approvals_status == 1) {

            $data_master['isSelectedForPO'] = 0;
            $this->db->where('inquiryMasterID', $code['inquiryID']);
            $update = $this->db->update('srp_erp_srm_orderinquirydetails', $data_master);
            if ($update) {
                $this->db->select('*');
                $this->db->from('srp_erp_srm_orderreviewdetails');
                $this->db->where('orderreviewID', $system_code);
                $codeD = $this->db->get()->result_array();

                foreach ($codeD as  $vl) {
                    $datad['isSelectedForPO'] = 1;
                    $this->db->where('inquiryMasterID', $code['inquiryID']);
                    $this->db->where('supplierID', $vl['supplierID']);
                    $this->db->where('itemAutoID', $vl['itemAutoID']);
                    $this->db->update('srp_erp_srm_orderinquirydetails', $datad);
                }
            }





            $orderreviewID = $system_code;

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
                $datapdf['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }else{
                $this->db->select('srp_erp_srm_orderreviewmaster.*,srp_erp_srm_orderinquirymaster.documentCode as inquirycode');
                $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'left');
                $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID', $orderreviewID);
                $datapdf['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }

            $inquiryID = $datapdf['reviewmaster']['inquiryID'];

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
            $datapdf['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

            foreach($datapdf['item'] as $key => $val){
                $datapdf['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$datapdf['item'][$key]['itemImage'], '1 hour');
            }
            $datapdf['supplierIDarr'] = $suparr;
            $datapdf['logo']=mPDFImage;
            if($this->input->post('html')){
                $datapdf['logo']=htmlImage;
            }


            foreach ($supplierIDs as $suvl){

                $this->db->select('*');
                $this->db->from('srp_erp_srm_suppliermaster');
                $this->db->where('supplierAutoID', $suvl['supplierID']);
                $suppliermstr = $this->db->get()->row_array();

                $this->db->select('*');
                $this->db->from('srp_erp_srm_orderinquirydetails');
                $this->db->where('inquiryDetailID', $suvl['inquiryDetailID']);
                $orderincdtl = $this->db->get()->row_array();

                $ship_data = fetch_address_po(trim($this->input->post('shippingAddressID') ?? ''));

                $data['documentID'] = 'PO';
                $data['narration'] = 'SRM based PO';
                $data['supplierPrimaryCode'] = $suppliermstr['erpSupplierAutoID'];
                $data['purchaseOrderType'] = 'Standard';
                $data['referenceNumber'] = $code['documentSystemCode'];
                $data['creditPeriod'] = $suppliermstr['supplierCreditPeriod'];
                $data['supplierID'] = $suppliermstr['erpSupplierAutoID'];
                $data['supplierCode'] = $suppliermstr['supplierSystemCode'];
                $data['supplierName'] = $suppliermstr['supplierName'];
                $data['supplierAddress'] = $suppliermstr['supplierAddress1'] . ' ' . $suppliermstr['supplierAddress2'];
                $data['supplierTelephone'] = $suppliermstr['supplierTelephone'];
                $data['supplierFax'] = $suppliermstr['supplierFax'];
                $data['supplierEmail'] = $suppliermstr['supplierEmail'];
                $data['expectedDeliveryDate'] = $orderincdtl['expectedDeliveryDate'];
                $data['documentDate'] = current_date();

                $data['shippingAddressID'] = $ship_data['addressID'];
                $data['shippingAddressDescription'] = $ship_data['addressDescription'];
                $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
                $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
                $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
                $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];


                $segment_arr_default = default_segment_drop();
                $segment = explode('|', $segment_arr_default);
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');
                $trans_currency = currency_conversionID($ordrinq['transactionCurrencyID'], $ordrinq['transactionCurrencyID']);
                $data['transactionCurrencyID'] = $ordrinq['transactionCurrencyID'];
                $data['transactionCurrency'] = trim($trans_currency['CurrencyCode'] ?? '');
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data['termsandconditions'] = trim($this->input->post('Note') ?? '');
                $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $data['supplierCurrencyID'] = $suppliermstr['supplierCurrencyID'];
                $data['supplierCurrency'] = $suppliermstr['supplierCurrency'];
                $data['isSrmCreated'] = 1;
                $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
                $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                //$data['purchaseOrderCode'] = $this->sequence->sequence_generator($data['documentID']);
                $pomaster=$this->db->insert('srp_erp_purchaseordermaster', $data);
                $po_id = $this->db->insert_id();

                //add pr document on po
                $po_doc_sub_id =$code['inquiryID'].'_'.$this->common_data['company_data']['company_id'].'_'.$suvl['supplierID'];
                $this->db->select('*');
                $this->db->from('srp_erp_documentattachments');
                $this->db->where('documentSubID', $po_doc_sub_id);
                $ordrinqdocuments = $this->db->get()->result_array();

                if(count($ordrinqdocuments)>0){

                    foreach($ordrinqdocuments as $doc_pr){

                        $dataattFromPr['documentID'] = 'PO';
                        $dataattFromPr['documentSystemCode'] = $po_id;
                        $dataattFromPr['attachmentDescription'] = 'PR Attachment';
                        $dataattFromPr['myFileName'] = $doc_pr['myFileName'];
                        $dataattFromPr['fileType'] = $doc_pr['fileType'];
                        $dataattFromPr['fileSize'] = $doc_pr['fileSize'];
                        $dataattFromPr['timestamp'] = date('Y-m-d H:i:s');
                        $dataattFromPr['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataattFromPr['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataattFromPr['createdUserGroup'] = $this->common_data['user_group'];
                        $dataattFromPr['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataattFromPr['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataattFromPr['modifiedUserName'] = $this->common_data['current_user'];
                        $dataattFromPr['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataattFromPr['createdPCID'] = $this->common_data['current_pc'];
                        $dataattFromPr['createdUserID'] = $this->common_data['current_userID'];
                        $dataattFromPr['createdUserName'] = $this->common_data['current_user'];
                        $dataattFromPr['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_documentattachments', $dataattFromPr);
                    }

                }

                if($pomaster){

                    $this->db->select('*');
                    $this->db->from('srp_erp_srm_orderreviewdetails');
                    $this->db->where('orderreviewID', $system_code);
                    $this->db->where('supplierID', $suvl['supplierID']);
                    $ordrDqry = $this->db->get()->result_array();

                    foreach ($ordrDqry as $dtlval){
                        $item_arr = fetch_item_data($dtlval['itemAutoID']);

                        $this->db->select('*');
                        $this->db->from('srp_erp_srm_orderinquirydetails');
                        $this->db->where('inquiryDetailID', $dtlval['inquiryDetailID']);
                        $orderincdtl = $this->db->get()->row_array();

                        $this->db->select('UnitShortCode');
                        $this->db->where('UnitID', $orderincdtl['defaultUOMID']);
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $masterUnitcod = $this->db->get('srp_erp_unit_of_measure')->row('UnitShortCode');

                        $dataD['purchaseOrderID'] = $po_id;
                        $dataD['itemAutoID'] = $dtlval['itemAutoID'];
                        $dataD['itemSystemCode'] = $item_arr['itemSystemCode'];
                        $dataD['itemType'] = $item_arr['mainCategory'];
                        $dataD['itemDescription'] = $item_arr['itemDescription'];
                        $dataD['unitOfMeasure'] = $masterUnitcod;
                        $dataD['unitOfMeasureID'] = $orderincdtl['defaultUOMID'];
                        $dataD['detailExpectedDeliveryDate'] = $orderincdtl['expectedDeliveryDate'];
                        $dataD['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                        $dataD['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                        $dataD['conversionRateUOM'] = conversionRateUOM_id($dataD['unitOfMeasureID'], $dataD['defaultUOMID']);
                        $dataD['requestedQty'] = $orderincdtl['supplierQty'];
                       // $dataD['unitAmount'] = $orderincdtl['supplierPrice'];
                      //  $dataD['discountAmount'] = $orderincdtl['supplierDiscount'];

                        $dataD['discountPercentage'] = $orderincdtl['supplierDiscountPercentage'];
                        //$dataD['discountAmount'] = $orderincdtl['supplierDiscount'];
                        $dataD['discountAmount'] = ($orderincdtl['supplierPrice']*$orderincdtl['supplierDiscountPercentage'])/100;
                    //  $data['requestedQty'] = $quantityRequested[$key];
                        $dataD['unitAmount'] = ($orderincdtl['supplierPrice'] - $dataD['discountAmount']);
                        $dataD['totalAmount'] = ($dataD['unitAmount'] * $orderincdtl['supplierQty']);
                        
                       // $dataD['totalAmount'] = ($dataD['unitAmount'] * $orderincdtl['supplierQty']);
                        $dataD['comment'] = 'pulled from SRM';
                        $dataD['remarks'] = '';
                        $dataD['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataD['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataD['GRVSelectedYN'] = 0;
                        $dataD['goodsRecievedYN'] = 0;
                        $dataD['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataD['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataD['modifiedUserName'] = $this->common_data['current_user'];
                        $dataD['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataD['createdUserGroup'] = $this->common_data['user_group'];
                        $dataD['createdPCID'] = $this->common_data['current_pc'];
                        $dataD['createdUserID'] = $this->common_data['current_userID'];
                        $dataD['createdUserName'] = $this->common_data['current_user'];
                        $dataD['createdDateTime'] = $this->common_data['current_date'];
                        $podetail=$this->db->insert('srp_erp_purchaseorderdetails', $dataD);
                    }



                    if($podetail){
                        $this->load->library('Approvals');
                        $this->load->library('sequence');
                        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
                        $currentuser = current_userID();
                        $companyID = $this->common_data['company_data']['company_id'];
                        $locationemployee = $this->common_data['emplanglocationid'];


                        ////pr value
                        $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
                        ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                                            FROM srp_erp_purchaseordermaster
                                                LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                                                LEFT JOIN (
                                                        SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                                        FROM srp_erp_taxledger 
                                                        WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                                        GROUP BY documentMasterAutoID 
                                                ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                                            WHERE
                                                srp_erp_purchaseordermaster.purchaseOrderID = {$po_id} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();
                        
                        $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];
                                   
                        
                        $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_purchaseordermaster where purchaseOrderID = $po_id AND companyID = {$companyID}")->row_array();


                        $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentID,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
                        $this->db->where('purchaseOrderID', $po_id);
                        $this->db->from('srp_erp_purchaseordermaster');
                        $po_data = $this->db->get()->row_array();
                        $docDate = $po_data['documentDate'];


                        $companyFinanceYearID = $this->db->query("SELECT
                            period.companyFinanceYearID as companyFinanceYearID
                        FROM
                            srp_erp_companyfinanceperiod period
                        WHERE
                            period.companyID = $companyID
                        AND '$docDate' BETWEEN period.dateFrom
                        AND period.dateTo
                        AND period.isActive = 1")->row_array();

                        if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                            $companyFinanceYearID['companyFinanceYearID'] = NULL;
                        }

                        if ($locationwisecodegenerate == 1) {
                            $this->db->select('locationID');
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location == '')) {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            } else {
                                if ($locationemployee != '') {
                                    $codegeratorpo = $this->sequence->sequence_generator_location($data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $locationemployee, $po_data['invYear'], $po_data['invMonth']);
                                } else {
                                    $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                    return false;
                                }

                            }


                        } else {
                            $codegeratorpo = $this->sequence->sequence_generator_fin($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $po_data['invYear'], $po_data['invMonth']);
                        }


                        $pvCd = array(
                            'purchaseOrderCode' => $codegeratorpo
                        );
                        $this->db->where('purchaseOrderID', $po_id);
                        $this->db->update('srp_erp_purchaseordermaster', $pvCd);

                        $approvals_status = $this->approvals->CreateApproval('PO', $po_data['purchaseOrderID'], $codegeratorpo, 'Purchase Order', 'srp_erp_purchaseordermaster', 'purchaseOrderID', 0, $po_data['documentDate'],$segmentID['segmentID'],$poLocalAmount);


                    }
                    $imageuploadlocal = $this->config->item('ftp_image_uplod_local');
                    if($imageuploadlocal == 2){
                        $this->db->select('companyID');
                        $this->db->where('documentID', 'PO');
                        $num = $this->db->get('srp_erp_documentattachments')->result_array();
                        $file_name = 'PO' . '_' . $po_id . '_' . (count($num) + 1);


                        $cc = $this->common_data['company_data']['company_code'];
                        $folderPath = !empty($cc) ? $cc . '/' : '';
                        $config['upload_path'] = realpath(APPPATH . '../' . $folderPath . '/');
                        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
                        $config['max_size'] = '5120'; // 5 MB
                        $config['file_name'] = $file_name;
                        $cc = current_companyCode();
                        $file_name = "$file_name";
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload($path)) {
                           // die(json_encode(['e', "Upload failed"] . $this->upload->display_errors()));
                        } else {
                            $upload_data = $this->upload->data();
                        }
                        $flsiz=filesize($path);
                    }else{
                        $this->db->select('companyID');
                        $this->db->where('documentID', 'PO');
                        $num = $this->db->get('srp_erp_documentattachments')->result_array();
                        $file_name = 'PO' . '_' . $po_id . '_' . (count($num) + 1);
                        $config['upload_path'] = realpath(APPPATH . '../attachments');
                        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
                        $config['max_size'] = '5120'; // 5 MB
                        $config['file_name'] = $file_name;

                        $flsiz=filesize($path);
                        $input = $this->s3->inputFile($path);
                        $cc = current_companyCode();
                        $folderPath = !empty($cc) ? $cc . '/' : '';
                        if ($this->s3->putMyObject($input, $folderPath . $file_name . '.' . 'pdf')) {
                            $s3Upload = true;
                        } else {
                            $s3Upload = false;
                        }

                        /** end of s3 integration */
                    }


                    $dataatt['documentID'] = 'PO';
                    $dataatt['documentSystemCode'] = $po_id;
                    $dataatt['attachmentDescription'] = 'Automatic Attachment';
                    $dataatt['myFileName'] = $folderPath . $file_name . '.' . 'pdf';
                    $dataatt['fileType'] = trim('pdf');
                    $dataatt['fileSize'] = $flsiz;
                    $dataatt['timestamp'] = date('Y-m-d H:i:s');
                    $dataatt['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataatt['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataatt['createdUserGroup'] = $this->common_data['user_group'];
                    $dataatt['modifiedPCID'] = $this->common_data['current_pc'];
                    $dataatt['modifiedUserID'] = $this->common_data['current_userID'];
                    $dataatt['modifiedUserName'] = $this->common_data['current_user'];
                    $dataatt['modifiedDateTime'] = $this->common_data['current_date'];
                    $dataatt['createdPCID'] = $this->common_data['current_pc'];
                    $dataatt['createdUserID'] = $this->common_data['current_userID'];
                    $dataatt['createdUserName'] = $this->common_data['current_user'];
                    $dataatt['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $dataatt);

                    // send po in vendor poratal

                    $this->db->select('*');
                    $this->db->where('purchaseOrderID', $po_id);
                    $this->db->from('srp_erp_purchaseordermaster');
                    $data_po_master['master'] = $this->db->get()->row_array();

                    $data_po_master['master']['srmSupplierID'] = $suvl['supplierID'];
                    $data_po_master['master']['company_name'] = $this->common_data['company_data']['company_name'];
                    $data_po_master['master']['company_address1'] = $this->common_data['company_data']['company_address1'];
                    $data_po_master['master']['company_address2'] = $this->common_data['company_data']['company_address2'];
                    $data_po_master['master']['company_city'] = $this->common_data['company_data']['company_city'];
                    $data_po_master['master']['company_province'] = $this->common_data['company_data']['company_province'];
                    $data_po_master['master']['company_country'] = $this->common_data['company_data']['company_country'];
                    $data_po_master['master']['company_code'] = $this->common_data['company_data']['company_code']; 
                    $data_po_master['master']['inquiryID'] = $code['inquiryID'];
                    $data_po_master['master']['purchaseOrderIDBackend'] = $po_id;

                    $master_n = [
                        "inquiryID"=> $code['inquiryID'],
                        "supplierID"=>$suvl['supplierID'],
                        "companyID"=>$this->common_data['company_data']['company_id'],
                    ]; 

                    $data_po_master['rfq'] = $master_n;

                    $this->db->select('*');
                    $this->db->from('srp_erp_purchaseorderdetails');
                    $this->db->where('purchaseOrderID', $po_id);
                    $data_po_master['details'] = $this->db->get()->result_array();

                    foreach($data_po_master['details'] as $key1=>$val_data){
                        $data_po_master['details'][$key1]['erpBackendID'] = $val_data['purchaseOrderDetailsID'];
                        $data_po_master['details'][$key1]['erpBackendMasterID'] = $po_id;
                    }

                    $this->db->select('*');
                    $this->db->from('srp_erp_documentattachments');
                    $this->db->where('documentSystemCode', $po_id);
                    $data_po_master['document'] = $this->db->get()->row_array();

                    $purchaseOrderID = $po_id;
                   // $data_po_master['extra'] = $this->Procurement_modal->fetch_po_template_data_for_supplier_portal($purchaseOrderID);
                
                   // $data_po_master['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID')!=''?existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID'):0);

                    $token = $this->getLoginToken();

                    $token_array=json_decode($token);
            
                    if($token_array){
            
                        if($token_array->success==true){
                        
                            $res=$this->saveCreatePoSupplierSide($data_po_master,$token_array->data->token);

                           // print_r($res);exit;
            
                            $res_array=json_decode($res);

                            if($res_array->status==true){
                                $data_detail1['isPortalPOSubmitted'] = 1;
                        
                                $this->db->where('purchaseOrderID', $purchaseOrderID);
                                $this->db->update('srp_erp_purchaseordermaster', $data_detail1);

                               // $this->send_company_approve_email_supplier($suvl['supplierID'],2);
                            }
                        }
                    }
                    // $res=$this->saveCreatePoSupplierSide($data_po_master);

                    // $res_array=json_decode($res);

                    // if($res_array->action_status==true){
                    //     $data_detail1['isPortalPOSubmitted'] = 1;
                        
                    //     $this->db->where('purchaseOrderID', $purchaseOrderID);
                    //     $this->db->update('srp_erp_purchaseordermaster', $data_detail1);

                    //     $this->send_company_approve_email_supplier($suvl['supplierID'],2);

                    // }

                }
            }

            unlink($path);
            $this->session->set_flashdata('s', 'Approved Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {

            $this->db->trans_commit();
            return true;
        }
    }

    public function saveCreatePoSupplierSide($master,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/save_supplier_po',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodysaveCreatePoSupplierSide($master),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodysaveCreatePoSupplierSide($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];
         return json_encode($jayParsedAry);
    }


    function add_suppliers()
    {
        $result = $this->db->query('INSERT INTO srp_erp_srm_suppliermaster ( erpSupplierAutoID,supplierSystemCode, supplierName, partyCategoryID, supplierAddress1, supplierAddress2, supplierCountry, supplierTelephone, supplierEmail, supplierUrl, supplierFax, secondaryCode, supplierCurrencyID, supplierCurrency, supplierCurrencyDecimalPlaces,supplierCreditPeriod,supplierCreditLimit,isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` ) 
                                                                           SELECT supplierAutoID, supplierSystemCode, supplierName, partyCategoryID, supplierAddress1, supplierAddress2, supplierCountry, supplierTelephone, supplierEmail, supplierUrl, supplierFax, secondaryCode, supplierCurrencyID, supplierCurrency, supplierCurrencyDecimalPlaces,supplierCreditPeriod,supplierCreditLimit,isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` 
 FROM srp_erp_suppliermaster 
                                WHERE companyID = ' . current_companyID() . '  AND supplierAutoID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function save_my_chat(){

        $this->db->trans_start();

        $chat_msg = trim($this->input->post('chat_msg') ?? '');
        $inquiryDetailID = trim($this->input->post('inquiryDetailID') ?? '');
        $inquiryMasterID = trim($this->input->post('inquiryMasterID') ?? '');
        $supplierID = trim($this->input->post('supplierID') ?? '');
        $chatType = trim($this->input->post('chatType') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $documentID = trim($this->input->post('documentID') ?? '');

        $data_chat['inquiryDetailID'] = $inquiryDetailID;
        $data_chat['inquiryMasterID'] = $inquiryMasterID;
        $data_chat['supplierID'] = $supplierID;
        $data_chat['chatType'] = $chatType;
        $data_chat['itemAutoID'] = $itemAutoID;
        $data_chat['documentID'] = $documentID;
        $data_chat['companyID'] = $this->common_data['company_data']['company_id'];
        $data_chat['message'] = $chat_msg;
        $data_chat['isSrm'] = 0;
        $data_chat['companyCode'] = $this->common_data['company_data']['company_code'];
        $data_chat['createdPCID'] = $this->common_data['current_pc'];
        $data_chat['createdUserID'] = $this->common_data['current_userID'];
        $data_chat['createdUserName'] = $this->common_data['current_user'];
        $data_chat['createdDateTime'] = $this->common_data['current_date'];

        $this->db->insert('srp_erp_srm_vendor_chat', $data_chat);

        $last_id = $this->db->insert_id();
        
        $token = $this->getLoginToken();

        $token_array=json_decode($token);

        if($token_array){

            if($token_array->success==true){
            
                $res=$this->sendChatForSRM($data_chat,$token_array->data->token);

                //print_r($res);exit;

                $res_array=json_decode($res);

                if($res_array->status==true){
                    $data_detail1['isSubmitted'] = 1;
            
                    $this->db->where('chatAutoID', $last_id);
                    $this->db->update('srp_erp_srm_vendor_chat', $data_detail1);
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Failed." . $this->db->_error_message());
        } else {

            $this->db->trans_commit();
            return array('s', 'send');
        }
    }


    public function sendChatForSRM($master,$token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/index.php/Api_ecommerce/save_supplier_chat',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodysendChatForSRM($master),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }


    public function getBodysendChatForSRM($master){

        $jayParsedAry = [
            "results" => [
                  "dataMaster" => $master
                  
               ] 
         ];
         return json_encode($jayParsedAry);
    }

    function re_upload_vendor_reject_document($master){
        $this->db->trans_start();

        $data_doc['url'] = $master['url'];
        $data_doc['approveYN'] = 0;

        $this->db->where('reqDocID', $master['id']);
        $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_doc);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }

    }

    function save_supplier_chat($master){
     
        $data_chat['inquiryDetailID'] = $master['inquiryDetailID'];
        $data_chat['inquiryMasterID'] = $master['inquiryMasterID'];
        $data_chat['supplierID'] = $master['supplierID'];
        $data_chat['chatType'] = $master['chatType'];
        $data_chat['itemAutoID'] = $master['itemAutoID'];
        $data_chat['companyID'] = $master['companyID'];
        $data_chat['message'] = $master['message'];
        $data_chat['documentID'] = $master['documentID'];
        $data_chat['isSrm'] = 1;

        // $data_chat['createdPCID'] = $this->common_data['current_pc'];
        // $data_chat['createdUserID'] = $data_user['userID'];
        $data_chat['createdUserName'] = $master['createdUserName'];
        $data_chat['createdDateTime'] = $master['createdDateTime'];

        $this->db->insert('srp_erp_srm_vendor_chat', $data_chat);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return true;
        }

    }

    function save_supplier_line_refer_back($master){

        if($master['type']==3){
            $data_detail1['supplierTechnicalSpecification'] = $master['reply'];
            
            $this->db->where('inquiryDetailID', $master['inquiryDetailID']);
            $this->db->where('companyID', $master['companyID']);
            $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail1);

        }

        if($master['type']==1){
            $data_detail1['supplierQty'] = $master['reply'];
            $data_detail1['supplierDiscount'] =  $master['vendor_discount'];
            $data_detail1['supplierTax'] =  $master['vendor_tax'];
            $data_detail1['isSelected'] =  1;
            
            $this->db->where('inquiryDetailID', $master['inquiryDetailID']);
            $this->db->where('companyID', $master['companyID']);
            $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail1);

        }

        if($master['type']==5){
            $data_detail1['terms'] = $master['reply'];
            
            $this->db->where('inquiryID', $master['inquiryDetailID']);
            $this->db->where('companyID', $master['companyID']);
            $this->db->where('supplierID', $master['supplierID']);
            $this->db->update('srp_erp_vendor_terms', $data_detail1);

        }

        if($master['type']==2){
            $data_detail1['supplierPrice'] = $master['reply'];
            $data_detail1['supplierDiscount'] =  $master['vendor_discount'];
            $data_detail1['supplierTax'] =  $master['vendor_tax'];
            $data_detail1['isSelected'] =  1;
            
            $this->db->where('inquiryDetailID', $master['inquiryDetailID']);
            $this->db->where('companyID', $master['companyID']);
            $this->db->update('srp_erp_srm_orderinquirydetails', $data_detail1);

        }

        $data_detail2['isReSubmited'] = 2;
        $data_detail2['reply'] = $master['reply'] ;
        $this->db->where("autoID",$master['backendID']);
        $this->db->update('srp_erp_srm_vendor_refer_back_request', $data_detail2);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return true;
        }

    }

    function save_vendor_attachment($master){

        $data['documentID'] = $master['documentID'];
        $data['documentSystemCode'] = $master['erpBackendID'];
        $data['attachmentDescription'] = $master['attachmentDescription'];
        $data['myFileName'] = $master['myFileName'];
        $data['fileType'] = $master['fileType'];
        $data['fileSize'] = $master['fileSize'];
        $data['timestamp'] = $master['timestamp'];
        $data['companyID'] = $master['erpCompanyID'];
        $data['companyCode'] = $master['erpCompanyCode'];
       
        $this->db->insert('srp_erp_documentattachments', $data);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            
            return true;
        }

    }

    function get_supplier_po_status($sub){

        if($sub){

            $purchaseOrderIDBackend =[0];
            $companyIDs =[0];
            $company_codes =[0];
            foreach($sub as $val){
                $purchaseOrderIDBackend[]=$val['purchaseOrderIDBackend'];
                $companyIDs[]=$val['companyID'];
                $company_codes[]=$val['companyCode'];
            }

            $this->db->select('purchaseOrderID,companyID,companyCode,isReceived,closedYN');
            $this->db->where_in('companyCode', $company_codes);
            $this->db->where_in('companyID', $companyIDs);
            $this->db->where_in('purchaseOrderID', $purchaseOrderIDBackend);
            $supplier_po = $this->db->get('srp_erp_purchaseordermaster')->result_array();

            if($supplier_po){
                return array("status"=>true,"results"=>$supplier_po);
            }else{
                return array("status"=>false,"results"=>'');
            }
            
        }else{
            return array("status"=>false,"results"=>"");
        }

    }
    
    function save_supplier_po_close_details($sub)
    {
        $this->db->trans_start();
        $system_code = $sub['masterID'];

        if($sub['type']==1){
            $data['closedYN'] = 1;
            $data['approvedYN'] = 5;
            $data['isCloseRequestYN'] =2;
            $this->db->where('purchaseOrderID', $system_code);
            $this->db->where('companyID',  $sub['companyID']);
            $this->db->update('srp_erp_purchaseordermaster', $data);
        }

        if($sub['type']==2){
            // $data['closedYN'] = 1;
            // $data['approvedYN'] = 5;
            $data['isCloseRequestYN'] =3;
            $this->db->where('purchaseOrderID', $system_code);
            $this->db->where('companyID',  $sub['companyID']);
            $this->db->update('srp_erp_purchaseordermaster', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array("status"=>false,"results"=>'');
        } else {
            $this->db->trans_commit();
            
            return array("status"=>true,"results"=>'');
        }
    }
    
    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function save_supplier_grv($master_grv,$details_grv)
    {
        $this->db->trans_start();
     
        $supplier_arr = $this->fetch_supplier_data($master_grv['supplierID']);
  
            $financeYearDetails=get_financial_year_srm_api($master_grv['grvDate'],$master_grv['companyMsterID']);
            if(empty($financeYearDetails)){
                //$this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false, 'last_id' => '','message'=>'Finance period not found for the selected document date');
                exit;
            }else{
                $FYBegin=$financeYearDetails['beginingDate'];
                $FYEnd=$financeYearDetails['endingDate'];
                $master_grv['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $master_grv['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails=get_financial_period_date_wise_srm_api($master_grv['grvDate'],$master_grv['companyMsterID']);

            if(empty($financePeriodDetails)){
                //$this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false, 'last_id' => '','message'=>'Finance period not found for the selected document date');
                exit;
            }else{

                $master_grv['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
            }

        $data_m['grvType'] = $master_grv['grvType'];
        $data_m['documentID'] = 'GRV';
        $data_m['contactPersonName'] = $master_grv['contactPersonName'];
        $data_m['contactPersonNumber'] = $master_grv['contactPersonNumber'];
        $data_m['supplierID'] = $master_grv['supplierID'];
       // $data['companyMsterID'] = trim($this->input->post('companyID') ?? '');

        //$narration = ($this->input->post('narration'));
        $data_m['grvNarration'] = $master_grv['grvNarration'];
        //$data['grvNarration'] = trim_desc($this->input->post('narration'));
        $data_m['companyFinanceYearID'] = $master_grv['financeyear'];
        $data_m['companyFinanceYear'] = $master_grv['companyFinanceYear'];
        $data_m['FYBegin'] = trim($FYBegin);
        $data_m['FYEnd'] = trim($FYEnd);
        $data_m['companyFinancePeriodID'] = $master_grv['financeyear_period'];
        /*$data['FYPeriodDateFrom']                   = trim($period[0] ?? '');
        $data['FYPeriodDateTo']                     = trim($period[1] ?? '');*/
        $data_m['grvDate'] = $master_grv['grvDate'];
        $data_m['deliveredDate'] = $master_grv['deliveredDate'];
        $data_m['grvDocRefNo'] = $master_grv['grvDocRefNo'];

        $data_m['supplierSystemCode'] = $supplier_arr['supplierSystemCode'];
        $data_m['supplierName'] = $supplier_arr['supplierName'];
        $data_m['supplierAddress'] = $supplier_arr['supplierAddress1'] . ' ' . $supplier_arr['supplierAddress2'];
        $data_m['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data_m['supplierFax'] = $supplier_arr['supplierFax'];
        $data_m['supplierEmail'] = $supplier_arr['supplierEmail'];
        $data_m['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data_m['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data_m['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data_m['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data_m['supplierliabilityType'] = $supplier_arr['liabilityType'];

        $data_m['segmentID'] = $master_grv['segmentID'];
        $data_m['segmentCode'] = $master_grv['segmentCode'];
        // $data['modifiedPCID'] = $this->common_data['current_pc'];
        // $data['modifiedUserID'] = $this->common_data['current_userID'];
        // $data['modifiedUserName'] = $this->common_data['current_user'];
        // $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data_m['wareHouseAutoID'] = $master_grv['wareHouseAutoID'];
        $data_m['wareHouseCode'] = $master_grv['wareHouseCode'];
        $data_m['wareHouseLocation'] = $master_grv['wareHouseLocation'];
        $data_m['wareHouseDescription'] = $master_grv['wareHouseDescription'];
        $data_m['isGroupBasedTax'] =  $master_grv['isGroupBasedTax'];
        $warehouseAutoID = $master_grv['companyMsterID'];
        $companyID = $master_grv['companyMsterID'];
        // $mfqWarehouseAutoID = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster WHERE warehouseAutoID = {$warehouseAutoID} AND companyID = {$companyID}")->row('mfqWarehouseAutoID');

        // if($mfqWarehouseAutoID) {
        //     $data['jobID'] = trim($this->input->post('jobID') ?? '');
        //     $data['jobNo'] = trim($this->input->post('jobNumber') ?? '');
        // } else {
            $data_m['jobID'] = null;
            $data_m['jobNo'] = null;
       // }

        $data_m['transactionCurrencyID'] = $master_grv['transactionCurrencyID'];
        $data_m['transactionCurrency'] = $master_grv['transactionCurrency'];
        $data_m['transactionExchangeRate'] = 1;
        $data_m['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_m['transactionCurrencyID']);
        $data_m['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data_m['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['companyLocalCurrencyID']);
        $data_m['companyLocalExchangeRate'] =$default_currency['conversion'];
        $data_m['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_m['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data_m['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['companyReportingCurrencyID']);
        $data_m['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_m['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        $data_m['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data_m['supplierCurrency'] = $supplier_arr['supplierCurrency'];

        $supplierCurrency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['supplierCurrencyID']);
        $data_m['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data_m['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];

       
            $data_m['companyID'] = $master_grv['companyMsterID'];
            $data_m['srmGrvAutoID'] =  $master_grv['grvAutoID'];
            $data_m['isSrmGenerated'] =  1;
           
           $data_m['companyCode'] = $this->common_data['company_data']['company_code'];
            $data_m['createdUserGroup'] = $this->common_data['user_group'];
            $data_m['createdPCID'] = $this->common_data['current_pc'];
            $data_m['createdUserID'] = $this->common_data['current_userID'];
            $data_m['createdUserName'] = $this->common_data['current_user'];
            $data_m['createdDateTime'] = date('y-m-d H:i:s');
            //$data['grvPrimaryCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_grvmaster', $data_m);
            $last_id = $this->db->insert_id();

            if($last_id){
                $noofitems = [];
                $grossqty = [];
                $buckets = [];
                $bucketweightID = [];
                $bucketweight = [];
                $taxCalculationMasterID = [];

                $po_DetailsID=[];
                $po_amount=[];
                $po_qty=[];
                $grvAutoID = $last_id;
                $req_po_master_id = $details_grv[0]['erpPurchaseOrderMastertID'];

               

                foreach($details_grv as $po_det){
                    $po_DetailsID[]= $po_det['erpPurchaseOrderDetailsID'];
                    $po_amount[]=$po_det['receivedAmount'];
                    $po_qty[]=$po_det['receivedQty'];
                }

                if($req_po_master_id){
                    $this->db->select('*');
                    $this->db->from('srp_erp_purchaseordermaster');
                    $this->db->where('purchaseOrderID', $req_po_master_id);
                    $po_master_segment = $this->db->get()->row_array();
                    if($po_master_segment){
                        $data_master_up['segmentID']=$po_master_segment['segmentID'];
                        $data_master_up['segmentCode']=$po_master_segment['segmentCode'];

                        $this->db->where('grvAutoID', $last_id);
                        $this->db->update('srp_erp_grvmaster', $data_master_up);
                    }
                }

                
        
                $this->db->trans_start();
                $items_arr = array();
                $this->db->select('srp_erp_purchaseorderdetails.*,ifnull(sum(srp_erp_grvdetails.receivedQty),0) AS receivedQty,ifnull(sum(srp_erp_paysupplierinvoicedetail.requestedQty),0) AS bsireceivedQty,srp_erp_purchaseordermaster.purchaseOrderCode');
                $this->db->from('srp_erp_purchaseorderdetails');
                $this->db->where_in('srp_erp_purchaseorderdetails.purchaseOrderDetailsID', $po_DetailsID);
                $this->db->join('srp_erp_purchaseordermaster', 'srp_erp_purchaseordermaster.purchaseOrderID = srp_erp_purchaseorderdetails.purchaseOrderID');
                $this->db->join('srp_erp_grvdetails', 'srp_erp_grvdetails.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
                $this->db->join('srp_erp_paysupplierinvoicedetail', 'srp_erp_paysupplierinvoicedetail.purchaseOrderDetailsID = srp_erp_purchaseorderdetails.purchaseOrderDetailsID', 'left');
                $this->db->group_by("purchaseOrderDetailsID");
                $query = $this->db->get()->result_array();
        
                $purchaseOrderID = array_column($query, 'purchaseOrderID');
        
               // return array('status' => true, 'last_id' => $data);
        
                $this->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription, jobID');
                $this->db->from('srp_erp_grvmaster');
                $this->db->where('grvAutoID', $grvAutoID);
                $master = $this->db->get()->row_array();
        
                $qty = $po_qty;
                $amount = $po_amount;
                for ($i = 0; $i < count($query); $i++) {
                    $this->db->select('purchaseOrderMastertID');
                    $this->db->from('srp_erp_grvdetails');
                    $this->db->where('purchaseOrderMastertID', $query[$i]['purchaseOrderID']);
                    $this->db->where('grvAutoID', $grvAutoID);
                    $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                    $order_detail = $this->db->get()->result_array();
                    $item_data = fetch_item_data($query[$i]['itemAutoID']);
                    if ($item_data['mainCategory'] == 'Inventory' or $item_data['mainCategory'] == 'Non Inventory') {
                        $this->db->select('itemAutoID');
                        $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                        $this->db->where('wareHouseAutoID', $master['wareHouseAutoID']);
                        $this->db->where('companyID', $master_grv['companyMsterID']);
                        $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                        if (empty($warehouseitems)) {
                            $item_id = array_search($query[$i]['itemSystemCode'], array_column($items_arr, 'itemSystemCode'));
                            if ((string)$item_id == '') {
                                $items_arr[$i]['wareHouseAutoID'] = $master['wareHouseAutoID'];
                                $items_arr[$i]['wareHouseLocation'] = $master['wareHouseLocation'];
                                $items_arr[$i]['wareHouseDescription'] = $master['wareHouseDescription'];
                                $items_arr[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                                $items_arr[$i]['barCodeNo']= $item_data['barcode'];
                                $items_arr[$i]['salesPrice']= $item_data['companyLocalSellingPrice'];
                                $items_arr[$i]['ActiveYN']= $item_data['isActive'];
                                $items_arr[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                                $items_arr[$i]['itemDescription'] = $query[$i]['itemDescription'];
                                $items_arr[$i]['unitOfMeasureID'] = $query[$i]['defaultUOMID'];
                                $items_arr[$i]['unitOfMeasure'] = $query[$i]['defaultUOM'];
                                $items_arr[$i]['currentStock'] = 0;
                                $items_arr[$i]['companyID'] = $master_grv['companyMsterID'];
                                $items_arr[$i]['companyCode'] = '';
                            }
                        }
                    }
                    $this->db->select('mainCategory');
                    $this->db->from('srp_erp_itemmaster');
                    $this->db->where('itemAutoID', $query[$i]['itemAutoID']);
                    $serviceitm= $this->db->get()->row_array();
        
                    if (!empty($order_detail) && $serviceitm['mainCategory']=="Inventory") {
                        $this->session->set_flashdata('w', 'PO Details added already.');
                    } else {
                        //$ACA_ID = $this->common_data['controlaccounts']['ACA'];
                        $this->db->select('GLAutoID');
                        $this->db->where('controlAccountType', 'ACA');
                        $this->db->where('companyID', $master_grv['companyMsterID']);
                        $ACA_ID = $this->db->get('srp_erp_companycontrolaccounts')->row_array();
                        $ACA = fetch_gl_account_desc($ACA_ID['GLAutoID']);
        
        
                        $potaxamnt=($query[$i]['taxAmount']+$query[$i]['generalTaxAmount'])/$query[$i]['requestedQty'];
                        $item_data = fetch_item_data($query[$i]['itemAutoID']);
                        $data[$i]['purchaseOrderMastertID'] = $query[$i]['purchaseOrderID'];
                        // $data[$i]['erpPurchaseOrderMastertID'] = $query[$i]['erpBackendMasterID'];
                        // $data[$i]['erpPurchaseOrderDetailsID'] = $query[$i]['erpBackendID'];
                        $data[$i]['purchaseOrderCode'] = $query[$i]['purchaseOrderCode'];
                        $data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                        $data[$i]['grvAutoID'] = $grvAutoID;
                        $data[$i]['itemAutoID'] = $query[$i]['itemAutoID'];
                        $data[$i]['itemSystemCode'] = $query[$i]['itemSystemCode'];
                        $data[$i]['itemDescription'] = $query[$i]['itemDescription'];
                        $data[$i]['defaultUOM'] = $query[$i]['defaultUOM'];
                        $data[$i]['defaultUOMID'] = $query[$i]['defaultUOMID'];
                        $data[$i]['unitOfMeasure'] = $query[$i]['unitOfMeasure'];
                        $data[$i]['unitOfMeasureID'] = $query[$i]['unitOfMeasureID'];
                        $data[$i]['conversionRateUOM'] = $query[$i]['conversionRateUOM'];
                        $data[$i]['requestedQty'] = $query[$i]['requestedQty'];
                       
                        if($this->existTaxPolicyDocumentWise_srm('srp_erp_grvmaster',$grvAutoID,'GRV','grvAutoID')== 1 && $taxCalculationMasterID[$i]!=0){
                            $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty']);
                        }else{ 
                            $data[$i]['requestedAmount'] = $query[$i]['unitAmount']-($query[$i]['generalDiscountAmount']/$query[$i]['requestedQty'])+$potaxamnt;
                        }
                     
                      
                        $data[$i]['comment'] = $query[$i]['comment'];
                        $data[$i]['receivedQty'] = $qty[$i];
                        //$data[$i]['noOfItems'] = $noofitems[$i];
                        //$data[$i]['grossQty'] = $grossqty[$i];
                       // $data[$i]['noOfUnits'] = $buckets[$i];
                        //$data[$i]['deduction'] = $bucketweight[$i];
                        //$data[$i]['bucketWeightID'] = $bucketweightID[$i];
                        $data[$i]['receivedAmount'] = $amount[$i];
                        $data[$i]['receivedTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
                        $data[$i]['fullTotalAmount'] = ($data[$i]['receivedQty'] * $data[$i]['receivedAmount']);
                        $data[$i]['financeCategory'] = $item_data['financeCategory'];
                        $data[$i]['itemCategory'] = trim($item_data['mainCategory'] ?? '');
                        if ($data[$i]['itemCategory'] == 'Inventory') {
        
                            if(!empty($master['jobID'])) {
                                $companyID = $master_grv['companyMsterID'];
                                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                                FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                                    WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();
                                if ($glDetails) {
                                    $data[$i]['BLGLAutoID'] = $glDetails['WIPGLAutoID'];
                                    $data[$i]['BLSystemGLCode'] = $glDetails['systemAccountCode'];
                                    $data[$i]['BLGLCode'] = $glDetails['GLSecondaryCode'];
                                    $data[$i]['BLDescription'] = $glDetails['GLDescription'];
                                    $data[$i]['BLType'] = $glDetails['subCategory'];
                                } else {
                                    $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                    $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                    $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                                    $data[$i]['BLDescription'] = $item_data['assteDescription'];
                                    $data[$i]['BLType'] = $item_data['assteType'];
                                }
                            } else {
                                $data[$i]['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                $data[$i]['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                $data[$i]['BLGLCode'] = $item_data['assteGLCode'];
                                $data[$i]['BLDescription'] = $item_data['assteDescription'];
                                $data[$i]['BLType'] = $item_data['assteType'];
                            }
                            $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                            $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                            $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                            $data[$i]['PLDescription'] = $item_data['costDescription'];
                            $data[$i]['PLType'] = $item_data['costType'];
        
                            
                        } elseif ($data[$i]['itemCategory'] == 'Fixed Assets') {
                            $data[$i]['PLGLAutoID'] = NULL;
                            $data[$i]['PLSystemGLCode'] = NULL;
                            $data[$i]['PLGLCode'] = NULL;
                            $data[$i]['PLDescription'] = NULL;
                            $data[$i]['PLType'] = NULL;
        
                            $data[$i]['BLGLAutoID'] = $ACA_ID['GLAutoID'];
                            $data[$i]['BLSystemGLCode'] = $ACA['systemAccountCode'];
                            $data[$i]['BLGLCode'] = $ACA['GLSecondaryCode'];
                            $data[$i]['BLDescription'] = $ACA['GLDescription'];
                            $data[$i]['BLType'] = $ACA['subCategory'];
                        } else {
                            if(!empty($master['jobID'])) {
                                $companyID = $master_grv['companyMsterID'];
                                $glDetails = $this->db->query("SELECT WIPGLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                                                    FROM srp_erp_warehousemaster LEFT JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_warehousemaster.WIPGLAutoID
                                                                        WHERE srp_erp_warehousemaster.companyID = {$companyID} AND wareHouseAutoID = {$master['wareHouseAutoID']}")->row_array();
                
                                if ($glDetails) {
                                    $data[$i]['PLGLAutoID'] = $glDetails['WIPGLAutoID'];
                                    $data[$i]['PLSystemGLCode'] = $glDetails['systemAccountCode'];
                                    $data[$i]['PLGLCode'] = $glDetails['GLSecondaryCode'];
                                    $data[$i]['PLDescription'] = $glDetails['GLDescription'];
                                    $data[$i]['PLType'] = $glDetails['subCategory'];
                                } else {
                                    $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                                    $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                    $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                                    $data[$i]['PLDescription'] = $item_data['costDescription'];
                                    $data[$i]['PLType'] = $item_data['costType'];
                                }
                            } else {
                                $data[$i]['PLGLAutoID'] = $item_data['costGLAutoID'];
                                $data[$i]['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                $data[$i]['PLGLCode'] = $item_data['costGLCode'];
                                $data[$i]['PLDescription'] = $item_data['costDescription'];
                                $data[$i]['PLType'] = $item_data['costType'];
                            }
        
                            $data[$i]['BLGLAutoID'] = '';
                            $data[$i]['BLSystemGLCode'] = '';
                            $data[$i]['BLGLCode'] = '';
                            $data[$i]['BLDescription'] = '';
                            $data[$i]['BLType'] = '';
                        } 
                        // if(existTaxPolicyDocumentWise('srp_erp_grvmaster',trim($this->input->post('grvAutoID') ?? ''),'GRV','grvAutoID')== 1 && $taxCalculationMasterID[$i]!=0){
                        //     $data[$i]['taxCalculationformulaID'] = $taxCalculationMasterID[$i];   
                        // }
        
                        $data[$i]['addonAmount'] = 0;
                        $data[$i]['addonTotalAmount'] = 0;
                        $data[$i]['comment'] = $query[$i]['comment'];
                        $data[$i]['remarks'] = $query[$i]['remarks'];
                        $data[$i]['companyCode'] =  $this->common_data['company_data']['company_code'];
                        $data[$i]['companyID'] = $master_grv['companyMsterID'];
                 
                        $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                        $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                        $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                        $data[$i]['createdUserName'] = $this->common_data['current_user'];
                        $data[$i]['createdDateTime'] = date('y-m-d H:i:s');
        
                        $po_data[$i]['purchaseOrderDetailsID'] = $query[$i]['purchaseOrderDetailsID'];
                        $po_data[$i]['GRVSelectedYN'] = 1;
                        if ($query[$i]['requestedQty'] <= (floatval($qty[$i]) + floatval($query[$i]['receivedQty'])+ floatval($query[$i]['bsireceivedQty']))) {
                            $po_data[$i]['goodsRecievedYN'] = 1;
                        } else {
                            $po_data[$i]['goodsRecievedYN'] = 0;
                        }
                    }
        
        
                }
        
                if (!empty($items_arr)) {
                    $items_arr = array_values($items_arr);
                    $this->db->insert_batch('srp_erp_warehouseitems', $items_arr);
                }
        
        
                if (!empty($data)) {
        
                    //print_r($data);
        
                    $this->db->insert_batch('srp_erp_grvdetails', $data);
        
        
        
                   /** sub item add */
                    //$grvAutoID = trim($this->input->post('grvAutoID') ?? '');
                    $output = $this->db->query("SELECT * FROM srp_erp_grvdetails INNER JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_grvdetails.itemAutoID AND isSubitemExist = 1 WHERE grvAutoID = '" . $grvAutoID . "'")->result_array();
                    if (!empty($output)) {
                        foreach ($output as $item) {
                            if ($item['isSubitemExist'] == 1) {
                                $qty = $item['receivedQty'];
                                $subData['uom'] = $data[0]['unitOfMeasure'];
                                $subData['uomID'] = $data[0]['unitOfMeasureID'];
                                $subData['grv_detailID'] = $item['grvDetailsID'];
                                $this->add_sub_itemMaster_tmpTbl($qty, $item['itemAutoID'], $grvAutoID, $item['grvDetailsID'], 'GRV', $item['itemSystemCode'], $subData,$master_grv['companyMsterID']);
                            }
                        }
                    }
                    
        
        
                    /** End sub item add */
        
                    $this->db->update_batch('srp_erp_purchaseorderdetails', $po_data, 'purchaseOrderDetailsID');
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                       // $this->session->set_flashdata('e', 'Good Received note : Details Save Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        //return array('status' => false);
                    } else {
                   // $this->session->set_flashdata('s', 'Good Received note : ' . count($query) . ' Item Details Saved Successfully.');
                    $this->db->trans_commit();
        
                        $companyID =$master_grv['companyMsterID'];
                       /// $grvAutoID =  trim($this->input->post('grvAutoID') ?? '');
        
                        $grvTax = $this->db->query("SELECT
                                                    srp_erp_purchaseorderdetails.taxCalculationformulaID,grvAutoID,
                                                   ((srp_erp_grvdetails.receivedQty * unitAmount)+(srp_erp_grvdetails.receivedQty* IFNULL(srp_erp_purchaseorderdetails.discountAmount,0))) as totalAmount,
                                                   (srp_erp_grvdetails.receivedQty * IFNULL(srp_erp_purchaseorderdetails.discountAmount,0)) as discountAmount, receivedTotalAmount,
                                                    grvDetailsID
                                               FROM
                                               `srp_erp_grvdetails` 
                                               LEFT JOIN srp_erp_purchaseorderdetails ON srp_erp_purchaseorderdetails.purchaseOrderDetailsID = srp_erp_grvdetails.purchaseOrderDetailsID
        
                                                WHERE
                                                srp_erp_grvdetails.companyID = $companyID 
                                                AND grvAutoID = $grvAutoID")->result_array();
        
        
        
                        $isRcmApplicable =  $this->isRcmApplicable_srm('srp_erp_purchaseordermaster', 'purchaseOrderID', $purchaseOrderID[0]);
                        if($this->existTaxPolicyDocumentWise_srm('srp_erp_grvmaster',$grvAutoID ,'GRV','grvAutoID')== 1){
                            if(!empty($grvTax)){
                                foreach($grvTax as $val){
                                    if($val['taxCalculationformulaID']!=0){
                                        tax_calculation_vat(null,null,$val['taxCalculationformulaID'],'grvAutoID',$grvAutoID ,$val['receivedTotalAmount'],'GRV',$val['grvDetailsID'],0,1,$isRcmApplicable);
                                    }
                                }
                            }
                        }
        
        
        
                        //return array('status' => true);
                    }
                } else {
                   // return array('status' => false, 'data' => 'PO Details added already.');
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                
                return array('status' => false, 'last_id' => '');
            } else {
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        
    }

    function isRcmApplicable_srm($masterTBL, $masterColname, $masterID)
    {
        //$CI =& get_instance();
        $companyID = current_companyID();
        $isRcmApplicable = $this->db->query("SELECT
	                                       rcmApplicableYN 
                                           FROM
                                            {$masterTBL}
                                           where 
                                           companyID = '{$companyID}'
                                           AND {$masterColname} = '{$masterID}'")->row('rcmApplicableYN');


        return $isRcmApplicable;
    }

    function existTaxPolicyDocumentWise_srm($tblName, $masterID, $documentID, $masterColName){
       // $CI = &get_instance();
        $companyID = current_companyID();

        $query = $this->db->query("SELECT
                              isGroupBasedTax
                              FROM {$tblName}
                              where companyID = '{$companyID}' 
                              AND {$masterColName} = '{$masterID}'")->row('isGroupBasedTax');

        return $query;
    }

    function save_supplier_invoice($master_grv,$details_grv,$documentsArr)
    {
        $this->db->trans_start();
       
        $financeYearDetails = get_financial_year_srm_api($master_grv['bookingDate'], $master_grv['companyMsterID']);
        if (empty($financeYearDetails)) {
            return array('status' => false, 'last_id' => '','message'=>'Finance period not found for the selected document date');
            exit;
        } else {
            $FYBegin = $financeYearDetails['beginingDate'];
            $FYEnd = $financeYearDetails['endingDate'];
            $master_grv['companyFinanceYear'] = $FYBegin . ' - ' . $FYEnd;
            $master_grv['financeyear'] = $financeYearDetails['companyFinanceYearID'];
        }
        $financePeriodDetails = get_financial_period_date_wise_srm_api($master_grv['bookingDate'], $master_grv['companyMsterID']);

        if (empty($financePeriodDetails)) {
            return array('status' => false, 'last_id' => '','message'=>'Finance period not found for the selected document date');
            exit;
        } else {
            $master_grv['financeyear_period'] = $financePeriodDetails['companyFinancePeriodID'];
        }

        $supplier_arr = $this->fetch_supplier_data($master_grv['supplierID']);
        $data_m['invoiceType'] = $master_grv['invoiceType'];
        $data_m['bookingDate'] = $master_grv['bookingDate'];
        $data_m['invoiceDueDate'] = $master_grv['invoiceDueDate'];
        $data_m['invoiceDate'] = $master_grv['invoiceDate'];

        $data_m['srmInvoiceAutoID'] = $master_grv['InvoiceAutoID'];
        $data_m['isSrmGenerated'] = 1;

        $data_m['companyFinanceYearID'] = $master_grv['financeyear'];
        $data_m['companyFinanceYear'] = $master_grv['companyFinanceYear'];
        $data_m['FYBegin'] = trim($FYBegin);
        $data_m['FYEnd'] = trim($FYEnd);
        $data_m['companyFinancePeriodID'] = $master_grv['financeyear_period'];
        //$data['FYPeriodDateFrom'] = trim($period[0] ?? '');
        //$data['FYPeriodDateTo'] = trim($period[1] ?? '');
        $data_m['documentID'] = 'BSI';
        $data_m['supplierID'] = $master_grv['supplierID'];
        $data_m['supplierCode'] = $supplier_arr['supplierSystemCode'];
        $data_m['supplierName'] = $supplier_arr['supplierName'];
        $data_m['supplierAddress'] = $supplier_arr['supplierAddress1'];
        $data_m['supplierTelephone'] = $supplier_arr['supplierTelephone'];
        $data_m['supplierFax'] = $supplier_arr['supplierFax'];
        $data_m['supplierliabilityAutoID'] = $supplier_arr['liabilityAutoID'];
        $data_m['supplierliabilitySystemGLCode'] = $supplier_arr['liabilitySystemGLCode'];
        $data_m['supplierliabilityGLAccount'] = $supplier_arr['liabilityGLAccount'];
        $data_m['supplierliabilityDescription'] = $supplier_arr['liabilityDescription'];
        $data_m['supplierliabilityType'] = $supplier_arr['liabilityType'];
        $data_m['supplierInvoiceNo'] = $master_grv['supplierInvoiceNo'];
        $data_m['supplierInvoiceDate'] = $master_grv['supplierInvoiceDate'];
        $data_m['transactionCurrency'] = $master_grv['transactionCurrency'];

        if($master_grv['purchaseOrderIDMaster']){
            $data_m['purchaseOrderIDMaster'] = $master_grv['purchaseOrderIDMaster'];

            //fetch po detail
            if($master_grv['purchaseOrderIDMaster']){
                $po_details = $this->fetch_po_details($master_grv['purchaseOrderIDMaster']);
                if($po_details){
                    $data_m['purchaseOrderDetails'] = $po_details['purchaseOrderCode'].'|'.$po_details['supplierCode'].'|'.$po_details['referenceNumber'];
                    $data_m['segmentID'] = $po_details['segmentID'];
                }


            }
           
        }
        
        //$data_m['segmentID'] = trim($this->input->post('segment') ?? '');
        /*$data['warehouseAutoID'] = $this->input->post('location');*/
        $data_m['RefNo'] = $master_grv['RefNo'];
        $data_m['comments'] = $master_grv['comments'];
        //$data['comments'] = trim($this->input->post('comments') ?? '');
        $data_m['transactionCurrencyID'] = $master_grv['transactionCurrencyID'];
        $data_m['transactionCurrency'] = $master_grv['transactionCurrency'];
        $data_m['transactionExchangeRate'] = 1;
        $data_m['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data_m['transactionCurrencyID']);
        $data_m['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data_m['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['companyLocalCurrencyID']);
        $data_m['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data_m['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_m['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data_m['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['companyReportingCurrencyID']);
        $data_m['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data_m['companyReportingCurrencyDecimalPlaces'] =  $reporting_currency['DecimalPlaces'];

        $data_m['supplierCurrencyID'] = $supplier_arr['supplierCurrencyID'];
        $data_m['supplierCurrency'] = $supplier_arr['supplierCurrency'];

        $supplierCurrency = currency_conversionID($data_m['transactionCurrencyID'], $data_m['supplierCurrencyID']);
        $data_m['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data_m['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];


        $data_m['rcmApplicableYN'] =$master_grv['rcmApplicableYN'];


     
            if (!empty($master_grv['supplierInvoiceNo']) || $master_grv['supplierInvoiceNo'] != '') {
                $q = "SELECT
                    supplierInvoiceNo,supplierID
                FROM
                    srp_erp_paysupplierinvoicemaster
                WHERE
                 supplierID = '" .$master_grv['supplierID'] . "'  AND supplierInvoiceNo = '" . $master_grv['supplierInvoiceNo'] . "'";
                $result = $this->db->query($q)->row_array();
                if ($result) {
                    //$this->session->set_flashdata('e', ' Supplier Invoice Number already exist for the selected supplier');
                    $this->db->trans_rollback();
                    return array('status' => false, 'last_id' => '' ,'message'=>"Supplier Invoice Number already exist");
                }
            }
            //$this->load->library('sequence');

          
            $data_m['companyID'] = $master_grv['companyMsterID'];
            $data_m['companyCode'] = $this->common_data['company_data']['company_code'];
            $data_m['createdUserGroup'] = $this->common_data['user_group'];
            $data_m['createdPCID'] = $this->common_data['current_pc'];
            $data_m['createdUserID'] = $this->common_data['current_userID'];
            $data_m['createdUserName'] = $this->common_data['current_user'];
            $data_m['createdDateTime'] = date('y-m-d H:i:s');
           // $data['bookingInvCode'] = 0;
            // if ((trim($this->input->post('invoiceType') ?? '') == 'StandardItem') || (trim($this->input->post('invoiceType') ?? '') == 'Standard')) {
            //     $data['isGroupBasedTax'] = ((getPolicyValues('GBT', 'All') == 1) ? 1 : 0);
            // }

            $this->db->insert('srp_erp_paysupplierinvoicemaster', $data_m);
            $last_id = $this->db->insert_id();

            if(count($documentsArr)>0){
                foreach($documentsArr as $val){
                    $data_att['documentID'] = 'BSI';
                    $data_att['documentSystemCode'] = $last_id;
                    $data_att['attachmentDescription'] = $val['attachmentDescription'];
                    $data_att['myFileName'] = $val['myFileName'];
                    $data_att['fileType'] = $val['fileType'];
                    $data_att['fileSize'] = $val['fileSize'];
                    $data_att['timestamp'] = $val['timestamp'];
                    $data_att['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_att['companyCode'] = $this->common_data['company_data']['company_code'];
                
                    $this->db->insert('srp_erp_documentattachments', $data_att);
                }
            }

            if($last_id){

                $amount = [];
                $match = [];
                $grvAutoID = [];
                $InvoiceAutoID = $last_id;

                foreach($details_grv as $po_det){
                    $amount[]= $po_det['transactionAmount'];
                    $grvAutoID[]=$po_det['erpGrvAutoID'];
                }

                
                $companyID = $master_grv['companyMsterID'];

                $this->db->select('bookingCurrencyExchangeRate, supplierCurrencyExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,match_supplierinvoiceAutoID');
                $this->db->where_in('grvAutoID', $grvAutoID);
                $this->db->from('srp_erp_match_supplierinvoice');
                $match_data = $this->db->get()->result_array();

                for ($i = 0; $i < count($match_data); $i++) {
                    $match[]=$match_data[$i]['match_supplierinvoiceAutoID'];
                }

                for ($i = 0; $i < count($match_data); $i++) {
                    $this->db->select('grvAutoID,grvType,companyLocalExchangeRate,companyReportingExchangeRate,supplierCurrencyExchangeRate, grvPrimaryCode ,grvDocRefNo,supplierliabilityAutoID,supplierliabilitySystemGLCode,supplierliabilityGLAccount,supplierliabilityType,supplierliabilityDescription,grvDate,grvNarration,segmentID,segmentCode,invoicedTotalAmount,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
                    $this->db->from('srp_erp_grvmaster');
                    $this->db->where('srp_erp_grvmaster.grvAutoID', $grvAutoID[$i]);
                    $master_recode = $this->db->get()->row_array();

                    

                    $data[$i]['InvoiceAutoID'] = $InvoiceAutoID ;
                    $data[$i]['grvAutoID'] = $master_recode['grvAutoID'];
                    $data[$i]['grvType'] = 'GRV Base';
                    $data[$i]['match_supplierinvoiceAutoID'] = $match[$i];
                    $data[$i]['grvPrimaryCode'] = $master_recode['grvPrimaryCode'];
                    $data[$i]['grvDocRefNo'] = $master_recode['grvDocRefNo'];
                    $data[$i]['grvDate'] = $master_recode['grvDate'];
                    $data[$i]['segmentID'] = $master_recode['segmentID'];
                    $data[$i]['segmentCode'] = $master_recode['segmentCode'];
                    $data[$i]['GLAutoID'] = $master_recode['supplierliabilityAutoID'];
                    $data[$i]['systemGLCode'] = $master_recode['supplierliabilitySystemGLCode'];
                    $data[$i]['GLCode'] = $master_recode['supplierliabilityGLAccount'];
                    $data[$i]['GLDescription'] = $master_recode['supplierliabilityDescription'];
                    $data[$i]['GLType'] = $master_recode['supplierliabilityType'];
                    $data[$i]['description'] = $master_recode['grvNarration'];
                    $transactionAmount = $amount[$i] / $match_data[$i]['bookingCurrencyExchangeRate'];
                    $data[$i]['transactionAmount'] = round($transactionAmount, $master_recode['transactionCurrencyDecimalPlaces']);
                    $data[$i]['transactionExchangeRate'] = $match_data[$i]['bookingCurrencyExchangeRate'];
                    $companyLocalAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyLocalExchangeRate'];
                    $data[$i]['companyLocalAmount'] = round($companyLocalAmount, $master_recode['companyLocalCurrencyDecimalPlaces']);
                    $data[$i]['companyLocalExchangeRate'] = $match_data[$i]['companyLocalExchangeRate'];
                    $companyReportingAmount = $data[$i]['transactionAmount'] / $match_data[$i]['companyReportingExchangeRate'];
                    $data[$i]['companyReportingAmount'] = round($companyReportingAmount, $master_recode['companyReportingCurrencyDecimalPlaces']);
                    $data[$i]['companyReportingExchangeRate'] = $match_data[$i]['companyReportingExchangeRate'];
                    $supplierAmount = $data[$i]['transactionAmount'] / $match_data[$i]['supplierCurrencyExchangeRate'];
                    $data[$i]['supplierAmount'] = round($supplierAmount, $master_recode['supplierCurrencyDecimalPlaces']);
                    $data[$i]['supplierCurrencyExchangeRate'] = $match_data[$i]['supplierCurrencyExchangeRate'];
                    $data[$i]['companyID'] = $master_grv['companyMsterID'];
                    $data[$i]['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $data[$i]['createdUserName'] = $this->common_data['current_user'];
            
                    $data[$i]['createdDateTime'] = date('y-m-d H:i:s');
                    $company_id = $master_grv['companyMsterID'];
                    $match_id = $data[$i]['match_supplierinvoiceAutoID'];
                    $number = $transactionAmount;
                    $status = 0;

                    $this->db->select('invoicedTotalAmount, bookingAmount');
                    $this->db->from('srp_erp_match_supplierinvoice');
                    $this->db->where('match_supplierinvoiceAutoID', $match_id);
                    $inv_data = $this->db->get()->row_array();
                    if ($inv_data['bookingAmount'] <= ($number + $inv_data['invoicedTotalAmount'])) {
                        $status = 1;
                    }

                    $this->db->query("UPDATE srp_erp_match_supplierinvoice SET invoicedTotalAmount = (invoicedTotalAmount +{$number}) , supplierInvoiceYN = '{$status}'  WHERE match_supplierinvoiceAutoID='{$match_id}' and companyID='{$company_id}'");
                }

                if (!empty($data)) {
                    $this->db->insert_batch('srp_erp_paysupplierinvoicedetail', $data);

                    $this->db->query("INSERT INTO srp_erp_taxledger (documentID, documentMasterAutoID, documentDetailAutoID, amount,taxGlAutoID,transferGLAutoID,taxMasterID,companyID,outputVatTransferGL,outputVatGL,isClaimable,rcmApplicableYN, taxFormulaMasterID, taxFormulaDetailID)
                                            SELECT 'BSI' AS documentID,
                                                    InvoiceAutoID AS documentMasterAutoID, 
                                                    InvoiceDetailAutoID AS documentDetailAutoID, 
                                                    IFNULL(( TRIM( ROUND( (( taxledger.taxamount / matchsupplierinvoice.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0) ,0)  +
                                                    IFNULL(( TRIM( ROUND( (( taxledgerAddon.taxamount / matchsupplierInvoiceAddon.bookingAmount ) * srp_erp_paysupplierinvoicedetail.transactionAmount),4)) + 0 ) ,0) AS amount,
                                                    taxledger.inputVatGLAccountAutoID AS taxGlAutoID,
                                                    taxledger.taxGlAutoID AS transferGLAutoID,
                                                    taxledger.taxMasterID AS taxMasterID,
                                                    {$companyID} AS companyID,
                                                    taxledger.outputVatTransferGL AS outputVatTransferGL,
                                                    taxledger.outputVatGL AS outputVatGL,
                                                    taxledger.isClaimable AS isClaimable,
                                                    taxledger.rcmApplicableYN AS rcmApplicableYN,
                                                    taxledger.taxFormulaMasterID,
                                                    taxledger.taxFormulaDetailID
                                                    FROM
                                                    `srp_erp_paysupplierinvoicedetail`
                                                    LEFT JOIN ( SELECT match_supplierinvoiceAutoID,addonID,bookingAmount  FROM srp_erp_match_supplierinvoice WHERE isAddon = 0 ) matchsupplierinvoice ON srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID = matchsupplierinvoice.match_supplierinvoiceAutoID
                                                    LEFT JOIN (SELECT match_supplierinvoiceAutoID,addonID,bookingAmount   FROM srp_erp_match_supplierinvoice WHERE isAddon = 1) matchsupplierInvoiceAddon ON matchsupplierInvoiceAddon.match_supplierinvoiceAutoID = srp_erp_paysupplierinvoicedetail.match_supplierinvoiceAutoID
                                                    LEFT JOIN (
                                                    SELECT
                                                            SUM( amount ) AS taxamount,
                                                            documentMasterAutoID,
                                                            taxMasterID,
                                                            documentDetailAutoID,
                                                            taxGlAutoID,
                                                            srp_erp_taxmaster.inputVatGLAccountAutoID,
                                                            outputVatTransferGL,
                                                            outputVatGL,
                                                            srp_erp_taxledger.isClaimable,
                                                            srp_erp_taxledger.rcmApplicableYN,
                                                            srp_erp_taxledger.taxFormulaMasterID,
                                                            srp_erp_taxledger.taxFormulaDetailID,
                                                            documentID
                                                            FROM
                                                            srp_erp_taxledger
                                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                            WHERE
                                                            documentID IN ( 'GRV' ) 
                                                            AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
                                                            AND taxCategory = 2 
                                                            GROUP BY
                                                            documentMasterAutoID 
                                                    ) taxledger ON taxledger.documentMasterAutoID = srp_erp_paysupplierinvoicedetail.grvAutoID
                                                    LEFT JOIN (
                                                            SELECT
                                                            SUM( amount ) AS taxamount,
                                                            documentMasterAutoID,
                                                            taxMasterID,
                                                            taxGlAutoID,
                                                            srp_erp_taxmaster.inputVatGLAccountAutoID,
                                                            outputVatTransferGL,
                                                            outputVatGL,
                                                            srp_erp_taxledger.isClaimable,
                                                            srp_erp_taxledger.rcmApplicableYN,
                                                            srp_erp_taxledger.documentDetailAutoID,
                                                            documentID
                                                            FROM
                                                            srp_erp_taxledger
                                                            LEFT JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID 
                                                            LEFT JOIN srp_erp_grvdetails ON srp_erp_grvdetails.grvDetailsID = srp_erp_taxledger.documentDetailAutoID
                                                            WHERE
                                                            documentID IN ( 'GRV-ADD' ) 
                                                            AND documentMasterAutoID IN ( SELECT grvAutoID FROM srp_erp_paysupplierinvoicedetail WHERE InvoiceAutoID = {$InvoiceAutoID} GROUP BY grvAutoID ) 
                                                            AND taxCategory = 2 
                                                            GROUP BY
                                                            documentMasterAutoID,documentDetailAutoID) taxledgerAddon  ON taxledgerAddon.documentDetailAutoID = matchsupplierInvoiceAddon.addonID WHERE
                                                            InvoiceAutoID = {$InvoiceAutoID} 
                                                            AND grvType = 'GRV Base' 
                                                            AND srp_erp_paysupplierinvoicedetail.InvoiceDetailAutoID NOT IN ( SELECT documentDetailAutoID AS documentDetailAutoID FROM `srp_erp_taxledger` WHERE `documentID` = 'BSI' GROUP BY documentDetailAutoID ) 
                                                            GROUP BY
                                                            InvoiceDetailAutoID");

                    $this->db->trans_complete();
                   
                } else {
                    return array('status' => false, 'last_id' => $last_id,'message'=>"No DO Base Item");
                }

            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
               
                $this->db->trans_rollback();
                return array('status' => false, 'last_id' => '');
            } else {
              
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
    }

    function fetch_po_details($po_id=null){

        $purchaseOrderID = ($po_id) ? $po_id : trim($this->input->post('purchaseOrderID') ?? '');

        $this->db->select('*');
        $this->db->where('purchaseOrderID', $purchaseOrderID);
        $this->db->from('srp_erp_purchaseordermaster as po`');
        return $this->db->get()->row_array();

     }

    function save_line_wise_refer_back(){

        $this->db->trans_start();



        $inquiryDetailID_refer = trim($this->input->post('inquiryDetailID_refer') ?? '');
        $inquiryMasterID_refer = trim($this->input->post('inquiryMasterID_refer') ?? '');
        $supplierID_refer = trim($this->input->post('supplierID_refer') ?? '');
        $chatType_refer = trim($this->input->post('chatType_refer') ?? '');
        $itemAutoID_refer = trim($this->input->post('itemAutoID_refer') ?? '');
        //$companyID_refer = trim($this->input->post('companyID_refer') ?? '');
        $comment_technical = trim($this->input->post('comment_technical') ?? '');

        if($chatType_refer==4 || $chatType_refer==5){
            $this->db->select('*');
            $this->db->from('srp_erp_srm_vendor_refer_back_request');
           // $this->db->where('inquiryDetailID', $inquiryDetailID_refer);
            $this->db->where('inquiryMasterID', $inquiryMasterID_refer);
            $this->db->where('supplierID', $supplierID_refer);
            $this->db->where('chatType', $chatType_refer);
           // $this->db->where('itemAutoID', $itemAutoID_refer);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('isReSubmited =', 1);
            $data_ms = $this->db->get()->row_array();

        }else{
            $this->db->select('*');
            $this->db->from('srp_erp_srm_vendor_refer_back_request');
            $this->db->where('inquiryDetailID', $inquiryDetailID_refer);
            $this->db->where('inquiryMasterID', $inquiryMasterID_refer);
            $this->db->where('supplierID', $supplierID_refer);
            $this->db->where('chatType', $chatType_refer);
            $this->db->where('itemAutoID', $itemAutoID_refer);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('isReSubmited =', 1);
            $data_ms = $this->db->get()->row_array();

        }

        if(!$data_ms){
            $data_chat['inquiryDetailID'] = $inquiryDetailID_refer;
            $data_chat['inquiryMasterID'] = $inquiryMasterID_refer;
            $data_chat['supplierID'] = $supplierID_refer;
            $data_chat['chatType'] = $chatType_refer;
            $data_chat['itemAutoID'] = $itemAutoID_refer;
            $data_chat['companyID'] = $this->common_data['company_data']['company_id'];
            $data_chat['comment'] = $comment_technical;
            $data_chat['createdPCID'] = $this->common_data['current_pc'];
            $data_chat['createdUserID'] = $this->common_data['current_userID'];
            $data_chat['createdUserName'] = $this->common_data['current_user'];
            $data_chat['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_srm_vendor_refer_back_request', $data_chat);
            $id= $this->db->insert_id();
            $data_chat['backendID'] = $id;
            $data_chat['companyCode'] = $this->common_data['company_data']['company_code'];

            // $master_n = [
                
            //     "inquiryDetailID"=> $inquiryDetailID_refer,
            //     "vendor_specification"=>$comment_technical,
            //     "companyID"=>$companyID_refer,
            //     "type"=> $chatType_refer,
            // ]; 

            $token = $this->getLoginToken();

            $token_array=json_decode($token);

            if($token_array){

                if($token_array->success==true){
                
                    $res=$this->send_line_wise_refer_back_request($data_chat,$token_array->data->token);

                 //print_r($res);exit;

                    $res_array=json_decode($res);

                    if($res_array->status==true){
                        $data_detail2['isReSubmited'] = 1;
                        $this->db->where("autoID",$id);
                        $this->db->update('srp_erp_srm_vendor_refer_back_request', $data_detail2);
                        
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Failed." . $this->db->_error_message());
            } else {

                $this->db->trans_commit();
                return array('s', 'Submit Successfully');
            }

        }else{
            return array('w', 'You Have Pending Refer Back Request');
        }

        
    }

    public function send_line_wise_refer_back_request($master_n,$token1){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->config->item('vendor_portal_api_base_url').'/'.'index.php/Api_ecommerce/send_line_wise_refer_back_request',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER =>false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$this->getBodysend_line_wise_refer_back($master_n),
        CURLOPT_HTTPHEADER => array(
            "SME-API-KEY: $token1",
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

     //   print_r($response);exit;
        return $response;
    }


    public function getBodysend_line_wise_refer_back($master_n){

        $jayParsedAry = [
                    "dataMaster" => $master_n
        ];

         return json_encode($jayParsedAry);
    }

    function srm_rfq_document_upload_line_wise($description = true)
    {
  
            $itemAutoID_doc = $this->input->post('itemAutoID_doc');
            $inquiryID_doc = $this->input->post('inquiryID_doc');
       
            $this->db->trans_start();

            $file_name = $this->input->post('inquiryID_doc') . '_' . substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,5) . '_' . (12 + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if(empty($ext)) {
              //  echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                return array('w', 'No extension found for the selected attachment');
                exit();
            }

           // print_r($this->s3);exit;


            $this->db->select('company_code');
            $this->db->where("company_id",$this->common_data['company_data']['company_id']);
            $this->db->from("srp_erp_company");
            $company = $this->db->get()->row_array();

           /// $cc = current_companyCode();
            $folderPath = !empty($company['company_code']) ? $company['company_code'] . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */

            $data_doc['url'] = $folderPath . $file_name . '.' . $ext;
            $data_doc['itemAutoID'] = $itemAutoID_doc;
            $data_doc['companyID'] = $this->common_data['company_data']['company_id'];
            $data_doc['inquiryID'] = $inquiryID_doc;

            $this->db->insert('srp_erp_srm_vendor_rfq_linewise_documents', $data_doc);

            $id= $this->db->insert_id();

            $up_url = $this->s3->createPresignedRequest($data_doc['url'], '1 hour');

            $this->db->trans_complete();
            if ( $this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error occurred');
            } else {
                $this->db->trans_commit();
                return array('s', 'Document Submit Successfully',$id, $up_url);
            }
        
    }

    function save_new_supplier_quick()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $ApprovalforSupplierMaster= getPolicyValues('ASM', 'All');
        $supplierAutoID= trim($this->input->post('supplierAutoID') ?? '');
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }

        $suppliercode = trim($this->input->post('suppliercode') ?? '');

        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));
        $liability = fetch_gl_account_desc(trim($this->input->post('liabilityAccount') ?? ''));
        $data['isActive'] = $isactive;
        $data['secondaryCode'] = trim($this->input->post('suppliercode') ?? '');
        $data['supplierName'] = trim($this->input->post('supplierName') ?? '');

        $suppliercountry = trim($this->input->post('suppliercountry') ?? '');
        $suppliercountryID = $this->db->query("SELECT CountryID FROM srp_erp_countrymaster WHERE CountryDes = '{$suppliercountry}'")->row('CountryID');
        $data['suppliercountryID'] = trim($suppliercountryID);
        $data['supplierCountry'] = trim($this->input->post('suppliercountry') ?? '');
        $data['supplierLocationID'] = trim($this->input->post('supplierLocationID') ?? '');
        $data['supplierTelephone'] = trim($this->input->post('supplierTelephone') ?? '');
        $data['supplierEmail'] = trim($this->input->post('supplierEmail') ?? '');
        $data['supplierUrl'] = trim($this->input->post('supplierUrl') ?? '');
        $data['supplierFax'] = trim($this->input->post('supplierFax') ?? '');
        $data['taxGroupID'] = trim($this->input->post('suppliertaxgroup') ?? '');
        $data['vatIdNo'] = trim($this->input->post('vatIdNo') ?? '');
        $data['supplierAddress1'] = trim($this->input->post('supplierAddress1') ?? '');
        $data['supplierAddress2'] = trim($this->input->post('supplierAddress2') ?? '');
        $data['partyCategoryID'] = trim($this->input->post('partyCategoryID') ?? '');
        $data['nameOnCheque'] = trim($this->input->post('nameOnCheque') ?? '');
        $data['masterApprovedYN'] = 1;
        $data['masterConfirmedYN'] = 1;
        $data['isSrmGenerated'] = 1;

        $data['vatEligible'] = trim($this->input->post('vatEligible') ?? '');
        $data['vatNumber'] = trim($this->input->post('vatNumber') ?? '');
        $data['vatPercentage'] = trim($this->input->post('vatPercentage') ?? '');

        $data['liabilityAutoID'] = $liability['GLAutoID'];
        $data['liabilitySystemGLCode'] = $liability['systemAccountCode'];
        $data['liabilityGLAccount'] = $liability['GLSecondaryCode'];
        $data['liabilityDescription'] = $liability['GLDescription'];
        $data['liabilityType'] = $liability['subCategory'];

        $data['supplierCreditPeriod'] = trim($this->input->post('supplierCreditPeriod') ?? '');
        $data['supplierCreditLimit'] = trim($this->input->post('supplierCreditLimit') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('supplierAutoID') ?? '')) {
           

            $isExistSupCode = $this->db->query("SELECT
                                                COUNT(supplierAutoID) as existsCount  
                                                FROM
                                                `srp_erp_suppliermaster`
                                                where 
                                                companyID  = $companyID 
                                                AND supplierAutoID != {$this->input->post('supplierAutoID')}
                                                AND secondaryCode = '{$suppliercode}'")->row('existsCount');
                
            if(!empty($isExistSupCode)){ 
               return array('e', 'Supplier code is already exist.');
               die();

            }





            $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
            $this->db->update('srp_erp_suppliermaster', $data);

            if(($ApprovalforSupplierMaster==0 || $ApprovalforSupplierMaster == NULL )){

                    $this->load->library('Approvals');
                    $this->db->select('supplierAutoID, supplierSystemCode,modifiedDateTime');
                    $this->db->where('supplierAutoID', $supplierAutoID );
                    $this->db->from('srp_erp_suppliermaster');
                    $grv_data = $this->db->get()->row_array();
                    $approvals_status = $this->approvals->auto_approve($supplierAutoID, 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$grv_data['supplierSystemCode'],$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
                }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                return array('s', 'Supplier : ' . $data['supplierName'] . ' Updated Successfully.',$this->input->post('supplierAutoID'));
                $this->db->trans_commit();
            }
        } else {

            $isExistSupCode = $this->db->query("SELECT
            COUNT(supplierAutoID) as existsCount  
            FROM
            `srp_erp_suppliermaster`
            where 
            companyID  = $companyID 
            AND secondaryCode = '{$suppliercode}'")->row('existsCount');

            if(!empty($isExistSupCode)){ 
                return array('e', 'Supplier code is already exist.');
                die();
            }


            $this->load->library('sequence');
            $data['supplierCurrencyID'] = trim($this->input->post('supplierCurrency') ?? '');
            $data['supplierCurrency'] = $currency_code[0];
            $data['supplierCurrencyDecimalPlaces'] = fetch_currency_desimal($data['supplierCurrency']);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['supplierSystemCode'] = $this->sequence->sequence_generator('SUP');
            $this->db->insert('srp_erp_suppliermaster', $data);
            $last_id = $this->db->insert_id();

            if(( $ApprovalforSupplierMaster==0 || $ApprovalforSupplierMaster== NULL )){

                    $this->load->library('Approvals');
                    //$this->db->select('supplierAutoID, supplierSystemCode,modifiedDateTime');
                    //$this->db->where('supplierAutoID', $last_id);
                   // $this->db->from('srp_erp_suppliermaster');
                   // $grv_data = $this->db->get()->row_array();
                    $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_suppliermaster','supplierAutoID', 'SUP',$data['supplierSystemCode'] ,$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Supplier : ' . $data['supplierName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
            
            } else {

                $data_srm['secondaryCode'] = trim($this->input->post('suppliercode') ?? '');
                $data_srm['supplierName'] = trim($this->input->post('supplierName') ?? '');
                $data_srm['supplierCurrencyID'] = trim($this->input->post('supplierCurrency') ?? '');
                $data_srm['supplierCurrency'] = $currency_code[0];
                $data_srm['erpSupplierAutoID']=$last_id;
                $data_srm['supplierCountry'] =  trim($suppliercountryID);

                $data_srm['supplierTelephone'] = trim($this->input->post('supplierTelephone') ?? '');
                $data_srm['supplieremail'] = trim($this->input->post('supplierEmail') ?? '');
                $data_srm['supplierFax'] = trim($this->input->post('supplierFax') ?? '');
                $data_srm['supplierAddress1'] = trim($this->input->post('supplierAddress1') ?? '');
                $data_srm['supplierAddress2'] = trim($this->input->post('supplierAddress2') ?? '');
                $data_srm['supplierUrl'] = trim($this->input->post('supplierUrl') ?? '');
                $data_srm['isActive'] = 1;
                //$data_srm['companyRequestMasterID']=$requestMasterID;
                $data_srm['isSupplierAcc']=0;

                $data_srm['companyID'] = current_companyID();
                $data_srm['createdUserID'] = current_userID();
                $data_srm['createdUserName'] = current_user();
                $data_srm['createdDateTime'] = format_date_mysql_datetime();
                $data_srm['createdPCID'] = current_pc();
                $data_srm['createdUserGroup'] = current_user_group();
                $data_srm['companyCode'] = current_companyCode();
                $data_srm['supplierSystemCode'] = $this->sequence->sequence_generator('SRM-SUP');
                $data_srm['createdUserGroup'] = user_group();
                $data_srm['timestamp'] = format_date_mysql_datetime();

                // print_r($data);exit;

                $this->db->insert('srp_erp_srm_suppliermaster', $data_srm);
                $last_id_srm = $this->db->insert_id();

                 $this->db->trans_commit();
                return array('s', 'Supplier : ' . $data['supplierName'] . ' Updated Successfully.',$last_id);
                
        
            }
        }
    }

    function remove_customer_order_review($orderreviewID)
    {
        
        $this->db->delete('srp_erp_srm_orderreviewmaster', array('orderreviewID' => $orderreviewID));
        $this->db->delete('srp_erp_srm_orderreviewdetails', array('orderreviewID' => $orderreviewID));
        return true;
    }

    function fetch_group_company_employee_detail($companyID){
        $this->db->trans_start();
       
        $groupCompanyID = $this->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
            )->row('companyGroupID');

            if(!empty($groupCompanyID)){
                $companyList = $this->db->query(
                    "SELECT companyID 
                     FROM srp_erp_companygroupdetails 
                     WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
                    )->result_array();
            }

           

            $this->db->SELECT("srp_employeesdetails.EIdNo,srp_employeesdetails.Ename2,srp_employeesdetails.EmpSecondaryCode,srp_employeedesignation.EmpDesignationID,srp_designation.DesDescription");
            if(!empty($groupCompanyID)) {
                $companyArray=[];
                if (count($companyList)>0) {
                    foreach($companyList as $val){
                        $companyArray[]=$val['companyID'];
                    }
                }
                $this->db->FROM('srp_employeesdetails,srp_erp_companygroupdetails AS cmpTB');
                $this->db->where_in('cmpTB.companyID',$companyArray);
                $this->db->group_by('srp_employeesdetails.EIdNo');
            } else {
                $this->db->FROM('srp_employeesdetails');
                $this->db->WHERE('Erp_companyID', $companyID);
            }
            $this->db->join('srp_employeedesignation', 'srp_employeedesignation.EmpID = srp_employeesdetails.EIdNo ', 'left');
            $this->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeedesignation.DesignationID ', 'left');
            $this->db->WHERE('srp_employeesdetails.empConfirmedYN', 1);        
            $this->db->WHERE('srp_employeesdetails.isDischarged', 0);
            $this->db->WHERE('srp_employeedesignation.isMajor', 1);
            $data = $this->db->get()->result_array();

            $data_arr =[];
            $this->db->trans_complete();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array("status"=>false ,"dataMaster"=>'');
            } else {
                $this->db->trans_commit();
                
                return array("status"=>true ,"dataMaster"=>$data);
            }
            
    }

    function add_sub_itemMaster_tmpTbl($qty, $itemAutoID, $masterID, $detailID, $code, $itemCode, $data, $co)
    {

        $uom = isset($data['uom']) && !empty($data['uom']) ? $data['uom'] : null;
        $uomID = isset($data['uomID']) && !empty($data['uomID']) ? $data['uomID'] : null;
        $grv_detailID = isset($data['grv_detailID']) && !empty($data['grv_detailID']) ? $data['grv_detailID'] : null;
        $warehouseAutoID = isset($data['warehouseAutoID']) && !empty($data['warehouseAutoID']) ? $data['warehouseAutoID'] : null;
        $data_subItemMaster = array();
        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $itemAutoID;
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $itemCode . '/GRV/' . $grv_detailID . '/' . $i;
                $data_subItemMaster[$x]['uom'] = $uom;
                $data_subItemMaster[$x]['wareHouseAutoID'] = $warehouseAutoID;
                $data_subItemMaster[$x]['uomID'] = $uomID;
                $data_subItemMaster[$x]['receivedDocumentID'] = $code;
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = $masterID;
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = $detailID;
                $data_subItemMaster[$x]['companyID'] = $co;
                // $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                // $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                // $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                // $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
        }

        if (!empty($data_subItemMaster)) {
            /** bulk insert to item master sub */
            $this->batch_insert_srp_erp_itemmaster_subtemp($data_subItemMaster);
        }
    }

    function batch_insert_srp_erp_itemmaster_subtemp($data)
    {
        $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data);
    }

    function company_request_referback_level_approval(){

        $masterID =trim($this->input->post('masterID') ?? '');
        $com = $this->common_data['company_data']['company_id'];

        if($masterID){

            $this->db->select('*');
            $this->db->where('companyReqID', $masterID);
            $this->db->from('srp_erp_srm_vendor_company_requests');
            $master_req = $this->db->get()->row_array();

            $this->db->select('*');
            $this->db->where('companyReqMasterID', $masterID);
            $this->db->where('isApprovalLevelReferBack', 1);
            $this->db->from('srp_erp_srm_vendor_company_request_documents');
            $master_doc = $this->db->get()->result_array();

            $token = $this->getLoginToken();
            
            $token_array=json_decode($token);

            if($master_doc){

                foreach($master_doc as $val){
                    $master_n = [
                        "documentID"=> $val['documentBackendID'],
                        "type"=> 2,
                        "comment"=>$val['comment'],
                        "backendID"=>$val['reqDocID']
                    
                    ];
            
                    if($token_array){
            
                        if($token_array->success==true){
                           
                            $res=$this->updateApproveDocumentDetails($master_n,$token_array->data->token);
            
                           $res_array=json_decode($res);
                           //print_r($res);exit;
                           if($res_array->status==true){
                            $data_detail1['submitApprovalYN'] = 1;
            
                            $this->db->where('reqDocID', $val['reqDocID']);
                            $this->db->update('srp_erp_srm_vendor_company_request_documents', $data_detail1);
            
                           }
                        }
                    }
                }

            }

            $master_n_ref = [
                "documentID"=> $master_req['portalReqID'],
                "type"=> 2,
                "comment"=>''
            
            ];
    
            if($token_array){
    
                if($token_array->success==true){
                   
                    $res=$this->updateRejectCompanyRequestDetails($master_n_ref,$token_array->data->token);
    
                   $res_array1=json_decode($res);
                  
                   if($res_array1->status==true){
    
                       // $this->send_supplier_company_request_status($master_req['contactPersonEmail'],2);
                        $data_detail_master['submitRejectYN'] = 1;
                        $data_detail_master['confirmYN'] = 0;
                        $data_detail_master['approveYN'] = 2;
                        $data_detail_master['erpSupplierID'] = null;
                        $data_detail_master['systemSupplierID'] = null;
        
                        $this->db->where('companyReqID', $masterID);
                        $this->db->update('srp_erp_srm_vendor_company_requests', $data_detail_master);

                        $this->load->library('approvals');
                        $status = $this->approvals->approve_delete($master_req['systemSupplierID'], 'SUP',false);
                        
                        if ($status == 1) {

                            $this->db->where('supplierAutoID', $master_req['erpSupplierID']);
                            $this->db->where('companyID', $com);
                            $this->db->delete('srp_erp_srm_supplieritems');

                            $this->db->where('supplierAutoID', $master_req['systemSupplierID']);
                            $this->db->delete('srp_erp_suppliermaster');

                            $this->db->where('supplierAutoID', $master_req['erpSupplierID']);
                            $this->db->delete('srp_erp_srm_suppliermaster');
                            
                            //return array('s', ' Referred Back Successfully.', $status);
                        } else {
                            //return array('e', ' Error in refer back.', $status);
                        }
                       
                   }
                }
            }

        }else{
            return array('w','Company Request Not Found !');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','error');
        } else {
            $this->db->trans_commit();
            
            return array('s','Refer Back Successfully Completed.');
        }
    }

    function send_checking_api(){

        $jayParsedAry = [
            "username" => $this->config->item('vendor_portal_api_username'),
            "password"=>$this->config->item('vendor_portal_api_password')
        ];

        $token = getLoginToken();

        $arr =array("envdata"=>$jayParsedAry,"login"=>$token);
                            
        // $token_array=json_decode($token);

        //print_r($jayParsedAry);exit;

        return $arr;
    }

    function save_order_review_auto_with_po_generate($autoappLevel,$reviewID)
    {


        
            $system_code = $reviewID;
            $level_id = 0;
            $status = 1;
            $comments = '';
            $code = 'ORD-RVW';
        

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderreviewmaster');
        $this->db->where('orderreviewID', $system_code);
        $code = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderinquirymaster');
        $this->db->where('inquiryID', $code['inquiryID']);
        $ordrinq = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_srm_orderreviewdetails');
        $this->db->where('orderreviewID', $system_code);
        $this->db->group_by("supplierID");
        $supplierIDs = $this->db->get()->result_array();

        $supnames=array();
        foreach ($supplierIDs as $supvl){

            $this->db->select('*');
            $this->db->from('srp_erp_srm_suppliermaster');
            $this->db->where('supplierAutoID', $supvl['supplierID']);
            $supplierIDerp = $this->db->get()->row_array();

            if(empty($supplierIDerp['erpSupplierAutoID']) || $supplierIDerp['erpSupplierAutoID']==null || $supplierIDerp['erpSupplierAutoID']==''){
                array_push($supnames,$supplierIDerp['supplierName']);
            }
        }

        if(!empty($supnames)){
            $jnsup=join(" , ",$supnames);
            $this->session->set_flashdata('e', 'Following suppliers are not linked. '.$jnsup.'');
            return false;
            exit;
        }

        $this->db->trans_start();
        $this->load->library('Approvals');

        // if($autoappLevel==0){
            $approvals_status=1;
        // }else{
        //     $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, $code['documentID']);
        // }

        // if($approvals_status == 3){
        //     $inquiryMasterID_now = trim($this->input->post('inquiryMasterID') ?? '');

        //     $datanow['isOrderReviewConfirmYN'] = 0;
                        
        //     $this->db->where('inquiryID', $inquiryMasterID_now);
        //     $this->db->update('srp_erp_srm_orderinquirymaster', $datanow);
        // }

        if ($approvals_status == 1) {

            $data_master['isSelectedForPO'] = 0;
            $this->db->where('inquiryMasterID', $code['inquiryID']);
            $update = $this->db->update('srp_erp_srm_orderinquirydetails', $data_master);
            if ($update) {
                $this->db->select('*');
                $this->db->from('srp_erp_srm_orderreviewdetails');
                $this->db->where('orderreviewID', $system_code);
                $codeD = $this->db->get()->result_array();

                foreach ($codeD as  $vl) {
                    $datad['isSelectedForPO'] = 1;
                    $this->db->where('inquiryMasterID', $code['inquiryID']);
                    $this->db->where('supplierID', $vl['supplierID']);
                    $this->db->where('itemAutoID', $vl['itemAutoID']);
                    $this->db->update('srp_erp_srm_orderinquirydetails', $datad);
                }
            }





            $orderreviewID = $system_code;

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
                $datapdf['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }else{
                $this->db->select('srp_erp_srm_orderreviewmaster.*,srp_erp_srm_orderinquirymaster.documentCode as inquirycode');
                $this->db->join('srp_erp_srm_orderinquirymaster', 'srp_erp_srm_orderinquirymaster.inquiryID = srp_erp_srm_orderreviewmaster.inquiryID', 'left');
                $this->db->where('srp_erp_srm_orderreviewmaster.orderreviewID', $orderreviewID);
                $datapdf['reviewmaster']=$this->db->get('srp_erp_srm_orderreviewmaster')->row_array();
            }

            $inquiryID = $datapdf['reviewmaster']['inquiryID'];

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
            $datapdf['item'] = $this->db->get('srp_erp_srm_orderinquirydetails')->result_array();

            foreach($datapdf['item'] as $key => $val){
                $datapdf['item'][$key]['awsImage']=$this->s3->createPresignedRequest('uploads/itemMaster/'.$datapdf['item'][$key]['itemImage'], '1 hour');
            }
            $datapdf['supplierIDarr'] = $suparr;
            $datapdf['logo']=mPDFImage;
            if($this->input->post('html')){
                $datapdf['logo']=htmlImage;
            }

            // $html = $this->load->view('system/srm/order_review_html_view', $datapdf, true);



            // $this->load->library('pdf');
            // $path = UPLOAD_PATH.base_url().'/uploads/srm/'. $system_code .'SRM' . current_userID() . ".pdf";
            // $this->pdf->save_pdf($html, 'A4', 1, $path);


            foreach ($supplierIDs as $suvl){

                $this->db->select('*');
                $this->db->from('srp_erp_srm_suppliermaster');
                $this->db->where('supplierAutoID', $suvl['supplierID']);
                $suppliermstr = $this->db->get()->row_array();


                $this->db->select('*');
                $this->db->from('srp_erp_srm_orderinquirydetails');
                $this->db->where('inquiryDetailID', $suvl['inquiryDetailID']);
                $orderincdtl = $this->db->get()->row_array();

                /*$this->db->select('contactPerson,addressID,isDefault,addressType,addressDescription,contactPersonTelephone,contactPersonFaxNo,contactPersonEmail');
                $this->db->from('srp_erp_address');
                $this->db->join('srp_erp_addresstype', 'srp_erp_addresstype.addressTypeID = srp_erp_address.addressTypeID');
                $this->db->where('srp_erp_address.companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('srp_erp_addresstype.addressTypeDescription', 'Ship To');
                $shipto = $this->db->get()->row_array();*/
                $ship_data = fetch_address_po(1);

                $data['documentID'] = 'PO';
                $data['narration'] = 'SRM based PO';
                $data['supplierPrimaryCode'] = $suppliermstr['erpSupplierAutoID'];
                $data['purchaseOrderType'] = 'Standard';
                $data['referenceNumber'] = $code['documentSystemCode'];
                $data['creditPeriod'] = $suppliermstr['supplierCreditPeriod'];
                $data['supplierID'] = $suppliermstr['erpSupplierAutoID'];
                $data['supplierCode'] = $suppliermstr['supplierSystemCode'];
                $data['supplierName'] = $suppliermstr['supplierName'];
                $data['supplierAddress'] = $suppliermstr['supplierAddress1'] . ' ' . $suppliermstr['supplierAddress2'];
                $data['supplierTelephone'] = $suppliermstr['supplierTelephone'];
                $data['supplierFax'] = $suppliermstr['supplierFax'];
                $data['supplierEmail'] = $suppliermstr['supplierEmail'];
                $data['expectedDeliveryDate'] = $orderincdtl['expectedDeliveryDate'];
                $data['documentDate'] = current_date();

                /*    $data['shippingAddressID'] = $shipto['addressID'];
                $data['shippingAddressDescription'] = $shipto['addressDescription'];
                $data['shipTocontactPersonID'] = $shipto['contactPerson'];
                $data['shipTocontactPersonTelephone'] = $shipto['contactPersonTelephone'];
                $data['shipTocontactPersonFaxNo'] = $shipto['contactPersonFaxNo'];
                $data['shipTocontactPersonEmail'] = $shipto['contactPersonEmail'];*/

                $data['shippingAddressID'] = $ship_data['addressID'];
                $data['shippingAddressDescription'] = $ship_data['addressDescription'];
                $data['shipTocontactPersonID'] = $ship_data['contactPerson'];
                $data['shipTocontactPersonTelephone'] = $ship_data['contactPersonTelephone'];
                $data['shipTocontactPersonFaxNo'] = $ship_data['contactPersonFaxNo'];
                $data['shipTocontactPersonEmail'] = $ship_data['contactPersonEmail'];


                $segment_arr_default = default_segment_drop();
                $segment = explode('|', $segment_arr_default);
                $data['segmentID'] = trim($segment[0] ?? '');
                $data['segmentCode'] = trim($segment[1] ?? '');
                $trans_currency = currency_conversionID($ordrinq['transactionCurrencyID'], $ordrinq['transactionCurrencyID']);
                $data['transactionCurrencyID'] = $ordrinq['transactionCurrencyID'];
                $data['transactionCurrency'] = trim($trans_currency['CurrencyCode'] ?? '');
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data['termsandconditions'] = '';
                $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $data['supplierCurrencyID'] = $suppliermstr['supplierCurrencyID'];
                $data['supplierCurrency'] = $suppliermstr['supplierCurrency'];
                $data['isSrmCreated'] = 1;
                $supplierCurrency = currency_conversionID($data['transactionCurrencyID'], $data['supplierCurrencyID']);
                $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
                $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                //$data['purchaseOrderCode'] = $this->sequence->sequence_generator($data['documentID']);
                $pomaster=$this->db->insert('srp_erp_purchaseordermaster', $data);
                $po_id = $this->db->insert_id();

                //add pr document on po
                $po_doc_sub_id =$code['inquiryID'].'_'.$this->common_data['company_data']['company_id'].'_'.$suvl['supplierID'];
                $this->db->select('*');
                $this->db->from('srp_erp_documentattachments');
                $this->db->where('documentSubID', $po_doc_sub_id);
                $ordrinqdocuments = $this->db->get()->result_array();

                if(count($ordrinqdocuments)>0){

                    foreach($ordrinqdocuments as $doc_pr){

                        $dataattFromPr['documentID'] = 'PO';
                        $dataattFromPr['documentSystemCode'] = $po_id;
                        $dataattFromPr['attachmentDescription'] = 'PR Attachment';
                        $dataattFromPr['myFileName'] = $doc_pr['myFileName'];
                        $dataattFromPr['fileType'] = $doc_pr['fileType'];
                        $dataattFromPr['fileSize'] = $doc_pr['fileSize'];
                        $dataattFromPr['timestamp'] = date('Y-m-d H:i:s');
                        $dataattFromPr['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataattFromPr['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataattFromPr['createdUserGroup'] = $this->common_data['user_group'];
                        $dataattFromPr['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataattFromPr['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataattFromPr['modifiedUserName'] = $this->common_data['current_user'];
                        $dataattFromPr['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataattFromPr['createdPCID'] = $this->common_data['current_pc'];
                        $dataattFromPr['createdUserID'] = $this->common_data['current_userID'];
                        $dataattFromPr['createdUserName'] = $this->common_data['current_user'];
                        $dataattFromPr['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_documentattachments', $dataattFromPr);
                    }

                }
                
                if($pomaster){

                    $this->db->select('*');
                    $this->db->from('srp_erp_srm_orderreviewdetails');
                    $this->db->where('orderreviewID', $system_code);
                    $this->db->where('supplierID', $suvl['supplierID']);
                    $ordrDqry = $this->db->get()->result_array();

                    foreach ($ordrDqry as $dtlval){
                        $item_arr = fetch_item_data($dtlval['itemAutoID']);

                        $this->db->select('*');
                        $this->db->from('srp_erp_srm_orderinquirydetails');
                        $this->db->where('inquiryDetailID', $dtlval['inquiryDetailID']);
                        $orderincdtl = $this->db->get()->row_array();

                        $this->db->select('UnitShortCode');
                        $this->db->where('UnitID', $orderincdtl['defaultUOMID']);
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $masterUnitcod = $this->db->get('srp_erp_unit_of_measure')->row('UnitShortCode');

                        $dataD['purchaseOrderID'] = $po_id;
                        $dataD['itemAutoID'] = $dtlval['itemAutoID'];
                        $dataD['itemSystemCode'] = $item_arr['itemSystemCode'];
                        $dataD['itemType'] = $item_arr['mainCategory'];
                        $dataD['itemDescription'] = $item_arr['itemDescription'];
                        $dataD['unitOfMeasure'] = $masterUnitcod;
                        $dataD['unitOfMeasureID'] = $orderincdtl['defaultUOMID'];
                        $dataD['detailExpectedDeliveryDate'] = $orderincdtl['expectedDeliveryDate'];
                        $dataD['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                        $dataD['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                        $dataD['conversionRateUOM'] = conversionRateUOM_id($dataD['unitOfMeasureID'], $dataD['defaultUOMID']);
                        $dataD['requestedQty'] = $orderincdtl['supplierQty'];
                       // $dataD['unitAmount'] = $orderincdtl['supplierPrice'];
                      //  $dataD['discountAmount'] = $orderincdtl['supplierDiscount'];

                        $dataD['discountPercentage'] = $orderincdtl['supplierDiscountPercentage'];
                       // $dataD['discountAmount'] = $orderincdtl['supplierDiscount'];
                        //  $data['requestedQty'] = $quantityRequested[$key];
                      //  $dataD['unitAmount'] = ($orderincdtl['supplierPrice'] - $orderincdtl['supplierDiscount']);

                        $dataD['discountAmount'] = ($orderincdtl['supplierPrice']*$orderincdtl['supplierDiscountPercentage'])/100;
                        //  $data['requestedQty'] = $quantityRequested[$key];
                        $dataD['unitAmount'] = ($orderincdtl['supplierPrice'] - $dataD['discountAmount']);
                        $dataD['totalAmount'] = ($dataD['unitAmount'] * $orderincdtl['supplierQty']);
                        
                       // $dataD['totalAmount'] = ($dataD['unitAmount'] * $orderincdtl['supplierQty']);
                        $dataD['comment'] = 'pulled from SRM';
                        $dataD['remarks'] = '';
                        $dataD['companyID'] = $this->common_data['company_data']['company_id'];
                        $dataD['companyCode'] = $this->common_data['company_data']['company_code'];
                        $dataD['GRVSelectedYN'] = 0;
                        $dataD['goodsRecievedYN'] = 0;
                        $dataD['modifiedPCID'] = $this->common_data['current_pc'];
                        $dataD['modifiedUserID'] = $this->common_data['current_userID'];
                        $dataD['modifiedUserName'] = $this->common_data['current_user'];
                        $dataD['modifiedDateTime'] = $this->common_data['current_date'];
                        $dataD['createdUserGroup'] = $this->common_data['user_group'];
                        $dataD['createdPCID'] = $this->common_data['current_pc'];
                        $dataD['createdUserID'] = $this->common_data['current_userID'];
                        $dataD['createdUserName'] = $this->common_data['current_user'];
                        $dataD['createdDateTime'] = $this->common_data['current_date'];
                        $podetail=$this->db->insert('srp_erp_purchaseorderdetails', $dataD);
                    }



                    if($podetail){
                        $this->load->library('Approvals');
                        $this->load->library('sequence');
                        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
                        $currentuser = current_userID();
                        $companyID = $this->common_data['company_data']['company_id'];
                        $locationemployee = $this->common_data['emplanglocationid'];


                        ////pr value
                        $documentTotal = $this->db->query("SELECT srp_erp_purchaseordermaster.purchaseOrderID AS purchaseOrderID, srp_erp_purchaseordermaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
                        ( det.transactionAmount -( generalDiscountPercentage / 100 )* det.transactionAmount )+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                                            FROM srp_erp_purchaseordermaster
                                                LEFT JOIN ( SELECT SUM( totalAmount )+ ifnull( SUM( taxAmount ), 0 ) AS transactionAmount, purchaseOrderID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.purchaseOrderID = srp_erp_purchaseordermaster.purchaseOrderID
                                                LEFT JOIN (
                                                        SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                                        FROM srp_erp_taxledger 
                                                        WHERE documentID = 'PO' AND documentDetailAutoID IS NULL AND companyID = {$companyID} 
                                                        GROUP BY documentMasterAutoID 
                                                ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaseordermaster.purchaseOrderID ) 
                                            WHERE
                                                srp_erp_purchaseordermaster.purchaseOrderID = {$po_id} AND srp_erp_purchaseordermaster.companyID = {$companyID}")->row_array();
                        
                        $poLocalAmount = $documentTotal['total_value'] /$documentTotal['companyLocalExchangeRate'];
                                   
                        
                        $segmentID = $this->db->query("SELECT segmentID FROM srp_erp_purchaseordermaster where purchaseOrderID = $po_id AND companyID = {$companyID}")->row_array();


                        $this->db->select('purchaseOrderCode,supplierCurrencyExchangeRate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseOrderID,transactionCurrencyDecimalPlaces,documentDate,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentID,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyDecimalPlaces,supplierCurrencyDecimalPlaces');
                        $this->db->where('purchaseOrderID', $po_id);
                        $this->db->from('srp_erp_purchaseordermaster');
                        $po_data = $this->db->get()->row_array();
                        $docDate = $po_data['documentDate'];


                        $companyFinanceYearID = $this->db->query("SELECT
                            period.companyFinanceYearID as companyFinanceYearID
                        FROM
                            srp_erp_companyfinanceperiod period
                        WHERE
                            period.companyID = $companyID
                        AND '$docDate' BETWEEN period.dateFrom
                        AND period.dateTo
                        AND period.isActive = 1")->row_array();

                        if (empty($companyFinanceYearID['companyFinanceYearID'])) {
                            $companyFinanceYearID['companyFinanceYearID'] = NULL;
                        }

                        if ($locationwisecodegenerate == 1) {
                            $this->db->select('locationID');
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location == '')) {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            } else {
                                if ($locationemployee != '') {
                                    $codegeratorpo = $this->sequence->sequence_generator_location($data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $locationemployee, $po_data['invYear'], $po_data['invMonth']);
                                } else {
                                    $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                    return false;
                                }

                            }


                        } else {
                            $codegeratorpo = $this->sequence->sequence_generator_fin($po_data['documentID'], $companyFinanceYearID['companyFinanceYearID'], $po_data['invYear'], $po_data['invMonth']);
                        }


                        $pvCd = array(
                            'purchaseOrderCode' => $codegeratorpo
                        );
                        $this->db->where('purchaseOrderID', $po_id);
                        $this->db->update('srp_erp_purchaseordermaster', $pvCd);

                        $approvals_status = $this->approvals->CreateApproval('PO', $po_data['purchaseOrderID'], $codegeratorpo, 'Purchase Order', 'srp_erp_purchaseordermaster', 'purchaseOrderID', 0, $po_data['documentDate'],$segmentID['segmentID'],$poLocalAmount);


                    }
                    $imageuploadlocal = $this->config->item('ftp_image_uplod_local');
                    if($imageuploadlocal == 2){
                        $this->db->select('companyID');
                        $this->db->where('documentID', 'PO');
                        $num = $this->db->get('srp_erp_documentattachments')->result_array();
                        $file_name = 'PO' . '_' . $po_id . '_' . (count($num) + 1);


                        $cc = $this->common_data['company_data']['company_code'];
                        $folderPath = !empty($cc) ? $cc . '/' : '';
                        $config['upload_path'] = realpath(APPPATH . '../' . $folderPath . '/');
                        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
                        $config['max_size'] = '5120'; // 5 MB
                        $config['file_name'] = $file_name;
                        $cc = current_companyCode();
                        $file_name = "$file_name";
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload($path)) {
                           // die(json_encode(['e', "Upload failed"] . $this->upload->display_errors()));
                        } else {
                            $upload_data = $this->upload->data();
                        }
                        $flsiz=filesize($path);
                    }else{
                        $this->db->select('companyID');
                        $this->db->where('documentID', 'PO');
                        $num = $this->db->get('srp_erp_documentattachments')->result_array();
                        $file_name = 'PO' . '_' . $po_id . '_' . (count($num) + 1);
                        $config['upload_path'] = realpath(APPPATH . '../attachments');
                        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
                        $config['max_size'] = '5120'; // 5 MB
                        $config['file_name'] = $file_name;

                        /** call s3 library */
                        /*$file = $path;
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

                        if(empty($ext)) {
                            $this->session->set_flashdata('e', 'No extension found for the selected attachment');
                            return false;
                        }*/
                        $flsiz=filesize($path);
                        $input = $this->s3->inputFile($path);
                        $cc = current_companyCode();
                        $folderPath = !empty($cc) ? $cc . '/' : '';
                        if ($this->s3->putMyObject($input, $folderPath . $file_name . '.' . 'pdf')) {
                            $s3Upload = true;
                        } else {
                            $s3Upload = false;
                        }

                        /** end of s3 integration */
                    }


                    $dataatt['documentID'] = 'PO';
                    $dataatt['documentSystemCode'] = $po_id;
                    $dataatt['attachmentDescription'] = 'Automatic Attachment';
                    $dataatt['myFileName'] = $folderPath . $file_name . '.' . 'pdf';
                    $dataatt['fileType'] = trim('pdf');
                    $dataatt['fileSize'] = $flsiz;
                    $dataatt['timestamp'] = date('Y-m-d H:i:s');
                    $dataatt['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataatt['companyCode'] = $this->common_data['company_data']['company_code'];
                    $dataatt['createdUserGroup'] = $this->common_data['user_group'];
                    $dataatt['modifiedPCID'] = $this->common_data['current_pc'];
                    $dataatt['modifiedUserID'] = $this->common_data['current_userID'];
                    $dataatt['modifiedUserName'] = $this->common_data['current_user'];
                    $dataatt['modifiedDateTime'] = $this->common_data['current_date'];
                    $dataatt['createdPCID'] = $this->common_data['current_pc'];
                    $dataatt['createdUserID'] = $this->common_data['current_userID'];
                    $dataatt['createdUserName'] = $this->common_data['current_user'];
                    $dataatt['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $dataatt);

                    // send po in vendor poratal

                    $this->db->select('*');
                    $this->db->where('purchaseOrderID', $po_id);
                    $this->db->from('srp_erp_purchaseordermaster');
                    $data_po_master['master'] = $this->db->get()->row_array();

                    $data_po_master['master']['srmSupplierID'] = $suvl['supplierID'];
                    $data_po_master['master']['company_name'] = $this->common_data['company_data']['company_name'];
                    $data_po_master['master']['company_address1'] = $this->common_data['company_data']['company_address1'];
                    $data_po_master['master']['company_address2'] = $this->common_data['company_data']['company_address2'];
                    $data_po_master['master']['company_city'] = $this->common_data['company_data']['company_city'];
                    $data_po_master['master']['company_province'] = $this->common_data['company_data']['company_province'];
                    $data_po_master['master']['company_country'] = $this->common_data['company_data']['company_country'];
                    $data_po_master['master']['company_code'] = $this->common_data['company_data']['company_code']; 
                    $data_po_master['master']['inquiryID'] = $code['inquiryID'];
                    $data_po_master['master']['purchaseOrderIDBackend'] = $po_id;

                    $master_n = [
                        "inquiryID"=> $code['inquiryID'],
                        "supplierID"=>$suvl['supplierID'],
                        "companyID"=>$this->common_data['company_data']['company_id'],
                    ]; 

                    $data_po_master['rfq'] = $master_n;

                    $this->db->select('*');
                    $this->db->from('srp_erp_purchaseorderdetails');
                    $this->db->where('purchaseOrderID', $po_id);
                    $data_po_master['details'] = $this->db->get()->result_array();

                    foreach($data_po_master['details'] as $key1=>$val_data){
                        $data_po_master['details'][$key1]['erpBackendID'] = $val_data['purchaseOrderDetailsID'];
                        $data_po_master['details'][$key1]['erpBackendMasterID'] = $po_id;
                    }

                    $this->db->select('*');
                    $this->db->from('srp_erp_documentattachments');
                    $this->db->where('documentSystemCode', $po_id);
                    $data_po_master['document'] = $this->db->get()->row_array();

                    $purchaseOrderID = $po_id;
                   // $data_po_master['extra'] = $this->Procurement_modal->fetch_po_template_data_for_supplier_portal($purchaseOrderID);
                
                   // $data_po_master['isGroupBasedTaxEnable'] = (existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID')!=''?existTaxPolicyDocumentWise('srp_erp_purchaseordermaster',$purchaseOrderID,'PO','purchaseOrderID'):0);

                    $token = $this->getLoginToken();

                    $token_array=json_decode($token);
            
                    if($token_array){
            
                        if($token_array->success==true){
                        
                            $res=$this->saveCreatePoSupplierSide($data_po_master,$token_array->data->token);

                           // print_r($res);exit;
            
                            $res_array=json_decode($res);

                            if($res_array->status==true){
                                $data_detail1['isPortalPOSubmitted'] = 1;
                        
                                $this->db->where('purchaseOrderID', $purchaseOrderID);
                                $this->db->update('srp_erp_purchaseordermaster', $data_detail1);

                               // $this->send_company_approve_email_supplier($suvl['supplierID'],2);
                            }
                        }
                    }
                    // $res=$this->saveCreatePoSupplierSide($data_po_master);

                    // $res_array=json_decode($res);

                    // if($res_array->action_status==true){
                    //     $data_detail1['isPortalPOSubmitted'] = 1;
                        
                    //     $this->db->where('purchaseOrderID', $purchaseOrderID);
                    //     $this->db->update('srp_erp_purchaseordermaster', $data_detail1);

                    //     $this->send_company_approve_email_supplier($suvl['supplierID'],2);

                    // }

                }
            }

            unlink($path);
           // $this->session->set_flashdata('s', 'Approved Successfully.');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {

            $this->db->trans_commit();
            return true;
        }
    }

}