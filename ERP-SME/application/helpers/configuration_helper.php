<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('LOGO','gears-ico.png');
define('PATH', "http://".$_SERVER['SERVER_NAME']."/");
define('SEND_GRID_EMAIL_KEY', "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");

if (!function_exists('server_path')) {
    function server_path($path){
        return PATH.$path;
    }
}

if (!function_exists('all_currency_drop')) {
    function all_currency_drop($status = true)
    {
        $CI =& get_instance();
        $CI->db->select("currencyID,CurrencyCode,CurrencyName");
        $CI->db->from('srp_erp_currencymaster');
        $currency = $CI->db->get()->result_array();
        $currency_arr = array('' => 'Select Currency');
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'])] = trim($row['CurrencyCode']) . ' | ' . trim($row['CurrencyName']);
            }
        }
        return $currency_arr;
    }
}

if (!function_exists('fetch_industry')) {
    function fetch_industry($status = true)
    {
        $CI =& get_instance();
        $CI->load->database("db2");
        $CI->db->select("industrytypeID,industryTypeDescription");
        $CI->db->from('srp_erp_industrytypes');
        $currency = $CI->db->get()->result_array();
        $currency_arr = array('' => 'Select Industry');
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['industrytypeID'])] = trim($row['industryTypeDescription']);
            }
        }
        return $currency_arr;
    }
}

if (!function_exists('tyim_value')) {
    function tyim_value($string)
    {
        $string = preg_replace("/[\n\r]/","",$string);
        return trim($string);
    }
}

if (!function_exists('confirm')) {
    function confirm($con)
    {
        $status = '<center>';
        if ($con == 0) {
            $status .= '<span class="label label-danger">&nbsp;</span>';
        } elseif ($con == 1) {
            $status .= '<span class="label label-success">&nbsp;</span>';
        } elseif ($con == 2) {
            $status .= '<span class="label label-warning">&nbsp;</span>';
        } else {
            $status .= '-';
        }
        $status .= '</center>';
        return $status;
    }
}

if (!function_exists('fetch_currency_dec')) {
    function fetch_currency_dec($code)
    {
        $CI =& get_instance();
        $CI->db->SELECT("CurrencyName");
        $CI->db->FROM('srp_erp_currencymaster');
        $CI->db->WHERE('CurrencyCode', $code);
        return $CI->db->get()->row('CurrencyName');
    }
}

if (!function_exists('load_financial_year_status')) {
    function load_financial_year_status($yearID, $isActive)
    {
        $status = ($isActive == 1)? 'checked': '';
        $str = '<div style="text-align: center">';
        $str .= '<input type="checkbox" id="statusactivate_'.$yearID.'" name="statusactivate"';
        $str .= ' onchange="changeFinancial_yearsatus('.$yearID.')" '.$status.' disabled>';
        $str .= '</div>';
        return $str;
    }
}

if (!function_exists('load_financial_year_current')) {
    function load_financial_year_current($yearID, $is_current)
    {        
        $str = '<div style="text-align: center">';
        $str .= '<input type="radio" class="finYearRadio" onclick="changeFinancial_yearcurrent('.$yearID.')" ';
        $str .= 'name="currentFinYear" id="currentFinYear_'.$yearID.'" data-status="'.$is_current.'" disabled>';
        $str .= '</div>';        
        return $str;
    }
}

if (!function_exists('load_financial_year_isactive_status')) {
    function load_financial_year_isactive_status($yearID, $isActive)
    {
        $status = ($isActive == 1)? 'checked': '';
        $str = '<div style="text-align: center">';
        $str .= '<input type="checkbox" id="isactivesatus_' . $yearID . '" name="isactivesatus" ';
        $str .= 'onchange="changeFinancial_yearisactivesatus(' . $yearID . ')" '.$status.' disabled>';
        $str .= '</div>';
        return $str;
    }
}

