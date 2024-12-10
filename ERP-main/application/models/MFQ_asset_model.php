<?php

class MFQ_asset_model extends ERP_Model
{
    function add_asset()
    {
        $result = $this->db->query('INSERT INTO srp_erp_mfq_fa_asset_master ( faID, companyID, companyCode, segmentID, segmentCode, docOriginSystemCode, docOrigin, docOriginDetailID, documentID, faAssetDept, barcode, serialNo, faCode, isFromGRV, grvAutoID, assetCodeS, faUnitSerialNo, assetDescription, comments, groupTO, dateAQ, dateDEP, depMonth, DEPpercentage, faCatID, faSubCatID, faSubCatID2, faSubCatID3, transactionCurrencyID, transactionCurrency, transactionCurrencyExchangeRate, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate, companyReportingAmount, companyReportingDecimalPlaces, auditCategory, partNumber, manufacture, unitAssign, unitAssignHistory, image, usedBy, usedByHistory, location, currentLocation, locationHistory, selectedForDisposal, disposed, disposedDate, dateDisposed, assetdisposalMasterAutoID, reasonDisposed, cashDisposal, costAtDisposal, ACCDEPDIP, profitLossDisposal, technicalHistory, costGLAutoID, costGLCode, costGLCodeDes, ACCDEPGLAutoID, ACCDEPGLCODE, ACCDEPGLCODEdes, DEPGLAutoID, DEPGLCODE, DEPGLCODEdes, DISPOGLAutoID, DISPOGLCODE, DISPOGLCODEdes, isPostToGL, postGLAutoID, postGLCode, postGLCodeDes, postDate, confirmedYN, confirmedByName, confirmedByEmpID, confirmedDate, approvedYN, approvedDate, currentLevelNo, approvedbyEmpID, approvedbyEmpName, createdUserGroup, selectedYN, assetType, supplierID, tempRecord, toolsCondition, selectedforJobYN, missingDepAmount, createdPCID, createdUserID, createdDateTime, createdUserName, modifiedPCID, modifiedUserID, modifiedDateTime, modifiedUserName, `timestamp` ) 
                                SELECT faID, companyID, companyCode, segmentID, segmentCode, docOriginSystemCode, docOrigin, docOriginDetailID, documentID, faAssetDept, barcode, serialNo, faCode, isFromGRV, grvAutoID, assetCodeS, faUnitSerialNo, assetDescription, comments, groupTO, dateAQ, dateDEP, depMonth, DEPpercentage, faCatID, faSubCatID, faSubCatID2, faSubCatID3, transactionCurrencyID, transactionCurrency, transactionCurrencyExchangeRate, transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID, companyReportingCurrency, companyReportingExchangeRate, companyReportingAmount, companyReportingDecimalPlaces, auditCategory, partNumber, manufacture, unitAssign, unitAssignHistory, image, usedBy, usedByHistory, location, currentLocation, locationHistory, selectedForDisposal, disposed, disposedDate, dateDisposed, assetdisposalMasterAutoID, reasonDisposed, cashDisposal, costAtDisposal, ACCDEPDIP, profitLossDisposal, technicalHistory, costGLAutoID, costGLCode, costGLCodeDes, ACCDEPGLAutoID, ACCDEPGLCODE, ACCDEPGLCODEdes, DEPGLAutoID, DEPGLCODE, DEPGLCODEdes, DISPOGLAutoID, DISPOGLCODE, DISPOGLCODEdes, isPostToGL, postGLAutoID, postGLCode, postGLCodeDes, postDate, confirmedYN, confirmedByName, confirmedByEmpID, confirmedDate, approvedYN, approvedDate, currentLevelNo, approvedbyEmpID, approvedbyEmpName, createdUserGroup, selectedYN, assetType, supplierID, tempRecord, toolsCondition, selectedforJobYN, missingDepAmount, createdPCID, createdUserID, createdDateTime, createdUserName, modifiedPCID, modifiedUserID, modifiedDateTime, modifiedUserName, `timestamp` FROM srp_erp_fa_asset_master 
                                WHERE companyID = ' . current_companyID() . '  AND faID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function update_srp_erp_mfq_fa_asset_master($id, $data = array())
    {
        $this->db->where('mfq_faID', $id);
        $result = $this->db->update('srp_erp_mfq_fa_asset_master', $data);
        return $result;
    }


    function insert_machine()
    {
        $flowserve = getPolicyValues('MANFL', 'All');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_fa_asset_master');
        $this->db->where('assetDescription', trim($this->input->post('assetDescription') ?? ''));
        $this->db->where('companyID', current_companyID());
        $machine = $this->db->get()->row_array();

        if (!$machine) {
            $post = $this->input->post();
            unset($post['mfqItemID']);
            unset($post['from_date']);
            unset($post['to_date']);
            $codes = generateMFQ_SystemCode('srp_erp_mfq_fa_asset_master', 'mfq_faID', 'companyID');

            $datetime = format_date_mysql_datetime();
            $post['serialNo'] = $codes['serialNo'];
            $post['faCode'] = $codes['systemCode'];
            $post['assetDescription'] = $this->input->post('assetDescription');
            $post['companyID'] = current_companyID();
            $post['companyCode'] = current_companyCode();
            $post['createdUserID'] = current_userID();
            $post['createdUserName'] = current_user();
            $post['createdDateTime'] = $datetime;
            $post['createdPCID'] = current_pc();
            $post['timestamp'] = $datetime;
            $post['isFromERP'] = 0;
            $post['unitRate'] = $this->input->post('unitRate');

            $result = $this->db->insert('srp_erp_mfq_fa_asset_master', $post);
            $last_id = $this->db->insert_id();
            if($flowserve =='FlowServe'){
                $date_format_policy = date_format_policy();
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $to_date_format = input_format_date($to_date, $date_format_policy);
                $from_date_format = input_format_date($from_date, $date_format_policy);
    
                $data_flow['overheadType'] =2;
                $data_flow['overheadCateoryID'] =$last_id;
                $data_flow['rate'] =$this->input->post('unitRate');
                $data_flow['startFrom'] = $from_date_format;
                $data_flow['startTo'] =$to_date_format;
                $data_flow['createdByempID'] =$this->common_data['current_userID'];
                $data_flow['createdDateTime'] =$this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
            }

            if ($result) {
                return array('error' => 0, 'message' => 'Machine successfully Added', 'code' => 1);
            } else {
                return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
            }
        } else {
            return array('error' => 1, 'message' => 'This machine name already exist!, please try different item names');
        }
    }

    function get_srp_erp_mfq_fa_asset_master($mfq_faID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_fa_asset_master');
        $this->db->where('mfq_faID', $mfq_faID);
        $result = $this->db->get()->row_array();
        $rate_current =$result['unitRate'];
        $data_rate = $this->db->query("select * from srp_erp_mfq_rate_effective_period where overheadCateoryID={$mfq_faID} AND overheadType=2 AND rate={$rate_current} ORDER BY createdDateTime DESC")->result_array();
        //echo $this->db->last_query();
        $result['dates']=count($data_rate)>0?$data_rate[0]:[];

        return $result;
    }

    function update_machine()
    {
        $post = $this->input->post();
        
        unset($post['mfq_faID']);
        unset($post['from_date']);
        unset($post['to_date']);
        $flowserve = getPolicyValues('MANFL', 'All');
        $datetime = format_date_mysql_datetime();
        $post['assetDescription'] = $this->input->post('assetDescription');
        $post['partNumber'] = $this->input->post('partNumber');
        $post['glAutoID'] = $this->input->post('glAutoID');

        $post['modifiedUserID'] = current_userID();
        $post['modifiedUserName'] = current_user();
        $post['modifiedDateTime'] = $datetime;
        $post['modifiedPCID'] = current_pc();
        $post['unitRate'] = $this->input->post('unitRate');


        $this->db->where('mfq_faID', $this->input->post('mfq_faID'));
        $result = $this->db->update('srp_erp_mfq_fa_asset_master', $post);

        if($flowserve =='FlowServe'){
            $date_format_policy = date_format_policy();
            $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
            $to_date_format = input_format_date($to_date, $date_format_policy);
            $from_date_format = input_format_date($from_date, $date_format_policy);

            $data_flow['overheadType'] =2;
            $data_flow['overheadCateoryID'] =$this->input->post('mfq_faID');
            $data_flow['rate'] =$this->input->post('unitRate');
            $data_flow['startFrom'] = $from_date_format;
            $data_flow['startTo'] =$to_date_format;
            $data_flow['createdByempID'] =$this->common_data['current_userID'];
            $data_flow['createdDateTime'] =$this->common_data['current_date'];

            $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
        }
        //echo $this->db->last_query();
        if ($result) {
            return array('error' => 0, 'message' => 'Machine successfully updated', 'code' => 2);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }
    function link_asset()
    {
        $result = $this->db->query("SELECT faID, companyID, companyCode, segmentID, segmentCode, docOriginSystemCode, docOrigin,
docOriginDetailID, documentID, faAssetDept, barcode, serialNo, faCode, isFromGRV, grvAutoID, assetCodeS, faUnitSerialNo, assetDescription, comments,
groupTO, dateAQ, dateDEP, depMonth, DEPpercentage, faCatID, faSubCatID, faSubCatID2, faSubCatID3, transactionCurrencyID, transactionCurrency, transactionCurrencyExchangeRate,
transactionAmount, transactionCurrencyDecimalPlaces, companyLocalCurrencyID, companyLocalCurrency, companyLocalExchangeRate, companyLocalAmount, companyLocalCurrencyDecimalPlaces, companyReportingCurrencyID,
companyReportingCurrency, companyReportingExchangeRate, companyReportingAmount, companyReportingDecimalPlaces, auditCategory, partNumber, manufacture, unitAssign, unitAssignHistory, image, usedBy,

 usedByHistory, location, currentLocation, locationHistory, selectedForDisposal, disposed,
disposedDate, dateDisposed, assetdisposalMasterAutoID, reasonDisposed, cashDisposal, costAtDisposal, ACCDEPDIP, profitLossDisposal, technicalHistory, costGLAutoID, costGLCode, costGLCodeDes,
 ACCDEPGLAutoID, ACCDEPGLCODE, ACCDEPGLCODEdes, DEPGLAutoID, DEPGLCODE, DEPGLCODEdes, DISPOGLAutoID,
 DISPOGLCODE, DISPOGLCODEdes, isPostToGL, postGLAutoID, postGLCode, postGLCodeDes, postDate, confirmedYN,
 confirmedByName, confirmedByEmpID, confirmedDate, approvedYN, approvedDate, currentLevelNo, approvedbyEmpID, approvedbyEmpName,
 createdUserGroup, selectedYN, assetType, supplierID, tempRecord, toolsCondition, selectedforJobYN, missingDepAmount, createdPCID, createdUserID, createdDateTime, createdUserName, modifiedPCID, modifiedUserID, modifiedDateTime,
 modifiedUserName, `timestamp` FROM srp_erp_fa_asset_master WHERE companyID= " . current_companyID() . " AND faID =" . $this->input->post('selectedItemsSync') . " ")->row_array();

     if($result)
     {
         $this->db->set('faID', $result["faID"]);
         $this->db->set('companyID', $result["companyID"]);
         $this->db->set('companyCode', $result["companyCode"]);
         $this->db->set('segmentID', $result["segmentID"]);
         $this->db->set('segmentCode', $result["segmentCode"]);
         $this->db->set('docOriginSystemCode', $result["docOriginSystemCode"]);
         $this->db->set('docOrigin', $result["docOrigin"]);
         $this->db->set('docOriginDetailID', $result["docOriginDetailID"]);
         $this->db->set('documentID', $result["documentID"]);
         $this->db->set('faAssetDept', $result["faAssetDept"]);
         $this->db->set('barcode', $result["barcode"]);
         $this->db->set('serialNo', $result["serialNo"]);
         $this->db->set('faCode', $result["faCode"]);
         $this->db->set('isFromGRV', $result["isFromGRV"]);
         $this->db->set('grvAutoID', $result["grvAutoID"]);
         $this->db->set('assetCodeS', $result["assetCodeS"]);
         $this->db->set('faUnitSerialNo', $result["faUnitSerialNo"]);
         $this->db->set('comments', $result["comments"]);
         $this->db->set('groupTO', $result["groupTO"]);
         $this->db->set('dateAQ', $result["dateAQ"]);
         $this->db->set('dateDEP', $result["dateDEP"]);
         $this->db->set('depMonth', $result["depMonth"]);
         $this->db->set('DEPpercentage', $result["DEPpercentage"]);
         $this->db->set('faCatID', $result["faCatID"]);
         $this->db->set('faSubCatID', $result["faSubCatID"]);
         $this->db->set('faSubCatID2', $result["faSubCatID2"]);
         $this->db->set('faSubCatID3', $result["faSubCatID3"]);
         $this->db->set('transactionCurrencyID', $result["transactionCurrencyID"]);
         $this->db->set('transactionCurrency', $result["transactionCurrency"]);
         $this->db->set('transactionCurrencyExchangeRate', $result["transactionCurrencyExchangeRate"]);
         $this->db->set('transactionAmount', $result["transactionAmount"]);
         $this->db->set('transactionCurrencyDecimalPlaces', $result["transactionCurrencyDecimalPlaces"]);
         $this->db->set('companyLocalCurrencyID', $result["companyLocalCurrencyID"]);
         $this->db->set('companyLocalCurrency', $result["companyLocalCurrency"]);
         $this->db->set('companyLocalExchangeRate', $result["companyLocalExchangeRate"]);
         $this->db->set('companyLocalAmount', $result["companyLocalAmount"]);
         $this->db->set('companyLocalCurrencyDecimalPlaces', $result["companyLocalCurrencyDecimalPlaces"]);
         $this->db->set('companyReportingCurrencyID', $result["companyReportingCurrencyID"]);
         $this->db->set('companyReportingCurrency', $result["companyReportingCurrency"]);
         $this->db->set('companyReportingExchangeRate', $result["companyReportingExchangeRate"]);
         $this->db->set('companyReportingAmount', $result["companyReportingAmount"]);
         $this->db->set('companyReportingDecimalPlaces', $result["companyReportingDecimalPlaces"]);
         $this->db->set('auditCategory', $result["auditCategory"]);
         $this->db->set('unitAssign', $result["unitAssign"]);
         $this->db->set('unitAssignHistory', $result["unitAssignHistory"]);
         $this->db->set('image', $result["image"]);
         $this->db->set('usedBy', $result["usedBy"]);
         $this->db->set('usedByHistory', $result["usedByHistory"]);
         $this->db->set('location', $result["location"]);
         $this->db->set('currentLocation', $result["currentLocation"]);
         $this->db->set('locationHistory', $result["locationHistory"]);
         $this->db->set('selectedForDisposal', $result["selectedForDisposal"]);
         $this->db->set('disposed', $result["disposed"]);
         $this->db->set('disposedDate', $result["disposedDate"]);
         $this->db->set('dateDisposed', $result["dateDisposed"]);
         $this->db->set('assetdisposalMasterAutoID', $result["assetdisposalMasterAutoID"]);
         $this->db->set('reasonDisposed', $result["reasonDisposed"]);
         $this->db->set('cashDisposal', $result["cashDisposal"]);
         $this->db->set('costAtDisposal', $result["costAtDisposal"]);
         $this->db->set('ACCDEPDIP', $result["ACCDEPDIP"]);
         $this->db->set('profitLossDisposal', $result["profitLossDisposal"]);
         $this->db->set('technicalHistory', $result["technicalHistory"]);
         $this->db->set('costGLAutoID', $result["costGLAutoID"]);
         $this->db->set('costGLCode', $result["costGLCode"]);
         $this->db->set('costGLCodeDes', $result["costGLCodeDes"]);
         $this->db->set('ACCDEPGLAutoID', $result["ACCDEPGLAutoID"]);
         $this->db->set('ACCDEPGLCODE', $result["ACCDEPGLCODE"]);
         $this->db->set('ACCDEPGLCODEdes', $result["ACCDEPGLCODEdes"]);
         $this->db->set('DEPGLAutoID', $result["DEPGLAutoID"]);
         $this->db->set('DEPGLCODE', $result["DEPGLCODE"]);
         $this->db->set('DEPGLCODEdes', $result["DEPGLCODEdes"]);
         $this->db->set('DISPOGLAutoID', $result["DISPOGLAutoID"]);
         $this->db->set('DISPOGLCODE', $result["DISPOGLCODE"]);
         $this->db->set('DISPOGLCODEdes', $result["DISPOGLCODEdes"]);
         $this->db->set('isPostToGL', $result["isPostToGL"]);
         $this->db->set('postGLAutoID', $result["postGLAutoID"]);
         $this->db->set('postGLCode', $result["postGLCode"]);
         $this->db->set('postGLCodeDes', $result["postGLCodeDes"]);
         $this->db->set('postDate', $result["postDate"]);
         $this->db->set('confirmedYN', $result["confirmedYN"]);
         $this->db->set('confirmedByName', $result["confirmedByName"]);
         $this->db->set('confirmedByEmpID', $result["confirmedByEmpID"]);
         $this->db->set('confirmedDate', $result["confirmedDate"]);
         $this->db->set('approvedYN', $result["approvedYN"]);
         $this->db->set('approvedDate', $result["approvedDate"]);
         $this->db->set('currentLevelNo', $result["currentLevelNo"]);
         $this->db->set('approvedbyEmpID', $result["approvedbyEmpID"]);
         $this->db->set('approvedbyEmpName', $result["approvedbyEmpName"]);
         $this->db->set('createdUserGroup', $result["createdUserGroup"]);
         $this->db->set('selectedYN', $result["selectedYN"]);
         $this->db->set('assetType', $result["assetType"]);
         $this->db->set('supplierID', $result["supplierID"]);
         $this->db->set('tempRecord', $result["tempRecord"]);
         $this->db->set('toolsCondition', $result["toolsCondition"]);
         $this->db->set('selectedforJobYN', $result["selectedforJobYN"]);
         $this->db->set('missingDepAmount', $result["missingDepAmount"]);
         $this->db->set('createdPCID', $result["createdPCID"]);
         $this->db->set('createdUserID', $result["createdUserID"]);
         $this->db->set('createdDateTime', $result["createdDateTime"]);
         $this->db->set('createdUserName', $result["createdUserName"]);
         $this->db->set('modifiedPCID', $result["modifiedPCID"]);
         $this->db->set('modifiedUserID', $result["modifiedUserID"]);
         $this->db->set('modifiedDateTime', $result["modifiedDateTime"]);
         $this->db->set('modifiedUserName', $result["modifiedUserName"]);
         $this->db->set('isFromErp', 1);
         $this->db->where('mfq_faID', $this->input->post('mfq_faID'));
         $update = $this->db->update('srp_erp_mfq_fa_asset_master');
         if ($update) {
             $this->session->set_flashdata('s', 'Records added Successfully');
             return array('status' => true);
         }
         else{
             $this->session->set_flashdata('e', 'Records adding failed');
             return array('status' => false);
         }
     }

    }


}
