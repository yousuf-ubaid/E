<?php

class Report_template_model extends ERP_Model
{

    function save_reportTemplateMaster()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
      

        $group_masterID = getParentgroupMasterID();
        if($group_masterID==0)
        {
         $templateType = trim($this->input->post('templateType') ?? '');
        }
        else{
        $templateType = 2;
        }
        
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['reportID'] = trim($this->input->post('reportID') ?? '');
        $data['templateType'] = $templateType;

        $data['createdUserGroup'] = current_user_group();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdUserName'] = current_employee();
        $data['createdDateTime'] = current_date();

        if($templateType == 1){ //Fund management
            $data['companyID'] = $companyID;
        }
        else{ // MPR
            $companyType = $this->session->userdata('companyType');
          
            $companyID = ($companyType == 1)? $companyID: getParentgroupMasterID();

            $data['companyID'] = $companyID;
            $data['companyType'] = $companyType;
        }

        $this->db->insert('srp_erp_companyreporttemplate', $data);
        $last_id = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Template Save Failed');
        } else {
            return array('s', 'Template Saved Successfully');
        }
    }

    function save_reportTemplateDetail()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $dateTime = current_date();
        $subMaster = trim($this->input->post('subMaster') ?? '');
        $subMaster = (empty($subMaster)) ? null : $subMaster;
        $itemType = $this->input->post('itemType');
        $grossrevenue = $this->input->post('Isdefault');
        $masterID = $this->input->post('masterID');
        $accountTypeCatergory = $this->input->post('accountTypecater');

        $isgrossrev = 0;
        if ($itemType == 3)
        {

            if($grossrevenue == 1)
            {
                $result = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` WHERE companyID = {$companyID} AND itemType = {$itemType} AND companyReportTemplateID = {$masterID} AND is_gross_rev = 1")->result_array();
                if(!empty($result))
                {
                    return array('e', 'Gross Revenue Type Is Already Assigned');
                }else
                {
                    $data['is_gross_rev'] = 1;
                }

            }
        }
        if(!empty($accountTypeCatergory))
        {
            if($itemType == 1)
            {
                $result = $this->db->query("SELECT detID,defaultType FROM `srp_erp_companyreporttemplatedetails` WHERE companyID = {$companyID}  AND itemType = {$itemType} AND companyReportTemplateID = {$masterID} AND defaultType = {$accountTypeCatergory}")->row_array();
                if(!empty($result))
                {
                    if($result['defaultType'] == 1)
                    {
                        $catName = 'Uncategorized Income';
                    }else
                    {
                        $catName = 'Uncategorized Expense';
                    }

                    return array('e', $catName.' Type Is Already Assigned');
                }else
                {
                    $data['defaultType'] = $accountTypeCatergory;
                }
            }
        }



        $data['companyReportTemplateID'] = trim($this->input->post('masterID') ?? '');
        $data['description'] = trim($this->input->post('description') ?? '');
        $data['sortOrder'] = trim($this->input->post('sortOrder') ?? '');
        $data['masterID'] = $subMaster;



        $data['itemType'] = $itemType;

        if($itemType == 2){
            $data['accountType'] = trim($this->input->post('accountType') ?? '');
        }
        $data['companyID'] = trim($companyID);
        $data['createdUserGroup'] = current_user_group();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdUserName'] = current_employee();
        $data['createdDateTime'] = $dateTime;
        $data['timestamp'] = $dateTime;

        $this->db->insert('srp_erp_companyreporttemplatedetails', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Error in process');
        } else {
            return array('s', 'Template Saved Successfully');
        }
    }

    function save_reportTemplateLink()
    {
        $this->db->trans_start();

        $masterID = trim($this->input->post('masterID') ?? '');
        $column = ($this->input->post('linkType') == 'S') ? 'glAutoID': 'subCategory';
        $detID = trim($this->input->post('detID') ?? '');
        $glAutoID_arr = $this->input->post('glAutoID');
        $sortOrder = $this->db->query("SELECT MAX(sortOrder) AS sortOrder FROM srp_erp_companyreporttemplatelinks WHERE templateDetailID={$detID}")->row('sortOrder');
        $data = [];

        /*To handle the group id*/
        $companyID = $this->db->query("SELECT companyID FROM srp_erp_companyreporttemplate WHERE companyReportTemplateID = {$masterID}")->row('companyID');

        foreach ($glAutoID_arr as $key=>$gl){
            $sortOrder++;
            $data[$key]['templateMasterID'] = $masterID;
            $data[$key]['templateDetailID'] = $detID;
            $data[$key][$column] = $gl;
            $data[$key]['sortOrder'] = $sortOrder;
            $data[$key]['companyID'] = $companyID;
            $data[$key]['createdUserGroup'] = current_user_group();
            $data[$key]['createdPCID'] = current_pc();
            $data[$key]['createdUserID'] = current_userID();
            $data[$key]['createdUserName'] = current_employee();
            $data[$key]['createdDateTime'] = current_date();
        }

        $this->db->insert_batch('srp_erp_companyreporttemplatelinks', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Error in sub item adding process');
        } else {
            return array('s', 'Sub items added successfully');
        }
    }
     function template_confirmation()
     {
         $companyID = current_companyID();
         $companyReportTemplateID = trim($this->input->post('companyReportTemplateID') ?? '');

        $confirmdYNrep = $this->db->query("SELECT companyReportTemplateID FROM `srp_erp_companyreporttemplate` where companyID = {$companyID} AND companyReportTemplateID = {$companyReportTemplateID} AND confirmedYN = 1")->row('companyReportTemplateID');

        $IsGrossRev = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` where companyID = {$companyID} AND itemType = 3 AND is_gross_rev = 1")->row('detID');
        $Isuncategorizedincome =  $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` where companyID = {$companyID} AND itemType = 1 AND defaultType = 1 ")->row('detID');
        $Isuncategorizedexpense = $this->db->query("SELECT detID FROM `srp_erp_companyreporttemplatedetails` where companyID = {$companyID} AND itemType = 1 AND defaultType = 2")->row('detID');

        if(!empty($IsGrossRev)&&!empty($Isuncategorizedincome)&&!empty($Isuncategorizedexpense))
        {
            if(!empty($confirmdYNrep))
            {
                return array('w', 'Document already confirmed');
            }else
            {
                $data = array(
                    'confirmedYN' => 1,
                    'confirmedDate' => $this->common_data['current_date'],
                    'confirmedByEmpID' => $this->common_data['current_userID'],
                );
                $this->db->where('companyReportTemplateID', trim($this->input->post('companyReportTemplateID') ?? ''));
                $this->db->update('srp_erp_companyreporttemplate', $data);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    return array('e', 'Document Confirmed failed!');
                } else {
                    return array('s', 'Document Confirmed Successfully!');

                }
            }
        }else
        {
            $colname = '';
            if(empty($IsGrossRev))
            {
              $colname .= 'Please Assign Gross Revenue Type <br>';
            }
            if(empty($Isuncategorizedincome))
            {
                $colname .= 'Please Assign Uncategorized Income Type <br>';
            }
            if(empty($Isuncategorizedexpense))
            {
                $colname .= 'Please Assign Uncategorized Expense Type';
            }

            return array('e', $colname);
        }




     }
     function template_unconfirmation()
     {
         $data = array(
             'confirmedYN' => 0,
             'confirmedDate' => '',
             'confirmedByEmpID' => '',
         );
         $this->db->where('companyReportTemplateID', trim($this->input->post('companyReportTemplateID') ?? ''));
         $this->db->update('srp_erp_companyreporttemplate', $data);
         $this->db->trans_complete();
         if ($this->db->trans_status() === FALSE) {
             return array('e', 'Document Un Confirmed failed!');
         } else {
             return array('s', 'Document Un Confirmed Successfully!');

         }
     }


}