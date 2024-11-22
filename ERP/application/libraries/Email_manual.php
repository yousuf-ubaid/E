<?php

use App\Event\EmailEvent;
use App\Exception\ServiceUnavailableException;

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_manual
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
        $this->CI->load->library('EventService');
    }

    function set_email_detail($params)
    {
        $data = array();
  
        if (!empty($params)) {

            foreach ($params as $key => $val) {

                $data[$key]["companyID"]= $val["companyID"];
                $data[$key]["documentID"]= $val["documentID"] ?? '';
                $data[$key]["documentSystemCode"]= $val["documentSystemCode"];
                $data[$key]["documentCode"]= $val["documentCode"];
                $data[$key]["emailSubject"]= $val["emailSubject"];
                $data[$key]["empEmail"]= $val["empEmail"];
                $data[$key]["empID"]= $val["empID"];
                $data[$key]["empName"]= $val["empName"];
                $data[$key]["type"]= $val["type"];
                if($val['type'] == 'approvals'){
                    $data[$key]["emailBody"] = $val['emailBody'] = base64_encode($this->CI->load->view('system/email_template/email_approval_template_log', $val, TRUE));
                }else{
                    $data[$key]["emailBody"] = $val['emailBody'] = base64_encode($val['emailBody']);
                }
             
                $data[$key]["timeStamp"]= $this->CI->common_data['current_date'];

                //Send email to the service
                $emailPolicy = getPolicyValues('SEN', 'All');
                if($emailPolicy == 1){
                    $this->sendMail($val, $data, $key);
                } else {
                    $data[$key]["sendResponse"] = 'Email Policy is Off';
                    $data[$key]["isEmailSend"] = 0;
                }
              
            }

            $this->CI->db->insert_batch('srp_erp_alert', $data);

        }
        return true;
    }

    function set_email_forgot_email($params){

        $data = array();
  
        if (!empty($params)) {

            foreach ($params as $key => $val) {

                $data[$key]["companyID"]= $val["companyID"];
                $data[$key]["emailSubject"]= $val["emailSubject"];
                $data[$key]["empEmail"]= $val["empEmail"];
                $data[$key]["empID"]= $val["empID"];
                $data[$key]["empName"]= $val["empName"];
                $data[$key]["type"]= $val["type"];
                $data[$key]["emailBody"] = $val['emailBody'] = base64_encode($this->CI->load->view('system/email_template/email_template', $val, TRUE));
                $data[$key]["timeStamp"]= $this->CI->common_data['current_date'] ?? date('Y-m-d H:i:s');

                $this->sendMail($val, $data, $key);
              
            }

           $this->CI->db->insert_batch('srp_erp_alert', $data);

        }
        return true;
    }

    function send_emails_to_service($data): void
    {
        $this->CI->eventservice->dispatch($this->getBody($data));
    }

    //Send email body
    function getBody($data): EmailEvent
    {
        $companyID = $data['companyID'];
        $email_token = null;

        if($data['type'] != 'ForgotPassword'){
            if($companyID){
                $company_detail = $this->CI->db->where('company_id',$companyID)->from('srp_erp_company')->get()->row_array();
    
                if($company_detail){
                    $email_token = $company_detail['email_token'];
                }
            }
        }else{
            $email_token = $this->CI->config->item('email_token');
        }

        return new EmailEvent(
            $data['empEmail'],
            $data['emailSubject'],
            $data['emailSubject'],
            $data['emailBody'],
            $email_token ?? ''
        );
    }

    /**
     * Send mail to service
     *
     * @param array $val
     * @param array $data
     * @param int|string $key
     * @return void
     */
    private function sendMail(array $val, array &$data, int|string $key): void
    {
        try {
            $this->send_emails_to_service($val);

            $data[$key]["sendResponse"] = 'Success';
            $data[$key]["sendResponseCode"] = 200;
            $data[$key]["isEmailSend"] = 1;

        } catch (ServiceUnavailableException $e) {
            $data[$key]["sendResponse"] = $e->getMessage();
        }
    }

}