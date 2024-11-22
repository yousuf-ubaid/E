<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
-- =============================================
-- File Name : Email_manual.php
-- Project Name : SME ERP
-- Module Name : Email
-- Author : Mohamed Mubashir
-- Create date : 20 - December 2016
-- Description : This file contains sending email to reciepient.

-- REVISION HISTORY
-- =============================================*/
class Email_manual
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
    }

    //array("companyID" => null,"empID" => array(),"documentID" => null,"documentSystemCode" => null,"documentCode" => null,"empName" => array(), "empEmail" => array(),"ccEmailID" => null, "emailSubject" => null,"emailBody" => null)
    function set_email_detail($params)
    {
        $data = array();
        if (is_array($params["empID"]) && is_array($params["empName"]) && is_array($params["empEmail"])) {
            foreach ($params["empEmail"] as $key => $val) {
                $data["companyID"] = $params["companyID"];
                $data["documentID"] = $params["documentID"];
                $data["documentSystemCode"] = $params["documentSystemCode"];
                $data["documentCode"] = $params["documentCode"];
                $data["emailSubject"] = $params["emailSubject"];
                $data["empEmail"] = $params["empEmail"][$key];
                $data["empID"] = $params["empID"][$key];
                $data["empName"] = $params["empName"][$key];
                $data["emailBody"] = $params["emailBody"];
            }
        }else{
            $data["companyID"] = $params["companyID"];
            $data["documentID"] = $params["documentID"];
            $data["documentSystemCode"] = $params["documentSystemCode"];
            $data["documentCode"] = $params["documentCode"];
            $data["emailSubject"] = $params["emailSubject"];
            $data["empEmail"] = $params["empEmail"];
            $data["empID"] = $params["empID"];
            $data["empName"] = $params["empName"];
            $data["emailBody"] = $params["emailBody"];
        }

        $result = $this->CI->db->insert_batch('srp_erp_alert', $data);
        if ($result) {
            return array('status' => true);
        } else {
            return array('status' => false);
        }
    }
}