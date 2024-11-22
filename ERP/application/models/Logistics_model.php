<?php

class Logistics_model extends ERP_Model
{
    function save_job_request()
    {
        $date_format_policy = date_format_policy();
        $jobID = $this->input->post('jobID');
        $BLLogisticRefNo = $this->input->post('BLLogisticRefNo');
        $bookingDate = input_format_date($this->input->post('arrivalDate'), $date_format_policy);
        $companyID = $this->common_data['company_data']['company_id'];
        $internalRefNo= trim($this->input->post('internalRefNo') ?? '');

        if (!$jobID) {
            $validate_BLLogisticRefNo = $this->db->query("SELECT COUNT(jobID) as jobID FROM `srp_erp_logisticjobs` WHERE companyID = {$companyID} AND BLLogisticRefNo LIKE  '" . $BLLogisticRefNo . "'")->row_array();
        } else {
            $validate_BLLogisticRefNo = $this->db->query("SELECT COUNT(jobID) as jobID FROM `srp_erp_logisticjobs` WHERE companyID = {$companyID} AND  BLLogisticRefNo LIKE  '" . $BLLogisticRefNo . "' AND jobID <> {$jobID}")->row_array();
        }

        if (!$jobID) {
            $validate_internalRefNo = $this->db->query("SELECT COUNT(jobID) as jobID FROM `srp_erp_logisticjobs` WHERE companyID = {$companyID} AND  internalRefNo IS NOT NULL  AND trim(internalRefNo) <> '' AND internalRefNo LIKE  '" . $internalRefNo . "' ")->row_array();
        } else {
            $validate_internalRefNo = $this->db->query("SELECT COUNT(jobID) as jobID FROM `srp_erp_logisticjobs` WHERE companyID = {$companyID} AND internalRefNo IS NOT NULL  AND trim(internalRefNo) <> '' AND internalRefNo LIKE  '" . $internalRefNo . "' AND jobID <> {$jobID}")->row_array();
        }

        if(!empty($validate_BLLogisticRefNo['jobID'])) {
            $this->session->set_flashdata('e', 'BL/Logistic Reference No Already Exist');
            return array('status' => false);
        }elseif (!empty($validate_internalRefNo['jobID'])){
            $this->session->set_flashdata('e', 'House BL / Internal Reference No Already Exist');
            return array('status' => false);
        } else {
            $this->db->trans_start();

            $data['customerID'] = trim($this->input->post('customerID') ?? '');
            $data['BLLogisticRefNo'] = trim($this->input->post('BLLogisticRefNo') ?? '');
            $data['containerNo'] = trim($this->input->post('containerNo') ?? '');
            $data['shippingLine'] = trim($this->input->post('shippingLine') ?? '');
            $data['serviceTypeID'] = trim($this->input->post('serviceTypeID') ?? '');
            $data['arrivalDate'] = $bookingDate;
            $data['bookingNumber'] = trim($this->input->post('bookingNumber') ?? '');
            $data['bayanStatusID'] = trim($this->input->post('bayanStatusID') ?? '');
            $data['encodeByEmpID'] = trim($this->input->post('encodeByEmpID') ?? '');
            $data['reminderInDays'] = trim($this->input->post('reminderInDays') ?? '');
            $data['internalRefNo'] = trim($this->input->post('internalRefNo') ?? '');
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            if (trim($this->input->post('jobID') ?? '')) {
                $this->db->where('jobID', trim($this->input->post('jobID') ?? ''));
                $this->db->update('srp_erp_logisticjobs', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Updated Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $this->input->post('jobID'));
                }
            } else {
                $this->load->library('sequence');
                $data['Documentcode'] = $this->sequence->sequence_generator('JOBREQ');
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_logisticjobs', $data);
                $last_id = $this->db->insert_id();
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    return array('status' => false);
                } else {
                    $this->session->set_flashdata('s', 'Saved Successfully.');
                    $this->db->trans_commit();
                    return array('status' => true, 'last_id' => $last_id);
                }
            }
        }
    }

    function load_job_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(arrivalDate,\'' . $convertFormat . '\') AS arrivalDate');
        $this->db->where('jobID', $this->input->post('jobID'));
        return $this->db->get('srp_erp_logisticjobs')->row_array();
    }

    function delete_job_request()
    {
        $jobID = $this->input->post('jobID');
        $data['deletedYN'] = 1;
        $data['deletedByEmpID'] = $this->common_data['current_userID'];
        $data['deletedDate'] = $this->common_data['current_date'];

        $this->db->where('jobID', $jobID);
        $this->db->update('srp_erp_logisticjobs', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('s', 'Job Request Deleted Successfully');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Failed to delete Job Request');
        }
    }


    public function save_documentDescriptions()
    {
        $description = $this->input->post('description');
        //$whereIN = "( '" . join("' , '", $description) . "' )";

        $isExist = $this->db->query("SELECT description FROM srp_erp_logisticdocumentmaster WHERE description = '$description'
                                     AND companyID=" . current_companyID())->result_array();
        if (empty($isExist)) {
            $this->db->trans_start();

                $data = array(
                    'description' => $description,
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date()
                );
                $this->db->insert('srp_erp_logisticdocumentmaster', $data);
                $docID = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {

            return array('e', $description . ' is already Exists');
        }

    }

    function edit_documentDescription()
    {
        $description = $this->input->post('edit_description');
        $docID = $this->input->post('hidden-id');

        $isExist = $this->db->query("SELECT description FROM srp_erp_logisticdocumentmaster WHERE description='$description'
                                     AND docID!={$docID} AND companyID=" . current_companyID())->result_array();
        if (empty($isExist)) {
            $this->db->trans_start();
            $data = array(
                'description' => $description,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedDateTime' => current_date()
            );
            $this->db->where('docID', $docID)->where('companyID', current_companyID())
                ->update('srp_erp_logisticdocumentmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', $description . ' is already Exists');
        }
    }

    function delete_documentDescription()
    {
        $hidden_id = $this->input->post('hidden-id');

        // Check is there any service document uploaded
        $isInUse = $this->db->query("SELECT docID FROM srp_erp_logisticservicetypedocuments WHERE  docID={$hidden_id}
                                     AND companyID=" . current_companyID())->result_array();
        if (empty($isInUse)) {
            $this->db->trans_start();
            $this->db->where('docID', $hidden_id)->delete('srp_erp_logisticdocumentmaster');

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Records deleted successfully');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in deleting process');
            }
        } else {
            return array('e', 'This description is in use.');
        }
    }


    /* function save_servicetype()
    {
        $servicetype = $this->input->post('servicetype[]');

        $data = array();
        foreach ($servicetype as $key => $st) {
            $data[$key]['serviceType'] = $st;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdDateTime'] = current_date();
            $data[$key]['createdUserName'] = current_employee();
        }

        $this->db->insert_batch('srp_erp_logisticservicetypes', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }
*/
    public function save_servicetype()
    {
        $servicetype = $this->input->post('add_servicetype');
       // $item = $this->input->post('item_add');

        $isExist = $this->db->query("SELECT serviceType FROM srp_erp_logisticservicetypes WHERE serviceType = '$servicetype'
                                     AND companyID=" . current_companyID())->result_array();

        if (empty($isExist)) {
            $this->db->trans_start();
           // foreach ($servicetype as $key => $st) {
                $data = array(
                    'serviceType' => $servicetype,
                    //'itemAutoID' => $item,
                    'createdUserGroup' => current_user_group(),
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdUserName' => current_employee(),
                    'createdDateTime' => current_date()
                   );
                $this->db->insert('srp_erp_logisticservicetypes', $data);
                $docID = $this->db->insert_id();
           // }
            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }
        } else {
            return array('e', $servicetype . ' already Exists');

        }

    }

    /* function editServicetype()
        {
            $serviceType = $this->input->post('serviceType');
            $hidden_id = $this->input->post('hidden-id');

            $data = array(
                'serviceType' => $serviceType,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedDateTime' => current_date(),
                'ModifiedUserName' => current_employee(),
            );

            $this->db->where('serviceID', $hidden_id)->update('srp_erp_logisticservicetypes', $data);
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records updated successfully');
            } else {
                return array('e', 'Error in updating record');
            }
        }
    */
    function editServicetype()
    {
        $serviceType = $this->input->post('serviceType');
        //$item = $this->input->post('item');
        //$isMandatory = ($this->input->post('edit_isMandatory') == 'on') ? 1 : 0;
        $serviceID = $this->input->post('edit_hidden-id');

        $isExist = $this->db->query("SELECT serviceType FROM srp_erp_logisticservicetypes WHERE serviceType='$serviceType'
                                     AND serviceID!={$serviceID} AND companyID=" . current_companyID())->result_array();
        if (empty($isExist)) {
            $this->db->trans_start();
            $data = array(
                'serviceType' => $serviceType,
               // 'itemAutoID' => $item,
                'modifiedPCID' => current_pc(),
                'modifiedUserID' => current_userID(),
                'modifiedDateTime' => current_date(),
                'ModifiedUserName' => current_employee()
            );
            $this->db->where('serviceID', $serviceID)
                ->where('companyID', current_companyID())
                ->update('srp_erp_logisticservicetypes', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in update process');
            }
        } else {
            return array('e', $serviceType . ' already Exists');
        }
    }

    function delete_servicetype()
    {
        $hidden_id = $this->input->post('hidden-id');

        $isInUse = $this->db->query("SELECT serviceID FROM srp_erp_logisticservicetypedocuments WHERE serviceID={$hidden_id}")->row('serviceID');

        if (isset($isInUse)) {
            return array('e', 'This Service Type is in use</br>You can not delete this');
        } else {
            $this->db->where('serviceID', $hidden_id)->delete('srp_erp_logisticservicetypes');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }

    function fetch_document_detail_table()
    {
        $this->db->select('serviceDocumentID,srp_erp_logisticservicetypedocuments.docID as docID,serviceID,isMandatory,s.description as description');
        $this->db->where('serviceID', trim($this->input->post('servID') ?? ''));
        $this->db->where('srp_erp_logisticservicetypedocuments.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_logisticservicetypedocuments');
        $this->db->join('srp_erp_logisticdocumentmaster s', 's.docID = srp_erp_logisticservicetypedocuments.docID','left');

        $data['detail'] = $this->db->get()->result_array();

        //echo $this->db->last_query();
        return $data;
    }

    function save_mandatorydocument()
    {
        //$this->db->trans_start();
        $data['docID'] = $this->input->post('document');
        $isMandatory = $this->input->post('isMandatory');
        $data['isMandatory'] = ($isMandatory == 1) ? $isMandatory : 0;
        $data['serviceID'] = $this->input->post('ds_hidden-id');
        // $data['serviceID'] = $this->input->post('servID');

        $isExist = $this->db->query("SELECT serviceDocumentID FROM srp_erp_logisticservicetypedocuments WHERE docID=" . $data['docID'] . " AND
                                    serviceID=" . $data['serviceID'] . " AND companyID=" . current_companyID())->row('serviceDocumentID');
//var_dump($isExist);
        if (!empty($isExist)) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->where('serviceDocumentID', $isExist);
            $this->db->update('srp_erp_logisticservicetypedocuments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Updated Successfully.');
                $this->db->trans_commit();
                //return array('status' => true, 'last_id' => $last_id);

                return array('status' => true, 'last_id' => $this->input->post('document'));
            }
        } else {

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_logisticservicetypedocuments', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }

        }
    }

    function delete_mandatoryDocument()
    {
        $serviceDocumentID = $this->input->post('serviceDocumentID');

       // $isInUse = $this->db->query("SELECT serviceDocumentID FROM srp_erp_logisticservicetypedocuments WHERE serviceID={$hidden_id}")->row('serviceID');

        if (empty($serviceDocumentID)) {
            return array('e', 'Error in deleting process');
        } else {
            $this->db->where('serviceDocumentID', $serviceDocumentID)->delete('srp_erp_logisticservicetypedocuments');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }
    }
    function save_invoice_header()
    {
        $companyID = current_companyID();
        $uploadID = $this->input->post('uploaddetailid');
        $logisticuploaddetail = $this->db->query("select * from srp_erp_logisticuploads WHERE companyID = $companyID AND uploadID = $uploadID  ")->row_array();
        $segment = $this->db->query("select default_segment,default_segment_id,default_segment from srp_erp_company WHERE company_id = $companyID ")->row_array();
        $customercurrenyID = $this->db->query("SELECT customerCurrencyID FROM `srp_erp_customermaster` where customerAutoID = {$logisticuploaddetail['customerID']}")->row_array();
        $refnologistics = $this->db->query("SELECT containerNo,internalRefNo FROM `srp_erp_logisticjobs` where internalRefNo = '{$logisticuploaddetail['transportDocument']}'")->row_array();
        $rebate = getPolicyValues('CRP', 'All');
        $financeyearperiodYN = getPolicyValues('FPC', 'All');

        $date_format_policy = date_format_policy();
        $currentdate = input_format_date(current_date(), $date_format_policy);

        if($rebate == 1) {
            $rebateDet = $this->db->query("SELECT rebatePercentage, rebateGLAutoID FROM `srp_erp_customermaster` WHERE customerAutoID = {$logisticuploaddetail['customerID']}")->row_array();
            if(!empty($rebate)) {
                $data['rebateGLAutoID'] = $rebateDet['rebateGLAutoID'];
                $data['rebatePercentage'] = $rebateDet['rebatePercentage'];
            }
        } else {
            $data['rebateGLAutoID'] = null;
            $data['rebatePercentage'] = null;
        }


        $financeyr = $this->db->query("SELECT
	companyFinancePeriodID,
	srp_erp_companyfinanceperiod.companyFinanceYearID,
	dateFrom,
	dateTo,
	srp_erp_companyfinanceyear.beginingDate as financebeg,
	srp_erp_companyfinanceyear.endingDate as financeend
FROM
	`srp_erp_companyfinanceperiod` 
	LEFT JOIN srp_erp_companyfinanceyear on srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID
WHERE
	srp_erp_companyfinanceperiod.companyID = $companyID
	AND srp_erp_companyfinanceperiod.isActive = 1 
	AND '{$currentdate}' BETWEEN dateFrom 
	AND dateTo")->row_array();

        $companyFinanceYear = $financeyr['financebeg'].' - '.$financeyr['financeend'];
        if($financeyearperiodYN==1) {


            $FYBegin = input_format_date($financeyr['financebeg'], $date_format_policy);
            $FYEnd = input_format_date($financeyr['financeend'], $date_format_policy);
        }else
        {
            $financeYearDetails=get_financial_year($currentdate);
            if(empty($financeYearDetails)){
                $this->session->set_flashdata('e', 'Finance period not found for the selected document date');
                return array('status' => false);
                exit;
            }else{
                $FYBegin=$financeYearDetails['beginingDate'];
                $FYEnd=$financeYearDetails['endingDate'];
                $_POST['companyFinanceYear'] = $FYBegin.' - '.$FYEnd;
                $_POST['financeyear'] = $financeYearDetails['companyFinanceYearID'];
            }
            $financePeriodDetails=get_financial_period_date_wise($currentdate);

            if(empty($financePeriodDetails)){
                return array('e', 'Finance period not found for the selected document date');
                exit;
            }else{

                $_POST['financeyear_period'] = $financeyr['companyFinancePeriodID'];
            }
        }
        $segmentcode =  $segment['default_segment'];
        $segmentID =  $segment['default_segment_id'];
        $customer_arr = $this->fetch_customer_data($logisticuploaddetail['customerID']);
        $currency_code = $this->common_data['company_data']['company_default_currency'];
        $currency_ID = $this->common_data['company_data']['company_default_currencyID'];


        $data['documentID'] = 'CINV';
        $data['companyFinanceYearID'] = $financeyr['companyFinanceYearID'];
        $data['companyFinanceYear'] = $companyFinanceYear;
        $data['contactPersonName'] = '';
        $data['contactPersonNumber'] = '';
        $data['FYBegin'] = trim($FYBegin);
        $data['FYEnd'] = trim($FYEnd);
        $data['companyFinancePeriodID'] = $financeyr['companyFinancePeriodID'];
        $data['invoiceDate'] = $currentdate;
        $data['customerInvoiceDate'] = $currentdate;
        $data['invoiceDueDate'] = $currentdate;
        $data['invoiceNarration'] = 'Generated To '.$logisticuploaddetail['declarationNumber'] ;
        $data['invoiceNote'] = '';
        $data['segmentID'] = $segmentID;
        $data['segmentCode'] =$segmentcode;

        $data['invoiceType'] = 'Direct';
        $data['referenceNo'] = $logisticuploaddetail['declarationNumber'];
        $data['isPrintDN'] = 0;
        $data['customerID'] = $customer_arr['customerAutoID'];
        $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
        $data['customerName'] = $customer_arr['customerName'];
        $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
        $data['customerTelephone'] = $customer_arr['customerTelephone'];
        $data['customerFax'] = $customer_arr['customerFax'];
        $data['customerEmail'] = $customer_arr['customerEmail'];
        $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
        $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
        $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
        $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
        $data['customerReceivableType'] = $customer_arr['receivableType'];
        $data['customerCurrency'] = $customer_arr['customerCurrency'];
        $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
        $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = $currency_ID;
        $data['transactionCurrency'] = $currency_code;
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
        $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
        $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];

        //$this->load->library('sequence');
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];

        $data['logisticBLNo'] =$refnologistics['internalRefNo'];
        $data['logisticContainerNo'] = $refnologistics['containerNo'];

        $data['invoiceCode'] = 0;
        $data['deliveryNoteSystemCode'] = $this->sequence->sequence_generator('DLN');
        
        $this->db->insert('srp_erp_customerinvoicemaster', $data);
        $invoiceAutoID = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Invoice   Saved Failed');
            die();
        } else {
            update_warehouse_items();
            update_item_master();
            $this->db->trans_commit();
           /* *******************************Detail Save start********************************/

            /* $itemAutoID = $this->db->query("SELECT itemAutoID FROM `srp_erp_logisticservicetypes` WHERE serviceID = {$logisticuploaddetail['serviceTypeID']}")->row('itemAutoID');*/

            $itemAutoID_arr = $this->db->query("SELECT itemID FROM `srp_erp_logisticservicetypeitems` WHERE serviceID = {$logisticuploaddetail['serviceTypeID']}")->result_array();
            foreach ($itemAutoID_arr as $row) {
              //  echo $row['itemID'];
           
                $this->db->select('mainCategory');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemAutoID', $row['itemID']);
                $serviceitm= $this->db->get()->row_array();
                $unittransactionAmount = 0;

                $item_arr = fetch_item_data($row['itemID']);
                $salesPrice = $item_arr['companyLocalSellingPrice'];
                $current_date = current_format_date();
                $convertFormat = convert_date_format_sql();
                $policy = getPolicyValues('CPS', 'All');

                $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
                $this->db->where('invoiceAutoID',$invoiceAutoID);
                $result = $this->db->get('srp_erp_customerinvoicemaster')->row_array();
                $localCurrencyER = 1 / $result['companyLocalExchangeRate'];
                if($policy == 1 && !empty($logisticuploaddetail['customerID'])){
                    $this->db->select("salesPrice, isModificationAllowed, DATE_FORMAT(applicableDateFrom,' $convertFormat ') AS applicableDateFrom, DATE_FORMAT(applicableDateTo,' $convertFormat ') AS applicableDateTo");
                    $this->db->where('itemAutoID', $row['itemID']);
                    $this->db->where('customerAutoID', $logisticuploaddetail['customerID']);
                    $this->db->where('isActive', 1);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $customerPrice = $this->db->get('srp_erp_customeritemprices')->row_array();

                    if(!empty($customerPrice))
                    {
                        if((empty($customerPrice['applicableDateFrom']) && empty($customerPrice['applicableDateTo'])) || (strtotime($customerPrice['applicableDateFrom']) <= strtotime($current_date) && empty($customerPrice['applicableDateTo'])) || (strtotime($customerPrice['applicableDateFrom']) <= strtotime($current_date) && strtotime($customerPrice['applicableDateTo']) >= strtotime($current_date))) {
                            $salesprice = $customerPrice['salesPrice'];

                            $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                            $unittransactionAmount = $unitprice;
                        } else {

                            $salesprice = trim($salesPrice);
                            $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                            $unittransactionAmount  = $unitprice;
                        }
                    }else
                    {

                        $salesprice = trim($salesPrice);
                        $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                        $unittransactionAmount = $unitprice;
                    }


                } else {

                    $salesprice = trim($salesPrice);
                    $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                    $unittransactionAmount = $unitprice;
                }

                $data_detail['invoiceAutoID'] = $invoiceAutoID;
                $data_detail['itemAutoID'] = $row['itemID'];
                $data_detail['itemSystemCode'] = $item_arr['itemSystemCode'];
                $data_detail['itemDescription'] = $item_arr['itemDescription'];
                $data_detail['unitOfMeasure'] = $item_arr['defaultUnitOfMeasure'];
                $data_detail['unitOfMeasureID'] =  $item_arr['defaultUnitOfMeasureID'];
                $data_detail['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                $data_detail['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                $data_detail['conversionRateUOM'] = conversionRateUOM_id($data_detail['unitOfMeasureID'], $data_detail['defaultUOMID']);
                $data_detail['requestedQty'] = 1;
                $data_detail['unittransactionAmount'] = $unittransactionAmount;
                $data_detail['transactionAmount'] = round($unittransactionAmount *  $data_detail['requestedQty'], $this->common_data['company_data']['company_default_decimal']);
                $companyLocalAmount = $data_detail['transactionAmount'] / 1;
                $data_detail['companyLocalAmount'] = round($companyLocalAmount, $companyLocalAmount);
                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                $companyReportingAmount = $data_detail['transactionAmount'] /  $reporting_currency['conversion'];
                $data_detail['companyReportingAmount'] = round($companyReportingAmount,$reporting_currency['DecimalPlaces']);
                $customer_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'],   $customercurrenyID['customerCurrencyID']);

                $customerAmount = $data_detail['transactionAmount'] /  $customer_currency['conversion'];;
                $data_detail['customerAmount'] = round($customerAmount, $customer_currency['DecimalPlaces']);
                $data_detail['comment'] = '';
                $data_detail['remarks'] ='';
                $data_detail['type'] = 'Item';
                $item_data = fetch_item_data($row['itemID']);
                $data_detail['wareHouseAutoID'] = 0;
                $data_detail['wareHouseCode'] = null;
                $data_detail['wareHouseLocation'] = null;
                $data_detail['wareHouseDescription'] = null;
                $data_detail['segmentID'] = $segmentID;
                $data_detail['segmentCode'] = $segmentcode;
                $data_detail['expenseGLAutoID'] = $item_data['costGLAutoID'];
                $data_detail['expenseGLCode'] = $item_data['costGLCode'];
                $data_detail['expenseSystemGLCode'] = $item_data['costSystemGLCode'];
                $data_detail['expenseGLDescription'] = $item_data['costDescription'];
                $data_detail['expenseGLType'] = $item_data['costType'];
                $data_detail['revenueGLAutoID'] = $item_data['revanueGLAutoID'];
                $data_detail['revenueGLCode'] = $item_data['revanueGLCode'];
                $data_detail['revenueSystemGLCode'] = $item_data['revanueSystemGLCode'];
                $data_detail['revenueGLDescription'] = $item_data['revanueDescription'];
                $data_detail['revenueGLType'] = $item_data['revanueType'];
                $data_detail['assetGLAutoID'] = $item_data['assteGLAutoID'];
                $data_detail['assetGLCode'] = $item_data['assteGLCode'];
                $data_detail['assetSystemGLCode'] = $item_data['assteSystemGLCode'];
                $data_detail['assetGLDescription'] = $item_data['assteDescription'];
                $data_detail['assetGLType'] = $item_data['assteType'];
                $data_detail['companyLocalWacAmount'] = $item_data['companyLocalWacAmount'];
                $data_detail['itemCategory'] = $item_data['mainCategory'];
                $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                $data_detail['modifiedDateTime'] = $this->common_data['current_date'];

                $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                $data_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                $data_detail['createdPCID'] = $this->common_data['current_pc'];
                $data_detail['createdUserID'] = $this->common_data['current_userID'];
                $data_detail['createdUserName'] = $this->common_data['current_user'];
                $data_detail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_customerinvoicedetails', $data_detail);
                $customerinvoicedetail = $this->db->insert_id();
            }                  
            $rebate = $this->db->query("SELECT rebatePercentage FROM srp_erp_customerinvoicemaster WHERE invoiceAutoID = {$invoiceAutoID}")->row_array();
            if(!empty($rebate['rebatePercentage'])) {
                $this->calculate_rebate_amount($invoiceAutoID);
            }
            $this->db->trans_complete();
            $data_logisticupdate['invoiceAutoID'] = $invoiceAutoID;
            $this->db->where('uploadID', $uploadID);
            $this->db->update('srp_erp_logisticuploads', $data_logisticupdate);
             /* *******************************Detail Save end********************************/

        /* *******************************Invoice Approval Start********************************/
            $total_amount = 0;
            $tax_total = 0;
            $t_arr = array();
            $companyID = current_companyID();
            $currentuser  = current_userID();
            $locationwisecodegenerate = getPolicyValues('LDG', 'All');
            $locationemployee = $this->common_data['emplanglocationid'];

            $this->db->select('invoiceDetailsAutoID');
            $this->db->where('invoiceAutoID', $invoiceAutoID);
            $this->db->from('srp_erp_customerinvoicedetails');
            $results = $this->db->get()->result_array();
            if (empty($results)) {
                return array('w', 'There are no records to confirm this document!');
            } else
            {
                $this->db->select('invoiceAutoID');
                $this->db->where('invoiceAutoID', $invoiceAutoID);
                $this->db->where('confirmedYN', 1);
                $this->db->from('srp_erp_customerinvoicemaster');
                $Confirmed = $this->db->get()->row_array();
                if (!empty($Confirmed)) {
                    return array('w', 'Document already confirmed');
                }else
                {
                    $this->load->library('Approvals');
                    $this->db->select('documentID,invoiceCode,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID');
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $master_dt = $this->db->get()->row_array();
                    $this->load->library('sequence');
                    $lenth=strlen($master_dt['invoiceCode']);
                    if($lenth == 1){
                        if($locationwisecodegenerate == 1)
                        {
                            $this->db->select('locationID');
                            $this->db->where('Erp_companyID', $companyID);
                            $this->db->where('EIdNo', $currentuser);
                            $this->db->from('srp_employeesdetails');
                            $location = $this->db->get()->row_array();
                            if ((empty($location)) || ($location ==' ')) {
                                return array('w' ,'Location is not assigned for current employee');
                            }else
                            {
                                if($locationemployee!='')
                                {
                                    $codegerator = $this->sequence->sequence_generator_location($master_dt['documentID'],$master_dt['companyFinanceYearID'], $locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                                }else
                                {
                                    return array('w' ,'Location is not assigned for current employee');
                                }
                            }
                        }
                        else
                        {
                            $codegerator = $this->sequence->sequence_generator_fin($master_dt['documentID'],$master_dt['companyFinanceYearID'],$master_dt['invYear'],$master_dt['invMonth']);
                        }
                        $invcod = array(
                            'invoiceCode' => $codegerator,
                        );
                        $this->db->where('invoiceAutoID', $invoiceAutoID);
                        $this->db->update('srp_erp_customerinvoicemaster', $invcod);
                    }
                    $this->db->select('invoiceAutoID, invoiceCode, documentID,transactionCurrency, transactionExchangeRate, companyLocalExchangeRate, companyReportingExchangeRate,customerCurrencyExchangeRate,DATE_FORMAT(invoiceDate, "%Y") as invYear,DATE_FORMAT(invoiceDate, "%m") as invMonth,companyFinanceYearID,invoiceDate ');
                    $this->db->where('invoiceAutoID', $invoiceAutoID);
                    $this->db->from('srp_erp_customerinvoicemaster');
                    $master_data = $this->db->get()->row_array();
                    $sql = "SELECT SUM(srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM) AS qty,srp_erp_warehouseitems.currentStock,
	                        (srp_erp_warehouseitems.currentStock - SUM(srp_erp_customerinvoicedetails.requestedQty / srp_erp_customerinvoicedetails.conversionRateUOM)) AS stock,
	                        srp_erp_warehouseitems.itemAutoID,srp_erp_customerinvoicedetails.wareHouseAutoID FROM srp_erp_customerinvoicedetails
                            INNER JOIN srp_erp_warehouseitems ON srp_erp_warehouseitems.itemAutoID = srp_erp_customerinvoicedetails.itemAutoID
                            JOIN `srp_erp_itemmaster` ON `srp_erp_customerinvoicedetails`.`itemAutoID` = `srp_erp_itemmaster`.`itemAutoID`
                            AND srp_erp_customerinvoicedetails.wareHouseAutoID = srp_erp_warehouseitems.wareHouseAutoID
                            WHERE invoiceAutoID = '{$invoiceAutoID}' AND (mainCategory != 'Service' AND mainCategory != 'Non Inventory') GROUP BY itemAutoID HAVING stock < 0";
                    $item_low_qty = $this->db->query($sql)->result_array();
                    if (!empty($item_low_qty)) {
                        return array('e', 'Some Item quantities are not sufficient to confirm this transaction.',$item_low_qty);
                    }
                    $autoApproval= get_document_auto_approval('CINV');

                    if($autoApproval==0){
                        $approvals_status = $this->approvals->auto_approve($master_data['invoiceAutoID'], 'srp_erp_customerinvoicemaster','invoiceAutoID', 'CINV',$master_data['invoiceCode'],$master_data['invoiceDate']);
                    }elseif($autoApproval==1){
                        $approvals_status = $this->approvals->CreateApproval($master_data['documentID'], $master_data['invoiceAutoID'], $master_data['invoiceCode'], 'Invoice', 'srp_erp_customerinvoicemaster', 'invoiceAutoID',0,$master_data['invoiceDate']);
                    }else{
                        return array('e', 'Approval levels are not set for this document');
                        exit;
                    }
                    if ($approvals_status == 1) {
                        /** item Master Sub check */
                        $validate = $this->validate_itemMasterSub($invoiceAutoID);
                        /** end of item master sub */
                        if ($validate) {
                            $this->db->select_sum('transactionAmount');
                            $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                            $transaction_total_amount = $this->db->get('srp_erp_customerinvoicedetails')->row('transactionAmount');

                            $this->db->select_sum('totalAfterTax');
                            $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                            $item_tax = $this->db->get('srp_erp_customerinvoicedetails')->row('totalAfterTax');
                            $total_amount = ($transaction_total_amount - $item_tax);
                            $this->db->select('taxDetailAutoID,supplierCurrencyExchangeRate,companyReportingExchangeRate ,companyLocalExchangeRate ,taxPercentage');
                            $this->db->where('InvoiceAutoID', $master_data['invoiceAutoID']);
                            $tax_arr = $this->db->get('srp_erp_customerinvoicetaxdetails')->result_array();
                            for ($x = 0; $x < count($tax_arr); $x++) {
                                $tax_total_amount = (($tax_arr[$x]['taxPercentage'] / 100) * $total_amount);
                                $t_arr[$x]['taxDetailAutoID'] = $tax_arr[$x]['taxDetailAutoID'];
                                $t_arr[$x]['transactionAmount'] = $tax_total_amount;
                                $t_arr[$x]['supplierCurrencyAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['supplierCurrencyExchangeRate']);
                                $t_arr[$x]['companyLocalAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyLocalExchangeRate']);
                                $t_arr[$x]['companyReportingAmount'] = ($t_arr[$x]['transactionAmount'] / $tax_arr[$x]['companyReportingExchangeRate']);
                                $tax_total = $t_arr[$x]['transactionAmount'];
                            }
                            /*updating transaction amount using the query used in the master data table */
                            $companyID=current_companyID();
                            $invautoid=$invoiceAutoID;
                            $r1 = "SELECT
	`srp_erp_customerinvoicemaster`.`invoiceAutoID` AS `invoiceAutoID`,
	`srp_erp_customerinvoicemaster`.`companyLocalExchangeRate` AS `companyLocalExchangeRate`,
	`srp_erp_customerinvoicemaster`.`companyLocalCurrencyDecimalPlaces` AS `companyLocalCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`companyReportingExchangeRate` AS `companyReportingExchangeRate`,
	`srp_erp_customerinvoicemaster`.`companyReportingCurrencyDecimalPlaces` AS `companyReportingCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`customerCurrencyExchangeRate` AS `customerCurrencyExchangeRate`,
	`srp_erp_customerinvoicemaster`.`customerCurrencyDecimalPlaces` AS `customerCurrencyDecimalPlaces`,
	`srp_erp_customerinvoicemaster`.`transactionCurrencyDecimalPlaces` AS `transactionCurrencyDecimalPlaces`,

	(
		IFNULL(addondet.taxPercentage, 0) / 100
	) * (
		IFNULL(det.transactionAmount, 0) - IFNULL(det.detailtaxamount, 0) - (
			(
				IFNULL(
					gendiscount.discountPercentage,
					0
				) / 100
			) * IFNULL(det.transactionAmount, 0)
		) + IFNULL(
			genexchargistax.transactionAmount,
			0
		)
	) + IFNULL(det.transactionAmount, 0) - (
		(
			IFNULL(
				gendiscount.discountPercentage,
				0
			) / 100
		) * IFNULL(det.transactionAmount, 0)
	) + IFNULL(
		genexcharg.transactionAmount,
		0
	) AS total_value

FROM
	`srp_erp_customerinvoicemaster`
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		sum(totalafterTax) AS detailtaxamount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicedetails
	GROUP BY
		invoiceAutoID
) det ON (
	`det`.`invoiceAutoID` = srp_erp_customerinvoicemaster.invoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(taxPercentage) AS taxPercentage,
		InvoiceAutoID
	FROM
		srp_erp_customerinvoicetaxdetails
	GROUP BY
		InvoiceAutoID
) addondet ON (
	`addondet`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(discountPercentage) AS discountPercentage,
		invoiceAutoID
	FROM
		srp_erp_customerinvoicediscountdetails
	GROUP BY
		invoiceAutoID
) gendiscount ON (
	`gendiscount`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoiceextrachargedetails
	WHERE
		isTaxApplicable = 1
	GROUP BY
		invoiceAutoID
) genexchargistax ON (
	`genexchargistax`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
LEFT JOIN (
	SELECT
		SUM(transactionAmount) AS transactionAmount,
		invoiceAutoID
	FROM
		srp_erp_customerinvoiceextrachargedetails
	GROUP BY
		invoiceAutoID
) genexcharg ON (
	`genexcharg`.`InvoiceAutoID` = srp_erp_customerinvoicemaster.InvoiceAutoID
)
WHERE
	`companyID` = $companyID
and srp_erp_customerinvoicemaster.invoiceAutoID= $invautoid ";
                            $totalValue = $this->db->query($r1)->row_array();
                            $data = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user'],
                                'transactionAmount' => (round($totalValue['total_value'],$totalValue['transactionCurrencyDecimalPlaces'])),
                                'companyLocalAmount' => (round($totalValue['total_value'] / $totalValue['companyLocalExchangeRate'],$totalValue['companyLocalCurrencyDecimalPlaces'])),
                                'companyReportingAmount' => (round($totalValue['total_value'] / $totalValue['companyReportingExchangeRate'],$totalValue['companyReportingCurrencyDecimalPlaces'])),
                                'customerCurrencyAmount' => (round($totalValue['total_value'] / $totalValue['customerCurrencyExchangeRate'],$totalValue['customerCurrencyDecimalPlaces'])),
                            );
                            $this->db->where('invoiceAutoID', $invoiceAutoID);
                            $this->db->update('srp_erp_customerinvoicemaster', $data);
                            if (!empty($t_arr)) {
                                $this->db->update_batch('srp_erp_customerinvoicetaxdetails', $t_arr, 'taxDetailAutoID');
                            }
                        } else {
                            return array('e', 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');
                            /*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*//*return array('error' => 1, 'message' => 'Please complete your sub item configurations<br/><br/> Please add sub item/s before confirm this document.');*/
                            exit;

                        }


                    }elseif($approvals_status == 3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        exit;
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        //$this->session->set_flashdata('e', 'Supplier Invoice Detail : ' . $data['GLDescription']. '  Saved Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                        /* return array('error' => 0, 'message' => 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());*/
                        return array('e', 'Supplier Invoice Detail : ' . $data['GLDescription'] . '  Saved Failed ' . $this->db->_error_message());
                        //return array('status' => false);
                    } else {
                        $autoApproval= get_document_auto_approval('CINV');

                        if($autoApproval==0) {
                            $result = $this->save_invoice_approval(0, $master_data['invoiceAutoID'], 1, 'Auto Approved');
                            if($result){
                                $this->db->trans_commit();
                              //  return array('s', 'Document confirmed successfully');
                            }
                        }else{
                            $this->db->trans_commit();
                           // return array('s', 'Document confirmed successfully');
                        }
                    }


                }
            }
        /* *******************************Invoice Approval End********************************/
            return array('s', 'Invoice Saved Successfully',$invoiceAutoID);





        }


        }
    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }
    function calculate_rebate_amount($invoiceAutoID)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $master = $this->db->query("SELECT SUM(srp_erp_customerinvoicedetails.transactionAmount) as transactionAmount, srp_erp_customerinvoicemaster.rebatePercentage
                FROM srp_erp_customerinvoicedetails 
                JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_customerinvoicedetails.invoiceAutoID
                WHERE srp_erp_customerinvoicedetails.invoiceAutoID = {$invoiceAutoID} AND srp_erp_customerinvoicedetails.companyID = {$companyID}")->row_array();

        $discount = $this->db->query("SELECT SUM(discountPercentage) AS discountPercentage FROM srp_erp_customerinvoicediscountdetails WHERE invoiceAutoID = {$invoiceAutoID} AND isChargeToExpense = 0 AND companyID = {$companyID}")->row_array();
        $extraCharge = $this->db->query("SELECT SUM(transactionAmount) AS extracharge FROM srp_erp_customerinvoiceextrachargedetails WHERE invoiceAutoID = {$invoiceAutoID} AND companyID = {$companyID}")->row_array();
        $totalDiscount = 0;
        if(!empty($discount)) {
            $totalDiscount += $master['transactionAmount'] * ($discount['discountPercentage'] / 100);
        }
        if(!empty($extraCharge)) {
            $totalDiscount = $totalDiscount - $extraCharge['extracharge'];
        }
        $totalAmount = $master['transactionAmount'] - $totalDiscount;

        $rebateTotal = $totalAmount * ($master['rebatePercentage'] / 100);

        $data['rebateAmount'] = $rebateTotal;

        $this->db->where('invoiceAutoID', $invoiceAutoID);
        $this->db->update('srp_erp_customerinvoicemaster', $data);
    }
    function validate_itemMasterSub($itemAutoID)
    {
        $query1 = "SELECT
                        count(*) AS countAll 
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster_sub subItemMaster ON subItemMaster.soldDocumentDetailID = cinvDetail.invoiceDetailsAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1 ";
        $r1 = $this->db->query($query1)->row_array();

        $query2 = "SELECT
                        SUM(cinvDetail.requestedQty) AS totalQty
                    FROM
                        srp_erp_customerinvoicemaster cinv
                    LEFT JOIN srp_erp_customerinvoicedetails cinvDetail ON cinv.invoiceAutoID = cinvDetail.invoiceAutoID
                    LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = cinvDetail.itemAutoID
                    WHERE
                        cinv.invoiceAutoID = '" . $itemAutoID . "'
                    AND itemmaster.isSubitemExist = 1";


        $r2 = $this->db->query($query2)->row_array();


        if (empty($r1) && empty($r2)) {
            $validate = true;
        } else if (empty($r1) || $r1['countAll'] == 0) {
            $validate = true;
        } else {
            if ($r1['countAll'] == $r2['totalQty']) {
                $validate = true;
            } else {
                $validate = false;
            }
        }
        return $validate;

    }

    function load_servicetype_items_details(){
        /*get post value */
        $serviceID = $this->input->post('serviceID');
        $companyID = $this->common_data['company_data']['company_id'];
        $where = " `masterTbl`.`serviceID` = $serviceID 
            AND `itemmaster`.`deletedYN` = 0 
            AND `itemmaster`.`isActive` = 1
            AND `itemmaster`.`mainCategory` = 'Service'
            AND itemmaster.companyID = $companyID ";
        $this->db->select(' masterTbl.serviceItemID AS serviceItemID,
            `masterTbl`.`serviceID`,
            `itemmaster`.`deletedYN`,
            `itemmaster`.`mainCategory` AS mainCategory,
            `itemmaster`.`seconeryItemCode` AS seconeryItemCode,
            `itemmaster`.`itemDescription` AS itemDescription,
            `itemmaster`.`isActive`,
            `itemmaster`.`itemSystemCode` AS itemSystemCode,
            itemmaster.companyID ');
        $this->db->from('`srp_erp_logisticservicetypeitems` `masterTbl`');
        $this->db->join('srp_erp_itemmaster itemmaster', 'masterTbl.itemID = itemmaster.itemAutoID ', 'left');
        $this->db->where($where);
        $result = $this->db->get()->result_array();
        return $result;

    }

    function delete_servicetype_item()
    {
        $serviceItemID = $this->input->post('serviceItemID');
        $this->db->where('serviceItemID', $serviceItemID);
        $results = $this->db->delete('srp_erp_logisticservicetypeitems');
        if ($results) {
            return array('error' => 0, 'message' => 'Record Deleted Successfully ');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact the system team!');
        }
    }

    function load_servicetype_items()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $keyword = $this->input->post('keyword');
        $serviceID = $this->input->post('serviceID');

        if (isset($keyword) && !empty($keyword)) {
            $where = "itemmaster.companyID = $companyid AND `itemmaster`.`deletedYN` = 0 
            AND `itemmaster`.`isActive` = 1
            AND `itemmaster`.`mainCategory` = 'Service'
            AND (`itemmaster`.`itemSystemCode` LIKE '%$keyword%' ESCAPE '!'
    OR `itemmaster`.`itemDescription` LIKE '%$keyword%' ESCAPE '!' )
            ";
        } else {
            $where = "itemmaster.companyID = $companyid AND `itemmaster`.`deletedYN` = 0 
            AND `itemmaster`.`isActive` = 1
            AND `itemmaster`.`mainCategory` = 'Service'";
        }

     /*   $where = "itemmaster.companyID = $companyid AND `itemmaster`.`deletedYN` = 0 
            AND `itemmaster`.`isActive` = 1
            AND `itemmaster`.`mainCategory` = 'Service'";*/
        $this->db->select('servicetypeitems.serviceItemID AS serviceItemID, itemmaster.*');
        $this->db->from('srp_erp_itemmaster itemmaster');
        $this->db->join('(SELECT * FROM srp_erp_logisticservicetypeitems WHERE `serviceID` = ' . $serviceID . '  ) AS servicetypeitems', '`servicetypeitems`.`itemID` = `itemmaster`.`itemAutoID`', 'left');
        $this->db->where($where);
        /*if (isset($keyword) && !empty($keyword)) {
            $this->db->like('itemmaster.itemSystemCode', $keyword);
             $this->db->or_like('itemmaster.itemDescription', $keyword);
        }*/
        $this->db->limit(20);

        $result = $this->db->get()->result_array();
        return $result;
    }

    function save_servicetypeItem()
    {
        $serviceID = trim($this->input->post('serviceID') ?? '');
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        $this->db->select('*');
        $this->db->where('itemID', $itemAutoID);
        $this->db->where('serviceID', $serviceID);
        $output = $this->db->get('srp_erp_logisticservicetypeitems')->row_array();

        if (empty($output)) {
            $this->db->trans_start();
            $data['serviceID'] = $serviceID;
            $data['itemID'] = $itemAutoID;
            $data['companyID'] = current_companyID();
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            
            $data['timestamp'] = format_date_mysql_datetime();
            $this->db->insert('srp_erp_logisticservicetypeitems', $data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->_error_message();
                return array('error' => 1, 'message' => 'Error: ' . $error);

            } else {
                $this->db->trans_commit();
                return array('error' => 0, 'message' => 'Record Added Successfully.', 'code' => $serviceID);
            }

        } else {
            return array('error' => 1, 'message' => 'This Item is already added');
        }


    }

    public function save_status()
    {
        $statusID = $this->input->post('hidden_statusid');
        $statusDescription = $this->input->post('statusDescription');
        $statusType = $this->input->post('statusType');
        $companyID = current_companyID();
        //$whereIN = "( '" . join("' , '", $description) . "' )";
        if (!empty($statusID)) {
            $isExist = $this->db->query("SELECT statusDescription FROM srp_erp_logisticstatus WHERE statusDescription = '$statusDescription' AND statusID != '$statusID' AND type ='$statusType'  AND companyID = '$companyID' " )->result_array();
        }else{
            $isExist = $this->db->query("SELECT statusDescription FROM srp_erp_logisticstatus WHERE statusDescription = '$statusDescription' AND type ='$statusType' AND companyID = '$companyID' " )->result_array();
        }
        if (empty($isExist)) {
            //$this->db->trans_start();
            if (!empty($statusID)) {
                $data = array(
                    'type' => $statusType,
                    'statusDescription' => $statusDescription,
                    'companyID' => current_companyID()
                );
                $this->db->where('statusID', $statusID);
                $result = $this->db->update('srp_erp_logisticstatus', $data);
                if ($result) {
                    return array('s', 'Records Updated Successfully.');
                }
            }else{
                $data = array(
                    'type' => $statusType,
                    'statusDescription' => $statusDescription,
                    'createdUserGroup' => current_user_group(),
                    'companyID' => current_companyID(),
                    'createdPCID' => current_pc(),
                    'createdUserID' => current_userID(),
                    'createdDateTime' => current_date(),
                    'createdUserName' => current_employee(),
                    'timestamp' =>format_date_mysql_datetime()
                );
                $result = $this->db->insert('srp_erp_logisticstatus', $data);
                $statusID = $this->db->insert_id();
                if ($result) {
                    return array('s', 'Records Added Successfully.');
                }
            }

          /*  $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Created Successfully.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');
            }*/
        } else {
            return array('e', $statusDescription . ' is already Exists');
        }
    }

    function delete_status()
    {
        $statusID = $this->input->post('hidden-id');
        $companyID = current_companyID();
        // Check if status used in uploads
        $isInUse = $this->db->query("SELECT partialReleasedID,processingStatusID,declarationStatusID,paymentStatusID,reviewStatusID  
                    FROM srp_erp_logisticuploads WHERE  partialReleasedID = {$statusID} OR processingStatusID = {$statusID}	OR
                     declarationStatusID = {$statusID} OR paymentStatusID = {$statusID} OR reviewStatusID = {$statusID} AND companyID = {$companyID} ")->result_array();

        if (empty($isInUse)) {

            $this->db->trans_start();
            $this->db->where('statusID', $statusID)->delete('srp_erp_logisticstatus');

            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Records deleted successfully');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in deleting process');
            }
        } else {
           return array('e', 'This Status Description is in use.');
        }
    }
}