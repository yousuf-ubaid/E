<style>
    .dataText {
        font-size: 12px;
        font-weight: 400;
    }

    .dataContact {
        text-align: center;
        color: #00C0EF;
    }

    .dataOrganizations {
        text-align: center;
        color: #00C0EF;
    }

    .dataLeads {
        text-align: center;
        color: #00C0EF;
    }

    .dataOpportunity {
        text-align: center;
        color: #00C0EF;
    }

    .dataProject {
        text-align: center;
        color: #00C0EF;
    }

    .dataMonth {
        color: teal;
    }

    .dataHead {
        color: teal;
    }

</style>
<?php
$companyID = $this->common_data['company_data']['company_id'];
$currentuserID = current_userID();
$issuperadmin = crm_isSuperAdmin();
$isGroupAdmin = crm_isGroupAdmin();
$masterEmployee = $this->input->post('employeeID');
$groupID = $this->input->post('groupID');
$year = trim($this->input->post('year'));
$permissiontype = trim($this->input->post('permission') ?? '');

$yearWiseMonth = array("'$year-01-01' AND '$year-01-31'", "'$year-02-01' AND '$year-02-31'", "'$year-03-01' AND '$year-03-31'", "'$year-04-01' AND '$year-04-31'", "'$year-05-01' AND '$year-05-31'", "'$year-06-01' AND '$year-06-31'", "'$year-07-01' AND '$year-07-31'", "'$year-08-01' AND '$year-08-31'", "'$year-09-01' AND '$year-09-31'", "'$year-10-01' AND '$year-10-31'", "'$year-11-01' AND '$year-11-31'", "'$year-12-01' AND '$year-12-31'");

$englishMonth = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");

