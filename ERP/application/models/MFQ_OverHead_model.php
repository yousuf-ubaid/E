<?php
class MFQ_OverHead_model extends ERP_Model
{
    function save_item_master(){
        $this->db->trans_start();
        if(!empty(trim($this->input->post('revanue') ?? '') && trim($this->input->post('revanue') != 'Select Revenue GL Account'))){
            $revanue        = explode('|', trim($this->input->post('revanue') ?? ''));
        }
        $cost           = explode('|', trim($this->input->post('cost') ?? ''));
        $asste          = explode('|', trim($this->input->post('asste') ?? ''));
        $mainCategory   = explode('|', trim($this->input->post('mainCategory') ?? ''));
        $isactive = 0;
        if(!empty($this->input->post('isActive'))){
            $isactive = 1;
        }

        $data['isActive']                               = $isactive;
        $data['seconeryItemCode']                       = trim($this->input->post('seconeryItemCode') ?? '');
        $data['itemName']                               = clear_descriprions(trim($this->input->post('itemName') ?? ''));
        $data['itemDescription']                        = clear_descriprions(trim($this->input->post('itemDescription') ?? ''));
        $data['subcategoryID']                          = trim($this->input->post('subcategoryID') ?? '');
        $data['subSubCategoryID']                       = trim($this->input->post('subSubCategoryID') ?? '');
        $data['partNo']                                 = trim($this->input->post('partno') ?? '');
        $data['reorderPoint']                           = trim($this->input->post('reorderPoint') ?? '');
        $data['maximunQty']                             = trim($this->input->post('maximunQty') ?? '');
        $data['minimumQty']                             = trim($this->input->post('minimumQty') ?? '');
        $data['barcode']                                = trim($this->input->post('barcode') ?? '');
        $data['comments']                               = trim($this->input->post('comments') ?? '');
        $data['modifiedPCID']                           = $this->common_data['current_pc'];
        $data['modifiedUserID']                         = $this->common_data['current_userID'];
        $data['modifiedUserName']                       = $this->common_data['current_user'];
        $data['modifiedDateTime']                       = $this->common_data['current_date'];
        $data['companyLocalCurrencyID']                 = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency']                   = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate']               = 1;
        $data['companyLocalSellingPrice']               = trim($this->input->post('companyLocalSellingPrice') ?? '');
        $data['companyLocalCurrencyDecimalPlaces']      = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency']               = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID']               = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'],$data['companyReportingCurrency']);
        $data['companyReportingExchangeRate']           = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces']  = $reporting_currency['DecimalPlaces'];
        $data['companyReportingSellingPrice']           = ($data['companyLocalSellingPrice']/$data['companyReportingExchangeRate']);

        if (trim($this->input->post('itemAutoID') ?? '')) {
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->update('srp_erp_itemmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item : ' .$data['itemSystemCode'].' - '. $data['itemName'] .  ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                //$this->lib_log->log_event('Item','Error','Item : ' .$data['itemSystemCode'].' - '. $data['itemName'] . ' Update Failed '.$this->db->_error_message(),'Item');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item : ' . $data['itemName']. ' Updated Successfully.');
                $this->db->trans_commit();
                //$this->lib_log->log_event('Item','Success','Item : ' . $data['companyCode'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Item');
                return array('status' => true, 'last_id' => $this->input->post('itemAutoID'));
            }
        } else {
            $uom           = explode('|', trim($this->input->post('uom') ?? ''));
            $this->load->library('sequence');
            // $this->db->select('codePrefix');
            // $this->db->where('itemCategoryID', $this->input->post('mainCategoryID'));
            // $code = $this->db->get('srp_erp_itemcategory')->row_array();
            $data['isActive']                     = $isactive;
            $data['itemImage']                    = 'no-image.png';
            $data['defaultUnitOfMeasureID']       = trim($this->input->post('defaultUnitOfMeasureID') ?? '');
            $data['defaultUnitOfMeasure']         = trim($uom[0] ?? '');
            $data['mainCategoryID']               = trim($this->input->post('mainCategoryID') ?? '');
            $data['mainCategory']                 = trim($mainCategory[1] ?? '');
            $data['financeCategory']              = $this->finance_category($data['mainCategoryID']);
            $data['assteGLAutoID']                = trim($this->input->post('assteGLAutoID') ?? '');
            $data['faCostGLAutoID']               = trim($this->input->post('COSTGLCODEdes') ?? '');
            $data['faACCDEPGLAutoID']             = trim($this->input->post('ACCDEPGLCODEdes') ?? '');
            $data['faDEPGLAutoID']                = trim($this->input->post('DEPGLCODEdes') ?? '');
            $data['faDISPOGLAutoID']              = trim($this->input->post('DISPOGLCODEdes') ?? '');

            if ($data['mainCategory']=='Fixed Assets') {
                $data['assteGLAutoID']                = trim($this->input->post('assteGLAutoID') ?? '');
                /*                $data['assteSystemGLCode']            = trim($asste[0] ?? '');
                                $data['assteGLCode']                  = trim($asste[1] ?? '');
                                $data['assteDescription']             = trim($asste[2] ?? '');
                                $data['assteType']                    = trim($asste[3] ?? '');
                                $data['revanueGLAutoID']              = trim($this->input->post('revanueGLAutoID') ?? '');
                                $data['revanueSystemGLCode']          = trim($revanue[0] ?? '');
                                $data['revanueGLCode']                = trim($revanue[1] ?? '');
                                $data['revanueDescription']           = trim($revanue[2] ?? '');
                                $data['revanueType']                  = trim($revanue[3] ?? '');*/
                $data['faCostGLAutoID']                  = trim($this->input->post('COSTGLCODEdes') ?? '');
                $data['faACCDEPGLAutoID']                  = trim($this->input->post('ACCDEPGLCODEdes') ?? '');
                $data['faDEPGLAutoID']                  = trim($this->input->post('DEPGLCODEdes') ?? '');
                $data['faDISPOGLAutoID']                  = trim($this->input->post('DISPOGLCODEdes') ?? '');

                $data['costGLAutoID']                 = '';
                $data['costSystemGLCode']             = '';
                $data['costGLCode']                   = '';
                $data['costDescription']              = '';
                $data['costType']                     = '';
            }elseif($data['mainCategory']=='Service' or $data['mainCategory']=='Non Inventory'){
                $data['assteGLAutoID']                = '';
                $data['assteSystemGLCode']            = '';
                $data['assteGLCode']                  = '';
                $data['assteDescription']             = '';
                $data['assteType']                    = '';
                $data['revanueGLAutoID']              = trim($this->input->post('revanueGLAutoID') ?? '');
                if(!empty($revanue)){
                    $data['revanueSystemGLCode']          = trim($revanue[0] ?? '');
                    $data['revanueGLCode']                = trim($revanue[1] ?? '');
                    $data['revanueDescription']           = trim($revanue[2] ?? '');
                    $data['revanueType']                  = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID']                 = trim($this->input->post('costGLAutoID') ?? '');
                $data['costSystemGLCode']             = trim($cost[0] ?? '');
                $data['costGLCode']                   = trim($cost[1] ?? '');
                $data['costDescription']              = trim($cost[2] ?? '');
                $data['costType']                     = trim($cost[3] ?? '');
            }else{
                $data['assteGLAutoID']                = trim($this->input->post('assteGLAutoID') ?? '');
                $data['assteSystemGLCode']            = trim($asste[0] ?? '');
                $data['assteGLCode']                  = trim($asste[1] ?? '');
                $data['assteDescription']             = trim($asste[2] ?? '');
                $data['assteType']                    = trim($asste[3] ?? '');
                $data['revanueGLAutoID']              = trim($this->input->post('revanueGLAutoID') ?? '');
                if(!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID']                 = trim($this->input->post('costGLAutoID') ?? '');
                $data['costSystemGLCode']             = trim($cost[0] ?? '');
                $data['costGLCode']                   = trim($cost[1] ?? '');
                $data['costDescription']              = trim($cost[2] ?? '');
                $data['costType']                     = trim($cost[3] ?? '');
            }
            $data['companyLocalWacAmount']        = 0.00;
            $data['companyReportingWacAmount']    = 0.00;
            $data['companyID']                    = $this->common_data['company_data']['company_id'];
            $data['companyCode']                  = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']             = $this->common_data['user_group'];
            $data['createdPCID']                  = $this->common_data['current_pc'];
            $data['createdUserID']                = $this->common_data['current_userID'];
            $data['createdUserName']              = $this->common_data['current_user'];
            $data['createdDateTime']              = $this->common_data['current_date'];
            $data['itemSystemCode']               = $this->sequence->sequence_generator(trim($mainCategory[0] ?? ''));
            $this->db->insert('srp_erp_itemmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item : ' .$data['itemSystemCode'].' - '.$data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item : ' .$data['itemSystemCode'].' - '.$data['itemSystemCode'].' - '. $data['itemName'] .  ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }


    function save_over_head(){

        $flowserve = getPolicyValues('MANFL', 'All');

        $itemAutoID = $this->input->post('itemAutoID');
        $itemDetails = null;


        if($itemAutoID){
            $itemDetails = fetch_item_data($itemAutoID);
        }

        if (!$this->input->post('overHeadID')) {
            $typeID = $this->input->post('service_type');
            if($this->input->post('service_type') == 1) {
                $ohid="OH";
            } else {
                $ohid="TPS";
            }
            $companycode=$this->common_data['company_data']['company_code'];
            $lastoverHeadID = $this->db->query("SELECT COUNT(overHeadID) as overHeadID FROM srp_erp_mfq_overhead WHERE overHeadCategoryID = 1 AND typeID = {$typeID}")->row_array();
            if(!empty($lastoverHeadID)){
                $new_overHeadID = $lastoverHeadID['overHeadID'] + 1;
            }else{
                $new_overHeadID = 1;
            }
            $overCode=str_pad($new_overHeadID, 6, '0', STR_PAD_LEFT);
            $overHeadCode=$companycode.'/'.$ohid.$overCode;
            $this->db->set('typeID', $this->input->post('service_type') );
            $this->db->set('description', $this->input->post('description') );
            $this->db->set('overHeadCode', $overHeadCode );
            $this->db->set('unitOfMeasureID', $this->input->post('unitOfMeasureID') );
            $this->db->set('financeGLAutoID', $this->input->post('financeGLAutoID') );
            $this->db->set('rate', $this->input->post('rate') );
            $this->db->set('mfqSegmentID', $this->input->post('mfqSegmentID') );
            $this->db->set('companyID', $this->common_data['company_data']['company_id'] );
            $this->db->set('overHeadCategoryID', $this->input->post('overHeadCategoryID') );

            if($itemAutoID){
                $this->db->set('erpItemAutoID', $itemAutoID);
                $this->db->set('description', $itemDetails['itemDescription']);
                $this->db->set('overHeadCode', $itemDetails['itemSystemCode']);
                $this->db->set('financeGLAutoID', $itemDetails['costGLAutoID']);
            }

            $data['supplierAutoID'] = $this->input->post('supplierAutoID');

            $result = $this->db->insert('srp_erp_mfq_overhead');

            $last_id = $this->db->insert_id();

            if($flowserve =='FlowServe'){
                $date_format_policy = date_format_policy();
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $to_date_format = input_format_date($to_date, $date_format_policy);
                $from_date_format = input_format_date($from_date, $date_format_policy);
    
                $data_flow['overheadType'] =1;
                $data_flow['overheadCateoryID'] =$last_id;
                $data_flow['rate'] =$this->input->post('rate');
                $data_flow['startFrom'] = $from_date_format;
                $data_flow['startTo'] =$to_date_format;
                $data_flow['createdByempID'] =$this->common_data['current_userID'];
                $data_flow['createdDateTime'] =$this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
            }

            if ($result) {
                return array('s', 'Record Inserted successfully');
            }else{
                return array('e', 'Record Insertion Failed');
            }
        } else {
            $last_id =$this->input->post('overHeadID');
            $data['typeID'] = $this->input->post('service_type');
            $data['description'] = $this->input->post('description');
            $data['unitOfMeasureID'] = $this->input->post('unitOfMeasureID');
            $data['financeGLAutoID'] = $this->input->post('financeGLAutoID');
            $data['overHeadCategoryID'] = $this->input->post('overHeadCategoryID');
            $data['mfqSegmentID'] = $this->input->post('mfqSegmentID');
            $data['rate'] = $this->input->post('rate');

            if($itemAutoID){
                $this->db->set('erpItemAutoID', $itemAutoID);
                $this->db->set('description', $itemDetails['itemDescription']);
                $this->db->set('overHeadCode', $itemDetails['itemSystemCode']);
                $this->db->set('financeGLAutoID', $itemDetails['costGLAutoID']);
            }

            $data['supplierAutoID'] = $this->input->post('supplierAutoID');

            $this->db->where('overHeadID', $this->input->post('overHeadID'));
            $result = $this->db->update('srp_erp_mfq_overhead', $data);

            if($flowserve =='FlowServe'){
                $date_format_policy = date_format_policy();
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $to_date_format = input_format_date($to_date, $date_format_policy);
                $from_date_format = input_format_date($from_date, $date_format_policy);
    
                $data_flow['overheadType'] = 1;
                $data_flow['overheadCateoryID'] =$last_id;
                $data_flow['rate'] =$this->input->post('rate');
                $data_flow['startFrom'] = $from_date_format;
                $data_flow['startTo'] =$to_date_format;
                $data_flow['createdByempID'] =$this->common_data['current_userID'];
                $data_flow['createdDateTime'] =$this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
            }

            if ($result) {
                return array('s', 'Record Updated successfully');
            }else{
                return array('s', 'Record Insertion Failed');
            }
        }

    }


    function save_labour(){
        $flowserve = getPolicyValues('MANFL', 'All');
        if (!$this->input->post('overHeadID')) {
            $ohid="LB";
            $companycode=$this->common_data['company_data']['company_code'];;
            $lastoverHeadID = $this->db->query("SELECT COUNT(overHeadID) as overHeadID FROM srp_erp_mfq_overhead WHERE overHeadCategoryID = 2")->row_array();
            if(!empty($lastoverHeadID)){
                $new_overHeadID = $lastoverHeadID['overHeadID'] + 1;
            }else{
                $new_overHeadID = 1;
            }
            $overCode=str_pad($new_overHeadID, 6, '0', STR_PAD_LEFT);
            $overHeadCode=$companycode.'/'.$ohid.$overCode;
            $this->db->set('description', $this->input->post('description') );
            $this->db->set('overHeadCode', $overHeadCode );
            $this->db->set('unitOfMeasureID', $this->input->post('unitOfMeasureID') );
            $this->db->set('mfqSegmentID', $this->input->post('mfqSegmentID') );
            $this->db->set('mfqsubSegmentID', $this->input->post('mfqsubSegmentID') );
            $this->db->set('rate', $this->input->post('rate') );
            $this->db->set('financeGLAutoID', $this->input->post('financeGLAutoID') );
            $this->db->set('companyID', $this->common_data['company_data']['company_id'] );
            $this->db->set('overHeadCategoryID', $this->input->post('overHeadCategoryID') );
            $result = $this->db->insert('srp_erp_mfq_overhead');

            $last_id = $this->db->insert_id();

            if($flowserve =='FlowServe'){
                $date_format_policy = date_format_policy();
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $to_date_format = input_format_date($to_date, $date_format_policy);
                $from_date_format = input_format_date($from_date, $date_format_policy);
    
                $data_flow['overheadType'] =3;
                $data_flow['overheadCateoryID'] =$last_id;
                $data_flow['rate'] =$this->input->post('rate');
                $data_flow['startFrom'] = $from_date_format;
                $data_flow['startTo'] =$to_date_format;
                $data_flow['createdByempID'] =$this->common_data['current_userID'];
                $data_flow['createdDateTime'] =$this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
            }
            if ($result) {
                return array('s', 'Record Inserted successfully');
            }else{
                return array('e', 'Record Insertion Failed');
            }
        } else {
            $last_id =$this->input->post('overHeadID');
            $data['description'] = $this->input->post('description');
            $data['unitOfMeasureID'] = $this->input->post('unitOfMeasureID');
            $data['mfqSegmentID'] = $this->input->post('mfqSegmentID');
            $data['mfqsubSegmentID'] = $this->input->post('mfqsubSegmentID');
            $data['rate'] = $this->input->post('rate');
            $data['financeGLAutoID'] = $this->input->post('financeGLAutoID');
            $data['overHeadCategoryID'] = $this->input->post('overHeadCategoryID');

            $this->db->where('overHeadID', $this->input->post('overHeadID'));
            $result = $this->db->update('srp_erp_mfq_overhead', $data);

            if($flowserve =='FlowServe'){
                $date_format_policy = date_format_policy();
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $to_date_format = input_format_date($to_date, $date_format_policy);
                $from_date_format = input_format_date($from_date, $date_format_policy);
    
                $data_flow['overheadType'] =3;
                $data_flow['overheadCateoryID'] =$last_id;
                $data_flow['rate'] =$this->input->post('rate');
                $data_flow['startFrom'] = $from_date_format;
                $data_flow['startTo'] =$to_date_format;
                $data_flow['createdByempID'] =$this->common_data['current_userID'];
                $data_flow['createdDateTime'] =$this->common_data['current_date'];

                $this->db->insert('srp_erp_mfq_rate_effective_period`', $data_flow);
            }

            if ($result) {
                return array('s', 'Record Updated successfully');
            }else{
                return array('s', 'Record Insertion Failed');
            }
        }

    }

    function editOverHead(){
        $overHeadID=$this->input->post('overHeadID');
        $data = $this->db->query("select * from srp_erp_mfq_overhead where overHeadID=$overHeadID")->row_array();

        $rate_current = $data['rate'];

        $data_rate = $this->db->query("select * from srp_erp_mfq_rate_effective_period where overheadCateoryID={$overHeadID} AND overheadType=1 AND rate={$rate_current} ORDER BY createdDateTime DESC")->result_array();
        $data_rate_3 = $this->db->query("select * from srp_erp_mfq_rate_effective_period where overheadCateoryID={$overHeadID} AND overheadType=3 AND rate={$rate_current} ORDER BY createdDateTime DESC")->result_array();
        //echo $this->db->last_query();

        $data['dates'] = count($data_rate)>0?$data_rate[0]:[];
        $data['dates_labour'] = count($data_rate_3)>0?$data_rate_3[0]:[];

        return $data;
    }

    function fetch_itemrecord()
    {
        $companyCode = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query('SELECT overHeadCode as itemSystemCode,description as itemDescription,overHeadID as itemAutoID,unitOfMeasureID as defaultUnitOfMeasure,CONCAT(description, " (" ,overHeadCode,")") AS "Match" FROM srp_erp_mfq_overhead WHERE (overHeadCode LIKE "' . $search_string . '" OR overHeadCode LIKE "' . $search_string . '" AND companyID = "' . $companyCode . '")')->result_array();
    }

    function fetch_related_uom_id(){
        $this->db->select('unitOfMeasureID as defaultUnitOfMeasureID,unitShortCode as defaultUnitOfMeasure,description as itemDescription');
        $this->db->from('srp_erp_mfq_overhead');
        $this->db->join('srp_erp_unit_of_measure', 'srp_erp_unit_of_measure.UnitID = srp_erp_mfq_overhead.unitOfMeasureID');
        $this->db->where('overHeadID',$this->input->post('itemAutoID'));
        return $this->db->get()->row_array();
    }
}
