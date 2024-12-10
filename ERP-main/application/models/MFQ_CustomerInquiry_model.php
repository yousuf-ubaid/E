<?php

class MFQ_CustomerInquiry_model extends ERP_Model
{
    function save_CustomerInquiry()
    {
        $last_id = "";
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $documentDate = input_format_date(trim($this->input->post('documentDate') ?? ''), $date_format_policy);
        $deliveryDate = input_format_date(trim($this->input->post('deliveryDate') ?? ''), $date_format_policy);
        $dueDate = input_format_date(trim($this->input->post('dueDate') ?? ''), $date_format_policy);
        $transactioncurreny = explode('|',$this->input->post('transactioncurrency'));
        $flowserve = getPolicyValues('MANFL', 'All');

        if($flowserve=='GCC'){
            $this->db->select('mfq.mfqSegmentID');
            $this->db->from('srp_erp_segment as seg');
            $this->db->join('srp_erp_mfq_segment as mfq', 'mfq.segmentCode = seg.segmentCode');
            $this->db->where('seg.companyID', current_companyID());
            $this->db->where('seg.isDefault', 1);
            $query = $this->db->get();
            $result = $query->row();
            $segmentid = $result->mfqSegmentID;
        }
        else{
            $segmentid = $this->input->post('DepartmentID');
        }
        $engineeringemployee = $this->input->post('engineeringemployee');
        $financeemployee = $this->input->post('financeemployee');
        $purchasingemployee = $this->input->post('purchasingemployee');
        $productionemployee = $this->input->post('productionemployee');
        $qaqcemployee = $this->input->post('qaqcemployee');
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
        $referenceNo = $this->input->post('referenceNo');
        $manufacturingType = $this->input->post('manufacturingType');
        $description = $this->input->post('description');

        $statusID = 1;
        $type = $this->input->post('type');
        $micoda = $this->input->post('micoda');
        $sourceID = $this->input->post('sourceID');
        $nrfqStatus = $this->input->post('rfq_status');
        $ndocumentStatus = $this->input->post('document_status');
        $norderStatus = $this->input->post('order_status');
        $cat = $this->input->post('cat');
        $submission_status = $this->input->post('submission_status');

        $companyid = current_companyID();
       
        $customeremail = $this->input->post('contactpersonemail');
        $contactpersonname = $this->input->post('contactpersonname');
        $customertp = $this->input->post('customerphone');
        $remainingdays = $this->input->post('remainindays');
        $prpengineer = $this->input->post('prpengineer');
        if($flowserve =='FlowServe'){
            $order_job = $this->input->post('order_job');
            $this->db->set('mfqJobMasterID', $order_job);
        }

        if($flowserve =='Micoda'){
            $salesmanagerid = $this->input->post('SalesManagerID');
            $this->db->set('SalesManagerID', $salesmanagerid);
            $estimatedEmpID = $this->input->post('estimatedEmpID');
            $this->db->set('estimatedEmpID', $estimatedEmpID);
        }

        $EngineeringDeadLine = input_format_date(trim($this->input->post('EngineeringDeadLine') ?? ''), $date_format_policy);

        $purchasingDeadLine = input_format_date(trim($this->input->post('purchasingDeadLine') ?? ''), $date_format_policy);
        $DeadLineproduction = input_format_date(trim($this->input->post('DeadLineproduction') ?? ''), $date_format_policy);
        $DeadLineqaqc = input_format_date(trim($this->input->post('DeadLineqaqc') ?? ''), $date_format_policy);
        $DeadLineqaqc = input_format_date(trim($this->input->post('DeadLineqaqc') ?? ''), $date_format_policy);
        $submissiondatDeadLineengineering = input_format_date(trim($this->input->post('submissiondatDeadLine') ?? ''), $date_format_policy);
        $submissiondatPurchasing = input_format_date(trim($this->input->post('submissiondatDeadLinepurchasing') ?? ''), $date_format_policy);

        $submissiondatDeadLineproduction = input_format_date(trim($this->input->post('submissiondatDeadLineproduction') ?? ''), $date_format_policy);
        $submissiondatDeadLinefinance= input_format_date(trim($this->input->post('submissiondatDeadLinefinance') ?? ''), $date_format_policy);
        $submissiondatqaqc = input_format_date(trim($this->input->post('submissiondateqaqcDeadLinepurchasing') ?? ''), $date_format_policy);
        $deadlinefinance = input_format_date(trim($this->input->post('DeadLinefinance') ?? ''), $date_format_policy);

        $expectedweeks = $this->input->post('expectedDeliveryWeeks');
        $expecteddate = ''; 
        if (!$this->input->post('ciMasterID')) {
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_customerinquiry', 'ciMasterID', 'companyID');
            $codes = $this->sequence->sequence_generator('CI', $serialInfo['serialNo']);
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('ciCode', $codes);
            $this->db->set('documentDate', $documentDate);
            $this->db->set('deliveryDate', $deliveryDate);
            $this->db->set('dueDate', $dueDate);
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('referenceNo', $this->input->post('referenceNo'));
            $this->db->set('statusID', $this->input->post('statusID'));
            $this->db->set('type', $this->input->post('type'));
            $this->db->set('manufacturingType', $this->input->post('manufacturingType'));
            $this->db->set('remindEmailBefore', $remainingdays);
            $this->db->set('proposalEngineerID', $prpengineer);
            $this->db->set('locationAssigned', $micoda);
            $this->db->set('inquirySource', $sourceID);
            $this->db->set('rfqStatus', $nrfqStatus);
            $this->db->set('documentStatus', $ndocumentStatus);
            $this->db->set('orderStatus', $norderStatus);
            $this->db->set('category', $cat);
            $this->db->set('submissionStatus', $submission_status);
            $this->db->set('currencyID',trim($this->input->post('currencyID') ?? '')) ;
            $this->db->set('transactionCurrencyID',trim($this->input->post('currencyID') ?? '')) ;
            $this->db->set('transactionCurrency',$transactioncurreny[0]) ;
            $this->db->set('transactionExchangeRate',1) ;
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->input->post('currencyID')));


            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']) ;
            $this->db->set('companyLocalCurrency',$this->common_data['company_data']['company_default_currency']) ;
            $default_currency = currency_conversionID(trim($this->input->post('currencyID') ?? ''),$this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyLocalCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']) ;
            $this->db->set('companyReportingCurrency',$this->common_data['company_data']['company_reporting_currency']) ;
            $default_currency = currency_conversionID(trim($this->input->post('currencyID') ?? ''),$this->common_data['company_data']['company_reporting_currencyID']);
            $this->db->set('companyReportingExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyReportingCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);



            if ($this->input->post('submissiondatDeadLine') != '') {
                $this->db->set('engineeringSubmissionDate', $submissiondatDeadLineengineering);
            } else {
                $this->db->set('engineeringSubmissionDate', null);
            }
            if ($this->input->post('submissiondatDeadLinepurchasing') != '') {
                $this->db->set('purchasingSubmissionDate', $submissiondatPurchasing);
            } else {
                $this->db->set('purchasingSubmissionDate', null);
            }
            if ($this->input->post('submissiondatDeadLineproduction') != '') {
                $this->db->set('productionSubmissionDate', $submissiondatDeadLineproduction);
            } else {
                $this->db->set('productionSubmissionDate', null);
            }
            if ($this->input->post('submissiondateqaqcDeadLinepurchasing') != '') {
                $this->db->set('QAQCSubmissionDate', $submissiondatqaqc);
            } else {
                $this->db->set('QAQCSubmissionDate', null);
            }
            // Finance subimission Dead Line
            if ($this->input->post('submissiondatDeadLinefinance') != '') {
                $this->db->set('financeSubmissionDate', $submissiondatDeadLinefinance);
            } else {
                $this->db->set('financeSubmissionDate', null);
            }


            $this->db->set('segmentID', $segmentid);
            $this->db->set('contactPerson', $contactpersonname);
            $this->db->set('customerPhoneNo', $customertp);
            $this->db->set('customerEmail', $customeremail);
            //$this->db->set('paymentTerm', $this->input->post('paymentTerm'));


            if (is_array($engineeringemployee)) {
               
                $engineeringResponsibleEmpID = implode(',', $engineeringemployee);
            } else {
                $engineeringResponsibleEmpID = '';
            }
            $this->db->set('engineeringResponsibleEmpID', $engineeringResponsibleEmpID);

            
            if ($this->input->post('EngineeringDeadLine') != '') {
                $this->db->set('engineeringEndDate', $EngineeringDeadLine);
            } else {
                $this->db->set('engineeringEndDate', null);
            }

            if (is_array($purchasingemployee)) {
               
                $purchasingResponsibleEmpID = implode(',', $purchasingemployee);
            } else {
                $purchasingResponsibleEmpID = '';
            }
            $this->db->set('purchasingResponsibleEmpID', $purchasingResponsibleEmpID);
            

            if ($this->input->post('purchasingDeadLine') != '') {
                $this->db->set('purchasingEndDate', $purchasingDeadLine);
            } else {
                $this->db->set('purchasingEndDate', null);
            }

            if (is_array($productionemployee)) {
               
                $productionResponsibleEmpID = implode(',', $productionemployee);
            } else {
                $productionResponsibleEmpID = '';
            }
            $this->db->set('productionResponsibleEmpID', $productionResponsibleEmpID);
            

            if ($this->input->post('DeadLineproduction') != '') {
                $this->db->set('productionEndDate', $DeadLineproduction);
            } else {
                $this->db->set('productionEndDate', null);
            }

            // Finance employee and end date
            if (is_array($financeemployee)) {   
                $financeResponsibleEmpID = implode(',', $financeemployee);
            } else {
                $financeResponsibleEmpID = '';
            }
            $this->db->set('financeResponsibleEmpID', $financeResponsibleEmpID);
           

            if ($this->input->post('DeadLinefinance') != '') {
                $this->db->set('financeEndDate', $deadlinefinance);
            } else {
                $this->db->set('DeadLinefinance', null);
            }

            if (is_array($qaqcemployee)) {
               
                $QAQCResponsibleEmpID = implode(',', $qaqcemployee);
            } else {
                $QAQCResponsibleEmpID = '';
            }
            $this->db->set('QAQCResponsibleEmpID', $QAQCResponsibleEmpID);
            
            if ($this->input->post('DeadLineqaqc') != '') {
                $this->db->set('QAQCEndDate', $DeadLineqaqc);
            } else {
                $this->db->set('QAQCEndDate', null);
            }
            $this->db->set('quotationStatus', 0);
            
            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_customerinquiry');
            $last_id = $this->db->insert_id();

            ///add department details

            if($this->input->post('engineering_tab_id')==1){

                $data_inquiry_department_details['ciMasterID']=$last_id;
                $data_inquiry_department_details['companyID']=$companyid;

                if ($this->input->post('submissiondatDeadLine') != '') {
                    $data_inquiry_department_details['submissionDate']=$submissiondatPurchasing;
                } else {
                    $data_inquiry_department_details['submissionDate']=null;
                }

                // $data_inquiry_department_details['responsibleEmpID']=$this->input->post('engineeringemployee');
                $engineeringEmployees = $this->input->post('engineeringemployee');
                if (is_array($engineeringEmployees)) {
                    $data_inquiry_department_details['responsibleEmpID'] = implode(',', $engineeringEmployees);
                } else {
                    $data_inquiry_department_details['responsibleEmpID'] = '';
                }

                if ($this->input->post('EngineeringDeadLine') != '') {
                    $data_inquiry_department_details['requiredDate']=$EngineeringDeadLine;
                } else {
                    $data_inquiry_department_details['requiredDate']=null;
                }
                
                $data_inquiry_department_details['delayInDays']=$this->input->post('noofdays');

                $data_inquiry_department_details['departmentMasterID']=$this->input->post('engineering_tab_id');

                $data_inquiry_department_details['modifiedPCID']=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data_inquiry_department_details['modifiedUserID']=current_userID();
                $data_inquiry_department_details['modifiedUserName']=current_user();
                $data_inquiry_department_details['modifiedDateTime']=current_date(true);

                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $data_inquiry_department_details);

            }

            if($this->input->post('purchasing_tab_id')==2){

                $data_inquiry_department_details['ciMasterID']=$last_id;
                $data_inquiry_department_details['companyID']=$companyid;

                if ($this->input->post('submissiondatDeadLinepurchasing') != '') {
                    $data_inquiry_department_details['submissionDate']=$submissiondatDeadLineengineering;
                } else {
                    $data_inquiry_department_details['submissionDate']=null;
                }

                // $data_inquiry_department_details['responsibleEmpID']=$this->input->post('purchasingemployee');
                $purchasingemployee = $this->input->post('purchasingemployee');
                if (is_array($purchasingemployee)) {
                    $data_inquiry_department_details['responsibleEmpID'] = implode(',', $purchasingemployee);
                } else {
                    $data_inquiry_department_details['responsibleEmpID'] = '';
                }

                if ($this->input->post('purchasingDeadLine') != '') {
                    $data_inquiry_department_details['requiredDate']=$purchasingDeadLine;
                } else {
                    $data_inquiry_department_details['requiredDate']=null;
                }
                
                $data_inquiry_department_details['delayInDays']=$this->input->post('noofdayspurchasing');

                $data_inquiry_department_details['departmentMasterID']=$this->input->post('purchasing_tab_id');

                $data_inquiry_department_details['modifiedPCID']=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data_inquiry_department_details['modifiedUserID']=current_userID();
                $data_inquiry_department_details['modifiedUserName']=current_user();
                $data_inquiry_department_details['modifiedDateTime']=current_date(true);

                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $data_inquiry_department_details);

            }