$contact_total = 0;
$organization_total = 0;
$leads_total = 0;
$opportunities_total = 0;
$projects_total = 0;
$project_total = 0;
?>
<table style="width: 100%" class="table table-responsive">
    <tr>
        <th class="dataHead">Month</th>
        <th class="dataHead">Contacts</th>
        <th class="dataHead">Organizations</th>
        <th class="dataHead">Leads</th>
        <th class="dataHead">Opportunities</th>
        <th class="dataHead">Projects</th>
    </tr>
    <tbody>
    <tr>
        <?php
        $where_count1 = " ";
        $where_count2 = " ";
        $where_count3 = " ";
        $where_count4 = " ";
        $where_organization1 = " ";
        $where_organization2 = " ";
        $where_organization3 = " ";
        $where_organization4 = " ";
        $where_lead1 =" ";
        $where_lead2 =" ";
        $where_lead3  =" ";
        $where_lead4 =" ";
        $where_opportunity1 = "";
        $where_project1 = " ";

        $where_opportunity1 =" ";
        $where_opportunity2 =" ";
        $where_opportunity3 =" ";
        $where_opportunity4 =" ";
        $where_project2 =" ";
        $where_project3 =" ";
        $where_project4 =" ";
        $where_lead1 = " ";
        $where_lead2 = " ";
        $where_lead3 = " ";
        $where_lead4 = " ";
        $where_project_all =" ";
        if (isset($masterEmployee) && !empty($masterEmployee)) {
            $employeeID = join(",", $masterEmployee);
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

        if (isset($groupID) && !empty($groupID) && empty($masterEmployee)) {
            $formattedGroup = join(',', $groupID);
            $groupEmployees = $this->db->query("SELECT empID FROM srp_erp_crm_usergroupdetails gd where gd.groupMasterID IN ($formattedGroup)")->result_array();
            $employeearray  = [];
            if(!empty($groupEmployees)){
                foreach($groupEmployees as $gp){
                    array_push($employeearray,$gp['empID']);
                }
                $employeeID = join(',', $employeearray);
            }
        }

        if ($issuperadmin['isSuperAdmin'] == 1 || $isGroupAdmin['adminYN'] == 1 ) {
            if (isset($masterEmployee) && empty($masterEmployee) && empty($groupID)) {
                foreach ($yearWiseMonth as $index => $row) {
                    $contact = $this->db->query("SELECT COUNT(contactID) as totContact FROM srp_erp_crm_contactmaster WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();
                    $date = explode('AND', $row);
                    echo " <tr>";
                    echo " <td><div class='dataMonth'>" . $englishMonth[$index] . "</div></td>";


                    echo "<td class='dataContact'> <a style=\"cursor: pointer\"
                           onclick=\"totaldoccounts($date[0],$date[1])\"><div class='dataText'>" . $contact['totContact'] . "</div></a></td>";
                    $contact_total += $contact['totContact'];

                    $organization = $this->db->query("SELECT COUNT(organizationID) as totOrganization FROM srp_erp_crm_organizations WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();
                    $date = explode('AND', $row);

                    echo " <td class='dataOrganizations'><a style=\"cursor: pointer\"
                           onclick=\"totalcountorganizationview($date[0],$date[1])\"><div class='dataText'>" . $organization['totOrganization'] . "</div></a></td>";
                    $organization_total += $organization['totOrganization'];

                    $leads = $this->db->query("SELECT COUNT(leadID) as totLeads FROM srp_erp_crm_leadmaster WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();

                    echo "<td class='dataLeads'><div class='dataText'><a style=\"cursor: pointer\"
                           onclick=\"totalleadscount($date[0],$date[1])\"><div class='dataText'>" . $leads['totLeads'] . "</div></a></div></td>";
                    $leads_total += $leads['totLeads'];

                    $opportunities = $this->db->query("SELECT COUNT(opportunityID) as totOpportunities FROM srp_erp_crm_opportunity WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();
                    //echo $this->db->last_query();

                    echo "<td class='dataOpportunity'><a style=\"cursor: pointer\"
                           onclick=\"totaloppcount($date[0],$date[1])\"><div class='dataText'>" . $opportunities['totOpportunities'] . "</div></a></td>";
                    $opportunities_total += $opportunities['totOpportunities'];

                    $project = $this->db->query("SELECT COUNT(projectID) as totProjects FROM srp_erp_crm_project WHERE companyID = '$companyID' AND DATE(createdDateTime) BETWEEN $row")->row_array();

                    echo "<td class='dataProject'><a style=\"cursor: pointer\"
                           onclick=\"totalprojectcount($date[0],$date[1])\"><div class='dataText'>" . $project['totProjects'] . "</div></a></td>";
                    echo " </tr>";
                    $project_total += $project['totProjects'];
                }
            } else {
                foreach ($yearWiseMonth as $index => $row) {
                    $date = explode('AND', $row);

                    echo " <tr>";
                    echo " <td><div class='dataMonth'>" . $englishMonth[$index] . "</div></td>";

                    $where_count1 = "WHERE srp_erp_crm_contactmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_contactmaster.createdDateTime) BETWEEN $row";

                    /*$where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";

                    $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";

                    $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";*/




                    $contact = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster  $where_count1 GROUP BY contactID")->result_array();
                    //echo $this->db->last_query();
                    $contactValue = sizeof($contact);

                    echo "<td class='dataContact'> <a style=\"cursor: pointer\"
                           onclick=\"totaldoccounts($date[0],$date[1])\"><div class='dataText'>" . $contactValue . " </div></a></td>";
                    $contact_total += $contactValue;

                    $where_organization1 = "WHERE srp_erp_crm_organizations.companyID = '{$companyID}' AND DATE(srp_erp_crm_organizations.createdDateTime) BETWEEN $row";
                    /*$where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                    $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                    $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";*/

                   /* if(!empty($employeeID)){

                        $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                        $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                        $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";
                    }*/


                    $organization = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations $where_organization1 GROUP BY organizationID")->result_array();
                    $organizationValue = sizeof($organization);
                    echo "<td class='dataOrganizations'><a style=\"cursor: pointer\"
                           onclick=\"totalcountorganizationview($date[0],$date[1])\"><div class='dataText'>" . $organizationValue . "</div></a></td>";
                    $organization_total += $organizationValue;

                    $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";
                    $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

                    $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

                    $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";
                    if(!empty($employeeID))
                    {
                        $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";
                        $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

                        $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";

                        $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' $filteruserresponsibleID AND srp_erp_crm_leadmaster.createdDateTime BETWEEN $row";
                    }


                    $leads = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();

                    $leadsValue = sizeof($leads);
                    echo "<td class='dataLeads'><a style=\"cursor: pointer\"
                           onclick=\"totalleadscount($date[0],$date[1])\"><div class='dataText'>" . $leadsValue . "</div></a></td>";
                    $leads_total += $leadsValue;

                    $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";
                    $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3  AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    if(!empty($employeeID))
                    {
                        $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";
                        $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                        $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                        $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}' $filteropportuniID AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    }

                    $opportunities = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();

                    $opportunitiesValue = sizeof($opportunities);
                    echo "<td class='dataOpportunity'><a style=\"cursor: pointer\"onclick=\"totaloppcount($date[0],$date[1])\"><div class='dataText'>" . $opportunitiesValue . "</div></a></td>";
                    $opportunities_total += $opportunitiesValue;

                    $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";
                    $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";
                    if(!empty($employeeID))
                    {
                        $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";
                        $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                        $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                        $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' $filterprojectId AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";
                    }


                    $project = $this->db->query("SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project1 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project2 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_project3 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_project4 GROUP BY projectID")->result_array();

                    $projectValue = sizeof($project);
                    echo " <td class='dataProject'><a style=\"cursor: pointer\"onclick=\"totalprojectcount($date[0],$date[1])\"><div class='dataText'>" . $projectValue . "</div></a></td>";
                    $project_total += $projectValue;
                    echo " </tr>";

                }
            }
        } else {

            foreach ($yearWiseMonth as $index => $row) {
                $date = explode('AND', $row);
                echo " <tr>";
                echo " <td><div class='dataMonth'>" . $englishMonth[$index] . "</div></td>";


                    /*$where_count1 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";

                    $where_count2 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";

                    $where_count3 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";

                    $where_count4 = "WHERE srp_erp_crm_documentpermission.documentID = 6 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_contactmaster.companyID = '{$companyID}' AND srp_erp_crm_contactmaster.createdDateTime BETWEEN $row";


                $contact = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count1 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID $where_count2 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_count3 GROUP BY contactID UNION SELECT contactID FROM srp_erp_crm_contactmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_contactmaster.contactID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_count4 GROUP BY contactID ")->result_array();*/
                $where_count1 = "WHERE srp_erp_crm_contactmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_contactmaster.createdDateTime) BETWEEN $row";
                $contact = $this->db->query("SELECT contactID FROM srp_erp_crm_contactmaster  $where_count1 GROUP BY contactID")->result_array();

                //echo $this->db->last_query();
                $contactValue = sizeof($contact);

                echo "<td class='dataContact'><a style=\"cursor: pointer\"
                           onclick=\"totaldoccounts($date[0],$date[1])\"><div class='dataText'>" . $contactValue . "</div></a></td>";
                $contact_total += $contactValue;


                    /*$where_organization1 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                    $where_organization2 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                    $where_organization3 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                    $where_organization4 = "WHERE srp_erp_crm_documentpermission.documentID = 8 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_organizations.companyID = '{$companyID}' AND srp_erp_crm_organizations.createdDateTime BETWEEN $row";

                $organization = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization1 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID $where_organization2 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_organization3 GROUP BY organizationID UNION SELECT organizationID FROM srp_erp_crm_organizations LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_organizations.organizationID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_organization4 GROUP BY organizationID")->result_array();*/
                $where_organization1 = "WHERE srp_erp_crm_organizations.companyID = '{$companyID}' AND DATE(srp_erp_crm_organizations.createdDateTime) BETWEEN $row";
                $organization = $this->db->query("SELECT organizationID FROM srp_erp_crm_organizations $where_organization1 GROUP BY organizationID")->result_array();

                $organizationValue = sizeof($organization);
                echo "<td class='dataOrganizations'><a style=\"cursor: pointer\"
                           onclick=\"totalcountorganizationview($date[0],$date[1])\"><div class='dataText'>" . $organizationValue . "</div></a></td>";
                $organization_total += $organizationValue;

                if (isset($employeeID) && !empty($employeeID)) {
                    $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

                    $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

                    $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

                    $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                }else if ($permissiontype == 1)
                {
                    $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND (srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID = " . $currentuserID . ") AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND (srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID = " . $currentuserID . ") AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND (srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " or srp_erp_crm_leadmaster.createdUserID = " . $currentuserID . ") AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $leads = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();


                }else if ($permissiontype == 2)
                {
                    $where_lead1 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $where_lead2 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

                    $where_lead3 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";

                    $where_lead4 = "WHERE srp_erp_crm_documentpermission.documentID = 5 AND srp_erp_crm_documentpermission.permissionID = 4  AND srp_erp_crm_leadmaster.companyID = '{$companyID}' AND srp_erp_crm_leadmaster.responsiblePersonEmpID = " . $currentuserID . " AND DATE(srp_erp_crm_leadmaster.createdDateTime) BETWEEN $row";
                    $leads = $this->db->query("SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead1 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID $where_lead2 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_lead3 GROUP BY leadID UNION SELECT leadID FROM srp_erp_crm_leadmaster LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_leadmaster.leadID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_lead4 GROUP BY leadID")->result_array();
                }
                $leadsValue = sizeof($leads);
                echo "<td class='dataLeads'><a style=\"cursor: pointer\"
                           onclick=\"totalleadscount($date[0],$date[1])\"><div class='dataText'>" . $leadsValue . "</div></a></td>";
                $leads_total += $leadsValue;


                if (isset($employeeID) && !empty($employeeID)) {
                    $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";
                } else if ($permissiontype == 1) {
                    $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";

                    $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row";
                }else if ($permissiontype == 2)
                {
                    $where_opportunity1 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . " ";

                    $where_opportunity2 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

                    $where_opportunity3 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}'  AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";

                    $where_opportunity4 = "WHERE srp_erp_crm_documentpermission.documentID = 4 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_opportunity.companyID = '{$companyID}' AND DATE(srp_erp_crm_opportunity.createdDateTime) BETWEEN $row AND srp_erp_crm_opportunity.responsibleEmpID = " . $currentuserID . "";
                }
                $opportunities = $this->db->query("SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity1 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID $where_opportunity2 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_opportunity3 GROUP BY opportunityID UNION SELECT opportunityID FROM srp_erp_crm_opportunity LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_opportunity.opportunityID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_opportunity4 GROUP BY opportunityID")->result_array();
                //echo $this->db->last_query();

                $opportunitiesValue = sizeof($opportunities);
                echo "<td class='dataOpportunity'><a style=\"cursor: pointer\"onclick=\"totaloppcount($date[0],$date[1])\"><div class='dataText'>" . $opportunitiesValue . "</div></a></td>";
                $opportunities_total += $opportunitiesValue;

                if (isset($employeeID) && !empty($employeeID)) {
                    $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID IN ($employeeID) AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                } else if ($permissiontype == 1) {
                    $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";

                    $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row";
                    $where_project_all = " UNION SELECT projectID FROM srp_erp_crm_project WHERE srp_erp_crm_project.companyID = '{$companyID}' AND srp_erp_crm_project.createdUserID = '{$currentuserID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row  GROUP BY projectID ";

                }else if ($permissiontype == 2)
                {
                    $where_project1 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 1 AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . "";

                    $where_project2 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 2 AND srp_erp_crm_documentpermission.permissionValue = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . "";

                    $where_project3 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 3 AND srp_erp_crm_usergroupdetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . "";

                    $where_project4 = "WHERE srp_erp_crm_documentpermission.documentID = 9 AND srp_erp_crm_documentpermission.permissionID = 4 AND srp_erp_crm_documentpermissiondetails.empID = " . $currentuserID . " AND srp_erp_crm_project.companyID = '{$companyID}' AND DATE(srp_erp_crm_project.createdDateTime) BETWEEN $row AND srp_erp_crm_project.responsibleEmpID = " . $currentuserID . "";

                }
               $project = $this->db->query("SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project1 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID $where_project2 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_usergroupdetails ON srp_erp_crm_usergroupdetails.groupMasterID = srp_erp_crm_documentpermission.permissionValue $where_project3 GROUP BY projectID UNION SELECT projectID FROM srp_erp_crm_project LEFT JOIN srp_erp_crm_documentpermission ON srp_erp_crm_documentpermission.documentAutoID = srp_erp_crm_project.projectID LEFT JOIN srp_erp_crm_documentpermissiondetails ON srp_erp_crm_documentpermissiondetails.documentPermissionID = srp_erp_crm_documentpermission.documentPermissionID $where_project4 GROUP BY projectID $where_project_all")->result_array();
              // echo $this->db->last_query();


                $projectValue = sizeof($project);
                echo " <td class='dataProject'><a style=\"cursor: pointer\"onclick=\"totalprojectcount($date[0],$date[1])\"><div class='dataText'>" . $projectValue . "</div></a></td>";
                $project_total += $projectValue;
                echo " </tr>";
            }

        }
        ?>

    </tbody>
    <tfoot style="border-top: 1px double #0044cc;border-bottom: 1px double #0044cc;">
    <tr>
        <td>
            Total
        </td>
        <td style="text-align: center">
            <?php echo $contact_total; ?>
        </td>
        <td style="text-align: center">
            <?php echo $organization_total; ?>
        </td>
        <td style="text-align: center">
            <?php echo $leads_total; ?>
        </td>
        <td style="text-align: center">
            <?php echo $opportunities_total; ?>
        </td>
        <td style="text-align: center">
            <?php echo $project_total; ?>
        </td>
    </tr>
    </tfoot>
</table>