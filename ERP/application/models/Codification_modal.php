<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Codification_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_attribute(){
        $this->db->trans_start();

        $companyID=$this->common_data['company_data']['company_id'];




        if (trim($this->input->post('attributeID') ?? '')) {

            $attributeDescription=$this->input->post('attributeDescription');
            $attributeID=$this->input->post('attributeID');
            $exist = $this->db->query("SELECT attributeID FROM srp_erp_itemcodificationattributes WHERE companyID = '{$companyID}' AND attributeDescription = '{$attributeDescription}' AND attributeID !=$attributeID ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }
            $data['attributeDescription'] = $this->input->post('attributeDescription');
            $data['valueType'] = $this->input->post('valueType');


            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('attributeID', trim($this->input->post('attributeID') ?? ''));
            $this->db->update('srp_erp_itemcodificationattributes', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully');
            }
        } else {
            $attributeDescription=$this->input->post('attributeDescription');
            $exist = $this->db->query("SELECT attributeID FROM srp_erp_itemcodificationattributes WHERE companyID = '{$companyID}' AND attributeDescription = '{$attributeDescription}' ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }

            $data['attributeDescription'] = $this->input->post('attributeDescription');
            $data['valueType'] = $this->input->post('valueType');
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_itemcodificationattributes', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Inserted Successfully');
            }
        }
    }


    function save_attribute_sub(){
        $this->db->trans_start();

        $companyID=$this->common_data['company_data']['company_id'];

        $attributeDescription=$this->input->post('attributeDescription');
        $exist = $this->db->query("SELECT attributeID FROM srp_erp_itemcodificationattributes WHERE companyID = '{$companyID}' AND attributeDescription = '{$attributeDescription}' ")->row_array();
        if(!empty($exist)){
            return array('e', 'Description already exist');exit;
        }

        $data['attributeDescription'] = $this->input->post('attributeDescription');
        $data['valueType'] = $this->input->post('valueType');
        $data['masterID'] = $this->input->post('attributeID');
        $data['levelNo'] = $this->input->post('levl');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_itemcodificationattributes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Insert Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Inserted Successfully');
        }
    }

    function load_assignto_drop()
    {
        $companyID=$this->common_data['company_data']['company_id'];

        $this->db->select('attributeDetailID,detailDescription');
        $this->db->where('attributeID', $this->input->post('attributeID'));
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_itemcodificationattributedetails');
        return  $this->db->get()->result_array();
    }

    function save_attribute_detail(){
        $this->db->trans_start();
        $companyID=$this->common_data['company_data']['company_id'];
        $detailDescription=$this->input->post('detailDescription');
        $attributeID=$this->input->post('attributeID');
        $masterID=$this->input->post('masterID');
        $comment=$this->input->post('comment');
        $attributeDetailID=$this->input->post('attributeDetailID');
        if($attributeDetailID){
            $exist = $this->db->query("SELECT attributeDetailID FROM srp_erp_itemcodificationattributedetails WHERE companyID = '{$companyID}' AND detailDescription = '{$detailDescription}' AND attributeID = '{$attributeID}' AND attributeDetailID != '{$attributeDetailID}' ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }

            $data['detailDescription'] = $detailDescription;
            $data['attributeID'] = $attributeID;
            $data['masterID'] = $masterID;
            $data['comment'] = $comment;

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('attributeDetailID', $attributeDetailID);
            $this->db->update('srp_erp_itemcodificationattributedetails', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully');
            }


        }else{
            $exist = $this->db->query("SELECT attributeDetailID FROM srp_erp_itemcodificationattributedetails WHERE companyID = '{$companyID}' AND detailDescription = '{$detailDescription}' AND attributeID = '{$attributeID}' ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }
            $data['detailDescription'] = $detailDescription;
            $data['attributeID'] = $attributeID;
            $data['masterID'] = $masterID;
            $data['comment'] = $comment;
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_itemcodificationattributedetails', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Inserted Successfully');
            }
        }

    }

    function save_setup(){
        $this->db->trans_start();
        $companyID=$this->common_data['company_data']['company_id'];
        if (trim($this->input->post('codificationSetupID') ?? '')) {
            $description=$this->input->post('description');
            $codificationSetupID=$this->input->post('codificationSetupID');
            $exist = $this->db->query("SELECT codificationSetupID FROM srp_erp_itemcodificationsetup WHERE companyID = '{$companyID}' AND description = '{$description}' AND codificationSetupID !=$codificationSetupID ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }
            $data['description'] = $this->input->post('description');
            $data['noOfElement'] = $this->input->post('noOfElement');

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('codificationSetupID', trim($this->input->post('codificationSetupID') ?? ''));
            $this->db->update('srp_erp_itemcodificationsetup', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Updated Successfully');
            }
        } else {
            $description=$this->input->post('description');
            $exist = $this->db->query("SELECT codificationSetupID FROM srp_erp_itemcodificationsetup WHERE companyID = '{$companyID}' AND description = '{$description}' ")->row_array();
            if(!empty($exist)){
                return array('e', 'Description already exist');exit;
            }

            $data['description'] = $this->input->post('description');
            $data['noOfElement'] = $this->input->post('noOfElement');
            $data['companyID'] = $this->common_data['company_data']['company_id'];

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_itemcodificationsetup', $data);
            $last_id = $this->db->insert_id();

            $noOfElement=$this->input->post('noOfElement');
            for ($x = 1; $x <= $noOfElement; $x++) {
                $dataD['codificationSetupID'] = $last_id;
                $dataD['sortOrder'] = $x;
                $dataD['companyID'] = $this->common_data['company_data']['company_id'];
                $dataD['createdPCID'] = $this->common_data['current_pc'];
                $dataD['createdUserID'] = $this->common_data['current_userID'];
                $dataD['createdDateTime'] = $this->common_data['current_date'];
                $dataD['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_itemcodificationsetupdetails', $dataD);
            }





            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Insert Failed');
            } else {
                $this->db->trans_commit();
                return array('s', 'Inserted Successfully');
            }
        }
    }

    function load_setup_detail(){
        $companyID=$this->common_data['company_data']['company_id'];
        $codificationSetupID=$this->input->post('codificationSetupID');
        $result = $this->db->query("SELECT * FROM srp_erp_itemcodificationsetupdetails WHERE companyID = '{$companyID}' AND codificationSetupID = '{$codificationSetupID}'  ")->result_array();
        return $result;
    }

    function update_setup_details(){
        $setupDetailID=$this->input->post('setupDetailID');
        $fieldnam=$this->input->post('fieldnam');
        $valu=$this->input->post('valu');

        $data[$fieldnam] = $valu;

        $this->db->where('setupDetailID', $setupDetailID);
        $result =$this->db->update('srp_erp_itemcodificationsetupdetails', $data);
        if($result){
            return array('s', 'Updated Successfully');
        }
    }

    function confirmSetup(){
        $companyID=$this->common_data['company_data']['company_id'];
        $codificationSetupID=$this->input->post('codificationSetupID');

        $attributeIDexist = $this->db->query("SELECT setupDetailID FROM srp_erp_itemcodificationsetupdetails WHERE companyID = '{$companyID}' AND codificationSetupID = '{$codificationSetupID}' AND attributeID is null ")->result_array();

        if(!empty($attributeIDexist)){
            return array('e', 'Details are not completed');exit;
        }else{
            $data['confirmedYN'] = 1;
            $data['confirmedByEmpID'] = $this->common_data['current_userID'];
            $data['confirmedDate'] = $this->common_data['current_date'];
            $data['confirmedByName'] = $this->common_data['current_user'];

            $this->db->where('codificationSetupID', $codificationSetupID);
            $result =$this->db->update('srp_erp_itemcodificationsetup', $data);
            if($result){
                return array('s', 'Updated Successfully');
            }
        }
    }

    function load_subcat()
    {
        $this->db->select('itemCategoryID,description,masterID');
        $this->db->where('masterID', $this->input->post('subid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('codificationSetupID IS  NULL', null, false);
        $this->db->from('srp_erp_itemcategory');
        return $subcat = $this->db->get()->result_array();
    }

    function save_assigned_setup_detail(){
        $subcategoryID=$this->input->post('subcategoryID');
        $codificationSetupID=$this->input->post('codificationSetupID');

        $data['codificationSetupID'] = $codificationSetupID;

        $this->db->where('itemCategoryID', $subcategoryID);
        $result =$this->db->update('srp_erp_itemcategory', $data);

        if($result){
            return array('s', 'Saved Successfully');
        }
    }

    function load_codification_tmplat(){
        $subcategoryID=$this->input->post('subid');
        $companyID=$this->common_data['company_data']['company_id'];

        $setupexsist = $this->db->query("SELECT itemCategoryID,codificationSetupID FROM srp_erp_itemcategory WHERE companyID = '{$companyID}' AND itemCategoryID = '{$subcategoryID}'  ")->row_array();

        if (!empty($setupexsist['codificationSetupID'])){
            $codificationSetupID=$setupexsist['codificationSetupID'];
            $setupexsist = $this->db->query("SELECT icsd.setupDetailID as setupDetailID,icsd.codificationSetupID as codificationSetupID,icsd.sortOrder as sortOrder,icsd.attributeID as attributeID,srp_erp_itemcodificationattributes.attributeDescription,srp_erp_itemcodificationattributes.masterID  FROM srp_erp_itemcodificationsetupdetails icsd
LEFT JOIN srp_erp_itemcodificationattributes ON icsd.attributeID = srp_erp_itemcodificationattributes.attributeID

WHERE icsd.companyID = '{$companyID}' AND icsd.codificationSetupID = '{$codificationSetupID}' ORDER BY sortOrder ASC  ")->result_array();
            return $setupexsist;
        }

    }


    function save_item_master()
    {
        $this->db->trans_start();
        $company_id=current_companyID();
        if (!empty(trim($this->input->post('revanue') ?? '') && trim($this->input->post('revanue') != 'Select Revenue GL Account'))) {
            $revanue = explode('|', trim($this->input->post('revanue') ?? ''));
        }
        $cost = explode('|', trim($this->input->post('cost') ?? ''));
        $asste = explode('|', trim($this->input->post('asste') ?? ''));
        $mainCategory = explode('|', trim($this->input->post('mainCategory') ?? ''));
        $stockadjustment=explode('|', trim($this->input->post('stockadjustment') ?? ''));
        $isactive = 0;
        if (!empty($this->input->post('isActive'))) {
            $isactive = 1;
        }

        $generatedtype = $this->input->post('generatedtype');
        $uom = explode('|', trim($this->input->post('uom') ?? ''));
        $data['isActive'] = $isactive;
        $data['seconeryItemCode'] = trim($this->input->post('seconeryItemCode') ?? '');
        $data['secondaryUOMID'] = trim($this->input->post('secondaryUOMID') ?? '');
        /*       $data['itemName'] = clear_descriprions(trim($this->input->post('itemName') ?? ''));*/
        $data['itemName'] = $this->input->post('itemName');
        /*  $data['itemDescription'] = clear_descriprions(trim($this->input->post('itemDescription') ?? ''));*/
        $data['itemDescription'] = $this->input->post('itemDescription');
        $data['subcategoryID'] = trim($this->input->post('subcategoryID') ?? '');
        $data['subSubCategoryID'] = trim($this->input->post('subSubCategoryID') ?? '');
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
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversion($data['companyLocalCurrency'], $data['companyReportingCurrency']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyReportingSellingPrice'] = ($data['companyLocalSellingPrice'] / $data['companyReportingExchangeRate']);
        $data['isSubitemExist'] = trim($this->input->post('isSubitemExist') ?? '');

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


                $attributeDetailID=$this->input->post('attributeDetailID');

                $code=array();
                $item_code=0;
                if(!empty($attributeDetailID)){
                    foreach ($attributeDetailID as $valu) {
                        if(!empty($valu)){
                            $desccod = $this->db->query("SELECT attributeID,detailDescription FROM srp_erp_itemcodificationattributedetails WHERE attributeDetailID = '{$valu}'  ")->row_array();
                            array_push($code,$desccod['detailDescription']);
                        }else{
                            return array('e','Fill All Codification Details');
                            exit;
                        }
                    }
                    $item_code= join("-",$code);

                    $this->db->select('itemSystemCode');
                    $this->db->from('srp_erp_itemmaster');
                    $this->db->where('itemSystemCode', $item_code);
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('itemAutoID!=', $this->input->post('itemAutoID'));
                    $codeExist = $this->db->get()->row_array();

                    if(!empty($codeExist)){
                        return array('e','Document Code already exist');
                        exit;
                    }
                }
                $data['itemSystemCode'] = $item_code;
                $this->db->where('itemAutoID', trim($this->input->post('itemAutoID') ?? ''));
                $this->db->update('srp_erp_itemmaster', $data);
                $this->db->trans_complete();
                $last_id = $this->input->post('itemAutoID');
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Update Failed ' . $this->db->_error_message());
                } else {
                    update_warehouseitems($last_id,$data['barcode'],$data['isActive'],$data['companyLocalSellingPrice']);
                    $this->db->trans_commit();
                    return array('s','Item : ' . $data['itemName'] . ' Updated Successfully.',$last_id,$data['barcode']);
                }
            }

        } else {


            $attributeDetailID=$this->input->post('attributeDetailID');

            $code=array();
            $item_code=0;
            if(!empty($attributeDetailID)){
                foreach ($attributeDetailID as $valu) {
                    if(!empty($valu)){
                        $desccod = $this->db->query("SELECT attributeID,detailDescription FROM srp_erp_itemcodificationattributedetails WHERE attributeDetailID = '{$valu}'  ")->row_array();
                        array_push($code,$desccod['detailDescription']);
                    }else{
                        return array('e','Fill All Codification Details');
                        exit;
                    }
                }
                $item_code= join("-",$code);

                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $item_code);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();

                if(!empty($codeExist)){
                    return array('e','Document Code already exist');
                    exit;
                }
            }

            $barcode= $this->input->post('barcode');
            $barcodeexist=$this->db->query("SELECT barcode FROM `srp_erp_itemmaster` WHERE barcode= '$barcode' AND deletedYN = 0")->row_array();
            if($barcodeexist && !empty($barcode)){
                $this->session->set_flashdata('e', 'Barcode is already exist.');
            }else
            {
                $uom = explode('|', trim($this->input->post('uom') ?? ''));
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
                $data['itemSystemCode'] = $item_code;
//check if itemSystemCode already exist
                $this->db->select('itemSystemCode');
                $this->db->from('srp_erp_itemmaster');
                $this->db->where('itemSystemCode', $data['itemSystemCode']);
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $codeExist = $this->db->get()->row_array();
                if(!empty($codeExist)){
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
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();

                    if(!empty($attributeDetailID)){
                        foreach ($attributeDetailID as $valu) {
                                $desccod = $this->db->query("SELECT attributeID,detailDescription FROM srp_erp_itemcodificationattributedetails WHERE attributeDetailID = '{$valu}'  ")->row_array();

                            $datacd['itemAutoID'] = $last_id;
                            $datacd['attributeDetaiID'] = $valu;
                            $datacd['attributeID'] = $desccod['attributeID'];
                            $datacd['attributeValue'] = $desccod['detailDescription'];

                            $datacd['companyID'] = $this->common_data['company_data']['company_id'];
                            $datacd['createdPCID'] = $this->common_data['current_pc'];
                            $datacd['createdUserID'] = $this->common_data['current_userID'];
                            $datacd['createdUserName'] = $this->common_data['current_user'];
                            $datacd['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_itemmastercodification', $datacd);
                        }
                    }


                    if($generatedtype == 'third')
                    {
                        $itemmaster = $this->db->query("SELECT CONCAT(itemDescription,'-',itemSystemCode,'-',partNo,'-',seconeryItemCode) as itemcode,defaultUnitOfMeasureID
                                                            FROM `srp_erp_itemmaster` where companyID  = $company_id AND itemAutoID = $last_id ")->row_array();
                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode'],$itemmaster['itemcode'],$itemmaster['defaultUnitOfMeasureID']);
                    }else
                    {
                        return array('s','Item : ' . $data['itemSystemCode'] . ' - ' . $data['itemSystemCode'] . ' - ' . $data['itemName'] . ' Saved Successfully.',$last_id,$data['barcode']);
                    }
                }
            }


        }
    }

    function finance_category($id)
    {
        $this->db->select('categoryTypeID');
        $this->db->where('itemCategoryID', $id);
        return $this->db->get('srp_erp_itemcategory')->row('categoryTypeID');
    }


    function item_codification_table_body(){
        $companyID=$this->common_data['company_data']['company_id'];
        $rslt = $this->db->query("SELECT
	`attributeID`,
	`valueType`,
	`masterID`,
	`attributeDescription` 
FROM
	`srp_erp_itemcodificationattributes` 
WHERE
	`companyID` = $companyID 
AND masterID =0 ")->result_array();

        return $rslt;
    }

    function editAttributeDetail(){
        $attributeDetailID=$this->input->post('attributeDetailID');
        $rslt = $this->db->query("SELECT attributeID,attributeDetailID,masterID,detailDescription,comment FROM `srp_erp_itemcodificationattributedetails` WHERE `attributeDetailID` = $attributeDetailID ")->row_array();

        return $rslt;
    }


    function load_sub_codes(){
        $setupDetailID=$this->input->post('setupDetailID');
        $attributeDetailID=$this->input->post('attributeDetailID');
        $attributeID=$this->input->post('attributeID');
        $subcategoryID=$this->input->post('subid');
        $companyID=$this->common_data['company_data']['company_id'];

        $setupexsist = $this->db->query("SELECT itemCategoryID,codificationSetupID FROM srp_erp_itemcategory WHERE companyID = '{$companyID}' AND itemCategoryID = '{$subcategoryID}'  ")->row_array();
        $codificationSetupID=$setupexsist['codificationSetupID'];
        $allsetupdtlm = $this->db->query("SELECT setupDetailID,attributeID FROM srp_erp_itemcodificationsetupdetails WHERE companyID = '{$companyID}' AND codificationSetupID = '{$codificationSetupID}'  ")->result_array();

        $subexistm = $this->db->query("SELECT attributeID,masterID FROM srp_erp_itemcodificationattributes WHERE companyID = '{$companyID}' AND masterID = '{$attributeID}'  ")->result_array();
        $allsetupdtl = array_group_by($allsetupdtlm, 'attributeID');
        $subexist = array_group_by($subexistm, 'attributeID');

        $out_arr = [];
        foreach ($allsetupdtl as $attributeID=>$row){
            if(array_key_exists($attributeID, $subexist)){
                $setupdtlId = $row[0]['setupDetailID'];
                $details_qry= $this->db->query("SELECT attributeDetailID,attributeID,masterID,detailDescription FROM srp_erp_itemcodificationattributedetails WHERE companyID = '{$companyID}' AND attributeID = '{$attributeID}' AND (masterID = '{$attributeDetailID}' OR masterID is null OR masterID = 0)  ")->result_array();

                if(!empty($details_qry)){
                    $out_arr[$setupdtlId] = $details_qry;
                }

            }
        }
        // print_r($out_arr);
        if(!empty($out_arr)&&!empty($attributeDetailID)){
            return array('s','success',$out_arr);
        }else{
            foreach ($allsetupdtl as $attributeID=>$row){
                if(array_key_exists($attributeID, $subexist)){
                    $setupdtlId = $row[0]['setupDetailID'];
                    $details_qry= $this->db->query("SELECT attributeDetailID,attributeID,masterID,detailDescription FROM srp_erp_itemcodificationattributedetails WHERE companyID = '{$companyID}' AND attributeID = '{$attributeID}'  ")->result_array();

                    if(!empty($details_qry)){
                        $out_arr[$setupdtlId] = $details_qry;
                    }

                }
            }

            return array('e','no sub',$out_arr);
        }
    }


    function load_codification_edit_drp(){
        $subcategoryID=$this->input->post('subid');
        $companyID=$this->common_data['company_data']['company_id'];

        $setupexsist = $this->db->query("SELECT itemCategoryID,codificationSetupID FROM srp_erp_itemcategory WHERE companyID = '{$companyID}' AND itemCategoryID = '{$subcategoryID}'  ")->row_array();

        if (!empty($setupexsist['codificationSetupID'])){
            $codificationSetupID=$setupexsist['codificationSetupID'];
            $setupexsist = $this->db->query("SELECT icsd.setupDetailID as setupDetailID,icsd.codificationSetupID as codificationSetupID,icsd.sortOrder as sortOrder,icsd.attributeID as attributeID,srp_erp_itemcodificationattributes.attributeDescription,srp_erp_itemcodificationattributes.masterID  FROM srp_erp_itemcodificationsetupdetails icsd
LEFT JOIN srp_erp_itemcodificationattributes ON icsd.attributeID = srp_erp_itemcodificationattributes.attributeID

WHERE icsd.companyID = '{$companyID}' AND icsd.codificationSetupID = '{$codificationSetupID}' ORDER BY sortOrder ASC  ")->result_array();
            return $setupexsist;
        }
    }


}