            if($this->input->post('production_tab_id')==3){

                $data_inquiry_department_details['ciMasterID']=$last_id;
                $data_inquiry_department_details['companyID']=$companyid;

                if ($this->input->post('submissiondatDeadLineproduction') != '') {
                    $data_inquiry_department_details['submissionDate']=$submissiondatDeadLineproduction;
                } else {
                    $data_inquiry_department_details['submissionDate']=null;
                }

                // $data_inquiry_department_details['responsibleEmpID']=$this->input->post('productionemployee');
                $productionemployee = $this->input->post('productionemployee');
                if (is_array($productionemployee)) {
                    $data_inquiry_department_details['responsibleEmpID'] = implode(',', $productionemployee);
                } else {
                    $data_inquiry_department_details['responsibleEmpID'] = '';
                }

                if ($this->input->post('DeadLineproduction') != '') {
                    $data_inquiry_department_details['requiredDate']=$DeadLineproduction;
                } else {
                    $data_inquiry_department_details['requiredDate']=null;
                }
                
                $data_inquiry_department_details['delayInDays']=$this->input->post('noofdaysproduction');

                $data_inquiry_department_details['departmentMasterID']=$this->input->post('production_tab_id');

                $data_inquiry_department_details['modifiedPCID']=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data_inquiry_department_details['modifiedUserID']=current_userID();
                $data_inquiry_department_details['modifiedUserName']=current_user();
                $data_inquiry_department_details['modifiedDateTime']=current_date(true);

                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $data_inquiry_department_details);

            }

            if($this->input->post('qc_tab_id')==4){

                $data_inquiry_department_details['ciMasterID']=$last_id;
                $data_inquiry_department_details['companyID']=$companyid;

                if ($this->input->post('submissiondateqaqcDeadLinepurchasing') != '') {
                    $data_inquiry_department_details['submissionDate']=$submissiondatqaqc;
                } else {
                    $data_inquiry_department_details['submissionDate']=null;
                }

                // $data_inquiry_department_details['responsibleEmpID']=$this->input->post('qaqcemployee');
                $qaqcemployee = $this->input->post('qaqcemployee');
                if (is_array($qaqcemployee)) {
                    $data_inquiry_department_details['responsibleEmpID'] = implode(',', $qaqcemployee);
                } else {
                    $data_inquiry_department_details['responsibleEmpID'] = '';
                }

                if ($this->input->post('DeadLineqaqc') != '') {
                    $data_inquiry_department_details['requiredDate']=$DeadLineqaqc;
                } else {
                    $data_inquiry_department_details['requiredDate']=null;
                }
                
                $data_inquiry_department_details['delayInDays']=$this->input->post('noofdaysqaqc');

                $data_inquiry_department_details['departmentMasterID']=$this->input->post('qc_tab_id');

                $data_inquiry_department_details['modifiedPCID']=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data_inquiry_department_details['modifiedUserID']=current_userID();
                $data_inquiry_department_details['modifiedUserName']=current_user();
                $data_inquiry_department_details['modifiedDateTime']=current_date(true);

                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $data_inquiry_department_details);

            }
            // Finance tab
            if($this->input->post('finance_tab_id')==5){

                $data_inquiry_department_details['ciMasterID']=$last_id;
                $data_inquiry_department_details['companyID']=$companyid;

                if ($this->input->post('submissiondatDeadLinefinance') != '') {
                    $data_inquiry_department_details['submissionDate']=$submissiondatDeadLinefinance;
                } else {
                    $data_inquiry_department_details['submissionDate']=null;
                }

                // $data_inquiry_department_details['responsibleEmpID']=$this->input->post('financeemployee');
                $financeemployee = $this->input->post('financeemployee');
                if (is_array($financeemployee)) {
                    $data_inquiry_department_details['responsibleEmpID'] = implode(',', $financeemployee);
                } else {
                    $data_inquiry_department_details['responsibleEmpID'] = '';
                }

                if ($this->input->post('DeadLinefinance') != '') {
                    $data_inquiry_department_details['requiredDate']=$deadlinefinance;
                } else {
                    $data_inquiry_department_details['requiredDate']=null;
                }
                
                $data_inquiry_department_details['delayInDays']=$this->input->post('noofdaysfinance');

                $data_inquiry_department_details['departmentMasterID']=$this->input->post('finance_tab_id');

                $data_inquiry_department_details['modifiedPCID']=gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data_inquiry_department_details['modifiedUserID']=current_userID();
                $data_inquiry_department_details['modifiedUserName']=current_user();
                $data_inquiry_department_details['modifiedDateTime']=current_date(true);

                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $data_inquiry_department_details);

            }

            

        } else {
            $last_id = $this->input->post('ciMasterID');


            $detailsexist = $this->db->query("SELECT * FROM `srp_erp_mfq_customerinquiry` where ciMasterID = '{$last_id}' AND companyID = '{$companyid}'")->row_array();
            if (!empty($detailsexist)) {

                $data_history['createdUserGroup'] = $this->common_data['user_group'];
                $data_history['createdPCID'] = $this->common_data['current_pc'];
                $data_history['createdUserID'] = $this->common_data['current_userID'];
                $data_history['createdUserName'] = $this->common_data['current_user'];
                $data_history['createdDateTime'] = $this->common_data['current_date'];
                $data_history['documentMasterID'] = $last_id;
                $data_history['documentID'] = 'CI';
                $data_history['companyID'] = current_companyID();

                if ($detailsexist['type'] != $type) {
                    $data_history['changeDescription'] = 'Inquiry Type Changed';
                    $data_history['fieldName'] = 'type';
                    $data_history['value'] = $type;
                    $data_history['previousValue'] = $detailsexist['type'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }

                if ($detailsexist['statusID'] != $statusID) {
                    $data_history['changeDescription'] = 'Status Changed';
                    $data_history['fieldName'] = 'statusID';
                    $data_history['value'] = $statusID;
                    $data_history['previousValue'] = $detailsexist['statusID'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['segmentID'] != $segmentid) {
                    $data_history['changeDescription'] = 'Segment Changed';
                    $data_history['fieldName'] = 'segmentID';
                    $data_history['value'] = $segmentid;
                    $data_history['previousValue'] = $detailsexist['segmentID'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['contactPerson'] != $contactpersonname) {
                    $data_history['changeDescription'] = 'Contact Person Name Changed';
                    $data_history['fieldName'] = 'contactPerson';
                    $data_history['value'] = $contactpersonname;
                    $data_history['previousValue'] = $detailsexist['contactPerson'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['customerPhoneNo'] != $customertp) {
                    $data_history['changeDescription'] = 'Customer Phone No Changed';
                    $data_history['fieldName'] = 'customerPhoneNo';
                    $data_history['value'] = $customertp;
                    $data_history['previousValue'] = $detailsexist['customerPhoneNo'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['customerEmail'] != $customeremail) {
                    $data_history['changeDescription'] = 'Customer Email Changed';
                    $data_history['fieldName'] = 'customerEmail';
                    $data_history['value'] = $customeremail;
                    $data_history['previousValue'] = $detailsexist['customerEmail'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }


                if ($detailsexist['dueDate'] != $dueDate) {
                    $data_history['changeDescription'] = 'Planned Submission Date Changed';
                    $data_history['fieldName'] = 'dueDate';
                    $data_history['value'] = $dueDate;
                    $data_history['previousValue'] = $detailsexist['dueDate'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }


                if ($detailsexist['description'] != $description) {
                    $data_history['changeDescription'] = 'Description Changed';
                    $data_history['fieldName'] = 'description';
                    $data_history['value'] = $description;
                    $data_history['previousValue'] = $detailsexist['description'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }

                if ($detailsexist['manufacturingType'] != $manufacturingType) {
                    $data_history['changeDescription'] = 'Manufacturing Type Changed';
                    $data_history['fieldName'] = 'manufacturingType';
                    $data_history['value'] = $manufacturingType;
                    $data_history['previousValue'] = $detailsexist['manufacturingType'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['referenceNo'] != $referenceNo) {
                    $data_history['changeDescription'] = 'Client Reference No Changed';
                    $data_history['fieldName'] = 'referenceNo';
                    $data_history['value'] = $referenceNo;
                    $data_history['previousValue'] = $detailsexist['referenceNo'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['mfqCustomerAutoID'] != $mfqCustomerAutoID) {
                    $data_history['changeDescription'] = 'Client Changed';
                    $data_history['fieldName'] = 'mfqCustomerAutoID';
                    $data_history['value'] = $mfqCustomerAutoID;
                    $data_history['previousValue'] = $detailsexist['mfqCustomerAutoID'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }

                if ($detailsexist['deliveryDate'] != $deliveryDate) {
                    $data_history['changeDescription'] = 'Actual Submission Date Changed';
                    $data_history['fieldName'] = 'deliveryDate';
                    $data_history['value'] = $deliveryDate;
                    $data_history['previousValue'] = $detailsexist['deliveryDate'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($detailsexist['engineeringResponsibleEmpID'] != $engineeringemployee) {
                    $data_history['changeDescription'] = 'Engineering	Responsible Person Changed';
                    $data_history['fieldName'] = 'engineeringResponsibleEmpID';
                    $data_history['value'] = implode(',', $engineeringemployee);
                    $data_history['previousValue'] = $detailsexist['engineeringResponsibleEmpID'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($this->input->post('EngineeringDeadLine') != '') {
                    if ($detailsexist['engineeringEndDate'] != $EngineeringDeadLine) {
                        $data_history['changeDescription'] = 'Engineering	End Date Changed';
                        $data_history['value'] = $EngineeringDeadLine;
                        $data_history['previousValue'] = $detailsexist['engineeringEndDate'];
                        $data_history['fieldName'] = 'engineeringEndDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }

                if ($detailsexist['purchasingResponsibleEmpID'] != $purchasingemployee) {
                    $data_history['changeDescription'] = 'Purchasing Responsible Person Changed';
                    $data_history['value'] =implode(',', $purchasingemployee); 
                    $data_history['previousValue'] = $detailsexist['purchasingResponsibleEmpID'];
                    $data_history['fieldName'] = 'purchasingResponsibleEmpID';
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($this->input->post('purchasingDeadLine') != '') {
                    if ($detailsexist['purchasingEndDate'] != $purchasingDeadLine) {
                        $data_history['changeDescription'] = 'Purchasing	End Date Changed';
                        $data_history['value'] = $purchasingDeadLine;
                        $data_history['previousValue'] = $detailsexist['purchasingEndDate'];
                        $data_history['fieldName'] = 'purchasingEndDate';

                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }

                if ($detailsexist['productionResponsibleEmpID'] != $productionemployee) {
                    $data_history['changeDescription'] = 'Production Responsible Person Changed';
                    $data_history['fieldName'] = 'productionResponsibleEmpID';
                    $data_history['value'] = implode(',', $productionemployee);
                    $data_history['previousValue'] = $detailsexist['productionResponsibleEmpID'];
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($this->input->post('DeadLineproduction') != '') {
                    if ($detailsexist['productionEndDate'] != $DeadLineproduction) {
                        $data_history['changeDescription'] = 'Production End Date Changed';
                        $data_history['value'] = $DeadLineproduction;
                        $data_history['previousValue'] = $detailsexist['productionEndDate'];
                        $data_history['fieldName'] = 'productionEndDate';

                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($detailsexist['QAQCResponsibleEmpID'] != $qaqcemployee) {
                    $data_history['changeDescription'] = 'QA/QC Responsible Person Changed';
                    $data_history['value'] = implode(',', $qaqcemployee);
                    $data_history['previousValue'] = $detailsexist['QAQCResponsibleEmpID'];
                    $data_history['fieldName'] = 'QAQCResponsibleEmpID';
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($this->input->post('DeadLineqaqc') != '') {
                    if ($detailsexist['QAQCEndDate'] != $DeadLineqaqc) {
                        $data_history['changeDescription'] = 'QA/QC	End Date Changed';
                        $data_history['value'] = $DeadLineqaqc;
                        $data_history['previousValue'] = $detailsexist['QAQCEndDate'];
                        $data_history['fieldName'] = 'QAQCEndDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($this->input->post('submissiondatDeadLine') != '') {
                    if ($detailsexist['engineeringSubmissionDate'] != $submissiondatDeadLineengineering) {
                        $data_history['changeDescription'] = 'Engineering Submission Date Changed';
                        $data_history['value'] = $submissiondatDeadLineengineering;
                        $data_history['previousValue'] = $detailsexist['engineeringSubmissionDate'];
                        $data_history['fieldName'] = 'engineeringSubmissionDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($this->input->post('submissiondatDeadLinepurchasing') != '') {
                    if ($detailsexist['purchasingSubmissionDate'] != $submissiondatPurchasing) {
                        $data_history['changeDescription'] = 'Purchasing Submission Date Changed';
                        $data_history['value'] = $submissiondatPurchasing;
                        $data_history['previousValue'] = $detailsexist['purchasingSubmissionDate'];
                        $data_history['fieldName'] = 'purchasingSubmissionDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($this->input->post('submissiondatDeadLineproduction') != '') {
                    if ($detailsexist['productionSubmissionDate'] != $submissiondatqaqc) {
                        $data_history['changeDescription'] = 'Production Submission Date Changed';
                        $data_history['value'] = $submissiondatDeadLineproduction;
                        $data_history['previousValue'] = $detailsexist['productionSubmissionDate'];
                        $data_history['fieldName'] = 'productionSubmissionDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($this->input->post('submissiondateqaqcDeadLinepurchasing') != '') {
                    if ($detailsexist['QAQCSubmissionDate'] != $submissiondatqaqc) {
                        $data_history['changeDescription'] = 'QA/QC Submission Date Changed';
                        $data_history['value'] = $submissiondatqaqc;
                        $data_history['previousValue'] = $detailsexist['QAQCSubmissionDate'];
                        $data_history['fieldName'] = 'QAQCSubmissionDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }

                // finance tab 
                if ($detailsexist['financeResponsibleEmpID'] != $financeemployee) {
                    $data_history['changeDescription'] = 'Finance Responsible Person Changed';
                    $data_history['value'] =implode(',', $financeemployee); 
                    $data_history['previousValue'] = $detailsexist['financeResponsibleEmpID'];
                    $data_history['fieldName'] = 'financeResponsibleEmpID';
                    $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                }
                if ($this->input->post('DeadLinefinance') != '') {
                    if ($detailsexist['financeEndDate'] != $deadlinefinance) {
                        $data_history['changeDescription'] = 'Finance End Date Changed';
                        $data_history['value'] = $deadlinefinance;
                        $data_history['previousValue'] = $detailsexist['financeEndDate'];
                        $data_history['fieldName'] = 'financeEndDate';

                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }
                if ($this->input->post('submissiondatDeadLinefinance') != '') {
                    if ($detailsexist['financeSubmissionDate'] != $submissiondatDeadLinefinance) {
                        $data_history['changeDescription'] = 'Finance Submission Date Changed';
                        $data_history['value'] = $submissiondatDeadLinefinance;
                        $data_history['previousValue'] = $detailsexist['financeSubmissionDate'];
                        $data_history['fieldName'] = 'financeSubmissionDate';
                        $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                    }
                }

            }

            if ($this->input->post('submissiondatDeadLine') != '') {
                $this->db->set('engineeringSubmissionDate', $submissiondatDeadLineengineering);
            } else {
                $this->db->set('engineeringSubmissionDate', null);
            }
            if ($this->input->post('submissiondatDeadLinepurchasing') != '') {
                $this->db->set('purchasingSubmissionDate', $submissiondatPurchasing);
            } else {
                $this->db->set('purchasingSubmissionDate', null);
            }
            if ($this->input->post('submissiondatDeadLineproduction') != '') {
                $this->db->set('productionSubmissionDate', $submissiondatDeadLineproduction);
            } else {
                $this->db->set('productionSubmissionDate', null);
            }
            if ($this->input->post('submissiondateqaqcDeadLinepurchasing') != '') {
                $this->db->set('QAQCSubmissionDate', $submissiondatqaqc);
            } else {
                $this->db->set('QAQCSubmissionDate', null);
            }
            if ($this->input->post('submissiondatDeadLinefinance') != '') {
                $this->db->set('financeSubmissionDate', $submissiondatDeadLinefinance);
            } else {
                $this->db->set('financeSubmissionDate', null);
            }

            $this->db->set('segmentID', $segmentid);
            $this->db->set('remindEmailBefore', $remainingdays);
            $this->db->set('proposalEngineerID', $prpengineer);
            $this->db->set('contactPerson', $contactpersonname);
            $this->db->set('customerPhoneNo', $customertp);
            $this->db->set('customerEmail', $customeremail);
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            
            $this->db->set('documentDate', $documentDate);
            $this->db->set('deliveryDate', $deliveryDate);
            $this->db->set('dueDate', $dueDate);
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('referenceNo', $this->input->post('referenceNo'));
            $this->db->set('statusID', $this->input->post('statusID'));
            $this->db->set('type', $this->input->post('type'));
            $this->db->set('manufacturingType', $this->input->post('manufacturingType'));
            $this->db->set('locationAssigned', $this->input->post('micoda'));
            $this->db->set('inquirySource', $this->input->post('sourceID'));

            $this->db->set('rfqStatus', $this->input->post('rfq_status'));
            $this->db->set('documentStatus', $this->input->post('document_status'));
            $this->db->set('orderStatus', $this->input->post('order_status'));
            $this->db->set('category', $this->input->post('cat'));
            $this->db->set('submissionStatus', $this->input->post('submission_status'));
            //$this->db->set('paymentTerm', $this->input->post('paymentTerm'));
            $this->db->set('currencyID',trim($this->input->post('currencyID') ?? '')) ;
            $this->db->set('transactionCurrencyID',trim($this->input->post('currencyID') ?? '')) ;
            $this->db->set('transactionCurrency',$transactioncurreny[0]) ;
            $this->db->set('transactionExchangeRate',1) ;
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->input->post('currencyID')));


            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']) ;
            $this->db->set('companyLocalCurrency',$this->common_data['company_data']['company_default_currency']) ;
            $default_currency = currency_conversionID(trim($this->input->post('currencyID') ?? ''),$this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyLocalCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']) ;
            $this->db->set('companyReportingCurrency',$this->common_data['company_data']['company_reporting_currency']) ;
            $default_currency = currency_conversionID(trim($this->input->post('currencyID') ?? ''),$this->common_data['company_data']['company_reporting_currencyID']);
            $this->db->set('companyReportingExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyReportingCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            if ($this->input->post('EngineeringDeadLine') != '') {
                $this->db->set('engineeringEndDate', $EngineeringDeadLine);
            } else {
                $this->db->set('engineeringEndDate', null);
            }


            if ($this->input->post('purchasingDeadLine') != '') {
                $this->db->set('purchasingEndDate', $purchasingDeadLine);
            } else {
                $this->db->set('purchasingEndDate', null);
            }
            if ($this->input->post('DeadLineproduction') != '') {
                $this->db->set('productionEndDate', $DeadLineproduction);
            } else {
                $this->db->set('productionEndDate', null);
            }
            if ($this->input->post('DeadLineqaqc') != '') {
                $this->db->set('QAQCEndDate', $DeadLineqaqc);
            } else {
                $this->db->set('QAQCEndDate', null);
            }

            // Finance end date
            if ($this->input->post('DeadLinefinance') != '') {
                $this->db->set('financeEndDate', $deadlinefinance);
            } else {
                $this->db->set('financeEndDate', null);
            }


            $this->db->set('quotationStatus', 0);

            if (is_array($engineeringemployee)) {
                $engineeringResponsibleEmpID = implode(',', $engineeringemployee);
            } else {
                $engineeringResponsibleEmpID = '';
            }
            $this->db->set('engineeringResponsibleEmpID', $engineeringResponsibleEmpID);

            
            if (is_array($purchasingemployee)) {
                $purchasingResponsibleEmpID = implode(',', $purchasingemployee);
            } else {
                $purchasingResponsibleEmpID = '';
            }
            $this->db->set('purchasingResponsibleEmpID', $purchasingResponsibleEmpID);

            if (is_array($productionemployee)) {
                $productionResponsibleEmpID = implode(',', $productionemployee);
            } else {
                $productionResponsibleEmpID = '';
            }
            $this->db->set('productionResponsibleEmpID', $productionResponsibleEmpID);

            if (is_array($qaqcemployee)) {
                $QAQCResponsibleEmpID = implode(',', $qaqcemployee);
            } else {
                $QAQCResponsibleEmpID = '';
            }
            $this->db->set('QAQCResponsibleEmpID', $QAQCResponsibleEmpID);

            if (is_array($financeemployee)) {   
                $financeResponsibleEmpID = implode(',', $financeemployee);
            } else {
                $financeResponsibleEmpID = '';
            }
            $this->db->set('financeResponsibleEmpID', $financeResponsibleEmpID);

            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('modifiedUserID', current_userID());
            $this->db->set('modifiedUserName', current_user());
            $this->db->set('modifiedDateTime', current_date(true));
            $this->db->where('ciMasterID', $this->input->post('ciMasterID'));
            $result = $this->db->update('srp_erp_mfq_customerinquiry');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

        } else {

            $ciDetailID = $this->input->post('ciDetailID');
            $mfqItemID = $this->input->post('mfqItemID');
            $description = $this->input->post('search');
            $mfqitemdetailsresults = $this->db->query("SELECT * FROM `srp_erp_mfq_customerinquirydetail` where companyID = '{$companyid}' AND ciMasterID = '{$last_id}'")->result_array();
            if (!empty($mfqItemID)) {
                foreach ($mfqItemID as $key => $val) {
                    if (!empty($ciDetailID[$key])) {
                        if (!empty($mfqItemID[$key]) || !empty($description[$key])) {
                            $date_format_policy = date_format_policy();

                            if (!empty($expectedweeks[$key])) {
                            $expecteddDate = $this->db->query("SELECT DATE_ADD( curdate(), INTERVAL $expectedweeks[$key]  WEEK ) AS expectedDeliveryDate ")->row_array();
                            $expectedDeliveryDate = input_format_date(trim($expecteddDate['expectedDeliveryDate'] ?? ''), $date_format_policy);
                              
                            } else {
                                $expectedweeks[$key] = '';
                                $expectedDeliveryDate = null;
                            }

                            if ($ciDetailID[$key] == $mfqitemdetailsresults[$key]['ciDetailID']) {
                                if ($mfqitemdetailsresults[$key]['expectedDeliveryDate'] != $expectedDeliveryDate) {
                                    $data_history_detail['companyID'] = current_companyID();
                                    $data_history_detail['documentMasterID'] = $last_id;
                                    $data_history_detail['documentDetailID'] = $ciDetailID[$key];
                                    $data_history_detail['documentID'] = 'CI';
                                    $data_history_detail['changeDescription'] = 'Delivery Date Changed';
                                    $data_history_detail['fieldName'] = 'expectedDeliveryDate';
                                    $data_history_detail['value'] = $expectedDeliveryDate;
                                    $data_history_detail['previousValue'] = $mfqitemdetailsresults[$key]['expectedDeliveryDate'];
                                    $data_history_detail['createdUserGroup'] = $this->common_data['user_group'];
                                    $data_history_detail['createdPCID'] = $this->common_data['current_pc'];
                                    $data_history_detail['createdUserID'] = $this->common_data['current_userID'];
                                    $data_history_detail['createdUserName'] = $this->common_data['current_user'];
                                    $data_history_detail['createdDateTime'] = $this->common_data['current_date'];
                                    $this->db->insert('srp_erp_mfq_changehistory', $data_history_detail);
                                }

                            }


                            $this->db->set('ciMasterID', $last_id);
                            if (empty($mfqItemID[$key]) || $mfqItemID[$key] == 'null') {
                                $this->db->set('itemDescription', $description[$key] ?? null);
                                $this->db->set('mfqItemID', null);
                            } else {
                                $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key] ?? null);
                                $this->db->set('itemDescription', null);
                            }

                            $this->db->set('bomMasterID', $this->input->post('bom')[$key] ?? null);
                            $this->db->set('expectedQty', $this->input->post('expectedQty')[$key] ?? null);
                            $this->db->set('segmentID', $this->input->post('segmentID')[$key] ?? null);
                            $this->db->set('expectedDeliveryWeeks',$expectedweeks[$key] ?? null);
                            $this->db->set('expectedDeliveryDate', $expectedDeliveryDate);
                            $this->db->set('remarks', $this->input->post('remarks')[$key] ?? null);
                            $this->db->set('deliveryTerms', $this->input->post('deliveryTerms')[$key] ?? null);

                            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('modifiedUserID', current_userID());
                            $this->db->set('modifiedUserName', current_user());
                            $this->db->set('modifiedDateTime', current_date(true));
                            $this->db->where('ciDetailID', $ciDetailID[$key]);
                            $result = $this->db->update('srp_erp_mfq_customerinquirydetail');


                        }
                    } else {
                        if (!empty($mfqItemID[$key]) || !empty($description[$key])) {

                            $date_format_policy = date_format_policy();

                            if (!empty($expectedweeks[$key])) {
                               
                                 $expecteddDate = $this->db->query("SELECT DATE_ADD( curdate(), INTERVAL $expectedweeks[$key]  WEEK ) AS expectedDeliveryDate ")->row_array();
                            $expectedDeliveryDate = input_format_date(trim($expecteddDate['expectedDeliveryDate'] ?? ''), $date_format_policy);
                              
                            } else {
                                $expectedweeks[$key] = '';
                                $expectedDeliveryDate = null;
                            }

                            $this->db->set('ciMasterID', $last_id);
                            if (empty($mfqItemID[$key])) {
                                $this->db->set('itemDescription', $description[$key] ?? null);
                                $this->db->set('mfqItemID', null);
                            } else {
                                $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key] ?? null);
                                $this->db->set('itemDescription', null);
                            }
                            $this->db->set('bomMasterID', $this->input->post('bom')[$key] ?? null);
                            $this->db->set('expectedQty', $this->input->post('expectedQty')[$key] ?? null);
                            $this->db->set('segmentID', $this->input->post('segmentID')[$key] ?? null);
                            $this->db->set('expectedDeliveryWeeks', $expectedweeks[$key] ?? null);
                            $this->db->set('expectedDeliveryDate', $expectedDeliveryDate);
                            $this->db->set('remarks', $this->input->post('remarks')[$key] ?? null);
                            $this->db->set('deliveryTerms', $this->input->post('deliveryTerms')[$key] ?? null);
                            $this->db->set('companyID', current_companyID());

                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('createdUserID', current_userID());
                            $this->db->set('createdUserName', current_user());
                            $this->db->set('createdDateTime', current_date(true));
                            $result = $this->db->insert('srp_erp_mfq_customerinquirydetail');
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Inquiry Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Inquiry Saved Successfully.', $last_id);
            }
        }

    }


    function customer_inquiry_confirmation_bkp()
    {
        $this->db->trans_start();
        $ciMasterID = trim($this->input->post('ciMasterID') ?? '');
        $this->db->select('*');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_customerinquiry');
        $row = $this->db->get()->row_array();
        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $this->load->library('Approvals');
            $this->db->select('*');
            $this->db->where('ciMasterID', $ciMasterID);
            $this->db->from('srp_erp_mfq_customerinquiry');
            $row = $this->db->get()->row_array();
            $approvals_status = $this->approvals->CreateApproval('CI', $row['ciMasterID'],
                $row['ciCode'], 'Customer Inquiry', 'srp_erp_mfq_customerinquiry', 'ciMasterID', 0);
            /* if ($approvals_status == 1) {
                 $this->db->set('confirmedYN', 1);
                 $this->db->set('confirmedUserID', current_userID());
                 $this->db->set('confirmedUserName', current_user());
                 $this->db->set('confirmedDate', current_date(false));
                 $this->db->where('ciMasterID', $ciMasterID);
                 $this->db->update('srp_erp_mfq_customerinquiry');
             }*/

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Inquiry Confirmed Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Customer Inquiry : Confirmed Successfully');
            }
        }
    }

    function fetch_tender_log_excel()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();

        $where = '';
        $convertFormat = convert_date_format_sql();
        $customercode = $this->input->post('customerCode');
        $proposalengID = $this->input->post('proposalengID');
        $rfqType = $this->input->post('rfqType');
        $rfqstatus = $this->input->post('rfqstatus');
        $micoda = $this->input->post('micoda');
        $nstatus = $this->input->post('nstatus');
        $orderstatus = $this->input->post('orderstatus');

        $date_format_policy = date_format_policy();

        $datefrom = $this->input->post('IncidateDateFrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);

        $dateto = $this->input->post('IncidateDateTo');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $where .= " AND ( i.documentDate >= '" . $datefromconvert . " 00:00:00' AND i.documentDate <= '" . $datetoconvert . " 23:59:00')";
        }


        if ($customercode) {
            $where .= " AND i.mfqCustomerAutoID IN (" . join(',', $customercode) . ")";
        }

        if ($proposalengID) {
            $where .= " AND i.proposalEngineerID IN (" . join(',', $proposalengID) . ")";
        }

        if ($rfqType) {
            $where .= " AND i.type IN (" . join(',', $rfqType) . ")";
        }

        if ($rfqstatus) {
            $where .= " AND i.rfqStatus IN (" . join(',', $rfqstatus) . ")";
        }

        if ($micoda) {
            $where .= " AND i.locationAssigned IN (" . join(',', $micoda) . ")";
        }

        if ($nstatus) {
            $where .= " AND i.documentStatus IN (" . join(',', $nstatus) . ")";
        }

        if ($orderstatus) {
            $where .= " AND i.orderStatus IN (" . join(',', $orderstatus) . ")";
        }


        $result = $this->db->query("SELECT
        ((discountedPrice * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) AS estimateValue,
	srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	srp_erp_currencymaster.CurrencyCode AS transactionCurrency,
	        i.ciMasterID,i.ciCode,job.workProcessID as workProcessID,cus.CustomerName,sou.description as source,cou.CountryDes as CountryDes,i.description,i.category,est.totDiscountPrice,est.totalSellingPrice,i.type,i.locationAssigned,i.confirmedByName,i.inquirySource,e.Ename2,i.documentDate,i.rfqStatus,i.documentStatus,i.orderStatus,i.dueDate,i.deliveryDate,i.submissionStatus,est.poNumber,est.documentDate as poDate,est.materialCertificationComment,est.deliveryDate as podeliveryDate,job.documentCode as jobNumber
        FROM
        srp_erp_mfq_customerinquiry i
            LEFT JOIN srp_erp_mfq_customermaster cus ON cus.mfqCustomerAutoID = i.mfqCustomerAutoID
            LEFT JOIN srp_erp_mfq_estimatemaster  est ON est.ciMasterID = i.ciMasterID
            LEFT JOIN srp_employeesdetails e on e.EIdNo = i.proposalEngineerID
            LEFT JOIN srp_erp_mfq_job job on job.estimateMasterID = est.estimateMasterID
            LEFT JOIN srp_erp_mfq_customer_inquiry_source sou on sou.sourceID = i.inquirySource
            LEFT JOIN srp_countrymaster cou on cou.countryID = i.locationAssigned

            LEFT JOIN `srp_erp_mfq_status` ON `srp_erp_mfq_status`.`statusID` = `est`.`submissionStatus`
            LEFT JOIN ( SELECT estimateMasterID, workProcessID FROM srp_erp_mfq_job WHERE ( isDeleted IS NULL OR isDeleted != 1 ) GROUP BY estimateMasterID ) job3 ON `job3`.`estimateMasterID` = `est`.`estimateMasterID`
            LEFT JOIN (
            SELECT
                dueDate,
                srp_erp_mfq_estimatedetail.ciMasterID,
                srp_erp_mfq_estimatedetail.estimateMasterID,
                srp_erp_mfq_estimatedetail.estimateDetailID,
                SUM( discountedPrice ) AS discountedPrice 
            FROM
                srp_erp_mfq_estimatedetail
                LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_estimatedetail.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID 
            WHERE
                srp_erp_mfq_estimatedetail.companyID = {$companyID} 
            GROUP BY
                srp_erp_mfq_estimatedetail.estimateMasterID 
            ) estd ON `estd`.`estimateMasterID` = `est`.`estimateMasterID`
            INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON `maxl`.`estimateMasterID` = `est`.`estimateMasterID`
            LEFT JOIN (
            SELECT
            IF
                ( SUM( approvedYN ) > 0, 1, 0 ) AS docApprovedYN,
                documentSystemCode 
            FROM
                srp_erp_documentapproved 
            WHERE
                documentID = 'EST' 
                AND companyID = {$companyID} 
            GROUP BY
                documentSystemCode 
            ) docApp ON `est`.`estimateMasterID` = `docApp`.`documentSystemCode`
            LEFT JOIN `srp_erp_mfq_segment` `mfqsegment` ON `mfqsegment`.`mfqSegmentID` = `est`.`mfqSegmentID`
            LEFT JOIN `srp_erp_currencymaster` ON `est`.`transactionCurrencyID` = `srp_erp_currencymaster`.`currencyID`
            LEFT JOIN `srp_erp_segment` `segment` ON `segment`.`segmentID` = `mfqsegment`.`segmentID`

        WHERE
        i.companyID = {$companyID}{$where}")->result_array();

       // print_r($result);exit;
      
        if($result) {

            $a = 1;
            foreach ($result AS $val) {

                $date_arr = explode('-',$val['documentDate']);

                $job_id=$val['workProcessID'];

                $days = (strtotime($val['podeliveryDate']) - strtotime($val['documentDate'])) / (60 * 60 * 24);

                $jobStatecrew = $this->db->query("SELECT
                                SUM(hoursSpent) as total
                                FROM
                                    `srp_erp_mfq_workprocesscrew`
                                    WHERE 
                                    srp_erp_mfq_workprocesscrew.workProcessID = '{$job_id}' And srp_erp_mfq_workprocesscrew.companyID = '{$companyID}'
                                ")->row_array();

                $det['SL.No'] = $a;
                $det['Tender No'] = $val['ciCode'];
                $det['Client'] = $val['CustomerName'];
                $det['Description'] = $val['description'];

                if($val['category']==1){
                    $det['Category'] = "ss Tank";
                }else{
                    $det['Category'] = "-";
                }

                $det['Price'] = $val['transactionCurrency'].' '.number_format($val['estimateValue'], $val['transactionCurrencyDecimalPlaces'], '.', '');

                if($val['type']==1){
                    $det['RFQ Type'] = "TENDER";
                }else if($val['type']==2){
                    $det['RFQ Type'] = "RFQ";
                }else if($val['type']==3){
                    $det['RFQ Type'] = "SPC";
                }else{
                    $det['RFQ Type'] = "-";
                }
                $det['Micoda Operation'] = $val['CountryDes'];
                $det['RFQ Originator'] = $val['confirmedByName'];
                $det['Source'] = $val['source'];
                $det['Estimator'] = $val['Ename2'];
                $det['Month'] = $date_arr[1];
                $det['Year'] = $date_arr[0];
               
                if($val['rfqStatus']==1){
                    $det['RFQ Status'] = "Tentative";
                }else if($val['rfqStatus']==2){
                    $det['RFQ Status'] = "Firm";
                }else if($val['rfqStatus']==3){
                    $det['RFQ Status'] = "Budget";
                }else{
                    $det['RFQ Status'] = "-";
                }
               

                if($val['documentStatus']==1){
                    $det['Status'] = "Open";
                }else if($val['documentStatus']==2){
                    $det['Status'] = "In Progress";
                }else if($val['documentStatus']==3){
                    $det['Status'] = "Completed";
                }else{
                    $det['Status'] = "-";
                }

                if($val['orderStatus']==1){
                    $det['Order Status'] = "Open";
                }else if($val['orderStatus']==2){
                    $det['Order Status'] = "In Progress";
                }else if($val['orderStatus']==3){
                    $det['Order Status'] = "Lost";
                }else if($val['orderStatus']==4){
                    $det['Order Status'] = "Awarded";
                }else{
                    $det['Order Status'] = "-";
                }

                $det['Assigned Date'] = $val['documentDate'];
                $det['Submission Date'] = $val['dueDate'];
                $det['Actual Submission Date'] = $val['deliveryDate'];

                if($val['submissionStatus']==1){
                    $det['Submission Status']= "RFE";
                }else{
                    $det['Submission Status'] = "-";
                }

                $det['Alloted Manhours'] = "";
                $det['Actual Manhours'] = $jobStatecrew['total'];
                $det['No. of Days Delayed'] = floor($days);

                $det['Total'] = "-";
                $det['REV. #'] = "-";
                $det['PO Received Date'] = $val['poDate'];
                $det['PO Number'] = $val['poNumber'];
                $det['Project Number'] = $val['jobNumber'];
                $det['Remark'] = $val['materialCertificationComment'];

                $a++;
                array_push($data, $det);
            }
            
        }

        return $data;
    }


    function fetch_project_process_log_excel()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();

        $where = '';
        $convertFormat = convert_date_format_sql();
        $customercode = $this->input->post('customerCode');
        $proposalengID = $this->input->post('proposalengID');
        $category = $this->input->post('category');
        $micno = $this->input->post('micno');
        $currenttatus = $this->input->post('currenttatus');
    

        $date_format_policy = date_format_policy();

        $datefrom = $this->input->post('IncidateDateFrom');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);

        $dateto = $this->input->post('IncidateDateTo');
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        
        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $where .= " AND ( srp_erp_mfq_job.documentDate >= '" . $datefromconvert . " 00:00:00' AND srp_erp_mfq_job.documentDate <= '" . $datetoconvert . " 23:59:00')";
        }


        if ($customercode) {
            $where .= " AND srp_erp_mfq_job.mfqCustomerAutoID IN (" . join(',', $customercode) . ")";
        }

        if ($proposalengID) {
            $where .= " AND inq.proposalEngineerID IN (" . join(',', $proposalengID) . ")";
        }

        if ($category) {
            $where .= " AND inq.category IN (" . join(',', $category) . ")";
        }


        $result = $this->db->query("SELECT
                documentCode,
                poNumber,
                workProcessID,
                DATE_FORMAT( srp_erp_mfq_job.documentDate, '{$convertFormat}' ) AS documentDate,
                (
                DATE_FORMAT( expectedDeliveryDate, '{$convertFormat}' )) AS expectedDeliveryDate,
                IFNULL( DATE_FORMAT( est.deliveryDate, '{$convertFormat}' ), ' - ' ) AS deliveryDate,
                IFNULL( DATE_FORMAT( srp_erp_mfq_deliverynote.deliveryDate, '{$convertFormat}' ), ' - ' ) AS actualDeliveryDate,
                srp_erp_mfq_job.description,
                templateDescription,
                ROUND(job2.percentage, 2) AS percentage,
                CONCAT( itemSystemCode, ' - ', itemDescription ) AS itemDescription,
                srp_erp_mfq_job.approvedYN,
                srp_erp_mfq_job.confirmedYN,
                isFromEstimate,
                cust.CustomerName AS CustomerName,
                inq.ciCode AS ciCode,
                inq.category AS category,
                est.estimateCode AS nestimateCode,
                est.totalSellingPrice AS totalSellingPrice,
                e.Ename2 AS Ename2,
                est.poNumber AS npoNumber,
                est.documentDate AS poDate,
                srp_erp_mfq_job.closedDate AS nclosedDate,
                srp_erp_mfq_job.documentDate AS jobCardDate,
                srp_erp_mfq_job.description AS jobdescription,
                srp_erp_mfq_job.qty AS qty,
                srp_erp_mfq_deliverynote.deliveryNoteCode AS deliveryNoteCode,
                srp_erp_mfq_job.estimateMasterID AS estimateMasterID,
                srp_erp_mfq_job.linkedJobID AS linkedJobID,
                srp_erp_mfq_job.isDeleted AS isDeleted,
                IFNULL(jobStatus, 1) AS jobStatus,
                ((discountedPrice * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) AS estimateValue,
                srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
                srp_erp_currencymaster.CurrencyCode AS transactionCurrency
            FROM
                `srp_erp_mfq_job`
                LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `srp_erp_mfq_job`.`mfqCustomerAutoID`
                LEFT JOIN `srp_erp_mfq_estimatemaster` `est` ON `est`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
                LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `est`.`ciMasterID` = `inq`.`ciMasterID`
                LEFT JOIN srp_employeesdetails  `e` on `e`.`EIdNo` = `inq`.`proposalEngineerID`
                LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID`
                LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID`
                LEFT JOIN `srp_erp_mfq_deliverynotedetail` ON `srp_erp_mfq_deliverynotedetail`.`jobID` = `srp_erp_mfq_job`.`workProcessID`
                LEFT JOIN `srp_erp_mfq_deliverynote` ON `srp_erp_mfq_deliverynote`.`deliverNoteID` = `srp_erp_mfq_deliverynotedetail`.`deliveryNoteID`

                LEFT JOIN (
                    SELECT
                        linkedJobID,
                        MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
                    FROM
                        srp_erp_mfq_job
                        LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
                        LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID 
                    GROUP BY
                        linkedJobID 
                ) MainJobStatus ON `MainJobStatus`.`linkedJobID` = `srp_erp_mfq_job`.`workProcessID`
                
                LEFT JOIN (
                SELECT
                    (
                    SUM( a.percentage )/ COUNT( * )) AS percentage,
                    linkedJobID 
                FROM
                    srp_erp_mfq_job
                    LEFT JOIN (
                    SELECT
                        jobID,
                        COUNT(*) AS totCount,
                        SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END ) AS completedCount,(
                        SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END )/ COUNT(*)) * 100 AS percentage 
                    FROM
                        srp_erp_mfq_workflowstatus 
                    GROUP BY
                        jobID 
                    ) a ON a.jobID = srp_erp_mfq_job.workProcessID 
                GROUP BY
                    linkedJobID 
                ) job2 ON `job2`.`linkedJobID` = `srp_erp_mfq_job`.`workProcessID` 


                LEFT JOIN `srp_erp_mfq_status` ON `srp_erp_mfq_status`.`statusID` = `est`.`submissionStatus`
                LEFT JOIN (
                SELECT
                    dueDate,
                    srp_erp_mfq_estimatedetail.ciMasterID,
                    srp_erp_mfq_estimatedetail.estimateMasterID,
                    srp_erp_mfq_estimatedetail.estimateDetailID,
                    SUM( discountedPrice ) AS discountedPrice 
                FROM
                    srp_erp_mfq_estimatedetail
                    LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_estimatedetail.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID 
                WHERE
                    srp_erp_mfq_estimatedetail.companyID = {$companyID} 
                GROUP BY
                    srp_erp_mfq_estimatedetail.estimateMasterID 
                ) estd ON `estd`.`estimateMasterID` = `est`.`estimateMasterID`
                INNER JOIN ( SELECT MAX( versionLevel ), versionOrginID, MAX( estimateMasterID ) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON `maxl`.`estimateMasterID` = `est`.`estimateMasterID`
                LEFT JOIN (
                SELECT
                IF
                    ( SUM( approvedYN ) > 0, 1, 0 ) AS docApprovedYN,
                    documentSystemCode 
                FROM
                    srp_erp_documentapproved 
                WHERE
                    documentID = 'EST' 
                    AND companyID = {$companyID} 
                GROUP BY
                    documentSystemCode 
                ) docApp ON  `docApp`.`documentSystemCode`=`est`.`estimateMasterID`
                LEFT JOIN `srp_erp_mfq_segment` `mfqsegment` ON `mfqsegment`.`mfqSegmentID` = `est`.`mfqSegmentID`
                LEFT JOIN `srp_erp_currencymaster` ON `est`.`transactionCurrencyID` = `srp_erp_currencymaster`.`currencyID`
                LEFT JOIN `srp_erp_segment` `segment` ON `segment`.`segmentID` = `mfqsegment`.`segmentID`


                WHERE
                    srp_erp_mfq_job.companyID = {$companyID} {$where}
                    AND ( `srp_erp_mfq_job`.`linkedJobID` != 0 ) 
                ORDER BY workProcessID DESC")->result_array();

            //print_r($result);exit;
      
        if($result) {

            $a = 1;
            foreach ($result AS $val) {

                $date_arr = explode('-',$val['jobCardDate']);
                $job_id=$val['workProcessID'];
                

                $jobState = $this->db->query("SELECT
                       job_id,stage_id,stage_progress,stage_remarks
                    FROM
                        `srp_erp_mfq_job_wise_stage`
                        WHERE 
                        srp_erp_mfq_job_wise_stage.job_id = '{$job_id}' And srp_erp_mfq_job_wise_stage.company_id = '{$companyID}'
                    ")->result_array();


                $det['No'] = $a;
                $det['MIC NO'] = "-";
                $det['Tendor No'] = $val['ciCode'];
                $det['Estimate No'] = $val['nestimateCode'];
                $det['Job Num'] = $val['documentCode'];
                $det['CLIENT'] = $val['CustomerName'];
                $det['CATEGORY'] = $val['category'];
                $det['CLIENT PO REF. NO'] = $val['npoNumber'];
                $det['PROJECT FOCAL'] = $val['Ename2'];
                $det['PO VALUE'] = $val['transactionCurrency'].' '.number_format($val['estimateValue'], $val['transactionCurrencyDecimalPlaces'], '.', '');
                $det['PO / IJOF DELIVERY'] = $val['poDate'];
                $det['COMMITTED COMPLETION DATE'] = $val['nclosedDate'];
                $det['ACTUAL COMPLETION DATE'] = "-";
                $det['MONTH'] = $date_arr[1];
                $det['YEAR'] = $date_arr[0];
                $det['Description'] = $val['jobdescription'];
                $det['Current Status'] = strip_tags(load_main_job_status($val['jobStatus']));

                if(count($jobState)>0){
                    
                    foreach($jobState as $stage){
                        //print_r( $stage);exit;
                        if($stage['stage_id']==1){
                            $det['ENGG'] = $stage['stage_progress'].' %';
                            $det['REMARK'] = $stage['stage_remarks'];
                        }else{
                            $det['ENGG'] = "-";
                            $det['REMARK'] = "-";
                        }

                        if($stage['stage_id']==2){
                            $det['PR'] = $stage['stage_progress'].' %';
                            $det['REMARK2'] = $stage['stage_remarks'];
                        }else{
                            $det['PR'] = "-";
                            $det['REMARK2'] = "-";
                        }

                        if($stage['stage_id']==3){
                            $det['PO'] = $stage['stage_progress'].' %';
                            $det['REMARK3'] = $stage['stage_remarks'];
                        }else{
                            $det['PO'] = "-";
                            $det['REMARK3'] = "-";
                        }

                        if($stage['stage_id']==4){
                            $det['FAB'] = $stage['stage_progress'].' %';
                        }else{
                            $det['FAB'] = "-";
                        }

                        if($stage['stage_id']==5){
                            $det['NDE'] = $stage['stage_progress'].' %';
                        }else{
                            $det['NDE'] = "-";
                        }

                        if($stage['stage_id']==6){
                            $det['HYDRO'] = $stage['stage_progress'].' %';
                        }else{
                            $det['HYDRO'] = "-";
                        }

                        if($stage['stage_id']==7){
                            $det['PAINT'] = $stage['stage_progress'].' %';
                        }else{
                            $det['PAINT'] = "-";
                        }

                        if($stage['stage_id']==8){
                            $det['FAT'] = $stage['stage_progress'].' %';
                            $det['REMARK4'] = $stage['stage_remarks'];
                        }else{
                            $det['FAT'] = "-";
                            $det['REMARK4'] = "-";
                        }

                        if($stage['stage_id']==9){
                            $det['MRB'] = $stage['stage_progress'].' %';
                        }else{
                            $det['MRB'] = "-";
                        }

                        if($stage['stage_id']==10){
                            $det['P&L'] = $stage['stage_progress'].' %';
                        }else{
                            $det['P&L'] = "-";
                        }

                    }

                }else{
                    $det['ENGG'] = "-";
                    $det['REMARK'] = "-";
                    $det['PR'] = "-";
                    $det['REMARK2'] = "-";
                    $det['PO'] = "-";
                    $det['REMARK3'] = "-";
                    $det['FAB'] = "-";

                    $det['NDE'] = "-";
                    $det['HYDRO'] = "-";
                    $det['PAINT'] = "-";

                    $det['FAT'] = "-";
                    $det['REMARK4'] = "-";

                    $det['MRB'] = "-";
                    $det['P&L'] = "-";
                }
               

                $det['Overall Progress Achieved %'] = isset($val['percentage'])?$val['percentage'].' %':"-";
                $det['TOTAL'] = $val['qty'];
                $det['PROJECT WITH VARIATION'] = "-";
                $det['VARIATION AMOUNT'] = "-";
                $det['STATUS OF VARIATION PO'] = "-";
                $det['ESTIMATED P&L'] = "-";
                $det['RESULT P&L'] = "-";
                $det['DELIVERY NOTE'] = $val['deliveryNoteCode'];
                $det['COLLECTION OF GOODS'] = "-";

            

                $a++;
                array_push($data, $det);
            }
            
        }

        return $data;
    }


    function customer_inquiry_confirmation()
    {
        $this->db->trans_start();
        $ciMasterID = trim($this->input->post('ciMasterID') ?? '');
        $companyid = current_companyID();
        $currentuserid = current_userID();
        $convertFormat = convert_date_format_sql();

        $this->db->select('*');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_customerinquiry');
        $row = $this->db->get()->row_array();

        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $this->db->select('srp_erp_mfq_customerinquiry.*,srp_erp_segment.segmentCode as segmentcode,srp_erp_mfq_customermaster.CustomerName as CustomerNamemfq,engineering.Ename2 as Engineeringname,Purchasing.Ename2 as Purchasingname,DATE_FORMAT(engineeringEndDate,\'' . $convertFormat . '\') as engineeringEndDateformated,DATE_FORMAT(purchasingEndDate,\'' . $convertFormat . '\') as purchasingEndDateDateformated,DATE_FORMAT(productionEndDate,\'' . $convertFormat . '\') as productionEndDateformated,DATE_FORMAT(QAQCEndDate,\'' . $convertFormat . '\') as QAQCEndDateDateformated,	production.Ename2 as Productionname,qaqc.Ename2 as qaqcname,DATE_FORMAT(dueDate,\'' . $convertFormat . '\') as plannedsubmissiondate');
            $this->db->where('ciMasterID', $ciMasterID);
            $this->db->from('srp_erp_mfq_customerinquiry');
            $this->db->join('srp_erp_mfq_segment', 'srp_erp_mfq_segment.mfqSegmentID = srp_erp_mfq_customerinquiry.segmentID', 'left');
            $this->db->join('srp_erp_segment', 'srp_erp_segment.segmentID = srp_erp_mfq_segment.segmentID', 'left');
            $this->db->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_customerinquiry.mfqCustomerAutoID', 'left');
            $this->db->join('srp_employeesdetails engineering', 'engineering.EIdNo = srp_erp_mfq_customerinquiry.engineeringResponsibleEmpID', 'left');
            $this->db->join('srp_employeesdetails Purchasing', 'Purchasing.EIdNo = srp_erp_mfq_customerinquiry.purchasingResponsibleEmpID', 'left');
            $this->db->join('srp_employeesdetails production', 'production.EIdNo = srp_erp_mfq_customerinquiry.productionResponsibleEmpID', 'left');
            $this->db->join('srp_employeesdetails qaqc', 'qaqc.EIdNo = srp_erp_mfq_customerinquiry.QAQCResponsibleEmpID', 'left');
            $masterrec = $this->db->get()->row_array();
            //echo $this->db->last_query();

            //            $validate_code = validate_code_duplication($masterrec['ciCode'], 'ciCode', $ciMasterID,'ciMasterID', 'srp_erp_mfq_customerinquiry');
            //            if(!empty($validate_code)) {
            //                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            //            }

            $email = $this->db->query("SELECT
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
                `srp_erp_mfq_usergroups`
                WHERE
                companyID = '$companyid'
                AND isActive = 1
                AND groupType = 1
                AND srp_erp_mfq_usergroups.segmentID = '{$masterrec['segmentID']}' )")->result_array();

            $companyid = current_companyID();
            $customeremail = $this->db->query("SELECT customerEmailAutoID FROM `srp_erp_mfq_customeremail` where companyID = '{$companyid}' And mfqCustomerAutoID = '{$masterrec['mfqCustomerAutoID']}' AND email = '{$masterrec['customerEmail']}'")->row_array();
            if (empty($customeremail)) {
                $dataemail['mfqCustomerAutoID'] = $masterrec['mfqCustomerAutoID'];
                $dataemail['email'] = $masterrec['customerEmail'];
                $dataemail['isDefault'] = 1;
                $dataemail['companyID'] = $companyid;
                $dataemail['createdUserGroup'] = $this->common_data['user_group'];
                $dataemail['createdPCID'] = $this->common_data['current_pc'];
                $dataemail['createdUserID'] = $this->common_data['current_userID'];
                $dataemail['createdDateTime'] = $this->common_data['current_date'];
                $dataemail['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_mfq_customeremail', $dataemail);
            }

            $this->db->set('confirmedYN', 1);
            $this->db->set('confirmedByEmpID', current_userID());
            $this->db->set('confirmedByName', current_user());
            $this->db->set('confirmedDate', current_date(false));
            $this->db->set('approvedYN', 1);
            $this->db->set('approvedbyEmpID', current_userID());
            $this->db->set('approvedbyEmpName', current_user());
            $this->db->set('approvedDate', current_date(false));
            $this->db->where('ciMasterID', $ciMasterID);
            $this->db->update('srp_erp_mfq_customerinquiry');

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Customer Inquiry Approved Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                if (!empty($email)) {
                    $data = array();
                    $data["header"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiry();
                    $data["itemDetail"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetail();
                    $data['logo'] = mPDFImage;
                    $html = $this->load->view('system/mfq/ajax/customer_inquiry_print', $data, true);
                    $this->load->library('pdf');
                    $path = UPLOAD_PATH . base_url() . '/uploads/Manufacturing/' . $ciMasterID . 'CI' . current_userID() . ".pdf";
                    $this->pdf->save_pdf($html, 'A4', 1, $path);

                    $detailcustomerinquiry = $this->db->query("SELECT
                            srp_erp_mfq_itemmaster.itemDescription as itemdescription,
                            expectedQty,
                            DATE_FORMAT( expectedDeliveryDate, '%d-%m-%Y' ) as expectedDeliveryDate
                            
                        FROM
                            `srp_erp_mfq_customerinquirydetail`
                            LEFT JOIN srp_erp_mfq_itemmaster on srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID 
                            WHERE 
                            srp_erp_mfq_customerinquirydetail.companyID = '$companyid' 
                            AND ciMasterID = '$ciMasterID'
                        ")->result_array();

                    foreach ($email as $val) {
                        $emailSamBody = '';
                        $param = array();
                        $param["empName"] = $val['Ename2'];
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
                            <h4>' . $masterrec['srp_erp_mfq_customerinquiry.ciCode'] . '</h4>
                            <label>Contact Person :  ' . $masterrec['contactPerson'] . '</label><br>
                            <label>Customer Phone No :  ' . $masterrec['customerPhoneNo'] . ' </label><br>
                            <label>Customer Email : ' . $masterrec['customerEmail'] . '  </label><br>
                            <label>Client Reference No :  ' . $masterrec['referenceNo'] . ' </label><br>
                            <label><b>Planned Submission Date :  ' . $masterrec['plannedsubmissiondate'] . ' </b></label><br>
                            <br>
                            <label>Description :' . $masterrec['description'] . '  </label>
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
                                    <td style="">Responsible: ' . $masterrec['Engineeringname'] . '</td>
                                <td style=""> </td>
                                <td style=""> </td>
                                    <td style="">Responsible:' . $masterrec['Purchasingname'] . '</td>
                                    <td style=""> </td>
                                <td style=""> </td>
                                    </tr>
                            <tr>
                                    <td style="">End Date: ' . $masterrec['engineeringEndDateformated'] . '</td>
                                <td style=""> </td>
                                <td style=""> </td>
                                    <td style="">End Date:' . $masterrec['purchasingEndDateDateformated'] . '</td>
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
                                    <td style="">Responsible:' . $masterrec['Productionname'] . '</td>
                                <td style=""> </td>
                                <td style=""> </td>
                                    <td style="">Responsible:' . $masterrec['qaqcname'] . '</td>
                                    <td style=""> </td>
                                <td style=""> </td>
                                    </tr>
                            <tr>
                                    <td style="">End Date:' . $masterrec['productionEndDateformated'] . ' </td>
                                <td style=""> </td>
                                <td style=""> </td>
                                    <td style="">End Date:' . $masterrec['QAQCEndDateDateformated'] . '</td>
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
                            'toEmail' => $val['EEmail'],
                            'subject' => 'RFQ Generated - ' . $masterrec["ciCode"] . ' - ' . $masterrec["segmentcode"] . ' - ' . $masterrec["CustomerNamemfq"],
                            'param' => $param
                        ];
                        
                        send_approvalEmail($mailData, 1, $path);
                    }
                }
                return array('s', 'Customer Inquiry : Approved Successfully');
            }
        }
    }


    function delete_customerInquiryDetail()
    {
        $masterID = $this->input->post('masterID');
        $this->db->select('ciDetailID');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->where('ciMasterID', $masterID);
        $result = $this->db->get()->result_array();
        $code = count($result) == 1 ? 1 : 2;

        $result = $this->db->delete('srp_erp_mfq_customerinquirydetail', array('ciDetailID' => $this->input->post('ciDetailID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!', 'code' => $code);
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    public function load_mfq_customerInquiry()
    {
        $convertFormat = convert_date_format_sql();
        $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquiry.createdUserID, srp_erp_mfq_customerinquiry.confirmedYN, srp_erp_mfq_customerinquiry.inquirySource, srp_erp_mfq_customerinquiry.locationAssigned, srp_erp_mfq_customerinquiry.rfqStatus, srp_erp_mfq_customerinquiry.documentStatus, srp_erp_mfq_customerinquiry.orderStatus, srp_erp_mfq_customerinquiry.confirmedByEmpID, srp_erp_mfq_customerinquiry.createdUserName as createdUserName, DATE_FORMAT(srp_erp_mfq_customerinquiry.createdDateTime,\'' . $convertFormat . '\') as createdDateTime,
            DATE_FORMAT(srp_erp_mfq_customerinquiry.confirmedDate,\'' . $convertFormat . '\') as confirmedDate,srp_erp_mfq_customerinquiry.confirmedByName as confirmedByName, DATE_FORMAT(QAQCSubmissionDate,\'' . $convertFormat . '\') as QAQCSubmissionDatecon,DATE_FORMAT(productionSubmissionDate,\'' . $convertFormat . '\') as productionSubmissionDatecon,
            DATE_FORMAT(purchasingSubmissionDate,\'' . $convertFormat . '\') as purchasingSubmissionDatecon,DATE_FORMAT(engineeringSubmissionDate,\'' . $convertFormat . '\') as engineeringSubmissionDatecon,
            DATE_FORMAT(engineeringEndDate,\'' . $convertFormat . '\') as engineeringEndDate,DATE_FORMAT(purchasingEndDate,\'' . $convertFormat . '\') as purchasingEndDate,
            DATE_FORMAT(productionEndDate,\'' . $convertFormat . '\') as productionEndDate,DATE_FORMAT(QAQCSubmissionDate,\'%' . $convertFormat . '\') as QAQCEndDate,
            DATE_FORMAT(documentDate,\'' . $convertFormat . '\') as documentDate,DATE_FORMAT(dueDate,\'' . $convertFormat . '\') as dueDate,DATE_FORMAT(deliveryDate,\'' . $convertFormat . '\') as deliveryDate,
            srp_erp_mfq_customerinquiry.description,paymentTerm, srp_erp_mfq_customerinquiry.mfqCustomerAutoID,ciMasterID as ciMasterID,ciCode,srp_erp_mfq_customermaster.CustomerName,referenceNo,statusID,type,
            engineeringResponsibleEmpID,purchasingResponsibleEmpID,productionResponsibleEmpID,QAQCResponsibleEmpID,DATEDIFF(engineeringSubmissionDate,engineeringEndDate) as Engineeringnoofdays,DATEDIFF(purchasingSubmissionDate,purchasingEndDate) as purchasingnoofdays,DATEDIFF(productionSubmissionDate,productionEndDate) as productionnoofdays,DATEDIFF(QAQCSubmissionDate,QAQCEndDate) as qaqcnoofdays,DATEDIFF(deliveryDate,dueDate) AS noofdaysdelaydeliverydue, 
            GROUP_CONCAT(DISTINCT engineeringresponsible.Ename2) as engineeringResponsibleEmpName,GROUP_CONCAT(DISTINCT purchasingresposible.Ename2) as purchasingResponsibleEmpName, GROUP_CONCAT(DISTINCT productionresponsiblemp.Ename2) as productionResponsibleEmpName, GROUP_CONCAT(DISTINCT qaqcresponsiblemp.Ename2) as qaqcResponsibleEmpName, GROUP_CONCAT(DISTINCT financeresponsible.Ename2) as financeResponsibleEmpName,
            srp_erp_mfq_customerinquiry.segmentID as rfqheadersegmentid,srp_erp_mfq_customerinquiry.contactPerson as contactpresongrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNorfq,srp_erp_mfq_customerinquiry.customerEmail as customerEmailrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNocustomer,srp_erp_mfq_customerinquiry.customerEmail as customerEmailcustomer,
            segment.segmentCode as department,srp_erp_mfq_customerinquiry.contactPerson as contactPersonIN,srp_erp_mfq_customerinquiry.remindEmailBefore,srp_erp_mfq_customerinquiry.proposalEngineerID, srp_erp_mfq_customerinquiry.estimatedEmpID, srp_erp_mfq_customerinquiry.SalesManagerID, srp_erp_mfq_customerinquiry.financeResponsibleEmpID,srp_erp_mfq_customerinquiry.financeSubmissionDate,srp_erp_mfq_customerinquiry.financeEndDate,
            DATEDIFF(srp_erp_mfq_customerinquiry.financeSubmissionDate,srp_erp_mfq_customerinquiry.financeEndDate) as financenoofdays,srp_erp_mfq_customerinquiry.transactionCurrencyID,
            currencymaster.CurrencyCode,srp_erp_mfq_customerinquiry.manufacturingType');
        $this->db->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_customerinquiry.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeesdetails engineeringresponsible', 'FIND_IN_SET(engineeringresponsible.EIdNo, srp_erp_mfq_customerinquiry.engineeringResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails financeresponsible', 'FIND_IN_SET(financeresponsible.EIdNo, srp_erp_mfq_customerinquiry.financeResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails purchasingresposible', 'FIND_IN_SET(purchasingresposible.EIdNo, srp_erp_mfq_customerinquiry.purchasingResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails productionresponsiblemp', 'FIND_IN_SET(productionresponsiblemp.EIdNo, srp_erp_mfq_customerinquiry.productionResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails qaqcresponsiblemp', 'FIND_IN_SET(qaqcresponsiblemp.EIdNo, srp_erp_mfq_customerinquiry.QAQCResponsibleEmpID)', 'left');
        $this->db->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = srp_erp_mfq_customerinquiry.segmentID', 'left');
        $this->db->join('srp_erp_segment segment', 'segment.segmentID = mfqsegment.segmentID', 'left');
        $this->db->join('srp_erp_currencymaster currencymaster', 'currencymaster.currencyID = srp_erp_mfq_customerinquiry.currencyID', 'left');
        $this->db->from('srp_erp_mfq_customerinquiry');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->group_by('srp_erp_mfq_customerinquiry.mfqCustomerAutoID');
        $result = $this->db->get()->row_array();
        return $result;
    }

    

    public function load_mfq_customerInquiryprint()
    {
        $convertFormat = convert_date_format_sql();
        // $ciMasterID =$this->input->post('ciMasterID');
        $ciMasterID =$this->uri->segment(3);
        $this->db->select('srp_erp_mfq_customerinquiry.createdUserID, srp_erp_mfq_customerinquiry.confirmedYN, srp_erp_mfq_customerinquiry.inquirySource, srp_erp_mfq_customerinquiry.locationAssigned, srp_erp_mfq_customerinquiry.rfqStatus, srp_erp_mfq_customerinquiry.documentStatus, srp_erp_mfq_customerinquiry.orderStatus, srp_erp_mfq_customerinquiry.confirmedByEmpID, srp_erp_mfq_customerinquiry.createdUserName as createdUserName, DATE_FORMAT(srp_erp_mfq_customerinquiry.createdDateTime,\'' . $convertFormat . '\') as createdDateTime,
            DATE_FORMAT(srp_erp_mfq_customerinquiry.confirmedDate,\'' . $convertFormat . '\') as confirmedDate,srp_erp_mfq_customerinquiry.confirmedByName as confirmedByName, DATE_FORMAT(QAQCSubmissionDate,\'' . $convertFormat . '\') as QAQCSubmissionDatecon,DATE_FORMAT(productionSubmissionDate,\'' . $convertFormat . '\') as productionSubmissionDatecon,
            DATE_FORMAT(purchasingSubmissionDate,\'' . $convertFormat . '\') as purchasingSubmissionDatecon,DATE_FORMAT(engineeringSubmissionDate,\'' . $convertFormat . '\') as engineeringSubmissionDatecon,
            DATE_FORMAT(engineeringEndDate,\'' . $convertFormat . '\') as engineeringEndDate,DATE_FORMAT(purchasingEndDate,\'' . $convertFormat . '\') as purchasingEndDate,
            DATE_FORMAT(productionEndDate,\'' . $convertFormat . '\') as productionEndDate,DATE_FORMAT(QAQCSubmissionDate,\'%' . $convertFormat . '\') as QAQCEndDate,
            DATE_FORMAT(documentDate,\'' . $convertFormat . '\') as documentDate,DATE_FORMAT(dueDate,\'' . $convertFormat . '\') as dueDate,DATE_FORMAT(deliveryDate,\'' . $convertFormat . '\') as deliveryDate,
            srp_erp_mfq_customerinquiry.description,paymentTerm, srp_erp_mfq_customerinquiry.mfqCustomerAutoID,ciMasterID as ciMasterID,ciCode,srp_erp_mfq_customermaster.CustomerName,referenceNo,statusID,type,
            engineeringResponsibleEmpID,purchasingResponsibleEmpID,productionResponsibleEmpID,QAQCResponsibleEmpID,DATEDIFF(engineeringSubmissionDate,engineeringEndDate) as Engineeringnoofdays,DATEDIFF(purchasingSubmissionDate,purchasingEndDate) as purchasingnoofdays,DATEDIFF(productionSubmissionDate,productionEndDate) as productionnoofdays,DATEDIFF(QAQCSubmissionDate,QAQCEndDate) as qaqcnoofdays,DATEDIFF(deliveryDate,dueDate) AS noofdaysdelaydeliverydue, 
            GROUP_CONCAT(DISTINCT engineeringresponsible.Ename2) as engineeringResponsibleEmpName,GROUP_CONCAT(DISTINCT purchasingresposible.Ename2) as purchasingResponsibleEmpName, GROUP_CONCAT(DISTINCT productionresponsiblemp.Ename2) as productionResponsibleEmpName, GROUP_CONCAT(DISTINCT qaqcresponsiblemp.Ename2) as qaqcResponsibleEmpName, GROUP_CONCAT(DISTINCT financeresponsible.Ename2) as financeResponsibleEmpName,
            srp_erp_mfq_customerinquiry.segmentID as rfqheadersegmentid,srp_erp_mfq_customerinquiry.contactPerson as contactpresongrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNorfq,srp_erp_mfq_customerinquiry.customerEmail as customerEmailrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNocustomer,srp_erp_mfq_customerinquiry.customerEmail as customerEmailcustomer,
            segment.segmentCode as department,srp_erp_mfq_customerinquiry.contactPerson as contactPersonIN,srp_erp_mfq_customerinquiry.remindEmailBefore,srp_erp_mfq_customerinquiry.proposalEngineerID, srp_erp_mfq_customerinquiry.estimatedEmpID, srp_erp_mfq_customerinquiry.SalesManagerID, srp_erp_mfq_customerinquiry.financeResponsibleEmpID,srp_erp_mfq_customerinquiry.financeSubmissionDate,srp_erp_mfq_customerinquiry.financeEndDate,
            DATEDIFF(srp_erp_mfq_customerinquiry.financeSubmissionDate,srp_erp_mfq_customerinquiry.financeEndDate) as financenoofdays,srp_erp_mfq_customerinquiry.transactionCurrencyID,
            currencymaster.CurrencyCode,srp_erp_mfq_customerinquiry.manufacturingType');
        $this->db->join('srp_erp_mfq_customermaster', 'srp_erp_mfq_customermaster.mfqCustomerAutoID = srp_erp_mfq_customerinquiry.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeesdetails engineeringresponsible', 'FIND_IN_SET(engineeringresponsible.EIdNo, srp_erp_mfq_customerinquiry.engineeringResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails financeresponsible', 'FIND_IN_SET(financeresponsible.EIdNo, srp_erp_mfq_customerinquiry.financeResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails purchasingresposible', 'FIND_IN_SET(purchasingresposible.EIdNo, srp_erp_mfq_customerinquiry.purchasingResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails productionresponsiblemp', 'FIND_IN_SET(productionresponsiblemp.EIdNo, srp_erp_mfq_customerinquiry.productionResponsibleEmpID)', 'left');
        $this->db->join('srp_employeesdetails qaqcresponsiblemp', 'FIND_IN_SET(qaqcresponsiblemp.EIdNo, srp_erp_mfq_customerinquiry.QAQCResponsibleEmpID)', 'left');
        $this->db->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = srp_erp_mfq_customerinquiry.segmentID', 'left');
        $this->db->join('srp_erp_segment segment', 'segment.segmentID = mfqsegment.segmentID', 'left');
        $this->db->join('srp_erp_currencymaster currencymaster', 'currencymaster.currencyID = srp_erp_mfq_customerinquiry.currencyID', 'left');
        $this->db->from('srp_erp_mfq_customerinquiry');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->group_by('srp_erp_mfq_customerinquiry.mfqCustomerAutoID');
        $result = $this->db->get()->row_array();
        return $result;
    }
    

    function load_mfq_customerInquiryDetail()
    {
        $convertFormat = convert_date_format_sql();
        // $ciMasterID =$this->uri->segment(3);
        $ciMasterID =$this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquirydetail.*, IFNULL(expectedDeliveryWeeks,"") AS expectedDeliveryWeeks , DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,srp_erp_mfq_segment.description as segment,bomm.bomMasterID,IFNULL(bomm.cost,0) as estimatedCost,bomMaster.bomMasterID as bomMasterID2');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_segment', 'mfqSegmentID = srp_erp_mfq_customerinquirydetail.segmentID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID ', 'left');
        $this->db->join('srp_erp_mfq_billofmaterial as bomMaster', 'bomMaster.bomMasterID = srp_erp_mfq_customerinquirydetail.bomMasterID ', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function load_mfq_customerInquiryDetailprint()
    {
        $convertFormat = convert_date_format_sql();
        $ciMasterID =$this->uri->segment(3);
        // $ciMasterID =$this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquirydetail.*, IFNULL(expectedDeliveryWeeks,"") AS expectedDeliveryWeeks , DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,srp_erp_mfq_segment.description as segment,bomm.bomMasterID,IFNULL(bomm.cost,0) as estimatedCost');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_segment', 'mfqSegmentID = srp_erp_mfq_customerinquirydetail.segmentID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function load_mfq_customerInquiryDetailOnlyItem()
    {
        $convertFormat = convert_date_format_sql();
        $ciMasterID = $this->input->post('ciMasterID');
        $currency = $this->db->query("SELECT currencyID FROM srp_erp_mfq_customerinquiry WHERE ciMasterID = {$ciMasterID}")->row('currencyID');
        $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $currency);
        $conversion = $default_currency['conversion'];

        $this->db->select('srp_erp_mfq_customerinquirydetail.*,DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,srp_erp_mfq_segment.description as segment,bomm.bomMasterID,IFNULL(bomm.cost / '. $conversion .',0) as estimatedCost');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_segment', 'mfqSegmentID = srp_erp_mfq_customerinquirydetail.segmentID', 'left');
        // $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0)+ IFNULL( mc.totalValue, 0 ))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID	LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID ) mc ON mc.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0)+ IFNULL( mc.totalValue, 0 ))) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID	LEFT JOIN ( SELECT SUM( totalValue ) AS totalValue, bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID ) mc ON mc.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID AND bomm.bomMasterID = srp_erp_mfq_customerinquirydetail.bomMasterID ', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->where('srp_erp_mfq_customerinquirydetail.mfqItemID IS NOT NULL');
        $result = $this->db->get()->result_array();

        return $result;
    }


    function attachement_upload()
    {
        $this->db->trans_start();
        $file_name = 'MFQ_' . $this->input->post('documentID') . '_' . time();
        $config['upload_path'] = realpath(APPPATH . '../attachments/MFQ');
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '5120'; // 5 MB
        $config['file_name'] = $file_name;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload("document_file")) {
            echo json_encode(array('status' => 0, 'type' => 'w', 'message' => 'Upload failed ' . $this->upload->display_errors()));
        } else {
            $upload_data = $this->upload->data();
//$fileName                       = $file_name.'_'.$upload_data["file_ext"];
            $data['workFlowID'] = trim($this->input->post('workFlowID') ?? '');
            $data['workProcessID'] = trim($this->input->post('workProcessID') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $file_name . $upload_data["file_ext"];
            $data['fileType'] = trim($upload_data["file_ext"]);
            $data['fileSize'] = trim($upload_data["file_size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_mfq_workflowattachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Upload failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Successfully Uploaded.');
            }
        }
    }

    function load_attachments()
    {
        $this->db->where('documentSystemCode', $this->input->post('documentSystemCode'));
        $this->db->where('documentID', $this->input->post('documentID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_documentattachments')->result_array();
        return $data;
    }

    function fetch_finish_goods()
    {
        $dataArr = array();
        $dataArr2 = array();
        $companyID = current_companyID();
        $search_string = "%" . $_GET['query'] . "%";
        $sql = 'SELECT mfqCategoryID,mfqSubcategoryID,secondaryItemCode,mfqSubSubCategoryID,itemSystemCode,costGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,srp_erp_mfq_itemmaster.mfqItemID as itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF" WHEN 0 THEN "MI"
END," - ",IFNULL(itemDescription,""), " (" ,IFNULL(itemSystemCode,""),")") AS "Match",partNo,srp_erp_unit_of_measure.unitDes as uom,IFNULL(bomm.cost,0) as cost,bomm.bomMasterID FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID
LEFT JOIN (SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm ON bomm.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID
WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR secondaryItemCode LIKE "' . $search_string . '") AND srp_erp_mfq_itemmaster.companyID = "' . $companyID . '" AND (srp_erp_mfq_itemmaster.itemType = 2 OR srp_erp_mfq_itemmaster.itemType = 3 OR srp_erp_mfq_itemmaster.itemType = 0) AND isActive="1"';
        $data = $this->db->query($sql)->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'mfqItemID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'uom' => $val['uom'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'partNo' => $val['partNo'], 'cost' => $val['cost'], 'bomMasterID' => $val['bomMasterID']);
            }
        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function save_customer_inquiry_approval()
    {
        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_id = trim($this->input->post('ciMasterID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'CI');
        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('ciMasterID', $system_id);
            $this->db->update('srp_erp_mfq_customerinquiry', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Customer Inquiry Approved Successfully');
        }
    }

    function fetchcontactpersonemail()
    {
        $customerid = $this->input->post('customerid');
        $companyid = current_companyID();

        $data = $this->db->query("SELECT customerEmail,customerTelephone FROM `srp_erp_mfq_customermaster` where companyID = '{$companyid}' AND mfqCustomerAutoID = '{$customerid}'")->row_array();
        return $data;
    }

    function fetch_pending_rfq()
    {
       
        $companyID = current_companyID();

        

        $data = $this->db->query("SELECT proposalEngineerID,ciMasterID FROM `srp_erp_mfq_customerinquiry` where companyID = '{$companyID}' GROUP BY srp_erp_mfq_customerinquiry.proposalEngineerID")->result_array();

        $result=[];

        foreach($data as $val2){

                $data_eng = $this->db->query("SELECT
                    i.ciMasterID,i.ciCode,job.workProcessID as workProcessID,cus.CustomerName,sou.description as source,i.confirmedYN,i.approvedYN,i.proposalEngineerID,cou.CountryDes as CountryDes,i.description,i.category,est.totDiscountPrice,est.totalSellingPrice,i.type,i.locationAssigned,i.confirmedByName,i.inquirySource,e.Ename2,i.documentDate,i.rfqStatus,i.documentStatus,i.orderStatus,i.dueDate,i.deliveryDate,i.submissionStatus,est.poNumber,est.documentDate as poDate,est.materialCertificationComment,est.deliveryDate as podeliveryDate,job.documentCode as jobNumber
                FROM
                srp_erp_mfq_customerinquiry i
                    LEFT JOIN srp_erp_mfq_customermaster cus ON cus.mfqCustomerAutoID = i.mfqCustomerAutoID
                    LEFT JOIN srp_erp_mfq_estimatemaster  est ON est.ciMasterID = i.ciMasterID
                    LEFT JOIN srp_employeesdetails e on e.EIdNo = i.proposalEngineerID
                    LEFT JOIN srp_erp_mfq_job job on job.estimateMasterID = est.estimateMasterID
                    LEFT JOIN srp_erp_mfq_customer_inquiry_source sou on sou.sourceID = i.inquirySource
                    LEFT JOIN srp_countrymaster cou on cou.countryID = i.locationAssigned

                WHERE
                i.companyID = {$companyID}  AND proposalEngineerID = '{$val2['proposalEngineerID']}' AND i.rfqStatus IN (2,3)")->result_array();

                //print_r($data_eng);exit;

                if(count($data_eng)>0){
                    $count=0;
                    $firm=0;
                    $budget=0;

                    $count_tender=0;
                    $count_rfq=0;
                    $count_spc=0;

                    foreach($data_eng as $val){
                        //$isEstimateCreated = $this->db->query("SELECT estimateMasterID FROM `srp_erp_mfq_estimatemaster` WHERE ciMasterID = {$val['ciMasterID']} AND companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                        //$isEstimateDetCreated = $this->db->query("SELECT srp_erp_mfq_estimatedetail.estimateMasterID FROM `srp_erp_mfq_estimatedetail` LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID WHERE srp_erp_mfq_estimatedetail.ciMasterID = {$val['ciMasterID']} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                    
                       // if (empty($isEstimateCreated) && empty($isEstimateDetCreated)) {


                            //$count=$count+1;
                        
                        //}
                        $count=$count+1;
                        if($val['rfqStatus']==2){
                                $firm=$firm+1;
                            
                        }
                        if($val['rfqStatus']==3){
                            $budget=$budget+1;
                        
                    }
                    
                    }

                    // if($count_tender>0){

                        $arr=array('type'=>'','estimator'=>$data_eng[0]['Ename2'],'firm'=>$firm,'budget'=>$budget,'remark'=>$data_eng[0]['description'],'tot_rfq'=>$count);
                        $result[]=$arr;
                    // }
                }
                   

        }
    
        return $result;
    }

    function fetch_total_pending_rfq()
    {
       
        $companyID = current_companyID();

        $data = $this->db->query("SELECT proposalEngineerID,ciMasterID FROM `srp_erp_mfq_customerinquiry` where companyID = '{$companyID}' GROUP BY srp_erp_mfq_customerinquiry.proposalEngineerID")->result_array();

        $result=[];

        foreach($data as $val2){

                $data_eng = $this->db->query("SELECT
                    i.ciMasterID,i.ciCode,job.workProcessID as workProcessID,cus.CustomerName,sou.description as source,i.confirmedYN,i.approvedYN,i.proposalEngineerID,cou.CountryDes as CountryDes,i.description,i.category,est.totDiscountPrice,est.totalSellingPrice,i.type,i.locationAssigned,i.confirmedByName,i.inquirySource,e.Ename2,i.documentDate,i.rfqStatus,i.documentStatus,i.orderStatus,i.dueDate,i.deliveryDate,i.submissionStatus,est.poNumber,est.documentDate as poDate,est.materialCertificationComment,est.deliveryDate as podeliveryDate,job.documentCode as jobNumber
                FROM
                srp_erp_mfq_customerinquiry i
                    LEFT JOIN srp_erp_mfq_customermaster cus ON cus.mfqCustomerAutoID = i.mfqCustomerAutoID
                    LEFT JOIN srp_erp_mfq_estimatemaster  est ON est.ciMasterID = i.ciMasterID
                    LEFT JOIN srp_employeesdetails e on e.EIdNo = i.proposalEngineerID
                    LEFT JOIN srp_erp_mfq_job job on job.estimateMasterID = est.estimateMasterID
                    LEFT JOIN srp_erp_mfq_customer_inquiry_source sou on sou.sourceID = i.inquirySource
                    LEFT JOIN srp_countrymaster cou on cou.countryID = i.locationAssigned

                WHERE
                i.companyID = {$companyID}  AND proposalEngineerID = '{$val2['proposalEngineerID']}' AND i.rfqStatus IN (2,3)")->result_array();

                $count=0;
                foreach($data_eng as $val){
                    //$isEstimateCreated = $this->db->query("SELECT estimateMasterID FROM `srp_erp_mfq_estimatemaster` WHERE ciMasterID = {$val['ciMasterID']} AND companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                    //$isEstimateDetCreated = $this->db->query("SELECT srp_erp_mfq_estimatedetail.estimateMasterID FROM `srp_erp_mfq_estimatedetail` LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID WHERE srp_erp_mfq_estimatedetail.ciMasterID = {$val['ciMasterID']} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                   
                   // if (empty($isEstimateCreated) && empty($isEstimateDetCreated)) {
                        $count=$count+1;
                       
                    //}
                   
                }

                if($count>0 && count($data_eng)>0){
                    $arr=array('name'=>$data_eng[0]['Ename2'],'y'=>$count);
                    $result[]=$arr;
                }

        }
     
        return $result;
    }

    function fetch_total_pending_rfq_bar_chart()
    {
       
        $companyID = current_companyID();

        

        $data = $this->db->query("SELECT proposalEngineerID,ciMasterID FROM `srp_erp_mfq_customerinquiry` where companyID = '{$companyID}' GROUP BY srp_erp_mfq_customerinquiry.proposalEngineerID")->result_array();

        $result=[];
        $result_name=[];
        $result_value=[];
        $result_value_b=[];

        foreach($data as $val2){

                $data_eng = $this->db->query("SELECT
                    i.ciMasterID,i.ciCode,job.workProcessID as workProcessID,cus.CustomerName,sou.description as source,i.confirmedYN,i.approvedYN,i.proposalEngineerID,cou.CountryDes as CountryDes,i.description,i.category,est.totDiscountPrice,est.totalSellingPrice,i.type,i.locationAssigned,i.confirmedByName,i.inquirySource,e.Ename2,i.documentDate,i.rfqStatus,i.documentStatus,i.orderStatus,i.dueDate,i.deliveryDate,i.submissionStatus,est.poNumber,est.documentDate as poDate,est.materialCertificationComment,est.deliveryDate as podeliveryDate,job.documentCode as jobNumber
                FROM
                srp_erp_mfq_customerinquiry i
                    LEFT JOIN srp_erp_mfq_customermaster cus ON cus.mfqCustomerAutoID = i.mfqCustomerAutoID
                    LEFT JOIN srp_erp_mfq_estimatemaster  est ON est.ciMasterID = i.ciMasterID
                    LEFT JOIN srp_employeesdetails e on e.EIdNo = i.proposalEngineerID
                    LEFT JOIN srp_erp_mfq_job job on job.estimateMasterID = est.estimateMasterID
                    LEFT JOIN srp_erp_mfq_customer_inquiry_source sou on sou.sourceID = i.inquirySource
                    LEFT JOIN srp_countrymaster cou on cou.countryID = i.locationAssigned

                WHERE
                i.companyID = {$companyID}  AND proposalEngineerID = '{$val2['proposalEngineerID']}' AND i.rfqStatus IN (2,3)")->result_array();

                $count=0;
                $firm=0;
                $budget=0;
                foreach($data_eng as $val){
                    //$isEstimateCreated = $this->db->query("SELECT estimateMasterID FROM `srp_erp_mfq_estimatemaster` WHERE ciMasterID = {$val['ciMasterID']} AND companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                   //$isEstimateDetCreated = $this->db->query("SELECT srp_erp_mfq_estimatedetail.estimateMasterID FROM `srp_erp_mfq_estimatedetail` LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID WHERE srp_erp_mfq_estimatedetail.ciMasterID = {$val['ciMasterID']} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} AND (isDeleted IS NULL OR isDeleted != 1)")->result_array();
                   
                   // if (empty($isEstimateCreated) && empty($isEstimateDetCreated)) {
                        $count=$count+1;

                        if($val['rfqStatus']==2){
                            $firm=$firm+1;
                        
                        }
                        if($val['rfqStatus']==3){
                            $budget=$budget+1;
                        
                        }
                       
                   // }
                   
                }

                if(count($data_eng)>0){
                    $arr=$data_eng[0]['Ename2'];
                    $result_name[]=$arr;               
                   
                        $result_value[]=$firm;              
                        
                        $result_value_b[]=$budget;
                    }
        }

        $result['res_name']=$result_name;
        $result['res_val_firm']=$result_value;
        $result['res_val_budget']=$result_value_b;
        return $result;
    }

    function fetch_total_project_mfq()
    {
       
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $result2 = $this->db->query("SELECT         
                    srp_erp_mfq_job.workProcessID,
                    inq.proposalEngineerID

                    FROM
                        `srp_erp_mfq_job`
                        LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
                        LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
                            
                    WHERE
                        srp_erp_mfq_job.companyID = {$companyID}
                    
                    GROUP BY inq.proposalEngineerID")->result_array();


        $resul=[];

        foreach($result2 as $val){
        
            $data_eng = $this->db->query("SELECT
            documentCode,
            poNumber,
            workProcessID,
            
            srp_erp_mfq_job.description,
            templateDescription,
            ROUND(job2.percentage, 2) AS percentage,
            CONCAT( itemSystemCode, ' - ', itemDescription ) AS itemDescription,
            srp_erp_mfq_job.approvedYN,
            srp_erp_mfq_job.confirmedYN,
            isFromEstimate,
            cust.CustomerName AS CustomerName,
            e.Ename2 AS Ename2,
            srp_erp_mfq_job.estimateMasterID AS estimateMasterID,
            srp_erp_mfq_job.linkedJobID AS linkedJobID,
            srp_erp_mfq_job.isDeleted AS isDeleted 
            FROM
            `srp_erp_mfq_job`
            LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `srp_erp_mfq_job`.`mfqCustomerAutoID`
          
                
            LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
            LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
            LEFT JOIN srp_employeesdetails `e` ON `e`.EIdNo = `inq`.proposalEngineerID
            LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID`
            LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID`
            LEFT JOIN `srp_erp_mfq_deliverynotedetail` ON `srp_erp_mfq_deliverynotedetail`.`jobID` = `srp_erp_mfq_job`.`workProcessID`
            LEFT JOIN `srp_erp_mfq_deliverynote` ON `srp_erp_mfq_deliverynote`.`deliverNoteID` = `srp_erp_mfq_deliverynotedetail`.`deliveryNoteID`
            LEFT JOIN (
            SELECT
                (
                SUM( a.percentage )/ COUNT( * )) AS percentage,
                linkedJobID 
            FROM
                srp_erp_mfq_job
                LEFT JOIN (
                SELECT
                    jobID,
                    COUNT(*) AS totCount,
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END ) AS completedCount,(
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END )/ COUNT(*)) * 100 AS percentage 
                FROM
                    srp_erp_mfq_workflowstatus 
                GROUP BY
                    jobID 
                ) a ON a.jobID = srp_erp_mfq_job.workProcessID 
            GROUP BY
                linkedJobID 
            ) job2 ON `job2`.`linkedJobID` = `srp_erp_mfq_job`.`workProcessID` 
            WHERE
                srp_erp_mfq_job.companyID = {$companyID} AND inq.proposalEngineerID={$val['proposalEngineerID']}
                AND ( `srp_erp_mfq_job`.`linkedJobID` = 0 OR `srp_erp_mfq_job`.`linkedJobID` = '' OR `srp_erp_mfq_job`.`linkedJobID` IS NULL ) 
            ORDER BY workProcessID DESC")->result_array();

            $pending=0;
            $completed=0;
            $count=0;

            foreach($data_eng as $val){
                $count=$count+1;
                if($val['percentage']==100){
                    $completed= $completed+1;
                }else{
                    $pending=$pending+1;
                }

            }

            $arr=array('estimator'=>$data_eng[0]['Ename2'],'pending'=>$pending,'complete'=>$completed,'remark'=>$data_eng[0]['description'],'total'=>$count);
            $result[]=$arr;

        }
    
        return $result;
    }


    function generate_job_total_barchart()
    {
       
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $result2 = $this->db->query("SELECT
            
            srp_erp_mfq_job.workProcessID,
            inq.proposalEngineerID

            FROM
                `srp_erp_mfq_job`
                LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
                LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
               
                
            WHERE
                srp_erp_mfq_job.companyID = {$companyID}
             
             GROUP BY inq.proposalEngineerID")->result_array();

        $resul=[];     
        foreach($result2 as $val){
        
            $data_eng = $this->db->query("SELECT
            documentCode,
            poNumber,
            workProcessID,
            
            srp_erp_mfq_job.description,
            templateDescription,
            ROUND(job2.percentage, 2) AS percentage,
            CONCAT( itemSystemCode, ' - ', itemDescription ) AS itemDescription,
            srp_erp_mfq_job.approvedYN,
            srp_erp_mfq_job.confirmedYN,
            isFromEstimate,
            cust.CustomerName AS CustomerName,
            e.Ename2 AS Ename2,
            srp_erp_mfq_job.estimateMasterID AS estimateMasterID,
            srp_erp_mfq_job.linkedJobID AS linkedJobID,
            srp_erp_mfq_job.isDeleted AS isDeleted 
            FROM
            `srp_erp_mfq_job`
            LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `srp_erp_mfq_job`.`mfqCustomerAutoID`
          
                
            LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
            LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
            LEFT JOIN srp_employeesdetails `e` ON `e`.EIdNo = `inq`.proposalEngineerID
            LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID`
            LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID`
            LEFT JOIN `srp_erp_mfq_deliverynotedetail` ON `srp_erp_mfq_deliverynotedetail`.`jobID` = `srp_erp_mfq_job`.`workProcessID`
            LEFT JOIN `srp_erp_mfq_deliverynote` ON `srp_erp_mfq_deliverynote`.`deliverNoteID` = `srp_erp_mfq_deliverynotedetail`.`deliveryNoteID`
            LEFT JOIN (
            SELECT
                (
                SUM( a.percentage )/ COUNT( * )) AS percentage,
                linkedJobID 
            FROM
                srp_erp_mfq_job
                LEFT JOIN (
                SELECT
                    jobID,
                    COUNT(*) AS totCount,
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END ) AS completedCount,(
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END )/ COUNT(*)) * 100 AS percentage 
                FROM
                    srp_erp_mfq_workflowstatus 
                GROUP BY
                    jobID 
                ) a ON a.jobID = srp_erp_mfq_job.workProcessID 
            GROUP BY
                linkedJobID 
            ) job2 ON `job2`.`linkedJobID` = `srp_erp_mfq_job`.`workProcessID` 
            WHERE
                srp_erp_mfq_job.companyID = {$companyID} AND inq.proposalEngineerID={$val['proposalEngineerID']}
                AND ( `srp_erp_mfq_job`.`linkedJobID` = 0 OR `srp_erp_mfq_job`.`linkedJobID` = '' OR `srp_erp_mfq_job`.`linkedJobID` IS NULL ) 
            ORDER BY workProcessID DESC")->result_array();

            $pending=0;
            $completed=0;
            $count=0;

            foreach($data_eng as $val){
                $count=$count+1;

            }

            if($count>0 && count($data_eng)>0){
                $arr=array('name'=>$data_eng[0]['Ename2'],'y'=>$count);
                $result[]=$arr;
            }

        }
    
        return $result;
    }

    function generate_job_total_linechart()
    {
       
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $result2 = $this->db->query("SELECT
            
            srp_erp_mfq_job.workProcessID,
            inq.proposalEngineerID

            FROM
                `srp_erp_mfq_job`
                LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
                LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
               
                
            WHERE
                srp_erp_mfq_job.companyID = {$companyID}
             
             GROUP BY inq.proposalEngineerID")->result_array();

        $resul=[];
        $result_value_b=[];
        $result_value=[];
        $result_name=[];     
        foreach($result2 as $val){
        
            $data_eng = $this->db->query("SELECT
            documentCode,
            poNumber,
            workProcessID,
            
            srp_erp_mfq_job.description,
            templateDescription,
            ROUND(job2.percentage, 2) AS percentage,
            CONCAT( itemSystemCode, ' - ', itemDescription ) AS itemDescription,
            srp_erp_mfq_job.approvedYN,
            srp_erp_mfq_job.confirmedYN,
            isFromEstimate,
            cust.CustomerName AS CustomerName,
            e.Ename2 AS Ename2,
            srp_erp_mfq_job.estimateMasterID AS estimateMasterID,
            srp_erp_mfq_job.linkedJobID AS linkedJobID,
            srp_erp_mfq_job.isDeleted AS isDeleted 
            FROM
            `srp_erp_mfq_job`
            LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `srp_erp_mfq_job`.`mfqCustomerAutoID`
          
                
            LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
            LEFT JOIN srp_erp_mfq_customerinquiry `inq` ON `estimate`.`ciMasterID` = `inq`.`ciMasterID`
            LEFT JOIN srp_employeesdetails `e` ON `e`.EIdNo = `inq`.proposalEngineerID
            LEFT JOIN `srp_erp_mfq_templatemaster` ON `srp_erp_mfq_templatemaster`.`templateMasterID` = `srp_erp_mfq_job`.`workFlowTemplateID`
            LEFT JOIN `srp_erp_mfq_itemmaster` ON `srp_erp_mfq_itemmaster`.`mfqItemID` = `srp_erp_mfq_job`.`mfqItemID`
            LEFT JOIN `srp_erp_mfq_deliverynotedetail` ON `srp_erp_mfq_deliverynotedetail`.`jobID` = `srp_erp_mfq_job`.`workProcessID`
            LEFT JOIN `srp_erp_mfq_deliverynote` ON `srp_erp_mfq_deliverynote`.`deliverNoteID` = `srp_erp_mfq_deliverynotedetail`.`deliveryNoteID`
            LEFT JOIN (
            SELECT
                (
                SUM( a.percentage )/ COUNT( * )) AS percentage,
                linkedJobID 
            FROM
                srp_erp_mfq_job
                LEFT JOIN (
                SELECT
                    jobID,
                    COUNT(*) AS totCount,
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END ) AS completedCount,(
                    SUM( CASE WHEN STATUS = 1 THEN 1 ELSE 0 END )/ COUNT(*)) * 100 AS percentage 
                FROM
                    srp_erp_mfq_workflowstatus 
                GROUP BY
                    jobID 
                ) a ON a.jobID = srp_erp_mfq_job.workProcessID 
            GROUP BY
                linkedJobID 
            ) job2 ON `job2`.`linkedJobID` = `srp_erp_mfq_job`.`workProcessID` 
            WHERE
                srp_erp_mfq_job.companyID = {$companyID} AND inq.proposalEngineerID={$val['proposalEngineerID']}
                AND ( `srp_erp_mfq_job`.`linkedJobID` = 0 OR `srp_erp_mfq_job`.`linkedJobID` = '' OR `srp_erp_mfq_job`.`linkedJobID` IS NULL ) 
            ORDER BY workProcessID DESC")->result_array();

            $pending=0;
            $completed=0;

     

            foreach($data_eng as $val){

                if($val['percentage']==100){
                    $completed=$completed+1;
                
                }else{
                    $pending=$pending+1;
                }
                        
            }

            if(count($data_eng)>0){
                $arr=$data_eng[0]['Ename2'];
                $result_name[]=$arr;               
               
                $result_value[]=$completed;              
                
                $result_value_b[]=$pending;
            }

        }
    
        $result['res_name']=$result_name;
        $result['res_val_complete']=$result_value;
        $result['res_val_pending']=$result_value_b;
        return $result;
    }

    function actualsubmissiondate()
    {

        $CImasterid = trim($this->input->post('pk') ?? '');
        $deliverydate = $this->input->post('value');
        $companyid = current_companyID();
        $detailsexist = $this->db->query("SELECT * FROM `srp_erp_mfq_customerinquiry` where ciMasterID = '{$CImasterid}' AND companyID = '{$companyid}'")->row_array();

        $date_format_policy = date_format_policy();
        $format_deliverdate = null;
        if (isset($deliverydate) && !empty($deliverydate)) {
            $format_deliverdate = input_format_date($deliverydate, $date_format_policy);
        }
        $data['deliveryDate'] = $format_deliverdate;
        $this->db->where('ciMasterID', $CImasterid);
        $this->db->update('srp_erp_mfq_customerinquiry', $data);


        if ($detailsexist['deliveryDate'] != $format_deliverdate) {
            $data_history['createdUserGroup'] = $this->common_data['user_group'];
            $data_history['createdPCID'] = $this->common_data['current_pc'];
            $data_history['createdUserID'] = $this->common_data['current_userID'];
            $data_history['createdUserName'] = $this->common_data['current_user'];
            $data_history['createdDateTime'] = $this->common_data['current_date'];
            $data_history['documentID'] = 'CI';
            $data_history['companyID'] = current_companyID();
            $data_history['documentID'] = 'CI';
            $data_history['documentMasterID'] = $CImasterid;
            $data_history['changeDescription'] = 'Actual Submission Date Changed';
            $data_history['fieldName'] = 'deliveryDate';
            $data_history['value'] = $format_deliverdate;
            $data_history['previousValue'] = $detailsexist['deliveryDate'];
            $this->db->insert('srp_erp_mfq_changehistory', $data_history);
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Actual Submissioin Date Updated Successfully');
        }
    }

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
                    $data["header"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquirydeadline($value['ciMasterID']);
                    $data["itemDetail"] = $this->MFQ_CustomerInquiry_model->load_mfq_customerInquiryDetaildeadline($value['ciMasterID']);
                    $data['logo'] = mPDFImage;
                    $html = $this->load->view('system/mfq/ajax/customer_inquiry_print', $data, true);
                    $this->load->library('pdf');
                    $path = UPLOAD_PATH . base_url() . '/uploads/Manufacturing/' . 'Customer_Inquiry_' . 'CI' . current_userID() . ".pdf";
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
                                <td class="detailtabletd">' . $detailval['expectedQty'] . '</td>
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
                                'subject' => 'RFQ Generated - ' . $value["ciCode"] . ' ' . $value["segmentcode"] . ' ' . $value["CustomerNamemfq"],
                                'param' => $param
                            ];
                            send_approvalEmail($mailData, 1, $path);
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
        $convertFormat = convert_date_format_sql();
        //  $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('DATE_FORMAT(QAQCSubmissionDate,\'' . $convertFormat . '\') as QAQCSubmissionDatecon,DATE_FORMAT(productionSubmissionDate,\'' . $convertFormat . '\') as productionSubmissionDatecon,DATE_FORMAT(purchasingSubmissionDate,\'' . $convertFormat . '\') as purchasingSubmissionDatecon,DATE_FORMAT(engineeringSubmissionDate,\'' . $convertFormat . '\') as engineeringSubmissionDatecon,DATE_FORMAT(engineeringEndDate,\'' . $convertFormat . '\') as engineeringEndDate,DATE_FORMAT(purchasingEndDate,\'' . $convertFormat . '\') as purchasingEndDate,DATE_FORMAT(productionEndDate,\'' . $convertFormat . '\') as productionEndDate,DATE_FORMAT(QAQCEndDate,\'' . $convertFormat . '\') as QAQCEndDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') as documentDate,DATE_FORMAT(dueDate,\'' . $convertFormat . '\') as dueDate,DATE_FORMAT(deliveryDate,\'' . $convertFormat . '\') as deliveryDate,srp_erp_mfq_customerinquiry.description,paymentTerm, srp_erp_mfq_customerinquiry.mfqCustomerAutoID,ciMasterID as ciMasterID,ciCode,srp_erp_mfq_customermaster.CustomerName,referenceNo,statusID,type,engineeringResponsibleEmpID,purchasingResponsibleEmpID,productionResponsibleEmpID,QAQCResponsibleEmpID,DATEDIFF(engineeringSubmissionDate,engineeringEndDate) as Engineeringnoofdays,DATEDIFF(purchasingSubmissionDate,purchasingEndDate) as purchasingnoofdays,DATEDIFF(productionSubmissionDate,productionEndDate) as productionnoofdays,DATEDIFF(QAQCSubmissionDate,QAQCEndDate) as qaqcnoofdays,DATEDIFF(deliveryDate,dueDate) AS noofdaysdelaydeliverydue,engineeringresponsible.Ename2 as engineeringResponsibleEmpName,purchasingresposible.Ename2 as purchasingResponsibleEmpName,productionresponsiblemp.Ename2 as productionResponsibleEmpName,qaqcresponsiblemp.Ename2 as qaqcResponsibleEmpName,srp_erp_mfq_customerinquiry.segmentID as rfqheadersegmentid,srp_erp_mfq_customerinquiry.contactPerson as contactpresongrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNorfq,srp_erp_mfq_customerinquiry.customerEmail as customerEmailrfq,srp_erp_mfq_customerinquiry.customerPhoneNo as customerPhoneNocustomer,srp_erp_mfq_customerinquiry.customerEmail as customerEmailcustomer,mfqsegment.segmentCode as department,srp_erp_mfq_customerinquiry.contactPerson as contactPersonIN');
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
        $convertFormat = convert_date_format_sql();
        // $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquirydetail.*,DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,srp_erp_mfq_segment.description as segment,bomm.bomMasterID,IFNULL(bomm.cost,0) as estimatedCost');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_segment', 'mfqSegmentID = srp_erp_mfq_customerinquirydetail.segmentID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function decline_customer_inquiry_quote()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $ciMasterID = $this->input->post('ciMasterID');
        $comment = $this->input->post('comment');

        $data['quotationStatus'] = 2;
        $data['statusID'] = 3;
        $data['quotationDeclinedComments'] = $comment;
        $data['quotaionDeclinedDate'] = current_date();
        $data['quotationDeclinedByEmpID'] = current_userID();

        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_mfq_customerinquiry', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return (array('e', 'Failed to update Quote status'));
        } else {
            $this->db->trans_commit();
            return (array('s', 'Quote status updated Successfully'));

        }
    }

    function fetch_customerInquiry_details()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $customer = $this->input->post("customerID");
        $status = $this->input->post("statusID");
        $DepartmentID = $this->input->post("DepartmentID");
        $rfqtype = $this->input->post("rfqtype");
        $proposalengID = $this->input->post("proposalengID");
        $jobstatus = $this->input->post("jobstatus");
        $where = '';

        if ($customer) {
            $where .= " AND ci.mfqCustomerAutoID IN (" . join(',', $customer) . ")";
        }
        if ($status) {
            $where .= " AND ci.statusID = {$status}";
        }
        if($rfqtype)
        {
            $where .= " AND ci.type = {$rfqtype}";
        }
        if($DepartmentID)
        {
            $where .= " AND ci.segmentID IN (" . join(',', $DepartmentID) . ")";
        }
        if(!empty($proposalengID))
        {
            $where .= " AND ci.proposalEngineerID IN (" . join(',', $proposalengID) . ")";
        }
        if($jobstatus)
        {  
             $where .= ' AND jobStatus = '.$jobstatus.' ';
        }

        $result = $this->db->query("SELECT
	jobStatus,
	DATE_FORMAT( DNDate.deliveryDate, '{$convertFormat}' ) AS actualDeliveryDate,
	DATE_FORMAT( mfqjob.expectedDeliveryDate, '{$convertFormat}' ) AS expectedDeliveryDate,
	DATE_FORMAT( jbMas.awardedDate, '{$convertFormat}' ) AS awardedDate,
	jbMas.documentCode AS documentCode,
	jbMas.workProcessID AS workProcessID,
	DATE_FORMAT( ci.documentDate, '{$convertFormat}' ) AS documentDate,
	DATE_FORMAT( ci.dueDate, '{$convertFormat}' ) AS dueDate,
	DATE_FORMAT( ci.deliveryDate, '{$convertFormat}' ) AS deliveryDate,
	ci.description,
	ci.paymentTerm,
	cust.CustomerName AS CustomerName,
	ci.ciMasterID AS ciMasterID,
	ci.ciCode AS ciCode,
	ci.confirmedYN AS confirmedYN,
	ci.statusID AS statusID,
	statusColor,
	statusBackgroundColor,
	srp_erp_mfq_status.description AS statusDescription,
	ci.dueDate AS plannedDate,
	referenceNo,
	ci.approvedYN AS approvedYN,
	est.confirmedYN AS estConfirmedYN,
	IFNULL( mfqsegment.segmentcode, '-' ) AS segment,
	IFNULL( srp_erp_mfq_estimatemaster.confirmedYN, 0 ) AS estimateconf,
	empdetail.Ename2 AS proposalengineer,
	quotationStatus,
	IFNULL( srp_erp_mfq_estimatemaster.poNumber, '' ) AS poNumber,
	((
		discountedPrice * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) AS estimateValue,
	srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	CONCAT( srp_erp_currencymaster.CurrencyCode, ' : ' ) AS transactionCurrency,
	est.estimateCode AS estimateCode,
	srp_erp_mfq_estimatemaster.estimateMasterID AS estimateMasterID 
FROM
	`srp_erp_mfq_customerinquiry` `ci`
	LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `ci`.`mfqCustomerAutoID`
	LEFT JOIN `srp_erp_mfq_status` ON `srp_erp_mfq_status`.`statusID` = `ci`.`statusID`
	LEFT JOIN (
        SELECT
            srp_erp_mfq_estimatedetail.estimateMasterID,
            srp_erp_mfq_estimatemaster.approvedYN,
            srp_erp_mfq_estimatedetail.ciMasterID,
            confirmedYN,
            SUM( discountedPrice ) AS discountedPrice,
            srp_erp_mfq_estimatemaster.estimateCode 
        FROM
            srp_erp_mfq_estimatedetail
		INNER JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatemaster.estimateMasterID = srp_erp_mfq_estimatedetail.estimateMasterID 
        INNER JOIN ( SELECT MAX(versionLevel), versionOrginID, MAX(estimateMasterID) AS estimateMasterID FROM srp_erp_mfq_estimatemaster est2 GROUP BY versionOrginID ) maxl ON maxl.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID
        GROUP BY srp_erp_mfq_estimatedetail.estimateMasterID
	) est ON `est`.`ciMasterID` = `ci`.`ciMasterID`
	LEFT JOIN `srp_erp_mfq_segment` `mfqsegment` ON `mfqsegment`.`mfqSegmentID` = `ci`.`segmentID`
	LEFT JOIN `srp_erp_mfq_estimatemaster` ON `srp_erp_mfq_estimatemaster`.`ciMasterID` = `ci`.`ciMasterID`
	LEFT JOIN `srp_erp_currencymaster` ON `srp_erp_mfq_estimatemaster`.`transactionCurrencyID` = `srp_erp_currencymaster`.`currencyID`
	LEFT JOIN `srp_erp_mfq_job` ON `srp_erp_mfq_estimatemaster`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID` 
	AND `linkedJobID` IS NOT NULL 
	AND ( `srp_erp_mfq_job`.`isDeleted` = 0 OR `srp_erp_mfq_job`.`isDeleted` IS NULL )
    LEFT JOIN(
        SELECT estimateMasterID, expectedDeliveryDate, linkedJobID FROM srp_erp_mfq_job WHERE linkedJobID IS NULL
    )mfqjob ON srp_erp_mfq_estimatemaster.estimateMasterID = mfqjob.estimateMasterID

	LEFT JOIN `srp_erp_mfq_job` AS `jbMas` ON `jbMas`.`workProcessID` = `srp_erp_mfq_job`.`linkedJobID`
	LEFT JOIN `srp_erp_segment` `segment` ON `segment`.`segmentID` = `mfqsegment`.`segmentID`
	LEFT JOIN `srp_employeesdetails` `empdetail` ON `empdetail`.`EIdNo` = `ci`.`proposalEngineerID`
	LEFT JOIN (
        SELECT
            MAX( deliveryDate ) AS deliveryDate,
            linkedJobID 
        FROM
            srp_erp_mfq_deliverynote
            JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_deliverynote.deliverNoteID
            JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_deliverynotedetail.jobID 
        WHERE
            deletedYn != 1 
        GROUP BY
            linkedJobID 
	) DNDate ON `DNDate`.`linkedJobID` = `jbMas`.`workProcessID`
	LEFT JOIN (
        SELECT
            linkedJobID,
            MIN( CASE WHEN invoiceAutoID IS NOT NULL THEN 3 WHEN srp_erp_mfq_deliverynotedetail.deliveryNoteID IS NOT NULL THEN 2 ELSE 1 END ) AS jobStatus 
        FROM
            srp_erp_mfq_job
            LEFT JOIN srp_erp_mfq_deliverynotedetail ON srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID
            LEFT JOIN srp_erp_mfq_customerinvoicemaster ON srp_erp_mfq_customerinvoicemaster.deliveryNoteID = srp_erp_mfq_deliverynotedetail.deliveryNoteID 
        GROUP BY
            linkedJobID 
	) MainJobStatus ON `MainJobStatus`.`linkedJobID` = `jbMas`.`workProcessID` 
WHERE
	`ci`.`companyID` = {$companyID} {$where}
GROUP BY ci.ciMasterID 
ORDER BY ciMasterID DESC")->result_array();

        if($result) {
            $a = 1;
            foreach ($result AS $val) {
                $det['recordNo'] = $a;
                $det['ciCode'] = $val['ciCode'];
                $det['documentDate'] = $val['documentDate'];
                $det['CustomerName'] = $val['CustomerName'];
                $det['proposalengineer'] = $val['proposalengineer'];
                $det['segment'] = $val['segment'];
                $det['referenceNo'] = $val['referenceNo'];
                $det['actualSubmission'] = $val['deliveryDate'];
                $det['dueDate'] = $val['dueDate'];
                $det['expectedDeliveryDate'] = $val['expectedDeliveryDate'];
                $det['actualDeliveryDate'] = $val['actualDeliveryDate'];
                $det['awardedDate'] = $val['awardedDate'];
                $det['poNumber'] = $val['poNumber'];
                $det['documentCode'] = $val['documentCode'];
                $det['jobStatus'] = strip_tags(load_main_job_status($val['jobStatus']));
                $det['estimateCode'] = $val['estimateCode'];
                $det['transactionCurrency'] = $val['transactionCurrency'];
                $det['value'] = number_format($val['estimateValue'], $val['transactionCurrencyDecimalPlaces']);

                if ($val['statusID'] == 1) {
                    $det['statusID'] = 'Open';
                } else if ($val['statusID'] == 2) {
                    $det['statusID'] = 'Awarded';
                } else if ($val['statusID'] == 3) {
                    $det['statusID'] = 'Losy';
                }

                if ($val['quotationStatus'] == 0) {
                    $det['status'] = 'Open';
                } else if ($val['quotationStatus'] == 1) {
                    $det['status'] = 'Submitted';
                } else {
                    $det['status'] = 'Declined';
                }
                $a++;
                array_push($data, $det);
            }
        }

        return $data;
    }

    function upload_attachment_for_inquiry()
    {
        $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
        $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
        $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {
            $this->load->library('s3');
            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if(empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }

            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */
            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['documentSubID'] = trim($this->input->post('att_docType') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_documentattachments', $data);

            $companyID = current_companyID();
            $this->db->select('DISTINCT(srp_erp_customerinvoicemaster.invoiceAutoID) AS erpInvoiceAutoID, srp_erp_mfq_customerinvoicemaster.invoiceAutoID AS mfqInvoiceAutoID');
            $this->db->join('srp_erp_mfq_estimatemaster', 'srp_erp_mfq_estimatemaster.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID', 'LEFT');
            $this->db->join('srp_erp_mfq_job', 'srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID', 'LEFT');
            $this->db->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID', 'LEFT');
            $this->db->join('srp_erp_mfq_customerinvoicemaster', 'srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID', 'LEFT');
            $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.mfqInvoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID', 'LEFT');
            $this->db->where('srp_erp_mfq_customerinquiry.ciMasterID', trim($this->input->post('documentSystemCode') ?? ''));
            $this->db->where('srp_erp_mfq_customerinvoicemaster.confirmedYN', 1);
            $this->db->where('srp_erp_mfq_customerinquiry.companyID', $companyID);
            $invoiceIDs = $this->db->get('srp_erp_mfq_customerinquiry')->row_array();

            if($invoiceIDs) {
                if($invoiceIDs['erpInvoiceAutoID']) {
                    $data['documentID'] = 'CINV';
                    $data['documentSystemCode'] = $invoiceIDs['erpInvoiceAutoID'];
                    $data['documentSubID'] = $invoiceIDs['mfqInvoiceAutoID'];
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                }
                if($invoiceIDs['mfqInvoiceAutoID']) {
                    $data['documentID'] = 'MCINV_EST';
                    $data['documentSystemCode'] = trim($invoiceIDs['mfqInvoiceAutoID'] ?? '');
                    $data['documentSubID'] = trim($this->input->post('documentSystemCode') ?? '');
                    $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
                    $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
                    $data['fileType'] = trim($ext);
                    $data['fileSize'] = trim($file["size"]);
                    $data['timestamp'] = date('Y-m-d H:i:s');
                    $data['companyID'] = $companyID;
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                }
            }
            
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload);
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload);
            }
        }
    }


    public function savestage() {
        $stages = $this->input->post("stages");

        foreach ($stages as $stage) {
            
            $stagename=$stage['description'];
            $defaulttype = $stage['default_type'];
            $weightage= $stage['weightage'];

            $detail = [
                'stage_name'=>$stagename,
                'DefaultType' => $defaulttype,
                'weightage'=> $weightage
            ];

          $result=$this->db->query("INSERT INTO srp_erp_mfq_stage (stage_name, DefaultType,weightage) VALUES ('" . $detail['stage_name'] . "', '" . $detail['DefaultType'] . "', '" . $detail['weightage'] . "')");
        }


        if ($result) {
            echo 'Stages saved successfully';
        } else {
            echo 'Stages save failed';
        }
        
    }

    function saveweightage() {
        $companyid = current_companyID();
        $stageid = $this->input->post('stageid');
        $weightages = $this->input->post('weightages');       
        
        $success = true;
        foreach ($weightages as $stage) {
            $detail = [
                'stageID' => $stageid,
                'createdbyEMPID' => current_userID(),
                'CreatedDateTime' => current_date(true),
                'checklistDescription' => $stage,
                'companyID' => $companyid
            ];
    
            $result = $this->db->insert('srp_erp_mfq_stage_checklist', $detail);
        }
        
        if ($success) {
            echo 'Weight age saved successfully';
        } else {
            echo 'Weight age save failed';
        }
    }
    

}