<?php

class Company_model extends ERP_Model
{
    function save_company_master()
    {
        $company_link_id = trim($this->session->userdata("company_link_id"));
        $branch_link_id = trim($this->session->userdata("branchID"));
        $this->db->trans_start();

        $data['company_code'] = trim($this->input->post('companycode') ?? '');
        $data['company_name'] = trim($this->input->post('companyname') ?? '');
        $data['company_start_date'] = trim($this->input->post('companystartdate') ?? '');
        $data['company_url'] = trim($this->input->post('companyurl') ?? '');
        $data['legalName'] = trim_desc($this->input->post('legalname'));
        $data['textIdentificationNo'] = trim($this->input->post('txtidntificationno') ?? '');
        $data['taxCardNo'] = trim($this->input->post('taxCardNo') ?? '');
        $data['textYear'] = trim($this->input->post('textyear') ?? '');
        if ($this->input->post('industry')) {
            $industry = explode(' | ', $this->input->post('industry') ?? '');
            $data['industryID'] = $industry[0];
            $data['industry'] = $industry[1];
        }
        $data['company_email'] = trim($this->input->post('companyemail') ?? '');
        $data['companyVatNumber'] = trim($this->input->post('vatnumber') ?? '');
        $data['companySVatNumber'] = trim($this->input->post('svatnumber') ?? '');
        $data['vatRegisterYN'] = trim($this->input->post('vatRegister') ?? '');
        $data['registration_no'] = trim($this->input->post('registration_no') ?? '');
        $data['company_phone'] = trim($this->input->post('companyphone') ?? '');
        $data['company_address1'] = trim($this->input->post('companyaddress1') ?? '');
        $data['company_address2'] = trim($this->input->post('companyaddress2') ?? '');
        $data['company_city'] = trim($this->input->post('companycity') ?? '');
        $data['company_province'] = trim($this->input->post('companyprovince') ?? '');
        $data['company_postalcode'] = trim($this->input->post('companypostalcode') ?? '');
        $data['company_country'] = trim($this->input->post('companycountry') ?? '');
        $data['default_segment'] = trim($this->input->post('default_segment') ?? '');
        $data['email_token'] = trim($this->input->post('emailToken') ?? '');
        $data['modifiedPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $data['modifiedUserID'] = trim($this->session->userdata("empID"));
        $data['modifiedUserName'] = trim($this->session->userdata("username"));
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');
        $supportToken = trim($this->input->post('supportToken') ?? '');

        $companyID = trim($this->input->post('companyid') ?? '');
        if ($companyID) {
            $this->db->where('company_id', $companyID);
            $this->db->update('srp_erp_company', $data);
            
            $db2 = $this->load->database('db2', TRUE);
            $db2->where('company_id', $companyID)->update('srp_erp_company', [
                'company_business_name' => $data['company_name'],
                'supportToken'=>$supportToken
            ]);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Company : ' . $data['company_name'] . ' Update Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Company : ' . $data['company_name'] . ' Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('companyid'));
            }
        } 
        else {
            $data['company_logo'] = $data['company_code'] . '.png';
            $data['company_default_currency'] = trim($this->input->post('company_default_currency') ?? '');
            $data['company_default_decimal'] = fetch_currency_desimal($this->input->post('company_default_currency'));
            $data['company_reporting_currency'] = trim($this->input->post('company_reporting_currency') ?? '');
            $data['company_reporting_decimal'] = fetch_currency_desimal($this->input->post('company_reporting_currency'));
            $data['company_link_id'] = $company_link_id;
            $data['branch_link_id'] = $branch_link_id;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $data['createdUserID'] = trim($this->session->userdata("empID"));
            $data['createdUserName'] = trim($this->session->userdata("username"));
            $data['createdDateTime'] = date('Y-m-d h:i:s');
            $this->db->insert('srp_erp_company', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('e', 'Company : ' . $data['company_name'] . '  Saved Failed ' . $this->db->_error_message());
                $this->db->trans_rollback();
                return array('status' => false);
            } else {
                $this->session->set_flashdata('s', 'Company : ' . $data['company_name'] . ' Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_company_control_account()
    {
        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_companycontrolaccounts')->result_array();
    }

    function save_company_control_accounts()
    {
        $this->db->trans_start();
        $company_id = trim($this->input->post('companyid') ?? '');
        $this->cache->delete('000002_' . $company_id);
        $this->db->delete('srp_erp_companycontrolaccounts', array('companyID' => $company_id));
        $APA_dec = explode('|', $this->input->post('APA_dec'));
        $data[0]['accountType'] = 'Accounts Payable';
        $data[0]['accountCode'] = 'APA';
        $data[0]['GLCode'] = trim($APA_dec[0] ?? '');
        $data[0]['accountDescription'] = trim($APA_dec[1] ?? '');
        $data[0]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[0]['companyCode'] = $this->common_data['company_data']['company_code'];
        $ARA_dec = explode('|', $this->input->post('ARA_dec'));
        $data[1]['accountType'] = 'Accounts Receivable';
        $data[1]['accountCode'] = 'ARA';
        $data[1]['GLCode'] = trim($ARA_dec[0] ?? '');
        $data[1]['accountDescription'] = trim($ARA_dec[1] ?? '');
        $data[1]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[1]['companyCode'] = $this->common_data['company_data']['company_code'];
        $INVA_dec = explode('|', $this->input->post('INVA_dec'));
        $data[2]['accountType'] = 'Inventory Control';
        $data[2]['accountCode'] = 'INVA';
        $data[2]['GLCode'] = trim($INVA_dec[0] ?? '');
        $data[2]['accountDescription'] = trim($INVA_dec[1] ?? '');
        $data[2]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[2]['companyCode'] = $this->common_data['company_data']['company_code'];
        $ACA_dec = explode('|', $this->input->post('ACA_dec'));
        $data[3]['accountType'] = 'Asset Control Account';
        $data[3]['accountCode'] = 'ACA';
        $data[3]['GLCode'] = trim($ACA_dec[0] ?? '');
        $data[3]['accountDescription'] = trim($ACA_dec[1] ?? '');
        $data[3]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[3]['companyCode'] = $this->common_data['company_data']['company_code'];
        $PCA_dec = explode('|', $this->input->post('PCA_dec'));
        $data[4]['accountType'] = 'Payroll Control Account';
        $data[4]['accountCode'] = 'PCA';
        $data[4]['GLCode'] = trim($PCA_dec[0] ?? '');
        $data[4]['accountDescription'] = trim($PCA_dec[1] ?? '');
        $data[4]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[4]['companyCode'] = $this->common_data['company_data']['company_code'];
        $UGRV_dec = explode('|', $this->input->post('UGRV_dec'));
        $data[5]['accountType'] = 'Unbilled GRV';
        $data[5]['accountCode'] = 'UGRV';
        $data[5]['GLCode'] = trim($UGRV_dec[0] ?? '');
        $data[5]['accountDescription'] = trim($UGRV_dec[1] ?? '');
        $data[5]['companyID'] = $this->common_data['company_data']['company_id'];
        $data[5]['companyCode'] = $this->common_data['company_data']['company_code'];

        $this->db->insert_batch('srp_erp_companycontrolaccounts', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Company Control Accounts  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Company Control Accounts  Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function load_company_header()
    {
        $this->db->select('*');
        $this->db->where('company_id', $this->input->post('companyid'));
        return $this->db->get('srp_erp_company')->row_array();
    }

    function load_company_api_urls()
    {
        $this->db->select('*');
        $this->db->where('company_id', $this->input->post('companyid'));
        return $this->db->get('srp_erp_company')->row_array();
    }

    function get_company_config_details()
    {
        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_documentcodemaster')->result_array();
    }

    function save_state()
    {
        $this->db->trans_start();
        $data['stateDescription'] = $this->input->post('state');
        $this->db->insert('srp_erp_state', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'State : ' . $data['stateDescription'] . '  Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'State : ' . $data['stateDescription'] . ' Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function save_control_account()
    {
        $this->db->trans_start();
        $data['GLSecondaryCode'] = $this->input->post('GLSecondaryCode');
        $data['GLDescription'] = $this->input->post('GLDescription');
        $this->db->where('controlAccountsAutoID', $this->input->post('controlAccountsAutoID'));
        $this->db->update('srp_erp_companycontrolaccounts', $data);
        $this->db->where('GLAutoID', $this->input->post('GLAutoID'));
        $this->db->update('srp_erp_chartofaccounts', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Control Account : ' . $data['GLDescription'] . '  Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Control Account : ' . $data['GLDescription'] . ' Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_chartofcontrol_account()
    {
        $this->db->trans_start();
        $account_type = explode('|', trim($this->input->post('account_type') ?? ''));
        $data['accountCategoryTypeID'] = trim($this->input->post('accountCategoryTypeID') ?? '');
        $data['masterCategory'] = trim($account_type[0] ?? '');
        $data['subCategory'] = trim($account_type[1] ?? '');
        $data['CategoryTypeDescription'] = trim($account_type[2] ?? '');
        $data['GLSecondaryCode'] = trim($this->input->post('GLSecondaryCode') ?? '');
        $data['GLDescription'] = trim($this->input->post('GLDescription') ?? '');
        $data['masterAutoID'] = trim($this->input->post('masterAccount') ?? '');
        $data['isBank'] = trim($this->input->post('isBank') ?? '');

        $master_account = explode('|', trim($this->input->post('masterAccount_dec') ?? ''));


        $data['masterAccount'] = trim($master_account[0] ?? '');
        $data['masterAccountDescription'] = trim($master_account[2] ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->load->library('sequence');
        $this->load->library('Approvals');
        $data['isActive'] = 1;
        $data['controllaccountYN'] = 1;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['companyCode'] = $this->common_data['company_data']['company_code'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['systemAccountCode'] = $this->sequence->sequence_generator($data['subCategory']);

        $data['confirmedYN'] = 1;
        $data['confirmedbyEmpID'] = $this->common_data['current_userID'];
        $data['confirmedbyName'] = $this->common_data['current_user'];
        $data['confirmedDate'] = $this->common_data['current_date'];

        $data['approvedYN'] = 1;
        $data['approvedbyEmpID'] = $this->common_data['current_userID'];
        $data['approvedbyEmpName'] = $this->common_data['current_user'];
        $data['approvedDate'] = $this->common_data['current_date'];
        $data['approvedComment'] = 'Auto approved';
        $this->db->insert('srp_erp_chartofaccounts', $data);
        $last_id = $this->db->insert_id();
        //$status = $this->approvals->CreateApproval('GL', $last_id, $data['systemAccountCode'], 'Chart Of Accont', 'srp_erp_chartofaccounts', 'GLAutoID',1);
        /*        if (!$status) {
                    $data['approvedYN'] = 1;
                    $data['approvedbyEmpID'] = $this->common_data['current_userID'];
                    $data['approvedbyEmpName'] = $this->common_data['current_user'];
                    $data['approvedDate'] = $this->common_data['current_date'];
                    $data['approvedComment'] = 'Auto approved';
                    $this->db->where('GLAutoID', $last_id);
                    $this->db->update('srp_erp_chartofaccounts', $data);
                    //`srp_erp_companycontrolaccounts`
                }*/
        $this->db->insert('srp_erp_companycontrolaccounts', array('controlAccountType' => '-', 'controlAccountDescription' => $data['GLDescription'], 'GLAutoID' => $last_id, 'systemAccountCode' => $data['systemAccountCode'], 'GLSecondaryCode' => $data['GLSecondaryCode'], 'GLDescription' => $data['GLDescription'], 'companyID' => $data['companyID'], 'companyCode' => $data['companyCode']));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', 'Ledger  : ' . $data['GLDescription'] . ' Save Failed ');
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Ledger : ' . $data['GLDescription'] . ' Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }

    }

    function company_image_upload()
    {
        $this->load->library('s3');
        $comapnyid = trim($this->input->post('faID') ?? '');

        $itemimageexist = $this->db->query("SELECT
	company_logo 
FROM
	`srp_erp_company`
	WHERE
	company_id = $comapnyid ")->row_array();

        if (!empty($itemimageexist)) {
            $this->s3->delete('images/logo/' . $itemimageexist['company_logo']);
        }

        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'com_' . trim($this->input->post('faID') ?? '') . '_' . $this->common_data['company_data']['company_code'] . '_' . time() . '.' . 'png';

        $file = $_FILES['files'];
        if ($file['error'] == 1) {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types)) {
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");

        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 5) {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $path = "images/logo/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }


        /* $output_dir = "images/logo/";
         if (!file_exists($output_dir)) {
             mkdir("images/logo/", 0744);
         }*/

        /*$attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'com_' . trim($this->input->post('faID') ?? '') . '_'.time().'.' . 'png';
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['company_logo'] = $fileName;

        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', "Image Upload Failed." . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Image uploaded  Successfully.');
            $this->db->trans_commit();
            return array('status' => true,'image'=> $fileName);
        }*/


        /*$path = UPLOAD_PATH .base_url().$output_dir;
        $fileName = 'com_' . trim($this->input->post('faID') ?? '') . '_'.time().'.' . 'png';
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);*/
        //empImage is  => $_FILES['empImage']['name'];
        $this->db->trans_start();
        $data['company_logo'] = $fileName;
        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        $data['company_logo'] = $fileName;
        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);
        return array('s', $fileName);
    }

    function update_company_codes_prefixChange()
    {
        $get = $this->input->post('financeTable');
        $codeID = $this->input->post('codeID');
        $prefix = $this->input->post('prefix');
        $serialno = $this->input->post('serialno');
        $format_length = $this->input->post('format_length');
        $approvalLevel = $this->input->post('approvalLevel');
        $approvalType = $this->input->post('approvalType');
        $format_1 = $this->input->post('format_1');
        $format_2 = $this->input->post('format_2');
        $format_3 = $this->input->post('format_3');
        $format_4 = $this->input->post('format_4');
        $format_5 = $this->input->post('format_5');
        $format_6 = $this->input->post('format_6');
        $printHeaderFooterYN = $this->input->post('printHeaderFooterYN');

        if (isset($get)) {
            $this->db->trans_start();
            $table = 'srp_erp_financeyeardocumentcodemaster';
            foreach ($codeID as $key => $codeAutoID) {
                $data = array(
                    'prefix' => $prefix[$key],
                    'serialNo' => $serialno[$key],
                    'formatLength' => $format_length[$key],
                    'approvalLevel' => $approvalLevel[$key],
                    'format_1' => $format_1[$key],
                    'format_2' => $format_2[$key],
                    'format_3' => $format_3[$key],
                    'format_4' => $format_4[$key],
                    'format_5' => $format_5[$key],
                    'format_6' => $format_6[$key],
                );
                $this->db->where('codeID', $codeAutoID);
                $this->db->update($table, $data);
            }
        } else {
            $this->db->trans_start();
            $table = 'srp_erp_documentcodemaster';
            foreach ($codeID as $key => $codeAutoID) {
                $data = array(
                    'prefix' => $prefix[$key],
                    'serialNo' => $serialno[$key],
                    'formatLength' => $format_length[$key],
                    'approvalLevel' => $approvalLevel[$key],
                    'format_1' => $format_1[$key],
                    'format_2' => $format_2[$key],
                    'format_3' => $format_3[$key],
                    'format_4' => $format_4[$key],
                    'format_5' => $format_5[$key],
                    'format_6' => $format_6[$key],
                    'printHeaderFooterYN' => $printHeaderFooterYN[$key],
                    'approvalType' => $approvalType[$key],

                );
                $this->db->where('codeID', $codeAutoID);
                $this->db->update($table, $data);
            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Company Codes :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Company Codes :  Updated Successfully.');
        }
    }

    function update_company_url_change()
    {
        
        $data['api_create_url'] = trim($this->input->post('createurl') ?? '');
        $data['api_update_url'] = trim($this->input->post('updateurl') ?? '');
        $companyID = trim($this->input->post('companyid') ?? '');
        
        if ($companyID) {
            $this->db->where('company_id', $companyID);
            $this->db->update('srp_erp_company', $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Company Urls :  Update Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Company Urls :  Updated Successfully.');
            }
        } 
    }

    function currency_validation()
    {
        $status = TRUE;
        $data_array = array();
        $this->db->select('CurrencyCode');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('currencyID', $this->input->post('CurrencyID'));
        $currency_data = $this->db->get('srp_erp_companycurrencyassign')->row_array();
        $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
        $this->db->where('subCurrencyID', $this->common_data['company_data']['company_default_currencyID']);
        $currency_default = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
        if (empty($currency_default)) {
            $status = FALSE;
        }
        $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
        $this->db->where('subCurrencyID', $this->common_data['company_data']['company_reporting_currencyID']);
        $currency_reporting = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
        if (empty($currency_reporting)) {
            $status = FALSE;
        }

        $party_status = FALSE;
        $currency_party = array();
        $party_currency_code = null;
        if ($this->input->post('partyAutoID')) {
            $party_status = TRUE;
            if ($this->input->post('partyType') == 'SUP') {
                $this->db->select('supplierCurrencyID,supplierCurrency');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('supplierAutoID', $this->input->post('partyAutoID'));
                $party_data = $this->db->get('srp_erp_suppliermaster')->row_array();
                $party_currency_id = $party_data['supplierCurrencyID'];
                $party_currency_code = $party_data['supplierCurrency'];
            } else {
                $this->db->select('customerCurrencyID,customerCurrency');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('customerAutoID', $this->input->post('partyAutoID'));
                $party_data = $this->db->get('srp_erp_customermaster')->row_array();
                $party_currency_id = $party_data['customerCurrencyID'];
                $party_currency_code = $party_data['customerCurrency'];
            }
            $this->db->select('masterCurrencyCode,subCurrencyCode,conversion');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('masterCurrencyID', $this->input->post('CurrencyID'));
            $this->db->where('subCurrencyID', $party_currency_id);
            $currency_party = $this->db->get('srp_erp_companycurrencyconversion')->row_array();
            if (empty($currency_party)) {
                $status = FALSE;
            }
        }

        $data_array['status'] = $status;
        $data_array['data']['default'] = $currency_default;
        $data_array['data']['reporting'] = $currency_reporting;
        $data_array['data']['party_status'] = $party_status;
        $data_array['data']['party'] = $currency_party;
        $data_array['data']['rpt'] = $this->common_data['company_data']['company_reporting_currency'];
        $data_array['data']['def'] = $this->common_data['company_data']['company_default_currency'];
        $data_array['data']['par'] = $party_currency_code;
        $data_array['data']['currency'] = $currency_data['CurrencyCode'];
        return $data_array;
    }

    function update_Serialization()
    {
        $codeID = $this->input->post('codeID');
        $isFYBasedSerialNo = $this->input->post('isFYBasedSerialNo');
        $data = array(
            'isFYBasedSerialNo' => $isFYBasedSerialNo,
        );
        $this->db->where('codeID', $codeID);
        $result = $this->db->update('srp_erp_documentcodemaster', $data);
        if ($result == 1) {
            return array('s', 'Updated Successfully.');
        }
    }

    function update_approval_types()
    {
        $codeID = $this->input->post('codeID');
        $approvalType = $this->input->post('approvalType');
        $data = array(
            'approvalType' => $approvalType,
        );
        $this->db->where('codeID', $codeID);
        $result = $this->db->update('srp_erp_documentcodemaster', $data);
        if ($result == 1) {
            return array('s', 'Updated Successfully.');
        }
    }

    function add_missing_document_code()
    {
        $companyID = current_companyID();
        $financeyearID = $this->input->post('financeyearID');
        $result = $this->db->query("SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $financeyearID as financeyearID

FROM
	srp_erp_documentcodemaster dcm
WHERE
companyID= $companyID AND isFYBasedSerialNo=1 AND
	documentID NOT IN (
		SELECT documentID from srp_erp_financeyeardocumentcodemaster where companyID=$companyID and financeyearID=$financeyearID)")->row_array();

        if (!empty($result)) {
            $results = $this->db->query("INSERT INTO srp_erp_financeyeardocumentcodemaster
(
documentID,
document,
prefix,
startSerialNo,
serialNo,
formatLength,
approvalLevel,
format_1,
format_2,
format_3,
format_4,
format_5,
format_6,
companyID,
financeyearID
)
 (SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $financeyearID as financeyearID

FROM
	srp_erp_documentcodemaster dcm
WHERE
companyID=$companyID AND isFYBasedSerialNo=1 AND
	documentID NOT IN (
		SELECT documentID from srp_erp_financeyeardocumentcodemaster where companyID=$companyID and financeyearID=$financeyearID))");
            if ($results) {
                return array('s', 'Documents Codes Added Successfully');
            } else {
                return array('e', '');
            }
        } else {
            return array('e', '');
        }

    }

    function add_missing_document_code_location()
    {
        $companyID = current_companyID();
        $location = $this->input->post('location');
        $financeyearid = $this->input->post('financeyearlocation');

        if ($financeyearid != 0) {
            $result = $this->db->query("SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $location as locationID,
 $financeyearid AS financeyearID
FROM
	srp_erp_documentcodemaster dcm
		 JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `dcm`.`documentID`
WHERE
companyID= $companyID
  AND `isApprovalDocument` = 1
  AND isFYBasedSerialNo = 1
 AND
	dcm.documentID NOT IN (
		SELECT documentID from srp_erp_locationdocumentcodemaster where companyID=$companyID and locationID = $location and financeyearID = $financeyearid)")->row_array();

            if (!empty($result)) {
                $results = $this->db->query("INSERT INTO srp_erp_locationdocumentcodemaster
(
documentID,
document,
prefix,
startSerialNo,
serialNo,
formatLength,
approvalLevel,
format_1,
format_2,
format_3,
format_4,
format_5,
format_6,
companyID,
locationID,
financeyearID
)
 (SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $location as locationID,
  $financeyearid AS financeyearID
FROM
	srp_erp_documentcodemaster dcm
	 JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `dcm`.`documentID`
WHERE
companyID=$companyID
 AND `isApprovalDocument` = 1
AND isFYBasedSerialNo = 1
 AND
	dcm.documentID NOT IN (
		SELECT documentID from srp_erp_locationdocumentcodemaster where companyID=$companyID and locationID= $location and financeyearID = $financeyearid))");
                if ($results) {
                    return array('s', 'Documents Codes Added Successfully');

                } else {
                    return array('e', '');
                }
            } else {
                return array('e', '');
            }
        } else {
            $result = $this->db->query("SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $location as locationID,
  $financeyearid as financeYearID
FROM
	srp_erp_documentcodemaster dcm
		 JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `dcm`.`documentID`
WHERE
companyID= $companyID
  AND `isApprovalDocument` = 1
 AND
	dcm.documentID NOT IN (
		SELECT documentID from srp_erp_locationdocumentcodemaster where companyID=$companyID and locationID = $location and financeYearID = $financeyearid )")->row_array();

            if (!empty($result)) {
                $results = $this->db->query("INSERT INTO srp_erp_locationdocumentcodemaster
(
documentID,
document,
prefix,
startSerialNo,
serialNo,
formatLength,
approvalLevel,
format_1,
format_2,
format_3,
format_4,
format_5,
format_6,
companyID,
locationID,
financeYearID
)
 (SELECT
	dcm.documentID,
  dcm.document,
  dcm.prefix,
  0 as startSerialNo,
  0 as serialNo,
  6 as formatLength,
  approvalLevel as approvalLevel,
  'prefix' as format_1,
  '/' as format_2,
  'yyyy' as format_3,
  '/' as format_4,
  'mm' as format_5,
  '/' as format_6,
 companyID as companyID,
 $location as locationID,
  $financeyearid as financeYearID
FROM
	srp_erp_documentcodemaster dcm
	 JOIN `srp_erp_documentcodes` ON `srp_erp_documentcodes`.`documentID` = `dcm`.`documentID`
WHERE
companyID=$companyID
 AND `isApprovalDocument` = 1
 AND
	dcm.documentID NOT IN (
		SELECT documentID from srp_erp_locationdocumentcodemaster where companyID=$companyID and locationID= $location and financeYearID = $financeyearid))");
                if ($results) {
                    return array('s', 'Documents Codes Added Successfully');

                } else {
                    return array('e', '');
                }
            } else {
                return array('e', '');
            }
        }


    }

    function update_company_location_codes_prefixChange()
    {
        $get = $this->input->post('locationTable');
        $codeID = $this->input->post('codeID');
        $prefix = $this->input->post('prefix');
        $serialno = $this->input->post('serialno');
        $format_length = $this->input->post('format_length');
        $approvalLevel = $this->input->post('approvalLevel');
        $format_1 = $this->input->post('format_1');
        $format_2 = $this->input->post('format_2');
        $format_3 = $this->input->post('format_3');
        $format_4 = $this->input->post('format_4');
        $format_5 = $this->input->post('format_5');
        $format_6 = $this->input->post('format_6');
        $printHeaderFooterYN = $this->input->post('printHeaderFooterYN');

        if (isset($get)) {
            $this->db->trans_start();
            $table = 'srp_erp_locationdocumentcodemaster';
            foreach ($codeID as $key => $codeAutoID) {
                $data = array(
                    'prefix' => $prefix[$key],
                    'serialNo' => $serialno[$key],
                    'formatLength' => $format_length[$key],
                    'approvalLevel' => $approvalLevel[$key],
                    'format_1' => $format_1[$key],
                    'format_2' => $format_2[$key],
                    'format_3' => $format_3[$key],
                    'format_4' => $format_4[$key],
                    'format_5' => $format_5[$key],
                    'format_6' => $format_6[$key],
                );
                $this->db->where('codeID', $codeAutoID);
                $this->db->update($table, $data);
            }
        } else {
            $this->db->trans_start();
            $table = 'srp_erp_documentcodemaster';
            foreach ($codeID as $key => $codeAutoID) {
                $data = array(
                    'prefix' => $prefix[$key],
                    'serialNo' => $serialno[$key],
                    'formatLength' => $format_length[$key],
                    'approvalLevel' => $approvalLevel[$key],
                    'format_1' => $format_1[$key],
                    'format_2' => $format_2[$key],
                    'format_3' => $format_3[$key],
                    'format_4' => $format_4[$key],
                    'format_5' => $format_5[$key],
                    'format_6' => $format_6[$key],
                    'printHeaderFooterYN' => $printHeaderFooterYN[$key],

                );
                $this->db->where('codeID', $codeAutoID);
                $this->db->update($table, $data);
            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Company Codes :  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Company Codes :  Updated Successfully.');
        }
    }

    function document_code_location_finacebasedup()
    {
        $this->db->trans_start();
        $locationid = $this->input->post('location');
        $financeYearID = $this->input->post('finaceyear');
        $table = 'srp_erp_locationdocumentcodemaster';

        $data['financeYearID'] = $financeYearID;
        $this->db->where('locationID', $locationid);
        $this->db->update($table, $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Finance Year:  Update Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Finance Year :  Updated Successfully.');
        }
    }

    function insert_system_audit_log_nav()
    {
        $db2 = $this->load->database('db2', TRUE);

        $isGroupUser = $this->common_data['isGroupUser'];
        if ($isGroupUser == 1) {
            return false;
            /*$company_id = current_companyID();
            $user_id = current_userID();
            $user_company = $this->db->get_where('srp_employeesdetails', ['EIdNo'=>$user_id])->row('Erp_companyID');

            if($user_company != $company_id){
                return false;
            }*/
        }

        $navigationMenuID = $this->input->post('navigationMenuID');
        $transactionType = $this->input->post('transactionType');
        $documentID = $this->input->post('documentID');


        $dataAdit['empID'] = $this->common_data['current_userID'];
        $dataAdit['transactionType'] = $transactionType;
        $dataAdit['navigationMenuID'] = $navigationMenuID;
        $dataAdit['documentID'] = $documentID;
        $dataAdit['companyID'] = $this->common_data['company_data']['company_id'];
        $dataAdit['remarks'] = 'Opened ' . $documentID;
        $dataAdit['createdUserID'] = $this->common_data['current_userID'];
        $dataAdit['createdUserName'] = $this->common_data['current_user'];
        $dataAdit['createdPCID'] = $this->common_data['current_pc'];
        $dataAdit['createdDateTime'] = fetch_current_time_by_timezone($this->common_data['company_data']['company_id']);
        $db2->insert('system_audit_log', $dataAdit);
    }

    function QHSE_api_requests($data, $req_url, $is_put=false){
        $companyID = current_companyID();
        $db2 = $this->load->database('db2', TRUE);
        $authorization = $db2->query("SELECT `key` FROM `keys` WHERE company_id = {$companyID} AND key_type = 'QHSE'")->row('key');

        if(empty($authorization)){
            return ['status'=> 'e', 'message'=> 'QHSE authorization key not configured.'];
        }

        $site_url = $this->config->item('QHSE_login_url');
        if(empty($site_url)){
            return ['status'=> 'e', 'message'=> 'QHSE site url not configured.' ];
        }

        $site_url .= $req_url;

        $headers = [
            'Authorization: QHSE '.$authorization,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt( $ch, CURLOPT_URL, $site_url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, ($is_put)? 'PUT': 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
        $response = curl_exec ( $ch );
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $msg = "<br/>" . curl_error($ch);
            return [ 'status'=> 'e', 'message'=> $msg, 'http_code'=> $http_code ];
        }
        curl_close ( $ch );

        if(!in_array($http_code, ['200', '401', '404', '422', '500'])){
            return [ 'status'=> 'e', 'message'=> '<br/>some thing went wrong,<br/>Please contact for system support', 'http_code'=> $http_code ];
        }

        $response = json_decode($response);

        $status = ($response->success != false)? 's': 'e';
        $rt_data = [
            'status'=> $status, 'message'=> $response->message, 'http_code'=> $http_code
        ];

        if($status == 's'){
            if(property_exists($response, 'data')){
                $rt_data['data'] = $response->data;
            }
        }

        if($http_code == '422'){ //Unprocessable Entity (Form validation failed)
            $msg = '';
            foreach ($rt_data['message'] as $row){
                $msg .= implode('<br/>', $row);
            }
            $rt_data['message'] = $msg;
        }

        return $rt_data;
    }
    function company_secondarylogo_image_upload()
    {
        $this->load->library('s3');
        $comapnyid = trim($this->input->post('faID') ?? '');

        $itemimageexist = $this->db->query("SELECT
	company_secondary_logo 
FROM
	`srp_erp_company`
	WHERE
	company_id = $comapnyid ")->row_array();

        if (!empty($itemimageexist)) {
            $this->s3->delete('images/logo/' . $itemimageexist['company_secondary_logo']);
        }

        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'comsecondary_' . trim($this->input->post('faID') ?? '') . '_' . $this->common_data['company_data']['company_code'] . '_' . time() . '.' . 'png';

        $file = $_FILES['files'];
        if ($file['error'] == 1) {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_types = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar';
        $allowed_types = explode('|', $allowed_types);
        if (!in_array($ext, $allowed_types)) {
            return array('e', "The file type you are attempting to upload is not allowed. ( .{$ext} )");

        }
        $size = $file['size'];
        $size = number_format($size / 1048576, 2);

        if ($size > 5) {
            return array('e', "The file you are attempting to upload is larger than the permitted size. (maximum 5MB)");

        }
        $path = "images/logo/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }


        /* $output_dir = "images/logo/";
         if (!file_exists($output_dir)) {
             mkdir("images/logo/", 0744);
         }*/

        /*$attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'com_' . trim($this->input->post('faID') ?? '') . '_'.time().'.' . 'png';
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['company_logo'] = $fileName;

        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', "Image Upload Failed." . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', 'Image uploaded  Successfully.');
            $this->db->trans_commit();
            return array('status' => true,'image'=> $fileName);
        }*/


        /*$path = UPLOAD_PATH .base_url().$output_dir;
        $fileName = 'com_' . trim($this->input->post('faID') ?? '') . '_'.time().'.' . 'png';
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|png|jpg|jpeg';
        $config['max_size'] = '200000';
        $config['file_name'] = $fileName;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);*/
        //empImage is  => $_FILES['empImage']['name'];
        $this->db->trans_start();
        $data['company_secondary_logo'] = $fileName;
        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);

        $this->db->trans_complete();
        $data['company_secondary_logo'] = $fileName;
        $this->db->where('company_id', trim($this->input->post('faID') ?? ''));
        $this->db->update('srp_erp_company', $data);
        return array('s', $fileName);
    }
    function realmax_api_requests($req_url, $is_put=false){
        $companyID = current_companyID();
        $db2 = $this->load->database('db2', TRUE);
        $api_key = $db2->query("SELECT `key` FROM `keys` WHERE company_id = {$companyID} AND key_type = 'REALMAX'")->row('key');
        $currentuserID = current_userID();
        $fields = array(
            'api_key' => $api_key,
            'user_id' =>$currentuserID,
        );
        $site_url = $this->config->item('realmax_login_url');
        if(empty($site_url)){
            return ['status'=> 'e', 'message'=> 'Real Max site url not configured.' ];
        }
        $site_url .= $req_url;
        $headers = [
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt( $ch, CURLOPT_URL, $site_url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, ($is_put)? 'PUT': 'POST' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($fields) );
        $response = curl_exec ( $ch );
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            $msg = "<br/>" . curl_error($ch);
            return [ 'status'=> 'e', 'message'=> $msg, 'http_code'=> $http_code ];
        }
        curl_close ( $ch );

        if(!in_array($http_code, ['200', '401', '404', '422', '500'])){
            return [ 'status'=> 'e', 'message'=> '<br/>some thing went wrong,<br/>Please contact for system support', 'http_code'=> $http_code ];
        }
        $response = json_decode($response);
        $status = ($response->success != false)? 's': 'e';
        $rt_data = [
            'status'=> $status, 'message'=> $response->message, 'http_code'=> $http_code
        ];
        if($status == 's'){
            if(property_exists($response, 'data')){
                $rt_data['data'] = $response->data;
            }
        }
        if($http_code == '422'){
            $msg = '';
            foreach ($rt_data['message'] as $row){
                $msg .= implode('<br/>', $row);
            }
            $rt_data['message'] = $msg;
        }

        return $rt_data;
    }
    function update_postDate()
    {
        $codeID = $this->input->post('codeID');
        $postDate = $this->input->post('postDate');
        $data = array(
            'postDate' => $postDate,
        );
        $this->db->where('codeID', $codeID);
        $result = $this->db->update('srp_erp_documentcodemaster', $data);
        if ($result == 1) {
            return array('s', 'Updated Successfully.');
        }
    }

    function statusChangeControlAccount()
    {
        $status = $this->input->post('status');
        $companyID = current_companyID();

        $controlAccountsAutoID = $this->input->post('controlAccountsAutoID');
        $GLAutoID = $this->input->post('GLAutoID');
        $company_id = current_companyID();
       
        $this->db->trans_start();

        if($status ==1){
            $detail['controllAccountYN'] = '0';
        } else{
            $detail['controllAccountYN'] = '1';
        }
        $this->db->where('GLAutoID', $GLAutoID);
        $result = $this->db->update('srp_erp_chartofaccounts', $detail);
            if ($result == 1) {
                $data['controlAccountsAutoID'] = $controlAccountsAutoID;
                $data['GLAutoID'] = $GLAutoID;
                $data['status'] = $status;
                $data['companyID'] = $company_id ;
                $data['companyCode'] = current_companyCode();
                $data['createdUserGroup'] = user_group();
                $data['createdPCID'] = current_pc();
                $data['createdUserID'] = current_userID();
                //$data['createdDateTime'] = current_date();
                $data['createdDateTime'] = fetch_current_time_by_timezone($this->common_data['company_data']['company_id']);
                $data['createdUserName'] = current_user();
                
                $insert = $this->db->insert('control_account_log', $data);
            }
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            return array('s', 'Updated successfully ');
        } else {
            $this->db->trans_rollback();
            return array('e', 'Error in process');
        }

    }

    function fetch_controlaccountlog_excel()
    {

        $companyID = current_companyID();
        $controlAccounDrop = $this->input->post('controlAccounDrop');
        $controlAccount_filter = (!empty($custcontrolAccounDropomer))? " AND controlaccounts.controlAccountsAutoID IN ({$controlAccounDrop})": '';
        $where = "log.companyID = " . $companyID . $controlAccount_filter ;
        $this->datatables->select("controlAccountLogAutoID,
        controlaccounts.controlAccountType as controlAccountType,
        controlaccounts.controlAccountDescription as controlAccountDescription,
        chartofaccounts.systemAccountCode as systemAccountCode,
        chartofaccounts.GLSecondaryCode as GLSecondaryCode,
        chartofaccounts.GLDescription as GLDescription,
        log.createdUserName as createdUserName,
        DATE_FORMAT(log.createdDateTime, '%Y-%m-%d') AS createdDateTime, 
        CASE
            WHEN status = '0' THEN  'Closed' 
            WHEN status = '1' THEN  'Open' 
            ELSE '-' 
            END AS status");
        $this->datatables->from('control_account_log log');
        $this->datatables->join('srp_erp_chartofaccounts chartofaccounts', 'chartofaccounts.GLAutoID = log.GLAutoID', 'LEFT');
        $this->datatables->join('srp_erp_companycontrolaccounts controlaccounts', 'controlaccounts.controlAccountsAutoID = log.controlAccountsAutoID ' , 'LEFT');
        $this->datatables->where($where);
        $result = $this->db->get()->result_array();
        

        $a = 1;
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'controlAccountType' => $row['controlAccountType'],
                'controlAccountDescription' => $row['controlAccountDescription'],
                'systemAccountCode' => $row['systemAccountCode'],
                'GLSecondaryCode' => $row['GLSecondaryCode'],
                'GLDescription' => $row['GLDescription'],
                'createdUserName' => $row['createdUserName'],
                'createdDateTime' => $row['createdDateTime'],
                'status' => $row['status']
            );
            $a++;
        }
        return $data;
    }

    function fetch_controlaccount_excel()
    {

        $companyID = current_companyID();
        $where = "log.companyID = " . $companyID ;

        $this->datatables->select("controlAccountsAutoID,controlAccountType,controlAccountDescription, 
        controlaccounts.systemAccountCode as systemAccountCode, chartofaccounts.GLAutoID as GLAutoID, chartofaccounts.GLSecondaryCode,chartofaccounts.GLDescription,chartofaccounts.controllAccountYN as controllAccountYN ");
        $this->datatables->from('srp_erp_companycontrolaccounts controlaccounts');
        $this->datatables->join('srp_erp_chartofaccounts chartofaccounts', 'chartofaccounts.GLAutoID = controlaccounts.GLAutoID', 'INNER');
        $this->datatables->where('controlaccounts.companyID', $this->common_data['company_data']['company_id']);
        $result = $this->db->get()->result_array();
        

        $a = 1;
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'controlAccountType' => $row['controlAccountType'],
                'controlAccountDescription' => $row['controlAccountDescription'],
                'systemAccountCode' => $row['systemAccountCode'],
                'GLSecondaryCode' => $row['GLSecondaryCode'],
                'GLDescription' => $row['GLDescription']
            );
            $a++;
        }
        return $data;
    }

    function fetch_audit_log_excel()
    {

        $companyID = current_companyID();
        $where = "log.companyID = " . $companyID ;

        $date_format_policy = date_format_policy();
        $date_from = $this->input->post('filter_date_from');
        $date_from_convert = input_format_date($date_from, $date_format_policy);
        $date_to = $this->input->post('fliter_date_to');
        $date_to_convert = input_format_date($date_to, $date_format_policy);
        $date_filter = (!empty($date_from) && !empty($date_to))? " AND ( DATE(createdDateTime) BETWEEN '{$date_from_convert}' AND '{$date_to_convert}')" : '';


        $employee = $this->input->post('employee');
        $employee_filter = (!empty($employee))? " AND EIdNo IN ({$employee})": '';

        $companyID = current_companyID();

        $where = "companyID = " . $companyID . $employee_filter .  $date_filter ."";

        $db2 = $this->load->database('db2', TRUE);
        $main_db = $db2->database;
        $this->datatables->select("auditlogID,empDetailTBL.Ecode,Ename2,IFNULL(documentID,IF(transactionType=0,'Logged In to system',IF(transactionType=2,'Logged out from the system',documentID))) as documentID,createdDateTime");
        $this->datatables->from("$main_db.system_audit_log auditTBL");
        $this->datatables->join("srp_employeesdetails  empDetailTBL","empDetailTBL.EIdNo = auditTBL.empID");
        $this->datatables->where($where);
        $result = $this->db->get()->result_array();
        

        $a = 1;
        $data = array();
        foreach ($result as $row)
        {
            $data[] = array(
                'Num' => $a,
                'Ecode' => $row['Ecode'],
                'Ename2' => $row['Ename2'],
                'documentID' => $row['documentID'],
                'createdDateTime' => $row['createdDateTime']
            );
            $a++;
        }
        return $data;
    }

    function getSupportToken($company_id)
    {
        $db2 = $this->load->database('db2', TRUE);
        $db2->select('*');
        $db2->where('company_id', $company_id);
        return $db2->get('srp_erp_company')->row('supportToken');
    }
    function save_invoice_template(){

        $this->db->trans_start();
        $data['invoiceTemplateName'] = trim($this->input->post('template_name') ?? '');
        $data['customerName'] = trim($this->input->post('customer_name') ?? '');
        $data['customerTelephone'] = trim($this->input->post('customer_tel') ?? '');
        $data['contactPersonTel'] = trim($this->input->post('contact_person_tel') ?? '');
        $data['customerVatNo'] = trim($this->input->post('customer_vat') ?? '');
        $data['invoiceNumber'] = trim($this->input->post('invoice_no') ?? '');
        $data['customerAddress'] = trim($this->input->post('customer_address') ?? '');
        $data['contactPerson'] = trim($this->input->post('contact_person_name') ?? '');
        $data['narration'] = trim($this->input->post('contact_narration') ?? '');
        $data['segment'] = trim($this->input->post('segment') ?? '');
        $data['documentDate'] = trim($this->input->post('document_date') ?? '');

        $data['topHeight'] = trim($this->input->post('top_height') ?? '');
        $data['bottomHeight'] = trim($this->input->post('bottom_height') ?? '');
        $data['leftWidth'] = trim($this->input->post('left_width') ?? '');
        $data['rightWidth'] = trim($this->input->post('right_width') ?? '');

        $data['referenceNumber'] = trim($this->input->post('reference_number') ?? '');
        $data['currency'] = trim($this->input->post('currency') ?? '');
        $data['invoiceDate'] = trim($this->input->post('invoiceDate') ?? '');
        $data['invoiceDueDate'] = trim($this->input->post('invoiceDueDate') ?? '');

        $data['companyID'] = $this->common_data['company_data']['company_id'];
        /*
        $data['createdUserID'] = trim($this->session->userdata("empID"));
        $data['createdUserName'] = trim($this->session->userdata("username"));
        $data['createdDateTime'] = date('Y-m-d h:i:s');*/
        $this->db->insert('srp_erp_invoicetemplatemaster', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('e', ' Saved Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
            return array('status' => false);
        } else {
            $this->session->set_flashdata('s', ' Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function fetch_invoice_templates(){
        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        return $this->db->get('srp_erp_invoicetemplatemaster')->result_array();
    }
}
