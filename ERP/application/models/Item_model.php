<?php

class Item_model extends ERP_Model
{
    function save_item_master()
    {
        $this->db->trans_start();
        $company_id=current_companyID();
        $ApprovalforItemMaster= getPolicyValues('AIM', 'All');

        if (!empty(trim($this->input->post('revanue') ?? '') && trim($this->input->post('revanue') != 'Select Revenue GL Account'))) {
            $revanue = explode('|', trim($this->input->post('revanue') ?? ''));
        }
        $cost = explode('|', trim($this->input->post('cost') ?? ''));
        $asste = explode('|', trim($this->input->post('asste') ?? ''));
        $mainCategory = explode('|', trim($this->input->post('mainCategory') ?? ''));
        $stockadjustment=explode('|', trim($this->input->post('stockadjustment') ?? ''));
        $isactive = 0;
        $isSellThis = 1;
        $isBuyThis = 1;

        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        if (empty($this->input->post('sell_this'))) {
            $isSellThis = 0;
        }
        if (empty($this->input->post('buy_this'))) {
            $isBuyThis = 0;
        }

        $generatedtype = $this->input->post('generatedtype');
        $uom = explode('|', trim($this->input->post('uom') ?? ''));
        $data['isMfqItem'] = trim($this->input->post('isMfqItem') ?? '');
        $data['isActive'] = $isactive;
        $data['allowedtoSellYN'] = $isSellThis;
        $data['allowedtoBuyYN'] = $isBuyThis;
        $data['seconeryItemCode'] = trim($this->input->post('seconeryItemCode') ?? '');
        $data['secondaryUOMID'] = trim($this->input->post('secondaryUOMID') ?? '');
        $data['itemName'] = $this->input->post('itemName');
        $data['itemDescription'] = $this->input->post('itemDescription');
        $data['subcategoryID'] = trim($this->input->post('subcategoryID') ?? '');
        $data['subSubCategoryID'] = trim($this->input->post('subSubCategoryID') ?? '');
        $data['subSubSubCategoryID'] = trim($this->input->post('subSubSubCategoryID') ?? '');
        $data['partNo'] = trim($this->input->post('partno') ?? '');
        $data['reorderPoint'] = trim($this->input->post('reorderPoint') ?? '');
        $data['maximunQty'] = trim($this->input->post('maximunQty') ?? '');
        $data['minimumQty'] = trim($this->input->post('minimumQty') ?? '');
        $data['defaultUnitOfMeasureID'] = trim($this->input->post('defaultUnitOfMeasureID') ?? '');
        $data['defaultUnitOfMeasure'] = trim($uom[0] ?? '');
        $data['comments'] = trim($this->input->post('comments') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate'] = 1;
        $data['companyLocalSellingPrice'] = trim($this->input->post('companyLocalSellingPrice') ?? '');
        $data['companyLocalPurchasingPrice'] = trim($this->input->post('companyLocalPurchasingPrice') ?? '');
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
        $data['companyReportingPurchasingPrice'] = ((float)$data['companyLocalPurchasingPrice'] / (float)$data['companyReportingExchangeRate']);

        $data['isSubitemExist'] = trim($this->input->post('isSubitemExist') ?? '');
        $data['subItemapplicableon'] = (($this->input->post('isSubitemExist') == 1)?$this->input->post('subItem') :1);

        if($this->input->post('revanueGLAutoID')){
            $data['mainCategory'] = trim($mainCategory[1] ?? '');
            if ($data['mainCategory'] == 'Fixed Assets') {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes') ?? '');
                $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes') ?? '');
                $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes') ?? '');
                $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes') ?? '');

                $data['costGLAutoID'] = '';
                $data['costSystemGLCode'] = '';
                $data['costGLCode'] = '';
                $data['costDescription'] = '';
                $data['costType'] = '';

                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->input->post('stockadjust') ?? '');
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0] ?? '');
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1] ?? '');
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2] ?? '');
                $data['stockAdjustmentType'] = trim($stockadjustment[3] ?? '');

            } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                $data['assteGLAutoID'] = '';
                $data['assteSystemGLCode'] = '';
                $data['assteGLCode'] = '';
                $data['assteDescription'] = '';
                $data['assteType'] = '';
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');

            } elseif ($data['mainCategory'] == 'Inventory') {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                $data['assteGLCode'] = trim($asste[1] ?? '');
                $data['assteDescription'] = trim($asste[2] ?? '');
                $data['assteType'] = trim($asste[3] ?? '');
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['stockAdjustmentGLAutoID'] = trim($this->input->post('stockadjust') ?? '');
                $data['stockAdjustmentSystemGLCode'] = trim($stockadjustment[0] ?? '');
                $data['stockAdjustmentGLCode'] = trim($stockadjustment[1] ?? '');
                $data['stockAdjustmentDescription'] = trim($stockadjustment[2] ?? '');
                $data['stockAdjustmentType'] = trim($stockadjustment[3] ?? '');
                $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');

            } else {
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                $data['assteGLCode'] = trim($asste[1] ?? '');
                $data['assteDescription'] = trim($asste[2] ?? '');
                $data['assteType'] = trim($asste[3] ?? '');
                $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
                if (!empty($revanue)) {
                    $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                    $data['revanueGLCode'] = trim($revanue[1] ?? '');
                    $data['revanueDescription'] = trim($revanue[2] ?? '');
                    $data['revanueType'] = trim($revanue[3] ?? '');
                }
                $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
                $data['costSystemGLCode'] = trim($cost[0] ?? '');
                $data['costGLCode'] = trim($cost[1] ?? '');
                $data['costDescription'] = trim($cost[2] ?? '');
                $data['costType'] = trim($cost[3] ?? '');
            }

        }

        if (trim($this->input->post('itemAutoID') ?? '')) {
            $itemauto=$this->input->post('itemAutoID');
            $barcode= $this->input->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND itemAutoID != '$itemauto' AND deletedYN = 0")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already assigned.');
            }
            else
            {
                $itemAutoID=trim($this->input->post('itemAutoID') ?? '');
                $barcode = trim($this->input->post('barcode') ?? '');
                $bar=$this->db->query("SELECT * FROM `srp_erp_itemmaster` WHERE itemAutoID=$itemAutoID")->row_array();
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $bar['itemSystemCode'];
                }

                $this->db->where('itemAutoID', $itemAutoID);
                $this->db->update('srp_erp_itemmaster', $data);

                if(($ApprovalforItemMaster== 0 || $ApprovalforItemMaster == NULL)){
                    $this->load->library('Approvals');
                    $approvals_status = $this->approvals->auto_approve($itemAutoID, 'srp_erp_itemmaster','itemAutoID', 'INV',$bar['itemSystemCode'],$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
                }
                $this->db->trans_complete();
                $last_id = $this->input->post('itemAutoID');
                if ($this->db->trans_status() === FALSE) {
                   // $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    //$this->lib_log->log_event('Item','Error','Item : ' .$data['itemSystemCode'].' - '. $data['itemName'] . ' Update Failed '.$this->db->_error_message(),'Item');
                    return array('e','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());

                } else {

                    update_warehouseitems($last_id,$data['barcode'],$data['isActive'],$data['companyLocalSellingPrice']);
                   // $this->session->set_flashdata('s', 'Item : ' . $data['itemName'] . ' Updated Successfully.');
                    $this->db->trans_commit();
                    //$this->lib_log->log_event('Item','Success','Item : ' . $data['companyCode'].' Update Successfully. Affected Rows - ' . $this->db->affected_rows(),'Item');
                   // return array('status' => true, 'last_id' => $this->input->post('itemAutoID'),'barcode'=>$data['barcode']);
                    return array('s','Item : ' . $data['itemName'] . ' Updated Successfully.',$last_id,$data['barcode']);
                }
            }

        } else {
            $barcode= $this->input->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND deletedYN = 0")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already exist.');
            }else
            {
                $uom = explode('|', trim($this->input->post('uom') ?? ''));
                $this->load->library('sequence');
                // $this->db->select('codePrefix');
                // $this->db->where('itemCategoryID', $this->input->post('mainCategoryID'));
                // $code = $this->db->get('srp_erp_itemcategory')->row_array();
                $data['isActive'] = $isactive;
                $data['itemImage'] = 'no-image.png';
                $data['defaultUnitOfMeasureID'] = trim($this->input->post('defaultUnitOfMeasureID') ?? '');
                $data['defaultUnitOfMeasure'] = trim($uom[0] ?? '');
                $data['mainCategoryID'] = trim($this->input->post('mainCategoryID') ?? '');
                $data['mainCategory'] = trim($mainCategory[1] ?? '');
                $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);
                $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes') ?? '');
                $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes') ?? '');
                $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes') ?? '');
                $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes') ?? '');

                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                    $data['faCostGLAutoID'] = trim($this->input->post('COSTGLCODEdes') ?? '');
                    $data['faACCDEPGLAutoID'] = trim($this->input->post('ACCDEPGLCODEdes') ?? '');
                    $data['faDEPGLAutoID'] = trim($this->input->post('DEPGLCODEdes') ?? '');
                    $data['faDISPOGLAutoID'] = trim($this->input->post('DISPOGLCODEdes') ?? '');

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';
                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                        $data['revanueGLCode'] = trim($revanue[1] ?? '');
                        $data['revanueDescription'] = trim($revanue[2] ?? '');
                        $data['revanueType'] = trim($revanue[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }

                else {
                    $data['assteGLAutoID'] = trim($this->input->post('assteGLAutoID') ?? '');
                    $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                    $data['assteGLCode'] = trim($asste[1] ?? '');
                    $data['assteDescription'] = trim($asste[2] ?? '');
                    $data['assteType'] = trim($asste[3] ?? '');
                    $data['revanueGLAutoID'] = trim($this->input->post('revanueGLAutoID') ?? '');
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanue[0] ?? '');
                        $data['revanueGLCode'] = trim($revanue[1] ?? '');
                        $data['revanueDescription'] = trim($revanue[2] ?? '');
                        $data['revanueType'] = trim($revanue[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($this->input->post('costGLAutoID') ?? '');
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }
                $data['companyLocalWacAmount'] = 0.00;
                $data['companyReportingWacAmount'] = 0.00;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $subsubCategoryBaseNewSequencePolicy = getPolicyValues('IMSSNS', 'All');
                
                if($subsubCategoryBaseNewSequencePolicy==1){
                    

                    $data['itemSystemCode'] = $this->sequence->sequence_generator_item_with_sub_sub_category(
                        trim($mainCategory[0] ?? ''),
                        0,
                        $data['companyID'],
                        $data['companyCode'],
                        $this->getItemCategoryPrefix($data['subcategoryID']),
                        $this->getItemCategoryPrefix($data['subSubCategoryID']),
                        $this->getItemCategoryPrefix($data['subSubSubCategoryID']),
                        $this->input->post('mainCategoryID')
                    );
                    
                }else{

                    $data['itemSystemCode'] = $this->sequence->sequence_generator_item(
                        trim($mainCategory[0] ?? ''),
                        0,
                        $data['companyID'],
                        $data['companyCode'],
                        $this->getItemCategoryPrefix($data['subcategoryID']),
                        $this->getItemCategoryPrefix($data['subSubCategoryID']),
                        $this->getItemCategoryPrefix($data['subSubSubCategoryID'])
                    );

                }
                // $data['itemSystemCode'] = $this->sequence->sequence_generator_item(
                //     trim($mainCategory[0] ?? ''),
                //     0,
                //     $data['companyID'],
                //     $data['companyCode'],
                //     $this->getItemCategoryPrefix($data['subcategoryID']),
                //     $this->getItemCategoryPrefix($data['subSubCategoryID']),
                //     $this->getItemCategoryPrefix($data['subSubSubCategoryID'])
                // );
//check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if(!empty($codeExist)){
                    //$this->db->query("UPDATE srp_erp_documentcodemaster SET serialNo = (serialNo-1)  WHERE documentID='{$mainCategory[0]}' AND companyID = '{$company_id}'");
                    $this->session->set_flashdata('w', 'Item System Code : ' . $codeExist['itemSystemCode'] . ' Already Exist ');
                    $this->db->trans_rollback();

                    return array('status' => false);

                }

                $barcode = trim($this->input->post('barcode') ?? '');
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $data['itemSystemCode'];
                }
                $this->db->insert('srp_erp_itemmaster', $data);
                $last_id = $this->db->insert_id();
                
                if(($ApprovalforItemMaster== 0 || $ApprovalforItemMaster == NULL)){

                    $this->load->library('Approvals');
                    $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_itemmaster','itemAutoID', 'INV',$data['itemSystemCode'],$this->common_data['current_date']);

                    if ($approvals_status==1) {
                        //return array('s', 'Document confirmed Successfully');
                    }else if($approvals_status ==3){
                        return array('w', 'There are no users exist to perform approval for this document.');
                        $this->db->trans_rollback();
                    } else {
                        return array('e', 'Document confirmation failed');
                        $this->db->trans_rollback();
                    }
                }

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    //$this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                    $this->db->trans_rollback();
                    //return array('status' => false);
                    return array('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                } else {
                    //$this->session->set_flashdata('s', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.');
                    $this->db->trans_commit();

                    if($generatedtype == 'third')
                    {
                        $itemmaster = $this->db->query("SELECT CONCAT(itemDescription,'-',itemSystemCode,'-',partNo,'-',seconeryItemCode) as itemcode,defaultUnitOfMeasureID
                                                            FROM `srp_erp_itemmaster` where companyID  = $company_id AND itemAutoID = $last_id ")->row_array();

                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode'],$itemmaster['itemcode'],$itemmaster['defaultUnitOfMeasureID']);
                    }else
                    {
                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode']);
                    }

                   // return array('status' => true, 'last_id' => $last_id,'barcode'=>$data['barcode']);
                }
            }


        }
    }

    function item_image_upload()
    {
        $this->load->library('s3');
        $itemautoid = trim($this->input->post('faID') ?? '');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT itemImage FROM `srp_erp_itemmaster` where companyID = '{$companyid}'  AND itemAutoID = '{$itemautoid}' AND itemImage != \"no-image.png\"")->row_array();
        if(!empty($itemimageexist))
        {
            $this->s3->delete('uploads/itemMaster/'.$itemimageexist['itemImage']);
        }

        //$output_dir = "uploads/itemMaster/";
     /*   if (!file_exists($output_dir)) {
            mkdir("uploads/itemMaster/", 007);
        }*/
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);

        //move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $fileName = 'Item_' . trim($this->input->post('faID') ?? '') .'_'.$this->common_data['company_data']['company_code'].'.' . $info->getExtension();

        $file = $_FILES['files'];
        if($file['error'] == 1){
            $this->session->set_flashdata('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
            return array('status' => false);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            $this->session->set_flashdata('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");
            return array('status' => false);
        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if($size > 5){
            $this->session->set_flashdata('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");
            return array('status' => false);
        }
        $path = "uploads/itemMaster/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            $this->session->set_flashdata('e', "Error in document upload location configuration");
            return array('status' => false);
        }
        $this->db->trans_start();
        $data['itemimage'] = $fileName;
        $data['timestamp'] = date('Y-m-d H:i:s');

        $this->db->where('itemAutoID', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_itemmaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', "Image Upload Failed." . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Image uploaded  Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => trim($this->input->post('faID') ?? ''));
        }
    }

    function finance_category($id)
    {
        $this->db->select('categoryTypeID');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_itemcategory')->row('categoryTypeID');
    }


    function load_item_header()
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }

    function delete_item()
    {
        $itemAutoID = $this->input->post('itemAutoID');
        $companyID = current_companyID();
        $items = $this->db->query("SELECT
	checkitemtransaction.*,
CASE
	
	WHEN checkitemtransaction.doctype = \"DO\" THEN
	srp_erp_deliveryorder.DOCode 
	WHEN checkitemtransaction.doctype = \"QUT\" THEN
	srp_erp_contractmaster.contractCode 
	WHEN checkitemtransaction.doctype = \"buybackdispatch\" THEN
	srp_erp_buyback_dispatchnote.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"buybackGRN\" THEN
	srp_erp_buyback_grn.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"CRMQUT\" THEN
	srp_erp_crm_quotation.quotationCode 
	WHEN checkitemtransaction.doctype = \"CINV\" THEN
	srp_erp_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"RV\" THEN
	srp_erp_customerreceiptmaster.RVcode 
	WHEN checkitemtransaction.doctype = \"GRV\" THEN
	srp_erp_grvmaster.grvPrimaryCode
    WHEN checkitemtransaction.doctype = \"MI\" THEN
	srp_erp_itemissuemaster.itemIssueCode 
    WHEN checkitemtransaction.doctype = \"MR\" THEN
	srp_erp_materialrequest.MRCode 
    WHEN checkitemtransaction.doctype = \"MRN\" THEN
	srp_erp_materialreceiptmaster.mrnCode 
    WHEN checkitemtransaction.doctype = \"MFQBOM\" THEN
	srp_erp_mfq_billofmaterial.documentCode 
    WHEN checkitemtransaction.doctype = \"MFQINV\" THEN
	srp_erp_mfq_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"PV\" THEN
	srp_erp_paymentvouchermaster.PVcode 
    WHEN checkitemtransaction.doctype = \"BSI\" THEN
	srp_erp_paysupplierinvoicemaster.bookingInvCode 
	WHEN checkitemtransaction.doctype = \"POSINV\" THEN
	srp_erp_pos_invoice.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"PO\" THEN
	srp_erp_purchaseordermaster.purchaseOrderCode 
	WHEN checkitemtransaction.doctype = \"PRD\" THEN
	srp_erp_purchaserequestmaster.purchaseRequestCode 
	WHEN checkitemtransaction.doctype = \"SR\" THEN
	srp_erp_salesreturnmaster.salesReturnCode 
    WHEN checkitemtransaction.doctype = \"SA\" THEN
    stockA.stockAdjustmentCode 
    WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.stockCountingCode
	   WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stockTransferCode	
	ELSE \"\" 
	END documentcode,
	CASE
	
	WHEN checkitemtransaction.doctype = \"SA\" THEN
	stockA.sacompanyID 
	WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.sccompanyID 
	WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stcompanyID
	
	
	ELSE
	checkitemtransaction.companyID
	END companyidNEw
	
FROM
	`checkitemtransaction`
	LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = checkitemtransaction.doccode 
	AND doctype = 'DO'
	LEFT JOIN srp_erp_contractmaster ON srp_erp_contractmaster.contractAutoID = checkitemtransaction.doccode 
	AND doctype = 'QUT' 
	LEFT JOIN srp_erp_buyback_dispatchnote On srp_erp_buyback_dispatchnote.dispatchAutoID = checkitemtransaction.doccode AND doctype = 'buybackdispatch'
	LEFT JOIN srp_erp_buyback_grn On srp_erp_buyback_grn.grnAutoID = checkitemtransaction.doccode AND doctype = 'buybackGRN' 
	LEFT JOIN srp_erp_crm_quotation on srp_erp_crm_quotation.quotationAutoID = checkitemtransaction.doccode AND doctype = 'CRMQUT'  
	LEFT JOIN srp_erp_customerinvoicemaster on srp_erp_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'CINV' 
	LEFT JOIN srp_erp_customerreceiptmaster on srp_erp_customerreceiptmaster.receiptVoucherAutoId = checkitemtransaction.doccode AND doctype = 'RV' 
	LEFT JOIN srp_erp_grvmaster on srp_erp_grvmaster.grvAutoID = checkitemtransaction.doccode AND doctype = 'GRV' 
	LEFT JOIN srp_erp_itemissuemaster on srp_erp_itemissuemaster.itemIssueAutoID = checkitemtransaction.doccode AND doctype = 'MI' 
	LEFT JOIN srp_erp_materialrequest on srp_erp_materialrequest.mrAutoID = checkitemtransaction.doccode AND doctype = 'MR' 
	LEFT JOIN srp_erp_materialreceiptmaster on srp_erp_materialreceiptmaster.mrnAutoID = checkitemtransaction.doccode AND doctype = 'MRN'  
	LEFT JOIN srp_erp_mfq_billofmaterial on srp_erp_mfq_billofmaterial.bomMasterID = checkitemtransaction.doccode AND doctype = 'MFQBOM'  
	LEFT JOIN srp_erp_mfq_customerinvoicemaster on srp_erp_mfq_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'MFQINV'  
	LEFT JOIN srp_erp_paymentvouchermaster on srp_erp_paymentvouchermaster.payVoucherAutoId = checkitemtransaction.doccode AND doctype = 'PV' 
    LEFT JOIN srp_erp_paysupplierinvoicemaster on srp_erp_paysupplierinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND doctype = 'BSI' 
    LEFT JOIN srp_erp_pos_invoice on srp_erp_pos_invoice.invoiceID = checkitemtransaction.doccode AND doctype = 'POSINV' 
    LEFT JOIN srp_erp_purchaseordermaster on srp_erp_purchaseordermaster.purchaseOrderID = checkitemtransaction.doccode AND doctype = 'PO'  
    LEFT JOIN srp_erp_purchaserequestmaster on srp_erp_purchaserequestmaster.purchaseRequestID = checkitemtransaction.doccode AND doctype = 'PRD'  
    LEFT JOIN srp_erp_salesreturnmaster on srp_erp_salesreturnmaster.salesReturnAutoID = checkitemtransaction.doccode AND doctype = 'SR'  
   	LEFT JOIN (SELECT srp_erp_stockadjustmentmaster.companyID as sacompanyID ,stockAdjustmentAutoID,stockAdjustmentCode from srp_erp_stockadjustmentmaster LEFT JOIN checkitemtransaction on srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = checkitemtransaction.doccode where doctype = 'SA'  
	GROUP BY
		stockAdjustmentAutoID
	 ) stockA on stockA.stockAdjustmentAutoID = checkitemtransaction.doccode    
	 
	  	LEFT JOIN (SELECT srp_erp_stockcountingmaster.companyID as sccompanyID ,stockCountingAutoID,stockCountingCode from srp_erp_stockcountingmaster LEFT JOIN checkitemtransaction on srp_erp_stockcountingmaster.stockCountingAutoID = checkitemtransaction.doccode where doctype = 'SC'  
	GROUP BY
    stockCountingAutoID
	 ) stockcounting on stockcounting.stockCountingAutoID = checkitemtransaction.doccode  
	 
	 LEFT JOIN (
	SELECT
		srp_erp_stocktransfermaster.companyID AS stcompanyID,
		stockTransferAutoID,
		stockTransferCode 
	FROM
		srp_erp_stocktransfermaster
		LEFT JOIN checkitemtransaction ON srp_erp_stocktransfermaster.stockTransferAutoID = checkitemtransaction.doccode 
	WHERE
		doctype = 'ST' 
        group by 
		stockTransferAutoID
	) stocktransfer ON stocktransfer.stockTransferAutoID = checkitemtransaction.doccode 
	
WHERE
 checkitemtransaction.itemAutoID = '{$itemAutoID}'
GROUP BY 
doctype,doccode
HAVING 
companyidNEw = '{$companyID}'")->result_array();
        if($items)
            {
        $this->session->set_flashdata('e', 'You cannot Delete this record. Because this item has been pulled.');
        return false;
            }
        $data['deletedYN'] = 1;
        $data['isActive'] = 0;
        $data['deleteByEmpID'] = $this->common_data['current_userID'];
        $data['deletedDatetime'] = $this->common_data['current_date'];

        $this->db->where('itemAutoID', $itemAutoID);
        $this->db->update('srp_erp_itemmaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->session->set_flashdata('s', 'Item Deleted Successfully');
            return true;
        } else {
            $this->session->set_flashdata('e', 'Item Deletion Failed');
        }

       /* $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->delete('srp_erp_itemmaster');
        $this->session->set_flashdata('s', 'Item Deleted Successfully');
        return true;*/
    }

    function load_subcat()
    {
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemcategory');
        return $subcat = $this->db->get()->result_array();
    }

    function load_se_code_temp(){
        $subsubCategoryBaseNewSequencePolicy= getPolicyValues('IMSSNS', 'All');
        $id=$this->input->post('subid');
        $subcategoryID=$this->input->post('subcategoryID');
        $subSubCategoryID=$this->input->post('subSubCategoryID');
        $subSubSubCategoryID=$this->input->post('subSubSubCategoryID');
        $companyID = $this->common_data['company_data']['company_id'];
        $companyCode = $this->common_data['company_data']['company_code'];
       // print_r($id);exit;
        $this->db->select('*');
        $this->db->where('itemCategoryID', $id);
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemcategory');
        $cat = $this->db->get()->row_array();

        if($subsubCategoryBaseNewSequencePolicy==1){
                    

            $code = $this->sequence->sequence_generator_item_with_sub_sub_category_temp(
                $this->getItemCategoryPrefix($id),
                0,
                $companyID,
                $companyCode,
                $this->getItemCategoryPrefix($subcategoryID),
                $this->getItemCategoryPrefix($subSubCategoryID),
                $this->getItemCategoryPrefix($subSubSubCategoryID),
                $id
            );
            
        }else{

            $code = $this->sequence->sequence_generator_item_temp(
                $this->getItemCategoryPrefix($id),
                0,
                $companyID,
                $companyCode,
                $this->getItemCategoryPrefix($subcategoryID),
                $this->getItemCategoryPrefix($subSubCategoryID),
                $this->getItemCategoryPrefix($subSubSubCategoryID)
            );

        }

        // if($cat){
        //     $this->load->library('sequence');
        //     $code = $this->sequence->sequence_generator_for_seconday_code($cat['codePrefix']);
        
            if($code){
                return array('s',$code);
            }else{
                return array('e','');
            }
        
        // }else{
        //     return array('e','');
        // }

    }

    function load_subsubcat()
    {
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subsubid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemcategory');
        return $subsubcat = $this->db->get()->result_array();
    }
    function load_subitem(){
        $this->db->select('itemAutoID,itemName,subcategoryID,itemSystemCode');
        $this->db->where('subcategoryID', $this->input->post('subitemid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_itemmaster');
        return $subitem = $this->db->get()->result_array();
    }

    function edit_item()
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('id'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }


    function item_master_img_uplode()
    {
        $output_dir = "uploads/itemmaster/";
        $itemAutoID = trim($this->input->post('image_itemmaster_hn') ?? '');
        $attachment_file = $_FILES["img_file"];
        $info = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = 'ITM' . "_" . $itemAutoID . '.' . $info->getExtension();
        move_uploaded_file($_FILES["img_file"]["tmp_name"], $output_dir . $fileName);
        $this->db->where('itemAutoID', $itemAutoID);
        $result = $this->db->update('srp_erp_itemmaster', array('itemImage' => $output_dir . $fileName));
        if ($result) {
            $this->session->set_flashdata('s', 'Image Uploaded Successfully');
            return true;
        }
    }

    function img_uplode()
    {
        $output_dir = "images/item/";
        $attachment_file = ($_FILES["img_file"] ? $_FILES["img_file"] : 'no-image.png');
        $info = new SplFileInfo($_FILES["img_file"]["name"]);
        $fileName = trim($this->input->post('item_id') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["img_file"]["tmp_name"], $output_dir . $fileName);
        $this->db->where('itemAutoID', trim($this->input->post('item_id') ?? ''));
        $this->db->update('srp_erp_itemmaster', array('itemImage' => $fileName));
        return array('status' => true);
    }

    function load_gl_codes()
    {
        $this->db->select('revenueGL,costGL,assetGL,faCostGLAutoID,faACCDEPGLAutoID,faDEPGLAutoID,faDISPOGLAutoID,stockAdjustmentGL');
        $this->db->where('itemCategoryID', $this->input->post('itemCategoryID'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function load_item_gl_codes()
    {
        $this->db->select('revanueGLCode,costGLCode,revanueGLAutoID,costGLAutoID');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }

    function changeitemactive()
    {

        $data['isActive'] = ($this->input->post('chkedvalue'));
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $result = $this->db->update('srp_erp_itemmaster', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Records Updated Successfully');
            return true;
        }
    }

    function load_category_type_id()
    {
        $this->db->select('itemCategoryID,categoryTypeID');
        $this->db->where('itemCategoryID', $this->input->post('itemCategoryID'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function load_unitprice_exchangerate()
    {

        $localwacAmount = trim($this->input->post('LocalWacAmount') ?? '');
        $this->db->select('purchaseOrderID,transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces');
        $this->db->where('purchaseOrderID', $this->input->post('poID'));
        $result = $this->db->get('srp_erp_purchaseordermaster')->row_array();
        $localCurrency = currency_conversion($result['companyLocalCurrency'], $result['transactionCurrency']);
        $unitprice = round(($localwacAmount / $localCurrency['conversion']), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function fetch_sales_price()
    {
        $unitOfMeasureID = trim($this->input->post('unitOfMeasureID') ?? '');

        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $itemDetail = $this->db->get('srp_erp_itemmaster')->row_array();

        $defaultUOM = $itemDetail["defaultUnitOfMeasureID"];//default unit of measure

        //$conversionUOM = conversionRateUOM_id($unitOfMeasureID,$defaultUOM);

        $this->db->select('transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
        $this->db->where($this->input->post('primaryKey'), $this->input->post('id'));
        $result = $this->db->get($this->input->post('tableName'))->row_array();

        //$localCurrency = currency_conversion($result['companyLocalCurrency'] ,$result['transactionCurrency']);
        $localCurrencyER = 1 / $result['companyLocalExchangeRate'];

        $salesprice = trim($this->input->post('salesprice') ?? '');
        /* echo $this->input->post('salesprice')."<br>";
         echo $localCurrencyER;*/

        $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);

        return array('status' => true, 'amount' => $unitprice);
    }

    function fetch_sales_price_customerWise($salesPrice = null)
    {
        if(empty($salesPrice)) {
            $salesPrice = $this->input->post('salesprice');
        }
        $current_date = current_format_date();
        $convertFormat = convert_date_format_sql();
        $itemAutoID = $this->input->post('itemAutoID');
        $customerID = $this->input->post('customerAutoID');
        $policy = getPolicyValues('CPS', 'All');
        $catlogue = getPolicyValues('PMIC', 'All');

        $this->db->select('contractDate,isBackToBack,transactionCurrencyID,transactionExchangeRate,transactionCurrency,companyLocalCurrency,transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
        $this->db->where($this->input->post('primaryKey'), $this->input->post('id'));
        $result = $this->db->get($this->input->post('tableName'))->row_array();

        $localCurrencyER = 1 / $result['companyLocalExchangeRate'];

        if($policy == 1 && !empty($customerID)){
            $this->db->select("salesPrice, isModificationAllowed, DATE_FORMAT(applicableDateFrom,' $convertFormat ') AS applicableDateFrom, DATE_FORMAT(applicableDateTo,' $convertFormat ') AS applicableDateTo");
            $this->db->where('itemAutoID', $itemAutoID);
            $this->db->where('customerAutoID', $customerID);
            $this->db->where('isActive', 1);
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $customerPrice = $this->db->get('srp_erp_customeritemprices')->row_array();

            if(!empty($customerPrice))
            {
                if((empty($customerPrice['applicableDateFrom']) && empty($customerPrice['applicableDateTo'])) || (strtotime($customerPrice['applicableDateFrom']) <= strtotime($current_date) && empty($customerPrice['applicableDateTo'])) || (strtotime($customerPrice['applicableDateFrom']) <= strtotime($current_date) && strtotime($customerPrice['applicableDateTo']) >= strtotime($current_date))) {
                    $salesprice = $customerPrice['salesPrice'];

                    $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                    return array('status' => true, 'amount' => $unitprice);
                } else {

                    $salesprice = trim($salesPrice);
                    $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                    return array('status' => true, 'amount' => $unitprice);
                }
            }elseif($catlogue == 1 && $result['isBackToBack'] == 1){

                //pull items with catlogue values
                $this->db->where('itemAutoID',$itemAutoID);
                $this->db->where('approvedYN',1);
                $this->db->join('srp_erp_inventorycataloguemaster','srp_erp_inventorycataloguemaster.mrAutoID = srp_erp_inventorycataloguedetails.mrAutoID');
                $catlogues = $this->db->from('srp_erp_inventorycataloguedetails')->get()->result_array();

                $price_arr = array();
                foreach($catlogues as $cat_value){

                    if(($cat_value['fromDate'] <= $result['contractDate']) && ($cat_value['toDate'] >= $result['contractDate'])){
                        $price_arr['status'] = true;
                        $price_arr['amount'] = $cat_value['transactionAmount'];
                    }

                }

                if(count($price_arr) > 0){
                    return $price_arr;
                }else{
                    $salesprice = trim($salesPrice);
                    $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                    return array('status' => true, 'amount' => $unitprice);

                }
                

            } else
            {

                $salesprice = trim($salesPrice);
                $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
                return array('status' => true, 'amount' => $unitprice);
            }


        } else {

            $salesprice = trim($salesPrice);
            $unitprice = round(($salesprice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
            return array('status' => true, 'amount' => $unitprice);
        }
    }

    function load_sub_itemMaster_view($itemCode){
        $this->db->select('itemmaster_sub.*, wh.wareHouseDescription as warehouseDescription', false);
        $this->db->join('srp_erp_itemmaster itemmaster', 'itemmaster.itemAutoID = itemmaster_sub.itemAutoID', 'left');
        $this->db->join('srp_erp_warehousemaster wh', 'wh.wareHouseAutoID = itemmaster_sub.wareHouseAutoID', 'left');
        $this->db->where('itemmaster.itemAutoID', $itemCode);
        $this->db->where("(itemmaster_sub.isSold <> 1 OR itemmaster_sub.isSold IS NULL)");
        $r = $this->db->get('srp_erp_itemmaster_sub itemmaster_sub')->result_array();
        return $r;
    }

    function save_item_percentage(){
        $updateArray = array();
        for($x = 0; $x < sizeof($this->input->post("itemAutoID")); $x++){
            $updateArray[] = array(
                'itemAutoID'=>$this->input->post("itemAutoID")[$x],
                'finCompanyPercentage' => $this->input->post("finCompanyPercentage")[$x],
                'pvtCompanyPercentage' => $this->input->post("pvtCompanyPercentage")[$x],
            );
        }
        $this->db->trans_start();
        $this->db->update_batch('srp_erp_itemmaster', $updateArray, 'itemAutoID');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e',"Percentage Update Failed");
        } else {
            $this->db->trans_commit();
            return array('s',"Percentage Updated Successfully");
        }
    }

    function save_item_bin_location(){
        $binLocationID=$this->input->post('binLocationID');
        $itemBinlocationID=$this->input->post('itemBinlocationID');
        $itemAutoID=$this->input->post('itemAutoID');
        $wareHouseAutoID=$this->input->post('wareHouseAutoID');
        if($itemBinlocationID){
            if(!empty($binLocationID)){
                $data['binLocationID'] = $binLocationID;
                $this->db->where('itemBinlocationID', $itemBinlocationID);
                $results = $this->db->update('srp_erp_itembinlocation', $data);
                if ($results) {
                    return array('s', 'successfully updated',$itemBinlocationID);
                }
            }else{
                $this->db->where('itemBinlocationID', $itemBinlocationID);
                $results =$this->db->delete('srp_erp_itembinlocation');
                if ($results) {
                    return array('s', 'Successfully deleted');
                }
            }
        }else{
            $this->db->set('itemAutoID', ($itemAutoID));
            $this->db->set('wareHouseAutoID', ($wareHouseAutoID));
            $this->db->set('binLocationID', ($binLocationID));
            $this->db->set('companyID', (current_companyID()));
            $this->db->set('createdUserGroup', ($this->common_data['user_group']));
            $this->db->set('createdPCID', ($this->common_data['current_pc']));
            $this->db->set('createdUserID', ($this->common_data['current_userID']));
            $this->db->set('createdDateTime', ($this->common_data['current_date']));
            $this->db->set('createdUserName', ($this->common_data['current_user']));
            $result = $this->db->insert('srp_erp_itembinlocation');
            $last_id = $this->db->insert_id();
            if ($result) {
                return array('s','successfully Saved',$last_id);
            }
        }
    }

    function load_item_bin_location(){
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        $this->db->where('companyID', current_companyID());
        return $this->db->get('srp_erp_itembinlocation')->result_array();
    }

    function fetch_customer_details_for_pricing(){
        $this->db->select('*');
        $this->db->where('companyID', current_companyID());
        return $this->db->get('srp_erp_customermaster')->result_array();
    }

    function fetch_outlet_details_for_pricing(){
        $this->db->select('*');
        $this->db->where('companyID', current_companyID());
        return $this->db->get('srp_erp_warehousemaster')->result_array();
    }

    function fetch_item_details_for_pricing(){
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemId'));
        return $this->db->get('srp_erp_itemmaster')->row_array();
    }

    function save_item_pricing_detail(){

        $this->db->trans_start();

        $priceId = $this->input->post('itemPriceedit');
        $type = trim($this->input->post('type') ?? '');
        $customer_arr = $this->input->post('customer[]');
        $customer = $this->input->post('customer');
        $itemAutoID = $this->input->post('itemAutoID');
        $outlet = trim($this->input->post('outlet') ?? '');
        $isActive = $this->input->post('isactive');
        $uom = $this->input->post('uom');
        $payment_arr = $this->input->post('payment_arr');

        $outlet_row = get_warehouse_details($outlet);
        $item_details = get_item_details($itemAutoID);

        if($customer == 'All' || empty($customer)){
            $customer_arr = array('All' => 'All');
        }

        $base_arr = array();
        foreach($customer_arr as $customer){
            
            if($isActive == 1){

                $check_is_active_record = check_item_pricing_exists($type,$itemAutoID,$customer,$outlet,$uom);
    
                if($check_is_active_record && ($priceId != $check_is_active_record['pricingAutoID'])){
                    $this->db->trans_complete();
                    //return array('e', 'Active pricing record already exists.');
                    $base_arr[$customer]['error'] = 'error';
                    $base_arr[$customer]['message'] = 'Active pricing record already exists.';
                    continue;
                }
            }
    
            if(($item_details['defaultUnitOfMeasureID'] == $uom) && $type == 'Price' && $payment_arr == null){
                $this->db->trans_complete();
                //return array('e', "You can not set price for item's default unit of measure.");
                $base_arr[$customer]['error'] = 'error';
                $base_arr[$customer]['message'] = 'You can not set price for item default unit of measure.';
                continue;
            }
            
    
            $data['pricingType'] = $type;
            $data['customer'] = (!empty($customer)) ? $customer : 'All';
            $data['uomMasterID'] = $uom;
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrencyCode'] = $this->common_data['company_data']['company_default_currency'];
    
            $data['outlet'] = (!empty($outlet_row['wareHouseCode'])) ? $outlet_row['wareHouseCode'] : 'All';
            $data['wareHouseAutoID'] = $outlet;
    
            $data['cost'] = $this->input->post('cost');
            $data['margin'] = $this->input->post('margin');
            $data['salesPrice'] = $this->input->post('salesprice');
            $data['discount'] = $this->input->post('discount');
            $data['rSalesPrice'] = $this->input->post('rsalesprice');
            $data['profit'] = $this->input->post('profit');
            $data['paymentMethod'] = $payment_arr;
            $data['itemMasterID'] = $this->input->post('itemAutoID');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $def = $this->input->post('isdefault');
            $isactive = $isActive;
    
            if($def){
                $data['isDefault']=true;
            }else{
                $data['isDefault']=false;
            }
    
            if($isactive){
                $data['isActive']=true;
            }else{
                $data['isActive']=false;
            }
    
            if($priceId != ""){
    
                $this->db->where('pricingAutoID', $priceId);
                $this->db->update('srp_erp_item_master_pricing', $data);
    
            }else{
    
                $data['createdDateTime'] = $this->common_data['current_date'];
    
                $this->db->insert('srp_erp_item_master_pricing', $data);
                $last_id = $this->db->insert_id();
    
            }
            $this->db->trans_complete();
    
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $base_arr[$customer]['error'] = 'error';
                $base_arr[$customer]['message'] = 'Update failed.';
                continue;
            } else {
                $this->db->trans_commit();
                $base_arr[$customer]['error'] = '';
                $base_arr[$customer]['message'] = 'Records Successfully Saved.';
                continue;
            }
        }

        $message_success = '';
        $message_error= '';
        foreach($base_arr as $key => $value){
            if($value['error']){
                $message_error .= $key  .' - '. $value['message'].' <br> ';
            }else{
                $message_success .= $key  .' - '. $value['message'].' <br> ';
            }
        }

        $this->session->set_flashdata('s', $message_success);
        $this->session->set_flashdata('e', $message_error);
        return TRUE;
        
    }

    function save_part_number_detail(){

        $this->db->trans_start();

        $partId = $this->input->post('itemPartNumberedit');
        $supplier = trim($this->input->post('supplier') ?? '');
        $partNumber = trim($this->input->post('partNumber') ?? '');
        $isActive = $this->input->post('isactive');

        $supplier_row = get_suppliermaster_details($supplier);
        

        $data['supplierAutoID'] = $supplier_row['supplierAutoID'];
        $data['supplierName'] = $supplier_row['supplierName'];

        $data['supplierSystemCode'] =$supplier_row['supplierSystemCode'];
        $data['itemAutoID'] = $this->input->post('itemAutoID');
        $data['partNumber'] = $partNumber;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];


        if($isActive){
            $data['isActive']=true;
        }else{
            $data['isActive']=false;
        }

        if($partId != ""){

            $this->db->where('partNumberAutoID', $partId);
            $this->db->update('srp_erp_item_part_number', $data);

        }else{

            $check_is_active_record = check_item_part_number_exists($supplier,$this->input->post('itemAutoID'),$partNumber);

            if($check_is_active_record){
                $this->db->trans_complete();
                return array('e', 'Item Part Number already exists.');
                exit;
            }else{
                $data['createdDateTime'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_item_part_number', $data);
                $last_id = $this->db->insert_id();
            }

        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Update failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function edit_item_pricing()
    {

        $this->db->select("srp_erp_item_master_pricing.*");
        $this->db->where('pricingAutoID', $this->input->post('id'));
        $result = $this->db->get('srp_erp_item_master_pricing')->row_array();

        // $result['salesPrice'] = format_number($result['salesPrice'],2);
        // $result['cost'] = format_number($result['cost'],2);
        // $result['rSalesPrice'] = format_number($result['rSalesPrice'],2);

        return $result;

    }

    function edit_item_part_number(){
        $this->db->select('*');
        $this->db->where('partNumberAutoID', $this->input->post('id'));
        return $this->db->get('srp_erp_item_part_number')->row_array();
    }

    function delete_item_pricing()
    {
        $this->db->where('pricingAutoID', $this->input->post('id'));
        $result= $this->db->delete('srp_erp_item_master_pricing');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function delete_item_part_number()
    {
        $this->db->where('partNumberAutoID', $this->input->post('id'));
        $result= $this->db->delete('srp_erp_item_part_number');
        if ($result) {
            $this->session->set_flashdata('s', 'Records Deleted Successfully');
            return true;
        }
    }

    function saveMultipleItemMaster()
    {
        $this->db->trans_start();
        $ApprovalforItemMaster= getPolicyValues('AIM', 'All');
        $secondaryUOM = getPolicyValues('SUOM', 'All');
        $mainCategoryID = $this->input->post('mainCategoryID');
        $mainCategoryIDselect = $this->input->post('mainCategoryIDselect');
        $mainCategory = $this->input->post('mainCategory');
        $revanue = $this->input->post('revanue');
        $costpost = $this->input->post('cost');
        $asstepost = $this->input->post('asste');
        $stockadjustment = $this->input->post('stockadjustment');
        $seconeryItemCode = $this->input->post('seconeryItemCode');
        $itemName = $this->input->post('itemName');
        $itemDescription = $this->input->post('itemDescription');
        $subcategoryID = $this->input->post('subcategoryID');
        $subSubCategoryID = $this->input->post('subSubCategoryID');
        $partno = $this->input->post('partno');
        $reorderPoint = $this->input->post('reorderPoint');
        $maximunQty = $this->input->post('maximunQty');
        $minimumQty = $this->input->post('minimumQty');
        $comments = $this->input->post('comments');
        $companyLocalSellingPrice = $this->input->post('companyLocalSellingPrice');
        $revanueGLAutoID = $this->input->post('revanueGLAutoID');
        $assteGLAutoID = $this->input->post('assteGLAutoID');
        $COSTGLCODEdes = $this->input->post('COSTGLCODEdes');
        $ACCDEPGLCODEdes = $this->input->post('ACCDEPGLCODEdes');
        $DEPGLCODEdes = $this->input->post('DEPGLCODEdes');
        $DISPOGLCODEdes = $this->input->post('DISPOGLCODEdes');
        $stockadjust = $this->input->post('stockadjust');
        $costGLAutoID = $this->input->post('costGLAutoID');
        $barcode = $this->input->post('barcode');
        $uom = $this->input->post('uom');
        if($secondaryUOM==1){
            $uomSecondary = $this->input->post('uomSecondary');
            $secondaryUnitOfMeasureID = $this->input->post('secondaryUnitOfMeasureID');
        }
        $defaultUnitOfMeasureID = $this->input->post('defaultUnitOfMeasureID');
        $itemAutoIDhn = $this->input->post('itemAutoIDhn');
        $comparr=array();
        $this->load->library('Approvals');
        foreach($itemAutoIDhn as $key => $mainCateg) {
            $data='';
            $company_id = current_companyID();
            if (!empty(trim($revanue[$key]) && trim($revanue[$key] != 'Select Revenue GL Account'))) {
                $revanueex = explode('|', trim($revanue[$key]));
            }
            $cost = explode('|', trim($costpost[$key]));
            $asste = explode('|', trim($asstepost[$key]));
            $mainCategoryex = explode('|', trim($mainCategory[$key]));
            $stockadjustmentex = explode('|', trim($stockadjustment[$key]));
            $isactive = 1;

            $data['isActive'] = $isactive;
            $data['seconeryItemCode'] = trim($seconeryItemCode[$key]);
            $data['itemName'] = clear_descriprions(trim($itemName[$key]));
            $data['itemDescription'] = clear_descriprions(trim($itemDescription[$key]));
            $data['subcategoryID'] = trim($subcategoryID[$key]);
            if(!empty($subSubCategoryID[$key])){
                $data['subSubCategoryID'] = trim($subSubCategoryID[$key]);
            }
            $data['partNo'] = trim($partno[$key]);
            $data['reorderPoint'] = trim($reorderPoint[$key]);
            $data['maximunQty'] = trim($maximunQty[$key]);
            $data['minimumQty'] = trim($minimumQty[$key]);

            $data['comments'] = trim($comments[$key]);
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $data['companyLocalExchangeRate'] = 1;
            $data['companyLocalSellingPrice'] = trim($companyLocalSellingPrice[$key]);
            $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
            $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
            //$data['isSubitemExist'] = trim($this->input->post('isSubitemExist') ?? '');

            if ($revanueGLAutoID[$key]) {
                $data['mainCategory'] = trim($mainCategoryex[1] ?? '');
                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                    $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                    $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                    $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';

                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['stockAdjustmentGLAutoID'] = trim($stockadjust[$key]);
                    $data['stockAdjustmentSystemGLCode'] = trim($stockadjustmentex[0] ?? '');
                    $data['stockAdjustmentGLCode'] = trim($stockadjustmentex[1] ?? '');
                    $data['stockAdjustmentDescription'] = trim($stockadjustmentex[2] ?? '');
                    $data['stockAdjustmentType'] = trim($stockadjustmentex[3] ?? '');

                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');

                } elseif ($data['mainCategory'] == 'Inventory') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                    $data['assteGLCode'] = trim($asste[1] ?? '');
                    $data['assteDescription'] = trim($asste[2] ?? '');
                    $data['assteType'] = trim($asste[3] ?? '');
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['stockAdjustmentGLAutoID'] = trim($stockadjust[$key]);
                    $data['stockAdjustmentSystemGLCode'] = trim($stockadjustmentex[0] ?? '');
                    $data['stockAdjustmentGLCode'] = trim($stockadjustmentex[1] ?? '');
                    $data['stockAdjustmentDescription'] = trim($stockadjustmentex[2] ?? '');
                    $data['stockAdjustmentType'] = trim($stockadjustmentex[3] ?? '');
                } else {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                    $data['assteGLCode'] = trim($asste[1] ?? '');
                    $data['assteDescription'] = trim($asste[2] ?? '');
                    $data['assteType'] = trim($asste[3] ?? '');
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }
            }
            if(!empty($barcode[$key])){
                $barcode = $barcode[$key];
            }else{
                $barcode='';
            }


            $barcodeexist = $this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' ")->row_array();
            if ($barcodeexist && !empty($barcode)) {
                return array('e', 'Barcode is already exist. ');
            } else {
                $uoms = explode('|', trim($uom[$key]));

                if($secondaryUOM==1){
                    $uomsSecondary = explode('|', trim($uomSecondary[$key]));
                    $data['secondaryUOMID'] = trim($secondaryUnitOfMeasureID[$key]);
                    //$data['secondaryUnitOfMeasure'] = trim($uomsSecondary[0] ?? '');
                }
                $this->load->library('sequence');
                $data['isActive'] = $isactive;
                $data['itemImage'] = 'no-image.png';
                $data['defaultUnitOfMeasureID'] = trim($defaultUnitOfMeasureID[$key]);
                $data['defaultUnitOfMeasure'] = trim($uoms[0] ?? '');
                $data['mainCategoryID'] = trim($mainCategoryIDselect);
                $data['mainCategory'] = trim($mainCategoryex[1] ?? '');
                $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);
                $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                if ($data['mainCategory'] == 'Fixed Assets') {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['faCostGLAutoID'] = trim($COSTGLCODEdes[$key]);
                    $data['faACCDEPGLAutoID'] = trim($ACCDEPGLCODEdes[$key]);
                    $data['faDEPGLAutoID'] = trim($DEPGLCODEdes[$key]);
                    $data['faDISPOGLAutoID'] = trim($DISPOGLCODEdes[$key]);

                    $data['costGLAutoID'] = '';
                    $data['costSystemGLCode'] = '';
                    $data['costGLCode'] = '';
                    $data['costDescription'] = '';
                    $data['costType'] = '';
                } elseif ($data['mainCategory'] == 'Service' or $data['mainCategory'] == 'Non Inventory') {
                    $data['assteGLAutoID'] = '';
                    $data['assteSystemGLCode'] = '';
                    $data['assteGLCode'] = '';
                    $data['assteDescription'] = '';
                    $data['assteType'] = '';
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                } else {
                    $data['assteGLAutoID'] = trim($assteGLAutoID[$key]);
                    $data['assteSystemGLCode'] = trim($asste[0] ?? '');
                    $data['assteGLCode'] = trim($asste[1] ?? '');
                    $data['assteDescription'] = trim($asste[2] ?? '');
                    $data['assteType'] = trim($asste[3] ?? '');
                    $data['revanueGLAutoID'] = trim($revanueGLAutoID[$key]);
                    if (!empty($revanue)) {
                        $data['revanueSystemGLCode'] = trim($revanueex[0] ?? '');
                        $data['revanueGLCode'] = trim($revanueex[1] ?? '');
                        $data['revanueDescription'] = trim($revanueex[2] ?? '');
                        $data['revanueType'] = trim($revanueex[3] ?? '');
                    }
                    $data['costGLAutoID'] = trim($costGLAutoID[$key]);
                    $data['costSystemGLCode'] = trim($cost[0] ?? '');
                    $data['costGLCode'] = trim($cost[1] ?? '');
                    $data['costDescription'] = trim($cost[2] ?? '');
                    $data['costType'] = trim($cost[3] ?? '');
                }
                $data['companyLocalWacAmount'] = 0.00;
                $data['companyReportingWacAmount'] = 0.00;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['itemSystemCode'] = $this->sequence->sequence_generator(trim($mainCategoryex[0] ?? ''));
    //check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if (!empty($codeExist)) {
                    return array('w', 'Item System Code : ' . $codeExist['itemSystemCode'] . ' Already Exist ');
                }

                if(!empty($barcode[$key])){
                    $barcode = $barcode[$key];
                }else{
                    $barcode='';
                }
                if ($barcode != '') {
                    $data['barcode'] = $barcode;
                } else {
                    $data['barcode'] = $data['itemSystemCode'];
                }
            }
            $this->db->insert('srp_erp_itemmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->delete('srp_erp_itemmaster_temp', array('itemAutoID' => $itemAutoIDhn[$key]));

            if(($ApprovalforItemMaster== 0 || $ApprovalforItemMaster == NULL)){
                $this->load->library('Approvals');
                $approvals_status = $this->approvals->auto_approve($last_id, 'srp_erp_itemmaster','itemAutoID', 'INV',$data['itemSystemCode'],$this->common_data['current_date']);
                if($approvals_status == 1){

                } else if($approvals_status ==3){
                    return array('w', 'There are no users exist to perform approval for this document.');
                    $this->db->trans_rollback();
                } else {
                    return array('e', 'Document confirmation failed');
                    $this->db->trans_rollback();
                }
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Upload failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Records Successfully Saved');
        }
    }

    function clear_temp_table()
    {
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $result=$this->db->delete('srp_erp_itemmaster_temp');
        if($result){
            return array('s', 'Records Successfully Deleted');
        }else{
            return array('e', 'Deletion failed');
        }
    }

    function item_pricing_report(){
        $itemAutoID=$this->input->post('itemAutoID');
        $companyID=current_companyID();
        $convertFormat = convert_date_format_sql();
        $data['PO']= $this->db->query("SELECT
	pom.purchaseOrderCode,
	pom.purchaseOrderID as docid,
	DATE_FORMAT(pom.documentDate, '$convertFormat') AS documentDate,
	pom.supplierName,
	SUM(pod.requestedQty) AS qty,
	pod.unitOfMeasure,
	pom.transactionCurrency,
	pom.transactionCurrencyDecimalPlaces as currencydecimal,
	SUM(pod.unitAmount) AS unitcost,
	(SUM(pod.requestedQty)*SUM(pod.unitAmount)) AS totalcost
FROM
	srp_erp_purchaseorderdetails pod
LEFT JOIN srp_erp_purchaseordermaster pom ON pod.purchaseOrderID = pom.purchaseOrderID
WHERE
	pom.companyID = $companyID
AND itemAutoID = $itemAutoID
AND approvedYN = 1
GROUP BY
	pod.purchaseOrderID ")->result_array();

        $data['GRV']= $this->db->query("SELECT
	grvm.grvAutoID as docid,
	grvm.grvPrimaryCode,
	DATE_FORMAT(grvm.grvDate, '$convertFormat') AS documentDate,
	grvm.supplierName,
	SUM(grvd.receivedQty) AS qty,
	grvd.unitOfMeasure,
	grvm.transactionCurrency,
	grvm.transactionCurrencyDecimalPlaces as currencydecimal,
	SUM(grvd.receivedAmount) AS unitcost,
	(
		SUM(grvd.receivedQty) * SUM(grvd.receivedAmount)
	) AS totalcost
FROM
	srp_erp_grvdetails grvd
LEFT JOIN srp_erp_grvmaster grvm ON grvd.grvAutoID = grvm.grvAutoID
WHERE
	grvm.companyID = $companyID
AND itemAutoID = $itemAutoID
AND approvedYN = 1
AND grvm.grvType = 'Standard'
GROUP BY
	grvd.grvAutoID ")->result_array();

        $data['BSI']= $this->db->query("SELECT
	bsim.InvoiceAutoID as docid,
	bsim.bookingInvCode,
	DATE_FORMAT(bsim.bookingDate, '$convertFormat') AS documentDate,
	bsim.supplierName,
	SUM(bsid.requestedQty) AS qty,
	bsid.unitOfMeasure,
	bsim.transactionCurrency,
	bsim.transactionCurrencyDecimalPlaces as currencydecimal,
	SUM(bsid.unittransactionAmount) AS unitcost,
	(
		SUM(bsid.requestedQty) * SUM(bsid.unittransactionAmount)
	) AS totalcost
FROM
	srp_erp_paysupplierinvoicedetail bsid
LEFT JOIN srp_erp_paysupplierinvoicemaster bsim ON bsid.InvoiceAutoID = bsim.InvoiceAutoID
WHERE
	bsim.companyID = $companyID
AND itemAutoID = $itemAutoID
AND approvedYN = 1
AND bsid.type = 'Item'
GROUP BY
	bsid.invoiceAutoID")->result_array();

        $data['PV']= $this->db->query("SELECT
	pvm.payVoucherAutoId as docid,
	pvm.PVcode,
	DATE_FORMAT(pvm.PVdate, '$convertFormat') AS documentDate,
	pvm.partyName,
	SUM(pvd.requestedQty) AS qty,
	pvd.unitOfMeasure,
	pvm.transactionCurrency,
	pvm.transactionCurrencyDecimalPlaces as currencydecimal,
	SUM(pvd.unittransactionAmount) AS unitcost,
	(
		SUM(pvd.requestedQty) * SUM(pvd.unittransactionAmount)
	) AS totalcost
FROM
	srp_erp_paymentvoucherdetail pvd
LEFT JOIN srp_erp_paymentvouchermaster pvm ON pvd.payVoucherAutoId = pvm.payVoucherAutoId
WHERE
	pvm.companyID = $companyID
AND itemAutoID = $itemAutoID
AND approvedYN = 1
GROUP BY
	pvd.payVoucherAutoId")->result_array();

        $data['item']= $this->db->query("SELECT
	itemSystemCode,itemName
FROM
	srp_erp_itemmaster
WHERE
	itemAutoID = $itemAutoID
AND companyID =$companyID")->row_array();



        return $data;
    }
    function item_type_pull()
    {
        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');
        $typevalue = $this->input->post('typevalue');
        $Type = $this->input->post('Type');
        $Maincate = $this->input->post('Maincate');
        $ItemMaster = $this->db->query("SELECT * FROM `srp_erp_itemmaster` where companyID  = $companyID AND itemAutoID = $itemAutoID")->row_array();

        $data['item'] = $this->db->query("SELECT
	checkitemtransaction.*,
	CASE
		WHEN checkitemtransaction.doctype = \"DO\" THEN
		srp_erp_deliveryorder.referenceNo 
		WHEN checkitemtransaction.doctype = \"QUT\" THEN
		srp_erp_contractmaster.referenceNo 
		WHEN checkitemtransaction.doctype = \"buybackdispatch\" THEN
		srp_erp_buyback_dispatchnote.referenceNo 
		WHEN checkitemtransaction.doctype = \"buybackGRN\" THEN
		srp_erp_buyback_grn.referenceNo 
		WHEN checkitemtransaction.doctype = \"CRMQUT\" THEN
		srp_erp_crm_quotation.referenceNo 
		WHEN checkitemtransaction.doctype = \"CINV\" THEN
		srp_erp_customerinvoicemaster.referenceNo 
		WHEN checkitemtransaction.doctype = \"RV\" THEN
		srp_erp_customerreceiptmaster.referanceNo 
		WHEN checkitemtransaction.doctype = \"GRV\" THEN
		srp_erp_grvmaster.grvDocRefNo 
		WHEN checkitemtransaction.doctype = \"MI\" THEN
		srp_erp_itemissuemaster.issueRefNo 
		WHEN checkitemtransaction.doctype = \"MR\" THEN
		srp_erp_materialrequest.referenceNo 
		WHEN checkitemtransaction.doctype = \"MRN\" THEN
		srp_erp_materialreceiptmaster.RefNo 
		WHEN checkitemtransaction.doctype = \"MFQBOM\" THEN
		'' 
		WHEN checkitemtransaction.doctype = \"MFQINV\" THEN
		srp_erp_mfq_customerinvoicemaster.referenceNo 
		WHEN checkitemtransaction.doctype = \"PV\" THEN
		srp_erp_paymentvouchermaster.referenceNo 
		WHEN checkitemtransaction.doctype = \"BSI\" THEN
		srp_erp_paysupplierinvoicemaster.RefNo 
		WHEN checkitemtransaction.doctype = \"POSINV\" THEN
		srp_erp_pos_invoice.cardRefNo 
		WHEN checkitemtransaction.doctype = \"PO\" THEN
		srp_erp_purchaseordermaster.referenceNumber 
		WHEN checkitemtransaction.doctype = \"PRD\" THEN
		srp_erp_purchaserequestmaster.referenceNumber 
		WHEN checkitemtransaction.doctype = \"SR\" THEN
		srp_erp_salesreturnmaster.referenceNo 
		WHEN checkitemtransaction.doctype = \"SA\" THEN
		stockA.referenceNo 
		WHEN checkitemtransaction.doctype = \"SC\" THEN
		stockcounting.referenceNo 
		WHEN checkitemtransaction.doctype = \"ST\" THEN
		stocktransfer.referenceNo ELSE \"\" 
	END referanceNo,
CASE
	
	WHEN checkitemtransaction.doctype = \"DO\" THEN
	srp_erp_deliveryorder.DOCode 
	WHEN checkitemtransaction.doctype = \"QUT\" THEN
	srp_erp_contractmaster.contractCode 
	WHEN checkitemtransaction.doctype = \"buybackdispatch\" THEN
	srp_erp_buyback_dispatchnote.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"buybackGRN\" THEN
	srp_erp_buyback_grn.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"CRMQUT\" THEN
	srp_erp_crm_quotation.quotationCode 
	WHEN checkitemtransaction.doctype = \"CINV\" THEN
	srp_erp_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"RV\" THEN
	srp_erp_customerreceiptmaster.RVcode 
	WHEN checkitemtransaction.doctype = \"GRV\" THEN
	srp_erp_grvmaster.grvPrimaryCode
    WHEN checkitemtransaction.doctype = \"MI\" THEN
	srp_erp_itemissuemaster.itemIssueCode 
    WHEN checkitemtransaction.doctype = \"MR\" THEN
	srp_erp_materialrequest.MRCode 
    WHEN checkitemtransaction.doctype = \"MRN\" THEN
	srp_erp_materialreceiptmaster.mrnCode 
    WHEN checkitemtransaction.doctype = \"MFQBOM\" THEN
	srp_erp_mfq_billofmaterial.documentCode 
    WHEN checkitemtransaction.doctype = \"MFQINV\" THEN
	srp_erp_mfq_customerinvoicemaster.invoiceCode 
	WHEN checkitemtransaction.doctype = \"PV\" THEN
	srp_erp_paymentvouchermaster.PVcode 
    WHEN checkitemtransaction.doctype = \"BSI\" THEN
	srp_erp_paysupplierinvoicemaster.bookingInvCode 
	WHEN checkitemtransaction.doctype = \"POSINV\" THEN
	srp_erp_pos_invoice.documentSystemCode 
	WHEN checkitemtransaction.doctype = \"PO\" THEN
	srp_erp_purchaseordermaster.purchaseOrderCode 
	WHEN checkitemtransaction.doctype = \"PRD\" THEN
	srp_erp_purchaserequestmaster.purchaseRequestCode 
	WHEN checkitemtransaction.doctype = \"SR\" THEN
	srp_erp_salesreturnmaster.salesReturnCode 
    WHEN checkitemtransaction.doctype = \"SA\" THEN
    stockA.stockAdjustmentCode 
    WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.stockCountingCode
	   WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stockTransferCode	
	ELSE \"\" 
	END documentcode,
	CASE
	
	WHEN checkitemtransaction.doctype = \"SA\" THEN
	stockA.sacompanyID 
	WHEN checkitemtransaction.doctype = \"SC\" THEN
	stockcounting.sccompanyID 
	WHEN checkitemtransaction.doctype = \"ST\" THEN
	stocktransfer.stcompanyID
	
	
	ELSE
	checkitemtransaction.companyID
	END companyidNEw,
	checkitemtransaction.doctype AS documentType
	
FROM
	`checkitemtransaction`
	LEFT JOIN srp_erp_deliveryorder ON srp_erp_deliveryorder.DOAutoID = checkitemtransaction.doccode 
	AND checkitemtransaction.doctype = 'DO'
	LEFT JOIN srp_erp_contractmaster ON srp_erp_contractmaster.contractAutoID = checkitemtransaction.doccode 
	AND checkitemtransaction.doctype = 'QUT' 
	LEFT JOIN srp_erp_buyback_dispatchnote On srp_erp_buyback_dispatchnote.dispatchAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'buybackdispatch'
	LEFT JOIN srp_erp_buyback_grn On srp_erp_buyback_grn.grnAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'buybackGRN' 
	LEFT JOIN srp_erp_crm_quotation on srp_erp_crm_quotation.quotationAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'CRMQUT'  
	LEFT JOIN srp_erp_customerinvoicemaster on srp_erp_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'CINV' 
	LEFT JOIN srp_erp_customerreceiptmaster on srp_erp_customerreceiptmaster.receiptVoucherAutoId = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'RV' 
	LEFT JOIN srp_erp_grvmaster on srp_erp_grvmaster.grvAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'GRV' 
	LEFT JOIN srp_erp_itemissuemaster on srp_erp_itemissuemaster.itemIssueAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'MI' 
	LEFT JOIN srp_erp_materialrequest on srp_erp_materialrequest.mrAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'MR' 
	LEFT JOIN srp_erp_materialreceiptmaster on srp_erp_materialreceiptmaster.mrnAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'MRN'  
	LEFT JOIN srp_erp_mfq_billofmaterial on srp_erp_mfq_billofmaterial.bomMasterID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'MFQBOM'  
	LEFT JOIN srp_erp_mfq_customerinvoicemaster on srp_erp_mfq_customerinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'MFQINV'  
	LEFT JOIN srp_erp_paymentvouchermaster on srp_erp_paymentvouchermaster.payVoucherAutoId = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'PV' 
    LEFT JOIN srp_erp_paysupplierinvoicemaster on srp_erp_paysupplierinvoicemaster.invoiceAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'BSI' 
    LEFT JOIN srp_erp_pos_invoice on srp_erp_pos_invoice.invoiceID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'POSINV' 
    LEFT JOIN srp_erp_purchaseordermaster on srp_erp_purchaseordermaster.purchaseOrderID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'PO'  
    LEFT JOIN srp_erp_purchaserequestmaster on srp_erp_purchaserequestmaster.purchaseRequestID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'PRD'  
    LEFT JOIN srp_erp_salesreturnmaster on srp_erp_salesreturnmaster.salesReturnAutoID = checkitemtransaction.doccode AND checkitemtransaction.doctype = 'SR'  
   	LEFT JOIN (SELECT srp_erp_stockadjustmentmaster.companyID as sacompanyID ,stockAdjustmentAutoID,stockAdjustmentCode, referenceNo from srp_erp_stockadjustmentmaster LEFT JOIN checkitemtransaction on srp_erp_stockadjustmentmaster.stockAdjustmentAutoID = checkitemtransaction.doccode where checkitemtransaction.doctype = 'SA'  
	
 	 ) stockA on stockA.stockAdjustmentAutoID = checkitemtransaction.doccode    
	 
	  	LEFT JOIN (SELECT srp_erp_stockcountingmaster.companyID as sccompanyID ,stockCountingAutoID,stockCountingCode, referenceNo from srp_erp_stockcountingmaster LEFT JOIN checkitemtransaction on srp_erp_stockcountingmaster.stockCountingAutoID = checkitemtransaction.doccode where checkitemtransaction.doctype = 'SC'  
	
	 ) stockcounting on stockcounting.stockCountingAutoID = checkitemtransaction.doccode  
	 
	 LEFT JOIN (
	SELECT
		srp_erp_stocktransfermaster.companyID AS stcompanyID,
		stockTransferAutoID,
		stockTransferCode,
		referenceNo 
	FROM
		srp_erp_stocktransfermaster
		LEFT JOIN checkitemtransaction ON srp_erp_stocktransfermaster.stockTransferAutoID = checkitemtransaction.doccode 
	WHERE
		doctype = 'ST' 
	) stocktransfer ON stocktransfer.stockTransferAutoID = checkitemtransaction.doccode 
	
WHERE
 checkitemtransaction.itemAutoID = '{$itemAutoID}'
GROUP BY 
doctype,doccode
HAVING 
 companyidNEw = '{$companyID}'  ")->result_array();


        switch ($Type) {

            case "1":
                if($ItemMaster['mainCategoryID']!= $typevalue)
                {
                    $data['typechange'] = 1;
                    $data['cattype'] = 'Main';
                    $data['typevalue'] = $ItemMaster['mainCategoryID'];
                    $data['typevaluesub'] = $ItemMaster['subcategoryID'];
                    $data['typevaluesubsub'] = $ItemMaster['subSubCategoryID'];
                } else
                {
                    $data['typechange'] = 0;
                }
                break;
            case "2":
                if($ItemMaster['subcategoryID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'Sub';
                    $data['typevalue'] = $ItemMaster['subcategoryID'];
                    $data['typevaluesubsub'] = $ItemMaster['subSubCategoryID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "3":
                if($ItemMaster['defaultUnitOfMeasureID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'UomDe';
                    $data['typevalue'] = $ItemMaster['defaultUnitOfMeasureID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "4":
                if($ItemMaster['secondaryUOMID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'SecUom';
                    $data['typevalue'] = $ItemMaster['secondaryUOMID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "5":
                if($ItemMaster['revanueGLAutoID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'revenueGL';
                    $data['typevalue'] = $ItemMaster['revanueGLAutoID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "6":
                if($ItemMaster['costGLAutoID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'costGL';
                    $data['typevalue'] = $ItemMaster['costGLAutoID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "7":
                if($ItemMaster['assteGLAutoID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'assetGL';
                    $data['typevalue'] = $ItemMaster['assteGLAutoID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "8":
                if($ItemMaster['stockAdjustmentGLAutoID']!=$typevalue)
                {

                    $data['typechange'] = 1;
                    $data['cattype'] = 'stockAdjustment';
                    $data['typevalue'] = $ItemMaster['stockAdjustmentGLAutoID'];
                } else
                {   $data['typechange'] = 0;

                }
                break;
            case "9":

                    if($ItemMaster['faCostGLAutoID']!=$typevalue)
                    {

                        $data['typechange'] = 1;
                        $data['cattype'] = 'faCost';
                        $data['typevalue'] = $ItemMaster['faCostGLAutoID'];
                    } else
                    {   $data['typechange'] = 0;

                    }
                    break;




            default:
                exit();
        }
        return $data;
    }


    function load_sales_details_report_in_sales_and_marketing($dateFrom, $dateTo, $warehouse = null, $items = null, $customer = null)
    {
        $where = " AND (srp_erp_itemledger.documentDate  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "')";

        $currency = $this->input->post('currency');
        $column_filter = $this->input->post('columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";
        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'srp_erp_itemmaster.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }
        if ($warehouse != null) {
             $where .= " AND srp_erp_itemledger.wareHouseAutoID IN(" . $warehouse . ") ";
        }

        if ($items != null) {
            $where .= " AND srp_erp_itemledger.itemAutoID IN(" . $items . ") ";
        }

        if ($customer != null) {
            $where .= " AND cutomerTable.partyID IN(" . $customer . ") ";
        }

        $companyID=current_companyID();
        $currencyExchange='srp_erp_itemledger.companyLocalExchangeRate';
        $currencyAmount = 'srp_erp_itemledger.companyLocalAmount';
        if($currency=='Reporting'){
            $currencyExchange='srp_erp_itemledger.companyReportingExchangeRate';
            $currencyAmount = 'srp_erp_itemledger.companyReportingAmount';
        }

        $q = "SELECT
                    srp_erp_itemledger.documentSystemCode,
                    srp_erp_itemledger.documentDate,
                    srp_erp_itemledger.documentCode,
                    srp_erp_itemledger.documentAutoID,
                    $feilds1
                    srp_erp_itemmaster.itemAutoID,
                    srp_erp_itemmaster.itemDescription,
                    srp_erp_itemmaster.defaultUnitOfMeasure,
                    srp_erp_itemmaster.itemSystemCode,
                    srp_erp_itemmaster.seconeryItemCode,
                    srp_erp_itemcategory.description as subCategory,
                    cutomerTable.customerSystemCode,
                    cutomerTable.customerName,
                    TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionQTY / convertionRate)), 4 ))))))*-1 AS qty,
                    SUM((((transactionQTY * salesPrice)/ 100) * (100 - discountPer)) / $currencyExchange)*-1 as totSalesVal,
                    SUM(transactionAmount/ $currencyExchange)*-1 AS totalCost,
                    (SUM($currencyAmount)/SUM(transactionQTY/convertionRate)) as averageCost
                FROM srp_erp_itemledger
                LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
                LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID
                LEFT JOIN 
                    (
                        SELECT * FROM
                            (
                                (SELECT invoiceAutoID as documentMasterAutoID, documentID as documentID, customerID as partyID, IFNULL(discountPer, 0) as discountPer 
                                    FROM srp_erp_customerinvoicemaster 
                                    LEFT JOIN (SELECT SUM(discountPercentage) as discountPer, invoiceAutoID as masterID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID)discount ON discount.masterID = srp_erp_customerinvoicemaster.invoiceAutoID
                                    WHERE companyID=$companyID and approvedYN=1
                                ) UNION ALL
                                (SELECT DOAutoID as documentMasterAutoID, documentID as documentID, customerID as partyID, 0 as discountPer FROM srp_erp_deliveryorder WHERE companyID=$companyID and approvedYN=1) UNION ALL
                                (SELECT salesReturnID as documentMasterAutoID, invoiceID as documentID, customerID as partyID, 0 as discountPer FROM srp_erp_pos_salesreturn WHERE companyID=$companyID) UNION ALL
                                (SELECT salesReturnAutoID as documentMasterAutoID, documentID as documentID, customerID as partyID, 0 as discountPer FROM srp_erp_salesreturnmaster WHERE companyID=$companyID and approvedYN=1) UNION ALL
                                (SELECT invoiceID as documentMasterAutoID, invoiceID as documentID, customerID as partyID, 0 as discountPer FROM srp_erp_pos_invoice WHERE companyID=$companyID)
                            ) invTables
                            JOIN srp_erp_customermaster on srp_erp_customermaster.customerAutoID=invTables.partyID
                ) as cutomerTable ON srp_erp_itemledger.documentAutoID = cutomerTable.documentMasterAutoID AND cutomerTable.documentID = srp_erp_itemledger.documentCode
                WHERE srp_erp_itemledger.companyID = $companyID AND srp_erp_itemledger.documentID IN('CINV','RV','SLR','DO') $where
                GROUP BY srp_erp_itemledger.itemAutoID,srp_erp_itemledger.documentCode,srp_erp_itemledger.documentAutoID";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_item_wise_prfitability_report($dateFrom, $dateTo, $warehouse = null, $items = null)
    {


        $currency = $this->input->post('currency');
        $column_filter = $this->input->post('columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";
        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'srp_erp_itemmaster.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }
        if ($warehouse != null) {
            $where_tmp[] = " AND srp_erp_itemledger.wareHouseAutoID IN(" . $warehouse . ") ";
        }

        if ($items != null) {
            if ($warehouse != null) {
                $where_tmp[] = " srp_erp_itemledger.itemAutoID IN(" . $items . ") ";
            }else{
                $where_tmp[] = "AND srp_erp_itemledger.itemAutoID IN(" . $items . ") ";
            }
        }


        $where_tmp[] = " ( srp_erp_itemledger.documentDate  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' )";

        $where = join('AND', $where_tmp);
        $companyID=current_companyID();
        $currencyExchange='srp_erp_itemledger.companyLocalExchangeRate';
        if($currency=='Reporting'){
        $currencyExchange='srp_erp_itemledger.companyReportingExchangeRate';
        }

        $q = "SELECT
    $feilds1
    srp_erp_itemmaster.itemAutoID,
	srp_erp_itemmaster.itemDescription,
	srp_erp_itemmaster.defaultUnitOfMeasure,
	srp_erp_itemmaster.itemSystemCode,
	srp_erp_itemmaster.seconeryItemCode,
	TRIM(TRAILING '.' FROM (TRIM(TRAILING 0 FROM ((ROUND((SUM( transactionQTY / convertionRate)), 4 ))))))*-1 AS qty,	
SUM(
		(transactionQTY * salesPrice) / $currencyExchange
	)*-1 as totSalesVal,
	SUM(transactionAmount/ $currencyExchange)*-1 AS totalCost

FROM
	srp_erp_itemledger
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
WHERE
	srp_erp_itemledger.companyID = $companyID
AND srp_erp_itemledger.documentID IN('CINV','RV','POS','SLR','RET','DO')
" . $where . "
GROUP BY
	srp_erp_itemledger.itemAutoID";
        /*echo $q;*/



        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function get_item_wise_prfitability_report_DD($dateFrom, $dateTo, $warehouse = null)
    {


        $currency = $this->input->post('currency');
        $itemAutoID = $this->input->post('itemAutoID');

        if ($warehouse != null) {
            $where_tmp[] = " AND srp_erp_itemledger.wareHouseAutoID IN(" . $warehouse . ") ";
        }


        $where_tmp[] = " ( srp_erp_itemledger.documentDate  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "' )";

        $where = join('AND', $where_tmp);
        $companyID=current_companyID();
        $currencyExchange='srp_erp_itemledger.companyLocalExchangeRate';
        $currencyDecimal='srp_erp_itemledger.companyLocalCurrencyDecimalPlaces';
        if($currency=='Reporting'){
            $currencyExchange='srp_erp_itemledger.companyReportingExchangeRate';
            $currencyDecimal='srp_erp_itemledger.companyReportingCurrencyDecimalPlaces';
        }

        $q = "SELECT
    $currencyDecimal AS decimalplace,
    srp_erp_itemledger.documentID,
	srp_erp_itemledger.documentAutoID,
	srp_erp_itemledger.documentSystemCode,
    transactionQTY / convertionRate AS qty,
    (transactionQTY * salesPrice) / $currencyExchange AS totSalesVal,
	transactionAmount/ $currencyExchange AS totalCost

FROM
	srp_erp_itemledger
LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_itemledger.itemAutoID
WHERE
	srp_erp_itemledger.companyID = $companyID
AND srp_erp_itemledger.documentID IN('CINV','RV','POS','SLR','RET','DO')
" . $where . "
AND srp_erp_itemledger.itemAutoID=$itemAutoID";
        /*echo $q;*/



        $result = $this->db->query($q)->result_array();
        return $result;
    }

    function item_barcode_validate()
    {
        $companyID = current_companyID();
        $barCode = $this->input->post('barCode');
        $itemAutoID = $this->input->post('itemAutoID');

        $where = '';
        if(!empty($itemAutoID)) {
            $where = " AND itemAutoID != " . $itemAutoID;
        }

        $result = $this->db->query("SELECT itemSystemCode AS documentcode, itemDescription as item FROM srp_erp_itemmaster 
                                            WHERE	companyID = {$companyID} AND barcode LIKE '" . $barCode . "' {$where}")->result_array();

        return $result;
    }

    function get_sales_detail_report_excel($isBarcode = false,$isPartNo = false)
    {
        $data = array();
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpwarehus = $this->input->post('wareHouseAutoID');
        $tmpitems = $this->input->post('items');
        //$barcode = false;
        //$partNo = false;
        /* if (isset($columnSelectionDrop)) {
             if (in_array("barcode", $columnSelectionDrop)) {
                 $barcode = true;
             }
             if (in_array("partNo", $columnSelectionDrop)) {
                 $partNo = true;
             }
         }*/

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }

        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpwarehus) && !empty($tmpwarehus)) {
            $tmpWarehouse = join(",", $tmpwarehus);
            $warehouse = $tmpWarehouse;
        } else {
            $warehouse = null;
        }

        if (isset($tmpitems) && !empty($tmpitems)) {
            $tmpItems = join(",", $tmpitems);
            $item = $tmpItems;
        } else {
            $item = null;
        }

        $d = $this->common_data['company_data']['company_default_decimal'];
        if($this->input->post('currency') == 'Reporting'){
            $d = $this->common_data['company_data']['company_reporting_decimal'];
        }

        $itemizedSalesReport = $this->load_sales_details_report_in_sales_and_marketing($filterDate, $date2, $warehouse,$item);
        $i = 0;
        $totalQty = 0;
        $totalAmount = 0;
        $totalWAC = 0;
        $totalProfit = 0;
        $totalAverageCost = 0;
        if (!empty($itemizedSalesReport)) {
            $i = 1;
            foreach ($itemizedSalesReport as $item) {
                $totalQty += ($item['qty']);
                $totalAmount += ($item['totSalesVal']);
                $totalWAC += ($item['totalCost']);
                $totalProfit += ($item['totSalesVal']) - ($item['totalCost']);
                $totalAverageCost += ($item['averageCost']);

                $margin = 0;
                if ($item['totSalesVal'] != 0) {
                    $margin = number_format(((($item['totSalesVal'])-($item['totalCost'])) / ($item['totSalesVal'])) * 100, 2, '.', '');
                }

                $item['itemDescription'] = str_replace("<br>","        ",$item['itemDescription']);
                $item['itemDescription'] = str_replace("&nbsp;","  ",$item['itemDescription']);

                $details = array(
                    'no' => $i,
                    'customerSystemCode' => $item['customerSystemCode'],
                    'customerName' => $item['customerName'],
                    'documentSystemCode' => $item['documentSystemCode'],
                    'documentDate' => $item['documentDate'],
                    'itemCode' => $item['itemSystemCode'],
                    'SecCode' => $item['seconeryItemCode']/*,
                    'itemDescription' => $item['itemDescription'],
                    'UOM' =>  $item['defaultUnitOfMeasure'],
                    'qty' => ($item['qty']),
                    'salesVal' => number_format(($item['totSalesVal']), $d, '.', ''),
                    'TotCost' => number_format(($item['totalCost']), $d, '.', ''),
                    'Profit' => number_format(($item['totSalesVal'])-($item['totalCost']), $d, '.', ''),
                    'margin' => $margin . '%'*/
                );
                if($isBarcode){
                    $details['barcode'] = $item['barcode'];
                }
                if($isPartNo){
                    $details['partNo'] = $item['partNo'];
                }
                $details['itemDescription'] = $item['itemDescription'];
                $details['UOM'] = $item['defaultUnitOfMeasure'];
                $details['qty'] = ($item['qty']);
                $details['salesVal'] = number_format(($item['totSalesVal']), $d, '.', '');
                $details['TotCost'] = number_format(($item['totalCost']), $d, '.', '');
                $details['Profit'] = number_format(($item['totSalesVal'])-($item['totalCost']), $d, '.', '');
                $details['margin'] = $margin . '%';
                $details['averageCost'] = $item['averageCost'];
                $details['subCategory'] = $item['subCategory'];
                $data['details'][] = $details;
                $i++;
            }

            $totalArr = array(
                'no' => ' ',
                'customerSystemCode' => '',
                'customerName' => '',
                'documentSystemCode' => '',
                'documentDate' => '',
                'itemCode' => '',
                'SecCode' => '',
                'itemDescription' => '',
                'UOM' =>  '',
                'qty' => 'Total'/*,
                'salesVal' => number_format($totalAmount, $d, '.', ''),
                'TotCost' => number_format($totalWAC, $d, '.', ''),
                'Profit' => number_format($totalProfit, $d, '.', ''),
                'margin' => number_format(($totalProfit / $totalAmount) * 100, 2, '.', '') . '%'
           */ );
            if($isBarcode){
                $totalArr['barcode'] = ' ';
            }
            if($isPartNo){
                $totalArr['partNo'] = ' ';
            }
            $totalArr['itemDescription'] = ' ';
            $totalArr['UOM'] = ' ';
            $totalArr['qty'] = 'Total';
            $totalArr['salesVal'] = number_format($totalAmount, $d, '.', '');
            $totalArr['TotCost'] = number_format($totalWAC, $d, '.', '');
            $totalArr['Profit'] = number_format($totalProfit, $d, '.', '');
            $totalArr['margin'] = number_format(($totalProfit / $totalAmount) * 100, 2, '.', '') . '%';
            $totalArr['averageCost'] = number_format($totalAverageCost, $d, '.', '');
            $data['details'][] = $totalArr;
        }

        $data['rowCount'] = $i;
        return $data;
    }

    function get_item_wise_prfitability_report_excel($isBarcode = false,$isPartNo = false)
    {
        $data = array();
        $tmpFilterDate = trim(str_replace('/', '-', $this->input->post('filterFrom')));
        $tmpFilterDateTo = trim(str_replace('/', '-', $this->input->post('filterTo')));
        $tmpwarehus = $this->input->post('wareHouseAutoID');
        $tmpitems = $this->input->post('items');
        //$barcode = false;
        //$partNo = false;
       /* if (isset($columnSelectionDrop)) {
            if (in_array("barcode", $columnSelectionDrop)) {
                $barcode = true;
            }
            if (in_array("partNo", $columnSelectionDrop)) {
                $partNo = true;
            }
        }*/

        if (isset($tmpFilterDate) && !empty($tmpFilterDate)) {
            $filterDate = date('Y-m-d H:i:s', strtotime($tmpFilterDate));
        } else {
            $filterDate = date('Y-m-d 00:00:00');
        }

        if (!empty($tmpFilterDateTo)) {
            $date2 = date('Y-m-d H:i:s', strtotime($tmpFilterDateTo));
        } else {
            $date2 = date('Y-m-d 23:59:59');
        }

        if (isset($tmpwarehus) && !empty($tmpwarehus)) {
            $tmpWarehouse = join(",", $tmpwarehus);
            $warehouse = $tmpWarehouse;
        } else {
            $warehouse = null;
        }

        if (isset($tmpitems) && !empty($tmpitems)) {
            $tmpItems = join(",", $tmpitems);
            $item = $tmpItems;
        } else {
            $item = null;
        }

        $d = $this->common_data['company_data']['company_default_decimal'];
        if($this->input->post('currency') == 'Reporting'){
            $d = $this->common_data['company_data']['company_reporting_decimal'];
        }

        $itemizedSalesReport = $this->get_item_wise_prfitability_report($filterDate, $date2, $warehouse,$item);

        $totalQty = 0;
        $totalAmount = 0;
        $totalWAC = 0;
        $totalProfit = 0;
        if (!empty($itemizedSalesReport)) {
            $i = 1;
            foreach ($itemizedSalesReport as $item) {
                $totalQty += ($item['qty']);
                $totalAmount += ($item['totSalesVal']);
                $totalWAC += ($item['totalCost']);
                $totalProfit += ($item['totSalesVal']) - ($item['totalCost']);
                $margin = 0;
                if ($item['totSalesVal'] != 0) {
                    $margin = number_format(((($item['totSalesVal'])-($item['totalCost'])) / ($item['totSalesVal'])) * 100, 2, '.', '');
                }

                $item['itemDescription'] = str_replace("<br>","        ",$item['itemDescription']);
                $item['itemDescription'] = str_replace("&nbsp;","  ",$item['itemDescription']);

                $details = array(
                    'no' => $i,
                    'itemCode' => $item['itemSystemCode'],
                    'SecCode' => $item['seconeryItemCode']/*,
                    'itemDescription' => $item['itemDescription'],
                    'UOM' =>  $item['defaultUnitOfMeasure'],
                    'qty' => ($item['qty']),
                    'salesVal' => number_format(($item['totSalesVal']), $d, '.', ''),
                    'TotCost' => number_format(($item['totalCost']), $d, '.', ''),
                    'Profit' => number_format(($item['totSalesVal'])-($item['totalCost']), $d, '.', ''),
                    'margin' => $margin . '%'*/
                );
                if($isBarcode){
                    $details['barcode'] = $item['barcode'];
                }
                if($isPartNo){
                    $details['partNo'] = $item['partNo'];
                }
                $details['itemDescription'] = $item['itemDescription'];
                $details['UOM'] = $item['defaultUnitOfMeasure'];
                $details['qty'] = ($item['qty']);
                $details['salesVal'] = number_format(($item['totSalesVal']), $d, '.', '');
                $details['TotCost'] = number_format(($item['totalCost']), $d, '.', '');
                $details['Profit'] = number_format(($item['totSalesVal'])-($item['totalCost']), $d, '.', '');
                $details['margin'] = $margin . '%';

                $data['details'][] = $details;
                $i++;
            }

            $totalArr = array(
                'no' => ' ',
                'itemCode' => 'Total',
                'SecCode' => '   '/*,
                'itemDescription' => '   ',
                'UOM' =>  '   ',
                'qty' => '   ',
                'salesVal' => number_format($totalAmount, $d, '.', ''),
                'TotCost' => number_format($totalWAC, $d, '.', ''),
                'Profit' => number_format($totalProfit, $d, '.', ''),
                'margin' => number_format(($totalProfit / $totalAmount) * 100, 2, '.', '') . '%'
           */ );
            if($isBarcode){
                $totalArr['barcode'] = ' ';
            }
            if($isPartNo){
                $totalArr['partNo'] = ' ';
            }
            $totalArr['itemDescription'] = ' ';
            $totalArr['UOM'] = ' ';
            $totalArr['qty'] = ' ';
            $totalArr['salesVal'] = number_format($totalAmount, $d, '.', '');
            $totalArr['TotCost'] = number_format($totalWAC, $d, '.', '');
            $totalArr['Profit'] = number_format($totalProfit, $d, '.', '');
            $totalArr['margin'] = number_format(($totalProfit / $totalAmount) * 100, 2, '.', '') . '%';
            
            $data['details'][] = $totalArr;
        }

        $data['rowCount'] = $i;
        return $data;
    }

    function export_excel_item_master(){
        $mainCategoryID = $this->input->post('mainCategoryID');
        $subcategoryID = $this->input->post('subcategoryID');
        $subsubcategoryID = $this->input->post('subsubcategoryID');
        $companyID = current_companyID();

        $wheremain='';
        $wheresub='';
        $wheresubsub='';

        if(!empty($mainCategoryID)){
            $wheremain='AND mainCategoryID = '.$mainCategoryID;
        }

        if(!empty($subcategoryID)){
            $wheresub='AND subcategoryID = '.$subcategoryID;
        }

        if(!empty($subsubcategoryID)){
            $wheresubsub='AND subSubCategoryID = '.$subsubcategoryID;
        }

        $result = $this->db->query("SELECT
	itemAutoID,
	srp_erp_itemmaster.deletedYN AS deletedYN,
	itemSystemCode,
	itemName,
	seconeryItemCode,
	itemDescription,
	mainCategory,
	defaultUnitOfMeasure,	
	currentStock,
	barcode,
	partNo,
	companyLocalSellingPrice,
	companyLocalCurrency,
	companyLocalCurrencyDecimalPlaces,
	costDescription,
	assteDescription,
	isActive,
	companyLocalWacAmount,
	subcat.description AS SubCategoryDescription,
	subsubcat.description AS SubSubCategoryDescription,	
	 companyLocalWacAmount AS TotalWacAmount,
	 srp_erp_itemmaster.maximunQty,
	 srp_erp_itemmaster.minimumQty,
	 srp_erp_itemmaster.reorderPoint,
	 srp_erp_itemmaster.companyLocalSellingPrice
FROM
	`srp_erp_itemmaster`
	JOIN `srp_erp_itemcategory` `subcat` ON `srp_erp_itemmaster`.`subcategoryID` = `subcat`.`itemCategoryID`
	LEFT JOIN `srp_erp_itemcategory` `subsubcat` ON `srp_erp_itemmaster`.`subSubCategoryID` = `subsubcat`.`itemCategoryID` 
WHERE
	`deletedYN` = '0' 
	AND `srp_erp_itemmaster`.`companyID` = $companyID 
	$wheremain $wheresub $wheresubsub
ORDER BY
	`itemAutoID` DESC 
	")->result_array();

//var_dump($this->db->last_query());exit;
        $data = array();
        $a = 1;
        foreach ($result as $row)
        {
            $hideWacAmount = getPolicyValues('HWC','All');
            $row['itemDescription'] = str_replace("<br>","        ",$row['itemDescription']);
            $row['itemDescription'] = str_replace("&nbsp;","  ",$row['itemDescription']);
            if($hideWacAmount==1){
                $data[] = array(
                    'Num' => $a,
                    'mainCategory' => $row['mainCategory'],
                    'SubCategoryDescription' => $row['SubCategoryDescription'],
                    'SubSubCategoryDescription' => $row['SubSubCategoryDescription'],
                    'itemSystemCode' => $row['itemSystemCode'],
                    'seconeryItemCode' => $row['seconeryItemCode'],
                    'itemDescription' => $row['itemDescription'],
                    'barcode' => $row['barcode'],
                    'partNo' => $row['partNo'],
                    'defaultUnitOfMeasure' => $row['defaultUnitOfMeasure'],
                    'CurrentStock' => $row['currentStock'],
                    'maximunQty' => $row['maximunQty'],
                    'minimumQty' => $row['minimumQty'],
                    'reorderPoint' => $row['reorderPoint'],
                    'companyLocalCurrency' => $row['companyLocalCurrency'],
                    'companyLocalSellingPrice' => $row['companyLocalSellingPrice'],
                );
            }else{
                $data[] = array(
                    'Num' => $a,
                    'mainCategory' => $row['mainCategory'],
                    'SubCategoryDescription' => $row['SubCategoryDescription'],
                    'SubSubCategoryDescription' => $row['SubSubCategoryDescription'],
                    'itemSystemCode' => $row['itemSystemCode'],
                    'seconeryItemCode' => $row['seconeryItemCode'],
                    'itemDescription' => $row['itemDescription'],
                    'barcode' => $row['barcode'],
                    'partNo' => $row['partNo'],
                    'defaultUnitOfMeasure' => $row['defaultUnitOfMeasure'],
                    'CurrentStock' => $row['currentStock'],
                    'maximunQty' => $row['maximunQty'],
                    'minimumQty' => $row['minimumQty'],
                    'reorderPoint' => $row['reorderPoint'],
                    'companyLocalCurrency' => $row['companyLocalCurrency'],
                    'TotalWacAmount' => $row['TotalWacAmount'],
                    'companyLocalSellingPrice' => $row['companyLocalSellingPrice'],
                );
            }

            $a++;
        }

        return ['items' => $data];
    }

    function export_excel_item_master_report(){
        $mainCategoryID = $this->input->post('mainCategoryID');
        $subcategoryID = $this->input->post('subcategoryID');
        $subsubcategoryID = $this->input->post('subsubcategoryID');
        $companyID = current_companyID();

        $wheremain='';
        $wheresub='';
        $wheresubsub='';

        if(!empty($mainCategoryID)){
            $wheremain='AND mainCategoryID = '.$mainCategoryID;
        }

        if(!empty($subcategoryID)){
            $wheresub='AND subcategoryID = '.$subcategoryID;
        }

        if(!empty($subsubcategoryID)){
            $wheresubsub='AND subSubCategoryID = '.$subsubcategoryID;
        }

        $result = $this->db->query("SELECT
	itemAutoID,
	srp_erp_itemmaster.deletedYN AS deletedYN,
	itemSystemCode,
	itemName,
	seconeryItemCode,
	itemDescription,
	mainCategory,
	defaultUnitOfMeasure,	
	currentStock,
	barcode,
	partNo,
	companyLocalSellingPrice,
	companyLocalCurrency,
	companyLocalCurrencyDecimalPlaces,
	costDescription,
	assteDescription,
	isActive,
	companyLocalWacAmount,
	subcat.description AS SubCategoryDescription,
	subsubcat.description AS SubSubCategoryDescription,	
	 companyLocalWacAmount AS TotalWacAmount,
	 srp_erp_itemmaster.maximunQty,
	 srp_erp_itemmaster.minimumQty,
	 srp_erp_itemmaster.reorderPoint,
	 srp_erp_itemmaster.companyLocalSellingPrice,
	 srp_erp_itemmaster.companyLocalPurchasingPrice
FROM
	`srp_erp_itemmaster`
	JOIN `srp_erp_itemcategory` `subcat` ON `srp_erp_itemmaster`.`subcategoryID` = `subcat`.`itemCategoryID`
	LEFT JOIN `srp_erp_itemcategory` `subsubcat` ON `srp_erp_itemmaster`.`subSubCategoryID` = `subsubcat`.`itemCategoryID` 
WHERE `srp_erp_itemmaster`.`companyID` = $companyID 
	$wheremain $wheresub $wheresubsub
ORDER BY
	`itemAutoID` DESC 
	")->result_array();

//var_dump($this->db->last_query());exit;
        $data = array();
        $a = 1;
        foreach ($result as $row)
        {
            // $hideWacAmount = getPolicyValues('HWC','All');
            $hideWacAmount = 0;
            $row['itemDescription'] = str_replace("<br>","        ",$row['itemDescription']);
            $row['itemDescription'] = str_replace("&nbsp;","  ",$row['itemDescription']);
            if($hideWacAmount==1){
                $d = array(
                    'Num' => $a,
                    'mainCategory' => $row['mainCategory'],
                    'SubCategoryDescription' => $row['SubCategoryDescription'],
                    'SubSubCategoryDescription' => $row['SubSubCategoryDescription'],
                    'itemSystemCode' => $row['itemSystemCode'],
                    'seconeryItemCode' => $row['seconeryItemCode'],
                    'itemDescription' => $row['itemDescription'],
                    'barcode' => $row['barcode'],
                    'partNo' => $row['partNo'],
                    'defaultUnitOfMeasure' => $row['defaultUnitOfMeasure'],
                    'CurrentStock' => $row['currentStock'],
                    'maximunQty' => $row['maximunQty'],
                    'minimumQty' => $row['minimumQty'],
                    'reorderPoint' => $row['reorderPoint'],
                    'companyLocalCurrency' => $row['companyLocalCurrency'],
                    'companyLocalSellingPrice' => $row['companyLocalSellingPrice']
                );
                $showPurchasePrice = getPolicyValues('SPP', 'All');
                if($showPurchasePrice){
                    $d['companyLocalPurchasingPrice'] = $row['companyLocalPurchasingPrice'];
                }
                array_push($data,$d);
            }else{
                $d = array(
                    'Num' => $a,
                    'mainCategory' => $row['mainCategory'],
                    'SubCategoryDescription' => $row['SubCategoryDescription'],
                    'SubSubCategoryDescription' => $row['SubSubCategoryDescription'],
                    'itemSystemCode' => $row['itemSystemCode'],
                    'seconeryItemCode' => $row['seconeryItemCode'],
                    'itemDescription' => $row['itemDescription'],
                    'barcode' => $row['barcode'],
                    'partNo' => $row['partNo'],
                    'defaultUnitOfMeasure' => $row['defaultUnitOfMeasure'],
                    'CurrentStock' => $row['currentStock'],
                    'maximunQty' => $row['maximunQty'],
                    'minimumQty' => $row['minimumQty'],
                    'reorderPoint' => $row['reorderPoint'],
                    'companyLocalCurrency' => $row['companyLocalCurrency'],
                    'TotalWacAmount' => $row['TotalWacAmount'],
                    'companyLocalSellingPrice' => $row['companyLocalSellingPrice']
                );
                $showPurchasePrice = getPolicyValues('SPP', 'All');
                if($showPurchasePrice){
                    $d['companyLocalPurchasingPrice'] = $row['companyLocalPurchasingPrice'];
                }
                array_push($data,$d);
            }

            $a++;
        }

        return ['items' => $data];
    }

    function im_confirmation()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $currentuser  = current_userID();
        $system_id = trim($this->input->post('itemAutoID') ?? '');


        $this->db->select('itemAutoID');
        $this->db->where('itemAutoID',$system_id);
        $this->db->where('masterConfirmedYN', 1);
        $this->db->from('srp_erp_itemmaster');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Document already confirmed');
        }else{
            $this->load->library('Approvals');
            $this->db->select('itemAutoID, itemSystemCode,modifiedDateTime');
            $this->db->where('itemAutoID', $system_id);
            $this->db->from('srp_erp_itemmaster');
            $grv_data = $this->db->get()->row_array();

            $autoApproval= get_document_auto_approval('INV');

            if($autoApproval==0){
                $approvals_status = $this->approvals->auto_approve($grv_data['itemAutoID'], 'srp_erp_itemmaster','itemAutoID', 'INV',$grv_data['itemSystemCode'],$this->common_data['current_date']);
            }elseif($autoApproval==1){
                $approvals_status = $this->approvals->CreateApproval('INV', $grv_data['itemAutoID'], $grv_data['itemSystemCode'], 'Inventory', 'srp_erp_itemmaster', 'itemAutoID',0,$this->common_data['current_date']);

            }else{
                return array('e', 'Approval levels are not set for this document');

            }
            
            /*if ($approvals_status==1) {
                return array('s', 'Document confirmed Successfully');
            }else if($approvals_status==3){
                return array('w', 'There are no users exist to perform approval for this document.');
            } else {
                return array('e', 'Document confirmation failed');
            }
*/
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Error in Item Master confirmation process.');
            } else {
                $this->db->trans_commit();
                if ($approvals_status==1) {
                    return array('s', 'Document confirmed Successfully');
                }else if($approvals_status==3){
                    return array('w', 'There are no users exist to perform approval for this document.');
                } else {
                    return array('e', 'Document confirmation failed');
                }

            } 
        }
        
       // return array('s', 'There are no records to confirm this document!');
    }

    function check_confirmation()
    {
        $companyID = current_companyID();
        $itemAutoID = $this->input->post('itemAutoID');

        $result = $this->db->query("SELECT * FROM srp_erp_itemmaster 
                                            WHERE	companyID = {$companyID} AND itemAutoID = {$itemAutoID} AND masterConfirmedYN = 1 ")->row_array();

        if (empty($result)) {
            //return array('e', 'No Records Found');
            return $result;
        } else {
            if($result['masterApprovedYN']==1){
                return array('s', 'Fully Approved Item',2);
            }else{
                return array('s', 'Confirmed Item');
            }
        }
    }

    function approve_itemmaster($level_id){
        $this->db->trans_start();
        $itemAutoID = trim($this->input->post('itemAutoID') ?? '');
        //$level_id = trim($this->input->post('level') ?? '');
        //$status = trim($this->input->post('status') ?? '');
        //$comments = trim($this->input->post('comments') ?? '');
        $status = 1;
        $comments ='';

        $this->load->library('approvals');
        $approvals_status = $this->approvals->approve_document($itemAutoID, $level_id, $status, $comments, 'INV');

//        if ($approvals_status == 1) {
//
//            $do_details = $this->db->query("SELECT status FROM srp_erp_deliveryorder WHERE DOAutoID = {$orderID} ")->row('status');
//            if( $do_details!= 2){
//                $cont_data['status'] = 2;
//                $this->db->where('DOAutoID', $orderID);
//                $this->db->update('srp_erp_deliveryorder', $cont_data);
//            }
//
//            $master = $this->db->get_where('srp_erp_deliveryorder', ['DOAutoID'=> $orderID])->row_array();
//
//        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Item Master approval process.');
        } else {
            $this->db->trans_commit();

            switch ($approvals_status){
                case 1: return ['s', 'Item fully approved.', 2]; break;
                case 2: return ['s', 'Item order level - '.$level_id.' successfully approved']; break;
                case 3: return ['s', 'Item successfully rejected.']; break;
                case 5: return ['w', 'Previous Level Approval Not Finished']; break;
                default : return ['e', 'Error in item approvals process'];
            }
        }
    }
    /* Function added */
    function fetch_purchase_price()
    {
        $purchasePrice = $this->input->post('purchaseprice');
        $id=$this->input->post('id');
        $primaryKey=$this->input->post('primaryKey');
        $tableName = $this->input->post('tableName');
        $this->db->select('transactionCurrencyDecimalPlaces,companyLocalExchangeRate');
        $this->db->where($primaryKey, $id);
        $result = $this->db->get($tableName)->row_array();
        $localCurrencyER = 1 / $result['companyLocalExchangeRate'];
        $purchasePrice = trim($purchasePrice);
        $unitprice = round(($purchasePrice / $localCurrencyER), $result['transactionCurrencyDecimalPlaces']);
        return array('status' => true, 'amount' => $unitprice,'masterId'=>$id);
    }

    function load_service_details_report_in_sales_and_marketing($dateFrom, $dateTo,  $items = null, $customer = null)
    {
        //$where = " AND (documentDate  BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "')";
        $where = " ";
        $currency = $this->input->post('s_currency');
        $column_filter = $this->input->post('s_columSelectionDrop');
        $feildsra = array();
        $feilds1 = "";
        if (isset($column_filter)) {
            foreach ($column_filter as $val) {
                if ($val == "barcode" || $val == "partNo" ) {
                    $feildsra[]= 'item.' . $val;
                }
            }
            $feilds1 = join(',', $feildsra);
            if (!empty($feilds1)){
                $feilds1 = $feilds1. ",";
            }
        }
        /*if ($warehouse != null) {
            $where .= " AND srp_erp_itemledger.wareHouseAutoID IN(" . $warehouse . ") ";
        }*/

        if ($items != null) {
            $where .= " AND item.itemAutoID IN(" . $items . ") ";
        }

        if ($customer != null) {
            $where .= " AND customerID IN(" . $customer . ") ";
        }

        $companyID=current_companyID();
        $currencyExchange='companyLocalExchangeRate';
        $currencyAmount = 'companyLocalAmount';
        if($currency=='Reporting'){
            $currencyExchange='companyReportingExchangeRate';
            $currencyAmount = 'companyReportingAmount';
        }

        $q = " SELECT $feilds1
            invoiceDetailsAutoID as detailTableID,
            mainTable.invoiceAutoID AS masterTableID,
            mainTable.documentID AS documentID, 
            transactionCurrencyDecimalPlaces,
            mainTable.transactionCurrency,
            invoiceCode AS DocumentCode,
            invoiceDate AS documentDate,
            detailTbl.unitOfMeasure,
            item.itemAutoID,
            item.itemDescription,
            item.defaultUnitOfMeasure,
            item.itemSystemCode,
            item.seconeryItemCode,
            srp_erp_itemcategory.description AS subCategory,
            cs.customerAutoID,
            cs.customerName,
            cs.customerSystemCode,
             (ROUND( detailTbl.requestedQty - SUM( IFNULL( srp_erp_salesreturndetails.return_Qty, 0 )), 2 ))
             AS qty,
            isGroupBasedTax, 
            CASE WHEN 
             isGroupBasedTax = 1 THEN
             (( detailTbl.unittransactionAmount - IFNULL(detailTbl.discountAmount ,0))/ 100 ) * ( 100 - IFNULL( masterDiscountPercentage, 0 )) + ( IFNULL( detailTbl.taxAmount, 0 )/ detailTbl.requestedQty ) ELSE 
             ((( detailTbl.unittransactionAmount - IFNULL(detailTbl.discountAmount ,0))/ 100 ) * ( 100 - IFNULL( masterDiscountPercentage, 0 )))/mainTable.companyLocalExchangeRate 
            END AS unitSalesVal,
            CASE WHEN 
             isGroupBasedTax = 1 THEN
             ((( detailTbl.unittransactionAmount - IFNULL(detailTbl.discountAmount ,0))/ 100 ) * ( 100 - IFNULL( masterDiscountPercentage, 0 )) + ( IFNULL( detailTbl.taxAmount, 0 )/ detailTbl.requestedQty )) *
             (ROUND( detailTbl.requestedQty - SUM( IFNULL( srp_erp_salesreturndetails.return_Qty, 0 )), 2 ))  ELSE 
             (((( detailTbl.unittransactionAmount - IFNULL(detailTbl.discountAmount ,0))/ 100 ) * ( 100 - IFNULL( masterDiscountPercentage, 0 )))/mainTable.companyLocalExchangeRate) *
             (ROUND( detailTbl.requestedQty - SUM( IFNULL( srp_erp_salesreturndetails.return_Qty, 0 )), 2 ))
            END AS totSalesVal
        FROM
            srp_erp_customerinvoicemaster mainTable
            JOIN srp_erp_customerinvoicedetails detailTbl ON detailTbl.invoiceAutoID = mainTable.invoiceAutoID
            JOIN srp_erp_customermaster cs ON cs.customerAutoID = mainTable.customerID
            JOIN srp_erp_itemmaster item ON item.itemAutoID = detailTbl.ItemAutoID
            LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = item.subcategoryID
            LEFT JOIN srp_erp_salesreturndetails ON mainTable.invoiceAutoID = srp_erp_salesreturndetails.invoiceAutoID 	AND detailTbl.itemAutoID = srp_erp_salesreturndetails.itemAutoID
            LEFT JOIN ( SELECT SUM( discountPercentage ) AS masterDiscountPercentage, invoiceAutoID FROM srp_erp_customerinvoicediscountdetails GROUP BY invoiceAutoID ) discountPercentage ON discountPercentage.invoiceAutoID = mainTable.invoiceAutoID 
            
            WHERE     
                mainTable.companyID = $companyID AND  mainTable.approvedYN = 1 
            -- 	mainTable.customerID IN ( 69) 
                AND item.mainCategory = 'Service' 
                AND (mainTable.invoiceDate BETWEEN '$dateFrom' AND '$dateTo' ) $where
                GROUP BY detailTbl.invoiceDetailsAutoID  
                HAVING qty > 0 

            UNION ALL 
            SELECT $feilds1
                DODetailsAutoID as detailTableID,
                ord_mas.DOAutoID AS masterTableID,
                ord_mas.documentID AS documentID, 
                transactionCurrencyDecimalPlaces,
                ord_mas.transactionCurrency,
                DOCode AS DocumentCode,
                DODate AS documentDate,
                ord_det.unitOfMeasure,
                item.itemAutoID,
                item.itemDescription,
                item.defaultUnitOfMeasure,
                item.itemSystemCode,
                item.seconeryItemCode,
                srp_erp_itemcategory.description AS subCategory,
                cs.customerAutoID,
                cs.customerName,
                cs.customerSystemCode,
                (ROUND( ord_det.requestedQty - SUM( IFNULL( ret_det.return_Qty, 0 )), 2  ))  AS qty,
                isGroupBasedTax, 
                CASE WHEN isGroupBasedTax = 1 THEN 
                (( ord_det.unittransactionAmount - ord_det.discountAmount ) + ( IFNULL( ord_det.taxAmount, 0 )/ ord_det.requestedQty ) ) ELSE 
                (( ord_det.unittransactionAmount - ord_det.discountAmount ) /ord_mas.companyLocalExchangeRate) 
                END AS unitSalesVal ,
                CASE WHEN isGroupBasedTax = 1 THEN 
                (( ord_det.unittransactionAmount - ord_det.discountAmount ) + ( IFNULL( ord_det.taxAmount, 0 )/ ord_det.requestedQty ) ) * 
                ( ROUND( ord_det.requestedQty - SUM( IFNULL( ret_det.return_Qty, 0 )), 2 )) ELSE 
                (( ord_det.unittransactionAmount - ord_det.discountAmount ) /ord_mas.companyLocalExchangeRate) *
                ( ROUND( ord_det.requestedQty - SUM( IFNULL( ret_det.return_Qty, 0 )), 2 ))
                END AS totSalesVal 
            
            FROM
                srp_erp_deliveryorder ord_mas
                JOIN srp_erp_deliveryorderdetails AS ord_det ON ord_mas.DOAutoID = ord_det.DOAutoID
                JOIN srp_erp_customermaster AS cs ON cs.customerAutoID = ord_mas.customerID
                JOIN srp_erp_itemmaster item ON item.itemAutoID = ord_det.ItemAutoID
                LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = item.subcategoryID
                LEFT JOIN srp_erp_salesreturndetails AS ret_det ON ord_mas.DOAutoID = ret_det.DOAutoID AND ord_det.itemAutoID = ret_det.itemAutoID 
                
                WHERE
                ord_mas.companyID = $companyID AND  ord_mas.approvedYN = 1 
            -- 	ord_mas.customerID IN ( 69) 
            AND item.mainCategory = 'Service' 
                    AND (ord_mas.DODate BETWEEN '$dateFrom' AND '$dateTo' ) $where
                GROUP BY ord_det.DODetailsAutoID 
                HAVING qty > 0 
            UNION ALL
            SELECT
                receiptVoucherDetailAutoID as detailTableID,
                rv_mas.receiptVoucherAutoId AS masterTableID,
                rv_mas.documentID AS documentID, 
                transactionCurrencyDecimalPlaces,
                rv_mas.transactionCurrency,
                RVcode AS DocumentCode,
                RVdate AS documentDate,
                rv_det.unitOfMeasure,
                item.itemAutoID,
                item.itemDescription,
                item.defaultUnitOfMeasure,
                item.itemSystemCode,
                item.seconeryItemCode,
                srp_erp_itemcategory.description AS subCategory,
                cs.customerAutoID,
                cs.customerName,
                cs.customerSystemCode,
                (ROUND( rv_det.requestedQty , 2  ))  AS qty,
                '' as isGroupBasedTax,
                ( rv_det.unittransactionAmount - rv_det.discountAmount ) AS unitSalesVal,
                ( rv_det.unittransactionAmount - rv_det.discountAmount ) * (	ROUND( rv_det.requestedQty, 2 )) AS totSalesVal 
            FROM
                srp_erp_customerreceiptmaster rv_mas
                JOIN srp_erp_customerreceiptdetail AS rv_det ON rv_mas.receiptVoucherAutoId = rv_det.receiptVoucherAutoId
                JOIN srp_erp_customermaster AS cs ON cs.customerAutoID = rv_mas.customerID
                JOIN srp_erp_itemmaster item ON item.itemAutoID = rv_det.ItemAutoID
                LEFT JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = item.subcategoryID
             WHERE
                rv_mas.companyID = $companyID AND  rv_mas.approvedYN = 1 
            -- 	rv_mas.customerID IN ( 69) 
                AND item.mainCategory = 'Service' 
                    AND (rv_mas.RVdate BETWEEN '$dateFrom' AND '$dateTo' ) $where
                GROUP BY rv_det.receiptVoucherDetailAutoID
            	HAVING qty > 0
                ";

        $result = $this->db->query($q)->result_array();
        return $result;
    }

    /**
     * Get Category prefix
     *
     * @param integer $id
     * @return string
     */
    private function getItemCategoryPrefix($id)
    {
        $this->db->select('codePrefix');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_itemcategory')->row('codePrefix');
    }

    /* End  Function */
}
