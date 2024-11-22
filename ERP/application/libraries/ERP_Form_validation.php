<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class ERP_Form_validation extends CI_Form_validation {

    protected $CI;
    function __construct($rules = array())
    {
        parent::__construct($rules);
        $this->CI =& get_instance();
        $this->_config_rules = $rules;
    }

    function validate_date($date): bool
    {
        $DateFormate = convert_date_format();
        $date_format_policy = date_format_policy();
        $convertedDate = input_format_date($date, $date_format_policy);
        $d = DateTime::createFromFormat($DateFormate, $convertedDate);
        if ($convertedDate == "1970-01-01") {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function validate_datetime($date): bool
    {
        $date_format_policy = date_format_policy();
        $convertedDate = input_format_date($date, $date_format_policy, 'Y-m-d h:i A');
    
        if ($convertedDate == "1970-01-01 05:30 AM") {
            $this->CI->form_validation->set_message('validate_datetime', 'The {field} is invalid');
            return FALSE;
        } else {
            return TRUE;
        }
    }
}

