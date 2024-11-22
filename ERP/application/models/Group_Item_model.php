<?php

class Group_Item_model extends ERP_Model
{
    function load_subcat()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subid'));
        $this->db->where('groupID', $companyID);
        $this->db->from('srp_erp_groupitemcategory');
        return $this->db->get()->result_array();
    }

    function load_subsubcat()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subsubid'));
        $this->db->where('groupID', $companyID);
        $this->db->from('srp_erp_groupitemcategory');
        return $this->db->get()->result_array();
    }

    function save_item_master()
    {
        $companyID=$this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $this->db->trans_start();
        $mainCategory = explode('|', trim($this->input->post('mainCategory') ?? ''));
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }
        $subcategoryID=$this->input->post('subcategoryID');
        $subcatgl = $this->db->query("SELECT * FROM srp_erp_groupitemcategory WHERE itemCategoryID=$subcategoryID")->row_array();
        $revenueGL=$subcatgl['revenueGL'];
        $costGL=$subcatgl['costGL'];
        $assetGL=$subcatgl['assetGL'];
        $faCostGLAutoID=$subcatgl['faCostGLAutoID'];
        $faACCDEPGLAutoID=$subcatgl['faACCDEPGLAutoID'];
        $faDEPGLAutoID=$subcatgl['faDEPGLAutoID'];
        $faDISPOGLAutoID=$subcatgl['faDISPOGLAutoID'];

        $data['isActive'] = $isactive;
        $data['secondaryItemCode'] = trim($this->input->post('seconeryItemCode') ?? '');
        $data['itemName'] = clear_descriprions(trim($this->input->post('itemName') ?? ''));
        $data['itemDescription'] = clear_descriprions(trim($this->input->post('itemDescription') ?? ''));
        $data['subcategoryID'] = trim($this->input->post('subcategoryID') ?? '');
        $data['subSubCategoryID'] = trim($this->input->post('subSubCategoryID') ?? '');
        $data['subSubSubCategoryID'] = trim($this->input->post('subSubSubCategoryID') ?? '');
        $data['revenueGLAutoID'] = trim($revenueGL);
        $data['costGLAutoID'] = trim($costGL);
        $data['assetGLAutoID'] = trim($assetGL);
        $data['faCostGLAutoID'] = trim($faCostGLAutoID);
        $data['faACCDEPGLAutoID'] = trim($faACCDEPGLAutoID);
        $data['faDEPGLAutoID'] = trim($faDEPGLAutoID);
        $data['faDISPOGLAutoID'] = trim($faDISPOGLAutoID);

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalExchangeRate'] = 1;
        $data['companyLocalSellingPrice'] = trim($this->input->post('companyLocalSellingPrice') ?? '');
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyLocalPurchasingPrice'] = trim($this->input->post('companyLocalPurchasingPrice') ?? '');
        
        if (trim($this->input->post('itemAutoID') ?? '')) {
            $itemAutoID=trim($this->input->post('itemAutoID') ?? '');
           
            $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
            $this->db->update('srp_erp_groupitemmaster', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item : ' . $data['itemName'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('itemAutoID'));
            }
        } else {
            $uom = explode('|', trim($this->input->post('uom') ?? ''));
            $this->load->library('sequence');

            $data['isActive'] = $isactive;
            $data['itemImage'] = 'no-image.png';
            $data['defaultUnitOfMeasureID'] = trim($this->input->post('defaultUnitOfMeasureID') ?? '');
            $data['defaultUnitOfMeasure'] = trim($uom[0] ?? '');
            $data['mainCategoryID'] = trim($this->input->post('mainCategoryID') ?? '');
            $data['mainCategory'] = trim($mainCategory[1] ?? '');
            $data['financeCategory'] = $this->finance_category($data['mainCategoryID']);


            $data['companyLocalWacAmount'] = 0.00;
            $data['companyReportingWacAmount'] = 0.00;
            $data['groupID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $number = $this->db->query("SELECT IFNULL(MAX(serialNo),0) as serialNo FROM srp_erp_groupitemmaster")->row_array();
            $data['serialNo'] = $number["serialNo"]+1;

            $data['itemSystemCode'] = $this->sequence->sequence_generator_group_item(
                trim($mainCategory[0] ?? ''),
                0,
                $companyID,
                $this->common_data['company_data']['company_code'],
                $this->getCategoryPrefix($data['subcategoryID']),
                $this->getCategoryPrefix($data['subSubCategoryID']),
                $this->getCategoryPrefix($data['subSubSubCategoryID']),
                $companyID
            );

            $this->db->insert('srp_erp_groupitemmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    /**
     * Get Category prefix
     * 
     * @param integer $id
     * @return string
     */
    private function getCategoryPrefix($id)
    {
        $this->db->select('codePrefix');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_groupitemcategory')->row('codePrefix');
    }

    function finance_category($id)
    {
        $this->db->select('categoryTypeID');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_groupitemcategory')->row('categoryTypeID');
    }

    function load_item_header()
    {
        $this->db->select('*');
        $this->db->where('itemAutoID', $this->input->post('itemAutoID'));
        return $this->db->get('srp_erp_groupitemmaster')->row_array();
    }

    function save_item_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $ItemAutoID = $this->input->post('ItemAutoID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;

        $results=$this->db->delete('srp_erp_groupitemmasterdetails', array('companyGroupID' => $grpid, 'groupItemMasterID' => $this->input->post('groupItemMasterID')));

        foreach($companyid as $key => $val){
            if(!empty($ItemAutoID[$key])){
                $data['groupItemMasterID'] = trim($this->input->post('groupItemMasterID') ?? '');
                $data['ItemAutoID'] = trim($ItemAutoID[$key]);
                $data['companyID'] = trim($val);
                $data['companyGroupID'] = $grpid;

                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $results = $this->db->insert('srp_erp_groupitemmasterdetails', $data);
            }
            //$last_id = $this->db->insert_id();
        }

        if ($results) {
            return array('s', 'Item Link Saved Successfully');
        } else {
            return array('e', 'Item Link Save Failed');
        }
    }

    function delete_item_link()
    {
        $this->db->where('groupItemDetailID', $this->input->post('groupItemDetailID'));
        $result = $this->db->delete('srp_erp_groupitemmasterdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function save_item_duplicate(){
        $companyid = $this->input->post('checkedCompanies');
        $com = current_companyID();
        $grpid = $com;
        $masterGroupID=getParentgroupMasterID();
        $results='';
        $comparr=array();

        $policyItem = getPolicyValues('ITM', 'All');
        $this->load->library('sequence');

        $itemSystemCode = null;
        $sequenceGenerated = false;

        foreach($companyid as $val)
        {
            $i=0;
            $this->db->select('groupItemDetailID');
            $this->db->where('groupItemMasterID', $this->input->post('itemAutoIDDuplicatehn'));
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $masterGroupID);
            $linkexsist = $this->db->get('srp_erp_groupitemmasterdetails')->row_array();

            $this->db->select('*');
            $this->db->where('itemAutoID', $this->input->post('itemAutoIDDuplicatehn'));
            $CurrentCus = $this->db->get('srp_erp_groupitemmaster')->row_array();

            $this->db->select('groupItemCategoryDetailID');
            $this->db->where('groupItemCategoryID', $CurrentCus['mainCategoryID']);
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $masterGroupID);
            $categorylinkexsist = $this->db->get('srp_erp_groupitemcategorydetails')->row_array();

            if (empty($categorylinkexsist))
            {
                $i++;
                $companyName = get_companyData($val);
                $this->db->select('description');
                $this->db->where('itemCategoryID', $CurrentCus['mainCategoryID']);
                $partyDesc = $this->db->get('srp_erp_groupitemcategory')->row_array();
                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Category not linked" ." (".$partyDesc['description'].")" ));
            }

            if (!empty($CurrentCus['mainCategoryID']))
            {
                $this->db->select('itemCategoryID');
                $this->db->where('groupItemCategoryID', $CurrentCus['mainCategoryID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $categoryMainexsist = $this->db->get('srp_erp_groupitemcategorydetails')->row_array();

                if (empty($categoryMainexsist))
                {
                    $i++;
                    $companyName = get_companyData($val);
                    $this->db->select('description');
                    $this->db->where('itemCategoryID', $CurrentCus['mainCategoryID']);
                    $glDesc = $this->db->get('srp_erp_groupitemcategory')->row_array();
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Category not linked" . " (" . $glDesc['description'] . ")"));
                }
            }

            if(!empty($CurrentCus['subcategoryID']))
            {
                $this->db->select('itemCategoryID,');
                $this->db->where('groupItemCategoryID', $CurrentCus['subcategoryID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $categorySubexsist = $this->db->get('srp_erp_groupitemcategorydetails')->row_array();
                if (empty($categorySubexsist)) {
                    $i++;
                    $companyName = get_companyData($val);
                    $this->db->select('description');
                    $this->db->where('itemCategoryID', $CurrentCus['subcategoryID']);
                    $glDesc = $this->db->get('srp_erp_groupitemcategory')->row_array();
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Sub Category not linked" . " (" . $glDesc['description'] . ")"));
                }
            }

            if(!empty($CurrentCus['subSubCategoryID']))
            {
                $this->db->select('itemCategoryID');
                $this->db->where('groupItemCategoryID', $CurrentCus['subSubCategoryID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $categorySubSubexsist = $this->db->get('srp_erp_groupitemcategorydetails')->row_array();

                if (empty($categorySubSubexsist)) {
                    $i++;
                    $companyName = get_companyData($val);
                    $this->db->select('description');
                    $this->db->where('itemCategoryID', $CurrentCus['subSubCategoryID']);
                    $glDesc = $this->db->get('srp_erp_groupitemcategory')->row_array();
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Sub Sub Category not linked" . " (" . $glDesc['description'] . ")"));
                }
            }

            if(!empty($CurrentCus['subSubSubCategoryID']))
            {
                $this->db->select('itemCategoryID');
                $this->db->where('groupItemCategoryID', $CurrentCus['subSubSubCategoryID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $categorySubSubSubexsist = $this->db->get('srp_erp_groupitemcategorydetails')->row_array();

                if (empty($categorySubSubSubexsist)) {
                    $i++;
                    $companyName = get_companyData($val);
                    $this->db->select('description');
                    $this->db->where('itemCategoryID', $CurrentCus['subSubSubCategoryID']);
                    $glDesc = $this->db->get('srp_erp_groupitemcategory')->row_array();
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Sub Sub Sub Category not linked" . " (" . $glDesc['description'] . ")"));
                }
            }

            if(!empty($CurrentCus['defaultUnitOfMeasureID']))
            {
                $this->db->select('UOMMasterID');
                $this->db->where('groupUOMMasterID', $CurrentCus['defaultUnitOfMeasureID']);
                $this->db->where('companyID', $val);
                $this->db->where('companyGroupID', $masterGroupID);
                $uomexsist = $this->db->get('srp_erp_groupuomdetails')->row_array();

                if (empty($uomexsist)) {
                    $i++;
                    $companyName = get_companyData($val);
                    $this->db->select('UnitDes');
                    $this->db->where('UnitID', $CurrentCus['defaultUnitOfMeasureID']);
                    $unitDesc = $this->db->get('srp_erp_group_unit_of_measure')->row_array();
                    array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "UOM not linked" . " (" . $unitDesc['UnitDes'] . ")"));
                }
            }

            $this->db->select('itemAutoID');
            $this->db->where('itemName', $CurrentCus['itemName']);
            $this->db->where('companyID', $val);
            $CurrentCOAexsist = $this->db->get('srp_erp_itemmaster')->row_array();

            if (!empty($CurrentCOAexsist)) {
                $i++;
                $companyName = get_companyData($val);

                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Item name already exist" . " (" . $CurrentCus['itemName'] . ")"));
            }

            if ($i==0)
            {
                if (empty($linkexsist))
                {
                    $subcatid=$CurrentCus['subcategoryID'];
                    $subCategory = $this->db->query("SELECT itemCategoryID FROM srp_erp_groupitemcategorydetails WHERE groupItemCategoryID = " . $subcatid . " AND companyID=" . $val . " AND companyGroupID=" . $masterGroupID . " ")->row_array();
                    $itmcatid=$subCategory['itemCategoryID'];
                    $glcods = $this->db->query("SELECT * FROM srp_erp_itemcategory WHERE itemCategoryID = " . $itmcatid . "  ")->row_array();

                    $this->db->select('company_code,company_default_currencyID,company_default_currency,company_default_decimal,company_reporting_currencyID,company_reporting_currency,company_reporting_decimal');
                    $this->db->where('company_id', $val);
                    $compDetails = $this->db->get('srp_erp_company')->row_array();

                    $this->db->select('description,codePrefix');
                    $this->db->where('itemCategoryID', $categoryMainexsist['itemCategoryID']);
                    $mainCatdet = $this->db->get('srp_erp_itemcategory')->row_array();

                    $data['isActive'] = $CurrentCus['isActive'];
                    $data['seconeryItemCode'] = $CurrentCus['secondaryItemCode'];
                    $data['itemName'] = $CurrentCus['itemName'];
                    $data['itemDescription'] = $CurrentCus['itemDescription'];
                    $data['subcategoryID'] = $categorySubexsist['itemCategoryID'];

                    if(!empty($categorySubSubexsist['itemCategoryID'])) {
                        $data['subSubCategoryID'] = $categorySubSubexsist['itemCategoryID'];
                    }

                    if(!empty($categorySubSubSubexsist['itemCategoryID'])) {
                        $data['subSubSubCategoryID'] = $categorySubSubSubexsist['itemCategoryID'];
                    }

                    $data['partNo'] = $CurrentCus['partNo'];
                    $data['reorderPoint'] = $CurrentCus['partNo'];
                    $data['maximunQty'] = $CurrentCus['maximunQty'];
                    $data['minimumQty'] = $CurrentCus['minimumQty'];
                    $data['comments'] = $CurrentCus['comments'];
                    $data['modifiedPCID'] = $this->common_data['current_pc'];
                    $data['modifiedUserID'] = $this->common_data['current_userID'];
                    $data['modifiedUserName'] = $this->common_data['current_user'];
                    $data['modifiedDateTime'] = $this->common_data['current_date'];
                    $data['companyLocalCurrencyID'] = $compDetails['company_default_currencyID'];
                    $data['companyLocalCurrency'] = $compDetails['company_default_currency'];
                    $data['companyLocalExchangeRate'] = 1;
                    $data['companyLocalSellingPrice'] = $CurrentCus['companyLocalSellingPrice'];
                    $data['companyLocalPurchasingPrice'] =$CurrentCus['companyLocalPurchasingPrice'];
                    $data['companyLocalCurrencyDecimalPlaces'] = $compDetails['company_default_decimal'];
                    $data['companyReportingCurrency'] = $compDetails['company_reporting_currency'];
                    $data['companyReportingCurrencyID'] = $compDetails['company_reporting_currencyID'];
                    $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
                    $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $data['companyReportingCurrencyDecimalPlaces'] = $compDetails['company_reporting_decimal'];
                    $data['companyReportingSellingPrice'] = $CurrentCus['companyReportingSellingPrice'];
                    $data['isSubitemExist'] = $CurrentCus['isSubitemExist'];
                    $data['assteGLAutoID'] = $glcods['assetGL'];
                    $data['faCostGLAutoID'] = $glcods['faCostGLAutoID'];
                    $data['faACCDEPGLAutoID'] = $glcods['faACCDEPGLAutoID'];
                    $data['faDEPGLAutoID'] = $glcods['faDEPGLAutoID'];
                    $data['faDISPOGLAutoID'] = $glcods['faDISPOGLAutoID'];
                    $data['stockAdjustmentGLAutoID'] = $glcods['stockAdjustmentGL'];
                    $stkAdjglDet=$this->fetch_gl_account_desc($glcods['stockAdjustmentGL']);
                    $data['stockAdjustmentSystemGLCode'] = $stkAdjglDet['systemAccountCode'];
                    $data['stockAdjustmentGLCode'] = $stkAdjglDet['GLSecondaryCode'];
                    $data['stockAdjustmentDescription'] = $stkAdjglDet['GLDescription'];
                    $data['stockAdjustmentType'] = $stkAdjglDet['subCategory'];

                    $data['costGLAutoID'] = $glcods['costGL'];
                    $costglDet=$this->fetch_gl_account_desc($glcods['costGL']);
                    $data['costSystemGLCode'] = $costglDet['systemAccountCode'];
                    $data['costGLCode'] = $costglDet['GLSecondaryCode'];
                    $data['costDescription'] = $costglDet['GLDescription'];
                    $data['costType'] = $costglDet['subCategory'];

                    $data['revanueGLAutoID'] = $glcods['revenueGL'];
                    $revglDet = $this->fetch_gl_account_desc($glcods['revenueGL']);
                    $data['revanueSystemGLCode'] = $revglDet['systemAccountCode'];
                    $data['revanueGLCode'] = $revglDet['GLSecondaryCode'];
                    $data['revanueDescription'] = $revglDet['GLDescription'];
                    $data['revanueType'] = $revglDet['subCategory'];


                    $data['assteGLAutoID'] = $glcods['assetGL'];
                    $astglDet = $this->fetch_gl_account_desc($glcods['assetGL']);
                    $data['assteSystemGLCode'] = $astglDet['systemAccountCode'];
                    $data['assteGLCode'] = $astglDet['GLSecondaryCode'];
                    $data['assteDescription'] = $astglDet['GLDescription'];
                    $data['assteType'] = $astglDet['subCategory'];


                    $this->db->SELECT("UnitID,UnitDes,UnitShortCode");
                    $this->db->FROM('srp_erp_unit_of_measure');
                    $this->db->WHERE('UnitID', $uomexsist['UOMMasterID']);
                    $units = $this->db->get()->row_array();

                    $data['itemImage'] = 'no-image.png';
                    $data['defaultUnitOfMeasureID'] = $uomexsist['UOMMasterID'];
                    $data['defaultUnitOfMeasure'] = $units['UnitShortCode'];
                    $data['mainCategoryID'] = $categoryMainexsist['itemCategoryID'];
                    $data['mainCategory'] = trim($mainCatdet['description'] ?? '');
                    $data['financeCategory'] = $this->finance_category($CurrentCus['mainCategoryID']);

                    $data['companyLocalWacAmount'] = 0.00;
                    $data['companyReportingWacAmount'] = 0.00;
                    $data['companyID'] = $val;
                    $companyCode = get_companyData($val);
                    $data['companyCode'] = $companyCode['company_code'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    
                    if (1 == $policyItem && false === $sequenceGenerated)
                    {
                        $itemSystemCode = $this->sequence->sequence_generator_group_item(
                            trim($mainCatdet['codePrefix'] ?? ''),
                            0,
                            $grpid,
                            $this->common_data['company_data']['groupCode'],
                            $this->getCategoryPrefix($CurrentCus['subcategoryID']),
                            $this->getCategoryPrefix($CurrentCus['subSubCategoryID']),
                            $this->getCategoryPrefix($CurrentCus['subSubSubCategoryID']),
                            $grpid
                        );
                        $sequenceGenerated = true;
                    }
                    
                    if (0 == $policyItem)
                    {
                        $itemSystemCode = $this->sequence->sequence_generator_group_item(
                            trim($mainCatdet['codePrefix'] ?? ''),
                            0,
                            $val,
                            $companyCode['company_code'],
                            $this->getCategoryPrefix($CurrentCus['subcategoryID']),
                            $this->getCategoryPrefix($CurrentCus['subSubCategoryID']),
                            $this->getCategoryPrefix($CurrentCus['subSubSubCategoryID'])
                        );
                    }

                    $data['itemSystemCode'] = $itemSystemCode;
                    
                    $data['barcode'] = $data['itemSystemCode'];
                    $this->db->insert('srp_erp_itemmaster', $data);
                    $last_id = $this->db->insert_id();


                    $dataLink['groupItemMasterID'] = trim($this->input->post('itemAutoIDDuplicatehn') ?? '');
                    $dataLink['ItemAutoID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;

                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupitemmasterdetails', $dataLink);
                }
            }
            else 
            {
                continue;
            }

        }

        if ($results)
        {
            return array('s', 'Item Replicated Successfully',$comparr);
        } else
        {
            return array('e', 'Item Replication not successful',$comparr);
        }

    }
    function updategroppolicy()
    {
        $groupPolicyvalue = $this->input->post('policyValue');

        $groupPolicymasterID = $this->input->post('groupPolicymasterID');
        $companyid = current_companyID();
        $this->db->delete('srp_erp_grouppolicy', array('groupPolicymasterID' => $groupPolicymasterID));
        $data['groupPolicymasterID'] = $groupPolicymasterID;
        $data['groupID'] = $companyid;
        $data['code'] = 'ITM';
        $data['documentID'] = 'All';
        $data['isYN'] = $groupPolicyvalue;
        $data['value'] = $groupPolicyvalue;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $results = $this->db->insert('srp_erp_grouppolicy', $data);
        if ($results) {
            return array('s', 'Item Master policy updated successfully');
        } else {
            return array('e', 'Item Master policy updated failed');
        }

    }

    function fetch_gl_account_desc($id)
    {
        $this->db->SELECT("*");
        $this->db->FROM('srp_erp_chartofaccounts');
        $this->db->WHERE('GLAutoID', $id);
        //$this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get()->row_array();
    }


}
