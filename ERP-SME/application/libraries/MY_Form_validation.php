<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Form_validation extends CI_Form_validation {

    protected $CI;
    function __construct($rules = [])
    {
        parent::__construct($rules);
        $this->CI =& get_instance();
        $this->_config_rules = $rules;
    }

    /**
     * Validate float
     *
     * @param mixed $value
     * @return bool
     */
    public function validate_numeric(mixed $value): bool
    {
        $validData = is_numeric($value);
        if(!$validData){
            $this->CI->form_validation->set_message('validate_numeric', 'The {field} field must be a valid number.');
        }

        return $validData;
    }
}

