<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class AssetManagemnt_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_asset()
    {
        $this->db->trans_start();
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $DEPpercentage = trim($this->input->post('DEPpercentage') ?? '');
        $DEPpercentage = floatval(preg_replace('/[^\d.]/', '', $DEPpercentage));
        $isOperationalAsset = $this->input->post('isOperationalAsset');

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['assetDescription'] = trim($this->input->post('assetDescription') ?? '');
        $data['MANUFACTURE'] = trim($this->input->post('MANUFACTURE') ?? '');
        $data['dateAQ'] = trim($this->input->post('dateAQ') ?? '');
        $data['dateDEP'] = trim($this->input->post('dateDEP') ?? '');
        $data['depMonth'] = trim($this->input->post('depMonth') ?? '');
        $data['DEPpercentage'] = $DEPpercentage;
        $data['comments'] = trim($this->input->post('comments') ?? '');
        $data['currentLocation'] = trim($this->input->post('currentLocation') ?? '');
        $data['faUnitSerialNo'] = trim($this->input->post('faUnitSerialNo') ?? '');
        $data['barcode'] = trim($this->input->post('barcode') ?? '');
        $data['isOperationalAsset'] = $isOperationalAsset;
        $data['custodianID'] = trim($this->input->post('custodianID') ?? '');
        $data['replacementAssetsID'] = trim($this->input->post('replacementID') ?? '');
        $data['rfidCode'] = trim($this->input->post('rfidCode') ?? '');
        if(!empty($this->input->post('accDepAmount'))){
            $data['accDepAmount'] = trim($this->input->post('accDepAmount') ?? '');
        }
        if(!empty($this->input->post('accDepDate'))){
            $data['accDepDate'] = trim($this->input->post('accDepDate') ?? '');
        }
        if(!empty($this->input->post('salvageValue'))){
            $data['salvageAmount'] = $this->input->post('salvageValue');
        }
        
        /*Current Convertioon*/

        $data['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];

        $data['transactionCurrency'] = get_currency_code(trim($this->input->post('transactionCurrency') ?? ''));
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrency') ?? '');
        $data['transactionAmount'] = $this->input->post('COSTUNIT');
        
        $data['transactionCurrencyExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);;

        $localCurrency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $localCurrency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
        $data['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $data['companyLocalAmount'] = round(($data['transactionAmount'] / $localCurrency['conversion']), $data['companyLocalCurrencyDecimalPlaces']);

        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['companyReportingAmount'] = round(($data['transactionAmount'] / $reporting_currency['conversion']), $data['companyReportingDecimalPlaces']);

        /*Current Convertioon*/

        $data['assetType'] = $this->input->post('assetType');

        /*If own Assets*/
        if ($data['assetType'] == 1) {
            $costGLCode = fetch_gl_account_desc($this->input->post('COSTGLCODEdes'));
            $data['costGLAutoID'] = $this->input->post('COSTGLCODEdes');
            $data['costGLCode'] = $costGLCode['systemAccountCode'];
            $data['COSTGLCODEdes'] = $costGLCode['GLDescription'];

            $ACCDEPGLCODE = fetch_gl_account_desc($this->input->post('ACCDEPGLCODEdes'));
            $data['ACCDEPGLAutoID'] = $this->input->post('ACCDEPGLCODEdes');;
            $data['ACCDEPGLCODE'] = $ACCDEPGLCODE['systemAccountCode'];;
            $data['ACCDEPGLCODEdes'] = $ACCDEPGLCODE['GLDescription'];;

            $DEPGLCODE = fetch_gl_account_desc($this->input->post('DEPGLCODEdes'));
            $data['DEPGLAutoID'] = $this->input->post('DEPGLCODEdes');
            $data['DEPGLCODE'] = $DEPGLCODE['systemAccountCode'];
            $data['DEPGLCODEdes'] = $DEPGLCODE['GLDescription'];

            $DISPOGLCODE = fetch_gl_account_desc($this->input->post('DISPOGLCODEdes'));
            $data['DISPOGLAutoID'] = $this->input->post('DISPOGLCODEdes');
            $data['DISPOGLCODE'] = $DISPOGLCODE['systemAccountCode'];
            $data['DISPOGLCODEdes'] = $DISPOGLCODE['GLDescription'];
        } else {

            $data['costGLAutoID'] = null;
            $data['costGLCode'] = null;
            $data['COSTGLCODEdes'] = null;

            $data['ACCDEPGLAutoID'] = null;
            $data['ACCDEPGLCODE'] = null;
            $data['ACCDEPGLCODEdes'] = null;

            $data['DEPGLAutoID'] = null;
            $data['DEPGLCODE'] = null;
            $data['DEPGLCODEdes'] = null;

            $data['DISPOGLAutoID'] = null;
            $data['DISPOGLCODE'] = null;
            $data['DISPOGLCODEdes'] = null;

            $data['supplierID'] = $this->input->post('supplier');
        }

        $data['faCatID'] = trim($this->input->post('faCatID') ?? '');
        $data['faSubCatID'] = $this->input->post('faSubCatID');

        $isPostToGL = $this->input->post('isPostToGL');
        $isFromGRV = $this->input->post('isFromGRV');

        /*Is not GRV*/
        if ($isFromGRV != 1) {
            if (isset($isPostToGL)) {
                $postGL = fetch_gl_account_desc($this->input->post('postGLAutoID'));
                $data['isPostToGL'] = 1;
                $data['postGLAutoID'] = $this->input->post('postGLAutoID');
                $data['postGLCode'] = $postGL['systemAccountCode'];
                $data['postGLCodeDes'] = $postGL['GLDescription'];
            } else {
                $data['isPostToGL'] = null;
                $data['postGLAutoID'] = null;
                $data['postGLCode'] = null;
                $data['postGLCodeDes'] = null;
            }
        }

        $data['postDate'] = $this->input->post('postDate');

        $groupTO = $this->input->post('groupTO');

        if ($groupTO) {
            $data['groupTO'] = $groupTO;
        } else {
            $data['groupTO'] = null;
        }

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['timestamp'] = $this->common_data['current_date'];
        $data['documentID'] = 'FA';

        if (trim($this->input->post('faID') ?? '')) {
            $this->db->where('faID', trim($this->input->post('faID') ?? ''));
            $this->db->update('srp_erp_fa_asset_master', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Asset Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Asset Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => trim($this->input->post('faID') ?? ''));
            }

        } else {
            $this->load->library('sequence');
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['timestamp'] = $this->common_data['current_date'];
            $data['faCode'] = $this->sequence->sequence_generator("FA");

            $this->db->insert('srp_erp_fa_asset_master', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', "Save Failed." . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => 'error');
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => 'success', 'last_id' => $last_id, 'faCode' => $data['faCode']);
            }
        }
    }

    function assetConfirm()
    {
        $this->save_asset();
        $this->load->library('approvals');
        $pk = $this->input->post('faID');

        $assetDetails = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE faID='{$pk}'")->row_array();
        $companyID=current_companyID();
        $accDepDate= $assetDetails['accDepDate'];
        $accDepAmount= $assetDetails['accDepAmount'];
        if($accDepAmount>0){
            $financeyearDate = $this->db->query("SELECT
            srp_erp_companyfinanceyear.*,srp_erp_companyfinanceperiod.dateFrom,srp_erp_companyfinanceperiod.dateTo
            FROM
                `srp_erp_companyfinanceyear`
            JOIN srp_erp_companyfinanceperiod ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID AND '$accDepDate' BETWEEN dateFrom
            AND dateTo AND srp_erp_companyfinanceperiod.isActive=1
            WHERE
                '$accDepDate' BETWEEN beginingDate
            AND endingDate
            AND srp_erp_companyfinanceyear.isActive = 1
            AND srp_erp_companyfinanceyear.companyID = $companyID")->row_array();

            if(empty($financeyearDate)){
                $this->session->set_flashdata('e', 'Accumulated Depreciation Date is not between active financial periods ');
                return array('status' => false);
                exit;
            }
        }

        $_POST = $assetDetails;

        $this->form_validation->set_rules('assetType', 'Asset Type', 'trim|required');
        $this->form_validation->set_rules('segmentID', 'Segment', 'trim|required');
        $this->form_validation->set_rules('assetDescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('manufacture', 'Manufacture', 'trim|required');
        $this->form_validation->set_rules('dateAQ', 'Date Acquired', 'trim|required');
        $this->form_validation->set_rules('dateDEP', 'Depreciation Date Start', 'trim|required');
        $this->form_validation->set_rules('depMonth', 'Life time in years', 'trim|required');
        $this->form_validation->set_rules('DEPpercentage', 'DEP %', 'trim|required');
        $this->form_validation->set_rules('companyLocalAmount', 'Unit Price (Local)', 'trim|required');
        $this->form_validation->set_rules('transactionCurrency', 'Currency', 'trim|required');
        $this->form_validation->set_rules('faCatID', 'Main Catrgory', 'trim|required');
        $this->form_validation->set_rules('faSubCatID', 'Sub Catrgory', 'trim|required');

        /*if own Assets*/
        $assetType = $assetDetails['assetType'];
        if ($assetType == 1) {
            $this->form_validation->set_rules('costGLAutoID', 'Cost Account', 'trim|required');
            $this->form_validation->set_rules('ACCDEPGLAutoID', 'Acc Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DEPGLAutoID', 'Dep GL Code', 'trim|required');
            $this->form_validation->set_rules('DISPOGLAutoID', 'Disposal GL Code', 'trim|required');

            $this->form_validation->set_rules('postGLAutoID', 'Post to GL Code', 'trim|required');
            $this->form_validation->set_rules('postDate', 'Post Date', 'trim|required');
        }

        if ($assetType == 2) {
            $this->form_validation->set_rules('supplierID', 'Supplier', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
        } else {
            $approvals_status = $this->approvals->CreateApproval('FA', $pk, $assetDetails['faCode'], 'Asset Management', 'srp_erp_fa_asset_master', 'faID');
            if ($approvals_status==1) {
                $data = array('confirmedYN' => 1, 'confirmedByEmpID' => $this->common_data['current_userID'], 'confirmedDate' => $this->common_data['current_date']);
                $this->db->where('faID', $pk);
                $result = $this->db->update('srp_erp_fa_asset_master', $data);

                $cost_asset['assetID'] = $assetDetails['faID'];
                $cost_asset['assetDescription'] = $assetDetails['assetDescription'];

                $cost_asset['costDate'] = $assetDetails['postDate'];

                $cost_asset['companyID'] = $this->common_data['company_data']['company_id'];
                $cost_asset['companyCode'] = $this->common_data['company_data']['company_code'];
                $cost_asset['segmentID'] = $assetDetails['segmentID'];
                $cost_asset['segmentCode'] = $assetDetails['segmentCode'];

                $cost_asset['createdPCID'] = $this->common_data['current_pc'];
                $cost_asset['createdUserID'] = $this->common_data['current_userID'];
                $cost_asset['createdUserName'] = $this->common_data['current_user'];
                $cost_asset['createdDateTime'] = $this->common_data['current_date'];
                $cost_asset['timestamp'] = $this->common_data['current_date'];

                $cost_asset['companyLocalCurrencyID'] = $assetDetails['companyLocalCurrencyID'];
                $cost_asset['companyLocalCurrency'] = $assetDetails['companyLocalCurrency'];
                $cost_asset['companyLocalExchangeRate'] = $assetDetails['companyLocalExchangeRate'];
                $cost_asset['companyLocalCurrencyDecimalPlaces'] = $assetDetails['companyLocalCurrencyDecimalPlaces'];
                $cost_asset['companyReportingCurrency'] = $assetDetails['companyReportingCurrency'];
                $cost_asset['companyReportingCurrencyID'] = $assetDetails['companyReportingCurrencyID'];

                $cost_asset['companyReportingExchangeRate'] = $assetDetails['companyReportingExchangeRate'];
                $cost_asset['companyReportingCurrencyDecimalPlaces'] = $assetDetails['companyReportingDecimalPlaces'];
                $cost_asset['companyLocalAmount'] = $assetDetails['companyLocalAmount'];
                $cost_asset['companyReportingAmount'] = $assetDetails['companyReportingAmount'];

                $this->db->insert('srp_erp_fa_assetcost', $cost_asset);

                $this->session->set_flashdata('s', 'Confirmed Successfully');
                return array('status' => true);
            }else if($approvals_status==3){
                $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
                return array('status' => true);
            } else {
                $this->session->set_flashdata('e', 'Document confirmation failed.');
                return array('status' => false);
            }
        }
    }

    function delete_asset()
    {
        $faId = $this->input->post('faID');
        $this->db->trans_start();
        $this->db->delete('srp_erp_fa_asset_master', array('faID' => $faId, 'companyID' => $this->common_data['company_data']['company_id']));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Asset Successfully Deleted');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function fetch_template_data($faid)
    {
         $assetDetails = $this->db->query("SELECT 
        srp_erp_fa_asset_master.faID,
        srp_erp_fa_asset_master.barcode,
        srp_erp_fa_asset_master.serialNo, 
        srp_erp_fa_asset_master.companyID, 
        srp_erp_fa_asset_master.currentLocation,
        srp_erp_fa_asset_master.companyCode, 
        srp_erp_fa_asset_master.segmentID,
        srp_erp_fa_asset_master.segmentCode, 
        srp_erp_fa_asset_master.faAssetDept, 
        srp_erp_fa_asset_master.faCode, 
        srp_erp_fa_asset_master.assetCodeS, 
        srp_erp_fa_asset_master.faUnitSerialNo, 
        srp_erp_fa_asset_master.assetDescription, 
        srp_erp_fa_asset_master.comments, 
        srp_erp_fa_asset_master.dateAQ, 
        srp_erp_fa_asset_master.dateDEP, 
        srp_erp_fa_asset_master.depMonth, 
        srp_erp_fa_asset_master.DEPpercentage, 
        srp_erp_fa_asset_master.faCatID, 
        srp_erp_fa_asset_master.faSubCatID, 
        srp_erp_fa_asset_master.companyLocalCurrency, 
        srp_erp_fa_asset_master.companyLocalExchangeRate, 
        srp_erp_fa_asset_master.companyReportingCurrency, 
        srp_erp_fa_asset_master.companyReportingExchangeRate, 
        srp_erp_fa_asset_master.companyLocalAmount, 
        srp_erp_fa_asset_master.companyReportingAmount, 
        srp_erp_fa_asset_master.auditCategory, 
        srp_erp_fa_asset_master.partNumber, 
        srp_erp_fa_asset_master.manufacture, 
        srp_erp_fa_asset_master.unitAssign, 
        srp_erp_fa_asset_master.unitAssignHistory, 
        srp_erp_fa_asset_master.image, 
        srp_erp_fa_asset_master.usedBy, 
        srp_erp_fa_asset_master.usedByHistory, 
        srp_erp_fa_asset_master.location, 
        srp_erp_fa_asset_master.currentLocation,
         srp_erp_fa_asset_master.locationHistory, 
         srp_erp_fa_asset_master.selectedForDisposal, 
         srp_erp_fa_asset_master.disposed, 
         srp_erp_fa_asset_master.disposedDate, 
         srp_erp_fa_asset_master.dateDisposed, 
         srp_erp_fa_asset_master.assetdisposalMasterAutoID, 
         srp_erp_fa_asset_master.reasonDisposed, 
         srp_erp_fa_asset_master.cashDisposal, 
         srp_erp_fa_asset_master.costAtDisposal, 
         srp_erp_fa_asset_master.ACCDEPDIP, 
         srp_erp_fa_asset_master.profitLossDisposal, 
         srp_erp_fa_asset_master.technicalHistory, 
         srp_erp_fa_asset_master.costGLAutoID, 
         srp_erp_fa_asset_master.costGLCode, 
         srp_erp_fa_asset_master.costGLCodeDes, 
         srp_erp_fa_asset_master.ACCDEPGLAutoID, 
         srp_erp_fa_asset_master.ACCDEPGLCODE, 
         srp_erp_fa_asset_master.ACCDEPGLCODEdes, 
         srp_erp_fa_asset_master.DEPGLAutoID, 
         srp_erp_fa_asset_master.DEPGLCODE, 
         srp_erp_fa_asset_master.DEPGLCODEdes, 
         srp_erp_fa_asset_master.DISPOGLAutoID, 
         srp_erp_fa_asset_master.DISPOGLCODE, 
         srp_erp_fa_asset_master.DISPOGLCODEdes, 
         srp_erp_fa_asset_master.isPostToGL, 
         srp_erp_fa_asset_master.postGLAutoID, 
         srp_erp_fa_asset_master.postGLCode, 
         srp_erp_fa_asset_master.postGLCodeDes, 
         srp_erp_fa_asset_master.confirmedYN, 
         srp_erp_fa_asset_master.confirmedByEmpID, 
         srp_erp_fa_asset_master.confirmedByName, 
         srp_erp_fa_asset_master.confirmedDate, 
         srp_erp_fa_asset_master.approvedYN, 
         srp_erp_fa_asset_master.approvedDate, 
         srp_erp_fa_asset_master.approvedbyEmpID, 
         srp_erp_fa_asset_master.approvedbyEmpName, 
         srp_erp_fa_asset_master.createdUserGroup, 
         srp_erp_fa_asset_master.selectedYN, 
         srp_erp_fa_asset_master.assetType, 
         srp_erp_fa_asset_master.supplierID, 
         srp_erp_fa_asset_master.tempRecord, 
         srp_erp_fa_asset_master.toolsCondition, 
         srp_erp_fa_asset_master.selectedforJobYN, 
         srp_erp_fa_asset_master.`timestamp`, 
         srp_erp_fa_asset_master.createdPCID, 
         srp_erp_fa_asset_master.createdUserID, 
         srp_erp_fa_asset_master.createdDateTime, 
         srp_erp_fa_asset_master.createdUserName, 
         srp_erp_fa_asset_master.modifiedPCID, 
         srp_erp_fa_asset_master.modifiedUserID, 
         srp_erp_fa_asset_master.modifiedDateTime, 
         srp_erp_fa_asset_master.modifiedUserName, 
         srp_erp_itemcategory.itemCategoryID, 
         srp_erp_itemcategory.description  MaincatDescription, 
         srp_erp_itemcategory_sub.description SubcatDescription,locationName,
         srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces,
         srp_erp_fa_asset_master.postDate,
         srp_erp_suppliermaster.supplierName,
         srp_erp_fa_asset_master.docOrigin,
         srp_erp_fa_asset_master.docOriginSystemCode 
          FROM srp_erp_fa_asset_master
          LEFT JOIN srp_erp_itemcategory ON srp_erp_fa_asset_master.faCatID = srp_erp_itemcategory.itemCategoryID 
          INNER JOIN srp_erp_itemcategory AS srp_erp_itemcategory_sub 
          ON srp_erp_fa_asset_master.faSubCatID = srp_erp_itemcategory_sub.itemCategoryID
          LEFT JOIN srp_erp_location ON srp_erp_fa_asset_master.currentLocation=srp_erp_location.locationID
          LEFT JOIN srp_erp_suppliermaster ON srp_erp_fa_asset_master.supplierID=srp_erp_suppliermaster.supplierAutoID
          WHERE srp_erp_fa_asset_master.faID = '{$faid}'")->row_array();

        return $assetDetails;
    }

    function post_to_gl($faID)
    {
        $assetDetails = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE faID='{$faID}'")->row_array();
        /*GL Post*/
        if ($assetDetails['isPostToGL'] == 1 && $assetDetails['assetType'] == 1) {

            /*if third party*/
            if ($assetDetails['assetType'] == 2) {

                $supplierMaster = $this->db->query("SELECT * FROM `srp_erp_suppliermaster` WHERE `supplierAutoID` = '{$assetDetails['supplierID']}'")->row_array();
                /*Dr*/
                $postGL = fetch_gl_account_desc($assetDetails['postGLAutoID']);

                $GL['documentCode'] = $assetDetails['documentID'];
                $GL['documentMasterAutoID'] = $assetDetails['faID'];
                $GL['documentSystemCode'] = $assetDetails['faCode'];
                $GL['documentDetailAutoID'] = 0;
                $GL['documentNarration'] = $assetDetails['assetDescription'];
                $GL['GLAutoID'] = $assetDetails['postGLAutoID'];
                $GL['systemGLCode'] = $assetDetails['postGLCode'];
                $GL['GLCode'] = $postGL['GLSecondaryCode'];
                $GL['GLDescription'] = $assetDetails['postGLCodeDes'];
                $GL['GLType'] = $postGL['subCategory'];
                $GL['amount_type'] = 'dr';

                $GL['companyLocalCurrencyID'] = $assetDetails['companyLocalCurrencyID'];
                $GL['companyLocalCurrency'] = $assetDetails['companyLocalCurrency'];
                $GL['companyLocalExchangeRate'] = $assetDetails['companyLocalExchangeRate'];
                $GL['companyLocalCurrencyDecimalPlaces'] = $assetDetails['companyLocalCurrencyDecimalPlaces'];
                $GL['companyReportingCurrency'] = $assetDetails['companyReportingCurrency'];
                $GL['companyReportingCurrencyID'] = $assetDetails['companyReportingCurrencyID'];

                /*$reporting_currency = currency_conversion($GL['companyLocalCurrency'], $GL['companyReportingCurrency']);*/

                $GL['companyReportingExchangeRate'] = $assetDetails['companyReportingExchangeRate'];
                $GL['companyReportingCurrencyDecimalPlaces'] = $assetDetails['companyReportingDecimalPlaces'];
                $GL['companyLocalAmount'] = round(($assetDetails['companyLocalAmount']), $GL['companyLocalCurrencyDecimalPlaces']);
                $GL['companyReportingAmount'] = round(($assetDetails['companyReportingAmount']), $GL['companyReportingCurrencyDecimalPlaces']);

                $GL['transactionCurrencyID'] = $assetDetails['transactionCurrencyID'];
                $GL['transactionCurrency'] = $assetDetails['transactionCurrency'];
                $GL['transactionExchangeRate'] = $assetDetails['transactionCurrencyExchangeRate'];
                $GL['transactionCurrencyDecimalPlaces'] = $assetDetails['transactionCurrencyDecimalPlaces'];
                $GL['transactionAmount'] = round(($assetDetails['transactionAmount']), $GL['transactionCurrencyDecimalPlaces']);

                $GL['companyID'] = $this->common_data['company_data']['company_id'];
                $GL['companyCode'] = $this->common_data['company_data']['company_code'];
                $GL['segmentID'] = $assetDetails['segmentID'];
                $GL['segmentCode'] = $assetDetails['segmentCode'];
                $GL['createdUserGroup'] = current_user_group();

                $GL['createdPCID'] = $this->common_data['current_pc'];
                $GL['createdUserID'] = $this->common_data['current_userID'];
                $GL['createdUserName'] = $this->common_data['current_user'];
                $GL['createdDateTime'] = $this->common_data['current_date'];
                $GL['timestamp'] = $this->common_data['current_date'];

                $GL['documentDate'] = $assetDetails['postDate'];
                $GL['documentYear'] = date('Y', strtotime($assetDetails['postDate']));
                $GL['documentMonth'] = date('m', strtotime($assetDetails['postDate']));

                $GL['confirmedByEmpID'] = $assetDetails['confirmedByEmpID'];
                $GL['confirmedByName'] = $assetDetails['confirmedByName'];
                $GL['confirmedDate'] = $assetDetails['confirmedDate'];
                $GL['approvedDate'] = $assetDetails['approvedDate'];
                $GL['approvedbyEmpID'] = $assetDetails['approvedbyEmpID'];
                $GL['approvedbyEmpName'] = $assetDetails['approvedbyEmpName'];

                $GL['partyType'] = 'SUP';
                $GL['partyAutoID'] = $supplierMaster['supplierAutoID'];
                $GL['partySystemCode'] = $supplierMaster['supplierSystemCode'];
                $GL['partyName'] = $supplierMaster['supplierName'];
                $GL['partyCurrencyID'] = $supplierMaster['supplierCurrencyID'];
                $GL['partyCurrency'] = $supplierMaster['supplierCurrency'];
                $conversion_arr = currency_conversionID($assetDetails['transactionCurrencyID'], $GL['partyCurrencyID']);
                $GL['partyExchangeRate'] = $conversion_arr['conversion'];
                $GL['partyCurrencyAmount'] = ($assetDetails['transactionAmount'] / $conversion_arr['conversion']);
                $GL['partyCurrencyDecimalPlaces'] = $supplierMaster['supplierCurrencyDecimalPlaces'];
                $GL['subLedgerType'] = null;
                $GL['subLedgerDesc'] = null;


                $this->db->insert('srp_erp_generalledger', $GL);

                /*Cr*/
                $liabilityGl = fetch_gl_account_desc($supplierMaster['liabilityAutoID']);

                $GLCr['documentCode'] = $assetDetails['documentID'];
                $GLCr['documentMasterAutoID'] = $assetDetails['faID'];
                $GLCr['documentSystemCode'] = $assetDetails['faCode'];
                $GLCr['documentDetailAutoID'] = 0;
                $GLCr['documentNarration'] = $assetDetails['assetDescription'];
                $GLCr['GLAutoID'] = $liabilityGl['GLAutoID'];
                $GLCr['systemGLCode'] = $liabilityGl['systemAccountCode'];
                $GLCr['GLCode'] = $liabilityGl['GLSecondaryCode'];
                $GLCr['GLDescription'] = $liabilityGl['GLDescription'];
                $GLCr['GLType'] = $liabilityGl['subCategory'];
                $GLCr['amount_type'] = 'cr';

                $GLCr['companyLocalCurrencyID'] = $assetDetails['companyLocalCurrencyID'];
                $GLCr['companyLocalCurrency'] = $assetDetails['companyLocalCurrency'];
                $GLCr['companyLocalExchangeRate'] = $assetDetails['companyLocalExchangeRate'];
                $GLCr['companyLocalCurrencyDecimalPlaces'] = $assetDetails['companyLocalCurrencyDecimalPlaces'];
                $GLCr['companyReportingCurrency'] = $assetDetails['companyReportingCurrency'];
                $GLCr['companyReportingCurrencyID'] = $assetDetails['companyReportingCurrencyID'];

                $GLCr['companyReportingExchangeRate'] = $assetDetails['companyReportingExchangeRate'];
                $GLCr['companyReportingCurrencyDecimalPlaces'] = $assetDetails['companyReportingDecimalPlaces'];
                $GLCr['companyLocalAmount'] = round(($assetDetails['companyLocalAmount'] * -1), $GLCr['companyLocalCurrencyDecimalPlaces']);
                $GLCr['companyReportingAmount'] = round(($assetDetails['companyReportingAmount'] * -1), $GLCr['companyReportingCurrencyDecimalPlaces']);

                $GLCr['transactionCurrency'] = $assetDetails['transactionCurrency'];
                $GLCr['transactionCurrencyID'] = $assetDetails['transactionCurrencyID'];
                $GLCr['transactionExchangeRate'] = $assetDetails['transactionCurrencyExchangeRate'];
                $GLCr['transactionCurrencyDecimalPlaces'] = $assetDetails['transactionCurrencyDecimalPlaces'];
                $GLCr['transactionAmount'] = round(($assetDetails['transactionAmount'] * -1), $GLCr['transactionCurrencyDecimalPlaces']);

                $GLCr['companyID'] = $this->common_data['company_data']['company_id'];
                $GLCr['companyCode'] = $this->common_data['company_data']['company_code'];
                $GLCr['segmentID'] = $assetDetails['segmentID'];
                $GLCr['segmentCode'] = $assetDetails['segmentCode'];
                $GLCr['createdUserGroup'] = current_user_group();

                $GLCr['createdPCID'] = $this->common_data['current_pc'];
                $GLCr['createdUserID'] = $this->common_data['current_userID'];
                $GLCr['createdUserName'] = $this->common_data['current_user'];
                $GLCr['createdDateTime'] = $this->common_data['current_date'];
                $GLCr['timestamp'] = $this->common_data['current_date'];

                $GLCr['documentDate'] = $assetDetails['postDate'];
                $GLCr['documentYear'] = date('Y', strtotime($assetDetails['postDate']));
                $GLCr['documentMonth'] = date('m', strtotime($assetDetails['postDate']));

                $GLCr['confirmedByEmpID'] = $assetDetails['confirmedByEmpID'];
                $GLCr['confirmedByName'] = $assetDetails['confirmedByName'];
                $GLCr['confirmedDate'] = $assetDetails['confirmedDate'];
                $GLCr['approvedDate'] = $assetDetails['approvedDate'];
                $GLCr['approvedbyEmpID'] = $assetDetails['approvedbyEmpID'];
                $GLCr['approvedbyEmpName'] = $assetDetails['approvedbyEmpName'];

                $GLCr['partyType'] = 'SUP';
                $GLCr['partyAutoID'] = $supplierMaster['supplierAutoID'];
                $GLCr['partySystemCode'] = $supplierMaster['supplierSystemCode'];
                $GLCr['partyName'] = $supplierMaster['supplierName'];
                $GLCr['partyCurrencyID'] = $supplierMaster['supplierCurrencyID'];
                $GLCr['partyCurrency'] = $supplierMaster['supplierCurrency'];
                $conversion_arr = currency_conversionID($assetDetails['transactionCurrencyID'], $GL['partyCurrencyID']);
                $GLCr['partyExchangeRate'] = $conversion_arr['conversion'];
                //$GLCr['partyCurrencyAmount'] = ($assetDetails['transactionAmount'] / $conversion_arr['conversion']);
                $GLCr['partyCurrencyDecimalPlaces'] = $supplierMaster['supplierCurrencyDecimalPlaces'];
                $GLCr['subLedgerType'] = '2';
                $GLCr['subLedgerDesc'] = 'AP';

                $this->db->insert('srp_erp_generalledger', $GLCr);

            } else {
                /*Cr*/
                $postGL = fetch_gl_account_desc($assetDetails['postGLAutoID']);

                $GL['documentCode'] = $assetDetails['documentID'];
                $GL['documentMasterAutoID'] = $assetDetails['faID'];
                $GL['documentSystemCode'] = $assetDetails['faCode'];
                $GL['documentDetailAutoID'] = 0;
                $GL['documentNarration'] = $assetDetails['assetDescription'];
                $GL['GLAutoID'] = $assetDetails['postGLAutoID'];
                $GL['systemGLCode'] = $assetDetails['postGLCode'];
                $GL['GLCode'] = $postGL['GLSecondaryCode'];
                $GL['GLDescription'] = $assetDetails['postGLCodeDes'];
                $GL['GLType'] = $postGL['subCategory'];
                $GL['amount_type'] = 'cr';

                $GL['companyLocalCurrencyID'] = $assetDetails['companyLocalCurrencyID'];
                $GL['companyLocalCurrency'] = $assetDetails['companyLocalCurrency'];
                $GL['companyLocalExchangeRate'] = $assetDetails['companyLocalExchangeRate'];
                $GL['companyLocalCurrencyDecimalPlaces'] = $assetDetails['companyLocalCurrencyDecimalPlaces'];
                $GL['companyReportingCurrency'] = $assetDetails['companyReportingCurrency'];
                $GL['companyReportingCurrencyID'] = $assetDetails['companyReportingCurrencyID'];

                $GL['companyReportingExchangeRate'] = $assetDetails['companyReportingExchangeRate'];
                $GL['companyReportingCurrencyDecimalPlaces'] = $assetDetails['companyReportingDecimalPlaces'];
                $GL['companyLocalAmount'] = round(($assetDetails['companyLocalAmount'] * -1), $GL['companyLocalCurrencyDecimalPlaces']);
                $GL['companyReportingAmount'] = round(($assetDetails['companyReportingAmount'] * -1), $GL['companyReportingCurrencyDecimalPlaces']);

                $GL['transactionCurrencyID'] = $assetDetails['transactionCurrencyID'];
                $GL['transactionCurrency'] = $assetDetails['transactionCurrency'];
                $GL['transactionExchangeRate'] = $assetDetails['transactionCurrencyExchangeRate'];
                $GL['transactionCurrencyDecimalPlaces'] = $assetDetails['transactionCurrencyDecimalPlaces'];
                $GL['transactionAmount'] = round(($assetDetails['transactionAmount'] * -1), $GL['transactionCurrencyDecimalPlaces']);

                $GL['companyID'] = $this->common_data['company_data']['company_id'];
                $GL['companyCode'] = $this->common_data['company_data']['company_code'];
                $GL['segmentID'] = $assetDetails['segmentID'];
                $GL['segmentCode'] = $assetDetails['segmentCode'];
                $GL['createdUserGroup'] = current_user_group();

                $GL['createdPCID'] = $this->common_data['current_pc'];
                $GL['createdUserID'] = $this->common_data['current_userID'];
                $GL['createdUserName'] = $this->common_data['current_user'];
                $GL['createdDateTime'] = $this->common_data['current_date'];
                $GL['timestamp'] = $this->common_data['current_date'];

                $GL['documentDate'] = $assetDetails['postDate'];
                $GL['documentYear'] = date('Y', strtotime($assetDetails['postDate']));
                $GL['documentMonth'] = date('m', strtotime($assetDetails['postDate']));

                $GL['confirmedByEmpID'] = $assetDetails['confirmedByEmpID'];
                $GL['confirmedByName'] = $assetDetails['confirmedByName'];
                $GL['confirmedDate'] = $assetDetails['confirmedDate'];
                $GL['approvedDate'] = $assetDetails['approvedDate'];
                $GL['approvedbyEmpID'] = $assetDetails['approvedbyEmpID'];
                $GL['approvedbyEmpName'] = $assetDetails['approvedbyEmpName'];

                $this->db->insert('srp_erp_generalledger', $GL);

                /*Dr*/

                $costGl = fetch_gl_account_desc($assetDetails['costGLAutoID']);

                $GLCr['documentCode'] = $assetDetails['documentID'];
                $GLCr['documentMasterAutoID'] = $assetDetails['faID'];
                $GLCr['documentSystemCode'] = $assetDetails['faCode'];
                $GLCr['documentDetailAutoID'] = 0;
                $GLCr['documentNarration'] = $assetDetails['assetDescription'];
                $GLCr['GLAutoID'] = $assetDetails['costGLAutoID'];
                $GLCr['systemGLCode'] = $assetDetails['costGLCode'];
                $GLCr['GLCode'] = $costGl['GLSecondaryCode'];
                $GLCr['GLDescription'] = $assetDetails['costGLCodeDes'];
                $GLCr['GLType'] = $costGl['subCategory'];
                $GLCr['amount_type'] = 'dr';

                $GLCr['companyLocalCurrencyID'] = $assetDetails['companyLocalCurrencyID'];
                $GLCr['companyLocalCurrency'] = $assetDetails['companyLocalCurrency'];
                $GLCr['companyLocalExchangeRate'] = $assetDetails['companyLocalExchangeRate'];
                $GLCr['companyLocalCurrencyDecimalPlaces'] = $assetDetails['companyLocalCurrencyDecimalPlaces'];
                $GLCr['companyReportingCurrency'] = $assetDetails['companyReportingCurrency'];
                $GLCr['companyReportingCurrencyID'] = $assetDetails['companyReportingCurrencyID'];

//            $reporting_currency = currency_conversion($GLCr['companyLocalCurrency'], $GLCr['companyReportingCurrency']);

                $GLCr['companyReportingExchangeRate'] = $assetDetails['companyReportingExchangeRate'];
                $GLCr['companyReportingCurrencyDecimalPlaces'] = $assetDetails['companyReportingDecimalPlaces'];
                $GLCr['companyLocalAmount'] = round($assetDetails['companyLocalAmount'], $GLCr['companyLocalCurrencyDecimalPlaces']);
                $GLCr['companyReportingAmount'] = round($assetDetails['companyReportingAmount'], $GLCr['companyReportingCurrencyDecimalPlaces']);

                $GLCr['transactionCurrency'] = $assetDetails['transactionCurrency'];
                $GLCr['transactionCurrencyID'] = $assetDetails['transactionCurrencyID'];
                $GLCr['transactionExchangeRate'] = $assetDetails['transactionCurrencyExchangeRate'];
                $GLCr['transactionCurrencyDecimalPlaces'] = $assetDetails['transactionCurrencyDecimalPlaces'];
                $GLCr['transactionAmount'] = round($assetDetails['transactionAmount'], $GLCr['transactionCurrencyDecimalPlaces']);

                $GLCr['companyID'] = $this->common_data['company_data']['company_id'];
                $GLCr['companyCode'] = $this->common_data['company_data']['company_code'];
                $GLCr['segmentID'] = $assetDetails['segmentID'];
                $GLCr['segmentCode'] = $assetDetails['segmentCode'];
                $GLCr['createdUserGroup'] = current_user_group();

                $GLCr['createdPCID'] = $this->common_data['current_pc'];
                $GLCr['createdUserID'] = $this->common_data['current_userID'];
                $GLCr['createdUserName'] = $this->common_data['current_user'];
                $GLCr['createdDateTime'] = $this->common_data['current_date'];
                $GLCr['timestamp'] = $this->common_data['current_date'];

                $GLCr['documentDate'] = $assetDetails['postDate'];
                $GLCr['documentYear'] = date('Y', strtotime($assetDetails['postDate']));
                $GLCr['documentMonth'] = date('m', strtotime($assetDetails['postDate']));

                $GLCr['confirmedByEmpID'] = $assetDetails['confirmedByEmpID'];
                $GLCr['confirmedByName'] = $assetDetails['confirmedByName'];
                $GLCr['confirmedDate'] = $assetDetails['confirmedDate'];
                $GLCr['approvedDate'] = $assetDetails['approvedDate'];
                $GLCr['approvedbyEmpID'] = $assetDetails['approvedbyEmpID'];
                $GLCr['approvedbyEmpName'] = $assetDetails['approvedbyEmpName'];

                $this->db->insert('srp_erp_generalledger', $GLCr);
               $postGLAutoID= $assetDetails['postGLAutoID'];
                $isBank = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE GLAutoID = $postGLAutoID")->row_array();

                if($isBank['isBank']==1){
                    $BL['documentDate'] =  $assetDetails['postDate'];
                    $BL['transactionType'] = 2;
                    $BL['transactionCurrencyID'] = $assetDetails['transactionCurrencyID'];
                    $BL['transactionCurrency'] = $assetDetails['transactionCurrency'];
                    $BL['transactionExchangeRate'] = $assetDetails['transactionCurrencyExchangeRate'];
                    $BL['transactionCurrencyDecimalPlaces'] = $assetDetails['transactionCurrencyDecimalPlaces'];
                    $BL['transactionAmount'] = round($assetDetails['transactionAmount'], $BL['transactionCurrencyDecimalPlaces']);
                    $BL['bankCurrencyID'] = $isBank['bankCurrencyID'];
                    $BL['bankCurrency'] = $isBank['bankCurrencyCode'];
                    $bankCurrency = currency_conversionID($assetDetails['transactionCurrencyID'], $isBank['bankCurrencyID']);
                    $BL['bankCurrencyExchangeRate'] = $bankCurrency['conversion'];
                    $BL['bankCurrencyDecimalPlaces'] = $bankCurrency['DecimalPlaces'];
                    $BL['bankCurrencyAmount'] = round($assetDetails['transactionAmount']/$bankCurrency['conversion'], $bankCurrency['DecimalPlaces']);
                    $BL['modeofPayment'] = 1;
                    $BL['memo'] = $assetDetails['assetDescription'];
                    $BL['bankName'] = $isBank['bankName'];
                    $BL['bankGLAutoID'] = $isBank['GLAutoID'];
                    $BL['bankSystemAccountCode'] = $isBank['systemAccountCode'];
                    $BL['bankGLSecondaryCode'] = $isBank['GLSecondaryCode'];
                    $BL['documentMasterAutoID'] = $assetDetails['faID'];
                    $BL['documentType'] = 'PV';
                    $BL['documentSystemCode'] = $assetDetails['faCode'];
                    $BL['companyID'] = $this->common_data['company_data']['company_id'];
                    $BL['companyCode'] = $this->common_data['company_data']['company_code'];
                    $BL['segmentID'] = $assetDetails['segmentID'];
                    $BL['segmentCode'] = $assetDetails['segmentCode'];
                    $BL['createdPCID'] = $this->common_data['current_pc'];
                    $BL['createdUserID'] = $this->common_data['current_userID'];
                    $BL['createdUserName'] = $this->common_data['current_user'];
                    $BL['createdDateTime'] = $this->common_data['current_date'];
                    $BL['timestamp'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_bankledger', $BL);
                }
            }


        }
        /*//GL Post*/
    }

    /**
     * @return array
     */
    function assetDepGenerate()
    {
        $financeyear = trim($this->input->post('financeyear') ?? '');
        $financeyear_period = trim($this->input->post('financeyear_period') ?? '');
        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $period = explode('|', $financeyear_period);
        $FYperiod = input_format_date($period[0], $date_format_policy);
        $FYperiodto = input_format_date($period[1], $date_format_policy);

        $this->db->trans_start();
        $yearEx = explode('-', $FYperiod);
        $month = $yearEx[1] . '/' . $yearEx[0];
        $isAvaialbeDep = $this->db->query("SELECT * FROM `srp_erp_fa_assetdepreciationperiods` WHERE `depMonthYear` = '{$month}' AND companyID='{$companyID}'")->row_array();
        $isAvaialbearr = array();
        $faidwr = '';
        $isAvaialbeDeps = $this->db->query("SELECT * FROM `srp_erp_fa_assetdepreciationperiods` WHERE `depMonthYear` = '{$month}' AND companyID='{$companyID}'")->result_array();

        if(!empty($isAvaialbeDeps)){
            foreach($isAvaialbeDeps as $val){
                array_push($isAvaialbearr,$val['faID']);
            }
        }

        if(!empty($isAvaialbearr)){
            $whereNotIN = "( " . join(",", $isAvaialbearr) . " )";
            $faidwr = " AND srp_erp_fa_asset_master.faID NOT IN ". $whereNotIN;
        }

        $assetsMasters = $this->db->query("SELECT 
        srp_erp_fa_asset_master.faID,
        srp_erp_fa_asset_master.salvageAmount, 
        srp_erp_fa_asset_master.faCode, 
        srp_erp_fa_asset_master.serialNo, 
        srp_erp_fa_asset_master.assetDescription, 
        srp_erp_fa_asset_master.depMonth, 
        srp_erp_fa_asset_master.DEPpercentage, 
        Sum(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS AccDepAmount, 
        srp_erp_fa_asset_master.assetCodeS, 
        srp_erp_fa_asset_master.faUnitSerialNo, 
        srp_erp_fa_asset_master.faCatID, 
        srp_erp_fa_asset_master.faSubCatID, 
        srp_erp_fa_asset_master.faSubCatID2, 
        srp_erp_fa_asset_master.faSubCatID3, 
        srp_erp_fa_asset_master.transactionCurrency, 
        srp_erp_fa_asset_master.transactionCurrencyID, 
        srp_erp_fa_asset_master.transactionCurrencyExchangeRate, 
        srp_erp_fa_asset_master.transactionAmount, 
        srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces,
         srp_erp_fa_asset_master.companyLocalCurrency,
         srp_erp_fa_asset_master.companyLocalCurrencyID, 
         srp_erp_fa_asset_master.companyLocalExchangeRate, 
         srp_erp_fa_asset_master.companyLocalAmount, 
         srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces, 
         srp_erp_fa_asset_master.companyReportingCurrency, 
         srp_erp_fa_asset_master.companyReportingCurrencyID, 
         srp_erp_fa_asset_master.companyReportingExchangeRate,
          srp_erp_fa_asset_master.companyReportingAmount, 
          srp_erp_fa_asset_master.companyReportingDecimalPlaces,
          srp_erp_fa_asset_master.segmentID,
          srp_erp_fa_asset_master.segmentCode,
          srp_erp_fa_asset_master.missingDepAmount 
          FROM srp_erp_fa_asset_master 
          LEFT JOIN srp_erp_fa_assetdepreciationperiods 
          ON srp_erp_fa_asset_master.faID = srp_erp_fa_assetdepreciationperiods.faID 
          WHERE `dateDEP` <= '{$FYperiodto}' 
          AND (accDepDate <= '{$FYperiodto}' 
          or accDepDate IS NULL)  
          AND srp_erp_fa_asset_master.companyID='{$companyID}' 
          AND srp_erp_fa_asset_master.approvedYN='1' 
          AND srp_erp_fa_asset_master.selectedForDisposal <> 1 
          AND srp_erp_fa_asset_master.assetType=1 
          AND srp_erp_fa_asset_master.DEPpercentage>0 $faidwr GROUP BY srp_erp_fa_asset_master.faID ")->result_array();

        $financeyearDate = $this->db->query("SELECT * FROM `srp_erp_companyfinanceyear` WHERE `companyFinanceYearID` = '{$financeyear}' AND companyID='{$companyID}'")->row_array();

        if (!empty($assetsMasters)) {
            $this->load->library('sequence');

            $depMaster['companyID'] = $companyID;
            $depMaster['companyCode'] = current_companyCode();

            $depMaster['documentID'] = 'FAD';
            $depMaster['depDate'] = $FYperiodto;
            $depMaster['serialNo'] = '';

            $depMaster['companyFinanceYearID'] = $financeyear;

            $depMaster['FYBegin'] = $financeyearDate['beginingDate'];
            $depMaster['FYEnd'] = $financeyearDate['endingDate'];

            $depMaster['FYPeriodDateFrom'] = $FYperiod;
            $depMaster['FYPeriodDateTo'] = $FYperiodto;

            $depMaster['depCode'] = $this->sequence->sequence_generator("FAD");
            $depMaster['depMonthYear'] = $month;

            $depMaster['createdPCID'] = $this->common_data['current_pc'];
            $depMaster['createdUserID'] = $this->common_data['current_userID'];
            $depMaster['createdUserName'] = $this->common_data['current_user'];
            $depMaster['createdDateTime'] = $this->common_data['current_date'];
            $depMaster['timestamp'] = $this->common_data['current_date'];

            /*Currency*/
            $depMaster['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $depMaster['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $depMaster['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $depMaster['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];

            $reporting_currency = currency_conversion($depMaster['companyLocalCurrency'], $depMaster['companyReportingCurrency']);

            $depMaster['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $depMaster['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $depMaster['transactionExchangeRate'] = 1;
            $depMaster['transactionAmount'] = '';
            $depMaster['transactionCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];

            $depMaster['companyLocalExchangeRate'] = 1;
            $depMaster['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $depMaster['companyLocalAmount'] = '';

            $depMaster['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $depMaster['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $depMaster['companyReportingAmount'] = ((float)$depMaster['companyLocalAmount'] / $depMaster['companyReportingExchangeRate']);

            /*Currency*/

            $this->db->insert('srp_erp_fa_depmaster', $depMaster);
            $last_dep_id = $this->db->insert_id();

            foreach ($assetsMasters as $assetsMaster) {

                $blAmount = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];
                $pendingDepreciation = dep_calculate($assetsMaster['companyLocalAmount'], $assetsMaster['DEPpercentage'],$assetsMaster['salvageAmount']);
                $depAmount = $pendingDepreciation + $assetsMaster['missingDepAmount'];

                $nbv = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];
                $salvageAmount = $assetsMaster['salvageAmount'];

                if($nbv < $salvageAmount) {
                    $nbv = $salvageAmount;
                }
                if ($nbv < $depAmount) {
                    $depAmount = $nbv;
                }
              
                if(round($depAmount, $assetsMaster['companyLocalCurrencyDecimalPlaces']) > 0)
                { 
                
                    if ($blAmount > $salvageAmount) {
                        $depDetails["companyID"] = $companyID;
                        $depDetails["depMasterAutoID"] = $last_dep_id;
                        $depDetails["faFinanceCatID"] = '';
                        $depDetails["faMainCategory"] = $assetsMaster['faCatID'];
                        $depDetails["faSubCategory"] = $assetsMaster['faSubCatID'];
                        $depDetails["faID"] = $assetsMaster['faID'];
                        $depDetails["faCode"] = $assetsMaster['faCode'];
                        $depDetails["assetDescription"] = $assetsMaster['assetDescription'];
                        $depDetails["depMonth"] = $yearEx[0];
                        $depDetails["depPercent"] = $assetsMaster['DEPpercentage'];
                        $depDetails["depMonthYear"] = $month;
    
    
                        $depDetails['companyLocalCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];
                        $depDetails['companyLocalCurrency'] = $assetsMaster['companyLocalCurrency'];
                        $depDetails['companyReportingCurrencyID'] = $assetsMaster['companyReportingCurrencyID'];
                        $depDetails['companyReportingCurrency'] = $assetsMaster['companyReportingCurrency'];
                        $depDetails['transactionCurrency'] = $assetsMaster['companyLocalCurrency'];
                        $depDetails['transactionCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];
    
                        $localCurrency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyLocalCurrency']);
                        $transactionCurrency = currency_conversion($depDetails['transactionCurrency'], $depDetails['companyLocalCurrency']);
                        $reporting_currency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyReportingCurrency']);
    
                        $depDetails['companyLocalExchangeRate'] = 1;
                        $depDetails['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                        $depDetails['transactionExchangeRate'] = 1;
    
                        $depDetails['companyLocalCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];
                        $depDetails['companyReportingCurrencyDecimalPlaces'] = $assetsMaster['companyReportingDecimalPlaces'];
                        $depDetails['transactionCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];
    
    
                        $depDetails['companyLocalAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                        $depDetails['transactionAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                        $depDetails['companyReportingAmount'] = round(($depAmount / $reporting_currency['conversion']), $assetsMaster['companyReportingDecimalPlaces']);
    
                        /*//Currency*/
                        $depDetails['segmentID'] = $assetsMaster['segmentID'];
                        $depDetails['segmentCode'] = $assetsMaster['segmentCode'];
    
    
                        $depDetails['createdPCID'] = $this->common_data['current_pc'];
                        $depDetails['createdUserID'] = $this->common_data['current_userID'];
                        $depDetails['createdUserName'] = $this->common_data['current_user'];
                        $depDetails['createdDateTime'] = $this->common_data['current_date'];
                        $depDetails['timestamp'] = $this->common_data['current_date'];
    
                        $this->db->insert('srp_erp_fa_assetdepreciationperiods', $depDetails);
    
                    }
                }
              
            }
           

            /*updating asset masster 'missingDepAmount'*/
            $data_fa['missingDepAmount'] = 0;
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_fa_asset_master', $data_fa);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', "Save Failed." . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_dep_id);
            }

        } else {
            $this->session->set_flashdata('e', "There is no asset Exists to perform depreciation.");
            return array('status' => false);
        }
    }

    function delete_asset_depreciation()
    {
        $depMasterAutoID = $this->input->post('depMasterAutoID');
        $this->db->trans_start();

        $this->db->delete('srp_erp_fa_depmaster', array('depMasterAutoID' => $depMasterAutoID, 'companyID' => $this->common_data['company_data']['company_id']));

        $this->db->delete('srp_erp_fa_assetdepreciationperiods', array('depMasterAutoID' => $depMasterAutoID, 'companyID' => $this->common_data['company_data']['company_id']));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Asset Depreciation Successfully Deleted');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }


    public function groupToAsset()
    {
        $faCatID = $this->input->post('faCatID');
        $companyCode = current_companyCode();
        $assets = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.assetDescription FROM srp_erp_fa_asset_master WHERE srp_erp_fa_asset_master.faCatID = '{$faCatID}' AND srp_erp_fa_asset_master.companyCode = '{$companyCode}' ")->result_array();

        $option = '';
        foreach ($assets as $key => $asset) {
            $option .= "<option value='{$asset['faID']}'>" . $asset['faCode'] . '-' . $asset['assetDescription'] . "</option>";
        }
        echo $option;
    }

    function assetDepConfirm()
    {
        $this->load->library('approvals');
        $pk = $this->input->post('depMasterAutoID');
        $depmaster = $this->db->query("SELECT * FROM srp_erp_fa_depmaster WHERE depMasterAutoID='{$pk}'")->row_array();

        $validate_code = validate_code_duplication($depmaster['depCode'], 'depCode', $pk,'depMasterAutoID', 'srp_erp_fa_depmaster');
        if(!empty($validate_code)) {
            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
            return array('status' => false);
        }
        $approvals_status = $this->approvals->CreateApproval('FAD', $pk, $depmaster['depCode'], 'Asset Management', 'srp_erp_fa_depmaster', 'depMasterAutoID');
        if ($approvals_status==1) {
            $data = array('confirmedYN' => 1, 'confirmedByEmpID' => $this->common_data['current_userID'], 'confirmedDate' => $this->common_data['current_date']);
            $this->db->where('depMasterAutoID', $pk);
            $result = $this->db->update('srp_erp_fa_depmaster', $data);

            $this->session->set_flashdata('s', 'Confirmed Successfully');
            return array('status' => true);
        }else if($approvals_status==3){
            $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return array('status' => false);
        }
    }

    function save_depreciation_approval($system_code)
    {
        /*Dr*/
        $depMastersDebits = $this->db->query("SELECT
	srp_erp_fa_depmaster.depCode,
	srp_erp_fa_depmaster.depDate,
	srp_erp_fa_depmaster.depMonthYear,
	srp_erp_fa_depmaster.FYBegin,
	srp_erp_fa_depmaster.FYEnd,
	srp_erp_fa_depmaster.FYPeriodDateFrom,
	srp_erp_fa_depmaster.FYPeriodDateTo,
	srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID,
	srp_erp_fa_assetdepreciationperiods.faFinanceCatID,
	srp_erp_fa_assetdepreciationperiods.faID,
	srp_erp_fa_assetdepreciationperiods.faMainCategory,
	srp_erp_fa_assetdepreciationperiods.faSubCategory,
	srp_erp_fa_assetdepreciationperiods.faCode,
	srp_erp_fa_assetdepreciationperiods.assetDescription,
	srp_erp_fa_assetdepreciationperiods.transactionCurrency,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyID,
	srp_erp_fa_assetdepreciationperiods.transactionExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrency,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrency,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces,
	srp_erp_fa_depmaster.documentID,
	srp_erp_fa_depmaster.serialNo,
	srp_erp_fa_depmaster.depMasterAutoID,
	srp_erp_fa_asset_master.ACCDEPGLCODE,
	srp_erp_fa_asset_master.DEPGLCODE,
	srp_erp_fa_asset_master.ACCDEPGLAutoID,
	srp_erp_fa_asset_master.DEPGLAutoID,
	srp_erp_fa_assetdepreciationperiods.segmentID,
	srp_erp_fa_assetdepreciationperiods.segmentCode,
	srp_erp_fa_depmaster.approvedbyEmpName,
    srp_erp_fa_depmaster.approvedbyEmpID,
    srp_erp_fa_depmaster.approvedDate,
    srp_erp_fa_depmaster.confirmedByEmpID,
    srp_erp_fa_depmaster.confirmedByName,
    srp_erp_fa_depmaster.confirmedDate
FROM
	srp_erp_fa_assetdepreciationperiods
INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.depMasterAutoID = '{$system_code}' GROUP BY DEPGLAutoID,
	srp_erp_fa_asset_master.segmentID")->result_array();

        foreach ($depMastersDebits as $depMastersDebit) {

            /*Dr*/
            $depgl = fetch_gl_account_desc($depMastersDebit['DEPGLAutoID']);

            $GLCr['documentCode'] = $depMastersDebit['documentID'];
            $GLCr['documentMasterAutoID'] = $depMastersDebit['depMasterAutoID'];
            $GLCr['documentSystemCode'] = $depMastersDebit['depCode'];
            $GLCr['documentDetailAutoID'] = 0;
            $GLCr['documentNarration'] = '';
            $GLCr['GLAutoID'] = $depMastersDebit['DEPGLAutoID'];
            $GLCr['systemGLCode'] = $depgl['systemAccountCode'];
            $GLCr['GLCode'] = $depgl['GLSecondaryCode'];
            $GLCr['GLDescription'] = $depgl['GLDescription'];
            $GLCr['GLType'] = $depgl['subCategory'];
            $GLCr['amount_type'] = 'dr';

            $GLCr['companyLocalCurrency'] = $depMastersDebit['companyLocalCurrency'];
            $GLCr['companyLocalCurrencyID'] = $depMastersDebit['companyLocalCurrencyID'];
            $GLCr['companyLocalExchangeRate'] = $depMastersDebit['companyLocalExchangeRate'];
            $GLCr['companyLocalCurrencyDecimalPlaces'] = $depMastersDebit['companyLocalCurrencyDecimalPlaces'];
            $GLCr['companyReportingCurrency'] = $depMastersDebit['companyReportingCurrency'];
            $GLCr['companyReportingCurrencyID'] = $depMastersDebit['companyReportingCurrencyID'];


            $GLCr['companyReportingExchangeRate'] = $depMastersDebit['companyReportingExchangeRate'];
            $GLCr['companyReportingCurrencyDecimalPlaces'] = $depMastersDebit['companyReportingCurrencyDecimalPlaces'];
            $GLCr['companyLocalAmount'] = $depMastersDebit['companyLocalAmount'];
            $GLCr['companyReportingAmount'] = $depMastersDebit['companyReportingAmount'];

            $GLCr['transactionCurrencyID'] = $depMastersDebit['companyLocalCurrencyID'];
            $GLCr['transactionCurrency'] = $depMastersDebit['companyLocalCurrency'];
            $GLCr['transactionExchangeRate'] = $depMastersDebit['companyLocalExchangeRate'];
            $GLCr['transactionAmount'] = round($depMastersDebit['companyLocalAmount'], $depMastersDebit['companyLocalCurrencyDecimalPlaces']);
            $GLCr['transactionCurrencyDecimalPlaces'] = $depMastersDebit['companyLocalCurrencyDecimalPlaces'];


            $GLCr['companyID'] = $this->common_data['company_data']['company_id'];
            $GLCr['companyCode'] = $this->common_data['company_data']['company_code'];
            $GLCr['segmentID'] = $depMastersDebit['segmentID'];
            $GLCr['segmentCode'] = $depMastersDebit['segmentCode'];
            $GLCr['createdUserGroup'] = current_user_group();

            $GLCr['createdPCID'] = $this->common_data['current_pc'];
            $GLCr['createdUserID'] = $this->common_data['current_userID'];
            $GLCr['createdUserName'] = $this->common_data['current_user'];
            $GLCr['createdDateTime'] = $this->common_data['current_date'];
            $GLCr['timestamp'] = $this->common_data['current_date'];

            $GLCr['documentDate'] = $depMastersDebit['depDate'];
            $GLCr['documentYear'] = date('Y', strtotime($depMastersDebit['depDate']));
            $GLCr['documentMonth'] = date('m', strtotime($depMastersDebit['depDate']));


            $GLCr['confirmedByEmpID'] = $depMastersDebit['confirmedByEmpID'];
            $GLCr['confirmedByName'] = $depMastersDebit['confirmedByName'];
            $GLCr['confirmedDate'] = $depMastersDebit['confirmedDate'];
            $GLCr['approvedDate'] = $depMastersDebit['approvedDate'];
            $GLCr['approvedbyEmpID'] = $depMastersDebit['approvedbyEmpID'];
            $GLCr['approvedbyEmpName'] = $depMastersDebit['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $GLCr);

        }

        /*Cr*/
        $depMastersCredits = $this->db->query("SELECT
	srp_erp_fa_depmaster.depCode,
	srp_erp_fa_depmaster.depDate,
	srp_erp_fa_depmaster.depMonthYear,
	srp_erp_fa_depmaster.FYBegin,
	srp_erp_fa_depmaster.FYEnd,
	srp_erp_fa_depmaster.FYPeriodDateFrom,
	srp_erp_fa_depmaster.FYPeriodDateTo,
	srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID,
	srp_erp_fa_assetdepreciationperiods.faFinanceCatID,
	srp_erp_fa_assetdepreciationperiods.faID,
	srp_erp_fa_assetdepreciationperiods.faMainCategory,
	srp_erp_fa_assetdepreciationperiods.faSubCategory,
	srp_erp_fa_assetdepreciationperiods.faCode,
	srp_erp_fa_assetdepreciationperiods.assetDescription,
	srp_erp_fa_assetdepreciationperiods.transactionCurrency,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyID,
	srp_erp_fa_assetdepreciationperiods.transactionExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrency,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrency,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces,
	srp_erp_fa_depmaster.documentID,
	srp_erp_fa_depmaster.serialNo,
	srp_erp_fa_depmaster.depMasterAutoID,
	srp_erp_fa_asset_master.ACCDEPGLCODE,
	srp_erp_fa_asset_master.DEPGLCODE,
	srp_erp_fa_asset_master.ACCDEPGLAutoID,
	srp_erp_fa_asset_master.DEPGLAutoID,
	srp_erp_fa_assetdepreciationperiods.segmentID,
	srp_erp_fa_assetdepreciationperiods.segmentCode,
	srp_erp_fa_depmaster.approvedbyEmpName,
    srp_erp_fa_depmaster.approvedbyEmpID,
    srp_erp_fa_depmaster.approvedDate,
    srp_erp_fa_depmaster.confirmedByEmpID,
    srp_erp_fa_depmaster.confirmedByName,
    srp_erp_fa_depmaster.confirmedDate
FROM
	srp_erp_fa_assetdepreciationperiods
INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.depMasterAutoID = '{$system_code}'
GROUP BY
	ACCDEPGLAutoID")->result_array();

        foreach ($depMastersCredits as $depMastersCredit) {
            /*Cr*/

            $accdepGl = fetch_gl_account_desc($depMastersCredit['ACCDEPGLAutoID']);

            $GLdr['documentCode'] = $depMastersCredit['documentID'];
            $GLdr['documentMasterAutoID'] = $depMastersCredit['depMasterAutoID'];
            $GLdr['documentSystemCode'] = $depMastersCredit['depCode'];
            $GLdr['documentDetailAutoID'] = 0;
            $GLdr['documentNarration'] = '';
            $GLdr['GLAutoID'] = $depMastersCredit['ACCDEPGLAutoID'];
            $GLdr['systemGLCode'] = $accdepGl['systemAccountCode'];
            $GLdr['GLCode'] = $accdepGl['GLSecondaryCode'];
            $GLdr['GLDescription'] = $accdepGl['GLDescription'];
            $GLdr['GLType'] = $accdepGl['subCategory'];
            $GLdr['amount_type'] = 'cr';

            $GLdr['companyLocalCurrency'] = $depMastersCredit['companyLocalCurrency'];
            $GLdr['companyLocalCurrencyID'] = $depMastersCredit['companyLocalCurrencyID'];
            $GLdr['companyLocalExchangeRate'] = $depMastersCredit['companyLocalExchangeRate'];
            $GLdr['companyLocalCurrencyDecimalPlaces'] = $depMastersCredit['companyLocalCurrencyDecimalPlaces'];
            $GLdr['companyReportingCurrency'] = $depMastersCredit['companyReportingCurrency'];
            $GLdr['companyReportingCurrencyID'] = $depMastersCredit['companyReportingCurrencyID'];


            $GLdr['companyReportingExchangeRate'] = $depMastersCredit['companyReportingExchangeRate'];
            $GLdr['companyReportingCurrencyDecimalPlaces'] = $depMastersCredit['companyReportingCurrencyDecimalPlaces'];
            $GLdr['companyLocalAmount'] = ($depMastersCredit['companyLocalAmount'] * -1);
            $GLdr['companyReportingAmount'] = ($depMastersCredit['companyReportingAmount'] * -1);

            $GLdr['transactionCurrency'] = $depMastersCredit['companyLocalCurrency'];
            $GLdr['transactionCurrencyID'] = $depMastersCredit['companyLocalCurrencyID'];
            $GLdr['transactionExchangeRate'] = $depMastersCredit['companyLocalExchangeRate'];
            $GLdr['transactionAmount'] = round(($depMastersCredit['companyLocalAmount'] * -1), $depMastersCredit['companyLocalCurrencyDecimalPlaces']);
            $GLdr['transactionCurrencyDecimalPlaces'] = $depMastersCredit['companyLocalCurrencyDecimalPlaces'];

            $GLdr['companyID'] = $this->common_data['company_data']['company_id'];
            $GLdr['companyCode'] = $this->common_data['company_data']['company_code'];
            $GLdr['segmentID'] = null;
            $GLdr['segmentCode'] = null;
            $GLdr['createdUserGroup'] = current_user_group();

            $GLdr['createdPCID'] = $this->common_data['current_pc'];
            $GLdr['createdUserID'] = $this->common_data['current_userID'];
            $GLdr['createdUserName'] = $this->common_data['current_user'];
            $GLdr['createdDateTime'] = $this->common_data['current_date'];
            $GLdr['timestamp'] = $this->common_data['current_date'];

            $GLdr['documentDate'] = $depMastersCredit['depDate'];
            $GLdr['documentYear'] = date('Y', strtotime($depMastersCredit['depDate']));
            $GLdr['documentMonth'] = date('m', strtotime($depMastersCredit['depDate']));

            $GLdr['confirmedByEmpID'] = $depMastersCredit['confirmedByEmpID'];
            $GLdr['confirmedByName'] = $depMastersCredit['confirmedByName'];
            $GLdr['confirmedDate'] = $depMastersCredit['confirmedDate'];
            $GLdr['approvedDate'] = $depMastersCredit['approvedDate'];
            $GLdr['approvedbyEmpID'] = $depMastersCredit['approvedbyEmpID'];
            $GLdr['approvedbyEmpName'] = $depMastersCredit['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $GLdr);
        }


    }

    function referback_asset()
    {
        $faId = $this->input->post('faId');

        $this->db->select('approvedYN');
        $this->db->where('faId', trim($faId));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_fa_asset_master');
        $approved_fa_asset_master = $this->db->get()->row_array();
        if (!empty($approved_fa_asset_master)) {
            $this->session->set_flashdata('e', 'The document already approved');
            return true;
        }else
        {
            $data['confirmedYN'] = 0;
            $data['confirmedByEmpID'] = NULL;
            $data['confirmedByName'] = NULL;
            $data['confirmedDate'] = NULL;
            $data['approvedYN'] = 0;
            $data['currentLevelNo'] = 1;
            $data['approvedbyEmpID'] = NULL;
            $data['approvedbyEmpName'] = NULL;
            $data['approvedDate'] = NULL;


            $this->db->where('faID', $faId);
            $result = $this->db->update('srp_erp_fa_asset_master', $data);

            $faCode = $this->db->query("SELECT faCode FROM srp_erp_fa_asset_master WHERE faID='$faId'")->row_array();

            $this->db->delete('srp_erp_fa_assetcost', array('assetID' => $faId, 'companyID' => $this->common_data['company_data']['company_id']));
            $this->session->set_flashdata('s', 'Records Updated Successfully');
            $this->db->delete('srp_erp_documentapproved', array('documentSystemCode' => $this->input->post('faId'), 'documentID' => 'FA', 'companyID' => $this->common_data['company_data']['company_id']));
            return true;
        }



    }

    function save_attachment()
    {
        $this->load->library('s3');
        $this->db->trans_start();
        $documentSystemCode = trim($this->input->post('documentSystemCode') ?? '');
        $issueDate = $this->input->post('dateissued');
        $expiryDate = $this->input->post('dateexpired');
        if(!empty($issueDate) && !empty($expiryDate)){
            if ($issueDate < $expiryDate) {
                $info = new SplFileInfo($_FILES["document_file"]["name"]);
                $output_dir = "uploads/asset_attachment'";
                $fileName = trim($this->input->post('document_description') ?? '') . '_' .current_companyCode(). $documentSystemCode . '.' . $info->getExtension();
                $file = $_FILES['document_file'];
                if($file['error'] == 1){
                    return array('e' ,'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).');
                    exit();

                }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
                $allowed_types = explode('|', $allowed_types);
                if(!in_array($ext, $allowed_types)){
                    return array('e' , "The file type you are attempting to upload is not allowed. ( .{$ext} )");
                    exit();
                }
                $size = $file['size'];
                $size = number_format($size / 1048576, 2);

                if($size > 5){
                    return array('e' , "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )");
                    exit();
                }

                $path = "uploads/asset_attachment/$fileName";
                $s3Upload = $this->s3->upload($file['tmp_name'], $path);

                if (!$s3Upload) {
                    return array('e' , "Error in document upload location configuration");
                    exit();
                }


                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
                $data['docExpiryDate'] = trim($this->input->post('dateexpired') ?? '');
                $data['dateofIssued'] = trim($this->input->post('dateissued') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('document_description') ?? '');
                $data['myFileName'] = $path;
                $data['fileType'] = $info->getExtension();

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyID'] = current_companyID();
                $data['companyCode'] = current_companyCode();

                if (trim($this->input->post('documentSystemCode') ?? '')) {
                    $data['createdUserGroup'] = current_user_group();
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['timestamp'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                    $last_id = $this->db->insert_id();
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        //$this->session->set_flashdata('e', 'Asset Document : Save failed ' . $this->db->error());
                        $this->db->trans_rollback();
                        return array('e' ,'Asset Document : Save failed');
                    } else {
                        $this->session->set_flashdata('s', 'Asset Document : Created Successfully.');
                        $this->db->trans_commit();
                        return array('s' ,'Asset Document : Created Successfully',$last_id);
                        //return array('status' => true, 'id' => $last_id);
                    }
                }
            } else {
                return array('e' ,'Date of expiry Should be greater than Issued date.');
            }
        }else{
               /* $output_dir = "uploads/asset_attachment/";
                if (!file_exists($output_dir)) {
                    mkdir("uploads/asset_attachment", 007);
                }
                $attachment_file = $_FILES["document_file"];
                $info = new SplFileInfo($_FILES["document_file"]["name"]);*/
            $info = new SplFileInfo($_FILES["document_file"]["name"]);
            $output_dir = "uploads/asset_attachment'";
            $fileName = trim($this->input->post('document_description') ?? '') . '_' .current_companyCode(). $documentSystemCode . '.' . $info->getExtension();
            $file = $_FILES['document_file'];
            if($file['error'] == 1){
                return array('e' ,'The file you are attempting to upload is larger than the permitted size. (maximum 5MB).');
                exit();

            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
            $allowed_types = explode('|', $allowed_types);
            if(!in_array($ext, $allowed_types)){
                return array('e' , "The file type you are attempting to upload is not allowed. ( .{$ext} )");
                exit();
            }
            $size = $file['size'];
            $size = number_format($size / 1048576, 2);

            if($size > 5){
                return array('e' , "The file you are attempting to upload is larger than the permitted size. ( Maximum 5MB )");
                exit();
            }

            $path = "uploads/asset_attachment/$fileName";
            $s3Upload = $this->s3->upload($file['tmp_name'], $path);

            if (!$s3Upload) {
                return array('e' , "Error in document upload location configuration");
                exit();
            }

              /*  $fileName = trim($this->input->post('document_description') ?? '') . '_' . $documentSystemCode . '.' . $info->getExtension();
                move_uploaded_file($_FILES["document_file"]["tmp_name"], $output_dir . $fileName);*/
                $data['documentID'] = trim($this->input->post('documentID') ?? '');
                $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
                $data['docExpiryDate'] = trim($this->input->post('dateexpired') ?? '');
                $data['dateofIssued'] = trim($this->input->post('dateissued') ?? '');
                $data['attachmentDescription'] = trim($this->input->post('document_description') ?? '');
                $data['myFileName'] = $path;
                $data['fileType'] = $info->getExtension();

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];

                $data['companyID'] = current_companyID();
                $data['companyCode'] = current_companyCode();

                if (trim($this->input->post('documentSystemCode') ?? '')) {
                    $data['createdUserGroup'] = current_user_group();
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['timestamp'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentattachments', $data);
                    $last_id = $this->db->insert_id();
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        //$this->session->set_flashdata('e', 'Asset Document : Save failed ' . $this->db->error());
                        $this->db->trans_rollback();
                        return array('e' ,'Asset Document : Save failed');
                    } else {
                        $this->session->set_flashdata('s', 'Asset Document : Created Successfully.');
                        $this->db->trans_commit();
                        return array('s' ,'Asset Document : Created Successfully',$last_id);
                        //return array('status' => true, 'id' => $last_id);
                    }
                }
        }

    }

    public function asset_image_upload()
    {
        $this->load->library('s3');
        $faID = $this->input->post('faID');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT
	image 
FROM
	`srp_erp_fa_asset_master`
	where 
	companyID = $companyid 
	AND faid = $faID")->row_array();
        if(!empty($itemimageexist))
        {
            $this->s3->delete('uploads/assets/'.$itemimageexist['image']);
        }

        $info = new SplFileInfo($_FILES["files"]["name"]);

        $fileName = 'asset_' . trim($this->input->post('faID') ?? '').'_'.$this->common_data['company_data']['company_code'].  '.' . $info->getExtension();

        $file = $_FILES['files'];
        if($file['error'] == 1){
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if(!in_array($ext, $allowed_types)){
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");

        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if($size > 5){
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $path = "uploads/assets/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }


        $this->db->trans_start();


        /*$dirname = 'asset_' . trim($this->input->post('faID') ?? '');
        $output_dir = "uploads/assets/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/assets/", 007);
        }
        $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'asset_' . trim($this->input->post('faID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);*/

        $data['image'] = $fileName;

        $this->db->where('faID', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_fa_asset_master', $data);


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

    function delete_attachment()
    {

        $index = $this->input->post('index');
        $faID = $this->input->post('faID');


        $this->db->trans_start();

        $this->db->delete('srp_erp_documentattachments', array('attachmentID' => $index, 'documentSystemCode' => $faID));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Asset Document : Delete failed ' . $this->db->error());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Asset Document : Deleted Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    /*Asset Disposal*/
    function save_disposal_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $dsposlDoumntDate = $this->input->post('disposalDocumentDate');
        $disposalDocumentDate = input_format_date($dsposlDoumntDate, $date_format_policy);

        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $financeyear = $this->input->post('companyFinanceYearID');
        $segment = explode('|', trim($this->input->post('segment') ?? ''));
        $financeyear_period = trim($this->input->post('financeyear_period') ?? '');
        $period = explode('|', $financeyear_period);

        $assetType = $this->input->post('assetType');
        $interCompanyID = $this->input->post('interCompanyID');

        if($assetType==2){

            if(!$interCompanyID){
                $this->session->set_flashdata('e', 'Select a inter Company' );
                return array('status' => false);
            }
            $this->db->select('interCompanyID');
            $this->db->from('srp_erp_fa_asset_disposalmaster');
            $this->db->where('interCompanyID', $interCompanyID);
            $this->db->where('companyID', $companyID);
            $query = $this->db->get();
            $isPresent = $query->row();
            if($isPresent){
                $this->session->set_flashdata('e', 'This company alredy inter connected' );
                return array('status' => false);
            }
            $data['interCompanyID'] = $interCompanyID;
        }
        else{
            $data['interCompanyID'] = null;
        }

       

        $financeyearDate = $this->db->query("SELECT * FROM `srp_erp_companyfinanceyear` WHERE `companyFinanceYearID` = '{$financeyear}' AND companyID='{$companyID}'")->row_array();
        $data['type'] = $assetType;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $company_code;
        $data['segmentID'] = trim($segment[0] ?? '');
        $data['segmentCode'] = trim($segment[1] ?? '');
        $data['companyFinanceYearID'] = $this->input->post('companyFinanceYearID');
        $data['FYBegin'] = $financeyearDate['beginingDate'];
        $data['FYEnd'] = $financeyearDate['endingDate'];
        $data['FYPeriodDateFrom'] = $period[0];
        $data['FYPeriodDateTo'] = $period[1];
        $data['documentID'] = 'ADSP';
        $data['disposalDocumentDate'] = $disposalDocumentDate;
        $narration = ($this->input->post('narration'));
        $data['narration'] = str_replace('<br />', PHP_EOL, $narration);
        //$data['narration'] = $this->input->post('narration');

        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['timestamp'] = $this->common_data['current_date'];

        if (trim($this->input->post('assetdisposalMasterAutoID') ?? '')) {

            $this->db->where('assetdisposalMasterAutoID', trim($this->input->post('assetdisposalMasterAutoID') ?? ''));
            $this->db->update('srp_erp_fa_asset_disposalmaster', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Update Failed' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Updated Successfully');
                $this->db->trans_commit();
                return array('status' => true, 'pk_id' => trim($this->input->post('assetdisposalMasterAutoID') ?? ''), 'is_reload' => false);
            }


        } else {
            $this->load->library('sequence');
            $data['disposalDocumentCode'] = $this->sequence->sequence_generator("ADSP");

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['timestamp'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_fa_asset_disposalmaster', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', "Save Failed" . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully');
                $this->db->trans_commit();
                return array('status' => true, 'pk_id' => $last_id, 'disposalDocumentCode' => $data['disposalDocumentCode'], 'is_reload' => true);
            }
        }

    }

    function add_to_disposal()
    {
        $assetdisposalMasterAutoID = $this->input->post('assetdisposalMasterAutoID');
        $faId = $this->input->post('faId');
        $disposalAmount = $this->input->post('disposalAmount');
        $companyID = current_companyID();
        $companyCode = current_companyCode();

        $exsist = $this->db->query("SELECT
IFNULL(srp_erp_fa_assetdepreciationperiods.companyLocalAmount,0) AS accLocalAmount,
 srp_erp_fa_depmaster.depCode,
 srp_erp_fa_asset_master.faID,
 srp_erp_fa_asset_master.faCode
FROM
	srp_erp_fa_asset_master


LEFT JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_asset_master.faID = srp_erp_fa_assetdepreciationperiods.faID
INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_assetdepreciationperiods.depMasterAutoID = srp_erp_fa_depmaster.depMasterAutoID

WHERE
	srp_erp_fa_asset_master.faID = {$faId}
AND srp_erp_fa_depmaster.approvedYN = '0'")->result_array();

        if(!empty($exsist)){
            return array('w', "Following Asset Depreciation documents are not approved. Please approve those documents and try again ",$exsist);
            exit;
        }

        $data = $this->db->query("SELECT

@accLocalAmount := IF (
	ISNULL(accLocalAmount),
	0,
	accLocalAmount
) AS accLocalAmount,
IF (
	ISNULL(accTransactionAmount),
	0,
	accTransactionAmount
) AS accTransactionAmount,
IF (
	ISNULL(accReportingAmount),
	0,
	accReportingAmount
) AS accReportingAmount,
 srp_erp_fa_asset_master.faID,
 srp_erp_fa_asset_master.faCode,
 srp_erp_fa_asset_master.serialNo,
 srp_erp_fa_asset_master.faUnitSerialNo,
 srp_erp_fa_asset_master.companyLocalAmount,
 srp_erp_fa_asset_master.DISPOGLAutoID,
 srp_erp_fa_asset_master.ACCDEPGLAutoID,
 srp_erp_fa_asset_master.costGLAutoID,
 DATE_FORMAT(
	srp_erp_fa_asset_master.dateAQ,
	'%Y/%m/%d'
) AS dateAQ,
 srp_erp_fa_asset_master.dateDEP,
 srp_erp_fa_asset_master.DEPpercentage,
 srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces,
 srp_erp_fa_asset_master.assetDescription,
 srp_erp_fa_asset_master.segmentID,
 srp_erp_fa_asset_master.segmentCode,
 srp_erp_fa_asset_master.DISPOGLCODE,
 srp_erp_fa_asset_master.ACCDEPGLCODE,
 srp_erp_fa_asset_master.COSTGLCODE,
 @nbv := (
	companyLocalAmount - @accLocalAmount
) nbv,

IF (ISNULL(@nbv), 0 ,@nbv) AS netBookValueLocalAmount
FROM
	srp_erp_fa_asset_master
LEFT JOIN (
	SELECT
		srp_erp_fa_assetdepreciationperiods.faID,
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) accLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) accTransactionAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) accReportingAmount,
		srp_erp_fa_assetdepreciationperiods.companyID,
		srp_erp_fa_assetdepreciationperiods.approvedYN,
		srp_erp_fa_assetdepreciationperiods.confirmedYN
	FROM
		srp_erp_fa_assetdepreciationperiods
		INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_assetdepreciationperiods.depMasterAutoID = srp_erp_fa_depmaster.depMasterAutoID
	WHERE
		 srp_erp_fa_depmaster.approvedYN = '1'
	GROUP BY
		faID
) assetdepreciationperiods ON srp_erp_fa_asset_master.faID = assetdepreciationperiods.faID
WHERE
	srp_erp_fa_asset_master.faID ={$faId}")->row_array();

        if ($data) {
            $post['assetdisposalMasterAutoID'] = $assetdisposalMasterAutoID;
            $post['companyID'] = $companyID;
            $post['companyCode'] = $companyCode;
            $post['segmentID'] = $data['segmentID'];
            $post['segmentCode'] = $data['segmentCode'];
            $post['faID'] = $data['faID'];
            $post['faCode'] = $data['faCode'];
            $post['faUnitSerialNo'] = $data['faUnitSerialNo'];
            $post['assetDescription'] = $data['assetDescription'];


            $post['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $post['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $post['companyLocalExchangeRate'] = 1;
            $post['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $post['companyLocalAmount'] = $disposalAmount;


            $post['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $post['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $post['companyReportingDecimalPlaces'] = $this->common_data['company_data']['company_reporting_decimal'];

            $reporting_currency = currency_conversion($post['companyLocalCurrency'], $post['companyReportingCurrency']);
            $post['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $post['companyReportingAmount'] = ($disposalAmount / $reporting_currency['conversion']);

            $post['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $post['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $post['transactionCurrencyExchangeRate'] = 1;
            $post['transactionCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $post['transactionAmount'] = $disposalAmount;

            $post['netBookValueLocalAmount'] = $data['netBookValueLocalAmount'];
            $post['netBookValueReportingAmount'] = $data['netBookValueLocalAmount'];
            $post['netBookValueTransactionAmount'] = ($data['netBookValueLocalAmount'] / $reporting_currency['conversion']);

            $post['depLocalAmount'] = '';
            $post['depReportingAmount'] = '';

            $post['accLocalAmount'] = $data['accLocalAmount'];
            $post['accReportingAmount'] = $data['accReportingAmount'];
            $post['accTransactionAmount'] = $data['accTransactionAmount'];

            $post['COSTGLAutoID'] = $data['costGLAutoID'];
            $post['ACCDEPGLAutoID'] = $data['ACCDEPGLAutoID'];
            $post['DISPOGLAutoID'] = $data['DISPOGLAutoID'];

            $post['createdPCID'] = $this->common_data['current_pc'];
            $post['createdUserID'] = $this->common_data['current_userID'];
            $post['createdUserName'] = $this->common_data['current_user'];
            $post['createdDateTime'] = $this->common_data['current_date'];
            $post['timestamp'] = $this->common_data['current_date'];


            $post['modifiedPCID'] = $this->common_data['current_pc'];
            $post['modifiedUserID'] = $this->common_data['current_userID'];
            $post['modifiedUserName'] = $this->common_data['current_user'];
            $post['modifiedDateTime'] = $this->common_data['current_date'];
            $post['timestamp'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_fa_asset_disposaldetail', $post);
            $last_id = $this->db->insert_id();
            $dataUpdate['selectedForDisposal'] = 1;

            $this->db->where('faID', $faId);
            $this->db->update('srp_erp_fa_asset_master', $dataUpdate);


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                //return array('status' => true, 'pk_id' => $last_id);
                return array('s', 'Asset Added to Disposal');
            }
        }
    }

    function remove_asset_from_disposal()
    {
        $assetDisposalDetailAutoID = $this->input->post('assetDisposalDetailAutoID');
        $faId = $this->input->post('faId');
        $this->db->trans_start();

        $this->db->delete('srp_erp_fa_asset_disposaldetail', array('assetDisposalDetailAutoID' => $assetDisposalDetailAutoID, 'companyID' => $this->common_data['company_data']['company_id']));

        $dataUpdate['selectedForDisposal'] = 0;

        $this->db->where('faID', $faId);
        $this->db->update('srp_erp_fa_asset_master', $dataUpdate);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Removed Assets from Disposal.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function assetDisposalConfirm()
    {
        $this->load->library('approvals');
        $pk = $this->input->post('assetdisposalMasterAutoID');
        $dismaster = $this->db->query("SELECT * FROM srp_erp_fa_asset_disposalmaster WHERE assetdisposalMasterAutoID='{$pk}'")->row_array();

        $validate_code = validate_code_duplication($dismaster['disposalDocumentCode'], 'disposalDocumentCode', $pk,'assetdisposalMasterAutoID', 'srp_erp_fa_asset_disposalmaster');
        if(!empty($validate_code)) {
            $this->session->set_flashdata('e', 'The document Code Already Exist.(' . $validate_code . ')');
            return array(false, 'error');
        }

        $approvals_status = $this->approvals->CreateApproval('ADSP', $pk, $dismaster['disposalDocumentCode'], 'Asset Management', 'srp_erp_fa_asset_disposalmaster', 'assetdisposalMasterAutoID');
        if ($approvals_status == 1) {
            $data = array('confirmedYN' => 1, 'confirmedByEmpID' => $this->common_data['current_userID'], 'confirmedDate' => $this->common_data['current_date']);
            $this->db->where('assetdisposalMasterAutoID', $pk);
            $result = $this->db->update('srp_erp_fa_asset_disposalmaster', $data);

            $this->session->set_flashdata('s', 'Confirmed Successfully');
            return array('status' => true);
        }else if($approvals_status==3){
            $this->session->set_flashdata('w', 'There are no users exist to perform approval for this document.');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Document confirmation failed.');
            return array('status' => false);
        }
    }

    function save_disposal_approval($system_code)
    {
        $assetdisposalMasterAutoID = $this->input->post('assetdisposalMasterAutoID');

        $company_id = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $current_date = current_date();
        $current_pc = $this->common_data['current_pc'];
        $current_userID = $this->common_data['current_userID'];
        $current_employee = current_employee();
        $current_user_group = current_user_group();
        $current_user = $this->common_data['current_user'];

        /*Disposal GL entries*/
        $disposalGLEntiries = $this->db->query("SELECT
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
	srp_erp_fa_asset_disposalmaster.disposalDocumentCode,
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate,
	srp_erp_fa_asset_disposalmaster.segmentID,
	srp_erp_fa_asset_disposalmaster.narration,
	srp_erp_fa_asset_disposaldetail.segmentCode,
	srp_erp_fa_asset_disposaldetail.segmentID,
	srp_erp_fa_asset_disposaldetail.companyCode,
	srp_erp_fa_asset_disposaldetail.faID,
	srp_erp_fa_asset_disposaldetail.faCode,
	srp_erp_fa_asset_disposaldetail.assetDescription,
	srp_erp_fa_asset_disposaldetail.COSTGLAutoID,
	srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID,
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyID,
	srp_erp_fa_asset_disposaldetail.transactionCurrency,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrency,
	srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrency,
	srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces,
	srp_erp_fa_asset_master.documentID,
	SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal,
	SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction,
	SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting,
	SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount,
	SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount,
	SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount,
	srp_erp_fa_asset_disposalmaster.approvedbyEmpName,
    srp_erp_fa_asset_disposalmaster.approvedbyEmpID,
    srp_erp_fa_asset_disposalmaster.approvedDate,
    srp_erp_fa_asset_disposalmaster.confirmedDate,
    srp_erp_fa_asset_disposalmaster.confirmedByName,
    srp_erp_fa_asset_disposalmaster.confirmedByEmpID
FROM
	srp_erp_fa_asset_disposalmaster
INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN (
	SELECT
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	WHERE
		srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}'
	GROUP BY
		faID
) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID
WHERE
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}'
GROUP BY
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_master.segmentID")->result_array();

        foreach ($disposalGLEntiries as $disposalGLEntiry) {
            /*1. GL entries Asset Disposal GL Debit -- Asset Cost Amount*/
            $DISPOGLAutoID = fetch_gl_account_desc($disposalGLEntiry['DISPOGLAutoID']);
            $assetDisposalGLDr['documentCode'] = 'ADSP';
            $assetDisposalGLDr['documentMasterAutoID'] = $disposalGLEntiry['assetdisposalMasterAutoID'];
            $assetDisposalGLDr['documentSystemCode'] = $disposalGLEntiry['disposalDocumentCode'];
            $assetDisposalGLDr['documentDetailAutoID'] = 0;
            $assetDisposalGLDr['documentNarration'] = $disposalGLEntiry['narration'];
            $assetDisposalGLDr['GLAutoID'] = $disposalGLEntiry['DISPOGLAutoID'];
            $assetDisposalGLDr['systemGLCode'] = $DISPOGLAutoID['systemAccountCode'];
            $assetDisposalGLDr['GLCode'] = $DISPOGLAutoID['GLSecondaryCode'];
            $assetDisposalGLDr['GLDescription'] = $DISPOGLAutoID['GLDescription'];
            $assetDisposalGLDr['GLType'] = $DISPOGLAutoID['subCategory'];
            $assetDisposalGLDr['amount_type'] = 'dr';

            $assetDisposalGLDr['companyLocalCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDr['companyLocalCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDr['companyLocalExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDr['companyLocalCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDr['companyReportingCurrency'] = $disposalGLEntiry['companyReportingCurrency'];
            $assetDisposalGLDr['companyReportingCurrencyID'] = $disposalGLEntiry['companyReportingCurrencyID'];

            $assetDisposalGLDr['companyReportingExchangeRate'] = $disposalGLEntiry['companyReportingExchangeRate'];
            $assetDisposalGLDr['companyReportingCurrencyDecimalPlaces'] = $disposalGLEntiry['companyReportingDecimalPlaces'];
            $assetDisposalGLDr['companyLocalAmount'] = round($disposalGLEntiry['assetCostLocal'], $assetDisposalGLDr['companyLocalCurrencyDecimalPlaces']);
            $assetDisposalGLDr['companyReportingAmount'] = round(($disposalGLEntiry['assetCostLocal'] / $disposalGLEntiry['companyReportingExchangeRate']), $assetDisposalGLDr['companyReportingCurrencyDecimalPlaces']);

            $assetDisposalGLDr['transactionCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDr['transactionCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDr['transactionExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDr['transactionCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDr['transactionAmount'] = round($disposalGLEntiry['assetCostLocal'], $assetDisposalGLDr['transactionCurrencyDecimalPlaces']);

            $assetDisposalGLDr['documentDate'] = $disposalGLEntiry['disposalDocumentDate'];
            $assetDisposalGLDr['documentYear'] = date('Y', strtotime($disposalGLEntiry['disposalDocumentDate']));
            $assetDisposalGLDr['documentMonth'] = date('m', strtotime($disposalGLEntiry['disposalDocumentDate']));

            $assetDisposalGLDr['companyID'] = $company_id;
            $assetDisposalGLDr['companyCode'] = $company_code;
            $assetDisposalGLDr['createdUserGroup'] = $current_user_group;

            $assetDisposalGLDr['createdPCID'] = $current_pc;
            $assetDisposalGLDr['createdUserID'] = $current_userID;
            $assetDisposalGLDr['createdUserName'] = $current_user;
            $assetDisposalGLDr['createdDateTime'] = $current_date;
            $assetDisposalGLDr['timestamp'] = $current_date;

            $assetDisposalGLDr['segmentID'] = $disposalGLEntiry['segmentID'];
            $assetDisposalGLDr['segmentCode'] = $disposalGLEntiry['segmentCode'];

            $assetDisposalGLDr['confirmedByEmpID'] = $disposalGLEntiry['confirmedByEmpID'];
            $assetDisposalGLDr['confirmedByName'] = $disposalGLEntiry['confirmedByName'];
            $assetDisposalGLDr['confirmedDate'] = $disposalGLEntiry['confirmedDate'];
            $assetDisposalGLDr['approvedDate'] = $disposalGLEntiry['approvedDate'];
            $assetDisposalGLDr['approvedbyEmpID'] = $disposalGLEntiry['approvedbyEmpID'];
            $assetDisposalGLDr['approvedbyEmpName'] = $disposalGLEntiry['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $assetDisposalGLDr);

            /*2 .GL entries Asset Disposal GL Credit -- Acc Dep Amount*/
            $assetDisposalGLDEPAmountCr['documentCode'] = 'ADSP';
            $assetDisposalGLDEPAmountCr['documentMasterAutoID'] = $disposalGLEntiry['assetdisposalMasterAutoID'];
            $assetDisposalGLDEPAmountCr['documentSystemCode'] = $disposalGLEntiry['disposalDocumentCode'];
            $assetDisposalGLDEPAmountCr['documentDetailAutoID'] = 0;
            $assetDisposalGLDEPAmountCr['documentNarration'] = $disposalGLEntiry['narration'];
            $assetDisposalGLDEPAmountCr['GLAutoID'] = $disposalGLEntiry['DISPOGLAutoID'];
            $assetDisposalGLDEPAmountCr['systemGLCode'] = $DISPOGLAutoID['systemAccountCode'];
            $assetDisposalGLDEPAmountCr['GLCode'] = $DISPOGLAutoID['GLSecondaryCode'];
            $assetDisposalGLDEPAmountCr['GLDescription'] = $DISPOGLAutoID['GLDescription'];
            $assetDisposalGLDEPAmountCr['GLType'] = $DISPOGLAutoID['subCategory'];
            $assetDisposalGLDEPAmountCr['amount_type'] = 'cr';

            $assetDisposalGLDEPAmountCr['companyLocalCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDEPAmountCr['companyLocalCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDEPAmountCr['companyLocalExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDEPAmountCr['companyLocalCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDEPAmountCr['companyReportingCurrency'] = $disposalGLEntiry['companyReportingCurrency'];
            $assetDisposalGLDEPAmountCr['companyReportingCurrencyID'] = $disposalGLEntiry['companyReportingCurrencyID'];


            $assetDisposalGLDEPAmountCr['companyReportingExchangeRate'] = $disposalGLEntiry['companyReportingExchangeRate'];
            $assetDisposalGLDEPAmountCr['companyReportingCurrencyDecimalPlaces'] = $disposalGLEntiry['companyReportingDecimalPlaces'];
            $assetDisposalGLDEPAmountCr['companyLocalAmount'] = round(($disposalGLEntiry['accDepcompanyLocalAmount'] * -1), $assetDisposalGLDEPAmountCr['companyLocalCurrencyDecimalPlaces']);
            $assetDisposalGLDEPAmountCr['companyReportingAmount'] = round((($disposalGLEntiry['accDepcompanyLocalAmount'] / $assetDisposalGLDEPAmountCr['companyReportingExchangeRate']) * -1), $assetDisposalGLDEPAmountCr['companyReportingCurrencyDecimalPlaces']);

            $assetDisposalGLDEPAmountCr['transactionCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDEPAmountCr['transactionCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDEPAmountCr['transactionExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDEPAmountCr['transactionCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDEPAmountCr['transactionAmount'] = round(($disposalGLEntiry['accDepcompanyLocalAmount'] * -1), $assetDisposalGLDEPAmountCr['transactionCurrencyDecimalPlaces']);

            $assetDisposalGLDEPAmountCr['documentDate'] = $disposalGLEntiry['disposalDocumentDate'];
            $assetDisposalGLDEPAmountCr['documentYear'] = date('Y', strtotime($disposalGLEntiry['disposalDocumentDate']));
            $assetDisposalGLDEPAmountCr['documentMonth'] = date('m', strtotime($disposalGLEntiry['disposalDocumentDate']));


            $assetDisposalGLDEPAmountCr['companyID'] = $company_id;
            $assetDisposalGLDEPAmountCr['companyCode'] = $company_code;
            $assetDisposalGLDEPAmountCr['createdUserGroup'] = $current_user_group;

            $assetDisposalGLDEPAmountCr['createdPCID'] = $current_pc;
            $assetDisposalGLDEPAmountCr['createdUserID'] = $current_userID;
            $assetDisposalGLDEPAmountCr['createdUserName'] = $current_user;
            $assetDisposalGLDEPAmountCr['createdDateTime'] = $current_date;
            $assetDisposalGLDEPAmountCr['timestamp'] = $current_date;

            $assetDisposalGLDEPAmountCr['segmentID'] = $disposalGLEntiry['segmentID'];
            $assetDisposalGLDEPAmountCr['segmentCode'] = $disposalGLEntiry['segmentCode'];

            $assetDisposalGLDEPAmountCr['confirmedByEmpID'] = $disposalGLEntiry['confirmedByEmpID'];
            $assetDisposalGLDEPAmountCr['confirmedByName'] = $disposalGLEntiry['confirmedByName'];
            $assetDisposalGLDEPAmountCr['confirmedDate'] = $disposalGLEntiry['confirmedDate'];
            $assetDisposalGLDEPAmountCr['approvedDate'] = $disposalGLEntiry['approvedDate'];
            $assetDisposalGLDEPAmountCr['approvedbyEmpID'] = $disposalGLEntiry['approvedbyEmpID'];
            $assetDisposalGLDEPAmountCr['approvedbyEmpName'] = $disposalGLEntiry['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $assetDisposalGLDEPAmountCr);

            /*3 .GL entries Asset Disposal GL Credit -- Disposal Amount*/
            $assetDisposalGLDiPAmountCr['documentCode'] = 'ADSP';
            $assetDisposalGLDiPAmountCr['documentMasterAutoID'] = $disposalGLEntiry['assetdisposalMasterAutoID'];
            $assetDisposalGLDiPAmountCr['documentSystemCode'] = $disposalGLEntiry['disposalDocumentCode'];
            $assetDisposalGLDiPAmountCr['documentDetailAutoID'] = 0;
            $assetDisposalGLDiPAmountCr['documentNarration'] = $disposalGLEntiry['narration'];
            $assetDisposalGLDiPAmountCr['GLAutoID'] = $disposalGLEntiry['DISPOGLAutoID'];
            $assetDisposalGLDiPAmountCr['systemGLCode'] = $DISPOGLAutoID['systemAccountCode'];
            $assetDisposalGLDiPAmountCr['GLCode'] = $DISPOGLAutoID['GLSecondaryCode'];
            $assetDisposalGLDiPAmountCr['GLDescription'] = $DISPOGLAutoID['GLDescription'];
            $assetDisposalGLDiPAmountCr['GLType'] = $DISPOGLAutoID['subCategory'];
            $assetDisposalGLDiPAmountCr['amount_type'] = 'cr';

            $assetDisposalGLDiPAmountCr['companyLocalCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDiPAmountCr['companyLocalCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDiPAmountCr['companyLocalExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDiPAmountCr['companyLocalCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDiPAmountCr['companyReportingCurrency'] = $disposalGLEntiry['companyReportingCurrency'];
            $assetDisposalGLDiPAmountCr['companyReportingCurrencyID'] = $disposalGLEntiry['companyReportingCurrencyID'];


            $assetDisposalGLDiPAmountCr['companyReportingExchangeRate'] = $disposalGLEntiry['companyReportingExchangeRate'];
            $assetDisposalGLDiPAmountCr['companyReportingCurrencyDecimalPlaces'] = $disposalGLEntiry['companyReportingDecimalPlaces'];
            $assetDisposalGLDiPAmountCr['companyLocalAmount'] = round(($disposalGLEntiry['companyLocalAmount'] * -1), $assetDisposalGLDiPAmountCr['companyLocalCurrencyDecimalPlaces']);
            $assetDisposalGLDiPAmountCr['companyReportingAmount'] = round((($disposalGLEntiry['companyLocalAmount'] / $assetDisposalGLDiPAmountCr['companyReportingExchangeRate']) * -1), $assetDisposalGLDiPAmountCr['companyReportingCurrencyDecimalPlaces']);

            $assetDisposalGLDiPAmountCr['transactionCurrency'] = $disposalGLEntiry['companyLocalCurrency'];
            $assetDisposalGLDiPAmountCr['transactionCurrencyID'] = $disposalGLEntiry['companyLocalCurrencyID'];
            $assetDisposalGLDiPAmountCr['transactionExchangeRate'] = $disposalGLEntiry['companyLocalExchangeRate'];
            $assetDisposalGLDiPAmountCr['transactionCurrencyDecimalPlaces'] = $disposalGLEntiry['companyLocalCurrencyDecimalPlaces'];
            $assetDisposalGLDiPAmountCr['transactionAmount'] = round(($disposalGLEntiry['companyLocalAmount'] * -1), $assetDisposalGLDiPAmountCr['transactionCurrencyDecimalPlaces']);

            $assetDisposalGLDiPAmountCr['documentDate'] = $disposalGLEntiry['disposalDocumentDate'];
            $assetDisposalGLDiPAmountCr['documentYear'] = date('Y', strtotime($disposalGLEntiry['disposalDocumentDate']));
            $assetDisposalGLDiPAmountCr['documentMonth'] = date('m', strtotime($disposalGLEntiry['disposalDocumentDate']));

            $assetDisposalGLDiPAmountCr['companyID'] = $company_id;
            $assetDisposalGLDiPAmountCr['companyCode'] = $company_code;
            $assetDisposalGLDiPAmountCr['createdUserGroup'] = $current_user_group;

            $assetDisposalGLDiPAmountCr['createdPCID'] = $current_pc;
            $assetDisposalGLDiPAmountCr['createdUserID'] = $current_userID;
            $assetDisposalGLDiPAmountCr['createdUserName'] = $current_user;
            $assetDisposalGLDiPAmountCr['createdDateTime'] = $current_date;
            $assetDisposalGLDiPAmountCr['timestamp'] = $current_date;

            $assetDisposalGLDiPAmountCr['segmentID'] = $disposalGLEntiry['segmentID'];
            $assetDisposalGLDiPAmountCr['segmentCode'] = $disposalGLEntiry['segmentCode'];

            $assetDisposalGLDiPAmountCr['confirmedByEmpID'] = $disposalGLEntiry['confirmedByEmpID'];
            $assetDisposalGLDiPAmountCr['confirmedByName'] = $disposalGLEntiry['confirmedByName'];
            $assetDisposalGLDiPAmountCr['confirmedDate'] = $disposalGLEntiry['confirmedDate'];
            $assetDisposalGLDiPAmountCr['approvedDate'] = $disposalGLEntiry['approvedDate'];
            $assetDisposalGLDiPAmountCr['approvedbyEmpID'] = $disposalGLEntiry['approvedbyEmpID'];
            $assetDisposalGLDiPAmountCr['approvedbyEmpName'] = $disposalGLEntiry['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $assetDisposalGLDiPAmountCr);
        }


        /**/
        $datas = $this->db->query("SELECT
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
	srp_erp_fa_asset_disposalmaster.disposalDocumentCode,
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate,
	srp_erp_fa_asset_disposalmaster.segmentID,
	srp_erp_fa_asset_disposalmaster.narration,
	srp_erp_fa_asset_disposaldetail.segmentCode,
	srp_erp_fa_asset_disposaldetail.segmentID,
	srp_erp_fa_asset_disposaldetail.companyCode,
	srp_erp_fa_asset_disposaldetail.faID,
	srp_erp_fa_asset_disposaldetail.faCode,
	srp_erp_fa_asset_disposaldetail.assetDescription,
	srp_erp_fa_asset_disposaldetail.COSTGLAutoID,
	srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID,
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_disposaldetail.transactionCurrency,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyID,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrency,
	srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrency,
	srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces,
	srp_erp_fa_asset_master.documentID,
	SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal,
	SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction,
	SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting,
	SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount,
	SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount,
	SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount,
	srp_erp_fa_asset_disposalmaster.approvedbyEmpName,
    srp_erp_fa_asset_disposalmaster.approvedbyEmpID,
    srp_erp_fa_asset_disposalmaster.approvedDate,
    srp_erp_fa_asset_disposalmaster.confirmedDate,
    srp_erp_fa_asset_disposalmaster.confirmedByName,
    srp_erp_fa_asset_disposalmaster.confirmedByEmpID
FROM
	srp_erp_fa_asset_disposalmaster
INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN (
	SELECT
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	WHERE
		srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}'
	GROUP BY
		faID
) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID
WHERE
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_disposaldetail.COSTGLAutoID")->result_array();

        foreach ($datas as $data) {
            /*1. GL entries Cost GL - Cost Amount*/
            $COSTGLAutoID = fetch_gl_account_desc($data['COSTGLAutoID']);
            $assetCostGLCr['documentCode'] = 'ADSP';
            $assetCostGLCr['documentMasterAutoID'] = $data['assetdisposalMasterAutoID'];
            $assetCostGLCr['documentSystemCode'] = $data['disposalDocumentCode'];
            $assetCostGLCr['documentDetailAutoID'] = 0;
            $assetCostGLCr['documentNarration'] = $data['narration'];
            $assetCostGLCr['GLAutoID'] = $data['COSTGLAutoID'];
            $assetCostGLCr['systemGLCode'] = $COSTGLAutoID['systemAccountCode'];
            $assetCostGLCr['GLCode'] = $COSTGLAutoID['GLSecondaryCode'];
            $assetCostGLCr['GLDescription'] = $COSTGLAutoID['GLDescription'];
            $assetCostGLCr['GLType'] = $COSTGLAutoID['subCategory'];
            $assetCostGLCr['amount_type'] = 'cr';

            $assetCostGLCr['companyLocalCurrencyID'] = $data['companyLocalCurrencyID'];
            $assetCostGLCr['companyLocalCurrency'] = $data['companyLocalCurrency'];
            $assetCostGLCr['companyLocalExchangeRate'] = $data['companyLocalExchangeRate'];
            $assetCostGLCr['companyLocalCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
            $assetCostGLCr['companyReportingCurrency'] = $data['companyReportingCurrency'];
            $assetCostGLCr['companyReportingCurrencyID'] = $data['companyReportingCurrencyID'];


            $assetCostGLCr['companyReportingExchangeRate'] = $data['companyReportingExchangeRate'];
            $assetCostGLCr['companyReportingCurrencyDecimalPlaces'] = $data['companyReportingDecimalPlaces'];
            $assetCostGLCr['companyLocalAmount'] = round(($data['assetCostLocal'] * -1), $assetCostGLCr['companyLocalCurrencyDecimalPlaces']);
            $assetCostGLCr['companyReportingAmount'] = round((($data['assetCostLocal'] / $assetCostGLCr['companyReportingExchangeRate']) * -1), $assetCostGLCr['companyReportingCurrencyDecimalPlaces']);

            $assetCostGLCr['transactionCurrencyID'] = $data['companyLocalCurrencyID'];
            $assetCostGLCr['transactionCurrency'] = $data['companyLocalCurrency'];
            $assetCostGLCr['transactionExchangeRate'] = $data['companyLocalExchangeRate'];
            $assetCostGLCr['transactionCurrencyDecimalPlaces'] = $data['companyLocalCurrencyDecimalPlaces'];
            $assetCostGLCr['transactionAmount'] = round(($data['assetCostLocal'] * -1), $assetCostGLCr['transactionCurrencyDecimalPlaces']);

            $assetCostGLCr['documentDate'] = $data['disposalDocumentDate'];
            $assetCostGLCr['documentYear'] = date('Y', strtotime($data['disposalDocumentDate']));
            $assetCostGLCr['documentMonth'] = date('m', strtotime($data['disposalDocumentDate']));

            $assetCostGLCr['companyID'] = $company_id;
            $assetCostGLCr['companyCode'] = $company_code;
            $assetCostGLCr['createdUserGroup'] = $current_user_group;

            $assetCostGLCr['createdPCID'] = $current_pc;
            $assetCostGLCr['createdUserID'] = $current_userID;
            $assetCostGLCr['createdUserName'] = $current_user;
            $assetCostGLCr['createdDateTime'] = $current_date;
            $assetCostGLCr['timestamp'] = $current_date;

            $assetCostGLCr['segmentID'] = $data['segmentID'];
            $assetCostGLCr['segmentCode'] = $data['segmentCode'];

            $assetCostGLCr['confirmedByEmpID'] = $data['confirmedByEmpID'];
            $assetCostGLCr['confirmedByName'] = $data['confirmedByName'];
            $assetCostGLCr['confirmedDate'] = $data['confirmedDate'];
            $assetCostGLCr['approvedDate'] = $data['approvedDate'];
            $assetCostGLCr['approvedbyEmpID'] = $data['approvedbyEmpID'];
            $assetCostGLCr['approvedbyEmpName'] = $data['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $assetCostGLCr);

        }


        /**/
        $items = $this->db->query("SELECT
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
	srp_erp_fa_asset_disposalmaster.disposalDocumentCode,
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate,
	srp_erp_fa_asset_disposalmaster.segmentID,
	srp_erp_fa_asset_disposalmaster.narration,
	srp_erp_fa_asset_disposaldetail.segmentCode,
	srp_erp_fa_asset_disposaldetail.segmentID,
	srp_erp_fa_asset_disposaldetail.companyCode,
	srp_erp_fa_asset_disposaldetail.faID,
	srp_erp_fa_asset_disposaldetail.faCode,
	srp_erp_fa_asset_disposaldetail.assetDescription,
	srp_erp_fa_asset_disposaldetail.COSTGLAutoID,
	srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID,
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyID,
	srp_erp_fa_asset_disposaldetail.transactionCurrency,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrency,
	srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrency,
	srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces,
	srp_erp_fa_asset_master.documentID,
	SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal,
	SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction,
	SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting,
	SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount,
	SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount,
	SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount,
	srp_erp_fa_asset_disposalmaster.approvedbyEmpName,
    srp_erp_fa_asset_disposalmaster.approvedbyEmpID,
    srp_erp_fa_asset_disposalmaster.approvedDate,
    srp_erp_fa_asset_disposalmaster.confirmedDate,
    srp_erp_fa_asset_disposalmaster.confirmedByName,
    srp_erp_fa_asset_disposalmaster.confirmedByEmpID
FROM
	srp_erp_fa_asset_disposalmaster
INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN (
	SELECT
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	WHERE
		srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}'
	GROUP BY
		faID
) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID
WHERE
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID")->result_array();

        foreach ($items as $item) {
            /*3. GL entries Asset Acq GL Debit*/
            $ACCDEPGLAutoID = fetch_gl_account_desc($item['ACCDEPGLAutoID']);
            $assetAcqGLDR['documentCode'] = 'ADSP';
            $assetAcqGLDR['documentMasterAutoID'] = $item['assetdisposalMasterAutoID'];
            $assetAcqGLDR['documentSystemCode'] = $item['disposalDocumentCode'];
            $assetAcqGLDR['documentDetailAutoID'] = 0;
            $assetAcqGLDR['documentNarration'] = $item['narration'];
            $assetAcqGLDR['GLAutoID'] = $item['ACCDEPGLAutoID'];
            $assetAcqGLDR['systemGLCode'] = $ACCDEPGLAutoID['systemAccountCode'];
            $assetAcqGLDR['GLCode'] = $ACCDEPGLAutoID['GLSecondaryCode'];
            $assetAcqGLDR['GLDescription'] = $ACCDEPGLAutoID['GLDescription'];
            $assetAcqGLDR['GLType'] = $ACCDEPGLAutoID['subCategory'];
            $assetAcqGLDR['amount_type'] = 'dr';

            $assetAcqGLDR['companyLocalCurrencyID'] = $item['companyLocalCurrencyID'];
            $assetAcqGLDR['companyLocalCurrency'] = $item['companyLocalCurrency'];
            $assetAcqGLDR['companyLocalExchangeRate'] = $item['companyLocalExchangeRate'];
            $assetAcqGLDR['companyLocalCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces'];
            $assetAcqGLDR['companyReportingCurrency'] = $item['companyReportingCurrency'];
            $assetAcqGLDR['companyReportingCurrencyID'] = $item['companyReportingCurrencyID'];


            $assetAcqGLDR['companyReportingExchangeRate'] = $item['companyReportingExchangeRate'];
            $assetAcqGLDR['companyReportingCurrencyDecimalPlaces'] = $item['companyReportingDecimalPlaces'];
            $assetAcqGLDR['companyLocalAmount'] = round($item['accDepcompanyLocalAmount'], $assetAcqGLDR['companyLocalCurrencyDecimalPlaces']);
            $assetAcqGLDR['companyReportingAmount'] = round(($item['accDepcompanyLocalAmount'] / $assetAcqGLDR['companyReportingExchangeRate']), $assetAcqGLDR['companyReportingCurrencyDecimalPlaces']);

            $assetAcqGLDR['transactionCurrency'] = $item['companyLocalCurrency'];
            $assetAcqGLDR['transactionCurrencyID'] = $item['companyLocalCurrencyID'];
            $assetAcqGLDR['transactionExchangeRate'] = $item['companyLocalExchangeRate'];
            $assetAcqGLDR['transactionCurrencyDecimalPlaces'] = $item['companyLocalCurrencyDecimalPlaces'];
            $assetAcqGLDR['transactionAmount'] = round($item['accDepcompanyLocalAmount'], $assetAcqGLDR['transactionCurrencyDecimalPlaces']);

            $assetAcqGLDR['documentDate'] = $item['disposalDocumentDate'];
            $assetAcqGLDR['documentYear'] = date('Y', strtotime($item['disposalDocumentDate']));
            $assetAcqGLDR['documentMonth'] = date('m', strtotime($item['disposalDocumentDate']));

            $assetAcqGLDR['companyID'] = $company_id;
            $assetAcqGLDR['companyCode'] = $company_code;
            $assetAcqGLDR['createdUserGroup'] = $current_user_group;

            $assetAcqGLDR['createdPCID'] = $current_pc;
            $assetAcqGLDR['createdUserID'] = $current_userID;
            $assetAcqGLDR['createdUserName'] = $current_user;
            $assetAcqGLDR['createdDateTime'] = $current_date;
            $assetAcqGLDR['timestamp'] = $current_date;

            $assetAcqGLDR['segmentID'] = $item['segmentID'];
            $assetAcqGLDR['segmentCode'] = $item['segmentCode'];

            $assetAcqGLDR['confirmedByEmpID'] = $item['confirmedByEmpID'];
            $assetAcqGLDR['confirmedByName'] = $item['confirmedByName'];
            $assetAcqGLDR['confirmedDate'] = $item['confirmedDate'];
            $assetAcqGLDR['approvedDate'] = $item['approvedDate'];
            $assetAcqGLDR['approvedbyEmpID'] = $item['approvedbyEmpID'];
            $assetAcqGLDR['approvedbyEmpName'] = $item['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $assetAcqGLDR);
        }


        /**/
        $banks = $this->db->query("SELECT
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
	srp_erp_fa_asset_disposalmaster.disposalDocumentCode,
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate,
	srp_erp_fa_asset_disposalmaster.segmentID,
	srp_erp_fa_asset_disposalmaster.narration,
	srp_erp_fa_asset_disposaldetail.segmentCode,
	srp_erp_fa_asset_disposaldetail.segmentID,
	srp_erp_fa_asset_disposaldetail.companyCode,
	srp_erp_fa_asset_disposaldetail.faID,
	srp_erp_fa_asset_disposaldetail.faCode,
	srp_erp_fa_asset_disposaldetail.assetDescription,
	srp_erp_fa_asset_disposaldetail.COSTGLAutoID,
	srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID,
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_disposaldetail.transactionCurrency,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyID,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrency,
	srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrency,
	srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces,
	srp_erp_fa_asset_master.documentID,
	SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal,
	SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction,
	SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting,
	SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount,
	SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount,
	SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount,
	srp_erp_fa_asset_disposalmaster.approvedbyEmpName,
    srp_erp_fa_asset_disposalmaster.approvedbyEmpID,
    srp_erp_fa_asset_disposalmaster.approvedDate,
    srp_erp_fa_asset_disposalmaster.confirmedDate,
    srp_erp_fa_asset_disposalmaster.confirmedByName,
    srp_erp_fa_asset_disposalmaster.confirmedByEmpID
FROM
	srp_erp_fa_asset_disposalmaster
INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN (
	SELECT
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	WHERE
		srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}'
	GROUP BY
		faID
) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID
WHERE
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}'")->result_array();

        foreach ($banks as $bank) {
            /*5. GL entries Bank GL Debit*/
            $bankAccGL = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE GLAutoID = ( SELECT GLAutoID FROM srp_erp_companycontrolaccounts WHERE controlAccountType = 'ADSP' AND companyID = '{$company_id}' )")->row_array();
            $bankAccGLDr['documentCode'] = 'ADSP';
            $bankAccGLDr['documentMasterAutoID'] = $bank['assetdisposalMasterAutoID'];
            $bankAccGLDr['documentSystemCode'] = $bank['disposalDocumentCode'];
            $bankAccGLDr['documentDetailAutoID'] = 0;
            $bankAccGLDr['documentNarration'] = $bank['narration'];
            $bankAccGLDr['GLAutoID'] = $bankAccGL['GLAutoID'];
            $bankAccGLDr['systemGLCode'] = $bankAccGL['systemAccountCode'];
            $bankAccGLDr['GLCode'] = $bankAccGL['GLSecondaryCode'];
            $bankAccGLDr['GLDescription'] = $bankAccGL['GLDescription'];
            $bankAccGLDr['GLType'] = $bankAccGL['subCategory'];
            $bankAccGLDr['amount_type'] = 'dr';

            $bankAccGLDr['companyLocalCurrency'] = $bank['companyLocalCurrency'];
            $bankAccGLDr['companyLocalCurrencyID'] = $bank['companyLocalCurrencyID'];
            $bankAccGLDr['companyLocalExchangeRate'] = $bank['companyLocalExchangeRate'];
            $bankAccGLDr['companyLocalCurrencyDecimalPlaces'] = $bank['companyLocalCurrencyDecimalPlaces'];
            $bankAccGLDr['companyReportingCurrency'] = $bank['companyReportingCurrency'];
            $bankAccGLDr['companyReportingCurrencyID'] = $bank['companyReportingCurrencyID'];


            $bankAccGLDr['companyReportingExchangeRate'] = $bank['companyReportingExchangeRate'];
            $bankAccGLDr['companyReportingCurrencyDecimalPlaces'] = $bank['companyReportingDecimalPlaces'];
            $bankAccGLDr['companyLocalAmount'] = round($bank['companyLocalAmount'], $bankAccGLDr['companyLocalCurrencyDecimalPlaces']);
            $bankAccGLDr['companyReportingAmount'] = round(($bank['companyLocalAmount'] / $bankAccGLDr['companyReportingExchangeRate']), $bankAccGLDr['companyLocalCurrencyDecimalPlaces']);

            $bankAccGLDr['transactionCurrencyID'] = $bank['companyLocalCurrencyID'];
            $bankAccGLDr['transactionCurrency'] = $bank['companyLocalCurrency'];
            $bankAccGLDr['transactionExchangeRate'] = $bank['companyLocalExchangeRate'];
            $bankAccGLDr['transactionCurrencyDecimalPlaces'] = $bank['companyLocalCurrencyDecimalPlaces'];
            $bankAccGLDr['transactionAmount'] = round($bank['companyLocalAmount'], $bankAccGLDr['transactionCurrencyDecimalPlaces']);


            $bankAccGLDr['documentDate'] = $bank['disposalDocumentDate'];
            $bankAccGLDr['documentYear'] = date('Y', strtotime($bank['disposalDocumentDate']));
            $bankAccGLDr['documentMonth'] = date('m', strtotime($bank['disposalDocumentDate']));

            $bankAccGLDr['companyID'] = $company_id;
            $bankAccGLDr['companyCode'] = $company_code;
            $bankAccGLDr['createdUserGroup'] = $current_user_group;

            $bankAccGLDr['createdPCID'] = $current_pc;
            $bankAccGLDr['createdUserID'] = $current_userID;
            $bankAccGLDr['createdUserName'] = $current_user;
            $bankAccGLDr['createdDateTime'] = $current_date;
            $bankAccGLDr['timestamp'] = $current_date;

            $bankAccGLDr['segmentID'] = $bank['segmentID'];
            $bankAccGLDr['segmentCode'] = $bank['segmentCode'];

            $bankAccGLDr['confirmedByEmpID'] = $bank['confirmedByEmpID'];
            $bankAccGLDr['confirmedByName'] = $bank['confirmedByName'];
            $bankAccGLDr['confirmedDate'] = $bank['confirmedDate'];
            $bankAccGLDr['approvedDate'] = $bank['approvedDate'];
            $bankAccGLDr['approvedbyEmpID'] = $bank['approvedbyEmpID'];
            $bankAccGLDr['approvedbyEmpName'] = $bank['approvedbyEmpName'];

            $this->db->insert('srp_erp_generalledger', $bankAccGLDr);
        }

        /**/
        $assets = $this->db->query("SELECT
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
	srp_erp_fa_asset_disposalmaster.disposalDocumentCode,
	srp_erp_fa_asset_disposalmaster.disposalDocumentDate,
	srp_erp_fa_asset_disposalmaster.segmentID,
	srp_erp_fa_asset_disposalmaster.narration,
	srp_erp_fa_asset_disposaldetail.segmentCode,
	srp_erp_fa_asset_disposaldetail.segmentID,
	srp_erp_fa_asset_disposaldetail.companyCode,
	srp_erp_fa_asset_disposaldetail.faID,
	srp_erp_fa_asset_disposaldetail.faCode,
	srp_erp_fa_asset_disposaldetail.assetDescription,
	srp_erp_fa_asset_disposaldetail.COSTGLAutoID,
	srp_erp_fa_asset_disposaldetail.ACCDEPGLAutoID,
	srp_erp_fa_asset_disposaldetail.DISPOGLAutoID,
	srp_erp_fa_asset_disposaldetail.transactionCurrency,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyID,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.transactionAmount) AS transactionAmount,
	srp_erp_fa_asset_disposaldetail.transactionCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrency,
	srp_erp_fa_asset_disposaldetail.companyLocalExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_asset_disposaldetail.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrencyID,
	srp_erp_fa_asset_disposaldetail.companyReportingCurrency,
	srp_erp_fa_asset_disposaldetail.companyReportingExchangeRate,
	SUM(srp_erp_fa_asset_disposaldetail.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_asset_disposaldetail.companyReportingDecimalPlaces,
	srp_erp_fa_asset_master.documentID,
	SUM(srp_erp_fa_asset_master.companyLocalAmount) AS assetCostLocal,
	SUM(srp_erp_fa_asset_master.transactionAmount) AS assetCostTransaction,
	SUM(srp_erp_fa_asset_master.companyReportingAmount) AS assetCostReporting,
	SUM(assetdepreciationperiods_tbl.accDepcompanyLocalAmount) AS accDepcompanyLocalAmount,
	SUM(assetdepreciationperiods_tbl.accDepcompanyReportingAmount) AS accDepcompanyReportingAmount,
	SUM(assetdepreciationperiods_tbl.accDeptransactionAmount) AS accDeptransactionAmount,
	srp_erp_fa_asset_disposalmaster.approvedbyEmpName,
    srp_erp_fa_asset_disposalmaster.approvedbyEmpID,
    srp_erp_fa_asset_disposalmaster.approvedDate,
    srp_erp_fa_asset_disposalmaster.confirmedDate,
    srp_erp_fa_asset_disposalmaster.confirmedByName,
    srp_erp_fa_asset_disposalmaster.confirmedByEmpID
FROM
	srp_erp_fa_asset_disposalmaster
INNER JOIN srp_erp_fa_asset_disposaldetail ON srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = srp_erp_fa_asset_disposaldetail.assetdisposalMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_asset_disposaldetail.faID = srp_erp_fa_asset_master.faID
LEFT JOIN (
	SELECT
		SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS accDepcompanyLocalAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS accDepcompanyReportingAmount,
		SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS accDeptransactionAmount,
		srp_erp_fa_assetdepreciationperiods.depMasterAutoID,
		srp_erp_fa_assetdepreciationperiods.faMainCategory,
		srp_erp_fa_assetdepreciationperiods.faSubCategory,
		srp_erp_fa_assetdepreciationperiods.faID
	FROM
		srp_erp_fa_assetdepreciationperiods
	WHERE
		srp_erp_fa_assetdepreciationperiods.companyID = '{$company_id}'
	GROUP BY
		faID
) assetdepreciationperiods_tbl ON srp_erp_fa_asset_master.faID = assetdepreciationperiods_tbl.faID
WHERE
	srp_erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = '{$assetdisposalMasterAutoID}' GROUP BY srp_erp_fa_asset_master.faID")->result_array();


        foreach ($assets as $asset) {
            /*Asset Master update*/
            $dataMaster['disposed'] = 1;
            $dataMaster['disposedDate'] = $asset['disposalDocumentDate'];
            $dataMaster['assetdisposalMasterAutoID'] = $asset['assetdisposalMasterAutoID'];
            $dataMaster['reasonDisposed'] = $asset['narration'];
            $dataMaster['cashDisposal'] = 0;
            $dataMaster['costAtDisposal'] = 0;
            $dataMaster['profitLossDisposal'] = 0;

            $this->db->where('faID', $asset['faID']);
            $this->db->update('srp_erp_fa_asset_master', $dataMaster);
        }
         json_encode(array('a' => $this->db));
        return;

    }

    function deleteDisposal()
    {
        $assetdisposalMasterAutoID = $this->input->post('assetdisposalMasterAutoID');

        $Assets = $this->db->query("SELECT faID FROM `srp_erp_fa_asset_disposaldetail` WHERE `assetdisposalMasterAutoID` = '{$assetdisposalMasterAutoID}'")->result_array();

        $this->db->trans_start();

        /*Asset Master Update*/
        $dataUpdate['selectedForDisposal'] = 0;
        foreach ($Assets as $asset) {
            $this->db->where('faID', $asset['faID']);
            $this->db->update('srp_erp_fa_asset_master', $dataUpdate);
        }


        $this->db->delete('srp_erp_fa_asset_disposaldetail', array('assetdisposalMasterAutoID' => $assetdisposalMasterAutoID, 'companyID' => $this->common_data['company_data']['company_id']));

        $this->db->delete('srp_erp_fa_asset_disposalmaster', array('assetdisposalMasterAutoID' => $assetdisposalMasterAutoID, 'companyID' => $this->common_data['company_data']['company_id']));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Disposal Successfully Deleted');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function assetDepGenerate_oldAssets()
    {
        $this->load->library('approvals');
        $financeyear = $this->common_data['company_data']['companyFinanceYearID'];
        $currentMonth = date('m');
        $currentYear = date("Y");
        $financeyear_period = $this->common_data['company_data']['companyFinanceYearID'];
        $companyID = current_companyID();
        $faID = trim($this->input->post('faID') ?? '');

        $currentDate = date("Y-m-d");
        $month = $currentMonth . '/' . $currentYear;

        $assetsMaster = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.serialNo, srp_erp_fa_asset_master.assetDescription, srp_erp_fa_asset_master.depMonth, srp_erp_fa_asset_master.DEPpercentage, Sum(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS AccDepAmount, srp_erp_fa_asset_master.assetCodeS, srp_erp_fa_asset_master.faUnitSerialNo, srp_erp_fa_asset_master.faCatID, srp_erp_fa_asset_master.faSubCatID, srp_erp_fa_asset_master.faSubCatID2, srp_erp_fa_asset_master.faSubCatID3, srp_erp_fa_asset_master.transactionCurrency, srp_erp_fa_asset_master.transactionCurrencyID, srp_erp_fa_asset_master.transactionCurrencyExchangeRate, srp_erp_fa_asset_master.transactionAmount, srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_master.companyLocalCurrency,srp_erp_fa_asset_master.companyLocalCurrencyID, srp_erp_fa_asset_master.companyLocalExchangeRate, srp_erp_fa_asset_master.companyLocalAmount, srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_master.companyReportingCurrency, srp_erp_fa_asset_master.companyReportingCurrencyID, srp_erp_fa_asset_master.companyReportingExchangeRate, srp_erp_fa_asset_master.companyReportingAmount, srp_erp_fa_asset_master.companyReportingDecimalPlaces,srp_erp_fa_asset_master.segmentID,srp_erp_fa_asset_master.segmentCode FROM srp_erp_fa_asset_master LEFT JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_asset_master.faID = srp_erp_fa_assetdepreciationperiods.faID WHERE  srp_erp_fa_asset_master.faID = '{$faID}' AND srp_erp_fa_asset_master.companyID='{$companyID}' AND srp_erp_fa_asset_master.selectedForDisposal <> 1 AND srp_erp_fa_asset_master.assetType=1 GROUP BY srp_erp_fa_asset_master.faID ")->row_array();

        /*       echo $this->db->last_query();
               exit;*/


        $isAvaialbeDep = $this->db->query("SELECT * FROM `srp_erp_fa_depmaster` WHERE `depMonthYear` = '{$month}' AND companyID='{$companyID}' AND depType = 1")->row_array();

        $financeyearDate = $this->db->query("SELECT * FROM `srp_erp_companyfinanceyear` WHERE `companyFinanceYearID` = '{$financeyear}' AND companyID='{$companyID}'")->row_array();

        if ($isAvaialbeDep) {
            $this->session->set_flashdata('e', "Already done Depreciation for the selected Asset.");
            return array('status' => false);
        }

        if ($assetsMaster) {
            $this->load->library('sequence');

            $depMaster['companyID'] = $companyID;
            $depMaster['companyCode'] = current_companyCode();

            $depMaster['documentID'] = 'FAD';
            $depMaster['depDate'] = $currentDate;
            $depMaster['serialNo'] = '';

            $depMaster['companyFinanceYearID'] = $financeyear;

            $depMaster['FYBegin'] = $financeyearDate['beginingDate'];
            $depMaster['FYEnd'] = $financeyearDate['endingDate'];

            $depMaster['FYPeriodDateFrom'] = $currentDate;
            $depMaster['FYPeriodDateTo'] = $currentDate;

            $depMaster['depCode'] = $this->sequence->sequence_generator("FAD");
            $depMaster['depMonthYear'] = $month;

            $depMaster['createdPCID'] = $this->common_data['current_pc'];
            $depMaster['createdUserID'] = $this->common_data['current_userID'];
            $depMaster['createdUserName'] = $this->common_data['current_user'];
            $depMaster['createdDateTime'] = $this->common_data['current_date'];
            $depMaster['timestamp'] = $this->common_data['current_date'];

            /*Currency*/
            $depMaster['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $depMaster['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $depMaster['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
            $depMaster['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];

            $reporting_currency = currency_conversion($depMaster['companyLocalCurrency'], $depMaster['companyReportingCurrency']);

            $depMaster['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $depMaster['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
            $depMaster['transactionExchangeRate'] = 1;
            $depMaster['transactionAmount'] = '';
            $depMaster['transactionCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];

            $depMaster['companyLocalExchangeRate'] = 1;
            $depMaster['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
            $depMaster['companyLocalAmount'] = '';

            $depMaster['companyReportingExchangeRate'] = $reporting_currency['conversion'];
            $depMaster['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $depMaster['companyReportingAmount'] = ($depMaster['companyLocalAmount'] / $depMaster['companyReportingExchangeRate']);
            $depMaster['depType'] = 1;

            $depMaster['confirmedYN'] = 1;
            $depMaster['confirmedByEmpID'] = $this->common_data['current_userID'];
            $depMaster['confirmedByName'] = $this->common_data['current_user'];
            $depMaster['confirmedDate'] = $this->common_data['current_date'];

            /*Currency*/

            $this->db->insert('srp_erp_fa_depmaster', $depMaster);
            $last_dep_id = $this->db->insert_id();

            if (!empty($last_dep_id)) {
                $approvals_status = $this->approvals->CreateApproval('FAD', $last_dep_id, $depMaster['depCode'], 'Asset Management', 'srp_erp_fa_depmaster', 'depMasterAutoID');
                if ($approvals_status) {
                    $data = array('confirmedYN' => 1, 'confirmedByEmpID' => $this->common_data['current_userID'], 'confirmedDate' => $this->common_data['current_date']);
                    $this->db->where('depMasterAutoID', $last_dep_id);
                    $result = $this->db->update('srp_erp_fa_depmaster', $data);
                }
            }

            $blAmount = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];
            $depAmount = dep_calculate($assetsMaster['companyLocalAmount'], $assetsMaster['DEPpercentage']);

            $nbv = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];

            if ($nbv < $depAmount) {
                $depAmount = $nbv;
            }

            if ($blAmount > 0) {
                $depDetails["companyID"] = $companyID;
                $depDetails["depMasterAutoID"] = $last_dep_id;
                $depDetails["faFinanceCatID"] = '';
                $depDetails["faMainCategory"] = $assetsMaster['faCatID'];
                $depDetails["faSubCategory"] = $assetsMaster['faSubCatID'];
                $depDetails["faID"] = $assetsMaster['faID'];
                $depDetails["faCode"] = $assetsMaster['faCode'];
                $depDetails["assetDescription"] = $assetsMaster['assetDescription'];
                $depDetails["depMonth"] = $currentYear;
                $depDetails["depPercent"] = $assetsMaster['DEPpercentage'];
                $depDetails["depMonthYear"] = $month;


                $depDetails['companyLocalCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];
                $depDetails['companyLocalCurrency'] = $assetsMaster['companyLocalCurrency'];
                $depDetails['companyReportingCurrencyID'] = $assetsMaster['companyReportingCurrencyID'];
                $depDetails['companyReportingCurrency'] = $assetsMaster['companyReportingCurrency'];
                $depDetails['transactionCurrency'] = $assetsMaster['companyLocalCurrency'];
                $depDetails['transactionCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];

                $localCurrency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyLocalCurrency']);
                $transactionCurrency = currency_conversion($depDetails['transactionCurrency'], $depDetails['companyLocalCurrency']);
                $reporting_currency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyReportingCurrency']);

                $depDetails['companyLocalExchangeRate'] = 1;
                $depDetails['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $depDetails['transactionExchangeRate'] = 1;

                $depDetails['companyLocalCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];
                $depDetails['companyReportingCurrencyDecimalPlaces'] = $assetsMaster['companyReportingDecimalPlaces'];
                $depDetails['transactionCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];


                $depDetails['companyLocalAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                $depDetails['transactionAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                $depDetails['companyReportingAmount'] = round(($depAmount / $reporting_currency['conversion']), $assetsMaster['companyReportingDecimalPlaces']);

                /*//Currency*/
                $depDetails['segmentID'] = $assetsMaster['segmentID'];
                $depDetails['segmentCode'] = $assetsMaster['segmentCode'];


                $depDetails['createdPCID'] = $this->common_data['current_pc'];
                $depDetails['createdUserID'] = $this->common_data['current_userID'];
                $depDetails['createdUserName'] = $this->common_data['current_user'];
                $depDetails['createdDateTime'] = $this->common_data['current_date'];
                $depDetails['timestamp'] = $this->common_data['current_date'];

                $this->db->insert('srp_erp_fa_assetdepreciationperiods', $depDetails);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', "Save Failed." . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_dep_id);
            }


        } else {
            $this->session->set_flashdata('e', "There is no asset Exists to perform depreciation.");
            return array('status' => false);
        }
    }

    function assetDepGenerate_oldAssets_backdate()
    {
        $this->load->library('approvals');
        $financeyear = $this->common_data['company_data']['companyFinanceYearID'];
        $currentMonth = date('m');
        $currentYear = date("Y");
        $financeyear_period = $this->common_data['company_data']['companyFinanceYearID'];
        $companyID = current_companyID();
        $faID = trim($this->input->post('faID') ?? '');

        $currentDate = date("Y-m-d");
        $month = $currentMonth . '/' . $currentYear;

        $isAvaialbeinDepMaster = $this->db->query("SELECT depCode FROM srp_erp_fa_depmaster WHERE companyID = '{$companyID}' AND depType = 1 AND approvedYN = 1")->row_array();

        if ($isAvaialbeinDepMaster) {
            return array('status' => false);
            exit();
        }

        $assetsMaster = $this->db->query("SELECT srp_erp_fa_asset_master.faID, srp_erp_fa_asset_master.faCode, srp_erp_fa_asset_master.serialNo, srp_erp_fa_asset_master.assetDescription, srp_erp_fa_asset_master.depMonth, srp_erp_fa_asset_master.DEPpercentage, Sum(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS AccDepAmount, srp_erp_fa_asset_master.assetCodeS, srp_erp_fa_asset_master.faUnitSerialNo, srp_erp_fa_asset_master.faCatID, srp_erp_fa_asset_master.faSubCatID, srp_erp_fa_asset_master.faSubCatID2, srp_erp_fa_asset_master.faSubCatID3, srp_erp_fa_asset_master.transactionCurrency, srp_erp_fa_asset_master.transactionCurrencyID, srp_erp_fa_asset_master.transactionCurrencyExchangeRate, srp_erp_fa_asset_master.transactionAmount, srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces, srp_erp_fa_asset_master.companyLocalCurrency,srp_erp_fa_asset_master.companyLocalCurrencyID, srp_erp_fa_asset_master.companyLocalExchangeRate, srp_erp_fa_asset_master.companyLocalAmount, srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces, srp_erp_fa_asset_master.companyReportingCurrency, srp_erp_fa_asset_master.companyReportingCurrencyID, srp_erp_fa_asset_master.companyReportingExchangeRate, srp_erp_fa_asset_master.companyReportingAmount, srp_erp_fa_asset_master.companyReportingDecimalPlaces,srp_erp_fa_asset_master.segmentID,srp_erp_fa_asset_master.segmentCode,DATE_FORMAT(srp_erp_fa_asset_master.dateDEP,'%Y-%m-%d') as DateofDep FROM srp_erp_fa_asset_master LEFT JOIN srp_erp_fa_assetdepreciationperiods ON srp_erp_fa_asset_master.faID = srp_erp_fa_assetdepreciationperiods.faID WHERE  srp_erp_fa_asset_master.faID = '{$faID}' AND srp_erp_fa_asset_master.companyID='{$companyID}' AND srp_erp_fa_asset_master.selectedForDisposal <> 1 AND srp_erp_fa_asset_master.assetType=1 GROUP BY srp_erp_fa_asset_master.faID ")->row_array();

        $isAvaialbeDep = $this->db->query("SELECT * FROM `srp_erp_fa_depmaster` WHERE `depMonthYear` = '{$month}' AND companyID='{$companyID}' AND depType = 1")->row_array();

        $financeyearDate = $this->db->query("SELECT * FROM `srp_erp_companyfinanceyear` WHERE `companyFinanceYearID` = '{$financeyear}' AND companyID='{$companyID}'")->row_array();
        /*
                if ($isAvaialbeDep) {
                    $this->session->set_flashdata('e', "Already done Depreciation for the selected Asset.");
                    return array('status' => false);
                }*/

        /*       echo $this->db->last_query();
               exit;*/
        if ($assetsMaster) {

            $DepLastRun = $this->db->query("SELECT MAX(depDate) as max FROM srp_erp_fa_depmaster where companyID = '{$companyID}' AND approvedYN = 1")->row_array();

            $start = new DateTime($assetsMaster['DateofDep']);
            $start->modify('first day of this month');
            $end = new DateTime($DepLastRun['max']);
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period = new DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $currentMonthNew = $dt->format("m/Y");
                $explodeYear = explode('/', $currentMonthNew);
                $this->load->library('sequence');

                $depMaster['companyID'] = $companyID;
                $depMaster['companyCode'] = current_companyCode();

                $depMaster['documentID'] = 'FAD';
                $depMaster['depDate'] = $currentDate;
                $depMaster['serialNo'] = '';

                $depMaster['companyFinanceYearID'] = $financeyear;

                $depMaster['FYBegin'] = $financeyearDate['beginingDate'];
                $depMaster['FYEnd'] = $financeyearDate['endingDate'];

                $depMaster['FYPeriodDateFrom'] = $currentDate;
                $depMaster['FYPeriodDateTo'] = $currentDate;

                $depMaster['depCode'] = $this->sequence->sequence_generator("FAD");
                $depMaster['depMonthYear'] = $currentMonthNew;

                $depMaster['createdPCID'] = $this->common_data['current_pc'];
                $depMaster['createdUserID'] = $this->common_data['current_userID'];
                $depMaster['createdUserName'] = $this->common_data['current_user'];
                $depMaster['createdDateTime'] = $this->common_data['current_date'];
                $depMaster['timestamp'] = $this->common_data['current_date'];

                /*Currency*/
                $depMaster['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $depMaster['companyLocalCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $depMaster['companyReportingCurrency'] = $this->common_data['company_data']['company_reporting_currency'];
                $depMaster['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];

                $reporting_currency = currency_conversion($depMaster['companyLocalCurrency'], $depMaster['companyReportingCurrency']);

                $depMaster['transactionCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $depMaster['transactionCurrency'] = $this->common_data['company_data']['company_default_currency'];
                $depMaster['transactionExchangeRate'] = 1;
                $depMaster['transactionAmount'] = '';
                $depMaster['transactionCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];

                $depMaster['companyLocalExchangeRate'] = 1;
                $depMaster['companyLocalCurrencyDecimalPlaces'] = $this->common_data['company_data']['company_default_decimal'];
                $depMaster['companyLocalAmount'] = '';

                $depMaster['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $depMaster['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $depMaster['companyReportingAmount'] = ($depMaster['companyLocalAmount'] / $depMaster['companyReportingExchangeRate']);
                $depMaster['depType'] = 1;

                $depMaster['confirmedYN'] = 1;
                $depMaster['confirmedByEmpID'] = $this->common_data['current_userID'];
                $depMaster['confirmedByName'] = $this->common_data['current_user'];
                $depMaster['confirmedDate'] = $this->common_data['current_date'];

                /*Currency*/

                $this->db->insert('srp_erp_fa_depmaster', $depMaster);
                $last_dep_id = $this->db->insert_id();

                if (!empty($last_dep_id)) {

                    $approvals_status = $this->approvals->CreateApproval('FAD', $last_dep_id, $depMaster['depCode'], 'Asset Management', 'srp_erp_fa_depmaster', 'depMasterAutoID');
                    if ($approvals_status) {
                        $data = array('confirmedYN' => 1, 'confirmedByEmpID' => $this->common_data['current_userID'], 'confirmedDate' => $this->common_data['current_date']);
                        $this->db->where('depMasterAutoID', $last_dep_id);
                        $result = $this->db->update('srp_erp_fa_depmaster', $data);
                    }
                }

                $blAmount = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];
                $depAmount = dep_calculate($assetsMaster['companyLocalAmount'], $assetsMaster['DEPpercentage']);

                $nbv = $assetsMaster['companyLocalAmount'] - $assetsMaster['AccDepAmount'];

                if ($nbv < $depAmount) {
                    $depAmount = $nbv;
                }

                if ($blAmount > 0) {

                    $depDetails["companyID"] = $companyID;
                    $depDetails["depMasterAutoID"] = $last_dep_id;
                    $depDetails["faFinanceCatID"] = '';
                    $depDetails["faMainCategory"] = $assetsMaster['faCatID'];
                    $depDetails["faSubCategory"] = $assetsMaster['faSubCatID'];
                    $depDetails["faID"] = $assetsMaster['faID'];
                    $depDetails["faCode"] = $assetsMaster['faCode'];
                    $depDetails["assetDescription"] = $assetsMaster['assetDescription'];
                    $depDetails["depMonth"] = $explodeYear[1];
                    $depDetails["depPercent"] = $assetsMaster['DEPpercentage'];
                    $depDetails["depMonthYear"] = $currentMonthNew;


                    $depDetails['companyLocalCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];
                    $depDetails['companyLocalCurrency'] = $assetsMaster['companyLocalCurrency'];
                    $depDetails['companyReportingCurrencyID'] = $assetsMaster['companyReportingCurrencyID'];
                    $depDetails['companyReportingCurrency'] = $assetsMaster['companyReportingCurrency'];
                    $depDetails['transactionCurrency'] = $assetsMaster['companyLocalCurrency'];
                    $depDetails['transactionCurrencyID'] = $assetsMaster['companyLocalCurrencyID'];

                    $localCurrency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyLocalCurrency']);
                    $transactionCurrency = currency_conversion($depDetails['transactionCurrency'], $depDetails['companyLocalCurrency']);
                    $reporting_currency = currency_conversion($depDetails['companyLocalCurrency'], $depDetails['companyReportingCurrency']);

                    $depDetails['companyLocalExchangeRate'] = 1;
                    $depDetails['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                    $depDetails['transactionExchangeRate'] = 1;

                    $depDetails['companyLocalCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];
                    $depDetails['companyReportingCurrencyDecimalPlaces'] = $assetsMaster['companyReportingDecimalPlaces'];
                    $depDetails['transactionCurrencyDecimalPlaces'] = $assetsMaster['companyLocalCurrencyDecimalPlaces'];


                    $depDetails['companyLocalAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                    $depDetails['transactionAmount'] = round($depAmount, $depDetails['companyLocalCurrencyDecimalPlaces']);
                    $depDetails['companyReportingAmount'] = round(($depAmount / $reporting_currency['conversion']), $assetsMaster['companyReportingDecimalPlaces']);

                    /*//Currency*/
                    $depDetails['segmentID'] = $assetsMaster['segmentID'];
                    $depDetails['segmentCode'] = $assetsMaster['segmentCode'];


                    $depDetails['createdPCID'] = $this->common_data['current_pc'];
                    $depDetails['createdUserID'] = $this->common_data['current_userID'];
                    $depDetails['createdUserName'] = $this->common_data['current_user'];
                    $depDetails['createdDateTime'] = $this->common_data['current_date'];
                    $depDetails['timestamp'] = $this->common_data['current_date'];

                    $this->db->insert('srp_erp_fa_assetdepreciationperiods', $depDetails);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', "Save Failed." . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_dep_id);
            }
        } else {
            $this->session->set_flashdata('e', "There is no asset Exists to perform depreciation.");
            return array('status' => false);
        }

    }

    function assetDepGenerate_CurrentMonth()
    {

        $this->db->trans_start();
        $faID = trim($this->input->post('faID') ?? '');
        $currentDate = date("Y-m-d");
        $month = trim($this->input->post('month') ?? '');
        $companyID = current_companyID();

        $assetMaster = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE faID='{$faID}'")->row_array();

        $DepLastRun = $this->db->query("SELECT MAX(depDate) as max FROM srp_erp_fa_depmaster where companyID = '{$companyID}' AND approvedYN = 1;")->row_array();

        /*calculating depreciation*/

        if ($month == 'currentMonth') {
            $depAmount = dep_calculate($assetMaster['companyLocalAmount'], $assetMaster['DEPpercentage']);
            $data['missingDepAmount'] = $depAmount;
        } else if ($month == 'backDate') {
            $totalMonths = number_months($assetMaster['dateDEP'], $DepLastRun['max']);
            $depAmount = dep_calculate($assetMaster['companyLocalAmount'], $assetMaster['DEPpercentage']);
            $data['missingDepAmount'] = $depAmount * $totalMonths;
        }

        $this->db->where('faID', $faID);
        $this->db->update('srp_erp_fa_asset_master', $data);
        $this->session->set_flashdata('s', 'Asset Depreciation Created Successfully.');

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    function saveAssetLocation()
    {
        $description = $this->input->post('location[]');
        $data = array();
        foreach ($description as $key => $de) {
            $data[$key]['locationName'] = $de;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['companyCode'] = current_companyCode();
        }
        $this->db->insert_batch('srp_erp_location', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }

    function deleteAssetLocation()
    {
        $location = $this->input->post('locationID');

        $this->db->where('locationID', $location)->delete('srp_erp_location');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }

    function updateAssetLocation()
    {

        $data['locationName'] = $this->input->post('assetLocationDesc');
        $this->db->where('locationID', $this->input->post('hidden-id'));
        $result = $this->db->update('srp_erp_location', $data);
        if ($result) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }
    function saveAssetCustodian()
    { $types = $this->input->post('CustodianTypes[]');
        $data = array();
        $existingRecords = array();
        
        foreach ($types as $key => $de) {
            $existingCheck = $this->db->get_where('srp_erp_fa_custodian', array(
                'custodianName' => $de,
                'companyID' => current_companyID(),
                'companyCode' => current_companyCode(),
            ))->row();
        
            if ($existingCheck) {
                // Record already exists, add custodianName to existingRecords array
                $existingRecords[] = $de;
            } else {
                // Record does not exist, proceed with insertion
                $data[$key]['custodianName'] = $de;
                $data[$key]['companyID'] = current_companyID();
                $data[$key]['companyCode'] = current_companyCode();
            }
        }
        
        if (!empty($data)) {
            // Insert only the non-existing records
            $this->db->insert_batch('srp_erp_fa_custodian', $data);
        
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records inserted successfully');
            } else {
                return array('e', 'Error in insert record');
            }
        } else {
            // All records already exist, provide a message with existing custodianNames
            return array('e', 'CustodianNames already exist: ' . implode(', ', $existingRecords));
        }
        
    }

    function deleteAssetCustodian()
    {
        $id = $this->input->post('id');

        $this->db->where('id', $id)->delete('srp_erp_fa_custodian');
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records deleted successfully');
        } else {
            return array('e', 'Error in deleting process');
        }
    }
    function updateAssetCustodian()
    {
                    // Get the new custodianName value from the input
            $newCustodianName = $this->input->post('assetCustodianTypes');

            // Check if the new value already exists in the database
            $this->db->where('custodianName', $newCustodianName);
            $duplicateCheck = $this->db->get('srp_erp_fa_custodian')->row();

            if (!$duplicateCheck) {
                // Perform the update since the custodianName is not a duplicate
                $data['custodianName'] = $newCustodianName;
                $this->db->where('id', $this->input->post('hidden-id'));
                $result = $this->db->update('srp_erp_fa_custodian', $data);

                if ($result) {
                    return array('s', 'Record updated successfully');
                } else {
                    return array('e', 'Error in updating record');
                }
            } else {
                // Return a message indicating that the custodianName already exists
                return array('e', 'CustodianName already exists, no update performed');
            }

    }
    function edit_attachment()
    {
        $this->db->select('*');
        $this->db->from('srp_erp_documentattachments');
        $this->db->where('attachmentID', $this->input->post('attachmentID'));
        $result = $this->db->get()->row_array();
        return $result;
    }

    function updateAttachment($id, $data)
    {
        $this->db->where('attachmentID', $id);
        $result = $this->db->update('srp_erp_documentattachments', $data);
        return $result;
    }

    function fetch_dep_total($depMasterAutoID){
        $this->db->select("
                SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) as DeptransactionAmount,
                srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces as DeptransactionCurrencyDecimalPlaces,
                SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) as DepcompanyLocalAmount,
                srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces as DepcompanyLocalCurrencyDecimalPlaces,
                SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) as DepcompanyReportingAmount,
                srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces as DepcompanyReportingCurrencyDecimalPlaces,
                SUM(srp_erp_fa_asset_master.transactionAmount) as transactionAmount,
                srp_erp_fa_asset_master.transactionCurrencyDecimalPlaces,
                SUM(srp_erp_fa_asset_master.companyLocalAmount) as companyLocalAmount,
                srp_erp_fa_asset_master.companyLocalCurrencyDecimalPlaces as companyLocalCurrencyDecimalPlaces,
                SUM(srp_erp_fa_asset_master.companyReportingAmount) as companyReportingAmount,
                srp_erp_fa_asset_master.companyReportingDecimalPlaces as companyReportingDecimalPlaces
                ", false);
        $this->db->from('srp_erp_fa_assetdepreciationperiods');
        $this->db->join('srp_erp_fa_asset_master', 'srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID');
        $this->db->where('srp_erp_fa_assetdepreciationperiods.depMasterAutoID', $depMasterAutoID);
        $this->db->where('srp_erp_fa_assetdepreciationperiods.companyID', current_companyID());
        $result = $this->db->get()->row_array();
        return $result;
    }

    function add_depreciation($system_code){
        $financeyear = $this->common_data['company_data']['companyFinanceYearID'];
        $companyID=current_companyID();
        $currentDate = date("Y-m-d");
        $assetMaster = $this->db->query("SELECT * FROM srp_erp_fa_asset_master WHERE faID='{$system_code}'")->row_array();
        //$financeyearDate = $this->db->query("SELECT * FROM `srp_erp_companyfinanceyear` WHERE `companyFinanceYearID` = '{$financeyear}' AND companyID='{$companyID}'")->row_array();
        $accDepDate= $assetMaster['accDepDate'];
        $financeyearDate = $this->db->query("SELECT
	    srp_erp_companyfinanceyear.*,srp_erp_companyfinanceperiod.dateFrom,srp_erp_companyfinanceperiod.dateTo
        FROM
            `srp_erp_companyfinanceyear`
        LEFT JOIN srp_erp_companyfinanceperiod ON srp_erp_companyfinanceyear.companyFinanceYearID = srp_erp_companyfinanceperiod.companyFinanceYearID AND '$accDepDate' BETWEEN dateFrom
        AND dateTo AND srp_erp_companyfinanceperiod.isActive=1
        WHERE
            '$accDepDate' BETWEEN beginingDate
        AND endingDate
        AND srp_erp_companyfinanceyear.isActive = 1
        AND srp_erp_companyfinanceyear.companyID = $companyID")->row_array();
        $time=strtotime($assetMaster['postDate']);
        //$currentMonthNew=date_format($assetMaster['postDate'],"m/Y");
        $currentMonthNew=date("m/Y",$time);
        $accDepTimeformate=strtotime($assetMaster['accDepDate']);
        $depMonthYear=date("m/Y",$accDepTimeformate);

        $explodeYear = explode('/', $currentMonthNew);
        $this->load->library('sequence');

        $depMaster['companyID'] = current_companyID();
        $depMaster['companyCode'] = current_companyCode();
        $depMaster['documentID'] = 'FAD';
        $depMaster['depDate'] = $assetMaster['accDepDate'];
        $depMaster['serialNo'] = '';
        $depMaster['companyFinanceYearID'] = $financeyearDate['companyFinanceYearID'];
        $depMaster['FYBegin'] = $financeyearDate['beginingDate'];
        $depMaster['FYEnd'] = $financeyearDate['endingDate'];
        $depMaster['FYPeriodDateFrom'] = $financeyearDate['dateFrom'];
        $depMaster['FYPeriodDateTo'] = $financeyearDate['dateTo'];
        $depMaster['depCode'] = $this->sequence->sequence_generator("FAD");
        //$depMaster['depMonthYear'] = $currentMonthNew;
        $depMaster['depMonthYear'] = $depMonthYear;
        $depMaster['createdPCID'] = $this->common_data['current_pc'];
        $depMaster['createdUserID'] = $this->common_data['current_userID'];
        $depMaster['createdUserName'] = $this->common_data['current_user'];
        $depMaster['createdDateTime'] = $this->common_data['current_date'];
        $depMaster['timestamp'] = $this->common_data['current_date'];

        /*Currency*/
        $depMaster['companyLocalCurrencyID'] = $assetMaster['companyLocalCurrencyID'];
        $depMaster['companyLocalCurrency'] = $assetMaster['companyLocalCurrency'];
        $depMaster['companyReportingCurrency'] = $assetMaster['companyReportingCurrency'];
        $depMaster['companyReportingCurrencyID'] = $assetMaster['companyReportingCurrencyID'];

        $reporting_currency = currency_conversion($depMaster['companyLocalCurrency'], $depMaster['companyReportingCurrency']);

        $depMaster['transactionCurrencyID'] = $assetMaster['transactionCurrencyID'];
        $depMaster['transactionCurrency'] = $assetMaster['transactionCurrency'];
        $depMaster['transactionExchangeRate'] = $assetMaster['transactionCurrencyExchangeRate'];
        $depMaster['transactionAmount'] = $assetMaster['accDepAmount']/ $assetMaster['companyLocalExchangeRate'];
        $depMaster['transactionCurrencyDecimalPlaces'] = $assetMaster['transactionCurrencyDecimalPlaces'];

        $depMaster['companyLocalExchangeRate'] = $assetMaster['companyLocalExchangeRate'];
        $depMaster['companyLocalCurrencyDecimalPlaces'] = $assetMaster['companyLocalCurrencyDecimalPlaces'];
        $depMaster['companyLocalAmount'] = $assetMaster['accDepAmount']/ $assetMaster['companyLocalExchangeRate'];

        $depMaster['companyReportingExchangeRate'] = $assetMaster['companyReportingExchangeRate'];
        $depMaster['companyReportingCurrencyDecimalPlaces'] = $assetMaster['companyReportingDecimalPlaces'];
        $depMaster['companyReportingAmount'] = ($depMaster['companyLocalAmount'] / $depMaster['companyReportingExchangeRate']);
        $depMaster['depType'] = 1;
        $depMaster['segmentID'] = $assetMaster['segmentID'];
        $depMaster['segmentCode'] = $assetMaster['segmentCode'];

        $depMaster['confirmedYN'] = 1;
        $depMaster['confirmedByEmpID'] = $this->common_data['current_userID'];
        $depMaster['confirmedByName'] = $this->common_data['current_user'];
        $depMaster['confirmedDate'] = $this->common_data['current_date'];

        $depMaster['approvedYN'] = 1;
        $depMaster['approvedbyEmpID'] = $this->common_data['current_userID'];
        $depMaster['approvedbyEmpName'] = $this->common_data['current_user'];
        $depMaster['approvedDate'] = $this->common_data['current_date'];

        /*Currency*/

        $results=$this->db->insert('srp_erp_fa_depmaster', $depMaster);
        $last_dep_id = $this->db->insert_id();
        if($results){
            $docApproved['departmentID'] = 'FAD';
            $docApproved['documentID'] = 'FAD';
            $docApproved['documentSystemCode'] = $last_dep_id;
            $docApproved['documentCode'] = $depMaster['depCode'];
            $docApproved['documentDate'] = $depMaster['depDate'];
            $docApproved['approvalLevelID'] = 1;
            $docApproved['roleID'] = 1;
            $docApproved['docConfirmedDate'] = $this->common_data['current_date'];
            $docApproved['docConfirmedByEmpID'] = $this->common_data['current_userID'];
            $docApproved['table_name'] = 'srp_erp_fa_depmaster';
            $docApproved['table_unique_field_name'] = 'depMasterAutoID';
            $docApproved['approvedEmpID'] = $this->common_data['current_userID'];
            $docApproved['approvedYN'] = 1;
            $docApproved['approvedDate'] = $this->common_data['current_date'];
            $docApproved['approvedPC'] = current_pc();
            $docApproved['companyID'] = current_companyID();
            //$docApproved['companyCode'] = current_company_code();

            $this->db->insert('srp_erp_documentapproved', $docApproved);


            $depDetails["companyID"] = $companyID;
            $depDetails['companyCode'] = current_companyCode();
            $depDetails["depMasterAutoID"] = $last_dep_id;
            $depDetails["faID"] = $assetMaster['faID'];
            $depDetails["faCode"] = $assetMaster['faCode'];
            $depDetails["assetDescription"] = $assetMaster['assetDescription'];
            $depDetails["depMonthYear"] = $depMaster['depMonthYear'];
            $depDetails['companyFinanceYearID'] =  $financeyearDate['companyFinanceYearID'];;
            $depDetails['FYBegin'] = $financeyearDate['beginingDate'];
            $depDetails['FYEnd'] = $financeyearDate['endingDate'];
            $depDetails['FYPeriodDateFrom'] = $financeyearDate['dateFrom'];
            $depDetails['FYPeriodDateTo'] = $financeyearDate['dateTo'];
            $depDetails['transactionCurrencyID'] = $assetMaster['transactionCurrencyID'];
            $depDetails['transactionCurrency'] = $assetMaster['transactionCurrency'];
            $depDetails['transactionExchangeRate'] = $assetMaster['transactionCurrencyExchangeRate'];
            $depDetails['transactionAmount'] = $assetMaster['accDepAmount']/ $assetMaster['companyLocalExchangeRate'];
            $depDetails['transactionCurrencyDecimalPlaces'] = $assetMaster['transactionCurrencyDecimalPlaces'];
            $depDetails['companyLocalCurrencyID'] = $assetMaster['companyLocalCurrencyID'];
            $depDetails['companyLocalCurrency'] = $assetMaster['companyLocalCurrency'];
            $depDetails['companyLocalExchangeRate'] = $assetMaster['companyLocalExchangeRate'];
            $depDetails['companyLocalCurrencyDecimalPlaces'] = $assetMaster['companyLocalCurrencyDecimalPlaces'];
            $depDetails['companyLocalAmount'] = $assetMaster['accDepAmount']/ $assetMaster['companyLocalExchangeRate'];
            $depDetails['companyReportingCurrency'] = $assetMaster['companyReportingCurrency'];
            $depDetails['companyReportingCurrencyID'] = $assetMaster['companyReportingCurrencyID'];
            $depDetails['companyReportingExchangeRate'] = $assetMaster['companyReportingExchangeRate'];
            $depDetails['companyReportingCurrencyDecimalPlaces'] = $assetMaster['companyReportingDecimalPlaces'];
            $depDetails['companyReportingAmount'] = ($depMaster['companyLocalAmount'] / $depMaster['companyReportingExchangeRate']);
            $depDetails['confirmedYN'] = 1;
            $depDetails['confirmedByEmpID'] = $this->common_data['current_userID'];
            $depDetails['confirmedByName'] = $this->common_data['current_user'];
            $depDetails['confirmedDate'] = $this->common_data['current_date'];
            $depDetails['segmentID'] = $assetMaster['segmentID'];
            $depDetails['segmentCode'] = $assetMaster['segmentCode'];
            $depDetails['createdPCID'] = $this->common_data['current_pc'];
            $depDetails['createdUserID'] = $this->common_data['current_userID'];
            $depDetails['createdUserName'] = $this->common_data['current_user'];
            $depDetails['createdDateTime'] = $this->common_data['current_date'];
            $depDetails['timestamp'] = $this->common_data['current_date'];
            $period=$this->db->insert('srp_erp_fa_assetdepreciationperiods', $depDetails);
            if($period){
                $depMastersDebits = $this->db->query("SELECT
	srp_erp_fa_depmaster.depCode,
	srp_erp_fa_depmaster.depDate,
	srp_erp_fa_depmaster.depMonthYear,
	srp_erp_fa_depmaster.FYBegin,
	srp_erp_fa_depmaster.FYEnd,
	srp_erp_fa_depmaster.FYPeriodDateFrom,
	srp_erp_fa_depmaster.FYPeriodDateTo,
	srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID,
	srp_erp_fa_assetdepreciationperiods.faFinanceCatID,
	srp_erp_fa_assetdepreciationperiods.faID,
	srp_erp_fa_assetdepreciationperiods.faMainCategory,
	srp_erp_fa_assetdepreciationperiods.faSubCategory,
	srp_erp_fa_assetdepreciationperiods.faCode,
	srp_erp_fa_assetdepreciationperiods.assetDescription,
	srp_erp_fa_assetdepreciationperiods.transactionCurrency,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyID,
	srp_erp_fa_assetdepreciationperiods.transactionExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrency,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrency,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces,
	srp_erp_fa_depmaster.documentID,
	srp_erp_fa_depmaster.serialNo,
	srp_erp_fa_depmaster.depMasterAutoID,
	srp_erp_fa_asset_master.ACCDEPGLCODE,
	srp_erp_fa_asset_master.DEPGLCODE,
	srp_erp_fa_asset_master.ACCDEPGLAutoID,
	srp_erp_fa_asset_master.DEPGLAutoID,
	srp_erp_fa_assetdepreciationperiods.segmentID,
	srp_erp_fa_assetdepreciationperiods.segmentCode,
	srp_erp_fa_depmaster.approvedbyEmpName,
    srp_erp_fa_depmaster.approvedbyEmpID,
    srp_erp_fa_depmaster.approvedDate,
    srp_erp_fa_depmaster.confirmedByEmpID,
    srp_erp_fa_depmaster.confirmedByName,
    srp_erp_fa_depmaster.confirmedDate
FROM
	srp_erp_fa_assetdepreciationperiods
INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.depMasterAutoID = '{$last_dep_id}' GROUP BY DEPGLAutoID,
	srp_erp_fa_asset_master.segmentID")->result_array();

                $drgl = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE accountDefaultType = 1 AND companyID = $companyID")->row_array();

                foreach ($depMastersDebits as $depMastersDebit) {

                    /*Dr*/
                    $depgl = fetch_gl_account_desc($drgl['GLAutoID']);

                    $GLCr['documentCode'] = $depMastersDebit['documentID'];
                    $GLCr['documentMasterAutoID'] = $depMastersDebit['depMasterAutoID'];
                    $GLCr['documentSystemCode'] = $depMastersDebit['depCode'];
                    $GLCr['documentDetailAutoID'] = 0;
                    $GLCr['documentNarration'] = '';
                    $GLCr['GLAutoID'] = $drgl['GLAutoID'];
                    $GLCr['systemGLCode'] = $depgl['systemAccountCode'];
                    $GLCr['GLCode'] = $depgl['GLSecondaryCode'];
                    $GLCr['GLDescription'] = $depgl['GLDescription'];
                    $GLCr['GLType'] = $depgl['subCategory'];
                    $GLCr['amount_type'] = 'dr';

                    $GLCr['companyLocalCurrency'] = $depMastersDebit['companyLocalCurrency'];
                    $GLCr['companyLocalCurrencyID'] = $depMastersDebit['companyLocalCurrencyID'];
                    $GLCr['companyLocalExchangeRate'] = $depMastersDebit['companyLocalExchangeRate'];
                    $GLCr['companyLocalCurrencyDecimalPlaces'] = $depMastersDebit['companyLocalCurrencyDecimalPlaces'];
                    $GLCr['companyReportingCurrency'] = $depMastersDebit['companyReportingCurrency'];
                    $GLCr['companyReportingCurrencyID'] = $depMastersDebit['companyReportingCurrencyID'];


                    $GLCr['companyReportingExchangeRate'] = $depMastersDebit['companyReportingExchangeRate'];
                    $GLCr['companyReportingCurrencyDecimalPlaces'] = $depMastersDebit['companyReportingCurrencyDecimalPlaces'];
                    $GLCr['companyLocalAmount'] = $depMastersDebit['companyLocalAmount'];
                    $GLCr['companyReportingAmount'] = $depMastersDebit['companyReportingAmount'];

                    $GLCr['transactionCurrencyID'] = $depMastersDebit['companyLocalCurrencyID'];
                    $GLCr['transactionCurrency'] = $depMastersDebit['companyLocalCurrency'];
                    $GLCr['transactionExchangeRate'] = $depMastersDebit['companyLocalExchangeRate'];
                    $GLCr['transactionAmount'] = round($depMastersDebit['companyLocalAmount'], $depMastersDebit['companyLocalCurrencyDecimalPlaces']);
                    $GLCr['transactionCurrencyDecimalPlaces'] = $depMastersDebit['companyLocalCurrencyDecimalPlaces'];


                    $GLCr['companyID'] = $this->common_data['company_data']['company_id'];
                    $GLCr['companyCode'] = $this->common_data['company_data']['company_code'];
                    $GLCr['segmentID'] = $depMastersDebit['segmentID'];
                    $GLCr['segmentCode'] = $depMastersDebit['segmentCode'];
                    $GLCr['createdUserGroup'] = current_user_group();

                    $GLCr['createdPCID'] = $this->common_data['current_pc'];
                    $GLCr['createdUserID'] = $this->common_data['current_userID'];
                    $GLCr['createdUserName'] = $this->common_data['current_user'];
                    $GLCr['createdDateTime'] = $this->common_data['current_date'];
                    $GLCr['timestamp'] = $this->common_data['current_date'];

                    $GLCr['documentDate'] = $depMastersDebit['depDate'];
                    $GLCr['documentYear'] = date('Y', strtotime($depMastersDebit['depDate']));
                    $GLCr['documentMonth'] = date('m', strtotime($depMastersDebit['depDate']));


                    $GLCr['confirmedByEmpID'] = $depMastersDebit['confirmedByEmpID'];
                    $GLCr['confirmedByName'] = $depMastersDebit['confirmedByName'];
                    $GLCr['confirmedDate'] = $depMastersDebit['confirmedDate'];
                    $GLCr['approvedDate'] = $depMastersDebit['approvedDate'];
                    $GLCr['approvedbyEmpID'] = $depMastersDebit['approvedbyEmpID'];
                    $GLCr['approvedbyEmpName'] = $depMastersDebit['approvedbyEmpName'];

                    $this->db->insert('srp_erp_generalledger', $GLCr);

                }

                /*Cr*/
                $depMastersCredits = $this->db->query("SELECT
	srp_erp_fa_depmaster.depCode,
	srp_erp_fa_depmaster.depDate,
	srp_erp_fa_depmaster.depMonthYear,
	srp_erp_fa_depmaster.FYBegin,
	srp_erp_fa_depmaster.FYEnd,
	srp_erp_fa_depmaster.FYPeriodDateFrom,
	srp_erp_fa_depmaster.FYPeriodDateTo,
	srp_erp_fa_assetdepreciationperiods.DepreciationPeriodsID,
	srp_erp_fa_assetdepreciationperiods.faFinanceCatID,
	srp_erp_fa_assetdepreciationperiods.faID,
	srp_erp_fa_assetdepreciationperiods.faMainCategory,
	srp_erp_fa_assetdepreciationperiods.faSubCategory,
	srp_erp_fa_assetdepreciationperiods.faCode,
	srp_erp_fa_assetdepreciationperiods.assetDescription,
	srp_erp_fa_assetdepreciationperiods.transactionCurrency,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyID,
	srp_erp_fa_assetdepreciationperiods.transactionExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.transactionAmount) AS transactionAmount,
	srp_erp_fa_assetdepreciationperiods.transactionCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrency,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyLocalExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyLocalAmount) AS companyLocalAmount,
	srp_erp_fa_assetdepreciationperiods.companyLocalCurrencyDecimalPlaces,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrency,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyID,
	srp_erp_fa_assetdepreciationperiods.companyReportingExchangeRate,
	SUM(srp_erp_fa_assetdepreciationperiods.companyReportingAmount) AS companyReportingAmount,
	srp_erp_fa_assetdepreciationperiods.companyReportingCurrencyDecimalPlaces,
	srp_erp_fa_depmaster.documentID,
	srp_erp_fa_depmaster.serialNo,
	srp_erp_fa_depmaster.depMasterAutoID,
	srp_erp_fa_asset_master.ACCDEPGLCODE,
	srp_erp_fa_asset_master.DEPGLCODE,
	srp_erp_fa_asset_master.ACCDEPGLAutoID,
	srp_erp_fa_asset_master.DEPGLAutoID,
	srp_erp_fa_assetdepreciationperiods.segmentID,
	srp_erp_fa_assetdepreciationperiods.segmentCode,
	srp_erp_fa_depmaster.approvedbyEmpName,
    srp_erp_fa_depmaster.approvedbyEmpID,
    srp_erp_fa_depmaster.approvedDate,
    srp_erp_fa_depmaster.confirmedByEmpID,
    srp_erp_fa_depmaster.confirmedByName,
    srp_erp_fa_depmaster.confirmedDate
FROM
	srp_erp_fa_assetdepreciationperiods
INNER JOIN srp_erp_fa_depmaster ON srp_erp_fa_depmaster.depMasterAutoID = srp_erp_fa_assetdepreciationperiods.depMasterAutoID
INNER JOIN srp_erp_fa_asset_master ON srp_erp_fa_assetdepreciationperiods.faID = srp_erp_fa_asset_master.faID
WHERE
	srp_erp_fa_depmaster.depMasterAutoID = '{$last_dep_id}'
GROUP BY
	ACCDEPGLAutoID")->result_array();

                foreach ($depMastersCredits as $depMastersCredit) {
                    /*Cr*/

                    $accdepGl = fetch_gl_account_desc($depMastersCredit['ACCDEPGLAutoID']);

                    $GLdr['documentCode'] = $depMastersCredit['documentID'];
                    $GLdr['documentMasterAutoID'] = $depMastersCredit['depMasterAutoID'];
                    $GLdr['documentSystemCode'] = $depMastersCredit['depCode'];
                    $GLdr['documentDetailAutoID'] = 0;
                    $GLdr['documentNarration'] = '';
                    $GLdr['GLAutoID'] = $depMastersCredit['ACCDEPGLAutoID'];
                    $GLdr['systemGLCode'] = $accdepGl['systemAccountCode'];
                    $GLdr['GLCode'] = $accdepGl['GLSecondaryCode'];
                    $GLdr['GLDescription'] = $accdepGl['GLDescription'];
                    $GLdr['GLType'] = $accdepGl['subCategory'];
                    $GLdr['amount_type'] = 'cr';

                    $GLdr['companyLocalCurrency'] = $depMastersCredit['companyLocalCurrency'];
                    $GLdr['companyLocalCurrencyID'] = $depMastersCredit['companyLocalCurrencyID'];
                    $GLdr['companyLocalExchangeRate'] = $depMastersCredit['companyLocalExchangeRate'];
                    $GLdr['companyLocalCurrencyDecimalPlaces'] = $depMastersCredit['companyLocalCurrencyDecimalPlaces'];
                    $GLdr['companyReportingCurrency'] = $depMastersCredit['companyReportingCurrency'];
                    $GLdr['companyReportingCurrencyID'] = $depMastersCredit['companyReportingCurrencyID'];


                    $GLdr['companyReportingExchangeRate'] = $depMastersCredit['companyReportingExchangeRate'];
                    $GLdr['companyReportingCurrencyDecimalPlaces'] = $depMastersCredit['companyReportingCurrencyDecimalPlaces'];
                    $GLdr['companyLocalAmount'] = ($depMastersCredit['companyLocalAmount'] * -1);
                    $GLdr['companyReportingAmount'] = ($depMastersCredit['companyReportingAmount'] * -1);

                    $GLdr['transactionCurrency'] = $depMastersCredit['companyLocalCurrency'];
                    $GLdr['transactionCurrencyID'] = $depMastersCredit['companyLocalCurrencyID'];
                    $GLdr['transactionExchangeRate'] = $depMastersCredit['companyLocalExchangeRate'];
                    $GLdr['transactionAmount'] = round(($depMastersCredit['companyLocalAmount'] * -1), $depMastersCredit['companyLocalCurrencyDecimalPlaces']);
                    $GLdr['transactionCurrencyDecimalPlaces'] = $depMastersCredit['companyLocalCurrencyDecimalPlaces'];

                    $GLdr['companyID'] = $this->common_data['company_data']['company_id'];
                    $GLdr['companyCode'] = $this->common_data['company_data']['company_code'];
                    $GLdr['segmentID'] = $depMastersCredit['segmentID'];
                    $GLdr['segmentCode'] = $depMastersCredit['segmentCode'];
                    $GLdr['createdUserGroup'] = current_user_group();

                    $GLdr['createdPCID'] = $this->common_data['current_pc'];
                    $GLdr['createdUserID'] = $this->common_data['current_userID'];
                    $GLdr['createdUserName'] = $this->common_data['current_user'];
                    $GLdr['createdDateTime'] = $this->common_data['current_date'];
                    $GLdr['timestamp'] = $this->common_data['current_date'];

                    $GLdr['documentDate'] = $depMastersCredit['depDate'];
                    $GLdr['documentYear'] = date('Y', strtotime($depMastersCredit['depDate']));
                    $GLdr['documentMonth'] = date('m', strtotime($depMastersCredit['depDate']));

                    $GLdr['confirmedByEmpID'] = $depMastersCredit['confirmedByEmpID'];
                    $GLdr['confirmedByName'] = $depMastersCredit['confirmedByName'];
                    $GLdr['confirmedDate'] = $depMastersCredit['confirmedDate'];
                    $GLdr['approvedDate'] = $depMastersCredit['approvedDate'];
                    $GLdr['approvedbyEmpID'] = $depMastersCredit['approvedbyEmpID'];
                    $GLdr['approvedbyEmpName'] = $depMastersCredit['approvedbyEmpName'];

                    $this->db->insert('srp_erp_generalledger', $GLdr);
                }
            }
        }
    }
    function saveAssetLocationcode()
    {
        $description = $this->input->post('location[]');
        $locationcode = $this->input->post('locationcode[]');
        $locationType = $this->input->post('locationType[]');
        $data = array();
        foreach ($description as $key => $de) {

            if (!empty($locationType)) {
                $thislocationType= (array_key_exists($key, $locationType)) ? $locationType[$key] : 0;
            } else {
                $thislocationType = 0;
            }

            $data[$key]['locationName'] = $de;
            $data[$key]['locationCode'] = $locationcode[$key];
            $data[$key]['isCostLocation'] = 1;
            $data[$key]['locationType'] = $thislocationType;
            $data[$key]['companyID'] = current_companyID();
            $data[$key]['companyCode'] = current_companyCode();
        }
        $this->db->insert_batch('srp_erp_location', $data);
        if ($this->db->affected_rows() > 0) {
            return array('s', 'Records inserted successfully');
        } else {
            return array('e', 'Error in insert record');
        }
    }
    function updateAssetLocationcode()
    {
        $locationType = $this->input->post('assetLocationType');
        $data['locationName'] = $this->input->post('assetLocationDesc');
        $data['locationCode'] = $this->input->post('assetLocationcode');
        $data['locationType'] = (isset($locationType) ? $locationType  : 0 );
        $this->db->where('locationID', $this->input->post('hidden-id'));
        $result = $this->db->update('srp_erp_location', $data);
        if ($result) {
            return array('s', 'Records updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }

    }
    function link_employees()
    {
        $selectedItem = $this->input->post('selectedItemsSync[]');
        $locationID = $this->input->post('locationid');
        $companyCode = $this->common_data['company_data']['company_code'];
        $compID = current_companyID();
        $data = [];

        foreach ($selectedItem as $key => $vals) {
            $data[$key]['empID'] = $vals;
            $data[$key]['locationID'] = $locationID;
            $data[$key]['companyID'] = $compID;
            $data[$key]['companyCode'] = $companyCode;
            $data[$key]['createdUserGroup'] = $this->common_data['user_group'];
            $data[$key]['createdPCID'] = $this->common_data['current_pc'];
            $data[$key]['createdUserID'] =  $this->common_data['current_userID'];
            $data[$key]['createdDateTime'] = $this->common_data['current_date'];
            $data[$key]['createdUserName'] = $this->common_data['current_user'];

        }
        $result = $this->db->insert_batch('srp_erp_locationemployees', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Employee Added successfully !');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Employee Insertion Failed!');
            return array('status' => false);
        }
    }

    function delete_employees()
    {
        $employeelocationid = $this->input->post('employeelocationid');
        $locationid = $this->input->post('locationID');
        $empID = $this->input->post('empID');
        $companyid = current_companyID();

        $updateemploc = $this->db->query("SELECT EIdNo from srp_employeesdetails where locationID = $locationid AND Erp_companyID = $companyid And EIdNo = $empID ")->row_array();
         if(!empty($updateemploc))
         {
             $data['locationID'] = null;
             $this->db->where('Erp_companyID',$companyid);
             $this->db->where('EIdNo',$empID);
             $this->db->update('srp_employeesdetails', $data);
             $this->session->set_userdata("emplanglocationid",$data['locationID']);
         }

        $this->db->delete('srp_erp_locationemployees', array('autoID' => $employeelocationid));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error while deleting!');
        } else {
            $this->db->trans_commit();
            return array('s', 'Employee deleted successfully');
        }
    }
    function deleteAssetLocationcode()
    {
        $location = $this->input->post('locationID');
        $this->db->select('*');
        $this->db->from('srp_erp_locationemployees');
        $this->db->where('locationID', $location);
        $results = $this->db->get()->row_array();
        if ($results) {
            return array('e', 'Please delete all the assign employees before deleting this location.');
        }else
        {
            $this->db->where('locationID', $location)->delete('srp_erp_location');
            if ($this->db->affected_rows() > 0) {
                return array('s', 'Records deleted successfully');
            } else {
                return array('e', 'Error in deleting process');
            }
        }

    }
   

function save_request_header()
{
    $this->db->trans_start();
    $date_format_policy = date_format_policy();
    $DocumentDatenote = trim($this->input->post('documentDate') ?? '');
    $DocumentDate = input_format_date($DocumentDatenote, $date_format_policy);
    
    $data['requestedByEmpID'] = trim_desc($this->input->post('requestedByEmpID'));
    $data['documentID'] = 'ARN';
    $data['comments'] = trim_desc($this->input->post('comments'));
    $data['reference'] = trim_desc($this->input->post('reference'));
    $data['segmentID'] = trim_desc($this->input->post('segmentID'));
    $data['location'] = trim_desc($this->input->post('location'));
    $data['requestedByName'] = trim_desc($this->input->post('Ename2'));
    $data['documentDate'] = $DocumentDate;
    $data['modifiedPCID'] = $this->common_data['current_pc'];
    $data['modifiedUserID'] = $this->common_data['current_userID'];
    $data['modifiedUserName'] = $this->common_data['current_user'];
    $data['modifiedDateTime'] = $this->common_data['current_date'];
    

        // No need to update the existing record, just use the provided id
        $masterId = trim($this->input->post('id') ?? '');

        // Insert new record
        $this->load->library('sequence');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['documentID'] = $this->sequence->sequence_generator($data['documentID']);
        
        // Insert into database
        $this->db->insert('srp_erp_asset_request_note', $data);
        $masterId = $this->db->insert_id(); // Get the ID of the inserted record
    

    $this->db->trans_complete();
    if ($this->db->trans_status() === FALSE) {
        $this->session->set_flashdata('e', 'Asset Request Note Save Failed ' . $this->db->_error_message());
        $this->db->trans_rollback();
        return array('status' => false);
    } else {
        $this->session->set_flashdata('s', 'Asset Request Note Saved Successfully.');
        $this->db->trans_commit();
        return array('status' => true, 'reqId' => $masterId);
        
    
// Returning the ID of the asset request note
    }
}

function save_request_details() {
    // Retrieve the masterID from POST data
    $masterID = trim($this->input->post('masterId') ?? '');
    
  
    // Prepare data for details
    $descriptions = $this->input->post('itemdescription');
    $projects = $this->input->post('project');
    $uom = $this->input->post('UOMID');
    $requestedQty = $this->input->post('requestedQty');
    $comments = $this->input->post('comments');




    // Prepare data for update
    $data = array();
    // print_r($masterID);exit();
    foreach ($descriptions as $key  => $value) {
        $data[] = array(
            'itemDescription' => $descriptions[$key],
            'UOMID' => $uom[$key], // Saving UOM ID
            'contractID' => $projects[$key],
            'requestedQTY' => $requestedQty[$key],
            'comments' => $comments[$key],
            'masterID' => $masterID
        );
    }
    $this->db->insert_batch('srp_erp_asset_request_details', $data);

    if ($this->db->affected_rows() > 0) {
        return array('s', 'Details added successfully');
    } else {
        return array('e', 'Error in adding records');
    }
    // $this->db->insert('srp_erp_asset_request_details', $data);

    // if ($this->db->affected_rows() > 0) {
    //     return array('s', 'Details added successfully');
    // } else {
    //     return array('e', 'Error in adding records');
    // }
} 
function fetch_arn_template_data($masterID)
{
        $convertFormat = convert_date_format_sql();
        $this->db->select('id,documentID,reference,DATE_FORMAT(documentDate,\'' . $convertFormat . '\') AS documentDate,requestedByEmpID,requestedByName,comments,confirmedYN,confirmedByEmpID,confirmedByName,approvedYN,approvedByEmpName,DATE_FORMAT(approvedDate,\'' . $convertFormat . ' %h:%i:%s\') AS approvedDate,srp_employeesdetails.ECode as ECode ');
        $this->db->where('id', $masterID);
        $this->db->from('srp_erp_asset_request_note');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_asset_request_note.requestedByEmpID');
        $data['master'] = $this->db->get()->row_array();

        $companyID = $this->common_data['company_data']['company_id'];
        $data['detail'] = $this->db->query("SELECT
        detailsID,
        masterID,
        itemDescription,
        cm.contractCode as contractCode,
        contractID,
        UOMID,
        requestedQTY,
        comments
        FROM
        srp_erp_asset_request_details
        LEFT JOIN 
        srp_erp_contractmaster cm ON cm.contractAutoID = srp_erp_asset_request_details.contractID
        WHERE
        masterID = '$masterID'
        ORDER BY detailsID")->result_array();

        $this->db->select('approvedYN, approvedDate, approvalLevelID,Ename1,Ename2,Ename3,Ename4');
        $this->db->where('documentSystemCode', $masterID);
        $this->db->where('documentID', 'ARN');
        $this->db->from('srp_erp_documentapproved');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.ECode = srp_erp_documentapproved.approvedEmpID');
        $data['approval'] = $this->db->get()->result_array();
        return $data;
}

function update_asset_request()
{
    $masterID = trim($this->input->post('masterID') ?? '');

    $itemDescriptionEdit= $this->input->post('itemDescriptionEdit');
    $uomidEdit= $this->input->post('uomidEdit');
    $contractIDEdit= $this->input->post('contractIDEdit');
    $requestedQTYEdit= $this->input->post('requestedQTYEdit');
    $commentsEdit= $this->input->post('commentsEdit');

    $this->db->trans_start();

    // $data['masterID'] = $masterID;
        $data['itemDescription'] = $itemDescriptionEdit;
        $data['UOMID'] =$uomidEdit;
        $data['contractID'] = $contractIDEdit;
        $data['requestedQTY'] =  $requestedQTYEdit;                     
        $data['comments'] =  $commentsEdit; 

if (trim($this->input->post('detailsID') ?? '')) {
            $this->db->where('detailsID', trim($this->input->post('detailsID') ?? ''));
            $this->db->update('srp_erp_asset_request_details', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Asset Request Detail : Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Asset Request Detail : Updated Successfully.');

            }
        }

}

function delete_asset_request()
{
    $masterID = trim($this->input->post('id') ?? '');
    
    // Check if there are related records in asset request details table
    $this->db->where('masterID', $masterID);
    $datas = $this->db->get('srp_erp_asset_request_details')->row_array();
    
    if ($datas) {
        // If related records exist, set flash message and return true
        $this->session->set_flashdata('e', 'Please delete all detail records before deleting this document.');
        return true;
    } else {
        // No related records found, proceed with deletion
        $this->db->trans_start();
        
        // Fetch document code for further check
        $documentCode = $this->db->get_where('srp_erp_asset_request_note', ['id' => $masterID])->row('documentID');
        
        $length = strlen($documentCode);    
        if ($length > 1) {
            // If document code exists, update the record
            $data = array(
                'isDeleted' => 1,
                'deletedEmpID' => current_userID(),
                'deletedDate' => current_date(),
            );
            $this->db->where('id', $masterID);
            $this->db->update('srp_erp_asset_request_note', $data);
        } else {
            // If no document code exists, delete related records
            $this->db->where('detailsID', $masterID)->delete('srp_erp_asset_request_details');
            $this->db->where('id', $masterID)->delete('srp_erp_asset_request_note');
        }

        $this->db->trans_complete();
        
        // Check transaction status
        if ($this->db->trans_status()) {
            // If transaction successful, set success flash message and return true
            $this->session->set_flashdata('s', 'Deleted Successfully.');
            return true;
        } else {
            // If transaction failed, set error flash message and return false
            $this->session->set_flashdata('e', 'Error in delete process.');
            return false;
        }
    }
 }


function fetch_asset_request_detail()
{
    $this->db->select('*');
    $this->db->where('detailsID', trim($this->input->post('detailsID') ?? ''));
    $this->db->from('srp_erp_asset_request_details');
    return $this->db->get()->row_array();
}

function asset_request_confirmation()
{
    $masterID = trim($this->input->post('masterID') ?? '');
  
    $this->db->select('*');
    $this->db->where('id', trim($this->input->post('masterID') ?? ''));
    $this->db->from('srp_erp_asset_request_note');
    $arn_data = $this->db->get()->row_array();

    $this->db->select('detailsID');
    $this->db->where('masterID', trim($this->input->post('masterID') ?? ''));
    $this->db->from('srp_erp_asset_request_details');
    $detail = $this->db->get()->row_array();

    if($detail){
        $validate_code = validate_code_duplication($arn_data['documentID'], 'documentID', $masterID,'id', 'srp_erp_asset_request_note');
        if(!empty($validate_code)) {
            return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
        }

        $data = array(
            'confirmedYN' => 1,
            'confirmedDate' => $this->common_data['current_date'],
            'confirmedByEmpID' => $this->common_data['current_userID'],
            'confirmedByName' => $this->common_data['current_user'],
        );
        $this->db->where('id', trim($this->input->post('masterID') ?? ''));
        $this->db->update('srp_erp_asset_request_note', $data);
        $this->db->select('managerID');
        $this->db->where('empID', trim($arn_data['requestedByEmpID'] ?? ''));
        $this->db->where('active', 1);
        $this->db->from('srp_erp_employeemanagers');
        $managerid = $this->db->get()->row_array();

        $token_android = firebaseToken($managerid["managerID"], 'android');
        $token_ios = firebaseToken($managerid["managerID"], 'apple');

        $firebaseBody = $arn_data['requestedByEmpName'] . " has applied for an Asset Request.";

        $this->load->library('firebase_notification');
        if(!empty($token_android)) {
            $this->firebase_notification->sendFirebasePushNotification("New Asset Request", $firebaseBody, $token_android, 2, $arn_data['documentID'], "ARN", $masterID, "android");
        }
        if(!empty($token_ios)) {
            $this->firebase_notification->sendFirebasePushNotification("New Asset Request", $firebaseBody, $token_ios, 2, $arn_data['documentID'], "ARN", $masterID, "apple");
        }

        return array('s','Approvals Created Successfully');
    }else{
        return array('e','No records found to confirm this document');
    }

}

function delete_asset_request_detail()
{
    $this->db->delete('srp_erp_asset_request_details', array('detailsID' => trim($this->input->post('detailsID') ?? '')));
    return true;
}

}

