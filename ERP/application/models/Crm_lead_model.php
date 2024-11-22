<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crm_lead_model extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function save_lead_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
        $leadstatusid = trim($this->input->post('statusID') ?? '');

        $expirydate = trim($this->input->post('expirydate') ?? '');
        $format_expirydate = null;
        if (isset($expirydate) && !empty($expirydate)) {
            $format_expirydate = input_format_date($expirydate, $date_format_policy);
        }

        $leadMasterID = trim($this->input->post('leadID') ?? '');
        $organization = $this->input->post('linkorganization');
        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $linkedorganizationID = 0;
        if (!empty($this->input->post('linkorganization'))) {
            $linkedorganizationID = $this->input->post('linkorganization');
        }

        $isclosed = 0;

            $leadstatustype = $this->db->query("select * from srp_erp_crm_status where companyID = $companyID AND  leadStatusID = '{$leadstatusid}' ")->row_array();


        if ($leadstatustype['statusType'] == 1) {
            $data['isClosed'] = 1;
        }
        $contactID = 0;
        if (!empty($this->input->post('contactID'))) {
            $contactID = $this->input->post('contactID');
        }


        $data['prefix'] = trim($this->input->post('prefix') ?? '');
        $data['firstName'] = trim($this->input->post('firstName') ?? '');
        $data['lastName'] = trim($this->input->post('lastName') ?? '');
        $data['title'] = trim($this->input->post('title') ?? '');
        $data['organization'] = trim($this->input->post('organization') ?? '');
        $data['linkedorganizationID'] = $linkedorganizationID;
        $data['statusID'] = trim($this->input->post('statusID') ?? '');
        $data['responsiblePersonEmpID'] = trim($this->input->post('responsiblePersonEmpID') ?? '');
        //$data['ratingID'] = trim($this->input->post('ratingID') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['phoneMobile'] = trim($this->input->post('phoneMobile') ?? '');
        $data['phoneHome'] = trim($this->input->post('phoneHome') ?? '');
        $data['fax'] = trim($this->input->post('fax') ?? '');
        $data['website'] = trim($this->input->post('website') ?? '');
        $data['industry'] = trim($this->input->post('industry') ?? '');
        $data['numberofEmployees'] = trim($this->input->post('numberofEmployees') ?? '');
        $data['sourceID'] = trim($this->input->post('sourceID') ?? '');
        $data['postalCode'] = trim($this->input->post('postalcode') ?? '');
        $data['city'] = trim($this->input->post('city') ?? '');
        $data['state'] = trim($this->input->post('state') ?? '');
        $data['countryID'] = trim($this->input->post('countryID') ?? '');
        $data['address'] = trim($this->input->post('address') ?? '');
        $data['contactID'] = $contactID;
        $data['expiryDate'] = $format_expirydate;
        $data['description'] = trim($this->input->post('description') ?? '');

        if ($leadMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('leadID', $leadMasterID);
            $update = $this->db->update('srp_erp_crm_leadmaster', $data);

            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 5, 'documentAutoID' => $leadMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 5, 'documentAutoID' => $leadMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 5;
                $permission_master['documentAutoID'] = $leadMasterID;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 5;
                                $permission_detail['documentAutoID'] = $leadMasterID;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
            }

            if ($leadstatustype['statusType'] == 2) {
                $leadMasterDetail = $this->db->query("SELECT * FROM srp_erp_crm_leadmaster where leadID = '{$leadMasterID}'")->row_array();
                if ($leadMasterDetail['contactID'] == 0) {
                    $this->load->library('sequence');
                    $data['companyID'] = $companyID;
                    $data_contact['documentSystemCode'] = $this->sequence->sequence_generator('CRM-CONT');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_contactmaster WHERE companyID = $companyID")->row_array();
                    $data_contact['serialNo'] = $serial['serialNumber'];
                    $data_contact['documentID'] = 'CRM-CONT';

                    /* $data_contact['documentSystemCode'] = ($company_code . '/' . 'CRM-CONT' . str_pad($data['serialNo'], 6,
                                        '0', STR_PAD_LEFT));*/

                    $data_contact['prefix'] = $leadMasterDetail['prefix'];
                    $data_contact['firstName'] = $leadMasterDetail['firstName'];
                    $data_contact['lastName'] = $leadMasterDetail['lastName'];
                    $data_contact['occupation'] = $leadMasterDetail['title'];
                    //$data_contact['department'] = $leadMasterDetail['department'];
                    $data_contact['organization'] = $leadMasterDetail['organization'];
                    $data_contact['email'] = $leadMasterDetail['email'];
                    $data_contact['phoneMobile'] = $leadMasterDetail['phoneMobile'];
                    $data_contact['phoneHome'] = $leadMasterDetail['phoneHome'];
                    $data_contact['fax'] = $leadMasterDetail['fax'];
                    $data_contact['postalCode'] = $leadMasterDetail['postalCode'];
                    $data_contact['city'] = $leadMasterDetail['city'];
                    $data_contact['state'] = $leadMasterDetail['state'];
                    $data_contact['countryID'] = $leadMasterDetail['countryID'];
                    $data_contact['address'] = $leadMasterDetail['address'];
                    $data_contact['leadID'] = $leadMasterID;
                    $data_contact['companyID'] = $companyID;
                    $data_contact['createdUserGroup'] = $this->common_data['user_group'];
                    $data_contact['createdPCID'] = $this->common_data['current_pc'];
                    $data_contact['createdUserID'] = $this->common_data['current_userID'];
                    $data_contact['createdUserName'] = $this->common_data['current_user'];
                    $data_contact['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_contactmaster', $data_contact);
                    $contactID_last = $this->db->insert_id();

                    $contactID_permission_update_master['documentID'] = 6;
                    $contactID_permission_update_master['documentAutoID'] = $contactID_last;
                    $contactID_permission_update_master['permissionID'] = 1;
                    $contactID_permission_update_master['companyID'] = $companyID;
                    $contactID_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                    $contactID_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                    $contactID_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                    $contactID_permission_update_master['createdUserName'] = $this->common_data['current_user'];
                    $contactID_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $contactID_permission_update_master);

                } else {
                    $contactID_last = $leadMasterDetail['contactID'];
                }
                $organizationDetail = $this->db->query("SELECT Name FROM srp_erp_crm_organizations where organizationID = '{$leadMasterDetail['linkedorganizationID']}'")->row_array();
                $leadProductDetail = $this->db->query("SELECT transactionCurrencyID,SUM(price) as productTotal FROM srp_erp_crm_leadproducts where leadID = '{$leadMasterID}'")->row_array();
                if ($leadMasterDetail['linkedorganizationID'] == 0) {
                    $this->load->library('sequence');
                    $data_organization['documentSystemCode'] = $this->sequence->sequence_generator('CRM-ORG');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_organizations WHERE companyID = $companyID")->row_array();
                    $data_organization['serialNo'] = $serial['serialNumber'];
                    $data_organization['documentID'] = 'CRM-ORG';
                    /* $data_organization['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                             '0', STR_PAD_LEFT));*/
                    $data_organization['Name'] = $leadMasterDetail['organization'];
                    $data_organization['industry'] = $leadMasterDetail['industry'];
                    $data_organization['numberofEmployees'] = $leadMasterDetail['numberofEmployees'];
                    $data_organization['email'] = $leadMasterDetail['email'];
                    $data_organization['telephoneNo'] = $leadMasterDetail['phoneHome'];
                    $data_organization['fax'] = $leadMasterDetail['fax'];
                    $data_organization['website'] = $leadMasterDetail['website'];
                    $data_organization['companyID'] = $companyID;
                    $data_organization['createdUserGroup'] = $this->common_data['user_group'];
                    $data_organization['createdPCID'] = $this->common_data['current_pc'];
                    $data_organization['createdUserID'] = $this->common_data['current_userID'];
                    $data_organization['createdUserName'] = $this->common_data['current_user'];
                    $data_organization['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_organizations', $data_organization);
                    $organizationID_last = $this->db->insert_id();

                    $organization_permission_update_master['documentID'] = 8;
                    $organization_permission_update_master['documentAutoID'] = $organizationID_last;
                    $organization_permission_update_master['permissionID'] = 1;
                    $organization_permission_update_master['companyID'] = $companyID;
                    $organization_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                    $organization_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                    $organization_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                    $organization_permission_update_master['createdUserName'] = $this->common_data['current_user'];
                    $organization_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $organization_permission_update_master);
                }
                $this->load->library('sequence');
                $data_opportunity['documentSystemCode'] = $this->sequence->sequence_generator('CRM-OPP');
                $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_opportunity WHERE companyID = $companyID")->row_array();
                $data_opportunity['serialNo'] = $serial['serialNumber'];
                $data_opportunity['documentID'] = 'CRM-OPP';
                /* $data_opportunity['documentSystemCode'] = ($company_code . '/' . 'CRM-OPP' . str_pad($data['serialNo'], 6,
               '0', STR_PAD_LEFT));*/
                $data_opportunity['opportunityname'] = "New Opportunity";
                $data_opportunity['responsibleEmpID'] = $leadMasterDetail['responsiblePersonEmpID'];
                $data_opportunity['leadID'] = $leadMasterID;
                $data_opportunity['transactionAmount'] = $leadProductDetail['productTotal'];
                $data_opportunity['transactionCurrencyID'] = $leadProductDetail['transactionCurrencyID'];
                $data_opportunity['transactionCurrencyExchangeRate'] = 1;
                $data_opportunity['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data_opportunity['transactionCurrencyID']);
                $data_opportunity['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $default_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyLocalCurrencyID']);
                $data_opportunity['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
                $data_opportunity['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data_opportunity['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyReportingCurrencyID']);
                $data_opportunity['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
                $data_opportunity['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $data_opportunity['companyID'] = $companyID;
                $data_opportunity['createdUserGroup'] = $this->common_data['user_group'];
                $data_opportunity['createdPCID'] = $this->common_data['current_pc'];
                $data_opportunity['createdUserID'] = $this->common_data['current_userID'];
                $data_opportunity['createdUserName'] = $this->common_data['current_user'];
                $data_opportunity['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_opportunity', $data_opportunity);
                $opportunity_last = $this->db->insert_id();
                if ($opportunity_last) {

                    $permission_update_master['documentID'] = 4;
                    $permission_update_master['documentAutoID'] = $opportunity_last;
                    $permission_update_master['permissionID'] = 1;
                    $permission_update_master['companyID'] = $companyID;
                    $permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                    $permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                    $permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                    $permission_update_master['createdUserName'] = $this->common_data['current_user'];
                    $permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $permission_update_master);

                    $data_link_contact['documentID'] = 4;
                    $data_link_contact['MasterAutoID'] = $opportunity_last;
                    $data_link_contact['relatedDocumentID'] = 6;
                    $data_link_contact['relatedDocumentMasterID'] = $contactID_last;
                    $data_link_contact['searchValue'] = $leadMasterDetail['firstName'] . " " . $leadMasterDetail['lastName'];
                    $data_link_contact['originFrom'] = NULL;
                    $data_link_contact['companyID'] = $companyID;
                    $data_link_contact['createdUserGroup'] = $this->common_data['user_group'];
                    $data_link_contact['createdPCID'] = $this->common_data['current_pc'];
                    $data_link_contact['createdUserID'] = $this->common_data['current_userID'];
                    $data_link_contact['createdUserName'] = $this->common_data['current_user'];
                    $data_link_contact['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_link', $data_link_contact);

                    if ($leadMasterDetail['linkedorganizationID'] == 0) {
                        $data_link_organization['documentID'] = 4;
                        $data_link_organization['MasterAutoID'] = $opportunity_last;
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $organizationID_last;
                        $data_link_organization['searchValue'] = $leadMasterDetail['organization'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);

                        $data_link_organization_toContact['documentID'] = 6;
                        $data_link_organization_toContact['MasterAutoID'] = $contactID_last;
                        $data_link_organization_toContact['relatedDocumentID'] = 8;
                        $data_link_organization_toContact['relatedDocumentMasterID'] = $organizationID_last;
                        $data_link_organization_toContact['searchValue'] = $leadMasterDetail['organization'];
                        $data_link_organization_toContact['originFrom'] = NULL;
                        $data_link_organization_toContact['companyID'] = $companyID;
                        $data_link_organization_toContact['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization_toContact['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization_toContact['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization_toContact['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization_toContact['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization_toContact);

                    } else {
                        $data_link_organization['documentID'] = 4;
                        $data_link_organization['MasterAutoID'] = $opportunity_last;
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                        $data_link_organization['searchValue'] = $organizationDetail['Name'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);

                        $data_link_organization['documentID'] = 6;
                        $data_link_organization['MasterAutoID'] = $contactID_last;
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                        $data_link_organization['searchValue'] = $organizationDetail['Name'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);
                    }

                    $update_lead['isClosed'] = 2;
                    $update_lead['modifiedPCID'] = $this->common_data['current_pc'];
                    $update_lead['modifiedUserID'] = $this->common_data['current_userID'];
                    $update_lead['modifiedUserName'] = $this->common_data['current_user'];
                    $update_lead['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->where('leadID', $leadMasterID);
                    $this->db->update('srp_erp_crm_leadmaster', $update_lead);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Lead Updated Successfully.', $leadMasterID);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $companyID;
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-LEAD');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_leadmaster WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-LEAD';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-LEAD' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_leadmaster', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 5;
                $permission_master['documentAutoID'] = $last_id;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 5;
                                $permission_detail['documentAutoID'] = $last_id;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
                if ($leadstatustype['statusType'] == 2) {
                    $leadMasterDetail = $this->db->query("SELECT * FROM srp_erp_crm_leadmaster where leadID = '{$last_id}'")->row_array();
                    if ($leadMasterDetail['contactID'] == 0) {
                        $this->load->library('sequence');
                        $data['companyID'] = $companyID;
                        $data_contact['documentSystemCode'] = $this->sequence->sequence_generator('CRM-CONT');
                        $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_contactmaster WHERE companyID = $companyID")->row_array();
                        $data_contact['serialNo'] = $serial['serialNumber'];
                        $data_contact['documentID'] = 'CRM-CONT';
                        $data_contact['prefix'] = $leadMasterDetail['prefix'];
                        $data_contact['firstName'] = $leadMasterDetail['firstName'];
                        $data_contact['lastName'] = $leadMasterDetail['lastName'];
                        $data_contact['occupation'] = $leadMasterDetail['title'];
                        //$data_contact['department'] = $leadMasterDetail['department'];
                        $data_contact['organization'] = $leadMasterDetail['organization'];
                        $data_contact['email'] = $leadMasterDetail['email'];
                        $data_contact['phoneMobile'] = $leadMasterDetail['phoneMobile'];
                        $data_contact['phoneHome'] = $leadMasterDetail['phoneHome'];
                        $data_contact['fax'] = $leadMasterDetail['fax'];
                        $data_contact['postalCode'] = $leadMasterDetail['postalCode'];
                        $data_contact['city'] = $leadMasterDetail['city'];
                        $data_contact['state'] = $leadMasterDetail['state'];
                        $data_contact['countryID'] = $leadMasterDetail['countryID'];
                        $data_contact['address'] = $leadMasterDetail['address'];
                        $data_contact['leadID'] = $last_id;
                        $data_contact['companyID'] = $companyID;
                        $data_contact['createdUserGroup'] = $this->common_data['user_group'];
                        $data_contact['createdPCID'] = $this->common_data['current_pc'];
                        $data_contact['createdUserID'] = $this->common_data['current_userID'];
                        $data_contact['createdUserName'] = $this->common_data['current_user'];
                        $data_contact['createdDateTime'] = $this->common_data['current_date'];

                        $this->db->insert('srp_erp_crm_contactmaster', $data_contact);
                        /* $data_contact['documentSystemCode'] = ($company_code . '/' . 'CRM-CONT' . str_pad($data['serialNo'], 6,
                        '0', STR_PAD_LEFT));*/


                        $contactID_last = $this->db->insert_id();
                        $contactID_permission_update_master['documentID'] = 6;
                        $contactID_permission_update_master['documentAutoID'] = $contactID_last;
                        $contactID_permission_update_master['permissionID'] = 1;
                        $contactID_permission_update_master['companyID'] = $companyID;
                        $contactID_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                        $contactID_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                        $contactID_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                        $contactID_permission_update_master['createdUserName'] = $this->common_data['current_user'];
                        $contactID_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_documentpermission', $contactID_permission_update_master);

                    } else {
                        $contactID_last = $leadMasterDetail['contactID'];
                    }
                    $organizationDetail = $this->db->query("SELECT Name FROM srp_erp_crm_organizations where organizationID = '{$leadMasterDetail['linkedorganizationID']}'")->row_array();
                    $leadProductDetail = $this->db->query("SELECT transactionCurrencyID,SUM(price) as productTotal FROM srp_erp_crm_leadproducts where leadID = '{$last_id}'")->row_array();
                    if ($leadMasterDetail['linkedorganizationID'] == 0) {
                        $this->load->library('sequence');
                        $data_organization['Name'] = $leadMasterDetail['organization'];
                        $data_organization['industry'] = $leadMasterDetail['industry'];
                        $data_organization['numberofEmployees'] = $leadMasterDetail['numberofEmployees'];
                        $data_organization['email'] = $leadMasterDetail['email'];
                        $data_organization['telephoneNo'] = $leadMasterDetail['phoneHome'];
                        $data_organization['fax'] = $leadMasterDetail['fax'];
                        $data_organization['website'] = $leadMasterDetail['website'];
                        $data_organization['companyID'] = $companyID;
                        $data_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_organization['createdDateTime'] = $this->common_data['current_date'];

                        $data_organization['documentSystemCode'] = $this->sequence->sequence_generator('CRM-ORG');
                        $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_organizations WHERE companyID = $companyID")->row_array();
                        $data_organization['serialNo'] = $serial['serialNumber'];
                        $data_organization['documentID'] = 'CRM-ORG';
                        /* $data_organization['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                                 '0', STR_PAD_LEFT));*/
                        $this->db->insert('srp_erp_crm_organizations', $data_organization);
                        $organizationID_last = $this->db->insert_id();

                        $organization_permission_update_master['documentID'] = 8;
                        $organization_permission_update_master['documentAutoID'] = $organizationID_last;
                        $organization_permission_update_master['permissionID'] = 1;
                        $organization_permission_update_master['companyID'] = $companyID;
                        $organization_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                        $organization_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                        $organization_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                        $organization_permission_update_master['createdUserName'] = $this->common_data['current_user'];
                        $organization_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_documentpermission', $organization_permission_update_master);
                    }
                    $this->load->library('sequence');
                    $data_opportunity['opportunityname'] = "New Opportunity";
                    $data_opportunity['responsibleEmpID'] = $leadMasterDetail['responsiblePersonEmpID'];
                    $data_opportunity['leadID'] = $last_id;
                    $data_opportunity['transactionAmount'] = $leadProductDetail['productTotal'];
                    $data_opportunity['transactionCurrencyID'] = $leadProductDetail['transactionCurrencyID'];
                    $data_opportunity['transactionCurrencyExchangeRate'] = 1;
                    $data_opportunity['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data_opportunity['transactionCurrencyID']);
                    $data_opportunity['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                    $default_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyLocalCurrencyID']);
                    $data_opportunity['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
                    $data_opportunity['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
                    $data_opportunity['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                    $reporting_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyReportingCurrencyID']);
                    $data_opportunity['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
                    $data_opportunity['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                    $data_opportunity['companyID'] = $companyID;
                    $data_opportunity['createdUserGroup'] = $this->common_data['user_group'];
                    $data_opportunity['createdPCID'] = $this->common_data['current_pc'];
                    $data_opportunity['createdUserID'] = $this->common_data['current_userID'];
                    $data_opportunity['createdUserName'] = $this->common_data['current_user'];
                    $data_opportunity['createdDateTime'] = $this->common_data['current_date'];

                    $data_opportunity['documentSystemCode'] = $this->sequence->sequence_generator('CRM-OPP');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_opportunity WHERE companyID = $companyID")->row_array();
                    $data_opportunity['serialNo'] = $serial['serialNumber'];
                    $data_opportunity['documentID'] = 'CRM-OPP';
                    /* $data_opportunity['documentSystemCode'] = ($company_code . '/' . 'CRM-OPP' . str_pad($data['serialNo'], 6,
                   '0', STR_PAD_LEFT));*/
                    $this->db->insert('srp_erp_crm_opportunity', $data_opportunity);
                    $opportunity_last = $this->db->insert_id();
                    if ($opportunity_last) {

                        $permission_update_master['documentID'] = 4;
                        $permission_update_master['documentAutoID'] = $opportunity_last;
                        $permission_update_master['permissionID'] = 1;
                        $permission_update_master['companyID'] = $companyID;
                        $permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                        $permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                        $permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                        $permission_update_master['createdUserName'] = $this->common_data['current_user'];
                        $permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_documentpermission', $permission_update_master);

                        $data_link_contact['documentID'] = 4;
                        $data_link_contact['MasterAutoID'] = $opportunity_last;
                        $data_link_contact['relatedDocumentID'] = 6;
                        $data_link_contact['relatedDocumentMasterID'] = $contactID_last;
                        $data_link_contact['searchValue'] = $leadMasterDetail['firstName'] . " " . $leadMasterDetail['lastName'];
                        $data_link_contact['originFrom'] = NULL;
                        $data_link_contact['companyID'] = $companyID;
                        $data_link_contact['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_contact['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_contact['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_contact['createdUserName'] = $this->common_data['current_user'];
                        $data_link_contact['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_contact);

                        if ($leadMasterDetail['linkedorganizationID'] == 0) {
                            $data_link_organization['documentID'] = 4;
                            $data_link_organization['MasterAutoID'] = $opportunity_last;
                            $data_link_organization['relatedDocumentID'] = 8;
                            $data_link_organization['relatedDocumentMasterID'] = $organizationID_last;
                            $data_link_organization['searchValue'] = $leadMasterDetail['organization'];
                            $data_link_organization['originFrom'] = NULL;
                            $data_link_organization['companyID'] = $companyID;
                            $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                            $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                            $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                            $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                            $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_crm_link', $data_link_organization);

                            $data_link_organization_toContact['documentID'] = 6;
                            $data_link_organization_toContact['MasterAutoID'] = $contactID_last;
                            $data_link_organization_toContact['relatedDocumentID'] = 8;
                            $data_link_organization_toContact['relatedDocumentMasterID'] = $organizationID_last;
                            $data_link_organization_toContact['searchValue'] = $leadMasterDetail['organization'];
                            $data_link_organization_toContact['originFrom'] = NULL;
                            $data_link_organization_toContact['companyID'] = $companyID;
                            $data_link_organization_toContact['createdUserGroup'] = $this->common_data['user_group'];
                            $data_link_organization_toContact['createdPCID'] = $this->common_data['current_pc'];
                            $data_link_organization_toContact['createdUserID'] = $this->common_data['current_userID'];
                            $data_link_organization_toContact['createdUserName'] = $this->common_data['current_user'];
                            $data_link_organization_toContact['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_crm_link', $data_link_organization_toContact);

                        } else {
                            $data_link_organization['documentID'] = 4;
                            $data_link_organization['MasterAutoID'] = $opportunity_last;
                            $data_link_organization['relatedDocumentID'] = 8;
                            $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                            $data_link_organization['searchValue'] = $organizationDetail['Name'];
                            $data_link_organization['originFrom'] = NULL;
                            $data_link_organization['companyID'] = $companyID;
                            $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                            $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                            $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                            $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                            $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_crm_link', $data_link_organization);

                            $data_link_organization['documentID'] = 6;
                            $data_link_organization['MasterAutoID'] = $contactID_last;
                            $data_link_organization['relatedDocumentID'] = 8;
                            $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                            $data_link_organization['searchValue'] = $organizationDetail['Name'];
                            $data_link_organization['originFrom'] = NULL;
                            $data_link_organization['companyID'] = $companyID;
                            $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                            $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                            $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                            $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                            $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                            $this->db->insert('srp_erp_crm_link', $data_link_organization);
                        }

                        $update_lead['isClosed'] = 2;
                        $update_lead['modifiedPCID'] = $this->common_data['current_pc'];
                        $update_lead['modifiedUserID'] = $this->common_data['current_userID'];
                        $update_lead['modifiedUserName'] = $this->common_data['current_user'];
                        $update_lead['modifiedDateTime'] = $this->common_data['current_date'];
                        $this->db->where('leadID', $last_id);
                        $this->db->update('srp_erp_crm_leadmaster', $update_lead);
                    }
                }
            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Lead Saved Successfully.');

            }
        }
    }

    function load_lead_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*,DATE_FORMAT(expiryDate,\'' . $convertFormat . '\') AS expiryDate');
        $this->db->where('leadID', $this->input->post('leadID'));
        $this->db->from('srp_erp_crm_leadmaster');
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('relatedDocumentMasterID');
        $this->db->where('documentID', 5);
        $this->db->where('relatedDocumentID', 8);
        $this->db->where('MasterAutoID', $this->input->post('leadID'));
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 5);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('leadID'));
        $data['permission'] = $this->db->get()->result_array();

        return $data;

    }

    function delete_lead_master()
    {

        $currentuser = current_userID();
        $company_id = current_companyID();
        $leadID = trim($this->input->post('leadID') ?? '');
        $createduser = $this->db->query("SELECT createdUserID FROM `srp_erp_crm_leadmaster` where companyID = $company_id and leadID = $leadID")->row_array();
        $issuperadmin = crm_isSuperAdmin();
        $isgroupadmin = crm_isGroupAdmin();
        if ($issuperadmin['isSuperAdmin'] == 1 || $createduser['createdUserID'] == $currentuser || $isgroupadmin['adminYN'] == 1) {
            $this->db->where('leadID', $this->input->post('leadID'));
            $results = $this->db->delete('srp_erp_crm_leadmaster');
            return array('s', 'Lead Deleted Successfully');
        } else {
            return array('w', 'You do not have the permission to delete');
        }
    }

    function lead_image_upload()
    {
        $this->load->library('s3');
        $leadID =  trim($this->input->post('leadID') ?? '');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT
	leadImage 
FROM
	`srp_erp_crm_leadmaster` 
WHERE
	companyID = $companyid 
	AND leadID = $leadID ")->row_array();

        if(!empty($itemimageexist))
        {
            $this->s3->delete('uploads/crm/lead/'.$itemimageexist['leadImage']);
        }
        $info = new SplFileInfo($_FILES["files"]["name"]);

        $fileName = 'Lead_'.$this->common_data['company_data']['company_code'].'_'. trim($this->input->post('leadID') ?? '') . '.' . $info->getExtension();
        $currentDatetime = format_date_mysql_datetime();

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

        $path = "uploads/crm/lead/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }

        $this->db->trans_start();
        /*$output_dir = "uploads/crm/lead/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/crm", 007);
            mkdir("uploads/crm/lead/", 007);
        }*/
        $currentDatetime = format_date_mysql_datetime();
    /*    $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);*/
        $currentdate = $currentDatetime;
      /*  $fileName = 'Lead_' . trim($this->input->post('leadID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);*/

        $data['leadImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('leadID', trim($this->input->post('leadID') ?? ''));
        $this->db->update('srp_erp_crm_leadmaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');


        }


    }


    function add_lead_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $leadID = trim($this->input->post('leadID') ?? '');

        $data['contactID'] = $leadID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 5;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_crm_contactnotes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Lead Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', 'Lead Note Saved Successfully.');

        }
    }

    function add_lead_product()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $leadID = trim($this->input->post('leadID') ?? '');
        $leadProductID = trim($this->input->post('leadProductID') ?? '');

        $data['leadID'] = $leadID;
        $data['productID'] = trim($this->input->post('productID') ?? '');
        $data['productDescription'] = trim($this->input->post('description') ?? '');
        $data['subscriptionAmount'] = trim($this->input->post('subscriptionamount') ?? '');
        $data['ImplementationAmount'] = trim($this->input->post('implementationamount') ?? '');
        $data['price'] = trim($this->input->post('price') ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        if ($leadProductID) {
            $this->db->where('leadProductID', $leadProductID);
            $this->db->update('srp_erp_crm_leadproducts', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead Product Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Lead Product Updated Successfully.');
            }

        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_leadproducts', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead Product Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Lead Product Added Successfully.');

            }
        }
    }

    function get_opportunityMaster($id)
    {
        $this->db->select("*");
        $this->db->from("srp_erp_crm_opportunity");
        $this->db->where("opportunityID", $id);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function save_opportunity_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];
        $status = trim($this->input->post('statusID') ?? '');
        $relatedAutoIDs = $this->input->post('relatedAutoID');
        $relatedTo = $this->input->post('relatedTo');
        $relatedToSearch = $this->input->post('related_search');
        $linkedFromOrigin = $this->input->post('linkedFromOrigin');
        $reason = trim($this->input->post('reason') ?? '');
        $Otherreason = trim($this->input->post('otherreson') ?? '');
        $typeopportunityID = trim($this->input->post('opportunitytype') ?? '');
        $typeopportunitydescription = trim($this->input->post('opportunityTypedescription') ?? '');

        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $forcastCloseDate = trim($this->input->post('forcastCloseDate') ?? '');
        $format_forcastCloseDate = null;
        if (isset($forcastCloseDate) && !empty($forcastCloseDate)) {
            $format_forcastCloseDate = input_format_date($forcastCloseDate, $date_format_policy);
        }
        $closedate = trim($this->input->post('closedate') ?? '');
        $closedateconvert = input_format_date($closedate, $date_format_policy);

        $opportunityMasterID = trim($this->input->post('opportunityID') ?? '');
        $iscloseconvertoproject = $this->db->query("SELECT * FROM `srp_erp_crm_status` WHERE companyID = '$companyID' AND statusID = '{$status}' AND documentID = 4")->row_array();
        $duration = 0;
        if (!empty($this->input->post('duration'))) {
            $duration = $this->input->post('duration');
        }

        $convertProject = 0;
        if ($iscloseconvertoproject['statusType'] != 0) {
            $convertProject = $iscloseconvertoproject['statusType'];
        }
        if ($iscloseconvertoproject['statusType'] == 1) {
            $data['actualClosedDate'] = $closedateconvert;

        }
        if(($iscloseconvertoproject['statusType']==1)||($iscloseconvertoproject['statusType']==2)||$iscloseconvertoproject['statusType']==3)
        {
            $data['closeCriteriaID'] = $reason;
            if($reason == -1)
            {
                $data['reason'] = $Otherreason;
            }
        }

        $data['opportunityName'] = trim($this->input->post('opportunityname') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['statusID'] = trim($this->input->post('statusID') ?? '');
        $data['closeStatus'] = $iscloseconvertoproject['statusType'];
        $data['type'] = $typeopportunityID;
        $data['typeDescription'] = $typeopportunitydescription;

        $data['categoryID'] = trim($this->input->post('categoryID') ?? '');
        $data['probabilityofwinning'] = trim($this->input->post('probabilityofwinning') ?? '');
        $data['forcastCloseDate'] = $format_forcastCloseDate;
        $data['responsibleEmpID'] = trim($this->input->post('responsiblePersonEmpID') ?? '');
        $data['transactionAmount'] = trim($this->input->post('price') ?? '');
        $data['valueType'] = trim($this->input->post('valueType') ?? '');
        $data['valueAmount'] = $duration;
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrencyExchangeRate'] = 1;
        $data['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['pipelineID'] = trim($this->input->post('pipelineID') ?? '');
        $data['pipelineStageID'] = trim($this->input->post('pipelineStageID') ?? '');

        if ($opportunityMasterID) {
            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_crm_link', array('documentID' => 4, 'MasterAutoID' => $opportunityMasterID));
                foreach ($relatedAutoIDs as $key => $itemAutoID) {
                    $data_link['documentID'] = 4;
                    $data_link['MasterAutoID'] = $opportunityMasterID;
                    $data_link['relatedDocumentID'] = $relatedTo[$key];
                    $data_link['relatedDocumentMasterID'] = $itemAutoID;
                    $data_link['searchValue'] = $relatedToSearch[$key];
                    $data_link['originFrom'] = $linkedFromOrigin[$key];
                    $data_link['companyID'] = $companyID;
                    $data_link['createdUserGroup'] = $this->common_data['user_group'];
                    $data_link['createdPCID'] = $this->common_data['current_pc'];
                    $data_link['createdUserID'] = $this->common_data['current_userID'];
                    $data_link['createdUserName'] = $this->common_data['current_user'];
                    $data_link['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_link', $data_link);
                }
            }
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('opportunityID', $opportunityMasterID);
            $update = $this->db->update('srp_erp_crm_opportunity', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 4, 'documentAutoID' => $opportunityMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 4, 'documentAutoID' => $opportunityMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 4;
                $permission_master['documentAutoID'] = $opportunityMasterID;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 4;
                                $permission_detail['documentAutoID'] = $opportunityMasterID;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
                if ($iscloseconvertoproject['statusType'] == 2) {
                    $this->load->library('sequence');
                    $project_data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-PRO');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_project WHERE companyID = $companyID")->row_array();
                    $project_data['serialNo'] = $serial['serialNumber'];
                    $project_data['documentID'] = 'CRM-PRO';
                    /* $project_data['documentSystemCode'] = ($company_code . '/' . 'CRM-PRO' . str_pad($data['serialNo'], 6,
                   '0', STR_PAD_LEFT));*/
                    $masterDetail = $this->get_opportunityMaster($opportunityMasterID);
                    $project_data['projectName'] = $masterDetail['opportunityName'];
                    $project_data['description'] = $masterDetail['description'];
                    $project_data['categoryID'] = $masterDetail['categoryID'];
                    $project_data['opportunityID'] = $opportunityMasterID;
                    $project_data['probabilityofwinning'] = $masterDetail['probabilityofwinning'];
                    $project_data['forcastCloseDate'] = $masterDetail['forcastCloseDate'];
                    $project_data['responsibleEmpID'] = $masterDetail['responsibleEmpID'];
                    $project_data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
                    $project_data['transactionAmount'] = $masterDetail['transactionAmount'];
                    $project_data['transactionCurrencyExchangeRate'] = $masterDetail['transactionCurrencyExchangeRate'];
                    $project_data['transactionDecimalPlaces'] = $masterDetail['transactionDecimalPlaces'];
                    $project_data['companyLocalCurrencyID'] = $masterDetail['companyLocalCurrencyID'];
                    $project_data['companyLocalAmount'] = $masterDetail['companyLocalAmount'];
                    $project_data['companyLocalCurrencyExchangeRate'] = $masterDetail['companyLocalCurrencyExchangeRate'];
                    $project_data['companyLocalDecimalPlaces'] = $masterDetail['companyLocalDecimalPlaces'];
                    $project_data['companyReportingCurrencyID'] = $masterDetail['companyReportingCurrencyID'];
                    $project_data['companyReportingAmount'] = $masterDetail['companyReportingAmount'];
                    $project_data['companyReportingCurrencyExchangeRate'] = $masterDetail['companyReportingCurrencyExchangeRate'];
                    $project_data['companyReportingDecimalPlaces'] = $masterDetail['companyReportingDecimalPlaces'];
                    $project_data['valueType'] = $masterDetail['valueType'];
                    $project_data['valueAmount'] = $masterDetail['valueAmount'];
                    $project_data['statusID'] = '';
                    $project_data['closeStatus'] = $masterDetail['closeStatus'];
                    $project_data['reason'] = $masterDetail['reason'];
                    $project_data['pipelineID'] = 0;
                    $project_data['pipelineStageID'] = 0;
                    $project_data['leadID'] = $masterDetail['leadID'];
                    $project_data['projectStatus'] = 0;
                    $project_data['projectStartDate'] = $masterDetail['forcastCloseDate'];
                    $project_data['companyID'] = $companyID;
                    $project_data['createdUserGroup'] = $this->common_data['user_group'];
                    $project_data['createdPCID'] = $this->common_data['current_pc'];
                    $project_data['createdUserID'] = $this->common_data['current_userID'];
                    $project_data['createdUserName'] = $this->common_data['current_user'];
                    $project_data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_project', $project_data);
                    $projectID = $this->db->insert_id();
                    if ($projectID) {
                        $project_permission_master['documentID'] = 9;
                        $project_permission_master['documentAutoID'] = $projectID;
                        $project_permission_master['permissionID'] = 1;
                        $project_permission_master['companyID'] = $companyID;
                        $project_permission_master['createdUserGroup'] = $this->common_data['user_group'];
                        $project_permission_master['createdPCID'] = $this->common_data['current_pc'];
                        $project_permission_master['createdUserID'] = $this->common_data['current_userID'];
                        $project_permission_master['createdUserName'] = $this->common_data['current_user'];
                        $project_permission_master['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_documentpermission', $project_permission_master);
                    
                    
                        //Add related document opportnity
                        $links = array();
                        $links['documentID'] = 9;
                        $links['MasterAutoID'] = $projectID;
                        $links['relatedDocumentID'] = 4;
                        $links['relatedDocumentMasterID'] = $opportunityMasterID;
                        $links['searchValue'] = trim($this->input->post('opportunityname') ?? '');
                        $links['companyID'] = $companyID;
                        $links['createdUserGroup'] = $this->common_data['user_group'];
                        $links['createdPCID'] = $this->common_data['current_pc'];
                        $links['createdUserID'] = $this->common_data['current_userID'];
                        $links['createdUserName'] = $this->common_data['current_user'];
                        $links['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $links);
                    
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Opportunity Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Opportunity Updated Successfully.', $opportunityMasterID);
            }
        } else {
            $data['companyID'] = $companyID;
            $this->load->library('sequence');
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-OPP');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_opportunity WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-OPP';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-OPP' . str_pad($data['serialNo'], 6,
           '0', STR_PAD_LEFT));*/
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_opportunity', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                    foreach ($relatedAutoIDs as $key => $itemAutoID) {
                        $data_link['documentID'] = 4;
                        $data_link['MasterAutoID'] = $last_id;
                        $data_link['relatedDocumentID'] = $relatedTo[$key];
                        $data_link['relatedDocumentMasterID'] = $itemAutoID;
                        $data_link['searchValue'] = $relatedToSearch[$key];
                        $data_link['originFrom'] = $linkedFromOrigin[$key];
                        $data_link['companyID'] = $companyID;
                        $data_link['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link['createdPCID'] = $this->common_data['current_pc'];
                        $data_link['createdUserID'] = $this->common_data['current_userID'];
                        $data_link['createdUserName'] = $this->common_data['current_user'];
                        $data_link['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link);
                    }
                }
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 4;
                $permission_master['documentAutoID'] = $last_id;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 4;
                                $permission_detail['documentAutoID'] = $last_id;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
                if ($iscloseconvertoproject['statusType'] == 2) {
                    $this->load->library('sequence');
                    $project_data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-PRO');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_project WHERE companyID = $companyID")->row_array();
                    $project_data['serialNo'] = $serial['serialNumber'];
                    $project_data['documentID'] = 'CRM-PRO';
                    /* $project_data['documentSystemCode'] = ($company_code . '/' . 'CRM-PRO' . str_pad($data['serialNo'], 6,
                   '0', STR_PAD_LEFT));*/
                    $masterDetail = $this->get_opportunityMaster($last_id);
                    $project_data['projectName'] = $masterDetail['opportunityName'];
                    $project_data['description'] = $masterDetail['description'];
                    $project_data['categoryID'] = $masterDetail['categoryID'];
                    $project_data['opportunityID'] = $last_id;
                    $project_data['probabilityofwinning'] = $masterDetail['probabilityofwinning'];
                    $project_data['forcastCloseDate'] = $masterDetail['forcastCloseDate'];
                    $project_data['responsibleEmpID'] = $masterDetail['responsibleEmpID'];
                    $project_data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
                    $project_data['transactionAmount'] = $masterDetail['transactionAmount'];
                    $project_data['transactionCurrencyExchangeRate'] = $masterDetail['transactionCurrencyExchangeRate'];
                    $project_data['transactionDecimalPlaces'] = $masterDetail['transactionDecimalPlaces'];
                    $project_data['companyLocalCurrencyID'] = $masterDetail['companyLocalCurrencyID'];
                    $project_data['companyLocalAmount'] = $masterDetail['companyLocalAmount'];
                    $project_data['companyLocalCurrencyExchangeRate'] = $masterDetail['companyLocalCurrencyExchangeRate'];
                    $project_data['companyLocalDecimalPlaces'] = $masterDetail['companyLocalDecimalPlaces'];
                    $project_data['companyReportingCurrencyID'] = $masterDetail['companyReportingCurrencyID'];
                    $project_data['companyReportingAmount'] = $masterDetail['companyReportingAmount'];
                    $project_data['companyReportingCurrencyExchangeRate'] = $masterDetail['companyReportingCurrencyExchangeRate'];
                    $project_data['companyReportingDecimalPlaces'] = $masterDetail['companyReportingDecimalPlaces'];
                    $project_data['valueType'] = $masterDetail['valueType'];
                    $project_data['valueAmount'] = $masterDetail['valueAmount'];
                    $project_data['statusID'] = $masterDetail['statusID'];
                    $project_data['closeStatus'] = $masterDetail['closeStatus'];
                    $project_data['reason'] = $masterDetail['reason'];
                    $project_data['pipelineID'] = 0;
                    $project_data['pipelineStageID'] = 0;
                    $project_data['leadID'] = $masterDetail['leadID'];
                    $project_data['projectStatus'] = 0;
                    $project_data['projectStartDate'] = $masterDetail['forcastCloseDate'];
                    $project_data['companyID'] = $companyID;
                    $project_data['createdUserGroup'] = $this->common_data['user_group'];
                    $project_data['createdPCID'] = $this->common_data['current_pc'];
                    $project_data['createdUserID'] = $this->common_data['current_userID'];
                    $project_data['createdUserName'] = $this->common_data['current_user'];
                    $project_data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_project', $project_data);
                    $projectID = $this->db->insert_id();
                    if ($projectID) {
                        $project_permission_master['documentID'] = 9;
                        $project_permission_master['documentAutoID'] = $projectID;
                        $project_permission_master['permissionID'] = 1;
                        $project_permission_master['companyID'] = $companyID;
                        $project_permission_master['createdUserGroup'] = $this->common_data['user_group'];
                        $project_permission_master['createdPCID'] = $this->common_data['current_pc'];
                        $project_permission_master['createdUserID'] = $this->common_data['current_userID'];
                        $project_permission_master['createdUserName'] = $this->common_data['current_user'];
                        $project_permission_master['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_documentpermission', $project_permission_master);
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Opportunity Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Opportunity Saved Successfully.');

            }
        }
    }

    function load_opportunity_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*,DATE_FORMAT(forcastCloseDate,\'' . $convertFormat . '\') AS forcastCloseDate');
        $this->db->where('opportunityID', $this->input->post('opportunityID'));
        $this->db->from('srp_erp_crm_opportunity');
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('documentID', 4);
        $this->db->where('MasterAutoID', $this->input->post('opportunityID'));
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 4);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('opportunityID'));
        $data['permission'] = $this->db->get()->result_array();

        return $data;

    }

    function load_pipelineSubStage()
    {
        $this->db->select('pipeLineDetailID,stageName');
        $this->db->where('pipeLineID', $this->input->post('subid'));
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->from('srp_erp_crm_pipelinedetails');
        return $subcat = $this->db->get()->result_array();
    }

    function add_opportunity_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $opportunityID = trim($this->input->post('opportunityID') ?? '');
        $paath = trim($this->input->post('paath') ?? '');

        $message = "Opportunity";

        $data['contactID'] = $opportunityID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 4;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_crm_contactnotes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', '' . $message . ' Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', '' . $message . '  Note Saved Successfully.');

        }
    }


    function delete_opportunity_master()
    {
        $this->db->where('opportunityID', $this->input->post('opportunityID'));
        $results = $this->db->delete('srp_erp_crm_opportunity');
        $this->session->set_flashdata('s', 'Opportunity Deleted Successfully');
        return true;
    }

    function opportunity_update_status()
    {

        $opportunityMasterID = trim($this->input->post('opportunityID') ?? '');
        $statusid = trim($this->input->post('statusID') ?? '');
        $companyid = current_companyID();
        $opportunitystatus = $this->db->query("SELECT * FROM `srp_erp_crm_status` where companyID = '{$companyid}' AND documentID = 4 AND statusID = '{$statusid}'")->row_array();

        $data['statusID'] = trim($this->input->post('statusID') ?? '');
        $data['reason'] = trim($this->input->post('otherreson') ?? '');
        $data['closeCriteriaID'] = trim($this->input->post('reason') ?? '');
        if ($opportunitystatus['statusType'] == 1) {
            $data['closeStatus'] = 1;

        }else if ($opportunitystatus['statusType'] == 3) {
        $data['closeStatus'] = 3;
        }
        else if ($opportunitystatus['statusType'] == 2) {
            $data['closeStatus'] = $opportunitystatus['statusType'];
            $masterDetail = $this->get_opportunityMaster($opportunityMasterID);
            $this->load->library('sequence');
            $project_data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-PRO');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_project WHERE companyID = $companyid")->row_array();
            $project_data['serialNo'] = $serial['serialNumber'];
            $project_data['documentID'] = 'CRM-PRO';
            /* $project_data['documentSystemCode'] = ($company_code . '/' . 'CRM-PRO' . str_pad($data['serialNo'], 6,
          '0', STR_PAD_LEFT));*/
            $project_data['closeStatus'] = 2;

            $project_data['projectName'] = $masterDetail['opportunityName'];
            $project_data['description'] = $masterDetail['description'];
            $project_data['categoryID'] = $masterDetail['categoryID'];
            $project_data['opportunityID'] = $opportunityMasterID;
            $project_data['probabilityofwinning'] = $masterDetail['probabilityofwinning'];
            $project_data['forcastCloseDate'] = $masterDetail['forcastCloseDate'];
            $project_data['responsibleEmpID'] = $masterDetail['responsibleEmpID'];
            $project_data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
            $project_data['transactionAmount'] = $masterDetail['transactionAmount'];
            $project_data['transactionCurrencyExchangeRate'] = $masterDetail['transactionCurrencyExchangeRate'];
            $project_data['transactionDecimalPlaces'] = $masterDetail['transactionDecimalPlaces'];
            $project_data['companyLocalCurrencyID'] = $masterDetail['companyLocalCurrencyID'];
            $project_data['companyLocalAmount'] = $masterDetail['companyLocalAmount'];
            $project_data['companyLocalCurrencyExchangeRate'] = $masterDetail['companyLocalCurrencyExchangeRate'];
            $project_data['companyLocalDecimalPlaces'] = $masterDetail['companyLocalDecimalPlaces'];
            $project_data['companyReportingCurrencyID'] = $masterDetail['companyReportingCurrencyID'];
            $project_data['companyReportingAmount'] = $masterDetail['companyReportingAmount'];
            $project_data['companyReportingCurrencyExchangeRate'] = $masterDetail['companyReportingCurrencyExchangeRate'];
            $project_data['companyReportingDecimalPlaces'] = $masterDetail['companyReportingDecimalPlaces'];
            $project_data['valueType'] = $masterDetail['valueType'];
            $project_data['valueAmount'] = $masterDetail['valueAmount'];
            $project_data['statusID'] = $masterDetail['statusID'];

            $project_data['reason'] = $masterDetail['reason'];
            $project_data['pipelineID'] = 0;
            $project_data['pipelineStageID'] = 0;
            $project_data['leadID'] = $masterDetail['leadID'];
            $project_data['projectStatus'] = 0;
            $project_data['projectStartDate'] = $masterDetail['forcastCloseDate'];
            $project_data['companyID'] = $companyid;
            $project_data['createdUserGroup'] = $this->common_data['user_group'];
            $project_data['createdPCID'] = $this->common_data['current_pc'];
            $project_data['createdUserID'] = $this->common_data['current_userID'];
            $project_data['createdUserName'] = $this->common_data['current_user'];
            $project_data['createdDateTime'] = $this->common_data['current_date'];

            $this->db->insert('srp_erp_crm_project', $project_data);
            $projectID = $this->db->insert_id();
            if ($projectID) {
                $project_permission_master['documentID'] = 9;
                $project_permission_master['documentAutoID'] = $projectID;
                $project_permission_master['permissionID'] = 1;
                $project_permission_master['companyID'] = $companyid;
                $project_permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $project_permission_master['createdPCID'] = $this->common_data['current_pc'];
                $project_permission_master['createdUserID'] = $this->common_data['current_userID'];
                $project_permission_master['createdUserName'] = $this->common_data['current_user'];
                $project_permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $project_permission_master);
            }


        }


        $this->db->where('opportunityID', $opportunityMasterID);
        $this->db->update('srp_erp_crm_opportunity', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Opportunity Status Update Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Opportunity Status Updated Successfully.');
        }
    }

    function convert_leadToOpportunity()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $leadID = trim($this->input->post('leadID') ?? '');

        $leadMasterDetail = $this->db->query("SELECT * FROM srp_erp_crm_leadmaster where leadID = '{$leadID}'")->row_array();

        $organizationDetail = $this->db->query("SELECT Name FROM srp_erp_crm_organizations where organizationID = '{$leadMasterDetail['linkedorganizationID']}'")->row_array();

        $leadProductDetail = $this->db->query("SELECT transactionCurrencyID,SUM(price) as productTotal FROM srp_erp_crm_leadproducts where leadID = '{$leadID}'")->row_array();

        if ($leadMasterDetail['contactID'] == 0) {


            $this->load->library('sequence');
            $data_contact['documentSystemCode'] = $this->sequence->sequence_generator('CRM-CONT');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_contactmaster WHERE companyID = $companyID")->row_array();
            $data_contact['serialNo'] = $serial['serialNumber'];
            $data_contact['documentID'] = 'CRM-CONT';

            /* $data_contact['documentSystemCode'] = ($company_code . '/' . 'CRM-CONT' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/

            $data_contact['prefix'] = $leadMasterDetail['prefix'];
            $data_contact['firstName'] = $leadMasterDetail['firstName'];
            $data_contact['lastName'] = $leadMasterDetail['lastName'];
            $data_contact['occupation'] = $leadMasterDetail['title'];
            //$data_contact['department'] = $leadMasterDetail['department'];
            $data_contact['organization'] = $leadMasterDetail['organization'];
            $data_contact['email'] = $leadMasterDetail['email'];
            $data_contact['phoneMobile'] = $leadMasterDetail['phoneMobile'];
            $data_contact['phoneHome'] = $leadMasterDetail['phoneHome'];
            $data_contact['fax'] = $leadMasterDetail['fax'];
            $data_contact['postalCode'] = $leadMasterDetail['postalCode'];
            $data_contact['city'] = $leadMasterDetail['city'];
            $data_contact['state'] = $leadMasterDetail['state'];
            $data_contact['countryID'] = $leadMasterDetail['countryID'];
            $data_contact['address'] = $leadMasterDetail['address'];
            $data_contact['leadID'] = $leadID;
            $data_contact['companyID'] = $companyID;
            $data_contact['createdUserGroup'] = $this->common_data['user_group'];
            $data_contact['createdPCID'] = $this->common_data['current_pc'];
            $data_contact['createdUserID'] = $this->common_data['current_userID'];
            $data_contact['createdUserName'] = $this->common_data['current_user'];
            $data_contact['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_contactmaster', $data_contact);
            $contactID_last = $this->db->insert_id();

            $contactID_permission_update_master['documentID'] = 6;
            $contactID_permission_update_master['documentAutoID'] = $contactID_last;
            $contactID_permission_update_master['permissionID'] = 1;
            $contactID_permission_update_master['companyID'] = $companyID;
            $contactID_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
            $contactID_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
            $contactID_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
            $contactID_permission_update_master['createdUserName'] = $this->common_data['current_user'];
            $contactID_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_documentpermission', $contactID_permission_update_master);

        } else {
            $contactID_last = $leadMasterDetail['contactID'];
        }

        if ($leadMasterDetail['linkedorganizationID'] == 0) {
            $this->load->library('sequence');
            $data_organization['documentSystemCode'] = $this->sequence->sequence_generator('CRM-ORG');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_organizations WHERE companyID = $companyID")->row_array();
            $data_organization['serialNo'] = $serial['serialNumber'];
            $data_organization['documentID'] = 'CRM-ORG';
            /* $data_organization['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/
            $data_organization['Name'] = $leadMasterDetail['organization'];
            $data_organization['industry'] = $leadMasterDetail['industry'];
            $data_organization['numberofEmployees'] = $leadMasterDetail['numberofEmployees'];
            $data_organization['email'] = $leadMasterDetail['email'];
            $data_organization['telephoneNo'] = $leadMasterDetail['phoneHome'];
            $data_organization['fax'] = $leadMasterDetail['fax'];
            $data_organization['website'] = $leadMasterDetail['website'];
            $data_organization['companyID'] = $companyID;
            $data_organization['createdUserGroup'] = $this->common_data['user_group'];
            $data_organization['createdPCID'] = $this->common_data['current_pc'];
            $data_organization['createdUserID'] = $this->common_data['current_userID'];
            $data_organization['createdUserName'] = $this->common_data['current_user'];
            $data_organization['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_organizations', $data_organization);
            $organizationID_last = $this->db->insert_id();

            $organization_permission_update_master['documentID'] = 8;
            $organization_permission_update_master['documentAutoID'] = $organizationID_last;
            $organization_permission_update_master['permissionID'] = 1;
            $organization_permission_update_master['companyID'] = $companyID;
            $organization_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
            $organization_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
            $organization_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
            $organization_permission_update_master['createdUserName'] = $this->common_data['current_user'];
            $organization_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_documentpermission', $organization_permission_update_master);
        }
        $this->load->library('sequence');
        $data_opportunity['documentSystemCode'] = $this->sequence->sequence_generator('CRM-OPP');
        $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_opportunity WHERE companyID = $companyID")->row_array();
        $data_opportunity['serialNo'] = $serial['serialNumber'];
        $data_opportunity['documentID'] = 'CRM-OPP';
        /* $data_opportunity['documentSystemCode'] = ($company_code . '/' . 'CRM-OPP' . str_pad($data['serialNo'], 6,
       '0', STR_PAD_LEFT));*/

        $data_opportunity['opportunityname'] = "New Opportunity - {$leadMasterDetail['firstName']}";
        $data_opportunity['responsibleEmpID'] = $leadMasterDetail['responsiblePersonEmpID'];
        $data_opportunity['leadID'] = $leadID;
        $data_opportunity['transactionAmount'] = $leadProductDetail['productTotal'];
        $data_opportunity['transactionCurrencyID'] = $leadProductDetail['transactionCurrencyID'];
        $data_opportunity['transactionCurrencyExchangeRate'] = 1;
        $data_opportunity['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data_opportunity['transactionCurrencyID']);
        $data_opportunity['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyLocalCurrencyID']);
        $data_opportunity['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
        $data_opportunity['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data_opportunity['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyReportingCurrencyID']);
        $data_opportunity['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
        $data_opportunity['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data_opportunity['companyID'] = $companyID;
        $data_opportunity['createdUserGroup'] = $this->common_data['user_group'];
        $data_opportunity['createdPCID'] = $this->common_data['current_pc'];
        $data_opportunity['createdUserID'] = $this->common_data['current_userID'];
        $data_opportunity['createdUserName'] = $this->common_data['current_user'];
        $data_opportunity['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_crm_opportunity', $data_opportunity);
        $opportunity_last = $this->db->insert_id();
        if ($opportunity_last) {

            $permission_update_master['documentID'] = 4;
            $permission_update_master['documentAutoID'] = $opportunity_last;
            $permission_update_master['permissionID'] = 1;
            $permission_update_master['companyID'] = $companyID;
            $permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
            $permission_update_master['createdPCID'] = $this->common_data['current_pc'];
            $permission_update_master['createdUserID'] = $this->common_data['current_userID'];
            $permission_update_master['createdUserName'] = $this->common_data['current_user'];
            $permission_update_master['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_documentpermission', $permission_update_master);

            $data_link_contact['documentID'] = 4;
            $data_link_contact['MasterAutoID'] = $opportunity_last;
            $data_link_contact['relatedDocumentID'] = 6;
            $data_link_contact['relatedDocumentMasterID'] = $contactID_last;
            $data_link_contact['searchValue'] = $leadMasterDetail['firstName'] . " " . $leadMasterDetail['lastName'];
            $data_link_contact['originFrom'] = NULL;
            $data_link_contact['companyID'] = $companyID;
            $data_link_contact['createdUserGroup'] = $this->common_data['user_group'];
            $data_link_contact['createdPCID'] = $this->common_data['current_pc'];
            $data_link_contact['createdUserID'] = $this->common_data['current_userID'];
            $data_link_contact['createdUserName'] = $this->common_data['current_user'];
            $data_link_contact['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_link', $data_link_contact);

            if ($leadMasterDetail['linkedorganizationID'] == 0) {

                $data_link_organization['documentID'] = 4;
                $data_link_organization['MasterAutoID'] = $opportunity_last;
                $data_link_organization['relatedDocumentID'] = 8;
                $data_link_organization['relatedDocumentMasterID'] = $organizationID_last;
                $data_link_organization['searchValue'] = $leadMasterDetail['organization'];
                $data_link_organization['originFrom'] = NULL;
                $data_link_organization['companyID'] = $companyID;
                $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_link', $data_link_organization);

                $data_link_organization_toContact['documentID'] = 6;
                $data_link_organization_toContact['MasterAutoID'] = $contactID_last;
                $data_link_organization_toContact['relatedDocumentID'] = 8;
                $data_link_organization_toContact['relatedDocumentMasterID'] = $organizationID_last;
                $data_link_organization_toContact['searchValue'] = $leadMasterDetail['organization'];
                $data_link_organization_toContact['originFrom'] = NULL;
                $data_link_organization_toContact['companyID'] = $companyID;
                $data_link_organization_toContact['createdUserGroup'] = $this->common_data['user_group'];
                $data_link_organization_toContact['createdPCID'] = $this->common_data['current_pc'];
                $data_link_organization_toContact['createdUserID'] = $this->common_data['current_userID'];
                $data_link_organization_toContact['createdUserName'] = $this->common_data['current_user'];
                $data_link_organization_toContact['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_link', $data_link_organization_toContact);

            } else {
                $data_link_organization['documentID'] = 4;
                $data_link_organization['MasterAutoID'] = $opportunity_last;
                $data_link_organization['relatedDocumentID'] = 8;
                $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                $data_link_organization['searchValue'] = $organizationDetail['Name'];
                $data_link_organization['originFrom'] = NULL;
                $data_link_organization['companyID'] = $companyID;
                $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_link', $data_link_organization);

                $data_link_organization['documentID'] = 6;
                $data_link_organization['MasterAutoID'] = $contactID_last;
                $data_link_organization['relatedDocumentID'] = 8;
                $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                $data_link_organization['searchValue'] = $organizationDetail['Name'];
                $data_link_organization['originFrom'] = NULL;
                $data_link_organization['companyID'] = $companyID;
                $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_link', $data_link_organization);
            }

            $update_lead['isClosed'] = 2;
            $update_lead['modifiedPCID'] = $this->common_data['current_pc'];
            $update_lead['modifiedUserID'] = $this->common_data['current_userID'];
            $update_lead['modifiedUserName'] = $this->common_data['current_user'];
            $update_lead['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('leadID', $leadID);
            $this->db->update('srp_erp_crm_leadmaster', $update_lead);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Lead Converted to Opporunity Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Lead Converted to Opporunity Successfully.');

        }
    }

    function dashboardTotalDocuments_Count()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $currentuserID = current_userID();
        $isGroupAdmin = crm_isGroupAdmin();
        $issuperadmin = crm_isSuperAdmin();
        $masterEmployee = $this->input->post('employeeID');
        $groupID = $this->input->post('groupID');
        $permissiontype = $this->input->post('permissiontype');
        $where_count2 = " ";
        $where_count1 = " ";
        $where_count3 = " ";
        $where_count4 = " ";
        $where_organization3 = " ";
        $where_organization1 = " ";
        $where_organization2 = " ";
        $where_organization4 = " ";
        $where_lead2 = " ";
        $where_lead1 = " ";
        $where_lead3 = " ";
        $where_lead4 = " ";
        $where_opportunity2 = " ";
        $where_opportunity1 = " ";
        $where_opportunity3 = " ";
        $where_opportunity4 = " ";
        $where_task1 = " ";
        $where_task2 = " ";
        $where_task3 = " ";
        $where_task4 = " ";
        $where_project1 = " ";
        $where_project2 = " ";
        $where_project3 = " ";
        $where_project4 = " ";
        $contact_count = " ";
        $where_all_task = " ";
        $organizartion_count = " ";
        $where_project_all = "";
        $isAdmingroup = $this->db->query("select empID from srp_erp_crm_usergroupdetails where empID = $currentuserID AND companyID = $companyID AND adminYN = 1")->row_array();

        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $employeeID = join(",", $masterEmployee);
        }
        $filterAssigneesID = '';
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $filterAssigneesID = " AND srp_erp_crm_assignees.empID IN ($employeeID)";
        }
        $filteruserresponsibleID = '';
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $filteruserresponsibleID = " AND srp_erp_crm_leadmaster.responsiblePersonEmpID IN ($employeeID)";
        }
        $filteropportuniID = '';
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $filteropportuniID = " AND srp_erp_crm_opportunity.responsibleEmpID IN ($employeeID)";
        }
        $filterprojectId = '';
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $filterprojectId = " AND srp_erp_crm_project.responsibleEmpID IN ($employeeID)";
        }
        /* if ( empty($masterEmployee)) {
             $formattedGroup = join(',', $groupID);
             $groupEmployees = $this->db->query("SELECT empID FROM srp_erp_crm_usergroupdetails gd where gd.groupMasterID IN ($formattedGroup)")->result_array();
             $employeearray  = [];
             if(!empty($groupEmployees)){
                 foreach($groupEmployees as $gp){
                     array_push($employeearray,$gp['empID']);
                 }
                 $employeeID = join(',', $employeearray);
             }
         }*/
        if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {

            if (isset($masterEmployee) && empty($masterEmployee)) {
                $contact_count = $this->db->query("SELECT COUNT(*) as contactCount FROM srp_erp_crm_contactmaster where companyID = '{$companyID}'")->row_array();

                $organization_count = $this->db->query("SELECT COUNT(*) as organizationCount FROM srp_erp_crm_organizations where companyID = '{$companyID}'")->row_array();

                $lead_count = $this->db->query("SELECT COUNT(*) as leadCount FROM srp_erp_crm_leadmaster where companyID = '{$companyID}'")->row_array();

                $opportunity_count = $this->db->query("SELECT COUNT(*) as opportunityCount FROM srp_erp_crm_opportunity where companyID = '{$companyID}'")->row_array();

                $task_count = $this->db->query("SELECT COUNT(*) as taskCount FROM srp_erp_crm_task where companyID = '{$companyID}'")->row_array();

                $project_count = $this->db->query("SELECT COUNT(*) as projectCount FROM srp_erp_crm_project where companyID = '{$companyID}'")->row_array();

                $data['contact'] = $contact_count['contactCount'];
                $data['organization'] = $organization_count['organizationCount'];
                $data['lead'] = $lead_count['leadCount'];
                $data['opportunity'] = $opportunity_count['opportunityCount'];
                $data['task'] = $task_count['taskCount'];
                $data['project'] = $project_count['projectCount'];

            } else {

                $where_count1 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                $where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                /* If (!empty($employeeID)) {
                     $where_count1 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                     $where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}'  ";
                     $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}' ";
                     $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}' ";


                 }*/
                $contact_count = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count1 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count2 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_count3 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_count4 GROUP BY contactID ")->result_array();

                $where_organization1 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                /*
                                if (!empty(!empty($employeeID))) {
                                    $where_organization1 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                                    $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                                    $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                                    $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";
                                }*/


                $organization_count = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization1 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization2 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_organization3 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_organization4 GROUP BY organizationID")->result_array();

                $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";
                $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2  AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";
                $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";
                $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                if (!empty($employeeID)) {

                    $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID";

                    $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID";


                    $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID";


                    $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID";
                }

                $lead_count = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();

                $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'";
                $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2  AND srp_erp_crm_opportunity.companyID = '{$companyID}'";
                $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_opportunity.companyID = '{$companyID}'";
                $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_opportunity.companyID = '{$companyID}'";
                if (!empty($employeeID)) {
                    $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID";

                    $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID";

                    $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID";

                    $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID";
                }


                $opportunity_count = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();

                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}'";
                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_task.companyID = '{$companyID}'";
                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_task.companyID = '{$companyID}'";
                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_task.companyID = '{$companyID}'";
                if (!empty($employeeID)) {
                    $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                }


                $task_count = $this->db->query("SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID $where_task1 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID $where_task2 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID $where_task3 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID $where_task4 GROUP BY taskID")->result_array();

                $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' ";
                $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_project.companyID = '{$companyID}' ";
                $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_project.companyID = '{$companyID}' ";
                $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_project.companyID = '{$companyID}' ";
                if (!empty($employeeID)) {
                    $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId ";
                    $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId ";

                    $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId ";

                    $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId ";
                }


                $project_count = $this->db->query("SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project1 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project2 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_project3 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_project4 GROUP BY projectID ")->result_array();

                $data['contact'] = sizeof($contact_count);
                $data['organization'] = sizeof($organization_count);
                $data['lead'] = sizeof($lead_count);
                $data['opportunity'] = sizeof($opportunity_count);
                $data['task'] = sizeof($task_count);
                $data['project'] = sizeof($project_count);

            }

        } else {

            // get the count of Contact according to the logged user


            /*   if (isset($employeeID) && !empty($employeeID)) {
                   $where_count1 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";

                   $where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";

                   $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";

                   $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
               } else {
                   $where_count1 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                   $where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                   $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
                   $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}'";
               }*/
            //$contact_count = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count1 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count2 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_count3 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_count4 GROUP BY contactID ")->result_array();
            $where_count1 = "WHERE srp_erp_crm_contactmaster.companyID = '{$companyID}'";
            $contact_count = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count1 GROUP BY contactID")->result_array();
            //echo $this->db->last_query();

            /* if (isset($employeeID) && !empty($employeeID)) {
                 $where_organization1 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}'";
             } else {

                 $where_organization1 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}'";

                 $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}'";

             }*/
            //$organization_count = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization1 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization2 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_organization3 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_organization4 GROUP BY organizationID")->result_array();
            $where_organization1 = "WHERE srp_erp_crm_organizations.companyID = '{$companyID}'";
            $organization_count = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization1 GROUP BY organizationID")->result_array();

            if (isset($employeeID) && !empty($employeeID)) {
                $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

            } else if ($permissiontype == 1) {
                $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND (srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND (srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";

                $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID =  " . $currentuserID . ") AND srp_erp_crm_leadmaster.companyID = '{$companyID}'";
            } else if ($permissiontype == 2) {
                $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

                $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

                $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";

                $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " ";
            }
            $lead_count = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();
            // echo '<pre>'.$this->db->last_query().'</pre>';
            if (isset($employeeID) && !empty($employeeID)) {

                $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

            } else if ($permissiontype == 1) {

                $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'";

                $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'";
            } else if ($permissiontype == 2) {
                $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

                $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

                $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

                $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";
            }
            $opportunity_count = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();

            //tasks
            if (isset($employeeID) && !empty($employeeID)) {
                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}'";

            } else if ($permissiontype == 1) {
                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_all_task = "UNION SELECT taskID FROM srp_erp_crm_task WHERE srp_erp_crm_task.companyID = $companyID AND srp_erp_crm_task.createdUserID = $currentuserID GROUP BY taskID";

            } else if ($permissiontype == 2) {
                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            }
            $task_count = $this->db->query("SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task1 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task2 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_task3 GROUP BY taskID UNION SELECT taskID FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_assignees on srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_task4 GROUP BY taskID $where_all_task")->result_array();
            if (isset($employeeID) && !empty($employeeID)) {
                $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' ";
            } else if ($permissiontype == 1) {
                $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}'";

                $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' ";

                $where_project_all = "UNION SELECT projectID FROM srp_erp_crm_project WHERE srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.createdUserID = $currentuserID GROUP BY projectID";

            } else if ($permissiontype == 2) {
                $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . " ";

                $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . " ";

                $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . " ";

                $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . " ";
            }
            $project_count = $this->db->query("SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project1 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project2 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_project3 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_project4 GROUP BY projectID $where_project_all")->result_array();
            //echo '<pre>'.$this->db->last_query().'</pre>';

            $data['contact'] = sizeof($contact_count);
            $data['organization'] = sizeof($organization_count);
            $data['lead'] = sizeof($lead_count);
            $data['opportunity'] = sizeof($opportunity_count);
            $data['task'] = sizeof($task_count);
            $data['project'] = sizeof($project_count);
        }

        return $data;
    }

    function reopen_opportunity_master()
    {
        $this->db->trans_start();
        $opportunityID = trim($this->input->post('opportunityID') ?? '');
        $data['closeStatus'] = 0;
        $data['statusID'] = 0;
        $data['closeCriteriaID'] = null;
        $data['reason'] = null;
        $this->db->where('opportunityID', $opportunityID);
        $this->db->update('srp_erp_crm_opportunity', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Opportunity Reopen Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Opportunity Reopen Successfully.');

        }
    }


    function load_project_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = trim($this->input->post('projectID') ?? '');
        $this->db->select('*, DATE_FORMAT(projectStartDate,\'' . $convertFormat . '\') AS projectStartDate,DATE_FORMAT(projectEndDate,\'' . $convertFormat . '\') AS projectEndDate,DATE_FORMAT(closedDate,\'' . $convertFormat . '\') AS closedDates,');
        $this->db->where('companyID', $companyID);
        $this->db->where('projectID', $projectID);
        $this->db->from('srp_erp_crm_project');
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('*');
        $this->db->where('documentID', 9);
        $this->db->where('MasterAutoID', $projectID);
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 9);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $projectID);
        $data['permission'] = $this->db->get()->result_array();

        return $data;

    }

    function add_project_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $projectID = trim($this->input->post('projectID') ?? '');

        $data['contactID'] = $projectID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 9;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_crm_contactnotes', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Project Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', 'Project  Note Saved Successfully.');

        }
    }

    function load_projectBase_period()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('salesTargetID,CONCAT(DATE_FORMAT(dateFrom,\'' . $convertFormat . '\')," | ", DATE_FORMAT(dateTo,\'' . $convertFormat . '\')) AS formattedDate');
        $this->db->where('projectID', $this->input->post('projectID'));
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_crm_salestarget');
        return $subcat = $this->db->get()->result_array();
    }

    function save_sales_targetAchieved_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];

        $salesTargetID = trim($this->input->post('salesTargetID') ?? '');
        $dateFrom = trim($this->input->post('dateFrom') ?? '');
        $dateTo = trim($this->input->post('dateTo') ?? '');
        $userID = trim($this->input->post('userID') ?? '');
        $noOfUnits = trim($this->input->post('noOfUnits') ?? '');

        $format_dateFrom = null;
        if (isset($dateFrom) && !empty($dateFrom)) {
            $format_dateFrom = input_format_date($dateFrom, $date_format_policy);
        }
        $format_dateTo = null;
        if (isset($dateTo) && !empty($dateTo)) {
            $format_dateTo = input_format_date($dateTo, $date_format_policy);
        }

        $existPeriod = $this->db->query("SELECT * FROM srp_erp_crm_salestarget WHERE companyID = $companyID AND userID = $userID  AND salesTargetID != $salesTargetID AND (('$format_dateFrom' BETWEEN dateFrom
AND dateTo ) OR ('$format_dateTo' BETWEEN dateFrom AND dateTo ) OR ((dateFrom > '$format_dateFrom') AND (dateTo < '$format_dateTo')))")->row_array();

        if ($existPeriod) {
            return array('e', 'Already a period added.');
        } else {

            $data['dateFrom'] = $format_dateFrom;
            $data['dateTo'] = $format_dateTo;
            $data['userID'] = $userID;
            $data['units'] = $noOfUnits;
            $data['targetValue'] = trim($this->input->post('targetValue') ?? '');
            $data['productID'] = trim($this->input->post('productID') ?? '');
            $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            if ($salesTargetID) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('salesTargetID', $salesTargetID);
                $update = $this->db->update('srp_erp_crm_salestarget', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Sales Target Achieved Amount Update Failed ' . $this->db->_error_message());

                } else {
                    $this->db->trans_commit();
                    return array('s', 'Sales Target Achieved Amount Updated Successfully.', $salesTargetID);
                }
            } else {
                $data['companyID'] = $companyID;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_salestarget', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Sales Target Achieved Amount Save Failed ' . $this->db->_error_message());
                } else {
                    $this->db->trans_commit();
                    return array('s', 'Sales Target Achieved Amount Saved Successfully.');

                }
            }
        }
    }

    function delete_salesTarget_Acheived()
    {
        $this->db->delete('srp_erp_crm_salestarget', array('salesTargetID' => trim($this->input->post('salesTargetID') ?? '')));
        return true;
    }

    function save_project_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];
        $projectStatus = trim($this->input->post('projectStatus') ?? '');
        $relatedAutoIDs = $this->input->post('relatedAutoID');
        $relatedTo = $this->input->post('relatedTo');
        $relatedToSearch = $this->input->post('related_search');
        $linkedFromOrigin = $this->input->post('linkedFromOrigin');
        $closedDt = $this->input->post('closedDate');
        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');
        $projectStartDate = trim($this->input->post('projectStartDate') ?? '');
        $isclose = $this->db->query("SELECT * FROM `srp_erp_crm_status` WHERE companyID = '$companyID' AND statusID = '{$projectStatus}' AND documentID = 9")->row_array();
        $startdateconverted = input_format_date($this->input->post('projectStartDate'), $date_format_policy);
        $closedate = trim($this->input->post('closedate') ?? '');
        $cancelDate = trim($this->input->post('cancelDate') ?? '');
        $reasonpro = trim($this->input->post('reasoncancel') ?? '');
        $closedateconvert = input_format_date($closedate, $date_format_policy);
        $canceldate = input_format_date($cancelDate, $date_format_policy);

        if (($closedateconvert < $startdateconverted) && ($isclose['statusType'] == 1)) {
            return array('e', 'close date Cannot be less than statdate');
            exit();
        }


        $format_projectStartDate = null;
        if (isset($projectStartDate) && !empty($projectStartDate)) {
            $format_projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        }

        $closedDates = null;
        if (isset($closedDt) && !empty($closedDt)) {
            $closedDates = input_format_date($closedDt, $date_format_policy);
        }

        $projectEndDate = trim($this->input->post('projectEndDate') ?? '');
        $format_projectEndDate = null;
        if (isset($projectEndDate) && !empty($projectEndDate)) {
            $format_projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        }

        $projectIDMasterID = trim($this->input->post('projectID') ?? '');

        $duration = 0;
        if (!empty($this->input->post('duration'))) {
            $duration = $this->input->post('duration');
        }
        $isclosed = 0;
        if ($isclose['statusType'] == 1) {
            $isclosed = 1;
            $data['reason'] = $reasonpro;
        }
        if($isclose['statusType'] == 3)
        { $isclosed = 3;
            $data['reason'] = $reasonpro;
        }

        $data['projectName'] = trim($this->input->post('opportunityname') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['statusID'] = trim($this->input->post('projectStatus') ?? '');
        $data['projectStatus'] = trim($this->input->post('projectStatus') ?? '');
        $data['categoryID'] = trim($this->input->post('categoryID') ?? '');
        $data['projectStartDate'] = $format_projectStartDate;
        $data['projectEndDate'] = $format_projectEndDate;
        $data['responsibleEmpID'] = trim($this->input->post('responsiblePersonEmpID') ?? '');
        $data['transactionAmount'] = trim($this->input->post('price') ?? '');
        $data['valueType'] = trim($this->input->post('valueType') ?? '');
        $data['valueAmount'] = $duration;
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionCurrencyExchangeRate'] = 1;
        $data['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        $data['pipelineID'] = trim($this->input->post('pipelineID') ?? '');
        $data['pipelineStageID'] = trim($this->input->post('pipelineStageID') ?? '');

        if ($isclose['statusType'] == 3) {
            $data['closedSystemDate'] = $this->common_data['current_date'];
            $data['closedDate'] = $canceldate;

        }


        if ($projectIDMasterID) {
            $this->db->select("closedDate");
            $this->db->where('projectID', $projectIDMasterID);
            $this->db->from('srp_erp_crm_project');
            $closedDate = $this->db->get()->row_array();

            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_crm_link', array('documentID' => 9, 'MasterAutoID' => $projectIDMasterID));
                foreach ($relatedAutoIDs as $key => $itemAutoID) {
                    $data_link['documentID'] = 9;
                    $data_link['MasterAutoID'] = $projectIDMasterID;
                    $data_link['relatedDocumentID'] = ($relatedTo[$key]=='0')?'':$relatedTo[$key];
                    $data_link['relatedDocumentMasterID'] = $itemAutoID;
                    $data_link['searchValue'] = $relatedToSearch[$key];
                    $data_link['originFrom'] = $linkedFromOrigin[$key];
                    $data_link['companyID'] = $companyID;
                    $data_link['createdUserGroup'] = $this->common_data['user_group'];
                    $data_link['createdPCID'] = $this->common_data['current_pc'];
                    $data_link['createdUserID'] = $this->common_data['current_userID'];
                    $data_link['createdUserName'] = $this->common_data['current_user'];
                    $data_link['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_link', $data_link);
                }
            }
            $data['isClosed'] = $isclosed;
            if ($isclosed == 1 && empty($closedDate['closedDate'])) {
                $data['closedSystemDate'] = $this->common_data['current_date'];
                $data['closedDate'] = $closedateconvert;
            }
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('projectID', $projectIDMasterID);
            $update = $this->db->update('srp_erp_crm_project', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 9, 'documentAutoID' => $projectIDMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 9, 'documentAutoID' => $projectIDMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 9;
                $permission_master['documentAutoID'] = $projectIDMasterID;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 9;
                                $permission_detail['documentAutoID'] = $projectIDMasterID;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Project Updated Successfully.', $projectIDMasterID);
            }
        } else {
            $data['isClosed'] = $isclosed;
            if ($isclosed == 1) {
                $data['closedSystemDate'] = $this->common_data['current_date'];
                $data['closedDate'] = $closedateconvert;

            } else {
                $data['closedSystemDate'] = null;
                $data['closedDate'] = null;
            }
            $this->load->library('sequence');
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-PRO');
            $data['closeStatus'] = 2;
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_project WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-PRO';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-PRO' . str_pad($data['serialNo'], 6,
           '0', STR_PAD_LEFT));*/

            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_project', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                    foreach ($relatedAutoIDs as $key => $itemAutoID) {
                        $data_link['documentID'] = 9;
                        $data_link['MasterAutoID'] = $last_id;
                        $data_link['relatedDocumentID'] = ($relatedTo[$key]=='0')?'':$relatedTo[$key];
                        $data_link['relatedDocumentMasterID'] = $itemAutoID;
                        $data_link['searchValue'] = $relatedToSearch[$key];
                        $data_link['originFrom'] = $linkedFromOrigin[$key];
                        $data_link['companyID'] = $companyID;
                        $data_link['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link['createdPCID'] = $this->common_data['current_pc'];
                        $data_link['createdUserID'] = $this->common_data['current_userID'];
                        $data_link['createdUserName'] = $this->common_data['current_user'];
                        $data_link['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link);
                    }
                }
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 9;
                $permission_master['documentAutoID'] = $last_id;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 9;
                                $permission_detail['documentAutoID'] = $last_id;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Project Saved Successfully.');

            }
        }
    }

    function update_project_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];

        /*        $relatedAutoIDs = $this->input->post('relatedAutoID');
                $relatedTo = $this->input->post('relatedTo');
                $relatedToSearch = $this->input->post('related_search');
                $linkedFromOrigin = $this->input->post('linkedFromOrigin');*/

        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $projectStartDate = trim($this->input->post('projectStartDate') ?? '');
        $format_projectStartDate = null;
        if (isset($projectStartDate) && !empty($projectStartDate)) {
            $format_projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        }

        $projectEndDate = trim($this->input->post('projectEndDate') ?? '');
        $format_projectEndDate = null;
        if (isset($projectEndDate) && !empty($projectEndDate)) {
            $format_projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        }

        $projectIDMasterID = trim($this->input->post('projectID') ?? '');

        $data['description'] = trim($this->input->post('description') ?? '');
        $data['statusID'] = trim($this->input->post('statusID') ?? '');
        $data['projectStatus'] = trim($this->input->post('projectStatus') ?? '');
        $data['probabilityofwinning'] = trim($this->input->post('probabilityofwinning') ?? '');
        $data['projectStartDate'] = $format_projectStartDate;
        $data['projectEndDate'] = $format_projectEndDate;
        $data['responsibleEmpID'] = trim($this->input->post('responsiblePersonEmpID') ?? '');
        $data['pipelineID'] = trim($this->input->post('pipelineID') ?? '');
        $data['pipelineStageID'] = trim($this->input->post('pipelineStageID') ?? '');

        if ($projectIDMasterID) {
            /*            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                            $this->db->delete('srp_erp_crm_link', array('documentID' => 4, 'MasterAutoID' => $projectIDMasterID));
                            foreach ($relatedAutoIDs as $key => $itemAutoID) {
                                $data_link['documentID'] = 4;
                                $data_link['MasterAutoID'] = $projectIDMasterID;
                                $data_link['relatedDocumentID'] = $relatedTo[$key];
                                $data_link['relatedDocumentMasterID'] = $itemAutoID;
                                $data_link['searchValue'] = $relatedToSearch[$key];
                                $data_link['originFrom'] = $linkedFromOrigin[$key];
                                $data_link['companyID'] = $companyID;
                                $data_link['createdUserGroup'] = $this->common_data['user_group'];
                                $data_link['createdPCID'] = $this->common_data['current_pc'];
                                $data_link['createdUserID'] = $this->common_data['current_userID'];
                                $data_link['createdUserName'] = $this->common_data['current_user'];
                                $data_link['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_link', $data_link);
                            }
                        }*/
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('projectID', $projectIDMasterID);
            $update = $this->db->update('srp_erp_crm_project', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 4, 'documentAutoID' => $projectIDMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 4, 'documentAutoID' => $projectIDMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 4;
                $permission_master['documentAutoID'] = $projectIDMasterID;
                $permission_master['permissionID'] = $userPermission;
                $permission_master['companyID'] = $companyID;
                $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $permission_master['createdPCID'] = $this->common_data['current_pc'];
                $permission_master['createdUserID'] = $this->common_data['current_userID'];
                $permission_master['createdUserName'] = $this->common_data['current_user'];
                $permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                $permission_id = $this->db->insert_id();
                if ($userPermission == 4) {
                    if ($permission_id) {
                        if (isset($employees) && !empty($employees)) {
                            foreach ($employees as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 4;
                                $permission_detail['documentAutoID'] = $projectIDMasterID;
                                $permission_detail['empID'] = $val;
                                $permission_detail['companyID'] = $companyID;
                                $permission_detail['createdUserGroup'] = $this->common_data['user_group'];
                                $permission_detail['createdPCID'] = $this->common_data['current_pc'];
                                $permission_detail['createdUserID'] = $this->common_data['current_userID'];
                                $permission_detail['createdUserName'] = $this->common_data['current_user'];
                                $permission_detail['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_crm_documentpermissiondetails', $permission_detail);
                            }
                        }
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Project Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Project Updated Successfully.', $projectIDMasterID);
            }
        }
    }

    function load_edit_salesTarget_achieved()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select("salesTargetID,units,targetValue,transactionCurrencyID,DATE_FORMAT(dateFrom,'" . $convertFormat . "') AS dateFrom,DATE_FORMAT(dateTo,'" . $convertFormat . "') AS dateTo,userID");
        $this->db->where('salesTargetID', $this->input->post('salesTargetID'));
        $this->db->from('srp_erp_crm_salestarget');
        $data = $this->db->get()->row_array();
        return $data;
    }

    function load_lead_productsEdit()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('leadProductID', $this->input->post('leadProductID'));
        return $this->db->get('srp_erp_crm_leadproducts')->row_array();
    }

    function load_lead_productsDelete()
    {
        $this->db->delete('srp_erp_crm_leadproducts', array('leadProductID' => trim($this->input->post('leadProductID') ?? '')));
        return true;
    }

    function save_sales_target_multiple()
    {
        $employee = $this->input->post('userID');
        $targetValues = $this->input->post('targetValue');
        $dateFrom = $this->input->post('dateFrom');
        $dateTo = $this->input->post('dateTo');
        $transactionCurrencyID = $this->input->post('transactionCurrencyID');
        $no_of_units = $this->input->post('no_of_units');
        $productID = $this->input->post('arr_crm_productsID');

        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];

        foreach ($targetValues as $key => $value) {

            $format_dateFrom = null;
            if (isset($dateFrom[$key]) && !empty($dateFrom[$key])) {
                $format_dateFrom = input_format_date($dateFrom[$key], $date_format_policy);
            }
            $format_dateTo = null;
            if (isset($dateTo[$key]) && !empty($dateTo[$key])) {
                $format_dateTo = input_format_date($dateTo[$key], $date_format_policy);
            }

            $existPeriod = $this->db->query("SELECT salesTargetID FROM srp_erp_crm_salestarget WHERE companyID = $companyID AND userID = {$employee[$key]} AND (('$format_dateFrom' BETWEEN dateFrom
AND dateTo ) OR ('$format_dateTo' BETWEEN dateFrom AND dateTo ) OR ((dateFrom > '$format_dateFrom') AND (dateTo < '$format_dateTo')))")->row_array();
 
            $existProductID = $this->db->query("SELECT productID FROM srp_erp_crm_salestarget WHERE companyID = $companyID AND userID = {$employee[$key]} AND  productID = $productID AND (('$format_dateFrom' BETWEEN dateFrom
AND dateTo ) OR ('$format_dateTo' BETWEEN dateFrom AND dateTo ) OR ((dateFrom > '$format_dateFrom') AND (dateTo < '$format_dateTo')))")->row_array();
 

            $data['dateFrom'] = $format_dateFrom;
            $data['dateTo'] = $format_dateTo;
            $data['userID'] = $employee[$key];
            $data['targetValue'] = $value;
            $data['transactionCurrencyID'] = $transactionCurrencyID[$key];
            $data['transactionExchangeRate'] = 1;
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['units'] = $no_of_units;
            $data['productID'] = $productID;

            if ($existPeriod) {
                if($existProductID){
                    return array('e', 'Already Period Added for the product selected.');
                    exit();
                } else {
                    $this->db->insert('srp_erp_crm_salestarget', $data);
                   // exit();
                }              
            } else {
                $this->db->insert('srp_erp_crm_salestarget', $data);
            }

            
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Target Achieved Amount Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Target Achieved Amount Saved Successfully.');
        }

    }

    function save_salesTarget_achived_multiple()
    {
        $acheivedValues = $this->input->post('acheivedValue');
        $dateFrom = $this->input->post('dateFrom');
        $projectID = $this->input->post('projectID');
        $salesTargetID = trim($this->input->post('salesTargetID') ?? '');

        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];

        $masterDate = $this->db->query("SELECT transactionCurrencyID,transactionExchangeRate FROM srp_erp_crm_salestarget WHERE salesTargetID = $salesTargetID ")->row_array();

        foreach ($acheivedValues as $key => $value) {

            $format_dateFrom = null;
            if (isset($dateFrom[$key]) && !empty($dateFrom[$key])) {
                $format_dateFrom = input_format_date($dateFrom[$key], $date_format_policy);
            }

            $data['documentDate'] = $format_dateFrom;
            $data['userID'] = $this->common_data['current_userID'];
            $data['projectID'] = $projectID[$key];
            $data['salesTargetID'] = $salesTargetID;
            $data['acheivedValue'] = $value;
            $data['transactionCurrencyID'] = $masterDate['transactionCurrencyID'];
            $data['transactionExchangeRate'] = $masterDate['transactionExchangeRate'];
            $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
            $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
            $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
            $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
            $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
            $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
            $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_salestargetacheived', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sales Target Achieved Amount Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sales Target Achieved Amount Saved Successfully.');
        }

    }

    function delete_salesTarget_Acheived_profile()
    {
        $this->db->delete('srp_erp_crm_salestargetacheived', array('salesTargetAcheivedID' => trim($this->input->post('salesTargetAcheivedID') ?? '')));
        return true;
    }

    function load_edit_salesTarget_achieved_profile()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select("salesTargetAcheivedID,salesTargetID,acheivedValue,projectID,DATE_FORMAT(documentDate,'" . $convertFormat . "') AS documentDate");
        $this->db->where('salesTargetAcheivedID', $this->input->post('salesTargetAcheivedID'));
        $this->db->from('srp_erp_crm_salestargetacheived');
        $data = $this->db->get()->row_array();
        return $data;
    }

    function save_sales_targetAchieved_header_profile()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $salesTargetAcheivedID = trim($this->input->post('salesTargetAcheivedID') ?? '');
        $dateFrom = trim($this->input->post('dateFrom') ?? '');


        $format_dateFrom = null;
        if (isset($dateFrom) && !empty($dateFrom)) {
            $format_dateFrom = input_format_date($dateFrom, $date_format_policy);
        }

        $data['documentDate'] = $format_dateFrom;
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        $data['acheivedValue'] = trim($this->input->post('acheivedValue') ?? '');

        if ($salesTargetAcheivedID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('salesTargetAcheivedID', $salesTargetAcheivedID);
            $update = $this->db->update('srp_erp_crm_salestargetacheived', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sales Target Achieved Amount Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Sales Target Achieved Amount Updated Successfully.', $salesTargetAcheivedID);
            }
        }
    }

    function crm_opportunity()
    {

        $statusid = trim($this->input->post('statusid') ?? '');
        $companyid = current_companyID();

        if (!empty($statusid) || ($statusid != '')) {
            $issubexist = $this->db->query("SELECT statusid,statusType FROM `srp_erp_crm_status` where statusID = '{$statusid}' AND companyID ='{$companyid}' and documentID = 4")->row_array();
            if ($issubexist['statusType'] == 1) {
                $data['isexist'] = 1;
            } else if ($issubexist['statusType'] == 2) {
                $data['isexist'] = 2;
            } else if ($issubexist['statusType'] == 3) {
                $data['isexist'] = 3;
            }
            else {
                $data['isexist'] = 0;
            }

        }
        return $data;


    }

    function save_opportunitie_statusid()
    {
        $companyID = current_companyID();
        $status = $this->input->post('statusid');
        $opportunityID = $this->input->post('opportunityID');
        $iscloseconvertoproject = $this->db->query("SELECT * FROM `srp_erp_crm_status` WHERE companyID = '$companyID' AND statusID = '{$status}' AND documentID = 4")->row_array();

        $opportunity = $this->input->post('opportunityID');
        $statusid = $this->input->post('statusid');
        $companyid = current_companyID();
        $opportunitydet = $this->db->query("SELECT * FROM `srp_erp_crm_opportunity` WHERE companyID = '{$companyid}'  and opportunityID = '{$opportunity}'")->row_array();
        $statusidcrm = $this->db->query("SELECT statusType FROM `srp_erp_crm_status` where companyID = '{$companyid}' and documentID = 4 and statusID = '{$statusid}' ")->row_array();

        if((($statusidcrm['statusType']==2) || ($statusidcrm['statusType']==1) || ($statusidcrm['statusType']==3) || ($statusidcrm['statusType']==0)))
        {
            if(($opportunitydet['description']== '') || ($opportunitydet['description']== null))
            {
                echo json_encode(array('e', 'Description field is required '));
                exit();

            }
            if($statusidcrm['statusType']!=0)
            {
                if(($opportunitydet['reason']== '') || ($opportunitydet['reason']== null))
                {
                    echo json_encode(array('e', 'Criteria field is required '));
                    exit();


                }
            }


            if(($opportunitydet['categoryID']== '') || ($opportunitydet['categoryID']== null))
            {
                echo json_encode(array('e', 'Category field is required '));
                exit();

            }

        }else
        {
        }



        $statustype = 0;
        if ($iscloseconvertoproject['statusType'] != 0) {
            $statustype = $iscloseconvertoproject['statusType'];
        }


        $data['closeStatus'] = $statustype;
        $data['statusID'] = $status;
        $this->db->where('opportunityID', $opportunityID);
        $this->db->update('srp_erp_crm_opportunity', $data);
        if ($iscloseconvertoproject['statusType'] == 2) {

            $project_data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-PRO');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_project WHERE companyID = $companyID")->row_array();
            $project_data['serialNo'] = $serial['serialNumber'];
            $project_data['documentID'] = 'CRM-PRO';
            /* $project_data['documentSystemCode'] = ($company_code . '/' . 'CRM-PRO' . str_pad($data['serialNo'], 6,
          '0', STR_PAD_LEFT));*/
            $masterDetail = $this->get_opportunityMaster($opportunityID);
            $project_data['projectName'] = $masterDetail['opportunityName'];
            $project_data['description'] = $masterDetail['description'];
            $project_data['categoryID'] = $masterDetail['categoryID'];
            $project_data['opportunityID'] = $opportunityID;
            $project_data['probabilityofwinning'] = $masterDetail['probabilityofwinning'];
            $project_data['forcastCloseDate'] = $masterDetail['forcastCloseDate'];
            $project_data['responsibleEmpID'] = $masterDetail['responsibleEmpID'];
            $project_data['transactionCurrencyID'] = $masterDetail['transactionCurrencyID'];
            $project_data['transactionAmount'] = $masterDetail['transactionAmount'];
            $project_data['transactionCurrencyExchangeRate'] = $masterDetail['transactionCurrencyExchangeRate'];
            $project_data['transactionDecimalPlaces'] = $masterDetail['transactionDecimalPlaces'];
            $project_data['companyLocalCurrencyID'] = $masterDetail['companyLocalCurrencyID'];
            $project_data['companyLocalAmount'] = $masterDetail['companyLocalAmount'];
            $project_data['companyLocalCurrencyExchangeRate'] = $masterDetail['companyLocalCurrencyExchangeRate'];
            $project_data['companyLocalDecimalPlaces'] = $masterDetail['companyLocalDecimalPlaces'];
            $project_data['companyReportingCurrencyID'] = $masterDetail['companyReportingCurrencyID'];
            $project_data['companyReportingAmount'] = $masterDetail['companyReportingAmount'];
            $project_data['companyReportingCurrencyExchangeRate'] = $masterDetail['companyReportingCurrencyExchangeRate'];
            $project_data['companyReportingDecimalPlaces'] = $masterDetail['companyReportingDecimalPlaces'];
            $project_data['valueType'] = $masterDetail['valueType'];
            $project_data['valueAmount'] = $masterDetail['valueAmount'];
            $project_data['statusID'] = '';
            $project_data['closeStatus'] = $masterDetail['closeStatus'];
            $project_data['reason'] = $masterDetail['reason'];
            $project_data['pipelineID'] = 0;
            $project_data['pipelineStageID'] = 0;
            $project_data['leadID'] = $masterDetail['leadID'];
            $project_data['projectStatus'] = 0;
            $project_data['projectStartDate'] = $masterDetail['forcastCloseDate'];
            $project_data['companyID'] = $companyID;
            $project_data['createdUserGroup'] = $this->common_data['user_group'];
            $project_data['createdPCID'] = $this->common_data['current_pc'];
            $project_data['createdUserID'] = $this->common_data['current_userID'];
            $project_data['createdUserName'] = $this->common_data['current_user'];
            $project_data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_project', $project_data);
            $projectID = $this->db->insert_id();
            if ($projectID) {
                $project_permission_master['documentID'] = 9;
                $project_permission_master['documentAutoID'] = $projectID;
                $project_permission_master['permissionID'] = 1;
                $project_permission_master['companyID'] = $companyID;
                $project_permission_master['createdUserGroup'] = $this->common_data['user_group'];
                $project_permission_master['createdPCID'] = $this->common_data['current_pc'];
                $project_permission_master['createdUserID'] = $this->common_data['current_userID'];
                $project_permission_master['createdUserName'] = $this->common_data['current_user'];
                $project_permission_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_documentpermission', $project_permission_master);

              


            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Opportunity Status Update Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Opportunity Status Updated Successfully.');
        }
    }

    function load_opportunity_productsEdit()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('opportunityProductID', $this->input->post('opporProductID'));
        return $this->db->get('srp_erp_crm_opportunityproducts')->row_array();

    }

    function remove_opportunity_product(){

        $opportunity_product = trim($this->input->post('opportunityid') ?? '');
        
        try{

            $this->db->where('opportunityProductID', $opportunity_product);
            $results = $this->db->delete('srp_erp_crm_opportunityproducts');
            
            return array('s', 'Opportunity product removed Successfully');

        }catch(Exception $e){
            return array('e', 'Opportunity product removed Failed');
        }

    }

    function add_opportunity_product()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $opportunityid = trim($this->input->post('opportunityid') ?? '');
        $opportunityproductid = trim($this->input->post('opportunityproductid') ?? '');

        $data['opportunityID'] = $opportunityid;
        $data['productID'] = trim($this->input->post('productID') ?? '');
        $data['productDescription'] = trim($this->input->post('description') ?? '');
        $data['price'] = trim($this->input->post('price') ?? '');
        $data['subscriptionAmount'] = trim($this->input->post('subscriptionamount') ?? '');
        $data['ImplementationAmount'] = trim($this->input->post('implementationamount') ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = isset($default_currency['DecimalPlaces']) ? $default_currency['DecimalPlaces'] : 2;
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
        if ($opportunityproductid) {
            $this->db->where('opportunityProductID', $opportunityproductid);
            $this->db->update('srp_erp_crm_opportunityproducts', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Opportunity Product Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Opportunity Product Updated Successfully.');
            }

        } else {
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_opportunityproducts', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Opportunity Product Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Opportunity Product Added Successfully.');
            }
        }
    }

    function leads_covert_detail()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $leadid = $this->input->post('leadid');
        $query = $this->db->query("SELECT * FROM `srp_erp_crm_leadmaster` WHERE companyID = $companyID AND leadID = $leadid")->row_array();
        return $query;
    }

    function opportunity_products_value($oppotunityID){

        $this->db->select('SUM(srp_erp_crm_opportunityproducts.price) as amount');
        $this->db->where('srp_erp_crm_opportunityproducts.opportunityID',$oppotunityID);
        return $this->db->from('srp_erp_crm_opportunityproducts')->get()->row('amount');

    }

    function save_convert_opportunity_validation()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $companyID = $this->common_data['company_data']['company_id'];
        $leadstatusid = trim($this->input->post('statusID') ?? '');

        $expirydate = trim($this->input->post('expirydate') ?? '');
        $format_expirydate = null;
        if (isset($expirydate) && !empty($expirydate)) {
            $format_expirydate = input_format_date($expirydate, $date_format_policy);
        }

        $leadMasterID = trim($this->input->post('leadID') ?? '');
        $organization = $this->input->post('linkorganization');
        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $linkedorganizationID = 0;
        if (!empty($this->input->post('linkorganization'))) {
            $linkedorganizationID = $this->input->post('linkorganization');
        }

        $isclosed = 0;
        if ($leadstatusid) {
            $leadstatustype = $this->db->query("select * from srp_erp_crm_status where companyID = $companyID AND  leadStatusID = $leadstatusid ")->row_array();
        }

        if ($leadstatustype['statusType'] == 1) {
            $data['isClosed'] = 1;
        }
        $contactID = 0;
        if (!empty($this->input->post('contactID'))) {
            $contactID = $this->input->post('contactID');
        }



        $data['organization'] = trim($this->input->post('organization') ?? '');
        $data['linkedorganizationID'] = $linkedorganizationID;
        $data['statusID'] = trim($this->input->post('statusID') ?? '');
        $data['responsiblePersonEmpID'] = trim($this->input->post('responsiblePersonEmpID') ?? '');
        //$data['ratingID'] = trim($this->input->post('ratingID') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['phoneMobile'] = trim($this->input->post('phoneMobile') ?? '');

        if ($leadMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('leadID', $leadMasterID);
            $update = $this->db->update('srp_erp_crm_leadmaster', $data);

            if ($leadstatustype['statusType'] == 2) {
                $leadMasterDetail = $this->db->query("SELECT * FROM srp_erp_crm_leadmaster where leadID = '{$leadMasterID}'")->row_array();

                $organizationDetail = $this->db->query("SELECT Name FROM srp_erp_crm_organizations where organizationID = '{$leadMasterDetail['linkedorganizationID']}'")->row_array();
                $leadProductDetail = $this->db->query("SELECT transactionCurrencyID,SUM(price) as productTotal FROM srp_erp_crm_leadproducts where leadID = '{$leadMasterID}'")->row_array();
                if ($leadMasterDetail['linkedorganizationID'] == 0) {
                    $this->load->library('sequence');
                    $data_organization['documentSystemCode'] = $this->sequence->sequence_generator('CRM-ORG');
                    $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_organizations WHERE companyID = $companyID")->row_array();
                    $data_organization['serialNo'] = $serial['serialNumber'];
                    $data_organization['documentID'] = 'CRM-ORG';
                    /* $data_organization['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                             '0', STR_PAD_LEFT));*/
                    $data_organization['Name'] = $leadMasterDetail['organization'];
                    $data_organization['industry'] = $leadMasterDetail['industry'];
                    $data_organization['numberofEmployees'] = $leadMasterDetail['numberofEmployees'];
                    $data_organization['email'] = $leadMasterDetail['email'];
                    $data_organization['telephoneNo'] = $leadMasterDetail['phoneHome'];
                    $data_organization['fax'] = $leadMasterDetail['fax'];
                    $data_organization['website'] = $leadMasterDetail['website'];
                    $data_organization['companyID'] = $companyID;
                    $data_organization['createdUserGroup'] = $this->common_data['user_group'];
                    $data_organization['createdPCID'] = $this->common_data['current_pc'];
                    $data_organization['createdUserID'] = $this->common_data['current_userID'];
                    $data_organization['createdUserName'] = $this->common_data['current_user'];
                    $data_organization['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_organizations', $data_organization);
                    $organizationID_last = $this->db->insert_id();

                    $organization_permission_update_master['documentID'] = 8;
                    $organization_permission_update_master['documentAutoID'] = $organizationID_last;
                    $organization_permission_update_master['permissionID'] = 1;
                    $organization_permission_update_master['companyID'] = $companyID;
                    $organization_permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                    $organization_permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                    $organization_permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                    $organization_permission_update_master['createdUserName'] = $this->common_data['current_user'];
                    $organization_permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $organization_permission_update_master);
                }
                $this->load->library('sequence');
                $data_opportunity['documentSystemCode'] = $this->sequence->sequence_generator('CRM-OPP');
                $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_opportunity WHERE companyID = $companyID")->row_array();
                $data_opportunity['serialNo'] = $serial['serialNumber'];
                $data_opportunity['documentID'] = 'CRM-OPP';
                /* $data_opportunity['documentSystemCode'] = ($company_code . '/' . 'CRM-OPP' . str_pad($data['serialNo'], 6,
               '0', STR_PAD_LEFT));*/
                $data_opportunity['opportunityname'] = "New Opportunity";
                $data_opportunity['responsibleEmpID'] = $leadMasterDetail['responsiblePersonEmpID'];
                $data_opportunity['leadID'] = $leadMasterID;
                $data_opportunity['transactionAmount'] = $leadProductDetail['productTotal'];
                $data_opportunity['transactionCurrencyID'] = $leadProductDetail['transactionCurrencyID'];
                $data_opportunity['transactionCurrencyExchangeRate'] = 1;
                $data_opportunity['transactionDecimalPlaces'] = fetch_currency_desimal_by_id($data_opportunity['transactionCurrencyID']);
                $data_opportunity['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $default_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyLocalCurrencyID']);
                $data_opportunity['companyLocalCurrencyExchangeRate'] = $default_currency['conversion'];
                $data_opportunity['companyLocalDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data_opportunity['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data_opportunity['transactionCurrencyID'], $data_opportunity['companyReportingCurrencyID']);
                $data_opportunity['companyReportingCurrencyExchangeRate'] = $reporting_currency['conversion'];
                $data_opportunity['companyReportingDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $data_opportunity['companyID'] = $companyID;
                $data_opportunity['createdUserGroup'] = $this->common_data['user_group'];
                $data_opportunity['createdPCID'] = $this->common_data['current_pc'];
                $data_opportunity['createdUserID'] = $this->common_data['current_userID'];
                $data_opportunity['createdUserName'] = $this->common_data['current_user'];
                $data_opportunity['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_opportunity', $data_opportunity);
                $opportunity_last = $this->db->insert_id();
                if ($opportunity_last) {

                    $permission_update_master['documentID'] = 4;
                    $permission_update_master['documentAutoID'] = $opportunity_last;
                    $permission_update_master['permissionID'] = 1;
                    $permission_update_master['companyID'] = $companyID;
                    $permission_update_master['createdUserGroup'] = $this->common_data['user_group'];
                    $permission_update_master['createdPCID'] = $this->common_data['current_pc'];
                    $permission_update_master['createdUserID'] = $this->common_data['current_userID'];
                    $permission_update_master['createdUserName'] = $this->common_data['current_user'];
                    $permission_update_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $permission_update_master);


                    if ($leadMasterDetail['linkedorganizationID'] == 0) {
                        $data_link_organization['documentID'] = 4;
                        $data_link_organization['MasterAutoID'] = $opportunity_last;
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $organizationID_last;
                        $data_link_organization['searchValue'] = $leadMasterDetail['organization'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);

                        $data_link_organization_toContact['documentID'] = 6;
                        $data_link_organization_toContact['MasterAutoID'] = $leadMasterDetail['contactID'];
                        $data_link_organization_toContact['relatedDocumentID'] = 8;
                        $data_link_organization_toContact['relatedDocumentMasterID'] = $organizationID_last;
                        $data_link_organization_toContact['searchValue'] = $leadMasterDetail['organization'];
                        $data_link_organization_toContact['originFrom'] = NULL;
                        $data_link_organization_toContact['companyID'] = $companyID;
                        $data_link_organization_toContact['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization_toContact['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization_toContact['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization_toContact['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization_toContact['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization_toContact);

                    } else {
                        $data_link_organization['documentID'] = 4;
                        $data_link_organization['MasterAutoID'] = $opportunity_last;
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                        $data_link_organization['searchValue'] = $organizationDetail['Name'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);

                        $data_link_organization['documentID'] = 6;
                        $data_link_organization['MasterAutoID'] =  $leadMasterDetail['contactID'];
                        $data_link_organization['relatedDocumentID'] = 8;
                        $data_link_organization['relatedDocumentMasterID'] = $leadMasterDetail['linkedorganizationID'];
                        $data_link_organization['searchValue'] = $organizationDetail['Name'];
                        $data_link_organization['originFrom'] = NULL;
                        $data_link_organization['companyID'] = $companyID;
                        $data_link_organization['createdUserGroup'] = $this->common_data['user_group'];
                        $data_link_organization['createdPCID'] = $this->common_data['current_pc'];
                        $data_link_organization['createdUserID'] = $this->common_data['current_userID'];
                        $data_link_organization['createdUserName'] = $this->common_data['current_user'];
                        $data_link_organization['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_link_organization);
                    }

                    $update_lead['isClosed'] = 2;
                    $update_lead['modifiedPCID'] = $this->common_data['current_pc'];
                    $update_lead['modifiedUserID'] = $this->common_data['current_userID'];
                    $update_lead['modifiedUserName'] = $this->common_data['current_user'];
                    $update_lead['modifiedDateTime'] = $this->common_data['current_date'];
                    $this->db->where('leadID', $leadMasterID);
                    $this->db->update('srp_erp_crm_leadmaster', $update_lead);
                }
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Lead Updated Successfully.', $leadMasterID);
            }
        }
    }

    function load_opportunity_products_prices()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('productID', $this->input->post('productid'));
        return $this->db->get('srp_erp_crm_products')->row_array();

    }
}