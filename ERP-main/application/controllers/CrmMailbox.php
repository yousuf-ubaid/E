<?php

class CrmMailbox extends ERP_Controller
{

    protected $connection;

    function __construct()
    {
        parent::__construct();
        $this->load->library('imap');
        $this->load->library('pagination');
        $this->load->helper('crm');
        $this->load->model('CrmMailbox_modal');
    }

    function getInboxMails(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password =  base64_decode($activeemaildet['password']);
        $data_pagination = $this->input->post('pageID');
        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] =  $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;
        $config['folders'] = $this->input->post('folder');
        $config['expunge_on_disconnect'] = false;
        $config['cache'] = [
            'active' => true,
            'adapter' => 'file',
            'backup' => 'file',
            'key_prefix' => 'imap:',
            'ttl' => 60,
        ];



        /*$obj = $this->load->library('pop3',[$config['host'],$config['username'],$password,$config['port'],true]);
        $this->pop3->getEmails();
        exit($obj);*/


       $conn = $this->imap->connect($config);
        $totalCount = $this->imap->count_messages($this->input->post('folder'));

        $folders = range(0,$totalCount);
        $folders = array_reverse($folders);

        $per_page = 10;
        /*$perpagecount = $totalCount / $this->input->post('pageID');
        if($perpagecount < 10)
        {
            $per_page = 9;
        }*/
        $config = array();
        $config["base_url"] = "#inbox-list";
        $config["total_rows"] =  $totalCount;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;


        /* var_dump( array_slice($folders, $per_page * $this->input->post('pageID') - $per_page, $per_page));
         exit();*/
        /*  print_r($folders);
           exit();*/

        /*$folders = $this->imap->search();
        echo var_dump($folders);
        exit;*/
        $folders = $this->imap->paginate($folders,$this->input->post('pageID'));
        $emails['emails'] = $folders;
        $this->pagination->initialize($config);
        $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
        $dataCount = count($folders);

        $data["empCount"] = $totalCount;
        $data["pagination"] = $this->pagination->create_links_employee_master();
        $data["per_page"] = $per_page;
        $thisPageStartNumber = ($page+1);
        $thisPageEndNumber = $page+$dataCount;
        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['emailsucessconfig'] = $emails['emails'][0]['uid'];

