<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Navigation_model
 */
class Navigation_model extends ERP_Model
{
    /**
     * Get navigation header
     *
     * @param integer $companyId
     * @param integer $userId
     * @param integer $userType
     * @param integer $companyType
     * @param integer $isGroupUser
     * @return array
     */
    public function getNavigationHeader($companyId, $userId, $userType, $companyType, $isGroupUser)
    {
        if ($userType != 1) {
            if ($companyType == 1) {
                if ($isGroupUser == 1) {
                    $db2 = $this->load->database('db2', TRUE);
                    $db2->select('userGroupID');
                    $db2->where("companyID", $companyId);
                    $db2->where("empID", $userId);
                    $groupDetails = $db2->get("groupusercompanies")->row_array();
                    $userGroupID = $groupDetails['userGroupID'];
                    $detail = $this->db->query("SELECT srp_erp_navigationmenus.languageID, srp_erp_navigationusergroupsetup.*,IFNULL(template.TempPageNameLink,srp_erp_navigationmenus.url) as TempPageNameLink, srp_erp_navigationmenus.isExternalLink,srp_erp_navigationmenus.iconColor 
                    FROM srp_erp_employeenavigation 
                    INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
                    INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID 
                    LEFT JOIN (
                        SELECT srp_erp_templates.TempMasterID,srp_erp_templates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink
                        FROM srp_erp_templates
                        LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                        WHERE srp_erp_templates.companyID={$companyId}
                    ) AS template ON (template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID)
                    WHERE empID={$userId} AND srp_erp_employeenavigation.companyID={$companyId} AND srp_erp_employeenavigation.userGroupID={$userGroupID} AND srp_erp_navigationmenus.basicYN = 1 
                    ORDER BY levelNo,sortOrder ASC ")->result_array();
                }
                else {
                    $detail = $this->db->query("SELECT srp_erp_navigationmenus.*, IFNULL(template.TempPageNameLink,srp_erp_navigationmenus.url) as TempPageNameLink, srp_erp_navigationmenus.isExternalLink 
                                FROM srp_erp_navigationmenus
                                LEFT JOIN (
                                    SELECT srp_erp_templates.TempMasterID, srp_erp_templates.navigationMenuID, srp_erp_templatemaster.TempPageNameLink 
                                    FROM srp_erp_templates
                                    LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID 
                                    WHERE srp_erp_templates.companyID = {$companyId} 
                                ) AS template ON template.navigationMenuID = srp_erp_navigationmenus.navigationMenuID  
                                WHERE srp_erp_navigationmenus.basicYN = 1
                                ORDER BY levelNo, sortOrder ASC")->result_array();
                }
            }
            else {
                $sql = "SELECT srp_erp_navigationmenus.secondaryDescription,srp_erp_navigationmenus.languageID,srp_erp_companysubgroupnavigationsetup.*,IFNULL(template.TempPageNameLink,srp_erp_companysubgroupnavigationsetup.url) as TempPageNameLink,srp_erp_navigationmenus.iconColor
                FROM srp_erp_companysubgroupnavigationsetup 
                LEFT JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID
                LEFT JOIN srp_erp_companysubgroupmaster ON srp_erp_companysubgroupnavigationsetup.compaySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
                LEFT JOIN srp_erp_companysubgroupemployees ON srp_erp_companysubgroupemployees.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
                LEFT JOIN (
                    SELECT srp_erp_companysubgrouptemplates.TempMasterID,srp_erp_companysubgrouptemplates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink,companySubGroupID 
                    FROM srp_erp_companysubgrouptemplates 
                    LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_companysubgrouptemplates.TempMasterID 
                ) AS template ON (template.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID)
                WHERE srp_erp_companysubgroupemployees.EmpID={$userId} AND companyGroupID={$companyId} AND isGroup = 1 Order by levelNo,sortOrder ASC ";
                $detail = $this->db->query($sql)->result_array();
            }
        }
        else {
            if ($companyType == 1) {
                if ($isGroupUser == 1) {
                    $db2 = $this->load->database('db2', TRUE);
                    $db2->select('userGroupID');
                    $db2->where("companyID", $companyId);
                    $db2->where("empID", $userId);
                    $groupDetails = $db2->get("groupusercompanies")->row_array();
                    $userGroupID = $groupDetails['userGroupID'];
                    $detail = $this->db->query("SELECT srp_erp_navigationmenus.secondaryDescription,srp_erp_navigationmenus.languageID,srp_erp_navigationmenus.imgIcon, srp_erp_navigationusergroupsetup.*,IFNULL(template.TempPageNameLink,srp_erp_navigationmenus.url) as TempPageNameLink, srp_erp_navigationmenus.isExternalLink,srp_erp_navigationmenus.iconColor
                        FROM srp_erp_employeenavigation
                        INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
                        INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
                        LEFT JOIN (
                            SELECT srp_erp_templates.TempMasterID,srp_erp_templates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink
                            FROM srp_erp_templates
                            LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                            WHERE srp_erp_templates.companyID={$companyId} ) AS template ON (template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
                        )
                        WHERE empID={$userId} AND srp_erp_employeenavigation.companyID={$companyId} AND srp_erp_employeenavigation.userGroupID={$userGroupID} ORDER BY levelNo,sortOrder ASC")->result_array();
                } else {
                    $detail = $this->db->query("
                        SELECT srp_erp_navigationmenus.secondaryDescription,srp_erp_navigationmenus.languageID,srp_erp_navigationmenus.imgIcon, srp_erp_navigationusergroupsetup.*,IFNULL(template.TempPageNameLink,srp_erp_navigationmenus.url) as TempPageNameLink, srp_erp_navigationmenus.isExternalLink,srp_erp_navigationmenus.iconColor
                        FROM srp_erp_employeenavigation
                        INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_employeenavigation.userGroupID = srp_erp_navigationusergroupsetup.userGroupID
                        INNER JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
                        LEFT JOIN (
                            SELECT srp_erp_templates.TempMasterID,srp_erp_templates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink
                            FROM srp_erp_templates
                                LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_templates.TempMasterID
                            WHERE srp_erp_templates.companyID={$companyId}
                        ) AS template ON (template.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID)
                        WHERE empID={$userId} AND srp_erp_employeenavigation.companyID={$companyId} Order by levelNo,sortOrder ASC")->result_array();
                }
            }
            else {
                $sql = "SELECT srp_erp_navigationmenus.secondaryDescription,srp_erp_navigationmenus.languageID,srp_erp_navigationmenus.imgIcon,srp_erp_companysubgroupnavigationsetup.*,IFNULL(template.TempPageNameLink,srp_erp_companysubgroupnavigationsetup.url) as TempPageNameLink,srp_erp_navigationmenus.iconColor
                FROM srp_erp_companysubgroupnavigationsetup 
                LEFT JOIN srp_erp_navigationmenus ON srp_erp_navigationmenus.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID
                LEFT JOIN srp_erp_companysubgroupmaster ON srp_erp_companysubgroupnavigationsetup.compaySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
                LEFT JOIN srp_erp_companysubgroupemployees ON srp_erp_companysubgroupemployees.companySubGroupID = srp_erp_companysubgroupmaster.companySubGroupID
                LEFT JOIN (
                    SELECT srp_erp_companysubgrouptemplates.TempMasterID,srp_erp_companysubgrouptemplates.navigationMenuID,srp_erp_templatemaster.TempPageNameLink,companySubGroupID 
                    FROM srp_erp_companysubgrouptemplates 
                    LEFT JOIN srp_erp_templatemaster ON srp_erp_templatemaster.TempMasterID = srp_erp_companysubgrouptemplates.TempMasterID 
                ) AS template ON (template.navigationMenuID = srp_erp_companysubgroupnavigationsetup.navigationMenuID)
                WHERE srp_erp_companysubgroupemployees.EmpID={$userId} AND companyGroupID={$companyId} AND isGroup = 1 
                Order by levelNo,sortOrder ASC";
                $detail = $this->db->query($sql)->result_array();
            }
        }

        return $detail;
    }

    /**
     * Get navigation by id
     *
     * @param int $navigationId
     * @return array
     */
    public function getNavigationById(int $navigationId): array
    {
        $this->db->select('*');
        $this->db->from('srp_erp_navigationmenus');
        $this->db->where('navigationMenuID', $navigationId);
        return $this->db->get()->row_array();
    }

    /**
     * Get all navigation
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql = "
WITH RECURSIVE navigation_tree AS (
    SELECT 
        navigationMenuID,
        description,
        secondaryDescription,
        masterID,
        url,
        levelNo,
        sortOrder,
        isSubExist
    FROM 
        srp_erp_navigationmenus
    WHERE 
        navigationMenuID IN (
            SELECT 
                navigationMenuID 
            FROM 
                srp_erp_moduleassign 
            WHERE 
                companyID = ?
        )
    
    UNION ALL
    
    SELECT 
        nm.navigationMenuID,
        nm.description,
        nm.secondaryDescription,
        nm.masterID,
        nm.url,
        nm.levelNo,
        nm.sortOrder,
        nm.isSubExist
    FROM 
        srp_erp_navigationmenus AS nm
    INNER JOIN 
        navigation_tree AS nt ON nt.navigationMenuID = nm.masterID
)
SELECT * FROM navigation_tree;
";

        $query = $this->db->query($sql, [current_companyID()]);
        return $query->result_array();
    }

    /**
     * Save navigation secondary description
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function saveNavigationSecondaryDescription(array $data): bool
    {
        $bulkUpdateData = [];
        if(!empty($data['navigationMenuID'])){
            foreach ($data['navigationMenuID'] as $key => $navigationMenuID) {
                $bulkUpdateData[] = [
                    'secondaryDescription' => $data['secondaryDescription'][$key],
                    'navigationMenuID'     => $navigationMenuID
                ];
            }

        }
        return $this->db->update_batch('srp_erp_navigationmenus', $bulkUpdateData, 'navigationMenuID');
    }


}