if (!function_exists('load_financial_year_isactive_current')) { /*get po action list*/
    function load_financial_year_isactive_current($periodID, $is_current, $yearID)
    { 
        $status = ($is_current == 1)? 'checked': '';
        $str = '<div style="text-align: center">';
        $str .= '<input type="radio" onclick="change_financial_period_current('.$periodID.','.$yearID.')" ';
        $str .= 'name="iscurrentstatus" id="iscurrentstatus_' . $periodID . '" '.$status.' disabled>';
        $str .= '</div>';
        return $str;
    }
}

if (!function_exists('load_financialperiod_isclosed_closed')) {
    function load_financialperiod_isclosed_closed($periodID, $isClose)
    { 
        $status = ($isClose == 1)? 'checked': '';
        $disabled = ($isClose == 1)? '': 'disabled';
        $str = '<div style="text-align: center">';
        $str .= '<input type="checkbox" id="finPeriodCloseChk_'.$periodID.'" name="closefinaperiod" ';
        $str .= 'onchange="finanCloseReactiveConf('.$periodID.', \'period\')" '.$status.' '.$disabled.'>';
        $str .= '</div>';        
        return $str;
    }
}

if (!function_exists('load_financial_year_close')) {
    function load_financial_year_close($yearID, $isClose)
    {
        $status = ($isClose == 1)? 'checked': '';
        $disabled = ($isClose == 1)? '': 'disabled';
        $str = '<div style="text-align: center">';
        $str .= '<input type="checkbox" id="finYearCloseChk_' . $yearID . '" name="finYearCloseChk" ';
        $str .= 'onchange="finanCloseReactiveConf('.$yearID.', \'year\')" '.$status.' '.$disabled.'>';
        $str .= '</div>';    
        return $str; 
    }
}

if (!function_exists('fetch_all_modules')) {
    function fetch_all_modules($id = false, $state = true) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $CI->db->select('navigationMenuID,description,masterID');
        $CI->db->from('srp_erp_navigationmenus');
        $CI->db->where('masterID', null);
        $data = $CI->db->get()->result_array();
        if ($state == true) {
            $data_arr = array('' => 'Select Module');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['navigationMenuID'])] =  trim($row['description']);

            }
        }
        return $data_arr;
    }
}

if (!function_exists('fetch_all_companies')) {
    function fetch_all_companies($id = false, $state = true) /*$id parameter is used to display only ID as value in select option*/
    {
        $CI =& get_instance();
        $CI->db->select('company_id,company_name,company_code');
        $CI->db->from('srp_erp_company');
        $data = $CI->db->get()->result_array();
        if ($state == true) {
            $data_arr = array('' => 'Select Company');
        } else {
            $data_arr = '';
        }
        if (isset($data)) {
            foreach ($data as $row) {
                $data_arr[trim($row['company_id'])] =  trim($row['company_name']). ' | ' . trim($row['company_code']);

            }
        }
        return $data_arr;
    }
}

if(!function_exists('subscription_action')){
    function subscription_action($company_id){
        $str = '<div class="pull-right" style="width: 40px"><a href="#" onclick="subscription_action('.$company_id.',  this)" ';
        $str .= 'title="Update subscription amount"> <i class="fa fa-edit"></i></a>&nbsp; | &nbsp; <a href="#" title="History" onclick="load_history('.$company_id.', this)">';
        $str .= '<i class="fa fa-history"></i> </a> | ';
        $str .= '<span title="Config" onclick="load_warehouse('.$company_id.', this)"><i class="fa fa-gear"></i></span> </div>';

        return $str;
    }
}

if(!function_exists('table_class')){
    function table_class(){
        return 'table table-bordered table-striped table-condensed';
    }
}

if(!function_exists('current_pc')){
    function current_pc(){
        return gethostbyaddr($_SERVER['REMOTE_ADDR']);
    }
}

if(!function_exists('current_userID')){
    function current_userID(){
        $ci =& get_instance();
        return $ci->session->userdata('log_userID');
    }
}

if(!function_exists('current_userName')){
    function current_userName(){
        $ci =& get_instance();
        return $ci->session->userdata('sme_company_userDisplayName');
    }
}

if(!function_exists('current_userType')){
    function current_userType(){
        $ci =& get_instance();
        return $ci->session->userdata('adminType');
    }
}

