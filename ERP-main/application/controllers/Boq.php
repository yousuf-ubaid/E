<?php use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class Boq extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        global $globalHeaderID;
        $this->load->model('Boq_model');
        $this->load->helper('boq');
        $this->load->library('s3');
    }

    function fetch_Boq_headertable()
    {        
        $convertFormat = convert_date_format_sql();
        $archive_status = $this->input->post('archive_status');
        $segmentID = $this->input->post('segmentID');
        $crTypes = $this->input->post('crTypes');
        $companyID = $this->common_data['company_data']['company_id'];
        $Where = ' boq.companyID = ' . $companyID;

        if ($archive_status == 1) {
            $Where .= ' AND ArchivingYN = 1';
        } else {
            $Where .= ' AND (ArchivingYN IS NULL OR ArchivingYN = 0)';
        }


        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $Where .=  "  AND (projectCode Like '%$sSearch%') OR (projectName LIKE '%$sSearch%') OR (projectDescription LIKE '%$sSearch%') OR (srp_erp_customermaster.customerSystemCode LIKE  '%$sSearch%') OR (srp_erp_customermaster.customerName LIKE  '%$sSearch%')";
        }

        $this->datatables->select("headerID, boq.companyID as companyID,companyName, customerCode,boq.customerName,  comment, 	boq.projectID as projectID, projectCode, 
                projectNumber,boq.createdDateTime as createdDateTime ,projectDateFrom, 
                projectDateTo,approvedYN,confirmedYN as confirmedYNBOQ,confirmedYN,approvedYN as approvedYNBOQ, ArchivingYN,
                projectDescription,pr.projectName, sg.description AS segDes,srp_erp_customermaster.customerSystemCode,srp_erp_customermaster.customerName as customerNamefiler")
            ->from('srp_erp_boq_header AS boq')
            ->join('srp_erp_projects AS pr', 'pr.projectID = boq.projectID')
            ->join('srp_erp_segment AS sg', 'sg.segmentID = boq.segementID')
            ->join('srp_erp_customermaster', 'srp_erp_customermaster.customerAutoID = boq.customerCode','left')
            ->edit_column('confirmedYN', '$1', 'confirm(confirmedYN)')
            ->where($Where)
            ->add_column('approvedYN', '$1', 'confirm_ap_user(approvedYN, confirmedYN, "P", headerID)')
            ->edit_column('action', '$1', 'loadboqheaderaction(headerID,confirmedYNBOQ,approvedYNBOQ, ArchivingYN)')
            ->edit_column('createdDateTime', '<span >$1 </span>', 'convert_date_format(createdDateTime)')
            ->edit_column('projectDateTo', '<span >$1 </span>', 'convert_date_format(projectDateTo)')
            ->edit_column('projectDateFrom', '<span >$1 </span>', 'convert_date_format(projectDateFrom)');

            if($crTypes){
                $crTypes = explode(',', $crTypes);
                $this->datatables->where_in('boq.customerType', $crTypes);
            }

            if($segmentID){
                $segmentID = explode(',', $segmentID);
                $this->datatables->where_in('boq.segementID', $segmentID);
            }        
        echo $this->datatables->generate();
    }

    function fetch_Boq_categoryTable()
    {
        $this->datatables->select('categoryID,projectName as project,concat(categoryCode," | ",categoryDescription) as category ,categoryCode,categoryDescription,sortOrder, GLDescription as GLcode')
            ->from('srp_erp_boq_category')
            ->join('srp_erp_projects', 'srp_erp_boq_category.projectID=srp_erp_projects.projectID')
            ->where('srp_erp_boq_category.companyID', $this->common_data['company_data']['company_id'])
            ->edit_column('action', '$1', 'load_boq_category_action(categoryID,categoryDescription)');
        echo $this->datatables->generate();
    }

    function save_boq_category()
    {
        $this->form_validation->set_rules('CatCode', 'Category Code', 'trim|required');
        $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
        $this->form_validation->set_rules('CatDescrip', 'Description', 'trim|required');
        $this->form_validation->set_rules('GLcode', 'Revenue GL Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->save_boq_category());

        }
    }

    function getCategorySortID()
    {
        echo json_encode($this->Boq_model->getCategorySortID());

    }

    function getSubcategorySortID()
    {
        echo json_encode($this->Boq_model->getSubcategorySortID());

    }

    function load_sub_category_table()
    {


        $this->datatables->select('subCategoryID,description,sortOrder,unitID as UnitShortCode ')
            ->from('srp_erp_boq_subcategory')
            ->where('categoryID', $this->input->post('MainCatID'))
            ->edit_column('action', '$1', 'load_boq_sub_category_action(subCategoryID)');
        echo $this->datatables->generate();
    }

    function save_boq_subcategory()
    {


        $this->form_validation->set_rules('SubCatDes', 'Description', 'trim|required');


        $this->db->select('categoryCode');
        $this->db->from('srp_erp_boq_category');
        $this->db->where('categoryCode', $this->input->post('MainCatID'));
        $catexist = $this->db->get()->row_array();


        if ($catexist) {
            $this->session->set_flashdata($msgtype = 'e', 'Entered Sub Category is already exist');
            echo json_encode(FALSE);
        } else {


            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata($msgtype = 'e', validation_errors());
                echo json_encode(FALSE);
            } else {
                echo json_encode($this->Boq_model->save_boq_subcategory());
            }
        }
    }

    function getReportingCurrency()
    {
        echo json_encode($this->Boq_model->getReportingCurrency());

    }

    function save_boq_header()
    {
        $date_format_policy = date_format_policy();
        $retentionpercentage = $this->input->post('retentionpercentage');
        $projectstartDate = input_format_date($this->input->post('prjStartDate'), $date_format_policy);
        $projectendDate = input_format_date($this->input->post('prjEndDate'), $date_format_policy);
      
      
    
        
        if ($this->input->post('headerID') == '') {
            $this->form_validation->set_rules('projectID', 'Project', 'trim|required');
            $this->form_validation->set_rules('segement', 'Segement', 'trim|required');
            $this->form_validation->set_rules('currency', 'Currency conversion', 'trim|required');
            $this->form_validation->set_rules('customer', 'Customer name', 'trim|required');

        }
        $this->form_validation->set_rules('documentdate', 'Document start date', 'trim|required');
        $this->form_validation->set_rules('customertype', 'Customer Type', 'trim|required');

        $this->form_validation->set_rules('prjStartDate', 'Project start date', 'trim|required');
        $this->form_validation->set_rules('prjEndDate', 'Project end date', 'trim|required');
        $this->form_validation->set_rules('projectname', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('retentionpercentage', 'Retention Percentage', 'trim|required');
        $this->form_validation->set_rules('advancepercentage', 'Advance Percentage', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
            exit();

        } else {
            if ($retentionpercentage < 0) {
                // if ($retentionpercentage <= 0) {
                echo json_encode(array('e', 'Retention Percentage should be greater than or equal to 0'));
                exit();
            } 
            if($projectendDate <  $projectstartDate)
            { 
                echo json_encode(array('e', 'Project end date cannot be less than project start date'));
                exit();
            }
            
            
            
            else {
                echo json_encode($this->Boq_model->save_boq_header());
            }

        }

    }

    function getSubcategoryDropDown()
    {
        echo json_encode($this->Boq_model->getSubcategoryDropDown());
    }

    function save_boq_header_details()
    {
        $this->form_validation->set_rules('category', 'Category', 'trim|required');
        $this->form_validation->set_rules('subcategory', 'Sub Category', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('unitID', 'Unit', 'trim|required');
        $this->form_validation->set_rules('unitshortcode', 'Unit', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->save_boq_header_details());
        }

    }

    function loadcostheaderdetailstable()
    {
        $policy = getPolicyValues('PCR', 'P');
        if ($policy == 0) {
            echo $this->Boq_model->detailTableOrderByselling();
        } else {

            echo $this->Boq_model->detailTableOrderBycost();
        }


    }


    function fetchItems()
    {
        echo json_encode($this->Boq_model->fetchItems());
    }

    function save_boq_cost_sheet()
    {
        $this->form_validation->set_rules('search', 'Category Code', 'trim|required');
        $this->form_validation->set_rules('uom', 'Item ', 'trim|required');
        $this->form_validation->set_rules('qty', 'Revenue GL Code', 'trim|required');
        $this->form_validation->set_rules('unitcost', 'Revenue GL Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->save_boq_cost_sheet());
        }
    }

    function loadboqcosttable()
    { 
        $tendertype = $this->input->post('tendertype');
        $where_tendertype = '';
        if($tendertype!=1)
        { 
            $where_tendertype .= ' AND (tenderType = 0 OR tenderType IS NULL)'; 
        }

        /*      $this->db->select("srp_erp_boq_header.customerCurrencyID as customerCurrencyID ,costingID,detailID,UOMID,UnitShortCode,Qty,unitCost,totalCost,costCurrencyCode,itemCode,itemDescription,CONCAT(itemCode," - ",itemDescription) as item");
              $this->db->from('srp_erp_boq_costing');
              $this->db->join('srp_erp_boq_header', 'srp_erp_boq_header.headerID=srp_erp_boq_costing.headerID', 'left');
              $this->db->where('srp_erp_boq_costing.detailID', $this->input->post('detailID'));
              $details = $this->db->get()->result_array();*/
        $detailsID = $this->input->post('detailID');
        $details = $this->db->query("SELECT srp_erp_boq_header.customerCurrencyID AS customerCurrencyID, costingID, detailID, UOMID, UnitShortCode, Qty, unitCost, totalCost, costCurrencyCode, itemCode, itemDescription, CONCAT(itemCode, ' - ', itemDescription) AS item,pretenderConfirmedYN, tenderType, confirmedYN FROM srp_erp_boq_costing INNER JOIN srp_erp_boq_header on srp_erp_boq_header.headerID=srp_erp_boq_costing.headerID WHERE srp_erp_boq_costing.detailID=$detailsID  $where_tendertype")->result_array();

        $table = '<table id="loadcosttable" class="' . table_class() . '"><thead><tr><th>Item</th><th>UOM</th><th>Qty</th><th >Unit Cost</th><th>Total Cost</th><th></th></tr></thead><tbody>';
        $totalValue = 0;
        if ($details) {
            $customerCurrencyID = 0;
            foreach ($details as $value) {
                $totalValue += $value['totalCost'];
                $customerCurrencyID = $value['customerCurrencyID'];
                $table .= '<tr>';
                $table .= '<td>' . $value["itemDescription"] . '</td>';
                $table .= '<td>' . $value["UnitShortCode"] . '</td>';
                $table .= '<td><div style="text-align: right">' . $value["Qty"] . '</td><div></td>';
                $table .= '<td><div style="text-align: right">' . number_format((float)$value["unitCost"], 2, '.',
                        '') . '</div></td>';
                $table .= '<td><div style="text-align: right">' . number_format((float)$value["totalCost"], 2, '.',
                        '') . '</div></td>';

                if($value['confirmedYN']!=1 || $value['tenderType'] == 1)
                {
                    $table .= '<td><span class="pull-right"><a onclick="deleteBoqCost(' . $value['costingID'] . ',' . $value['detailID'] . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a></td>';
                }               
                $status = '';
                $table .= '</tr>';
            }
        }
        $table .= '</tbody>';
        $table .= '<tfoot><tr>';
        $table .= '<td colspan="4" style="text-align: right">Grand Total</td>';
        $table .= '<td><div style="text-align: right">' . number_format((float)$totalValue, 2, '.', '') . '</div></td>';
        $table .= '<td></td>';
        $table .= '</tr></tfoot>';
        $table .= '</table>';
        echo $table;
    }


    function saveboqdetailscalculation()
    {
        echo json_encode($this->Boq_model->saveboqdetailscalculation());
    }

    function getallsavedvalues()
    {
        echo json_encode($this->Boq_model->getallsavedvalues());
    }


    function loadsummaryTable()
    {
        $policy = getPolicyValues('PCR', 'P');

        if ($policy == 0) {
            echo $this->Boq_model->summaryTableOrderByselling();
        } else {
            echo $this->Boq_model->summaryTableOrderBycost();
        }


    }

    function confirm_boq()
    {
        echo json_encode($this->Boq_model->confirm_boq());
    }

    function item_search()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $com_currency = $this->common_data['company_data']['company_default_currencyID'];
        $com_currencyDPlace = $this->common_data['company_data']['company_default_decimal'];
        $search_string = "%" . $this->input->post('q') . "%";
        $currency = $this->input->post('currency');


        if ($this->input->post('q') == '') {
            $q = "SELECT itemAutoID,itemSystemCode, itemDescription, defaultUnitOfMeasure, companyLocalCurrency, companyLocalWacAmount,companyLocalCurrency as  subCurrencyCode,
	companyLocalCurrency as  masterCurrencyCode,
	companyLocalWacAmount as cost FROM srp_erp_itemmaster WHERE  companyID = $companyID AND isActive = 1 Limit 10";

        } else {
            $q = "SELECT itemAutoID,itemSystemCode, itemDescription, defaultUnitOfMeasure, companyLocalCurrency, companyLocalWacAmount,companyLocalCurrency as  subCurrencyCode,
	companyLocalCurrency as  masterCurrencyCode,
	companyLocalWacAmount as cost FROM srp_erp_itemmaster WHERE companyID = $companyID AND isActive = 1 AND (itemSystemCode LIKE '{$search_string}' OR itemDescription LIKE '{$search_string}' OR seconeryItemCode LIKE '{$search_string}') 	AND companyID = $companyID  Limit 10";
        }

        $data = $this->db->query($q)->result_array();

        echo json_encode($data);


    }

    function deleteBoqHeader()
    {
        echo $this->Boq_model->deleteBoqHeader();
    }

    function deleteboqdetail()
    {
        echo $this->Boq_model->deleteboqdetail();
    }

    function deleteboqcost()
    {
        echo $this->Boq_model->deleteboqcost();
    }

    function save_project()
    {
        if ($this->input->post('projectID') == NULL) {
            $this->form_validation->set_rules('projectName', 'Project Name', 'trim|required');
            $this->form_validation->set_rules('projectCurrencyID', 'Currency', 'trim|required');
        }

        $this->form_validation->set_rules('segementID', 'Segement ', 'trim|required');
        $this->form_validation->set_rules('projectStartDate', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('projectEndDate', 'End Date', 'trim|required');


        if ($this->form_validation->run() == FALSE) {

            echo json_encode($msgtype = 'e', validation_errors());
        } else {
            echo $this->Boq_model->save_project();
        }

    }

    function fetch_Boq_projectTable()
    {
        $convertFormat = convert_date_format_sql();
        $this->datatables->select('projectID, projectName as projectName ,projectType as projectType ,srp_erp_projects.description as description ,projectCurrencyID, srp_erp_projects.segmentID,DATE_FORMAT(projectStartDate,"' . $convertFormat . '") AS projectStartDate , DATE_FORMAT(projectEndDate,"' . $convertFormat . '") AS projectEndDate,  CONCAT(segmentCode," | ", srp_erp_segment.description) AS segment, CurrencyCode as CurrencyCode')
            ->from('srp_erp_projects')
            ->join('srp_erp_segment', 'srp_erp_projects.segmentID = srp_erp_segment.segmentID')
            ->join('srp_erp_currencymaster', 'srp_erp_currencymaster.currencyID = srp_erp_projects.projectCurrencyID')
            ->where('srp_erp_projects.companyID', $this->common_data['company_data']['company_id'])
            ->edit_column('action', '$1', 'loadprojectAction(projectID)');
        echo $this->datatables->generate();
    }


    function delete_project()
    {
        echo $this->Boq_model->delete_project();
    }

    function get_project_data()
    {
        echo json_encode($this->Boq_model->get_project_data());
    }

    function loadCategory()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $projectID = $this->input->post('projectID');
        $category = $this->db->query("SELECT categoryID,concat(categoryCode,' | ',categoryDescription) as cat FROM `srp_erp_boq_category` WHERE companyID={$companyID} AND projectID={$projectID}")->result_array();

        $c_arr = array('' => 'Please Select');
        if (!empty($category)) {
            foreach ($category as $row) {
                $c_arr[trim($row['categoryID'] ?? '')] = trim($row['cat'] ?? '');

            }
        }

        echo $html = form_dropdown('category', $c_arr, '',
            'onchange="getSubcategory()" class="form-control searchbox" id="categoryID" required');


    }

    function delete_category()
    {
        echo $this->Boq_model->delete_category();
    }

    function deletesubcategory()
    {
        echo $this->Boq_model->deletesubcategory();
    }

    function get_project_pdf()
    {


        $headerID = $this->input->post('headerID');
        if (isset($headerID)) {
            $globalHeaderID = $headerID;
        }


        $sumtotalTransCurrency = 0;
        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        $table = '<table id="summarytable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th>S.No</th><th >Items</th><th>UOM</th><th>Qty</th><th> Rate</th><th> Amount</th></tr>';
        $table .= '<tr>';

        $table .= '</tr>';


        $table .= '</thead>';
        $table .= '<tbody>';


        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $globalHeaderID);
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


                                $table .= '</tr>';

                            }
                        }


                    }
                    $table .= '<tr bgcolor="#d6e9c6" style="background-color: #d6e9c6"><td></td>';
                    $table .= '<td><strong>Sub Total to Summary</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$amount, 2, '.',
                            ',') . '</strong></td>';


                    $table .= '</tr>';

                }


            }
        }

        $table .= '<tr>';
        $table .= '<td style="text-align:right " colspan="5"><strong>Total</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency, 2, '.', ',');
        '</strong></td>';


        $table .= '</tr>';
        $table .= '</tbody></table>';


        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $globalHeaderID);
        $this->db->group_by("categoryID");
        $this->db->order_by("sortOrder", "ASC");
        $details = $this->db->get()->result_array();
        $data['details'] = $details;
        $convertFormat = convert_date_format_sql();

        $master = $this->db->query('SELECT srp_erp_boq_header.comment,srp_erp_projects.description,confirmedYN,approvedYN,customerCurrencyID,customerName,companyName,projectCode,DATE_FORMAT(projectDateFrom,"' . $convertFormat . '") AS projectDateFrom ,DATE_FORMAT(projectDateTo,"' . $convertFormat . '") AS projectDateTo FROM `srp_erp_boq_header` LEFT JOIN srp_erp_projects on srp_erp_boq_header.projectID=srp_erp_projects.projectID WHERE headerID =
    ' . $globalHeaderID . ' ')->row_array();
        $data['master'] = $master;
        $html = $this->load->view('system/pm/project_summary_pdf', $data, TRUE, $master['approvedYN']);
        $this->load->library('pdf');

        $pdf = $this->pdf->printed($html, 'A4');


    }

    function fetch_project_approval()
    {
        $convertFormat = convert_date_format_sql();


        $this->datatables->select("comment,headerID,
srp_erp_boq_header.projectID,
projectCode,
projectNumber,
projectName,

segementID,
customerID,
customerCode,
customerName as customerName, DATE_FORMAT(projectDateFrom,'$convertFormat') AS projectDateFrom,DATE_FORMAT(projectDateTo,'$convertFormat') AS projectDateTo,DATE_FORMAT(projectDocumentDate,'$convertFormat') AS projectDocumentDate,  srp_erp_documentapproved.approvedYN as approvedYN, documentApprovedID, approvalLevelID,srp_erp_boq_header.confirmedYN,srp_erp_boq_header.approvedYN",
            FALSE);
        $this->datatables->from('srp_erp_boq_header');
        $this->datatables->join('srp_erp_projects', 'srp_erp_boq_header.projectID = `srp_erp_projects` .projectID ',
            'left');
        $this->datatables->join('srp_erp_documentapproved',
            'srp_erp_documentapproved.documentSystemCode = srp_erp_boq_header.headerID AND srp_erp_documentapproved.approvalLevelID = `srp_erp_boq_header.currentLevelNo');
        $this->datatables->join('srp_erp_approvalusers',
            'srp_erp_approvalusers.levelNo = srp_erp_boq_header.currentLevelNo');
        $this->datatables->where('srp_erp_approvalusers.documentID', 'P');
        $this->datatables->where('srp_erp_approvalusers.employeeID', $this->common_data['current_userID']);
        $this->datatables->where('srp_erp_documentapproved.documentID', 'P');
        $this->datatables->where('srp_erp_documentapproved.approvedYN', trim($this->input->post('approvedYN') ?? ''));
        $this->datatables->where('srp_erp_boq_header.companyID', current_companyID());
        $this->datatables->where('srp_erp_approvalusers.companyID', current_companyID());
        $this->datatables->add_column('projectCode', '$1',
            'approval_change_modal(projectCode,headerID,documentApprovedID,approvalLevelID,approvedYN,"P")');
        $this->datatables->add_column('confirmed', "<center>Level $1</center>", 'approvalLevelID');
        $this->datatables->add_column('approved', '$1', 'document_approval_drilldown(approvedYN, "P", headerID)');
        $this->datatables->add_column('edit', '$1',
            'bank_transfer_approval(headerID,approvalLevelID,approvedYN,documentApprovedID)');
        echo $this->datatables->generate();

    }

    function project_summary()
    {

        $globalHeaderID = $this->input->post('headerID');


        $sumtotalTransCurrency = 0;
        $sumtotalCostTranCurrency = 0;
        $sumtotalLabourTranCurrency = 0;
        $sumtotalCostAmountTranCurrency = 0;
        $table = '<table id="summarytable" class="' . table_class() . 'custometbl"><thead>';
        $table .= '<tr><th>S.No</th><th >Items</th><th  >Unit</th><th>Qty</th><th> Rate</th><th> Amount</th></tr>';
        $table .= '<tr>';

        $table .= '</tr>';


        $table .= '</thead>';
        $table .= '<tbody>';


        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $globalHeaderID);
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


                                $table .= '</tr>';

                            }
                        }


                    }
                    $table .= '<tr bgcolor="#d6e9c6" style="background-color: #d6e9c6"><td></td>';
                    $table .= '<td><strong>Sub Total to Summary</strong></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td></td>';
                    $table .= '<td style="text-align: right"><strong>' . number_format((float)$amount, 2, '.',
                            ',') . '</strong></td>';


                    $table .= '</tr>';

                }


            }
        }

        $table .= '<tr>';
        $table .= '<td style="text-align:right " colspan="5"><strong>Total</strong></td>';
        $table .= '<td style="text-align: right"><strong>' . number_format((float)$sumtotalTransCurrency, 2, '.', ',');
        '</strong></td>';


        $table .= '</tr>';
        $table .= '</tbody></table>';


        $this->db->select('srp_erp_boq_details.categoryID,headerID,srp_erp_boq_details.categoryName,sortOrder');
        $this->db->from('srp_erp_boq_details');
        $this->db->join('srp_erp_boq_category', 'srp_erp_boq_category.categoryID = srp_erp_boq_details.categoryID');
        $this->db->where('headerID', $globalHeaderID);
        $this->db->group_by("categoryID");
        $this->db->order_by("sortOrder", "ASC");
        $details = $this->db->get()->result_array();
        $data['details'] = $details;
        $convertFormat = convert_date_format_sql();

        $master = $this->db->query('SELECT srp_erp_boq_header.comment,srp_erp_projects.description,confirmedYN,approvedYN,customerCurrencyID,customerName,companyName,projectCode,DATE_FORMAT(projectDateFrom,"' . $convertFormat . '") AS projectDateFrom ,DATE_FORMAT(projectDateTo,"' . $convertFormat . '") AS projectDateTo FROM `srp_erp_boq_header` LEFT JOIN srp_erp_projects on srp_erp_boq_header.projectID=srp_erp_projects.projectID WHERE headerID =
    ' . $globalHeaderID . ' ')->row_array();
        $data['master'] = $master;
        $html = $this->load->view('system/pm/project_summary_pdf', $data, TRUE, $master['approvedYN']);
        echo $html;
    }

    function insert_project_approval()
    {
        $this->form_validation->set_rules('headerID', 'headerID', 'trim|required');
        $this->form_validation->set_rules('Level', 'Level', 'trim|required');
        if ($this->input->post('status') == 2) {
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
        }
        $this->form_validation->set_rules('status', 'Status', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->confirm_project_approval());
        }
    }

    function fetch_Boq_projectPlanning()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = $this->common_data['company_data']['company_id'];
        $this->datatables->select('headerID, companyID,companyName, customerCode, customerName,  comment, projectID, projectCode, projectNumber,DATE_FORMAT(createdDateTime,"' . $convertFormat . '") AS createdDateTime , DATE_FORMAT(projectDateFrom,"' . $convertFormat . '") AS projectDateFrom, DATE_FORMAT(projectDateTo,"' . $convertFormat . '") AS projectDateTo,approvedYN,confirmedYN')
            ->from('srp_erp_boq_header')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->edit_column('action', '$1', 'loadHeaderBoqlanning(headerID)');
        echo $this->datatables->generate();
    }

    function save_boq_projectPlanning()
    {
        $relatedprojectID = $this->input->post('relatedprojectID');
        $relationship = $this->input->post('relationship');
        $projectPlannningID = $this->input->post('projectPlannningID');

        $this->form_validation->set_rules('projectphase', 'Phase', 'trim|required');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('project_category', 'Project Category', 'trim|required');
        $this->form_validation->set_rules('assignedEmployee[]', 'Assign Employee', 'trim|required');
        $this->form_validation->set_rules('startDate', 'startDate', 'trim|required');
        $this->form_validation->set_rules('endDate', 'endDate', 'trim|required');
        if ($relatedprojectID != '' && $relationship == '') {
            $this->form_validation->set_rules('relationship', 'Relationship', 'trim|required');
            echo json_encode(array('e', 'Relationship field is required'));
            die();
        }
        if ($relatedprojectID == '' && $relationship != '') {
            $this->form_validation->set_rules('relatedprojectID', 'Depended Task', 'trim|required');
            echo json_encode(array('e', 'Depended Task field is required'));
            die();
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
            die();
        } else {
            if ($projectPlannningID == $relatedprojectID) {
                echo json_encode(array('e', ' Cannot add a relationship for the same task'));
                die();
            } else {
                echo json_encode($this->Boq_model->save_boq_projectPlanning());
            }


        }
    }

    function project_planningSortOrder()
    {
        $headerID = $this->input->post('headerID');
        $companyID = $this->common_data['company_data']['company_id'];
        $data = $this->db->query("SELECT IF( isnull(MAX(sortOrder)), 1 , ( MAX(sortOrder) + 1) ) as sortOrder FROM srp_erp_projectplanning WHERE headerID=$headerID AND masterID=0 AND companyID=$companyID")->row_array();
        echo json_encode($data);
    }

    function loadTaskData()
    {
        $convertFormat = convert_date_format_sql();
        $headerID = $this->input->post('headerID');
        $companyID = $this->common_data['company_data']['company_id'];
        $phases = $this->db->query("SELECT COUNT(timelineID) as count  FROM `srp_erp_projecttimeline` where  companyID = $companyID AND headerID = $headerID")->row('count');
        $data['header'] = $this->db->query("SELECT	srp_erp_projectplanning.headerID,DATEDIFF(DATE(plannedcompletionDate),DATE(endDate)) as difference,DATE_FORMAT( timeline.plannedcompletionDate, '$convertFormat' ) AS plannedcompletionDate,srp_erp_projectplanning.projectPlannningID, srp_erp_projectplanning.headerID, masterID, description, note, percentage, DATE_FORMAT(startDate, '$convertFormat' ) AS startDate, DATE_FORMAT(endDate, '$convertFormat' ) AS endDate, bgColor, levelNo, sortOrder, GROUP_CONCAT(Ename2 SEPARATOR ' , ') AS ename2,phaseDescription,srp_erp_projectplanning.timelineID,
                                            IF( DATEDIFF( DATE( plannedcompletionDate ), DATE( enddatetimelineactual ))< 0, 1, 0 ) type 
                                                FROM srp_erp_projectplanning LEFT JOIN ( SELECT Ename2,srp_erp_projectplanningassignee.headerID, projectPlannningID FROM srp_erp_projectplanningassignee 
                                                LEFT JOIN `srp_employeesdetails` ON empID = EIdNo WHERE headerID = $headerID order by empID asc ) t ON t.projectPlannningID = srp_erp_projectplanning.projectPlannningID AND t.headerID = srp_erp_projectplanning.headerID 
                                                LEFT JOIN srp_erp_projecttimeline timeline on timeline.timelineID = srp_erp_projectplanning.timelineID
                                                LEFT JOIN (SELECT timeline.timelineID,enddatetimelineactual FROM srp_erp_projecttimeline timeline LEFT JOIN (
                                                SELECT MAX( DATE( endDate )) AS enddatetimelineactual, srp_erp_projectplanning.timelineID FROM
		                                        srp_erp_projectplanning GROUP BY srp_erp_projectplanning.projectPlannningID, masterID , timelineID ORDER BY
		                                        enddate DESC  LIMIT $phases ) projectplanning  on projectplanning.timelineID = timeline.timelineID WHERE timeline.headerID = $headerID 
		                                        AND timeline.companyID = $companyID 
                                                ) maxdatetimeline on maxdatetimeline.timelineID = timeline.timelineID
                                                
                                                
                                                
                                                WHERE srp_erp_projectplanning.headerID = $headerID AND srp_erp_projectplanning.companyID=$companyID GROUP BY srp_erp_projectplanning.projectPlannningID, masterID ORDER BY sortOrder ASC")->result_array();
        $data['master'] = $this->db->query("SELECT confirmedYN FROM `srp_erp_boq_header` where headerID = $headerID")->row_array();

        $data['sortOrder'] = $this->db->query("select sortOrder from srp_erp_projectplanning  WHERE headerID = $headerID AND companyID=$companyID AND masterID=0 ")->result_array();
        $html = $this->load->view('system/pm/ajax-load-planning-data', $data, TRUE);
        echo $html;

    }

    function project_subplanningSortOrder()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $headerID = $this->input->post('headerID');
        $data = $this->db->query("SELECT IF( isnull(MAX(sortOrder)), 1 , ( MAX(sortOrder) + 1) ) as sortOrder FROM srp_erp_projectplanning WHERE masterID=$headerID AND companyID=$companyID ")->row_array();
        echo json_encode($data);
    }

    function getallchart()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $headerID = $this->input->post('headerID');
        $data = $this->db->query("SELECT srp_erp_projectplanning.projectPlannningID,CONCAT(relatedtaskID,'',relationshipTpye) as relationship,relatedtaskID,srp_erp_projectplanning.headerID, masterID, description, note, percentage, startDate,  endDate, bgColor, levelNo, sortOrder, GROUP_CONCAT(Ename2 SEPARATOR ' , ') AS ename2 FROM srp_erp_projectplanning LEFT JOIN ( SELECT Ename2,srp_erp_projectplanningassignee.headerID, projectPlannningID FROM srp_erp_projectplanningassignee LEFT JOIN `srp_employeesdetails` ON empID = EIdNo WHERE headerID = $headerID order by empID asc ) t ON t.projectPlannningID = srp_erp_projectplanning.projectPlannningID AND t.headerID = srp_erp_projectplanning.headerID 	LEFT JOIN srp_erp_pmrelationship on srp_erp_pmrelationship.relationshipID = srp_erp_projectplanning.relationshiptypeID WHERE srp_erp_projectplanning.headerID = $headerID AND companyID=$companyID GROUP BY srp_erp_projectplanning.projectPlannningID, masterID ORDER BY sortOrder ASC ")->result_array();
        echo json_encode($data);
    }


    function update_project_planning()
    {
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        $companyID = current_companyID();
        $projectplanning = $this->db->query("SELECT $name as colval,srp_erp_boq_header.headerID,srp_erp_boq_header.projectCode FROM `srp_erp_projectplanning` LEFT JOIN srp_erp_boq_header on srp_erp_boq_header.headerID = srp_erp_projectplanning.headerID where projectPlannningID = $pk")->row_array();
        if ($value != $projectplanning['colval']) {
            if ($name == 'bgColor') {
                $colornew = '';
                $colorold = '';
                if ($value == 'ggroupblack') {
                    $colornew = 'Black';
                } else if ($value == 'gtaskblue') {
                    $colornew = 'Blue';
                } else if ($value == 'gtaskred') {
                    $colornew = 'Red';
                } else if ($value == 'gtaskpurple') {
                    $colornew = 'Purple';
                } else if ($value == 'gtaskgreen') {
                    $colornew = 'Green';
                } else {
                    $colornew = 'Pink';
                }
                if ($projectplanning['colval'] == 'ggroupblack') {
                    $colorold = 'Black';
                } else if ($projectplanning['colval'] == 'gtaskblue') {
                    $colorold = 'Blue';
                } else if ($projectplanning['colval'] == 'gtaskred') {
                    $colorold = 'Red';
                } else if ($projectplanning['colval'] == 'gtaskpurple') {
                    $colorold = 'Purple';
                } else if ($projectplanning['colval'] == 'gtaskgreen') {
                    $colorold = 'Green';
                } else {
                    $colorold = 'Pink';
                }

                $colname = audit_log_colname($name, 'srp_erp_projectplanning');
                $auditlog['old_val'] = $colorold;
                $auditlog['display_old_val'] = $colorold;
                $auditlog['new_val'] = $colornew;
                $auditlog['display_new_val'] = $colornew;
                $auditlog['tableName'] = $colname['tbl_name'];
                $auditlog['columnName'] = $colname['col_name'];
                $auditlog['rowID'] = $projectplanning['headerID'];
                $auditlog['companyID'] = $companyID;
                $auditlog['userID'] = current_userID();
                $auditlog['timestamp'] = current_date();
                $auditlog['DocumentName'] = $projectplanning['projectCode'];
                $auditlog['DocumentType'] = 'PM';
                $this->db->insert('srp_erp_audit_log', $auditlog);
            } else {
                $colname = audit_log_colname($name, 'srp_erp_projectplanning');
                $auditlog['old_val'] = $projectplanning['colval'];
                $auditlog['display_old_val'] = $projectplanning['colval'];
                $auditlog['new_val'] = $value;
                $auditlog['display_new_val'] = $value;
                $auditlog['tableName'] = $colname['tbl_name'];
                $auditlog['columnName'] = $colname['col_name'];
                $auditlog['rowID'] = $projectplanning['headerID'];
                $auditlog['companyID'] = $companyID;
                $auditlog['userID'] = current_userID();
                $auditlog['timestamp'] = current_date();
                $auditlog['DocumentName'] = $projectplanning['projectCode'];
                $auditlog['DocumentType'] = 'PM';
                $this->db->insert('srp_erp_audit_log', $auditlog);
            }


            $this->db->trans_complete();
        }
        $this->db->update('srp_erp_projectplanning', array($name => $value), array('projectPlannningID' => $pk));

        return TRUE;

    }

    function deleteplanning()
    {
        echo $this->Boq_model->deleteplanning();
    }

    function change_projectplanningSortOrder()
    {
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $id = $this->input->post('id');
        $masterID = $this->input->post('masterID');
        $companyID = $this->common_data['company_data']['company_id'];
        if ($type == 'm') {
            $main = $this->db->query("select sortOrder,projectPlannningID from srp_erp_projectplanning where projectPlannningID=$id")->row_array();
            $second = $this->db->query("select sortOrder,projectPlannningID from srp_erp_projectplanning where sortOrder=$value AND masterID=0 AND companyID=$companyID")->row_array();

            $data[0]['sortOrder'] = $second['sortOrder'];
            $data[0]['projectPlannningID'] = $id;
            $data[1]['sortOrder'] = $main['sortOrder'];
            $data[1]['projectPlannningID'] = $second['projectPlannningID'];

            $this->db->update_batch('srp_erp_projectplanning', $data, 'projectPlannningID');

        }

        if ($type == 's') {
            $main = $this->db->query("select sortOrder,projectPlannningID,description from srp_erp_projectplanning where projectPlannningID=$masterID")->row_array();
            $second = $this->db->query("select sortOrder,projectPlannningID,description from srp_erp_projectplanning where sortOrder=$value AND masterID=$id AND companyID=$companyID")->row_array();

            $data[0]['sortOrder'] = $second['sortOrder'];
            $data[0]['projectPlannningID'] = $masterID;
            $data[1]['sortOrder'] = $main['sortOrder'];
            $data[1]['projectPlannningID'] = $second['projectPlannningID'];
            $this->db->update_batch('srp_erp_projectplanning', $data, 'projectPlannningID');
        }
        echo json_encode(array('s', 'Successfully Updated'));
    }

    function get_project()
    {
        $convertFormat = convert_date_format_sql();

        $projectID = $this->input->post('projectID');
        $data = $this->db->query("select projectCurrencyID,segmentID, DATE_FORMAT(projectStartDate,'{$convertFormat}') as projectStartDate, DATE_FORMAT(projectEndDate,'{$convertFormat}') as projectEndDate   from srp_erp_projects WHERE projectID={$projectID}")->row_array();
        echo json_encode($data);
    }

    function clone_project()
    {
        $this->form_validation->set_rules('headerID', 'Project ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->clone_boq_projectPlanning());
        }
    }

    function archive_projet()
    {
        $headerID = $this->input->post('headerID');
        $data['ArchivingYN'] = 1;
        $data['ArchivingBY'] = current_userID();
        $data['ArchivingDate'] = current_date();
        $this->db->where('headerID', $headerID);
        $this->db->update('srp_erp_boq_header', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Archive Project'));
        }
    }

    function referback_project()
    {
        $headerID = $this->input->post('headerID');

        $this->db->select('approvedYN,projectCode');
        $this->db->where('headerID', trim($headerID));
        $this->db->where('approvedYN', 1);
        $this->db->where('confirmedYN', 1);
        $this->db->from('srp_erp_boq_header');
        $approved_project_header = $this->db->get()->row_array();
        if (!empty($approved_project_header)) {
            echo json_encode(array('e', 'The document already approved - ' . $approved_project_header['projectCode']));
        } else {
            $this->load->library('Approvals');
            $status = $this->approvals->approve_delete($headerID, 'P');
            if ($status == 1) {
                echo json_encode(array('s', ' Referred Back Successfully.', $status));
            } else {
                echo json_encode(array('e', ' Error in refer back.', $status));
            }
        }


    }

    function load_gantchart()
    {
        $data['headerID'] = $this->input->post('headerID');
        $this->load->view('system/pm/load_ganttchartpm', $data);


    }

    function get_project_relatedtask()
    {
        $projectID = $this->input->post('projectPlannningID');
        $companyID = current_companyID();
        $relatedproject = $this->db->query("SELECT `projectPlannningID`, 
                                                        `description` 
                                                         FROM 
                                                         `srp_erp_projectplanning` 
                                                         WHERE `companyID` = '{$companyID}' 
                                                         AND `headerID` = '{$projectID}'")->result_array();
        $data_arr = array('' => 'Select Project');

        if (!empty($relatedproject)) {
            foreach ($relatedproject as $row) {
                $data_arr[trim($row['projectPlannningID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        echo form_dropdown('relatedprojectID', $data_arr, '', 'class="form-control select2" id="relatedprojectID"');


    }

    function get_project_relatedtask_edit()
    {
        $projectID = $this->input->post('projectPlannningID');
        $companyID = current_companyID();
        $relatedproject = $this->db->query("SELECT `projectPlannningID`, 
                                                        `description` 
                                                         FROM 
                                                         `srp_erp_projectplanning` 
                                                         WHERE 
                                                         `companyID` = '{$companyID}' 
                                                         AND `headerID` = '{$projectID}'")->result_array();
        $data_arr = array('' => 'Select Project');

        if (!empty($relatedproject)) {
            foreach ($relatedproject as $row) {
                $data_arr[trim($row['projectPlannningID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        echo form_dropdown('relatedprojectID', $data_arr, '', 'class="form-control select2" id="relatedprojectID_edit"');


    }

    function update_projectrelationship()
    {
        $relatedprojectID = $this->input->post('relatedprojectID');
        $relationship = $this->input->post('relationship');
        $projectPlannningID = $this->input->post('projectplanningID');

        $this->form_validation->set_rules('relatedprojectID', 'Related Task', 'trim|required');
        $this->form_validation->set_rules('relationship', 'Relationship', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($projectPlannningID == $relatedprojectID) {
                echo json_encode(array('e', ' Cannot add a relationship for the same task'));
            } else {
                echo json_encode($this->Boq_model->update_pm_relationship());
            }
        }
    }

    function generate_mytimesheet()
    {
        $this->db->trans_start();
        $companyID = current_companyID();
        $startdate = $this->input->post('datefrommytask');
        $enddate = $this->input->post('datetomytask');
        $userID = $this->input->post('user');
        $date_format_policy = date_format_policy();
        $startdateformatted = input_format_date($startdate, '%Y-%m-%d');
        $todateformatted = input_format_date($enddate, '%Y-%m-%d');

        $this->form_validation->set_rules('datefrommytask', 'Date From', 'trim|required');
        $this->form_validation->set_rules('datetomytask', 'Date To', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        } else {
            $detail = $this->db->query("SELECT srp_erp_boq_header.projectCode, 
                                                    srp_erp_boq_header.customerName, 
                                                    srp_erp_projects.projectName, 
                                                    srp_erp_projectplanning.description,	
                                                    srp_erp_projects.description as projectdescription,
                                                    startDate,
	                                                endDate,
	                                                srp_erp_projectplanning.projectPlannningID,
	                                                srp_erp_customermaster.customerName as pmcustomername,
	                                                srp_erp_boq_header.projectID
                                                    FROM 
                                                    `srp_erp_projectplanningassignee`
	                                                LEFT JOIN srp_erp_boq_header ON srp_erp_boq_header.headerID = srp_erp_projectplanningassignee.headerID
	                                                LEFT JOIN srp_erp_projects ON srp_erp_projects.projectID = srp_erp_boq_header.projectID
	                                                LEFT JOIN srp_erp_projectplanning on srp_erp_projectplanning.projectPlannningID = srp_erp_projectplanningassignee.projectPlannningID
	                                                LEFT JOIN srp_erp_customermaster on srp_erp_customermaster.customerAutoID = srp_erp_boq_header.customerCode
                                                    WHERE
	                                                srp_erp_projectplanningassignee.empID = $userID
	                                                AND Date( startDate ) >= '{$startdateformatted}'
	                                                AND Date( endDate) <= '{$todateformatted}'")->result_array();

            $isExist = $this->db->query("SELECT timesheetMasterID ,
                                              DATE('{$startdateformatted}') as dfrom,
                                              DATE('{$todateformatted}') as dto ,
                                              fromDate,
                                              toDate
                                              FROM
	                                          `srp_erp_pm_mytimesheet` 
                                              WHERE
	                                          companyID = $companyID 
	                                          AND EmpID = $userID 
	                                          having dfrom between fromDate and toDate and dto between fromDate and toDate")->result_array();
            $this->load->library('sequence');
            if (empty($isExist)) {

                $data_master['documentCode'] = $this->sequence->sequence_generator('TS');
                $data_master['EmpID'] = $userID;
                $data_master['fromDate'] = $startdateformatted;
                $data_master['toDate'] = $todateformatted;
                $data_master['companyID'] = $companyID;
                $data_master['createdUserGroup'] = $this->common_data['user_group'];
                $data_master['createdPCID'] = $this->common_data['current_pc'];
                $data_master['createdUserID'] = $this->common_data['current_userID'];
                $data_master['createdUserName'] = $this->common_data['current_user'];
                $data_master['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_pm_mytimesheet', $data_master);
                $last_id = $this->db->insert_id();
                if (!empty($detail)) {
                    foreach ($detail as $val) {
                        $data_detail['timesheetMasterID'] = $last_id;
                        $data_detail['empID'] = $userID;
                        $data_detail['projectID'] = $val['projectID'];
                        $data_detail['taskID'] = $val['projectPlannningID'];
                        $data_detail['companyID'] = $companyID;
                        $data_detail['createdUserGroup'] = $this->common_data['user_group'];
                        $data_detail['createdPCID'] = $this->common_data['current_pc'];
                        $data_detail['createdUserID'] = $this->common_data['current_userID'];
                        $data_detail['createdUserName'] = $this->common_data['current_user'];
                        $data_detail['createdDateTime'] = $this->common_data['current_date'];
                        $this->db->insert('srp_erp_pm_mytimesheetdetail', $data_detail);

                    }
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo json_encode(array('e', 'Save Failed'));
                } else {
                    $this->db->trans_commit();
                    echo json_encode(array('s', 'Successfully Saved'));

                }
            } else {
                echo json_encode(array('e', 'Timesheet already generated to selected date range ' . '(' . $startdateformatted . ' - ' . $todateformatted . ')' . ''));
            }
        }


    }

    function load_timesheet_mytimesheet()
    {

        $companyID = current_companyID();
        $startdate = $this->input->post('datefrommytask');
        $enddate = $this->input->post('datetomytask');
        $userID = $this->input->post('user');
        $date_format_policy = date_format_policy();
        $startdateformatted = input_format_date($startdate, '%Y-%m-%d');
        $todateformatted = input_format_date($enddate, '%Y-%m-%d');
        $daterange = array();
        $dateproject = array();
        $start = $startdateformatted;
        $end = $todateformatted;
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            array_push($daterange, $date->format('D jS M Y'));
            array_push($dateproject, $date->format('Y-m-d'));
        }

        $data['timesheetmaster'] = $this->db->query("SELECT DocumentCode,
                                                              timesheetMasterID,
                                                              fromDate,
                                                              toDate,
                                                              timesheetMasterID
                                                              FROM
	                                                          `srp_erp_pm_mytimesheet`
	                                                          where 
                                                              EmpID = $userID")->result_array();

        $data['daterange'] = $daterange;
        $data['dateproject'] = $dateproject;
        $this->load->view('system/pm/load_mytimesheet', $data);
    }

    function save_pm_approval_levels()
    {
        $this->form_validation->set_rules('level', 'Level', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            die(json_encode(['e', validation_errors()]));
        }

        $level = $this->input->post('level');

        $this->db->where('companyID', current_companyID());
        $this->db->where('documentID', 'PM-T');
        $this->db->update('srp_erp_documentcodemaster', ['approvalLevel' => $level]);

        echo json_encode(['s', 'Level updated successfully']);
    }

    function setup_timesheetApproval()
    {
        $companyID = current_companyID();
        $createdPCID = current_pc();
        $createdUserID = current_userID();
        $createdUserGroup = current_user_group();
        $createdUserName = current_employee();
        $createdDateTime = current_date();

        $appLevel = $this->input->post('appLevel');
        $appType = $this->input->post('appType');


        $dataArr = [];
        foreach ($appLevel as $key => $row) {
            $thisAppType = $appType[$key];
            $thisEmp = $this->input->post('empID_' . $row);


            if (empty($thisAppType)) {
                die(
                json_encode(['e', 'Please select a approval type for level ' . $row])
                );
            }

            if ($thisAppType == 3 && empty($thisEmp)) {
                /*** If approval type is HR manager than employee can not be blank ***/
                die(
                json_encode(['e', 'Please select a employee for for level ' . $row])
                );
            }


            $dataArr[$key]['approvalLevel'] = $row;
            $dataArr[$key]['approvalType'] = $thisAppType;
            $dataArr[$key]['companyID'] = $companyID;
            $dataArr[$key]['createdPCID'] = $createdPCID;
            $dataArr[$key]['createdUserGroup'] = $createdUserGroup;
            $dataArr[$key]['createdUserID'] = $createdUserID;
            $dataArr[$key]['createdUserName'] = $createdUserName;
            $dataArr[$key]['createdDateTime'] = $createdDateTime;
        }

        $this->db->trans_start();
        $this->db->where('companyID', $companyID)->delete('srp_erp_pm_timesheetapprovalsetup');
        $this->db->where('companyID', $companyID)->delete('srp_erp_pm_timesheetapprovalsetuphremployees');

        foreach ($dataArr as $rowD) {
            $this->db->insert('srp_erp_pm_timesheetapprovalsetup', $rowD);

            if ($rowD['approvalType'] == 3) {
                $id = $this->db->insert_id();
                $level = $rowD['approvalLevel'];
                $empArr = $this->input->post('empID_' . $level);
                $dataHr = [];
                foreach ($empArr as $key => $empID) {
                    $dataHr[$key]['approvalSetupID'] = $id;
                    $dataHr[$key]['empID'] = $empID;
                    $dataHr[$key]['companyID'] = $companyID;
                    $dataHr[$key]['createdPCID'] = $createdPCID;
                    $dataHr[$key]['createdUserGroup'] = $createdUserGroup;
                    $dataHr[$key]['createdUserID'] = $createdUserID;
                    $dataHr[$key]['createdUserName'] = $createdUserName;
                    $dataHr[$key]['createdDateTime'] = $createdDateTime;
                }

                $this->db->insert_batch('srp_erp_pm_timesheetapprovalsetuphremployees', $dataHr);

            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(['e', 'Error in Timesheet approval setup']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['s', 'Timesheet Approval setup successfully updated']);
        }
    }

    function load_timesheet_mytimesheet_submit()
    {
        $timesheetMasterID = $this->input->post('timesheetID');
        $userID = current_userID();
        $timesheetMaster = $this->db->query("SELECT DATE(fromDate) as fromdate, 
                                                  DATE(toDate) as toDate 
                                                  FROM `srp_erp_pm_mytimesheet`
                                                  where timesheetMasterID = $timesheetMasterID")->row_array();

        $daterange = array();
        $dateproject = array();
        $start = $timesheetMaster['fromdate'];
        $end = $timesheetMaster['toDate'];
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            array_push($daterange, $date->format('D jS M Y'));
            array_push($dateproject, $date->format('Y-m-d'));
        }
        $data['daterange'] = $daterange;
        $data['dateproject'] = $dateproject;
        $data['detail'] = $this->db->query("SELECT
                                                projectmaster.projectCode,
                                                projects.description as projectdescription,
                                                customermaster.customerName,
                                                projectplanning.startDate,
                                                projectplanning.endDate,
                                                projectplanning.projectPlannningID,
                                                projects.projectName as projectName,
                                                projectplanning.description,
                                                mytimesheetdetail.confirmedYN,
                                                mytimesheetdetail.approvedYN,
                                                mytimesheetmaster.timesheetMasterID,
                                                mytimesheetdetail.timesheetDetailID
                                                from 
                                                srp_erp_pm_mytimesheetdetail mytimesheetdetail
                                                LEFT JOIN srp_erp_pm_mytimesheet mytimesheetmaster on mytimesheetmaster.timesheetMasterID = mytimesheetdetail.timesheetMasterID
                                                LEFT JOIN srp_erp_projectplanning projectplanning on projectplanning.projectPlannningID = mytimesheetdetail.taskID
                                                LEFT JOIN srp_erp_boq_header projectmaster on projectmaster.headerID = projectplanning.headerID
                                                LEFT JOIN srp_erp_projects projects on projects.projectID = projectmaster.projectID
                                                LEFT JOIN srp_erp_customermaster customermaster on customermaster.customerAutoID = projectmaster.customerCode
                                                where 
                                                mytimesheetdetail.EmpID = $userID 
                                                AND mytimesheetdetail.timesheetMasterID = $timesheetMasterID")->result_array();

        $this->load->view('system/pm/load_mytimesheet_submitview', $data);
    }

    function mytimesheet_submit()
    {
        $this->db->trans_start();
        $mytimesheetMasterID = $this->input->post('timesheetmasterID');
        $submittedID = $this->input->post('issubmit');
        $this->form_validation->set_rules('issubmit[]', 'Submit', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            foreach ($submittedID as $val) {
                $data['confirmedYN'] = 1;
                $data['confirmedByEmpID'] = $this->common_data['current_userID'];
                $data['confirmedByName'] = $this->common_data['current_user'];
                $data['confirmedDate'] = $this->common_data['current_date'];
                $this->db->where('timesheetDetailID', $val)->update('srp_erp_pm_mytimesheetdetail', $data);

                $this->mytimesheet_ApprovalCreate($val, $level = 1, current_userID());
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Approval Created failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Approval Created Successfully'));
            }

        }
    }

    function mytimesheet_ApprovalCreate($timesheetdetailID, $level, $employee)
    {
        $companyID = current_companyID();
        $current_userID = current_userID();
        $setupData = getprojectmanagementApprovalSetup();
        $approvalEmp_arr = $setupData['approvalEmp'];
        $approvalLevel = $setupData['approvalLevel'];
        $isManagerAvailableForNxtApproval = 0;
        $nextLevel = null;
        $nextApprovalEmpID = null;
        $data_app = [];
        $empID = $employee;


        if ($level <= $approvalLevel) {
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
            $x = $level;

            $i = 0;

            while ($x <= $approvalLevel) {

                $isCurrentLevelApproval_exist = 0;
                $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $x);
                $arr = array_map(function ($k) use ($approvalSetup) {
                    return $approvalSetup[$k];
                }, $keys);

                $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';

                if ($approvalType == 3) {
                    $isCurrentLevelApproval_exist = 1;

                    if ($isManagerAvailableForNxtApproval == 0) {
                        $nextLevel = $x;
                        $nextApprovalEmpID = (array_key_exists($x, $approvalEmp_arr)) ? $approvalEmp_arr[$x] : '';
                        $isManagerAvailableForNxtApproval = 1;
                    }
                } else {
                    $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                    if (!empty($managers[$managerType])) {
                        $isCurrentLevelApproval_exist = 1;

                        if ($isManagerAvailableForNxtApproval == 0) {
                            $nextLevel = $x;
                            $nextApprovalEmpID = $managers[$managerType];
                            $isManagerAvailableForNxtApproval = 1;
                        }
                    }

                }


                $x++;
            }
        }

        $upData = [
            'currentLevelNo' => $nextLevel,
            'modifiedPCID' => $this->common_data['current_pc'],
            'modifiedUserID' => $current_userID,
            'modifiedUserName' => $this->common_data['current_user'],
            'modifiedDateTime' => current_date()
        ];
        $this->db->where('timesheetDetailID', $timesheetdetailID);
        $update = $this->db->update('srp_erp_pm_mytimesheetdetail', $upData);

        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
        }


    }

    function load_timesheet_mytimesheet_approval()
    {
        $setupData = getprojectmanagementApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalSetup = $setupData['approvalSetup'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $empID = current_userID();
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $x = 0;
        $str = 'CASE';
        while ($x < $approvalLevel) {
            $level = $x + 1;
            $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
            $arr = array_map(function ($k) use ($approvalSetup) {
                return $approvalSetup[$k];
            }, $keys);
            $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
            if ($approvalType == 3) {
                $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                $hrManagerID = array_column($hrManagerID, 'empID');
                if (!empty($hrManagerID)) {

                    $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ';
                    foreach ($hrManagerID as $key => $hrManagerRow) {
                        $str .= ($key > 0) ? ' OR' : '';
                        $str .= ' ( \'' . $empID . '\' = ' . $hrManagerRow . ')';
                    }
                    $str .= ' , 1, 0 ) ';
                }
            } else {
                $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ' . $managerType . ' = ' . $empID . ', 1, 0 ) ';
            }
            $x++;
        }
        $str .= 'END AS isInApproval';
        $data['approvaldata'] = $this->db->query("SELECT timesheetMasterID,documentCode, DATE_FORMAT(fromDate,'{$convertFormat}') AS fromDate, DATE_FORMAT(toDate,'{$convertFormat}') AS toDate FROM (SELECT	*,  {$str} FROM 
                                                      (SELECT timeMaster.*,`repManager`,mytimesheetmaster.documentCode,mytimesheetmaster.fromDate,mytimesheetmaster.toDate FROM srp_erp_pm_mytimesheetdetail AS timeMaster
			                                          LEFT JOIN srp_erp_pm_mytimesheet mytimesheetmaster ON mytimesheetmaster.timesheetMasterID = timeMaster.timesheetMasterID
			                                          JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = timeMaster.empID
			                                          LEFT JOIN ( SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers WHERE active = 1 AND companyID = $companyID ) AS repoManagerTB ON timeMaster.empID = repoManagerTB.empID 
		                                              WHERE timeMaster.companyID = $companyID AND timeMaster.confirmedYN = 1 AND timeMaster.approvedYN = '0') AS timesheetapprovaldata
		                                              LEFT JOIN ( SELECT managerID AS topManager, empID AS topEmpID FROM srp_erp_employeemanagers WHERE companyID = 1 AND active = 1 ) AS topManagerTB ON timesheetapprovaldata.repManager = topManagerTB.topEmpID) AS t1 
                                                      WHERE `t1`.`isInApproval` = 1 GROUP BY timesheetMasterID")->result_array();
        $this->load->view('system/pm/load_mytimesheet_approvalview', $data);
    }

    function load_timesheet_mytimesheet_approval_viewall()
    {
        $timesheetmasterID = $this->input->post('timesheetMasterID');
        $setupData = getprojectmanagementApprovalSetup();
        $approvalLevel = $setupData['approvalLevel'];
        $approvalSetup = $setupData['approvalSetup'];
        $approvalEmp_arr = $setupData['approvalEmp'];
        $empID = current_userID();
        $companyID = $this->common_data['company_data']['company_id'];
        $convertFormat = convert_date_format_sql();
        $x = 0;
        $str = 'CASE';
        while ($x < $approvalLevel) {
            $level = $x + 1;
            $keys = array_keys(array_column($approvalSetup, 'approvalLevel'), $level);
            $arr = array_map(function ($k) use ($approvalSetup) {
                return $approvalSetup[$k];
            }, $keys);
            $approvalType = (!empty($arr[0])) ? $arr[0]['approvalType'] : '';
            if ($approvalType == 3) {
                $hrManagerID = (array_key_exists($level, $approvalEmp_arr)) ? $approvalEmp_arr[$level] : [];
                $hrManagerID = array_column($hrManagerID, 'empID');
                if (!empty($hrManagerID)) {
                    $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ';
                    foreach ($hrManagerID as $key => $hrManagerRow) {
                        $str .= ($key > 0) ? ' OR' : '';
                        $str .= ' ( \'' . $empID . '\' = ' . $hrManagerRow . ')';
                    }
                    $str .= ' , 1, 0 ) ';
                }
            } else {
                $managerType = (!empty($arr[0])) ? $arr[0]['desCode'] : '';
                $str .= ' WHEN( currentLevelNo = ' . $level . ' ) THEN IF( ' . $managerType . ' = ' . $empID . ', 1, 0 ) ';
            }
            $x++;
        }
        $str .= 'END AS isInApproval';

        $data['approval_viewdata'] = $this->db->query("SELECT currentLevelNo,timesheetMasterID,employeename,description as taskdescription,customerName,projectName, DATE_FORMAT(startDate,'{$convertFormat}') AS startDate,
                                                        DATE_FORMAT(endDate,'{$convertFormat}') AS endDate,projectCode,timesheetDetailID FROM (SELECT *,{$str} FROM(SELECT lMaster.*,`repManager`,mytimesheetmaster.documentCode,
			                                            mytimesheetmaster.fromDate,mytimesheetmaster.toDate,empTB.Ename2 as employeename,projectplanning.description,customermaster.customerName,projectmaster.projectName,
			                                            projectplanning.startDate,projectplanning.endDate,boqheader.projectCode FROM srp_erp_pm_mytimesheetdetail AS lMaster
			                                            LEFT JOIN srp_erp_pm_mytimesheet mytimesheetmaster ON mytimesheetmaster.timesheetMasterID = lMaster.timesheetMasterID
			                                            LEFT JOIN srp_erp_projectplanning projectplanning on projectplanning.projectPlannningID = lMaster.taskid
			                                            LEFT JOIN srp_erp_boq_header boqheader on boqheader.headerID = projectplanning.headerID
			                                            LEFT JOIN srp_erp_projects projectmaster on projectmaster.projectID = boqheader.ProjectID
			                                            LEFT JOIN srp_erp_customermaster customermaster on customermaster.CustomerAutoID = boqheader.customerCode
			                                            JOIN srp_employeesdetails AS empTB ON empTB.EIdNo = lMaster.empID
			                                            LEFT JOIN ( SELECT empID, managerID AS repManager FROM srp_erp_employeemanagers WHERE active = 1 AND companyID = $companyID ) AS repoManagerTB ON lMaster.empID = repoManagerTB.empID 
		                                                WHERE lMaster.companyID = $companyID AND lMaster.confirmedYN = 1 AND lMaster.approvedYN = '0' AND lMaster.timesheetMasterID = $timesheetmasterID) AS timesheetapprovaldata
		                                                LEFT JOIN ( SELECT managerID AS topManager, empID AS topEmpID FROM srp_erp_employeemanagers WHERE companyID = $companyID AND active = 1 ) AS topManagerTB ON timesheetapprovaldata.repManager = topManagerTB.topEmpID 
	                                                    ) AS t1 WHERE `t1`.`isInApproval` = 1")->result_array();

        $this->load->view('system/pm/mytimesheet_approval', $data);

    }

    function timesheet_approval()
    {

        echo json_encode($this->Boq_model->save_timesheetApproval());
    }

    function save_clone_project()
    {

        $this->form_validation->set_rules('projectname', 'Project Name', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $projectname = $this->input->post('projectname');
            $projectExist = $this->db->query("SELECT projectName FROM `srp_erp_projects` where projectName = '{$projectname}'")->row('projectName');
            if (!empty($projectExist)) {
                echo json_encode(array('e', 'Project Name Already Exist'));

            } else {
                echo json_encode($this->Boq_model->save_clone_project());
            }
        }

    }

    function load_projecttaskschedule()
    {
        $datefrom = $this->input->post('filter_date_from');
        $dateto = $this->input->post('filter_date_to');
        $project = $this->input->post('project[]');
        $projectID = '';
        if (!empty($project)) {
            $projectID = "( " . join("' , '", $project) . " )";
        }
        $data['projectID'] = $projectID;
        $data['datefromconvert'] = $datefrom;
        $data['datetoconvert'] = $dateto;
        $this->load->view('system/pm/project_task', $data);
    }

    function load_project_tasksheduledata()
    {
        $datefrom = $this->input->post('datefromconvert');
        $dateto = $this->input->post('datetoconvert');
        $project = $this->input->post('project');
        $companyID = current_companyID();
        $convertFormat = convert_date_format_sql();
        $date_format_policy = date_format_policy();
        $datefromconvert = input_format_date($datefrom, $date_format_policy);
        $datetoconvert = input_format_date($dateto, $date_format_policy);
        $date = "";
        $userID = current_userID();
        if (!empty($datefrom) && !empty($dateto)) {
            $date .= " AND ( startDate >= '" . $datefromconvert . "' AND endDate <= '" . $datetoconvert . "')";
        }
        $project_filter = '';
        if (!empty($project)) {
            $project_filter .= " AND header.ProjectID IN ($project)";
        }

        $sSearch = $this->input->post('sSearch');
        $searches = '';
        if ($sSearch) {
            $search = str_replace("\\", "\\\\", $sSearch);
            $searches = "  AND (ename2 Like '%$sSearch%') OR (srp_erp_projectplanning.description Like '%$sSearch%') OR (header.projectDescription Like '%$sSearch%') OR (projects.projectName Like '%$sSearch%')";
        }


        $where = ' srp_erp_projectplanning.companyID = ' . $companyID . ' ' . $date . ' ' . $project_filter . ' AND t.EIdNo= ' . $userID . '' . $searches . '';
        $this->datatables->select('header.projectCode,header.projectDescription,IFNULL(planning.description,srp_erp_projectplanning.description) as project,srp_erp_projectplanning.projectPlannningID,srp_erp_projectplanning.headerID as headerID,srp_erp_projectplanning.masterID as masterID,srp_erp_projectplanning.description as description,
        srp_erp_projectplanning.note,srp_erp_projectplanning.percentage,DATE_FORMAT( srp_erp_projectplanning.startDate,"' . $convertFormat . '") AS startDate,DATE_FORMAT( srp_erp_projectplanning.endDate,"' . $convertFormat . '") AS endDate,srp_erp_projectplanning.bgColor,
        srp_erp_projectplanning.levelNo,srp_erp_projectplanning.sortOrder,GROUP_CONCAT( Ename2 SEPARATOR \' , \' ) AS ename2,projects.projectName')
            ->from('srp_erp_projectplanning')
            ->join("(SELECT EIdNo,Ename2, srp_erp_projectplanningassignee.headerID, projectPlannningID FROM srp_erp_projectplanningassignee LEFT JOIN `srp_employeesdetails` ON empID = EIdNo ORDER BY empID ASC ) t", 't.projectPlannningID = srp_erp_projectplanning.projectPlannningID 
	AND t.headerID = srp_erp_projectplanning.headerID', 'left')
            ->join('srp_erp_projectplanning planning', 'planning.projectPlannningID = srp_erp_projectplanning.masterID', 'left')
            ->join('srp_erp_boq_header header', 'header.headerID = srp_erp_projectplanning.headerID', 'left')
            ->join('srp_erp_projects projects', 'projects.projectID = header.projectID', 'left')
            ->where($where)
            ->group_by("srp_erp_projectplanning.projectPlannningID")
            ->group_by("srp_erp_projectplanning.masterID")
            ->group_by("header.headerID");
        echo $this->datatables->generate();
    }

    function get_employee_tansferpm()
    {
        $project_filter = '';
        $project = $this->input->post('project');
        if (!empty($project)) {
            $project_filter .= " AND header.ProjectID IN ($project)";
        }
        $where = ' srp_erp_projectplanning.companyID = ' . current_companyID() . ' ' . $project_filter . '';
        $this->datatables->select('srp_erp_projectplanning.projectPlannningID as projectPlannningID,header.projectCode,project.projectName as project,header.projectDescription as projectname,srp_erp_projectplanning.projectPlannningID,srp_erp_projectplanning.headerID,masterID,srp_erp_projectplanning.description,
                                 DATE_FORMAT( startDate, \'%d-%m-%Y\' ) AS startDate,DATE_FORMAT( endDate, \'%d-%m-%Y\' ) AS endDate,GROUP_CONCAT( Ename2 SEPARATOR \',\' ) AS ename2,GROUP_CONCAT( EIdNo SEPARATOR \',\' ) AS employeeID,header.headerID as headerID,header.ProjectID');
        $this->datatables->from('srp_erp_projectplanning');
        $this->datatables->join('( SELECT EIdNo,Ename2, srp_erp_projectplanningassignee.headerID, projectPlannningID FROM srp_erp_projectplanningassignee LEFT JOIN `srp_employeesdetails` ON empID = EIdNo   ORDER BY empID ASC )t',
            't.projectPlannningID = srp_erp_projectplanning.projectPlannningID AND t.headerID = srp_erp_projectplanning.headerID', 'left');
        $this->datatables->join('srp_erp_boq_header header ', 'header.headerID = t.headerID');
        $this->datatables->join('srp_erp_projects project', 'project.projectID = header.projectID');
        $this->datatables->where($where);
        $this->datatables->add_column('transferbtn', '<span class="pull-right"><a href="#" onclick="project_transfer($1,\'$2\',\'$3\',\'$4\',$5)"><span title="Transfer" rel="tooltip" class="fa fa-plus"></span></a>', 'projectPlannningID,startDate,endDate,employeeID,headerID');
        $this->db->group_by("srp_erp_projectplanning.projectPlannningID");
        $this->db->group_by("masterID");
        $this->db->having("ename2 IS NOT NULL");
        echo $this->datatables->generate();
    }

    function fetch_employee_drop_by_task()
    {
        $data_arr = array();
        $companyID = current_companyID();
        $planningID = trim($this->input->post('planningID') ?? '');
        $employeeID = trim($this->input->post('employeeID') ?? '');
        $employee = $this->db->query("SELECT
	                                     srp_erp_projectplanningassignee.EmpID,
	                                     empdetail.Ename2 as employeename
                                         FROM
	                                    `srp_erp_projectplanning`
	                                     LEFT JOIN srp_erp_projectplanningassignee on srp_erp_projectplanningassignee.projectPlannningID = srp_erp_projectplanning.projectPlannningID
	                                     LEFT JOIN srp_employeesdetails empdetail on empdetail.EIdNo = 	srp_erp_projectplanningassignee.EmpID
	                                     where 
	                                     companyID = $companyID
	                                     AND srp_erp_projectplanning.projectPlannningID = $planningID or masterID = $planningID
	                                     AND srp_erp_projectplanningassignee.empID IN ($employeeID)")->result_array();
        if (!empty($employee)) {
            foreach ($employee as $row) {
                $data_arr[trim($row['EmpID'] ?? '')] = trim($row['employeename'] ?? '');
            }
        }
        echo form_dropdown('transferemployee[]', $data_arr, '', 'class="form-control " id="filter_employees"  multiple="" ');
    }

    function fetch_employee_project()
    {
        $headerID = trim($this->input->post('headerID') ?? '');
        $companyID = current_companyID();
        $employee_project = $this->db->query("SELECT
	                                              srp_erp_boq_header.projectID,
	                                              projectCode,
	                                              projectName,
	                                              srp_erp_boq_header.headerID
                                                  FROM
	                                             `srp_erp_boq_header`
	                                              LEFT JOIN srp_erp_projects projects ON projects.projectID = srp_erp_boq_header.projectID 
                                                  WHERE
                                                  srp_erp_boq_header.companyID = $companyID ")->result_array();
        $data_arr = array('' => 'Select Transfer Project');
        if (!empty($employee_project)) {
            foreach ($employee_project as $row) {
                $data_arr[trim($row['headerID'] ?? '')] = trim($row['projectName'] ?? '');
            }
        }
        echo form_dropdown('transferprojectID', $data_arr, '', 'class="form-control select2" id="transferprojectID" onchange="fetch_transfertask(this.value)"');
    }

    function fetch_transfertaskid()
    {
        $transfertaskID = $this->input->post('transfertaskID');
        $startdate = $this->input->post('startdate');
        $enddate = $this->input->post('enddate');
        $projectplanningID = $this->input->post('projectplanningID');
        $date_format_policy = date_format_policy();
        $startdateformatted = input_format_date($startdate, '%Y-%m-%d');
        $todateformatted = input_format_date($enddate, '%Y-%m-%d');
        /*  AND Date( startDate ) >= '{$startdateformatted}'  AND Date( endDate) <= '{$todateformatted}'*/
        $companyID = current_companyID();
        $taskIDtransfer = $this->db->query("SELECT
	                                             projectPlannningID,description
                                                 FROM
	                                            `srp_erp_projectplanning`
	                                             where 
	                                             companyID = $companyID
	                                             AND headerID = $transfertaskID
	                                             AND projectPlannningID != $projectplanningID
	                                             ")->result_array();
        $data_arr = array('' => 'Select Transfer Task');
        if (!empty($taskIDtransfer)) {
            foreach ($taskIDtransfer as $row) {
                $data_arr[trim($row['projectPlannningID'] ?? '')] = trim($row['description'] ?? '');
            }
        }
        echo form_dropdown('transfertaskID', $data_arr, '', 'class="form-control select2" id="transfertaskID"');

    }

    function save_project_transfer()
    {
        $this->form_validation->set_rules('headerid', 'Project', 'trim|required');
        $this->form_validation->set_rules('filter_employees[]', 'Employee', 'trim|required');
        $this->form_validation->set_rules('transferprojectID', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('transfertaskID', 'Project Name', 'trim|required');
        $this->form_validation->set_rules('projectplanningID', 'Planning ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Boq_model->save_project_transfer());
        }
    }

    function save_project_transfer_master()
    {
        $this->form_validation->set_rules('narration', 'Narration', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $insert_data['Transferdate'] = $this->common_data['current_date'];
            $insert_data['TransferbyempID'] = current_userID();
            $insert_data['Narration'] = $this->input->post('narration');
            $insert_data['companyID'] = current_companyID();
            $insert_data['companyCode'] = current_company_code();
            $insert_data['createdUserGroup'] = $this->common_data['user_group'];
            $insert_data['createdPCID'] = $this->common_data['current_pc'];
            $insert_data['createdUserID'] = $this->common_data['current_userID'];
            $this->db->insert('srp_erp_project_transfer', $insert_data);
            $this->db->trans_complete();
            if ($this->db->trans_status() == true) {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Project Name Already Exist'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Project Name Already Exist'));
            }
        }
    }

    function fetch_projecttransfer_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $this->datatables->select('ID,DATE_FORMAT(Transferdate,"' . $convertFormat . '") AS Transferdate,	srp_employeesdetails.Ename2 as employeename,Narration')
            ->from('srp_erp_project_transfer')
            ->join('srp_employeesdetails', 'srp_employeesdetails.EIdNo = srp_erp_project_transfer.TransferbyempID', 'left')
            ->where('companyID', $companyid)
            ->add_column('edit', '<a onclick="load_details($1)"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil" ></span>', 'ID');
        echo $this->datatables->generate();
    }

    function fetch_emp_detail()
    {
        $convertFormat = convert_date_format_sql();
        $data['masterID'] = $this->input->post('masterID');
        $companyID = current_companyID();
        $data['detail'] = $this->db->query("SELECT ID, DATE_FORMAT(Transferdate,\"$convertFormat\") AS Transferdate, srp_employeesdetails.Ename2 as employeename, Narration FROM `srp_erp_project_transfer` 
                                                LEFT JOIN srp_employeesdetails ON srp_employeesdetails.EIdNo = srp_erp_project_transfer.TransferbyempID WHERE companyID = $companyID AND ID = {$data['masterID']}")->row_array();

        $data['detail_rec'] = $this->db->query("SELECT
srp_erp_projectplanning.description as projectplannig,
transfer.description as transferplanning,
project.projectName as projecttransferfrom,
empdetail.Ename2,
projecttransfer.projectName as transferprojectname

FROM
	`srp_erp_projectplanningassignee`
	LEFT JOIN srp_erp_projectplanning on srp_erp_projectplanning.projectPlannningID = srp_erp_projectplanningassignee.projectPlannningID
	LEFT JOIN srp_erp_projectplanning transfer on transfer.projectPlannningID = srp_erp_projectplanningassignee.TransferprojectPlannningID
	LEFT JOIN srp_erp_boq_header header on header.headerID = srp_erp_projectplanningassignee.headerID
	LEFT JOIN srp_erp_projects project on project.projectID = header.projectID
	LEFT JOIN srp_employeesdetails empdetail on empdetail.EIdNo = srp_erp_projectplanningassignee.empID
	LEFT JOIN srp_erp_boq_header headertransterproject on  headertransterproject.headerID = transfer.headerID
	LEFT JOIN srp_erp_projects projecttransfer on  projecttransfer.ProjectID = headertransterproject.ProjectID
	where 
	ProjectTransfermasterID = {$data['masterID']}
	ANd header.companyID = $companyID")->result_array();
        $view = $this->load->view('system/pm/project_transfer_sub_detail', $data, true);
        echo json_encode(['s', 'view' => $view]);
    }

    function fetch_pt_master()
    {
        $convertFormat = convert_date_format_sql();
        $companyid = current_companyID();
        $masterID = $this->input->post('masterID');
        $this->datatables->select('srp_erp_projectplanning.projectPlannningID as projectPlannningID,srp_erp_projectplanning.description as projectplannig,transfer.description as transferplanning,project.projectName as projecttransferfrom,empdetail.Ename2,projecttransfer.projectName as transferprojectname')
            ->from('srp_erp_projectplanningassignee')
            ->join('srp_erp_projectplanning', 'srp_erp_projectplanning.projectPlannningID = srp_erp_projectplanningassignee.projectPlannningID', 'left')
            ->join('srp_erp_projectplanning transfer', 'transfer.projectPlannningID = srp_erp_projectplanningassignee.TransferprojectPlannningID', 'left')
            ->join('srp_erp_boq_header header', 'header.headerID = srp_erp_projectplanningassignee.headerID', 'left')
            ->join('srp_erp_projects project', 'project.projectID = header.projectID', 'left')
            ->join('srp_employeesdetails empdetail', 'empdetail.EIdNo = srp_erp_projectplanningassignee.empID', 'left')
            ->join('srp_erp_boq_header headertransterproject', 'headertransterproject.headerID = transfer.headerID', 'left')
            ->join('srp_erp_projects projecttransfer', 'projecttransfer.ProjectID = headertransterproject.ProjectID', 'left')
            ->where('ProjectTransfermasterID', $masterID)
            ->where('header.companyID', $companyid);
        echo $this->datatables->generate();
    }

    function load_pm_audit_report()
    {
        $this->form_validation->set_rules('project_id[]', 'Project', 'required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', 'End Date', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo ' <div class="alert alert-warning" role="alert">
                ' . validation_errors() . '
                    </div>';
        } else {
            $data['ProjectID'] = join(",", $this->input->post('project_id'));

            $data['Employeetype'] = '';
            $data['EmploymentStatus'] = '';
            if ($this->input->post('employeenationality_id')) {
                $data['Employeetype'] = join(",", $this->input->post('employeenationality_id'));
            }

            if ($this->input->post('employeenatstatus')) {
                $data['EmploymentStatus'] = join(",", $this->input->post('employeenatstatus'));
            }


            $data['start_date'] = $this->input->post('start_date');
            $data['end_date'] = $this->input->post('end_date');
            $this->load->view('system/pm/project_audit_report', $data);
        }

    }

    function fetch_audit_report()
    {
        $case = '';
        $companyID = current_companyID();
        $projectID = $this->input->post('projectid');
        $startdate = $this->input->post('startdate');
        $enddate = $this->input->post('enddate');
        $date_format_policy = date_format_policy();
        $formatted_startdate = input_format_date($startdate, $date_format_policy);
        $formatted_enddate = input_format_date($enddate, $date_format_policy);


        $where = 'log.companyID = ' . $companyID . ' AND DocumentType = \'PM\' AND display_name IS NOT NULL AND header.projectID IN(' . $projectID . ') AND  DATE(log.`timestamp`) BETWEEN \'' . $formatted_startdate . '\' AND \'' . $formatted_enddate . '\'  ';


        $this->datatables->select('log.id AS id,log.columnName,concat( DocumentName, \'-\', displaycol.display_name ) AS display_name,display_old_val,display_new_val,log.`timestamp`,log.`timestamp`,concat(empdetail.ECode, \' - \', empdetail.Ename2 ) AS updateemployee,projects.projectName as projectName')
            ->from('`srp_erp_audit_log` log')
            ->join('`srp_erp_audit_display_columns` `displaycol`', '`log`.`columnName` = `displaycol`.`col_name` AND `log`.`tableName` = `displaycol`.`tbl_name`', 'left')
            ->join('srp_employeesdetails empdetail', 'empdetail.EIdNo = log.userID', 'left')
            ->join('srp_erp_boq_header header', ' header.headerID = log.rowID', 'left')
            ->join('srp_erp_projects projects', 'projects.projectID = header.projectID', 'left')
            ->where($where)
            ->group_by('log.id');
        echo $this->datatables->generate();
    }

    function get_projectmanagement_auditrptexcel()
    {
        $primaryLanguage = getPrimaryLanguage();
        $employee = $this->input->post('employeeid');
        $startDate = $this->input->post('startdate');
        $endDate = $this->input->post('enddate');
        $nationality_id = $this->input->post('empnationality');
        $emp_status = $this->input->post('employementstatus');
        $companyID = current_companyID();

        $date_format_policy = date_format_policy();
        $formatted_startdate = input_format_date($startDate, $date_format_policy);
        $formatted_enddate = input_format_date($endDate, $date_format_policy);
        $detail = $this->fetch_auditlog_detail();

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Audit Report');

        $ex_data = [];
        $fileName = 'Audit Report.xlsx';

        $ex_data[0] = [$this->common_data['company_data']['company_name']];
        $ex_data[1] = ['Audit Report'];
        $ex_data[2] = [];
        $ex_data[3] = [
            '#',
            'Project',
            'Column Name',
            'Old Value',
            'New Value',
            'Updated Time',
            'Updated By',
        ];

        $r = 1;
        foreach ($detail as $row) {
            $ex_data[] = [
                $r, $row['projectName'], $row['display_name'], $row['display_old_val'], $row['display_new_val'], $row['timestamp'],
                $row['updateemployee']
            ];
            $r++;
        }

        $this->excel->getActiveSheet()->fromArray($ex_data, null, 'A1');
        $this->excel->getActiveSheet()->getStyle('A1:I3')->getFont()->setBold(true)->setSize(11)->setName('Calibri');
        $this->excel->getActiveSheet()->getStyle('A1:I3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->excel->getActiveSheet()->mergeCells('A1:I1');
        $this->excel->getActiveSheet()->mergeCells('A2:I2');
        $this->excel->getActiveSheet()->mergeCells('A3:I3');
        $this->excel->getActiveSheet()->getStyle('A4:I4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('cee2f3');

        $fileName = 'Audit Report.xls';
        header('Content-Type: application/vnd.ms-excel;charset=utf-16');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($this->excel);
        $writer->save('php://output');
    }

    function get_pm_auditrpt()
    {
        $employee = $this->input->post('employeeid');
        $startDate = $this->input->post('startdate');
        $endDate = $this->input->post('enddate');
        $nationality_id = $this->input->post('empnationality');
        $emp_status = $this->input->post('employementstatus');

        $companyID = current_companyID();
        $date_format_policy = date_format_policy();
        $formatted_startdate = input_format_date($startDate, $date_format_policy);
        $formatted_enddate = input_format_date($endDate, $date_format_policy);
        $data['detail'] = $this->fetch_auditlog_detail();


        $html = $this->load->view('system/pm/pm_audit_report_pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4', 1);


    }

    function fetch_auditlog_detail()
    {
        $companyID = current_companyID();
        $projectID = $this->input->post('projectid');
        $startdate = $this->input->post('startdate');
        $enddate = $this->input->post('enddate');
        $date_format_policy = date_format_policy();
        $formatted_startdate = input_format_date($startdate, $date_format_policy);
        $formatted_enddate = input_format_date($enddate, $date_format_policy);
        $where = ' 	log.companyID = ' . $companyID . ' AND DocumentType = \'PM\' AND display_name IS NOT NULL AND header.projectID IN (' . $projectID . ') AND DATE( log.TIMESTAMP) BETWEEN \'' . $formatted_startdate . '\' AND \'' . $formatted_enddate . '\' ';
        $query = $this->db->query("SELECT `log`.`id` AS `id`, `log`.`columnName`, concat( DocumentName, '-', displaycol.display_name ) AS display_name, `display_old_val`, `display_new_val`, `log`.`timestamp`, `log`.`timestamp`, concat( empdetail.ECode, ' - ', empdetail.Ename2 ) AS updateemployee,
	`projects`.`projectName` AS `projectName` FROM `srp_erp_audit_log` `log` LEFT JOIN `srp_erp_audit_display_columns` `displaycol` ON `log`.`columnName` = `displaycol`.`col_name` 
	AND `log`.`tableName` = `displaycol`.`tbl_name` LEFT JOIN `srp_employeesdetails` `empdetail` ON `empdetail`.`EIdNo` = `log`.`userID`
	LEFT JOIN `srp_erp_boq_header` `header` ON `header`.`headerID` = `log`.`rowID` LEFT JOIN `srp_erp_projects` `projects` ON `projects`.`projectID` = `header`.`projectID` WHERE $where
	AND `DocumentType` = 'PM' AND `display_name` IS NOT NULL GROUP BY `log`.`id` ORDER BY `id` DESC")->result_array();
        return $query;

    }

    function get_project_category()
    {
        $projectID = $this->input->post('projectID');
        $companyID = current_companyID();
        $relatedproject = $this->db->query("SELECT categoryID,concat(categoryCode,' | ',categoryDescription) as cat
                                                         FROM `srp_erp_boq_category` WHERE `companyID` = '{$companyID}' AND `projectID` = '{$projectID}'")->result_array();
        $data_arr = array('' => 'Select Project Category');

        if (!empty($relatedproject)) {
            foreach ($relatedproject as $row) {
                $data_arr[trim($row['categoryID'] ?? '')] = trim($row['cat'] ?? '');
            }
        }
        echo form_dropdown('project_category', $data_arr, '', 'class="form-control select2" id="project_category"');


    }

    function udate_varianceamt()
    {
        $amount = $this->input->post('amount');
        $detailID = $this->input->post('detailID');
        $data['variationAmount'] = $amount;
        $data['variationAmountAfterConfirmedYN'] = $amount;
        $this->db->where('detailID', $detailID);
        $this->db->update('srp_erp_boq_details', $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() == true) {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Variance Amount Updated Sucessfully'));
        } else {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'Variance Amount Updated faild'));
        }
    }

    function fetch_eoi_attachment()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $documentID = $this->input->post('documentID');
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` WHERE companyID = $companyID 
                    AND documentSystemCode = $headerID AND documentID = '{$documentID}'")->result_array();
        $this->load->view('system/pm/eoi_attachment_view', $data);
    }

    function save_eoi_tender()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $tenderstatus = $this->input->post('tenderstatus');
        $statustender = $this->db->query("select tenderstatus from srp_erp_boq_header where companyID = $companyID AND headerID=$headerID")->row('tenderstatus');
        $date_format_policy = date_format_policy();
        
        $insurancepolictStartDate = input_format_date($this->input->post('insPolicyDateFrom'), $date_format_policy);
        $insurancepolictendDate = input_format_date($this->input->post('insPolicyDateTo'), $date_format_policy);

        if (($tenderstatus != 3) && ($tenderstatus != 4)) {
            if ((($statustender == 3) || ($statustender == 4)) || ($statustender == 2)) {
                echo json_encode(array('e', 'You cannot change the tender status'));
                exit();
            }

        }


        $this->form_validation->set_rules("eoistatus", 'EOI Status', 'trim|required');
        $this->form_validation->set_rules("eoisubdate", 'EOI Submission Date', 'trim|required');
        /*   $this->form_validation->set_rules("commentsstatus", 'Comments', 'trim|required');
           $this->form_validation->set_rules("commentsstatus", 'Comments', 'trim|required');*/
        $this->form_validation->set_rules("headerID", 'headerID', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if($insurancepolictendDate < $insurancepolictStartDate)
            { 
                echo json_encode(array('e', 'Insurance policy End date cannot be less than policy start'));
                exit();
            }else{
                echo json_encode($this->Boq_model->save_eoi_tender());
            }

          
        }
    }

    function fetch_tender_attachment()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');

        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = $headerID AND documentID = 'PROTENDER'")->result_array();
        $this->load->view('system/pm/tender_attachment_view', $data);
    }

    function fetch_bid_attachment()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');

        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = $headerID AND documentID = 'PROBID'")->result_array();
        $this->load->view('system/pm/bid_attachment_view.php', $data);
    }

    function fetch_budget_attachment()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');

        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = $headerID AND documentID = 'PROBUDGET'")->result_array();
        $this->load->view('system/pm/budget_attachment_view.php', $data);
    }

    function save_sent_forapproval()
    {
        $headerID = $this->input->post('headerID');


        $this->db->select('headerID');
        $this->db->where('headerID', $headerID);
        $this->db->where('budgetapprovalmanagement', 2);
        $this->db->from('srp_erp_boq_header');
        $Confirmed = $this->db->get()->row_array();
        if (!empty($Confirmed)) {
            echo json_encode(array('w', 'Document already sent for approval'));
            exit;
        } else {
            $headerID = $headerID;

            $this->load->library('Approvals');
            $this->db->select('*');
            $this->db->where('headerID', $headerID);
            $this->db->from('srp_erp_boq_header');
            $c_data = $this->db->get()->row_array();
            $autoApproval = get_document_auto_approval('PVE');
            if ($autoApproval == 1) {
                $approvals_status = $this->approvals->CreateApproval_boq_budget('PVE', $c_data['headerID'], $c_data['projectCode'], 'Project - Budget Approval', 'srp_erp_boq_header', 'headerID', 1, '');
            } else {
                echo json_encode(array('e', 'Approval levels are not set for this document'));
                exit;
            }
            if ($approvals_status) {
                $data = array(
                    'bdconfirmedYNmn' => 1,
                    'budgetapprovalmanagement' => 2,
                    'bdconfirmedDatemn' => $this->common_data['current_date'],
                    'bdconfirmedByEmpIDmn' => $this->common_data['current_userID'],
                    'bdconfirmedByNamemn' => $this->common_data['current_user'],
                );
                $this->db->where('headerID', $headerID);
                $this->db->update('srp_erp_boq_header', $data);
                $isexist = (!empty(fetch_boq_approvals()) ? 1 : 0);
                echo json_encode(array('s', 'Approvals Created Successfully', $isexist));

            } else {
                echo json_encode(array('e', 'oops, something went wrong!.'));
            }
        }
    }

    function save_approvalstatus()
    {
        $level = fetch_boq_approvals();
        $system_code = trim($this->input->post('headerID') ?? '');
        $level_id = $level['approvalLevelID'];
        $status = trim($this->input->post('status') ?? '');
        $code = 'PVE';
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('headerID');
                $this->db->where('headerID', trim($system_code));
                $this->db->where('bdconfirmedYNmn', 2);
                $this->db->from('srp_erp_boq_header');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('headerID');
            $this->db->where('headerID', trim($system_code));
            $this->db->where('bdconfirmedYNmn', 2);
            $this->db->from('srp_erp_boq_header');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_approval());
                    }
                }
            }
        }
    }

    function fetch_bd_data()
    {
        $headerID = $this->input->post('headerID');
        $companyID = current_companyID();
        $budgetdetail = $this->db->query("SELECT budgetapprovalmanagement, bdconfirmedYNmn, bdapprovedYNmn FROM `srp_erp_boq_header` where companyID = $companyID AND headerID = $headerID")->row_array();
        echo json_encode($budgetdetail);
    }

    function fetch_project_charter()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyId = current_companyID();
        $data['detail'] = $this->db->query("SELECT teamID,empName,roleDescription,CASE WHEN organizationID = 1 THEN 'Client' WHEN organizationID = 2 THEN 'Contractor'
                                                WHEN organizationID = 3 THEN 'External' END AS Organization FROM `srp_erp_projectteam`
	                                            LEFT JOIN srp_erp_project_role role on role.roleID = srp_erp_projectteam.roleID
	                                            WHERE srp_erp_projectteam.CompanyID = $companyId AND boqheaderID = {$data['headerID'] } ORDER BY srp_erp_projectteam.teamID DESC")->result_array();

        $data['header'] = $this->db->query("SELECT charterprojectDescription, delayedbyClient, delayedbyContractor FROM `srp_erp_boq_header` WHERE companyID = $companyId 
	                                             AND headerID = {$data['headerID']}")->row_array();


        $data['timelineforproject'] = $this->db->query("SELECT timelineID,phaseDescription, DATE_FORMAT(plannedcompletionDate,'%Y-%m-%d') AS plannedcompletionDate, IFNULL(DATE_FORMAT(actualcompletionDate,'%Y-%m-%d'),'-') as actualcompletionDate
                                                            FROM `srp_erp_projecttimeline` where CompanyID = $companyId AND headerID ={$data['headerID'] } ")->result_array();

        $this->load->view('system/pm/projectcharter', $data);
    }

    function fetch_boq_attachment()
    {
        $companyID = current_companyID();
        $data['headerID'] = $this->input->post('headerID');
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentID = 'PROBOQ'")->result_array();
        $this->load->view('system/pm/boq_attachment_view', $data);
    }

    function save_project_team()
    {
        $organization = $this->input->post('organization');
        $this->form_validation->set_rules("organization", 'Organization', 'trim|required');
        $this->form_validation->set_rules("organizationrole", 'Role', 'trim|required');
        if ($organization == 2) {
            $this->form_validation->set_rules("Employee", 'Employee', 'trim|required');
        } else {
            $this->form_validation->set_rules("empname", 'Employee', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Boq_model->save_project_team());
        }
    }

    function fetch_project_team()
    {
        $teamID = $this->input->post('teamid');
        $data = $this->db->query("SELECT organizationID, roleID, empid, empName FROM srp_erp_projectteam WHERE teamID = $teamID")->row_array();
        echo json_encode($data);
    }

    function delete_project_team()
    {
        $teamID = $this->input->post('teamid');
        $this->db->trans_begin();
        $this->db->delete('srp_erp_projectteam', array('teamID' => $teamID));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function save_boq_charter()
    {
        $hederID = $this->input->post('headerID');
        $descriptionoftheproject = $this->input->post('descriptionoftheproject');
        $delayedbyclient = $this->input->post('delayedbyclient');
        $delayedbycontractor = $this->input->post('delayedbycontractor');
        $this->db->trans_begin();

        $data['charterprojectDescription'] = $descriptionoftheproject;
        $data['delayedbyClient'] = $delayedbyclient;
        $data['delayedbyContractor'] = $delayedbycontractor;

        $this->db->where('headerID', $hederID);
        $this->db->update('srp_erp_boq_header', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Updated'));
        }

    }

    function fetch_boq_materialplanning()
    {
        $headerID = $this->input->post('headerID');
        $boq_detailID = $this->input->post('boq_detailID');
        $companyID = current_companyID();
        $type = $this->input->post('type');
        $data['detail'] = $this->db->query("SELECT itemSystemCode, itemDescription, currentStock, requiredQty FROM `srp_erp_projectactivityplanning`
	                                    LEFT JOIN srp_erp_itemmaster on srp_erp_itemmaster.itemAutoID = srp_erp_projectactivityplanning.itemAutoID
	                                    where srp_erp_projectactivityplanning.companyID =$companyID AND type = 1   AND boqheaderID = $headerID  AND detailID = $boq_detailID AND (srp_erp_projectactivityplanning.tenderType = 1 OR srp_erp_projectactivityplanning.tenderType IS NULL)")->result_array();
        $this->load->view('system/pm/materialplanning', $data);

    }

    function save_material()
    {
        $this->db->trans_begin();
        $headerID = $this->input->post('headerID');
        $boq_detailID = $this->input->post('boq_detailID');
        $companyID = current_companyID();

        $isexist = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where headerID = $headerID AND detailID =$boq_detailID")->row('costingID');
        if (empty($isexist)) {
            echo json_encode(array('e', 'Items does not exist'));
            exit();
        }
        $detail = $this->db->query("SELECT activityplanningID  FROM `srp_erp_projectactivityplanning`
	                                    where srp_erp_projectactivityplanning.companyID =$companyID AND boqheaderID = $headerID  AND detailID = $boq_detailID ")->result_array();
        if (!empty($detail)) {

            $this->db->delete('srp_erp_projectactivityplanning', array('detailID' => $boq_detailID, 'boqheaderID' => $headerID));
        }
        $itemmasterDet = $this->db->query("SELECT itemmaster.itemAutoID,itemmaster.itemSystemCode, itemmaster.itemDescription,currentStock,srp_erp_boq_costing.Qty as requiredQty
                                                    FROM `srp_erp_boq_costing` LEFT JOIN srp_erp_itemmaster itemmaster on itemmaster.itemAutoID = srp_erp_boq_costing.ItemAutoID
                                                    WHERE headerID = $headerID  AND detailID = $boq_detailID")->result_array();
        foreach ($itemmasterDet as $val) {
            $data['boqheaderID'] = $headerID;
            $data['detailID'] = $boq_detailID;
            $data['type'] = 1;
            $data['itemAutoID'] = $val['itemAutoID'];
            $data['requiredQty'] = $val['requiredQty'];
            $data['currentQty'] = $val['currentStock'];
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_projectactivityplanning', $data);

        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            echo json_encode(array('e', 'Records Updated faild'));
        } else {
            echo json_encode(array('s', 'Material planning updated successfully'));
        }

    }

    function fetch_boq_hrplanning()
    {
        $data['headerID'] = $this->input->post('headerID');
        $data['boq_detailID'] = $this->input->post('boq_detailID');
        $companyID = current_companyID();
        $data['hrplanningdet'] = $this->db->query("SELECT activityplanningID,srp_designation.DesDescription,availablenoofheads,requirednoofheads,	CASE WHEN hrplanaction = 1 THEN 'New Recruitment'
                                                        WHEN hrplanaction = 2 THEN 'Shared Manpower' WHEN hrplanaction = 3 THEN 'Sub-let Workforce'  WHEN hrplanaction = 4 THEN 'Resource Available' END AS hrplanaction
                                                        FROM `srp_erp_projectactivityplanning` LEFT JOIN srp_designation on srp_designation.DesignationID = srp_erp_projectactivityplanning.designationID 
	                                                    LEFT JOIN (SELECT COUNT(IFNULL( EIdNo, 0 )) AS empcount,EmpDesignationId FROM srp_employeesdetails WHERE Erp_companyID = $companyID GROUP BY EmpDesignationId
	                                                    ) empdetail on empdetail.EmpDesignationId = srp_erp_projectactivityplanning.designationID
	                                                    WHERE  companyID = $companyID  AND type = 2  AND boqheaderID = {$data['headerID']}  AND detailID = {$data['boq_detailID']} 
	                                                  ORDER BY activityplanningID asc")->result_array();

        $this->load->view('system/pm/hrplanning', $data);
    }

    function fetch_noofheads()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $boq_detailID = $this->input->post('boq_detailID');
        $designationID = $this->input->post('designationID');
        $drop = $this->db->query("select COUNT(IFNULL(EIdNo,0)) as empcount, IFNULL(activityplan.requirednoofheads,0) as requirednoofheads
from srp_employeesdetails LEFT JOIN (SELECT SUM(IFNULL(requirednoofheads,0)) as requirednoofheads,designationID
FROM `srp_erp_projectactivityplanning` where boqheaderID = $headerID AND designationID = $designationID) activityplan on activityplan.designationID = srp_employeesdetails.EmpDesignationId
where Erp_companyID = $companyID AND EmpDesignationId = $designationID")->row_array();
        echo json_encode($drop);

    }

    function save_hrplanning()
    {
        $headerID = $this->input->post('headerID');
        $boq_detailID = $this->input->post('boq_detailID');
        $designationID = $this->input->post('DesignationID');
        $noofrequiredheads = $this->input->post('noofrequiredheads');
        $noofavailableheads = $this->input->post('noofavailableheads');
        $hrplanningtype = $this->input->post('hrplanningtype');
        $companyID = current_companyID();


        $this->form_validation->set_rules("DesignationID", 'Designation', 'trim|required');
        $this->form_validation->set_rules("noofrequiredheads", 'No Of Required Heads', 'trim|required');
        $this->form_validation->set_rules("hrplanningtype", 'HR Planning Action', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $isexist = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` where companyID  =$companyID AND type = 2 
                                        AND boqheaderID = $headerID AND detailID = $boq_detailID AND designationID = $designationID")->row('activityplanningID');
            if ($isexist) {
                echo json_encode(array('e', 'Designation already exist,please delete and try again'));
                exit();
            }
            $data['boqheaderID'] = $headerID;
            $data['detailID'] = $boq_detailID;
            $data['type'] = 2;
            $data['designationID'] = $designationID;
            $data['requirednoofheads'] = $noofrequiredheads;
            $data['availablenoofheads'] = $noofavailableheads;
            $data['hrplanaction'] = $hrplanningtype;

            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_projectactivityplanning', $data);

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                echo json_encode(array('e', 'Records Updated faild'));
            } else {
                echo json_encode(array('s', 'HR planning updated successfully'));
            }
        }
    }

    function delete_hrplanning()
    {
        $planningID = $this->input->post('activityplanningID');
        $this->db->trans_begin();
        $this->db->delete('srp_erp_projectactivityplanning', array('activityplanningID' => $planningID));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function equipmentplanning_view()
    {
        $data['headerID'] = $this->input->post('headerID');
        $data['boq_detailID'] = $this->input->post('boq_detailID');
        $companyID = current_companyID();
        $data['asset_view'] = $this->db->query("SELECT equipmentType as equipmentTypeID,IF(equipmentType=1,'Own','Rented') as equipmentType,
IFNULL(assetDescription,'-') as assetDescription,
IFNULL(supmaster.supplierName,'-') as supplierName,
IFNULL(faCode,'-') as faCode,
IFNULL(equipmentDescription,'-') as equipmentDescription,
IFNULL(IF(equpimentcostType=1,CONCAT(rentedPeriod,' ','Hours'),CONCAT(rentedPeriod,' ','Day')),'-') as rentedPeriod,
IFNULL(IF(equpimentcostType=1,CONCAT(equpimentcost,' ','Per Hour'),CONCAT(equpimentcost,' ','Per Day')),'-') as cost,
IFNULL(IF(operatorYN=1,'Yes',IF(operatorYN=2,'No','-')),'-') as operatoravailability,
activityplanningID
    FROM `srp_erp_projectactivityplanning` LEFT JOIN srp_erp_fa_asset_master assetmaster on assetmaster.faID = srp_erp_projectactivityplanning.faID
    LEFT JOIN srp_erp_suppliermaster supmaster on supmaster.supplierAutoID = srp_erp_projectactivityplanning.supplierID
    where srp_erp_projectactivityplanning.companyID = $companyID AND type=3 AND boqheaderID = {$data['headerID']} AND detailID = {$data['boq_detailID']}")->result_array();
        $this->load->view('system/pm/equipmentplanning', $data);
    }

    function save_asset()
    {
        $equipmenttype = $this->input->post('equipmenttype');
        $assetdrop = $this->input->post('asset_drop');
        $supplierdrop = $this->input->post('supplierdrop');
        $assettext_field = $this->input->post('assettext_field');
        $asset_text_id = $this->input->post('asset_text_id');
        $this->form_validation->set_rules("equipmenttype", 'Equipment Type', 'trim|required');
        if ($equipmenttype == 1) {
            $this->form_validation->set_rules("asset_drop", 'Asset', 'trim|required');
        } else if ($equipmenttype == 2) {
            $this->form_validation->set_rules("assettext_field", 'Asset', 'trim|required');
            $this->form_validation->set_rules("rentedperiods", 'Rented Periods In Days/In Hours', 'trim|required');
            $this->form_validation->set_rules("equpimentcosttype", 'Equpiment Cost Type', 'trim|required');
            $this->form_validation->set_rules("perhcost", 'Per Day/Hour Cost', 'trim|required');
            $this->form_validation->set_rules("withoperator", 'With Operator', 'trim|required');
        } else {
            $this->form_validation->set_rules("equipmenttype", 'Equipment Type', 'trim|required');
        }

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($equipmenttype == 2 && $assettext_field != '' && $asset_text_id == '' && ($supplierdrop == '')) {
                echo json_encode(array('e', 'Supplier field is required'));
            } else {
                echo json_encode($this->Boq_model->save_asset());
            }


        }
    }

    function delete_equipment_plning()
    {
        $planningID = $this->input->post('activityplanningID');
        $this->db->trans_begin();
        $this->db->delete('srp_erp_projectactivityplanning', array('activityplanningID' => $planningID));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function fetch_exe_plan()
    {
        $data['headerID'] = $this->input->post('headerID');
        $this->load->view('system/pm/executionmonitoringandcontrol_view', $data);
    }
    function fetch_exe_plan_monitoring_con(){
        $data['headerID'] = $this->input->post('headerID');
        $this->load->view('system/pm/monitoringandcontrol', $data);
    }

    function save_projecttimeline()
    {
        $headerID = $this->input->post('hederIDtimeline');
        $timelineID = $this->input->post('timelineID');
        $compayID = current_companyID();
        $date_format_policy = date_format_policy();
        $plannedsubdate = $this->input->post('plannedsubdate');
        $plannedsubdate_formatted = input_format_date($plannedsubdate, $date_format_policy);
        $phasedescription = $this->input->post('phasedescription');

        $this->form_validation->set_rules("phasedescription", 'Phase Description', 'trim|required');
        $this->form_validation->set_rules("plannedsubdate", 'Planned Completion Date', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {


            $data['plannedcompletionDate'] = $plannedsubdate_formatted;
            $data['phaseDescription'] = $phasedescription;
            $data['headerID'] = $headerID;

            if($this->input->post('dateValidate') == 0){
                $period = $this->db->where('headerID', $headerID)
                    ->where("'{$plannedsubdate_formatted}' BETWEEN DATE(projectDateFrom) AND DATE(projectDateTo)")
                    ->get('srp_erp_boq_header')->row('headerID');

                if(empty($period)){
                    $msg = 'Completion Date does not fall on project start date to end date.';
                    $msg .= '<br/><b>Are you sure, You want to proceed?</b>';
                    die( json_encode(['w', $msg]) );
                }
            }        
                    
            if ($timelineID) {


                $phaseexist = $this->db->query("SELECT timelineID FROM `srp_erp_projecttimeline` WHERE CompanyID = $compayID AND timelineID!=$timelineID
	                                         AND headerID = $headerID AND phaseDescription = '{$phasedescription}'")->row('timelineID');
                if (!empty($phaseexist)) {
                    echo json_encode(array('e', 'Phase Already Exist!'));
                    exit();
                }

                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('timelineID', $timelineID);
                $this->db->update('srp_erp_projecttimeline', $data);


            } else {
                $phaseexist = $this->db->query("SELECT timelineID FROM `srp_erp_projecttimeline` WHERE CompanyID = $compayID 
	                                         AND headerID = $headerID AND phaseDescription = '{$phasedescription}'")->row('timelineID');
                if (!empty($phaseexist)) {
                    echo json_encode(array('e', 'Phase Already Exist!'));
                    exit();
                }
                $data['companyID'] = current_companyID();
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $this->db->insert('srp_erp_projecttimeline', $data);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully Project Time Line Added'));
            }
        }
    }

    function fetch_timelinedetail()
    {
        $convertFormat = convert_date_format_sql();
        $timelineID = $this->input->post('timelineID');
        $compnayID = current_companyID();
        $data = $this->db->query("SELECT phaseDescription, DATE_FORMAT(plannedcompletionDate,'{$convertFormat}') AS plannedcompletionDate
                                       FROM `srp_erp_projecttimeline` where CompanyID = $compnayID AND timelineID = $timelineID")->row_array();
        echo json_encode($data);
    }

    function delete_projecttimeline()
    {
        $timelineID = $this->input->post('timelineID');
        $this->db->trans_begin();
        $this->db->delete('srp_erp_projecttimeline', array('timelineID' => $timelineID));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function save_recovery_plan()
    {
        $headerID = $this->input->post('hederIDrecoveryplan');
        $phaseID = $this->input->post('phaseID');
        $recoverydueto = $this->input->post('recoverydueto');
        $descriptionofthedelay = $this->input->post('descriptionofthedelay');
        $recoveryplandescription = $this->input->post('recoveryplandescription');
        $additionalmaterial = $this->input->post('additionalmaterial');
        $additionalhr = $this->input->post('additionalhr');
        $otherreq = $this->input->post('otherreq');
        $companyID = current_companyID();
        $this->form_validation->set_rules("recoverydueto", 'Recovery Due to', 'trim|required');
        $this->form_validation->set_rules("hederIDrecoveryplan", 'Header ID', 'trim|required');
        $this->form_validation->set_rules("phaseID", 'Phase ID', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $planningID = $this->db->query("SELECT MAX(DATE( endDate )) AS enddatetimelineactual,projectPlannningID FROM srp_erp_projectplanning  WHERE 
                                                srp_erp_projectplanning.headerID = $headerID AND srp_erp_projectplanning.companyID = $companyID  AND timelineID = $phaseID GROUP BY srp_erp_projectplanning.projectPlannningID,masterID 
                                                ORDER BY enddate DESC  LIMIT 1")->row_array();

            $isexist = $this->db->query("SELECT recoveryID FROM `srp_erp_recoveryplan` where  CompanyID = $companyID  AND headerID = $headerID AND timelineID = $phaseID ")->row_array();
            if ($isexist) {
                $this->db->delete('srp_erp_recoveryplan', array('recoveryID' => $isexist['recoveryID']));
            }
            $data['headerID'] = $headerID;
            $data['timelineID'] = $phaseID;
            $data['projectPlannningID'] = $planningID['projectPlannningID'];
            $data['recoverydueto'] = $recoverydueto;
            $data['descriptionofthedelay'] = $descriptionofthedelay;
            $data['recoveryplandescription'] = $recoveryplandescription;
            $data['costimpactmaterial'] = $additionalmaterial;
            $data['costimpacthr'] = $additionalhr;
            $data['costimpactother'] = $otherreq;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->trans_begin();
            $this->db->insert('srp_erp_recoveryplan', $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully Recovery Plan Updated'));
            }
        }

    }

    function fetch_recoveryplan()
    {
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');
        $phaseID = $this->input->post('timelineID');
        $data = $this->db->query("SELECT projectPlannningID, recoverydueto, descriptionofthedelay, recoveryplandescription, costimpactmaterial, costimpacthr, costimpactother
                                       FROM `srp_erp_recoveryplan` where companyID = $companyID AND headerID  = $headerID AND timelineID = $phaseID")->row_array();
        echo json_encode($data);
    }

    function fetch_recovery_attachment()
    {
        $companyID = current_companyID();
        $data['timelineID'] = $this->input->post('timelineID');
        $data['headerID'] = $this->input->post('headerID');
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentSubID = {$data['timelineID']}  AND documentID = 'PRORP'")->result_array();
        $this->load->view('system/pm/recovery_attachment_view', $data);
    }

    function do_upload_aws_S3($description = true)
    {
        //$this->load->model('upload_modal');
        if ($description) {
            $this->form_validation->set_rules('attachmentDescription', 'Attachment Description', 'trim|required');
            $this->form_validation->set_rules('documentSystemCode', 'documentSystemCode', 'trim|required');
            $this->form_validation->set_rules('document_name', 'document_name', 'trim|required');
            $this->form_validation->set_rules('documentID', 'documentID', 'trim|required');
        }
        //$this->form_validation->set_rules('document_file', 'File', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('status' => 0, 'type' => 'e', 'message' => validation_errors()));
        } else {

            $this->db->trans_start();
            $this->db->select('companyID');
            $this->db->where('documentID', trim($this->input->post('documentID') ?? ''));
            $num = $this->db->get('srp_erp_documentattachments')->result_array();
            $file_name = $this->input->post('documentID') . '_' . $this->input->post('documentSystemCode') . '_' . (count($num) + 1);
            $config['upload_path'] = realpath(APPPATH . '../attachments');
            $config['allowed_types'] = 'gif|jpg|jpeg|png|doc|docx|ppt|pptx|ppsx|pdf|xls|xlsx|xlsxm|rtf|msg|txt|7zip|zip|rar|msg';
            $config['max_size'] = '5120'; // 5 MB
            $config['file_name'] = $file_name;

            /** call s3 library */
            $file = $_FILES['document_file'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            if (empty($ext)) {
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'No extension found for the selected attachment'));
                exit();
            }

            $cc = current_companyCode();
            $folderPath = !empty($cc) ? $cc . '/' : '';
            if ($this->s3->upload($file['tmp_name'], $folderPath . $file_name . '.' . $ext)) {
                $s3Upload = true;
            } else {
                $s3Upload = false;
            }

            /** end of s3 integration */

            $data['documentID'] = trim($this->input->post('documentID') ?? '');
            $data['documentSystemCode'] = trim($this->input->post('documentSystemCode') ?? '');
            $data['attachmentDescription'] = trim($this->input->post('attachmentDescription') ?? '');
            $data['documentSubID'] = trim($this->input->post('documentSubID') ?? '');
            $data['myFileName'] = $folderPath . $file_name . '.' . $ext;
            $data['fileType'] = trim($ext);
            $data['fileSize'] = trim($file["size"]);
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['companyCode'] = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_documentattachments', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('status' => 0, 'type' => 'e', 'message' => 'Upload failed ' . $this->db->_error_message(), 's3Upload' => $s3Upload));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status' => 1, 'type' => 's', 'message' => 'Successfully ' . $file_name . ' uploaded.', 's3Upload' => $s3Upload));
            }
        }
    }

    function load_change_request()
    {

        $data['headerID'] = $this->input->post('headerID');
        $convertFormat = convert_date_format_sql();
        $comapnyID = current_companyID();
        $data['changereq'] = $this->db->query("SELECT requestID,crcode, CASE WHEN typeofcr = 1 THEN \"Enhancement\" WHEN typeofcr = 2 THEN \"Defect\" END as typeofcr, submittername, descriptionofrequest, DATE_FORMAT(datesubmitted,'$convertFormat') as datesubmitted, DATE_FORMAT(daterequired,'$convertFormat')  as daterequired, CASE WHEN priority = 1 THEN \"Low\" WHEN priority = 2 THEN \"Medium\" WHEN priority = 3 THEN \"High\" WHEN priority = 4 THEN \"Mandatory\" END as priority,
                                                    reasonforchange, assumptionsandnotes, commentschangereq FROM `srp_erp_changerequests`  WHERE CompanyID = $comapnyID AND type = 1")->result_array();
        $data['inialanalysis'] = $this->db->query("SELECT srp_erp_boq_category.categoryDescription,srp_erp_boq_subcategory.description,chreq.crcode,crID,srp_erp_changerequests.requestID,hourimpact,durationimpact,scheduleimpact,commentsinitial,costimpact,
                                                        recommendations FROM `srp_erp_changerequests` LEFT JOIN srp_erp_boq_category on srp_erp_boq_category.categoryID = srp_erp_changerequests.category LEFT JOIN srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory LEFT JOIN (select requestID,crcode from srp_erp_changerequests) chreq on chreq.requestID = srp_erp_changerequests.crID 
                                                        where srp_erp_changerequests.companyID = $comapnyID AND type = 2 ")->result_array();

        $data['changecontrolboard'] = $this->db->query("SELECT confirmedYN,approvedYN,decision,chreq.crcode,crID,srp_erp_changerequests.requestID,CASE WHEN decision = 1 THEN \"Approved\" WHEN decision = 2 THEN \"Approved with Conditions\" WHEN decision = 3 THEN \"Rejected\"  WHEN decision = 4 THEN \"More Info\" END as decision_txt ,DATE_FORMAT(decisiondate,'$convertFormat') as decisiondate ,decisionexplanation
                                                         FROM `srp_erp_changerequests` LEFT JOIN (select requestID,crcode from srp_erp_changerequests) chreq on chreq.requestID = srp_erp_changerequests.crID 
                                                        where companyID = $comapnyID AND type = 3 ")->result_array();

        $this->load->view('system/pm/getchangereq', $data);
    }

    function save_changerequests()
    {
        $this->form_validation->set_rules('cr', 'CR Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $headerID = $this->input->post('hederidchangerequests');
            $changereqID = $this->input->post('changereqID');
            $companyID = current_companyID();
            $date_format_policy = date_format_policy();
            $data['crcode'] = $this->input->post('cr');

            $data['typeofcr'] = $this->input->post('typeofcr');
            $data['submittername'] = $this->input->post('submittername');
            $data['descriptionofrequest'] = $this->input->post('breifdescriptionofrequest');
            $data['datesubmitted'] = input_format_date($this->input->post('datesubmitted'), $date_format_policy);
            $data['daterequired'] = input_format_date($this->input->post('daterequired'), $date_format_policy);
            $data['priority'] = $this->input->post('priority');
            $data['reasonforchange'] = $this->input->post('reasonofchange');
            $data['assumptionsandnotes'] = $this->input->post('assumptionsandnotes');
            $data['commentschangereq'] = $this->input->post('commentschangereq');
            $data['type'] = 1;
            if ($changereqID) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('requestID', $changereqID);
                $this->db->update('srp_erp_changerequests', $data);
            } else {
                $data['headerID'] = $headerID;
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->trans_begin();
                $this->db->insert('srp_erp_changerequests', $data);
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully added change request'));
            }
        }

    }

    function fetch_changerequest()
    {
        $convertFormat = convert_date_format_sql();
        $companyID = current_companyID();
        $changereqID = $this->input->post('changereqID');
        $data = $this->db->query("SELECT boqDetailID,headerID,requestID,crcode, typeofcr, submittername, descriptionofrequest, DATE_FORMAT(datesubmitted,'$convertFormat') as datesubmitted, DATE_FORMAT(daterequired,'$convertFormat')  as daterequired, priority,
                                                    reasonforchange, assumptionsandnotes, commentschangereq FROM `srp_erp_changerequests` WHERE CompanyID = $companyID AND requestID = $changereqID AND type = 1 ")->row_array();
        echo json_encode($data);
    }

    function delete_change_req()
    {
        $requestID = $this->input->post('requestID');
        $this->db->trans_begin();
        $this->db->delete('srp_erp_changerequests', array('requestID' => $requestID));
        $this->db->delete('srp_erp_boq_costing', array('crID' => $requestID));
        $this->db->delete('srp_erp_projectactivityplanning', array('crID' => $requestID));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Successfully Deleted'));
        }
    }

    function fetch_changereq_attachment()
    {
        $companyID = current_companyID();
        $data['headerID'] = $this->input->post('headerID');
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']}   AND documentID = 'PROCR'")->result_array();
        $this->load->view('system/pm/changereq_attachment_view', $data);
    }

    function fetch_cr_code()
    {
        $data_arr = array();
        $headerID = $this->input->post('headerID');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $cr_code = $this->db->query("SELECT requestID,crcode FROM `srp_erp_changerequests` where companyID = $comapnyid AND headerID = $headerID AND type =1")->result_array();
        $data_arr = array('' => 'Select CR Code');
        if (!empty($cr_code)) {
            foreach ($cr_code as $row) {
                $data_arr[trim($row['requestID'] ?? '')] = trim($row['crcode'] ?? '');
            }
        }
        echo form_dropdown('crcode', $data_arr, '', 'class="form-control select2" id="crcode"');
    }

    function save_changerequestsinitial()
    {
        $this->form_validation->set_rules('crcode', 'CR Code', 'trim|required');
        $this->form_validation->set_rules('boqdetail_changereq', 'Category', 'trim|required');
        $this->form_validation->set_rules('boqdetail_changereq_sub', 'Sub Category', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $headerID = $this->input->post('hederidinitial');
            $costImpact = (double)$this->input->post('costImpact');
            $crID = $this->input->post('crcode');
            $changereqID = $this->input->post('changereqID_initial');
            $companyID = current_companyID();
            $date_format_policy = date_format_policy();
            $data['crID'] = $this->input->post('crcode');
            $data['category'] = $this->input->post('boqdetail_changereq');
            $data['subCategory'] = $this->input->post('boqdetail_changereq_sub');
            $data['headerID'] = $headerID;
            $data['hourimpact'] = $this->input->post('hourimpact');
            $data['durationimpact'] = $this->input->post('durationimpact');
            $data['scheduleimpact'] = $this->input->post('scheduleimpact');
            $data['commentsinitial'] = $this->input->post('commentsinitial');
            $data['costimpact'] = $this->input->post('costImpact');
            $data['recommendations'] = $this->input->post('recommendations');
            $data['totalLabourcost'] = $this->input->post('labourcost_initial');
            $data['type'] = 2;
            if ($changereqID) {
                $isexist = $this->db->query("select crID from srp_erp_changerequests where companyID = $companyID AND headerID = $headerID AND crid = $crID  AND requestID !=$changereqID  AND type = 2
")->row_array();
                if ($isexist['crID']) {
                    echo json_encode(array('e', 'CR Code already Exist'));
                    exit();
                }
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('requestID', $changereqID);
                $this->db->update('srp_erp_changerequests', $data);
            } else {
                $isexist = $this->db->query("select crID from srp_erp_changerequests where companyID = $companyID AND headerID = $headerID AND crid = $crID AND type = 2
")->row_array();
                if ($isexist['crID']) {
                    echo json_encode(array('e', 'CR Code already Exist'));
                    exit();
                }
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->trans_begin();
                $this->db->insert('srp_erp_changerequests', $data);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Successfully added initial analysis'));
            }
        }

    }

    function fetch_changerequest_initial()
    {
        $ID = $this->input->post('changereqID');
        $data = $this->db->query("SELECT
	crID,requestID,hourimpact,durationimpact,scheduleimpact,commentsinitial,costimpact,recommendations,category,subCategory
FROM
	`srp_erp_changerequests`
	where 
	requestID = $ID")->row_array();
        echo json_encode($data);
    }

    function fetch_cr_code_changecontrolboard()
    {
        $data_arr = array();
        $headerID = $this->input->post('headerID');
        $comapnyid = $this->common_data['company_data']['company_id'];
        $cr_code = $this->db->query("SELECT requestID,crcode FROM `srp_erp_changerequests` where companyID = $comapnyid AND headerID = $headerID AND type =1")->result_array();
        $data_arr = array('' => 'Select CR Code');
        if (!empty($cr_code)) {
            foreach ($cr_code as $row) {
                $data_arr[trim($row['requestID'] ?? '')] = trim($row['crcode'] ?? '');
            }
        }
        echo form_dropdown('crcode_changeboard', $data_arr, '', 'class="form-control select2" id="crcode_changeboard"');
    }

    function save_changerequestsinitial_controlboard()
    {

        $this->form_validation->set_rules('crcode_changeboard', 'CR Code', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $crID = $this->input->post('crcode_changeboard');
            $headerID = $this->input->post('hederidchangecontrol');
            $changereqID = $this->input->post('changereqID_change');
            $companyID = current_companyID();
            $date_format_policy = date_format_policy();
            $data['crID'] = $this->input->post('crcode_changeboard');
            $data['decision'] = $this->input->post('decision');
            $data['decisiondate'] = input_format_date($this->input->post('decisiondate'), $date_format_policy);
            $data['decisionexplanation'] = $this->input->post('decisionexplanation');
            $data['headerID'] = $headerID;
            $data['type'] = 3;
            $detail = $this->db->query("select category,subCategory from srp_erp_changerequests WHERE companyID = $companyID AND crID = $crID")->row_array();
            $data['category'] = $detail['category'];
            $data['subCategory'] = $detail['subCategory'];



            if ($data['decision'] == '6' or $data['decision'] == '1' or $data['decision'] == '2') {
               
            $isexist = $this->db->query("SELECT requestID FROM `srp_erp_changerequests` where type = 2  ANd crID = {$data['crID']}")->row('requestID');
                if(empty($isexist))
                echo json_encode(array('e', 'Initial Analysis should be added before approving'));
                exit();
            }


            if ($changereqID) {
                $isexist = $this->db->query("select crID from srp_erp_changerequests where companyID = $companyID AND headerID = $headerID AND crid = $crID  AND requestID !=$changereqID AND type =3
")->row_array();
                if ($isexist['crID']) {
                    echo json_encode(array('e', 'CR Code already Exist'));
                    exit();
                }

            } else {
                $isexist = $this->db->query("select crID from srp_erp_changerequests where companyID = $companyID AND headerID = $headerID AND crid = $crID  AND type =3
")->row_array();
                if ($isexist['crID']) {
                    echo json_encode(array('e', 'CR Code already Exist'));
                    exit();
                }
            }
            $cost = $this->db->query("select IFNULL(costimpact,0) AS costimpact FROM srp_erp_changerequests where companyID = $companyID AND crID = $crID")->row_array();
            $Totalcost = $this->db->query("SELECT chreq.crcode,IFNULL(SUM(totalLabourTranCurrency), 0 ) + IFNULL(SUM(totalCostTranCurrency) , 0 ) AS total FROM `srp_erp_changerequests`
	                                            LEFT JOIN srp_erp_boq_details ON srp_erp_boq_details.categoryID = srp_erp_changerequests.category
	                                             	LEFT JOIN (SELECT requestID,crcode FROM srp_erp_changerequests Where companyID = $companyID ) chreq on chreq.requestID = srp_erp_changerequests.crID
	                                             WHERE companyID = $companyID 
	                                            AND srp_erp_boq_details.headerID = $headerID AND crID = $crID GROUP BY category")->row_array();
            $totalcostImpact = ($Totalcost['total']) * 10 / 100;
            if ($cost['costimpact'] > $totalcostImpact) {

                if ($data['decision'] == '5' or $data['decision'] == '3' or $data['decision'] == '1' or $data['decision'] == '2') {
                    echo json_encode(array('e', 'Special Approval hasbeen created Please Change the status to sent for approval'));
                    exit();
                }
            }
            if ($changereqID) {
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $this->db->where('requestID', $changereqID);
                $this->db->update('srp_erp_changerequests', $data);

                if ($data['decision'] == '3') {
                    {
                        $data_up_emp = array(
                            'confirmedYN' => 2,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],

                        );
                        $this->db->where('requestID', $changereqID);
                        $this->db->update('srp_erp_changerequests', $data_up_emp);
                    }
                } else if ($data['decision'] == '6') {
                    $data_up_emp = array(
                        'confirmedYN' => 1,
                        'confirmedDate' => $this->common_data['current_date'],
                        'confirmedByEmpID' => $this->common_data['current_userID'],
                        'confirmedByName' => $this->common_data['current_user'],

                    );
                    $this->db->where('requestID', $changereqID);
                    $this->db->update('srp_erp_changerequests', $data_up_emp);

                } else if ($data['decision'] == '1' || $data['decision'] == '2' || $data['decision'] == '5') {
                    if ($data['decision'] == '1' || $data['decision'] == '2') {
                        $query = $this->db->query("select confirmedYN from srp_erp_changerequests where companyID = $companyID AND requestID = $changereqID")->row('confirmedYN');
                        if ($query != 1) {
                            echo json_encode(array('e', 'Please Confirm The Document'));
                            exit();
                        } else {

                            $detail = $this->db->query("SELECT totalLabourcost,category,subCategory,srp_erp_boq_subcategory.unitID,chengereq.crcode,headerID,crID
                                            FROM `srp_erp_changerequests` left join srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory
	                                        LEFT JOIN (select requestID,crcode from srp_erp_changerequests where companyID = $companyID)chengereq on  chengereq.requestID = srp_erp_changerequests.crID
	                                        where srp_erp_changerequests.requestID = $changereqID")->row_array();
                            $data_detail_up['unitID'] = $detail['unitID'];
                            $data_detail_up['categoryID'] = $detail['category'];
                            $data_detail_up['subCategoryID'] = $detail['subCategory'];
                            $data_detail_up['itemDescription'] = $detail['crcode'];
                            $data_detail_up['headerID'] = $detail['headerID'];
                            $d = $this->db->query("select categoryDescription from srp_erp_boq_category where categoryID={$data_detail_up['categoryID']}")->row_array();
                            $data_detail_up['categoryName'] = $d['categoryDescription'];
                            $s = $this->db->query("select description from srp_erp_boq_subcategory where subCategoryID={$data_detail_up['subCategoryID']}")->row_array();
                            $data_detail_up['subCategoryName'] = $s['description'];
                            $data_detail_up['totalLabourTranCurrency'] = $detail['totalLabourcost'];
                            $this->db->insert('srp_erp_boq_details', $data_detail_up);
                            $last_id_detail = $this->db->insert_id();

                            $activityplanning = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` WHERE crID = {$detail['crID']}")->result_array();
                            $costing = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where crID = {$detail['crID']}")->result_array();
                            foreach ($activityplanning as $val) {
                                $data_act['boqheaderID'] = $detail['headerID'];
                                $data_act['detailID'] = $last_id_detail;
                                $this->db->where('activityplanningID', $val['activityplanningID']);
                                $this->db->update('srp_erp_projectactivityplanning', $data_act);
                            }
                            foreach ($costing as $val) {
                                $data_cost['headerID'] = $detail['headerID'];
                                $data_cost['detailID'] = $last_id_detail;
                                $this->db->where('costingID', $val['costingID']);
                                $this->db->update('srp_erp_boq_costing', $data_cost);
                            }
                            $this->Boq_model->update_costing_sheet_initial($last_id_detail);
                            $data_up_emp = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user'],
                                'approvedYN' => 1,
                                'approvedDate' => date('Y-m-d'),
                                'approvedbyEmpID' => current_userID(),
                                'approvedbyEmpName' => current_user()
                            );
                            $this->db->where('requestID', $changereqID);
                            $this->db->update('srp_erp_changerequests', $data_up_emp);
                        }

                    } else {
                        $detail = $this->db->query("SELECT totalLabourcost,category,subCategory,srp_erp_boq_subcategory.unitID,chengereq.crcode,headerID,crID
                                            FROM `srp_erp_changerequests` left join srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory
	                                        LEFT JOIN (select requestID,crcode from srp_erp_changerequests where companyID = $companyID)chengereq on  chengereq.requestID = srp_erp_changerequests.crID
	                                        where srp_erp_changerequests.requestID = $changereqID")->row_array();
                        $data_detail_up['unitID'] = $detail['unitID'];
                        $data_detail_up['categoryID'] = $detail['category'];
                        $data_detail_up['subCategoryID'] = $detail['subCategory'];
                        $data_detail_up['itemDescription'] = $detail['crcode'];
                        $data_detail_up['headerID'] = $detail['headerID'];
                        $d = $this->db->query("select categoryDescription from srp_erp_boq_category where categoryID={$data_detail_up['categoryID']}")->row_array();
                        $data_detail_up['categoryName'] = $d['categoryDescription'];
                        $s = $this->db->query("select description from srp_erp_boq_subcategory where subCategoryID={$data_detail_up['subCategoryID']}")->row_array();
                        $data_detail_up['subCategoryName'] = $s['description'];
                        $data_detail_up['totalLabourTranCurrency'] = $detail['totalLabourcost'];
                        $this->db->insert('srp_erp_boq_details', $data_detail_up);
                        $last_id_detail = $this->db->insert_id();

                        $activityplanning = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` WHERE crID = {$detail['crID']}")->result_array();
                        $costing = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where crID = {$detail['crID']}")->result_array();
                        foreach ($activityplanning as $val) {
                            $data_act['boqheaderID'] = $detail['headerID'];
                            $data_act['detailID'] = $last_id_detail;
                            $this->db->where('activityplanningID', $val['activityplanningID']);
                            $this->db->update('srp_erp_projectactivityplanning', $data_act);
                        }
                        foreach ($costing as $val) {
                            $data_cost['headerID'] = $detail['headerID'];
                            $data_cost['detailID'] = $last_id_detail;
                            $this->db->where('costingID', $val['costingID']);
                            $this->db->update('srp_erp_boq_costing', $data_cost);
                        }
                        $this->Boq_model->update_costing_sheet_initial($last_id_detail);
                        $data_up_emp = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                            'approvedYN' => 1,
                            'approvedDate' => date('Y-m-d'),
                            'approvedbyEmpID' => current_userID(),
                            'approvedbyEmpName' => current_user()
                        );
                        $this->db->where('requestID', $changereqID);
                        $this->db->update('srp_erp_changerequests', $data_up_emp);
                    }


                }


            } else {
                $data['companyID'] = $this->common_data['company_data']['company_id'];
                $data['createdUserGroup'] = $this->common_data['user_group'];
                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];
                $this->db->trans_begin();
                $this->db->insert('srp_erp_changerequests', $data);
                $id_req = $this->db->insert_id();

                if ($cost['costimpact'] > $totalcostImpact) {
                    if ($data['decision'] == '6') {
                        $this->load->library('Approvals');
                        $autoApproval = get_document_auto_approval('CR');
                        $approvals_status = $this->approvals->CreateApproval('CR', $id_req, $Totalcost['crcode'],
                            'CHANGE CONTROL BOARD', 'srp_erp_changerequests', 'requestID');
                        $data_up = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],
                        );
                        if ($approvals_status == 1) {
                            $this->db->where('requestID', $id_req);
                            $this->db->update('srp_erp_changerequests', $data_up);
                            if ($this->db->trans_status() === true) {
                                $this->db->trans_commit();

                            } else {
                                $this->db->trans_rollback();
                                echo json_encode(array('e', 'Error in approval created process'));
                                exit();
                            }
                        } else if ($approvals_status == 3) {
                            $this->db->trans_rollback();
                            echo json_encode(array('w', 'There are no users exist to perform Change control board approval for this company.'));
                            exit();
                        } else {
                            $this->db->trans_rollback();
                            echo json_encode(array('e', 'Error in process'));
                            exit();
                        }
                    }
                } else {
                    if ($data['decision'] == '3') {
                        {
                            $data_up_emp = array(
                                'confirmedYN' => 2,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user'],

                            );
                            $this->db->where('requestID', $id_req);
                            $this->db->update('srp_erp_changerequests', $data_up_emp);
                        }
                    } else if ($data['decision'] == '6') {
                        $data_up_emp = array(
                            'confirmedYN' => 1,
                            'confirmedDate' => $this->common_data['current_date'],
                            'confirmedByEmpID' => $this->common_data['current_userID'],
                            'confirmedByName' => $this->common_data['current_user'],

                        );
                        $this->db->where('requestID', $id_req);
                        $this->db->update('srp_erp_changerequests', $data_up_emp);

                    } else if ($data['decision'] == '1' || $data['decision'] == '2' || $data['decision'] == '5') {
                        if ($data['decision'] == '1' || $data['decision'] == '2') {
                            $query = $this->db->query("select confirmedYN from srp_erp_changerequests where companyID = $companyID AND requestID = $id_req")->row('confirmedYN');
                            if ($query != 1) {
                                echo json_encode(array('e', 'Please Confirm The Document'));
                                exit();
                            } else {

                                $detail = $this->db->query("SELECT totalLabourcost,category,subCategory,srp_erp_boq_subcategory.unitID,chengereq.crcode,headerID,crID
                                            FROM `srp_erp_changerequests` left join srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory
	                                        LEFT JOIN (select requestID,crcode from srp_erp_changerequests where companyID = $companyID)chengereq on  chengereq.requestID = srp_erp_changerequests.crID
	                                        where srp_erp_changerequests.requestID = $id_req")->row_array();
                                $data_detail_up['unitID'] = $detail['unitID'];
                                $data_detail_up['categoryID'] = $detail['category'];
                                $data_detail_up['subCategoryID'] = $detail['subCategory'];
                                $data_detail_up['itemDescription'] = $detail['crcode'];
                                $data_detail_up['headerID'] = $detail['headerID'];
                                $d = $this->db->query("select categoryDescription from srp_erp_boq_category where categoryID={$data_detail_up['categoryID']}")->row_array();
                                $data_detail_up['categoryName'] = $d['categoryDescription'];
                                $s = $this->db->query("select description from srp_erp_boq_subcategory where subCategoryID={$data_detail_up['subCategoryID']}")->row_array();
                                $data_detail_up['subCategoryName'] = $s['description'];
                                $data_detail_up['totalLabourTranCurrency'] = $detail['totalLabourcost'];
                                $this->db->insert('srp_erp_boq_details', $data_detail_up);
                                $last_id_detail = $this->db->insert_id();

                                $activityplanning = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` WHERE crID = {$detail['crID']}")->result_array();
                                $costing = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where crID = {$detail['crID']}")->result_array();
                                foreach ($activityplanning as $val) {
                                    $data_act['boqheaderID'] = $detail['headerID'];
                                    $data_act['detailID'] = $last_id_detail;
                                    $this->db->where('activityplanningID', $val['activityplanningID']);
                                    $this->db->update('srp_erp_projectactivityplanning', $data_act);
                                }
                                foreach ($costing as $val) {
                                    $data_cost['headerID'] = $detail['headerID'];
                                    $data_cost['detailID'] = $last_id_detail;
                                    $this->db->where('costingID', $val['costingID']);
                                    $this->db->update('srp_erp_boq_costing', $data_cost);
                                }
                                $this->Boq_model->update_costing_sheet_initial($last_id_detail);
                                $data_up_emp = array(
                                    'confirmedYN' => 1,
                                    'confirmedDate' => $this->common_data['current_date'],
                                    'confirmedByEmpID' => $this->common_data['current_userID'],
                                    'confirmedByName' => $this->common_data['current_user'],
                                    'approvedYN' => 1,
                                    'approvedDate' => date('Y-m-d'),
                                    'approvedbyEmpID' => current_userID(),
                                    'approvedbyEmpName' => current_user()
                                );
                                $this->db->where('requestID', $id_req);
                                $this->db->update('srp_erp_changerequests', $data_up_emp);
                            }

                        } else {
                            $detail = $this->db->query("SELECT totalLabourcost,category,subCategory,srp_erp_boq_subcategory.unitID,chengereq.crcode,headerID,crID
                                            FROM `srp_erp_changerequests` left join srp_erp_boq_subcategory on srp_erp_boq_subcategory.subCategoryID = srp_erp_changerequests.subCategory
	                                        LEFT JOIN (select requestID,crcode from srp_erp_changerequests where companyID = $companyID)chengereq on  chengereq.requestID = srp_erp_changerequests.crID
	                                        where srp_erp_changerequests.requestID = $id_req")->row_array();
                            $data_detail_up['unitID'] = $detail['unitID'];
                            $data_detail_up['categoryID'] = $detail['category'];
                            $data_detail_up['subCategoryID'] = $detail['subCategory'];
                            $data_detail_up['itemDescription'] = $detail['crcode'];
                            $data_detail_up['headerID'] = $detail['headerID'];
                            $d = $this->db->query("select categoryDescription from srp_erp_boq_category where categoryID={$data_detail_up['categoryID']}")->row_array();
                            $data_detail_up['categoryName'] = $d['categoryDescription'];
                            $s = $this->db->query("select description from srp_erp_boq_subcategory where subCategoryID={$data_detail_up['subCategoryID']}")->row_array();
                            $data_detail_up['subCategoryName'] = $s['description'];
                            $data_detail_up['totalLabourTranCurrency'] = $detail['totalLabourcost'];
                            $this->db->insert('srp_erp_boq_details', $data_detail_up);
                            $last_id_detail = $this->db->insert_id();

                            $activityplanning = $this->db->query("SELECT activityplanningID FROM `srp_erp_projectactivityplanning` WHERE crID = {$detail['crID']}")->result_array();
                            $costing = $this->db->query("SELECT costingID FROM `srp_erp_boq_costing` where crID = {$detail['crID']}")->result_array();
                            foreach ($activityplanning as $val) {
                                $data_act['boqheaderID'] = $detail['headerID'];
                                $data_act['detailID'] = $last_id_detail;
                                $this->db->where('activityplanningID', $val['activityplanningID']);
                                $this->db->update('srp_erp_projectactivityplanning', $data_act);
                            }
                            foreach ($costing as $val) {
                                $data_cost['headerID'] = $detail['headerID'];
                                $data_cost['detailID'] = $last_id_detail;
                                $this->db->where('costingID', $val['costingID']);
                                $this->db->update('srp_erp_boq_costing', $data_cost);
                            }
                            $this->Boq_model->update_costing_sheet_initial($last_id_detail);
                            $data_up_emp = array(
                                'confirmedYN' => 1,
                                'confirmedDate' => $this->common_data['current_date'],
                                'confirmedByEmpID' => $this->common_data['current_userID'],
                                'confirmedByName' => $this->common_data['current_user'],
                                'approvedYN' => 1,
                                'approvedDate' => date('Y-m-d'),
                                'approvedbyEmpID' => current_userID(),
                                'approvedbyEmpName' => current_user()
                            );
                            $this->db->where('requestID', $id_req);
                            $this->db->update('srp_erp_changerequests', $data_up_emp);
                        }


                    }
                }


            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();

                echo json_encode(array('s', 'Successfully added initial analysis'));
            }
        }

    }

    function fetch_changerequest_controlboard()
    {
        $ID = $this->input->post('changereqID');
        $convertFormat = convert_date_format_sql();

        $data = $this->db->query("SELECT
requestID,
headerID,
crID,
decision,
DATE_FORMAT(decisiondate,'$convertFormat') as decisiondate,
decisionexplanation
	FROM `srp_erp_changerequests`
 WHERE
	 requestID = $ID")->row_array();

        echo json_encode($data);
    }

    function fetch_project_phases()
    {
        $headerID = $this->input->post('headerID');
        $companyID = current_companyID();
        $data_arr = array();
        $phase = $this->db->query("SELECT timelineID,phaseDescription FROM `srp_erp_projecttimeline` where companyID = $companyID AND headerID = $headerID")->result_array();
        $data_arr = array('' => 'Select Project Phase');
        if (!empty($phase)) {
            foreach ($phase as $row) {
                $data_arr[trim($row['timelineID'] ?? '')] = trim($row['phaseDescription'] ?? '');
            }
        }
        echo form_dropdown('projectphase', $data_arr, '', 'class="form-control select2" id="projectphase"');
    }

    function fetch_projectclosure()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['checklist_tempdetail'] = $this->db->query("SELECT description, checklistmaster.checklistDescription, checklisttemplateID, documentchecklistID,
	                                                          documentchecklistmasterID,srp_erp_documentchecklist.confirmedYN FROM `srp_erp_documentchecklist` LEFT JOIN srp_erp_checklistmaster checklistmaster 
	                                                          on checklistmaster.checklistID = srp_erp_documentchecklist.checklisttemplateID
	                                                          where srp_erp_documentchecklist.CompanyID = $companyID 
	                                                          AND documentchecklistmasterID = {$data['headerID'] }")->result_array();
        $data['boqmaster'] = $this->db->query("SELECT lessonslearned,projectID FROM `srp_erp_boq_header` where companyID = $companyID AND headerID = {$data['headerID'] } ")->row_array();
        $this->load->view('system/pm/projectclosure', $data);
    }

    function save_checkList()
    {

        $description = $this->input->post('description');
        $noofcol = $this->input->post('numberofcolumn');
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('numberofcolumn', 'Number of Column', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $data['documentID'] = 'PM';
            $data['checklistDescription'] = $description;
            $data['numberOfColumns'] = $noofcol;
            $data['isActive'] = 1;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->trans_begin();
            $this->db->insert('srp_erp_checklistmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            for ($i = 1; $i <= $noofcol; $i++) {
                $data_detail['checklistID'] = $last_id;
                $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                $data_detail['createdPCID'] = $this->common_data['current_pc'];
                $data_detail['createdUserID'] = $this->common_data['current_userID'];
                $data_detail['createdUserName'] = $this->common_data['current_user'];
                $data_detail['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_checklistdetail', $data_detail);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Check list created successfully'));
            }
        }

    }

    function fetch_checklistmaster()
    {
        $this->datatables->select('checklistID,checklistDescription,isActive')
            ->where('companyID', $this->common_data['company_data']['company_id'])
            ->where('documentID', 'PM')
            ->from('srp_erp_checklistmaster');
        $this->datatables->add_column('status', '$1', 'fetch_active_status_checklist(isActive)');
        $this->datatables->add_column('edit', '$1', 'fetch_checklistactions(checklistID)');
        echo $this->datatables->generate();
    }

    function fetch_headerinformation()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        $data['checkListID'] = $this->input->post('checklistID');
        $data['masterData'] = $this->db->query("SELECT checklistID,checklistDescription,isActive,numberOfColumns FROM `srp_erp_checklistmaster` where companyID= $companyID AND documentID = 'PM' 
                                                    AND checklistID = {$data['checkListID']}")->row_array();

        $data['checklistDetail'] = $this->db->query("SELECT checklistdetailID, checklistID, columnTypeID, detailDescription, isFinding,
	                                                      sortOrder, width, bgColor, fontColor FROM `srp_erp_checklistdetail` where companyID = $companyID AND 
	                                                      checklistID = {$data['checkListID']}")->result_array();
        $data['noofrows'] = $this->db->query("SELECT COUNT(criteriaID) as count FROM `srp_erp_checklistcriteria` where checklistmasterID  = {$data['checkListID']} ")->row('count');
        $this->load->view('system/pm/fetch_heaederinformation', $data);
    }

    function update_activestatuscheklist()
    {
        $checklistID = $this->input->post('id');
        $activestatus = $this->input->post('value');
        $data['isActive'] = $activestatus;
        $this->db->trans_begin();
        $this->db->where('checklistID', $checklistID);
        $this->db->update('srp_erp_checklistmaster', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Check list status updated successfully'));
        }
    }

    function update_checklistDetail()
    {

        $columns = $this->input->post('columns');
        foreach ($columns as $key => $val) {
            $num = ($key + 1);
            $this->form_validation->set_rules("columns[{$key}]", "Line {$num} Columns", 'trim|required');
            // $this->form_validation->set_rules("columntitle[{$key}]", "Line {$num} Column title", 'trim|required');
            $this->form_validation->set_rules("Width[{$key}]", "Line {$num}  Width", 'trim|required');
            $this->form_validation->set_rules("sortorder[{$key}]", "Line {$num} Sort Order", 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->Boq_model->update_checklistDetail());
        }

    }

    function feth_checklist_detailtemp()
    {
        $data['checklistID'] = $this->input->post('checklistID');
        $companyID = current_companyID();

        $data['coltype'] = $this->db->query("SELECT checklistdetailID, checklistID, columnTypeID, detailDescription, width, bgColor, fontColor 
                                           FROM `srp_erp_checklistdetail` where companyID = $companyID  AND checklistID = {$data['checklistID']} ORDER BY sortOrder asc")->result_array();

        $data['checklisttempdetail'] = $this->db->query("select srp_erp_checklistcriteria.*,iF((criteriamasterID = 0 && isTitle=1),srp_erp_checklistcriteria.criteriaDescription,checklistmaster.criteriaDescription) as criteriamasterdes
FROM srp_erp_checklistcriteria LEFT JOIN ( SELECT criteriaID,criteriaDescription FROM srp_erp_checklistcriteria where companyID = $companyID AND documentID = 'PM' AND isDelete IS NULL 
AND isTitle = 1 ) checklistmaster on checklistmaster.criteriaID = criteriaMasterID WHERE companyID = $companyID AND documentID = 'PM' AND isDelete IS NULL AND checklistmasterID = {$data['checklistID']} 
ORDER BY sortOder,criteriamasterID ASC")->result_array();

        $data['noofrows'] = $this->db->query("SELECT COUNT(criteriaID) as count FROM `srp_erp_checklistcriteria` where checklistmasterID  = {$data['checklistID']} AND isDelete IS NULL")->row('count');

        $this->load->view('system/pm/project_checklist_template', $data);
    }

    function update_critiriadetil()
    {

        $companyID = current_companyID();
        $checklistID = $this->input->post('checklistID');
        $noofrows = $this->input->post('noofrows');
        $nooflabels = $this->db->query("SELECT COUNT(checklistdetailID)as count FROM `srp_erp_checklistdetail` WHERE
	                                        companyID = $companyID AND columnTypeID = 1 AND checklistID = $checklistID")->row('count');

        $checklistdetailID = $this->db->query("SELECT checklistdetailID FROM `srp_erp_checklistdetail` where companyID = $companyID AND checklistID = $checklistID AND columnTypeID  = 1
                                                    ORDER BY sortOrder ASC")->result_array();

        $this->form_validation->set_rules('noofrows', 'Number of Rows', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            for ($i = 1; $i <= $noofrows; $i++) {
                if ($nooflabels == 1) {

                    $data[$i]['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                    $data[$i]['criteriaDescription'] = 'label_' . $i;
                }
                if ($nooflabels == 2) {
                    $data[$i]['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                    $data[$i]['criteriaDescription'] = 'label_' . $i;
                    $data[$i]['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                    $data[$i]['criteriaDescriptionOne'] = 'label_' . $i;
                }
                if ($nooflabels == 3) {
                    $data[$i]['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                    $data[$i]['criteriaDescription'] = 'label_' . $i;
                    $data[$i]['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                    $data[$i]['criteriaDescriptionOne'] = 'label_' . $i;
                    $data[$i]['checklistDetailID2'] = $checklistdetailID[2]['checklistdetailID'];
                    $data[$i]['criteriaDescriptinTwo'] = 'label_' . $i;
                }
                if ($nooflabels == 4) {
                    $data[$i]['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                    $data[$i]['criteriaDescription'] = 'label_' . $i;
                    $data[$i]['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                    $data[$i]['criteriaDescriptionOne'] = 'label_' . $i;
                    $data[$i]['checklistDetailID2'] = $checklistdetailID[2]['checklistdetailID'];
                    $data[$i]['criteriaDescriptinTwo'] = 'label_' . $i;
                    $data[$i]['checklistDetailID3'] = $checklistdetailID[3]['checklistdetailID'];
                    $data[$i]['criteriaDescriptinThree'] = 'label_' . $i;
                }
                $data[$i]['checklistmasterID'] = $checklistID;
                $data[$i]['documentID'] = 'PM';
                $data[$i]['sortOder'] = $i;
                $data[$i]['companyID'] = $this->common_data['company_data']['company_id'];
                $data[$i]['createdPCID'] = $this->common_data['current_pc'];
                $data[$i]['createdUserID'] = $this->common_data['current_userID'];
                $data[$i]['createdUserName'] = $this->common_data['current_user'];
                $data[$i]['createdDateTime'] = $this->common_data['current_date'];
                $this->db->insert('srp_erp_checklistcriteria', $data[$i]);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Check list created successfully'));
            }

        }
    }

    function update_checklistlabel()
    {
        $name = $this->input->post('name');
        $value = $this->input->post('value');
        $pk = $this->input->post('pk');
        $companyID = current_companyID();
        $this->db->update('srp_erp_checklistcriteria', array($name => $value), array('criteriaID' => $pk));
        return TRUE;
    }

    function update_critertia_headerstatus()
    {
        $companyID = current_companyID();
        $criteriaID = $this->input->post('id');
        $isTitle = $this->input->post('value');
        $data['isTitle'] = $isTitle;
        $data['criteriamasterID'] = 0;
        $this->db->trans_begin();
        $this->db->where('criteriaID', $criteriaID);
        $this->db->update('srp_erp_checklistcriteria', $data);
        $checklistmasterID = $this->db->query("SELECT checklistmasterID FROM srp_erp_checklistcriteria WHERE criteriaID = $criteriaID")->row('checklistmasterID');
        $criterianextID = $this->db->query("SELECT criteriaID from srp_erp_checklistcriteria where isTitle = 1 AND sortOder > (SELECT sortOder FROM `srp_erp_checklistcriteria`
	    where companyID = $companyID AND documentID = 'PM' AND criteriaID = $criteriaID) AND companyID = $companyID AND isDelete IS NULL AND documentID = 'PM' AND checklistmasterID = $checklistmasterID
	    LIMIT 1 ")->row('criteriaID');


        if (!empty($criterianextID)) {
            $criterianextID_exist = $criterianextID;
        } else {
            $criterianextID_exist = $this->db->query("SELECT criteriaID FROM srp_erp_checklistcriteria 
            WHERE sortOder > ( SELECT sortOder FROM `srp_erp_checklistcriteria` WHERE companyID = $companyID AND documentID = 'PM' AND criteriaID = $criteriaID ) AND companyID = 13
            AND documentID = 'PM' AND checklistmasterID = $checklistmasterID AND isDelete IS NULL ORDER BY criteriaID DESC LIMIT 1 ")->row('criteriaID');
        }


        $checkilstIDs = $this->db->query("SELECT criteriaID FROM srp_erp_checklistcriteria WHERE companyID = $companyID AND documentID = 'PM' AND isTitle!=1
                                                AND criteriaID BETWEEN $criteriaID and $criterianextID_exist")->result_array();


        foreach ($checkilstIDs as $val) {
            if ($isTitle == 1) {
                $data_up['criteriamasterID'] = $criteriaID;
            } else {
                $data_up['criteriamasterID'] = 0;
            }
            $this->db->where('criteriaID', $val['criteriaID']);
            $this->db->update('srp_erp_checklistcriteria', $data_up);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Is Title updated successfully'));
        }
    }

    function save_projectclosuretemp()
    {
        $this->form_validation->set_rules('projectclosuredescription', 'Description', 'trim|required');
        $this->form_validation->set_rules('checklisttemplate', 'Template', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $companyID = current_companyID();
            $hederidprojectclosure = $this->input->post('hederidprojectclosure');
            $projectclosuredescription = $this->input->post('projectclosuredescription');
            $checklisttemplate = $this->input->post('checklisttemplate');
            $checklisttempdata = $this->db->query("SELECT * FROM `srp_erp_checklistcriteria` WHERE companyID = $companyID AND isDelete IS NULL AND checklistmasterID = $checklisttemplate ")->result_array();
            $checklisttype = $this->db->query("SELECT checklistID,checklistdetailID FROM `srp_erp_checklistdetail` where companyID = $companyID AND columnTypeID !=1 AND checklistID = $checklisttemplate")->result_array();
            $data['documentID'] = 'PM';
            $data['documentchecklistmasterID'] = $hederidprojectclosure;
            $data['checklisttemplateID'] = $checklisttemplate;
            $data['description'] = $projectclosuredescription;
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->trans_begin();
            $this->db->insert('srp_erp_documentchecklist', $data);
            $documentchecklistID_tempID = $this->db->insert_id();
            foreach ($checklisttype as $col) {
                foreach ($checklisttempdata as $val) {
                    $data_detail['documentchecklistID'] = $documentchecklistID_tempID;
                    $data_detail['criteriaID'] = $val['criteriaID'];
                    $data_detail['checklistdetailID'] = $col['checklistdetailID'];
                    $data_detail['companyID'] = $this->common_data['company_data']['company_id'];
                    $data_detail['createdPCID'] = $this->common_data['current_pc'];
                    $data_detail['createdUserID'] = $this->common_data['current_userID'];
                    $data_detail['createdUserName'] = $this->common_data['current_user'];
                    $data_detail['createdDateTime'] = $this->common_data['current_date'];
                    $this->db->insert('srp_erp_documentchecklistcriteriadetails', $data_detail);
                }
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Project template added sucessfully'));
            }
        }


    }

    function get_template_checklist_project()
    {
        $data['checklistID'] = $this->input->post('checklisttemplateID');
        $data['documentchecklistID'] = $this->input->post('documentchecklistID');
        $data['documentchecklistmasterID'] = $this->input->post('documentchecklistmasterID');
        $convertFormat = convert_date_format_sql();
        $currrentuserID = current_userID();
        $companyID = current_companyID();

        $data['coltype'] = $this->db->query("SELECT checklistdetailID, checklistID, columnTypeID, detailDescription, width, bgColor, fontColor 
                                           FROM `srp_erp_checklistdetail` where companyID = $companyID  AND checklistID = {$data['checklistID']} ORDER BY sortOrder asc")->result_array();

        $data['checklisttempdetail'] = $this->db->query("SELECT srp_erp_checklistcriteria.* FROM `srp_erp_documentchecklistcriteriadetails`
	                                                         LEFT JOIN srp_erp_checklistcriteria on srp_erp_checklistcriteria.criteriaID = srp_erp_documentchecklistcriteriadetails.criteriaID
	                                                         where srp_erp_documentchecklistcriteriadetails.companyID = $companyID AND documentID = 'PM' AND documentchecklistID = {$data['documentchecklistID']} 
	                                                        GROUP BY criteriaID ORDER BY sortOder ASC")->result_array();
        $data['templatemaster'] = $this->db->query("SELECT
	documentchecklistID,srp_erp_documentchecklist.documentID,documentchecklistmasterID,checklisttemplateID,srp_erp_documentchecklist.description,date,contractno,structure,section,drawings,se_qc,Foreman,
	srp_erp_projects.projectName,remarks,DATE_FORMAT(date,'$convertFormat') AS date,srp_erp_documentchecklist.confirmedYN as checklistconfirmedYN,srp_erp_documentchecklist.approvedYN
FROM
	`srp_erp_documentchecklist`
	LEFT join srp_erp_boq_header on srp_erp_boq_header.headerID = srp_erp_documentchecklist.documentchecklistmasterID
	LEFT JOIN srp_erp_projects on srp_erp_projects.projectID = srp_erp_boq_header.projectID
	where 
	srp_erp_documentchecklist.companyID =$companyID 
	AND srp_erp_documentchecklist.documentID = 'PM'
	AND documentchecklistID = " . $data['documentchecklistID'] . " ")->row_array();

        $data['approvalexist'] = $this->db->query("SELECT documentchecklistID,approvalLevelID FROM `srp_erp_documentchecklist`
    JOIN `srp_erp_documentapproved` ON `srp_erp_documentapproved`.`documentSystemCode` = `srp_erp_documentchecklist`.`documentchecklistID` 
    AND `srp_erp_documentapproved`.`approvalLevelID` = `srp_erp_documentchecklist`.`currentLevelNo`
    JOIN `srp_erp_approvalusers` ON `srp_erp_approvalusers`.`levelNo` = `srp_erp_documentchecklist`.`currentLevelNo` 
    WHERE `srp_erp_documentapproved`.`documentID` IN ( 'PMIP' )  AND `srp_erp_approvalusers`.`documentID` IN ( 'PMIP' ) 
    AND `srp_erp_approvalusers`.`employeeID` = $currrentuserID  AND `srp_erp_documentapproved`.`approvedYN` = '0' AND `srp_erp_documentchecklist`.`companyID` = $companyID 
	AND `srp_erp_approvalusers`.`companyID` = $companyID GROUP BY srp_erp_documentapproved.documentSystemCode")->row_array();

        $this->load->view('system/pm/project_wise_template', $data);
    }

    function update_checklist_template_detail_checkbox()
    {
        $checklistdetail = explode('_', $this->input->post('checklistID'));
        $value = $this->input->post('status');
        $criteriaID = $checklistdetail[0];
        $checklistdetailID = $checklistdetail[1];
        $checklistID = $checklistdetail[2];
        $data['criteriavalue'] = $value;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->trans_begin();
        $this->db->where('criteriaID', $criteriaID);
        $this->db->where('documentchecklistID', $checklistID);
        $this->db->where('checklistdetailID', $checklistdetailID);
        $this->db->update('srp_erp_documentchecklistcriteriadetails', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Criteria detail updated successfully'));
        }

    }

    function update_checklist_template_detail_textbox()
    {
        $checklistdetail = explode('_', $this->input->post('checklistID'));
        $value = $this->input->post('value');
        $criteriaID = $checklistdetail[0];
        $checklistdetailID = $checklistdetail[1];
        $checklistID = $checklistdetail[2];
        $data['criteriavalue'] = $value;
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        $this->db->trans_begin();
        $this->db->where('criteriaID', $criteriaID);
        $this->db->where('documentchecklistID', $checklistID);
        $this->db->where('checklistdetailID', $checklistdetailID);
        $this->db->update('srp_erp_documentchecklistcriteriadetails', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Criteria detail updated successfully'));
        }
    }

    function add_new_update_critiriadetil()
    {

        $companyID = current_companyID();
        $checklistID = $this->input->post('checklistID');
        $nooflabels = $this->db->query("SELECT COUNT(checklistdetailID)as count FROM `srp_erp_checklistdetail` WHERE
	                                        companyID = $companyID AND columnTypeID = 1 AND checklistID = $checklistID")->row('count');

        $checklistdetailID = $this->db->query("SELECT checklistdetailID FROM `srp_erp_checklistdetail` where companyID = $companyID AND checklistID = $checklistID AND columnTypeID  = 1
                                                    ORDER BY sortOrder ASC")->result_array();
        $count = $this->db->query("SELECT COUNT(criteriaID) as count FROM `srp_erp_checklistcriteria` where checklistmasterID  = {$checklistID}  AND isDelete IS NULL")->row('count');

        $this->form_validation->set_rules('checklistID', 'CheckList', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            if ($nooflabels == 1) {

                $data['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                $data['criteriaDescription'] = 'label_' . ($count + 1);
            }
            if ($nooflabels == 2) {
                $data['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                $data['criteriaDescription'] = 'label_' . ($count + 1);
                $data['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                $data['criteriaDescriptionOne'] = 'label_' . ($count + 1);
            }
            if ($nooflabels == 3) {
                $data['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                $data['criteriaDescription'] = 'label_' . ($count + 1);
                $data['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                $data['criteriaDescriptionOne'] = 'label_' . ($count + 1);
                $data['checklistDetailID2'] = $checklistdetailID[2]['checklistdetailID'];
                $data['criteriaDescriptinTwo'] = 'label_' . ($count + 1);
            }
            if ($nooflabels == 4) {
                $data['checklistDetailID'] = $checklistdetailID[0]['checklistdetailID'];
                $data['criteriaDescription'] = 'label_' . ($count + 1);
                $data['checklistDetailID1'] = $checklistdetailID[1]['checklistdetailID'];
                $data['criteriaDescriptionOne'] = 'label_' . ($count + 1);
                $data['checklistDetailID2'] = $checklistdetailID[2]['checklistdetailID'];
                $data['criteriaDescriptinTwo'] = 'label_' . ($count + 1);
                $data['checklistDetailID3'] = $checklistdetailID[3]['checklistdetailID'];
                $data['criteriaDescriptinThree'] = 'label_' . ($count + 1);
            }
            $data['checklistmasterID'] = $checklistID;
            $data['documentID'] = 'PM';
            $data['sortOder'] = ($count + 1);
            $data['companyID'] = $this->common_data['company_data']['company_id'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_checklistcriteria', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Check list created successfully'));
            }

        }
    }

    function delete_criteriadetail()
    {
        $criteriaID = $this->input->post('criteriaID');
        $data['isDelete'] = 1;
        $this->db->where('criteriaID', $criteriaID);
        $this->db->update('srp_erp_checklistcriteria', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Criteria deleted successfully'));
        }
    }

    function update_checklist_template_masterdata()
    {
        $checklistID = $this->input->post('checklistID');
        $value = $this->input->post('value');
        $colname = $this->input->post('colname');

        if ($colname == 'date') {
            $date_format_policy = date_format_policy();
            $Date = input_format_date($value, $date_format_policy);
            $data[$colname] = $Date;
        } else {
            $data[$colname] = $value;
        }

        $this->db->where('documentchecklistID', $checklistID);
        $this->db->update('srp_erp_documentchecklist', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo json_encode(array('e', 'failed'));
        } else {
            $this->db->trans_commit();
            echo json_encode(array('s', 'Success'));
        }
    }

    function delete_project_template()
    {
        $checklistID = $this->input->post('documentchecklistID');
        $this->db->where('documentchecklistID', $checklistID);
        $this->db->delete('srp_erp_documentchecklist');

        $this->db->where('documentchecklistID', $checklistID);
        $result = $this->db->delete('srp_erp_documentchecklistcriteriadetails');
        if ($result) {
            echo json_encode(array('s', 'Record deleted successfully!'));
        } else {
            echo json_encode(array('e', 'Error while deleting, please contact your system team!'));
        }
    }

    function save_project_closure_lessonlearn()
    {
        $headerID = $this->input->post('headerID');
        $lessonslearned = $this->input->post('lessonslearned');
        $this->form_validation->set_rules("headerID", "Header", 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $data['lessonslearned'] = $lessonslearned;
            $this->db->where('headerID', $headerID);
            $this->db->update('srp_erp_boq_header', $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'failed'));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('s', 'updated successfully'));
            }
        }

    }

    function fetch_project_closureattachment()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentID = 'PROCLO'")->result_array();
        $this->load->view('system/pm/project_closure_attachment_view', $data);
    }

    function project_template_confirm()
    {
        $companyID = current_companyID();
        $this->load->library('Approvals');

        $documentchecklistID = $this->input->post('documentchecklistID');
        $isconfirmed = $this->db->query("SELECT documentchecklistID FROM `srp_erp_documentchecklist` where confirmedYN = 1 	AND documentchecklistID = $documentchecklistID")->row('documentchecklistID');
        if (!empty($isconfirmed)) {
            echo json_encode(array('w', 'Document already confirmed'));
        } else {
            $autoApproval = get_document_auto_approval('PMIP');


            $master = $this->db->query("SELECT boqheader.projectCode FROM `srp_erp_documentchecklist` LEFT JOIN srp_erp_boq_header boqheader on boqheader.headerID = srp_erp_documentchecklist.documentchecklistmasterID
	where srp_erp_documentchecklist.companyID = $companyID 	AND documentchecklistID = {$documentchecklistID}")->row_array();
            $approvals_status = $this->approvals->CreateApproval('PMIP', $documentchecklistID, $master['projectCode'],
                'Project Inspection Process', 'srp_erp_documentchecklist', 'documentchecklistID');
            $data = array(
                'confirmedYN' => 1,
                'confirmedDate' => $this->common_data['current_date'],
                'confirmedByEmpID' => $this->common_data['current_userID'],
                'confirmedByName' => $this->common_data['current_user'],
            );
            if ($approvals_status == 1) {
                $this->db->where('documentchecklistID', $documentchecklistID);
                $this->db->update('srp_erp_documentchecklist', $data);
                if ($this->db->trans_status() === true) {
                    $this->db->trans_commit();
                    echo json_encode(array('s', 'Successfully confirmed'));
                } else {
                    $this->db->trans_rollback();
                    echo json_encode(array('e', 'Error in approval created process'));
                }
            } else if ($approvals_status == 3) {
                $this->db->trans_rollback();
                echo json_encode(array('w', 'There are no users exist to perform inspection process approval for this company.'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Error in process'));
            }
        }
    }

    function project_approval_checklist()
    {
        $system_code = trim($this->input->post('documentchecklistID') ?? '');
        $level_id = trim($this->input->post('approvalLevelID') ?? '');
        $status = trim($this->input->post('value') ?? '');
        $code = 'PMIP';
        if ($status == 1) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('documentchecklistID');
                $this->db->where('documentchecklistID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_documentchecklist');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('value', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_checklist_approval());
                    }
                }
            }
        } else if ($status == 2) {
            $this->db->select('documentchecklistID');
            $this->db->where('documentchecklistID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_documentchecklist');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('value', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_checklist_approval());
                    }
                }
            }
        }
    }

    function downloadExcel()
    {
        $csv_data = [
            [
                0 => 'Asset',
                1 => 'Equpiment Cost Type',
                2 => 'Rented Periods In Days/Hours',
                3 => 'Per Day/Hour Cost',
                4 => 'With Operator',
            ]
        ];
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=Equipment Planning.csv");
        $output = fopen("php://output", "w");
        foreach ($csv_data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }

    function equipment_master_excelUpload()
    {
        $headerID = $this->input->post('boqheaderID');
        $boqdetailID = $this->input->post('boqdetailID');
        $supplierupform = $this->input->post('supplierupform');
        $companyID = current_companyID();
        $x1 = 0;
        $filename = $_FILES["excelUpload_file"]["tmp_name"];
        $filed = fopen($filename, "r");
        if (isset($_FILES['excelUpload_file']['size']) && $_FILES['excelUpload_file']['size'] > 0) {
            $type = explode(".", $_FILES['excelUpload_file']['name']);
            if (strtolower(end($type)) != 'csv') {
                die(json_encode(['e', 'File type is not csv - ', $type]));
            }
            $i = 0;
            $x = 0;
            $n = 0;
            $equipmenttype = '';
            $operatorYN = '';
            $filename = $_FILES["excelUpload_file"]["tmp_name"];
            $file = fopen($filename, "r");
            $filed = fopen($filename, "r");
            $dataExcel = [];
            while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
                if ($i > 0) {
                    if (($getData[1] == 'Hour') || ($getData[1] == 'Hours') || ($getData[1] == 'hours') || ($getData[1] == 'hour')) {
                        $equipmenttype = 1;
                    } else if (($getData[1] == 'Per Day') || ($getData[1] == 'per day') || ($getData[1] == 'Day') || ($getData[1] == 'Days') || ($getData[1] == 'day') || ($getData[1] == 'days')) {
                        $equipmenttype = 2;
                    } else {
                        $equipmenttype = '';
                    }
                    if (($getData[4] == 'yes') || ($getData[4] == 'Yes') || ($getData[4] == 'YES') || ($getData[4] == 'Y') || ($getData[4] == 'y')) {
                        $operatorYN = '1';
                    } else {
                        $operatorYN = '2';
                    }

                    $dataExcel[$i]['boqheaderID'] = $headerID;
                    $dataExcel[$i]['detailID'] = $boqdetailID;
                    $dataExcel[$i]['supplierID'] = $supplierupform;
                    $dataExcel[$i]['type'] = 3;
                    $dataExcel[$i]['equipmentType'] = 2;
                    $dataExcel[$i]['equipmentDescription'] = $getData[0];
                    $dataExcel[$i]['equpimentcost'] = $getData[3];
                    $dataExcel[$i]['equpimentcostType'] = $equipmenttype;
                    $dataExcel[$i]['rentedPeriod'] = $getData[2];
                    $dataExcel[$i]['operatorYN'] = $operatorYN;
                    $dataExcel[$i]['companyID'] = current_companyID();
                    $dataExcel[$i]['createdUserID'] = current_userID();
                    $dataExcel[$i]['createdPCID'] = $this->common_data['current_pc'];
                    $dataExcel[$i]['createdUserID'] = $this->common_data['current_userID'];
                    $dataExcel[$i]['createdUserName'] = $this->common_data['current_user'];
                    $dataExcel[$i]['createdDateTime'] = $this->common_data['current_date'];
                }
                $i++;
            }
            fclose($file);
            if (!empty($dataExcel)) {
                $result = $this->db->insert_batch('srp_erp_projectactivityplanning', $dataExcel);
                if ($result) {
                    echo json_encode(['s', 'Successfully Updated']);
                } else {
                    echo json_encode(['e', 'Upload Failed']);
                }
            } else {
                echo json_encode(['e', 'No records in the uploaded file']);

            }
        } else {
            echo json_encode(['e', 'No Files Attached']);
        }
    }

    function update_linkasset()
    {
        $companyID = current_companyID();
        $activityplanningID = $this->input->post('activityplanningID');
        $assetlinkID = $this->input->post('assetlinkID');
        $this->form_validation->set_rules("assetlinkID", "Asset", 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            $supplierID = $this->input->post('supplierlinkasset');
            if ($assetlinkID && $supplierID == '') {
                $supplier = $this->db->query("SELECT supplierID FROM `srp_erp_fa_asset_master` where companyID = $companyID AND faID = $assetlinkID ")->row('supplierID');
            } else {
                $supplier = $supplierID;
            }

            $data = array(
                'faID' => $assetlinkID,
                'supplierID' => $supplier
            );
            $this->db->where('activityplanningID', $activityplanningID);
            $this->db->update('srp_erp_projectactivityplanning', $data);
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Assset Successfully Linked'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Error In Assset Link'));
            }
        }
    }

    function fetch_load_boq_detaildrop()
    {
        $headerID = $this->input->post('headerID');
        $cat_drop = $this->db->query("SELECT categoryID,categoryName FROM `srp_erp_boq_details` where headerID = $headerID")->result_array();
        $data_arr = array('' => 'Select Category');
        if (!empty($cat_drop)) {
            foreach ($cat_drop as $row) {
                $data_arr[trim($row['categoryID'] ?? '')] = trim($row['categoryName'] ?? '');
            }
        }
        echo form_dropdown('boqdetail_changereq', $data_arr, '', 'class="form-control select2" id="boqdetail_changereq" onchange="fetch_subcatergory(' . $headerID . ',this.value)"');
    }

    function fetch_load_subcatboq()
    {
        $catergoryID = $this->input->post('catergoryID');
        $headerID = $this->input->post('headerID');
        $cat_drop = $this->db->query("SELECT subCategoryID, subCategoryName FROM `srp_erp_boq_details` where headerID = $headerID AND categoryID = $catergoryID")->result_array();
        $data_arr = array('' => 'Select Sub Category');
        if (!empty($cat_drop)) {
            foreach ($cat_drop as $row) {
                $data_arr[trim($row['subCategoryID'] ?? '')] = trim($row['subCategoryName'] ?? '');
            }
        }
        echo form_dropdown('boqdetail_changereq_sub', $data_arr, '', 'class="form-control select2" id="boqdetail_changereq_sub" ');

    }

    function save_boq_cost_sheet_initialanalysis()
    {
        $this->form_validation->set_rules('search', 'Category Code', 'trim|required');
        $this->form_validation->set_rules('uom', 'Item ', 'trim|required');
        $this->form_validation->set_rules('qty', 'Revenue GL Code', 'trim|required');
        $this->form_validation->set_rules('unitcost', 'Revenue GL Code', 'trim|required');


        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->Boq_model->save_boq_cost_sheet_initialanalysis());
        }
    }

    function loadboqcosttable_initialanalysis()
    {
        $crCode = $this->input->post('crcode');
        $details = $this->db->query("SELECT costingID, detailID, UOMID, UnitShortCode, Qty, unitCost, totalCost, costCurrencyCode, itemCode, itemDescription, CONCAT( itemCode, ' - ', itemDescription ) AS item, crID FROM srp_erp_boq_costing where crID = $crCode")->result_array();

        $table = '<table id="loadcosttable" class="' . table_class() . '"><thead><tr><th>Item</th><th>UOM</th><th>Qty</th><th >Unit Cost</th><th>Total Cost</th><th></th></tr></thead><tbody>';
        $totalValue = 0;
        if ($details) {
            $customerCurrencyID = 0;
            foreach ($details as $value) {
                $totalValue += $value['totalCost'];
                $table .= '<tr>';
                $table .= '<td>' . $value["itemDescription"] . '</td>';
                $table .= '<td>' . $value["UnitShortCode"] . '</td>';
                $table .= '<td><div style="text-align: right">' . $value["Qty"] . '</td><div></td>';
                $table .= '<td><div style="text-align: right">' . number_format((float)$value["unitCost"], 2, '.',
                        '') . '</div></td>';
                $table .= '<td><div style="text-align: right">' . number_format((float)$value["totalCost"], 2, '.',
                        '') . '</div></td>';
                $table .= '<td><span class="pull-right"><a onclick="deleteBoqCost_initialanalysis(' . $value['costingID'] . ',' . $crCode . ')" ><span style="color:#ff3f3a" class="glyphicon glyphicon-trash "></span></a></td>';
                $status = '';
                $table .= '</tr>';
            }
        }
        $table .= '</tbody>';
        $table .= '<tfoot><tr>';
        $table .= '<td colspan="4" style="text-align: right">Grand Total</td>';
        $table .= '<td>
                <input type="hidden" name="totalvalue_tbl" id="totalvalue_tbl" value=' . number_format((float)$totalValue, 2, '.', '') . '>
            <div style="text-align: right">' . number_format((float)$totalValue, 2, '.', '') . '</div></td>';
        $table .= '<td></td>';
        $table .= '</tr></tfoot>';
        $table .= '</table>';
        echo $table;


    }

    function delete_unsaved_costitems()
    {
        $crcode = $this->input->post('crcode');
        $this->db->delete('srp_erp_boq_costing', array('crID' => $crcode));
        $this->db->delete('srp_erp_projectactivityplanning', array('crID' => $crcode));
        echo json_encode(array('s', 'Successfully Deleted'));
    }

    function deleteboqcost_initialanalysis()
    {
        echo $this->Boq_model->deleteboqcost_initialanalysis();
    }

    function save_approvalstatus_request()
    {
        $system_code = trim($this->input->post('requestID') ?? '');
        $level_id = trim($this->input->post('approvalLevelID') ?? '');
        $status = trim($this->input->post('status') ?? '');
        $code = 'CR';
        if ($status == 1 || $status == 2) {
            $approvedYN = checkApproved($system_code, $code, $level_id);
            if ($approvedYN) {
                $this->session->set_flashdata('w', 'Document already approved');
                echo json_encode(FALSE);
            } else {
                $this->db->select('requestID');
                $this->db->where('requestID', trim($system_code));
                $this->db->where('confirmedYN', 2);
                $this->db->from('srp_erp_changerequests');
                $po_approved = $this->db->get()->row_array();
                if (!empty($po_approved)) {
                    $this->session->set_flashdata('w', 'Document already rejected');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_approval_changereq());
                    }
                }
            }
        } else if ($status == 3) {
            $this->db->select('requestID');
            $this->db->where('requestID', trim($system_code));
            $this->db->where('confirmedYN', 2);
            $this->db->from('srp_erp_changerequests');
            $po_approved = $this->db->get()->row_array();
            if (!empty($po_approved)) {
                $this->session->set_flashdata('w', 'Document already rejected');
                echo json_encode(FALSE);
            } else {
                $rejectYN = checkApproved($system_code, $code, $level_id);
                if (!empty($rejectYN)) {
                    $this->session->set_flashdata('w', 'Document already approved');
                    echo json_encode(FALSE);
                } else {
                    $this->form_validation->set_rules('status', 'Status', 'trim|required');
                    if ($this->form_validation->run() == FALSE) {
                        $this->session->set_flashdata($msgtype = 'e', validation_errors());
                        echo json_encode(FALSE);
                    } else {
                        echo json_encode($this->Boq_model->save_boq_approval_changereq());
                    }
                }
            }
        }
    }

    function load_inspection_detail()
    {
        $data['headerID'] = $this->input->post('headerID');
        $convertFormat = convert_date_format_sql();
        $comapnyID = current_companyID();
        $data['checklist_tempdetail'] = $this->db->query("SELECT description, checklistmaster.checklistDescription, checklisttemplateID, documentchecklistID,
	                                                          documentchecklistmasterID,srp_erp_documentchecklist.confirmedYN FROM `srp_erp_documentchecklist` LEFT JOIN srp_erp_checklistmaster checklistmaster 
	                                                          on checklistmaster.checklistID = srp_erp_documentchecklist.checklisttemplateID
	                                                          where srp_erp_documentchecklist.CompanyID = $comapnyID 
	                                                          AND documentchecklistmasterID = {$data['headerID'] }")->result_array();
        $data['boqmaster'] = $this->db->query("SELECT lessonslearned FROM `srp_erp_boq_header` where companyID = $comapnyID AND headerID = {$data['headerID'] } ")->row_array();
        $this->load->view('system/pm/getinspectiondetail', $data);
    }

    function fetch_project_invoices()
    {
        $projectID = $this->input->post('projectID');
        $comapnyID = current_companyID();
        $data['details'] = $this->db->query("SELECT invoiceCode, invoiceDate, invoiceDueDate, customerSystemCode, customerName, transactionAmount 
                                                FROM srp_erp_customerinvoicemaster where companyID = $comapnyID AND invoiceType = 'Project' AND projectID = $projectID ")->result_array();
        $this->load->view('system/pm/fetch_invoice_project_detail', $data);
    }

    function fetch_project_retention()
    {
        $projectID = $this->input->post('projectID');
        $comapnyID = current_companyID();
        $data['details'] = $this->db->query("SELECT retention.* FROM srp_erp_customerinvoicemaster LEFT JOIN (select retensionInvoiceID, invoiceDate, invoiceCode, invoiceDueDate, customerSystemCode, customerName, transactionAmount from srp_erp_customerinvoicemaster where companyID = 13 ) retention on retention.retensionInvoiceID = srp_erp_customerinvoicemaster.invoiceAutoID where companyID = $comapnyID AND projectID = $projectID ")->result_array();
        $this->load->view('system/pm/fetch_retention_project_detail', $data);
    }

    function sendtorfq()
    {

        $detailID = $this->input->post('detailID');
        $rfqexist = $this->db->query("select issendforrfq from srp_erp_boq_details where detailID = $detailID")->row('issendforrfq');

        if ($rfqexist == 1) {
            echo json_encode(array('e', 'RFQ Already Generated'));
            exit();
        } else {
            $data = array(
                'issendforrfq' => 1
            );
            $this->db->where('detailID', $detailID);
            $this->db->update('srp_erp_boq_details', $data);
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                echo json_encode(array('s', 'RFQ Generated successfully'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Error In RFQ Generated'));
            }
        }

    }

    function load_daily_qulityreport()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['is_print'] = 'N';

        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='DQR'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['headerID']}")->row('projectDescription');

        $this->load->view('system/pm/load_daily_qa_rpt', $data);
    }

    function save_boq_tem_repdetails()
    {
        $tempkey = $this->input->post('tempkey');
        if($tempkey == 'IR')
        {
            $this->form_validation->set_rules('buildingname', 'Building Name', 'trim|required');
            $this->form_validation->set_rules('subject', 'Subject/Item Of Inspection', 'trim|required');
            $this->form_validation->set_rules('inspectionrepdate', 'Date', 'trim|required');
            $this->form_validation->set_rules('inspectionreptime', 'Time', 'trim|required');
            $this->form_validation->set_rules('appradio', 'Status', 'trim|required');
            $this->form_validation->set_rules('comments', 'Comments', 'trim|required');
            $this->form_validation->set_rules('consultantcomments', 'Consultant Comments', 'trim|required');
            $this->form_validation->set_rules('clientcomments', 'Client Comments', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode( array('e', validation_errors()));
            }else {
                echo json_encode($this->Boq_model->save_boq_tem_repdetails());
            }
        }
        else if($tempkey == 'DQR')
        {

            $fieldval = $this->input->post('fieldvaluemulti');
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            $this->form_validation->set_rules('reportno', 'Report No', 'trim|required');
            $this->form_validation->set_rules('contractno', 'Contract No', 'trim|required');
            $this->form_validation->set_rules('location', 'Location', 'trim|required');
            $this->form_validation->set_rules('discipline', 'Discipline', 'trim|required');
            $this->form_validation->set_rules('issuedby', 'Issued by', 'trim|required');
            $this->form_validation->set_rules('drawings', 'Drawings', 'trim|required');
            $this->form_validation->set_rules('inspectionsummary', 'Inspection Summary', 'trim|required');
            $x=1;
            foreach ($fieldval as $key => $search) {
                $sortorder = $this->input->post('sortorder');
                //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
                $this->form_validation->set_rules("fieldvaluemulti[{$key}]", 'Line No'.$x.' Inspection Request No\'s', 'trim|required');
                $this->form_validation->set_rules("fieldvaluemultiradio_".$sortorder[$key], 'Line No'.$x.' Main Contractor Status', 'trim|required');
                $this->form_validation->set_rules("fieldvaluemulticheck_".$sortorder[$key], 'Line No'.$x.' Consultant Status', 'trim|required');
              //  $this->form_validation->set_rules("fieldvaluemulti[{$key}]", 'Line No'.$x.' Inspection Request No\'s', 'trim|required');
                $x++;

            }
            $this->form_validation->set_rules('materialinspection', 'Material Inspection', 'trim|required');
            $this->form_validation->set_rules('testconducted', 'Test Conducted', 'trim|required');
            $this->form_validation->set_rules('ncrissued', 'NCR Issued', 'trim|required');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode( array('e', validation_errors()));
            }else {
                echo json_encode($this->Boq_model->save_boq_tem_repdetails());
            }
        }else if($tempkey == 'RFI')
        {
            $this->form_validation->set_rules('rfi_date', 'Date', 'trim|required');
            $this->form_validation->set_rules('rfi_decipline', 'Rfi Discipline', 'trim|required');
            $this->form_validation->set_rules('rfi_consultant', 'Consultant', 'trim|required');
            $this->form_validation->set_rules('rfi_contractor', 'Contractor', 'trim|required');
            $this->form_validation->set_rules('rfi_subject', 'Subject', 'trim|required');
            $this->form_validation->set_rules('rfi_dateInfo', ' Date Info. Required', 'trim|required');
            $this->form_validation->set_rules('receivedbylv', 'Received By L&V ', 'trim|required');
            $this->form_validation->set_rules('signedbylv', 'Signed By L&V ', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                echo json_encode( array('e', validation_errors()));
            }else {
                echo json_encode($this->Boq_model->save_boq_tem_repdetails());
            }
        }else if($tempkey == 'SOHO')
        {
            $this->form_validation->set_rules('sector', 'Sector', 'trim|required');
            $this->form_validation->set_rules('nameofwork', 'Name Of Work', 'trim|required');
            $this->form_validation->set_rules('sohodate', 'Date', 'trim|required');
            $this->form_validation->set_rules('snagcomment', 'Snags / Comments', 'trim|required');

            $x=1;
            $fieldval = $this->input->post('itemdescription');
            foreach ($fieldval as $key => $search) {
                $sortorder = $this->input->post('sortorder');
                //$this->form_validation->set_rules("search[{$key}]", 'Item', 'trim|required');
                $this->form_validation->set_rules("itemdescription[{$key}]", 'Line No'.$x.' Item Description', 'trim|required');
                $this->form_validation->set_rules("completed_".$sortorder[$key], 'Line No'.$x.' Completed', 'trim|required');
                $this->form_validation->set_rules("remarks_".$sortorder[$key], 'Line No'.$x.' Remarks', 'trim|required');
                //  $this->form_validation->set_rules("fieldvaluemulti[{$key}]", 'Line No'.$x.' Inspection Request No\'s', 'trim|required');
                $x++;

            }
            if ($this->form_validation->run() == FALSE) {
                echo json_encode( array('e', validation_errors()));
            }else {
                echo json_encode($this->Boq_model->save_boq_tem_repdetails());
            }
        }else
        {
            echo json_encode($this->Boq_model->save_boq_tem_repdetails());
        }




    }

    function fetch_dailyqulityrep_multidetail()
    {
        $companyID = current_companyID();
        $projectID = $this->input->post('ProjectID');
        $headerID = $this->input->post('headerID');
        $data = $this->db->query("SELECT detailID,sortOrder,fieldValue,SUBSTRING_INDEX( fieldValue, '|+', 1 ) AS detail,SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+', 2 ), '|+',- 1 ) AS Radiovalue,
	                                  SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+',- 1 ), '|+', 1 ) AS checkboxvalue FROM srp_erp_pm_templateheader
                                      LEFT JOIN	srp_erp_pm_templatedetails  on srp_erp_pm_templateheader.headerID = srp_erp_pm_templatedetails.headerID WHERE
	                                  srp_erp_pm_templateheader.companyID = $companyID AND srp_erp_pm_templateheader.headerID = $headerID AND tempElementKey = 'IRS' ORDER BY sortOrder ASC")->result_array();
        echo json_encode($data);

    }

    function delete_inspectionreqstatus()
    {
        $detailID = $this->input->post('detail_id');
        $result = $this->db->delete('srp_erp_pm_templatedetails', array('detailID' => $detailID));
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Record deleted successfully!'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error while deleting, please contact your system team!'));
        }
    }

    function load_inspectionrequest()
    {
        $companyID = current_companyID();
        $data['headerID'] = $this->input->post('headerID');
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='IR'  AND projectID = {$data['headerID']}")->row('headerID');
        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['headerID']}")->row('projectDescription');
        $this->load->view('system/pm/load_inspection_request', $data);
    }

    function load_projectsummaryview()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='PS'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['headerID']}")->row('projectDescription');

        $this->load->view('system/pm/load_project_summary', $data);
    }

    function get_daily_quality_report()
    {
        $data['headerID']  =  $this->input->post('tempmasterID');
        $companyID = current_companyID();
        $data['projectID']= $this->input->post('tempmasterID');
        $data['detailid'] =  $this->input->post('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['detailid']}")->row('projectDescription');


        $data['inspectionreq_det'] = $this->db->query("SELECT detailID,sortOrder,fieldValue,SUBSTRING_INDEX( fieldValue, '|+', 1 ) AS detail,SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+', 2 ), '|+',- 1 ) AS Radiovalue,
	                                  SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+',- 1 ), '|+', 1 ) AS checkboxvalue FROM srp_erp_pm_templateheader
                                      LEFT JOIN	srp_erp_pm_templatedetails  on srp_erp_pm_templateheader.headerID = srp_erp_pm_templatedetails.headerID WHERE
	                                  srp_erp_pm_templateheader.companyID = $companyID AND srp_erp_pm_templateheader.headerID = {$data['headerID']} AND tempElementKey = 'IRS' ORDER BY sortOrder ASC")->result_array();


        $html = $this->load->view('system/pm/load_daily_qa_rpt_pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function load_projecthandover()
    {
        $companyID = current_companyID();
        $data['headerID'] = $this->input->post('headerID');
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='SOHO'  AND projectID = {$data['headerID']}")->row('headerID');
        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['headerID']}")->row('projectDescription');
        $this->load->view('system/pm/load_projecthandover_form', $data);
    }

    function fetch_sheetofhandover()
    {
        $companyID = current_companyID();
        $projectID = $this->input->post('ProjectID');
        $data['headerID'] = $this->input->post('headerID');
        $data = $this->db->query("SELECT detailID,sortOrder,fieldValue,SUBSTRING_INDEX( fieldValue, '|+', 1 ) AS itemDescription,SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+', 2 ), '|+',- 1 ) AS completed,
	                                  SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+',- 1 ), '|+', 1 ) AS remarks FROM srp_erp_pm_templateheader
                                      LEFT JOIN	srp_erp_pm_templatedetails  on srp_erp_pm_templateheader.headerID = srp_erp_pm_templatedetails.headerID WHERE
	                                  srp_erp_pm_templateheader.companyID = $companyID AND srp_erp_pm_templateheader.headerID = {$data['headerID']} ANd tempkey = 'SOHO' AND tempElementKey = 'SOHODETAIL' ORDER BY sortOrder ASC")->result_array();
        echo json_encode($data);
    }

    function delete_sheetofhandingover()
    {
        $detailID = $this->input->post('detail_id');
        $result = $this->db->delete('srp_erp_pm_templatedetails', array('detailID' => $detailID));
        if ($result) {
            echo json_encode(array('error' => 0, 'message' => 'Record deleted successfully!'));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'Error while deleting, please contact your system team!'));
        }
    }

    function load_project_RFI()
    {
        $companyID = current_companyID();
        $data['project'] = $this->input->post('project');
        $data['headerID'] = $this->input->post('headerID');
        $data['documentCode'] = $this->db->query("SELECT documentCode FROM srp_erp_pm_templateheader 
                                    WHERE companyID = {$companyID} AND tempkey ='RFI' AND headerID = {$data['headerID']}")->row('documentCode');
        $data['projectData'] = $this->db->query("SELECT customerName,projectDescription FROM srp_erp_boq_header 
                                    WHERE companyID = {$companyID} AND headerID = {$data['project']}")->row_array();

        if (empty($data['documentCode'])) {
            $serial = $this->db->select_max('serialNo')->where(['tempkey' => 'RFI'])->get('srp_erp_pm_templateheader')->row('serialNo');
            $serial += 1;
            $rfi_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
            $current_year = date('Y');
            $rfi_code = "TIE/RFI/{$rfi_code}/{$current_year}";
            $data['documentCode'] = $rfi_code;
            $data['headerID'] = 0;
        }

        $this->load->view('system/pm/load_projectRFI_form', $data);
    }

    function fetch_RFI_docs()
    {
        $project = $this->input->post('project');
        $convertFormat = convert_date_format_sql();
        $open = '<span class="pull-right" onClick="openRFIDoc($1)" style="color: #3c8dbc"><i class="fa fa-eye"></i></span>';
        $this->datatables->select("headerID,documentCode, createdUserName, DATE_FORMAT(createdDateTime, '{$convertFormat}') createdDate")
            ->from('srp_erp_pm_templateheader mas')
            ->where('mas.tempkey', 'RFI')
            ->add_column('action', $open, 'headerID')
            ->where('mas.projectID', $project);

        echo $this->datatables->generate();
    }

    function fetch_qa_qc()
    {
        $project = $this->input->post('project');
        $convertFormat = convert_date_format_sql();
        $open = '<span class="pull-right" onClick="openQAQC($1)" style="color: #3c8dbc"><i class="fa fa-eye"></i></span>';
        $this->datatables->select("headerID,documentCode, createdUserName, DATE_FORMAT(createdDateTime, '{$convertFormat}') createdDate")
            ->from('srp_erp_pm_templateheader mas')
            ->where('mas.tempkey', 'DQR')
            ->add_column('action', $open, 'headerID')
            ->where('mas.projectID', $project);

        echo $this->datatables->generate();
    }

    function fetch_qc_view()
    {
        $data['headerID']  = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['is_print'] = 'N';
        $data['projectID']= $this->input->post('project');
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='DQR'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['projectID']}")->row('projectDescription');
        $this->load->view('system/pm/load_daily_qa_rpt_multi', $data);
    }

    function fetch_rec()
    {
        $project = $this->input->post('project');
        $convertFormat = convert_date_format_sql();
        $open = '<span class="pull-right" onClick="openrec($1)" style="color: #3c8dbc"><i class="fa fa-eye"></i></span>';
        $this->datatables->select("headerID,documentCode, createdUserName, DATE_FORMAT(createdDateTime, '{$convertFormat}') createdDate")
            ->from('srp_erp_pm_templateheader mas')
            ->where('mas.tempkey', 'IR')
            ->add_column('action', $open, 'headerID')
            ->where('mas.projectID', $project);

        echo $this->datatables->generate();
    }

    function fetch_rec_view()
    {
        $data['headerID']  = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['is_print'] = 'N';
        $data['projectID']= $this->input->post('project');
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='IR'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['projectID']}")->row('projectDescription');
        $this->load->view('system/pm/load_inspection_request_multi', $data);
    }

    function fetch_soho()
    {
        $project = $this->input->post('project');
        $convertFormat = convert_date_format_sql();
        $open = '<span class="pull-right" onClick="open_soho($1)" style="color: #3c8dbc"><i class="fa fa-eye"></i></span>';
        $this->datatables->select("headerID,documentCode, createdUserName, DATE_FORMAT(createdDateTime, '{$convertFormat}') createdDate")
            ->from('srp_erp_pm_templateheader mas')
            ->where('mas.tempkey', 'SOHO')
            ->add_column('action', $open, 'headerID')
            ->where('mas.projectID', $project);

        echo $this->datatables->generate();
    }

    function fetch_soho_view()
    {
        $data['headerID']  = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['is_print'] = 'N';
        $data['projectID']= $this->input->post('project');
        $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='SOHO'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['projectID']}")->row('projectDescription');
        $this->load->view('system/pm/load_projecthandover_form_multi', $data);
    }
    function get_inspection_rec_pdf()
    {

        $data['headerID']  =  $this->input->post('tempmasterID');
        $companyID = current_companyID();
        $data['projectID']= $this->input->post('tempmasterID');
        $data['detailid'] =  $this->input->post('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['detailid']}")->row('projectDescription');
        $html = $this->load->view('system/pm/load_inspection_request_multi_pdf', $data, true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }
    function get_soho_pdf()
    {
        $data['headerID']  =  $this->input->post('tempmasterID');
        $companyID = current_companyID();
        $data['projectID']= $this->input->post('tempmasterID');
        $data['detailid'] =  $this->input->post('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['detailid']}")->row('projectDescription');
        $data['detailsoho'] = $this->db->query("SELECT detailID,sortOrder,fieldValue,SUBSTRING_INDEX( fieldValue, '|+', 1 ) AS itemDescription,SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+', 2 ), '|+',- 1 ) AS completed,
	                                  SUBSTRING_INDEX( SUBSTRING_INDEX( fieldValue, '|+',- 1 ), '|+', 1 ) AS remarks FROM srp_erp_pm_templateheader
                                      LEFT JOIN	srp_erp_pm_templatedetails  on srp_erp_pm_templateheader.headerID = srp_erp_pm_templatedetails.headerID WHERE
	                                  srp_erp_pm_templateheader.companyID = $companyID AND srp_erp_pm_templateheader.headerID = {$data['headerID']} ANd tempkey = 'SOHO' AND tempElementKey = 'SOHODETAIL' ORDER BY sortOrder ASC")->result_array();
        $html = $this->load->view('system/pm/load_projecthandover_form_multi_pdf', $data,true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }

    function project_related_docs(){
        $projectID = $this->input->post('projectID');
        $revenue_gl_arr = all_revenue_gl_drop();

        //Supplier Invoice
        $su_inv = $this->db->query("SELECT invMas.InvoiceAutoID  AS masterID, bookingInvCode AS docCode, bookingDate AS docDate,
                            round( SUM(invDet.transactionAmount), 3 ) AS amount
                            FROM srp_erp_paysupplierinvoicedetail AS invDet 
                            JOIN srp_erp_paysupplierinvoicemaster AS invMas ON invMas.InvoiceAutoID = invDet.InvoiceAutoID
                            WHERE invDet.projectID = {$projectID} AND invMas.approvedYN = 1 AND NOT EXISTS (
                                SELECT projectDocumentID FROM srp_erp_customerinvoicedetails AS cusInv 
                                WHERE cusInv.projectDocumentID = 'BSI'
                                AND cusInv.projectDocumentMasterAutoID = invMas.InvoiceAutoID
                            )
                            GROUP BY invMas.InvoiceAutoID")->result_array();
        $view = $this->create_table_view('BSI', $su_inv, $revenue_gl_arr);

        //Payment vouchers
        $pv = $this->db->query("SELECT payMas.payVoucherAutoId AS masterID, PVcode AS docCode, PVdate AS docDate,
                            round( SUM(payDet.transactionAmount), 3 ) AS amount
                            FROM srp_erp_paymentvoucherdetail AS payDet 
                            JOIN srp_erp_paymentvouchermaster AS payMas ON payMas.payVoucherAutoId = payDet.payVoucherAutoId
                            WHERE payDet.projectID = {$projectID} AND payMas.approvedYN = 1 AND NOT EXISTS (
                                SELECT projectDocumentID FROM srp_erp_customerinvoicedetails AS cusInv 
                                WHERE cusInv.projectDocumentID = 'PV'
                                AND cusInv.projectDocumentMasterAutoID = payMas.payVoucherAutoId 
                            )
                            GROUP BY payMas.payVoucherAutoId")->result_array();
        $view .= $this->create_table_view('PV', $pv, $revenue_gl_arr);

        //Debit note
        $debit_note = $this->db->query("SELECT deMas.debitNoteMasterAutoID AS masterID, debitNoteCode AS docCode, debitNoteDate AS docDate,
                            round( SUM(deDet.transactionAmount), 3 ) AS amount
                            FROM srp_erp_debitnotedetail AS deDet 
                            JOIN srp_erp_debitnotemaster AS deMas ON deMas.debitNoteMasterAutoID = deDet.debitNoteMasterAutoID
                            WHERE deDet.projectID = {$projectID} AND deMas.approvedYN = 1 AND NOT EXISTS (
                                SELECT projectDocumentID FROM srp_erp_customerinvoicedetails AS cusInv 
                                WHERE cusInv.projectDocumentID = 'DN'
                                AND cusInv.projectDocumentMasterAutoID = deMas.debitNoteMasterAutoID
                            )
                            GROUP BY deMas.debitNoteMasterAutoID")->result_array();
        $view .= $this->create_table_view('DN', $debit_note, $revenue_gl_arr);

        //JV
        $jv = $this->db->query("SELECT jvMas.JVMasterAutoId AS masterID, JVcode AS docCode, JVdate AS docDate,
                            round( SUM(jvDet.debitAmount), 3 ) AS amount
                            FROM srp_erp_jvdetail AS jvDet 
                            JOIN srp_erp_jvmaster AS jvMas ON jvMas.JVMasterAutoId = jvDet.JVMasterAutoId
                            WHERE jvDet.projectID = {$projectID} AND jvMas.approvedYN = 1 AND debitAmount <> 0 
                            AND NOT EXISTS (
                                SELECT projectDocumentID FROM srp_erp_customerinvoicedetails AS cusInv 
                                WHERE cusInv.projectDocumentID = 'JV'
                                AND cusInv.projectDocumentMasterAutoID = jvMas.JVMasterAutoId
                            )
                            GROUP BY jvMas.JVMasterAutoId")->result_array();
        $view .= $this->create_table_view('JV', $jv, $revenue_gl_arr);


        echo json_encode(['s', 'view'=> $view]);
    }

    function create_table_view($type, $data, $revenue_gl_arr){
        $title = '';
        switch ($type){
            case 'BSI':
                $title = 'Supplier Invoice';
                break;
            case 'PV':
                $title = 'Payment voucher';
                break;
            case 'DN':
                $title = 'Debit Note';
                break;
            case 'JV':
                $title = 'Journal Voucher';
                break;
        }

        $str = '<tr><th colspan="6"> '.$title.'</th> </tr>';

        if($data){
            foreach($data as $key=>$row){
                $gl_id = $type.'_'.$row['masterID'];
                $str .= '<tr class="prj-data-tr">
                            <td style="text-align: right">'.($key + 1).'</td> 
                            <td>'.$row['docDate'].'</td> 
                            <td>'.$row['docCode'].'</td> 
                            <td style="text-align: right">'.$row['amount'].'</td>
                            <td style="text-align: center; width: 280px">
                                <div class="form-group" >
                                     <div class="input-group">
                                        <span class="input-group-addon group-add-on-custom" onclick="prj_revenueGL_apply_to_all(this)">
                                            <i class="fa fa-arrow-circle-down" style="font-size: 11px;"></i>
                                        </span>                                    
                                        '.form_dropdown('revenueGL', $revenue_gl_arr, 0,
                                    'class="form-control prj-revenueGL" id="revenueGL_'.$gl_id.'"').'                                    
                                     </div>                                                                
                                </div>                                   
                            </td> 
                            <td style="text-align: center">                                                                
                                <input type="checkbox" value="'.$row['masterID'].'" data-id="'.$type.'" class="boq-prj-chk">                            
                            </td> 
                         </tr>';
            }
        }
        else{
            $str .= '<tr><td colspan="5"> No Records Found </td> </tr>';
        }

        return $str;
    }

    function get_rfi_rec_pdf()
    {
        $companyID = current_companyID();

        $data['project'] = $this->input->post('headerID');
        $data['headerID'] = $this->input->post('tempmasterID');

        $data['documentCode'] = $this->db->query("SELECT documentCode FROM srp_erp_pm_templateheader 
                                    WHERE companyID = {$companyID} AND tempkey ='RFI' AND headerID = {$data['headerID']}")->row('documentCode');
        $data['projectData'] = $this->db->query("SELECT customerName,projectDescription FROM srp_erp_boq_header 
                                    WHERE companyID = {$companyID} AND headerID = {$data['project']}")->row_array();
        if (empty($data['documentCode'])) {
            $serial = $this->db->select_max('serialNo')->where(['tempkey' => 'RFI'])->get('srp_erp_pm_templateheader')->row('serialNo');
            $serial += 1;
            $rfi_code = str_pad($serial, 3, 0, STR_PAD_LEFT);
            $current_year = date('Y');
            $rfi_code = "TIE/RFI/{$rfi_code}/{$current_year}";
            $data['documentCode'] = $rfi_code;
            $data['headerID'] = 0;
        }
        $html = $this->load->view('system/pm/load_projectRFI_form_pdf', $data,true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');
    }
    function get_prosum_pdf()
    {

        $data['project'] = $this->input->post('headerID');
        $data['headerID'] = $this->input->post('tempmasterID');

        $companyID = current_companyID();
       // $data['detailid'] = $this->db->query("SELECT headerID FROM `srp_erp_pm_templateheader` where companyID = $companyID AND  tempkey ='PS'  AND projectID = {$data['headerID']}")->row('headerID');

        $data['project'] = $this->db->query("SELECT projectDescription FROM `srp_erp_boq_header` WHERE companyID = $companyID AND headerID = {$data['project']}")->row('projectDescription');

        $html = $this->load->view('system/pm/load_project_summary_pdf', $data,true);
        $this->load->library('pdf');
        $this->pdf->printed($html, 'A4');


    }
    function confirm_pre_tender(){ 
        $this->form_validation->set_rules('headerID', 'Header', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode( array('e', validation_errors()));
        }else {
            echo json_encode($this->Boq_model->confirm_boq());
            /* $headerID = trim($this->input->post('headerID') ?? '');
            $data = array(
                'pretenderConfirmedYN' => 1
            );
            $this->db->where('headerID', $headerID);
            $this->db->update('srp_erp_boq_header', $data);
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                echo json_encode(array('s', 'Pre Tender Confirmed successfully'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Pre Tender Confirmed Failed'));
            } */
        }
    }
    function posttenderview()
    { 
        $data['header'] = $this->Boq_model->getallsavedvalues();
        $companyID = current_companyID();
        $headerID = $this->input->post('headerID');

        $this->db->select('categoryID,headerID,categoryName');
        $this->db->from('srp_erp_boq_details');
        $this->db->where('headerID', $headerID);
        $this->db->group_by("categoryID");
        $data['details'] = $this->db->get()->result_array();

        $actual = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLI') GROUP BY projectID")->row_array();
        $actualPLE = $this->db->query("SELECT projectID, sum( transactionAmount /projectExchangeRate) as amount FROM `srp_erp_generalledger` WHERE projectID ={$headerID} and (  GLType='PLE') GROUP BY projectID")->row_array();
        
        $data['actualrevenue'] = 0;
        $data['actualCost']  = 0;
        if (!empty($actual)) {
            $data['actualrevenue']= $actual['amount'];
        }
        if (!empty($actualPLE)) {
            $data['actualCost'] = $actualPLE['amount'];
        }
        
        
        //echo '<pre>'; print_r($data['header'] );exit();
        $this->load->view('system/pm/load_pm_posttenderview',$data);
    }
    function fetch_project_quantitysur()
    { 
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['qscomment'] = $this->db->query("SELECT qsComment FROM srp_erp_boq_header WHERE companyid = $companyID AND headerID = {$data['headerID']}")->row('qsComment');
        $this->load->view('system/pm/quantitysurveying',$data);
    }
    function update_qscomment()
    { 
        $headerID = trim($this->input->post('headerID') ?? '');
        $comment = $this->input->post('quantitysurveying');
            $data = array(
                'qscomment' => $comment
            );
            $this->db->where('headerID', $headerID);
            $this->db->update('srp_erp_boq_header', $data);
            if ($this->db->trans_status() === true) {
                $this->db->trans_commit();
                echo json_encode(array('s', ' Quantity Surveying Updated successfully'));
            } else {
                $this->db->trans_rollback();
                echo json_encode(array('e', 'Quantity Surveying Updated Failed'));
            }

    }
    function fetch_qs_comment_attachment()
    {
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentID = 'PROQS'")->result_array();
        $this->load->view('system/pm/quantitysurveyingattachment', $data);
    }
    function fetch_cc_certificate_attachment()
    { 
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentID = 'PROCC'")->result_array();
        $this->load->view('system/pm/completioncertificate_attachment', $data);
    }
    function fetch_maintenacewarrantyattachment()
    { 
        $data['headerID'] = $this->input->post('headerID');
        $companyID = current_companyID();
        $data['attachment'] = $this->db->query("SELECT * FROM `srp_erp_documentattachments` where companyID = $companyID  AND documentSystemCode = {$data['headerID']} AND documentID = 'PROMW'")->result_array();
        $this->load->view('system/pm/maintenacewarranty_attachment', $data);
    }
}
