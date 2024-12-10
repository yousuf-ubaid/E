<?php


/** ================================
 * -- File Name : Scheduler_services.php
 * -- Project Name : GS_SME
 * -- Module Name : Scheduler services
 * -- Create date : 02 - May 2016
 * -- Description : This controller used for automate function like cron jobs, notifications, schedulers and emails
 */

class Scheduler_services extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        ini_set('max_execution_time', 360);
        ini_set('memory_limit', '2048M');
    }

    function index(){

        $job = $this->uri->segment(2);
        if(empty($job)){
            die('Not a valid scheduler');
        }
        $db2 = $this->load->database('db2', TRUE);

        $schedule_data = $db2->get_where('scheduler_master', ['scheduler_uri'=>$job])->row_array();
        if(empty($schedule_data)){
            die('Scheduler not found');
        }

        echo "<h3>".$schedule_data['scheduler_description']."</h3>";

        $sch_id = $schedule_data['id'];
        $company_data = $db2->select('company_id, host, db_name, db_username, db_password,company_code')->from('srp_erp_company')
                        ->join("(SELECT company_id AS sch_comID FROM scheduler_company 
                                  WHERE scheduler_id = {$sch_id} AND is_active = 1) AS sch_tb",
                                'sch_tb.sch_comID=srp_erp_company.company_id')
                        ->where('host is NOT NULL', NULL, FALSE)->where('db_username is NOT NULL', NULL, FALSE)
                        ->where('db_password is NOT NULL', NULL, FALSE)->where('db_name is NOT NULL', NULL, FALSE)
                        ->get()->result_array();

        if(empty($company_data)){
            die('Scheduler not assigned for any of the company in this host');
        }

        switch ($job){
            case 'hr-doc-expiry':
                $this->hr_doc_expiry($company_data);
            break;
            case 'leave-monthly-accrual':
                $this->leave_monthly_accrual($company_data);
                break;
            case 'leave-annual-accrual':
                $this->leave_annual_accrual($company_data);
                break;

            default: die('Not a valid call');
        }
    }

    function hr_doc_expiry($company_data){


        $to_day = date('Y-m-d');
        $expire_date = date('Y-m-d', strtotime("$to_day +7days"));
        $this->load->library('s3');

        foreach($company_data as $val){
            $this->setup_db($val); // setup company db
            $company_id = $val['company_id'];
            $com_logo = $this->db->select('company_logo')->from('srp_erp_company')->where(['company_id'=>$company_id])->get()->row('company_logo');

            $com_logo = "images/logo/{$com_logo}";
            $com_logo = $this->s3->createPresignedRequest($com_logo, '1 hour');


            $emp_docs = $this->db->query("SELECT Erp_companyID, Ename2 AS emp_name, EEmail AS emp_mail, doc_det.*
                    FROM srp_employeesdetails AS empTB 
                    JOIN (
                        SELECT DocDesFormID, DocDescription,  sub_types.description AS sub_typesDes, documentNo, mas.DocDesID AS DocDesID, PersonID, 
                        DATE_FORMAT( issueDate, '%d-%m-%Y' ) AS issueDate, DATE_FORMAT( expireDate, '%d-%m-%Y' ) AS expireDate, 
                        IF(issuedBy=-1, issuedByText, IF(sysType.issuedByType = 1, CONCAT(company_code, ' - ', company_name), country_tb.CountryDes) ) AS issueDet
                        FROM srp_documentdescriptionmaster mas
                        JOIN srp_documentdescriptionsetup AS setup ON mas.DocDesID = setup.DocDesID
                        JOIN srp_erp_system_document_types AS sysType ON mas.systemTypeID = sysType.id
                        JOIN ( 
                             SELECT * FROM srp_documentdescriptionforms WHERE PersonType = 'E' AND isActive = 1 AND isDeleted = 0 
                             AND isExpiryMailSend = 0 AND (expireDate <= '{$to_day}' OR expireDate = '{$expire_date}')
                        ) AS forms ON forms.DocDesID = mas.DocDesID
                        LEFT JOIN ( 
                            SELECT * FROM srp_erp_system_document_sub_types WHERE companyID = {$company_id} 
                        ) AS sub_types ON sub_types.sub_id = forms.subDocumentType
                        LEFT JOIN srp_erp_company AS comTB ON forms.issuedBy = comTB.company_id
                        LEFT JOIN ( 
                            SELECT countryID, CountryDes FROM srp_countrymaster WHERE Erp_companyID = {$company_id} 
                        ) AS country_tb ON country_tb.countryID = forms.issuedBy 
                        WHERE mas.Erp_companyID = {$company_id}  AND mas.isDeleted = 0 ORDER BY issueDate DESC
                    ) AS doc_det ON empTB.EIdNo = doc_det.PersonID 
                    WHERE empTB.isDischarged = 0 #AND empTB.EIdNo = 1167 LIMIT 1")->result_array();


            $dep_docs = $this->db->query("SELECT PersonID, emp_name, docType, relName, relaType.relationship, expireDate, emp_mail, detID
                      FROM(
                            SELECT PersonID, Ename2 AS emp_name, expireDate, 'Passport' AS docType, empfamilydetailsID AS detID,
                            relName, relationship, emp_tb.segmentID, emp_tb.EEmail AS emp_mail
                            FROM srp_employeesdetails AS emp_tb 
                            JOIN (
                                SELECT empID AS PersonID, `name` AS relName, DATE(passportExpiredate) AS expireDate, relationship, empfamilydetailsID
                                FROM srp_erp_family_details AS family_det WHERE isPassExpiryMailSend = 0
                            )  AS pass_tb ON emp_tb.EIdNo = pass_tb.PersonID
                            WHERE emp_tb.Erp_companyID = {$company_id} AND (expireDate <= '{$to_day}' OR expireDate = '{$expire_date}')
                            AND emp_tb.isDischarged = 0
                        
                            UNION ALL
                        
                            SELECT PersonID, Ename2 AS emp_name, expireDate, 'Visa' AS docType, empfamilydetailsID AS detID, 
                            relName, relationship, emp_tb.segmentID, emp_tb.EEmail AS emp_mail
                            FROM srp_employeesdetails AS emp_tb 
                            JOIN (
                                SELECT empID AS PersonID, `name` AS relName, DATE(VisaexpireDate) AS expireDate, relationship, empfamilydetailsID
                                FROM srp_erp_family_details AS family_det WHERE isVisaExpiryMailSend = 0
                            )  AS visa_tb  ON emp_tb.EIdNo = visa_tb.PersonID
                            WHERE emp_tb.Erp_companyID = {$company_id} AND (expireDate <= '{$to_day}' OR expireDate = '{$expire_date}')
                            AND emp_tb.isDischarged = 0 
                      ) AS empTB
                      LEFT JOIN srp_erp_family_relationship AS relaType ON relaType.relationshipID = empTB.relationship ")->result_array();


            if(!empty($dep_docs)){
                $dep_docs = array_group_by($dep_docs, 'PersonID');
            }

            //echo '<pre>'; print_r($dep_docs); echo '</pre>';

            if(!empty($emp_docs)){
                $emp_docs = array_group_by($emp_docs, 'PersonID');

                foreach($emp_docs as $emp_id=>$docs){
                    $data['docs'] = $docs;

                    if(array_key_exists($emp_id, $dep_docs)){
                        $data['dep_docs'] = $dep_docs[$emp_id];
                        unset($dep_docs[$emp_id]);
                    }

                    $view = $this->load->view('system/schedules/hr-doc-expiry-email', $data, true);

                    $mailData['toEmail'] = $docs[0]['emp_mail'];
                    $mailData['subject'] = 'Document Expiry Reminder';
                    $mailData['param'] = [
                        'empName' => $docs[0]['emp_name'], 'body' => $view,
                        'custom_width' => '830', 'com_logo' => $com_logo
                    ];

                    //if($mailData['toEmail'] == 'karangoda82@gmail.com'){ }

                    $this->send_schedule_email($mailData);

                    foreach($docs as $exp){
                        $this->db->where(['DocDesFormID'=>$exp['DocDesFormID']])->update('srp_documentdescriptionforms', ['isExpiryMailSend'=> 1]);
                    }

                    if(!empty($data['dep_docs'])){
                        foreach($data['dep_docs'] as $exp){
                            $column = ($exp['docType'] == 'Passport')? 'isPassExpiryMailSend': 'isVisaExpiryMailSend';
                            $this->db->where(['empfamilydetailsID'=>$exp['detID']])->update('srp_erp_family_details', [$column=> 1]);
                        }
                    }
                }
            }


            if(!empty($dep_docs)){

                foreach($dep_docs as $emp_id=>$docs) {
                    $data['dep_docs'] = $docs;
                    $view = $this->load->view('system/schedules/hr-doc-expiry-email', $data, true);

                    $mailData['toEmail'] = $docs[0]['emp_mail'];
                    $mailData['subject'] = 'Document Expiry Reminder';
                    $mailData['param'] = [
                        'empName' => $docs[0]['emp_name'], 'body' => $view,
                        'custom_width' => '830', 'com_logo' => $com_logo
                    ];

                    $this->send_schedule_email($mailData);

                    foreach ($docs as $exp) {
                        $column = ($exp['docType'] == 'Passport') ? 'isPassExpiryMailSend' : 'isVisaExpiryMailSend';
                        $this->db->where(['empfamilydetailsID' => $exp['detID']])->update('srp_erp_family_details', [$column => 1]);
                    }
                }
            }
        }
    }

    function send_schedule_email($mailData, $attachment = 0, $path = 0){

        $this->load->library('email_manual');

        $toEmail = $mailData['toEmail'];
        $subject = $mailData['subject'];
        $param = $mailData['param'];
        $from = (hstGeras==1)? 'noreply@redberylit.com': 'noreply@redberylit.com';

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

        $this->load->library('email', $config);


        if (!empty($param)) {
            $this->email->from($from, EMAIL_SYS_NAME);
            $this->email->to($toEmail);
            $this->email->subject($subject);
            $this->email->message($this->load->view('system/email_template/email_approval_template_log', $param, TRUE));
            if ($attachment == 1) {
                $this->email->attach($path);
            }

            $result = $this->email->send();
            $this->email->clear(true);

            return $result;
       }

    }



    function leave_monthly_accrual($company_data)
    {
        foreach ($company_data as $val)
        {
            $this->setup_db($val);
            $currentdate = input_format_date(current_date(),'yyyy-mm-dd');
             $d = explode('-',  date('m-Y', strtotime($currentdate)));
             $company_id = $val['company_id'];
             $companycode = $val['company_code'];
             $leavegropdetails = $this->db->query("SELECT srp_erp_leavegroup.leaveGroupID, description, leaveTypeID,companyID FROM `srp_erp_leavegroupdetails` 
	                                                    LEFT JOIN srp_erp_leavegroup on srp_erp_leavegroup.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID
                                                        WHERE policyMasterID = 3 AND companyID = $company_id GROUP BY leaveGroupID")->result_array();
            foreach ($leavegropdetails as $leavegrouptype)
            {
                $accrualexist = $this->db->query("SELECT leaveaccrualMasterID FROM `srp_erp_leaveaccrualmaster` WHERE companyID = $company_id 
                AND leaveGroupID = '{$leavegrouptype['leaveGroupID']}' AND `Year`=  {$d[1]} AND `month` =  {$d[0]} AND policyMasterID = 3")->row_array();
                if(empty($accrualexist['leaveaccrualMasterID']))
                {
                    $this->db->trans_begin();
                    $companyID = $company_id;
                    $description = 'Monthly Leave Accrual - '.$leavegrouptype['description'];
                    $leaveGroupID = $leavegrouptype['leaveGroupID'];
                    $this->load->library('sequence');
                    $code = $this->sequence->sequence_generator_scheduleservices('LAM',0,$companyID,$companycode);

                        $data = array(
                            'companyID' => $companyID,
                            'leaveaccrualMasterCode' => $code,
                            'documentID' => 'LAM',
                            'description' => $description,
                            'year' => $d[1],
                            'month' => $d[0],
                            'leaveGroupID' => $leaveGroupID,
                            'createDate' => date('Y-m-d H:i:s'),
                            'policyMasterID' => 3
                        );
                    $insert = $this->db->insert('srp_erp_leaveaccrualmaster', $data);


                    if ($insert) {
                        $last_id = $this->db->insert_id();
                        $detail = array();
                        $date = $d[1] . '-' . $d[0] . '-' . '01';
                        $lastDate = date("Y-m-t", strtotime($date));
                        $q2 = "SELECT DateAssumed, CONCAT(EIdNo, '-', srp_erp_leavetype.leaveTypeID) AS leaveTypeKey, EIdNo, srp_employeesdetails.leaveGroupID, srp_erp_leavegroupdetails.*, policyID FROM `srp_employeesdetails` INNER JOIN `srp_erp_leavegroupdetails` ON srp_erp_leavegroupdetails.leaveGroupID = srp_employeesdetails.leaveGroupID AND policyMasterID=3 AND DateAssumed <= '{$lastDate}' INNER JOIN `srp_erp_leavetype` ON srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leavetype.leaveTypeID WHERE isDischarged !=1 AND Erp_companyID = {$companyID} AND srp_employeesdetails.leaveGroupID IS NOT NULL AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND (EIdNo , srp_erp_leavetype.leaveTypeID) NOT IN (SELECT empID, leaveType FROM `srp_erp_leaveaccrualmaster` INNER JOIN srp_erp_leaveaccrualdetail ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID WHERE year = {$d[1]} AND month = {$d[0]} AND srp_erp_leaveaccrualmaster.leaveaccrualMasterID != {$last_id} AND srp_erp_leaveaccrualmaster.manualYN=0 GROUP BY empID , leaveType)";
                        $result = $this->db->query($q2)->result_array();
                        $updateArr = array();
                        $insert_Arr = array();
                        if ($result) {
                            foreach ($result as $val) {
                                $daysEntitled = $val['noOfDays'];
                                $datas = array('leaveaccrualMasterID' => $last_id,
                                    'empID' => $val['EIdNo'],
                                    'leaveGroupID' => $leaveGroupID,
                                    'leaveType' => $val['leaveTypeID'],
                                    'daysEntitled' => $daysEntitled,
                                    'description' => 'Leave Accrual ' . date('m-Y', strtotime($currentdate)),
                                    'createDate' => date('Y-m-d H:i:s')
                                );
                                array_push($insert_Arr, array(
                                    'leaveTypeID' => $val['leaveTypeID'], 'empID' => $val['EIdNo'], 'days' => $daysEntitled, 'companyID' => $companyID, 'companyCode' => $companycode, 'createdUserGroup' => '',
                                    'createdPCID' =>  NULL,
                                    'createdUserID' => NULL,
                                    'createdDateTime' => NULL,
                                    'createdUserName' => NULL,
                                ));
                                array_push($detail, $datas);
                            }
                            $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);
                        }


                    }
                    if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('e', 'Failed.');
                    echo json_encode(array('error' => 1));
                    exit;
                } else {
                    $this->db->trans_commit();
                    $this->confrim_leave_accrual($last_id,$company_id,$companycode);
                    echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                }
                }
            }

        }
    }

    function leave_annual_accrual($company_data)
    {
        foreach ($company_data as $val)
        {
            $this->setup_db($val);
            $company_id = $val['company_id'];
            $companycode = $val['company_code'];
            $leavegropdetails = $this->db->query("SELECT srp_erp_leavegroup.leaveGroupID, description, leaveTypeID,companyID FROM `srp_erp_leavegroupdetails` 
	                                                    LEFT JOIN srp_erp_leavegroup on srp_erp_leavegroup.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID
                                                        WHERE policyMasterID = 1 AND companyID = $company_id GROUP BY leaveGroupID")->result_array();

            foreach ($leavegropdetails as $leavegrouptype)
            {
                $year = date('Y');
                $accrualexist = $this->db->query("SELECT leaveaccrualMasterID FROM `srp_erp_leaveaccrualmaster` WHERE companyID = $company_id 
                AND leaveGroupID = '{$leavegrouptype['leaveGroupID']}' AND `Year`=  {$year} AND policyMasterID = 1")->row_array();
                if(empty($accrualexist))
                {
                    $this->db->trans_begin();
                    $companyID = $company_id;
                    $description = 'Annual Leave Accrual';
                    $leaveGroupID = $leavegrouptype['leaveGroupID'];
                    $this->load->library('sequence');
                    $code = $this->sequence->sequence_generator_scheduleservices('LAM',0,$companyID,$companycode);

                    $data = array(
                        'companyID' => $companyID,
                        'leaveaccrualMasterCode' => $code,
                        'documentID' => 'LAM',
                        'description' => $description,
                        'year' => date('Y'),
                        'month' => date('m'),
                        'leaveGroupID' => $leaveGroupID,
                        'createdUserGroup' => null,
                        'createDate' => date('Y-m-d H:i:s'),
                        'createdpc' => null,
                        'policyMasterID' => 1

                    );
                    $this->db->insert('srp_erp_leaveaccrualmaster', $data);
                    $last_id = $this->db->insert_id();
                    $detail = array();
                    $date = date('Y');
                    $q2 = "SELECT * FROM srp_employeesdetails inner JOIN(select * from `srp_erp_leavegroupdetails` WHERE leaveGroupID = {$leaveGroupID} AND policyMasterID=1 ) leavegroup on leavegroup.leaveGroupID=srp_employeesdetails.leaveGroupID WHERE NOT EXISTS( SELECT * FROM srp_erp_leaveaccrualdetail WHERE srp_employeesdetails.EIdNo = empID AND leaveGroupID = {$leaveGroupID} AND initalDate={$date} GROUP BY empID) AND srp_employeesdetails.leaveGroupID = {$leaveGroupID} AND isDischarged !=1 AND  Erp_companyID={$companyID}";
                    $q12 = $q2;

                    $result = $this->db->query($q2)->result_array();

                    $exist = $this->db->query("SELECT concat(det.empID,'-',det.leaveGroupID,'-',det.leaveType) as leavekey FROM `srp_erp_leaveaccrualmaster` INNER JOIN (SELECT * FROM `srp_erp_leaveaccrualdetail` WHERE nextDate IS NOT NULL) det ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID=det.leaveaccrualMasterID WHERE companyID = {$companyID} AND initalDate={$date} ")->result_array();


                    $updateArr = array();
                    $insert_Arr = array();
                    if ($result) {
                        foreach ($result as $val) {
                            $daysEntitled = 0;
                            $hoursEntitled = 0;

                            $daysEntitled = $val['noOfDays'];


                            $datas = array('leaveaccrualMasterID' => $last_id, 'empID' => $val['EIdNo'],
                                'leaveGroupID' => $leaveGroupID, 'leaveType' => $val['leaveTypeID'],
                                'daysEntitled' => $daysEntitled, 'hoursEntitled' => $hoursEntitled,
                                'description' => 'Leave Accrual ' . date('Y'),
                                'createDate' => date('Y-m-d H:i:s'),
                                'createdUserGroup' => NULL, 'createdPCid' => null,
                                'initalDate' => date('Y'), 'nextDate' => date('Y') + 1,
                            );

                            $keys = array_keys(array_column($exist, 'leavekey'), $val['EIdNo'] . '-' . $val['leaveGroupID'] . '-' . $val['leaveTypeID']);
                            $new_array = array_map(function ($k) use ($exist) {
                                return $exist[$k];
                            }, $keys);

                            if (empty($new_array)) {
                                array_push($detail, $datas);
                            }

                        }
                        if (!empty($detail)) {
                            $this->db->insert_batch('srp_erp_leaveaccrualdetail', $detail);
                        }

                    }
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('e', 'Failed.');
                        echo json_encode(array('error' => 1));
                        exit;
                    } else {
                        $this->db->trans_commit();
                        echo json_encode(array('error' => 0, 'leaveGroupID' => $last_id));
                        $this->confrim_leave_accrual($last_id,$company_id,$companycode);

                    }
                }

                }



        }
    }





    function confrim_leave_accrual($last_id, $company_id, $companycode)
    {
        $masterID = $last_id;
        $cmpID = $company_id;
        $x = 1;
        $lastID = 0;
        $levaccM = $this->db->query("SELECT * FROM srp_erp_leaveaccrualmaster
                    WHERE leaveaccrualMasterID = {$masterID}")->row_array();

        $empidLevid = $this->db->query("SELECT empID,leaveType FROM srp_erp_leaveaccrualdetail
                      WHERE leaveaccrualMasterID = {$masterID}
                      GROUP BY empID,leaveType")->result_array();
        $emid = array();

        foreach ($empidLevid as $val) {
            $empID = $val['empID'];
            $leaveType = $val['leaveType'];
            $res = $this->db->query("SELECT
                    documentCode,ECode
                FROM
                    srp_erp_leavemaster
                    LEFT JOIN srp_employeesdetails on srp_erp_leavemaster.empID = srp_employeesdetails.EIdNo
                    LEFT JOIN srp_erp_leavegroupdetails on srp_erp_leavegroupdetails.leaveGroupID = srp_employeesdetails.leaveGroupID AND srp_erp_leavegroupdetails.leaveTypeID=$leaveType
                WHERE
                    empID ={$empID}
                AND srp_erp_leavegroupdetails.maxCarryForward=1
                AND srp_erp_leavemaster.leaveTypeID ={$leaveType}
                AND srp_erp_leavemaster.approvedYN !=1

                ")->row_array();
            if (!empty($res)) {
                $dat = array('ECode' => $res['ECode'], 'documentCode' => $res['documentCode']);
                array_push($emid, $dat);
            }
        }

        if (empty($emid)) {
            $carryfwd = $this->db->query("SELECT
                    empID,
                    leaveType,
                    initalDate,
                    srp_erp_leavegroupdetails.maxCarryForward
                FROM
                    srp_erp_leaveaccrualdetail
                LEFT JOIN srp_erp_leavegroupdetails ON srp_erp_leaveaccrualdetail.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID
                AND srp_erp_leaveaccrualdetail.leaveType = srp_erp_leavegroupdetails.leaveTypeID
                WHERE
                    leaveaccrualMasterID = {$masterID}
                AND srp_erp_leavegroupdetails.isCarryForward=1
                AND srp_erp_leavegroupdetails.maxCarryForward IS NOT NULL
                GROUP BY
                    srp_erp_leaveaccrualdetail.leaveType")->result_array();

            if (!empty($carryfwd)) {
                if ($levaccM['policyMasterID'] == 1) {
                    foreach ($carryfwd as $value) {
                        $initalDate = $value['initalDate'];
                        $startDate = $value['initalDate'] . '-01-01';
                        $lvtypeid = $value['leaveType'];
                        $carryfwds = $this->db->query("SELECT
                    srp_erp_leaveaccrualdetail.empID,
                    srp_erp_leaveaccrualdetail.initalDate,
                    srp_erp_leaveaccrualdetail.nextDate,
                    srp_erp_leaveaccrualdetail.policyMasterID,
                    leaveType,
                    IFNULL(SUM(daysEntitled), 0) AS previousEntitlDays,
                    IFNULL(taken.previousTaken, 0) AS previousTaken,
                    IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0) AS balance,
                srp_erp_leavegroupdetails.maxCarryForward,
                srp_erp_leaveaccrualdetail.leaveGroupID as lvgrpid,

                IF (
                    IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0) > srp_erp_leavegroupdetails.maxCarryForward,
                    (IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0)-srp_erp_leavegroupdetails.maxCarryForward)*-1,
                    0

                ) AS adjestment
                FROM
                    srp_erp_leaveaccrualdetail
                LEFT JOIN srp_erp_leaveaccrualmaster ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID
                LEFT JOIN srp_erp_leavegroupdetails ON srp_erp_leaveaccrualdetail.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID
                AND srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType
                LEFT JOIN (
                    SELECT
                        empID,
                        leaveTypeID,
                        SUM(days) AS previousTaken
                    FROM
                        srp_erp_leavemaster
                    WHERE
                        startDate < '$startDate'
                    GROUP BY
                        leaveTypeID,
                        empID
                ) taken ON taken.empID = srp_erp_leaveaccrualdetail.empID
                AND taken.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType
                WHERE
                    initalDate < '$initalDate'
                    AND srp_erp_leaveaccrualmaster.companyID = $cmpID
                    AND srp_erp_leaveaccrualmaster.leaveGroupID = {$levaccM['leaveGroupID']}
                    AND srp_erp_leaveaccrualmaster.policyMasterID = 1
                    AND srp_erp_leavegroupdetails.leaveTypeID = $lvtypeid
                GROUP BY
                    leaveType,
                    srp_erp_leaveaccrualdetail.empID
                    HAVING adjestment != 0")->result_array();
                    if (!empty($carryfwds)) {
                        if ($x == 1) {
                            $this->load->library('sequence');
                            $code = $this->sequence->sequence_generator_scheduleservices('LAM', $company_id, $companycode);
                            $dataAM = array(
                                'companyID' => $company_id,
                                'leaveaccrualMasterCode' => $code,
                                'documentID' => 'LAM',
                                'description' => 'Leave accrual adjestment for ' . $levaccM['leaveaccrualMasterCode'],
                                'year' => $levaccM['year'],
                                'manualYN' => $levaccM['manualYN'],
                                'month' => $levaccM['month'],
                                'leaveGroupID' => $levaccM['leaveGroupID'],
                                'policyMasterID' => $levaccM['policyMasterID'],
                                'confirmedYN' => 1,
                                'confirmedby' => null,
                                'confirmedDate' => null,
                                'createdUserGroup' => null,
                                'createdpc' => null,
                                'createDate' => null,
                            );
                            $resultM = $this->db->insert('srp_erp_leaveaccrualmaster', $dataAM);
                            $lastID = $this->db->insert_id();
                            $x++;
                        }
                        foreach ($carryfwds as $val) {
                            $dataAD = array(
                                'leaveaccrualMasterID' => $lastID,
                                'empID' => $val['empID'],
                                'leaveGroupID' => $val['lvgrpid'],
                                'leaveType' => $val['leaveType'],
                                'daysEntitled' => $val['adjestment'],
                                'hoursEntitled' => 0,
                                'maxCarryForwardDays' => $val['maxCarryForward'],
                                'description' => 'Leave accrual adjestment for ' . $levaccM['leaveaccrualMasterCode'],
                                'createDate' => date('Y-m-d H:i:s'),
                                'createdUserGroup' => null,
                                'createdPCid' => null,
                                'initalDate' => $val['initalDate'],
                                'nextDate' => $val['nextDate'],
                                'policyMasterID' => $val['policyMasterID'],
                            );
                            $resultAD = $this->db->insert('srp_erp_leaveaccrualdetail', $dataAD);
                        }
                    }
                }
                } 
                elseif ($levaccM['policyMasterID'] == 3) {
                    foreach ($carryfwd as $value) {
                        $mnth = $levaccM['month'];
                        if ($levaccM['month'] > 9) {
                            $mnth = $levaccM['month'];
                        } else {
                            $mnth = '0' . $levaccM['month'];
                        }
                        $startDate = $levaccM['year'] . '-' . $mnth . '-01';
                        $lvtypeid = $value['leaveType'];
                        $carryfwds = $this->db->query("SELECT
                    srp_erp_leaveaccrualdetail.empID,
                    srp_erp_leaveaccrualdetail.initalDate,
                    srp_erp_leaveaccrualdetail.nextDate,
                    srp_erp_leaveaccrualdetail.policyMasterID,
                    leaveType,
                    IFNULL(SUM(daysEntitled), 0) AS previousEntitlDays,
                    IFNULL(taken.previousTaken, 0) AS previousTaken,
                    IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0) AS balance,
                    srp_erp_leavegroupdetails.maxCarryForward,
                    srp_erp_leaveaccrualdetail.leaveGroupID AS lvgrpid,

                IF (
                    IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0) > srp_erp_leavegroupdetails.maxCarryForward,
                    (
                        IFNULL(SUM(daysEntitled), 0) - IFNULL(taken.previousTaken, 0) - srp_erp_leavegroupdetails.maxCarryForward
                    ) *- 1,
                    0
                ) AS adjestment
                FROM
                    srp_erp_leaveaccrualmaster
                LEFT JOIN srp_erp_leaveaccrualdetail ON srp_erp_leaveaccrualmaster.leaveaccrualMasterID = srp_erp_leaveaccrualdetail.leaveaccrualMasterID
                LEFT JOIN srp_erp_leavegroupdetails ON srp_erp_leaveaccrualdetail.leaveGroupID = srp_erp_leavegroupdetails.leaveGroupID
                AND srp_erp_leavegroupdetails.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType
                LEFT JOIN (
                    SELECT
                        empID,
                        leaveTypeID,
                        SUM(days) AS previousTaken
                    FROM
                        srp_erp_leavemaster
                    WHERE
                        startDate < '$startDate'
                    GROUP BY
                        leaveTypeID,
                        empID
                ) taken ON taken.empID = srp_erp_leaveaccrualdetail.empID
                AND taken.leaveTypeID = srp_erp_leaveaccrualdetail.leaveType
                WHERE
                    srp_erp_leaveaccrualmaster.companyID = $cmpID
                    AND DATE_FORMAT( CONCAT( `YEAR`, '-', `MONTH`, '-01' ), '%Y-%m-%d' ) < '$startDate' 
                    AND srp_erp_leaveaccrualmaster.leaveGroupID = {$levaccM['leaveGroupID']}
                    AND srp_erp_leaveaccrualmaster.policyMasterID = 3
                    AND srp_erp_leavegroupdetails.leaveTypeID = $lvtypeid
                GROUP BY
                    srp_erp_leaveaccrualdetail.leaveType,
                    srp_erp_leaveaccrualdetail.empID
                    HAVING adjestment != 0")->result_array();
                        if (!empty($carryfwds)) {
                            if ($x == 1) {
                                $this->load->library('sequence');
                                $code = $this->sequence->sequence_generator_scheduleservices('LAM', $company_id, $companycode);
                                $dataAM = array(
                                    'companyID' => $company_id,
                                    'leaveaccrualMasterCode' => $code,
                                    'documentID' => 'LAM',
                                    'description' => 'Leave accrual adjestment for ' . $levaccM['leaveaccrualMasterCode'],
                                    'year' => $levaccM['year'],
                                    'manualYN' => $levaccM['manualYN'],
                                    'month' => $levaccM['month'],
                                    'leaveGroupID' => $levaccM['leaveGroupID'],
                                    'policyMasterID' => $levaccM['policyMasterID'],
                                    'confirmedYN' => 1,
                                    'confirmedby' => null,
                                    'confirmedDate' => null,
                                    'createdUserGroup' => null,
                                    'createdpc' => null,
                                    'createDate' => null,
                                );
                                $resultM = $this->db->insert('srp_erp_leaveaccrualmaster', $dataAM);
                                $lastID = $this->db->insert_id();
                                $x++;
                            }
                            foreach ($carryfwds as $val) {
                                $dataAD = array(
                                    'leaveaccrualMasterID' => $lastID,
                                    'empID' => $val['empID'],
                                    'leaveGroupID' => $val['lvgrpid'],
                                    'leaveType' => $val['leaveType'],
                                    'daysEntitled' => $val['adjestment'],
                                    'hoursEntitled' => 0,
                                    'maxCarryForwardDays' => $val['maxCarryForward'],
                                    'description' => 'Leave accrual adjestment for ' . $levaccM['leaveaccrualMasterCode'],
                                    'createDate' => date('Y-m-d H:i:s'),
                                    'createdUserGroup' => current_user_group(),
                                    'createdPCid' => current_pc(),
                                    'initalDate' => $val['initalDate'],
                                    'nextDate' => $val['nextDate'],
                                    'policyMasterID' => $val['policyMasterID'],
                                );
                                $resultAD = $this->db->insert('srp_erp_leaveaccrualdetail', $dataAD);
                            }
                        }
                    }
                }

                $data['confirmedYN'] = 1;
                $data['confirmedby'] = null;
                $data['confirmedDate'] = null;
                $update = $this->db->update('srp_erp_leaveaccrualmaster', $data, array('leaveaccrualMasterID' => $masterID));
                //echo json_encode(array('s', 'Successfully confirmed','s'));
            } else {
                $data['confirmedYN'] = 1;
                $data['confirmedby'] = null;
                $data['confirmedDate'] = null;
                $update = $this->db->update('srp_erp_leaveaccrualmaster', $data, array('leaveaccrualMasterID' => $masterID));
                // echo json_encode(array('s', 'Successfully confirmed','s'));
            }
        } else {
            //echo json_encode(array('w', 'Approve following leave applications',$emid));
        }
    }





    function setup_db($conn_data){
        $config['hostname'] = trim($this->encryption->decrypt($conn_data["host"]));
        $config['username'] = trim($this->encryption->decrypt($conn_data["db_username"]));
        $config['password'] = trim($this->encryption->decrypt($conn_data["db_password"]));
        $config['database'] = trim($this->encryption->decrypt($conn_data["db_name"]));
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
        $this->load->database($config, FALSE, TRUE);
    }

}