if (!function_exists('send_subscription_mail')) {
    function send_subscription_mail($mailData)
    {
        $CI =& get_instance();
        if(ENVIRONMENT == 'development'){ // to avoid mail
            //return true;
        }

        $CI->load->library('email_manual');

        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sendgrid.net';
        $config['smtp_user'] = 'apikey';
        $config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);

        $no_replay = (SYS_NAME == 'SPUR')? 'noreply@spur-int.com': 'noreply@gearsstd-int.com';
        $CI->email->from($no_replay, SYS_NAME);


        $CI->email->to($toEmail);
        $CI->email->subject($subject);
        $CI->email->message($CI->load->view('email_subscription_template', $mailData, true));


        $CI->email->send();
        $CI->email->clear(TRUE);
    }
}

if (!function_exists('file_type_icon')) {
    function file_type_icon($file_type)
    {
        $file_type = strtolower(trim($file_type));
        $icon = '<i class="color fa fa-file-pdf-o" aria-hidden="true"></i>';

        switch($file_type){
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

if(!function_exists('users_action')){
    function users_action($empID, $userGroupID, $name, $userName, $isDischarged){
        $str = '<div style="width: 60px; text-align: right">';
        /*if(empty($userGroupID)){
            $str .= '<button onclick="make_admin('.$empID.')" class="btn btn-default btn-xs" type="submit">Make Admin</button>';
        }
        else{
            $str .= $userGroupID;
        }
        $str .= ' &nbsp; | &nbsp; ';*/

        $str .= '<button type="button" onclick="user_setup('.$empID.', \''.$name.'\', \''. $userName.'\')"';
        $str .= ' title="Change Password" class="btn btn-default btn-xs"><i class="fa fa-key"></i></button>';
        
        if($isDischarged == 1){
            $str .= ' &nbsp; ';
            $str .= '<button type="button" onclick="undo_discharge_conf('.$empID.', this)"';
            $str .= ' title="Undo Discharge " class="btn btn-default btn-xs"><i class="fa fa-rotate-right"></i></button>';
        }
        
        $str .= '</div>';

        return $str;
    }
}

if(!function_exists('company_status_str')){
    function company_status_str($company_id, $isDisabled, $tbl){
        /*$checked = ($isDisabled == 0) ? 'checked' : '';
        return '<input type="checkbox" class="switch-chk" id="com_status_' . $company_id . '" onchange="change_company_status(' . $company_id . ')"
                    data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" data-on-color="success" data-off-text="Inactive" 
                    data-label-width="0" ' . $checked . '>';*/
        $class = $txt = '';
        switch ($isDisabled){
            case 0 :
                $class = 'success';
                $txt = 'Active';
                break;

            case 1 :
                $class = 'danger';
                $txt = 'Inactive';
                break;

            case 2 :
                $class = 'warning';
                $txt = 'On Hold';
                break;

            case 3 :
                $class = 'info';
                $txt = 'Expire';
                break;
        }
        
        $str = '<div style="text-align: center">';
        $str .= '<button type="button" class="btn btn-xs btn-'.$class.'" onclick="open_subChange_modal('.$company_id.', \''.$isDisabled.'\', \''.$tbl.'\', this)"> '.$txt.' </button>';
        $str .= '</div>';
        return $str;
    }
}

if (!function_exists('send_reminder_mail')) {
    function send_reminder_mail($remideremaildata)
    {
        $CI =& get_instance();
        if(ENVIRONMENT == 'development'){

        }

        $CI->load->library('email_manual');

        $toEmail = $remideremaildata['toEmail'];
        $subject = 'Subscription Expiry Reminder';

        $config['charset'] = "utf-8";
        $config['mailtype'] = "html";
        $config['wordwrap'] = TRUE;
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'smtp.sendgrid.net';
        $config['smtp_user'] = 'apikey';
        $config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = '587';
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $CI->load->library('email', $config);

        $no_replay = (SYS_NAME == 'SPUR')? 'noreply@spur-int.com': 'noreply@gearsstd-int.com';
        $CI->email->from($no_replay, SYS_NAME);


        $CI->email->to($toEmail);
        $CI->email->subject($subject);
        $CI->email->message($CI->load->view('email_reminder_template',$remideremaildata, true));
        $type=$CI->email->send();
        $CI->email->clear(TRUE);
        return $type;


    }
}

if (!function_exists('audit_column_arr')) {
    function audit_column_arr()
    {
        $data = get_instance()->db->select('id,tbl_name,col_name')->from('srp_erp_audit_display_columns')
                    ->get()->result_array();

        return $data;
    }
}

if (!function_exists('payActiveCompany_arr')) {
    function payActiveCompany_arr()
    {
        $data = get_instance()->db->select("company_id, company_name, company_code")
                    ->from('srp_erp_company')->where('adminType', current_userType())
                    ->get()->result_array();

        return $data;
    }
}

if (!function_exists('status_display_val')) {
    function status_display_val($val)
    {
        $val_dis = '';
        switch ($val){
            case 0: $val_dis = 'Active'; break;
            case 1: $val_dis = 'Inactive'; break;
            case 2: $val_dis = 'On Hold'; break;
        }
        return $val_dis;
    }
}

if(!function_exists('login_action')){
    function login_action($empID, $name, $userName, $noOfLoginAttempt, $isActive){
        $checked = ($noOfLoginAttempt == 4 || $isActive == 0) ? '' : 'checked';
        $str = '<input type="checkbox" class="switch-chk" id="login_status' . $empID . '" ';
        $str .= 'onchange="change_login_status(this, '.$empID.', \''.$name.'\', \''. $userName.'\')"';
        $str .= 'data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="Inactive" data-label-width="0" ' . $checked . '>';
        return  $str;
    }
}

if (!function_exists('is_QHSE_integrated')) {
    function is_QHSE_integrated($companyID)
    {
        $CI =& get_instance();
        $count = $CI->db->query("SELECT COUNT(`key`) cn FROM `keys` WHERE company_id = {$companyID} AND key_type = 'QHSE'")->row('cn');

        return ($count > 0)? 'Y': 'N';
    }
}

if(!function_exists('warehouse_action')){
    function warehouse_action($id, $wareHouse, $status){
        $checked = ($status) ? 'checked': '';
        $str = '<input type="checkbox" class="switch-chk" id="warehouseStatus' . $id . '" ';
        $str .= 'onchange="change_warehouse_status(this, '.$id.', \''.$wareHouse.'\')"';
        $str .= 'data-size="mini" data-on-text="Active" data-handle-width="45" data-off-color="danger" ';
        $str .= 'data-on-color="success" data-off-text="Inactive" data-label-width="0" ' . $checked . '>';
        return  $str;
    }
}

if(!function_exists('daysLeftForExpire')){
    function daysLeftForExpire($company_id, $isSubscriptionDisabled){
        if($isSubscriptionDisabled != 0){
            //return '';
        }

        $CI =& get_instance();
        return $CI->db->query("SELECT DATEDIFF( (sub.dueDate), CURRENT_DATE) AS daysLeft
                        FROM companysubscriptionhistory AS sub
                        LEFT JOIN (
                            SELECT inv_mas.subscriptionID, inv_mas.isAmountPaid 
                            FROM subscription_invoice_master AS inv_mas
                            JOIN subscription_invoice_details AS inv_det ON inv_mas.invID = inv_det.invID
                            WHERE inv_mas.companyID = {$company_id} AND inv_det.itemID = 1 
                        ) AS inv_mas ON sub.subscriptionID = inv_mas.subscriptionID
                        WHERE sub.companyID = {$company_id}
                        AND IF (inv_mas.isAmountPaid IS NULL, 0, inv_mas.isAmountPaid  ) = 0
                        ORDER BY sub.subscriptionID ASC LIMIT 1")->row('daysLeft');
    }
}

if (!function_exists('required_mark')) {
    function required_mark()
    {
        $tmp = '<span title="required field" style="color:red; font-weight: 600; font-size: 12px;">*</span>';
        echo $tmp;
    }
}

if (!function_exists('PBS_contract_api_requests')) {
    function PBS_contract_api_requests()
    {
        $site_url = PBS_CONTRACT_API;
        if (empty($site_url)) {
            return ['status' => 'e', 'message' => 'PBS contract API url not configured.'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $site_url);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json'] );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'get');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $msg = "<br/>" . curl_error($ch);
            return ['status' => 'e', 'message' => $msg, 'http_code' => $http_code];
        }
        curl_close($ch);

        if (!in_array($http_code, ['200'])) {
            return ['status' => 'e', 'message' => '<br/>Some thing went wrong in getting PBS contracts,<b>Please contact for system support.</b>', 'http_code' => $http_code];
        }

        $response = json_decode($response);
        if (!is_object($response)) {
            return ['status' => 'e', 'message' => '<br/>Some thing went wrong in getting PBS contracts,<b>Please contact for system support.</b>', 'http_code' => $http_code];
        }

        $status = ($response->success != false) ? 's' : 'e';
        $rt_data = [
            'status' => $status,
            'message' => $response->message,
            'http_code' => $http_code
        ];

        if ($status == 's') {
            if (property_exists($response, 'data')) {
                $rt_data['data'] = $response->data;
            }
        }

        return $rt_data;
    }
}

if (!function_exists('user_type_table')) {
    function user_type_table($emp_id, $type)
    {        
        $is_basic_user = ($type == 0)? 'selected': '';
        $is_module_user = ($type == 1)? 'selected': '';

        $str = '<select name="userType" class="form-control user_type_drop" id="userType_'.$emp_id.'" onchange="update_userType(this,'.$emp_id.')">';        
        $str .= '<option value="0" '.$is_basic_user.'>Basic User</option>';
        $str .= '<option value="1" '.$is_module_user.'>Module User</option></select>';
        
        return $str; 
    }
}

if (!function_exists('company_type')) {
    function company_type($placeHolder = '')
    {                
        $types_arr = get_instance()->db->order_by('description')
                            ->get('system_company_type')->result_array();
        
        $arr = [];
        if($placeHolder){
            $arr = [0 => $placeHolder];
        }
        
        foreach($types_arr as $val){
            $arr[ $val['id'] ] = $val['description'];
        }

        return $arr;
    }
}

if (!function_exists('payment_type')) {
    function payment_type($in=[], $placeHolder = '')
    {                
        $ci =& get_instance();

        $ci->db->order_by('pay_description');
        if($in){
            $ci->db->where_in('id', $in);
        }
        $types_arr = $ci->db->get('system_payment_types')->result_array();
        
        $arr = ($placeHolder)? ['' => $placeHolder]: [];        
        foreach($types_arr as $val){
            $arr[ $val['id'] ] = $val['pay_description'];
        }

        return $arr;
    }
}

if (!function_exists('payLogStr')) {
    function payLogStr($logID, $input)
    {                 
        $input2 = json_decode($input);

        if( is_object($input2) ){
            $str = '<span class="label-invoice" onclick="logDecode('.$logID.')" title="more">'.substr($input, 0, 100).'</span>';
        }
        else{
            $str = $input;
        }

        return $str;
    }
}

if (!function_exists('get_navigation_modules')) {
    function get_navigation_modules($param=[])
    {                
        $ci =& get_instance();
        $in = (array_key_exists('in', $param))? $param['in']: null;
        $placeHolder = (array_key_exists('placeHolder', $param))? $param['placeHolder']: null;
        $isDrop = (array_key_exists('isDrop', $param))? $param['isDrop']: null;

        if($in){
            $ci->db->where_in('id', $param['in']);
        }

        $types_arr = $ci->db->order_by('description')->get('navigation_modules')->result_array();
        
        if($isDrop){
            return $types_arr;            
        }
         
        $arr = ($placeHolder)? ['' => $placeHolder]: [];        
        foreach($types_arr as $val){
            $arr[ $val['moduleID'] ] = $val['description'];
        }

        return $arr;
    }
}

if (!function_exists('array_group_by')) {
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
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);

            return NULL;
        }
        $func = (is_callable($key) ? $key : NULL);
        $_key = $key;
        // Load the new array, splitting by the target key
        $grouped = [];
        foreach ($array as $value) {
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            } else {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $params);
            }
        }

        return $grouped;
    }
}

