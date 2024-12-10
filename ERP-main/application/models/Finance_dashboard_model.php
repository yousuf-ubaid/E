<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Finance_dashboard_model extends CI_Model
{

    function __contruct()
    {

        parent::__contruct();
    }

    function getTotalRevenue($beginingDate, $endDate)
    {
        $company_type = $this->session->userdata("companyType");
        $company_id = $this->common_data['company_data']['company_id'];
        if($company_type == 1) {
            $query = "SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as totalRevenueLoc, SUM(srp_erp_generalledger.companyReportingAmount)*-1 as totalRevenue
                      FROM srp_erp_generalledger
                      JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
                      AND srp_erp_chartofaccounts.companyID = {$company_id}
                      WHERE srp_erp_generalledger.documentDate BETWEEN '{$beginingDate}' AND '{$endDate}' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 
                      AND srp_erp_generalledger.companyID = {$company_id}";
        }
        else {
            $query = "SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as totalRevenueLoc, SUM(srp_erp_generalledger.companyReportingAmount)*-1 as totalRevenue
                      FROM srp_erp_generalledger
                      JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
                      AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$company_id}')
                      WHERE srp_erp_generalledger.documentDate BETWEEN '{$beginingDate}' AND '{$endDate}' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND 
                      srp_erp_generalledger.companyID IN ( SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$company_id}' )";

        }
        $result = $this->db->query($query)->row_array();
        //echo $this->db->last_query();
        return $result["totalRevenue"];
    }

    function getNetProfit($beginingDate, $endDate)
    {
        $company_type = $this->session->userdata("companyType");
        $comapnyID = $this->common_data['company_data']['company_id'];

        if($company_type == 1)
        {
            $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as netProfitLoc,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as netProfit
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
INNER JOIN srp_erp_accountcategorytypes ON srp_erp_accountcategorytypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'])->row_array();
        }else
        {
            $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as netProfitLoc,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as netProfit
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
INNER JOIN srp_erp_accountcategorytypes ON srp_erp_accountcategorytypes.accountCategoryTypeID = srp_erp_chartofaccounts.accountCategoryTypeID
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_generalledger.companyID  IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')" )->row_array();
        }

        //echo $this->db->last_query();
        return $result["netProfit"];
    }

    function getOverallPerformance($beginingDate, $endDate, $months)
    {
        $company_type = $this->session->userdata("companyType");
        $comapnyID = $this->common_data['company_data']['company_id'];
        $feilds = "";
        if (!empty($months)) {
            foreach ($months as $key => $val2) {
                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger.documentDate,'%Y-%m') = '$key',srp_erp_generalledger.companyReportingAmount * -1,0) ) as `" . $val2 . "`,";
            }
        }
        if($company_type==1)
        {
            $sql = "SELECT $feilds 'Revenue' as description
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT $feilds 'COGS' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT $feilds 'Other Expense' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT $feilds 'GP' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'];
        }else
        {
            $sql = "SELECT $feilds 'Revenue' as description
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
	UNION 
	SELECT $feilds 'COGS' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
	UNION 
	SELECT $feilds 'Other Expense' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
	UNION 
	SELECT $feilds 'GP' as description 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}')
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$comapnyID}') ";
        }

        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function getPerformanceSummary($beginingDate, $endDate)
    {
        $sql = "SELECT 'Revenue' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . " 
	UNION 
	SELECT 'COGS' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 12 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT 'Other Expense' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 13 AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION 
	SELECT 'Gross Profit' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND (srp_erp_chartofaccounts.accountCategoryTypeID = 11 OR srp_erp_chartofaccounts.accountCategoryTypeID = 12) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'] . "
	UNION
	SELECT 'Net Profit' as description,SUM(companyLocalAmount)*-1 as amountLoc,SUM(companyReportingAmount)*-1 as amount 
FROM
	srp_erp_generalledger
INNER JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' AND srp_erp_chartofaccounts.companyID = " . $this->common_data['company_data']['company_id'] . "
WHERE
	srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID IN(11,12,13,14,15) AND srp_erp_generalledger.companyID = " . $this->common_data['company_data']['company_id'];
        $result = $this->db->query($sql)->result_array();
        return $result;
    }


    function getRevenueDetailAnalysis($beginingDate, $endDate)
    {
        $company_id = current_companyID();
       /* $sql = "SELECT
                    SUM((((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyLocalExchangeRate)) as companyLocalAmount,
                    SUM((((transactionQTY * -1) * salesPrice)/srp_erp_itemledger.companyReportingExchangeRate)) as companyReportingAmount,
                    srp_erp_itemcategory.description as subCategory,
                    srp_erp_itemcategory.itemCategoryID,
                    srp_erp_itemledger.companyLocalCurrencyDecimalPlaces,
                    srp_erp_itemledger.companyReportingCurrencyDecimalPlaces
                FROM 
                    srp_erp_itemledger
                JOIN srp_erp_itemmaster ON srp_erp_itemledger.itemAutoID = srp_erp_itemmaster.itemAutoID  AND srp_erp_itemmaster.mainCategory = 'Inventory'
                JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID
                WHERE 
                    srp_erp_itemledger.companyID = {$company_id}  
                    AND srp_erp_itemledger.documentCode IN('CINV','DN','RV') 
                    AND srp_erp_itemledger.documentDate BETWEEN '$beginingDate' AND '$endDate'
                GROUP BY 
                    srp_erp_itemmaster.subcategoryID 
                ORDER BY 
                    companyReportingAmount DESC";*/

        $sql = "SELECT
                    SUM( companyLocalAmount ) AS companyLocalAmount,
                    SUM( companyReportingAmount ) AS companyReportingAmount,
                    subCategory,
                    itemCategoryID,
                    companyLocalCurrencyDecimalPlaces,
                    companyReportingCurrencyDecimalPlaces 
                FROM(
                    SELECT
                        SUM((((transactionQTY * - 1) * salesPrice)/srp_erp_itemledger.companyLocalExchangeRate)) AS companyLocalAmount,
                        SUM((((transactionQTY * - 1) * salesPrice)/ srp_erp_itemledger.companyReportingExchangeRate)) AS companyReportingAmount,
                        srp_erp_itemcategory.description AS subCategory,
                        srp_erp_itemcategory.itemCategoryID,
                        srp_erp_itemledger.companyLocalCurrencyDecimalPlaces,
                        srp_erp_itemledger.companyReportingCurrencyDecimalPlaces,
                        IFNULL( posMasterAutoID, 0 ) AS isFromPOS 
                    FROM
                        srp_erp_itemledger
                        JOIN srp_erp_itemmaster ON srp_erp_itemledger.itemAutoID = srp_erp_itemmaster.itemAutoID 
                        AND srp_erp_itemmaster.mainCategory = 'Inventory'
                        LEFT JOIN srp_erp_customerinvoicemaster ON srp_erp_customerinvoicemaster.invoiceAutoID = srp_erp_itemledger.documentAutoID 
                        AND srp_erp_itemledger.documentID = srp_erp_customerinvoicemaster.documentID
                        JOIN srp_erp_itemcategory ON srp_erp_itemcategory.itemCategoryID = srp_erp_itemmaster.subcategoryID 
                    WHERE
                        srp_erp_itemledger.companyID = {$company_id} 
                        AND srp_erp_itemledger.documentCode IN ( 'CINV', 'DN', 'RV' ) 
                        AND srp_erp_itemledger.documentDate BETWEEN '$beginingDate' AND '$endDate'
                    GROUP BY
                        srp_erp_itemmaster.subcategoryID 
                    HAVING
                        isFromPOS = 0 UNION ALL
                    SELECT
                        SUM( srp_erp_pos_invoicedetail.companyLocalAmount ) AS companyLocalAmount,
                        SUM( srp_erp_pos_invoicedetail.companyReportingAmount ) AS companyReportingExchangeRate,
                        srp_erp_itemcategory.description AS subCategory,
                        srp_erp_itemcategory.itemCategoryID,
                        srp_erp_pos_invoice.companyLocalCurrencyDecimalPlaces,
                        srp_erp_pos_invoice.companyReportingCurrencyDecimalPlaces,
                        0 AS isFromPOS 
                    FROM
                        srp_erp_pos_invoice
                        LEFT JOIN srp_erp_pos_invoicedetail ON srp_erp_pos_invoice.invoiceID = srp_erp_pos_invoicedetail.invoiceID
                        LEFT JOIN srp_erp_itemmaster ON srp_erp_pos_invoicedetail.itemAutoID = srp_erp_itemmaster.itemAutoID
                        LEFT JOIN srp_erp_itemcategory ON srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID 
                    WHERE
                        srp_erp_pos_invoice.companyID = {$company_id} 
                        AND srp_erp_pos_invoice.createdDateTime BETWEEN '$beginingDate' AND '$endDate'
                        AND srp_erp_pos_invoice.isVoid = 0 
                    GROUP BY
                        subcategoryID 
                    ) tble 
                GROUP BY
                    itemCategoryID 
                ORDER BY
                    companyReportingAmount";
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function getAssignedDashboard()
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();
        $companyType = $this->session->userdata("companyType");
        if($companyType == 1)
        {
            $this->db->select('srp_erp_userdashboardmaster.userDashboardID,srp_erp_userdashboardmaster.dashboardDescription,srp_erp_dashboardtemplate.pageName');
            $this->db->where('employeeID', current_userID());
            $this->db->where('isGroupYN!=', 2);
            $this->db->join('srp_erp_dashboardtemplate', 'srp_erp_dashboardtemplate.templateID = srp_erp_userdashboardmaster.templateID');
            $this->db->order_by('srp_erp_userdashboardmaster.sortOrder');
            $result = $this->db->get('srp_erp_userdashboardmaster')->result_array();
        }else {
            $this->db->select('srp_erp_userdashboardmaster.userDashboardID,srp_erp_userdashboardmaster.dashboardDescription,srp_erp_dashboardtemplate.pageName');
            $this->db->where('employeeID', current_userID());
            $this->db->where('isGroupYN!=', 1);
            $this->db->join('srp_erp_dashboardtemplate', 'srp_erp_dashboardtemplate.templateID = srp_erp_userdashboardmaster.templateID');
            $this->db->order_by('srp_erp_userdashboardmaster.sortOrder');
            $result = $this->db->get('srp_erp_userdashboardmaster')->result_array();


        }

        //echo $this->db->last_query();
        $data["dashboard"] = $result;
        return $data;
    }

    function getAssignedDashboardWidget()
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $usergroup = $this->db->get('srp_erp_employeenavigation')->row_array();
        $companyType = $this->session->userdata("companyType");
        if($companyType == 1)
        {
            $this->db->select('*');
            $this->db->where('employeeID', current_userID());
            $this->db->where('srp_erp_userdashboardwidget.userDashboardID', $this->input->post("userDashboardID"));
            $this->db->join('srp_erp_widgetmaster', 'srp_erp_widgetmaster.widgetID = srp_erp_userdashboardwidget.widgetID');
            $this->db->join('srp_erp_widgetposition', 'srp_erp_widgetposition.widgetPositionID = srp_erp_userdashboardwidget.positionID');
            $this->db->join('(SELECT * FROM srp_erp_usergroupwidget WHERE userGroupID = ' . $usergroup['userGroupID'] . ' AND companyID = ' . current_companyID() . ') as ugw', 'ugw.widgetID = srp_erp_userdashboardwidget.widgetID');
            $this->db->order_by('srp_erp_userdashboardwidget.userDashboardID asc,srp_erp_userdashboardwidget.sortOrder asc');
            $result = $this->db->get('srp_erp_userdashboardwidget')->result_array();
        }else
        {
            $this->db->select('*');
            $this->db->where('employeeID', current_userID());
            $this->db->where('srp_erp_userdashboardwidget.userDashboardID', $this->input->post("userDashboardID"));
            $this->db->join('srp_erp_widgetmaster', 'srp_erp_widgetmaster.widgetID = srp_erp_userdashboardwidget.widgetID');
            $this->db->join('srp_erp_widgetposition', 'srp_erp_widgetposition.widgetPositionID = srp_erp_userdashboardwidget.positionID');
           /* $this->db->join('(SELECT * FROM srp_erp_usergroupwidget WHERE companyID = ' . current_companyID() . ') as ugw', 'ugw.widgetID = srp_erp_userdashboardwidget.widgetID');*/
            $this->db->order_by('srp_erp_userdashboardwidget.userDashboardID asc,srp_erp_userdashboardwidget.sortOrder asc');
            $result = $this->db->get('srp_erp_userdashboardwidget')->result_array();
        }



        $data["dashboardWidget"] = $result;
        return $data;

    }

    /*Started Function*/
    function getShortcutLinks()
    {
        $this->db->select('*');
        $this->db->where('EIdNo', current_userID());
        $this->db->where('isPublic', 0);
        $result = $this->db->get('srp_erp_dashboard_links')->result_array();
        return $result;

    }

    function save_private_link()
    {

        $this->db->set('EIdNo', current_userID());
        $this->db->set('isPublic', 0);
        $this->db->set('title', $this->input->post("description"));
        $this->db->set('hyperlink', $this->input->post("hyperlink"));
        $this->db->set('description', $this->input->post("description"));
        $this->db->set('createdUserID', current_userID());
        $this->db->set('createdPc', $this->common_data['current_pc']);
        $this->db->set('createdDatetime', $this->common_data['current_date']);
        $results = $this->db->insert('srp_erp_dashboard_links');
        if ($results) {
            return array('s', 'Link Added Successfully');
        } else {
            return array('e', 'Error In Adding Link');
        }
    }

    function deletePrivateLink()
    {
        $results = $this->db->delete('srp_erp_dashboard_links', array('linkID' => trim($this->input->post('linkID') ?? '')));
        if ($results) {
            return array('s', 'Link Deleted Successfully');
        } else {
            return array('e', 'Error In Deleting Link');
        }
    }

    function getPublicLinks()
    {

        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $userGroupID = $this->db->get('srp_erp_employeenavigation')->row_array();
        if ($userGroupID) {
            $usergrpID = $userGroupID['userGroupID'];
            $userID = current_userID();
            $companyID = current_companyID();

            $result = $this->db->query("SELECT
	lm.*, lm.description as masterdesc ,IFNULL(srp_erp_dashboard_links.linkMasterID,0) as linkMasterID,srp_erp_dashboard_links.description as dtldesc
FROM
	srp_erp_dashboard_links_master lm
LEFT JOIN srp_erp_dashboard_links ON srp_erp_dashboard_links.linkMasterID = lm.linkID AND srp_erp_dashboard_links.EIdNo = $userID
INNER JOIN srp_erp_navigationmenus ON lm.hyperlink = srp_erp_navigationmenus.url and lm.pageID = srp_erp_navigationmenus.pageID
INNER JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
AND srp_erp_navigationusergroupsetup.userGroupID = $usergrpID Order By lm.type")->result_array();
        } else {
            $result = '';
        }
        return $result;
    }

    function getPublicList()
    {
        $this->db->select('userGroupID');
        $this->db->where('empID', current_userID());
        $this->db->where('companyID', current_companyID());
        $userGroupID = $this->db->get('srp_erp_employeenavigation')->row_array();
        if ($userGroupID) {
            $usergrpID = $userGroupID['userGroupID'];
            $userID = current_userID();
            $companyID = current_companyID();

            $result = $this->db->query("SELECT
	srp_erp_dashboard_links.*, srp_erp_dashboard_links_master.type,
	IFNULL(
		srp_erp_navigationusergroupsetup.UserGroupSetupID,
		0
	) AS UserGroupSetupID, srp_erp_dashboard_links_master.pageID
FROM
	`srp_erp_dashboard_links`
LEFT JOIN srp_erp_dashboard_links_master ON srp_erp_dashboard_links.linkMasterID = srp_erp_dashboard_links_master.linkID
LEFT JOIN srp_erp_navigationmenus ON srp_erp_dashboard_links.hyperlink = srp_erp_navigationmenus.url AND srp_erp_dashboard_links_master.pageID = srp_erp_navigationmenus.pageID
LEFT JOIN srp_erp_navigationusergroupsetup ON srp_erp_navigationmenus.navigationMenuID = srp_erp_navigationusergroupsetup.navigationMenuID
AND srp_erp_navigationusergroupsetup.userGroupID = $usergrpID
WHERE
	srp_erp_dashboard_links.isPublic = - 1
AND EIdNo = $userID")->result_array();
        } else {
            $result = '';
        }
        return $result;

    }

    function save_public_link()
    {
        $results = $this->db->delete('srp_erp_dashboard_links', array('EIdNo' => current_userID(), 'isPublic' => -1));
        if ($results) {
            $description = $this->input->post('description');
            if (!empty($this->input->post('widgetCheck'))) {
                foreach ($this->input->post('widgetCheck') as $key => $val) {
                    $this->db->select('linkID,title,hyperlink,description');
                    $this->db->where('linkID', $val);
                    $link = $this->db->get('srp_erp_dashboard_links_master')->row_array();
                    if ($link) {
                        $this->db->set('EIdNo', current_userID());
                        $this->db->set('isPublic', -1);
                        $this->db->set('linkMasterID', $val);
                        $this->db->set('title', $link['title']);
                        $this->db->set('hyperlink', $link['hyperlink']);
                        $this->db->set('description', $description[$key]);
                        $this->db->set('createdUserID', current_userID());
                        $this->db->set('createdPc', $this->common_data['current_pc']);
                        $this->db->set('createdDatetime', $this->common_data['current_date']);
                        $result = $this->db->insert('srp_erp_dashboard_links');
                    }
                }
            } else {
                return array('s', 'Link Added Successfully');
            }
            if ($result) {
                return array('s', 'Link Added Successfully');
            } else {
                return array('e', 'Error In Adding Link');
            }
        }

    }

    /*End Function*/

    function getRevenueDetailAnalysisByGLcode($beginingDate, $endDate)
    {
        $company_id = $this->common_data['company_data']['company_id'];
        if($this->session->userdata("companyType") == 1) {
            $query = "SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as companyLocalAmount,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as companyReportingAmount,
                      srp_erp_chartofaccounts.GLDescription,srp_erp_generalledger.GLAutoID
                      FROM srp_erp_generalledger
                      JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
                      AND srp_erp_chartofaccounts.companyID = {$company_id}
                      WHERE srp_erp_generalledger.documentDate BETWEEN '{$beginingDate}' AND '{$endDate}' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 
                      AND srp_erp_generalledger.companyID = {$company_id} GROUP BY srp_erp_generalledger.GLAutoID ORDER BY companyReportingAmount DESC";
        }
        else {
            $query = "SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as companyLocalAmount,SUM(srp_erp_generalledger.companyReportingAmount)*-1 as companyReportingAmount,
                      srp_erp_chartofaccounts.GLDescription,srp_erp_generalledger.GLAutoID
                      FROM srp_erp_generalledger
                      JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
                      AND srp_erp_chartofaccounts.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$company_id}')
                      WHERE srp_erp_generalledger.documentDate BETWEEN '$beginingDate' AND '$endDate' AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 
                      AND srp_erp_generalledger.companyID IN (SELECT companyID FROM `srp_erp_companygroupdetails` where companyGroupID = '{$company_id}') 
                      GROUP BY srp_erp_generalledger.GLAutoID ORDER BY companyReportingAmount DESC";
        }
        $result = $this->db->query($query)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*Started Function*/
    function getNewMembers()
    {
        $cmpid = current_companyID();
        $result = $this->db->query("

SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
Limit 10
;")->result_array();
        return $result;
    }


    function save_to_do_list()
    {
        $datFormat = date('Y-m-d', strtotime($this->input->post('startDate')));
        $this->db->set('employeeId', current_userID());
        $this->db->set('companyId', current_companyID());
        $this->db->set('description', $this->input->post('description'));
        $this->db->set('startDate', $datFormat);
        $this->db->set('startTime', $this->input->post('startTime'));
        $this->db->set('priority', $this->input->post('priority'));
        $this->db->set('createdDateTime', $this->common_data['current_date']);
        $this->db->set('isDeleted', 0);
        $this->db->set('modifiedDateTime', $this->common_data['current_date']);
        $this->db->set('DeletedDateTime', $this->common_data['current_date']);
        $this->db->set('deletedByEmpID', $this->common_data['current_userID']);
        $result = $this->db->insert('srp_erp_to_do_list');

        if ($result) {
            return array('s', 'Record added successfully');
        } else {
            return array('e', 'Error in adding record');
        }
    }

    function getToDoList()
    {
        $cmpid = current_companyID();
        $empid = current_userID();
        return $this->db->query("SELECT srp_erp_to_do_list.*,srp_erp_priority_master.priorityDescription FROM `srp_erp_to_do_list` LEFT JOIN srp_erp_priority_master on srp_erp_to_do_list.priority = srp_erp_priority_master.priorityID WHERE  employeeId = '$empid' AND companyId = '$cmpid' AND isCompleated = 0 AND isDeleted = 0 ORDER BY srp_erp_to_do_list.autoId ASC;")->result_array();
    }

    function check_to_do_list()
    {
        $curdate = $this->common_data['current_date'];
        $data['isCompleated'] = $this->input->post('checked');
        if ($this->input->post('checked') == -1) {
            $data['endDate'] = date('Y-m-d', strtotime($curdate));
        } else {
            $data['endDate'] = NULL;
        }
        $data['modifiedDateTime'] = $this->common_data['current_date'];

        $this->db->where('autoId', trim($this->input->post('autoId') ?? ''));
        $results = $this->db->update('srp_erp_to_do_list', $data);
        if ($results) {
            return array('s', 'Record updated successfully');
        } else {
            return array('e', 'Error in updating record');
        }
    }

    function deletetodoList()
    {
        $data['autoId'] = trim($this->input->post('autoId') ?? '');
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $data['isDeleted'] = 1;
        $this->db->where('autoId', $data['autoId']);
        $this->db->update('srp_erp_to_do_list', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'To Do List Update Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Record Deleted Successfully.');
        }
        //$results = $this->db->delete('srp_erp_to_do_list', array('autoId' => trim($this->input->post('autoId') ?? '')));
       // if ($results) {
         //   return array('s', 'Record deleted successfully');
       // } else {
       //     return array('e', 'Error in deleting record');
       // }
    }

    function getToDoListHistory()
    {
        $cmpid = current_companyID();
        $empid = current_userID();
        $result = $this->db->query("SELECT srp_erp_to_do_list.*,srp_erp_priority_master.priorityDescription FROM `srp_erp_to_do_list` LEFT JOIN srp_erp_priority_master on srp_erp_to_do_list.priority = srp_erp_priority_master.priorityID WHERE startDate <= CURDATE() AND employeeId = '$empid' AND companyId = '$cmpid' AND isCompleated = -1 AND isDeleted = 0 ORDER BY srp_erp_to_do_list.autoId ASC;")->result_array();
        return $result;
    }

    /*End Function*/

    function getTotalSalesLog()
    {
        $currentYear = date("Y");
        $lastYear = date("Y", strtotime("-1 year"));
        $beginingDate = "";
        $beginingDateLast = "";
        $endDate = "";
        $endDateLast = "";
        $period = $this->input->post("period");
        $lastTwoYears = get_last_two_financial_year();
        if (!empty($lastTwoYears)) {
            $beginingDate = $lastTwoYears[$period]["beginingDate"];
            $endDate = $lastTwoYears[$period]["endingDate"];
            $beginingDateLast = isset($lastTwoYears[$period + 1]["beginingDate"]) ? $lastTwoYears[$period + 1]["beginingDate"] : '';
            $endDateLast = isset($lastTwoYears[$period + 1]["endingDate"]) ? $lastTwoYears[$period + 1]["endingDate"] : '';
        }

        $result = $this->db->query('SELECT sum(if(documentDate >= \'' . $beginingDate . '\' AND documentDate <= \'' . $endDate . '\',companyReportingAmount,0)) * -1 as currentYear,SUM(if(documentDate >= \'' . $beginingDateLast . '\' AND documentDate <= \'' . $endDateLast . '\',companyReportingAmount,0))* -1 as lastYear,DecimalPlaces FROM `srp_erp_generalledger` INNER JOIN `srp_erp_chartofaccounts` ON `srp_erp_generalledger`.`GLAutoID` = `srp_erp_chartofaccounts`.`GLAutoID` AND `srp_erp_chartofaccounts`.`masterCategory` = "PL" AND `srp_erp_chartofaccounts`.`companyID` = ' . $this->common_data['company_data']['company_id'] . ' LEFT JOIN `srp_erp_customermaster` ON `srp_erp_customermaster`.`customerAutoID` = `srp_erp_generalledger`.`partyAutoID` LEFT JOIN `srp_erp_currencymaster` ON `srp_erp_currencymaster`.`currencyID` = `srp_erp_generalledger`.`companyReportingCurrencyID` WHERE `srp_erp_chartofaccounts`.`accountCategoryTypeID` = 11 AND `srp_erp_generalledger`.`companyID` =  ' . $this->common_data['company_data']['company_id'] . ' AND `srp_erp_generalledger`.`partyType` = "CUS"')->row_array();
        return $result;
    }

    function getRestNewMembers($pageId)
    {
        $row = $pageId * 10;
        $cmpid = current_companyID();
        $result = $this->db->query("
SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
Limit $row,$row
;")->result_array();
        return $result;
    }


    function getAllNewMembers()
    {
        $cmpid = current_companyID();
        $result = $this->db->query("SELECT
    EIdNo,
    Ename2,
    EmpImage,
    srp_designation.DesDescription,
    (CURDATE() - INTERVAL 1 MONTH) AS onemonth,
    srp_employeesdetails.EDOJ,
    DATE(srp_employeesdetails.EDOJ) AS datecreated
FROM
    `srp_employeesdetails`
LEFT JOIN srp_designation ON srp_employeesdetails.EmpDesignationId = srp_designation.DesignationID
WHERE
    srp_employeesdetails.isDeleted = 0
    AND isSystemAdmin = 0
AND isActive = 1
AND srp_employeesdetails.Erp_companyID = '$cmpid'
HAVING
    EDOJ BETWEEN onemonth and CURDATE()
;")->result_array();
        return $result;
    }

    function getRevenueDetailAnalysisBySegment($beginingDate, $endDate)
    {
        $company_type = $this->session->userdata("companyType");
        $company_id = $this->common_data['company_data']['company_id'];
        if($company_type == 1) {
            $result = $this->db->query("SELECT SUM(srp_erp_generalledger.companyLocalAmount)*-1 as companyLocalAmount,
                        SUM(srp_erp_generalledger.companyReportingAmount)*-1 as companyReportingAmount, srp_erp_segment.description,srp_erp_generalledger.GLAutoID
                        FROM srp_erp_generalledger
                        JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
                        JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID AND srp_erp_segment.companyID = {$company_id}
                        WHERE srp_erp_generalledger.documentDate BETWEEN '{$beginingDate}' AND '{$endDate}' AND srp_erp_generalledger.companyID = {$company_id} 
                        AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 
                        GROUP BY srp_erp_generalledger.segmentID")->result_array();
        }
        else {
            $result = $this->db->query("SELECT SUM( srp_erp_generalledger.companyLocalAmount ) *- 1 AS companyLocalAmount, 
	                        SUM( srp_erp_generalledger.companyReportingAmount ) *- 1 AS companyReportingAmount, srp_erp_groupsegment.description, srp_erp_generalledger.GLAutoID 
	                        FROM srp_erp_generalledger
	                        JOIN srp_erp_segment ON srp_erp_generalledger.segmentID = srp_erp_segment.segmentID 
	                        JOIN srp_erp_groupsegmentdetails on srp_erp_groupsegmentdetails.segmentID = srp_erp_segment.segmentID 
	                        JOIN srp_erp_groupsegment ON srp_erp_groupsegment.segmentID = srp_erp_groupsegmentdetails.groupSegmentID 
	                        JOIN srp_erp_chartofaccounts ON srp_erp_generalledger.GLAutoID = srp_erp_chartofaccounts.GLAutoID AND srp_erp_chartofaccounts.masterCategory = 'PL' 
	                        WHERE srp_erp_generalledger.documentDate BETWEEN '{$beginingDate}' AND '{$endDate}'
	                        AND srp_erp_generalledger.companyID IN ( SELECT companyID FROM `srp_erp_companygroupdetails` WHERE companyGroupID = {$company_id} ) 
	                        AND srp_erp_chartofaccounts.accountCategoryTypeID = 11 GROUP BY srp_erp_groupsegment.segmentID")->result_array();
        }
        //echo $this->db->last_query();
        return $result;
    }

    function updatePBLink()
    {
        $linkID = $this->input->post('linkID');
        $description = $this->input->post('description');
        $valu = $this->input->post('valu');
        if ($valu == 1) {
            $this->db->select('linkID,title,hyperlink,description');
            $this->db->where('linkID', $linkID);
            $link = $this->db->get('srp_erp_dashboard_links_master')->row_array();
            if ($link) {
                $this->db->set('EIdNo', current_userID());
                $this->db->set('isPublic', -1);
                $this->db->set('linkMasterID', $linkID);
                $this->db->set('title', $link['title']);
                $this->db->set('hyperlink', $link['hyperlink']);
                $this->db->set('description', $description);
                $this->db->set('createdUserID', current_userID());
                $this->db->set('createdPc', $this->common_data['current_pc']);
                $this->db->set('createdDatetime', $this->common_data['current_date']);
                $result = $this->db->insert('srp_erp_dashboard_links');
                if ($result) {
                    return array('s', 'Record updated successfully');
                } else {
                    return array('e', 'Updating failed');
                }
            }
        } else {
            $result = $this->db->delete('srp_erp_dashboard_links', array('EIdNo' => current_userID(), 'linkMasterID' => $linkID));
            if ($result) {
                return array('s', 'Record updated successfully');
            } else {
                return array('e', 'Updating failed');
            }
        }

    }

    function updateLinkDescription()
    {
        $linkID = $this->input->post('linkID');
        $description = $this->input->post('description');


        $this->db->select('linkID');
        $this->db->where('linkMasterID', $linkID);
        $this->db->where('EIdNo', current_userID());
        $link = $this->db->get('srp_erp_dashboard_links')->row_array();
        if (!empty($link)) {
            $data['description'] = $description;

            $this->db->where('EIdNo', current_userID());
            $this->db->where('linkMasterID', $linkID);
            $result = $this->db->update('srp_erp_dashboard_links', $data);
            if ($result) {
                return array('s', 'Record updated successfully');
            } else {
                return array('e', 'Updating failed');
            }
        } else {
            return array('e', 'Updating failed');
        }


    }

    function sync_details_group_rpt()
    {




            $this->db->trans_commit();
            $companyID = current_companyID();
            $userID = current_userID();
            $db2 = $this->load->database('db2', TRUE);
            $db2->select('EidNo,groupID');
            $db2->where("companyID", $companyID);
            $db2->where("empID", $userID);
            $groupdetails = $db2->get("user")->row_array();
            $dbname = $db2->query("SELECT srp_erp_company.db_name,srp_erp_company.host,srp_erp_company.db_username,srp_erp_company.db_password FROM `groupusercompanies` LEFT JOIN srp_erp_company ON srp_erp_company.company_id = groupusercompanies.companyID WHERE groupID  ='{$groupdetails['groupID']}'")->result_array();
            $output = array();
            foreach ($dbname as $val) {
                $dbnames = trim($this->encryption->decrypt($val["db_name"]));
                $output[] = $dbnames;

            }


            $output = array_unique($output);

            $empdetailTru = $this->db->query("DELETE FROM srp_employeesdetails_groupmonitoring");

            if ($empdetailTru == true)
            {
                $chartofaccTru = $this->db->query("DELETE FROM srp_erp_chartofaccounts_groupmonitoring");
            }
            if($chartofaccTru == true)
            {
                $generalLedgerTru = $this->db->query("DELETE FROM srp_erp_generalledger_groupmonitoring");
            }
            if($generalLedgerTru == true)
            {
                $groupcompany = $this->db->query("DELETE FROM srp_erp_company_groupmonitoring");
            }

            if(($groupcompany==true)) {
                foreach ($output as $val1) {
                    $this->db->query("INSERT INTO srp_employeesdetails_groupmonitoring (EIdNo,serialNo,ECode,EmpSecondaryCode,EmpTitleId,manPowerNo,ssoNo,EmpDesignationId,Ename1,Ename2,Ename3,Ename4,empSecondName,EFamilyName,initial,EmpShortCode,Enameother1,Enameother2,Enameother3,Enameother4,empSecondNameOther,EFamilyNameOther,empSignature,EmpImage,Gender,EpAddress1,EpAddress2,EpAddress3,EpAddress4,ZipCode,EpTelephone,EpFax,EpMobile,EcAddress1,EcAddress2,EcAddress3,EcAddress4,EcPOBox,EcPC,EcArea,EcTel,EcExtension,EcFax,EcMobile,EEmail,personalEmail,EDOB,EDOJ,NIC,insuranceNo,EPassportNO,EPassportExpiryDate,EVisaExpiryDate,Nid,Rid,AirportDestination,travelFrequencyID,commissionSchemeID,medicalInfo,SchMasterId,branchID,UserName,Password,isDeleted,HouseID,HouseCatID,HPID,isPayrollEmployee,payCurrencyID,payCurrency,isLeft,DateLeft,LeftComment,BloodGroup,DateAssumed,probationPeriod,isDischarged,dischargedByEmpID,EmployeeConType,dischargedDate,lastWorkingDate,dischargedComment,finalSettlementDoneYN,MaritialStatus,Nationality,isLoginAttempt,isChangePassword,CreatedUserName,CreatedDate,CreatedPC,ModifiedUserName,Timestamp,ModifiedPC,isActive,NoOfLoginAttempt,languageID,locationID,segmentID,Erp_companyID,floorID,empMachineID,leaveGroupID,isCheckin,token,overTimeGroup,familyStatusID,gratuityID,isSystemAdmin,isHRAdmin,contractStartDate,contractEndDate,contractRefNo,empConfirmDate,empConfirmedYN,rejoinDate,previousEmpID,pos_userGroupMasterID,pos_barCode,isLocalPosSyncEnable,isLocalPosSalesRptEnable)
         SELECT
	EIdNo,
	serialNo,
	ECode,
	EmpSecondaryCode,
	EmpTitleId,
	manPowerNo,
	ssoNo,
	EmpDesignationId,
	Ename1,
	Ename2,
	Ename3,
	Ename4,
	empSecondName,
	EFamilyName,
	initial,
	EmpShortCode,
	Enameother1,
	Enameother2,
	Enameother3,
	Enameother4,
	empSecondNameOther,
	EFamilyNameOther,
	empSignature,
	EmpImage,
	Gender,
	EpAddress1,
	EpAddress2,
	EpAddress3,
	EpAddress4,
	ZipCode,
	EpTelephone,
	EpFax,
	EpMobile,
	EcAddress1,
	EcAddress2,
	EcAddress3,
	EcAddress4,
	EcPOBox,
	EcPC,
	EcArea,
	EcTel,
	EcExtension,
	EcFax,
	EcMobile,
	EEmail,
	personalEmail,
	EDOB,
	EDOJ,
	NIC,
	insuranceNo,
	EPassportNO,
	EPassportExpiryDate,
	EVisaExpiryDate,
	srp_nationality.countryID as Nid,
	Rid,
	AirportDestination,
	travelFrequencyID,
	commissionSchemeID,
	medicalInfo,
	$val1.srp_employeesdetails.SchMasterId,
	$val1.srp_employeesdetails.branchID,
	UserName,
	PASSWORD,
	isDeleted,
	HouseID,
	HouseCatID,
	HPID,
	isPayrollEmployee,
	payCurrencyID,
	payCurrency,
	isLeft,
	DateLeft,
	LeftComment,
	BloodGroup,
	DateAssumed,
	probationPeriod,
	isDischarged,
	dischargedByEmpID,
	EmployeeConType,
	dischargedDate,
	lastWorkingDate,
	dischargedComment,
	finalSettlementDoneYN,
	MaritialStatus,
	srp_employeesdetails.Nationality,
	isLoginAttempt,
	isChangePassword,
	$val1.srp_employeesdetails.CreatedUserName,
	$val1.srp_employeesdetails.CreatedDate,
	$val1.srp_employeesdetails.CreatedPC,
	$val1.srp_employeesdetails.ModifiedUserName,
	$val1.srp_employeesdetails.TIMESTAMP,
	srp_employeesdetails.ModifiedPC,
	isActive,
	NoOfLoginAttempt,
	languageID,
	locationID,
	segmentID,
	srp_employeesdetails.Erp_companyID,
	floorID,
	empMachineID,
	leaveGroupID,
	isCheckin,
	token,
	overTimeGroup,
	familyStatusID,
	gratuityID,
	isSystemAdmin,
	isHRAdmin,
	contractStartDate,
	contractEndDate,
	contractRefNo,
	empConfirmDate,
	empConfirmedYN,
	rejoinDate,
	previousEmpID,
	pos_userGroupMasterID,
	pos_barCode,
	isLocalPosSyncEnable,
	isLocalPosSalesRptEnable 
FROM
	$val1.srp_employeesdetails
	LEFT JOIN srp_nationality on srp_nationality.NId = srp_employeesdetails.Nid");
                    $this->db->query("INSERT INTO srp_erp_chartofaccounts_groupmonitoring (GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterAccountYN,masterAutoID,masterAccount,masterAccountDescription,masterCategory,levelNo,accountCategoryTypeID,CategoryTypeDescription,subCategory,controllAccountYN,isActive,accountDefaultType,isAuto,isCard,isBank,isCash,replicatedCoaID,bankName,bankBranch,bankShortCode,bankSwiftCode,bankCheckNumber,authourizedSignatureLevel,bankAccountNumber,bankCurrencyID,bankCurrencyCode,bankCurrencyDecimalPlaces,confirmedYN,confirmedDate,confirmedbyEmpID,confirmedbyName,approvedYN,approvedDate,approvedbyEmpID,approvedbyEmpName,approvedComment,companyID,companyCode,createdPCID,createdUserGroup,createdUserName,createdUserID,createdDateTime,modifiedPCID,modifiedUserID,modifiedUserName,modifiedDateTime,timestamp) SELECT GLAutoID,systemAccountCode,GLSecondaryCode,GLDescription,masterAccountYN,masterAutoID,masterAccount,masterAccountDescription,masterCategory,levelNo,accountCategoryTypeID,CategoryTypeDescription,subCategory,controllAccountYN,isActive,accountDefaultType,isAuto,isCard,isBank,isCash,replicatedCoaID,bankName,bankBranch,bankShortCode,bankSwiftCode,bankCheckNumber,authourizedSignatureLevel,bankAccountNumber,bankCurrencyID,bankCurrencyCode,bankCurrencyDecimalPlaces,confirmedYN,confirmedDate,confirmedbyEmpID,confirmedbyName,approvedYN,approvedDate,approvedbyEmpID,approvedbyEmpName,approvedComment,companyID,companyCode,createdPCID,createdUserGroup,createdUserName,createdUserID,createdDateTime,modifiedPCID,modifiedUserID,modifiedUserName,modifiedDateTime,timestamp FROM $val1.srp_erp_chartofaccounts");
                    $this->db->query("INSERT INTO srp_erp_generalledger_groupmonitoring(generalLedgerAutoID,wareHouseAutoID,documentCode,documentMasterAutoID,documentDetailAutoID,documentSystemCode,documentType,documentDate,documentYear,documentMonth,projectID,projectExchangeRate,documentNarration,chequeNumber,GLAutoID,systemGLCode,GLCode,GLDescription,GLType,amount_type,isFromItem,transactionCurrencyID,transactionCurrency,transactionExchangeRate,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate,companyLocalAmount,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyID,companyReportingCurrency,companyReportingExchangeRate,companyReportingAmount,companyReportingCurrencyDecimalPlaces,partyContractID,partyType,partyAutoID,partySystemCode,partyName,partyCurrencyID,partyCurrency,partyExchangeRate,partyCurrencyAmount,partyCurrencyDecimalPlaces,subLedgerType,subLedgerDesc,taxMasterAutoID,partyVatIdNo,is_sync,id_store,isAddon,confirmedByEmpID,confirmedByName,confirmedDate,approvedDate,approvedbyEmpID,approvedbyEmpName,segmentID,segmentCode,companyID,companyCode,createdUserGroup,createdPCID,createdUserID,createdDateTime,createdUserName,modifiedPCID,modifiedUserID,modifiedDateTime,modifiedUserName,timestamp,OtherFeesID)SELECT generalLedgerAutoID,wareHouseAutoID,documentCode,documentMasterAutoID,documentDetailAutoID,documentSystemCode,documentType,documentDate,documentYear,documentMonth,projectID,projectExchangeRate,documentNarration,chequeNumber,GLAutoID,systemGLCode,GLCode,GLDescription,GLType,amount_type,isFromItem,transactionCurrencyID,transactionCurrency,transactionExchangeRate,transactionAmount,transactionCurrencyDecimalPlaces,companyLocalCurrencyID,companyLocalCurrency,companyLocalExchangeRate,companyLocalAmount,companyLocalCurrencyDecimalPlaces,companyReportingCurrencyID,companyReportingCurrency,companyReportingExchangeRate,companyReportingAmount,companyReportingCurrencyDecimalPlaces,partyContractID,partyType,partyAutoID,partySystemCode,partyName,partyCurrencyID,partyCurrency,partyExchangeRate,partyCurrencyAmount,partyCurrencyDecimalPlaces,subLedgerType,subLedgerDesc,taxMasterAutoID,partyVatIdNo,is_sync,id_store,isAddon,confirmedByEmpID,confirmedByName,confirmedDate,approvedDate,approvedbyEmpID,approvedbyEmpName,segmentID,segmentCode,companyID,companyCode,createdUserGroup,createdPCID,createdUserID,createdDateTime,createdUserName,modifiedPCID,modifiedUserID,modifiedDateTime,modifiedUserName,timestamp,OtherFeesID FROM $val1.srp_erp_generalledger");

                    $this->db->query("INSERT INTO srp_erp_company_groupmonitoring (`company_id`, `company_link_id`, `branch_link_id`, `productID`, `company_code`, `company_name`, `company_start_date`, `company_url`, `company_logo`, `company_default_currencyID`, `company_default_currency`, `company_default_decimal`, `company_reporting_currencyID`, `company_reporting_currency`, `company_reporting_decimal`, `company_email`, `company_phone`, `companyPrintName`, `companyPrintAddress`, `companyPrintTelephone`, `companyPrintOther`, `companyPrintTagline`, `company_address1`, `company_address2`, `company_city`, `company_province`, `company_postalcode`, `countryID`, `company_country`, `legalName`, `isVatEligible`, `vatIdNo`, `textIdentificationNo`, `textYear`, `industryID`, `industry`, `mfqIndustryID`, `default_segment`, `default_segment_id`, `supportURL`, `noOfUsers`, `companyFinanceYearID`, `companyFinanceYear`, `FYBegin`, `FYEnd`, `companyFinancePeriodID`, `FYPeriodDateFrom`, `FYPeriodDateTo`, `pos_isFinanceEnables`, `isBuyBackEnabled`, `companyType`, `pvtCompanyID`, `defaultTimezoneID`, `confirmedYN`, `localposaccesstoken`, `createdUserGroup`, `createdPCID`, `createdUserID`, `createdDateTime`, `createdUserName`, `modifiedPCID`, `modifiedUserID`, `modifiedDateTime`, `modifiedUserName`, `timestamp`) SELECT `company_id`, `company_link_id`, `branch_link_id`, `productID`, `company_code`, `company_name`, `company_start_date`, `company_url`, `company_logo`, `company_default_currencyID`, `company_default_currency`, `company_default_decimal`, `company_reporting_currencyID`, `company_reporting_currency`, `company_reporting_decimal`, `company_email`, `company_phone`, `companyPrintName`, `companyPrintAddress`, `companyPrintTelephone`, `companyPrintOther`, `companyPrintTagline`, `company_address1`, `company_address2`, `company_city`, `company_province`, `company_postalcode`, `countryID`, `company_country`, `legalName`, `isVatEligible`, `vatIdNo`, `textIdentificationNo`, `textYear`, `industryID`, `industry`, `mfqIndustryID`, `default_segment`, `default_segment_id`, `supportURL`, `noOfUsers`, `companyFinanceYearID`, `companyFinanceYear`, `FYBegin`, `FYEnd`, `companyFinancePeriodID`, `FYPeriodDateFrom`, `FYPeriodDateTo`, `pos_isFinanceEnables`, `isBuyBackEnabled`, `companyType`, `pvtCompanyID`, `defaultTimezoneID`, `confirmedYN`, `localposaccesstoken`, `createdUserGroup`, `createdPCID`, `createdUserID`, `createdDateTime`, `createdUserName`, `modifiedPCID`, `modifiedUserID`, `modifiedDateTime`, `modifiedUserName`, `timestamp` FROM $val1.srp_erp_company");

                }


                /**************Log Update Start**************/
                $IsExistLogDet = $this->db->query("SELECT autoID FROM srp_erp_groupmonitoringlog WHERE companyID = 116 ORDER BY autoID DESC LIMIT 1")->row_array();
                if(!empty($IsExistLogDet))
                {
                    $lastupdate['uploadedEndDate'] = $this->common_data['current_date'];
                    $this->db->where('companyID', current_companyID());
                    $this->db->update('srp_erp_groupmonitoringlog', $lastupdate);

                }else
                {
                    $data_log_insert['uplodedStarDate'] = $this->common_data['current_date'];
                    $data_log_insert['uploadedEndDate'] = $this->common_data['current_date'];
                    $data_log_insert['uploadedByEmpID'] = current_userID();
                    $data_log_insert['companyID'] = current_companyID();
                    $this->db->insert('srp_erp_groupmonitoringlog', $data_log_insert);
                }
                /**************Log Update End**************/
                return array('s', 'Group monitoring report updated successfully.');
            } else
            {
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    return array('e', 'Group monitoring report Update Failed ' . $this->db->_error_message());

            }

        }


    }

    function monthlyincomestatement()
    {
        $date = $this->input->post('Year');
        $startdate = "01-01-$date";
        $enddate = "31-12-$date";
        $dmfrom = date('Y-m', strtotime($startdate));
        $fieldNameChk = "companyLocalAmount";
        $companyid = $this->input->post('companyID');

        $dmto = date('Y-m', strtotime($enddate));
        $months = get_month_list_from_date(format_date($startdate), format_date($enddate), "Y-m", "1 month"); /*calculate months*/
        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $feilds5 = "";
        $having = array();


        if (!empty($months)) {
            foreach ($months as $key => $val2) {

                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger_groupmonitoring.documentDate,'%Y-%m') = '$key',srp_erp_generalledger_groupmonitoring.companyLocalAmount * -1,0) ) as `" . $key . "`,";

                $having[] = "(`" . $key . "` != 0 OR `" . $key . "` != - 0)";
            }
        }
        $this->db->select('primarylanguageemp`.`systemDescription` AS `language');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_lang_languages as primarylanguageemp', 'primarylanguageemp.languageID = srp_employeesdetails.languageID', 'INNER');
        $this->db->where('EIdNo', current_userID());
        $result_employee = $this->db->get()->row_array();
        if (!empty($result_employee['language'])||($result_employee['language']!='')) {
            if($result_employee['language']=='english')
            {
                $feilds5 .= 'CategoryTypeDescription as CategoryTypeDescriptionlanguage';
            }else if($result_employee['language']=='arabic')
            {
                $feilds5 .='categoryTypeDescriptionOthers CategoryTypeDescriptionlanguage';
            }else
            {
                $feilds5 .= 'CategoryTypeDescriptions as CategoryTypeDescriptionlanguage';
            }
        }
        else {
            $this->db->select('primary.systemDescription as language');
            $this->db->from('srp_erp_lang_companylanguages');
            $this->db->join('srp_erp_lang_languages as primary', 'primary.languageID = srp_erp_lang_companylanguages.primaryLanguageID', 'INNER');
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                if($result['language']=='english')
                {
                    $feilds5 .= 'CategoryTypeDescription as CategoryTypeDescriptionlanguage';
                }else if ($result['language']=='arabic')
                {
                    $feilds5 .='categoryTypeDescriptionOthers CategoryTypeDescriptionlanguage';
                }
                else
                {
                    $feilds5 .= 'CategoryTypeDescription as CategoryTypeDescriptionlanguage';
                }
            } else {
                $feilds5 .= 'CategoryTypeDescription as CategoryTypeDescriptionEnglish';
            }
        }

        //$feilds .= "CL.DecimalPlaces as companyLocalAmountDecimalPlaces,";


        $result = $this->db->query("select *,$feilds5,CASE
	
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Income\" THEN
	1 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Cost of Goods Sold\" THEN
	2 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Expense\" THEN
	3 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Other Income\" THEN
	4 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Other Expense\" THEN
	5 ELSE 7 
	END catergorytypenew,
	srp_erp_accountcategorytypes.CategoryTypeDescription AS subCategorynew 
	
	 from srp_erp_accountcategorytypes left join(SELECT * FROM (SELECT $feilds
	CASE
	
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Income\" THEN
	1 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Cost of Goods Sold\" THEN
	2 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Expense\" THEN
	3 
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Other Income\" THEN
	4
	WHEN srp_erp_accountcategorytypes.CategoryTypeDescription = \"Other Expense\" THEN
	5 ELSE 7 
	END catergorytype,
	gcoa.accountCategoryTypeID,
	srp_erp_accountcategorytypes.CategoryTypeDescription as subCategory
FROM
	srp_erp_generalledger_groupmonitoring
INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) gcoa ON srp_erp_generalledger_groupmonitoring.GLAutoID = gcoa.GLAutoID 
	AND srp_erp_generalledger_groupmonitoring.companyID = gcoa.companyID
INNER JOIN srp_erp_accountcategorytypes ON srp_erp_accountcategorytypes.accountCategoryTypeID = gcoa.accountCategoryTypeID 
WHERE
	srp_erp_generalledger_groupmonitoring.documentDate BETWEEN '" . format_date($startdate) . "' AND '" . format_date($enddate) . "' AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
	AND srp_erp_accountcategorytypes.Type = \"PL\"
GROUP BY
	 gcoa.accountCategoryTypeID )as tbl1 GROUP BY tbl1.accountCategoryTypeID
	  ORDER BY
tbl1.catergorytype ASC
	 )t1  on t1.accountCategoryTypeID=srp_erp_accountcategorytypes.accountCategoryTypeID  where srp_erp_accountcategorytypes.Type = \"PL\" ")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    function balancesheet_rpt()
    {
        $date = $this->input->post('Year');
        $date = range(($date-2),$date);
        $companyid = $this->input->post('companyID');

        $this->db->select('primarylanguageemp`.`systemDescription` AS `language');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_lang_languages as primarylanguageemp', 'primarylanguageemp.languageID = srp_employeesdetails.languageID', 'INNER');
        $this->db->where('EIdNo', current_userID());
        $result_employee = $this->db->get()->row_array();
        $feilds5 = '';
        if (!empty($result_employee['language'])||($result_employee['language']!='')) {
            if($result_employee['language']=='english')
            {
                $feilds5 .= 'CategoryTypeDescription as subCategorynew';
            }else if($result_employee['language']=='arabic')
            {
                $feilds5 .='categoryTypeDescriptionOthers subCategorynew';
            }else
            {
                $feilds5 .= 'CategoryTypeDescriptions as subCategorynew';
            }
        }
        else {
            $this->db->select('primary.systemDescription as language');
            $this->db->from('srp_erp_lang_companylanguages');
            $this->db->join('srp_erp_lang_languages as primary', 'primary.languageID = srp_erp_lang_companylanguages.primaryLanguageID', 'INNER');
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                if($result['language']=='english')
                {
                    $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                }else if ($result['language']=='arabic')
                {
                    $feilds5 .='categoryTypeDescriptionOthers subCategorynew';
                }
                else
                {
                    $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                }
            } else {
                $feilds5 .= 'CategoryTypeDescription as subCategorynew';
            }
        }

        $sql = "SELECT
                *,
                CASE
                
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Fixed Asset\" THEN
                1 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Account Receivable\" THEN
                3 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Current Asset\" THEN
                4 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Bank\" THEN
                6 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Equity\" THEN
                7 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Long Term Liability\" THEN
                8 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Account Payable\" THEN
                9 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Current Liability\" THEN
                10 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Current Asset\" THEN
                5 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Asset\" THEN
                2 ELSE 11 
                END catergorytypenew,
                
                $feilds5
                
            FROM
                srp_erp_accountCategoryTypes LEFT JOIN (SELECT
            SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[0]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[0]. "',
            
            SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[1]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[1]. "',
            
            SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[2]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[2]. "',
            
                srp_erp_accountCategoryTypes.sortOrder,
            IF
                ( srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA', 'ASSETS', IF ( srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSL', 'LIABILITIES', 'ND' ) ) AS mainCategory,
            
                srp_erp_generalledger_groupmonitoring.GLAutoID,
                srp_erp_accountCategoryTypes.CategoryTypeDescription AS subCategory,
            CASE
                
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Fixed Asset\" THEN
                1 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Account Receivable\" THEN
                3 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Current Asset\" THEN
                4
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Bank\" THEN
                6
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Equity\" THEN
                7 
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Long Term Liability\" THEN
                8
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Account Payable\" THEN
                9
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Current Liability\" THEN
                10
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Current Asset\" THEN
                5
                WHEN srp_erp_accountCategoryTypes.CategoryTypeDescription = \"Other Asset\" THEN
                2
            ELSE 11
                END catergorytype,
                srp_erp_chartofaccounts_groupmonitoring.accountCategoryTypeID 
            FROM
                srp_erp_generalledger_groupmonitoring
                INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID,subCategory FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) srp_erp_chartofaccounts_groupmonitoring ON srp_erp_generalledger_groupmonitoring.GLAutoID = srp_erp_chartofaccounts_groupmonitoring.GLAutoID 
                AND srp_erp_generalledger_groupmonitoring.companyID = srp_erp_chartofaccounts_groupmonitoring.companyID
                INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts_groupmonitoring.accountCategoryTypeID
                
                
            WHERE
                srp_erp_generalledger_groupmonitoring.documentDate <= '" .$date[2]. "-12-31' 
                AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
                AND srp_erp_accountCategoryTypes.Type = \"BS\"
            GROUP BY
                srp_erp_chartofaccounts_groupmonitoring.accountCategoryTypeID
            ORDER BY
                catergorytype ASC)t1 ON t1.accountCategoryTypeID = srp_erp_accountCategoryTypes.accountCategoryTypeID WHERE srp_erp_accountCategoryTypes.Type = \"BS\" ORDER BY catergorytypenew ASC";


        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function emp_localization()
    {
        $companyid = $this->input->post('companyID');

        $feilds5 = '';

        $this->db->select('primarylanguageemp`.`systemDescription` AS `language');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_lang_languages as primarylanguageemp', 'primarylanguageemp.languageID = srp_employeesdetails.languageID', 'INNER');
        $this->db->where('EIdNo', current_userID());
        $result_employee = $this->db->get()->row_array();
        if (!empty($result_employee['language'])||($result_employee['language']!='')) {
            if($result_employee['language']=='english')
            {
                $feilds5 .= 'TRIM(IFNULL(srp_erp_countrymaster.Nationality,\'-\')) as national';
            }else if($result_employee['language']=='arabic')
            {
                $feilds5 .='	TRIM(IFNULL(srp_erp_countrymaster.Nationality_O,\'-\')) as national';
            }else
            {
                $feilds5 .= '	TRIM(IFNULL(srp_erp_countrymaster.Nationality,\'-\')) as national';
            }
        }
        else {
            $this->db->select('primary.systemDescription as language');
            $this->db->from('srp_erp_lang_companylanguages');
            $this->db->join('srp_erp_lang_languages as primary', 'primary.languageID = srp_erp_lang_companylanguages.primaryLanguageID', 'INNER');
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                if($result['language']=='english')
                {
                    $feilds5 .= 'TRIM(IFNULL(srp_erp_countrymaster.Nationality,\'-\')) as national';
                }else if ($result['language']=='arabic')
                {
                    $feilds5 .='	TRIM(IFNULL(srp_erp_countrymaster.Nationality_O,\'-\')) as national';
                }
                else
                {
                    $feilds5 .= 'TRIM(IFNULL(srp_erp_countrymaster.Nationality,\'-\')) as national';
                }
            } else {
                $feilds5 .= 'TRIM(IFNULL(srp_erp_countrymaster.Nationality,\'-\')) as national';
            }
        }


        $result['empcount'] = $this->db->query("SELECT
    	IFNULL(SUM( CASE WHEN srp_erp_countrymaster.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END ),0) AS localEmployee,
	IFNULL( SUM( CASE WHEN srp_erp_countrymaster.countryID != " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END ),0)  AS expatriateEmployee,
	$feilds5
FROM
    srp_employeesdetails_groupmonitoring
    LEFT JOIN srp_erp_company ON srp_employeesdetails_groupmonitoring.Erp_companyID = srp_erp_company.company_id
    LEFT JOIN srp_nationality ON srp_nationality.NId = srp_employeesdetails_groupmonitoring.NId 
    LEFT JOIN srp_erp_countrymaster on srp_erp_countrymaster.countryID = srp_employeesdetails_groupmonitoring.Nid
WHERE
    srp_employeesdetails_groupmonitoring.Erp_companyID IN (" . join(',', $companyid) . ") 
		AND isDischarged = 0 
    AND empConfirmedYN = 1 
    AND isSystemAdmin = 0 
")->result_array();
        return $result;

    }

    function load_last_update()
    {
        $companyID = current_companyID();
        $data = $this->db->query("SELECT DATE_FORMAT( uploadedEndDate, '%d-%m-%Y' ) as lastupdate FROM `srp_erp_groupmonitoringlog` WHERE 
	companyID = {$companyID}  ORDER BY autoID  DESC LIMIT 1;")->row_array();
        return $data;
    }
    function monthlyincomestatement_chart()
    {
        $date = $this->input->post('Year');
        $startdate = "01-01-$date";
        $enddate = "31-12-$date";
        $dmfrom = date('Y-m', strtotime($startdate));
        $fieldNameChk = "companyLocalAmount";
        $companyid = $this->input->post('companyID');

        $dmto = date('Y-m', strtotime($enddate));
        $months = get_month_list_from_date(format_date($startdate), format_date($enddate), "Y-m", "1 month"); /*calculate months*/
        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $having = array();

        $feilds5 = '';
        $feilds6 = '';
        $this->db->select('primarylanguageemp`.`systemDescription` AS `language');
        $this->db->from('srp_employeesdetails');
        $this->db->join('srp_erp_lang_languages as primarylanguageemp', 'primarylanguageemp.languageID = srp_employeesdetails.languageID', 'INNER');
        $this->db->where('EIdNo', current_userID());
        $result_employee = $this->db->get()->row_array();
        if (!empty($result_employee['language'])||($result_employee['language']!='')) {
            if($result_employee['language']=='english')
            {
                $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                $feilds6.='"Net Profit" as subCategorynew';
            }else if($result_employee['language']=='arabic')
            {
                $feilds5 .='categoryTypeDescriptionOthers as subCategorynew';
                $feilds6.='"صافي الربح"
as subCategorynew';
            }else
            {
                $feilds5 .= 'CategoryTypeDescriptions as subCategorynew';
                $feilds6.='"Net Profit" as subCategorynew';
            }
        }
        else {
            $this->db->select('primary.systemDescription as language');
            $this->db->from('srp_erp_lang_companylanguages');
            $this->db->join('srp_erp_lang_languages as primary', 'primary.languageID = srp_erp_lang_companylanguages.primaryLanguageID', 'INNER');
            $this->db->where('companyID', current_companyID());
            $result = $this->db->get()->row_array();
            if (!empty($result)) {
                if($result['language']=='english')
                {
                    $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                    $feilds6.='"Net Profit" as subCategorynew';
                }else if ($result['language']=='arabic')
                {
                    $feilds5 .='categoryTypeDescriptionOthers subCategorynew';
                    $feilds6.='"صافي الربح"
as subCategorynew';
                }
                else
                {
                    $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                    $feilds6.='"Net Profit" as subCategorynew';
                }
            } else {
                $feilds5 .= 'CategoryTypeDescription as subCategorynew';
                $feilds6.='"Net Profit" as subCategorynew';
            }
        }



        if (!empty($months)) {
            foreach ($months as $key => $val2) {

                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger_groupmonitoring.documentDate,'%Y-%m') = '$key',srp_erp_generalledger_groupmonitoring.companyLocalAmount * -1,0) ) as `" . $key . "`,";

                $having[] = "(`" . $key . "` != 0 OR `" . $key . "` != - 0)";
            }
        }
        //$feilds .= "CL.DecimalPlaces as companyLocalAmountDecimalPlaces,";


        $result = $this->db->query("SELECT * FROM (SELECT $feilds
	CASE
	 WHEN srp_erp_accountCategoryTypes.accountCategoryTypeID = \"11\" THEN
    \"1\" 
    WHEN srp_erp_accountCategoryTypes.accountCategoryTypeID = \"12\" THEN
    \"2\" 
    WHEN srp_erp_accountCategoryTypes.accountCategoryTypeID = \"13\" THEN
    \"3\" 
    WHEN srp_erp_accountCategoryTypes.accountCategoryTypeID = \"14\" THEN
    \"1\" 
    WHEN srp_erp_accountCategoryTypes.accountCategoryTypeID = \"15\" THEN
    \"5\" ELSE \"7\" 
	END catergorytype,
    gcoa.accountCategoryTypeID,
	CategoryTypeDescription as subCategory,
	$feilds5
FROM
	srp_erp_generalledger_groupmonitoring
INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) gcoa ON srp_erp_generalledger_groupmonitoring.GLAutoID = gcoa.GLAutoID 
	AND srp_erp_generalledger_groupmonitoring.companyID = gcoa.companyID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = gcoa.accountCategoryTypeID 
WHERE
	srp_erp_generalledger_groupmonitoring.documentDate BETWEEN '" . format_date($startdate) . "' AND '" . format_date($enddate) . "' AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
	AND srp_erp_accountCategoryTypes.Type = \"PL\"
	AND srp_erp_accountCategoryTypes.accountCategoryTypeID IN (11,12,13)
GROUP BY
	
	 catergorytype )as tbl1 GROUP BY catergorytype 
	 UNION 
	 SELECT * FROM (SELECT $feilds
	\"NP\" as  catergorytype,
	gcoa.accountCategoryTypeID,
	\"Net Profit\" as subCategory,
	$feilds6
FROM
	srp_erp_generalledger_groupmonitoring
INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) gcoa ON srp_erp_generalledger_groupmonitoring.GLAutoID = gcoa.GLAutoID 
	AND srp_erp_generalledger_groupmonitoring.companyID = gcoa.companyID
INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = gcoa.accountCategoryTypeID 
WHERE
	srp_erp_generalledger_groupmonitoring.documentDate BETWEEN '" . format_date($startdate) . "' AND '" . format_date($enddate) . "' AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
	AND srp_erp_accountCategoryTypes.Type = \"PL\"
 )as tbl1 GROUP BY tbl1.accountCategoryTypeID ")->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    function localaization_country()
    {
        $result['country'] =  $this->db->query("SELECT
	Nationality 
FROM
	`srp_erp_countrymaster`
	where 
	countryID = " . $this->common_data['company_data']['countryID'] ." ")->row_array();
        return $result;
    }
    function groupmonitoringdashboard()
    {
        $date = $this->input->post('year');
        $startdate = "01-01-$date";
        $enddate = "31-12-$date";
        $dmfrom = date('Y-m', strtotime($startdate));
        $fieldNameChk = "companyLocalAmount";
        $companyid = $this->input->post('companyID');
        $accountCategoryTypeID = $this->input->post('accountCategoryTypeID');

        $dmto = date('Y-m', strtotime($enddate));
        $months = get_month_list_from_date(format_date($startdate), format_date($enddate), "Y-m", "1 month"); /*calculate months*/
        $feilds = "";
        $feilds2 = "";
        $feilds3 = "";
        $having = array();


        if (!empty($months)) {
            foreach ($months as $key => $val2) {

                $feilds .= "SUM(if(DATE_FORMAT(srp_erp_generalledger_groupmonitoring.documentDate,'%Y-%m') = '$key',srp_erp_generalledger_groupmonitoring.companyLocalAmount * -1,0) ) as `" . $key . "`,";

                $having[] = "(`" . $key . "` != 0 OR `" . $key . "` != - 0)";
            }
        }
        //$feilds .= "CL.DecimalPlaces as companyLocalAmountDecimalPlaces,";

        $result = $this->db->query("SELECT $feilds
			srp_erp_generalledger_groupmonitoring.companyID,
	gcoa.accountCategoryTypeID,
	srp_erp_accountcategorytypes.CategoryTypeDescription AS subCategory,
	srp_erp_company_groupmonitoring.company_name as companyname
FROM
	srp_erp_generalledger_groupmonitoring
	INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) gcoa ON srp_erp_generalledger_groupmonitoring.GLAutoID = gcoa.GLAutoID 
	AND srp_erp_generalledger_groupmonitoring.companyID = gcoa.companyID
	INNER JOIN srp_erp_accountcategorytypes ON srp_erp_accountcategorytypes.accountCategoryTypeID = gcoa.accountCategoryTypeID 
	left join srp_erp_company_groupmonitoring on srp_erp_company_groupmonitoring.company_id = srp_erp_generalledger_groupmonitoring.companyID
WHERE
	srp_erp_generalledger_groupmonitoring.documentDate BETWEEN '" . format_date($startdate) . "' AND '" . format_date($enddate) . "'
	AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
	AND srp_erp_accountcategorytypes.Type = \"PL\" 
	AND srp_erp_accountcategorytypes.accountCategoryTypeID = '{$accountCategoryTypeID}'
	GROUP BY
	srp_erp_generalledger_groupmonitoring.companyID
")->result_array();


        return $result;
    }
    function groupmonitoringdashboardblancesheet()
    {
        $accountCategoryTypeID = $this->input->post('accountCategoryTypeID');
        $date = $this->input->post('year');
        $date = range(($date-2),$date);
        $companyid = $this->input->post('companyID');

        $result = $this->db->query("SELECT
	*,
	srp_erp_accountCategoryTypes.CategoryTypeDescription AS subCategorynew
	
FROM
	srp_erp_accountCategoryTypes
	LEFT JOIN (
	SELECT
		srp_erp_company_groupmonitoring.company_name as companyname,
		    SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[0]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[0]. "',
            SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[1]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[1]. "',
            SUM(IF(srp_erp_generalledger_groupmonitoring.documentYear <= '" .$date[2]. "',IF(srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA',( srp_erp_generalledger_groupmonitoring.companyLocalAmount ),( srp_erp_generalledger_groupmonitoring.companyLocalAmount ) *- 1),0)) as '" .$date[2]. "',
	
	IF
		( srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSA', 'ASSETS', IF ( srp_erp_chartofaccounts_groupmonitoring.subCategory = 'BSL', 'LIABILITIES', 'ND' ) ) AS mainCategory,
		srp_erp_generalledger_groupmonitoring.GLAutoID,
		srp_erp_accountCategoryTypes.CategoryTypeDescription AS subCategory,

	srp_erp_chartofaccounts_groupmonitoring.accountCategoryTypeID 
FROM
	srp_erp_generalledger_groupmonitoring
	INNER JOIN ( SELECT glautoID, companyID, accountCategoryTypeID, subCategory FROM srp_erp_chartofaccounts_groupmonitoring GROUP BY GLAutoID, companyID ) srp_erp_chartofaccounts_groupmonitoring ON srp_erp_generalledger_groupmonitoring.GLAutoID = srp_erp_chartofaccounts_groupmonitoring.GLAutoID 
	AND srp_erp_generalledger_groupmonitoring.companyID = srp_erp_chartofaccounts_groupmonitoring.companyID
	INNER JOIN srp_erp_accountCategoryTypes ON srp_erp_accountCategoryTypes.accountCategoryTypeID = srp_erp_chartofaccounts_groupmonitoring.accountCategoryTypeID 
	left join srp_erp_company_groupmonitoring on srp_erp_company_groupmonitoring.company_id = srp_erp_generalledger_groupmonitoring.companyID
WHERE
	srp_erp_generalledger_groupmonitoring.documentDate <= '" .$date[2]. "-12-31' 
	AND srp_erp_generalledger_groupmonitoring.companyID IN (" . join(',', $companyid) . ")
	AND srp_erp_accountCategoryTypes.Type = \"BS\" 
	AND srp_erp_accountcategorytypes.accountCategoryTypeID = '{$accountCategoryTypeID}'
	GROUP BY
	srp_erp_generalledger_groupmonitoring.companyID

	) t1 ON t1.accountCategoryTypeID = srp_erp_accountCategoryTypes.accountCategoryTypeID 
WHERE
	srp_erp_accountCategoryTypes.Type = \"BS\" 
		AND srp_erp_accountcategorytypes.accountCategoryTypeID = '{$accountCategoryTypeID}'
	

")->result_array();
        return $result;
    }
    function group_monitoring_emplocal()
    {
        $companyid = $this->input->post('companyID');
        $type = $this->input->post('type');




            $result['empcount'] = $this->db->query("SELECT
    	IFNULL(SUM( CASE WHEN srp_erp_countrymaster.countryID = " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END ),0) AS localemp,
    	  IFNULL( SUM( CASE WHEN srp_erp_countrymaster.countryID != " . $this->common_data['company_data']['countryID'] . " THEN 1 ELSE 0 END ),0)  AS expemp,
    	companygroup.company_name as compname,
    	 ROUND(((SUM(CASE WHEN srp_erp_countrymaster.countryID = ".$this->common_data['company_data']['countryID']." THEN 1 ELSE 0 END)/(SUM(CASE WHEN srp_erp_countrymaster.countryID = ".$this->common_data['company_data']['countryID']." THEN 1 ELSE 0 END) + SUM(CASE WHEN srp_erp_countrymaster.countryID != ".$this->common_data['company_data']['countryID']." THEN 1 ELSE 0 END)))*100),0) as localization
FROM
    srp_employeesdetails_groupmonitoring
    LEFT JOIN srp_erp_company ON srp_employeesdetails_groupmonitoring.Erp_companyID = srp_erp_company.company_id
    LEFT JOIN srp_nationality ON srp_nationality.NId = srp_employeesdetails_groupmonitoring.NId 
    LEFT JOIN srp_erp_countrymaster on srp_erp_countrymaster.countryID = srp_employeesdetails_groupmonitoring.Nid
    LEFT JOIN srp_erp_company_groupmonitoring companygroup on companygroup.company_id = srp_employeesdetails_groupmonitoring.Erp_companyID
WHERE
    srp_employeesdetails_groupmonitoring.Erp_companyID IN (" . join(',', $companyid) . ") 
		AND isDischarged = 0 
    AND empConfirmedYN = 1 
    AND isSystemAdmin = 0 
    GROUP BY
	srp_employeesdetails_groupmonitoring.Erp_companyID
")->result_array();
            return $result;
    }
    function save_action_tracker()
    {
        $mprID = $this->input->post('periods_mpr');
        $companyID = $this->input->post('selectedcomapnyID');
        $SegmentID = $this->input->post('segmentID');
        $adddescription = $this->input->post('adddescription');
        $targetdate = $this->input->post('targetdate');
        $EmployeeID = $this->input->post('employeeID');
        $companyType = $this->input->post('companyType');
        $date_format_policy = date_format_policy();
        $companyIDCreated = current_companyID();
        $format_startdate = null;
        if (isset($targetdate)) {
            $format_startdate = input_format_date($targetdate, $date_format_policy);
        }
        $segment = explode('|', $SegmentID);

        $data['mprID'] = $mprID;
        $data['assignedCompanyID'] = $companyID;
        $data['assignedSegmentID'] = $segment[0];
        $data['description'] = $adddescription;
        $data['targetDate'] = $format_startdate;
        $data['responsibleEmpID'] = $EmployeeID;
        $data['status'] = 0;
        $data['createdByEmpID'] =  $this->common_data['current_userID'];
        $data['createdDatetime'] = $this->common_data['current_date'];
        $data['companyType'] = $companyType;
        $data['createdCompanyID'] = $companyIDCreated;
        $this->db->insert('srp_erp_actionitems', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Action Tracker insertion Faild ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Action Tracker Added Successfully.');
        }
    }
    function get_company_action_tracker()
    {
        $convertFormat = convert_date_format_sql();

        $companyID = current_companyID();
    $Output = $this->db->query('SELECT actonitems.actionID,actonitems.description, empreportedby.Ename2 as Reportedby, assignedemp.Ename2 as AssignedEmp,IFNULL(DATE_FORMAT(targetDate,\'' . $convertFormat . '\'),\'-\') AS targetDate,IFNULL(DATE_FORMAT(completedDate,\'' . $convertFormat . '\'),\'-\') AS completedDate , company.company_code as companycode, segment.segmentCode as segment FROM srp_erp_actionitems actonitems LEFT JOIN srp_employeesdetails empreportedby on empreportedby.EIdNo = actonitems.createdByEmpID LEFT JOIN srp_employeesdetails assignedemp on assignedemp.EIdNo = actonitems.responsibleEmpID LEFT JOIN srp_erp_company company on company.company_id = actonitems.assignedCompanyID left join srp_erp_segment segment on segment.segmentID = actonitems.assignedSegmentID where actonitems.createdCompanyID = '.$companyID.' ')->result_array();
        return $Output;
    }

 function get_company_action_tracker_view()
 {
     $convertFormat = convert_date_format_sql();
     $detailID = $this->input->post('actionID');
     $companyType = $this->session->userdata("companyType");
     $mprID = $this->input->post('mprID');
     $companyID = current_companyID();

        $finance_period_join = '';
        $colname = '';
     if ($companyType == 1) {
         $finance_period_join = ' LEFT JOIN srp_erp_companyfinanceperiod on srp_erp_companyfinanceperiod.companyFinancePeriodID = actonitems.mprID';
         $colname = 'srp_erp_companyfinanceperiod.dateFrom';
         $actionItemsCompanyType = ' AND actonitems.companyType = 1';
         $finance_period_colname = 'companyFinancePeriodID';
         $finance_period_tblname = 'srp_erp_companyfinanceperiod' ;

     } else
     {
         $finance_period_join = 'LEFT JOIN srp_erp_groupfinanceperiod on srp_erp_groupfinanceperiod.groupFinancePeriodID = actonitems.mprID';
         $colname = 'srp_erp_groupfinanceperiod.dateFrom';
         $actionItemsCompanyType = ' AND actonitems.companyType = 2';
         $finance_period_colname ='groupFinancePeriodID';
         $finance_period_tblname ='srp_erp_groupfinanceperiod';

     }




     $companyID = current_companyID();
     $Output = $this->db->query('SELECT '.$colname.',actonitems.approvedYN,actonitems.status,actonitems.actionID,actonitems.description, empreportedby.Ename2 as Reportedby, assignedemp.Ename2 as AssignedEmp,IFNULL(DATE_FORMAT(targetDate,\'' . $convertFormat . '\'),\'-\') AS targetDate,IFNULL(DATE_FORMAT(completedDate,\'' . $convertFormat . '\'),\'-\') AS completedDate , company.company_code as companycode, segment.segmentCode as segment FROM srp_erp_actionitems actonitems LEFT JOIN srp_employeesdetails empreportedby on empreportedby.EIdNo = actonitems.createdByEmpID LEFT JOIN srp_employeesdetails assignedemp on assignedemp.EIdNo = actonitems.responsibleEmpID LEFT JOIN srp_erp_company company on company.company_id = actonitems.assignedCompanyID 
     '.$finance_period_join.'
    left join srp_erp_segment segment on segment.segmentID = actonitems.assignedSegmentID where actonitems.createdCompanyID = ' . $companyID . ' '.$actionItemsCompanyType.' AND mprID = '.$mprID.' UNION ALL 	SELECT
	srp_erp_companyfinanceperiod.dateFrom,
	actonitems.approvedYN,
	actonitems.STATUS,
	actonitems.actionID,
	actonitems.description,
	empreportedby.Ename2 AS Reportedby,
	assignedemp.Ename2 AS AssignedEmp,
	IFNULL( DATE_FORMAT( targetDate, \'%d-%m-%Y\' ), \'-\' ) AS targetDate,
	IFNULL( DATE_FORMAT( completedDate, \'%d-%m-%Y\' ), \'-\' ) AS completedDate,
	company.company_code AS companycode,
	segment.segmentCode AS segment 
FROM
	srp_erp_actionitems actonitems
	LEFT JOIN srp_employeesdetails empreportedby ON empreportedby.EIdNo = actonitems.createdByEmpID
	LEFT JOIN srp_employeesdetails assignedemp ON assignedemp.EIdNo = actonitems.responsibleEmpID
	LEFT JOIN srp_erp_company company ON company.company_id = actonitems.assignedCompanyID
	LEFT JOIN srp_erp_companyfinanceperiod ON srp_erp_companyfinanceperiod.companyFinancePeriodID = actonitems.mprID
	LEFT JOIN srp_erp_segment segment ON segment.segmentID = actonitems.assignedSegmentID 
WHERE
	actonitems.createdCompanyID = '.$companyID.' AND actonitems.status !=3 AND mprID IN (
	SELECT
	'.$finance_period_colname.'
FROM 
		'.$finance_period_tblname.'
WHERE
	dateFrom < ( SELECT dateFrom FROM '.$finance_period_tblname.' WHERE '.$finance_period_colname.' = '.$mprID.' )) AND actonitems.companyType = '.$companyType.'  ')->result_array();
     return $Output;

 }
    function get_company_action_tracker_view_master()
    {
        $convertFormat = convert_date_format_sql();
        $detailID = $this->input->post('actionID');



        $companyID = current_companyID();
        $Output = $this->db->query('SELECT IFNULL(actonitems.completionComment,\'-\') as completoncomment,actonitems.status,actonitems.companyType, actonitems.responsibleEmpID,actonitems.assignedSegmentID,actonitems.assignedCompanyID,actonitems.actionID,actonitems.description, empreportedby.Ename2 as Reportedby, assignedemp.Ename2 as AssignedEmp,IFNULL(DATE_FORMAT(targetDate,\'' . $convertFormat . '\'),\'-\') AS targetDate,IFNULL(DATE_FORMAT(completedDate,\'' . $convertFormat . '\'),\'-\') AS completedDate,IFNULL(DATE_FORMAT(actonitems.createdDatetime,\'' . $convertFormat . '\'),\'-\') AS createddate,IFNULL(DATE_FORMAT(actonitems.approvedDate,\'' . $convertFormat . '\'),\'-\') AS approvedDate , company.company_name as companycode, segment.segmentCode as segment,CONCAT(\'MPR For \',DATE_FORMAT(financeperiod.dateFrom,\'%M %Y\')) as monthname,IFNULL(actonitems.approvalComment,\'-\') as approvecom FROM srp_erp_actionitems actonitems LEFT JOIN srp_employeesdetails empreportedby on empreportedby.EIdNo = actonitems.createdByEmpID LEFT JOIN srp_employeesdetails assignedemp on assignedemp.EIdNo = actonitems.responsibleEmpID LEFT JOIN srp_erp_company company on company.company_id = actonitems.assignedCompanyID left join srp_erp_segment segment on segment.segmentID = actonitems.assignedSegmentID 	LEFT JOIN srp_erp_companyfinanceperiod financeperiod on financeperiod.companyFinancePeriodID = actonitems.mprID where actionID = ' . $detailID . '  ')->row_array();
        return $Output;

    }
    function update_action_tracker_detial()
    {
            $actiontrackerdeitlID = $this->input->post('actiontrackerdetailID');

            $mprID = $this->input->post('periods_mpr');
            $companyID = $this->input->post('selectedcomapnyID_edit');
            $SegmentID = $this->input->post('segmentID_edit');
            $adddescription = $this->input->post('actiondescriptionedit');
            $EmployeeID = $this->input->post('employeeID_edit');
            $companyType = $this->input->post('companyType');
            $companyIDCreated = current_companyID();
            $segment = explode('|', $SegmentID);
            $data['mprID'] = $mprID;
            $data['assignedCompanyID'] = $companyID;
            $data['assignedSegmentID'] = $segment[0];
            $data['description'] = $adddescription;
            $data['responsibleEmpID'] = $EmployeeID;
            $data['status'] = 0;
            $data['createdByEmpID'] =  $this->common_data['current_userID'];
            $data['createdDatetime'] = $this->common_data['current_date'];
            $data['companyType'] = $companyType;
            $data['createdCompanyID'] = $companyIDCreated;
            $this->db->where('actionID', $actiontrackerdeitlID);
            $this->db->update('srp_erp_actionitems', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e', 'Action Tracker update Faild ' . $this->db->_error_message());
            } else {
                $this->db->trans_commit();
                return array('s', 'Action Tracker Updated Successfully.');
            }

    }
    function update_close_status()
    {
        $actiontrackerdeitlID = $this->input->post('actionID');
        $data['status'] = 3;
        $data['completedDate'] = $this->common_data['current_date'];
        $data['approvedYN'] = 1;
        $data['approvedDate'] =$this->common_data['current_date'];
        $this->db->where('actionID', $actiontrackerdeitlID);
        $this->db->update('srp_erp_actionitems', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Action Tracker update Faild ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Action Tracker Status Updated Successfully.');
        }
    }
    function fetch_assigned_taskmyprofile()
    {
        $companyID = current_companyID();
        $responsibleID = current_userID();
        $convertFormat = convert_date_format_sql();
        $output = $this->db->query('SELECT actionitems.actionID,actionitems.approvedYN,IFNULL(DATE_FORMAT(targetDate,\'' . $convertFormat . '\'),\'-\') AS targetDate,IFNULL(DATE_FORMAT(completedDate,\'' . $convertFormat . '\'),\'-\') AS completedDate,IFNULL(DATE_FORMAT(actionitems.createdDatetime,\'' . $convertFormat . '\'),\'-\') AS createddate, actionitems.description AS Task, empdet.Ename2 AS composedby, financeperiod.dateFrom AS MONTH, targetDate, segment.segmentCode AS segment, company.company_code AS company, actionitems.`status` FROM srp_erp_actionitems actionitems LEFT JOIN srp_employeesdetails empdet ON actionitems.createdByEmpID = empdet.EIdNo LEFT JOIN srp_erp_companyfinanceperiod financeperiod ON financeperiod.companyFinancePeriodID = actionitems.mprID LEFT JOIN srp_erp_segment segment ON segment.segmentID = actionitems.assignedSegmentID LEFT JOIN srp_erp_company company ON company.company_id = actionitems.assignedCompanyID WHERE assignedCompanyID = '.$companyID.' AND responsibleEmpID = '.$responsibleID.' ')->result_array();

        return $output;
    }
    function update_mpr_task_status()
    {


        $actiontrackerdeitlID = $this->input->post('assignedID');
        $status = $this->input->post('status');
        $completiondate = $this->input->post('completiondate');
        $comment = $this->input->post('comment');
        $date_format_policy = date_format_policy();
        $format_startdate = null;
        if (isset($completiondate)) {
            $format_startdate = input_format_date($completiondate, $date_format_policy);
        }
        if($status == 2)
        {
            $data['status'] = $status;
            $data['completedDate'] = $format_startdate;
            $data['completionComment'] = $comment;
        }else
        {
            $data['status'] = $status;
        }
        $this->db->where('actionID', $actiontrackerdeitlID);
        $this->db->update('srp_erp_actionitems', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Action Tracker update Faild ' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Action Tracker Status Updated Successfully.');
        }
    }
    function fetch_created_taskmyprofile()
    {
        $companyID = current_companyID();
        $responsibleID = current_userID();
        $convertFormat = convert_date_format_sql();
        $text = $this->input->post('q');
        $search_string = '';
        if (isset($text) && !empty($text)) {
            $search_string = " AND ((actionitems.description Like '%" . $text . "%') OR(empdet.Ename2 Like '%" . $text . "%')OR(employeeresponsible.Ename2 Like '%" . $text . "%')OR(company.company_code Like '%" . $text . "%')OR(segment.segmentCode Like '%" . $text . "%'))";
        }

        $output = $this->db->query('SELECT actionitems.actionID,actionitems.approvedYN,IFNULL(DATE_FORMAT(targetDate,\'' . $convertFormat . '\'),\'-\') AS targetDate,IFNULL(DATE_FORMAT(completedDate,\'' . $convertFormat . '\'),\'-\') AS completedDate,IFNULL(DATE_FORMAT(actionitems.createdDatetime,\'' . $convertFormat . '\'),\'-\') AS createddate, actionitems.description AS Task, empdet.Ename2 AS composedby, financeperiod.dateFrom AS MONTH, targetDate, segment.segmentCode AS segment, company.company_code AS company, actionitems.`status`,employeeresponsible.Ename2 AS responsibleEmpname FROM srp_erp_actionitems actionitems LEFT JOIN srp_employeesdetails empdet ON actionitems.createdByEmpID = empdet.EIdNo LEFT JOIN srp_erp_companyfinanceperiod financeperiod ON financeperiod.companyFinancePeriodID = actionitems.mprID LEFT JOIN srp_erp_segment segment ON segment.segmentID = actionitems.assignedSegmentID LEFT JOIN srp_employeesdetails employeeresponsible ON employeeresponsible.EIdNo = actionitems.responsibleEmpID LEFT JOIN srp_erp_company company ON company.company_id = actionitems.assignedCompanyID WHERE createdByEmpID = '.$responsibleID.' '.$search_string.' ')->result_array();

        return $output;
    }

    function edit_to_do_list()
    {
        $companyID = current_companyID();
        $autoID = trim($this->input->post('autoID') ?? '');
        $data = $this->db->query("select autoId,description,priority,startDate,startTime FROM srp_erp_to_do_list WHERE autoId = {$autoID}")->row_array();
       //var_dump($data);
        return $data;

    }
    function update_to_do_list()
    {
        $data['autoId'] = trim($this->input->post('autoId') ?? '');
        $data['startDate'] = trim($this->input->post('edit_startDate') ?? '');
        $data['startTime'] = trim($this->input->post('edit_startTime') ?? '');
        $data['description'] = trim($this->input->post('edit_description') ?? '');
        $data['priority'] = trim($this->input->post('edit_priority') ?? '');
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->where('autoId', $data['autoId']);
        $this->db->update('srp_erp_to_do_list', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'To Do List Update Failed ' . $this->db->_error_message());

        } else {
            $this->db->trans_commit();
            return array('s', 'Record Updated Successfully.');
        }
    }

    function fetch_PO_localVSinternational_permonth($financeyearid, $country)
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $FinanceYearData = $this->db->query("SELECT YEAR(beginingDate) as year FROM srp_erp_companyfinanceyear WHERE companyFinanceYearID = $financeyearid")->row_array();
        $FinanceYear = $FinanceYearData['year'];

        for($x = 1; $x <= 12; $x++)
        {
            $local =  $this->db->query("SELECT
	IFNULL(COUNT(purchaseOrderID), 0) AS totalDocuments 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID} 
	AND YEAR(documentDate) = '{$FinanceYear}' AND MONTH(documentDate) = {$x}
	AND supplierCountry = '{$country}' 
	AND (
		(closedYN = 0 AND approvedYN = 1)
		OR (
			closedYN = 1 AND approvedYN = 5
		AND purchaseOrderID IN ( SELECT purchaseOrderMastertID FROM srp_erp_grvdetails WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID UNION ALL SELECT purchaseOrderMastertID FROM srp_erp_paysupplierinvoicedetail WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID ) 
	))")->row_array();

            $data['local'][] = $local['totalDocuments'];

            $international =  $this->db->query("SELECT
	IFNULL(COUNT(purchaseOrderID), 0) AS totalDocuments 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN srp_erp_suppliermaster ON srp_erp_purchaseordermaster.supplierID = srp_erp_suppliermaster.supplierAutoID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID}
	/*AND approvedYN = 1 */
	AND YEAR(documentDate) = '{$FinanceYear}' AND MONTH(documentDate) = {$x}
	AND supplierCountry != '{$country}' 
	AND (
		(closedYN = 0 AND approvedYN = 1) 
		OR (
			closedYN = 1 AND approvedYN = 5
		AND purchaseOrderID IN ( SELECT purchaseOrderMastertID FROM srp_erp_grvdetails WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID UNION ALL SELECT purchaseOrderMastertID FROM srp_erp_paysupplierinvoicedetail WHERE purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID ) 
	))")->row_array();

            $data['international'][] = $international['totalDocuments'];
        }

        return $data;
    }

    function fetch_supplier_delivery_analysis()
    {
        $data = array();
        $companyID = $this->common_data['company_data']['company_id'];

        /** fetch top 10 suppliers for PO */
        $supplierIDs = $this->db->query("SELECT
	IFNULL( COUNT( purchaseOrderID ), 0 ) AS totalDocuments,
	supplierID,
	CONCAT( '\"', supplierCode, ' | ', supplierName, '\"' ) AS supplierSystemCode,
	/*CONCAT( '\"', supplierCode, '\"' ) AS supplierSystemCode,*/
	supplierCode AS supplierCode,
	supplierName 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN ( SELECT SUM( requestedQty ) AS docQty, purchaseOrderID AS masterID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.masterID = srp_erp_purchaseordermaster.purchaseOrderID
	INNER JOIN (
	SELECT
		SUM( qty ) AS qty,
		purchaseOrderMastertID 
	FROM
		(SELECT purchaseOrderMastertID, SUM(receivedQty) AS qty 
		FROM srp_erp_grvdetails 
		LEFT JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID 
		WHERE approvedYN = 1
		GROUP BY purchaseOrderMastertID
		        UNION ALL 
         SELECT purchaseOrderMastertID, SUM(requestedQty) AS qty 
         FROM srp_erp_paysupplierinvoicedetail 
         LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
		WHERE approvedYN = 1 
         GROUP BY purchaseOrderMastertID) a 
	GROUP BY
		purchaseOrderMastertID 
	) pulledDetails ON pulledDetails.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID} 
	AND (
		( approvedYN = 1 AND det.docQty <= pulledDetails.qty ) 
	OR ( closedYN = 1 AND approvedYN = 5 )) 
GROUP BY
	supplierID 
ORDER BY
	totalDocuments DESC 
	LIMIT 10")->result_array();

        $data['suppliers'] = array_column($supplierIDs, 'supplierSystemCode');

        /** fetch details for chart */
        $docdetails = array();
        foreach ($supplierIDs as $val){
            $details = $this->db->query("SELECT
	purchaseOrderID AS purchaseOrderID,
	supplierID,
	documentDate,
	MAX( pulledDetails.receivedDate ) AS receivedDate,
	DATEDIFF(receivedDate, expectedDeliveryDate) AS DateDiff,
	qty,
	srp_erp_purchaseordermaster.supplierName
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN ( SELECT SUM( requestedQty ) AS docQty, purchaseOrderID AS masterID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.masterID = srp_erp_purchaseordermaster.purchaseOrderID
	INNER JOIN (
	SELECT
		SUM( qty ) AS qty,
		purchaseOrderMastertID,
		MAX( receivedDate ) AS receivedDate 
	FROM
		(
		SELECT
			purchaseOrderMastertID,
			SUM( receivedQty ) AS qty,
			MAX( grvDate ) AS receivedDate 
		FROM
			srp_erp_grvdetails
			LEFT JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID 
		WHERE
			approvedYN = 1 
		GROUP BY
			purchaseOrderMastertID UNION ALL
		SELECT
			purchaseOrderMastertID,
			SUM( requestedQty ) AS qty,
			MAX( invoiceDate ) AS receivedDate 
		FROM
			srp_erp_paysupplierinvoicedetail
			LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
		WHERE
			approvedYN = 1 
		GROUP BY
			purchaseOrderMastertID 
		) a 
	GROUP BY
		purchaseOrderMastertID 
	) pulledDetails ON pulledDetails.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID 
WHERE
	srp_erp_purchaseordermaster.companyID = ($companyID) 
	AND (
		( approvedYN = 1 AND det.docQty <= pulledDetails.qty ) 
	OR ( closedYN = 1 AND approvedYN = 5 )) 
	AND supplierID = {$val['supplierID']} 
GROUP BY
	purchaseOrderID")->result_array();

            $view['ontime'] = 0;
            $view['oneWeek'] = 0;
            $view['twoWeek'] = 0;
            $view['threeWeek'] = 0;
            $view['overMonth'] = 0;
            $view['totalCount'] = 0;
            foreach($details AS $det) {
                if ($det['DateDiff'] < 7) {
                    $view['ontime'] += 1;
                } else if ($det['DateDiff'] < 14) {
                    $view['oneWeek'] += 1;
                } else if ($det['DateDiff'] < 21) {
                    $view['twoWeek'] += 1;
                } else if ($det['DateDiff'] < 28) {
                    $view['threeWeek'] += 1;
                } else  {
                    $view['overMonth'] += 1;
                }
                $view['totalCount'] += 1;
            }
            $view['Supplier'] = $val['supplierName'];
            array_push($docdetails, $view);

            $supplierCodeLength = $val['supplierCode'];
        }
        $data['codelength'] = strlen($supplierCodeLength);
        $data['ontime'] = array_column($docdetails, 'ontime');
        $data['oneWeek'] = array_column($docdetails, 'oneWeek');
        $data['twoWeek'] = array_column($docdetails, 'twoWeek');
        $data['threeWeek'] = array_column($docdetails, 'threeWeek');
        $data['overMonth'] = array_column($docdetails, 'overMonth');
        $data['totalCount'] = array_column($docdetails, 'totalCount');
        $data['Supplier'] = array_column($docdetails, 'Supplier');

        return $data;
    }

    function fetch_supplier_delivery_analysis_drilldown()
    {
        $supplier = $this->input->post("supplier");
        $filterdocval = explode(" | ", $supplier);
        $companyID = $this->common_data['company_data']['company_id'];

        $data['master'] = $this->db->query("SELECT
	supplierName,
	supplierSystemCode 
FROM
	srp_erp_suppliermaster 
WHERE
    companyID = {$companyID} AND
	supplierSystemCode = '{$filterdocval[0]}'")->row_array();

        $data['details'] = $this->db->query("SELECT
	purchaseOrderID AS purchaseOrderID,
	purchaseOrderCode,
	DATEDIFF(receivedDate, expectedDeliveryDate) AS DateDiff,
	documentDate,
	expectedDeliveryDate,
	MAX( pulledDetails.receivedDate ) AS receivedDate 
FROM
	`srp_erp_purchaseordermaster`
	LEFT JOIN ( SELECT SUM( requestedQty ) AS docQty, purchaseOrderID AS masterID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.masterID = srp_erp_purchaseordermaster.purchaseOrderID
	INNER JOIN (
	SELECT
		SUM( qty ) AS qty,
		purchaseOrderMastertID,
		MAX( receivedDate ) AS receivedDate 
	FROM
		(
		SELECT
			purchaseOrderMastertID,
			SUM( receivedQty ) AS qty,
			MAX( grvDate ) AS receivedDate 
		FROM
			srp_erp_grvdetails
			LEFT JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID 
		WHERE
			approvedYN = 1 
		GROUP BY
			purchaseOrderMastertID UNION ALL
		SELECT
			purchaseOrderMastertID,
			SUM( requestedQty ) AS qty,
			MAX( invoiceDate ) AS receivedDate 
		FROM
			srp_erp_paysupplierinvoicedetail
			LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
		WHERE
			approvedYN = 1 
		GROUP BY
			purchaseOrderMastertID 
		) a 
	GROUP BY
		purchaseOrderMastertID 
	) pulledDetails ON pulledDetails.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID 
WHERE
	srp_erp_purchaseordermaster.companyID = {$companyID} 
	AND (
		( approvedYN = 1 AND det.docQty <= pulledDetails.qty ) 
	OR ( closedYN = 1 AND approvedYN = 5 )) 
	AND supplierCode = '{$filterdocval[0]}'
GROUP BY
	purchaseOrderID")->result_array();

        return $data;
    }

    function fetch_raw_materials_avg_purchase()
    {
        $supplier = $this->input->post("supplier");
        $filterdocval = explode(" | ", $supplier);
        $companyID = $this->common_data['company_data']['company_id'];
        $data['master'] = '';
        /* $data['master'] = $this->db->query("SELECT
            supplierName,
            supplierSystemCode 
        FROM
            srp_erp_suppliermaster 
        WHERE
            companyID = {$companyID} AND
            supplierSystemCode = '{$filterdocval[0]}'")->row_array();

                $data['details'] = $this->db->query("SELECT
            purchaseOrderID AS purchaseOrderID,
            purchaseOrderCode,
            DATEDIFF(receivedDate, expectedDeliveryDate) AS DateDiff,
            documentDate,
            expectedDeliveryDate,
            MAX( pulledDetails.receivedDate ) AS receivedDate 
        FROM
            `srp_erp_purchaseordermaster`
            LEFT JOIN ( SELECT SUM( requestedQty ) AS docQty, purchaseOrderID AS masterID FROM srp_erp_purchaseorderdetails GROUP BY purchaseOrderID ) det ON det.masterID = srp_erp_purchaseordermaster.purchaseOrderID
            INNER JOIN (
            SELECT
                SUM( qty ) AS qty,
                purchaseOrderMastertID,
                MAX( receivedDate ) AS receivedDate 
            FROM
                (
                SELECT
                    purchaseOrderMastertID,
                    SUM( receivedQty ) AS qty,
                    MAX( grvDate ) AS receivedDate 
                FROM
                    srp_erp_grvdetails
                    LEFT JOIN srp_erp_grvmaster ON srp_erp_grvdetails.grvAutoID = srp_erp_grvmaster.grvAutoID 
                WHERE
                    approvedYN = 1 
                GROUP BY
                    purchaseOrderMastertID UNION ALL
                SELECT
                    purchaseOrderMastertID,
                    SUM( requestedQty ) AS qty,
                    MAX( invoiceDate ) AS receivedDate 
                FROM
                    srp_erp_paysupplierinvoicedetail
                    LEFT JOIN srp_erp_paysupplierinvoicemaster ON srp_erp_paysupplierinvoicemaster.InvoiceAutoID = srp_erp_paysupplierinvoicedetail.InvoiceAutoID 
                WHERE
                    approvedYN = 1 
                GROUP BY
                    purchaseOrderMastertID 
                ) a 
            GROUP BY
                purchaseOrderMastertID 
            ) pulledDetails ON pulledDetails.purchaseOrderMastertID = srp_erp_purchaseordermaster.purchaseOrderID 
        WHERE
            srp_erp_purchaseordermaster.companyID = {$companyID} 
            AND (
                ( approvedYN = 1 AND det.docQty <= pulledDetails.qty ) 
            OR ( closedYN = 1 AND approvedYN = 5 )) 
            AND supplierCode = '{$filterdocval[0]}'
        GROUP BY
            purchaseOrderID")->result_array(); */

        return $data;
    }
}
