<?php

class MFQ_Job_model extends ERP_Model
{
    function save_job_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $PBM_policy = (getPolicyValues('PBM', 'All')==1?getPolicyValues('PBM', 'All'):0);
        if($this->input->post('fromType') == 'EST'){
            $format_startdate =  date('Y-m-d');
            $format_enddate =  date('Y-m-d');
            $format_deliverydate =  date('Y-m-d');
        }else{
            
            $format_startdate = input_format_date(trim($this->input->post('startDate') ?? ''), $date_format_policy);
            $format_enddate = input_format_date(trim($this->input->post('endDate') ?? ''), $date_format_policy);
            $format_deliverydate = null;
            if(trim($this->input->post('deliveryDate') ?? '')) {
                $format_deliverydate = input_format_date(trim($this->input->post('deliveryDate') ?? ''), $date_format_policy);
            }
        }
       
        $fromType = $this->input->post('fromType');
        $jobType = $this->input->post('jobType');

        $flowserve = getPolicyValues('MANFL', 'All');

     
        if (!$this->input->post('workProcessID')) {

            if($flowserve =='FlowServe'){
                
                $mfqItemID_flow = $this->input->post('mfqItemID');

                $this->db->select('secondaryItemCode, IFNULL(serialNo, 0) AS serialNo');
                $this->db->from("srp_erp_mfq_itemmaster");
                $this->db->where("mfqItemID", $mfqItemID_flow );
                $this->db->where('companyID', current_companyID());
                $mfqItem = $this->db->get()->row_array();

                // $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_job', 'workProcessID', 'companyID');

                // $codes = $this->sequence->mfq_sequence_generator_flowserve('JOB', $mfqItem['serialNo'], $mfqItem['secondaryItemCode']);
                // $this->db->set('serialNo', ($mfqItem['serialNo']+1));
                // $this->db->set('documentCode', $codes);

                $this->db->select('segmentCode');
                $this->db->from("srp_erp_mfq_segment");
                $this->db->where("companyID", current_companyID());
                $this->db->where("mfqSegmentID", $this->input->post('mfqSegmentID'));
                $segmentCode = $this->db->get()->row('segmentCode');

                // $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_segment', 'mfqSegmentID', 'companyID', null);
                $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_job', 'workProcessID', 'companyID');
                $codes = $this->sequence->mfq_sequence_generator('JOB', $serialInfo['serialNo'], $segmentCode);

                $this->db->set('serialNo', $serialInfo['serialNo']);
                $this->db->set('documentCode', $codes);
                $this->db->set('jobType', $jobType);


            }else{
                $this->db->select('segmentCode');
                $this->db->from("srp_erp_mfq_segment");
                $this->db->where("companyID", current_companyID());
                $this->db->where("mfqSegmentID", $this->input->post('mfqSegmentID'));
                $segmentCode = $this->db->get()->row('segmentCode');

                $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_job', 'workProcessID', 'companyID');
                $codes = $this->sequence->mfq_sequence_generator('JOB', $serialInfo['serialNo'], $segmentCode );
                $this->db->set('serialNo', $serialInfo['serialNo']);
                $this->db->set('documentCode', $codes);
            }
            
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('workFlowTemplateID', $this->input->post('workFlowTemplateID'));
            $this->db->set('ownerID', $this->input->post('ownerID'));
            $this->db->set('documentDate', date('Y-m-d'));
            $this->db->set('startDate', $format_startdate);
            $this->db->set('endDate', $format_enddate);
            $this->db->set('expectedDeliveryDate', $format_deliverydate);
            if ($this->input->post('type') == 2) {
                $this->db->set('mfqItemID', $this->input->post('estMfqItemID'));
                $this->db->set('estimateMasterID', $this->input->post('estimateMasterID'));
                $this->db->set('estimateDetailID', $this->input->post('estimateDetailID'));
                $this->db->set('bomMasterID', $this->input->post('bomMasterID'));
            } else {
                $this->db->set('mfqItemID', $this->input->post('mfqItemID'));
            }
            $this->db->set('qty', $this->input->post('qty'));
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('mfqSegmentID', $this->input->post('mfqSegmentID'));
            $this->db->set('type', $this->input->post('type'));
            $this->db->set('mfqWarehouseAutoID', $this->input->post('mfqWarehouseAutoID'));
            $this->db->set('documentID', 'JOB');

            $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
            $this->db->set('transactionExchangeRate', 1);
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
            $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
            $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
            $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
            $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
            $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
            $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

            $this->db->select("srp_erp_customermaster.customerCurrencyID,srp_erp_customermaster.customerCurrency");
            $this->db->from("srp_erp_mfq_customermaster");
            $this->db->join("srp_erp_customermaster", "srp_erp_mfq_customermaster.CustomerAutoID=srp_erp_customermaster.customerAutoID", "LEFT");
            $this->db->where("mfqCustomerAutoID", $this->input->post('mfqCustomerAutoID'));
            $custInfo = $this->db->get()->row_array();

            $this->db->set('mfqCustomerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('mfqCustomerCurrency', $custInfo["customerCurrency"]);

            $customer_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $custInfo['customerCurrencyID']);
            $this->db->set('mfqCustomerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('mfqCustomerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);
            $this->db->set('isSaved', 1);

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_job');
            $last_id = $this->db->insert_id();
            if($flowserve =='FlowServe'){
            ////////////////////////////////////////////////////////start/////////////////////////////////////////////////////
            $EngineeringDeadLine = input_format_date(trim($this->input->post('EngineeringDeadLine') ?? ''), $date_format_policy);
            $purchasingDeadLine = input_format_date(trim($this->input->post('purchasingDeadLine') ?? ''), $date_format_policy);
            $DeadLineproduction = input_format_date(trim($this->input->post('DeadLineproduction') ?? ''), $date_format_policy);
            $DeadLineqaqc = input_format_date(trim($this->input->post('DeadLineqaqc') ?? ''), $date_format_policy);

            $submissiondatDeadLineengineering = input_format_date(trim($this->input->post('submissiondatDeadLine') ?? ''), $date_format_policy);
            $submissiondatPurchasing = input_format_date(trim($this->input->post('submissiondatDeadLinepurchasing') ?? ''), $date_format_policy);
            $submissiondatDeadLineproduction = input_format_date(trim($this->input->post('submissiondatDeadLineproduction') ?? ''), $date_format_policy);
            $submissiondatqaqc = input_format_date(trim($this->input->post('submissiondateqaqcDeadLinepurchasing') ?? ''), $date_format_policy);

            $delayInDays=$this->input->post('noofdays');
            $noofdaysproduction=$this->input->post('noofdaysproduction');
            $noofdayspurchasing=$this->input->post('noofdayspurchasing');
            $noofdaysqaqc=$this->input->post('noofdaysqaqc');

            $engineeringemployee=$this->input->post('engineeringemployee');
            $purchasingemployee=$this->input->post('purchasingemployee');
            $productionemployee=$this->input->post('productionemployee');
            $qaqcemployee=$this->input->post('qaqcemployee');

            if ($this->input->post('DeadLineqaqc') != '' && $this->input->post('submissiondateqaqcDeadLinepurchasing')!='') {

                $data_person_qc['documentID']='JOB';
                $data_person_qc['documentmasterAutoID']=$last_id;
                $data_person_qc['responsibleType']=4;
                $data_person_qc['empID']=$qaqcemployee;
                $data_person_qc['requiredDate']=$DeadLineqaqc;
                $data_person_qc['submissionDate']=$submissiondatqaqc;
                $data_person_qc['delays']=$noofdaysqaqc;
                $data_person_qc['createdUserGroup'] = $this->common_data['user_group'];
                $data_person_qc['createdPCID'] = $this->common_data['current_pc'];
                $data_person_qc['createdUserID'] = $this->common_data['current_userID'];
                $data_person_qc['createdUserName'] = $this->common_data['current_user'];
                $data_person_qc['createdDateTime'] = $this->common_data['current_date'];
                $data_person_qc['modifiedPCID'] = $this->common_data['current_pc'];
                $data_person_qc['modifiedUserID'] = $this->common_data['current_userID'];
                $data_person_qc['modifiedUserName'] = $this->common_data['current_user'];
                $data_person_qc['modifiedDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_person_responsible', $data_person_qc);
            }

            if ($this->input->post('DeadLineproduction') != '' && $this->input->post('submissiondatDeadLineproduction')!='') {

                $data_person_prod['documentID']='JOB';
                $data_person_prod['documentmasterAutoID']=$last_id;
                $data_person_prod['responsibleType']=3;
                $data_person_prod['empID']=$productionemployee;
                $data_person_prod['requiredDate']=$DeadLineproduction;
                $data_person_prod['submissionDate']=$submissiondatDeadLineproduction;
                $data_person_prod['delays']=$noofdaysproduction;
                $data_person_prod['createdUserGroup'] = $this->common_data['user_group'];
                $data_person_prod['createdPCID'] = $this->common_data['current_pc'];
                $data_person_prod['createdUserID'] = $this->common_data['current_userID'];
                $data_person_prod['createdUserName'] = $this->common_data['current_user'];
                $data_person_prod['createdDateTime'] = $this->common_data['current_date'];
                $data_person_prod['modifiedPCID'] = $this->common_data['current_pc'];
                $data_person_prod['modifiedUserID'] = $this->common_data['current_userID'];
                $data_person_prod['modifiedUserName'] = $this->common_data['current_user'];
                $data_person_prod['modifiedDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_person_responsible', $data_person_prod);
            }

            if ($this->input->post('purchasingDeadLine') != '' && $this->input->post('submissiondatDeadLinepurchasing')!='') {

                $data_person_pr['documentID']='JOB';
                $data_person_pr['documentmasterAutoID']=$last_id;
                $data_person_pr['responsibleType']=2;
                $data_person_pr['empID']=$purchasingemployee;
                $data_person_pr['requiredDate']=$purchasingDeadLine;
                $data_person_pr['submissionDate']=$submissiondatPurchasing;
                $data_person_pr['delays']=$noofdayspurchasing;
                $data_person_pr['createdUserGroup'] = $this->common_data['user_group'];
                $data_person_pr['createdPCID'] = $this->common_data['current_pc'];
                $data_person_pr['createdUserID'] = $this->common_data['current_userID'];
                $data_person_pr['createdUserName'] = $this->common_data['current_user'];
                $data_person_pr['createdDateTime'] = $this->common_data['current_date'];
                $data_person_pr['modifiedPCID'] = $this->common_data['current_pc'];
                $data_person_pr['modifiedUserID'] = $this->common_data['current_userID'];
                $data_person_pr['modifiedUserName'] = $this->common_data['current_user'];
                $data_person_pr['modifiedDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_person_responsible', $data_person_pr);
            }
    
            if ($this->input->post('submissiondatDeadLine') != '' && $this->input->post('EngineeringDeadLine')!='') {

                    $data_person['documentID']='JOB';
                    $data_person['documentmasterAutoID']=$last_id;
                    $data_person['responsibleType']=1;
                    $data_person['empID']=$engineeringemployee;
                    $data_person['requiredDate']=$EngineeringDeadLine;
                    $data_person['submissionDate']=$submissiondatDeadLineengineering;
                    $data_person['delays']=$delayInDays;
                    $data_person['createdUserGroup'] = $this->common_data['user_group'];
                    $data_person['createdPCID'] = $this->common_data['current_pc'];
                    $data_person['createdUserID'] = $this->common_data['current_userID'];
                    $data_person['createdUserName'] = $this->common_data['current_user'];
                    $data_person['createdDateTime'] = $this->common_data['current_date'];
                    $data_person['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_person['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_person['modifiedUserName'] = $this->common_data['current_user'];
                    $data_person['modifiedDateTime'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_mfq_person_responsible', $data_person);
                }
               

               ////////////////////////////////////////////////////////end/////////////////////////////////////////////////////
            }
            $workFlowID = "";
            $workFlowTemplateID = "";
            $description = "";
            $linkWorkflow = "";
            if (isset($_POST["customWorkFlowID"])) {
                $workFlowID = explode(',', $this->input->post("customWorkFlowID"));
                $workFlowTemplateID = explode(',', $this->input->post("customWorkFlowTemplateID"));
                $description = explode(',', $this->input->post("customDescription"));
                $linkWorkflow = $this->input->post("linkProcess");
            }

            $createdJobArr = array();
            $data = array();
            $templateDet = "";
            if ($workFlowID) {
                foreach ($workFlowID as $key => $val) {
                    $data[] = array('jobID' => $last_id, 'workFlowID' => $val, 'workFlowTemplateID' => $workFlowTemplateID[$key], 'sortOrder' => $key + 1, 'description' => $description[$key], 'companyID' => current_companyID(), 'templateMasterID' => $this->input->post('workFlowTemplateID'));
                }
                $this->db->insert_batch("srp_erp_mfq_customtemplatedetail", $data);

                if ($linkWorkflow) {
                    $this->db->query("UPDATE srp_erp_mfq_customtemplatedetail AS cust
                        LEFT JOIN (
                                SELECT prev_id, cur_id 
                                FROM (
                                        SELECT templateDetailID AS cur_id,
                                            ( SELECT min( templateDetailID ) FROM srp_erp_mfq_customtemplatedetail WHERE templateDetailID = (cur_id-1) AND jobID = {$last_id} ) AS prev_id 
                                        FROM srp_erp_mfq_customtemplatedetail 
                                        WHERE
                                            jobID = {$last_id} 
                                    ) AS tmp 
                                WHERE prev_id IS NOT NULL
                        ) target ON target.cur_id = cust.templateDetailID SET cust.linkWorkFlow = target.prev_id WHERE cust.jobID = {$last_id}");
                }

                $this->db->select("*");
                $this->db->from("srp_erp_mfq_customtemplatedetail");
                $this->db->where("templateMasterID", $this->input->post('workFlowTemplateID'));
                $this->db->where("jobID", $last_id);
                $templateDet = $this->db->get()->result_array();
                $data = array();
                if ($templateDet) {
                    foreach ($templateDet as $val) {
                        $data[] = array('workFlowID' => $val["workFlowID"], 'templateDetailID' => $val["templateDetailID"], 'jobID' => $last_id, 'companyID' => current_companyID());
                    }
                    $this->db->insert_batch("srp_erp_mfq_workflowstatus", $data);
                }
            } else {
                $this->db->select("*");
                $this->db->from("srp_erp_mfq_templatedetail");
                $this->db->where("templateMasterID", $this->input->post('workFlowTemplateID'));
                $templateDet = $this->db->get()->result_array();
                $data = array();
                if ($templateDet) {
                    foreach ($templateDet as $val) {
                        $data[] = array('workFlowID' => $val["workFlowID"], 'templateDetailID' => $val["templateDetailID"], 'jobID' => $last_id, 'companyID' => current_companyID());
                    }
                    $this->db->insert_batch("srp_erp_mfq_workflowstatus", $data);
                }
            }
            $warehouse_ID = 0;
            if ($fromType == "EST") {
                if ($this->input->post('bomMasterID')) {
                    $this->db->select("*");
                    $this->db->from("srp_erp_mfq_warehousemaster");
                    $this->db->where("mfqWarehouseAutoID", $this->input->post('mfqWarehouseAutoID'));
                    $warehouse = $this->db->get()->row_array();

                    $this->db->select("*");
                    $this->db->from("srp_erp_warehousemaster");
                    $this->db->where("warehouseAutoID", $warehouse["warehouseAutoID"]);
                    $warehouseERP = $this->db->get()->row_array();
                    $warehouse_ID = $warehouseERP['wareHouseAutoID'];

                    $this->db->query("INSERT INTO srp_erp_warehouseitems (wareHouseAutoID,wareHouseLocation,wareHouseDescription,itemAutoID,itemSystemCode,itemDescription,unitOfMeasureID,unitOfMeasure,currentStock,companyID,companyCode) SELECT " . $warehouseERP["wareHouseAutoID"] . ",'" . $warehouseERP["wareHouseLocation"] . "','" . $warehouseERP["wareHouseDescription"] . "',srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_itemmaster.defaultUnitOfMeasure,0,srp_erp_itemmaster.companyID,srp_erp_itemmaster.companyCode FROM srp_erp_mfq_bom_materialconsumption mc INNER JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = mc.mfqItemID INNER JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_itemmaster.itemAutoID WHERE NOT EXISTS (SELECT * FROM srp_erp_warehouseitems WHERE srp_erp_warehouseitems.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID AND warehouseAutoID = " . $warehouse["warehouseAutoID"] . ") AND srp_erp_mfq_itemmaster.itemType = 3 AND mc.bomMasterID =" . $this->input->post('bomMasterID'));

                    $this->db->select("srp_erp_mfq_bom_materialconsumption.*,srp_erp_mfq_itemmaster.*,(((IFNULL(srp_erp_warehouseitems.currentStock,0)- IFNULL(jcm.qtyUsed,0)) + IFNULL(jc.qty,0))-(srp_erp_mfq_bom_materialconsumption.qtyUsed* {$this->input->post('qty')})) as remainingQty,srp_erp_mfq_billofmaterial.bomMasterID as bomID");
                    $this->db->from("srp_erp_mfq_bom_materialconsumption");
                    $this->db->join("srp_erp_mfq_itemmaster", "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
                    $this->db->join("srp_erp_warehouseitems", "srp_erp_warehouseitems.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID AND srp_erp_warehouseitems.companyID = " . current_companyID() . " AND srp_erp_warehouseitems.warehouseAutoID =" . $warehouse_ID, "left");
                    $this->db->join("srp_erp_mfq_billofmaterial", "srp_erp_mfq_billofmaterial.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
                    $this->db->join("(SELECT SUM(qtyUsed) as qtyUsed,srp_erp_mfq_jc_materialconsumption.mfqItemID FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_jc_materialconsumption.workProcessID WHERE approvedYN = 0 AND srp_erp_mfq_jc_materialconsumption.companyID = " . current_companyID() . " GROUP BY srp_erp_mfq_jc_materialconsumption.mfqItemID) jcm", "jcm.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
                    $this->db->join("(SELECT SUM(qty) as qty,mfqItemID FROM srp_erp_mfq_job WHERE mfqWarehouseAutoID = " . $this->input->post('mfqWarehouseAutoID') . " AND approvedYN = 0 AND companyID = " . current_companyID() . " GROUP BY mfqItemID) jc", "jc.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");

                    $this->db->where("srp_erp_mfq_bom_materialconsumption.bomMasterID", $this->input->post('bomMasterID'));
                    $this->db->where("itemType", 3);
                    $this->db->where("srp_erp_mfq_itemmaster.mainCategory", "Inventory");
                    $this->db->having("remainingQty < ", 0);
                    $this->db->get()->result_array();
                }
            }
            
            $data = array();
            if ($this->input->post('estimateDetailID')) {
                $data = $this->db->query('SELECT estimateCode,ciCode FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatedetail.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_customerinquiry.ciMasterID = srp_erp_mfq_estimatedetail.ciMasterID WHERE srp_erp_mfq_estimatedetail.estimateDetailID=' . $this->input->post('estimateDetailID'))->row_array();
            } else {
                $data["estimateCode"] = "";
                $data["ciCode"] = "";
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job saved Failed ' . $this->db->_error_message());

            } else {
                if($flowserve =='FlowServe'){

                    $data_fa_mfq['serialNo'] = $mfqItem['serialNo']+1;
                    $this->db->where('mfqItemID', $this->input->post('mfqItemID'));
                    $this->db->where('companyID', current_companyID());
                    $res =$this->db->update('srp_erp_mfq_itemmaster', $data_fa_mfq);
                    
                }
                $this->db->trans_commit();
                return array('s', 'Job saved Successfully.', $last_id, $codes, $data["estimateCode"], $data["ciCode"], $createdJobArr);
            }
        } else {
            
            $mfqItemID = "";
            if ($this->input->post('type') == 2) {
                $mfqItemID = $this->input->post('estMfqItemID');
            } else {
                $mfqItemID = $this->input->post('mfqItemID');
            }
            $header = $this->load_job_header();
           
            $itemDetail = get_specific_mfq_item($this->input->post('mfqItemID'));
            $qtyExceed = 0;
            $expectedQty = 0;
            $balanceQty = 0;

            if( $this->input->post('type')!=1 && $PBM_policy == 0){ 
                $expectedQty = $this->db->query('SELECT expectedQty FROM srp_erp_mfq_estimatedetail WHERE estimateDetailID=' . $this->input->post('estimateDetailID'))->row('expectedQty');
                $qtyExceed = $this->db->query('SELECT SUM(qty) as qty FROM srp_erp_mfq_job WHERE mfqItemID = ' . $mfqItemID . ' AND workProcessID != ' . $this->input->post('workProcessID') . ' AND linkedJobID=' . $header['linkedJobID'])->row('qty');
                $balanceQty = $expectedQty - $qtyExceed;
            }
            if ($PBM_policy == 0 && $itemDetail["mainCategory"] == "Inventory" && (($qtyExceed + $this->input->post('qty')) > $expectedQty) && $header["levelNo"] != 3 &&  $this->input->post('type')!=1) {
                return array('w', 'You cannot create more than ' . $balanceQty . ' quantity');
            } else {
                $data['description'] = $this->input->post('description');
                $data['workFlowTemplateID'] = $this->input->post('workFlowTemplateID');
                $data['description'] = $this->input->post('description');
                $data['startDate'] = $format_startdate;
                $data['endDate'] = $format_enddate;
                $data['expectedDeliveryDate'] = $format_deliverydate;
               
                $data['mfqItemID'] = $this->input->post('mfqItemID');
                if ($this->input->post('type') == 2) {
                    $data['mfqItemID'] = $this->input->post('estMfqItemID');
                    $data['estimateDetailID'] = $this->input->post('estimateDetailID');
                    $data['bomMasterID'] = $this->input->post('bomMasterID');
                } else {
                    $data['mfqItemID'] = $this->input->post('mfqItemID');
                    $data['estimateDetailID'] = null;
                    $data['bomMasterID'] = null;
                }
                $data['qty'] = $this->input->post('qty');
                $data['mfqCustomerAutoID'] = $this->input->post('mfqCustomerAutoID');
                $data['mfqSegmentID'] = $this->input->post('mfqSegmentID');
                $data['type'] = $this->input->post('type');
                $data['mfqWarehouseAutoID'] = $this->input->post('mfqWarehouseAutoID');
                $data['companyID'] = current_companyID();
                $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                $data['modifiedUserID'] = current_userID();//$this->session->userdata("username");
                $data['modifiedUserName'] = current_user();//$this->session->userdata("username");
                $data['modifiedDateTime'] = current_date(true);

                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $result = $this->db->update('srp_erp_mfq_job', $data);
                $last_id = $this->input->post('workProcessID');
                if($flowserve =='FlowServe'){
                    ////////////////////////////////////////////////////////start/////////////////////////////////////////////////////
                    $EngineeringDeadLine = input_format_date(trim($this->input->post('EngineeringDeadLine') ?? ''), $date_format_policy);
                    $purchasingDeadLine = input_format_date(trim($this->input->post('purchasingDeadLine') ?? ''), $date_format_policy);
                    $DeadLineproduction = input_format_date(trim($this->input->post('DeadLineproduction') ?? ''), $date_format_policy);
                    $DeadLineqaqc = input_format_date(trim($this->input->post('DeadLineqaqc') ?? ''), $date_format_policy);
        
                    $submissiondatDeadLineengineering = input_format_date(trim($this->input->post('submissiondatDeadLine') ?? ''), $date_format_policy);
                    $submissiondatPurchasing = input_format_date(trim($this->input->post('submissiondatDeadLinepurchasing') ?? ''), $date_format_policy);
                    $submissiondatDeadLineproduction = input_format_date(trim($this->input->post('submissiondatDeadLineproduction') ?? ''), $date_format_policy);
                    $submissiondatqaqc = input_format_date(trim($this->input->post('submissiondateqaqcDeadLinepurchasing') ?? ''), $date_format_policy);
        
                    $delayInDays=$this->input->post('noofdays');
                       $noofdaysproduction=$this->input->post('noofdaysproduction');
                       $noofdayspurchasing=$this->input->post('noofdayspurchasing');
                       $noofdaysqaqc=$this->input->post('noofdaysqaqc');
        
                       $engineeringemployee=$this->input->post('engineeringemployee');
                       $purchasingemployee=$this->input->post('purchasingemployee');
                       $productionemployee=$this->input->post('productionemployee');
                       $qaqcemployee=$this->input->post('qaqcemployee');
        
                    if ($this->input->post('DeadLineqaqc') != '' && $this->input->post('submissiondateqaqcDeadLinepurchasing')!='') {

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 4);
                        $this->db->where('empID', $qaqcemployee);
                        $this->db->where('requiredDate', $DeadLineqaqc);
                        $this->db->where('submissionDate', $submissiondatqaqc);
                        $data_in_table = $this->db->get('srp_erp_mfq_person_responsible')->row_array();

                        if($data_in_table){

                        }else{

                            //add old record for history
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 4);
                        $this->db->order_by('createdDateTime', 'desc');
                        $hit_history = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($hit_history)>0){

                            $data_history['documentID'] = 'JOB';
                            $data_history['documentMasterID'] = $this->input->post('workProcessID');
                            $data_history['documentDetailID'] = 4;
                           
                            $data_history['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_history['createdUserGroup'] = $this->common_data['user_group'];
                            $data_history['createdPCID'] = $this->common_data['current_pc'];
                            $data_history['createdUserID'] = $this->common_data['current_userID'];
                            $data_history['createdUserName'] = $this->common_data['current_user'];
                            $data_history['createdDateTime'] = $this->common_data['current_date'];
                            $data_history['modifiedPCID'] = $this->common_data['current_pc'];
                            $data_history['modifiedUserID'] = $this->common_data['current_userID'];
                            $data_history['modifiedUserName'] = $this->common_data['current_user'];
                            $data_history['modifiedDateTime'] = $this->common_data['current_date'];

                            if ($hit_history[0]['empID'] != $qaqcemployee) {
                                $data_history['fieldName'] = 'QAQCResponsibleEmpID';
                                $data_history['changeDescription'] = 'QA/QC Responsible Person Changed';
                                $data_history['previousValue'] = $hit_history[0]['empID'];
                                $data_history['value'] = $qaqcemployee;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                            }

                            if ($hit_history[0]['requiredDate'] != $DeadLineqaqc) {
                                $data_history['fieldName'] = 'QAQCEndDate';
                                $data_history['changeDescription'] = 'QA/QC	End Date Changed';
                                $data_history['previousValue'] = $hit_history[0]['requiredDate'];
                                $data_history['value'] = $qaqcemployee;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                            }

                            if ($hit_history[0]['submissionDate'] != $submissiondatqaqc) {
                                $data_history['fieldName'] = 'QAQCSubmissionDate';
                                $data_history['changeDescription'] = 'QA/QC Submission Date Changed';
                                $data_history['previousValue'] = $hit_history[0]['submissionDate'];
                                $data_history['value'] = $submissiondatqaqc;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history);
                            }

                        }

                        $data_person_qc['documentID']='JOB';
                        $data_person_qc['documentmasterAutoID']=$last_id;
                        $data_person_qc['responsibleType']=4;
                        $data_person_qc['empID']=$qaqcemployee;
                        $data_person_qc['requiredDate']=$DeadLineqaqc;
                        $data_person_qc['submissionDate']=$submissiondatqaqc;
                        $data_person_qc['delays']=$noofdaysqaqc;
                        $data_person_qc['createdUserGroup'] = $this->common_data['user_group'];
                        $data_person_qc['createdPCID'] = $this->common_data['current_pc'];
                        $data_person_qc['createdUserID'] = $this->common_data['current_userID'];
                        $data_person_qc['createdUserName'] = $this->common_data['current_user'];
                        $data_person_qc['createdDateTime'] = $this->common_data['current_date'];
                        $data_person_qc['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_person_qc['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_person_qc['modifiedUserName'] = $this->common_data['current_user'];
                        $data_person_qc['modifiedDateTime'] = $this->common_data['current_date'];
        
                        $this->db->insert('srp_erp_mfq_person_responsible', $data_person_qc);

                        }
                    }
        
                    if ($this->input->post('DeadLineproduction') != '' && $this->input->post('submissiondatDeadLineproduction')!='') {

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 3);
                        $this->db->where('empID', $productionemployee);
                        $this->db->where('requiredDate', $DeadLineproduction);
                        $this->db->where('submissionDate', $submissiondatDeadLineproduction);
                        $data_in_table = $this->db->get('srp_erp_mfq_person_responsible')->row_array();

                        if($data_in_table){

                        }else{

                            //add old record for history
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 3);
                        $this->db->order_by('createdDateTime', 'desc');
                        $hit_history = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($hit_history)>0){

                            $data_history_pro['documentID'] = 'JOB';
                            $data_history_pro['documentMasterID'] = $this->input->post('workProcessID');
                            $data_history_pro['documentDetailID'] = 3;
                           
                            $data_history_pro['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_history_pro['createdUserGroup'] = $this->common_data['user_group'];
                            $data_history_pro['createdPCID'] = $this->common_data['current_pc'];
                            $data_history_pro['createdUserID'] = $this->common_data['current_userID'];
                            $data_history_pro['createdUserName'] = $this->common_data['current_user'];
                            $data_history_pro['createdDateTime'] = $this->common_data['current_date'];
                            $data_history_pro['modifiedPCID'] = $this->common_data['current_pc'];
                            $data_history_pro['modifiedUserID'] = $this->common_data['current_userID'];
                            $data_history_pro['modifiedUserName'] = $this->common_data['current_user'];
                            $data_history_pro['modifiedDateTime'] = $this->common_data['current_date'];

                            if ($hit_history[0]['empID'] != $productionemployee) {
                                $data_history_pro['fieldName'] = 'productionResponsibleEmpID';
                                $data_history_pro['changeDescription'] = 'Production Responsible Person Changed';
                                $data_history_pro['previousValue'] = $hit_history[0]['empID'];
                                $data_history_pro['value'] = $productionemployee;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pro);
                            }

                            if ($hit_history[0]['requiredDate'] != $DeadLineproduction) {
                                $data_history_pro['fieldName'] = 'productionEndDate';
                                $data_history_pro['changeDescription'] = 'Production	End Date Changed';
                                $data_history_pro['previousValue'] = $hit_history[0]['requiredDate'];
                                $data_history_pro['value'] = $DeadLineproduction;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pro);
                            }

                            if ($hit_history[0]['submissionDate'] != $submissiondatDeadLineproduction) {
                                $data_history_pro['fieldName'] = 'productionSubmissionDate';
                                $data_history_pro['changeDescription'] = 'Production Submission Date Changed';
                                $data_history_pro['previousValue'] = $hit_history[0]['submissionDate'];
                                $data_history_pro['value'] = $submissiondatDeadLineproduction;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pro);
                            }

                        }
        
                        $data_person_prod['documentID']='JOB';
                        $data_person_prod['documentmasterAutoID']=$last_id;
                        $data_person_prod['responsibleType']=3;
                        $data_person_prod['empID']=$productionemployee;
                        $data_person_prod['requiredDate']=$DeadLineproduction;
                        $data_person_prod['submissionDate']=$submissiondatDeadLineproduction;
                        $data_person_prod['delays']=$noofdaysproduction;
                        $data_person_prod['createdUserGroup'] = $this->common_data['user_group'];
                        $data_person_prod['createdPCID'] = $this->common_data['current_pc'];
                        $data_person_prod['createdUserID'] = $this->common_data['current_userID'];
                        $data_person_prod['createdUserName'] = $this->common_data['current_user'];
                        $data_person_prod['createdDateTime'] = $this->common_data['current_date'];
                        $data_person_prod['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_person_prod['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_person_prod['modifiedUserName'] = $this->common_data['current_user'];
                        $data_person_prod['modifiedDateTime'] = $this->common_data['current_date'];
        
                        $this->db->insert('srp_erp_mfq_person_responsible', $data_person_prod);
                    }
                    }
        
                    if ($this->input->post('purchasingDeadLine') != '' && $this->input->post('submissiondatDeadLinepurchasing')!='') {
        
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 2);
                        $this->db->where('empID', $purchasingemployee);
                        $this->db->where('requiredDate', $purchasingDeadLine);
                        $this->db->where('submissionDate', $submissiondatPurchasing);
                        $data_in_table = $this->db->get('srp_erp_mfq_person_responsible')->row_array();

                        if($data_in_table){

                        }else{

                            //add old record for history
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 2);
                        $this->db->order_by('createdDateTime', 'desc');
                        $hit_history = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($hit_history)>0){

                            $data_history_pur['documentID'] = 'JOB';
                            $data_history_pur['documentMasterID'] = $this->input->post('workProcessID');
                            $data_history_pur['documentDetailID'] = 2;
                           
                            $data_history_pur['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_history_pur['createdUserGroup'] = $this->common_data['user_group'];
                            $data_history_pur['createdPCID'] = $this->common_data['current_pc'];
                            $data_history_pur['createdUserID'] = $this->common_data['current_userID'];
                            $data_history_pur['createdUserName'] = $this->common_data['current_user'];
                            $data_history_pur['createdDateTime'] = $this->common_data['current_date'];
                            $data_history_pur['modifiedPCID'] = $this->common_data['current_pc'];
                            $data_history_pur['modifiedUserID'] = $this->common_data['current_userID'];
                            $data_history_pur['modifiedUserName'] = $this->common_data['current_user'];
                            $data_history_pur['modifiedDateTime'] = $this->common_data['current_date'];

                            if ($hit_history[0]['empID'] != $purchasingemployee) {
                                $data_history_pur['fieldName'] = 'purchasingResponsibleEmpID';
                                $data_history_pur['changeDescription'] = 'Purchasing Responsible Person Changed';
                                $data_history_pur['previousValue'] = $hit_history[0]['empID'];
                                $data_history_pur['value'] = $purchasingemployee;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pur);
                            }

                            if ($hit_history[0]['requiredDate'] != $purchasingDeadLine) {
                                $data_history_pur['fieldName'] = 'purchasingEndDate';
                                $data_history_pur['changeDescription'] = 'Purchasing	End Date Changed';
                                $data_history_pur['previousValue'] = $hit_history[0]['requiredDate'];
                                $data_history_pur['value'] = $purchasingDeadLine;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pur);
                            }

                            if ($hit_history[0]['submissionDate'] != $submissiondatPurchasing) {
                                $data_history_pur['fieldName'] = 'purchasingSubmissionDate';
                                $data_history_pur['changeDescription'] = 'Purchasing Submission Date Changed';
                                $data_history_pur['previousValue'] = $hit_history[0]['submissionDate'];
                                $data_history_pur['value'] = $submissiondatPurchasing;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_pur);
                            }

                        }
                        $data_person_pr['documentID']='JOB';
                        $data_person_pr['documentmasterAutoID']=$last_id;
                        $data_person_pr['responsibleType']=2;
                        $data_person_pr['empID']=$purchasingemployee;
                        $data_person_pr['requiredDate']=$purchasingDeadLine;
                        $data_person_pr['submissionDate']=$submissiondatPurchasing;
                        $data_person_pr['delays']=$noofdayspurchasing;
                        $data_person_pr['createdUserGroup'] = $this->common_data['user_group'];
                        $data_person_pr['createdPCID'] = $this->common_data['current_pc'];
                        $data_person_pr['createdUserID'] = $this->common_data['current_userID'];
                        $data_person_pr['createdUserName'] = $this->common_data['current_user'];
                        $data_person_pr['createdDateTime'] = $this->common_data['current_date'];
                        $data_person_pr['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_person_pr['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_person_pr['modifiedUserName'] = $this->common_data['current_user'];
                        $data_person_pr['modifiedDateTime'] = $this->common_data['current_date'];
        
                        $this->db->insert('srp_erp_mfq_person_responsible', $data_person_pr);
                    }
                    }
            
                    if ($this->input->post('submissiondatDeadLine') != '' && $this->input->post('EngineeringDeadLine')!='') {
        
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 1);
                        $this->db->where('empID', $engineeringemployee);
                        $this->db->where('requiredDate', $EngineeringDeadLine);
                        $this->db->where('submissionDate', $submissiondatDeadLineengineering);
                        $data_in_table = $this->db->get('srp_erp_mfq_person_responsible')->row_array();

                        if($data_in_table){

                        }else{

                            //add old record for history
                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 1);
                        $this->db->order_by('createdDateTime', 'desc');
                        $hit_history = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($hit_history)>0){

                            $data_history_eng['documentID'] = 'JOB';
                            $data_history_eng['documentMasterID'] = $this->input->post('workProcessID');
                            $data_history_eng['documentDetailID'] = 1;
                           
                            $data_history_eng['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_history_eng['createdUserGroup'] = $this->common_data['user_group'];
                            $data_history_eng['createdPCID'] = $this->common_data['current_pc'];
                            $data_history_eng['createdUserID'] = $this->common_data['current_userID'];
                            $data_history_eng['createdUserName'] = $this->common_data['current_user'];
                            $data_history_eng['createdDateTime'] = $this->common_data['current_date'];
                            $data_history_eng['modifiedPCID'] = $this->common_data['current_pc'];
                            $data_history_eng['modifiedUserID'] = $this->common_data['current_userID'];
                            $data_history_eng['modifiedUserName'] = $this->common_data['current_user'];
                            $data_history_eng['modifiedDateTime'] = $this->common_data['current_date'];

                            if ($hit_history[0]['empID'] != $engineeringemployee) {
                                $data_history_eng['fieldName'] = 'engineeringResponsibleEmpID';
                                $data_history_eng['changeDescription'] = 'Engineering Responsible Person Changed';
                                $data_history_eng['previousValue'] = $hit_history[0]['empID'];
                                $data_history_eng['value'] = $engineeringemployee;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_eng);
                            }

                            if ($hit_history[0]['requiredDate'] != $EngineeringDeadLine) {
                                $data_history_eng['fieldName'] = 'engineeringEndDate';
                                $data_history_eng['changeDescription'] = 'Engineering	End Date Changed';
                                $data_history_eng['previousValue'] = $hit_history[0]['requiredDate'];
                                $data_history_eng['value'] = $EngineeringDeadLine;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_eng);
                            }

                            if ($hit_history[0]['submissionDate'] != $submissiondatDeadLineengineering) {
                                $data_history_eng['fieldName'] = 'engineeringSubmissionDate';
                                $data_history_eng['changeDescription'] = 'Engineering Submission Date Changed';
                                $data_history_eng['previousValue'] = $hit_history[0]['submissionDate'];
                                $data_history_eng['value'] = $submissiondatDeadLineengineering;

                                $this->db->insert('srp_erp_mfq_changehistory', $data_history_eng);
                            }

                        }
                            $data_person['documentID']='JOB';
                            $data_person['documentmasterAutoID']=$last_id;
                            $data_person['responsibleType']=1;
                            $data_person['empID']=$engineeringemployee;
                            $data_person['requiredDate']=$EngineeringDeadLine;
                            $data_person['submissionDate']=$submissiondatDeadLineengineering;
                            $data_person['delays']=$delayInDays;
                            $data_person['createdUserGroup'] = $this->common_data['user_group'];
                            $data_person['createdPCID'] = $this->common_data['current_pc'];
                            $data_person['createdUserID'] = $this->common_data['current_userID'];
                            $data_person['createdUserName'] = $this->common_data['current_user'];
                            $data_person['createdDateTime'] = $this->common_data['current_date'];
                            $data_person['modifiedPCID'] = $this->common_data['current_pc'];
                            $data_person['modifiedUserID'] = $this->common_data['current_userID'];
                            $data_person['modifiedUserName'] = $this->common_data['current_user'];
                            $data_person['modifiedDateTime'] = $this->common_data['current_date'];
        
                            $this->db->insert('srp_erp_mfq_person_responsible', $data_person);
                          }
                        }
                       
        
                       ////////////////////////////////////////////////////////end/////////////////////////////////////////////////////
                }

                $createdJobArr = array();
                $master = $this->db->query('SELECT * FROM srp_erp_mfq_job WHERE workProcessID=' . $this->input->post('workProcessID'))->row_array();
               
                if (!$master["isSaved"]) {
                    $data = [];
                    $data['isSaved'] = 1;
                    $this->db->where('workProcessID', $this->input->post('workProcessID'));
                    $result = $this->db->update('srp_erp_mfq_job', $data);

                    $workFlowID = "";
                    $workFlowTemplateID = "";
                    $description = "";
                    $linkWorkflow = "";
                    $last_id = $this->input->post('workProcessID');
                    if (isset($_POST["customWorkFlowID"])) {

                        $workFlowID = explode(',', $this->input->post("customWorkFlowID"));
                        $workFlowTemplateID = explode(',', $this->input->post("customWorkFlowTemplateID"));
                        $description = explode(',', $this->input->post("customDescription"));
                        $linkWorkflow = $this->input->post("linkProcess");
                    }

                    $data = array();
                    $templateDet = "";
                    if ($workFlowID) {
                        foreach ($workFlowID as $key => $val) {
                            $data[] = array('jobID' => $last_id, 'workFlowID' => $val, 'workFlowTemplateID' => $workFlowTemplateID[$key], 'sortOrder' => $key + 1, 'description' => $description[$key], 'companyID' => current_companyID(), 'templateMasterID' => $this->input->post('workFlowTemplateID'));
                        }
                        $this->db->insert_batch("srp_erp_mfq_customtemplatedetail", $data);

                        if ($linkWorkflow) {
                            $this->db->query("UPDATE srp_erp_mfq_customtemplatedetail AS cust
                                LEFT JOIN (
                                    SELECT prev_id, cur_id 
                                    FROM (
                                        SELECT templateDetailID AS cur_id, ( SELECT min( templateDetailID ) FROM srp_erp_mfq_customtemplatedetail WHERE templateDetailID = (cur_id-1) AND jobID = {$last_id} ) AS prev_id 
                                        FROM srp_erp_mfq_customtemplatedetail 
                                        WHERE jobID = {$last_id} 
                                        ) AS tmp 
                                    WHERE prev_id IS NOT NULL
                                ) target ON target.cur_id = cust.templateDetailID SET cust.linkWorkFlow = target.prev_id WHERE cust.jobID = {$last_id}");
                        }

                        $this->db->select("*");
                        $this->db->from("srp_erp_mfq_customtemplatedetail");
                        $this->db->where("templateMasterID", $this->input->post('workFlowTemplateID'));
                        $this->db->where("jobID", $last_id);
                        $templateDet = $this->db->get()->result_array();
                        $data = array();
                        if ($templateDet) {
                            foreach ($templateDet as $val) {
                                $data[] = array('workFlowID' => $val["workFlowID"], 'templateDetailID' => $val["templateDetailID"], 'jobID' => $last_id, 'companyID' => current_companyID());
                            }
                            $this->db->insert_batch("srp_erp_mfq_workflowstatus", $data);
                        }
                    } else {
                        $this->db->select("*");
                        $this->db->from("srp_erp_mfq_templatedetail");
                        $this->db->where("templateMasterID", $this->input->post('workFlowTemplateID'));
                        $templateDet = $this->db->get()->result_array();
                        $data = array();
                        if ($templateDet) {
                            foreach ($templateDet as $val) {
                                $data[] = array('workFlowID' => $val["workFlowID"], 'templateDetailID' => $val["templateDetailID"], 'jobID' => $last_id, 'companyID' => current_companyID());
                            }
                            $this->db->insert_batch("srp_erp_mfq_workflowstatus", $data);
                        }
                    }
                    if ($fromType == "EST") {
                        $data = [];
                        $data['isFromEstimate'] = 2;
                        $this->db->where('workProcessID', $this->input->post('workProcessID'));
                        $result = $this->db->update('srp_erp_mfq_job', $data);

                        if ($this->input->post('bomMasterID')) {
                            $this->db->select("*");
                            $this->db->from("srp_erp_mfq_warehousemaster");
                            $this->db->where("mfqWarehouseAutoID", $this->input->post('mfqWarehouseAutoID'));
                            $warehouse = $this->db->get()->row_array();

                            $this->db->select("*");
                            $this->db->from("srp_erp_warehousemaster");
                            $this->db->where("warehouseAutoID", $warehouse["warehouseAutoID"]);
                            $warehouseERP = $this->db->get()->row_array();

                            $this->db->query("INSERT INTO srp_erp_warehouseitems (wareHouseAutoID,wareHouseLocation,wareHouseDescription,itemAutoID,itemSystemCode,itemDescription,unitOfMeasureID,unitOfMeasure,currentStock,companyID,companyCode) SELECT " . $warehouseERP["wareHouseAutoID"] . ",'" . $warehouseERP["wareHouseLocation"] . "','" . $warehouseERP["wareHouseDescription"] . "',srp_erp_itemmaster.itemAutoID,srp_erp_itemmaster.itemSystemCode,srp_erp_itemmaster.itemDescription,srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_itemmaster.defaultUnitOfMeasure,0,srp_erp_itemmaster.companyID,srp_erp_itemmaster.companyCode FROM srp_erp_mfq_bom_materialconsumption mc INNER JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = mc.mfqItemID INNER JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_itemmaster.itemAutoID WHERE NOT EXISTS (SELECT * FROM srp_erp_warehouseitems WHERE srp_erp_warehouseitems.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID AND warehouseAutoID = " . $warehouse["warehouseAutoID"] . ") AND srp_erp_mfq_itemmaster.itemType = 3 AND mc.bomMasterID =" . $this->input->post('bomMasterID'));
                        }
                    }
                }
              
                $updateJobCard = $this->updateJobCardOnSave($this->input->post('workProcessID'));
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Job saved Failed ' . $this->db->_error_message());
                } else {  
                    $this->db->trans_commit();
                    return array('s', 'Job saved Successfully', $this->input->post('workProcessID'), "", "", "", $createdJobArr);
                }
            }
        }
    }

    function save_job_detail()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $format_Jobdate = input_format_date(trim($this->input->post('documentDate') ?? ''), $date_format_policy);

        $data['mfqItemID'] = $this->input->post('mfqItemID');
        $data['qty'] = $this->input->post('qty');
        $data['mfqCustomerAutoID'] = $this->input->post('mfqCustomerAutoID');
        $data['mfqSegmentID'] = $this->input->post('mfqSegmentID');
        $data['companyID'] = current_companyID();
        $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);//gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $data['modifiedUserID'] = current_userID();//$this->session->userdata("username");
        $data['modifiedUserName'] = current_user();//$this->session->userdata("username");
        $data['modifiedDateTime'] = current_date(true);
        $this->db->set('documentDate', $format_Jobdate);
        $this->db->where('workProcessID', $this->input->post('workProcessID'));
        $result = $this->db->update('srp_erp_mfq_job', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Job detail saved Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Job detail saved Successfully.', $this->input->post('workProcessID'));
        }
    }

    function save_sub_job()
    {
        $JBRpolicy = getPolicyValues('JBR', 'All');
        if(!$JBRpolicy) {
            $JBRpolicy = 0;
        }
        $MasterCurrencyRec = $this->db->query("SELECT transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces FROM `srp_erp_mfq_estimatemaster` WHERE estimateMasterID = {$this->input->post('estimateMasterID')}")->row_array();
        $qtyRequired = 0;
        $companyID = current_companyID();
        $itemDetail = get_specific_mfq_item($this->input->post('mfqItemID'));


        $mfqItemID = $this->input->post('mfqItemID');
        $validateMaterails = $this->db->query("SELECT itemAutoID AS erpItemAutoID, srp_erp_mfq_itemmaster.mfqItemID, itemSystemCode, secondaryItemCode, defaultUnitOfMeasure, itemName, itemDescription
                            FROM srp_erp_mfq_bom_materialconsumption
                            JOIN srp_erp_mfq_billofmaterial ON srp_erp_mfq_billofmaterial.bomMasterID = srp_erp_mfq_bom_materialconsumption.bomMasterID 
                            JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID                            
                            WHERE srp_erp_mfq_billofmaterial.mfqItemID = {$mfqItemID} AND srp_erp_mfq_billofmaterial.companyID = {$companyID}
                            AND (itemAutoID <= 0 OR mfqCategoryID <= 0 OR mfqSubCategoryID <= 0 OR itemType = 0 OR mainCategoryID = 0 OR subcategoryID = 0 OR defaultUnitOfMeasureID <= 0)")->result_array();

        if(!empty($validateMaterails)) {
            return array('w', 'Please update missing details for raw materials', $validateMaterails);
        }

        if ($itemDetail["mainCategory"] == "Inventory") {
            $qtyExceed = $this->db->query('SELECT IFNULL(SUM(qty),0) as qty FROM srp_erp_mfq_job WHERE mfqItemID=' . $this->input->post('mfqItemID') . ' AND linkedJobID=' . $this->input->post('workProcessID'))->row('qty');
            $qtyRequired = $this->input->post('expectedQty') - $qtyExceed;
        } else {
            $qtyRequired = $this->input->post('expectedQty');
        }
        if ($itemDetail["mainCategory"] == "Inventory" && $qtyRequired <= 0) {
            return array('w', 'You have already created the sufficient quantity ' . $this->input->post('expectedQty'));
        } else {
            $qtyRequired = $this->input->post('createQty');
            $this->db->trans_start();
            $jobCount = $this->db->query('SELECT COUNT(*) as jobCount FROM srp_erp_mfq_job WHERE linkedJobID=' . $this->input->post('workProcessID'))->row('jobCount');
            $header = $this->load_job_header();
            $code = str_replace("JOB", "PO", $header["documentCode"]) . " - " . (str_pad(($jobCount + 1), 2, '0', STR_PAD_LEFT));
            $this->db->set('expectedDeliveryDate', $header['expectedDeliveryDate']);
            $this->db->set('description', $header['description']);
            $this->db->set('serialNo', $header['serialNo']);
            $this->db->set('documentCode', $code);
            $this->db->set('documentDate', date('Y-m-d'));
            $this->db->set('startDate', date('Y-m-d'));
            $this->db->set('endDate', date('Y-m-d'));
            $this->db->set('mfqItemID', $this->input->post('mfqItemID'));
            $this->db->set('estimateDetailID', $this->input->post('estimateDetailID'));
            $this->db->set('bomMasterID', $this->input->post('bomMasterID'));
            $this->db->set('qty', $this->input->post('createQty'));
            // $this->db->set('qty', $qtyRequired);
            $this->db->set('mfqCustomerAutoID', $header['mfqCustomerAutoID']);
            $this->db->set('mfqSegmentID', $header['mfqSegmentID']);
            $this->db->set('type', 2);
            $this->db->set('mfqWarehouseAutoID', $header['mfqWarehouseAutoID']);
            $this->db->set('estimateMasterID', $this->input->post('estimateMasterID'));
            $this->db->set('documentID', 'JOB');
            $this->db->set('levelNo', 2);

            $this->db->set('transactionCurrencyID',$MasterCurrencyRec['transactionCurrencyID']) ;
            $this->db->set('transactionCurrency',$MasterCurrencyRec['transactionCurrency']) ;
            $this->db->set('transactionExchangeRate',1) ;
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($MasterCurrencyRec['transactionCurrencyID']));
            $this->db->set('companyLocalCurrencyID',$MasterCurrencyRec['companyLocalCurrencyID']) ;
            $this->db->set('companyLocalCurrency',$MasterCurrencyRec['companyLocalCurrency']) ;
            $default_currency = currency_conversionID($MasterCurrencyRec['transactionCurrencyID'],$MasterCurrencyRec['companyLocalCurrencyID']);
            $this->db->set('companyLocalExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyLocalCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);
            $this->db->set('companyReportingCurrencyID',$MasterCurrencyRec['companyReportingCurrencyID']) ;
            $this->db->set('companyReportingCurrency',$MasterCurrencyRec['companyReportingCurrency']) ;
            $default_currency = currency_conversionID($MasterCurrencyRec['transactionCurrencyID'],$MasterCurrencyRec['companyReportingCurrencyID']);
            $this->db->set('companyReportingExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyReportingCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            $this->db->select("srp_erp_customermaster.customerCurrencyID,srp_erp_customermaster.customerCurrency");
            $this->db->from("srp_erp_mfq_customermaster");
            $this->db->join("srp_erp_customermaster", "srp_erp_mfq_customermaster.CustomerAutoID=srp_erp_customermaster.customerAutoID", "LEFT");
            $this->db->where("mfqCustomerAutoID", $header['mfqCustomerAutoID']);
            $custInfo = $this->db->get()->row_array();

            $this->db->set('mfqCustomerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('mfqCustomerCurrency', $custInfo["customerCurrency"]);
            $customer_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $custInfo['customerCurrencyID']);
            $this->db->set('mfqCustomerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('mfqCustomerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);
            $this->db->set('isSaved', 0);
            $this->db->set('isFromEstimate', 1);
            $this->db->set('linkedJobID', $this->input->post('workProcessID'));

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_job');
            $last_id = $this->db->insert_id();
            $last_material_id = "";


            //Update item output table
            $this->db->set('companyID', current_companyID());
            $this->db->set('mfqItemID', $this->input->post('mfqItemID'));
            $this->db->set('itemAutoID', $itemDetail['itemAutoID']);
            $this->db->set('itemSystemCode', $itemDetail['itemSystemCode']);
            $this->db->set('itemName', $itemDetail['itemName']);
            $this->db->set('mfqItemDescription', $itemDetail['itemDescription']);
            $this->db->set('qty', $this->input->post('createQty'));
            $this->db->set('workProcessID', $last_id);
            $this->db->set('itemMaster', 1);
            $resultitem = $this->db->insert('srp_erp_mfq_joboutputitems');



            $wareHouseAutoID = $this->db->query('SELECT warehouseAutoID FROM srp_erp_mfq_warehousemaster  WHERE mfqWarehouseAutoID=' . $header['mfqWarehouseAutoID'])->row('warehouseAutoID');

            // insert record to warehouse if no item found for warehouse
            $bomMaterialConsumtion = $this->db->query("SELECT srp_erp_mfq_itemmaster.*  FROM srp_erp_mfq_bom_materialconsumption INNER JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID WHERE srp_erp_mfq_itemmaster.itemType = 1 AND bomMasterID='".$this->input->post('bomMasterID')." ' AND srp_erp_mfq_itemmaster.mainCategory = 'Inventory' ")->result_array();

            if ($bomMaterialConsumtion) {
                foreach ($bomMaterialConsumtion as $val) {
                    $this->db->select('itemAutoID');
                    $this->db->where('itemAutoID', $val['itemAutoID']);
                    $this->db->where('wareHouseAutoID', $wareHouseAutoID);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
                    $item_data = fetch_item_data($val['itemAutoID']);
                    if (empty($warehouseitems)) {
                        $wareHouseDetail = $this->db->query('SELECT * FROM srp_erp_warehousemaster  WHERE warehouseAutoID=' . $wareHouseAutoID)->row_array();
                        $data_arr = array(
                            'wareHouseAutoID' => $wareHouseAutoID,
                            'wareHouseLocation' => $wareHouseDetail['wareHouseLocation'],
                            'wareHouseDescription' => $wareHouseDetail['wareHouseDescription'],
                            'itemAutoID' => $val['itemAutoID'],
                            'barCodeNo' => $item_data['barcode'],
                            'salesPrice' => $item_data['companyLocalSellingPrice'],
                            'ActiveYN' => $item_data['isActive'],
                            'itemSystemCode' => $val['itemSystemCode'],
                            'itemDescription' => $val['itemDescription'],
                            'unitOfMeasureID' => $val['defaultUnitOfMeasureID'],
                            'unitOfMeasure' => $val['defaultUnitOfMeasure'],
                            'currentStock' => 0,
                            'companyID' => $this->common_data['company_data']['company_id'],
                            'companyCode' => $this->common_data['company_data']['company_code'],
                        );
                        $this->db->insert('srp_erp_warehouseitems', $data_arr);
                    }
                }
            }


            if($this->input->post('bomMasterID') && $JBRpolicy == 1)
            {
                $bomMaterialConsumtion = $this->db->query("SELECT srp_erp_mfq_bom_materialconsumption.*,(srp_erp_mfq_bom_materialconsumption.qtyUsed * '$qtyRequired') as required,
                        srp_erp_mfq_itemmaster.itemAutoID as itemAutoID,((srp_erp_mfq_bom_materialconsumption.qtyUsed * '$qtyRequired') - IFNULL(wi.currentStock,0)) as qtyRequired,
                        IFNULL(wi.currentStock,0) as currentStock
                        FROM srp_erp_mfq_bom_materialconsumption INNER JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID 
                        LEFT JOIN (
                        SELECT itemAutoID, SUM(transactionQTY / convertionRate) AS currentStock,wareHouseAutoID
                        FROM srp_erp_itemledger
                        WHERE companyID = $companyID
                        GROUP BY itemAutoID
                        ) wi ON wi.itemAutoID =  srp_erp_mfq_itemmaster.itemAutoID WHERE srp_erp_mfq_itemmaster.itemType = 1 AND bomMasterID= ".$this->input->post('bomMasterID')." AND srp_erp_mfq_itemmaster.mainCategory = 'Inventory'")->result_array();

                if($bomMaterialConsumtion) {
                    $wareHouseDetails = $this->db->query('SELECT * FROM srp_erp_warehousemaster WHERE warehouseAutoID=' . $wareHouseAutoID)->row_array();
                    $this->load->library('sequence');
                    $this->db->set('documentID', 'MR');
                    $this->db->set('itemType', 'Inventory');
                    $this->db->set('MRCode', $this->sequence->sequence_generator('MR'));
                    $this->db->set('requestedDate', current_date(true));
                    $this->db->set('jobNo', $this->input->post('workProcessID'));
                    $this->db->set('wareHouseAutoID', $wareHouseAutoID);
                    $this->db->set('wareHouseCode', $wareHouseDetails["wareHouseCode"]);
                    $this->db->set('wareHouseLocation', $wareHouseDetails["wareHouseLocation"]);
                    $this->db->set('wareHouseDescription', $wareHouseDetails["wareHouseDescription"]);
                    $this->db->set('employeeName', current_user());
                    $this->db->set('employeeCode', current_userCode());
                    $this->db->set('employeeID', current_userID());
                    $this->db->set('comment', $header['description']);
                    $this->db->set('createdUserGroup', $this->common_data['user_group']);
                    $this->db->set('createdPCID', $this->common_data['current_pc']);
                    $this->db->set('createdUserID', $this->common_data['current_userID']);
                    $this->db->set('createdUserName', $this->common_data['current_user']);
                    $this->db->set('createdDateTime', $this->common_data['current_date']);
                    $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                    $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                    $this->db->set('companyLocalExchangeRate', 1);
                    $this->db->set('companyLocalCurrencyDecimalPlaces', $this->common_data['company_data']['company_default_decimal']);
                    $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                    $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                    $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                    $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                    $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
                    $result = $this->db->insert('srp_erp_materialrequest');
                    $last_material_id = $this->db->insert_id();
                    foreach ($bomMaterialConsumtion as $val) {
                        //insert record to material request and detail
                        if ($last_material_id) {
                            $item_data = fetch_item_data($val['itemAutoID']);
                            $data['mrAutoID'] = $last_material_id;
                            $data['itemAutoID'] = $item_data['itemAutoID'];
                            $data['itemSystemCode'] = $item_data['itemSystemCode'];
                            $data['itemDescription'] = $item_data['itemDescription'];
                            $data['unitOfMeasure'] = $item_data['defaultUnitOfMeasure'];
                            $data['unitOfMeasureID'] = $item_data['defaultUnitOfMeasureID'];
                            $data['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                            $data['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
                            $data['qtyRequested'] = $val['required'];
                            $data['comments'] = $header['description'];
                            $data['remarks'] = '';
                            $data['currentWareHouseStock'] = $val['currentStock'];
                            $data['itemFinanceCategory'] = $item_data['subcategoryID'];
                            $data['itemFinanceCategorySub'] = $item_data['subSubCategoryID'];
                            $data['financeCategory'] = $item_data['financeCategory'];
                            $data['itemCategory'] = $item_data['mainCategory'];
                            $data['currentlWacAmount'] = $item_data['companyLocalWacAmount'];
                            $data['currentStock'] = $item_data['currentStock'];
    
                            if ($data['financeCategory'] == 1 or $data['financeCategory'] == 3) {
                                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                $data['PLGLCode'] = $item_data['costGLCode'];
                                $data['PLDescription'] = $item_data['costDescription'];
                                $data['PLType'] = $item_data['costType'];
    
                                $data['BLGLAutoID'] = $item_data['assteGLAutoID'];
                                $data['BLSystemGLCode'] = $item_data['assteSystemGLCode'];
                                $data['BLGLCode'] = $item_data['assteGLCode'];
                                $data['BLDescription'] = $item_data['assteDescription'];
                                $data['BLType'] = $item_data['assteType'];
                            } elseif ($data['financeCategory'] == 2) {
                                $data['PLGLAutoID'] = $item_data['costGLAutoID'];
                                $data['PLSystemGLCode'] = $item_data['costSystemGLCode'];
                                $data['PLGLCode'] = $item_data['costGLCode'];
                                $data['PLDescription'] = $item_data['costDescription'];
                                $data['PLType'] = $item_data['costType'];
    
                                $data['BLGLAutoID'] = '';
                                $data['BLSystemGLCode'] = '';
                                $data['BLGLCode'] = '';
                                $data['BLDescription'] = '';
                                $data['BLType'] = '';
                            }
    
                            $data['totalValue'] = ($data['currentlWacAmount'] * ($data['qtyRequested'] / $data['conversionRateUOM']));
    
                            $data['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data['companyID'] = $this->common_data['company_data']['company_id'];
                            $data['createdUserGroup'] = $this->common_data['user_group'];
                            $data['createdPCID'] = $this->common_data['current_pc'];
                            $data['createdUserID'] = $this->common_data['current_userID'];
                            $data['createdUserName'] = $this->common_data['current_user'];
                            $data['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_materialrequestdetails', $data);
    
                            $this->db->select('itemAutoID');
                            $this->db->where('itemAutoID', $item_data['itemAutoID']);
                            $this->db->where('wareHouseAutoID', $wareHouseAutoID);
                            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                            $warehouseitems = $this->db->get('srp_erp_warehouseitems')->row_array();
    
                            if (empty($warehouseitems)) {
                                $data_arr = array(
                                    'wareHouseAutoID' => $wareHouseAutoID,
                                    'wareHouseLocation' => $wareHouseDetails['wareHouseLocation'],
                                    'wareHouseDescription' => $wareHouseDetails['wareHouseDescription'],
                                    'itemAutoID' => $item_data['itemAutoID'],
                                    'barCodeNo' => $item_data['barcode'],
                                    'salesPrice' => $item_data['companyLocalSellingPrice'],
                                    'ActiveYN' => $item_data['isActive'],
                                    'itemSystemCode' => $item_data['itemSystemCode'],
                                    'itemDescription' => $item_data['itemDescription'],
                                    'unitOfMeasureID' => $item_data['defaultUnitOfMeasureID'],
                                    'unitOfMeasure' => $item_data['defaultUnitOfMeasure'],
                                    'currentStock' => 0,
                                    'companyID' => $this->common_data['company_data']['company_id'],
                                    'companyCode' => $this->common_data['company_data']['company_code'],
                                );
                                $this->db->insert('srp_erp_warehouseitems', $data_arr);
                            }
                        }
                    }
                }

                $bomMaterialConsumtion = $this->db->query("SELECT srp_erp_mfq_bom_materialconsumption.*,(srp_erp_mfq_bom_materialconsumption.qtyUsed * '$qtyRequired') as required,
                                                                srp_erp_mfq_itemmaster.itemAutoID as itemAutoID,((srp_erp_mfq_bom_materialconsumption.qtyUsed * '$qtyRequired') - IFNULL(wi.currentStock,0)) as qtyRequired,
                                                                IFNULL(wi.currentStock,0) as currentStock  FROM srp_erp_mfq_bom_materialconsumption INNER JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID 
                                                                LEFT JOIN (
                                                                SELECT itemAutoID, SUM(transactionQTY / convertionRate) AS currentStock,wareHouseAutoID
                                                                FROM srp_erp_itemledger
                                                                WHERE companyID = $companyID
                                                                GROUP BY itemAutoID
                                                                ) wi ON wi.itemAutoID =  srp_erp_mfq_itemmaster.itemAutoID WHERE srp_erp_mfq_itemmaster.itemType = 1 AND bomMasterID= ".$this->input->post('bomMasterID')." AND srp_erp_mfq_itemmaster.mainCategory = 'Inventory' HAVING required > currentStock")->result_array();

                if ($bomMaterialConsumtion) {
                    $this->load->library('sequence');
                    $this->db->set('documentID', 'PRQ');
                    $this->db->set('jobID', $this->input->post('workProcessID'));
                    $this->db->set('requestedEmpID', current_userID());
                    $this->db->set('requestedByName', current_user());
                    $this->db->set('expectedDeliveryDate', current_date(true));
                    $this->db->set('documentDate', current_date(true));
                    
                    $mfqSegmentID = $header['mfqSegmentID'];
                    if($mfqSegmentID) {
                        $segmentID = $this->db->query("SELECT srp_erp_segment.segmentID, srp_erp_mfq_segment.segmentCode FROM srp_erp_mfq_segment JOIN srp_erp_segment ON srp_erp_segment.segmentID = srp_erp_mfq_segment.mfqSegmentID WHERE mfqSegmentID = {$mfqSegmentID} AND srp_erp_mfq_segment.companyID = {$companyID}")->row_array();
                        $this->db->set('segmentID', $segmentID['segmentID']);
                        $this->db->set('segmentCode', $segmentID['segmentCode']);
                    }
                    $this->db->set('purchaseRequestCode', '');

                    $this->db->set('referenceNumber', $header['description']);
                    $this->db->set('narration', $header['description']);
                    $this->db->set('createdUserGroup', $this->common_data['user_group']);
                    $this->db->set('createdPCID', $this->common_data['current_pc']);
                    $this->db->set('createdUserID', $this->common_data['current_userID']);
                    $this->db->set('createdUserName', $this->common_data['current_user']);
                    $this->db->set('createdDateTime', $this->common_data['current_date']);
                    $this->db->set('transactionCurrencyID', $MasterCurrencyRec['transactionCurrencyID']);
                    $this->db->set('transactionCurrency', $MasterCurrencyRec['transactionCurrency']);
                    $this->db->set('transactionExchangeRate', 1);
                    $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($MasterCurrencyRec['transactionCurrencyID']));
                    $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                    $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                    $this->db->set('companyLocalExchangeRate', 1);
                    $this->db->set('companyLocalCurrencyDecimalPlaces', $this->common_data['company_data']['company_default_decimal']);
                    $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                    $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                    $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                    $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                    $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
                    $result = $this->db->insert('srp_erp_purchaserequestmaster');
                    $last_prq_id = $this->db->insert_id();

                    foreach ($bomMaterialConsumtion as $val) {
                        //insert record to material request and detail
                        if ($last_prq_id) {
                            $item_data = fetch_item_data($val['itemAutoID']);
                            $data_prq['purchaseRequestID'] = $last_prq_id;
                            $data_prq['expectedDeliveryDate'] = current_date(true);
                            $data_prq['itemAutoID'] = $item_data['itemAutoID'];
                            $data_prq['itemSystemCode'] = $item_data['itemSystemCode'];
                            $data_prq['itemDescription'] = $item_data['itemDescription'];
                            $data_prq['itemType'] = $item_data['mainCategory'];
                            $data_prq['unitOfMeasure'] = $item_data['defaultUnitOfMeasure'];
                            $data_prq['unitOfMeasureID'] = $item_data['defaultUnitOfMeasureID'];
                            $data_prq['defaultUOM'] = $item_data['defaultUnitOfMeasure'];
                            $data_prq['defaultUOMID'] = $item_data['defaultUnitOfMeasureID'];
                            $data_prq['conversionRateUOM'] = conversionRateUOM_id($data_prq['unitOfMeasureID'], $data_prq['defaultUOMID']);
                            $data_prq['requestedQty'] = $val['required'];
                            $data_prq['unitAmount'] = ($item_data['companyLocalWacAmount']);
                            $data_prq['totalAmount'] = ($item_data['companyLocalWacAmount'] * ($data_prq['requestedQty'] / $data_prq['conversionRateUOM']));
                            $data_prq['comment'] = $header['description'];
                            $data_prq['remarks'] = '';

                            $data_prq['companyCode'] = $this->common_data['company_data']['company_code'];
                            $data_prq['companyID'] = $this->common_data['company_data']['company_id'];
                            $data_prq['createdUserGroup'] = $this->common_data['user_group'];
                            $data_prq['createdPCID'] = $this->common_data['current_pc'];
                            $data_prq['createdUserID'] = $this->common_data['current_userID'];
                            $data_prq['createdUserName'] = $this->common_data['current_user'];
                            $data_prq['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_purchaserequestdetails', $data_prq);
                        }
                    }
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Job card added failed.' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                insert_warehouseitems($wareHouseAutoID); //if warehouse not exist in srp_erp_warehouseitems tbl it will insert the recordsa to the relevant warehouse. ;)
                return array('s', 'Job No: ' . $code, $last_id, $last_material_id);
            }
        }
    }

    function load_job_header()
    {
        $workProcessID = $this->input->post('workProcessID');
        $convertFormat = convert_date_format_sql();
        $data = $this->db->query('select outputWH.warehouseDescription as outputWarehouseDescription,srp_erp_mfq_job.qty as jobQty,srp_erp_mfq_warehousemaster.warehouseDescription,srp_erp_mfq_warehousemaster.warehouseAutoID as erpWarehouse,srp_erp_mfq_job.*,IFNULL(DATE_FORMAT(srp_erp_mfq_job.documentDate,\'' . $convertFormat . '\'), "") AS jobDate,IFNULL(DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\'), "") AS expectedDelDate,DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate,DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate,UnitDes,CustomerName,srp_erp_mfq_segment.description as segment,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN "RM" WHEN 2 THEN "FG" WHEN 3 THEN "SF"
            END," - ",srp_erp_mfq_itemmaster.itemSystemCode,\' - \',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,srp_erp_mfq_job.estimateDetailID,type,IFNULL(est.estimateCode,"") as estimateCode,IFNULL(ciCode,"") as ciCode,srp_erp_mfq_job.poNumber,srp_erp_mfq_job.poDate FROM srp_erp_mfq_job
            LEFT JOIN srp_erp_mfq_itemmaster ON  srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID 
            LEFT JOIN srp_erp_unit_of_measure ON UnitID = defaultUnitOfMeasureID 
            LEFT JOIN srp_erp_mfq_customermaster ON srp_erp_mfq_job.mfqCustomerAutoID=srp_erp_mfq_customermaster.mfqCustomerAutoID 
            LEFT JOIN srp_erp_mfq_segment ON srp_erp_mfq_segment.mfqSegmentID=srp_erp_mfq_job.mfqSegmentID 
            LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_warehousemaster.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID
            LEFT JOIN srp_erp_warehousemaster outputWH ON outputWH.wareHouseAutoID=srp_erp_mfq_job.outputWarehouseAutoID
            LEFT JOIN (SELECT IFNULL(estimateCode,"") as estimateCode,srp_erp_mfq_estimatedetail.estimateDetailID,IFNULL(ciCode,"") as ciCode FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatedetail.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID 
            LEFT JOIN srp_erp_mfq_customerinquiry ON srp_erp_mfq_customerinquiry.ciMasterID = srp_erp_mfq_estimatedetail.ciMasterID) est ON est.estimateDetailID = srp_erp_mfq_job.estimateDetailID WHERE workProcessID=' . $workProcessID)->row_array();
                
            //echo $this->db->last_query();exit;

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 1);
                        $this->db->order_by('createdDateTime', 'desc');
                        $data_eng = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($data_eng)>0){
                            $data['eng']=$data_eng[0];
                        }else{
                            $data['eng']=[];
                        }

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 2);
                        $this->db->order_by('createdDateTime', 'desc');
                        $data_pur = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($data_pur)>0){
                            $data['purchasing']=$data_pur[0];
                        }else{
                            $data['purchasing']=[];
                        }

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 3);
                        $this->db->order_by('createdDateTime', 'desc');
                        $data_production = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($data_production)>0){
                            $data['production']=$data_production[0];
                        }else{
                            $data['production']=[];
                        }

                        $this->db->select('*');
                        $this->db->where('documentID', 'JOB');
                        $this->db->where('documentmasterAutoID', $this->input->post('workProcessID'));
                        $this->db->where('responsibleType', 4);
                        $this->db->order_by('createdDateTime', 'desc');
                        $data_qc = $this->db->get('srp_erp_mfq_person_responsible')->result_array();

                        if(count($data_qc)>0){
                            $data['qc']=$data_qc[0];
                        }else{
                            $data['qc']=[];
                        }


        return $data;
    }

    function get_job_estimate_total(){
        $workProcessID = $this->input->post('workProcessID');

        $this->db->where('workProcessID',$workProcessID);
        $this->db->from('srp_erp_mfq_job');
        $estimate_details = $this->db->join('srp_erp_mfq_estimatemaster','srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID','left')->get()->row_array();

        if($estimate_details){
            return $estimate_details['totalSellingPrice'];
        }
    }

    function get_bill_of_material_detail(){

        $this->load->model('MFQ_BillOfMaterial_model');

        $workProcessID = $this->input->post('workProcessID');

        $this->db->where('workProcessID',$workProcessID);
        $job_detail =  $this->db->from('srp_erp_mfq_job')->get()->row_array();

        $grand_total = 0;

        if($job_detail && $job_detail['bomMasterID']){

            $_POST['bomMasterID'] = $job_detail['bomMasterID'];
            $results = $this->MFQ_BillOfMaterial_model->load_mfq_billOfMaterial_detail($job_detail['bomMasterID']);

            foreach($results as $key => $section){

                if($key == 'material'){
                    foreach($section as $material){
                        $grand_total += $material['materialCharge'];
                    }
                }elseif($key == 'labour'){
                    foreach($section as $labour){
                        $grand_total += $labour['totalValue'];
                    }
                }elseif($key == 'overhead'){
                    foreach($section as $overhead){
                        $grand_total += $overhead['totalValue'];
                    }
                }elseif($key == 'third_party_service'){
                    foreach($section as $third_party_service){
                        $grand_total += $third_party_service['totalValue'];
                    }
                }elseif($key == 'machine'){
                    foreach($section as $machine){
                        $grand_total += $machine['totalValue'];
                    }
                }
               
            }

            return $grand_total;

        }else{
            return '0';
        }
       
    }

    function load_unit_of_measure()
    {
        $mfqItemID = $this->input->post('mfqItemID');
        if ($mfqItemID) {
            $data = $this->db->query('select UnitDes,defaultUnitOfMeasureID FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_unit_of_measure ON UnitID = defaultUnitOfMeasureID WHERE mfqItemID=' . $mfqItemID)->row_array();
            //echo $this->db->last_query();
            return $data;
        } else {
            return '';
        }
    }

    function load_mfq_estimate()
    {
        $this->db->where('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('approvedYN', 1);
        $data = $this->db->get('srp_erp_mfq_estimatemaster')->result_array();
        return $data;
    }

    function get_workflow_status()
    {
        $this->db->where('jobID', $this->input->post('workProcessID'));
        //$this->db->where('status', 1);
        $this->db->join('srp_erp_mfq_templatedetail', 'srp_erp_mfq_templatedetail.templateDetailID = srp_erp_mfq_workflowstatus.templateDetailID', 'inner');
        $data = $this->db->get('srp_erp_mfq_workflowstatus')->result_array();

        $this->db->where('srp_erp_mfq_workflowstatus.jobID', $this->input->post('workProcessID'));
        //$this->db->where('status', 1);
        $this->db->join('srp_erp_mfq_customtemplatedetail', 'srp_erp_mfq_customtemplatedetail.templateDetailID = srp_erp_mfq_workflowstatus.templateDetailID', 'inner');
        $data2 = $this->db->get('srp_erp_mfq_workflowstatus')->result_array();

        return array_merge($data, $data2);
    }

    function get_jobs()
    {
        $this->db->where('workProcessID', $this->input->post('workProcessID'));
        $data = $this->db->get('srp_erp_mfq_jobcardmaster')->result_array();
        return $data;
    }

    function close_job()
    {
        $workProcessID = $this->input->post('workProcessID');
        $uomID = $this->input->post('uomID');
        $qty = $this->input->post('qty');
        $companyID = current_companyID();

        $method = $this->input->post('method');
        $this->db->select('warehouseAutoID');
        $this->db->where('workProcessID', $this->input->post('workProcessID'));
        $this->db->join("srp_erp_mfq_warehousemaster", "srp_erp_mfq_job.mfqWarehouseAutoID=srp_erp_mfq_warehousemaster.mfqWarehouseAutoID", "left");
        $warehouse = $this->db->get('srp_erp_mfq_job')->row_array();
        $erpWarehouse = $this->input->post('location');

        /*$this->db->select('itm.*,(srp_erp_mfq_jc_materialconsumption.qtyUsed-itm.whCurrentStock) as remainingQty');
        $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $this->input->post('workProcessID'));
        $this->db->where('srp_erp_mfq_jc_materialconsumption.qtyUsed > itm.whCurrentStock');
        $this->db->where('itm.mainCategory', "Inventory");
        $this->db->join("(SELECT srp_erp_mfq_itemmaster.*,wh.currentStock as whCurrentStock  FROM srp_erp_mfq_itemmaster LEFT JOIN (SELECT itemAutoID,currentStock FROM srp_erp_warehouseitems WHERE companyID = " . current_companyID() . " AND wareHouseAutoID = " . $warehouse["warehouseAutoID"] . ") wh ON wh.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID) itm", "itm.mfqItemID=srp_erp_mfq_jc_materialconsumption.mfqItemID", "left");
        $material = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();*/

        //if ($method == 2) {
        $this->db->where('jobID', $this->input->post('workProcessID'));
        $this->db->where('status', 0);
        $data = $this->db->get('srp_erp_mfq_workflowstatus')->result_array();
        if ($data) {
            return array('w', 'Please complete all the process in job');
        } else {
            $validate_attachment = $this->db->query("SELECT docSetupID FROM srp_erp_mfq_documentsetup WHERE isMandatory = 1 AND isActive = 1
            AND docSetupID NOT IN ( SELECT documentSubID FROM srp_erp_documentattachments WHERE documentSystemCode = {$workProcessID} AND documentID = 'MFQ_JOB' )")->row_array();
            if ($validate_attachment) {
                return array('w', 'Please Add all Mandatory Attachments!');
            } else {
                $linkedDocValidation = $this->db->query("SELECT * FROM (
                    SELECT stockTransferAutoID AS documentAuoID, 'ST' AS documentID, 'Stock Transfer' AS documentType, stockTransferCode AS documentCode, tranferDate AS documentDate 
                        FROM srp_erp_stocktransfermaster 
                        WHERE approvedYN != 1 AND jobID = {$workProcessID} UNION ALL
                    SELECT mrnAutoID AS documentAuoID, 'MRN' AS documentID, 'Material Receipt Note' AS documentType, mrnCode AS documentCode, receivedDate AS documentDate 
                        FROM srp_erp_materialreceiptmaster 
                        WHERE approvedYN != 1 AND jobID = {$workProcessID} UNION ALL
                    SELECT grvAutoID AS documentAuoID, 'GRV' AS documentID, 'Goods Received Voucher' AS documentType, grvPrimaryCode AS documentCode, grvDate AS documentDate  
                        FROM srp_erp_grvmaster 
                        WHERE approvedYN != 1 AND jobID = {$workProcessID}
                )tbl")->result_array();
                if($linkedDocValidation) {
                    return array('w', 'Please Approve All Pending Linked Documents!', '', $linkedDocValidation);
                } else {
                    $date_format_policy = date_format_policy();
                    $format_closedDate = input_format_date(trim($this->input->post('closedDate') ?? ''), $date_format_policy);

                    $this->db->select('documentCode, secondaryUOMID');
                    $this->db->where('workProcessID', $this->input->post('workProcessID'));
                    $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID');
                    $mas_dt = $this->db->get('srp_erp_mfq_job')->row_array();
                    /*  $validate_code = validate_code_duplication($mas_dt['documentCode'], 'documentCode', $this->input->post('workProcessID'), 'workProcessID', 'srp_erp_mfq_job');
                    if (!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }*/

                    /*$this->db->select("*");
                    $this->db->from('srp_erp_companyfinanceperiod');
                    $this->db->join('srp_erp_companyfinanceyear', "srp_erp_companyfinanceyear.companyFinanceYearID=srp_erp_companyfinanceperiod.companyFinanceYearID", "LEFT");
                    $this->db->where('srp_erp_companyfinanceperiod.companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where("'{$format_closedDate}' BETWEEN dateFrom AND dateTo");
                    $this->db->where("srp_erp_companyfinanceperiod.isActive", 1);
                    $financePeriod = $this->db->get()->row_array();
                    if ($financePeriod) {*/
                    try {
                        $this->db->trans_start();
                        $this->load->library('approvals');
                        $this->db->select('*');
                        $this->db->where('workProcessID', $this->input->post('workProcessID'));
                        $this->db->from('srp_erp_mfq_job');
                        $row = $this->db->get()->row_array();
                        $approvals_status = $this->approvals->CreateApproval('JOB', $row['workProcessID'], $row['documentCode'], 'Job', 'srp_erp_mfq_job', 'workProcessID', 0);

                        if ($approvals_status == 1) {
                            $date_format_policy = date_format_policy();
                            $format_closedDate = input_format_date(trim($this->input->post('closedDate') ?? ''), $date_format_policy);
                            $this->db->set('closedDate', $format_closedDate);
                            $this->db->set('closedComment', $this->input->post('closedComment'));
                            if(($this->input->post('primaryQty'))) {
                                $this->db->set('outputWarehouseAutoID', $this->input->post('outputWarehouseAutoID'));
                                $this->db->set('qty', $this->input->post('primaryQty'));
                                $qty = $this->input->post('primaryQty');
                                if($mas_dt['secondaryUOMID'] != null) {
                                    if($this->input->post('secondaryQty')) {
                                        $this->db->set('secondaryQty', $this->input->post('secondaryQty'));
                                    } else {
                                        $this->db->set('secondaryQty', $this->input->post('primaryQty'));
                                    }
                                }
                            }
                            $this->db->set('closedByEmpID', current_userID());
                            $this->db->set('closedYN', 1);
                            $this->db->set('postingFinanceDate', $format_closedDate);
                            $this->db->set('outputWarehouseAutoID',$erpWarehouse );
                            $this->db->set('qty', $qty);
                            $this->db->set('uomID',$uomID );
                            $this->db->where('workProcessID', $this->input->post('workProcessID'));

                            $this->db->update('srp_erp_mfq_job');


                            //Capture Exceeded Cost
                            $mfq_job = $this->db->where('workProcessID',$this->input->post('workProcessID'))->from('srp_erp_mfq_job')->get()->row_array();
                            $esimated = $this->db->where('estimateDetailID',$mfq_job['estimateDetailID'])->from('srp_erp_mfq_estimatedetail')->get()->row_array();

                            $exceded_cost = $mfq_job['unitPrice'] - round(($esimated['estimatedCost'] / $esimated['expectedQty']),2);

                            $this->db->set('exceededCost',$exceded_cost );
                            $this->db->where('workProcessID', $this->input->post('workProcessID'));
                            $this->db->update('srp_erp_mfq_job');

                            //update items checklist
                            $mfqf_itemID = $mfq_job['mfqItemID'];

                            $checklists = $this->db->where('mfqItemautoID',$mfqf_itemID)->from('srp_erp_mfq_itemmaster_checklist')->get()->result_array();

                            foreach($checklists as $checkDetail){

                                $data = array();
                                //check already added
                                $ex_record = $this->db->where('mfqItemautoID',$mfqf_itemID)->where('workProcessID',$this->input->post('workProcessID'))->where('checklistID',$checkDetail['id'])->from('srp_erp_mfq_itemmaster_checklist_values')->get()->row_array();

                                if(empty($ex_record)){

                                    $data['checklistID'] = $checkDetail['id'];
                                    $data['workProcessID'] = $this->input->post('workProcessID');
                                    $data['mfqItemautoID'] = $mfqf_itemID;
                                    $data['checklistDescription'] = $checkDetail['checklistDescription'];
                                    $data['companyID'] = $companyID;

                                    $this->db->insert('srp_erp_mfq_itemmaster_checklist_values',$data);

                                }

                            }

                           
                            $this->db->trans_complete();
                            if ($this->db->trans_status() === FALSE) {
                                $this->db->trans_rollback();
                                return array('e', "Error Occurred");
                            } else {
                                $this->db->trans_commit();
                                return array('s', "Successfully job closed");
                            }
                        } else if ($approvals_status == 3) {
                            $this->db->trans_complete();
                            $this->db->trans_rollback();
                            return array('w', "There are no users exist to perform approval for this document.");
                        } else {
                            $this->db->trans_complete();
                            $this->db->trans_rollback();
                            return array('e', "oops, something went wrong!");
                        }
                    } catch (Exception $e) {
                        return array('e', $e->getMessage());
                    }
                    /*} else {
                        return array('w', 'Closing date not between financial period');
                    }*/
                }
            }
        }
        /*} else {
            return array('w', 'Some item quantities are not sufficient to confirm this transaction', $material);
        }*/
    }

    function update_checklist_response(){
        $id = trim($this->input->post('id') ?? '');
        $value = trim($this->input->post('value') ?? '');

        $data = array();
        $data['values'] = $value;

        $this->db->where('id',$id)->update('srp_erp_mfq_itemmaster_checklist_values',$data);
        return array('s', 'Updated Successfully');
    }

    function save_job_approval()
    {
        $companyID =current_companyID();
        $this->db->trans_start();
        $this->load->library('approvals');
        $financePeriod = "";
        $date_format_policy = date_format_policy();
        $system_id = trim($this->input->post('workProcessID') ?? '');
        $jobcardID = trim($this->input->post('jobcardID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $maxLevel = trim($this->input->post('maxLevel') ?? '');
        $format_postingFinanceDate = input_format_date(trim($this->input->post('postingFinanceDate') ?? ''), $date_format_policy);


        if($level_id == $maxLevel) {
            $this->db->select("*");
            $this->db->from('srp_erp_companyfinanceperiod');
            $this->db->join('srp_erp_companyfinanceyear', "srp_erp_companyfinanceyear.companyFinanceYearID=srp_erp_companyfinanceperiod.companyFinanceYearID", "LEFT");
            $this->db->where('srp_erp_companyfinanceperiod.companyID', $this->common_data['company_data']['company_id']);
            $this->db->where("'{$format_postingFinanceDate}' BETWEEN dateFrom AND dateTo");
            $this->db->where("srp_erp_companyfinanceperiod.isActive", 1);
            $financePeriod = $this->db->get()->row_array();

            if (!$financePeriod) {
                return array('w', 'Finance date not between financial period');
            }
        }

        $this->db->select("itemAutoID, defaultUnitOfMeasureID, defaultUnitOfMeasure, qtyUsed, usageQty, warehouseAutoID, itemSystemCode");
        $this->db->from('srp_erp_mfq_jc_materialconsumption material');
        $this->db->join('srp_erp_mfq_itemmaster mfqItem', "mfqItem.mfqItemID = material.mfqItemID", "LEFT");
        $this->db->join('srp_erp_mfq_job mfqJob', "mfqJob.workProcessID = material.workProcessID", "LEFT");
        $this->db->join('srp_erp_mfq_warehousemaster mfqWareHouse', "mfqWareHouse.mfqWarehouseAutoID = mfqJob.mfqWarehouseAutoID", "LEFT");
        $this->db->where('material.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqItem.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqItem.mainCategory', 'Inventory');
        $this->db->where('mfqJob.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqWareHouse.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('material.jobCardID', $jobcardID);
        $itemsInJobCard = $this->db->get()->result_array();

        $invalidarr = array();
        if($itemsInJobCard) {
            $isMinusAllowed = getPolicyValues('MQT', 'All');
            if ($isMinusAllowed == 1) {
            foreach ($itemsInJobCard AS $itemz) {

                if($itemz['itemAutoID']) {
                    $itemStock_check = $this->db->query("SELECT IFNULL(SUM( transactionQTY / convertionRate ), 0) AS currentStock,defaultUnitOfMeasureID as defaultUOMID FROM srp_erp_itemmaster LEFT JOIN srp_erp_itemledger on srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID WHERE wareHouseAutoID = {$itemz['warehouseAutoID']} AND srp_erp_itemledger.itemAutoID = {$itemz['itemAutoID']}")->row_array();
                    if($itemz['defaultUnitOfMeasureID'] == $itemStock_check['defaultUOMID']) {
                        if ($itemStock_check['currentStock'] < $itemz['usageQty']) {
                            array_push($invalidarr, array("itemCode" => $itemz['itemSystemCode'], "itemDescription" => "Stock not sufficient " , "currentStock" => $itemStock_check['currentStock'] . " " . $itemz['defaultUnitOfMeasure']));
                        }
                    } else {
                        $conversionRateUOM = conversionRateUOM_id($itemz['defaultUnitOfMeasureID'], $itemStock_check['defaultUOMID']);
                        if(!$conversionRateUOM) { $conversionRateUOM = 1; }
                        $qtyUsed = $itemz['usageQty'] / $conversionRateUOM;
                        if($itemStock_check['currentStock'] < $qtyUsed) {
                            array_push($invalidarr, array("itemCode" => $itemz['itemSystemCode'], "itemDescription" => "Stock not sufficient " , "currentStock" => $itemStock_check['currentStock'] . " " . $itemz['defaultUnitOfMeasure']));
                        }
                    }
                }

            }
            }
            if (!empty($invalidarr)) {
                return array('w', 'Error In Approving the Job Card', $invalidarr);
            }
        }

        $linkedDocValidation = $this->db->query("SELECT * FROM (
            SELECT stockTransferAutoID AS documentAuoID, 'ST' AS documentID, 'Stock Transfer' AS documentType, stockTransferCode AS documentCode, tranferDate AS documentDate 
                FROM srp_erp_stocktransfermaster 
                WHERE approvedYN != 1 AND jobID = {$system_id} UNION ALL
            SELECT mrnAutoID AS documentAuoID, 'MRN' AS documentID, 'Material Receipt Note' AS documentType, mrnCode AS documentCode, receivedDate AS documentDate 
                FROM srp_erp_materialreceiptmaster 
                WHERE approvedYN != 1 AND jobID = {$system_id} UNION ALL
            SELECT grvAutoID AS documentAuoID, 'GRV' AS documentID, 'Goods Received Voucher' AS documentType, grvPrimaryCode AS documentCode, grvDate AS documentDate  
                FROM srp_erp_grvmaster 
                WHERE approvedYN != 1 AND jobID = {$system_id}
        )tbl")->result_array();
        if($linkedDocValidation) {
            return array('w', 'Please Approve All Pending Linked Documents!', '', $linkedDocValidation);
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'JOB');
            if ($approvals_status == 1) {
                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];
                $this->db->where('workProcessID', $system_id);
                $this->db->update('srp_erp_mfq_job', $data);

                $totalJobAmount = 0;
                $double_entry = $this->fetch_double_entry_job_test($this->input->post('workProcessID'), $jobcardID);
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['workProcessID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['closedDate'];
                    $generalledger_arr[$i]['documentYear'] = date("Y", strtotime($double_entry['master_data']['closedDate']));
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['closedDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['description'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                    $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = 1;
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    //$generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];

                    $totalJobAmount += $double_entry['gl_detail'][$i]['gl_dr'];
                }
    
                if (!empty($generalledger_arr)) {
                    //$this->db->insert_batch('srp_erp_mfq_generalledger', $generalledger_arr);
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                if($totalJobAmount > 0) {
                    $this->db->query("UPDATE srp_erp_mfq_job SET 
                    transactionAmount = {$totalJobAmount},
                    companyLocalAmount = ROUND(({$totalJobAmount} / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces),
                    companyReportingAmount = ROUND(({$totalJobAmount} / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces)
                    WHERE workProcessID='{$system_id}'");
                }

                $this->db->select('unitCost AS unitCost, srp_erp_mfq_jc_materialconsumption.mfqItemID AS mfqItemID, materialCharge as materialCharge,qtyUsed, usageQty, itm.*');
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $this->db->where('jobCardID', $jobcardID);
                $this->db->join('(SELECT srp_erp_itemmaster.*,mfqItemID FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID) itm', 'itm.mfqItemID=srp_erp_mfq_jc_materialconsumption.mfqItemID', 'LEFT');
                $materialConsumption = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
    
                $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
                $this->db->where('companyID', $companyID);
                $this->db->where('categoryID', 1);
                $materialGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
    
                for ($a = 0; $a < count($materialConsumption); $a++) {
                    if ($materialConsumption[$a]['mainCategory'] == 'Inventory') {
                        $itemAutoID = $materialConsumption[$a]['mfqItemID'];
                        $jobID = $this->input->post('workProcessID');
    
                        if($materialConsumption[$a]['mfqItemID'] == 2782) {
                            $qty = $materialConsumption[$a]['usageQty'] / 1;
                            $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                            /*$item_arr[$a]['companyLocalWacAmount'] = round(((($materialConsumption[$a]['currentStock'] * $materialConsumption[$a]['companyLocalWacAmount']) + $materialConsumption[$a]['materialCharge']) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item_arr[$a]['currentStock'] * $materialConsumption[$a]['companyReportingWacAmount']) + ($materialConsumption[$a]['materialCharge'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);*/
    
                            $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                            $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                            $itemledger_arr[$a]['referenceNumber'] = null;
                            $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                            $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                            $itemledger_arr[$a]['convertionRate'] = 1;
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                            $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                            $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                            $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                            /* $itemledger_arr[$a]['approvedYN'] =  $double_entry['master_data']['approvedYN'];
                                $itemledger_arr[$a]['approvedDate'] =  $double_entry['master_data']['approvedDate'];
                                $itemledger_arr[$a]['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
                                $itemledger_arr[$a]['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
                            $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                            /*$itemledger_arr[$a]['companyCode'] =  $double_entry['master_data']['companyCode'];*/
                            $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                        } else {
                            if($materialGLSetup) {
                                if($materialGLSetup['isEntryEnabled'] == 1) {
                                    //if($materialGLSetup['linkedDocEntry'] == 1) {
                                        /*Linked Document Qty*/
                                        $materialLinkedQty = $this->db->Query("SELECT SUM(usageAmount) as qty FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL AND jobID = {$jobID} AND typeMasterAutoID = {$itemAutoID}")->row('qty');
                                        
                                        if(!$materialLinkedQty) { $materialLinkedQty = 0; }
                                        if($materialGLSetup['manualEntry'] == 0 && $materialGLSetup['linkedDocEntry'] == 1){
                                            $materialConsumption[$a]['materialCharge'] = $materialLinkedQty * $materialConsumption[$a]['unitCost'];
                                            $materialConsumption[$a]['usageQty'] = $materialLinkedQty;
                                        } else if($materialGLSetup['manualEntry'] == 1 && $materialGLSetup['linkedDocEntry'] == 0){
                                            $materialConsumption[$a]['materialCharge'] = ($materialConsumption[$a]['usageQty'] - $materialLinkedQty) * $materialConsumption[$a]['unitCost'];
                                            $materialConsumption[$a]['usageQty'] = $materialConsumption[$a]['usageQty'] - $materialLinkedQty;
                                        }
                                    //}
        
                                    $qty = $materialConsumption[$a]['usageQty'] / 1;
                                    $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                                    $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                                    /*$item_arr[$a]['companyLocalWacAmount'] = round(((($materialConsumption[$a]['currentStock'] * $materialConsumption[$a]['companyLocalWacAmount']) + $materialConsumption[$a]['materialCharge']) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                                    $item_arr[$a]['companyReportingWacAmount'] = round(((($item_arr[$a]['currentStock'] * $materialConsumption[$a]['companyReportingWacAmount']) + ($materialConsumption[$a]['materialCharge'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);*/
        
                                    $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                                    $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = null;
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                                    $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                                    $itemledger_arr[$a]['convertionRate'] = 1;
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                                    $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                                    $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                                    /* $itemledger_arr[$a]['approvedYN'] =  $double_entry['master_data']['approvedYN'];
                                     $itemledger_arr[$a]['approvedDate'] =  $double_entry['master_data']['approvedDate'];
                                     $itemledger_arr[$a]['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
                                     $itemledger_arr[$a]['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
                                    $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                                    /*$itemledger_arr[$a]['companyCode'] =  $double_entry['master_data']['companyCode'];*/
                                    $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                                }
                            }
                        }
                    }
                }
    
                if (!empty($item_arr)) {
                    //$item_arr = array_values($item_arr);
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
    
                if (!empty($itemledger_arr)) {
                    //$itemledger_arr = array_values($itemledger_arr);
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }
    
                $itemledger_arr = array();
                $item_arr = array();
                if ($double_entry['master_data']['mainCategory'] == 'Inventory' or $double_entry['master_data']['mainCategory'] == 'Non Inventory') {
                    $itemAutoID = $double_entry['master_data']['itemAutoID'];
                    $qty = $double_entry['master_data']['qty'] / 1;
                    $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock + {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    $item_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
                    $item_arr['currentStock'] = ($double_entry['master_data']['currentStock'] + $qty);
                    $item_arr['companyLocalWacAmount'] = round(((($double_entry['master_data']['currentStock'] * $double_entry['master_data']['companyLocalWacAmount']) + $double_entry['total']) / $item_arr['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                    $item_arr['companyReportingWacAmount'] = round(((($item_arr['currentStock'] * $double_entry['master_data']['companyReportingWacAmount']) + ($double_entry['total'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);
    
                    $itemledger_arr['documentID'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr['documentCode'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                    $itemledger_arr['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                    $itemledger_arr['documentDate'] = $double_entry['master_data']['closedDate'];
                    $itemledger_arr['referenceNumber'] = null;
                    $itemledger_arr['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                    $itemledger_arr['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                    $itemledger_arr['FYBegin'] = $double_entry['master_data']['FYBegin'];
                    $itemledger_arr['FYEnd'] = $double_entry['master_data']['FYEnd'];
                    $itemledger_arr['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                    $itemledger_arr['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];

                    $outputWarehouseAutoID = $double_entry['master_data']['outputWarehouseAutoID'];
                    if($outputWarehouseAutoID==''){
                        $itemledger_arr['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                    }else{
                        $itemledger_arr['wareHouseAutoID'] = $outputWarehouseAutoID;
                    }
                    $wh = $itemledger_arr['wareHouseAutoID'];
                    $whDetails=$this->db->query("select * from srp_erp_warehousemaster where wareHouseAutoID=$wh")->row();
                    $itemledger_arr['wareHouseCode'] = $whDetails->wareHouseCode;
                    $itemledger_arr['wareHouseLocation'] = $whDetails->wareHouseLocation;
                    $itemledger_arr['wareHouseDescription'] = $whDetails->wareHouseDescription;
                    $itemledger_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
                    $itemledger_arr['itemSystemCode'] = $double_entry['master_data']['itemSystemCode'];
                    $itemledger_arr['itemDescription'] = $double_entry['master_data']['itemDescription'];
                    $itemledger_arr['defaultUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
                    $itemledger_arr['defaultUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
                    $itemledger_arr['transactionQTY'] = $double_entry['master_data']['qty'];
                    $itemledger_arr['convertionRate'] = 1;
                    $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                    $itemledger_arr['PLGLAutoID'] = $double_entry['master_data']['costGLAutoID'];
                    $itemledger_arr['PLSystemGLCode'] = $double_entry['master_data']['costSystemGLCode'];
                    $itemledger_arr['PLGLCode'] = $double_entry['master_data']['costGLCode'];
                    $itemledger_arr['PLDescription'] = $double_entry['master_data']['costDescription'];
                    $itemledger_arr['PLType'] = $double_entry['master_data']['costType'];
                    $itemledger_arr['BLGLAutoID'] = $double_entry['master_data']['assteGLAutoID'];
                    $itemledger_arr['BLSystemGLCode'] = $double_entry['master_data']['assteSystemGLCode'];
                    $itemledger_arr['BLGLCode'] = $double_entry['master_data']['assteGLCode'];
                    $itemledger_arr['BLDescription'] = $double_entry['master_data']['assteDescription'];
                    $itemledger_arr['BLType'] = $double_entry['master_data']['assteType'];
                    $itemledger_arr['transactionAmount'] = $double_entry['total'];
                    $itemledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $itemledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $itemledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $itemledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $itemledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $itemledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr['companyLocalWacAmount'] = $item_arr['companyLocalWacAmount'];
                    $itemledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $itemledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $itemledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr['companyReportingWacAmount'] = $item_arr['companyReportingWacAmount'];
                    $itemledger_arr['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                    $itemledger_arr['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                    $itemledger_arr['partyCurrencyExchangeRate'] = 1;
                    $itemledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                    $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
                    $itemledger_arr['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                    $itemledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $itemledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $itemledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    /* $itemledger_arr['approvedYN'] =  $double_entry['master_data']['approvedYN'];
                     $itemledger_arr['approvedDate'] =  $double_entry['master_data']['approvedDate'];
                     $itemledger_arr['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
                     $itemledger_arr['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
                    $itemledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                    $itemledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                    $itemledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    /*$itemledger_arr['companyCode'] =  $double_entry['master_data']['companyCode'];*/
                    $itemledger_arr['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                    $itemledger_arr['createdPCID'] = $double_entry['master_data']['createdPCID'];
                    $itemledger_arr['createdUserID'] = $double_entry['master_data']['createdUserID'];
                    $itemledger_arr['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                    $itemledger_arr['createdUserName'] = $double_entry['master_data']['createdUserName'];
                    $itemledger_arr['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                    $itemledger_arr['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                    $itemledger_arr['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                    $itemledger_arr['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
    
                    if (!empty($item_arr)) {
                        $this->db->update('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                    }
    
                    if (!empty($itemledger_arr)) {
                        $this->db->insert('srp_erp_itemledger', $itemledger_arr);
                    }
                }
                $machine = $this->db->query("SELECT SUM(totalValue) as totalValue FROM srp_erp_mfq_jc_machine WHERE workProcessID='{$double_entry['master_data']['workProcessID']}'")->row_array();
                $unitPrice = (($double_entry['total'] + $machine["totalValue"]) / $double_entry['master_data']['qty']);
    
                $this->db->set('postingFinanceDate', $format_postingFinanceDate);
                $this->db->set('companyFinancePeriodID', $financePeriod["companyFinancePeriodID"]);
                $this->db->set('companyFinanceYearID', $financePeriod["companyFinanceYearID"]);
                $this->db->set('FYBegin', $financePeriod["beginingDate"]);
                $this->db->set('FYEnd', $financePeriod["endingDate"]);
                $this->db->set('FYPeriodDateFrom', $financePeriod["dateFrom"]);
                $this->db->set('FYPeriodDateTo', $financePeriod["dateTo"]);
                $this->db->set('unitPrice', $unitPrice);
                $this->db->where('workProcessID', $double_entry['master_data']['workProcessID']);
                $result = $this->db->update('srp_erp_mfq_job');
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully approved");
            }
        }
    }

    function fetch_double_entry_job($jobID, $jobcardID)
    {
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $master = "";
        $this->db->select('srp_erp_mfq_itemmaster.*');
        $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
        $chk_category = $this->db->get('srp_erp_mfq_job')->row_array();

        if($chk_category["mainCategory"] == "Service" || $chk_category["mainCategory"] == "Non Inventory"){
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_itemmaster.defaultUnitOfMeasureID,
	srp_erp_itemmaster.defaultUnitOfMeasure,
	srp_erp_itemmaster.currentStock,
	srp_erp_itemmaster.mainCategory,
	srp_erp_itemmaster.costGLAutoID,
	srp_erp_itemmaster.costSystemGLCode,
	srp_erp_itemmaster.costGLCode,
	srp_erp_itemmaster.costDescription,
	srp_erp_itemmaster.costType,
	srp_erp_itemmaster.assteGLAutoID,
	srp_erp_itemmaster.assteSystemGLCode,
	srp_erp_itemmaster.assteGLCode,
	srp_erp_itemmaster.assteDescription,
	srp_erp_itemmaster.assteType, 
	srp_erp_itemmaster.companyLocalWacAmount, 
	srp_erp_itemmaster.companyReportingWacAmount 
	FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_itemmaster.unbilledServicesGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }else{
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_itemmaster.defaultUnitOfMeasureID,
	srp_erp_itemmaster.defaultUnitOfMeasure,
	srp_erp_itemmaster.currentStock,
	srp_erp_itemmaster.mainCategory,
	srp_erp_itemmaster.costGLAutoID,
	srp_erp_itemmaster.costSystemGLCode,
	srp_erp_itemmaster.costGLCode,
	srp_erp_itemmaster.costDescription,
	srp_erp_itemmaster.costType,
	srp_erp_itemmaster.assteGLAutoID,
	srp_erp_itemmaster.assteSystemGLCode,
	srp_erp_itemmaster.assteGLCode,
	srp_erp_itemmaster.assteDescription,
	srp_erp_itemmaster.assteType, 
	srp_erp_itemmaster.companyLocalWacAmount, 
	srp_erp_itemmaster.companyReportingWacAmount 
	FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }

        $this->db->select('oh.*,srp_erp_mfq_jc_overhead.*');
        $this->db->where('workProcessID', $jobID);
        $this->db->where('jobCardID', $jobcardID);
        $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_overhead.overHeadID', 'LEFT');
        $overheadGL = $this->db->get('srp_erp_mfq_jc_overhead')->result_array();

        $this->db->select('oh.*,srp_erp_mfq_jc_labourtask.*');
        $this->db->where('workProcessID', $jobID);
        $this->db->where('jobCardID', $jobcardID);
        $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_labourtask.labourTask', 'LEFT');
        $labourGL = $this->db->get('srp_erp_mfq_jc_labourtask')->result_array();

        $this->db->select('materialCharge as materialCharge,jcMaterialConsumptionID,wh.*');
        $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $jobID);
        $this->db->where('srp_erp_mfq_jc_materialconsumption.jobCardID', $jobcardID);
        $this->db->join("(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,workProcessID FROM srp_erp_mfq_job LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_warehousemaster.WIPGLAutoID = srp_erp_chartofaccounts.GLAutoID) wh", "wh.workProcessID=srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
        $materialGL = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();

        $this->db->select('srp_erp_mfq_jc_machine.jcMachineID, srp_erp_mfq_jc_machine.transactionExchangeRate, srp_erp_mfq_jc_machine.companyLocalExchangeRate, srp_erp_mfq_jc_machine.companyReportingExchangeRate, srp_erp_mfq_fa_asset_master.glAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, srp_erp_mfq_jc_machine.totalValue');
        $this->db->where('srp_erp_mfq_jc_machine.workProcessID', $jobID);
        $this->db->where('srp_erp_mfq_jc_machine.jobCardID', $jobcardID);
        $this->db->join("srp_erp_mfq_fa_asset_master", "srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID","left");
        $this->db->join("srp_erp_chartofaccounts", "srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_fa_asset_master.glAutoID","left");
        $machine = $this->db->get('srp_erp_mfq_jc_machine')->result_array();

        $globalArray = array();
        $total = 0;

        /*overhead GL*/
        if ($overheadGL) {
            foreach ($overheadGL as $val) {
                $data_arr['auto_id'] = $val['jcOverHeadID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['totalValue'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['totalValue'];
                array_push($globalArray, $data_arr);
            }
        }

        /*labour GL*/
        if ($labourGL) {
            foreach ($labourGL as $val) {
                $data_arr['auto_id'] = $val['jcLabourTaskID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['totalValue'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['totalValue'];
                array_push($globalArray, $data_arr);
            }
        }
        /*material consumption GL*/
        if ($materialGL) {
            foreach ($materialGL as $val) {
                $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['materialCharge'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['materialCharge'];
                array_push($globalArray, $data_arr);
            }
        }

        /*Machine GL*/
        if ($machine) {
            foreach ($machine as $val) {
                $data_arr['auto_id'] = $val['jcMachineID'];
                $data_arr['gl_auto_id'] = $val['glAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['totalValue'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['totalValue'];
                array_push($globalArray, $data_arr);
            }
        }

        $data_arr['auto_id'] = $master['workProcessID'];
        $data_arr['gl_auto_id'] = $master['GLAutoID'];
        $data_arr['gl_code'] = $master['systemAccountCode'];
        $data_arr['secondary'] = $master['GLSecondaryCode'];
        $data_arr['gl_desc'] = $master['GLDescription'];
        $data_arr['gl_type'] = $master['subCategory'];
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['projectID'] = NULL;
        $data_arr['projectExchangeRate'] = NULL;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = '';
        $data_arr['partyAutoID'] = $master['customerAutoID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
        $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data_arr['partyExchangeRate'] = 1;
        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
        $data_arr['partyCurrencyAmount'] = ($total / $data_arr['partyExchangeRate']);
        $data_arr['gl_dr'] = $total;
        $data_arr['gl_cr'] = '';
        $data_arr['amount_type'] = 'dr';
        array_push($globalArray, $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'JOB';
        $gl_array['name'] = 'Job';
        $gl_array['primary_Code'] = $master['documentCode'];
        $gl_array['master_data'] = $master;
        $gl_array['date'] = $master['documentDate'];
        $gl_array['gl_detail'] = $globalArray;
        $gl_array['total'] = $total;
        return $gl_array;
    }

    function getSemifinishGoods()
    {
        $this->db->select("*");
        $this->db->from("srp_erp_mfq_warehousemaster");
        $this->db->where("mfqWarehouseAutoID", $this->input->post('mfqWarehouseAutoID'));
        $warehouse = $this->db->get()->row_array();

        $this->db->select("srp_erp_mfq_itemmaster.*,IFNULL(srp_erp_warehouseitems.currentStock,0) as currentStock,IFNULL(jcm.qtyUsed,0) as qtyInUse,IFNULL(jc.qty,0) as qtyInProduction, (((IFNULL(srp_erp_warehouseitems.currentStock,0)- IFNULL(jcm.qtyUsed,0)) + IFNULL(jc.qty,0))-(srp_erp_mfq_bom_materialconsumption.qtyUsed * {$this->input->post('qty')})) as remainingQty,srp_erp_mfq_billofmaterial.bomMasterID as bomID,(srp_erp_mfq_bom_materialconsumption.qtyUsed * {$this->input->post('qty')}) as bomQty");
        $this->db->from("srp_erp_mfq_bom_materialconsumption");
        $this->db->join("srp_erp_mfq_itemmaster", "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
        $this->db->join("srp_erp_warehouseitems", "srp_erp_warehouseitems.itemAutoID = srp_erp_mfq_itemmaster.itemAutoID AND srp_erp_warehouseitems.companyID = " . current_companyID() . " AND srp_erp_warehouseitems.warehouseAutoID =" . $warehouse["warehouseAutoID"], "left");
        $this->db->join("srp_erp_mfq_billofmaterial", "srp_erp_mfq_billofmaterial.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
        $this->db->join("(SELECT SUM(qtyUsed) as qtyUsed,srp_erp_mfq_jc_materialconsumption.mfqItemID FROM srp_erp_mfq_jc_materialconsumption LEFT JOIN srp_erp_mfq_job ON srp_erp_mfq_job.workProcessID = srp_erp_mfq_jc_materialconsumption.workProcessID WHERE approvedYN = 0 AND srp_erp_mfq_jc_materialconsumption.companyID = " . current_companyID() . " GROUP BY srp_erp_mfq_jc_materialconsumption.mfqItemID) jcm", "jcm.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");
        $this->db->join("(SELECT SUM(qty) as qty,mfqItemID FROM srp_erp_mfq_job WHERE mfqWarehouseAutoID = " . $this->input->post('mfqWarehouseAutoID') . " AND approvedYN = 0 AND companyID = " . current_companyID() . " GROUP BY mfqItemID) jc", "jc.mfqItemID = srp_erp_mfq_bom_materialconsumption.mfqItemID", "left");

        $this->db->where("srp_erp_mfq_bom_materialconsumption.bomMasterID", $this->input->post('bomMasterID'));
        $this->db->where("itemType", 3);
        $this->db->where("srp_erp_mfq_itemmaster.mainCategory", "Inventory");
        $this->db->having("remainingQty < ", 0);
        $bomDetail = $this->db->get()->result_array();
        return $bomDetail;
    }

    function load_route_card()
    {
        $this->db->where('jobID', $this->input->post('jobID'));
        $this->db->where('workProcessFlowID', $this->input->post('workFlowID'));
        $data = $this->db->get('srp_erp_mfq_job_routecard')->result_array();
        return $data;
    }

    function save_route_card()
    {
        $this->db->trans_start();
        $routeCardDetailID = $this->input->post('routeCardDetailID');
        $process = $this->input->post('process');
        if (!empty($process)) {
            foreach ($process as $key => $val) {
                if (!empty($routeCardDetailID[$key])) {
                    if (!empty($process[$key])) {
                        $this->db->set('jobID', $this->input->post('jobID'));
                        $this->db->set('workProcessFlowID', $this->input->post('workProcessFlowID'));
                        $this->db->set('process', $this->input->post('process')[$key]);
                        $this->db->set('Instructions', $this->input->post('Instructions')[$key]);
                        $this->db->set('acceptanceCriteria', $this->input->post('acceptanceCriteria')[$key]);
                        $this->db->set('QAQC', $this->input->post('QAQCO')[$key]);
                        $this->db->set('production', $this->input->post('productionO')[$key]);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('modifiedUserID', current_userID());
                        $this->db->set('modifiedUserName', current_user());
                        $this->db->set('modifiedDateTime', current_date(true));
                        $this->db->where('routeCardDetailID', $routeCardDetailID[$key]);
                        $result = $this->db->update('srp_erp_mfq_job_routecard');
                    }
                } else {
                    if (!empty($process[$key])) {
                        $this->db->set('jobID', $this->input->post('jobID'));
                        $this->db->set('workProcessFlowID', $this->input->post('workProcessFlowID'));
                        $this->db->set('process', $this->input->post('process')[$key]);
                        $this->db->set('Instructions', $this->input->post('Instructions')[$key]);
                        $this->db->set('acceptanceCriteria', $this->input->post('acceptanceCriteria')[$key]);
                        $this->db->set('QAQC', $this->input->post('QAQCO')[$key]);
                        $this->db->set('production', $this->input->post('productionO')[$key]);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $result = $this->db->insert('srp_erp_mfq_job_routecard');
                    }
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Route card saved failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Route Card Saved Successfully.');
        }
    }

    function delete_routecard()
    {
        $result = $this->db->delete('srp_erp_mfq_job_routecard', array('routeCardDetailID' => $this->input->post('routeCardDetailID')), 1);
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function load_material_consumption_qty()
    {
        $this->db->where('jobID', $this->input->post('jobID'));
        $this->db->where('status', 0);
        $this->db->order_by('workProcessFlowID', 'asc');
        $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

        $this->db->select("srp_erp_mfq_jc_materialconsumption.workProcessID,jcMaterialConsumptionID,CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,IFNULL(wh.currentStock,0) as currentStock,srp_erp_mfq_jc_materialconsumption.qtyUsed,usageQty,srp_erp_mfq_jc_materialconsumption.jobCardID,srp_erp_mfq_jc_materialconsumption.mfqItemID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_warehousemaster', "srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID", 'inner');
        $this->db->join('srp_erp_mfq_jc_materialconsumption', "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'inner');
        $this->db->join('(SELECT SUM(currentStock) as currentStock,wareHouseAutoID,itemAutoID FROM srp_erp_warehouseitems GROUP BY wareHouseAutoID,itemAutoID) wh', "wh.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID AND srp_erp_mfq_itemmaster.itemAutoID = wh.itemAutoID", 'left');
        $data["material"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_overhead.workProcessID,jcOverHeadID,totalHours,CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,usageHours,srp_erp_mfq_jc_overhead.jobCardID,srp_erp_mfq_jc_overhead.overHeadID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->where('srp_erp_mfq_overhead.typeID',1);
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_overhead', "srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_overhead.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_jc_overhead.overHeadID = srp_erp_mfq_overhead.overHeadID", 'inner');
        $data["overhead"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_overhead.workProcessID,jcOverHeadID,totalHours,CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,usageHours,srp_erp_mfq_jc_overhead.jobCardID,srp_erp_mfq_jc_overhead.overHeadID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->where('srp_erp_mfq_overhead.typeID',2);
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_overhead', "srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_overhead.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_jc_overhead.overHeadID = srp_erp_mfq_overhead.overHeadID", 'inner');
        $data["thirdparty"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_labourtask.workProcessID,jcLabourTaskID,totalHours,CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,usageHours,srp_erp_mfq_jc_labourtask.jobCardID,srp_erp_mfq_jc_labourtask.labourTask as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_labourtask', "srp_erp_mfq_jc_labourtask.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_labourtask.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_labourtask.labourTask", 'inner');
        $data["labour"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_machine.workProcessID,totalHours,jcMachineID,CONCAT(faCode,' - ',assetDescription) as description,usageHours");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_machine', "srp_erp_mfq_jc_machine.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_machine.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_fa_asset_master', "srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID", 'inner');
        $data["machine"] = $this->db->get()->result_array();

        return $data;
    }

    function save_usage_qty()
    {
        $this->db->trans_start();
        $jobID = $this->input->post('jobID');
        if (!empty($jobID)) {
            foreach ($jobID as $key => $val) {
                if (!empty($jobID[$key]) && $this->input->post('qtyUsage')[$key] != 0) {
                    $this->db->set('jobID', $jobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcMaterialConsumptionID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('qtyUsage')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 1);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $jobID[$key]);
                    $this->db->where('typeID', 1);
                    $this->db->where('jobDetailID', $this->input->post('jcMaterialConsumptionID')[$key]);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateQtyUsed = $this->db->get()->row('usageAmount');

                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET usageQty = {$updateQtyUsed},materialCost = unitCost * {$updateQtyUsed},materialCharge = (unitCost * {$updateQtyUsed})+((unitCost * {$updateQtyUsed})*(markUp/100))  WHERE jcMaterialConsumptionID=" . $this->input->post('jcMaterialConsumptionID')[$key]);
                }
            }
        }

        $ljobID = $this->input->post('ljobID');
        if (!empty($ljobID)) {
            foreach ($ljobID as $key => $val) {
                if (!empty($ljobID[$key]) && $this->input->post('ltotalHours')[$key] != 0) {
                    $this->db->set('jobID', $ljobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcLabourTaskID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('ltotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 2);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $ljobID[$key]);
                    $this->db->where('typeID', 2);
                    $this->db->where('jobDetailID', $this->input->post('jcLabourTaskID')[$key]);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_labourtask SET usageHours={$updateUsageAmount},totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcLabourTaskID=" . $this->input->post('jcLabourTaskID')[$key]);
                }
            }
        }

        $ojobID = $this->input->post('ojobID');
        if (!empty($ojobID)) {
            foreach ($ojobID as $key => $val) {
                if (!empty($ojobID[$key]) && $this->input->post('ototalHours')[$key] != 0) {
                    $this->db->set('jobID', $ojobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcOverHeadID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('ototalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 3);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $ojobID[$key]);
                    $this->db->where('typeID', 3);
                    $this->db->where('jobDetailID', $this->input->post('jcOverHeadID')[$key]);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_overhead SET usageHours={$updateUsageAmount} ,totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcOverHeadID=" . $this->input->post('jcOverHeadID')[$key]);
                }
            }
        }

        $tpsjobID = $this->input->post('tpsjobID');
        if (!empty($tpsjobID)) {
            foreach ($tpsjobID as $key => $val) {
                if (!empty($tpsjobID[$key]) && $this->input->post('tpstotalHours')[$key] != 0) {
                    $this->db->set('jobID', $tpsjobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('tpsOverHeadID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('tpstotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 5);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $tpsjobID[$key]);
                    $this->db->where('typeID', 5);
                    $this->db->where('jobDetailID', $this->input->post('tpsOverHeadID')[$key]);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_overhead SET usageHours={$updateUsageAmount} ,totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcOverHeadID=" . $this->input->post('tpsOverHeadID')[$key]);
                }
            }
        }


        $mjobID = $this->input->post('mjobID');
        if (!empty($mjobID)) {
            foreach ($mjobID as $key => $val) {
                if (!empty($mjobID[$key]) && $this->input->post('mtotalHours')[$key] != 0) {
                    $this->db->set('jobID', $mjobID[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcMachineID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('mtotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 4);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $mjobID[$key]);
                    $this->db->where('typeID', 4);
                    $this->db->where('jobDetailID', $this->input->post('jcMachineID')[$key]);
                    $this->db->SELECT('SUM(usageAmount) as usageAmount');
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_machine SET usageHours={$updateUsageAmount},totalValue = hourlyRate*{$updateUsageAmount} WHERE jcMachineID=" . $this->input->post('jcMachineID')[$key]);
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Usage quantity saved failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Usage Quantity Saved Successfully.');
        }
    }

    function load_usage_history()
    {
        $this->db->select("CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,qtyUsage,srp_employeesdetails.Ename1");
        $this->db->from("srp_erp_mfq_jc_usage");
        $this->db->where('jobID', $this->input->post("jobID"));
        $this->db->where('srp_erp_mfq_jc_usage.jcMaterialConsumptionID', $this->input->post("jcMaterialConsumptionID"));
        $this->db->where('srp_erp_mfq_jc_usage.companyID', current_companyID());
        $this->db->join('srp_erp_mfq_jc_materialconsumption', "srp_erp_mfq_jc_materialconsumption.jcMaterialConsumptionID = srp_erp_mfq_jc_usage.jcMaterialConsumptionID", 'inner');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'inner');
        $this->db->join('srp_employeesdetails', "srp_employeesdetails.EidNo = srp_erp_mfq_jc_usage.createdUserID", 'inner');
        $data = $this->db->get()->result_array();

        return $data;
    }

    function save_material_request()
    {
        $this->db->trans_start();

        $this->db->set('wareHouseAutoID', $this->input->post('wareHouseAutoID'));
        $this->db->where('mrAutoID', $this->input->post('mrAutoID'));
        $result = $this->db->update('srp_erp_materialrequest');

        $mrDetailID = $this->input->post('mrDetailID');
        if (!empty($mrDetailID)) {
            foreach ($mrDetailID as $key => $val) {
                if (!empty($mrDetailID[$key]) && $this->input->post('qtyRequested')[$key] >= 0) {
                    $this->db->set('qtyRequested', $this->input->post('qtyRequested')[$key]);
                    $this->db->where('mrDetailID', $mrDetailID[$key]);
                    $result = $this->db->update('srp_erp_materialrequestdetails');
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Material Request saved failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Material Request Saved Successfully.');
        }
    }

    function fetch_usage_history()
    {
        $workProcessID = $this->input->post('jobID');
        $autoID = $this->input->post('autoID');
        $typeID = $this->input->post('typeID');
        $convertFormat = convert_date_format_sql();

        $sql = "SELECT *,DATE_FORMAT(createdDateTime,'" . $convertFormat . "') as createdDateTime FROM srp_erp_mfq_jc_usage WHERE jobID =" . $workProcessID . " AND jobDetailID =" . $autoID . " AND typeID =" . $typeID . " AND companyID=" . current_companyID();
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function delete_job()
    {
        $this->db->select('workProcessID');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('linkedJobID', trim($this->input->post('workProcessID') ?? ''));
        $linkedJob = $this->db->get()->row_array();
       
        if (empty($linkedJob)) {
            $data = array(
                'isDeleted' => 1,
                'deletedByEmpID' => current_userID(),
                'deletedDatetime' => current_date(),
            );
            $this->db->where('workProcessID', trim($this->input->post('workProcessID') ?? ''));
            $this->db->update('srp_erp_mfq_job', $data);
            $this->session->set_flashdata('s', 'Job Deleted Successfully.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'please delete all sub jobs before deleting this Job.');
            return true;
        }
    }

    function delete_sub_job()
    {
        $data = array();
        $workProcessID = $this->input->post('workProcessID');
       
        $this->db->select('workFlowTemplateID');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('workProcessID', trim($this->input->post('workProcessID') ?? ''));
        $workFlowTemplateID = $this->db->get()->row_array();

        if(!empty($workFlowTemplateID['workFlowTemplateID'])) {
            $data = $this->db->query("SELECT srp_erp_mfq_customtemplatedetail.workFlowID,ws.status
            FROM srp_erp_mfq_customtemplatedetail 
            LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_customtemplatedetail.workFlowTemplateID 
            LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID 
            LEFT JOIN (SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = $workProcessID) ws ON ws.templateDetailID = srp_erp_mfq_customtemplatedetail.templateDetailID
            WHERE templateMasterID= {$workFlowTemplateID['workFlowTemplateID']} 
            AND srp_erp_mfq_customtemplatedetail.jobID = {$workProcessID} 
            AND ws.status = 1
            ORDER BY srp_erp_mfq_customtemplatedetail.sortOrder")->result_array();

            if(empty($data)){
                $data = $this->db->query("SELECT srp_erp_mfq_templatedetail.workFlowID,ws.status 
                FROM srp_erp_mfq_templatedetail 
                LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_templatedetail.workFlowTemplateID 
                LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID 
                LEFT JOIN (SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = {$workProcessID}) ws ON ws.templateDetailID = srp_erp_mfq_templatedetail.templateDetailID  
                WHERE templateMasterID= {$workFlowTemplateID['workFlowTemplateID']} AND ws.status = 1 ORDER BY srp_erp_mfq_templatedetail.sortOrder")->result_array();
            }
        }
       
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_job');
        $this->db->where('linkedJobID', $this->input->post('workProcessID'));
        $this->db->where('closedYN', 1);
        $outputJob = $this->db->get()->result_array();

        $documentsPulled = $this->db->query("SELECT * FROM
                (
                    SELECT jobID FROM srp_erp_stocktransfermaster GROUP BY jobID UNION ALL
                    SELECT jobID FROM srp_erp_grvmaster GROUP BY jobID UNION ALL
                    SELECT jobID FROM srp_erp_materialreceiptmaster GROUP BY jobID
                ) tbl WHERE jobID = {$workProcessID}")->result_array();
        
        if (empty($data) && empty($outputJob)) {
            if(empty($documentsPulled)) {
                $this->db->where('workProcessID', trim($this->input->post('workProcessID') ?? ''));
                $this->db->delete('srp_erp_mfq_job');
                $this->session->set_flashdata('s', 'Job Deleted Successfully.');
                return true;
            } else {
                $this->session->set_flashdata('e', 'Documents Already Pulled for this Job.');
                return false;
            }
        } else {
            $this->session->set_flashdata('e', 'Job Card Already Created for this Job.');
            return false;
        }
    }

    function get_job_pulled_documents()
    {
        $companyID = current_companyID();
        $workProcessID = $this->input->post('workProcessID');
        $result['PRQ'] = $this->db->query("SELECT purchaseRequestID AS documentAutoID, documentID, documentDate, purchaseRequestCode AS DocumentCode, segmentCode, companyLocalCurrency AS transactionCurrency, IFNULL(amount/srp_erp_purchaserequestmaster.companyLocalExchangeRate,0) AS transactionAmount, srp_erp_purchaserequestmaster.companyLocalCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, approvedYN
                    FROM srp_erp_purchaserequestmaster 
                    LEFT JOIN (SELECT SUM(totalAmount) AS amount, purchaseRequestID AS detMasID FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID)det ON det.detMasID = srp_erp_purchaserequestmaster.purchaseRequestID
                    WHERE jobID = {$workProcessID} AND companyID = {$companyID} ")->result_array();
       
       $result['pulledDoc'] = $this->db->query("SELECT * FROM(          
                SELECT srp_erp_stocktransfermaster.stockTransferAutoID AS documentAutoID, documentID, tranferDate AS documentDate, stockTransferCode AS DocumentCode, segmentCode, companyLocalCurrency AS transactionCurrency, IFNULL(amount/srp_erp_stocktransfermaster.companyLocalExchangeRate,0) AS transactionAmount, companyLocalCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, approvedYN
                    FROM srp_erp_stocktransfermaster 
                    LEFT JOIN (SELECT SUM(totalValue) AS amount, stockTransferAutoID AS detMasID FROM srp_erp_stocktransferdetails GROUP BY stockTransferAutoID)det ON det.detMasID = srp_erp_stocktransfermaster.stockTransferAutoID
                    WHERE jobID = {$workProcessID} AND companyID = {$companyID}      
            UNION
                SELECT srp_erp_materialreceiptmaster.mrnAutoID AS documentAutoID, documentID, receivedDate AS documentDate, mrnCode AS DocumentCode, segmentCode, companyLocalCurrency AS transactionCurrency, IFNULL( amount/srp_erp_materialreceiptmaster.companyLocalExchangeRate, 0 ) AS transactionAmount, companyLocalCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, approvedYN
                    FROM srp_erp_materialreceiptmaster
                    LEFT JOIN ( SELECT SUM( totalValue ) AS amount, mrnAutoID AS detMasID FROM srp_erp_materialreceiptdetails GROUP BY mrnAutoID ) det ON det.detMasID = srp_erp_materialreceiptmaster.mrnAutoID 
                    WHERE jobID = {$workProcessID} AND companyID = {$companyID}
            UNION
                SELECT srp_erp_grvmaster.grvAutoID AS documentAutoID, documentID, grvDate AS documentDate, grvPrimaryCode AS DocumentCode, segmentCode, companyLocalCurrency AS transactionCurrency, IFNULL( amount/srp_erp_grvmaster.companyLocalExchangeRate, 0 ) AS transactionAmount, srp_erp_grvmaster.companyLocalCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces, approvedYN
                    FROM srp_erp_grvmaster
                    LEFT JOIN ( SELECT SUM( fullTotalAmount ) AS amount, grvAutoID AS detMasID FROM srp_erp_grvdetails GROUP BY grvAutoID ) det ON det.detMasID = srp_erp_grvmaster.grvAutoID 
                    WHERE jobID = {$workProcessID} AND companyID = {$companyID})tbl ORDER BY documentDate ASC")->result_array();     

        return $result;
    }

    function updateJobCardOnSave($workProcessID)
    {
        $companyID = current_companyID();
        $current_userID = current_userID();
        $current_user = current_user();
        $current_date = current_date();
        if(!empty($workProcessID)) {
            $this->db->select("srp_erp_mfq_job.*,UnitDes,srp_erp_mfq_itemmaster.itemDescription,IFNULL(est.estimateCode,'') as estimateCode");
            $this->db->from('srp_erp_mfq_job');
            $this->db->join('srp_erp_mfq_itemmaster', ' srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join('srp_erp_unit_of_measure', ' UnitID = defaultUnitOfMeasureID', 'LEFT');
            $this->db->join('(SELECT IFNULL(estimateCode,"") as estimateCode,srp_erp_mfq_estimatedetail.estimateDetailID FROM srp_erp_mfq_estimatedetail LEFT JOIN srp_erp_mfq_estimatemaster ON srp_erp_mfq_estimatedetail.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID) est', 'est.estimateDetailID = srp_erp_mfq_job.estimateDetailID', 'LEFT');
            $this->db->where('workProcessID', $workProcessID);
            $output = $this->db->get()->row_array();

            $templateDetails = $this->db->query("SELECT srp_erp_mfq_templatedetail.sortOrder, srp_erp_mfq_templatedetail.description, pageNameLink,
	srp_erp_mfq_templatedetail.workFlowID, documentID, ws.STATUS, srp_erp_mfq_templatedetail.templateDetailID, IFNULL( linkworkFlow, 0 ) AS linkworkFlow 
FROM srp_erp_mfq_templatedetail
	LEFT JOIN srp_erp_mfq_workflowtemplate ON srp_erp_mfq_workflowtemplate.workFlowTemplateID = srp_erp_mfq_templatedetail.workFlowTemplateID
	LEFT JOIN srp_erp_mfq_systemworkflowcategory ON srp_erp_mfq_workflowtemplate.workFlowID = srp_erp_mfq_systemworkflowcategory.workFlowID
	LEFT JOIN ( SELECT * FROM srp_erp_mfq_workflowstatus WHERE jobID = {$workProcessID} ) ws ON ws.templateDetailID = srp_erp_mfq_templatedetail.templateDetailID 
WHERE templateMasterID = {$output['workFlowTemplateID']} AND `status` = 0
ORDER BY srp_erp_mfq_templatedetail.sortOrder LIMIT 1")->row_array();

            $save = false;
            $this->db->select('*');
            $this->db->from('srp_erp_mfq_job');
            $this->db->where('linkedJobID', $workProcessID);
            $outputJob = $this->db->get()->result_array();
            $jobCount = count($outputJob);
            if (!empty($outputJob)) {
                $this->db->select('*');
                $this->db->from('srp_erp_mfq_job');
                $this->db->where('linkedJobID', $workProcessID);
                $this->db->where('closedYN', 1);
                $outputJob = $this->db->get()->result_array();
                $jobClosedCount = count($outputJob);
                if ($jobCount == $jobClosedCount) {
                    $save = true;
                } else {
                    $save = false;
                }
            } else {
                $save = true;
            }

            if ($save) {
                $last_id = "";
//                $this->db->trans_start();

                $this->db->set('jobNo', $output['documentCode']);
                $this->db->set('bomID', $output['bomMasterID']);
                $this->db->set('quotationRef', $output['estimateCode']);
                $this->db->set('description', $output['description']);
                $this->db->set('workProcessID', $this->input->post('workProcessID'));
                $this->db->set('workFlowID', 1);
                $this->db->set('templateDetailID', $templateDetails['templateDetailID']);
                $this->db->set('companyID', $companyID);
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', $current_userID);
                $this->db->set('createdUserName', $current_user);
                $this->db->set('createdDateTime', $current_date);

                $result = $this->db->insert('srp_erp_mfq_jobcardmaster');
                $last_id = $this->db->insert_id();

                $this->db->set('unitPrice', 0);
                $this->db->where('workProcessID', $workProcessID);
                $result = $this->db->update('srp_erp_mfq_job');

                $qty = $output['qty'];
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());

                } else {
                    /** Material Consumption */
                    $this->db->select("srp_erp_mfq_bom_materialconsumption.*,(qtyUsed * $qty) as qtyUsed,(qtyUsed * $qty) * unitCost  as materialCost,(((qtyUsed * $qty) * unitCost * markUp)/100)+((qtyUsed * $qty) * unitCost) as materialCharge,srp_erp_mfq_itemmaster.itemType,job.confirmedYN,job.linkedJobID,job.documentCode,CONCAT(CASE srp_erp_mfq_itemmaster.itemType WHEN 1 THEN 'RM' WHEN 2 THEN 'FG' WHEN 3 THEN 'SF'
END,' - ',srp_erp_mfq_itemmaster.itemDescription) as itemDescription,partNo,UnitDes");
                    $this->db->from('srp_erp_mfq_bom_materialconsumption');
                    $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_bom_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'inner');
                    $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_itemmaster.defaultUnitOfMeasureID', 'inner');
                    $this->db->join("(SELECT mfqItemID,confirmedYN,linkedJobID,documentCode FROM srp_erp_mfq_job WHERE linkedJobID = $workProcessID) job", 'srp_erp_mfq_bom_materialconsumption.mfqItemID = job.mfqItemID', 'left');
                    $this->db->where('bomMasterID', $output['bomMasterID']);
                    $materialConsumption = $this->db->get()->result_array();
                    if (!empty($materialConsumption)) {
                        foreach ($materialConsumption as $key => $val) {
                            if (!empty($val['mfqItemID'])) {
                                $this->db->set('mfqItemID', $val['mfqItemID']);
                                $this->db->set('qtyUsed', $val['qtyUsed']);
                                $this->db->set('usageQty', 0);
                                $this->db->set('unitCost', $val['unitCost']);
                                $this->db->set('materialCost', 0);
                                $this->db->set('markUp', $val['markUp']);
                                $this->db->set('materialCharge', 0);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $workProcessID);
                                $this->db->set('companyID', $companyID);

                                $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                                $this->db->set('transactionExchangeRate', 1);
                                $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                                $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                                $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                                $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                                $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                                $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                                $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                                $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                $this->db->set('createdUserID', $current_userID);
                                $this->db->set('createdUserName', $current_user);
                                $this->db->set('createdDateTime', $current_date);
                                $result = $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                            }
                        }
                    }

                    /** Labour Task */
                    $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
                    $this->db->from('srp_erp_mfq_bom_labourtask');
                    $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_labourtask.labourTask', 'inner');
                    $this->db->where('bomMasterID', $output['bomMasterID']);
                    $labourTask = $this->db->get()->result_array();
                    if (!empty($labourTask)) {
                        foreach ($labourTask as $key => $val) {
                            if (!empty($val['labourTask'])) {
                                $this->db->set('labourTask', $val['labourTask']);
                                /* $this->db->set('activityCode', $this->input->post('la_activityCode')[$key]);*/
                                $this->db->set('uomID', $val['uomID'] == "" ? NULL : $val['uomID']);
                                $this->db->set('segmentID', $val['segmentID']);
                                $this->db->set('subsegmentID', $val['subsegmentID']);
                                $this->db->set('hourlyRate', $val['hourlyRate']);
                                $this->db->set('totalHours', $val['totalHours']);
                                $this->db->set('usageHours', 0);
                                $this->db->set('totalValue', $val['totalValue']);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $workProcessID);
                                $this->db->set('companyID', $companyID);

                                $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                                $this->db->set('transactionExchangeRate', 1);
                                $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                                $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                                $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                                $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                                $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                                $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                                $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                                $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                $this->db->set('createdUserID', $current_userID);
                                $this->db->set('createdUserName', $current_user);
                                $this->db->set('createdDateTime', $current_date);
                                $result = $this->db->insert('srp_erp_mfq_jc_labourtask');
                            }
                        }
                    }

                    /** Over Head Cost*/
                    $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
                    $this->db->from('srp_erp_mfq_bom_overhead');
                    $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID', 'inner');
                    $this->db->where('bomMasterID', $output['bomMasterID']);
                    $this->db->where('srp_erp_mfq_overhead.typeID', 1);
                    $overheadCost = $this->db->get()->result_array();
                    if (!empty($overheadCost)) {
                        foreach ($overheadCost as $key => $val) {
                            if (!empty($val['overHeadID'])) {
                                $this->db->set('overHeadID', $val['overHeadID']);
                                /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                                $this->db->set('uomID', $val['uomID'] == "" ? NULL : $val['uomID']);
                                $this->db->set('segmentID', $val['segmentID']);
                                $this->db->set('hourlyRate', $val['hourlyRate']);
                                $this->db->set('totalHours', $val['totalHours']);
                                $this->db->set('usageHours', 0);
                                $this->db->set('totalValue', $val['totalValue']);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $workProcessID);
                                $this->db->set('companyID', $companyID);

                                $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                                $this->db->set('transactionExchangeRate', 1);
                                $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                                $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                                $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                                $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                                $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                                $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                                $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                                $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                $this->db->set('createdUserID', $current_userID);
                                $this->db->set('createdUserName', $current_user);
                                $this->db->set('createdDateTime', $current_date);
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                            }
                        }
                    }

                    /** Machine Cost*/
                    $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue,srp_erp_mfq_bom_machine.segmentID as segment");
                    $this->db->from('srp_erp_mfq_bom_machine');
                    $this->db->join('srp_erp_mfq_fa_asset_master', 'srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_bom_machine.mfq_faID', 'inner');
                    $this->db->where('bomMasterID', $output['bomMasterID']);
                    $machineCost = $this->db->get()->result_array();
                    if (!empty($machineCost)) {
                        foreach ($machineCost as $key => $val) {
                            if (!empty($val['mfq_faID'])) {
                                $this->db->set('mfq_faID', $val['mfq_faID']);
                                /*$this->db->set('activityCode', $this->input->post('mc_activityCode')[$key]);*/
                                $this->db->set('uomID', $val['uomID'] == "" ? NULL : $val['uomID']);
                                $this->db->set('segmentID', $val['segmentID']);
                                $this->db->set('hourlyRate', $val['hourlyRate']);
                                $this->db->set('totalHours', $val['totalHours']);
                                $this->db->set('usageHours', 0);
                                $this->db->set('totalValue', $val['totalValue']);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('workProcessID', $workProcessID);
                                $this->db->set('companyID', $companyID);

                                $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                                $this->db->set('transactionExchangeRate', 1);
                                $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                                $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                                $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                                $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                                $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                                $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                                $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                                $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                $this->db->set('createdUserID', $current_userID);
                                $this->db->set('createdUserName', $current_user);
                                $this->db->set('createdDateTime', $current_date);
                                $result = $this->db->insert('srp_erp_mfq_jc_machine');
                            }
                        }
                    }

                    /**Third Party Services */
                    $this->db->select("*,(totalHours * $qty) as totalHours,(totalHours * $qty) * hourlyRate as totalValue");
                    $this->db->from('srp_erp_mfq_bom_overhead');
                    $this->db->join('srp_erp_mfq_overhead', 'srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID', 'inner');
                    $this->db->where('bomMasterID', $output['bomMasterID']);
                    $this->db->where('srp_erp_mfq_overhead.typeID', 2);
                    $thirdparty = $this->db->get()->result_array();

                    $jcOverHeadIDthirdparty = $this->input->post('jcthirdpartyservice');
                    $overHeadID = $this->input->post('tpsID');

                    if (!empty($thirdparty)) {
                        foreach ($thirdparty as $key => $val) {
                            if (!empty($val['overHeadID'])) {
                                $this->db->set('overHeadID', $val['overHeadID']);
                                /*$this->db->set('activityCode', $this->input->post('oh_activityCode')[$key]);*/
                                $this->db->set('uomID', $val['uomID'] == "" ? NULL : $val['uomID']);
                                $this->db->set('hourlyRate', $val['hourlyRate']);
                                $this->db->set('totalHours', $val['totalHours']);
                                $this->db->set('totalValue', $val['totalValue']);
                                $this->db->set('jobCardID', $last_id);
                                $this->db->set('usageHours', 0);
                                $this->db->set('workProcessID', $workProcessID);
                                $this->db->set('companyID', $companyID);

                                $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                                $this->db->set('transactionExchangeRate', 1);
                                $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                                $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                                $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                                $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                                $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                                $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                                $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                                $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                                $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                                $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                                $this->db->set('createdUserID', $current_userID);
                                $this->db->set('createdUserName', $current_user);
                                $this->db->set('createdDateTime', $current_date);
                                $result = $this->db->insert('srp_erp_mfq_jc_overhead');
                            }
                        }
                    }
                    /**Third Party Services End */

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        return array('e', 'Job Card Saved Failed ' . $this->db->_error_message());
                    } else {
//                        $this->db->trans_commit();
                        return array('s', 'Job Card Saved Successfully.', $last_id);
                    }
                }
            } else {
                return array('w', 'There are pending related job cards to be closed');
            }
        }
    }

    function fetch_double_entry_job_test($jobID, $jobcardID)
    {
        $policyJEC = getPolicyValues('JEC', 'All');
        $companyID = current_companyID();
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $master = "";
        $this->db->select('srp_erp_mfq_itemmaster.*');
        $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
        $chk_category = $this->db->get('srp_erp_mfq_job')->row_array();

        if($chk_category["mainCategory"] == "Service" || $chk_category["mainCategory"] == "Non Inventory"){
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_itemmaster.defaultUnitOfMeasureID,
	srp_erp_itemmaster.defaultUnitOfMeasure,
	srp_erp_itemmaster.currentStock,
	srp_erp_itemmaster.mainCategory,
	srp_erp_itemmaster.costGLAutoID,
	srp_erp_itemmaster.costSystemGLCode,
	srp_erp_itemmaster.costGLCode,
	srp_erp_itemmaster.costDescription,
	srp_erp_itemmaster.costType,
	srp_erp_itemmaster.assteGLAutoID,
	srp_erp_itemmaster.assteSystemGLCode,
	srp_erp_itemmaster.assteGLCode,
	srp_erp_itemmaster.assteDescription,
	srp_erp_itemmaster.assteType, 
	srp_erp_itemmaster.companyLocalWacAmount, 
	srp_erp_itemmaster.companyReportingWacAmount 
	FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_itemmaster.unbilledServicesGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }else{
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.itemDescription,
	srp_erp_itemmaster.defaultUnitOfMeasureID,
	srp_erp_itemmaster.defaultUnitOfMeasure,
	srp_erp_itemmaster.currentStock,
	srp_erp_itemmaster.mainCategory,
	srp_erp_itemmaster.costGLAutoID,
	srp_erp_itemmaster.costSystemGLCode,
	srp_erp_itemmaster.costGLCode,
	srp_erp_itemmaster.costDescription,
	srp_erp_itemmaster.costType,
	srp_erp_itemmaster.assteGLAutoID,
	srp_erp_itemmaster.assteSystemGLCode,
	srp_erp_itemmaster.assteGLCode,
	srp_erp_itemmaster.assteDescription,
	srp_erp_itemmaster.assteType, 
	srp_erp_itemmaster.companyLocalWacAmount, 
	srp_erp_itemmaster.companyReportingWacAmount 
	FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }

        $globalArray = array();
        $total = 0;

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 3);
        $overheadGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($overheadGLSetup) {
            if($overheadGLSetup['isEntryEnabled'] == 1 && $overheadGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_overhead.*');
                $this->db->where('workProcessID', $jobID);
                $this->db->where('jobCardID', $jobcardID);
                $this->db->where('srp_erp_mfq_overhead.typeID', 1);
                $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID');
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_overhead.overHeadID', 'LEFT');
                $overheadGL = $this->db->get('srp_erp_mfq_jc_overhead')->result_array();
                /*overhead GL*/
                if ($overheadGL) {
                    foreach ($overheadGL as $val) {
                        $data_arr['auto_id'] = $val['jcOverHeadID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'];
                        $data_arr['segment'] = $master['segmentCode'];
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'];
                        $data_arr['partySystemCode'] = $master['customerSystemCode'];
                        $data_arr['partyName'] = $master['customerName'];
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 5);
        $thirdPartyServiceGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($thirdPartyServiceGLSetup) {
            if($thirdPartyServiceGLSetup['isEntryEnabled'] == 1 && $thirdPartyServiceGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_overhead.*');
                $this->db->where('workProcessID', $jobID);
                $this->db->where('jobCardID', $jobcardID);
                $this->db->where('srp_erp_mfq_overhead.typeID', 2);
                $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID');
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_overhead.overHeadID', 'LEFT');
                $thirdPartyServiceGL = $this->db->get('srp_erp_mfq_jc_overhead')->result_array();
                /*Third Party Service GL*/
                if ($thirdPartyServiceGL) {
                    foreach ($thirdPartyServiceGL as $val) {
                        $data_arr['auto_id'] = $val['jcOverHeadID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'];
                        $data_arr['segment'] = $master['segmentCode'];
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'];
                        $data_arr['partySystemCode'] = $master['customerSystemCode'];
                        $data_arr['partyName'] = $master['customerName'];
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 2);
        $labourGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($labourGLSetup) {
            if($labourGLSetup['isEntryEnabled'] == 1 && $labourGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_labourtask.*');
                $this->db->where('workProcessID', $jobID);
                $this->db->where('jobCardID', $jobcardID);
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_labourtask.labourTask', 'LEFT');
                $labourGL = $this->db->get('srp_erp_mfq_jc_labourtask')->result_array();

                /*labour GL*/
                if ($labourGL) {
                    foreach ($labourGL as $val) {
                        $data_arr['auto_id'] = $val['jcLabourTaskID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'];
                        $data_arr['segment'] = $master['segmentCode'];
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'];
                        $data_arr['partySystemCode'] = $master['customerSystemCode'];
                        $data_arr['partyName'] = $master['customerName'];
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 4);
        $machineSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($machineSetup) {
            if($machineSetup['isEntryEnabled'] == 1 && $machineSetup['manualEntry'] == 1) {
                $this->db->select('srp_erp_mfq_jc_machine.jcMachineID, srp_erp_mfq_jc_machine.transactionExchangeRate, srp_erp_mfq_jc_machine.companyLocalExchangeRate, srp_erp_mfq_jc_machine.companyReportingExchangeRate, srp_erp_mfq_fa_asset_master.glAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, srp_erp_mfq_jc_machine.totalValue');
                $this->db->where('srp_erp_mfq_jc_machine.workProcessID', $jobID);
                $this->db->where('srp_erp_mfq_jc_machine.jobCardID', $jobcardID);
                $this->db->join("srp_erp_mfq_fa_asset_master", "srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID","left");
                $this->db->join("srp_erp_chartofaccounts", "srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_fa_asset_master.glAutoID","left");
                $machine = $this->db->get('srp_erp_mfq_jc_machine')->result_array();

                /*Machine GL*/
                if ($machine) {
                    foreach ($machine as $val) {
                        $data_arr['auto_id'] = $val['jcMachineID'];
                        $data_arr['gl_auto_id'] = $val['glAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'];
                        $data_arr['segment'] = $master['segmentCode'];
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'];
                        $data_arr['partySystemCode'] = $master['customerSystemCode'];
                        $data_arr['partyName'] = $master['customerName'];
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 1);
        $materialGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($materialGLSetup) {
            if($materialGLSetup['isEntryEnabled'] == 1) {
                $this->db->select('qtyUsed AS qtyUsed,usageQty AS usageQty, unitCost AS unitCost, mfqItemID AS mfqItemID,materialCharge as materialCharge,jcMaterialConsumptionID,wh.*');
                $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $jobID);
                $this->db->where('srp_erp_mfq_jc_materialconsumption.jobCardID', $jobcardID);
                $this->db->where('mfqItemID != 2782');
                $this->db->join("(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,workProcessID FROM srp_erp_mfq_job LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_warehousemaster.WIPGLAutoID = srp_erp_chartofaccounts.GLAutoID) wh", "wh.workProcessID=srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
                $materialGL = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
                /*material consumption GL*/
                if ($materialGL) {
                    foreach ($materialGL as $val) {
                        /*Linked Document Qty*/
                        $materialLinkedQty = $this->db->Query("SELECT SUM(usageAmount) as qty FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL AND jobID = {$jobID} AND typeMasterAutoID = {$val['mfqItemID']}")->row('qty');

                        if($materialGLSetup['linkedDocEntry'] == 1 && $materialGLSetup['manualEntry'] == 1) {
                            $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                            $data_arr['gl_auto_id'] = $val['GLAutoID'];
                            $data_arr['gl_code'] = $val['systemAccountCode'];
                            $data_arr['secondary'] = $val['GLSecondaryCode'];
                            $data_arr['gl_desc'] = $val['GLDescription'];
                            $data_arr['gl_type'] = $val['subCategory'];
                            $data_arr['segment_id'] = $master['segmentID'];
                            $data_arr['segment'] = $master['segmentCode'];
                            $data_arr['projectID'] = NULL;
                            $data_arr['projectExchangeRate'] = NULL;
                            $data_arr['isAddon'] = 0;
                            $data_arr['subLedgerType'] = 0;
                            $data_arr['subLedgerDesc'] = null;
                            $data_arr['partyContractID'] = null;
                            $data_arr['partyType'] = 'Customer';
                            $data_arr['partyAutoID'] = $master['customerAutoID'];
                            $data_arr['partySystemCode'] = $master['customerSystemCode'];
                            $data_arr['partyName'] = $master['customerName'];
                            $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                            $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                            $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $data_arr['partyExchangeRate'] = 1;
                            $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                            $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                            $data_arr['gl_dr'] = '';
                            $data_arr['gl_cr'] = $val['materialCharge'];
                            $data_arr['amount_type'] = 'cr';
                            $total += $val['materialCharge'];
                            // print_r($val['materialCharge']);
                            array_push($globalArray, $data_arr);
                        } else if($materialGLSetup['manualEntry'] == 0 && $materialGLSetup['linkedDocEntry'] == 1) {
                            if(!empty($materialLinkedQty) && $materialLinkedQty != 0) {
                                
                                $val['materialCharge'] = $materialLinkedQty * $val['unitCost'];

                                $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                                $data_arr['gl_code'] = $val['systemAccountCode'];
                                $data_arr['secondary'] = $val['GLSecondaryCode'];
                                $data_arr['gl_desc'] = $val['GLDescription'];
                                $data_arr['gl_type'] = $val['subCategory'];
                                $data_arr['segment_id'] = $master['segmentID'];
                                $data_arr['segment'] = $master['segmentCode'];
                                $data_arr['projectID'] = NULL;
                                $data_arr['projectExchangeRate'] = NULL;
                                $data_arr['isAddon'] = 0;
                                $data_arr['subLedgerType'] = 0;
                                $data_arr['subLedgerDesc'] = null;
                                $data_arr['partyContractID'] = null;
                                $data_arr['partyType'] = 'Customer';
                                $data_arr['partyAutoID'] = $master['customerAutoID'];
                                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                                $data_arr['partyName'] = $master['customerName'];
                                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                                $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                                $data_arr['partyExchangeRate'] = 1;
                                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                                $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                                $data_arr['gl_dr'] = '';
                                $data_arr['gl_cr'] = $val['materialCharge'];
                                $data_arr['amount_type'] = 'cr';
                                $total += $val['materialCharge'];
                                array_push($globalArray, $data_arr);
                            }
                        } else if($materialGLSetup['manualEntry'] == 1 && $materialGLSetup['linkedDocEntry'] == 0) {
                            if(!empty($materialLinkedQty) && $materialLinkedQty != 0) {	
                                $val['materialCharge'] =  ($val['usageQty'] - $materialLinkedQty) * $val['unitCost'];	
                            }
                            $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                            $data_arr['gl_auto_id'] = $val['GLAutoID'];
                            $data_arr['gl_code'] = $val['systemAccountCode'];
                            $data_arr['secondary'] = $val['GLSecondaryCode'];
                            $data_arr['gl_desc'] = $val['GLDescription'];
                            $data_arr['gl_type'] = $val['subCategory'];
                            $data_arr['segment_id'] = $master['segmentID'];
                            $data_arr['segment'] = $master['segmentCode'];
                            $data_arr['projectID'] = NULL;
                            $data_arr['projectExchangeRate'] = NULL;
                            $data_arr['isAddon'] = 0;
                            $data_arr['subLedgerType'] = 0;
                            $data_arr['subLedgerDesc'] = null;
                            $data_arr['partyContractID'] = null;
                            $data_arr['partyType'] = 'Customer';
                            $data_arr['partyAutoID'] = $master['customerAutoID'];
                            $data_arr['partySystemCode'] = $master['customerSystemCode'];
                            $data_arr['partyName'] = $master['customerName'];
                            $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                            $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                            $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                            $data_arr['partyExchangeRate'] = 1;
                            $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                            $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                            $data_arr['gl_dr'] = '';
                            $data_arr['gl_cr'] = $val['materialCharge'];
                            $data_arr['amount_type'] = 'cr';
                            $total += $val['materialCharge'];
                            array_push($globalArray, $data_arr);
                        }
                    }
                }
            }
        }

        $this->db->select('qtyUsed AS qtyUsed, usageQty AS usageQty, unitCost AS unitCost, mfqItemID AS mfqItemID,materialCharge as materialCharge,jcMaterialConsumptionID,wh.*');
        $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $jobID);
        $this->db->where('srp_erp_mfq_jc_materialconsumption.jobCardID', $jobcardID);
        $this->db->where('mfqItemID', 2782);
        $this->db->join("(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,workProcessID FROM srp_erp_mfq_job LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_warehousemaster.WIPGLAutoID = srp_erp_chartofaccounts.GLAutoID) wh", "wh.workProcessID=srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
        $materialWIPGL = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
        if($materialWIPGL) {
            foreach ($materialWIPGL as $val) {            
                $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'];
                $data_arr['segment'] = $master['segmentCode'];
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'];
                $data_arr['partySystemCode'] = $master['customerSystemCode'];
                $data_arr['partyName'] = $master['customerName'];
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
                $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
                $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['materialCharge'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['materialCharge'];
                array_push($globalArray, $data_arr);
            }
        }

        if($policyJEC &&  $policyJEC == 1) {
            if($chk_category["mainCategory"] == "Inventory") {
                $itemcategory = 'Inventory';
            } else {
                $itemcategory = 'Service';
            }
            $gldetails = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                    FROM srp_erp_mfq_postingconfiguration JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_postingconfiguration.value
                    WHERE configurationCode = '$itemcategory' AND srp_erp_mfq_postingconfiguration.companyID = {$companyID}")->row_array();

            $data_arr['gl_auto_id'] = $gldetails['GLAutoID'];
            $data_arr['gl_code'] = $gldetails['systemAccountCode'];
            $data_arr['secondary'] = $gldetails['GLSecondaryCode'];
            $data_arr['gl_desc'] = $gldetails['GLDescription'];
            $data_arr['gl_type'] = $gldetails['subCategory'];
        } else {
            $data_arr['gl_auto_id'] = $master['GLAutoID'];
            $data_arr['gl_code'] = $master['systemAccountCode'];
            $data_arr['secondary'] = $master['GLSecondaryCode'];
            $data_arr['gl_desc'] = $master['GLDescription'];
            $data_arr['gl_type'] = $master['subCategory'];
        }

        $data_arr['auto_id'] = $master['workProcessID'];
       
        $data_arr['segment_id'] = $master['segmentID'];
        $data_arr['segment'] = $master['segmentCode'];
        $data_arr['projectID'] = NULL;
        $data_arr['projectExchangeRate'] = NULL;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = '';
        $data_arr['partyAutoID'] = $master['customerAutoID'];
        $data_arr['partySystemCode'] = $master['customerSystemCode'];
        $data_arr['partyName'] = $master['customerName'];
        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'];
        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'];
        $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'];
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'];
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'];
        $data_arr['partyExchangeRate'] = 1;
        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'];
        $data_arr['partyCurrencyAmount'] = ($total / $data_arr['partyExchangeRate']);
        $data_arr['gl_dr'] = $total;
        $data_arr['gl_cr'] = '';
        $data_arr['amount_type'] = 'dr';
        array_push($globalArray, $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'];
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'];
        $gl_array['code'] = 'JOB';
        $gl_array['name'] = 'Job';
        $gl_array['primary_Code'] = $master['documentCode'];
        $gl_array['master_data'] = $master;
        $gl_array['date'] = $master['documentDate'];
        $gl_array['gl_detail'] = $globalArray;
        $gl_array['total'] = $total;
        return $gl_array;
    }

    function fetch_job_details()
    {
        $data = array();
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();

        $result = $this->db->query("SELECT
	documentCode,
	poNumber,
	workProcessID,
	DATE_FORMAT( srp_erp_mfq_job.documentDate, '{$convertFormat}' ) AS documentDate,
	(
	DATE_FORMAT( expectedDeliveryDate, '{$convertFormat}' )) AS expectedDeliveryDate,
	IFNULL( DATE_FORMAT( estimate.deliveryDate, '{$convertFormat}' ), ' - ' ) AS deliveryDate,
	IFNULL( DATE_FORMAT( srp_erp_mfq_deliverynote.deliveryDate, '{$convertFormat}' ), ' - ' ) AS actualDeliveryDate,
	srp_erp_mfq_job.description,
	templateDescription,
	ROUND(job2.percentage, 2) AS percentage,
	CONCAT( itemSystemCode, ' - ', itemDescription ) AS itemDescription,
	srp_erp_mfq_job.approvedYN,
	srp_erp_mfq_job.confirmedYN,
	isFromEstimate,
	cust.CustomerName AS CustomerName,
	srp_erp_mfq_job.estimateMasterID AS estimateMasterID,
	srp_erp_mfq_job.linkedJobID AS linkedJobID,
	srp_erp_mfq_job.isDeleted AS isDeleted 
FROM
	`srp_erp_mfq_job`
	LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `srp_erp_mfq_job`.`mfqCustomerAutoID`
	LEFT JOIN `srp_erp_mfq_estimatemaster` `estimate` ON `estimate`.`estimateMasterID` = `srp_erp_mfq_job`.`estimateMasterID`
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
	srp_erp_mfq_job.companyID = {$companyID} 
	AND ( `srp_erp_mfq_job`.`linkedJobID` = 0 OR `srp_erp_mfq_job`.`linkedJobID` = '' OR `srp_erp_mfq_job`.`linkedJobID` IS NULL ) 
ORDER BY workProcessID DESC")->result_array();
        if($result) {
            $a = 1;
            foreach ($result AS $val) {
                $det['recordNo'] = $a;
                $det['documentCode'] = $val['documentCode'];
                $det['documentDate'] = $val['documentDate'];
                $det['expectedDeliveryDate'] = $val['expectedDeliveryDate'];
                $det['CustomerName'] = $val['CustomerName'];
                $det['poNumber'] = $val['poNumber'];
                $det['description'] = $val['description'];
                $det['jobstatus'] = strip_tags(load_main_job_status($val['workProcessID']));
                $det['status'] = '';
                $det['percentage'] = $val['percentage'];

                $a++;
                array_push($data, $det);
            }
        }
        return $data;
    }

    function save_document_setup()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $docSetupID = $this->input->post('docSetupID');
        $description = $this->input->post('description');
        $IsActive = $this->input->post('IsActive');
        $isMandatory = $this->input->post('isMandatory');

        $data['documentID'] = 'JOB';
        $data['description'] = $description;
        $data['isMandatory'] = $isMandatory;
        $data['isActive'] = $IsActive;

        if(!empty($docSetupID)) {
            $validateDsc = $this->db->query("SELECT COUNT(docSetupID) as docSetupID FROM srp_erp_mfq_documentsetup WHERE docSetupID != {$docSetupID} AND companyID = {$companyID} AND description = '{$description}'")->row('docSetupID');
            if (!empty($validateDsc)) {
                $this->session->set_flashdata('w', 'Description Already Exist!');
                $this->db->trans_rollback();
                return array('status' => false);
            }

            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = current_date();
            $data['modifiedPCID'] = current_pc();
            $this->db->where('docSetupID', $docSetupID);
            $this->db->update('srp_erp_mfq_documentsetup', $data);
        } else {
            $validateDsc = $this->db->query("SELECT COUNT(docSetupID) as docSetupID FROM srp_erp_mfq_documentsetup WHERE companyID = {$companyID} AND description = '{$description}'")->row('docSetupID');
            if (!empty($validateDsc)) {
                $this->session->set_flashdata('w', 'Description Already Exist!');
                $this->db->trans_rollback();
                return array('status' => false);
            }

            $data['companyID'] = $companyID;
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = current_date();
            $data['createdPCID'] = current_pc();
            $this->db->insert('srp_erp_mfq_documentsetup', $data);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Saved Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Document Setup Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function load_document_setup()
    {
        $docSetupID = $this->input->post('docSetupID');
        $companyID = current_companyID();
        $data =  $this->db->query("SELECT docSetupID, description, isMandatory, isActive FROM srp_erp_mfq_documentsetup WHERE docSetupID = {$docSetupID} AND companyID = {$companyID}")->row_array();
        return $data;
    }

    function delete_document_setup()
    {
        $docSetupID = $this->input->post('docSetupID');
        $this->db->delete('srp_erp_mfq_documentsetup', array('docSetupID' => $docSetupID));
        return array('s', 'Document Setup Deleted Successfully!');
    }

    function itemLedger_update_DN($deliverNoteID)
    {
        $this->db->trans_start();
        $this->db->select('srp_erp_mfq_job.*,deliveredQty, customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*, IFNULL(ledger.companyLocalAmount/qty, 0) AS localWacAmount, IFNULL( ledger.companyReportingAmount/qty, 0 ) AS reportingWacAmount');
        $this->db->where('deliveryNoteID', $deliverNoteID);
        $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
        $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
        $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
                    srp_erp_itemmaster.itemSystemCode, srp_erp_itemmaster.itemDescription, srp_erp_itemmaster.defaultUnitOfMeasureID, srp_erp_itemmaster.defaultUnitOfMeasure, srp_erp_itemmaster.currentStock, srp_erp_itemmaster.mainCategory, srp_erp_itemmaster.costGLAutoID, srp_erp_itemmaster.costSystemGLCode, srp_erp_itemmaster.costGLCode, srp_erp_itemmaster.costDescription,
                    srp_erp_itemmaster.costType, srp_erp_itemmaster.assteGLAutoID, srp_erp_itemmaster.assteSystemGLCode, srp_erp_itemmaster.assteGLCode, srp_erp_itemmaster.assteDescription, srp_erp_itemmaster.assteType
                    FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
        $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
        $this->db->join('(SELECT SUM(companyReportingAmount) AS companyReportingAmount, SUM(companyLocalAmount) AS companyLocalAmount, documentSystemCode, documentAutoID, itemAutoID FROM srp_erp_itemledger WHERE documentCode = "JOB" GROUP BY documentAutoID, itemAutoID) ledger', "ledger.documentAutoID = srp_erp_mfq_job.workProcessID AND ledger.itemAutoID = itm.itemAutoID", "left");
        $this->db->join('srp_erp_mfq_deliverynotedetail', "srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID", "inner");
        $master = $this->db->get('srp_erp_mfq_job')->result_array();

        $item_arr2 = array();
        $itemledger_arr2 = array();
        if($master) {
            foreach($master as $mas) {
                if ($mas['mainCategory'] == 'Inventory') {
                    $itemAutoID = $mas['itemAutoID'];
                    $qty = $mas['qty'] / 1;
                    $wareHouseAutoID = $mas['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    $item_arr['itemAutoID'] = $mas['itemAutoID'];
                    $item_arr['currentStock'] = ($mas['currentStock'] - $qty);
    
                    $itemledger_arr['documentID'] = $mas['documentID'];
                    $itemledger_arr['documentCode'] = $mas['documentID'];
                    $itemledger_arr['documentAutoID'] = $mas['workProcessID'];
                    $itemledger_arr['documentSystemCode'] = $mas['documentCode'];
                    $itemledger_arr['documentDate'] = $mas['closedDate'];
                    $itemledger_arr['referenceNumber'] = null;
                    $itemledger_arr['companyFinanceYearID'] = $mas['companyFinanceYearID'];
                    $itemledger_arr['companyFinanceYear'] = $mas['companyFinanceYear'];
                    $itemledger_arr['FYBegin'] = $mas['FYBegin'];
                    $itemledger_arr['FYEnd'] = $mas['FYEnd'];
                    $itemledger_arr['FYPeriodDateFrom'] = $mas['FYPeriodDateFrom'];
                    $itemledger_arr['FYPeriodDateTo'] = $mas['FYPeriodDateTo'];
                    $itemledger_arr['wareHouseAutoID'] = $mas['wareHouseAutoID'];
                    $itemledger_arr['wareHouseCode'] = $mas['wareHouseCode'];
                    $itemledger_arr['wareHouseLocation'] = $mas['wareHouseLocation'];
                    $itemledger_arr['wareHouseDescription'] = $mas['wareHouseDescription'];
                    $itemledger_arr['itemAutoID'] = $mas['itemAutoID'];
                    $itemledger_arr['itemSystemCode'] = $mas['itemSystemCode'];
                    $itemledger_arr['itemDescription'] = $mas['itemDescription'];
                    $itemledger_arr['defaultUOMID'] = $mas['defaultUnitOfMeasureID'];
                    $itemledger_arr['defaultUOM'] = $mas['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOM'] = $mas['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOMID'] = $mas['defaultUnitOfMeasureID'];
                    $itemledger_arr['transactionQTY'] = $mas['qty'];
                    $itemledger_arr['convertionRate'] = 1;
                    $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                    $itemledger_arr['PLGLAutoID'] = $mas['costGLAutoID'];
                    $itemledger_arr['PLSystemGLCode'] = $mas['costSystemGLCode'];
                    $itemledger_arr['PLGLCode'] = $mas['costGLCode'];
                    $itemledger_arr['PLDescription'] = $mas['costDescription'];
                    $itemledger_arr['PLType'] = $mas['costType'];
                    $itemledger_arr['BLGLAutoID'] = $mas['assteGLAutoID'];
                    $itemledger_arr['BLSystemGLCode'] = $mas['assteSystemGLCode'];
                    $itemledger_arr['BLGLCode'] = $mas['assteGLCode'];
                    $itemledger_arr['BLDescription'] = $mas['assteDescription'];
                    $itemledger_arr['BLType'] = $mas['assteType'];
                    $itemledger_arr['transactionAmount'] = $mas['localWacAmount'] * $mas['deliveredQty'];
                    $itemledger_arr['transactionCurrencyID'] = $mas['transactionCurrencyID'];
                    $itemledger_arr['transactionCurrency'] = $mas['transactionCurrency'];
                    $itemledger_arr['transactionExchangeRate'] = $mas['transactionExchangeRate'];
                    $itemledger_arr['transactionCurrencyDecimalPlaces'] = $mas['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalCurrencyID'] = $mas['companyLocalCurrencyID'];
                    $itemledger_arr['companyLocalCurrency'] = $mas['companyLocalCurrency'];
                    $itemledger_arr['companyLocalExchangeRate'] = $mas['companyLocalExchangeRate'];
                    $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $mas['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr['companyLocalWacAmount'] = $mas['localWacAmount'];
                    $itemledger_arr['companyReportingCurrencyID'] = $mas['companyReportingCurrencyID'];
                    $itemledger_arr['companyReportingCurrency'] = $mas['companyReportingCurrency'];
                    $itemledger_arr['companyReportingExchangeRate'] = $mas['companyReportingExchangeRate'];
                    $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $mas['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr['companyReportingWacAmount'] = $mas['reportingWacAmount'];
                    $itemledger_arr['partyCurrencyID'] = $mas['mfqCustomerCurrencyID'];
                    $itemledger_arr['partyCurrency'] = $mas['mfqCustomerCurrency'];
                    $itemledger_arr['partyCurrencyExchangeRate'] = 1;
                    $itemledger_arr['partyCurrencyDecimalPlaces'] = $mas['mfqCustomerCurrencyDecimalPlaces'];
                    $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
                    $itemledger_arr['confirmedYN'] = $mas['confirmedYN'];
                    $itemledger_arr['confirmedByEmpID'] = $mas['confirmedByEmpID'];
                    $itemledger_arr['confirmedByName'] = $mas['confirmedByName'];
                    $itemledger_arr['confirmedDate'] = $mas['confirmedDate'];
                    $itemledger_arr['segmentID'] = $mas['segmentID'];
                    $itemledger_arr['segmentCode'] = $mas['segmentCode'];
                    $itemledger_arr['companyID'] = $mas['companyID'];
                    $itemledger_arr['createdUserGroup'] = $mas['createdUserGroup'];
                    $itemledger_arr['createdPCID'] = $mas['createdPCID'];
                    $itemledger_arr['createdUserID'] = $mas['createdUserID'];
                    $itemledger_arr['createdDateTime'] = $mas['createdDateTime'];
                    $itemledger_arr['createdUserName'] = $mas['createdUserName'];
                    $itemledger_arr['modifiedPCID'] = $mas['modifiedPCID'];
                    $itemledger_arr['modifiedUserID'] = $mas['modifiedUserID'];
                    $itemledger_arr['modifiedDateTime'] = $mas['modifiedDateTime'];
                    $itemledger_arr['modifiedUserName'] = $mas['modifiedUserName'];
                    if (!empty($item_arr)) {
                        $item_arr2[] = $item_arr;
                    }
                    if (!empty($itemledger_arr)) {
                        $itemledger_arr2[] = $itemledger_arr;
                    }
                }
            }
            if (!empty($item_arr2)) {
                $this->db->update_batch('srp_erp_itemmaster', $item_arr2, 'itemAutoID');
            }
            if (!empty($itemledger_arr2)) {
                $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr2);
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 'Saved Failed';
        } else {
            $this->db->trans_commit();
            return 'Ledger Updated Successfully!';
        }
    }

    function usage_qty_update($jobID)
    {
        $this->db->trans_start();
        $itemIDs = array();
        $this->db->where('jobID', $jobID);
        $this->db->where('status', 0);
        $this->db->order_by('workProcessFlowID', 'asc');
        $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

        if(empty($templateDetailID)) {
            $this->db->where('jobID', $jobID);
            $this->db->order_by('workProcessFlowID', 'desc');
            $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
        }

        $this->db->select("DISTINCT(srp_erp_mfq_itemmaster.itemAutoID) AS itemAutoID,srp_erp_mfq_jc_materialconsumption.workProcessID,srp_erp_mfq_jc_materialconsumption.materialCost AS materialCost,jcMaterialConsumptionID,CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,IFNULL(wh.currentStock,0) as currentStock,srp_erp_mfq_jc_materialconsumption.qtyUsed,usageQty,srp_erp_mfq_jc_materialconsumption.jobCardID,srp_erp_mfq_jc_materialconsumption.mfqItemID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $jobID);
        $this->db->where('srp_erp_mfq_itemmaster.itemAutoID IS NOT NULL');
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_warehousemaster', "srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID", 'inner');
        $this->db->join('srp_erp_mfq_jc_materialconsumption', "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'inner');
        $this->db->join('(SELECT SUM(currentStock) as currentStock,wareHouseAutoID,itemAutoID FROM srp_erp_warehouseitems GROUP BY wareHouseAutoID,itemAutoID) wh', "wh.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID AND srp_erp_mfq_itemmaster.itemAutoID = wh.itemAutoID", 'left');
        $data = $this->db->get()->result_array();

        $updateQty = array();
        if($data){
            foreach ($data AS $item) {
                $updateDetQty = '';
                $itemID = $item['itemAutoID'];
                $updateDetQty = $this->db->query("SELECT * FROM
                        (
                            SELECT itemAutoID, jobID, srp_erp_stocktransferdetails.stockTransferAutoID AS documentAutoID, 'ST' AS documentID, transfer_QTY AS Qty, (totalValue/srp_erp_stocktransfermaster.companyLocalExchangeRate) AS totalValue
                            FROM srp_erp_stocktransferdetails
                                JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = srp_erp_stocktransferdetails.stockTransferAutoID
                                WHERE approvedYN = 1
                            GROUP BY srp_erp_stocktransferdetails.stockTransferAutoID, itemAutoID UNION ALL
                            SELECT itemAutoID, jobID, srp_erp_grvdetails.grvAutoID AS documentAutoID, 'GRV' AS documentID, SUM( receivedQty ) AS Qty, (fullTotalAmount/srp_erp_grvmaster.companyLocalExchangeRate) AS totalValue
                            FROM srp_erp_grvdetails
                                JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = srp_erp_grvdetails.grvAutoID 
                                WHERE approvedYN = 1
                            GROUP BY srp_erp_grvdetails.grvAutoID, itemAutoID UNION ALL
                            SELECT itemAutoID, jobID, srp_erp_materialreceiptdetails.mrnAutoID AS documentAutoID, 'MRN' AS documentID, SUM( qtyReceived ) AS Qty, (totalValue/srp_erp_materialreceiptmaster.companyLocalExchangeRate) AS totalValue
                            FROM srp_erp_materialreceiptdetails
                                JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptmaster.mrnAutoID = srp_erp_materialreceiptdetails.mrnAutoID 
                                WHERE approvedYN = 1
                            GROUP BY srp_erp_materialreceiptdetails.mrnAutoID, itemAutoID 
                        ) tbl WHERE jobID = {$jobID} AND itemAutoID = {$itemID}")->result_array();

                if($updateDetQty) {
                    foreach($updateDetQty as $updateQty) {
                        $updateQtyUsed = null;
                        $qtyUsage = $updateQty['Qty'];
                        $this->db->set('jobID', $jobID);
                        $this->db->set('jobDetailID', $item['jcMaterialConsumptionID']);
                        $this->db->set('jobCardID', $item['jobCardID']);
                        $this->db->set('typeMasterAutoID', $item['typeMasterAutoID']);
                        $this->db->set('linkedDocumentID', $updateQty['documentID']);
                        $this->db->set('linkedDocumentAutoID', $updateQty['documentAutoID']);
                        $this->db->set('usageAmount', $qtyUsage);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $this->db->set('typeID', 1);
                        $this->db->insert('srp_erp_mfq_jc_usage');
    
                        $this->db->where('jobID', $jobID);
                        $this->db->where('typeID', 1);
                        $this->db->where('jobDetailID', $item['jcMaterialConsumptionID']);
                        $this->db->SELECT('SUM(usageAmount) as usageAmount');
                        $this->db->FROM('srp_erp_mfq_jc_usage');
                        $updateQtyUsed = $this->db->get()->row('usageAmount');
    
                        if($updateQtyUsed) {
                            $jcMaterialConsumptionID = $item['jcMaterialConsumptionID'];
                        $costrecalculate = $this->db->query("SELECT materialCost FROM srp_erp_mfq_jc_materialconsumption WHERE jcMaterialConsumptionID = {$jcMaterialConsumptionID}")->row('materialCost');
                            // $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET usageQty = {$updateQtyUsed},materialCost = unitCost * {$updateQtyUsed},materialCharge = (unitCost * {$updateQtyUsed})+((unitCost * {$updateQtyUsed})*(markUp/100))  WHERE jcMaterialConsumptionID= {$item['jcMaterialConsumptionID']}");
                            $materialCost = $costrecalculate + $updateQty['totalValue'];
                            $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET 
                                    usageQty = {$updateQtyUsed},
                                    materialCost = {$materialCost},
                                    unitCost = ({$materialCost} / {$updateQtyUsed}),
                                    materialCharge = ({$materialCost})+(({$materialCost})*(markUp/100))
                                WHERE jcMaterialConsumptionID= {$jcMaterialConsumptionID}");
                        }
                    }
                }
            }
            $itemIDs = array_column($data, 'itemAutoID');
        }

        $createItem = array();
        $where = "";
        if(!empty($itemIDs)) 
        {
            $where = " AND itemAutoID NOT IN (" . join(',', $itemIDs) . ")";
        }
        $createItem = $this->db->query("SELECT * FROM
                        (
                            SELECT det.itemAutoID, mfqItemID, jobID, det.stockTransferAutoID AS documentAutoID, 'ST' AS documentID, transfer_QTY AS Qty, (totalValue/srp_erp_stocktransfermaster.companyLocalExchangeRate) AS totalValue 
                            FROM srp_erp_stocktransferdetails det
                                JOIN srp_erp_stocktransfermaster ON srp_erp_stocktransfermaster.stockTransferAutoID = det.stockTransferAutoID 
                                LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID
                                WHERE approvedYN = 1
                            GROUP BY det.stockTransferAutoID, det.itemAutoID UNION ALL
                            SELECT det.itemAutoID, mfqItemID, jobID, det.grvAutoID AS documentAutoID, 'GRV' AS documentID, receivedQty AS Qty, (fullTotalAmount/srp_erp_grvmaster.companyLocalExchangeRate) AS totalValue
                            FROM srp_erp_grvdetails det
                                JOIN srp_erp_grvmaster ON srp_erp_grvmaster.grvAutoID = det.grvAutoID
                                LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID 
                                WHERE approvedYN = 1
                            GROUP BY det.grvAutoID, det.itemAutoID UNION ALL
                            SELECT det.itemAutoID, mfqItemID, jobID, det.mrnAutoID AS documentAutoID, 'MRN' AS documentID, qtyReceived AS Qty, (totalValue/srp_erp_materialreceiptmaster.companyLocalExchangeRate) AS totalValue
                            FROM srp_erp_materialreceiptdetails det
                                JOIN srp_erp_materialreceiptmaster ON srp_erp_materialreceiptmaster.mrnAutoID = det.mrnAutoID 
                                LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = det.itemAutoID
                                WHERE approvedYN = 1
                            GROUP BY det.mrnAutoID, det.itemAutoID 
                        ) tbl WHERE jobID = {$jobID} {$where}")->result_array();

        if(!empty($createItem))
        {
            $this->db->where('jobID', $jobID);
            // $this->db->where('status', 1);
            $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jobcardmaster.templateDetailID = srp_erp_mfq_workflowstatus.templateDetailID  AND srp_erp_mfq_jobcardmaster.workProcessID = srp_erp_mfq_workflowstatus.jobID', 'INNER');
            $this->db->order_by('workProcessFlowID', 'desc');
            $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

            // if(empty($templateDetailID)) {
            //     $this->db->where('jobID', $jobID);
            //     $this->db->where('status', 0);
            //     $this->db->order_by('workProcessFlowID', 'asc');
            //     $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
            // }

            $this->db->select("jobcardID AS jobCardID");
            $this->db->from("srp_erp_mfq_jobcardmaster");
            $this->db->where('templateDetailID', $templateDetailID);
            $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $jobID);
            $jobCardID = $this->db->get()->row('jobCardID');

            if($jobCardID) {
                foreach($createItem as $val) {
                    $itemAutoID = $val['itemAutoID'];
                    if(empty($val['mfqItemID']))
                    {
                        $itemExist = '';
                        $itemExist = $this->db->query("SELECT mfqItemID FROM srp_erp_mfq_itemmaster WHERE itemAutoID = {$itemAutoID}")->row('mfqItemID');
                        if($itemExist) {
                            $val['mfqItemID'] = $itemExist;
                        } else {
                            $result = $this->db->query('INSERT INTO srp_erp_mfq_itemmaster (
                                            itemAutoID, categoryType, itemSystemCode, secondaryItemCode, itemImage,
                                            itemName, itemDescription, mainCategoryID,mainCategory,subcategoryID, subSubCategoryID, 
                                            itemUrl, barcode, financeCategory, partNo,
                                            defaultUnitOfMeasureID, defaultUnitOfMeasure, currentStock, reorderPoint,
                                            maximunQty, minimumQty, revenueGLAutoID, revenueSystemGLCode, revenueGLCode,
                                            revenueDescription, revenueType, costGLAutoID, costSystemGLCode, costGLCode,
                                            costDescription, costType, assetGLAutoID, assetSystemGLCode, assetGLCode, assetDescription,
                                            assetType, faCostGLAutoID, faACCDEPGLAutoID, faDEPGLAutoID, faDISPOGLAutoID,
                                            isActive, comments, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate,
                                            companyLocalSellingPrice, companyLocalWacAmount, companyLocalCurrencyDecimalPlaces,
                                            companyReportingCurrencyID, companyReportingCurrency, companyID, companyCode
                                        ) SELECT
                                        
                                        itemAutoID, IF ( mainCategory = "Inventory" OR mainCategoryID = "Non Inventory", 1,
                                        IF ( mainCategory = "Service", 2, NULL ) ) AS categoryType,
                                        itemSystemCode, seconeryItemCode, itemImage, itemName,
                                        itemDescription, mainCategoryID,mainCategory,subcategoryID, subSubCategoryID, 
                                        itemUrl, barcode, financeCategory,
                                        partNo, defaultUnitOfMeasureID, defaultUnitOfMeasure,
                                        currentStock, reorderPoint, maximunQty, minimumQty,
                                        revanueGLAutoID, revanueSystemGLCode, revanueGLCode,
                                        revanueDescription, revanueType, costGLAutoID, costSystemGLCode, costGLCode,
                                        costDescription, costType, assteGLAutoID, assteSystemGLCode,
                                        assteGLCode, assteDescription, assteType, faCostGLAutoID, faACCDEPGLAutoID,
                                        faDEPGLAutoID, faDISPOGLAutoID, isActive, comments, companyLocalCurrencyID,
                                        companyLocalCurrency, companyLocalExchangeRate, companyLocalSellingPrice,
                                        companyLocalWacAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID,
                                        companyReportingCurrency, companyID, companyCode
                                        FROM
                                    srp_erp_itemmaster WHERE companyID = ' . $this->common_data['company_data']['company_id'] . ' AND itemAutoID = ' . $itemAutoID);
                        
                            $val['mfqItemID'] = $this->db->insert_id();
                        }
                    }

                    $mfqItemID = $val['mfqItemID'];
                    $materialAdded = $this->db->query("SELECT jcMaterialConsumptionID, usageQty, unitCost, markUp, materialCost FROM srp_erp_mfq_jc_materialconsumption WHERE workProcessID = {$jobID} AND jobCardID = {$jobCardID} AND mfqItemID = {$mfqItemID}")->row_array();

                    if($materialAdded) {
                        $jcMaterialID = $materialAdded['jcMaterialConsumptionID'];
                        $jcMaterialUpdate['usageQty'] = $materialAdded['usageQty'] + $val['Qty'];
                        $jcMaterialUpdate['materialCost'] = $materialAdded['materialCost'] + $val['totalValue'];
                        $jcMaterialUpdate['unitCost'] = ($materialAdded['materialCost'] + $val['totalValue']) / $jcMaterialUpdate['usageQty'];
                        $jcMaterialUpdate['materialCharge'] = ($jcMaterialUpdate['materialCost']) + ($jcMaterialUpdate['materialCost'] * ($materialAdded['markUp']/100));
                        $this->db->where('workProcessID', $jobID);
                        $this->db->where('jobCardID', $jobCardID);
                        $this->db->where('mfqItemID', $mfqItemID);
                        $this->db->where('jcMaterialConsumptionID', $jcMaterialID);
                        $this->db->update('srp_erp_mfq_jc_materialconsumption', $jcMaterialUpdate);
        
                    } else {
                        $this->db->set('mfqItemID', $val['mfqItemID']);
                        $this->db->set('qtyUsed', $val['Qty']);
                        $this->db->set('usageQty', $val['Qty']);
                        $this->db->set('unitCost', ($val['totalValue'] / $val['Qty']));
                        $this->db->set('materialCost', $val['totalValue']);
                        $this->db->set('markUp', 0);
                        $this->db->set('materialCharge', $val['totalValue']);
                        $this->db->set('jobCardID', $jobCardID);    
                        $this->db->set('workProcessID', $jobID);
                        $this->db->set('companyID', current_companyID());
                        $this->db->set('transactionCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('transactionCurrency', $this->common_data['company_data']['company_default_currency']);
                        $this->db->set('transactionExchangeRate', 1);
                        $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($this->common_data['company_data']['company_default_currencyID']));
                        $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                        $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_default_currencyID']);
                        $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                        $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                        $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                        $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                        $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                        $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                        $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);

                        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdUserName', current_user());
                        $this->db->set('createdDateTime', current_date(true));
                        $this->db->insert('srp_erp_mfq_jc_materialconsumption');
                        $jcMaterialID = $this->db->insert_id();
                    }
    
                    $this->db->set('jobID', $jobID);
                    $this->db->set('jobDetailID', $jcMaterialID);
                    $this->db->set('jobCardID', $jobCardID);
                    $this->db->set('typeMasterAutoID', $val['mfqItemID']);
                    $this->db->set('linkedDocumentID', $val['documentID']);
                    $this->db->set('linkedDocumentAutoID', $val['documentAutoID']);
                    $this->db->set('usageAmount', $val['Qty']);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 1);
                    $this->db->insert('srp_erp_mfq_jc_usage');                    
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 'Saved Failed';
        } else {
            $this->db->trans_commit();
            return 'Usage Qty Updated Successfully!';
        }
    }

    function job_close_ledger_entries($jobID)
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $this->db->where('jobID', $jobID);
        $this->db->where('status', 0);
        $this->db->order_by('workProcessFlowID', 'asc');
        $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');

        if(empty($templateDetailID)) {
            $this->db->where('jobID', $jobID);
            $this->db->order_by('workProcessFlowID', 'desc');
            $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->row('templateDetailID');
        }

        $this->db->select("jobcardID AS jobCardID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $jobID);
        $jobcardID = $this->db->get()->row('jobCardID');
        
        $double_entry = $this->fetch_double_entry_job_test($jobID, $jobcardID);

        for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
            $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['workProcessID'];
            $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
            $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
            $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['closedDate'];
            $generalledger_arr[$i]['documentYear'] = date("Y", strtotime($double_entry['master_data']['closedDate']));
            $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['closedDate']));
            $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['description'];
            $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
            $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
            $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
            $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
            $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
            $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
            $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
            $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
            $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
            $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
            $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
            $generalledger_arr[$i]['partyExchangeRate'] = 1;
            $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
            $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
            $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
            $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
            $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
            $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
            $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
            $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                //$generalledger_arr[$i]['companyCode'] = $double_entry['master_data']['companyCode'];
            $amount = $double_entry['gl_detail'][$i]['gl_dr'];
            if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
            }
            $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
            $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
            $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
            $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
            $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
            $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
            $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
            $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
            $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
            $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
            $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
            $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
            $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
            $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
            $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
            $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
            $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
            $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
            $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
            $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
            $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
            $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];
        }

        if (!empty($generalledger_arr)) {
            //$this->db->insert_batch('srp_erp_mfq_generalledger', $generalledger_arr);
            $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
        }

        $this->db->select('unitCost AS unitCost, srp_erp_mfq_jc_materialconsumption.mfqItemID AS mfqItemID, materialCharge as materialCharge,qtyUsed, usageQty, itm.*');
        $this->db->where('workProcessID', $jobID);
        $this->db->where('jobCardID', $jobcardID);
        $this->db->join('(SELECT srp_erp_itemmaster.*,mfqItemID FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID) itm', 'itm.mfqItemID=srp_erp_mfq_jc_materialconsumption.mfqItemID', 'LEFT');
        $materialConsumption = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 1);
        $materialGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();

        for ($a = 0; $a < count($materialConsumption); $a++) {
            if ($materialConsumption[$a]['mainCategory'] == 'Inventory') {
                $itemAutoID = $materialConsumption[$a]['mfqItemID'];
                if($materialConsumption[$a]['mfqItemID'] == 2782) {
                    $qty = $materialConsumption[$a]['usageQty'] / 1;
                    $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                    $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                    $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                    
                    $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                    $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                    $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                    $itemledger_arr[$a]['referenceNumber'] = null;
                    $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                    $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                    $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                    $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                    $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                    $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                    $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                    $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                    $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                    $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                    $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                    $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                    $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                    $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                    $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                    $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                    $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                    $itemledger_arr[$a]['convertionRate'] = 1;
                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                    $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                    $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                    $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                    $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                    $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                    $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                    $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                    $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                    $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                    $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                    $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                    $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                    $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                    $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                    $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                    $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    /* $itemledger_arr[$a]['approvedYN'] =  $double_entry['master_data']['approvedYN'];
                        $itemledger_arr[$a]['approvedDate'] =  $double_entry['master_data']['approvedDate'];
                        $itemledger_arr[$a]['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
                        $itemledger_arr[$a]['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
                    $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                    $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                    $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                    /*$itemledger_arr[$a]['companyCode'] =  $double_entry['master_data']['companyCode'];*/
                    $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                    $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                    $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                    $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                    $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                    $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                    $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                    $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                    $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                } else {
                    if($materialGLSetup) {
                        if($materialGLSetup['isEntryEnabled'] == 1) {
                            //if($materialGLSetup['linkedDocEntry'] == 1) {
                                /*Linked Document Qty*/
                                $materialLinkedQty = $this->db->Query("SELECT SUM(usageAmount) as qty FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL AND jobID = {$jobID} AND typeMasterAutoID = {$itemAutoID}")->row('qty');

                                if(!$materialLinkedQty) { $materialLinkedQty = 0; }
                                if($materialGLSetup['manualEntry'] == 0 && $materialGLSetup['linkedDocEntry'] == 1){
                                    $materialConsumption[$a]['materialCharge'] = $materialLinkedQty * $materialConsumption[$a]['unitCost'];
                                    $materialConsumption[$a]['usageQty'] = $materialLinkedQty;
                                } else if($materialGLSetup['manualEntry'] == 1 && $materialGLSetup['linkedDocEntry'] == 0){
                                    $materialConsumption[$a]['materialCharge'] = ($materialConsumption[$a]['usageQty'] - $materialLinkedQty) * $materialConsumption[$a]['unitCost'];
                                    $materialConsumption[$a]['usageQty'] = $materialConsumption[$a]['usageQty'] - $materialLinkedQty;
                                }
                            //}

                            $qty = $materialConsumption[$a]['usageQty'] / 1;
                            $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                            /*$item_arr[$a]['companyLocalWacAmount'] = round(((($materialConsumption[$a]['currentStock'] * $materialConsumption[$a]['companyLocalWacAmount']) + $materialConsumption[$a]['materialCharge']) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                            $item_arr[$a]['companyReportingWacAmount'] = round(((($item_arr[$a]['currentStock'] * $materialConsumption[$a]['companyReportingWacAmount']) + ($materialConsumption[$a]['materialCharge'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr[$a]['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);*/

                            $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                            $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                            $itemledger_arr[$a]['referenceNumber'] = null;
                            $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                            $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                            $itemledger_arr[$a]['convertionRate'] = 1;
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                            $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                            $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                            $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                            /* $itemledger_arr[$a]['approvedYN'] =  $double_entry['master_data']['approvedYN'];
                             $itemledger_arr[$a]['approvedDate'] =  $double_entry['master_data']['approvedDate'];
                             $itemledger_arr[$a]['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
                             $itemledger_arr[$a]['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
                            $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                            /*$itemledger_arr[$a]['companyCode'] =  $double_entry['master_data']['companyCode'];*/
                            $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                        }
                    }
                }
            }
        }

        if (!empty($item_arr)) {
            //$item_arr = array_values($item_arr);
            $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
        }

        if (!empty($itemledger_arr)) {
            //$itemledger_arr = array_values($itemledger_arr);
            $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
        }

        $itemledger_arr = array();
        $item_arr = array();
        if ($double_entry['master_data']['mainCategory'] == 'Inventory' or $double_entry['master_data']['mainCategory'] == 'Non Inventory') {
            $itemAutoID = $double_entry['master_data']['itemAutoID'];
            $qty = $double_entry['master_data']['qty'] / 1;
            $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock + {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
            $item_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
            $item_arr['currentStock'] = ($double_entry['master_data']['currentStock'] + $qty);
            $item_arr['companyLocalWacAmount'] = round(((($double_entry['master_data']['currentStock'] * $double_entry['master_data']['companyLocalWacAmount']) + $double_entry['total']) / $item_arr['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
            $item_arr['companyReportingWacAmount'] = round(((($item_arr['currentStock'] * $double_entry['master_data']['companyReportingWacAmount']) + ($double_entry['total'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);

            $itemledger_arr['documentID'] = $double_entry['master_data']['documentID'];
            $itemledger_arr['documentCode'] = $double_entry['master_data']['documentID'];
            $itemledger_arr['documentAutoID'] = $double_entry['master_data']['workProcessID'];
            $itemledger_arr['documentSystemCode'] = $double_entry['master_data']['documentCode'];
            $itemledger_arr['documentDate'] = $double_entry['master_data']['closedDate'];
            $itemledger_arr['referenceNumber'] = null;
            $itemledger_arr['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
            $itemledger_arr['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
            $itemledger_arr['FYBegin'] = $double_entry['master_data']['FYBegin'];
            $itemledger_arr['FYEnd'] = $double_entry['master_data']['FYEnd'];
            $itemledger_arr['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
            $itemledger_arr['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
            $itemledger_arr['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
            $itemledger_arr['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
            $itemledger_arr['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
            $itemledger_arr['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
            $itemledger_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
            $itemledger_arr['itemSystemCode'] = $double_entry['master_data']['itemSystemCode'];
            $itemledger_arr['itemDescription'] = $double_entry['master_data']['itemDescription'];
            $itemledger_arr['defaultUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
            $itemledger_arr['defaultUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
            $itemledger_arr['transactionUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
            $itemledger_arr['transactionUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
            $itemledger_arr['transactionQTY'] = $double_entry['master_data']['qty'];
            $itemledger_arr['convertionRate'] = 1;
            $itemledger_arr['currentStock'] = $item_arr['currentStock'];
            $itemledger_arr['PLGLAutoID'] = $double_entry['master_data']['costGLAutoID'];
            $itemledger_arr['PLSystemGLCode'] = $double_entry['master_data']['costSystemGLCode'];
            $itemledger_arr['PLGLCode'] = $double_entry['master_data']['costGLCode'];
            $itemledger_arr['PLDescription'] = $double_entry['master_data']['costDescription'];
            $itemledger_arr['PLType'] = $double_entry['master_data']['costType'];
            $itemledger_arr['BLGLAutoID'] = $double_entry['master_data']['assteGLAutoID'];
            $itemledger_arr['BLSystemGLCode'] = $double_entry['master_data']['assteSystemGLCode'];
            $itemledger_arr['BLGLCode'] = $double_entry['master_data']['assteGLCode'];
            $itemledger_arr['BLDescription'] = $double_entry['master_data']['assteDescription'];
            $itemledger_arr['BLType'] = $double_entry['master_data']['assteType'];
            $itemledger_arr['transactionAmount'] = $double_entry['total'];
            $itemledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
            $itemledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
            $itemledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
            $itemledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
            $itemledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
            $itemledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
            $itemledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
            $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
            $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
            $itemledger_arr['companyLocalWacAmount'] = $item_arr['companyLocalWacAmount'];
            $itemledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
            $itemledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
            $itemledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
            $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
            $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
            $itemledger_arr['companyReportingWacAmount'] = $item_arr['companyReportingWacAmount'];
            $itemledger_arr['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
            $itemledger_arr['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
            $itemledger_arr['partyCurrencyExchangeRate'] = 1;
            $itemledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
            $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
            $itemledger_arr['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
            $itemledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
            $itemledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
            $itemledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
            /* $itemledger_arr['approvedYN'] =  $double_entry['master_data']['approvedYN'];
             $itemledger_arr['approvedDate'] =  $double_entry['master_data']['approvedDate'];
             $itemledger_arr['approvedbyEmpID'] =  $double_entry['master_data']['approvedbyEmpID'];
             $itemledger_arr['approvedbyEmpName'] =  $double_entry['master_data']['approvedbyEmpName'];*/
            $itemledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
            $itemledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
            $itemledger_arr['companyID'] = $double_entry['master_data']['companyID'];
            /*$itemledger_arr['companyCode'] =  $double_entry['master_data']['companyCode'];*/
            $itemledger_arr['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
            $itemledger_arr['createdPCID'] = $double_entry['master_data']['createdPCID'];
            $itemledger_arr['createdUserID'] = $double_entry['master_data']['createdUserID'];
            $itemledger_arr['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
            $itemledger_arr['createdUserName'] = $double_entry['master_data']['createdUserName'];
            $itemledger_arr['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
            $itemledger_arr['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
            $itemledger_arr['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
            $itemledger_arr['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];

            if (!empty($item_arr)) {
                $this->db->update('srp_erp_itemmaster', $item_arr, 'itemAutoID');
            }

            if (!empty($itemledger_arr)) {
                $this->db->insert('srp_erp_itemledger', $itemledger_arr);
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 'Saved Failed!';
        } else {
            $this->db->trans_commit();
            return 'Job Closed Ledger Entries Posted Successfully!';
        }
    }

    function get_item_wise_template()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $companyID = current_companyID();
        $this->db->select("templateMasterID as templateMasterID, templateDescription, isDefault");
        $this->db->from('srp_erp_mfq_templatemaster');
        $this->db->join('srp_erp_mfq_workflowtemplateitems', "srp_erp_mfq_workflowtemplateitems.workFlowTemplateID = srp_erp_mfq_templatemaster.templateMasterID");
        $this->db->where('srp_erp_mfq_templatemaster.companyID',$companyID);
        $this->db->where('srp_erp_mfq_workflowtemplateitems.companyID',$companyID);
        $this->db->where('mfqItemID',$itemAutoID);
        $template = $this->db->get()->result_array();
        return $template;
    }

    function fetch_double_entry_job_process_based($jobID, $jobcardID)
    {
        $policyJEC = getPolicyValues('JEC', 'All');
        $companyID = current_companyID();
        $gl_array = array();
        $gl_array['gl_detail'] = array();
        $master = [];
        $this->db->select('srp_erp_mfq_itemmaster.*');
        $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_itemmaster.mfqItemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
        $chk_category = $this->db->get('srp_erp_mfq_job')->row_array();

        if($chk_category["mainCategory"] == "Service" || $chk_category["mainCategory"] == "Non Inventory"){
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
                                srp_erp_itemmaster.itemSystemCode,
                                srp_erp_itemmaster.itemDescription,
                                srp_erp_itemmaster.defaultUnitOfMeasureID,
                                srp_erp_itemmaster.defaultUnitOfMeasure,
                                srp_erp_itemmaster.currentStock,
                                srp_erp_itemmaster.mainCategory,
                                srp_erp_itemmaster.costGLAutoID,
                                srp_erp_itemmaster.costSystemGLCode,
                                srp_erp_itemmaster.costGLCode,
                                srp_erp_itemmaster.costDescription,
                                srp_erp_itemmaster.costType,
                                srp_erp_itemmaster.assteGLAutoID,
                                srp_erp_itemmaster.assteSystemGLCode,
                                srp_erp_itemmaster.assteGLCode,
                                srp_erp_itemmaster.assteDescription,
                                srp_erp_itemmaster.assteType, 
                                srp_erp_itemmaster.companyLocalWacAmount, 
                                srp_erp_itemmaster.companyReportingWacAmount 
                                FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_itemmaster.unbilledServicesGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }else{
            $this->db->select('srp_erp_mfq_job.*,customerAutoID,customerSystemCode,customerName,seg.segmentID,seg.segmentCode,itm.*,wh.*');
            $this->db->where('srp_erp_mfq_job.workProcessID', $jobID);
            $this->db->join("(SELECT srp_erp_segment.segmentCode,srp_erp_mfq_segment.segmentID,mfqSegmentID FROM srp_erp_mfq_segment LEFT JOIN srp_erp_segment ON srp_erp_mfq_segment.segmentID = srp_erp_segment.segmentID) seg", "srp_erp_mfq_job.mfqSegmentID=seg.mfqSegmentID", "left");
            $this->db->join("(SELECT srp_erp_customermaster.*,mfqCustomerAutoID FROM srp_erp_mfq_customermaster LEFT JOIN srp_erp_customermaster ON srp_erp_mfq_customermaster.CustomerAutoID = srp_erp_customermaster.CustomerAutoID) cust", "srp_erp_mfq_job.mfqCustomerAutoID=cust.mfqCustomerAutoID", "INNER");
            $this->db->join('(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,srp_erp_mfq_itemmaster.mfqItemID as itemID,srp_erp_itemmaster.itemAutoID,
                                srp_erp_itemmaster.itemSystemCode,
                                srp_erp_itemmaster.itemDescription,
                                srp_erp_itemmaster.defaultUnitOfMeasureID,
                                srp_erp_itemmaster.defaultUnitOfMeasure,
                                srp_erp_itemmaster.currentStock,
                                srp_erp_itemmaster.mainCategory,
                                srp_erp_itemmaster.costGLAutoID,
                                srp_erp_itemmaster.costSystemGLCode,
                                srp_erp_itemmaster.costGLCode,
                                srp_erp_itemmaster.costDescription,
                                srp_erp_itemmaster.costType,
                                srp_erp_itemmaster.assteGLAutoID,
                                srp_erp_itemmaster.assteSystemGLCode,
                                srp_erp_itemmaster.assteGLCode,
                                srp_erp_itemmaster.assteDescription,
                                srp_erp_itemmaster.assteType, 
                                srp_erp_itemmaster.companyLocalWacAmount, 
                                srp_erp_itemmaster.companyReportingWacAmount 
                                FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_itemmaster.assteGLAutoID = srp_erp_chartofaccounts.GLAutoID) itm', 'itm.itemID=srp_erp_mfq_job.mfqItemID', 'LEFT');
            $this->db->join("(SELECT srp_erp_warehousemaster.wareHouseAutoID,srp_erp_warehousemaster.wareHouseCode,srp_erp_warehousemaster.wareHouseLocation,srp_erp_warehousemaster.wareHouseDescription,mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID) wh", "wh.mfqWarehouseAutoID=srp_erp_mfq_job.mfqWarehouseAutoID", "left");
            $master = $this->db->get('srp_erp_mfq_job')->row_array();
        }

        $globalArray = array();
        $total = 0;

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 3);
        $overheadGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($overheadGLSetup) {
            if($overheadGLSetup['isEntryEnabled'] == 1 && $overheadGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_overhead.*, IFNULL(dayComputation, 0) as dayComputation, workProcessFlowID, JCstartDate, status');
                $this->db->where('srp_erp_mfq_jc_overhead.workProcessID', $jobID);
                $this->db->where("srp_erp_mfq_jc_overhead.jobCardID IN ($jobcardID)");
                $this->db->where('srp_erp_mfq_overhead.typeID', 1);
                $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID');
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_overhead.overHeadID', 'LEFT');
                $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID', 'LEFT');
                $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_overhead.workProcessID', 'LEFT');
                $overheadGL = $this->db->get('srp_erp_mfq_jc_overhead')->result_array();
                /*overhead GL*/
                if ($overheadGL) {
                    foreach ($overheadGL as $val) {
                        /* Daily Qty Update */
                        if($val['status'] == 0 && $val['dayComputation'] == 1) {
                            $unitCost = $val['hourlyRate'];
                            //$now = time();
                            $startDate = strtotime($val['JCstartDate']);
                            $now = strtotime(current_date(false));
                            $datediff = $now - $startDate;
                           $days = round($datediff / (60 * 60 * 24)) + 1;
                            $usageQty = ($val['totalHours'] * $days);
                            $materialCost = $usageQty * $unitCost;
                            $val['totalValue'] += $materialCost;
                        }

                        $data_arr['auto_id'] = $val['jcOverHeadID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'] ?? null;
                        $data_arr['segment'] = $master['segmentCode'] ?? null;
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                        $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                        $data_arr['partyName'] = $master['customerName'] ?? null;
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 5);
        $thirdPartyServiceGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($thirdPartyServiceGLSetup) {
            if($thirdPartyServiceGLSetup['isEntryEnabled'] == 1 && $thirdPartyServiceGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_overhead.*, IFNULL(dayComputation, 0) as dayComputation, workProcessFlowID, JCstartDate, status');
                $this->db->where('srp_erp_mfq_jc_overhead.workProcessID', $jobID);
                $this->db->where("srp_erp_mfq_jc_overhead.jobCardID IN ($jobcardID)");
                $this->db->where('srp_erp_mfq_overhead.typeID', 2);
                $this->db->join('srp_erp_mfq_overhead','srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_overhead.overHeadID');
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_overhead.overHeadID', 'LEFT');
                $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID', 'LEFT');
                $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_overhead.workProcessID', 'LEFT');
                $thirdPartyServiceGL = $this->db->get('srp_erp_mfq_jc_overhead')->result_array();
                /*Third Party Service GL*/
                if ($thirdPartyServiceGL) {
                    foreach ($thirdPartyServiceGL as $val) {
                        /* Daily Qty Update */
                        if($val['status'] == 0 && $val['dayComputation'] == 1) {
                            $unitCost = $val['hourlyRate'];
                            //$now = time();
                            $startDate = strtotime($val['JCstartDate']);
                            $now = strtotime(current_date(false));
                            $datediff = $now - $startDate;
                            $days = round($datediff / (60 * 60 * 24)) + 1;
                            $usageQty = ($val['totalHours'] * $days);
                            $materialCost = $usageQty * $unitCost;
                            $val['totalValue'] += $materialCost;
                        }
                        
                        $data_arr['auto_id'] = $val['jcOverHeadID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'] ?? null;
                        $data_arr['segment'] = $master['segmentCode'] ?? null;
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                        $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                        $data_arr['partyName'] = $master['customerName'] ?? null;
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 2);
        $labourGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($labourGLSetup) {
            if($labourGLSetup['isEntryEnabled'] == 1 && $labourGLSetup['manualEntry'] == 1) {
                $this->db->select('oh.*,srp_erp_mfq_jc_labourtask.*, IFNULL(dayComputation, 0) as dayComputation, workProcessFlowID, JCstartDate, status');
                $this->db->where('srp_erp_mfq_jc_labourtask.workProcessID', $jobID);
                $this->db->where("srp_erp_mfq_jc_labourtask.jobCardID IN ($jobcardID)");
                $this->db->join('(SELECT srp_erp_chartofaccounts.*,overHeadID FROM srp_erp_mfq_overhead LEFT JOIN srp_erp_chartofaccounts ON srp_erp_mfq_overhead.financeGLAutoID = srp_erp_chartofaccounts.GLAutoID) oh', 'oh.overHeadID=srp_erp_mfq_jc_labourtask.labourTask', 'LEFT');
                $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jc_labourtask.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID', 'LEFT');
                $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_labourtask.workProcessID', 'LEFT');
                $labourGL = $this->db->get('srp_erp_mfq_jc_labourtask')->result_array();

                /*labour GL*/
                if ($labourGL) {
                    foreach ($labourGL as $val) {
                        /* Daily Qty Update */
                        if($val['status'] == 0 && $val['dayComputation'] == 1) {
                            $unitCost = $val['hourlyRate'];
                            //$now = time();
                            $startDate = strtotime($val['JCstartDate']);
                            $now = strtotime(current_date(false));
                            $datediff = $now - $startDate;
                            $days = round($datediff / (60 * 60 * 24)) + 1;
                            $usageQty = ($val['totalHours'] * $days);
                            $materialCost = $usageQty * $unitCost;
                            $val['totalValue'] += $materialCost;
                        }

                        $data_arr['auto_id'] = $val['jcLabourTaskID'];
                        $data_arr['gl_auto_id'] = $val['GLAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'] ?? null;
                        $data_arr['segment'] = $master['segmentCode'] ?? null;
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                        $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                        $data_arr['partyName'] = $master['customerName'] ?? null;
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 4);
        $machineSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($machineSetup) {
            if($machineSetup['isEntryEnabled'] == 1 && $machineSetup['manualEntry'] == 1) {
                $this->db->select('IFNULL(dayComputation, 0) as dayComputation, workProcessFlowID, hourlyRate, totalHours, JCstartDate, status, srp_erp_mfq_jc_machine.jcMachineID, srp_erp_mfq_jc_machine.transactionExchangeRate, srp_erp_mfq_jc_machine.companyLocalExchangeRate, srp_erp_mfq_jc_machine.companyReportingExchangeRate, srp_erp_mfq_fa_asset_master.glAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory, srp_erp_mfq_jc_machine.totalValue');
                $this->db->where('srp_erp_mfq_jc_machine.workProcessID', $jobID);
                $this->db->where("srp_erp_mfq_jc_machine.jobCardID IN ($jobcardID)");
                $this->db->join("srp_erp_mfq_fa_asset_master", "srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID","left");
                $this->db->join("srp_erp_chartofaccounts", "srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_fa_asset_master.glAutoID","left");
                $this->db->join('srp_erp_mfq_jobcardmaster', 'srp_erp_mfq_jc_machine.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID', 'LEFT');
                $this->db->join('srp_erp_mfq_workflowstatus', 'srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_machine.workProcessID', 'LEFT');
                $machine = $this->db->get('srp_erp_mfq_jc_machine')->result_array();

                /*Machine GL*/
                if ($machine) {
                    foreach ($machine as $val) {
                        /* Daily Qty Update */
                        if($val['status'] == 0 && $val['dayComputation'] == 1) {
                            $unitCost = $val['hourlyRate'];
                            //$now = time();
                            $startDate = strtotime($val['JCstartDate']);
                            $now = strtotime(current_date(false));
                            $datediff = $now - $startDate;
                            $days = round($datediff / (60 * 60 * 24)) + 1;
                            $usageQty = ($val['totalHours'] * $days);
                            $materialCost = $usageQty * $unitCost;
                            $val['totalValue'] += $materialCost;
                        }

                        $data_arr['auto_id'] = $val['jcMachineID'];
                        $data_arr['gl_auto_id'] = $val['glAutoID'];
                        $data_arr['gl_code'] = $val['systemAccountCode'];
                        $data_arr['secondary'] = $val['GLSecondaryCode'];
                        $data_arr['gl_desc'] = $val['GLDescription'];
                        $data_arr['gl_type'] = $val['subCategory'];
                        $data_arr['segment_id'] = $master['segmentID'] ?? null;
                        $data_arr['segment'] = $master['segmentCode'] ?? null;
                        $data_arr['projectID'] = NULL;
                        $data_arr['projectExchangeRate'] = NULL;
                        $data_arr['isAddon'] = 0;
                        $data_arr['subLedgerType'] = 0;
                        $data_arr['subLedgerDesc'] = null;
                        $data_arr['partyContractID'] = null;
                        $data_arr['partyType'] = 'Customer';
                        $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                        $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                        $data_arr['partyName'] = $master['customerName'] ?? null;
                        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                        $data_arr['transactionExchangeRate'] = $val['transactionExchangeRate'];
                        $data_arr['companyLocalExchangeRate'] = $val['companyLocalExchangeRate'];
                        $data_arr['companyReportingExchangeRate'] = $val['companyReportingExchangeRate'];
                        $data_arr['partyExchangeRate'] = 1;
                        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                        $data_arr['partyCurrencyAmount'] = ($val['totalValue'] / $data_arr['partyExchangeRate']);
                        $data_arr['gl_dr'] = '';
                        $data_arr['gl_cr'] = $val['totalValue'];
                        $data_arr['amount_type'] = 'cr';
                        $total += $val['totalValue'];
                        array_push($globalArray, $data_arr);
                    }
                }
            }
        }

    

        $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
        $this->db->where('companyID', $companyID);
        $this->db->where('categoryID', 1);
        $materialGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
        if($materialGLSetup) {
            if($materialGLSetup['isEntryEnabled'] == 1) {
                $this->db->select('IFNULL(dayComputation, 0) as dayComputation, JCstartDate, workProcessFlowID, status, qtyUsed AS qtyUsed,usageQty AS usageQty, unitCost AS unitCost, mfqItemID AS mfqItemID,materialCharge as materialCharge,jcMaterialConsumptionID,wh.*');
                $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $jobID);
                $this->db->where("srp_erp_mfq_jc_materialconsumption.jobCardID IN ($jobcardID)");
                $this->db->where('mfqItemID != 2782');
                $this->db->join("(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,workProcessID FROM srp_erp_mfq_job LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_warehousemaster.WIPGLAutoID = srp_erp_chartofaccounts.GLAutoID) wh", "wh.workProcessID=srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
                $this->db->join("srp_erp_mfq_jobcardmaster", "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID", "left");
                $this->db->join("srp_erp_mfq_workflowstatus", "srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
                $materialGL = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
                /*material consumption GL*/
                if ($materialGL) {
                    foreach ($materialGL as $val) {
                        /* Daily Qty Update */
                        if($val['status'] == 0 && $val['dayComputation'] == 1) {
                            $unitCost = $val['unitCost'];
                            //$now = time();
                            $startDate = strtotime($val['JCstartDate']);
                            $now = strtotime(current_date(false));
                            $datediff = $now - $startDate;
                           $days = round($datediff / (60 * 60 * 24)) + 1;
                            $usageQty = ($val['qtyUsed'] * $days);
                            $materialCost = $usageQty * $unitCost;

                            $val['materialCharge'] += $materialCost;
                        }

                        /*Linked Document Qty*/
                        $materialLinkedQty = $this->db->Query("SELECT SUM(usageAmount) as qty FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL AND jobID = {$jobID} AND typeMasterAutoID = {$val['mfqItemID']}")->row('qty');

                        if($materialGLSetup['linkedDocEntry'] == 1 && $materialGLSetup['manualEntry'] == 1) {
                            $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                            $data_arr['gl_auto_id'] = $val['GLAutoID'];
                            $data_arr['gl_code'] = $val['systemAccountCode'];
                            $data_arr['secondary'] = $val['GLSecondaryCode'];
                            $data_arr['gl_desc'] = $val['GLDescription'];
                            $data_arr['gl_type'] = $val['subCategory'];
                            $data_arr['segment_id'] = $master['segmentID'] ?? null;
                            $data_arr['segment'] = $master['segmentCode'] ?? null;
                            $data_arr['projectID'] = NULL;
                            $data_arr['projectExchangeRate'] = NULL;
                            $data_arr['isAddon'] = 0;
                            $data_arr['subLedgerType'] = 0;
                            $data_arr['subLedgerDesc'] = null;
                            $data_arr['partyContractID'] = null;
                            $data_arr['partyType'] = 'Customer';
                            $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                            $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                            $data_arr['partyName'] = $master['customerName'] ?? null;
                            $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                            $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                            $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'] ?? null;
                            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'] ?? null;
                            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'] ?? null;
                            $data_arr['partyExchangeRate'] = 1;
                            $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                            $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                            $data_arr['gl_dr'] = '';
                            $data_arr['gl_cr'] = $val['materialCharge'];
                            $data_arr['amount_type'] = 'cr';
                            $total += $val['materialCharge'];
                            // print_r($val['materialCharge']);
                            array_push($globalArray, $data_arr);
                        } else if($materialGLSetup['manualEntry'] == 0 && $materialGLSetup['linkedDocEntry'] == 1) {
                            if(!empty($materialLinkedQty) && $materialLinkedQty != 0) {
                                
                                $val['materialCharge'] = $materialLinkedQty * $val['unitCost'];

                                $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                                $data_arr['gl_code'] = $val['systemAccountCode'];
                                $data_arr['secondary'] = $val['GLSecondaryCode'];
                                $data_arr['gl_desc'] = $val['GLDescription'];
                                $data_arr['gl_type'] = $val['subCategory'];
                                $data_arr['segment_id'] = $master['segmentID'] ?? null;
                                $data_arr['segment'] = $master['segmentCode'] ?? null;
                                $data_arr['projectID'] = NULL;
                                $data_arr['projectExchangeRate'] = NULL;
                                $data_arr['isAddon'] = 0;
                                $data_arr['subLedgerType'] = 0;
                                $data_arr['subLedgerDesc'] = null;
                                $data_arr['partyContractID'] = null;
                                $data_arr['partyType'] = 'Customer';
                                $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                                $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                                $data_arr['partyName'] = $master['customerName'] ?? null;
                                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                                $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'] ?? null;
                                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'] ?? null;
                                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'] ?? null;
                                $data_arr['partyExchangeRate'] = 1;
                                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                                $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                                $data_arr['gl_dr'] = '';
                                $data_arr['gl_cr'] = $val['materialCharge'];
                                $data_arr['amount_type'] = 'cr';
                                $total += $val['materialCharge'];
                                array_push($globalArray, $data_arr);
                            }
                        } else if($materialGLSetup['manualEntry'] == 1 && $materialGLSetup['linkedDocEntry'] == 0) {
                            if(!empty($materialLinkedQty) && $materialLinkedQty != 0) {	
                                $val['materialCharge'] =  ($val['usageQty'] - $materialLinkedQty) * $val['unitCost'];	
                            }
                            $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                            $data_arr['gl_auto_id'] = $val['GLAutoID'];
                            $data_arr['gl_code'] = $val['systemAccountCode'];
                            $data_arr['secondary'] = $val['GLSecondaryCode'];
                            $data_arr['gl_desc'] = $val['GLDescription'];
                            $data_arr['gl_type'] = $val['subCategory'];
                            $data_arr['segment_id'] = $master['segmentID'] ?? null;
                            $data_arr['segment'] = $master['segmentCode'] ?? null;
                            $data_arr['projectID'] = NULL;
                            $data_arr['projectExchangeRate'] = NULL;
                            $data_arr['isAddon'] = 0;
                            $data_arr['subLedgerType'] = 0;
                            $data_arr['subLedgerDesc'] = null;
                            $data_arr['partyContractID'] = null;
                            $data_arr['partyType'] = 'Customer';
                            $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                            $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                            $data_arr['partyName'] = $master['customerName'] ?? null;
                            $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                            $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                            $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'] ?? null;
                            $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'] ?? null;
                            $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'] ?? null;
                            $data_arr['partyExchangeRate'] = 1;
                            $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                            $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                            $data_arr['gl_dr'] = '';
                            $data_arr['gl_cr'] = $val['materialCharge'];
                            $data_arr['amount_type'] = 'cr';
                            $total += $val['materialCharge'];
                            array_push($globalArray, $data_arr);
                        }
                    }
                }
            }
        }

       

        $this->db->select('IFNULL(dayComputation, 0) as dayComputation, JCstartDate, workProcessFlowID, status, qtyUsed AS qtyUsed, usageQty AS usageQty, unitCost AS unitCost, mfqItemID AS mfqItemID,materialCharge as materialCharge,jcMaterialConsumptionID,wh.*');
        $this->db->where('srp_erp_mfq_jc_materialconsumption.workProcessID', $jobID);
        $this->db->where("srp_erp_mfq_jc_materialconsumption.jobCardID IN ($jobcardID)");
        $this->db->where('mfqItemID', 2782);
        $this->db->join("(SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterCategory,subCategory,workProcessID FROM srp_erp_mfq_job LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_job.mfqWarehouseAutoID = srp_erp_mfq_warehousemaster.mfqWarehouseAutoID LEFT JOIN srp_erp_warehousemaster ON srp_erp_mfq_warehousemaster.warehouseAutoID = srp_erp_warehousemaster.warehouseAutoID LEFT JOIN srp_erp_chartofaccounts ON srp_erp_warehousemaster.WIPGLAutoID = srp_erp_chartofaccounts.GLAutoID) wh", "wh.workProcessID=srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
        $this->db->join("srp_erp_mfq_jobcardmaster", "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID", "left");
        $this->db->join("srp_erp_mfq_workflowstatus", "srp_erp_mfq_workflowstatus.templateDetailID = srp_erp_mfq_jobcardmaster.templateDetailID AND srp_erp_mfq_workflowstatus.jobID = srp_erp_mfq_jc_materialconsumption.workProcessID", "left");
        $materialWIPGL = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
        if($materialWIPGL) {
            foreach ($materialWIPGL as $val) {            
                 /* Daily Qty Update */
                 if($val['status'] == 0 && $val['dayComputation'] == 1) {
                    $unitCost = $val['unitCost'];
                    //$now = time();
                    $startDate = strtotime($val['JCstartDate']);
                    $now = strtotime(current_date(false));
                    $datediff = $now - $startDate;
                    $days = round($datediff / (60 * 60 * 24)) + 1;
                    $usageQty = ($val['qtyUsed'] * $days);
                    $materialCost = $usageQty * $unitCost;
                    $val['materialCharge'] += $materialCost;
                }

                $data_arr['auto_id'] = $val['jcMaterialConsumptionID'];
                $data_arr['gl_auto_id'] = $val['GLAutoID'];
                $data_arr['gl_code'] = $val['systemAccountCode'];
                $data_arr['secondary'] = $val['GLSecondaryCode'];
                $data_arr['gl_desc'] = $val['GLDescription'];
                $data_arr['gl_type'] = $val['subCategory'];
                $data_arr['segment_id'] = $master['segmentID'] ?? null;
                $data_arr['segment'] = $master['segmentCode'] ?? null;
                $data_arr['projectID'] = NULL;
                $data_arr['projectExchangeRate'] = NULL;
                $data_arr['isAddon'] = 0;
                $data_arr['subLedgerType'] = 0;
                $data_arr['subLedgerDesc'] = null;
                $data_arr['partyContractID'] = null;
                $data_arr['partyType'] = 'Customer';
                $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
                $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
                $data_arr['partyName'] = $master['customerName'] ?? null;
                $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
                $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
                $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'] ?? null;
                $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'] ?? null;
                $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'] ?? null;
                $data_arr['partyExchangeRate'] = 1;
                $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
                $data_arr['partyCurrencyAmount'] = ($val['materialCharge'] / $data_arr['partyExchangeRate']);
                $data_arr['gl_dr'] = '';
                $data_arr['gl_cr'] = $val['materialCharge'];
                $data_arr['amount_type'] = 'cr';
                $total += $val['materialCharge'];
                array_push($globalArray, $data_arr);
            }
        }

        if($policyJEC &&  $policyJEC == 1) {
            if($chk_category["mainCategory"] == "Inventory") {
                $itemcategory = 'Inventory';
            } else {
                $itemcategory = 'Service';
            }
            $gldetails = $this->db->query("SELECT GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                    FROM srp_erp_mfq_postingconfiguration JOIN srp_erp_chartofaccounts ON srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_postingconfiguration.value
                    WHERE configurationCode = '$itemcategory' AND srp_erp_mfq_postingconfiguration.companyID = {$companyID}")->row_array();

            $data_arr['gl_auto_id'] = $gldetails['GLAutoID'];
            $data_arr['gl_code'] = $gldetails['systemAccountCode'];
            $data_arr['secondary'] = $gldetails['GLSecondaryCode'];
            $data_arr['gl_desc'] = $gldetails['GLDescription'];
            $data_arr['gl_type'] = $gldetails['subCategory'];
        } else {
            $data_arr['gl_auto_id'] = $master['GLAutoID'] ?? null;
            $data_arr['gl_code'] = $master['systemAccountCode'] ?? null;
            $data_arr['secondary'] = $master['GLSecondaryCode'] ?? null;
            $data_arr['gl_desc'] = $master['GLDescription'] ?? null;
            $data_arr['gl_type'] = $master['subCategory'] ?? null;
        }

        $data_arr['auto_id'] = $master['workProcessID'] ?? null;
       
        $data_arr['segment_id'] = $master['segmentID'] ?? null;
        $data_arr['segment'] = $master['segmentCode'] ?? null;
        $data_arr['projectID'] = NULL;
        $data_arr['projectExchangeRate'] = NULL;
        $data_arr['isAddon'] = 0;
        $data_arr['subLedgerType'] = 0;
        $data_arr['subLedgerDesc'] = null;
        $data_arr['partyContractID'] = null;
        $data_arr['partyType'] = '';
        $data_arr['partyAutoID'] = $master['customerAutoID'] ?? null;
        $data_arr['partySystemCode'] = $master['customerSystemCode'] ?? null;
        $data_arr['partyName'] = $master['customerName'] ?? null;
        $data_arr['partyCurrencyID'] = $master['mfqCustomerCurrencyID'] ?? null;
        $data_arr['partyCurrency'] = $master['mfqCustomerCurrency'] ?? null;
        $data_arr['transactionExchangeRate'] = $master['transactionExchangeRate'] ?? null;
        $data_arr['companyLocalExchangeRate'] = $master['companyLocalExchangeRate'] ?? null;
        $data_arr['companyReportingExchangeRate'] = $master['companyReportingExchangeRate'] ?? null;
        $data_arr['partyExchangeRate'] = 1;
        $data_arr['partyCurrencyDecimalPlaces'] = $master['mfqCustomerCurrencyDecimalPlaces'] ?? null;
        $data_arr['partyCurrencyAmount'] = ($total / $data_arr['partyExchangeRate']);
        $data_arr['gl_dr'] = $total;
        $data_arr['gl_cr'] = '';
        $data_arr['amount_type'] = 'dr';
        array_push($globalArray, $data_arr);

        $gl_array['currency'] = $master['transactionCurrency'] ?? null;
        $gl_array['decimal_places'] = $master['transactionCurrencyDecimalPlaces'] ?? null;
        $gl_array['code'] = 'JOB';
        $gl_array['name'] = 'Job';
        $gl_array['primary_Code'] = $master['documentCode'] ?? null;
        $gl_array['master_data'] = $master;
        $gl_array['date'] = $master['documentDate'] ?? null;
        $gl_array['gl_detail'] = $globalArray;
        $gl_array['total'] = $total;

       

        return $gl_array;
    }

    function fetch_job_item_units()
    {
        $workProcessID = $this->input->post('workProcessID');
        $companyID = current_companyID();

        $units = $this->db->query("SELECT srp_erp_itemmaster.defaultUnitOfMeasureID,srp_erp_mfq_job.qty, srp_erp_itemmaster.defaultUnitOfMeasure, UnitID, UnitDes, isSubitemExist, wareHouseAutoID, subItemapplicableon AS subItemUOM
                        FROM srp_erp_mfq_job 
                            JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID
                            LEFT JOIN srp_erp_mfq_warehousemaster ON srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID
                            LEFT JOIN srp_erp_unit_of_measure secondaryUnit ON secondaryUnit.UnitID = srp_erp_itemmaster.secondaryUOMID
                        WHERE workProcessID = {$workProcessID}")->row_array();

        //check usage for material consumption
        $this->db->where('workProcessID',$workProcessID);
        $this->db->where('companyID',$companyID);
        $usage_zero = $this->db->where('usageQty',0)->from('srp_erp_mfq_jc_materialconsumption')->get()->row_array();

        //check usage for material consumption
        $this->db->where('workProcessID',$workProcessID);
        $this->db->where('companyID',$companyID);
        $usage_zero_labour = $this->db->where('usageHours',0)->from('srp_erp_mfq_jc_labourtask')->get()->row_array();

        if($units['UnitID'] == $units['defaultUnitOfMeasureID']){
            $units['UnitID'] = null;
            $units['UnitDes'] = null;
        }

        if($usage_zero){
            $units['status'] = 'e';
            $units['message'] = 'There are items that does not updated the usage.';
        }elseif($usage_zero_labour){
            $units['status'] = 'e';
            $units['message'] = 'There are labour task items that does not updated the usage.';
        }else{
            $units['status'] = 's';
            $units['message'] = 'Usage allocated';
        }


        return $units;
    }

    function load_open_jobCard_dropdown()
    {
        $this->db->select("srp_erp_mfq_workflowstatus.templateDetailID as templateDetailID, jobID, description");
        $this->db->join("srp_erp_mfq_templatedetail","srp_erp_mfq_templatedetail.templateDetailID = srp_erp_mfq_workflowstatus.templateDetailID");
        $this->db->where('jobID', $this->input->post('jobID'));
        $this->db->where('status', 0);
        $this->db->order_by('workProcessFlowID', 'asc');
        $templateDetailID = $this->db->get('srp_erp_mfq_workflowstatus')->result_array();
        return $templateDetailID;
    }

    function load_material_consumption_qty_process_based()
    {
        $templateDetailID = $this->input->post('templateDetailID');
        $current_date = current_date(false);
        
        $this->db->select("srp_erp_mfq_jc_materialconsumption.workProcessID,srp_erp_mfq_itemmaster.mainCategory,jcMaterialConsumptionID,CONCAT(itemSystemCode,' - ',itemDescription) as itemDescription,
                    if(srp_erp_mfq_jc_materialconsumption.dayComputation = 1, (DATEDIFF('{$current_date}', JCstartDate) + 1) * IFNULL(srp_erp_mfq_bom_materialconsumption.qtyUsed, 0) + usageQty, usageQty) as usageQty,
                    IFNULL(wh.currentStock,0) as currentStock,srp_erp_mfq_jc_materialconsumption.qtyUsed, srp_erp_mfq_jc_materialconsumption.jobCardID,
                    srp_erp_mfq_jc_materialconsumption.mfqItemID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_warehousemaster', "srp_erp_mfq_warehousemaster.mfqWarehouseAutoID = srp_erp_mfq_job.mfqWarehouseAutoID", 'inner');
        $this->db->join('srp_erp_mfq_jc_materialconsumption', "srp_erp_mfq_jc_materialconsumption.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_materialconsumption.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'inner');
        $this->db->join('srp_erp_mfq_bom_materialconsumption', "srp_erp_mfq_bom_materialconsumption.bomMasterID = srp_erp_mfq_jobcardmaster.bomID AND srp_erp_mfq_bom_materialconsumption.mfqItemID = srp_erp_mfq_jc_materialconsumption.mfqItemID", 'left');
        $this->db->join('(SELECT SUM(currentStock) as currentStock,wareHouseAutoID,itemAutoID FROM srp_erp_warehouseitems GROUP BY wareHouseAutoID,itemAutoID) wh', "wh.wareHouseAutoID = srp_erp_mfq_warehousemaster.warehouseAutoID AND srp_erp_mfq_itemmaster.itemAutoID = wh.itemAutoID", 'left');
        $data["material"] = $this->db->get()->result_array();

    

        $this->db->select("srp_erp_mfq_jc_overhead.workProcessID,jcOverHeadID,srp_erp_mfq_jc_overhead.totalHours,CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,
                    if(srp_erp_mfq_jc_overhead.dayComputation = 1, (DATEDIFF('{$current_date}', JCstartDate) + 1) * IFNULL(srp_erp_mfq_bom_overhead.totalHours, 0) + usageHours, usageHours) as usageHours,
                    srp_erp_mfq_jc_overhead.jobCardID,srp_erp_mfq_jc_overhead.overHeadID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->where('srp_erp_mfq_overhead.typeID',1);
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_overhead', "srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_overhead.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_jc_overhead.overHeadID = srp_erp_mfq_overhead.overHeadID", 'inner');
        $this->db->join('srp_erp_mfq_bom_overhead', "srp_erp_mfq_bom_overhead.bomMasterID = srp_erp_mfq_jobcardmaster.bomID AND srp_erp_mfq_bom_overhead.overheadID = srp_erp_mfq_jc_overhead.overHeadID", 'left');
        $data["overhead"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_overhead.workProcessID,jcOverHeadID,srp_erp_mfq_jc_overhead.totalHours,CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,
                if(srp_erp_mfq_jc_overhead.dayComputation = 1, (DATEDIFF('{$current_date}', JCstartDate) + 1) * IFNULL(srp_erp_mfq_bom_overhead.totalHours, 0) + IFNULL(usageHours, 0), IFNULL(usageHours, 0)) as usageHours,
                srp_erp_mfq_jc_overhead.jobCardID,srp_erp_mfq_jc_overhead.overHeadID as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->where('srp_erp_mfq_overhead.typeID',2);
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_overhead', "srp_erp_mfq_jc_overhead.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_overhead.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_jc_overhead.overHeadID = srp_erp_mfq_overhead.overHeadID", 'inner');
        $this->db->join('srp_erp_mfq_bom_overhead', "srp_erp_mfq_bom_overhead.bomMasterID = srp_erp_mfq_jobcardmaster.bomID AND srp_erp_mfq_bom_overhead.overheadID = srp_erp_mfq_jc_overhead.overHeadID", 'left');
        $data["thirdparty"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_labourtask.workProcessID,jcLabourTaskID,srp_erp_mfq_jc_labourtask.totalHours,
                CONCAT(overHeadCode,' - ',srp_erp_mfq_overhead.description) as description,
                IF (srp_erp_mfq_jc_labourtask.dayComputation = 1, (DATEDIFF('{$current_date}', JCstartDate ) + 1 ) * IFNULL( srp_erp_mfq_bom_labourtask.totalHours, 0 ) + IFNULL(usageHours, 0), usageHours) AS usageHours,
                srp_erp_mfq_jc_labourtask.jobCardID,srp_erp_mfq_jc_labourtask.labourTask as typeMasterAutoID");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_labourtask', "srp_erp_mfq_jc_labourtask.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_labourtask.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_overhead', "srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_jc_labourtask.labourTask", 'inner');
        $this->db->join('srp_erp_mfq_bom_labourtask', "srp_erp_mfq_bom_labourtask.bomMasterID = srp_erp_mfq_jobcardmaster.bomID AND srp_erp_mfq_bom_labourtask.labourTask = srp_erp_mfq_jc_labourtask.labourTask", 'left');
        $data["labour"] = $this->db->get()->result_array();

        $this->db->select("srp_erp_mfq_jc_machine.workProcessID,srp_erp_mfq_jc_machine.totalHours, IF(srp_erp_mfq_jc_machine.dayComputation = 1, (DATEDIFF('{$current_date}', JCstartDate) + 1) * IFNULL(srp_erp_mfq_bom_machine.totalHours, 0) + IFNULL(usageHours, 0),usageHours) AS usageHours,jcMachineID,CONCAT(faCode,' - ',assetDescription) as description");
        $this->db->from("srp_erp_mfq_jobcardmaster");
        $this->db->where('templateDetailID', $templateDetailID);
        $this->db->where('srp_erp_mfq_jobcardmaster.workProcessID', $this->input->post('jobID'));
        $this->db->join('srp_erp_mfq_job', "srp_erp_mfq_job.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_jc_machine', "srp_erp_mfq_jc_machine.jobCardID = srp_erp_mfq_jobcardmaster.jobcardID AND srp_erp_mfq_jc_machine.workProcessID = srp_erp_mfq_jobcardmaster.workProcessID", 'inner');
        $this->db->join('srp_erp_mfq_fa_asset_master', "srp_erp_mfq_fa_asset_master.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID", 'inner');
        $this->db->join('srp_erp_mfq_bom_machine', "srp_erp_mfq_bom_machine.bomMasterID = srp_erp_mfq_jobcardmaster.bomID AND srp_erp_mfq_bom_machine.mfq_faID = srp_erp_mfq_jc_machine.mfq_faID", 'left');
        $data["machine"] = $this->db->get()->result_array();

        return $data;
    }



    function save_job_approval_process_based()
    {
        $companyID =current_companyID();
        $this->db->trans_start();
        $this->load->library('approvals');
        $financePeriod = "";
        $date_format_policy = date_format_policy();
        $system_id = trim($this->input->post('workProcessID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $maxLevel = trim($this->input->post('maxLevel') ?? '');
        $format_postingFinanceDate = input_format_date(trim($this->input->post('postingFinanceDate') ?? ''), $date_format_policy);

        $maxApprovalLevel = $this->approvals->maxlevel('JOB');
        $isFinalLevel = !empty($maxLevel) && $level_id == $maxApprovalLevel['levelNo'] ? true : false;

        if($level_id == $maxLevel) {
            $this->db->select("*");
            $this->db->from('srp_erp_companyfinanceperiod');
            $this->db->join('srp_erp_companyfinanceyear', "srp_erp_companyfinanceyear.companyFinanceYearID=srp_erp_companyfinanceperiod.companyFinanceYearID", "LEFT");
            $this->db->where('srp_erp_companyfinanceperiod.companyID', $this->common_data['company_data']['company_id']);
            $this->db->where("'{$format_postingFinanceDate}' BETWEEN dateFrom AND dateTo");
            $this->db->where("srp_erp_companyfinanceperiod.isActive", 1);
            $financePeriod = $this->db->get()->row_array();
            if (!$financePeriod) {
                return array('w', 'Finance date not between financial period');
            }
        }

        $this->db->select("itemAutoID, defaultUnitOfMeasureID, defaultUnitOfMeasure, qtyUsed, usageQty, warehouseAutoID, itemSystemCode");
        $this->db->from('srp_erp_mfq_jc_materialconsumption material');
        $this->db->join('srp_erp_mfq_itemmaster mfqItem', "mfqItem.mfqItemID = material.mfqItemID", "LEFT");
        $this->db->join('srp_erp_mfq_job mfqJob', "mfqJob.workProcessID = material.workProcessID", "LEFT");
        $this->db->join('srp_erp_mfq_warehousemaster mfqWareHouse', "mfqWareHouse.mfqWarehouseAutoID = mfqJob.mfqWarehouseAutoID", "LEFT");
        $this->db->where('material.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqItem.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqItem.mainCategory', 'Inventory');
        $this->db->where('mfqJob.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('mfqWareHouse.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('material.workProcessID', $system_id);
        $itemsInJobCard = $this->db->get()->result_array();

        $invalidarr = array();
        if($itemsInJobCard) {
            foreach ($itemsInJobCard AS $itemz) {
                if($itemz['itemAutoID']) {
                    $itemStock_check = $this->db->query("SELECT IFNULL(SUM( transactionQTY / convertionRate ), 0) AS currentStock,defaultUnitOfMeasureID as defaultUOMID FROM srp_erp_itemmaster LEFT JOIN srp_erp_itemledger on srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID WHERE wareHouseAutoID = {$itemz['warehouseAutoID']} AND srp_erp_itemledger.itemAutoID = {$itemz['itemAutoID']}")->row_array();
                    if($itemz['defaultUnitOfMeasureID'] == $itemStock_check['defaultUOMID']) {
                        if ($itemStock_check['currentStock'] < $itemz['usageQty']) {
                            array_push($invalidarr, array("itemCode" => $itemz['itemSystemCode'], "itemDescription" => "Stock not sufficient " , "currentStock" => $itemStock_check['currentStock'] . " " . $itemz['defaultUnitOfMeasure']));
                        }
                    } else {
                        $conversionRateUOM = conversionRateUOM_id($itemz['defaultUnitOfMeasureID'], $itemStock_check['defaultUOMID']);
                        if(!$conversionRateUOM) { $conversionRateUOM = 1; }
                        $qtyUsed = $itemz['usageQty'] / $conversionRateUOM;
                        if($itemStock_check['currentStock'] < $qtyUsed) {
                            array_push($invalidarr, array("itemCode" => $itemz['itemSystemCode'], "itemDescription" => "Stock not sufficient " , "currentStock" => $itemStock_check['currentStock'] . " " . $itemz['defaultUnitOfMeasure']));
                        }
                    }
                }
            }
            if (!empty($invalidarr)) {
                return array('w', 'Error In Approving the Job Card', $invalidarr);
            }
        }

        $linkedDocValidation = $this->db->query("SELECT * FROM (
            SELECT stockTransferAutoID AS documentAuoID, 'ST' AS documentID, 'Stock Transfer' AS documentType, stockTransferCode AS documentCode, tranferDate AS documentDate 
                FROM srp_erp_stocktransfermaster 
                WHERE approvedYN != 1 AND jobID = {$system_id} UNION ALL
            SELECT mrnAutoID AS documentAuoID, 'MRN' AS documentID, 'Material Receipt Note' AS documentType, mrnCode AS documentCode, receivedDate AS documentDate 
                FROM srp_erp_materialreceiptmaster 
                WHERE approvedYN != 1 AND jobID = {$system_id} UNION ALL
            SELECT grvAutoID AS documentAuoID, 'GRV' AS documentID, 'Goods Received Voucher' AS documentType, grvPrimaryCode AS documentCode, grvDate AS documentDate  
                FROM srp_erp_grvmaster 
                WHERE approvedYN != 1 AND jobID = {$system_id}
        )tbl")->result_array();
        if($linkedDocValidation) {
            return array('w', 'Please Approve All Pending Linked Documents!', '', $linkedDocValidation);
        } else {
            $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'JOB');
            if ($approvals_status == 1) {
                $data['approvedYN'] = $status;
                $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                $data['approvedbyEmpName'] = $this->common_data['current_user'];
                $data['approvedDate'] = $this->common_data['current_date'];
                $this->db->where('workProcessID', $system_id);
                $this->db->update('srp_erp_mfq_job', $data);

                $totalJobAmount = 0;
                $jobCardIDs = $this->db->select("jobcardID")->where('workProcessID',$system_id)->get('srp_erp_mfq_jobcardmaster')->result_array();
                $jobCardID_str = join(',', array_column($jobCardIDs, 'jobcardID'));
                $double_entry = $this->fetch_double_entry_job_process_based($this->input->post('workProcessID'), $jobCardID_str);
                
                for ($i = 0; $i < count($double_entry['gl_detail']); $i++) {
                    $generalledger_arr[$i]['documentMasterAutoID'] = $double_entry['master_data']['workProcessID'];
                    $generalledger_arr[$i]['documentCode'] = $double_entry['master_data']['documentID'];
                    $generalledger_arr[$i]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                    $generalledger_arr[$i]['documentDate'] = $double_entry['master_data']['closedDate'];
                    $generalledger_arr[$i]['documentYear'] = date("Y", strtotime($double_entry['master_data']['closedDate']));
                    $generalledger_arr[$i]['documentMonth'] = date("m", strtotime($double_entry['master_data']['closedDate']));
                    $generalledger_arr[$i]['documentNarration'] = $double_entry['master_data']['description'];
                    $generalledger_arr[$i]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $generalledger_arr[$i]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $generalledger_arr[$i]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $generalledger_arr[$i]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $generalledger_arr[$i]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $generalledger_arr[$i]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $generalledger_arr[$i]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $generalledger_arr[$i]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['partyType'] = $double_entry['gl_detail'][$i]['partyType'];
                    $generalledger_arr[$i]['partyAutoID'] = $double_entry['gl_detail'][$i]['partyAutoID'];
                    $generalledger_arr[$i]['partySystemCode'] = $double_entry['gl_detail'][$i]['partySystemCode'];
                    $generalledger_arr[$i]['partyName'] = $double_entry['gl_detail'][$i]['partyName'];
                    $generalledger_arr[$i]['partyCurrencyID'] = $double_entry['gl_detail'][$i]['partyCurrencyID'];
                    $generalledger_arr[$i]['partyCurrency'] = $double_entry['gl_detail'][$i]['partyCurrency'];
                    $generalledger_arr[$i]['partyExchangeRate'] = 1;
                    $generalledger_arr[$i]['partyCurrencyDecimalPlaces'] = $double_entry['gl_detail'][$i]['partyCurrencyDecimalPlaces'];
                    $generalledger_arr[$i]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $generalledger_arr[$i]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $generalledger_arr[$i]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $generalledger_arr[$i]['approvedDate'] = $double_entry['master_data']['approvedDate'];
                    $generalledger_arr[$i]['approvedbyEmpID'] = $double_entry['master_data']['approvedbyEmpID'];
                    $generalledger_arr[$i]['approvedbyEmpName'] = $double_entry['master_data']['approvedbyEmpName'];
                    $generalledger_arr[$i]['companyID'] = $double_entry['master_data']['companyID'];
                    $amount = $double_entry['gl_detail'][$i]['gl_dr'];
                    if ($double_entry['gl_detail'][$i]['amount_type'] == 'cr') {
                        $amount = ($double_entry['gl_detail'][$i]['gl_cr'] * -1);
                    }
                    $generalledger_arr[$i]['transactionAmount'] = round($amount, $generalledger_arr[$i]['transactionCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyLocalAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyLocalExchangeRate']), $generalledger_arr[$i]['companyLocalCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['companyReportingAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['companyReportingExchangeRate']), $generalledger_arr[$i]['companyReportingCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['partyCurrencyAmount'] = round(($generalledger_arr[$i]['transactionAmount'] / $generalledger_arr[$i]['partyExchangeRate']), $generalledger_arr[$i]['partyCurrencyDecimalPlaces']);
                    $generalledger_arr[$i]['amount_type'] = $double_entry['gl_detail'][$i]['amount_type'];
                    $generalledger_arr[$i]['documentDetailAutoID'] = $double_entry['gl_detail'][$i]['auto_id'];
                    $generalledger_arr[$i]['GLAutoID'] = $double_entry['gl_detail'][$i]['gl_auto_id'];
                    $generalledger_arr[$i]['systemGLCode'] = $double_entry['gl_detail'][$i]['gl_code'];
                    $generalledger_arr[$i]['GLCode'] = $double_entry['gl_detail'][$i]['secondary'];
                    $generalledger_arr[$i]['GLDescription'] = $double_entry['gl_detail'][$i]['gl_desc'];
                    $generalledger_arr[$i]['GLType'] = $double_entry['gl_detail'][$i]['gl_type'];
                    $generalledger_arr[$i]['segmentID'] = $double_entry['gl_detail'][$i]['segment_id'];
                    $generalledger_arr[$i]['segmentCode'] = $double_entry['gl_detail'][$i]['segment'];
                    $generalledger_arr[$i]['subLedgerType'] = $double_entry['gl_detail'][$i]['subLedgerType'];
                    $generalledger_arr[$i]['subLedgerDesc'] = $double_entry['gl_detail'][$i]['subLedgerDesc'];
                    $generalledger_arr[$i]['isAddon'] = $double_entry['gl_detail'][$i]['isAddon'];
                    $generalledger_arr[$i]['createdUserGroup'] = $this->common_data['user_group'];
                    $generalledger_arr[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['createdDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['createdUserName'] = $this->common_data['current_user'];
                    $generalledger_arr[$i]['modifiedPCID'] = $this->common_data['current_pc'];
                    $generalledger_arr[$i]['modifiedUserID'] = $this->common_data['current_userID'];
                    $generalledger_arr[$i]['modifiedDateTime'] = $this->common_data['current_date'];
                    $generalledger_arr[$i]['modifiedUserName'] = $this->common_data['current_user'];

                    $totalJobAmount += $double_entry['gl_detail'][$i]['gl_dr'];
                }
    
                if (!empty($generalledger_arr)) {
                    $this->db->insert_batch('srp_erp_generalledger', $generalledger_arr);
                }

                if($totalJobAmount > 0) {
                    $this->db->query("UPDATE srp_erp_mfq_job SET 
                    transactionAmount = {$totalJobAmount},
                    companyLocalAmount = ROUND(({$totalJobAmount} / companyLocalExchangeRate), companyLocalCurrencyDecimalPlaces),
                    companyReportingAmount = ROUND(({$totalJobAmount} / companyReportingExchangeRate), companyReportingCurrencyDecimalPlaces)
                    WHERE workProcessID='{$system_id}'");
                }

                $this->db->select('unitCost AS unitCost, srp_erp_mfq_jc_materialconsumption.mfqItemID AS mfqItemID, materialCharge as materialCharge,qtyUsed, usageQty, itm.*');
                $this->db->where('workProcessID', $this->input->post('workProcessID'));
                $this->db->join('(SELECT srp_erp_itemmaster.*,mfqItemID FROM srp_erp_mfq_itemmaster LEFT JOIN srp_erp_itemmaster ON srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID) itm', 'itm.mfqItemID=srp_erp_mfq_jc_materialconsumption.mfqItemID', 'LEFT');
                $materialConsumption = $this->db->get('srp_erp_mfq_jc_materialconsumption')->result_array();
    
                $this->db->select('isEntryEnabled, manualEntry, IFNULL(linkedDocEntry, 0) AS linkedDocEntry');
                $this->db->where('companyID', $companyID);
                $this->db->where('categoryID', 1);
                $materialGLSetup = $this->db->get('srp_erp_mfq_costingentrysetup')->row_array();
    
                for ($a = 0; $a < count($materialConsumption); $a++) {
                    if ($materialConsumption[$a]['mainCategory'] == 'Inventory') {
                        $itemAutoID = $materialConsumption[$a]['mfqItemID'];
                        $jobID = $this->input->post('workProcessID');
    
                        if($materialConsumption[$a]['mfqItemID'] == 2782) {
                            $qty = $materialConsumption[$a]['usageQty'] / 1;
                            $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                            $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                            $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                            
                            $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                            $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                            $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                            $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                            $itemledger_arr[$a]['referenceNumber'] = null;
                            $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                            $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                            $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                            $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                            $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                            $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                            $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                            $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                            $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                            $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                            $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                            $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                            $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                            $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                            $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                            $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                            $itemledger_arr[$a]['convertionRate'] = 1;
                            $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                            $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                            $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                            $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                            $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                            $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                            $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                            $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                            $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                            $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                            $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                            $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                            $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                            $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                            $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                            $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                            $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                            $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                            $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                            $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                            $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                            $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                            $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                            $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                            $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                            $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                            $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                            $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                            $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                            $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                            $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                            $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                            $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                            $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                            $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                            $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                            $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                            $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                            $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                            $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                            $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                            $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                            $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                            $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                        } else {
                            if($materialGLSetup) {
                                if($materialGLSetup['isEntryEnabled'] == 1) {
                                    $materialLinkedQty = $this->db->Query("SELECT SUM(usageAmount) as qty FROM srp_erp_mfq_jc_usage WHERE linkedDocumentAutoID IS NOT NULL AND jobID = {$jobID} AND typeMasterAutoID = {$itemAutoID}")->row('qty');
                                    
                                    if(!$materialLinkedQty) { $materialLinkedQty = 0; }
                                    if($materialGLSetup['manualEntry'] == 0 && $materialGLSetup['linkedDocEntry'] == 1){
                                        $materialConsumption[$a]['materialCharge'] = $materialLinkedQty * $materialConsumption[$a]['unitCost'];
                                        $materialConsumption[$a]['usageQty'] = $materialLinkedQty;
                                    } else if($materialGLSetup['manualEntry'] == 1 && $materialGLSetup['linkedDocEntry'] == 0){
                                        $materialConsumption[$a]['materialCharge'] = ($materialConsumption[$a]['usageQty'] - $materialLinkedQty) * $materialConsumption[$a]['unitCost'];
                                        $materialConsumption[$a]['usageQty'] = $materialConsumption[$a]['usageQty'] - $materialLinkedQty;
                                    }
        
                                    $qty = $materialConsumption[$a]['usageQty'] / 1;
                                    $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock = (currentStock - {$qty})  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                                    $item_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                                    $item_arr[$a]['currentStock'] = ($materialConsumption[$a]['currentStock'] - $qty);
                                   
                                    $itemledger_arr[$a]['documentID'] = $double_entry['master_data']['documentID'];
                                    $itemledger_arr[$a]['documentCode'] = $double_entry['master_data']['documentID'];
                                    $itemledger_arr[$a]['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                                    $itemledger_arr[$a]['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                                    $itemledger_arr[$a]['documentDate'] = $double_entry['master_data']['closedDate'];
                                    $itemledger_arr[$a]['referenceNumber'] = null;
                                    $itemledger_arr[$a]['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                                    $itemledger_arr[$a]['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                                    $itemledger_arr[$a]['FYBegin'] = $double_entry['master_data']['FYBegin'];
                                    $itemledger_arr[$a]['FYEnd'] = $double_entry['master_data']['FYEnd'];
                                    $itemledger_arr[$a]['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                                    $itemledger_arr[$a]['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];
                                    $itemledger_arr[$a]['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                                    $itemledger_arr[$a]['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                                    $itemledger_arr[$a]['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                                    $itemledger_arr[$a]['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                                    $itemledger_arr[$a]['itemAutoID'] = $materialConsumption[$a]['itemAutoID'];
                                    $itemledger_arr[$a]['itemSystemCode'] = $materialConsumption[$a]['itemSystemCode'];
                                    $itemledger_arr[$a]['itemDescription'] = $materialConsumption[$a]['itemDescription'];
                                    $itemledger_arr[$a]['defaultUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                                    $itemledger_arr[$a]['defaultUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                                    $itemledger_arr[$a]['transactionUOM'] = $materialConsumption[$a]['defaultUnitOfMeasure'];
                                    $itemledger_arr[$a]['transactionUOMID'] = $materialConsumption[$a]['defaultUnitOfMeasureID'];
                                    $itemledger_arr[$a]['transactionQTY'] = $materialConsumption[$a]['usageQty'] * -1;
                                    $itemledger_arr[$a]['convertionRate'] = 1;
                                    $itemledger_arr[$a]['currentStock'] = $item_arr[$a]['currentStock'];
                                    $itemledger_arr[$a]['PLGLAutoID'] = $materialConsumption[$a]['costGLAutoID'];
                                    $itemledger_arr[$a]['PLSystemGLCode'] = $materialConsumption[$a]['costSystemGLCode'];
                                    $itemledger_arr[$a]['PLGLCode'] = $materialConsumption[$a]['costGLCode'];
                                    $itemledger_arr[$a]['PLDescription'] = $materialConsumption[$a]['costDescription'];
                                    $itemledger_arr[$a]['PLType'] = $materialConsumption[$a]['costType'];
                                    $itemledger_arr[$a]['BLGLAutoID'] = $materialConsumption[$a]['assteGLAutoID'];
                                    $itemledger_arr[$a]['BLSystemGLCode'] = $materialConsumption[$a]['assteSystemGLCode'];
                                    $itemledger_arr[$a]['BLGLCode'] = $materialConsumption[$a]['assteGLCode'];
                                    $itemledger_arr[$a]['BLDescription'] = $materialConsumption[$a]['assteDescription'];
                                    $itemledger_arr[$a]['BLType'] = $materialConsumption[$a]['assteType'];
                                    $itemledger_arr[$a]['transactionAmount'] = $materialConsumption[$a]['materialCharge'] * -1;
                                    $itemledger_arr[$a]['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                                    $itemledger_arr[$a]['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                                    $itemledger_arr[$a]['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                                    $itemledger_arr[$a]['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                                    $itemledger_arr[$a]['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                                    $itemledger_arr[$a]['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                                    $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyLocalAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyLocalExchangeRate']), $itemledger_arr[$a]['companyLocalCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyLocalWacAmount'] = $materialConsumption[$a]['companyLocalWacAmount'];
                                    $itemledger_arr[$a]['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                                    $itemledger_arr[$a]['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                                    $itemledger_arr[$a]['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                                    $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['companyReportingAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']), $itemledger_arr[$a]['companyReportingCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['companyReportingWacAmount'] = ($materialConsumption[$a]['companyLocalWacAmount'] / $itemledger_arr[$a]['companyReportingExchangeRate']);
                                    $itemledger_arr[$a]['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                                    $itemledger_arr[$a]['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                                    $itemledger_arr[$a]['partyCurrencyExchangeRate'] = 1;
                                    $itemledger_arr[$a]['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                                    $itemledger_arr[$a]['partyCurrencyAmount'] = round(($itemledger_arr[$a]['transactionAmount'] / $itemledger_arr[$a]['partyCurrencyExchangeRate']), $itemledger_arr[$a]['partyCurrencyDecimalPlaces']);
                                    $itemledger_arr[$a]['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                                    $itemledger_arr[$a]['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                                    $itemledger_arr[$a]['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                                    $itemledger_arr[$a]['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                                    $itemledger_arr[$a]['segmentID'] = $double_entry['master_data']['segmentID'];
                                    $itemledger_arr[$a]['segmentCode'] = $double_entry['master_data']['segmentCode'];
                                    $itemledger_arr[$a]['companyID'] = $double_entry['master_data']['companyID'];
                                    $itemledger_arr[$a]['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                                    $itemledger_arr[$a]['createdPCID'] = $double_entry['master_data']['createdPCID'];
                                    $itemledger_arr[$a]['createdUserID'] = $double_entry['master_data']['createdUserID'];
                                    $itemledger_arr[$a]['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                                    $itemledger_arr[$a]['createdUserName'] = $double_entry['master_data']['createdUserName'];
                                    $itemledger_arr[$a]['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                                    $itemledger_arr[$a]['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                                    $itemledger_arr[$a]['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                                    $itemledger_arr[$a]['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
                                }
                            }
                        }
                    }
                }
    
                
                if (!empty($item_arr)) {
                    $this->db->update_batch('srp_erp_itemmaster', $item_arr, 'itemAutoID');
                }
    
                if (!empty($itemledger_arr)) {
                    $this->db->insert_batch('srp_erp_itemledger', $itemledger_arr);
                }
    
                $itemledger_arr = array();
                $item_arr = array();
                $this->db->select("outputWarehouseAutoID, srp_erp_warehousemaster.wareHouseAutoID as wareHouseAutoID, srp_erp_warehousemaster.wareHouseCode as wareHouseCode, srp_erp_warehousemaster.wareHouseLocation as wareHouseLocation, srp_erp_warehousemaster.wareHouseDescription as wareHouseDescription");
                $this->db->from('srp_erp_mfq_job');
                $this->db->join('srp_erp_warehousemaster', "srp_erp_warehousemaster.warehouseAutoID = srp_erp_mfq_job.outputWarehouseAutoID", "LEFT");
                $this->db->where('srp_erp_mfq_job.workProcessID', $system_id);
                $outputWarehouse = $this->db->get()->row_array();
                if ($double_entry['master_data']['mainCategory'] == 'Inventory' or $double_entry['master_data']['mainCategory'] == 'Non Inventory') {
                    $itemAutoID = $double_entry['master_data']['itemAutoID'];
                    $qty = $double_entry['master_data']['qty'] / 1;
                    $wareHouseAutoID = $double_entry['master_data']['wareHouseAutoID'];
                   
                  
                   /*  $item_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
                    $item_arr['currentStock'] = ($double_entry['master_data']['currentStock'] + $qty);
                    $item_arr['companyLocalWacAmount'] = round(((($double_entry['master_data']['currentStock'] * $double_entry['master_data']['companyLocalWacAmount']) + $double_entry['total']) / $item_arr['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                    $item_arr['companyReportingWacAmount'] = round(((($item_arr['currentStock'] * $double_entry['master_data']['companyReportingWacAmount']) + ($double_entry['total'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']); */
    
                    $itemledger_arr['documentID'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr['documentCode'] = $double_entry['master_data']['documentID'];
                    $itemledger_arr['documentAutoID'] = $double_entry['master_data']['workProcessID'];
                    $itemledger_arr['documentSystemCode'] = $double_entry['master_data']['documentCode'];
                    $itemledger_arr['documentDate'] = $double_entry['master_data']['closedDate'];
                    $itemledger_arr['referenceNumber'] = null;
                    $itemledger_arr['companyFinanceYearID'] = $double_entry['master_data']['companyFinanceYearID'];
                    $itemledger_arr['companyFinanceYear'] = $double_entry['master_data']['companyFinanceYear'];
                    $itemledger_arr['FYBegin'] = $double_entry['master_data']['FYBegin'];
                    $itemledger_arr['FYEnd'] = $double_entry['master_data']['FYEnd'];
                    $itemledger_arr['FYPeriodDateFrom'] = $double_entry['master_data']['FYPeriodDateFrom'];
                    $itemledger_arr['FYPeriodDateTo'] = $double_entry['master_data']['FYPeriodDateTo'];

                    if(!empty($outputWarehouse['outputWarehouseAutoID'])) {
                        $itemledger_arr['wareHouseAutoID'] = $outputWarehouse['wareHouseAutoID'];
                        $itemledger_arr['wareHouseCode'] = $outputWarehouse['wareHouseCode'];
                        $itemledger_arr['wareHouseLocation'] = $outputWarehouse['wareHouseLocation'];
                        $itemledger_arr['wareHouseDescription'] = $outputWarehouse['wareHouseDescription'];
                    } else {
                        $itemledger_arr['wareHouseAutoID'] = $double_entry['master_data']['wareHouseAutoID'];
                        $itemledger_arr['wareHouseCode'] = $double_entry['master_data']['wareHouseCode'];
                        $itemledger_arr['wareHouseLocation'] = $double_entry['master_data']['wareHouseLocation'];
                        $itemledger_arr['wareHouseDescription'] = $double_entry['master_data']['wareHouseDescription'];
                    }
                    
                    $itemledger_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
                    $itemledger_arr['itemSystemCode'] = $double_entry['master_data']['itemSystemCode'];
                    $itemledger_arr['itemDescription'] = $double_entry['master_data']['itemDescription'];
                    $itemledger_arr['defaultUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
                    $itemledger_arr['defaultUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOM'] = $double_entry['master_data']['defaultUnitOfMeasure'];
                    $itemledger_arr['transactionUOMID'] = $double_entry['master_data']['defaultUnitOfMeasureID'];
                    $itemledger_arr['transactionQTY'] = $double_entry['master_data']['qty'];
                    $itemledger_arr['convertionRate'] = 1;
                    $itemledger_arr['PLGLAutoID'] = $double_entry['master_data']['costGLAutoID'];
                    $itemledger_arr['PLSystemGLCode'] = $double_entry['master_data']['costSystemGLCode'];
                    $itemledger_arr['PLGLCode'] = $double_entry['master_data']['costGLCode'];
                    $itemledger_arr['PLDescription'] = $double_entry['master_data']['costDescription'];
                    $itemledger_arr['PLType'] = $double_entry['master_data']['costType'];
                    $itemledger_arr['BLGLAutoID'] = $double_entry['master_data']['assteGLAutoID'];
                    $itemledger_arr['BLSystemGLCode'] = $double_entry['master_data']['assteSystemGLCode'];
                    $itemledger_arr['BLGLCode'] = $double_entry['master_data']['assteGLCode'];
                    $itemledger_arr['BLDescription'] = $double_entry['master_data']['assteDescription'];
                    $itemledger_arr['BLType'] = $double_entry['master_data']['assteType'];
                    $itemledger_arr['transactionAmount'] = $double_entry['total'];
                    $itemledger_arr['transactionCurrencyID'] = $double_entry['master_data']['transactionCurrencyID'];
                    $itemledger_arr['transactionCurrency'] = $double_entry['master_data']['transactionCurrency'];
                    $itemledger_arr['transactionExchangeRate'] = $double_entry['master_data']['transactionExchangeRate'];
                    $itemledger_arr['transactionCurrencyDecimalPlaces'] = $double_entry['master_data']['transactionCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalCurrencyID'] = $double_entry['master_data']['companyLocalCurrencyID'];
                    $itemledger_arr['companyLocalCurrency'] = $double_entry['master_data']['companyLocalCurrency'];
                    $itemledger_arr['companyLocalExchangeRate'] = $double_entry['master_data']['companyLocalExchangeRate'];
                    $itemledger_arr['companyLocalCurrencyDecimalPlaces'] = $double_entry['master_data']['companyLocalCurrencyDecimalPlaces'];
                    $itemledger_arr['companyLocalAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyLocalExchangeRate']), $itemledger_arr['companyLocalCurrencyDecimalPlaces']);
                   
                    $itemledger_arr['companyReportingCurrencyID'] = $double_entry['master_data']['companyReportingCurrencyID'];
                    $itemledger_arr['companyReportingCurrency'] = $double_entry['master_data']['companyReportingCurrency'];
                    $itemledger_arr['companyReportingExchangeRate'] = $double_entry['master_data']['companyReportingExchangeRate'];
                    $itemledger_arr['companyReportingCurrencyDecimalPlaces'] = $double_entry['master_data']['companyReportingCurrencyDecimalPlaces'];
                    $itemledger_arr['companyReportingAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['companyReportingExchangeRate']), $itemledger_arr['companyReportingCurrencyDecimalPlaces']);
                 
                    $itemledger_arr['partyCurrencyID'] = $double_entry['master_data']['mfqCustomerCurrencyID'];
                    $itemledger_arr['partyCurrency'] = $double_entry['master_data']['mfqCustomerCurrency'];
                    $itemledger_arr['partyCurrencyExchangeRate'] = 1;
                    $itemledger_arr['partyCurrencyDecimalPlaces'] = $double_entry['master_data']['mfqCustomerCurrencyDecimalPlaces'];
                    $itemledger_arr['partyCurrencyAmount'] = round(($itemledger_arr['transactionAmount'] / $itemledger_arr['partyCurrencyExchangeRate']), $itemledger_arr['partyCurrencyDecimalPlaces']);
                    $itemledger_arr['confirmedYN'] = $double_entry['master_data']['confirmedYN'];
                    $itemledger_arr['confirmedByEmpID'] = $double_entry['master_data']['confirmedByEmpID'];
                    $itemledger_arr['confirmedByName'] = $double_entry['master_data']['confirmedByName'];
                    $itemledger_arr['confirmedDate'] = $double_entry['master_data']['confirmedDate'];
                    $itemledger_arr['segmentID'] = $double_entry['master_data']['segmentID'];
                    $itemledger_arr['segmentCode'] = $double_entry['master_data']['segmentCode'];
                    $itemledger_arr['companyID'] = $double_entry['master_data']['companyID'];
                    $itemledger_arr['createdUserGroup'] = $double_entry['master_data']['createdUserGroup'];
                    $itemledger_arr['createdPCID'] = $double_entry['master_data']['createdPCID'];
                    $itemledger_arr['createdUserID'] = $double_entry['master_data']['createdUserID'];
                    $itemledger_arr['createdDateTime'] = $double_entry['master_data']['createdDateTime'];
                    $itemledger_arr['createdUserName'] = $double_entry['master_data']['createdUserName'];
                    $itemledger_arr['modifiedPCID'] = $double_entry['master_data']['modifiedPCID'];
                    $itemledger_arr['modifiedUserID'] = $double_entry['master_data']['modifiedUserID'];
                    $itemledger_arr['modifiedDateTime'] = $double_entry['master_data']['modifiedDateTime'];
                    $itemledger_arr['modifiedUserName'] = $double_entry['master_data']['modifiedUserName'];
    
               

                    
                    $currentStockUpdate = $this->db->query("SELECT
                                                            SUM(transactionQTY/convertionRate) as currentStock 
                                                            FROM
                                                            `srp_erp_itemledger` 
                                                            WHERE
                                                            itemAutoID = {$double_entry['master_data']['itemAutoID']}
                                                            GROUP BY
                                                            ItemAutoID")->row('currentStock');

                    $item_arr['itemAutoID'] = $double_entry['master_data']['itemAutoID'];
                    $item_arr['currentStock'] = $currentStockUpdate + $qty;
                    $item_arr['companyLocalWacAmount'] = round((((($currentStockUpdate + $qty) * $double_entry['master_data']['companyLocalWacAmount']) + $double_entry['total']) / $item_arr['currentStock']), $double_entry['master_data']['companyLocalCurrencyDecimalPlaces']);
                    $item_arr['companyReportingWacAmount'] = round(((($item_arr['currentStock'] * $double_entry['master_data']['companyReportingWacAmount']) + ($double_entry['total'] / $double_entry['master_data']['companyReportingExchangeRate'])) / $item_arr['currentStock']), $double_entry['master_data']['companyReportingCurrencyDecimalPlaces']);

                    $itemledger_arr['companyLocalWacAmount'] = $item_arr['companyLocalWacAmount'];
                    $itemledger_arr['companyReportingWacAmount'] = $item_arr['companyReportingWacAmount'];
                    $itemledger_arr['currentStock'] = $item_arr['currentStock'];
                 
                    if (!empty($itemledger_arr)) {
                        $this->db->insert('srp_erp_itemledger', $itemledger_arr);
                    }

                    $currentStockWarehouse = $this->db->query("SELECT
                                                               SUM(transactionQTY/convertionRate) as currentStock 
                                                               FROM
                                                               `srp_erp_itemledger` 
                                                               WHERE
                                                               itemAutoID = {$double_entry['master_data']['itemAutoID']}
                                                               AND wareHouseAutoID = {$double_entry['master_data']['wareHouseAutoID']}
                                                               GROUP BY
                                                               ItemAutoID")->row('currentStock');

                   $comapanyID = current_companyID();
                   $companyCode = current_companyCode();
            
                   $isExistWarehouse = $this->db->query("SELECT 
                                                         * 
                                                         FROM 
                                                         srp_erp_warehouseitems
                                                         where 
                                                         companyID = {$comapanyID} 
                                                         AND wareHouseAutoID = {$double_entry['master_data']['wareHouseAutoID']}
                                                         AND itemAutoID = {$double_entry['master_data']['itemAutoID']}")->row_array();
                
                if(empty($isExistWarehouse)){ 
                    $itemMaster = $this->db->query("SELECT 
                                                    * 
                                                    FROM 
                                                    `srp_erp_itemmaster` 
                                                    where 
                                                    companyID = $comapanyID 
                                                    AND 
                                                    ItemAutoID = {$double_entry['master_data']['itemAutoID']}")->row_array();
                                    
                    $wareHouseMaster = $this->db->query("SELECT warehouseAutoID,warehouseCode,warehouseDescription,warehouseLocation FROM `srp_erp_mfq_warehousemaster` where warehouseAutoID = {$double_entry['master_data']['wareHouseAutoID']}")->row_array();


                    /* $this->db->query("INSERT INTO srp_erp_warehouseitems (
                                        wareHouseAutoID,
                                        wareHouseLocation,
                                        wareHouseDescription,
                                        itemAutoID,
                                        itemSystemCode,
                                        itemDescription,
                                        ActiveYN,
                                        salesPrice,
                                        unitOfMeasureID,
                                        unitOfMeasure,
                                        currentStock,
                                        companyID,
                                        companyCode)
                                        VALUES (
                                            '{$double_entry['master_data']['wareHouseAutoID']}',
                                            '{$wareHouseMaster['warehouseLocation']}',
                                            '{$wareHouseMaster['warehouseDescription']}',
                                            '{$itemMaster['itemAutoID']}',
                                            '{$itemMaster['itemSystemCode']}',
                                            '{$itemMaster['itemDescription']}',
                                            '1',
                                            '{$itemMaster['companyLocalSellingPrice']}',
                                            '{$itemMaster['defaultUnitOfMeasureID']}',
                                            '{$itemMaster['defaultUnitOfMeasure']}',
                                            '{$itemMaster['defaultUnitOfMeasure']}',
                                            '{$currentStockUpdate}',
                                            '{$comapanyID}',
                                            '{$companyCode}',
                                        )
                    "); */

                $this->db->query("INSERT INTO srp_erp_warehouseitems (
                    wareHouseAutoID, 
                    wareHouseLocation,
                    wareHouseDescription, 
                    itemAutoID, 
                    itemSystemCode,
                    itemDescription, 
                    ActiveYN, 
                    salesPrice,
                    unitOfMeasureID, 
                    unitOfMeasure, 
                    currentStock, 
                    companyID, 
                    companyCode )
                    VALUES
                        (
                             {$double_entry['master_data']['wareHouseAutoID']},
                            '{$wareHouseMaster['warehouseLocation']}',
                            '{$wareHouseMaster['warehouseDescription']}',
                             {$itemMaster['itemAutoID']},
                            '{$itemMaster['itemSystemCode']}',
                            '{$itemMaster['itemDescription']}',
                            '1',
                            '{$itemMaster['companyLocalSellingPrice']}',
                            '{$itemMaster['defaultUnitOfMeasureID']}',
                            '{$itemMaster['defaultUnitOfMeasure']}',
                            '{$currentStockUpdate}',
                            '{$comapanyID}',
                            '{$companyCode}'
                        )");


                }else { 
                    $this->db->query("UPDATE srp_erp_warehouseitems SET currentStock =  {$currentStockWarehouse}  WHERE wareHouseAutoID='{$wareHouseAutoID}' and itemAutoID='{$itemAutoID}'");
                }
                   
               
                  
                    if (!empty($item_arr)) {
                        $this->db->query("UPDATE 
                                          `srp_erp_itemmaster` 
                                          SET 
                                          `currentStock` = {$item_arr['currentStock']},
                                          `companyLocalWacAmount` = {$item_arr['companyLocalWacAmount']},
                                          `companyReportingWacAmount` = {$item_arr['companyReportingWacAmount']}
                                          where 
                                          `itemAutoID` = {$item_arr['itemAutoID']}");

                        //$this->db->update('srp_erp_itemmasters', $item_arr, 'itemAutoID');
                    }
    
                
                }
                $machine = $this->db->query("SELECT SUM(totalValue) as totalValue FROM srp_erp_mfq_jc_machine WHERE workProcessID='{$double_entry['master_data']['workProcessID']}'")->row_array();
                $unitPrice = (($double_entry['total'] + $machine["totalValue"]) / $double_entry['master_data']['qty']);
    
                $this->db->set('postingFinanceDate', $format_postingFinanceDate);
                $this->db->set('companyFinancePeriodID', $financePeriod["companyFinancePeriodID"]);
                $this->db->set('companyFinanceYearID', $financePeriod["companyFinanceYearID"]);
                $this->db->set('FYBegin', $financePeriod["beginingDate"]);
                $this->db->set('FYEnd', $financePeriod["endingDate"]);
                $this->db->set('FYPeriodDateFrom', $financePeriod["dateFrom"]);
                $this->db->set('FYPeriodDateTo', $financePeriod["dateTo"]);
                $this->db->set('unitPrice', $unitPrice);
                $this->db->where('workProcessID', $double_entry['master_data']['workProcessID']);
                $result = $this->db->update('srp_erp_mfq_job');


                if($isFinalLevel == 1 && $status == 1) {
                    $masterID = $this->input->post('workProcessID');
                    $subItemDet = $this->db->query("SELECT  * FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentID = 'JOB' AND receivedDocumentAutoID = '" . $masterID . "'")->result_array();
                    if (!empty($subItemDet)) {
                        $i = 0;
                        foreach ($subItemDet as $item) {
                            unset($subItemDet[$i]['subItemAutoID']);
                            $i++;
                        }
                        $this->db->insert_batch('srp_erp_itemmaster_sub', $subItemDet);
                        //$this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID), array('receivedDocumentID' => 'JOB'), array('receivedDocumentDetailID' => null));
                        $this->db->query("DELETE 
                                          FROM
                                          `srp_erp_itemmaster_subtemp` 
                                          WHERE 
                                          receivedDocumentID = 'JOB'  AND (receivedDocumentDetailID IS NULL or receivedDocumentDetailID= '') 
                                          AND receivedDocumentAutoID = $masterID");
                    }
                }
                if($status == 2) {
                   // $this->db->delete('srp_erp_itemmaster_subtemp', array('receivedDocumentAutoID' => $masterID), array('receivedDocumentID' => 'JOB'), array('receivedDocumentDetailID' => null));
                
                   $this->db->query("DELETE 
                   FROM
                   `srp_erp_itemmaster_subtemp` 
                   WHERE 
                   receivedDocumentID = 'JOB'  AND (receivedDocumentDetailID IS NULL or receivedDocumentDetailID= '') 
                   AND receivedDocumentAutoID = $masterID");
                
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully approved");
            }
        }
    }

    function insert_sub_item_configuration()
    {
        $qty = trim($this->input->post('outputQty') ?? '');

       
        $this->db->select("srp_erp_itemmaster.defaultUnitOfMeasureID, srp_erp_mfq_itemmaster.itemSystemCode, srp_erp_itemmaster.defaultUnitOfMeasure, srp_erp_mfq_job.mfqItemID, srp_erp_mfq_itemmaster.itemAutoID,srp_erp_itemmaster.isSubitemExist,srp_erp_itemmaster.subItemapplicableon,srp_erp_itemmaster.secondaryUOMID as secondaryUOMID");
        $this->db->from('srp_erp_mfq_job');
        $this->db->join('srp_erp_mfq_itemmaster', "srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_job.mfqItemID", "LEFT");
        $this->db->join('srp_erp_itemmaster', "srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID", "LEFT");
        $this->db->where("workProcessID", trim($this->input->post('workProcessID') ?? ''));
        $master = $this->db->get()->row_array();
        
        if($master['isSubitemExist'] == 1 && $master['subItemapplicableon'] == 2 && ($master['secondaryUOMID']!=0 || $master['secondaryUOMID']!='')){ 
            $qty = trim($this->input->post('secondaryQty') ?? '');  
        }
         
        $workProcessID = trim($this->input->post('workProcessID') ?? '');
        $this->db->query("DELETE FROM srp_erp_itemmaster_subtemp WHERE receivedDocumentID = 'JOB' AND receivedDocumentAutoID = $workProcessID");

        if ($qty > 0) {
            $x = 0;
            for ($i = 1; $i <= $qty; $i++) {
                $data_subItemMaster[$x]['itemAutoID'] = $master['itemAutoID'];
                $data_subItemMaster[$x]['mfqItemID'] = $master['mfqItemID'];
                $data_subItemMaster[$x]['subItemSerialNo'] = $i;
                $data_subItemMaster[$x]['subItemCode'] = $master['itemSystemCode'] . '/JOB/' . $this->input->post('workProcessID') . '/' . $i;
                $data_subItemMaster[$x]['uom'] = $master['defaultUnitOfMeasure'];
                $data_subItemMaster[$x]['wareHouseAutoID'] = trim($this->input->post('outputWarehouseAutoID') ?? '');
                $data_subItemMaster[$x]['uomID'] = $master['defaultUnitOfMeasureID'];
                $data_subItemMaster[$x]['receivedDocumentID'] = 'JOB';
                $data_subItemMaster[$x]['receivedDocumentAutoID'] = trim($this->input->post('workProcessID') ?? '');
                $data_subItemMaster[$x]['receivedDocumentDetailID'] = null;
                $data_subItemMaster[$x]['companyID'] = $this->common_data['company_data']['company_id'];
                $data_subItemMaster[$x]['createdUserGroup'] = $this->common_data['user_group'];
                $data_subItemMaster[$x]['createdPCID'] = $this->common_data['current_pc'];
                $data_subItemMaster[$x]['createdUserID'] = $this->common_data['current_userID'];
                $data_subItemMaster[$x]['createdDateTime'] = $this->common_data['current_date'];
                $x++;
            }
            // echo '<pre>'; print_r($data_subItemMaster);exit;
            $this->db->insert_batch('srp_erp_itemmaster_subtemp', $data_subItemMaster);
        }

        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(srp_erp_itemmaster_subtemp.expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('receivedDocumentDetailID IS NULL');
        $this->db->where('receivedDocumentID', 'JOB');
        $this->db->where('receivedDocumentAutoID', trim($this->input->post('workProcessID') ?? ''));
        $r = $this->db->get('srp_erp_itemmaster_subtemp')->result_array();

        return $r;
    }

    function save_usage_qty_job()
    {
        $this->db->trans_start();
        $jobID = $this->input->post('jobID');
        if (!empty($jobID)) {
            foreach ($jobID as $key => $val) {
                if (!empty($jobID[$key]) && $this->input->post('qtyUsage')[$key] != 0) {
                    $this->db->set('jobID', $jobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcMaterialConsumptionID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('qtyUsage')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 1);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $jobID[$key]);
                    $this->db->where('typeID', 1);
                    $this->db->where('jobDetailID', $this->input->post('jcMaterialConsumptionID')[$key]);
                    $this->db->SELECT('(usageQty) as usageAmount');
                    $this->db->join("srp_erp_mfq_jc_materialconsumption","srp_erp_mfq_jc_materialconsumption.jcMaterialConsumptionID = srp_erp_mfq_jc_usage.jobDetailID");
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateQtyUsed = $this->db->get()->row('usageAmount');
                    $updateQtyUsed += $this->input->post('qtyUsage')[$key];
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_materialconsumption SET usageQty = {$updateQtyUsed},materialCost = unitCost * {$updateQtyUsed},materialCharge = (unitCost * {$updateQtyUsed})+((unitCost * {$updateQtyUsed})*(markUp/100))  WHERE jcMaterialConsumptionID=" . $this->input->post('jcMaterialConsumptionID')[$key]);
                }
            }
        }

        $ljobID = $this->input->post('ljobID');
        if (!empty($ljobID)) {
            foreach ($ljobID as $key => $val) {
                if (!empty($ljobID[$key]) && $this->input->post('ltotalHours')[$key] != 0) {
                    $this->db->set('jobID', $ljobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcLabourTaskID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('ltotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 2);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $ljobID[$key]);
                    $this->db->where('typeID', 2);
                    $this->db->where('jobDetailID', $this->input->post('jcLabourTaskID')[$key]);
                    $this->db->SELECT('(usageHours) as usageAmount');
                    $this->db->join("srp_erp_mfq_jc_labourtask","srp_erp_mfq_jc_labourtask.jcLabourTaskID = srp_erp_mfq_jc_usage.jobDetailID");
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $updateUsageAmount += $this->input->post('ltotalHours')[$key];
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_labourtask SET usageHours={$updateUsageAmount},totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcLabourTaskID=" . $this->input->post('jcLabourTaskID')[$key]);
                }
            }
        }

        $ojobID = $this->input->post('ojobID');
        if (!empty($ojobID)) {
            foreach ($ojobID as $key => $val) {
                if (!empty($ojobID[$key]) && $this->input->post('ototalHours')[$key] != 0) {
                    $this->db->set('jobID', $ojobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcOverHeadID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('ototalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 3);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $ojobID[$key]);
                    $this->db->where('typeID', 3);
                    $this->db->where('jobDetailID', $this->input->post('jcOverHeadID')[$key]);
                    $this->db->SELECT('(usageHours) as usageAmount');
                    $this->db->join("srp_erp_mfq_jc_overhead","srp_erp_mfq_jc_overhead.jcOverHeadID = srp_erp_mfq_jc_usage.jobDetailID");
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $updateUsageAmount += $this->input->post('ototalHours')[$key];
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_overhead SET usageHours={$updateUsageAmount} ,totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcOverHeadID=" . $this->input->post('jcOverHeadID')[$key]);
                }
            }
        }

        $tpsjobID = $this->input->post('tpsjobID');
        if (!empty($tpsjobID)) {
            foreach ($tpsjobID as $key => $val) {
                if (!empty($tpsjobID[$key]) && $this->input->post('tpstotalHours')[$key] != 0) {
                    $this->db->set('jobID', $tpsjobID[$key]);
                    $this->db->set('jobDetailID', $this->input->post('tpsOverHeadID')[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('tpstotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 5);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $tpsjobID[$key]);
                    $this->db->where('typeID', 5);
                    $this->db->where('jobDetailID', $this->input->post('tpsOverHeadID')[$key]);
                    $this->db->SELECT('(usageHours) as usageAmount');
                    $this->db->join("srp_erp_mfq_jc_overhead","srp_erp_mfq_jc_overhead.jcOverHeadID = srp_erp_mfq_jc_usage.jobDetailID");
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');

                    $updateUsageAmount += $this->input->post('tpstotalHours')[$key];
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_overhead SET usageHours={$updateUsageAmount} ,totalValue = hourlyRate*{$updateUsageAmount}  WHERE jcOverHeadID=" . $this->input->post('tpsOverHeadID')[$key]);
                }
            }
        }


        $mjobID = $this->input->post('mjobID');
        if (!empty($mjobID)) {
            foreach ($mjobID as $key => $val) {
                if (!empty($mjobID[$key]) && $this->input->post('mtotalHours')[$key] != 0) {
                    $this->db->set('jobID', $mjobID[$key]);
                    $this->db->set('jobCardID', $this->input->post('jobCardID')[$key]);
                    $this->db->set('jobDetailID', $this->input->post('jcMachineID')[$key]);
                    $this->db->set('typeMasterAutoID', $this->input->post('typeMasterAutoID')[$key]);
                    $this->db->set('usageAmount', $this->input->post('mtotalHours')[$key]);
                    $this->db->set('companyID', current_companyID());
                    $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                    $this->db->set('createdUserID', current_userID());
                    $this->db->set('createdUserName', current_user());
                    $this->db->set('createdDateTime', current_date(true));
                    $this->db->set('typeID', 4);
                    $result = $this->db->insert('srp_erp_mfq_jc_usage');

                    $this->db->where('jobID', $mjobID[$key]);
                    $this->db->where('typeID', 4);
                    $this->db->where('jobDetailID', $this->input->post('jcMachineID')[$key]);
                    $this->db->SELECT('(usageHours) as usageAmount');
                    $this->db->join("srp_erp_mfq_jc_machine","srp_erp_mfq_jc_machine.jcOverHeadID = srp_erp_mfq_jc_usage.jobDetailID");
                    $this->db->FROM('srp_erp_mfq_jc_usage');
                    $updateUsageAmount = $this->db->get()->row('usageAmount');
                    $updateUsageAmount += $this->input->post('mtotalHours')[$key];
                    $result = $this->db->query("UPDATE srp_erp_mfq_jc_machine SET usageHours={$updateUsageAmount},totalValue = hourlyRate*{$updateUsageAmount} WHERE jcMachineID=" . $this->input->post('jcMachineID')[$key]);
                }
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Usage quantity saved failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Usage Quantity Saved Successfully.');
        }
    }

    function fetch_mfq_item_detail($id){
        $this->db->select('srp_erp_unit_of_measure.UnitDes as secUOMDes,srp_erp_mfq_jc_materialconsumption.*,srp_erp_mfq_itemmaster.itemAutoID,srp_erp_itemmaster.secondaryUOMID,srp_erp_mfq_itemmaster.itemDescription,srp_erp_mfq_jc_usage.usageID,srp_erp_mfq_jc_usage.jobID');
        $this->db->from('srp_erp_mfq_jc_materialconsumption');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_jc_materialconsumption.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_mfq_jc_usage', 'srp_erp_mfq_jc_materialconsumption.jcMaterialConsumptionID = srp_erp_mfq_jc_usage.jobDetailID', 'left');
        $this->db->join('srp_erp_itemmaster', 'srp_erp_mfq_itemmaster.itemAutoID = srp_erp_itemmaster.itemAutoID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_itemmaster.secondaryUOMID', 'left');
        $this->db->where('jcMaterialConsumptionID', $id);
        $r = $this->db->get()->row_array();
        //echo $this->db->last_query();

        return $r;
    }

    function update_mfq_remarks(){
        $this->db->trans_start();
        $data['stage_remarks'] = $this->input->post('mfq_stage_remark');
        $stage_id = $this->input->post('mfq_stage_id');
        $job_id = $this->input->post('mfq_job_id');
        $data['date_updated'] = current_date(true);
        $data['updated_by'] = current_user();

        $this->db->where('job_id', $job_id);
        $this->db->where('stage_id', $stage_id);
        $this->db->where('company_id', current_companyID());
        $result = $this->db->update('srp_erp_mfq_job_wise_stage', $data);

         $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully Updated");
            }
    }

    function update_mfq_progress(){
        $this->db->trans_start();
        $data['stage_progress'] = $this->input->post('mfq_stage_progress');     
        $stage_id = $this->input->post('mfq_stage_id');  
        $job_id = $this->input->post('mfq_job_id');
        $data['date_updated'] = current_date(true);
        $data['updated_by'] = current_user();

        $this->db->where('job_id', $job_id);
        $this->db->where('stage_id', $stage_id);
        $this->db->where('company_id', current_companyID());
        $result = $this->db->update('srp_erp_mfq_job_wise_stage', $data);

         $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully Updated");
            }
    }

    function save_mfq_job_wise_stage(){

        // $this->db->trans_start();        

        $stage_id = $this->input->post('mfq_stage_id');  
        $stageID = $this->input->post('mfq_stage_id');  
        $job_id = $this->input->post('mfq_job_id');
        $templateDetailID = $this->input->post('templateDetailID');
        $workFlowID = $this->input->post('workFlowID');

        //workflow weightage
        $res = $this->db->where('stage_id',$stage_id)->from('srp_erp_mfq_stage')->get()->row_array();

        $data['job_id'] = $job_id;
        $data['stage_id'] = $stage_id;
        $data['stage_progress'] = 0;
        $data['templateDetailID'] = $templateDetailID;
        $data['workFlowID'] = $workFlowID;
        $data['weightage'] = $res['weightage'];
        $data['company_id'] = current_companyID();
        $data['date_updated'] = current_date(true);
        $data['updated_by'] = current_user();

        /********check existing stage start */
        $this->db->SELECT('stage_id');        
        $this->db->FROM('srp_erp_mfq_job_wise_stage');
        $this->db->where("job_id", $job_id);
        $this->db->where("templateDetailID", $templateDetailID);
        $this->db->where('stage_id', $stage_id);
        $this->db->where('workFlowID', $workFlowID);
        $this->db->where("company_id", current_companyID());
        $stage_id = $this->db->get()->row('stage_id');

        //update for mfq stage workflow
        $stage_checklist = $this->db->where('stageID',$stageID)->from('srp_erp_mfq_stage_checklist')->get()->result_array();

        foreach($stage_checklist as $value){

            $data_st = array();

            $data_st['jobID'] = $job_id;
            $data_st['stage_id'] = $stageID;
            $data_st['checklistName'] = $value['checklistDescription'];
            $data_st['checklistID'] = $value['id'];
            $data_st['templateID'] = $templateDetailID;
            $data_st['workProcessID'] = $workFlowID;
            $data_st['value'] = 0;

            $result = $this->db->insert('srp_erp_mfq_job_stage_checklist', $data_st);
        }


        if(!empty($stage_id)){
            return array('w', "Stage already exist!");
        } else{
            $result = $this->db->insert('srp_erp_mfq_job_wise_stage', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully Updated");
            }
        }       
        
    }

    function update_stage_assignee(){

        $workProcessID = $this->input->post('workProcessID');  
        $employee = $this->input->post('employee');
        $stageID = $this->input->post('stageID');

        //update
        $assignee = join(",",$employee);

        $data = array();
        $data['assigneeID'] = $assignee;

        $this->db->where('job_id',$workProcessID)->where('stage_id',$stageID)->update('srp_erp_mfq_job_wise_stage',$data);

        $this->session->set_flashdata('s', 'Assignee updated Successfully.');
        return true;
    }

    function update_stage_value(){
        
        $workProcessID = $this->input->post('workProcessID');  
        $stage_id = $this->input->post('stage_id');
        $type = $this->input->post('type');
        $value = $this->input->post('value');

        $data = array();

        $data[$type] = $value;

        $this->db->where('job_id',$workProcessID)->where('stage_id',$stage_id)->update('srp_erp_mfq_job_wise_stage',$data);

        if($type == 'approved'){

            $weightage =  $this->db->query("
                SELECT round(((approved.approved_weightage / total.total_weightage) * 100),2) as weightage
                FROM srp_erp_mfq_job_wise_stage as stage
                LEFT JOIN (
                    SELECT SUM(weightage) as approved_weightage,job_id
                    FROM srp_erp_mfq_job_wise_stage as stage_a
                    WHERE job_id = {$workProcessID} AND approved = 1
                    GROUP BY job_id

                ) as approved ON stage.job_id = approved.job_id
                LEFT JOIN (
                    SELECT SUM(weightage) as total_weightage,job_id
                    FROM srp_erp_mfq_job_wise_stage as stage_a
                    WHERE job_id = {$workProcessID}
                    GROUP BY job_id
                ) as total ON stage.job_id = total.job_id

                WHERE stage.job_id = {$workProcessID}
            ")->row_array();

            return $weightage;
          
        }

        $this->session->set_flashdata('s', 'Updated Successfully.');
        return true;

    }

    function create_variance_documents(){

        $workProcessID = $this->input->post('workProcessID');
        $companyID = current_companyID();

        $mfq_job = $this->db->where('workProcessID',$workProcessID)->where('companyID',$companyID)->from('srp_erp_mfq_job')->get()->row_array();
        $number_of_variance = $this->db->where('workProcessID',$workProcessID)->where('companyID',$companyID)->where('varianceYN',1)->from('srp_erp_mfq_job')->get()->result_array();

        if($mfq_job){

            $number_of_variance = count($number_of_variance);
            $current_number = $number_of_variance+1 ;
           

            // $documentCode = $mfq_job['documentCode'].'-(V'. $number_of_variance+1 .')';
            $documentCode = $mfq_job['documentCode']." - (V$current_number)";
            $description = $mfq_job['description'].'- Variance Document('. $current_number . ')';

            unset($mfq_job['workProcessID']);

            $mfq_job['documentCode'] = $documentCode;
            $mfq_job['description'] = $description;
            $mfq_job['varianceYN'] = 1;


            $this->db->insert('srp_erp_mfq_job',$mfq_job);

        }

        $this->session->set_flashdata('s', 'Variance Created Successfully.');
        return true;
        //srp_erp_mfq_job


    }

    function save_rfi_header(){

        $workProcessID = $this->input->post("workProcessID");
        $rfiJobID = $this->input->post("rfiJobID");
        $equpSerial = $this->input->post("equpSerial");
        $drawingRef = $this->input->post("drawingRef");
        $rfiQty = $this->input->post("rfiQty");
        $clientTpi = $this->input->post("clientTpi");
        $empID = $this->input->post("empID");
        $companyID = current_companyID();
        $data = array();
        $rfiNo = rand(11111,99999);

        $data['workProcessID'] = $workProcessID;
        $data['jobDescription'] = $rfiJobID;
        $data['rfiNumber'] = $rfiNo;
        $data['equipmentSerial'] = $equpSerial;
        $data['drawingRef'] = $drawingRef;
        $data['quantity'] = $rfiQty;
        $data['status'] = 'Open';
        $data['clientTpi'] = $clientTpi;
        $data['companyID'] = $companyID;
        $data['responsibleEmpID'] = join(',',$empID);
        $data['requestedDate'] = current_date(true);
        $data['updated_by'] = current_user();

        $this->db->insert('srp_erp_mfq_jobrfimaster',$data);

        $this->session->set_flashdata('s', 'Successfully created the RFI header.');
        return True;

    }

    function change_value_detail(){
        $selectType = $this->input->post("selectType");
        $remarks = $this->input->post("remarks");
        $rfiID = $this->input->post("rfiID");
        $workProcessID = $this->input->post("workProcessID");
        $val = $this->input->post("val");
        $status = $this->input->post("status");
        $stage = $this->input->post("stage");
        $stageComment = $this->input->post("stageComment");

        $data = array();

        if($selectType == 1){
            $data['rfiType'] = $val;
        }

        if($remarks){
            $data['remarks'] = $val;
        }

        if($status){
            $data['status'] = $status;
        }

        if($selectType == 2){
            $data['responseType'] = $val;
        }

        if($stage){
            $data['stageID'] = $stage;
        }

        if($stageComment){
            $data['stageComment'] = $stageComment;
        }


        $this->db->where('rfiID',$rfiID)->update('srp_erp_mfq_jobrfimaster',$data);
        
        $this->session->set_flashdata('s', 'Successfully updated RFI.');
        return true;

    }

    function delete_rfi_master(){

        $id = $this->input->post('id');
        $companyID = current_companyID();

        $this->db->where('companyID',$companyID)->where('rfiID',$id)->delete('srp_erp_mfq_jobrfimaster');

        $this->session->set_flashdata('s', 'Successfully Deleted.');
        return True;

    }

    function get_added_third_party_suppliers(){

        $workFlowID = $this->input->post('workFlowID');
        $jobCardID = $this->input->post('jobCardID');
        $companyID = current_companyID();


        $records = $this->db->query("SELECT jc_overhead.*,mfq_overhead.*,supplier.supplierName,supplier.supplierSystemCode
            FROM `srp_erp_mfq_jc_overhead` as jc_overhead
            LEFT JOIN srp_erp_mfq_overhead as mfq_overhead ON jc_overhead.overHeadID = mfq_overhead.overHeadID
            LEFT JOIN srp_erp_suppliermaster as supplier ON mfq_overhead.supplierAutoID = supplier.supplierAutoID
            WHERE jc_overhead.jobCardID = '{$jobCardID}' AND jc_overhead.workProcessID = '{$workFlowID}' AND jc_overhead.companyID = '{$companyID}' 
            AND mfq_overhead.supplierAutoID IS NOT NULL")->result_array();
        
        //AND mfq_overhead.typeID = 2

        return $records;

    }

    function po_genearete_overhead(){

        //Procurement_modal
        $this->load->model('Procurement_modal');
        $this->load->helpers('procurement');

        $overheadID = $this->input->post('overheadID');

        $overHeadArr = join(',',$overheadID);
        $date_format_policy = date_format_policy();
        $date = $this->common_data['current_date'];
        $format_POdate = input_format_date($date, $date_format_policy);

        $overheadDetails = $this->db->query("SELECT jc_overhead.*,mfq_overhead.*,supplier.supplierName,supplier.supplierSystemCode,segment.segmentCode,item.*
            FROM `srp_erp_mfq_jc_overhead` as jc_overhead
            LEFT JOIN srp_erp_mfq_overhead as mfq_overhead ON jc_overhead.overHeadID = mfq_overhead.overHeadID
            LEFT JOIN srp_erp_suppliermaster as supplier ON mfq_overhead.supplierAutoID = supplier.supplierAutoID
            LEFT JOIN srp_erp_segment as segment ON jc_overhead.segmentID = segment.segmentID
            LEFT JOIN srp_erp_itemmaster as item ON mfq_overhead.erpItemAutoID = item.itemAutoID
            WHERE jc_overhead.jcOverHeadID IN ({$overHeadArr})")->result_array();

        $supplier_arr = array();
        foreach($overheadDetails as $key => $value){
            $supplier_arr[$value['supplierAutoID']][] = $value;
        }

        foreach($supplier_arr as $supplierAutoID => $supplier_val){

            //to create header one time
            $headerID = null;

            foreach($supplier_val as $overheadKey => $overheadVal){
                $_POST['purchaseOrderType'] = 'Standard';
                $_POST['segment'] = $overheadVal['segmentID'].'|'.$overheadVal['segmentCode'];
                $_POST['referenceNumber'] = '';
                $_POST['supplierPrimaryCode'] = $supplierAutoID;
                $_POST['transactionCurrencyID'] = $overheadVal['transactionCurrencyID'];
                $_POST['POdate'] = $format_POdate;
                $_POST['expectedDeliveryDate'] = date($date_format_policy ,strtotime('+14 days',strtotime($format_POdate)));
                $_POST['documentTaxType'] = 1;
                $_POST['currency_code'] = $overheadVal['transactionCurrency']." | ".$overheadVal['transactionCurrency'];
                $_POST['shippingAddressID'] = 9;

                if(empty($headerID)){
                    $headerDetails = $this->Procurement_modal->save_purchase_order_header();
                    if($headerDetails){
                        $headerID = $headerDetails['last_id'];
                    }
                }
             
                if($headerID){

                    if($overheadVal){

                        $itemAutoID = array();
                        $search = array();
                        $UnitOfMeasureID = array();
                        $estimatedAmount = array();
                        $comment = array();
                        $quantityRequested = array();
                        $uom = array();
                        $discount_amount = array();
                        $discount = array();


                        $erpItemAutoID = $overheadVal['erpItemAutoID'];
                       
                        $search[] = $overheadVal['itemName'].' - '.$overheadVal['itemSystemCode'].' - '.$overheadVal['mainCategory'].' - '.$overheadVal['mainCategory'];
                        $itemAutoID[] = $overheadVal['itemAutoID'];
                        $UnitOfMeasureID[] = $overheadVal['defaultUnitOfMeasureID'];
                        $estimatedAmount[] = $overheadVal['totalValue'];
                        $quantityRequested[] = 1;
                        $discount_amount[] = 0;
                        $discount[] = 0;
                        $comment[] = '';
                        $uom[] = $overheadVal['defaultUnitOfMeasure'].' | '.$overheadVal['defaultUnitOfMeasure'];
                       
                        $_POST['search'] =  $search;
                        $_POST['itemAutoID'] = $itemAutoID;
                        $_POST['UnitOfMeasureID'] = $UnitOfMeasureID;
                        $_POST['quantityRequested'] = $quantityRequested;
                        $_POST['estimatedAmount'] = $estimatedAmount;
                        $_POST['comment'] = $comment;
                        $_POST['purchaseOrderID'] = $headerID;
                        $_POST['uom'] = $uom;
                        $_POST['discount_amount'] = $discount_amount;
                        $_POST['discount'] = $discount;

                    }

                    $headerDetails = $this->Procurement_modal->save_purchase_order_detail();


                }

                //update overhead po number
                $data_overhead = array();
                $data_overhead['purchaseOrderID'] = $headerID;

                $this->db->where('jcOverHeadID',$overheadVal['jcOverHeadID'])->update('srp_erp_mfq_jc_overhead',$data_overhead);

                
            }

          
        }
        
        $this->session->set_flashdata('s', 'Successfully created the Purchase Order.');
        return True;
    }

    function save_outputitems(){

        $workProcessID = $this->input->post('workProcessID');
        $quantity = $this->input->post('quantity');
        $mfqItemID = $this->input->post('mfqItemID');
        $itemAutoID = $this->input->post('itemAutoID');

        $itemDetail = get_specific_mfq_item($this->input->post('mfqItemID'));

        //check exists
        $ex = $this->db->where('mfqItemID',$this->input->post('mfqItemID'))->where('workProcessID',$workProcessID)->from('srp_erp_mfq_joboutputitems')->get()->row_array();

        if($ex){
            $this->session->set_flashdata('e', 'Item Already Exsist.');
            return True;
        }

        $data = array();

        $data['mfqItemID'] = $mfqItemID;
        $data['itemAutoID'] = $itemAutoID;
        $data['itemSystemCode'] = $itemDetail['itemSystemCode'];
        $data['itemName'] = $itemDetail['itemName'];
        $data['mfqItemDescription'] = $itemDetail['itemDescription'];
        $data['qty'] = $quantity;
        $data['workProcessID'] = $workProcessID;
        $data['itemName'] = $itemDetail['itemName'];
        $data['companyID'] = current_companyID();

        $this->db->insert('srp_erp_mfq_joboutputitems',$data);

        $this->session->set_flashdata('s', 'Successfully created the Record.');
        return True;


    }


    function update_total_item_estimate(){

        $workProcessID = $this->input->post('workProcessID');
        $id = $this->input->post('id');
        $value = $this->input->post('value');

        $data = array();
        $data['totalPercentage'] = $value;

        $this->db->where('id',$id)->update('srp_erp_mfq_joboutputitems',$data);

        $this->session->set_flashdata('s', 'Successfully updated.');
        return True;

    }

}