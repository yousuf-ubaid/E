<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_request_modal extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_purchase_request_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDate') ?? '');
        $Pqrdate = trim($this->input->post('documentDate') ?? '');
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $format_POdate = input_format_date($Pqrdate, $date_format_policy);


        $singleSourcePR = getPolicyValues('SSPR', 'All');
        $enableOperationM = getPolicyValues('EOM', 'All');
        $supplierSelection = getPolicyValues('SSFPR', 'All');
       
        if($singleSourcePR==1){
            $single_source_val = trim($this->input->post('single_source_val') ?? '');
            $single_narration = trim($this->input->post('single_narration') ?? '');

            $data['isSingleSourcePr'] = $single_source_val; 
            $data['singleSourceComment'] =  $single_narration;
        }
        
        if($enableOperationM==1){
            $contractID = trim($this->input->post('contractID') ?? '');
            $data['contractID'] = $contractID;
        }

        if($supplierSelection==1){
            $supplierID_pr = trim($this->input->post('supplierID_pr') ?? '');
            $data['supplierAutoID'] = $supplierID_pr;
        }

        $severityType = trim($this->input->post('severityType') ?? '');

        if($severityType){
            $data['severityType'] = $severityType;
        }

        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $currency_code = explode('|', trim($this->input->post('currency_code') ?? ''));

        $data['documentID'] = 'PRQ';
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        $data['requestedEmpID'] = trim($this->input->post('requestedEmpID') ?? '');
        $data['requestedByName'] = trim($this->input->post('requestedByName') ?? '');
        $data['itemCategoryID'] = trim($this->input->post('itemCatType') ?? '');
        $data['isTechSpecRequired'] = trim($this->input->post('is_tech_spec_val') ?? '');

        $narration = ($this->input->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);

        $data['transactionCurrency'] = trim($this->input->post('transactionCurrency') ?? '');
        $data['referenceNumber'] = trim($this->input->post('referenceNumber') ?? '');
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['documentDate'] = $format_POdate;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['jobNumber'] = trim($this->input->post('jobNumber') ?? '');
        $data['jobID'] = trim($this->input->post('workProcessID') ?? '');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrency'] = trim($currency_code[0] ?? '');
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

        //update manufacture items from the job
        // $this->pull_manufacture_items_jobs(75);  exit;

        if (trim($this->input->post('purchaseRequestID') ?? '')) {
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
            $this->db->update('srp_erp_purchaserequestmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Purchase Request Updated Successfully.');
                $this->db->trans_commit();
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$data['supplierName'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                return array('status' => true, 'last_id' => $this->input->post('purchaseRequestID'));
            }
        } else {
            //$this->load->library('sequence');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            //$data['purchaseRequestCode'] = $this->sequence->sequence_generator($data['documentID']);
            $this->db->insert('srp_erp_purchaserequestmaster', $data);
            $last_id = $this->db->insert_id();

            //update manufacture items from the job
            $this->pull_manufacture_items_jobs($last_id);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Purchase Request Save Failed ' . $this->db->_error_message());
                //$this->lib_log->log_event('Purchase Order','Error','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Failed '.$this->db->_error_message(),'Purchase Order');
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                update_warehouse_items();
                update_item_master();
                $this->session->set_flashdata('s', 'Purchase Request Saved Successfully.');
                //$this->lib_log->log_event('Purchase Order','Success','Purchase Order For : ( '.$data['supplierCode'].' ) '.$this->input->post('desc') . ' Save Successfully. Affected Rows - ' . $this->db->affected_rows(),'Purchase Order');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function pull_manufacture_items_jobs($purchaseRequestID){

        $this->db->where('purchaseRequestID',$purchaseRequestID);
        $master = $this->db->from('srp_erp_purchaserequestmaster')->get()->row_array();

        $date = date('Y-m-d');


        if($master){

            $jobID = $master['jobID'];

            if($jobID){


                $materialItems = $this->db->query(
                    "  
                        SELECT mitemcon.*, itemMaster.currentStock,itemMaster.itemAutoID,itemMaster.defaultUnitOfMeasureID,itemMaster.defaultUnitOfMeasure
                        FROM srp_erp_mfq_jc_materialconsumption as mitemcon
                        LEFT JOIN srp_erp_mfq_itemmaster as mfqItemMaster ON mitemcon.mfqItemID = mfqItemMaster.mfqItemID
                        LEFT JOIN srp_erp_itemmaster as itemMaster ON mfqItemMaster.itemAutoID = itemMaster.itemAutoID
                        WHERE mitemcon.workProcessID = '{$jobID}' AND mitemcon.companyID = '{$this->common_data['company_data']['company_id']}' AND itemMaster.mainCategory != 'Service'
                    "
                )->result_array();

             
                
                $itemAutoID = array();
                $expectedDeliveryDateDetail = array();
                $UnitOfMeasureID = array();
                $quantityRequested = array();
                $estimatedAmount = array();
                $uom = array();
                $comment = array();
                $discount = array();
                $estimated = array();

                foreach($materialItems as $item){

                   
                    $item_estimated = ($item['qtyUsed'] - $item['usageQty']);

                    if($item['currentStock'] >= $item_estimated){
                        continue;
                    }
                    
                    $itemAutoID[] = $item['itemAutoID'];
                    $expectedDeliveryDateDetail[] = date('Y-m-d',strtotime($master['expectedDeliveryDate']));
                    $UnitOfMeasureID[] = $item['defaultUnitOfMeasureID'];
                    $quantityRequested[] = $item_estimated - $item['currentStock'];//$item['qtyUsed'];
                    $estimatedAmount[] = $item['unitCost'];
                    $uom[] = $item['defaultUnitOfMeasure'];
                    $estimated[] = $item_estimated - $item['currentStock']; //$item['qtyUsed'];
                    $comment[] = '';
                    $discount[] = '';

                }

                $_POST['purchaseRequestID'] = $purchaseRequestID;
                $_POST['itemAutoID'] = $itemAutoID;
                $_POST['expectedDeliveryDateDetail'] = $expectedDeliveryDateDetail;
                $_POST['UnitOfMeasureID'] = $UnitOfMeasureID;
                $_POST['quantityRequested'] = $quantityRequested;
                $_POST['estimatedAmount'] = $estimatedAmount;
                $_POST['uom'] = $uom;
                $_POST['comment'] = $comment;
                $_POST['discount'] = $discount;
                $_POST['estimated'] = $estimated;
          
                //update purchase request details
                $res = $this->save_purchase_request_detail();

            }

        }

        return TRUE;

    }

    function save_uom()
    {
        $this->db->trans_start();
        $data['UnitShortCode'] = trim($this->input->post('UnitShortCode') ?? '');
        $data['UnitDes'] = trim($this->input->post('UnitDes') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('UnitID') ?? '')) {
            $this->db->where('UnitID', trim($this->input->post('UnitID') ?? ''));
            $this->db->update('srp_erp_unit_of_measure', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Update Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('UnitID'));
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_unit_of_measure', $data);
            $last_id = $this->db->insert_id();
            $this->db->insert('srp_erp_unitsconversion', array('masterUnitID' => $last_id, 'subUnitID' => $last_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'companyID' => $this->common_data['company_data']['company_id']));

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Unit of measure Save Failed ');
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Unit of measure Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function save_uom_conversion()
    {
        //$this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->insert('srp_erp_unitsconversion', $data);
        $last_id = $this->db->insert_id();

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $this->db->insert('srp_erp_unitsconversion', $data);
        //$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Save Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function save_inv_tax_detail()
    {
        $this->db->select('taxMasterAutoID');
        $this->db->where('purchaseOrderAutoID', $this->input->post('purchaseOrderID'));
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $tax_detail = $this->db->get('srp_erp_purchaseordertaxdetails')->row_array();
        if (!empty($tax_detail)) {
            return array('status' => 1, 'type' => 'w', 'data' => ' Tax Detail added already ! ');
        }

        $this->db->trans_start();
        $this->db->select('*');
        $this->db->where('taxMasterAutoID', $this->input->post('text_type'));
        $master = $this->db->get('srp_erp_taxmaster')->row_array();

        $this->db->select('transactionCurrency,transactionExchangeRate,transactionCurrencyDecimalPlaces ,transactionCurrencyID, companyLocalCurrency, companyLocalExchangeRate,companyLocalCurrencyDecimalPlaces, companyReportingCurrency, companyReportingExchangeRate, companyReportingCurrencyDecimalPlaces,companyLocalCurrencyID, companyReportingCurrencyID');
        $this->db->where('purchaseOrderID', $this->input->post('purchaseOrderID'));
        $inv_master = $this->db->get('srp_erp_purchaseordermaster')->row_array();

        $data['purchaseOrderAutoID'] = trim($this->input->post('purchaseOrderID') ?? '');
        $data['taxMasterAutoID'] = $master['taxMasterAutoID'];
        $data['taxDescription'] = $master['taxDescription'];
        $data['taxShortCode'] = $master['taxShortCode'];
        $data['supplierAutoID'] = $master['supplierAutoID'];
        $data['supplierSystemCode'] = $master['supplierSystemCode'];
        $data['supplierName'] = $master['supplierName'];
        $data['supplierCurrencyID'] = $master['supplierCurrencyID'];
        $data['supplierCurrency'] = $master['supplierCurrency'];
        $data['supplierCurrencyDecimalPlaces'] = $master['supplierCurrencyDecimalPlaces'];
        $data['GLAutoID'] = $master['supplierGLAutoID'];
        $data['systemGLCode'] = $master['supplierGLSystemGLCode'];
        $data['GLCode'] = $master['supplierGLAccount'];
        $data['GLDescription'] = $master['supplierGLDescription'];
        $data['GLType'] = $master['supplierGLType'];
        $data['taxPercentage'] = trim($this->input->post('percentage') ?? '');
        $data['transactionAmount'] = trim($this->input->post('amount') ?? '');
        $data['transactionCurrencyID'] = $inv_master['transactionCurrencyID'];
        $data['transactionCurrency'] = $inv_master['transactionCurrency'];
        $data['transactionExchangeRate'] = $inv_master['transactionExchangeRate'];
        $data['transactionCurrencyDecimalPlaces'] = $inv_master['transactionCurrencyDecimalPlaces'];
        $data['companyLocalCurrencyID'] = $inv_master['companyLocalCurrencyID'];
        $data['companyLocalCurrency'] = $inv_master['companyLocalCurrency'];
        $data['companyLocalExchangeRate'] = $inv_master['companyLocalExchangeRate'];
        $data['companyReportingCurrencyID'] = $inv_master['companyReportingCurrencyID'];
        $data['companyReportingCurrency'] = $inv_master['companyReportingCurrency'];
        $data['companyReportingExchangeRate'] = $inv_master['companyReportingExchangeRate'];

        $supplierCurrency = currency_conversion($data['transactionCurrency'], $data['supplierCurrency']);
        $data['supplierCurrencyExchangeRate'] = $supplierCurrency['conversion'];
        $data['supplierCurrencyDecimalPlaces'] = $supplierCurrency['DecimalPlaces'];
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('taxDetailAutoID') ?? '')) {
            $this->db->where('taxDetailAutoID', trim($this->input->post('taxDetailAutoID') ?? ''));
            $this->db->update('srp_erp_purchaseordertaxdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === 0) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Update Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Updated Successfully.', 'last_id' => $this->input->post('taxDetailAutoID'));
            }
        } else {
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_purchaseordertaxdetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('status' => 0, 'type' => 'e', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Save Failed ');
            } else {
                $this->db->trans_commit();
                return array('status' => 1, 'type' => 's', 'data' => 'Tax Detail : ' . $data['GLDescription'] . ' Saved Successfully.', 'last_id' => $last_id);
            }
        }
    }

    function change_conversion()
    {
        $this->db->trans_start();
        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_unitsconversion', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Unit of measure conversion Update Failed ');
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Unit of measure conversion Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_purchase_request_detail()
    {
        $purchaseRequestDetailsID = $this->input->post('purchaseRequestDetailsID');
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $itemAutoIDs = $this->input->post('itemAutoID');
        $UnitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $uom = $this->input->post('uom');
        $estimatedAmount = $this->input->post('estimatedAmount');
        $quantityRequested = $this->input->post('quantityRequested');
        $discount = $this->input->post('discount');
        $comment = $this->input->post('comment');
        $expectedDelDate = $this->input->post('expectedDeliveryDateDetail');
        $estimated = $this->input->post('estimated');
        $activityCode = $this->input->post('activityCode');
        

        $this->db->trans_start();
       
        foreach ($itemAutoIDs as $key => $itemAutoID) {

            $item_arr = fetch_item_data($itemAutoID);
            $uomEx = explode('|', $uom[$key]);
            if (!$purchaseRequestDetailsID) {
                $this->db->select('purchaseRequestID,itemDescription,itemSystemCode');
                $this->db->from('srp_erp_purchaserequestdetails');
                $this->db->where('itemType', 'Inventory');
                $this->db->where('purchaseRequestID', $purchaseRequestID);
                $this->db->where('itemAutoID', $itemAutoID);
                $order_detail = $this->db->get()->row_array();
                if (!empty($order_detail)) {
                    return array('w', 'Purchase Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
                }
            }

            $date_format_policy = date_format_policy();
            $expectedDeliveryDate = $expectedDelDate[$key];
            $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);

     

            $data['purchaseRequestID'] = $purchaseRequestID;
            $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
            $data['itemAutoID'] = $itemAutoID;
            $data['itemSystemCode'] = $item_arr['itemSystemCode'];
            $data['itemType'] = $item_arr['mainCategory'];
            $data['itemDescription'] = $item_arr['itemDescription'];
            $data['unitOfMeasure'] = trim($uomEx[0] ?? '');
            $data['unitOfMeasureID'] = $UnitOfMeasureID[$key];
            $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
            $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
            $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
            $data['discountPercentage'] = $discount[$key];
            $data['discountAmount'] = ($estimatedAmount[$key] / 100) * $discount[$key];
            $data['requestedQty'] = $quantityRequested[$key];
            $data['unitAmount'] = ($estimatedAmount[$key] - $data['discountAmount']);
            $data['totalAmount'] = ($data['unitAmount'] * $quantityRequested[$key]);
            $data['activityCodeID'] = $activityCode[$key] ?? '';
            $data['comment'] = $comment[$key] ?? '';
            $data['remarks'] = '';

            if(isset($estimated[$key])){
                $data['estimatedQty'] = $estimated[$key];
            }

           

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            // $data['GRVSelectedYN'] = 0;
            //$data['goodsRecievedYN'] = 0;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_purchaserequestdetails', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Purchase Order Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Purchase Order Details :  Saved Successfully.');
        }

    }

    function update_purchase_request_detail()
    {

        if (!empty($this->input->post('purchaseRequestDetailsID'))) {
            $this->db->select('purchaseRequestID,itemDescription,itemSystemCode');
            $this->db->from('srp_erp_purchaserequestdetails');
            $this->db->where('itemType', 'Inventory');
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->where('purchaseRequestDetailsID !=', trim($this->input->post('purchaseRequestDetailsID') ?? ''));
            $order_detail = $this->db->get()->row_array();
            if (!empty($order_detail)) {
                return array('w', 'Purchase Request Detail : ' . $order_detail['itemSystemCode'] . ' ' . $order_detail['itemDescription'] . '  already exists.');
            }
        }
        $this->db->trans_start();
        $item_arr = fetch_item_data(trim($this->input->post('itemAutoID') ?? ''));
        $uom = explode('|', $this->input->post('uom'));
        $date_format_policy = date_format_policy();
        $advancecostPolicy = getPolicyValues('ACC', 'All'); 
        $expectedDeliveryDate = trim($this->input->post('expectedDeliveryDateDetailEdit') ?? '');
        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate, $date_format_policy);
        $data['purchaseRequestID'] = trim($this->input->post('purchaseRequestID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemAutoID') ?? '');
        $data['expectedDeliveryDate'] = $format_expectedDeliveryDate;
        $data['itemType'] = $item_arr['mainCategory'];
        $data['itemSystemCode'] = $item_arr['itemSystemCode'];
        $data['itemDescription'] = $item_arr['itemDescription'];
        $data['unitOfMeasure'] = trim($uom[0] ?? '');
        $data['unitOfMeasureID'] = trim($this->input->post('UnitOfMeasureID') ?? '');
        $data['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
        $data['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
        $data['conversionRateUOM'] = conversionRateUOM_id($data['unitOfMeasureID'], $data['defaultUOMID']);
        $data['discountPercentage'] = trim($this->input->post('discount') ?? '');
        $data['discountAmount'] = (trim($this->input->post('estimatedAmount') ?? '') / 100) * trim($this->input->post('discount') ?? '');
        $data['requestedQty'] = trim($this->input->post('quantityRequested') ?? '');
        $data['unitAmount'] = (trim($this->input->post('estimatedAmount') ?? '') - $data['discountAmount']);
        $data['totalAmount'] = ($data['unitAmount'] * trim($this->input->post('quantityRequested') ?? ''));
        
        if($advancecostPolicy==1){
            $data['activityCodeID'] = trim($this->input->post('activityCodeEdit') ?? '');
        }

        if($advancecostPolicy==1){
            $data['isbudegted'] =  $this->input->post('isbudget');
        }

        $data['comment'] = trim($this->input->post('comment') ?? '');
        $data['remarks'] = trim($this->input->post('remarks') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        if (trim($this->input->post('purchaseRequestDetailsID') ?? '')) {
            $this->db->where('purchaseRequestDetailsID', trim($this->input->post('purchaseRequestDetailsID') ?? ''));
            $this->db->update('srp_erp_purchaserequestdetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Purchase Request Detail : ' . $data['itemSystemCode'] . ' Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Purchase Request Detail : ' . $data['itemSystemCode'] . ' Updated Successfully.');

            }
        }
    }

    function conversionRateUOM($umo, $default_umo)
    {
        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $default_umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $masterUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('UnitID');
        $this->db->where('UnitShortCode', $umo);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $subUnitID = $this->db->get('srp_erp_unit_of_measure')->row('UnitID');

        $this->db->select('conversion');
        $this->db->from('srp_erp_unitsconversion');
        $this->db->where('masterUnitID', $masterUnitID);
        $this->db->where('subUnitID', $subUnitID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row('conversion');
    }

    function fetch_supplier_data($supplierID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', $supplierID);
        return $this->db->get()->row_array();
    }

    function load_purchase_request_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->from('srp_erp_purchaserequestmaster');
        return $this->db->get()->row_array();
    }

    function fetch_itemrecode_pqr()
    {
        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['query'] . "%";
        $data = $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT( IFNULL(itemDescription,"empty"), " - ", IFNULL(itemSystemCode,"empty"), " - ", IFNULL(partNo,"empty")  , " - ", IFNULL(seconeryItemCode,"empty")) AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '" OR barcode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1" AND masterApprovedYN = "1" ')->result_array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $dataArr[] = array('value' => $val["Match"], 'data' => $val['itemSystemCode'], 'itemAutoID' => $val['itemAutoID'], 'currentStock' => $val['currentStock'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID'], 'companyLocalSellingPrice' => $val['companyLocalSellingPrice'], 'companyLocalWacAmount' => $val['companyLocalWacAmount'], 'isSubitemExist' => $val['isSubitemExist']);
            }

        }
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function fetch_itemrecode()
    {
        $companyCode = $this->common_data['company_data']['company_code'];
        $search_string = "%" . $_GET['q'] . "%";
        return $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyCode = "' . $companyCode . '" AND isActive="1"')->result_array();
    }

    function fetch_pqr_detail_table()
    {
        
        $convertFormat = convert_date_format_sql();
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $this->db->select('transactionCurrency,transactionCurrencyDecimalPlaces,isTechSpecRequired');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->from('srp_erp_purchaserequestmaster');
        $data['currency'] = $this->db->get()->row_array();
        $this->db->select('*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,CONCAT_WS(\' - Part No : \',IF(LENGTH(srp_erp_purchaserequestdetails.itemDescription),srp_erp_purchaserequestdetails.itemDescription,NULL),IF(LENGTH(srp_erp_itemmaster.partNo),srp_erp_itemmaster.partNo,NULL))as Itemdescriptionpartno,'.$item_code.'');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_purchase_request_detail()
    {
        $this->db->delete('srp_erp_purchaserequestdetails', array('purchaseRequestDetailsID' => trim($this->input->post('purchaseRequestDetailsID') ?? '')));
        return true;
    }

    function delete_tax_detail()
    {
        $this->db->delete('srp_erp_purchaseordertaxdetails', array('taxDetailAutoID' => trim($this->input->post('taxDetailAutoID') ?? '')));
        return true;
    }

    function delete_purchase_request()
    {
        $masterID = trim($this->input->post('purchaseRequestID') ?? '');
        /*$this->db->delete('srp_erp_purchaseordermaster', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID') ?? '')));
        $this->db->delete('srp_erp_purchaseorderdetails', array('purchaseOrderID' => trim($this->input->post('purchaseOrderID') ?? '')));
        return true;*/
        $this->db->select('*');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $datas = $this->db->get()->row_array();
        if ($datas) {
            $this->session->set_flashdata('e', 'please delete all detail records before deleting this document.');
            return true;
        } else {

            /*$data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
            $this->db->update('srp_erp_purchaserequestmaster', $data);
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;*/
            
        /* Added*/    
            $documentCode = $this->db->get_where('srp_erp_purchaserequestmaster', ['purchaseRequestID'=> $masterID])->row('purchaseRequestCode');
            $this->db->trans_start();

            $length = strlen($documentCode);    
            if($length > 1){
                $data = array(
                    'isDeleted' => 1,
                    'deletedEmpID' => current_userID(),
                    'deletedDate' => current_date(),
                );
                $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                $this->db->update('srp_erp_purchaserequestmaster', $data);
            }
            else{
                $this->db->where('purchaseRequestID', $masterID)->delete('srp_erp_purchaserequestdetails');
                $this->db->where('purchaseRequestID', $masterID)->delete('srp_erp_purchaserequestmaster');
            }

            $this->db->trans_complete();
            if($this->db->trans_status() == true){
                $this->session->set_flashdata('s', 'Deleted Successfully.');
                return true;
            }else{
                $this->session->set_flashdata('e', 'Error in delete process.');
               
               return false;
            }
        /* End */    
        }
    }

    function fetch_purchase_request_detail()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('srp_erp_purchaserequestdetails.*,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,srp_erp_itemmaster.seconeryItemCode as seconeryItemCode,itemledgercurrent.currentstock AS itemledstock');
        $this->db->where('purchaseRequestDetailsID', trim($this->input->post('purchaseRequestDetailsID') ?? ''));
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID','Left');
        $this->db->join("(SELECT IF (mainCategory = 'Inventory',  (SUM(transactionQTY/ convertionRate)),\" \") AS currentstock, srp_erp_itemledger.itemAutoID 
                            FROM `srp_erp_itemledger`
                            LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID 
                            WHERE srp_erp_itemledger.itemAutoID is not null
                            GROUP BY srp_erp_itemledger.itemAutoID 
                          )itemledgercurrent","itemledgercurrent.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID ","left");
        
        return $this->db->get()->row_array();
    }

    function fetch_template_data($purchaseRequestID)
    {
        $secondaryCode = getPolicyValues('SSC', 'All'); 
        $item_code = 'srp_erp_itemmaster.itemSystemCode as itemSystemCode';
        if($secondaryCode  == 1){ 
            $item_code = 'seconeryItemCode as itemSystemCode';
        }
        $convertFormat = convert_date_format_sql();
        $this->db->select('purchaseRequestID,createdUserName,transactionCurrency,transactionCurrencyDecimalPlaces,purchaseRequestCode, DATE_FORMAT(createdDateTime,\'' . $convertFormat . '\') AS createdDateTime,
                createdUserID, confirmedByEmpID, confirmedbyName, referenceNumber,requestedByName,narration,DATE_FORMAT(expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,confirmedByName,confirmedYN,
                DATE_FORMAT(confirmedDate,\'' . $convertFormat . '\') AS confirmedDate,approvedbyEmpID,approvedbyEmpName,approvedYN,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,
                DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,segmentCode,CASE WHEN confirmedYN = 2 || confirmedYN = 3   THEN " - " WHEN confirmedYN = 1 THEN 
                CONCAT_WS(\' on \',IF(LENGTH(confirmedbyName),confirmedbyName,\'-\'),IF(LENGTH(DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' )),DATE_FORMAT( confirmedDate, \'' . $convertFormat . ' %h:%i:%s\' ),NULL)) ELSE "-" END confirmedYNn,
                jobNumber,severityType,versionNo');
        $this->db->where('purchaseRequestID', $purchaseRequestID);
        $this->db->from('srp_erp_purchaserequestmaster');
        $data['master'] = $this->db->get()->row_array();
        $data['master']['CurrencyDes'] = fetch_currency_dec($data['master']['transactionCurrency']);
       
       
        $this->db->select(' '.$item_code.',srp_erp_itemmaster.itemDescription,unitOfMeasure,requestedQty,unitAmount,discountAmount,comment, totalAmount, discountPercentage,DATE_FORMAT(srp_erp_purchaserequestdetails.expectedDeliveryDate,\'' . $convertFormat . '\') AS expectedDeliveryDate,CONCAT_WS(\'- \',IF(LENGTH(srp_erp_purchaserequestdetails.comment),srp_erp_purchaserequestdetails.comment,NULL),\' Part No : \',IF(LENGTH(srp_erp_itemmaster.partNo),srp_erp_itemmaster.partNo,NULL))as Itemdescriptionpartno,srp_erp_purchaserequestdetails.purchaseRequestDetailsID,srp_erp_purchaserequestdetails.purchaseRequestID,srp_erp_purchaserequestdetails.isClosedYN,srp_erp_purchaserequestdetails.activityCodeID,srp_erp_purchaserequestdetails.estimatedQty,srp_erp_purchaserequestmaster.versionNo');
        $this->db->where('srp_erp_purchaserequestdetails.purchaseRequestID', $purchaseRequestID);
        $this->db->join('srp_erp_itemmaster','srp_erp_itemmaster.itemAutoID = srp_erp_purchaserequestdetails.itemAutoID');
        $this->db->join('srp_erp_purchaserequestmaster', 'srp_erp_purchaserequestmaster.purchaseRequestID = srp_erp_purchaserequestdetails.purchaseRequestID');
        $this->db->from('srp_erp_purchaserequestdetails');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $purchaseRequestID);
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
    }

    function purchase_request_confirmation()
    {
        $locationwisecodegenerate = getPolicyValues('LDG', 'All');
        $singleSourcePR = getPolicyValues('SSPR', 'All');
        $prMandatoryFADocPolicy = getPolicyValues('PRMFADOC', 'All');
        $enableCategoryPR = getPolicyValues('ECPR', 'All');
        $locationemployee = $this->common_data['emplanglocationid'];
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $purchaseRequestID = trim($this->input->post('purchaseRequestID') ?? '');
        $this->db->select('purchaseRequestID,itemAutoID,purchaseRequestDetailsID,itemType,activityCodeID,totalAmount');
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->from('srp_erp_purchaserequestdetails');
        $record = $this->db->get()->result_array();
        if (empty($record)) {
            $this->session->set_flashdata('w', 'There are no records to confirm this document!');
            return false;
        } else {

            if($prMandatoryFADocPolicy==1){

                foreach($record as $val){

                    if($val['itemType']=='Fixed Assets'){

                        $this->db->select('*');
                        $this->db->where('documentID', 'PRQ');
                        $this->db->where('subDocumentSystemCode', $val['purchaseRequestDetailsID']);
                        $this->db->where('companyID', $companyID);
                        $this->db->from('srp_erp_documentattachments');
                        $record_FaDoc = $this->db->get()->result_array();
    
                        if (empty($record_FaDoc)) {
                            $this->session->set_flashdata('w', 'There are no fixed assets item document!');
                            return false;
                        }
                    }

                }

            }

            $this->db->select('purchaseRequestID');
            $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
            $this->db->where('confirmedYN', 1);
            $this->db->from('srp_erp_purchaserequestmaster');
            $Confirmed = $this->db->get()->row_array();
            if (!empty($Confirmed)) {
                $this->session->set_flashdata('w', 'Document already confirmed ');
                return false;
            }else {
                $this->db->select('purchaseRequestCode,documentID,DATE_FORMAT(documentDate, "%Y") as invYear,DATE_FORMAT(documentDate, "%m") as invMonth,documentDate,companyLocalExchangeRate,isSingleSourcePr');
                $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                $this->db->from('srp_erp_purchaserequestmaster');
                $master_dt = $this->db->get()->row_array();

                $docDate=$master_dt['documentDate'];
                $Comp=current_companyID();
                
                $companyFinanceYearID = $this->db->query("SELECT
                                                            period.companyFinanceYearID as companyFinanceYearID
                                                        FROM
                                                            srp_erp_companyfinanceperiod period
                                                        WHERE
                                                            period.companyID = $Comp
                                                        AND '$docDate' BETWEEN period.dateFrom
                                                        AND period.dateTo")->row_array();

                if(empty($companyFinanceYearID['companyFinanceYearID'])){
                    $companyFinanceYearID['companyFinanceYearID']=NULL;
                }

                // start approval typs checking
                $approvalType = getApprovalTypesONDocumentCode('PRQ', $Comp);
                $purchasereqID =trim($this->input->post('purchaseRequestID') ?? '');

                $documentTotal = $this->db->query("SELECT srp_erp_purchaserequestmaster.purchaseRequestID AS purchaseRequestID, srp_erp_purchaserequestmaster.companyLocalExchangeRate, transactionCurrencyID, transactionCurrency,
                    ( det.transactionAmount - det.discountAmount)+ IFNULL( gentax.gentaxamount, 0 ) AS total_value 
                    FROM srp_erp_purchaserequestmaster
                        LEFT JOIN ( SELECT SUM( totalAmount ) AS transactionAmount, purchaseRequestID , discountAmount FROM srp_erp_purchaserequestdetails GROUP BY purchaseRequestID ) det ON det.purchaseRequestID = srp_erp_purchaserequestmaster.purchaseRequestID
                        LEFT JOIN (
                                SELECT ifnull( SUM( amount ), 0 ) AS gentaxamount, documentMasterAutoID 
                                FROM srp_erp_taxledger 
                                WHERE documentID = 'PRQ' AND documentDetailAutoID IS NULL AND companyID = {$Comp} 
                                GROUP BY documentMasterAutoID 
                        ) gentax ON ( gentax.documentMasterAutoID = srp_erp_purchaserequestmaster.purchaseRequestID ) 
                    WHERE
                    srp_erp_purchaserequestmaster.purchaseRequestID = {$purchasereqID} AND srp_erp_purchaserequestmaster.companyID = {$Comp}")->row_array();

                $poLocalAmount = $documentTotal['total_value'] /$master_dt['companyLocalExchangeRate'];

               
                

                $approval_type_data = $this->db->query("SELECT segmentID,itemCategoryID FROM srp_erp_purchaserequestmaster where purchaseRequestID = $purchasereqID AND companyID = {$Comp}")->row_array();

                if($approvalType['approvalType'] == 1) {


                    if($singleSourcePR ==1){
                        if($master_dt['isSingleSourcePr']==1){
                            $amountApprovable = single_source_based_approval_setup('PRQ',null,1);
                        }
                        
                    }
                   

                    if(isset($amountApprovable['type']) && $amountApprovable['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PRQ Single Source');
                        return false;
                        exit;
                    }
                }

                if($approvalType['approvalType'] == 2) {


                    if($singleSourcePR ==1){
                        if($master_dt['isSingleSourcePr']==1){
                            $amountApprovable = amount_based_approval_setup('PRQ', $poLocalAmount,null,1);
                        }else{
                            $amountApprovable = amount_based_approval_setup('PRQ', $poLocalAmount,null,0);
                        }
                        
                    }else{
                        $amountApprovable = amount_based_approval_setup('PRQ', $poLocalAmount,null,0);
                    }
                   

                    if($amountApprovable['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $amountApprovable['level'] . ' is not configured for this PRQ Value');
                        return false;
                        exit;
                    }
                }
                if($approvalType['approvalType'] == 3) {

                    if($singleSourcePR ==1){
                        if($master_dt['isSingleSourcePr']==1){
                            $segment_based_approval = segment_based_approval('PRQ', $approval_type_data['segmentID'],null,1);
                        }else{
                            $segment_based_approval = segment_based_approval('PRQ', $approval_type_data['segmentID'],null,0);
                        }
                        
                    }else{
                        $segment_based_approval = segment_based_approval('PRQ', $approval_type_data['segmentID'],null,0);
                    }
                    

                    if($segment_based_approval['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $segment_based_approval['level'] . ' is not configured for this PRQ Value');
                        return false;
                        exit;
                    }
                }
                if($approvalType['approvalType'] == 4) {

                    if($singleSourcePR ==1){
                        if($master_dt['isSingleSourcePr']==1){
                            $amount_base_segment_based_approval = amount_base_segment_based_approval('PRQ', $poLocalAmount, $approval_type_data['segmentID'],null,1);
                        }else{
                            $amount_base_segment_based_approval = amount_base_segment_based_approval('PRQ', $poLocalAmount, $approval_type_data['segmentID'],null,0);
                        }
                        
                    }else{
                        $amount_base_segment_based_approval = amount_base_segment_based_approval('PRQ', $poLocalAmount, $approval_type_data['segmentID'],null,0);
                    }

                   

                    if($amount_base_segment_based_approval['type'] == 'e') {
                        $this->session->set_flashdata('w', 'Approval Level ' . $amount_base_segment_based_approval['level'] . ' is not configured for this PRQ Value');
                        return false;
                        exit;
                    }
                }

                if($approvalType['approvalType'] == 5) {  //category Base Approval

                    if($enableCategoryPR==1){

                        if($singleSourcePR ==1){
                            if($master_dt['isSingleSourcePr']==1){
                                $category_base_segment_based_approval = category_based_approval_setup('PRQ', $poLocalAmount, $approval_type_data['itemCategoryID'],null,1);
                            }else{
                                $category_base_segment_based_approval = category_based_approval_setup('PRQ', $poLocalAmount, $approval_type_data['itemCategoryID'],null,0);
                            }
                            
                        }else{
                            $category_base_segment_based_approval = category_based_approval_setup('PRQ', $poLocalAmount, $approval_type_data['itemCategoryID'],null,0);
                        }

                        if($category_base_segment_based_approval['type'] == 'e') {
                            $this->session->set_flashdata('w', 'Approval Level ' . $category_base_segment_based_approval['level'] . ' is not configured for this PRQ Value');
                            return false;
                            exit;
                        }
                    }else{
                        $this->session->set_flashdata('w', 'Please enable Category Policy on PR');
                        return false;
                        exit;
                    }
                }
                
                //end start approval typs
                
                $this->load->library('sequence');
                if($master_dt['purchaseRequestCode'] == "0" || empty($master_dt['purchaseRequestCode'])) {

                    if($locationwisecodegenerate == 1)
                    {
                        $this->db->select('locationID');
                        $this->db->where('EIdNo', $currentuser);
                        $this->db->where('Erp_companyID', $companyID);
                        $this->db->from('srp_employeesdetails');
                        $location = $this->db->get()->row_array();
                        if ((empty($location)) || ($location =='')) {
                            $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                            return false;
                        }else
                        {
                            if($locationemployee!='')
                            {
                                $codegeratorpr = $this->sequence->sequence_generator_location($master_dt['documentID'],$companyFinanceYearID['companyFinanceYearID'],$locationemployee,$master_dt['invYear'],$master_dt['invMonth']);
                            }else
                            {
                                $this->session->set_flashdata('w', 'Location is not assigned for current employee');
                                return false;
                            }
                        }
                    }else
                    {
                        if($companyFinanceYearID['companyFinanceYearID'] == NULL) {
                             $this->session->set_flashdata('w', 'Financial Year Not generated For this Document Date!');
                            return false;
                        } else if($master_dt['invYear'] == null) {
                              $this->session->set_flashdata('w', 'Document Year Not Found For this Document!');
                            return false;
                        } else if ($master_dt['invMonth'] == null){
                             $this->session->set_flashdata('w', 'Document Month Not Found For this Document!');
                            return false;
                        } else {
                            $codegeratorpr = $this->sequence->sequence_generator_fin($master_dt['documentID'], $companyFinanceYearID['companyFinanceYearID'], $master_dt['invYear'], $master_dt['invMonth']);
                        }
                    }

                    
                    $validate_code = validate_code_duplication($codegeratorpr, 'purchaseRequestCode', $purchaseRequestID,'purchaseRequestID', 'srp_erp_purchaserequestmaster');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }

                    $pvCd = array(
                        'purchaseRequestCode' => $codegeratorpr
                    );
                    $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                    $this->db->update('srp_erp_purchaserequestmaster', $pvCd);
                } else {
                    $validate_code = validate_code_duplication($master_dt['purchaseRequestCode'], 'purchaseRequestCode', $purchaseRequestID,'purchaseRequestID', 'srp_erp_purchaserequestmaster');
                    if(!empty($validate_code)) {
                        $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
                        return false;
                    }
                }

                $this->load->library('Approvals');
                $this->db->select('purchaseRequestCode,documentDate,companyReportingExchangeRate,companyLocalExchangeRate ,purchaseRequestID,transactionCurrencyDecimalPlaces');
                $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                $this->db->from('srp_erp_purchaserequestmaster');
                $po_data = $this->db->get()->row_array();

                $autoApproval= get_document_auto_approval('PRQ');

                if($autoApproval==0){
                    $approvals_status = $this->approvals->auto_approve($po_data['purchaseRequestID'], 'srp_erp_purchaserequestmaster','purchaseRequestID', 'PRQ',$po_data['purchaseRequestCode'],$po_data['documentDate']);
                }elseif($autoApproval==1){

                    if($singleSourcePR ==1){

                        if($master_dt['isSingleSourcePr']==1){
                            $approvals_status = $this->approvals->CreateApproval('PRQ', $po_data['purchaseRequestID'], $po_data['purchaseRequestCode'], 'Purchase Request', 'srp_erp_purchaserequestmaster', 'purchaseRequestID',0,$po_data['documentDate'],$approval_type_data['segmentID'],$poLocalAmount,1,null,$approval_type_data['itemCategoryID']);
                        }else{
                            $approvals_status = $this->approvals->CreateApproval('PRQ', $po_data['purchaseRequestID'], $po_data['purchaseRequestCode'], 'Purchase Request', 'srp_erp_purchaserequestmaster', 'purchaseRequestID',0,$po_data['documentDate'],$approval_type_data['segmentID'],$poLocalAmount,0,null,$approval_type_data['itemCategoryID']);
                        }

                    }else{
                            $approvals_status = $this->approvals->CreateApproval('PRQ', $po_data['purchaseRequestID'], $po_data['purchaseRequestCode'], 'Purchase Request', 'srp_erp_purchaserequestmaster', 'purchaseRequestID',0,$po_data['documentDate'],$approval_type_data['segmentID'],$poLocalAmount,0,null,$approval_type_data['itemCategoryID']);
                    }
                    
                }else{
                    $this->session->set_flashdata('e', 'Approval levels are not set for this document');
                    return false;
                }
                
                if ($approvals_status == 1) {
                    $this->db->select_sum('totalAmount');
                    $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                    $po_total = $this->db->get('srp_erp_purchaserequestdetails')->row('totalAmount');

                    $autoApproval= get_document_auto_approval('PRQ');

                    ///load assign buyers
                    $assignBuyersPolicy = getPolicyValues('ABFC', 'All');

                    if($assignBuyersPolicy){

                        foreach($record as $key=>$det){
                            $this->db->select('*');
                            $this->db->where('itemAutoID', $det['itemAutoID']);
                            $this->db->from('srp_erp_itemmaster');
                            $item_dt = $this->db->get()->row_array();
                            $buyers_cat=[];
                            $buyers_sub_cat=[];
                            $notAssignBuyers=0;
                            $notAssignBuyers_sub=0;
                            if($item_dt){
                                if($item_dt['subcategoryID']){

                                    $this->db->select('*');
                                    $this->db->where('subCatID', $item_dt['subcategoryID']);
                                    $this->db->where('subSubCatID',null);
                                    $this->db->where('userType',0);
                                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                                    $this->db->from('srp_erp_incharge_assign');
                                    $item_category_assign_emp = $this->db->get()->result_array();

                                    if($item_category_assign_emp){
                                        foreach($item_category_assign_emp as $val)
                                        {
                                            //$buyers_cat[]=$val['empID'];
                                            $buyers_cat[]=["autoID"=>$val['autoID'],"empID"=>$val['empID']];
                                        }
                                    }else{
                                        $notAssignBuyers=$det['purchaseRequestDetailsID'];
    
                                    }

                                }

                                if($item_dt['subSubCategoryID']){
                                    $this->db->select('*');
                                    $this->db->where('subSubCatID', $item_dt['subSubCategoryID']);
                                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                                    $this->db->where('userType',0);
                                    $this->db->from('srp_erp_incharge_assign');
                                    $item_category_sub_assign_emp = $this->db->get()->result_array();

                                    if($item_category_sub_assign_emp){
                                        foreach($item_category_sub_assign_emp as $val)
                                        {
                                            //$buyers_sub_cat[]=$val['empID'];
                                            $buyers_sub_cat[]=["autoID"=>$val['autoID'],"empID"=>$val['empID']];
                                        }
                                    }else{
                                        $notAssignBuyers_sub=$det['purchaseRequestDetailsID'];
    
                                    }
                                }

                                $new_buy = array_merge($buyers_cat,$buyers_sub_cat);

                                if(count($new_buy)>0){

                                    foreach($new_buy as $val){
                                        $data_buyer['documentID'] = $master_dt['documentID'];
                                        $data_buyer['empID'] = $val['empID'];
                                        $data_buyer['assignMasterID'] = $val['autoID'];
                                        $data_buyer['companyID'] = $this->common_data['company_data']['company_id'];
                                        $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                                        $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                                        $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                                        $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                                        $data_buyer['createdUserName'] = $this->common_data['current_user'];

                                        $data_buyer['activityMasterID'] = $purchaseRequestID;
                                        $data_buyer['activityDetailID'] = $det['purchaseRequestDetailsID'];
                                        $data_buyer['activityIsActive'] = 1;
                                
                                        $this->db->insert('srp_erp_incharge_assign', $data_buyer);
                                    }
                                }

                                if($notAssignBuyers!=0 && $notAssignBuyers_sub!=0){
                                        $data_buyer1['documentID'] = $master_dt['documentID'];
                                        $data_buyer1['empID'] = null;
                                        $data_buyer1['assignMasterID'] =null;
                                        $data_buyer1['companyID'] = $this->common_data['company_data']['company_id'];
                                        $data_buyer1['createdUserGroup'] = $this->common_data['user_group'];
                                        $data_buyer1['createdPCID'] = $this->common_data['current_pc'];
                                        $data_buyer1['createdUserID'] =  $this->common_data['current_userID'];
                                        $data_buyer1['createdDateTime'] = $this->common_data['current_date'];
                                        $data_buyer1['createdUserName'] = $this->common_data['current_user'];

                                        $data_buyer1['activityMasterID'] = $purchaseRequestID;
                                        $data_buyer1['activityDetailID'] = $notAssignBuyers;
                                        $data_buyer1['activityIsActive'] = 0;
                                
                                        $this->db->insert('srp_erp_incharge_assign', $data_buyer1);
                                }
                            }
            
                        }
                    }

                    $this->load->library('costAllocation');
                    foreach($record as $value)
                    {
                        if (null !== $value['activityCodeID'])
                        {
                            $costAllocation = [
                                'documentId'     => $master_dt['documentID'],
                                'masterId'       => $value['purchaseRequestID'],
                                'detailId'       => $value['purchaseRequestDetailsID'],
                                'amount'         => $value['totalAmount'],
                                'activityCodeID' => $value['activityCodeID'],
                            ];
                            $output = $this->costallocation->saveDocumentCostAllocation($costAllocation);
                            if(false === $output)
                            {
                                return false;
                            }
                        }
                    }

                    if($autoApproval==0) {
                        $data = array(
                            'transactionAmount' => round($po_total, $po_data['transactionCurrencyDecimalPlaces']),
                            'companyLocalAmount' => ($po_total / $po_data['companyLocalExchangeRate']),
                            'companyReportingAmount' => ($po_total / $po_data['companyReportingExchangeRate']),
                            'isReceived' => 0,
                        );
                        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                        $this->db->update('srp_erp_purchaserequestmaster', $data);
                        $result = $this->save_purchase_request_approval(0, $po_data['purchaseRequestID'], 1, 'Auto Approved');
                        if($result){
                            $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                            return true;
                        }
                    }else{
                        $data = array(
                            'confirmedYN' => 1,
                            'approvedYN' => 0,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => round($po_total, $po_data['transactionCurrencyDecimalPlaces']),
                            'companyLocalAmount' => ($po_total / $po_data['companyLocalExchangeRate']),
                            'companyReportingAmount' => ($po_total / $po_data['companyReportingExchangeRate']),
                            'isReceived' => 0,
                        );
                        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
                        $this->db->update('srp_erp_purchaserequestmaster', $data);

                        $this->session->set_flashdata('s', 'Approvals Created Successfully ');
                        return true;
                    }                   
                } else {
                    return false;
                }
            }
        }
    }

    function save_purchase_request_approval($autoappLevel=1,$system_idAP=0,$statusAP=0,$commentsAP=0)
    {
        $this->db->trans_start();
        $this->load->library('Approvals');

        if($autoappLevel==1){
            $system_code = trim($this->input->post('purchaseRequestID') ?? '');
            $level_id = trim($this->input->post('Level') ?? '');
            $status = trim($this->input->post('po_status') ?? '');
            $comments = trim($this->input->post('comments') ?? '');
        }else{
            $system_code = $system_idAP;
            $level_id = 0;
            $status = $statusAP;
            $comments = $commentsAP;
            $_post['purchaseRequestID']=$system_code;
            $_post['Level']=$level_id;
            $_post['status']=$status;
            $_post['comments']=$comments;
        }

        if($autoappLevel==0){
            $approvals_status=1;
        }else{
            $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'PRQ');
        }

        if ($approvals_status == 1) {
           // $data['approvedYN'] = $status;
           // $data['approvedbyEmpID'] = $this->common_data['current_userID'];
           // $data['approvedbyEmpName'] = $this->common_data['current_user'];
           // $data['approvedDate'] = $this->common_data['current_date'];
            //$data['companyLocalAmount']     = $company_loc_tot;
            //$data['companyReportingAmount'] = $company_rpt_tot;
            //$data['supplierCurrencyAmount'] = $supplier_cr_tot;
            //$data['transactionAmount']      = $transaction_loc_tot;

            //$this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
            //$result = $this->db->update('srp_erp_purchaserequestmaster', $data);
            $this->session->set_flashdata('s', 'Approved Successfully.');
        }
        /*if ($result) {
            if ($status == 1) {
                $this->session->set_flashdata('s', 'Approved Successfully.');
            } else {
                $this->session->set_flashdata('s', 'Rejected Successfully.');
            }

        } else {
            $this->session->set_flashdata('e', 'Approval Failed.');
        }*/

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function save_purchase_order_close()
    {
        $this->db->trans_start();
        $system_code = trim($this->input->post('purchaseOrderID') ?? '');

        $data['closedYN'] = 1;
        $data['closedDate'] = $this->input->post('closedDate');
        $data['closedReason'] = trim($this->input->post('comments') ?? '');
        $data['approvedYN'] = 5;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('purchaseOrderID', trim($this->input->post('purchaseOrderID') ?? ''));
        $this->db->update('srp_erp_purchaseordermaster', $data);
        $this->session->set_flashdata('s', 'Purchase Order Closed Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function fetch_convertion_detail_table()
    {
        $this->db->select('subUnitID,conversion,s.UnitShortCode as sub_code,s.UnitDes as sub_dese,m.UnitShortCode as m_code,m.UnitDes as m_dese');
        $this->db->where('masterUnitID', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_unitsconversion.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unitsconversion');
        $this->db->join('srp_erp_unit_of_measure s', 's.UnitID = srp_erp_unitsconversion.subUnitID');
        $this->db->join('srp_erp_unit_of_measure m', 'm.UnitID = srp_erp_unitsconversion.masterUnitID');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('UnitID,UnitShortCode,UnitDes');
        $this->db->where('UnitID !=', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_unit_of_measure.companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_unit_of_measure');
        //$this->db->join('srp_erp_unitsconversion', 'srp_erp_unitsconversion.subUnitID != srp_erp_unit_of_measure.UnitID','inner');
        $data['drop'] = $this->db->get()->result_array();
        //echo $this->db->last_query();
        return $data;
    }

    function fetch_supplier_currency()
    {
        $this->db->select('supplierCurrency');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_supplier_currency_by_id()
    {
        $this->db->select('supplierCurrencyID,supplierCreditPeriod');
        $this->db->from('srp_erp_suppliermaster');
        $this->db->where('supplierAutoID', trim($this->input->post('supplierAutoID') ?? ''));
        return $this->db->get()->row_array();
    }

    function fetch_customer_currency()
    {
        $this->db->select('customerCurrency');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', trim($this->input->post('customerAutoID') ?? ''));
        return $this->db->get()->row_array();
    }


    function delete_purchaseOrder_attachement()
    {
        $attachmentID = $this->input->post('attachmentID');
        $myFileName = $this->input->post('myFileName');
        $url = base_url("attachments");
        $link = "$url/$myFileName";
        if (!unlink(UPLOAD_PATH . $link)) {
            return false;
        } else {
            $this->db->delete('srp_erp_documentattachments', array('attachmentID' => trim($attachmentID)));
            return true;
        }
    }

    function re_open_procurement()
    {
        $data = array(
            'isDeleted' => 0,
        );
        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->update('srp_erp_purchaserequestmaster', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;
    }

    function fetch_last_grn_amount()
    {
        $itemAutoId = $this->input->post('itemAutoId');
        $currencyID = $this->input->post('currencyID');
        $data = $this->db->query('SELECT
	grvdetails.receivedAmount
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . '
and grvDate=(SELECT
	max(grvmaster.grvDate) as maxdate
FROM
	srp_erp_grvdetails grvdetails
JOIN srp_erp_grvmaster grvmaster ON grvdetails.grvAutoID=grvmaster.grvAutoID

where grvmaster.approvedYN=1 and  grvmaster.transactionCurrencyID=' . $currencyID . ' and itemAutoID=' . $itemAutoId . ')')->row_array();
        return $data;
    }

    function fetch_signaturelevel()
    {
        $this->db->select('approvalSignatureLevel');
        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PRQ');
        $this->db->from('srp_erp_documentcodemaster ');
        return $this->db->get()->row_array();
    }

    function save_purchase_request_close()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $docdate = $this->input->post('prclosedDate');
        $closeddate = input_format_date($docdate, $date_format_policy);
        $data['closedYN'] = 1;
        $data['closedDate'] = $closeddate;
        $data['closedBy'] = $this->common_data['current_userID'];
        $data['closedReason'] = trim($this->input->post('comments') ?? '');
        $data['approvedYN'] = 5;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];

        $this->db->where('purchaseRequestID', trim($this->input->post('purchaseRequestID') ?? ''));
        $this->db->update('srp_erp_purchaserequestmaster', $data);
        $this->session->set_flashdata('s', 'Purchase Request Closed Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function validate_close_prq()
    {
        $purchaseRequestID = $this->input->post('purchaseRequestID');
        $compID = current_companyID();
        $balanceQty = $this->db->query("SELECT
                IFNULL(SUM(requestedQty), 0) - (IFNULL(PVQty, 0) + IFNULL(POQty, 0)) as balanceQty
                FROM srp_erp_purchaserequestdetails 
                LEFT JOIN (SELECT SUM(requestedQty) AS PVQty, prMasterID FROM srp_erp_paymentvoucherdetail GROUP BY prMasterID)pv ON pv.prMasterID = srp_erp_purchaserequestdetails.purchaseRequestID
                LEFT JOIN (SELECT SUM(requestedQty) AS POQty, prMasterID FROM srp_erp_purchaseorderdetails GROUP BY prMasterID)po ON po.prMasterID = srp_erp_purchaserequestdetails.purchaseRequestID
                WHERE purchaseRequestID = $purchaseRequestID AND companyID = $compID
                GROUP BY purchaseRequestID")->row('balanceQty');

        if ($balanceQty == 0) {
            return array('w', 'Item Quantity fully received!');
        } else {
            return array('s', '');
        }

    }

    function assign_buyers_pr_details(){
        $assignBuyersSync = $this->input->post('assignBuyersSync');

        $this->db->trans_start();
      
        if(!empty($assignBuyersSync)){
            foreach($assignBuyersSync as $val){

                $data['activityIsActive'] = 1;
                $data['assignByEmpID'] = $this->common_data['current_userID'];
                $this->db->where('autoID', $val);
                $this->db->update('srp_erp_incharge_assign', $data);
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Update Successfully');
        }
    }

    function delete_pr_buyers_detail($id){
        $this->db->where('activityMasterID', $id)->where('documentID', 'PRQ')->where('userType', 0)->where('companyID', $this->common_data['company_data']['company_id'])->delete('srp_erp_incharge_assign');

    }

    function remove_assign_buyers_pr(){
        $id = $this->input->post('id');

        $this->db->trans_start();
        $data['activityIsActive'] = 0;
        $data['assignByEmpID'] = null;
        $this->db->where('autoID', $id);
        $this->db->update('srp_erp_incharge_assign', $data);
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Update Successfully');
        }
    }

    function remove_assign_buyers_pr_item(){
        $id = $this->input->post('id');

        $this->db->where('autoID', $id)->delete('srp_erp_incharge_assign');
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Delete Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Delete Successfully');
        }
    }

    function remove_assign_items_line_wise(){

        $documentID_cl = $this->input->post('documentID_cl');
        $masterID_cl = $this->input->post('masterID_cl');
        $detailID_cl = $this->input->post('detailID_cl');
        $tableName_cl = $this->input->post('tableName_cl');
        $master_col_name_cl = $this->input->post('master_col_name_cl');
        $narration_cl = $this->input->post('narration_cl');

        $this->db->trans_start();

        $data['isClosedYN'] = 1;
        $data['isClosedBy'] = $this->common_data['current_userID'];
        $data['isClosedDate'] = $this->common_data['current_date'];
        $data['isClosedComment'] = $narration_cl;

        $this->db->where( $master_col_name_cl, $detailID_cl);
        $this->db->where( 'companyID', current_companyID());
        $this->db->update($tableName_cl, $data);
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Update Successfully',$documentID_cl,$masterID_cl);
        }
    }

    function save_srm_acknowledge(){

       
        $masterID_ac = $this->input->post('masterID_ac');
        $detailID_ac = $this->input->post('detailID_ac');
      
        $comment_ac = $this->input->post('ac_comment');

        $this->db->trans_start();

        $data['isAcknowledgeYN'] = 1;
        $data['isAcknowledgeBy'] = $this->common_data['current_userID'];
        $data['isAcknowledgeDate'] = $this->common_data['current_date'];
        $data['isAcknowledgeComment'] = $comment_ac;
        $data['isAcknowledgeByName'] = $this->common_data['current_user'];

        $this->db->where('purchaseRequestDetailsID', $detailID_ac);
        $this->db->where('companyID', current_companyID());
        $this->db->update('srp_erp_purchaserequestdetails', $data);
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Update Successfully',$masterID_ac,$detailID_ac);
        }
    }

    function add_technical_users_to_pr_item(){
        $user_arr = $this->input->post("technical_users");
        $master_id = $this->input->post("master_id");
        $details_id = $this->input->post("details_id");
        $item_id = $this->input->post("item_id");

        if($user_arr){
            foreach($user_arr as $val){

                $this->db->select('*');
                $this->db->where('companyID', current_companyID());
                $this->db->where('documentID', 'PRQ');

                $this->db->where('empID', $val);
                $this->db->where('activityMasterID', $master_id);
                $this->db->where('activityDetailID', $details_id);
                $this->db->where('userType', 1);
                $this->db->where('itemAutoID', $item_id);

                $this->db->from('srp_erp_incharge_assign');
                $added_user= $this->db->get()->row_array();

                if(!$added_user){
                    $data_buyer['documentID'] = 'PRQ';
                    $data_buyer['empID'] = $val;
                    $data_buyer['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                    $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                    $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                    $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                    $data_buyer['createdUserName'] = $this->common_data['current_user'];
        
                    $data_buyer['activityMasterID'] = $master_id;
                    $data_buyer['activityDetailID'] = $details_id;
                    $data_buyer['activityIsActive'] = 0;
                    $data_buyer['userType'] = 1;
                    $data_buyer['itemAutoID'] = $item_id;
            
                    $this->db->insert('srp_erp_incharge_assign', $data_buyer);

                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', ' Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
        
        } else {
            return array('s', 'Updated Successfully.','');
            $this->db->trans_commit();
    
        }
    }

    function delete_assign_tech_users_pr(){
        $id = $this->input->post('id');

        $this->db->trans_start();

        $this->db->where('autoID', $id)->delete('srp_erp_incharge_assign');
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Successfully Deleted');
        }
    }

    function add_buyers_to_document_item(){
        $buyers_subsub = $this->input->post("buyers_for_cat");
        $selected_master_id = $this->input->post("selected_master_id");
        $selected_detail_id = $this->input->post("selected_detail_id");


        if(count($buyers_subsub)>0){
            foreach($buyers_subsub as $val){

                $this->db->select('*');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('activityDetailID', $selected_detail_id);
                $this->db->where('empID', $val);
                $this->db->where('activityMasterID', $selected_master_id);
                $this->db->where('documentID', 'PRQ');
                $this->db->from('srp_erp_incharge_assign');
                $added_emp= $this->db->get()->row_array();

                if(!$added_emp){

                    $data_buyer['documentID'] = 'PRQ';
                    $data_buyer['empID'] = $val;
                    $data_buyer['assignMasterID'] = NULL;
                    $data_buyer['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                    $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                    $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                    $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                    $data_buyer['createdUserName'] = $this->common_data['current_user'];

                    $data_buyer['activityMasterID'] = $selected_master_id;
                    $data_buyer['activityDetailID'] = $selected_detail_id;
                    $data_buyer['activityIsActive'] = 1;
            
                    $this->db->insert('srp_erp_incharge_assign', $data_buyer);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        return array('e', ' Update Failed ' . $this->db->_error_message());
                        $this->db->trans_rollback();
                    
                    } else {
                        return array('s', 'Updated Successfully.',$selected_master_id,$selected_detail_id);
                        $this->db->trans_commit();
                
                    }

                }else{
                    return array('w', 'Alredy Added','');
                }
            }
        }
  
    }

    function fetch_close_document_details()
    {
        $documentID = $this->input->post("documentID");
        $MasterID = $this->input->post("MasterID");
        $DetailsID = $this->input->post("DetailsID");
        $tbName = $this->input->post("tbName");
        $master_col_name_cl = $this->input->post("master_col_name_cl");
        $convertFormat = convert_date_format_sql();

        $this->db->select('srp_employeesdetails.ECode,srp_employeesdetails.Ename1,DATE_FORMAT('.$tbName.'.isClosedDate,\'' . $convertFormat . '\') AS isClosedDate,'.$tbName.'.isClosedComment,'.$tbName.'.isAcknowledgeByName,'.$tbName.'.isAcknowledgeYN,'.$tbName.'.isAcknowledgeComment,DATE_FORMAT('.$tbName.'.isAcknowledgeDate,\'' . $convertFormat . '\') AS isAcknowledgeDate');
        $this->db->where('companyID', current_companyID());
        $this->db->where($master_col_name_cl,$DetailsID);
        $this->db->from( $tbName);
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = '.$tbName.'.isClosedBy');
        return  $this->db->get()->row_array();

        
    }

    /**
     * Get purchase request header
     *
     * @param integer $id
     * @return array
     */
    public function getPurchaseRequest($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_purchaserequestmaster');
        $this->db->where('purchaseRequestID', $id);
        return $this->db->get()->row_array();
    }

    /**
     * Get purchase request detail
     *
     * @param integer $id
     * @return array
     */
    public function getPurchaseRequestDetail($id)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_purchaserequestdetails');
        $this->db->where('purchaseRequestDetailsID', $id);
        return $this->db->get()->row_array();
    }

    public function add_quotation_version_po(){

        $this->db->trans_start();
        $masterID = trim($this->input->post('masterID') ?? '');

        $_POST['purchaseRequestID'] = $masterID;
        $_POST['json'] = true;

        $controllerInstance = & get_instance();

        $view = $controllerInstance->load_purchase_request_conformation();

        $this->db->select('*');
        $this->db->where('purchaseRequestID', $masterID);
        $invoice_data = $this->db->get('srp_erp_purchaserequestmaster')->row_array();

        //check for po 
        $this->db->select('*');
        $this->db->where('prMasterID', $masterID);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $purchaseOrderEx = $this->db->get('srp_erp_purchaseorderdetails')->row_array();

        if($purchaseOrderEx){
            return array('e', 'Purchase Request already pulled to the PO');
            $this->db->trans_rollback();
        }

        //Add version to table
        $data = array();
        $data['documentID'] = $invoice_data['documentID'];
        $data['documentMasterID'] = $invoice_data['purchaseRequestID'];
        if($invoice_data['versionNo'] > 0){
            $data['documentCode'] = $invoice_data['purchaseRequestCode'].'(V'.$invoice_data['versionNo'].')';
        }else{
            $data['documentCode'] = $invoice_data['purchaseRequestCode'];
        }
       
        $data['versionNo'] = $invoice_data['versionNo'];
        $data['createdDate'] = $this->common_data['current_date'];
        $data['createdBy'] = $this->common_data['current_user'];
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['header'] = json_encode($invoice_data);
        $data['viewjson'] = $view;

        //Add version to table
        $this->db->insert('srp_erp_document_version',$data);

        //Delete approvals
        $this->load->library('approvals');
        $status = $this->approvals->approve_delete($masterID, 'PRQ');

      
        if($status == 1){
            //update master record
            $this->db->query("UPDATE srp_erp_purchaserequestmaster SET versionNo = (versionNo +1) , confirmedYN = 0 , approvedYN = 0 WHERE purchaseRequestID='{$masterID}'");
    
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('e', ' Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
        
        } else {
            return array('s', 'New Version of PRQ Created Successfully.');
            $this->db->trans_commit();
    
        }

    }
}
