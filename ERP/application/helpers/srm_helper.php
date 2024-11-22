<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*DO NOT USE THIS THIS IS AN EXAMPLE  */
if (!function_exists('load_all_custer_drop')) {
    function load_all_custer_drop()
    {
        $CI =& get_instance();
        $CI->db->select("CustomerName");
        $CI->db->from('srp_erp_srm_customermaster');
        $result = $CI->db->get()->result_array();
        $customer_err = array('' => 'Select Status');
        if (!empty($result)) {
            foreach ($result as $row) {
                $customer_err[trim($row['customerID'] ?? '')] = (trim($row['customerName'] ?? ''));
            }
        }
        return $customer_err;
    }
}

if (!function_exists('all_srm_customer_drop')) {
    function all_srm_customer_drop($status = true) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("CustomerAutoID,CustomerName");
        $CI->db->from('srp_erp_srm_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $customer_arr = array('' => 'Select Customer');
        } else {
            $customer_arr = [];
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $customer_arr[trim($row['CustomerAutoID'] ?? '')] = (trim($row['CustomerName'] ?? ''));
            }
        }
        return $customer_arr;
    }
}

if (!function_exists('all_srm_Currency_drop')) {
    function all_srm_Currency_drop($status = true) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("CustomerAutoID,customerCurrency");
        $CI->db->from('srp_erp_srm_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $currncy_arr = array('' => 'Select Customer');
        } else {
            $currncy_arr = [];
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $currncy_arr[trim($row['CustomerAutoID'] ?? '')] = (trim($row['customerCurrency'] ?? ''));
            }
        }
        return $currncy_arr;
    }
}


if (!function_exists('all_srm_supplier_drop')) {
    function all_srm_supplier_drop($status = true) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_srm_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();

        if ($status) {
            $supplier_arr = array('' => 'Select supplier');
        } else {
            $supplier_arr = [];
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierName'] ?? ''));
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('all_srm_supplier_drop_for_company_request')) {
    function all_srm_supplier_drop_for_company_request($status = true) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_srm_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isSupplierAcc', 0);
        $supplier = $CI->db->get()->result_array();

        if ($status) {
            $supplier_arr = array('' => 'Select supplier');
        } else {
            $supplier_arr = [];
        }
        if (isset($supplier)) {
            foreach ($supplier as $row) {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierName'] ?? ''));
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('all_srm_supplie_Currency_drop')) {
    function all_srm_supplie_Currency_drop($status = true) /*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->select("supplierAutoID,supplierCurrency");
        $CI->db->from('srp_erp_srm_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $customer = $CI->db->get()->result_array();
        if ($status) {
            $currncy_arr = array('' => 'Select currency');
        } else {
            $currncy_arr = [];
        }
        if (isset($customer)) {
            foreach ($customer as $row) {
                $currncy_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierCurrency'] ?? ''));
            }
        }
        return $currncy_arr;
    }
}

/*Load all countries for select2*/
if (!function_exists('load_all_countries')) {
    function load_all_countries($status = true)/*Load all Supplier*/
    {
        $CI =& get_instance();
        $CI->db->SELECT("countryID,countryShortCode,CountryDes");
        $CI->db->FROM('srp_erp_countrymaster');
        $countries = $CI->db->get()->result_array();
        $countries_arr = array('' => 'Select Country');
        if (isset($countries)) {
            foreach ($countries as $row) {
                $countries_arr[trim($row['countryID'] ?? '')] = trim($row['CountryDes'] ?? '');
            }
        }
        return $countries_arr;
    }
}

/*Load all SRM Customers for select2*/
if (!function_exists('all_srm_customers')) {
    function all_srm_customers($status = true)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CustomerAutoID,CustomerName");
        $CI->db->FROM('srp_erp_customermaster');
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status) {
            $data_arr = array('' => 'Select Customer');
        } else {
            $data_arr = [];
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['CustomerAutoID'] ?? '')] = trim($row['CustomerName'] ?? '');
            }
        }
        return $data_arr;
    }
}

