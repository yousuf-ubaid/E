<?php

/**
 * Created by PhpStorm.
 * Date: 7/10/2018
 * Time: 9:53 AM
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Fund_management extends ERP_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Fund_management_model');
        $this->load->helper('fund_management');
    }

    function save_company_details(){
        $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
        $this->form_validation->set_rules('industryID', 'Industry Type', 'required|trim');
        $this->form_validation->set_rules('com_currencyID', 'Currency', 'required|trim');
        $this->form_validation->set_rules('email', 'Email ID', 'required|trim|valid_email');
        $this->form_validation->set_rules('telephone', 'Email ID', 'required|trim');
        $this->form_validation->set_rules('address', 'Address', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]));
        }


        $company_name = $this->input->post('company_name');
        $industryID = $this->input->post('industryID');
        $currencyID = $this->input->post('com_currencyID');
        $comment = $this->input->post('comment');
        $email = $this->input->post('email');
        $telephone = $this->input->post('telephone');
        $fax = $this->input->post('fax');
        $web_site = $this->input->post('web_site');
        $address = $this->input->post('address');
        $postal_code = $this->input->post('postal_code');
        $countryID = $this->input->post('countryID');
        $incomeStatement = $this->input->post('incomeStatement');
        $balanceSheet = $this->input->post('balanceSheet');
        $companyID = current_companyID();
        $dateTime = current_date();

        $company_exit = $this->is_company_exit($company_name);
        if(!empty($company_exit)){
            die( json_encode(['e', 'This company name already exist']));
        }


        $data = [
            'company_name' => $company_name,
            'currencyID' => $currencyID,
            'industryTypesID' => $industryID,
            'address' => $address,
            'postal_code' => $postal_code,
            'countryID' => $countryID,
            'email_id' => $email,
            'tel_no' => $telephone,
            'fax_no' => $fax,
            'web_site' => $web_site,
            'com_comment' => $comment,
            'incomeStatementID' => $incomeStatement,
            'balanceSheet' => $balanceSheet,
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_companymaster', $data);
        $id = $this->db->insert_id();

        echo json_encode(['s', 'Company details updated successfully.', 'id'=>$id]);
    }

    function load_company_data_view(){
        $companyID = current_companyID();
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filterValue') ?? '');
        $doc_status = trim($this->input->post('doc_status') ?? '');

        $search_string = '';
        if (!empty($text)) {
            $search_string = " AND ((company_name LIKE '%" . $text . "%') OR (email_id LIKE '%" . $text . "%') OR (address LIKE '%" . $text . "%')";
            $search_string .= " OR (postal_code LIKE '%" . $text . "%') OR (CountryDes LIKE '%" . $text . "%') OR (tel_no LIKE '%" . $text . "%'))";
        }

        $search_sorting = '';
        if (!empty($sorting) && $sorting != '#') {
            $search_sorting = " AND company_name LIKE '" . $sorting . "%'";
        }

        $doc_status_str = '';

        if($doc_status != 0){
            switch ($doc_status){
                case '1':
                    $doc_status_str = ", fm_pendingDocs('FMC', fmMas.id, {$companyID}, 0) AS docStatus";
                break;

                case '2':
                    $currentDate = date('Y-m-d');
                    $doc_status_str = ", fm_elapseDocs('FMC', fmMas.id, {$companyID}, '{$currentDate}', 0) AS docStatus";
                break;

                case '3':
                    $currentDate = date('Y-m-d');
                    $doc_status_str = ", fm_expiryRemainingDocs('FMC', fmMas.id, {$companyID}, '{$currentDate}', 0) AS docStatus";
                break;
            }
        }

        $query = ($doc_status != 0)? "SELECT * FROM (": "";
        $query .= "SELECT fmMas.*, conMas.CountryDes {$doc_status_str}
                 FROM srp_erp_fm_companymaster fmMas  
                 LEFT JOIN srp_countrymaster conMas ON fmMas.countryID = conMas.countryID
                 WHERE companyID={$companyID} {$search_string} {$search_sorting}
                 ORDER BY fmMas.id DESC";
        $query .= ($doc_status != 0)? ") t1 WHERE docStatus > 0": "";

        $comData = $this->db->query($query)->result_array();

        //echo '<pre>'.$this->db->last_query().'</pre>';
        $data['comData'] = $comData;

        $this->load->view('system/fund-management/ajax/company-table-view', $data);
    }

    function get_company_basic_details(){
        $companyID = current_companyID();
        $id = trim($this->input->post('id') ?? '');

        $where = ['id'=>$id, 'companyID'=>$companyID];
        $result = $this->db->get_where('srp_erp_fm_companymaster', $where)->row_array();

        echo json_encode($result);
    }

    function update_company_details(){
        $this->form_validation->set_rules('editID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('company_name', 'Company Name', 'required|trim');
        $this->form_validation->set_rules('industryID', 'Industry Type', 'required|trim');
        $this->form_validation->set_rules('com_currencyID', 'Currency', 'required|trim');
        $this->form_validation->set_rules('email', 'Email ID', 'required|trim|valid_email');
        $this->form_validation->set_rules('telephone', 'Email ID', 'required|trim');
        $this->form_validation->set_rules('address', 'Address', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]));
        }

        $editID = $this->input->post('editID');
        $company_name = $this->input->post('company_name');
        $industryID = $this->input->post('industryID');
        $currencyID = $this->input->post('com_currencyID');
        $comment = $this->input->post('comment');
        $email = $this->input->post('email');
        $telephone = $this->input->post('telephone');
        $fax = $this->input->post('fax');
        $web_site = $this->input->post('web_site');
        $address = $this->input->post('address');
        $postal_code = $this->input->post('postal_code');
        $countryID = $this->input->post('countryID');
        $incomeStatement = $this->input->post('incomeStatement');
        $balanceSheet = $this->input->post('balanceSheet');
        $companyID = current_companyID();
        $dateTime = current_date();

        $company_exit = $this->is_company_exit($company_name);
        if(!empty($company_exit)){
            if($company_exit != $editID){
                die( json_encode(['e', 'This company name already exist']));
            }
        }



        $oldCurr = $this->db->get_where('srp_erp_fm_companymaster', ['id'=>$editID])->row('currencyID');
        if($oldCurr != $currencyID){
            /*Check investment processed with this company*/
            $result = $this->db->get_where('srp_erp_fm_master', ['invCompanyID'=>$editID])->row('invCompanyID');
            if(!empty($result)){
                die( json_encode(['e', 'Already investment made with this company.<br/>You can not change the currency of this company.']));
            }
        }


        $data = [
            'company_name' => $company_name,
            'currencyID' => $currencyID,
            'industryTypesID' => $industryID,
            'address' => $address,
            'postal_code' => $postal_code,
            'countryID' => $countryID,
            'email_id' => $email,
            'tel_no' => $telephone,
            'fax_no' => $fax,
            'web_site' => $web_site,
            'com_comment' => $comment,
            'incomeStatementID' => $incomeStatement,
            'balanceSheet' => $balanceSheet,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $where = ['id'=>$editID, 'companyID'=>$companyID];
        $this->db->where($where)->update('srp_erp_fm_companymaster', $data);

        echo json_encode(['s', 'Company details updated successfully.', 'id'=>0]);
    }

    function is_company_exit($str){
        $str = trim($str);
        $companyID = current_companyID();
        $where = ['company_name'=>$str, 'companyID'=>$companyID];

        return $this->db->get_where('srp_erp_fm_companymaster', $where)->row('id');
    }

    function is_email_exit($str){
        $str = trim($str);
        $where = ['email'=>$str ];
        return $this->db->get_where('srp_erp_fm_contactdetails', $where)->row('contactID');
    }

    function save_user_data(){
        $this->form_validation->set_rules('masterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('e_name', 'Name', 'required|trim');
        $this->form_validation->set_rules('designationID', 'Designation', 'required|trim');
        $this->form_validation->set_rules('emailID', 'Email ID', 'required|trim|valid_email');
        $this->form_validation->set_rules('user_name', 'User name', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $masterID = $this->input->post('masterID');
        $e_name = $this->input->post('e_name');
        $designationID = $this->input->post('designationID');
        $telNo = $this->input->post('telNo');
        $email = $this->input->post('emailID');
        $user_name = $this->input->post('user_name');
        $password = $this->input->post('password');

        $companyID = current_companyID();
        $dateTime = current_date();

        $email_exit = $this->is_email_exit($email);
        if(!empty($email_exit)){
            die( json_encode(['e', 'This email address already exist']));
        }


        $data = [
            'fm_companyID' => $masterID,
            'contactName' => $e_name,
            'designationID' => $designationID,
            'telNo' => $telNo,
            'email' => $email,
            'userName' => $user_name,
            'password' => $password,
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_contactdetails', $data);

        echo json_encode(['s', 'User details updated successfully.']);
    }

    function update_user_data(){
        $this->form_validation->set_rules('edit_userID', 'User ID', 'required|trim');
        $this->form_validation->set_rules('masterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('e_name', 'Name', 'required|trim');
        $this->form_validation->set_rules('designationID', 'Designation', 'required|trim');
        $this->form_validation->set_rules('emailID', 'Email ID', 'required|trim|valid_email');


        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $edit_userID = $this->input->post('edit_userID');
        $masterID = $this->input->post('masterID');
        $e_name = $this->input->post('e_name');
        $designationID = $this->input->post('designationID');
        $telNo = $this->input->post('telNo');
        $email = $this->input->post('emailID');


        $companyID = current_companyID();
        $dateTime = current_date();

        $email_exit = $this->is_email_exit($email);
        if(!empty($email_exit)){
            if($email_exit != $edit_userID) {
                die(json_encode(['e', 'This email address already exist']));
            }
        }

        $where = ['contactID' => $edit_userID, 'fm_companyID' => $masterID, 'companyID' => $companyID,];

        $data = [
            'contactName' => $e_name,
            'designationID' => $designationID,
            'telNo' => $telNo,
            'email' => $email,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->where($where)->update('srp_erp_fm_contactdetails', $data);

        echo json_encode(['s', 'User details updated successfully.']);
    }

    function get_company_user_details(){
        $companyID = current_companyID();
        $id = trim($this->input->post('id') ?? '');


        $userData = $this->db->query("SELECT conDet.*, desTB.DesDescription
                                    FROM srp_erp_fm_contactdetails conDet
                                    JOIN srp_designation desTB ON desTB.DesignationID = conDet.designationID
                                    WHERE fm_companyID = {$id} AND companyID={$companyID}
                                    ORDER BY conDet.contactID DESC")->result_array();
        $data['userData'] = $userData;

        $this->load->view('system/fund-management/ajax/user-table-view', $data);
    }

    function get_user_basic_details(){
        $companyID = current_companyID();
        $id = trim($this->input->post('uID') ?? '');


        $userData = $this->db->query("SELECT contactName, designationID, telNo, email, userName  
                                      FROM srp_erp_fm_contactdetails
                                      WHERE contactID = {$id} AND companyID={$companyID}")->row_array();

        echo json_encode($userData);
    }

    function save_share_holder_data(){
        $this->form_validation->set_rules('masterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('holderName', 'Holder Name', 'required|trim');
        $this->form_validation->set_rules('percent', 'Percentage', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');


        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $masterID = $this->input->post('masterID');
        $holderName = $this->input->post('holderName');
        $percent = $this->input->post('percent');
        $amount = $this->input->post('amount');
        $companyID = current_companyID();
        $dateTime = current_date();

        if($percent == 0 || $percent > 100){
            die( json_encode(['e', 'Percentage should be greater than zero and lesser than 100']));
        }

        if($amount == 0){
            die( json_encode(['e', 'Amount is not valid']));
        }

        $totPer = $this->db->query("SELECT SUM(sharePercentage) per FROM srp_erp_fm_shareholders WHERE fm_masterID = {$masterID}")->row('per');

        if(($totPer+$percent) > 100){
            die( json_encode(['e', 'Total investment percentage can not be greater than 100.']));
        }


        $data = [
            'fm_masterID' => $masterID,
            'holderName' => $holderName,
            'sharePercentage' => $percent,
            'shareAmount' => $amount,
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_shareholders', $data);

        echo json_encode(['s', 'Share holders details updated successfully.']);
    }

    function get_company_share_holder_details(){
        $companyID = current_companyID();
        $id = trim($this->input->post('id') ?? '');


        $shareData = $this->db->query("SELECT shareID, holderName, sharePercentage, shareAmount, CurrencyCode, DecimalPlaces
                                    FROM srp_erp_fm_shareholders holTB    
                                    JOIN srp_erp_fm_companymaster fmCom ON holTB.fm_masterID = fmCom.id
                                    JOIN srp_erp_currencymaster curTB ON fmCom.currencyID = curTB.currencyID
                                    WHERE fm_masterID = {$id} AND holTB.companyID={$companyID}
                                    ORDER BY shareID DESC")->result_array();

        $data['shareData'] = $shareData;

        $this->load->view('system/fund-management/ajax/share-table-view', $data);
    }

    function get_share_holder_details(){
        $companyID = current_companyID();
        $shareID = trim($this->input->post('shareID') ?? '');


        $shareData = $this->db->query("SELECT holderName, sharePercentage, shareAmount FROM srp_erp_fm_shareholders
                                      WHERE shareID = {$shareID} AND companyID={$companyID}")->row_array();

        echo json_encode($shareData);
    }

    function update_share_data(){
        $this->form_validation->set_rules('edit_shareID', 'Share Holder ID', 'required|trim');
        $this->form_validation->set_rules('masterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('holderName', 'Holder Name', 'required|trim');
        $this->form_validation->set_rules('percent', 'Percentage', 'required|trim');
        $this->form_validation->set_rules('currencyID', 'Currency', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');


        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $edit_shareID = $this->input->post('edit_shareID');
        $masterID = $this->input->post('masterID');
        $holderName = $this->input->post('holderName');
        $percent = $this->input->post('percent');
        $amount = $this->input->post('amount');
        $currencyID = $this->input->post('currencyID');
        $companyID = current_companyID();
        $dateTime = current_date();

        if($amount == 0){
            die( json_encode(['e', 'Amount is not valid']));
        }

        if($percent == 0 || $percent > 100){
            die( json_encode(['e', 'Percentage should be greater than zero and lesser than 100']));
        }


        $totPer = $this->db->query("SELECT SUM(sharePercentage) per FROM srp_erp_fm_shareholders 
                      WHERE fm_masterID = {$masterID} AND shareID != {$edit_shareID}")->row('per');

        if(($totPer+$percent) > 100){
            die( json_encode(['e', 'Total investment percentage can not be greater than 100.']));
        }

        $data = [
            'holderName' => $holderName,
            'sharePercentage' => $percent,
            'shareAmount' => $amount,
            'currencyID' => $currencyID,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $where = ['shareID' => $edit_shareID, 'fm_masterID' => $masterID, 'companyID' => $companyID,];

        $this->db->where($where)->update('srp_erp_fm_shareholders', $data);

        echo json_encode(['s', 'Share holders details updated successfully.']);
    }

    function save_investment_type(){
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('gl_code', 'GL code', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $description = $this->input->post('description');
        $gl_code = $this->input->post('gl_code');
        $companyID = current_companyID();
        $dateTime = current_date();

        $isExist = $this->db->query("SELECT description FROM srp_erp_fm_types WHERE description = '{$description}' AND companyID={$companyID}")->row('description');

        if(!emptY($isExist)){
            die( json_encode(['e', 'This investment type already exist']));
        }


        $data = [
            'description' => $description,
            'glCode' => $gl_code,
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_types', $data);

        echo json_encode(['s', 'New investment type added successfully.']);
    }

    function load_investment_type_view(){
        $companyID = current_companyID();
        $text = trim($this->input->post('searchTask') ?? '');
        $sorting = trim($this->input->post('filterValue') ?? '');

        $search_string = '';
        if (!empty($text)) {
            $search_string = " AND ((description LIKE '%" . $text . "%') OR (GLSecondaryCode LIKE '%" . $text . "%') OR (GLDescription LIKE '%" . $text . "%'))";
        }

        $search_sorting = '';
        if (!empty($sorting) && $sorting != '#') {
            $search_sorting = " AND description LIKE '" . $sorting . "%'";
        }

        $invType = $this->db->query("SELECT invTypeID, description, glCode, GLSecondaryCode,GLDescription   
                                     FROM srp_erp_fm_types ty
                                     LEFT JOIN srp_erp_chartofaccounts ch ON ch.GLAutoID = ty.glCode
                                     WHERE ty.companyID={$companyID} {$search_string} {$search_sorting}
                                     ORDER BY invTypeID DESC")->result_array();
        $data['invType'] = $invType;

        $this->load->view('system/fund-management/ajax/investment-type-table-view', $data);
    }

    function get_investment_type_details(){
        $companyID = current_companyID();
        $invTypeID = trim($this->input->post('invTypID') ?? '');


        $invData = $this->db->query("SELECT invTypeID, description, glCode
                                      FROM srp_erp_fm_types
                                      WHERE invTypeID = {$invTypeID} AND companyID={$companyID}")->row_array();

        echo json_encode($invData);
    }

    function update_investment_type(){
        $this->form_validation->set_rules('edit_invID', 'Type ID', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('gl_code', 'GL code', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $id = $this->input->post('edit_invID');
        $description = $this->input->post('description');
        $gl_code = $this->input->post('gl_code');
        $companyID = current_companyID();
        $dateTime = current_date();

        $isExist = $this->db->query("SELECT description FROM srp_erp_fm_types WHERE description = '{$description}' AND invTypeID!={$id} AND companyID={$companyID}")->row('description');

        if(!emptY($isExist)){
            die( json_encode(['e', 'This investment type already exist']));
        }


        $data = [
            'description' => $description,
            'glCode' => $gl_code,
            'companyID' => $companyID,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $where = ['invTypeID' => $id, 'companyID' => $companyID,];

        $this->db->where($where)->update('srp_erp_fm_types', $data);

        echo json_encode(['s', 'Investment type updated successfully.']);
    }

    public function fetch_investment()
    {
        $convertFormat = convert_date_format_sql();
        $companyFilter = trim($this->input->post('companyFilter') ?? '');

        $companyID = current_companyID();
        $where = 't1.companyID = '.$companyID ;
        $where .= ($companyFilter != '')? ' AND invCompanyID IN ('.$companyFilter.')': '';
        $doc_status = trim($this->input->post('doc_status') ?? '');

        $doc_status_str = '';

        if($doc_status != 0){
            switch ($doc_status){
                case '1':
                    $doc_status_str = ", fm_pendingDocs('FMIT', t1.id, {$companyID},t1.invTypeID) AS docStatus";
                break;

                case '2':
                    $currentDate = date('Y-m-d');
                    $doc_status_str = ", fm_elapseDocs('FMIT', t1.id, {$companyID}, '{$currentDate}',t1.invTypeID) AS docStatus";
                break;

                case '3':
                    $currentDate = date('Y-m-d');
                    $doc_status_str = ", fm_expiryRemainingDocs('FMIT', t1.id, {$companyID}, '{$currentDate}',t1.invTypeID) AS docStatus";
                break;
            }
        }


        $query = "SELECT t1.id AS id, documentCode, t1.invTypeID AS invTypeID, description AS invDes, t1.trCurrencyID AS currencyID, CurrencyCode, 
                  DATE_FORMAT(invDate,'{$convertFormat}') AS invDate, DecimalPlaces, trAmount, disburseAmount, t1.narration AS narration, company_name,
                  t1.companyID {$doc_status_str}
                  FROM srp_erp_fm_master t1
                  JOIN srp_erp_fm_types t2 ON t2.invTypeID=t1.invTypeID
                  JOIN srp_erp_fm_companymaster com ON com.id=t1.invCompanyID
                  JOIN srp_erp_currencymaster t3 ON t3.currencyID=t1.trCurrencyID
                  LEFT JOIN ( 
                       SELECT SUM(disburseAmount) disburseAmount, invMasterID FROM 
                       srp_erp_fm_details WHERE companyID = {$companyID} GROUP BY invMasterID
                  ) det ON det.invMasterID = t1.id
                  WHERE {$where}";

        //echo '<pre>'.$query.'</pre>';

        $this->datatables->select('id as id, documentCode as documentCode, invTypeID as invTypeID, invDes as invDes, currencyID as currencyID, CurrencyCode as CurrencyCode, invDate as invDate, DecimalPlaces, trAmount as trAmount, ROUND(trAmount, 2) as trAmount_search, disburseAmount as disburseAmount, ROUND(disburseAmount, 2) as disburseAmount_search, ROUND((trAmount - disburseAmount), 2) as balance_search, narration as narration, company_name as company_name', false)
            ->from("({$query}) t1")
            ->add_column('investment_det', '$1', 'investment_det(invDate, narration, CurrencyCode)')
            ->add_column('invAmount_str', '$1','investment_amount_det(DecimalPlaces, trAmount, disburseAmount)')
            ->add_column('status', '$1', 'get_attachment_status("FMIT", id, invTypeID)')
            ->add_column('action', '$1', 'investment_master_action(id)');

        if($doc_status != 0){
            $this->datatables->where('docStatus > 0');
        }

        echo $this->datatables->generate();

    }

    public function fetch_investment_old()
    {
        $convertFormat = convert_date_format_sql();
        $companyFilter = trim($this->input->post('companyFilter') ?? '');

        $companyID = current_companyID();
        $where = 't1.companyID = '.$companyID ;
        $where .= ($companyFilter != '')? ' AND invCompanyID IN ('.$companyFilter.')': '';

        $this->datatables->select('t1.id AS id, documentCode, t1.invTypeID AS invTypeID, description AS invDes, t1.trCurrencyID AS currencyID,
                 CurrencyCode, DATE_FORMAT(invDate,\'' . $convertFormat . '\') AS invDate, DecimalPlaces, trAmount, disburseAmount,
                 t1.narration AS narration, company_name', false)
            ->from('srp_erp_fm_master t1')
            ->join('srp_erp_fm_types t2', 't2.invTypeID=t1.invTypeID')
            ->join('srp_erp_fm_companymaster com', 'com.id=t1.invCompanyID')
            ->join('srp_erp_currencymaster t3', 't3.currencyID=t1.trCurrencyID')
            ->join("( SELECT SUM(disburseAmount) disburseAmount, invMasterID FROM 
                       srp_erp_fm_details WHERE companyID = {$companyID} GROUP BY invMasterID
                    ) det", 'det.invMasterID = t1.id', 'left')
            ->add_column('investment_det', '$1', 'investment_det(invDate, narration, CurrencyCode)')
            //->add_column('invAmount_str', '<div style="text-align: right">$1</div>', 'trAmount')
            ->add_column('invAmount_str', '$1','investment_amount_det(DecimalPlaces, trAmount, disburseAmount)')
            ->add_column('status', '$1', 'get_attachment_status("FMIT", id)')
            ->add_column('action', '$1', 'investment_master_action(id)')
            ->where($where);
        echo $this->datatables->generate();
    }

    function save_investment(){
        $this->form_validation->set_rules('inv_company', 'Investment Company', 'required|trim');
        $this->form_validation->set_rules('invType', 'Investment Type', 'required|trim');
        $this->form_validation->set_rules('invDate', 'Investment Date', 'required|trim');
        $this->form_validation->set_rules('currencyID', 'Currency ID', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');
        $this->form_validation->set_rules('narration', 'narration', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $invCompany = $this->input->post('inv_company');
        $invType = $this->input->post('invType');
        $invDate = $this->input->post('invDate');
        $currencyID = $this->input->post('currencyID');
        $amount = $this->input->post('amount');
        $narration = $this->input->post('narration');
        $companyID = current_companyID();
        $dateTime = current_date();
        $inv_DPlace = fetch_currency_desimal_by_id( $currencyID );

        $inv_companyData = $this->db->query("SELECT comTB.currencyID
                                FROM srp_erp_fm_companymaster comTB                                 
                                WHERE comTB.id = {$invCompany}")->row_array();

        $inv_comCurrency = $inv_companyData['currencyID'];
        $invCom_curr_data = currency_conversionID($currencyID, $inv_comCurrency);

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_curr_data = currency_conversionID($currencyID, $localCurrencyID);

        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $rpt_curr_data = currency_conversionID($currencyID, $repCurrencyID);

        $date_format_policy = date_format_policy();
        $invDate = input_format_date($invDate, $date_format_policy);


        //Generate document Code
        $serialNo = $this->db->query("SELECT serialNo FROM srp_erp_fm_master WHERE companyID={$companyID} ORDER BY id DESC LIMIT 1")->row('serialNo');
        $serialNo = ($serialNo != null) ? $serialNo + 1 : 1;
        $this->load->library('sequence');
        $documentCode = $this->sequence->sequence_generator('FMI', $serialNo);


        $data = [
            'invCompanyID' => $invCompany,
            'documentCode' => $documentCode,
            'invTypeID' => $invType,
            'narration' => $narration,
            'trCurrencyID' => $currencyID,
            'trDPlace' => $inv_DPlace,
            'trAmount' => $amount,
            'invDate' => $invDate,
            'invComCurrencyID' => $inv_comCurrency,
            'invComDPlace' => $invCom_curr_data['DecimalPlaces'],
            'invComCurrencyER' => $invCom_curr_data['conversion'],
            'localCurrencyID' => $localCurrencyID,
            'localDPlace' => $com_curr_data['DecimalPlaces'],
            'localCurrencyER' => $com_curr_data['conversion'],
            'rptCurrencyID' => $repCurrencyID,
            'rptDPlace' => $rpt_curr_data['DecimalPlaces'],
            'rptCurrencyER' => $rpt_curr_data['conversion'],
            'companyID' => $companyID,
            'serialNo' => $serialNo,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_master', $data);
        $id = $this->db->insert_id();

        echo json_encode(['s', 'New investment saved successfully.', 'id'=>$id]);
    }

    function investment_master_details(){
        $investID = $this->input->post('investID');
        echo json_encode($this->Fund_management_model->investment_master_details($investID));
    }

    function edit_investment_amount(){
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $amount = trim($this->input->post('value') ?? '');

        $masterData = $this->Fund_management_model->investment_master_details($masterID);
        $disburseTot = $masterData['disbTot'];
        $dPlace = $masterData['trDPlace'];

        if($disburseTot > 0){
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('You can not make changes on this master.Already disburse processed.');
        }

        $amount = str_replace(',', '', $amount);

        if ($amount == '' || $amount == 0 || !is_numeric($amount)) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Amount is not valid.');
        }

        $where = [ 'companyID' => $companyID, 'id' => $masterID, ];
        $dateTime = current_date();
        $data = [
            'trAmount' => round($amount, $dPlace),
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_fm_master', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            $amount = number_format($amount, $dPlace);
            echo json_encode(['s', 'Investment amount updated successfully', 'amount'=> $amount]);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in investment amount update process');
        }
    }

    function edit_investment_narration(){
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $narration = trim($this->input->post('value') ?? '');

        $masterData = $this->Fund_management_model->investment_master_details($masterID);
        $disburseTot = $masterData['disbTot'];


        if($disburseTot > 0){
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('You can not make changes on this master.Already disburse processed.');
        }


        if ($narration == '') {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Narration is not valid.');
        }

        $where = [ 'companyID' => $companyID, 'id' => $masterID, ];
        $dateTime = current_date();
        $data = [
            'narration' => $narration,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_fm_master', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Narration updated successfully']);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in narration update process');
        }
    }

    function edit_investment_date(){
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $invDate = trim($this->input->post('value') ?? '');


        $masterData = $this->Fund_management_model->investment_master_details($masterID);
        $disburseTot = $masterData['disbTot'];

        if($disburseTot > 0){
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('You can not make changes on this master.Already disburse processed.');
        }

        if ($invDate == '' || $invDate == null) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Date is not valid.');
        }

        $where = [ 'companyID' => $companyID, 'id' => $masterID, ];
        $dateTime = current_date();
        $data = [
            'invDate' => $invDate,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_fm_master', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Date updated successfully', 'invDate'=> $invDate]);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in investment date update process');
        }
    }

    function investment_disburse_view(){
        $investID = trim($this->input->post('investID') ?? '');
        $companyID = current_companyID();

        $data['disData'] = $this->db->query("SELECT detID, disburseDate, disburseAmount, disburseBankGL,
                                  chAcc.bankName, chAcc.bankSwiftCode, det.confirmedYN, narration, PVcode 
                                  FROM srp_erp_fm_details det
                                  JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.disburseBankGL
                                  LEFT JOIN srp_erp_paymentvouchermaster pvMas ON pvMas.payVoucherAutoId = det.paymentVoucherID
                                  WHERE det.companyID={$companyID} AND invMasterID={$investID}")->result_array();

        $this->load->view('system/fund-management/ajax/investment-disburse-view', $data);
    }

    function add_disburse(){
        $this->form_validation->set_rules('invMasterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('disburseDate', 'Date', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');
        $this->form_validation->set_rules('bankGL', 'Bank GL', 'required|trim');
        $this->form_validation->set_rules('narration', 'Narration', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $masterID = $this->input->post('invMasterID');
        $disburseDate = $this->input->post('disburseDate');
        $isConfirm = $this->input->post('isConfirm');
        $amount = $this->input->post('amount');
        $bankGL = $this->input->post('bankGL');
        $narration = $this->input->post('narration');
        $companyID = current_companyID();
        $dateTime = current_date();

        if($amount == 0){
            die( json_encode(['e', 'Amount is not valid']));
        }

        $date_format_policy = date_format_policy();
        $disburseDate = input_format_date($disburseDate, $date_format_policy);

        $masterData = $this->Fund_management_model->investment_master_details($masterID);

        $trCurrID = $masterData['currencyID'];
        $inv_DPlace = $masterData['trDPlace'];

        $invAmount = $masterData['trAmount'];
        $invDate = $masterData['invDate'];
        $disbTot = $masterData['disbTot'];

        if($invDate > $disburseDate){
            die( json_encode(['e', 'Disburse date can not be lesser than investment date.']));
        }

        if($invAmount < ($disbTot+$amount)){
            $msg = 'Total disburse amount should be lesser than investment amount.';
            $msg .= '<p>In this investment you can disburse up to '. number_format(($invAmount-$disbTot), $inv_DPlace);

            die( json_encode(['e', $msg]) );
        }

        $inv_comCurrency = $masterData['invComCurrencyID'];
        $invCom_curr_data = currency_conversionID($trCurrID, $inv_comCurrency);

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_curr_data = currency_conversionID($trCurrID, $localCurrencyID);

        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $rpt_curr_data = currency_conversionID($trCurrID, $repCurrencyID);


        $data = [
            'invMasterID' => $masterID,
            'disburseDate' => $disburseDate,
            'disburseAmount' => round($amount, $inv_DPlace),
            'disburseBankGL' => $bankGL,
            'narration' => $narration,
            'trCurrencyID' => $trCurrID,
            'trDPlace' => $inv_DPlace,
            'invComCurrencyID' => $inv_comCurrency,
            'invComDPlace' => $invCom_curr_data['DecimalPlaces'],
            'invComCurrencyER' => $invCom_curr_data['conversion'],
            'localCurrencyID' => $localCurrencyID,
            'localDPlace' => $com_curr_data['DecimalPlaces'],
            'localCurrencyER' => $com_curr_data['conversion'],
            'rptCurrencyID' => $repCurrencyID,
            'rptDPlace' => $rpt_curr_data['DecimalPlaces'],
            'rptCurrencyER' => $rpt_curr_data['conversion'],
            'companyID' => $companyID,
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_begin();

        $this->db->insert('srp_erp_fm_details', $data);

        if($isConfirm == 1){
            $disburseID = $this->db->insert_id();
            $data2 = $data;
            $data2['detailID'] = $disburseID;
            $data2['dateTime'] = $dateTime;
            $result = $this->create_paymentVoucher($masterData, $data2);

            if($result[0] == 'e'){
                $this->db->trans_rollback();

                die( json_encode($result) );
            }
            else{
                $data['paymentVoucherID'] = $result['payVoucherAutoId'];
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = current_userID();
                $data['confirmedDate'] = $dateTime;

                $where = ['detID' => $disburseID];
                $this->db->where($where)->update('srp_erp_fm_details', $data);
            }
        }


        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Disburse details updated successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in disburse.']);
        }
    }

    function update_disburse(){
        $this->form_validation->set_rules('invMasterID', 'Master ID', 'required|trim');
        $this->form_validation->set_rules('disburseID', 'Disburse ID', 'required|trim');
        $this->form_validation->set_rules('disburseDate', 'Date', 'required|trim');
        $this->form_validation->set_rules('amount', 'Amount', 'required|trim');
        $this->form_validation->set_rules('bankGL', 'Bank GL', 'required|trim');
        $this->form_validation->set_rules('narration', 'Narration', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $disburseID = $this->input->post('disburseID');
        $masterID = $this->input->post('invMasterID');
        $isConfirm = $this->input->post('isConfirm');
        $disburseDate = $this->input->post('disburseDate');
        $amount = $this->input->post('amount');
        $bankGL = $this->input->post('bankGL');
        $narration = $this->input->post('narration');
        $companyID = current_companyID();
        $dateTime = current_date();

        $confirmedYN = $this->db->get_where('srp_erp_fm_details', ['detID'=>$disburseID])->row('confirmedYN');

        if($confirmedYN == 1){
            die( json_encode(['e', 'This disburse is already confirmed you can not make changes on this.']));
        }

        if($amount == 0){
            die( json_encode(['e', 'Amount is not valid']));
        }

        $date_format_policy = date_format_policy();
        $disburseDate = input_format_date($disburseDate, $date_format_policy);

        $masterData = $this->Fund_management_model->investment_master_details($masterID, $disburseID);

        $trCurrID = $masterData['currencyID'];
        $inv_DPlace = $masterData['trDPlace'];

        $invAmount = $masterData['trAmount'];
        $invDate = $masterData['invDate'];
        $disbTot = $masterData['disbTot'];

        if($invDate > $disburseDate){
            die( json_encode(['e', 'Disburse date can not be lesser than investment date.']));
        }

        if($invAmount < ($disbTot+$amount)){
            $msg = 'Total disburse amount should be lesser than investment amount.';
            $msg .= '<p>In this investment you can disburse up to '. number_format(($invAmount-$disbTot), $inv_DPlace);

            die( json_encode(['e', $msg]));
        }

        $inv_comCurrency = $masterData['invComCurrencyID'];
        $invCom_curr_data = currency_conversionID($trCurrID, $inv_comCurrency);

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_curr_data = currency_conversionID($trCurrID, $localCurrencyID);

        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $rpt_curr_data = currency_conversionID($trCurrID, $repCurrencyID);



        $data = [
            'disburseDate' => $disburseDate,
            'disburseAmount' => $amount,
            'disburseBankGL' => $bankGL,
            'narration' => $narration,
            'invComCurrencyID' => $inv_comCurrency,
            'invComDPlace' => $invCom_curr_data['DecimalPlaces'],
            'invComCurrencyER' => $invCom_curr_data['conversion'],
            'localCurrencyID' => $localCurrencyID,
            'localDPlace' => $com_curr_data['DecimalPlaces'],
            'localCurrencyER' => $com_curr_data['conversion'],
            'rptCurrencyID' => $repCurrencyID,
            'rptDPlace' => $rpt_curr_data['DecimalPlaces'],
            'rptCurrencyER' => $rpt_curr_data['conversion'],
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $where = ['invMasterID'=>$masterID, 'detID'=>$disburseID, 'companyID'=>$companyID];

        $this->db->trans_begin();


        if($isConfirm == 1){
            //$data['detailID'] = $this->db->insert_id();
            $data2 = $data;
            $data2['detailID'] = $disburseID;
            $data2['dateTime'] = $dateTime;
            $result = $this->create_paymentVoucher($masterData, $data2);

            if($result[0] == 'e'){
                $this->db->trans_rollback();

                die( json_encode($result) );
            }
            else{
                $data['paymentVoucherID'] = $result['payVoucherAutoId'];
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = current_userID();
                $data['confirmedDate'] = $dateTime;

                $this->db->where($where)->update('srp_erp_fm_details', $data);
            }
        }
        else{
            $this->db->where($where)->update('srp_erp_fm_details', $data);
        }

        if($this->db->trans_status() == true){
            $this->db->trans_commit();
            echo json_encode(['s', 'Disburse details updated successfully.']);
        }else{
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in disburse.']);
        }

    }

    function create_paymentVoucher($maserData, $detailData){

        $companyID = current_companyID();
        $dateTime = $detailData['dateTime'];
        $date_text1 = $detailData['disburseDate'];
        $periodStart = date('Y-m-01', strtotime($date_text1));
        $periodEnd = date('Y-m-t', strtotime($date_text1));

        /*Start of payment voucher master */
        $fm_documentCode = $maserData['documentCode'];
        $bankData = $this->db->get_where('srp_erp_chartofaccounts', ['GLAutoID'=>$detailData['disburseBankGL']])->row_array();

        $bank_curr_data = currency_conversionID($maserData['currencyID'], $bankData['bankCurrencyID']);

        $financePeriod_arr = $this->db->select('companyFinancePeriodID, companyFinanceYearID')->from('srp_erp_companyfinanceperiod')
            ->where(
                array(
                    'dateFrom' => $periodStart,
                    'dateTo' => $periodEnd,
                    'isActive' => 1,
                    //'isCurrent' => 1,
                    'companyID' => $companyID
                )
            )->get()->row_array();

        $financeYear_arr = $this->db->select('companyFinanceYearID, beginingDate, endingDate')->from('srp_erp_companyfinanceyear')
            ->where(
                array(
                    'companyFinanceYearID' => $financePeriod_arr['companyFinanceYearID'],
                    'isActive' => 1,
                    //'isCurrent' => 1,
                    'companyID' => $companyID
                )
            )->get()->row_array();

        $financeYearID = $financeYear_arr['companyFinanceYearID'];
        $companyFinanceYear = $financeYear_arr['beginingDate'] .' '.$financeYear_arr['endingDate'];

        $this->load->library('sequence');
        $year = date('Y', strtotime($financeYear_arr['beginingDate']));
        $month = date('m', strtotime($financeYear_arr['beginingDate']));
        $pvCode = $this->sequence->sequence_generator_fin('PV', $financeYearID, $year, $month);

        $pvMaster = [
            'documentID' => 'PV',
            'PVcode' => $pvCode,
            'PVdate' => $detailData['disburseDate'],
            'pvType' => 'Direct',
            'referenceNo' => $fm_documentCode,

            'companyFinanceYearID' => $financeYearID,
            'companyFinanceYear' => $companyFinanceYear,
            'FYBegin' => $financeYear_arr['beginingDate'],
            'FYEnd' => $financeYear_arr['endingDate'],
            'companyFinancePeriodID' => $financePeriod_arr['companyFinancePeriodID'],

            'modeOfPayment' => $bankData['isCash'],
            'paymentType' => ($bankData['isCash'] == 1)? 0: 1,

            'PVbank' => $bankData['bankName'],
            'PVbankCode' => $bankData['GLAutoID'],
            'bankGLAutoID' => $bankData['GLAutoID'],
            'bankSystemAccountCode' => $bankData['systemAccountCode'],
            'bankGLSecondaryCode' => $bankData['GLSecondaryCode'],
            'PVbankBranch' => $bankData['bankBranch'],
            'PVbankSwiftCode' => $bankData['bankSwiftCode'],
            'PVbankAccount' => $bankData['bankAccountNumber'],
            'PVbankType' => $bankData['subCategory'],

            'PVNarration' => $fm_documentCode.' '.$maserData['narration'],
            'partyType' => 'DIR',

            'partyName' => $maserData['company_name'],
            'partyAddress' => $maserData['address'],
            'partyTelephone' => $maserData['tel_no'],
            'partyFax' => $maserData['fax_no'],
            'partyEmail' => $maserData['email_id'],


            'confirmedYN' => 1,
            'confirmedByEmpID' => current_userID(),
            'confirmedByName' => current_employee(),
            'confirmedDate' => $dateTime,

            'transactionCurrencyID' => $maserData['currencyID'],
            'transactionCurrency' => $maserData['CurrencyCode'],
            'transactionExchangeRate' => 1,
            'transactionAmount' => $detailData['disburseAmount'],
            'transactionCurrencyDecimalPlaces' => $maserData['trDPlace'],

            'companyLocalCurrencyID' => $detailData['localCurrencyID'],
            'companyLocalCurrency' => get_currency_code($detailData['localCurrencyID']),
            'companyLocalExchangeRate' => $detailData['localCurrencyER'],
            'companyLocalAmount' => round(($detailData['disburseAmount'] / $detailData['localCurrencyER']), $detailData['localDPlace']),
            'companyLocalCurrencyDecimalPlaces' => $detailData['localDPlace'],

            'companyReportingCurrencyID' => $detailData['rptCurrencyID'],
            'companyReportingCurrency' => get_currency_code($detailData['rptCurrencyID']),
            'companyReportingExchangeRate' => $detailData['rptCurrencyER'],
            'companyReportingAmount' => round(($detailData['disburseAmount'] / $detailData['rptCurrencyER']), $detailData['rptDPlace']),
            'companyReportingCurrencyDecimalPlaces' => $detailData['rptDPlace'],

            'partyCurrencyID' => $detailData['invComCurrencyID'],
            'partyCurrency' => get_currency_code($detailData['invComCurrencyID']),
            'partyExchangeRate' => $detailData['invComCurrencyER'],
            'partyCurrencyAmount' => round(($detailData['disburseAmount'] / $detailData['invComCurrencyER']), $detailData['invComDPlace']),
            'partyCurrencyDecimalPlaces' => $detailData['invComDPlace'],


            'bankCurrencyID' => $bankData['bankCurrencyID'],
            'bankCurrency' =>  $bank_curr_data['CurrencyCode'],
            'bankCurrencyExchangeRate' => $bank_curr_data['conversion'],
            'bankCurrencyAmount' => round(($detailData['disburseAmount'] / $bank_curr_data['conversion']), $bank_curr_data['DecimalPlaces']),
            'bankCurrencyDecimalPlaces' => $bank_curr_data['DecimalPlaces'],

            'companyID' => $companyID,
            'companyCode' => current_companyCode(),
            'createdUserGroup' => current_user_group(),
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_paymentvouchermaster', $pvMaster);
        /*End of payment voucher master */





        /*Start of payment voucher detail */
        $pvMasterID = $this->db->insert_id();
        $invTypeID = $maserData['invTypeID'];
        $inv_data = $this->db->query("SELECT chAcc.* FROM srp_erp_fm_types tyTB
                                     JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID =  tyTB.glCode
                                     WHERE tyTB.invTypeID = {$invTypeID} ")->row_array();

        $pvDet = [
            'payVoucherAutoId' => $pvMasterID,
            'type' => 'GL',
            'referenceNo' => $fm_documentCode,
            'GLAutoID' => $inv_data['GLAutoID'],
            'systemGLCode' => $inv_data['systemAccountCode'],
            'GLCode' => $inv_data['GLSecondaryCode'],
            'GLDescription' => $inv_data['GLDescription'],
            'GLType' => $inv_data['subCategory'],
            'description' => $fm_documentCode.' '.$detailData['narration'],

            'transactionCurrencyID' => $maserData['currencyID'],
            'transactionCurrency' => $maserData['CurrencyCode'],
            'transactionExchangeRate' => 1,
            'transactionAmount' => $detailData['disburseAmount'],
            'transactionCurrencyDecimalPlaces' => $maserData['trDPlace'],

            'companyLocalCurrencyID' => $detailData['localCurrencyID'],
            'companyLocalCurrency' => get_currency_code($detailData['localCurrencyID']),
            'companyLocalExchangeRate' => $detailData['localCurrencyER'],
            'companyLocalAmount' => round(($detailData['disburseAmount'] / $detailData['localCurrencyER']), $detailData['localDPlace']),
            'companyLocalCurrencyDecimalPlaces' => $detailData['localDPlace'],

            'companyReportingCurrencyID' => $detailData['rptCurrencyID'],
            'companyReportingCurrency' => get_currency_code($detailData['rptCurrencyID']),
            'companyReportingExchangeRate' => $detailData['rptCurrencyER'],
            'companyReportingAmount' => round(($detailData['disburseAmount'] / $detailData['rptCurrencyER']), $detailData['rptDPlace']),
            'companyReportingCurrencyDecimalPlaces' => $detailData['rptDPlace'],

            'partyCurrencyID' => $detailData['invComCurrencyID'],
            'partyCurrency' => get_currency_code($detailData['invComCurrencyID']),
            'partyExchangeRate' => $detailData['invComCurrencyER'],
            'partyAmount' => round(($detailData['disburseAmount'] / $detailData['invComCurrencyER']), $detailData['invComDPlace']),
            'partyCurrencyDecimalPlaces' => $detailData['invComDPlace'],


            'companyID' => $companyID,
            'companyCode' => current_companyCode(),
            'createdUserGroup' => current_user_group(),
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_paymentvoucherdetail', $pvDet);

        /*End of payment voucher detail */


        /*Approval create*/
        $this->load->library('approvals');
        $approvals_status = $this->approvals->CreateApproval('PV', $pvMasterID, $pvCode, 'Payment Voucher', 'srp_erp_paymentvouchermaster', 'PayVoucherAutoId');

        if ($approvals_status == 1) {
            return ['s', '', 'payVoucherAutoId' => $pvMasterID];
        }else if($approvals_status==3){
            return ['e', 'There are no users exist to perform approval for this document.'];
        } else {
            return ['e', 'Something went wrong!, In approval creation process'];
        }
    }

    function save_document_setup(){
        $this->form_validation->set_rules('sys_docID', 'Documents Type', 'trim|required');
        $this->form_validation->set_rules('description[]', 'Document', 'trim|required');
        $this->form_validation->set_rules('expiry_alert[]', 'Expiry Alert Before', 'trim|required');

        $sys_docID = $this->input->post('sys_docID');

        if($sys_docID == 'FMIT'){
            $this->form_validation->set_rules('invType', 'Investment Type', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = current_companyID();
        $invType = $this->input->post('invType');
        $description = $this->input->post('description[]');
        $isRequired = $this->input->post('isRequired[]');
        $chk_expireDate = $this->input->post('is_expireDate[]');
        $expiry_alert = $this->input->post('expiry_alert[]');
        $dateTime = current_date();

        $invType = ($sys_docID == 'FMIT') ? $invType: 0;

        $description_str = array_map('trim', $description);
        $description_str = "'".implode("',", $description_str)."'";

        $data = [];
        foreach ($description as $key=>$row){
            $data[] = [
                'description' => $row,
                'systemDocumentID' => $sys_docID,
                'documentSubID' => $invType,
                'isMandatory' => $isRequired[$key],
                'issuedBy_req' => null,
                'expireDate_req' => $chk_expireDate[$key],
                'sendExpiryAlertBefore' => $expiry_alert[$key],
                'companyID' => $companyID,
                'createdUserID' => current_userID(),
                'createdDateTime' => $dateTime,
                'createdPCID' => current_pc(),
                'timestamp' => $dateTime,
            ];
        }

        $this->db->insert_batch('srp_erp_fm_documentsetup', $data);

        echo json_encode(['s', 'Successfully inserted']);

    }

    function fetch_document_setup(){
        $sys_docID = $this->input->post('sys_docID');

        $this->datatables->select('docSetupID, document AS syDescription, t1.description AS docDescription, documentSubID,
                isMandatory, expireDate_req, sendExpiryAlertBefore, systemDocumentID, IFNULL(t3.description, \'-\') AS invDescription')
            ->from('srp_erp_fm_documentsetup AS t1')
            ->join('srp_erp_documentcodes AS t2', 't1.systemDocumentID=t2.documentID')
            ->join('srp_erp_fm_types AS t3', 't1.documentSubID=t3.invTypeID', 'left')
            ->add_column('edit', '$1', 'action_docSetup(docSetupID, doc_Description)')
            ->add_column('mandatory', '<center>$1</center>', 'mandatoryStatus(isMandatory)')
            ->add_column('st_isMandatory', '<center>$1</center>', 'mandatoryStatus(isMandatory)')
            ->add_column('st_expireDate_req', '<center>$1</center>', 'mandatoryStatus(expireDate_req)')
            ->add_column('st_sendNot', '<div style="text-align: right">$1</div>', 'sendExpiryAlertBefore')
            ->where('t1.companyID', current_companyID());

        if(!empty($sys_docID)){
            $this->datatables->where('t1.systemDocumentID', $sys_docID);
        }

        echo $this->datatables->generate();
    }

    function edit_documentDocumentSetup(){
        $this->form_validation->set_rules('setupID', 'Setup ID', 'trim|required');
        $this->form_validation->set_rules('edit_docDescription', 'Document', 'trim|required');
        $this->form_validation->set_rules('expiry_alert', 'Expiry alert date', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $setupID = $this->input->post('setupID');
        $description = $this->input->post('edit_docDescription');
        $isMandatory = ($this->input->post('edit_isMandatory') == 'on') ? 1 : 0;
        $is_expireDate = ($this->input->post('edit_expireDate') == 'on') ? 1 : 0;
        $expiry_alert = $this->input->post('expiry_alert');
        $dateTime = current_date();

        $data = [
            'description' => $description,
            'isMandatory' => $isMandatory,
            'expireDate_req' => $is_expireDate,
            'sendExpiryAlertBefore' => $expiry_alert,
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'modifiedPCID' => current_pc(),
            'timestamp' => $dateTime,
        ];

        $where = [ 'docSetupID'=>$setupID, 'companyID' => current_companyID() ];

        $this->db->where($where)->update('srp_erp_fm_documentsetup', $data);

        echo json_encode(['s', 'Successfully updated ']);
    }

    function delete_documentSetup(){
        $this->form_validation->set_rules('hidden-id', 'Documents ID', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $hidden_id = $this->input->post('hidden-id');

        // Check this description used in document setup uploaded
        $isInUse = $this->db->query("SELECT documentSubID FROM srp_erp_documentattachments WHERE documentSubID={$hidden_id} 
                              AND documentID IN ('FMC', 'FMIT')")->result_array();

        if (!empty($isInUse)) {
            die( json_encode(['e', 'This document setup is used in document attachment.<br/>You can not delete this setup.']) );
        }


        $this->db->trans_start();

        $this->db->where('docSetupID', $hidden_id)->delete('srp_erp_fm_documentsetup');

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            die( json_encode(['s', 'Records deleted successfully']) );
        } else {
            $this->db->trans_rollback();
            die( json_encode(['e', 'Error in deleting process']) );
        }

    }

    function get_attachment_details(){
        $companyID = current_companyID();
        $systemDocumentID = trim($this->input->post('systemDocumentID') ?? '');
        $documentSystemCode = trim($this->input->post('documentSystemCode') ?? '');
        $docSubID = 0;

        if($systemDocumentID == 'FMIT'){
            $docSubID = $this->db->get_where('srp_erp_fm_master', ['id'=>$documentSystemCode])->row('invTypeID');
        }

        $attachData = get_attachment_details($systemDocumentID, $documentSystemCode, $docSubID);

        $data['attachData'] = $attachData;

        $this->load->view('system/fund-management/ajax/company-attachment-view', $data);
    }

    function document_upload(){
        $this->form_validation->set_rules('docSysID', 'Document Setup ID', 'trim|required');

        $docSysID = $this->input->post('docSysID');
        $systemDocType = '';

        if(!empty($docSysID)){
            $req_field = $this->db->get_where('srp_erp_fm_documentsetup', ['docSetupID'=>$docSysID])->row_array();
            $systemDocType = $req_field['systemDocumentID'];

            if($req_field['expireDate_req'] == 1){
                $this->form_validation->set_rules('expireDate', 'Expire Date', 'trim|required');
            }

            $validationColumn = ($systemDocType == 'FMC')? 'Company ID': 'Investment ID';

            $this->form_validation->set_rules('documentSystemCode', $validationColumn, 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }

        $companyID = current_companyID();
        $documentSystemCode = $this->input->post('documentSystemCode');
        $expireDate = $this->input->post('expireDate');
        $dateTime = current_date();

        $date_format_policy = date_format_policy();
        $expireDate = (!empty($expireDate)) ? input_format_date($expireDate, $date_format_policy) : null;

        //Check is there is a document already added for this company
        $where = ['documentID' => $systemDocType, 'documentSubID' => $docSysID, 'documentSystemCode' => $documentSystemCode];
        $isExisting = $this->db->where($where)->select('documentID')->from('srp_erp_documentattachments')->get()->row('documentID');

        if (!empty($isExisting)) {
            die( json_encode(['e', 'This document has been updated already']) );
        }

        $path = UPLOAD_PATH_POS . 'documents/';
        $fileName = $systemDocType.'_'.$documentSystemCode. '_' . time();
        $fileName = str_replace(' ', '', strtolower($fileName));
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);


        if (!$this->upload->do_upload("doc_file")) {
            die( json_encode( ['e', 'Upload failed ' . $this->upload->display_errors(), 'path' => $path] ) );
        }


        $data = array(
            'documentID' => $systemDocType,
            'documentSubID' => $docSysID,
            'documentSystemCode' => $documentSystemCode,
            'attachmentDescription' => '',
            'myFileName' => $this->upload->data('file_name'),
            'docExpiryDate' => $expireDate,

            'companyID' => $companyID,
            'companyCode' => current_companyCode(),
            'createdUserGroup' => current_user_group(),
            'createdUserID' => current_userID(),
            'createdUserName' => current_employee(),
            'createdDateTime' => $dateTime,
            'createdPCID' => current_pc(),
            'timestamp' => $dateTime,
        );


        $this->db->insert('srp_erp_documentattachments', $data);

        if ($this->db->affected_rows() > 0) {
            die( json_encode( ['s', 'Document successfully uploaded'] ) );
        } else {
            die( json_encode( ['e', 'Error in document upload'] ) );
        }
    }

    function update_attachmentDetails(){
        $this->form_validation->set_rules('attachID', 'Attachment ID', 'trim|required');
        $this->form_validation->set_rules('docSysID', 'Document Setup ID', 'trim|required');
        $this->form_validation->set_rules('documentSystemCode', 'Company ID', 'trim|required');

        $docSysID = $this->input->post('docSysID');
        $systemDocType = '';
        if(!empty($docSysID)){
            $req_field = $this->db->get_where('srp_erp_fm_documentsetup', ['docSetupID'=>$docSysID])->row_array();
            $systemDocType = $req_field['systemDocumentID'];
            if($req_field['expireDate_req'] == 1){
                $this->form_validation->set_rules('edit_expiryDate', 'Expire Date', 'trim|required');
            }
        }

        if ($this->form_validation->run() == FALSE) {
            die( json_encode( ['e', validation_errors()] ) );
        }

        $companyID = current_companyID();
        $attachID = $this->input->post('attachID');
        $expireDate = $this->input->post('edit_expiryDate');
        $dateTime = current_date();

        $date_format_policy = date_format_policy();
        $expireDate = (!empty($expireDate)) ? input_format_date($expireDate, $date_format_policy) : null;

        $data = array(
            'docExpiryDate' => $expireDate,
            'modifiedUserID' => current_userID(),
            'modifiedUserName' => current_employee(),
            'modifiedDateTime' => $dateTime,
            'modifiedPCID' => current_pc(),
            'timestamp' => $dateTime,
        );

        $where = ['documentID' => $systemDocType, 'attachmentID' => $attachID, 'companyID' => $companyID];
        $this->db->where($where)->update('srp_erp_documentattachments', $data);

        die( json_encode( ['s', 'Details updated successfully'] ) );
    }

    function get_document_status_more_details(){
        $sysType = $this->input->post('sysType');
        $documentSystemCode = $this->input->post('documentSystemCode');
        $statusType = $this->input->post('statusType');

        $str  = '';
        $result = get_attachment_details($sysType, $documentSystemCode);


        if (!empty($result)) {

            $primaryLanguage = getPrimaryLanguage();
            $this->lang->load('fn_management', $primaryLanguage);
            $this->lang->load('common', $primaryLanguage);

            switch ($statusType){
                case 'pending':
                    foreach ($result as $row) {
                        if (empty($row['myFileName']) && $row['isMandatory'] == 1) {
                            $str .= ' <b>-</b> '. $row['description'].'<br/>';
                        }

                    }
                break;

                case 'elapse':
                    $status = $this->lang->line('fn_man_elapsed');
                    $days_str = $this->lang->line('common_days');
                    foreach ($result as $row) {
                        $expiryDate = $row['docExpiryDate'];
                        $today = date('Y-m-d');
                        if ($expiryDate != null && ($today > $expiryDate)) {
                            $date1 = new DateTime($expiryDate);
                            $date2 = new DateTime($today);
                            $diff = $date2->diff($date1)->format("%a");
                            $elapse = intval($diff);

                            $str .= ' <b>-</b> '. $row['description'].' : &nbsp; &nbsp; <b>'.$status.' '.$elapse.' '.$days_str.'</b><br/>';
                        }
                    }
                break;

                case 'expiry':
                    $status = $this->lang->line('fn_man_expiry_remain');
                    $days_str = $this->lang->line('common_days');
                    foreach ($result as $row) {
                        $expiryDate = $row['docExpiryDate'];

                        if ($expiryDate != null) {
                            $today = date('Y-m-d');
                            $date1 = new DateTime($expiryDate);
                            $date2 = new DateTime($today);
                            $diff = $date2->diff($date1)->format("%a");
                            $remainingDays = intval($diff);

                            if ($today < $expiryDate) {
                                $sendExpiryAlertBefore = $row['sendExpiryAlertBefore'];
                                if($sendExpiryAlertBefore > 0 && $remainingDays <= $sendExpiryAlertBefore){
                                    $str .= ' <b>-</b> '. $row['description'].' : &nbsp; &nbsp; <b>'.$status.' '.$remainingDays.' '.$days_str.'</b><br/>';
                                }
                            }
                        }
                    }
                break;

                default: $str = '';
            }
        }

        echo $str;
    }

    function save_financial(){
        $this->form_validation->set_rules('inv_company', 'Company ID', 'required|trim');
        $this->form_validation->set_rules('reportID', 'Statement', 'required|trim');
        $this->form_validation->set_rules('periodYear', 'Year', 'required|trim');
        $this->form_validation->set_rules('periodMonth', 'Month', 'required|trim');
        $this->form_validation->set_rules('narration', 'Narration', 'required|trim');


        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $fm_company = $this->input->post('inv_company');
        $periodYear = $this->input->post('periodYear');
        $periodMonth = $this->input->post('periodMonth');
        $reportID = $this->input->post('reportID');
        $narration = $this->input->post('narration');
        $companyID = current_companyID();
        $dateTime = current_date();
        $fn_period = date('Y-m-d', strtotime($periodYear.'-'.$periodMonth.'-01'));

        //Is there is a submission already exists
        $where = [
            'fm_companyID' => $fm_company,
            'fn_period' => $fn_period,
            'reportID' => $reportID
        ];

        $isSubmitted = $this->db->get_where('srp_erp_fm_financialsmaster', $where)->row('documentCode');

        if(!empty($isSubmitted)){
            die( json_encode(['e', 'There is a submission already exists on this period <br/>[ '.$isSubmitted.' ].']) );
        }

        $companyData = $this->db->get_where('srp_erp_fm_companymaster', ['id'=>$fm_company])->row_array();
        $key = ($reportID == 5)? 'incomeStatementID': 'balanceSheet';
        $fm_currencyID = $companyData['currencyID'];
        $templateID = $companyData[$key];

        if($templateID == 0){
            $reportDescription = $this->db->get_where('srp_erp_reporttemplate', ['reportID'=>$reportID])->row('reportDescription');
            $msg = $reportDescription.' default template not found in '.$companyData['company_name'];
            die( json_encode(['e', $msg]) );
        }

        //Generate document Code
        $serialNo = $this->db->query("SELECT serialNo FROM srp_erp_fm_financialsmaster WHERE companyID={$companyID} ORDER BY id DESC LIMIT 1")->row('serialNo');
        $serialNo = ($serialNo != null) ? $serialNo + 1 : 1;
        $this->load->library('sequence');
        $documentCode = $this->sequence->sequence_generator('FMF', $serialNo);

        $localCurrencyID = $this->common_data['company_data']['company_default_currencyID'];
        $com_curr_data = currency_conversionID($fm_currencyID, $localCurrencyID);

        $repCurrencyID = $this->common_data['company_data']['company_reporting_currencyID'];
        $rpt_curr_data = currency_conversionID($fm_currencyID, $repCurrencyID);

        $data = [
            'documentCode' => $documentCode,
            'fm_companyID' => $fm_company,
            'fn_period' => $fn_period,
            'templateID' => $templateID,
            'reportID' => $reportID,
            'submissionDate' => $dateTime,
            'serialNo' => $serialNo,
            'trCurrencyID' => $fm_currencyID,
            'trCurrencyDPlace' => fetch_currency_desimal_by_id($fm_currencyID),
            'localCurrencyID' => $localCurrencyID,
            'localDPlace' => $com_curr_data['DecimalPlaces'],
            'localCurrencyER' => $com_curr_data['conversion'],
            'rptCurrencyID' => $repCurrencyID,
            'rptDPlace' => $rpt_curr_data['DecimalPlaces'],
            'rptCurrencyER' => $rpt_curr_data['conversion'],
            'narration' => $narration,
            'companyID' => $companyID,
            'documentID' => 'FMF',
            'createdPCID' => current_pc(),
            'createdUserID' => current_userID(),
            'createdDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->insert('srp_erp_fm_financialsmaster', $data);
        $id = $this->db->insert_id();

        echo json_encode(['s', 'Financial header details saved successfully.', 'id'=>$id]);
    }

    function fetch_financial(){
        $convertFormat = convert_date_format_sql();
        $companyFilter = trim($this->input->post('companyFilter') ?? '');

        $companyID = current_companyID();
        $where = 't1.companyID = '.$companyID ;
        $where .= ($companyFilter != '')? ' AND fm_companyID IN ('.$companyFilter.')': '';

        $this->datatables->select('t1.id AS id, t1.documentCode AS documentCode, company_name,  DATE_FORMAT(fn_period, \'%Y %b\') AS fn_period, t1.narration AS narration,
              t3.reportDescription AS reportDes, DATE_FORMAT(submissionDate,\''.$convertFormat.'\') AS submissionDate, CurrencyCode, t1.confirmedYN as confirmedYN')
            ->from('srp_erp_fm_financialsmaster AS t1')
            ->join('srp_erp_fm_companymaster AS t2', 't1.fm_companyID=t2.id')
            ->join('srp_erp_reporttemplate AS t3', 't1.reportID=t3.reportID')
            ->join('srp_erp_currencymaster AS t4', 't1.trCurrencyID=t4.currencyID')
            ->add_column('action', '$1', 'financial_master_action(id, confirmedYN)')
            ->where($where);

        if(!empty($sys_docID)){
            $this->datatables->where('t1.systemDocumentID', $sys_docID);
        }

        echo $this->datatables->generate();
    }

    function financial_master_details(){
        $fin_ID = trim($this->input->post('fin_ID') ?? '');

        $masterData = $this->Fund_management_model->financial_master_details($fin_ID);

        if(empty($masterData)){
            die( json_encode(['e', 'Master record not found']));
        }

        echo json_encode(['s', 'masterData'=>$masterData]);
    }

    function finance_template_view(){
        $fin_ID = trim($this->input->post('fin_ID') ?? '');
        $masterData = $this->Fund_management_model->financial_master_details($fin_ID);
        //echo '<pre>'; print_r($masterData); echo '</pre>';
        echo load_fm_template($fin_ID, $masterData);
    }

    function edit_submission_narration(){
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $narration = trim($this->input->post('value') ?? '');

        $masterData = $this->Fund_management_model->financial_master_details($masterID);

        if($masterData['confirmedYN'] == 1){
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('You can not make changes on this master.Already confirmed.');
        }

        if ($narration == '') {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Narration is not valid.');
        }

        $where = [ 'companyID' => $companyID, 'id' => $masterID, ];
        $dateTime = current_date();
        $data = [
            'narration' => $narration,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_fm_financialsmaster', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            echo json_encode(['s', 'Narration updated successfully']);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in narration update process');
        }
    }

    function edit_submission_date(){
        $companyID = current_companyID();
        $masterID = $this->input->get('masterID');
        $submissionDate = trim($this->input->post('value') ?? '');

        $masterData = $this->Fund_management_model->financial_master_details($masterID);


        if($masterData['confirmedYN'] == 1){
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('You can not make changes on this master.Already confirmed.');
        }

        if ($submissionDate == '' || $submissionDate == null) {
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Date is not valid.');
        }

        $where = [ 'companyID' => $companyID, 'id' => $masterID, ];
        $dateTime = current_date();
        $data = [
            'submissionDate' => $submissionDate,
            'modifiedPCID' => current_pc(),
            'modifiedUserID' => current_userID(),
            'modifiedDateTime' => $dateTime,
            'timestamp' => $dateTime
        ];

        $this->db->trans_start();
        $this->db->where($where)->update('srp_erp_fm_financialsmaster', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(['s', 'Date updated successfully', 'submissionDate'=> $submissionDate]);
        } else {
            $this->db->trans_rollback();
            header($_SERVER['SERVER_PROTOCOL'] . '', true, 500);
            die('Error in date update process');
        }
    }

    function update_financialData(){
        $fin_ID = trim($this->input->post('fin_ID') ?? '');
        $glID = trim($this->input->post('glID') ?? '');
        $amount = trim($this->input->post('amount') ?? '');

        $masterData = $this->Fund_management_model->financial_master_details($fin_ID);

        if($masterData['confirmedYN'] == 1){
            die( json_encode(['e', 'You can  not make change on this document.<br/>This document is already confirmed']) );
        }


        $template = $masterData['templateID'];
        $dPlace = $masterData['trCurrencyDPlace'];
        //echo '<pre>'; print_r($masterData); echo '</pre>';

        $amount = round($amount, $masterData['trCurrencyDPlace']);

        $where = ['documentMasterAutoID'=>$fin_ID, 'GLAutoID'=>$glID];
        $recordExists = $this->db->get_where('srp_erp_fm_financialdetails', $where)->row_array();

        $this->db->trans_start();

        $record_type = '';

        if(!empty($recordExists)){
            $amount_type = 'cr';

            $record_type = $recordExists['GLType'];
            if( in_array($record_type, ['PLI', 'PLE']) ) { //Income or Expense
                if ($record_type == 'PLI') { //Income
                    $amount_type = ($amount > 0) ? $amount_type : 'dr';
                    $amount = $amount * -1;
                } else { //Expense (PLE)
                    $amount_type = ($amount > 0) ? 'dr' : $amount_type;
                    $amount = $amount * -1;
                }
            }
            else{ //Assets (BSA) or Liability (BSE)
                $amount_type = ($amount > 0) ? 'dr' : $amount_type;
            }

            $data['amount_type'] = $amount_type;
            $data['transactionAmount'] = $amount;
            $data['companyLocalAmount'] = round(($amount/$masterData['localCurrencyER']), $masterData['localDPlace']);
            $data['companyLocalCurrencyDecimalPlaces'] = $masterData['localDPlace'];
            $data['companyReportingAmount'] = round(($amount/$masterData['rptCurrencyER']), $masterData['rptDPlace']);
            $data['partyCurrencyAmount'] = $amount;
            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = current_date(true);
            $data['modifiedUserName'] = current_user();
            $data['timestamp'] = current_date(true);

            $this->db->where(['generalLedgerAutoID'=>$recordExists['generalLedgerAutoID']])
                ->update('srp_erp_fm_financialdetails', $data);
        }
        else{

            $accountData = $this->db->query("SELECT systemAccountCode, GLSecondaryCode, GLDescription, subCategory
                                             FROM srp_erp_chartofaccounts WHERE GLAutoID = {$glID}")->row_array();

            $data['documentCode'] = 'FMF';
            $data['documentMasterAutoID'] = $fin_ID;
            $data['documentSystemCode'] = $masterData['documentCode'];
            $data['documentType'] = '';
            $data['documentDate'] = date('Y-m-t', strtotime($masterData['fn_period_org']));
            $data['documentYear'] = date('Y', strtotime($masterData['fn_period_org']));
            $data['documentMonth'] = date('m', strtotime($masterData['fn_period_org']));
            $data['projectID'] = null;
            $data['projectExchangeRate'] = 1;
            $data['documentNarration'] = $masterData['narration'];
            $data['GLAutoID'] = $glID;
            $data['systemGLCode'] = $accountData['systemAccountCode'];
            $data['GLCode'] = $accountData['GLSecondaryCode'];
            $data['GLDescription'] = $accountData['GLDescription'];
            $data['GLType'] = $accountData['subCategory']; // PLI => income , PLE => expense

            $amount_type = 'cr';

            $record_type = $accountData['subCategory'];
            if( in_array($record_type, ['PLI', 'PLE']) ) { //Income or Expense
                if ($record_type == 'PLI') { //Income
                    $amount_type = ($amount > 0) ? $amount_type : 'dr';
                    $amount = $amount * -1;
                } else { //Expense (PLE)
                    $amount_type = ($amount > 0) ? 'dr' : $amount_type;
                    $amount = $amount * -1;
                }
            }
            else{ //Assets (BSA) or Liability (BSE)
                $amount_type = ($amount > 0) ? 'dr' : $amount_type;
            }


            $data['amount_type'] = $amount_type;

            $data['transactionCurrencyID'] = $masterData['trCurrencyID'];
            $data['transactionCurrency'] = get_currency_code($masterData['trCurrencyID']);
            $data['transactionExchangeRate'] = 1;
            $data['transactionAmount'] = $amount;
            $data['transactionCurrencyDecimalPlaces'] = $masterData['trCurrencyDPlace'];
            $data['companyLocalCurrencyID'] = $masterData['localCurrencyID'];
            $data['companyLocalCurrency'] = get_currency_code($masterData['localCurrencyID']);
            $data['companyLocalExchangeRate'] = $masterData['localCurrencyER'];
            $data['companyLocalAmount'] = round(($amount/$masterData['localCurrencyER']), $masterData['localDPlace']);
            $data['companyLocalCurrencyDecimalPlaces'] = $masterData['localDPlace'];
            $data['companyReportingCurrencyID'] = $masterData['rptCurrencyID'];
            $data['companyReportingCurrency'] = get_currency_code($masterData['rptCurrencyID']);
            $data['companyReportingExchangeRate'] = $masterData['rptCurrencyER'];
            $data['companyReportingAmount'] = round(($amount/$masterData['rptCurrencyER']), $masterData['rptDPlace']);
            $data['companyReportingCurrencyDecimalPlaces'] =  $masterData['rptDPlace'];
            $data['partyType'] = 'Others';
            $data['partyAutoID'] = $masterData['fm_companyID'];
            $data['partySystemCode'] = null;
            $data['partyName'] = $masterData['company_name'];
            $data['partyCurrencyID'] = $masterData['trCurrencyID'];
            $data['partyCurrency'] = get_currency_code($masterData['trCurrencyID']);
            $data['partyExchangeRate'] = 1;
            $data['partyCurrencyAmount'] = $amount;
            $data['partyCurrencyDecimalPlaces'] = $masterData['trCurrencyDPlace'];
            /*$data['segmentID'] = 0;
            $data['segmentCode'] = 0;*/
            $data['companyID'] = current_companyID();
            $data['companyCode'] = current_companyCode();
            $data['createdUserGroup'] = current_user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = current_date(true);
            $data['createdUserName'] = current_user();
            $data['timestamp'] = current_date(true);

            $this->db->insert('srp_erp_fm_financialdetails', $data);
        }

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            $group_glData = $this->db->query("SELECT detID, glAutoID FROM (
                                            SELECT detID, subCategory
                                            FROM srp_erp_companyreporttemplatedetails det
                                            JOIN srp_erp_companyreporttemplatelinks link ON det.detID = link.templateDetailID
                                            WHERE companyReportTemplateID = {$template} AND itemType = 3
                                        ) gData
                                        JOIN srp_erp_companyreporttemplatelinks glData ON glData.templateDetailID = gData.subCategory 
                                        ORDER BY detID")->result_array();
            $group_glData = array_group_by($group_glData, 'detID');

            $tot_arr = [];
            foreach ($group_glData as $key=>$item){
                $where_in_array = array_column($item, 'glAutoID');
                $where_in = implode(',', $where_in_array);
                $amount_str = (in_array($record_type, ['PLI', 'PLE']))? 'IFNULL(transactionAmount,0) * -1': 'IFNULL(transactionAmount,0)';
                $amount = $this->db->query("SELECT FORMAT( SUM({$amount_str}), transactionCurrencyDecimalPlaces) trAmount
                                            FROM srp_erp_fm_financialdetails 
                                            WHERE documentMasterAutoID = {$fin_ID} AND GLAutoID IN ({$where_in})")->row('trAmount');
                $amount = (empty($amount))? number_format(0, $dPlace): $amount;
                $tot_arr[$key] = $amount;
            }
            echo json_encode(['s', 'Updated successfully.', 'tot_arr'=>$tot_arr]);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function delete_financial(){
        $id = $this->input->post('delID');
        $masterData = $this->Fund_management_model->financial_master_details($id);

        if($masterData['confirmedYN'] == 1){
            die( json_encode(['e', 'You can  not make change on this document.<br/>This document is already confirmed']) );
        }

        $this->db->trans_start();

        $this->db->delete('srp_erp_fm_financialdetails', ['documentMasterAutoID'=> $id]);
        $this->db->delete('srp_erp_fm_financialsmaster', ['id'=> $id]);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Deleted successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }
    }

    function finance_submission_print(){
        $fin_ID = trim($this->uri->segment(3));
        $returnType = $this->input->post('returnType');
        $masterData = $this->Fund_management_model->financial_master_details($fin_ID);
        $tBody = '';

        if($masterData['docType'] == 'FIN_BS'){
            $tBody = load_fm_balance_sheet_template($fin_ID, $masterData, $print=1);
        }
        else{
            $tBody = load_fm_template($fin_ID, $masterData, $print=1);
        }

        $data['tBody'] = $tBody;
        $data['masterData'] = $masterData;
        $data['returnType'] = $returnType;

        $html = $this->load->view('system/fund-management/print/finance-submission-print', $data, true);
        //die($html);
        if($returnType == 'view'){
            echo $html;
        }else{
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4');
        }
    }

    function submission_confirm(){
        $this->form_validation->set_rules('fin_ID', 'Master ID', 'required|trim');
        if ($this->form_validation->run() == FALSE) {
            die( json_encode(['e', validation_errors()]) );
        }

        $companyID = current_companyID();
        $masterID = $this->input->post('fin_ID');
        $masterData = $this->Fund_management_model->financial_master_details($masterID);
        if($masterData['confirmedYN'] == 1){
            die( json_encode(['e', 'This document is already confirmed']) );
        }

        $temMasterID = $masterData['templateID'];

        $this->db->select('detID, description, itemType, sortOrder')
            ->from('srp_erp_companyreporttemplatedetails') ->where('companyReportTemplateID',$temMasterID)
            ->where('masterID IS NULL')->where('companyID',$companyID) ->order_by('sortOrder');
        $data = $this->db->get()->result_array();

        $unassigned = '';

        foreach ($data as $row){

            $subMasterID = $row['detID'];

            if($row['itemType'] == 2){
                $subData = $this->db->query("SELECT detID, description, itemType, sortOrder
                                           FROM srp_erp_companyreporttemplatedetails det
                                           WHERE masterID = {$subMasterID} ORDER BY sortOrder")->result_array();

                foreach ($subData as $sub_row){
                    $detID = $sub_row['detID'];
                    if($sub_row['itemType'] == 1){ /*Sub category*/

                        $glData = $this->db->query("SELECT linkID, det.glAutoID, sortOrder, templateDetailID, trAmount,
                                    CONCAT(systemAccountCode, ' - ',GLSecondaryCode, ' - ',GLDescription) as glData
                                    FROM srp_erp_companyreporttemplatelinks det
                                    JOIN srp_erp_chartofaccounts chAcc ON chAcc.GLAutoID = det.glAutoID
                                    LEFT JOIN (
                                        SELECT GLAutoID, (transactionAmount * -1) trAmount 
                                        FROM srp_erp_fm_financialdetails WHERE documentMasterAutoID = {$masterID}
                                    ) fnData ON fnData.GLAutoID = det.glAutoID
                                    WHERE templateDetailID = {$detID} AND trAmount IS NULL  ORDER BY sortOrder")->result_array();


                        foreach ($glData as $gl_row){
                            $glAutoID = $gl_row['glData'];
                            $unassigned .= '<br/>'.$glAutoID;
                        }
                    }
                }
            }
        }

        if($unassigned != ''){
            die( json_encode(['m', 'Please update the values for following GL\'s.<br/>'.$unassigned]) );
        }

        $validate_code = validate_code_duplication($masterData['documentCode'], 'documentCode', $masterID,'id', 'srp_erp_fm_financialsmaster');
        if(!empty($validate_code)) {
            echo json_encode(['e', 'The document Code Already Exist.(' . $validate_code . ')']);
        }

        $dateTime = current_date(true);

        $updData = [
            'confirmedYN' => 1,
            'confirmedByEmpID' => current_userID(),
            'confirmedByName' => current_employee(),
            'confirmedDate' => $dateTime,
            'timestamp' => $dateTime
        ];


        $where = ['companyID'=>$companyID, 'id'=>$masterID];

        $this->db->trans_start();

        $this->db->where($where)
            ->update('srp_erp_fm_financialsmaster', $updData);

        $this->db->trans_complete();
        if($this->db->trans_status() == true){
            echo json_encode(['s', 'Updated successfully.']);
        }else{
            echo json_encode(['e', 'Error in process.']);
        }

        /*$this->load->library('approvals'); // Document Confirmation also created with the approvals library
        $approvals_status = $this->approvals->CreateApproval('FMF', $masterID, $masterData['documentCode'], 'Financial Submission Approval', 'srp_erp_fm_financialsmaster', 'id');

        if ($approvals_status == 3) {
            echo json_encode(['w', 'There is no user exists to perform Financial submission approval for this company.']);
        } elseif ($approvals_status == 1) {
            echo json_encode(['s', 'Create Approval : ' . $masterData['documentCode']]);
        } else {
            echo json_encode(['w', 'some thing went wrong', $approvals_status]);
        }*/
    }

    function load_dropDown_submission_years(){
        $fm_companyID = $this->input->post('fm_companyID');

        $sYears = $this->db->query("SELECT DATE_FORMAT(fn_period, '%Y') AS sYears FROM srp_erp_fm_financialsmaster 
                            WHERE fm_companyID = {$fm_companyID} AND confirmedYN = 1 GROUP BY YEAR(fn_period) ")->result_array();

        $str = '<select name="submission_year" class="form-control select2" id="submission_year">';
        $str .= '<option value="">Select a year</option>';
        if(!empty($sYears)){
            foreach ($sYears as $row){
                $str .= '<option value="'.$row['sYears'].'">'.$row['sYears'].'</option>';
            }
        }
        $str .= '</select>';

        echo $str;
    }

    function load_income_statement_view(){
        $this->form_validation->set_rules('inv_company', 'Company', 'required|trim');
        $this->form_validation->set_rules('submission_year', 'Year', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $msg = '<div class="alert alert-warning" role="alert">'.validation_errors().'</div>';
            die( $msg );
        }

        $inv_company = $this->input->post('inv_company');
        $periodYear = $this->input->post('submission_year');
        $returnType = 'view';

        $masterData = $this->db->query("SELECT id, fn_period, templateID, MONTH(fn_period) sMonth,
                       trCurrencyDPlace AS trDPlace 
                       FROM srp_erp_fm_financialsmaster
                       WHERE fm_companyID = {$inv_company} AND YEAR(fn_period) = {$periodYear} 
                       AND confirmedYN = 1 ORDER BY MONTH(fn_period)")->result_array();

        if(empty($masterData)){
            $msg = '<div class="alert alert-warning" role="alert">No result found.</div>';
            die( $msg );
        }

        $temMasterID = $masterData[0]['templateID'];
        $dPlace = $masterData[0]['trDPlace'];

        $masterData = array_group_by($masterData, 'sMonth');
        $tBody = load_fm_statement_report($masterData, $periodYear, $temMasterID, $dPlace);

        $data['company_name'] = $this->db->get_where('srp_erp_fm_companymaster', ['id'=>$inv_company])->row('company_name');
        $data['year'] = $periodYear;
        $data['tBody'] = $tBody;
        $data['returnType'] = $returnType;

        $html = $this->load->view('system/fund-management/reports/income-statement-rpt-ajax', $data, true);

        if($returnType == 'view'){
            echo $html;
        }else{
            $this->load->library('pdf');
            $this->pdf->printed($html, 'A4');
        }

    }
}
