<?php

class MFQ_Estimate_model extends ERP_Model
{
    function save_Estimate()
    {
        $last_id = "";
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $documentDate = input_format_date(trim($this->input->post('documentDate') ?? ''), $date_format_policy);
        $deliveryDate = input_format_date(trim($this->input->post('deliveryDate') ?? ''), $date_format_policy);
        $Cimaterid = $this->input->post('ciMasterID');
        $companyid = current_companyID();
        $transactioncurreny = explode('|',$this->input->post('transactioncurrency'));

        /// get department details for update
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_cus_inquiry_department_details estd');
        $this->db->where('estd.ciMasterID', $Cimaterid);
        $this->db->where('estd.companyID', $companyid);
        $result_department = $this->db->get()->result_array();

        $this->db->set('showDiscountYN',$this->input->post('discountView')) ;
        if(!empty($Cimaterid))
        {
            $customerinvoicemaster = $this->db->query("SELECT segmentID,transactionCurrencyID,transactionCurrency,transactionExchangeRate,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrency,companyLocalCurrencyID,companyLocalExchangeRate,companyLocalAmount,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyID,companyReportingCurrency,companyReportingExchangeRate,companyReportingCurrencyDecimalPlaces FROM `srp_erp_mfq_customerinquiry` where companyID = '{$companyid}' And ciMasterID = '{$Cimaterid}'")->row_array();

            $this->db->set('mfqSegmentID', $customerinvoicemaster['segmentID']);

            $this->db->set('currencyID',$customerinvoicemaster['transactionCurrencyID']) ;
            $this->db->set('transactionCurrencyID',$customerinvoicemaster['transactionCurrencyID']) ;
            $this->db->set('transactionCurrency',$customerinvoicemaster['transactionCurrency']) ;
            $this->db->set('transactionExchangeRate',1) ;
            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($customerinvoicemaster['transactionCurrencyID']));

            $this->db->set('companyLocalCurrencyID',$customerinvoicemaster['companyLocalCurrencyID']) ;
            $this->db->set('companyLocalCurrency',$customerinvoicemaster['companyLocalCurrency']) ;
            $default_currency = currency_conversionID($customerinvoicemaster['transactionCurrencyID'],$customerinvoicemaster['companyLocalCurrencyID']);
            $this->db->set('companyLocalExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyLocalCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            $this->db->set('companyReportingCurrencyID',$customerinvoicemaster['companyReportingCurrencyID']) ;
            $this->db->set('companyReportingCurrency',$customerinvoicemaster['companyReportingCurrency']) ;
            $default_currency = currency_conversionID($customerinvoicemaster['transactionCurrencyID'],$customerinvoicemaster['companyReportingCurrencyID']);
            $this->db->set('companyReportingExchangeRate',$default_currency['conversion']) ;
            $this->db->set('companyReportingCurrencyDecimalPlaces',$default_currency['DecimalPlaces']);

            $this->db->set('ciMasterID', $Cimaterid);
        }else
        {
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
        }

        if (!$this->input->post('estimateMasterID')) {
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_estimatemaster', 'estimateMasterID', 'companyID');
            $codes = $this->sequence->sequence_generator('EST', $serialInfo['serialNo']);
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('estimateCode', $codes);
            $this->db->set('documentDate', $documentDate);
            $this->db->set('documentID', "EST");
            $this->db->set('deliveryDate', $deliveryDate);
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('scopeOfWork', $this->input->post('scopeOfWork'));
            $this->db->set('technicalDetail', $this->input->post('technicalDetail'));
            $this->db->set('exclusions', $this->input->post('exclusions'));
            $this->db->set('submissionStatus', $this->input->post('submissionStatus'));
            $this->db->set('paymentTerms', $this->input->post('paymentTerms'));
            $this->db->set('termsAndCondition', $this->input->post('termsAndCondition'));
            $this->db->set('warranty', $this->input->post('warranty'));
            $this->db->set('validity', $this->input->post('validity'));
            $this->db->set('deliveryTerms', $this->input->post('deliveryTerms'));
            //$this->db->set('isFormulaChanged', $markupPolicy);
            $this->db->set('isFormulaChanged', trim($this->input->post('pricingFormula') ?? ''));

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_estimatemaster');
            $last_id = $this->db->insert_id();

            $this->db->set('versionOrginID', $last_id);
            $this->db->where('estimateMasterID', $last_id);
            $result = $this->db->update('srp_erp_mfq_estimatemaster');

        } else {
            $last_id = $this->input->post('estimateMasterID');

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

            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('documentDate', $documentDate);
            $this->db->set('deliveryDate', $deliveryDate);
            $this->db->set('documentID', "EST");
            $this->db->set('description', $this->input->post('description'));
            $this->db->set('scopeOfWork', $this->input->post('scopeOfWork'));
            $this->db->set('technicalDetail', $this->input->post('technicalDetail'));
            $this->db->set('exclusions', $this->input->post('exclusions'));
            $this->db->set('submissionStatus', $this->input->post('submissionStatus'));
            $this->db->set('paymentTerms', $this->input->post('paymentTerms'));
            $this->db->set('termsAndCondition', $this->input->post('termsAndCondition'));
            $this->db->set('warranty', $this->input->post('warranty'));
            $this->db->set('validity', $this->input->post('validity'));
            $this->db->set('deliveryTerms', $this->input->post('deliveryTerms'));
            $this->db->set('isFormulaChanged', trim($this->input->post('pricingFormula') ?? ''));

            $this->db->set('modifiedPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('modifiedUserID', current_userID());
            $this->db->set('modifiedUserName', current_user());
            $this->db->set('modifiedDateTime', current_date(true));

            $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
            $result = $this->db->update('srp_erp_mfq_estimatemaster');

        }
        $ciMasterIdexist = $this->db->query("SELECT ciMasterID FROM srp_erp_mfq_estimatemaster WHERE estimateMasterID = {$last_id}")->row_array();
        $ciMasterIDexist = 0;
        if(!empty($ciMasterIdexist['ciMasterID']))
        {
            $ciMasterIDexist = 1;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Estimate Saved Failed ' . $this->db->_error_message());
        } else {

            //update department details table estimateMasterID

            if(count($result_department)>0){
                foreach($result_department as $val){
                    $dept['estimateMasterID']= $last_id;
                    $this->db->where('inquiryDepartmentDetailID', $val['inquiryDepartmentDetailID']);
                    $this->db->update('srp_erp_mfq_cus_inquiry_department_details',$dept);
                }
            }

            $this->db->trans_commit();
            return array('s', 'Estimate Saved Successfully.', $last_id,$ciMasterIDexist);
        }
    }

    function save_estimate_version()
    {
        $this->db->trans_start();
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatemaster');
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->get()->row_array();
        $documentCode = $result["estimateCode"];
        $estimateMasterID = $this->input->post('estimateMasterID');
        if ($result['versionOrginID'] != $this->input->post('estimateMasterID')) {
            $this->db->select('estimateCode');
            $this->db->from('srp_erp_mfq_estimatemaster');
            $this->db->where('estimateMasterID', $result['versionOrginID']);
            $docCode = $this->db->get()->row_array();
            $documentCode = $docCode["estimateCode"];
            $estimateMasterID = $result['versionOrginID'];
        }
        $this->db->select_max('versionLevel');
        $this->db->from('srp_erp_mfq_estimatemaster');
        $this->db->where('versionOrginID', $estimateMasterID);
        $max = $this->db->get()->row_array();

        $this->db->set('mfqCustomerAutoID', $result['mfqCustomerAutoID']);
        $this->db->set('ciMasterID', $result['ciMasterID']);
        $this->db->set('serialNo', $result['serialNo']);
        $this->db->set('estimateCode', $documentCode . '/V' . ($max["versionLevel"] + 1));
        $this->db->set('documentDate', $result["documentDate"]);
        $this->db->set('mfqSegmentID', $result["mfqSegmentID"]);
        $this->db->set('totMargin', $result["totMargin"]);
        $this->db->set('totalSellingPrice', $result["totalSellingPrice"]);
        $this->db->set('totDiscount', $result["totDiscount"]);
        $this->db->set('totDiscountPrice', $result["totDiscountPrice"]);
        $this->db->set('totalCost', $result["totalCost"]);
        $this->db->set('manufacturingType', $result["manufacturingType"]);
        $this->db->set('transactionCurrencyID', $result["transactionCurrencyID"]);
        $this->db->set('transactionCurrency', $result["transactionCurrency"]);
        $this->db->set('transactionExchangeRate', $result["transactionExchangeRate"]);
        $this->db->set('transactionAmount', $result["transactionAmount"]);
        $this->db->set('companyLocalCurrency', $result["companyLocalCurrency"]);
        $this->db->set('companyLocalCurrencyID', $result["companyLocalCurrencyID"]);
        $this->db->set('companyLocalExchangeRate', $result["companyLocalExchangeRate"]);
        $this->db->set('companyLocalAmount', $result["companyLocalAmount"]);
        $this->db->set('companyReportingCurrencyID', $result["companyReportingCurrencyID"]);
        $this->db->set('companyReportingCurrency', $result["companyReportingCurrency"]);
        $this->db->set('companyReportingExchangeRate', $result["companyReportingExchangeRate"]);
        $this->db->set('companyReportingExchangeRate', $result["companyReportingExchangeRate"]);
        $this->db->set('companyReportingAmount', $result["companyReportingAmount"]);
        $this->db->set('documentID', "EST");
        $this->db->set('deliveryDate', $result["deliveryDate"]);
        $this->db->set('description', $result['description']);
        $this->db->set('scopeOfWork', $result['scopeOfWork']);
        $this->db->set('exclusions', $result['exclusions']);
        $this->db->set('technicalDetail', $result['technicalDetail']);
        $this->db->set('submissionStatus', 6);
        $this->db->set('paymentTerms', $result['paymentTerms']);
        $this->db->set('termsAndCondition', $result['termsAndCondition']);
        $this->db->set('warranty', $result['warranty']);
        $this->db->set('validity', $result['validity']);
        $this->db->set('deliveryTerms', $result['deliveryTerms']);
        $this->db->set('versionOrginID', $estimateMasterID);
        $this->db->set('versionLevel', $max["versionLevel"] + 1);
        $this->db->set('isFormulaChanged', 1);
        $this->db->set('currencyID', $this->common_data['company_data']['company_default_currencyID']);
        $this->db->set('companyID', current_companyID());
        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $this->db->set('createdUserID', current_userID());
        $this->db->set('createdUserName', current_user());
        $this->db->set('createdDateTime', current_date(true));

        $result = $this->db->insert('srp_erp_mfq_estimatemaster');
        $last_id = $this->db->insert_id();

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatedetail');
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->get()->result_array();
        foreach ($result as $val) {
            $this->db->set('estimateMasterID', $last_id);
            $this->db->set('ciMasterID', $val['ciMasterID']);
            $this->db->set('ciDetailID', $val['ciDetailID']);
            $this->db->set('bomMasterID', $val['bomMasterID']);
            $this->db->set('mfqItemID', $val['mfqItemID']);
            $this->db->set('expectedQty', $val['expectedQty']);
            $this->db->set('estimatedCost', $val['estimatedCost']);
            $this->db->set('margin', $val['margin']);
            $this->db->set('sellingPrice', $val['sellingPrice']);
            $this->db->set('discount', $val['discount']);
            $this->db->set('discountedPrice', $val['discountedPrice']);
            $this->db->set('transactionCurrencyID', $val['transactionCurrencyID']);
            $this->db->set('transactionCurrency', $val['transactionCurrency']);
            $this->db->set('transactionExchangeRate', $val['transactionExchangeRate']);
            $this->db->set('transactionCurrencyDecimalPlaces', $val['transactionCurrencyDecimalPlaces']);
            $this->db->set('companyLocalCurrencyID', $val['companyLocalCurrencyID']);
            $this->db->set('companyLocalCurrency', $val['companyLocalCurrency']);
            $this->db->set('companyLocalExchangeRate', $val['companyLocalExchangeRate']);
            $this->db->set('companyLocalCurrencyDecimalPlaces', $val['companyLocalCurrencyDecimalPlaces']);
            $this->db->set('companyReportingCurrency', $val['companyReportingCurrency']);
            $this->db->set('companyReportingCurrencyID', $val['companyReportingCurrencyID']);
            $this->db->set('companyReportingExchangeRate', $val['companyReportingExchangeRate']);
            $this->db->set('companyReportingCurrencyDecimalPlaces', $val['companyReportingCurrencyDecimalPlaces']);
            $this->db->set('companyID', $val['companyID']);
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));
            $result = $this->db->insert('srp_erp_mfq_estimatedetail');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Estimate Saved Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Estimate Saved Successfully.', $last_id);
        }
    }

    function delete_estimateDetail()
    {
        $this->db->trans_start();
        $result = $this->db->delete('srp_erp_mfq_estimatedetail', array('estimateDetailID' => $this->input->post('estimateDetailID')), 1);
        if ($result) {
            $this->db->select('SUM(estd.estimatedCost) as estimatedCost,SUM(discountedPrice) as discountedPrice,(((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice)) as totalSellingPrice,((((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice)) - ((totDiscount * (((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice))) / 100)) as totDiscountPrice');
            $this->db->from('srp_erp_mfq_estimatedetail estd');
            $this->db->join('srp_erp_mfq_estimatemaster est', 'est.estimateMasterID = estd.estimateMasterID', 'left');
            $this->db->where('estd.estimateMasterID', $this->input->post('estimateMasterID'));
            $result = $this->db->get()->row_array();

            $this->db->set('totalSellingPrice', $result["totalSellingPrice"]);
            $this->db->set('totDiscountPrice', $result["totDiscountPrice"]);
            $this->db->set('totalCost', $result["estimatedCost"]);
            $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
            $result = $this->db->update('srp_erp_mfq_estimatemaster');
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');

        } else {
            $this->db->trans_commit();
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        }
    }

    function load_mfq_estimate()
    {
        $convertFormat = convert_date_format_sql();
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->select('DATE_FORMAT(est.documentDate,\'' . $convertFormat . '\') as documentDate,DATE_FORMAT(est.deliveryDate,\'' . $convertFormat . '\') as deliveryDate,est.description, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,cust.mfqCustomerAutoID,est.scopeOfWork,est.technicalDetail,IFNULL(est.totMargin, 0) AS totMargin,IFNULL(est.totDiscount, 0) AS totDiscount,est.submissionStatus,est.paymentTerms,est.termsAndCondition,est.totalSellingPrice,est.totDiscountPrice,est.totalCost,est.exclusions,est.designCode,est.designEditor,est.warranty,est.engineeringDrawings,est.engineeringDrawingsComment,est.submissionOfITP,est.itpComment,est.qcqtDocumentation,est.scopeOfWork,est.deliveryTerms,est.mfqSegmentID,est.mfqWarehouseAutoID,est.orderStatus,est.poNumber,srp_months.MonthName,est.validity,est.createdUserID,est.approvedbyEmpID,est.materialCertificationComment,est.manufacturingType,segment.segmentCode as department,est.transactionCurrencyID,est.transactionCurrencyDecimalPlaces AS decimalPlace,est.ciMasterID,currencymaster.CurrencyCode,est.currencyID, showDiscountYN AS showDiscountYN,
        currencymastertrans.CurrencyCode as transcurrencycode,est.transactionCurrencyID as transactionCurrencyest, est.isFormulaChanged as isFormulaChanged');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_months', 'srp_months.MonthId = est.warranty', 'left');
        $this->db->join('srp_erp_mfq_segment mfqsegment', 'mfqsegment.mfqSegmentID = est.mfqSegmentID', 'left');
        $this->db->join('srp_erp_segment segment', 'segment.segmentID = mfqsegment.segmentID', 'left');
        $this->db->join('srp_erp_currencymaster currencymaster', 'currencymaster.currencyID = est.currencyID', 'left');
        $this->db->join('srp_erp_currencymaster currencymastertrans', 'currencymastertrans.currencyID = est.transactionCurrencyID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $result = $this->db->get()->row_array();

        $this->db->select('approvedEmpID');
        $this->db->from('srp_erp_documentapproved');
        $this->db->where('documentID', 'EST');
        $this->db->where('documentSystemCode', $estimateMasterID);
        $this->db->where('companyID', current_companyID());
        $this->db->where('approvalLevelID', 1);
        $reviewer = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_materialcertificate');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $result2 = $this->db->get()->result_array();
        $finalarray = $result;
        $finalarray["materialcertificate"] = array_column($result2, 'materialCertificateID');
        $finalarray["reviewedBy"] = $reviewer['approvedEmpID'] ?? null;
        return $finalarray;
    }

    function load_mfq_estimate_detail()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->select('estm.warrantyCost, estm.commision,estm.estimateMasterID,estm.isFormulaChanged as isFormulaChanged,itemSystemCode,itemDescription,IFNULL(UnitDes,"") as UnitDes,expectedQty,estimatedCost,(expectedQty*estimatedCost) as totalCost,estimateDetailID,est.allotedManHrs,est.unitSellingPrice,est.companyLocalCurrencyDecimalPlaces,est.transactionCurrencyDecimalPlaces,ciCode,est.margin,est.actualMargin,est.sellingPrice,est.estimateMasterID,est.mfqItemID,bomm.bomMasterID,est.discount,est.discountedPrice, estm.mfqCustomerAutoID,estm.description,srp_erp_mfq_itemmaster.itemType,CONCAT(itemDescription," (",itemSystemCode,")") as concatItemDescription, est.transactionCurrencyDecimalPlaces AS transactionCurrencyDecimalPlaces');
        $this->db->from('srp_erp_mfq_estimatedetail est');
        $this->db->join('srp_erp_mfq_estimatemaster estm', 'estm.estimateMasterID = est.estimateMasterID', 'left');
        $this->db->join('srp_erp_mfq_itemmaster', 'est.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_customerinquiry', 'est.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = est.mfqItemID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function load_mfq_estimate_detail_subJobGenerate()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $jobID = $this->input->post('jobID');
        $this->db->select('estm.estimateMasterID,itemSystemCode,IFNULL(jobQty, 0) AS jobQty, sumedQty - IFNULL(jobQty, 0) AS balanceQty, itemDescription,IFNULL(UnitDes,"") as UnitDes,expectedQty, sumedQty, estimatedCost,(expectedQty*estimatedCost) as totalCost,estimateDetailID,est.companyLocalCurrencyDecimalPlaces,est.transactionCurrencyDecimalPlaces,ciCode,est.margin,est.sellingPrice,est.estimateMasterID,est.mfqItemID,bomm.bomMasterID,est.discount,est.discountedPrice,estm.mfqCustomerAutoID,estm.description,srp_erp_mfq_itemmaster.itemType,CONCAT(itemDescription," (",itemSystemCode,")") as concatItemDescription');
        $this->db->from('srp_erp_mfq_estimatedetail est');
        $this->db->join('(SELECT SUM(expectedQty) AS sumedQty, mfqItemID, estimateMasterID FROM srp_erp_mfq_estimatedetail GROUP BY estimateMasterID, mfqItemID)estQty', 'estQty.mfqItemID = est.mfqItemID AND est.estimateMasterID = estQty.estimateMasterID', 'left');
        $this->db->join('(SELECT SUM(qty) as jobQty, linkedJobID, mfqItemID FROM srp_erp_mfq_job GROUP BY linkedJobID, mfqItemID)createdQty', 'createdQty.mfqItemID = est.mfqItemID AND createdQty.linkedJobID = ' . $jobID, 'left');
        $this->db->join('srp_erp_mfq_estimatemaster estm', 'estm.estimateMasterID = est.estimateMasterID', 'left');
        $this->db->join('srp_erp_mfq_itemmaster', 'est.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('srp_erp_mfq_customerinquiry', 'est.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = est.mfqItemID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $result = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function fetch_customer_inquiry()
    {
        $this->db->where('srp_erp_mfq_customerinquiry.mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
        $this->db->where('srp_erp_mfq_customerinquiry.companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('srp_erp_mfq_customerinquiry.approvedYN', 1);
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $this->db->join('srp_erp_mfq_estimatemaster', 'srp_erp_mfq_estimatemaster.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID');
        $data = $this->db->get('srp_erp_mfq_customerinquiry')->result_array();
        return $data;
    }

    function load_mfq_customerInquiryDetail()
    {
        $convertFormat = convert_date_format_sql();
        $ciMasterID = $this->input->post('ciMasterID');
        $this->db->select('srp_erp_mfq_customerinquirydetail.*,DATE_FORMAT(srp_erp_mfq_customerinquirydetail.expectedDeliveryDate,\'' . $convertFormat . '\') as expectedDeliveryDate,IFNULL(srp_erp_mfq_customerinquirydetail.itemDescription,CONCAT(srp_erp_mfq_itemmaster.itemDescription," (",itemSystemCode,")")) as itemDescription,itemSystemCode,IFNULL(UnitDes,"") as UnitDes,IFNULL(bomm.cost,0) as cost,bomm.bomMasterID,(IFNULL(expectedQty,0)-IFNULL(ed.pulledQty,0)) as balanceQty,IFNULL(srp_erp_mfq_customerinquirydetail.mfqItemID,"") as mfqItemID');
        $this->db->from('srp_erp_mfq_customerinquirydetail');
        $this->db->join('srp_erp_mfq_itemmaster', 'srp_erp_mfq_customerinquirydetail.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID', 'left');
        $this->db->join('srp_erp_unit_of_measure', 'unitID = defaultUnitOfMeasureID', 'left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0) + IFNULL(mac.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_machine GROUP BY bomMasterID) mac ON mac.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = srp_erp_mfq_customerinquirydetail.mfqItemID', 'left');
        $this->db->join('(SELECT SUM(expectedQty) as pulledQty,ciDetailID FROM srp_erp_mfq_estimatedetail GROUP BY ciDetailID) ed', 'srp_erp_mfq_customerinquirydetail.ciDetailID = ed.ciDetailID', 'left');
        $this->db->where('ciMasterID', $ciMasterID);
        $this->db->where('(IFNULL(expectedQty, 0) - IFNULL(ed.pulledQty, 0)) > 0');
        $result = $this->db->get()->result_array();
        return $result;
    }

    function save_EstimateDetail()
    {
        $result = $this->db->query("SELECT * FROM srp_erp_mfq_estimatedetail WHERE estimateMasterID=" . $this->input->post('estimateMasterID'))->row_array();
        $ciMasterID = array_unique($this->input->post('ciMasterID'));

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatemaster');
        $this->db->where('estimateMasterID', trim($this->input->post('estimateMasterID') ?? ''));
        $master = $this->db->get()->row_array();

        // if (empty($result) || $result["ciMasterID"] == $ciMasterID[0]) {
            $result2 = $this->db->query("SELECT * FROM srp_erp_mfq_estimatedetail WHERE estimateMasterID=" . $this->input->post('estimateMasterID') . " AND mfqItemID IN (" . join(",", $this->input->post('mfqItemID')) . ")")->result_array();
            if (empty($result2)) { // check for item already exist
                $this->db->trans_start();
                $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
                $estimateMasterID = $this->input->post('estimateMasterID');
                $mfqItemID = $this->input->post('mfqItemID');
                $totEstimatedCost = 0;
                $totEstimatedCostFinal = 0;
                $manufacturingType = null;

                if (!empty($mfqItemID)) {
                    foreach ($mfqItemID as $key => $val) {
                        if (!empty($mfqItemID[$key])) {
                            $totEstimatedCost += $this->input->post('estimatedCost')[$key];
                            if(!$manufacturingType){
                                $result = $this->db->query("SELECT manufacturingType FROM srp_erp_mfq_customerinquiry WHERE ciMasterID=" . $this->input->post('ciMasterID')[$key])->row_array();
                                $manufacturingType = $result["manufacturingType"];
                            }

                            $this->db->set('estimateMasterID', $estimateMasterID);
                            $this->db->set('ciMasterID', $this->input->post('ciMasterID')[$key]);
                            $this->db->set('ciDetailID', $this->input->post('ciDetailID')[$key]);
                            $this->db->set('bomMasterID', $this->input->post('bomMasterID')[$key]);
                            $this->db->set('mfqItemID', $this->input->post('mfqItemID')[$key]);
                            $this->db->set('expectedQty', $this->input->post('expectedQty')[$key]);
                            $this->db->set('estimatedCost', $this->input->post('estimatedCost')[$key]);
                            $this->db->set('transactionCurrencyID', $master['transactionCurrencyID']);
                            $this->db->set('transactionCurrency', fetch_currency_code($master['transactionCurrencyID']));
                            $this->db->set('transactionExchangeRate', 1);
                            $this->db->set('transactionCurrencyDecimalPlaces', fetch_currency_desimal_by_id($master['transactionCurrencyID']));
                            $this->db->set('companyLocalCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
                            $this->db->set('companyLocalCurrency', $this->common_data['company_data']['company_default_currency']);
                            $default_currency = currency_conversionID($master['transactionCurrencyID'], $this->common_data['company_data']['company_default_currencyID']);
                            $this->db->set('companyLocalExchangeRate', $default_currency['conversion']);
                            $this->db->set('companyLocalCurrencyDecimalPlaces', $default_currency['DecimalPlaces']);

                            $this->db->set('companyReportingCurrency', $this->common_data['company_data']['company_reporting_currency']);
                            $this->db->set('companyReportingCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
                            $reporting_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $this->common_data['company_data']['company_reporting_currencyID']);
                            $this->db->set('companyReportingExchangeRate', $reporting_currency['conversion']);
                            $this->db->set('companyReportingCurrencyDecimalPlaces', $reporting_currency['DecimalPlaces']);
                            $this->db->set('companyID', current_companyID());
                            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                            $this->db->set('createdUserID', current_userID());
                            $this->db->set('createdUserName', current_user());
                            $this->db->set('createdDateTime', current_date(true));
                            $totalCost = ($this->input->post('expectedQty')[$key] * $this->input->post('estimatedCost')[$key]);
                            $totEstimatedCostFinal += $totalCost;
                            $this->db->set('sellingPrice', $totalCost, false);
                            $this->db->set('discountedPrice', $totalCost, false);
                            $result = $this->db->insert('srp_erp_mfq_estimatedetail');
                        }
                    }

                    //Update Estimated Cost to estimatemaster table as totalCost
                    $this->db->select('SUM(estd.estimatedCost) as estimatedCost,SUM(discountedPrice) as discountedPrice,(((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice)) as totalSellingPrice,((((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice)) - ((totDiscount * (((totMargin*SUM(discountedPrice)) / 100) + SUM(discountedPrice))) / 100)) as totDiscountPrice');
                    $this->db->from('srp_erp_mfq_estimatedetail estd');
                    $this->db->join('srp_erp_mfq_estimatemaster est', 'est.estimateMasterID = estd.estimateMasterID', 'left');
                    $this->db->where('estd.estimateMasterID', $estimateMasterID);
                    $result = $this->db->get()->row_array();

                    $this->db->set('totalSellingPrice', $result["totalSellingPrice"]);
                    $this->db->set('totDiscountPrice', $result["totDiscountPrice"]);
                    $this->db->set('totalCost', $result["estimatedCost"]);
                    $this->db->set('manufacturingType', $manufacturingType);
                    $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
                    $result = $this->db->update('srp_erp_mfq_estimatemaster');

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        return array('e', 'Estimate detail Failed ' . $this->db->_error_message());

                    } else {
                        $this->db->trans_commit();
                        return array('s', 'Estimate detail added Successfully.', $estimateMasterID);
                    }
                }
            } else {
                return array('w', 'Item already added to estimate');
            }
        // } else {
        //     return array('w', 'Only one custmer inquiry item can be pulled to estimate');
        // }
    }

    function confirm_Estimate()
    {
        $this->db->trans_start();
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $this->db->select('*');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_estimatemaster');
        $row = $this->db->get()->row_array();
        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $this->load->library('approvals');
            $this->db->select('*');
            $this->db->where('estimateMasterID', $estimateMasterID);
            $this->db->from('srp_erp_mfq_estimatemaster');
            $row = $this->db->get()->row_array();

            $validate_code = validate_code_duplication($row['estimateCode'], 'estimateCode', $estimateMasterID,'estimateMasterID', 'srp_erp_mfq_estimatemaster');
            if(!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }

            $approvals_status = $this->approvals->CreateApproval('EST', $row['estimateMasterID'], $row['estimateCode'], 'Estimate', 'srp_erp_mfq_estimatemaster', 'estimateMasterID', 0);
//            if ($approvals_status == 1) {}
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Estimate Confirmed Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Estimate : Confirmed Successfully');
            }
        }
    }

    function set_EstimateProposal(){

        $estimateMasterID = trim($this->input->post('estimateID') ?? '');
        $companyID = current_companyID();

        $this->db->select('*');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_mfq_estimateproposalreview');
        $row = $this->db->get()->row_array();

        //estimate details
        $this->db->select('*');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_mfq_estimatemaster');
        $estimate_master = $this->db->get()->row_array();

        if(empty($row)){

            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_estimateproposalreview', 'proposalID', 'companyID');
            $codes = $this->sequence->sequence_generator('ESTP', $serialInfo['serialNo']);

            $data = array();

            $data['estimateMasterID'] = $estimateMasterID;
            $data['serialNo'] = $serialInfo['serialNo'];
            $data['proposalCode'] = $codes;
            $data['documentID'] = 'ESTP';
            $data['documentCode'] = $estimate_master['estimateCode'];
            $data['documentDate'] = $estimate_master['documentDate'];
            $data['transactionCurrencyID'] = $estimate_master['transactionCurrencyID'];
            $data['transactionCurrency'] = $estimate_master['transactionCurrency'];
            $data['companyID'] = $companyID;
            $data['confirmedYN'] = 1;
            $data['confirmedDate'] = current_date(true);
            $data['confirmedByEmpID'] = current_userID();
            $data['confirmedByName'] = current_user();
            $data['createdUserName'] = current_user();
            $data['createdDateTime'] = current_date(true);

            $res = $this->db->insert('srp_erp_mfq_estimateproposalreview',$data);

        }

        return True;

    }

    function confirm_EstimateProposal()
    {
        $this->db->trans_start();
        $estimateMasterID = trim($this->input->post('estimateID') ?? '');

        $this->db->select('*');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_mfq_estimateproposalreview');
        $row = $this->db->get()->row_array();

        if (!empty($row)) {
            return array('w', 'Document already confirmed');
        } else {
            $this->load->library('approvals');

            $this->db->select('*');
            $this->db->where('estimateMasterID', $estimateMasterID);
            $this->db->from('srp_erp_mfq_estimateproposalreview');
            $row = $this->db->get()->row_array();

            $validate_code = validate_code_duplication($row['proposalCode'], 'proposalCode', $estimateMasterID,'estimateMasterID', 'srp_erp_mfq_estimateproposalreview');
            if(!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }

            $approvals_status = $this->approvals->CreateApproval('ESTP', $row['proposalID'], $row['proposalCode'], 'Estimate Proposal Review', 'srp_erp_mfq_estimateproposalreview', 'proposalID', 0);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Estimate Confirmed Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Estimate : Confirmed Successfully');
            }
        }
    }

    function save_estimate_detail_margin()
    {
        $this->db->trans_start();
        $this->db->set('margin', $this->input->post('margin'));
        $this->db->set('sellingPrice', $this->input->post('sellingPrice'));
        $this->db->set('discountedPrice', $this->input->post('discountedPrice'));
        $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Margin updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Margin Updated Successfully.');
        }

    }

    function save_estimate_detail_discount()
    {
        $this->db->trans_start();
        $this->db->set('discount', $this->input->post('discount'));
        $this->db->set('discountedPrice', $this->input->post('discountedPrice'));
        $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Discount updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Discount Updated Successfully.');
        }
    }

    function save_estimate_detail_actualMargin()
    {
        $this->db->trans_start();
        $this->db->set('actualMargin', $this->input->post('actualMargin'));
        //$this->db->set('discountedPrice', $this->input->post('discountedPrice'));
        $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Actual Margin updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Actual Margin Updated Successfully.');
        }
    }

    function save_estimate_detail_margin_total()
    {
        $this->db->trans_start();
        $this->db->set('totMargin', $this->input->post('totalMargin'));
        $this->db->set('totalSellingPrice', $this->input->post('totalSellingPrice'));
        $this->db->set('totDiscountPrice', $this->input->post('totDiscountPrice'));
        $this->db->set('totalActualMargin', $this->input->post('totActualMargin'));
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Margin updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Margin Updated Successfully.');
        }

    }

    function save_estimate_detail_discount_total()
    {
        $this->db->trans_start();
        $this->db->set('totDiscount', $this->input->post('totDiscount'));
        $this->db->set('totDiscountPrice', $this->input->post('totDiscountPrice'));
        $this->db->set('totalActualMargin', $this->input->post('totActualMargin'));
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Discount updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Discount Updated Successfully.');
        }

    }

    function save_estimate_detail_warranty_cost()
    {
        $this->db->trans_start();
        $this->db->set('warrantyCost', $this->input->post('warrantyCost'));
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Warranty Cost updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Warranty Cost Updated Successfully.');
        }
    }

    function save_estimate_detail_commision()
    {
        $this->db->trans_start();
        $this->db->set('commision', $this->input->post('commision'));
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Commision updated Failed ');

        } else {
            $this->db->trans_commit();
            return array('s', 'Commision Updated Successfully.');
        }
    }

    function load_mfq_estimate_version($status = true)
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->select('versionOrginID');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $result1 = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->where('est.versionOrginID', $result1["versionOrginID"]);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function load_mfq_estimate_proposal_review()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');

        $this->db->select('cus.CustomerName,inq.referenceNo,est.poNumber,est.quotedComment,inq.description,inq.ciCode as inqNumber,est.estimateCode,est.totDiscount,est.totalCost,est.confirmedByName,est.confirmedDate,est.approvedbyEmpName,est.approvedDate,review.confirmedYN,review.approvedYN,review.approvedbyEmpName');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customerinquiry inq', 'inq.ciMasterID = est.ciMasterID','left');
        $this->db->join('srp_erp_mfq_estimateproposalreview review', 'review.estimateMasterID = est.estimateMasterID','left');
        $this->db->join('srp_erp_mfq_customermaster cus', 'cus.mfqCustomerAutoID = est.mfqCustomerAutoID','left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $this->db->where('est.companyID', $this->common_data['company_data']['company_id']);
        $result = $this->db->get()->row_array();

        $this->db->select('dpt.inquiryDepartmentDetailID,dpt.dptComment,dpt.departmentMasterID,emp.Ename2,dpt.modifiedUserName,dpt.modifiedDateTime');
        $this->db->from('srp_erp_mfq_cus_inquiry_department_details dpt');
        $this->db->join('srp_employeesdetails emp', 'emp.EIdNo = dpt.responsibleEmpID','left');
        $this->db->where('dpt.estimateMasterID', $estimateMasterID);
        $this->db->where('dpt.companyID', $this->common_data['company_data']['company_id']);
        $dpt_data = $this->db->get()->result_array();

        $data['estimate']=$result;
        $data['dpt_data']=$dpt_data;

        return $data;
    }

    function update_mfq_estimate_po_number(){
        $this->db->trans_start();

        $data['poNumber'] = $this->input->post('poNumber');

        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster', $data);

         $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully Updated");
            }
    }

    function update_mfq_estimate_quotedComment(){
        $this->db->trans_start();

        $data['quotedComment'] = $this->input->post('quotedComment');

        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster', $data);

         $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Error Occurred");
            } else {
                $this->db->trans_commit();
                return array('s', "Successfully Updated");
            }
    }

    function update_inquiry_department_comment(){
        $this->db->trans_start();

        $estimateMasterID=$this->input->post('estimateMasterID');
        $dptMasterID=$this->input->post('dptMasterID');
        $comment=$this->input->post('comment');

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $this->db->where('est.companyID',$this->common_data['company_data']['company_id']);
        $result = $this->db->get()->row_array();

        if($result){

            $this->db->select('*');
            $this->db->from('srp_erp_mfq_cus_inquiry_department_details dpt');
            $this->db->where('dpt.estimateMasterID', $estimateMasterID);
            $this->db->where('dpt.departmentMasterID', $dptMasterID);
            $this->db->where('dpt.companyID',$this->common_data['company_data']['company_id']);
            $result_exist = $this->db->get()->row_array();

            if($result_exist){

                $data['dptComment'] = $comment;

                $this->db->where('estimateMasterID', $estimateMasterID);
                $this->db->where('departmentMasterID', $dptMasterID);
                $this->db->where('companyID',$this->common_data['company_data']['company_id']);
                $result1 = $this->db->update('srp_erp_mfq_cus_inquiry_department_details', $data);

            }else{
                $dataArray = array(
                    'estimateMasterID' => $estimateMasterID,
                    'ciMasterID' => $result['ciMasterID'],
                    'dptComment' => $comment,
                    'departmentMasterID' => $dptMasterID,
                    'companyID' => $this->common_data['company_data']['company_id'],
                    'modifiedPCID' => $this->common_data['current_pc'],
                    'modifiedUserID' => $this->common_data['current_userID'],
                    'modifiedUserName' => $this->common_data['current_user'],
                    'modifiedDateTime' => $this->common_data['current_date'],
                );
        
                $this->db->insert('srp_erp_mfq_cus_inquiry_department_details', $dataArray);
            }

           
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Error Occurred");
        } else {
            $this->db->trans_commit();
            return array('s', "Successfully Updated");
        }
    }

    function load_emails()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->select('customermail.*');
        $this->db->from('srp_erp_mfq_customeremail customermail');
        $this->db->join('srp_erp_mfq_estimatemaster estimatemaster', 'customermail.mfqCustomerAutoID = estimatemaster.mfqCustomerAutoID');
        $this->db->where('estimatemaster.estimateMasterID', $estimateMasterID);
        $result['customer'] = $this->db->get()->result_array();

        return $result;

    }

    function send_emails()
    {
        $checkmailid = $this->input->post('checkmailid');
        $estimateMasterID = $this->input->post('estimateMasterID');
        $newkmail = $this->input->post('emailNW');
        $companyID = current_companyID();
        if ($checkmailid || !empty(array_filter($newkmail))) {
            $this->db->select('estimatedetail.estimateMasterID,itemmaster.itemDescription,inq.description, inq.ciMasterID');
            $this->db->from('srp_erp_mfq_estimatedetail estimatedetail');
            $this->db->join('srp_erp_mfq_itemmaster itemmaster', 'estimatedetail.mfqItemID = itemmaster.mfqItemID');
            $this->db->join('srp_erp_mfq_customerinquiry inq', 'inq.ciMasterID = estimatedetail.ciMasterID');
            $this->db->where('estimatedetail.estimateMasterID', $estimateMasterID);
            $result1 = $this->db->get()->result_array();
            $des = array_column($result1, 'itemDescription');
            $datadis = join(",", $des);

          
            $data['header'] =  $this->load_mfq_estimate();
            $data['detail'] =  $this->load_mfq_estimate_detail();
            $data['customercountry'] = $this->db->query("SELECT customerCountry FROM srp_erp_mfq_customermaster WHERE mfqCustomerAutoID = '{$data['header']['mfqCustomerAutoID']}'")->row_array();
            $data["mode"] = "pdf";
            $data["pdfType"] = 0;
            $subject = array_unique(array_column($result1, 'description'))[0]." - Quotation No:". $data['header']["estimateCode"];
            $this->load->library('NumberToWords');
            $html = $this->load->view('system/mfq/ajax/quotation_print', $data, true);
            //$this->load->library('pdf');
            $path = 'uploads/mfq/'.$estimateMasterID . "-QUT-" . current_userID() . ".pdf";
            //$this->pdf->save_pdf($html, 'A4', 1, $path);

            $this->db->set('isMailSent', 1);
            $this->db->where('estimateMasterID', $estimateMasterID);
            $this->db->update('srp_erp_mfq_estimatemaster');

            if ($path) {
                $datauploadpdfpath = array(
                            'documentID' => 'ESTMAIL',
                            'documentSystemCode' => $estimateMasterID,
                            'attachmentDescription' => 'Estimater Mail Attachment PDF',
                            'myFileName' => $path,
                            'timestamp' => date('Y-m-d H:i:s'),
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
                $this->db->insert('srp_erp_documentattachments', $datauploadpdfpath);
            }
            
            $files = $_FILES;
            if ($files['upload']['name'][0] == "") {

                $this->load->library('upload');
                $path = "attachments/mfq_est_mail";
                //$path = NGOImage . 'projectProposalImage/';
                if (!file_exists($path)) {
                    mkdir("attachments/mfq_est_mail", 777);
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
                            'documentID' => 'ESTMAIL',
                            'documentSystemCode' => $estimateMasterID,
                            'attachmentDescription' => 'Estimater Mail Attachment',
                            'documentSubID' => '1',
                            'myFileName' => $path.'/'.$journalName,
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
                        $this->db->insert('srp_erp_documentattachments', $dataupload);
                    }
                }
            }



            if ($checkmailid) {
                $this->db->select('email');
                $this->db->from('srp_erp_mfq_customeremail');
                $this->db->where_in('customerEmailAutoID', $checkmailid);
                $result = $this->db->get()->result_array();
                foreach ($result as $val) {
                    $param["empName"] = '';
                    $param["body"] = 'Thank you for forwarding us your valued Inquiry. Based on information furnished, we are pleased to submit our quotation as follows. <br/>
                                          <table border="0px">
                                          </table>';
                    $mailData = [
                        'approvalEmpID' => '',
                        'documentCode' => '',
                        'toEmail' => $val["email"],
                        'subject' => $subject,
                        'param' => $param,
                        'from' => current_companyName()
                    ];
                    send_Email_mfq($mailData,1,$estimateMasterID);
                }
            }
            if ($newkmail) {
                foreach ($newkmail as $val) {
                    $param["empName"] = '';
                    $param["body"] = 'Thank you for forwarding us your valued Inquiry. Based on information furnished, we are pleased to submit our quotation as follows. <br/>
                                          <table border="0px">
                                          </table>';
                    $mailData = [
                        'approvalEmpID' => '',
                        'documentCode' => '',
                        'toEmail' => $val,
                        'subject' => $subject,
                        'param' => $param,
                        'from' => current_companyName()
                    ];
                    send_Email_mfq($mailData,1,$estimateMasterID);

                }
            }

            $this->db->trans_start();
            $companyID = current_companyID();
            $resultemailattach = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID AND documentID = 'ESTMAIL' AND documentSystemCode = $estimateMasterID")->result_array();
            foreach ($resultemailattach as $val)
            {
               if($val['documentSubID'] == 1)
                {
                    unlink($val['myFileName']);
                }

            }
            $this->db->delete('srp_erp_documentattachments', array('documentSystemCode' => $estimateMasterID,'documentID'=>'ESTMAIL'));
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', $this->db->_error_message());
                $this->db->trans_rollback();
            } else {
                foreach($result1 AS $res){
                    $quotationStatus['quotationStatus'] = 1;
                    $this->db->where('ciMasterID', $res['ciMasterID']);
                    $update = $this->db->update('srp_erp_mfq_customerinquiry', $quotationStatus);
                }
               
                return array('s', 'Email Send Successfully.');
                $this->db->trans_commit();
            }
        } else {
            return array('e', 'Please Select an Email ID.');

        }
    }

    function save_estimate_approval()
    {
        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('estimateMasterID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'EST');
        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('estimateMasterID', $system_id);
            $this->db->update('srp_erp_mfq_estimatemaster', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Estimate Approved Successfully');
        }
    }


    function save_additional_order_detail()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $awardedDate = $this->input->post('awardedDate');
        $awardedDate = input_format_date($awardedDate, $date_format_policy);
        $expectedDeliveryDate = $this->input->post('expectedDeliveryDate');
        if ($expectedDeliveryDate) {
            $expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        } else{
            $expectedDeliveryDate = null;
        }
        $last_id = "";
        $codes="";
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->set('exclusions', $this->input->post('exclusions'));
        $this->db->set('designCode', $this->input->post('designCode'));
        $this->db->set('designEditor', $this->input->post('designEditor'));
        $this->db->set('engineeringDrawings', $this->input->post('engineeringDrawings'));
        $this->db->set('engineeringDrawingsComment', $this->input->post('engineeringDrawingsComment'));
        $this->db->set('submissionOfITP', $this->input->post('submissionOfITP'));
        $this->db->set('itpComment', $this->input->post('itpComment'));
        $this->db->set('qcqtDocumentation', $this->input->post('qcqtDocumentation'));
        $this->db->set('scopeOfWork', $this->input->post('scopeOfWork'));
        $this->db->set('mfqSegmentID', $this->input->post('mfqSegmentID'));
        $this->db->set('mfqWarehouseAutoID', $this->input->post('mfqWarehouseAutoID'));
        $this->db->set('orderStatus', $this->input->post('orderStatus'));
        $this->db->set('poNumber', $this->input->post('poNumber'));
        $this->db->set('materialCertificationComment', $this->input->post('materialCertificationComment'));
        $this->db->where('estimateMasterID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatemaster');

        if ($result) {
            if ($this->input->post('materialCertificateID')) {
                $data = [];
                $this->db->delete('srp_erp_mfq_materialcertificate', array('estimateMasterID' => $this->input->post('estimateMasterID')));
                foreach ($this->input->post('materialCertificateID') as $key => $val) {
                    $data[$key]['materialCertificateID'] = $val;
                    $data[$key]['estimateMasterID'] = $this->input->post('estimateMasterID');
                    $data[$key]['companyID'] = $this->common_data['company_data']['company_id'];
                }
                $this->db->insert_batch('srp_erp_mfq_materialcertificate', $data);
            }
           $master =  $this->load_mfq_estimate();


            $this->db->select('segmentCode');
            $this->db->from("srp_erp_mfq_segment");
            $this->db->where("companyID", current_companyID());
            $this->db->where("mfqSegmentID", $this->input->post('mfqSegmentID'));
            $segmentCode = $this->db->get()->row('segmentCode');

            $MasterCurrencyRec = $this->db->query("SELECT transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces FROM `srp_erp_mfq_estimatemaster` WHERE estimateMasterID = {$estimateMasterID}")->row_array();

//            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_job', 'workProcessID', 'companyID');
            $serialInfo = generate_job_SystemCode($this->input->post('mfqSegmentID'), $segmentCode, 'srp_erp_mfq_job');
//            $codes = $this->sequence->mfq_sequence_generator('JOB', $serialInfo['serialNo'],$segmentCode);
            $this->db->set('description', $master["description"]);
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('documentCode', $serialInfo['systemCode']);
            $this->db->set('documentDate', date('Y-m-d'));
            $this->db->set('startDate', date('Y-m-d'));
            $this->db->set('endDate', date('Y-m-d'));
            $this->db->set('manufacturingType', $master["manufacturingType"]);
            /*$this->db->set('mfqItemID', $this->input->post('mfqItemID'));
            $this->db->set('estimateDetailID', $this->input->post('estimateDetailID'));
            $this->db->set('bomMasterID', $this->input->post('bomMasterID'));
            $this->db->set('qty', $this->input->post('expectedQty'));*/
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('mfqSegmentID', $this->input->post('mfqSegmentID'));
            $this->db->set('type', 2);
            $this->db->set('mfqWarehouseAutoID', $this->input->post('mfqWarehouseAutoID'));
            $this->db->set('estimateMasterID', $this->input->post('estimateMasterID'));
            $this->db->set('documentID', 'JOB');
            $this->db->set('levelNo', 1);

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
            $this->db->where("mfqCustomerAutoID", $this->input->post('mfqCustomerAutoID'));
            $custInfo = $this->db->get()->row_array();

            $this->db->set('mfqCustomerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('mfqCustomerCurrency', $custInfo["customerCurrency"]);

            $customer_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $custInfo['customerCurrencyID']);
            $this->db->set('mfqCustomerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('mfqCustomerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);
            $this->db->set('isSaved', 0);
            $this->db->set('isFromEstimate', 1);
            $this->db->set('awardedDate', $awardedDate);
            $this->db->set('expectedDeliveryDate', $expectedDeliveryDate);

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_job');
            $last_id = $this->db->insert_id();


            $estimateMasterID = $this->input->post('estimateMasterID');
            $compID = current_companyID();
            $ciMasterID = $this->db->query("SELECT DISTINCT ciMasterID AS ciMasterID FROM srp_erp_mfq_estimatedetail WHERE companyID = {$compID} AND estimateMasterID = {$estimateMasterID}")->result_array();

            foreach($ciMasterID AS $ci){
                $CIdata['statusID'] = 2;
                $this->db->where('ciMasterID', $ci['ciMasterID']);
                $this->db->where('companyID', $compID);
                $this->db->update('srp_erp_mfq_customerinquiry', $CIdata);
            }
        
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Additional Order detail failed.' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Job No: '.$serialInfo['systemCode'], $this->input->post('estimateMasterID'), $last_id);
        }

    }

    function fetch_job_order_save()
    {
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $workProcessID = trim($this->input->post('workProcessID') ?? '');

        $convertFormat = convert_date_format_sql();
        $this->db->select('DATE_FORMAT(est.createdDateTime,\'' . $convertFormat . '\') as createdDateTime, cust.CustomerName as CustomerName,est.estimateMasterID,est.estimateCode,est.scopeOfWork,est.createdUserName,est.createdUserID,designationMaster.DesDescription,est.exclusions,est.approvedbyEmpName,DATE_FORMAT(est.approvedDate,\'' . $convertFormat . '\') as approvedDate,est.description as jobTitle,est.poNumber,est.designCode,est.designEditor,est.engineeringDrawings,est.submissionOfITP,est.qcqtDocumentation,est.versionLevel,est.deliveryTerms');
        $this->db->from('srp_erp_mfq_estimatemaster est');
        $this->db->join('srp_erp_mfq_customermaster cust', 'cust.mfqCustomerAutoID = est.mfqCustomerAutoID', 'left');
        $this->db->join('srp_employeedesignation designationPD', 'designationPD.EmpDesignationID = est.createdUserID AND designationPD.isActive = 1', 'left');
        $this->db->join('srp_designation designationMaster', 'designationMaster.DesignationID = designationPD.DesignationID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $data["header"] = $this->db->get()->row_array();

        $data["jobMaster"] = $this->MFQ_Job_model->load_job_header();
        $data["certifications"] = $this->load_mfq_estimate_certifications();
        $data["estimateDetail"] = $this->load_mfq_estimate_detail();

        $userInput = $this->input->post();
        $this->db->set('companyID', current_companyID());
        $this->db->set('estimateMasterID', $estimateMasterID);
        $this->db->set('designCode', $userInput["designCode"]);
        $this->db->set('designEditor', $userInput["designEditor"]);
        $this->db->set('addenta', $userInput["addenta"]);
        $this->db->set('paintingSpecifications', $userInput["paintingSpecifications"]);
        $this->db->set('submisionDRG', $userInput["submisionDRG"]);
        $this->db->set('submisionITP', $userInput["submisionITP"]);
        $this->db->set('activity', $userInput["activity"]);
        $this->db->set('heatTreatment', $userInput["heatTreatment"]);
        $this->db->set('pressureTestingPneumatic', $userInput["pressureTestingPneumatic"]);
        $this->db->set('pressureTestingHydro', $userInput["pressureTestingHydro"]);
        $this->db->set('pressureTestingComment', $userInput["pressureTestingComment"]);
        $this->db->set('NDT1Comment', $userInput["NDT1Comment"]);
        $this->db->set('RT', $userInput["RT"]);
        $this->db->set('UT', $userInput["UT"]);
        $this->db->set('RTUTComment', $userInput["RTUTComment"]);
        $this->db->set('NDT2Comment', $userInput["NDT2Comment"]);
        $this->db->set('MPT', $userInput["MPT"]);
        $this->db->set('LPT', $userInput["LPT"]);
        $this->db->set('MPTLPTComment', $userInput["MPTLPTComment"]);
        $this->db->set('inspectionDocumentation', $userInput["inspectionDocumentation"]);
        $this->db->set('remarks', $userInput["remarks"]);
        $this->db->set('deliverycomments', $userInput["deliverycomments"]);
        $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
        $this->db->set('createdUserID', current_userID());
        $this->db->set('createdUserName', current_user());
        $this->db->set('createdDateTime', current_date(true));
        $result = $this->db->insert('srp_erp_mfq_jobordercomments');
        $last_id = $this->db->insert_id();

        if($userInput["materialCertificateID"]){
            foreach ($userInput["materialCertificateID"] as $key => $val){
                $this->db->set('companyID', current_companyID());
                $this->db->set('estimateMasterID', $estimateMasterID);
                $this->db->set('comment', $userInput["materialCertificationComment"][$key]);
                $this->db->set('materialCertificateID', $val);
                $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdUserName', current_user());
                $this->db->set('createdDateTime', current_date(true));
                $result = $this->db->insert('srp_erp_mfq_jobordermccomment');
            }
        }

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_jobordercomments');
        $this->db->where('commentID',$last_id);
        $data['userInput'] = $this->db->get()->row_array();

        $data["type"] = 'pdf';

        $html = $this->load->view('system/mfq/ajax/estimate_job_order_print_preview', $data, true);
        $this->load->library('pdf');
        $path = UPLOAD_PATH_MFQ . $estimateMasterID . "-" . time() . ".pdf";
        $footer = 'Doc No:HEMT-QM-RC-008 Rev.7';
        $this->pdf->save_pdf($html, 'A4', 1, $path,$footer);

        $this->db->select('ed.Ename2,ugd.empID,ed.EEmail');
        $this->db->from('srp_erp_mfq_usergroupdetails ugd');
        $this->db->join('srp_employeesdetails ed', 'ugd.empID = ed.EIdNo');
        $this->db->where_in('ugd.userGroupID', $this->input->post("usergroup"));
        $this->db->where('ugd.companyID', current_companyID());
        $operationEmployees = $this->db->get()->result_array();

        if (!empty($operationEmployees)) {
            foreach ($operationEmployees as $row) {
                $body = "Job Card " . $data["jobMaster"]['documentCode'] . " has been created. Please refer to the attached PDF.<br><br><strong>Client : </strong>" . $data["header"]['CustomerName'] . "<br><strong>Scope : </strong>" . $data["header"]['scopeOfWork'] . "<br><br>Best Regards<br>Quotation Team";

                $param["empName"] = $row['Ename2'];
                $param["body"] = $body;
                $mailData = [
                    'approvalEmpID' => "-",
                    'documentCode' => "-",
                    'toEmail' => $row['EEmail'],
                    'subject' => $data["jobMaster"]['documentCode'] . " - " . $data["header"]['CustomerName'],
                    'param' => $param
                ];
                send_approvalEmail($mailData, 1, $path);
            }
            return array('s', 'Successfully email sent');
        }else{
            return array('e', 'No emails found');
        }
    }
    
    function open_all_notes(){
        $docid = trim($this->input->post('docid') ?? '');
        $typeID = trim($this->input->post('typeID') ?? '');
        $this->db->select('autoID,description');
        $this->db->where('documentID', $docid);
        $this->db->where('typeID', $typeID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->result_array();
        return $data;
    }
    function load_default_note(){
        
        $this->db->select('*');
        $this->db->where('documentID', 'EST');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('isDefault', 1);
        $data = $this->db->get('srp_erp_termsandconditions')->result_array();
        return $data;
    }
    function load_notes(){
        $autoID = trim($this->input->post('allnotedesc') ?? '');
        $this->db->select('description');
        $this->db->where('autoID', $autoID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $data = $this->db->get('srp_erp_termsandconditions')->row_array();
        return $data;
    }
  
    
    function load_mfq_estimate_certifications()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $this->db->select('mcm.Description,mcm.materialCertificateID');
        $this->db->from('srp_erp_mfq_materialcertificate mcd');
        $this->db->join('srp_erp_mfq_materialcertificatemaster mcm', 'mcm.materialCertificateID = mcd.materialCertificateID');
        $this->db->where('mcd.estimateMasterID', $estimateMasterID);
        $result = $this->db->get()->result_array();

        return $result;
    }

    function save_estimate_detail_selling_price(){
        $this->db->trans_start();
        $this->db->set('margin', $this->input->post('margin'));
        $this->db->set('sellingPrice', $this->input->post('sellingPrice'));
        $this->db->set('discountedPrice', $this->input->post('discountedPrice'));
        $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Selling Price Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Selling Price Successfully.');
        }
    }

    function load_mfq_estimate_job_order(){
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_jobordercomments');
        $this->db->where('estimateMasterID',$this->input->post('estimateMasterID'));
        $data = $this->db->get()->row_array();
        return $data;
    }

    function load_mfq_estimate_job_order_mc_comment(){
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_jobordermccomment');
        $this->db->join('srp_erp_mfq_materialcertificatemaster mcm', 'mcm.materialCertificateID = srp_erp_mfq_jobordermccomment.materialCertificateID');
        $this->db->where('estimateMasterID',$this->input->post('estimateMasterID'));
        $data = $this->db->get()->result_array();
        return $data;
    }

    function save_mfq_job(){
        $this->db->trans_start();
        $last_id = "";
        $codes="";
        $estimateMasterID = $this->input->post('estimateMasterID');
        $poNumber = $this->input->post('poNumber');
        $poDate = $this->input->post('poDate');

        $master =  $this->load_mfq_estimate();
        $this->db->select('segmentCode');
        $this->db->from("srp_erp_mfq_segment");
        $this->db->where("companyID", current_companyID());
        $this->db->where("mfqSegmentID", $this->input->post('segmentID'));
        $segmentCode = $this->db->get()->row('segmentCode');

        $MasterCurrencyRec = $this->db->query("SELECT transactionCurrencyID, transactionCurrency, transactionExchangeRate, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingCurrencyID, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces FROM `srp_erp_mfq_estimatemaster` WHERE estimateMasterID = {$estimateMasterID}")->row_array();


            $date_format_policy = date_format_policy();
            $serialInfo = generateMFQ_SystemCode('srp_erp_mfq_job', 'workProcessID', 'companyID');
            $codes = $this->sequence->mfq_sequence_generator('JOB', $serialInfo['serialNo'],$segmentCode);
            $this->db->set('description', $master["description"]);
            $this->db->set('serialNo', $serialInfo['serialNo']);
            $this->db->set('documentCode', $codes);
            $this->db->set('documentDate', date('Y-m-d'));
            $this->db->set('startDate', date('Y-m-d'));
            $this->db->set('endDate', date('Y-m-d'));
            $this->db->set('manufacturingType', $master["manufacturingType"]);
            $this->db->set('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
            $this->db->set('mfqSegmentID', $this->input->post('segmentID'));
            $this->db->set('type', 2);
            $this->db->set('mfqWarehouseAutoID', $this->input->post('warehouseID'));
            $this->db->set('estimateMasterID', $this->input->post('estimateMasterID'));
            $this->db->set('documentID', 'JOB');
            $this->db->set('levelNo', 1);
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
            $this->db->where("mfqCustomerAutoID", $this->input->post('mfqCustomerAutoID'));
            $custInfo = $this->db->get()->row_array();

            $this->db->set('mfqCustomerCurrencyID', $custInfo["customerCurrencyID"]);
            $this->db->set('mfqCustomerCurrency', $custInfo["customerCurrency"]);

            $customer_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $custInfo['customerCurrencyID']);
            $this->db->set('mfqCustomerCurrencyExchangeRate', $customer_currency['conversion']);
            $this->db->set('mfqCustomerCurrencyDecimalPlaces', $customer_currency['DecimalPlaces']);
            $this->db->set('isSaved', 0);
            $this->db->set('isFromEstimate', 1);

            $this->db->set('poNumber', $poNumber);
            $this->db->set('poDate', date('Y-m-d',strtotime($poDate)));

            $this->db->set('companyID', current_companyID());
            $this->db->set('createdPCID', gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $this->db->set('createdUserID', current_userID());
            $this->db->set('createdUserName', current_user());
            $this->db->set('createdDateTime', current_date(true));

            $result = $this->db->insert('srp_erp_mfq_job');
            $last_id = $this->db->insert_id();

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Additional Order detail failed.' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Job No: '.$codes, $this->input->post('estimateMasterID'), $last_id);
        }

    }

    function delete_estimate()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_estimatedetail');
        $this->db->where('estimateMasterID', trim($this->input->post('estimateMasterID') ?? ''));
        $estimateDetails = $this->db->get()->row_array();
       
        if (empty($estimateDetails)) {
            $data = array(
                'isDeleted' => 1,
                'deletedByEmpID' => current_userID(),
                'deletedDatetime' => current_date(),
            );
            $this->db->where('estimateMasterID', trim($this->input->post('estimateMasterID') ?? ''));
            $this->db->update('srp_erp_mfq_estimatemaster', $data);
            $this->session->set_flashdata('s', 'Estimate Deleted Successfully.');
            return true;
        } else {
            $this->session->set_flashdata('e', 'please delete all estimate details before deleting this Estimate.');
            return true;
        }
    }

    function validate_item()
    {
        $estimateMasterID = $this->input->post('estimateMasterID');
        $companyID = current_companyID();
        $data['status'] = 0;

        $data['item'] = $this->db->query("SELECT srp_erp_mfq_itemmaster.mfqItemID, itemSystemCode, secondaryItemCode, defaultUnitOfMeasure, itemName, itemDescription FROM srp_erp_mfq_estimatedetail
	                                      LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_estimatedetail.mfqItemID 
                                          WHERE estimateMasterID = {$estimateMasterID} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} 
                                          AND (mfqCategoryID <= 0 OR mfqSubCategoryID <= 0 OR itemType = 0 OR mainCategoryID = 0 OR subcategoryID = 0)
                                        ")->result_array();

//        $items = $this->db->query("SELECT mfqItemID FROM srp_erp_mfq_estimatedetail WHERE estimateMasterID = {$estimateMasterID} AND companyID = {$companyID}")->result_array();

//        $data['thirdPartyService'] = array();
//        foreach ($items AS $item) {

        $thirdPartyService = $this->db->query("SELECT srp_erp_mfq_overhead.overHeadID, overHeadCode, srp_erp_mfq_overhead.description AS overHeadDescription
                    FROM srp_erp_mfq_bom_overhead
                    LEFT JOIN srp_erp_mfq_billofmaterial ON srp_erp_mfq_billofmaterial.bomMasterID = srp_erp_mfq_bom_overhead.bomMasterID
                    JOIN srp_erp_mfq_overhead ON srp_erp_mfq_overhead.overHeadID = srp_erp_mfq_bom_overhead.overheadID
                    LEFT JOIN srp_erp_mfq_estimatedetail ON srp_erp_mfq_estimatedetail.mfqItemID = srp_erp_mfq_billofmaterial.mfqItemID
                    WHERE estimateMasterID = {$estimateMasterID} AND(financeGLAutoID IS NULL OR mfqSegmentID IS NULL) AND srp_erp_mfq_bom_overhead.companyID = {$companyID} AND srp_erp_mfq_billofmaterial.companyID = {$companyID}")->result_array();
        $data['thirdPartyService'] = $thirdPartyService;
//        }
//        echo '<pre>'; print_r($data['thirdPartyService']);

        if(!empty($data['thirdPartyService']) || !empty($data['item'])) {
            $data['status'] = 1;
            return $data;
        }

        $data['notLinkedItem'] = $this->db->query("SELECT srp_erp_mfq_itemmaster.mfqItemID, itemSystemCode, IFNULL(secondaryItemCode, ' - ') AS secondaryItemCode, defaultUnitOfMeasure, itemName, itemDescription FROM srp_erp_mfq_estimatedetail
	LEFT JOIN srp_erp_mfq_itemmaster ON srp_erp_mfq_itemmaster.mfqItemID = srp_erp_mfq_estimatedetail.mfqItemID 
WHERE estimateMasterID = {$estimateMasterID} AND srp_erp_mfq_estimatedetail.companyID = {$companyID} AND srp_erp_mfq_itemmaster.itemAutoID IS NULL")->result_array();

        if(!empty($data['notLinkedItem'])) {
            $data['status'] = 2;
        }
        return $data;
    }

    function change_discount_view()
    {
        $data = array(
            'showDiscountYN' => trim($this->input->post('discountView') ?? ''),
        );
        $this->db->where('estimateMasterID', trim($this->input->post('estimateMasterID') ?? ''));
        $this->db->update('srp_erp_mfq_estimatemaster', $data);
        return true;
    }

    function estimate_item_cost()
    {
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $cost = trim($this->input->post('cost') ?? '');
        $this->db->SELECT("transactionCurrencyID");
        $this->db->FROM('srp_erp_mfq_estimatemaster');
        $this->db->where('estimateMasterID', $estimateMasterID);
        $data = $this->db->get()->row_array();
        $default_currency = currency_conversionID($this->common_data['company_data']['company_default_currencyID'], $data['transactionCurrencyID']);
        $conversion = $default_currency['conversion'];

        return ($cost / $conversion);
    }

    function fetch_estimate_details()
    {
        $data = array();
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $result = $this->db->query("SELECT
	((discountedPrice * (( 100 + IFNULL( totMargin, 0 ))/ 100 )) * (( 100 - IFNULL( totDiscount, 0 ))/ 100 )) AS estimateValue,
	srp_erp_currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
	srp_erp_currencymaster.CurrencyCode AS transactionCurrency,
	DATE_FORMAT( est.documentDate, '{$convertFormat}' ) AS documentDate,
	est.description AS description,
	cust.CustomerName AS CustomerName,
	est.estimateMasterID AS estimateMasterID,
	est.estimateCode AS estimateCode,
	est.confirmedYN AS confirmedYN,
	est.submissionStatus AS submissionStatus,
	statusColor,
	statusBackgroundColor,
	srp_erp_mfq_status.description AS statusDescription,
	estd.dueDate AS dueDate,
	job.estimateMasterID AS estimateMasterIDJob,
	est.approvedYN AS approvedYN,
	job.workProcessID AS workProcessID,
	docApp.docApprovedYN AS docApprovedYN,
	est.isMailSent AS isMailSent,
	IFNULL( segment.segmentCode, '-' ) AS depcode,
	est.isDeleted AS isDeleted 
FROM
	`srp_erp_mfq_estimatemaster` `est`
	LEFT JOIN `srp_erp_mfq_customermaster` `cust` ON `cust`.`mfqCustomerAutoID` = `est`.`mfqCustomerAutoID`
	LEFT JOIN `srp_erp_mfq_status` ON `srp_erp_mfq_status`.`statusID` = `est`.`submissionStatus`
	LEFT JOIN ( SELECT estimateMasterID, workProcessID FROM srp_erp_mfq_job WHERE ( isDeleted IS NULL OR isDeleted != 1 ) GROUP BY estimateMasterID ) job ON `job`.`estimateMasterID` = `est`.`estimateMasterID`
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
WHERE est.companyID = {$companyID} 
ORDER BY estimateMasterID DESC")->result_array();

        if($result) {
            $a = 1;
            foreach ($result AS $val) {
                $det['recordNo'] = $a;
                $det['estimateCode'] = $val['estimateCode'];
                $det['documentDate'] = $val['documentDate'];
                $det['segment'] = $val['depcode'];
                $det['CustomerName'] = $val['CustomerName'];
                $det['description'] = $val['description'];
                $det['transactionCurrency'] = $val['transactionCurrency'];
                $det['estimateValue'] = number_format($val['estimateValue'], $val['transactionCurrencyDecimalPlaces'], '.', '');

                if ($val['approvedYN'] == 0) {
                    if ($val['confirmedYN'] == 0 && $val['submissionStatus'] == 6) {
                        $det['confirmedYN'] = 'Revised';
                    } else if ($val['confirmedYN'] == 0 || $val['confirmedYN'] == 3) {
                        $det['confirmedYN'] = 'Pending';
                    } else if ($val['confirmedYN'] == 2) {
                        $det['confirmedYN'] = 'Rejected';
                    } else {
                        if ($val['submissionStatus'] == 6) {
                            $det['confirmedYN'] = 'Revised';
                        } else {
                            $det['confirmedYN'] = 'Pending';
                        }
                    }
                } elseif ($val['approvedYN'] == 1) {
                    if ($val['confirmedYN'] == 1) {
                        if ($val['submissionStatus'] == 6) {
                            $det['confirmedYN'] = 'Revised';
                        } else {
                            $det['confirmedYN'] = 'Approved';
                        }
                    } else {
                        $det['confirmedYN'] = ' ';
                    }
                } elseif ($val['approvedYN'] == 2) {
                    $det['confirmedYN'] = ' ';
                } elseif ($val['approvedYN'] == 6) {
                    $det['confirmedYN'] = ' ';
                } else {
                    $det['confirmedYN'] = '-';
                }

                if ($val['isMailSent'] == 1) {
                    $det['estimateStatus'] = 'Submitted';
                } else {
                    if ($val['dueDate'] < date('Y-m-d')) {
                        $det['estimateStatus'] = 'Overdue';
                    } else {
                        $det['estimateStatus'] = 'Open';
                    }
                }

                $a++;
                array_push($data, $det);
            }
        }

        return $data;
    }

    function fetch_assigned_revenue_gl()
    {
        $mfqItemID = $this->input->post('mfqItemID');
        $glAutoID = $this->db->query("SELECT revenueGLAutoID FROM srp_erp_mfq_itemmaster WHERE mfqItemID = {$mfqItemID}")->row('revenueGLAutoID');
        return $glAutoID;
    }

    function update_mfq_linked_item()
    {
        $linkedItemAutoID = $this->input->post('linkedItemAutoID');
        $data['itemAutoID'] = $linkedItemAutoID;
        $this->db->where('mfqItemID', $this->input->post('mfqItemID'));
        $result = $this->db->update('srp_erp_mfq_itemmaster', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Linked ERP Item Updated Successfully!');
            return array('status' => true);
        }
        else{
            $this->session->set_flashdata('e', 'Linked ERP Item Update Failed');
            return array('status' => false);
        }
    }

    function upload_attachment_for_estimate()
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
            $this->db->join('srp_erp_mfq_job', 'srp_erp_mfq_job.estimateMasterID = srp_erp_mfq_estimatemaster.estimateMasterID', 'LEFT');
            $this->db->join('srp_erp_mfq_deliverynotedetail', 'srp_erp_mfq_deliverynotedetail.jobID = srp_erp_mfq_job.workProcessID', 'LEFT');
            $this->db->join('srp_erp_mfq_customerinvoicemaster', 'srp_erp_mfq_deliverynotedetail.deliveryNoteID = srp_erp_mfq_customerinvoicemaster.deliveryNoteID', 'LEFT');
            $this->db->join('srp_erp_customerinvoicemaster', 'srp_erp_customerinvoicemaster.mfqInvoiceAutoID = srp_erp_mfq_customerinvoicemaster.invoiceAutoID', 'LEFT');
            $this->db->where('srp_erp_mfq_estimatemaster.estimateMasterID', trim($this->input->post('documentSystemCode') ?? ''));
            $this->db->where('srp_erp_mfq_customerinvoicemaster.confirmedYN', 1);
            $this->db->where('srp_erp_mfq_estimatemaster.companyID', $companyID);
            $invoiceIDs = $this->db->get('srp_erp_mfq_estimatemaster')->row_array();

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

    function validate_item_pulled()
    {
        $mfqItemID = trim($this->input->post('mfqItemID') ?? '');
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $compID = current_companyID();
        $data = $this->db->query("SELECT estimateMasterID as estimateMasterID FROM srp_erp_mfq_estimatedetail WHERE companyID = {$compID} AND mfqItemID = {$mfqItemID} AND estimateMasterID != {$estimateMasterID}")->row_array();
        if(empty($data)) {
            $itemAutoID = $this->db->query("SELECT itemAutoID FROM srp_erp_mfq_itemmaster WHERE companyID = {$compID} AND mfqItemID = {$mfqItemID}")->row('itemAutoID');
            return array('msg' => 's', 'itemAutoID' => $itemAutoID);
        }
        return array('msg' => 'e', 'itemAutoID' => null);
    }

    function load_estimate_detail_items(){
        $estimateMasterID = trim($this->input->post('estimateMasterID') ?? '');
        $jobID = "''";
        $this->db->select('estm.estimateMasterID,itemSystemCode,IFNULL(jobQty, 0) AS jobQty, sumedQty - IFNULL(jobQty, 0) AS balanceQty, itemDescription,IFNULL(UnitDes,"") as UnitDes,expectedQty, sumedQty, estimatedCost,(expectedQty*estimatedCost) as totalCost,est.estimateDetailID,est.companyLocalCurrencyDecimalPlaces,est.transactionCurrencyDecimalPlaces,ciCode,est.margin,est.sellingPrice,est.estimateMasterID,est.mfqItemID,bomm.bomMasterID,est.discount,est.discountedPrice,estm.mfqCustomerAutoID,estm.description,srp_erp_mfq_itemmaster.itemType,CONCAT(itemDescription," (",itemSystemCode,")") as concatItemDescription');
        $this->db->from('srp_erp_mfq_estimatedetail est');
        $this->db->join('( SELECT SUM( expectedQty ) AS sumedQty, mfqItemID, estimateMasterID FROM srp_erp_mfq_estimatedetail GROUP BY estimateMasterID, mfqItemID ) estQty','estQty.mfqItemID = est.mfqItemID AND est.estimateMasterID = estQty.estimateMasterID','left');
        $this->db->join('( SELECT SUM( qty ) AS jobQty,estimateDetailID FROM srp_erp_mfq_job GROUP BY  estimateDetailID) createdQty', 'createdQty.estimateDetailID = est.estimateDetailID', 'left');
        $this->db->join('srp_erp_mfq_estimatemaster estm','estm.estimateMasterID = est.estimateMasterID','left');
        $this->db->join('srp_erp_mfq_itemmaster','est.mfqItemID = srp_erp_mfq_itemmaster.mfqItemID','left');
        $this->db->join('srp_erp_unit_of_measure','unitID = defaultUnitOfMeasureID','left');
        $this->db->join('srp_erp_mfq_customerinquiry','est.ciMasterID = srp_erp_mfq_customerinquiry.ciMasterID','left');
        $this->db->join('(SELECT ((IFNULL(bmc.materialCharge,0) + IFNULL(lt.totalValue,0) + IFNULL(oh.totalValue,0))/bom.Qty) as cost,bom.mfqItemID,bom.bomMasterID FROM srp_erp_mfq_billofmaterial bom LEFT JOIN (SELECT SUM(materialCharge) as materialCharge,bomMasterID FROM srp_erp_mfq_bom_materialconsumption GROUP BY bomMasterID) bmc ON bmc.bomMasterID = bom.bomMasterID  LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_labourtask GROUP BY bomMasterID) lt ON lt.bomMasterID = bom.bomMasterID LEFT JOIN (SELECT SUM(totalValue) as totalValue,bomMasterID FROM srp_erp_mfq_bom_overhead GROUP BY bomMasterID) oh ON oh.bomMasterID = bom.bomMasterID  GROUP BY mfqItemID) bomm', 'bomm.mfqItemID = est.mfqItemID', 'left');
        $this->db->where('est.estimateMasterID', $estimateMasterID);
        $result = $this->db->get()->result_array();

        $itemIDs = array_column($result, 'mfqItemID');
        
        $template = $this->db->select('master.templateMasterID,master.templateDescription,itm_temp.mfqItemID')
                             ->from('srp_erp_mfq_workflowtemplateitems itm_temp')
                             ->join('srp_erp_mfq_templatemaster master','itm_temp.workFlowTemplateID = master.templateMasterID','left')
                             ->where_in('mfqItemID',$itemIDs)
                             ->order_by('mfqItemID')
                             ->group_by('master.templateMasterID,mfqItemID')
                             ->get()
                             ->result_array();
                      
        $template = array_group_by($template, 'mfqItemID');   
        
        foreach ($result as $key => $value) {
            $itemid = $value['mfqItemID'];
            $result[$key]['_item_template'] = [];

            if(array_key_exists($itemid, $template)){
                $result[$key]['_item_template'] = $template[$itemid];
            }
            
            
        }

        return $result;
    }


    function save_allottedManhours()
    {
        $this->db->trans_start();
        $this->db->set('allotedManHrs', $this->input->post('allotedManHrs'));
        $this->db->where('estimateDetailID', $this->input->post('estimateMasterID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Alloted ManHrs updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Alloted ManHrs Updated Successfully.');
        }
    }

    function save_unitSellingPrice()
    {
        $this->db->trans_start();
        $this->db->set('unitSellingPrice', $this->input->post('unitSellingPrice'));
        $this->db->where('estimateDetailID', $this->input->post('estimateDetailID'));
        $result = $this->db->update('srp_erp_mfq_estimatedetail');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Unit SellingPrice updated Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Unit SellingPrice Updated Successfully.');
        }
    }

    function save_estimate_proposal_approval(){

        $this->db->trans_start();
        $this->load->library('approvals');
        $system_id = trim($this->input->post('proposalID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('po_status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');
        $approvals_status = $this->approvals->approve_document($system_id, $level_id, $status, $comments, 'ESTP');
        if ($approvals_status == 1) {
            $data['approvedYN'] = $status;
            $data['approvedbyEmpID'] = $this->common_data['current_userID'];
            $data['approvedbyEmpName'] = $this->common_data['current_user'];
            $data['approvedDate'] = $this->common_data['current_date'];
            $this->db->where('proposalID', $system_id);
            $this->db->update('srp_erp_mfq_estimateproposalreview', $data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('s', 'Error occurred');
        } else {
            $this->db->trans_commit();
            return array('s', 'Estimate Approved Successfully');
        }
    }
}