/*Load all campaign status*/
if (!function_exists('all_customer_order_status')) {
    function all_customer_order_status($custom = true)
    {
        $CI =& get_instance();
        $CI->db->select("statusID,description,documentID");
        $CI->db->from('srp_erp_srm_status');
        $CI->db->where('documentID', 3);
        $CI->db->where('isActive', 1);
        //$CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $status = $CI->db->get()->result_array();
        if ($custom) {
            $status_arr = array('' => 'Select Status');
        } else {
            $status_arr = array('' => 'Status');
        }
        if (isset($status)) {
            foreach ($status as $row) {
                $status_arr[trim($row['statusID'] ?? '')] = (trim($row['description'] ?? ''));
            }
        }
        return $status_arr;
    }
}
/*Load all order inquiry reviews*/
if (!function_exists('all_order_inquiries')) {
    function all_order_inquiries($custom = true)
    {
        $CI =& get_instance();
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
        return $inquiry_arr;
    }
}

if (!function_exists('orerew_action_approval')) {
    function orerew_action_approval($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_address_po')) {
    function fetch_address_po($id)
    {
        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_address');
        $CI->db->where('addressID', $id);
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('load_prq_action_srm')) { /*get po action list*/
    function load_prq_action_srm($purchaseRequestID, $POConfirmedYN, $approved, $createdUserID,$isDeleted,$POconfirmedByEmp)
    {
        $CI =&get_instance();
        $CI->load->library('session');
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('procurement_approval', $primaryLanguage);

        $purchaseRequest = $CI->lang->line('procurement_purchase_request');
        $EditPurchaseRequest = $CI->lang->line('procurement_edit_purchase_request');

        $status = '<span class="">';

        if ($POConfirmedYN != 1 && $isDeleted==0) {
           
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PRQ\',\'' . $purchaseRequestID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn"></span></a>';
           }
        if ($POConfirmedYN == 1 && $isDeleted==0) {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'PRQ\',\'' . $purchaseRequestID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn"></span></a>';

        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_company_request_action')) { /*get po action list*/
    function load_company_request_action($requestID, $approveYN)
    {
        $CI =&get_instance();
        $CI->load->library('session');

        $status = '<span class="">';

        if ($approveYN==0) {
           
            $status .= '<a onclick="view_vendor_request_document(' . $requestID . ');"><span title="view document" rel="tooltip" class="glyphicon glyphicon-th-list" style="color:rgb(75, 71, 209);"></span></a>';
            $status .= '&nbsp; | &nbsp;<a onclick="approve_vendor_company_request(' . $requestID . ')" ><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok" style="color:rgb(48, 224, 67);"></span></a>';
        }else{
            $status .= '<a onclick="view_vendor_request_document(' . $requestID . ');"><span title="view document" rel="tooltip" class="glyphicon glyphicon-th-list" style="color:rgb(75, 71, 209);"></span></a>';
        }
       

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_company_request_status')) {
    function load_company_request_status($approvedYN,$confirmYN)
    {
        $status = '';

        if($confirmYN ==1){

            if(($confirmYN ==1 && $approvedYN == 0) || $confirmYN ==1 && $approvedYN == 4){
                $status .= '<div class="text-center"><span class="label label-primary" style ="background-color:#f4ec67 !important">&nbsp;</span></div>';
            }else{
                if ($approvedYN == 1) {
                    $status .= '<div class="text-center"><span class="label label-success">&nbsp;</span></div>';
                
                }else if($approvedYN == 2){
                    $status .= '<div class="text-center"><span class="label label-warning">&nbsp;</span></div>';
                } else if($approvedYN == 3){
                    $status .= '<div class="text-center"><span class="label label-danger">&nbsp;</span></div>';
                }else if($approvedYN == 4){
                    $status .= '<div class="text-center"><span class="label label-primary" style ="background-color:#22a6b3 !important">&nbsp;</span></div>';
                }
            }
           
            // else  {
            //     $status .= '<div class="text-center"><span class="label label-info">&nbsp;</span></div>';
            // }
        }else{

            if($approvedYN == 2){
                $status .= '<div class="text-center"><span class="label label-warning">&nbsp;</span></div>';
            } else if($approvedYN == 3){
                $status .= '<div class="text-center"><span class="label label-danger">&nbsp;</span></div>';
            }else if($approvedYN == 4){
                $status .= '<div class="text-center"><span class="label label-primary" style ="background-color:#22a6b3 !important">&nbsp;</span></div>';
            }else{
                $status .= '<div class="text-center"><span class="label label-info">&nbsp;</span></div>';
            }
           
        }
        
        return $status;
    }
}

if (!function_exists('load_company_request_vendor_image')) {
    function load_company_request_vendor_image($id,$name,$email,$address)
    {
        $status = '';
        $status .= '<div class="contact-box">
                           
                            <div class="link-box"><h5 class="contacttitle fw-600"><a class="link-person noselect"
                                    href="#" onclick="">'.$name.'</a><br>'.$email.'<br>'.$address.'</a>
                                </h5></div>
                        </div>';
        return $status;
    }
}

if (!function_exists('load_company_request_vendor_view_action')) {
    function load_company_request_vendor_view_action($id,$approved,$confirmYN)
    {
        $status = '';
        if(($approved==4 && $confirmYN==0) || ($approved==0 && $confirmYN==0)){
            $status .= '<a
            onclick="fetchPage(\'system/srm/supplier/srm_supplier_request_view\',' . $id . ',\'View Supplier Company Request\')">
            <span title="" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"
                  data-original-title="Edit"></span></a>';
        }else{
            $status .= '<a
            onclick="fetchPage(\'system/srm/supplier/srm_supplier_request_view_show\',' . $id . ',\'View Supplier Company Request\')">
            <span title="" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn"
                  data-original-title="Edit"></span></a>';
        }
       
        // if($approved==0){
        //     $status .= '<a
        //     onclick="reject_vendor_company_request(' . $id . ');">
        //         <span title="" rel="tooltip" class="glyphicon glyphicon-remove"
        //                 data-original-title="Reject"></span></a>';
        // }
        
        return $status;
    }
}

if (!function_exists('load_prq_action')) {
    function load_prq_action($id,$code,$text)
    {
        $status = '';
        $status .= '<div class="text-center">                           
                            <a class="btn btn-primary-new size-sm" onclick=\'add_prq_id("' . $id . '","' . $code . '","' . $text . '"); \'>Add</a>
                        </div>';
        
        return $status;
    }
}

if (!function_exists('load_order_review_action')) {
    function load_order_review_action($poID,$rewID)
    {
        $document = 'ORD-RVW';
        $status = '';
        $status .= '<div class="text-center">                           
                            <a onclick=\'fetch_approval_view_review("' . $rewID . '"); \'><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" data-original-title="View"></span></a>
                        </div>';
        
        return $status;
    }
}


if (!function_exists('load_order_review_action_pending')) {
    function load_order_review_action_pending($poID)
    {
        $document = 'ORD-RVW';
        $status = '';
        // $status .= '<div class="text-center">                           
        //                     <a class="btn btn-primary-new size-xs" onclick=\'load_order_inquiry_details_for_genarate("' . $poID . '"); \'>Generate</a>
        //                 </div>';
                        $status .= '<a 
                        onclick="fetchPage(\'system/srm/srm_order_review_statement\',' . $poID . ',\'statement\')">
                        <span title="" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" data-original-title="View"></span></a>';
        
        return $status;
    }
}

if (!function_exists('load_selfservice_order_review_action_pending')) {
    function load_selfservice_order_review_action_pending($poID)
    {
        $document = 'ORD-RVW';
        $status = '';
        // $status .= '<div class="text-center">                           
        //                     <a class="btn btn-primary-new size-xs" onclick=\'load_order_inquiry_details_for_genarate("' . $poID . '"); \'>Generate</a>
        //                 </div>';
                        $status .= '<a 
                        onclick="fetchPage(\'system/srm/selfservice_order_review_statement\',' . $poID . ',\'statement\')">
                        <span title="" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn" data-original-title="View"></span></a>';
        
        return $status;
    }
}

if (!function_exists('load_order_review_status_pending')) {
    function load_order_review_status_pending($poID)
    {
        $document = 'ORD-RVW';

        $CI =& get_instance();
       
        $companyID= $CI->common_data['company_data']['company_id'];

       

        $reqcount = $CI->db->query("SELECT
                COUNT(t1.inquiryDetailID) as reqcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                FROM
                `srp_erp_srm_orderinquirydetails`
                LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                WHERE
                `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$poID}' 
                AND `isRfqCreated` = 1 
                --  AND isRfqEmailed = 1 
                GROUP BY
                `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('reqcount');


        $subcount = $CI->db->query("SELECT
                COUNT(t1.inquiryDetailID) as subcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                FROM
                `srp_erp_srm_orderinquirydetails`
                LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                WHERE
                `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$poID}' 
                AND `isRfqCreated` = 1 
                --  AND isRfqEmailed = 1 
                AND isSupplierSubmited = 1 
                GROUP BY
                `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('subcount');


        $status = '';
        // if ($approvedYN == 1) {

            if($subcount==0){
                $status .= '<div class="text-center"><span class="label label-warning">Pending</span></div>';
            }else{
                if($reqcount==$subcount){
                    $status .= '<div class="text-center"><span class="label label-success">Fully Submitted</span></div>';
                }else{
                    $status .= '<div class="text-center"><span class="label label-info">Partially Submitted</span></div>';
                }
            }
            
        
        // }
        
        return $status;
    }
}

if (!function_exists('load_order_submit_count')) {
    function load_order_submit_count($poID)
    {
        $document = 'ORD-RVW';

        $CI =& get_instance();
        
        $companyID= $CI->common_data['company_data']['company_id'];

        

        $reqcount = $CI->db->query("SELECT
                COUNT(t1.inquiryDetailID) as reqcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                FROM
                `srp_erp_srm_orderinquirydetails`
                LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                WHERE
                `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$poID}' 
                AND `isRfqCreated` = 1 
                --  AND isRfqEmailed = 1 
                GROUP BY
                `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('reqcount');


        $subcount = $CI->db->query("SELECT
                COUNT(t1.inquiryDetailID) as subcount  FROM (SELECT srp_erp_srm_orderinquirydetails.inquiryDetailID
                FROM
                `srp_erp_srm_orderinquirydetails`
                LEFT JOIN `srp_erp_srm_suppliermaster` ON `srp_erp_srm_orderinquirydetails`.`supplierID` = `srp_erp_srm_suppliermaster`.`supplierAutoID` 
                WHERE
                `srp_erp_srm_orderinquirydetails`.`companyID` = '{$companyID}' 
                AND `srp_erp_srm_orderinquirydetails`.`inquiryMasterID` = '{$poID}' 
                AND `isRfqCreated` = 1 
                --  AND isRfqEmailed = 1 
                AND isSupplierSubmited = 1 
                GROUP BY
                `srp_erp_srm_orderinquirydetails`.`supplierID`) t1")->row('subcount');


        $status = '';
        // if ($approvedYN == 1) {
            $status .= '<strong class="contacttitle">Requested Count:'.$reqcount.'</strong><br><strong class="contacttitle">Submitted Count :'.$subcount.'</strong>';
            
        // }
        
        return $status;
    }
}







