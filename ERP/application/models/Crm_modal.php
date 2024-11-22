<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crm_modal extends ERP_Model
{
    function __contruct()
    {
        parent::__contruct();
    }

    function check_isEmail_campaign_type($typeID)
    {
        $this->db->select('isdefault');
        $this->db->where('categoryID', $typeID);
        $this->db->from('srp_erp_crm_categories');
        return $this->db->get()->row_array();
    }

    function save_campaign_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $newurl = explode("/", $_SERVER['SCRIPT_NAME']);
        $actual_link = "http://$_SERVER[HTTP_HOST]/$newurl[1]/images/";
        $company_code = $this->common_data['company_data']['company_code'];
        $status = trim($this->input->post('statusID') ?? '');
        $startdate = trim($this->input->post('startdate') ?? '');
        $end_date = trim($this->input->post('end_date') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $format_startdate = null;
        if (isset($startdate) && !empty($startdate)) {
            $format_startdate = input_format_date($startdate, $date_format_policy);
        }
        $format_end_date = null;
        if (isset($end_date) && !empty($end_date)) {
            $format_end_date = input_format_date($end_date, $date_format_policy);
        }
        $closedate = trim($this->input->post('closedate') ?? '');
        $closedateconvert = input_format_date($closedate, $date_format_policy);

        $isclose = $this->db->query("SELECT * FROM `srp_erp_crm_status` WHERE companyID = '$companyID' AND statusID = '{$status}' AND documentID = 1")->row_array();

        $startdateconverted = input_format_date($this->input->post('startdate'), $date_format_policy);

        if (($closedateconvert < $startdateconverted) && ($isclose['statusType'] == 1)) {
            return array('e', 'close date Cannot be less than statdate');
            exit();
        }


        $assignTos = $this->input->post('employees');

        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees_permission');

        $campaignMasterID = trim($this->input->post('campaignID') ?? '');
        $isclosed = 0;
        if ($isclose['statusType'] == 1) {
            $isclosed = 1;

        }

        $typeID = trim($this->input->post('typeID') ?? '');
        $emailbody = '';
        $campaign_type = $this->Crm_modal->check_isEmail_campaign_type($typeID);
        if ($campaign_type['isdefault'] == 1) {

            $emailbody = '<div class="" style="border: #dcdcdc solid 1px;"><div id="toolbar"><div class="toolbar-title"></div></div><div class="post-area"><article class=""><header class=""><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td align="center" valign="top"style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table class="container" cellpadding="0" cellspacing="0" width="545"border="0"><tbody><tr><td align="left" valign="top"style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td align="center" valign="top" class="main-content"
style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0">
<tbody><tr><td align="left" valign="top" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td class="webversion" align="center" valign="top" style="-webkit-text-size-adjust:none;color:#5d5d5d;font-family:Arial,Sans-Serif;padding-top:11px;padding-bottom:15px;padding-right:0;padding-left:0;font-size:10px;"><span class="preheader" style="display:none !important;">We’ve got far too much good stuff to fit in this little bit<br>
</span></td></tr></tbody></table></td></tr><tr><td align="left" valign="top" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td align="left" valign="top" class="header" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0">
<tbody><tr><td align="center" valign="top" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;">
<img src="' . $actual_link . "logo/" . $this->common_data['company_data']['company_logo'] . '" width="85" alt="Company Logo" title="Company Logo" style="border-width:0;display:block;font-family:Arial, Sans-Serif, Times, serif;color:#b29548;"><hr></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>
</td></tr></tbody></table><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td align="center" valign="top" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table class="container" cellpadding="0" cellspacing="0" width="600" border="0"><tbody><tr><td align="center" valign="top" class="" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0"><tbody><tr><td align="center" valign="top" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;"><table cellpadding="0" cellspacing="0" width="100%" border="0">
<tbody><tr><td align="center" valign="top" class="copy" style="-webkit-text-size-adjust:none;font-family:\'Times New Roman\', serif;padding-bottom:10px;padding-right:110px;padding-left:110px;">' . $this->input->post('description') . '</td></tr><tr><td align="center" valign="top" class="copy" style="-webkit-text-size-adjust:none;font-family:\'Times New Roman\', serif;padding-bottom:10px;padding-right:110px;padding-left:110px;"><p>If you have any Inquiries, feel free to contact our customer support team.</p></td></tr></tbody></table></td></tr><tr><td align="center" valign="top" class="mobile mobile-cta" style="-webkit-text-size-adjust:none;color:#5d5d5d;font-family:Arial, Sans-Serif, Times, Serif;display:none;overflow:hidden;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-size:0px;width:0px;height:0px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;line-height:0px;"><img src=' . $actual_link . "crm/divider.gif" . ' alt="View Veggie Menu" title="View Veggie Menu" width="1"
height="1" class="mobile scaled" style="border-width:0;font-family:Arial, Sans-Serif, Times, serif;color:#b29548;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;overflow:hidden;display:none;font-size:0px;width:0px;height:0px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;line-height:0px;"></td></tr><tr><td align="center" valign="top" class="divider" style="-webkit-text-size-adjust:none;font-family:Arial, Sans-Serif, Times, Serif;color:#5d5d5d;padding-top:0;padding-bottom:25px;padding-right:0;padding-left:0;"><img src="' . $actual_link . "crm/divider.gif" . '" alt="" class="scaled" width="600" height="21" style="border-width:0;display:block;font-family:Arial, Sans-Serif, Times, serif;color:#b29548;"></td></tr></tbody></table>
</td></tr></tbody></table></td></tr></tbody></table></strong></header></article></div></div>';
        }
        $data['name'] = trim($this->input->post('campaign_name') ?? '');
        $data['description'] = trim_desc($this->input->post('description'));
        $data['emailDescription'] = $emailbody;
        $data['objective'] = trim_desc($this->input->post('objective'));
        $data['type'] = trim($this->input->post('typeID') ?? '');
        $data['status'] = trim($this->input->post('statusID') ?? '');
        $data['startDate'] = $format_startdate;
        $data['endDate'] = $format_end_date;
        $data['isClosed'] = $isclosed;
        if ($isclose['statusType'] == 1) {
            $data['completedDate'] = $closedateconvert;
            $data['completedBy'] = $this->common_data['current_userID'];
        } else {
            $data['completedDate'] = null;
            $data['completedBy'] = null;
        }

        if ($campaignMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->delete('srp_erp_crm_assignees', array('MasterAutoID' => $campaignMasterID));
            if (isset($assignTos) && !empty($assignTos)) {
                foreach ($assignTos as $val) {
                    $this->db->select('empID,MasterAutoID,companyID');
                    $this->db->from('srp_erp_crm_assignees');
                    $this->db->where('empID', $val);
                    $this->db->where('MasterAutoID', $campaignMasterID);
                    $this->db->where('companyID', $companyID);
                    $this->db->where('documentID', 1);
                    $order_detail = $this->db->get()->row_array();
                    $employeeDetail = fetch_employeeNo($order_detail['empID']);
                    if (!empty($order_detail)) {
                        return array('w', 'Employee : ' . $employeeDetail['ECode'] . ' ' . $employeeDetail['Ename2'] . '  already exists.');
                    }

                    $data_detail['documentID'] = 1;
                    $data_detail['MasterAutoID'] = $campaignMasterID;
                    $data_detail['empID'] = $val;
                    $data_detail['companyID'] = $companyID;
                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_assignees', $data_detail);
                }

            }

            $this->db->where('campaignID', $campaignMasterID);
            $update = $this->db->update('srp_erp_crm_campaignmaster', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 1, 'documentAutoID' => $campaignMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 1, 'documentAutoID' => $campaignMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 1;
                $permission_master['documentAutoID'] = $campaignMasterID;
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
                                $permission_detail['documentID'] = 1;
                                $permission_detail['documentAutoID'] = $campaignMasterID;
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
                return array('e', 'Campaign Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Campaign Updated Successfully.', $campaignMasterID);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $companyID;
            $this->load->library('sequence');
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-CMP');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_campaignmaster WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-CMP';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_campaignmaster', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if (isset($assignTos) && !empty($assignTos)) {
                    foreach ($assignTos as $val) {
                        $data_detail['documentID'] = 1;
                        $data_detail['MasterAutoID'] = $last_id;
                        $data_detail['empID'] = $val;
                        $data_detail['companyID'] = $companyID;
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_assignees', $data_detail);
                    }

                }
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 1;
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
                                $permission_detail['documentID'] = 1;
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
                return array('e', 'Campaign Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Campaign Saved Successfully.', $last_id);

            }
        }
    }

    function save_campaign_attendees()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];

        $campaignMasterID = trim($this->input->post('campaignID') ?? '');
        $attendeeMasterID = trim($this->input->post('attendeesID') ?? '');

        $data['campaignID'] = $campaignMasterID;
        $data['contactID'] = trim($this->input->post('contactID') ?? '');
        $data['prefix'] = trim($this->input->post('prefix') ?? '');
        $data['firstName'] = trim($this->input->post('firstName') ?? '');
        $data['lastName'] = trim($this->input->post('lastName') ?? '');
        $data['phoneHome'] = trim($this->input->post('phoneHome') ?? '');
        $data['phoneMobile'] = trim($this->input->post('phoneMobile') ?? '');
        $data['fax'] = trim($this->input->post('fax') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['occupation'] = trim($this->input->post('occupation') ?? '');
        $data['organization'] = trim($this->input->post('organization') ?? '');
        $data['countryID'] = trim($this->input->post('countryID') ?? '');
        $data['address'] = trim($this->input->post('address') ?? '');

        if ($attendeeMasterID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('attendeesID', $attendeeMasterID);
            $this->db->update('srp_erp_crm_attendees', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Campaign Attendee Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Campaign Attendee Updated Successfully.');
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_attendees', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Campaign Attendee Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Campaign Attendee Saved Successfully.');

            }
        }
    }

    function load_campaign_header()
    {
        $convertFormat = convert_date_format_sql();
        $currentuser = current_userID();
        $companyID = current_companyID();
        $campaignid = $this->input->post('campaignID');
        $this->db->select('*,DATE_FORMAT(startDate,\'' . $convertFormat . '\') AS startDate,DATE_FORMAT(endDate,\'' . $convertFormat . '\') AS endDate');
        $this->db->where('campaignID', $this->input->post('campaignID'));
        $data['header'] = $this->db->get('srp_erp_crm_campaignmaster')->row_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 1);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('campaignID'));
        $data['permission'] = $this->db->get()->result_array();

        $assignpermission = $this->db->query("SELECT empID from srp_erp_crm_assignees where documentID = 1 AND companyID = " . $companyID . " AND MasterAutoID = '{$campaignid}' AND empID = '{$currentuser}'")->row_array();
        if (!empty($assignpermission)) {
            $data['assignpermission'] = 1;
        } else {
            $data['assignpermission'] = 0;
        }


        return $data;
    }

    function load_campaign_attendees_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->where('attendeesID', $this->input->post('attendeesID'));
        return $this->db->get('srp_erp_crm_attendees')->row_array();
    }

    function delete_campaign()
    {

        $currentuser = current_userID();
        $company_id = current_companyID();
        $campaignID = trim($this->input->post('campaignID') ?? '');
        $createduser = $this->db->query("SELECT createdUserID FROM `srp_erp_crm_campaignmaster` where companyID = $company_id and campaignID = $campaignID")->row_array();
        $issuperadmin = crm_isSuperAdmin();
        $isgroupadmin = crm_isGroupAdmin();
        if ($issuperadmin['isSuperAdmin'] == 1 || $createduser['createdUserID'] == $currentuser || $isgroupadmin['adminYN'] == 1) {
            $this->db->delete('srp_erp_crm_campaignmaster', array('campaignID' => trim($this->input->post('campaignID') ?? '')));
            $this->db->delete('srp_erp_crm_assignees', array('MasterAutoID' => trim($this->input->post('campaignID') ?? '')));
            $this->db->delete('srp_erp_crm_attendees', array('campaignID' => trim($this->input->post('campaignID') ?? '')));
            return array('s', 'Campaign deleted successfully');
        } else {
            return array('w', 'You do not have the permission to delete');
        }

    }

    function delete_task()
    {
        $currentuser = current_userID();
        $company_id = current_companyID();
        $taskid = trim($this->input->post('taskID') ?? '');
        $createduser = $this->db->query("SELECT createdUserID FROM `srp_erp_crm_task` where companyID = $company_id and taskID = $taskid")->row_array();
        $issuperadmin = crm_isSuperAdmin();
        $isgroupadmin = crm_isGroupAdmin();
        if ($issuperadmin['isSuperAdmin'] == 1 || $createduser['createdUserID'] == $currentuser || $isgroupadmin['adminYN'] == 1) {
            $this->db->delete('srp_erp_crm_task', array('taskID' => trim($this->input->post('taskID') ?? '')));
            $this->db->delete('srp_erp_crm_assignees', array('MasterAutoID' => trim($this->input->post('taskID') ?? '')));
            return array('s', 'Task deleted successfully');
        } else {
            return array('w', 'You do not have the permission to delete');
        }

    }

    function fetch_campaign_employee_detail()
    {
        $this->db->select('AssingeeID,empID,MasterAutoID');
        $this->db->from('srp_erp_crm_assignees');
        $this->db->where('MasterAutoID', $this->input->post('MasterAutoID'));
        return $this->db->get()->result_array();
    }

    function fetch_campaign_attendees_detail()
    {
        $this->db->select('attendeesID,  CONCAT(firstName, \' \', lastName) as fullname,organization,occupation,phoneMobile,email,isAttended,srp_employeesdetails.Ename2,convertedToLead');
        $this->db->from('srp_erp_crm_attendees');
        $this->db->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_crm_attendees.attendeeMarkedby', 'left');
        $this->db->where('campaignID', $this->input->post('campaignID'));
        return $this->db->get()->result_array();
    }

    function delete_campaign_detail()
    {
        $this->db->where('AssingeeID', $this->input->post('AssingeeID'));
        $results = $this->db->delete('srp_erp_crm_assignees');
        $this->session->set_flashdata('s', 'Assigned Employee Deleted Successfully');
        return true;
    }

    function delete_campaign_attendees_detail()
    {
        $this->db->where('attendeesID', $this->input->post('attendeesID'));
        $results = $this->db->delete('srp_erp_crm_attendees');
        $this->session->set_flashdata('s', 'Assigned Attendee Deleted Successfully');
        return true;
    }

    function save_task_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $company_code = $this->common_data['company_data']['company_code'];
        $status = trim($this->input->post('statusID') ?? '');
        $startdate = trim($this->input->post('startdate') ?? '');
        $duedate = trim($this->input->post('duedate') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $relatedAutoIDs = $this->input->post('relatedAutoID');
        $relatedTo = $this->input->post('relatedTo');
        $relatedToSearch = $this->input->post('related_search');
        $linkedFromOrigin = $this->input->post('linkedFromOrigin');
        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');
        $employeesuserpermission = $this->input->post('multipleemployees');
        $isclosed = 0;
        $companyid = current_companyID();
        $groupid = trim($this->input->post('groupID') ?? '');
        $closedate = trim($this->input->post('closedate') ?? '');
        $closedateconvert = input_format_date($closedate, $date_format_policy);
        $issubtask = trim($this->input->post('issubtask') ?? '');
        $isclose = $this->db->query("SELECT * FROM `srp_erp_crm_status` WHERE companyID = '$companyid' AND statusID = '{$status}' AND documentID = 2")->row_array();

        $startdateconverted = input_format_date($this->input->post('startdate'), $date_format_policy);

        if (($closedateconvert < $startdateconverted) && ($isclose['statusType'] == 1)) {
            return array('e', 'close date Cannot be less than statdate');
            exit();
        }

        $isclosed = 0;
        if ($isclose['statusType'] == 1) {
            $isclosed = 1;

        }
        $opportunityID = 0;
        if (!empty($this->input->post('opportunityID'))) {
            $opportunityID = trim($this->input->post('opportunityID') ?? '');
        }
        $projectID = 0;
        if (!empty($this->input->post('projectID'))) {
            $projectID = trim($this->input->post('projectID') ?? '');
        }
        $pipelineStageID = 0;
        if (!empty($this->input->post('pipelineStageID'))) {
            $pipelineStageID = trim($this->input->post('pipelineStageID') ?? '');
        }

        $format_startdate = null;
        if (isset($startdate) && !empty($startdate)) {
            $dteStart = new DateTime($startdate);
            $format_startdate = $dteStart->format('Y-m-d H:i:s');
        }
        $format_duedate = null;
        if (isset($duedate) && !empty($duedate)) {
            $dueStart = new DateTime($duedate);
            $format_duedate = $dueStart->format('Y-m-d H:i:s');
        }
        $assignTos = $this->input->post('employees');
        $taskMasterID = trim($this->input->post('taskID') ?? '');


        $data['subject'] = trim($this->input->post('subject') ?? '');
        $data['isSubTaskEnabled'] = trim($this->input->post('issubtask') ?? '');
        $data['categoryID'] = trim($this->input->post('categoryID') ?? '');
        $data['status'] = trim($this->input->post('statusID') ?? '');
        $data['isClosed'] = $isclosed;
        $data['opportunityID'] = $opportunityID;
        $data['projectID'] = $projectID;
        $data['pipelineStageID'] = $pipelineStageID;

        $data['starDate'] = $format_startdate;
        $data['DueDate'] = $format_duedate;
        $data['Priority'] = trim($this->input->post('priority') ?? '');
        $data['visibility'] = trim($this->input->post('userPermission') ?? '');
        $data['description'] = trim_desc($this->input->post('description'));
        if ($isclose['statusType'] == 1) {
            $data['completedDate'] = $closedateconvert;
            $data['completedBy'] = $this->common_data['current_userID'];
        } else {
            $data['completedDate'] = null;
            $data['completedBy'] = null;
        }

        if ($taskMasterID) {
            $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_crm_subtasks where taskID = $taskMasterID ANd companyID  = $companyID")->row_array();
            $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_crm_subtasks where taskID = $taskMasterID ANd companyID  = $companyID AND `status` = 2")->row_array();

            if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
                $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
            } else {
                $subtaskpercentage = 0;
            }
            if ($issubtask == 1) {
                $data['progress'] = $subtaskpercentage;
            } else {
                $data['progress'] = trim($this->input->post('progress') ?? '');
            }


            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->delete('srp_erp_crm_assignees', array('documentID' => 2, 'MasterAutoID' => $taskMasterID));
            if (isset($assignTos) && !empty($assignTos)) {
                foreach ($assignTos as $val) {

                    /*                    $this->db->select('empID,MasterAutoID,companyID');
                                        $this->db->from('srp_erp_crm_assignees');
                                        $this->db->where('empID', $val);
                                        $this->db->where('MasterAutoID', $taskMasterID);
                                        $this->db->where('companyID', $companyID);
                                        $this->db->where('documentID', 2);
                                        $order_detail = $this->db->get()->row_array();
                                        $employeeDetail = fetch_employeeNo($order_detail['empID']);
                                        if (!empty($order_detail)) {
                                            return array('w', 'Employee : ' . $employeeDetail['ECode'] . ' ' . $employeeDetail['Ename2'] . '  already exists.');
                                        }*/

                    $data_detail['documentID'] = 2;
                    $data_detail['MasterAutoID'] = $taskMasterID;
                    $data_detail['empID'] = $val;
                    $data_detail['companyID'] = $companyID;
                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_assignees', $data_detail);
                }

            }
            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_crm_link', array('documentID' => 2, 'MasterAutoID' => $taskMasterID));
                foreach ($relatedAutoIDs as $key => $itemAutoID) {
                    $data_link['documentID'] = 2;
                    $data_link['MasterAutoID'] = $taskMasterID;
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


            $this->db->where('taskID', $taskMasterID);
            $update = $this->db->update('srp_erp_crm_task', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 2, 'documentAutoID' => $taskMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 2, 'documentAutoID' => $taskMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 2;
                $permission_master['documentAutoID'] = $taskMasterID;
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
                        if (isset($employeesuserpermission) && !empty($employeesuserpermission)) {
                            foreach ($employeesuserpermission as $val) {
                                $permission_detail['documentPermissionID'] = $permission_id;
                                $permission_detail['documentID'] = 2;
                                $permission_detail['documentAutoID'] = $taskMasterID;
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
                return array('e', 'Task Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Task Updated Successfully.', $taskMasterID);
            }
        } else {

            if ($issubtask != 1) {
                $data['progress'] = trim($this->input->post('progress') ?? '');
            }

            $this->load->library('sequence');
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-TSK');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_task WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-TSK';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_task', $data);
            $last_id = $this->db->insert_id();


            if ($last_id) {
                if (isset($assignTos) && !empty($assignTos)) {
                    foreach ($assignTos as $val) {
                        $data_detail['documentID'] = 2;
                        $data_detail['MasterAutoID'] = $last_id;
                        $data_detail['empID'] = $val;
                        $data_detail['companyID'] = $companyID;
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_assignees', $data_detail);
                    }

                }
                if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                    foreach ($relatedAutoIDs as $key => $itemAutoID) {
                        $data_link['documentID'] = 2;
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
            }
            if ($userPermission == 2) {
                $permission_master['permissionValue'] = $this->common_data['current_userID'];
            } else if ($userPermission == 3) {
                $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
            }
            $permission_master['documentID'] = 2;
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
                    if (isset($employeesuserpermission) && !empty($employeesuserpermission)) {
                        foreach ($employeesuserpermission as $val) {
                            $permission_detail['documentPermissionID'] = $permission_id;
                            $permission_detail['documentID'] = 2;
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
        if ($issubtask == 1) {
            $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_crm_subtasks where taskID = $last_id ANd companyID  = $companyID")->row_array();
            $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_crm_subtasks where taskID = $last_id ANd companyID  = $companyID AND `status` = 2")->row_array();

            if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
                $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
            } else {
                $subtaskpercentage = 0;
            }
            $dataupdatetask['progress'] = $subtaskpercentage;
            $this->db->where('taskID', $last_id);
            $this->db->update('srp_erp_crm_task', $dataupdatetask);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Task Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Task Saved Successfully.', $last_id);

        }


    }


    function load_task_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $taskid = $this->input->post('taskID');
        $empid = current_userID();
        $this->db->select('*,DATE_FORMAT(starDate,\'' . $convertFormat . ' %h:%i %p\') AS starDate,DATE_FORMAT(DueDate,\'' . $convertFormat . ' %h:%i %p\') AS DueDate,DATE_FORMAT(completedDate,\'' . $convertFormat . '\') AS completedDatecovverted');
        $this->db->where('companyID', $companyID);
        $this->db->where('taskID', $this->input->post('taskID'));
        $data['header'] = $this->db->get('srp_erp_crm_task')->row_array();

        $this->db->select('*');
        $this->db->where('documentID', 2);
        $this->db->where('MasterAutoID', $this->input->post('taskID'));
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 2);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('taskID'));
        $data['permission'] = $this->db->get()->result_array();

        $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_crm_subtasks where taskID = $taskid ANd companyID  = $companyID")->row_array();
        $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_crm_subtasks where taskID = $taskid ANd companyID  = $companyID AND `status` = 2")->row_array();

        if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
            $data['subtaskpercentage'] = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
        } else {
            $data['subtaskpercentage'] = 0;
        }


        $assignpermission = $this->db->query("SELECT empID FROM `srp_erp_crm_assignees` WHERE companyID = '{$companyID}' AND documentID = 2 AND MasterAutoID = '{$taskid}' AND empID = '{$empid}'")->row_array();
        if (!empty($assignpermission)) {
            $data['assignpermission'] = 1;
        } else {
            $data['assignpermission'] = 0;
        }

        return $data;
    }

    function fetch_tasks_employee_detail()
    {
        $this->db->select('AssingeeID,empID,MasterAutoID');
        $this->db->from('srp_erp_crm_assignees');
        $this->db->where('MasterAutoID', $this->input->post('MasterAutoID'));
        $this->db->where('documentID', 2);
        return $this->db->get()->result_array();
    }

    function fetch_document_relate_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $related_type = $_GET['t'];

        $dataArr = array();
        $dataArr2 = array();
        $dataArr2['query'] = 'test';

        if (!empty($related_type)) {
            switch ($related_type) {
                case 2:
                    $data = $this->db->query('SELECT taskID,subject AS "Match" FROM srp_erp_crm_task WHERE companyID = "' . $companyID . '" AND subject LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['taskID'], 'DoucumentAutoID' => $val['taskID'], 'relatedDoucumentID' => 2);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 4:
                    $data = $this->db->query('SELECT opportunityID,opportunityName AS "Match" FROM srp_erp_crm_opportunity WHERE companyID = "' . $companyID . '" AND opportunityName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['opportunityID'], 'DoucumentAutoID' => $val['opportunityID'], 'relatedDoucumentID' => 4);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 5:
                    $data = $this->db->query('SELECT leadID,CONCAT(firstName," ", lastName) AS "Match" FROM srp_erp_crm_leadmaster WHERE companyID = "' . $companyID . '" AND firstName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['leadID'], 'DoucumentAutoID' => $val['leadID'], 'relatedDoucumentID' => 5);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 6:
                    $data = $this->db->query('SELECT contactID,CONCAT(firstName," ", lastName) AS "Match" FROM srp_erp_crm_contactmaster WHERE companyID = "' . $companyID . '" AND firstName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['contactID'], 'DoucumentAutoID' => $val['contactID'], 'relatedDoucumentID' => 6);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 7:
                    echo "Your favorite color is green!";
                    break;
                case 8:
                    $data = $this->db->query('SELECT organizationID,Name AS "Match" FROM srp_erp_crm_organizations WHERE companyID = "' . $companyID . '" AND Name LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['organizationID'], 'DoucumentAutoID' => $val['organizationID'], 'relatedDoucumentID' => 8);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                case 9:
                    $data = $this->db->query('SELECT projectID,projectName AS "Match" FROM srp_erp_crm_project WHERE companyID = "' . $companyID . '" AND projectName LIKE "' . $search_string . '"')->result_array();
                    if (!empty($data)) {
                        foreach ($data as $val) {
                            $dataArr[] = array('value' => $val["Match"], 'data' => $val['projectID'], 'DoucumentAutoID' => $val['projectID'], 'relatedDoucumentID' => 9);
                        }
                        $dataArr2['suggestions'] = $dataArr;
                    }
                    return $dataArr2;
                    break;
                default:
                    return '';
            }

        }

    }

    function fetch_contact_relate_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        $dataArr = array();
        $dataArr2 = array();

        if (!empty($search_string)) {
            $data = $this->db->query('SELECT *,CONCAT(firstName," ", lastName) AS "Match" FROM srp_erp_crm_contactmaster WHERE srp_erp_crm_contactmaster.companyID = "' . $companyID . '" AND firstName LIKE "' . $search_string . '" ')->result_array();
            if (!empty($data)) {
                foreach ($data as $val) {
                    $dataArr[] = array('value' => $val["Match"], 'prefix' => $val['prefix'], 'contactID' => $val['contactID'], 'firstName' => $val['firstName'], 'lastName' => $val['lastName'], 'title' => $val['occupation'], 'phoneMobile' => $val['phoneMobile'], 'email' => $val['email'], 'phoneHome' => $val['phoneHome'], 'fax' => $val['fax'], 'address' => $val['address'], 'city' => $val['city'], 'postalCode' => $val['postalCode'], 'state' => $val['state'], 'countryID' => $val['countryID']);
                }
                $dataArr2['suggestions'] = $dataArr;
            }
            return $dataArr2;
        }
    }

    function save_contact_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $contactMasterID = trim($this->input->post('contactID') ?? '');
        $organization = $this->input->post('linkorganization');

        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $data['prefix'] = trim($this->input->post('prefix') ?? '');
        $data['firstName'] = trim($this->input->post('firstName') ?? '');
        $data['lastName'] = trim($this->input->post('lastName') ?? '');
        $data['occupation'] = trim($this->input->post('occupation') ?? '');
        $data['department'] = trim($this->input->post('department') ?? '');
        $data['organization'] = trim($this->input->post('organization') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['phoneMobile'] = trim($this->input->post('phoneMobile') ?? '');
        $data['phoneHome'] = trim($this->input->post('phoneHome') ?? '');
        $data['fax'] = trim($this->input->post('fax') ?? '');
        $data['postalCode'] = trim($this->input->post('postalcode') ?? '');
        $data['city'] = trim($this->input->post('city') ?? '');
        $data['state'] = trim($this->input->post('state') ?? '');
        $data['countryID'] = trim($this->input->post('countryID') ?? '');
        $data['address'] = trim($this->input->post('address') ?? '');
        $data['sourceID'] = trim($this->input->post('sourceID') ?? '');
        $data['remark'] = trim($this->input->post('remarkContact') ?? '');
        $data['AccountID'] = 0;
        $data['campaignID'] = 0;

        if ($contactMasterID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
            $update = $this->db->update('srp_erp_crm_contactmaster', $data);

            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 6, 'documentAutoID' => $contactMasterID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 6, 'documentAutoID' => $contactMasterID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 6;
                $permission_master['documentAutoID'] = $contactMasterID;
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
                                $permission_detail['documentID'] = 6;
                                $permission_detail['documentAutoID'] = $contactMasterID;
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

            //deleting existing organizations from srp_erp_crm_link table
            $this->db->where('documentID', 6);
            $this->db->where('relatedDocumentID', 8);
            $this->db->where('MasterAutoID', $contactMasterID);
            $this->db->delete('srp_erp_crm_link');

            if (isset($organization) && !empty($organization)) {
                foreach ($organization as $val) {
                    $data_detail['documentID'] = 6;
                    $data_detail['MasterAutoID'] = $contactMasterID;
                    $data_detail['relatedDocumentID'] = 8;
                    $data_detail['companyID'] = $companyID;
                    $data_detail['relatedDocumentMasterID'] = $val;
                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_link', $data_detail);
                }

            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contact Update Failed ' /*. $this->db->_error_message()*/);

            } else {
                $this->db->trans_commit();
                return array('s', 'Contact Updated Successfully.', $contactMasterID);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-CONT');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_contactmaster WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-CONT';

            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-CONT' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/


            $this->db->insert('srp_erp_crm_contactmaster', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if (isset($organization) && !empty($organization)) {
                    foreach ($organization as $val) {
                        $data_detail['documentID'] = 6;
                        $data_detail['MasterAutoID'] = $last_id;
                        $data_detail['relatedDocumentID'] = 8;
                        $data_detail['relatedDocumentMasterID'] = $val;
                        $data_detail['companyID'] = $companyID;
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_link', $data_detail);
                    }

                }
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 6;
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
                                $permission_detail['documentID'] = 6;
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
                return array('e', 'Contact Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Contact Saved Successfully.');

            }
        }
    }

    function load_contact_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('*');
        $this->db->where('contactID', $this->input->post('contactID'));
        $this->db->from('srp_erp_crm_contactmaster');
        $data['header'] = $this->db->get()->row_array();

        $this->db->select('relatedDocumentMasterID');
        $this->db->where('documentID', 6);
        $this->db->where('relatedDocumentID', 8);
        $this->db->where('MasterAutoID', $this->input->post('contactID'));
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 6);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('contactID'));
        $data['permission'] = $this->db->get()->result_array();

        return $data;

    }

    function load_organization_header()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select('*');
        $this->db->where('organizationID', $this->input->post('organizationID'));
        $data['header'] = $this->db->get('srp_erp_crm_organizations')->row_array();

        $this->db->select('permissionID,permissionValue,srp_erp_crm_documentpermissiondetails.empID');
        $this->db->from('srp_erp_crm_documentpermission');
        $this->db->join('srp_erp_crm_documentpermissiondetails', 'srp_erp_crm_documentpermission.documentPermissionID = srp_erp_crm_documentpermissiondetails.documentPermissionID', 'LEFT');
        $this->db->where('srp_erp_crm_documentpermission.documentID', 8);
        $this->db->where('srp_erp_crm_documentpermission.documentAutoID', $this->input->post('organizationID'));
        $data['permission'] = $this->db->get()->result_array();

        return $data;

    }

    function update_task_edit_view_comment()
    {

        $this->db->trans_start();
        $data['comment'] = trim($this->input->post('taskcomment') ?? '');
        $data['commentedUserID'] = $this->common_data['current_userID'];
        $this->db->where('taskID', trim($this->input->post('taskID') ?? ''));
        $this->db->update('srp_erp_crm_task', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Comment Save Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Comment Saved Successfully.');

        }
    }

    function update_campaign_edit_view_comment()
    {

        $this->db->trans_start();
        $data['comment'] = trim($this->input->post('campaigncomment') ?? '');
        $data['commentedUserID'] = $this->common_data['current_userID'];
        $this->db->where('campaignID', trim($this->input->post('campaignID') ?? ''));
        $this->db->update('srp_erp_crm_campaignmaster', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Comment Save Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Comment Saved Successfully.');

        }
    }

    function delete_contact_master()
    {
        $contactid = $this->input->post('contactID');
        $companyid = current_companyID();
        $results = $this->db->query("SELECT
	relatedDocumentMasterID
FROM
	`srp_erp_crm_link`
	where
	companyID = $companyid
	AND documentID = 2
	AND relatedDocumentMasterID = $contactid
	UNION
	SELECT
	relatedDocumentMasterID
FROM
	`srp_erp_crm_link`
	where
	companyID = $companyid
	AND documentID = 4
	AND relatedDocumentMasterID = $contactid
		UNION
	SELECT
	relatedDocumentMasterID
FROM
	`srp_erp_crm_link`
	where
	companyID = $companyid
	AND documentID = 9
	AND relatedDocumentMasterID = $contactid ")->row_array();
        if (!empty($results)) {
            $this->session->set_flashdata('e', 'Contact cannot be deleted,it has already pulled in another document');
            return false;
        } else {
            $this->db->where('contactID', $this->input->post('contactID'));
            $results = $this->db->delete('srp_erp_crm_contactmaster');
            $this->session->set_flashdata('s', 'Contact Deleted Successfully');
            return true;
        }


    }

    function delete_organization_master()
    {
        $organizationID = $this->input->post('organizationID');
        $companyid = current_companyID();
        /* $results = $this->db->query("SELECT
     relatedDocumentMasterID
 FROM
     `srp_erp_crm_link`
     where
     companyID = $companyid
     AND documentID = 2
     AND relatedDocumentMasterID = $organizationID
     UNION
     SELECT
     relatedDocumentMasterID
 FROM
     `srp_erp_crm_link`
     where
     companyID = $companyid
     AND documentID = 4
     AND relatedDocumentMasterID = $organizationID
         UNION
     SELECT
     relatedDocumentMasterID
 FROM
     `srp_erp_crm_link`
     where
     companyID = $companyid
     AND documentID = 9
     AND relatedDocumentMasterID = $organizationID ")->row_array();*/
        $this->db->where('organizationID', $this->input->post('organizationID'));
        $results = $this->db->delete('srp_erp_crm_organizations');
        $this->session->set_flashdata('s', 'Organization Deleted Successfully');
        return true;
    }

    function campaign_attendess_marked()
    {

        $this->db->trans_start();
        $value = trim($this->input->post('value') ?? '');
        if ($value == 0) {
            $data['attendeeMarkedby'] = '';
        } else {
            $data['attendeeMarkedby'] = $this->common_data['current_userID'];
        }
        $data['isAttended'] = $value;
        $this->db->where('attendeesID', trim($this->input->post('attendeesID') ?? ''));
        $this->db->update('srp_erp_crm_attendees', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Comment Save Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Campaign Attendees Updated Successfully.');

        }
    }

    function add_all_contact_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $contactID = trim($this->input->post('contactID') ?? '');
        $notesID = trim($this->input->post('notesID') ?? '');

        $data['contactID'] = $contactID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['documentID'] = 6;

        if ($notesID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('notesID', $notesID);
            $this->db->update('srp_erp_crm_contactnotes', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contact Note Update Failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Contact Note Updated Successfully.');

            }
        } else {

            $data['companyID'] = $companyID;
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
                return array('e', 'Contact Note Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Contact Note Saved Successfully.');

            }
        }

    }

    function contact_image_upload()
    {
        $this->load->library('s3');
        $contactID = $this->input->post('contactID');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT
	contactImage 
FROM
	`srp_erp_crm_contactmaster` 
WHERE
	companyID = $companyid 
	AND contactID = $contactID ")->row_array();
        if(!empty($itemimageexist))
        {
            $this->s3->delete('uploads/crm/profileimage/'.$itemimageexist['contactImage']);
        }
        $info = new SplFileInfo($_FILES["files"]["name"]);

        $fileName = 'Contact_' . trim($this->input->post('contactID') ?? '') .'_'.$this->common_data['company_data']['company_code']. '.' . $info->getExtension();

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
        $path = "uploads/crm/profileimage/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }



        $this->db->trans_start();
     /*   $output_dir = "uploads/crm/profileimage/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/crm", 007);
            mkdir("uploads/crm/profileimage", 007);
        }*/
        /*$attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        echo $info->getExtension();exit;
        $fileName = 'Contact_' . trim($this->input->post('contactID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);

        $data['contactImage'] = $fileName;

        $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
        $this->db->update('srp_erp_crm_contactmaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Image uploaded  Successfully.');
        }*/


        $currentDatetime = format_date_mysql_datetime();
   /*     $attachment_file = $_FILES["files"];
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $currentdate = $currentDatetime;
        $fileName = 'Contact_' . trim($this->input->post('contactID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);*/
        $currentDatetime = format_date_mysql_datetime();
        $currentdate = $currentDatetime;

        $data['contactImage'] = $fileName;
        $data['timestamp'] = $currentdate;

        $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
        $this->db->update('srp_erp_crm_contactmaster', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();

            return array('s', 'Image uploaded  Successfully.');


        }

        /*  $attachment_file = $_FILES["files"];
          $info = new SplFileInfo($_FILES["files"]["name"]);
          $path = UPLOAD_PATH . base_url() . $output_dir;
          $fileName = 'Contact_' . trim($this->input->post('contactID') ?? '') . '.' . $info->getExtension();
          $config['upload_path'] = $path;
          $config['allowed_types'] = 'gif|png|jpg|jpeg';
          $config['max_size'] = '200000';
          $config['file_name'] = $fileName;
          $this->load->library('upload', $config);
          $this->upload->initialize($config);
          //empImage is  => $_FILES['empImage']['name'];
          $currentDatetime = format_date_mysql_datetime();

          $this->db->trans_complete();
          if (!$this->upload->do_upload("files")) {
              return array('e', 'Image upload failed ' . $this->upload->display_errors());
          } else {
              $data['contactImage'] = $fileName;
              $data['timestamp'] = $currentDatetime;
              $this->db->where('contactID', trim($this->input->post('contactID') ?? ''));
              $this->db->update('srp_erp_crm_contactmaster', $data);
              return array('s', $fileName);
          }*/

    }

    function save_organization_header()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();

        $companyID = $this->common_data['company_data']['company_id'];
        $company_code = $this->common_data['company_data']['company_code'];
        $organizationID = trim($this->input->post('organizationID') ?? '');

        $userPermission = $this->input->post('userPermission');
        $employees = $this->input->post('employees');

        $billingCountryID = 0;
        if (!empty($this->input->post('billingCountryID'))) {
            $billingCountryID = $this->input->post('billingCountryID');
        }

        $shippingCountryID = 0;
        if (!empty($this->input->post('shippingCountryID'))) {
            $shippingCountryID = $this->input->post('shippingCountryID');
        }

        $data['Name'] = trim($this->input->post('Name') ?? '');
        $data['industry'] = trim($this->input->post('industry') ?? '');
        $data['numberofEmployees'] = trim($this->input->post('numberofEmployees') ?? '');
        $data['email'] = trim($this->input->post('email') ?? '');
        $data['telephoneNo'] = trim($this->input->post('telephoneNo') ?? '');
        $data['fax'] = trim($this->input->post('fax') ?? '');
        $data['website'] = trim($this->input->post('website') ?? '');
        $data['billingAddress'] = trim($this->input->post('billingAddress') ?? '');
        $data['billingCity'] = trim($this->input->post('billingCity') ?? '');
        $data['billingCountryID'] = $billingCountryID;
        $data['billingPostalCode'] = trim($this->input->post('billingPostalCode') ?? '');
        $data['billingState'] = trim($this->input->post('billingState') ?? '');
        $data['shippingAddress'] = trim($this->input->post('shippingAddress') ?? '');
        $data['shippingCity'] = trim($this->input->post('shippingCity') ?? '');
        $data['shippingCountryID'] = $shippingCountryID;
        $data['shippingPostalCode'] = trim($this->input->post('shippingPostalCode') ?? '');
        $data['shippingState'] = trim($this->input->post('shippingState') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['customerID'] = trim($this->input->post('linktocustomerID') ?? '');

        if ($organizationID) {

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('organizationID', $organizationID);
            $update = $this->db->update('srp_erp_crm_organizations', $data);
            if ($update) {
                $this->db->delete('srp_erp_crm_documentpermission', array('documentID' => 4, 'documentAutoID' => $organizationID));
                $this->db->delete('srp_erp_crm_documentpermissiondetails', array('documentID' => 4, 'documentAutoID' => $organizationID));
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 8;
                $permission_master['documentAutoID'] = $organizationID;
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
                                $permission_detail['documentID'] = 8;
                                $permission_detail['documentAutoID'] = $organizationID;
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
                return array('e', 'Organization Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Organization Updated Successfully.', $organizationID);
            }
        } else {
            $this->load->library('sequence');
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];;
            $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-ORG');
            $serial = $this->db->query("SELECT IF( isnull( MAX( serialNo ) ), 1, ( MAX( serialNo ) + 1 ) ) AS serialNumber FROM srp_erp_crm_organizations WHERE companyID = $companyID")->row_array();
            $data['serialNo'] = $serial['serialNumber'];
            $data['documentID'] = 'CRM-ORG';
            /* $data['documentSystemCode'] = ($company_code . '/' . 'CRM-ORG' . str_pad($data['serialNo'], 6,
                     '0', STR_PAD_LEFT));*/

            $this->db->insert('srp_erp_crm_organizations', $data);
            $last_id = $this->db->insert_id();
            if ($last_id) {
                if ($userPermission == 2) {
                    $permission_master['permissionValue'] = $this->common_data['current_userID'];
                } else if ($userPermission == 3) {
                    $permission_master['permissionValue'] = trim($this->input->post('groupID') ?? '');
                }
                $permission_master['documentID'] = 8;
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
                                $permission_detail['documentID'] = 8;
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
                return array('e', 'Organization Save Failed ' . $this->db->_error_message(), $last_id);
            } else {
                $this->db->trans_commit();
                return array('s', 'Organization Saved Successfully.');

            }
        }
    }

    function organization_image_upload()
    {
        $this->load->library('s3');
        $organizationID = trim($this->input->post('organizationID') ?? '');
        $companyid = current_companyID();
        $itemimageexist = $this->db->query("SELECT
	organizationLogo 
FROM
	`srp_erp_crm_organizations` 
WHERE
	companyID = $companyid 
	AND organizationID = $organizationID ")->row_array();
        if(!empty($itemimageexist))
        {
            $this->s3->delete('uploads/crm/organizationLogo/'.$itemimageexist['organizationLogo']);
        }
        $info = new SplFileInfo($_FILES["files"]["name"]);



        $fileName = 'Organization_'.$this->common_data['company_data']['company_code'].'_'. trim($this->input->post('organizationID') ?? '') . '.' . $info->getExtension();
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
        $path = "uploads/crm/organizationLogo/$fileName";
        $s3Upload = $this->s3->upload($file['tmp_name'], $path);

        if (!$s3Upload) {
            return array('e', "Error in document upload location configuration");
        }

       /* $output_dir = "uploads/crm/organizationLogo/";
        if (!file_exists($output_dir)) {
            mkdir("uploads/crm", 007);
            mkdir("uploads/crm/organizationLogo", 007);
        }
        $attachment_file = $_FILES["files"];
        $currentDatetime = format_date_mysql_datetime();
        $info = new SplFileInfo($_FILES["files"]["name"]);
        $fileName = 'Organization_' . trim($this->input->post('organizationID') ?? '') . '.' . $info->getExtension();
        move_uploaded_file($_FILES["files"]["tmp_name"], $output_dir . $fileName);*/
        $this->db->trans_start();
        $data['organizationLogo'] = $fileName;
        $data['timestamp'] = $currentDatetime;

        $this->db->where('organizationID', trim($this->input->post('organizationID') ?? ''));
        $this->db->update('srp_erp_crm_organizations', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', "Image Upload Failed." . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Image uploaded  Successfully.');
        }

        /* $this->db->trans_start();
         $output_dir = "uploads/crm/organizationLogo/";
         if (!file_exists($output_dir)) {
             mkdir("uploads/crm", 007);
             mkdir("uploads/crm/organizationLogo", 007);
         }

         $attachment_file = $_FILES["files"];
         $info = new SplFileInfo($_FILES["files"]["name"]);
         $path = UPLOAD_PATH . base_url() . $output_dir;
         $fileName = 'Organization_' . trim($this->input->post('organizationID') ?? '') . '.' . $info->getExtension();
         $config['upload_path'] = $path;
         $config['allowed_types'] = 'gif|png|jpg|jpeg';
         $config['max_size'] = '200000';
         $config['file_name'] = $fileName;

         $this->load->library('upload', $config);
         $this->upload->initialize($config);


         $this->db->trans_complete();
         if (!$this->upload->do_upload("files")) {
             return array('e', 'Image upload failed ' . $this->upload->display_errors());
         } else {
             $data['organizationLogo'] = $fileName;
             $this->db->where('organizationID', trim($this->input->post('organizationID') ?? ''));
             $this->db->update('srp_erp_crm_organizations', $data);

             return array('s', 'Image uploaded  Successfully.');
         }*/
    }

    function add_all_organization_notes()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $organizationID = trim($this->input->post('organizationID') ?? '');

        $data['contactID'] = $organizationID;
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['companyID'] = $companyID;
        $data['documentID'] = 8;
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
            return array('e', 'Contact Note Save Failed ' . $this->db->_error_message(), $last_id);
        } else {
            $this->db->trans_commit();
            return array('s', 'Contact Note Saved Successfully.');

        }
    }

    function srp_erp_add_users()
    {
        $this->db->trans_start();
        $employee = $this->input->post('employees');
        $commaList = implode(', ', $employee);
        $companyID = $this->common_data['company_data']['company_id'];
        $emp = $this->db->query("select * from srp_employeesdetails WHERE EIdNo IN ({$commaList})")->result_array();
        if ($emp) {
            foreach ($emp as $value) {
                $exist = $this->db->query("select userID from srp_erp_crm_users WHERE employeeID = {$value['EIdNo']} AND companyID= $companyID")->row_array();
                if (empty($exist)) {
                    $data['employeeID'] = $value['EIdNo'];
                    $data['employeeName'] = $value['Ename2'];
                    $data['emailID'] = $value['EEmail'];;
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $this->db->insert('srp_erp_crm_users', $data);
                }
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Users save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Users Saved Successfully.');
        }

    }

    function delete_user()
    {
        $companyid = current_companyID();
        $userid = $this->input->post('userID');

        $userexist = $this->db->query("SELECT * FROM `srp_erp_crm_usergroupdetails` where companyID = $companyid And userID = $userid")->row_array();
        if (!empty($userexist)) {
            return array('w', 'This user already assigned for a usergroup.');
        } else {

            $this->db->delete('srp_erp_crm_users', array('userID' => trim($this->input->post('userID') ?? '')));
            return array('s', 'User deleted Successfully.');
        }


    }

    function srp_erp_add_usergroup()
    {


        $this->db->trans_start();
        $usergroup = $this->input->post('groupName');
        $companyID = $this->common_data['company_data']['company_id'];
        $usergroupexist = $this->db->query("SELECT *  FROM `srp_erp_crm_usergroups` where  companyID = $companyID And groupName = '" . $usergroup . "' ")->row_array();
        if ($usergroupexist) {
            return array('e', 'User group already exist.');
        } else {
            $data['groupName'] = $usergroup;
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');
            $data['createdUserName'] = $this->common_data['current_user'];

            $this->db->insert('srp_erp_crm_usergroups', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'User group save failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'User group saved successfully.');

            }
        }


    }

    function delete_usergroup()
    {
        $this->db->delete('srp_erp_crm_usergroups', array('groupID' => trim($this->input->post('groupID') ?? '')));
        $this->db->delete('srp_erp_crm_usergroupdetails', array('groupMasterID' => trim($this->input->post('groupID') ?? '')));
        return true;
    }

    function assign_employee_usergroup()
    {
        $this->db->trans_start();
        $employee = $this->input->post('employees');
        $groupMasterID = $this->input->post('groupID');
        $commaList = implode(', ', $employee);
        $companyid = current_companyID();
        $groupDetail = $this->db->query("select isAdmin from srp_erp_crm_usergroups WHERE groupID = {$groupMasterID}")->row_array();
        $emp = $this->db->query("select * from srp_erp_crm_users WHERE userID IN ({$commaList})")->result_array();
        if ($emp) {
            $x = 0;
            foreach ($emp as $value) {
                $userexist = $this->db->query("SELECT userID  FROM `srp_erp_crm_usergroupdetails` where companyID = $companyid AND userID = {$value['userID']}")->row_array();
                if (!empty($userexist)) {
                    return array('w', 'Selected user already added to another group.');
                } else {
                    $data[$x]['groupMasterID'] = $groupMasterID;
                    $data[$x]['userID'] = $value['userID'];
                    $data[$x]['adminYN'] = $groupDetail['isAdmin'];
                    $data[$x]['empID'] = $value['employeeID'];
                    $data[$x]['employeeName'] = $value['employeeName'];
                    $data[$x]['companyID'] = $value['companyID'];
                    $data[$x]['createdUserGroup'] = $this->common_data['user_group'];
                    $data[$x]['createdPCID'] = $this->common_data['current_pc'];
                    $data[$x]['createdUserID'] = $this->common_data['current_userID'];
                    $data[$x]['createdDateTime'] = date('Y-m-d H:i:s');
                    $data[$x]['createdUserName'] = $this->common_data['current_user'];
                    $x++;
                }

            }
        }
        $this->db->insert_batch('srp_erp_crm_usergroupdetails', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'User save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'User saved successfully.');

        }
    }

    function delete_usergroupdetail()
    {
        $this->db->delete('srp_erp_crm_usergroupdetails', array('groupDetailID' => trim($this->input->post('groupDetailID') ?? '')));
        return true;
    }

    function save_pipleline()
    {
        $this->db->trans_start();
        $data['pipeLineName'] = $this->input->post('pipeLineName');
        $data['opportunityYN'] = $this->input->post('opportunityYN');
        $data['projectYN'] = $this->input->post('projectYN');
        $data['leadYN'] = $this->input->post('leadYN');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = date('Y-m-d H:i:s');
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_crm_pipeline', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Pipeline save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Pipeline saved successfully.');

        }

    }

    function save_piplelineStage()
    {
        $this->db->trans_start();
        $pipeLineDetailID = $this->input->post('pipeLineDetailID');

        $companyID = $this->common_data['company_data']['company_id'];
        $data['stageName'] = $this->input->post('stageName');
        $data['probability'] = $this->input->post('probability');
        $data['pipeLineID'] = $this->input->post('masterID');
        if (!isset($pipeLineDetailID)) {
            $sortqry = $this->db->query("select sortOrder from srp_erp_crm_pipelinedetails WHERE pipeLineID={$data['pipeLineID']}  order by pipeLineDetailID desc limit 1")->row_array();
            $data['sortOrder'] = (!empty($sortqry) ? $sortqry['sortOrder'] + 1 : 1);
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_crm_pipelinedetails', $data);
        } else {

            $sortOrder = $this->input->post('sortOrder');

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->update('srp_erp_crm_pipelinedetails', $data, array('pipeLineDetailID' => $pipeLineDetailID));

            $current_sortOrder = $this->db->query("select sortOrder,pipeLineDetailID from srp_erp_crm_pipelinedetails WHERE pipeLineDetailID={$pipeLineDetailID} AND companyID={$companyID}")->row_array();

            if ($sortOrder != $current_sortOrder['sortOrder']) {
                $all_sortOrder = $this->db->query("select * from srp_erp_crm_pipelinedetails WHERE pipeLineID={$data['pipeLineID']} AND companyID={$companyID} order by sortOrder asc")->result_array();
                if (!empty($all_sortOrder)) {
                    $keys = array_keys(array_column($all_sortOrder, 'sortOrder'), $sortOrder);
                    $new_array = array_map(function ($k) use ($all_sortOrder) {
                        return $all_sortOrder[$k];
                    }, $keys);

                    $detail[0]['pipeLineDetailID'] = $current_sortOrder['pipeLineDetailID'];
                    $detail[0]['sortOrder'] = $sortOrder;

                    $detail[1]['pipeLineDetailID'] = $new_array[0]['pipeLineDetailID'];
                    $detail[1]['sortOrder'] = $current_sortOrder['sortOrder'];
                    $this->db->update_batch('srp_erp_crm_pipelinedetails', $detail, 'pipeLineDetailID');

                }
            }


        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Pipeline stage save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Pipeline stage saved Successfully.');

        }
    }

    function delete_pipelineDetail()
    {
        $this->db->delete('`srp_erp_crm_pipelinedetails` ', array('pipeLineDetailID' => trim($this->input->post('pipeLineDetailID') ?? '')));
        return true;
    }

    function delete_pipeline()
    {
        $this->db->delete('`srp_erp_crm_pipelinedetails` ', array('pipeLineID' => trim($this->input->post('pipeLineID') ?? '')));
        $this->db->delete('srp_erp_crm_pipeline', array('pipeLineID' => trim($this->input->post('pipeLineID') ?? '')));
        return true;
    }

    function loadpipeline()
    {
        $pipeLineID = $this->input->post('masterID');
        $pipeline = $this->db->query("SELECT * FROM `srp_erp_crm_pipelinedetails` WHERE pipeLineID={$pipeLineID}")->result_array();

        $html = '';
        if (!empty($pipeline)) {
            $count = count($pipeline);
            $percentage = 100 / $count;
            $html .= '<div class="arrow-steps clearfix">';
            foreach ($pipeline as $pipe) {

                //$html .= '<div class="step"><span style="width:' . $percentage . '%">' . $pipe['stageName'] . ' ' . $pipe['probability'] . '% </span >';
                $html .= '<div class="step"><span>' . $pipe['stageName'] . ' ' . $pipe['probability'] . '% </span></div>';
            }
            $html .= '</div>';

        }
        return $html;

    }

    function load_taskRelated_fromLead()
    {

        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('leadID,CONCAT(firstName, \' \', lastName) as fullname');
        $this->db->where('leadID', $this->input->post('leadID'));
        $this->db->from('srp_erp_crm_leadmaster');
        return $this->db->get()->row_array();

    }

    function load_taskRelated_fromOpportunity()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('opportunityID,opportunityName as fullname');
        $this->db->where('opportunityID', $this->input->post('opportunityID'));
        $this->db->from('srp_erp_crm_opportunity');
        return $this->db->get()->row_array();

    }

    function show_opportunity_pipeline()
    {
        $pipelineID = $this->input->post('pipelineID');
        $pipelineStageID = $this->input->post('pipelineStageID');
        $projectid = $this->input->post('projectID');
        $html = '';
        if (!empty($pipelineID)) {
            $pipeline = $this->db->query("SELECT * FROM srp_erp_crm_pipelinedetails WHERE pipeLineID={$pipelineID}")->result_array();

            if (!empty($pipeline)) {
                if (!empty($projectid)) {
                    $count = count($pipeline);
                    $percentage = 100 / $count;
                    $html .= '<div class="arrow-steps clearfix">';
                    foreach ($pipeline as $pipe) {
                        $active = 'not-current';
                        if ($pipe['pipeLineDetailID'] == $pipelineStageID) {
                            $active = "current";
                        }
                        $test = trim($pipe['stageName'] ?? '');

                        $html .= '<div class="step ' . $active . '" style="margin-top:3px !important;" onclick="checkCurrentTabprojecttask(' . $projectid . ',' . $pipe['pipeLineDetailID'] . ',\'' . $test . '\')">
                        <span>' . $pipe['stageName'] . '</span>
                      </div>';
                    }
                } else {
                    $count = count($pipeline);
                    $percentage = 100 / $count;
                    $html .= '<div class="arrow-steps clearfix">';
                    foreach ($pipeline as $pipe) {
                        $active = 'not-current';
                        if ($pipe['pipeLineDetailID'] == $pipelineStageID) {
                            $active = "current";
                        }
                        $html .= '<div class="step ' . $active . '" style="margin-top:3px !important;"><span>' . $pipe['stageName'] . '</span></div>';
                    }
                }


                $html .= '</div>';


            }
        }
        return $html;

    }

    function srp_erp_save_campaignType()
    {
        $this->db->trans_start();
        $data['description'] = $this->input->post('campaignType');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $this->db->insert('srp_erp_crm_campaigntypes', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Campaign type save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Campaign type saved successfully.');

        }
    }

    function deleteCampaignType()
    {
        $this->db->delete('srp_erp_crm_campaigntypes', array('typeID' => trim($this->input->post('typeID') ?? '')));
        return true;
    }

    function create_document_status()
    {
        $companyid = current_companyID();
        $docid = $this->input->post('documentID');
        if ($docid == 5) {
            $IsClosestatus = $this->input->post('IsClosestatuslead');
        } else if ($docid == 4) {
            $IsClosestatus = $this->input->post('IsClosestatusopportunities');
        } else if ($docid == 9) {
            $IsClosestatus = $this->input->post('IsClosestatusproject');
        } else {
            $IsClosestatus = $this->input->post('IsClosestatus');
        }


        $this->db->trans_start();
        $data['documentID'] = $this->input->post('documentID');
        $data['description'] = $this->input->post('status');
        $data['statusColor'] = $this->input->post('color');
        $data['statusBackgroundColor'] = $this->input->post('backgroundColor');
        $data['statusType'] = $IsClosestatus;
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $statusID = $this->input->post('statusID');
        $documentstatusinsert = $this->db->query("SELECT statusType FROM `srp_erp_crm_status` WHERE companyID = '{$companyid}' AND statusType = '{$IsClosestatus}' AND documentID = '{$docid}'")->row_array();
        $documentstatusupdate = $this->db->query("SELECT statusType FROM `srp_erp_crm_status` WHERE companyID = '{$companyid}' AND statusType = '{$IsClosestatus}' AND documentID = '{$docid}' AND statusID != '{$statusID}' ")->row_array();


        if ($statusID == '') {
            if (($documentstatusinsert['statusType'] == 1)) {
                return array('e', 'Document status Is Already Closed.');
            }
            if (($documentstatusinsert['statusType'] == 2)) {
                return array('e', 'Document status Is Already Closed.');
            } else {
                if ($docid == 5) {
                    $dataleadstatus['companyID'] = $this->common_data['company_data']['company_id'];
                    $dataleadstatus['description'] = $this->input->post('status');
                    $this->db->insert('srp_erp_crm_leadstatus', $dataleadstatus);
                    $data['leadStatusID'] = $this->db->insert_id();
                }
                $this->db->insert('srp_erp_crm_status', $data);
            }


        } else {
            if (($documentstatusupdate['statusType'] == 1) || ($documentstatusupdate['statusType'] == 2)) {
                return array('e', 'Document status Is Already Closed.');
            } else {

                if ($docid == 5) {
                    $documentstatusupdateleadstaus = $this->db->query("SELECT statusType,leadStatusID FROM `srp_erp_crm_status` WHERE companyID = '{$companyid}' AND documentID = '{$docid}' AND statusID = '{$statusID}'")->row_array();
                    $dataleadstatusup['description'] = $this->input->post('status');
                    $this->db->update('srp_erp_crm_leadstatus', $dataleadstatusup, array('statusID' => $documentstatusupdateleadstaus['leadStatusID']));
                }

                $this->db->update('srp_erp_crm_status', $data, array('statusID' => $statusID));
            }

        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Document status save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Document status saved successfully.');

        }
    }

    function deleteDocumentStatus()
    {
        $statusid = $this->input->post('statusID');
        $companyid = current_companyID();

        $status = $this->db->query("SELECT statusID,leadStatusID,documentID FROM `srp_erp_crm_status` where companyID = $companyid AND statusID = $statusid")->row_array();
        if ($status['documentID'] == 5) {
            $this->db->delete('srp_erp_crm_leadstatus', array('statusID' => $status['leadStatusID']));
        }

        $this->db->delete('srp_erp_crm_status', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return array('status' => 0, 'message' => 'Document Status Deleted Successfully!');


        /*$this->db->delete('srp_erp_crm_status', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return true;*/
    }

    function srp_erp_save_leadStatus()
    {
        $this->db->trans_start();
        $status = $this->input->post('Status');
        $data['description'] = $this->input->post('leadStatus');

        if (!empty($status)) {
            $this->db->where('statusID', $status);
            $this->db->update('srp_erp_crm_leadstatus', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead status Update Failed.');
            } else {
                $this->db->trans_commit();
                return array('s', 'Lead status Updated Successfully.');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $this->db->insert('srp_erp_crm_leadstatus', $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Lead status save failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Lead status saved successfully.');

            }
        }


    }

    function deleteLeadStatus()
    {
        $this->db->delete('srp_erp_crm_leadstatus', array('statusID' => trim($this->input->post('statusID') ?? '')));
        return true;
    }

    function srp_erp_save_product()
    {
        $this->db->trans_start();
        $productid = $this->input->post('productid');
        $data['productName'] = $this->input->post('product');
        $data['subscriptionAmount'] = $this->input->post('subscriptionamount');
        $data['ImplementationAmount'] = $this->input->post('implementationamount');
        $data['otherAmount'] = $this->input->post('otheramount');
        $data['uomID'] = $this->input->post('defaultUnitOfMeasureID');
        $data['segmentID'] = $this->input->post('segmentID');

        //var_dump($data);
        
        if (!empty($productid)) {
            $this->db->where('productID', $productid);
            $this->db->update('srp_erp_crm_products', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Product Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Product Updated Successfully.');
            }
        } else {
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $this->db->insert('srp_erp_crm_products', $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Product save failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Product saved successfully.');

            }
        }
    }

    function delete_product()
    {
        $this->db->delete('srp_erp_crm_products', array('productID' => trim($this->input->post('productID') ?? '')));
        return true;
    }

    function create_category_status()
    {
        $this->db->trans_start();
        $data['documentID'] = $this->input->post('documentID');
        $data['description'] = $this->input->post('description');
        $data['textColor'] = $this->input->post('textColor');
        $data['backgroundColor'] = $this->input->post('backgroundColor');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $categoryID = $this->input->post('categoryID');
        if ($categoryID == '') {
            $this->db->insert('srp_erp_crm_categories', $data);
        } else {
            $this->db->update('srp_erp_crm_categories', $data, array('categoryID' => $categoryID));
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Category save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Category saved successfully.');

        }
    }

    function deleteCaegory()
    {
        $this->db->delete('srp_erp_crm_categories', array('categoryID' => trim($this->input->post('categoryID') ?? '')));
        return true;
    }

    function create_source()
    {
        $this->db->trans_start();
        $data['documentID'] = $this->input->post('documentID');
        $data['description'] = $this->input->post('description');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $sourceID = $this->input->post('sourceID');
        if ($sourceID == '') {
            $this->db->insert('srp_erp_crm_source', $data);
        } else {
            $this->db->update('srp_erp_crm_source', $data, array('sourceID' => $sourceID));
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Source save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Source saved successfully.');

        }
    }

    function delete_source()
    {
        $this->db->delete('srp_erp_crm_source', array('sourceID' => trim($this->input->post('sourceID') ?? '')));
        return true;
    }

    function save_sales_person_target()
    {

        $date_format_policy = date_format_policy();
        $companyID = $this->common_data['company_data']['company_id'];

        $projectID = trim($this->input->post('projectID') ?? '');
        $userID = trim($this->input->post('userID') ?? '');
        $dateFrom = trim($this->input->post('dateFrom') ?? '');
        $format_dateFrom = null;
        if (isset($dateFrom) && !empty($dateFrom)) {
            $format_dateFrom = input_format_date($dateFrom, $date_format_policy);
        }

        $dateTo = trim($this->input->post('dateTo') ?? '');
        $format_dateTo = null;
        if (isset($dateTo) && !empty($dateTo)) {
            $format_dateTo = input_format_date($dateTo, $date_format_policy);
        }

        $existPeriod = $this->db->query("SELECT * FROM srp_erp_crm_salestarget WHERE companyID = $companyID AND projectID = $projectID AND userID = $userID  AND (('$format_dateFrom' BETWEEN dateFrom
AND dateTo ) OR ('$format_dateTo' BETWEEN dateFrom AND dateTo ) OR ((dateFrom > '$format_dateFrom') AND (dateTo < '$format_dateTo')))")->row_array();

        if ($existPeriod) {
            return array('e', 'Already Period Added.');
        } else {
            $this->db->trans_start();
            $data['userID'] = $this->input->post('userID');
            $data['projectID'] = $this->input->post('projectID');
            $data['dateFrom'] = $format_dateFrom;
            $data['dateTo'] = $format_dateTo;
            $data['targetValue'] = trim($this->input->post('targetValue') ?? '');
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
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_salestarget', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sales Target save failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Sales Target saved successfully.');

            }
        }

    }

    function load_taskRelated_fromProject()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('projectID,projectName as fullname');
        $this->db->where('projectID', $this->input->post('projectID')[0]);
        $this->db->from('srp_erp_crm_project');
        return $this->db->get()->row_array();

    }

    function fetch_salesTarget_detail_table()
    {

        $companyid = $this->common_data['company_data']['company_id'];

        $userID = trim($this->input->post('userID') ?? '');
        $projectID = trim($this->input->post('projectID') ?? '');

        $where = "srp_erp_crm_salestarget.companyID = " . $companyid . " AND userID = '$userID' AND projectID = '$projectID' ";
        $convertFormat = convert_date_format_sql();
        $this->db->select('salesTargetID,targetValue,DATE_FORMAT(dateFrom,\'' . $convertFormat . '\') AS dateFrom, DATE_FORMAT(dateTo,\'' . $convertFormat . '\') AS dateTo,CurrencyCode');
        $this->db->where($where);
        $this->db->from('srp_erp_crm_salestarget');
        $this->db->join('srp_erp_currencymaster', 'srp_erp_crm_salestarget.transactionCurrencyID = srp_erp_currencymaster.currencyID');
        $this->db->order_by('srp_erp_crm_salestarget.salesTargetID', 'desc');
        $data['detail'] = $this->db->get()->result_array();
        return $data;
    }

    function delete_salesTarget_newPerson()
    {
        $this->db->delete('srp_erp_crm_salestarget', array('salesTargetID' => trim($this->input->post('salesTargetID') ?? '')));
        return true;
    }

    function send_email_campaign()
    {
        $campaignID = trim($this->input->post('campaignID') ?? '');
        $companyID = $this->common_data['company_data']['company_id'];
        $currentUser = $this->common_data['current_userID'];

        $this->db->select('name,emailDescription');
        $this->db->where('campaignID', $campaignID);
        $this->db->from('srp_erp_crm_campaignmaster');
        $masterRecord = $this->db->get()->row_array();

        $this->db->select('attendeesID,email,emailSend');
        $this->db->where('campaignID', $campaignID);
        $this->db->from('srp_erp_crm_attendees');
        $attendeesRecord = $this->db->get()->result_array();

        if (!empty($attendeesRecord)) {
            foreach ($attendeesRecord as $row) {
                if (!empty($row['email']) && ($row['emailSend'] == 0)) {
                    $config['mailtype'] = "html";
                    $config['protocol'] = 'smtp';
                    $config['smtp_host'] = 'smtp.sparkpostmail.com';
                    $config['smtp_user'] = 'SMTP_Injection';
                    $config['smtp_pass'] = '6d911d3e2ffe9faabc3af1e289eb067908deb1c5';
                    $config['smtp_crypto'] = 'tls';
                    $config['smtp_port'] = '587';
                    $condig['crlf'] = "\r\n";
                    $config['newline'] = "\r\n";
                    $this->load->library('email', $config);
                    if (hstGeras == 1) {
                        $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                    } else {
                        $this->email->from('noreply@redberylit.com', EMAIL_SYS_NAME);
                    }
                    $this->email->to($row['email']);
                    $this->email->subject($masterRecord['name']);
                    $this->email->message($masterRecord['emailDescription']);
                    $result = $this->email->send();

                    if ($result) {
                        $data['emailSend'] = 1;
                        $data['isAttended'] = 1;
                        $data['attendeeMarkedby'] = $currentUser;
                        $this->db->where('attendeesID', $row['attendeesID']);
                        $update = $this->db->update('srp_erp_crm_attendees', $data);
                    }
                }
            }
            return array('s', 'Emails Send Successfully');
        } else {
            return array('e', 'No Attendees Found');
        }
    }

    function save_quotation()
    {
        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $this->load->library('sequence');

        $companyID = $this->common_data['company_data']['company_id'];
        $quotationAutoID = trim($this->input->post('quotationAutoID') ?? '');

        $confirmedYN = trim($this->input->post('confirmedYN') ?? '');
        $documentDate = trim($this->input->post('documentDate') ?? '');
        $expiryDate = trim($this->input->post('expiryDate') ?? '');
        $relatedAutoIDs = $this->input->post('relatedAutoID');
        $relatedTo = $this->input->post('relatedTo');
        $relatedToSearch = $this->input->post('related_search');
        $linkedFromOrigin = $this->input->post('linkedFromOrigin');
        $opportunity = trim($this->input->post('opportunityID') ?? '');

        $contactpersonid = trim($this->input->post('contactPersonID') ?? '');

        $contractAutoID = trim($this->input->post('quotationid') ?? '');
        $productIDs = $this->input->post('productID');
        $estimatedAmount = $this->input->post('unitcost');
        $comment = $this->input->post('comment');
        $unitOfMeasure = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('Qty');
        $expectedDeliveryDate = $this->input->post('expectedDeliveryDate');
        $discountPercentage = $this->input->post('discount');
        $discountAmount = $this->input->post('discountamountcal');
        $totalcost = $this->input->post('totalcost');
        $itemAutoID = $this->input->post('itemautoid');
        $search = $this->input->post('search');
        $ItemTax = $this->input->post('ItemTax');
        $item_taxPercentage = $this->input->post('item_taxPercentage');
        $taxamt = $this->input->post('taxamt');
        $transactionCurrencyID = trim($this->input->post('transactionCurrencyID') ?? '');
        $unitOfMeasureID = $this->input->post('UnitOfMeasureID');
        $unitOfMeasuredescription = $this->input->post('UOM');
        $itemAutoID_policybased = $this->input->post('itemautoid');
      

        $this->db->trans_start();


        $format_documentDate = null;
        if (isset($documentDate) && !empty($documentDate)) {
            $format_documentDate = input_format_date($documentDate, $date_format_policy);
        }
        $format_expiryDate = null;
        if (isset($expiryDate) && !empty($expiryDate)) {
            $format_expiryDate = input_format_date($expiryDate, $date_format_policy);
        }
        if (isset($confirmedYN) && $confirmedYN == 1) {


            $detailexist = $this->db->query("select contractAutoID from srp_erp_crm_quotationdetails where companyID = '{$companyID}' AND contractAutoID = '{$quotationAutoID}'")->result_array();
            if (empty($detailexist)) {
                return array('w', 'There are no records to confirm this document!');
            } else {
                if($quotationAutoID) {
                    $this->db->select('quotationCode');
                    $this->db->where('quotationAutoID', $quotationAutoID);
                    $this->db->from('srp_erp_crm_quotation');
                    $qu_code = $this->db->get()->row_array();

                    $validate_code = validate_code_duplication($qu_code['quotationCode'], 'quotationCode', $quotationAutoID,'quotationAutoID', 'srp_erp_crm_quotation');
                    if(!empty($validate_code)) {
                        return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
                    }
                }

                $data["quotationDate"] = $format_documentDate;
                $data["quotationExpDate"] = $format_expiryDate;
                $data["referenceNo"] = trim($this->input->post('referenceNo') ?? '');
                $data["quotationNarration"] = trim($this->input->post('narration') ?? '');
                $data["termsAndConditions"] = trim($this->input->post('termsAndConditions') ?? '');
                $data["quotationPersonName"] = $this->input->post('contactPersonName');
                $data["quotationPersonNumber"] = trim($this->input->post('contactPersonNumber') ?? '');
                $data["quotationPersonID"] = $contactpersonid;
                $data["quotationPersonEmail"] = trim($this->input->post('contactpersonemail') ?? '');
                $data["customerID"] = trim($this->input->post('customerID') ?? '');
                $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
                $data['transactionExchangeRate'] = 1;
                $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
                $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
                $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
                $data['companyLocalExchangeRate'] = $default_currency['conversion'];
                $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
                $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
                $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
                $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
                $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];
                $data["confirmedYN"] = 1;
                $data["confirmedDate"] = $this->common_data['current_date'];
                $data["confirmedByEmpID"] = $this->common_data['current_userID'];
                $data["confirmedByName"] = $this->common_data['current_user'];

                if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                    $this->db->delete('srp_erp_crm_link', array('documentID' => 4, 'MasterAutoID' => $quotationAutoID));
                    foreach ($relatedAutoIDs as $key => $itemAutoID) {
                        $data_link['documentID'] = 4;
                        $data_link['MasterAutoID'] = $quotationAutoID;
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

                $this->db->delete('srp_erp_crm_quotationdetails', array('contractAutoID' => $quotationAutoID));
                foreach ($itemAutoID_policybased as $key => $itemautoID) {
                    //echo '<pre>';print_r($itemautoID);
                    if (!empty($itemautoID) || ($itemautoID = null)) {
                        $format_expectedDeliveryDate = null;
                        if (isset($expectedDeliveryDate[$key]) && !empty($expectedDeliveryDate[$key])) {
                            $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
                        }
                        if (($quantityRequested[$key] == '') || ($quantityRequested[$key] == null) || ($quantityRequested[$key] == 0)) {
                            return array('e', 'Qty field is required ');
                            exit();
                        }
                        if (($estimatedAmount[$key] == '') || ($estimatedAmount[$key] == null) || ($estimatedAmount[$key] == 0)) {
                            return array('e', 'Price field is required ');
                            exit();
                        }
    
                        $data_detail['contractAutoID'] = $quotationAutoID;
                       
                        if($productIDs[$key]!='' && $productIDs[$key]!=''){ 
                            $data_detail['productID'] = ($productIDs[$key]);
                        }else { 
                            $data_detail['productID'] =  null;
                        }
                        $data_detail['comment'] = $comment[$key];
                        $data_detail['requestedQty'] = $quantityRequested[$key];
                        $data_detail['unittransactionAmount'] = $estimatedAmount[$key];
                        $data_detail['discountPercentage'] = $discountPercentage[$key];
                        $data_detail['discountAmount'] = $discountAmount[$key];
                        $data_detail['transactionAmount'] = $totalcost[$key];
                        $data_detail['itemAutoID'] = $itemautoID;
    
                        $data_detail['taxMasterAutoID'] = $ItemTax[$key];
                        $data_detail['taxPercentage'] = $item_taxPercentage[$key];
                        $data_detail['taxAmount'] = $taxamt[$key];
                        $data_detail['unitOfMeasureID'] = $unitOfMeasureID[$key];
                        $data_detail['unitOfMeasure'] = $unitOfMeasuredescription[$key];
                        $reporting_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_reporting_currencyID']);
                        $companyReportingAmount = $totalcost[$key] / $reporting_currency['conversion'];
                        $data_detail['companyReportingAmount'] = round($companyReportingAmount, $reporting_currency['DecimalPlaces']);
    
                        $companyLocalAmount_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_default_currencyID']);
    
                        $companyLocalAmount = $totalcost[$key] / $companyLocalAmount_currency['conversion'];
                        $data_detail['companyLocalAmount'] = round($companyLocalAmount, $companyLocalAmount_currency['DecimalPlaces']);
    
                        $data_detail['expectedDeliveryDate'] = $format_expectedDeliveryDate;
                        $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                        $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                        $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_quotationdetails', $data_detail);
                    }
                }

                $url = $companyID;
                $companyidcomp = base64_encode(($url));
                $qut = base64_encode(($quotationAutoID));
                $organizationemail = $this->db->query("SELECT srp_erp_crm_quotation.customerID,quotationPersonName,srp_erp_crm_quotation.quotationPersonEmail as orgemail  FROM `srp_erp_crm_quotation` LEFT JOIN srp_erp_crm_organizations on srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID WHERE srp_erp_crm_quotation.companyID = '{$companyID}' AND quotationAutoID = '{$quotationAutoID}'")->row_array();
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                //$link = "'$_SERVER[HTTP_HOST]'/index.php/QuotationPortal/crm_quotation_view?comp=$companyidcomp&qut=$qut";
                $link2 = '/index.php/QuotationPortal/crm_quotation_view?comp='.$companyidcomp.'&qut='.$qut.'';
                $link = "$protocol$_SERVER[HTTP_HOST]".$link2;

                $emailsubject = "Estimate";
                $emailbody = '<div style="width: 80%;margin: auto;background-color:#fbfbfb;padding: 2%;font-family: sans-serif;"><b>To : ' . $organizationemail['quotationPersonName'] . '</b> <br><p>Hello</p><br><p>'. ucwords($this->common_data['company_data']['company_name']).' issues this Estimate Document detailed through the below link.NO ACTION IS REQUIRED but you can view the document at your discretion.</p><br><br><a href="'.$link.'"
>Click here to access the Estimate.</a><br><br><p>For more detail contact the Sales & Marketing department.</p><br><p>Thank You</p></div>';

                $config['wordwrap'] = TRUE;
                $config['protocol'] = 'smtp';
                $config['smtp_host'] = $this->config->item('email_smtp_host');
                $config['smtp_user'] = $this->config->item('email_smtp_username');
                $config['smtp_pass'] = $this->config->item('email_smtp_password');
                $config['smtp_crypto'] = 'tls';
                $config['smtp_port'] = '587';
                $config['SMTPOptions'] =  array (
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $config['crlf'] = "\r\n";
                $config['newline'] = "\r\n";
                $this->load->library('email', $config);
               
                $this->email->from($this->config->item('email_smtp_from'), EMAIL_SYS_NAME); 
                
                $this->email->set_mailtype('html');
                $this->email->subject($emailsubject);
                $this->email->message($emailbody);
                $this->email->to($organizationemail['orgemail']);
                $tmpResult = $this->email->send();
                if ($tmpResult) {
                    $this->email->clear(TRUE);
                } 
            }
        }

        $data["quotationDate"] = $format_documentDate;
        $data["quotationExpDate"] = $format_expiryDate;

        $data["referenceNo"] = trim($this->input->post('referenceNo') ?? '');
        $data["quotationNarration"] = trim($this->input->post('narration') ?? '');
        $data["termsAndConditions"] = trim($this->input->post('termsAndConditions') ?? '');
        $data["quotationPersonName"] = $this->input->post('contactPersonName');
        $data["quotationPersonNumber"] = trim($this->input->post('contactPersonNumber') ?? '');
        $data["quotationPersonID"] = $contactpersonid;
        $data["quotationPersonEmail"] = trim($this->input->post('contactpersonemail') ?? '');
        $data["customerID"] = trim($this->input->post('customerID') ?? '');
        $data['transactionCurrencyID'] = trim($this->input->post('transactionCurrencyID') ?? '');
        $data['transactionExchangeRate'] = 1;
        $data['transactionCurrencyDecimalPlaces'] = fetch_currency_desimal_by_id($data['transactionCurrencyID']);
        $data['companyLocalCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
        $default_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyLocalCurrencyID']);
        $data['companyLocalExchangeRate'] = $default_currency['conversion'];
        $data['companyLocalCurrencyDecimalPlaces'] = $default_currency['DecimalPlaces'];
        $data['companyReportingCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
        $reporting_currency = currency_conversionID($data['transactionCurrencyID'], $data['companyReportingCurrencyID']);
        $data['companyReportingExchangeRate'] = $reporting_currency['conversion'];
        $data['companyReportingCurrencyDecimalPlaces'] = $reporting_currency['DecimalPlaces'];

        if ($quotationAutoID) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];

            $this->db->where('quotationAutoID', $quotationAutoID);
            $this->db->update('srp_erp_crm_quotation', $data);

            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_crm_link', array('documentID' => 4, 'MasterAutoID' => $quotationAutoID));
                foreach ($relatedAutoIDs as $key => $itemAutoID) {
                    $data_link['documentID'] = 4;
                    $data_link['MasterAutoID'] = $quotationAutoID;
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
    
             
                $this->db->delete('srp_erp_crm_quotationdetails', array('contractAutoID' => $quotationAutoID));
                foreach ($itemAutoID_policybased as $key => $itemautoID) {
                    //echo '<pre>';print_r($itemautoID);
                    if (!empty($itemautoID) || ($itemautoID = null)) {
                        $format_expectedDeliveryDate = null;
                        if (isset($expectedDeliveryDate[$key]) && !empty($expectedDeliveryDate[$key])) {
                            $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
                        }
                        if (($quantityRequested[$key] == '') || ($quantityRequested[$key] == null) || ($quantityRequested[$key] == 0)) {
                            return array('e', 'Qty field is required ');
                            exit();
                        }
                        if (($estimatedAmount[$key] == '') || ($estimatedAmount[$key] == null) || ($estimatedAmount[$key] == 0)) {
                            return array('e', 'Price field is required ');
                            exit();
                        }
    
                        $data_detail['contractAutoID'] = $quotationAutoID;
                       
                        if($productIDs[$key]!='' && $productIDs[$key]!=''){ 
                            $data_detail['productID'] = ($productIDs[$key]);
                        }else { 
                            $data_detail['productID'] =  null;
                        }
                        $data_detail['comment'] = $comment[$key];
                        $data_detail['requestedQty'] = $quantityRequested[$key];
                        $data_detail['unittransactionAmount'] = $estimatedAmount[$key];
                        $data_detail['discountPercentage'] = $discountPercentage[$key];
                        $data_detail['discountAmount'] = $discountAmount[$key];
                        $data_detail['transactionAmount'] = $totalcost[$key];
                        $data_detail['itemAutoID'] = $itemautoID;
    
                        $data_detail['taxMasterAutoID'] = $ItemTax[$key];
                        $data_detail['taxPercentage'] = $item_taxPercentage[$key];
                        $data_detail['taxAmount'] = $taxamt[$key];
                        $data_detail['unitOfMeasureID'] = $unitOfMeasureID[$key];
                        $data_detail['unitOfMeasure'] = $unitOfMeasuredescription[$key];
                        $reporting_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_reporting_currencyID']);
                        $companyReportingAmount = $totalcost[$key] / $reporting_currency['conversion'];
                        $data_detail['companyReportingAmount'] = round($companyReportingAmount, $reporting_currency['DecimalPlaces']);
    
                        $companyLocalAmount_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_default_currencyID']);
    
                        $companyLocalAmount = $totalcost[$key] / $companyLocalAmount_currency['conversion'];
                        $data_detail['companyLocalAmount'] = round($companyLocalAmount, $companyLocalAmount_currency['DecimalPlaces']);
    
                        $data_detail['expectedDeliveryDate'] = $format_expectedDeliveryDate;
                        $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                        $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                        $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                        $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                        $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_crm_quotationdetails', $data_detail);
                    }
                }
              //  exit();
            
            
           
        

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Quotation Update Failed ' . $this->db->_error_message());

            } else {
                $this->db->trans_commit();
                return array('s', 'Quotation Updated Successfully.', $quotationAutoID);
            }
        } else {
            $quotationCode = $this->sequence->sequence_generator('CRM-QUO');
            $validate_code = $this->db->query("SELECT quotationCode AS Code FROM srp_erp_crm_quotation WHERE companyID = {$companyID} AND quotationCode LIKE '%{$quotationCode}%'")->row('Code');
            if(!empty($validate_code)) {
                return array('e', 'The document Code Already Exist.(' . $validate_code . ')');
            }
            $data["quotationCode"] = $quotationCode;
            $data["quotationtype"] = 'Quotation';
            $data["documentID"] = 'QUT';
            $data['opportunityID'] = trim($this->input->post('opportunityID') ?? '');
            $data['companyID'] = $companyID;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_crm_quotation', $data);
            $last_id = $this->db->insert_id();

            if (isset($relatedAutoIDs) && !empty($relatedAutoIDs)) {
                $this->db->delete('srp_erp_crm_link', array('documentID' => 4, 'MasterAutoID' => $last_id));
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

            $policy = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);

            $this->db->delete('srp_erp_crm_quotationdetails', array('contractAutoID' => $last_id));
            foreach ($itemAutoID_policybased as $key => $itemautoID) {
                if (!empty($itemautoID) || ($itemautoID = null)) {

                    $format_expectedDeliveryDate = null;
                    if (isset($expectedDeliveryDate[$key]) && !empty($expectedDeliveryDate[$key])) {
                        $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
                    }
                    if (($quantityRequested[$key] == '') || ($quantityRequested[$key] == null) || ($quantityRequested[$key] == 0)) {
                        return array('e', 'Qty field is required ');
                        exit();
                    }
                    if (($estimatedAmount[$key] == '') || ($estimatedAmount[$key] == null) || ($estimatedAmount[$key] == 0)) {
                        return array('e', 'Price field is required ');
                        exit();
                    }
                    $data_detail['contractAutoID'] = $last_id;

                    if($productIDs[$key]!='' && $productIDs[$key]!=''){ 
                        $data_detail['productID'] = ($productIDs[$key]);
                    }else { 
                        $data_detail['productID'] =  null;
                    }

                    $data_detail['comment'] = $comment[$key];
                    $data_detail['requestedQty'] = $quantityRequested[$key];
                    $data_detail['unittransactionAmount'] = $estimatedAmount[$key];
                    $data_detail['discountPercentage'] = $discountPercentage[$key];
                    $data_detail['discountAmount'] = $discountAmount[$key];
                    $data_detail['transactionAmount'] = $totalcost[$key];
                    $data_detail['itemAutoID'] = $itemAutoID[$key];
                    $data_detail['taxMasterAutoID'] = $ItemTax[$key];
                    $data_detail['taxPercentage'] = $item_taxPercentage[$key];
                    $data_detail['taxAmount'] = $taxamt[$key];
                    $data_detail['expectedDeliveryDate'] = $format_expectedDeliveryDate;
                    $reporting_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_reporting_currencyID']);
                    $companyReportingAmount = (int)$totalcost[$key] / $reporting_currency['conversion'];
                    $data_detail['companyReportingAmount'] = round($companyReportingAmount, $reporting_currency['DecimalPlaces']);
                    $data_detail['unitOfMeasureID'] = $unitOfMeasureID[$key];
                    $data_detail['unitOfMeasure'] = $unitOfMeasuredescription[$key];
                    $companyLocalAmount_currency = currency_conversionID($transactionCurrencyID, $this->common_data['company_data']['company_default_currencyID']);

                    $companyLocalAmount = (int)$totalcost[$key] / $companyLocalAmount_currency['conversion'];
                    $data_detail['companyLocalAmount'] = round($companyLocalAmount, $companyLocalAmount_currency['DecimalPlaces']);

                    $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_detail['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data_detail['modifiedPCID'] = $this->common_data['current_pc'];
                    $data_detail['modifiedUserID'] = $this->common_data['current_userID'];
                    $data_detail['modifiedUserName'] = $this->common_data['current_user'];
                    $data_detail['modifiedDateTime'] = $this->common_data['current_date'];
                    $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_quotationdetails', $data_detail);
                }
            }


            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Quotation Save Failed ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Quotation Added Successfully.', $last_id);

            }
        }
    }

    function get_estimate_quotation_link(){
                $companyID = $this->common_data['company_data']['company_id'];
                $quotationAutoID = trim($this->input->post('quotationAutoID') ?? '');

                $url = $companyID;
                $companyidcomp = base64_encode(($url));
                $qut = base64_encode(($quotationAutoID));
                $organizationemail = $this->db->query("SELECT srp_erp_crm_quotation.customerID,quotationPersonName,srp_erp_crm_quotation.quotationPersonEmail as orgemail  FROM `srp_erp_crm_quotation` LEFT JOIN srp_erp_crm_organizations on srp_erp_crm_organizations.organizationID = srp_erp_crm_quotation.customerID WHERE srp_erp_crm_quotation.companyID = '{$companyID}' AND quotationAutoID = '{$quotationAutoID}'")->row_array();
                $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

                //$link = "'$_SERVER[HTTP_HOST]'/index.php/QuotationPortal/crm_quotation_view?comp=$companyidcomp&qut=$qut";
                $link2 = '/index.php/QuotationPortal/crm_quotation_view?comp='.$companyidcomp.'&qut='.$qut.'';
                $link = "$protocol$_SERVER[HTTP_HOST]".$link2;               
                
                if (!empty($quotationAutoID)) {           
                    return $link;
                } else {
                    return array('e', 'Please contact our support team');
                }
    
    }

    function load_quotation_autoGeneratedID()
    {
        $quotationAutoID = trim($this->input->post('quotationAutoID') ?? '');

        $lastID = $this->db->query('SELECT quotationCode FROM srp_erp_crm_quotation WHERE quotationAutoID = ' . $quotationAutoID . '')->row_array();
        return array($lastID['quotationCode']);
    }

    function load_quotation_header()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $this->db->select('*,DATE_FORMAT(quotationDate,\'' . $convertFormat . '\') AS quotationDate,DATE_FORMAT(quotationExpDate,\'' . $convertFormat . '\') AS quotationExpDate');
        $this->db->where('quotationAutoID', $this->input->post('quotationAutoID'));
        $data = $this->db->get('srp_erp_crm_quotation')->row_array();


        $this->db->select('*');
        $this->db->where('documentID', 4);
        $this->db->where('companyID', $companyid);
        $this->db->where('MasterAutoID', $this->input->post('quotationAutoID'));
        $this->db->from('srp_erp_crm_link');
        $data['detail'] = $this->db->get()->result_array();
        return $data;

    }

    function fetch_productCode()
    {
        $dataArr = array();
        $dataArr2 = array();
       // check policy to list down the items from item mater or crm product
        $companyCode = $this->common_data['company_data']['company_code'];
        $dataArr2['query'] = 'test';
        $contractDetailsAutoID = $_GET['qutDetailID'];
        if(isset($contractDetailsAutoID) && !empty($contractDetailsAutoID) && ($contractDetailsAutoID!='undefined') ){ 
            $isRecExist = $this->db->query("SELECT
            COUNT(contractDetailsAutoID) as isrecExist 
            FROM
            `srp_erp_crm_quotationdetails`
            where 
            contractDetailsAutoID = $contractDetailsAutoID 
            AND (productID IS NULL OR productID = 0)
            AND itemAutoID IS NOT NULL")->row('isrecExist');
            $policy = ($isRecExist > 0 ? 1:(getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0));
        }else { 
            $policy = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);
        }
      
        $companyID = $this->common_data['company_data']['company_id'];
        $search_string = "%" . $_GET['query'] . "%";
        if($policy == 0){
            $data = $this->db->query('SELECT productID, productName AS "Match", srp_erp_crm_products.itemAutoID, uomID as defaultUnitOfMeasureID,
                                      CONCAT(srp_erp_unit_of_measure.UnitShortCode,\' | \',srp_erp_unit_of_measure.UnitDes) as defaultUnitOfMeasure 
                                      FROM srp_erp_crm_products 
                                      LEFT JOIN srp_erp_unit_of_measure on srp_erp_unit_of_measure.UnitID = srp_erp_crm_products.uomID 
                                      WHERE productName LIKE "' . $search_string . '" 
                                      AND srp_erp_crm_products.companyID = "' . $companyID . '"')->result_array();
            if (!empty($data)) {
                foreach ($data as $val) {
                    $dataArr[] = array('value' => $val["Match"], 'productID' => $val['productID'], 'itemAutoID' => $val['itemAutoID'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID']);
                }
            }

        }else{
            $data = $this->db->query('SELECT itemAutoID, CONCAT(IFNULL( itemDescription, "empty" ), " - ", IFNULL( itemSystemCode, "empty" ), " - ",
                                      IFNULL( partNo, "empty" ), " - ", IFNULL( seconeryItemCode, "empty" )) AS "Match",defaultUnitOfMeasureID,defaultUnitOfMeasure	
                                      FROM srp_erp_itemmaster
                                      WHERE (itemSystemCode LIKE "' . $search_string . '"  OR itemDescription LIKE "' . $search_string . '" 
                                      OR seconeryItemCode LIKE  "' . $search_string . '"  OR barcode LIKE  "' . $search_string . '" )	
                                      AND companyID = "' . $companyID . '" AND companyCode = "' . $companyCode . '" 
                                      AND isActive = "1" AND masterApprovedYN = "1" ')->result_array();
            if (!empty($data)) {
                foreach ($data as $val) {
                    $dataArr[] = array('value' => $val["Match"], 'productID' => '', 'itemAutoID' => $val['itemAutoID'], 'defaultUnitOfMeasure' => $val['defaultUnitOfMeasure'], 'defaultUnitOfMeasureID' => $val['defaultUnitOfMeasureID']);
                }
            }                         
        }
        
        $dataArr2['suggestions'] = $dataArr;
        return $dataArr2;
    }

    function save_quotation_detail()
    {
        $date_format_policy = date_format_policy();
        $policy = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);
        $companyID = $this->common_data['company_data']['company_id'];
        $contractAutoID = trim($this->input->post('quotationid') ?? '');
        $productIDs = $this->input->post('productID');
        $estimatedAmount = $this->input->post('unitcost');
        $comment = $this->input->post('comment');
        $unitOfMeasure = $this->input->post('UnitOfMeasureID');
        $quantityRequested = $this->input->post('Qty');
        $expectedDeliveryDate = $this->input->post('expectedDeliveryDate');
        $discountPercentage = $this->input->post('discount');
        $discountAmount = $this->input->post('discountamt');
        $totalcost = $this->input->post('totalcost');
        $itemAutoID = $this->input->post('itemautoid');

        $this->db->trans_start();

        $this->db->delete('srp_erp_crm_quotationdetails', array('contractAutoID' => $contractAutoID));
        if($policy == 0){
            foreach ($productIDs as $key => $productID) {
                /*   $format_expectedDeliveryDate = null;
                   if (isset($expectedDeliveryDate[$key]) && !empty($expectedDeliveryDate[$key])) {
                       $format_expectedDeliveryDate = input_format_date($expectedDeliveryDate[$key], $date_format_policy);
                   }*/
                $data['contractAutoID'] = $contractAutoID;
                $data['productID'] = $productID;
                $data['comment'] = $comment[$key];
                $data['requestedQty'] = $quantityRequested[$key];
                $data['unittransactionAmount'] = $estimatedAmount[$key];
                $data['discountPercentage'] = $discountPercentage[$key];
                $data['discountAmount'] = $discountAmount[$key];
                $data['transactionAmount'] = $totalcost[$key];
                $data['itemAutoID'] = $itemAutoID[$key];
    
    
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_quotationdetails', $data);
            }
        }else{
            foreach ($itemAutoID as $key => $itemAutoIDs) {

                $data['contractAutoID'] = $contractAutoID;
                $data['productID'] = '';
                $data['comment'] = $comment[$key];
                $data['requestedQty'] = $quantityRequested[$key];
                $data['unittransactionAmount'] = $estimatedAmount[$key];
                $data['discountPercentage'] = $discountPercentage[$key];
                $data['discountAmount'] = $discountAmount[$key];
                $data['transactionAmount'] = $totalcost[$key];
                $data['itemAutoID'] = $itemAutoIDs[$key];
    
    
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['companyCode'] = $this->common_data['company_data']['company_code'];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_quotationdetails', $data);
            }
        }
        

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Quotation Details :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Quotation Details :  Saved Successfully.');
        }

    }

    function delete_quotation_detail()
    {
        $contractDetailsAutoID = trim($this->input->post('contractDetailsAutoID') ?? '');
        $this->db->delete('srp_erp_crm_quotationdetails', array('contractDetailsAutoID' => $contractDetailsAutoID));
        return true;
    }

    function delete_crm_quotation()
    {
        $quotationAutoID = trim($this->input->post('quotationAutoID') ?? '');
        $this->db->delete('srp_erp_crm_quotation', array('quotationAutoID' => $quotationAutoID));
        $this->db->delete('srp_erp_crm_quotationdetails', array('contractAutoID' => $quotationAutoID));
        return true;
    }

    function load_opprtunity_BaseOrganization()
    {
        $opportunityID = trim($this->input->post('opportunityID') ?? '');

        $lastID = $this->db->query('SELECT relatedDocumentMasterID FROM srp_erp_crm_link WHERE relatedDocumentID = 8 AND MasterAutoID = ' . $opportunityID . '')->row_array();
        return array($lastID['relatedDocumentMasterID']);
    }

    function delete_master_notes_allDocuments()
    {
        $this->db->where('notesID', $this->input->post('notesID'));
        $results = $this->db->delete('srp_erp_crm_contactnotes');
        return true;
    }

    function edit_master_notes_allDocuments()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $this->db->select('notesID,description');
        $this->db->where('notesID', trim($this->input->post('notesID') ?? ''));
        $this->db->where('companyID', $companyID);
        $this->db->from('srp_erp_crm_contactnotes');
        return $this->db->get()->row_array();

    }

    function convert_attendees_to_lead()
    {
        $companyID = $this->common_data['company_data']['company_id'];

        $selectedItemsSync = $this->input->post('selectedItemsSync');

        $this->db->trans_start();
        foreach ($selectedItemsSync as $attendee) {
            $campainAttendeeDetail = $this->db->query("SELECT * FROM srp_erp_crm_attendees WHERE attendeesID = {$attendee} AND convertedToLead = 0")->row_array();
            if (!empty($campainAttendeeDetail)) {
                $data['documentSystemCode'] = $this->sequence->sequence_generator('CRM-LEAD');
                $data['prefix'] = $campainAttendeeDetail['prefix'];
                $data['firstName'] = $campainAttendeeDetail['firstName'];
                $data['lastName'] = $campainAttendeeDetail['lastName'];
                $data['title'] = '';
                $data['organization'] = $campainAttendeeDetail['organization'];
                $data['linkedorganizationID'] = '';
                $data['statusID'] = '';
                $data['email'] = $campainAttendeeDetail['email'];
                $data['phoneMobile'] = $campainAttendeeDetail['phoneMobile'];
                $data['phoneHome'] = $campainAttendeeDetail['phoneHome'];
                $data['fax'] = $campainAttendeeDetail['fax'];
                $data['industry'] = $campainAttendeeDetail['department'];
                $data['postalCode'] = $campainAttendeeDetail['postalCode'];
                $data['city'] = $campainAttendeeDetail['city'];
                $data['state'] = $campainAttendeeDetail['state'];
                $data['countryID'] = $campainAttendeeDetail['countryID'];
                $data['address'] = $campainAttendeeDetail['address'];
                $data['campaignID'] = $campainAttendeeDetail['campaignID'];
                $data['companyID'] = $companyID;
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_crm_leadmaster', $data);
                $lead_last_id = $this->db->insert_id();

                if ($lead_last_id) {
                    $permission_master['documentID'] = 5;
                    $permission_master['documentAutoID'] = $lead_last_id;
                    $permission_master['permissionID'] = 1;
                    $permission_master['companyID'] = $companyID;
                    $permission_master['createdUserGroup'] = $this->common_data['user_group'];
                    $permission_master['createdPCID'] = $this->common_data['current_pc'];
                    $permission_master['createdUserID'] = $this->common_data['current_userID'];
                    $permission_master['createdUserName'] = $this->common_data['current_user'];
                    $permission_master['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_crm_documentpermission', $permission_master);
                }

                $update_attendees['convertedToLead'] = 1;
                $this->db->where('attendeesID', $attendee);
                $this->db->update('srp_erp_crm_attendees', $update_attendees);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Error in Lead Convert');
        } else {
            $this->db->trans_commit();
            return array('s', 'Attendees Successfully Converted to Lead.');

        }
    }

    function fetch_lead_status()
    {
        $companyid = current_companyID();
        $statusid = $this->input->post('statusID');
        $data = $this->db->query("SELECT * FROM `srp_erp_crm_leadstatus` where companyID = $companyid And statusID = '{$statusid}'")->row_array();
        return $data;
    }

    function fetch_product_details() 
    {
        $companyid = current_companyID();
        $productid = $this->input->post('productID');
        $data = $this->db->query("SELECT * FROM `srp_erp_crm_products` where companyID = $companyid and productID = $productid")->row_array();
        return $data;
    }

    function fetch_pipelicename()
    {
        $companyid = $this->common_data['company_data']['company_id'];
        $data = $this->db->query("Select pipeLineID,pipeLineName from srp_erp_crm_pipeline where companyID = $companyid AND projectYN = 1")->result_array();

        return $data;
    }

    function AddSubTaskDetail()
    {
        $date_format_policy = date_format_policy();
        $taskdescription = $this->input->post('Taskdescription');
        $esttaskstartdate = $this->input->post('estsubtaskdate');
        $esttaskenddate = $this->input->post('estsubtaskdateend');
        $indays = $this->input->post('indays');
        $inhrs = $this->input->post('inhrs');
        $inmins = $this->input->post('inmns');
        $assignTos = $this->input->post('assign');
        $Taskid = $this->input->post('Taskid');
        $companyid = current_companyID();
        $crmtask = $this->db->query("SELECT *,DATE_FORMAT(starDate, '%Y-%m-%d') as crmtaskstartdate,DATE_FORMAT(DueDate, '%Y-%m-%d') as crmtasksdeuedate FROM `srp_erp_crm_task` WHERE companyID = '{$companyid}' AND taskID = '{$Taskid}'")->row_array();
        $Taskdescription = $this->input->post('Taskdescription');
        foreach ($Taskdescription as $key => $val) {
            $startdate = input_format_date(trim($this->input->post('estsubtaskdate')[$key]), $date_format_policy);
            $estsubtaskdateend = input_format_date(trim($this->input->post('estsubtaskdateend')[$key]), $date_format_policy);
            if ($startdate > $estsubtaskdateend) {
                return array('e', 'Est. Start Date cannot be greater than Est. End Date');
                exit();
            }

            if ($startdate < $crmtask['crmtaskstartdate']) {
                return array('e', 'Est. Start Date cannot be less than Task Start Date.');
                exit();
            }

            
            if ($startdate > $crmtask['crmtasksdeuedate']) {
                return array('e', 'Est. Start Date cannot be greater than Task Due Date.');
                exit();
            }

            if ($estsubtaskdateend > $crmtask['crmtasksdeuedate']) {
                return array('e', 'Estimated End Date Cannot be greater than Task Due Date.');
                exit();
            }
            if ($startdate > $estsubtaskdateend) {
                return array('e', 'Est. Start Date cannot be greater than Est. End Date');
                exit();
            }

        }

        foreach ($taskdescription as $key => $val) {


            $data['taskDescription'] = $val;
            $data['taskID'] = $Taskid;
            $data['startDate'] = input_format_date($esttaskstartdate[$key], $date_format_policy);
            $data['endDate'] = input_format_date($esttaskenddate[$key], $date_format_policy);
            $data['estimatedDays'] = $indays[$key];
            $data['estimatedHours'] = ($inhrs[$key] * 60) + ($inmins[$key]);
            $data['companyID'] = $companyid;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_crm_subtasks', $data);
            $last_id_sub = $this->db->insert_id();
            $assinees = ((explode(',', $assignTos[$key])));
            foreach ($assinees as $val) {
                $datasub['empID'] = $val;
                $datasub['documentID'] = 10;
                $datasub['MasterAutoID'] = $last_id_sub;
                $datasub['companyID'] = $companyid;
                $datasub['createdUserGroup'] = $this->common_data['user_group'];
                $datasub['createdPCID'] = $this->common_data['current_pc'];
                $datasub['createdUserID'] = $this->common_data['current_userID'];
                $datasub['createdDateTime'] = $this->common_data['current_date'];
                $datasub['createdUserName'] = $this->common_data['current_user'];
                $this->db->insert('srp_erp_crm_assignees', $datasub);
            }
        }


        $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_crm_subtasks where taskID = $Taskid ANd companyID  = $companyid")->row_array();
        $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_crm_subtasks where taskID = $Taskid ANd companyID  = $companyid AND `status` = 2")->row_array();

        if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
            $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
        } else {
            $subtaskpercentage = 0;
        }
        $dataupdatetask['progress'] = $subtaskpercentage;
        $this->db->where('taskID', $Taskid);
        $this->db->update('srp_erp_crm_task', $dataupdatetask);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sub Task :  Save Failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Added Successfully.', $last_id_sub,$dataupdatetask['progress']);
        }
    }

    function save_sub_task_Enable()
    {
        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();
        $this->db->trans_start();
        $subtask = $this->db->query("SELECT `status` FROM `srp_erp_crm_subtasks` where  companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();
        if ($subtask['status'] == 2) {
            return array('w', 'Sub Task Already Completed, cannot be start.');
        } else {
            $data['empID'] = $currentuser;
            $data['subTaskID'] = $subtaskid;
            $data['taskID'] = $taskid;
            $data['status'] = 1;
            $data['startDatetime'] = format_date_mysql_datetime();
            $data['companyID'] = $companyid;
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_crm_subtasksessions', $data);
            $last_id = $this->db->insert_id();

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Sub task failed' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Sub Task Started successfully.', $last_id);

            }
        }


    }

    function stop_subtask()
    {
        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $subtasksessionID = trim($this->input->post('subtasksession') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();

        $dateTimestoped = format_date_mysql_datetime();


        /* $timestartintominutes = explode('-', $subtaskstarttime['starttime']);
         $convertedstarttimeminuts =  (($timestartintominutes[0]*60) + ($timestartintominutes[1]));


         $dateTimestopedTime = explode(':',$dateTimestoped[1]);
         $stopedtimeminutes = (($dateTimestopedTime[0]*60) + ($dateTimestopedTime[1]));*/

        /*  print_r($subtaskstarttime['starttime']);
          exit();*/
        $totaltimespentminutes = /*($stopedtimeminutes - $convertedstarttimeminuts);*/

        $timespent = $this->db->query("SELECT TIMESTAMPDIFF( MINUTE, startDatetime,'$dateTimestoped') AS timeminutes FROM srp_erp_crm_subtasksessions subtasksession WHERE subtasksession.companyID = '{$companyid}'  AND subtasksession.taskID = '{$taskid}' AND subtasksession.subTaskID = '{$subtaskid}' AND subtasksession.sessionID = '{$subtasksessionID}' ")->row_array();
        $this->db->trans_start();
        $data['empID'] = $currentuser;
        $data['subTaskID'] = $subtaskid;
        $data['taskID'] = $taskid;
        $data['timeSpent'] = $timespent['timeminutes'];
        $data['status'] = 2;
        $data['endDateTime'] = format_date_mysql_datetime();
        $data['companyID'] = $companyid;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $data['createdUserName'] = $this->common_data['current_user'];

        $this->db->where('taskID', $taskid);
        $this->db->where('subTaskID', $subtaskid);
        $this->db->where('companyID', $companyid);
        $this->db->where('sessionID', $subtasksessionID);
        $subtaskstarttime =
            $this->db->update('srp_erp_crm_subtasksessions', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Sub task failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Stoped successfully.');

        }
    }

    function load_subtask_details()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $subtaskid = $this->input->post('subtaskid');
        $empid = current_userID();
        $assignpermission = $this->db->query("SELECT empID FROM `srp_erp_crm_assignees` WHERE companyID = '{$companyID}' AND documentID = 10 AND MasterAutoID = '{$subtaskid}' AND empID = '{$empid}'")->row_array();
        if (!empty($assignpermission)) {
            $data['assignpermission'] = 1;
        } else {
            $data['assignpermission'] = 0;
        }

        return $data;
    }

    function load_subtsk_status()
    {
        $subtaskid = trim($this->input->post('subTaskID') ?? '');
        $taskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT `status` FROM `srp_erp_crm_subtasks` where companyID = $companyid ANd subTaskID = $subtaskid And taskID = $taskid")->row_array();
        return $data;
    }

    function save_subTask_status()
    {
        $this->db->trans_start();
        $subtaskid = trim($this->input->post('subtaskID') ?? '');
        $taskid = trim($this->input->post('TaskID') ?? '');
        $companyid = current_companyID();
        $statussubtask = trim($this->input->post('statussubtask') ?? '');

        $this->db->select('sessionID');
        $this->db->where('companyID', $companyid);
        $this->db->where('subTaskID', $subtaskid);
        $this->db->where('taskID', $taskid);
        $this->db->where('status', 1);
        $this->db->from('srp_erp_crm_subtasksessions');
        $record = $this->db->get()->result_array();
        if (!empty($record)) {
            return array('w', 'stop subtask session before change the status of this sub task!');
        } else {
            $data['status'] = $statussubtask;

            $this->db->where('companyID', $companyid);
            $this->db->where('subTaskID', $subtaskid);
            $this->db->where('taskID', $taskid);
            $this->db->update('srp_erp_crm_subtasks', $data);


            $subtaskcount = $this->db->query("select COUNT(taskID) as totaltask from srp_erp_crm_subtasks where taskID = $taskid ANd companyID  = $companyid")->row_array();
            $totaltaskclosed = $this->db->query("select  COUNT(taskID) as completedtask from srp_erp_crm_subtasks where taskID = $taskid ANd companyID  = $companyid AND `status` = 2")->row_array();

            if (($subtaskcount['totaltask'] != 0) && $totaltaskclosed['completedtask'] != 0) {
                $subtaskpercentage = ($totaltaskclosed['completedtask'] / $subtaskcount['totaltask']) * 100;
            } else {
                $subtaskpercentage = 0;
            }
            $dataupdatetask['progress'] = $subtaskpercentage;
            $this->db->where('taskID', $taskid);
            $this->db->update('srp_erp_crm_task', $dataupdatetask);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', "Status Update Failed." . $this->db->_error_message());
            } else {
                return array('s', 'Status updated successfully!.', $dataupdatetask['progress']);
            }
        }
    }

    function save_sub_task_comment()
    {

        /*   $time = new DateTime();
           echo $time->format("Y-m-d H:i:s P");
           $time = new DateTime(null, new DateTimeZone('Europe/London'));
           echo $time->format("Y-m-d H:i:s P");*/

        $subtaskid = trim($this->input->post('subtaskid') ?? '');
        $taskid = trim($this->input->post('taskid') ?? '');
        $commentsubtask = trim($this->input->post('commentsubtask') ?? '');
        $companyid = current_companyID();
        $currentuser = current_userID();
        $data['subTaskID'] = $subtaskid;
        $data['taskID'] = $taskid;
        $data['empID'] = $currentuser;
        $data['chatDescription'] = $commentsubtask;
        $data['companyID'] = $companyid;
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = current_date();
        $data['createdUserName'] = $this->common_data['current_user'];
        $this->db->insert('srp_erp_crm_chat', $data);
        $last_id = $this->db->insert_id();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            //return array('e', 'Sub task failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            //return array('s', 'Sub Task Started successfully.',$last_id);

        }

    }

    function start_date_est_date_validation()
    {
        $date_format_policy = date_format_policy();
        $taskid = trim($this->input->post('taskID') ?? '');
        $startDate = $this->input->post('startDate');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $startDateconvert = input_format_date($startDate, $date_format_policy);


        $startdatevalidation = $this->db->query("select DATE_FORMAT(task.starDate ,'%Y-%m-%d') as startdatetASK from srp_erp_crm_task task where companyID = '{$companyid}' And taskID = '{$taskid}' ")->row_array();

        if ($startDateconvert >= $startdatevalidation['startdatetASK']) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function end_date_est_date_validation()
    {
        $date_format_policy = date_format_policy();
        $taskid = trim($this->input->post('taskID') ?? '');
        $endstartDate = $this->input->post('endstartDate');
        $companyid = current_companyID();
        $convertFormat = convert_date_format_sql();
        $endDateconvert = input_format_date($endstartDate, $date_format_policy);

        $enddatevalidation = $this->db->query("select DATE_FORMAT( task.DueDate, '%Y-%m-%d') AS duedatetASK  from srp_erp_crm_task task where companyID = '{$companyid}' And taskID = '{$taskid}'")->row_array();
        if ($endDateconvert <= $enddatevalidation['duedatetASK']) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function crm_task_close_ischk()
    {
        $taskid = trim($this->input->post('taskID') ?? '');
        $companyid = current_companyID();
        $subtaskexist = $this->db->query("SELECT subTaskID FROM srp_erp_crm_subtasks subtask left join srp_erp_crm_task task on task.taskID = subtask.taskID WHERE subtask.companyID = '{$companyid}' AND subtask.taskID = '{$taskid}' And subtask.`status` != 2
	AND task.isSubTaskEnabled = 1")->result_array();
        if (!empty($subtaskexist)) {
            $data = 1;
        } else {
            $data = 0;
        }
        return $data;
    }

    function crm_is_subtask_exist()
    {
        $taskid = trim($this->input->post('taskID') ?? '');
        $statusid = trim($this->input->post('statusid') ?? '');
        $companyid = current_companyID();

        $data['closedstatus'] = $this->db->query("SELECT statusid,statusType FROM `srp_erp_crm_status` where statusID = '{$statusid}' AND companyID  = '{$companyid}' ")->row_array();


        $issubtaskexist = $this->db->query("SELECT subTaskID FROM srp_erp_crm_subtasks subtask WHERE subtask.companyID = '{$companyid}' AND subtask.taskID = '{$taskid}' And status!=2")->result_array();

        if (!empty($issubtaskexist) && $data['closedstatus']['statusType'] == 1) {
            $data['isexist'] = 1;
        } else {
            $data['isexist'] = 0;
        }
        return $data;


    }

    function get_crm_task_subtask_rpt()
    {
        $companyID = current_companyID();
        $currentuserID = current_userID();
        $isGroupAdmin = crm_isGroupAdmin();
        $issuperadmin = crm_isSuperAdmin();
        $date_format_policy = date_format_policy();

        $dateto = $this->input->post('EndDate');
        $datefrom = $this->input->post('StartDate');
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);

        $date = "";
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( DATE_FORMAT( srp_erp_crm_task.starDate, '%Y-%m-%d') >= '" . $datefromconvert . "' AND DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ) <= '" . $datetoconvert . "')";
        }


        $permissiontype = $this->input->post('permissiontype');
        $where_task1 = " ";
        $where_task2 = " ";
        $where_task3 = " ";
        $where_task4 = " ";
        $where_all_task = " ";
        $categorytaskassignee = $this->input->post('catergoryid');
        $taskstatus = $this->input->post('status');
        $masterEmployee = $this->input->post('employee');
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $employeeID = join(',', $masterEmployee);
        }
        $filterAssigneesID = '';
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $filterAssigneesID = " AND srp_erp_crm_assignees.empID IN ($employeeID)";
        }
        $where_task_cat = '';
        if ((isset($categorytaskassignee) && !empty($categorytaskassignee))) {
            $where_task_cat = " AND srp_erp_crm_task.categoryID = $categorytaskassignee";
        }
        $where_task_status = '';
        if ((isset($taskstatus) && !empty($taskstatus))) {
            $where_task_status = " AND srp_erp_crm_task.status = $taskstatus";
        }

        if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1) {
            if (isset($masterEmployee) && empty($masterEmployee)) {
                $qry = "SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID Where  srp_erp_crm_task.companyID = '{$companyID}' $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID ORDER BY srp_erp_crm_task.taskID DESC";
            } else {
                if (!empty($employeeID)) {
                    $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                    $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_task.companyID = '{$companyID}' $filterAssigneesID";
                }
                $qry = "SELECT * FROM (SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task1 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task2 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_task3 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID union SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_task4 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID) Task ORDER BY
Task.taskID DESC";
            }

        } else {
            if ($permissiontype == 1) {
                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}'";

                $where_all_task = "UNION SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID WHERE srp_erp_crm_task.companyID = $companyID AND srp_erp_crm_task.createdUserID = $currentuserID $where_task_cat $where_task_status $date GROUP BY taskID";

            } else if ($permissiontype == 2) {
                $where_task1 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task2 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task3 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

                $where_task4 = "WHERE srp_erp_crm_documentpermission.documentID = 2 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_task.companyID = '{$companyID}' AND srp_erp_crm_assignees.empID = " . $currentuserID . " ";

            }
            $qry = "SELECT * FROM (SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task1 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID $where_task2 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID UNION SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_task3 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID union SELECT srp_erp_crm_task.documentSystemCode,srp_erp_crm_task.taskID,srp_erp_crm_task.categoryID,srp_erp_crm_task.SUBJECT as taskdescription,srp_erp_crm_categories.description as categoryDescription,textColor,backGroundColor,visibility,DATE_FORMAT(starDate,'%Y-%m-%d') AS starDate,DATE_FORMAT(DueDate,'%Y-%m-%d') AS DueDate,srp_erp_crm_status.description as statusDescription,srp_erp_crm_assignees.empID,srp_erp_crm_task.progress,srp_erp_crm_task.status,srp_erp_crm_task.Priority,isClosed,CASE WHEN srp_erp_crm_task.Priority = \"1\" THEN \"Low\" WHEN srp_erp_crm_task.Priority = \"2\" THEN \"Medium\" WHEN srp_erp_crm_task.Priority = \"3\" THEN \"High\" END PriorityTask,DATEDIFF(DATE_FORMAT( srp_erp_crm_task.DueDate, '%Y-%m-%d' ),CURDATE()) as datedifferencetask  FROM srp_erp_crm_task LEFT JOIN srp_erp_crm_categories ON srp_erp_crm_categories.categoryID = srp_erp_crm_task.categoryID LEFT JOIN srp_erp_crm_status ON srp_erp_crm_status.statusID = srp_erp_crm_task.status LEFT JOIN srp_erp_crm_assignees ON srp_erp_crm_assignees.MasterAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_task.taskID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_task4 $where_task_cat $where_task_status $date GROUP BY srp_erp_crm_task.taskID $where_all_task) Task ORDER BY
