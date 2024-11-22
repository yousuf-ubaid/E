<?php

class Boq_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('employee_helper');


    }


    function save_boq_category()
    {


        $this->db->trans_start();

        $data['categoryCode'] = trim($this->input->post('CatCode') ?? '');
        $data['projectID'] = trim($this->input->post('projectID') ?? '');
        $data['categoryDescription'] = trim($this->input->post('CatDescrip') ?? '');
        $GLAutoID = trim($this->input->post('GLcode') ?? '');
        $get_gl = $this->db->query("SELECT GLDescription,systemAccountCode FROM srp_erp_chartofaccounts WHERE GLAutoID={$GLAutoID}")->row_array();
        $data['companyID'] = $this->common_data['company_data']['company_id'];


        $data['GLDescription'] = $get_gl['GLDescription'];
        $data['GLcode'] = $get_gl['systemAccountCode'];
        $data['GLAutoID'] = $GLAutoID;
        $data['sortOrder'] = trim($this->input->post('SortOrder') ?? '');


        if (trim($this->input->post('categoryID') ?? '')) {
            $data['modifiedPcID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');

        } else {
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = date('Y-m-d H:i:s');
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');
            //$data['timestamp'] = date('Y-m-d h:s');

            $this->db->insert('srp_erp_boq_category', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Category save failed');
                $this->db->trans_rollback();
            } else {

                $this->db->trans_commit();

                return array('s', 'Category saved successfully');
            }
        }
    }

    function getCategorySortID()
    {

        $this->db->select_max('sortOrder');
        $this->db->from('srp_erp_boq_category');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('projectID', $this->input->post('projectID'));

        $data = $this->db->get()->row_array();
        if (is_null($data['sortOrder'])) {
            return 1;
        } else {
            return $data['sortOrder'] + 1;
        }
    }

    function getSubcategorySortID()
    {

        $this->db->select_max('sortOrder');
        $this->db->from('srp_erp_boq_subcategory');
        $this->db->where('categoryID', $this->input->post('MainCatID'));


        $data = $this->db->get()->row_array();
        if (is_null($data['sortOrder'])) {
            return $this->input->post('categoryID') + 0.1;
        } else {
            return $data['sortOrder'] + 0.1;
        }
    }

    function save_boq_subcategory()
    {
        $this->db->trans_start();

        $data['categoryID'] = trim($this->input->post('MainCatID') ?? '');
        $data['description'] = trim($this->input->post('SubCatDes') ?? '');
        $data['sortOrder'] = trim($this->input->post('subSortOrder') ?? '');
        $data['unitID'] = trim($this->input->post('unitID') ?? '');


        if (trim($this->input->post('AutoID') ?? '')) {
            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = date('Y-m-d h:s');
        } else {
            $data['createdUserGroup'] = user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = date('Y-m-d h:s');
            // $data['timestamp'] = date('Y-m-d h:s');

            $this->db->insert('srp_erp_boq_subcategory', $data);
            $last_id = $this->db->insert_id();

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('e', 'Save Failed');
            } else {
                $this->db->trans_commit();

                return array('s', 'Saved Successfully', $last_id);
            }
        }
    }

    function getReportingCurrency()
    {
        return load_currency_drop();
    }


    function save_boq_header()
    {

        $this->db->trans_start();

        $date_format_policy = date_format_policy();
        $projectStartDate = $this->input->post('prjStartDate');
        $projectEndDate = $this->input->post('prjEndDate');
        $documentdate = $this->input->post('documentdate');
        $projectname = $this->input->post('projectname');
        $customertype = $this->input->post('customertype');
        $projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        $projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        $documentdate = input_format_date($documentdate, $date_format_policy);

        $data['projectDateFrom'] = $projectStartDate;
        $data['customerType'] = $customertype;
        $data['projectDateTo'] = $projectEndDate;
        $data['projectDocumentDate'] = $documentdate;
        $data['comment'] = trim($this->input->post('comments') ?? '');
        $data['retensionPercentage'] = $this->input->post('retentionpercentage');
        $data['advancePercentage'] = $this->input->post('advancepercentage');
        $data['warrantyPeriod'] = $this->input->post('warrantyPeriod');




        if (trim($this->input->post('headerID') ?? '')) {
            $headerID = $this->input->post('headerID');
            $projectMaster = $this->db->query("SELECT
                                                    projectCode,
	                                                DATE(projectDateFrom) as projectDateFrom,
	                                                DATE(projectDateTo) as projectDateTo,
                                                    DATE(projectDocumentDate) as projectDocumentDate,
                                                    projectID,
	                                                comment,
	                                                modifiedPCID,
                                                    modifiedUserID,
                                                    modifiedDateTime,
                                                    retensionPercentage,
                                                    customerType,advancePercentage,
                                                    warrantyPeriod
	                                            
                                                    FROM
	                                                `srp_erp_boq_header`
	                                                where 
	                                                headerID = $headerID")->row_array();



            $data['modifiedPCID'] = current_pc();
            $data['modifiedUserID'] = current_userID();
            $data['modifiedDateTime'] = date('Y-m-d h:s');
            /*  $data['projectDateFrom']     = trim($this->input->post('prjStartDate') ?? '');
              $data['projectDateTo']       = trim($this->input->post('prjEndDate') ?? '');
              $data['projectDocumentDate'] = trim($this->input->post('documentdate') ?? '');
              $data['comment']             = trim($this->input->post('comments') ?? '');*/
            $this->db->where('headerID', $this->input->post('headerID'));
            $this->db->update('srp_erp_boq_header', $data);



            foreach ($data as $key => $val) {
                $colname = audit_log_colname($key, 'srp_erp_boq_header');

                if($val!= $projectMaster[$key])
                {

                        if(($colname['col_name'] == 'projectDateFrom')||($colname['col_name'] == 'projectDateTo')||($colname['col_name'] == 'projectDocumentDate')||($colname['col_name'] == 'comment')||($colname['col_name'] == 'customerType'))
                        {
                            $auditlog['old_val'] = $projectMaster[$key];
                            $auditlog['display_old_val'] = $projectMaster[$key];
                            $auditlog['new_val'] = $val;
                            $auditlog['display_new_val'] = $val;
                            $auditlog['tableName'] = $colname['tbl_name'];
                            $auditlog['columnName'] = $colname['col_name'];
                            $auditlog['rowID'] = $headerID;
                            $auditlog['companyID'] = current_companyID();
                            $auditlog['userID'] = current_userID();
                            $auditlog['DocumentType'] = 'PM';
                            $auditlog['DocumentName'] = $projectMaster['projectCode'];
                            $auditlog['timestamp'] = current_date();
                            $this->db->insert('srp_erp_audit_log', $auditlog);
                        }

                }
            };
          $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('e', 'Save Failed');
            } else {


                $this->db->trans_commit();

                return array('s', 'Saved Successfully ', $this->input->post('headerID'));

            }


        } else {
            $projectID = $this->input->post('projectID');
            /*$exist = $this->db->query("select * from `srp_erp_boq_header` WHERE projectID=$projectID")->row_array();
            if (!empty($exist)) {
                return array('e', 'Project already assigned');
                exit;
            }*/
            $data['projectID'] = trim($this->input->post('projectID') ?? '');
            $data['projectDescription'] = $projectname;
            $data['documentID'] = 'P';
            $data['companyID'] = current_companyID();
            $data['companyName'] = current_companyName();
            $data['segementID'] = trim($this->input->post('segement') ?? '');
            $data['createdDateTime'] = trim($this->input->post('documentdate') ?? '');
            $customer = trim($this->input->post('customer') ?? '');
            $data['customerCode'] = $customer;
            $data['customerName'] = trim($this->input->post('customerName') ?? '');

            $data['customerCurrencyID'] = trim($this->input->post('currency') ?? '');


            $data['createdUserGroup'] = user_group();
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = date('Y-m-d h:s:i');
            $this->load->library('sequence');
            $data['projectCode'] = $this->sequence->sequence_generator('P');


            /**/
            $data['localCurrencyID'] = $this->common_data['company_data']['company_default_currencyID'];
            $data['localCurrencyName'] = $this->common_data['company_data']['company_default_currency'];

            $default_currency = currency_conversionID($this->input->post('currency'), $data['localCurrencyID']);
            $data['localCurrencyER'] = $default_currency['conversion'];
            $reporting_currency = currency_conversionID($data['localCurrencyID'],
                $this->common_data['company_data']['company_reporting_currencyID']);
            $data['rptCurrencyID'] = $this->common_data['company_data']['company_reporting_currencyID'];
            $data['rptCurencyName'] = $this->common_data['company_data']['company_reporting_currency'];
            $data['rptCurrencyER'] = $reporting_currency['conversion'];
            /**/


            $this->db->insert('srp_erp_boq_header', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('e', 'Save Failed');
            } else {

                $this->db->trans_commit();

                return array('s', 'Successfully Saved', $last_id, $data['projectCode']);
                /*   return array('status' => true, 'last_id' => $last_id, 'pcode' => $data['projectCode']);*/
            }
        }

    }

    function getSubcategoryDropDown()
    {
        $this->db->select('subCategoryID,description,unitID');
        $this->db->from('srp_erp_boq_subcategory');


        $this->db->where('categoryID', $this->input->post('categoryID'));

        $data = $this->db->get()->result_array();

        return $data;
    }

    function save_boq_header_details()
    {
        $this->db->trans_start();
        $data['unitID'] = trim($this->input->post('unitID') ?? '');
        $data['categoryID'] = trim($this->input->post('category') ?? '');
        $data['subCategoryID'] = trim($this->input->post('subcategory') ?? '');
        $data['itemDescription'] = trim($this->input->post('description') ?? '');
        $data['headerID'] = trim($this->input->post('headerID') ?? '');;
        $companyID = current_companyID();

        $d = $this->db->query("select * from srp_erp_boq_category where categoryID={$data['categoryID']}")->row_array();


        $data['categoryName'] = $d['categoryDescription'];

        $s = $this->db->query("select * from srp_erp_boq_subcategory where subCategoryID={$data['subCategoryID']}")->row_array();

        $data['subCategoryName'] = $s['description'];
        $data['tendertype'] = $this->input->post('tendertype');




        if (trim($this->input->post('detailID') ?? '')) {

        } else {

            $this->db->insert('srp_erp_boq_details', $data);
            $last_id = $this->db->insert_id();

            foreach ($data as $key => $val)
            {
                $colname = audit_log_colname($key, 'srp_erp_boq_details');
                if (($colname['col_name']=='categoryID'))
                {
                    $category = $this->db->query("SELECT header.projectCode,srp_erp_boq_details.Categoryid,CONCAT(category.categoryCode ,'-',categoryDescription) as category FROM `srp_erp_boq_details` 
                                                       LEFT JOIN srp_erp_boq_header header on header.headerID = srp_erp_boq_details.headerID
	                                                   LEFT JOIN srp_erp_boq_category category on category.categoryID = srp_erp_boq_details.categoryID
	                                                   where 
	                                                   header.companyID = $companyID 
                                                       AND	srp_erp_boq_details.headerID = {$data['headerID']}")->row_array();
                    $auditlog['old_val'] = NULL;
                    $auditlog['display_old_val'] =NULL;
                    $auditlog['new_val'] = $category['Categoryid'];
                    $auditlog['display_new_val'] = $category['category'];
                    $auditlog['tableName'] = $colname['tbl_name'];
                    $auditlog['columnName'] = $colname['col_name'];
                    $auditlog['rowID'] = $data['headerID'];
                    $auditlog['companyID'] = $companyID;
                    $auditlog['DocumentName'] = $category['projectCode'];
                    $auditlog['DocumentType'] = 'PM';
                    $auditlog['userID'] = current_userID();
                    $auditlog['timestamp'] = current_date();
                    $this->db->insert('srp_erp_audit_log', $auditlog);

                }else if(($colname['col_name']=='subCategoryID'))
                {
                    $subcategory = $this->db->query("SELECT header.projectCode, srp_erp_boq_details.subCategoryID, description AS subcatcategory 
                                                          FROM `srp_erp_boq_details` LEFT JOIN srp_erp_boq_header header ON header.headerID = srp_erp_boq_details.headerID
                                                          LEFT JOIN srp_erp_boq_subcategory subcategory ON subcategory.subCategoryID = srp_erp_boq_details.subCategoryID WHERE
	                                                      header.companyID = $companyID AND srp_erp_boq_details.headerID = {$data['headerID']}")->row_array();
                    $auditlog['old_val'] = NULL;
                    $auditlog['display_old_val'] =NULL;
                    $auditlog['new_val'] = $subcategory['subCategoryID'];
                    $auditlog['display_new_val'] = $subcategory['subcatcategory'];
                    $auditlog['tableName'] = $colname['tbl_name'];
                    $auditlog['columnName'] = $colname['col_name'];
                    $auditlog['rowID'] = $data['headerID'];
                    $auditlog['companyID'] = $companyID;
                    $auditlog['DocumentName'] = $subcategory['projectCode'];
                    $auditlog['DocumentType'] = 'PM';
                    $auditlog['userID'] = current_userID();
                    $auditlog['timestamp'] = current_date();
                    $this->db->insert('srp_erp_audit_log', $auditlog);
                }else if(($colname['col_name']=='itemDescription'))
                {
                    $project = $this->db->query("SELECT projectCode FROM srp_erp_boq_header where companyID = $companyID AND headerID =  {$data['headerID']}")->row_array();
                    $auditlog['old_val'] = NULL;
                    $auditlog['display_old_val'] =NULL;
                    $auditlog['new_val'] = $val;
                    $auditlog['display_new_val'] = $val;
                    $auditlog['tableName'] = $colname['tbl_name'];
                    $auditlog['columnName'] = $colname['col_name'];
                    $auditlog['rowID'] = $data['headerID'];
                    $auditlog['companyID'] = $companyID;
                    $auditlog['DocumentName'] = $project['projectCode'];
                    $auditlog['DocumentType'] = 'PM';
                    $auditlog['userID'] = current_userID();
                    $auditlog['timestamp'] = current_date();
                    $this->db->insert('srp_erp_audit_log', $auditlog);
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                return array('e' => 'Save Failed');
            } else {
                $this->db->trans_commit();

                return array('s', $last_id);
            }
        }

    }

    function fetchItems()
    {
        $companyID = current_companyID();
        $search_string = "%" . $_GET['q'] . "%";
        /* return $this->db->query('SELECT itemmaster.primaryCode,itemmaster.financeCategoryMaster,itemassigned.itemUnitOfMeasure,units.UnitShortCode,itemassigned.secondaryItemCode,itemassigned.itemDescription,itemassigned.itemCodeSystem,CONCAT(itemassigned.itemDescription, " (" ,itemassigned.itemPrimaryCode,")") AS "Match" FROM itemassigned INNER JOIN itemmaster ON itemmaster.itemCodeSystem=itemassigned.itemCodeSystem INNER JOIN units ON units.UnitID=itemassigned.itemUnitOfMeasure WHERE itemassigned.isActive = 1 AND itemassigned.isAssigned = -1 AND itemassigned.companyID = "' . $companyID . '" AND (itemassigned.itemPrimaryCode LIKE "' . $search_string . '" OR itemassigned.itemDescription LIKE "' . $search_string . '")')->result_array();*/

        $dataArr = array();
        $dataArr2 = array();
        $data = $this->db->query('SELECT mainCategoryID,subcategoryID,seconeryItemCode,subSubCategoryID,revanueGLCode,itemSystemCode,costGLCode,assteGLCode,defaultUnitOfMeasure,defaultUnitOfMeasureID,itemDescription,itemAutoID,currentStock,companyLocalWacAmount,companyLocalSellingPrice,CONCAT(itemDescription, " (" ,itemSystemCode,")") AS "Match" , isSubitemExist FROM srp_erp_itemmaster WHERE (itemSystemCode LIKE "' . $search_string . '" OR itemDescription LIKE "' . $search_string . '" OR seconeryItemCode LIKE "' . $search_string . '") AND companyID = "' . $companyID . '" AND isActive=1 ')->result_array();

        return $data;

    }

    function save_boq_cost_sheet()
    {
        $this->db->trans_start();
        $tendertype = trim($this->input->post('tendertype_cost') ?? '');
        $data['headerID'] = trim($this->input->post('headerID') ?? '');
        $data['categoryID'] = trim($this->input->post('categoryID') ?? '');
        $data['subCategoryID'] = trim($this->input->post('subcategoryID') ?? '');
        $data['Qty'] = trim($this->input->post('qty') ?? '');
        $data['UnitShortCode'] = trim($this->input->post('uom') ?? '');
        $data['unitCost'] = trim($this->input->post('unitcost') ?? '');
        $data['totalCost'] = trim($this->input->post('totalcost') ?? '');
        $data['costCurrencyCode'] = trim($this->input->post('customerCurrencyID') ?? '');
        $data['detailID'] = trim($this->input->post('detailID') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemautoidproject') ?? '');
        $data['tenderType'] = trim($this->input->post('tendertype_cost') ?? '');
        $companyID = current_companyID();
        $item = trim($this->input->post('search') ?? '');
        $t = explode(' ', $item);
        $v = array_pop($t);
        $itemcode = str_replace(array('(', ')'), '', $v);
        $data['itemCode'] = $itemcode;
        $data['itemdescription'] = $item;   




        if (trim($this->input->post('costingID') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
        } else {
            $data['createdUserGroup'] = user_group();
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');

            $this->db->insert('srp_erp_boq_costing', $data);
            $last_id_new = $this->db->insert_id();
            
            


            
            $this->update_costing_sheet($data['detailID'],$tendertype);
           // $this->boqcostsheedposttender($costing_details);

            $this->db->trans_complete();
            $last_id = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e' => 'Saved Failed');
            } else {
                $this->db->trans_commit();
                $itemDetail = $this->db->query("SELECT itemmaster.itemAutoID, itemmaster.itemSystemCode, itemmaster.itemDescription, currentStock 
                                                     FROM srp_erp_itemmaster itemmaster WHERE itemAutoID = {$data['itemAutoID']}")->row_array();
                $data_item['boqheaderID']= $data['headerID'];
                $data_item['costingID']= $last_id_new;
                $data_item['detailID']= $data['detailID'] ;
                $data_item['type']=1;
                $data_item['itemAutoID']=$data['itemAutoID'];
                $data_item['requiredQty']=  $data['Qty'];
                $data_item['currentQty']=$itemDetail['currentStock'];
                $data_item['companyID'] = $this->common_data['company_data']['company_id'];
                $data_item['createdUserGroup'] = $this->common_data['user_group'];
                $data_item['createdPCID'] = $this->common_data['current_pc'];
                $data_item['createdUserID'] = $this->common_data['current_userID'];
                $data_item['createdUserName'] = $this->common_data['current_user'];
                $data_item['createdDateTime'] = $this->common_data['current_date'];
                $data_item['tenderType'] =trim($this->input->post('tendertype_cost') ?? '');;
                $this->db->insert('srp_erp_projectactivityplanning', $data_item);

                foreach ($data as $key => $val)
                {
                    $colname = audit_log_colname($key, 'srp_erp_boq_costing');
                    if(($colname['col_name']!='createdUserGroup')&&(($colname['col_name']!='createdPCID'))&&(($colname['col_name']!='createdUserID'))&&(($colname['col_name']!='createdDateTime'))&&(($colname['col_name']!='modifiedPCID'))
                        && ($colname['col_name']!='modifiedUserID')&& ($colname['col_name']!='modifiedDateTime')&& ($colname['col_name']!='timestamp')&& ($colname['col_name']!='detailID') && ($colname['col_name']!='categoryID')
                        && ($colname['col_name']!='subCategoryID')  && ($colname['col_name']!='itemSystemCode') && ($colname['col_name']!='UOMID')&&($colname['col_name']!='headerID')&&($colname['col_name']!='itemCode')
                    )
                    {
                        $projectCode = $this->db->query("SELECT CONCAT(header.projectCode,' (',subCategoryName,' Cost ) ') as projectCode FROM
	                                                         `srp_erp_boq_costing` costing LEFT JOIN srp_erp_boq_details detail on  detail.detailID = costing.detailID
	                                                          LEFT JOIN srp_erp_boq_header header on header.headerID = costing.headerID where costingID = $last_id_new")->row_array();
                        $auditlog['old_val'] = NULL;
                        $auditlog['display_old_val'] = NULL;
                        $auditlog['new_val'] = $val;
                        $auditlog['display_new_val'] = $val;
                        $auditlog['tableName'] = $colname['tbl_name'];
                        $auditlog['columnName'] = $colname['col_name'];
                        $auditlog['rowID'] = $data['headerID'];
                        $auditlog['companyID'] = $companyID;
                        $auditlog['userID'] = current_userID();
                        $auditlog['timestamp'] = current_date();
                        $auditlog['DocumentName'] = $projectCode['projectCode'];
                        $auditlog['DocumentType'] = 'PM';
                        $this->db->insert('srp_erp_audit_log', $auditlog);
                    }
                }
                $this->db->trans_complete();
                return array('s', 'Saved Successfully', $last_id);
            }
        }

    }



    function saveboqdetailscalculation()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $tendertype = $this->input->post('tendertype');
        
        $detailID = trim($this->input->post('detailID') ?? '');
        $CI =& get_instance();
        $CI->db->select('srp_erp_boq_details.headerID as headerID, Qty,unitRateTransactionCurrency,totalTransCurrency,markUp,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,totalLocalCurrency,totalRptCurrency,costUnitLocalCurrency,costUnitRptCurrency,totalLabourLocalCurrency,totalLabourRptCurrency,CONCAT(srp_erp_boq_header.projectCode,\' (\',subCategoryName,\' Cost ) \') as projectCode,detailID,rptCurrencyER,localCurrencyER,unitRateLocal,unitRateRptCurrency,totalLocalCurrency,totalRptCurrency,costUnitLocalCurrency,costUnitRptCurrency,totalLabourLocalCurrency,totalLabourRptCurrency,totalCostAmountLocalCurrency,totalCostAmountRptCurrency');
        $CI->db->from('srp_erp_boq_details');
        $CI->db->join("srp_erp_boq_header", "srp_erp_boq_details.headerID = srp_erp_boq_header.headerID", "INNER");
        $CI->db->where('detailID', $detailID);
        $ER = $CI->db->get()->row_array();

        if($tendertype == 1){ 
        
            $data['QtyAfterConfirmedYN'] = trim($this->input->post('Qty') ?? '');
            $data['unitRateLocalAfterConfirmedYN'] = trim($this->input->post('unitRateTransactionCurrency') ?? '') / $ER['localCurrencyER'];
            $data['unitRateTransactionCurrencyAfterConfirmedYN'] = trim($this->input->post('unitRateTransactionCurrency') ?? '');
            $data['totalTransCurrencyAfterConfirmedYN'] = trim($this->input->post('totalTransCurrency') ?? '');
            $data['totalLocalCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '') / $ER['localCurrencyER'];
            $data['unitRateRptCurrencyAfterConfirmedYN'] = trim($this->input->post('unitRateTransactionCurrency') ?? '') / $ER['rptCurrencyER'];
            $data['totalRptCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '') / $ER['rptCurrencyER'];
            $data['markUPAfterConfirmedYN'] = trim($this->input->post('markUp') ?? '');
            $data['costUnitLocalCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '') / $ER['localCurrencyER'];
            $data['totalCostLocalCurrencyAfterConfirmedYN'] = 0;
            $data['costUnitRptCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '') / $ER['rptCurrencyER'];
            $data['totalCostRptCurrencyAfterConfirmedYN'] = 0;
            $data['totalCostTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '');
            $data['totalLabourTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalLabourTranCurrency') ?? '');
            $data['totalLabourLocalCurrencyAfterConfirmedYN'] =trim($this->input->post('totalLabourTranCurrency') ?? '') / $ER['localCurrencyER'];
            $data['totalLabourRptCurrencyAfterConfirmedYN'] =  trim($this->input->post('totalLabourTranCurrency') ?? '') / $ER['rptCurrencyER'];
            $data['totalCostAmountTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostAmountTranCurrency') ?? '');
            $data['totalCostAmountLocalCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostAmountTranCurrency') ?? '') / $ER['localCurrencyER'];
            $data['totalCostAmountRptCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostAmountTranCurrency') ?? '') / $ER['rptCurrencyER'];
        
          
        }else { 
            
            $data['Qty'] = trim($this->input->post('Qty') ?? '');
            $data['unitRateTransactionCurrency'] = trim($this->input->post('unitRateTransactionCurrency') ?? '');
            $data['totalTransCurrency'] = trim($this->input->post('totalTransCurrency') ?? '');
            $data['markUp'] = trim($this->input->post('markUp') ?? '');
            $data['totalCostTranCurrency'] = trim($this->input->post('totalCostTranCurrency') ?? '');
            $data['totalLabourTranCurrency'] = trim($this->input->post('totalLabourTranCurrency') ?? '');
            $data['totalCostAmountTranCurrency'] = trim($this->input->post('totalCostAmountTranCurrency') ?? '');
            $data['unitRateLocal'] = $data['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
            $data['unitRateRptCurrency'] = $data['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];
            $data['totalLocalCurrency'] = $data['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $data['totalRptCurrency'] = $data['totalCostTranCurrency'] / $ER['rptCurrencyER'];
            $data['costUnitLocalCurrency'] = $data['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $data['costUnitRptCurrency'] = $data['totalCostTranCurrency'] / $ER['rptCurrencyER'];
            $data['totalLabourLocalCurrency'] = $data['totalLabourTranCurrency'] / $ER['localCurrencyER'];
            $data['totalLabourRptCurrency'] = $data['totalLabourTranCurrency'] / $ER['rptCurrencyER'];
            $data['totalCostAmountLocalCurrency'] = $data['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
            $data['totalCostAmountRptCurrency'] = $data['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];
            /* Post-tender Before Confirmation Start*/
            $data['QtyAfterConfirmedYN'] = trim($this->input->post('Qty') ?? '');
            $data['unitRateLocalAfterConfirmedYN'] = $data['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
            $data['unitRateTransactionCurrencyAfterConfirmedYN'] = trim($this->input->post('unitRateTransactionCurrency') ?? '');
            $data['totalTransCurrencyAfterConfirmedYN'] = trim($this->input->post('totalTransCurrency') ?? '');
            $data['totalLocalCurrencyAfterConfirmedYN'] = $data['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $data['unitRateRptCurrencyAfterConfirmedYN'] = $data['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];
            $data['totalRptCurrencyAfterConfirmedYN'] = $data['totalCostTranCurrency'] / $ER['rptCurrencyER'];
            $data['markUPAfterConfirmedYN'] = trim($this->input->post('markUp') ?? '');
            $data['costUnitLocalCurrencyAfterConfirmedYN'] = $data['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $data['totalCostLocalCurrencyAfterConfirmedYN'] = 0;
            $data['costUnitRptCurrencyAfterConfirmedYN'] = $data['totalCostTranCurrency'] / $ER['rptCurrencyER'];
            $data['totalCostRptCurrencyAfterConfirmedYN'] = 0;
            $data['totalCostTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostTranCurrency') ?? '');
            $data['totalLabourTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalLabourTranCurrency') ?? '');
            $data['totalLabourLocalCurrencyAfterConfirmedYN'] = $data['totalLabourTranCurrency'] / $ER['localCurrencyER'];
            $data['totalLabourRptCurrencyAfterConfirmedYN'] = $data['totalLabourTranCurrency'] / $ER['rptCurrencyER'];
            $data['totalCostAmountTranCurrencyAfterConfirmedYN'] = trim($this->input->post('totalCostAmountTranCurrency') ?? '');
            $data['totalCostAmountLocalCurrencyAfterConfirmedYN'] = $data['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
            $data['totalCostAmountRptCurrencyAfterConfirmedYN'] = $data['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];
            /* Post-tender Before Confirmation End*/
        }
        /* foreach ($data as $key => $val)
        {
            if($val!=$ER[$key])
            {
                $colname = audit_log_colname($key, 'srp_erp_boq_details');
                $auditlog['old_val'] = $ER[$key];
                $auditlog['display_old_val'] = $ER[$key];
                $auditlog['new_val'] = $val;
                $auditlog['display_new_val'] = $val;
                $auditlog['tableName'] = $colname['tbl_name'];
                $auditlog['columnName'] = $colname['col_name'];
                $auditlog['rowID'] = $ER['headerID'];
                $auditlog['companyID'] = $companyID;
                $auditlog['userID'] = current_userID();
                $auditlog['timestamp'] = current_date();
                $auditlog['DocumentName'] = $ER['projectCode'];
                $auditlog['DocumentType'] = 'PM';
                $this->db->insert('srp_erp_audit_log', $auditlog);
                $this->db->trans_complete();
            }

        } */
        $this->db->where('detailID', $detailID);
        $this->db->update('srp_erp_boq_details', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('status' => false, '' => '');

        } else {
            $this->db->trans_commit();
            return array('status' => TRUE, '' => '');
        }

       

    }

    function getallsavedvalues()
    {
        $convertFormat = convert_date_format_sql();
        $this->db->select("pretenderConfirmedYN,retensionPercentage,advancePercentage,projectDescription,confirmedYN,srp_erp_boq_header.projectID,projectCode, comment, srp_erp_boq_header.companyID,companyName, segementID, 
        customerCode,customerName, customerCurrencyID, DATE_FORMAT(projectDateFrom, '{$convertFormat}') as projectDateFrom, 
        DATE_FORMAT(projectDateTo, '{$convertFormat}') as projectDateTo, DATE_FORMAT(projectDocumentDate, '{$convertFormat}') as projectDocumentDate,
         DATE_FORMAT(eoisubmissiondate, '{$convertFormat}') as eoisubmissiondate,eoistatus,tenderreferenceno,tendervalue,tenderstatus,
        DATE_FORMAT(tendersubmissiondate, '{$convertFormat}') as tendersubmissiondate,`typeofcontract`,commentsstatus,descriptionofthecontract,
        specialconditions,DATE_FORMAT(bidsubmissiondate, '{$convertFormat}') as bidsubmissiondate,DATE_FORMAT(bidduedate, '{$convertFormat}') as bidduedate,
        DATE_FORMAT(bidexpirydate, '{$convertFormat}') as bidexpirydate,bidvalidityperiod,bondvalue,companytosupplybidbond,customerType,budgetapprovalmanagement,
        consultant,totalbudgetestimation,budgetapprovalinternalclient,warrantyPeriod,insPolicyDes, DATE_FORMAT(insPolicyDateFrom, '{$convertFormat}') as insPolicyDateFrom, 
        DATE_FORMAT(insPolicyDateTo, '{$convertFormat}') as insPolicyDateTo,projectName as projectnamepm,CONCAT(srp_erp_segment.segmentCode,' | ',srp_erp_segment.description) as segmentdesc,CONCAT(srp_erp_currencymaster.CurrencyCode,' | ',srp_erp_currencymaster.CurrencyName) as currencypm"); 
        $this->db->from('srp_erp_boq_header');
        $this->db->join('srp_erp_projects','srp_erp_projects.projectID = srp_erp_boq_header.projectID','left');
        $this->db->join('srp_erp_segment','srp_erp_segment.segmentID = srp_erp_boq_header.segementID','left');
        $this->db->join('srp_erp_currencymaster','srp_erp_currencymaster.currencyID = srp_erp_boq_header.localCurrencyID','left');

        $this->db->where('headerID', $this->input->post('headerID'));

        $data = $this->db->get()->row();

        return $data;
    }

    function deleteBoqHeader()
    {
        $this->db->trans_begin();

        $this->db->delete('srp_erp_boq_costing', array('headerID' => $this->input->post('headerID')));
        $this->db->delete('srp_erp_boq_details', array('headerID' => $this->input->post('headerID')));
        $this->db->delete('srp_erp_boq_header', array('headerID' => $this->input->post('headerID')));
        $this->db->delete('srp_erp_projectplanning', array('headerID' => $this->input->post('headerID')));
        $this->db->delete('`srp_erp_projectplanningassignee` ', array('headerID' => $this->input->post('headerID')));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function deleteboqdetail()
    {

        $this->db->trans_begin();

        $this->db->delete('srp_erp_boq_costing', array('detailID' => $this->input->post('detailID')));
        $this->db->delete('srp_erp_boq_details', array('detailID' => $this->input->post('detailID')));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));

        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));

        }

    }

    function deleteboqcost()
    {
        $this->db->delete('srp_erp_boq_costing', array('costingID' => $this->input->post('costingID')));
        $this->db->delete('srp_erp_projectactivityplanning', array('costingID' => $this->input->post('costingID')));

        if ($this->db->affected_rows()) {


            $this->db->select_sum('totalCost');
            $this->db->from('srp_erp_boq_costing');
            $this->db->where('detailID', $this->input->post('detailID'));
            $totalcost = $this->db->get()->row_array();
            $data['Amount'] = $totalcost['totalCost'];
            $detailID = $this->input->post('detailID');

            $details = $this->db->query('select * from srp_erp_boq_details where detailID = ' . $detailID . ' ')->row_array();

            if($details['totalLabourTranCurrencyAfterConfirmedYN'] > 0){
                $unit['unitRateTransactionCurrency'] = ($data['Amount']+$details['totalLabourTranCurrencyAfterConfirmedYN']/$details['Qty']) * (100 + $details['markUp']) / 100; //formaula for get unit rate
            }else{
                $unit['unitRateTransactionCurrency'] = ($data['Amount']) * (100 + $details['markUp']) / 100; //formaula for get unit rate
            }


            $unit['unitRateTransactionCurrency'] = ($data['Amount']) * (100 + $details['markUp']) / 100; //formaula for get unit rate

            $unit['totalTransCurrency'] = $unit['unitRateTransactionCurrency'] * $details['Qty'];
            $unit['totalCostTranCurrency'] = $data['Amount'] * $details['Qty'];


            $unit['totalCostAmountTranCurrency'] = $unit['totalCostTranCurrency'] + $details['totalLabourTranCurrency'];


            //hit other details using header exchange rate

            $this->db->select('rptCurrencyER,localCurrencyER');
            $this->db->from('srp_erp_boq_details');
            $this->db->join("srp_erp_boq_header", "srp_erp_boq_details.headerID = srp_erp_boq_header.headerID", "INNER");
            $this->db->where('detailID', $detailID);
            $ER = $this->db->get()->row_array();


            $unit['unitRateLocal'] = $unit['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
            $unit['unitRateRptCurrency'] = $unit['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];

            $unit['totalLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $unit['totalRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];

            $unit['costUnitLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
            $unit['costUnitRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];

            /*    $unit['totalLabourLocalCurrency'] = $unit['totalLabourTranCurrency'] / $ER['localCurrencyER'];
                $unit['totalLabourRptCurrency']   = $unit['totalLabourTranCurrency'] / $ER['rptCurrencyER'];*/

            $unit['totalCostAmountLocalCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
            $unit['totalCostAmountRptCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];


            $unit['unitCostTranCurrency'] = $data['Amount'];

            $this->db->where('detailID', $detailID);
            $this->db->update('srp_erp_boq_details', $unit);


            echo json_encode(array('s', 'This Item ' . $this->input->post('dec') . '  deleted Successfully .'));

        } else {
            echo json_encode(array('e', 'Operation could not proceed. Please contact IT team'));

        }

    }

    function save_project()
    {
        $date_format_policy = date_format_policy();
        $projectStartDate = $this->input->post('projectStartDate');
        $projectEndDate = $this->input->post('projectEndDate');
        $projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        $projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        $this->db->trans_begin();
        $data['companyID'] = $this->common_data['company_data']['company_id'];

        $data['segmentID'] = $this->input->post('segementID');
        $data['projectStartDate'] = $projectStartDate;
        $data['projectEndDate'] = $projectEndDate;
        $data['projectType'] = 1;//BOQ;

        $data['description'] = $this->input->post('description');


        $projectID = $this->input->post('projectID');
        if ($projectID != NULL) {
            $this->db->update('srp_erp_projects', $data, array('projectID' => $projectID));

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));

            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully Updated'));

            }
        } else {
            $data['projectName'] = $this->input->post('projectName');
            $data['projectCurrencyID'] = $this->input->post('projectCurrencyID');
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdPCID'] = current_pc();
            $data['createdUserID'] = current_userID();
            $data['createdDateTime'] = date('Y-m-d h:s:i');
            $this->db->insert('srp_erp_projects', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));

            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully Saved'));

            }
        }


    }

    function delete_project()
    {

        $this->db->trans_begin();
        $projetID = $this->input->post('projectID');
        $header = $this->db->query("select * from srp_erp_boq_header where projectID=$projetID ")->row_array();
        if (!empty($header)) {
            echo json_encode(array('e', 'You cannot delete, please delete all assigned documents to continue'));
            exit;
        }
        $category = $this->db->query("select * from srp_erp_boq_category where projectID=$projetID ")->row_array();
        if (!empty($category)) {
            echo json_encode(array('e', 'You cannot delete, please delete all assigned category to continue'));
            exit;
        }
        $this->db->delete('srp_erp_projects', array('projectID' => $this->input->post('projectID')));


        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }

    }

    function get_project_data()
    {
        $projectID = $this->input->post('projectID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query('Select DATE_FORMAT(projectStartDate,"' . $convertFormat . '") AS projectStartDate,DATE_FORMAT(projectEndDate,"' . $convertFormat . '") AS projectEndDate,  projectCurrencyID, projectID, projectName, projectType, segmentID,description from srp_erp_projects WHERE projectID=' . $projectID . ' ')->row_array();

        return $data;
    }

    function delete_category()
    {
        $categoryID = $this->input->post('categoryID');
        $this->db->trans_begin();

        $header = $this->db->query("select * from srp_erp_boq_details where categoryID=$categoryID ")->row_array();
        if (!empty($header)) {
            echo json_encode(array('e', 'You cannot delete, Category assigned for a project'));
            exit;
        }

        $this->db->delete('srp_erp_boq_category', array('categoryID' => $this->input->post('categoryID')));
        $this->db->delete('srp_erp_boq_subcategory', array('categoryID' => $this->input->post('categoryID')));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }

    }

    function deletesubcategory()
    {
        $subCategoryID = $this->input->post('subCategoryID');
        $this->db->trans_begin();

        $header = $this->db->query("select * from srp_erp_boq_details where subCategoryID=$subCategoryID ")->row_array();
        if (!empty($header)) {
            echo json_encode(array('e', 'You cannot delete, Sub category assigned for a project'));
            exit;
        }


        $this->db->delete('srp_erp_boq_subcategory', array('subCategoryID' => $this->input->post('subCategoryID')));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function confirm_boq()
    {

        $headerID = $this->input->post('headerID');
        $this->load->library('Approvals');

        $this->db->select('headerID');
        $this->db->where('headerID', $headerID);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_boq_header');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            return array('w', 'Document already confirmed');
        } else {

            $autoApproval = get_document_auto_approval('P');


            $master = $this->db->query("select projectCode from srp_erp_boq_header where headerID={$headerID}")->row_array();
            $approvals_status = $this->approvals->CreateApproval('P', $headerID, $master['projectCode'],
                'Project', 'srp_erp_boq_header', 'headerID');
            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user'],
            );
            if ($approvals_status == 1) {
                $this->db->where('headerID', $headerID);
                $this->db->update('srp_erp_boq_header', $data);
                if ($this->db->trans_status() === true) {
                    $this->db->trans_commit();
                    return array('s', 'Successfully confirmed');
                } else {
                    $this->db->trans_rollback();
                    return array('e', 'Error in approval created process');
                }
            }
            if ($approvals_status == 3) {
                $this->db->trans_rollback();
                return array('w', 'There are no users exist to perform \'Project\' approval for this company.');
            } else {
                $this->db->trans_rollback();
                return array('e', 'Error in process');

            }

        }

    }

    function confirm_project_approval()
    {

        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('headerID') ?? '');
        $level_id = trim($this->input->post('Level') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $comments = trim($this->input->post('comments') ?? '');

        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status, $comments, 'P');
        if($approvals_status == 1) {
            $data = array(
                'pretenderConfirmedYN' => 1
            );
            $this->db->where('headerID', $system_code);
            $this->db->update('srp_erp_boq_header', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            return TRUE;
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('s', 'Project Approved Successfully.');

            return TRUE;
        }

    }

    function save_boq_projectPlanning()
    {
        $date_format_policy = date_format_policy();
        $this->db->trans_start();
        $data['description'] = $this->input->post('description');
        $data['note'] = $this->input->post('note');
        $data['timelineID'] = $this->input->post('projectphase');
        $projectStartDate = $this->input->post('startDate');
        $projectEndDate = $this->input->post('endDate');
        $dependedtask = $this->input->post('dependedtask');
        $relationship = $this->input->post('relationship');
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $timelineID =  $this->input->post('projectphase');
        $projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        $projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        if ($projectStartDate > $projectEndDate) {
            return array( 'e', 'Please check the date');
            exit();
        }
        $isdateupdate = $this->db->query("SELECT MAX(endDate) as endDate FROM `srp_erp_projectplanning` where companyID = $companyID AND timelineID = $timelineID 
                                                   AND headerID = $headerID limit 1 ")->row('endDate');
        if(($projectEndDate > $isdateupdate)||($isdateupdate==''))
        {

            $data_up['actualcompletionDate'] = $projectEndDate;
            $this->db->where('timelineID', $timelineID);
            $this->db->update('srp_erp_projecttimeline', $data_up);
        }

        $data['startDate'] = $projectStartDate;
        $data['endDate'] = $projectEndDate;
        $data['sortOrder'] = $this->input->post('sortOrder');
        $data['headerID'] = $this->input->post('headerID');
        $data['projectCategoryID'] = $this->input->post('project_category');
        $data['percentage'] = $this->input->post('percentage');
        $data['bgColor'] = $this->input->post('color');
        $data['companyID'] = $this->common_data['company_data']['company_id'];
        $data['relatedtaskID'] = $dependedtask;
        $data['relationshiptypeID'] = $relationship;

        $projectPlannningID = $this->input->post('projectPlannningID');
        if ($projectPlannningID != 0) {
            $data['masterID'] = $projectPlannningID;

            $validate = $this->db->query("select startDate,endDate from srp_erp_projectplanning where  projectPlannningID = $projectPlannningID")->row_array();
            if (!empty($validate)) {
                if ($validate['startDate'] > $projectStartDate || $validate['endDate'] < $projectStartDate) {
                    return array( 'e', 'start date should be lower than main task date');
                    exit();
                }
                if ($validate['startDate'] > $projectEndDate || $validate['endDate'] < $projectEndDate) {
                    return array( 'e', 'end date should be lower than main task date');
                    exit();
                }
            }

        } else {
            $data['masterID'] = 0;
        }
        $this->db->insert('srp_erp_projectplanning', $data);
        $last_id = $this->db->insert_id();

        $empID = $this->input->post('assignedEmployee');
        $data['empID'] = $empID[0];
        if (!empty($empID)) {
            $x = 0;
            foreach ($empID as $value) {
                $x++;
                $detail[$x]['projectPlannningID'] = $last_id;
                $detail[$x]['headerID'] = $this->input->post('headerID');
                $detail[$x]['empID'] = $value;
            }


            $this->db->insert_batch('srp_erp_projectplanningassignee', $detail);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Task added successfully');
        }
    }


    function deleteplanning()
    {
        $projectplanningID = $this->input->post('projectPlannningID');
        $projectTime = $this->db->query("SELECT headerID,timelineID FROM `srp_erp_projectplanning` where projectPlannningID = $projectplanningID")->row_array();
        $timelineupdate = $this->db->query("SELECT MAX(endDate) as endate FROM `srp_erp_projectplanning` where headerID = {$projectTime['headerID']} 
                                                AND timelineID = {$projectTime['timelineID']} AND projectPlannningID != $projectplanningID LIMIT 1 ")->row('endate');
        $this->db->trans_begin();
        $data_up['actualcompletionDate'] = $timelineupdate;
        $this->db->where('timelineID', $projectTime['timelineID']);
        $this->db->update('srp_erp_projecttimeline', $data_up);
        $this->db->delete('srp_erp_projectplanning',
            array('projectPlannningID' => $this->input->post('projectPlannningID')));
        $this->db->delete('srp_erp_projectplanning', array('masterID' => $this->input->post('projectPlannningID')));
        $this->db->delete('`srp_erp_projectplanningassignee` ',
            array('projectPlannningID' => $this->input->post('projectPlannningID')));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function summaryTableOrderByselling()
    {

        $sumtotalTransCurrency = 0;
        $sumtotalcatCurrency = 0;
        $sumtotalcatCurrencycost = 0;
        $sumtotalsubcatCurrency = 0;
        $sumtotalsubcatCurrencycost = 0;

        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        $sumcategorytotal = 0;
        $table = '<table id="summarytable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th rowspan="3">S.No</th><th rowspan="3">Items</th><th rowspan="3" >UOM</th></th><th rowspan="2" colspan="3">Selling Price</th><th rowspan="3" style="width: 70px">Actual</th><th rowspan="3" style="width: 70px">Markup %</th><th colspan="5">Cost</th><th rowspan="3">Variation</th></tr>';
        $table .= '<tr>';
        $table .= '<th colspan="2">Material Cost</th><th rowspan="2">Total Labour Cost</th><th rowspan="2">Total Cost</th><th rowspan="2">Actual</th>';
        $table .= '</tr>';

        $table .= '<tr><th>Qty</th><th>Unit Rate</th><th>Total Value</th><th>Unit <!--cost--></th><th>Total</th></tr>';
        $table .= '</thead>';
        $table .= '<tbody>';


        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder,srp_erp_boq_details.detailID');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $this->input->post('headerID'));
        $this->db->group_by("categoryID");
        $this->db->order_by("sortOrder", "ASC");
        $details = $this->db->get()->result_array();


        if ($details) {
            $i = 0;
            foreach ($details as $value) {
                $categorytotal = $this->db->query("SELECT sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` 
                                                        WHERE project_categoryID = '{$value['categoryID']}' AND ( GLType = 'PLI' ) GROUP BY project_categoryID")->row('amount');

                $categorytotalcost = $this->db->query("SELECT sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` 
                                                        WHERE project_categoryID = '{$value['categoryID']}' AND ( GLType = 'PLE' ) GROUP BY project_categoryID")->row('amount');


                $cattot = $categorytotal*-1;
                $cattotcost = $categorytotalcost*-1;
                $i++;
                $table .= '<tr><td><strong>' . $i . '</strong></td>';
                $table .= '<td><strong>' . $value['categoryName'] . '</strong></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td style="text-align: right">'.number_format($cattot,2) .'</td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td style="text-align: right">'.number_format($cattotcost,2) .'</td></tr>';
                $table .= '<td></td>';


                $this->db->select('srp_erp_boq_details.subCategoryID,headerID,srp_erp_boq_details.subCategoryName,sortOrder,variationAmount,srp_erp_boq_details.detailID');
                $this->db->from('srp_erp_boq_details');
                $this->db->join('srp_erp_boq_subcategory',
                    'srp_erp_boq_subcategory.subCategoryID = srp_erp_boq_details.subCategoryID', 'categoryID');
                $this->db->where('headerID', $value['headerID']);
                $this->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
                $this->db->group_by("subCategoryID");
                $this->db->order_by("sortOrder", "ASC");
                $subcategory = $this->db->get()->result_array();
                $sumtotalcatCurrency += $cattot;
                $sumtotalcatCurrencycost += $cattotcost;


                if ($subcategory) {
                    $x = 0;
                    $amount = 0;
                    $cost = 0;
                    $lablour = 0;
                    $totalcost = 0;
                    foreach ($subcategory as $sub) {
                        $projectsubcat = $this->db->query("SELECT sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE
	                                                            project_subCategoryID = '{$sub['detailID']}' AND ( GLType = 'PLI' ) GROUP BY project_subCategoryID")->row('amount');
                        $categorytotalcost = $this->db->query("SELECT sum( transactionAmount / projectExchangeRate ) AS amount FROM `srp_erp_generalledger` WHERE
	                                                            project_subCategoryID = '{$sub['detailID']}' AND ( GLType = 'PLE' ) GROUP BY project_subCategoryID")->row('amount');
                        $projectsubcat = $projectsubcat*-1;
                        $categorytotalcost = $categorytotalcost*-1;
                        $x++;
                        $table .= '<tr><td><strong>' . $i . '.' . $x . '</strong></td>';
                        $table .= '<td><strong>' . $sub['subCategoryName'] . ' </strong></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td style="text-align: right">'.number_format($projectsubcat,2).'</td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td>'.number_format($categorytotalcost,2).'</td>';
                        $table .= '<td style="text-align: right">'.number_format($sub['variationAmount'],2).'</td> </tr>';


                        /**/
                        $sumtotalsubcatCurrency += $projectsubcat;
                        $sumtotalsubcatCurrencycost += $categorytotalcost;

                        $this->db->select('detailID,categoryName,UnitID as UnitShortCode,unitRateTransactionCurrency,categoryID,totalTransCurrency,subCategoryID,subCategoryName,markUp,itemDescription,srp_erp_boq_details.unitID,Qty,unitCostTranCurrency,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,srp_erp_boq_header.customerCurrencyID as customerCurrencyID');
                        $this->db->from('srp_erp_boq_details');
                        $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_boq_details.headerID',
                            'inner');

                        $this->db->where('srp_erp_boq_header.headerID', $value['headerID']);
                        $this->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
                        $this->db->where('srp_erp_boq_details.subCategoryID', $sub['subCategoryID']);

                        $subdetails = $this->db->get()->result_array();

                        if ($subdetails) {

                            $y = 0;
                            foreach ($subdetails as $val) {
                                $y++;

                                $table .= '<tr>';


                                $table .= '<td width="10px">' . $i . '.' . $x . '.' . $y . '</td>';
                                $table .= '<td>' . $val['itemDescription'] . '</td>';
                                /*        $table .= '<td>'.$val['itemDescription'].'</td>';*/
                                $table .= '<td width="40px">' . $val['UnitShortCode'] . '</td>';
                                $table .= '<td width="40px" style="text-align: right">' . $val['Qty'] . '</td>';

                                $amount += $val['totalTransCurrency'];
                                $cost += $val['totalCostTranCurrency'];
                                $lablour += $val['totalLabourTranCurrency'];
                                $totalcost += $val['totalCostAmountTranCurrency'];

                                $sumtotalTransCurrency += $val['totalTransCurrency'];
                                $sumtotalCostTranCurrency += $val['totalCostTranCurrency'];
                                $sumtotalLabourTranCurrency += $val['totalLabourTranCurrency'];
                                $sumtotalCostAmountTranCurrency += $val['totalCostAmountTranCurrency'];

                                $unitRateTransactionCurrency = number_format((float)$val['unitRateTransactionCurrency'], 2, '.',
                                    ',');
                                $totalTransCurrency = number_format((float)$val['totalTransCurrency'], 2, '.', ',');
                                $unitCostTranCurrency = number_format((float)$val['unitCostTranCurrency'], 2, '.', ',');
                                $totalCostTranCurrency = number_format((float)$val['totalCostTranCurrency'], 2, '.', ',');
                                $totalLabourTranCurrency = number_format((float)$val['totalLabourTranCurrency'], 2, '.', ',');
                                $totalCostAmountTranCurrency = number_format((float)$val['totalCostAmountTranCurrency'], 2, '.',
                                    ',');


                                $table .= '<td width="140px" style="text-align: right">' . $unitRateTransactionCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalTransCurrency . '</td>';
                                $table .= '<td width="60px" style="text-align: right">&nbsp;</td>';
                                $table .= '<td width="60px" style="text-align: right">' . $val['markUp'] . '</td>';


                                $table .= '<td width="140px" style="text-align: right">' . $unitCostTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalCostTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalLabourTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalCostAmountTranCurrency . '</td>';
                                $table .= '<td width="140px" style="text-align: right">0</td>';


                                $table .= '</tr>';

                            }
                        }


                    }
                    $table .= '<tr style="background-color: #d6e9c6"><td></td>';
                    $table .= '<td><strong>Sub Total to Summary</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$amount, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$cost, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$lablour, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$totalcost, 2, '.',
                            ',') . '</strong></td>';

                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$totalcost, 2, '.',
                            ',') . '</strong></td>';

                    $table .= '<td style="text-align: right"> </td>';
                    $table .= '</tr>';

                }


            }
        }

        $table .= '<tr>';
        $table .= '<td style="text-align: " colspan="5"><strong>Total</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency, 2, '.', ',');
        $table .= '<td style="text-align: right"><strong>'. number_format((float)($sumtotalcatCurrency +$sumtotalsubcatCurrency), 2, '.', ',');
        '</strong></td>';
        $table .= '<td colspan="3" style="text-align: right">' . number_format((float)$sumtotalCostTranCurrency, 2,
                '.',
                ',');
        '</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalLabourTranCurrency, 2, '.',
                ',');
        '</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalCostAmountTranCurrency, 2,
                '.', ',');
        '</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)($sumtotalcatCurrencycost+$sumtotalsubcatCurrencycost), 2,
                '.', ',');
        '</strong></td>';

        $table .= '</tr>';
        $actualrevenue = 0;
        $actualCost = 0;
        $headerID = $this->input->post('headerID');
        $project = $this->db->query("select projectID from srp_erp_boq_header WHERE headerID={$headerID} ")->row_array();
        $actual = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLI') GROUP BY projectID")->row_array();
        $actualPLE = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLE') GROUP BY projectID")->row_array();



        if (!empty($actual)) {
            $actualrevenue = $actual['amount'];
        }
        if (!empty($actualPLE)) {
            $actualCost = $actualPLE['amount'];
        }

        $table .= '<tr><td colspan="6" style="text-align: right"><strong>Estimated Revenue</strong></td><td style="text-align: right"><strong>' . number_format($sumtotalTransCurrency,
                2) . '</strong></td>

                <td colspan="5" style="text-align: right"><strong>Estimated Cost</strong></td>
                <td style="text-align: right"><strong>' . number_format($sumtotalCostAmountTranCurrency,2) . '</strong></td>



</tr>';
        $table .= '<tr><td colspan="6" style="text-align: right"><strong>Actual Revenue</strong></td><td style="text-align: right"><strong>' . number_format((-1 * $actualrevenue),
                2) . '</strong></td><td colspan="5" style="text-align: right"><strong>Actual Cost</strong></td><td style="text-align: right"><strong>' . number_format($actualCost,
                2) . '</strong></td></tr>';
        $table .= '</tbody></table>';


        return $table;


    }


    function summaryTableOrderBycost()
    {


        $sumtotalTransCurrency = 0;
        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        /*   $table                          = '<table id="summarytable" class="' . table_class() . 'custometbl"><thead>';
           $table                          .= '<tr><th rowspan="3">S.No</th><th rowspan="3">Items</th><th rowspan="3" >Unit</th></th><th rowspan="2" colspan="3">Selling Price</th><th rowspan="3" style="width: 70px">Markup %</th><th colspan="4">Cost</th></tr>';
           $table                          .= '<tr>';
           $table                          .= '<th colspan="2">Material Cost</th><th rowspan="2">Total Labour Cost</th><th rowspan="2">Total Cost</th>';
           $table                          .= '</tr>';

           $table .= '<tr><th>Qty</th><th>Unit Rate</th><th>Total Value</th><th>Unit <!--cost--></th><th>Total</th></tr>';
           $table .= '</thead>';
           $table .= '<tbody>';*/
        $table = '<table id="summarytable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th rowspan="3">S.No</th><th rowspan="3">Items</th><th rowspan="3" >UOM</th><th colspan="4">Cost</th><th rowspan="2" colspan="3">Selling Price</th><th rowspan="3" style="width: 70px">Markup %</th></tr>';
        $table .= '<tr>';
        $table .= '<th colspan="2">Material Cosst</th><th rowspan="2">Total Labour Cost</th><th rowspan="2">Total Cost</th>';
        $table .= '</tr>';

        $table .= '<tr><th>Unit <!--cost--></th><th>Total</th><th>Qty</th><th>Unit Rate</th><th>Total Value</th></tr>';
        $table .= '</thead>';
        $table .= '<tbody>';

        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $this->input->post('headerID'));
        $this->db->group_by("categoryID");
        $this->db->order_by("sortOrder", "ASC");
        $details = $this->db->get()->result_array();


        if ($details) {
            $i = 0;
            foreach ($details as $value) {
                $i++;
                $table .= '<tr><td><strong>' . $i . '</strong></td>';
                $table .= '<td><strong>' . $value['categoryName'] . '</strong></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td>';
                $table .= '<td></td></tr>';


                $this->db->select('srp_erp_boq_details.subCategoryID,headerID,srp_erp_boq_details.subCategoryName,sortOrder');
                $this->db->from('srp_erp_boq_details');
                $this->db->join('srp_erp_boq_subcategory',
                    'srp_erp_boq_subcategory.subCategoryID = srp_erp_boq_details.subCategoryID', 'categoryID');
                $this->db->where('headerID', $value['headerID']);
                $this->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
                $this->db->group_by("subCategoryID");
                $this->db->order_by("sortOrder", "ASC");
                $subcategory = $this->db->get()->result_array();

                if ($subcategory) {
                    $x = 0;
                    $amount = 0;
                    $cost = 0;
                    $lablour = 0;
                    $totalcost = 0;
                    foreach ($subcategory as $sub) {
                        $x++;
                        $table .= '<tr><td><strong>' . $i . '.' . $x . '</strong></td>';
                        $table .= '<td><strong>' . $sub['subCategoryName'] . '</strong></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td>';
                        $table .= '<td></td></tr>';


                        /**/

                        $this->db->select('detailID,categoryName,UnitID as UnitShortCode,unitRateTransactionCurrency,categoryID,totalTransCurrency,subCategoryID,subCategoryName,markUp,itemDescription,srp_erp_boq_details.unitID,Qty,unitCostTranCurrency,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,srp_erp_boq_header.customerCurrencyID as customerCurrencyID');
                        $this->db->from('srp_erp_boq_details');
                        $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_boq_details.headerID',
                            'inner');

                        $this->db->where('srp_erp_boq_header.headerID', $value['headerID']);
                        $this->db->where('srp_erp_boq_details.categoryID', $value['categoryID']);
                        $this->db->where('srp_erp_boq_details.subCategoryID', $sub['subCategoryID']);

                        $subdetails = $this->db->get()->result_array();

                        if ($subdetails) {

                            $y = 0;
                            foreach ($subdetails as $val) {
                                $y++;

                                $table .= '<tr>';


                                $table .= '<td width="10px">' . $i . '.' . $x . '.' . $y . '</td>';
                                $table .= '<td>' . $val['itemDescription'] . '</td>';
                                /*        $table .= '<td>'.$val['itemDescription'].'</td>';*/
                                $table .= '<td width="40px">' . $val['UnitShortCode'] . '</td>';


                                $amount += $val['totalTransCurrency'];
                                $cost += $val['totalCostTranCurrency'];
                                $lablour += $val['totalLabourTranCurrency'];
                                $totalcost += $val['totalCostAmountTranCurrency'];

                                $sumtotalTransCurrency += $val['totalTransCurrency'];
                                $sumtotalCostTranCurrency += $val['totalCostTranCurrency'];
                                $sumtotalLabourTranCurrency += $val['totalLabourTranCurrency'];
                                $sumtotalCostAmountTranCurrency += $val['totalCostAmountTranCurrency'];

                                $unitRateTransactionCurrency = number_format((float)$val['unitRateTransactionCurrency'], 2, '.',
                                    ',');
                                $totalTransCurrency = number_format((float)$val['totalTransCurrency'], 2, '.', ',');
                                $unitCostTranCurrency = number_format((float)$val['unitCostTranCurrency'], 2, '.', ',');
                                $totalCostTranCurrency = number_format((float)$val['totalCostTranCurrency'], 2, '.', ',');
                                $totalLabourTranCurrency = number_format((float)$val['totalLabourTranCurrency'], 2, '.', ',');
                                $totalCostAmountTranCurrency = number_format((float)$val['totalCostAmountTranCurrency'], 2, '.',
                                    ',');

                                $table .= '<td width="140px" style="text-align: right">' . $unitCostTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalCostTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalLabourTranCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalCostAmountTranCurrency . '</td>';

                                $table .= '<td width="40px" style="text-align: right">' . $val['Qty'] . '</td>';
                                $table .= '<td width="140px" style="text-align: right">' . $unitRateTransactionCurrency . '</td>';

                                $table .= '<td width="140px" style="text-align: right">' . $totalTransCurrency . '</td>';

                                $table .= '<td width="60px" style="text-align: right">' . $val['markUp'] . '</td>';


                                $table .= '</tr>';

                            }
                        }


                    }
                    $table .= '<tr style="background-color: #d6e9c6"><td></td>';
                    $table .= '<td><strong>Sub Total to Summary</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';

                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$cost, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$lablour, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$totalcost, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$amount, 2, '.',
                            ',') . '</strong></td>';
                    $table .= '<td></td>';


                    $table .= '</tr>';

                }


            }
        }

        $table .= '<tr>';
        $table .= '<td style="text-align: " colspan="4"><strong>Total</strong></td>';
        $table .= '<td colspan="" style="text-align: right">' . number_format((float)$sumtotalCostTranCurrency, 2,
                '.',
                ',');
        '</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalLabourTranCurrency, 2, '.',
                ',');
        '</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalCostAmountTranCurrency, 2,
                '.', ',');
        '</strong></td>';
        $table .= '<td colspan="3" style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency,
                2, '.', ',');
        '</strong></td>';
        $table .= '<td></td>';


        $table .= '</tr>';
        $actualrevenue = 0;
        $actualCost = 0;
        $headerID = $this->input->post('headerID');
        $project = $this->db->query("select projectID from srp_erp_boq_header WHERE headerID={$headerID} ")->row_array();
        $actual = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$project['projectID']} and (  GLType='PLI') GROUP BY projectID")->row_array();
        $actualPLE = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$project['projectID']} and (  GLType='PLE') GROUP BY projectID")->row_array();
        if (!empty($actual)) {
            $actualrevenue = $actual['amount'];
        }
        if (!empty($actualPLE)) {
            $actualCost = $actualPLE['amount'];
        }

        $table .= '<tr><td colspan="6" style="text-align: right"><strong>Estimated Cost</strong></td><td style="text-align: right"><strong>' . number_format($sumtotalCostAmountTranCurrency,
                2) . '</strong></td><td colspan="2" style="text-align: right"><strong>Estimated Revenue</strong></td><td style="text-align: right"><strong>' . number_format($sumtotalTransCurrency,
                2) . '</strong></td><td></td></tr>';
        $table .= '<tr><td colspan="6" style="text-align: right"><strong>Actual Cost</strong></td><td style="text-align: right"><strong>' . number_format($actualCost,
                2) . '</strong></td><td colspan="2" style="text-align: right"><strong>Actual Revenue</strong></td><td style="text-align: right"><strong>' . number_format((-1 * $actualrevenue),
                2) . '</strong></td><td></td></tr>';
        $table .= '</tbody></table>';

        return $table;
    }

    function detailTableOrderByselling()
    {
        $sumtotalTransCurrency = 0;
        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        $varianceamount = 0;
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');

        $this->db->select('categoryID,headerID,categoryName');
        $this->db->from('srp_erp_boq_details');
        $this->db->where('headerID', $this->input->post('headerID'));
        $this->db->where('tendertype', 0);
        $this->db->group_by("categoryID");
        $details = $this->db->get()->result_array();

        $header = $this->db->query("SELECT IFNULL( pretenderConfirmedYN,0) as  pretenderConfirmedYN, IFNULL( confirmedYN,0) as  confirmedYN FROM `srp_erp_boq_header` where CompanyID = $companyID AND headerID = $headerID")->row_array();

        $readonly = '';
        if($header['confirmedYN'] == 1)
        { 
            $readonly='readonly';
        }

        $table = '<table id="loadcosttable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th rowspan="3">Category</th><th rowspan="3">Description</th><th rowspan="3" >UOM</th></th><th rowspan="2" colspan="3">Selling Price</th><th rowspan="3" width="70px">Markup %</th><th colspan="4">Cost</th><th>&nbsp;</th><th></th></tr>';
        $table .= '<tr>';
        $table .= '<th colspan="2">Material Cost</th><th rowspan="2">Total Labour Cost</th><th rowspan="2">Total Cost</th><th rowspan="2">Variation</th><th rowspan="2"></th>';
        $table .= '</tr>';

        $table .= '<tr><th>Qty</th><th>Unit Rate</th><th>Total Value</th><th>Unit <!--cost--></th><th>Total</th></tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        if ($details) {
            foreach ($details as $val) {

                $table .= '<tr>';
                $table .= '<td  colspan="13"><b>' . $val['categoryName'] . '</b></td>';
                $table .= '</tr>';

                $this->db->select('pretenderConfirmedYN, confirmedYN,issendforrfq,confirmedYN,detailID,categoryName,unitID,unitRateTransactionCurrency,categoryID,totalTransCurrency,subCategoryID,subCategoryName,markUp,itemDescription,Qty,unitCostTranCurrency,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,srp_erp_boq_header.customerCurrencyID as customerCurrencyID,variationAmount');
                $this->db->from('srp_erp_boq_details');
                $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_boq_details.headerID', 'inner');
                $this->db->where('srp_erp_boq_header.headerID', $val['headerID']);
                $this->db->where('srp_erp_boq_details.categoryID', $val['categoryID']);
                $this->db->where('srp_erp_boq_details.tendertype', 0);
                $subdetails = $this->db->get()->result_array();
                if ($subdetails) {

                    foreach ($subdetails as $value) {

                        $sumtotalTransCurrency += $value['totalTransCurrency'];
                        $sumtotalCostTranCurrency += $value['totalCostTranCurrency'];
                        $sumtotalLabourTranCurrency += $value['totalLabourTranCurrency'];
                        $sumtotalCostAmountTranCurrency += $value['totalCostAmountTranCurrency'];
                        $table .= '<tr>';
                        /* $table .= '<td></td>';*/
                        $table .= '<td style="vertical-align: middle">' . $value['subCategoryName'] . '</td>';
                        $table .= '<td style="vertical-align: middle">' . $value['itemDescription'] . '</td>';
                        $table .= '<td style="vertical-align: middle" width="40px">' . $value['unitID'] . '</td>';
                        $table .= '<td width="60px"><input class="form-control srmbtnhn qty-input" style="text-align: right;" min="0" type="number" name="Qty" id="Qty_' . $value['detailID'] . '" value="' . $value['Qty'] . '" onchange="calculateonchangqty(' . $value['detailID'] . ')" '.$readonly.'></td>';
                        $unitRateTransactionCurrency = number_format((float)$value['unitRateTransactionCurrency'], 2, '.', ',');
                        $totalTransCurrency = number_format((float)$value['totalTransCurrency'], 2, '.', ',');
                        $unitCostTranCurrency = number_format((float)$value['unitCostTranCurrency'], 2, '.', ',');
                        $totalCostTranCurrency = number_format((float)$value['totalCostTranCurrency'], 2, '.', ',');
                        $totalLabourTranCurrency = number_format((float)$value['totalLabourTranCurrency'], 2, '.', ',');
                        $totalCostAmountTranCurrency = number_format((float)$value['totalCostAmountTranCurrency'], 2, '.', ',');
                        $varianceamount = number_format((float)$value['variationAmount'], 2, '.', ',');

                        $table .= '<td width="110px"><input  class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="unitRateTransactionCurrency" id="unitRateTransactionCurrency_' . $value['detailID'] . '" value=' . $unitRateTransactionCurrency . '  ></td>';

                        $table .= '<td width="110px"><input  class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="totalTransCurrency" id="totalTransCurrency_' . $value['detailID'] . '" value=' . $totalTransCurrency . '  ></td>';

                        $table .= '<td width="60px"><input class="form-control srmbtnhn" style="text-align: right;" type="number" min="0" name="markUp" id="markUp_' . $value['detailID'] . '" value="' . $value['markUp'] . '" onchange="calculatetotalchangemarkup(' . $value['detailID'] . ')" '.$readonly.'></td>';

                        $table .= '<td width="110"><a onclick="modalcostsheet(' . $value['categoryID'] . ',' . $value['subCategoryID'] . ',' . $value['customerCurrencyID'] . ',' . $value['detailID'] . ','.$header['pretenderConfirmedYN'].',0)" class="btn btn-default btn-xs fa fa-plus srmbtnhn"></a><input  class="form-control" style="width: 70px;
                                            text-align: right;
                                            float: right; text-align: right;" type="text" readonly="readonly" id="unitCostTranCurrency_' . $value['detailID'] . '" name="unitCostTranCurrency" id="" value="' . $unitCostTranCurrency . '"></td>';

                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" readonly="readonly" type="text" name="totalCostTranCurrency" id="totalCostTranCurrency_' . $value['detailID'] . '" value="' . $totalCostTranCurrency . '"  ></td>';

                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalLabourTranCurrency_' . $value['detailID'] . '"  type="text" step="any" value="' . $totalLabourTranCurrency . '" name="totalLabourTranCurrency" onchange="calculatelabourcost(' . $value['detailID'] . ')" '.$readonly.'></td>';
                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalCostAmountTranCurrency_' . $value['detailID'] . '" type="text" step="any" value="' . $totalCostAmountTranCurrency . '" name="totalCostAmountTranCurrency" onchange="calculatetotalamount(' . $value['detailID'] . ')" '.$readonly.'></td>';
                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="varianceamount_' . $value['detailID'] . '" type="text" step="any" value="' . $varianceamount . '" name="varianceamount" onchange="varianceamount(this.value,' . $value['detailID'] . ')" '.$readonly.'></td>';

                        $rfq ='';
                        if ($value['issendforrfq'] != 1)
                        {
                          $rfq.='<span><a onclick="sendtorfq('.$value['detailID'].') "><span title="" rel="tooltip" class="glyphicon glyphicon-ok" data-original-title="Send To RFQ"></span></a>&nbsp;|&nbsp';
                        }

                        if ($value['confirmedYN'] != 1) {
                            $deleteboq = '';
                            if($value['confirmedYN']!=1)
                            { 
                                $deleteboq .= '&nbsp;|&nbsp;<a class="" onclick="deleteBoqdetail(' . $value['detailID'] . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a>';
                            }
                            
                            $table .= '<td class="pull-right"> '.$rfq.'
                             <a class="" onclick="fetch_activityplanning(' . $value['detailID'] . ','.$val['headerID'].',0)" ><span title="" rel="tooltip" class="fa fa-plus" data-original-title="Add"></span></a>'.$deleteboq.'</td>';

                        } else {
                            $table .= '<td> </td>';
                        }
                        $table .= '</tr>';
                    }
                }
            }
        }

        $table .= '<tr>
                        <td style="text-align: " colspan="5"><strong>Total</strong></td>
                        <td style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency, 2, '.',
                ',') . '</strong></td>
                        <td colspan="3" style="text-align: right">' . number_format((float)$sumtotalCostTranCurrency,
                2, '.', ',') . '</strong></td>
                        <td style="text-align: right"><strong>' . number_format((float)$sumtotalLabourTranCurrency, 2,
                '.', ',') . '</strong></td>
                        <td style="text-align: right"><strong>' . number_format((float)$sumtotalCostAmountTranCurrency,
                2, '.', ',') . '</strong></td>
                        <td></td>
                    </tr>';

        $actualrevenue = 0;
        $actualCost = 0;
        $headerID = $this->input->post('headerID');
        $project = $this->db->query("select projectID from srp_erp_boq_header WHERE headerID={$headerID} ")->row_array();
        $actual = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLI') GROUP BY projectID")->row_array();
        $actualPLE = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLE') GROUP BY projectID")->row_array();
        if (!empty($actual)) {
            $actualrevenue = $actual['amount'];
        }
        if (!empty($actualPLE)) {
            $actualCost = $actualPLE['amount'];
        }

        /*$sumtotalCostTranCurrency*/
        $table .= '<tr>
            <td colspan="5" style="text-align: right"><strong>Estimated Revenue</strong></td>
            <td style="text-align: right"><strong>' . number_format($sumtotalTransCurrency, 2) . '</strong></td>
            <td colspan="4" style="text-align: right"><strong>Estimated Cost</strong></td>
            <td style="text-align: right"><strong>' . number_format($sumtotalCostAmountTranCurrency, 2) . '</strong>
            </td>
            <td></td>
        </tr>';
        $table .= '
        <tr>
            <td colspan="5" style="text-align: right"><strong>Actual Revenue</strong></td>
            <td style="text-align: right"><strong>' . number_format((-1 * $actualrevenue), 2) . '</strong></td>
            <td colspan="4" style="text-align: right"><strong>Actual Cost</strong></td>
            <td style="text-align: right"><strong>' . number_format($actualCost, 2) . '</strong></td>
            <td></td>
        </tr>';

        $table .= '</table>';
        
         if($header['confirmedYN']!=1){
       $table.= ' <br> 
        <div class="row pull-right">
                 <div class="col-md-12">
                    <button class="btn btn-success submitWizard" onclick="pretenderConfirmation()">Confirm<!--Confirm--></button>
                </div>
        </div>';
         }

        return $table;
    }

    function detailTableOrderBycost()
    {
        $sumtotalTransCurrency = 0;
        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        $this->db->select('categoryID,headerID,categoryName');
        $this->db->from('srp_erp_boq_details');
        $this->db->where('headerID', $this->input->post('headerID'));
        $this->db->group_by("categoryID");
        $details = $this->db->get()->result_array();
        $table = '<table id="loadcosttable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th rowspan="3">Category</th><th rowspan="3">Description</th><th rowspan="3" >UOM</th><th colspan="4">Cost</th></th><th rowspan="2" colspan="3">Selling Price</th><th rowspan="3" width="70px">Markup %</th><th rowspan="3"></th></tr>';
        $table .= '<tr>';
        $table .= '<th colspan="2">Material Cost</th><th rowspan="2">Total Labour Cost</th><th rowspan="2">Total Cost</th>';
        $table .= '</tr>';

        $table .= '<tr><th>Unit <!--cost--></th><th>Total</th><th>Qty</th><th>Unit Rate</th><th>Total Value</th></tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        if ($details) {
            foreach ($details as $val) {
                $table .= '<tr>';
                $table .= '<td  colspan="12"><b>' . $val['categoryName'] . '</b></td>';
                $table .= '</tr>';

                $this->db->select('detailID,categoryName,unitID,unitRateTransactionCurrency,categoryID,totalTransCurrency,subCategoryID,subCategoryName,markUp,itemDescription,Qty,unitCostTranCurrency,totalCostTranCurrency,totalLabourTranCurrency,totalCostAmountTranCurrency,srp_erp_boq_header.customerCurrencyID as customerCurrencyID');
                $this->db->from('srp_erp_boq_details');
                $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID = srp_erp_boq_details.headerID', 'inner');
                $this->db->where('srp_erp_boq_header.headerID', $val['headerID']);
                $this->db->where('srp_erp_boq_details.categoryID', $val['categoryID']);
                $subdetails = $this->db->get()->result_array();
                if ($subdetails) {
                    foreach ($subdetails as $value) {
                        $sumtotalTransCurrency += $value['totalTransCurrency'];
                        $sumtotalCostTranCurrency += $value['totalCostTranCurrency'];
                        $sumtotalLabourTranCurrency += $value['totalLabourTranCurrency'];
                        $sumtotalCostAmountTranCurrency += $value['totalCostAmountTranCurrency'];
                        $table .= '<tr>';
                        /* $table .= '<td></td>';*/
                        $table .= '<td style="vertical-align: middle">' . $value['subCategoryName'] . '</td>';
                        $table .= '<td style="vertical-align: middle">' . $value['itemDescription'] . '</td>';
                        $table .= '<td style="vertical-align: middle" width="40px">' . $value['unitID'] . '</td>';

                        $unitRateTransactionCurrency = number_format((float)$value['unitRateTransactionCurrency'], 2, '.', ',');
                        $totalTransCurrency = number_format((float)$value['totalTransCurrency'], 2, '.', ',');
                        $unitCostTranCurrency = number_format((float)$value['unitCostTranCurrency'], 2, '.', ',');
                        $totalCostTranCurrency = number_format((float)$value['totalCostTranCurrency'], 2, '.', ',');
                        $totalLabourTranCurrency = number_format((float)$value['totalLabourTranCurrency'], 2, '.', ',');
                        $totalCostAmountTranCurrency = number_format((float)$value['totalCostAmountTranCurrency'], 2, '.', ',');
                        $table .= '<td width="110"><a onclick="modalcostsheet(' . $value['categoryID'] . ',' . $value['subCategoryID'] . ',' . $value['customerCurrencyID'] . ',' . $value['detailID'] . ',0)" class="btn btn-default btn-xs fa fa-plus srmbtnhn"></a><input  class="form-control srmbtnhn" style="width: 70px;
    text-align: right;
    float: right; text-align: right;" type="text" readonly="readonly" id="unitCostTranCurrency_' . $value['detailID'] . '" name="unitCostTranCurrency" id="" value="' . $unitCostTranCurrency . '"></td>';

                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" readonly="readonly" type="text" name="totalCostTranCurrency" id="totalCostTranCurrency_' . $value['detailID'] . '" value="' . $totalCostTranCurrency . '"  ></td>';

                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalLabourTranCurrency_' . $value['detailID'] . '"  type="text" step="any" value="' . $totalLabourTranCurrency . '" name="totalLabourTranCurrency" onchange="calculatelabourcost(' . $value['detailID'] . ')" ></td>';
                        $table .= '<td width="110px"><input class="form-control srmbtnhn" style="text-align: right;" id="totalCostAmountTranCurrency_' . $value['detailID'] . '" type="text" step="any" value="' . $totalCostAmountTranCurrency . '" name="totalCostAmountTranCurrency" onchange="calculatetotalamount(' . $value['detailID'] . ')" ></td>';

                        $table .= '<td width="60px"><input class="form-control srmbtnhn qty-input" style="text-align: right;" min="0" type="number" name="Qty" id="Qty_' . $value['detailID'] . '" value="' . $value['Qty'] . '" onchange="calculateonchangqty(' . $value['detailID'] . ')" ></td>';

                        $table .= '<td width="110px"><input  class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="unitRateTransactionCurrency" id="unitRateTransactionCurrency_' . $value['detailID'] . '" value=' . $unitRateTransactionCurrency . '  ></td>';

                        $table .= '<td width="110px"><input  class="form-control srmbtnhn" style="text-align: right;" type="text" readonly="readonly" name="totalTransCurrency" id="totalTransCurrency_' . $value['detailID'] . '" value=' . $totalTransCurrency . '  ></td>';

                        $table .= '<td width="60px"><input class="form-control srmbtnhn" style="text-align: right;" type="number" min="0" name="markUp" id="markUp_' . $value['detailID'] . '" value="' . $value['markUp'] . '" onchange="calculatetotalchangemarkup(' . $value['detailID'] . ')" ></td>';


                        $table .= '<td> <span class="pull-right "><a class="srmbtnhnr" onclick="deleteBoqdetail(' . $value['detailID'] . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a></td>';


                        $table .= '</tr>';
                    }
                }
            }
        }

        $table .= '<tr>
                        <td style="text-align: " colspan="4"><strong>Total</strong></td>
                             <td colspan="" style="text-align: right">' . number_format((float)$sumtotalCostTranCurrency,
                2, '.', ',') . '</strong></td>
                        <td colspan="" style="text-align: right"><strong>' . number_format((float)$sumtotalLabourTranCurrency,
                2,
                '.', ',') . '</strong></td>
                        <td style="text-align: right"><strong>' . number_format((float)$sumtotalCostAmountTranCurrency,
                2, '.', ',') . '</strong></td>
                        <td  colspan="3" style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency,
                2, '.',
                ',') . '</strong></td>
                   
                        <td></td>
                            <td></td>
                    </tr>';

        $actualrevenue = 0;
        $actualCost = 0;
        $headerID = $this->input->post('headerID');
        $project = $this->db->query("select projectID from srp_erp_boq_header WHERE headerID={$headerID} ")->row_array();
        $actual = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$project['projectID']} and (  GLType='PLI') GROUP BY projectID")->row_array();
        $actualPLE = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$project['projectID']} and (  GLType='PLE') GROUP BY projectID")->row_array();
        if (!empty($actual)) {
            $actualrevenue = $actual['amount'];
        }
        if (!empty($actualPLE)) {
            $actualCost = $actualPLE['amount'];
        }

        /*$sumtotalCostTranCurrency*/
        $table .= '<tr>
  <td colspan="6" style="text-align: right"><strong>Estimated Cost</strong></td>
              <td style="text-align: right"><strong>' . number_format($sumtotalCostAmountTranCurrency, 2) . '</strong>
            </td>
               
            <td colspan="2" style="text-align: right"><strong>Estimated Revenue</strong></td>
         <td style="text-align: right"><strong>' . number_format($sumtotalTransCurrency, 2) . '</strong></td>
        
            <td></td>
            
            <td></td>
        </tr>';
        $table .= '
        <tr>
             <td colspan="6" style="text-align: right"><strong>Actual Cost</strong></td>
            <td style="text-align: right"><strong>' . number_format($actualCost, 2) . '</strong></td>
            <td colspan="2" style="text-align: right"><strong>Actual Revenue</strong></td>
            <td style="text-align: right"><strong>' . number_format((-1 * $actualrevenue), 2) . '</strong></td>
       
            <td></td>
            <td></td>
        </tr>';

        $table .= '</table>';

        return $table;


    }

    function clone_boq_projectPlanning()
    {
        $headerID = $this->input->post('headerID');
        $companyID = current_companyID();
        $header = $this->db->query("select *,	DATE(projectDateFrom) as projectStartDate,DATE(projectDateTo) as projectEndDate,
	                                    DATE(projectDocumentDate) as documentdate from `srp_erp_boq_header` WHERE companyID = $companyID AND headerID =$headerID")->row_array();

        $this->db->trans_start();
        $date_format_policy = date_format_policy();
        $projectStartDate = $header['projectStartDate'];
        $projectEndDate = $header['projectEndDate'];
        $documentdate = $header['projectDocumentDate'];
        $projectStartDate = input_format_date($projectStartDate, $date_format_policy);
        $projectEndDate = input_format_date($projectEndDate, $date_format_policy);
        $documentdate = input_format_date($documentdate, $date_format_policy);

        $data['projectDateFrom'] = $projectStartDate;
        $data['projectDateTo'] = $projectEndDate;
        $data['projectDocumentDate'] = $documentdate;
        $data['comment'] = $header['comment'];
        $projectID = $header['projectID'];
        $data['projectID'] = $projectID;
        $data['clonefromID'] = $headerID;
        $data['documentID'] = 'P';
        $data['companyID'] = $header['companyID'];
        $data['companyName'] = $header['companyName'];
        $data['segementID'] = $header['segementID'];
        $data['createdDateTime'] = $header['createdDateTime'];
        $data['customerCode'] = $header['customerCode'];
        $data['customerName'] = $header['customerName'];
        $data['customerCurrencyID'] = $header['customerCurrencyID'];
        $data['createdUserGroup'] = user_group();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdDateTime'] = date('Y-m-d h:s:i');
        $this->load->library('sequence');
        $data['projectCode'] = $this->sequence->sequence_generator('P');
        $data['localCurrencyID'] = $header['localCurrencyID'];
        $data['localCurrencyName'] = $header['localCurrencyName'];
        $default_currency = currency_conversionID($header['localCurrencyID'], $data['localCurrencyID']);
        $data['localCurrencyER'] = $default_currency['conversion'];
        $reporting_currency = currency_conversionID($data['localCurrencyID'], $header['rptCurrencyID']);
        $data['rptCurrencyID'] = $header['rptCurrencyID'];
        $data['rptCurencyName'] = $header['rptCurencyName'];
        $data['rptCurrencyER'] = $reporting_currency['conversion'];
        $this->db->insert('srp_erp_boq_header', $data);
        $last_id = $this->db->insert_id();

        $projectdetail = $this->db->query("SELECT * FROM `srp_erp_boq_details` where headerID = {$data['clonefromID']}")->result_array();

        if (!empty($projectdetail)) {
            foreach ($projectdetail as $val) {
                $costing_detail = $this->db->query("SELECT * FROM `srp_erp_boq_costing` where detailID = {$val['detailID']}")->result_array();
                $databoq_detail['headerID'] = $last_id;
                $databoq_detail['categoryID'] = $val['categoryID'];
                $databoq_detail['categoryName'] = $val['categoryName'];
                $databoq_detail['subCategoryID'] = $val['subCategoryID'];
                $databoq_detail['subCategoryName'] = $val['subCategoryName'];
                $databoq_detail['itemDescription'] = $val['itemDescription'];
                $databoq_detail['unitID'] = $val['unitID'];
                $databoq_detail['Qty'] = $val['Qty'];
                $databoq_detail['unitRateLocal'] = $val['unitRateLocal'];
                $databoq_detail['unitRateTransactionCurrency'] = $val['unitRateTransactionCurrency'];
                $databoq_detail['totalTransCurrency'] = $val['totalTransCurrency'];
                $databoq_detail['totalLocalCurrency'] = $val['totalLocalCurrency'];
                $databoq_detail['unitRateRptCurrency'] = $val['unitRateRptCurrency'];
                $databoq_detail['totalRptCurrency'] = $val['totalRptCurrency'];
                $databoq_detail['markUp'] = $val['markUp'];
                $databoq_detail['costUnitLocalCurrency'] = $val['costUnitLocalCurrency'];
                $databoq_detail['totalCostLocalCurrency'] = $val['totalCostLocalCurrency'];
                $databoq_detail['costUnitRptCurrency'] = $val['costUnitRptCurrency'];
                $databoq_detail['totalCostRptCurrency'] = $val['totalCostRptCurrency'];
                $databoq_detail['unitCostTranCurrency'] = $val['unitCostTranCurrency'];
                $databoq_detail['totalCostTranCurrency'] = $val['totalCostTranCurrency'];
                $databoq_detail['totalLabourTranCurrency'] = $val['totalLabourTranCurrency'];
                $databoq_detail['totalLabourLocalCurrency'] = $val['totalLabourLocalCurrency'];
                $databoq_detail['totalLabourRptCurrency'] = $val['totalLabourRptCurrency'];
                $databoq_detail['totalCostAmountTranCurrency'] = $val['totalCostAmountTranCurrency'];
                $databoq_detail['totalCostAmountLocalCurrency'] = $val['totalCostAmountLocalCurrency'];
                $databoq_detail['totalCostAmountRptCurrency'] = $val['totalCostAmountRptCurrency'];
                $this->db->insert('srp_erp_boq_details', $databoq_detail);
                $last_id_detail = $this->db->insert_id();
                if (!empty($costing_detail)) {
                    foreach ($costing_detail as $val1) {
                        $data_costing['headerID'] = $last_id;
                        $data_costing['detailID'] = $last_id_detail;
                        $data_costing['categoryID'] = $val1['categoryID'];
                        $data_costing['subCategoryID'] = $val1['subCategoryID'];
                        $data_costing['itemSystemCode'] = $val1['itemSystemCode'];
                        $data_costing['itemCode'] = $val1['itemCode'];
                        $data_costing['itemDescription'] = $val1['itemDescription'];
                        $data_costing['Qty'] = $val1['Qty'];
                        $data_costing['UOMID'] = $val1['UOMID'];
                        $data_costing['UnitShortCode'] = $val1['UnitShortCode'];
                        $data_costing['unitCost'] = $val1['unitCost'];
                        $data_costing['totalCost'] = $val1['totalCost'];
                        $data_costing['costCurrencyCode'] = $val1['costCurrencyCode'];
                        $data_costing['createdUserGroup'] = $val1['createdUserGroup'];
                        $data_costing['createdUserID'] = $val1['createdUserID'];
                        $data_costing['createdDateTime'] = $val1['createdDateTime'];
                        $this->db->insert('srp_erp_boq_costing', $data_costing);
                    }
                }
            }
        }
        $projectplanning = $this->db->query("SELECT * FROM `srp_erp_projectplanning` where headerID =  {$data['clonefromID']} AND masterID = 0")->result_array();

        if (!empty($projectplanning)) {
            foreach ($projectplanning as $val) {
                $projectplanning_masterID = $this->db->query("SELECT * FROM `srp_erp_projectplanning` where masterID =  {$val['projectPlannningID']} AND masterID != 0")->result_array();
                $projectassignees = $this->db->query("SELECT * FROM `srp_erp_projectplanningassignee` where projectPlannningID = {$val['projectPlannningID']}")->result_array();
                $data_proplan['headerID'] = $last_id;
                $data_proplan['masterID'] = $val['masterID'];
                $data_proplan['description'] = $val['description'];
                $data_proplan['note'] = $val['note'];
                $data_proplan['empID'] = $val['empID'];
                $data_proplan['percentage'] = $val['percentage'];
                $data_proplan['startDate'] = $val['startDate'];
                $data_proplan['endDate'] = $val['endDate'];
                $data_proplan['bgColor'] = $val['bgColor'];
                $data_proplan['levelNo'] = $val['levelNo'];
                $data_proplan['sortOrder'] = $val['sortOrder'];
                $data_proplan['companyID'] = $val['companyID'];
                $this->db->insert('srp_erp_projectplanning', $data_proplan);
                $last_id_projectplanning = $this->db->insert_id();

                if (!empty($projectassignees)) {
                    foreach ($projectassignees as $assigneeemp) {
                        $detail_assignees['projectPlannningID'] = $last_id_projectplanning;
                        $detail_assignees['headerID'] = $last_id;
                        $detail_assignees['empID'] = $assigneeemp['empID'];
                        $this->db->insert('srp_erp_projectplanningassignee', $detail_assignees);
                    }

                }


                if (!empty($projectplanning_masterID)) {
                    foreach ($projectplanning_masterID as $val2) {
                        $projectassignees = $this->db->query("SELECT * FROM `srp_erp_projectplanningassignee` where projectPlannningID = {$val2['projectPlannningID']}")->result_array();
                        $data_proplan_masterID['headerID'] = $last_id;
                        $data_proplan_masterID['masterID'] = $last_id_projectplanning;
                        $data_proplan_masterID['description'] = $val2['description'];
                        $data_proplan_masterID['note'] = $val2['note'];
                        $data_proplan_masterID['empID'] = $val2['empID'];
                        $data_proplan_masterID['percentage'] = $val2['percentage'];
                        $data_proplan_masterID['startDate'] = $val2['startDate'];
                        $data_proplan_masterID['endDate'] = $val2['endDate'];
                        $data_proplan_masterID['bgColor'] = $val2['bgColor'];
                        $data_proplan_masterID['levelNo'] = $val2['levelNo'];
                        $data_proplan_masterID['sortOrder'] = $val2['sortOrder'];
                        $data_proplan_masterID['companyID'] = $val2['companyID'];
                        $this->db->insert('srp_erp_projectplanning', $data_proplan_masterID);
                        $last_id_projectplanning_withmasterID = $this->db->insert_id();
                        if (!empty($projectassignees)) {
                            foreach ($projectassignees as $assigneeemp) {
                                $detail_assignees_masterID['projectPlannningID'] = $last_id_projectplanning_withmasterID;
                                $detail_assignees_masterID['headerID'] = $last_id;
                                $detail_assignees_masterID['empID'] = $assigneeemp['empID'];
                                $this->db->insert('srp_erp_projectplanningassignee', $detail_assignees_masterID);
                            }

                        }
                        $projectplanningattachments = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where documentID = 'P-Task' AND documentSystemCode = {$val2['projectPlannningID']}")->result_array();
                        if (!empty($projectplanningattachments)) {
                            foreach ($projectplanningattachments as $attachmentval) {
                                $projectattachment['documentID'] = 'P-Task';
                                $projectattachment['documentSystemCode'] = $last_id_projectplanning_withmasterID;
                                $projectattachment['attachmentDescription'] = $attachmentval['attachmentDescription'];
                                $projectattachment['myFileName'] = $attachmentval['myFileName'];
                                $projectattachment['docExpiryDate'] = $attachmentval['docExpiryDate'];
                                $projectattachment['dateofIssued'] = $attachmentval['dateofIssued'];
                                $projectattachment['fileType'] = $attachmentval['fileType'];
                                $projectattachment['fileSize'] = $attachmentval['fileSize'];
                                $projectattachment['segmentID'] = $attachmentval['segmentID'];
                                $projectattachment['segmentCode'] = $attachmentval['segmentCode'];
                                $projectattachment['companyID'] = current_companyID();
                                $projectattachment['companyCode'] = $this->common_data['company_data']['company_code'];
                                $projectattachment['createdUserGroup'] = $this->common_data['user_group'];
                                $projectattachment['createdPCID'] = $this->common_data['current_pc'];
                                $projectattachment['createdUserID'] = $this->common_data['current_userID'];
                                $projectattachment['createdUserName'] = $this->common_data['current_user'];
                                $projectattachment['createdDateTime'] = $this->common_data['current_date'];
                                $this->db->insert('srp_erp_documentattachments', $projectattachment);
                            }
                        }
                    }


                }


            }
        }


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Save Failed');
        } else {

            $this->db->trans_commit();
            return array('s', 'Successfully Saved', $last_id, $data['projectCode']);
        }

    }

    function update_pm_relationship()
    {
        $this->db->trans_start();
        $relatedtaskID = $this->input->post('relatedprojectID');
        $relationshiptypeID = $this->input->post('relationship');
        $projectPlannningID = $this->input->post('projectplanningID');
        $data['relatedtaskID'] = $projectPlannningID;
        $data['relationshiptypeID'] = $relationshiptypeID;
        $this->db->where('projectPlannningID', $relatedtaskID);
        $this->db->update('srp_erp_projectplanning', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Updated Failed');
        } else {
            $this->db->trans_commit();
            return array('s', 'Relationship Updated Sucessfully');
        }
    }

    function save_timesheetApproval()
    {
        $timesheetdetailID = $this->input->post('timesheetDetailID');
        $timesheetmasterID = $this->input->post('timesheetMasterID');
        $empID = current_userID();
        $companyID = current_companyID();
        $level = $this->input->post('currentlevelno');
        $setupData = getprojectmanagementApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $isManagerAvailableForNxtApproval = 0;
        $nextApprovalEmpID = null;
        $nextLevel = ($level + 1);

        /**** If the number of approval level is less than current approval than only this process will run ****/
        if ($nextLevel <= $approvalLevel) {

            $managers = $this->db->query("SELECT * FROM (
                                             SELECT repManager
                                             FROM srp_employeesdetails AS empTB
                                             LEFT JOIN (
                                                 SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers
                                                 WHERE active = 1 AND empID={$empID} AND companyID={$companyID}
                                             ) AS repoManagerTB ON empTB.EIdNo = repoManagerTB.empID
                                             WHERE Erp_companyID = '{$companyID}' AND EIdNo={$empID}
                                         ) AS empData
                                         LEFT JOIN (
                                              SELECT managerID AS topManager, empID AS topEmpID
                                              FROM srp_erp_employeemanagers WHERE companyID={$companyID} AND active = 1
                                         ) AS topManagerTB ON empData.repManager = topManagerTB.topEmpID")->row_array();

            $approvalSetup = $setupData['approvalSetup'];
            $x = $nextLevel;

            /**** Validate is there a manager available for next approval level ****/
            while ($x <= $approvalLevel) {
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
                if ($approvalType == 3) {
                    //$hrManagerID = (!empty($arr[0])) ? $arr[0]['empID'] : '';
                    $hrManagerID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                    $nextLevel = $x;
                    $nextApprovalEmpID = $hrManagerID;
                    $isManagerAvailableForNxtApproval = 1;
                    $x = $approvalLevel;

                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = $managers[$managerType];
                        $isManagerAvailableForNxtApproval = 1;
                        $x = $approvalLevel;
                    }

                }

                $x++;
            }

        }

        if ($isManagerAvailableForNxtApproval == 1) {
            $upData = [
                'currentLevelNo' => $nextLevel,
                'modifiedPCID' => $this->common_data['current_pc'],
                'modifiedUserID' => $empID,
                'modifiedUserName' => $this->common_data['current_user'],
                'modifiedDateTime' => current_date()
            ];

            $this->db->trans_start();

            $this->db->where('timesheetDetailID', $timesheetdetailID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_pm_mytimesheetdetail', $upData);

            $this->db->trans_complete();

            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                return array('s', 'Level ' . $level . ' is ' . 'Approved successfully');
            } else {
                $this->db->trans_rollback();
                $common_failed = $this->lang->line('common_failed');/* 'failed'*/
                return array('e', $common_failed);
            }

        }else {

            $data = array(
                'currentLevelNo' => $approvalLevel,
                'approvedYN' => 1,
                'approvedDate' => current_date(),
                'approvedbyEmpID' => current_userID(),
                'approvedbyEmpName' => $this->common_data['current_user'],
            );

            $this->db->trans_start();
            $this->db->where('timesheetDetailID', $timesheetdetailID);
            $this->db->where('companyID', $companyID);
            $this->db->update('srp_erp_pm_mytimesheetdetail', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {

                return array('s', 'Timesheet approved successfully');
            } else {
                return array('e', 'Timesheet approved Faild');
            }
        }



    }
    function save_clone_project()
    {
        $projectID = $this->input->post('projectID');
        $projectname = $this->input->post('projectname');
        $companyid = current_companyID();
        $project = $this->db->query("SELECT * FROM `srp_erp_projects` where companyID  = $companyid AND projectID = $projectID ")->row_array();

        $data['projectName'] = $projectname;
        $data['clonefromID'] = $projectID;
        $data['projectType'] = $project['projectType'];
        $data['description'] = $project['description'];
        $data['projectCurrencyID'] = $project['projectCurrencyID'];
        $data['segmentID'] = $project['segmentID'];
        $data['companyID'] = current_companyID();
        $data['projectStartDate'] = $project['projectStartDate'];
        $data['projectEndDate'] = $project['projectEndDate'];
        $data['createdPCID'] =  $this->common_data['current_pc'];
        $data['createdUserID'] =  $this->common_data['current_userID'];
        $data['createdDateTime'] =  $this->common_data['current_date'];
        $data['createdUserName'] =    $this->common_data['current_user'];
        $this->db->insert('srp_erp_projects',$data);
        $project_id  = $this->db->insert_id();
        $catergory = $this->db->query("SELECT categoryID, categoryCode, categoryDescription, sortOrder, GLDescription,GLcode FROM `srp_erp_boq_category`where projectID = 1")->result_array();
        foreach ($catergory as $val)
        {
            $datacat['projectID'] = $project_id;
            $datacat['categoryCode'] =$val['categoryCode'];
            $datacat['categoryDescription'] =$val['categoryDescription'];
            $datacat['sortOrder'] =$val['sortOrder'];
            $datacat['GLDescription'] =$val['GLDescription'];
            $datacat['GLcode'] =$val['GLcode'];
            $datacat['createdPCID'] =  $this->common_data['current_pc'];
            $datacat['createdUserID'] =  $this->common_data['current_userID'];
            $datacat['createdDateTime'] =  $this->common_data['current_date'];
            $datacat['companyID'] = current_companyID();
            $this->db->insert('srp_erp_boq_category',$datacat);
            $cat_id  = $this->db->insert_id();

            $subcatergory = $this->db->query("SELECT categoryID, description, sortOrder, unitID FROM `srp_erp_boq_subcategory` where categoryID = {$val['categoryID']}")->result_array();
            foreach ($subcatergory as $subval)
            {
                $data_sub['categoryID'] = $cat_id;
                $data_sub['description'] =$subval['description'];
                $data_sub['sortOrder'] =$subval['sortOrder'];
                $data_sub['unitID'] =$subval['unitID'];
                $data_sub['createdPCID'] =  $this->common_data['current_pc'];
                $data_sub['createdUserID'] =  $this->common_data['current_userID'];
                $data_sub['createdDateTime'] =  $this->common_data['current_date'];
                $this->db->insert('srp_erp_boq_subcategory',$data_sub);
            }

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e', 'Project Clone Failed' . $this->db->_error_message());
        } else {
            $this->db->trans_commit();
            return array('s', 'Project Clone Successfully.');
        }
    }
    function save_project_transfer()
    {
        $headerID = $this->input->post('headerid');
        $projectPlannningID = $this->input->post('projectplanningID');
        $empID = $this->input->post('filter_employees');
        $transferprojectID = $this->input->post('transferprojectID');
        $transfertaskID = $this->input->post('transfertaskID');
        $transfermasterID = $this->input->post('transfermasterID');
        $employee_join = '';
        if($empID)
        {
            $employee_join = join(",", $empID) ;
        }
        $companyID = current_companyID();
        $related_task = $this->db->query("SELECT srp_erp_projectplanning.projectPlannningID,
                                              description,srp_erp_projectplanningassignee.empID
                                              FROM
	                                         `srp_erp_projectplanning` 
	                                          JOIN srp_erp_projectplanningassignee on srp_erp_projectplanningassignee.projectPlannningID = srp_erp_projectplanning.projectPlannningID
                                              WHERE
	                                          companyID = $companyID 
	                                          AND srp_erp_projectplanning.headerID = $headerID 
                                              AND srp_erp_projectplanning.projectPlannningID = $projectPlannningID or masterID = $projectPlannningID
                                              ANd srp_erp_projectplanningassignee.empID IN ($employee_join)")->result_array();
        if(!empty($related_task))
        {
            foreach ($related_task as $val)
            {

                $data_detail['projectPlannningID'] = $transfertaskID;
                $data_detail['TransferprojectPlannningID'] =$projectPlannningID;
                $data_detail['headerID'] =$transferprojectID;
                $data_detail['empID'] = $val['empID'];
                $data_detail['ProjectTransfermasterID'] = $transfermasterID;
                $this->db->insert('srp_erp_projectplanningassignee', $data_detail);
                $this->db->delete('srp_erp_projectplanningassignee', ['projectPlannningID' => $val['projectPlannningID']]);

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Project Transfer faild.');
        } else {
            return array('s', 'Project transferred successfully');
        }

    }
    function save_eoi_tender()
    {
        $date_format_policy = date_format_policy();
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $eoistatus = $this->input->post('eoistatus');
        $eoisubdate = $this->input->post('eoisubdate');
        $tenderreferenceno = $this->input->post('tenderreferenceno');
        $tendervalue = $this->input->post('tendervalue');
        $tenderstatus = $this->input->post('tenderstatus');
        $tendersubmissiondate = $this->input->post('tendersubmissiondate');

        $typeofcontract = $this->input->post('typeofcontract');
        $commentsstatus = $this->input->post('commentsstatus');
        $descriptionofthecontract = $this->input->post('descriptionofthecontract');
        $specialconditions = $this->input->post('specialconditions');

        $bidsubmissiondate = $this->input->post('bidsubmissiondate');
        $bidduedate = $this->input->post('bidduedate');
        $bidexpirydate = $this->input->post('bidexpirydate');
        $bidvalidityperiod = $this->input->post('bidvalidityperiod');
        $bondvalue = $this->input->post('bondvalue');
        $companytosupplybidbond = $this->input->post('active');
        $consultant = $this->input->post('consultant');
        $budgetestimation = $this->input->post('budgetestimation');
        $bapprovalinternalclient = $this->input->post('bapprovalinternalclient');

        $eoisubdate_formated = input_format_date($eoisubdate, $date_format_policy);
        $tendersubmissiondate_formated = input_format_date($tendersubmissiondate, $date_format_policy);

        $bidsubmissiondate_formated = input_format_date($bidsubmissiondate, $date_format_policy);
        $bidduedate_formated = input_format_date($bidduedate, $date_format_policy);
        $bidexpirydate_formated = input_format_date($bidexpirydate, $date_format_policy);

        $policyDateFrom = input_format_date($this->input->post('policyDateFrom'), $date_format_policy);
        $policyDateTo = input_format_date($this->input->post('policyDateTo'), $date_format_policy);
        $data = array(
            'eoistatus' => $eoistatus,
            'eoisubmissiondate' => $eoisubdate_formated,
            'tenderreferenceno' => $tenderreferenceno,

            'insPolicyDes' => $this->input->post('policyDescription'),
            'insPolicyDateFrom' => $policyDateFrom,
            'insPolicyDateTo' => $policyDateTo,

            'tendervalue' => $tendervalue,
            'tenderstatus' => $tenderstatus,
            'tendersubmissiondate' => $tendersubmissiondate_formated,
            'typeofcontract' => $typeofcontract,
            'commentsstatus' => $commentsstatus,
            'descriptionofthecontract' => $descriptionofthecontract,

            'bidsubmissiondate' => $bidsubmissiondate_formated,
            'bidduedate' => $bidduedate_formated,
            'bidexpirydate' => $bidexpirydate_formated,
            'bidvalidityperiod' => $bidvalidityperiod,
            'bondvalue' => $bondvalue,
            'companytosupplybidbond' => $companytosupplybidbond,

            'totalbudgetestimation' => $budgetestimation,
            'budgetapprovalinternalclient' => $bapprovalinternalclient,

            'consultant' => $consultant,

        ); 

        $this->db->trans_start();
        $this->db->where('headerID', $headerID);
        $this->db->where('companyID', $companyID);
        $this->db->update('srp_erp_boq_header', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            return array('s', 'Project detail updated successfully',$tenderstatus);
        } else {
            return array('e', 'Project detail update Faild');
        }
    }
    function save_boq_approval()
    {

            $this->db->trans_start();
            $this->load->library('Approvals');
            $level = fetch_boq_approvals();
            $system_code = trim($this->input->post('headerID') ?? '');
            $level_id = $level['approvalLevelID'];
            $status = trim($this->input->post('status') ?? '');
            $companyID = current_companyID();
            $code = 'PVE';
            $approvals_status = $this->approvals->approve_document_boq($system_code, $level_id, $status,'', $code);
            if ($approvals_status == 1) {

                $this->session->set_flashdata('s', 'Approval Successfully.');
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return true;
            } else {
                $this->db->trans_commit();
                return true;
            }
    }
    function save_project_team()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $headerID = $this->input->post('hederIDproject');
        $organization = $this->input->post('organization');
        $Employeedrop = $this->input->post('Employee');
        $empname = $this->input->post('empname');
        $organizationrole = $this->input->post('organizationrole');
        $teamID = $this->input->post('teamidproject');
        if ($Employeedrop) {
            $employeename = $this->db->query("select Ename2 from srp_employeesdetails where Erp_companyID = $companyID AND EIdNo = $Employeedrop")->row_array();
        }

        $data['roleID'] = $organizationrole;
        $data['OrganizationID'] = $organization;
        if ($organization == 2) {
            $data['empID'] = $Employeedrop;
            $data['empName'] = $employeename['Ename2'];
        } else {
            $data['empName'] = $empname;
        }
        if ($teamID) {
            $this->db->where('teamID', $teamID);
            $this->db->update('srp_erp_projectteam', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Project Team updated faild.');
            } else {
                return array('s', 'Project Team updated successfully');
            }
        } else
        {
            $data['boqheaderID'] = $headerID;
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];

            $this->db->insert('srp_erp_projectteam', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Project Team Added faild.');
            } else {
                return array('s', 'Project Team Added successfully');
            }
        }



    }
    function save_asset()
    {
        $supplierID = $this->input->post('supplierdrop');
        $supplier = '';
        $headerID = $this->input->post('headerID');
        $detailID = $this->input->post('boq_detailID');
        $equipmenttype = $this->input->post('equipmenttype');
        $assetdrop = $this->input->post('asset_drop');
        $assettext_field = $this->input->post('assettext_field');
        $rentedperiods = $this->input->post('rentedperiods');
        $equpimentcosttype = $this->input->post('equpimentcosttype');
        $perhcost = $this->input->post('perhcost');
        $withoperator = $this->input->post('withoperator');
        $asset_text_id = $this->input->post('asset_text_id');
        $companyID = current_companyID();
        if($asset_text_id && $supplierID =='')
        {
            $supplier = $this->db->query("SELECT supplierID FROM `srp_erp_fa_asset_master` where companyID = $companyID AND faID = $asset_text_id ")->row('supplierID');
        }else{
            $supplier = $supplierID;
        }


        $data['boqheaderID'] =$headerID;
        $data['detailID'] =$detailID;
        $data['type'] =3;
        $data['equipmentType'] =$equipmenttype;
        if($equipmenttype == 1)
        {
            $data['faID'] =$assetdrop;
        }else
        {
            $data['equipmentDescription'] = $assettext_field;
            $data['rentedPeriod'] = $rentedperiods;
            $data['equpimentcostType'] = $equpimentcosttype;
            $data['equpimentcost'] = $perhcost;
            $data['operatorYN'] = $withoperator;
            $data['faID'] = $asset_text_id;
            $data['supplierID'] = $supplier;
        }
        $data['companyID'] = current_companyID();
        $data['createdUserGroup'] = $this->common_data['user_group'];
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdUserName'] = $this->common_data['current_user'];
        $data['createdDateTime'] = $this->common_data['current_date'];
        $this->db->insert('srp_erp_projectactivityplanning', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Asset  Added faild.');
        } else {
            return array('s', 'Asset Added Successfully');
        }
    }
    function update_checklistDetail()
    {
        $checklistID = $this->input->post('checklistID');
        $columns = $this->input->post('columns');
        $columntitle = $this->input->post('columntitle');
        $Width = $this->input->post('Width');
        $fontColor = $this->input->post('fontcolor');
        $bgColor= $this->input->post('bgcolor');
        $sortorder = $this->input->post('sortorder');
        $this->db->where('checklistID', $checklistID);
        $this->db->delete('srp_erp_checklistdetail');

        for ($i = 0; $i < count($columns); $i++) {
            $data[$i]['checklistID'] =$checklistID;
            $data[$i]['columnTypeID'] = $columns[$i];
            $data[$i]['detailDescription'] = $columntitle[$i];
            $data[$i]['sortOrder'] = $sortorder[$i];
            $data[$i]['width'] = $Width[$i];
            $data[$i]['bgColor'] = $bgColor[$i];
            $data[$i]['fontColor'] = $fontColor[$i];
            $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
            $data[$i]['createdPCID'] = $this->common_data['current_pc'];
            $data[$i]['createdUserID'] = $this->common_data['current_userID'];
            $data[$i]['createdUserName'] = $this->common_data['current_user'];
            $data[$i]['createdDateTime'] = $this->common_data['current_date'];

        }
        $this->db->insert_batch('srp_erp_checklistdetail', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', 'Check List Detail  Added faild.');
        } else {
            return array('s', 'Check List Detail Added Successfully');
        }

    }
    function save_boq_checklist_approval()
    {

        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('documentchecklistID') ?? '');
        $level_id = trim($this->input->post('approvalLevelID') ?? '');
        $status = trim($this->input->post('value') ?? '');
        $code = 'PMIP';
        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status,'', $code);
        if ($approvals_status == 1) {
            $this->session->set_flashdata('s', 'Approval Successfully.');
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function save_boq_cost_sheet_initialanalysis()
    {
        $this->db->trans_start();
        $data['categoryID'] = trim($this->input->post('categoryIDinitialanalysis') ?? '');
        $data['subCategoryID'] = trim($this->input->post('subcategoryIDinitialanalysis') ?? '');
        $data['crID'] = trim($this->input->post('crcode') ?? '');
        $data['Qty'] = trim($this->input->post('qty') ?? '');
        $data['UnitShortCode'] = trim($this->input->post('uom') ?? '');
        $data['unitCost'] = trim($this->input->post('unitcost') ?? '');
        $data['totalCost'] = trim($this->input->post('totalcost') ?? '');
        $data['itemAutoID'] = trim($this->input->post('itemautoidprojectinitialanalysis') ?? '');
        $companyID = current_companyID();
        $item = trim($this->input->post('search') ?? '');
        $t = explode(' ', $item);
        $v = array_pop($t);
        $itemcode = str_replace(array('(', ')'), '', $v);
        $data['itemCode'] = $itemcode;
        $data['itemdescription'] = $item;
        if (trim($this->input->post('costingID') ?? '')) {
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = date('Y-m-d H:i:s');
        } else {
            $data['createdUserGroup'] = user_group();
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = date('Y-m-d H:i:s');

            $this->db->insert('srp_erp_boq_costing', $data);
            $last_id_new = $this->db->insert_id();
            $this->update_costing_sheet($data['detailID'],0);
            $this->db->trans_complete();
            $last_id = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return array('e' => 'Saved Failed');
            } else {
                $this->db->trans_commit();
                $itemDetail = $this->db->query("SELECT itemmaster.itemAutoID, itemmaster.itemSystemCode, itemmaster.itemDescription, currentStock 
                                                     FROM srp_erp_itemmaster itemmaster WHERE itemAutoID = {$data['itemAutoID']}")->row_array();
                $data_item['costingID']= $last_id_new;
                $data_item['crID'] = trim($this->input->post('crcode') ?? '');
                $data_item['type']=1;
                $data_item['itemAutoID']=$data['itemAutoID'];
                $data_item['requiredQty']=  $data['Qty'];
                $data_item['currentQty']=$itemDetail['currentStock'];
                $data_item['companyID'] = $this->common_data['company_data']['company_id'];
                $data_item['createdUserGroup'] = $this->common_data['user_group'];
                $data_item['createdPCID'] = $this->common_data['current_pc'];
                $data_item['createdUserID'] = $this->common_data['current_userID'];
                $data_item['createdUserName'] = $this->common_data['current_user'];
                $data_item['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_projectactivityplanning', $data_item);
                $this->db->trans_complete();
                return array('s', 'Saved Successfully', $last_id);
            }
        }

    }
    function deleteboqcost_initialanalysis()
    {
        $crID = $this->input->post('crcode');
        $this->db->delete('srp_erp_boq_costing', array('costingID' => $this->input->post('costingID')));
        $this->db->delete('srp_erp_projectactivityplanning', array('costingID' => $this->input->post('costingID')));
        if ($this->db->affected_rows()) {
            $count = $this->db->query("select count(costingID) as count from srp_erp_boq_costing WHERE crID = $crID")->row('count');
            echo json_encode(array('s', 'This Item ' . $this->input->post('dec') . '  deleted Successfully .',$count));

        } else {
            echo json_encode(array('e', 'Operation could not proceed. Please contact IT team'));

        }
    }
    function save_boq_approval_changereq()
    {

        $this->db->trans_start();
        $this->load->library('Approvals');
        $system_code = trim($this->input->post('requestID') ?? '');
        $level_id =  trim($this->input->post('approvalLevelID') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $companyID = current_companyID();
        $code = 'CR';
        $approvals_status = $this->approvals->approve_document($system_code, $level_id, $status,'', $code);
        if ($approvals_status == 1) {
            $data_up['decision'] = $status;
            $this->session->set_flashdata('s', 'Approval Successfully.');
            $this->db->where('requestID', $system_code);
            $this->db->update('srp_erp_changerequests', $data_up);
            $this->db->trans_start();

            $detail = $this->db->query("SELECT totalLabourcost,category,subCategory,srp_erp_boq_subcategory.unitID,chengereq.crcode,headerID,crID
                                            FROM `srp_erp_changerequests` left join srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory
	                                        LEFT JOIN (select requestID,crcode from srp_erp_changerequests where companyID = $companyID)chengereq on  chengereq.requestID = srp_erp_changerequests.crID
	                                        where srp_erp_changerequests.requestID = $system_code")->row_array();
            $data['unitID'] = $detail['unitID'];
            $data['categoryID'] = $detail['category'];
            $data['subCategoryID'] = $detail['subCategory'];
            $data['itemDescription'] = $detail['crcode'];
            $data['headerID'] = $detail['headerID'];
            $d = $this->db->query("select categoryDescription from srp_erp_boq_category where categoryID={$data['categoryID']}")->row_array();
            $data['categoryName'] = $d['categoryDescription'];
            $s = $this->db->query("select description from srp_erp_boq_subcategory where subCategoryID={$data['subCategoryID']}")->row_array();
            $data['subCategoryName'] = $s['description'];
            $data['totalLabourTranCurrency'] = $detail['totalLabourcost'];
            $this->db->insert('srp_erp_boq_details', $data);
            $last_id = $this->db->insert_id();

            $activityplanning = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` WHERE crID = {$detail['crID']}")->result_array();
            $costing = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where crID = {$detail['crID']}")->result_array();
            foreach ($activityplanning as $val)
            {
                $data_act['boqheaderID'] = $detail['headerID'];
                $data_act['detailID'] =$last_id;
                $this->db->where('activityplanningID', $val['activityplanningID']);
                $this->db->update('srp_erp_projectactivityplanning', $data_act);
            }
            foreach ($costing as $val)
            {
                $data_cost['headerID'] = $detail['headerID'];
                $data_cost['detailID'] =$last_id;
                $this->db->where('costingID', $val['costingID']);
                $this->db->update('srp_erp_boq_costing', $data_cost);
            }
            $this->update_costing_sheet_initial($last_id);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return true;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function update_costing_sheet_initial($detailID)
    {
        $CI =& get_instance();
        $CI->db->select_sum('totalCost');
        $CI->db->from('srp_erp_boq_costing');
        $CI->db->where('detailID', $detailID);
        $d = $CI->db->get()->row_array();

        $data['detailID'] = $detailID;
        $data['Amount'] = $d['totalCost'];

        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_boq_costingsheet');
        $CI->db->where('detailID', $detailID);
        $exist = $CI->db->get()->row_array();

        $details = $this->db->query("select * from srp_erp_boq_details where detailID= $detailID")->row_array();
        $unit['unitRateTransactionCurrency'] = $data['Amount'] * (100 + $details['markUp']) / 100; //formaula for get unit rate
        $unit['totalTransCurrency'] = $unit['unitRateTransactionCurrency'] * $details['Qty'];
        $unit['totalCostTranCurrency'] = $data['Amount'] * $details['Qty'];
        $unit['totalCostAmountTranCurrency'] = $unit['totalCostTranCurrency'] + $details['totalLabourTranCurrency'];

        //hit other details using header exchange rate
        $CI =& get_instance();
        $CI->db->select('rptCurrencyER,localCurrencyER');
        $CI->db->from('srp_erp_boq_details');
        $CI->db->join("srp_erp_boq_header", "srp_erp_boq_details.headerID = srp_erp_boq_header.headerID", "INNER");
        $CI->db->where('detailID', $detailID);
        $ER = $CI->db->get()->row_array();
        $unit['unitRateLocal'] = $unit['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
        $unit['unitRateRptCurrency'] = $unit['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];
        $unit['totalLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
        $unit['totalRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        $unit['costUnitLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
        $unit['costUnitRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        /* $unit['totalLabourLocalCurrency'] = $unit['totalLabourTranCurrency'] / $ER['localCurrencyER'];
         $unit['totalLabourRptCurrency']   = $unit['totalLabourTranCurrency'] / $ER['rptCurrencyER'];*/
        $unit['totalCostAmountLocalCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
        $unit['totalCostAmountRptCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];
        $unit['unitCostTranCurrency'] = $data['Amount'];

        //var_dump($unit);
        $this->db->where('detailID', $detailID);
        $this->db->update('srp_erp_boq_details', $unit);

        if (!empty($exist)) {

            $this->db->where('detailID', $detailID);
            $this->db->update('srp_erp_boq_costingsheet', $data);


        } else {
            $this->db->insert('srp_erp_boq_costingsheet', $data);
        }

        // return true;

        // return true;
    }
    function update_costing_sheet($detailID,$tendertype)
    {
        $CI =& get_instance();
        $CI->db->select_sum('totalCost');
        $CI->db->from('srp_erp_boq_costing');
        $CI->db->where('detailID', $detailID);
        $d = $CI->db->get()->row_array();


        $CI =& get_instance();
        $CI->db->select('*');
        $CI->db->from('srp_erp_boq_costingsheet');
        $CI->db->where('detailID', $detailID);
        $exist = $CI->db->get()->row_array();



        $detailID = $this->input->post('detailID');
        $details = $this->db->query("select * from srp_erp_boq_details where detailID= $detailID")->row_array();


            $CI =& get_instance();
            $CI->db->select('rptCurrencyER,localCurrencyER');
            $CI->db->from('srp_erp_boq_details');
            $CI->db->join("srp_erp_boq_header", "srp_erp_boq_details.headerID = srp_erp_boq_header.headerID", "INNER");
            $CI->db->where('detailID', $detailID);
            $ER = $CI->db->get()->row_array();
        
            if($tendertype == 0)
            {

                $data['detailID'] = $detailID;
                $data['Amount'] = $d['totalCost'];
                if($details['totalLabourTranCurrencyAfterConfirmedYN']>0){
                    $unit['unitRateTransactionCurrency'] = ($data['Amount'] + ($details['totalLabourTranCurrencyAfterConfirmedYN']/$details['Qty']) ) * (100 + $details['markUp']) / 100; //formaula for get unit rate
                }else{
                    $unit['unitRateTransactionCurrency'] = ($data['Amount']) * (100 + $details['markUp']) / 100; //formaula for get unit rate
                }

                $unit['totalTransCurrency'] = $unit['unitRateTransactionCurrency'] * $details['Qty'];
                $unit['totalCostTranCurrency'] = $data['Amount'] * $details['Qty'];
                $unit['totalCostAmountTranCurrency'] = $unit['totalCostTranCurrency'] + $details['totalLabourTranCurrency'];
                //hit other details using header exchange rate
               // echo  '<pre>';print_r($unit);exit;

                $unit['unitRateLocal'] = $unit['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
                $unit['unitRateRptCurrency'] = $unit['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];
        
                $unit['totalLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
                $unit['totalRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        
                $unit['costUnitLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
                $unit['costUnitRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        
                /* $unit['totalLabourLocalCurrency'] = $unit['totalLabourTranCurrency'] / $ER['localCurrencyER'];
                 $unit['totalLabourRptCurrency']   = $unit['totalLabourTranCurrency'] / $ER['rptCurrencyER'];*/
        
                $unit['totalCostAmountLocalCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
                $unit['totalCostAmountRptCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];
        
        
                $unit['unitCostTranCurrency'] = $data['Amount'];
        
    
                $unit['unitRateTransactionCurrencyAfterConfirmedYN'] = $data['Amount'] * (100 + $details['markUp']) / 100; //formaula for get unit rate
                $unit['totalTransCurrencyAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] * $details['Qty'];
                $unit['totalCostTranCurrencyAfterConfirmedYN'] = $data['Amount'] * $details['Qty'];
                $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] + $details['totalLabourTranCurrency'];
                $unit['unitRateLocalAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['unitRateRptCurrencyAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['totalLocalCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['totalRptCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['costUnitLocalCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['costUnitRptCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['totalCostAmountLocalCurrencyAfterConfirmedYN'] = $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['totalCostAmountRptCurrencyAfterConfirmedYN'] = $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['unitCostTranCurrencyAfterConfirmedYN'] = $data['Amount'];
            }else if($tendertype == 1){ 
                $data['detailID'] = $detailID;
                $data['Amount'] = $d['totalCost'];
                $unit['unitRateTransactionCurrencyAfterConfirmedYN'] = $data['Amount'] * (100 + $details['markUp']) / 100; //formaula for get unit rate
                $unit['totalTransCurrencyAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] * $details['Qty'];
                $unit['totalCostTranCurrencyAfterConfirmedYN'] = $data['Amount'] * $details['Qty'];
                $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] + $details['totalLabourTranCurrency'];
                $unit['unitRateLocalAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['unitRateRptCurrencyAfterConfirmedYN'] = $unit['unitRateTransactionCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['totalLocalCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['totalRptCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['costUnitLocalCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['costUnitRptCurrencyAfterConfirmedYN'] = $unit['totalCostTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['totalCostAmountLocalCurrencyAfterConfirmedYN'] = $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] / $ER['localCurrencyER'];
                $unit['totalCostAmountRptCurrencyAfterConfirmedYN'] = $unit['totalCostAmountTranCurrencyAfterConfirmedYN'] / $ER['rptCurrencyER'];
                $unit['unitCostTranCurrencyAfterConfirmedYN'] = $data['Amount'];
            }else { 
                $data['detailID'] = $detailID;
                $data['Amount'] = $d['totalCost'];
                $unit['unitRateTransactionCurrency'] = $data['Amount'] * (100 + $details['markUp']) / 100; //formaula for get unit rate
                $unit['totalTransCurrency'] = $unit['unitRateTransactionCurrency'] * $details['Qty'];
                $unit['totalCostTranCurrency'] = $data['Amount'] * $details['Qty'];
                $unit['totalCostAmountTranCurrency'] = $unit['totalCostTranCurrency'] + $details['totalLabourTranCurrency'];
                //hit other details using header exchange rate
                
                  
                $unit['unitRateLocal'] = $unit['unitRateTransactionCurrency'] / $ER['localCurrencyER'];
                $unit['unitRateRptCurrency'] = $unit['unitRateTransactionCurrency'] / $ER['rptCurrencyER'];
        
                $unit['totalLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
                $unit['totalRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        
                $unit['costUnitLocalCurrency'] = $unit['totalCostTranCurrency'] / $ER['localCurrencyER'];
                $unit['costUnitRptCurrency'] = $unit['totalCostTranCurrency'] / $ER['rptCurrencyER'];
        
                /* $unit['totalLabourLocalCurrency'] = $unit['totalLabourTranCurrency'] / $ER['localCurrencyER'];
                 $unit['totalLabourRptCurrency']   = $unit['totalLabourTranCurrency'] / $ER['rptCurrencyER'];*/
        
                $unit['totalCostAmountLocalCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['localCurrencyER'];
                $unit['totalCostAmountRptCurrency'] = $unit['totalCostAmountTranCurrency'] / $ER['rptCurrencyER'];
        
        
                $unit['unitCostTranCurrency'] = $data['Amount'];
            }
            
            $this->db->where('detailID', $detailID);
            $this->db->update('srp_erp_boq_details', $unit);
        if (!empty($exist)) {
            $this->db->where('detailID', $detailID);
            $this->db->update('srp_erp_boq_costingsheet', $data);
       } else {
            $this->db->insert('srp_erp_boq_costingsheet', $data);
        }
    }
    function save_boq_tem_repdetails()
    {
        //print_r($this->input->post()); die();
        $fieldmulti = $this->input->post('fieldvaluemulti');
        $tempkey = $this->input->post('tempkey');
        $tempmasterID = $this->input->post('tempmasterID');
        $tempElementKey = $this->input->post('tempElementKey');
        $tempElementKeymultiple = $this->input->post('tempElementKeymultiple');

        $itemdescritionmultiple = $this->input->post('itemdescription');

        $date_format_policy = date_format_policy();
        $date = $this->input->post('date');
        $format_date = input_format_date($date, $date_format_policy);
        $last_id_master = '';
        $x = 1;
        $reportname = '';
        $format_date_ir = input_format_date($this->input->post('inspectionrepdate'), $date_format_policy);
        $format_datesoho = input_format_date($this->input->post('sohodate'), $date_format_policy);
        $format_datepscop = input_format_date($this->input->post('completionproject'), $date_format_policy);
        $format_rfidate = input_format_date($this->input->post('rfi_dateInfo'), $date_format_policy);

        $insert_arr = [];
        if ($tempmasterID == '' || $tempmasterID == 0) {
            $data_master['tempkey'] = $tempkey;
            $data_master['projectID'] = $this->input->post('headerID');
            $data_master['companyID'] = current_companyID();
            $data_master['createdUserGroup'] = $this->common_data['user_group'];
            $data_master['createdPCID'] = $this->common_data['current_pc'];
            $data_master['createdUserID'] = $this->common_data['current_userID'];
            $data_master['createdUserName'] = $this->common_data['current_user'];
            $data_master['createdDateTime'] = $this->common_data['current_date'];

            if($tempkey == 'RFI'){
                $serial = $this->db->select_max('serialNo')->where(['tempkey'=> 'RFI'])->get('srp_erp_pm_templateheader')->row('serialNo');
                $serial += 1;
                $rfi_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
                $current_year = date('Y');
                $rfi_code = "TIE/RFI/{$rfi_code}/{$current_year}";
                $data_master['serialNo'] = $serial;
                $data_master['documentCode'] = $rfi_code;
            }
            if($tempkey == 'DQR'){
                $serial = $this->db->select_max('serialNo')->where(['tempkey'=> 'DQR'])->get('srp_erp_pm_templateheader')->row('serialNo');
                $serial += 1;
                $dqr_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
                $current_year = date('Y');
                $dqr_code = "TIE/DQR/{$dqr_code}/{$current_year}";
                $data_master['serialNo'] = $serial;
                $data_master['documentCode'] = $dqr_code;
            }
            if($tempkey == 'IR'){
                $serial = $this->db->select_max('serialNo')->where(['tempkey'=> 'IR'])->get('srp_erp_pm_templateheader')->row('serialNo');
                $serial += 1;
                $dqr_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
                $current_year = date('Y');
                $dqr_code = "TIE/REC/{$dqr_code}/{$current_year}";
                $data_master['serialNo'] = $serial;
                $data_master['documentCode'] = $dqr_code;
            }
            if($tempkey == 'SOHO'){
                $serial = $this->db->select_max('serialNo')->where(['tempkey'=> 'SOHO'])->get('srp_erp_pm_templateheader')->row('serialNo');
                $serial += 1;
                $dqr_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
                $current_year = date('Y');
                $dqr_code = "TIE/SOHO/{$dqr_code}/{$current_year}";
                $data_master['serialNo'] = $serial;
                $data_master['documentCode'] = $dqr_code;
            }
            $this->db->insert('srp_erp_pm_templateheader', $data_master);
            $last_id_master = $this->db->insert_id();
        }
        if ($last_id_master != '') {
            $masterID = $last_id_master;
        } else {
            $masterID = $tempmasterID;
        }

        if ($tempkey == 'DQR') {
            $reportname = 'Daily quality report';
            $insert_arr = array('DQRDA|' . $format_date, 'DQRRN|' . $this->input->post('reportno'), 'DQRCN|' . $this->input->post('contractno'), 'DQRLN|' . $this->input->post('location'),
                'DQRDP|' . $this->input->post('discipline'), 'DQRIB|' . $this->input->post('issuedby'), 'DQRIDR|' . $this->input->post('drawings'), 'DQRIS|' . $this->input->post('inspectionsummary'),
                'DQRMIA|' . $this->input->post('materialinspection'), 'DQRTCA|' . $this->input->post('testconducted'),
                'DQRNCRA|' . $this->input->post('ncrissued'), 'DQRREM|' . $this->input->post('remarks'));
        }
        if ($tempkey == 'IR') {
            $reportname = 'Inspection Request report';
            $insert_arr = array('IRBN|' . $this->input->post('buildingname'), 'IRINS|' . $this->input->post('subject'), 'IRDA|' . $format_date_ir, 'IRTI|' . $this->input->post('inspectionreptime'), 'IRST|' . $this->input->post('appradio'),
                'IRCONCOM|' . $this->input->post('consultantcomments'), 'IRCLICOM|' . $this->input->post('clientcomments'),'IRCOM|'.$this->input->post('comments')
            );
        }
        if ($tempkey == 'PS')
        {
            $reportname = 'Project Summary';
            $insert_arr = array('PSTAA1|' . $this->input->post('totalapprovedamount1'),'PSTAA2|' . $this->input->post('totalapprovedamount2'),
            'PSCIM1|' . $this->input->post('civilmep1'),'PSCIM2|' . $this->input->post('civilmep2'),'PSSAL1|' . $this->input->post('salaries1'),
            'PSSAL2|' . $this->input->post('salaries2'),'PSOTH1|' . $this->input->post('Others1'),'PSOTH2|' . $this->input->post('Others2'),
            'PSHC1|' . $this->input->post('handlingchar1'),'PSHC2|' . $this->input->post('handlingchar2'),'PSTOTCL1|' . $this->input->post('totalclaimed1'),
            'PSTOTCL2|' . $this->input->post('totalclaimed2'),'PSSAV1|' . $this->input->post('savings1'),'PSSAV2|' . $this->input->post('savings2'),
            'PSMP|' . str_replace('<br />', PHP_EOL, $this->input->post('majorprobenc')),'PSRD|' .$this->input->post('recommendation'),'PSPL|' . $this->input->post('projectsummarylocaltion'),
            'PSCOP|'.$format_datepscop,'PSPRONO|'. $this->input->post('projectno')
            );
        }
        if ($tempkey == 'SOHO')
        {
            $reportname = 'Sheet Of Handing Over';
            $insert_arr = array('SOHOSEC|' . $this->input->post('sector'),'SOHONOW|' . $this->input->post('nameofwork'),'SOHODATE|'.$format_datesoho,'SOHOSNAG|'.$this->input->post('snagcomment'));
        }
        if($tempkey == 'RFI'){
            $reportname = 'Request for Information';

            $rfi_civil = (!empty($this->input->post('rfi_civil')))? 1: 0;
            $rfi_architectural = (!empty($this->input->post('rfi_architectural')))? 1: 0;
            $rfi_mechanical = (!empty($this->input->post('rfi_mechanical')))? 1: 0;
            $rfi_electrical = (!empty($this->input->post('rfi_electrical')))? 1: 0;
            $rfi_plumbing = (!empty($this->input->post('rfi_plumbing')))? 1: 0;
            $rfi_others = (!empty($this->input->post('rfi_others')))? 1: 0;

            $insert_arr = [

                "RFIDEC|{$this->input->post('rfi_decipline')}",
                "RFICONS|{$this->input->post('rfi_consultant')}",
                "RFICONT|{$this->input->post('rfi_contractor')}",
                "RFISUB|{$this->input->post('rfi_subject')}",
                "RFIDATINF|{$this->input->post('rfi_dateInfo')}",
                "RFICIV|{$rfi_civil}",
                "RFIARC|{$rfi_architectural}",
                "RFIMEC|{$rfi_mechanical}",
                "RFIELE|{$rfi_electrical}",
                "RFIPLU|{$rfi_plumbing}",
                "RFIOTH|{$rfi_others}",
                "RFIOTHSPC|{$this->input->post('rfi_othersSpecify')}",
                "RFIRES|{$this->input->post('rfi_response')}",
                "RFIDAT|{$format_rfidate}",
                "RFIRBLV|{$this->input->post('receivedbylv')}",
                "RFISBLV|{$this->input->post('signedbylv')}",
            ];
        }
        $this->db->delete('srp_erp_pm_templatedetails', array('companyID'=>current_companyID(),'headerID'=>$masterID));
        if(!empty($insert_arr))
        {
            foreach ($insert_arr as $val)
            {

                $temdetail =  explode('|',$val);
                $fieldname =$temdetail[0];
                $fieldval =$temdetail[1];
                if($fieldval!='')
                {
                    $data_up['tempElementKey'] = $tempElementKey;
                    $data_up['tempElementSubKey'] = $fieldname;
                    $data_up['headerID'] = $masterID;
                    $data_up['fieldValue'] = $fieldval;
                    $data_up['companyID'] = current_companyID();
                    $data_up['createdUserGroup'] = $this->common_data['user_group'];
                    $data_up['createdPCID'] =  $this->common_data['current_pc'];
                    $data_up['createdUserID'] = $this->common_data['current_userID'];
                    $data_up['createdUserName'] = $this->common_data['current_user'];
                    $data_up['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_pm_templatedetails', $data_up);
                }

            }
        }


        if(($tempkey=='DQR'))
        {
            foreach ($fieldmulti as $key => $multtival) {
                if($multtival!='')
                {
                    $sortorder = $this->input->post('sortorder');
                    $chkboxvalue = $this->input->post('fieldvaluemulticheck_'.$sortorder[$key]);
                    $radiobtnvalue = $this->input->post('fieldvaluemultiradio_'.$sortorder[$key]);
                    $data['tempElementKey'] = $tempElementKeymultiple;
                    $data['fieldValue'] = $multtival.'|+'.$radiobtnvalue.'|+'.$chkboxvalue;
                    $data['sortOrder'] = $x;
                    $data['headerID'] = $masterID;
                    $data['companyID'] = current_companyID();
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] =  $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_pm_templatedetails', $data);
                    $x++;
                }

            }
        }

        if(($tempkey=='SOHO'))
        {
            foreach ($itemdescritionmultiple as $key => $multtival) {
                if($multtival!='')
                {
                    $sortorder = $this->input->post('sortorder');
                    $completed = $this->input->post('completed_'.$sortorder[$key]);
                    $remarks = $this->input->post('remarks_'.$sortorder[$key]);
                    $data['tempElementKey'] = $tempElementKeymultiple;
                    $data['fieldValue'] = $multtival.'|+'.$completed.'|+'.$remarks;
                    $data['sortOrder'] = $x;
                    $data['headerID'] = $masterID;
                    $data['companyID'] = current_companyID();
                    $data['createdUserGroup'] = $this->common_data['user_group'];
                    $data['createdPCID'] =  $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_pm_templatedetails', $data);
                    $x++;
                }

            }
        }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e',$reportname.' updated failed');
            $this->db->trans_rollback();
         } else {
            return array('s',$reportname.' updated successfully',$masterID);
            $this->db->trans_commit();
        }
       





    }
    function boqcostsheedposttender($masterrec)
    {
        $this->db->trans_start();
        $data['headerID'] = $masterrec['headerID'];
        $data['categoryID'] = $masterrec['categoryID'];
        $data['subCategoryID'] = $masterrec['subCategoryID'];
        $data['Qty'] = $masterrec['Qty'];
        $data['UnitShortCode'] = $masterrec['UnitShortCode'];
        $data['unitCost'] = $masterrec['unitCost'];
        $data['totalCost'] = $masterrec['totalCost'];
        $data['costCurrencyCode'] = $masterrec['costCurrencyCode'];
        $data['detailID'] = $masterrec['detailID'];
        $data['itemAutoID'] =$masterrec['itemAutoID'];
        $data['tenderType'] = 1;
        $companyID = current_companyID();
        $data['itemCode'] = $masterrec['itemCode'];
        $data['itemdescription'] = $masterrec['itemdescription'];
        $data['createdUserGroup'] = user_group();
        $data['createdPCID'] = $this->common_data['current_pc'];
        $data['createdUserID'] = $this->common_data['current_userID'];
        $data['createdDateTime'] = date('Y-m-d H:i:s');
        $this->db->insert('srp_erp_boq_costing', $data);
        $last_id_new = $this->db->insert_id();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        
        } else {
            $this->db->trans_complete();
            $itemDetail = $this->db->query("SELECT itemmaster.itemAutoID, itemmaster.itemSystemCode, itemmaster.itemDescription, currentStock 
            FROM srp_erp_itemmaster itemmaster WHERE itemAutoID = {$data['itemAutoID']}")->row_array();
            $data_item['boqheaderID']= $data['headerID'];
            $data_item['costingID']= $last_id_new;
            $data_item['detailID']= $data['detailID'] ;
            $data_item['type']=1;
            $data_item['itemAutoID']=$data['itemAutoID'];
            $data_item['requiredQty']=  $data['Qty'];
            $data_item['currentQty']=$itemDetail['currentStock'];
            $data_item['companyID'] = $this->common_data['company_data']['company_id'];
            $data_item['createdUserGroup'] = $this->common_data['user_group'];
            $data_item['createdPCID'] = $this->common_data['current_pc'];
            $data_item['createdUserID'] = $this->common_data['current_userID'];
            $data_item['createdUserName'] = $this->common_data['current_user'];
            $data_item['createdDateTime'] = $this->common_data['current_date'];
            $data_item['tenderType'] = 1;
            $this->db->insert('srp_erp_projectactivityplanning', $data_item);
        }
    }
}
