<?php

class Access_menu_model extends ERP_Model
{

    /*function loadWidet()
    {
        $result = $this->db->query("SELECT widgetID,widgetName from srp_erp_widgetmaster")->result_array();
        return $result;
    }*/

    function loadWidet($usergroupID)
    {
        $compid = current_companyID();
        $companyType = $this->session->userdata("companyType");
        $isGroupYN  = '';
        if($companyType == 1) {
            $isGroupYN = 'AND isGroupYN = 1';
        }else
        {
            $isGroupYN = 'AND isGroupYN = 2';;
        }

        $result = $this->db->query("SELECT
    srp_erp_widgetmaster.widgetID,
    srp_erp_widgetmaster.widgetName,
    srp_erp_usergroupwidget.widgetID AS widget
FROM
    srp_erp_widgetmaster
LEFT JOIN srp_erp_usergroupwidget ON srp_erp_widgetmaster.widgetID = srp_erp_usergroupwidget.widgetID
WHERE
    srp_erp_usergroupwidget.companyID = $compid
AND srp_erp_usergroupwidget.userGroupID = $usergroupID
$isGroupYN
UNION
SELECT
        srp_erp_widgetmaster.widgetID,
        srp_erp_widgetmaster.widgetName,
        NULL AS widget
    FROM
        srp_erp_widgetmaster
    WHERE
        srp_erp_widgetmaster.widgetID NOT IN(
            SELECT
                widgetID
            FROM
                srp_erp_usergroupwidget
                
            WHERE
                usergroupID = $usergroupID
                AND companyID = $compid)
                $isGroupYN
                
                ")->result_array();

        return $result;
    }

    function save_widget()
    {
        //print_r($_POST); exit;
        $results='';
        $userGroupID = $this->input->post('userGroupIDWidget');
        $widgetcheck = $this->input->post('widgetCheck');
        $isAlreadySelected = $this->input->post('isAlreadySelected');
        $compID = current_companyID();
        if (!empty($widgetcheck)) {
            foreach ($isAlreadySelected as $key=>$vals) {
                $data = explode('|', $isAlreadySelected[$key]);
                $wedgetID = trim($data[1] ?? '');

                if($data[0] == 'yes' && !in_array( $wedgetID , $widgetcheck)){
                    $del_arr = array(
                        'userGroupID' => $userGroupID,
                        'companyID' => current_companyID(),
                        'widgetID' => $wedgetID,
                    );
                    $this->db->where($del_arr)->delete('srp_erp_usergroupwidget');
                    //echo '<p>'.$this->db->last_query();
                }
            }
            foreach ($widgetcheck as $key=>$vals) {
                $result = $this->db->query("SELECT widgetID FROM srp_erp_usergroupwidget where userGroupID = $userGroupID and companyID= $compID and widgetID= $vals")->result();
                if ($result) {
                    continue;
                } else {
                    $this->db->set('companyID', $compID);
                    $this->db->set('userGroupID', $userGroupID);
                    $this->db->set('widgetID', $vals);
                    $results = $this->db->insert('srp_erp_usergroupwidget');
                }
            }
        }else{
            $delAll = array(
                'userGroupID' => $userGroupID,
                'companyID' => current_companyID(),
            );
            $this->db->where($delAll)->delete('srp_erp_usergroupwidget');
            return array('e', 'Select Widget');
        }
        if ($results) {
            return array('s', 'Widget Added Successfully');
        }else{
            return array('s', 'Widget Added Successfully');
        }
    }

    function deleteUserGroupID(){
        $assigned  = $this->db->select('*')->from('srp_erp_employeenavigation')->where(array('userGroupID' => $this->input->post('userGroupID'), 'companyID' => current_companyID()))->get()->result_array();
        if($assigned){
            return array('w', 'You cannot delete this usergroup because it is already assigned to users');
        }else{
            $this->db->where('userGroupID', $this->input->post('userGroupID'));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->delete('srp_erp_usergroupwidget');

            $this->db->where('userGroupID', $this->input->post('userGroupID'));
            $this->db->where('companyID', current_companyID());
            $result = $this->db->delete('srp_erp_usergroups');
            if($result){
                return array('s', 'User group successfully deleted.');
            }else{
                return array('e', 'Error Occurred');
            }
        }
    }

    function load_user_group()
    {
        $userGroupID = trim($this->input->post('userGroupID') ?? '');
        $data = $this->db->query("select description FROM srp_erp_usergroups WHERE userGroupID = {$userGroupID} ")->row_array();
        return $data;
    }

    function update_emp_language()
    {
        $companyID = current_companyID();
        $EIdNo = current_userID();
        $languageid =  trim($this->input->post('languageid') ?? '');
        $emplang = $this->db->query("select languageID from srp_employeesdetails WHERE Erp_companyID={$companyID} AND EIdNo={$EIdNo}")->row_array();

        $this->session->set_userdata("emplangid", $languageid);
        $data['languageID'] = $languageid;
        $this->db->where('Erp_companyID', current_companyID());
        $this->db->where('EIdNo', current_userID());
        $this->db->update('srp_employeesdetails', $data);

    }


    function update_emp_location()
    {
        $companyID = current_companyID();
        $EIdNo = current_userID();
        $locationID =  trim($this->input->post('locationID') ?? '');
        $data['locationID'] = $locationID;
        $this->session->set_userdata("emplanglocationid",$data['locationID']);
        $this->db->where('Erp_companyID', current_companyID());
        $this->db->where('EIdNo', current_userID());
        $this->db->update('srp_employeesdetails', $data);

    }

    /*$ext = ( !empty(array_search( $wedgetID , $widgetcheck)) )? 'true' : 'false'  ;
                //$ext =  $data[1] ;
                echo '<p>'.$ext.' |' .$key .'';*/

    public function control_staff_access($page_url)
    {


        $companyID = current_companyID();
        $companyType = $this->session->userdata("companyType");
        $empID = current_userID();

        $query = $this->db->query("SELECT TempMasterID FROM srp_erp_templatemaster WHERE TempPageNameLink ='" . $page_url . "'");
        $TempMasterID = $query->row();

        $query = $this->db->query("SELECT userGroupID FROM srp_erp_employeenavigation WHERE companyID = '" . $companyID . "' AND empID = '" . $empID . "'");
        $res_au = $query->result();

        $data = array();
        foreach ($res_au as $row) {

            $query = $this->db->query("SELECT * FROM srp_erp_navigationusergroupsetup LEFT JOIN srp_erp_templates ON srp_erp_templates.navigationMenuID=srp_erp_navigationusergroupsetup.navigationMenuID WHERE  srp_erp_templates.companyID = '" . $companyID . "' AND srp_erp_navigationusergroupsetup.userGroupID ='" . $row->userGroupID . "' AND srp_erp_templates.TempMasterID = '" . $TempMasterID->TempMasterID . "'");
            $res_modulepages = $query->result();

            foreach ($res_modulepages as $rows) {

                if ($rows->Add_Edit == 0 && $rows->Views == 0 && $rows->Print_Excel == 0) {
                    $data[] = 1;
                } else {
                    if ($rows->Add_Edit == 0) {
                        $data[] = 'Add';
                    }
                    if ($rows->Views == 0) {
                        $data[] = 'View';
                    }
                    if ($rows->Print_Excel == 0) {
                        $data[] = 'Print';
                    }
                }
            }
        }

        return $data;

    }
    function getusergroupcomapny()
    {
        $companyid = $this->input->post('companyid');
        $iscompanyusergroup = $this->db->query("SELECT companyGroupID AS company_id FROM srp_erp_companygroupmaster WHERE companyGroupID = $companyid ")->row_array();
        if(!empty($iscompanyusergroup))
        {
            $data = 1;
        }else
        {
            $data = 2;
        }
        return $data;
    }

    function fetch_template_keyword() {
        $companyID = current_companyID();
        $documentID = $this->input->post('documentID');
        $companyType = $this->session->userdata("companyType");

        if($companyType == 1)
        {
            $keyword = $this->db->query("SELECT
                    srp_erp_templates.templateKey as templateKey
                FROM
                srp_erp_templates
                LEFT JOIN 
                    `srp_erp_templatemaster` ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                WHERE
                    companyID = {$companyID} AND 
                    documentCode = '{$documentID}'")->row_array();
        }else {
            $keyword = $this->db->query("SELECT
	                                         templateKey AS templateKey 
                                             FROM
	                                         srp_erp_companysubgrouptemplates
	                                         LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_companysubgrouptemplates.TempMasterID 
                                             WHERE
	                                         documentCode = '{$documentID}'")->row_array();
        }


        if(!empty($keyword)) {
            return $keyword['templateKey'];
        } else {
            return '';
        }
    }
}