Task.taskID DESC";
        }


        $output = $this->db->query($qry)->result_array();
        return $output;
    }

    function fetch_tasks_employee_detailsubtask()
    {
        $this->db->select('AssingeeID,empID,MasterAutoID');
        $this->db->from('srp_erp_crm_assignees');
        $this->db->where('MasterAutoID', $this->input->post('MasterAutoID'));
        $this->db->where('documentID', 10);
        return $this->db->get()->result_array();
    }

    function update_subtaask_detail()
    {
        $this->db->trans_start();

        $subtaskAutoid = $this->input->post('subtaskAutoid');
        $taskautoid = $this->input->post('taskautoid');
        $Taskdescription = $this->input->post('Taskdescriptionedit');
        $indays = $this->input->post('indaysedit');
        $inhrs = $this->input->post('inhrsedit');
        $inmns = $this->input->post('inmnsedit');
        $employeessubtask = $this->input->post('employeessubtaskedit');
        $companyid = current_companyID();

        $estsubtaskdate = $this->input->post('estsubtaskdateedit');
        $estsubtaskdateend = $this->input->post('estsubtaskdateendedit');
        $date_format_policy = date_format_policy();
        $format_estsubtaskdate = null;
        $crmtask = $this->db->query("SELECT *,DATE_FORMAT(starDate, '%Y-%m-%d') as crmtaskstartdate,DATE_FORMAT(DueDate, '%Y-%m-%d') as crmtasksdeuedate FROM `srp_erp_crm_task` WHERE companyID = '{$companyid}' AND taskID = '{$taskautoid}'")->row_array();
        if (isset($estsubtaskdate) && !empty($estsubtaskdate)) {
            $format_estsubtaskdate = input_format_date($estsubtaskdate, $date_format_policy);
        }
        $format_estsubtaskdateend = null;
        if (isset($estsubtaskdate) && !empty($estsubtaskdateend)) {
            $format_estsubtaskdateend = input_format_date($estsubtaskdateend, $date_format_policy);
        }


        $startdate = input_format_date(trim($this->input->post('estsubtaskdateedit') ?? ''), $date_format_policy);
        $estsubtaskdateend = input_format_date(trim($this->input->post('estsubtaskdateendedit') ?? ''), $date_format_policy);
        if ($startdate > $estsubtaskdateend) {
            return array('e', 'Est. Start Date cannot be greater than Est. End Date');
            exit();
        }

        if ($crmtask['crmtaskstartdate'] > $startdate) {
            return array('e', 'Est. Start Date cannot be less than Task Start Date.');
            exit();
        }


        if ($startdate > $crmtask['crmtasksdeuedate']) {
            return array('e', 'Est. Start Date cannot be greater than Task Due Date.');
            exit();
        }

        if ($estsubtaskdateend > $crmtask['crmtasksdeuedate']) {
            return array('e', 'Estimated End Date Cannot be greater than Task Due Date.');
            exit();
        }
        if ($startdate > $estsubtaskdateend) {
            return array('e', 'Est. Start Date cannot be greater than Est. End Date');
            exit();
        }


        $data['taskDescription'] = $Taskdescription;
        $data['startDate'] = $format_estsubtaskdate;
        $data['endDate'] = $format_estsubtaskdateend;
        $data['estimatedDays'] = $indays;
        $data['estimatedHours'] = ($inhrs * 60) + ($inmns);
        $data['companyID'] = $companyid;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['modifiedUserName'] = $this->common_data['current_user'];

        $this->db->where('MasterAutoID', $subtaskAutoid);
        $this->db->where('documentID', 10);
        $this->db->delete('srp_erp_crm_assignees');

        foreach ($employeessubtask as $val) {
            $datasub['empID'] = $val;
            $datasub['documentID'] = 10;
            $datasub['MasterAutoID'] = $subtaskAutoid;
            $datasub['companyID'] = $companyid;
            $datasub['createdUserGroup'] = $this->common_data['user_group'];
            $datasub['createdPCID'] = $this->common_data['current_pc'];
            $datasub['createdUserID'] = $this->common_data['current_userID'];
            $datasub['createdDateTime'] = $this->common_data['current_date'];
            $datasub['createdUserName'] = $this->common_data['current_user'];
            $this->db->insert('srp_erp_crm_assignees', $datasub);
        }
        $this->db->where('taskID', $taskautoid);
        $this->db->where('subTaskID', $subtaskAutoid);
        $this->db->update('srp_erp_crm_subtasks', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'subtask detail updated failes.');
        } else {
            $this->db->trans_commit();
            return array('s', 'Sub Task Detail updated successfully.');
        }


    }

    function crm_is_campaigns()
    {

        $statusid = trim($this->input->post('statusid') ?? '');
        $companyid = current_companyID();

        $issubexist = $this->db->query("SELECT statusid,statusType FROM `srp_erp_crm_status` where statusID = '{$statusid}' AND companyID ='{$companyid}' and documentID = 1")->row_array();
        if ($issubexist['statusType'] == 1) {
            $data['isexist'] = 1;
        } else {
            $data['isexist'] = 0;
        }
        return $data;


    }

    function crm_projects()
    {

        $statusid = trim($this->input->post('statusid') ?? '');
        $companyid = current_companyID();

        $issubexist = $this->db->query("SELECT statusid,statusType FROM `srp_erp_crm_status` where statusID = '{$statusid}' AND companyID ='{$companyid}' and documentID = 9")->row_array();
        if ($issubexist['statusType'] == 1) {
            $data['isexist'] = 1;
        } else if ($issubexist['statusType'] == 3) {
            $data['isexist'] = 3;
        } else {
            $data['isexist'] = 0;
        }
        return $data;


    }

    function add_opportunity_product()
    {
        $this->db->trans_start();

        $companyID = $this->common_data['company_data']['company_id'];

        $leadID = trim($this->input->post('leadID') ?? '');
        $leadProductID = trim($this->input->post('leadProductID') ?? '');

        $data['opportunityID'] = $leadID;
        $data['productID'] = trim($this->input->post('productID') ?? '');
        $data['productDescription'] = trim($this->input->post('description') ?? '');
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
            $this->db->where('opportunityProductID', $leadProductID);
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

    function create_criteria()
    {
        $this->db->trans_start();

        $data['description'] = $this->input->post('description');
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $criteriaID = $this->input->post('criteriaID');
        if ($criteriaID == '') {
            $this->db->insert('srp_erp_crm_closingcriterias', $data);
        } else {
            $this->db->update('srp_erp_crm_closingcriterias', $data, array('criteriaID' => $criteriaID));
        }


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Criteria save failed ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Criteria saved successfully.');

        }
    }

    function delete_critiria()
    {
        $companyid = current_companyID();
        $critieriaid = trim($this->input->post('criteriaID') ?? '');

        $critertiaExceed = $this->db->query("SELECT opportunityID FROM `srp_erp_crm_opportunity` where companyID = '{$companyid}' AND closeCriteriaID = '{$critieriaid}'")->row_array();
        if (!empty($critertiaExceed)) {

            return false;
        } else {
            $this->db->delete('srp_erp_crm_closingcriterias', array('criteriaID' => trim($this->input->post('criteriaID') ?? '')));
            return true;
        }


    }

    function pipeline_probabilitiy_cal()
    {
        $companyid = current_companyID();
        $pipeLineDetailID = $this->input->post('pipelinestagedetailid');
        $pipelineID = $this->input->post('pipelineID');
        $data = $this->db->query("SELECT sum(probability) as probability FROM srp_erp_crm_pipelinedetails WHERE pipeLineID = '{$pipelineID}' AND pipeLineDetailID <= '{$pipeLineDetailID}' AND companyID = '{$companyid}'  GROUP BY pipeLineID")->row_array();
        return $data;
    }

    function link_item()
    {
        $companyid = current_companyID();
        $this->db->set('itemAutoID', $this->input->post('selectedItemsSync'));
        $this->db->where('productID', $this->input->post('ProductID'));
        $this->db->where('companyID', $companyid);
        $result = $this->db->update('srp_erp_crm_products');
        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        } else {
            $this->session->set_flashdata('e', 'Records adding failed');
            return array('status' => false);
        }

    }

    function load_quotation_details()
    {
        $QuotationAutoID = $this->input->post('QuotationAutoID');
        $companyid = current_companyID();
        //$policy = (getPolicyValues('IFE', 'All')==1?getPolicyValues('IFE', 'All'):0);

        $data = $this->db->query("SELECT srp_erp_crm_quotationdetails.*,
                                  IFNULL( srp_erp_crm_products.productName  ,CONCAT(srp_erp_itemmaster.itemDescription,' - ',srp_erp_itemmaster.itemSystemCode) ) as productName
                                  FROM `srp_erp_crm_quotationdetails`
                                  LEFT JOIN srp_erp_crm_products ON srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID
                                  LEFT JOIN srp_erp_itemmaster ON srp_erp_itemmaster.itemAutoID = srp_erp_crm_quotationdetails.itemAutoID 
                                  WHERE srp_erp_crm_quotationdetails.companyID = '{$companyid}'
                                  AND contractAutoID = '{$QuotationAutoID}'")->result_array();

        // $data = $this->db->query("SELECT srp_erp_crm_quotationdetails.*, srp_erp_crm_products.productName
        //                           FROM `srp_erp_crm_quotationdetails`
	    //                           LEFT JOIN srp_erp_crm_products on srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID
	    //                           where  srp_erp_crm_quotationdetails.companyID = '{$companyid}' 
	    //                           AND contractAutoID = '{$QuotationAutoID}'
	    //                         ")->result_array();
        return $data;
    }

    function delete_quotation_detail_line()
    {
        $QuotationDetailAutoID = $this->input->post('QuotationDeatailAutoID');
        $result = $this->db->delete('srp_erp_crm_quotationdetails', array('contractDetailsAutoID' => $QuotationDetailAutoID));
        if ($result) {
            return array('error' => 0, 'message' => 'Record deleted successfully!');
        } else {
            return array('error' => 1, 'message' => 'Error while deleting, please contact your system team!');
        }
    }

    function itemtax()
    {
        $taxtype = $this->input->post('type');
        $companyid = current_companyID();
        $data = $this->db->query("SELECT taxMasterAutoID,taxDescription,taxShortCode,taxPercentage,CONCAT(taxShortCode, \"|\", taxDescription, \"|\",taxPercentage, \" %\") AS tax  FROM `srp_erp_taxmaster` WHERE taxType = $taxtype AND isActive = 1 ANd isApplicableforTotal = 0 AND companyID = $companyid")->result_array();
        return $data;
    }

    function re_open_quotation_detail()
    {
        $QuotationAutoID = $this->input->post('QuotationAutoID');
        $companyid = current_companyID();
        $data = array(
            'confirmedYN' => 0,
            'confirmedByEmpID' => null,
            'confirmedByName' => null,
            'confirmedDate' => null,
            'approvedYN' => 0,
            'approvalComment' => null,
        );
        $this->db->where('companyID', $companyid);
        $this->db->where('quotationAutoID', $QuotationAutoID);
        $this->db->update('srp_erp_crm_quotation', $data);
        $this->session->set_flashdata('s', 'Re Opened Successfully.');
        return true;

    }

    function fetch_qu_so_det()
    {
        $QuotationAutoID = $this->input->post('QuotationAutoID');
        $companyid = current_companyID();
        $data['master'] = $this->db->query("Select quotationCode,customerID from srp_erp_crm_quotation where companyID = '{$companyid}' AND quotationAutoID = '{$QuotationAutoID}' ")->row_array();
        $data['detail'] = $this->db->query("SELECT srp_erp_crm_quotationdetails.productID,srp_erp_crm_quotationdetails.contractDetailsAutoID, srp_erp_crm_products.productName FROM `srp_erp_crm_quotationdetails` LEFT JOIN srp_erp_crm_products on srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID where srp_erp_crm_quotationdetails.companyID = '{$companyid}' AND contractAutoID = '{$QuotationAutoID}' AND ((srp_erp_crm_quotationdetails.itemAutoID IS NULL) OR (srp_erp_crm_quotationdetails.itemAutoID = 0)) AND srp_erp_crm_products.itemAutoID IS NULL")->result_array();
        $data['detail_up'] = $this->db->query("SELECT srp_erp_crm_quotationdetails.productID,srp_erp_crm_quotationdetails.contractDetailsAutoID, srp_erp_crm_products.productName FROM `srp_erp_crm_quotationdetails` LEFT JOIN srp_erp_crm_products on srp_erp_crm_products.productID = srp_erp_crm_quotationdetails.productID where srp_erp_crm_quotationdetails.companyID = '{$companyid}' AND contractAutoID = '{$QuotationAutoID}' AND ((srp_erp_crm_quotationdetails.itemAutoID IS NULL) OR (srp_erp_crm_quotationdetails.itemAutoID = 0)) ")->result_array();
        $data['organizationcus'] = $this->db->query("SELECT
	customerID,
	srp_erp_customermaster.customerName
FROM
	`srp_erp_crm_organizations` 
	LEFT JOIN srp_erp_customermaster on srp_erp_customermaster.customerAutoID = srp_erp_crm_organizations.customerID
WHERE
	srp_erp_crm_organizations.companyID = '{$companyid}' 
	AND organizationID = '{$data['master']['customerID']}'")->row_array();


        if(empty($data['organizationcus']['customerID']))
        {
            $data['isexist'] = 2;
        }else
             {
            if (!empty($data['detail_up']))
            {


                $product = $this->db->query("SELECT
	itemAutoID 
FROM
	`srp_erp_crm_products` 
WHERE
	companyID = '{$companyid}' 
	AND srp_erp_crm_products.itemAutoID IS NULL
	AND productID IN (
SELECT
	srp_erp_crm_quotationdetails.productID 
FROM
	`srp_erp_crm_quotationdetails` 
WHERE
	srp_erp_crm_quotationdetails.companyID = '{$companyid}' 
	
	AND contractAutoID = '{$QuotationAutoID}' 
	AND ( ( srp_erp_crm_quotationdetails.itemAutoID IS NULL ) OR ( srp_erp_crm_quotationdetails.itemAutoID = 0 ) ) )")->result_array();

                if((empty($product)))
                {
                    foreach ($data['detail_up'] as $val)
                    {
                        $productItemAutoID =  $this->db->query("SELECT itemAutoID FROM `srp_erp_crm_products` where companyID = '{$companyid}' AND productID  = '{$val['productID']}'")->row_array();
                        $data_update_item['itemAutoID'] = $productItemAutoID['itemAutoID'];
                        $this->db->where('contractDetailsAutoID', $val['contractDetailsAutoID']);
                        $this->db->where('productID', $val['productID']);
                        $this->db->update('srp_erp_crm_quotationdetails', $data_update_item);
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        // return array('e', ' Item Link :  Update Failed ' . $this->db->_error_message());
                    } else {
                        $this->db->trans_commit();
                        //return array('s', 'Item Link Updated Successfully.');
                    }

                }
                else
                {

                    $data['isexist'] = 1;
                }

            }
            else
            {
                $data['isexist'] = 0;
            }
        }


        return $data;
    }

    function generate_so_qut()
    {
        $type = $this->input->post('typeID');
        $companyid = current_companyID();
        $qutID = $this->input->post('qutID');

        $crmquotationmaster = $this->db->query("SELECT srp_erp_crm_quotation.*,concat(contact.firstName,' ',contact.lastName) as qutpersonname FROM `srp_erp_crm_quotation` LEFT join srp_erp_crm_contactmaster contact on contact.contactID = srp_erp_crm_quotation.quotationPersonID where srp_erp_crm_quotation.companyID = '{$companyid}' AND quotationAutoID = '{$qutID}'")->row_array();

        $currency = $this->db->query("SELECT CurrencyCode FROM `srp_erp_currencymaster` where currencyID = '{$crmquotationmaster['transactionCurrencyID']}'")->row_array();
        $crmquotationdetail = $this->db->query("SELECT * FROM `srp_erp_crm_quotationdetails` WHERE companyID = '{$companyid}' AND contractAutoID = '{$qutID}'")->result_array();

        $organizationcus = $this->db->query("SELECT
	customerID,
	srp_erp_customermaster.customerName
FROM
	`srp_erp_crm_organizations` 
	LEFT JOIN srp_erp_customermaster on srp_erp_customermaster.customerAutoID = srp_erp_crm_organizations.customerID
WHERE
	srp_erp_crm_organizations.companyID = '{$companyid}' 
	AND organizationID = '{$crmquotationmaster['customerID']}'")->row_array();

        $customer_arr = $this->fetch_customer_data($organizationcus['customerID']);

                    if($type == 1)
                    {
                        $data['documentID'] = 'QUT';
                        $data['contractType'] = 'Quotation';
                    }else if ($type == 2)
                    {
                        $data['documentID'] = 'SO';
                        $data['contractType'] = 'Sales Order';
                    }else
                    {
                        $data['documentID'] = 'CNT';
                        $data['contractType'] = 'Contract';
                    }



                    $data['isSystemGenerated'] = 1;
                    $data['contactPersonName'] = $crmquotationmaster['qutpersonname'];
                    $data['contactPersonNumber'] = $crmquotationmaster['quotationPersonNumber'];
                    $data['contractDate'] = $crmquotationmaster['quotationDate'];
                    $data['contractExpDate'] = $crmquotationmaster['quotationExpDate'];
                    $data['contractNarration'] = $crmquotationmaster['quotationNarration'];
                    $data['referenceNo'] = $crmquotationmaster['referenceNo'];
                    $data['customerID'] = $customer_arr['customerAutoID'];
                    $data['customerSystemCode'] = $customer_arr['customerSystemCode'];
                    $data['customerName'] = $customer_arr['customerName'];
                    $data['customerAddress'] = $customer_arr['customerAddress1'] . ' ' . $customer_arr['customerAddress2'];
                    $data['customerTelephone'] = $customer_arr['customerTelephone'];
                    $data['customerFax'] = $customer_arr['customerFax'];
                    $data['customerEmail'] = $customer_arr['customerEmail'];
                    $data['customerReceivableAutoID'] = $customer_arr['receivableAutoID'];
                    $data['customerReceivableSystemGLCode'] = $customer_arr['receivableSystemGLCode'];
                    $data['customerReceivableGLAccount'] = $customer_arr['receivableGLAccount'];
                    $data['customerReceivableDescription'] = $customer_arr['receivableDescription'];
                    $data['customerReceivableType'] = $customer_arr['receivableType'];
                    $data['customerCurrency'] = $customer_arr['customerCurrency'];
                    $data['customerCurrencyID'] = $customer_arr['customerCurrencyID'];
                    $data['customerCurrencyDecimalPlaces'] = $customer_arr['customerCurrencyDecimalPlaces'];
                    $data['Note'] = '';
                    $data['transactionCurrencyID'] = $crmquotationmaster['transactionCurrencyID'];
                    $data['transactionCurrency'] = $currency['CurrencyCode'];
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
                    $customer_currency = currency_conversionID($data['transactionCurrencyID'], $data['customerCurrencyID']);
                    $data['customerCurrencyExchangeRate'] = $customer_currency['conversion'];
                    $data['customerCurrencyDecimalPlaces'] = $customer_currency['DecimalPlaces'];
                    $this->load->library('sequence');
                    $codegenerator = $this->sequence->sequence_generator($data['documentID']);
                    $data['contractCode'] = $codegenerator;
                    $data['companyCode'] = $this->common_data['company_data']['company_code'];
                    $data['companyID'] = $this->common_data['company_data']['company_id'];
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_contractmaster', $data);
                    $last_id = $this->db->insert_id();

                    $this->db->select('companyLocalExchangeRate,companyReportingExchangeRate,customerCurrencyExchangeRate');
                    $this->db->from('srp_erp_contractmaster');
                    $this->db->where('contractAutoID', $last_id);
                    $contract_master = $this->db->get()->row_array();



                 if ($type == 1) //for generate a quotation in sales and marketing
                            {
                         $data_update_qutstatus = array(
                             'approvedYN' => 3,
                             'erp_contractAutoID'=>$last_id
                             );
                            $this->db->where('quotationAutoID',$qutID);
                            $this->db->update('srp_erp_crm_quotation', $data_update_qutstatus);
                        }else if ($type == 2)
                         {
                     $data_update_qutstatus = array(
                         'approvedYN' => 4,
                         'erp_contractAutoID'=>$last_id
                        );

                     $this->db->where('quotationAutoID',$qutID);
                     $this->db->update('srp_erp_crm_quotation', $data_update_qutstatus);
                      }else if ($type == 3)
                 {
                     $data_update_qutstatus = array(
                         'approvedYN' => 5,
                         'erp_contractAutoID'=>$last_id

                     );

                     $this->db->where('quotationAutoID',$qutID);
                     $this->db->update('srp_erp_crm_quotation', $data_update_qutstatus);
                 }

                    foreach ($crmquotationdetail as $val) {
                        $item_arr = fetch_item_data($val['itemAutoID']);
                        $uom = explode('|', $val['unitOfMeasure']);
                         $data_detail_up['contractAutoID'] = $last_id;
                         $data_detail_up['itemAutoID'] = $val['itemAutoID'];
                         $data_detail_up['itemSystemCode'] = $item_arr['itemSystemCode'];
                         $data_detail_up['itemDescription'] = $item_arr['itemDescription'];
                         $data_detail_up['itemCategory'] = $item_arr['mainCategory'];
                         $data_detail_up['unitOfMeasure'] = trim($uom[0] ?? '');
                         $data_detail_up['unitOfMeasureID'] = trim($val['unitOfMeasureID'] ?? '');
                         $data_detail_up['itemReferenceNo'] = '';
                         $data_detail_up['defaultUOM'] = $item_arr['defaultUnitOfMeasure'];
                         $data_detail_up['defaultUOMID'] = $item_arr['defaultUnitOfMeasureID'];
                         $data_detail_up['conversionRateUOM'] = conversionRateUOM_id($data_detail_up['unitOfMeasureID'],  $data_detail_up['defaultUOMID']);
                         $data_detail_up['discountPercentage'] = $val['discountPercentage'];
                         $data_detail_up['discountAmount'] = $val['discountAmount'];
                         $data_detail_up['noOfItems'] = 0;
                         $data_detail_up['requestedQty'] = trim($val['requestedQty'] ?? '');
                         $data_detail_up['unittransactionAmount'] = (trim($val['unittransactionAmount'] ?? '') -  $data_detail_up['discountAmount']);
                         $data_detail_up['transactionAmount'] = ($data_detail_up['unittransactionAmount'] *  $data_detail_up['requestedQty']);
                         $data_detail_up['companyLocalAmount'] = ($data_detail_up['transactionAmount'] / $contract_master['companyLocalExchangeRate']);
                         $data_detail_up['companyReportingAmount'] = ($data_detail_up['transactionAmount'] / $contract_master['companyReportingExchangeRate']);
                         $data_detail_up['customerAmount'] = ($data_detail_up['transactionAmount'] / $contract_master['customerCurrencyExchangeRate']);
                         $data_detail_up['discountTotal'] = ($data_detail_up['discountAmount'] *  $data_detail_up['requestedQty']);
                         $data_detail_up['comment'] = trim($val['comment'] ?? '');
                         $data_detail_up['remarks'] = trim($val['remarks'] ?? '');
                         $data_detail_up['modifiedPCID'] = $this->common_data['current_pc'];
                         $data_detail_up['modifiedUserID'] = $this->common_data['current_userID'];
                         $data_detail_up['modifiedUserName'] = $this->common_data['current_user'];
                         $data_detail_up['modifiedDateTime'] = $this->common_data['current_date'];

                         $data_detail_up['companyID'] = $this->common_data['company_data']['company_id'];
                         $data_detail_up['companyCode'] = $this->common_data['company_data']['company_code'];
                         $data_detail_up['createdUserGroup'] = $this->common_data['user_group'];
                         $data_detail_up['createdPCID'] = $this->common_data['current_pc'];
                         $data_detail_up['createdUserID'] = $this->common_data['current_userID'];
                         $data_detail_up['createdUserName'] = $this->common_data['current_user'];
                         $data_detail_up['createdDateTime'] = $this->common_data['current_date'];
                         $this->db->insert('srp_erp_contractdetails', $data_detail_up);
                    }

                    $this->load->library('Approvals');
                    $this->db->select('documentID,contractType,contractCode,customerCurrencyExchangeRate,companyReportingExchangeRate, companyLocalExchangeRate ,contractAutoID,transactionCurrencyDecimalPlaces,contractDate');
                    $this->db->where('contractAutoID', $last_id);
                    $this->db->from('srp_erp_contractmaster');
                    $c_data = $this->db->get()->row_array();

                    $autoApproval= get_document_auto_approval($c_data['documentID']);



                    if($autoApproval==0){

                        $approvals_status = $this->approvals->auto_approve($c_data['contractAutoID'], 'srp_erp_contractmaster','contractAutoID', $c_data['documentID'],$c_data['contractCode'], $c_data['contractDate']);
                    }elseif($autoApproval==1){
                        $approvals_status = $this->approvals->CreateApproval($c_data['documentID'], $c_data['contractAutoID'], $c_data['contractCode'], $c_data['contractType'], 'srp_erp_contractmaster', 'contractAutoID', 1, $c_data['contractDate']);
                    }else{
                        return array('e', 'Approval levels are not set for this document');
                        exit;
                    }


                    if ($approvals_status) {
                        $this->db->select_sum('transactionAmount');
                        $this->db->where('contractAutoID', $last_id);
                        $total = $this->db->get('srp_erp_contractdetails')->row('transactionAmount');
                        $data_confirm = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'transactionAmount' => round($total, $c_data['transactionCurrencyDecimalPlaces']),
                            'companyLocalAmount' => ($total / $c_data['companyLocalExchangeRate']),
                            'companyReportingAmount' => ($total / $c_data['companyReportingExchangeRate']),
                            'customerCurrencyAmount' => ($total / $c_data['customerCurrencyExchangeRate']),
                        );
                        $this->db->where('contractAutoID',$last_id);
                        $this->db->update('srp_erp_contractmaster', $data_confirm);
                        /*$this->session->set_flashdata('s', 'Create Approval : ' . $c_data['contractCode'] . ' Approvals Created Successfully ');
                        return true;*/
                        if($autoApproval==0) {
                            $result = $this->save_quotation_contract_approval(0, $c_data['contractAutoID'], 1, 'Auto Approved');
                            if($result){
                               // return array('s', 'Approvals Created Successfully.');
                            }
                        }else{
                            //return array('s', 'Approvals Created Successfully.');
                        }
                    } else {
                        /*return false;*/
                        return array('e', 'oops, something went wrong!.');
                    }



                if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Contract Saved failed ' . $this->db->_error_message());
                } else {
                 $this->db->trans_commit();


                 return array('s', 'Contract Saved Successfully.');
        }
    }
    function fetch_customer_data($customerID)
    {
        $this->db->select('*');
        $this->db->from('srp_erp_customermaster');
        $this->db->where('customerAutoID', $customerID);
        return $this->db->get()->row_array();
    }
}