        $emails['emailconfigured'] = 1;
        if($conn)
        {
            $dataupdate['successYN'] = 1;
            $dataupdate['companyID'] = $companyid;
            $this->db->where('emailConfigID', $activeemaildet['emailConfigID']);
            $this->db->update('srp_erp_crm_emailconfiguration', $dataupdate);
            $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox', $emails, true);
        }else
        {
            $dataupdate['successYN'] = 0;
            $dataupdate['companyID'] = $companyid;
            $this->db->where('emailConfigID', $activeemaildet['emailConfigID']);
            $this->db->update('srp_erp_crm_emailconfiguration', $dataupdate);
        }
        echo json_encode($data);
    }



    function getReadMail(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password =  base64_decode($activeemaildet['password']);
        $data_pagination = $this->input->post('pageID');
        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] =  $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;
        $config['folders'] = $this->input->post('folder');
        $config['expunge_on_disconnect'] = false;
        $config['cache'] = [
            'active' => true,
            'adapter' => 'file',
            'backup' => 'file',
            'key_prefix' => 'imap:',
            'ttl' => 60,
        ];

        $this->imap->connect($config);
        $folders = $this->imap->get_message($this->input->post('uid'));
        $emails['body'] = $folders['body']['html'];
        $emails['from'] = $folders['from'];
        $emails['subject'] = $folders['subject'];
        $emails['attachments'] = $folders['downloaded_attachments'];
        $unixTimestamp=strtotime($folders['date']);
        $unixTimestamp = date("m/d/Y", $unixTimestamp);
        $emails['date'] = $unixTimestamp;
        $this->load->view('system/crm/ajax/crm_read_mail', $emails);
    }

    function getReadMailDetail(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password =  base64_decode($activeemaildet['password']);

        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] =  $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;
        $config['folders'] = 'INBOX';
        /*$config['expunge_on_disconnect'] = false;
        $config['cache'] = [
            'active' => true,
            'adapter' => 'file',
            'backup' => 'file',
            'key_prefix' => 'imap:',
            'ttl' => 60,
        ];*/

        $this->imap->connect($config);
        $folders = $this->imap->get_message_read($this->input->post('uid'));
        $emails['from'] = $folders['from'];
        $emails['subject'] = $folders['subject'];
        $unixTimestamp=strtotime($folders['date']);
        $unixTimestamp = date("m/d/Y", $unixTimestamp);
        $emails['date'] = $unixTimestamp;
        echo json_encode($emails);
    }

    function getReplyMail(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password =  base64_decode($activeemaildet['password']);

        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] =  $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;
        $config['folders'] = 'INBOX';
      /*  $config['expunge_on_disconnect'] = false;*/
       /* $config['cache'] = [
            'active' => true,
            'adapter' => 'file',
            'backup' => 'file',
            'key_prefix' => 'imap:',
            'ttl' => 60,
        ];*/

        $this->imap->connect($config);
        $folders = $this->imap->get_message_read($this->input->post('uid'));
        $from = $folders['from'];
        $cc = $folders['cc'];
        $bcc = $folders['bcc'];
        $ccArray = [];
        $bccArray = [];
        if($cc){
            foreach ($cc as $val){
                $ccArray[] = $val['email'];
            }
        }
        if($bcc){
            foreach ($bcc as $val){
                $bccArray[] = $val['email'];
            }
        }
        $data = $folders;
        $data['from'] = $from['email'];
        $data['cc'] =  implode(';',$ccArray);
        $data['bcc'] =  implode(';',$bccArray);
        $html = $this->load->view('system/crm/ajax/crm_reply_mail',$data,true);
        echo json_encode($html);
    }

    function save_reply_mail(){
        $this->form_validation->set_rules('to', ' To Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CrmMailbox_modal->save_reply_mail());
        }

    }
    function save_mailbox_configurations()
    {
        $this->form_validation->set_rules('usernameemail', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('host', 'Host', 'trim|required');
        $this->form_validation->set_rules('port', 'Port', 'trim|required');
        $this->form_validation->set_rules('encrypto', 'Email Encryption', 'trim|required');
        $this->form_validation->set_rules('accounttype', 'Account Type', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CrmMailbox_modal->save_mailbox_configurations());
        }
    }
    function composeMail(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $activeemaildet = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` WHERE companyID = '{$companyid}' And empID = '{$currentuserid}' And isDefault = 1 ")->row_array();
        $password =  base64_decode($activeemaildet['password']);

        $config['encrypto'] = $activeemaildet['encrypto'];
        $config['accountType'] = $activeemaildet['accountType'];
        //$config['validate'] = true;true;
        $config['host'] =  $activeemaildet['host'];
        $config['port'] = $activeemaildet['port'];
        $config['username'] = $activeemaildet['username'];
        $config['password'] = $password;
        $config['folders'] = 'INBOX';
        /*  $config['expunge_on_disconnect'] = false;*/
        /* $config['cache'] = [
             'active' => true,
             'adapter' => 'file',
             'backup' => 'file',
             'key_prefix' => 'imap:',
             'ttl' => 60,
         ];*/

        $this->imap->connect($config);
        $folders = $this->imap->get_message_read($this->input->post('uid'));
        $from = $folders['from'];
        $cc = $folders['cc'];
        $bcc = $folders['bcc'];
        $ccArray = [];
        $bccArray = [];
        if($cc){
            foreach ($cc as $val){
                $ccArray[] = $val['email'];
            }
        }
        if($bcc){
            foreach ($bcc as $val){
                $bccArray[] = $val['email'];
            }
        }
        $data = $folders;
        $data['from'] = $from['email'];
        $data['cc'] =  implode(';',$ccArray);
        $data['bcc'] =  implode(';',$bccArray);

        $companyid = $this->common_data['company_data']['company_id'];
        $ngoProposalID = trim($this->input->post('proposalID') ?? '');
        $data['ngoProposalID'] = $ngoProposalID;
        $where = "companyID = " . $companyid . " AND documentID = '6' AND documentAutoID = '289'";
        $this->db->select('*');
        $this->db->from('srp_erp_ngo_attachments');
        $this->db->where($where);
        $data['docDet'] = $this->db->get()->result_array();


        $html = $this->load->view('system/crm/ajax/crm_compose_mail',$data,true);
        echo json_encode($html);
    }
    function compose_email(){
        $this->form_validation->set_rules('to', ' To Email', 'trim|required|valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CrmMailbox_modal->save_compose_email());
        }


    }
    function getsenteMails(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $currentuseremail = $this->db->query("SELECT * FROM `srp_erp_crm_emailconfiguration` where companyID  = '{$companyid}' AND isDefault = 1 AND empID = '{$currentuserid}'")->row_array();
        $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` where companyID = '{$companyid}' and fromEmailAddress = '{$currentuseremail['username']}'")->result_array();


        $totalCount = count($count);

        $data_pagination = $this->input->post('pageID');
        $per_page = 10;
        $config = array();
        $config["base_url"] = "#sent-list";
        $config["total_rows"] =  $totalCount;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;

        $this->pagination->initialize($config);
        $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
        $dataCount = count($count);
        $sentfunction = 'sentemailpagination';
        $data["empCount"] = $totalCount;
        $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
        $data["per_page"] = $per_page;
        $thisPageStartNumber = ($page+1);
        $thisPageEndNumber = $page+$dataCount;

        $data['empemails'] = $this->db->query("SELECT * FROM `srp_erp_crm_emails` where companyID = '{$companyid}'  AND fromEmailAddress = '{$currentuseremail['username']}'
 ORDER BY crmEmailID LIMIT {$page}, {$per_page}")->result_array();


        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_sentmessages', $data, true);
        echo json_encode($data);
    }
    function getReadMailsent(){
        $companyid = current_companyID();
        $userid = current_userID();
        $email = $this->input->post('emailid');
        $data['details'] = $this->db->query("SELECT * FROM `srp_erp_crm_emails` where companyID = '{$companyid}' and createdUserID = '{$userid}'  AND crmEmailID = '{$email}'")->row_array();
        $this->load->view('system/crm/ajax/crm_read_mail_sent',$data);
       // echo json_encode($data);
    }
    function contactwise_emails()
    {
        $data['contactid'] = $this->input->post('contactID');
        $this->load->view('system/crm/mail_box_contact_wise',$data);
    }

    function getInboxMailscontactwise(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $contactid = $this->input->post('contactid');
        $contactdetails = $this->db->query("select * from srp_erp_crm_contactmaster where companyID = '{$companyid}' ANd contactID = '{$contactid}'")->row_array();
       /* $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` WHERE companyID = '{$companyid}' AND createdUserID = '{$currentuserid}' and documentID = 6 AND masterAutoID = '{$contactid}'")->result_array();
        $totalCount = count($count);

        $data_pagination = $this->input->post('pageID');
        $per_page = 10;
        $config = array();
        $config["base_url"] = "#sent-list";
        $config["total_rows"] =  $totalCount;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;

        $this->pagination->initialize($config);
        $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
        $dataCount = count($count);
        $sentfunction = 'inboxmailcontact';
        $data["empCount"] = $totalCount;
        $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
        $data["per_page"] = $per_page;
        $thisPageStartNumber = ($page+1);
        $thisPageEndNumber = $page+$dataCount;*/

        $data['empemails'] = $this->db->query("SELECT srp_erp_crm_emails.*,DATE_FORMAT( srp_erp_crm_emails.`timestamp`, '%d-%m-%Y %h:%i:%s %p' ) AS createdDateTimeconverted,srp_employeesdetails.Ename2 as nameemployee FROM `srp_erp_crm_emails` LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_crm_emails.createdUserID  where companyID = '{$companyid}' and documentID = 6 AND ( masterAutoID = '{$contactid}' OR ccEmail LIKE CONCAT('%', '{$contactdetails['email']}', '%') OR bccEmail LIKE CONCAT('%', '{$contactdetails['email']}', '%'))  ORDER BY crmEmailID")->result_array();

       /* $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";*/
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox_contact', $data, true);
        echo json_encode($data);
    }
    function getReadMailinbox_sent(){
        $companyid = current_companyID();
        $userid = current_userID();
        $email = $this->input->post('emailid');
        $data['details'] = $this->db->query("SELECT * FROM `srp_erp_crm_emails` where companyID = '{$companyid}' and  crmEmailID = '{$email}'")->row_array();
        $this->load->view('system/crm/ajax/crm_read_mail_inbox_contact',$data);
      // echo json_encode($data);
    }
    function compose_email_contact(){
    /*    $this->form_validation->set_rules('cc', ' CC Email', 'valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {*/
            echo json_encode($this->CrmMailbox_modal->save_compose_email_contact());


    }
    function compose_email_lead(){
        /*    $this->form_validation->set_rules('cc', ' CC Email', 'valid_email');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {*/
        echo json_encode($this->CrmMailbox_modal->save_compose_email_lead());


    }
    function organization_emails()
    {
        $data['organizationID'] = $this->input->post('organizationID');
        $this->load->view('system/crm/mail_box_organization_wise',$data);
    }
    function getInboxMailsorganizationwise(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $organizationID = $this->input->post('organizationID');
        $organizationdetails = $this->db->query("SELECT * FROM `srp_erp_crm_organizations` where companyID = '{$companyid}' AND organizationID = '{$organizationID}'")->row_array();
        $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` WHERE companyID = '{$companyid}' AND createdUserID = '{$currentuserid}' and documentID = 8 AND masterAutoID = '{$organizationID}'")->result_array();
        $totalCount = count($count);

        $data_pagination = $this->input->post('pageID');
        $per_page = 10;
        $config = array();
        $config["base_url"] = "#sent-list";
        $config["total_rows"] =  $totalCount;
        $config["per_page"] = $per_page;
        $config["data_page_attr"] = 'data-emp-pagination';
        $config["uri_segment"] = 3;

        $this->pagination->initialize($config);
        $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
        $dataCount = count($count);
        $sentfunction = 'inboxmailcontact';
        $data["empCount"] = $totalCount;
        $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
        $data["per_page"] = $per_page;
        $thisPageStartNumber = ($page+1);
        $thisPageEndNumber = $page+$dataCount;

        $data['empemails'] = $this->db->query("SELECT srp_erp_crm_emails.*,DATE_FORMAT( srp_erp_crm_emails.`timestamp`, '%d-%m-%Y %h:%i:%s %p' ) AS createdDateTimeconverted,srp_employeesdetails.Ename2 as nameemployee FROM `srp_erp_crm_emails` LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_crm_emails.createdUserID  where companyID = '{$companyid}' and documentID = 8 AND (masterAutoID = '{$organizationID}' OR ccEmail LIKE CONCAT('%', '{$organizationdetails['email']}', '%') OR bccEmail LIKE CONCAT('%', '{$organizationdetails['email']}', '%')) ORDER BY crmEmailID")->result_array();

        $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox_organization', $data, true);
        echo json_encode($data);
    }
    function getReadMailinbox_sent_organization(){
        $companyid = current_companyID();
        $userid = current_userID();
        $email = $this->input->post('emailid');
        $data['details'] = $this->db->query("SELECT * FROM `srp_erp_crm_emails` where companyID = '{$companyid}' and  crmEmailID = '{$email}'")->row_array();
        $this->load->view('system/crm/ajax/crm_read_mail_inbox_contact',$data);
        // echo json_encode($data);
    }

    function compose_email_organization(){

     /*   $this->form_validation->set_rules('cc', ' CC Email', 'valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {*/
            echo json_encode($this->CrmMailbox_modal->save_compose_email_organization());
        /*}*/


    }
    function load_mailbox_configuretion(){

        echo json_encode($this->CrmMailbox_modal->load_mailbox_configuretion());
    }
    function leadwise_emails()
    {
        $data['leadID'] = $this->input->post('leadID');
        $this->load->view('system/crm/mail_box_lead_wise',$data);
    }
    function getInboxMailsleadwise(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $leadID = $this->input->post('leadID');
        $leadwise = $this->db->query("select * from srp_erp_crm_leadmaster where companyID = '{$companyid}' ANd leadID = '{$leadID}'")->row_array();
        /* $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` WHERE companyID = '{$companyid}' AND createdUserID = '{$currentuserid}' and documentID = 6 AND masterAutoID = '{$contactid}'")->result_array();
         $totalCount = count($count);

         $data_pagination = $this->input->post('pageID');
         $per_page = 10;
         $config = array();
         $config["base_url"] = "#sent-list";
         $config["total_rows"] =  $totalCount;
         $config["per_page"] = $per_page;
         $config["data_page_attr"] = 'data-emp-pagination';
         $config["uri_segment"] = 3;

         $this->pagination->initialize($config);
         $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
         $dataCount = count($count);
         $sentfunction = 'inboxmailcontact';
         $data["empCount"] = $totalCount;
         $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
         $data["per_page"] = $per_page;
         $thisPageStartNumber = ($page+1);
         $thisPageEndNumber = $page+$dataCount;*/

        $data['empemails'] = $this->db->query("SELECT srp_erp_crm_emails.*,DATE_FORMAT( srp_erp_crm_emails.`timestamp`, '%d-%m-%Y %h:%i:%s %p' ) AS createdDateTimeconverted,srp_employeesdetails.Ename2 as nameemployee FROM `srp_erp_crm_emails` LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_crm_emails.createdUserID  where companyID = '{$companyid}' and documentID = 5 AND ( masterAutoID = '{$leadID}' OR ccEmail LIKE CONCAT('%', '{$leadwise['email']}', '%') OR bccEmail LIKE CONCAT('%', '{$leadwise['email']}', '%'))  ORDER BY crmEmailID")->result_array();

        /* $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";*/
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox_lead', $data, true);
        echo json_encode($data);
    }
    function opportunitieview()
    {
        $data['opportunityID'] = $this->input->post('opportunityID');
        $this->load->view('system/crm/mail_box_opportunitie_wise',$data);
    }
    function getInboxMailsopportunitie(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $opportunityID = $this->input->post('opportunityID');
        $contactdetails = $this->db->query("select * from srp_erp_crm_contactmaster where companyID = '{$companyid}' ANd contactID = '{$contactid}'")->row_array();
        /* $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` WHERE companyID = '{$companyid}' AND createdUserID = '{$currentuserid}' and documentID = 6 AND masterAutoID = '{$contactid}'")->result_array();
         $totalCount = count($count);

         $data_pagination = $this->input->post('pageID');
         $per_page = 10;
         $config = array();
         $config["base_url"] = "#sent-list";
         $config["total_rows"] =  $totalCount;
         $config["per_page"] = $per_page;
         $config["data_page_attr"] = 'data-emp-pagination';
         $config["uri_segment"] = 3;

         $this->pagination->initialize($config);
         $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
         $dataCount = count($count);
         $sentfunction = 'inboxmailcontact';
         $data["empCount"] = $totalCount;
         $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
         $data["per_page"] = $per_page;
         $thisPageStartNumber = ($page+1);
         $thisPageEndNumber = $page+$dataCount;*/

        $data['empemails'] = $this->db->query("SELECT srp_erp_crm_emails.*,DATE_FORMAT( srp_erp_crm_emails.`timestamp`, '%d-%m-%Y %h:%i:%s %p' ) AS createdDateTimeconverted,srp_employeesdetails.Ename2 as nameemployee FROM `srp_erp_crm_emails` LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_crm_emails.createdUserID  where companyID = '{$companyid}' and documentID = 4 AND (masterAutoID = '{$opportunityID}')  ORDER BY crmEmailID")->result_array();

        /* $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";*/
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox_opportunitie', $data, true);
        echo json_encode($data);
    }
    function compose_email_opportunitie(){
           $this->form_validation->set_rules('To', ' To Email', 'valid_email');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode(array('e', validation_errors()));
            } else {
                echo json_encode($this->CrmMailbox_modal->save_compose_email_opportunitie());
            }

    }
    function compose_email_project(){
        $this->form_validation->set_rules('To', ' To Email', 'valid_email');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->CrmMailbox_modal->save_compose_email_project());
        }

    }
    function projectview()
    {
        $data['projectID'] = $this->input->post('projectid');
        $this->load->view('system/crm/project_wise',$data);
    }
    function getInboxMailsprojects(){
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $projectID= $this->input->post('projectID');
        $contactdetails = $this->db->query("select * from srp_erp_crm_contactmaster where companyID = '{$companyid}' ANd contactID = '{$contactid}'")->row_array();
        /* $count = $this->db->query("SELECT * FROM `srp_erp_crm_emails` WHERE companyID = '{$companyid}' AND createdUserID = '{$currentuserid}' and documentID = 6 AND masterAutoID = '{$contactid}'")->result_array();
         $totalCount = count($count);

         $data_pagination = $this->input->post('pageID');
         $per_page = 10;
         $config = array();
         $config["base_url"] = "#sent-list";
         $config["total_rows"] =  $totalCount;
         $config["per_page"] = $per_page;
         $config["data_page_attr"] = 'data-emp-pagination';
         $config["uri_segment"] = 3;

         $this->pagination->initialize($config);
         $page = (!empty($data_pagination)) ? (($data_pagination -1) * $per_page): 0;
         $dataCount = count($count);
         $sentfunction = 'inboxmailcontact';
         $data["empCount"] = $totalCount;
         $data["pagination"] = $this->pagination->create_email_pagination($sentfunction);
         $data["per_page"] = $per_page;
         $thisPageStartNumber = ($page+1);
         $thisPageEndNumber = $page+$dataCount;*/

        $data['empemails'] = $this->db->query("SELECT srp_erp_crm_emails.*,DATE_FORMAT( srp_erp_crm_emails.`timestamp`, '%d-%m-%Y %h:%i:%s %p' ) AS createdDateTimeconverted,srp_employeesdetails.Ename2 as nameemployee FROM `srp_erp_crm_emails` LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_crm_emails.createdUserID  where companyID = '{$companyid}' and documentID = 9 AND (masterAutoID = '{$projectID}')  ORDER BY crmEmailID")->result_array();

        /* $data["filterDisplay"] = "Showing {$thisPageStartNumber} to {$thisPageEndNumber} of {$totalCount} entries";*/
        $data['html'] = $this->load->view('system/crm/ajax/crm_mail_inbox_project', $data, true);
        echo json_encode($data);
    }
    }