if (!function_exists('product_list')) {
    function product_list($param=[]){
        $placeHolder = (array_key_exists('placeHolder', $param))? $param['placeHolder']: null;

        $prod_arr = get_instance()->db->select('id, description')->get('product_master')->result_array();

        $arr = ($placeHolder)? ['' => $placeHolder]: [];        
        foreach($prod_arr as $val){
            $arr[ $val['id'] ] = $val['description'];
        }

        if(array_key_exists('isCustom', $param)){
            $arr[-1] = 'Custom';
        }
        
        return $arr;
    }
}

if (!function_exists('documentCode_drop')) {
    function documentCode_drop($param=[]){
        $placeHolder = (array_key_exists('placeHolder', $param))? $param['placeHolder']: null;

        $doc_arr = get_instance()->db->select('documentAutoID, documentID, document')
                        ->get('srp_erp_documentcodes')->result_array();

        $arr = ($placeHolder)? ['' => $placeHolder]: [];        
        foreach($doc_arr as $val){
            $arr[ $val['documentID'] ] = $val['documentID'].' | '.$val['document'];
        }
        
        return $arr;
    }
}

if (!function_exists('decryptData')) {
    function decryptData($data){
        return get_instance()->encryption->decrypt($data);        
    }
}

if (!function_exists('get_clientDB')) {
    function get_clientDB($only=[]){
        $ci = get_instance();
        $where_in = $ci->config->item('company_id_in');
        $where_not_in = $ci->config->item('company_id_not_in');

        $ci->db->select('host,db_name,db_password,db_username');
        if( $where_in ){        
            $ci->db->where_in('company_id', $where_in); 
        }
        
        if( $only ){        
            $ci->db->where_in('company_id', $only); 
        }
        
        if( $where_not_in ){        
            $ci->db->where_not_in('company_id', $where_not_in); 
        }
        $arr = $ci->db->get('srp_erp_company')->result_array();
        
        return $arr;
    }
}

