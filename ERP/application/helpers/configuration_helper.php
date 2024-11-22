<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (php_sapi_name() === 'cli') {
    $_SERVER['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__);
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
}

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
$newurl = explode("/", $_SERVER['SCRIPT_NAME']);
$actual_link = '';
$actual_link_html = '';
define('UPLOAD_PATH', $_SERVER["DOCUMENT_ROOT"]);
define('UPLOAD_PATH_POS', $_SERVER["DOCUMENT_ROOT"] . '/gs_sme/');
define('UPLOAD_PATH_MFQ', $_SERVER["DOCUMENT_ROOT"] . '/uploads/mfq/');

define('SETTINGS_BAR', 'off'); // on, off
define("mPDFImage", $actual_link);
define("htmlImage", $actual_link_html);
define("favicon", 'favicon.png');
define("ssoReportColumnDetails", serialize(array(
    'EPF' => array('shortOrder', 1),
    'ETF' => array('shortOrderETF', 2),
    'ETF-H' => array('shortOrderETFH', 3),
)));
define('STATIC_LINK', "$protocol$_SERVER[HTTP_HOST]");
define('SEND_GRID_EMAIL_KEY', "BNGvONwyN9GKPq3TjwCY1d0jkRKSDP+2CRVXuwBzy/QD");

define('SEND_GRID_EMAIL_KEY_2', "BBHHqmn6pT0FSfPa4GaCyR2482+WvEntTZCZuj92Hfal");
define('SEND_GRID_USERNAME', "AKIAZX25VTEUSAFRMNUX");

define("wacDecimalPlaces", 9); //decimal places for wac calculation
if (!function_exists('head_page'))
{
    function head_page($sub_heading, $status, $closeFunc = NULL): string
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $Filter = $CI->lang->line('common_filter');
        $filter = '';
        if ($status)
        {
            $filter = '<a data-toggle="collapse" data-target="#filter-panel"><i class="fa fa-filter"></i> ' . $Filter . '<!--Filter--></a>';
        }
        $closeFuncString = '';
        if (!empty($closeFunc))
        {
            $closeFuncString = 'onclick="' . trim($closeFunc) . '"';
        }

        return '<div class="row">
                    <div class="col-md-12" id="sub-container">
                      <div class="box">
                        <div class="box-header with-border" id="box-header-with-border">
                          <h3 class="box-title" id="box-header-title">' . $sub_heading . '</h3>
                          <div class="box-tools pull-right">
                            ' . $filter . '<button id="" class="btn btn-box-tool page-minus" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button  id="" ' . $closeFuncString . ' class="btn btn-box-tool headerclose navdisabl" type="button" ><i class="fa fa-times"></i></button>
                          </div>
                        </div>
                        <div class="box-body">';
    }
}

if (!function_exists('head_page_employee'))
{

    function head_page_employee()
    {
        return '<div class="row">
                    <div class="col-md-12">
                      <div class="box">
                        <div class="box-body" style="border-top: none;margin-top: -2px;">';
    }
}

if (!function_exists('footer_page'))
{
    function footer_page($right_foot, $left_foot, $status)
    {
        if ($status)
        {
            return '</div><div class="box-footer"></div></div></div></div>';
        }
        else
        {
            return '</div></div></div></div>';
        }
    }
}

if (!function_exists('text_type'))
{
    function text_type($status)
    {
        $data = '<span class="text-center">';
        if ($status == 1)
        {
            $data .= '<b>Sales Tax</b>';
        }
        else if ($status == 2)
        {
            $data .= '<b>Purchase Tax</b>';
        }
        else
        {
            $data .= '-';
        }
        $data .= '</span>';

        return $data;
    }
}

if (!function_exists('table_class'))
{
    function table_class()
    {
        return 'table table-bordered table-striped table-condensed table-row-select';
    }
}

if (!function_exists('item_more_info'))
{
    function item_more_info($itemID, $currentStock, $SellingPrice, $Currency, $decimal, $revanue, $cost, $asste, $WacAmount, $mainCategory)
    {
        //<b>Revanue GL Code : </b> '.$revanue.' &nbsp;&nbsp;<b>Cost GL Code : </b> '.$cost.' &nbsp;&nbsp;<b>Asste GL Code : </b> '.$asste.'<br>
        $data = '<br><span class="pull-right">';
        if ($mainCategory == 'Inventory')
        {
            $data .= '<b>Wac Amount : </b>' . number_format($WacAmount, $decimal) . ' &nbsp;&nbsp;&nbsp;';
        }
        $data .= '<b>Current Stock : </b>' . $currentStock . '&nbsp;&nbsp;&nbsp;';
        $data .= '<b>Selling Price : </b>' . $Currency . ' : ' . number_format($SellingPrice, $decimal);
        $data .= '</span>';

        return $data;
    }
}


if (!function_exists('row_class'))
{
    function row_class()
    {

        $CI = &get_instance();
        $CI->load->model('erp_rowclass');
        $CI->Srp_checkMail->row_class();
    }
}

if (!function_exists('set_next_db_process'))
{
    function set_next_db_process($next)
    {
        $CI = &get_instance();
        $CI->session->set_tempdata('next_db_process', $next, 500);

        return true;
    }
}

if (!function_exists('get_next_db_process'))
{
    function get_next_db_process()
    {
        $CI = &get_instance();
        $data = $CI->session->tempdata('next_db_process');
        return (empty($data)) ? '' : '[ ' . $data . ' ]';
    }
}

if (!function_exists('fetch_coa_type'))
{
    function fetch_coa_type($coa_type)
    {
        $data = '<span class="text-center">';
        if ($coa_type == 'PLI')
        {
            $data .= '<b>Income</b>';
        }
        elseif ($coa_type == 'PLE')
        {
            $data .= '<b>Expense</b>';
        }
        elseif ($coa_type == 'BSA')
        {
            $data .= '<b>Asset</b>';
        }
        elseif ($coa_type == 'BSL')
        {
            $data .= '<b>Liability</b>';
        }
        else
        {
            $data .= '<b>Equity</b>';
        }
        $data .= '</span>';

        return $data;
    }
}


if (!function_exists('format_date'))
{
    function format_date($date, $format = 'Y-m-d')
    {
        if (!is_null($date))
        {
            return date($format, strtotime($date));
        }
        else
        {
            return '';
        }
    }
}

if (!function_exists('current_date'))
{
    function current_date($time = TRUE)
    {
        if ($time)
        {
            return date('Y-m-d H:i:s');
        }
        else
        {
            return date('Y-m-d');
        }
    }
}

if (!function_exists('format_number'))
{
    function format_number($amount = 0, $decimal_place = 2)
    {
        if (is_null($amount))
        {
            $amount = 0;
        }
        if (is_null($decimal_place))
        {
            $decimal_place = 2;
        }

        return number_format($amount, $decimal_place);
    }
}

if (!function_exists('required_mark'))
{
    function required_mark()
    {
        $tmp = '<span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span>';
        echo $tmp;
    }
}

if (!function_exists('string_upper'))
{
    function string_upper($string)
    {
        return $string;
    }
}

if (!function_exists('trim_desc'))
{
    function trim_desc($string)
    {
        $string = preg_replace("/[\n\r]/", "", str_replace(array("'", '"'), array(" ", " "), $string ?? ''));

        return trim($string);
    }
}

if (!function_exists('current_companyID'))
{
    function current_companyID()
    {
        $CI = &get_instance();
        $companyID = isset($CI->common_data['company_data']['company_id']) ? $CI->common_data['company_data']['company_id'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('current_company_default_currencyID'))
{
    function current_company_default_currencyID()
    {
        $CI = &get_instance();
        $companyID = isset($CI->common_data['company_data']['company_default_currencyID']) ? $CI->common_data['company_data']['company_default_currencyID'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('current_company_reporting_currencyID'))
{
    function current_company_reporting_currencyID()
    {
        $CI = &get_instance();
        $companyID = isset($CI->common_data['company_data']['company_reporting_currency']) ? $CI->common_data['company_data']['company_reporting_currency'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('current_default_segment'))
{
    function current_default_segment()
    {
        $CI = &get_instance();
        $companyID = isset($CI->common_data['company_data']['default_segment']) ? $CI->common_data['company_data']['default_segment'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('current_companyCode'))
{
    function current_companyCode()
    {
        $CI = &get_instance();

        return trim($CI->common_data['company_data']['company_code']);
    }
}

if (!function_exists('current_userID'))
{
    function current_userID()
    {
        $CI = &get_instance();
        $userID = isset($CI->common_data['current_userID']) ? $CI->common_data['current_userID'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('current_userCode'))
{
    function current_userCode()
    {
        $CI = &get_instance();

        return trim($CI->common_data['current_userCode']);
    }
}

if (!function_exists('current_user'))
{
    function current_user()
    {
        $CI = &get_instance();
        if (!empty($CI->common_data['current_user']))
        {
            $user = trim($CI->common_data['current_user']);
        }
        else
        {
            $user = trim($CI->common_data['username']);
        }

        return $user;
    }
}

if (!function_exists('current_employee'))
{
    function current_employee()
    {
        $CI = &get_instance();

        return trim($CI->common_data['current_user']);
    }
}

if (!function_exists('current_user_group'))
{
    function current_user_group()
    {
        $CI = &get_instance();

        return trim($CI->common_data['user_group']);
    }
}

if (!function_exists('current_pc'))
{
    function current_pc()
    {
        $CI = &get_instance();

        return trim($CI->common_data['current_pc']);
    }
}

if (!function_exists('companyLogo'))
{
    function companyLogo()
    {
        $CI = &get_instance();
        $companyLogo = isset($CI->common_data['company_data']['company_logo']) ? $CI->common_data['company_data']['company_logo'] : 'no-logo.png';

        return trim($companyLogo);
    }
}

if (!function_exists('imagePath'))
{
    function imagePath()
    {
        $CI = &get_instance();

        return trim($CI->common_data['imagePath']);
    }
}


if (!function_exists('getCompanyImagePath'))
{
    function getCompanyImagePath()
    {
        $CI = &get_instance();
        $CI->db->select('imagePath,isLocalPath')
            ->from('srp_erp_pay_imagepath');
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

function checkIsFileExists($image_url)
{
    $ret = FALSE;
    if (file_exists(UPLOAD_PATH . '' . $image_url))
    {
        $ret = TRUE;
    }


    return ($ret == TRUE) ? $image_url : base_url('images/default.gif');
}

function checkIsFileExists_old($image_url)
{
    $ret = FALSE;
    $comImgPath_arr = getCompanyImagePath();


    if ($comImgPath_arr['isLocalPath'] == 1)
    {  //If file path is local
        if (file_exists(UPLOAD_PATH . $image_url))
        {
            $ret = TRUE;
        }
    }
    else
    { //If file path is not local
        $curl = curl_init($image_url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, TRUE);

        //do request
        $result = curl_exec($curl);

        //if request did not fail
        if ($result !== FALSE)
        {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200)
            {
                $ret = TRUE;
            }
        }

        curl_close($curl);
    }

    return ($ret == TRUE) ? $image_url : base_url('images/default.gif');
}

if (!function_exists('trim_value'))
{
    function trim_value($comments = '', $trimVal = 150)
    {
        $String = $comments;
        $truncated = (strlen($String) > $trimVal) ? substr(
            $String,
            0,
            $trimVal
        ) . '<span class="tol" rel="tooltip" style="color:#0088cc" title="' . str_replace(
            '"',
            '&quot;',
            $String
        ) . '">... more </span>' : $String;

        return $truncated;
    }
}

//load edit for item master
if (!function_exists('opensubcat'))
{
    function opensubcat($itemCategoryID, $description)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/item/sub_category_add","' . $itemCategoryID . '","' . $description . '","Sub Category",""); \'><button type="button" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus" style="color:green;"></span></button></a>';
        $status .= '</span>';

        return $status;
    }
}

//Generate documentcode
if (!function_exists('generate_seq_number'))
{
    function generate_seq_number($code = NULL, $count = NULL, $number = NULL, $schoolID = NULL)
    {
        return ($code . str_pad($number, $count, '0', STR_PAD_LEFT));
    }
}

if (!function_exists('all_account_category_drop'))
{
    function all_account_category_drop($status = TRUE, $filter = FALSE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("accountCategoryTypeID,Type,CategoryTypeDescription,subType");
        $CI->db->FROM('srp_erp_accountcategorytypes');
        if ($filter)
        {
            $CI->db->where('CategoryTypeDescription<>', 'Bank');
        }
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Category Types');
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['accountCategoryTypeID'] ?? '')] = trim($row['Type'] ?? '') . ' | ' . trim($row['subType'] ?? '') . ' | ' . trim($row['CategoryTypeDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_tax_drop'))
{
    function all_tax_drop($id = 2, $status = 1)
    {
        $CI = &get_instance();
        $CI->db->SELECT("taxMasterAutoID,taxDescription,taxShortCode,taxPercentage");
        $CI->db->FROM('srp_erp_taxmaster');
        $CI->db->where('taxType', $id);
        $CI->db->where('isActive', 1);
        $CI->db->where('isApplicableforTotal', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Tax Types');
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['taxMasterAutoID'] ?? '')] = trim($row['taxShortCode'] ?? '') . ' | ' . trim($row['taxDescription'] ?? '') . ' | ' . trim($row['taxPercentage'] ?? '') . ' %';
                }
            }
        }
        else
        {
            $data_arr = $data;
        }

        return $data_arr;
    }
}

if (!function_exists('supplier_gl_drop'))
{
    function supplier_gl_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->where_in('subCategory', array("BSL", "BSA"));
        $CI->db->WHERE('controllAccountYN', 1);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status == TRUE)
        {
            $data_arr = array('' => 'Select Supplier GL Account');
        }
        else
        {
            $data_arr = array('' => 'Select Customer GL Account');
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('supplier_group_gl_drop'))
{
    function supplier_group_gl_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_groupchartofaccounts');
        $CI->db->where_in('subCategory', array("BSL", "BSA"));
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Supplier GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_revenue_gl_drop'))
{
    function all_revenue_gl_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "PLI");
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_all_revenue_gl'))
{
    function dropdown_all_revenue_gl($code = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        if ($code)
        {
            $code = " AND subCategory != '$code' ";
        }
        else
        {
            $code = "";
        }
        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
$code
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX' or cmp.controlaccounttype='RRVR' or cmp.controlaccounttype='PRVR'))")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_liability_gl'))
{
    function dropdown_liability_gl($code = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        if ($code)
        {
            $code = " AND subCategory != '$code' ";
        }
        else
        {
            $code = "";
        }
        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID IN (8,9)
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
$code
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID IN (8,9)
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX' or cmp.controlaccounttype='RRVR' or cmp.controlaccounttype='PRVR'))")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_expense_gl'))
{
    function dropdown_expense_gl($code = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        if ($code)
        {
            $code = " AND subCategory != '$code' ";
        }
        else
        {
            $code = "";
        }
        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID IN (13,15)
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
$code
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID IN (13,15)
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX' or cmp.controlaccounttype='RRVR' or cmp.controlaccounttype='PRVR'))")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('all_cost_gl_drop'))
{
    function all_cost_gl_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "PLE");
        //$CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Cost GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_asset_gl_drop'))
{
    function all_asset_gl_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('subCategory', "BSA");
        //$CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Asset Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('master_account_drop'))
{
    function master_coa_account()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Master Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_gl_account_desc'))
{
    function fetch_gl_account_desc($id, $comid = NULL)
    {
        $CI = &get_instance();
        $companyType = $CI->session->userdata("companyType");

        if (!empty($comid) && $companyType == 2)
        {
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_chartofaccounts');
            $CI->db->WHERE('GLAutoID', $id);

            $CI->db->where('companyID', $comid);
        }
        else
        {
            $CI->db->SELECT("*");
            $CI->db->FROM('srp_erp_chartofaccounts');
            $CI->db->WHERE('GLAutoID', $id);

            $CI->db->where('companyID', current_companyID());
        }


        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_gl_account_from_systemAccountCode'))
{
    function fetch_gl_account_from_systemAccountCode($code, $company_id)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('systemAccountCode', $code);
        $CI->db->where('companyID', $company_id);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('get_ecommerce_setting_by_company'))
{
    function get_ecommerce_setting_by_company($company_id)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_ecommerce_settings');
        $CI->db->WHERE('company_id', $company_id);
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('authority_signature_bank_account_drop'))
{
    function authority_signature_bank_account_drop($status = TRUE)
    {
        $CI = &get_instance();
        $bank_arr = [];
        $CI->db->select("GLAutoID,,masterCategory,CategoryTypeDescription,subCategory,bankName,GLDescription");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isCash', 0);
        $CI->db->where('isCash', 0);
        $CI->db->where('accountCategoryTypeID', 1);
        $bank = $CI->db->get()->result_array();

        /*if ($status == 1) {
            return $bank;
        }*/

        if ($status)
        {
            $bank_arr = array('' => 'Select Bank Account');
        }

        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                //$type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['masterCategory'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . ' | ' . trim($row['CategoryTypeDescription'] ?? '') . ' | ' . trim($row['bankName'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }
        }

        return $bank_arr;
    }
}



if (!function_exists('company_bank_account_drop'))
{
    function company_bank_account_drop($status = 0, $isCash = 0, $select = 0)
    {
        $bank_arr = [];
        $CI = &get_instance();
        $CI->db->select("GLAutoID,bankName,bankBranch,bankSwiftCode,bankAccountNumber,subCategory,isCash");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        if ($isCash == 1)
        {
            $CI->db->where('isCash', 0);
        }
        $CI->db->where('companyID', current_companyID());
        $bank = $CI->db->get()->result_array();
        if ($status == 1)
        {
            return $bank;
        }

        if ($select == 0)
        {
            $bank_arr = array('' => 'Select Bank Account');
        }

        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['bankSwiftCode'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('fetch_all_gl_codes'))
{
    function fetch_all_gl_codes($code = NULL, $category = NULL, $retension = null)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory,accountCategoryTypeID");
        $CI->db->from('srp_erp_chartofaccounts');

        if ($code)
        {
            $CI->db->where('subCategory', $code);
        }

        if ($category)
        {
            $CI->db->where('subCategory !=', $category);
        }

        //$CI->db->where('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->WHERE('accountCategoryTypeID !=', 4);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

// if (!function_exists('load_expense_gl_drop')) {
//     function load_expense_gl_drop()
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('subCategory', "PLE");
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         $data = $CI->db->get()->result_array();
//         $data_arr = array('' => 'Select Expense GL Account');
//         if (isset($data)) {
//             foreach ($data as $row) {
//                 $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' .trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? ''). ' | ' . trim($row['subCategory'] ?? '');
//             }
//         }
//         return $data_arr;
//     }
// }

// if (!function_exists('coa_control_account')) {
//     function coa_control_account()
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("GLAutoID,GLSecondaryCode,GLDescription,systemAccountCode");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('controllAccountYN', 1);
//         $CI->db->where('isBank', 0);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         $data = $CI->db->get()->result_array();
//         $data_arr = array('' => 'Select Control Account');
//         if (isset($data)) {
//             foreach ($data as $row) {
//                 $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
//             }
//         }
//         return $data_arr;
//     }
// }

// if (!function_exists('companyBankAccounts_drop')) {
//     function companyBankAccounts_drop(){
//         $companyID = $CI->common_data['company_data']['company_id'];
//         $CI =& get_instance();
//         $CI->db->select("GLAutoID, bankName, bankAccountNumber");
//         $CI->db->from('srp_erp_chartofaccounts');
//         $CI->db->where('isBank', 1);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('isActive', 1);
//         $CI->db->where('bankName IS NOT NULL');
//         $CI->db->where('companyID', $companyID);
//         $CI->db->order_by('bankName');

//         $comBank = $CI->db->get()->result_array();
//         $comBank_arr = array('' => 'Select Bank');
//         if (isset($comBank)) {
//             foreach ($comBank as $row) {
//                 $comBank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '');
//             }
//         }
//         return $comBank_arr;
//     }
// }

// if (!function_exists('fetch_coa_desc')) {
//     function fetch_coa_desc($code)
//     {
//         $CI =& get_instance();
//         $CI->db->SELECT("*");
//         $CI->db->FROM('srp_erp_chartofaccounts');
//         $CI->db->WHERE('GLAutoID', $code);
//         $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
//         return $CI->db->get()->row_array();
//     }
// }


if (!function_exists('checkConfirmedPA'))
{
    function checkConfirmedPA($id)
    {
        $CI = &get_instance();
        $CI->db->select("confirmedYN");
        $CI->db->from('srp_erp_payment_application');
        $CI->db->WHERE('contractAutoID', $id);
        $CI->db->WHERE('confirmedYN', 0);

        $result = $CI->db->get()->row_array();

        if (!empty($result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

if (!function_exists('checkAllDocumentQtyIssued'))
{
    function checkAllDocumentQtyIssued($id, $autoID)
    {
        $CI = &get_instance();
        $contractAutoID = $CI->db->query("SELECT contractAutoID FROM srp_erp_payment_application_details WHERE contractAutoID = $id")->row('contractAutoID');

        if ($contractAutoID != NULL)
        {
            $data = $CI->db->query("SELECT IF(cuQty = PAcuQty  , true, false) as val FROM srp_erp_payment_application_details
            WHERE contractAutoID = $id AND PAAutoID = $autoID")->result_array();

            //var_dump($data);
            if (isset($data))
            {

                if (array_search('0', array_column($data, 'val')) !== FALSE)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return true;
        }
    }
}

if (!function_exists('category_type'))
{
    function category_type()
    {
        $CI = &get_instance();
        $CI->db->SELECT("categoryTypeID,categoryType,comment");
        $CI->db->FROM('srp_erp_itemcategorytype');
        //$CI->db->WHERE('masterAccountYN', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Category Type');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['categoryTypeID'] ?? '')] = trim($row['categoryType'] ?? '') . ' | ' . trim($row['comment'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_currency_desimal'))
{
    function fetch_currency_desimal($code)
    {
        $CI = &get_instance();
        $CI->db->SELECT("DecimalPlaces");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('DecimalPlaces');
    }
}

if (!function_exists('fetch_currency_desimal_by_id'))
{
    function fetch_currency_desimal_by_id($currencyID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("DecimalPlaces");
        $CI->db->FROM('srp_erp_companycurrencyassign');
        $CI->db->WHERE('currencyID', $currencyID);

        return $CI->db->get()->row('DecimalPlaces');
    }
}

if (!function_exists('fetch_item_data'))
{
    function fetch_item_data($itemAutoID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->WHERE('itemAutoID', $itemAutoID);
        $CI->db->where('companyID', current_companyID());

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_ware_house_item_data'))
{
    function fetch_ware_house_item_data($itemAutoID)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $wareHouseID = $CI->common_data['ware_houseID'];

        $CI->db->select("*");
        $CI->db->select("(SELECT currentStock FROM srp_erp_warehouseitems WHERE itemAutoID=t1.itemAutoID AND wareHouseAutoID={$wareHouseID}) AS wareHouseQty ");
        $CI->db->from('srp_erp_itemmaster t1');
        $CI->db->where('t1.itemAutoID', $itemAutoID);
        $CI->db->where('t1.companyID', $companyID);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_warehouse_items_data'))
{
    function fetch_warehouse_items_data($itemAutoIDsArray, $wareHouseAutoID = null)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $wareHouseID = $CI->common_data['ware_houseID'];

        if ($wareHouseAutoID)
        {
            $wareHouseID = $wareHouseAutoID;
        }

        $qry = array();
        foreach ($itemAutoIDsArray as $itemAutoID)
        {
            $qry[] = "SELECT
                    *,
                    ( SELECT currentStock FROM srp_erp_warehouseitems WHERE itemAutoID = t1.itemAutoID AND wareHouseAutoID = $wareHouseID ) AS wareHouseQty 
                FROM
                    `srp_erp_itemmaster` `t1` 
                WHERE
                    `t1`.`itemAutoID` = $itemAutoID 
                    AND `t1`.`companyID` = $companyID";
        }

        if (count($qry) > 0)
        {
            $unionQuery = implode(' UNION ALL ', $qry);
            $dbRes = $CI->db->query($unionQuery);
            return $dbRes->result_array();
        }
    }
}

if (!function_exists('fetch_currency_dec'))
{
    function fetch_currency_dec($code)
    {
        $CI = &get_instance();
        $CI->db->SELECT("CurrencyName");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('CurrencyName');
    }
}

//units drop down
if (!function_exists('load_unit_drop'))
{
    function load_unit_drop()
    {

        $CI = &get_instance();
        $CI->db->SELECT("UnitID,UnitDes,UnitShortCode");
        $CI->db->FROM('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $units = $CI->db->get()->result_array();

        return $units;
    }
}

if (!function_exists('load_location_drop'))
{
    function load_location_drop()
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->SELECT("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID");
        $CI->db->FROM('srp_erp_warehousemaster');
        $CI->db->WHERE('companyID', $companyID);
        $location = $CI->db->get()->result_array();

        return $location;
    }
}

if (!function_exists('load_warehouse_items'))
{
    function load_warehouse_items()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCodeSystem,itemDescriptionshort");
        $CI->db->FROM('srp_erp_itemmaster');
        $itemwarehouse = $CI->db->get()->result_array();

        return $itemwarehouse;
    }
}

//currency drop down
if (!function_exists('load_currency_drop'))
{
    function load_currency_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("CurrencyID,CurrencyName,CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $currncy = $CI->db->get()->result_array();

        return $currncy;
    }
}

//country drop down
if (!function_exists('load_country_drop'))
{
    function load_country_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}


if (!function_exists('load_all_country_drop'))
{
    function load_all_country_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Country');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['countryID'] ?? '')] = trim($row['countryShortCode'] ?? '') . ' | ' . trim($row['CountryDes'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_all_countryName_drop'))
{
    function load_all_countryName_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("countryID,CountryDes,countryShortCode");
        $CI->db->FROM('srp_erp_countrymaster');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Country');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['CountryDes'] ?? '')] = trim($row['CountryDes'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_drop'))
{
    function all_main_category_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_currency_drop'))
{
    function all_currency_drop($status = TRUE, $keyType = NULL)/*Load all currency*/
    {
        $CI = &get_instance();
        $CI->db->select("currencyID, CurrencyCode,CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        //$CI->db->where('isDefault', 0);
        $currency = $CI->db->get()->result_array();
        if ($status == TRUE)
        {
            $currency_arr = array('' => 'Select Currency');
        }
        if (isset($currency))
        {
            $masterVal = ($keyType == 'ID') ? 'currencyID' : 'CurrencyCode';
            foreach ($currency as $row)
            {
                $currency_arr[trim($row[$masterVal])] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }

        return $currency_arr;
    }
}

if (!function_exists('all_currency_new_drop'))
{
    function all_currency_new_drop($status = TRUE)/*Load all currency*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $currency = $CI->db->get()->result_array();
        if ($status)
        {
            $currency_arr = array('' => $CI->lang->line('common_select_currency')/*'Select Currency'*/);
        }
        else
        {
            $currency_arr = [];
        }
        if (isset($currency))
        {
            foreach ($currency as $row)
            {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }

        return $currency_arr;
    }
}

if (!function_exists('company_group_currency'))
{
    function company_group_currency($status = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $reporting_curr = $CI->common_data['company_data']['company_reporting_currencyID'];

        $CI->db->select("currencyID, CurrencyCode, CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('currencyID', $reporting_curr);
        $currency = $CI->db->get()->result_array();


        if ($status)
        {
            $group_currency_arr = array('' => $CI->lang->line('common_select_currency'));
        }
        else
        {
            $group_currency_arr = [];
        }
        if (isset($currency))
        {
            foreach ($currency as $row)
            {
                $group_currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }

        return $group_currency_arr;
    }
}


if (!function_exists('all_currency_master_drop'))
{
    function all_currency_master_drop($status = TRUE)/*Load all currency*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $currency = $CI->db->get()->result_array();
        if ($status)
        {
            $currency_arr = array('' => $CI->lang->line('common_select_currency')/*'Select Currency'*/);
        }
        else
        {
            $currency_arr = [];
        }
        if (isset($currency))
        {
            foreach ($currency as $row)
            {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }

        return $currency_arr;
    }
}


if (!function_exists('all_taxpayee_drop'))
{
    function all_taxpayee_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI = &get_instance();
        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => 'Select Tax Payee');
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('all_supplier_drop'))
{
    function all_supplier_drop($status = TRUE, $IsActive = null)/*Load all Supplier*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('masterApprovedYN', "1");
        if ($IsActive == 1)
        {
            $CI->db->where('isActive', 1);
        }

        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => $CI->lang->line('common_aelect_supplier')/*'Select Supplier'*/);
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['supplierAutoID'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('all_supplier_drop_systemCode'))
{
    function all_supplier_drop_systemCode($status = TRUE, $IsActive = null)/*Load all Supplier*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("supplierAutoID,supplierName,supplierSystemCode,supplierCountry,secondaryCode");
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('masterApprovedYN', "1");
        if ($IsActive == 1)
        {
            $CI->db->where('isActive', 1);
        }

        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => $CI->lang->line('common_aelect_supplier')/*'Select Supplier'*/);
        }
        else
        {
            $supplier_arr = [];
        }

        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['secondaryCode'] ?? '')] = (trim($row['supplierSystemCode'] ?? '') ? trim($row['supplierSystemCode'] ?? '') . ' | ' : '') . trim($row['supplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('fetch_supplier_data'))
{
    function fetch_supplier_data($supplierID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('supplierAutoID', $supplierID);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('fetch_supplier_data_by_supplier_systemcode'))
{
    function fetch_supplier_data_by_supplier_systemcode($supplierSystemCode)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('supplierSystemCode', $supplierSystemCode);

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('all_customer_drop_gpos'))
{
    function all_customer_drop_gpos($status = TRUE, $iscash = false, $IsActive = null)/*Load all Customer*/
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_customermaster.customerAutoID,srp_erp_customermaster.customerName,srp_erp_customermaster.customerSystemCode,srp_erp_customermaster.customerCountry,srp_erp_customermaster.companyCode,srp_erp_customermaster.customerTelephone");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->join('srp_erp_pos_invoice', 'srp_erp_pos_invoice.customerID = srp_erp_customermaster.customerAutoID', 'inner');
        $CI->db->where('srp_erp_customermaster.companyID', $CI->common_data['company_data']['company_id']);
        if ($IsActive == 1)
        {
            $CI->db->where('isActive', 1);
        }
        $customer = $CI->db->get()->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Customer');
        }
        else
        {
            $customer_arr = [];
        }
        if ($iscash)
        {
            $customer_arr = array(0 => 'Cash');
        }
        else
        {
            $customer_arr = [];
        }

        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                //$customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
                $customer_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerName'] ?? '') . ' | ' . (trim($row['customerTelephone'] ?? ''));
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('all_srp_erp_sales_person_drop'))
{
    function all_srp_erp_sales_person_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("salesPersonID,SalesPersonName,SalesPersonCode,wareHouseLocation");
        $CI->db->from('srp_erp_salespersonmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $sales_person = $CI->db->get()->result_array();
        if ($status)
        {
            $sales_person_arr = array('' => 'Select Sales person');
            if (isset($sales_person))
            {
                foreach ($sales_person as $row)
                {
                    $sales_person_arr[trim($row['salesPersonID'] ?? '')] = (trim($row['SalesPersonCode'] ?? '') ? trim($row['SalesPersonCode'] ?? '') . ' | ' : '') . trim($row['SalesPersonName'] ?? '') . (str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) ? ' | ' . str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) : '');
                }
            }
        }
        else
        {
            $sales_person_arr = [];
        }

        return $sales_person_arr;
    }
}

if (!function_exists('all_umo_drop'))
{
    function all_umo_drop()
    {
        $CI = &get_instance();
        $CI->db->select('UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['UnitShortCode'] ?? '')] = trim($row['UnitShortCode'] ?? '') . ' - ' . trim($row['UnitDes'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_umo_new_drop'))
{
    function all_umo_new_drop()
    {
        $CI = &get_instance();
        $CI->db->select('UnitID,UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_unit_of_measure');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['UnitID'] ?? '')] = trim($row['UnitShortCode'] ?? '') . ' | ' . trim($row['UnitDes'] ?? '');
            }
        }

        return $data_arr;
    }
}




if (!function_exists('all_fuel_type_drop'))
{
    function all_fuel_type_drop()
    {
        $CI = &get_instance();
        $CI->db->select('fuelTypeID,description,fuelRate');
        $CI->db->from('fleet_fuel_type');
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Fuel');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['fuelTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}




if (!function_exists('all_delivery_location_drop'))
{
    function all_delivery_location_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Warehouse Location');
        }
        else
        {
            $data_arr = [];
        }


        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] ?? '') . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) . ' | ' . preg_replace('/\s\s+/', ' ', str_replace("'", "&apos;", trim($row['wareHouseDescription'] ?? '')));
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_group_warehouse_drop'))
{
    function all_group_warehouse_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $CI->db->from('srp_erp_groupwarehousemaster');
        $CI->db->where('groupID', current_companyID());
        $CI->db->group_by('wareHouseAutoID');
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Warehouse Location');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] ?? '') . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseDescription'] ?? ''));
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_addresstype_drop'))
{
    function load_addresstype_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("addressTypeID,addressTypeDescription");
        $CI->db->FROM('srp_erp_addresstype');
        $address = $CI->db->get()->result_array();
        return $address;
    }
}


if (!function_exists('load_employee_drop'))
{
    function load_employee_drop($status = false)
    {
        //Query Modified for group based company Rifkan Razak
        $CI = &get_instance();
        $companyID = current_companyID();
        $groupCompanyID = $CI->db->query(
            "SELECT companyGroupID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyID = $companyID"
        )->row('companyGroupID');

        if (!empty($groupCompanyID))
        {
            $companyList = $CI->db->query(
                "SELECT companyID 
                     FROM srp_erp_companygroupdetails 
                     WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
            )->result_array();
        }

        $CI->db->SELECT("EIdNo,Ename2,EmpSecondaryCode");
        if (!empty($groupCompanyID))
        {
            $companyArray = [];
            if (count($companyList) > 0)
            {
                foreach ($companyList as $val)
                {
                    $companyArray[] = $val['companyID'];
                }
            }
            $CI->db->FROM('srp_employeesdetails,srp_erp_companygroupdetails AS cmpTB');
            $CI->db->where_in('cmpTB.companyID', $companyArray);
        }
        else
        {
            $CI->db->FROM('srp_employeesdetails');
            $CI->db->WHERE('Erp_companyID', $companyID);
        }
        $CI->db->WHERE('empConfirmedYN', 1);
        $CI->db->WHERE('isDischarged', 0);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Employee'); //array('' => '');

        if ($status == true)
        {
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primaryLanguage);
            $data_arr[''] = $CI->lang->line('common_select_employee');
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[$row['EIdNo']] = $row['Ename2'] . ' - ' . $row['EmpSecondaryCode'];
            }
        }

        return $data_arr;
    }
}

if (!function_exists('group_level_load_employee_drop'))
{
    function group_level_load_employee_drop($status = false)
    {


        $CI = &get_instance();
        $groupCompanyID = current_companyID();

        if (!empty($groupCompanyID))
        {
            $companyList = $CI->db->query(
                "SELECT companyID 
                     FROM srp_erp_companygroupdetails 
                     WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
            )->result_array();
        }

        $CI->db->SELECT("EIdNo,Ename2,EmpSecondaryCode");
        if (!empty($groupCompanyID))
        {
            $companyArray = [];
            if (count($companyList) > 0)
            {
                foreach ($companyList as $val)
                {
                    $companyArray[] = $val['companyID'];
                }
            }
            $CI->db->FROM('srp_employeesdetails,srp_erp_companygroupdetails AS cmpTB');
            $CI->db->where_in('cmpTB.companyID', $companyArray);
        }
        else
        {
            $CI->db->FROM('srp_employeesdetails');
            $CI->db->WHERE('Erp_companyID', $groupCompanyID);
        }
        $CI->db->WHERE('empConfirmedYN', 1);
        $CI->db->WHERE('isDischarged', 0);
        $data = $CI->db->get()->result_array();

        $data_arr = array(); //array('' => '');

        if ($status == true)
        {
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primaryLanguage);
            // $data_arr[''] = $CI->lang->line('common_select_employee');

        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '') . ' - ' . trim($row['EmpSecondaryCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('calculation_addon'))
{
    function calculation_addon($grv_full_total, $addon_total_amount, $item_total, $qty)
    {
        $data = array();
        $item_full_total = ($item_total * $qty);
        $data['per'] = $grv_full_total > 0 ? (($item_full_total / $grv_full_total) * 100) : 0;
        $data['full'] = (($data['per'] / 100) * $addon_total_amount);
        $data['unit'] = $qty > 0 ? ($data['full'] / $qty) : 0;
        $data['item_total'] = ($item_full_total + $data['full']);

        return $data;
    }
}

if (!function_exists('get_employee_current_company'))
{
    function get_employee_current_company($dropdown = null)
    {
        $CI = &get_instance();
        $base_arr = [];

        $CI->db->select('*');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->WHERE('empConfirmedYN', 1);
        $CI->db->WHERE('isDischarged', 0);
        $dropDownData = $CI->db->get()->result_array();

        $base_arr[''] = $CI->lang->line('common_select_employee');
        if (isset($dropDownData))
        {
            foreach ($dropDownData as $row)
            {
                $base_arr[trim($row['EIdNo'] ?? '')] = trim($row['EmpSecondaryCode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $base_arr;
    }
}

if (!function_exists('generate_filename'))
{
    function generate_filename($documentID = NULL, $documentSystemCode = NULL, $extention = NULL)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        return $companyID . "_" . $documentID . "_" . $documentSystemCode . "_" . time() . $extention;
    }
}

if (!function_exists('document_uploads_url'))
{
    function document_uploads_url()
    {
        $url = base_url('uploads') . '/';

        return $url;
    }
}

if (!function_exists('confirm'))
{
    function confirm($con)
    {

        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($con == 1)
        {
            $status .= '<span class="label label-success">&nbsp;</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('isDefaultYN'))
{
    function isDefaultYN($con)
    {

        //$status = '<center>';
        if ($con == 0)
        {
            $status = "No";
        }
        else
        {
            $status = "Yes";
        }
        //$status .= '</center>';

        return $status;
    }
}

//new change according to approval change 
if (!function_exists('confirm_approval'))
{
    function confirm_approval($con)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($con == 1)
        {
            $status .= '<span class="label label-success">&nbsp;</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_type'))
{
    function mfq_rfq_type($con)
    {
        $status = '<center>';
        if ($con == 1)
        {
            $status .= '<span class="label label-danger">Tender</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-success">RFQ</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-danger">SPC</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_order_status'))
{
    function mfq_rfq_order_status($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">Open</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-success">In Progress</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-danger">Lost</span>';
        }
        elseif ($con == 4)
        {
            $status .= '<span class="label label-danger">Awarded</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_category'))
{
    function mfq_rfq_category($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">ss Tank</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_submission_status'))
{
    function mfq_rfq_submission_status($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">REF</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_job_status'))
{
    function mfq_rfq_job_status($close, $confirm, $approval)
    {
        $status = '<center>';

        if ($close == 1)
        {
            $status .= '<span class="label label-danger">Closed</span>';
        }
        elseif ($approval == 1)
        {
            $status .= '<span class="label label-success">approved</span>';
        }
        elseif ($confirm == 1)
        {
            $status .= '<span class="label label-info">confirmed</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_status'))
{
    function mfq_rfq_status($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">Tentative</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-success">Firm</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-success">Budget</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_micoda_operation'))
{
    function mfq_rfq_micoda_operation($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">UAE</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-success">INDIA</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_document_status'))
{
    function mfq_rfq_document_status($con)
    {
        $status = '<center>';

        if ($con == 1)
        {
            $status .= '<span class="label label-danger">Open</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-success">In Progress</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-success">Completed</span>';
        }

        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

// approvals drill down history 
if (!function_exists('document_approval_drilldown'))
{
    function document_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        elseif ($con == 1)
        {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('confirm_user_approval_drilldown'))
{
    function confirm_user_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($con == 1)
        {
            $status .= '<span class="label label-success">&nbsp;</span>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
            /*            $status .= '<a onclick="approval_refer_back_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-warning"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';*/
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('confirm_ap_user'))
{
    function confirm_ap_user($approved_status, $confirmed_status, $code, $autoID, $isFromLeave = null, $collectedStatus = null, $paymentType = null, $modeOfPayment = null)
    {
        $CI = &get_instance();
        $status = '<center>';
        if ($approved_status == 0)
        {
            if ($confirmed_status == 0 || $confirmed_status == 3)
            {
                $status .= '<span class="label label-danger">&nbsp;</span>';
            }
            else if ($confirmed_status == 2)
            {
                $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            }
            else
            {
                $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            }
        }
        elseif ($approved_status == 1)
        {
            if ($confirmed_status == 1)
            {
                $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a><br>';
                if ($code == 'PV')
                {
                    if ($paymentType == 1 || $modeOfPayment == 2)
                    {
                        if ($collectedStatus == 0)
                        {
                            $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-danger"  style="font-size: 9px; width: 10%; "  title="Not Collected" rel="tooltip">Not Collected</span>';
                        }
                        else if ($collectedStatus == 1)
                        {
                            $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Collected" rel="tooltip">Collected </span>';
                        }
                        else if ($collectedStatus == 2)
                        {
                            $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-warning" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="On Hold" rel="tooltip">On Hold </span>';
                        }
                    }
                }
                elseif ($code == 'QUT')
                {
                    if ($collectedStatus == 1)
                    {
                        $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-danger"  style="font-size: 9px; width: 10%; "  title="Draft" rel="tooltip">Draft</span>';
                    }
                    else if ($collectedStatus == 2)
                    {
                        $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-warning" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Send to Customer" rel="tooltip">Send to Customer</span>';
                    }
                    else if ($collectedStatus == 3)
                    {
                        $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Approved" rel="tooltip">Approved </span>';
                    }
                    else if ($collectedStatus == 4)
                    {
                        $status .= '<a onclick="generatepaymentcollection_drilldown(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-danger" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Rejected" rel="tooltip">Rejected </span>';
                    }
                }
                elseif ($code == 'PO')
                {
                    // if ($paymentType == 1 || $modeOfPayment == 2) {

                    $CI->db->select('*');
                    $CI->db->from('srp_erp_purchaseordermaster');
                    $CI->db->where('purchaseOrderID', $autoID);
                    $po_all = $CI->db->get()->row_array();

                    $CI->db->select('*');
                    $CI->db->from('srp_erp_suppliermaster');
                    $CI->db->where('supplierAutoID', $po_all['supplierID']);
                    $CI->db->where('isSrmGenerated', 1);
                    $supplier_srm_created = $CI->db->get()->row_array();

                    if ($supplier_srm_created)
                    {
                        if ($collectedStatus == 0 || $collectedStatus == 1)
                        {
                            $status .= '<a onclick="open_po_sending_model_max_portal(\'' . $code . '\',' . $autoID . ',' . $collectedStatus . ')" <span class="label label-danger"  style="font-size: 9px; width: 10%; "  title="MAX Portal submitted Status" rel="tooltip">Not Submitted</span>';
                        }
                        else if ($collectedStatus == 2)
                        {
                            $status .= '<a <span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="MAX Portal submitted Status" rel="tooltip">Submitted </span>';
                        }
                    }
                    //}
                }
            }
            else
            {
                $status .= '<span class="label label-success">&nbsp;</span>';
            }
        }
        elseif ($approved_status == 2)
        {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        }
        elseif ($approved_status == 5)
        {
            $fn = ($isFromLeave == 1) ? 'onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')"' : '';
            $cls = ($isFromLeave == 1) ? 'cancel-pop-up' : '';
            $string = ($isFromLeave == 1) ? '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' : '&nbsp;';

            if ($code == 'QUT' || $code == 'SO' || $code == 'CNT' || $code == 'PO' || $code == 'PRQ' || $code == 'MR')
            {
                $status .= '<a onclick="fetch_approval_closed_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-info">';
                $status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
            }
            else
            {
                $status .= '<span class="label label-info ' . $cls . '" ' . $fn . '>' . $string . '</span>';
            }
        }
        elseif ($approved_status == 6)
        {
            $fn = 'onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')"';
            $status .= '<span class="label label-info cancel-pop-up" ' . $fn . '><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_segment_action'))
{
    function load_segment_action($segmentID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'edit_segmrnt("' . $segmentID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('reversing_approval'))
{
    function reversing_approval($documentID, $documentApprovedID, $id)
    {
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $companyID = current_companyID();
        $para2 = '';
        if ($documentID == 'BRC')
        {
            $bankGlAutoID = $CI->db->query("SELECT bankGLAutoID from srp_erp_bankrecmaster WHERE companyID = $companyID AND bankRecAutoID  = $id")->row_array();
            $para2 = $bankGlAutoID['bankGLAutoID'];
            $status .= '<a onclick=\'reversing_approval_modal("' . $documentID . '","' . $documentApprovedID . '","' . $id . '","' . $para2 . '"); \'><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
            $status .= '';
        }
        elseif ($documentID == 'CINV')
        {
            $invoiceType = $CI->db->query("SELECT invoiceType from `srp_erp_customerinvoicemaster`  WHERE companyID = $companyID AND invoiceAutoID  = $id")->row('invoiceType');
            $para2 = ($invoiceType == 'Commission') ? 'Commission' : '';
            $status .= '<a onclick=\'reversing_approval_modal("' . $documentID . '","' . $documentApprovedID . '","' . $id . '","' . $para2 . '"); \'><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        else
        {
            $status .= '<a onclick=\'reversing_approval_modal("' . $documentID . '","' . $documentApprovedID . '","' . $id . '","' . $para2 . '"); \'><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('reversing_split_approval'))
{
    function reversing_split_approval($documentID, $split_id = null)
    {
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $companyID = current_companyID();
        if ($split_id)
        {
            $status .= '<div class="text-inline"><span class="text-success"><i class="fa fa-check"></i> Added</span> </div>';
        }
        $status .= '<a class="btn btn-danger" onclick=\'reversing_split_amount("' . $documentID . '"); \'><span title="Revise" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> &nbsp Split</a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_segment_status'))
{
    function load_segment_status($segmentID, $state)
    {
        if ($state == 1)
        {
            $status = '<span class="pull-right">';
            $status .= '<input type="checkbox" id="statusactivate_' . $segmentID . '" name="statusactivate" onchange="changesegmentsatus(' . $segmentID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
            $status .= '</span>';
        }
        else if ($state == 0)
        {
            $status = '<span class="pull-right">';
            $status .= '<input type="checkbox" id="statusactivate_' . $segmentID . '" name="statusactivate" onchange="changesegmentsatus(' . $segmentID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
            $status .= '</span>';
        }

        return $status;
    }
}

if (!function_exists('all_employee_drop_mfq_apply'))
{
    function all_employee_drop_mfq_apply($status = TRUE, $isDischarged = 0, $isEmployeeWithout = null)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        if ($isEmployeeWithout != 1)
        {
            $CI->db->where('isPayrollEmployee', 1);
        }

        if ($isDischarged == 1)
        {
            $CI->db->where('isDischarged !=1 ');
        }
        $customer = $CI->db->get()->result_array();
        if ($status == TRUE)
        {

            if (isset($customer))
            {
                foreach ($customer as $row)
                {
                    $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                }
            }
        }
        else
        {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}


if (!function_exists('all_employee_drop'))
{
    function all_employee_drop($status = TRUE, $isDischarged = 0, $isEmployeeWithout = null, $multiple = 1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        if ($isEmployeeWithout != 1)
        {
            $CI->db->where('isPayrollEmployee', 1);
        }

        if ($isDischarged == 1)
        {
            $CI->db->where('isDischarged !=1 ');
        }

        $customer = $CI->db->get()->result_array();
        if ($status == TRUE)
        {
            if ($multiple)
            {
                $customer_arr = array('' => $CI->lang->line('common_select_employee'));/*'Select Employee'*/
            }

            if (isset($customer))
            {
                foreach ($customer as $row)
                {
                    $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                }
            }
        }
        else
        {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}

if (!function_exists('get_authorized_user_group_to_document'))
{
    function get_authorized_user_group_to_document($segmentID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("userGroupID");
        $CI->db->from('srp_erp_segment_usergroups');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('segmentID', $segmentID);
        $user_group = $CI->db->get()->result_array();

        $str = '';

        foreach ($user_group as $group)
        {
            $str .= $group['userGroupID'];
        }

        return $str;
    }
}


/*
    used : erp_srp_segment_view 
*/
if (!function_exists('all_group_drop'))
{
    function all_group_drop($status = TRUE, $type = null, $bypass = null)
    {
        $CI = &get_instance();
        $CI->db->select("userGroupID,description");
        $CI->db->from('srp_erp_usergroups');
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $group = $CI->db->get()->result_array();
        if ($status == TRUE)
        {
            if (empty($type))
            {
                $group_arr = array('' => 'Select Group');
            }

            if (empty($bypass))
            {
                $group_arr['0'] = $CI->common_data['company_data']['company_name'] . ' company only';
            }

            if (isset($group))
            {
                foreach ($group as $row)
                {
                    $group_arr[trim($row['userGroupID'] ?? '')] = trim($row['description'] ?? '');
                }
            }
        }
        else
        {
            $group_arr = $group;
        }

        return $group_arr;
    }
}

if (!function_exists('load_po_version_drop_down'))
{
    function load_po_version_drop_down($poID)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_purchaseordermaster_version');
        $CI->db->where('purchaseOrderID', $poID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $group = $CI->db->get()->result_array();

        $group_arr = array('' => 'Select Version');
        if (isset($group))
        {
            foreach ($group as $row)
            {
                if ($row['versionNo'] == 0)
                {
                    $group_arr[trim($row['versionAutoID'] ?? '')] = trim($row['purchaseOrderCode'] ?? '');
                }
                else
                {
                    $group_arr[trim($row['versionAutoID'] ?? '')] = trim($row['purchaseOrderCode'] ?? '') . '(v' . trim($row['versionNo'] ?? '') . ')';
                }
            }
        }

        return $group_arr;
    }
}

if (!function_exists('update_segment_usergroups'))
{
    function update_segment_usergroups($segmentID, $user_groups)
    {
        $CI = &get_instance();

        $companyID = $CI->common_data['company_data']['company_id'];

        //clear all the added usergroups
        $res = $CI->db->where('segmentID', $segmentID)->where('companyID', $companyID)->delete('srp_erp_segment_usergroups');

        // add all back
        if (count($user_groups) > 0)
        {
            foreach ($user_groups as $group)
            {

                $data = array();

                $data['segmentID'] = $segmentID;
                $data['userGroupID'] = $group;
                $data['added_date'] = current_date(true);
                $data['companyID'] = current_companyID();
                $data['companyCode'] = current_companyCode();

                $res = $CI->db->insert('srp_erp_segment_usergroups', $data);
            }
        }


        return $user_groups;
    }
}

if (!function_exists('get_Expense_Claim_Category'))
{
    function get_Expense_Claim_Category($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("expenseClaimCategoriesAutoID,claimcategoriesDescription");
        $CI->db->from('srp_erp_expenseclaimcategories');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('type', 1);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Expense Claim Category');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['expenseClaimCategoriesAutoID'] ?? '')] =  trim($row['claimcategoriesDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}


if (!function_exists('all_document_code_drop'))
{
    function all_document_code_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("srp_erp_documentcodemaster.documentID,srp_erp_documentcodemaster.document");
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->join(
            'srp_erp_documentcodes',
            'srp_erp_documentcodes.documentID = srp_erp_documentcodemaster.documentID'
        );
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isApprovalDocument', 1);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Document Code');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['documentID'] ?? '')] = trim($row['documentID'] ?? '') . ' | ' . trim($row['document'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_financeyear_drop'))
{
    function all_financeyear_drop($policyFormat = FALSE)
    {
        $convertFormat = convert_date_format();
        $CI = &get_instance();
        $CI->db->select('companyFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_companyfinanceyear');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('isActive', 1);
        //$CI->db->where('isCurrent', 1);
        $CI->db->where('isClosed', 0);
        $CI->db->order_by("beginingDate", "desc");
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($policyFormat)
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = trim(format_date(
                        $row['beginingDate'],
                        $convertFormat
                    )) . ' - ' . trim(format_date($row['endingDate'], $convertFormat));
                }
                else
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = trim($row['beginingDate'] ?? '') . ' - ' . trim($row['endingDate'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_company_location_code_drop'))
{
    function all_company_location_code_drop()
    {
        $convertFormat = convert_date_format();
        $CI = &get_instance();
        $CI->db->select('locationCode,locationName,locationID');
        $CI->db->from('srp_erp_location');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isCostLocation', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Location');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['locationID'] ?? '')] = trim($row['locationName'] ?? '') . ' - ' . trim($row['locationCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('convertCatType'))
{
    function convertCatType($key)
    {
        if ($key == 'A')
        {
            return 'Addition';
        }
        else if ($key == 'D')
        {
            return 'Deduction';
        }
        else if ($key == 'DC')
        {
            return 'Deduction';
        }
        else
        {
            return '-';
        }
    }
}

if (!function_exists('convertPercentage'))
{
    function convertPercentage($per, $type)
    {
        if ($type == 'A')
        {
            return '-';
        }
        else if ($type == 'DC')
        {
            return '-';
        }
        else if ($type == 'D')
        {
            return $per . ' %';
        }
        else
        {
            return $per . '|' . $type;
        }
    }
}

if (!function_exists('onclickFunction'))
{
    function onclickFunction($id, $des, $type, $per, $gl, $CC_Percentage = 0, $CC_GLCode = 0, $payrollCatID = 0, $isPayrollCategory = 1, $is_basic = 0, $is_variable = 0, $linkType = 0, $calType = 0, $location = 0, $employeeClaimYN = null)

    {
        $values = $id . ', \'' . $des . '\', \'' . $type . '\', \'' . $per . '\', \'' . $gl . '\', \'' . $CC_Percentage . '\',\'' . $CC_GLCode . '\'';
        $values .= ',\'' . $payrollCatID . '\',\'' . $isPayrollCategory . '\', \'' . $is_basic . '\',\'' . $is_variable . '\',\''  . $linkType . '\',\'' . $calType . '\',\'' . $location . '\',' . $employeeClaimYN;

        $str = '<spsn class="pull-right"><a onclick="editCat( ' . $values . ' )"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>';
        $str .= ' &nbsp;&nbsp; | &nbsp;&nbsp <a onclick="delete_cat(' . $id . ', \'' . $des . '\', \'' . $type . '\')">';
        $str .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';

        return $str;
    }
}

if (!function_exists('all_banks_drop'))
{
    function all_banks_drop()
    {
        $CI = &get_instance();
        $CI->db->select('bankID, bankCode, bankName, bankSwiftCode');
        $CI->db->from('srp_erp_pay_bankmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result();

        return $data;
    }
}

if (!function_exists('currency_conversion'))
{
    function currency_conversion($trans_currency, $againce_currency, $amount = 0)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [ what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currency
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currency
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI = &get_instance();
        if ($trans_currency == $againce_currency)
        {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('CurrencyCode', $trans_currency);
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['currencyID'];

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'];
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'];
            $data['CurrencyName'] = $data_arr['CurrencyName'];
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'];
            $data['convertedAmount'] = $amount * 1;
        }
        else
        {
            $CI->db->select('srp_erp_currencymaster.currencyID,srp_erp_companycurrencyconversion.masterCurrencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyCode', $trans_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyCode', $againce_currency);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', $CI->common_data['company_data']['company_id']);
            $CI->db->join(
                'srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID'
            );
            $data_arr = $CI->db->get()->row_array();

            /** Transaction Currency  **/
            $data['trCurrencyID'] = $data_arr['masterCurrencyID'] ?? '';

            /** Conversion currency detail  **/
            $data['currencyID'] = $data_arr['currencyID'] ?? '';
            $data['conversion'] = round($data_arr['conversion'] ?? 0, 9);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'] ?? '';
            $data['CurrencyName'] = $data_arr['CurrencyName'] ?? '';
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'] ?? '';
            $data['convertedAmount'] = $amount ?? '' * $data_arr['conversion'] ?? '';
        }

        return $data;
    }
}

if (!function_exists('currency_conversionID'))
{
    function currency_conversionID($trans_currencyID, $againce_currencyID, $amount = 0)
    {
        /*********************************************************************************************
         * Always transaction is going with transaction currency [ Transaction Currency => OMR ]
         * If we want to know the reporting amount [ Reporting Currency => USD ]
         * So the currency_conversion functions 1st parameter will be the USD [what we looking for ]
         * And the 2nd parameter will be the OMR [what we already got]
         *
         * Ex :
         *    Transaction currency  =>  OMR     => $trCurrency  OR  $trans_currencyID
         *    Transaction Amount    =>  1000/-  => $trAmount    OR  $amount
         *    Reporting Currency    =>  USD     => $reCurrency  OR  $againce_currencyID
         *
         *    $conversionData  = currency_conversion($trCurrency, $reCurrency, $trAmount);
         *    $conversionRate  = $conversionData['conversion'];
         *    $decimalPlace    = $conversionData['DecimalPlaces'];
         *    $reportingAmount = round( ($trAmount / $conversionRate) , $decimalPlace );
         **********************************************************************************************/
        $data = array();
        $CI = &get_instance();
        if ($trans_currencyID == $againce_currencyID)
        {
            $CI->db->select('currencyID,CurrencyCode,DecimalPlaces,CurrencyName');
            $CI->db->from('srp_erp_companycurrencyassign');
            $CI->db->where('currencyID', $trans_currencyID);
            $CI->db->where('companyID', current_companyID());
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'] ?? 0;
            $data['conversion'] = 1;
            $data['CurrencyCode'] = $data_arr['CurrencyCode'] ?? '';
            $data['CurrencyName'] = $data_arr['CurrencyName'] ?? '';
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'] ?? 0;
            $data['convertedAmount'] = $amount * 1;
        }
        else
        {
            $CI->db->select('srp_erp_currencymaster.currencyID,conversion,CurrencyCode,CurrencyName,DecimalPlaces');
            $CI->db->from('srp_erp_companycurrencyconversion');
            $CI->db->where('srp_erp_companycurrencyconversion.masterCurrencyID', $trans_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.subCurrencyID', $againce_currencyID);
            $CI->db->where('srp_erp_companycurrencyconversion.companyID', current_companyID());
            $CI->db->join(
                'srp_erp_currencymaster',
                'srp_erp_currencymaster.currencyID = srp_erp_companycurrencyconversion.subCurrencyID'
            );
            $data_arr = $CI->db->get()->row_array();
            $data['currencyID'] = $data_arr['currencyID'] ?? 0;
            $data['conversion'] = round($data_arr['conversion'] ?? 0, 15);
            $data['CurrencyCode'] = $data_arr['CurrencyCode'] ?? '';
            $data['CurrencyName'] = $data_arr['CurrencyName'] ?? '';
            $data['DecimalPlaces'] = $data_arr['DecimalPlaces'] ?? 0;
            $data['convertedAmount'] = ($data_arr['conversion'] ?? 0) * $amount;        
        }

        return $data;
    }
}

if (!function_exists('getCurrencyID_byCurrencyCode'))
{
    function getCurrencyID_byCurrencyCode($currencyCodee)
    {
        $CI = &get_instance();
        $CI->db->select('currencyID');
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('CurrencyCode', $currencyCodee);
        $result = $CI->db->get()->row_array();

        return $result['currencyID'];
    }
}

if (!function_exists('getCurrencyDetail_byCurrencyCode'))
{
    function getCurrencyDetail_byCurrencyCode($currencyCodee)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('CurrencyCode', $currencyCodee);
        $result = $CI->db->get()->row_array();

        return $result;
    }
}

if (!function_exists('get_currency_details_by_id'))
{
    function get_currency_details_by_id($currencyID)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->where('currencyID', $currencyID);
        $result = $CI->db->get()->row_array();

        return $result;
    }
}


if (!function_exists('load_state_drop'))
{
    function load_state_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("stateID,stateDescription");
        $CI->db->FROM('srp_erp_state');
        $state = $CI->db->get()->result_array();

        return $state;
    }
}

if (!function_exists('load_Financial_year_comments'))
{
    function load_Financial_year_comments($comments, $financialYearID)
    {
        $CI = &get_instance();
        $CI->load->library('session');

        // Start building the editable input HTML
        $status = '<a href="#" data-type="textarea" data-placement="bottom"
        style="width: 250px; display:block;"
                     id="financialYearComments"
                     data-pk="' . $financialYearID . '|' . htmlspecialchars($comments) . '"
                     data-name="comments"
                     data-title="Edit Comments"
                     class="xEditableComments comment_change_' . $financialYearID . '"
                     data-value="' . htmlspecialchars($comments) . '">
                    ' . htmlspecialchars(mb_strimwidth($comments, 0, 100, '...')) . '
                    </a>';

        return $status;
    }
}


if (!function_exists('load_Financial_year_status'))
{
    function load_Financial_year_status($companyFinanceYearID, $isActive)
    {
        $status = '<center>';
        if ($isActive == 1)
        {

            $status .= '<input type="checkbox" id="statusactivate_' . $companyFinanceYearID . '" name="statusactivate" onchange="changeFinancial_yearsatus(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked>';
        }
        else if ($isActive == 0)
        {
            $status .= '<input type="checkbox" id="statusactivate_' . $companyFinanceYearID . '" name="statusactivate" onchange="changeFinancial_yearsatus(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_current'))
{
    function load_Financial_year_current($companyFinanceYearID, $is_current)
    {
        $checked = "";
        $status = '<center>';
        if ($is_current)
        {
            $checked = "checked";
        }
        $status .= '<input type="radio" onclick="changeFinancial_yearcurrent(' . $companyFinanceYearID . ')"  name="statuscurrent" id="statuscurrent_' . $companyFinanceYearID . '" ' . $checked . '>';
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_isactive_status'))
{
    function load_Financial_year_isactive_status($companyFinancePeriodID, $isActive)
    {
        $status = '<center>';
        if ($isActive)
        {
            $status .= '<input type="checkbox" id="isactivesatus_' . $companyFinancePeriodID . '" name="isactivesatus"  data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="changeFinancial_yearisactivesatus(' . $companyFinancePeriodID . ')" checked>';
            $status .= '</span>';
        }
        else
        {
            $status .= '<input type="checkbox" id="isactivesatus_' . $companyFinancePeriodID . '" name="isactivesatus" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="changeFinancial_yearisactivesatus(' . $companyFinancePeriodID . ')">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_isactive_current'))
{
    function load_Financial_year_isactive_current($companyFinancePeriodID, $is_current, $companyFinanceYearID)
    {
        $status = '<center>';
        if ($is_current)
        {
            $status .= '<input type="radio" class="radiobtn" onclick="check_financial_period_iscurrent(' . $companyFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $companyFinancePeriodID . '" checked>';
        }
        else
        {
            $status .= '<input type="radio" class="radiobtn" onclick="check_financial_period_iscurrent(' . $companyFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $companyFinancePeriodID . '">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_financialperiod_isclosed_closed'))
{
    function load_financialperiod_isclosed_closed($companyFinancePeriodID, $is_Close)
    {
        $status = '<center>';
        if ($is_Close)
        {
            $status .= '<input type="checkbox" id="closefinaperiod_' . $companyFinancePeriodID . '" name="closefinaperiod" onchange="changefinancialperiodclose(' . $companyFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0" checked>';
        }
        else
        {
            $status .= '<input type="checkbox" id="closefinaperiod_' . $companyFinancePeriodID . '" name="closefinaperiod" onchange="changefinancialperiodclose(' . $companyFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('load_Financial_year_close'))
{
    function load_Financial_year_close($companyFinanceYearID, $is_Close)
    {
        $status = '<center>';
        if ($is_Close)
        {
            $status .= '<input type="checkbox" id="closeactivate_' . $companyFinanceYearID . '" name="closeactivate" onchange="changeFinancial_yearclose(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0" checked disabled>';
        }
        else
        {
            $status .= '<input type="checkbox" id="closeactivate_' . $companyFinanceYearID . '" name="closeactivate" onchange="changeFinancial_yearclose(' . $companyFinanceYearID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('edit'))
{
    function edit($itemAutoID, $isActive = 0, $isSubItemExist = NULL, $deletedYN = NULL)
    {
        $status = '<span class="pull-right">';
        if ($deletedYN != 1)
        {
            $status .= '<a onclick="item_pricing_report(' . $itemAutoID . ');"><span title="Price Inquiry" rel="tooltip" class="glyphicon glyphicon-tag"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            if (isset($isSubItemExist) && $isSubItemExist == 1)
            {
                $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }


            $status .= '<a class="text-yellow" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';

            if ($isActive)
            {
                /*            <input type="checkbox" id="itemchkbox_' . $itemAutoID . '" name="itemchkbox" onchange="changeitemactive(' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br>*/
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';

                /*| &nbsp;&nbsp;<a onclick="delete_item_master(' . $itemAutoID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>*/
            }
            else
            {
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
            }
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="text-yellow" onclick="delete_item_master(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_item_master_action')) {
    function load_item_master_action($itemAutoID, $isActive = 0, $isSubItemExist = NULL, $deletedYN = NULL, $confirmedYN = 0, $approvedYN = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $ApprovalforItemMaster = getPolicyValues('AIM', 'All');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($deletedYN != 1) {
            if (isset($isSubItemExist) && $isSubItemExist == 1) {
                $status .= '<li><a href="#" class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');">
                            <i class="fa fa-list" style="color: #673ab7;"></i> Sub Items</a></li>';
            }

            $status .= '<li><a href="#" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');">
                        <i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>'; 

            $status .= '<li><a href="#" onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\');">
                        <i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>'; 

            if ($ApprovalforItemMaster == 1 && $approvedYN == 0 && $confirmedYN == 1) {
                $status .= '<li><a href="#" onclick="referback_item(' . $itemAutoID . ');">
                            <i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
            }

            if ($ApprovalforItemMaster != 1 || ($ApprovalforItemMaster == 1 && $approvedYN == 0 && $confirmedYN == 0)) {
                $status .= '<li><a href="#" onclick="delete_item_master(' . $itemAutoID . ');">
                            <i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
            }
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('edit_item'))
{
    function edit_item($itemAutoID, $isActive = 0, $isSubItemExist = NULL, $deletedYN = NULL, $confirmedYN = 0, $approvedYN = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $ApprovalforItemMaster = getPolicyValues('AIM', 'All');


        $status = '<span class="pull-right set-row-items">';
        if ($deletedYN != 1)
        {
            // $status .= '<a onclick="item_pricing_report(' . $itemAutoID . ');"><span title="Price Inquiry" rel="tooltip" class="glyphicon glyphicon-tag"></span></a>&nbsp;&nbsp;';
            if (isset($isSubItemExist) && $isSubItemExist == 1)
            {
                $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }


            $status .= '<a class="text-yellow" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

            if ($isActive)
            {
                /*            <input type="checkbox" id="itemchkbox_' . $itemAutoID . '" name="itemchkbox" onchange="changeitemactive(' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br>*/
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';

                /*| &nbsp;&nbsp;<a onclick="delete_item_master(' . $itemAutoID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>*/
            }
            else
            {
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
            }


            if ($ApprovalforItemMaster == 1)
            {
                if ($approvedYN == 0)
                {
                    if ($confirmedYN == 1)
                    {
                        $status .= '<a onclick="referback_item(' . $itemAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
                    }
                    else
                    {
                        /*if(empty($items)){ */
                        $status .= '<a class="text-yellow" onclick="delete_item_master(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        /* }*/
                    }
                }
            }
            else
            {
                /*   if(empty($items)){*/
                $status .= '<a class="text-yellow" onclick="delete_item_master(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                /*   }*/
            }
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('item_pricing'))
{
    function item_pricing($itemAutoID, $isActive = 0, $isSubItemExist = NULL, $deletedYN = NULL, $confirmedYN = 0, $approvedYN = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $ApprovalforItemMaster = getPolicyValues('AIM', 'All');


        $status = '<span class="pull-right set-row-items">';
        if ($deletedYN != 1)
        {
            $status .= '<a onclick="fetchPage(\'system/item/erp_itempricing_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Price Inquiry" rel="tooltip" class="glyphicon glyphicon-tag"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_payment_bank'))
{
    function fetch_payment_bank()
    {
        $CI = &get_instance();
        $CI->db->select('BankID,BankCode,BankName');
        $CI->db->from('srp_bankmaster');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Bank');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['BankID'] ?? '')] = trim($row['BankCode'] ?? '') . ' | ' . trim($row['BankName'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_countryFlags'))
{
    function load_countryFlags()
    {
        $CI = &get_instance();
        $CI->db->select('countryID,countryShortCode,CountryDes');
        $CI->db->from('srp_erp_countrymaster');
        $data_arr = $CI->db->get()->result_array();
        $countries = array('' => 'Select Country');

        if (isset($countries))
        {
            foreach ($data_arr as $data)
            {
                $countries[$data['countryShortCode']] = $data['CountryDes'];
            }
        }
        echo json_encode($countries);
    }
}

if (!function_exists('fetch_segment_for_reports'))
{
    function fetch_segment_for_reports() /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $showSegment = getPolicyValues('SEGH', 'All');

        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        if ($showSegment != 1)
        {
            $CI->db->where('isShow', 1);
        }
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->order_by('segmentCode');
        $data = $CI->db->get()->result_array();

        $data_arr = array();
        foreach ($data as $value)
        {
            $data_arr[] = $value['segmentID'];
        }

        return $data_arr;
    }
}

/*  # Start : fetch segments -- with policies
    # companyType and $companyID=NULL parameter is used when creating a new Jurnal Voucher for group companies  */
if (!function_exists('fetch_segment'))
{
    function fetch_segment($id = FALSE, $state = TRUE, $companyID = NULL)
    /** $companyID = NULL */
    /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $userGroupID = getUserGroupId();
        $showSegment = getPolicyValues('SEGH', 'All');
        $fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $CI->lang->load('common', $primaryLanguage);

        $companyType = $CI->session->userdata("companyType");

        if (!empty($companyID) && $companyType == 2)
        {
            $companyid = $companyID;
        }
        else
        {
            $companyid = $CI->common_data['company_data']['company_id'];
        }

        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);

        if ($fshowallsegmentYN == 1 || $fshowallsegmentYN == 'On')
        {
            $where_clause = "select segmentID from srp_erp_segment_usergroups where userGroupID='$userGroupID'";
            $CI->db->where("`segmentID`  IN ($where_clause)", null, false);
        }


        if ($showSegment != 1)
        {
            $CI->db->where('isShow', 1);
        }

        $CI->db->where('companyID', $companyid /* remove $CI->common_data['company_data']['company_id']*/);
        $CI->db->order_by('segmentCode');
        $data = $CI->db->get()->result_array();
        if ($companyType == 2)
        {
            return $data;
        }
        else
        {
            if ($state == TRUE)
            {
                $data_arr = [];
            }
            else
            {
                $data_arr = [];
            }
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    if ($id)
                    {
                        $data_arr[$row['segmentID']] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                    }
                    else
                    {
                        $data_arr[$row['segmentID'] . '|' . $row['segmentCode']] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                    }
                }
            }

            return $data_arr;
        }
    }
}
/* End : fetch segment*/


/* create function to fetch segments for iou voucher category */
if (!function_exists('fetch_iouVoucher_segment'))
{
    function fetch_iouVoucher_segment($seg_id, $id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $userGroupID = getUserGroupId();
        $showSegment = getPolicyValues('SEGH', 'All');
        $fshowallsegmentYN = getPolicyValues('UGSE', 'All');
        $companyid = $CI->common_data['company_data']['company_id'];
        $CI->lang->load('common', $primaryLanguage);

        $segID = explode('|', $seg_id);
        // print_r($segID[0]);
        // exit;

        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);
        $CI->db->where('segmentID', $segID[0]);

        if ($fshowallsegmentYN == 1 || $fshowallsegmentYN == 'On')
        {
            $where_clause = "select segmentID from srp_erp_segment_usergroups where userGroupID='$userGroupID'";
            $CI->db->where("`segmentID`  IN ($where_clause)", null, false);
        }

        if ($showSegment != 1)
        {
            $CI->db->where('isShow', 1);
        }

        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array();
        if ($state == TRUE)
        {
            // $data_arr= array('' => $CI->lang->line('common_select_segment'));
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . '|' . trim($row['description'] ?? '');
            }
        }
        //  print_r($data_arr);
        //         exit;
        return $data_arr;
    }
}

if (!function_exists('fetch_segment_v2'))
{
    function fetch_segment_v2($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $showSegment = getPolicyValues('SEGH', 'All');

        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('status', 1);

        if ($showSegment != 1)
        {
            $CI->db->where('isShow', 1);
        }

        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->order_by('segmentCode');
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
                else
                {
                    $data_arr[trim($row['segmentID'] ?? '')]  = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('invoice_total_value'))
{
    function invoice_total_value($id, $code = 2)
    {
        $tax = 0;
        $CI = &get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('invoiceAutoID', $id);
        $transaction_total_amount = $CI->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');
        $CI->db->select_sum('totalAfterTax');
        $CI->db->where('invoiceAutoID', $id);
        $item_tax = $CI->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
        $totalAmount = ($transaction_total_amount - $item_tax);
        $CI->db->select('taxPercentage');
        $CI->db->where('invoiceAutoID', $id);
        $data_arr = $CI->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
        for ($i = 0; $i < count($data_arr); $i++)
        {
            $tax += (($data_arr[$i]['taxPercentage'] / 100) * $totalAmount);
        }
        $transaction_total_amount += $tax;

        return number_format($transaction_total_amount, $code);
    }
}

if (!function_exists('contract_total_value'))
{
    function contract_total_value($id, $code = 2)
    {
        $tax = 0;
        $CI = &get_instance();
        $CI->db->select_sum('transactionAmount');
        $CI->db->where('contractAutoID', $id);
        $transaction_total_amount = $CI->db->get('srp_erp_contractdetails')->row('transactionAmount');
        return number_format($transaction_total_amount, $code);
    }
}

if (!function_exists('load_invoice_action'))
{
    function load_invoice_action($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isSytemGenerated, $isPreliminaryPrinted, $isRecurring, $retensionValue = null)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $policyPIE = getPolicyValues('PIE', 'All');
        $retensionEnabled = getPolicyValues('RETO', 'All');
        $companyID = current_companyID();

        $status = '<div class="btn-group style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" id="actionDropdown' . $poID . '" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left" aria-labelledby="actionDropdown' . $poID . '">';

        if ($policyPIE && $policyPIE == 1 && $approved != 1)
        {
            $title = 'Preliminary Not Submitted';
            $checked = $isPreliminaryPrinted == 1 ? 'checked' : '';
            $title = $checked ? 'Preliminary Submitted' : $title;

            $status .= '<li title="' . $title . '">
                            <input type="checkbox" id="isprimilinaryPrinted_' . $poID . '" name="isprimilinaryPrinted" data-size="mini" 
                            data-on-text="Preliminary Printed" data-handle-width="45" 
                            data-off-color="danger" data-on-color="success" 
                            data-off-text="Preliminary Not Printed" data-label-width="0" 
                            onclick="update_preliminary_print_status(' . $poID . ')" ' . $checked . '>
                        </li>';
        }

        if (empty($tempInvoiceID))
        {
            $status .= '<li><a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\' title="Attachment"><span class="glyphicon glyphicon-paperclip"></span> Attachment</a></li>';

            if ($isDeleted == 1)
            {
                $status .= '<li><a onclick="reOpen_contract(' . $poID . ');" title="Re Open"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                if ($isSytemGenerated != 1)
                {
                    $status .= '<li><a onclick=\'fetchPage("system/invoices/erp_invoices",' . $poID . ',"Edit Customer Invoice","PO");\' title="Edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
                }
                else
                {
                    $status .= '<li><a onclick=\'issystemgenerateddoc();\' title="Edit"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
                }
            }

            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0)
            {
                $status .= '<li><a onclick="referback_customer_invoice(' . $poID . ',' . $isSytemGenerated . ');" title="Refer Back"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
            }

            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" title="View"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';

            $vatRegisterYN = $CI->db->query("SELECT vatRegisterYN FROM `srp_erp_company` where company_id = $companyID")->row('vatRegisterYN');
            if ($vatRegisterYN == 1)
            {
                $status .= '<li><a target="_blank" onclick="printTemplate_select(\'' . $poID . '\')" title="Print"><span class="glyphicon glyphicon-print"></span> Print</a></li>';
            }
            else
            {
                $status .= '<li><a target="_blank" href="' . site_url('invoices/load_invoices_conformation/') . '/' . $poID . '" title="Print"><span class="glyphicon glyphicon-print"></span> Print</a></li>';
            }

            if ($approved == 1)
            {
                $status .= '<li><a onclick="sendemail(' . $poID . ')" title="Send Mail"><i class="fa fa-envelope" aria-hidden="true"></i> Send Mail</a></li>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '<li><a onclick="confirmCustomerInvoicefront(' . $poID . ')" title="Confirm"><span class="glyphicon glyphicon-ok"></span> Confirm</a></li>';
                $status .= '<li><a onclick="delete_item(' . $poID . ',\'Invoices\');" title="Delete"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
            }
        }
        else
        {
            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" title="View"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('invoices/load_invoices_conformation/') . '/' . $poID . '" title="Print"><span class="glyphicon glyphicon-print"></span> Print</a></li>';
        }

        $outputs = get_pv_rv_based_on_policy('RV');
        if ($approved == 1)
        {
            $status .= '<li><a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document"><i class="fa fa-search" aria-hidden="true"></i> Trace Document</a></li>';
        }

        if ($approved == 1 && $outputs)
        {
            $status .= '<li><a onclick="open_receipt_voucher_modal(' . $poID . ')" title="Create Receipt Voucher"><i class="fa fa-file-text" aria-hidden="true"></i> Create Receipt Voucher</a></li>';
        }

        if ($approved == 1)
        {
            $status .= '<li><a onclick="Recurring_model(' . $poID . ');" title="Recurring"><span class="glyphicon glyphicon-refresh" style="color:rgb(209, 91, 71);"></span> Recurring</a></li>';
        }

        if ($approved == 1 && $retensionEnabled == 1 && $retensionValue > 0)
        {
            $status .= '<li><a onclick="Retension_model(' . $poID . ');" title="Generate Retension Invoice"><i class="fa fa-file-text"></i> Generate Retension Invoice</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}


if (!function_exists('load_invoice_action_buyback'))
{
    function load_invoice_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $isPrintDN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","HCINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_buyback",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($createdUserID == trim($CI->session->userdata("empID")) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_customer_invoice(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'HCINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';


        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';


        // $status .= '&nbsp;|&nbsp;<a target="_blank" href="' . site_url('InvoicesPercentage/print_tageline_buyback/') . '/' . $poID . '" ><span title="Print Tag" rel="tooltip" class="fa fa-tags"></span></a> ';

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_contract_action')) {
    function load_contract_action($poID, $POConfirmedYN, $approved, $createdUserID, $documentID, $confirmedYN, $isDeleted, $confirmedbyempid, $isSystemGenerated, $closedYN, $advance = null, $isBackToBack = null) {
        $CI = &get_instance();
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $poID . ');" title="Re Open" rel="tooltip"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Re Open</a></li>';
        } else {
            $attachmentText = ($documentID == "QUT") ? "Quotation" : (($documentID == "CNT") ? "Contract" : (($documentID == "SO") ? "Sales Order" : $documentID));
            $status .= '<li><a onclick=\'attachment_modal(' . $poID . ',"' . $attachmentText . '","' . $documentID . '","' . $confirmedYN . '");\' title="Attachment" rel="tooltip"><span class="glyphicon glyphicon-paperclip"></span> Attachment</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            if ($isSystemGenerated != 1) {
                $editLink = ($advance) ? "erp_quotation_contract_job" : "erp_quotation_contract";
                $status .= '<li><a onclick=\'fetchPage("system/quotation_contract/' . $editLink . '",' . $poID . ',"Edit Quotation or Contract","' . $documentID . '");\' title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
            } else {
                $status .= '<li><a onclick=\'issystemgenerateddoc();\' title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
            }
        }

        if ($POConfirmedYN == 1 && $advance) {
            $status .= '<li><a onclick=\'fetchPage("system/quotation_contract/erp_quotation_contract_job",' . $poID . ',"Edit Quotation or Contract","' . $documentID . '");\' title="Edit" rel="tooltip"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedbyempid == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referback_customer_contract(' . $poID . ',\'' . $documentID . '\',' . $isSystemGenerated . ');" title="Refer Back" rel="tooltip"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\')" title="View" rel="tooltip"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
        $status .= '<li><a onclick="document_drill_down_View_modal(\'' . $poID . '\',\'' . $documentID . '\')" title="Drill Down" rel="tooltip"><i class="fa fa-bars" aria-hidden="true"></i> Drill Down</a></li>';
        $status .= '<li><a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation/') . '/' . $poID . '" title="Print" rel="tooltip"><span class="glyphicon glyphicon-print"></span> Print</a></li>';

        if ($isBackToBack == 1) {
            $status .= '<li><a target="_blank" href="' . site_url('Quotation_contract/load_cost_sheet/') . '/' . $poID . '" title="Cost Sheet Print" rel="tooltip"><span class="glyphicon glyphicon-print"></span> Cost Sheet Print</a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('Quotation_contract/load_cost_distribution/') . '/' . $poID . '" title="Cost Distribution" rel="tooltip"><span class="glyphicon glyphicon-print"></span> Cost Distribution</a></li>';
        }

        if ($isDeleted == 0 && $POConfirmedYN == 1 && $approved == 1) {
            $status .= '<li><a onclick="document_version_View_modal(\'' . $documentID . '\',\'' . $poID . '\')" title="Documents" rel="tooltip"><i class="fa fa-files-o" aria-hidden="true"></i> Documents</a></li>';
            $status .= '<li><a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i> Send Mail</a></li>';
        }

        if ($documentID == 'QUT' && $isDeleted == 0 && $POConfirmedYN == 1 && $approved == 1) {
            $status .= '<li><a href="' . site_url('Quotation_contract/load_payment_advice/') . '/' . $poID . '" target="_blank" title="Payment Advice" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i> Payment Advice</a></li>';
        }

        if ($isDeleted == 0 && $POConfirmedYN == 1 && $approved == 1) {
            $status .= '<li><a onclick="open_generated_payment_applications_modal(' . $poID . ',\'' . $documentID . '\')" target="_blank" title="Payment Application" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i> Payment Application</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="delete_item(' . $poID . ',\'' . $documentID . '\');" title="Delete" rel="tooltip"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a onclick="traceDocument(' . $poID . ',\'' . $documentID . '\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i> Trace Document</a></li>';
            $status .= '<li><a onclick="check_item_balance_from_quotation_contract(' . $poID . ')" title="Generate Delivery Order" rel="tooltip"><i class="fa fa-file" aria-hidden="true"></i> Generate Delivery Order</a></li>';
        }
        
        if ($approved == 5) {
            $status .= '<li><a onclick="traceDocument(' . $poID . ',\'' . $documentID . '\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i> Trace Document</a></li>';
        }
        
        if ($approved == 1 && $isDeleted == 0 && $closedYN == 0) {
            $status .= '<li><a onclick=\'contract_close("' . $poID . '");\' title="Close" rel="tooltip"><i title="Close" rel="tooltip" class="fa fa-times" aria-hidden="true"></i> Close</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_rv_action')) {
    function load_rv_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $isSystemGenerated, $bankGLAutoID, $paymentType)
    {
        $CI = &get_instance();
        $CI->db->select('isCash');
        $CI->db->where('GLAutoID', $bankGLAutoID);
        $isCash = $CI->db->get('srp_erp_chartofaccounts')->row_array();

        $CI->load->library('session');
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $poID . ',"Receipt Voucher","RV",' . $POConfirmedYN . ');\'><span class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></span> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $poID . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Re Open</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            if ($isSystemGenerated != 1) {
                $status .= '<li><a onclick=\'fetchPage("system/receipt_voucher/erp_receipt_voucher",' . $poID . ',"Edit Receipt Voucher","PO");\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            } else {
                $status .= '<li><a onclick=\'issystemgenerateddoc();\'><span class="glyphicon glyphicon-pencil" style="color: #116f5e;"></span> Edit</a></li>';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referback_receipt_voucher(' . $poID . ',' . $isSystemGenerated . ');"><span class="glyphicon glyphicon-repeat" style="color: rgb(209, 91, 71);"></span> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\')"><span class="glyphicon glyphicon-eye-open" style="color: #03a9f4;"></span> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation/') . '/' . $poID . '"><span class="glyphicon glyphicon-print" style="color: #607d8b;"></span> Print</a></li>';

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="delete_item(' . $poID . ',\'Receipt Voucher\');"><span class="glyphicon glyphicon-trash" style="color: rgb(209, 91, 71);"></span> Delete</a></li>';
        }

        if ($isCash['isCash'] != 1 && $approved == 1 && $paymentType == 2) {
            $status .= '<li><a target="_blank" href="' . site_url('Receipt_voucher/load_rv_bank_transfer/') . '/' . $poID . '" hidden><span class="glyphicon glyphicon-file"></span> Bank Transfer Letter</a></li>';
        }

        if ($approved == 1) {
            $status .= '<li><a onclick="traceDocument(' . $poID . ',\'RV\')"><span class="fa fa-search" style="color: #fdc45e;" aria-hidden="true"></span> Trace Document</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('inv_action_approval'))
{
    function inv_action_approval($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('sales_markating_approval', $primaryLanguage);
        $invoice = $CI->lang->line('sales_markating_sales_purachase_commission_invoice');

        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $invoice . '", "' . $documentID . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","","' . $approval . '"  ); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('con_action_approval'))
{
    function con_action_approval($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('RV_action_approval'))
{
    function RV_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= ' &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('je_total_value'))
{
    function je_total_value($totalAmount, $currency)
    {
        return number_format($totalAmount, $currency);
    }
}

if (!function_exists('clear_descriprions'))
{
    function clear_descriprions($descriprions)
    {
        return htmlentities(str_replace(array('"', "'"), ' ', $descriprions));
    }
}

if (!function_exists('jv_approval'))
{
    function jv_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '&nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'JV\', ' . $poID . ',\' \', ' . $approval . ')"> <span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;&nbsp;';
        }


        //$status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_employee_currency'))
{
    function get_employee_currency($empID, $returnType)
    {
        $CI = &get_instance();

        $CI->db->select("cur.CurrencyCode AS code, DecimalPlaces AS dPlace")
            ->from("srp_employeesdetails AS emp")
            ->join("srp_erp_currencymaster AS cur", "cur.currencyID = emp.payCurrencyID")
            ->where("EIdNo='$empID'");
        $currency = $CI->db->get()->row();


        if ($returnType == 'c_code')
        {
            $val = $currency->code;
        }
        elseif ($returnType == '')
        {
            $val = $currency->dPlace;
        }
        elseif ($returnType == 'det')
        {
            $val = $currency;
        }
        else
        {
            $val = $currency->dPlace;
        }

        return $val;
    }
}

if (!function_exists('conversionRateUOM'))
{
    function conversionRateUOM($umo, $default_umo)
    {
        $CI = &get_instance();
        $comm_id = $CI->common_data['company_data']['company_id'];
        $CI->db->select('UnitID');
        $CI->db->where('UnitShortCode', $default_umo);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $masterUnitID = $CI->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $CI->db->select('UnitID');
        $CI->db->where('UnitShortCode', $umo);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $subUnitID = $CI->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $CI->db->select('conversion');
        $CI->db->from('srp_erp_unitsconversion');
        $CI->db->where('masterUnitID', $masterUnitID);
        $CI->db->where('subUnitID', $subUnitID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row('conversion');
    }
}

if (!function_exists('conversionRateUOM_id'))
{
    function conversionRateUOM_id($subUnitID, $masterUnitID)
    {
        $CI = &get_instance();
        $comm_id = $CI->common_data['company_data']['company_id'];
        $CI->db->select('conversion');
        $CI->db->from('srp_erp_unitsconversion');
        $CI->db->where('masterUnitID', $masterUnitID);
        $CI->db->where('subUnitID', $subUnitID);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        return $CI->db->get()->row('conversion');
    }
}

if (!function_exists('load_bank_with_card'))
{
    function load_bank_with_card($active = null)
    {
        $CI = &get_instance();
        $CI->db->select('GLAutoID, bankName, bankBranch, GLSecondaryCode, systemAccountCode, GLDescription');
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        if ($active)
        {
            $CI->db->WHERE('isActive', 1);
        }
        else
        {
            $CI->db->where('isBank', 1);
            $CI->db->where('isCard', 1);
        }

        return $CI->db->get()->result_array();
    }
}

/**** Added by mubashir ***/
if (!function_exists('fetch_item_data_by_company'))
{
    function fetch_item_data_by_company($drop = null, $mainCategory = null)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_itemmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);

        $CI->db->where('isActive', 1);
        $CI->db->where('masterApprovedYN', 1);

        if ($mainCategory)
        {
            $CI->db->where('mainCategory', $mainCategory);
        }
        else
        {
            $CI->db->where('financeCategory', 1);
        }

        $data = $CI->db->get()->result_array();
        if ($drop)
        {
            $base_arr = array();
            if ($mainCategory)
            {
                $base_arr[0] = 'Select Sevice Item';
            }

            foreach ($data as $item)
            {
                $base_arr[$item['itemAutoID']] = $item['itemSystemCode'] . ' - ' . $item['itemName'];
            }

            return $base_arr;
        }

        return $data;
    }
}

if (!function_exists('fetch_group_item_data_by_company'))
{
    function fetch_group_item_data_by_company()
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupitemmaster');
        $CI->db->join('srp_erp_groupitemmasterdetails', 'srp_erp_groupitemmaster.itemAutoID = srp_erp_groupitemmasterdetails.groupItemMasterID', 'INNER');
        $CI->db->where('srp_erp_groupitemmaster.groupID', current_companyID());
        $CI->db->where('financeCategory', 1);
        $CI->db->group_by('srp_erp_groupitemmaster.itemAutoID');

        return $CI->db->get()->result_array();
    }
}

/*display serverside warning message for reporting purpose*/
if (!function_exists('warning_message'))
{
    function warning_message($message)
    {
        return '<div class="callout callout-warning">
               ' . $message . '
              </div>';
    }
}

/*export excel and pdf button*/
if (!function_exists('export_buttons'))
{
    function export_buttons($id, $fileName, $excel = TRUE, $pdf = TRUE, $btnSize = 'btn-xs', $functionName = 'generateReportPdf()')
    {
        $export = '<div class="pull-right">';
        if ($pdf)
        {
            $export .= '<button class="btn btn-pdf ' . $btnSize . '" id="btn-pdf" type="button" onclick="' . $functionName . '">
                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
            </button> ';
        }
        if ($excel)
        {
            $export .= '<a href="" class="btn btn-excel ' . $btnSize . '" id="btn-excel" download="' . $fileName . '.xls"
               onclick="var file = tableToExcel(\'' . $id . '\', \'' . $fileName . '\'); $(this).attr(\'href\', file);">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a>';
        }
        $export .= '</div>';

        return $export;
    }
}

/*display local and reporting currency for reporting purpose*/
if (!function_exists('show_report_currency'))
{
    function show_report_currency()
    {
        $CI = &get_instance();

        return '<div class="col-md-12">
            <strong>Currency: </strong>' . $CI->common_data['company_data']['company_default_currency'] . '|' . $CI->common_data['company_data']['company_reporting_currency'] . '</div>';
    }
}

/*get financial year for a perticular date*/
if (!function_exists('get_financial_year'))
{

    function get_financial_year($date)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where("'{$date}' BETWEEN beginingDate AND endingDate");

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('get_financial_year_srm_api'))
{

    function get_financial_year_srm_api($date, $companyID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyID', $companyID);
        $CI->db->where("'{$date}' BETWEEN beginingDate AND endingDate");

        return $CI->db->get()->row_array();
    }
}

/*get group financial year for a perticular date*/
if (!function_exists('get_group_financial_year'))
{

    function get_group_financial_year($date)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupfinanceyear');
        $CI->db->WHERE('groupID', current_companyID());
        $CI->db->where("'{$date}' BETWEEN beginingDate AND endingDate");

        return $CI->db->get()->row_array();
    }
}

/*print debit credit*/
if (!function_exists('print_debit_credit'))
{
    function print_debit_credit($amount, $decimalPlace = 2, $GLAutoID = NULL, $masterCategory = NULL, $GLDescription = NULL, $currency = NULL, $isLink = FALSE, $month = NULL)
    {
        if ($isLink)
        {
            if ($amount < 0)
            {
                return '<td class="text-right">-</td><td class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(
                    abs($amount),
                    $decimalPlace
                ) . '</a></td>';
            }
            else
            {
                if ($amount > 0)
                {
                    return '<td  class="text-right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(
                        $amount,
                        $decimalPlace
                    ) . '</a></td><td class="text-right">-</td>';
                }
                else
                {
                    return "<td  class='text-right'>-</td><td class='text-right'>-</td>";
                }
            }
        }
        else
        {
            if ($amount < 0)
            {
                return "<td class='text-right'>-</td><td class='text-right'>" . number_format(
                    abs($amount),
                    $decimalPlace
                ) . "</td>";
            }
            else
            {
                if ($amount > 0)
                {
                    return "<td  class='text-right'>" . number_format(
                        $amount,
                        $decimalPlace
                    ) . "</td><td class='text-right'>-</td>";
                }
                else
                {
                    return "<td  class='text-right'>-</td><td class='text-right'>-</td>";
                }
            }
        }
    }
}

if (!function_exists('print_debit_credit_pdf'))
{
    function print_debit_credit_pdf($amount, $decimalPlace = 2, $GLAutoID = NULL, $masterCategory = NULL, $GLDescription = NULL, $currency = NULL, $isLink = FALSE, $month = NULL)
    {
        if ($isLink)
        {
            if ($amount < 0)
            {
                return '<td class="text-right">-</td><td align="right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(
                    abs($amount),
                    $decimalPlace
                ) . '</a></td>';
            }
            else
            {
                if ($amount > 0)
                {
                    return '<td  align="right"><a href="#" class="drill-down-cursor" onclick="generateDrilldownReport(\'' . $GLAutoID . '\',\'' . $masterCategory . '\',\'' . $GLDescription . '\',\'' . $currency . '\',\'' . $month . '\')">' . number_format(
                        $amount,
                        $decimalPlace
                    ) . '</a></td><td align="right">-</td>';
                }
                else
                {
                    return "<td  class='text-right'>-</td><td align='right'>-</td>";
                }
            }
        }
        else
        {
            if ($amount < 0)
            {
                return "<td class='text-right'>-</td><td align='right'>" . number_format(
                    abs($amount),
                    $decimalPlace
                ) . "</td>";
            }
            else
            {
                if ($amount > 0)
                {
                    return "<td  align='right'>" . number_format(
                        $amount,
                        $decimalPlace
                    ) . "</td><td align='right'>-</td>";
                }
                else
                {
                    return "<td  align='right'>-</td><td align='right'>-</td>";
                }
            }
        }
    }
}

// Get a set of date beetween the 2 period
if (!function_exists('get_month_list_from_date'))
{
    function get_month_list_from_date($beginingDate, $endDate, $format, $intervalType, $caption = "MY")
    {
        $start = new DateTime($beginingDate); // beginingDate
        $end = new DateTime($endDate); // endDate
        $interval = DateInterval::createFromDateString($intervalType); // 1 month interval
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period
        $months = array();
        foreach ($period as $dt)
        {
            if ($caption == 'MY')
            {
                $months[$dt->format($format)] = $dt->format("M") . "-" . $dt->format("Y");
            }
            else if ($caption == 'M')
            {
                $months[$dt->format($format)] = $dt->format("M");
            }
            else if ($caption == 'My')
            {
                $months[$dt->format($format)] = $dt->format("M") . "-" . $dt->format("y");
            }
        }

        return $months;
    }
}
/*get last two financial year*/
if (!function_exists('get_last_two_financial_year'))
{

    function get_last_two_financial_year()
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('isActive', 1);
        $CI->db->ORDER_BY('beginingDate DESC');

        return $CI->db->get()->result_array();
    }
}
/*get format number for datatable with extended style*/
if (!function_exists('dashboard_format_number'))
{
    function dashboard_format_number($amount = 0, $decimal_place = 2)
    {
        if ($amount == 0)
        {
            return "<span class='text-muted'>" . number_format($amount, $decimal_place) . "</span>";
        }
        else if ($amount > 0)
        {
            return "<span class='text-green'>" . number_format($amount, $decimal_place) . "</span>";
        }
        else
        {
            return "<span class='text-red'>" . number_format($amount, $decimal_place) . "</span>";
        }
    }
}

/*get format number for report without rounding the amount with extended style*/
if (!function_exists('report_format_number'))
{
    function report_format_number($amount = 0)
    {
        //$amount = explode(".",$amount);
        $commaSepAmount = preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $amount);

        return $commaSepAmount;
    }
}


/*color due days*/
if (!function_exists('dashboard_color_duedays'))
{
    function dashboard_color_duedays($days)
    {
        if ($days <= 5)
        {
            return "<span class='badge bg-red'>" . $days . "</span>";
        }
        else if ($days > 5 && $days <= 10)
        {
            return "<span class='badge bg-green'>" . $days . "</span>";
        }
        else
        {
            return "<span class='badge bg-default'>" . $days . "</span>";
        }
    }
}

/*get all financial year for dropdown*/
if (!function_exists('all_financeyear_report_drop'))
{
    function all_financeyear_report_drop($policyFormat = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('companyFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_companyfinanceyear');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($policyFormat)
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = convert_date_format(trim($row['beginingDate'] ?? '')) . ' - ' . convert_date_format(trim($row['endingDate'] ?? ''));
                }
                else
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = trim($row['beginingDate'] ?? '') . ' - ' . trim($row['endingDate'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

/*get all group financial year for dropdown*/
if (!function_exists('all_group_financeyear_report_drop'))
{
    function all_group_financeyear_report_drop($policyFormat = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('groupFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_groupfinanceyear');
        $CI->db->where('groupID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Financial Year');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($policyFormat)
                {
                    $data_arr[trim($row['groupFinanceYearID'] ?? '')] = convert_date_format(trim($row['beginingDate'] ?? '')) . ' - ' . convert_date_format(trim($row['endingDate'] ?? ''));
                }
                else
                {
                    $data_arr[trim($row['groupFinanceYearID'] ?? '')] = trim($row['beginingDate'] ?? '') . ' - ' . trim($row['endingDate'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

/**** End ***/

/*bank rec - shahmy*/

if (!function_exists('load_bank_rec_action'))
{
    function load_bank_rec_action($glCode)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'fetchPage("system/bank_rec/erp_bank_reconciliation_bank_summary","' . $glCode . '","Bank Reconciliation ","Bank Reconciliation"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_bank_rec_summary_action'))
{
    function load_bank_rec_summary_action($glCode, $bankRecAutoID, $confirmYN, $approvedYN, $createdUserID, $confirmedByEmpID)
    {
        $data = $glCode . '|' . $bankRecAutoID;
        $dataarray = array($glCode, $bankRecAutoID);
        $status = '<span class="pull-right">';
        $CI = &get_instance();

        /**/
        $CI->db->select('bankRecMonthID');
        $CI->db->from('srp_erp_bankledger');
        $CI->db->where('bankRecMonthID', $bankRecAutoID);
        $datas = $CI->db->get()->row_array();


        $status .= '<a onclick=\'attachment_modal(' . $bankRecAutoID . ',"Bank Reconciliation","BR");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmYN == 1)
        {
            $status .= '<a onclick="referback_bankrec(' . $bankRecAutoID . ');"><span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }

        if ($confirmYN == 0 || $confirmYN == 3 || $confirmYN == 2)
        {
            $status .= '<a onclick=\'fetchPage("system/bank_rec/erp_bank_reconciliation_new","' . $data . '","Bank Reconciliation  ","Bank Reconciliation ","BR"); \'><span class="glyphicon glyphicon-pencil" ></span>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        }


        $status .= '<a target="_blank" onclick="documentPageView_modal(\'BR\',\'' . $bankRecAutoID . '\',\'' . $glCode . '\')" ><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_rec_book_balance/') . '/' . $bankRecAutoID . '/' . $glCode . '" ><span class="glyphicon glyphicon-print"></span></a>';

        if (empty($datas['bankRecMonthID']))
        {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_bankrec(' . $bankRecAutoID . ');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        //  $status .='<a target="_blank" href="/srp_new/index.php/Bank_rec/bank_rec_confirmation/'.$bankRecAutoID.'"><span class="glyphicon glyphicon-print"></span></a>';


        $status .= '</span>';

        return $status;
    }
}

/*end of bank rec*/

if (!function_exists('bankrec_approval'))
{
    function bankrec_approval($bankRecAutoID, $bankGLAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $bankRecAutoID . '","' . $bankGLAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BR\',\'' . $bankRecAutoID . '\',\'' . $bankGLAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        //$status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_rec_book_balance/') . '/' . $bankRecAutoID . '/' . $bankGLAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

/**/
if (!function_exists('fetch_by_gl_codes'))
{
    function fetch_by_gl_codes($codes = NULL)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        if ($codes)
        {
            foreach ($codes as $key => $code)
            {
                $CI->db->where($key, $code);
            }
        }
        $CI->db->where('controllAccountYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_all_location'))
{
    function fetch_all_location()
    {
        $CI = &get_instance();
        $CI->db->SELECT("locationID,locationName");
        $CI->db->from('srp_erp_location');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Location');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['locationID'] ?? '')] = trim($row['locationName'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('fetch_all_custodian'))
{
    function fetch_all_custodian()
    {
        $CI = &get_instance();
        $CI->db->SELECT("id,custodianName");
        $CI->db->from('srp_erp_fa_custodian');
        $CI->db->where('companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Custodian Type');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['custodianName'] ?? '');
            }
        }

        return $data_arr;
    }
}



if (!function_exists('fetch_all_companytemplate'))
{
    function fetch_all_companytemplate($reportID)
    {
        if ($reportID == "FIN_IS")
        {
            $rid = 5;
        }
        else if ($reportID == "FIN_BS")
        {
            $rid = 6;
        }

        $CI = &get_instance();
        $CI->db->SELECT("companyReportTemplateID,description");
        $CI->db->from('srp_erp_companyreporttemplate');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('reportID', $rid);
        $data = $CI->db->get()->result_array();
        $data_arr = array('0' => 'Default template');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['companyReportTemplateID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}










if (!function_exists('fetch_master_category'))
{
    function fetch_master_category()
    {
        $CI = &get_instance();
        $CI->db->SELECT("faCatID,catDescription");
        $CI->db->from('srp_erp_fa_category');
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['faCatID'] ?? '')] = trim($row['catDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_sub_category'))
{
    function fetch_sub_category($masterCat)
    {
        $CI = &get_instance();
        $CI->db->SELECT("faCatSubID,catDescription,faCatID");
        $CI->db->from('srp_erp_fa_categorysub');
        $CI->db->where('isActive', 1);
        $CI->db->where('faCatID', $masterCat);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Sub Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['faCatSubID'] ?? '')] = trim($row['catDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}
/**/

if (!function_exists('get_documentCode'))
{
    function get_document_code($documentID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->select('prefix,startSerialNo,serialNo');
        $CI->db->from('srp_erp_documentcodemaster');
        $CI->db->where('documentID', $documentID);
        /*    $CI->db->where('companyID', $companyID);*/
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('bank_transaction_edit')) {
    function bank_transaction_edit($transactionID, $confirmYN, $approvedYN, $createduserID, $transferType, $fromBankGLAutoID, $confirmedByEmp) {
        $printChequeBeforeApproval = getPolicyValues('CHA', 'All');
        if ($printChequeBeforeApproval == ' ' || $printChequeBeforeApproval == null) {
            $printChequeBeforeApproval = 0;
        }

        $CI = &get_instance();
        $CI->db->select('COUNT(`srp_erp_chartofaccountchequetemplates`.`coaChequeTemplateID`) as templateCount');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('GLAutoID', $fromBankGLAutoID);
        $CI->db->join('srp_erp_systemchequetemplates', 'srp_erp_chartofaccountchequetemplates.systemChequeTemplateID = srp_erp_systemchequetemplates.chequeTemplateID', 'left');
        $CI->db->from('srp_erp_chartofaccountchequetemplates');
        $count = $CI->db->get()->row_array();

        $CI->db->select('coaChequeTemplateID');
        $CI->db->where('GLAutoID', $fromBankGLAutoID);
        $CI->db->where('companyID', current_companyID());
        $templateexist = $CI->db->get('srp_erp_chartofaccountchequetemplates')->row_array();

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $transactionID . ',"Bank Transfer","BT",' . $confirmYN . ');\'><i class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></i> Attachment</a></li>';

        if ($printChequeBeforeApproval == 1) {
            if ($transferType == 2 && !empty($templateexist)) {
                $status .= '<li><a onclick=cheque_print_modal(' . $transactionID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i class="fa fa-cc" style="color:#607d8b;"></i> Cheque Print</a></li>';
            }
        } else {
            if ($transferType == 2 && $approvedYN == 1 && !empty($templateexist)) {
                $status .= '<li><a onclick=cheque_print_modal(' . $transactionID . ',' . $count['templateCount'] . ',' . $templateexist['coaChequeTemplateID'] . '); ><i class="fa fa-cc" style="color:#607d8b;"></i> Cheque Print</a></li>';
            }
        }

        if ($approvedYN != 1 && $confirmYN == 1 && ($createduserID == current_userID() || $confirmedByEmp == current_userID())) {
            $status .= '<li><a onclick="referbackgrv(' . $transactionID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        if ($confirmYN == 0 || $confirmYN == 3 || $confirmYN == 2) {
            $status .= '<li><a onclick=\'bank_transaction_edit("' . $transactionID . '"); \'><i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>';

            $status .= '<li><a onclick="delete_item(' . $transactionID . ');"><i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'BT\',\'' . $transactionID . '\')" ><i class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></i> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $transactionID . '" ><i class="glyphicon glyphicon-print" style="color:#607d8b;"></i> Print</a></li>';

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('bank_transfer_approval'))
{
    function bank_transfer_approval($bankTransferAutoID, $approvalLevelID, $approvedYN, $documentApprovedID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $bankTransferAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'BT\',\'' . $bankTransferAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '<a target="_blank" href="' . site_url('Bank_rec/bank_transfer_view/') . '/' . $bankTransferAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_budget_action'))
{
    function load_budget_action($budgetAutoID, $confirmedYN, $approvedYN)
    {
        $status = '<span class="pull-right">';
        if ($confirmedYN != 1)
        {
            $status .= '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page","' . $budgetAutoID . '","Budget Detail ","Budget Detail"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>&nbsp;&nbsp;';
        }
        if ($confirmedYN == 1 && $approvedYN != 1)
        {
            $status .= '<a onclick="referbackbudget(' . $budgetAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }
        $status .= '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page_view","' . $budgetAutoID . '","Budget Detail ","Budget Detail"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open" ></span>';

        $status .= '</span>';

        return $status;
    }
}
/*bank register*/
if (!function_exists('load_bank_register_action'))
{
    function load_bank_register_action($glCode, $from, $to)
    {
        $data = $from . '_' . $to;
        // echo $to;
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'fetchPage("system/bank_register/erp_bank_register_details","' . $glCode . '","Bank Register ","Bank Register","' . $data . '"); \'><span class="glyphicon glyphicon-eye-open" ></span>';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('company_PL_bank_account_drop'))
{
    function company_PL_bank_account_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select("GLAutoID,systemAccountCode,
    GLSecondaryCode,
    GLDescription");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterCategory', 'PL');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }
        }

        return $bank_arr;
    }
}


if (!function_exists('fetch_all_gl_codes_report'))
{ /*fetch all gl codes except controll accounts and master accounts*/
    function fetch_all_gl_codes_report()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        //$CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_all_group_gl_codes_report'))
{ /*fetch all group gl codes except controll accounts and master accounts*/
    function fetch_all_group_gl_codes_report()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_groupchartofaccounts');
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('groupID', current_companyID());
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_currencyAssigned'))
{ /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_currencyAssigned()
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("select concat(CurrencyCode,' | ',CurrencyName) as currencyNmae,concat(currencyID,'|',CurrencyCode,'|',DecimalPlaces) as currency from srp_erp_currencymaster
WHERE NOT EXISTS (select null from srp_erp_companycurrencyassign WHERE companyID={$companyID} AND srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID)")->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['currency'] ?? '')] = trim($row['currencyNmae'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('dropdown_currencyAssignedExchangeDropdown'))
{ /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_currencyAssignedExchangeDropdown()
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT srp_erp_currencymaster.currencyID,concat(srp_erp_currencymaster.CurrencyCode,' | ',srp_erp_currencymaster.CurrencyName) as currencyName FROM srp_erp_companycurrencyassign LEFT JOIN srp_erp_currencymaster on srp_erp_companycurrencyassign.currencyID=srp_erp_currencymaster.currencyID
WHERE companyID = {$companyID}")->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['currencyID'] ?? '')] = trim($row['currencyName'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_currency_ID'))
{
    function fetch_currency_ID($code)
    {
        $CI = &get_instance();
        $CI->db->SELECT("currencyID");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('currencyID');
    }
}

if (!function_exists('get_currency_code'))
{
    function get_currency_code($cuID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('currencyID', $cuID);

        return $CI->db->get()->row('CurrencyCode');
    }
}

if (!function_exists('get_currency_id'))
{
    function get_currency_id($code)
    {
        $CI = &get_instance();
        $CI->db->SELECT("currencyID");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);

        return $CI->db->get()->row('currencyID');
    }
}

if (!function_exists('fetch_account_review'))
{
    function fetch_account_review($AccountReviewState = TRUE, $printState = TRUE, $approval = 0,$versionID = null)
    {
        if ($approval == 1)
        {
            if ($AccountReviewState)
            {
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default-new size-sm de_link mx-1" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a>  </span> </div> </div>';

                return $html;
            }
        }
        else
        {
            if($versionID){
                $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default-new size-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';

                return $html;
            }else{
                if ($AccountReviewState && $printState)
                {
                    $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default-new size-sm de_link mx-1" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a> <a class="btn btn-default-new size-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';

                    return $html;
                }
                else if ($AccountReviewState)
                {
                    $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default-new size-sm de_link mx-1" id="de_link" target="_blank" href="#"><span class="glyphicon glyphicon-random" aria-hidden="true"></span> &nbsp;&nbsp;&nbsp;Account Review Entries </a>  </span> </div> </div>';

                    return $html;
                }
                else if ($printState)
                {
                    $html = '<div id="" class="row review hide"> <div class="col-md-12"> <span class="no-print pull-right"> <a class="btn btn-default-new size-sm no-print pull-right" id="a_link" target="_blank" href="#"> <span class="glyphicon glyphicon-print" aria-hidden="true"></span> </a> </span> </div> </div>';

                    return $html;
                }
            }
            
        }
    }
}

if (!function_exists('dropdown_erp_usergroups'))
{ /*fetch all gl codes except controll accounts and master accounts*/
    function dropdown_erp_usergroups($companyID)
    {

        $CI = &get_instance();
        if (!isset($companyID) || $companyID == '')
        {
            $companyID = $CI->common_data['company_data']['company_id'];

            return $customer_arr = array('' => 'Select');
        }


        $data = $CI->db->query("SELECT userGroupID,description FROM `srp_erp_usergroups` where isActive=1  AND companyID = {$companyID}")->result_array();
        $data_arr = array('' => 'All');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['userGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('edit_employee_nav_access'))
{
    function edit_employee_nav_access($itemAutoID)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="delete_item(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('srm_rfq_vendor_new_update_aprove'))
{
    function srm_rfq_vendor_new_update_aprove($inquaryDetailID, $isvendorSubmit, $referType, $unitPriceApproveYN, $qtyApproveYN, $dateApproveYN)
    {
        if ($isvendorSubmit == 2 && $referType == "Price" && $unitPriceApproveYN == 1 && $qtyApproveYN == 1 && $dateApproveYN == 1)
        {
            // $status = '<span class="pull-right">';
            // $status .= '<a onclick="add_new_vendor_price(' . $inquaryDetailID . ');" class="btn btn-success-new btn-xs">View</a>';
            // $status .= '</span>';

            $status = '<span class="pull-right">';
            $status .= '<a onclick="add_new_vendor_price(' . $inquaryDetailID . ');" ><span title="view" rel="tooltip" class="glyphicon glyphicon-eye-open" style="color:rgb(71, 209, 86);"></a>';
            $status .= '</span>';
        }

        else if ($isvendorSubmit == 1 && $referType == "Price")
        {
            $status = '<span class="pull-right">';
            $status .= '<a onclick="add_new_vendor_price(' . $inquaryDetailID . ');" class="btn btn-success-new btn-xs">View</a>';
            $status .= '</span>';
        }
        else if ($isvendorSubmit == 2 && $referType == "General")
        {
            $status = '<span class="pull-right">';
            $status .= '<h5 ><span title="ok" rel="tooltip" class="glyphicon glyphicon-ok" style="color:rgb(71, 209, 86);"></h5>';
            $status .= '</span>';
        }
        else
        {
            $status = '<span class="pull-right">';
            $status .= '<h5 ><span class="badge badge-primary">Pending</span></h5>';
            $status .= '</span>';
        }

        return $status;
    }
}

if (!function_exists('drop_down_migration_config_document'))
{
    function drop_down_migration_config_document($status = TRUE)
    {
        $CI = &get_instance();
        $group_company_arr = array();
        $companyID = current_companyID();


        $CI->db->SELECT("documentID");
        $CI->db->from('srp_erp_migration_config');

        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->group_by("documentID");
        $group_company = $CI->db->get()->result_array();

        if ($status)
        {
            $group_company_arr = array('' => 'Select a Document');
        }
        if (isset($group_company))
        {
            foreach ($group_company as $row)
            {
                $group_company_arr[trim($row['documentID'] ?? '')] = trim($row['documentID'] ?? '');
            }
        }

        return $group_company_arr;
    }
}

if (!function_exists('all_not_accessed_employee'))
{
    function all_not_accessed_employee($companyID)
    {
        $CI = &get_instance();
        $customer_arr = array();
        /* if(!isset($companyID) || $companyID ==''){
             return $customer_arr=array();
         }*/
        $companyIDd = current_companyID();
        //$customer=  $CI->db->query("SELECT EIdNo,ECode,Ename1,Ename2,Ename3,Ename4 FROM srp_employeesdetails LEFT join srp_erp_employeenavigation  ON EIdNo = empID AND companyID={$companyID} WHERE Erp_companyID ={$companyID} AND employeeNavigationID is null ")->result_array();

        /* $customer=$CI->db->query("SELECT * FROM srp_erp_companygroupdetails INNER JOIN srp_employeesdetails ON srp_erp_companygroupdetails.companyID = srp_employeesdetails.Erp_companyID LEFT JOIN srp_erp_employeenavigation on EIdNo=empID AND srp_erp_employeenavigation.companyID={$companyID} WHERE companyGroupID = (SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}) AND employeeNavigationID is null")->result_array();*/
        $customer = FALSE;
        if ($companyID != '')
        {
            $customer = $CI->db->query("select * from srp_employeesdetails
LEFT JOIN srp_erp_employeenavigation on companyID=$companyID AND empID =EidNo
WHERE Erp_companyID=$companyIDd AND employeeNavigationID is null AND isDischarged=0")->result_array();
        }


        if ($customer)
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}

/** change function */
if (!function_exists('Drop_down_group_of_companies'))
{
    /** changed d to D */
    function Drop_down_group_of_companies($status = TRUE)
    {
        $CI = &get_instance();
        $group_company_arr = array();
        $companyID = current_companyID();
        $companyType = $CI->session->userdata("companyType");

        if ($companyType == 2)
        {
            $comp = customer_company_link();

            foreach ($comp as $val)
            {
                $company[] = $val['companyID'];
            }
            //print_r($company);exit;

            foreach ($company as $val)
            {
                //print_r($val);
                $group_company = $CI->db->query("SELECT companyID, CONCAT(company_code, ' - ', company_name) AS company FROM srp_erp_companygroupdetails INNER JOIN `srp_erp_company` ON company_id = companyID WHERE parentID = (SELECT parentID FROM srp_erp_companygroupdetails WHERE companyID = {$val}) ")->result_array();
                //print_r($group_company);
                //$group_company = get_company_accoding_to_id($val);
            }
            //exit;
        }
        else
        {
            $companyGroup = $CI->db->query("SELECT parentID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
            $group_company = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company from `srp_erp_company` where company_id={$companyID} ")->result_array();
            if (!empty($companyGroup))
            {
                $group_company_arr = array();
                $group_company = $CI->db->query("SELECT companyID, CONCAT(company_code, ' - ', company_name) AS company FROM srp_erp_companygroupdetails INNER JOIN `srp_erp_company` ON company_id = companyID WHERE parentID = (SELECT parentID FROM srp_erp_companygroupdetails WHERE companyID = {$companyID}) ")->result_array();
            }
        }

        if ($status)
        {
            $group_company_arr = array('' => 'Select a Company');
        }
        if (isset($group_company))
        {
            foreach ($group_company as $row)
            {
                $group_company_arr[trim($row['companyID'] ?? '')] = trim($row['company'] ?? '');
            }
        }

        return $group_company_arr;
    }
}


if (!function_exists('erp_navigation_usergroups'))
{
    function erp_navigation_usergroups()
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT userGroupID,description FROM `srp_erp_usergroups` where isActive=1  AND companyID = {$companyID}")->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['userGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

/*journal Entry Action*/

if (!function_exists('load_journal_voucher_actions')) {
    function load_journal_voucher_actions($JVMasterAutoId, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $JVType, $confirmedByEmpID, $isSystemGenerated) {
        $CI = &get_instance();
        $CI->load->library('session');

        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        if ($JVType == 'Recurring') {
            $action .= '<li>
                            <a href="#" onclick=\'recurring_attachment_modal(' . $JVMasterAutoId . ',"Journal Voucher","JV",' . $confirmedYN . ');\' >
                                <span class="glyphicon glyphicon-paperclip" title="Attachment" rel="tooltip"></span> Attachments
                            </a>
                        </li>';
        } else {
            $action .= '<li>
                            <a href="#" onclick=\'attachment_modal(' . $JVMasterAutoId . ',"Journal Voucher","JV",' . $confirmedYN . ');\' >
                                <span class="glyphicon glyphicon-paperclip" title="Attachment" rel="tooltip"></span> Attachments
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="templateClone(' . $JVMasterAutoId . ',\'Journal Voucher\',\'JV\',' . $confirmedYN . ')" >
                                <span class="fa fa-copy" title="Clone" rel="tooltip"></span> Clone
                            </a>
                        </li>';
        }

        if ($isDeleted == 1) {
            $action .= '<li>
                            <a href="#" onclick="reOpen_contract(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');" >
                                <span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" title="Re Open" rel="tooltip"></span> Re Open
                            </a>
                        </li>';
        }

        if ($isSystemGenerated != 1) {
            if ($confirmedYN != 1 && $isDeleted == 0) {
                $action .= '<li>
                                <a href="#" onclick=\'fetchPage("system/finance/journal_entry_new",' . $JVMasterAutoId . ',"Edit Journal Voucher","Journal Voucher");\'>
                                    <span class="glyphicon glyphicon-pencil" title="Edit" rel="tooltip"></span> Edit
                                </a>
                            </li>';
            }
        } else {
            $action .= '<li>
                            <a href="#" onclick=\'issystemgenerateddoc();\'>
                                <span class="glyphicon glyphicon-pencil" title="Edit" rel="tooltip"></span> Edit
                            </a>
                        </li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approvedYN == 0 && $confirmedYN == 1 && $isDeleted == 0) {
            $action .= '<li>
                            <a href="#" onclick="referback_journal_entry(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');" >
                                <span class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);" title="Refer Back" rel="tooltip"></span> Refer Back
                            </a>
                        </li>';
        }

        $action .= '<li>
                        <a target="_blank" href="#" onclick="documentPageView_modal(\'JV\',\'' . $JVMasterAutoId . '\')" >
                            <span class="glyphicon glyphicon-eye-open" title="View" rel="tooltip"></span> View
                        </a>
                    </li>';

        $action .= '<li>
                        <a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $JVMasterAutoId . '" >
                            <span class="glyphicon glyphicon-print" title="Print" rel="tooltip"></span> Print
                        </a>
                    </li>';
        if ($confirmedYN != 1 && $isDeleted == 0) {
            $action .= '<li>
                            <a href="#" onclick="delete_journal_entry(' . $JVMasterAutoId . ',\'Invoices\');" >
                                <span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);" title="Delete" rel="tooltip"></span> Delete
                            </a>
                        </li>';
        }

        $action .= '</ul></div>';

        return $action;
    }
}


if (!function_exists('journal_entry_action'))
{
    function journal_entry_action($JVMasterAutoId, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $JVType, $confirmedByEmpID, $isSystemGenerated)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right d-flex">';
        if ($JVType == 'Recurring')
        {
            $status .= '<a onclick=\'recurring_attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;
             <a onclick="templateClone(' . $JVMasterAutoId . ',\'Journal Entry\',\'JV\',' . $confirmedYN . ')">
             <span title="Clone" rel="tooltip" class="fa fa-copy" aria-hidden="true"></span></a>&nbsp;&nbsp;';
        }
        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }

        if ($isSystemGenerated != 1)
        {
            if ($confirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '<a onclick=\'fetchPage("system/finance/journal_entry_new",' . $JVMasterAutoId . ',"Edit Journal Entry","Journal Entry"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
            }
        }
        else
        {
            $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }


        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_journal_entry(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'JV\',\'' . $JVMasterAutoId . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        $status .= '<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $JVMasterAutoId . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;<a onclick="delete_journal_entry(' . $JVMasterAutoId . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('company_PL_account_drop'))
{
    function company_PL_account_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID<>', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('company_PL_account_drop'))
{
    function company_PL_account_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('masterAccountYN', 0);
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID<>', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $bank = $CI->db->get()->result_array();
        $bank_arr = array('' => 'Select GL Account');
        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $bank_arr;
    }
}

if (!function_exists('company_groupstatus')) {
    function company_groupstatus($autoID, $status, $description = null) {
        $checked = ($status == 1) ? 'checked' : '';
        $isDisable = '';
        
        $checkbox = '<div class="btn-group" style="margin-left: 20px;">
                        <input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onchange="changeStatus(' . $autoID . ')" 
                        data-size="mini" data-on-text="Check" data-handle-width="25" data-off-color="danger" data-on-color="success" 
                        data-off-text="NO" data-label-width="0" ' . $checked . ' ' . $isDisable . '> Check
                    </div>';
        
        if ($status == 1) {
            return '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                            <li>' . $checkbox . '</li>
                            <li><a onclick="openAddWidgetModel(' . $autoID . ')">
                                <i class="glyphicon glyphicon-plus" style="color:#4caf50;"></i> Add Widget</a></li>
                            <li><a onclick="editUserGroup(' . $autoID . ')">
                                <i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>
                            <li><a onclick="deleteUserGroup(' . $autoID . ')">
                                <i class="text-red glyphicon glyphicon-trash"></i> Delete</a></li>
                        </ul>
                    </div>';
        } else {
            return '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">
                            <li>' . $checkbox . '</li>
                            <li><a onclick="editUserGroup(' . $autoID . ')">
                                <i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>
                            <li><a onclick="deleteUserGroup(' . $autoID . ')">
                                <i class="text-red glyphicon glyphicon-trash"></i> Delete</a></li>
                        </ul>
                    </div>';
        }
    }
}

if (!function_exists('drill_down_navigation_dropdown'))
{
    function drill_down_navigation_dropdown()
    {
        $CI = &get_instance();
        $group_company_arr = array();
        $companyID = current_companyID();
        $userID = current_userID();
        $isGroupUser = $CI->common_data['isGroupUser'];
        if ($isGroupUser == 1)
        {
            $db2 = $CI->load->database('db2', TRUE);
            $db2->select('EidNo');
            $db2->where("companyID", $companyID);
            $db2->where("empID", $userID);
            $groupdetails = $db2->get("groupusercompanies")->row_array();
            $idno = $groupdetails['EidNo'];
            $group_company = $db2->query("select companyID as companyID, empID as eid,                             
                                CONCAT(srp_erp_company.company_code,' | ',srp_erp_company.company_name) as company
                                FROM groupusercompanies
                                LEFT JOIN srp_erp_company ON srp_erp_company.company_id=groupusercompanies.companyID 
                                WHERE EidNo={$idno} ")->result_array();
            foreach ($group_company as $row)
            {
                $group_company_arr[trim($row['companyID'] ?? '') . '-1-1-' . $row['eid']] = trim($row['company'] ?? '');
            }
        }
        else
        {
            /*if there is no company*/
            $group_company = $CI->db->query("SELECT company_id as companyID,CONCAT(company_code, ' - ', company_name) AS company 
                                    FROM srp_erp_employeenavigation 
                                    INNER JOIN srp_erp_company on company_id=companyID 
                                    INNER JOIN srp_erp_usergroups on srp_erp_employeenavigation.userGroupID=srp_erp_usergroups.userGroupID 
                                    WHERE empID = {$userID} AND isActive=1")->result_array();
            if (!empty($group_company))
            {
                $CI->load->model('session_model');
                foreach ($group_company as $row)
                {
                    $gr_company_id = trim($row['companyID'] ?? '');
                    $subscription_Exp = $CI->session_model->check_subscription_status($gr_company_id, 1);
                    if ($subscription_Exp[0] == 's')
                    {
                        $group_company_arr[$gr_company_id . '-1-0-' . $userID] = trim($row['company'] ?? '');
                    }
                    elseif ($subscription_Exp['error_type'] == 'expired')
                    {
                        $group_company_arr[$gr_company_id . '-1-0-' . $userID] = trim($row['company'] ?? '');
                    }
                }
            }
            else
            {
                $group_company = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company 
                                    from srp_erp_company where company_id={$companyID} ")->row_array();
                // dropdown session company
                $group_company_arr[trim($group_company['companyID'] ?? '') . '-1-0-' . $userID] = trim($group_company['company'] ?? '');
            }
            //add group to company
            $group_company = $CI->db->query("SELECT grMas.companyGroupID as companyID, grMas.description AS company 
                                     FROM srp_erp_companysubgroupemployees AS subGrpEmp
                                     INNER JOIN srp_erp_companysubgroupmaster AS subGrp ON subGrpEmp.companySubGroupID=subGrp.companySubGroupID 
                                     INNER JOIN srp_erp_companygroupmaster AS grMas ON grMas.companyGroupID=subGrp.companyGroupID 
                                     WHERE subGrpEmp.EmpID = {$userID} GROUP BY grMas.companyGroupID")->result_array();
            if (!empty($group_company) && $isGroupUser == 0)
            {
                foreach ($group_company as $row)
                {
                    $group_company_arr[trim($row['companyID'] ?? '') . '-2-0-' . $userID] = trim($row['company'] ?? '');
                }
            }
        }
        return $group_company_arr;
    }
}

if (!function_exists('approval_change_modal'))
{ /**/
    function approval_change_modal($pocode, $poID, $ApprovedID, $Level, $approved, $documentID, $isRejected = 0)
    {
        $status = '';
        if ($approved == 0)
        {
            if ($isRejected == 0)
            {
                $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $documentID . '"); \'>' . $pocode . '</a>';
            }
            else
            {
                $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
            }
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('approval_change_modal_buyback'))
{ /**/
    function approval_change_modal_buyback($pocode, $poID, $ApprovedID, $Level, $approved, $documentID, $buy, $isRejected = 0)
    {
        $status = '';
        if ($approved == 0)
        {
            if ($isRejected == 0)
            {
                $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
            }
            else
            {
                $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '","' . $buy . '"); \'>' . $pocode . '</a>';
            }
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '","' . $buy . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}


if (!function_exists('approval_change_modal_treasury'))
{ /**/
    function approval_change_modal_treasury($pocode, $bankRecAutoID, $bankGLautoID, $ApprovedID, $Level, $approved, $documentID)
    {
        $status = '';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $bankRecAutoID . '","' . $bankGLautoID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $bankRecAutoID . '","' . $bankGLautoID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('approval_change_modal'))
{ /**/
    function approval_change_modal($pocode, $poID, $ApprovedID, $Level, $approved, $documentID)
    {
        $status = '';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'>' . $pocode . '</a>';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '", "' . $poID . '"); \'>' . $pocode . '</a>';
        }

        return $status;
    }
}

if (!function_exists('liabilityGL_drop'))
{
    function liabilityGL_drop($asResult = null)
    {

        $CI = &get_instance();

        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        //$CI->db->WHERE('controllAccountYN ', 0);
        $CI->db->WHERE('accountCategoryTypeID != 4');
        $CI->db->WHERE('isBank', 0);
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->ORDER_BY('GLSecondaryCode');
        $data = $CI->db->get()->result_array();

        //echo $CI->db->last_query();
        /*$data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }*/

        if ($asResult != null)
        {
            $data_arr = array('' => '');
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
                }
            }
            return $data_arr;
        }
        else
        {
            return $data;
        }

        return $data;
    }
}

if (!function_exists('fetch_widget_template'))
{ //get default templates
    function fetch_widget_template()
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_dashboardtemplate');
        $CI->db->WHERE('isDefault', 0);
        $data = $CI->db->get()->result_array();

        return $data;
    }
}

if (!function_exists('fetch_currency_code'))
{
    function fetch_currency_code($currencyID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("CurrencyCode");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('currencyID', $currencyID);

        return $CI->db->get()->row('CurrencyCode');
    }
}

if (!function_exists('edit_loantreasury')) {
    function edit_loantreasury($itemAutoID, $receiptVoucherID, $type) {
        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'fetchPage("system/bank_rec/erp_loan_mgt_new","' . $itemAutoID . '","Loan Management");\'><i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>';
        $status .= '<li><a onclick="delete_loan(' . $itemAutoID . ');"><i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';

        if ($type == 3) {
            if ($receiptVoucherID) {
                $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $receiptVoucherID . '\')" ><i class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></i> View</a></li>';
            } else {
                $status .= '<li><a onclick="open_receipt_voucher_modal(' . $itemAutoID . ');"><i class="glyphicon glyphicon-list-alt" style="color:rgb(65, 122, 211);"></i> Create Receipt Voucher</a></li>';
            }
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('mfq_rfq_year'))
{
    function mfq_rfq_year($date, $type)
    {
        $status = "";
        $date_arr = explode('-', $date);
        if ($type == 1)
        {
            $status = '<span style="width:50px" class="pull-right">' . $date_arr[0] . '';

            $status .= '</span>';
        }
        else
        {
            $status = '<span style="width:50px" class="pull-right">' . $date_arr[1] . '';

            $status .= '</span>';
        }

        return $status;
    }
}

if (!function_exists('mfq_rfq_job_crew'))
{
    function mfq_rfq_job_crew($job_id)
    {

        $CI = &get_instance();
        $CI->db->select("SUM(hoursSpent) as total");
        $CI->db->from('srp_erp_mfq_workprocesscrew');
        $CI->db->where('srp_erp_mfq_workprocesscrew.workProcessID', $job_id);
        $CI->db->where('srp_erp_mfq_workprocesscrew.companyID', $CI->common_data['company_data']['company_id']);
        $jobState = $CI->db->get()->row_array();

        $status = "";

        $status = '<span style="width:50px" class="pull-right">' . $jobState['total'] . '';

        $status .= '</span>';

        return $status;
    }
}



if (!function_exists('mfq_rfq_load_job_process'))
{
    function mfq_rfq_load_job_process($job_id, $type, $remark)
    {

        $CI = &get_instance();
        $CI->db->select("stage_id,stage_progress,stage_remarks");
        $CI->db->from('srp_erp_mfq_job_wise_stage');
        $CI->db->where('srp_erp_mfq_job_wise_stage.job_id', $job_id);
        $CI->db->where('srp_erp_mfq_job_wise_stage.company_id', $CI->common_data['company_data']['company_id']);
        $jobState = $CI->db->get()->result_array();

        $status = "";
        if (count($jobState) > 0)
        {

            foreach ($jobState as $stage)
            {

                if ($stage['stage_id'] == $type && $remark == 0)
                {

                    $status = '<span style="width:50px" class="pull-right">' . job_status($stage['stage_progress']) . '';

                    $status .= '</span>';
                    return $status;
                }
                else if ($stage['stage_id'] == $type && $remark == 1)
                {

                    $status = '<span style="width:50px" class="pull-right">' . $stage['stage_remarks'] . '';

                    $status .= '</span>';
                    return $status;
                }
            }
        }
    }
}

if (!function_exists('mfq_rfq_delayed'))
{
    function mfq_rfq_delayed($date1, $date2)
    {
        $status = "";
        $days = (strtotime($date1) - strtotime($date2)) / (60 * 60 * 24);

        $status = '<span style="width:50px" class="pull-right">' . floor($days) . '';

        $status .= '</span>';

        return $status;
    }
}



if (!function_exists('fetch_industryTypes'))
{
    function fetch_industryTypes()
    {
        $CI = &get_instance();
        $CI->db->SELECT("industrytypeID,industryTypeDescription");
        $CI->db->FROM('srp_erp_industrytypes');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Industry');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['industrytypeID'] ?? '') . ' | ' . trim($row['industryTypeDescription'] ?? '')] = trim($row['industryTypeDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('erp_bank_facilityStatus'))
{
    function erp_bank_facilityStatus()
    {
        $CI = &get_instance();
        $data = $CI->db->query("select statusID,description from srp_erp_bankfacilitystatus")->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['statusID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('erp_bankfacilityrateType'))
{
    function erp_bankfacilityrateType()
    {
        $CI = &get_instance();
        $data = $CI->db->query("select ratetypeID,ratetypeName from `srp_erp_bankfacilityratetype` ")->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['ratetypeID'] ?? '')] = trim($row['ratetypeName'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('number_months'))
{
    function number_months($date1, $date2)
    {
        $diff = abs(strtotime($date2) - strtotime($date1));
        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = ceil(($diff - ($years * 365 * 60 * 60 * 24)) / ((365 * 60 * 60 * 24) / 12));

        return $months;
    }
}


if (!function_exists('current_companyName'))
{
    function current_companyName($nameonly = false)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        if ($nameonly)
        {
            $data = $CI->db->query("select company_id as companyID,company_name as company from `srp_erp_company` where company_id={$companyID} ")->row_array();
        }
        else
        {
            $data = $CI->db->query("select company_id as companyID,CONCAT(company_code,' | ',company_name) as company from `srp_erp_company` where company_id={$companyID} ")->row_array();
        }


        return $data['company'];
    }
}

if (!function_exists('leavemaster_dropdown'))
{
    function leavemaster_dropdown($policyID = FALSE, $all = FALSE)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $where = '';
        if ($policyID)
        {
            $where = " AND policyID={$policyID}";
        }
        if ($all)
        {
            $leavetype_arr = array('' => $CI->lang->line('common_all')/*'All'*/);
        }
        else
        {
            $leavetype_arr = array('' => $CI->lang->line('common_select')/*'Select'*/);
        }


        $leavetype = $CI->db->query("SELECT leaveTypeID, description FROM srp_erp_leavetype  WHERE companyID={$companyID} AND typeConfirmed=1  $where ")->result_array();
        if (!empty($leavetype))
        {
            foreach ($leavetype as $row)
            {
                $leavetype_arr[trim($row['leaveTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $leavetype_arr;
    }
}

if (!function_exists('payroll_dropdown'))
{
    function payroll_dropdown($isNonPayroll = NULL)
    {

        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("payrollMasterID,concat(MONTHNAME(STR_TO_DATE(payrollMonth, '%m')) ,' - ',payrollYear,' | ',narration) as month");
        $CI->db->from($tableName);
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('approvedYN', 1);

        $data = $CI->db->get()->result_array();
        $payroll_arr = array('' => $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $payroll_arr[trim($row['payrollMasterID'] ?? '')] = trim($row['month'] ?? '');
            }
        }

        return $payroll_arr;
    }
}


if (!function_exists('segment_employee_drop'))
{
    function segment_employee_drop($segment)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $customer = $CI->db->query("SELECT * FROM srp_employeesdetails WHERE Erp_companyID ={$companyID}  AND segmentID={$segment}")->result_array();
        $customer_arr = array('' => 'Select Employee');
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                /*$customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename1'] ?? '') . ' ' . trim($row['Ename2'] ?? '') . ' ' . trim($row['Ename3'] ?? '') . ' ' . trim($row['Ename4'] ?? '');*/
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('fetch_employeeNo'))
{
    function fetch_employeeNo($employeeNo)
    {
        $CI = &get_instance();
        $CI->db->select('EIdNo,ECode,Ename2,EcMobile,Nid,EEmail');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', $employeeNo);
        $data = $CI->db->get()->row_array();

        return $data;
    }
}

if (!function_exists('leaveAction'))
{
    function leaveAction($id, $type, $dec)
    {
        switch ($type)
        {
            case 'hourly':
                if ($id == 0)
                {
                    $status = 'Daily';
                }
                else if ($id == 1)
                {
                    $status = 'Monthly';
                }
                else
                {
                    $status = 'Annually';
                };
                break;
            case 'default':
                $status = ($id == 0 ? '<span class="label label-danger">&nbsp;</span>' : '<span class="label label-success">&nbsp;</span>');
                break;
            case 'ID':
                $CI = &get_instance();
                $set = $CI->db->query("SELECT * FROM srp_employeesdetails WHERE leaveGroupID=$id")->row_array();
                $status = '<span class="pull-right">';
                $status .= '<a onclick="fetchPage(\'system/hrm/new_leave_group\',' . $id . ',\'Add Leave group\',\'Leave Group\');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; ';

                if (empty($set))
                {
                    $status .= '|&nbsp;&nbsp; <a onclick="deleteLeave(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:#d15b47;"></span></a>';
                }
                break;
            default:
                '';
        }

        $status .= ' &nbsp; | &nbsp; <span title="Employees" rel="tooltip" onclick="load_leaveGrpEmployees(' . $id . ', \'' . $dec . '\')" class="glyphicon glyphicon-user"></span>';
        $status .= '&nbsp;&nbsp;</span>';
        return $status;
    }
}

if (!function_exists('monthlyleavegroup_drop'))
{
    function monthlyleavegroup_drop($type = 1)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $customer = $CI->db->query("SELECT *  FROM srp_erp_leavegroup WHERE companyID={$companyID} ")->result_array();
        $customer_arr = array('' => 'Select leave group');
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['leaveGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $customer_arr;
    }
}


if (!function_exists('leaveGroup_drop'))
{
    function leaveGroup_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT leaveGroupID,description FROM `srp_erp_leavegroup` WHERE leaveGroupID=(select leaveGroupID from `srp_erp_leavegroupdetails` WHERE leaveGroupID=srp_erp_leavegroup.leaveGroupID group by leaveGroupID) AND companyID={$companyID}")->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_leave_group')/*'Select Leave Group'*/);
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['leaveGroupID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('accrualAction'))
{
    function accrualAction($id, $confirmYN)
    {
        $status = '<span style="max-width:70px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_accrual","' . $id . '","Leave Accrual"); \'>';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($confirmYN != 1)
        {
            $status .= '&nbsp;|&nbsp;<a onclick=\'delete_accrual(' . $id . '); \'>';
            $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color: #d15b47"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_all_customers'))
{
    function get_all_customers()/*get all Customers*/
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $customer = $CI->db->get()->result_array();

        return $customer;
    }
}


if (!function_exists('format_date_dob'))
{
    function format_date_dob($date = NULL)
    {
        if (isset($date))
        {
            if (!empty($date))
            {
                return date('dS F Y (l)', strtotime($date));
            }
        }
        else
        {
            return date('dS F Y (l)', time());
        }
    }
}



if (!function_exists('covertToMysqlDate'))
{
    function covertToMysqlDate($date = null)
    {
        return (!empty($date)) ? input_format_date($date, date_format_policy()) : '';
    }
}

if (!function_exists('all_priority_new_drop'))
{
    function all_priority_new_drop($status = TRUE)/*Load all currency*/
    {
        $CI = &get_instance();
        $CI->db->select("priorityID,priorityDescription");
        $CI->db->from('srp_erp_priority_master');
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Priority');
        if (isset($prio))
        {
            foreach ($prio as $row)
            {
                $priority_arr[trim($row['priorityID'] ?? '')] = trim($row['priorityDescription'] ?? '');
            }
        }

        return $priority_arr;
    }
}


if (!function_exists('format_date_mysql_datetime'))
{
    function format_date_mysql_datetime($date = NULL)
    {
        if (isset($date))
        {
            if (!empty($date))
            {
                return date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $date)));
            }
        }
        else
        {
            return date('Y-m-d H:i:s', time());
        }
    }
}

if (!function_exists('format_date_getYear'))
{
    function format_date_getYear($date = NULL)
    {
        if (isset($date))
        {
            return date('Y', strtotime($date));
        }
        else
        {
            return date('Y', time());
        }
    }
}

if (!function_exists('operand_arr'))
{
    function operand_arr()
    {
        return array('+', '*', '/', '-', '(', ')');
    }
}

if (!function_exists('leaveAdjustmentAction'))
{
    function leaveAdjustmentAction($id, $confirmYN)
    {
        $status = '<span style="width:50px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_adjustment","' . $id . '","Leave Accrual"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($confirmYN == 0)
        {
            $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a onclick="delete_master(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('amount_percentage'))
{ /*calculate percentage for total amount*/
    function amount_percentage($totalamount, $amount)
    {

        $perecentage = 0;

        if ($totalamount && $amount != 0)
        {
            $perecentage = ($amount / $totalamount) * 100;
        }

        return round($perecentage, 2);
    }
}

if (!function_exists('tax_groupMaster'))
{
    function tax_groupMaster($id)
    {
        if ($id == 1)
        {
            return 'Outputs (Sales etc)';
        }
        else if ($id == 2)
        {
            return 'Inputs (Purchases, imports etc)';
        }

        return '';
    }
}

if (!function_exists('customer_tax_groupMaster'))
{
    function customer_tax_groupMaster()
    {
        $CI = &get_instance();
        $CI->db->select("taxGroupID,Description");
        $CI->db->from('srp_erp_taxgroup');
        $CI->db->where('taxType', 1);
        $CI->db->where('companyID', current_companyID());
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Tax Group');
        if (isset($prio))
        {
            foreach ($prio as $row)
            {
                $priority_arr[trim($row['taxGroupID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }

        return $priority_arr;
    }
}

if (!function_exists('supplier_tax_groupMaster'))
{
    function supplier_tax_groupMaster()
    {
        $CI = &get_instance();
        $CI->db->select("taxGroupID,Description");
        $CI->db->from('srp_erp_taxgroup');
        $CI->db->where('taxType', 2);
        $CI->db->where('companyID', current_companyID());
        $prio = $CI->db->get()->result_array();
        $priority_arr = array('' => 'Select Tax Group');
        if (isset($prio))
        {
            foreach ($prio as $row)
            {
                $priority_arr[trim($row['taxGroupID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }

        return $priority_arr;
    }
}

if (!function_exists('array_group_by'))
{
    /**
     * Groups an array by a given key.
     *
     * Groups an array into arrays by a given key, or set of keys, shared between all array members.
     *
     * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
     * This variant allows $key to be closures.
     *
     * @param array $array The array to have grouping performed on.
     * @param mixed $key,... The key to group or split by. Can be a _string_,
     *                       an _integer_, a _float_, or a _callable_.
     *
     *                       If the key is a callback, it must return
     *                       a valid key from the array.
     *
     *                       ```
     *                       string|int callback ( mixed $item )
     *                       ```
     *
     * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
     */
    function array_group_by(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key))
        {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);

            return NULL;
        }
        $func = (is_callable($key) ? $key : NULL);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value)
        {
            if (is_callable($func))
            {
                $key = call_user_func($func, $value);
            }
            elseif (is_object($value) && isset($value->{$_key}))
            {
                $key = $value->{$_key};
            }
            elseif (isset($value[$_key]))
            {
                $key = $value[$_key];
            }
            else
            {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2)
        {
            $args = func_get_args();
            foreach ($grouped as $key => $value)
            {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }

        return $grouped;
    }
}

if (!function_exists('date_format_policy'))
{ //get company date format user defined
    function date_format_policy()
    {
        $CI = &get_instance();

        return $CI->common_data['company_policy']['DF']['All'][0]["policyvalue"] ?? '';
    }
}

if (!function_exists('convert_date_format'))
{ //convert to php date format
    function convert_date_format($date = NULL)
    {
        if ($date)
        {
            $date_format_policy = date_format_policy();
            $text = str_replace('yyyy', 'Y', $date_format_policy);
            $text = str_replace('mm', 'm', $text);
            $text = str_replace('dd', 'd', $text);

            return format_date($date, $text);
        }
        else
        {
            $date_format_policy = date_format_policy();
            $text = str_replace('yyyy', 'Y', $date_format_policy);
            $text = str_replace('mm', 'm', $text);
            $text = str_replace('dd', 'd', $text);

            return $text;
        }
    }
}

if (!function_exists('convert_date_format_sql'))
{ //convert to php date format
    function convert_date_format_sql()
    {
        $date_format_policy = date_format_policy();
        $text = str_replace('yyyy', '%Y', $date_format_policy);
        $text = str_replace('mm', '%m', $text);
        $text = str_replace('dd', '%d', $text);

        return $text;
    }
}

if (!function_exists('input_format_date'))
{ //format company date policy to mysql format
    function input_format_date($date, $format, $defaultFormat = 'Y-m-d')
    {
        if (!is_null($date))
        {
            switch ($format)
            {
                case "mm-dd-yyyy":
                    $date = str_replace('-', '/', $date);

                    return date($defaultFormat, strtotime($date));
                    break;
                case "dd-mm-yyyy":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "yyyy-mm-dd":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "mm/dd/yyyy":
                    return date($defaultFormat, strtotime($date));
                    break;
                case "dd/mm/yyyy":
                    $date = str_replace('/', '-', $date);

                    return date($defaultFormat, strtotime($date));
                    break;
                case "yyyy/mm/dd":
                    return date($defaultFormat, strtotime($date));
                    break;
                default:
                    return date($defaultFormat, strtotime($date));
            }
        }
        else
        {
            return '';
        }
    }
}

if (!function_exists('input_format_date_php'))
{ //format company date policy to PHP(resolve 2k38) format
    function input_format_date_php($date, $format, $defaultFormat = 'Y-m-d')
    {
        if (!is_null($date))
        {
            switch ($format)
            {
                case "mm-dd-yyyy":
                    $date = str_replace('-', '/', $date);
                    $date_obj = new DateTime($date);
                    return $date_obj->format('Y-m-d');
                    break;
                case "dd-mm-yyyy":
                case "yyyy-mm-dd":
                case "mm/dd/yyyy":
                case "yyyy/mm/dd":
                    $date_obj = new DateTime($date);
                    return $date_obj->format('Y-m-d');
                    break;
                case "dd/mm/yyyy":
                    $date = str_replace('/', '-', $date);
                    $date_obj = new DateTime($date);
                    return $date_obj->format('Y-m-d');
                    break;
                default:
                    $date_obj = new DateTime($date);
                    return $date_obj->format('Y-m-d');
            }
        }
        else
        {
            return '';
        }
    }
}

if (!function_exists('current_format_date'))
{ //convert to php date format
    function current_format_date()
    {
        $date_format_policy = date_format_policy();  //get comany policy date
        $current_date = convert_date_format(current_date(FALSE));

        return $current_date;
    }
}

if (!function_exists('getDefaultPayroll'))
{
    function getDefaultPayroll($code, $isNonPayroll = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $filter = ($isNonPayroll != 'Y') ? '1' : '2';

        $result = $CI->db->query("SELECT salaryCategoryID, GLCode FROM srp_erp_pay_salarycategories AS salCat
                                  JOIN srp_erp_defaultpayrollcategories AS defaultTB ON defaultTB.id = salCat.payrollCatID
                                  WHERE companyID={$companyID} AND defaultTB.code='{$code}' AND isPayrollCategory={$filter} ")->row_array();

        return $result;
    }
}


if (!function_exists('get_financial_from_to'))
{ //get finance begining enddate
    function get_financial_from_to($companyFinanceYearID)
    {
        $CI = &get_instance();
        $CI->db->select('beginingDate, endingDate');
        $CI->db->where('companyFinanceYearID', $companyFinanceYearID);
        $financialYear = $CI->db->get('srp_erp_companyfinanceyear')->row_array();

        return $financialYear;
    }
}


if (!function_exists('get_current_designation'))
{
    function get_current_designation()
    {
        $empID = current_userID();
        $CI = &get_instance();
        $designation = $CI->db->query("SELECT DesDescription FROM `srp_employeesdetails` LEFT JOIN `srp_designation`  on EmpDesignationId=DesignationID WHERE EidNo={$empID}")->row_array();

        return $designation['DesDescription'];
    }
}

if (!function_exists('current_warehouseID'))
{
    function current_warehouseID()
    {
        $warehouseID = get_outletID();

        /*
         * $CI =& get_instance();
         * //$session = $CI->session->userdata('ware_houseID');
        $session = $CI->common_data['ware_houseID'];
        print_r($session);
        ;*/

        /*$warehouseID = isset($CI->common_data['ware_houseID']) ? $CI->common_data['ware_houseID'] : NULL; //$CI->common_data['company_data']['wareHouseID'];*/

        return $warehouseID;
    }
}

if (!function_exists('get_calender'))
{
    function get_calender()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $year = date('Y');
        $result = $CI->db->query("SELECT ( SELECT MAX(fulldate) FROM srp_erp_calender WHERE companyID = {$companyID} ) endDate, ( SELECT MIN(fulldate) FROM srp_erp_calender WHERE companyID = {$companyID} ) AS startDate FROM `srp_erp_calender` WHERE companyID = {$companyID}")->row_array();

        return $result;
    }
}

if (!function_exists('fetchFinancePeriod'))
{ //get finance period using companyFinancePeriodID.
    function fetchFinancePeriod($companyFinancePeriodID)
    {
        $CI = &get_instance();

        $result = $CI->db->query("SELECT dateFrom,dateTo FROM srp_erp_companyfinanceperiod WHERE companyFinancePeriodID=$companyFinancePeriodID")->row_array();

        return $result;
    }
}

if (!function_exists('getDocumentMaster'))
{ //get finance period using companyFinancePeriodID.
    function getDocumentMaster()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("codeID,documentID,document");
        $CI->db->FROM('srp_erp_documentcodemaster');
        $CI->db->where('companyID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Document');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['documentID'] ?? '')] = trim($row['document'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('reportMaster_action'))
{
    function reportMaster_action($id, $reportCode)
    {
        $str = '<div align="right"><a onclick="reportDetails(' . $id . ', \'' . strtolower($reportCode) . '\')">';
        $str .= '<span class="glyphicon glyphicon-pencil" title="Edit" rel="tooltip"></span></a></div>';

        return $str;
    }
}

if (!function_exists('get_ssoReportFields'))
{
    function get_ssoReportFields($where, $reportType = NULL)
    {

        $CI = &get_instance();
        $companyID = current_companyID();

        if ($where == 'E')
        {
            $result = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  WHERE isEmployeeLevel=1 AND isFillable=1")->result_array();
        }
        else
        {
            $whereStr = '';
            if ($reportType != NULL)
            {
                $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
                $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
                $masterID = $ssoReportColumnDetails[$reportType][1];
                //$whereStr = "AND (masterTB.reportType='".$reportType."' OR masterTB.reportType IS NULL ) AND masterTB.".$shortOrderColumn." IS NOT NULL";
                $whereStr = "AND masterTB." . $shortOrderColumn . " IS NOT NULL";
            }
            $result = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, reportValue, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  LEFT JOIN srp_erp_sso_reporttemplatedetails AS det ON det.reportID=masterTB.id AND det.companyID={$companyID} AND masterID={$masterID}
                                  WHERE isCompanyLevel=1 AND isFillable=1 {$whereStr}")->result_array();
            //$q = $CI->db->last_query();
        }

        return $result;
    }
}

if (!function_exists('get_ssoReportConfigData'))
{
    function get_ssoReportConfigData($reportType)
    {
        $result_arr = array();

        $ssoData = sso_drop();
        $payGroup_arr = payGroup_drop();

        $result_arr['companyLevel'] = get_ssoReportFields('C', $reportType);
        $result_arr['SSO_arr'] = $ssoData;
        $result_arr['payGroup_arr'] = $payGroup_arr;
        $result_arr['shortOrder'] = ssoReport_shortOrder($reportType);

        return $result_arr;
    }
}

if (!function_exists('sso_drop'))
{
    function sso_drop($dropDown = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT payGroupID, ssoTB.description AS titleDes
                                FROM srp_erp_paygroupmaster AS payGroup
                                JOIN srp_erp_socialinsurancemaster AS ssoTB
                                ON ssoTB.socialInsuranceID=payGroup.socialInsuranceID AND ssoTB.companyID={$companyID}
                                WHERE payGroup.companyID={$companyID} ")->result_array();

        if ($dropDown == 'Y')
        {
            $data_arr = ['' => ''];

            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['payGroupID'] ?? '')] = trim($row['titleDes'] ?? '');
                }
            }

            return $data_arr;
        }
        else
        {
            return $data;
        }
    }
}

if (!function_exists('get_ssoEmpLevelConfig'))
{
    function get_ssoEmpLevelConfig($reportID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $result_arr = array();

        $result = $CI->db->query("SELECT EIdNo, ECode, Ename2, Ename3, initial, description, temFieldsTB.id AS fieldID, inputName, reportValue, ssoNo
                                   FROM srp_employeesdetails AS empTB
                                   JOIN srp_erp_sso_reporttemplatefields AS temFieldsTB
                                   LEFT JOIN (
                                      SELECT empID, reportID, reportValue FROM srp_erp_sso_reporttemplatedetails WHERE companyID={$companyID} AND masterID={$reportID}
                                   ) AS temDetailsTB ON temDetailsTB.reportID = temFieldsTB.id AND EIdNo=empID
                                   WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND empTB.empConfirmedYN=1
                                   AND temFieldsTB.isFillable=1 AND temFieldsTB.isEmployeeLevel=1 AND isDischarged != 1 ORDER BY EIdNo, temFieldsTB.id")->result_array();

        //AND isDischarged=0
        $lastEmpID = NULL;
        $i = 0;
        foreach ($result as $key => $row)
        {
            $empID = $row['EIdNo'];
            $inputName = $row['inputName'];

            if ($empID == $lastEmpID)
            {

                if ($inputName == 'lastName')
                {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['Ename3'] : $row['reportValue'];
                }
                else if ($inputName == 'initials')
                {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['initial'] : $row['reportValue'];
                }
                else if ($inputName == 'memNumber')
                {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['ssoNo'] : $row['reportValue'];
                }
                else
                {
                    $reportVal = $row['reportValue'];
                }

                $result_arr[$i]['columnValue'][$inputName] = $reportVal;
            }
            else
            {
                $i += ($key == 0) ? 0 : 1;
                $result_arr[$i]['empID'] = $empID;
                $result_arr[$i]['eCode'] = $row['ECode'];
                $result_arr[$i]['eName'] = $row['Ename2'];


                if ($inputName == 'lastName')
                {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['Ename3'] : $row['reportValue'];
                }
                else if ($inputName == 'initials')
                {
                    $reportVal = ($row['reportValue'] == NULL) ? $row['initial'] . ' 150 ' : '50 ' . $row['reportValue'];
                }
                else
                {

                    $reportVal = $row['reportValue'];
                }
                $result_arr[$i]['columnValue'][$inputName] = $reportVal;
            }

            $lastEmpID = $empID;
        }

        return $result_arr;
    }
}

if (!function_exists('dropdown_all_overHead_gl'))
{
    function dropdown_all_overHead_gl()
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT
  coa.GLAutoID,
  coa.systemAccountCode,
  coa.GLSecondaryCode,
  coa.GLDescription,
  coa.systemAccountCode,
  coa.subCategory
FROM
  `srp_erp_chartofaccounts` coa
WHERE
  coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.`isBank` = 0
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0")->result_array();

        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }

    if (!function_exists('get_mfq_category'))
    {
        function get_mfq_category()
        {
            $CI = &get_instance();
            $companyID = current_companyID();
            $result = $CI->db->query("SELECT * FROM `srp_erp_mfq_itemcategory` WHERE companyID={$companyID}")->result_array();

            return $result;
        }
    }
}

if (!function_exists('ssoReport_shortOrder'))
{
    function ssoReport_shortOrder($reportType)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
        $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
        $masterID = $ssoReportColumnDetails[$reportType][1];

        $data = $CI->db->query("SELECT masterTB.id, fieldName, description, isLeft_strPad,
                                COALESCE(configTB.strLength, masterTB.strLength) AS strLength,
                                COALESCE(configTB.shortOrder, masterTB.{$shortOrderColumn}) AS shortOrder
                                FROM srp_erp_sso_reporttemplatefields AS masterTB
                                LEFT JOIN (
                                    SELECT reportID, strLength, shortOrder FROM srp_erp_sso_reporttemplateconfig WHERE companyID={$companyID} AND masterID={$masterID}
                                ) AS configTB ON configTB.reportID=masterTB.id
                                WHERE masterTB.{$shortOrderColumn} IS NOT NULL
                                ORDER BY shortOrder")->result_array();

        /*$m= $CI->db->last_query();
        return array($m,$data);*/

        return $data;
    }
}


if (!function_exists('dropdown_leavepolicy'))
{
    function dropdown_leavepolicy()
    {
        $CI = &get_instance();
        $data = $CI->db->query("select policyMasterID,policyDescription from `srp_erp_leavepolicymaster` ")->result_array();

        if (isset($data))
        {
            foreach ($data as $row)
            {
                if (trim($row['policyMasterID'] ?? '') != 2)
                {
                    $data_arr[trim($row['policyMasterID'] ?? '')] = trim($row['policyDescription'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('AnnualaccrualAction'))
{
    function AnnualaccrualAction($id, $confirmYN)
    {

        $status = '<span style="max-width:70px" class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/new_leave_annually_accrual","' . $id . '","Leave Annual Accrual"); \'>';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

        if ($confirmYN != 1)
        {
            $status .= '&nbsp;|&nbsp;<a onclick=\'delete_accrual(' . $id . '); \'>';
            $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color: #d15b47"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('action_epfReport'))
{
    function action_epfReport($id, $confirmedYN)
    {
        $status = '<div class="pull-right">';

        if ($confirmedYN != 1)
        {
            $status .= '<a onclick="generate_newReport(\'' . $id . '\')"><span class="glyphicon glyphicon-pencil"></span></a>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<span class="glyphicon glyphicon-trash tbTrash" style="color: rgb(209, 91, 71);" onclick="delete_epfReport(\'' . $id . '\')"></span>';
        }
        else
        {
            $status .= '<a onclick="generate_newReport(\'' . $id . '\')"><i class="fa fa-fw fa-eye"></i></a>';
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $status .= '<i class="fa fa-file-text-o" aria-hidden="true" onclick="get_detFile(\'' . $id . '\')"></i>';
        }
        $status .= '</div>';

        return $status;
    }
}

if (!function_exists('epfReportTextbox'))
{
    function epfReportTextbox($id, $ocGrade)
    {
        $str = '<input type="text" name="ocGrade[]" class="form-control trInputs" value="' . $ocGrade . '" />';
        $str .= '<input type="hidden" name="empID[]" value="' . $id . '" />';

        return $str;
    }
}


if (!function_exists('addBtn'))
{
    function addBtn()
    {
        /*$CurrencyCode = "'".trim($CurrencyCode)."'";
        $code = "'".trim($code)."'";
        $empName = "'".trim($empName)."'";
        //$addTempTB = 'onclick="addTempTB('.$EIdNo.', '.$code.', '.$empName.', '.$CurrencyCode.', '.$DecimalPlaces.' )"';*/
        $addTempTB = 'onclick="addTempTB(this)"';

        $view = '<div class="" align="center"> <button class="btn btn-primary btn-xs" ' . $addTempTB . ' style="font-size:10px" type="button"> + Add </button> </div>';

        return $view;
    }
}




if (!function_exists('editcustomer'))
{
    function editcustomer($customerAutoID, $deletedYN)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-left">';
        if ($deletedYN != 1)
        {
            $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
            $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
            $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
            $salesreturn = $CI->db->query("select customerID from srp_erp_salesreturnmaster WHERE customerID=$customerAutoID ;")->row_array();
            $receiptmatching = $CI->db->query("select customerID from srp_erp_rvadvancematch WHERE customerID=$customerAutoID ;")->row_array();
            $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
            $deliveryorder = $CI->db->query("select customerID from srp_erp_deliveryorder WHERE customerID=$customerAutoID ;")->row_array();
            $contractmaster = $CI->db->query("select customerID from srp_erp_contractmaster WHERE customerID=$customerAutoID ;")->row_array();
            $customerinvoice_temp = $CI->db->query("select customerID from srp_erp_customerinvoicemaster_temp WHERE customerID=$customerAutoID ;")->row_array();

            if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $salesreturn || $receiptmatching || $deliveryorder || $contractmaster || $customerinvoice_temp))
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
            }
            else
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
            }
        }
        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('editcustomerotherlang'))
{
    function editcustomerotherlang($customerAutoID, $deletedYN)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        if ($deletedYN != 1)
        {
            $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
            $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
            $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
            $salesreturn = $CI->db->query("select customerID from srp_erp_salesreturnmaster WHERE customerID=$customerAutoID ;")->row_array();
            $receiptmatching = $CI->db->query("select customerID from srp_erp_rvadvancematch WHERE customerID=$customerAutoID ;")->row_array();
            $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
            if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $salesreturn || $receiptmatching))
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new_otherlang\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
            }
            else
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="fetchPage(\'system/customer/erp_customer_master_new_otherlang\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
            }
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('editcustomerBuyback'))
{
    function editcustomerBuyback($customerAutoID, $deletedYN)
    {
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        if ($deletedYN != 1)
        {
            $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
            $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
            $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
            $salesreturn = $CI->db->query("select customerID from srp_erp_salesreturnmaster WHERE customerID=$customerAutoID ;")->row_array();
            $receiptmatching = $CI->db->query("select customerID from srp_erp_rvadvancematch WHERE customerID=$customerAutoID ;")->row_array();
            $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
            $contractmaster = $CI->db->query("select customerID from srp_erp_contractmaster WHERE customerID=$customerAutoID ;")->row_array();
            $customerinvoicemaster_temp = $CI->db->query("select customerID from srp_erp_customerinvoicemaster_temp WHERE customerID=$customerAutoID ;")->row_array();
            if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $salesreturn || $receiptmatching || $contractmaster || $customerinvoicemaster_temp))
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;   <a onclick="attach_salePrice_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Sales Prices" rel="tooltip" class="fa fa-file-text-o"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;
                                                <a onclick="fetchPage(\'system/customer/erp_Customer_master_buyback\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
            }
            else
            {
                $status .= '<spsn class="pull-right"><a onclick="attachment_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;   <a onclick="attach_salePrice_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Sales Prices" rel="tooltip" class="fa fa-file-text-o"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;
                                                <a onclick="fetchPage(\'system/customer/erp_Customer_master_buyback\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
            }
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('getcustomeramount'))
{
    function getcustomeramount($customerAutoID, $customerCurrency, $customerCurrencyID)
    {
        // echo $to;
        $comid = current_companyID();
        $CI = &get_instance();
        $decimalplaces = $CI->db->query("SELECT decimalplaces FROM srp_erp_currencymaster WHERE currencyID=$customerCurrencyID ;")->row_array();
        $status = '<span class="">';
        $customeramount = $CI->db->query("SELECT sum( srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate ) as Amount FROM srp_erp_generalledger WHERE companyID = '$comid'
AND partyType = 'CUS'
AND partyAutoID = '$customerAutoID'
AND subLedgerType=3 ;")->row_array();
        $status .= '<spsn class=""><b>' . $customerCurrency . ' :</b> ' . number_format(
            $customeramount['Amount'],
            $decimalplaces['decimalplaces']
        ) . '</span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('isPayrollCategoryStr'))
{
    function isPayrollCategoryStr($isPayrollCategory)
    {
        return ($isPayrollCategory == '1') ? 'Payroll' : 'Non payroll';
    }
}
if (!function_exists('leaveApplicationEmployee'))
{
    function leaveApplicationEmployee()
    {
        $CI = &get_instance();
        $com = current_companyID();
        $CI->db->select("EIdNo, ECode,DesDescription, IFNULL(Ename2, '') AS employee,isMonthly as policyMasterID,srp_employeesdetails.leaveGroupID, DepartmentDes");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $CI->db->join('srp_erp_leavegroup', 'srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID', 'INNER');
        $CI->db->join(' (
                         SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                         JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                         WHERE departTB.Erp_companyID=' . $com . ' AND empDep.Erp_companyID=' . $com . ' AND empDep.isActive=1 AND empDep.isPrimary = 1  GROUP BY EmpID
                     ) AS departTB', 'departTB.empID_Dep=srp_employeesdetails.EIdNo', 'left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $com);
        /*   $CI->db->where('isDischarged !=', 1);*/
        $result = $CI->db->get()->result_array();

        return $result;
    }
}

if (!function_exists('fetch_my_attendees_and_reporting_self'))
{
    function fetch_my_attendees_and_reporting_self()
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $empID = current_userID();

        //fetch attendees

        $qry1 = "SELECT attendee.attendeeID as EIdNo, srp_employeesdetails.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
        IFNULL(srp_employeesdetails.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
        IFNULL(DepartmentDes, '') as department, concat(empManager.ECode,' | ',empManager.Ename2) as manager
        FROM srp_erp_employee_attendees AS attendee
        JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = attendee.attendeeID
        INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
        INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
        LEFT JOIN `srp_erp_segment`  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
        LEFT JOIN 
            (   SELECT emp1.empID,emp1.managerID,emp2.Ename2,emp1.employeeManagersID,emp2.ECode
                FROM `srp_erp_employeemanagers` as emp1
                LEFT JOIN srp_employeesdetails as emp2 ON emp1.empID = emp2.EIdNo
                WHERE emp1.active = 1 AND emp1.isPrimary=1
                GROUP BY emp1.empID 
            ) as empManager	ON attendee.attendeeID = empManager.empID
        LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
        LEFT JOIN  (
                SELECT EmpID AS empID_Dep, DepartmentDes 
                FROM srp_departmentmaster AS departTB
                JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                WHERE departTB.Erp_companyID=$companyID AND empDep.Erp_companyID=$companyID AND empDep.isActive=1 AND empDep.isPrimary = 1 
                GROUP BY EmpID 
        ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
        WHERE srp_employeesdetails.Erp_companyID=$companyID AND  attendee.companyID={$companyID} AND attendee.empID={$empID}";

        $result = $CI->db->query($qry1)->result_array();

        ///fetch reporting emp
        $qry2 = "SELECT empManager.empID as EIdNo, empManager.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
            IFNULL(empManager.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
            IFNULL(DepartmentDes, '') as department, concat(manager.ECode,' | ',manager.Ename2) as manager
            FROM srp_erp_employeemanagers AS attendee
            JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = attendee.empID
            INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
            INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
            LEFT JOIN `srp_erp_segment`  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
            LEFT JOIN 
            (   SELECT emp1.empID,emp1.managerID as mgID,emp2.Ename2,emp1.employeeManagersID,emp2.ECode
                FROM `srp_erp_employeemanagers` as emp1
                LEFT JOIN srp_employeesdetails as emp2 ON emp1.empID = emp2.EIdNo
                WHERE emp1.active = 1
                GROUP BY emp1.empID 
            ) as empManager	ON EIdNo = empManager.empID OR EIdNo = empManager.mgID 
            LEFT JOIN srp_employeesdetails manager on mgID=manager.EIdNo
            LEFT JOIN  (
                SELECT EmpID AS empID_Dep, DepartmentDes 
                FROM srp_departmentmaster AS departTB
                JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                WHERE departTB.Erp_companyID=$companyID AND empDep.Erp_companyID=$companyID AND empDep.isActive=1 AND empDep.isPrimary = 1 
                GROUP BY EmpID 
            ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
            WHERE srp_employeesdetails.Erp_companyID=$companyID AND  attendee.companyID={$companyID} AND attendee.managerID={$empID} GROUP BY empManager.ECode";

        $reporting_users = $CI->db->query($qry2)->result_array();

        $qry3 = "SELECT empManager.empID as EIdNo, empManager.ECode, srp_employeesdetails.EmpSecondaryCode, DesDescription,
                IFNULL(empManager.Ename2, '') AS employee, srp_employeesdetails.leaveGroupID,srp_employeesdetails.DateAssumed,
                IFNULL(DepartmentDes, '') as department, concat(manager.ECode,' | ',manager.Ename2) as manager
                FROM srp_employeesdetails
                INNER JOIN srp_designation on srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
                INNER JOIN srp_erp_leavegroup on srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID
                LEFT JOIN `srp_erp_segment`  on srp_erp_segment.segmentID=srp_employeesdetails.segmentID
                LEFT JOIN 
                (   SELECT emp1.empID,emp1.managerID,emp2.Ename2,emp1.employeeManagersID,emp2.ECode
                    FROM `srp_erp_employeemanagers` as emp1
                    LEFT JOIN srp_employeesdetails as emp2 ON emp1.empID = emp2.EIdNo
                    WHERE emp1.active = 1
                    GROUP BY emp1.empID 
                ) as empManager	ON EIdNo = empManager.empID OR EIdNo = empManager.managerID 
                LEFT JOIN srp_employeesdetails manager on managerID=manager.EIdNo
                LEFT JOIN  (
                    SELECT EmpID AS empID_Dep, DepartmentDes 
                    FROM srp_departmentmaster AS departTB
                    JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                    WHERE departTB.Erp_companyID=$companyID AND empDep.Erp_companyID=$companyID AND empDep.isActive=1 AND empDep.isPrimary = 1 
                    GROUP BY EmpID 
                ) AS departTB ON departTB.empID_Dep=srp_employeesdetails.EIdNo
                WHERE srp_employeesdetails.Erp_companyID=$companyID  AND srp_employeesdetails.EIdNo={$empID} GROUP BY empManager.ECode";
        $current_user = $CI->db->query($qry3)->result_array();
        $attendeeArr = array_merge($result, $reporting_users, $current_user);

        return $attendeeArr;
    }
}

if (!function_exists('workFromHomeApplicationEmployee'))
{
    function workFromHomeApplicationEmployee($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('isDischarged !=', 1);
        $CI->db->where('isActive !=', 0);
        $CI->db->where('Erp_companyID', current_companyID());

        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Employee');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $data_arr;
    }
}
/*end:  Apply fror WFH (work from home) */

if (!function_exists('all_employees'))
{
    function all_employees($isDischarged = false, $intType = '')
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $dischargeFilter = ($isDischarged) ? 'AND isDischarged !=1' : '';

        /**** if @intType parameter given sql will combine with srp_erp_system_integration_user table  ** */
        $int_column = ($intType != '') ? ", intUsr.integratedUserID" : '';
        $sql = "SELECT empTB.* {$int_column} FROM srp_employeesdetails AS empTB ";
        if ($intType != '')
        {
            $sql .= "LEFT JOIN (  
                        SELECT empID, integratedUserID FROM srp_erp_system_integration_user
                        WHERE companyID={$companyID} AND integratedSystem='{$intType}'
                     ) AS intUsr ON intUsr.empID = empTB.EIdNo ";
        }
        $sql .= "WHERE Erp_companyID={$companyID} {$dischargeFilter}";

        return $CI->db->query($sql)->result_array();
    }
}

if (!function_exists('all_employees_drop'))
{
    function all_employees_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->SELECT("EIdNo,Ename2");
        $CI->db->FROM('srp_employeesdetails');
        $CI->db->where('isDischarged !=', 1);
        $CI->db->where('isSystemAdmin !=', 1);
        $CI->db->where('Erp_companyID', current_companyID());

        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Employee');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('editcustomercategory'))
{
    function editcustomercategory($partyCategoryID)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_suppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory || $suppliercategory))
        {
            $status .= '<spsn class="pull-right"><a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        }
        else
        {
            $status .= '<spsn class="pull-right"><a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('leavetype_bygroup'))
{
    function leavetype_bygroup($leaveGroupID = FALSE)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = '';
        if ($leaveGroupID)
        {
            $data = $CI->db->query("SELECT t.leaveTypeID,t.description,isAllowminus,isCalenderDays FROM srp_erp_leavegroupdetails INNER JOIN ( SELECT * FROM `srp_erp_leavetype` ) t ON t.leaveTypeID = srp_erp_leavegroupdetails.leaveTypeID WHERE leaveGroupID = {$leaveGroupID}")->result_array();
        }

        return $data;
    }
}


if (!function_exists('party_category'))
{
    function party_category($partyType, $status = TRUE)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("select partyCategoryID,partyType,categoryDescription from `srp_erp_partycategories` where companyID=$companyID and partyType=$partyType ")->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Category');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['partyCategoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('editsuppliercategory'))
{
    function editsuppliercategory($partyCategoryID)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_suppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory || $suppliercategory))
        {
            $status .= '<spsn class="pull-right"><a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        }
        else
        {
            $status .= '<spsn class="pull-right"><a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('isReportMasterConfigured'))
{
    function isReportMasterConfigured($reportType)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $errorMsg_arr = array();

        //return 'Y'; die();

        $whereStr = '';
        if ($reportType != NULL)
        {
            $ssoReportColumnDetails = unserialize(ssoReportColumnDetails);
            $shortOrderColumn = $ssoReportColumnDetails[$reportType][0];
            $masterID = $ssoReportColumnDetails[$reportType][1];
            //$whereStr = "AND (masterTB.reportType='".$reportType."' OR masterTB.reportType IS NULL ) AND masterTB.".$shortOrderColumn." IS NOT NULL";
            $whereStr = "AND masterTB." . $shortOrderColumn . " IS NOT NULL";
        }
        $companyConf = $CI->db->query("SELECT masterTB.id, description, masterTable, inputName, reportValue, fieldName
                                  FROM srp_erp_sso_reporttemplatefields AS masterTB
                                  JOIN srp_erp_sso_reporttemplatedetails AS det ON det.reportID=masterTB.id AND det.companyID={$companyID} AND masterID={$masterID}
                                  WHERE isCompanyLevel=1 AND isFillable=1 {$whereStr}")->result_array();
        if (empty($companyConf))
        {
            array(array_push($errorMsg_arr, 'Report configuration not done'));
        }

        $empConfig = $CI->db->query("SELECT * FROM srp_erp_sso_reporttemplatedetails AS detailTB
                                     JOIN srp_erp_sso_reporttemplatefields AS fieldsTB ON fieldsTB.id = detailTB.reportID
                                     WHERE companyID={$companyID} AND isEmployeeLevel=1")->result_array();
        //echo $CI->db->last_query();

        if (empty($empConfig))
        {
            array(array_push($errorMsg_arr, 'Employee configuration not done'));
        }

        return (count($errorMsg_arr) == 0) ? 'Y' : 'N';
    }
}

if (!function_exists('isReportEmployeeConfigured'))
{
    function isReportEmployeeConfigured($reportID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $result = $CI->db->query("SELECT EIdNo, ECode, Ename2, Ename3, description, temFieldsTB.id AS fieldID, inputName, reportValue
                                   FROM srp_employeesdetails AS empTB
                                   JOIN srp_erp_sso_reporttemplatefields AS temFieldsTB
                                   JOIN (
                                      SELECT empID, reportID, reportValue FROM srp_erp_sso_reporttemplatedetails WHERE companyID={$companyID} AND masterID={$reportID}
                                   ) AS temDetailsTB ON temDetailsTB.reportID = temFieldsTB.id AND EIdNo=empID
                                   WHERE Erp_companyID={$companyID} AND isPayrollEmployee=1 AND isDischarged=0
                                   AND temFieldsTB.isFillable=1 AND temFieldsTB.isEmployeeLevel=1 ORDER BY EIdNo, temFieldsTB.id")->result_array();

        //return $result;
        return (!empty($result)) ? 'Y' : 'N';
    }
}

if (!function_exists('getsupplieramount'))
{
    function getsupplieramount($supplierAutoID, $supplierCurrency, $supplierCurrencyID)
    {
        // echo $to;
        $comid = current_companyID();
        $CI = &get_instance();
        $decimalplaces = $CI->db->query("SELECT decimalplaces FROM srp_erp_currencymaster WHERE currencyID=$supplierCurrencyID ;")->row_array();
        $status = '<span class="">';
        $supplieramount = $CI->db->query("SELECT sum( srp_erp_generalledger.transactionAmount/srp_erp_generalledger.partyExchangeRate )*-1 as Amount FROM srp_erp_generalledger WHERE companyID = '$comid'
AND partyType = 'SUP'
AND partyAutoID = '$supplierAutoID'
AND subLedgerType=2 ;")->row_array();
        $status .= '<spsn class=""><b>' . $supplierCurrency . ' :</b> ' . number_format(
            $supplieramount['Amount'],
            $decimalplaces['decimalplaces']
        ) . '</span>';

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('current_timezoneID'))
{
    function current_timezoneID()
    {
        $CI = &get_instance();
        $userID = isset($CI->common_data['timezoneID']) ? $CI->common_data['timezoneID'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('current_timezoneDescription'))
{
    function current_timezoneDescription()
    {
        $CI = &get_instance();
        $userID = isset($CI->common_data['timezoneDescription']) ? $CI->common_data['timezoneDescription'] : NULL;

        return trim($userID);
    }
}

if (!function_exists('validate_date'))
{
    function validate_date($date)
    {
        $CI = &get_instance();
        $DateFormate = convert_date_format();
        $date_format_policy = date_format_policy();
        $convertedDate = input_format_date($date, $date_format_policy);
        $d = DateTime::createFromFormat($DateFormate, $convertedDate);
        if ($convertedDate == "1970-01-01")
        {
            $CI->form_validation->set_message('validate_date', ' %s is not in correct date format');

            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}

if (!function_exists('load_sc_action')) {
    function load_sc_action($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmpID) {
        $CI = &get_instance();
        $CI->load->library('session');

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick=\'attachment_modal(' . $poID . ',"Sales Commission","SC",' . $POConfirmedYN . ');\'><i class="glyphicon glyphicon-paperclip" style="color:#4caf50;"></i> Attachment</a></li>';

        if ($isDeleted == 1) {
            $status .= '<li><a onclick="reOpen_contract(' . $poID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Re Open</a></li>';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) && $approved == 0 && $POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="referbacksc(' . $poID . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick=\'fetchPage("system/sales/sale_commission_generate",' . $poID . ',"Edit Sales Commission","SC"); \'><i class="glyphicon glyphicon-pencil" style="color:#116f5e;"></i> Edit</a></li>';
            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\')"><i class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></i> View</a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('sales/load_sc_conformation/') . '/' . $poID . '"><i class="glyphicon glyphicon-print" style="color:#607d8b;"></i> Print</a></li>';
            $status .= '<li><a onclick="delete_item(' . $poID . ',\'Sales Commission\');"><i class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></i> Delete</a></li>';
        }

        if ($POConfirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\')"><i class="glyphicon glyphicon-eye-open" style="color:#03a9f4;"></i> View</a></li>';
            $status .= '<li><a target="_blank" href="' . site_url('sales/load_sc_conformation/') . '/' . $poID . '"><i class="glyphicon glyphicon-print" style="color:#607d8b;"></i> Print</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('load_fy_action'))
{
    function load_fy_action($isClosed, $companyFYid)
    {

        $CI = &get_instance();
        $empID = current_userID();
        $is_13FinancePeriod = 0;

        $is_13Fp = getPolicyValues('13FP', 'All');
        if (isset($is_13Fp))
        {
            $is_13FinancePeriod = $is_13Fp;
        }

        $userAdmin = $CI->db->query("SELECT isSystemAdmin FROM srp_employeesdetails WHERE EIdNo =$empID")->row_array();

        $action = '<span class="pull-right">';


        if ($isClosed == 1 && $userAdmin['isSystemAdmin'] == 1)
        {
            if ($is_13FinancePeriod == 1)
            {
                $action .= '<a onclick="create_department_Financial_Period_modal(' . $companyFYid . ')"><span title="Generate department financial periods" rel="tooltip"  class="glyphicon glyphicon-cog" style="color:gray;"></span></a>&nbsp;| &nbsp';
            }
            $action .= '<a onclick="reopen_finacial_year(' . $companyFYid . ')"><span title="reopen" rel="tooltip" class="glyphicon glyphicon-repeat"></span></a> &nbsp;| &nbsp';
            $action .= '<a onclick="delete_financial_year(' . $companyFYid . ')"><span title="delete" rel="tooltip"  class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        else if ($isClosed == 0)
        {
            if ($is_13FinancePeriod == 1)
            {
                $action .= '<a onclick="create_department_Financial_Period_modal(' . $companyFYid . ')"><span title="Generate department financial periods" rel="tooltip"  class="glyphicon glyphicon-cog" style="gray;"></span></a>&nbsp;| &nbsp';
            }
            $action .= '<a onclick="openisactiveeditmodel(' . $companyFYid . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a> &nbsp;| &nbsp';
            $action .= '<a onclick="delete_financial_year(' . $companyFYid . ')"><span title="delete" rel="tooltip"  class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        else
        {
            $action .= '<a onclick="delete_financial_year(' . $companyFYid . ')"><span title="delete" rel="tooltip"  class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }
        $action .= '</span>';
        return $action;
    }
}

if (!function_exists('load_fp_action'))
{
    function load_fp_action($isClosed, $companyFPid)
    {
        $CI = &get_instance();
        $empID = current_userID();
        $userAdmin = $CI->db->query("SELECT isSystemAdmin FROM srp_employeesdetails WHERE EIdNo =$empID")->row_array();
        $action = '';
        if ($isClosed == 1 && $userAdmin['isSystemAdmin'] == 1)
        {
            $action .= '<a onclick="reopen_finacial_period(' . $companyFPid . ')"><span title="Edit" class="glyphicon glyphicon-repeat"  rel="tooltip"></a>';
        }
        else
        {
            $action .= '<span class="glyphicon glyphicon-remove-circle" style="color: #ff0000c2 "></span>';
        }
        $action .= '</span>';
        return $action;
    }
}

if (!function_exists('getPolicyValues'))
{
    function getPolicyValues($code, $documentCode)
    {
        $CI = &get_instance();
        $policyValues = null;
        $policyArr = $CI->common_data['company_policy'];

        if (array_key_exists($code, $policyArr))
        {
            if (array_key_exists($documentCode, $policyArr[$code]))
            {
                $policyValues = $policyArr[$code][$documentCode][0]["policyvalue"];
            }
        }
        return $policyValues;
    }
}

if (!function_exists('getEmployeeInApproval'))
{
    function getEmployeeInApproval($empid)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->where('documentID', 'EMP');
        $CI->db->where('employeeID', $empid);
        $CI->db->from('srp_erp_approvalusers');
        $query = $CI->db->get();
        return $query->result_array();
    }
}

if (!function_exists('sc_action_approval'))
{
    function sc_action_approval($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Commission","SC");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'SC\',\'' . $poID . '\',\' \',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('payee_drop'))
{
    function payee_drop()/*Load all payee masters*/
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data = $CI->db->query("SELECT paygroupmaster.payGroupID , payeemaster.Description
                        FROM srp_erp_payeemaster AS payeemaster
                        JOIN srp_erp_paygroupmaster AS paygroupmaster ON paygroupmaster.payeeID = payeemaster.payeeMasterID AND paygroupmaster.companyID={$companyID}
                        WHERE payeemaster.companyID={$companyID}")->result_array();

        $data_arr = array('' => 'Select Payee');

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['payGroupID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_defaultPayeeSetup'))
{
    function get_defaultPayeeSetup()/*Load all payee masters setup*/
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $result = $CI->db->query("SELECT reportValue FROM srp_erp_sso_reporttemplatedetails
                                  WHERE companyID={$companyID} AND masterID=4 ORDER BY reportID ")->result_array();

        if (!empty($result))
        {
            $data = array();
            $data['payee'] = $result[0]['reportValue'];
            $data['payGroup'] = $result[1]['reportValue'];
            $data['regNo'] = $result[2]['reportValue'];

            return $data;
        }
        else
        {
            return $result;
        }
    }
}

if (!function_exists('payGroup_drop'))
{
    function payGroup_drop($dropDown = NULL)/*Load all pay groups masters*/
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT payGroupID, description FROM srp_erp_paygroupmaster
                                WHERE companyID={$companyID} AND isGroupTotal=1 ")->result_array();

        if ($dropDown == 'Y')
        {
            $data_arr = array('' => '');

            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['payGroupID'] ?? '')] = trim($row['description'] ?? '');
                }
            }

            return $data_arr;
        }
        else
        {
            return $data;
        }
    }
}

if (!function_exists('fetch_contract_type')) {
    function fetch_contract_type($status=true)
    {
        $CI =& get_instance();
        $base_arr = array();
 
        $CI->db->select('*');
        $CI->db->from('srp_erp_contract_type');
        $CI->db->where('companyID', current_companyID());
        $results = $CI->db->get()->result_array();
 
        if ($status) {
            $base_arr = array('' => 'Select Contract Type');
        } else {
            $base_arr = [];
        }
        if (isset($results)) {
            foreach ($results as $row) {
               
                $base_arr[trim($row['contractCode'] ?? '')] = trim($row['contractName'] ?? '');
            }
        }
 
        return $base_arr;
    }
}


if (!function_exists('getPeriods'))
{
    function getPeriods($yearStart, $yearEnd)
    {
        $data = array();
        $i = 0;
        while ($i < 12)
        {
            //$monthStart = ($i == 0)? $yearStart:  date('Y-m-d', strtotime($yearStart.' + 1 month'));
            $monthStart = $yearStart;
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            $data[$i]['dateFrom'] = $monthStart;
            $data[$i]['dateTo'] = $monthEnd;

            $yearStart = date('Y-m-d', strtotime($yearStart . ' + 1 month'));
            $i++;
        }

        return $data;
    }
}

if (!function_exists('fetch_glcode_claim_category'))
{
    function fetch_glcode_claim_category($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('systemAccountCode,GLAutoID,GLDescription');
        $CI->db->from('srp_erp_chartofaccounts');
        // $CI->db->where('masterCategory', 'PL');
        $CI->db->where('controllAccountYN', 0);
        $CI->db->where('accountCategoryTypeID !=', 4);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('masterAccountYN ', 0);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_gl_code')/*'Select GL Code'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('fetch_claim_category'))
{
    function fetch_claim_category($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('expenseClaimCategoriesAutoID,glCode,claimcategoriesDescription');
        $CI->db->from('srp_erp_expenseclaimcategories');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_claim_category')/*'Select Claim Category'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['expenseClaimCategoriesAutoID'] ?? '')] = trim($row['glCode'] ?? '') . ' | ' . trim($row['claimcategoriesDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('load_subItem_notSold_report'))
{
    function load_subItem_notSold_report($itemAutoID, $warehouseAutoID)
    {
        $CI = &get_instance();
        $data = $CI->db->query("SELECT * FROM srp_erp_itemmaster_sub iSub  WHERE iSub.itemAutoID = '" . $itemAutoID . "' AND    ( ISNULL(iSub.isSold) OR iSub.isSold = '' OR iSub.isSold = 0 ) AND iSub.wareHouseAutoID = '" . $warehouseAutoID . "' ")->result_array();

        return $data;
    }
}


/** Created on 16-05-2017 */
if (!function_exists('all_customer_drop'))
{
    function all_customer_drop($status = TRUE, $IsActive = null, $resultDestination = null) /*Load all Supplier*/
    {
        $CI = &get_instance();
        $CI->db->select("customerAutoID,customerName,customerSystemCode,customerCountry");
        $CI->db->from('srp_erp_customermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('deletedYN', 0);
        if ($IsActive == 1)
        {
            $CI->db->where('isActive', 1);
        }

        $customer = $CI->db->get()->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Customer');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['customerAutoID'] ?? '')] = (trim($row['customerSystemCode'] ?? '') ? trim($row['customerSystemCode'] ?? '') . ' | ' : '') . trim($row['customerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }

            if ($resultDestination == 'vat_report')
            { //this part will display only on customer drop down in vat report.
                $customer_arr[0] = 'Other';
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('group_company_drop_without_current'))
{
    function group_company_drop_without_current()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->lang->load('common', getPrimaryLanguage());

        $data = $CI->db->query("SELECT company_id, CONCAT(company_code, ' - ', company_name) AS cName
                            FROM srp_erp_company compTB
                            JOIN (
                                SELECT companyID FROM srp_erp_companygroupdetails
                                WHERE companyGroupID = ( SELECT companyGroupID FROM srp_erp_companygroupdetails
                                WHERE companyID={$companyID})
                            ) AS grpDet ON grpDet.companyID = compTB.company_id  WHERE compTB.company_id <> {$companyID} ORDER BY company_name")->result_array();

        return $data;
    }
}

if (!function_exists('all_group_customer_drop'))
{

    function all_group_customer_drop($status = TRUE) /*Load all Supplier*/
    {

        $companies = getallsubGroupCompanies();
        $masterGroupID = getParentgroupMasterID();

        $customer = false;
        if ($companies)
        {
            $CI = &get_instance();
            $CI->db->select("groupCustomerAutoID,groupCustomerName,groupcustomerSystemCode,srp_erp_groupcustomermaster.customerCountry");
            $CI->db->from('srp_erp_groupcustomermaster');
            $CI->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID', 'INNER');
            $CI->db->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = srp_erp_groupcustomerdetails.customerMasterID', 'LEFT');
            $CI->db->where('srp_erp_groupcustomermaster.companyGroupID', $masterGroupID);
            $CI->db->where('srp_erp_customermaster.deletedYN', 0);
            $CI->db->where_in('srp_erp_groupcustomerdetails.companyID', $companies);
            $CI->db->group_by('groupCustomerAutoID');
            $customer = $CI->db->get()->result_array();
        }

        if ($status)
        {
            $customer_arr = array('' => 'Select Customer');
        }
        else
        {
            $customer_arr = [];
        }
        if (!empty($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['groupCustomerAutoID'] ?? '')] = (trim($row['groupcustomerSystemCode'] ?? '') ? trim($row['groupcustomerSystemCode'] ?? '') . ' | ' : '') . trim($row['groupCustomerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('all_sales_person_drop'))
{
    function all_sales_person_drop($status = TRUE)/*Load all Sales person*/
    {
        $CI = &get_instance();
        $CI->db->select("salesPersonID,SalesPersonName,SalesPersonCode");
        $CI->db->from('srp_erp_salespersonmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => 'Select Sales person');
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['salesPersonID'] ?? '')] = trim($row['SalesPersonCode'] ?? '') . ' | ' . trim($row['SalesPersonName'] ?? '');
            }
        }

        return $supplier_arr;
    }
}

/** Created on 18-05-2017 */
if (!function_exists('lastPayrollProcessedForEmp'))
{
    function lastPayrollProcessedForEmp($empID, $payrollType = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        if ($payrollType == 2)
        {
            $masterTB = 'srp_erp_non_payrollmaster';
            $detailTB = 'srp_erp_non_payrollheaderdetails';
        }
        else
        {
            $masterTB = 'srp_erp_payrollmaster';
            $detailTB = 'srp_erp_payrollheaderdetails';
        }
        $lastDate = $CI->db->query("SELECT MAX( STR_TO_DATE(CONCAT(payrollYear,'-',payrollMonth,'-01'), '%Y-%m-%d') ) lastDate
                                     FROM {$masterTB} AS masterTB
                                     JOIN {$detailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
                                     AND EmpID = {$empID} AND detailTB.companyID={$companyID}
                                     WHERE masterTB.companyID={$companyID}")->row('lastDate');

        return $lastDate;
    }
}

if (!function_exists('get_template_drop'))
{
    function get_template_drop($FormCatID)
    {
        $CI = &get_instance();
        $CI->db->select("TempMasterID,FormCatID,TempDes");
        $CI->db->from('srp_erp_templatemaster');
        $CI->db->where('FormCatID', $FormCatID);
        $group = $CI->db->get()->result_array();

        $tempalte_arr = [];
        if (isset($group))
        {
            foreach ($group as $row)
            {
                $tempalte_arr[trim($row['TempMasterID'] . ' | ' . $row['FormCatID'])] = trim($row['TempDes'] ?? '');
            }
        }

        return $tempalte_arr;
    }
}


if (!function_exists('get_salaryComparison'))
{
    function get_salaryComparison($type = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        if ($type == 'left')
        {
            $comparison = $CI->db->query("SELECT * FROM srp_erp_salarycomparisonsystemtable AS comparisonMaster
                                           LEFT JOIN(
                                                  SELECT formulaID,masterID,formulaStr FROM srp_erp_salarycomparisonformula WHERE companyID={$companyID}
                                           )  AS formulaTB ON formulaTB.masterID = comparisonMaster.id ")->result_array();
        }
        else
        {
            $comparison = $CI->db->query("SELECT masterID, formulaStr, description FROM srp_erp_salarycomparisonformula AS formulaTB
                                  JOIN srp_erp_salarycomparisonsystemtable AS sysTB ON sysTB.id=formulaTB.masterID
                                  WHERE companyID={$companyID}")->result_array();
        }

        return $comparison;
    }
}

if (!function_exists('formulaDecode'))
{
    function formulaDecode($formula)
    {
        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup(1);
        $operand_arr = operand_arr();
        $formulaText = '';

        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row)
        {
            if (trim($formula_row) != '')
            {
                if (in_array($formula_row, $operand_arr))
                { //validate is a operand

                    $formulaText .= '<li class="formula-li formula-operation" data-value="|' . $formula_row . '|" onclick="addSelectedClass(this)">';
                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                    $formulaText .= '<span class="formula-text-value">' . $formula_row . '</span></li>';
                }
                else
                {

                    $elementType = $formula_row[0];


                    if ($elementType == '_')
                    {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                        $formulaText .= '<li class="formula-li formula-number" data-value="_' . $num . '_" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value" style="display:none">' . $num . '</span>';
                        $formulaText .= '<input type="text" class="formula-number-text" onkeyup="updateDataValue(this)" value="' . $num . '"></li>';
                    }
                    else if ($elementType == '@')
                    {
                        /*** SSO ***/
                        $SSO_Arr = explode('@', $formula_row);
                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr)
                        {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= '<li class="formula-li" data-value="@' . $SSO_Arr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $ssoDescription . '</span></li>';
                    }
                    else if ($elementType == '#')
                    {
                        /*** Salary category ***/
                        $catArr = explode('#', $formula_row);
                        $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr)
                        {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                        $formulaText .= '<li class="formula-li" data-value="#' . $catArr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $salaryDescription . '</span></li>';
                    }
                    else if ($elementType == '~')
                    {
                        /*** Pay group ***/
                        $payGroup_Arr = explode('~', $formula_row);
                        $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $payGroup_Arr[1]);
                        $new_array = array_map(function ($k) use ($payGroup_arr)
                        {
                            return $payGroup_arr[$k];
                        }, $keys);

                        $payGrpDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                        $formulaText .= '<li class="formula-li" data-value="~' . $payGroup_Arr[1] . '" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value">' . $payGrpDescription . '</span></li>';
                    }
                    else if ($elementType == '!')
                    {
                        $monthlyADArr = explode('!', $formula_row);

                        if (trim($monthlyADArr[1] ?? '') == '0')
                        {
                            /*** Balance Payment ***/

                            $formulaText .= '<li class="formula-li" data-value="!0" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">Balance Payment</span></li>';
                        }
                        else if ($monthlyADArr[1] == 'MA' || $monthlyADArr[1] == 'MD')
                        {
                            /*** Monthly Addition or Monthly Deduction ***/

                            $description = ($monthlyADArr[1] == 'MA') ? 'Monthly Addition' : 'Monthly Deduction';

                            $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">' . $description . '</span></li>';
                        }
                        else if ($monthlyADArr[1] == 'FG')
                        {
                            $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">Basic Pay</span></li>';
                        }
                        else if ($monthlyADArr[1] == 'TW')
                        {
                            $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">Total working days</span></li>';
                        }
                        else if ($monthlyADArr[1] == 'NMD')
                        {
                            $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">Days in Month</span></li>';
                        }
                    }
                }
            }
        }
        return $formulaText;
    }
}

if (!function_exists('formulaDecode2'))
{
    function formulaDecode2($formulaData = array())
    {

        $CI = &get_instance();
        $companyID = current_companyID();
        $formula = trim($formulaData['formulaString'] ?? '');
        $formulaText = '';

        $salary_categories_arr = salary_categories(array('A', 'D'));
        $payGroup_arr = get_payGroup();
        $salaryCatID = array();
        $operand_arr = operand_arr();

        $formula_arr = explode('|', $formula); // break the formula

        foreach ($formula_arr as $formula_row)
        {

            if (trim($formula_row) != '')
            {
                if (in_array($formula_row, $operand_arr))
                { //validate is a operand

                    $formulaText .= '<li class="formula-li formula-operation" data-value="|' . $formula_row . '|" onclick="addSelectedClass(this)">';
                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                    $formulaText .= '<span class="formula-text-value">' . $formula_row . '</span></li>';
                }
                else
                {
                    $isNotCat = strpos($formula_row, '_'); // check is a amount

                    /********************************************************************************************
                     * If a amount remove '_' symbol and append in the formula
                     * if a salary category  remove '#' symbol and append in the formula
                     * else if it's a balance payment '!' because there is no MA or MD in SSO formula builder
                     ********************************************************************************************/
                    if ($isNotCat !== false)
                    {
                        $numArr = explode('_', $formula_row);
                        $num = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];

                        $formulaText .= '<li class="formula-li formula-number" data-value="_' . $num . '_" onclick="addSelectedClass(this)">';
                        $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                        $formulaText .= '<span class="formula-text-value" style="display:none">' . $num . '</span>';
                        $formulaText .= '<input type="text" class="formula-number-text" onkeyup="updateDataValue(this)" value="' . $num . '"></li>';
                    }
                    else
                    {

                        $isNotSSO = strpos($formula_row, '@');
                        /**Salary Category or SSO type**/


                        if ($isNotSSO !== false)
                        { // SSO type
                            $SSO_Arr = explode('@', $formula_row);
                            $keys = array_keys(array_column($payGroup_arr, 'payGroupID'), $SSO_Arr[1]);
                            $new_array = array_map(function ($k) use ($payGroup_arr)
                            {
                                return $payGroup_arr[$k];
                            }, $keys);

                            $ssoDescription = (!empty($new_array[0])) ? trim($new_array[0]['description']) : '';

                            $formulaText .= '<li class="formula-li" data-value="@' . $SSO_Arr[1] . '" onclick="addSelectedClass(this)">';
                            $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                            $formulaText .= '<span class="formula-text-value">' . $ssoDescription . '</span></li>';
                        }
                        else
                        {
                            $isNotSalaryCat = strpos($formula_row, '#'); //Salary Category or SSO type


                            if ($isNotSalaryCat !== false)
                            { // salary category type
                                $catArr = explode('#', $formula_row);
                                $keys = array_keys(array_column($salary_categories_arr, 'salaryCategoryID'), $catArr[1]);
                                $new_array = array_map(function ($k) use ($salary_categories_arr)
                                {
                                    return $salary_categories_arr[$k];
                                }, $keys);

                                $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['salaryDescription']) : '';

                                $formulaText .= '<li class="formula-li" data-value="#' . $catArr[1] . '" onclick="addSelectedClass(this)">';
                                $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                $formulaText .= '<span class="formula-text-value">' . $salaryDescription . '</span></li>';
                            }
                            else
                            {
                                $monthlyADArr = explode('!', $formula_row);

                                if (trim($monthlyADArr[1] ?? '') == '0')
                                {

                                    $formulaText .= '<li class="formula-li" data-value="!0" onclick="addSelectedClass(this)">';
                                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                    $formulaText .= '<span class="formula-text-value">Balance Payment</span></li>';
                                }
                                else if ($monthlyADArr[1] == 'MA' || $monthlyADArr[1] == 'MD')
                                {

                                    $description = ($monthlyADArr[1] == 'MA') ? 'Monthly Addition' : 'Monthly Deduction';

                                    $formulaText .= '<li class="formula-li" data-value="!' . $monthlyADArr[1] . '" onclick="addSelectedClass(this)">';
                                    $formulaText .= '<span class="formula-remove close" onclick="removeFormulaItem(this)"><i class="fa fa-times"></i></span>';
                                    $formulaText .= '<span class="formula-text-value">' . $description . '</span></li>';
                                }
                            }
                        }
                    }
                }
            }
        }
        return $formulaText;
    }
}

if (!function_exists('machine_type_drop'))
{
    function machine_type_drop()
    {

        $CI = &get_instance();
        $CI->db->select("machineTypeID,description");
        $CI->db->from('srp_erp_machinetype');
        $group = $CI->db->get()->result_array();

        $tempalte_arr = ['' => 'Please select'];

        if (isset($group))
        {
            foreach ($group as $row)
            {

                $tempalte_arr[trim($row['machineTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $tempalte_arr;
    }
}

if (!function_exists('edit_machine_type'))
{
    function edit_machine_type($sortOrder, $ID, $detailID)
    {
        $CI = &get_instance();
        $data = $CI->db->query("select * from srp_erp_machinedetail WHERE machineMasterID={$ID} order by sortOrder ASC")->result_array();
        if (!empty($data))
        {
            $html = '<select id="sortOrder" name="sortOrder" onchange="edit_updateSortOrder(this.value,' . $ID . ',' . $detailID . ')">';
            foreach ($data as $row)
            {
                $selected = ($sortOrder == $row['sortOrder'] ? 'selected' : '');
                $html .= '<option ' . $selected . ' value="' . $row['sortOrder'] . '">' . $row['sortOrder'] . '</option>';
            }
            $html .= '</select>';
        }

        return $html;
    }
}

if (!function_exists('machine_drop'))
{
    function machine_drop()
    {

        $CI = &get_instance();
        $CI->db->select("machineMasterID,description");
        $CI->db->from('srp_erp_machinemaster');
        $group = $CI->db->get()->result_array();

        $tempalte_arr = ['' => 'Please select'];

        if (isset($group))
        {
            foreach ($group as $row)
            {

                $tempalte_arr[trim($row['machineMasterID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $tempalte_arr;
    }
}

if (!function_exists('connection_drop'))
{
    function connection_drop()
    {

        $CI = &get_instance();
        $data = $CI->db->query(" SELECT dbYN,connectionTypeID,connectionType FROM srp_erp_machine_connection")->result_array();

        return $data;
    }
}

if (!function_exists('edit_machine_mapping'))
{
    function edit_machine_mapping($machineTypeID, $ID, $detailID)
    {
        $CI = &get_instance();
        $data = $CI->db->query("select * from srp_erp_machinetype")->result_array();
        if (!empty($data))
        {
            $html = '<select id="machineTypeID" name="machineTypeID" onchange="edit_updatemachinetype(this.value,' . $ID . ',' . $detailID . ')">';
            $html .= '<option></option>';
            foreach ($data as $row)
            {
                $selected = ($machineTypeID == $row['machineTypeID'] ? 'selected' : '');
                $html .= '<option ' . $selected . ' value="' . $row['machineTypeID'] . '">' . $row['description'] . '</option>';
            }
            $html .= '</select>';
        }

        return $html;
    }
}


if (!function_exists('getPrimaryLanguage'))
{
    function getPrimaryLanguage()
    {
        $CI = &get_instance();
        $CI->load->library('company_language');
        $idiom = $CI->company_language->getPrimaryLanguage();

        return $idiom;
    }
}


if (!function_exists('language_string_conversion'))
{
    function language_string_conversion($string, $masterID = NULL)
    {
        $outputString = strtolower(str_replace(array('-', ' ', '&', '/'), array('_', '_', '_', '_'), $masterID . '_' . trim($string)));

        return $outputString;
    }
}


if (!function_exists('getSecondaryLanguage'))
{
    function getSecondaryLanguage()
    {
        $CI = &get_instance();
        $CI->load->library('company_language');
        $idiom = $CI->company_language->getSecondaryLanguage();

        return $idiom;
    }
}

if (!function_exists('fetch_main_group'))
{
    function fetch_main_group($id = FALSE, $state = FALSE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        /*$CI->db->select('companyGroupDetailID,srp_erp_companygroupmaster.description as groupdescription');
        $CI->db->join('srp_erp_companygroupmaster',
            'srp_erp_companygroupdetails.companyGroupID = srp_erp_companygroupmaster.companyGroupID');
        $CI->db->from('srp_erp_companygroupdetails');
        $CI->db->where('srp_erp_companygroupdetails.companyGroupID', $CI->common_data['company_data']['company_id']);*/

        $CI->db->select('companyGroupID,description');
        $CI->db->from('srp_erp_companygroupmaster');


        $data = $CI->db->get()->result_array();

        $data_arr = [];

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['companyGroupID'] ?? '')] = trim($row['description'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('dropdown_subGroup'))
{
    function dropdown_subGroup($groupID = false, $all = FALSE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $CI->db->select('companySubGroupID,companyGroupID,description');
        $CI->db->from('srp_erp_companysubgroupmaster');
        if ($groupID)
        {
            $CI->db->where('companyGroupID', $groupID);
        }

        $data = $CI->db->get()->result_array();


        $data_arr = [];
        if ($all)
        {
            $data_arr = ['' => 'All'];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['companySubGroupID'] ?? '')] = trim($row['description'] ?? '');
            }

            return $data_arr;
        }
    }
}


if (!function_exists('all_not_assigned_employee_to_subGroup'))
{
    function all_not_assigned_employee_to_subGroup($GroupID)
    {
        $CI = &get_instance();
        $customer_arr = array();

        $master = $CI->db->query("select masterID FROM  srp_erp_companygroupmaster WHERE companyGroupID=$GroupID")->row_array();


        $employees = $CI->db->query("SELECT
                EIdNo,
                Ename2,
                EmpSecondaryCode,
                Erp_companyID ,
                subGroupEmpID
            FROM
                ( SELECT EIdNo, Ename2, EmpSecondaryCode, Erp_companyID FROM `srp_erp_companygroupdetails` INNER JOIN srp_employeesdetails ON srp_employeesdetails.Erp_companyID = srp_erp_companygroupdetails.companyID WHERE parentID = {$master['masterID']} ) t
                LEFT JOIN 
                (
                SELECT subGroupEmpID,EmpID FROM srp_erp_companysubgroupemployees 
                INNER JOIN 
                srp_erp_companysubgroupmaster ON srp_erp_companysubgroupemployees.companySubGroupID=srp_erp_companysubgroupmaster.companySubGroupID
                WHERE srp_erp_companysubgroupmaster.companyGroupID = $GroupID
                )x
                ON 
                t.EIdNo <> x.EmpID 
            ")->result_array();


        if ($employees)
        {
            foreach ($employees as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['EmpSecondaryCode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('payrollMonth_dropDown'))
{
    function payrollMonth_dropDown($isNonPayroll = NULL)
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT DATE_FORMAT(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') AS monthID,
                                 DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y - %M') AS monthStr
                                 FROM(
                                    SELECT payrollYear, payrollMonth, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                    FROM {$tableName} WHERE companyID={$companyID} AND approvedYN=1
                                 ) AS payrollDateTB GROUP BY payrollDate ORDER BY payrollDate DESC")->result_array();


        $payroll_arr = array('' => $CI->lang->line('common_please_select'));
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $payroll_arr[trim($row['monthID'] ?? '')] = trim($row['monthStr'] ?? '');
            }
        }

        return $payroll_arr;
    }
}

if (!function_exists('payrollMonth_dropDown_with_visible_date'))
{
    function payrollMonth_dropDown_with_visible_date($isNonPayroll = NULL)
    {
        $companyID = current_companyID();
        $tableName = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $data = $CI->db->query("SELECT DATE_FORMAT(CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') AS monthID,
                                 DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y - %M') AS monthStr
                                 FROM(
                                    SELECT payrollYear, payrollMonth, DATE_FORMAT( CONCAT(payrollYear,'-',payrollMonth,'-01') , '%Y-%m-%d') payrollDate
                                    FROM {$tableName} WHERE companyID={$companyID} AND approvedYN=1 AND visibleDate <= CURDATE()
                                 ) AS payrollDateTB GROUP BY payrollDate ORDER BY payrollDate DESC")->result_array();


        $payroll_arr = array('' => $CI->lang->line('common_please_select'));
        //$payroll_arr = array('' =>  $CI->lang->line('common_please_select')/*'Please Select'*/);
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $payroll_arr[trim($row['monthID'] ?? '')] = trim($row['monthStr'] ?? '');
            }
        }

        return $payroll_arr;
    }
}


if (!function_exists('user_group'))
{
    function user_group()/*Group */
    {
        $CI = &get_instance();
        $CI->load->library('session');

        return trim($CI->session->userdata("usergroupID"));
    }
}

if (!function_exists('fetch_employee_ec'))
{
    function fetch_employee_ec($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('EIdNo,Ename2,ECode');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isSystemAdmin !=', 1);
        $CI->db->where('isDischarged', 0);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_employee')/*'Select Employee'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['EIdNo'] ?? '') . '|' . trim($row['Ename2'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $data_arr;
    }
}

/** Created on 06-06-2017 */
if (!function_exists('isPayrollProcessedForEmpGroup'))
{
    function isPayrollProcessedForEmpGroup($empID, $payYear, $payMonth, $isNonPayroll)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $headerDetailTB = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollheaderdetails' : 'srp_erp_payrollheaderdetails';
        $payrollMaster = ($isNonPayroll == 'Y') ? 'srp_erp_non_payrollmaster' : 'srp_erp_payrollmaster';

        $lastDate = $CI->db->query("SELECT CONCAT(ECode,' - ', Ename2) AS empData
                                     FROM {$payrollMaster} AS masterTB
                                     JOIN {$headerDetailTB} AS detailTB ON detailTB.payrollMasterID = masterTB.payrollMasterID
                                     AND EmpID IN ({$empID}) AND detailTB.companyID={$companyID}
                                     WHERE masterTB.companyID={$companyID} AND payrollYear={$payYear} AND payrollMonth={$payMonth}")->result_array();

        return $lastDate;
    }
}

if (!function_exists('editcustomerGroup'))
{
    function editcustomerGroup($customerAutoID)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $customerinvoice = $CI->db->query("select customerID from srp_erp_customerinvoicemaster WHERE customerID=$customerAutoID ;")->row_array();
        $customerreceipt = $CI->db->query("select customerID from srp_erp_customerreceiptmaster WHERE customerID=$customerAutoID ;")->row_array();
        $creditnote = $CI->db->query("select customerID from srp_erp_creditnotemaster WHERE customerID=$customerAutoID ;")->row_array();
        $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$customerAutoID AND partyType = 'CUS';")->row_array();
        $status .= '<spsn class="pull-right"><a onclick="load_duplicate_customer(' . $customerAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="openLinkModal(' . $customerAutoID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
        $status .= '<a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>';
        /* if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger)) {
             $status .= '<spsn class="pull-right"><a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
         } else {
             $status .= '<spsn class="pull-right"><a onclick="fetchPage(\'system/GroupMaster/erp_customer_group_master_new\',' . $customerAutoID . ',\'Edit Customer\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_customer(' . $customerAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
         }*/
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_customer_grp_drop'))
{
    function all_customer_grp_drop($status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select("groupCustomerAutoID,groupCustomerName,groupcustomerSystemCode,customerCountry");
        $CI->db->from('srp_erp_groupcustomermaster');
        $CI->db->where('companygroupID', $companyGroup['companyGroupID'] ?? '');
        $customer = $CI->db->get()->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Customer');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['groupCustomerAutoID'] ?? '')] = (trim($row['groupcustomerSystemCode'] ?? '') ? trim($row['groupcustomerSystemCode'] ?? '') . ' | ' : '') . trim($row['groupCustomerName'] ?? '') . (trim($row['customerCountry'] ?? '') ? ' | ' . trim($row['customerCountry'] ?? '') : '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('all_supplier_group_drop'))
{
    function all_supplier_group_drop($status = TRUE)/*Load all Supplier*/
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select("groupSupplierAutoID,groupSupplierName,groupSupplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_groupsuppliermaster');
        $CI->db->where('companygroupID', $companyGroup['companyGroupID'] ?? '');
        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => 'Select Supplier');
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['groupSupplierAutoID'] ?? '')] = (trim($row['groupSupplierSystemCode'] ?? '') ? trim($row['groupSupplierSystemCode'] ?? '') . ' | ' : '') . trim($row['groupSupplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}

if (!function_exists('customer_company_link'))
{
    function customer_company_link($groupCustomerMasterID = NULL, $status = TRUE)
    {
        $CI = &get_instance();
        $masterGroupID = getParentgroupMasterID();
        $CI = &get_instance();
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE parentID = ($masterGroupID) ")->result_array();
        return $customer;
    }
}


if (!function_exists('dropdown_companyCustomers'))
{
    function dropdown_companyCustomers($companyID, $customerMasterID = null)
    {
        $CI = &get_instance();
        $employees = array();

        if ($companyID != '')
        {

            $employees = $CI->db->query("SELECT
	customerAutoID,customerSystemCode,customerName
FROM
	srp_erp_customermaster
WHERE companyID = ($companyID) AND isActive = 1 AND deletedYN = 0 AND NOT EXISTS
        (
        SELECT  customerMasterID
        FROM    srp_erp_groupcustomerdetails
        WHERE   srp_erp_customermaster.customerAutoID = srp_erp_groupcustomerdetails.customerMasterID /**/
        )")->result_array();
        }
        if ($customerMasterID != '')
        {
            $cust = $CI->db->query("SELECT
	customerAutoID,customerSystemCode,customerName
FROM
	srp_erp_customermaster
WHERE customerAutoID = ($customerMasterID) AND isActive = 1 AND deletedYN = 0 ")->row_array();
        }
        $data_arr = array('' => 'Select Customer');
        if (!empty($cust))
        {
            $data_arr[trim($cust['customerAutoID'] ?? '')] = trim($cust['customerSystemCode'] ?? '') . ' | ' . trim($cust['customerName'] ?? '');
        }
        if ($employees)
        {
            foreach ($employees as $row)
            {
                $data_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerSystemCode'] ?? '') . ' | ' . trim($row['customerName'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('editgroupcategory'))
{
    function editgroupcategory($partyCategoryID)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $customercategory = $CI->db->query("select partyCategoryID from srp_erp_groupcustomermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        //$suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_groupsuppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($customercategory))
        {
            $status .= '<span class="pull-right"><a onclick="load_duplicate_group_customer_category(' . $partyCategoryID . ')"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_customer_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        }
        else
        {
            $status .= '<span class="pull-right"><a onclick="load_duplicate_group_customer_category(' . $partyCategoryID . ')"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_customer_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editcustomercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('editsuppliergroupcategory'))
{
    function editsuppliergroupcategory($partyCategoryID)
    {
        // echo $to;
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        //$customercategory = $CI->db->query("select partyCategoryID from srp_erp_customermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        $suppliercategory = $CI->db->query("select partyCategoryID from srp_erp_groupsuppliermaster WHERE partyCategoryID=$partyCategoryID ;")->row_array();
        if (!empty($suppliercategory))
        {
            $status .= '<span class="pull-right"><a onclick="load_duplicate_group_supplier_category(' . $partyCategoryID . ')"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_supplier_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;</span>';
        }
        else
        {
            $status .= '<span class="pull-right"><a onclick="load_duplicate_group_supplier_category(' . $partyCategoryID . ')"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_group_supplier_category(' . $partyCategoryID . ')"><span title="" rel="tooltip" class="glyphicon glyphicon-link" data-original-title="Link"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="editsuppliercategory(' . $partyCategoryID . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_category(' . $partyCategoryID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';
        }


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('party_group_category'))
{
    function party_group_category($partyType, $status = TRUE)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $grpID = $companyID;
        $data = $CI->db->query("select partyCategoryID,partyType,categoryDescription from `srp_erp_grouppartycategories` where groupID=$grpID and partyType=$partyType ")->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Category');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['partyCategoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('master_coa_account_group'))
{
    function master_coa_account_group()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $grpID = $companyGroup['companyGroupID'] ?? '';
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,systemAccountCode,subCategory");
        $CI->db->FROM('srp_erp_groupchartofaccounts');
        $CI->db->WHERE('masterAccountYN', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        $CI->db->where('groupID', $grpID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Master Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('edit_otElement'))
{
    function edit_otElement($id, $description, $usageCount)
    {
        $status = '<spsn class="pull-right"><a onclick="edit_element(' . $id . ', \'' . $description . '\')">';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        if ($usageCount == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_ot_element(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" ';
            $status .= 'style="color:rgb(209, 91, 71);"></span></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('edit_overtimegroup'))
{
    function edit_overtimegroup($id)
    {

        $status = '<spsn class="pull-right"><a onclick="edit_overtimegroup(' . $id . ')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_ot_group(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';

        return $status;
    }
}
if (!function_exists('ot_systeminput'))
{
    function ot_systeminput()
    {
        $CI = &get_instance();
        $data = $CI->db->query("select * from srp_erp_ot_systeminputs ")->result_array();

        return $data;
    }
}
if (!function_exists('load_OT_slab_action'))
{
    function load_OT_slab_action($otSlabsMasterID)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'fetchPage("system/hrm/OverTimeManagementSalamAir/over_time_slab_new",' . $otSlabsMasterID . ',"Over Time Slab","OTSLAB"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        //$status .= '<a onclick="delete_item(' . $otSlabsMasterID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('set_deleted_class'))
{
    function set_deleted_class($isDeleted)
    {
        $status = 'notdeleted';
        if ($isDeleted == 1)
        {
            $status = 'deleted';
        }

        return $status;
    }
}

if (!function_exists('ot_slabDrop_down'))
{
    function ot_slabDrop_down($groupID)
    {
        $CI = &get_instance();
        $currency = $CI->db->query("select CurrencyID from srp_erp_ot_groups WHERE otGroupID=$groupID ;")->row_array();
        $currencyID = $currency['CurrencyID'];
        $companyID = current_companyID();
        $data = $CI->db->query("select otSlabsMasterID, Description from srp_erp_ot_slabsmaster WHERE companyID={$companyID} And transactionCurrencyID={$currencyID}")->result_array();

        return $data;
    }
}

if (!function_exists('edit_overTimeGroupDetail'))
{
    function edit_overTimeGroupDetail($id, $systemInputID, $hourlyRate, $slabMasterID, $inputType)
    {

        $status = '<spsn class="pull-right"><a onclick="edit_overTimeGroupDetail(\'' . $id . '\', \'' . $systemInputID . '\', ';
        $status .= '\'' . $hourlyRate . '\', \'' . $slabMasterID . '\',\'' . $inputType . '\')">';
        $status .= '<span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        $status .= '<a onclick="delete_ot_group_detail(' . $id . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash"';
        $status .= 'style="color:rgb(209, 91, 71);"></span></a></span>';

        return $status;
    }
}


if (!function_exists('load_OT_group_employee_action'))
{
    function load_OT_group_employee_action($otGroupEmpID)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        //$status .= '<a onclick=\'fetchPage("system/hrm/OverTimeManagementSalamAir/over_time_slab_new",' . $otSlabsMasterID . ',"Over Time Slab","OTSLAB"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '<a onclick="delete_item(' . $otGroupEmpID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_not_assigned_employee_to_OT_group'))
{
    function all_not_assigned_employee_to_OT_group($otGroupID)
    {
        $CI = &get_instance();
        $customer_arr = array();

        $companyIDd = current_companyID();

        $CurrencyID = $CI->db->query("SELECT
	CurrencyID
FROM
	srp_erp_ot_groups

WHERE otGroupID = $otGroupID")->row_array();

        $Currency = $CurrencyID['CurrencyID'];

        $employees = $CI->db->query("SELECT
	EIdNo,Ename2
FROM
	srp_employeesdetails e
WHERE NOT EXISTS
        (
        SELECT  EmpID
        FROM    srp_erp_ot_groupemployees
        WHERE   srp_erp_ot_groupemployees.empID = e.EIdNo AND srp_erp_ot_groupemployees.companyID = $companyIDd

        ) AND e.isPayrollEmployee=1 AND e.empConfirmedYN=1 AND e.Erp_companyID = $companyIDd AND e.payCurrencyID=$Currency")->result_array();


        if ($employees)
        {
            foreach ($employees as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('getDesignationDrop'))
{
    function getDesignationDrop($status = false)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("DesignationID,DesDescription");
        $CI->db->FROM('srp_designation');
        $CI->db->WHERE('Erp_companyID', $companyID);
        $CI->db->WHERE('isDeleted', 0);
        $data = $CI->db->get()->result_array();
        $data_arr = [];

        if ($status == true)
        {
            $primaryLanguage = getPrimaryLanguage();
            $CI->lang->load('common', $primaryLanguage);
            $data_arr[''] = $CI->lang->line('common_select_a_option');
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['DesignationID'] ?? '')] = trim($row['DesDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

//genser drop down
if (!function_exists('load_gender_drop'))
{
    function load_gender_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("genderID,name");
        $CI->db->FROM('srp_erp_gender');
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}

if (!function_exists('get_hrms_insuranceCategory'))
{
    function get_hrms_insuranceCategory($isDrop = null)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->select("insurancecategoryID,description");
        $CI->db->from('srp_erp_family_insurancecategory');
        $CI->db->where('companyID', $companyID);
        $data = $CI->db->get()->result_array();

        if ($isDrop == null)
        {
            return $data;
        }

        $data_arr = array('' => $CI->lang->line('common_select'));
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['insurancecategoryID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('hrms_relationship_drop'))
{
    function hrms_relationship_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("relationshipID,relationship");
        $CI->db->FROM('srp_erp_family_relationship');
        $cntry = $CI->db->get()->result_array();
        $location_arr = array('' => $CI->lang->line('common_select_relationship')/*'Select Relationship'*/);
        if (isset($cntry))
        {
            foreach ($cntry as $row)
            {
                $location_arr[trim($row['relationshipID'] ?? '')] = trim($row['relationship'] ?? '');
            }
        }

        return $location_arr;
    }
}

if (!function_exists('format_date_other'))
{
    function format_date_other($date = NULL)
    {
        if (isset($date))
        {
            if (!empty($date))
            {
                return date('dS M Y', strtotime($date));
            }
        }
        else
        {
            return date('dS M Y', time());
        }
    }
}

if (!function_exists('document_uploads_family_url'))
{
    function document_uploads_family_url()
    {
        $url = base_url('images/family_images') . '/';

        return $url;
    }
}

if (!function_exists('get_hrms_relationship'))
{
    function get_hrms_relationship()
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_family_relationship');

        return $CI->db->get()->result_array();
    }
}


if (!function_exists('supplier_company_link'))
{
    function supplier_company_link($groupSupplierMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupsupplierdetails
        WHERE   srp_erp_groupsupplierdetails.groupSupplierMasterID = $groupSupplierMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupsupplierdetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companySuppliers'))
{
    function dropdown_companySuppliers($companyID, $supplierMasterID = null)
    {
        $CI = &get_instance();
        $employees = array();

        if ($companyID != '')
        {

            $employees = $CI->db->query("SELECT
	supplierAutoID,supplierSystemCode,supplierName
FROM
	srp_erp_suppliermaster
WHERE companyID = ($companyID) AND deletedYN = 0 AND isActive = 1 AND NOT EXISTS
        (
        SELECT  SupplierMasterID
        FROM    srp_erp_groupsupplierdetails
        WHERE   srp_erp_suppliermaster.supplierAutoID = srp_erp_groupsupplierdetails.SupplierMasterID
        )")->result_array();
        }
        if ($supplierMasterID != '')
        {
            $sup = $CI->db->query("SELECT
	supplierAutoID,supplierSystemCode,supplierName
FROM
	srp_erp_suppliermaster
WHERE supplierAutoID = ($supplierMasterID) AND deletedYN = 0 AND isActive = 1")->row_array();
        }
        $data_arr = array('' => 'Select Supplier');
        if (!empty($sup))
        {
            $data_arr[trim($sup['supplierAutoID'] ?? '')] = trim($sup['supplierSystemCode'] ?? '') . ' | ' . trim($sup['supplierName'] ?? '');
        }
        if ($employees)
        {
            foreach ($employees as $row)
            {
                $data_arr[trim($row['supplierAutoID'] ?? '')] = trim($row['supplierSystemCode'] ?? '') . ' | ' . trim($row['supplierName'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_all_nationality_drop'))
{
    function load_all_nationality_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', current_companyID());
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => $CI->lang->line('common_select_nationality')/*'Select Nationality'*/);
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['NId'] ?? '')] = trim($row['Nationality'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_nationality_drop'))
{
    function load_nationality_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("NId,Nationality");
        $CI->db->FROM('srp_nationality');
        $CI->db->where('Erp_companyID', current_companyID());
        $cntry = $CI->db->get()->result_array();

        return $cntry;
    }
}


if (!function_exists('getDocumentfamilyAttachment'))
{
    function getDocumentfamilyAttachment($id)
    {
        $status = '';
        if ($id == 1)
        {
            $status = '<spsn class="">Passport</span>';
        }
        else if ($id == 2)
        {
            $status = '<spsn class="">Visa</span>';
        }
        else
        {
            $status = '<spsn class="">Insurance</span>';
        }

        return $status;
    }
}

if (!function_exists('editsupplier'))
{
    function editsupplier($supplierAutoID, $deletedYN, $confirmedYN = 0, $approvedYN = 0, $referidentity = 0, $pageidentity = 0, $isSrmGenarated = 0)
    /** $referidentity = 0, $pageidentity = 0 */
    {
        // echo $to;
        $CI = &get_instance();
        $ApprovalforsupplierMaster = getPolicyValues('ASM', 'All');
        //$newpage_id= ''.$supplierAutoID.'_'.$pageidentity.'';
        $status = '<span class="pull-right">';
        if ($deletedYN != 1)
        {
            $customerinvoice = $CI->db->query("select supplierID from srp_erp_paysupplierinvoicemaster WHERE supplierID=$supplierAutoID ;")->row_array();
            $customerreceipt = $CI->db->query("select partyID from srp_erp_paymentvouchermaster WHERE partyID=$supplierAutoID ;")->row_array();
            $purchaseorder = $CI->db->query("select supplierID from srp_erp_purchaseordermaster WHERE supplierID=$supplierAutoID ;")->row_array();
            $paymentmatching = $CI->db->query("select supplierID from srp_erp_pvadvancematch WHERE supplierID=$supplierAutoID ;")->row_array();
            $grv = $CI->db->query("select supplierID from srp_erp_grvmaster WHERE supplierID=$supplierAutoID ;")->row_array();
            $purchasereturn = $CI->db->query("select supplierID from srp_erp_stockreturnmaster WHERE supplierID=$supplierAutoID ;")->row_array();
            $creditnote = $CI->db->query("select supplierID from srp_erp_debitnotemaster WHERE supplierID=$supplierAutoID ;")->row_array();
            $generalledger = $CI->db->query("select partyautoID from srp_erp_generalledger WHERE partyautoID=$supplierAutoID AND partyType = 'SUP';")->row_array();

            if ($isSrmGenarated == 1)
            {
                $CI->db->select("srp_erp_srm_vendor_company_requests.companyReqID");
                $CI->db->from('srp_erp_srm_suppliermaster');
                $CI->db->join('srp_erp_srm_vendor_company_requests', 'srp_erp_srm_vendor_company_requests.companyReqID = srp_erp_srm_suppliermaster.companyRequestMasterID');
                $CI->db->where('srp_erp_srm_suppliermaster.erpSupplierAutoID', $supplierAutoID);
                $company_request = $CI->db->get()->row_array();
            }


            if (!empty($customerinvoice || $customerreceipt || $creditnote || $generalledger || $purchaseorder || $paymentmatching || $grv || $purchasereturn))
            {
                if ($pageidentity == 1)
                {
                    $status .= '<a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_approval_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>&nbsp;&nbsp;'; //fetchPage(\'system/supplier/erp_supplier_master_new\',' . $newpage_id .',\'Edit Supplier\')
                }
                else
                {
                    $status .= '<a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>&nbsp;&nbsp;';
                }
            }
            else
            {
                if ($pageidentity == 1)
                {
                    $status .= '<a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_approval_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>&nbsp;&nbsp;';
                }
                else
                {
                    $status .= '<a onclick="attachment_modal(' . $supplierAutoID . ',\'Supplier\',\'SUP\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip glyphicon-paperclip-btn"></span></a>&nbsp;&nbsp;<a onclick="fetchPage(\'system/supplier/erp_supplier_master_new\',' . $supplierAutoID . ',\'Edit Supplier\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil glyphicon-pencil-btn"></span></a>&nbsp;&nbsp;';
                }

                if ($isSrmGenarated == 1)
                {
                    $status .= '<a onclick="fetchPage(\'system/supplier/erp_supplier_master_srm_request_view\',' . $company_request['companyReqID'] . ',\'Company Request\')"><span title="View Srm Company Request" rel="tooltip" class="glyphicon glyphicon-eye-open glyphicon-eye-open-btn"></span></a>&nbsp;&nbsp;';
                }

                if ($ApprovalforsupplierMaster == 1)
                {
                    if ($approvedYN == 0 && $confirmedYN != 1)
                    {
                        $status .= '<a onclick="delete_supplier(' . $supplierAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
                    }
                }
                else
                {
                    $status .= '<a onclick="delete_supplier(' . $supplierAutoID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash glyphicon-trash-btn" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
                }
            }
            if ($ApprovalforsupplierMaster == 1 && $approvedYN == 0 && $confirmedYN == 1 && $referidentity == 0)
            {
                $status .= '<a onclick="referback_supplier(' . $supplierAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
            }


            /*    if($ApprovalforItemMaster == 1){
                if($approvedYN == 0){
                    if ($confirmedYN == 1 ) {
                        $status .= '<a onclick="referback_item(' . $itemAutoID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;';
                    }else{
                        if(empty($items)){
                            $status .= '<a class="text-yellow" onclick="delete_item_master(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                        }
                    }
                }
            }*/
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('load_LA_approval_action'))
{
    function load_LA_approval_action($leaveMasterID, $ECConfirmedYN, $approved, $createdUserID, $level, $isFromCancel = 0)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->lang->load('hrms_approvals', $primaryLanguage);
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $leavappliction = $CI->lang->line('hrms_payroll_leave_application');
        $status .= '<a onclick=\'attachment_modal(' . $leaveMasterID . ',"' . $leavappliction . '","LA");\'>';
        $status .= '<span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';

        if ($approved == 0)
        {
            $status .= '<a target="_blank" onclick="load_emp_leaveDet_new(\'' . $leaveMasterID . '\',\'' . $approved . '\', ' . $level . ', ' . $isFromCancel . ')" >';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="load_emp_leaveDet_new(\'' . $leaveMasterID . '\',\'' . $approved . '\', ' . $level . ')" >';
            $status .= '<span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('chart_of_account_company_link'))
{
    function chart_of_account_company_link($GLAutoID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupchartofaccountdetails
        WHERE   srp_erp_groupchartofaccountdetails.groupChartofAccountMasterID = $GLAutoID AND srp_erp_companygroupdetails.companyID = srp_erp_groupchartofaccountdetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companychartofAccounts'))
{
    function dropdown_companychartofAccounts($companyID, $chartofAccountID, $groupChartofAccountMasterID, $masterAccountYN)
    {
        $CI = &get_instance();
        $accounts = array();

        $master = $CI->db->query("SELECT
	*
FROM
	srp_erp_groupchartofaccounts
WHERE GLAutoID = ($groupChartofAccountMasterID) ")->row_array();
        $accountCategoryTypeID = $master['accountCategoryTypeID'];
        $isBank = $master['isBank'];
        $isCash = $master['isCash'];
        if ($isBank == 1)
        {
            $where = 'AND isCash=' . $isCash;
        }
        else
        {
            $where = '';
        }

        if ($companyID != '')
        {

            $accounts = $CI->db->query("SELECT
	 GLAutoID,systemAccountCode,GLDescription
FROM
	srp_erp_chartofaccounts
WHERE companyID = ($companyID) AND accountCategoryTypeID = $accountCategoryTypeID AND masterAccountYN = $masterAccountYN $where AND deletedYN = 0 AND  isActive = 1 AND NOT EXISTS
        (
        SELECT  groupChartofAccountDetailID
        FROM    srp_erp_groupchartofaccountdetails
        WHERE   srp_erp_chartofaccounts.GLAutoID = srp_erp_groupchartofaccountdetails.chartofAccountID 
        )")->result_array();
        }

        if ($chartofAccountID != '')
        {
            $chart = $CI->db->query("SELECT
	GLAutoID,systemAccountCode,GLDescription
FROM
	srp_erp_chartofaccounts
WHERE GLAutoID = ($chartofAccountID) AND deletedYN = 0 AND  isActive ")->row_array();
        }
        $data_arr = array('' => 'Select Chart OF Accounts');

        if (!empty($chart))
        {
            $data_arr[trim($chart['GLAutoID'] ?? '')] = trim($chart['systemAccountCode'] ?? '') . ' | ' . trim($chart['GLDescription'] ?? '');
        }

        if ($accounts)
        {
            foreach ($accounts as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('segment_company_link'))
{
    function segment_company_link($groupSegmentID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupsegmentdetails
        WHERE   srp_erp_groupsegmentdetails.groupSegmentID = $groupSegmentID AND srp_erp_companygroupdetails.companyID = srp_erp_groupsegmentdetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companysegments'))
{
    function dropdown_companysegments($companyID, $segmentID = null)
    {
        $CI = &get_instance();
        $segment = array();

        if ($companyID != '')
        {

            $segment = $CI->db->query("SELECT
	 segmentID,companyID,segmentCode,description
FROM
	srp_erp_segment
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupSegmentDetailID
        FROM    srp_erp_groupsegmentdetails
        WHERE   srp_erp_segment.segmentID = srp_erp_groupsegmentdetails.segmentID
        )")->result_array();
        }

        if ($segmentID != '')
        {
            $cust = $CI->db->query("SELECT
	segmentID,companyID,segmentCode,description
FROM
	srp_erp_segment
WHERE segmentID = ($segmentID)")->row_array();
        }
        $data_arr = array('' => 'Select Segment');

        if (!empty($cust))
        {
            $data_arr[trim($cust['segmentID'] ?? '')] = trim($cust['segmentCode'] ?? '') . ' | ' . trim($cust['description'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_boq_category_action'))
{ /*get po action list*/
    function load_boq_category_action($action, $CatDescrip)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'addNewSubCategory("' . $action . '","' . $CatDescrip . '"); \'><span class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        $status .= '<a onclick="deleteCategory(' . $action . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';
        return $status;
    }
}

if (!function_exists('nextSortOrder'))
{
    function nextSortOrder()
    {

        $CI = &get_instance();
        $CI->db->select_max('sortOrder');
        $CI->db->from('srp_erp_boq_category');

        $data = $CI->db->get()->row_array();
        if (is_null($data['sortOrder']))
        {
            return 0;
        }
        else
        {
            return $data['sortOrder'];
        }
    }
}

if (!function_exists('load_boq_sub_category_action'))
{ /*get po action list*/
    function load_boq_sub_category_action($action)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'deletesubcategory("' . $action . '"); \'><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';

        return $status;
    }
}

if (!function_exists('get_category'))
{
    function get_category()/*Load all location*/
    {

        $CI = &get_instance();
        $CI->db->select("categoryID, categoryCode,categoryDescription");
        $CI->db->from('srp_erp_boq_category');

        $cateogry = $CI->db->get()->result_array();
        $cateogry_arr = array('' => 'Select a Category');
        if (isset($cateogry))
        {
            foreach ($cateogry as $row)
            {
                $cateogry_arr[trim($row['categoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
            }
        }

        return $cateogry_arr;
    }
}


if (!function_exists('loadboqheaderaction'))
{ /*get po action list*/
    function loadboqheaderaction($action)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick="fetchPage(\'system/pm/erp_boq_estimation_add_new\',' . $action . ',\'System Log\')" ><span class=" glyphicon glyphicon-pencil "></span></a>';

        $status .= '<span class="pull-right"> &nbsp;|&nbsp;';

        $status .= '<a onclick="deleteBoqHeader(' . $action . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';

        return $status;
    }
}

if (!function_exists('opensubcatgroup'))
{
    function opensubcatgroup($itemCategoryID, $description)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="link_group_itemcategory(' . $itemCategoryID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a> &nbsp;|&nbsp; <a onclick=\'fetchPage("system/GroupItemCategory/sub_category_add_group","' . $itemCategoryID . '","' . $description . '","Sub Category",""); \'><button type="button" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-plus" style="color:green;"></span></button></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('checkApproved'))
{ //get finance period using companyFinancePeriodID.
    function checkApproved($documentSystemCode, $documentID, $approvalLevelID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("documentApprovedID");
        $CI->db->FROM('srp_erp_documentapproved');
        $CI->db->where('companyID', $companyID);
        $CI->db->where('documentSystemCode', $documentSystemCode);
        $CI->db->where('documentID', $documentID);
        $CI->db->where('approvalLevelID', $approvalLevelID);
        $CI->db->where('approvedYN', 1);
        $data = $CI->db->get()->row_array();
        if (!empty($data))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

if (!function_exists('loadprojectAction'))
{ /*get po action list*/
    function loadprojectAction($id)
    {
        $status = '<span class="pull-right"><a onclick="edit_project( ' . $id . ' )"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span>';
        $status .= '&nbsp;&nbsp; | &nbsp;&nbsp; <a onclick="delete_project(' . $id . ')" >';
        $status .= '<span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>';


        return $status;
    }
}

if (!function_exists('get_all_boq_project'))
{
    function get_all_boq_project()
    {
        $CI = &get_instance();
        $CI->db->select("projectID, projectName");
        $CI->db->from('srp_erp_projects');
        $CI->db->where('companyID', current_companyID());

        $cateogry = $CI->db->get()->result_array();
        $cateogry_arr = array('' => 'Select a project');
        if (isset($cateogry))
        {
            foreach ($cateogry as $row)
            {
                $cateogry_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }

        return $cateogry_arr;
    }
}

if (!function_exists('itemcategory_company_link'))
{
    function itemcategory_company_link($itemCategoryID, $status = TRUE)/*Load all item category company*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];

        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupitemcategorydetails
        WHERE   srp_erp_groupitemcategorydetails.groupItemCategoryID = $itemCategoryID AND srp_erp_companygroupdetails.companyID = srp_erp_groupitemcategorydetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_company_group_item_categories'))
{
    function dropdown_company_group_item_categories($companyID, $itemCategoryID = null)
    {
        $CI = &get_instance();
        $segment = array();


        if ($companyID != '')
        {
            $segment = $CI->db->query("SELECT
	 itemCategoryID,description,codePrefix
FROM
	srp_erp_itemcategory
WHERE companyID = ($companyID) AND masterID IS NULL
AND NOT EXISTS
        (
        SELECT  groupItemCategoryDetailID
        FROM    srp_erp_groupitemcategorydetails
        WHERE   srp_erp_itemcategory.itemCategoryID = srp_erp_groupitemcategorydetails.itemCategoryID
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
            $data_arr[trim($cust['itemCategoryID'] ?? '')] = trim($cust['codePrefix'] ?? '') . ' | ' . trim($cust['description'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('get_all_mfq_industry'))
{
    function get_all_mfq_industry()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_industrytypes');

        $workflow = $CI->db->get()->result_array();
        $workflow_arr = array('' => 'Select an Industry');
        if (isset($workflow))
        {
            foreach ($workflow as $row)
            {
                $workflow_arr[trim($row['industrytypeID'] ?? '')] = trim($row['industryTypeDescription'] ?? '');
            }
        }
        return $workflow_arr;
    }
}


if (!function_exists('get_all_system_workflow'))
{
    function get_all_system_workflow()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_mfq_systemworkflowcategory');

        $workflow = $CI->db->get()->result_array();
        $workflow_arr = array('' => 'Select a Work Flow');
        if (isset($workflow))
        {
            foreach ($workflow as $row)
            {
                $workflow_arr[trim($row['workFlowID'] ?? '')] = trim($row['workFlowDescription'] ?? '');
            }
        }
        return $workflow_arr;
    }
}


if (!function_exists('get_all_workflow_template'))
{
    function get_all_workflow_template()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->join(
            'srp_erp_mfq_systemworkflowcategory',
            'srp_erp_mfq_systemworkflowcategory.workflowID = srp_erp_mfq_workflowtemplate.workflowID'
        );
        $CI->db->from('srp_erp_mfq_workflowtemplate');

        $workflow = $CI->db->get()->result_array();
        return $workflow;
    }
}


if (!function_exists('ot_tempalte_description'))
{ /*get po action list*/
    function ot_tempalte_description($defultDescription, $categoryDescription)
    {
        $status = '';
        if (!empty($defultDescription))
        {
            $status = $defultDescription;
        }
        else
        {
            $status = $categoryDescription;
        }
        return $status;
    }
}

if (!function_exists('get_user_isChangePassword'))
{
    function get_user_isChangePassword()
    {
        $CI = &get_instance();
        $CI->db->select('isChangePassword');
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $isChangePassword = $CI->db->get()->row('isChangePassword');
        if ($isChangePassword == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}


if (!function_exists('all_ot_category_drop'))
{
    function all_ot_category_drop($companyID)
    {
        $CI = &get_instance();
        $data = $CI->db->query("SELECT
 description,
	id AS overtimeCategoryID,
	0 AS defaultcategoryID,
	1 AS inputType
FROM
	srp_erp_pay_overtimecategory
where companyid=$companyID and id not in (select overtimeCategoryID from srp_erp_generalottemplatedetails where companyID=$companyID)
UNION
select description, 0 AS overtimeCategoryID,
	defaultTypeID AS defaultcategoryID,
	2 AS inputType from srp_erp_generalotdefaulttypes where defaultTypeID not in (select defaultcategoryID from srp_erp_generalottemplatedetails where companyID=$companyID)
	")->result_array();
        $data_arr = [];
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['overtimeCategoryID'] ?? '') . '|' . trim($row['defaultcategoryID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('loadHeaderBoqlanning'))
{ /*get po action list*/
    function loadHeaderBoqlanning($action)
    {
        $status = '<span class="pull-right">';


        $status .= '<a onclick="fetchPage(\'system/pm/erp_boq_project_planning\',' . $action . ',\'System Log\')" ><span class=" glyphicon glyphicon-pencil "></span></a>';


        return $status;
    }

    if (!function_exists('all_mfq_customer_drop'))
    {
        function all_mfq_customer_drop($status = TRUE, $IsActive = null)/*Load all Customer*/
        {
            $CI = &get_instance();
            $CI->db->select("mfqCustomerAutoID,CustomerName,CustomerSystemCode,CustomerCountry,CompanyCode");
            $CI->db->from('srp_erp_mfq_customermaster');
            $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
            if ($IsActive == 1)
            {
                $CI->db->where('isActive', 1);
            }
            $customer = $CI->db->get()->result_array();
            if ($status)
            {
                $customer_arr = array('' => 'Select Customer');
            }
            else
            {
                $customer_arr = [];
            }
            if (isset($customer)) {
                foreach ($customer as $row) {
                    $customerID = trim($row['mfqCustomerAutoID'] ?? '');
                    $customerCode = trim($row['CustomerSystemCode'] ?? '');
                    $customerName = trim($row['CustomerName'] ?? '');
                    $customerCountry = trim($row['CustomerCountry'] ?? '');
            
                    $customer_arr[$customerID] = 
                        ($customerCode ? $customerCode . ' | ' : '') . 
                        $customerName . 
                        ($customerCountry ? ' | ' . $customerCountry : '');
                }
            }            

            return $customer_arr;
        }
    }
}

if (!function_exists('load_attendance_summary_actions')) {
    function load_attendance_summary_actions($generalOTMasterID, $confirmedYN, $approvedYN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        
        $action = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';
                       
        if ($confirmedYN != 1) {
            $action .= '<li><a onclick="fetchPage(\'system/OverTime/erp_genaral_ot_detail\', ' . $generalOTMasterID . ', \'Over Time\', \'ATS\')">
                            <span class="glyphicon glyphicon-pencil" style="color:#116f5e"></span> Edit</a>
                        </li>';
        }

        if (($confirmedYN == 1 && $approvedYN == 0) || $approvedYN == 2) {
            $action .= '<li><a onclick="referback_general_ot(' . $generalOTMasterID . ')">
                            <span class="glyphicon glyphicon-repeat" style="color:#d15b47"></span> Refer Back</a>
                        </li>';
        }

        $action .= '<li><a onclick="general_ot_view_model(' . $generalOTMasterID . ')">
                        <span class="glyphicon glyphicon-eye-open" style="color:#03a9f4"></span> View</a>
                    </li>';

        $action .= '<li><a target="_blank" href="' . site_url('OverTime/load_general_ot_print/') . '/' . $generalOTMasterID . '">
                        <span class="glyphicon glyphicon-print" style="color:#607d8b"></span> Print</a>
                    </li>';

        if ($confirmedYN == 0 || $confirmedYN == 3) {
            $action .= '<li><a onclick="delete_general_ot_template(' . $generalOTMasterID . ',\'Invoices\')">
                            <span class="glyphicon glyphicon-trash" style="color:#d15b47"></span> Delete</a>
                        </li>';
        }

        $action .= '</ul></div>';
        
        return $action;
    }
}

if (!function_exists('general_ot_action'))
{
    function general_ot_action($generalOTMasterID, $confirmedYN, $approvedYN)
    {

        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if ($confirmedYN != 1)
        {
            $status .= '<a onclick=\'fetchPage("system/OverTime/erp_genaral_ot_detail",' . $generalOTMasterID . ',"Over Time","ATS"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($confirmedYN == 1 && $approvedYN == 0 || $approvedYN == 2)
        {
            $status .= '<a onclick="referback_general_ot(' . $generalOTMasterID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="general_ot_view_model(' . $generalOTMasterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('OverTime/load_general_ot_print/') . '/' . $generalOTMasterID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($confirmedYN == 0 || $confirmedYN == 3)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_general_ot_template(' . $generalOTMasterID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('got_action_approval'))
{ /*get po action list*/
    function got_action_approval($generalOTMasterID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $generalOTMasterID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp; ';
        }
        else
        {
            $status .= '<a target="_blank" onclick="general_ot_view_model(' . $generalOTMasterID . ')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('load_group_uom_action'))
{ /*get po action list*/
    function load_group_uom_action($UnitID, $desc, $code)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="load_duplicate_group_uom(' . $UnitID . ')"><span title="Replicate" rel="tooltip" class="glyphicon glyphicon-duplicate"></span></a>&nbsp;|&nbsp;<a onclick="link_uom(' . $UnitID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;<a onclick=\'fetch_umo_detail_con(' . $UnitID . ',"' . $desc . '","' . $code . '");\'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('group_item_edit'))
{
    function group_item_edit($itemAutoID, $isActive = 0, $isSubItemExist = NULL)
    {
        $status = '<span class="pull-right">';

        $status .= '<a onclick="link_group_item_master(' . $itemAutoID . ')"><span title="Link" rel="tooltip" class="glyphicon glyphicon-link" ></span></a>&nbsp;|&nbsp;';

        if ($isActive)
        {
            $status .= '<a onclick="load_duplicate_item(' . $itemAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="fetchPage(\'system/GroupItemMaster/erp_group_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick="load_duplicate_item(' . $itemAutoID . ')"><span title="Replicate" rel="tooltip"class="glyphicon glyphicon-duplicate"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="fetchPage(\'system/GroupItemMaster/erp_group_item_new\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('all_group_main_category_drop'))
{
    function all_group_main_category_drop()
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_groupitemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('groupID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Main Category');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('empCodeGenerateTemp'))
{
    function empCodeGenerateTemp($tibianType = null)
    {
        //Generate Employee Code
        $CI = &get_instance();
        $CI->load->library('sequence');

        if ($tibianType == null)
        {
            return $CI->sequence->sequence_generator_temp('EMP');
        }

        return $CI->sequence->sequence_generator_employee($tibianType);
    }
}

if (!function_exists('all_group_umo_new_drop'))
{
    function all_group_umo_new_drop()
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        //$companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $CI->db->select('UnitID,UnitShortCode,UnitDes');
        $CI->db->from('srp_erp_group_unit_of_measure');
        $CI->db->WHERE('groupID', $companyID);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select UOM');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['UnitID'] ?? '')] = trim($row['UnitShortCode'] ?? '') . ' | ' . trim($row['UnitDes'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('item_company_link'))
{
    function item_company_link($groupItemMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupitemmasterdetails
        WHERE   srp_erp_groupitemmasterdetails.groupItemMasterID = $groupItemMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupitemmasterdetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }

        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}


if (!function_exists('dropdown_companyitems'))
{  //if any one changing this function please inform to hisham
    function dropdown_companyitems($companyID, $ItemAutoID = null, $all = false)
    {
        $CI = &get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '')
        {

            $segment = $CI->db->query("SELECT
	 itemAutoID,companyID,itemSystemCode,itemDescription
FROM
	srp_erp_itemmaster
WHERE companyID = ($companyID) AND isActive = 1 AND deletedYN = 0 AND NOT EXISTS
        (
        SELECT  groupItemDetailID
        FROM    srp_erp_groupitemmasterdetails
        WHERE   srp_erp_itemmaster.itemAutoID = srp_erp_groupitemmasterdetails.ItemAutoID
        )")->result_array();
        }

        if ($ItemAutoID != '')
        {
            $cust = $CI->db->query("SELECT
	itemAutoID,companyID,itemSystemCode,itemDescription
FROM
	srp_erp_itemmaster
WHERE itemAutoID = ($ItemAutoID) AND isActive = 1 AND deletedYN = 0 ")->row_array();
        }
        if ($all)
        {
            $data_arr = array('' => 'Select Item');
        }
        else
        {
            $data_arr = array();
        }

        if (!empty($cust))
        {
            $data_arr[trim($cust['itemAutoID'] ?? '')] = trim($cust['itemSystemCode'] ?? '') . ' | ' . trim($cust['itemDescription'] ?? '');
        }
        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['itemDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('fetch_report_type'))
{
    function fetch_report_type()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_reporttemplate');
        $CI->db->where('isCustomizable', 1);
        $template = $CI->db->get()->result_array();
        $template_arr = array('' => 'Select Type');
        if (isset($template))
        {
            foreach ($template as $row)
            {
                $template_arr[trim($row['reportID'] ?? '')] = trim($row['reportDescription'] ?? '');
            }
        }
        return $template_arr;
    }
}


if (!function_exists('gl_description_template'))
{
    function gl_description_template($templateID, $requestType, int $id = null, array $master_data = null)
    {
        $CI = &get_instance();

        $reportID = $master_data['reportID'];
        $masterCategory = '';
        if ($reportID == 5)
        {
            $masterCategory = 'PL';
        }
        else if ($reportID == 6)
        {
            $masterCategory = 'BS';
        }

        $companyID = current_companyID();
        $template = [];

        if ($requestType == 'S')
        { /*For sub category*/

            $str = '';
            if ($master_data['templateType'] == 1)
            { //If template type is Fund management
                if ($reportID == 5)
                {
                    $subCategory = "IF(d.accountType = 'I', 'PLI', 'PLE')";
                }
                else if ($reportID == 6)
                {
                    $subCategory = "IF(d.accountType = 'A', 'BSA', 'BSL')";
                }

                $str = "AND chTB.subCategory = (
                          SELECT {$subCategory} accType
                          FROM  srp_erp_companyreporttemplatedetails detTB 
                          JOIN srp_erp_companyreporttemplatedetails d ON detTB.masterID = d.detID 
                          WHERE detTB.detID = {$id}
                        )";
            }

            if ($master_data['companyType'] != 2)
            {

                $template = $CI->db->query("SELECT GLAutoID, CONCAT(systemAccountCode, ' | ', GLSecondaryCode,  ' | ', GLDescription) desStr
                        FROM srp_erp_chartofaccounts chTB
                        WHERE masterCategory = '{$masterCategory}' AND companyID = {$companyID} AND masterAccountYN = 0
                        AND NOT EXISTS (
                           SELECT glAutoID FROM srp_erp_companyreporttemplatelinks linkTB
                           JOIN srp_erp_companyreporttemplatedetails detTB ON detTB.detID = linkTB.templateDetailID
                           WHERE detTB.companyReportTemplateID = {$templateID} AND linkTB.glAutoID = chTB.GLAutoID 
                        ){$str}")->result_array();
            }
            else
            {

                $companygroupID = getParentgroupMasterID();

                $template = $CI->db->query("SELECT GLAutoID, CONCAT(systemAccountCode, ' | ', GLSecondaryCode,  ' | ', GLDescription) desStr
                FROM srp_erp_chartofaccounts chTB
                WHERE masterCategory = '{$masterCategory}' AND companyID IN(select companyID from  srp_erp_companygroupdetails where companygroupID={$companygroupID}) AND masterAccountYN = 0
                AND NOT EXISTS (
                   SELECT glAutoID FROM srp_erp_companyreporttemplatelinks linkTB
                   JOIN srp_erp_companyreporttemplatedetails detTB ON detTB.detID = linkTB.templateDetailID
                   WHERE detTB.companyReportTemplateID = {$templateID} AND linkTB.glAutoID = chTB.GLAutoID 
                ){$str}")->result_array();
            }

        }
        else
        { /*For Group Total*/
            $this_masterID = $CI->db->get_where('srp_erp_companyreporttemplatedetails', ['detID' => $id])->row('masterID');
            $masterWhere = ($this_masterID != null) ? 'AND masterID = ' . $this_masterID : '';
            $template = $CI->db->query("SELECT description AS desStr, detID AS GLAutoID
                        FROM srp_erp_companyreporttemplatedetails detTB
                        WHERE companyReportTemplateID = {$templateID} AND itemType=1 {$masterWhere}
                        AND NOT EXISTS (
                           SELECT subCategory FROM srp_erp_companyreporttemplatelinks lkTB
                           WHERE templateDetailID = {$id} AND lkTB.subCategory = detTB.detID                           
                        )")->result_array();
        }


        $template_arr = [];
        if (isset($template))
        {
            foreach ($template as $row)
            {
                $template_arr[trim($row['GLAutoID'] ?? '')] = trim($row['desStr'] ?? '');
            }
        }
        return $template_arr;
    }
}

if (!function_exists('project_is_exist'))
{
    function project_is_exist()
    {
        $CI = &get_instance();
        $CI->db->SELECT("value");
        $CI->db->FROM('srp_erp_companypolicy');
        $CI->db->WHERE('companypolicymasterID', 9);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $row = $CI->db->get()->row_array();
        $data = 0;
        if (!empty($row))
        {
            if ($row['value'] == 1)
            {
                $data = 1;
            }
            else
            {
                $data = 0;
            }
        }
        return $data;
    }
}

if (!function_exists('project_currency'))
{
    function project_currency($projectID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("projectCurrencyID");
        $CI->db->FROM('srp_erp_projects');
        $CI->db->WHERE('projectID', $projectID);
        $row = $CI->db->get()->row_array();
        if (!empty($row))
        {
            return $row['projectCurrencyID'];
        }
    }
}

if (!function_exists('get_password_complexity'))
{
    function get_password_complexity()
    {
        $CI = &get_instance();
        $CI->db->select("projectComplexcityID,minimumLength,isCapitalLettersMandatory,isSpecialCharactersMandatory");
        $CI->db->from('srp_erp_passwordcomplexcity');
        $CI->db->where('companyID', current_companyID());
        return $CI->db->get()->row_array();
    }
}

if (!function_exists('get_password_complexity_exist'))
{
    function get_password_complexity_exist()
    {
        $CI = &get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'PC');
        $masterid = $CI->db->get()->row_array();

        $CI->db->select("companyPolicyAutoID");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('value', 1);
        $template = $CI->db->get()->row_array();
        $value = 0;
        if (!empty($template))
        {
            $value = 1;
        }
        else
        {
            $value = 0;
        }

        return $value;
    }
}

if (!function_exists('is_rpos_finance_posting_enabled'))
{
    function is_rpos_finance_posting_enabled()
    {
        $CI = &get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'PFP');
        $masterid = $CI->db->get()->row_array();
        $value = 1;
        $CI->db->select("companyPolicyAutoID");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('value', 0);
        $template = $CI->db->get()->row_array();
        if (!empty($template))
        {
            $value = 0;
        }
        else
        {
            $value = 1;
        }
        return $value;
    }
}

if (!function_exists('is_show_secondary_code_enabled'))
{
    function is_show_secondary_code_enabled()
    {
        $CI = &get_instance();
        $CI->db->select("companypolicymasterID");
        $CI->db->from('srp_erp_companypolicymaster');
        $CI->db->where('code', 'SSC');
        $masterid = $CI->db->get()->row_array();
        $value = 1;
        $CI->db->select("companyPolicyAutoID");
        $CI->db->from('srp_erp_companypolicy');
        $CI->db->where('companypolicymasterID', $masterid['companypolicymasterID']);
        $CI->db->where('value', 1);
        $template = $CI->db->get()->row_array();
        if (!empty($template))
        {
            $value = 1;
        }
        else
        {
            $value = 0;
        }
        return $value;
    }
}


if (!function_exists('finance_posting_button'))
{
    function finance_posting_button($shiftID)
    {
        $actions = '<div style="text-align: center;"><button class="btn btn-warning" data-shift_id="' . $shiftID . '" onclick="manual_function_finance_posting.call(this)">Run Finance Function</button></div>';
        return $actions;
    }
}


if (!function_exists('default_delivery_location_drop'))
{
    function default_delivery_location_drop($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select('wareHouseAutoID');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDefault', 1);
        $data = $CI->db->get()->row_array();
        if (!empty($data)) {
            return $data['wareHouseAutoID'];
        }

        return '';
    }
}


if (!function_exists('loadDefaultWarehousechkbx'))
{
    function loadDefaultWarehousechkbx($wareHouseAutoID, $isDefault)
    {
        $status = '<span class="pull-right">';


        if ($isDefault == 1)
        {
            $status .= '<input onchange="setDefaultWarehouse(this,' . $wareHouseAutoID . ')" id="isDefault" type="checkbox"  value="1" name="isDefault" checked>';
        }
        else
        {
            $status .= '<input onchange="setDefaultWarehouse(this,' . $wareHouseAutoID . ')" id="isDefault" type="checkbox"  value="1" name="isDefault">';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('current_user_segemnt'))
{
    function current_user_segemnt($id = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select("segmentID");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $data = $CI->db->get()->row_array();

        $CI = &get_instance();
        $CI->db->select("segmentCode");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('segmentID', $data['segmentID']);
        $datas = $CI->db->get()->row_array();
        if ($id)
        {
            $result = $data['segmentID'];
        }
        else
        {
            $result = (isset($data['segmentID']) ? $data['segmentID'] : '') . '|' . (isset($datas['segmentCode']) ? $datas['segmentCode'] : '');
        }


        return $result;
    }
}

if (!function_exists('get_segemnt_by_id'))
{
    function get_segemnt_by_id($segmentID)
    {

        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_segment');
        $CI->db->where('segmentID', $segmentID);
        $datas = $CI->db->get()->row_array();

        return $datas;
    }
}

if (!function_exists('expenseIncomeGL_drop'))
{
    function expenseIncomeGL_drop()
    {
        $CI = &get_instance();

        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        //$CI->db->WHERE('controllAccountYN ', 0);
        $CI->db->WHERE('masterCategory', 'PL');
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->ORDER_BY('GLSecondaryCode');
        $data = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        /*$data_arr = array('' => 'Select GL Account');
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }*/

        return $data;
    }
}

if (!function_exists('get_chart_of_accounts_masterID_drop'))
{
    function get_chart_of_accounts_masterID_drop($masterID)
    {
        $CI = &get_instance();

        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        //$CI->db->WHERE('controllAccountYN ', 0);
        $CI->db->WHERE('masterCategory', $masterID);
        $CI->db->WHERE('isActive', 1);
        $CI->db->WHERE('approvedYN', 1);
        $CI->db->WHERE('companyID', current_companyID());
        $CI->db->ORDER_BY('GLSecondaryCode');
        $data = $CI->db->get()->result_array();
        //echo $CI->db->last_query();
        $data_arr = array('' => 'Select GL Account');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('all_group_supplier_drop'))
{
    function all_group_supplier_drop($status = TRUE)/*Load all group Supplier*/
    {
        $CI = &get_instance();
        $CI->db->select("groupSupplierAutoID,groupSupplierName,groupSupplierSystemCode,supplierCountry");
        $CI->db->from('srp_erp_groupsuppliermaster');
        $CI->db->join('srp_erp_groupsupplierdetails', 'srp_erp_groupsuppliermaster.groupSupplierAutoID = srp_erp_groupsupplierdetails.groupSupplierMasterID', 'INNER');
        $CI->db->where('srp_erp_groupsuppliermaster.companyGroupID', current_companyID());
        $supplier = $CI->db->get()->result_array();
        if ($status)
        {
            $supplier_arr = array('' => 'Select Supplier');
        }
        else
        {
            $supplier_arr = [];
        }
        if (isset($supplier))
        {
            foreach ($supplier as $row)
            {
                $supplier_arr[trim($row['groupSupplierAutoID'] ?? '')] = (trim($row['groupSupplierSystemCode'] ?? '') ? trim($row['groupSupplierSystemCode'] ?? '') . ' | ' : '') . trim($row['groupSupplierName'] ?? '') . (trim($row['supplierCountry'] ?? '') ? ' | ' . trim($row['supplierCountry'] ?? '') : '');
            }
        }

        return $supplier_arr;
    }
}


if (!function_exists('warehouse_company_link'))
{
    function warehouse_company_link($wareHouseAutoID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupwarehousedetails
        WHERE   srp_erp_groupwarehousedetails.groupWarehouseMasterID = $wareHouseAutoID AND srp_erp_companygroupdetails.companyID = srp_erp_groupwarehousedetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companywarehouses'))
{
    function dropdown_companywarehouses($companyID, $warehosueMasterID = null)
    {
        $CI = &get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '')
        {

            $segment = $CI->db->query("SELECT
	 wareHouseAutoID,companyID,wareHouseCode,wareHouseDescription
FROM
	srp_erp_warehousemaster
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupWarehouseDetailID
        FROM    srp_erp_groupwarehousedetails
        WHERE   srp_erp_warehousemaster.wareHouseAutoID = srp_erp_groupwarehousedetails.warehosueMasterID
        )")->result_array();
        }

        if ($warehosueMasterID != '')
        {
            $cust = $CI->db->query("SELECT
	wareHouseAutoID,companyID,wareHouseCode,wareHouseDescription
FROM
	srp_erp_warehousemaster
WHERE wareHouseAutoID = ($warehosueMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select Warehouse');
        if (!empty($cust))
        {
            $data_arr[trim($cust['wareHouseAutoID'] ?? '')] = trim($cust['wareHouseCode'] ?? '') . ' | ' . trim($cust['wareHouseDescription'] ?? '');
        }
        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] ?? '') . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseDescription'] ?? ''));
            }
        }

        return $data_arr;
    }
}


if (!function_exists('uom_company_link'))
{
    function uom_company_link($groupUOMMasterID, $status = TRUE)/*Load all Customer*/
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $companyGroup = $CI->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $companygroupID = $companyGroup['companyGroupID'];
        $customer = $CI->db->query("SELECT
	srp_erp_companygroupdetails.companyID,srp_erp_company.company_code,srp_erp_company.company_name
FROM
	srp_erp_companygroupdetails
	JOIN srp_erp_company ON srp_erp_company.company_id = srp_erp_companygroupdetails.companyID
WHERE companygroupID = ($companygroupID) AND NOT EXISTS
        (
        SELECT  companyID
        FROM    srp_erp_groupuomdetails
        WHERE   srp_erp_groupuomdetails.groupUOMMasterID = $groupUOMMasterID AND srp_erp_companygroupdetails.companyID = srp_erp_groupuomdetails.companyID
        )")->result_array();
        if ($status)
        {
            $customer_arr = array('' => 'Select Company');
        }
        else
        {
            $customer_arr = [];
        }
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['companyID'] ?? '')] = trim($row['company_code'] ?? '') . ' | ' . trim($row['company_name'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('dropdown_companyuom'))
{
    function dropdown_companyuom($companyID, $UOMMasterID = null)
    {
        $CI = &get_instance();
        $segment = array();
        $data_arr = array();


        if ($companyID != '')
        {

            $segment = $CI->db->query("SELECT
	 UnitID,companyID,UnitShortCode,UnitDes
FROM
	srp_erp_unit_of_measure
WHERE companyID = ($companyID) AND NOT EXISTS
        (
        SELECT  groupUOMDetailID
        FROM    srp_erp_groupuomdetails
        WHERE   srp_erp_unit_of_measure.UnitID = srp_erp_groupuomdetails.UOMMasterID
        )")->result_array();
        }

        if ($UOMMasterID != '')
        {
            $cust = $CI->db->query("SELECT
	UnitID,companyID,UnitShortCode,UnitDes
FROM
	srp_erp_unit_of_measure
WHERE UnitID = ($UOMMasterID)")->row_array();
        }
        $data_arr = array('' => 'Select UOM');

        if (!empty($cust))
        {
            $data_arr[trim($cust['UnitID'] ?? '')] = trim($cust['UnitShortCode'] ?? '') . ' | ' . trim($cust['UnitDes'] ?? '');
        }

        if ($segment)
        {
            foreach ($segment as $row)
            {
                $data_arr[trim($row['UnitID'] ?? '')] = trim($row['UnitShortCode'] ?? '') . ' | ' . trim($row['UnitDes'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('send_approvalEmail_Manual'))
{
    function send_approvalEmail_Manual($mailData, $attachment = 0, $path = 0)
    {
        $companyID = current_companyID();
        $manual_email = array();
        $base_arr = array();
        $CI = &get_instance();

        $manual_email['companyID'] = $companyID;
        $manual_email['empName'] = $mailData['param']['empName'];
        $manual_email['documentSystemCode'] = $mailData['documentCode'];
        $manual_email['documentCode'] = $mailData['documentCode'];
        $manual_email['emailSubject'] = $mailData['subject'];
        $manual_email['empEmail'] = $mailData['toEmail'];
        $manual_email['type'] = $mailData['type'];
        $manual_email['emailBody'] = $mailData['param']['body'];
        $manual_email['empID'] = $mailData['empID'];

        $base_arr[] = $manual_email;

        $CI->load->library('email_manual');
        $res = $CI->email_manual->set_email_detail($base_arr);

        return true;
    }
}

if (!function_exists('send_approvalEmail'))
{
    function send_approvalEmail($mailData, $attachment = 0, $path = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $manual_email = array();
        $base_arr = array();

        $manual_email['companyID'] = $companyID;
        $manual_email['empName'] = isset($mailData['param']['empName']) ? $mailData['param']['empName'] : 'Unknown Employee';
        $manual_email['documentSystemCode'] = isset($mailData['documentSystemCode']) ? $mailData['documentSystemCode'] : '';
        $manual_email['documentCode'] = isset($mailData['documentCode']) ? $mailData['documentCode'] : '';
        $manual_email['documentID'] = isset($mailData['documentID']) ? $mailData['documentID'] : '';
        $manual_email['emailSubject'] = isset($mailData['emailSubject']) ? $mailData['emailSubject'] : 'No Subject';
        $manual_email['empEmail'] = isset($mailData['empEmail']) ? $mailData['empEmail'] : '';
        $manual_email["type"] = isset($mailData['type']) ? $mailData['type'] : 'unknown';
        $manual_email['emailBody'] = isset($mailData['param']['body']) ? $mailData['param']['body'] : 'No body content';
        $manual_email['empID'] = isset($mailData['empID']) ? $mailData['empID'] : '';

        $base_arr[] = $manual_email;

        $CI->load->library('email_manual');
        $res = $CI->email_manual->set_email_detail($base_arr);

        return true;
    }
}

if (!function_exists('send_push_notification'))
{
    function send_push_notification($managerID, $description, $documentCode, $type)
    {
        $CI = &get_instance();
        //send mobile notification
        $CI->db->select('player_id');
        $CI->db->from('srp_devices');
        $CI->db->where('emp_id', $managerID);
        $devices = $CI->db->get()->result_array();
        $player_ids = array();
        foreach ($devices as $device_id)
        {
            $content = array(
                "en" => $documentCode,
            );
            $headings = array("en" => $description);
            $fields = array(
                'app_id' => "2ca0ecc7-6ecf-436d-b82a-bb898b822674",
                'include_player_ids' => array($device_id["player_id"]), // add player ids here
                'data' => array("type" => "approval"), // other contents eg:- id, name
                'contents' => $content,
                'headings' => $headings
            );
            $fields = json_encode($fields);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Basic Y2ZmMDE2N2ItMzNlOC00ZDZjLTllNDktMDI0M2QxNTliZTU1'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $response = curl_exec($ch);
            curl_close($ch);
        }
    }
}

if (!function_exists('get_passport_visa_details'))
{
    function get_passport_visa_details()
    {
        $CI = &get_instance();
        $CI->db->select("EPassportExpiryDate,EVisaExpiryDate");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('EIdNo', current_userID());
        $result = $CI->db->get()->row_array();

        return $result;
    }
}

if (!function_exists('addRecurringBtn'))
{
    function addRecurringBtn($RJVMasterAutoId)
    {
        $view = '<div class="" align="center"> <button class="btn btn-primary btn-xs" onclick="addTempTB(' . $RJVMasterAutoId . ')" style="font-size:10px"> + Add </button> </div>';

        return $view;
    }
}

if (!function_exists('dropdown_all_revenue_gl_JV'))
{
    function dropdown_all_revenue_gl_JV($companyID = NULL)
    {
        $CI = &get_instance();
        $companyType = $CI->session->userdata("companyType");

        if (!empty($companyID) && $companyType == 2)
        {
            $companyID = $companyID;
        }
        else
        {
            $companyID = current_companyID();
        }

        $data = $CI->db->query("SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND coa.controllAccountYN = 0
UNION
SELECT
    coa.GLAutoID,
    coa.systemAccountCode,
    coa.GLSecondaryCode,
    coa.GLDescription,
    coa.systemAccountCode,
    coa.subCategory
FROM
    `srp_erp_chartofaccounts` coa
WHERE
    coa.`masterAccountYN` = 0
AND coa.`approvedYN` = 1
AND coa.`isActive` = 1
AND coa.accountCategoryTypeID != 4
AND coa.`companyID` = '{$companyID}'
AND  GLAutoID in(SELECT
    GLAutoID
FROM
    srp_erp_companycontrolaccounts cmp
WHERE
    cmp.companyID = '{$companyID}'
AND (cmp.controlaccounttype = 'ADSP' or cmp.controlaccounttype='PCA' or cmp.controlaccounttype='TAX' or cmp.controlaccounttype='RRVR' or cmp.controlaccounttype='PRVR'))")->result_array();

        if ($companyType == 2)
        {
            return $data;
        }
        else
        {
            $data_arr = array('' => 'Select GL Account');
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
                }
            }

            return $data_arr;
        }
    }
}

if (!function_exists('all_main_category_report_drop'))
{
    function all_main_category_report_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where_in('categoryTypeID', [1, 2]);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_group_report_drop'))
{
    function all_main_category_group_report_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_groupitemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $CI->db->where_in('categoryTypeID', [1, 2]);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('stock_adjustment_control_drop'))
{
    function stock_adjustment_control_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,subCategory");
        $CI->db->FROM('srp_erp_chartofaccounts');
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->WHERE('isBank', 0);
        $CI->db->WHERE('accountCategoryTypeID !=', 4);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select GL Code');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLSecondaryCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['subCategory'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('fetch_company_assigned_attributes'))
{
    function fetch_company_assigned_attributes()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("srp_erp_companyattributeassign.*,srp_erp_systemattributemaster.attributeDescription as attributeDescription,srp_erp_systemattributemaster.attributeType as attributeType,srp_erp_systemattributemaster.columnName as columnName");
        $CI->db->FROM('srp_erp_companyattributeassign');
        $CI->db->join('srp_erp_systemattributemaster', 'srp_erp_companyattributeassign.systemAttributeID = srp_erp_systemattributemaster.systemAttributeID');
        $CI->db->WHERE('companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('isMandatory_completed_document'))
{
    function isMandatory_completed_document($grvID, $code)
    {
        $CI = &get_instance();
        $result = $CI->db->query("SELECT
                                    *
                                FROM
                                    srp_erp_itemmaster_subtemp
                                WHERE
                                    receivedDocumentAutoID = '" . $grvID . "'
                                AND receivedDocumentID = '$code'")->result_array();
        if (empty($result))
        {
            return 0;
        }
        else
        {
            $attributes = fetch_company_assigned_attributes();
            foreach ($attributes as $val)
            {
                if ($val['isMandatory'] == 1)
                {
                    foreach ($result as $value)
                    {
                        if (empty($value[$val['columnName']]))
                        {
                            return 1;
                        }
                    }
                }
            }
            return 0;
        }
    }
}

if (!function_exists('companyWarehouseBinLocations'))
{
    function companyWarehouseBinLocations()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("srp_erp_warehousemaster.*");
        $CI->db->FROM('srp_erp_warehousemaster');
        //$CI->db->join('srp_erp_warehousebinlocation','srp_erp_warehousemaster.wareHouseAutoID = srp_erp_warehousebinlocation.warehouseAutoID','left');
        $CI->db->WHERE('srp_erp_warehousemaster.companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('companyBinLocations'))
{
    function companyBinLocations()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->db->SELECT("srp_erp_warehousebinlocation.*");
        $CI->db->FROM('srp_erp_warehousebinlocation');
        //$CI->db->join('srp_erp_warehousebinlocation','srp_erp_warehousemaster.wareHouseAutoID = srp_erp_warehousebinlocation.warehouseAutoID','left');
        $CI->db->WHERE('srp_erp_warehousebinlocation.companyID', $companyID);
        $data = $CI->db->get()->result_array();
        return $data;
    }
}

if (!function_exists('fetch_tax_type'))
{
    function fetch_tax_type($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('taxMasterAutoID,taxDescription,taxShortCode,IF(taxType = 1,"Sales tax","Purchase tax") as taxType', false);
        $CI->db->from('srp_erp_taxmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Tax Type');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    $data_arr[trim($row['taxMasterAutoID'] ?? '')] = trim($row['taxShortCode'] ?? '') . ' | ' . trim($row['taxDescription'] ?? '') . ' | ' . trim($row['taxType'] ?? '');
                }
                else
                {
                    $data_arr[trim($row['taxMasterAutoID'] ?? '') . '|' . trim($row['taxShortCode'] ?? '')] = trim($row['taxShortCode'] ?? '') . ' | ' . trim($row['taxDescription'] ?? '') . ' | ' . trim($row['taxType'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('emp_master_authenticate'))
{
    function emp_master_authenticate()
    {
        /*****************************************************************
         * - Check company policy on 'Employee Master Edit Approval'
         * and current user has the authentication for make changes
         *****************************************************************/
        $CI = &get_instance();

        $isAuthenticate = getPolicyValues('EMA', 'All');

        if ($isAuthenticate == 1)
        {
            $userID = current_userID();
            $companyID = current_companyID();

            $result = $CI->db->query("SELECT employeeID FROM srp_erp_approvalusers
                            JOIN srp_erp_documentcodes ON srp_erp_approvalusers.documentID=srp_erp_documentcodes.documentID
                            WHERE companyID={$companyID}  AND srp_erp_approvalusers.documentID='EMP' AND employeeID={$userID}")->row('employeeID');

            $isAuthenticate = (!empty($result)) ? 0 : 1;
        }

        return $isAuthenticate;
    }
}

if (!function_exists('isPendingDataAvailable'))
{
    function isPendingDataAvailable()
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $data = $CI->db->query("SELECT EIdNo, EmpSecondaryCode AS empShtrCode, Ename2 FROM srp_employeesdetails AS t1
                        JOIN (
                            SELECT empID FROM srp_erp_employeedatachanges WHERE companyID={$companyID} AND approvedYN=0
                            UNION
                            SELECT empID  FROM srp_erp_employeefamilydatachanges WHERE companyID={$companyID} AND approvedYN=0
                            UNION
                            SELECT empID FROM srp_erp_family_details WHERE approvedYN=0
                        ) AS pendingDataTB ON pendingDataTB.empID=t1.EIdNo
                        WHERE Erp_companyID={$companyID}")->result_array();

        return $data;
    }
}

if (!function_exists('all_discount_drop'))
{
    function all_discount_drop($id = 1, $status = 1)
    {
        $CI = &get_instance();
        $CI->db->SELECT("discountExtraChargeID,Description,type,isChargeToExpense,isTaxApplicable,glCode");
        $CI->db->FROM('srp_erp_discountextracharges');
        $CI->db->where('type', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Types');
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['discountExtraChargeID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        }
        else
        {
            $data_arr = $data;
        }

        return $data_arr;
    }
}

if (!function_exists('inv_action_approval_buyback'))
{
    function inv_action_approval_buyback($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","HCINV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","buy","' . $approval . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('all_tax_drop_fin'))
{
    function all_tax_drop_fin($id = 2, $status = 1)
    {
        $CI = &get_instance();
        $CI->db->SELECT("taxCalculationformulaID,Description");
        $CI->db->FROM('srp_erp_taxcalculationformulamaster');
        $CI->db->where('taxType', $id);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Tax Types');
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['taxCalculationformulaID'] ?? '')] = trim($row['Description'] ?? '');
                }
            }
        }
        else
        {
            $data_arr = $data;
        }

        return $data_arr;
    }
}
if (!function_exists('print_template_pdf'))
{
    function print_template_pdf($documentid, $defaultlink)
    {

        $CI = &get_instance();
        $CI->db->SELECT("TemplateMasterID");
        $CI->db->FROM('srp_erp_printtemplates');
        $CI->db->where('documentID', $documentid);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();
        if (!empty($data))
        {
            $CI->db->SELECT("TempPageNameLink");
            $CI->db->FROM('srp_erp_printtemplatemaster');
            $CI->db->where('TemplateMasterID', $data['TemplateMasterID']);
            $TemplateMasterIDlink = $CI->db->get()->row_array();
            if (!empty($TemplateMasterIDlink))
            {
                return $TemplateMasterIDlink['TempPageNameLink'];
            }
            else
            {
                return $defaultlink;
            }
        }
        else
        {
            return $defaultlink;
        }
    }
}
if (!function_exists('print_group_template_pdf'))
{
    function print_group_template_pdf($documentid, $defaultlink)
    {

        $CI = &get_instance();
        $CI->db->SELECT("TemplateMasterID");
        $CI->db->FROM('srp_erp_printtemplates');
        $CI->db->where('documentID', $documentid);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->row_array();
        if (!empty($data))
        {
            $CI->db->SELECT("TempPageNameLink");
            $CI->db->FROM('srp_erp_printtemplatemaster');
            $CI->db->where('TemplateMasterID', $data['TemplateMasterID']);
            $TemplateMasterIDlink = $CI->db->get()->row_array();
            if (!empty($TemplateMasterIDlink))
            {
                return $TemplateMasterIDlink['TempPageNameLink'];
            }
            else
            {
                return $defaultlink;
            }
        }
        else
        {
            return $defaultlink;
        }
    }
}
if (!function_exists('print_template_paper_size'))
{
    function print_template_paper_size($documentid, $defaultpapersize)
    {

        $CI = &get_instance();
        $CI->db->SELECT('paperSize');
        $CI->db->FROM('srp_erp_printtemplates');
        $CI->db->where('documentID', $documentid);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $pagesize = $CI->db->get()->row_array();

        if (!empty($pagesize['paperSize']))
        {

            return $pagesize['paperSize'];
        }
        else
        {
            return $defaultpapersize;
        }
    }
}

if (!function_exists('generate_encrypt_link'))
{
    function generate_encrypt_link($full_path, $description = null, $extra = null)
    {
        $CI = &get_instance();
        return $CI->encryption_url->generate_encrypt_link($full_path, $description, $extra);
    }
}

if (!function_exists('generate_encrypt_link_start'))
{
    function generate_encrypt_link_start($full_path, $extra = null)
    {
        $CI = &get_instance();
        return $CI->encryption_url->generate_encrypt_link_start($full_path, $extra);
    }
}

if (!function_exists('generate_encrypt_link_back'))
{
    function generate_encrypt_link_back()
    {
        $CI = &get_instance();
        return $CI->encryption_url->generate_encrypt_link_back();
    }
}

if (!function_exists('generate_encrypt_link_only'))
{
    function generate_encrypt_link_only($full_path)
    {
        $CI = &get_instance();
        return $CI->encryption_url->generate_encrypt_link_only($full_path);
    }
}

if (!function_exists('checkPostURL'))
{
    function checkPostURL($URLList)
    {
        $CI = &get_instance();
        $controllerName = $CI->uri->segment(1);
        $functionName = $CI->uri->segment(2);
        $isGet = false;
        if (!empty($URLList))
        {
            foreach ($URLList as $url)
            {
                if ($url == $controllerName . '/' . $functionName)
                {
                    $isGet = true;
                    break;
                }
            }
        }

        if ($isGet)
        {
            if (strtoupper($CI->input->method()) == 'GET')
            {
                header('HTTPS/1.0 403 Forbidden');
                header('HTTP/1.0 403 Forbidden');
                exit;
            }
        }
    }
}

if (!function_exists('get_companyInfo'))
{
    function get_companyInfo()
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', current_companyID());
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_companyControlAccounts'))
{
    function get_companyControlAccounts($accountType)
    {
        $CI = &get_instance();
        $CI->db->select("CA.*");
        $CI->db->from('srp_erp_companycontrolaccounts CCA');
        $CI->db->join('srp_erp_chartofaccounts CA', 'CA.GLAutoID = CCA.GLAutoID');
        $CI->db->where('CCA.companyID', current_companyID());
        $CI->db->where('CCA.controlAccountType', $accountType);
        $result = $CI->db->get()->row_array();
        return $result;
    }
}

if (!function_exists('get_companyInformation'))
{
    function get_companyInformation($companyID)
    {
        $CI = &get_instance();
        $CI->load->database();
        $CI->db->select('*');
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $companyID);
        $companyInfo = $CI->db->get()->row_array();
        return $companyInfo;
    }
}

if (!function_exists('array_filter_reports'))
{
    function array_filter_reports($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $policySegment = getPolicyValues('SEGH', 'All');

        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');

        if ($policySegment != 1)
        {
            $CI->db->where('isShow', 1);
        }
        //$CI->db->where('status', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
                else
                {
                    $data_arr[trim($row['segmentID'] ?? '') . '|' . trim($row['segmentCode'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_companyData'))
{
    function get_companyData($id)
    {
        $CI = &get_instance();
        $CI->db->select('company_code,company_name');
        $CI->db->from('srp_erp_company');
        $CI->db->where('company_id', $id);
        $data = $CI->db->get()->row_array();
        //$data = $CI->db->get()->row('company_name');


        return $data;
    }
}

if (!function_exists('fetch_segment_reports'))
{
    function fetch_segment_reports($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $showSegment = getPolicyValues('SEGH', 'All');

        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('segmentCode,description,segmentID');
        $CI->db->from('srp_erp_segment');
        if ($showSegment != 1)
        {
            $CI->db->where('isShow', 1);
        }
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->order_by('segmentCode');
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_segment')/*'Select Segment'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($id)
                {
                    $data_arr[trim($row['segmentID'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
                else
                {
                    $data_arr[trim($row['segmentID'] ?? '') . '|' . trim($row['segmentCode'] ?? '')] = trim($row['segmentCode'] ?? '') . ' | ' . trim($row['description'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('group_company_drop'))
{
    function group_company_drop()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->lang->load('common', getPrimaryLanguage());

        $data = $CI->db->query("SELECT company_id, CONCAT(company_code, ' - ', company_name) AS cName
                            FROM srp_erp_company compTB
                            JOIN (
                                SELECT companyID FROM srp_erp_companygroupdetails
                                WHERE companyGroupID = ( SELECT companyGroupID FROM srp_erp_companygroupdetails
                                WHERE companyID={$companyID})
                            ) AS grpDet ON grpDet.companyID = compTB.company_id ORDER BY company_name")->result_array();

        return $data;
    }
}


if (!function_exists('group_company_drop_without_current'))
{
    function group_company_drop_without_current()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $CI->lang->load('common', getPrimaryLanguage());

        $data = $CI->db->query("SELECT company_id, CONCAT(company_code, ' - ', company_name) AS cName
                            FROM srp_erp_company compTB
                            JOIN (
                                SELECT companyID FROM srp_erp_companygroupdetails
                                WHERE companyGroupID = ( SELECT companyGroupID FROM srp_erp_companygroupdetails
                                WHERE companyID={$companyID})
                            ) AS grpDet ON grpDet.companyID = compTB.company_id  WHERE compTB.company_id != {$companyID} ORDER BY company_name")->result_array();

        return $data;
    }
}

if (!function_exists('all_employee_drop_with_non_payroll'))
{
    function all_employee_drop_with_non_payroll($status = TRUE, $isDischarged = 0)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        if ($isDischarged == 1)
        {
            $CI->db->where('isDischarged !=1 ');
        }
        $customer = $CI->db->get()->result_array();
        if ($status == TRUE)
        {
            $customer_arr = array('' => $CI->lang->line('common_select_employee'));/*'Select Employee'*/
            if (isset($customer))
            {
                foreach ($customer as $row)
                {
                    $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
                }
            }
        }
        else
        {
            $customer_arr = $customer;
        }

        return $customer_arr;
    }
}

if (!function_exists('drill_down_emp_language'))
{
    function drill_down_emp_language()
    {
        $CI = &get_instance();
        $data = $CI->db->query("SELECT *,
            CASE 
                WHEN description = 'Arabic' THEN 'ع'
                WHEN description = 'English' THEN 'ENG'
                WHEN description = 'French' THEN 'FRA' 
            END AS `languageshortcode` 
        FROM srp_erp_lang_languages
        WHERE isActive = 1 
        ORDER BY languageID DESC")->result_array();
        return $data;
    }
}

if (!function_exists('getallsubGroupCompanies'))
{
    function getallsubGroupCompanies($commaSeperated = false)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $group = $CI->db->query("SELECT companyGroupID,description,groupCode,reportingTo FROM srp_erp_companygroupmaster ORDER BY reportingTo ASC")->result_array();
        $companies = $CI->db->query("SELECT
	companyGroupID,company_code,company_name,srp_erp_companygroupdetails.companyID,typeID,companyGroupDetailID,description
FROM
	srp_erp_companygroupdetails
	LEFT JOIN srp_erp_groupstructuretype ON typeID=groupStructureTypeID
	INNER JOIN srp_erp_company ON srp_erp_companygroupdetails.companyID = srp_erp_company.company_id")->result_array();

        $data = [];
        $tree = getsubgroupcompanyArray($group, $companyID, $companies, $data);
        if (!empty($tree))
        {
            if ($commaSeperated)
            {
                $tree = implode(',', $tree);
            }
        }


        return $tree;
    }
}

if (!function_exists('getsubgroupcompanyArray'))
{
    function getsubgroupcompanyArray(array $elements, $parentId, array $companies, array $data)
    {
        $branch = array();
        $companyID = current_companyID();
        if ($parentId == $companyID)
        {
            $keys = array_keys(array_column($companies, 'companyGroupID'), $parentId);
            $company = array_map(function ($k) use ($companies)
            {
                return $companies[$k];
            }, $keys);

            if (!empty($company))
            {
                foreach ($company as $c)
                {
                    if ($c['companyID'] != '')
                    {
                        array_push($data, $c['companyID']);
                    }
                }
            }
        }
        if (!empty($elements))
        {
            foreach ($elements as $element)
            {

                if ($element['reportingTo'] == $parentId)
                {

                    $keys = array_keys(array_column($companies, 'companyGroupID'), $element['companyGroupID']);
                    $company = array_map(function ($k) use ($companies)
                    {
                        return $companies[$k];
                    }, $keys);

                    if (!empty($company))
                    {
                        foreach ($company as $c)
                        {
                            if ($c['companyID'] != '')
                            {
                                array_push($data, $c['companyID']);
                            }
                        }
                    }

                    $children = getsubgroupcompanyArray($elements, $element['companyGroupID'], $companies, $data);
                }
            }
        }
        return $data;
    }
}


if (!function_exists('getParentgroupMasterID'))
{
    function getParentgroupMasterID()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $CI->db->select('masterID');
        $CI->db->from('srp_erp_companygroupmaster');
        $CI->db->where('companyGroupID', $companyID);
        $data = $CI->db->get()->row_array();
        return $data['masterID'] ?? '';
    }
}



if (!function_exists('get_fm_templateDetails'))
{
    function get_fm_templateDetails($masterID)
    {
        $CI = &get_instance();

        /*To handle the group id*/
        $master_data = $CI->db->query("SELECT companyID, companyType, templateType, reportID, confirmedYN 
                                    FROM srp_erp_companyreporttemplate WHERE companyReportTemplateID = {$masterID}")->row_array();
        $companyID = $master_data['companyID'];
        $chartOfAccount_tb = ($master_data['companyType'] == 2) ? 'srp_erp_groupchartofaccounts' : 'srp_erp_chartofaccounts';
        $gross_rows = $CI->db->query("SELECT detID, description, is_gross_rev FROM srp_erp_companyreporttemplatedetails 
                                 WHERE companyReportTemplateID = {$masterID} AND itemType = 3")->result_array();

        $templateType = $master_data['templateType']; /*1 => Fund management, 2 => MPR*/

        $CI->db->select('detID, description, itemType, sortOrder,itemType,defaultType,is_gross_rev');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID', $masterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID', $companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        $returnData = '';
        $delete_span = '<span class="glyphicon glyphicon-trash" style="color:#d15b47; font-size: 10px;"></span>';
        $edit_span = '<span class="glyphicon glyphicon-pencil" style="color:dodgerblue; font-size: 10px;"></span>';
        foreach ($data as $row)
        {
            $order1 = $row['sortOrder'];
            $masterID = $row['detID'];
            $masterType = ($row['itemType'] == 2) ? 'H' : 'G';

            $returnData .= '<tr><td class="mini-header">' . $order1 . '</td><td class="mini-header"><i class="fa fa-minus-square"></i> <span id="title_str_' . $masterID . '">' . $row['description'] . '</span></td>';
            $returnData .= '<td class="mini-header">' . rpt_template_det_type($row['itemType']) . '</td>';
            $returnData .= '<td class="mini-header" style="text-align: center"><input type="text" class="number sortTxt" name="detSort[]" value="' . $order1 . '"/>';
            $returnData .= '<input type="hidden" name="detSortID[]" value="' . $masterID . '"/></td>';
            $returnData .= '<td class="mini-header" style="text-align: right"> ';
            if ($masterType == 'H')
            {
                $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="new_header_or_group(' . $masterID . ',\'' . $master_data['confirmedYN'] . '\')">';
                $returnData .= '<span class="glyphicon glyphicon-plus" style="color:green; font-size: 10px;"></span></button> &nbsp; ';
            }
            else
            {
                $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="sub_item_config(' . $masterID . ', \'' . $row['description'] . '\', \'' . $masterType . '\')">';
                $returnData .= '<span class="glyphicon glyphicon-cog" style="color:green; font-size: 10px;"></span></button> &nbsp; ';
            }

            $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="edit_title(' . $masterID . ', \'' . $row['description'] . '\', \'' . $master_data['confirmedYN'] . '\',\'' . $row['itemType'] . '\',\'' . $row['defaultType'] . '\',\'' . $row['is_gross_rev'] . '\')">' . $edit_span . '</button> &nbsp; ';
            $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="delete_template_data(' . $masterID . ', \'' . $masterType . '\',\'' . $master_data['confirmedYN'] . '\')">' . $delete_span . '</button></td></tr>';


            $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder,itemType,defaultType,is_gross_rev
                        FROM srp_erp_companyreporttemplatedetails det
                        WHERE masterID = {$masterID} ORDER BY sortOrder")->result_array();

            if ($masterType == 'H')
            {
                foreach ($subData as $sub_row)
                {
                    $detID = $sub_row['detID'];
                    $order2 = $sub_row['sortOrder'];
                    $type = ($sub_row['itemType'] == 1) ? 'S' : 'G';
                    $returnData .= '<tr><td class="sub1">' . $order1 . '.' . $order2 . '</td><td class="sub1"><div style="width: 25px; display: inline-block;"> &nbsp; </div>';
                    $returnData .= '<span id="title_str_' . $detID . '">' . $sub_row['description'] . '</span></td> <td class="sub1">' . rpt_template_det_type($sub_row['itemType']) . '</td>';
                    $returnData .= '<td class="sub1"  style="text-align: center"><input type="text" class="number sortTxt" name="detSort[]" value="' . $order2 . '"/>';
                    $returnData .= '<input type="hidden" name="detSortID[]" value="' . $sub_row['detID'] . '"/></td>';
                    $returnData .= '<td class="sub1" style="text-align: right"><button type="button" class="btn btn-xs btn-default" onclick="sub_item_config(' . $detID . ', \'' . $sub_row['description'] . '\', \'' . $type . '\')">';
                    $returnData .= '<span class="glyphicon glyphicon-cog" style="color:green; font-size: 10px;"></span></button> &nbsp; ';
                    $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="edit_title(' . $detID . ', \'' . $sub_row['description'] . '\',\'' . $master_data['confirmedYN'] . '\',\'' . $sub_row['itemType'] . '\',\'' . $sub_row['defaultType'] . '\',\'' . $sub_row['is_gross_rev'] . '\')">' . $edit_span . '</button>  &nbsp;  ';
                    $returnData .= '<button type="button" class="btn btn-xs btn-default" onclick="delete_template_data(' . $detID . ', \'' . $type . '\',\'' . $master_data['confirmedYN'] . '\')">' . $delete_span . '</button></td> </tr>';

                    if ($sub_row['itemType'] == 1)
                    { /*Sub category*/

                        $glData = $CI->db->query("SELECT linkID, det.glAutoID, sortOrder, templateDetailID,
                                    CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                    FROM srp_erp_companyreporttemplatelinks det
                                    JOIN {$chartOfAccount_tb} chAcc ON chAcc.GLAutoID = det.glAutoID
                                    WHERE templateDetailID = {$detID} ORDER BY sortOrder")->result_array();


                        foreach ($glData as $gl_row)
                        {
                            $order3 = $gl_row['sortOrder'];
                            $returnData .= '<tr><td class="sub2">' . $order1 . '.' . $order2 . '.' . $order3 . '</td><td class="sub2"><div style="width: 55px; display: inline-block;">  ';
                            $returnData .= '&nbsp; </div>' . $gl_row['glData'] . ' </td><td class="sub2"></td><td class="sub2" style="text-align: center"><input type="text" class="number sortTxt" name="glSort[]" value="' . $order3 . '"/>';
                            $returnData .= '<input type="hidden" name="glSortID[]" value="' . $gl_row['linkID'] . '"/></td>';
                            $returnData .= '<td class="sub2"><button type="button" class="btn btn-xs btn-default pull-right" onclick="delete_template_data(' . $gl_row['linkID'] . ', \'GL\',\'' . $master_data['confirmedYN'] . '\')">';
                            $returnData .= '' . $delete_span . '</button></td></tr>';
                        }
                    }

                    if ($sub_row['itemType'] == 3)
                    { /*Group*/

                        $glData = $CI->db->query("SELECT linkID, link.subCategory, link.sortOrder, templateDetailID,
                                    description
                                    FROM srp_erp_companyreporttemplatelinks link
                                    JOIN srp_erp_companyreporttemplatedetails det ON det.detID = link.subCategory
                                    WHERE templateDetailID = {$detID} ORDER BY sortOrder")->result_array();

                        foreach ($glData as $gl_row)
                        {
                            $order3 = $gl_row['sortOrder'];
                            $returnData .= '<tr><td class="sub2">' . $order1 . '.' . $order2 . '.' . $order3 . '</td><td class="sub2"><div style="width: 55px; display: inline-block;">  ';
                            $returnData .= '&nbsp; </div>' . $gl_row['description'] . ' </td><td class="sub2"></td><td class="sub2" style="text-align: center"><input type="text" class="number sortTxt" name="glSort[]" value="' . $order3 . '"/>';
                            $returnData .= '<input type="hidden" name="glSortID[]" value="' . $gl_row['linkID'] . '"/></td>';
                            $returnData .= '<td class="sub2"><button type="button" class="btn btn-xs btn-default pull-right" onclick="delete_template_data(' . $gl_row['linkID'] . ', \'GL\',\'' . $master_data['confirmedYN'] . '\')">';
                            $returnData .= '' . $delete_span . '</button></td></tr>';
                        }
                    }
                }
            }
            else
            {
                /*Group*/

                $subData = $CI->db->query("SELECT linkID, link.subCategory, link.sortOrder, templateDetailID, description
                                    FROM srp_erp_companyreporttemplatelinks link
                                    JOIN srp_erp_companyreporttemplatedetails det ON det.detID = link.subCategory
                                    WHERE templateDetailID = {$masterID} ORDER BY sortOrder")->result_array();

                foreach ($subData as $sub_row)
                {
                    $order2 = $sub_row['sortOrder'];
                    $returnData .= '<tr><td class="sub2">' . $order1 . '.' . $order2 . '</td><td class="sub2"><div style="width: 55px; display: inline-block;">  ';
                    $returnData .= '&nbsp; </div>' . $sub_row['description'] . ' </td><td class="sub2"></td>';
                    $returnData .= '<td class="sub2" style="text-align: center"><input type="text" class="number sortTxt" name="glSort[]" value="' . $order2 . '"/>';
                    $returnData .= '<input type="hidden" name="glSortID[]" value="' . $sub_row['linkID'] . '"/></td>';
                    $returnData .= '<td class="sub2"><button type="button" class="btn btn-xs btn-default pull-right" onclick="delete_template_data(' . $sub_row['linkID'] . ', \'G-Link\')">';
                    $returnData .= '' . $delete_span . '</button></td></tr>';
                }
            }
        }

        $gross_rows_arr = ['' => 'Select a column'];
        $is_gross_rev = 0;
        if (!empty($gross_rows))
        {
            foreach ($gross_rows as $g_row)
            {
                if ($g_row['is_gross_rev'] == 1)
                {
                    $is_gross_rev = $g_row['detID'];
                }
                $gross_rows_arr[$g_row['detID']] = $g_row['description'];
            }
        }

        return ['master_data' => $master_data, 'view' => $returnData, 'gross_rows_arr' => $gross_rows_arr, 'is_gross_rev' => $is_gross_rev];
    }
}

if (!function_exists('rpt_template_det_type'))
{
    function rpt_template_det_type($type)
    {
        $str = '';
        switch ($type)
        {
            case  1:
                $str = 'Sub Category';
                break;
            case  2:
                $str = 'Header';
                break;
            case  3:
                $str = 'Group Total';
                break;
        }
        return $str;
    }
}

if (!function_exists('rpt_template_action'))
{
    function rpt_template_action($id, $des, $sort, $type)
    {
        $onClick = 'onclick="edit_details(' . $id . ', \'' . $des . '\', ' . $type . ', ' . $sort . ')" ';

        $str = '<spsn class="pull-right">';
        $str .= '<span title="Edit" style="color: #3c8dbc" rel="tooltip" class="glyphicon glyphicon-pencil" ' . $onClick . '></span> | ';
        $str .= '<span title="Config" style="color: #3c8dbc" rel="tooltip" class="glyphicon glyphicon-cog" onclick="get_configData(' . $id . ', ' . $type . ')" ></span> | ';
        $str .= '<span title="Delete" style="color:#d15b47;" rel="tooltip" class="glyphicon glyphicon-trash" onclick="delete_reportTemplateDetail(' . $id . ')" ></span>';
        $str .= '</span>';

        return $str;
    }
}

if (!function_exists('load_fm_template'))
{
    function load_fm_template($masterID, $masterData, $print = 0)
    {
        $CI = &get_instance();

        $temMasterID = $masterData['templateID'];
        $dPlace = $masterData['trCurrencyDPlace'];
        $docType = $masterData['docType'];
        $fn_period = $masterData['fn_period_org'];
        $fm_companyID = $masterData['fm_companyID'];
        $companyID = current_companyID();

        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID', $temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID', $companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();

        $amount_con = ($docType == 'FIN_IS') ? '(transactionAmount * -1)' : 'transactionAmount';
        $amount_con2 = ($docType == 'FIN_IS') ? 'IFNULL(transactionAmount,0) * -1' : 'IFNULL(transactionAmount,0)';

        $returnData = '';
        foreach ($data as $row)
        {
            $order1 = $row['sortOrder'];
            $templateID = $row['detID'];


            if ($row['itemType'] == 2)
            {
                $returnData .= '<tr>';
                if ($print == 0)
                {
                    $returnData .= '<td class="mini-header index-td">' . $order1 . '</td>';
                }
                $returnData .= '<td class="mini-header description_td_rpt"><span class="td-main-header"><i class="fa fa-minus-square"></i>  ' . $row['description'] . '</span></td>';
                if ($docType == 'FIN_BS')
                {
                    $returnData .= '<td class="mini-header" style="text-align: right; width: 100px"></td>';
                }
                $returnData .= '<td class="mini-header amount_td_rpt" style="text-align: center; width: 100px"></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID} ORDER BY sortOrder")->result_array();

                foreach ($subData as $sub_row)
                {
                    $detID = $sub_row['detID'];
                    $order2 = $sub_row['sortOrder'];

                    if ($sub_row['itemType'] == 1)
                    { /*Sub category*/
                        $returnData .= '<tr class="hoverTr">';
                        if ($print == 0)
                        {
                            $returnData .= '<td class="sub1 index-td">' . $order1 . '.' . $order2 . '</td>';
                        }
                        $returnData .= '<td class="sub1 description_td_rpt"> ' . $sub_row['description'] . ' </td>';
                        if ($docType == 'FIN_BS')
                        {
                            $returnData .= '<td class="mini-header" style="text-align: right; width: 100px"></td>';
                        }
                        $returnData .= '<td class="sub1 amount_td_rpt" style="text-align: center"> </td></tr>';


                        $glData = $CI->db->query("SELECT linkID, det.glAutoID, sortOrder, templateDetailID, trAmount,
                                    CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                    FROM srp_erp_companyreporttemplatelinks det
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                    LEFT JOIN (
                                        SELECT GLAutoID, {$amount_con} trAmount 
                                        FROM srp_erp_fm_financialdetails WHERE documentMasterAutoID = {$masterID}
                                    ) fnData ON fnData.GLAutoID = det.glAutoID
                                    WHERE templateDetailID = {$detID} ORDER BY sortOrder")->result_array();
                        $pr_val = [];
                        if ($docType == 'FIN_BS')
                        {
                            $pr_val = $CI->db->query("SELECT linkID, det.glAutoID, sortOrder, templateDetailID, SUM(trAmount) trAmount,
                                    CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                    FROM srp_erp_companyreporttemplatelinks det
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                    LEFT JOIN (
                                        SELECT GLAutoID, IFNULL(transactionAmount ,0) trAmount 
                                        FROM srp_erp_fm_financialdetails WHERE documentMasterAutoID IN (
                                            SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                            JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                            WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                            AND fn_period < '{$fn_period}'  
                                        )
                                    ) fnData ON fnData.GLAutoID = det.glAutoID
                                    WHERE templateDetailID = {$detID} GROUP BY det.glAutoID ORDER BY sortOrder")->result_array();

                            $pr_val = array_group_by($pr_val, 'glAutoID');
                        }


                        foreach ($glData as $gl_row)
                        {
                            $order3 = $gl_row['sortOrder'];
                            $glAutoID = $gl_row['glAutoID'];
                            $trAmount = $gl_row['trAmount'];
                            $returnData .= '<tr class="hoverTr">';
                            if ($print == 0)
                            {
                                $returnData .= '<td class="sub2 index-td">' . $order1 . '.' . $order2 . '.' . $order3 . '</td>';
                            }
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">' . $gl_row['glData'] . '</td>';
                            if ($docType == 'FIN_BS')
                            {
                                $pr_amount = (array_key_exists($glAutoID, $pr_val)) ? $pr_val[$glAutoID][0]['trAmount'] : 0;
                                $returnData .= '<td class="sub2" style="text-align: right; width: 100px" id="old_amount_' . $glAutoID . '">' . number_format($pr_amount, $dPlace) . '</td>';
                            }
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">';
                            if ($print == 1)
                            {
                                $returnData .= number_format($trAmount, $dPlace);
                            }
                            else
                            {
                                $trAmount = (!empty($trAmount)) ? round($trAmount, $dPlace) : $trAmount;
                                $returnData .= '<input type="text" class="numeric" value="' . $trAmount . '" onchange="update_amount(this, ' . $glAutoID . ')" 
                                                  onkeyup="calculate_new_amount(this, ' . $glAutoID . ')"/>';
                            }
                            $returnData .= '</td>';
                            if ($docType == 'FIN_BS')
                            {
                                $returnData .= '<td style="text-align: right; width: 100px" id="new_amount_' . $glAutoID . '">' . number_format(($pr_amount + $trAmount), $dPlace) . '</td>';
                            }
                            $returnData .= '</tr>';
                        }
                    }

                    if ($sub_row['itemType'] == 3)
                    { /*Group*/


                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();
                        $amount = $gr_amount = 0;
                        if (!empty($group_glData))
                        {
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                            $amount = $CI->db->query("SELECT ROUND( SUM({$amount_con2}), transactionCurrencyDecimalPlaces) trAmount                                               
                                                      FROM srp_erp_fm_financialdetails 
                                                      WHERE documentMasterAutoID = {$masterID} AND GLAutoID IN ({$where_in})")->row('trAmount');

                            $amount = (empty($amount)) ? 0 : $amount;

                            if ($docType == 'FIN_BS')
                            {
                                $gr_amount = $CI->db->query("SELECT ROUND( SUM({$amount_con2}), transactionCurrencyDecimalPlaces) trAmount                                               
                                                      FROM srp_erp_fm_financialdetails 
                                                      WHERE documentMasterAutoID IN (                                                                                        
                                                            SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                                            JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                                            WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                                            AND fn_period < '{$fn_period}'                                                        
                                                      ) AND GLAutoID IN ({$where_in})")->row('trAmount');

                                $gr_amount = (empty($gr_amount)) ? 0 : $gr_amount;
                            }
                        }

                        //$returnData .= '<tr class="hoverTr"><td colspan="3"><pre>'.$CI->db->last_query().'</pre></td></tr>';
                        $returnData .= '<tr class="hoverTr">';
                        if ($print == 0)
                        {
                            $returnData .= '<td class="sub1 index-td">' . $order1 . '.' . $order2 . '</td>';
                        }
                        $returnData .= '<td class="sub1 description_td_rpt"><span id="title_str_' . $detID . '">' . $sub_row['description'] . '</span></td>';
                        if ($docType == 'FIN_BS')
                        {
                            $returnData .= '<td style="text-align: right; width: 100px" id="oldSubTot_' . $detID . '">' . number_format($gr_amount, $dPlace) . '</td>';
                        }
                        $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right;" id="subTot_' . $detID . '" >' . number_format($amount, $dPlace) . '</td>';
                        if ($docType == 'FIN_BS')
                        {
                            $returnData .= '<td style="text-align: right; width: 100px" id="newSubTot_' . $detID . '">' . number_format(($gr_amount + $amount), $dPlace) . '</td>';
                        }
                        $returnData .= '</tr>';
                    }
                }
            }
            else
            {
                /*Group*/

                $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$templateID}
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();
                $amount = $gr_amount2 = 0;
                if (!empty($group_glData))
                {
                    $where_in_array = array_column($group_glData, 'glAutoID');
                    $where_in = implode(',', $where_in_array);
                    $amount = $CI->db->query("SELECT ROUND( SUM({$amount_con2}), transactionCurrencyDecimalPlaces) trAmount 
                                              FROM srp_erp_fm_financialdetails 
                                              WHERE documentMasterAutoID = {$masterID} AND GLAutoID IN ({$where_in})")->row('trAmount');

                    $amount = (empty($amount)) ? 0 : $amount;

                    if ($docType == 'FIN_BS')
                    {
                        $gr_amount2 = $CI->db->query("SELECT ROUND( SUM({$amount_con2}), transactionCurrencyDecimalPlaces) trAmount 
                                              FROM srp_erp_fm_financialdetails 
                                              WHERE documentMasterAutoID IN (                                                                                        
                                                    SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                                    JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                                    WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                                    AND fn_period < '{$fn_period}'  
                                              )                                              
                                              AND GLAutoID IN ({$where_in})")->row('trAmount');

                        $gr_amount2 = (empty($gr_amount2)) ? 0 : $gr_amount2;
                    }
                }

                //$returnData .= '<tr class="hoverTr"><td colspan="3"><pre>'.$CI->db->last_query().'</pre></td></tr>';
                $cols = ($print == 0) ? 3 : 2;
                $returnData .= '<tr><td colspan="' . $cols . '">&nbsp;</td></tr>';
                $returnData .= '<tr class="hoverTr">';
                if ($print == 0)
                {
                    $returnData .= '<td class="mini-header index-td">' . $order1 . '</td>';
                }
                $returnData .= '<td class="mini-header"><span class="td-main-header description_td_rpt"><i class="fa fa-minus-square"></i>  ' . $row['description'] . '</span></td>';
                if ($docType == 'FIN_BS')
                {
                    $returnData .= '<td class="sub1 total_black_rpt" style="text-align: right; width: 100px" id="oldSubTot_' . $templateID . '">' . number_format($gr_amount2, $dPlace) . '</td>';
                }
                $returnData .= '<td class="sub1 total_black_rpt amount_td_rpt" style="text-align: right;" id="subTot_' . $templateID . '" >' . number_format($amount, $dPlace) . '</td>';
                if ($docType == 'FIN_BS')
                {
                    $returnData .= '<td class="total_black_rpt" style="text-align: right; width: 100px" id="newSubTot_' . $templateID . '">' . number_format(($gr_amount2 + $amount), $dPlace) . '</td>';
                }
                $returnData .= '</tr>';
            }
        }

        return $returnData;
    }
}

if (!function_exists('update_warehouse_items'))
{
    function update_warehouse_items()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $warehouseitems = $CI->db->query("SELECT
    srp_erp_warehouseitems.itemAutoID,
  srp_erp_warehouseitems.itemSystemCode,
  srp_erp_warehouseitems.wareHouseAutoID,
    srp_erp_warehouseitems.currentStock as warehousestock,
  IFNULL(round(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),2),0) as itemledQty,
  round(ifnull((srp_erp_warehouseitems.currentStock),0) - IFNULL(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),0),2) as diff
FROM
    srp_erp_warehouseitems
LEFT JOIN srp_erp_itemledger ON (srp_erp_warehouseitems.itemAutoID = srp_erp_itemledger.itemAutoID
AND srp_erp_warehouseitems.wareHouseAutoID = srp_erp_itemledger.wareHouseAutoID)
WHERE srp_erp_warehouseitems.companyID=$companyID
GROUP BY
    srp_erp_warehouseitems.itemAutoID,
    srp_erp_warehouseitems.wareHouseAutoID,
  srp_erp_itemledger.itemAutoID,
  srp_erp_itemledger.wareHouseAutoID
having diff !=0 ORDER BY srp_erp_itemledger.itemAutoID")->result_array();

        if (!empty($warehouseitems))
        {
            $limit = count($warehouseitems);

            $warehouseitemsupdate = $CI->db->query("update srp_erp_warehouseitems wm join
(SELECT
    srp_erp_warehouseitems.itemAutoID,
  srp_erp_warehouseitems.itemSystemCode,
  srp_erp_warehouseitems.wareHouseAutoID,
    srp_erp_warehouseitems.currentStock as warehousestock,
  IFNULL(round(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),2),0) as itemledQty,
  round(ifnull((srp_erp_warehouseitems.currentStock),0) - IFNULL(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),0),2) as diff
FROM
    srp_erp_warehouseitems
LEFT JOIN srp_erp_itemledger ON (srp_erp_warehouseitems.itemAutoID = srp_erp_itemledger.itemAutoID
AND srp_erp_warehouseitems.wareHouseAutoID = srp_erp_itemledger.wareHouseAutoID)
WHERE srp_erp_warehouseitems.companyID=$companyID
GROUP BY
    srp_erp_warehouseitems.itemAutoID,
    srp_erp_warehouseitems.wareHouseAutoID,
  srp_erp_itemledger.itemAutoID,
  srp_erp_itemledger.wareHouseAutoID

having diff !=0 ORDER BY srp_erp_itemledger.itemAutoID LIMIT $limit) t1 on wm.itemautoID=t1.itemautoID and wm.wareHouseAutoID=t1.wareHouseAutoID
set wm.currentStock=t1.itemledQty");
        }
    }
}

if (!function_exists('update_item_master'))
{
    function update_item_master()
    {

        $companyID = current_companyID();
        $CI = &get_instance();
        $itemmaster = $CI->db->query("SELECT
    srp_erp_itemmaster.itemAutoID,
  srp_erp_itemmaster.itemSystemCode,
    srp_erp_itemmaster.currentStock as warehousestock,
  IFNULL(round(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),2),0) as itemledQty,
  round(IFNULL((srp_erp_itemmaster.currentStock),0) - IFNULL(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),0),2)as diff
FROM
    srp_erp_itemmaster
LEFT JOIN srp_erp_itemledger ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
WHERE srp_erp_itemmaster.companyID=$companyID
GROUP BY
    srp_erp_itemmaster.itemAutoID,
  srp_erp_itemledger.itemAutoID
having diff !=0 ORDER BY srp_erp_itemledger.itemAutoID")->result_array();

        if (!empty($itemmaster))
        {
            $limit = count($itemmaster);

            $itemmasterupdate = $CI->db->query("update srp_erp_itemmaster join
(SELECT
    srp_erp_itemmaster.itemAutoID,
  srp_erp_itemmaster.itemSystemCode,
    srp_erp_itemmaster.currentStock as warehousestock,
  IFNULL(round(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),2),0) as itemledQty,
  round(IFNULL((srp_erp_itemmaster.currentStock),0) - IFNULL(sum(srp_erp_itemledger.transactionQTY/srp_erp_itemledger.convertionRate),0),2)as diff
FROM
    srp_erp_itemmaster
LEFT JOIN srp_erp_itemledger ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
WHERE srp_erp_itemmaster.companyID=$companyID
GROUP BY
    srp_erp_itemmaster.itemAutoID,
  srp_erp_itemledger.itemAutoID
having diff !=0 ORDER BY srp_erp_itemledger.itemAutoID LIMIT $limit) t1 on srp_erp_itemmaster.itemautoid=t1.itemautoID
set srp_erp_itemmaster.currentStock=t1.itemledQty");
        }
    }
}

if (!function_exists('drilldown_emp_location_drop'))
{
    function drilldown_emp_location_drop()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $currentuser = current_userID();

        $data = $CI->db->query("SELECT locationmaster.* 
                    FROM srp_erp_location locationmaster 
                    LEFT JOIN srp_erp_locationemployees emplocation on emplocation.locationID = locationmaster.locationID 
                    WHERE locationmaster.companyID = $companyID AND isCostLocation = 1 AND empID = $currentuser")->result_array();
        return $data;
    }
}

if (!function_exists('all_financeyear_drop_location'))
{ //finance drop location wise codegeneration
    function all_financeyear_drop_location($policyFormat = FALSE)
    {
        $convertFormat = convert_date_format();
        $CI = &get_instance();
        $CI->db->select('companyFinanceYearID,beginingDate,endingDate');
        $CI->db->from('srp_erp_companyfinanceyear');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        //$CI->db->where('isCurrent', 1);
        $CI->db->where('isClosed', 0);
        $CI->db->order_by("beginingDate", "desc");
        $data = $CI->db->get()->result_array();
        $data_arr = array(0 => 'N/A');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($policyFormat)
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = trim(format_date(
                        $row['beginingDate'],
                        $convertFormat
                    )) . ' - ' . trim(format_date($row['endingDate'], $convertFormat));
                }
                else
                {
                    $data_arr[trim($row['companyFinanceYearID'] ?? '')] = trim($row['beginingDate'] ?? '') . ' - ' . trim($row['endingDate'] ?? '');
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_bucketweight_drop'))
{
    function all_bucketweight_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("weightAutoID,bucketWeight");
        $CI->db->FROM('srp_erp_buyback_bucketweight');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['weightAutoID'] ?? '')] = trim($row['bucketWeight'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_document_auto_approval'))
{
    function get_document_auto_approval($documentCode)
    {
        $CI = &get_instance();
        $CI->db->SELECT("levelNo");
        $CI->db->FROM('srp_erp_approvalusers');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('documentID', $documentCode);
        $data = $CI->db->get()->row_array();
        if (!empty($data) && $data['levelNo'] == 0)
        {
            return 0;
        }
        elseif (is_array($data) && $data['levelNo'] > 0)
        {
            return 1;
        }
        else
        {
            return 2;
        }
    }
}

if (!function_exists('get_financial_period_date_wise'))
{

    function get_financial_period_date_wise($date)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceperiod');
        $CI->db->WHERE('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where("'{$date}' BETWEEN dateFrom AND dateTo");

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('get_financial_period_date_wise_srm_api'))
{

    function get_financial_period_date_wise_srm_api($date, $companyID)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_companyfinanceperiod');
        $CI->db->WHERE('companyID', $companyID);
        $CI->db->where("'{$date}' BETWEEN dateFrom AND dateTo");

        return $CI->db->get()->row_array();
    }
}

if (!function_exists('document_status'))
{
    function document_status($docID, $masterID, $isOn = 0)
    {
        $tableName = null;
        $masterColumn = null;
        $confirmColumn = 'confirmedYN';
        $approvalColumn = 'approvedYN';
        $documentCode = null;
        $companyColumn = 'companyID';
        $currLevelColumn = 'currentLevelNo';
        $documentDateColumn = 'createdDateTime';

        switch ($docID)
        {
            case 'FS':
                $tableName = 'srp_erp_pay_finalsettlementmaster';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                break;

            case 'LO':
                $tableName = 'srp_erp_pay_emploan';
                $masterColumn = 'ID';
                $documentCode = 'loanCode';
                $currLevelColumn = 'currentApprovalLevel';
                break;

            case 'VD':
                $tableName = 'srp_erp_variablepaydeclarationmaster';
                $masterColumn = 'vpMasterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'documentDate';
                break;

            case 'DO':
                $tableName = 'srp_erp_deliveryorder';
                $masterColumn = 'DOAutoID';
                $documentCode = 'DOCode';
                $documentDateColumn = 'DODate';
                break;

            case 'SAR':
                $tableName = 'srp_erp_pay_salaryadvancerequest';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'request_date';
                break;

            case 'LEC':
                $tableName = 'srp_erp_pay_leaveencashment';
                $masterColumn = 'masterID';
                $documentCode = 'documentCode';
                $documentDateColumn = 'encashment_date';
                break;

            case 'HDR':
                $tableName = 'srp_erp_hr_letterrequests';
                $masterColumn = 'request_id';
                $documentCode = 'documentCode';
                $documentDateColumn = 'request_date';
                break;

            case 'EC':
                $tableName = 'srp_erp_expenseclaimmaster';
                $masterColumn = 'expenseClaimMasterAutoID';
                $documentCode = 'expenseClaimCode';
                $documentDateColumn = 'expenseClaimDate';
                $currLevelColumn = 1;
                break;

            case 'INV':
                $tableName = 'srp_erp_itemmaster';
                $masterColumn = 'itemAutoID';
                $documentCode = 'itemSystemCode';
                $confirmColumn = 'masterConfirmedYN';
                $approvalColumn = 'masterApprovedYN';
                $currLevelColumn = 'masterCurrentLevelNo';
                break;
            case 'SUP':
                $tableName = 'srp_erp_suppliermaster';
                $masterColumn = 'supplierAutoID';
                $documentCode = 'supplierSystemCode';
                $confirmColumn = 'masterConfirmedYN';
                $approvalColumn = 'masterApprovedYN';
                $currLevelColumn = 'masterCurrentLevelNo';
                break;
            case 'CS':
                $tableName = 'srp_erp_commisionscheme';
                $masterColumn = 'schemeID';
                $documentCode = 'documentCode';
                $confirmColumn = 'confirmedYN';
                $approvalColumn = 'approvedYN';
                $currLevelColumn = 'currentLevelNo';
                break;

            default:
                return ['error' => 1, 'message' => 'Document ID not configured for status check.<br/>Please contact the system support team.'];
        }

        $ci = &get_instance();
        $companyID = current_companyID();

        $ci->db->select("{$confirmColumn} AS confirmVal, {$approvalColumn} AS approvalVal, {$currLevelColumn} AS appLevel, 
        {$documentCode} AS docCode, {$documentDateColumn} AS createdDate");
        $ci->db->from("{$tableName}");
        $ci->db->where("{$masterColumn}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);
        $document_output = $ci->db->get()->row_array();


        if (empty($document_output))
        {
            return ['error' => 1, 'message' => 'Document master record not found'];
        }

        if ($document_output['approvalVal'] == 1)
        {
            return ['error' => 1, 'message' => 'This document already <b>approved</b>,<br/>Please refresh the page and check again.'];
        }

        if ($isOn == 1)
        { /* Is on refer back */
            return [
                'error' => 0,
                'tableName' => $tableName,
                'masterColumn' => $masterColumn,
                'confirmColumn' => $confirmColumn,
                'approvalColumn' => $approvalColumn,
                'currLevelColumn' => $currLevelColumn,
                'data' => $document_output
            ];
        }

        if ($document_output['confirmVal'] == 1)
        {
            return ['error' => 1, 'message' => 'This document already <b>confirmed</b>,<br/>Please refresh the page and check again.'];
        }

        return ['error' => 0, 'message' => 'still not confirmed', 'data' => $document_output];
    }
}
if (!function_exists('load_contract_action_buyback'))
{
    function load_contract_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID, $documentID, $confirmedYN, $isDeleted, $confirmedbyempid, $isSystemGenerated, $closedYN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        else if ($documentID == "QUT")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Quotation","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else if ($documentID == "CNT")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Contract","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else if ($documentID == "SO")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Order","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $documentID . '","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '<a onclick=\'fetchPage("system/quotation_contract/erp_quotation_contract_buyback",' . $poID . ',"Edit Quotation or Contract","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedbyempid == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_customer_contract(' . $poID . ',\'' . $documentID . '\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\',\'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a onclick="document_drill_down_View_modal(\'' . $poID . '\',\'' . $documentID . '\')"><i title="Drill Down" rel="tooltip" class="fa fa-bars" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        //$status .= '<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '<a onclick="load_printtemp(' . $poID . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($documentID == 'QUT' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="document_version_View_modal(\'' . $documentID . '\',\'' . $poID . '\')"><i title="Documents" rel="tooltip" class="fa fa-files-o" aria-hidden="true"></i></a>&nbsp;&nbsp;|';
                $status .= ' <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;&nbsp;';
            }
        }
        if ($documentID == 'CNT' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }
        if ($documentID == 'SO' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'' . $documentID . '\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'' . $documentID . '\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }
        if ($approved == 1 && $isDeleted == 0 && $closedYN == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'contract_close_buyback("' . $poID . '"); \'><i title="Close" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>';
        }
        /*        if ($POConfirmedYN != 0 && $POConfirmedYN != 2) {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
                    $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
                }*/
        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('con_action_approval_buyback'))
{
    function con_action_approval_buyback($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}
//set class if finance period is closed AND set class if finance period is current
if (!function_exists('set_is_closed_is_current_class'))
{
    function set_is_closed_is_current_class($isClosed, $isCurrent)
    {
        $status = '';
        if ($isClosed == 1 && $isCurrent == 1)
        {
            $status = 'Closed Current';
        }
        else if ($isClosed == 1 && $isCurrent == 0)
        {
            $status = 'Closed';
        }
        else if ($isClosed == 0 && $isCurrent == 1)
        {
            $status = 'Current';
        }
        else
        {
            $status = 'noClosed';
        }

        return $status;
    }
}


if (!function_exists('edit_customerPriceSetup'))
{
    function edit_customerPriceSetup($cpsAutoID, $confirmedYN, $approvedYN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick="Customer_priceSetup_DocumentView(' . $cpsAutoID . ',\'CPS\');"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';

        if ($approvedYN != 1 && $confirmedYN == 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="referback_Customer_priceSetup(' . $cpsAutoID . ',\'CPS\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($confirmedYN != 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'fetchPage("system/customer/erp_new_Customer_priceSetup",' . $cpsAutoID . ',"Edit Customer Price","CPS"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_Customer_priceSetup(' . $cpsAutoID . ',\'CPS\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('customer_PriceSetup_approval_action'))
{
    function customer_PriceSetup_approval_action($cpsAutoID, $approvalLevelID, $approvedYN, $documentApprovedID, $documentID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $cpsAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CPS\',\'' . $cpsAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('edit_customerWisePriceList'))
{
    function edit_customerWisePriceList($customerAutoID)
    {
        $status = '<span class="pull-right">';
        if ($customerAutoID)
        {
            $status .= '<a onclick="attach_CustomerWisePrice_modal(' . $customerAutoID . ',\'Customer\',\'CUS\');"><span title="Sales Prices" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('customer_SalesItemList_action'))
{
    function customer_SalesItemList_action($customerPriceID)
    {
        $status = '<span class="pull-right">';
        if ($customerPriceID)
        {
            $status .= '<a onclick="delete_Customer_itemprice(' . $customerPriceID . ')"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('all_insurance_type_drop'))
{
    function all_insurance_type_drop()
    {
        $CI = &get_instance();
        $CI->db->select("insuranceTypeID,insuranceType");
        $CI->db->from('srp_erp_invoiceinsurancetypes');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('masterTypeID', 0);
        $insurancetype = $CI->db->get()->result_array();

        $insurancetypearr = array('' => 'Select Insurance Type');
        if (isset($insurancetype))
        {
            foreach ($insurancetype as $row)
            {
                $insurancetypearr[trim($row['insuranceTypeID'] ?? '')] = (trim($row['insuranceType'] ?? ''));
            }
        }
        return $insurancetypearr;
    }
}
if (!function_exists('load_invoice_action_insurancetype'))
{
    function load_invoice_action_insurancetype($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isSytemGenerated)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if (empty($tempInvoiceID))
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

            if ($isDeleted == 1)
            {
                $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                if ($isSytemGenerated != 1)
                {
                    $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_insurance",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                else
                {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                }
            }

            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
            {
                $status .= '<a onclick="referback_customer_invoice(' . $poID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'insurance\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_invoicetype/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            if ($approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'insurance\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_invoicetype/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }

        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="open_receipt_voucher_modal(' . $poID . ')" title="Create Receipt Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a> ';
        }


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('inv_action_approval_insurance'))
{
    function inv_action_approval_insurance($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","insurance","' . $approval . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('load_invoice_action_margin'))
{
    function load_invoice_action_margin($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isSytemGenerated, $isPreliminaryPrinted)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $policyPIE = getPolicyValues('PIE', 'All');
        $status = '<span class="pull-right">';
        $checked = '';
        $title = 'Preliminary Not Submitted';
        if ($policyPIE && $policyPIE == 1 && $approved != 1)
        {
            if ($isPreliminaryPrinted == 1)
            {
                $checked = 'checked';
                $title = 'Preliminary Submitted';
            }
            $status .= '<span title="' . $title . '" rel="tooltip"><input type="checkbox" id="isprimilinaryPrinted_' . $poID . '" name="isprimilinaryPrinted"  data-size="mini" data-on-text="Preliminary Printed" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Preliminary Not Printed" data-label-width="0" onclick="update_preliminary_print_status(' . $poID . ')" ' . $checked . '></span>&nbsp;&nbsp;|&nbsp;&nbsp;';
            // $status .= '<a><span title="Preliminary Printed" rel="tooltip" class="fa fa-flag"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';         
        }
        if (empty($tempInvoiceID))
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

            if ($isDeleted == 1)
            {
                $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                if ($isSytemGenerated != 1)
                {
                    $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_margin",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                else
                {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                }
            }


            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
            {
                $status .= '<a onclick="referback_customer_invoice(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'margin\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_invoicetype/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            if ($approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'margin\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_invoicetype/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }

        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> &nbsp; | &nbsp; <a onclick="open_receipt_voucher_modal(' . $poID . ')" title="Create Receipt Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
        }


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('documentallapproval'))
{
    function documentallapproval($poID, $Level, $approved, $ApprovedID, $document, $iapprovedYNdummy, $DocumentCode, $payrollMonth, $bankGLAutoID, $isFromCancel = 0)
    {
        $CI = &get_instance();
        $status = '<span class="pull-right">';
        $docTemplate = '';
        if ($document == "CINV")
        {
            $docTemplate = fetch_approval_tem_by_link($document);
        }

        $att_status = '<a onclick=\'document_attachment_view("' . $poID . '","' . $document . '"); \'><span title="" rel="tooltip" class="glyphicon glyphicon-paperclip" data-original-title="Attachment"></span></a>&nbsp;';

        /*  $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0) {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';

        } else {*/
        if ($document == "HCINV")
        {
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '", "buy"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "GRV")
        {
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_grv("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "PV")
        {
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;<a onclick=\'fetch_approval_paymentvoucher("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "FA")
        {
            $status .= $att_status . '<a onclick=\'fetch_approval_fa("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $iapprovedYNdummy . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_fa("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $approved . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "FAD")
        {
            $status .= $att_status . '<a onclick=\'fetch_approval_fad("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $iapprovedYNdummy . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_fad("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $approved . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "ADSP")
        {
            $status .= $att_status . '<a onclick=\'fetch_approval_adsp("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $iapprovedYNdummy . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_adsp("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $approved . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if (($document == "SP" || $document == "SPN"))
        {
            $status .= $att_status . '<a onclick=\'load_paysheetApproval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $payrollMonth . '","' . $DocumentCode . '","' . $iapprovedYNdummy . '","' . $document . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'load_paysheetApproval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $payrollMonth . '","' . $DocumentCode . '","' . $approved . '","' . $document . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "BRC")
        {
            $status .= $att_status . '<a onclick=\'documentPageView_modal("BR","' . $poID . '","' . $bankGLAutoID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $bankGLAutoID . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "LO")
        {
            $status .= $att_status . '<a onclick=\'load_emp_loanDet("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $iapprovedYNdummy . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'load_emp_loanDet("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $approved . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "FS")
        {
            $status .= $att_status . '<a onclick=\'load_approvalView("' . $poID . '","' . $Level . '","' . $iapprovedYNdummy . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'load_approvalView("' . $poID . '","' . $Level . '","' . $approved . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "BD")
        {
            $status .= $att_status . '<a onclick=\'fetchPage("system/budget/erp_budget_detail_page_approval","' . $poID . '","Budget Approval","' . $Level . '","ALLApproval"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;';
        }
        else if ($document == "LA")
        {
            $status .= '<a onclick=\'load_emp_leaveDet_new("' . $poID . '","' . $iapprovedYNdummy . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'load_emp_leaveDet_new("' . $poID . '","' . $approved . '","' . $Level . '","' . $isFromCancel . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "EC")
        {
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_ec("' . $poID . '","' . $document . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "LEC")
        {
            $document_type = $payrollMonth;
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '","' . $document_type . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open">';
            $status .= '</span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_lec("' . $poID . '","' . $document_type . '","' . $Level . '","' . $ApprovedID . '"); \'>';
            $status .= '<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else if ($document == "SAR")
        {
            $document_type = $payrollMonth;
            $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open">';
            $status .= '</span></a>&nbsp;&nbsp| &nbsp;&nbsp;<a onclick=\'fetch_approval_sar("' . $poID . '","' . $Level . '","' . $ApprovedID . '"); \'>';
            $status .= '<span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {

            if ($docTemplate == 'system/invoices/invoice_approval_insurance')
            {
                $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '","insurance"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $bankGLAutoID . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            }
            else if ($docTemplate == 'system/invoices/invoices_approval_margin')
            {
                $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '","margin"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $bankGLAutoID . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            }
            else
            {
                $status .= $att_status . '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","' . $bankGLAutoID . '"); \'><span title="Approve" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            }
        }



        /*  }
      */  //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('fetch_approval_tem_by_link'))
{
    function fetch_approval_tem_by_link($docid)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $approvalcode = '';
        switch ($docid)
        {
            case 'CINV':
                $approvalcode = 'CINV_APP';
                break;
            default:
                return ['error' => 1, 'message' => 'Document ID not configured for Template check.<br/>Please contact the system support team.'];
        }

        $templatelink = $CI->db->query("SELECT TempPageNameLink as link

FROM
    `srp_erp_templatemaster`
where documentCode='{$approvalcode}' and TempMasterID in (select TempMasterID from srp_erp_templates where companyID='{$companyID}')")->row_array();
        return !empty($templatelink) ? $templatelink['link'] : null;
    }
}


if (!function_exists('load_delivery_order_action')) {
    function load_delivery_order_action($id, $confirmedYN, $approved, $createdUserID, $doc_code, $isDeleted, $confirmedByEmpID) {
        $CI =& get_instance();
        $current_user = current_userID();
        $doc_code = str_replace('/', '-', $doc_code);

        $status = '<div class="btn-group" style="display: flex;justify-content: center;">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        Actions <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-md-left">';

        $status .= '<li><a onclick="attachment_modal(' . $id . ', \'Delivery Order\', \'DO\', ' . $confirmedYN . ')"><i class="glyphicon glyphicon-paperclip" style="color: #4caf50;"></i> Attachment</a></li>';

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="open_delivery_order(' . $id . ')"><i class="glyphicon glyphicon-pencil" style="color: #116f5e;"></i> Edit</a></li>';
        }

        if (in_array($current_user, [$createdUserID, $confirmedByEmpID]) && $approved == 0 && $confirmedYN == 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="refer_back_delivery_order(' . $id . ');"><i class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></i> Refer Back</a></li>';
        }

        $status .= '<li><a target="_blank" onclick="documentPageView_modal(\'DO\',\'' . $id . '\')"><i class="glyphicon glyphicon-eye-open"></i> View</a></li>';

        $status .= '<li><a target="_blank" href="' . site_url('Delivery_order/load_order_confirmation_view') . '/' . $id . '/' . $doc_code . '"><i class="glyphicon glyphicon-print"></i> Print</a></li>';

        if ($approved == 1) {
            $status .= '<li><a onclick="send_email(' . $id . ')"><i class="fa fa-envelope" aria-hidden="true" style="color: #ff2d7c;"></i> Send Mail</a></li>';
            $status .= '<li><a onclick="Generate_Invoice(' . $id . ')"><i class="fa fa-file" aria-hidden="true"></i> Generate Invoice</a></li>';
            $status .= '<li><a onclick="traceDocument(' . $id . ',\'DO\')"><i class="fa fa-search" style="color: #fdc45e;"></i> Trace Document</a></li>';
        }

        if ($confirmedYN != 1 && $isDeleted == 0) {
            $status .= '<li><a onclick="confirm_order_front(' . $id . ')"><i class="glyphicon glyphicon-ok"></i> Confirm</a></li>';
            $status .= '<li><a onclick="delete_item(' . $id . ');"><i class="glyphicon glyphicon-trash"></i> Delete</a></li>';
        }

        $status .= '</ul></div>';

        return $status;
    }
}

if (!function_exists('delivery_order_approval_action'))
{
    function delivery_order_approval_action($id, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="attachment_modal(' . $id . ', \'Delivery Order\', \'DO\')"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a> &nbsp;| &nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick="fetch_approval(' . $id . ',' . $ApprovedID . ',' . $Level . ')"><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a> &nbsp;';
        }
        else
        {
            $status .= '<a onclick="documentPageView_modal(\'' . $documentID . '\',' . $id . ',\'\',' . $approval . ')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a> &nbsp;';
        }

        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('fetch_signature_level'))
{
    function fetch_signature_level($document_id)
    {
        $CI = &get_instance();
        $where = ['companyID' => current_companyID(), 'documentID' => $document_id];
        return $CI->db->get_where('srp_erp_documentcodemaster', $where)->row('approvalSignatureLevel');
    }
}

if (!function_exists('send_approvalEmail_manufacturing'))
{
    function send_approvalEmail_manufacturing($mailData, $attachment = 0, $path = 0)
    {
        $CI = &get_instance();

        $CI->load->library('email_manual');

        $approvalEmpID = $mailData['approvalEmpID'];
        $documentCode = $mailData['documentCode'];
        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = $CI->config->item('email_smtp_host');
        $config['smtp_user'] = $CI->config->item('email_smtp_username');
        $config['smtp_pass'] = $CI->config->item('email_smtp_password');
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);
        if (array_key_exists("from", $mailData))
        {
            $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
        }
        else
        {
            $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
        }

        if (!empty($param))
        {
            $CI->email->to($toEmail);
            $CI->email->subject($subject);
            $CI->email->message($CI->load->view('system/email_template/email_approval_template_log_manufacturing', $param, TRUE));
            if ($attachment == 1)
            {
                $CI->email->attach($path);
            }
        }

        $CI->email->send();
        $CI->email->clear(TRUE);
        send_push_notification($approvalEmpID, $subject, $documentCode, 1);
    }
}

if (!function_exists('all_supplier_drop_isactive_inactive'))
{
    function all_supplier_drop_isactive_inactive($pID, $docID)
    {
        $ci = &get_instance();
        $companyID = current_companyID();
        $masterID = $pID;

        switch ($docID)
        {
            case 'GRV':
            case 'SRN':
                $tableName = 'srp_erp_grvmaster';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'grvPrimaryCode';
                $companyColumn = 'srp_erp_grvmaster.companyID';
                $documentid = 'grvAutoID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_grvmaster.supplierID';
                $wherecondition = '';
                break;
            case 'BSI':
                $tableName = 'srp_erp_paysupplierinvoicemaster';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'bookingInvCode';
                $companyColumn = 'srp_erp_paysupplierinvoicemaster.companyID';
                $documentid = 'InvoiceAutoID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID';
                $wherecondition = '';

                break;
            case 'PV':
                $tableName = 'srp_erp_paymentvouchermaster';
                $masterColumn = 'partyID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'PVcode';
                $companyColumn = 'srp_erp_paymentvouchermaster.companyID';
                $documentid = 'payVoucherAutoId';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_paymentvouchermaster.partyID';
                $wherecondition = $ci->db->where('pvType', 'Supplier');
                break;
            case 'PO':
                $tableName = 'srp_erp_purchaseordermaster';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry,srp_erp_purchaseordermaster.contractAutoID';
                $documentsyscode = 'purchaseOrderCode';
                $companyColumn = 'srp_erp_purchaseordermaster.companyID';
                $documentid = 'purchaseOrderID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_purchaseordermaster.supplierID';
                $wherecondition = '';
                break;
            case 'SR':
                $tableName = 'srp_erp_stockreturnmaster';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'stockReturnCode';
                $companyColumn = 'srp_erp_stockreturnmaster.companyID';
                $documentid = 'stockReturnAutoID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_stockreturnmaster.supplierID';
                $wherecondition = '';
                break;
            case 'DN':
                $tableName = 'srp_erp_debitnotemaster';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'debitNoteCode';
                $companyColumn = 'srp_erp_debitnotemaster.companyID';
                $documentid = 'debitNoteMasterAutoID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_debitnotemaster.supplierID';
                $wherecondition = '';
                break;
            case 'PVM':
                $tableName = 'srp_erp_pvadvancematch';
                $masterColumn = 'supplierID As 
supplierID,sup.isActive,sup.supplierAutoID,sup.supplierName,sup.supplierSystemCode,sup.supplierCountry';
                $documentsyscode = 'matchSystemCode';
                $companyColumn = 'srp_erp_pvadvancematch.companyID';
                $documentid = 'matchID';
                $jointbl = 'srp_erp_suppliermaster sup';
                $condition = 'sup.supplierAutoID = srp_erp_pvadvancematch.supplierID';
                $wherecondition = '';
                break;


            default:
                return ['error' => 1, 'message' => 'Document ID not configured for status check.'];
        }


        $ci->db->select("{$documentsyscode} AS documentsystemcode, {$masterColumn}");
        $ci->db->from("{$tableName}");
        $ci->db->join("{$jointbl}", "{$condition}");
        $ci->db->where("{$documentid}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);
        $wherecondition;
        $output = $ci->db->get()->row_array();
        return $output;
    }
}
if (!function_exists('getPolicydescription_masterid'))
{
    function getPolicydescription_masterid($id)
    {
        $ci = &get_instance();
        $ci->db->select("groupPolicyDescription,grouppolicymasterID");
        $ci->db->from("srp_erp_grouppolicymaster");
        $ci->db->where('grouppolicymasterID', $id);
        $outputval = $ci->db->get()->row_array();
        return $outputval;
    }
}
if (!function_exists('getgrouppolicyvalues'))
{
    function getgrouppolicyvalues($id)
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_grouppolicymaster_value');
        $CI->db->where('groupPolicymasterID', $id);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['systemValue'] ?? '')] = trim($row['value'] ?? '');
            }
        }
        return $data_arr;
    }
}
if (!function_exists('getPolicydescription_values_detail'))
{
    function getPolicydescription_values_detail($id)
    {
        $ci = &get_instance();
        $ci->db->select("*");
        $ci->db->from("srp_erp_grouppolicy");
        $ci->db->where('groupPolicymasterID', $id);
        $ci->db->where('groupID', current_companyID());
        $outputval = $ci->db->get()->row_array();
        return $outputval;
    }
}
if (!function_exists('getPolicyValuesgroup'))
{
    function getPolicyValuesgroup($code, $documentCode)
    {
        $CI = &get_instance();
        $policyValues = null;
        $companyid = current_companyID();
        // $companygroupid = $CI->db->query("SELECT parentID FROM `srp_erp_companygroupdetails` where companyID = $companyid")->row_array();
        $policyvalue = $CI->db->query("SELECT * FROM `srp_erp_grouppolicy` where groupID = (SELECT parentID FROM 
            `srp_erp_companygroupdetails` where companyID = $companyid) AND code = '{$code}'")->row_array();

        if (!empty($policyvalue))
        {
            if ($policyvalue['isYN'] == 1)
            {
                $policyValues = 1;
            }
            else
            {
                $policyValues = 0;
            }
        }
        else
        {
            $policyValues = 1;
        }

        return $policyValues;
    }
}
if (!function_exists('getusergroupcomapny'))
{
    function getusergroupcomapny()
    {
        $CI = &get_instance();
        $Values = null;
        $companyid = current_companyID();
        // $companygroupid = $CI->db->query("SELECT parentID FROM `srp_erp_companygroupdetails` where companyID = $companyid")->row_array();
        $iscompanyusergroup = $CI->db->query("SELECT companyGroupID AS company_id FROM srp_erp_companygroupmaster WHERE companyGroupID 
= '{$companyid}' ")->row_array();
        if (!empty($iscompanyusergroup))
        {
            $Values = 1;
        }
        else
        {
            $Values = 2;
        }
        return $Values;
    }
}
if (!function_exists('edit_item_master_report'))
{
    function edit_item_master_report($itemAutoID, $isActive = 0, $isSubItemExist = NULL)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick="item_pricing_report(' . $itemAutoID . ');"><span title="Price Inquiry" rel="tooltip" class="glyphicon glyphicon-tag"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        if (isset($isSubItemExist) && $isSubItemExist == 1)
        {
            $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }


        $status .= '<a class="text-yellow" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';

        if ($isActive)
        {
            /*            <input type="checkbox" id="itemchkbox_' . $itemAutoID . '" name="itemchkbox" onchange="changeitemactive(' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br>*/
            $status .= '<a onclick="fetchPage(\'system/item/erp_item_new_report\',' . $itemAutoID . ',\'View Item\')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

            /*| &nbsp;&nbsp;<a onclick="delete_item_master(' . $itemAutoID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>*/
        }
        else
        {
            $status .= '<a onclick="fetchPage(\'system/item/erp_item_new_report\',' . $itemAutoID . ',\'View Item\')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}


if (!function_exists('fetch_item_dropdown'))
{
    function fetch_item_dropdown($state = TRUE, $seccode = false, $limit = 10, $issystemcode = 0) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select('itemSystemCode,itemName,itemAutoID,seconeryItemCode');
        $CI->db->from('srp_erp_itemmaster');
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->limit($limit);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Items');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $itemSecondaryCodePolicy = is_show_secondary_code_enabled();
                if ($itemSecondaryCodePolicy)
                {
                    if ($issystemcode == 1)
                    {
                        $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                    }
                    else
                    {
                        $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                    }
                }
                else
                {
                    if ($seccode == true)
                    {
                        if ($issystemcode == 1)
                        {
                            $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                        }
                        else
                        {
                            $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                        }
                    }
                    else
                    {
                        if ($issystemcode == 1)
                        {
                            $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['seconeryItemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                        }
                        else
                        {
                            $data_arr[trim($row['itemAutoID'] ?? '')] = trim($row['itemSystemCode'] ?? '') . ' | ' . trim($row['itemName'] ?? '');
                        }
                    }
                }
            }
        }

        return $data_arr;
    }
}

if (!function_exists('update_warehouseitems'))
{
    function update_warehouseitems($itemAutoID, $barcode, $isActive, $companyLocalSellingPrice)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $data['barCodeNo'] = $barcode;
        $data['ActiveYN'] = $isActive;
        $data['salesPrice'] = $companyLocalSellingPrice;
        $CI->db->where('itemAutoID', $itemAutoID);
        $CI->db->where('companyID', $companyID);
        $results = $CI->db->update('srp_erp_warehouseitems', $data);
        if ($results)
        {
            return true;
        }
    }
}


if (!function_exists('attendance_date_format_drop'))
{
    function attendance_date_format_drop()
    {
        $drop['mm/dd/yyyy'] = 'mm/dd/yyyy';
        $drop['mm-dd-yyyy'] = 'mm-dd-yyyy';
        $drop['dd/mm/yyyy'] = 'dd/mm/yyyy';
        $drop['dd-mm-yyyy'] = 'dd-mm-yyyy';

        return $drop;
    }
}

if (!function_exists('attendance_date_format_convert'))
{
    function attendance_date_format_convert($date_format, $date_str)
    {
        $date = null;
        switch ($date_format)
        {
            case 'mm-dd-yyyy':
                $date_str = explode('-', $date_str);
                $date = date('Y-m-d', strtotime($date_str[2] . '-' . $date_str[0] . '-' . $date_str[1]));
                break;

            case 'mm/dd/yyyy':
                $date_str_val = explode('/', $date_str);
                $date = date('Y-m-d', strtotime($date_str));
                break;

            case 'dd/mm/yyyy':
                $date_str = explode('/', $date_str);
                $date = date('Y-m-d', strtotime($date_str[2] . '-' . $date_str[1] . '-' . $date_str[0]));
                break;

            case 'dd-mm-yyyy':
                $date_str = explode('-', $date_str);
                $date = date('Y-m-d', strtotime($date_str[2] . '-' . $date_str[1] . '-' . $date_str[0]));
                break;
        }

        return $date;
    }

    if (!function_exists('load_invoice_action_suom'))
    {
        function load_invoice_action_suom($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isPreliminaryPrinted)
        {
            $CI = &get_instance();
            $CI->load->library('session');
            $status = '<span class="pull-right">';
            $policyPIE = getPolicyValues('PIE', 'All');
            $checked = '';
            $title = 'Preliminary Not Submitted';
            if ($policyPIE && $policyPIE == 1 && $approved != 1)
            {
                if ($isPreliminaryPrinted == 1)
                {
                    $checked = 'checked';
                    $title = 'Preliminary Submitted';
                }
                $status .= '<span title="' . $title . '" rel="tooltip"><input type="checkbox" id="isprimilinaryPrinted_' . $poID . '" name="isprimilinaryPrinted"  data-size="mini" data-on-text="Preliminary Printed" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Preliminary Not Printed" data-label-width="0" onclick="update_preliminary_print_status(' . $poID . ')" ' . $checked . '></span>&nbsp;&nbsp;|&nbsp;&nbsp;';
                // $status .= '<a><span title="Preliminary Printed" rel="tooltip" class="fa fa-flag"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';         
            }
            if (empty($tempInvoiceID))
            {
                $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

                if ($isDeleted == 1)
                {
                    $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }

                if ($POConfirmedYN != 1 && $isDeleted == 0)
                {
                    $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_suom",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }

                if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
                {
                    $status .= '<a onclick="referback_customer_invoice(' . $poID . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }

                $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'SUOM\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_suom/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
                if ($approved == 1)
                {
                    $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
                }
                if ($POConfirmedYN != 1 && $isDeleted == 0)
                {
                    $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                    $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
                }
            }
            else
            {
                $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'SUOM\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_suom/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            }

            if ($approved == 1)
            {
                $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
            }


            $status .= '</span>';

            return $status;
        }
    }

    if (!function_exists('inv_action_approval_suom'))
    {
        function inv_action_approval_suom($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
        {
            $status = '<span class="pull-right">';
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            if ($approved == 0)
            {
                $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
            }
            else
            {
                $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","SUOM","' . $approval . '"  ); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
            }
            //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


            $status .= '</span>';

            return $status;
        }
    }
}
if (!function_exists('drill_down_navigation_dropdown_dashboard_user'))
{
    function drill_down_navigation_dropdown_dashboard_user()
    {
        $CI = &get_instance();
        $group_company_arr = array();
        $currencyid = $CI->common_data['company_data']['company_default_currencyID'];
        $companyID = current_companyID();
        $userID = current_userID();
        $isGroupUser = $CI->common_data['isGroupUser'];
        if ($isGroupUser == 1)
        {
            $db2 = $CI->load->database('db2', TRUE);
            $db2->select('EidNo');
            $db2->where("companyID", $companyID);
            $db2->where("empID", $userID);
            $groupdetails = $db2->get("groupusercompanies")->row_array();
            $idno = $groupdetails['EidNo'];
            $group_company = $db2->query("select companyID as companyID,CONCAT(srp_erp_company.company_code,' | ',srp_erp_company.company_name) as company,empID as eid FROM `groupusercompanies` LEFT JOIN srp_erp_company ON srp_erp_company.company_id=groupusercompanies.companyID WHERE EidNo= '{$idno}' AND srp_erp_company.company_default_currencyID = '$currencyid'")->result_array();
            foreach ($group_company as $row)
            {
                $group_company_arr[trim($row['companyID'] ?? '')] = trim($row['company'] ?? '');
            }
        }
        return $group_company_arr;
    }
}
if (!function_exists('documentyeardropdown'))
{
    function documentyeardropdown()
    {
        $CI = &get_instance();
        $data = $CI->db->query("SELECT
	documentYear
FROM
	`srp_erp_generalledger_groupmonitoring`
WHERE
	documentYear IS NOT NULL
GROUP BY
	documentYear
	ORDER BY
	documentYear desc
")->result_array();
        if (empty($data))
        {
            $data_arr = array('' => 'Select Year');
        }
        else if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['documentYear'] ?? '')] = trim($row['documentYear'] ?? '');
            }
        }


        return $data_arr;
    }
}
if (!function_exists('all_currency_new_drop_groupmonitoring'))
{
    function all_currency_new_drop_groupmonitoring($status = TRUE)/*Load all currency*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("srp_erp_companycurrencyassign.currencyID,srp_erp_currencymaster.CurrencyCode,srp_erp_currencymaster.CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $CI->db->join('srp_erp_companycurrencyassign', 'srp_erp_companycurrencyassign.currencyID = srp_erp_currencymaster.currencyID');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $currency = $CI->db->get()->result_array();
        if ($status)
        {
            $currency_arr = array('' => $CI->lang->line('common_select_currency')/*'Select Currency'*/);
        }
        else
        {
            $currency_arr = [];
        }
        if (isset($currency))
        {
            foreach ($currency as $row)
            {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '');
            }
        }

        return $currency_arr;
    }
}

if (!function_exists('load_cheque_register_action'))
{
    function load_cheque_register_action($chequeRegisterID)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $status .= '<a onclick="cheque_register_master_modal(' . $chequeRegisterID . ');"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" style=""></span></a>&nbsp;|&nbsp;<a onclick="cheque_register_detail_modal(' . $chequeRegisterID . ');"><span title="Detail" rel="tooltip" class="glyphicon glyphicon-th-list" style=""></span></a>';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_all_cheque_register_drop'))
{
    function load_all_cheque_register_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("chequeRegisterDetailID,chequeNo,srp_erp_chequeregister.description");
        $CI->db->join('srp_erp_chequeregister', 'srp_erp_chequeregister.chequeRegisterID = srp_erp_chequeregisterdetails.chequeRegisterID', 'left');
        $CI->db->where('srp_erp_chequeregisterdetails.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_chequeregisterdetails.status !=', 2);
        $CI->db->FROM('srp_erp_chequeregisterdetails');
        $output = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Cheque No');
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['chequeRegisterDetailID'] ?? '')] = trim($row['chequeNo'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('load_default_bank'))
{
    function load_default_bank()
    {
        $CI = &get_instance();
        $CI->db->SELECT("GLAutoID");

        $CI->db->where('srp_erp_chartofaccounts.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('srp_erp_chartofaccounts.isDefaultlBank ', 1);
        $CI->db->FROM('srp_erp_chartofaccounts');
        $output = $CI->db->get()->row_array();

        if (!empty($output))
        {
            return $output['GLAutoID'];
        }
        else
        {
            return '';
        }
    }
}

if (!function_exists('get_defaultSSOSetup'))
{
    function get_defaultSSOSetup()/*Load all SSO masters setup*/
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $result = $CI->db->query("SELECT reportValue FROM srp_erp_sso_reporttemplatedetails
                                  WHERE companyID={$companyID} AND masterID=5 ORDER BY reportID ")->result_array();

        if (!empty($result))
        {
            $data = array();
            $data['sso_employee'] = $result[0]['reportValue'];
            $data['sso_employer'] = $result[1]['reportValue'];

            return $data;
        }
        else
        {
            return $result;
        }
    }
}

if (!function_exists('get_csrf_token_data'))
{
    function get_csrf_token_data()
    {
        $CI = &get_instance();
        return [
            'name' => $CI->security->get_csrf_token_name(),
            'hash' => $CI->security->get_csrf_hash()
        ];
    }
}

if (!function_exists('load_leave_type_drop'))
{
    function load_leave_type_drop()
    {
        $CI = &get_instance();
        $company_id = current_companyID();

        $output = $CI->db->query("SELECT lv_type.leaveTypeID, lv_type.description
                                FROM srp_erp_leavegroup AS lv_grp
                                JOIN srp_erp_leavegroupdetails AS lv_grp_det ON lv_grp.leaveGroupID = lv_grp_det.leaveGroupID
                                JOIN srp_erp_leavetype AS lv_type ON lv_type.leaveTypeID = lv_grp_det.leaveTypeID
                                WHERE lv_type.companyID = {$company_id} AND lv_grp.companyID = {$company_id} AND lv_type.isAnnualLeave = 1
                                GROUP BY lv_grp_det.leaveTypeID ORDER BY lv_type.description")->result_array();

        //$data_arr = ['' => 'Select Leave type'];
        $data_arr = [];
        if (isset($output))
        {
            foreach ($output as $row)
            {
                $data_arr[trim($row['leaveTypeID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('fetch_aws_companyimage'))
{
    function fetch_aws_companyimage($logoname)
    {
        $CI = &get_instance();
        $CI->load->library('s3');
        if ($logoname != '')
        {
            $path = 'images/logo/' . $logoname;
            $assetmasterattachment = $CI->s3->createPresignedRequest($path, '+1 hour');
        }
        else
        {
            $assetmasterattachment = $CI->s3->createPresignedRequest('images/item/no-image.png', '+1 hour');
        }

        $generatedHTML = "<center><img class='img-thumbnail c-img-size' src='$assetmasterattachment'><center>";
        return $generatedHTML;
    }
}


if (!function_exists('all_tax_formula_drop'))
{
    function all_tax_formula_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("taxCalculationformulaID,Description");
        $CI->db->FROM('srp_erp_taxcalculationformulamaster');
        $CI->db->where('isClaimable', 0);
        $CI->db->where('taxType', 2);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Tax Formula');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['taxCalculationformulaID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }


        return $data_arr;
    }
}

if (!function_exists('gl_description_template_mpr_group'))
{
    function gl_description_template_mpr_group($templateID, $requestType, $id)
    {
        $CI = &get_instance();
        $CI->db->select("reportID");
        $CI->db->from('srp_erp_companyreporttemplate');
        $CI->db->where('companyReportTemplateID', $templateID);
        $type = $CI->db->get()->row_array();
        $masterCategory = '';
        if ($type['reportID'] == 5)
        {
            $masterCategory = 'PL';
        }
        else if ($type['reportID'] == 6)
        {
            $masterCategory = 'BS';
        }

        $companyID = current_companyID();
        $template = [];

        if ($requestType == 'S')
        { /*For sub category*/
            $template = $CI->db->query("SELECT GLAutoID, CONCAT(systemAccountCode, ' | ', GLSecondaryCode,  ' | ', GLDescription) desStr
                        FROM srp_erp_chartofaccounts chTB
                        WHERE masterCategory = '{$masterCategory}' AND companyID = {$companyID} AND masterAccountYN = 0
                        AND NOT EXISTS (
                           SELECT glAutoID FROM srp_erp_companyreporttemplatelinks linkTB
                           JOIN srp_erp_companyreporttemplatedetails detTB ON detTB.detID = linkTB.templateDetailID
                           WHERE detTB.companyReportTemplateID = {$templateID} AND linkTB.glAutoID = chTB.GLAutoID 
                        )
                        AND chTB.subCategory = (
                          SELECT IF(d.accountType = 'I', 'PLI', 'PLE') accType
                          FROM srp_erp_companyreporttemplatedetails detTB 
                          JOIN srp_erp_companyreporttemplatedetails d ON detTB.masterID = d.detID 
                          WHERE detTB.detID = {$id}
                        )")->result_array();
        }
        else
        { /*For Group Total*/
            $this_masterID = $CI->db->get_where('srp_erp_companyreporttemplatedetails', ['detID' => $id])->row('masterID');
            $masterWhere = ($this_masterID != null) ? 'AND masterID = ' . $this_masterID : '';
            $template = $CI->db->query("SELECT description AS desStr, detID AS GLAutoID
                        FROM srp_erp_companyreporttemplatedetails detTB
                        WHERE companyReportTemplateID = {$templateID} AND itemType=1 {$masterWhere}
                        AND NOT EXISTS (
                           SELECT subCategory FROM srp_erp_companyreporttemplatelinks lkTB
                           WHERE templateDetailID = {$id} AND lkTB.subCategory = detTB.detID                           
                        )")->result_array();
        }


        $template_arr = [];
        if (isset($template))
        {
            foreach ($template as $row)
            {
                $template_arr[trim($row['GLAutoID'] ?? '')] = trim($row['desStr'] ?? '');
            }
        }
        return $template_arr;
    }
}
/*get last two financial year to group company*/
if (!function_exists('get_last_two_financial_year_group'))
{

    function get_last_two_financial_year_group()
    {
        $CI = &get_instance();
        $CI->db->SELECT("*");
        $CI->db->FROM('srp_erp_groupfinanceyear');
        $CI->db->WHERE('groupID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('isActive', 1);
        $CI->db->ORDER_BY('beginingDate DESC');

        return $CI->db->get()->result_array();
    }
}
if (!function_exists('get_group_company'))
{
    function get_group_company($status = true)
    {
        $CI = &get_instance();
        $CI->db->SELECT("companyID,CONCAT(company_code, ' - ', company_name) AS company");
        $CI->db->FROM('srp_erp_companygroupdetails companygrpdet');
        $CI->db->JOIN('srp_erp_company company', 'company.company_id = companygrpdet.companyID', 'INNER');
        $CI->db->where('companyGroupID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        if ($status == false)
        {
            $data_arr = array('' => 'Select A Company');
        }


        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['companyID'] ?? '')] = trim($row['company'] ?? '');
            }
        }


        return $data_arr;
    }
}

if (!function_exists('RV_action_approval_suom'))
{
    function RV_action_approval_suom($poID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= ' &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\',\'suom\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        // $status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('fetch_company_by_id'))
{
    function fetch_company_by_id()
    {
        $CI = &get_instance();
        $CI->db->SELECT("company_id as companyID,CONCAT(company_code, ' - ', company_name) AS company");
        $CI->db->FROM('srp_erp_company');
        $CI->db->where('company_id', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select A Company');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['companyID'] ?? '')] = trim($row['company'] ?? '');
            }
        }


        return $data_arr;
    }
}

if (!function_exists('subscription_action'))
{
    function subscription_action($invId, $paymentStatus)
    {
        $class = ($paymentStatus == 0) ? 'btn-un-paid' : 'btn-paid';
        $text = ($paymentStatus == 0) ? 'Unpaid' : 'Paid';

        $str = '<div class="action-div"><button class="btn-subscription ' . $class . '" onclick="load_invoice(' . $invId . ')">' . $text . '</button></div>';
        return $str;
    }
}

//
if (!function_exists('corporate_goal_confirm_status'))
{
    function corporate_goal_confirm_status($status)
    {
        $element = '<center>';
        if ($status == 0)
        {
            $element .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($status == 1)
        {
            $element .= '<span class="label label-success">&nbsp;</span>';
        }
        $element .= '</center>';

        return $element;
    }
}

if (!function_exists('corporate_goal_approval_action'))
{
    function corporate_goal_approval_action($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'corporate_goal_approval_modal("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'corporate_goal_approval_modal("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open act-btn-margin"></span></a>&nbsp;&nbsp';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('cg_approval_drilldown'))
{
    function cg_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        elseif ($con == 1)
        {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('url_exists'))
{
    function url_exists($url)
    {
        $url = str_replace("http://", "", $url);
        if (strstr($url, "/"))
        {
            $url = explode("/", $url, 2);
            $url[1] = "/" . $url[1];
        }
        else
        {
            $url = array($url, "/");
        }

        $fh = fsockopen($url[0], 80);
        if ($fh)
        {
            fputs($fh, "GET " . $url[1] . " HTTP/1.1\nHost:" . $url[0] . "\n\n");
            if (fread($fh, 22) == "HTTP/1.1 404 Not Found")
            {
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }
}

if (!function_exists('file_type_icon'))
{
    function file_type_icon($file_type)
    {
        $file_type = strtolower(trim($file_type));
        $icon = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';

        switch ($file_type)
        {
            case "xlsx":
            case "xlsxm":
            case "xls":
                $icon = '<i class="color fa fa-file-excel-o" aria-hidden="true"></i>';
                break;

            case "doc":
            case "docx":
                $icon = '<i class="color fa fa-file-word-o" aria-hidden="true"></i>';
                break;

            case "ppt":
            case "pptx":
                $icon = '<i class="color fa fa-file-powerpoint-o" aria-hidden="true"></i>';
                break;

            case "txt":
                $icon = '<i class="color fa fa-file-text-o" aria-hidden="true"></i>';
                break;

            case "jpg":
            case "jpeg":
            case "gif":
            case "png":
                $icon = '<i class="color fa fa-file-image-o" aria-hidden="true"></i>';
                break;
        }

        return $icon;
    }
}


if (!function_exists('subscription_pay_status_action'))
{
    function subscription_pay_status_action($paymentStatus)
    {

        $str = '<span class="label label-danger""> &nbsp; </span>';
        if ($paymentStatus == -1)
        {
            $str = '<span class="label label-warning"> &nbsp; </span>';
        }
        else if ($paymentStatus == 1)
        {
            $str = '<span class="label label-success"> &nbsp; </span>';
        }

        return '<div style="text-align: center">' . $str . '</div>';
    }
}

if (!function_exists('document_approval_total_value'))
{
    function document_approval_total_value($amount, $decimalplaces, $currency)
    {

        $value = '';
        if (is_numeric($amount))
        {
            $value = "<div class='pull-right'><b>" . number_format($amount, $decimalplaces) . " : </b>" . $currency . "</div>";
        }
        return $value;
    }
}

if (!function_exists('get_pv_rv_based_on_policy'))
{ //get finance period using companyFinancePeriodID.
    function get_pv_rv_based_on_policy($documentID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $currentUser = $CI->common_data['current_userID'];
        $empuserGroupID = $CI->db->query("SELECT userGroupID FROM srp_erp_employeenavigation WHERE companyID=$companyID AND empID= '$currentUser' ")->row_array();
        $userGroupID = $empuserGroupID['userGroupID'];
        $result = $CI->db->query("SELECT * FROM srp_erp_documentpolicyusergroup WHERE companyID=$companyID AND documentID= '$documentID' AND userGroupID=$userGroupID AND companypolicymasterID=37")->row_array();
        if (!empty($result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

if (!function_exists('system_hrPeriodTypes_drop'))
{
    function system_hrPeriodTypes_drop()
    {
        $data = get_instance()->db->get('srp_erp_systemhrperiodtypes')->result_array();

        $data_arr = [];
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[$row['id']] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('CustomerCreditLimit'))
{ //Customer credit limit validation in Quotation contract & Invoice (Buyback).
    function CustomerCreditLimit($customerID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $currentCreditLimit = array();
        $customerCreditLimit = $CI->db->query("SELECT customerCreditLimit, creditToleranceAmount, customerAutoID, customerCurrencyID FROM srp_erp_customermaster WHERE companyID = {$companyID} AND customerAutoID = {$customerID}")->row_array();
        if ($customerCreditLimit['customerCreditLimit'] > 0)
        {

            /*Contract Total Amount*/
            $contractTotal = $CI->db->query("SELECT IFNULL(SUM(contractAmount), 0) AS contractAmount
FROM (
	SELECT SUM( srp_erp_contractdetails.transactionAmount /  srp_erp_contractmaster.companyLocalExchangeRate) AS contractAmount
	FROM srp_erp_contractmaster
	LEFT JOIN srp_erp_contractdetails ON srp_erp_contractdetails.contractAutoID = srp_erp_contractmaster.contractAutoID 
    WHERE srp_erp_contractmaster.companyID = {$companyID} AND customerID = {$customerID}
GROUP BY srp_erp_contractmaster.contractAutoID)tbl")->row_array();

            /*Invoice Total Amount*/
            $invoiceTotal = $CI->db->query("SELECT 
customerID, IFNULL(SUM(invoiceAmount), 0) as invoiceAmount
FROM (
SELECT
	customerID,
	(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL((det.transactionAmount/2),0)-(IFNULL((det.detailtaxamount/companyLocalExchangeRate),0)))))+IFNULL((det.transactionAmount/companyLocalExchangeRate),0)) as invoiceAmount
FROM
	`srp_erp_customerinvoicemaster_temp`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster_temp.invoiceAutoID )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster_temp.InvoiceAutoID )
	WHERE
	`srp_erp_customerinvoicemaster_temp`.`companyID` = {$companyID} 
	AND `customerID` = {$customerID} 
)tbl")->row_array();

            /*Advance Match Amount*/
            $advanceMatch = $CI->db->query("SELECT customerID, IFNULL(SUM(advanceMatchAmount), 0) as advanceMatchAmount FROM (SELECT customerID, srp_erp_rvadvancematch.matchID, SUM(srp_erp_rvadvancematchdetails.transactionAmount / srp_erp_rvadvancematch.companyLocalExchangeRate) as advanceMatchAmount FROM `srp_erp_rvadvancematch` LEFT JOIN srp_erp_rvadvancematchdetails ON srp_erp_rvadvancematchdetails.matchID = srp_erp_rvadvancematch.matchID AND (InvoiceAutoID <> 0 OR InvoiceAutoID IS NOT NULL) WHERE srp_erp_rvadvancematch.companyID = {$companyID} AND customerID = {$customerID} GROUP BY srp_erp_rvadvancematch.matchID) tbl")->row_array();

            /*Credit note / Sales return Match Amount*/
            $creditNoteMatch = $CI->db->query("SELECT IFNULL(SUM(rvMatchedAmount), 0) as rvMatchedAmount
FROM(
SELECT 
customerID,
SUM(srp_erp_customerreceiptdetail.transactionAmount / srp_erp_customerreceiptmaster.companyLocalExchangeRate) as rvMatchedAmount
FROM
srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId

WHERE srp_erp_customerreceiptmaster.companyID = {$companyID} AND (type = 'SLR' OR type = 'creditnote') AND customerID = {$customerID}
GROUP BY srp_erp_customerreceiptmaster.receiptVoucherAutoId
)tbl")->row_array();


            /*Contract Match Amount*/
            $contractMatch = $CI->db->query("SELECT IFNULL(SUM(invoiceMatchAmount), 0) AS invoiceMatchAmount FROM 
(
SELECT
	customerID, contractAutoID,
	(((IFNULL(addondet.taxPercentage,0)/100)*((IFNULL((det.transactionAmount/2),0)-(IFNULL((det.detailtaxamount/companyLocalExchangeRate),0)))))+IFNULL((det.transactionAmount/companyLocalExchangeRate),0)) as invoiceMatchAmount
FROM
	`srp_erp_customerinvoicemaster_temp`
	LEFT JOIN ( SELECT SUM( transactionAmount ) AS transactionAmount, sum( totalafterTax ) AS detailtaxamount, invoiceAutoID, contractAutoID FROM srp_erp_customerinvoicedetails_temp GROUP BY invoiceAutoID ) det ON ( `det`.`invoiceAutoID` = srp_erp_customerinvoicemaster_temp.invoiceAutoID )
	LEFT JOIN ( SELECT SUM( taxPercentage ) AS taxPercentage, InvoiceAutoID FROM srp_erp_customerinvoicetaxdetails_temp GROUP BY InvoiceAutoID ) addondet ON ( `addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster_temp.InvoiceAutoID )
	WHERE
	`srp_erp_customerinvoicemaster_temp`.`companyID` = {$companyID} 
	AND (contractAutoID <> 0 OR contractAutoID IS NOT NULL) AND `customerID` = {$customerID}
	)tbl")->row_array();

            /*Advance And Invoice Total*/
            $advanceInvoiceTotal = $CI->db->query("SELECT IFNULL(SUM(invAdvanceTotalAmount), 0) as invAdvanceTotalAmount FROM
(SELECT customerID, SUM(srp_erp_customerreceiptdetail.transactionAmount / srp_erp_customerreceiptmaster.companyLocalExchangeRate) as invAdvanceTotalAmount 
FROM
srp_erp_customerreceiptmaster
LEFT JOIN srp_erp_customerreceiptdetail ON srp_erp_customerreceiptdetail.receiptVoucherAutoId = srp_erp_customerreceiptmaster.receiptVoucherAutoId
WHERE srp_erp_customerreceiptmaster.companyID = {$companyID} AND (type = 'Invoice' OR type = 'Advance') AND customerID = {$customerID} AND approvedYN = 1
GROUP BY srp_erp_customerreceiptmaster.receiptVoucherAutoId 
)tbl")->row_array();

            /*Credit Note Total*/
            $creditNoteTotal = $CI->db->query("SELECT IFNULL(SUM(creditnoteTotal), 0) as creditnoteTotal FROM
(SELECT	customerID, SUM( srp_erp_creditnotedetail.transactionAmount / srp_erp_creditnotemaster.companyLocalExchangeRate) AS creditnoteTotal
FROM
	`srp_erp_creditnotemaster`
	LEFT JOIN srp_erp_creditnotedetail ON srp_erp_creditnotedetail.creditNoteMasterAutoID = srp_erp_creditnotemaster.creditNoteMasterAutoID
WHERE
	`srp_erp_creditnotemaster`.`companyID` = {$companyID}  AND customerID = {$customerID}
	GROUP BY srp_erp_creditnotemaster.creditNoteMasterAutoID
)tbl")->row_array();

            /*Sales Return Total*/
            $salesReturnTotal = $CI->db->query("SELECT IFNULL(SUM(salesReturnTotal), 0) as salesReturnTotal FROM (
SELECT
customerID,
SUM(totalValue / companyLocalExchangeRate) as salesReturnTotal
FROM srp_erp_salesreturnmaster
	LEFT JOIN srp_erp_salesreturndetails ON srp_erp_salesreturndetails.salesReturnAutoID = srp_erp_salesreturnmaster.salesReturnAutoID 
WHERE srp_erp_salesreturnmaster.companyID = {$companyID} AND customerID = {$customerID}
	GROUP BY srp_erp_salesreturnmaster.salesReturnAutoID) tbl
")->row_array();

            $totadd = ($contractTotal['contractAmount'] + $invoiceTotal['invoiceAmount'] + $advanceMatch['advanceMatchAmount'] + $creditNoteMatch['rvMatchedAmount']) - ($contractMatch['invoiceMatchAmount'] + $advanceInvoiceTotal['invAdvanceTotalAmount'] + $creditNoteTotal['creditnoteTotal'] + $salesReturnTotal['salesReturnTotal']);

            $default_currency = currency_conversionID($customerCreditLimit['customerCurrencyID'], $CI->common_data['company_data']['company_default_currencyID']);
            $customerLimit_Local = (($customerCreditLimit['customerCreditLimit'] + $customerCreditLimit['creditToleranceAmount']) / $default_currency['conversion']);
            //            var_dump($customerLimit_Local . '--' . $customerLimit_Local . '==' . $default_currency['conversion']);
            $currentCreditLimit['amount'] = $customerLimit_Local - $totadd;
            $currentCreditLimit['assigned'] = 1;

            return $currentCreditLimit;
        }
        else
        {
            $currentCreditLimit['amount'] = 0;
            $currentCreditLimit['assigned'] = 0;
            return $currentCreditLimit;
        }
    }
}
if (!function_exists('all_po_drop'))
{
    function all_po_drop()
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select("srp_erp_purchaseordermaster.purchaseOrderID,purchaseOrderCode");
        $CI->db->from('srp_erp_purchaseordermaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('approvedYN', 1);
        $data = $CI->db->get()->result_array();

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['purchaseOrderID'] ?? '')] = (trim($row['purchaseOrderCode'] ?? ''));
            }
        }

        return $data_arr;
    }
}
if (!function_exists('all_main_category_report_drop_pos'))
{
    function all_main_category_report_drop_pos()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_itemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('categoryTypeID!=', 3);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_main_category_group_report_drop_pos'))
{
    function all_main_category_group_report_drop_pos()
    {
        $CI = &get_instance();
        $CI->db->SELECT("itemCategoryID,description,codePrefix");
        $CI->db->FROM('srp_erp_groupitemcategory');
        $CI->db->WHERE('masterID', NULL);
        $CI->db->where('groupID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('categoryTypeID!=', 3);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['itemCategoryID'] ?? '')] = trim($row['codePrefix'] ?? '') . ' | ' . trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('customer_PriceSetup_approval_edit'))
{
    function customer_PriceSetup_approval_edit($cpsAutoID, $approvalLevelID, $approvedYN, $documentApprovedID, $documentID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0)
        {
            $status .= '<a onclick=\'fetch_approval_cps("' . $cpsAutoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal_CPS(\'CPS\',\'' . $cpsAutoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('edit_customerPriceSetup_new'))
{
    function edit_customerPriceSetup_new($cpsAutoID, $confirmedYN, $approvedYN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $status .= '<a onclick="Customer_priceSetup_DocumentView_new(' . $cpsAutoID . ',\'CPS\');"><span title="" rel="tooltip" class="glyphicon glyphicon-eye-open" data-original-title="View"></span></a>';

        if ($approvedYN != 1 && $confirmedYN == 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="referback_Customer_priceSetup(' . $cpsAutoID . ',\'CPS\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($confirmedYN != 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'fetchPage("system/customer/erp_new_Customer_priceSetup_new",' . $cpsAutoID . ',"Edit Customer Price Setup","CPS"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>';

            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_Customer_priceSetup(' . $cpsAutoID . ',\'CPS\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';
        return $status;
    }
}


if (!function_exists('master_customer_drop'))
{
    function master_customer_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("customerAutoID,customerName");
        $CI->db->FROM('srp_erp_customermaster');
        $CI->db->WHERE('masterID', null);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Group To');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['customerAutoID'] ?? '')] = trim($row['customerName'] ?? '');
            }
        }

        return $data_arr;
    }
}
if (!function_exists('load_warehouses'))
{
    function load_warehouses($warehouseid)
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->SELECT("wareHouseCode,wareHouseDescription,companyCode,wareHouseAutoID,wareHouseLocation");
        $CI->db->FROM('srp_erp_warehousemaster');
        $CI->db->WHERE('companyID', $companyID);
        $CI->db->WHERE('wareHouseAutoID', $warehouseid);
        $location = $CI->db->get()->row_array();

        return $location;
    }
}
if (!function_exists('insert_warehouseitems'))
{
    function insert_warehouseitems($warehouseid)
    {

        $CI = &get_instance();
        $warehouselocation = load_warehouses($warehouseid);
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->query("INSERT INTO srp_erp_warehouseitems (wareHouseAutoID,wareHouseLocation,wareHouseDescription,itemAutoID,itemSystemCode,itemDescription,barCodeNo,ActiveYN,salesPrice,unitOfMeasureID,unitOfMeasure,currentStock,companyID,companyCode)
    SELECT
	 $warehouseid as wareHouseAutoID,
	'{$warehouselocation['wareHouseLocation']}' as wareHouseLocation,
	'{$warehouselocation['wareHouseDescription']}' as wareHouseDescription,
	item.itemAutoID,
	itemaster.itemSystemCode,
	itemaster.itemDescription,
	itemaster.barcode,
	itemaster.isActive,
	itemaster.companyLocalSellingPrice,
	itemaster.defaultUnitOfMeasureID AS unitOfMeasureID,
	itemaster.defaultUnitOfMeasure AS unitOfMeasure,
	0 AS currentStock,
	itemaster.CompanyID AS CompanyID,
	itemaster.companyCode AS companyCode 
FROM
	srp_erp_itemledger item
	LEFT JOIN srp_erp_itemmaster itemaster ON itemaster.itemAutoID = item.itemAutoID 
WHERE
    wareHouseAutoID = $warehouseid
    AND	item.itemAutoID NOT IN ( SELECT srp_erp_warehouseitems.itemAutoID FROM srp_erp_warehouseitems WHERE srp_erp_warehouseitems.wareHouseAutoID = $warehouseid ) 
GROUP BY
	item.itemAutoID,
	item.wareHouseAutoID");
    }
}


if (!function_exists('load_rv_action_buyback'))
{
    function load_rv_action_buyback($poID, $POConfirmedYN, $approved, $createdUserID, $isDeleted, $confirmedByEmp, $isSystemGenerated)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Receipt Voucher","RV",' . $POConfirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            if ($isSystemGenerated != 1)
            {
                $status .= '<a onclick=\'fetchPage("system/receipt_voucher/erp_receipt_voucher_buyback",' . $poID . ',"Edit Receipt Voucher","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }
            else
            {
                $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmp == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_receipt_voucher(' . $poID . ',' . $isSystemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'RV\',\'' . $poID . '\', \'buy\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';

        //$status .= '<a target="_blank" href="' . site_url('Receipt_voucher/load_rv_conformation_buyback/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
        $status .= '<a target="_blank" onclick="load_printtemp(' . $poID . ')"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Receipt Voucher\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if ($approved == 1)
        {
            $status .= '&nbsp;&nbsp;<a onclick="traceDocument(' . $poID . ',\'RV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('company_finance_year'))
{
    function company_finance_year($financeyearid)
    {

        $CI = &get_instance();
        $CI->db->SELECT("companyFinanceYearID, DATE(beginingDate) as startdate, DATE(endingDate) as endingdate");
        $CI->db->FROM('srp_erp_companyfinanceyear');
        $CI->db->WHERE('companyFinanceYearID', $financeyearid);
        $financeyear = $CI->db->get()->row_array();

        return $financeyear;
    }
}

if (!function_exists('confirm_user_deliveredstatus'))
{
    function confirm_user_deliveredstatus($code, $autoID, $deliveredstatus, $approvedYN, $confirmedYN)
    {
        $status = '<center>';

        //if ($confirmed_status == 1) {
        // $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success">';
        //$status .= '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a><br>';
        if ($code == 'DO')
        {
            if ($deliveredstatus == 0)
            {
                $status .= '<a onclick="generatedeliverystatus_drilldown(\'' . $code . '\', ' . $autoID . ', ' . $deliveredstatus . ', ' . $approvedYN . ', ' . $confirmedYN . ')" <span class="label label-danger"  style="font-size: 9px; width: 10%; "  title="Not Collected" rel="tooltip">Not Delivered</span>';
            }
            else if ($deliveredstatus == 1)
            {
                $status .= '<a onclick="generatedeliverystatus_drilldown(\'' . $code . '\', ' . $autoID . ', ' . $deliveredstatus . ', ' . $approvedYN . ', ' . $confirmedYN . ')" <span class="label label-info" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Collected" rel="tooltip">sent to Delivery </span>';
            }
            else if ($deliveredstatus == 2)
            {
                $status .= '<a onclick="generatedeliverystatus_drilldown(\'' . $code . '\', ' . $autoID . ', ' . $deliveredstatus . ', ' . $approvedYN . ', ' . $confirmedYN . ')" <span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Collected" rel="tooltip">Delivered </span>';
            }
            else
            {
                $status .= '-';
            }
        }
        //} else {
        //  $status .= '<span class="label label-success">&nbsp;</span>';
        //}
        // else {
        //$status .= '-';
        //}
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('payroll_group_drop'))
{
    function payroll_group_drop()
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $data = $CI->db->select("hrGroupID,hrMas.description AS description, sysType.description AS periodSysType")
            ->from('srp_erp_hrperiodgroup AS hrMas')
            ->join('srp_erp_systemhrperiodtypes AS sysType', 'sysType.id=hrMas.periodTypeID')
            ->where('companyID', $companyID)->get()->result_array();

        $data_arr = [];
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['hrGroupID'] ?? '')] = trim($row['description'] ?? '') . ' | ' . trim($row['periodSysType'] ?? '');
            }
        }
        return $data_arr;
    }
}

//operations Contract types
if (!function_exists('all_contarct_types'))
{
    function all_contarct_types($status = TRUE)/*Load all currency*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("*");
        $CI->db->from('contracttype');
        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
        $contype = $CI->db->get()->result_array();
        if ($status)
        {
            $contype_arr = array('' => 'Select Contract Type');
        }
        else
        {
            $contype_arr = [];
        }
        if (isset($contype))
        {
            foreach ($contype as $row)
            {
                $contype_arr[trim($row['contractTypeId'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $contype_arr;
    }
}
if (!function_exists('all_customer_category_report_drop'))
{
    function all_customer_category_report_drop()
    {
        $CI = &get_instance();
        $CI->db->SELECT("partyCategoryID,categoryDescription");
        $CI->db->FROM('srp_erp_partycategories');
        //$CI->db->WHERE('masterID', NULL);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('partyType', 1);
        $data = $CI->db->get()->result_array();
        $data_arr = array();
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['partyCategoryID'] ?? '')] = trim($row['categoryDescription'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('buyback_deliveredstatus'))
{
    function buyback_deliveredstatus($invoiceAutoID, $deliveredstatus, $approvedYN, $confirmedYN)
    {
        $status = '<center>';
        if ($deliveredstatus == 0)
        {
            $status .= '<a onclick="generate_buybackdeliverystatus_drilldown_buyback(' . $invoiceAutoID . ', ' . $deliveredstatus . ', ' . $approvedYN . ', ' . $confirmedYN . ')" <span class="label label-danger"  style="font-size: 9px; width: 10%; "  title="Not Delivered" rel="tooltip">Not Delivered</span>';
        }
        else
        {
            $status .= '<a onclick="generate_buybackdeliverystatus_drilldown_buyback(' . $invoiceAutoID . ', ' . $deliveredstatus . ', ' . $approvedYN . ', ' . $confirmedYN . ')" <span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.6em 0.3em;" title="Delivered" rel="tooltip">Delivered </span>';
        }
        $status .= '</center>';
        return $status;
    }
}
//op field master drop down
if (!function_exists('op_location_drop'))
{
    function op_location_drop($status = TRUE)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $fields = $CI->db->query("SELECT * FROM `fieldmaster` WHERE  companyID='$companyID' ORDER BY trim(fieldmaster.fieldName) REGEXP '^[a-z]' DESC, trim(fieldmaster.fieldName)")->result_array();
        if ($status)
        {
            $fields_arr = array('' => 'Select Location');
        }
        else
        {
            $fields_arr = [];
        }
        if (isset($fields))
        {
            foreach ($fields as $row)
            {
                $fields_arr[trim($row['FieldID'] ?? '')] = trim($row['fieldName'] ?? '');
            }
        }

        return $fields_arr;
    }
}

if (!function_exists('op_serviceline_drop'))
{
    function op_serviceline_drop($status = TRUE)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $fields = $CI->db->query("SELECT serviceLineSystemID,ServiceLineCode,ServiceLineDes FROM `serviceline` WHERE  companyID='$companyID'")->result_array();
        if ($status)
        {
            $fields_arr = array('' => 'Select Service Line');
        }
        else
        {
            $fields_arr = [];
        }
        if (isset($fields))
        {
            foreach ($fields as $row)
            {
                $fields_arr[trim($row['serviceLineSystemID'] ?? '')] = trim($row['ServiceLineCode'] ?? '') . ' | ' . trim($row['ServiceLineDes'] ?? '');
            }
        }

        return $fields_arr;
    }
}
if (!function_exists('load_contract_action_nh'))
{
    function load_contract_action_nh($poID, $POConfirmedYN, $approved, $createdUserID, $documentID, $confirmedYN, $isDeleted, $confirmedbyempid, $isSystemGenerated, $closedYN)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        else if ($documentID == "QUT")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Quotation","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else if ($documentID == "CNT")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Contract","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else if ($documentID == "SO")
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Sales Order","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $documentID . '","' . $documentID . '","' . $confirmedYN . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            /*$status .= '<a onclick=\'fetchPage("system/quotation_contract/erp_quotation_contract",' . $poID . ',"Edit Quotation or Contract","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';*/
            if ($isSystemGenerated != 1)
            {
                $status .= '<a onclick=\'fetchPage("system/quotation_contract/erp_quotation_contract_Nh",' . $poID . ',"Edit Quotation or Contract","' . $documentID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            }
            else
            {
                $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
            }
        }

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedbyempid == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_customer_contract(' . $poID . ',\'' . $documentID . '\',' . $isSystemGenerated . ',);"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }

        $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\',\'NH\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a onclick="document_drill_down_View_modal(\'' . $poID . '\',\'' . $documentID . '\')"><i title="Drill Down" rel="tooltip" class="fa fa-bars" aria-hidden="true"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

        $status .= '<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation_nh/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        if ($documentID == 'QUT' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="document_version_View_modal(\'' . $documentID . '\',\'' . $poID . '\')"><i title="Documents" rel="tooltip" class="fa fa-files-o" aria-hidden="true"></i></a>&nbsp;&nbsp;|';
                $status .= ' <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;&nbsp;';
            }
        }
        if ($documentID == 'CNT' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }
        if ($documentID == 'SO' && $isDeleted == 0)
        {
            if ($POConfirmedYN == 1 and $approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
        }

        if ($POConfirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'' . $documentID . '\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'' . $documentID . '\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a>
            &nbsp;&nbsp;|&nbsp;&nbsp; <a onclick="check_item_balance_from_quotation_contract(' . $poID . ')" title="Generate Delivery Order/ Invoice" rel="tooltip"><i class="fa fa-file" aria-hidden="true"></i></a>';
        }
        if ($approved == 1 && $isDeleted == 0 && $closedYN == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick=\'contract_close("' . $poID . '"); \'><i title="Close" rel="tooltip" class="fa fa-times" aria-hidden="true"></i></a>';
        }
        /*        if ($POConfirmedYN != 0 && $POConfirmedYN != 2) {
                    $status .= '<a target="_blank" onclick="documentPageView_modal(\'' . $documentID . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
                    $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Quotation_contract/load_contract_conformation/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
                }*/


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('con_action_approval_nh'))
{
    function con_action_approval_nh($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '","NH"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('load_invoice_action_DS'))
{
    function load_invoice_action_DS($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isSytemGenerated, $isPreliminaryPrinted)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $policyPIE = getPolicyValues('PIE', 'All');
        $status = '<span class="pull-right">';
        $checked = '';
        $title = 'Preliminary Not Submitted';
        if ($policyPIE && $policyPIE == 1 && $approved != 1)
        {
            if ($isPreliminaryPrinted == 1)
            {
                $checked = 'checked';
                $title = 'Preliminary Submitted';
            }
            $status .= '<span title="' . $title . '" rel="tooltip"><input type="checkbox" id="isprimilinaryPrinted_' . $poID . '" name="isprimilinaryPrinted"  data-size="mini" data-on-text="Preliminary Printed" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Preliminary Not Printed" data-label-width="0" onclick="update_preliminary_print_status(' . $poID . ')" ' . $checked . '></span>&nbsp;&nbsp;|&nbsp;&nbsp;';
            // $status .= '<a><span title="Preliminary Printed" rel="tooltip" class="fa fa-flag"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';         
        }
        if (empty($tempInvoiceID))
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

            if ($isDeleted == 1)
            {
                $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                if ($isSytemGenerated != 1)
                {
                    $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_DS",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                else
                {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                }
            }

            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
            {
                $status .= '<a onclick="referback_customer_invoice(' . $poID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'DS\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_ds/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            if ($approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'DS\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_ds/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }

        $outputs = get_pv_rv_based_on_policy('RV');
        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> ';
        }

        if ($approved == 1 && $outputs)
        {
            $status .= ' &nbsp; | &nbsp; <a onclick="open_receipt_voucher_modal(' . $poID . ')" title="Create Receipt Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
        }


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('inv_action_approval_ds'))
{
    function inv_action_approval_ds($poID, $Level, $approved, $ApprovedID, $documentID, $approval = 1)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice", "' . $documentID . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $documentID . '","' . $poID . '","DS","' . $approval . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('firebaseToken'))
{
    function firebaseToken($emp_id, $device_type, $company_id = null)
    {
        //For web call company id will assign inside the function and for the API call we have to pass a parameter
        $company_id = ($company_id == null) ? current_companyID() : $company_id;

        $CI = &get_instance();
        $token = array();
        $db2 = $CI->load->database('db2', TRUE);
        if (!empty($emp_id))
        {
            /************************************************
             * $device_type => android | apple
             ************************************************/
            $firebaseToken_android = $db2->query("SELECT player_id FROM srp_erp_devices WHERE emp_id IN ({$emp_id}) AND isLogged = 1 
                                              AND company_id = {$company_id} AND device = '{$device_type}'")->result_array();

            $token = array_column($firebaseToken_android, 'player_id');
        }
        return $token;
    }
}
if (!function_exists('load_all_projects'))
{
    function load_all_projects($status = true)
    {
        $CI = &get_instance();
        $CI->db->SELECT("projectID,projectName");
        $CI->db->FROM('srp_erp_projects');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $project = $CI->db->get()->result_array();
        if ($status)
        {
            $project_arr = array('' => 'Select Project');
        }
        else
        {
            $project_arr = array();
        }
        if (isset($project))
        {
            foreach ($project as $row)
            {
                $project_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        return $project_arr;
    }
}
if (!function_exists('load_all_project_pm'))
{
    function load_all_project_pm()
    {
        $project_arr = array();

        $CI = &get_instance();
        $CI->db->SELECT("projectPlannningID,description");
        $CI->db->FROM('srp_erp_projectplanning');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $project = $CI->db->get()->result_array();
        if (isset($project))
        {
            foreach ($project as $row)
            {
                $project_arr[trim($row['projectPlannningID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $project_arr;
    }
}
if (!function_exists('project_relationship'))
{
    function project_relationship()
    {
        $CI = &get_instance();
        $CI->db->SELECT("relationshipID,relationshipName");
        $CI->db->FROM('srp_erp_pmrelationship');
        $project_relationship = $CI->db->get()->result_array();
        $project_arr_relationship = array('' => 'Select Project Relationship');
        if (isset($project_relationship))
        {
            foreach ($project_relationship as $row)
            {
                $project_arr_relationship[trim($row['relationshipID'] ?? '')] = trim($row['relationshipName'] ?? '');
            }
        }
        return $project_arr_relationship;
    }
}

if (!function_exists('getprojectmanagementApprovalSetup'))
{
    function getprojectmanagementApprovalSetup($isSetting = 'N', $input_companyId = null)
    {

        $CI = &get_instance();

        if ($input_companyId == null)
        {
            $companyID = current_companyID();
        }
        else
        {
            $companyID = $input_companyId;
        }
        $appSystemValues = $CI->db->query("SELECT * FROM srp_erp_leavesetupsystemapprovaltypes")->result_array();
        if ($isSetting == 'Y')
        {
            $arr = [0 => ''];
            foreach ($appSystemValues as $key => $val)
            {
                $arr[$val['id']] = $val['description'];
            }
            $appSystemValues = $arr;
        }

        $approvalLevel = $CI->db->query("SELECT approvalLevel FROM srp_erp_documentcodemaster WHERE documentID = 'PM-T' AND
                                         companyID={$companyID} ")->row('approvalLevel');

        $approvalSetup = $CI->db->query("SELECT approvalLevel, approvalType, empID, systemTB.*
                                         FROM srp_erp_pm_timesheetapprovalsetup AS setupTB
                                         JOIN srp_erp_leavesetupsystemapprovaltypes AS systemTB ON systemTB.id = setupTB.approvalType
                                         WHERE companyID={$companyID} ORDER BY approvalLevel")->result_array();

        $approvalEmp = $CI->db->query("SELECT approvalLevel, empTB.empID
                                       FROM srp_erp_pm_timesheetapprovalsetup AS setupTB
                                       JOIN srp_erp_pm_timesheetapprovalsetuphremployees AS empTB ON empTB.approvalSetupID = setupTB.approvalSetupID
                                       WHERE setupTB.companyID={$companyID} AND empTB.companyID={$companyID}")->result_array();
        if (!empty($approvalEmp))
        {
            $approvalEmp = array_group_by($approvalEmp, 'approvalLevel');
        }


        return [
            'approvalLevel' => $approvalLevel,
            'approvalSetup' => $approvalSetup,
            'appSystemValues' => $appSystemValues,
            'approvalEmp' => $approvalEmp
        ];
    }
}
if (!function_exists('Project_Subcategory_is_exist'))
{
    function Project_Subcategory_is_exist()
    {
        $CI = &get_instance();
        $CI->db->SELECT("value");
        $CI->db->FROM('srp_erp_companypolicy');
        $CI->db->WHERE('companypolicymasterID', 46);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $row = $CI->db->get()->row_array();
        $data = 0;
        if (!empty($row))
        {
            if ($row['value'] == 1)
            {
                $data = 1;
            }
            else
            {
                $data = 0;
            }
        }
        return $data;
    }
}

if (!function_exists('load_all_project'))
{
    function load_all_project()
    {
        $CI = &get_instance();
        $CI->db->SELECT("projectID,projectName");
        $CI->db->FROM('srp_erp_projects');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $project = $CI->db->get()->result_array();
        if (isset($project))
        {
            foreach ($project as $row)
            {
                $project_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        return $project_arr;
    }
}
if (!function_exists('clearance_form_approval'))
{
    function clearance_form_approval($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $poID . ',"' . $document . '","' . $document . '");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'documentPageView_modal("' . $document . '","' . $poID . '"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp';
        }
        //$status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'' . $document . '\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('doc_request_passportdetail'))
{
    function doc_request_passportdetail()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $data_arr = $CI->db->query("SELECT autoID,Description,IFNULL(descriptionenable,0) as descriptionenable  FROM `srp_erp_clearanceformitems` WHERE companyID = $companyID AND fieldType = 3 ORDER BY sortorder ASC")->result_array();
        return $data_arr;
    }
}
if (!function_exists('fetch_company'))
{
    function fetch_company()
    {
        $CI = &get_instance();
        $company_id = current_companyID();
        $company = $CI->db->query("SELECT company_id, concat(company_code,' | ',company_name) as company FROM `srp_erp_company` where company_id = {$company_id} ")->row('company');
        return $company;
    }
}
if (!function_exists('audit_log_colname'))
{
    function audit_log_colname($colname, $tblname)
    {
        $CI = &get_instance();
        $company_id = current_companyID();
        $auditlog = $CI->db->query("SELECT * FROM `srp_erp_audit_display_columns` where col_name = '{$colname}' AND tbl_name = '{$tblname}'")->row_array();
        return $auditlog;
    }
}

if (!function_exists('load_pos_location_drop_multi'))
{
    function load_pos_location_drop_multi()
    {
        $CI = &get_instance();
        $company = $CI->common_data['company_data']['company_id'];
        $CI->db->select("wareHouseAutoID,wareHouseCode,wareHouseLocation,wareHouseDescription");
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('isPosLocation', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $company);
        $data = $CI->db->get()->result_array();

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $emp_arr[trim($row['wareHouseAutoID'] ?? '')] = str_replace("'", "&apos;", trim($row['wareHouseDescription'] ?? ''));
            }
        }

        return $emp_arr;
    }
}

if (!function_exists('load_fm_balance_sheet_template'))
{
    function load_fm_balance_sheet_template($masterID, $masterData, $print = 0)
    {
        $CI = &get_instance();

        $temMasterID = $masterData['templateID'];
        $dPlace = $masterData['trCurrencyDPlace'];
        $docType = $masterData['docType'];
        $fn_period = $masterData['fn_period_org'];
        $fm_companyID = $masterData['fm_companyID'];
        $companyID = current_companyID();

        $CI->db->select('detID, description, itemType, sortOrder');
        $CI->db->from('srp_erp_companyreporttemplatedetails');
        $CI->db->where('companyReportTemplateID', $temMasterID);
        $CI->db->where('masterID IS NULL');
        $CI->db->where('companyID', $companyID);
        $CI->db->order_by('sortOrder');
        $data = $CI->db->get()->result_array();


        $returnData = '';
        foreach ($data as $row)
        {
            $order1 = $row['sortOrder'];
            $templateID = $row['detID'];


            if ($row['itemType'] == 2)
            {
                $returnData .= '<tr>';
                if ($print == 0)
                {
                    $returnData .= '<td class="mini-header index-td">' . $order1 . '</td>';
                }
                $returnData .= '<td class="mini-header description_td_rpt"><span class="td-main-header"><i class="fa fa-minus-square"></i>  ' . $row['description'] . '</span></td>';
                $returnData .= '<td class="mini-header amount_td_rpt" style="text-align: center; width: 100px"></td></tr>';


                $subData = $CI->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$templateID} ORDER BY sortOrder")->result_array();

                foreach ($subData as $sub_row)
                {
                    $detID = $sub_row['detID'];
                    $order2 = $sub_row['sortOrder'];

                    if ($sub_row['itemType'] == 1)
                    { /*Sub category*/
                        $returnData .= '<tr class="hoverTr">';
                        if ($print == 0)
                        {
                            $returnData .= '<td class="sub1 index-td">' . $order1 . '.' . $order2 . '</td>';
                        }
                        $returnData .= '<td class="sub1 description_td_rpt"> ' . $sub_row['description'] . ' </td>';
                        $returnData .= '<td class="sub1 amount_td_rpt" style="text-align: center"> </td></tr>';


                        $glData = $CI->db->query("SELECT linkID, det.glAutoID, sortOrder, templateDetailID, SUM(trAmount) trAmount,
                                    CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                    FROM srp_erp_companyreporttemplatelinks det
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                    LEFT JOIN (
                                        SELECT GLAutoID, IFNULL(transactionAmount ,0) trAmount 
                                        FROM srp_erp_fm_financialdetails WHERE documentMasterAutoID IN (
                                            SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                            JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                            WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                            AND fn_period <= '{$fn_period}'  
                                        )
                                    ) fnData ON fnData.GLAutoID = det.glAutoID
                                    WHERE templateDetailID = {$detID} GROUP BY det.glAutoID ORDER BY sortOrder")->result_array();

                        foreach ($glData as $gl_row)
                        {
                            $order3 = $gl_row['sortOrder'];
                            $glAutoID = $gl_row['glAutoID'];
                            $trAmount = $gl_row['trAmount'];
                            $returnData .= '<tr class="hoverTr">';
                            if ($print == 0)
                            {
                                $returnData .= '<td class="sub2 index-td">' . $order1 . '.' . $order2 . '.' . $order3 . '</td>';
                            }
                            $returnData .= '<td class="sub2 description_td_rpt glDescription">' . $gl_row['glData'] . '</td>';
                            $returnData .= '<td class="sub2 amount_td_rpt" style="text-align: right">';
                            if ($print == 1)
                            {
                                $returnData .= number_format($trAmount, $dPlace);
                            }
                            else
                            {
                                $trAmount = (!empty($trAmount)) ? round($trAmount, $dPlace) : $trAmount;
                                $returnData .= '<input type="text" class="numeric" value="' . $trAmount . '" onchange="update_amount(this, ' . $glAutoID . ')" 
                                                  onkeyup="calculate_new_amount(this, ' . $glAutoID . ')"/>';
                            }
                            $returnData .= '</td></tr>';
                        }
                    }

                    if ($sub_row['itemType'] == 3)
                    { /*Group*/


                        $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$detID}
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();
                        $amount = 0;
                        if (!empty($group_glData))
                        {
                            $where_in_array = array_column($group_glData, 'glAutoID');
                            $where_in = implode(',', $where_in_array);
                            $amount = $CI->db->query("SELECT ROUND( SUM(IFNULL(transactionAmount,0)), transactionCurrencyDecimalPlaces) trAmount                                               
                                                      FROM srp_erp_fm_financialdetails 
                                                      WHERE documentMasterAutoID IN (                                                                                        
                                                            SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                                            JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                                            WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                                            AND fn_period <= '{$fn_period}'                                                        
                                                      ) AND GLAutoID IN ({$where_in})")->row('trAmount');

                            $amount = (empty($amount)) ? 0 : $amount;
                        }

                        //$returnData .= '<tr class="hoverTr"><td colspan="3"><pre>'.$CI->db->last_query().'</pre></td></tr>';
                        $returnData .= '<tr class="hoverTr">';
                        if ($print == 0)
                        {
                            $returnData .= '<td class="sub1 index-td">' . $order1 . '.' . $order2 . '</td>';
                        }
                        $returnData .= '<td class="sub1 description_td_rpt"><span id="title_str_' . $detID . '">' . $sub_row['description'] . '</span></td>';
                        $returnData .= '<td class="sub1 sub_total_rpt amount_td_rpt" style="text-align: right;" id="subTot_' . $detID . '" >' . number_format($amount, $dPlace) . '</td>';
                        $returnData .= '</tr>';
                    }
                }
            }
            else
            {
                /*Group*/

                $group_glData = $CI->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$temMasterID} AND itemType = 3 AND detID={$templateID}
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();
                $amount = 0;
                if (!empty($group_glData))
                {
                    $where_in_array = array_column($group_glData, 'glAutoID');
                    $where_in = implode(',', $where_in_array);
                    $amount = $CI->db->query("SELECT ROUND( SUM(IFNULL(transactionAmount,0)), transactionCurrencyDecimalPlaces) trAmount 
                                              FROM srp_erp_fm_financialdetails 
                                              WHERE documentMasterAutoID IN (                                                                                        
                                                    SELECT t1.id FROM srp_erp_fm_financialsmaster t1
                                                    JOIN srp_erp_reporttemplate t3 ON t1.reportID=t3.reportID        
                                                    WHERE t1.fm_companyID = {$fm_companyID} AND t3.documentCode='FIN_BS' 
                                                    AND fn_period <= '{$fn_period}'  
                                              )                                              
                                              AND GLAutoID IN ({$where_in})")->row('trAmount');

                    $amount = (empty($amount)) ? 0 : $amount;
                }

                //$returnData .= '<tr class="hoverTr"><td colspan="3"><pre>'.$CI->db->last_query().'</pre></td></tr>';
                $cols = ($print == 0) ? 3 : 2;
                $returnData .= '<tr><td colspan="' . $cols . '">&nbsp;</td></tr>';
                $returnData .= '<tr class="hoverTr">';
                if ($print == 0)
                {
                    $returnData .= '<td class="mini-header index-td">' . $order1 . '</td>';
                }
                $returnData .= '<td class="mini-header"><span class="td-main-header description_td_rpt"><i class="fa fa-minus-square"></i>  ' . $row['description'] . '</span></td>';
                $returnData .= '<td class="sub1 total_black_rpt amount_td_rpt" style="text-align: right;" id="subTot_' . $templateID . '" >' . number_format($amount, $dPlace) . '</td>';
                $returnData .= '</tr>';
            }
        }

        return $returnData;
    }
}

if (!function_exists('is_QHSE_integrated'))
{
    function is_QHSE_integrated()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $db2 = $CI->load->database('db2', TRUE);

        $count = $db2->query("SELECT COUNT(`key`) cn FROM `keys` WHERE company_id = {$companyID} AND key_type = 'QHSE'")->row('cn');

        return ($count > 0) ? 'Y' : 'N';
    }
}


if (!function_exists('edit_item_codification'))
{
    function edit_item_codification($itemAutoID, $isActive = 0, $isSubItemExist = NULL, $deletedYN = NULL)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $items = $CI->db->query("SELECT
	checkitemtransaction.*,
CASE
	
	WHEN checkitemtransaction.doctype = \"DO\" THEN
	srp_erp_deliveryorder.DOCode 
	WHEN checkitemtransaction.doctype = \"QUT\" THEN
	srp_erp_contractmaster.contractCode 
	WHEN checkitemtransaction.doctype = \"buybackdispatch\" THEN
	srp_erp_buyback_dispatchnote.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"buybackGRN\" THEN
	srp_erp_buyback_grn.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"CRMQUT\" THEN
	srp_erp_crm_quotation.quotationCode 
	WHEN checkitemtransaction.doctype = \"CINV\" THEN
	srp_erp_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"RV\" THEN
	srp_erp_customerreceiptmaster.RVcode 
	WHEN checkitemtransaction.doctype = \"GRV\" THEN
	srp_erp_grvmaster.grvPrimaryCode
    WHEN checkitemtransaction.doctype = \"MI\" THEN
	srp_erp_itemissuemaster.itemIssueCode 
    WHEN checkitemtransaction.doctype = \"MR\" THEN
	srp_erp_materialrequest.MRCode 
    WHEN checkitemtransaction.doctype = \"MRN\" THEN
	srp_erp_materialreceiptmaster.mrnCode 
    WHEN checkitemtransaction.doctype = \"MFQBOM\" THEN
	srp_erp_mfq_billofmaterial.documentCode 
    WHEN checkitemtransaction.doctype = \"MFQINV\" THEN
	srp_erp_mfq_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"PV\" THEN
	srp_erp_paymentvouchermaster.PVcode 
    WHEN checkitemtransaction.doctype = \"BSI\" THEN
	srp_erp_paysupplierinvoicemaster.bookingInvCode 
	WHEN checkitemtransaction.doctype = \"POSINV\" THEN
	srp_erp_pos_invoice.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"PO\" THEN
	srp_erp_purchaseordermaster.purchaseOrderCode 
	WHEN checkitemtransaction.doctype = \"PRD\" THEN
	srp_erp_purchaserequestmaster.purchaseRequestCode 
	WHEN checkitemtransaction.doctype = \"SR\" THEN
	srp_erp_salesreturnmaster.salesReturnCode 
    WHEN checkitemtransaction.doctype = \"SA\" THEN
    stockA.stockAdjustmentCode 
    WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.stockCountingCode
	   WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stockTransferCode	
	ELSE \"\" 
	END documentcode,
	CASE
	
	WHEN checkitemtransaction.doctype = \"SA\" THEN
	stockA.sacompanyID 
	WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.sccompanyID 
	WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stcompanyID
	
	
	ELSE
	checkitemtransaction.companyID
	END companyidNEw
	
FROM
	`checkitemtransaction`
	LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = checkitemtransaction.doccode 
	AND doctype = 'DO'
	LEFT JOIN srp_erp_contractmaster ON srp_erp_contractmaster.contractAutoID = checkitemtransaction.doccode 
	AND doctype = 'QUT' 
	LEFT JOIN srp_erp_buyback_dispatchnote On srp_erp_buyback_dispatchnote.dispatchAutoID = checkitemtransaction.doccode AND doctype = 'buybackdispatch'
	LEFT JOIN srp_erp_buyback_grn On srp_erp_buyback_grn.grnAutoID = checkitemtransaction.doccode AND doctype = 'buybackGRN' 
	LEFT JOIN srp_erp_crm_quotation on srp_erp_crm_quotation.quotationAutoID = checkitemtransaction.doccode AND doctype = 'CRMQUT'  
	LEFT JOIN srp_erp_customerinvoicemaster on srp_erp_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'CINV' 
	LEFT JOIN srp_erp_customerreceiptmaster on srp_erp_customerreceiptmaster.receiptVoucherAutoId = checkitemtransaction.doccode AND doctype = 'RV' 
	LEFT JOIN srp_erp_grvmaster on srp_erp_grvmaster.grvAutoID = checkitemtransaction.doccode AND doctype = 'GRV' 
	LEFT JOIN srp_erp_itemissuemaster on srp_erp_itemissuemaster.itemIssueAutoID = checkitemtransaction.doccode AND doctype = 'MI' 
	LEFT JOIN srp_erp_materialrequest on srp_erp_materialrequest.mrAutoID = checkitemtransaction.doccode AND doctype = 'MR' 
	LEFT JOIN srp_erp_materialreceiptmaster on srp_erp_materialreceiptmaster.mrnAutoID = checkitemtransaction.doccode AND doctype = 'MRN'  
	LEFT JOIN srp_erp_mfq_billofmaterial on srp_erp_mfq_billofmaterial.bomMasterID = checkitemtransaction.doccode AND doctype = 'MFQBOM'  
	LEFT JOIN srp_erp_mfq_customerinvoicemaster on srp_erp_mfq_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'MFQINV'  
	LEFT JOIN srp_erp_paymentvouchermaster on srp_erp_paymentvouchermaster.payVoucherAutoId = checkitemtransaction.doccode AND doctype = 'PV' 
    LEFT JOIN srp_erp_paysupplierinvoicemaster on srp_erp_paysupplierinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'BSI' 
    LEFT JOIN srp_erp_pos_invoice on srp_erp_pos_invoice.invoiceID = checkitemtransaction.doccode AND doctype = 'POSINV' 
    LEFT JOIN srp_erp_purchaseordermaster on srp_erp_purchaseordermaster.purchaseOrderID = checkitemtransaction.doccode AND doctype = 'PO'  
    LEFT JOIN srp_erp_purchaserequestmaster on srp_erp_purchaserequestmaster.purchaseRequestID = checkitemtransaction.doccode AND doctype = 'PRD'  
    LEFT JOIN srp_erp_salesreturnmaster on srp_erp_salesreturnmaster.salesReturnAutoID = checkitemtransaction.doccode AND doctype = 'SR'  
   	LEFT JOIN (SELECT srp_erp_stockadjustmentmaster.companyID as sacompanyID ,stockAdjustmentAutoID,stockAdjustmentCode from srp_erp_stockadjustmentmaster LEFT JOIN checkitemtransaction on srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = checkitemtransaction.doccode where doctype = 'SA'  
	GROUP BY
		stockAdjustmentAutoID
	 ) stockA on stockA.stockAdjustmentAutoID = checkitemtransaction.doccode    
	 
	  	LEFT JOIN (SELECT srp_erp_stockcountingmaster.companyID as sccompanyID ,stockCountingAutoID,stockCountingCode from srp_erp_stockcountingmaster LEFT JOIN checkitemtransaction on srp_erp_stockcountingmaster.stockCountingAutoID = checkitemtransaction.doccode where doctype = 'SC'  
	GROUP BY
    stockCountingAutoID
	 ) stockcounting on stockcounting.stockCountingAutoID = checkitemtransaction.doccode  
	 
	 LEFT JOIN (
	SELECT
		srp_erp_stocktransfermaster.companyID AS stcompanyID,
		stockTransferAutoID,
		stockTransferCode 
	FROM
		srp_erp_stocktransfermaster
		LEFT JOIN checkitemtransaction ON srp_erp_stocktransfermaster.stockTransferAutoID = checkitemtransaction.doccode 
	WHERE
		doctype = 'ST' 
        group by 
		stockTransferAutoID
	) stocktransfer ON stocktransfer.stockTransferAutoID = checkitemtransaction.doccode 
	
WHERE
 checkitemtransaction.itemAutoID = '{$itemAutoID}'
GROUP BY 
doctype,doccode
HAVING 
companyidNEw = '{$companyID}'  ")->result_array();

        $status = '<span class="pull-right">';
        if ($deletedYN != 1)
        {
            $status .= '<a onclick="item_pricing_report(' . $itemAutoID . ');"><span title="Price Inquiry" rel="tooltip" class="glyphicon glyphicon-tag"></span></a>&nbsp;&nbsp;';
            if (isset($isSubItemExist) && $isSubItemExist == 1)
            {
                $status .= '<a class="text-purple" onclick="subItemConfigList_modal(' . $itemAutoID . ');"><span title="Sub Items" rel="tooltip" class="fa fa-list"></span></a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
            }


            $status .= '<a class="text-yellow" onclick="attachment_modal(' . $itemAutoID . ',\'Item\',\'ITM\');"><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;';

            if ($isActive)
            {
                /*            <input type="checkbox" id="itemchkbox_' . $itemAutoID . '" name="itemchkbox" onchange="changeitemactive(' . $itemAutoID . ')" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" checked><br><br>*/
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new_codification\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';

                /*| &nbsp;&nbsp;<a onclick="delete_item_master(' . $itemAutoID . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>*/
            }
            else
            {
                $status .= '<a onclick="fetchPage(\'system/item/erp_item_new_codification\',' . $itemAutoID . ',\'Edit Item\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
            }

            if (empty($items))
            {
                $status .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a class="text-yellow" onclick="delete_item_master(' . $itemAutoID . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }
        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('receivedhistory_taxcalculation'))
{
    function receivedhistory_taxcalculation($documentID, $gross, $DocID, $discount, $taxmasterID, $exchangerate)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        switch ($documentID)
        {
            case "BSI":
                $invoiceTotal = $CI->db->query("SELECT SUM(supplierAmount) as totalinvoice FROM `srp_erp_paysupplierinvoicedetail` where companyID = $companyID 
	                                                AND InvoiceAutoID = $DocID")->row('totalinvoice');
                $taxamountitemwise = $CI->db->query("SELECT 
                  (($gross -(($gross/$invoiceTotal)*$discount))*taxPercentage)/100 as totaitemwiseamount
        
FROM
	`srp_erp_paysupplierinvoicetaxdetails`
WHERE
	srp_erp_paysupplierinvoicetaxdetails.invoiceAutoID = $DocID AND taxMasterAutoID = $taxmasterID")->row('totaitemwiseamount');
                if (!empty($taxamountitemwise))
                {
                    $totaltaxamount = $taxamountitemwise;
                }
                else
                {
                    $totaltaxamount = 0;
                }

                return $totaltaxamount;

                break;
            case "PV":
                $invoiceTotal = $CI->db->query("SELECT SUM(transactionAmount) as totalinvoice FROM `srp_erp_paymentvoucherdetail` where companyID = $companyID 
	                                                AND payVoucherAutoId = $DocID")->row('totalinvoice');
                $taxamountitemwise = $CI->db->query("SELECT 
                  (($gross -(($gross/$invoiceTotal)*$discount))*taxPercentage)/100 as totaitemwiseamount
        
FROM
	`srp_erp_paymentvouchertaxdetails`
WHERE
	srp_erp_paymentvouchertaxdetails.payVoucherAutoId = $DocID AND taxMasterAutoID = $taxmasterID")->row('totaitemwiseamount');
                if (!empty($taxamountitemwise))
                {
                    $totaltaxamount = $taxamountitemwise;
                }
                else
                {
                    $totaltaxamount = 0;
                }

                return $totaltaxamount;

                break;


            default:
                return 0;
        }
    }
}
if (!function_exists('send_customerinvoice_emailCc'))
{
    function send_customerinvoice_emailCc($mailData, $attachment, $path, $documentid, $attachmentID_join)
    {
        $CI = &get_instance();
        /*if(ENVIRONMENT == 'development'){ // to avoid mail
            return true;
        }*/
        $CI->load->library('email_manual');
        $CI->load->library('s3');
        $approvalEmpID = $mailData['approvalEmpID'];
        $documentCode = $mailData['documentCode'];
        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];
        $ccemail = $mailData['ccEmail'];
        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = $CI->config->item('email_smtp_host');
        $config['smtp_user'] = $CI->config->item('email_smtp_username');
        $config['smtp_pass'] = $CI->config->item('email_smtp_password');
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);
        if (array_key_exists("from", $mailData))
        {
            if (hstGeras == 1)
            {
                $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
            }
            else
            {
                $CI->email->from($CI->config->item('email_smtp_from'), $mailData['from']);
            }
        }
        else
        {
            if (hstGeras == 1)
            {
                $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            }
            else
            {
                $CI->email->from($CI->config->item('email_smtp_from'), EMAIL_SYS_NAME);
            }
        }
        if (!empty($param))
        {
            $CI->email->to($toEmail);
            if (!empty($ccemail))
            {
                $CI->email->cc($ccemail);
            }
            $CI->email->subject($subject);
            $CI->email->message($CI->load->view('system/email_template/email_approval_template_log', $param, TRUE));
            if ($attachment == 1)
            {
                // $path .= $link;
                $CI->email->attach($path);
                if (!empty($attachmentID_join))
                {
                    $fileattachmet = $CI->db->query("SELECT attachmentID,myFileName as filename,CONCAT(attachmentDescription,'.',fileType) as description FROM `srp_erp_documentattachments` where 
	                                                 documentID = '$documentid' AND attachmentID IN ($attachmentID_join)")->result_array();
                    if (!empty($fileattachmet))
                    {
                        foreach ($fileattachmet as $attachmentval)
                        {
                            $link = $CI->s3->createPresignedRequest($attachmentval['filename'], '+1 hour');
                            $CI->email->attach($link, 'attachment', $attachmentval['description']);
                        }
                    }
                }
            }
        }

        $result = $CI->email->send();
        $CI->email->clear(TRUE);
        send_push_notification($approvalEmpID, $subject, $documentCode, 1);
    }
}

if (!function_exists('fetch_invoice_dropdown'))
{
    function fetch_invoice_dropdown($state = TRUE, $seccode = false) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $companyID = $CI->common_data['company_data']['company_id'];
        $where_tmp[] = "  srp_erp_pos_invoice.isVoid = 0 AND srp_erp_pos_invoice.companyID = " . current_companyID();

        $where = join('AND', $where_tmp);

        $CI->db->select("srp_erp_pos_invoice.invoiceID,srp_erp_pos_invoice.documentSystemCode");
        $CI->db->from('srp_erp_pos_invoice');
        $CI->db->join("srp_erp_pos_invoicedetail", "srp_erp_pos_invoice.invoiceID = srp_erp_pos_invoicedetail.invoiceID", "LEFT");
        $CI->db->join("srp_erp_itemmaster", "srp_erp_pos_invoicedetail.itemAutoID = srp_erp_itemmaster.itemAutoID", "LEFT");
        $CI->db->join("srp_erp_itemcategory", "srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID", "LEFT");
        $CI->db->join("srp_erp_itemcategory subct", "srp_erp_itemmaster.subSubCategoryID = subct.itemCategoryID", "LEFT");

        $CI->db->join('(SELECT SUM( netTotal ) AS totalreturn, invoiceID FROM srp_erp_pos_salesreturn WHERE companyID = ' . $companyID . '  GROUP BY invoiceID) rtn', '(rtn.invoiceID = srp_erp_pos_invoice.invoiceID)', 'left');

        $CI->db->join("srp_erp_pos_salesreturndetails", "srp_erp_pos_invoicedetail.invoiceID = srp_erp_pos_salesreturndetails.invoiceID and srp_erp_pos_invoicedetail.itemAutoID = srp_erp_pos_salesreturndetails.itemAutoID", "LEFT");
        $CI->db->where($where);
        $CI->db->group_by("srp_erp_pos_invoicedetail.itemAutoID");
        $CI->db->group_by("srp_erp_pos_invoicedetail.invoiceID");
        $data = $CI->db->get()->result_array();

        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Invoice');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['invoiceID'] ?? '')] = trim($row['documentSystemCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('amount_based_approval'))
{
    function amount_based_approval($documentCode, $documentAmount, $level_id = null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('documentID', $documentCode);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }
        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        foreach ($data as $level)
        {
            $approverAvailable = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND (fromAmount <= {$documentAmount} AND toAmount >= {$documentAmount})")->row_array();

            if (empty($approverAvailable))
            {
                return array('type' => 'e', 'level' => $level['levels']);
            }
        }
        return array('type' => 's');
    }
}

if (!function_exists('segment_based_approval'))
{
    function segment_based_approval($documentCode, $segmentID, $level_id = null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('segmentID', $segmentID);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }
        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        foreach ($data as $level)
        {
            $approverAvailable = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND segmentID = $segmentID )")->row_array();

            if (empty($approverAvailable))
            {
                return array('type' => 'e', 'level' => $level['levels']);
            }
        }
        return array('type' => 's');
    }
}

if (!function_exists('amount_base_segment_based_approval'))
{
    function amount_base_segment_based_approval($documentCode, $documentAmount, $segmentID, $level_id = null)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('segmentID', $segmentID);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }
        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        foreach ($data as $level)
        {
            $approverAvailable = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND segmentID = $segmentID AND (fromAmount <= {$documentAmount} AND toAmount >= {$documentAmount})")->row_array();

            if (empty($approverAvailable))
            {
                return array('type' => 'e', 'level' => $level['levels']);
            }
        }
        return array('type' => 's');
    }
}

if (!function_exists('getApprovalTypesONDocumentCode'))
{
    function getApprovalTypesONDocumentCode($documentID, $companyID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $approvalTypes = $CI->db->query("SELECT approvalType FROM `srp_erp_documentcodemaster`	                                    
	                                    where  companyID = {$companyID} and documentID = '{$documentID}' ")->row_array();
        return $approvalTypes;
    }
}

if (!function_exists('get_all_project_invoice'))
{
    function get_all_project_invoice()
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $cateogry = $CI->db->query("select srp_erp_projects.projectID,srp_erp_projects.projectName
                                        from srp_erp_projects INNER JOIN (SELECT projectID from srp_erp_boq_header where 
                                        companyID = $companyID) boqproject on boqproject.ProjectID = srp_erp_projects.projectID where 
                                        companyID = $companyID ")->result_array();
        $cateogry_arr = array('' => 'Select a project');
        if (isset($cateogry))
        {
            foreach ($cateogry as $row)
            {
                $cateogry_arr[trim($row['projectID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }

        return $cateogry_arr;
    }
}
if (!function_exists('get_advance_amount'))
{
    function get_advance_amount($invoiceAutoID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $advanceamount = $CI->db->query("SELECT sum(srp_erp_rvadvancematchdetails.transactionAmount) as advanceamount FROM `srp_erp_rvadvancematchdetails`
	                                    LEFT JOIN srp_erp_rvadvancematch advancematch on advancematch.matchID = srp_erp_rvadvancematchdetails.matchID
	                                    where  advancematch.matchinvoiceAutoID = $invoiceAutoID GROUP BY advancematch.matchinvoiceAutoID  ")->row('advanceamount');


        return $advanceamount;
    }
}

if (!function_exists('getApprovalTypes'))
{
    function getApprovalTypes($documentID, $companyID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $approvalTypes = $CI->db->query("SELECT approvalType FROM `srp_erp_documentcodemaster`	                                    
	                                    where  companyID = $companyID and documentID = $documentID ")->row('advanceamount');
        return $approvalTypes;
    }
}

/* Function added */
if (!function_exists('default_segment_drop'))
{
    function default_segment_drop($status = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('segmentID,segmentCode');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('isDefault', 1);
        $data = $CI->db->get()->row_array();
        if ($status)
        {
            $defaultval = trim($data['segmentID'] ?? '');
        }
        else
        {
            $defaultval = trim($data['segmentID'] ?? '') . '|' . trim($data['segmentCode'] ?? '');
        }
        return $defaultval;
    }
}
/* End function */

if (!function_exists('activitity_code_dropdown'))
{
    function activitity_code_dropdown($status = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_activity_code_main');
        $CI->db->where('company_id', current_companyID());
        $CI->db->where('is_active', 1);
        $data = $CI->db->get()->result_array();
        //$defaultval = $data['segmentID'];
        if ($status == TRUE)
        {
            $data_arr = array('' => 'Select Activity Code');
        }
        else
        {
            $data_arr = [];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['activity_code'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_frequency_list_drop'))
{
    function all_frequency_list_drop($status = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_frequencymaster');
        $data = $CI->db->get()->result_array();
        //$defaultval = $data['segmentID'];

        if ($status == TRUE)
        {
            $data_arr = array('' => 'Select Frequency');
        }
        else
        {
            $data_arr = [];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['autoID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('all_incoterms_drop'))
{
    function all_incoterms_drop($status = FALSE)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_incotermsmaster');
        $data = $CI->db->get()->result_array();
        //$defaultval = $data['segmentID'];

        if ($status == TRUE)
        {
            $data_arr = array('' => 'Select Incoterms');
        }
        else
        {
            $data_arr = [];
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['autoID'] ?? '')] = trim($row['description'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_invoice_detail'))
{
    function load_invoice_detail($invoiceNarration, $customermastername, $invoiceDate, $invoiceDueDate, $invoiceType, $referenceNo, $acknowledgementDate, $invoiceAutoID)
    {
        if (!isset($invoiceNarration) || $invoiceNarration == '')
        {
            $invoiceNarrationModified = '';
        }
        else
        {
            $invoiceNarrationModified = ucfirst(mb_strimwidth($invoiceNarration, 0, 100, '...'));
        }
        $CI = &get_instance();
        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $CI->load->library('session');
        $status = '';
        $status = '<span class="text-left">';
        $status .= '<b>Customer Name : </b> ' . $customermastername . ' <br> <b>Document Date : </b> ' . $invoiceDate . ' <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> ' . $invoiceDueDate . ' <br>';

        if ($acknowledgementDateYN == 1)
        {
            $status .= '<b>Acknowledgement Date : </b>
                        <a href="#" data-type="combodate" data-placement="bottom"
                                    id="acknowledgementDate" 
                                    data-pk="' . $invoiceAutoID . '|' . $acknowledgementDate . '"
                                    data-name="acknowledgementDate" data-title="Acknowledgement Date"
                                    class="xEditableDate date_change_' . $invoiceAutoID . '"
                                    data-value="' . format_date($acknowledgementDate) . '"
                                    data-related="_acknowledgementDate">
                        </a> &nbsp | &nbsp';
        }

        $status .= '<b>Type : </b> ' . $invoiceType . ' <br><b>Ref No : </b> ' . $referenceNo . ' <br> <b>Comments : </b> ' . $invoiceNarrationModified . '';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('trim_value_invoice'))
{
    function trim_value_invoice($comments = '', $trimVal = 150, $placement = 'bottom')
    {
        $String = $comments;
        $truncated = (strlen($String) > $trimVal) ? mb_substr(
            $String,
            0,
            $trimVal
        ) . '<span class="tol" rel="tooltip" data-placement="' . $placement . '" style="color:#0088cc" title="' . str_replace(
            '"',
            '&quot;',
            $String
        ) . '">... more </span>' : $String;

        return $truncated;
    }
}

if (!function_exists('fetch_employee_department'))
{
    function fetch_employee_department($state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_reports', $primaryLanguage);
        $CI->db->select('DepartmentMasterID, DepartmentDes');
        $CI->db->from('srp_departmentmaster');
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('hrms_reports_select_department')/*'Select Department'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('succession_header_active_status'))
{
    function succession_header_active_status($isActive, $headerID)
    {
        if ($isActive == '1')
        {
            return '<input type="checkbox" value="" checked onclick="header_active_check(this,' . $headerID . ')">';
        }
        else
        {
            return '<input type="checkbox" value="" onclick="header_active_check(this,' . $headerID . ')">';
        }
    }
}

if (!function_exists('succession_plan_link'))
{
    function succession_plan_link($segmentID, $description)
    {
        return '<div onclick="goto_succession_plans(' . $segmentID . ',\'' . $description . '\')"  class="succession_plan_link"><i class="fa fa-external-link"></i></div>';
    }
}

if (!function_exists('succession_header_edit'))
{
    function succession_header_edit($headerID, $description, $isActive)
    {
        return '<div class="btn-link" data-description="' . $description . '" data-is_active="' . $isActive . '" data-header_id="' . $headerID . '" onclick="edit_header.call(this)" rel="tooltip" title="Edit Header"><i class="fa fa-edit"></i></div>';
    }
}

if (!function_exists('sp_view_btn'))
{
    function sp_view_btn($spAutoID, $confirmedYN, $approvedYN)
    {
        $element = '';
        if ($approvedYN == 1)
        {
            $element .= '<span style="padding: 9px;">&nbsp;</span>';
        }
        else if ($confirmedYN != 1)
        {
            $element .= '<div class="btn-link action-button" style="color:red;" onclick="delete_sp(' . $spAutoID . ')"><i class="glyphicon glyphicon-trash"></i></div>';
        }
        else
        {

            $element .= '<a onclick="refer_back_confirmed_plan(' . $spAutoID . '); "><span title="" rel="tooltip" class="glyphicon glyphicon-repeat" data-original-title="Referback"></span></a>';
        }
        $element .= '<div class="btn-link  action-button" onclick="view_sp(' . $spAutoID . ')"><i class="glyphicon glyphicon-pencil"></i></div>';
        return $element;
    }
}

if (!function_exists('succession_plan_confirm_status'))
{
    function succession_plan_confirm_status($status)
    {
        $element = '<center>';
        if ($status == 0)
        {
            $element .= '<span class="label label-danger">&nbsp;</span>';
        }
        elseif ($status == 1)
        {
            $element .= '<span class="label label-success">&nbsp;</span>';
        }
        $element .= '</center>';

        return $element;
    }
}

if (!function_exists('sp_approval_drilldown'))
{
    function sp_approval_drilldown($con, $code, $autoID)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" class="label label-danger"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        elseif ($con == 1)
        {
            $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" class="label label-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('succession_plan_approval_action'))
{
    function succession_plan_approval_action($poID, $Level, $approved, $ApprovedID, $document, $isRejected)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'succession_plan_approval_modal("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","edit"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'refer_back_succession_plan("' . $poID . '"); \'><span title="Referback" rel="tooltip" class="glyphicon glyphicon-repeat act-btn-margin"></span></a>&nbsp;&nbsp';
            $status .= '<a onclick=\'succession_plan_approval_modal("' . $poID . '","' . $ApprovedID . '","' . $Level . '","' . $document . '","view"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open act-btn-margin"></span></a>&nbsp;&nbsp';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('succession_plan_confirmed_status'))
{
    function succession_plan_confirmed_status($confirmedYN, $approvedYN)
    {
        $status = '';
        if ($approvedYN == '1')
        {
            $status .= '<span class="label label-success">Approved</span>';
        }
        else if ($confirmedYN == '1')
        {
            $status .= '<span class="label label-success">Confirmed</span>';
        }
        else if ($confirmedYN == '0')
        {
            $status .= '<span class="label label-danger">Draft</span>';
        }
        else if ($confirmedYN == '2')
        {
            $status .= '<span class="label label-warning">Reffer backed</span>';
        }
        return $status;
    }
}

if (!function_exists('succession_plan_header_table_action'))
{
    function succession_plan_header_table_action($documentHeaderID, $confirmedYN)
    {
        $element = '';
        if ($confirmedYN != '1')
        {
            $element .= '<div class="btn-link action-button" style="color:red;" onclick="delete_sph(' . $documentHeaderID . ')"><i class="glyphicon glyphicon-trash"></i></div>';
        }
        return $element;
    }
}

if (!function_exists('approvalStatus'))
{
    function approvalStatus($con, $confirmedYN, $approvedYN, $policyCode, $code, $autoID)
    {

        $CI = &get_instance();
        $ApprovalforMaster = getPolicyValues($policyCode, 'All');
        //var_dump($ApprovalforMaster);
        $status = '<center>';

        if ($con == 0)
        {
            $status .= '<span class="label label-danger">&nbsp;</span><br>';
        }
        elseif ($con == 1)
        {
            $status .= '<span class="label label-success">&nbsp;</span><br>';
        }
        elseif ($con == 2)
        {
            $status .= '<span class="label label-warning">&nbsp;</span><br>';
        }
        elseif ($con == 3)
        {
            $status .= '<span class="label label-warning">&nbsp;</span><br>';
        }
        else
        {
            $status .= '-';
        }

        if ($ApprovalforMaster == 1)
        {

            if ($approvedYN == 0)
            {
                if ($confirmedYN == 0)
                {
                    $status .= '&nbsp<span class="label label-danger" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Draft" rel="tooltip" >Draft</span>';
                }
                else if ($confirmedYN == 1)
                {
                    $status .= '<a onclick="fetch_all_approval_users_modal(\'' . $code . '\',' . $autoID . ')" >';
                    $status .= '<span class="label label-warning" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Confirmed" rel="tooltip" >Confirmed</span></a>';
                }
                else if ($confirmedYN == 2)
                {
                    $status .= '<span class="label label-warning" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Referred back" rel="tooltip" >Referred back</span></a>';
                }
            }
            else if ($approvedYN == 1)
            {
                if ($confirmedYN == 1)
                {
                    $status .= '<a onclick="fetch_approval_user_modal(\'' . $code . '\',' . $autoID . ')" >';
                    $status .= '<span class="label label-success" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Approved" rel="tooltip" >Approved</span></a>';
                }
                else
                {
                    $status .= '<span class="label label-success">&nbsp;</span>';
                }
            }
            else if ($approvedYN == 2)
            {
                if ($confirmedYN == 1)
                {
                    $status .= '<a onclick="fetch_approval_reject_user_modal(\'' . $code . '\',' . $autoID . ')" >';
                    $status .= '<span class="label label-warning" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="Rejected" rel="tooltip" >Rejected</span></a>';
                }
                else
                {
                    $status .= '<span class="label label-success">&nbsp;</span>';
                }
            }

            if ($code == 'SUP')
            {
                $companyID = current_companyID();
                $approval_checklist = $CI->db->query("SELECT * FROM `srp_erp_document_approval_checklistdeails` where documentID = '{$code}' AND documentMasterID = '{$autoID}' AND companyID = '{$companyID}'")->row_array();

                if ($approval_checklist)
                {
                    $approval_checklist_master = $CI->db->query("SELECT * FROM `srp_erp_document_approval_checklist` where checklistID = '{$approval_checklist['checklistID']}'")->row_array();

                    $status .= '<br><span class="label label-danger" style="font-size: 9px; width: 10%; padding: 0.2em 1.9em .3em;" title="" rel="tooltip" >' . $approval_checklist_master['checklistDescription'] . '</span></a>';
                }
            }

            $status .= '</center>';

            return $status;
        }
    }
}



if (!function_exists('current_emp_location'))
{
    function current_emp_location()
    {
        $CI = &get_instance();
        $companyID = isset($CI->common_data['company_data']['emplanglocationid']) ? $CI->common_data['company_data']['emplanglocationid'] : NULL;

        return trim($companyID);
    }
}

if (!function_exists('softskills_designation_policy'))
{
    function softskills_designation_policy()
    {
        $CI = &get_instance();
        $CI->db->SELECT("value");
        $CI->db->FROM('srp_erp_companypolicy');
        $CI->db->WHERE('companypolicymasterID', 64);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $row = $CI->db->get()->row_array();
        $data = 0;
        if (!empty($row))
        {
            if ($row['value'] == 1)
            {
                $data = 1;
            }
            else
            {
                $data = 0;
            }
        }
        return $data;
    }
}

function fetch_support_contact_info()
{
    $CI = &get_instance();
    $db2 = $CI->load->database('db2', TRUE);
    $result = $db2->query("SELECT * FROM `supportcontacts` ")->result_array();
    return $result;
}

if (!function_exists('fetch_invoice_dropdown_new'))
{
    function fetch_invoice_dropdown_new($state = TRUE, $seccode = false)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $companyID = $CI->common_data['company_data']['company_id'];
        $data = $CI->db->query("SELECT `srp_erp_pos_invoice`.`invoiceID`, 
                                         `srp_erp_pos_invoice`.`invoiceCode` 
                                          FROM `srp_erp_pos_invoice`
                                          WHERE `srp_erp_pos_invoice`.`isVoid` = 0 
                                          AND `srp_erp_pos_invoice`.`companyID` = $companyID order by invoiceID desc LIMIT 10")->result_array();

        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Invoice');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['invoiceID'] ?? '')] = trim($row['invoiceCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('validate_code_duplication'))
{
    function validate_code_duplication($code, $fieldName, $documentID, $idField, $tableName, $com = "companyID")
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $code = $CI->db->query("SELECT {$fieldName} AS Code FROM {$tableName} WHERE {$com} = {$companyID} AND {$idField} != $documentID AND {$fieldName} = '{$code}'")->row('Code');

        return $code;
    }
}

if (!function_exists('setup_clientDB'))
{
    function setup_clientDB($conn_data)
    {
        $CI = &get_instance();

        $config['hostname'] = trim($CI->encryption->decrypt($conn_data["host"]));
        $config['username'] = trim($CI->encryption->decrypt($conn_data["db_username"]));
        $config['password'] = trim($CI->encryption->decrypt($conn_data["db_password"]));
        $config['database'] = trim($CI->encryption->decrypt($conn_data["db_name"]));
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

        //echo $conn_data['company_name'] . '<br>'.$config['database'] . '<br>';
        $CI->load->database($config, FALSE, TRUE);
    }
}


if (!function_exists('fetch_aws_companyimagepath'))
{
    function fetch_aws_companyimagepath($logoname)
    {
        $CI = &get_instance();
        $CI->load->library('s3');
        if ($logoname != '')
        {
            $path = 'images/logo/' . $logoname;
            $assetmasterattachment = $CI->s3->createPresignedRequest($path, '+1 hour');
        }
        else
        {
            $assetmasterattachment = $CI->s3->createPresignedRequest('images/item/no-image.png', '+1 hour');
        }

        return $assetmasterattachment;
    }
}

if (!function_exists('commission_scheme_action'))
{
    function commission_scheme_action($id, $confirmedYN, $approved, $createdUserID, $doc_code, $isDeleted, $confirmedByEmp)
    {
        $CI = &get_instance();
        $current_user = current_userID();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        $doc_code = str_replace('/', '-', $doc_code);

        $status .= '<a target="_blank" onclick="documentPageView_modal_CS(\'CS\',\'' . $id . '\')"><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

        if ($approved != 1 && $confirmedYN == 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="referback_commission_scheme(' . $id . ',\'CS\');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($isDeleted == 1)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="reOpen_commissoinScheme(' . $id . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>';
        }
        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="fetchPage(\'system/sales/erp_commission_scheme_new\',' . $id . ',\'Edit Commission Scheme\')"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }

        /* if (in_array($current_user , [$createdUserID, $confirmedByEmpID]) and $approved == 0 and $confirmedYN == 1 && $isDeleted == 0) {
            $status .= '<a onclick="refer_back_delivery_order(' . $id . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat refer-back-icon"></span></a> &nbsp;| &nbsp;';
        } */


        /* $status .= '&nbsp; | &nbsp;<a target="_blank" href="' . site_url('Delivery_order/load_order_confirmation_view') . '/' . $id . '/'.$doc_code.'" ><span title="Print"';
        $status .= '<span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';
         */
        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="delete_commission_scheme(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash delete-icon"></span></a>';
        }

        /*  <spsn class="pull-right"><a onclick="fetchPage(\'system/sales/erp_commission_scheme_new\',\'$1\',\'Commisssion Scheme\')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a onclick="delete_commission_scheme($1)"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a></span>
        */
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('approval_edit'))
{
    function approval_edit($autoID, $approvalLevelID, $approvedYN, $documentApprovedID, $documentID)
    {
        $status = '<span class="pull-right">';
        if ($approvedYN == 0)
        {
            $status .= '<a onclick=\'fetch_approval_cs("' . $autoID . '","' . $documentApprovedID . '","' . $approvalLevelID . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal_CS(\'CS\',\'' . $autoID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('fetch_employee_department2'))
{
    function fetch_employee_department2($state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('hrms_reports', $primaryLanguage);
        $CI->db->select('DepartmentMasterID, DepartmentDes');
        $CI->db->from('srp_departmentmaster');
        $CI->db->where('isActive', 1);
        $CI->db->where('Erp_companyID', current_companyID());
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('hrms_reports_select_department')/*'Select Department'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['DepartmentMasterID'] ?? '')] = trim($row['DepartmentDes'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_invoice_action_commission'))
{
    function load_invoice_action_commission($poID, $POConfirmedYN, $approved, $createdUserID, $confirmedYN, $isDeleted, $tempInvoiceID, $confirmedByEmpID, $isSytemGenerated)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if (empty($tempInvoiceID))
        {
            $status .= '<a onclick=\'attachment_modal(' . $poID . ',"Invoice","CINV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';

            if ($isDeleted == 1)
            {
                $status .= '<a onclick="reOpen_contract(' . $poID . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }

            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                if ($isSytemGenerated != 1)
                {
                    $status .= '<a onclick=\'fetchPage("system/invoices/erp_invoices_cs",' . $poID . ',"Edit Customer Invoice","PO"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
                }
                else
                {
                    $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
                }
            }

            if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $POConfirmedYN == 1 && $isDeleted == 0)
            {
                $status .= '<a onclick="referback_customer_invoice(' . $poID . ',' . $isSytemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'Commission\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            //$status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_cs/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
            if ($approved == 1)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="sendemail(' . $poID . ')" title="Send Mail" rel="tooltip"><i class="fa fa-envelope" aria-hidden="true"></i></a>';
            }
            if ($POConfirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="confirmCustomerInvoicefront(' . $poID . ') "><span title="Confirm" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
                $status .= '&nbsp;&nbsp;| &nbsp;&nbsp;<a onclick="delete_item(' . $poID . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
            }
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\',\'Commission\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';
            //$status .= '<a target="_blank" onclick="documentPageView_modal(\'CINV\',\'' . $poID . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>';

            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a target="_blank" href="' . site_url('invoices/load_invoices_conformation_cs/') . '/' . $poID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        }

        $outputs = get_pv_rv_based_on_policy('RV');
        if ($approved == 1)
        {
            $status .= '&nbsp; | &nbsp;<a onclick="traceDocument(' . $poID . ',\'CINV\')" title="Trace Document" rel="tooltip"><i class="fa fa-search" aria-hidden="true"></i></a> ';
        }

        if ($approved == 1 && $outputs)
        {
            $status .= ' &nbsp; | &nbsp; <a onclick="open_receipt_voucher_modal(' . $poID . ')" title="Create Receipt Voucher" rel="tooltip"><i class="fa fa-file-text" aria-hidden="true"></i></a>';
        }


        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('reupdate_companylocalwac'))
{
    function reupdate_companylocalwac($tblname, $documentAutoID, $columnname, $waccol, $documentID = null, $previousWac = null)
    {

        $CI = &get_instance();
        $companyID = current_companyID();

        switch ($documentID)
        {
            case 'SA':
            case 'SCNT':
                $CI->db->query("UPDATE $tblname
                JOIN (
               SELECT
               itemautoID,
              IFNULL(( sum( transactionAmount / companyLocalExchangeRate ) / sum( transactionQty / convertionRate ) ),0) AS wac
               FROM
               srp_erp_itemledger
               WHERE
               itemautoID IN ( SELECT itemautoID 
                               FROM $tblname 
                               WHERE companyID = $companyID 
                               AND $columnname = $documentAutoID )
               GROUP BY
               itemautoID
           ) ledger ON ledger.itemautoID = $tblname.itemAutoID
               set
               $tblname.$waccol=ledger.wac,
               $tblname.$previousWac=ledger.wac
               where $tblname.$columnname=$documentAutoID");

                break;
            case 'ST':
                $CI->db->query("CALL stock_transfer_wac_update($documentAutoID)");
                // $CI->db->query("UUPDATE $tblname
                //                         JOIN (
                //                             SELECT
                //                                 itemAutoID,
                //                                 IFNULL( ( sum( transactionAmount / companyLocalExchangeRate ) / sum( transactionQty / convertionRate ) ),0) AS wac
                //                             FROM
                //                                 srp_erp_itemledger
                //                             WHERE
                //                                 itemautoID IN (SELECT itemautoID FROM $tblname WHERE $columnname = $documentAutoID)
                //                             GROUP BY
                //                                 itemAutoID
                //                         ) ledger ON ledger.itemAutoID = $tblname.itemAutoID
                //                         set $tblname.$waccol=ledger.wac
                //                         where $tblname.$columnname=$documentAutoID");

                break;
            default:

                $CI->db->query("UPDATE $tblname
                JOIN (
               SELECT
               itemautoID,
              IFNULL( ( sum( transactionAmount / companyLocalExchangeRate ) / sum( transactionQty / convertionRate ) ),0) AS wac
               FROM
               srp_erp_itemledger
               WHERE
               itemautoID IN ( SELECT itemautoID 
                               FROM $tblname 
                               WHERE companyID = $companyID 
                               AND $columnname = $documentAutoID )
               GROUP BY
               itemautoID
           ) ledger ON ledger.itemautoID = $tblname.itemAutoID
               set $tblname.$waccol=ledger.wac
               where $tblname.$columnname=$documentAutoID");
        }
    }
}

if (!function_exists('load_ic_action'))
{
    function load_ic_action($id, $confirmedYN, $approved, $createdUserID, $confirmedByEmpID, $isDeleted)
    {

        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';

        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approved == 0 and $confirmedYN == 1)
        {
            $status .= '<a onclick="referbackic(' . $id . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
        }
        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal_IC(\'IC\',\'' . $id . '\',\'' . $confirmedYN . '\')" ><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;';
        }
        if ($confirmedYN == 1)
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal_IC(\'IC\',\'' . $id . '\',\'' . $confirmedYN . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('ic_action_approval'))
{ /*get ic action list*/
    function ic_action_approval($masterID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $status = '<span class="pull-right">';
        if ($approved == 0)
        {
            $status .= '<a onclick=\'fetch_approval("' . $masterID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>';
        }
        else
        {
            $status .= '<a target="_blank" onclick="documentPageView_modal_IC(\'IC\',\'' . $masterID . '\',\'\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }
        $status .= '</span>';
        return $status;
    }
}

if (!function_exists('payment_type'))
{
    function payment_type($in = [], $placeHolder = '')
    {
        $ci = &get_instance();
        $db2 = $ci->load->database('db2', TRUE);

        $db2->order_by('pay_description');
        if ($in)
        {
            $db2->where_in('id', $in);
        }
        $types_arr = $db2->get('system_payment_types')->result_array();

        $arr = ($placeHolder) ? ['' => $placeHolder] : [];
        foreach ($types_arr as $val)
        {
            $arr[$val['id']] = $val['pay_description'];
        }

        return $arr;
    }
}

if (!function_exists('view_acknowledgementDate'))
{
    function view_acknowledgementDate($acknowledgementDate)
    {
        $status = '';
        $acknowledgementDateYN = getPolicyValues('SAD', 'All');
        if (!empty($acknowledgementDateYN) && $acknowledgementDateYN == 1)
        {
            $status .= "<b><br> Acknowledgement Date :</b>" . $acknowledgementDate;
        }
        return $status;
    }
}

if (!function_exists('journal_entry_action_buyback'))
{
    function journal_entry_action_buyback($JVMasterAutoId, $confirmedYN, $approvedYN, $createdUserID, $isDeleted, $JVType, $confirmedByEmpID, $isSystemGenerated)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($JVType == 'Recurring')
        {
            $status .= '<a onclick=\'recurring_attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        else
        {
            $status .= '<a onclick=\'attachment_modal(' . $JVMasterAutoId . ',"Journal Entry","JV",' . $confirmedYN . ');\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($isDeleted == 1)
        {
            $status .= '<a onclick="reOpen_contract(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');"><span title="Re Open" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        if ($isSystemGenerated != 1)
        {
            if ($confirmedYN != 1 && $isDeleted == 0)
            {
                $status .= '<a onclick=\'fetchPage("system/finance/journal_entry_new",' . $JVMasterAutoId . ',"Edit Journal Entry","Journal Entry"); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
            }
        }
        else
        {
            $status .= '<a onclick=\'issystemgenerateddoc(); \'><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;| &nbsp;&nbsp;';
        }
        if (($createdUserID == trim($CI->session->userdata("empID")) || $confirmedByEmpID == trim($CI->session->userdata("empID"))) and $approvedYN == 0 and $confirmedYN == 1 && $isDeleted == 0)
        {
            $status .= '<a onclick="referback_journal_entry(' . $JVMasterAutoId . ',' . $isSystemGenerated . ');"><span title="Refer Back" rel="tooltip" class="glyphicon glyphicon-repeat" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp;';
        }
        $status .= '<a target="_blank" onclick="documentPageView_modal(\'JV\',\'' . $JVMasterAutoId . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;|&nbsp;&nbsp';
        //$status .= '<a target="_blank" href="' . site_url('Journal_entry/journal_entry_conformation/') . '/' . $JVMasterAutoId . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';
        $status .= '<a onclick="load_printtemp(' . $JVMasterAutoId . ');"><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a> ';

        if ($confirmedYN != 1 && $isDeleted == 0)
        {
            $status .= '&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="delete_journal_entry(' . $JVMasterAutoId . ',\'Invoices\');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';
        }

        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('load_invoice_detail_commission'))
{
    function load_invoice_detail_commission($invoiceNarration, $customermastername, $invoiceDate, $invoiceDueDate, $invoiceType, $referenceNo)
    {
        $CI = &get_instance();
        //$acknowledgementDateYN = getPolicyValues('SAD', 'All');
        $CI->load->library('session');
        $status = '';
        $status = '<span class="text-left">';
        $status .= '<b>Customer Name : </b> ' . $customermastername . ' <br> <b>Document Date : </b> ' . $invoiceDate . ' <b style="text-indent: 1%;">&nbsp | &nbsp Due Date : </b> ' . $invoiceDueDate . ' <br>';

        $status .= '<b>Type : </b> ' . $invoiceType . ' <br><b>Ref No : </b> ' . $referenceNo . ' <br> <b>Comments : </b> ' . ucwords(trim_value_invoice($invoiceNarration, 100, 'bottom')) . '';
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('get_jobcard_workflow_template'))
{
    function get_jobcard_workflow_template($status = TRUE)
    {
        $CI = &get_instance();
        $CompanyID = current_companyID();
        $flowserveLanguagePolicy = getPolicyValues('LNG', 'All');

        $CI->db->select("*");
        $CI->db->join(
            'srp_erp_mfq_systemworkflowcategory',
            'srp_erp_mfq_systemworkflowcategory.workflowID = srp_erp_mfq_workflowtemplate.workflowID'
        );
        $CI->db->from('srp_erp_mfq_workflowtemplate');
        $CI->db->where('srp_erp_mfq_workflowtemplate.workFlowID', 1);

        if ($flowserveLanguagePolicy == 'FlowServe')
        {
            $CI->db->where('srp_erp_mfq_workflowtemplate.workFlowCompanyID', $CompanyID);
        }


        $output = $CI->db->get()->result_array();
        if ($status)
        {
            $result = array('' => 'Select Process ');
        }
        else
        {
            $result = '';
        }
        if (!empty($output))
        {
            foreach ($output as $row)
            {
                $result[$row['workFlowTemplateID']] = $row['description'];
            }
        }
        return $result;
    }
}

if (!function_exists('fetch_po_detail_status'))
{
    function fetch_po_detail_status($purchaseOrderDetailsID)
    {
        $CI = &get_instance();
        $result = $CI->db->query("SELECT
                                `srp_erp_purchaseorderdetails`.*,
                                IFNULL(`grvDetail`.`receivedQty`, 0) AS `receivedQty`,
                                IFNULL(`podetail`.`bsireceivedQty`, 0) AS `bsireceivedQty`,
                                 `srp_erp_purchaseordermaster`.`purchaseOrderCode` 
                                 FROM
                                 `srp_erp_purchaseorderdetails`
                                 JOIN `srp_erp_purchaseordermaster` ON `srp_erp_purchaseordermaster`.`purchaseOrderID` = `srp_erp_purchaseorderdetails`.`purchaseOrderID`
                                 LEFT JOIN (SELECT 
                                            purchaseOrderDetailsID,
                                            ifnull( sum( srp_erp_grvdetails.receivedQty ), 0 ) AS receivedQty
                                            FROM 
                                            srp_erp_grvdetails
                                            GROUP BY
                                            purchaseOrderDetailsID) grvDetail on grvDetail.purchaseOrderDetailsID = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID`
                                LEFT JOIN( SELECT
                                            purchaseOrderDetailsID,	
                                            ifnull( sum( srp_erp_paysupplierinvoicedetail.requestedQty ), 0 ) AS bsireceivedQty
                                            from 
                                            srp_erp_paysupplierinvoicedetail
                                            GROUP BY
                                            purchaseOrderDetailsID) podetail  ON `podetail`.`purchaseOrderDetailsID` = `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID` 
                                            WHERE
                                            `srp_erp_purchaseorderdetails`.`purchaseOrderDetailsID` = '{$purchaseOrderDetailsID}' 
                                            GROUP BY
                                            `purchaseOrderDetailsID`")->row_array();
        return $result;
    }
}

if (!function_exists('fetch_itemledger_currentstock'))
{
    function fetch_itemledger_currentstock($itemAutoID)
    {
        $CompanyID = current_companyID();
        $CI = &get_instance();
        $result = $CI->db->query("SELECT
                                  SUM(transactionQTY/convertionRate) as currentStock
                                  FROM
                                  srp_erp_itemledger 
                                  WHERE
                                  companyID = $CompanyID
                                  AND itemAutoID = $itemAutoID
                                  GROUP BY
                                  ItemAutoID")->row("currentStock");
        return $result;
    }
}

if (!function_exists('fetch_itemledger_transactionAmount'))
{
    function fetch_itemledger_transactionAmount($itemAutoID, $exchangerate)
    {
        $CompanyID = current_companyID();
        $CI = &get_instance();
        $result = $CI->db->query("SELECT
                                  (SUM(SUBSTRING(transactionAmount,1,16)/$exchangerate)) / (sum( transactionQty / convertionRate))  as Amount
                                  FROM
                                  srp_erp_itemledger 
                                  WHERE
                                  companyID = $CompanyID
                                  AND itemAutoID = $itemAutoID
                                  GROUP BY
                                  ItemAutoID")->row("Amount");
        return $result;
    }
}

if (!function_exists('fetch_itemledger_transactionAmount_validation'))
{
    function fetch_itemledger_transactionAmount_validation($itemAutoID)
    {
        $CompanyID = current_companyID();
        $CI = &get_instance();
        $result = $CI->db->query("SELECT
            srp_erp_itemmaster.itemSystemCode,
            itemName,
            TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND(( ( (SUM( transactionAmount / srp_erp_itemledger.companyLocalExchangeRate)) / (sum( transactionQty / convertionRate) )  )), 3 )))))) AS Amount
            FROM
            srp_erp_itemledger
            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
            WHERE
            srp_erp_itemledger.companyID = $CompanyID AND srp_erp_itemmaster.mainCategoryID = 1
            AND srp_erp_itemledger.itemAutoID IN ($itemAutoID) 
            GROUP BY
            srp_erp_itemledger.ItemAutoID 
            HAVING
            Amount < 0")->result_array();
        return $result;
    }
}

if (!function_exists('load_ch_action'))
{
    function load_ch_action($id, $isDeleted)
    {
        $CI = &get_instance();
        $CI->load->library('session');
        $status = '<span class="pull-right">';
        if ($isDeleted == 0)
        {
            $status .= '<a onclick="open_edit_commission_hierarchy_model(' . $id . ')"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp; | &nbsp;&nbsp;';
            $status .= '<a onclick="delete_commission_hierarchy(' . $id . ')"><span class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>&nbsp;&nbsp;&nbsp;';
        }
        $status .= '</span>';

        return $status;
    }
}

if (!function_exists('check_finance_year_id_exists'))
{
    function check_finance_year_id_exists($financeYearID)
    {
        $CI = &get_instance();
        $documentsExistRecords = array();
        $documentCount = 0;
        $companyID = current_companyID();
        $db2 = $CI->load->database('db2', TRUE);
        $database_name = $db2->query("SELECT db_name FROM `srp_erp_company` WHERE company_id = $companyID")->row('db_name');
        $database_name = $CI->encryption->decrypt($database_name);
        $financeYearTbl = $CI->db->query("SELECT
                                           table_name as tablename
                                           FROM 
                                           information_schema.`COLUMNS`
                                           where 
                                           table_schema = '{$database_name}'
                                           AND (column_name = 'companyFinanceYearID' or column_name = 'matchID' or column_name = 'financeYearID')
                                           AND table_name NOT IN ('srp_erp_companyfinanceperiod','srp_erp_companyfinanceyear','srp_erp_pvadvancematchdetails','srp_erp_rvadvancematchdetails','srp_erp_budgettransferdetail')
                                          ")->result_array();
        foreach ($financeYearTbl as $val)
        {
            if ($val['tablename'] == 'srp_erp_pvadvancematch')
            {
                $financeyearID = $CI->db->query("SELECT
                                                 DATE(beginingDate) as beginingDate,
                                                 DATE(endingDate) as endingDate
                                                 FROM
                                                `srp_erp_companyfinanceyear`
                                                WHERE 
                                                companyFinanceYearID = $financeYearID")->row_array();
                $pvadvancematchExist = $CI->db->query("  SELECT 
                                                        COUNT(matchID) as count
                                                        from 
                                                        srp_erp_pvadvancematch 
                                                        where 
                                                        companyID  = $companyID AND
                                                        matchDate  BETWEEN '{$financeyearID['beginingDate']}' AND '{$financeyearID['endingDate']}'")->row('count');
                if ($pvadvancematchExist > 0)
                {
                    array_push($documentsExistRecords, $pvadvancematchExist);
                }
            }
            if ($val['tablename'] == 'srp_erp_rvadvancematch')
            {
                $financeyearID = $CI->db->query("SELECT
                                                 DATE(beginingDate) as beginingDate,
                                                 DATE(endingDate) as endingDate
                                                 FROM
                                                `srp_erp_companyfinanceyear`
                                                WHERE 
                                                companyFinanceYearID = $financeYearID")->row_array();
                $rvadvancematchExist = $CI->db->query("  SELECT 
                                                        COUNT(matchID) as count
                                                        from 
                                                        srp_erp_rvadvancematch 
                                                        where 
                                                        companyID  = $companyID AND
                                                        matchDate  BETWEEN '{$financeyearID['beginingDate']}' AND '{$financeyearID['endingDate']}'")->row('count');
                if ($rvadvancematchExist > 0)
                {
                    array_push($documentsExistRecords, $pvadvancematchExist);
                }
            }


            switch ($val['tablename'])
            {
                case 'srp_erp_budgettransfer':
                case 'srp_erp_financeyeardocumentcodemaster':
                case 'srp_erp_locationdocumentcodemaster':
                    $colname = 'financeYearID';
                    break;
                default:
                    $colname = 'companyFinanceYearID';
            }
            if ($val['tablename'] != 'srp_erp_rvadvancematch')
            {
                $documents = $CI->db->query("select
                                            COUNT(*) as count
                                            from 
                                            {$val['tablename']}
                                            where 
                                            $colname = $financeYearID")->row('count');
                if ($documents > 0)
                {
                    array_push($documentsExistRecords, $documents);
                }
            }
        }
        if (!empty($documentsExistRecords))
        {
            $documentCount = 1;
        }
        else
        {
            $documentCount = 0;
        }
        return $documentCount;
    }
}



if (!function_exists('tax_calculation_vat'))
{
    function tax_calculation_vat($tblName, $dataInsert, $taxType, $masterIDColName, $masterID, $tax_total, $documentID, $documentDetailID = null, $discountAmount = 0, $lineWise = 0, $isRcmApplicable = 0, $taxDetailAutoID = null, $retensionAmount = 0)
    {

        $CI = &get_instance();
        $companyID = current_companyID();
        $vatRegisterYN = $CI->db->query("SELECT vatRegisterYN FROM `srp_erp_company` where company_id = $companyID ")->row('vatRegisterYN');

        if ($lineWise == 0  && (($dataInsert != null)))
        {
            $CI->db->select('taxFormulaMasterID');
            $CI->db->where($masterIDColName, $masterID);
            $CI->db->where('taxFormulaMasterID', $taxType);
            $tax_detail = $CI->db->get($tblName)->row('taxFormulaMasterID');
            if (!empty($tax_detail))
            {
                return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already! ');
            }
        }

        $last_id = null;
        if ($documentID != 'GPOS')
        {
            $CI->db->trans_start();
        }
        /*&& $documentID != 'BSI'*/
        // if ($documentID != 'CNT' && $documentID != 'GRV' && $documentID != 'CINV' && $documentID != 'DO' && $documentID != 'PO-PRQ' && $documentID != 'RV'  && $documentID != 'CN' && $documentID != 'DN' && $documentID != 'PV') {

        if ($lineWise == 0 && (($dataInsert != null)))
        {
            if (($documentID == 'CNT' || $documentID == 'PO'))
            {
                foreach ($dataInsert as $key => $val)
                {
                    $data["{$key}"] = $val;
                }
                $result = $CI->db->insert($tblName, $data);
                $last_id = $CI->db->insert_id();
            }
            else
            {
                $result = true;
            }
        }
        else
        {
            $result = true;
        }

        if ($documentID == 'PO-PRQ')
        {
            $documentID = 'PO';
        }

        $documentTypeID = 0; // To get GL type. 0->Default  1-> input 2->output 3-> input transfer  4-> out put transfer
        switch ($documentID)
        {
            case 'GRV':
            case 'GRV-ADD':
                $documentTypeID = 3;
                break;
            case 'DO':
                $documentTypeID = 4;
                break;
            case 'CINV':
            case 'MCINV':
            case 'RV':
            case 'CNT':
            case 'SLR':
            case 'CN':
            case 'GPOS':
                $documentTypeID = 2;
                break;
            case 'BSI':
            case 'PV':
            case 'PO':
            case 'PR':
            case 'DN':
                $documentTypeID = 1;
                break;
            default:
                $documentTypeID = 0;
        }

        if ($lineWise == 1)
        {
            $discount = $discountAmount;
        }
        else
        {
            switch ($documentID)
            {
                case 'PO':
                    $discount = $CI->db->query("SELECT
	                                                ( pomaster.generalDiscountPercentage / 100 )*(SUM( totalAmount )) AS generalTax,
	                                                linewiseDiscount.discountamountLine as discountamountLine,	
                                                    (SUM(totalAmount) +linewiseDiscount.discountamountLine)  as taxapplicableAmt
       
                                                    FROM
                                                    srp_erp_purchaseordermaster pomaster 
                                                    LEFT JOIN srp_erp_purchaseorderdetails detailtbl ON pomaster.purchaseOrderID = detailtbl.purchaseOrderID
                                                    LEFT JOIN (SELECT
                                                    IFNULL(SUM( (((unitAmount+discountAmount) * (discountPercentage/100)))*requestedQty),0)  as discountamountLine,
                                                    purchaseOrderID
                                                    FROM
                                                    `srp_erp_purchaseorderdetails`
                                                    where 
                                                    companyID = $companyID 
                                                    AND purchaseOrderID = $masterID
                                                    GROUP BY
                                                    purchaseOrderID )linewiseDiscount on linewiseDiscount.purchaseOrderID = pomaster.purchaseOrderID
                                                    WHERE
                                                    pomaster.companyID = $companyID 
                                                    AND pomaster.purchaseOrderID = $masterID")->row_array();

                    $tax_total = ($discount['taxapplicableAmt']);
                    $discount = ($discountAmount + $discount['discountamountLine']);


                    break;
                default:
                    $discount = 0;
            }
        }

        if ($result)
        {
            if ($lineWise == 1)
            {
                $detail_filter = ' AND documentDetailAutoID = ' . $documentDetailID;
                $taxType_filter = ' AND taxFormulaMasterID = ' . $taxType;
            }
            else
            {
                if ($documentID == 'GRV-ADD')
                {
                    $detail_filter = ' AND documentDetailAutoID = ' . $documentDetailID;
                    $taxType_filter = ' AND taxFormulaMasterID = ' . $taxType;
                }
                else
                {
                    $detail_filter = ' AND documentDetailAutoID IS NULL';
                    $taxType_filter = '';
                }
            }
            $isExistYN = 1;
            //AND documentDetailAutoID = $documentDetailID
            $docTaxType = $CI->db->query("SELECT
                                          taxFormulaMasterID
                                          FROM
                                            srp_erp_taxledger
                                          Where 
                                            documentID = '{$documentID}'
                                            AND documentMasterAutoID = $masterID
                                            $taxType_filter
                                            $detail_filter")->row('taxFormulaMasterID');

            if ($taxType != $docTaxType)
            {
                $isExistYN = 0;
                if ($lineWise == 1)
                {
                    fetchExistsDetailTBL($documentID, $masterID, $documentDetailID);
                }
            }
            else
            {
                $isExistYN = 1;
            }


            $CI->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories,IFNULL(ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage) as taxcalperce,
                    CASE
                    WHEN (' . $documentTypeID . ' = 1 AND taxCategory = 2) THEN inputVatGLAccountAutoID
                    WHEN (' . $documentTypeID . ' = 2 AND taxCategory = 2) THEN outputVatGLAccountAutoID
                    WHEN (' . $documentTypeID . ' = 3 AND taxCategory = 2) THEN inputVatTransferGLAccountAutoID
                    WHEN (' . $documentTypeID . ' = 4 AND taxCategory = 2) THEN outputVatTransferGLAccountAutoID
                    ELSE supplierGLAutoID
                    END as taxGLAutoID,
                    outputVatGLAccountAutoID,
                    outputVatTransferGLAccountAutoID,
                    IF(srp_erp_taxmaster.taxCategory = 2,' . $vatRegisterYN . ', srp_erp_taxmaster.isClaimable) AS isClaimable
                    
                    ');
            $CI->db->join("(SELECT  
                    taxFormulaDetailID,
                    taxPercentage
                    FROM
                    `srp_erp_taxledger` 
                    WHERE
                    documentID = '{$documentID}'
                    AND documentMasterAutoID = $masterID
                    $detail_filter) ledger", "ledger.taxFormulaDetailID = srp_erp_taxcalculationformuladetails.formulaDetailID", "LEFT");
            $CI->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID', 'LEFT');
            $CI->db->where('taxCalculationformulaID', $taxType);
            $formulaDtl = $CI->db->get('srp_erp_taxcalculationformuladetails')->result_array();
            if (!empty($formulaDtl))
            {
                $ledgerIDArr = array();
                $taxAmount = 0;
                foreach ($formulaDtl as $val)
                {

                    $sortOrder = $val['sortOrder'];
                    $tax_categories = $CI->db->query("SELECT
                                                      srp_erp_taxcalculationformuladetails.*,srp_erp_taxmaster.taxDescription,srp_erp_taxmaster.taxPercentage,
                                                      srp_erp_taxmaster.taxCategory as taxCategory
                                                      FROM
                                                      srp_erp_taxcalculationformuladetails
                                                      LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                      WHERE
	                                                  taxCalculationformulaID =$taxType
                                                      AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder")->result_array();

                    $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories, $tax_total, $discount, $val['taxcalperce'], $taxType, $documentID, $masterID, $documentDetailID);
                    $formulaDecodeval = $formulaBuilder['formulaDecode'];


                    if ($isRcmApplicable == 1)
                    {

                        if ($formulaBuilder['taxCategory'] != 2)
                        {
                            $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                        }
                        else
                        {
                            $amounttx = 0;
                        }
                    }
                    else
                    {
                        $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                    }
                    $amounttxledger = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                    $dataleg['documentID'] = $documentID;
                    $dataleg['documentMasterAutoID'] = $masterID;
                    $dataleg['documentDetailAutoID'] = $documentDetailID;
                    $dataleg['taxDetailAutoID'] = ($last_id != '' ? $last_id : $taxDetailAutoID);
                    $dataleg['taxPercentage'] = $val['taxcalperce'];
                    $dataleg['ismanuallychanged'] = 0;
                    $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                    $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                    $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                    $dataleg['taxGlAutoID'] = $val['taxGLAutoID'];
                    $dataleg['rcmApplicableYN'] = (($isRcmApplicable == 1) ? 1 : 0);
                    $dataleg['outputVatTransferGL'] = $val['outputVatTransferGLAccountAutoID'];
                    $dataleg['outputVatGL'] = $val['outputVatGLAccountAutoID'];
                    $dataleg['amount'] = $amounttxledger;
                    $dataleg['isClaimable'] = $val['isClaimable'];
                    $dataleg['formula'] = $val['formula'];
                    $dataleg['companyCode'] = $CI->common_data['company_data']['company_code'];
                    $dataleg['companyID'] = $CI->common_data['company_data']['company_id'];
                    $dataleg['createdUserGroup'] = $CI->common_data['user_group'];
                    $dataleg['createdPCID'] = $CI->common_data['current_pc'];
                    $dataleg['createdUserID'] = $CI->common_data['current_userID'];
                    $dataleg['createdUserName'] = $CI->common_data['current_user'];
                    $dataleg['createdDateTime'] = $CI->common_data['current_date'];
                    if ($isExistYN == 0)
                    {
                        $Dresult = $CI->db->insert('srp_erp_taxledger', $dataleg);
                        $ledgerIDArr[] = $CI->db->insert_id();
                    }
                    else
                    {

                        $taxLedger = $CI->db->query("SELECT taxLedgerAutoID FROM `srp_erp_taxledger` Where 
                                                 documentID = '{$documentID}'  AND documentMasterAutoID =  '{$masterID}'
                                                 $detail_filter  AND taxFormulaDetailID = '{$val['formulaDetailID']}' ")->row('taxLedgerAutoID');

                        $CI->db->where('taxLedgerAutoID', $taxLedger);
                        $CI->db->update('srp_erp_taxledger', $dataleg);

                        $ledgerIDArr[] = $taxLedger;
                    }

                    $taxAmount += ($isRcmApplicable == 1 ? $amounttx : $amounttxledger);
                }
                //exit;


                if ($lineWise == 1)
                {
                    $ledgerID = implode(',', $ledgerIDArr);
                    switch ($documentID)
                    {
                        case 'PO':
                        case 'PO-PRQ':
                            $ledger_details = $CI->db->query("SELECT
                                                                        srp_erp_suppliermaster.supplierCountryID,
                                                                        vatEligible,
                                                                        supplierID,
                                                                        supplierLocationID,
                                                                        locationType 
                                                                    FROM
                                                                        srp_erp_purchaseordermaster
                                                                        LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_purchaseordermaster.supplierID
                                                                        LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID 
                                                                    WHERE
                                                                        purchaseOrderID = {$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['supplierCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['supplierID'];
                            $ledgerUpdate_details['locationID'] = $ledger_details['supplierLocationID'];
                            $ledgerUpdate_details['locationType'] = $ledger_details['locationType'];
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('purchaseOrderDetailsID', $documentDetailID);
                            $CI->db->update('srp_erp_purchaseorderdetails', $data_detailTBL);
                        case 'GRV':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_suppliermaster.supplierCountryID,
                                                                    vatEligible,
                                                                    supplierID,
                                                                    supplierLocationID,
                                                                    locationType 
                                                                FROM
                                                                    srp_erp_grvmaster
                                                                    LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_grvmaster.supplierID
                                                                    LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID 
                                                                WHERE
                                                                    grvAutoID = {$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['supplierCountryID'] ?? '';
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'] ?? '';
                            $ledgerUpdate_details['partyID'] = $ledger_details['supplierID'] ?? '';
                            $ledgerUpdate_details['locationID'] = $ledger_details['supplierLocationID'] ?? '';
                            $ledgerUpdate_details['locationType'] = $ledger_details['locationType'] ?? '';
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('grvDetailsID', $documentDetailID);
                            $CI->db->update('srp_erp_grvdetails', $data_detailTBL);
                            break;
                        case 'CNT':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    CustomerAutoID
                                                                FROM
                                                                    srp_erp_contractmaster
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_contractmaster.customerID
                                                                WHERE
                                                                    contractAutoID ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['CustomerAutoID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('contractDetailsAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_contractdetails', $data_detailTBL);
                            break;
                        case 'CINV':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    CustomerAutoID
                                                                FROM
                                                                    srp_erp_customerinvoicemaster
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerinvoicemaster.customerID
                                                                WHERE
                                                                    invoiceAutoID ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['CustomerAutoID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            if (isset($dataInsert['invoiceType']) && $dataInsert['invoiceType'] == 'DeliveryOrder')
                            {
                                $data_detailTBL['transactionAmount'] = $tax_total - $discountAmount + $retensionAmount;
                            }
                            else
                            {
                                $data_detailTBL['transactionAmount'] = $tax_total + $taxAmount - $discountAmount + $retensionAmount;
                            }

                            $CI->db->where('invoiceDetailsAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_customerinvoicedetails', $data_detailTBL);
                            break;
                        case 'RV':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    CustomerAutoID
                                                                FROM
                                                                    srp_erp_customerreceiptmaster
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_customerreceiptmaster.customerID
                                                                WHERE
                                                                    receiptVoucherAutoId ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['CustomerAutoID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $data_detailTBL['transactionAmount'] = $tax_total + $taxAmount - $discountAmount;
                            $CI->db->where('receiptVoucherDetailAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_customerreceiptdetail', $data_detailTBL);
                            break;
                        case 'DO':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    CustomerAutoID
                                                                FROM
                                                                    srp_erp_deliveryorder
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_deliveryorder.customerID
                                                                WHERE
                                                                    DOAutoID ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['CustomerAutoID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $data_detailTBL['transactionAmount'] = $tax_total + $taxAmount - $discountAmount;
                            $data_detailTBL['deliveredTransactionAmount'] = $tax_total + $taxAmount - $discountAmount;
                            $CI->db->where('DODetailsAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_deliveryorderdetails', $data_detailTBL);
                            break;
                        case 'PV':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_suppliermaster.supplierCountryID,
                                                                    vatEligible,
                                                                    partyID,
                                                                    supplierLocationID,
                                                                    locationType
                                                                FROM
                                                                    srp_erp_paymentvouchermaster
                                                                    LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paymentvouchermaster.partyID
                                                                    LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID 
                                                                WHERE
                                                                    payVoucherAutoId ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['supplierCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['partyID'];
                            $ledgerUpdate_details['locationID'] = $ledger_details['supplierLocationID'];
                            $ledgerUpdate_details['locationType'] = $ledger_details['locationType'];
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('payVoucherDetailAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_paymentvoucherdetail', $data_detailTBL);
                            break;
                        case 'BSI':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_suppliermaster.supplierCountryID,
                                                                    vatEligible,
                                                                    supplierID,
                                                                    supplierLocationID,
                                                                    locationType 
                                                                FROM
                                                                    srp_erp_paysupplierinvoicemaster
                                                                    LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_paysupplierinvoicemaster.supplierID
                                                                    LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID 
                                                                WHERE
                                                                    InvoiceAutoID ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['supplierCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['supplierID'];
                            $ledgerUpdate_details['locationID'] = $ledger_details['supplierLocationID'];
                            $ledgerUpdate_details['locationType'] = $ledger_details['locationType'];
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('InvoiceDetailAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_paysupplierinvoicedetail', $data_detailTBL);
                            break;
                        case 'CN':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    customerID
                                                                FROM
                                                                    srp_erp_creditnotemaster
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.CustomerAutoID = srp_erp_creditnotemaster.customerID
                                                                WHERE
                                                                    creditNoteMasterAutoID ={$masterID}")->row_array();


                            $credit_note_detail = $CI->db->query("SELECT
                                                                    transactionAmount,
                                                                    companyLocalExchangeRate,
                                                                    companyReportingExchangeRate 
                                                                    FROM
                                                                    srp_erp_creditnotedetail
                                                                    WHERE
                                                                    creditNoteDetailsID = {$documentDetailID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['customerID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $data_detailTBL['transactionAmount'] = ($credit_note_detail['transactionAmount'] + $taxAmount);
                            $data_detailTBL['companyLocalAmount'] = (($credit_note_detail['transactionAmount'] + $taxAmount) / $credit_note_detail['companyLocalExchangeRate']);
                            $data_detailTBL['companyReportingAmount'] = (($credit_note_detail['transactionAmount'] + $taxAmount) / $credit_note_detail['companyReportingExchangeRate']);
                            $CI->db->where('creditNoteDetailsID', $documentDetailID);
                            $CI->db->update('srp_erp_creditnotedetail', $data_detailTBL);
                            break;
                        case 'DN':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_suppliermaster.supplierCountryID,
                                                                    vatEligible,
                                                                    supplierID,
                                                                    supplierLocationID,
                                                                    locationType 
                                                                FROM
                                                                    srp_erp_debitnotemaster
                                                                    LEFT JOIN srp_erp_suppliermaster ON srp_erp_suppliermaster.supplierAutoID = srp_erp_debitnotemaster.supplierID
                                                                    LEFT JOIN srp_erp_location ON srp_erp_suppliermaster.supplierLocationID = srp_erp_location.locationID 
                                                                WHERE
                                                                    debitNoteMasterAutoID ={$masterID}")->row_array();


                            $debit_note_detail = $CI->db->query("select 
                                                                   transactionAmount,
                                                                   companyLocalExchangeRate,
                                                                   companyReportingExchangeRate
                                                                   from 
                                                                   srp_erp_debitnotedetail
                                                                   where 
                                                                   debitNoteDetailsID = {$documentDetailID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['supplierCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['supplierID'];
                            $ledgerUpdate_details['locationID'] = $ledger_details['supplierLocationID'];
                            $ledgerUpdate_details['locationType'] = $ledger_details['locationType'];
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $data_detailTBL['transactionAmount'] = ($debit_note_detail['transactionAmount'] + $taxAmount);
                            $data_detailTBL['companyLocalAmount'] = (($debit_note_detail['transactionAmount'] + $taxAmount) / $debit_note_detail['companyLocalExchangeRate']);
                            $data_detailTBL['companyReportingAmount'] = (($debit_note_detail['transactionAmount'] + $taxAmount) / $debit_note_detail['companyReportingExchangeRate']);
                            $CI->db->where('debitNoteDetailsID', $documentDetailID);
                            $CI->db->update('srp_erp_debitnotedetail', $data_detailTBL);
                            break;
                        case 'MCINV':
                            $ledger_details = $CI->db->query("SELECT
                                                                    srp_erp_customermaster.customerCountryID,
                                                                    vatEligible,
                                                                    srp_erp_customermaster.CustomerAutoID 
                                                                FROM
                                                                    srp_erp_mfq_customerinvoicemaster
                                                                    LEFT JOIN srp_erp_mfq_customermaster ON srp_erp_mfq_customerinvoicemaster.mfqCustomerAutoID = srp_erp_mfq_customermaster.mfqCustomerAutoID 
                                                                    LEFT JOIN srp_erp_customermaster ON srp_erp_customermaster.customerAutoID = srp_erp_mfq_customermaster.CustomerAutoID
                                                                WHERE
                                                                    invoiceAutoID ={$masterID}")->row_array();

                            $ledgerUpdate_details['countryID'] = $ledger_details['customerCountryID'];
                            $ledgerUpdate_details['partyVATEligibleYN'] = $ledger_details['vatEligible'];
                            $ledgerUpdate_details['partyID'] = $ledger_details['CustomerAutoID'];
                            $ledgerUpdate_details['locationID'] = null;
                            $ledgerUpdate_details['locationType'] = null;
                            $CI->db->where('taxLedgerAutoID IN (' . $ledgerID . ')');
                            $CI->db->update('srp_erp_taxledger', $ledgerUpdate_details);

                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $data_detailTBL['transactionAmount'] = $tax_total + $taxAmount - $discountAmount;
                            $CI->db->where('invoiceDetailsAutoID', $documentDetailID);
                            $CI->db->update('srp_erp_mfq_customerinvoicedetails', $data_detailTBL);
                            break;

                        case 'GPOS':
                            $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                            $data_detailTBL['taxAmount'] = $taxAmount;
                            $CI->db->where('invoiceDetailsID', $documentDetailID);
                            $CI->db->update('srp_erp_pos_invoicedetail', $data_detailTBL);
                            break;
                    }
                }

                if ($documentID == 'GRV-ADD')
                {
                    $data_detailTBL['taxCalculationformulaID'] = $dataleg['taxFormulaMasterID'];
                    $data_detailTBL['taxAmount'] = $taxAmount;
                    $CI->db->where('id', $documentDetailID);
                    $CI->db->update('srp_erp_grv_addon', $data_detailTBL);
                }
            }
            else
            {
                if ($documentID == 'GRV' || $documentID == 'GPOS')
                {
                    //nothing to execute.
                }
                else
                {
                    $CI->db->delete($tblName, array('taxDetailAutoID' => trim($last_id)));
                }
            }
        }

        if ($documentID != 'GPOS')
        {
            $CI->db->trans_complete();
            if ($CI->db->trans_status() === FALSE)
            {
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail Save Failed ');
            }
            else
            {

                $CI->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail Saved Successfully.', 'last_id' => $last_id);
            }
        }
        else
        {
            return 1;
        }
    }
}

//update tax calculation 
if (!function_exists('tax_calculation_update_vat'))
{
    function tax_calculation_update_vat($tblName, $masterColName, $masterAutoID, $taxAmount, $discountAmount, $documentID, $isLineWise = 0)
    {

        $CI = &get_instance();
        $companyID = current_companyID();
        $CI->db->select('*');
        $CI->db->where($masterColName, $masterAutoID);
        $tax_detail = $CI->db->get($tblName)->result_array();
        foreach ($tax_detail as $valu)
        {
            $CI->db->where('taxDetailAutoID', $valu['taxDetailAutoID']);
            $CI->db->where('documentMasterAutoID', $masterAutoID);
            $CI->db->where('documentDetailAutoID', null);
            $CI->db->where('documentID', $documentID);
            $CI->db->delete('srp_erp_taxledger');
            // $res = $CI->db->delete('srp_erp_taxledger', array('taxDetailAutoID' => $valu['taxDetailAutoID'], 'documentMasterAutoID' => $masterAutoID, 'documentDetailAutoID' => null, 'documentID' => $documentID));
            if ($CI->db->affected_rows() > 0)
            {
                $CI->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories,IFNULL(ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage)  as taxcalperce');
                $CI->db->join("(SELECT  
                       taxFormulaDetailID,
                       taxPercentage
                       FROM
                       `srp_erp_taxledger` 
                       WHERE
                       documentID = '{$documentID}'
                       AND documentMasterAutoID = $masterAutoID
                       AND documentDetailAutoID = null) ledger", "ledger.taxFormulaDetailID = srp_erp_taxcalculationformuladetails.formulaDetailID", "LEFT");
                $CI->db->where('taxCalculationformulaID', $valu['taxFormulaMasterID']);
                $CI->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID', 'LEFT');
                $formulaDtl = $CI->db->get('srp_erp_taxcalculationformuladetails')->result_array();
                $taxCalculationformulaID = $valu['taxFormulaMasterID'];


                switch ($documentID)
                {
                    case 'PO':
                        $discount = $CI->db->query("SELECT
	                                                ( pomaster.generalDiscountPercentage / 100 )*(SUM( totalAmount )) AS generalTax,
	                                                linewiseDiscount.discountamountLine as discountamountLine
                                                    FROM
                                                    srp_erp_purchaseordermaster pomaster 
                                                    LEFT JOIN srp_erp_purchaseorderdetails detailtbl ON pomaster.purchaseOrderID = detailtbl.purchaseOrderID
                                                    LEFT JOIN (SELECT
                                                    IFNULL(SUM( (((unitAmount+discountAmount) * (discountPercentage/100)))*requestedQty),0)  as discountamountLine,
                                                    purchaseOrderID
                                                    FROM
                                                    `srp_erp_purchaseorderdetails`
                                                    where 
                                                    companyID = $companyID 
                                                    AND purchaseOrderID = $masterAutoID
                                                    GROUP BY
                                                    purchaseOrderID )linewiseDiscount on linewiseDiscount.purchaseOrderID = pomaster.purchaseOrderID
                                                    WHERE
                                                    pomaster.companyID = $companyID 
                                                    AND pomaster.purchaseOrderID = $masterAutoID")->row_array();

                        $taxAmount = ($taxAmount + $discount['discountamountLine']);

                        $discountAmount = ($discount['generalTax'] + $discount['discountamountLine']);


                        break;
                    default:
                        $discount = 0;
                }



                if (!empty($formulaDtl))
                {
                    foreach ($formulaDtl as $val)
                    {
                        $sortOrder = $val['sortOrder'];
                        $tax_categories = $CI->db->query("SELECT 
                                                            srp_erp_taxcalculationformuladetails.*,
                                                            srp_erp_taxmaster.taxDescription,
                                                            srp_erp_taxmaster.taxPercentage,
                                                            srp_erp_taxmaster.taxCategory as taxCategory
                                                            FROM
                                                            srp_erp_taxcalculationformuladetails
                                                            LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                            WHERE
                                                            taxCalculationformulaID = $taxCalculationformulaID
                                                            AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();
                        $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories, $taxAmount, $discountAmount, $val['taxcalperce'], $taxCalculationformulaID, $documentID, $masterAutoID, null);
                        $formulaDecodeval = $formulaBuilder['formulaDecode'];
                        $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row_array();
                        $dataleg['documentID'] = $documentID;
                        $dataleg['documentMasterAutoID'] = $masterAutoID;
                        $dataleg['taxDetailAutoID'] = $valu['taxDetailAutoID'];
                        $dataleg['taxFormulaMasterID'] = $val['taxCalculationformulaID'];
                        $dataleg['taxFormulaDetailID'] = $val['formulaDetailID'];
                        $dataleg['taxPercentage'] = $val['taxcalperce'];
                        $dataleg['ismanuallychanged'] = 0;
                        $dataleg['taxMasterID'] = $val['taxMasterAutoID'];
                        $dataleg['amount'] = $amounttx['amount'];
                        $dataleg['formula'] = $val['formula'];
                        $dataleg['companyCode'] = $CI->common_data['company_data']['company_code'];
                        $dataleg['companyID'] = $CI->common_data['company_data']['company_id'];
                        $dataleg['createdUserGroup'] = $CI->common_data['user_group'];
                        $dataleg['createdPCID'] = $CI->common_data['current_pc'];
                        $dataleg['createdUserID'] = $CI->common_data['current_userID'];
                        $dataleg['createdUserName'] = $CI->common_data['current_user'];
                        $dataleg['createdDateTime'] = $CI->common_data['current_date'];
                        $results = $CI->db->insert('srp_erp_taxledger', $dataleg);
                    }
                }
            }
        }
    }
}

if (!function_exists('tax_formulaBuilder_to_sql'))
{
    function tax_formulaBuilder_to_sql_vat($ssoRow, $salary_categories_arr, $amount, $discount, $taxpercentage, $taxCalculationformulaID, $documentID, $masterID = null, $documentDetailID = null, $isFromDD = 0, $taxFormulaDetailID = null, $taxLedgerAutoID = null, $fieldTpye = 0)
    {

        $type = $fieldTpye;
        $formula = (is_array($ssoRow)) ? trim($ssoRow['formulaString'] ?? '') : $ssoRow;
        $taxCategory = (is_array($ssoRow)) ? trim($ssoRow['taxCategory'] ?? '') : $ssoRow;

        $payGroupCategories = (is_array($ssoRow)) ? trim($ssoRow['payGroupCategories'] ?? '') : '';
        $taxCalculationformulaID = $taxCalculationformulaID;
        $formulaText = '';
        $salaryCatID = array();
        $formulaDecode_arr = array();
        $operand_arr = operand_arr();

        if ($isFromDD == 1 && $ssoRow['formulaDetailID'] == $taxFormulaDetailID && $type != 1)
        {

            $formula = str_replace($ssoRow['formulaString'], '_' . $amount . '_', $formula);
        }

        if (!empty($payGroupCategories))
        {
            global $globalFormula;
            $globalFormula = $formula;

            if ($isFromDD == 1)
            {
                $decode_data = decode_taxGroup_vat_DD($ssoRow, 0, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID, $taxLedgerAutoID, $fieldTpye);
            }
            else
            {
                $decode_data = decode_taxGroup_vat($ssoRow, 0, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID);
            }


            if (is_array($decode_data))
            {
                if ($decode_data[0] == 'e')
                {
                    //If maximum recursive exceeded than return will be a array else string
                    return $decode_data;
                }
            }
            $formula = $decode_data;
        }
        $formula_arr = explode('|', $formula); // break the formula

        $n = 0;

        foreach ($formula_arr as $formula_row)
        {

            if (trim($formula_row) != '')
            {
                if (in_array($formula_row, $operand_arr))
                { //validate is a operand
                    $formulaText .= ' ' . $formula_row . ' ';

                    $formulaDecode_arr[] = $formula_row;
                }
                else
                {

                    $elementType = $formula_row[0];


                    if ($elementType == '_')
                    {
                        /*** Number ***/
                        $numArr = explode('_', $formula_row);
                        $formulaText .= (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                        $formulaDecode_arr[] = (is_numeric($numArr[1])) ? $numArr[1] : $numArr[0];
                    }
                    else if ($elementType == '#')
                    {
                        /*** Salary category ***/


                        $catArr = explode('#', $formula_row);
                        $salaryCatID[$n]['ID'] = $catArr[1];
                        $salaryCatID[$n]['columnType'] = 'TAX_CAT';

                        $keys = array_keys(array_column($salary_categories_arr, 'taxMasterAutoID'), $catArr[1]);
                        $new_array = array_map(function ($k) use ($salary_categories_arr)
                        {
                            return $salary_categories_arr[$k];
                        }, $keys);

                        $salaryDescription = (!empty($new_array[0])) ? trim($new_array[0]['taxDescription']) : '';

                        $formulaText .= $salaryDescription;

                        $salaryDescription_arr = explode(' ', $salaryDescription);
                        $salaryDescription_arr = preg_replace("/[^a-zA-Z 0-9]+/", "", $salaryDescription_arr);
                        $salaryCatID[$n]['cat'] = implode('_', $salaryDescription_arr) . '_' . $n;
                        $formulaDecode_arr[] = 'SUM(' . $salaryCatID[$n]['cat'] . ')';
                    }
                    else if ($elementType == '!')
                    {
                        $monthlyADArr = explode('!', $formula_row);
                        if ($monthlyADArr[1] == 'AMT')
                        {
                            $formulaText .= 'Amount';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = $amount;
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = $MD_MD_Description;
                            $salaryCatID[$n]['columnType'] = 'AMT';
                        }
                        else if ($monthlyADArr[1] == 'DIS')
                        {
                            $formulaText .= 'Discount';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = $discount;
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = $MD_MD_Description;
                            $salaryCatID[$n]['columnType'] = 'DIS';
                        }
                        else
                        {
                            $formulaText .= 'Tax Percentage';
                            $MD_MD_Description = $monthlyADArr[1] . '_' . $n;

                            $formulaDecode_arr[] = ($taxpercentage / 100);
                            $salaryCatID[$n]['cat'] = $monthlyADArr[1];
                            $salaryCatID[$n]['description'] = $MD_MD_Description;
                            $salaryCatID[$n]['columnType'] = 'DIS';
                        }

                        /*** Monthly Addition or Monthly Deduction ***/
                    }

                    $n++;
                }
            }
        }

        $formulaDecode = implode(' ', $formulaDecode_arr);

        $select_salaryCat_str = '';
        $select_group_str = '';
        $select_monthlyAD_str = '';
        $whereInClause = '';
        $where_MA_MD_Clause = array();
        $whereInClause_group = '';
        $separator_salCat_count = 0;
        $separator_group_count = 0;
        $separator_monthlyAD_count = 0;


        foreach ($salaryCatID as $key1 => $row)
        {
            $separator_salCat = ($separator_salCat_count > 0) ? ',' : '';
            $separator_group = ($separator_group_count > 0) ? ',' : '';
            $separator_monthlyAD = ($separator_monthlyAD_count > 0) ? ',' : '';

            if ($row['columnType'] == 'TAX_CAT')
            {
                $select_salaryCat_str .= $separator_salCat . 'IF(salCatID=' . $row['ID'] . ', SUM(transactionAmount) , 0 ) AS ' . $row['cat'] . '';
                $whereInClause .= $separator_salCat . ' ' . $row['ID'];
                $separator_salCat_count++;
            }
            if ($row['columnType'] == 'AMT')
            {

                $select_monthlyAD_str .= $separator_monthlyAD . ' IF(calculationTB=\'' . $row['cat'] . '\', SUM(transactionAmount) , 0 ) AS ' . $row['description'] . '';

                //array_push($where_MA_MD_Clause, array($row['cat']=>$row['cat']));
                $where_MA_MD_Clause[] = $row['cat'];
                $separator_monthlyAD_count++;
            }
            if ($row['columnType'] == 'PER')
            {

                $select_monthlyAD_str .= $separator_monthlyAD . ' IF(calculationTB=\'' . $row['cat'] . '\', SUM(transactionAmount) , 0 ) AS ' . $row['description'] . '';

                //array_push($where_MA_MD_Clause, array($row['cat']=>$row['cat']));
                $where_MA_MD_Clause[] = $row['cat'];
                $separator_monthlyAD_count++;
            }
            if ($row['columnType'] == 'DIS')
            {

                $select_monthlyAD_str .= $separator_monthlyAD . ' IF(calculationTB=\'' . $row['cat'] . '\', SUM(transactionAmount) , 0 ) AS ' . $row['description'] . '';

                //array_push($where_MA_MD_Clause, array($row['cat']=>$row['cat']));
                $where_MA_MD_Clause[] = $row['cat'];
                $separator_monthlyAD_count++;
            }
        }

        $returnData = array(
            'formulaDecode' => $formulaDecode,
            'select_salaryCat_str' => $select_salaryCat_str,
            'select_group_str' => $select_group_str,
            'select_monthlyAD_str' => $select_monthlyAD_str,
            'whereInClause' => $whereInClause,
            'where_MA_MD_Clause' => $where_MA_MD_Clause,
            'whereInClause_group' => $whereInClause_group,
            'taxCategory' => $taxCategory,

        );
        //echo '<pre>'; print_r($returnData);
        return $returnData;
    }
}

if (!function_exists('existTaxPolicyDocumentWise'))
{
    function existTaxPolicyDocumentWise($tblName, $masterID, $documentID, $masterColName)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $query = $CI->db->query("SELECT
                              isGroupBasedTax
                              FROM $tblName
                              where companyID = $companyID 
                              AND $masterColName = $masterID")->row('isGroupBasedTax');

        return $query;
    }
}
if (!function_exists('all_tax_formula_drop_groupByTax'))
{
    function all_tax_formula_drop_groupByTax($taxtype = 2)
    {
        $CI = &get_instance();
        $CI->db->SELECT("taxCalculationformulaID,Description");
        $CI->db->FROM('srp_erp_taxcalculationformulamaster');
        //$CI->db->where('isClaimable', 0);
        $CI->db->where('taxType', $taxtype);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Tax Formula');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['taxCalculationformulaID'] ?? '')] = trim($row['Description'] ?? '');
            }
        }


        return $data_arr;
    }
}

if (!function_exists('fetch_line_wise_itemTaxFormulaID'))
{
    function fetch_line_wise_itemTaxFormulaID($itemAutoID, $col1, $col2, $taxtype = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $where = '';
        $assignedTaxFormula = 0;
        if ($taxtype != 0)
        {
            $where = ' AND srp_erp_taxcalculationformulamaster.taxType = ' . $taxtype . '';
        }

        if ($itemAutoID)
        {
            $where_item_tax = ' AND taxType = 1';
            if ($taxtype != 0)
            {
                $where_item_tax = ' AND taxType = ' . $taxtype . '';
            }
            $assignedTaxFormula = $CI->db->query("SELECT
	                                              IFNULL( taxFormulaID, 0 ) AS taxFormulaID 
                                                  FROM
	                                              `srp_erp_itemtaxformula` 
                                                  WHERE
	                                              ItemAutoID = {$itemAutoID} 
	                                              {$where_item_tax}
                                                  ORDER BY
	                                              itemTaxformulaID DESC 
	                                              LIMIT 1")->row('taxFormulaID');
        }
        if ($assignedTaxFormula == null)
        {
            $assignedTaxFormula = 0;
        }

        $query = $CI->db->query("SELECT
                                taxCalculationformulaID as $col1,
                                Description as $col2,
                                $assignedTaxFormula as assignedItemTaxFormula
                                FROM 
                                srp_erp_taxcalculationformulamaster Where companyID = $companyID $where")->result_array();
        return $query;
    }
}

if (!function_exists('fetch_line_wise_itemTaxcalculation'))
{
    function fetch_line_wise_itemTaxcalculation($taxFormulaMasterID, $taxApplicapleAmt, $discountAmount, $documentID, $masterID, $detailID = null, $isRcmDocument = 0)
    {
        $amount = array();
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select("*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories,IFNULL(ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage)  as  taxPercentagedetail");
        $CI->db->join("(SELECT  
                       taxFormulaDetailID,
                       taxPercentage
                       FROM
                       `srp_erp_taxledger` 
                       WHERE
                       documentID = '{$documentID}'
                       AND documentMasterAutoID = $masterID
                       AND documentDetailAutoID = '$detailID') ledger", "ledger.taxFormulaDetailID = srp_erp_taxcalculationformuladetails.formulaDetailID", "LEFT");
        $CI->db->join("srp_erp_taxmaster", "srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID", "LEFT");
        $CI->db->where('taxCalculationformulaID', $taxFormulaMasterID);

        $formulaDtl = $CI->db->get("srp_erp_taxcalculationformuladetails")->result_array();
        if (!empty($formulaDtl))
        {
            $amountTax = 0;
            foreach ($formulaDtl as $val)
            {
                $sortOrder = $val['sortOrder'];
                $tax_categories = $CI->db->query("SELECT 
                                                    srp_erp_taxcalculationformuladetails.*,
                                                    srp_erp_taxmaster.taxDescription,
                                                    srp_erp_taxmaster.taxPercentage,
                                                    srp_erp_taxmaster.taxCategory as taxCategory
                                                    FROM
                                                    srp_erp_taxcalculationformuladetails
                                                    LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                    WHERE
                                                    taxCalculationformulaID = $taxFormulaMasterID
                                                    AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();


                $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories, $taxApplicapleAmt, $discountAmount, $val['taxPercentagedetail'], $taxFormulaMasterID, $documentID, $masterID, $detailID);

                $formulaDecodeval = $formulaBuilder['formulaDecode'];
                if (!empty($formulaDecodeval))
                {
                    if ($isRcmDocument == 1)
                    {
                        if ($formulaBuilder['taxCategory'] != 2)
                        {
                            if (! $CI->db->simple_query("SELECT $formulaDecodeval as amount"))
                            {
                                $amount['error'] = 1;
                                $amount['amount'] = 0;
                                return $amount;
                            }
                            else
                            {
                                $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                            }
                        }
                        else
                        {
                            $amounttx = 0;
                        }
                    }
                    else
                    {
                        if (! $CI->db->simple_query("SELECT $formulaDecodeval as amount"))
                        {
                            $amount['error'] = 1;
                            $amount['amount'] = 0;
                            return $amount;
                        }
                        else
                        {
                            $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                        }
                    }
                }
                else
                {
                    $amounttx = 0;
                }



                $amountTax += $amounttx;
            }
            $amount['error'] = 0;
            $amount['amount'] = (float)$amountTax;
            return $amount;
        }

        $amount['error'] = 0;
        $amount['amount'] = 0;
        return $amount;
    }
}



if (!function_exists('fetch_line_wise_itemTaxcalculation_gpos'))
{
    function fetch_line_wise_itemTaxcalculation_gpos($taxFormulaMasterID, $taxApplicapleAmt, $discountAmount, $documentID, $detailID = null, $isRcmDocument = 0)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $CI->db->select("*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories, srp_erp_taxcalculationformuladetails.taxPercentage as  taxPercentagedetail");
        $CI->db->join("srp_erp_taxmaster", "srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID", "LEFT");
        $CI->db->where('taxCalculationformulaID', $taxFormulaMasterID);
        $formulaDtl = $CI->db->get("srp_erp_taxcalculationformuladetails")->result_array();
        if (!empty($formulaDtl))
        {
            $amountTax = 0;
            foreach ($formulaDtl as $val)
            {
                $sortOrder = $val['sortOrder'];
                $tax_categories = $CI->db->query("SELECT 
                                                    srp_erp_taxcalculationformuladetails.*,
                                                    srp_erp_taxmaster.taxDescription,
                                                    srp_erp_taxmaster.taxPercentage,
                                                    srp_erp_taxmaster.taxCategory as taxCategory
                                                    FROM
                                                    srp_erp_taxcalculationformuladetails
                                                    LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                    WHERE
                                                    taxCalculationformulaID = $taxFormulaMasterID
                                                    AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder  ")->result_array();


                $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories, $taxApplicapleAmt, $discountAmount, $val['taxPercentagedetail'], $taxFormulaMasterID, $documentID, null, $detailID);

                $formulaDecodeval = $formulaBuilder['formulaDecode'];

                if (!empty($formulaDecodeval))
                {
                    if ($isRcmDocument == 1)
                    {
                        if ($formulaBuilder['taxCategory'] != 2)
                        {
                            $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                        }
                        else
                        {
                            $amounttx = 0;
                        }
                    }
                    else
                    {
                        $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount ")->row('amount');
                    }
                }
                else
                {
                    $amounttx = 0;
                }



                $amountTax += $amounttx;
            }
            return (float)$amountTax;
        }


        return 0;
    }
}



if (!function_exists('fetchExistsDetailTBL'))
{
    function fetchExistsDetailTBL($documentID, $documentMasterAutoID, $documentDetailAutoID, $taxDetailTBL = null, $isTaxEmpty = 0, $transactionAmount = 0)
    {
        $CI = &get_instance();
        if (!empty($documentDetailAutoID))
        {
            $detail_filter = ' AND documentDetailAutoID = ' . $documentDetailAutoID;
        }
        else
        {
            $detail_filter = ' AND documentDetailAutoID IS NULL';
        }

        $query = $CI->db->query("SELECT
                                 taxDetailAutoID,
                                 taxLedgerAutoID
                                 FROM 
                                 `srp_erp_taxledger` 
                                 where
                                 documentID = '{$documentID}'
		                         AND documentMasterAutoID = $documentMasterAutoID $detail_filter")->row_array();


        if (!empty($query))
        {

            $CI->db->query("DELETE FROM srp_erp_taxledger WHERE documentID = '{$documentID}' AND documentMasterAutoID = $documentMasterAutoID $detail_filter");
            if ($taxDetailTBL != '')
            {
                $CI->db->query("DELETE FROM $taxDetailTBL WHERE taxDetailAutoID = '{$query['taxDetailAutoID']}'");
            }
        }

        if ($isTaxEmpty == 1)
        {
            switch ($documentID)
            {
                case 'BSI':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $CI->db->where('InvoiceDetailAutoID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_paysupplierinvoicedetail', $data_detailTBL);
                    break;
                case 'PO':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $CI->db->where('purchaseOrderDetailsID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_purchaseorderdetails', $data_detailTBL);
                    break;
                case 'CINV':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $data_detailTBL['transactionAmount'] = $transactionAmount;
                    $CI->db->where('invoiceDetailsAutoID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_customerinvoicedetails', $data_detailTBL);
                    break;
                case 'CNT':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $CI->db->where('contractDetailsAutoID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_contractdetails', $data_detailTBL);
                    break;
                case 'DO':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $data_detailTBL['transactionAmount'] = $transactionAmount;
                    $data_detailTBL['deliveredTransactionAmount'] = $transactionAmount;
                    $CI->db->where('DODetailsAutoID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_deliveryorderdetails', $data_detailTBL);
                    break;
                case 'CN':
                    $data_detailTBL['taxCalculationformulaID'] = null;
                    $data_detailTBL['taxAmount'] = 0;
                    $data_detailTBL['transactionAmount'] = $transactionAmount;
                    $CI->db->where('creditNoteDetailsID', $documentDetailAutoID);
                    $CI->db->update('srp_erp_creditnotedetail', $data_detailTBL);
                    break;
            }
        }
    }
}

if (!function_exists('update_group_based_tax'))
{
    function update_group_based_tax($masterTable, $masterIDColumn, $masterID, $taxTable = null, $masterIDColDefault = null, $documentID = NULL)
    {

        $CI = &get_instance();
        $group_based_tax = is_null(getPolicyValues('GBT', 'All')) ? 0 : getPolicyValues('GBT', 'All');

        $masterIDColumnCal = ($masterIDColDefault != '' ? $masterIDColDefault : $masterIDColumn);
        $CI->db->select('isGroupBasedTax');
        $CI->db->where($masterIDColumnCal, $masterID);
        $isGroupBasedTax = $CI->db->get($masterTable)->row_array();

        /* switch ($documentID) {
            case 'GRV':
            case 'CINV':
            case 'PO-PRQ': 
                $taxAdded = $CI->db->query("SELECT
                                            taxLedgerAutoID
                                            FROM
                                            `srp_erp_taxledger`
                                            where 
                                            documentID = '$documentID'
                                            AND documentMasterAutoID = $masterID")->row_array();
                break;
            default:
                if ($isGroupBasedTax != $group_based_tax) {
                    $CI->db->select('taxsDetailAutoID');
                    $CI->db->where($masterIDColumn, $masterID);
                    $taxAdded = $CI->db->get($taxTable)->row_array();
                }
        } */

        /* if ($isGroupBasedTax != $group_based_tax) {
            $CI->db->select('taxsDetailAutoID');
            $CI->db->where($masterIDColumn, $masterID);
            $taxAdded = $CI->db->get($taxTable)->row_array();
        } */
        $taxAdded = $CI->db->query("SELECT
        taxLedgerAutoID
        FROM
        `srp_erp_taxledger`
        where 
        documentID = '$documentID'
        AND documentMasterAutoID = $masterID")->row_array();

        switch ($documentID)
        {
            case 'GRV':
                $detailTBL = 'srp_erp_grvdetails';
                $colName = 'grvAutoID';
                break;

            case 'PO':
            case 'PO-PRQ':
                $detailTBL = 'srp_erp_purchaseorderdetails';
                $colName = 'purchaseOrderID';
                break;

            case 'PV':
                $detailTBL = 'srp_erp_paymentvoucherdetail';
                $colName = 'payVoucherAutoId';
                break;

            case 'RV':
                $detailTBL = 'srp_erp_customerreceiptdetail';
                $colName = 'receiptVoucherAutoId';
                break;
            case 'CINV':
                $detailTBL = 'srp_erp_customerinvoicedetails';
                $colName = 'invoiceAutoID';
                break;
            case 'DO':
                $detailTBL = 'srp_erp_deliveryorderdetails';
                $colName = 'DOAutoID';
                break;
            case 'CNT':
                $detailTBL = 'srp_erp_contractdetails';
                $colName = 'contractAutoID';
                break;
            case 'BSI':
                $detailTBL = 'srp_erp_paysupplierinvoicedetail';
                $colName = 'InvoiceAutoID';
                break;
            case 'CN':
                $detailTBL = 'srp_erp_creditnotedetail';
                $colName = 'creditNoteMasterAutoID';
                break;
            case 'DN':
                $detailTBL = 'srp_erp_debitnotedetail';
                $colName = 'debitNoteMasterAutoID';
                break;
            case 'SLR':
                $detailTBL = 'srp_erp_salesreturndetails';
                $colName = 'salesReturnDetailsID';
                break;
            default:
                return -1;
        }

        $detailTBLExist = $CI->db->query("SELECT
                                          COUNT(*) as docCount 
                                          FROM
                                          $detailTBL
                                          where 
                                          $colName = $masterID")->row('docCount');


        if (empty($taxAdded) && $detailTBLExist == 0)
        {
            $CI->db->query("UPDATE
                            $masterTable
                            SET isGroupBasedTax = $group_based_tax
                            WHERE $masterIDColumnCal = $masterID ");
        }
    }
}

$globalFormula = '';
if (!function_exists('decode_taxGroup_vat'))
{
    function decode_taxGroup_vat($formulaData, $decode_taxGroup_count, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID)
    {

        $CI = &get_instance();
        $companyID = current_companyID();
        $payGroupCategories = $formulaData['payGroupCategories'];
        global $globalFormula;
        $decode_taxGroup_count++;

        if ($decode_taxGroup_count > 1000)
        {
            //If the recursive worked more than 200 times than terminate the function
            return ['e', 'Decode tax group function got terminated.<br/>'];
        }

        if ($masterID != '')
        {
            $result = $CI->db->query("SELECT masterTB.taxMasterAutoID, formula AS formulaString, taxMasters AS payGroupCategories,IFNULL(ledger.taxPercentage, formula.taxPercentage)  as taxPercentage FROM srp_erp_taxmaster AS masterTB
                                  JOIN srp_erp_taxcalculationformuladetails AS formula ON formula.taxMasterAutoID=masterTB.taxMasterAutoID AND taxCalculationformulaID=$taxCalculationformulaID
                                  LEFT JOIN (SELECT  
                                             taxFormulaDetailID,
	                                         taxPercentage
                                             FROM
	                                         `srp_erp_taxledger` 
                                             WHERE
                                             documentID = '{$documentID}'
                                             AND documentMasterAutoID = $masterID
                                             AND documentDetailAutoID = '$documentDetailID' ) ledger on ledger.taxFormulaDetailID = formula.formulaDetailID
                                            WHERE masterTB.companyID = {$companyID} AND masterTB.taxMasterAutoID IN ($payGroupCategories)")->result_array();
        }
        else
        {
            $result = $CI->db->query("SELECT masterTB.taxMasterAutoID, formula AS formulaString, taxMasters AS payGroupCategories, formula.taxPercentage as taxPercentage FROM srp_erp_taxmaster AS masterTB
                                  JOIN srp_erp_taxcalculationformuladetails AS formula ON formula.taxMasterAutoID=masterTB.taxMasterAutoID AND taxCalculationformulaID=$taxCalculationformulaID
                                  WHERE masterTB.companyID = {$companyID} AND masterTB.taxMasterAutoID IN ($payGroupCategories)")->result_array();
        }

        foreach ($result as $row)
        {
            $searchVal = '#' . $row['taxMasterAutoID'];

            $replaceVal = '|(|' . $row['formulaString'] . '|)|';

            if (!empty($row['payGroupCategories']))
            {
                $replaceVal = str_replace('!PER', '_' . ($row['taxPercentage'] / 100) . '_', $replaceVal);
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $return = decode_taxGroup_vat($row, $decode_taxGroup_count, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID);
                if (is_array($return))
                {
                    if ($return[0] == 'e')
                    {
                        return $return;
                        break;
                    }
                }
            }
            else
            {

                $replaceVal = str_replace('!PER', '_' . ($row['taxPercentage'] / 100) . '_', $replaceVal);
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $payGroupCategories = null;
            }
        }

        return $globalFormula;
    }
}


$globalFormulaDD = '';
if (!function_exists('decode_taxGroup_vat_DD'))
{
    function decode_taxGroup_vat_DD($formulaData, $decode_taxGroup_count, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID, $taxLedgerAutoID, $fieldtype)
    {

        $CI = &get_instance();
        $companyID = current_companyID();
        $payGroupCategories = $formulaData['payGroupCategories'];
        $taxCalculationformulaID = $taxCalculationformulaID;

        global $globalFormula;
        $decode_taxGroup_count++;

        if ($decode_taxGroup_count > 1000)
        {
            //If the recursive worked more than 200 times than terminate the function
            return ['e', 'Decode tax group function got terminated.<br/>'];
        }

        $result = $CI->db->query("SELECT masterTB.taxMasterAutoID, formula AS formulaString, taxMasters AS payGroupCategories,IFNULL(ledger.taxPercentage, formula.taxPercentage)  as taxPercentage,ledger.amount as amountcal FROM srp_erp_taxmaster AS masterTB
                                  JOIN srp_erp_taxcalculationformuladetails AS formula ON formula.taxMasterAutoID=masterTB.taxMasterAutoID AND taxCalculationformulaID=$taxCalculationformulaID
                                  LEFT JOIN (SELECT  
                                             taxFormulaDetailID,
	                                         taxPercentage,
                                             amount
                                             FROM
	                                         `srp_erp_taxledger` 
                                             WHERE
                                             documentID = '{$documentID}'
                                             AND documentMasterAutoID = $masterID
                                             AND documentDetailAutoID = '$documentDetailID' ) ledger on ledger.taxFormulaDetailID = formula.formulaDetailID
                                            WHERE masterTB.companyID = {$companyID} AND masterTB.taxMasterAutoID IN ($payGroupCategories)")->result_array();
        // echo '<pre>';print_r($result); echo '</pre>';

        foreach ($result as $row)
        {

            if ($fieldtype != 2)
            {
                $searchVal = '#' . $row['taxMasterAutoID'];
                $replaceVal = '|(|' . $row['formulaString'] . '|)|';
            }
            else
            {
                $updateValue = $CI->db->query("SELECT taxMasterAutoID,amount FROM `srp_erp_taxledger`
                                           LEFT JOIN srp_erp_taxcalculationformuladetails ON srp_erp_taxcalculationformuladetails.formulaDetailID = srp_erp_taxledger.taxFormulaDetailID
                                           where
                                           taxLedgerAutoID = $taxLedgerAutoID")->row_array();

                $searchVal = '#' . $row['taxMasterAutoID'];
                if ($searchVal == '#' . $updateValue['taxMasterAutoID'])
                {
                    $replaceVal = '_' . $updateValue['amount'] . '_';
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                }
                $replaceVal = '|(|' . $row['formulaString'] . '|)|';
            }





            if (!empty($row['payGroupCategories']))
            {

                $replaceVal = str_replace('!PER', '_' . ($row['taxPercentage'] / 100) . '_', $replaceVal);
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $return = decode_taxGroup_vat_DD($row, $decode_taxGroup_count, $taxCalculationformulaID, $documentID, $masterID, $documentDetailID);
                if (is_array($return))
                {
                    if ($return[0] == 'e')
                    {
                        return $return;
                        break;
                    }
                }
            }
            else
            {

                $replaceVal = str_replace('!PER', '_' . ($row['taxPercentage'] / 100) . '_', $replaceVal);
                $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                $payGroupCategories = null;
            }
        }

        return $globalFormula;
    }
}


if (!function_exists('update_tax_calculation_DD'))
{
    function update_tax_calculation_DD($taxApplicapleAmt, $discountAmount, $taxPercentage, $taxLedgerAutoID, $taxFormulaMasterID, $taxFormulaDetailID, $documentID, $masterID, $documentDetailID, $updateDD, $FieldToUpdate)
    {

        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];
        $tax_val_arr = array();


        $CI->db->select('*,srp_erp_taxcalculationformuladetails.formula as formulaString,srp_erp_taxcalculationformuladetails.taxMasters AS  payGroupCategories,IFNULL(ledger.taxPercentage, srp_erp_taxcalculationformuladetails.taxPercentage)  as taxPercentagedetail,ledger.formula as formulaLedger,ledger.amount as amountcal,
        ledger.taxPercentage AS taxPercentagecal');
        $CI->db->where('taxCalculationformulaID', $taxFormulaMasterID);
        $CI->db->join("(SELECT  
        taxFormulaDetailID,
        taxPercentage,
        formula,
        amount
        FROM
        `srp_erp_taxledger` 
        WHERE
        documentID = '{$documentID}'
        AND documentMasterAutoID = $masterID
        AND documentDetailAutoID = '$documentDetailID') ledger", "ledger.taxFormulaDetailID = srp_erp_taxcalculationformuladetails.formulaDetailID", "LEFT");
        $CI->db->join('srp_erp_taxmaster', 'srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID', 'LEFT');
        $formulaDtl = $CI->db->get('srp_erp_taxcalculationformuladetails')->result_array();
        if (!empty($formulaDtl))
        {
            $amountTax = 0;
            foreach ($formulaDtl as $val)
            {
                $sortOrder = $val['sortOrder'];
                $tax_categories = $CI->db->query("SELECT
                                                    srp_erp_taxcalculationformuladetails.*,
                                                    srp_erp_taxmaster.taxDescription,
                                                    srp_erp_taxmaster.taxPercentage,
                                                    srp_erp_taxmaster.taxCategory as taxCategory
                                                    FROM
                                                    srp_erp_taxcalculationformuladetails
                                                    LEFT JOIN srp_erp_taxmaster on srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxcalculationformuladetails.taxMasterAutoID
                                                    WHERE
                                                    taxCalculationformulaID = $taxFormulaMasterID
                                                    AND srp_erp_taxcalculationformuladetails.companyID = $companyID AND sortOrder < $sortOrder")->result_array();


                $formulaBuilder = tax_formulaBuilder_to_sql_vat($val, $tax_categories, (($FieldToUpdate == 2 && $val['taxFormulaDetailID'] == $taxFormulaDetailID) ? ($val['amountcal']) : $taxApplicapleAmt), $discountAmount, (($FieldToUpdate == 2 && $val['taxFormulaDetailID'] == $taxFormulaDetailID) ? ($taxPercentage) : ($val['taxPercentagecal'])), $taxFormulaMasterID, $documentID, $masterID, $documentDetailID, $updateDD, $taxFormulaDetailID, $taxLedgerAutoID, $FieldToUpdate);
                $formulaDecodeval = $formulaBuilder['formulaDecode'];

                $amounttx = $CI->db->query("SELECT $formulaDecodeval as amount")->row('amount');
                $data['taxCalculationFormulaDetailID'] = $val['formulaDetailID'];
                $data['amount'] = $amounttx;
                $data['formulaDecodeval'] = $formulaDecodeval;
                $data['formula'] = $val['formulaDetailID'];
                array_push($tax_val_arr, $data);
            }

            //exit;
            return $tax_val_arr;
        }

        return 0;
    }
}


if (!function_exists('get_all_documentcodes'))
{
    function get_all_documentcodes()/*Load all document codes*/
    {
        $CI = &get_instance();
        $CI->db->select("contractAutoID,contractCode");
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->where('companyID', current_companyID());
        $documentcode_arr = array();
        $documentcode = $CI->db->get()->result_array();
        if (isset($documentcode))
        {
            foreach ($documentcode as $row)
            {
                $documentcode_arr[trim($row['contractAutoID'] ?? '')] = trim($row['contractCode'] ?? '');
            }
        }
        return $documentcode_arr;
    }
}


if (!function_exists('all_customer_drop_isactive_inactive'))
{
    function all_customer_drop_isactive_inactive($pID, $docID)
    {
        $ci = &get_instance();
        $companyID = current_companyID();
        $masterID = $pID;
        $output = array();
        switch ($docID)
        {
            case 'CNT':
            case 'SO':
            case 'QUT':
                $tableName = 'srp_erp_contractmaster';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry,srp_erp_contractmaster.customerOrderID';
                $documentsyscode = 'contractCode';
                $companyColumn = 'srp_erp_contractmaster.companyID';
                $documentid = 'contractAutoID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_contractmaster.customerID';
                $wherecondition = '';
                break;
            case 'CINV':
                $tableName = 'srp_erp_customerinvoicemaster';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry';
                $documentsyscode = 'invoiceCode';
                $companyColumn = 'srp_erp_customerinvoicemaster.companyID';
                $documentid = 'invoiceAutoID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_customerinvoicemaster.customerID';
                $wherecondition = '';
                break;
            case 'SLR':
                $tableName = 'srp_erp_salesreturnmaster';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry';
                $documentsyscode = 'salesReturnCode';
                $companyColumn = 'srp_erp_salesreturnmaster.companyID';
                $documentid = 'salesReturnAutoID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_salesreturnmaster.customerID';
                $wherecondition = '';
                break;
            case 'CN':
                $tableName = 'srp_erp_creditnotemaster';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry';
                $documentsyscode = 'creditNoteCode';
                $companyColumn = 'srp_erp_creditnotemaster.companyID';
                $documentid = 'creditNoteMasterAutoID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_creditnotemaster.customerID';
                $wherecondition = '';
                break;
            case 'RVM':
                $tableName = 'srp_erp_rvadvancematch';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry';
                $documentsyscode = 'matchSystemCode';
                $companyColumn = 'srp_erp_rvadvancematch.companyID';
                $documentid = 'matchID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_rvadvancematch.customerID';
                $wherecondition = '';
                break;
            case 'DO':
                $tableName = 'srp_erp_deliveryorder';
                $masterColumn = 'customerID AS customerID,cus.isActive,cus.customerAutoID,cus.customerName,cus.customerSystemCode,cus.customerCountry';
                $documentsyscode = 'DOCode';
                $companyColumn = 'srp_erp_deliveryorder.companyID';
                $documentid = 'DOAutoID';
                $jointbl = 'srp_erp_customermaster cus';
                $condition = 'cus.customerAutoID = srp_erp_deliveryorder.customerID';
                $wherecondition = '';
                break;

            default:
                return $output;
        }


        $ci->db->select("{$documentsyscode} AS documentsystemcode, {$masterColumn}");
        $ci->db->from("{$tableName}");
        $ci->db->join("{$jointbl}", "{$condition}");
        $ci->db->where("{$documentid}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);
        $wherecondition;

        $output = $ci->db->get()->row_array();
        return $output;
    }
}

if (!function_exists('fetch_rcmDetails'))
{
    function fetch_rcmDetails($supplierAutoID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $companyDetails = $CI->db->query("SELECT
	                                        IFNULL(isVatEligible,0) as isVatEligible,
	                                        countryID
                                            FROM
                                            `srp_erp_company`
                                            where
                                            company_id = $companyID ")->row_array();

        $supplierDetails = $CI->db->query("select
                                             supplierAutoID,
                                             supplierName,
                                             srp_erp_countrymaster.countryID,
                                             IFNULL(vatEligible,0) as vatEligible,
                                             IFNULL(locationType,0)  as locationType
                                             from
                                             srp_erp_suppliermaster
                                             LEFT JOIN srp_erp_countrymaster ON srp_erp_countrymaster.CountryDes = srp_erp_suppliermaster.supplierCountry
                                             LEFT JOIN srp_erp_location ON srp_erp_location.locationID = srp_erp_suppliermaster.supplierLocationID
                                             where
                                             srp_erp_suppliermaster.companyID = $companyID
                                             AND supplierAutoID = $supplierAutoID")->row_array();


        $data = 0;
        if (($companyDetails['isVatEligible'] == 1) && (($supplierDetails['countryID'] != $companyDetails['countryID']) || $supplierDetails['locationType'] == 1))
        {
            $data = 1;
        }
        return $data;
    }
}
if (!function_exists('isRcmApplicable'))
{
    function isRcmApplicable($masterTBL, $masterColname, $masterID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $isRcmApplicable = $CI->db->query("SELECT
	                                       rcmApplicableYN 
                                           FROM
                                            $masterTBL
                                           where 
                                           companyID = $companyID 
                                           AND $masterColname = $masterID")->row('rcmApplicableYN');


        return $isRcmApplicable;
    }
}

if (!function_exists('vat_type_dropdown'))
{
    function vat_type_dropdown($status = TRUE)
    {
        $CI = &get_instance();
        $vatDetails = $CI->db->query("SELECT vatTypeID, Description FROM srp_erp_tax_vat_type")->result_array();

        if ($status)
        {
            $data_arr = array('' => 'Select VAT Type');
        }
        foreach ($vatDetails as $vatDet)
        {
            $data_arr[trim($vatDet['vatTypeID'] ?? '')] = trim($vatDet['Description'] ?? '');
        }

        return $data_arr;
    }
}
if (!function_exists('fetch_current_time_by_timezone'))
{
    function fetch_current_time_by_timezone($companyID)
    {
        $CI = &get_instance();
        $company_id = $companyID;

        $timeZone = $CI->db->query("SELECT description FROM srp_erp_timezonedetail JOIN srp_erp_company ON srp_erp_company.defaultTimezoneID = srp_erp_timezonedetail.detailID WHERE company_id = $company_id")->row('description');

        if ($timeZone)
        {
            $date = new DateTime('now', new DateTimeZone($timeZone));
            $currentTime =  date_format($date, 'Y-m-d H:i:s');
        }
        else
        {
            $currentTime = date('Y-m-d H:i:s');
        }
        return $currentTime;
    }
}

if (!function_exists('load_controlaccount_status'))
{
    function load_controlaccount_status($autoID, $GLAutoID, $controllAccountYN)
    {
        if (!empty($GLAutoID))
        {
            $checked = ($controllAccountYN == 0) ? 'checked' : '';

            return '<input type="checkbox" class="switch-chk btn-sm" id="status_' . $autoID . '" onChange="changeControlAccoutnStatus(this,' . $autoID . ', \'' . $GLAutoID . '\')"
                    data-size="mini" data-on-text="OPEN" data-handle-width="40" data-off-color="danger" data-on-color="success"
                    data-off-text="CLOSE" data-label-width="0" ' . $checked . ' >';
        }
        else
        {
            return '-';
        }
    }
}
if (!function_exists('controlAcountLogStatus'))
{
    function controlAcountLogStatus($con)
    {
        $status = '<center>';
        if ($con == 0)
        {
            $status .= '<span class="label label-danger">Closed</span>';
        }
        elseif ($con == 1)
        {
            $status .= '<span class="label label-success">Open</span>';
        }
        else
        {
            $status .= '-';
        }
        $status .= '</center>';

        return $status;
    }

    if (!function_exists('all_control_account_drop'))
    {
        function all_control_account_drop($status = TRUE)
        {
            $CI = &get_instance();
            $CI->db->select("controlAccountsAutoID,controlAccountType,controlAccountDescription");
            $CI->db->from('srp_erp_companycontrolaccounts ');
            $CI->db->where('companyID', current_companyID());

            //$CI->db->where('isApprovalDocument', 1);
            $data = $CI->db->get()->result_array();
            if ($status)
            {
                $data_arr = array('' => 'Select Control Account');
            }
            else
            {
                $data_arr = [];
            }
            if (isset($data))
            {
                foreach ($data as $row)
                {
                    $data_arr[trim($row['controlAccountsAutoID'] ?? '')] = trim($row['controlAccountType'] ?? '') . ' | ' . trim($row['controlAccountDescription'] ?? '');
                }
            }

            return $data_arr;
        }
    }
}

if (!function_exists('all_chart_control_account_drop'))
{
    function all_chart_control_account_drop($status = TRUE, $accountType = null)
    {
        $CI = &get_instance();
        $CI->db->select("chart_accounts.*,control_accounts.controlAccountDescription,control_accounts.controlAccountType");
        $CI->db->from('srp_erp_chartofaccounts as chart_accounts');
        $CI->db->join('srp_erp_companycontrolaccounts as control_accounts', 'chart_accounts.GLAutoID = control_accounts.GLAutoID', 'left');
        $CI->db->where('chart_accounts.companyID', current_companyID());
        if ($accountType)
        {
            $CI->db->where('control_accounts.controlAccountType', $accountType);
        }

        $data = $CI->db->get()->result_array();


        if ($status)
        {
            $data_arr = array('' => 'Select Control Account');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['controlAccountType'] ?? '') . ' | ' . trim($row['controlAccountDescription'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('load_cusomer_master_detail'))
{
    function load_cusomer_master_detail($customerName, $customerAddress1, $customerAddress2, $customerCountry, $secondaryCode, $customerCurrency, $customerEmail, $customerTelephone, $IdCardNumber, $customerCreditPeriod, $rebatePercentage, $customerCreditLimit, $vatEligible, $partyCurrencyDecimalPlaces, $vatIdNo)
    {
        if ($vatEligible == '1')
        {
            $vatYN = 'NO';
        }
        elseif ($vatEligible == '2')
        {
            $vatYN = 'YES';
        }
        else
        {
            $vatYN = '-';
        }
        $status = '';
        $status = '<span class="text-left">';
        $status .= '<b>Name : </b> ' . $customerName . ' &nbsp;&nbsp;&nbsp;<b>Secondary Code : </b> ' . $secondaryCode . ' <br><b >Address : </b> ' . $customerAddress1 . '&nbsp;&nbsp;' . $customerAddress2 . '&nbsp;&nbsp;' . $customerCountry . '<br><b> Email </b> ' . $customerEmail . '&nbsp;&nbsp;&nbsp;<b>Telephone</b>' . $customerTelephone . '<br><b>Id Card No :</b>' . $IdCardNumber . '<br><b>Credit Period : </b>' . $customerCreditPeriod . '&nbsp;Months&nbsp;&nbsp;<b>Credit Limit : </b>' . $customerCurrency . '&nbsp' . number_format($customerCreditLimit, $partyCurrencyDecimalPlaces ?? 0) . '<br><b>VAT Eligible  : </b>' . $vatYN . '&nbsp;&nbsp;&nbsp;<b>VAT Identification No : </b>' . $vatIdNo . '<br><b>Rebate Percentage : </b>' . $rebatePercentage;
        $status .= '</span>';

        return $status;
    }
}
if (!function_exists('fetch_tax_details'))
{
    function fetch_tax_details($documentID, $documentMasterAutoID, $isRcmDocument = 0)
    {
        $companyID = current_companyID();
        $CI = &get_instance();
        $whereTaxCat = '';
        if ($isRcmDocument == 1)
        {
            $whereTaxCat = ' AND taxCategory!=2';
        }
        if ($documentID == 'DOCINV')
        {
            $DOMasterIDs = $CI->db->query("SELECT DISTINCT DOMasterID FROM srp_erp_customerinvoicedetails WHERE invoiceAutoID = 2908 AND DOMasterID IS NOT NULL")->result_array();

            if ($DOMasterIDs)
            {
                $DOMasterID = implode(',', array_column($DOMasterIDs, 'DOMasterID'));

                $data = $CI->db->query("SELECT
                                        srp_erp_taxmaster.taxShortCode,
                                        srp_erp_taxmaster.taxDescription,
                                        SUM(amount) as taxAmount,
                                        srp_erp_taxledger.taxPercentage,
                                        srp_erp_taxmaster.taxCategory
                                    FROM
	                                    `srp_erp_taxledger`
	                                JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                                WHERE 
                                        srp_erp_taxledger.companyID = {$companyID} 
                                        AND documentID IN ('DO')
                                        AND documentMasterAutoID IN ({$DOMasterID})
                                        $whereTaxCat
	                                GROUP BY 
	                                    taxMasterID")->result_array();

                return $data;
            }
            else
            {
                return array();
            }
        }

        $data = $CI->db->query("SELECT
                                    srp_erp_taxmaster.taxShortCode,
                                    srp_erp_taxmaster.taxDescription,
	                                SUM(amount) as taxAmount,
	                                srp_erp_taxledger.taxPercentage,
                                    srp_erp_taxmaster.taxCategory
                                    FROM
	                                `srp_erp_taxledger`
	                                JOIN srp_erp_taxmaster ON srp_erp_taxmaster.taxMasterAutoID = srp_erp_taxledger.taxMasterID
	                                WHERE 
	                                srp_erp_taxledger.companyID = {$companyID} 
	                                AND documentID IN ('{$documentID}')
	                                AND documentMasterAutoID = {$documentMasterAutoID}
                                    $whereTaxCat
	                                GROUP BY 
	                                taxMasterID")->result_array();

        return $data;
    }
}
if (!function_exists('load_warehouse_status'))
{
    function load_warehouse_status($isActive)
    {
        $status = '<center>';

        if (!empty($isActive) && $isActive == 1)
        {
            $status .= '<span style="background: #03a84e;font-family: inherit;" class="badge">Active</span>';
        }
        else
        {
            $status .= '<span style="background: #b92e1d;font-family: inherit;" class="badge">Deactivate</span>';
        }

        $status .= '</center>';

        return $status;
    }
}

if (!function_exists('all_delivery_location_drop_with_status'))
{
    function all_delivery_location_drop_with_status($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode,isActive');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Warehouse Location');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($row['isActive'] == 1)
                {
                    $activestatus = 'Active';
                }
                else
                {
                    $activestatus = 'Inactive';
                }

                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] ?? '') . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseDescription'] . '&nbsp;&nbsp;&nbsp;&nbsp; - ' . $activestatus));
            }
        }
        return $data_arr;
    }
}
if (!function_exists('all_delivery_location_drop_active'))
{
    function all_delivery_location_drop_active($status = TRUE)
    {
        $CI = &get_instance();
        $CI->db->select('wareHouseAutoID,wareHouseLocation,wareHouseDescription,wareHouseCode');
        $CI->db->from('srp_erp_warehousemaster');
        $CI->db->where('companyCode', $CI->common_data['company_data']['company_code']);
        $CI->db->where('isActive', 1);
        $data = $CI->db->get()->result_array();
        if ($status)
        {
            $data_arr = array('' => 'Select Warehouse Location');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['wareHouseAutoID'] ?? '')] = trim($row['wareHouseCode'] ?? '') . ' | ' . str_replace("'", "&apos;", trim($row['wareHouseLocation'] ?? '')) . ' | ' . preg_replace('/\s\s+/', ' ', str_replace("'", "&apos;", trim($row['wareHouseDescription'] ?? '')));
            }
        }

        return $data_arr;
    }
}
if (!function_exists('all_warehouse_drop_isactive_inactive'))
{
    function all_warehouse_drop_isactive_inactive($pID, $docID)
    {
        $ci = &get_instance();
        $companyID = current_companyID();
        $masterID = $pID;
        $output = array();
        switch ($docID)
        {
            case 'SLR':
                $tableName = 'srp_erp_salesreturnmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_salesreturnmaster.companyID';
                $documentid = 'salesReturnAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_salesreturnmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'GRV':
            case 'SRN':
                $tableName = 'srp_erp_grvmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_grvmaster.companyID';
                $documentid = 'grvAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_grvmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'SR':
                $tableName = 'srp_erp_stockreturnmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stockreturnmaster.companyID';
                $documentid = 'stockReturnAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stockreturnmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'MR':
                $tableName = 'srp_erp_materialrequest';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_materialrequest.companyID';
                $documentid = 'mrAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_materialrequest.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'MI':
                $tableName = 'srp_erp_itemissuemaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_itemissuemaster.companyID';
                $documentid = 'itemIssueAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_itemissuemaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'MRN':
                $tableName = 'srp_erp_materialreceiptmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_materialreceiptmaster.companyID';
                $documentid = 'mrnAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_materialreceiptmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'ST':
                $tableName = 'srp_erp_stocktransfermaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stocktransfermaster.companyID';
                $documentid = 'stockTransferAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stocktransfermaster.from_wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'ST2':
                $tableName = 'srp_erp_stocktransfermaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stocktransfermaster.companyID';
                $documentid = 'stockTransferAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stocktransfermaster.to_wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'SA':
                $tableName = 'srp_erp_stockadjustmentmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stockadjustmentmaster.companyID';
                $documentid = 'stockAdjustmentAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stockadjustmentmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'SCNT':
                $tableName = 'srp_erp_stockcountingmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stockcountingmaster.companyID';
                $documentid = 'stockCountingAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stockcountingmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'STB':
                $tableName = 'srp_erp_stocktransfermaster_bulk';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $companyColumn = 'srp_erp_stocktransfermaster_bulk.companyID';
                $documentid = 'stockTransferAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_stocktransfermaster_bulk.from_wareHouseAutoID';
                $wherecondition = '';
                break;
            case 'CNT':
            case 'SO':
            case 'QUT':
                $tableName = 'srp_erp_contractmaster';
                $masterColumn = 'warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive';
                $documentsyscode = 'contractCode';
                $companyColumn = 'srp_erp_contractmaster.companyID';
                $documentid = 'contractAutoID';
                $jointbl = 'srp_erp_warehousemaster warehouse';
                $condition = 'warehouse.wareHouseAutoID = srp_erp_contractmaster.wareHouseAutoID';
                $wherecondition = '';
                break;
            default:
                return $output;
        }

        $ci->db->select("{$masterColumn}");
        $ci->db->from("{$tableName}");
        $ci->db->join("{$jointbl}", "{$condition}");
        $ci->db->where("{$documentid}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);
        $wherecondition;

        $output = $ci->db->get()->row_array();
        return $output;
    }
}

if (!function_exists('all_warehouse_drop_isactive_inactive_multiple'))
{
    function all_warehouse_drop_isactive_inactive_multiple($pID, $docID)
    {
        $ci = &get_instance();
        $companyID = current_companyID();
        $masterID = $pID;
        $output = array();
        switch ($docID)
        {
            case 'STB':
                $tableName = 'srp_erp_stocktransfermaster_bulk';
                $masterColumn = 'to_wareHouseAutoID';
                $companyColumn = 'srp_erp_stocktransfermaster_bulk.companyID';
                $documentid = 'stockTransferAutoID';
                break;
            default:
                return $output;
        }

        $ci->db->select("{$masterColumn}");
        $ci->db->from("{$tableName}");
        $ci->db->where("{$documentid}", $masterID);
        $ci->db->where("{$companyColumn}", $companyID);

        $output1 = $ci->db->get()->row_array();
        if (!empty($output1))
        {
            $toWarehouse = explode(',', $output1['to_wareHouseAutoID']);

            $ci->db->select("warehouse.wareHouseAutoID,warehouse.wareHouseLocation,warehouse.wareHouseDescription,warehouse.wareHouseCode,warehouse.isActive");
            $ci->db->from("srp_erp_warehousemaster warehouse");
            $ci->db->where_in('wareHouseAutoID', $toWarehouse);
            $ci->db->where('warehouse.isActive', 0);
            $ci->db->where('companyID', $companyID);

            $output = $ci->db->get()->result_array();
        }

        return $output;
    }
}


// Get a set of date beetween the 2 period
if (!function_exists('get_daterange_list_from_date'))
{
    function get_daterange_list_from_date($beginingDate, $endDate, $format, $intervalType, $caption = "MY")
    {
        $start = new DateTime($beginingDate); // beginingDate
        $end = new DateTime($endDate); // endDate
        $interval = DateInterval::createFromDateString($intervalType); // 1 month interval
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period
        $months = array();
        foreach ($period as $dt)
        {
            if ($caption == 'MY')
            {
                $months[$dt->format($format)] = $dt->format("M") . "-" . $dt->format("Y");
            }
            else
            {
                $months[$dt->format($format)] = $dt->format($format);
            }
        }

        return $months;
    }
}

if (!function_exists('current_token'))
{
    function current_token()
    {
        $CI = &get_instance();
        if (!empty($CI->common_data['supportToken']))
        {
            $token = trim($CI->common_data['supportToken']);
        }
        else
        {
            $token = '';
        }
        return $token;
    }
}

if (!function_exists('update_do_item_wise_policy_value'))
{
    function update_do_item_wise_policy_value($masterID)
    {
        $CI = &get_instance();
        $DOItemWiseYNPolicyValue = getPolicyValues('DOIW', 'All');
        $DOItemWiseYN = is_null($DOItemWiseYNPolicyValue) ? 0 : $DOItemWiseYNPolicyValue;
        // $CI->db->select('isDOItemWisePolicy');
        // $CI->db->where($masterIDColumn, $masterID);
        // $isDOItemWisePolicy = $CI->db->get($masterTable)->row_array();
        $detailTBLExist = $CI->db->query("SELECT  COUNT(*) as docCount   FROM  srp_erp_customerinvoicedetails
                                          where invoiceAutoID = $masterID")->row('docCount');
        if ($detailTBLExist == 0)
        {
            $CI->db->query("UPDATE srp_erp_customerinvoicemaster   SET isDOItemWisePolicy = $DOItemWiseYN
                            WHERE invoiceAutoID = $masterID ");
        }
    }
}

if (!function_exists('approved_emp_details'))
{
    function approved_emp_details($documentID, $masterID)
    {
        $CI = &get_instance();
        $CI->db->select('approvalLevelID, Ename2, DesDescription, approvedDate');
        $CI->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_documentapproved.approvedEmpID');
        $CI->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId', 'LEFT');
        $CI->db->where('companyID', current_companyID());
        $CI->db->where('approvedYN', 1);
        $CI->db->where('documentSystemCode', $masterID);
        $CI->db->where('documentID', $documentID);
        $CI->db->order_by('approvalLevelID', 'ASC');
        $result = $CI->db->get('srp_erp_documentapproved')->result_array();

        return $result;
    }
}

if (!function_exists('designation_by_empid'))
{
    function designation_by_empid($empID)
    {
        $CI = &get_instance();
        $CI->db->select('EmpDesignationId, DesDescription');
        $CI->db->join('srp_designation', 'srp_designation.DesignationID = srp_employeesdetails.EmpDesignationId', 'LEFT');
        $CI->db->where('srp_employeesdetails.Erp_companyID', current_companyID());
        $CI->db->where('EIdNo', $empID);
        $result = $CI->db->get('srp_employeesdetails')->row_array();

        return $result;
    }
}

if (!function_exists('get_item_master_image'))
{
    function get_item_master_image($itemImage, $itemAutoID)
    {

        /*

        if ($itemImage == '' || $itemImage == NULL) {
           
        } else {
            
        }
    */

        $CI = &get_instance();
        $CI->load->library('s3');
        $path = 'uploads/itemMaster/' . $itemImage;
        $companyid = current_companyID();

        $itemimageexist = $CI->db->query("SELECT itemImage FROM `srp_erp_itemmaster` where companyID = '{$companyid}'  AND itemAutoID = '{$itemAutoID}' AND itemImage != \"no-image.png\"")->row_array();
        if (!empty($itemimageexist))
        {
            $img_item = $CI->s3->createPresignedRequest($path, '+1 hour');
            $generatedHTML = "<center><img class='img-thumbnail-2 zoom' alt='No image' src='$img_item' style='width:50px;height: 50px;'><center>";
        }
        else
        {
            $img_item = $CI->s3->createPresignedRequest('images/item/no-image.png', '+1 hour');
            $generatedHTML = "<center><img class='img-thumbnail-2 zoom' alt='No image' src='$img_item' style='width:50px;height: 50px;'><center>";
        }

        return $generatedHTML;
    }
}



if (!function_exists('company_bank_account_name_drop'))
{
    function company_bank_account_name_drop($status = 0, $isCash = 0, $select = 0)
    {
        $bank_arr = [];
        $CI = &get_instance();
        $CI->db->select("GLAutoID,bankName,bankBranch,bankSwiftCode,bankAccountNumber,subCategory,isCash, GLDescription");
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->WHERE('controllAccountYN', 0);
        $CI->db->WHERE('masterAccountYN', 0);
        $CI->db->where('isBank', 1);
        $CI->db->where('isActive', 1);
        $CI->db->where('approvedYN', 1);
        if ($isCash == 1)
        {
            $CI->db->where('isCash', 0);
        }
        $CI->db->where('companyID', current_companyID());
        $bank = $CI->db->get()->result_array();
        if ($status == 1)
        {
            return $bank;
        }

        if ($select == 0)
        {
            $bank_arr = array('' => 'Select Bank Account');
        }

        if (isset($bank))
        {
            foreach ($bank as $row)
            {
                $type = ($row['isCash'] == '1') ? ' | Cash' : ' | Bank';
                $bank_arr[trim($row['GLAutoID'] ?? '')] = trim($row['bankName'] ?? '') . ' | ' . trim($row['bankBranch'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '') . ' | ' . trim($row['bankAccountNumber'] ?? '') . ' | ' . trim($row['subCategory'] ?? '') . $type;
            }
        }

        return $bank_arr;
    }

    if (!function_exists('send_custom_email'))
    {
        function send_custom_email($to, $body, $subject, $attachment = null)
        {
            $CI = &get_instance();

            $config['charset'] = "utf-8";
            $config['mailtype'] = "html";
            $config['wordwrap'] = TRUE;
            $config['protocol'] = 'smtp';
            $config['smtp_host'] = $CI->config->item('email_smtp_host');
            $config['smtp_user'] = $CI->config->item('email_smtp_username');
            $config['smtp_pass'] = $CI->config->item('email_smtp_password');
            $config['smtp_crypto'] = 'tls';
            $config['smtp_port'] = $CI->config->item('email_smtp_port');
            $condig['crlf'] = "\r\n";
            $config['newline'] = "\r\n";

            $CI->load->library('email', $config);

            $CI->email->from($CI->config->item('email_smtp_from'));


            if (!empty($body))
            {
                $CI->email->to($to);
                $CI->email->subject($subject);
                $CI->email->message($body);
                if ($attachment == 1)
                {
                    //$CI->email->attach($path);
                }
            }

            $result = $CI->email->send();
            // $result = $CI->email->print_debugger();
            // $CI->email->clear(TRUE);

            return $result;
            //send_push_notification($approvalEmpID, $subject, $documentCode, 1);
        }
    }
}

if (!function_exists('get_warehouse_according_to_type'))
{
    function get_warehouse_according_to_type($select = 0)
    {
        $bank_arr = array();
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("srp_erp_warehousemaster.*");
        $CI->db->from("srp_erp_warehousemaster");
        $CI->db->where('warehouseType', 4);
        $CI->db->where('isActive', 1);
        $CI->db->where('companyID', $companyID);
        $output = $CI->db->get()->result_array();

        if ($select == 0)
        {
            $bank_arr = array('' => 'Select WArehouse');
        }


        foreach ($output as $row)
        {
            $bank_arr[$row['wareHouseAutoID']] = $row['wareHouseCode'] . ' - ' . $row['wareHouseDescription'];
        }


        return $bank_arr;
    }
}

if (!function_exists('update_item_batch_number_details'))
{
    function update_item_batch_number_details($invoiceArray)
    {
        $CI = &get_instance();

        $resultArray = array();

        if (count($invoiceArray) > 0)
        {

            foreach ($invoiceArray as $invoice)
            {

                $batch_number2 = explode(",", $invoice['batchNumber']);

                if (empty($invoice['batchNumber']))
                {
                    return true;
                }

                $requestedQtyWithUOM = $invoice['requestedQty'] / ($invoice['conversionRateUOM'] != 0) ? $invoice['conversionRateUOM'] : 1;


                if (count($batch_number2) > 0)
                {
                    $balanceRequestQtr = 0;


                    foreach ($batch_number2 as $key1 => $val)
                    {
                        $CI->db->select('*');
                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                        $CI->db->where('wareHouseAutoID',  $invoice['wareHouseAutoID']);
                        $CI->db->where('batchNumber', $val);
                        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
                        $batchitems = $CI->db->get('srp_erp_inventory_itembatch')->row_array();

                        if ($batchitems)
                        {

                            if ($batchitems['qtr'] > 0)
                            {

                                if ($balanceRequestQtr === 0)
                                {
                                    if ($requestedQtyWithUOM <= $batchitems['qtr'])
                                    {
                                        $data_batch['qtr'] = $batchitems['qtr'] - $requestedQtyWithUOM;
                                        $CI = &get_instance();
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);

                                        $resultArray[] = ['batchNumber' => $val, 'qty' => $requestedQtyWithUOM, 'itemAutoID' => $invoice['itemAutoID'], 'wareHouseAutoID' => $invoice['wareHouseAutoID']];

                                        break;
                                    }
                                    else
                                    {

                                        $data_batch['qtr'] = 0;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[] = ['batchNumber' => $val, 'qty' => $batchitems['qtr'], 'itemAutoID' => $invoice['itemAutoID'], 'wareHouseAutoID' => $invoice['wareHouseAutoID']];
                                        $balanceRequestQtr = $requestedQtyWithUOM - $batchitems['qtr'];
                                    }
                                }
                                else
                                {


                                    if ($balanceRequestQtr <= $batchitems['qtr'])
                                    {

                                        $data_batch['qtr'] = $batchitems['qtr'] - $balanceRequestQtr;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[] = ['batchNumber' => $val, 'qty' => $balanceRequestQtr, 'itemAutoID' => $invoice['itemAutoID'], 'wareHouseAutoID' => $invoice['wareHouseAutoID']];
                                        break;
                                    }
                                    else
                                    {

                                        $data_batch['qtr'] = 0;
                                        $CI->db->where('itemMasterID',  $invoice['itemAutoID']);
                                        $CI->db->where('wareHouseAutoID',  $invoice['wareHouseAutoID']);
                                        $CI->db->where('batchNumber', $val);
                                        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
                                        $CI->db->update('srp_erp_inventory_itembatch', $data_batch);
                                        $resultArray[] = ['batchNumber' => $val, 'qty' => $batchitems['qtr'], 'itemAutoID' => $invoice['itemAutoID'], 'wareHouseAutoID' => $invoice['wareHouseAutoID']];
                                        $balanceRequestQtr = $balanceRequestQtr - $batchitems['qtr'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $resultArray;
    }
}


if (!function_exists('get_item_batch_details_on_warehouse'))
{
    function get_item_batch_details_on_warehouse($itemAutoID, $batchNumber, $wareHouseID)
    {
        // srp_erp_inventory_itembatch
        $CI = &get_instance();

        $CI->db->where('itemMasterID',  $itemAutoID);
        $CI->db->where('wareHouseAutoID',  $wareHouseID);
        $CI->db->where('batchNumber', $batchNumber);
        $CI->db->where('companyId', $CI->common_data['company_data']['company_id']);
        $batch_details = $CI->db->from('srp_erp_inventory_itembatch')->get()->row_array();

        return $batch_details;
    }
}


if (!function_exists('set_item_batch'))
{
    function set_item_batch($qty, $itemAutoID, $batchNumber, $batchexpiry, $wareHouseID, $conversionRate)
    {
        // srp_erp_inventory_itembatch
        $CI = &get_instance();
        $data = array();
        $company_id = current_companyID();

        $ex_batch = get_item_batch_details_on_warehouse($itemAutoID, $batchNumber, $wareHouseID);

        if ($ex_batch)
        {
            //Batch exist
            $qty_ex = $ex_batch['qtr'] + $qty;

            $data['qtr'] = $qty_ex;

            $res = $CI->db->where('id', $ex_batch['id'])->update('srp_erp_inventory_itembatch', $data);
        }
        else
        {
            //Batch not exists
            if ($qty > 0)
            {

                $data['batchNumber'] = $batchNumber;
                $data['qtr'] = $qty / $conversionRate;
                $data['batchExpireDate'] = $batchexpiry;
                $data['itemMasterID'] = $itemAutoID;
                $data['companyId'] = $company_id;
                $data['wareHouseAutoID'] = $wareHouseID;
                $data['createdUserID'] = $CI->common_data['current_userID'];
                $data['createdDateTime'] = $CI->common_data['current_date'];

                $CI->db->insert('srp_erp_inventory_itembatch', $data);
            }
        }
    }
}


if (!function_exists('update_customerinvoicemaster_reference'))
{
    function update_customerinvoicemaster_reference($invoiceAutoID)
    {
        //not used 

        $CI = &get_instance();
        $compID = current_companyID();

        //update master reference
        $CI->db->where('invoiceAutoID', $invoiceAutoID);
        $CI->db->where('companyID', $compID);
        $ref_customerdetails = $CI->db->select('contractAutoID')->from('srp_erp_customerinvoicedetails')->get()->result_array();

        //update customer invoice master details
        $data_ref = array();
        $valid_str = '';
        $data_contains = array();
        $data_contract_details = array();

        foreach ($ref_customerdetails as $value_ref)
        {

            $contract_arr = $CI->db->where('contractAutoID', $value_ref['contractAutoID'])->where('companyID', $compID)->from('srp_erp_contractmaster')->get()->row_array();

            $ref_no = $contract_arr['referenceNo'];
            if (!in_array($ref_no, $data_contains))
            {
                if ($valid_str)
                {
                    $valid_str = $valid_str . ' | ' . $ref_no;
                }
                else
                {
                    $valid_str = $ref_no;
                }
            }

            $data_contains[] = $ref_no;

            $data_contract_details[$value_ref['contractAutoID']] = $contract_arr;
        }

        if (count($data_contract_details) > 1 || count($data_contract_details) == 0)
        {
            //get customermaster
            $CI->db->where('invoiceAutoID', $invoiceAutoID);
            $invoice_details = $CI->db->from('srp_erp_customerinvoicemaster')->get()->row_array();

            if ($invoice_details)
            {
                $customerID = $invoice_details['customerID'];

                //get default customer details
                $CI->db->where('customerAutoID', $customerID);
                $customer_details = $CI->db->from('srp_erp_customermaster')->get()->row_array();

                if ($customer_details)
                {
                    $data_ref['customerAddress'] = $customer_details['customerAddress1'] . ' ' . $customer_details['customerAddress2'];
                    $data_ref['customerTelephone'] = $customer_details['customerTelephone'];
                    $data_ref['customerEmail'] = $customer_details['customerEmail'];
                    $data_ref['customerWebURL'] = $customer_details['customerUrl'];
                }

                $data_ref['contractPaymentTerms'] = $customer_details['customerCreditPeriod'] * 30;
            }
        }
        elseif (count($data_contract_details) == 1)
        {
            //get from contract
            foreach ($data_contract_details as $key => $value)
            {

                $data_ref['customerAddress'] = $value['customerAddress'];
                $data_ref['customerTelephone'] = $value['customerTelephone'];
                $data_ref['customerEmail'] = $value['customerEmail'];
                $data_ref['customerWebURL'] = $value['customerWebURL'];
            }

            $data_ref['contractPaymentTerms'] = $value['paymentTerms'];
        }

        $data_ref['referenceNo'] = $valid_str;

        $CI->db->where('invoiceAutoID', $invoiceAutoID);
        $CI->db->update('srp_erp_customerinvoicemaster', $data_ref);

        return TRUE;
    }
}

if (!function_exists('get_contractmaster_details'))
{
    function get_contractmaster_details($contractAutoID)
    {
        $CI = &get_instance();
        $compID = current_companyID();
        $contract_arr = $CI->db->where('contractAutoID', $contractAutoID)->where('companyID', $compID)->from('srp_erp_contractmaster')->get()->row_array();

        return $contract_arr;
    }
}
if (!function_exists('all_employeessasset'))
{
    function all_employeessasset($isDischarged = false, $intType = '')
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $dischargeFilter = ($isDischarged) ? 'AND isDischarged !=1' : '';
        $sql = "select * from user_asset";

        /**** if @intType parameter given sql will combine with srp_erp_system_integration_user table  ** */
        // $int_column = ($intType != '') ? ", intUsr.integratedUserID" : '';
        // $sql = "SELECT empTB.* {$int_column} FROM srp_employeesdetails AS empTB ";
        // if ($intType != '') {
        //     $sql .= "LEFT JOIN (
        //                 SELECT empID, integratedUserID FROM srp_erp_system_integration_user
        //                 WHERE companyID={$companyID} AND integratedSystem='{$intType}'
        //              ) AS intUsr ON intUsr.empID = empTB.EIdNo ";
        // }
        // $sql .= "WHERE Erp_companyID={$companyID} {$dischargeFilter}";

        return $CI->db->query($sql)->result_array();
    }
}


if (!function_exists('getUserGroupId'))
{
    function getUserGroupId()
    {
        $CI = &get_instance();
        $compID = current_companyID();
        $empID =  $CI->common_data['current_userID'];
        $user_arr = $CI->db->where('empID', $empID)->where('companyID', $compID)->from('srp_erp_employeenavigation')->get()->row_array();
        //  echo var_dump($user_arr);
        // die('ll');
        return   $user_arr['userGroupID'];
    }
}

if (!function_exists('get_billing_records_job'))
{
    function get_billing_records_job($customerID, $segmentID)
    {
        // srp_erp_inventory_itembatch
        $CI = &get_instance();

        $CI->db->select('bill.*,job.job_code');
        $CI->db->from('srp_erp_job_billing as bill');
        $CI->db->join('srp_erp_jobsmaster as job', 'bill.job_id = job.id', 'left');
        $CI->db->join('srp_erp_contractmaster as contract', 'job.contract_po_id = contract.contractAutoID', 'left');
        $CI->db->where('bill.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('contract.segmentID', $segmentID);
        $CI->db->where('contract.customerID', $customerID);
        $CI->db->where('bill.invoice_id IS NULL', null);
        $billing_details = $CI->db->get()->result_array();

        return $billing_details;
    }
}

// start: leave salary provision configuration

if (!function_exists('fetch_provision_and_expense_Gl'))
{
    function fetch_provision_and_expense_Gl($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('systemAccountCode,GLAutoID,GLDescription');
        $CI->db->from('srp_erp_chartofaccounts');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('isActive', 1);
        $CI->db->where('isBank', 0);
        $CI->db->where('masterAccountYN', 0);
        $data = $CI->db->get()->result_array();
        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_gl')/*'Select GL'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GLAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('fetch_provision_Gl2'))
{
    function fetch_provision_Gl2($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $CI->db->select('t1.GlAutoID as GlAutoID, t2.systemAccountCode as systemAccountCode, t2.GLDescription as GLDescription');
        $CI->db->from('srp_erp_leave_salary_provision AS t1');
        $CI->db->join('srp_erp_chartofaccounts AS t2', 't2.GLAutoID = t1.GlAutoID');
        $CI->db->where('t1.isProvision', 1);
        $CI->db->where('t1.companyID', $CI->common_data['company_data']['company_id']);
        $CI->db->where('t2.isActive', 1);
        $CI->db->where('t2.isBank', 0);
        $CI->db->where('t2.masterAccountYN', 0);
        $data = $CI->db->get()->result_array();

        if ($state == TRUE)
        {
            $data_arr = array('' => $CI->lang->line('common_select_provision_gl')/*'Select Provision GL'*/);
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['GlAutoID'] ?? '')] = trim($row['systemAccountCode'] ?? '') . ' | ' . trim($row['GLDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}

// end : leave salary provision configuration


if (!function_exists('check_jv_valid_period'))
{
    function check_jv_valid_period($period, $type = 'Gratuity')
    {
        // srp_erp_inventory_itembatch
        $CI = &get_instance();
        // $type = 'Gratuity';
        $compID = current_companyID();

        //Check already ran for the financial period
        $CI->db->select('*');
        $CI->db->from('srp_erp_jvmaster as jv');
        $CI->db->where('jv.JVType', $type);
        $CI->db->where('jv.companyID', $compID);
        $CI->db->where('jv.companyFinancePeriodID', $period);
        $already_ex = $CI->db->get()->result_array();

        //Check already ran for the financial period
        $CI->db->select('*');
        $CI->db->from('srp_erp_jvmaster as jv');
        $CI->db->where('jv.JVType', $type);
        $CI->db->where('jv.companyID', $compID);
        $CI->db->where('jv.companyFinancePeriodID >', $period);
        $already_greater = $CI->db->get()->result_array();

        if ($already_ex)
        {
            return array('e', "Already ran the $type for this time period");
        }
        else if ($already_greater)
        {
            return array('e', "Already ran the $type for greater than this time period");
        }
    }
}


if (!function_exists('check_leave_salary_provision_setup'))
{
    function check_leave_salary_provision_setup()
    {
        // srp_erp_inventory_itembatch
        $CI = &get_instance();
        // $type = 'Gratuity';
        $compID = current_companyID();

        //Check already ran for the financial period
        $CI->db->select('*');
        $CI->db->from('srp_erp_leave_salary_provision as jv');
        $CI->db->where('jv.isProvision', 1);
        $CI->db->where('jv.companyID', $compID);
        $already_ex = $CI->db->get()->row_array();

        if (empty($already_ex))
        {
            return array('e', "Leave Salary Provision is Not Configured");
        }
    }
}

// start : Payable > transaction > payment voucher
if (!function_exists('fetch_expensegl'))
{
    function fetch_expensegl($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $CI->db->select('t1.expenseGLAutoID as expenseGLAutoID, CONCAT(t2.systemAccountCode, " | " ,t2.GLDescription) as expenseGLDescription');
        $CI->db->from('srp_erp_leave_salary_provision AS t1');
        $CI->db->join('srp_erp_chartofaccounts AS t2', 't2.GLAutoID = t1.expenseGLAutoID');
        $CI->db->where('t1.isProvision', 1);
        $CI->db->where('t1.companyID', $companyID);
        $CI->db->where('t2.isActive', 1);
        $CI->db->where('t2.isBank', 0);
        $CI->db->where('t2.masterAccountYN', 0);
        $data = $CI->db->get()->row_array();

        // to return expenseGLAutoID as the default value
        return isset($data['expenseGLAutoID']) ? $data['expenseGLAutoID'] : '';
    }
}
// end :


/* fetch voucher category for add new iou voucher*/
if (!function_exists('fetch_voucher_category'))
{
    function fetch_voucher_category($id = FALSE, $state = TRUE)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $CI->db->select('glCode, expenseClaimCategoriesAutoID , claimcategoriesDescription');
        $CI->db->from('srp_erp_expenseclaimcategories');
        $CI->db->where('type', 3);
        $CI->db->where('companyID', $companyID);
        $data = $CI->db->get()->result_array();


        if ($state == TRUE)
        {
            $data_arr = array('' => 'select voucher category');
        }
        else
        {
            $data_arr = [];
        }
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['expenseClaimCategoriesAutoID'] ?? '')] = trim($row['glCode'] ?? '') . ' | ' . trim($row['claimcategoriesDescription'] ?? '');
            }

            return $data_arr;
        }
    }
}
// end : 


/**unecpected function */
if (!function_exists('supplier_master_action_approval'))
{
    function supplier_master_action_approval($supplierAutoID, $Level, $approved, $ApprovedID, $isRejected, $approval = 1)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $SupplierMasterAttachments = "Supplier Master Attachments";
        $status = '<span class="pull-right">';
        $status .= '<a onclick=\'attachment_modal(' . $supplierAutoID . ',"' . $SupplierMasterAttachments . '","SUP");\'><span title="Attachment" rel="tooltip" class="glyphicon glyphicon-paperclip"></span></a>&nbsp;&nbsp; ';
        if ($approved == 0)
        {
            $status .= '| &nbsp;&nbsp;<a onclick=\'fetch_approval("' . $supplierAutoID . '","' . $ApprovedID . '","' . $Level . '"); \'><span title="View" rel="tooltip" class="glyphicon glyphicon-ok"></span></a>&nbsp;&nbsp;';
        }
        else
        {
            $status .= '| &nbsp;&nbsp;<a target="_blank" onclick="documentPageView_modal(\'SUP\',\'' . $supplierAutoID . '\',\'\',\'' . $approval . '\')" ><span title="View" rel="tooltip" class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;';
        }


        // $status .= '| &nbsp;&nbsp;<a target="_blank" href="' . site_url('Payable/load_supplier_invoice_conformation/') . '/' . $supplierAutoID . '" ><span title="Print" rel="tooltip" class="glyphicon glyphicon-print"></span></a>';

        $status .= '</span>';
        return $status;
    }
}

// end : Payable > transaction > lpayment voucher

if (!function_exists('load_employee_with_group_drop'))
{
    function load_employee_with_group_drop($status = false)
    {


        $CI = &get_instance();
        $companyID = current_companyID();

        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);
        $CI->db->select("EIdNo,ECode,Ename1,Ename2,Ename3,Ename4");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('Erp_companyID', current_companyID());
        $CI->db->where('empConfirmedYN', 1);
        $CI->db->where('isDischarged', 0);
        $CI->db->or_where('isDischarged', null);
        $customer = $CI->db->get()->result_array();

        $customer_arr = [];
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('fetch_dorpdown_my_attendess'))
{
    function fetch_dorpdown_my_attendess($empID)
    {
        $companyID = current_companyID();
        $CI = &get_instance();

        $customer = $CI->db->query("SELECT *
            FROM
            srp_employeesdetails emp
            WHERE
            emp.Erp_companyID = $companyID AND
            emp.empConfirmedYN =1 AND
            emp.isDischarged =0
            AND	emp.EIdNo NOT IN ( SELECT empID FROM srp_erp_employeemanagers WHERE srp_erp_employeemanagers.managerID = $empID ) ")->result_array();

        $customer_arr = [];
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['Ename2'] ?? '');
            }
        }

        return $customer_arr;
    }
}

if (!function_exists('delete_assign_buyers'))
{
    function delete_assign_buyers($id)
    {
        $status = '<span class="">';
        $status .= '<a class="text-yellow" onclick="delete_category_assign_buyers(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return $status;
    }
}

if (!function_exists('delete_assign_attendees'))
{
    function delete_assign_attendees($id)
    {
        $status = '<span class="">';
        $status .= '<a class="text-yellow" onclick="delete_assign_attenddes(' . $id . ');"><span title="Delete" rel="tooltip" class="glyphicon glyphicon-trash" style="color:rgb(209, 91, 71);"></span></a>';

        return $status;
    }
}

if (!function_exists('get_all_asset_with_disposed_drop'))
{
    function get_all_asset_with_disposed_drop($status, $isDisposed)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        if ($isDisposed == 1)
        {
            $cateogry = $CI->db->query("SELECT faID,faCode,assetDescription FROM `srp_erp_fa_asset_master` WHERE CompanyID = $companyID  AND confirmedYN = 1 AND approvedYN = 1 AND disposed != 1")->result_array();
        }
        else
        {
            $cateogry = $CI->db->query("SELECT faID,faCode,assetDescription FROM `srp_erp_fa_asset_master` WHERE CompanyID = $companyID  AND confirmedYN = 1 AND approvedYN = 1")->result_array();
        }

        $cateogry_arr = array('' => 'Select a Asset');
        if (isset($cateogry))
        {
            foreach ($cateogry as $row)
            {
                $cateogry_arr[trim($row['faID'] ?? '')] = trim($row['faCode'] ?? '') . ' | ' . trim($row['assetDescription'] ?? '');
            }
        }

        return $cateogry_arr;
    }
}

if (!function_exists('amount_based_approval_setup'))
{
    function amount_based_approval_setup($documentCode, $documentAmount, $level_id = null, $isSingleSourcePR = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('documentID', $documentCode);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }

        if ($isSingleSourcePR == 1)
        {
            $CI->db->where('criteriaID', 1);
        }

        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        $approverAvailable = [];
        foreach ($data as $level)
        {
            $approverAvailable_res = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND (fromAmount <= {$documentAmount} AND toAmount >= {$documentAmount})")->row_array();

            $approverAvailable[] = $approverAvailable_res['approvalUserID'];
        }

        if (empty($approverAvailable))
        {
            return array('type' => 'e', 'level' => '');
        }
        else
        {
            return array('type' => 's');
        }
    }
}

if (!function_exists('single_source_based_approval_setup'))
{
    function single_source_based_approval_setup($documentCode, $level_id = null, $isSingleSourcePR = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('documentID', $documentCode);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }

        if ($isSingleSourcePR == 1)
        {
            $CI->db->where('criteriaID', 1);
        }

        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        $approverAvailable = [];
        foreach ($data as $level)
        {
            $approverAvailable_res = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']}")->row_array();

            $approverAvailable[] = $approverAvailable_res['approvalUserID'];
        }

        if (empty($approverAvailable))
        {
            return array('type' => 'e', 'level' => '');
        }
        else
        {
            return array('type' => 's');
        }
    }
}

if (!function_exists('segment_based_approval'))
{
    function segment_based_approval($documentCode, $segmentID, $level_id = null, $isSingleSourcePR = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('segmentID', $segmentID);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }

        if ($isSingleSourcePR == 1)
        {
            $CI->db->where('criteriaID', 1);
        }

        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();

        $approverAvailable = [];

        foreach ($data as $level)
        {
            $approverAvailable_res = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND segmentID = {$segmentID}")->row_array();

            $approverAvailable[] = $approverAvailable_res['approvalUserID'];
        }

        if (empty($approverAvailable))
        {
            return array('type' => 'e', 'level' => '');
        }
        else
        {
            return array('type' => 's');
        }
    }
}

if (!function_exists('amount_base_segment_based_approval'))
{
    function amount_base_segment_based_approval($documentCode, $documentAmount, $segmentID, $level_id = null, $isSingleSourcePR = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('segmentID', $segmentID);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }

        if ($isSingleSourcePR == 1)
        {
            $CI->db->where('criteriaID', 1);
        }
        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();
        $approverAvailable = [];

        foreach ($data as $level)
        {
            $approverAvailable_res = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND segmentID = {$segmentID} AND (fromAmount <= {$documentAmount} AND toAmount >= {$documentAmount})")->row_array();

            $approverAvailable[] = $approverAvailable_res['approvalUserID'];
        }

        if (empty($approverAvailable))
        {
            return array('type' => 'e', 'level' => '');
        }
        else
        {
            return array('type' => 's');
        }
    }
}

if (!function_exists('getApprovalTypesONDocumentCode'))
{
    function getApprovalTypesONDocumentCode($documentID, $companyID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $approvalTypes = $CI->db->query("SELECT approvalType FROM `srp_erp_documentcodemaster`	                                    
	                                    where  companyID = {$companyID} and documentID = '{$documentID}' ")->row_array();
        return $approvalTypes;
    }
}

if (!function_exists('getApprovalTypes'))
{
    function getApprovalTypes($documentID, $companyID)
    {
        $CI = &get_instance();
        $companyID = current_companyID();
        $approvalTypes = $CI->db->query("SELECT approvalType FROM `srp_erp_documentcodemaster`	                                    
	                                    where  companyID = $companyID and documentID = $documentID ")->row('advanceamount');
        return $approvalTypes;
    }
}

if (!function_exists('fetch_signature_authority_on_gl_code'))
{
    function fetch_signature_authority_on_gl_code($GLAutoID)
    {
        $CI = &get_instance();
        $primaryLanguage = getPrimaryLanguage();
        $CI->lang->load('common', $primaryLanguage);

        $companyID = current_companyID();

        $CI->db->select('empID');
        $CI->db->where('glAutoID', $GLAutoID);
        $CI->db->where('deletedYN', 0);
        $CI->db->where('companyID', $companyID);
        $CI->db->from('srp_erp_chartofaccount_signatures');
        $emp = $CI->db->get()->result_array();

        $data = array();

        if ($emp)
        {
            foreach ($emp as $row)
            {
                $CI->db->select('EIdNo,ECode,Ename2');
                $CI->db->where('EIdNo', $row['empID']);
                $CI->db->where('Erp_companyID', $companyID);
                $CI->db->from('srp_employeesdetails');
                $result = $CI->db->get()->row_array();

                if (!empty($result))
                {
                    $data[$result['EIdNo']] = $result['ECode'] . ' | ' . $result['Ename2'];
                }
            }
        }

        return $data;
    }
}

///////////////////////// srm api call start ////////////////////////////////////////////
if (!function_exists('getLoginToken'))
{
    function getLoginToken()
    {

        $CI = &get_instance();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $CI->config->item('vendor_portal_api_base_url') . '/' . 'index.php/Api_spur/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => getLoginBody(),
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
}

if (!function_exists('getLoginBody'))
{
    function getLoginBody()
    {
        $CI = &get_instance();
        $jayParsedAry = [
            "username" => $CI->config->item('vendor_portal_api_username'),
            "password" => $CI->config->item('vendor_portal_api_password')
        ];

        return json_encode($jayParsedAry);
    }
}

if (!function_exists('send_approve_po_srm_portal'))
{
    function send_approve_po_srm_portal($id, $type, $data = null)
    {
        // 1-po ,2-grv ,3 -customer invoice
        $CI = &get_instance();
        $master_n = [


            "poID" => $id,
            "type" => $type,
            "companyID" => current_companyID(),
            "companyCode" => $CI->common_data['company_data']['company_code'],
            "dataSub" => $data

        ];

        $token = getLoginToken();

        $token_array = json_decode($token);

        if ($token_array)
        {

            if ($token_array->success == true)
            {

                $res = savePoStatusSupplierSide($master_n, $token_array->data->token);

                $res_array = json_decode($res);


                if ($res_array->status == true)
                {

                    if ($type == 1)
                    {
                        $data_detail1['isPortalPOSubmitted'] = 2;

                        $CI->db->where('purchaseOrderID', $id);
                        $CI->db->update('srp_erp_purchaseordermaster', $data_detail1);
                    }

                    return true;
                    // $this->send_company_approve_email_supplier($suvl['supplierID'],2);
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}

if (!function_exists('savePoStatusSupplierSide'))
{

    function savePoStatusSupplierSide($master, $token)
    {
        $CI = &get_instance();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $CI->config->item('vendor_portal_api_base_url') . '/index.php/Api_ecommerce/save_supplier_po_status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => getBodysavePoStatusSupplierSide($master),
            CURLOPT_HTTPHEADER => array(
                "SME-API-KEY: $token",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}

if (!function_exists('getBodysavePoStatusSupplierSide'))
{
    function getBodysavePoStatusSupplierSide($master)
    {
        $CI = &get_instance();
        $jayParsedAry = [
            "results" => [
                "dataMaster" => $master

            ]
        ];
        return json_encode($jayParsedAry);
    }
}


if (!function_exists('srmCommonApiCall'))
{

    function srmCommonApiCall($master, $details, $token, $endpoint)
    {
        $CI = &get_instance();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $CI->config->item('vendor_portal_api_base_url') . '/index.php' . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => getBodysrmCommonApiCall($master, $details),
            CURLOPT_HTTPHEADER => array(
                "SME-API-KEY: $token",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}

if (!function_exists('getBodysrmCommonApiCall'))
{
    function getBodysrmCommonApiCall($master, $sub)
    {
        $CI = &get_instance();
        $jayParsedAry = [
            "results" => [
                "dataMaster" => $master,
                "dataSub" => $sub

            ]
        ];
        return json_encode($jayParsedAry);
    }
}
if (!function_exists('fetchProjectCode_ioubooking'))
{
    function fetchProjectCode_ioubooking($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();

        $CI->db->select('contractAutoID,contractCode');
        $CI->db->from('srp_erp_contractmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('confirmedYN', 1);
        $CI->db->where('approvedYN', 1);

        $data = $CI->db->get()->result_array();
        $data_arr = [];
        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Project Code');
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['contractAutoID'] ?? '')] = trim($row['contractCode'] ?? '');
            }

            return $data_arr;
        }
    }
}
if (!function_exists('fetchJobCode_ioubooking'))
{
    function fetchJobCode_ioubooking($id = FALSE, $state = TRUE) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI = &get_instance();

        $CI->db->select('id, job_code');
        $CI->db->from('srp_erp_jobsmaster');
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        //$CI->db->where('confirmed', 1);
        $CI->db->where('approved', 1);
        $data = $CI->db->get()->result_array();

        $data_arr = [];
        if ($state == TRUE)
        {
            $data_arr = array('' => 'Select Job Code');
        }

        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['job_code'] ?? '');
            }

            return $data_arr;
        }
    }
}

if (!function_exists('getLoginTokenWithoutPW'))
{
    function getLoginTokenWithoutPW($supplierID)
    {

        $CI = &get_instance();
        $curl = curl_init();

        $CI->db->select('*');
        $CI->db->from('srp_erp_suppliermaster');
        $CI->db->where('supplierAutoID', $supplierID);
        $supplier_details = $CI->db->get()->row_array();

        if ($supplier_details)
        {
            return array("status" => true, "token" => $supplier_details['apiKey']);
        }
        else
        {
            return array("status" => false, "token" => '');
        }
    }
}

if (!function_exists('get_customer_order'))
{
    function get_customer_order($isBtB = null)
    {
        $CI = &get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_srm_customerordermaster as customer');

        $CI->db->where('customer.companyID', $CI->common_data['company_data']['company_id']);
        if ($isBtB)
        {
            $CI->db->where('customer.isBackToBack', '1');
        }
        $CI->db->where('customer.confirmedYN', '1');
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Customer Order');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['customerOrderID'] ?? '')] = trim($row['customerOrderCode'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('get_quotation_detail_status'))
{
    function get_quotation_detail_status($approved = null, $contractAutoID = null)
    {
        $CI = &get_instance();
        $compID = current_companyID();

        $CI->db->select('srp_erp_contractmaster.*');
        if ($approved)
        {
            $CI->db->where('documentStatus', $approved);
        }
        $CI->db->where('srp_erp_contractmaster.companyID', $compID);

        if ($contractAutoID)
        {
            $CI->db->where("srp_erp_purchaseordermaster.contractAutoID IS NULL OR srp_erp_purchaseordermaster.contractAutoID = '{$contractAutoID}'", null);
        }
        else
        {
            $CI->db->where('srp_erp_purchaseordermaster.purchaseOrderID IS NULL', null);
        }

        $CI->db->from('srp_erp_contractmaster');
        $CI->db->join('srp_erp_purchaseordermaster', 'srp_erp_contractmaster.contractAutoID = srp_erp_purchaseordermaster.contractAutoID', 'left');

        $contract_arr =  $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Quotation');
        if (isset($contract_arr))
        {
            foreach ($contract_arr as $row)
            {
                $data_arr[trim($row['contractAutoID'] ?? '')] = trim($row['contractCode'] ?? '');
            }
        }

        return $data_arr;
    }
}


if (!function_exists('get_purchase_order_list'))
{
    function get_purchase_order_list($isBtB = null)
    {
        $CI = &get_instance();
        $CI->db->select('purchaseOrder.*,cusorder.customerOrderCode');
        $CI->db->from('srp_erp_purchaseordermaster as purchaseOrder');
        $CI->db->from('srp_erp_srm_customerordermaster as cusorder', 'purchaseOrder.customerOrderID = cusorder.customerOrderID', 'left');
        $CI->db->where('purchaseOrder.companyID', $CI->common_data['company_data']['company_id']);
        if ($isBtB)
        {
            $CI->db->where('purchaseOrder.purchaseOrderType', 'BQUT');
        }
        $CI->db->where('purchaseOrder.confirmedYN', '1');
        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Purchase Order');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                if ($row['customerOrderCode'])
                {
                    $data_arr[trim($row['purchaseOrderID'] ?? '')] = trim($row['purchaseOrderCode'] ?? '') . ' - ' . trim($row['customerOrderCode'] ?? '');
                }
                else
                {
                    $data_arr[trim($row['purchaseOrderID'] ?? '')] = trim($row['purchaseOrderCode'] ?? '');
                }
            }
        }
        return $data_arr;
    }
}

if (!function_exists('get_customerOrderList'))
{
    function get_customerOrderList($isBtB = null, $customerOrderID = null)
    {
        $CI = &get_instance();
        $CI->db->select('cusorder.*');
        $CI->db->from('srp_erp_srm_customerordermaster as cusorder');
        $CI->db->join('srp_erp_contractmaster as master', 'cusorder.customerOrderID = master.customerOrderID', 'left');
        $CI->db->where('cusorder.companyID', $CI->common_data['company_data']['company_id']);

        if ($isBtB)
        {
            $CI->db->where('cusorder.isBackToBack', '1');
        }

        if ($customerOrderID)
        {
            $CI->db->where("master.customerOrderID IS NULL OR master.customerOrderID = {$customerOrderID}", null);
        }
        else
        {
            $CI->db->where("master.customerOrderID IS NULL", null);
        }

        $CI->db->where('cusorder.confirmedYN', '1');
        $CI->db->order_by('cusorder.customerOrderID', 'DESC');

        $data = $CI->db->get()->result_array();

        $data_arr = array('' => 'Select Customer Order');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['customerOrderID'] ?? '')] = trim($row['customerOrderCode'] ?? '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('get_mode_of_travel'))
{
    function get_mode_of_travel($id = null, $arr = null)
    {


        $modeOfPayment = array('' => 'Select Mode of Shipment', '1' => 'Ex-Works', '2' => 'By Land', '3' => 'By Sea', '4' => 'By Air');

        if ($id)
        {
            foreach ($modeOfPayment as $key => $value)
            {
                if ($key == $id)
                {
                    return $value;
                }
            }
        }
        if ($arr)
        {
            return $modeOfPayment;
        }
    }
}

if (!function_exists('get_operationDocuments'))
{
    function get_operationDocuments()
    {
        $companyID = current_companyID();
        $CI = &get_instance();

        $opDocuments = $CI->db->query("SELECT id, documentDescription, documentFile FROM srp_erp_operation_documents
                                       WHERE companyID={$companyID} ")->result_array();

        return $opDocuments;
    }
}
if (!function_exists('getTravelTypes'))
{
    function getTravelTypes()
    {
        $CI = &get_instance();
        $CI->db->select("id, tripType");
        $CI->db->from('srp_erp_travel_type');
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('load_airportdestination_drop'))
{
    function load_airportdestination_drop()
    {
        $CI = &get_instance();
        $CI->db->select('destinationID,City');
        $CI->db->from('srp_erp_airportdestinationmaster');
        $cities = $CI->db->get()->result_array();

        return $cities;
    }
}

if (!function_exists('load_city_drop'))
{
    function load_city_drop()
    {
        $CI = &get_instance();
        $CI->db->select('City');
        $CI->db->from('srp_erp_airportdestinationmaster');
        $cities = $CI->db->get()->result_array();

        return $cities;
    }
}


if (!function_exists('getCurrencyCodes'))
{
    function getCurrencyCodes()
    {
        $CI = &get_instance();
        $CI->db->select("CurrencyID, CurrencyCode");
        $CI->db->from('srp_erp_currencymaster');
        $result = $CI->db->get()->result_array();

        return $result;
    }
}
if (!function_exists('getemployee'))
{
    function getemployee()
    {
        $CI = &get_instance();
        $com = current_companyID();
        $CI->db->select("EIdNo, IFNULL(Ename2, '') AS employee");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $com);
        $result = $CI->db->get()->result_array();
        return $result;
    }
}

if (!function_exists('travelapplicationemployee'))
{
    function travelapplicationemployee()
    {
        $CI = &get_instance();
        $com = current_companyID();
        $CI->db->select("EIdNo, ECode,DesDescription, IFNULL(Ename2, '') AS employee,isMonthly as policyMasterID,srp_employeesdetails.leaveGroupID, DepartmentDes");
        $CI->db->from('srp_employeesdetails');
        $CI->db->join('srp_designation', 'srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID');
        $CI->db->join('srp_erp_leavegroup', 'srp_employeesdetails.leaveGroupID=srp_erp_leavegroup.leaveGroupID', 'INNER');
        $CI->db->join(' (
                         SELECT EmpID AS empID_Dep, DepartmentDes FROM srp_departmentmaster AS departTB
                         JOIN srp_empdepartments AS empDep ON empDep.DepartmentMasterID = departTB.DepartmentMasterID
                         WHERE departTB.Erp_companyID=' . $com . ' AND empDep.Erp_companyID=' . $com . ' AND empDep.isActive=1 AND empDep.isPrimary = 1  GROUP BY EmpID
                     ) AS departTB', 'departTB.empID_Dep=srp_employeesdetails.EIdNo', 'left');
        $CI->db->where('srp_employeesdetails.Erp_companyID', $com);
        $CI->db->where('isActive', 1);
        $CI->db->where('isDischarged', 0);

        /*   $CI->db->where('isDischarged !=', 1);*/
        $customer = $CI->db->get()->result_array();
        $customer_arr = array();
        if (isset($customer))
        {
            foreach ($customer as $row)
            {
                $customer_arr[trim($row['EIdNo'] ?? '')] = trim($row['ECode'] ?? '') . ' | ' . trim($row['employee'] ?? '');
            }
        }

        return $customer_arr;
    }
}


/////////////////// srm api call end /////////////////////////////////////////////


if (!function_exists('fetch_all_employees'))
{
    function fetch_all_employees($id = FALSE, $status = TRUE)
    {
        $CI = &get_instance();
        $companyID = $CI->common_data['company_data']['company_id'];

        $CI->db->select("EIdNo, Ename2");
        $CI->db->from('srp_employeesdetails');
        $CI->db->where('isDischarged', 0);
        $CI->db->where('Erp_companyID', $companyID);
        $employees = $CI->db->get()->result_array();
        $employee_arr=[];
        if ($status == TRUE)
        {
            $employee_arr = array('' => 'Select Employee');
        }
        if (isset($employees))
        {
            // $masterVal = ($keyType == 'ID') ? 'currencyID' : 'CurrencyCode';
            foreach ($employees as $row)
            {
                $employee_arr[trim($row['EIdNo'] ?? '')] = trim($row['Ename2'] ?? '');
            }
        }

        return $employee_arr;
    }
}

if (!function_exists('getLoginTokenCurrentCompany'))
{
    function getLoginTokenCurrentCompany()
    {

        $CI = &get_instance();

        $db2 = $CI->load->database('db2', TRUE);
        $db2->select('*');
        $db2->where("companyID", $CI->common_data['company_data']['company_id']);
        $db2->where("empID", $CI->common_data['current_userID']);
        $resultDb2 = $db2->get("user")->row_array();

        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';


        $link = "$protocol$_SERVER[HTTP_HOST]" . '/' . 'index.php/Api_self/login';

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => getLoginBodygetLoginTokenCurrentCompany($resultDb2['Username'], $resultDb2['Password']),
            CURLOPT_HTTPHEADER => array(
                // "SME-API-KEY: $token",
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}

if (!function_exists('getLoginBodygetLoginTokenCurrentCompany'))
{
    function getLoginBodygetLoginTokenCurrentCompany($username, $pass)
    {
        $CI = &get_instance();
        $jayParsedAry = [
            "username" => $username,
            "password" => $pass
        ];

        return json_encode($jayParsedAry);
    }
}

if (!function_exists('load_fp_departments'))
{
    function load_fp_departments($status = TRUE)
    {
        $CI = &get_instance();

        $CI->db->select("id, departmentName");
        $CI->db->from('srp_erp_fy_department');
        $departments = $CI->db->get()->result_array();
        $departments_arr = [];
        if ($status == TRUE)
        {
            $departments_arr = array('' => 'Select Department');
        }
        if (isset($departments))
        {
            foreach ($departments as $row)
            {
                $departments_arr[trim($row['id'] ?? '')] = trim($row['departmentName'] ?? '');
            }
        }

        return $departments_arr;
    }
}



if (!function_exists('stock_type_dropdown'))
{
    function stock_type_dropdown()
    {
        $CI = &get_instance();
        $CI->db->SELECT("id,description");
        $CI->db->FROM('srp_erp_stockcount_type');
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Stock Count Type');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('family_drop'))
{
    function family_drop($empid)
    {
        $CI = &get_instance();
        $CI->db->select("fm.*, fmr.relationship AS relation,emp.AirportDestinationID AS fromDestinationID");
        $CI->db->from('srp_erp_family_details AS fm');
        $CI->db->join('srp_erp_family_relationship AS fmr', 'fmr.relationshipID = fm.relationship');
        $CI->db->join('srp_employeesdetails AS emp', 'emp.EIdNo = fm.empID');
        $CI->db->where('empID', $empid); 
        

        $fam = $CI->db->get()->result_array();
        return $fam;
    }
}

if (!function_exists('load_job_category_list'))
{
    function load_job_category_list()
    {
        $CI = &get_instance();
        $CI->db->select("id, JobCategory");
        $CI->db->from('srp_erp_designation_category');
        $types =  $CI->db->get()->result_array();

        $types_arr = array('' => $CI->lang->line('common_select_type')/*'Select Job Category'*/);
        if (isset($types))
        {
            foreach ($types as $row)
            {
                $types_arr[trim($row['id'] ?? '')] = trim($row['JobCategory'] ?? '');
            }
        }
        return $types_arr;
    }
}

if (!function_exists('load_segment_masterID_options'))
{
    function load_segment_masterID_options()
    {
        $CI = &get_instance();
        $CI->db->select('segmentID, segmentCode, description');
        $CI->db->from('srp_erp_segment');
        $CI->db->where('companyID', current_companyID());


        $CI->db->group_start();
        $CI->db->where('masterID IS NULL');
        $CI->db->or_where('masterID', 0);
        $CI->db->group_end();

        $CI->db->order_by('segmentCode');
        $data = $CI->db->get()->result_array();

        $data_arr = [];

        if (!empty($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['segmentID'] ?? '')] = (trim($row['segmentCode'] ?? '') ? trim($row['segmentCode'] ?? '') . '|' : '') . (trim($row['description'] ?? '') ? trim($row['description'] ?? '') : '');
            }
        }

        return $data_arr;
    }
}

if (!function_exists('category_based_approval_setup'))
{
    function category_based_approval_setup($documentCode, $documentAmount, $categoryID, $level_id = null, $isSingleSourcePR = 0)
    {
        $CI = &get_instance();
        $companyID = current_companyID();

        $CI->db->select("DISTINCT(levelNo) AS levels");
        $CI->db->from('srp_erp_approvalusers');
        $CI->db->where('typeID', $categoryID);
        $CI->db->where('companyID', $companyID);
        if ($level_id)
        {
            $CI->db->where('levelNo >= ', $level_id);
        }

        if ($isSingleSourcePR == 1)
        {
            $CI->db->where('criteriaID', 1);
        }
        $CI->db->order_by('levelNo ASC');
        $data = $CI->db->get()->result_array();


        $approverAvailable = [];

        foreach ($data as $level)
        {
            $approverAvailable_res = $CI->db->query("SELECT approvalUserID 
                     FROM srp_erp_approvalusers 
                     WHERE documentID = '{$documentCode}' AND companyID = {$companyID} 
                     AND levelNo = {$level['levels']} AND typeID = {$categoryID} AND (fromAmount <= {$documentAmount} AND toAmount >= {$documentAmount})")->row_array();

            $approverAvailable[] = $approverAvailable_res['approvalUserID'];
        }

        if (empty($approverAvailable))
        {
            return array('type' => 'e', 'level' => '');
        }
        else
        {
            return array('type' => 's');
        }
    }
}

/**department wise financial period active status */
if (!function_exists('load_department_Financial_year_isactive_status'))
{
    function load_department_Financial_year_isactive_status($departmentFinancePeriodID, $isActive, $department_required = null)
    {
        $status = '<center>';

        if ($department_required == 'department_required')
        {
            $status .= '<input type="checkbox" id="isactivesatus_' . $departmentFinancePeriodID . '" name="isactivesatus" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="department_required()">';
        }
        else
        {
            if ($isActive)
            {
                $status .= '<input type="checkbox" id="isactivesatus_' . $departmentFinancePeriodID . '" name="isactivesatus"  data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="change_department_Financial_yearisactivesatus(' . $departmentFinancePeriodID . ')" checked>';
                $status .= '</span>';
            }
            else
            {
                $status .= '<input type="checkbox" id="isactivesatus_' . $departmentFinancePeriodID . '" name="isactivesatus" data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Deactive" data-label-width="0" onclick="change_department_Financial_yearisactivesatus(' . $departmentFinancePeriodID . ')">';
            }
        }
        $status .= '</center>';

        return $status;
    }
}

/**department wise financial period current status */
if (!function_exists('load_department_Financial_year_isactive_current'))
{
    function load_department_Financial_year_isactive_current($departmentFinancePeriodID, $is_current, $companyFinanceYearID, $department_required = null)
    {
        $status = '<center>';

        if ($department_required == 'department_required')
        {
            $status .= '<input type="radio" class="radiobtn" onclick="department_required()" name="iscurrentstatus" id="iscurrentstatus_' . $departmentFinancePeriodID . '">';
        }
        else
        {
            if ($is_current)
            {
                $status .= '<input type="radio" class="radiobtn" onclick="check_department_financial_period_iscurrent(' . $departmentFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $departmentFinancePeriodID . '" checked>';
            }
            else
            {
                $status .= '<input type="radio" class="radiobtn" onclick="check_department_financial_period_iscurrent(' . $departmentFinancePeriodID . ',' . $companyFinanceYearID . ')" name="iscurrentstatus" id="iscurrentstatus_' . $departmentFinancePeriodID . '">';
            }
        }
        $status .= '</center>';

        return $status;
    }
}

/**department wise financial period closed status */
if (!function_exists('load_department_financialperiod_isclosed_closed'))
{
    function load_department_financialperiod_isclosed_closed($departmentFinancePeriodID, $is_Close, $department_required = null)
    {
        $status = '<center>';
        if ($department_required == 'department_required')
        {
            $status .= '<input type="checkbox" id="closefinaperiod_' . $departmentFinancePeriodID . '" name="closefinaperiod" onchange="department_required()" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
        }
        else
        {
            if ($is_Close)
            {
                $status .= '<input type="checkbox" id="closefinaperiod_' . $departmentFinancePeriodID . '" name="closefinaperiod" onchange="change_department_financialperiodclose(' . $departmentFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0" checked>';
            }
            else
            {
                $status .= '<input type="checkbox" id="closefinaperiod_' . $departmentFinancePeriodID . '" name="closefinaperiod" onchange="change_department_financialperiodclose(' . $departmentFinancePeriodID . ')" data-size="mini" data-on-text="Closed" data-handle-width="45" data-on-color="danger" data-off-color="success" data-off-text="Close" data-label-width="0">';
            }
        }
        $status .= '</center>';

        return $status;
    }
}

/**department wise financial period action */
if (!function_exists('load_department_fp_action'))
{
    function load_department_fp_action($isClosed, $departmentFinancePeriodID, $department_required = null)
    {
        $CI = &get_instance();
        $empID = current_userID();
        $userAdmin = $CI->db->query("SELECT isSystemAdmin FROM srp_employeesdetails WHERE EIdNo =$empID")->row_array();
        $action = '';

        if ($department_required == 'department_required')
        {
            $action .= '<a onclick="department_required()"><span title="Edit" class="glyphicon glyphicon-repeat"  rel="tooltip"></a>';
        }
        else
        {
            if ($isClosed == 1 && $userAdmin['isSystemAdmin'] == 1)
            {
                $action .= '<a onclick="reopen_department_finacial_period(' . $departmentFinancePeriodID . ')"><span title="Edit" class="glyphicon glyphicon-repeat"  rel="tooltip"></a>';
            }
            else
            {
                $action .= '<span class="glyphicon glyphicon-remove-circle" style="color: #ff0000c2 "></span>';
            }
        }
        $action .= '</span>';
        return $action;
    }
}

if (!function_exists('fetch_asset_status'))
{
    function fetch_asset_status()
    {
        $CI = &get_instance();
        $CI->db->SELECT("id, status");
        $CI->db->from('fleet_asset_utilization_status');
        $CI->db->where('related_to', 4);
        $data = $CI->db->get()->result_array();
        $data_arr = array('' => 'Select Asset Status');
        if (isset($data))
        {
            foreach ($data as $row)
            {
                $data_arr[trim($row['id'] ?? '')] = trim($row['status'] ?? '');
            }
        }
        return $data_arr;
    }
}

if (!function_exists('add_version_code')) { /*get po action list*/
    function add_version_code($code, $versionNo)
    {
        if($versionNo ==0){
            $status = $code;
        }else{
            $status = $code.'(V'.$versionNo.')';
        }
        
       
        return $status;
    }
}


if (!function_exists('load_version_drop_down'))
{
    function load_version_drop_down($poID,$type)
    {
        $CI = &get_instance();
        $CI->db->select("*");
        $CI->db->from('srp_erp_document_version');
        $CI->db->where('documentMasterID', $poID);
        $CI->db->where('documentID', $type);
        $CI->db->where('companyID', $CI->common_data['company_data']['company_id']);
        $group = $CI->db->get()->result_array();

        $group_arr = array('' => 'Select Version');
        if (isset($group))
        {
            foreach ($group as $row)
            {
                $group_arr[$row['id']] = $row['documentCode'];
            }
        }

        return $group_arr;
    }
}
