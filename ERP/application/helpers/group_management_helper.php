<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*DO NOT USE THIS THIS IS AN EXAMPLE  */
if (!function_exists('get_company_accoding_to_id'))
{
    function get_company_accoding_to_id($companyID)
    {
        $CI = &get_instance();
        if ($companyID != '')
        {
            $company = $CI->db->query("SELECT
	 company_code,company_name
FROM
	srp_erp_company
WHERE company_id = ($companyID)")->row_array();
        }
        return $company;
    }
}

if (!function_exists('get_group_customer_details'))
{
    function get_group_customer_details($groupCustomerAutoID, $companyID)
    {

        /**/

        $masterGroupID = getParentgroupMasterID();

        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 customerMasterID
FROM
	srp_erp_groupcustomerdetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupCustomerMasterID=$groupCustomerAutoID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_supplier_details'))
{
    function get_group_supplier_details($groupSupplierMasterID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 SupplierMasterID
FROM
	srp_erp_groupsupplierdetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupSupplierMasterID=$groupSupplierMasterID")->row_array();
        }
        return $customer;
    }
}


if (!function_exists('get_group_chartofaccounts_details'))
{
    function get_group_chartofaccounts_details($groupChartofAccountMasterID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
                    chartofAccountID
                FROM
                    srp_erp_groupchartofaccountdetails
                WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupChartofAccountMasterID=$groupChartofAccountMasterID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_segment_details'))
{
    function get_group_segment_details($groupSegmentID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
            segmentID
        FROM
            srp_erp_groupsegmentdetails
        WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND groupSegmentID=$groupSegmentID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_customer_category_details'))
{
    function get_group_customer_category_details($groupCustomerCategoryID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
            partyCategoryID
        FROM
            srp_erp_grouppartycategorydetails
        WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND groupPartyCategoryID=$groupCustomerCategoryID")->row_array();
        }
        return $customer;
    }
}
if (!function_exists('get_group_category_details'))
{
    function get_group_category_details($groupItemCategoryID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 itemCategoryID
FROM
	srp_erp_groupitemcategorydetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupItemCategoryID=$groupItemCategoryID")->row_array();
        }
        return $customer;
    }
}


if (!function_exists('get_group_segment_details'))
{
    function get_group_segment_details($groupSegmentID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 segmentID
FROM
	srp_erp_groupsegmentdetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupSegmentID=$groupSegmentID")->row_array();
        }
        return $customer;
    }
}


if (!function_exists('get_group_item_details'))
{
    function get_group_item_details($groupItemMasterID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 ItemAutoID
FROM
	srp_erp_groupitemmasterdetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupItemMasterID=$groupItemMasterID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_uom_details'))
{
    function get_group_uom_details($groupUOMMasterID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
                UOMMasterID
            FROM
                srp_erp_groupuomdetails
            WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupUOMMasterID=$groupUOMMasterID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_warehouse_details'))
{
    function get_group_warehouse_details($groupwareHouseAutoID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 warehosueMasterID
FROM
	srp_erp_groupwarehousedetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupWarehouseMasterID=$groupwareHouseAutoID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('get_group_customer_category_details'))
{
    function get_group_customer_category_details($groupCustomerCategoryID, $companyID)
    {
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $Grpid = $companyGroup['companyGroupID'];
        if ($companyID != '')
        {
            $customer = $CI->db->query("SELECT
	 partyCategoryID
FROM
	srp_erp_grouppartycategorydetails
WHERE companyID = $companyID AND companyGroupID=$masterGroupID AND  groupPartyCategoryID=$groupCustomerCategoryID")->row_array();
        }
        return $customer;
    }
}

if (!function_exists('dropdown_company_group_customer_categories'))
{
    function dropdown_company_group_customer_categories($companyID, $partyCategoryID = null)
    {
        $CI = &get_instance();
        $segment = array();


        if ($companyID != '')
        {
            $segment = $CI->db->query("SELECT
	 partyCategoryID,categoryDescription,companyCode
FROM
	srp_erp_partycategories
WHERE companyID = ($companyID) AND partyType = 1
AND NOT EXISTS
        (
        SELECT  partyCategoryID
        FROM    srp_erp_grouppartycategorydetails
        WHERE   srp_erp_partycategories.partyCategoryID = srp_erp_grouppartycategorydetails.partyCategoryID AND srp_erp_partycategories.partyType = 1
        )")->result_array();
        }

        if ($partyCategoryID != '')
        {
            $cust = $CI->db->query("SELECT
	partyCategoryID,categoryDescription,companyCode
FROM
	srp_erp_partycategories
WHERE partyCategoryID = ($partyCategoryID)")->row_array();
        }
        $data_arr = array('' => 'Select Category');

        if (!empty($cust))
        {
            $data_arr[trim($cust['partyCategoryID'] ?? '')] = trim($cust['companyCode'] ?? '') . ' | ' . trim($cust['categoryDescription'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['partyCategoryID'] ?? '')] = trim($row['companyCode'] ?? '') . ' | ' . trim($row['categoryDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_gl_account_desc_cus_group'))
{
    function fetch_gl_account_desc_cus_group($id)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupchartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);

        return $CI->db->get()->row_array();
    }
}


if (!function_exists('dropdown_company_group_supplier_categories'))
{
    function dropdown_company_group_supplier_categories($companyID, $partyCategoryID = null)
    {
        $CI = &get_instance();
        $segment = array();


        if ($companyID != '')
        {
            $segment = $CI->db->query("SELECT
	 partyCategoryID,categoryDescription,companyCode
FROM
	srp_erp_partycategories
WHERE companyID = ($companyID) AND partyType = 2
AND NOT EXISTS
        (
        SELECT  groupPartyCategoryDetailID
        FROM    srp_erp_grouppartycategorydetails
        WHERE   srp_erp_partycategories.partyCategoryID = srp_erp_grouppartycategorydetails.partyCategoryID AND srp_erp_partycategories.partyType = 2
        )")->result_array();
        }

        if ($partyCategoryID != '')
        {
            $cust = $CI->db->query("SELECT
	partyCategoryID,categoryDescription,companyCode
FROM
	srp_erp_partycategories
WHERE partyCategoryID = ($partyCategoryID)")->row_array();
        }
        $data_arr = array('' => 'Select Category');

        if (!empty($cust))
        {
            $data_arr[trim($cust['partyCategoryID'] ?? '')] = trim($cust['companyCode'] ?? '') . ' | ' . trim($cust['categoryDescription'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['partyCategoryID'] ?? '')] = trim($row['companyCode'] ?? '') . ' | ' . trim($row['categoryDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_company_group_sub_item_categories'))
{
    function dropdown_company_group_sub_item_categories($companyID, $itemCategoryID, $groupItemCategoryID)
    {
        $CI = &get_instance();
        $segment = array();


        if ($companyID != '')
        {
            $masterIDSC = $CI->db->query("SELECT
	masterID
FROM
	srp_erp_groupitemcategory
WHERE
itemCategoryID=$groupItemCategoryID")->row_array();
            $gid = $masterIDSC['masterID'];
            $segment = $CI->db->query("SELECT
	*
FROM
	srp_erp_itemcategory
WHERE
	companyID = $companyID
AND masterID IN (
	SELECT
		itemCategoryID
	FROM
		srp_erp_groupitemcategorydetails
	WHERE
		groupItemCategoryID = $gid
)")->result_array();
        }

        if ($itemCategoryID != '')
        {
            $cust = $CI->db->query("SELECT
	itemCategoryID,description,codePrefix
FROM
	srp_erp_itemcategory
WHERE itemCategoryID = ($itemCategoryID)")->row_array();
        }
        $data_arr = array('' => 'Select Category');

        if (!empty($cust))
        {
            $data_arr[trim($cust['itemCategoryID'] ?? '')] =  trim($cust['description'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_gl_account_desc_cus_group_company'))
{
    function fetch_gl_account_desc_cus_group_company($id, $companyid)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);
        $CI->db->where('companyID', $companyid);

        return $CI->db->get()->row_array();
    }
}


if (!function_exists('fetch_gl_account_desc_cus_group_company'))
{
    function fetch_gl_account_desc_cus_group_company($id, $companyid)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('GLAutoID', $id);
        $CI->db->where('companyID', $companyid);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_customerid_from_group'))
{
    function fetch_customerid_from_group($groupCustomerID, $companyID)
    {
        $CI = &get_instance();
        $groupcustomer = $CI->db->query("SELECT 
        customerMasterID
        from 
        srp_erp_groupcustomerdetails
        WHERE 
        companyID IN ($companyID)
        AND groupCustomerMasterID IN ($groupCustomerID)")->result_array();
        return $groupcustomer;
    }
}
if (!function_exists('fetch_supplierID_from_group'))
{
    function fetch_supplierID_from_group($groupSupplierID, $companyID)
    {
        $CI = &get_instance();
        $groupsuplier = $CI->db->query("SELECT 
        SupplierMasterID
        from 
        srp_erp_groupsupplierdetails
        WHERE 
        companyID IN ($companyID)
        AND groupSupplierMasterID IN ($groupSupplierID)")->result_array();
        return $groupsuplier;
    }
}
