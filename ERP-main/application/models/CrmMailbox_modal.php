<?php

class CrmMailbox_modal extends ERP_Model
{
    function save_reply_mail()
    {
        $toemailcrmmailbox = trim($this->input->post('to') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $companyCode = current_companyCode();
        $currentuserid = current_userID();
        $companyid = current_companyID();
        $empemail = $this->db->query("SELECT EEmail FROM `srp_employeesdetails` where Erp_companyID = '{$companyid}' And EIdNo = '{$currentuserid}'")->row_array();
        $crm_contactmail = $this->db->query(" SELECT email FROM `srp_erp_crm_contactmaster` where  companyID = '{$companyid}'  And email = '{$toemailcrmmailbox}' ")->row_array();

        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password = base64_decode($activeemaildet['password']);


        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] = $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;

        $config['folders'] = 'INBOX';
        $config['expunge_on_disconnect'] = false;
        $config['cache'] = [
            'active' => true,
            'adapter' => 'file',
            'backup' => 'file',
            'key_prefix' => 'imap:',
            'ttl' => 60,
        ];
        $this->imap->connect($config);
        $folders = $this->imap->get_message_read($this->input->post('uid'));
        $mailData = [
            'approvalEmpID' => '',
            'documentCode' => '',
            'toEmail' => $toemailcrmmailbox,
            'subject' => $Subject,
            'from' => $empemail['EEmail'],
            'message' => $message . '<br><hr><br>' . $folders['body']['html']
        ];

        if (!empty($crm_contactmail)) {
            $data['emailConfigID'] = 1;
            $data['fromEmailAddress'] = $empemail['EEmail'];
            $data['toEmailAddress'] = $toemailcrmmailbox;
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message . '<br><hr><br>' . $folders['body']['html'];
            $data['mailType'] = 2;
            $data['uid'] = $this->input->post('uid');
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
                return array('s', 'Email Send Successfully.');
                send_Email_crm_mailbox($mailData, 1);
            }
        } else {
            send_Email_crm_mailbox($mailData, 1);
        }

    }

    function save_mailbox_configurations()
    {
        $email = trim($this->input->post('usernameemail') ?? '');
        $password = trim($this->input->post('password') ?? '');
        $encryptovalue = trim($this->input->post('encryptovalue') ?? '');
        $encryptoID = trim($this->input->post('encrypto') ?? '');
        $accounttype = trim($this->input->post('accounttype') ?? '');
        $accounttypevalue = trim($this->input->post('accounttypevalue') ?? '');
        $host = trim($this->input->post('host') ?? '');
        $port = trim($this->input->post('port') ?? '');
        $emailconfigid = trim($this->input->post('emailconfigid') ?? '');


        $data['empID'] = current_userID();
        $data['encryptoID'] = $encryptoID;
        $data['encrypto'] = $encryptovalue;
        $data['accountTypeID'] = $accounttype;
        $data['accountType'] = $accounttypevalue;
        $data['host'] = $host;
        $data['port'] = $port;

        $emialDomain =  explode("@",$email);
        $domain = explode(".",$emialDomain[1]);
        if($domain[0] == 'gmail')
        {
            $data['username'] = 'recent:'.$email;
        }else
        {
            $data['username'] = $email;
        }
        $data['displayUserName'] = $email;
        $data['isDefault'] = 1;
        $data['password'] = base64_encode($password);
      if($emailconfigid)
      {
          $this->db->where('emailConfigID', $emailconfigid);
          $this->db->update('srp_erp_crm_emailconfiguration', $data);


      }else
      {

          $data['companyID'] = current_companyID();
          $data['companyCode'] = $this->common_data['company_data']['company_code'];
          $data['createdUserGroup'] = $this->common_data['user_group'];
          $data['createdPCID'] = current_pc();
          $data['createdUserID'] = $this->common_data['current_userID'];
          $data['createdDateTime'] = current_date();
          $data['createdUserName'] = $this->common_data['current_user'];

          $this->db->insert('srp_erp_crm_emailconfiguration', $data);

      }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Mail Box Configuration Saved failed.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Mail Box Configuration Saved Sucessfully.');
        }




    }

    function save_compose_email()
    {
        $toemailcrmmailbox = trim($this->input->post('to') ?? '');
        $bbcemailcrmmailbox = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        $crm_contactmail = $this->db->query(" SELECT email FROM `srp_erp_crm_contactmaster` where  companyID = '{$companyid}'  And email = '{$toemailcrmmailbox}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('upload[]');
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');


            $mailData = [
                'approvalEmpID' => '',
                'documentCode' => '',
                'toEmail' => $toemailcrmmailbox,
                'ccEmail' => $ccemailcrmmailbox,
                'bccEmail' => $bbcemailcrmmailbox,
                'subject' => $Subject,
                'from' => $activeemaildet['displayUserName'],
                'message' => $message
            ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['username'];
            $data['toEmailAddress'] = $toemailcrmmailbox;
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['ccEmail'] = $toemailcrmmailbox;
            $data['bccEmail'] = $bbcemailcrmmailbox;
            $data['mailType'] = 1;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['upload']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['upload']['name']); $i++) {

                    $_FILES['upload']['name']= $files['upload']['name'][$i];
                    $_FILES['upload']['type']= $files['upload']['type'][$i];
                    $_FILES['upload']['tmp_name']= $files['upload']['tmp_name'][$i];
                    $_FILES['upload']['error']= $files['upload']['error'][$i];
                    $_FILES['upload']['size']= $files['upload']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['upload']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('upload')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }

    }



    function save_compose_email_contact()
    {
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');
        $bccemailcrmmailbox = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $contactid = trim($this->input->post('contactid') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        $crm_contactmail = $this->db->query(" SELECT email FROM `srp_erp_crm_contactmaster` where  companyID = '{$companyid}'  And contactID = '{$contactid}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('photo[]');


     $mailData = [
               'approvalEmpID' => '',
               'documentCode' => '',
               'toEmail' => $crm_contactmail['email'],
               'ccEmail' => $ccemailcrmmailbox,
               'bccEmail' => $bccemailcrmmailbox,
               'subject' => $Subject,
               'from' =>  $activeemaildet['displayUserName'],
               'message' => $message
           ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['displayUserName'];
            $data['toEmailAddress'] = $crm_contactmail['email'];
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['ccEmail'] = $ccemailcrmmailbox;
            $data['bccEmail'] = $bccemailcrmmailbox;
            $data['mailType'] = 1;
            $data['documentID'] = 6;
            $data['masterAutoID'] = $contactid;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp']  = format_date_mysql_datetime();
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['photo']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['photo']['name']); $i++) {

                    $_FILES['photo']['name']= $files['photo']['name'][$i];
                    $_FILES['photo']['type']= $files['photo']['type'][$i];
                    $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$i];
                    $_FILES['photo']['error']= $files['photo']['error'][$i];
                    $_FILES['photo']['size']= $files['photo']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['photo']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('photo')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose Contact',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }

    }
    function save_compose_email_organization()
    {
        $toemailcrmmailbox = trim($this->input->post('to') ?? '');
        $bccEmail = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $organizationid = trim($this->input->post('organizationid') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        $crm_organization = $this->db->query(" SELECT email FROM `srp_erp_crm_organizations` where companyID = '{$companyid}' And organizationID =  '{$organizationid}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('photo[]');
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');



            $mailData = [
                'approvalEmpID' => '',
                'documentCode' => '',
                'toEmail' => $crm_organization['email'],
                'ccEmail' => $ccemailcrmmailbox,
                'bccEmail' => $bccEmail,
                'subject' => $Subject,
                'from' => $activeemaildet['displayUserName'],
                'message' => $message
            ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['displayUserName'];
            $data['toEmailAddress'] = $crm_organization['email'];
            $data['bccEmail'] = $bccEmail;
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['mailType'] = 1;
            $data['ccEmail'] = $ccemailcrmmailbox;
            $data['documentID'] = 8;
            $data['masterAutoID'] = $organizationid;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp']  = format_date_mysql_datetime();
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['photo']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['photo']['name']); $i++) {

                    $_FILES['photo']['name']= $files['photo']['name'][$i];
                    $_FILES['photo']['type']= $files['photo']['type'][$i];
                    $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$i];
                    $_FILES['photo']['error']= $files['photo']['error'][$i];
                    $_FILES['photo']['size']= $files['photo']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['photo']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('photo')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose Contact',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }
    }
    function load_mailbox_configuretion()
    {
        $companyid = current_companyID();
        $empid = current_userID();
        $data['details'] = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` where companyID = '{$companyid}' AND isDefault = 1 AND empID = '{$empid}' ")->row_array();
        $data['passworddecode'] = base64_decode($data['details']['password']);
        return $data;
    }


    function save_compose_email_lead()
    {
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');
        $bccemailcrmmailbox = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $leadID = trim($this->input->post('leadid') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        $crm_contactmail = $this->db->query("SELECT email FROM `srp_erp_crm_leadmaster` where  companyID = '{$companyid}'  And leadID = '{$leadID}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('photo[]');


        $mailData = [
            'approvalEmpID' => '',
            'documentCode' => '',
            'toEmail' => $crm_contactmail['email'],
            'ccEmail' => $ccemailcrmmailbox,
            'bccEmail' => $bccemailcrmmailbox,
            'subject' => $Subject,
            'from' => $activeemaildet['displayUserName'],
            'message' => $message
        ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['displayUserName'];
            $data['toEmailAddress'] = $crm_contactmail['email'];
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['ccEmail'] = $ccemailcrmmailbox;
            $data['bccEmail'] = $bccemailcrmmailbox;
            $data['mailType'] = 1;
            $data['documentID'] = 5;
            $data['masterAutoID'] = $leadID;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp']  = format_date_mysql_datetime();
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['photo']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['photo']['name']); $i++) {

                    $_FILES['photo']['name']= $files['photo']['name'][$i];
                    $_FILES['photo']['type']= $files['photo']['type'][$i];
                    $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$i];
                    $_FILES['photo']['error']= $files['photo']['error'][$i];
                    $_FILES['photo']['size']= $files['photo']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['photo']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('photo')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose Contact',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }

    }
    function save_compose_email_opportunitie()
    {
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');
        $to = trim($this->input->post('To') ?? '');
        $bccemailcrmmailbox = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $opportunitieid = trim($this->input->post('opportunitieid') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        //$crm_contactmail = $this->db->query("SELECT email FROM `srp_erp_crm_leadmaster` where  companyID = '{$companyid}'  And leadID = '{$leadID}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('photo[]');


        $mailData = [
            'approvalEmpID' => '',
            'documentCode' => '',
            'toEmail' => $to,
            'ccEmail' => $ccemailcrmmailbox,
            'bccEmail' => $bccemailcrmmailbox,
            'subject' => $Subject,
            'from' => $activeemaildet['displayUserName'],
            'message' => $message
        ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['displayUserName'];
            $data['toEmailAddress'] = $to;
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['ccEmail'] = $ccemailcrmmailbox;
            $data['bccEmail'] = $bccemailcrmmailbox;
            $data['mailType'] = 1;
            $data['documentID'] = 4;
            $data['masterAutoID'] = $opportunitieid;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp']  = format_date_mysql_datetime();
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['photo']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['photo']['name']); $i++) {

                    $_FILES['photo']['name']= $files['photo']['name'][$i];
                    $_FILES['photo']['type']= $files['photo']['type'][$i];
                    $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$i];
                    $_FILES['photo']['error']= $files['photo']['error'][$i];
                    $_FILES['photo']['size']= $files['photo']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['photo']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('photo')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose Contact',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }

    }
    function save_compose_email_project()
    {
        $ccemailcrmmailbox = trim($this->input->post('cc') ?? '');
        $to = trim($this->input->post('To') ?? '');
        $bccemailcrmmailbox = trim($this->input->post('Bcc') ?? '');
        $message = trim($this->input->post('compose-textarea') ?? '');
        $Subject = trim($this->input->post('Subject') ?? '');
        $projectid = trim($this->input->post('projectid') ?? '');
        $companyCode = current_companyCode();
        $companyid = current_companyID();
        //$crm_contactmail = $this->db->query("SELECT email FROM `srp_erp_crm_leadmaster` where  companyID = '{$companyid}'  And leadID = '{$leadID}' ")->row_array();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $photo = $this->input->post('photo[]');


        $mailData = [
            'approvalEmpID' => '',
            'documentCode' => '',
            'toEmail' => $to,
            'ccEmail' => $ccemailcrmmailbox,
            'bccEmail' => $bccemailcrmmailbox,
            'subject' => $Subject,
            'from' => $activeemaildet['displayUserName'],
            'message' => $message
        ];


        if (!empty($activeemaildet)) {
            $data['emailConfigID'] = $activeemaildet['emailConfigID'];
            $data['fromEmailAddress'] = $activeemaildet['displayUserName'];
            $data['toEmailAddress'] = $to;
            $data['emailSubject'] = $Subject;
            $data['emailBody'] = $message;
            $data['ccEmail'] = $ccemailcrmmailbox;
            $data['bccEmail'] = $bccemailcrmmailbox;
            $data['mailType'] = 1;
            $data['documentID'] = 9;
            $data['masterAutoID'] = $projectid;
            $data['companyID'] = $companyid;
            $data['companyCode'] = $companyCode;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['timestamp']  = format_date_mysql_datetime();
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_emails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();

            $files = $_FILES;
            $images = array();

            if ($files['photo']['name'][0] != "") {

                $this->load->library('upload');
                $path = "attachments/crm/Crm_mailbox/crm_mailbox_attachments";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/crm", 777);
                    mkdir("attachments/crm/Crm_mailbox/crm_mailbox_attachments", 777);
                }
                $config['upload_path'] = $path;
                $config['allowed_types'] = '*';
                $config['max_size'] = '200000';


                for ($i = 0; $i < count($files['photo']['name']); $i++) {

                    $_FILES['photo']['name']= $files['photo']['name'][$i];
                    $_FILES['photo']['type']= $files['photo']['type'][$i];
                    $_FILES['photo']['tmp_name']= $files['photo']['tmp_name'][$i];
                    $_FILES['photo']['error']= $files['photo']['error'][$i];
                    $_FILES['photo']['size']= $files['photo']['size'][$i];

                    $journalName = str_replace(' ', '_',  $_FILES['photo']['name']);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('photo')) {

                        return array('e', 'Upload failed ' . $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();
                        $dataupload = array(
                            'emailID' => $last_id,
                            'attachmentDescription' => 'Email Compose Contact',
                            'myFileName' => $journalName,
                            'timestamp' => date('Y-m-d H:i:s'),
                            'fileType' => trim($upload_data["file_ext"]),
                            'fileSize' => trim($upload_data["file_size"]),
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'createdUserGroup' => $this->common_data['user_group'],
                            'modifiedPCID' => $this->common_data['current_pc'],
                            'modifiedUserID' => $this->common_data['current_userID'],
                            'modifiedUserName' => $this->common_data['current_user'],
                            'modifiedDateTime' => $this->common_data['current_date'],
                            'createdPCID' => $this->common_data['current_pc'],
                            'createdUserID' => $this->common_data['current_userID'],
                            'createdUserName' => $this->common_data['current_user'],
                            'createdDateTime' => $this->common_data['current_date'],
                        );
                        $this->db->insert('srp_erp_crm_emailattachments', $dataupload);
                    }
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData, 1,$last_id);
                    return array('s', 'Email Send Successfully.');
                }
            }else
            {

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Email Send Failed.');

                } else {
                    $this->db->trans_commit();
                    send_Email_crm_mailbox($mailData);
                    return array('s', 'Email Send Successfully.');
                }

            }






        }

    }

}