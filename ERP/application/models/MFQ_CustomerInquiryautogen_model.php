<?php

class MFQ_CustomerInquiryautogen_model extends ERP_Model
{

    function automatedemailmanufacturingcustomerinquiry()
    {

        $CI =& get_instance();
        $db2 = $CI->load->database('db2', TRUE);
        $db2->select('*');
        $db2->where('host is NOT NULL', NULL, FALSE);
        $db2->where('db_username is NOT NULL', NULL, FALSE);
        $db2->where('db_password is NOT NULL', NULL, FALSE);
        $db2->where('db_name is NOT NULL', NULL, FALSE);
        $companyInfo = $db2->get("srp_erp_company")->result_array(); /// to get the companies*/

        $todayIs = time();

        $day_before = date('Y-m-d');
        $count = 0;
        if (!empty($companyInfo)) {
            $summery = '';

            foreach ($companyInfo as $val) {
                $config['hostname'] = trim($this->encryption->decrypt($val["host"]));
                $config['username'] = trim($this->encryption->decrypt($val["db_username"]));
                $config['password'] = trim($this->encryption->decrypt($val["db_password"]));
                $config['database'] = trim($this->encryption->decrypt($val["db_name"]));
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

                echo $val['company_name'] . '<br>';
                echo $config['database'] . '<br>';

                $this->load->database($config, FALSE, TRUE);

                $remainngdays = $this->db->query("SELECT * 
FROM
	(
SELECT
	srp_erp_mfq_customerinquiry.*,
	 srp_erp_mfq_segment.segmentcode,
	`srp_erp_mfq_customermaster`.`CustomerName` AS `CustomerNamemfq`,
	`engineering`.`Ename2` AS `Engineeringname`,
	`Purchasing`.`Ename2` AS `Purchasingname`,
	DATE_FORMAT( engineeringEndDate, '%d-%m-%Y' ) AS engineeringEndDateformated,
	DATE_FORMAT( purchasingEndDate, '%d-%m-%Y' ) AS purchasingEndDateDateformated,
	DATE_FORMAT( productionEndDate, '%d-%m-%Y' ) AS productionEndDateformated,
	DATE_FORMAT( QAQCEndDate, '%d-%m-%Y' ) AS QAQCEndDateDateformated,
	DATE_FORMAT( dueDate, '%d-%m-%Y' ) AS plannedsubmissiondate,
	`production`.`Ename2` AS `Productionname`,
	`qaqc`.`Ename2` AS `qaqcname`,
	DATEDIFF( ( srp_erp_mfq_customerinquiry.dueDate - INTERVAL srp_erp_mfq_customerinquiry.remindEmailBefore DAY ), NOW( ) ) AS remailningdays
FROM
	`srp_erp_mfq_customerinquiry` 
	LEFT JOIN `srp_erp_mfq_segment` ON `srp_erp_mfq_segment`.`mfqSegmentID` = `srp_erp_mfq_customerinquiry`.`segmentID`
	LEFT JOIN `srp_erp_segment` ON `srp_erp_segment`.`segmentID` = `srp_erp_mfq_segment`.`segmentID`
	LEFT JOIN `srp_erp_mfq_customermaster` ON `srp_erp_mfq_customermaster`.`mfqCustomerAutoID` = `srp_erp_mfq_customerinquiry`.`mfqCustomerAutoID`
	LEFT JOIN `srp_employeesdetails` `engineering` ON `engineering`.`EIdNo` = `srp_erp_mfq_customerinquiry`.`engineeringResponsibleEmpID`
	LEFT JOIN `srp_employeesdetails` `Purchasing` ON `Purchasing`.`EIdNo` = `srp_erp_mfq_customerinquiry`.`purchasingResponsibleEmpID`
	LEFT JOIN `srp_employeesdetails` `production` ON `production`.`EIdNo` = `srp_erp_mfq_customerinquiry`.`productionResponsibleEmpID`
	LEFT JOIN `srp_employeesdetails` `qaqc` ON `qaqc`.`EIdNo` = `srp_erp_mfq_customerinquiry`.`QAQCResponsibleEmpID` 
	) t1 
WHERE
    t1.companyID = '{$val['company_id']}'
AND	t1.remailningdays = 0")->result_array();





                // to get the detail which are same to currentdate

                foreach ($remainngdays as $value) {

                    $data = array();
                    $data["header"] = $this->MFQ_CustomerInquiryautogen_model->load_mfq_customerInquirydeadline($value['ciMasterID']);
                    $data["itemDetail"] = $this->MFQ_CustomerInquiryautogen_model->load_mfq_customerInquiryDetaildeadline($value['ciMasterID']);
                    $data["companydetails"] = $this->MFQ_CustomerInquiryautogen_model->load_companydetails($value['companyID']);
                    $data['logo'] = mPDFImage;
                    $html = $this->load->view('system/mfq/ajax/customer_inquiry_print_autogenemail', $data, true);
                    $this->load->library('pdf');
                    $path = UPLOAD_PATH . base_url() . '/uploads/Manufacturing/' . 'Customer_Inquiry_'.$value['ciMasterID']. 'CI' . current_userID() . ".pdf";
                    $this->pdf->save_pdf($html, 'A4', 1, $path);
                    $emaillist = $this->db->query("SELECT
	usergroupdetail.empID,
	empdetail.Ename2,
	empdetail.EEmail,
	srp_erp_mfq_usergroups.segmentID
FROM
	srp_erp_mfq_usergroupdetails usergroupdetail
	LEFT JOIN srp_employeesdetails empdetail ON empdetail.EIdNo = usergroupdetail.empID
	LEFT JOIN srp_erp_mfq_usergroups on srp_erp_mfq_usergroups.userGroupID = usergroupdetail.userGroupID
	where
	usergroupdetail.userGroupID IN (
SELECT
userGroupID
FROM
srp_erp_mfq_usergroups
WHERE
isActive = 1
AND groupType = 1
AND srp_erp_mfq_usergroups.segmentID = '{$value['segmentID']}' AND srp_erp_mfq_usergroups.companyID = '{$val['company_id']}') ")->result_array();


                    $detailcustomerinquiry = $this->db->query("SELECT
	srp_erp_mfq_itemmaster.itemDescription as itemdescription,
	expectedQty,
	DATE_FORMAT( expectedDeliveryDate, '%d-%m-%Y' ) as expectedDeliveryDate
	
FROM
	`srp_erp_mfq_customerinquirydetail`
	LEFT JOIN srp_erp_mfq_itemmaster on srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID 
	WHERE 
	ciMasterID = '{$value['ciMasterID']}' AND srp_erp_mfq_customerinquirydetail.companyID = '{$val['company_id']}'
")->result_array();

                    if (!empty($emaillist)) {


                        foreach ($emaillist as $email) {
                            $emailSamBody = '';
                            $param = array();
                            $param["empName"] = $email['Ename2'];
                            $param["companylogo"] =  $data["companydetails"]['company_logo'];
                            $emailSamBody .= '<!DOCTYPE html>
<html>
<head>

<style>
.detailtable {
  border-collapse: collapse;
}

.detailtable, .detailtabletd, .detailtableth {
  border: 1px solid black;
}
</style>
</head>


<body>
  <h4>' . $value['srp_erp_mfq_customerinquiry.ciCode'] . '</h4>
  <label>Contact Person :  ' . $value['contactPerson'] . '</label><br>
<label>Customer Phone No :  ' . $value['customerPhoneNo'] . ' </label><br>
<label>Customer Email : ' . $value['customerEmail'] . '  </label><br>
<label>Client Reference No :  ' . $value['referenceNo'] . ' </label><br>
<label><b>Planned Submission Date :  ' . $value['plannedsubmissiondate'] . ' </b></label><br>
<br>
<label>Description :' . $value['description'] . '  </label>
<br>
<br>
 <table style="width: 100%">
        <tbody>
	 <tr>
         <td style=""><b>Engineering</b></td>
	 <td style=""> </td>
         <td style=""> </td>
         <td style=""><b>Purchasing</b></td>
         <td style=""> </td>
         <td style=""> </td>
        </tr>
	<tr>
         <td style="">Responsible: ' . $value['Engineeringname'] . '</td>
	 <td style=""> </td>
	<td style=""> </td>
         <td style="">Responsible:' . $value['Purchasingname'] . '</td>
          <td style=""> </td>
	<td style=""> </td>
        </tr>
<tr>
         <td style="">End Date: ' . $value['engineeringEndDateformated'] . '</td>
	 <td style=""> </td>
	<td style=""> </td>
         <td style="">End Date:' . $value['purchasingEndDateDateformated'] . '</td>
          <td style=""> </td>
	<td style=""> </td>
        </tr>

</tbody>
</table>
<br>
<table style="width: 100%">
        <tbody>
	 <tr>
         <td style=""><b>Production</b></td>
	 <td style=""> </td>
         <td style=""> </td>
         <td style=""><b>QA/QC</b></td>
         <td style=""> </td>
         <td style=""> </td>
        </tr>
	<tr>
         <td style="">Responsible:' . $value['Productionname'] . '</td>
	 <td style=""> </td>
	<td style=""> </td>
         <td style="">Responsible:' . $value['qaqcname'] . '</td>
          <td style=""> </td>
	<td style=""> </td>
        </tr>
<tr>
         <td style="">End Date:' . $value['productionEndDateformated'] . ' </td>
	 <td style=""> </td>
	<td style=""> </td>
         <td style="">End Date:' . $value['QAQCEndDateDateformated'] . '</td>
          <td style=""> </td>
	<td style=""> </td>
        </tr>
</tbody>
</table>
<h4>Item Details</h4>


<table class="detailtable">
  <tr>
    <th class="detailtableth">Item Description</th>
    <th class="detailtableth">Expected Qty</th>
	<th class="detailtableth">Delivery Date</th>
  </tr>';
                            foreach ($detailcustomerinquiry as $detailval) {
                                $emailSamBody .=
                                    '<tr>
                                <td class="detailtabletd">' . $detailval['itemdescription'] . '</td>
                                <td align="right"; class="detailtabletd">' . $detailval['expectedQty'] . '</td>
                                <td class="detailtabletd">' . $detailval['expectedDeliveryDate'] . '</td>
                            </tr>';
                            }
                            $emailSamBody .= '</table>
</body>
</html>
<table border="0px">
</table>';
                            $param["body"] = $emailSamBody;
                            $mailData = [
                                'approvalEmpID' => '',
                                'documentCode' => '',
                                'toEmail' => $email['EEmail'],
                                'subject' => 'RFQ Submission Reminder - ' . $value["ciCode"] . ' - ' . $value["segmentcode"] . ' - ' . $value["CustomerNamemfq"],
                                'param' => $param
                            ];
                            send_approvalEmail_manufacturing($mailData, 1, $path);
                            $count++;
                            $summery .= $email['EEmail'] . ' <br/>';

                        }




                            /*
                                                    $param = array();
                                                    $param["empName"] = '';
                                                    $param["body"] = 'We are pleased to submit our proposal as follow. <br/>
                                                                      <table border="0px">
                                                                      </table>';
                                                    $mailData = [
                                                        'approvalEmpID' => '',
                                                        'documentCode' => '',
                                                        'toEmail' => 'aflal.abdeen@gmail.com',
                                                        'subject' => 'Project Proposal',
                                                        'param' => $param
                                                    ];
                                                    send_approvalEmail($mailData, 1, 0);*/



                    }



                }
            }

        } else {

            echo 'company not found!.';
            exit;
        }

        if ($count) {
            $mail_config['wordwrap'] = TRUE;
            $mail_config['protocol'] = 'smtp';
            $mail_config['smtp_host'] = 'smtp.sendgrid.net';
            $mail_config['smtp_user'] = 'apikey';
            $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
            $mail_config['smtp_crypto'] = 'tls';

            $mail_config['smtp_port'] = '587';
            $mail_config['crlf'] = "\r\n";
            $mail_config['newline'] = "\r\n";
            $this->load->library('email', $mail_config);

            if(hstGeras==1){
                $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
            }else{
                $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
            }
            $this->email->set_mailtype('html');
            $this->email->subject('Email Sending Summery (Customer Inquiry) - Summary on' . $day_before);
            $msg = 'Following email received Customer Inquiry Summery on ' . $day_before . '<br/><br/>' . $summery . '<br/><br/><br/><br/>This is auto generated email by ' . EMAIL_SYS_NAME;
            $this->email->message($msg);
            $this->email->to('hisham@gears-int.com');
            $tmpResult = $this->email->send();
            if ($tmpResult) {
                $this->email->clear(TRUE);
            }
        }


    }


    /* }*/
    function load_mfq_customerInquirydeadline($ciMasterID)
    {

        //  $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('DATE_FORMAT(QAQCSubmissionDate,\'%d-%m-%Y\' ) as QAQCSubmissionDatecon,DATE_FORMAT(productionSubmissionDate,\'%d-%m-%Y\' ) as productionSubmissionDatecon,DATE_FORMAT(purchasingSubmissionDate,\'%d-%m-%Y\' ) as purchasingSubmissionDatecon,DATE_FORMAT(engineeringSubmissionDate,\'%d-%m-%Y\') as engineeringSubmissionDatecon,DATE_FORMAT(engineeringEndDate,\'%d-%m-%Y\') as engineeringEndDate,DATE_FORMAT(purchasingEndDate,\'%d-%m-%Y\') as purchasingEndDate,DATE_FORMAT(productionEndDate,\'%d-%m-%Y\') as productionEndDate,DATE_FORMAT(QAQCEndDate,\'%d-%m-%Y\') as QAQCEndDate,DATE_FORMAT(documentDate,\'%d-%m-%Y\') as documentDate,DATE_FORMAT(dueDate,\'%d-%m-%Y\') as dueDate,DATE_FORMAT(deliveryDate,\'%d-%m-%Y\') as deliveryDate,srp_erp_mfq_customerinquiry.description,paymentTerm, srp_erp_mfq_customerinquiry.mfqCustomerAutoID,ciMasterID as ciMasterID,ciCode,srp_erp_mfq_customermaster.CustomerName,referenceNo,statusID,type,engineeringResponsibleEmpID,purchasingResponsibleEmpID,productionResponsibleEmpID,QAQCResponsibleEmpID,DATEDIFF(engineeringSubmissionDate,engineeringEndDate) as Engineeringnoofdays,DATEDIFF(purchasingSubmissionDate,purchasingEndDate) as purchasingnoofdays,DATEDIFF(productionSubmissionDate,productionEndDate) as productionnoofdays,DATEDIFF(QAQCSubmissionDate,QAQCEndDate) as qaqcnoofdays,DATEDIFF(deliveryDate,dueDate) AS noofdaysdelaydeliverydue,engineeringresponsible.Ename2 as engineeringResponsibleEmpName,purchasingresposible.Ename2 as purchasingResponsibleEmpName,productionresponsiblemp.Ename2 as productionResponsibleEmpName,qaqcresponsiblemp.Ename2 as qaqcResponsibleEmpName,srp_erp_mfq_customerinquiry.segmentID as rfqheadersegmentid,srp_erp_mfq_customerinquiry.contactPerson as contactpresongrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNorfq,srp_erp_mfq_customerinquiry.customerEmail as customerEmailrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNocustomer,srp_erp_mfq_customerinquiry.customerEmail as customerEmailcustomer,mfqsegment.segmentCode as department,srp_erp_mfq_customerinquiry.contactPerson as contactPersonIN');
        $this->db->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_customerinquiry.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeesdetails engineeringresponsible', 'engineeringresponsible.EIdNo = srp_erp_mfq_customerinquiry.engineeringResponsibleEmpID', 'left');
        $this->db->join('srp_employeesdetails purchasingresposible', 'purchasingresposible.EIdNo = srp_erp_mfq_customerinquiry.purchasingResponsibleEmpID', 'left');
        $this->db->join('srp_employeesdetails productionresponsiblemp', 'productionresponsiblemp.EIdNo = srp_erp_mfq_customerinquiry.productionResponsibleEmpID', 'left');
        $this->db->join('srp_employeesdetails qaqcresponsiblemp', 'qaqcresponsiblemp.EIdNo = srp_erp_mfq_customerinquiry.QAQCResponsibleEmpID', 'left');
        $this->db->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = srp_erp_mfq_customerinquiry.segmentID', 'left');
        $this->db->join('srp_erp_segment segment', 'segment.segmentID = mfqsegment.segmentID', 'left');
        $this->db->from('srp_erp_mfq_customerinquiry');
        $this->db->where('ciMasterID', $ciMasterID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function load_mfq_customerInquiryDetaildeadline($ciMasterID)
    {

        // $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquirydetail.*,DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'%d-%m-%Y\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,srp_erp_mfq_segment.description as segment,bomm.bomMasterID,IFNULL(bomm.cost,0) as estimatedCost');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_segment', 'mfqSegmentID = srp_erp_mfq_customerinquirydetail.segmentID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $result = $this->db->get()->result_array();
        return $result;
    }
    function load_companydetails($companyid)
    {
        $result  = $this->db->query("SELECT * FROM `srp_erp_company` where company_id = '{$companyid}' ")->row_array();
        return $result;
    }
}