if (!function_exists('readableBytes')) {
    function readableBytes($bytes) {
        //https://ourcodeworld.com/articles/read/718/converting-bytes-to-human-readable-values-kb-mb-gb-tb-pb-eb-zb-yb-with-php

        if($bytes == 0 || empty($bytes)){
            return 0;
        }

        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }
}

//set class if finance period is closed AND set class if finance period is current
if (!function_exists('set_is_closed_is_current_class')) {
    function set_is_closed_is_current_class($isClosed,$isCurrent)
    {
        $status = '';
        if ($isClosed == 1 && $isCurrent==1) {
            $status = 'Closed Current';
        }else if($isClosed == 1 && $isCurrent==0){
            $status = 'Closed';
        }else if($isClosed == 0 && $isCurrent==1){
            $status = 'Current';
        }else{
            $status = 'noClosed';
        }

        return $status;
    }
}

if (!function_exists('flushStatus_lable')) {
    function flushStatus_lable($status)
    {
        $lbl = 'warning';        
        $str = '<div style="text-align: center">';        
        
        switch ($status) {
            case 1:
                $lbl = 'success';
                $status = 'Processed';
            break;

            case 2:
                $lbl = 'danger';
                $status = 'Failed';
            break; 

            default:
                $status = 'Not run';
        }

        $str .= '<label class="label label-'.$lbl.'">'.$status.'</label>';
        $str .= '</div>';
        return $str;
    }
}

if (!function_exists('date_to_id_conv')) {
    function date_to_id_conv($date)
    {
        return date('YmdHis', strtotime($date));
    }
}

if (!function_exists('list_comnpay')) {
    function list_comnpay($id_list)
    {
        $ci = get_instance(); 

        if(empty($id_list)){
            return '';
        }
        
        $id_list = explode(',', $id_list);
        $ci->db->select('company_name')->where_in('company_id', $id_list);
        $arr = $ci->db->get('srp_erp_company')->result_array();
        
        return '- '.join('<br/> - ', array_column($arr, 'company_name'));
    }
}
