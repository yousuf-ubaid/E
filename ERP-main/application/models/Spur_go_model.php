<?php

class Spur_go_model extends ERP_Model
{
    Private $mainDB;
    private $db_name;
    private $db_username;
    private $db_password;
    private $db_host;
    function save_spurgo_companydetails()
    {
        $this->encryption->initialize(array('driver' => 'mcrypt'));
        /**********User Signup Form Details Start *********/
        $companyname = $this->input->post('companyname');
        $companyemail = $this->input->post('comapnyemail');
        $fullname = $this->input->post('fullname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $currencyID = $this->input->post('company_default_currencyID');
        $usernameprimary = $this->input->post('usernameprimary');
        $usernamesecondary = $this->input->post('usernamesec');

        $namesec = $this->input->post('namesec');
        $usernamesec = $this->input->post('usernamesec');
        $passwordsec = $this->input->post('passwordsec');

        $nameprimary = $this->input->post('nameprimary');
        $usernameprimary = $this->input->post('usernameprimary');
        $passwordprimary = $this->input->post('passwordprimary');

        $istermsYN = $this->input->post('istermsYN');

        if($istermsYN == 0)
        {
            return array('e', 'Terms of Service Field is required');
            exit();
        }



        /**********User Signup Form Details End *********/

        /**********Load Main Db Start *********/
        $this->mainDB = $this->load->database('db2', TRUE);
        $usernameprimary_q =  $this->mainDB->query("select EidNo from `user` where Username='$usernameprimary'")->row('EidNo');
        $usernamesecondary_q = '';

        if(!empty($nameprimary)&&!empty($namesec))
        {
            if($nameprimary == $namesec)
            {
                return array('e','Line 1 Name and line 2 Name Cannot be Same');
                exit();
            }
        }

        if(!empty($usernameprimary)&&!empty($usernamesec))
        {
            if($usernameprimary == $usernamesec)
                {
                    return array('e','Line 1 User Name and line 2 User Name Cannot be Same');
                    exit();
                }
        }


        if($usernamesecondary)
        {
            $usernamesecondary_q =  $this->mainDB->query("SELECT  `EidNo` FROM `user` where Username = '$usernamesecondary'")->row('EidNo');
        }
        if($usernameprimary_q){
            return array('e', 'user name already exist ('.$usernameprimary.')');
            exit();
        }
        if($usernamesecondary_q){
             return array('e', 'user name already exist ('.$usernamesecondary.')');
            exit();
        }
        $this->mainDB->trans_start();
        /**********Load Main Db End *********/

        /********** Get Host and other values from env(encrypted) Start*********/
        $data_main['host'] = spur_go_DB_HOST;
        $data_main['db_name'] = spur_go_DB_NAME;
        $data_main['db_username'] = spur_go_DB_USER;
        $data_main['db_password'] = spur_go_DB_PASSWORD;
        //$data_main['attachmentHost'] = $this->input->post('attachmentHost');
       // $data_main['attachmentFolderName'] = $this->input->post('attachmentFolderName');
        //$data_main['adminType'] = $userType;
        /********** Get Host and other values from env(encrypted) End*********/

        /**********Insert The Values to Main DB Start*********/
        $config['hostname'] = trim($this->encryption->decrypt(spur_go_DB_HOST));
        $config['username'] = trim($this->encryption->decrypt(spur_go_DB_USER));
        $config['password'] = trim($this->encryption->decrypt(spur_go_DB_PASSWORD));
        $config['database'] = trim($this->encryption->decrypt(spur_go_DB_NAME));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = FALSE;
        $config['pconnect'] = FALSE;
        $db_obj = $this->load->database($config, TRUE); // ommit the error
        if ($db_obj->conn_id) {
            $this->mainDB->insert('srp_erp_company', $data_main);
            $company_id = $this->mainDB->insert_id();
            $this->mainDB->trans_complete();
            if ($this->mainDB->trans_status() === FALSE) {
                $this->mainDB->trans_rollback();
                return array('e', 'Error in host detail save process');
            } else {
                $this->db_host = trim(spur_go_DB_HOST);
                $this->db_name = trim(spur_go_DB_NAME);;
                $this->db_password = trim(spur_go_DB_PASSWORD);
                $this->db_username = trim(spur_go_DB_USER);
                $this->db->trans_commit();
              $currencydecilmal = $this->mainDB->query("SELECT  `DecimalPlaces` FROM srp_erp_currencymaster where currencyID = $currencyID")->row('DecimalPlaces');
              $data = array(
                    'companycode' => $this->input->post('companycode'),
                    'companyname' => $companyname,
                    'companyurl' => $this->input->post('companyurl'),
                    'companyphone' => $this->input->post('companyphone'),
                    'company_default_currencyID' => $this->input->post('company_default_currencyID'),
                    'default_currency' => $this->input->post('default_currency'),
                    'reporting_currency' => $this->input->post('reporting_currency'),
                    'company_reporting_currencyID' => $this->input->post('company_default_currencyID'),
                    'currencydecimal' =>$currencydecilmal,
                    'timezone' => $this->input->post('timezone'),
                    'companyaddress1' => $this->input->post('companyaddress1'),
                    'companyaddress2' => $this->input->post('companyaddress2'),
                    'companycity' => $this->input->post('companycity'),
                    'companypostalcode' => $this->input->post('companypostalcode'),
                    'province' => $this->input->post('province'),
                    'companycountry' => $this->input->post('companycountry'),
                    'companyemail' => $companyemail,
                    'nameprimary' => $this->input->post('nameprimary'),
                    'usernameprimary' => $this->input->post('usernameprimary'),
                    'passwordprimary' => $this->input->post('passwordprimary'),
                    'namesec' => $this->input->post('namesec'),
                    'usernamesec' => $this->input->post('usernamesec'),
                    'passwordsec' => $this->input->post('passwordsec'),

                );


                $this->save_companydetails($company_id,$data);
                return array('s', 'Host detail added successfully.', 'last_id' => $company_id);
            }
        }
        else {
            return array('e', 'Unable to connect with database with given db details');
        }
        /**********Insert The Values to Main DB End*********/


    }
    function save_companydetails($company_id,$data_detail)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $default_currency = explode('|', $data_detail['default_currency']);
        $reporting_currency = explode('|', $data_detail['reporting_currency']);
        $com_country = explode('|', $data_detail['companycountry']);
        $data['company_link_id'] = 0;
        $data['branch_link_id'] =  0;
        $data['company_code'] = $data_detail['companycode'];
        $data['company_name'] = $data_detail['companyname'];
        $data['company_start_date'] = date("Y-m-d");
        $data['company_url'] =  $data_detail['companyurl'];
        $data['company_email'] = $data_detail['companyemail'];
        $data['company_phone'] = $data_detail['companyphone'];
        $data['company_address1'] =  $data_detail['companyaddress1'];
        $data['company_address2'] = $data_detail['companyaddress2'];
        $data['company_city'] = $data_detail['companycity'];
        $data['company_province'] = $data_detail['province'];
        $data['company_postalcode'] =$data_detail['companypostalcode'];
        $data['countryID'] = $com_country[0];
        $data['company_country'] = $com_country[1];
        $data['company_default_currencyID'] = $data_detail['company_default_currencyID'];
        $data['company_default_currency'] = ($default_currency[0]);
        $data['company_default_decimal'] = $data_detail['currencydecimal'];
        $data['company_reporting_currencyID'] = $data_detail['company_reporting_currencyID'];
         $data['company_reporting_currency'] = ($reporting_currency[0]);
        $data['company_reporting_decimal'] = $data_detail['currencydecimal'];
        $data['defaultTimezoneID'] = $data_detail['timezone'];
        //$data['noOfUsers'] = $this->input->post('noOfUsers');
        $companyID = $company_id;
        $this->db->select('*');
        $this->db->where('company_id', $companyID);
        $exist_company = $this->db->get('srp_erp_company')->row_array();
        $data['modifiedPCID'] =  ' ';
        $data['modifiedUserID'] = ' ';
        $data['modifiedUserName'] = ' ';
        $data['modifiedDateTime'] = date('Y-m-d h:i:s');
        if ($exist_company) {
            $this->mainDB = $this->load->database('db2', TRUE);
            $this->mainDB->where('company_id', $companyID);
            $this->mainDB->update('srp_erp_company', $data);
            $this->db->where('company_id', $companyID);
            $this->db->update('srp_erp_company', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return ['e', 'Company : ' . $data['company_name'] . ' Update Failed ' . $this->db->_error_message()];
            }
            else {
                $this->db->trans_commit();
                return ['s', 'Company : ' . $data['company_name'] . ' Update Successfully.', 'last_id' => $companyID];
            }
        }else
        {
            $data['company_logo'] = 'no-logo.png';
            $data['createdUserGroup'] = 'Admin';
            $data['createdPCID'] = '';
            $data['createdUserID'] = null;
            $data['createdUserName'] = null;
            $data['createdDateTime'] = $data['modifiedDateTime'];
            $data["company_id"] = (int)$companyID;
            $data['pos_isFinanceEnables'] = 1;
            $this->db->insert('srp_erp_company', $data);

            $company_id = $this->db->insert_id();

            $this->db->query("INSERT INTO `srp_erp_customertypemaster` ( `customerDescription`, `isDefault`, `company_id`, `createdBy`, `createdDatetime`, `createdPc`, `timestamp`) 
                              VALUES ('Eat-in', '1', $company_id, '', NULL, '', '0000-00-00 00:00:00'), ('Take-away', '0', $company_id, '', NULL, '', '0000-00-00 00:00:00'),
                              ('Delivery Orders', '0', $company_id, '', NULL, '', '0000-00-00 00:00:00');");

            unset($data["company_id"]);
            unset($data["pos_isFinanceEnables"]);

            $this->mainDB = $this->load->database('db2', TRUE);
            $this->mainDB->where('company_id', $companyID);
            $this->mainDB->update('srp_erp_company', $data);

            //add to navigation templates
            $newTemplates = $this->db->query("SELECT srp_erp_templatemaster.TempMasterID,srp_erp_formcategory.FormCatID,srp_erp_formcategory.navigationMenuID
                                              FROM srp_erp_formcategory
                                              INNER JOIN srp_erp_templatemaster ON srp_erp_formcategory.FormCatID = srp_erp_templatemaster.FormCatID 
                                              WHERE isDefault = 1")->result_array();
            if ($newTemplates) {
                $templates = array();
                foreach ($newTemplates as $val) {
                    $templates[] = array('companyID' => $company_id, 'TempMasterID' => $val['TempMasterID'], 'FormCatID' => $val['FormCatID'], 'navigationMenuID' => $val['navigationMenuID']);
                }
                $this->db->insert_batch('srp_erp_templates', $templates);
            }
          /*  $user_group_arr = array(
                array('description' => 'Administrator', 'isActive' => 1, 'companyID' => $company_id),

            );
            $insert = $this->db->insert_batch('srp_erp_usergroups', $user_group_arr);*/
            $insert = $this->db->insert('srp_erp_usergroups', array('description' => 'Administrator', 'isActive' => 1, 'companyID' => $company_id));
            $user_group_id = $this->db->insert_id();
            $usergroupArr = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$company_id} AND description IN 
                                            ('Administrator') 
                                             ")->result_array();
            $defaultWidgets = $this->db->query("select widgetID from srp_erp_widgetmaster")->result_array();
            if ($insert) {
                if (!empty($usergroupArr)) {
                    $x = 0;
                    foreach ($usergroupArr as $row) {
                        foreach ($defaultWidgets as $val) {
                                $widgetdata[$x]['companyID'] = $company_id;
                                $widgetdata[$x]['userGroupID'] = $row['userGroupID'];
                                $widgetdata[$x]['widgetID'] = $val['widgetID'];
                                $x++;
                        }
                    }
                }
            }
            if ($widgetdata) {
                $insertDefaultWidget = $this->db->insert_batch('srp_erp_usergroupwidget', $widgetdata);
            }
            $title_arr = array();
            $title_arr[0]['TitleDescription'] = 'Mr';
            $title_arr[0]['Erp_companyID'] = $company_id;
            $title_arr[1]['TitleDescription'] = 'Mrs';
            $title_arr[1]['Erp_companyID'] = $company_id;
            $title_arr[2]['TitleDescription'] = 'Miss';
            $title_arr[2]['Erp_companyID'] = $company_id;
            $this->db->insert_batch('srp_titlemaster', $title_arr);
            $cat_arr = array();
            array_push($cat_arr, array('partyType' => 1, 'categoryDescription' => 'General', 'companyCode' => $data['company_code'], 'companyID' => $company_id));
            array_push($cat_arr, array('partyType' => 2, 'categoryDescription' => 'General', 'companyCode' => $data['company_code'], 'companyID' => $company_id));
            $this->db->insert_batch('srp_erp_partycategories', $cat_arr);
            $religion_arr = array();
            $religion_arr[0]['Religion'] = 'Christianity';
            $religion_arr[0]['ReligionAr'] = 'ؤاقهسفهشى';
            $religion_arr[0]['Erp_companyID'] = $company_id;
            $religion_arr[1]['Religion'] = 'Islam';
            $religion_arr[1]['ReligionAr'] = 'ةعسخمهة';
            $religion_arr[1]['Erp_companyID'] = $company_id;
            $religion_arr[2]['Religion'] = 'Hinduism';
            $religion_arr[2]['ReligionAr'] = 'اهىيع';
            $religion_arr[2]['Erp_companyID'] = $company_id;
            $religion_arr[3]['Religion'] = 'Buddhism';
            $religion_arr[3]['ReligionAr'] = 'يبيبسبس';
            $religion_arr[3]['Erp_companyID'] = $company_id;
            $religion_arr[4]['Religion'] = 'Others';
            $religion_arr[4]['ReligionAr'] = '';
            $religion_arr[4]['Erp_companyID'] = $company_id;
            $this->db->insert_batch('srp_erp_companypolicymaster_value', [
                ['companypolicymasterID' => '5', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id],
                ['companypolicymasterID' => '6', 'value' => 'General', 'systemValue' => '0', 'companyID' => $company_id]
            ]);
            $nationality_arr = array();
            $country_arr = array();
            $this->db->select('*');
            $this->db->from('srp_erp_countrymaster');
            $nationality = $this->db->get()->result_array();
            foreach ($nationality as $key => $value) {
                $nationality_arr[$key]['Nationality'] = $value['Nationality'];
                $nationality_arr[$key]['Erp_companyID'] = $company_id;
                $country_arr[$key]['countryShortCode'] = $value['countryShortCode'];
                $country_arr[$key]['CountryDes'] = $value['CountryDes'];
                $country_arr[$key]['CountryDes'] = $value['CountryDes'];
                $country_arr[$key]['countryMasterID'] = $value['countryID'];
                $country_arr[$key]['Erp_companyID'] = $company_id;
            }
            if ($nationality_arr) {
                $this->db->insert_batch('srp_nationality', $nationality_arr);
            }

            if ($religion_arr) {
                $this->db->insert_batch('srp_religion', $religion_arr);
            }

            if ($country_arr) {
                $this->db->insert_batch('srp_countrymaster', $country_arr);
            }
            $currency_arr = array();
            $currency_arr = array();
            if ($data['company_default_currencyID'] == $data['company_reporting_currencyID']) {
                $currency_arr['currencyID'] = $data['company_default_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_default_currency'];
                $currency_arr['CurrencyName'] = $default_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_default_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                    'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                    'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                    'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                ]);
            }
            else {
                $currency_arr['currencyID'] = $data['company_default_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_default_currency'];
                $currency_arr['CurrencyName'] = $default_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_default_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                        'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                        'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                        'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                    ]
                );

                $currency_arr['currencyID'] = $data['company_reporting_currencyID'];
                $currency_arr['CurrencyCode'] = $data['company_reporting_currency'];
                $currency_arr['CurrencyName'] = $reporting_currency[1];
                $currency_arr['DecimalPlaces'] = $data['company_reporting_decimal'];
                $currency_arr['companyID'] = $company_id;
                $currency_arr['companyCode'] = $data['company_code'];
                $this->db->insert('srp_erp_companycurrencyassign', $currency_arr);
                $currency_id = $this->db->insert_id();

                $this->db->insert('srp_erp_companycurrencyconversion', [
                    'companyID' => $company_id, 'companyCode' => $data['company_code'], 'mastercurrencyassignAutoID' => $currency_id, 'subcurrencyassignAutoID' => $currency_id,
                    'masterCurrencyID' => $currency_arr['currencyID'], 'masterCurrencyCode' => $currency_arr['CurrencyCode'], 'subCurrencyID' => $currency_arr['currencyID'],
                    'subCurrencyCode' => $currency_arr['CurrencyCode'], 'conversion' => 1
                ]);
            }

            $ememployeetype = array();
            $ememployeetype_arr = $this->db->get('srp_erp_systememployeetype')->result_array();
            for ($i = 0; $i < count($ememployeetype_arr); $i++) {
                $ememployeetype[$i]['Description'] = $ememployeetype_arr[$i]['employeeType'];
                $ememployeetype[$i]['typeID'] = $ememployeetype_arr[$i]['employeeTypeID'];
                $ememployeetype[$i]['Erp_CompanyID'] = $company_id;
            }
            if ($ememployeetype) {
                $this->db->insert_batch('srp_empcontracttypes', $ememployeetype);
            }

            $companypolicies = $this->config->item('policy_values');

            if(!empty($companypolicies))
            {
                foreach ($companypolicies as $policyval)
                {
                    $value = explode('|',$policyval);
                    $defval = $value[0];
                    $policyID =  $value[1];
                    $this->db->query("INSERT INTO `srp_erp_companypolicy` (`companypolicymasterID`,`companyID`,`documentID`,`isYN`,`value`) 
                              SELECT companypolicymasterID,'{$company_id}', documentID,1, $defval FROM srp_erp_companypolicymaster where companypolicymasterID = $policyID");

                }
            }

            $this->load->library('sequence', $this->get_db_array());
            $this->db->select('*');
            $this->db->from('srp_erp_accountcategorytypes');
            $account_types = $this->db->get()->result_array();
            for ($i = 0; $i < count($account_types); $i++) {
                $master_arr['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($account_types[$i]['subType'], 0, $company_id, $data['company_code']);
                $master_arr['GLSecondaryCode'] = $master_arr['systemAccountCode'];
                $master_arr['GLDescription'] = $account_types[$i]['CategoryTypeDescription'] . ' - Accounts ';
                $master_arr['masterAccountYN'] = 1;
                $master_arr['controllAccountYN'] = 0;
                $master_arr['masterAutoID'] = 0;
                $master_arr['masterCategory'] = $account_types[$i]['Type'];
                $master_arr['accountCategoryTypeID'] = $account_types[$i]['accountCategoryTypeID'];
                $master_arr['CategoryTypeDescription'] = $account_types[$i]['CategoryTypeDescription'];
                $master_arr['subCategory'] = $account_types[$i]['subType'];
                $master_arr['isActive'] = 1;
                $master_arr['isAuto'] = 1;
                $master_arr['approvedYN'] = 1;
                $master_arr['approvedDate'] = $data['createdDateTime'];
                $master_arr['approvedbyEmpID'] = $data['createdUserName'];
                $master_arr['approvedbyEmpName'] = $data['createdUserName'];
                $master_arr['approvedComment'] = 'By System';
                $master_arr['companyID'] = $company_id;
                $master_arr['companyCode'] = $data['company_code'];
                $master_arr['createdPCID'] = $data['modifiedPCID'];
                $master_arr['createdUserGroup'] = $user_group_id;
                $master_arr['createdUserName'] = $data['createdUserName'];
                $master_arr['createdUserID'] = $data['createdUserName'];
                $master_arr['createdDateTime'] = $data['createdDateTime'];
                $master_arr['modifiedPCID'] = $data['modifiedPCID'];
                $master_arr['modifiedUserID'] = $data['createdUserName'];
                $master_arr['modifiedUserName'] = $data['createdUserName'];
                $master_arr['modifiedDateTime'] = $data['createdDateTime'];
                $this->db->insert('srp_erp_chartofaccounts', $master_arr);
                $master_id = $this->db->insert_id();
                $control_account = array();
                if ($master_arr['CategoryTypeDescription'] == 'Account Receivable') {
                    $GL_data['GLSecondaryCode'] = 'AR0001';
                    $GL_data['GLDescription'] = 'Accounts Receivable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];;
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ARA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Account Payable') {
                    $GL_data['GLSecondaryCode'] = 'AP0001';
                    $GL_data['GLDescription'] = 'Accounts Payable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'APA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Current Asset') {
                    $GL_data['GLSecondaryCode'] = 'IN0001';
                    $GL_data['GLDescription'] = 'Inventory Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'INVA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    $GL_data['GLSecondaryCode'] = 'ADSP0001';
                    $GL_data['GLDescription'] = 'Asset Disposal Proceeds Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ADSP';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    //WIP
                    $GL_data['GLSecondaryCode'] = 'WIP';
                    $GL_data['GLDescription'] = 'Work in Progress';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'WIP';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    //RRVR
                    $GL_data['GLSecondaryCode'] = 'RRVR';
                    $GL_data['GLDescription'] = 'Receipt Reversal Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'RRVR';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    //GIT
                    $GL_data['GLSecondaryCode'] = 'GIT';
                    $GL_data['GLDescription'] = 'Goods In Transit';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'GIT';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'IOU';
                    $GL_data['GLDescription'] = 'IOU Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'IOU';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'IEXC';
                    $GL_data['GLDescription'] = 'IEXC';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'IEXC';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);


                    $GL_data['GLSecondaryCode'] = 'UBI';
                    $GL_data['GLDescription'] = 'Un-Billed Invoices';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'UBI';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Current Liability') {
                    $GL_data['GLSecondaryCode'] = 'PCA0001';
                    $GL_data['GLDescription'] = 'Payroll Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'PCA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    $GL_data['GLSecondaryCode'] = 'TAX0001';
                    $GL_data['GLDescription'] = 'TAX Payable Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'TAX';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);

                    $GL_data['GLSecondaryCode'] = 'UGRV0001';
                    $GL_data['GLDescription'] = 'Unbill GRV Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'UGRV';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                    //PRVR
                    $GL_data['GLSecondaryCode'] = 'PRVR';
                    $GL_data['GLDescription'] = 'Payment Reversal Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'PRVR';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);

                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Cost of Goods Sold') {
                    $GL_data['GLSecondaryCode'] = 'COGS0001';
                    $GL_data['GLDescription'] = 'Cost of goods sold Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'COGS';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Expense') {
                    $GL_data['GLSecondaryCode'] = 'LEC';
                    $GL_data['GLDescription'] = 'Leave Encashment Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'LEC';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Other Expense') {
                    $GL_data['GLSecondaryCode'] = 'ERGL0001';
                    $GL_data['GLDescription'] = 'Exchange Rate Gain or Loss';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ERGL';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
                elseif ($master_arr['CategoryTypeDescription'] == 'Fixed Asset') {
                    $GL_data['GLSecondaryCode'] = 'AST0001';
                    $GL_data['GLDescription'] = 'Asset Control Account';
                    $GL_data['masterCategory'] = $master_arr['masterCategory'];
                    $GL_data['subCategory'] = $account_types[$i]['subType'];
                    $GL_data['accountCategoryTypeID'] = $master_arr['accountCategoryTypeID'];
                    $GL_data['CategoryTypeDescription'] = $master_arr['CategoryTypeDescription'];
                    $GL_data['masterAutoID'] = $master_id;
                    $GL_data['masterAccount'] = $master_arr['systemAccountCode'];
                    $GL_data['masterAccountDescription'] = $master_arr['GLDescription'];
                    $GL_data['isActive'] = 1;
                    $GL_data['isAuto'] = 1;
                    $GL_data['controllAccountYN'] = 1;
                    $GL_data['approvedYN'] = 1;
                    $GL_data['approvedDate'] = date('Y-m-d');
                    $GL_data['approvedbyEmpName'] = 'System';
                    $GL_data['systemAccountCode'] = $this->sequence->sequence_generator_spurgo($GL_data['subCategory'], 0, $company_id, $data['company_code']);
                    $GL_data['companyID'] = $company_id;
                    $GL_data['companyCode'] = $data['company_code'];
                    $GL_data['createdPCID'] = $data['modifiedPCID'];
                    $GL_data['createdUserGroup'] = $user_group_id;
                    $GL_data['createdUserName'] = $data['createdUserName'];
                    $GL_data['createdUserID'] = $data['createdUserName'];
                    $GL_data['createdDateTime'] = $data['createdDateTime'];
                    $GL_data['modifiedPCID'] = $data['modifiedPCID'];
                    $GL_data['modifiedUserID'] = $data['createdUserName'];
                    $GL_data['modifiedUserName'] = $data['createdUserName'];
                    $GL_data['modifiedDateTime'] = $data['createdDateTime'];
                    $this->db->insert('srp_erp_chartofaccounts', $GL_data);
                    $GLAutoID = $this->db->insert_id();
                    $con_account['controlAccountType'] = 'ACA';
                    $con_account['controlAccountDescription'] = $GL_data['GLDescription'];
                    $con_account['GLAutoID'] = $GLAutoID;
                    $con_account['systemAccountCode'] = $GL_data['systemAccountCode'];
                    $con_account['GLSecondaryCode'] = $GL_data['GLSecondaryCode'];
                    $con_account['GLDescription'] = $GL_data['GLDescription'];
                    $con_account['companyID'] = $GL_data['companyID'];
                    $con_account['companyCode'] = $GL_data['companyCode'];
                    $this->db->insert('srp_erp_companycontrolaccounts', $con_account);
                    //array_push($control_account,$con_account);
                }
            }

            if ($data['company_link_id'] != 0 AND $data['branch_link_id'] != 0) {
                $emp_arr = array();
                // $this->db->insert('srp_erp_usergroups', array('description'=>'School Team','isActive'=>1,'companyID'=>$company_id));
                // $school_group_id = $this->db->insert_id();
                $this->db->select('EIdNo');
                $this->db->where('SchMasterId', $data['company_link_id']);
                $this->db->where('branchID', $data['branch_link_id']);
                $this->db->from('srp_employeesdetails');
                $emp_data = $this->db->get()->result_array();
                for ($i = 0; $i < count($emp_data); $i++) {
                    $this->db->where('EIdNo', $emp_data[$i]['EIdNo']);
                    $this->db->update('srp_employeesdetails', array('Erp_companyID' => $company_id));
                }
            }

            $this->db->select('*');
            $this->db->from('srp_erp_documentcodes');
            $document_codes = $this->db->get()->result_array();
            for ($i = 0; $i < count($document_codes); $i++) {
                $document_code[$i]['documentID'] = $document_codes[$i]['documentID'];
                $document_code[$i]['document'] = $document_codes[$i]['document'];
                $document_code[$i]['prefix'] = $document_codes[$i]['documentID'];
                $document_code[$i]['startSerialNo'] = 1;
                $document_code[$i]['serialNo'] = 0;
                $document_code[$i]['formatLength'] = 6;
                $document_code[$i]['approvalLevel'] = 3;
                $document_code[$i]['format_1'] = 'prefix';
                $document_code[$i]['format_2'] = '/';
                $document_code[$i]['companyID'] = $company_id;
                $document_code[$i]['companyCode'] = $data['company_code'];
                $document_code[$i]['createdPCID'] = $data['modifiedPCID'];
                $document_code[$i]['createdUserGroup'] = $user_group_id;
                $document_code[$i]['createdUserName'] = $data['createdUserName'];
                $document_code[$i]['createdUserID'] = $data['createdUserName'];
                $document_code[$i]['createdDateTime'] = $data['createdDateTime'];
                $document_code[$i]['modifiedPCID'] = $data['modifiedPCID'];
                $document_code[$i]['modifiedUserID'] = $data['createdUserName'];
                $document_code[$i]['modifiedUserName'] = $data['createdUserName'];
                $document_code[$i]['modifiedDateTime'] = $data['createdDateTime'];
            }
            $document_code = array_values($document_code);
            if ($document_code) {
                $this->db->insert_batch('srp_erp_documentcodemaster', $document_code);
            }

            $item_category[0]['description'] = 'Inventory';
            $item_category[0]['categoryTypeID'] = '1';
            $item_category[0]['codePrefix'] = 'INV';
            $item_category[0]['StartSerial'] = '1';
            $item_category[0]['codeLength'] = '6';
            $item_category[0]['companyID'] = $company_id;
            $item_category[0]['companyCode'] = $data['company_code'];
            $item_category[0]['createdPCID'] = $data['modifiedPCID'];
            $item_category[0]['createdUserGroup'] = $user_group_id;
            $item_category[0]['createdUserName'] = $data['createdUserName'];
            $item_category[0]['createdUserID'] = $data['createdUserName'];
            $item_category[0]['createdDateTime'] = $data['createdDateTime'];
            $item_category[0]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[0]['modifiedUserID'] = $data['createdUserName'];
            $item_category[0]['modifiedUserName'] = $data['createdUserName'];
            $item_category[0]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[1]['description'] = 'Service';
            $item_category[1]['categoryTypeID'] = '2';
            $item_category[1]['codePrefix'] = 'SRV';
            $item_category[1]['StartSerial'] = '1';
            $item_category[1]['codeLength'] = '6';
            $item_category[1]['companyID'] = $company_id;
            $item_category[1]['companyCode'] = $data['company_code'];
            $item_category[1]['createdPCID'] = $data['modifiedPCID'];
            $item_category[1]['createdUserGroup'] = $user_group_id;
            $item_category[1]['createdUserName'] = $data['createdUserName'];
            $item_category[1]['createdUserID'] = $data['createdUserName'];
            $item_category[1]['createdDateTime'] = $data['createdDateTime'];
            $item_category[1]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[1]['modifiedUserID'] = $data['createdUserName'];
            $item_category[1]['modifiedUserName'] = $data['createdUserName'];
            $item_category[1]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[2]['description'] = 'Fixed Assets';
            $item_category[2]['categoryTypeID'] = '3';
            $item_category[2]['codePrefix'] = 'FA';
            $item_category[2]['StartSerial'] = '1';
            $item_category[2]['codeLength'] = '6';
            $item_category[2]['companyID'] = $company_id;
            $item_category[2]['companyCode'] = $data['company_code'];
            $item_category[2]['createdPCID'] = $data['modifiedPCID'];
            $item_category[2]['createdUserGroup'] = $user_group_id;
            $item_category[2]['createdUserName'] = $data['createdUserName'];
            $item_category[2]['createdUserID'] = $data['createdUserName'];
            $item_category[2]['createdDateTime'] = $data['createdDateTime'];
            $item_category[2]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[2]['modifiedUserID'] = $data['createdUserName'];
            $item_category[2]['modifiedUserName'] = $data['createdUserName'];
            $item_category[2]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category[3]['description'] = 'Non Inventory';
            $item_category[3]['categoryTypeID'] = '2';
            $item_category[3]['codePrefix'] = 'NINV';
            $item_category[3]['StartSerial'] = '1';
            $item_category[3]['codeLength'] = '6';
            $item_category[3]['companyID'] = $company_id;
            $item_category[3]['companyCode'] = $data['company_code'];
            $item_category[3]['createdPCID'] = $data['modifiedPCID'];
            $item_category[3]['createdUserGroup'] = $user_group_id;
            $item_category[3]['createdUserName'] = $data['createdUserName'];
            $item_category[3]['createdUserID'] = $data['createdUserName'];
            $item_category[3]['createdDateTime'] = $data['createdDateTime'];
            $item_category[3]['modifiedPCID'] = $data['modifiedPCID'];
            $item_category[3]['modifiedUserID'] = $data['createdUserName'];
            $item_category[3]['modifiedUserName'] = $data['createdUserName'];
            $item_category[3]['modifiedDateTime'] = $data['createdDateTime'];

            $item_category = array_values($item_category);
            if ($item_category) {
                $this->db->insert_batch('srp_erp_itemcategory', $item_category);
            }

            $segment['segmentCode'] = 'GEN';
            $segment['description'] = 'General';
            $segment['companyID'] = $company_id;
            $segment['companyCode'] = $data['company_code'];
            $this->db->insert('srp_erp_segment', $segment);
            $seg_id = $this->db->insert_id();
            $this->db->where('company_id', $company_id);
            $this->db->update('srp_erp_company', array('default_segment' => $seg_id . '|GEN'));

            $data_u['UnitShortCode'] = 'Each';
            $data_u['UnitDes'] = 'Each';
            $data_u['companyID'] = $company_id;
            $data_u['createdPCID'] = $data['modifiedPCID'];
            $data_u['createdUserGroup'] = $user_group_id;
            $data_u['createdUserName'] = $data['createdUserName'];
            $data_u['createdUserID'] = $data['createdUserName'];
            $data_u['createdDateTime'] = $data['createdDateTime'];
            $data_u['modifiedPCID'] = $data['modifiedPCID'];
            $data_u['modifiedUserID'] = $data['createdUserName'];
            $data_u['modifiedUserName'] = $data['createdUserName'];
            $data_u['modifiedDateTime'] = $data['createdDateTime'];
            $this->db->insert('srp_erp_unit_of_measure', $data_u);
            $unite_id = $this->db->insert_id();
            $this->db->insert('srp_erp_unitsconversion', array('masterUnitID' => $unite_id, 'subUnitID' => $unite_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'companyID' => $company_id));

            $warehouse['wareHouseCode'] = 'GEN';
            $warehouse['wareHouseDescription'] = 'General';
            $warehouse['wareHouseLocation'] = $data['company_city'];
            $warehouse['warehouseAddress'] = $data['company_address1'] . ' ' . $data['company_address2'];
            $warehouse['warehouseTel'] = $data['company_phone'];
            $warehouse['isPosLocation'] = 0;
            $warehouse['isActive'] = 1;
            $warehouse['companyID'] = $company_id;
            $warehouse['companyCode'] = $data['company_code'];
            $warehouse['createdPCID'] = $data['modifiedPCID'];
            $warehouse['createdUserGroup'] = $user_group_id;
            $warehouse['createdUserName'] = $data['createdUserName'];
            $warehouse['createdUserID'] = $data['createdUserName'];
            $warehouse['createdDateTime'] = $data['createdDateTime'];
            $warehouse['modifiedPCID'] = $data['modifiedPCID'];
            $warehouse['modifiedUserID'] = $data['createdUserName'];
            $warehouse['modifiedUserName'] = $data['createdUserName'];
            $warehouse['modifiedDateTime'] = $data['createdDateTime'];
            $this->db->insert('srp_erp_warehousemaster', $warehouse);


            $arrayGroup = array(array("description" => "General", "companyID" => $company_id, "companyCode" => $data['company_code']));
            $this->db->insert_batch('srp_erp_pay_overtimegroupmaster', $arrayGroup);

            /*add leave type*/
            $leave_type_arr = array(
                array('description' => 'Annual Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code']),
                array('description' => 'Sick Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code']),
                array('description' => 'Emergency Leave', 'isPaidLeave' => 1, 'companyID' => $company_id, 'companyCode' => $data['company_code'])
            );
            $insert = $this->db->insert_batch('srp_erp_leavetype', $leave_type_arr);

            /*add leave group*/
            $leave_group_arr = array(
                array('description' => 'Permanent Employees', 'companyID' => $company_id),
                array('description' => 'Temporary Employees', 'companyID' => $company_id)
            );
            $insert = $this->db->insert_batch('srp_erp_leavegroup', $leave_group_arr);

            /*add staff salaries account to chartofaccount*/
            $chartofaccount = $this->db->query("SELECT * FROM srp_erp_chartofaccounts WHERE companyID=$company_id AND masterAccountYN = 1 AND accountCategoryTypeID = 13")->row_array();
            $master_arr['systemAccountCode'] = $this->sequence->sequence_generator_spurgo('PLE', 0, $company_id, $data['company_code']);
            $master_arr['GLSecondaryCode'] = 'SAL-0001';
            $master_arr['GLDescription'] = 'Staff Salaries Account';
            $master_arr['masterAccountYN'] = 0;
            $master_arr['controllAccountYN'] = 0;
            $master_arr['masterAutoID'] = $chartofaccount["GLAutoID"];
            $master_arr['masterCategory'] = $chartofaccount['masterCategory'];
            $master_arr['accountCategoryTypeID'] = $chartofaccount['accountCategoryTypeID'];
            $master_arr['CategoryTypeDescription'] = $chartofaccount['CategoryTypeDescription'];
            $master_arr['subCategory'] = $chartofaccount['subCategory'];
            $master_arr['isActive'] = 1;
            $master_arr['isAuto'] = 1;
            $master_arr['approvedYN'] = 1;
            $master_arr['approvedDate'] = $data['createdDateTime'];
            $master_arr['approvedbyEmpID'] = $data['createdUserName'];
            $master_arr['approvedbyEmpName'] = $data['createdUserName'];
            $master_arr['approvedComment'] = 'By System';
            $master_arr['companyID'] = $company_id;
            $master_arr['companyCode'] = $data['company_code'];
            $master_arr['createdPCID'] = $data['modifiedPCID'];
            $master_arr['createdUserGroup'] = $user_group_id;
            $master_arr['createdUserName'] = $data['createdUserName'];
            $master_arr['createdUserID'] = $data['createdUserName'];
            $master_arr['createdDateTime'] = $data['createdDateTime'];
            $master_arr['modifiedPCID'] = $data['modifiedPCID'];
            $master_arr['modifiedUserID'] = $data['createdUserName'];
            $master_arr['modifiedUserName'] = $data['createdUserName'];
            $master_arr['modifiedDateTime'] = $data['createdDateTime'];
            $insert = $this->db->insert('srp_erp_chartofaccounts', $master_arr);
            $GLAutoID = $this->db->insert_id();
            /*add salary category*/
            $salary_category_arr = array(
                array('salaryDescription' => 'Basic Salary', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code']),
                array('salaryDescription' => 'Transport Allowance', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code']),
                array('salaryDescription' => 'Housing Allowance', 'companyID' => $company_id, 'salaryCategoryType' => 'A', 'GLCode' => $GLAutoID, 'companyCode' => $data['company_code'])
            );

            $this->db->insert_batch('srp_erp_pay_salarycategories', $salary_category_arr);
            $this->save_users($company_id,$data_detail,$user_group_id);
            return ['s', 'Company : ' . $data['company_name'] . ' Saved Successfully.', 'last_id' => $company_id];
        }
    }
    function fetch_currency_arr()
    {
        $this->mainDB = $this->load->database('db2', TRUE);
        $this->mainDB->select("currencyID,CurrencyCode,CurrencyName,DecimalPlaces");
        $this->mainDB->from('srp_erp_currencymaster');
        $currency = $this->mainDB->get()->result_array();
        $currency_arr = array('' => 'Select Currency');
        if (isset($currency)) {
            foreach ($currency as $row) {
                $currency_arr[trim($row['currencyID'] ?? '')] = trim($row['CurrencyCode'] ?? '') . ' | ' . trim($row['CurrencyName'] ?? '');
            }
        }
        return $currency_arr;
    }

    function load_country_drop()
    {
        $this->mainDB->SELECT("countryID,CountryDes,countryShortCode");
        $this->mainDB->FROM('srp_erp_countrymaster');
        return $this->mainDB->get()->result_array();
    }

    function save_users($company_id,$data_detail,$user_group_id)
    {
        $this->load->helper('string');
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $this->load->library('sequence', $this->get_db_array());
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id', $company_id);
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();
        $companyID = $company_id;
        $token = random_string('alnum', 16);
        // user 1 //
        $user_data['Ename2'] = $data_detail['nameprimary'];
        $user_data['Gender'] = 1;
        $user_data['userType'] = 1;
        $user_data['EEmail'] =  $data_detail['usernameprimary'];
        $user_data['UserName'] =  $data_detail['usernameprimary'];
        $user_data['Password'] = md5($data_detail['passwordprimary']);
        $user_data['EmpImage'] = '/gs_sme/images/users/default.gif';
        $user_data['SchMasterId'] = $company_data['company_link_id'];
        $user_data['branchID'] = $company_data['branch_link_id'];
        $user_data['Erp_companyID'] = $companyID;
        $user_data['isSystemAdmin'] = 1;
        $user_data['isChangePassword'] = 0;
        $user_data['isPayrollEmployee'] = 0;
        $user_data['ECode'] = $this->sequence->sequence_generator_spurgo('EMP', 0, $company_data['company_id'], $company_data['company_code']);
        $this->db->insert('srp_employeesdetails', $user_data);
        $user_id = $this->db->insert_id();
        $user_data2['UserName'] =$data_detail['usernameprimary'];
        $user_data2['Password'] = md5($data_detail['passwordprimary']);
        $user_data2['companyID'] = $companyID;
        $user_data2['isSystemAdmin'] = 1;
        $user_data2['email'] =$data_detail['usernameprimary'];
        $user_data2['empID'] = $user_id;
        $user_data2['login_token'] = $token;
        $this->mainDB = $this->load->database('db2', TRUE);
        $this->mainDB->insert('user', $user_data2);
        $this->db->insert('srp_erp_employeenavigation', array('userGroupID' => $user_group_id, 'empID' => $user_id, 'companyID' => $company_data['company_id']));

        // user 2//
        if($data_detail['namesec'])
        {
            $user_data_02['Ename2'] = $data_detail['namesec'];
            $user_data_02['Gender'] = 1;
            $user_data_02['userType'] = 1;
            $user_data_02['EEmail'] =  $data_detail['usernamesec'];
            $user_data_02['UserName'] =  $data_detail['usernamesec'];
            $user_data_02['Password'] = md5($data_detail['passwordsec']);
            $user_data_02['EmpImage'] = '/gs_sme/images/users/default.gif';
            $user_data_02['SchMasterId'] = $company_data['company_link_id'];
            $user_data_02['branchID'] = $company_data['branch_link_id'];
            $user_data_02['Erp_companyID'] = $companyID;
            $user_data_02['isSystemAdmin'] = 0;
            $user_data_02['isPayrollEmployee'] = 0;
            $user_data_02['isChangePassword'] = 0;
            $user_data_02['ECode'] = $this->sequence->sequence_generator_spurgo('EMP', 0, $company_data['company_id'], $company_data['company_code']);
            $this->db->insert('srp_employeesdetails', $user_data_02);
            $user_id = $this->db->insert_id();
            $user_data2_02['UserName'] =$data_detail['usernamesec'];
            $user_data2_02['Password'] = md5($data_detail['passwordsec']);
            $user_data2_02['companyID'] = $companyID;
            $user_data2_02['isSystemAdmin'] = 0;
            $user_data2_02['email'] =$data_detail['usernamesec'];
            $user_data2_02['empID'] = $user_id;
            $this->mainDB = $this->load->database('db2', TRUE);
            $this->mainDB->insert('user', $user_data2_02);
            $this->db->insert('srp_erp_employeenavigation', array('userGroupID' => $user_group_id, 'empID' => $user_id, 'companyID' => $company_data['company_id']));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'User : ' . $user_data['UserName'] . '  Saved Failed '];
        } else {
            $this->db->trans_commit();
            $this->save_employee_navigations($companyID,$user_data2['login_token']);
        }
    }
    function save_employee_navigations($companyID,$user1token)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $nav = array();
        $companyID = $companyID;
        $navigationMenuID = $this->config->item('modules');
        $count = count($navigationMenuID);

        $navigation_ID = null;
        for ($i = 0; $i < $count; $i++) {
            $nav[$i]['navigationMenuID'] = $navigationMenuID[$i];
            $nav[$i]['companyID'] = $companyID;
            if (($count - 1) > $i) {
                $navigation_ID .= $navigationMenuID[$i] . ',';
            } else {
                $navigation_ID .= $navigationMenuID[$i];
            }
        }
        if (!empty($nav)) {
            $this->db->delete('srp_erp_moduleassign', array('companyID' => $companyID));
            $nav = array_values($nav);
            $this->db->insert_batch('srp_erp_moduleassign', $nav);
        }

        $usergroupArr = $this->db->query("SELECT * FROM srp_erp_usergroups WHERE companyID = {$companyID} AND description IN ('Administrator') ")->result_array();

        if (!empty($usergroupArr)) {
            foreach ($usergroupArr as $row) {
                /*delete previous links*/
                $this->db->delete('srp_erp_navigationusergroupsetup', array('userGroupID' => $row['userGroupID']));

                $navigationMenuID =  $this->config->item('modules');

                switch ($row['description']) {
                    case 'CEO':
                    case 'CFO':
                    case 'Administrator':
                        $navigation_ID = implode(',', $navigationMenuID);
                        break;
                    case 'Finance Manager':
                        //remove HRMS
                        $hrms = array_search(38, $navigationMenuID);
                        unset($navigationMenuID[$hrms]);
                        $navigation_ID = implode(', ', $navigationMenuID);
                        break;
                    case 'HR Manager':
                        $array = array(38, 29, 329); //HRMS , My Profile ,Dashboard
                        $result = array_intersect($navigationMenuID, $array);
                        $navigation_ID = implode(', ', $result);
                        break;
                    case 'Sales Manager':
                        $array = array(361, 34, 29);  // - Sales & Marketing , Account Receivable , Dashboard
                        $result = array_intersect($navigationMenuID, $array);
                        $navigation_ID = implode(', ', $result);
                        break;

                    default:
                        continue;
                }
                if (empty($navigation_ID)) {
                    continue; //continue if value empty;
                }
                $data_nav = $this->db->query("SELECT srp_erp_navigationmenus.navigationMenuID,srp_erp_navigationmenus.levelNo,srp_erp_navigationmenus.sortOrder, IFNULL(srp_erp_navigationusergroupsetup.navigationMenuID, 0) AS navID FROM srp_erp_navigationmenus LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID AND userGroupID = {$row['userGroupID']} WHERE srp_erp_navigationmenus.navigationMenuID NOT IN (SELECT srp_erp_navigationmenus.navigationMenuID FROM srp_erp_navigationmenus LEFT JOIN `srp_erp_moduleassign` ON srp_erp_navigationmenus.navigationMenuID = srp_erp_moduleassign.navigationMenuID AND companyID = '{$companyID}' AND srp_erp_navigationmenus.navigationMenuID IN ({$navigation_ID})  WHERE masterID IS NULL AND moduleID IS NULL) ORDER BY levelNo , sortOrder")->result_array();
                $navigationID = null;
                $nav_count = count($data_nav);
                for ($i = 0; $i < $nav_count; $i++) {
                    if (($nav_count - 1) > $i) {
                        $navigationID .= $data_nav[$i]['navigationMenuID'] . ',';
                    } else {
                        $navigationID .= $data_nav[$i]['navigationMenuID'];
                    }
                }

                $this->db->query("INSERT srp_erp_navigationusergroupsetup (userGroupID ,navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist) SELECT {$row['userGroupID']},navigationMenuID,description,masterID,url,pageID,pageTitle,pageIcon,levelNo,sortOrder,isSubExist FROM srp_erp_navigationmenus WHERE navigationMenuID IN ({$navigationID})");
            }
        }
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id',$companyID);
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $this->db->select('EIdNo,Ename1,Ename2');
        $this->db->where('Erp_companyID',$companyID);
        $this->db->where('isSystemAdmin', 1);
        $this->db->from('srp_employeesdetails');
        $users = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->join('srp_erp_documentcodes', 'srp_erp_documentcodes.documentID=srp_erp_approvalusers.documentID', 'inner');
        $this->db->where('companyID',$companyID);
        $this->db->group_by('moduleID');
        $this->db->from('srp_erp_approvalusers');
        $exist = $this->db->get()->result_array();

        $this->db->select('documentID,document,moduleID');
        $this->db->where_in('moduleID', $navigationMenuID);
        $this->db->from('srp_erp_documentcodes');
        $code = $this->db->get()->result_array();
        if (empty($exist)) {
            if (!empty($users)) {
                $this->db->select('documentID,document');
                $this->db->where_in('moduleID', $navigationMenuID);
                $this->db->from('srp_erp_documentcodes');
                $code = $this->db->get()->result_array();
                $approvals = array();
                foreach ($users as $user) {
                    for ($i = 0; $i < count($code); $i++) {
                        $approvals[$i]['levelNo'] = 1;
                        $approvals[$i]['documentID'] = $code[$i]['documentID'];
                        $approvals[$i]['document'] = $code[$i]['document'];
                        $approvals[$i]['employeeID'] = $user["EIdNo"];
                        $approvals[$i]['employeeName'] = $user['Ename1'] . ' ' . $user['Ename2'];
                        $approvals[$i]['companyID'] = $company_data['company_id'];
                        $approvals[$i]['companyCode'] = $company_data['company_code'];
                        $approvals[$i]['Status'] = 1;
                        $approvals[$i]['createdUserGroup'] = NULL;
                        $approvals[$i]['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                        $approvals[$i]['createdUserID'] = null;
                        $approvals[$i]['createdUserName'] = null;
                        $approvals[$i]['createdDateTime'] = date('Y-m-d H:i:s');
                    }
                }
                if (!empty($approvals)) {
                    $approvals = array_values($approvals);
                    $this->db->insert_batch('srp_erp_approvalusers', $approvals);
                }
            }
        } else {
            $moduleIDs = array_column($exist, 'moduleID');
            $arrayDiff = array_diff($moduleIDs, $navigationMenuID);

            if ($arrayDiff) {
                $arrayDiff = join(',', $arrayDiff);
                $this->db->query("DELETE srp_erp_approvalusers FROM srp_erp_approvalusers 
                                INNER JOIN srp_erp_documentcodes ON srp_erp_documentcodes.documentID=srp_erp_approvalusers.documentID 
                                WHERE moduleID IN({$arrayDiff}) AND companyID = $companyID");
            }

            /* var_dump($arrayDiff);
             exit;*/
            $arrayDiff2 = array_diff($navigationMenuID, $moduleIDs);
            if ($arrayDiff2) {
                $this->db->select('documentID,document');
                $this->db->where_in('moduleID', $arrayDiff2);
                $this->db->from('srp_erp_documentcodes');
                $code = $this->db->get()->result_array();

                $approvals2 = array();
                if (!empty($users)) {
                    foreach ($users as $user) {
                        for ($i = 0; $i < count($code); $i++) {
                            $approvals2[$i]['levelNo'] = 1;
                            $approvals2[$i]['documentID'] = $code[$i]['documentID'];
                            $approvals2[$i]['document'] = $code[$i]['document'];
                            $approvals2[$i]['employeeID'] = $user["EIdNo"];
                            $approvals2[$i]['employeeName'] = $user['Ename1'] . ' ' . $user['Ename2'];
                            $approvals2[$i]['companyID'] = $company_data['company_id'];
                            $approvals2[$i]['companyCode'] = $company_data['company_code'];
                            $approvals2[$i]['Status'] = 1;
                            $approvals2[$i]['createdUserGroup'] = NULL;
                            $approvals2[$i]['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
                            $approvals2[$i]['createdUserID'] = null;
                            $approvals2[$i]['createdUserName'] = null;
                            $approvals2[$i]['createdDateTime'] = date('Y-m-d H:i:s');
                        }
                    }
                }
                if (!empty($approvals2)) {
                    $approvals2 = array_values($approvals2);
                    $this->db->insert_batch('srp_erp_approvalusers', $approvals2);
                }
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $this->save_financial_year($companyID);



            $data_maindb = array(
                'confirmedYN' => 1,
            );
            $this->db->where('company_id', $companyID)->update('srp_erp_company', $data_maindb);
            $this->load->database($this->get_db_array(), FALSE, TRUE);
            $data = array(
                'confirmedYN' => 1,
            );
            $this->db->where('company_id',$companyID);
            $this->db->update('srp_erp_company', $data);

            $this->db->query("UPDATE srp_erp_company
JOIN srp_erp_countrymaster ON srp_erp_company.company_country=srp_erp_countrymaster.CountryDes
set srp_erp_company.countryID=srp_erp_countrymaster.countryID");

            $this->db->query("UPDATE srp_nationality
JOIN srp_erp_countrymaster on srp_nationality.nationality=srp_erp_countrymaster.nationality
set srp_nationality.countryID=srp_erp_countrymaster.countryID");

            $this->db->query("
INSERT INTO srp_erp_pay_templatefields (
    fieldName,
    caption,
    fieldType,
    salaryCatID,
    companyID,
    companyCode
) (
    SELECT
        salary.salaryDescription AS fieldName,
        salary.salaryDescription AS caption,
        salary.salarycategoryType AS fieldType,
        salary.salaryCategoryID AS salaryCatID,
        salary.companyID AS companyID,
        salary.companycode AS companyCode
    FROM
        srp_erp_pay_salarycategories salary
    WHERE
        salary.salaryCategoryID NOT IN (
            SELECT
                ifnull(salarycatID, 0)
            FROM
                srp_erp_pay_templatefields
            GROUP BY
                salaryCatID
        )
)");
            $employee_details = $this->db->query("SELECT EIdNo, ECode, EEmail, Ename2 FROM srp_employeesdetails WHERE Erp_companyID = $companyID AND isSystemAdmin = 1 ")->row_array();
            /********User Activation Email Start********/
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
            $link2 = base_url().'index.php/Login/login_submitspur_go?Token='.$user1token.'';
            $link = "$protocol$_SERVER[HTTP_HOST]".$link2;

            $param['empName'] =$param['empName'] = 'Test';
            $param['body'] = '<div style="width: 80%;margin: auto;background-color:#fbfbfb;padding: 2%;font-family: sans-serif;"><p>Thank you for registering. To actvate your account, please click on the following link (this will confirm your email address).</p><br><br><a href="'.$link.'"
>Click here to access the Login.</a><br><p>Thank You</p></div>';

            $emailsubject = "Activation Required to Activate your Account";
            $mail_config['wordwrap'] = TRUE;
            $mail_config['protocol'] = 'smtp';
            $mail_config['smtp_host'] = 'smtp.sendgrid.net';
            $mail_config['smtp_user'] = 'apikey';
            $mail_config['smtp_pass'] = SEND_GRID_EMAIL_KEY;
            $mail_config['smtp_crypto'] = 'tls';
            $mail_config['smtp_port'] = '587';
            $mail_config['crlf'] = "\r\n";
            $mail_config['newline'] = "\r\n";
            $this->load->library('email', $mail_config);
            if (hstGeras == 1) {
                $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
            } else {
                $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
            }
            $this->email->set_mailtype('html');
            $this->email->subject($emailsubject);
            $this->email->message($this->load->view('system/email_template/email_approval_template_log_spur_go', $param, TRUE));
            $this->email->to($employee_details['EEmail']);
            $tmpResult = $this->email->send();
            if ($tmpResult) {
                $this->email->clear(TRUE);
            }
            /********User Activation Email End********/
        }
        return true;
    }

    function save_financial_year($companyID)
    {
        $this->load->database($this->get_db_array(), FALSE, TRUE);
        $this->db->trans_start();
        $x = 0;
        $this->db->select('company_link_id,branch_link_id,company_code,company_id');
        $this->db->where('company_id',$companyID);
        $this->db->from('srp_erp_company');
        $company_data = $this->db->get()->row_array();

        $this->db->select('beginingDate,endingDate,companyFinanceYearID,isCurrent');
        $this->db->where('companyID', $company_data['company_id']);
        $this->db->where('isCurrent', 1);
        $this->db->from('srp_erp_companyfinanceyear');
        $iscurrentChk = $this->db->get()->row_array();

        $company_id = $company_data['company_id'];
        $Date = new DateTime('now');
        $Date_end = new DateTime('now');
        $start_date1 = $Date->modify('-5 year')->format('Y-01-01');
        $end_date1 = $Date_end->modify('10 year')->format('Y-01-01');
        $financeyearmonth = $this->input->post('financeyearmonth');
        $lastday = intval(date('t', strtotime('2020-'.$financeyearmonth.'-01')));
        $daterange =  ($end_date1 - $start_date1);
        for ($i=0;$i<($daterange);$i++)
        {
            $start_date = date(''.($start_date1)+$i.'-'.$financeyearmonth.'-01');
            $date = new DateTime(''.($start_date1)+$i.'-'.$financeyearmonth.'-'.$lastday.' 00:00:00');
            $date->modify('12 month');
            $end_date = $date->format("Y-m-d");
            $data['beginingDate'] = $start_date;
            $data['endingDate'] = $end_date;
            $data['comments'] =  $company_data['company_code'].' - Financial Year';
            $data['isActive'] = 1;
            $data['isClosed'] = 0;
            if ($iscurrentChk['isCurrent'] == 1) {
                $data['isCurrent'] = 0;
            } else {
                $data['isCurrent'] = 1;
            }
            $data['companyCode'] = $company_data['company_code'];
            $data['companyID'] = $company_id;
            $data['createdUserGroup'] = null;
            $data['createdPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $data['createdUserID'] = null;//$this->session->userdata("username");
            $data['createdUserName'] = null;//$this->session->userdata("username");
            $data['modifiedDateTime'] = date('Y-m-d h:i:s');
            $data['createdDateTime'] = $data['modifiedDateTime'];
            $data['modifiedPCID'] = null;//gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $data['modifiedUserID'] = null;//$this->session->userdata("username");
            $data['modifiedUserName'] = null;//$this->session->userdata("username");
            $this->db->insert('srp_erp_companyfinanceyear', $data);

            $last_id = $this->db->insert_id();
            $date_arr = array();
            $first_date = $start_date;
            $next_date = $end_date;
            while ($first_date <= $next_date) {
                $last_date = date("Y-m-t", strtotime($first_date));
                array_push($date_arr, array('dateFrom' => $first_date, 'dateTo' => $last_date, 'companyFinanceYearID' => $last_id, 'companyID' => $data['companyID'], 'companyCode' => $data['companyCode'], 'isActive' => 1));
                $first_date = date("Y-m-d", strtotime($first_date . '+ 1 month'));
                $x++;
            }
            $date_arr = array_values($date_arr);
            $this->db->insert_batch('srp_erp_companyfinanceperiod', $date_arr);
            if ($iscurrentChk['isCurrent'] == 1) {
                $data_comp = array(
                    'companyFinanceYear' => ($iscurrentChk['beginingDate'] . " - " . ($iscurrentChk['endingDate'])),
                    'companyFinanceYearID' => $iscurrentChk['companyFinanceYearID'],
                );

                $this->db->where("company_id", $company_id);
                $this->db->update("srp_erp_company", $data_comp);
            } else {
                $data_comp1 = array(
                    'companyFinanceYear' => ($start_date . " - " .$end_date),
                    'companyFinanceYearID' => $last_id,
                );

                $this->db->where("company_id", $company_id);
                $this->db->update("srp_erp_company", $data_comp1);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return ['e', 'Error in Financial year create process<p>' . $this->db->_error_message()];
        } else {
            $this->db->trans_commit();
            return ['s', 'Financial year created successfully with Finance Period.', 'last_id' => $last_id];
        }
    }


    function get_db_array()
    {
        $config['hostname'] = trim($this->encryption->decrypt($this->db_host));
        $config['username'] = trim($this->encryption->decrypt($this->db_username));
        $config['password'] = trim($this->encryption->decrypt($this->db_password));
        $config['database'] = trim($this->encryption->decrypt($this->db_name));
        $config['dbdriver'] = 'mysqli';
        $config['db_debug'] = TRUE;
        return $config;
    }


}